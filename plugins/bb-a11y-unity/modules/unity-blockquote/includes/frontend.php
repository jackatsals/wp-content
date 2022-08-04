<?php if ($settings->blockquote) : ?>
    <blockquote class="unity-blockquote">
        <?php echo wpautop($settings->blockquote); ?>
        <?php if ($settings->cite) : ?>
            <cite><?php echo $settings->cite; ?></cite>
        <?php endif; ?>
    </blockquote>
<?php endif; ?>
