<?PHP
/*
 * Creates the SVG thermometer
 */

function stringLength($a,$b){
    return mb_strlen($a.$b);
}

function thermhtml($thermProperties){
    ob_start();
    $thermDefaultStyle = get_donation_thermometer_style_defaults();
    $optionsCSS = wp_parse_args( get_option('thermometer_style',$thermDefaultStyle), $thermDefaultStyle);
    echo '<style type="text/css">';
	echo '.thermometer_svg{' . wp_strip_all_tags($optionsCSS['thermometer_svg']) . '}';
    echo '.therm_target{' . wp_strip_all_tags($optionsCSS['therm_target_style']) . '}';
    echo '.therm_raised{' . wp_strip_all_tags($optionsCSS['therm_raised_style']) . '}';
    echo '.therm_percent{' . wp_strip_all_tags($optionsCSS['therm_percent_style']) . '}';
    echo '.therm_subTarget{' . wp_strip_all_tags($optionsCSS['therm_subTarget_style']) . '}';
    echo '.therm_legend{' . wp_strip_all_tags($optionsCSS['therm_legend_style']) . '}';
    echo '.therm_majorTick{' . wp_strip_all_tags($optionsCSS['therm_majorTick_style']) . '}';
    echo '.therm_minorTick{' . wp_strip_all_tags($optionsCSS['therm_minorTick_style']) . '}';
    echo '.therm_border{' . wp_strip_all_tags($optionsCSS['therm_border_style']) . '}';
    echo '.therm_fill{' . wp_strip_all_tags($optionsCSS['therm_fill_style']) . '}';
    echo '.therm_subTargetArrow{' . wp_strip_all_tags($optionsCSS['therm_subArrow_style']) . '}';
    echo '.therm_raisedLevel{' . wp_strip_all_tags($optionsCSS['therm_raisedLevel_style']) . '}';
    echo '.therm_subRaisedLevel{' . wp_strip_all_tags($optionsCSS['therm_subRaisedLevel_style']) . '}';
    echo '.therm_arrow{' . wp_strip_all_tags($optionsCSS['therm_arrow_style']) . '}';
    echo '.therm_subTargetLevel{' . wp_strip_all_tags($optionsCSS['therm_subTargetLevel_style']) . '}';
    echo '</style>';


    $decsep      = sanitize_text_field($thermProperties['decsep']);
    $sep         = sanitize_text_field($thermProperties['sep']);
    $orientation = (sanitize_key($thermProperties['orientation']) === 'landscape') ? 'landscape' : 'portrait';
    $width_tp    = sanitize_text_field($thermProperties['width']);
    $height_tp   = sanitize_text_field($thermProperties['height']);
    $trailing    = ($thermProperties['trailing'] === 'true') ? 'true' : 'false';
    $shadow      = (int) $thermProperties['shadow'];
    $swap        = (int) $thermProperties['swapValues'];

    // thermometer values and units
    $raisedA = explode(';',sanitize_text_field($thermProperties['raised']));
    if (end($raisedA) == 'off'){
        $showRaised = 0;
        array_splice($raisedA,-1);
    }
    else{
        $showRaised = (int) $thermProperties['showRaised'];
    }

    foreach ($raisedA as &$item) {
        if ($decsep === ',') {
            $item = floatval(str_replace(',', '.', str_replace('.', '', strval($item))));
        } else {
            $item = floatval(str_replace(',', '', strval($item)));
        }
    }
    unset($item);
    $raisedTotal = array_sum($raisedA);

    $targetA = explode(';',sanitize_text_field($thermProperties['target']));
    foreach ($targetA as &$item) {
        if ($decsep === ',') {
            $item = floatval(str_replace(',', '.', str_replace('.', '', strval($item))));
        } else {
            $item = floatval(str_replace(',', '', strval($item)));
        }
    }
    unset($item);

    if (end($targetA) == 'off'){
        $showTarget = 0;
        array_splice($targetA,-1);
    }
    else{
        $showTarget = (int) $thermProperties['showTarget'];
    }

    $showSubTargets = (int) $thermProperties['targetlabels'];
    $targetTotal    = max(0.01, (float) end($targetA));

    $currency = sanitize_text_field($thermProperties['currency']);
    $decimals = absint($thermProperties['decimals']);
    $raisedPercent = ($targetTotal > 0) ? number_format(($raisedTotal/$targetTotal * 100),$decimals,$decsep,$sep) : 100;
    $raisedValue = ($trailing == 'true') ? number_format($raisedTotal,$decimals,$decsep,$sep).$currency : $currency.number_format($raisedTotal,$decimals,$decsep,$sep);
    $targetValue = ($trailing == 'true') ? number_format($targetTotal,$decimals,$decsep,$sep).$currency : $currency.number_format($targetTotal,$decimals,$decsep,$sep);
    $tValue = ($swap == 1) ? $raisedValue : $targetValue;

    if ($showSubTargets === 1 && count($targetA) > 1) {
        $prevTarget      = prev($targetA);
        $subTargetValue  = ($trailing === 'true') ? number_format($prevTarget, $decimals, $decsep, $sep) . $currency : $currency . number_format($prevTarget, $decimals, $decsep, $sep);
    } else {
        $subTargetValue  = '0';
    }

    // colours & legend
    $colorListA = (count($raisedA) > 1 && !empty($thermProperties['colorList'])) ? explode(';', rtrim(sanitize_text_field($thermProperties['colorList']), ';')) : array($thermProperties['fill']);
    foreach($colorListA as &$color) { $color = trim(sanitize_text_field($color)); } unset($color);
    $fill2 = sanitize_text_field($thermProperties['fill2']);

    $gradID = 'ThermGrad_' . md5($colorListA[0] . '_' . $fill2);
    if ($orientation === 'landscape') {
        $gradient = '<linearGradient id="' . $gradID . '" x1="0" x2="1" y1="0" y2="0"><stop style="stop-color: ' . esc_attr($colorListA[0]) . '" offset="0%" /><stop style="stop-color: ' . esc_attr($fill2) . '" offset="100%" /></linearGradient>';
    } else {
        $gradient = '<linearGradient id="' . $gradID . '" x1="0" x2="0" y1="0" y2="1"><stop style="stop-color: ' . esc_attr($fill2) . '" offset="0%" /><stop style="stop-color: ' . esc_attr($colorListA[0]) . '" offset="100%" /></linearGradient>';
    }

    $legend = rtrim(sanitize_text_field($thermProperties['legend']),';'); // trim last semicolon if added
    $legendA = array_slice(explode(';', $legend), 0, count($raisedA));

    $percentageColor = sanitize_text_field($thermProperties['percentageColor']);
    $targetColor = sanitize_text_field($thermProperties['targetColor']);
    $raisedColor = sanitize_text_field($thermProperties['raisedColor']);
    $subTargetColor = sanitize_text_field($thermProperties['subtargetColor']);
    $basicShadow = ($shadow == 1) ? 'url(#f1)' : '';

    // basic properties of the thermometer
    $minH = ($orientation == 'landscape') ? 59.5 : 246;
    $maxH = ($orientation == 'landscape') ? 269.5 : 36;
    $tickStep = 42;
    $leftM = ($orientation == 'landscape') ? 23.5 : 20; // Y : X
    $rightM = ($orientation == 'landscape') ? 59.5 : 56; // Y : X
    $tickM = (sanitize_key($thermProperties['ticks']) == 'left' || sanitize_key($thermProperties['ticks']) == 'top') ? $leftM : $rightM;
    $markerSize = 5;
    $legendStep = 15;
    $transformY  = ($orientation === 'landscape') ? 0 : (($showTarget === 1) ? 0 : 18);
    $viewboxY    = ($showTarget === 1) ? 305 : 287;
    $viewboxX2   = ($orientation === 'landscape') ? 90 : 76;

    $targetAnchorPoint = $maxH;
    $targetAnchor      = 'middle';
    if ($orientation === 'landscape') {
        if (mb_strlen($targetValue) >= 8) {
            $targetAnchorPoint = $viewboxY - 10;
            $targetAnchor      = 'end';
        }
    }

    $targetLen = mb_strlen($tValue);
    if ($tickM === $rightM){	// left or right ticks
        if($orientation != 'landscape'){
            $viewboxX1 = ($targetLen > 7) ? ($targetLen * -2.5) + 7 : 0;
        }
        else{
            $viewboxX1 = -4;
        }
        $majorTickL = $rightM - 13;
        $minorTickL = $rightM - 6;
        $markerMargin = $rightM + 2;
        $subMarkerMargin = ($orientation == 'landscape') ? $leftM - 2 : $rightM + 2;
        $raisedMargin = ($orientation == 'landscape') ? $rightM + 15 : $rightM + 10;
        $subTargetMargin = ($orientation == 'landscape') ? $leftM - 15 : $rightM + 10;
        $raisedAnchor = 'start';
    }
    else{
        if(count($targetA) > 1){
            $viewboxX1 = ($orientation == 'landscape') ? 0 : mb_strlen($subTargetValue)*-7;
        }
        else{
            $viewboxX1 = ($orientation == 'landscape') ? 0 : mb_strlen($raisedValue)*-7;
        }

        $majorTickL = $leftM + 13;
        $minorTickL = $leftM + 6;
        $markerMargin = $leftM - 2;
        $subMarkerMargin = ($orientation == 'landscape') ? $rightM + 2 : $leftM - 2;
        $raisedMargin = ($orientation == 'landscape') ? $leftM - 15 : $leftM - 10;
        $subTargetMargin = ($orientation == 'landscape') ? $rightM + 15 : $leftM - 10;
        $raisedAnchor = 'end';
    }

    if($orientation != 'landscape'){
        if (count($targetA) > 1){
            $viewboxX2 = 76 + max(mb_strlen($raisedValue), mb_strlen($subTargetValue))*8; // expand right
            $viewboxX2 = ($targetLen > 7) ? $viewboxX2 + ($targetLen * 2.5) - 9 : $viewboxX2;
        }
        elseif (!empty($raisedValue)){
            $viewboxX2 = 76 + mb_strlen($raisedValue)*8; // expand right
            $viewboxX2 = ($targetLen > 7) ? $viewboxX2 + ($targetLen * 2.5) - 9: $viewboxX2;
        }
    }

    if (!empty($legend)){
        //count chars
        $maxRaised = max(array_map('stringLength',$raisedA, $legendA))
            + mb_strlen(esc_attr($thermProperties['currency']))
            + 3; // max legend width incl. space & ()
        if ($sep != ''){
            $maxRaised = $maxRaised + substr_count(number_format(max($raisedA),$decimals,$decsep,$sep), $sep);
        }
        if ($decimals > 0){
            $maxRaised = $maxRaised + ($decimals + 1); // incl. point
        }

        if($orientation == 'landscape'){
            $transformY = ($transformY - ($maxRaised*6.25)); // expand left
            $viewboxY = ($viewboxY + ($maxRaised*6.25)); // expand right
            $viewboxX2 = max($viewboxX2,count($legendA)*17); // expand down
        }
        else{
            $viewboxY = $viewboxY+(count($legendA)*$legendStep); // expand down
            $viewboxX2 = max($viewboxX2, $maxRaised*6.25); // expand right
        }

    }

    // title/alt attribute
    if (strtolower(sanitize_text_field($thermProperties['title'])) == 'off'){
        $title = '';
    }
    elseif(!empty($thermProperties['title'])){
        $title = sanitize_text_field($thermProperties['title']);
    }
    else{
        $title = sprintf(__('Raised %1$s towards the %2$s target.', 'donation-thermometer'), $raisedValue, $targetValue);
    }

    // size properties
    $aspectRatio = $viewboxX2/$viewboxY; // width/height
    $workAround = 'n';
    
    if (!empty($width_tp)){
        if (is_numeric(substr($width_tp,-1)) or substr($width_tp, -2) == 'px'){
            $width = preg_replace("/[^0-9.]/", "", $width_tp );
            $height = ($orientation == 'landscape') ? $width * $aspectRatio : $width / $aspectRatio;
        }
        elseif (substr($width_tp,-1) == '%'){
            $width = $width_tp;
            $height = intval($width_tp)/$aspectRatio.'%';
            $workAround = 'yesW';
        }
    }
    elseif (!empty($height_tp)){
        if (is_numeric(substr($height_tp,-1)) or substr($height_tp, -2) == 'px'){
            $height = preg_replace("/[^0-9.]/", "", $height_tp );
            $width = ($orientation == 'landscape') ? $height/$aspectRatio : $height * $aspectRatio;
        }
        elseif (substr($height_tp,-1) == '%'){
            $height = $height_tp;
            $workAround = 'yesH';
        }
    }

    /*
     *
     * start making the svg thermometer
     *
     */
    $align_style = sanitize_text_field($thermProperties['align']);
    if ($workAround == 'yesW'){
        if($orientation == 'landscape'){
            echo '<div style="margin-bottom: 1.5em; height: auto; width: '.esc_attr($width).'; '. $align_style .'">';
            echo '<svg xmlns="http://www.w3.org/tr/svg" version="2" viewbox="'.esc_attr($transformY.' '.$viewboxX1.' '.$viewboxY.' '.$viewboxX2).'" aria-label="'.esc_attr($title).'" style="width: 100%;" preserveAspectRatio="" class="thermometer_svg">';
        }
        else{
            echo '<div style="margin-bottom: 1.5em; height: auto; width: '.esc_attr($width).'; '. $align_style .'">';
            echo '<svg xmlns="http://www.w3.org/tr/svg" version="2" viewbox="'.esc_attr($viewboxX1.' '.$transformY.' '.$viewboxX2.' '.$viewboxY).'" aria-label="'.esc_attr($title).'" preserveAspectRatio="xMidYMid" class="thermometer_svg">';
        }
    }
    elseif ($workAround == 'yesH'){

        if($orientation == 'landscape'){
            echo '<div style="margin-bottom: 1.5em; width: auto; height: '.esc_attr($height).'; '. $align_style .'">';
            echo '<svg xmlns="http://www.w3.org/tr/svg" version="2" viewbox="'.esc_attr($transformY.' '.$viewboxX1.' '.$viewboxY.' '.$viewboxX2).'" aria-label="'.esc_attr($title).'" style="width: 100%;" preserveAspectRatio="" class="thermometer_svg">';
        }
        else{
            echo '<div style="display: inline-block; height: '.esc_attr($height).'; position: relative; user-select: none;">';
            echo '<canvas class="Icon-canvas" height="'.esc_attr($viewboxY).'" width="'.esc_attr($viewboxX2).'" style="display: block; height: 100% !important; visibility: hidden;"></canvas>';
            echo '<svg xmlns="http://www.w3.org/tr/svg" version="2" viewbox="'.esc_attr($viewboxX1.' '.$transformY.' '.$viewboxX2.' '.$viewboxY).'" aria-label="'.esc_attr($title).'" preserveAspectRatio="xMidYMid" class="thermometer_svg" style="height: 100%; left: 0; position: absolute; top: 0; width: 100%; ">';
        }
    }
    else{
        echo '<div style="margin-bottom: 1.5em; height: '.esc_attr($height).'px; width: '.esc_attr($width).'px; '. $align_style .'">';
        if($orientation == 'landscape'){
            echo '<svg xmlns="http://www.w3.org/tr/svg" version="2" x="0" y="0" width="'.esc_attr($width).'" height="'.esc_attr($height).'" viewbox="'.esc_attr($transformY.' '.$viewboxX1.' '.$viewboxY.' '.$viewboxX2).'" aria-label="'.esc_attr($title).'" class="thermometer_svg" style="display: block;" preserveAspectRatio="xMidYMid">';
        }
        else{
            echo '<svg xmlns="http://www.w3.org/tr/svg" version="2" x=0 y=0 width="'.esc_attr($width).'" height="'.esc_attr($height).'" viewbox="'.esc_attr($viewboxX1.' '.$transformY.' '.$viewboxX2.' '.$viewboxY) .'" aria-label="'.esc_attr($title).'" preserveAspectRatio="xMidYMid" class="thermometer_svg">';
        }
    }

    echo '<defs>'.$gradient;

    echo '<filter id="f1" x="-20%" y="-20%" height="150%" width="150%" filterUnits="objectBoundingBox">
        <feGaussianBlur in="SourceAlpha" stdDeviation="3" result="shadow"/> <!-- stdDeviation is how much to blur -->
        <feOffset dx="1.8" dy="1.8" result="offsetblur" in="shadow"/> <!-- how much to offset -->
        <feComponentTransfer result="shadow1" in="offsetblur">
            <feFuncA type="linear" slope="0.6"/> <!-- slope is the opacity of the shadow -->
        </feComponentTransfer>
        <feBlend in = "SourceGraphic" in2 = "shadow1" mode = "normal"/>
        </filter>';
    echo '<filter id="blurFilter">
          <!-- filter processes -->
          <feGaussianBlur in="SourceGraphic" stdDeviation="7.5"/><!-- stdDeviation is amount of blur -->
      </filter>
      <filter id="blurFilter2">
          <!-- filter processes -->
          <feGaussianBlur in="SourceGraphic" stdDeviation="2.2"/><!-- stdDeviation is amount of blur -->
      </filter>
  </defs>';
    echo '<desc>Created using the Donation Thermometer plugin https://wordpress.org/plugins/donation-thermometer/.</desc>';

    // outline overlay with shadow
    if ($shadow == 1){
        if ($orientation == 'landscape'){
            echo '<path d="M 280 41.5 C 280 51.5 275.5 59.5 269.5 59.5 L 54.5 59.5 C 50.5 64 43.5 66.5 37.5 66.5 C 23.5 66.5 12.5 55.5 12.5 41.5 C 12.5 27.5 23.5 16.5 37.5 16.5 C 43.5 16.5 50.5 19.5 54.5 23.5 L 269.5 23.5 C 275.5 23.5 280 31.5 280 41.5" class="therm_border" filter="'.$basicShadow.'" ></path>';
        }
        else{
            echo '<path d="M38 25.5 C 28 25.5, 20 30, '.esc_attr($leftM.' '.$maxH).' L '.esc_attr($leftM).' 251 C 15.5 255, 13 262, 13 268 C 13 282, 24 293, 38 293 C 52 293, 63 282, 63 268 C 63 262, 60 255, '.$rightM.' 251 L '.$rightM.' '.$maxH.' C '.$rightM.' 30, 48 25.5, 38 25.5" class="therm_border" filter="'.$basicShadow.'" ></path>';
        }
    }

    // target
    if ($showTarget == 1){
        if($orientation == 'landscape'){
            echo '<text x="'.esc_attr($targetAnchorPoint).'" y="'.esc_attr($subTargetMargin).'" class="therm_target" fill="'.esc_attr($targetColor).'" dominant-baseline="central" style="text-anchor:'.$targetAnchor.'!important">'.esc_html($tValue).'</text>';
        }
        else{
            echo '<text x="38" y="20" class="therm_target" fill="'.esc_attr($targetColor).'" dominant-baseline="auto" text-anchor="middle">'.esc_html($tValue).'</text>';
        }

    }

    // background fill with a transparent border
    $fill_style = !empty($optionsCSS['therm_fill_style']) ? wp_strip_all_tags($optionsCSS['therm_fill_style']) : 'fill:transparent';
    if($orientation == 'landscape'){
        echo '<path d="M 280 41.5 C 280 51.5 275.5 59.5 269.5 59.5 L 54.5 59.5 C 50.5 64 43.5 66.5 37.5 66.5 C 23.5 66.5 12.5 55.5 12.5 41.5 C 12.5 27.5 23.5 16.5 37.5 16.5 C 43.5 16.5 50.5 19.5 54.5 23.5 L 269.5 23.5 C 275.5 23.5 280 31.5 280 41.5" style="'.esc_attr($fill_style).'; stroke-opacity: 0!important;"></path>';
    }
    else{
        echo '<path d="M38 25.5 C 28 25.5, 20 30, '.esc_attr($leftM.' '.$maxH).' L '.esc_attr($leftM).' 251 C 15.5 255, 13 262, 13 268 C 13 282, 24 293, 38 293 C 52 293, 63 282, 63 268 C 63 262, 60 255, '.esc_attr($rightM.' '.$maxH).' C '.esc_attr($rightM).' 30, 48 25.5, 38 25.5" style="'.esc_attr($fill_style).'; stroke-opacity: 0!important;"></path>';
    }

    if ($shadow == 1){ // shadows only under fill
        if($orientation == 'landscape'){
            //major
            echo '<path d="M '.esc_attr($maxH.' '.$tickM).' L '.esc_attr($maxH.' '.$majorTickL).' M  '.esc_attr(($maxH-($tickStep)).' '.$tickM).' L '.esc_attr(($maxH-($tickStep)).' '.$majorTickL).' M '.esc_attr(($maxH-($tickStep*2)).' '.$tickM).' L '.esc_attr(($maxH-($tickStep*2)).' '.$majorTickL).' M'.esc_attr(($maxH-($tickStep*3)).' '.$tickM).' L '.esc_attr(($maxH-($tickStep*3)).' '.$majorTickL).' M '.esc_attr(($maxH-($tickStep*4)).' '.$tickM).' L '.esc_attr(($maxH-($tickStep*4)).' '.$majorTickL).' M '.esc_attr($minH.' '.$tickM).' L '.esc_attr($minH.' '.$majorTickL).'" class="therm_majorTick" filter="'.esc_attr($basicShadow).'"/>';
            //minor
            echo '<path d="M '.esc_attr(($maxH-$tickStep*0.5)).' '.esc_attr($tickM).' L '.esc_attr(($maxH-$tickStep*0.5)).' '.esc_attr($minorTickL).' M '.esc_attr(($maxH-$tickStep*1.5).' '.$tickM).' L '.esc_attr(($maxH-$tickStep*1.5).' '.$minorTickL).' M '.esc_attr(($maxH-$tickStep*2.5).' '.$tickM).' L '.esc_attr(($maxH-$tickStep*2.5).' '.$minorTickL).' M '.esc_attr(($maxH-$tickStep*3.5).' '.$tickM).' L '.esc_attr(($maxH-$tickStep*3.5).' '.$minorTickL).' M '.esc_attr(($maxH-$tickStep*4.5).' '.$tickM).' L '.esc_attr(($maxH-$tickStep*4.5).' '.$minorTickL).'" class="therm_minorTick" filter="'.esc_attr($basicShadow).'"/>';
        }
        else{
            //major ticks
            echo '<path d="M '.esc_attr($tickM.' '.$maxH).' L '.esc_attr($majorTickL.' '.$maxH).' M '.esc_attr($tickM.' '.($maxH+$tickStep)).' L '.esc_attr($majorTickL.' '.($maxH+$tickStep)).' M'.esc_attr($tickM.' '.($maxH+($tickStep*2))).' L '.esc_attr($majorTickL.' '.($maxH+($tickStep*2))).' M '.esc_attr($tickM.' '.($maxH+($tickStep*3))).' L '.esc_attr($majorTickL.' '.($maxH+($tickStep*3))).' M '.esc_attr($tickM.' '.($maxH+($tickStep*4))).' L '.esc_attr($majorTickL.' '.($maxH+($tickStep*4))).' M '.esc_attr($tickM.' '.$minH).' L '.esc_attr($majorTickL.' '.$minH).'" class="therm_majorTick" filter="'.esc_attr($basicShadow).'"/>';

            //minor ticks
            echo '<path d="M '.esc_attr($tickM.' '.($maxH+$tickStep*0.5)).' L '.esc_attr($minorTickL.' '.($maxH+$tickStep*0.5)).' M '.esc_attr($tickM.' '.($maxH+$tickStep*1.5)).' L '.esc_attr($minorTickL.' '.($maxH+$tickStep*1.5)).' M '.esc_attr($tickM.' '.($maxH+$tickStep*2.5)).' L '.esc_attr($minorTickL.' '.($maxH+$tickStep*2.5)).' M '.esc_attr($tickM.' '.($maxH+$tickStep*3.5)).' L '.esc_attr($minorTickL.' '.($maxH+$tickStep*3.5)).' M '.esc_attr($tickM.' '.($maxH+$tickStep*4.5)).' L '.esc_attr($minorTickL.' '.($maxH+$tickStep*4.5)).'" class="therm_minorTick" filter="'.esc_attr($basicShadow).'"/>';
        }

    }

    // fill
    $oldThermLevel = $minH;
    if ($targetTotal > 0){
        $maxLevel = ($swap == 0) ? $minH - (($minH - $maxH) * ($raisedTotal/$targetTotal)) : $minH - (($minH - $maxH) * ($targetTotal/$raisedTotal));
    }
    else{
        $maxLevel = $minH - (($minH - $maxH) * ($raisedTotal/$raisedTotal));
    }

    $i = 0;
    $raisedN = count($raisedA) - 1;
    $raisedAr = array_reverse($raisedA);

    $rValue = ($swap == 0) ? $raisedValue : $targetValue;


    if($orientation == 'landscape'){
        if($shadow == 1 & $raisedTotal <= $targetTotal){ // extra shadow for fill
            echo '<path d="M '.esc_attr($maxLevel).' 59.5 L 54.5 59.5 C 50.5 64 43.5 66.5 37.5 66.5 C 23.5 66.5 12.5 55.5 12.5 41.5 C 12.5 27.5 23.5 16.5 37.5 16.5 C 43.5 16.5 50.5 19.5 54.5 23.5 L '.esc_attr($maxLevel).' 23.5 L '.esc_attr($maxLevel).' 59.5" style="stroke-width: 0;" filter="'.esc_attr($basicShadow).'"></path>';
        }
        elseif($shadow == 1 & $raisedTotal > $targetTotal){ // extra shadow for fill
            echo '<path d="M 280 41.5 C 280 51.5 275.5 59.5 269.5 59.5 L 54.5 59.5 C 50.5 64 43.5 66.5 37.5 66.5 C 23.5 66.5 12.5 55.5 12.5 41.5 C 12.5 27.5 23.5 16.5 37.5 16.5 C 43.5 16.5 50.5 19.5 54.5 23.5 L 269.5 23.5 C 275.5 23.5 280 31.5 280 41.5" style="stroke-width: 0;"  filter="'.esc_attr($basicShadow).'"></path>';
        }
        foreach($raisedA as $r){
            if ($i == 0) {
                $newThermLevel = ($raisedTotal > $targetTotal) ? $minH - (($minH - $maxH) * ($r/$raisedTotal)) : $minH - (($minH - $maxH) * ($r/$targetTotal));
                if($raisedTotal > $targetTotal){
                    echo '<path d="M 280 41.5 C 280 51.5 275.5 59.5 269.5 59.5 L 54.5 59.5 C 50.5 64 43.5 66.5 37.5 66.5 C 23.5 66.5 12.5 55.5 12.5 41.5 C 12.5 27.5 23.5 16.5 37.5 16.5 C 43.5 16.5 50.5 19.5 54.5 23.5 L 269.5 23.5 C 275.5 23.5 280 31.5 280 41.5" style="stroke-width: 0;" fill="url(#'.esc_attr($gradID).')"/>';
                }
                else{
                    echo '<path d="M '.esc_attr($newThermLevel).' '.esc_attr($rightM).' L 54.5 '.esc_attr($rightM).' C 50.5 64 43.5 66.5 37.5 66.5 C 23.5 66.5 12.5 55.5 12.5 41.5 C 12.5 27.5 23.5 16.5 37.5 16.5 C 43.5 16.5 50.5 19.5 54.5 '.esc_attr($leftM).' L '.esc_attr($newThermLevel).' '.esc_attr($leftM).' L '.esc_attr($newThermLevel).' '.esc_attr($rightM).'" style="stroke-width: 0;" fill="url(#'.esc_attr($gradID).')"/>';
                }

                if ($i == $raisedN & $raisedTotal <= $targetTotal){
                    echo '<path d="M '.esc_attr($newThermLevel).' '.esc_attr($leftM).' L '.esc_attr($newThermLevel).' '.esc_attr($rightM).'" class="therm_raisedLevel" />';
                }
            }
            else{
                ##$fill = ($i > count($colorListA)-1) ? esc_attr($thermProperties['fill']) : trim($colorListA[$i]); // if not enough colours in list -> transparent
                $fill = ($i > count($colorListA)-1) ? 'url(#'.$gradID.')' : trim($colorListA[$i]); // if not enough colours in list -> transparent
                $newThermLevel = ($raisedTotal > $targetTotal) ? $oldThermLevel - (($minH - $maxH) * ($r/$raisedTotal)) : $oldThermLevel - (($minH - $maxH) * ($r/$targetTotal));
                if ($raisedTotal > $targetTotal & $i == $raisedN){
                    echo '<path d="M '.esc_attr($maxH).' '.esc_attr($rightM).' L '.esc_attr($oldThermLevel).' '.esc_attr($rightM).' L '.esc_attr($oldThermLevel).' '.esc_attr($leftM).' '.esc_attr($maxH).' '.esc_attr($leftM).' C 275.5 23.5 280 31.5 280 41.5 C 280 51.5 275.5 59.5 269.5 59.5" fill="'.esc_html($fill).'" style="stroke-width: 0;"/>';
                }
                else{
                    echo '<rect x="'.esc_attr($oldThermLevel).'" y="'.esc_attr($leftM).'" width="'.esc_attr($newThermLevel-$oldThermLevel).'" height="'.esc_attr($rightM-$leftM).'" fill="'.esc_html($fill).'" style="stroke-width: 0;"/>';
                }

                echo '<path d="M '.esc_attr($oldThermLevel).' '.esc_attr($leftM).' L '.esc_attr($oldThermLevel).' '.esc_attr($rightM).'" class="therm_subRaisedLevel"/>';

                if ($i == $raisedN & $raisedTotal <= $targetTotal){
                    echo '<path d="M '.esc_attr($newThermLevel).' '.esc_attr($leftM).' L '.esc_attr($newThermLevel).' '.esc_attr($rightM).'" class="therm_subRaisedLevel"/>';
                }
            }
            $i++;
            $oldThermLevel = $newThermLevel;
        }
    }

    else{ /// portrait
        if($shadow == 1 & $raisedTotal <= $targetTotal){ // extra shadow for fill
            echo '<path d="M'.esc_attr($leftM.' '.$maxLevel).' L '.esc_attr($leftM).' 251 C 15.5 255, 13 262, 13 268 C 13 282, 24 293, 38 293 C 52 293, 63 282, 63 268 C 63 262, 60 255, '.esc_attr($rightM).' 251 L '.esc_attr($rightM).' '.esc_attr($maxLevel).' L '.esc_attr($leftM).' '.esc_attr($maxLevel).'" style="stroke-width: 0;" filter="'.esc_attr($basicShadow).'"></path>';
        }
        elseif($shadow == 1 & $raisedTotal > $targetTotal){ // extra shadow for fill
            echo '<path d="M'.esc_attr($leftM).' '.esc_attr($maxH).' L '.esc_attr($leftM).' 251 C 15.5 255, 13 262, 13 268 C 13 282, 24 293, 38 293 C 52 293, 63 282, 63 268 C 63 262, 60 255, '.esc_attr($rightM).' 251 L '.esc_attr($rightM).' '.esc_attr($maxH).' C '.esc_attr($rightM).' 30, 48 25.5, 38 25.5 C 28 25.5, 20 30, '.esc_attr($leftM).' '.esc_attr($maxH).'" style="stroke-width: 0;" filter="'.esc_attr($basicShadow).'"/>';
        }

        foreach($raisedA as $r){
            if ($i == 0) {
                $newThermLevel = ($raisedTotal > $targetTotal) ? $minH - (($minH - $maxH) * ($r/$raisedTotal)) : $minH - (($minH - $maxH) * ($r/$targetTotal));
                if($raisedTotal > $targetTotal){
                    echo '<path d="M'.esc_attr($leftM).' '.esc_attr($newThermLevel).' L '.esc_attr($leftM).' 251 C 15.5 255, 13 262, 13 268 C 13 282, 24 293, 38 293 C 52 293, 63 282, 63 268 C 63 262, 60 255, '.esc_attr($rightM).' 251 L '.esc_attr($rightM).' '.esc_attr($newThermLevel).' C '.esc_attr($rightM).' 30, 48 25.5, 38 25.5 C 28 25.5, 20 30, '.esc_attr($leftM).' '.esc_attr($newThermLevel).'" style="stroke-width: 0;" fill="url(#'.esc_attr($gradID).')"/>';
                }
                else{
                    echo '<path d="M'.esc_attr($leftM).' '.esc_attr($newThermLevel).' L '.esc_attr($leftM).' 251 C 15.5 255, 13 262, 13 268 C 13 282, 24 293, 38 293 C 52 293, 63 282, 63  268 C 63 262, 60 255, '.esc_attr($rightM).' 251 L '.esc_attr($rightM).' '.esc_attr($newThermLevel).' L '.esc_attr($leftM).' '.esc_attr($newThermLevel).'" style="stroke-width: 0;" fill="url(#'.esc_attr($gradID).')"/>';
                }

                if ($i == $raisedN & $raisedTotal <= $targetTotal){
                    echo '<path d="M'.esc_attr($leftM).' '.esc_attr($newThermLevel).' L '.esc_attr($rightM).' '.esc_attr($newThermLevel).'" class="therm_raisedLevel" />';
                }
            }
            else{
                ##$fill = ($i > count($colorListA)-1) ? esc_attr($thermProperties['fill']) : trim($colorListA[$i]); // if not enough colours in list -> transparent
                $fill = ($i > count($colorListA)-1) ? 'url(#'.$gradID.')' : trim($colorListA[$i]); // if not enough colours in list -> transparent
                $newThermLevel = ($raisedTotal > $targetTotal) ? $oldThermLevel - (($minH - $maxH) * ($r/$raisedTotal)) : $oldThermLevel - (($minH - $maxH) * ($r/$targetTotal));
                if ($raisedTotal > $targetTotal & $i == $raisedN){
                    echo '<path d="M '.esc_attr($leftM).' '.esc_attr($newThermLevel).' L '.esc_attr($leftM).' '.esc_attr($oldThermLevel).' L '.esc_attr($rightM).' '.esc_attr($oldThermLevel).' L '.esc_attr($rightM).' '.esc_attr($newThermLevel).' C '.esc_attr($rightM).' 30, 48 25.5, 38 25.5 C 28 25.5, 20 30, '.esc_attr($leftM).' '.esc_attr($newThermLevel).'" fill="'.esc_attr($fill).'" style="stroke-width: 0;" />';
                }
                else{
                    echo '<rect x="'.esc_attr($leftM).'" y="'.esc_attr($newThermLevel).'" width="'.esc_attr($rightM-$leftM).'" height="'.esc_attr($oldThermLevel-$newThermLevel).'" fill="'.esc_attr($fill).'" style="stroke-width: 0;"/>';
                }


                echo '<path d="M '.esc_attr($leftM).' '.esc_attr($oldThermLevel).' L '.esc_attr($rightM).' '.esc_attr($oldThermLevel).'" class="therm_subRaisedLevel"/>';

                if ($i == $raisedN & $raisedTotal <= $targetTotal){
                    echo '<path d="M '.esc_attr($leftM).' '.esc_attr($newThermLevel).' L '.esc_attr($rightM).' '.esc_attr($newThermLevel).'" class="therm_subRaisedLevel"/>';
                }
            }
            $i++;
            $oldThermLevel = $newThermLevel;
        }
    }

    // raised value & ticks
    if ( !empty($raisedValue) && $showRaised == 1 ){
        $rValue = ($swap == 0) ? $raisedValue : $targetValue;
        $rValueLevel = ($swap == 0) ? $newThermLevel : $minH - (($minH - $maxH) * ($targetTotal/$raisedTotal));
        if($orientation == 'landscape'){
            if ( $tickM == $rightM ){
                echo '<path d="M '.esc_attr($rValueLevel).' '.esc_attr($markerMargin).', '.esc_attr($rValueLevel-$markerSize).' '.esc_attr($markerMargin+$markerSize).', '.esc_attr($rValueLevel+$markerSize).' '.esc_attr($markerMargin+$markerSize).' Z" class="therm_arrow"/>';
            }
            elseif ($tickM == $leftM){
                echo '<path d="M '.esc_attr($rValueLevel).' '.esc_attr($markerMargin).', '.esc_attr($rValueLevel+$markerSize).' '.esc_attr($markerMargin-$markerSize).', '.esc_attr($rValueLevel-$markerSize).' '.esc_attr($markerMargin-$markerSize).' Z" class="therm_arrow" />';
            }

            echo '<text x="'.esc_attr($rValueLevel).'" y="'.esc_attr($raisedMargin).'" class="therm_raised" text-anchor="middle" dominant-baseline="central" fill="'.esc_attr($raisedColor).'">'.esc_html($rValue).'</text>';
            if ($swap == 1){
                echo '<path d="M'.esc_attr($rValueLevel).' '.esc_attr($leftM).' L '.esc_attr($rValueLevel).' '.esc_attr($rightM).'" class="therm_subTargetLevel"/>';
            }
        }

        else{
            if ( $tickM == $rightM ){
                echo '<path d="M '.esc_attr($markerMargin).' '.esc_attr($rValueLevel).', '.esc_attr($markerMargin+$markerSize).' '.esc_attr($rValueLevel-$markerSize).', '.esc_attr($markerMargin+$markerSize).' '.esc_attr($rValueLevel+$markerSize).' Z" class="therm_arrow"/>';
            }
            elseif ($tickM == $leftM){
                echo '<path d="M '.esc_attr($markerMargin).' '.esc_attr($rValueLevel).', '.esc_attr($markerMargin-$markerSize).' '.esc_attr($rValueLevel+$markerSize).', '.esc_attr($markerMargin-$markerSize).' '.esc_attr($rValueLevel-$markerSize).' Z" class="therm_arrow" />';
            }
            if (esc_attr($thermProperties['ticks']) == 'right'){
                echo '<text x="'.esc_attr($raisedMargin).'" y="'.esc_attr($rValueLevel).'" class="therm_raised" text-anchor="start" dominant-baseline="central" fill="'.esc_attr($raisedColor).'">'.esc_html($rValue).'</text>';
            }
            else{
                echo '<text x="'.esc_attr($raisedMargin).'" y="'.esc_attr($rValueLevel).'" class="therm_raised" text-anchor="end" dominant-baseline="central" fill="'.esc_attr($raisedColor).'">'.esc_html($rValue).'</text>';
            }
            if ($swap == 1){
                echo '<path d="M'.esc_attr($leftM).' '.esc_attr($rValueLevel).' L '.esc_attr($rightM).' '.esc_attr($rValueLevel).'" class="therm_subTargetLevel"/>';
            }
        }
    }

    // multiple subtargets
    if( count($targetA) > 1 ){ // only if multiple targets
        foreach( array_slice($targetA,0,-1) as $t ){ // and skip the last target total
            if ($targetTotal > 0){
                $targetLevel = $minH - (($minH - $maxH) * ($t/$targetTotal));
            }
            else{
                $targetLevel = $minH;
            }
            if ($orientation == 'portrait'){ // horizontal markers
                echo '<path d="M'.esc_attr($leftM).' '.esc_attr($targetLevel).' L '.esc_attr($rightM).' '.esc_attr($targetLevel).'" class="therm_subTargetLevel"/>';
            }
            else{
                echo '<path d="M'.esc_attr($targetLevel).' '.esc_attr($leftM).' L '.esc_attr($targetLevel).' '.esc_attr($rightM).'" class="therm_subTargetLevel"/>';
            }
            if ($raisedTotal <= $t*0.9 or $raisedTotal >= $t*1.1 or $showRaised == 0){ // within 10% but only when not reached the subtotal
                if ($showSubTargets == 1){
                    $t_formatted = ($trailing == 'true') ? esc_html(number_format($t,$decimals,$decsep,$sep).$currency) : esc_html($currency.number_format($t,$decimals,$decsep,$sep));
                    if ($orientation == 'portrait'){
                        if ( $tickM == $rightM ){
                            echo '<path d="M '.esc_attr($markerMargin).' '.esc_attr($targetLevel).', '.esc_attr($markerMargin+$markerSize).' '.esc_attr($targetLevel-$markerSize).', '.esc_attr($markerMargin+$markerSize).' '.esc_attr($targetLevel+$markerSize).' Z" class="therm_subTargetArrow"/>';
                        }
                        elseif ($tickM == $leftM){
                            echo '<path d="M '.esc_attr($markerMargin).' '.esc_attr($targetLevel).', '.esc_attr($markerMargin-$markerSize).' '.esc_attr($targetLevel+$markerSize).', '.esc_attr($markerMargin-$markerSize).' '.esc_attr($targetLevel-$markerSize).' Z" class="therm_subTargetArrow" />';
                        }

                        echo '<text x="'.esc_attr($raisedMargin).'" y="'.esc_attr($targetLevel).'" fill="'.esc_attr($subTargetColor).'" class="therm_subTarget" text-anchor="'.esc_attr($raisedAnchor).'" dominant-baseline="central">'.$t_formatted.'</text>';
                    }
                    elseif($orientation == 'landscape'){
                        if ( $tickM == $rightM ){
                            echo '<path d="M '.esc_attr($targetLevel).' '.esc_attr($subMarkerMargin).', '.esc_attr($targetLevel+$markerSize).' '.esc_attr($subMarkerMargin-$markerSize).', '.esc_attr($targetLevel-$markerSize).' '.esc_attr($subMarkerMargin-$markerSize).' Z" class="therm_subTargetArrow"/>';
                        }
                        elseif ($tickM == $leftM){
                            echo '<path d="M '.esc_attr($targetLevel).' '.esc_attr($subMarkerMargin).', '.esc_attr($targetLevel-$markerSize).' '.esc_attr($subMarkerMargin+$markerSize).', '.esc_attr($targetLevel+$markerSize).' '.esc_attr($subMarkerMargin+$markerSize).' Z" class="therm_subTargetArrow" />';
                        }

                        echo '<text x="'.esc_attr($targetLevel).'" y="'.esc_attr($subTargetMargin).'" fill="'.esc_attr($subTargetColor).'" class="therm_subTarget" text-anchor="middle" dominant-baseline="central">'.$t_formatted.'</text>';
                    }
                }
            }
        }
    }


    if($orientation == 'landscape'){
        //major
        echo '<path d="M '.esc_attr($maxH).' '.esc_attr($tickM).' L '.esc_attr($maxH).' '.esc_attr($majorTickL).' M  '.esc_attr($maxH-($tickStep)).' '.esc_attr($tickM).' L '.esc_attr($maxH-($tickStep)).' '.esc_attr($majorTickL).' M '.esc_attr($maxH-($tickStep*2)).' '.esc_attr($tickM).' L '.esc_attr($maxH-($tickStep*2)).' '.esc_attr($majorTickL).' M'.esc_attr($maxH-($tickStep*3)).' '.esc_attr($tickM).' L '.esc_attr($maxH-($tickStep*3)).' '.esc_attr($majorTickL).' M '.esc_attr($maxH-($tickStep*4)).' '.esc_attr($tickM).' L '.esc_attr($maxH-($tickStep*4)).' '.esc_attr($majorTickL).' M '.esc_attr($minH).' '.esc_attr($tickM).' L '.esc_attr($minH).' '.esc_attr($majorTickL).'" class="therm_majorTick"/>';
        //minor
        echo '<path d="M '.esc_attr($maxH-$tickStep*0.5).' '.esc_attr($tickM).' L '.esc_attr($maxH-$tickStep*0.5).' '.esc_attr($minorTickL).' M '.esc_attr($maxH-$tickStep*1.5).' '.esc_attr($tickM).' L '.esc_attr($maxH-$tickStep*1.5).' '.esc_attr($minorTickL).' M '.esc_attr($maxH-$tickStep*2.5).' '.esc_attr($tickM).' L '.esc_attr($maxH-$tickStep*2.5).' '.esc_attr($minorTickL).' M '.esc_attr($maxH-$tickStep*3.5).' '.esc_attr($tickM).' L '.esc_attr($maxH-$tickStep*3.5).' '.esc_attr($minorTickL).' M '.esc_attr($maxH-$tickStep*4.5).' '.esc_attr($tickM).' L '.esc_attr($maxH-$tickStep*4.5).' '.esc_attr($minorTickL).'" class="therm_minorTick"/>';
    }
    else{
        //major ticks
        echo '<path d="M '.esc_attr($tickM).' '.esc_attr($maxH).' L '.esc_attr($majorTickL).' '.esc_attr($maxH).' M '.esc_attr($tickM).' '.esc_attr($maxH+$tickStep).' L '.esc_attr($majorTickL).' '.esc_attr($maxH+$tickStep).' M'.esc_attr($tickM).' '.esc_attr($maxH+($tickStep*2)).' L '.esc_attr($majorTickL).' '.esc_attr($maxH+($tickStep*2)).' M '.esc_attr($tickM).' '.esc_attr($maxH+($tickStep*3)).' L '.esc_attr($majorTickL).' '.esc_attr($maxH+($tickStep*3)).' M '.esc_attr($tickM).' '.esc_attr($maxH+($tickStep*4)).' L '.esc_attr($majorTickL).' '.esc_attr($maxH+($tickStep*4)).' M '.esc_attr($tickM).' '.esc_attr($minH).' L '.esc_attr($majorTickL).' '.esc_attr($minH).'" class="therm_majorTick"/>';

        //minor ticks
        echo '<path d="M '.esc_attr($tickM).' '.esc_attr($maxH+$tickStep*0.5).' L '.esc_attr($minorTickL).' '.esc_attr($maxH+$tickStep*0.5).' M '.esc_attr($tickM).' '.esc_attr($maxH+$tickStep*1.5).' L '.esc_attr($minorTickL).' '.esc_attr($maxH+$tickStep*1.5).' M '.esc_attr($tickM).' '.esc_attr($maxH+$tickStep*2.5).' L '.esc_attr($minorTickL).' '.esc_attr($maxH+$tickStep*2.5).' M '.esc_attr($tickM).' '.esc_attr($maxH+$tickStep*3.5).' L '.esc_attr($minorTickL).' '.esc_attr($maxH+$tickStep*3.5).' M '.esc_attr($tickM).' '.esc_attr($maxH+$tickStep*4.5).' L '.esc_attr($minorTickL).' '.esc_attr($maxH+$tickStep*4.5).'" class="therm_minorTick" />';
    }

    // outline overlay	// title needs to be a child element to display as tooltip
    if($orientation == 'landscape'){
        echo '<path d="M 280 41.5 C 280 51.5 275.5 59.5 269.5 59.5 L 54.5 59.5 C 50.5 64 43.5 66.5 37.5 66.5 C 23.5 66.5 12.5 55.5 12.5 41.5 C 12.5 27.5 23.5 16.5 37.5 16.5 C 43.5 16.5 50.5 19.5 54.5 23.5 L 269.5 23.5 C 275.5 23.5 280 31.5 280 41.5" class="therm_border"><title>'.esc_html($title).'</title></path>';
    }
    else{
        echo '<path d="M38 25.5 C 28 25.5, 20 30, '.esc_attr($leftM).' '.esc_attr($maxH).' L '.esc_attr($leftM).' 251 C 15.5 255, 13 262, 13 268 C 13 282, 24 293, 38 293 C 52 293, 63 282, 63 268 C 63 262, 60 255, '.esc_attr($rightM).' 251 L '.esc_attr($rightM).' '.esc_attr($maxH).' C '.esc_attr($rightM).' 30, 48 25.5, 38 25.5" class="therm_border"><title>'.esc_html($title).'</title></path>';
    }


    // percentage
    if (esc_attr($thermProperties['showPercent']) == 1){
        if (mb_strlen($raisedPercent) < 3){
            $fontS_percent = 17;
        }
        elseif (mb_strlen($raisedPercent) < 4){
            $fontS_percent = 15;
        }
        elseif (mb_strlen($raisedPercent) < 6){
            $fontS_percent = 12;
        }
        else{
            $fontS_percent = 10;
        }

        if($orientation == 'landscape'){
            echo '<text x="37.5" y="41.5" class="therm_percent" style="text-anchor:middle;font-size: '.$fontS_percent.'px" dominant-baseline="central"  fill="'.esc_html($percentageColor).'">'.esc_html($raisedPercent).'%</text>';
        }
        else{
            echo '<text x="38" y="274" class="therm_percent" style="font-size: '.$fontS_percent.'px" text-anchor="middle" fill="'.esc_html($percentageColor).'">'.esc_html($raisedPercent).'%</text>';
        }
    }

    // legend
    if(!empty($legend)){
        $legendAr = array_reverse($legendA);
        $raisedAr = array_reverse($raisedA);
        $i = count($raisedAr) - 1; // for color
        $i2 = count($legendAr) - 1;
        $j = 0;

        if($orientation == 'landscape'){
            $legendLevel = 10;
            echo '<text class="therm_legend" x="'.($legendLevel-10).'" y="'.max(0,(41.5-((($legendStep+6)*count($legendAr))/2))).'" text-anchor="end" dominant-baseline="central">';
        }
        else{
            $legendLevel = 295;
            echo '<text class="therm_legend" x="'.($viewboxX1+4).'" y="'.$legendLevel.'" dominant-baseline="baseline" text-anchor="start">';
        }
        foreach($raisedAr as $r){
            if($i > $i2){
                $i--;
                continue;
            }
            $legendColor = (array_key_exists($i, $colorListA)) ? trim($colorListA[$i]) : 'black';
            if($orientation == 'landscape'){
                echo '<tspan x="'.($legendLevel-10).'" dy="'.$legendStep.'" fill="'.$legendColor.'" text-anchor="end" alignment-baseline="central">'.esc_html($legendAr[$j]);
            }
            else{
                echo '<tspan x="'.($viewboxX1+4).'" dy="'.$legendStep.'" fill="'.$legendColor.'" text-anchor="start" alignment-baseline="central">'.esc_html($legendAr[$j]);
            }
            if (count($raisedA) >= 1){
                echo ($trailing == 'true') ? esc_html(' ('.trim(number_format($r,$decimals,$decsep,$sep)).$currency.')') : esc_html(' ('.$currency.trim(number_format($r,$decimals,$decsep,$sep))).')</tspan>';
            }

            $i--;
            $j++;
        }
        echo '</text>';
    }

    echo '</svg></div>';

    return ob_get_clean();
}
