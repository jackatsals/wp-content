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
                <img src="<?php echo wp_get_attachment_url($settings->background_image); ?>" alt="" />
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
    <div class="cdlc-splash-aside">
        <h2>
            <?php if (!empty($settings->content_feed_icon)) : ?>
                <i class="cdlc-content-feed__icon dark:text-white <?php echo $settings->content_feed_icon; ?>" aria-hidden="true"></i>
            <?php endif; ?>
            <?php echo $settings->content_feed_title; ?>
        </h2>
        <div class="cdlc-splash-aside__content">
            <?php if ($query->have_posts()) : ?>
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <article>
                        <h3><a class="a11y-link-wrap" href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></h3>
                            <?php if (has_post_thumbnail()) : ?>
                                <figure>
                                    <?php echo the_post_thumbnail('thumbnail'); ?>
                                </figure>
                            <?php endif; ?>
                    </article>
                <?php endwhile; ?>
                <?php if (!empty($settings->link)) : ?>
                    <a href="<?php echo $settings->link; ?>"><?php echo $settings->link_text; ?></a>
                <?php endif; ?>
            <?php else : ?>
                <?php echo __('Sorry, nothing found. Please check back soon!', 'cdlc'); ?>
            <?php endif; wp_reset_postdata(); ?>
        </div>
    </div>
</section>
