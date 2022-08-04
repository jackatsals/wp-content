<?php if (!empty($settings->panels)) : ?>
    <div class="unity-accordion" data-allow-toggle>
        <?php $i = 0; foreach ($settings->panels as $panel) : ?>
            <?php if (!empty($panel->title)) : ?>
                <?php $attrs = $module->get_trigger_attributes($i); ?>
                <button class="unity-accordion__trigger" id="<?php echo esc_attr($attrs['id']); ?>" aria-expanded="false" aria-controls="<?php echo esc_attr($attrs['controls']); ?>">
                    <?php echo $panel->title; ?>
                    <svg viewBox="0 0 10 10" aria-hidden="true" focusable="false">
                        <rect class="vert" height="8" width="2" y="1" x="4" />
                        <rect height="2" width="8" y="4" x="1" />
                    </svg>
                </button>
            <?php endif; ?>
            <?php if (!empty($panel->content)) : ?>
                <?php $attrs = $module->get_panel_attributes($i); ?>
                <div role="region" class="unity-accordion__panel" id="<?php echo esc_attr($attrs['id']); ?>" aria-labelledby="<?php echo esc_attr($attrs['labelledby']); ?>" hidden>
                    <div><?php echo $panel->content; ?></div>
                </div>
            <?php endif; ?>
        <?php $i++; endforeach; ?>
    </div>
<?php endif; ?>
