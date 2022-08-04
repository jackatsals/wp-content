<?php if (!empty($settings->accent_color)) : ?>
    <?php $accent_color = $module->get_module_accent_color(); ?>
    .fl-node-<?php echo $id; ?> .unity-tabs {
        --theme-primary-color: <?php echo $accent_color ?>;
        --theme-primary-a11y-color: <?php echo Unity\A11Y\get_a11y_text_color($accent_color); ?>;
    }

    .fl-node-<?php echo $id; ?> .unity-tabs__tab {
        border-color: var(--theme-primary-color);
    }

    .fl-node-<?php echo $id; ?> .unity-tabs__tab[aria-selected="true"] {
        border-color: var(--theme-primary-color);
        color: var(--theme-primary-a11y-color);
        background-color: var(--theme-primary-color);
    }

    .fl-node-<?php echo $id; ?> .unity-tabs.unity-tabs--horizontal > [role="tablist"] {
        border-bottom-color: var(--theme-primary-color);
    }
<?php endif; ?>
