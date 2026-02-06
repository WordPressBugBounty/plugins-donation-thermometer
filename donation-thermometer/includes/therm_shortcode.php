<?php
/////////////////////////////// shortcode stuff...

function is_hex_color($color) {
    $color = trim($color);
    // Regex: ^# followed by exactly 3 or 6 hex digits
    return (bool) preg_match('/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $color);
}
function thermometer_graphic($atts){
    $atts = (shortcode_atts(
        array(
            'width' => '',
            'height' => '',
            'align' => '',
            'target' => '',
            'raised' => '',
            'alt' => '',
            'currency' => '',
            'sep' => '',
            'decsep' => '',
            'decimals' => '',
            'trailing' =>'',
            'fill' => '',
            'filltype' => '',
            'fill2' => '',
            'colorramp' => '',
            'legend' => '',
            'ticks' => '',
            'targetlabels' => '',
            'percentcolor' => '',
            'targetcolor' => '',
            'raisedcolor' => '',
            'subtargetcolor' => '',
            'shadow' => '',
            'orientation' => '',
            'showpercent' => '',
            'showtarget' => '',
            'showraised' => '',
            'swapvalues' => ''
        ), $atts));

    global $thermDefaults;
    $options = wp_parse_args( get_option('thermometer_options',$thermDefaults), $thermDefaults);
    $thermProperties = array();
    //thermometer alignment (vertical/horizontal)
    if(!empty($atts['orientation'])){
        $thermProperties['orientation'] = esc_attr($atts['orientation']);
    }
    else{
        $thermProperties['orientation'] = esc_attr($options['therm_orientation']);
    }

    //width
    //width value
    if($atts['width'] != '' && $atts['height'] != ''){
        return '<p style="color:red;">Use only width OR height parameter values.</p>';
    }

    if (!empty($atts['width'])){
        $thermProperties['width'] = esc_attr($atts['width']);
        $thermProperties['height'] = '';
    }
    else{
        $thermProperties['width'] = ($thermProperties['orientation'] == 'portrait') ? "200" : "501.5";
        $thermProperties['height'] = '';
    }

    //height
    if (!empty($atts['height'])){
        $thermProperties['height'] = esc_attr($atts['height']);
        $thermProperties['width'] = '';
    }
    elseif(empty($atts['height']) && !empty($atts['width'])){
        $thermProperties['width'] = esc_attr($atts['width']);
        $thermProperties['height'] = '';
    }
    else{
        $thermProperties['height'] = ($thermProperties['orientation'] == 'portrait') ? "533" : "148";
        $thermProperties['width'] = '';
    }
    //currency value to use
    if (empty($atts['currency'])){
        $thermProperties['currency'] = esc_attr($options['currency']);
    }
    elseif(strtolower($atts['currency']) == 'null'){ //get user to enter null for no value
        $thermProperties['currency'] = '';
    }
    else{
        $thermProperties['currency'] = esc_attr($atts['currency']); //set currency to default or shortcode value
    }

    //decimal separator
    if(!empty($atts['decsep'])){
        $thermProperties['decsep'] = esc_attr($atts['decsep']);
    }
    else{
        $thermProperties['decsep'] = ($options['decsep'] == ', (comma)') ? ',' : '.';
    }
    $sep = $thermProperties['decsep'];

    //target value
    if ($atts['target'] == '' && !empty($options['target_string'])){
        $thermProperties['target'] = esc_attr($options['target_string']);
    }
    elseif($atts['target'] == 'off'){
        $thermProperties['target'] = esc_attr($options['target_string']).';'.strval(esc_attr($atts['target']));
    }
    else{
        // if shortcode present
        if (!is_numeric(str_replace(",", ".", $atts['target'])) && (strpos($atts['target'], ';') === false) && !is_numeric(str_replace(',','',$atts['target']))) {
            $shortcode = "[".strval($atts['target'])."]";
            $atts['target'] = do_shortcode( $shortcode);
        }
        $thermProperties['target'] = preg_replace('/[^A-Za-z0-9\-\\'.$sep.'\;]/', '',strval(esc_attr($atts['target'])));
    }

    //sub target labels
    if (!empty($atts['targetlabels'])){
        $thermProperties['targetlabels'] = ($atts['targetlabels'] == 'off') ? 0 : 1;
    }
    else{
        $thermProperties['targetlabels'] = ($options['targetlabels'] == 'true') ? 1 : 0;
    }

    //thermometer shadow
    if (!empty($atts['shadow'])){
        $thermProperties['shadow'] = (strtolower($atts['shadow']) == 'false') ? 0 : 1;
    }
    else{
        $thermProperties['shadow'] = ($options['therm_shadow'] == 'true') ? 1 : 0;
    }

    //raised value
    if ($atts['raised'] == '' && !empty($options['raised_string'])){
        $thermProperties['raised'] = esc_attr($options['raised_string']);
    }
    else{
        // if shortcode present
        if (!is_numeric(str_replace(",", ".", $atts['raised'])) && (strpos($atts['raised'], ';') === false) && !is_numeric(str_replace(',','',$atts['raised']))) {
            $shortcode = "[".strval($atts['raised'])."]";
            $atts['raised'] = do_shortcode( $shortcode);
        }
        $thermProperties['raised'] = preg_replace('/[^A-Za-z0-9\-\\'.$sep.'\;]/', '',strval(esc_attr($atts['raised'])));
    }

    //align position
    if (strtolower($atts['align']) == 'center' || strtolower($atts['align']) == 'centre'){
        $thermProperties['align'] = 'display:block; margin-left:auto; margin-right:auto;';
    }
    elseif (strtolower($atts['align']) == 'left'){
        $thermProperties['align'] = 'display:block; float:left;';
    }
    elseif (!empty($atts['align'])){
        $thermProperties['align'] = 'display:block; float:'.strtolower(esc_attr($atts['align'])).';';
    }
    else{
        $thermProperties['align'] = 'display:block; float:left;';
    }


    //thousands separator
    if(!empty($atts['sep'])){
        $thermProperties['sep'] = esc_attr($atts['sep']);
    }
    else{
        if($options['thousands'] == ' (space)'){
            $thermProperties['sep'] = ' ';
        }
        elseif($options['thousands'] == '(none)'){
            $thermProperties['sep'] = '';
        }
        else{
            $thermProperties['sep'] = substr(esc_attr($options['thousands']),0,1);
        }
    }

    //decimal places
    if(is_numeric($atts['decimals'])){
        $thermProperties['decimals'] = esc_attr($atts['decimals']);
    }
    else{
        $thermProperties['decimals'] = esc_attr($options['decimals']);
    }

    // fill colour and gradient
    if (!empty($atts['fill']) and is_hex_color($atts['fill'])){
        $thermProperties['fill'] = esc_attr($atts['fill']);
    }
    else{
        $thermProperties['fill'] = esc_attr($options['colour_picker1']);
    }

    // create a gradient
    if(!empty($atts['filltype'])){
        if ($atts['filltype'] == 'gradient'){
            if(!empty($atts['fill2']) and is_hex_color($atts['fill2'])){
                $thermProperties['fill2'] = esc_attr($atts['fill2']);
            }
            else{
                $thermProperties['fill2'] = esc_attr($options['colour_picker6']);
            }
        }
        else{
            $thermProperties['fill2'] = esc_attr($thermProperties['fill']);
        }
    }
    else{
        if ($options['therm_filltype'] == 'gradient'){
            if(!empty($atts['fill2']) and is_hex_color($atts['fill2'])){
                $thermProperties['fill2'] = esc_attr($atts['fill2']);
            }
            else{
                $thermProperties['fill2'] = esc_attr($options['colour_picker6']);
            }
        }
        else{
            $thermProperties['fill2'] = esc_attr($thermProperties['fill']);
        }
    }

    // currency before or after number
    if(strtolower($atts['trailing']) == 'true'){
        $thermProperties['trailing'] = 'true';
    }
    elseif(strtolower($atts['trailing']) == 'false'){
        $thermProperties['trailing'] = 'false';
    }
    elseif(isset($options['trailing']) && ($options['trailing'] == "on" or $options['trailing'] == "true")){
        $thermProperties['trailing'] = 'true';
    }
    else{
        $thermProperties['trailing'] = 'false';
    }

    //title text
    if (!empty($atts['alt'])){
        $thermProperties['title'] = esc_attr($atts['alt']);
    }
    else{
        $thermProperties['title'] = '';
    }

    //legend
    if(!empty($atts['legend'])){
        $thermProperties['legend'] = esc_attr($atts['legend']);
    }
    else{
        $thermProperties['legend'] = '';
    }

    //tick alignment
    if(!empty($atts['ticks'])){
        $thermProperties['ticks'] = esc_attr($atts['ticks']);
    }
    else{
        $thermProperties['ticks'] = esc_attr($options['tick_align']);
    }

    // color ramp
    if(!empty($atts['colorramp'])){
        $thermProperties['colorList'] = esc_attr($atts['colorramp']);
    }
    else{
        $thermProperties['colorList'] = esc_attr($options['color_ramp']);
    }

    // show percentage
    if(!empty($atts['showpercent'])){
        $thermProperties['showPercent'] = ($atts['showpercent'] == 0 or strtolower($atts['showpercent']) == 'false') ? 0 : 1;
    }
    else{
        $thermProperties['showPercent'] = ($options['chkbox1'] == 1 or $options['chkbox1'] == 'true') ? 1 : 0;
    }

    // show target value
    if(!empty($atts['showtarget'])){
        $thermProperties['showTarget'] = ($atts['showtarget'] == 0 or strtolower($atts['showtarget']) == 'false') ? 0 : 1;
    }
    else{
        $thermProperties['showTarget'] = ($options['chkbox2'] == 1 or $options['chkbox2'] == 'true') ? 1 : 0;
    }

    // show raised value
    if(!empty($atts['showraised'])){
        $thermProperties['showRaised'] = ($atts['showraised'] == 0 or strtolower($atts['showraised']) == 'false') ? 0 : 1;
    }
    else{
        $thermProperties['showRaised'] = ($options['chkbox3'] == 1 or $options['chkbox3'] == 'true') ? 1 : 0;
    }

    // swap raised/target values
    if(!empty($atts['swapvalues'])){
        $thermProperties['swapValues'] = ($atts['swapvalues'] == 1 or strtolower($atts['swapvalues']) == 'true') ? 1 : 0;
    }
    else{
        $thermProperties['swapValues'] = ($options['swapValues'] == 1 or $options['swapValues'] == 'true') ? 1 : 0;
    }

    $thermProperties['percentageColor'] = (empty($atts['percentcolor'])) ? esc_attr($options['colour_picker2']) : esc_attr($atts['percentcolor']);
    $thermProperties['targetColor'] = (empty($atts['targetcolor'])) ? esc_attr($options['colour_picker3']) : esc_attr($atts['targetcolor']);
    $thermProperties['raisedColor'] = (empty($atts['raisedcolor'])) ? esc_attr($options['colour_picker4']) : esc_attr($atts['raisedcolor']);
    $thermProperties['subtargetColor'] = (empty($atts['subtargetcolor'])) ? esc_attr($options['colour_picker5']) : esc_attr($atts['subtargetcolor']);
    //print_r($thermProperties);
    //print_r($atts);
    // create a custom thermometer from shortcode parameters
    return thermhtml($thermProperties);
}

add_shortcode( 'thermometer','thermometer_graphic');

/*
// Additional shortcodes
*/

add_shortcode( 'therm_r','therm_raised');

function therm_raised(){
    global $thermDefaults;
    $options = wp_parse_args( get_option('thermometer_options',$thermDefaults), $thermDefaults);
    $raisedA = explode(';',esc_attr($options['raised_string']));

    if($options['thousands'] == ' (space)'){
        $sep = ' ';
    }
    elseif($options['thousands'] == '(none)'){
        $sep = '';
    }
    else{
        $sep = substr(esc_attr($options['thousands']),0,1);
    }
    $decsep = ($options['decsep'] == ', (comma)') ? ',' : '.';
    $decimals = esc_attr($options['decimals']);

    if (end($raisedA) == 'off'){
        array_splice($raisedA,-1);
    }

    if ($decsep == ','){
        foreach($raisedA as &$item) {
            $item = floatval(str_replace(',', '.', str_replace('.', '', strval($item))));
        }
    }
    else{
        foreach($raisedA as &$item) {
            $item = floatval(str_replace(',', '', strval($item)));
        }
    }
    $raised = array_sum($raisedA);

    if ($raised != ''){
        return number_format($raised, $decimals,$decsep,$sep);
    }
    else{
        return '<b>['.__('Value missing on settings page','donation-thermometer').']</b>';
    }
}

add_shortcode( 'therm_t','therm_target');

function therm_target(){
    global $thermDefaults;
    $options = wp_parse_args( get_option('thermometer_options',$thermDefaults), $thermDefaults);
    $target = esc_attr($options['target_string']);
    if($options['thousands'] == ' (space)'){
        $sep = ' ';
    }
    elseif($options['thousands'] == '(none)'){
        $sep = '';
    }
    else{
        $sep = substr(esc_attr($options['thousands']),0,1);
    }
    $decsep = ($options['decsep'] == ', (comma)') ? ',' : '.';
    $decimals = esc_attr($options['decimals']);
    if ($target != ''){
        $targetA = explode(';',$target);
        if (end($targetA) == 'off'){
            array_splice($targetA,-1);
        }
        if ($decsep == ','){
            $target = floatval(str_replace(',', '.', str_replace('.', '', strval(end($targetA)))));
        }

        else{
            $target = floatval(str_replace(',', '', strval(end($targetA))));
        }

        #$target = end($targetA);
        return number_format($target, $decimals,$decsep,$sep);
    }
    else{
        return '<b>['.__('Value missing on settings page','donation-thermometer').']</b>';
    }
}


add_shortcode( 'therm_%','therm_percent');

function therm_percent(){
    global $thermDefaults;
    $target = therm_target();
    $raised = therm_raised();
    $options = wp_parse_args( get_option('thermometer_options',$thermDefaults), $thermDefaults);
    $decimals = esc_attr($options['decimals']);
    $div = (float) str_replace(',', '', $raised) / (float) str_replace(',', '', $target);
    return ($target > 0) ? number_format(($div * 100),$decimals).'%' : __('unknown %','donation-thermometer');
}
