<?php

/**
 * Align
 */
FLBuilderCSS::responsive_rule([
    'settings'     => $settings,
    'setting_name' => 'align',
    'selector'     => ".fl-builder-content .fl-node-$id .unity-numbers",
    'prop'         => 'text-align',
]);

?>

<?php if (!empty($settings->number_font_size)) : ?>
    .fl-node-<?php echo $id; ?> span.unity-numbers__count {
        font-size: <?php echo $settings->number_font_size . $settings->number_font_size_unit; ?>;
    }
<?php endif; ?>
