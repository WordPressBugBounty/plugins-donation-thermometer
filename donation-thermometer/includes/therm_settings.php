<?php
// Section HTML, displayed before the first option

function style_section_text_fn() {
    echo '<p>'.__('These are the default CSS settings and associated classes for all thermometers on the site.','donation-thermometer').'</p>';
    echo '<p>';
    $fillProperty = '<i>fill</i>';
    $fillExample = 'fill: #32373c;';
    printf(/* translators: 1: <i>fill</i>, 2: fill: #32373c;*/__('N.B. Text colours can be defined using the %1$s property, for example: %2$s','donation-thermometer'), $fillProperty, $fillExample);
    echo '</p>';
}

function svg_style_fn($options) {
    $value = (isset(get_option('thermometer_style')[$options['type']])) ? esc_html(get_option('thermometer_style')[$options['type']]) : $options['default'];
    echo "<div class='form-item'><label for='svgStyle'></label>";
    echo '<textarea id="'.$options['type'].'" style="width: 400px;" name="thermometer_style['.$options['type'].']" placeholder="'.__('CSS styling for the entire thermometer svg','donation-thermometer').'">'.sanitize_text_field($value)."</textarea>";
    echo '<br/>'.__('Default','donation-thermometer').': <code>'.sanitize_text_field($options['default']).'</code></div>';
}

function target_style_fn($options) {
    $value = (isset(get_option('thermometer_style')[$options['type']])) ? esc_html(get_option('thermometer_style')[$options['type']]) : $options['default'];
    echo "<div class='form-item'><label for='targetStyle'></label>";
    echo '<textarea id="'.$options['type'].'" style="width: 400px;" name="thermometer_style['.$options['type'].']">'.sanitize_text_field($value)."</textarea>";
    echo '<br/>'.__('Default','donation-thermometer').': <code>'.sanitize_text_field($options['default']).'</code></div>';
}

function raised_style_fn($options) {
    $value = (isset(get_option('thermometer_style')[$options['type']])) ? esc_html(get_option('thermometer_style')[$options['type']]) : $options['default'];
    echo "<div class='form-item'><label for='raisedStyle'></label>";
    echo '<textarea id="'.$options['type'].'" style="width: 400px;" name="thermometer_style['.$options['type'].']">'.sanitize_text_field($value)."</textarea>";
    echo '<br/>'.__('Default','donation-thermometer').': <code>'.sanitize_text_field($options['default']).'</code></div>';
}
function percent_style_fn($options) {
    $value = (isset(get_option('thermometer_style')[$options['type']])) ? esc_html(get_option('thermometer_style')[$options['type']]) : $options['default'];
    echo "<div class='form-item'><label for='percentStyle'></label>";
    echo '<textarea id="'.$options['type'].'" style="width: 400px;" name="thermometer_style['.$options['type'].']">'.sanitize_text_field($value)."</textarea>";
    echo '<br/>';
    $imptCSS = '<i>!important</i>';
    printf(/* translators: 1: <i>!important</i> */__('N.B. The plugin automatically calculates this font-size depending on the value length. If you want to override this, use the %1$s rule.','donation-thermometer'), $imptCSS);
    echo '<br/>'.__('Default','donation-thermometer').': <code>'.sanitize_text_field($options['default']).'</code></div>';
}
function subTarget_style_fn($options) {
    $value = (isset(get_option('thermometer_style')[$options['type']])) ? esc_html(get_option('thermometer_style')[$options['type']]) : $options['default'];
    echo "<div class='form-item'><label for='subTargetStyle'></label>";
    echo '<textarea id="'.$options['type'].'" style="width: 400px;" name="thermometer_style['.$options['type'].']">'.sanitize_text_field($value)."</textarea>";
    echo '<br/>'.__('Default','donation-thermometer').': <code>'.sanitize_text_field($options['default']).'</code></div>';
}
function legend_style_fn($options) {
    $value = (isset(get_option('thermometer_style')[$options['type']])) ? esc_html(get_option('thermometer_style')[$options['type']]) : $options['default'];
    echo "<div class='form-item'><label for='legendStyle'></label>";
    echo '<textarea id="'.$options['type'].'" style="width: 400px;" name="thermometer_style['.$options['type'].']">'.sanitize_text_field($value)."</textarea>";
    echo '<br/>'.__('Default','donation-thermometer').': <code>'.sanitize_text_field($options['default']).'</code></div>';
}
function border_style_fn($options) {
    $value = (isset(get_option('thermometer_style')[$options['type']])) ? esc_html(get_option('thermometer_style')[$options['type']]) : $options['default'];
    echo "<div class='form-item'><label for='borderStyle'></label>";
    echo '<textarea id="'.$options['type'].'" style="width: 400px;" name="thermometer_style['.$options['type'].']">'.sanitize_text_field($value)."</textarea>";
    echo '<br/>'.__('Default','donation-thermometer').': <code>'.sanitize_text_field($options['default']).'</code></div>';
}
function fill_style_fn($options) {
    $value = (isset(get_option('thermometer_style')[$options['type']])) ? esc_html(get_option('thermometer_style')[$options['type']]) : $options['default'];
    echo "<div class='form-item'><label for='fillStyle'></label>";
    echo '<textarea id="'.$options['type'].'" style="width: 400px;" name="thermometer_style['.$options['type'].']">'.sanitize_text_field($value)."</textarea>";
    echo '<br/>'.__('Default','donation-thermometer').': <code>'.sanitize_text_field($options['default']).'</code></div>';
}

