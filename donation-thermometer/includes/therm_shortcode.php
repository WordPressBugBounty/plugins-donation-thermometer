<?php
defined('ABSPATH') or die('Access denied');

/////////////////////////////// shortcode stuff...

function is_hex_color($color) {
    $color = trim($color);
    // Regex: ^# followed by exactly 3 or 6 hex digits
    return (bool) preg_match('/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $color);
}
function thermometer_graphic($atts){
    $thermDefaults = get_donation_thermometer_defaults();
    $options = wp_parse_args( get_option('thermometer_options',$thermDefaults), $thermDefaults);

    $atts = shortcode_atts(
        array(
            'width'          => '',
            'height'         => '',
            'align'          => 'left',
            'target'         => $options['target_string'],
            'raised'         => $options['raised_string'],
            'alt'            => '',
            'currency'       => $options['currency'],
            'sep'            => '',
            'decsep'         => ($options['decsep'] === ', (comma)') ? ',' : '.',
            'decimals'       => $options['decimals'],
            'trailing'       => $options['trailing'],
            'fill'           => $options['colour_picker1'],
            'filltype'       => $options['therm_filltype'],
            'fill2'          => $options['colour_picker6'],
            'colorramp'      => $options['color_ramp'],
            'legend'         => '',
            'ticks'          => $options['tick_align'],
            'targetlabels'   => ($options['targetlabels'] === 'true') ? '1' : '0',
            'shadow'         => ($options['therm_shadow'] === 'true') ? '1' : '0',
            'orientation'    => $options['therm_orientation'],
            'showpercent'    => ($options['chkbox1'] === '1' || $options['chkbox1'] === 'true') ? '1' : '0',
            'showtarget'     => ($options['chkbox2'] === '1' || $options['chkbox2'] === 'true') ? '1' : '0',
            'showraised'     => ($options['chkbox3'] === '1' || $options['chkbox3'] === 'true') ? '1' : '0',
            'swapvalues'     => ($options['swapValues'] === '1' || $options['swapValues'] === 'true') ? '1' : '0',
            'percentcolor'   => $options['colour_picker2'],
            'targetcolor'    => $options['colour_picker3'],
            'raisedcolor'    => $options['colour_picker4'],
            'subtargetcolor' => $options['colour_picker5'],
        ),
        $atts,
        'thermometer'
    );
    
    $thermProperties = array();

    //thermometer alignment (vertical/horizontal)
    $thermProperties['orientation'] = sanitize_key($atts['orientation']);

    // Orientation
    $thermProperties['orientation'] = sanitize_key($atts['orientation']);

    // Handle Dimensional Sizing Bounds
    if ($atts['width'] !== '' && $atts['height'] !== '') {
        return '<p style="color:red;">' . __('Use only width OR height parameter values.', 'donation-thermometer') . '</p>';
    }

    if (!empty($atts['width'])) {
        $thermProperties['width']  = sanitize_text_field($atts['width']);
        $thermProperties['height'] = '';
    } elseif (!empty($atts['height'])) {
        $thermProperties['height'] = sanitize_text_field($atts['height']);
        $thermProperties['width']  = '';
    } else {
        $thermProperties['width']  = ($thermProperties['orientation'] === 'portrait') ? "200" : "501.5";
        $thermProperties['height'] = '';
    }

    // Currency Formatting Contexts
    $thermProperties['currency'] = (strtolower($atts['currency']) === 'null') ? '' : sanitize_text_field($atts['currency']);
    $thermProperties['decsep']   = sanitize_text_field($atts['decsep']);
    $sep                         = $thermProperties['decsep'];

    // Data Targets Evaluation
    $thermProperties['target'] = preg_replace('/[^A-Za-z0-9\-\\' . $sep . '\;]/', '', strval($atts['target']));
    $thermProperties['raised'] = preg_replace('/[^A-Za-z0-9\-\\' . $sep . '\;]/', '', strval($atts['raised']));

    // Operational Flags Checks
    $thermProperties['targetlabels'] = ($atts['targetlabels'] === 'off' || $atts['targetlabels'] === '0') ? 0 : 1;
    $thermProperties['shadow']       = ($atts['shadow'] === 'false' || $atts['shadow'] === '0') ? 0 : 1;
    $thermProperties['showPercent']  = ($atts['showpercent'] === 'false' || $atts['showpercent'] === '0') ? 0 : 1;
    $thermProperties['showTarget']   = ($atts['showtarget'] === 'false' || $atts['showtarget'] === '0') ? 0 : 1;
    $thermProperties['showRaised']   = ($atts['showraised'] === 'false' || $atts['showraised'] === '0') ? 0 : 1;
    $thermProperties['swapValues']   = ($atts['swapvalues'] === 'true' || $atts['swapvalues'] === '1') ? 1 : 0;

    // Element Alignment Layout Rules
    $align = strtolower($atts['align']);
    if ($align === 'center' || $align === 'centre') {
        $thermProperties['align'] = 'display:block; margin-left:auto; margin-right:auto;';
    } else {
        $thermProperties['align'] = 'display:block; float:' . sanitize_key($align) . ';';
    }

    // Thousands Separator Rule
    if (!empty($atts['sep'])) {
        $thermProperties['sep'] = sanitize_text_field($atts['sep']);
    } else {
        if ($options['thousands'] === ' (space)') {
            $thermProperties['sep'] = ' ';
        } elseif ($options['thousands'] === '(none)') {
            $thermProperties['sep'] = '';
        } else {
            $thermProperties['sep'] = substr(sanitize_text_field($options['thousands']), 0, 1);
        }
    }

    $thermProperties['decimals']  = absint($atts['decimals']);
    $thermProperties['title']     = sanitize_text_field($atts['alt']);
    $thermProperties['legend']    = sanitize_text_field($atts['legend']);
    $thermProperties['ticks']     = sanitize_text_field($atts['ticks']);
    $thermProperties['colorList'] = sanitize_text_field($atts['colorramp']);
    $thermProperties['trailing']  = ($atts['trailing'] === 'true' || $atts['trailing'] === 'on') ? 'true' : 'false';

    // Enforce Safe Color Definitions
    $thermProperties['fill']            = is_hex_color($atts['fill']) ? $atts['fill'] : $options['colour_picker1'];
    $thermProperties['fill2']           = ($atts['filltype'] === 'gradient' && is_hex_color($atts['fill2'])) ? $atts['fill2'] : $thermProperties['fill'];
    $thermProperties['percentageColor'] = is_hex_color($atts['percentcolor']) ? $atts['percentcolor'] : $options['colour_picker2'];
    $thermProperties['targetColor']     = is_hex_color($atts['targetcolor']) ? $atts['targetcolor'] : $options['colour_picker3'];
    $thermProperties['raisedColor']     = is_hex_color($atts['raisedcolor']) ? $atts['raisedcolor'] : $options['colour_picker4'];
    $thermProperties['subtargetColor']  = is_hex_color($atts['subtargetcolor']) ? $atts['subtargetcolor'] : $options['colour_picker5'];

    return thermhtml($thermProperties);
}

add_shortcode( 'thermometer','thermometer_graphic');

// Helper utility function to clean numeric strings
function get_clean_thermometer_value($value_string, $decsep) {
    $array = explode(';', $value_string);
    if (end($array) === 'off') {
        array_splice($array, -1);
    }
    
    foreach ($array as &$item) {
        if ($decsep === ',') {
            $item = floatval(str_replace(',', '.', str_replace('.', '', strval($item))));
        } else {
            $item = floatval(str_replace(',', '', strval($item)));
        }
    }
    return ($value_string !== '') ? array_sum($array) : false;
}

/*
// Additional shortcodes
*/

function therm_raised() {
    $thermDefaults = get_donation_thermometer_defaults();
    $options       = wp_parse_args(get_option('thermometer_options', $thermDefaults), $thermDefaults);
    
    $decsep   = ($options['decsep'] === ', (comma)') ? ',' : '.';
    $decimals = absint($options['decimals']);
    $sep      = ($options['thousands'] === ' (space)') ? ' ' : (($options['thousands'] === '(none)') ? '' : substr($options['thousands'], 0, 1));
    
    $raised = get_clean_thermometer_value($options['raised_string'], $decsep);
    
    if ($raised !== false) {
        return number_format($raised, $decimals, $decsep, $sep);
    }
    return '<b>[' . __('Value missing on settings page', 'donation-thermometer') . ']</b>';
}
add_shortcode('therm_r', 'therm_raised');

function therm_target() {
    $thermDefaults = get_donation_thermometer_defaults();
    $options       = wp_parse_args(get_option('thermometer_options', $thermDefaults), $thermDefaults);
    
    $decsep   = ($options['decsep'] === ', (comma)') ? ',' : '.';
    $decimals = absint($options['decimals']);
    $sep      = ($options['thousands'] === ' (space)') ? ' ' : (($options['thousands'] === '(none)') ? '' : substr($options['thousands'], 0, 1));
    
    $target = get_clean_thermometer_value($options['target_string'], $decsep);
    
    if ($target !== false) {
        return number_format($target, $decimals, $decsep, $sep);
    }
    return '<b>[' . __('Value missing on settings page', 'donation-thermometer') . ']</b>';
}
add_shortcode('therm_t', 'therm_target');

function therm_percent() {
    $thermDefaults = get_donation_thermometer_defaults();   
    $options       = wp_parse_args(get_option('thermometer_options', $thermDefaults), $thermDefaults);
    $decsep        = ($options['decsep'] === ', (comma)') ? ',' : '.';
    
    $target = get_clean_thermometer_value($options['target_string'], $decsep);
    $raised = get_clean_thermometer_value($options['raised_string'], $decsep);
    
    $decimals = absint($options['decimals']);
    
    if ($target > 0) {
        $div = $raised / $target;
        return number_format(($div * 100), $decimals) . '%';
    }
    return __('unknown %', 'donation-thermometer');
}
add_shortcode('therm_%', 'therm_percent');
