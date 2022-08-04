<?php

$query = $module->queryPosts($settings->post_type);

?>

<div class="cdlc-content-feed not-prose">
    <h2>
        <?php if (!empty($settings->icon)) : ?>
            <i class="cdlc-content-feed__icon dark:text-white <?php echo $settings->icon; ?>" aria-hidden="true"></i>
        <?php endif; ?>
        <?php echo $settings->title; ?>
    </h2>
    <div class="cdlc-content-feed__content">
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