function arrow_style_fn($options) {
    $value = (isset(get_option('thermometer_style')[$options['type']])) ? esc_html(get_option('thermometer_style')[$options['type']]) : $options['default'];
    echo "<div class='form-item'><label for='arrowStyle'></label>";
    echo '<textarea id="'.$options['type'].'" style="width: 400px;" name="thermometer_style['.$options['type'].']">'.sanitize_text_field($value)."</textarea>";
    echo '<br/>'.__('Default','donation-thermometer').': <code>'.sanitize_text_field($options['default']).'</code></div>';
}
function raisedLevel_style_fn($options) {
    $value = (isset(get_option('thermometer_style')[$options['type']])) ? esc_html(get_option('thermometer_style')[$options['type']]) : $options['default'];
    echo "<div class='form-item'><label for='raisedLevelStyle'></label>";
    echo '<textarea id="'.$options['type'].'" style="width: 400px;" name="thermometer_style['.$options['type'].']">'.sanitize_text_field($value)."</textarea>";
    echo '<br/>'.__('Default','donation-thermometer').': <code>'.sanitize_text_field($options['default']).'</code></div>';
}
function subRaisedLevel_style_fn($options) {
    $value = (isset(get_option('thermometer_style')[$options['type']])) ? esc_html(get_option('thermometer_style')[$options['type']]) : $options['default'];
    echo "<div class='form-item'><label for='subRaisedLevelStyle'></label>";
    echo '<textarea id="'.$options['type'].'" style="width: 400px;" name="thermometer_style['.$options['type'].']">'.sanitize_text_field($value)."</textarea>";
    echo '<br/>'.__('Default','donation-thermometer').': <code>'.sanitize_text_field($options['default']).'</code></div>';
}
function subArrow_style_fn($options) {
    $value = (isset(get_option('thermometer_style')[$options['type']])) ? esc_html(get_option('thermometer_style')[$options['type']]) : $options['default'];
    echo "<div class='form-item'><label for='subArrowStyle'></label>";
    echo '<textarea id="'.$options['type'].'" style="width: 400px;" name="thermometer_style['.$options['type'].']">'.sanitize_text_field($value)."</textarea>";
    echo '<br/>'.__('Default','donation-thermometer').': <code>'.sanitize_text_field($options['default']).'</code></div>';
}
function subTargetLevel_style_fn($options) {
    $value = (isset(get_option('thermometer_style')[$options['type']])) ? esc_html(get_option('thermometer_style')[$options['type']]) : $options['default'];
    echo "<div class='form-item'><label for='subTargetLevelStyle'></label>";
    echo '<textarea id="'.$options['type'].'" style="width: 400px;" name="thermometer_style['.$options['type'].']">'.sanitize_text_field($value)."</textarea>";
    echo '<br/>'.__('Default','donation-thermometer').': <code>'.sanitize_text_field($options['default']).'</code></div>';
}
function majorTick_style_fn($options) {
    $value = (isset(get_option('thermometer_style')[$options['type']])) ? esc_html(get_option('thermometer_style')[$options['type']]) : $options['default'];
    echo "<div class='form-item'><label for='majorTickStyle'></label>";
    echo '<textarea id="'.$options['type'].'" style="width: 400px;" name="thermometer_style['.$options['type'].']">'.sanitize_text_field($value)."</textarea>";
    echo '<br/>'.__('Default','donation-thermometer').': <code>'.sanitize_text_field($options['default']).'</code></div>';
}
function minorTick_style_fn($options) {
    $value = (isset(get_option('thermometer_style')[$options['type']])) ? esc_html(get_option('thermometer_style')[$options['type']]) : $options['default'];
    echo "<div class='form-item'><label for='minorTickStyle'></label>";
    echo '<textarea id="'.$options['type'].'" style="width: 400px;" name="thermometer_style['.$options['type'].']">'.sanitize_text_field($value)."</textarea>";
    echo '<br/>'.__('Default','donation-thermometer').': <code>'.sanitize_text_field($options['default']).'</code></div>';
}
function orientation_style_fn($options) {
    $value = (isset(get_option('thermometer_style')[$options['type']])) ? esc_html(get_option('thermometer_style')[$options['type']]) : $options['default'];
    echo "<div class='form-item'><label for='minorTickStyle'></label>";
    echo '<textarea id="'.$options['type'].'" style="width: 400px;" name="thermometer_style['.$options['type'].']">'.sanitize_text_field($value)."</textarea>";
    echo '<br/>'.__('Default','donation-thermometer').': <code>'.sanitize_text_field($options['default']).'</code></div>';
}


