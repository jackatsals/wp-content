<div class="cdlc-layout-card entry not-prose bg-white dark:bg-neutral-800 p-6 relative
  <?php
    if(!empty($settings->direction) && $settings->direction == 'vertical') echo (' vertical') ?>">
    <?php if (!empty( $settings->image )) : ?>
      <div class="img-container">
        <img src="<?php echo esc_url($settings->image_src); ?>" alt="<?php echo get_post_meta($settings->image, '_wp_attachment_image_alt', true); ?>" />
      </div>
    <?php endif ?>
  <div class="text-container">
    <?php if (!empty( $settings->title )) : ?>
      <h3 class="entry-title"><?php echo $settings->title; ?></h3>
    <?php endif ?>

    <?php if (!empty( $settings->text )) : ?>
      <p> <?php echo( $settings->text ) ?> </p>
    <?php endif ?>

    <div class="button-container">
      <?php if (!empty( $settings->button_text) && !empty( $settings->button_url)) : ?>
        <a class="btn" href="<?php echo($settings->button_url)?>"><?php echo($settings->button_text)?></a>
      <?php endif ?>
    </div>

  </div>
</div>
