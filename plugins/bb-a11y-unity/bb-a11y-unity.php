<?php
/**
 * Plugin Name:       Accessible Modules for Beaver Builder
 * Plugin URI:        https://github.com/unitymakesus/bb-a11y-unity
 * Description:       A set of accessible-first modules for Beaver Builder.
 * Version:           1.0.0
 * Author:            Unity Web Agency
 * Author URI:        https://unitywebagency.com
 * GitHub Plugin URI: unitymakesus/bb-a11y-unity
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bb-a11y-unity
 * Domain Path:       /languages
 */

define('BB_A11Y_UNITY_VERSION', '1.0.0');
define('BB_A11Y_UNITY_DIR', plugin_dir_path(__FILE__));
define('BB_A11Y_UNITY_URL', plugins_url('/', __FILE__ ));

/**
 * Check for required WordPress plugins.
 *
 * @since 1.0.0
 */
add_action('admin_notices', function () {
    $requires = [];

    if (!is_plugin_active('bb-plugin/fl-builder.php')) {
        $required[] = [
            'link' => 'https://www.wpbeaverbuilder.com/',
            'name' => __('Beaver Builder', 'bb-a11y-unity'),
        ];
    }

    if (!empty($required)) {
        foreach ($required as $req) {
            ?>
            <div class="notice notice-error"><p>
                <?php printf(__('<b>%s Plugin</b>: <a href="%s" target="_blank" rel="noreferrer noopener">%s</a> must be installed and activated.', 'bb-a11y-unity'), __('Unity Accessible Modules', 'bb-a11y-unity'), $req['link'], $req['name']); ?>
            </p></div>
            <?php
        }
        deactivate_plugins(plugin_basename(__FILE__));
    }
});

/**
 * Instantiate our Beaver Builder module classes.
 *
 * @since 1.0.0
 */
add_action('init', function () {
    require __DIR__ . '/vendor/autoload.php';

    if (!function_exists('get_a11y_text_color')) {
        require_once BB_A11Y_UNITY_DIR . 'inc/helpers.php';
    }

    if (!class_exists('UnityJsonManifest')) {
        require_once BB_A11Y_UNITY_DIR . 'classes/UnityJsonManifest.php';
    }

    if (class_exists('FLBuilder')) {
        require_once BB_A11Y_UNITY_DIR . 'modules/unity-accordion/unity-accordion.php';
        require_once BB_A11Y_UNITY_DIR . 'modules/unity-audio/unity-audio.php';
        require_once BB_A11Y_UNITY_DIR . 'modules/unity-blockquote/unity-blockquote.php';
        require_once BB_A11Y_UNITY_DIR . 'modules/unity-jump-link/unity-jump-link.php';
        require_once BB_A11Y_UNITY_DIR . 'modules/unity-modaal/unity-modaal.php';
        require_once BB_A11Y_UNITY_DIR . 'modules/unity-modaal-gallery/unity-modaal-gallery.php';
        require_once BB_A11Y_UNITY_DIR . 'modules/unity-numbers/unity-numbers.php';
        require_once BB_A11Y_UNITY_DIR . 'modules/unity-posts/unity-posts.php';
        require_once BB_A11Y_UNITY_DIR . 'modules/unity-slider/unity-slider.php';
        require_once BB_A11Y_UNITY_DIR . 'modules/unity-tabs/unity-tabs.php';
        require_once BB_A11Y_UNITY_DIR . 'modules/unity-video/unity-video.php';
    }
});
