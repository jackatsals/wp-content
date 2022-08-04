<?php if ($settings->cta_link) : ?>
    <div class="unity-jump-link">
        <a href="<?php echo esc_url($settings->cta_link); ?>"><?php echo $settings->cta_text; ?></a>
    </div>
<?php endif; ?>
