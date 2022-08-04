<?php
/**
 * Plugin Name:       CDLC Custom Modules
 * Plugin URI:
 * Description:       Custom page builder modules for Beaver Builder.
 * Version:           1.0.0
 * Author:            Unity Web Agency
 * Author URI:        https://unitywebagency.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cdlc
 * Domain Path:       /languages
 */

define('CBB_CDLC_VERSION', '1.0.0');
define('CBB_CDLC_DIR', plugin_dir_path(__FILE__));
define('CBB_CDLC_URL', plugins_url('/', __FILE__ ));

/**
 * Check for required WordPress plugins.
 *
 * @since    1.0.0
 */
add_action('admin_notices', function () {
    $requires = [];

    if (!is_plugin_active('bb-plugin/fl-builder.php')) {
        $required[] = [
            'link' => 'https://www.wpbeaverbuilder.com/',
            'name' => __('Beaver Builder', 'cdlc'),
        ];
    }

    if (!empty($required)) {
        foreach ($required as $req) {
            ?>
            <div class="notice notice-error"><p>
                <?php printf(__('<b>%s Plugin</b>: <a href="%s" target="_blank" rel="noreferrer noopener">%s</a> must be installed and activated.', 'cdlc'), __('Custom Modules', 'cdlc'), $req['link'], $req['name']); ?>
            </p></div>
            <?php
        }
        deactivate_plugins(plugin_basename(__FILE__));
    }
});

/**
 * Instantiate our Beaver Builder module classes.
 *
 * @since    1.0.0
 */
add_action('init', function () {
    if (!class_exists('CDLCJsonManifest')) {
        require_once CBB_CDLC_DIR . 'classes/class-json-manifest.php';
    }

    if (class_exists('FLBuilder')) {
        require_once CBB_CDLC_DIR . 'modules/cdlc-book-carousel/cdlc-book-carousel.php';
        require_once CBB_CDLC_DIR . 'modules/cdlc-call-to-action/cdlc-call-to-action.php';
        require_once CBB_CDLC_DIR . 'modules/cdlc-content-feed/cdlc-content-feed.php';
        require_once CBB_CDLC_DIR . 'modules/cdlc-icon-box/cdlc-icon-box.php';
        require_once CBB_CDLC_DIR . 'modules/cdlc-post-cards/cdlc-post-cards.php';
        require_once CBB_CDLC_DIR . 'modules/cdlc-quick-links/cdlc-quick-links.php';
        require_once CBB_CDLC_DIR . 'modules/cdlc-splash/cdlc-splash.php';
        require_once CBB_CDLC_DIR . 'modules/cdlc-splash-opening-hours/cdlc-splash-opening-hours.php';
        require_once CBB_CDLC_DIR . 'modules/cdlc-layout-card/cdlc-layout-card.php';
    }
});

/**
 * Allowlist for modules across Beaver Builder.
 *
 * @param boolean $enabled
 * @param object $instance
 */
add_filter('fl_builder_register_module', function ($enabled, $instance) {
    $allowlist = [
        'heading',
        'photo',
        'button',
        'rich-text',
        'html',
        'separator',
        'cdlc-book-carousel',
        'cdlc-call-to-action',
        'cdlc-content-feed',
        'cdlc-icon-box',
        'cdlc-layout-card',
        'cdlc-post-cards',
        'cdlc-quick-links',
        'cdlc-splash',
        'cdlc-splash-opening-hours',
        'unity-accordion',
        'unity-audio',
        'unity-blockquote',
        'unity-jump-link',
        'unity-modaal',
        'unity-modaal-gallery',
        'unity-numbers',
        // 'unity-posts',
        // 'unity-slider',
        'unity-tabs',
        'unity-video'
    ];

    if (!in_array($instance->slug, $allowlist)) {
        return false;
    }

    return $enabled;
}, 10, 2);
