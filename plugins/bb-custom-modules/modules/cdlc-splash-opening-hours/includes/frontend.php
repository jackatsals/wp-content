<div class="cdlc-splash-opening-hours not-prose">
    <div class="cdlc-splash-opening-hours-main">
        <?php if ($settings->title) : ?>
            <h2><span><?php echo $settings->title; ?></span></h2>
        <?php elseif (shortcode_exists('open_text')) : ?>
            <h2><span><?php echo do_shortcode('[open_text]%if_open_today% Today’s hours: %hours_today% %end% %if_closed_today% Closed today. %end%[/open_text]'); ?></span></h2>
        <?php endif; ?>
        <?php if (!empty($settings->text)) : ?>
            <div><span><?php echo $settings->text; ?></span></div>
        <?php endif; ?>
        <?php if (!empty($settings->background_image)) : ?>
            <img src="<?php echo wp_get_attachment_url($settings->background_image); ?>" alt="" />
        <?php endif; ?>
    </div>
    <?php if (shortcode_exists('open')) : ?>
        <div class="cdlc-splash-opening-hours-aside">
            <h2>
                <?php if (!empty($settings->hours_icon)) : ?>
                    <i class="cdlc-splash-opening-hours-aside__icon <?php echo $settings->hours_icon; ?>" aria-hidden="true"></i>
                <?php endif; ?>
                <?php echo ($settings->hours_title); ?>
            </h2>
            <div class="cdlc-splash-opening-hours-aside__content">
                <div class="flex items-start">
                    <?php echo do_shortcode('[open]'); ?>
                </div>
                <?php if (!empty($settings->hours_link) && !empty($settings->hours_link_text)) : ?>
                    <div class="link">
                        <a href="<?php echo $settings->hours_link; ?>"><?php echo $settings->hours_link_text; ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
