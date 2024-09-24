<?php
/**
 * This file could be used to catch submitted form data. When using a non-configuration
 * view to save form data, remember to use some kind of identifying field in your form.
 */

if(self::get_thermometer_widget_option('thousands') == ' (space)'){
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
$raisedA = explode(';',self::get_thermometer_widget_option('raised_string'));
if ($decsep == ','){
    foreach($raisedA as &$item) {
        $item = floatval(str_replace(',', '.', str_replace('.', '', strval($item))));
    }
}
$raised = array_sum($raisedA);
$targetA = explode(';',self::get_thermometer_widget_option('target_string'));
if ($decsep == ','){
    foreach($targetA as &$item) {
        $item = floatval(str_replace(',', '.', str_replace('.', '', strval($item))));
    }
}

$target = floatval(end($targetA));
$currency = self::get_thermometer_widget_option('currency');
$trailing = self::get_thermometer_widget_option('trailing');

echo '<p>'.__('Target amount', 'donation-thermometer').': <b><span style="color: '.self::get_thermometer_widget_option('colour_picker3').';" title="'.__('Target text color on thermometers', 'donation-thermometer').' = '.self::get_thermometer_widget_option('colour_picker3').'">';

if($trailing == 'false'){

    echo esc_html($currency.number_format($target,$decimals,$decsep,$sep));
}
else{
    echo esc_html(number_format($target,$decimals,$decsep,$sep).$currency);
}

echo '</b><span style="padding-left: 40px;" title="'.__('Use this shortcode to insert the target value in posts/pages', 'donation-thermometer').'">'.__('Shortcode', 'donation-thermometer').': <code style="font-family: monospace;">[therm_t]</code></span></p></p>';

echo '<p>'.__('Total raised', 'donation-thermometer').': <b><span style="color: '.self::get_thermometer_widget_option('colour_picker4').';" title="'.__('Raised text color on thermometers', 'donation-thermometer').' = '.self::get_thermometer_widget_option('colour_picker4').'">';

if($trailing == 'false'){
    echo esc_html($currency.number_format($raised,$decimals,$decsep,$sep));
}
else{
    echo esc_html(number_format($raised,$decimals,$decsep,$sep).$currency);
}
echo '</span></b>';
echo '<span style="padding-left: 40px;" title="'.__('Use this shortcode to insert the raised value in posts/pages', 'donation-thermometer').'">'.__('Shortcode', 'donation-thermometer').': <code style="font-family: monospace;">[therm_r]</code></span></p>';

echo '<p>'.__('Percent raised', 'donation-thermometer').': <b><span style="color: '.self::get_thermometer_widget_option('colour_picker2').';" title="'.__('Percent raised text color on thermometers', 'donation-thermometer').' = '.self::get_thermometer_widget_option('colour_picker2').'">';

echo ($target > 0) ? esc_html(number_format(($raised/$target * 100),$decimals,$decsep,$sep)).'%' : 'unknown%';

echo '</span></b>';
echo '<span style="padding-left: 40px;" title="'.__('Use this shortcode to insert the percent raised value in posts/pages', 'donation-thermometer').'">'.__('Shortcode', 'donation-thermometer').': <code style="font-family: monospace;">[therm_%]</code></span></p>';

echo '<p style="font-style: italic;font-size: 9pt;">'.sprintf(__('To change these global values, hover over the widget title and click on the "Configure" link, or visit the %1$s plugin settings%2$s page.','donation-thermometer'),'<a href="options-general.php?page=thermometer-settings.php&tab=settings">','</a>').'</p>';
