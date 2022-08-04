<div class="cdlc-call-to-action not-prose">
    <?php if (!empty($settings->text)) : ?>
        <h2 class="cdlc-call-to-action__text">
            <?php echo $settings->text; ?>
        </h2>
    <?php endif; ?>
    <?php if (!empty($settings->link)) : ?>
        <div class="cdlc-call-to-action__link-wrapper">
            <a class="cdlc-call-to-action__link btn" href="<?php echo $settings->link; ?>"><?php echo $settings->link_text; ?></a>
        </div>
    <?php endif; ?>
    <?php if (!empty($settings->background_image)) : ?>
        <img src="<?php echo wp_get_attachment_url($settings->background_image); ?>" alt="" />
    <?php endif; ?>
</div>
