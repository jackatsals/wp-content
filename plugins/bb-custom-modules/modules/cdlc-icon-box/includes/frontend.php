<div class="cdlc-icon-box not-prose">
    <?php if (!empty($settings->icon)) : ?>
        <i class="cdlc-icon-box__icon <?php echo $settings->icon; ?>" aria-hidden="true"></i>
    <?php endif; ?>
    <?php if (!empty($settings->text)) : ?>
        <a class="cdlc-icon-box__link a11y-link-wrap" href="<?php echo $settings->link; ?>">
            <?php echo $settings->text; ?>
        </a>
    <?php endif; ?>
</div>
