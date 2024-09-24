<?PHP
/*
 * Creates the SVG thermometer
 */

function stringLength($a,$b){
    return mb_strlen($a.$b);
}

function thermhtml($thermProperties){
    ob_start();
    global $thermDefaultStyle;
    $optionsCSS = wp_parse_args( get_option('thermometer_style',$thermDefaultStyle), $thermDefaultStyle);
    echo '<style>
	.thermometer_svg{'.$optionsCSS['thermometer_svg'].'}
	.therm_target{'.$optionsCSS['therm_target_style'].'}
	.therm_raised{'.$optionsCSS['therm_raised_style'].'}
	.therm_percent{'.$optionsCSS['therm_percent_style'].'}
	.therm_subTarget{'.$optionsCSS['therm_subTarget_style'].'}
	.therm_legend{'.$optionsCSS['therm_legend_style'].'}
	.therm_majorTick{'.$optionsCSS['therm_majorTick_style'].'}
	.therm_minorTick{'.$optionsCSS['therm_minorTick_style'].'}
	.therm_border{'.$optionsCSS['therm_border_style'].'}
	.therm_subTargetArrow{'.$optionsCSS['therm_subArrow_style'].'}
    .therm_raisedLevel{'.$optionsCSS['therm_raisedLevel_style'].'}
	.therm_subRaisedLevel{'.$optionsCSS['therm_subRaisedLevel_style'].'}
	.therm_arrow{'.$optionsCSS['therm_arrow_style'].'}
	.therm_subTargetLevel{'.$optionsCSS['therm_subTargetLevel_style'].'}
	</style>';

    // thermometer values and units
    $raisedA = explode(';',$thermProperties['raised']);
    if (end($raisedA) == 'off'){
        $showRaised = 0;
        array_splice($raisedA,-1);
    }
    else{
        $showRaised = $thermProperties['showRaised'];
    }

    if ($thermProperties['decsep'] == ','){
        foreach($raisedA as &$item) {
            $item = floatval(str_replace(',', '.', str_replace('.', '', strval($item))));
        }
    }
    else{
        foreach($raisedA as &$item) {
            $item = floatval(str_replace(',', '', strval($item)));
        }
    }
    $raisedTotal = array_sum($raisedA);

    $targetA = explode(';',$thermProperties['target']);
    if ($thermProperties['decsep'] == ','){
        foreach($targetA as &$item) {
            $item = floatval(str_replace(',', '.', str_replace('.', '', strval($item))));
        }
    }
    else{
        foreach($targetA as &$item) {
            $item = floatval(str_replace(',', '', strval($item)));
        }
    }
    if (end($targetA) == 'off'){
        $showTarget = 0;
        array_splice($targetA,-1);
    }
    else{
        $showTarget = $thermProperties['showTarget'];
    }

    $showSubTargets = $thermProperties['targetlabels'];
    $targetTotal = max(0,end($targetA));

    $currency = $thermProperties['currency'];
    $decimals = $thermProperties['decimals'];
    $raisedPercent = ($targetTotal > 0) ? number_format(($raisedTotal/$targetTotal * 100),$decimals,$thermProperties['decsep'],$thermProperties['sep']) : 100;
    $raisedValue = ($thermProperties['trailing'] == 'true') ? number_format($raisedTotal,$decimals,$thermProperties['decsep'],$thermProperties['sep']).$currency : $currency.number_format($raisedTotal,$decimals,$thermProperties['decsep'],$thermProperties['sep']);
    $targetValue = ($thermProperties['trailing'] == 'true') ? number_format($targetTotal,$decimals,$thermProperties['decsep'],$thermProperties['sep']).$currency : $currency.number_format($targetTotal,$decimals,$thermProperties['decsep'],$thermProperties['sep']);
    $tValue = ($thermProperties['swapValues'] == 1) ? $raisedValue : $targetValue;
    end($targetA); // move pointer to end of array
    if ($showSubTargets == 1){
        $subTargetValue = ($thermProperties['trailing'] == 'true') ? number_format(prev($targetA),$decimals,$thermProperties['decsep'],$thermProperties['sep']).$currency : $currency.number_format(prev($targetA),$decimals,$thermProperties['decsep'],$thermProperties['sep']);
    }
    else{
        $subTargetValue = 0;
    }

    // colours & legend
    if (sizeof($raisedA) > 1 && !empty($thermProperties['colorList'])){
        $colorListA = explode(';',rtrim($thermProperties['colorList'],';'));
    }
    else{
        $colorListA = array($thermProperties['fill']);
    }

    if($thermProperties['orientation'] == 'landscape') {
        $gradID = 'ThermGrad_'. esc_html(trim($colorListA[0])) . '_' . $thermProperties['fill2'];
        $gradient = '<linearGradient id="'.$gradID.'" x1="0" x2="1" y1="0" y2="0">
          <stop style="stop-color: ' . esc_html(trim($colorListA[0])) . '" offset="0%" />
          <stop style="stop-color: ' . $thermProperties['fill2'] . '" offset="100%" />
        </linearGradient>';
    }
    else{
        $gradID = 'ThermGrad_'. $thermProperties['fill2'] . '_' . esc_html(trim($colorListA[0]));
        $gradient = '<linearGradient id="'.$gradID.'" x1="0" x2="0" y1="0" y2="1">
          <stop style="stop-color: ' . $thermProperties['fill2'] . '" offset="0%" />
          <stop style="stop-color: ' . esc_html(trim($colorListA[0])) . '" offset="100%" />
        </linearGradient>';
    }

    $legend = rtrim($thermProperties['legend'],';'); // trim last semicolon if added
    $legendA = explode(';',$legend);
    $legendA = array_slice($legendA,0,count($raisedA)); // shorten legend entries to match raised value count

    $percentageColor = $thermProperties['percentageColor'];
    $targetColor = $thermProperties['targetColor'];
    $raisedColor = $thermProperties['raisedColor'];
    $subTargetColor = $thermProperties['subtargetColor'];
    $basicShadow = ($thermProperties['shadow'] == 1) ? 'url(#f1)' : '';

    // basic properties of the thermometer
    $minH = ($thermProperties['orientation'] == 'landscape') ? 59.5 : 246;
    $maxH = ($thermProperties['orientation'] == 'landscape') ? 269.5 : 36;
    $tickStep = 42;
    $leftM = ($thermProperties['orientation'] == 'landscape') ? 23.5 : 20; // Y : X
    $rightM = ($thermProperties['orientation'] == 'landscape') ? 59.5 : 56; // Y : X
    $tickM = ($thermProperties['ticks'] == 'left' || $thermProperties['ticks'] == 'top') ? $leftM : $rightM;
    $markerSize = 5;
    $legendStep = 15;

    // transforms to svg depending what is shown
    if($thermProperties['orientation'] == 'landscape'){
        $transformY = 0;
    }
    else{
        $transformY = ($showTarget == '1') ? 0 : 18; // show target value move down
    }
    $viewboxY = ($showTarget == '1') ? 305 : 287;
    $viewboxX2 = ($thermProperties['orientation'] == 'landscape') ? 90 : 76;

    if($thermProperties['orientation'] == 'landscape'){
        if (mb_strlen($targetValue)<8){
            $targetAnchorPoint = $maxH;
            $targetAnchor = 'middle';
        }
        else{
            $targetAnchorPoint = $viewboxY -10;
            $targetAnchor = 'end';
        }
    }

    $targetLen = mb_strlen($tValue);
    if (!empty($tickM) && $tickM == $rightM){	// left or right ticks
        if($thermProperties['orientation'] != 'landscape'){
            $viewboxX1 = ($targetLen > 7) ? ($targetLen * -2.5) + 7 : 0;
        }
        else{
            $viewboxX1 = -4;
        }
        $majorTickL = $rightM - 13;
        $minorTickL = $rightM - 6;
        $markerMargin = $rightM + 2;
        $subMarkerMargin = ($thermProperties['orientation'] == 'landscape') ? $leftM - 2 : $rightM + 2;
        $raisedMargin = ($thermProperties['orientation'] == 'landscape') ? $rightM + 15 : $rightM + 10;
        $subTargetMargin = ($thermProperties['orientation'] == 'landscape') ? $leftM - 15 : $rightM + 10;
        $raisedAnchor = 'start';
    }
    else{
        if(count($targetA) > 1){
            $viewboxX1 = ($thermProperties['orientation'] == 'landscape') ? 0 : mb_strlen($subTargetValue)*-7;
        }
        else{
            $viewboxX1 = ($thermProperties['orientation'] == 'landscape') ? 0 : mb_strlen($raisedValue)*-7;
        }

        $majorTickL = $leftM + 13;
        $minorTickL = $leftM + 6;
        $markerMargin = $leftM - 2;
        $subMarkerMargin = ($thermProperties['orientation'] == 'landscape') ? $rightM + 2 : $leftM - 2;
        $raisedMargin = ($thermProperties['orientation'] == 'landscape') ? $leftM - 15 : $leftM - 10;
        $subTargetMargin = ($thermProperties['orientation'] == 'landscape') ? $rightM + 15 : $leftM - 10;
        $raisedAnchor = 'end';
    }

    if($thermProperties['orientation'] != 'landscape'){
        if (count($targetA) > 1){
            $viewboxX2 = 76 + mb_strlen($subTargetValue)*8; // expand right
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
            + mb_strlen($thermProperties['currency'])
            + 3; // max legend width incl. space & ()
        if ($thermProperties['sep'] != ''){
            $maxRaised = $maxRaised + substr_count(number_format(max($raisedA),$decimals,$thermProperties['decsep'],$thermProperties['sep']), $thermProperties['sep']);
        }
        if ($decimals > 0){
            $maxRaised = $maxRaised + ($decimals + 1); // incl. point
        }

        if($thermProperties['orientation'] == 'landscape'){
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
    if (strtolower($thermProperties['title']) == 'off'){
        $title = '';
    }
    elseif(!empty($thermProperties['title'])){
        $title = $thermProperties['title'];
    }
    else{
        $title = sprintf(/* translators: 1: the raised value 2: the target value */__('Raised %1$s towards the %2$s target.', 'donation-thermometer'),$raisedValue,$targetValue);
    }

    // size properties

    $aspectRatio = $viewboxX2/$viewboxY; // width/height
    $workAround = 'n';
    if (!empty($thermProperties['width'])){
        if (is_numeric(substr($thermProperties['width'],-1)) or substr($thermProperties['width'], -2) == 'px'){
            $width = preg_replace("/[^0-9]/", "", $thermProperties['width'] );
            $height = ($thermProperties['orientation'] == 'landscape') ? $width * $aspectRatio : $width / $aspectRatio;
        }
        elseif (substr($thermProperties['width'],-1) == '%'){
            $width = $thermProperties['width'];
            $height = intval($thermProperties['width'])/$aspectRatio.'%';
            $workAround = 'yesW';
        }
    }
    elseif (!empty($thermProperties['height'])){
        if (is_numeric(substr($thermProperties['height'],-1)) or substr($thermProperties['height'], -2) == 'px'){
            $height = preg_replace("/[^0-9]/", "", $thermProperties['height'] );
            $width = ($thermProperties['orientation'] == 'landscape') ? $height/$aspectRatio : $height * $aspectRatio;
        }
        elseif (substr($thermProperties['height'],-1) == '%'){
            $height = $thermProperties['height'];
            $workAround = 'yesH';
        }
    }

    /*
     *
     * start making the svg thermometer
     *
     */

    if ($workAround == 'yesW'){
        if($thermProperties['orientation'] == 'landscape'){
            echo '<div style="margin-bottom: 1.5em; height: auto; width: '.esc_html($width).'; '.esc_html($thermProperties['align']).'">';
            echo '<svg xmlns="http://www.w3.org/tr/svg" version="2" viewbox="'.$transformY.' '.$viewboxX1.' '.$viewboxY.' '.$viewboxX2.'" 		alt="'.esc_html($title).'" style="width: 100%;" preserveAspectRatio="" class="thermometer_svg">';
        }
        else{
            echo '<div style="margin-bottom: 1.5em; height: auto; width: '.esc_html($width).'; '.esc_html($thermProperties['align']).'">';
            echo '<svg xmlns="http://www.w3.org/tr/svg" version="2" viewbox="'.$viewboxX1.' '.$transformY.' '.$viewboxX2.' '.$viewboxY.'" 		alt="'.esc_html($title).'" preserveAspectRatio="xMidYMid" class="thermometer_svg">';
        }
    }
    elseif ($workAround == 'yesH'){

        if($thermProperties['orientation'] == 'landscape'){
            echo '<div style="margin-bottom: 1.5em; width: auto; height: '.esc_html($height).'; '.$thermProperties['align'].'">';
            echo '<svg xmlns="http://www.w3.org/tr/svg" version="2" viewbox="'.$transformY.' '.$viewboxX1.' '.$viewboxY.' '.$viewboxX2.'" 		alt="'.esc_html($title).'" style="width: 100%;" preserveAspectRatio="" class="thermometer_svg">';
        }
        else{
            echo '<div style="display: inline-block; height: '.esc_html($height).'; position: relative; user-select: none;">';
            echo '<canvas class="Icon-canvas" height="'.$viewboxY.'" width="'.$viewboxX2.'" style="display: block; height: 100% !important; visibility: hidden;"></canvas>';
            echo '<svg xmlns="http://www.w3.org/tr/svg" version="2" viewbox="'.$viewboxX1.' '.$transformY.' '.$viewboxX2.' '.$viewboxY.'" 		alt="'.esc_html($title).'" preserveAspectRatio="xMidYMid" class="thermometer_svg" style="height: 100%; left: 0; position: absolute; top: 0; width: 100%; ">';
        }
    }
    else{
        echo '<div style="margin-bottom: 1.5em; height: '.esc_html($height).'px; width: '.esc_html($width).'px; '.esc_html($thermProperties['align']).'">';
        if($thermProperties['orientation'] == 'landscape'){
            echo '<svg xmlns="http://www.w3.org/tr/svg" version="2" x="0" y="0" width="'.esc_html($width).'" height="'.esc_html($height).'" viewbox="'.$transformY.' '.$viewboxX1.' '.($viewboxY).' '.$viewboxX2.'" alt="'.esc_html($title).'" class="thermometer_svg" style="display: block;" preserveAspectRatio="xMidYMid">';
        }
        else{
            echo '<svg xmlns="http://www.w3.org/tr/svg" version="2" x=0 y=0 width="'.esc_html($width).'" height="'.esc_html($height).'" viewbox="'.$viewboxX1.' '.$transformY.' '.$viewboxX2.' '.$viewboxY.'" alt="'.esc_html($title).'" preserveAspectRatio="xMidYMid" class="thermometer_svg">';
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
    if ($thermProperties['shadow'] == 1){
        if ($thermProperties['orientation'] == 'landscape'){
            echo '<path d="M 280 41.5 C 280 51.5 275.5 59.5 269.5 59.5 L 54.5 59.5 C 50.5 64 43.5 66.5 37.5 66.5 C 23.5 66.5 12.5 55.5 12.5 41.5 C 12.5 27.5 23.5 16.5 37.5 16.5 C 43.5 16.5 50.5 19.5 54.5 23.5 L 269.5 23.5 C 275.5 23.5 280 31.5 280 41.5" class="therm_border" filter="'.$basicShadow.'" ></path>';
        }
        else{
            echo '<path d="M38 25.5 C 28 25.5, 20 30, '.$leftM.' '.$maxH.' L '.$leftM.' 251 C 15.5 255, 13 262, 13 268 C 13 282, 24 293, 38 293 C 52 293, 63 282, 63 268 C 63 262, 60 255, '.$rightM.' 251 L '.$rightM.' '.$maxH.' C '.$rightM.' 30, 48 25.5, 38 25.5" class="therm_border" filter="'.$basicShadow.'" ></path>';
        }
    }

    // target
    if ($showTarget == 1){
        if($thermProperties['orientation'] == 'landscape'){
            echo '<text x="'.$targetAnchorPoint.'" y="'.$subTargetMargin.'" class="therm_target" fill="'.esc_html($targetColor).'" dominant-baseline="central" style="text-anchor:'.$targetAnchor.'!important">'.esc_html($tValue).'</text>';
        }
        else{
            echo '<text x="38" y="20" class="therm_target" fill="'.esc_html($targetColor).'" dominant-baseline="baseline" text-anchor="middle">'.esc_html($tValue).'</text>';
        }

    }

    // background fill with a transparent border
    if($thermProperties['orientation'] == 'landscape'){
        echo '<path d="M 280 41.5 C 280 51.5 275.5 59.5 269.5 59.5 L 54.5 59.5 C 50.5 64 43.5 66.5 37.5 66.5 C 23.5 66.5 12.5 55.5 12.5 41.5 C 12.5 27.5 23.5 16.5 37.5 16.5 C 43.5 16.5 50.5 19.5 54.5 23.5 L 269.5 23.5 C 275.5 23.5 280 31.5 280 41.5" style="'.$optionsCSS['therm_fill_style'].'; stroke-opacity: 0!important;"><title>'.esc_html($title).'</title></path>';
    }
    else{
        echo '<path d="M38 25.5 C 28 25.5, 20 30, '.$leftM.' '.$maxH.' L '.$leftM.' 251 C 15.5 255, 13 262, 13 268 C 13 282, 24 293, 38 293 C 52 293, 63 282, 63 268 C 63 262, 60 255, '.$rightM.' 251 L '.$rightM.' '.$maxH.' C '.$rightM.' 30, 48 25.5, 38 25.5" style="'.$optionsCSS['therm_fill_style'].'; stroke-opacity: 0!important;"><title>'.esc_html($title).'</title></path>';
    }

    if ($thermProperties['shadow'] == 1){ // shadows only under fill
        if($thermProperties['orientation'] == 'landscape'){
            //major
            echo '<path d="M '.$maxH.' '.$tickM.' L '.$maxH.' '.$majorTickL.' M  '.($maxH-($tickStep*1)).' '.$tickM.' L '.($maxH-($tickStep*1)).' '.$majorTickL.' M '.($maxH-($tickStep*2)).' '.$tickM.' L '.($maxH-($tickStep*2)).' '.$majorTickL.' M'.($maxH-($tickStep*3)).' '.$tickM.' L '.($maxH-($tickStep*3)).' '.$majorTickL.' M '.($maxH-($tickStep*4)).' '.$tickM.' L '.($maxH-($tickStep*4)).' '.$majorTickL.' M '.$minH.' '.$tickM.' L '.$minH.' '.$majorTickL.'" class="therm_majorTick" filter="'.$basicShadow.'"/>';
            //minor
            echo '<path d="M '.($maxH-$tickStep*0.5).' '.$tickM.' L '.($maxH-$tickStep*0.5).' '.$minorTickL.' M '.($maxH-$tickStep*1.5).' '.$tickM.' L '.($maxH-$tickStep*1.5).' '.$minorTickL.' M '.($maxH-$tickStep*2.5).' '.$tickM.' L '.($maxH-$tickStep*2.5).' '.$minorTickL.' M '.($maxH-$tickStep*3.5).' '.$tickM.' L '.($maxH-$tickStep*3.5).' '.$minorTickL.' M '.($maxH-$tickStep*4.5).' '.$tickM.' L '.($maxH-$tickStep*4.5).' '.$minorTickL.'" class="therm_minorTick" filter="'.$basicShadow.'"/>';
        }
        else{
            //major ticks
            echo '<path d="M '.$tickM.' '.$maxH.' L '.$majorTickL.' '.$maxH.' M '.$tickM.' '.($maxH+$tickStep).' L '.$majorTickL.' '.($maxH+$tickStep).' M'.$tickM.' '.($maxH+($tickStep*2)).' L '.$majorTickL.' '.($maxH+($tickStep*2)).' M '.$tickM.' '.($maxH+($tickStep*3)).' L '.$majorTickL.' '.($maxH+($tickStep*3)).' M '.$tickM.' '.($maxH+($tickStep*4)).' L '.$majorTickL.' '.($maxH+($tickStep*4)).' M '.$tickM.' '.$minH.' L '.$majorTickL.' '.$minH.'" class="therm_majorTick" filter="'.$basicShadow.'"/>';

            //minor ticks
            echo '<path d="M '.$tickM.' '.($maxH+$tickStep*0.5).' L '.$minorTickL.' '.($maxH+$tickStep*0.5).' M '.$tickM.' '.($maxH+$tickStep*1.5).' L '.$minorTickL.' '.($maxH+$tickStep*1.5).' M '.$tickM.' '.($maxH+$tickStep*2.5).' L '.$minorTickL.' '.($maxH+$tickStep*2.5).' M '.$tickM.' '.($maxH+$tickStep*3.5).' L '.$minorTickL.' '.($maxH+$tickStep*3.5).' M '.$tickM.' '.($maxH+$tickStep*4.5).' L '.$minorTickL.' '.($maxH+$tickStep*4.5).'" class="therm_minorTick" filter="'.$basicShadow.'"/>';
        }

    }

    // fill
    $oldThermLevel = $minH;
    if ($targetTotal > 0){
        $maxLevel = ($thermProperties['swapValues'] == 0) ? $minH - (($minH - $maxH) * ($raisedTotal/$targetTotal)) : $minH - (($minH - $maxH) * ($targetTotal/$raisedTotal));
    }
    else{
        $maxLevel = $minH - (($minH - $maxH) * ($raisedTotal/$raisedTotal));
    }

    $i = 0;
    $raisedN = count($raisedA) - 1;
    $raisedAr = array_reverse($raisedA);

    $rValue = ($thermProperties['swapValues'] == 0) ? $raisedValue : $targetValue;


    if($thermProperties['orientation'] == 'landscape'){
        if($thermProperties['shadow'] == 1 & $raisedTotal <= $targetTotal){ // extra shadow for fill
            echo '<path d="M '.$maxLevel.' 59.5 L 54.5 59.5 C 50.5 64 43.5 66.5 37.5 66.5 C 23.5 66.5 12.5 55.5 12.5 41.5 C 12.5 27.5 23.5 16.5 37.5 16.5 C 43.5 16.5 50.5 19.5 54.5 23.5 L '.$maxLevel.' 23.5 L '.$maxLevel.' 59.5" style="stroke-width: 0;" filter="'.$basicShadow.'"></path>';
        }
        elseif($thermProperties['shadow'] == 1 & $raisedTotal > $targetTotal){ // extra shadow for fill
            echo '<path d="M 280 41.5 C 280 51.5 275.5 59.5 269.5 59.5 L 54.5 59.5 C 50.5 64 43.5 66.5 37.5 66.5 C 23.5 66.5 12.5 55.5 12.5 41.5 C 12.5 27.5 23.5 16.5 37.5 16.5 C 43.5 16.5 50.5 19.5 54.5 23.5 L 269.5 23.5 C 275.5 23.5 280 31.5 280 41.5" style="stroke-width: 0;"  filter="'.$basicShadow.'"></path>';
        }
        foreach($raisedA as $r){
            if ($i == 0) {
                $newThermLevel = ($raisedTotal > $targetTotal) ? $minH - (($minH - $maxH) * ($r/$raisedTotal)) : $minH - (($minH - $maxH) * ($r/$targetTotal));
                if($raisedTotal > $targetTotal){
                    #echo '<path d="M 280 41.5 C 280 51.5 275.5 59.5 269.5 59.5 L 54.5 59.5 C 50.5 64 43.5 66.5 37.5 66.5 C 23.5 66.5 12.5 55.5 12.5 41.5 C 12.5 27.5 23.5 16.5 37.5 16.5 C 43.5 16.5 50.5 19.5 54.5 23.5 L 269.5 23.5 C 275.5 23.5 280 31.5 280 41.5" style="stroke-width: 0;" fill="'.esc_html(trim($colorListA[$i])).'"/>';
                    echo '<path d="M 280 41.5 C 280 51.5 275.5 59.5 269.5 59.5 L 54.5 59.5 C 50.5 64 43.5 66.5 37.5 66.5 C 23.5 66.5 12.5 55.5 12.5 41.5 C 12.5 27.5 23.5 16.5 37.5 16.5 C 43.5 16.5 50.5 19.5 54.5 23.5 L 269.5 23.5 C 275.5 23.5 280 31.5 280 41.5" style="stroke-width: 0;" fill="url(#'.$gradID.')"/>';
                }
                else{
                    #echo '<path d="M '.$newThermLevel.' '.$rightM.' L 54.5 '.$rightM.' C 50.5 64 43.5 66.5 37.5 66.5 C 23.5 66.5 12.5 55.5 12.5 41.5 C 12.5 27.5 23.5 16.5 37.5 16.5 C 43.5 16.5 50.5 19.5 54.5 '.$leftM.' L '.$newThermLevel.' '.$leftM.' L '.$newThermLevel.' '.$rightM.'" style="stroke-width: 0;" fill="'.esc_html(trim($colorListA[$i])).'"/>';
                    echo '<path d="M '.$newThermLevel.' '.$rightM.' L 54.5 '.$rightM.' C 50.5 64 43.5 66.5 37.5 66.5 C 23.5 66.5 12.5 55.5 12.5 41.5 C 12.5 27.5 23.5 16.5 37.5 16.5 C 43.5 16.5 50.5 19.5 54.5 '.$leftM.' L '.$newThermLevel.' '.$leftM.' L '.$newThermLevel.' '.$rightM.'" style="stroke-width: 0;" fill="url(#'.$gradID.')"/>';
                }

                if ($i == $raisedN & $raisedTotal <= $targetTotal){
                    echo '<path d="M '.$newThermLevel.' '.$leftM.' L '.$newThermLevel.' '.$rightM.'" class="therm_raisedLevel" />';
                }
            }
            else{
                ##$fill = ($i > count($colorListA)-1) ? $thermProperties['fill'] : trim($colorListA[$i]); // if not enough colours in list -> transparent
                $fill = ($i > count($colorListA)-1) ? 'url(#'.$gradID.')' : trim($colorListA[$i]); // if not enough colours in list -> transparent
                $newThermLevel = ($raisedTotal > $targetTotal) ? $oldThermLevel - (($minH - $maxH) * ($r/$raisedTotal)) : $oldThermLevel - (($minH - $maxH) * ($r/$targetTotal));
                if ($raisedTotal > $targetTotal & $i == $raisedN){
                    echo '<path d="M '.$maxH.' '.$rightM.' L '.$oldThermLevel.' '.$rightM.' L '.$oldThermLevel.' '.$leftM.' '.$maxH.' '.$leftM.' C 275.5 23.5 280 31.5 280 41.5 C 280 51.5 275.5 59.5 269.5 59.5" fill="'.esc_html($fill).'" style="stroke-width: 0;"/>';
                }
                else{
                    echo '<rect x="'.$oldThermLevel.'" y="'.$leftM.'" width="'.($newThermLevel-$oldThermLevel).'" height="'.($rightM-$leftM).'" fill="'.esc_html($fill).'" style="stroke-width: 0;"/>';
                }

                echo '<path d="M '.$oldThermLevel.' '.$leftM.' L '.$oldThermLevel.' '.$rightM.'" class="therm_subRaisedLevel"/>';

                if ($i == $raisedN & $raisedTotal <= $targetTotal){
                    echo '<path d="M '.$newThermLevel.' '.$leftM.' L '.$newThermLevel.' '.$rightM.'" class="therm_subRaisedLevel"/>';
                }
            }
            $i++;
            $oldThermLevel = $newThermLevel;
        }
    }

    else{ /// portrait
        if($thermProperties['shadow'] == 1 & $raisedTotal <= $targetTotal){ // extra shadow for fill
            echo '<path d="M'.$leftM.' '.$maxLevel.' L '.$leftM.' 251 C 15.5 255, 13 262, 13 268 C 13 282, 24 293, 38 293 C 52 293, 63 282, 63 268 C 63 262, 60 255, '.$rightM.' 251 L '.$rightM.' '.$maxLevel.' L '.$leftM.' '.$maxLevel.'" style="stroke-width: 0;" filter="'.$basicShadow.'"></path>';
        }
        elseif($thermProperties['shadow'] == 1 & $raisedTotal > $targetTotal){ // extra shadow for fill
            echo '<path d="M'.$leftM.' '.$maxH.' L '.$leftM.' 251 C 15.5 255, 13 262, 13 268 C 13 282, 24 293, 38 293 C 52 293, 63 282, 63 268 C 63 262, 60 255, '.$rightM.' 251 L '.$rightM.' '.$maxH.' C '.$rightM.' 30, 48 25.5, 38 25.5 C 28 25.5, 20 30, '.$leftM.' '.$maxH.'" style="stroke-width: 0;" filter="'.$basicShadow.'"/>';
        }

        foreach($raisedA as $r){
            if ($i == 0) {
                $newThermLevel = ($raisedTotal > $targetTotal) ? $minH - (($minH - $maxH) * ($r/$raisedTotal)) : $minH - (($minH - $maxH) * ($r/$targetTotal));
                if($raisedTotal > $targetTotal){
                    #echo '<path d="M'.$leftM.' '.$newThermLevel.' L '.$leftM.' 251 C 15.5 255, 13 262, 13 268 C 13 282, 24 293, 38 293 C 52 293, 63 282, 63 268 C 63 262, 60 255, '.$rightM.' 251 L '.$rightM.' '.$newThermLevel.' C '.$rightM.' 30, 48 25.5, 38 25.5 C 28 25.5, 20 30, '.$leftM.' '.$newThermLevel.'" style="stroke-width: 0;" fill="'.esc_html(trim($colorListA[$i])).'"/>';
                    echo '<path d="M'.$leftM.' '.$newThermLevel.' L '.$leftM.' 251 C 15.5 255, 13 262, 13 268 C 13 282, 24 293, 38 293 C 52 293, 63 282, 63 268 C 63 262, 60 255, '.$rightM.' 251 L '.$rightM.' '.$newThermLevel.' C '.$rightM.' 30, 48 25.5, 38 25.5 C 28 25.5, 20 30, '.$leftM.' '.$newThermLevel.'" style="stroke-width: 0;" fill="url(#'.$gradID.')"/>';
                }
                else{
                    #echo '<path d="M'.$leftM.' '.$newThermLevel.' L '.$leftM.' 251 C 15.5 255, 13 262, 13 268 C 13 282, 24 293, 38 293 C 52 293, 63 282, 63  268 C 63 262, 60 255, '.$rightM.' 251 L '.$rightM.' '.$newThermLevel.' L '.$leftM.' '.$newThermLevel.'" style="stroke-width: 0;" fill="'.esc_html(trim($colorListA[$i])).'"/>';
                    echo '<path d="M'.$leftM.' '.$newThermLevel.' L '.$leftM.' 251 C 15.5 255, 13 262, 13 268 C 13 282, 24 293, 38 293 C 52 293, 63 282, 63  268 C 63 262, 60 255, '.$rightM.' 251 L '.$rightM.' '.$newThermLevel.' L '.$leftM.' '.$newThermLevel.'" style="stroke-width: 0;" fill="url(#'.$gradID.')"/>';
                }

                if ($i == $raisedN & $raisedTotal <= $targetTotal){
                    echo '<path d="M'.$leftM.' '.$newThermLevel.' L '.$rightM.' '.$newThermLevel.'" class="therm_raisedLevel" />';
                }
            }
            else{
                ##$fill = ($i > count($colorListA)-1) ? $thermProperties['fill'] : trim($colorListA[$i]); // if not enough colours in list -> transparent
                $fill = ($i > count($colorListA)-1) ? 'url(#'.$gradID.')' : trim($colorListA[$i]); // if not enough colours in list -> transparent
                $newThermLevel = ($raisedTotal > $targetTotal) ? $oldThermLevel - (($minH - $maxH) * ($r/$raisedTotal)) : $oldThermLevel - (($minH - $maxH) * ($r/$targetTotal));
                if ($raisedTotal > $targetTotal & $i == $raisedN){
                    echo '<path d="M '.$leftM.' '.$newThermLevel.' L '.$leftM.' '.$oldThermLevel.' L '.$rightM.' '.$oldThermLevel.' L '.$rightM.' '.$newThermLevel.' C '.$rightM.' 30, 48 25.5, 38 25.5 C 28 25.5, 20 30, '.$leftM.' '.$newThermLevel.'" fill="'.esc_html($fill).'" style="stroke-width: 0;" />';
                }
                else{
                    echo '<rect x="'.$leftM.'" y="'.$newThermLevel.'" width="'.($rightM-$leftM).'" height="'.($oldThermLevel-$newThermLevel).'" fill="'.esc_html($fill).'" style="stroke-width: 0;"/>';
                }


                echo '<path d="M '.$leftM.' '.$oldThermLevel.' L '.$rightM.' '.$oldThermLevel.'" class="therm_subRaisedLevel"/>';

                if ($i == $raisedN & $raisedTotal <= $targetTotal){
                    echo '<path d="M '.$leftM.' '.$newThermLevel.' L '.$rightM.' '.$newThermLevel.'" class="therm_subRaisedLevel"/>';
                }
            }
            $i++;
            $oldThermLevel = $newThermLevel;
        }
    }

    // raised value & ticks
    if ( !empty($raisedValue) && $showRaised == 1 ){
        $rValue = ($thermProperties['swapValues'] == 0) ? $raisedValue : $targetValue;
        $rValueLevel = ($thermProperties['swapValues'] == 0) ? $newThermLevel : $minH - (($minH - $maxH) * ($targetTotal/$raisedTotal));
        if($thermProperties['orientation'] == 'landscape'){
            if ( $tickM == $rightM ){
                echo '<path d="M '.$rValueLevel.' '.$markerMargin.', '.($rValueLevel-$markerSize).' '.($markerMargin+$markerSize).', '.($rValueLevel+$markerSize).' '.($markerMargin+$markerSize).' Z" class="therm_arrow"/>';
            }
            elseif ($tickM == $leftM){
                echo '<path d="M '.$rValueLevel.' '.$markerMargin.', '.($rValueLevel+$markerSize).' '.($markerMargin-$markerSize).', '.($rValueLevel-$markerSize).' '.($markerMargin-$markerSize).' Z" class="therm_arrow" />';
            }

            echo '<text x="'.$rValueLevel.'" y="'.($raisedMargin).'" class="therm_raised" text-anchor="middle" dominant-baseline="central" fill="'.esc_html($raisedColor).'">'.esc_html($rValue).'</text>';
            if ($thermProperties['swapValues'] == 1){
                echo '<path d="M'.$rValueLevel.' '.$leftM.' L '.$rValueLevel.' '.$rightM.'" class="therm_subTargetLevel"/>';
            }
        }

        else{
            if ( $tickM == $rightM ){
                echo '<path d="M '.$markerMargin.' '.$rValueLevel.', '.($markerMargin+$markerSize).' '.($rValueLevel-$markerSize).', '.($markerMargin+$markerSize).' '.($rValueLevel+$markerSize).' Z" class="therm_arrow"/>';
            }
            elseif ($tickM == $leftM){
                echo '<path d="M '.$markerMargin.' '.$rValueLevel.', '.($markerMargin-$markerSize).' '.($rValueLevel+$markerSize).', '.($markerMargin-$markerSize).' '.($rValueLevel-$markerSize).' Z" class="therm_arrow" />';
            }
            if ($thermProperties['ticks'] == 'right'){
                echo '<text x="'.$raisedMargin.'" y="'.$rValueLevel.'" class="therm_raised" text-anchor="start" dominant-baseline="central" fill="'.esc_html($raisedColor).'">'.esc_html($rValue).'</text>';
            }
            else{
                echo '<text x="'.$raisedMargin.'" y="'.$rValueLevel.'" class="therm_raised" text-anchor="end" dominant-baseline="central" fill="'.esc_html($raisedColor).'">'.esc_html($rValue).'</text>';
            }
            if ($thermProperties['swapValues'] == 1){
                echo '<path d="M'.$leftM.' '.$rValueLevel.' L '.$rightM.' '.$rValueLevel.'" class="therm_subTargetLevel"/>';
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
                $targetLevel = $minH - (($minH - $maxH) * ($t/0.01));
            }
            if ($thermProperties['orientation'] == 'portrait'){ // horizontal markers
                echo '<path d="M'.$leftM.' '.$targetLevel.' L '.$rightM.' '.$targetLevel.'" class="therm_subTargetLevel"/>';
            }
            else{
                echo '<path d="M'.$targetLevel.' '.$leftM.' L '.$targetLevel.' '.$rightM.'" class="therm_subTargetLevel"/>';
            }
            if ($raisedTotal <= $t*0.9 or $raisedTotal >= $t*1.1 or $showRaised == 0){ // within 10% but only when not reached the subtotal
                if ($showSubTargets == 1){
                    $t = ($thermProperties['trailing'] == 'true') ? esc_html(number_format($t,$decimals,$thermProperties['decsep'],$thermProperties['sep']).$currency) : esc_html($currency.number_format($t,$decimals,$thermProperties['decsep'],$thermProperties['sep']));
                    if ($thermProperties['orientation'] == 'portrait'){
                        if ( $tickM == $rightM ){
                            echo '<path d="M '.$markerMargin.' '.$targetLevel.', '.($markerMargin+$markerSize).' '.($targetLevel-$markerSize).', '.($markerMargin+$markerSize).' '.($targetLevel+$markerSize).' Z" class="therm_subTargetArrow"/>';
                        }
                        elseif ($tickM == $leftM){
                            echo '<path d="M '.$markerMargin.' '.$targetLevel.', '.($markerMargin-$markerSize).' '.($targetLevel+$markerSize).', '.($markerMargin-$markerSize).' '.($targetLevel-$markerSize).' Z" class="therm_subTargetArrow" />';
                        }

                        echo '<text x="'.$raisedMargin.'" y="'.$targetLevel.'" fill="'.$subTargetColor.'" class="therm_subTarget" text-anchor="'.$raisedAnchor.'" dominant-baseline="central">'.$t.'</text>';
                    }
                    elseif($thermProperties['orientation'] == 'landscape'){
                        if ( $tickM == $rightM ){
                            echo '<path d="M '.$targetLevel.' '.$subMarkerMargin.', '.($targetLevel+$markerSize).' '.($subMarkerMargin-$markerSize).', '.($targetLevel-$markerSize).' '.($subMarkerMargin-$markerSize).' Z" class="therm_subTargetArrow"/>';
                        }
                        elseif ($tickM == $leftM){
                            echo '<path d="M '.$targetLevel.' '.$subMarkerMargin.', '.($targetLevel-$markerSize).' '.($subMarkerMargin+$markerSize).', '.($targetLevel+$markerSize).' '.($subMarkerMargin+$markerSize).' Z" class="therm_subTargetArrow" />';
                        }

                        echo '<text x="'.$targetLevel.'" y="'.$subTargetMargin.'" fill="'.$subTargetColor.'" class="therm_subTarget" text-anchor="middle" dominant-baseline="central">'.$t.'</text>';
                    }
                }
            }
        }
    }


    if($thermProperties['orientation'] == 'landscape'){
        //major
        echo '<path d="M '.$maxH.' '.$tickM.' L '.$maxH.' '.$majorTickL.' M  '.($maxH-($tickStep*1)).' '.$tickM.' L '.($maxH-($tickStep*1)).' '.$majorTickL.' M '.($maxH-($tickStep*2)).' '.$tickM.' L '.($maxH-($tickStep*2)).' '.$majorTickL.' M'.($maxH-($tickStep*3)).' '.$tickM.' L '.($maxH-($tickStep*3)).' '.$majorTickL.' M '.($maxH-($tickStep*4)).' '.$tickM.' L '.($maxH-($tickStep*4)).' '.$majorTickL.' M '.$minH.' '.$tickM.' L '.$minH.' '.$majorTickL.'" class="therm_majorTick"/>';
        //minor
        echo '<path d="M '.($maxH-$tickStep*0.5).' '.$tickM.' L '.($maxH-$tickStep*0.5).' '.$minorTickL.' M '.($maxH-$tickStep*1.5).' '.$tickM.' L '.($maxH-$tickStep*1.5).' '.$minorTickL.' M '.($maxH-$tickStep*2.5).' '.$tickM.' L '.($maxH-$tickStep*2.5).' '.$minorTickL.' M '.($maxH-$tickStep*3.5).' '.$tickM.' L '.($maxH-$tickStep*3.5).' '.$minorTickL.' M '.($maxH-$tickStep*4.5).' '.$tickM.' L '.($maxH-$tickStep*4.5).' '.$minorTickL.'" class="therm_minorTick"/>';
    }
    else{
        //major ticks
        echo '<path d="M '.$tickM.' '.$maxH.' L '.$majorTickL.' '.$maxH.' M '.$tickM.' '.($maxH+$tickStep).' L '.$majorTickL.' '.($maxH+$tickStep).' M'.$tickM.' '.($maxH+($tickStep*2)).' L '.$majorTickL.' '.($maxH+($tickStep*2)).' M '.$tickM.' '.($maxH+($tickStep*3)).' L '.$majorTickL.' '.($maxH+($tickStep*3)).' M '.$tickM.' '.($maxH+($tickStep*4)).' L '.$majorTickL.' '.($maxH+($tickStep*4)).' M '.$tickM.' '.$minH.' L '.$majorTickL.' '.$minH.'" class="therm_majorTick"/>';

        //minor ticks
        echo '<path d="M '.$tickM.' '.($maxH+$tickStep*0.5).' L '.$minorTickL.' '.($maxH+$tickStep*0.5).' M '.$tickM.' '.($maxH+$tickStep*1.5).' L '.$minorTickL.' '.($maxH+$tickStep*1.5).' M '.$tickM.' '.($maxH+$tickStep*2.5).' L '.$minorTickL.' '.($maxH+$tickStep*2.5).' M '.$tickM.' '.($maxH+$tickStep*3.5).' L '.$minorTickL.' '.($maxH+$tickStep*3.5).' M '.$tickM.' '.($maxH+$tickStep*4.5).' L '.$minorTickL.' '.($maxH+$tickStep*4.5).'" class="therm_minorTick" />';
    }

    // outline overlay	// title needs to be a child element to display as tooltip
    if($thermProperties['orientation'] == 'landscape'){
        echo '<path d="M 280 41.5 C 280 51.5 275.5 59.5 269.5 59.5 L 54.5 59.5 C 50.5 64 43.5 66.5 37.5 66.5 C 23.5 66.5 12.5 55.5 12.5 41.5 C 12.5 27.5 23.5 16.5 37.5 16.5 C 43.5 16.5 50.5 19.5 54.5 23.5 L 269.5 23.5 C 275.5 23.5 280 31.5 280 41.5" class="therm_border"><title>'.esc_html($title).'</title></path>';
    }
    else{
        echo '<path d="M38 25.5 C 28 25.5, 20 30, '.$leftM.' '.$maxH.' L '.$leftM.' 251 C 15.5 255, 13 262, 13 268 C 13 282, 24 293, 38 293 C 52 293, 63 282, 63 268 C 63 262, 60 255, '.$rightM.' 251 L '.$rightM.' '.$maxH.' C '.$rightM.' 30, 48 25.5, 38 25.5" class="therm_border"><title>'.esc_html($title).'</title></path>';
    }

    //
    /*if ($thermProperties['shadow'] == 1){
        echo '<path d="M '.($leftM+5).' '.($maxH+2).' L '.($leftM+5).' 253 C 20.5 257, 18 264, 18 268 C 18 282, 29 288, 38 288 C 47 288, 50 285, 53 282" style="stroke-width: 6px; stroke: #ffffffad; fill:transparent;" filter="url(#blurFilter)"/>';
        echo '<path d="M '.($leftM+5).' '.($maxH+2).' L '.($leftM+5).' 253 C 20.5 257, 18 264, 18 268 C 18 282, 29 288, 38 288 C 47 288, 50 285, 53 282" style="stroke-width: 1.5px; stroke: #f6eaea30; fill:transparent;" filter="url(#blurFilter2)"/>';
    }*/


    // percentage
    if ($thermProperties['showPercent'] == 1){
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

        if($thermProperties['orientation'] == 'landscape'){
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

        if($thermProperties['orientation'] == 'landscape'){
            $legendLevel = 10;
            echo '<text class="therm_legend" x="'.($legendLevel-10).'" y="'.max(0,(41.5-((($legendStep+6)*count($legendAr))/2))).'" text-anchor="end" dominant-baseline="central">';
        }
        else{
            $legendLevel = 295;
            echo '<text class="therm_legend" x="'.($viewboxX1+4).'" y="'.$legendLevel.'" dominant-baseline="baseline" text-anchor="left">';
        }
        foreach($raisedAr as $r){
            if($i > $i2){
                $i--;
                continue;
            }
            $legendColor = (array_key_exists($i, $colorListA)) ? trim($colorListA[$i]) : 'black';
            if($thermProperties['orientation'] == 'landscape'){
                echo '<tspan x="'.($legendLevel-10).'" dy="'.$legendStep.'" fill="'.$legendColor.'" text-anchor="end" alignment-baseline="central">'.esc_html($legendAr[$j]);
            }
            else{
                echo '<tspan x="'.($viewboxX1+4).'" dy="'.$legendStep.'" fill="'.$legendColor.'" text-anchor="left" alignment-baseline="central">'.esc_html($legendAr[$j]);
            }
            if (count($raisedA) >= 1){
                echo ($thermProperties['trailing'] == 'true') ? esc_html(' ('.trim(number_format($r,$decimals,$thermProperties['decsep'],$thermProperties['sep'])).$currency.')') : esc_html(' ('.$currency.trim(number_format($r,$decimals,$thermProperties['decsep'],$thermProperties['sep']))).')</tspan>';
            }

            $i--;
            $j++;
        }
        echo '</text>';
    }

    echo '</svg></div>';

    return ob_get_clean();
}
