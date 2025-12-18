<?php

$query = $module->queryPosts($settings->post_type);

?>

<section class="cdlc-splash not-prose">
    <div class="cdlc-splash-main">
        <article class="cdlc-splash__article">
            <?php if (!empty($settings->title)) : ?>
                <h2><span><?php echo $settings->title; ?></span></h2>
            <?php endif; ?>
            <?php if (!empty($settings->text)) : ?>
                <div><span><?php echo $settings->text; ?></span></div>
            <?php endif; ?>
        </article>
        <div class="cdlc-splash__background">
            <?php if (!empty($settings->background_image)) : ?>
                <?php echo wp_get_attachment_image($settings->background_image, 'large', false); ?>
            <?php endif; ?>
        </div>
        <?php if (!empty($settings->quick_links)) : ?>
            <div class="cdlc-splash__quick-links dark:bg-black dark:text-white">
                <div class="cdlc-splash__quick-links-content">
                    <?php if (!empty($settings->quick_links_title)) : ?>
                        <h2><?php echo $settings->quick_links_title; ?></h2>
                    <?php endif; ?>
                    <?php echo $settings->quick_links_text; ?>
                    <div class="cdlc-splash__quick-links-grid">
                        <?php foreach ($settings->quick_links as $quick_link) : ?>
                            <div class="cdlc-splash__quick-links-grid-item">
                                <?php if (!empty($quick_link->icon)) : ?>
                                    <i class="cdlc-splash__quick-links-icon dark:text-white <?php echo $quick_link->icon; ?>" aria-hidden="true"></i>
                                <?php endif; ?>
                                <?php if (!empty($quick_link->text)) : ?>
                                    <a class="cdlc-splash__quick-links-grid-link a11y-link-wrap" href="<?php echo $quick_link->link; ?>">
                                        <?php echo $quick_link->text; ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
