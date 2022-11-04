<?php

namespace App;

use function Roots\bundle;

/**
 * Register the theme assets.
 *
 * @return void
 */
add_action('wp_enqueue_scripts', function () {
    bundle('app')->enqueue();

    /**
     * Inline theme customization styles.
     */
    $theme_primary_color = get_theme_mod('theme_primary_color') ?: '#000000';
    $theme_secondary_color = get_theme_mod('theme_secondary_color') ?: '#000000';
    $theme_primary_a11y_color = get_a11y_text_color($theme_primary_color);
    $theme_secondary_a11y_color = get_a11y_text_color($theme_secondary_color);


    $styles = "
        :root {
            --theme-primary-color: {$theme_primary_color};
            --theme-primary-a11y-color: {$theme_primary_a11y_color};
            --theme-secondary-color: {$theme_secondary_color};
            --theme-secondary-a11y-color: {$theme_secondary_a11y_color};
        }
    ";

    wp_register_style('theme-color-styles', false);
    wp_enqueue_style('theme-color-styles');
    wp_add_inline_style('theme-color-styles', $styles);

    /**
     * Add class to body to allow target for dark primary colors.
     */
    if ($theme_primary_a11y_color === '#FFFFFF') {
        add_filter('body_class', function ($classes) {
            if (!get_theme_mod('theme_logo_ignore_dark_mode')) {
                $classes[] = 'dark-primary-color';
            }

            return $classes;
        }, 10, 1);
    }

    wp_enqueue_script('google-translate', 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit', [], null, true);
    wp_add_inline_script('google-translate', 'function googleTranslateElementInit(){new google.translate.TranslateElement({pageLanguage:"en"},"google_translate_element")}');
}, 100);

/**
 * Register login assets.
 *
 * @return void
 */
add_action('login_enqueue_scripts', function () {
    /**
     * Inline theme customization styles.
     */
    $theme_primary_color = get_theme_mod('theme_primary_color') ?: '#000000';
    $theme_primary_a11y_color = get_a11y_text_color($theme_primary_color);

    $styles = "
        :root {
            --theme-primary-color: {$theme_primary_color};
            --theme-primary-a11y-color: {$theme_primary_a11y_color};
        }
    ";

    wp_register_style('theme-color-styles', false);
    wp_enqueue_style('theme-color-styles');
    wp_add_inline_style('theme-color-styles', $styles);

    bundle('login')->enqueue();
}, 100);

/**
 * Register the initial theme setup.
 *
 * @return void
 */
add_action('after_setup_theme', function () {
    /**
     * Enable features from the Soil plugin if activated.
     * @link https://roots.io/plugins/soil/
     */
    add_theme_support('soil', [
        'clean-up',
        'nav-walker',
        'nice-search',
        'relative-urls'
    ]);

    /**
     * Disable full-site editing support.
     *
     * @link https://wptavern.com/gutenberg-10-5-embeds-pdfs-adds-verse-block-color-options-and-introduces-new-patterns
     */
    remove_theme_support('block-templates');

    /**
     * Register the navigation menus.
     * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
     */
    register_nav_menus([
        'primary_navigation' => __('Primary Navigation', 'sage'),
        'utility_navigation' => __('Utility Navigation', 'sage'),
        'social_links'       => __('Social Links', 'sage'),
    ]);

    /**
     * Disable the default block patterns.
     * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/#disabling-the-default-block-patterns
     */
    remove_theme_support('core-block-patterns');

    /**
     * Enable plugins to manage the document title.
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
     */
    add_theme_support('title-tag');

    /**
     * Enable post thumbnail support.
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    /**
     * Enable responsive embed support.
     * @link https://wordpress.org/gutenberg/handbook/designers-developers/developers/themes/theme-support/#responsive-embedded-content
     */
    add_theme_support('responsive-embeds');

    /**
     * Enable HTML5 markup support.
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
     */
    add_theme_support('html5', [
        'caption',
        'comment-form',
        'comment-list',
        'gallery',
        'search-form',
        'script',
        'style'
    ]);

    /**
     * Enable selective refresh for widgets in customizer.
     * @link https://developer.wordpress.org/themes/advanced-topics/customizer-api/#theme-support-in-sidebars
     */
    add_theme_support('customize-selective-refresh-widgets');

    /**
     * Enable custom logo support.
     * @link https://developer.wordpress.org/themes/functionality/custom-logo/#adding-custom-logo-support-to-your-theme
     */
    add_theme_support('custom-logo');
}, 20);

/**
 * Register the theme sidebars.
 *
 * @return void
 */
add_action('widgets_init', function () {
    $config = [
        'before_widget' => '<section class="widget %1$s %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ];

    register_sidebar([
        'name' => __('Primary', 'sage'),
        'id'   => 'sidebar-primary',
    ] + $config);
});
