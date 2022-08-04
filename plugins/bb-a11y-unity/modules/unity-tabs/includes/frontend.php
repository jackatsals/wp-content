<?php if (!empty($settings->tabs)) : ?>
    <div class="unity-tabs unity-tabs--<?php echo esc_attr($settings->aria_orientation); ?>">
        <div role="tablist" aria-label="<?php echo esc_attr($settings->aria_label); ?>" aria-orientation="<?php echo esc_attr($settings->aria_orientation); ?>">
            <?php $i = 0; foreach ($settings->tabs as $tab) : ?>
                <?php $attrs = $module->get_tab_attributes($i); ?>
                <button class="unity-tabs__tab" role="tab" id="<?php echo esc_attr($attrs['id']); ?>" aria-selected="<?php echo esc_attr($attrs['selected']); ?>" aria-controls="<?php echo esc_attr($attrs['controls']); ?>" <?php echo esc_attr($attrs['tabindex']); ?>>
                    <span><?php echo $tab->title; ?></span>
                    <?php if (!empty($tab->icon)) : ?>
                        <i class="fl-button-icon fl-button-icon-before <?php echo $tab->icon; ?>" aria-hidden="true"></i>
                    <?php endif; ?>
                </button>
            <?php $i++; endforeach; ?>
        </div>
        <?php $i = 0; foreach ($settings->tabs as $tabpanel) : ?>
            <?php $attrs = $module->get_tabpanel_attributes($i); ?>
            <div class="unity-tabs__tabpanel" role="tabpanel" id="<?php echo esc_attr($attrs['id']); ?>" aria-labelledby="<?php echo esc_attr($attrs['labelledby']); ?>" tabindex="0" <?php echo $attrs['hidden']; ?>>
                <?php echo $tabpanel->content; ?>
            </div>
        <?php $i++; endforeach; ?>
    </div>
<?php endif; ?>
