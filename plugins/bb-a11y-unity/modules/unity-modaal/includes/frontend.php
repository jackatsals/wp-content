<?php

$cta_target = $settings->cta_target ?: "modaal-{$id}";
$modaal_id = $settings->modaal_id ?: "modaal-{$id}";

?>

<?php if (!empty($settings->cta_text)) : ?>
    <div class="unity-modaal">
        <div class="unity-modaal__button-wrapper">
            <a role="button" href="#<?php echo $cta_target; ?>" class="unity-modaal__button">
                <?php echo $settings->cta_text; ?>
                <?php if (!empty($settings->cta_icon)) : ?>
                    <i class="fl-button-icon fl-button-icon-before <?php echo $settings->cta_icon; ?>" aria-hidden="true"></i>
                <?php endif; ?>
            </a>
        </div>
        <?php if (!empty($settings->modaal_content)) : ?>
            <div id="<?php echo $modaal_id; ?>" style="display:none;">
                <?php echo $settings->modaal_content; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
