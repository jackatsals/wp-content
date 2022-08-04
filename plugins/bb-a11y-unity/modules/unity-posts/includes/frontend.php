<?php

$query = FLBuilderLoop::query($settings);

?>


<?php if ($query->have_posts()) : ?>
    <div class="unity-posts">
        <?php while ($query->have_posts()) : $query->the_post(); ?>
            <article class="unity-posts__entry">
                <?php if (has_post_thumbnail() && $settings->display_thumbnail === 'show') : ?>
                    <figure class="unity-posts__entry-image"><?php echo the_post_thumbnail($settings->thumbnail_size); ?></figure>
                <?php endif; ?>
                <h2 class="unity-posts__entry-heading">
                    <a class="unity-posts__entry-link" href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a>
                </h2>
                <div class="unity-posts__entry-meta">
                    <?php if ($settings->display_date === 'show') : ?>
                        <span>Published on <time class="updated" datetime="<?php echo get_post_time('c', true); ?>"><?php echo get_the_date(); ?></time></span>
                    <?php endif; ?>
                    <?php if ($settings->display_author === 'show') : ?>
                        <?php $author_id = get_the_author_meta('ID'); ?>
                        <span>By <?php echo get_the_author_meta('nicename', $author_id); ?></span>
                    <?php endif; ?>
                </div>
                <?php if ($settings->display_excerpt === 'show') : ?>
                    <div class="unity-posts__entry-excerpt">
                        <?php echo wpautop(get_the_excerpt()); ?>
                    </div>
                <?php endif; ?>
            </article>
        <?php endwhile; ?>
    </div>
<?php endif; ?>
<?php wp_reset_postdata(); ?>
