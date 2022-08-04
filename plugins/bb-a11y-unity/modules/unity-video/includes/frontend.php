<div class="unity-video">
    <?php if ($settings->video_type === 'html5') : ?>
        <video class="unity-video__player" playsinline controls data-poster="<?php echo wp_get_attachment_url($settings->video_poster); ?>">
            <?php if (!empty($settings->video_mp4)) : ?>
                <source src="<?php echo wp_get_attachment_url($settings->video_mp4); ?>" type="video/mp4" />
            <?php endif; ?>
            <?php if (!empty($settings->video_webm)) : ?>
                <source src="<?php echo wp_get_attachment_url($settings->video_webm); ?>" type="video/webm" />
            <?php endif; ?>
            <?php if (!empty($settings->video_captions_webvtt)) : ?>
                <track kind="captions" label="English captions" src="<?php echo wp_get_attachment_url($settings->video_captions_webvtt); ?>" srclang="en" default />
            <?php endif; ?>
        </video>
    <?php else : ?>
        <div class="unity-video__player" id="player-<?php echo $module->node; ?>" data-plyr-provider="<?php echo esc_attr($settings->video_type); ?>" data-plyr-embed-id="<?php echo esc_url($settings->video_embed); ?>"></div>
    <?php endif; ?>
</div>