function help_section_text_fn() {
    echo '<h3>'.__('Basic usage & sizing','donation-thermometer').'</h3>';
    echo '<p>'.sprintf(__('Thermometers can be placed in a page, post or widget with the shortcode %1$s.','donation-thermometer'),'<code>[thermometer]</code>').'<p>';
    echo '<p>'.sprintf(__('Default values for the amount raised and target can be set on the settings tab or in the shortcode: %1$s.','donation-thermometer'),'<code>[thermometer raised=1523 target=5000]</code>').'<p>';
    echo '<p>'.sprintf(__('Individual thermometers can be sized using %1$s (pixels), or %2$s (percentage of parent container). Set only width OR height as the aspect ratio remains constant.','donation-thermometer'),'<code>height=200</code>', '<code>width=20%</code>').'</p>';

    echo '<h3>'.__('Advanced shortcode parameters','donation-thermometer').'</h3>';
    echo '<p>'.sprintf(__('These additional parameters can be used within the %1$s shortcode to construct unique thermometers. Examples show the default settings:','donation-thermometer'),'<code>[thermometer]</code>').'</p>';
    echo '<p><ul>';
    echo '<li><b>target:</b> '.__('the target value','donation-thermometer').': <code>target=500</code></li>';
    echo '<li><b>raised:</b> '.__('the raised value','donation-thermometer').': <code>raised=250</code>. '.__('Custom shortcodes can also be used here (without brackets)','donation-thermometer').': <code>raised=\'sc name="dyn_raised"\'</code></li>';
    echo '<li><b>swapvalues:</b> '.__('swap target and raised value locations (useful if your raised amount exceeds 100% of the goal)','donation-thermometer').': <code>swapvalues=false</code></li>';
    echo '<li><b>targetlabels:</b> '.__('sub target labels can be on/off (will disappear to avoid overlap with raised value)','donation-thermometer').': <code>targetlabels=on</code></li>';
    echo '<li><b>fill:</b> '.__('custom fill colour (hex values)','donation-thermometer').': <code>fill=#d7191c</code></li>';
    echo '<li><b>filltype:</b> '.__('the style of colour fill in the the thermometer, either uniform or as a gradient','donation-thermometer').': <code>filltype=uniform</code></li>';
    echo '<li><b>fill2:</b> '.__('the second/upper custom fill colour (hex values) to define the fill gradient','donation-thermometer').': <code>fill2=#eb7e80</code></li>';
    #echo '<li><b>shadow:</b> '.__('thermometer shadow (3D effect)','donation-thermometer').': <code>shadow=false</code></li>';
    echo '<li><b>orientation:</b> '.__('portrait/landscape','donation-thermometer').': <code>orientation=portrait</code></li>';
    echo '<li><b>align:</b> '.__('alignment within the parent container (left/right/center/none)','donation-thermometer').': <code>align=left</code></li>';
    echo '<li><b>sep:</b> '.__('thousands separator','donation-thermometer').': <code>sep=,</code></li>';
    echo '<li><b>decsep:</b> '.__('decimal separator','donation-thermometer').': <code>decsep=.</code></li>';
    echo '<li><b>decimals:</b> '.__('decimal places to use for target, raised and percentage values','donation-thermometer').': <code>decimals=0</code></li>';
    echo '<li><b>currency:</b> '.__('raised/target value units','donation-thermometer').': <code>currency=$</code> (or <code>currency=null</code>)</li>';
    echo '<li><b>trailing:</b> '.__('currency symbols follow numeric values (true/false)','donation-thermometer').': <code>trailing=false</code></li>';
    echo '<li><b>alt:</b> '.__('the default alt and title attributes of the image can be modified, or toggled off. Use apostrophes to input a custom string','donation-thermometer').': <code>alt=\'Raised £1523 towards the £2000 target.\'</code> (or <code>alt=off</code>)</li>';
    echo '<li><b>ticks:</b> '.__('alignment of ticks and the raised value (left/top or right/bottom)','donation-thermometer').': <code>ticks=right</code></li>';
    echo '<li><b>colorramp:</b> '.__('the sequence of fill colours (hex values) used for multiple categories','donation-thermometer').': <code>colorramp=\'#d7191c; #fdae61; #abd9e9; #2c7bb6\'</code></li>';
    echo '<li><b>showtarget:</b> '.__('show the target value','donation-thermometer').': <code>showtarget=true</code></li>';
    echo '<li><b>showraised:</b> '.__('show the raised value','donation-thermometer').': <code>showraised=true</code></li>';
    echo '<li><b>showpercent:</b> '.__('show the percentage value','donation-thermometer').': <code>showpercent=true</code></li>';
    echo '<li><b>percentcolor:</b> '.__('the colour of the percentage value','donation-thermometer').': <code>percentcolor=#000000</code></li>';
    echo '<li><b>targetcolor:</b> '.__('the colour of the target value','donation-thermometer').': <code>targetcolor=#000000</code></li>';
    echo '<li><b>raisedcolor:</b> '.__('the colour of the raised value','donation-thermometer').': <code>raisedcolor=#000000</code></li>';
    echo '<li><b>subtargetcolor:</b> '.__('the colour of the sub-target values','donation-thermometer').': <code>subtargetcolor=#000000</code></li>';
    echo '</ul></p>';

    echo '<h3>'.__('Multiple categories/targets','donation-thermometer').'</h3>';
    echo '<p><ul>';
    echo '<li>'.__('The total raised in the thermometer can be partitioned into multiple categories by separating raised values with a semicolon (bottom to top)','donation-thermometer').':<br/><code>[thermometer raised=732;234;100]</code>.</li>';
    echo '<li>'.sprintf(__('The colours for each category are set using the default global option below, or can be set for individual thermometers using the %1$s parameter, separating hex values using a semicolon','donation-thermometer'),'<code>colorramp</code>').':<br/>
            <code>[thermometer raised=732;234;100 colorramp=\'#d7191c; #fdae61; #abd9e9\']</code>.</li>';
    echo '<li>'.__('Incrementing target levels can be set by separating values with a semicolon (bottom to top)','donation-thermometer').':<br/><code>[thermometer raised=500 target=600;1000]</code>.</li>';
    echo '<li>'.__('A legend can be included by defining labels separated by semicolons (bottom to top)','donation-thermometer').':<br/><code>legend=\'Source A; Source B; Source C\'</code>.</li>';
    echo '</ul></p>';

    echo '<h3>'.__('Custom CSS','donation-thermometer').'</h3>';
    echo '<p>'.__('You can further customise each SVG element of the thermometer using CSS code from the tab above. These edits will apply to all thermometers on the site.','donation-thermometer').'<br/>';
    printf(/* translators: 1-2 are html code for a url link */__('If you want to apply custom CSS rules to thermometers on individual pages/posts then you can %1$s use a plugin such as this%2$s.','donation-thermometer'),'<a href="https://wordpress.org/plugins/wp-add-custom-css/" target="_blank">','</a>');
    echo '</p>';

    echo '<h3>'.__('Further help','donation-thermometer').'</h3>';
    echo '<p>'.sprintf(/* translators: 1-2 are html code for a url link */__('Please use the %1$s support forum%2$s for further help.','donation-thermometer'), '<a href="https://wordpress.org/support/plugin/donation-thermometer/" target="_blank">','</a>');
    echo '</p>';
}


function preview_section_text_fn() {
    echo '<p>';
    $thermShort = '<code>[thermometer]</code>';
    printf(/* translators: 1: <code>[thermometer]</code> */__('Based on the plugin settings defined on this options page, the default shortcode %1$s will produce an SVG image like this:', 'donation-thermometer'), $thermShort);
    echo '</p>';

    echo do_shortcode('[thermometer]');
    echo '<p style="clear: left;"><b>'.__('Shortcode example', 'donation-thermometer').'</b>:<br/>';
    printf(/* translators: 1-3 are the actual shortcodes */__('So far we have raised £%1$s towards our £%2$s target! That‘s %3$s of the total!', 'donation-thermometer'), '<code>[therm_r]</code>', '<code>[therm_t]</code>', '<code>[therm_%]</code>');
    echo '<br/>';
    printf(/* translators: 1-3 are the actual shortcodes */__('So far we have raised £%1$s towards our £%2$s target! That‘s %3$s of the total!', 'donation-thermometer'), do_shortcode('[therm_r]'), do_shortcode('[therm_t]'), do_shortcode('[therm_%]'));
    echo '</p>';
}

function section_text_fn() {
    echo '<p>'.sprintf(__('These are the default settings for all thermometers on the site. Individual thermometers can be modified within the %1$s shortcode using the parameters associated with each setting highlighted in brackets.','donation-thermometer'), '<code>[thermometer]</code>').'</p>';
}

// TEXTBOX - Name: plugin_options[fill_colour]
function fill_colour_fn($options) {
    $value = (isset(get_option('thermometer_options')[$options['type']])) ? esc_html(get_option('thermometer_options')[$options['type']]) : $options['default'];
    echo "<div class='form-item' style='display: inline;'>";
    echo '<input id="'.$options['type'].'" type="text" name="thermometer_options['.$options['type'].']" value="'.sanitize_text_field($value).'" class="colorwell"/>';
    echo "  (<code>fill=#d7191c</code>)";
    echo '<div id="picker" style="display: inline; position: absolute; margin-left:100px;"></div>';
}

// thermometer fill type
function therm_filltype_fn($options) {
    $value = (isset(get_option('thermometer_options')[$options['type']])) ? esc_html(get_option('thermometer_options')[$options['type']]) : $options['default'];
    $items = array("uniform","gradient");
    echo '<select id="'.$options['type'].'" name="thermometer_options['.$options['type'].']">';
    foreach($items as $item) {
        $selected = ($item == sanitize_text_field($value)) ? 'selected="selected"' : '';
        echo "<option value='".$item."' ".$selected.">$item</option>";
    }
    echo "</select>";
    echo " (<code>filltype=uniform</code>)";
}

function fill2_colour_fn($options) {
    $value = (isset(get_option('thermometer_options')[$options['type']])) ? esc_html(get_option('thermometer_options')[$options['type']]) : $options['default'];
    echo "<div class='form-item' style='display: inline;'>";
    echo '<input id="'.$options['type'].'" type="text" name="thermometer_options['.$options['type'].']" value="'.sanitize_text_field($value).'" class="colorwell"/>';
    echo "  (<code>fill2=#eb7e80</code>)";
    echo '<div id="picker" style="display: inline; position: absolute; margin-left:100px;"></div>';
}

// DROP-DOWN-BOX - Name: plugin_options[currency]
function  setting_dropdown_fn($options) {
    $value = (isset(get_option('thermometer_options')[$options['type']])) ? esc_html(get_option('thermometer_options')[$options['type']]) : $options['default'];
    echo '<input id="'.$options['type'].'" type="text" size="5" name="thermometer_options['.$options['type'].']" value="'.sanitize_text_field($value).'" />';
    echo ' '.__('define a custom global currency value', 'donation-thermometer').' (<code>currency=$</code>)';
}

function  setting_thousands_fn($options) {
    $value = (isset(get_option('thermometer_options')[$options['type']])) ? esc_html(get_option('thermometer_options')[$options['type']]) : $options['default'];
    $sep = substr(sanitize_text_field($value),0,1);
    $items = array(", (".__('comma', 'donation-thermometer').")",". (".__('point', 'donation-thermometer').")"," (".__('space', 'donation-thermometer').")","(".__('none', 'donation-thermometer').")");
    echo '<select id="'.$options['type'].'" name="thermometer_options['.$options['type'].']">';
    foreach($items as $item) {
        $selected = (substr($item,0,1)==$sep) ? 'selected="selected"' : '';
        echo "<option value='".$item."' ".$selected.">$item</option>";
    }
    echo "</select>";
    echo ' '.__('ie. £1,000 or €1.000 or $1000', 'donation-thermometer').' (<code>sep=,</code>)';
}

function  setting_decsep_fn($options) {
    $value = (isset(get_option('thermometer_options')[$options['type']])) ? esc_html(get_option('thermometer_options')[$options['type']]) : $options['default'];
    $decsep = substr(sanitize_text_field($value),0,1);
    $items = array(". (".__('point', 'donation-thermometer').")",", (".__('comma', 'donation-thermometer').")");
    echo '<select id="'.$options['type'].'" name="thermometer_options['.$options['type'].']">';
    foreach($items as $item) {
        $selected = (substr($item,0,1)==$decsep) ? 'selected="selected"' : '';
        echo "<option value='".$item."' ".$selected.">$item</option>";
    }
    echo "</select>";
    echo ' '.__('ie. £54.32 or €54,32', 'donation-thermometer').' (<code>decsep=.</code>)';
}


function  setting_decimals_fn($options) {
    $value = (isset(get_option('thermometer_options')[$options['type']])) ? esc_html(get_option('thermometer_options')[$options['type']]) : $options['default'];
    echo '<input id="'.$options['type'].'" type="number" step="1" name="thermometer_options['.$options['type'].']" value="'.sanitize_text_field($value).'" style="width: 4em;" />';
    echo ' '.__('number of decimal places', 'donation-thermometer').' (<code>decimals=0</code>)';
    echo '</div>';
}

// tick alignment
function  tick_align_fn($options) {
    $value = (isset(get_option('thermometer_options')[$options['type']])) ? esc_html(get_option('thermometer_options')[$options['type']]) : $options['default'];
    $items = array("right","left");
    echo '<select id="'.$options['type'].'" name="thermometer_options['.$options['type'].']">';
    foreach($items as $item) {
        $selected = ($item == sanitize_text_field($value)) ? 'selected="selected"' : '';
        echo "<option value='".$item."' ".$selected.">$item</option>";
    }
    echo "</select>";
    echo ' '.__('the raised value will also shift accordingly', 'donation-thermometer').' (<code>ticks=right</code>)';
}

// CHECK-BOX - Name: thermometer_options[trailing]
function setting_trailing_fn($options) {
    $value = (isset(get_option('thermometer_options')[$options['type']])) ? esc_html(get_option('thermometer_options')[$options['type']]) : $options['default'];
    $items = array("false","true");
    echo '<select id="'.$options['type'].'" name="thermometer_options['.$options['type'].']">';
    foreach($items as $item) {
        $selected = ($item == sanitize_text_field($value)
            or (sanitize_text_field($value) == '1' && $item == 'true')
            or (sanitize_text_field($value) == '2' && $item == 'false')
            or (sanitize_text_field($value) == '0' && $item == 'false')) ? 'selected="selected"' : '';
        echo "<option value='".$item."' ".$selected.">$item</option>";
    }
    echo "</select>";
    echo ' '.__('ie. £1,000 (false) or 1.000 NOK (true)', 'donation-thermometer').' (<code>trailing=false</code>)';
}

// CHECKBOX - Name: plugin_options[chkbox1] show percentage
function setting_chk1_fn($options) {
    $value = (isset(get_option('thermometer_options')[$options['type']])) ? esc_html(get_option('thermometer_options')[$options['type']]) : $options['default'];
    $items = array("true","false");
    echo '<select id="'.$options['type'].'" name="thermometer_options['.$options['type'].']">';
    foreach($items as $item) {
        $selected = ($item == sanitize_text_field($value)
            or (sanitize_text_field($value) == '1' && $item == 'true')
            or (sanitize_text_field($value) == '0' && $item == 'false')) ? 'selected="selected"' : '';
        echo "<option value='".$item."' ".$selected.">$item</option>";
    }
    echo "</select>";
    echo " (<code>showpercent=true</code>)";
}
// TEXTBOX - Name: plugin_options[text_colour] percentage colour
function text_colour_fn($options) {
    $value = (isset(get_option('thermometer_options')[$options['type']])) ? esc_html(get_option('thermometer_options')[$options['type']]) : $options['default'];
    echo "<div class='form-item'>";
    echo '<input id="'.$options['type'].'" type="text" name="thermometer_options['.$options['type'].']" value="'.sanitize_text_field($value).'" class="colorwell" />';
    echo " (<code>percentcolor=#000000</code>)";
}
// CHECKBOX - Name: plugin_options[chkbox2] show target
function setting_chk2_fn($options) {
    $value = (isset(get_option('thermometer_options')[$options['type']])) ? esc_html(get_option('thermometer_options')[$options['type']]) : $options['default'];
    $items = array("true","false");
    echo '<select id="'.$options['type'].'" name="thermometer_options['.$options['type'].']">';
    foreach($items as $item) {
        $selected = ($item == sanitize_text_field($value)
            or (sanitize_text_field($value) == '1' && $item == 'true')
            or (sanitize_text_field($value) == '0' && $item == 'false')) ? 'selected="selected"' : '';
        echo "<option value='".$item."' ".$selected.">$item</option>";
    }
    echo "</select>";
    echo " (<code>showtarget=true</code>)";
}

// CHECKBOX - Name: plugin_options[targetlabels] show sub-targets
function setting_chk4_fn($options) {
    $value = (isset(get_option('thermometer_options')[$options['type']])) ? esc_html(get_option('thermometer_options')[$options['type']]) : $options['default'];
    $items = array("true","false");
    echo '<select id="'.$options['type'].'" name="thermometer_options['.$options['type'].']">';
    foreach($items as $item) {
        $selected = ($item == sanitize_text_field($value)
            or (sanitize_text_field($value) == '1' && $item == 'true')
            or (sanitize_text_field($value) == '0' && $item == 'false')) ? 'selected="selected"' : '';
        echo "<option value='".$item."' ".$selected.">$item</option>";
    }
    echo "</select>";
    echo ' '.__('sub-target values adjacent to the thermometer', 'donation-thermometer').' (<code>targetlabels=on</code>)';
}

// TEXTBOX - Name: plugin_options[subtarget_colour]
function subtarget_colour_fn($options) {
    $value = (isset(get_option('thermometer_options')[$options['type']])) ? (get_option('thermometer_options')[$options['type']]) : $options['default'];
    echo "<div class='form-item'>";
    echo '<input id="'.$options['type'].'" type="text" name="thermometer_options['.$options['type'].']" value="'.sanitize_text_field($value).'" class="colorwell"/>';
    echo " (<code>subtargetcolor=#8a8a8a</code>)";
}

// CHECKBOX - Name: plugin_options[chkbox3] show raised
function setting_chk3_fn($options) {
    $value = (isset(get_option('thermometer_options')[$options['type']])) ? esc_html(get_option('thermometer_options')[$options['type']]) : $options['default'];
    $items = array("true","false");
    echo '<select id="'.$options['type'].'" name="thermometer_options['.$options['type'].']">';
    foreach($items as $item) {
        $selected = ($item == sanitize_text_field($value)
            or (sanitize_text_field($value) == '1' and $item == 'true')
            or (sanitize_text_field($value) == '0' and $item == 'false')) ? 'selected="selected"' : '';
        echo "<option value='".$item."' ".$selected.">$item</option>";
    }
    echo "</select>";
    echo " (<code>showraised=true</code>)";
}

// thermometer shadow
function therm_shadow_fn($options) {
    $value = (isset(get_option('thermometer_options')[$options['type']])) ? esc_html(get_option('thermometer_options')[$options['type']]) : $options['default'];
    $items = array("true","false");
    echo '<select id="'.$options['type'].'" name="thermometer_options['.$options['type'].']">';
    foreach($items as $item) {
        $selected = ($item == sanitize_text_field($value)
            or (sanitize_text_field($value) == '1' and $item == 'true')
            or (sanitize_text_field($value) == '0' and $item == 'false')) ? 'selected="selected"' : '';
        echo "<option value='".$item."' ".$selected.">$item</option>";
    }
    echo "</select>";
    echo " (<code>shadow=false</code>)";
}

// thermometer orientation
function therm_orientation_fn($options) {
    $value = (isset(get_option('thermometer_options')[$options['type']])) ? esc_html(get_option('thermometer_options')[$options['type']]) : $options['default'];
    $items = array("portrait","landscape");
    echo '<select id="'.$options['type'].'" name="thermometer_options['.$options['type'].']">';
    foreach($items as $item) {
        $selected = ($item == sanitize_text_field($value)) ? 'selected="selected"' : '';
        echo "<option value='".$item."' ".$selected.">$item</option>";
    }
    echo "</select>";
    echo " (<code>orientation=portrait</code>)";
}

// TEXTBOX - Name: plugin_options[target_colour]
function target_colour_fn($options) {
    $value = (isset(get_option('thermometer_options')[$options['type']])) ? esc_html(get_option('thermometer_options')[$options['type']]) : $options['default'];
    echo "<div class='form-item'>";
    echo '<input id="'.$options['type'].'" type="text" name="thermometer_options['.$options['type'].']" value="'.sanitize_text_field($value).'" class="colorwell" />';
    echo " (<code>targetcolor=#000000</code>)";
}
// TEXTBOX - Name: plugin_options[raised_colour]
function raised_colour_fn($options) {
    $value = (isset(get_option('thermometer_options')[$options['type']])) ? esc_html(get_option('thermometer_options')[$options['type']]) : $options['default'];
    echo "<div class='form-item'>";
    echo '<input id="'.$options['type'].'" type="text" name="thermometer_options['.$options['type'].']" value="'.sanitize_text_field($value).'" class="colorwell" />';
    echo " (<code>raisedcolor=#000000</code>)";
}
// TEXTBOX - Name: plugin_options[color_ramp]
function color_ramp_fn($options) {
    $value = (isset(get_option('thermometer_options')[$options['type']])) ? esc_textarea(get_option('thermometer_options')[$options['type']]) : $options['default'];
    echo '<textarea id="color_ramp" rows="3" col="15" name="thermometer_options['.$options['type'].']" style="margin-right: 20px;">'.sanitize_textarea_field($value).'</textarea>';
    echo "<div id='rampPreview' style='display: inline-block;'></div>";
    echo '<p>'.__('a list of hex colours for multiple thermometer categories (bottom to top), separated by semicolons', 'donation-thermometer').'<br/>
	(e.g., <code>#d7191c; #fdae61; #abd9e9; #2c7bb6</code>)  (<code>colorramp=#d7191c;...</code>).';
    echo __('This default colour ramp is red-green colour-blind friendly. Source:', 'donation-thermometer').' <a href="https://colorbrewer2.org" target="_blank">ColorBrewer 2.0</a>.';
    echo '</p>';

}
// TEXTBOX - Name: plugin_options[target_string]
function target_string_fn($options) {
    $value = (isset(get_option('thermometer_options')[$options['type']])) ? esc_html(get_option('thermometer_options')[$options['type']]) : $options['default'];
    echo '<input id="'.$options['type'].'" type="text" name="thermometer_options['.$options['type'].']" value="'.sanitize_text_field($value).'" />';
    echo ' '.__('separate incrementing target values with a semi-colon for multiple categories', 'donation-thermometer').' (<code>target=500</code>). <code>[therm_t]</code>';
}
// TEXTBOX - Name: plugin_options[raised_string]
function raised_string_fn($options) {
    $value = (isset(get_option('thermometer_options')[$options['type']])) ? esc_html(get_option('thermometer_options')[$options['type']]) : $options['default'];
    echo '<input id="'.$options['type'].'" type="text" name="thermometer_options['.$options['type'].']" value="'.sanitize_text_field($value).'" />';
    echo ' '.__('separate raised values with a semi-colon for multiple categories', 'donation-thermometer').' (<code>raised=1000</code>).';
    printf(/* translator: 1: the raised shortcode*/__('%1$s is cumulative', 'donation-thermometer'),'<code>[therm_r]</code>');
    echo '<p><b>'.__('Shortcode example', 'donation-thermometer').'</b>: ';
    printf(/* translators: 1-3 are the actual shortcodes */__('So far we have raised £%1$s towards our £%2$s target! That‘s %3$s of the total!', 'donation-thermometer'), '<code>[therm_r]</code>', '<code>[therm_t]</code>', '<code>[therm_%]</code>');
}
