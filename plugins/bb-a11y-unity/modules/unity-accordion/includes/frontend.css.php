<?php if (!empty($settings->accent_color)) : ?>
    <?php $accent_color = $module->get_module_accent_color(); ?>
    .fl-node-<?php echo $id; ?> .unity-accordion {
        --theme-primary-color: <?php echo $accent_color ?>;
        --theme-primary-a11y-color: <?php echo Unity\A11Y\get_a11y_text_color($accent_color); ?>;
    }
       
    .fl-node-<?php echo $id; ?> .unity-accordion__trigger {
        border-color: var(--theme-primary-color);
    }

    .fl-node-<?php echo $id; ?> .unity-accordion__trigger[aria-expanded="true"] {
        background-color: var(--theme-primary-color);
        color: var(--theme-primary-a11y-color);
    }
<?php endif; ?>
