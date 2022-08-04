<div class="cdlc-quick-links not-prose">
    <?php if (!empty($settings->title)) : ?>
        <h3 class="cdlc-quick-links__title"><?php echo $settings->title; ?></h3>
    <?php endif; ?>
    <?php if (!empty($settings->quick_links)) : ?>
        <div class="cdlc-quick-links__grid">
            <?php foreach ($settings->quick_links as $quick_link) : ?>
                <div class="cdlc-quick-links__grid-item">
                    <?php if (!empty($quick_link->icon)) : ?>
                        <i class="cdlc-quick-links__icon dark:text-white <?php echo $quick_link->icon; ?>" aria-hidden="true"></i>
                    <?php endif; ?>
                    <?php if (!empty($quick_link->text)) : ?>
                        <a class="cdlc-quick-links__link a11y-link-wrap" href="<?php echo $quick_link->link; ?>">
                            <?php echo $quick_link->text; ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
