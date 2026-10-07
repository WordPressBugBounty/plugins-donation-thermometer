<?php
/**
 * This file could be used to catch submitted form data. When using a non-configuration
 * view to save form data, remember to use some kind of identifying field in your form.
 */
$raised = ( isset( $_POST['raised'] ) ) ? sanitize_text_field( wp_unslash( $_POST['raised'] ) ) : self::get_thermometer_widget_option('raised_string');
$target = ( isset( $_POST['target'] ) ) ? sanitize_text_field( wp_unslash( $_POST['target'] ) ) : self::get_thermometer_widget_option('target_string');

$raised = preg_replace( '/[^0-9,.;-]/', '', $raised );
$target = preg_replace( '/[^0-9,.;-]/', '', $target );
self::update_thermometer_widget_options(
    array(
        'raised_string' => $raised,
        'target_string' => $target,
    )
);


echo '<div style="padding: 10px 0px 20px 0;">';
echo __('Target', 'donation-thermometer').': <input type="text" name="target" value="'.esc_attr( self::get_thermometer_widget_option('target_string') ).'" style="width: 200px;"/><br/>';
echo __('Raised', 'donation-thermometer').': <input type="text" name="raised" value="'.esc_attr( self::get_thermometer_widget_option('raised_string') ).'" style="width: 200px;"/><br/>';
echo '<p><i>'.__('NB. For multiple categories within the thermometer, separate values with a semi-colon', 'donation-thermometer').'</i></p>';
echo '</div>';
