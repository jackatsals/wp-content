<div class="unity-audio">
    <audio class="unity-audio__player" controls>
        <?php if (!empty($settings->audio_mp3)) : ?>
            <source src="<?php echo esc_url($settings->audio_mp3); ?>" type="audio/mp3" />
        <?php endif; ?>
    </audio>
    <?php if (!empty($settings->transcript_text)) : ?>
        <div class="unity-audio__transcript-text">
            <?php echo wpautop($settings->transcript_text); ?>
        </div>
    <?php endif; ?>
</div>
