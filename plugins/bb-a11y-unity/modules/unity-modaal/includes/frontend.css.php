<?php

/**
 * Align
 */
FLBuilderCSS::responsive_rule([
    'settings'     => $settings,
    'setting_name' => 'button_align',
    'selector'     => ".fl-node-$id .unity-modaal__button-wrapper",
    'prop'         => 'text-align',
]);

/**
 * Padding
 */
FLBuilderCSS::dimension_field_rule([
    'settings'     => $settings,
    'setting_name' => 'button_padding',
    'selector'     => ".fl-node-$id a.unity-modaal__button",
    'unit'         => 'px',
    'props'        => [
        'padding-top'    => 'button_padding_top',
        'padding-right'  => 'button_padding_right',
        'padding-bottom' => 'button_padding_bottom',
        'padding-left'   => 'button_padding_left',
    ],
]);

/**
 * Typography
 */
FLBuilderCSS::typography_field_rule([
    'settings'     => $settings,
    'setting_name' => 'button_typography',
    'selector'     => ".fl-node-$id a.unity-modaal__button, .fl-node-$id a.unity-modaal__button:visited",
]);

/**
 * Border
 */
FLBuilderCSS::border_field_rule([
    'settings'     => $settings,
    'setting_name' => 'button_border',
    'selector'     => ".fl-node-$id a.unity-modaal__button",
]);

?>

<?php if (!empty($settings->button_background_color)) : ?>
    <?php $rgb = $module->processHexToRgb($settings->button_background_color); ?>
    .fl-node-<?php echo $id; ?> .unity-modaal__button {
        --red: <?php echo $rgb->red(); ?>;
        --green: <?php echo $rgb->green(); ?>;
        --blue: <?php echo $rgb->blue(); ?>;
        --accessible-color: calc(((((var(--red) * 299) + (var(--green) * 587) + (var(--blue) * 114)) / 1000) - 128) * -1000);
    }
<?php endif; ?>

<?php if (!empty($settings->button_background_color_interact)) : ?>
    <?php $rgb = $module->processHexToRgb($settings->button_background_color_interact); ?>
    .fl-node-<?php echo $id; ?> .unity-modaal__button {
        --interact-red: <?php echo $rgb->red(); ?>;
        --interact-green: <?php echo $rgb->green(); ?>;
        --interact-blue: <?php echo $rgb->blue(); ?>;
        --interact-accessible-color: calc(((((var(--interact-red) * 299) + (var(--interact-green) * 587) + (var(--interact-blue) * 114)) / 1000) - 128) * -1000);
    }
<?php endif; ?>

<?php if ($settings->button_width === 'custom') : ?>
    .fl-node-<?php echo $id; ?> a.unity-modaal__button {
        width: <?php echo $settings->button_custom_width . $settings->button_custom_width_unit; ?>;
    }
<?php elseif ($settings->button_width === 'full') : ?>
    .fl-node-<?php echo $id; ?> a.unity-modaal__button {
        width: 100%;
    }
<?php endif; ?>
