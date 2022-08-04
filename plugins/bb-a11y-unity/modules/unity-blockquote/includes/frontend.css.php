<?php

/**
 * Align
 */
FLBuilderCSS::responsive_rule([
    'settings'     => $settings,
    'setting_name' => 'align',
    'selector'     => ".fl-node-$id .unity-blockquote",
    'prop'         => 'text-align',
]);

?>

<?php if (!empty($settings->accent_color)) : ?>
    .fl-node-<?php echo $id; ?> .unity-blockquote {
        border-color: #<?php echo $settings->accent_color; ?>
    }
<?php endif; ?>
