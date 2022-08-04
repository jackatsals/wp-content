<?php if (!empty($settings->numbers)) : ?>
    <div class="unity-numbers">
        <?php foreach ($settings->numbers as $number) : ?>
            <div>
                <span
                    class="unity-numbers__count"
                    data-number="<?php echo esc_attr($number->number); ?>"
                    data-number-decimal-places="<?php echo esc_attr($number->decimal_places); ?>"
                    data-number-prefix="<?php echo esc_attr($number->prefix); ?>"
                    data-number-suffix="<?php echo esc_attr($number->suffix); ?>"
                    aria-hidden="true"
                >
                    <?php echo $number->number; ?>
                </span>
                <span class="unity-numbers__count-text" aria-hidden="true"><?php echo $number->text; ?></span>

                <?php if (!empty($number->aria_label)) : ?>
                    <span class="screen-reader-text"><?php echo $number->aria_label; ?></span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
