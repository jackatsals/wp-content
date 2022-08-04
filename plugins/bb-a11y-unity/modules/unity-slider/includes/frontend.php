<?php if ($items = $module->getSliderItems($settings)) : ?>
    <div class="unity-slider" data-slide-count-lg="" data-slide-count-md="" data-slide-count-sm="">
        <?php foreach ($items as $item) : ?>
            <div class="unity-slider__slide">
                <?php if (!empty($item->slider_item_image)) : ?>
                    <figure class="unity-slider__slide-image-wrap">
                        <?php echo wp_get_attachment_image($item->slider_item_image, $setting->slider_item_images_size); ?>
                    </figure>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
