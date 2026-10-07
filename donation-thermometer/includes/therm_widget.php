<?php

if(self::get_thermometer_widget_option('thousands') == '(space)'){
    $sep = ' ';
}
elseif(self::get_thermometer_widget_option('thousands') == '(none)'){
    $sep = '';
}
else{
    $sep = substr(self::get_thermometer_widget_option('thousands'),0,1);
}
$decsep = (self::get_thermometer_widget_option('decsep') == ', (comma)') ? ',' : '.';
$decimals = intval(self::get_thermometer_widget_option('decimals'));

$raised_str = (string)(self::get_thermometer_widget_option('raised_string') ?? '');
$raisedA = explode(';', $raised_str);

if ($decsep == ','){
    foreach($raisedA as &$item) {
        $item = floatval(str_replace(',', '.', str_replace('.', '', strval($item))));
    }
    unset($item);
} else {
    $raisedA = array_map('floatval', $raisedA);
}

$raised = array_sum($raisedA);

$target_str = (string)(self::get_thermometer_widget_option('target_string') ?? '');
$targetA = explode(';', $target_str);

if ($decsep == ','){
    foreach($targetA as &$item) {
        $item = floatval(str_replace(',', '.', str_replace('.', '', strval($item))));
    }
    unset($item); 
} else {
    $targetA = array_map('floatval', $targetA);
}

$target = floatval(end($targetA));

$currency = self::get_thermometer_widget_option('currency');
$trailing = self::get_thermometer_widget_option('trailing');
$target_color = sanitize_hex_color( self::get_thermometer_widget_option('colour_picker3') ) ?: '#000000';
$raised_color = sanitize_hex_color( self::get_thermometer_widget_option('colour_picker4') ) ?: '#000000';
$percent_color = sanitize_hex_color( self::get_thermometer_widget_option('colour_picker2') ) ?: '#000000';

echo '<p>'.__('Target amount', 'donation-thermometer').': <b><span style="color: '.esc_attr( $target_color ).';" title="'.esc_attr( __('Target text color on thermometers', 'donation-thermometer').' = '.$target_color ).'">';

if($trailing == 'false'){

    echo esc_html($currency.number_format($target,$decimals,$decsep,$sep));
}
else{
    echo esc_html(number_format($target,$decimals,$decsep,$sep).$currency);
}

echo '</b><span style="padding-left: 40px;" title="'.esc_attr__('Use this shortcode to insert the target value in posts/pages', 'donation-thermometer').'">'.__('Shortcode', 'donation-thermometer').': <code style="font-family: monospace;">[therm_t]</code></span></p></p>';

echo '<p>'.__('Total raised', 'donation-thermometer').': <b><span style="color: '.esc_attr( $raised_color ).';" title="'.esc_attr( __('Raised text color on thermometers', 'donation-thermometer').' = '.$raised_color ).'">';

if($trailing == 'false'){
    echo esc_html($currency.number_format($raised,$decimals,$decsep,$sep));
}
else{
    echo esc_html(number_format($raised,$decimals,$decsep,$sep).$currency);
}
echo '</span></b>';
echo '<span style="padding-left: 40px;" title="'.esc_attr__('Use this shortcode to insert the raised value in posts/pages', 'donation-thermometer').'">'.__('Shortcode', 'donation-thermometer').': <code style="font-family: monospace;">[therm_r]</code></span></p>';

echo '<p>'.__('Percent raised', 'donation-thermometer').': <b><span style="color: '.esc_attr( $percent_color ).';" title="'.esc_attr( __('Percent raised text color on thermometers', 'donation-thermometer').' = '.$percent_color ).'">';

echo ($target > 0) ? esc_html(number_format(($raised/$target * 100),$decimals,$decsep,$sep)).'%' : 'unknown%';

echo '</span></b>';
echo '<span style="padding-left: 40px;" title="'.esc_attr__('Use this shortcode to insert the percent raised value in posts/pages', 'donation-thermometer').'">'.__('Shortcode', 'donation-thermometer').': <code style="font-family: monospace;">[therm_%]</code></span></p>';

echo '<p style="font-style: italic;font-size: 9pt;">'.sprintf(__('To change these global values, hover over the widget title and click on the "Configure" link, or visit the %1$s plugin settings%2$s page.','donation-thermometer'),'<a href="options-general.php?page=thermometer-settings.php&tab=settings">','</a>').'</p>';
