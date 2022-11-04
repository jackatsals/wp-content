<?php

namespace App;

use \WP_Customize_Control;

/**
 * Override main logo on the login screen.
 */
add_filter('login_headertext', function ($login_header_text) {
    if (!$logo = get_theme_mod('custom_logo')) {
        return $login_header_text;
    }

    return '<img src="' . esc_url(wp_get_attachment_url($logo)) .'" alt="' . get_bloginfo('name') . '" />';
}, 10, 1);

/**
 * Remove default WP welcome panel.
 */
remove_action('welcome_panel', 'wp_welcome_panel');

/**
 * Add a custom welcome panel.
 */
add_action('welcome_panel', function () {
    ?>
    <style>
        .welcome-panel::before,
        .welcome-panel-content {
            display: none !important;
        }
    </style>
    <div class="welcome-panel-content-custom">
        <div class="welcome-panel-header">
            <h2><?php echo __('Welcome aboard!', 'sage'); ?></h2>
            <p><?php echo __('Here’s a quick checklist of items for getting started with your new, accessible WordPress website!', 'sage'); ?></p>
        </div>
        <div class="welcome-panel-column-container">
            <div class="welcome-panel-column">
                <div class="welcome-panel-icon-styles"></div>
                <div class="welcome-panel-column-content">
                    <h3><?php echo __('Customize your theme.', 'sage'); ?></h3>
                    <p><?php echo __('Add your library’s logo, choose theme colors, and setup your navigation menus.', 'sage'); ?></p>
                    <?php if (current_user_can('customize')) : ?>
                        <a class="load-customize hide-if-no-customize" href="<?php echo wp_customize_url(); ?>"><?php echo __('Open the Customizer', 'sage'); ?></a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="welcome-panel-column">
                <div class="welcome-panel-icon-layout"></div>
                <div class="welcome-panel-column-content">
                    <h3><?php echo __('Add library information.', 'sage'); ?></h3>
                    <p><?php echo __('Enter your library’s contact information and set your catalog search to your library’s PAC.', 'sage'); ?></p>
                    <?php if (current_user_can('customize')) : ?>
                        <a class="load-customize hide-if-no-customize" href="<?php echo wp_customize_url(); ?>"><?php echo __('Add your library’s information.', 'sage'); ?></a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="welcome-panel-column">
                <div class="welcome-panel-icon-pages"></div>
                <div class="welcome-panel-column-content">
                    <h3><?php echo __('Start building your site.', 'sage'); ?></h3>
                    <p><?php echo __('Use the drag-and-drop page builder to create your library’s one-of-a-kind homepage.', 'sage'); ?></p>
                    <a href="<?php echo esc_url(admin_url('post-new.php?post_type=page')); ?>"><?php echo __('Add a new page', 'sage'); ?></a>
                </div>
            </div>
        </div>
    </div>
    <?php
});

/**
 * Remove unnecessary dashboard widgets.
 */
add_action('wp_dashboard_setup', function () {
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');
    remove_meta_box('tribe_dashboard_widget', 'dashboard', 'normal');
});

/**
 * Theme customizer
 */
add_action('customize_register', function (\WP_Customize_Manager $wp_customize) {
    /**
     * Library Information
     */
    $wp_customize->add_section('library_info_settings', [
        'title'       => __('Library Information', 'sage'),
        'description' => __('This information about your library will display in various theme areas.', 'sage'),
        'priority'    => 30,
        'capability'  => 'edit_theme_options',
    ]);

    $wp_customize->add_setting('library_phone_number', [
        'default'   => '',
        'type'      => 'theme_mod',
        'transport' => 'refresh',
    ]);

    $wp_customize->add_control('library_phone_number', [
        'label'       => __('Phone Number', 'sage'),
        'description' => __('Your library’s primary phone number.', 'sage'),
        'section'     => 'library_info_settings',
        'settings'    => 'library_phone_number',
        'type'        => 'tel',
    ]);

    $wp_customize->add_setting('library_email_address', [
        'default'   => '',
        'type'      => 'theme_mod',
        'transport' => 'refresh',
    ]);

    $wp_customize->add_control('library_email_address', [
        'label'             => __('Email Address', 'sage'),
        'description'       => __('Your library’s primary email address.', 'sage'),
        'section'           => 'library_info_settings',
        'settings'          => 'library_email_address',
        'type'              => 'email',
        'sanitize_callback' => 'sanitize_email',
    ]);

    $wp_customize->add_setting('library_address', [
        'default'   => '',
        'type'      => 'theme_mod',
        'transport' => 'refresh',
    ]);

    $wp_customize->add_control('library_address', [
        'label'       => __('Address', 'sage'),
        'description' => __('Your library’s primary location.', 'sage'),
        'section'     => 'library_info_settings',
        'settings'    => 'library_address',
        'type'        => 'textarea',
    ]);

    /**
     * Theme Settings
     */
    $wp_customize->add_section('theme_settings', [
        'title'      => __('Theme Settings', 'sage'),
        'priority'   => 30,
        'capability' => 'edit_theme_options',
    ]);

    $wp_customize->add_setting('theme_primary_color', [
        'capability' => 'edit_theme_options',
        'default'    => '',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control($wp_customize, 'theme_primary_color', [
        'label'             => __('Theme Primary Color', 'sage'),
        'description'       => __('Color used for main site elements.', 'sage'),
        'section'           => 'theme_settings',
        'settings'          => 'theme_primary_color',
        'sanitize_callback' => 'sanitize_hex_color',
    ]));

    $wp_customize->add_setting('theme_secondary_color', [
        'capability' => 'edit_theme_options',
        'default'    => '',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control($wp_customize, 'theme_secondary_color', [
        'label'             => __('Theme Secondary Color', 'sage'),
        'description'       => __('Color used for site accents.', 'sage'),
        'section'           => 'theme_settings',
        'settings'          => 'theme_secondary_color',
        'sanitize_callback' => 'sanitize_hex_color',
    ]));

    $wp_customize->add_setting('theme_logo_ignore_dark_mode', [
        'default'   => '',
        'type'      => 'theme_mod',
        'transport' => 'refresh',
    ]);

    $wp_customize->add_control('theme_logo_ignore_dark_mode', [
        'label'       => __('Ignore logo adjust on dark backgrounds?', 'sage'),
        'description' => __('The dark mode feature will automatically covert your logo on dark backgrounds (and dark mode) to white (monochrome). Please check this box to opt out of this behavior.', 'sage'),
        'section'     => 'theme_settings',
        'settings'    => 'theme_logo_ignore_dark_mode',
        'type'        => 'checkbox',
    ]);

    /**
     * Blog Settings
     */
    $wp_customize->add_section('blog_settings', [
        'title'      => __('Blog Settings', 'sage'),
        'priority'   => 30,
        'capability' => 'edit_theme_options',
    ]);

    $wp_customize->add_setting('show_publish_date', [
        'default'   => true,
        'type'      => 'theme_mod',
        'transport' => 'refresh',
    ]);

    $wp_customize->add_control('show_publish_date', [
        'label'       => __('Show Publish Date', 'sage'),
        'description' => __('Toggles the display of publish dates on the blog.', 'sage'),
        'section'     => 'blog_settings',
        'settings'    => 'show_publish_date',
        'type'        => 'checkbox',
    ]);

    $wp_customize->add_setting('show_author_name', [
        'default'   => false,
        'type'      => 'theme_mod',
        'transport' => 'refresh',
    ]);

    $wp_customize->add_control('show_author_name', [
        'label'       => __('Show Author Name', 'sage'),
        'description' => __('Toggles the display of author names on the blog.', 'sage'),
        'section'     => 'blog_settings',
        'settings'    => 'show_author_name',
        'type'        => 'checkbox',
    ]);

    $wp_customize->add_setting('show_categories', [
        'default'   => true,
        'type'      => 'theme_mod',
        'transport' => 'refresh',
    ]);

    $wp_customize->add_control('show_categories', [
        'label'       => __('Show Category Labels', 'sage'),
        'description' => __('Toggles the display of categories on the blog.', 'sage'),
        'section'     => 'blog_settings',
        'settings'    => 'show_categories',
        'type'        => 'checkbox',
    ]);

    $wp_customize->add_setting('featured_post', [
        'default'   => '',
        'type'      => 'theme_mod',
        'transport' => 'refresh',
    ]);

    $wp_customize->add_control(new CDLC_Customize_Posts_Dropdown($wp_customize, 'featured_post', [
        'label'       => __('Featured Post', 'sage'),
        'description' => __('Choose a post to feature at the top of your blog page.', 'sage'),
        'section'     => 'blog_settings',
        'settings'    => 'featured_post',
    ]));

    /**
     * Catalog Settings
     */
    $wp_customize->add_section('catalog_settings', [
        'title'      => __('Catalog Settings', 'sage'),
        'priority'   => 30,
        'capability' => 'edit_theme_options',
    ]);

    $wp_customize->add_setting('catalog_search_endpoint', [
        'default'   => '',
        'type'      => 'theme_mod',
        'transport' => 'refresh',
    ]);

    $wp_customize->add_control('catalog_search_endpoint', [
        'label'       => __('Catalog Search Endpoint', 'sage'),
        'description' => __('Select which catalog the main search should hook up with.', 'sage'),
        'section'     => 'catalog_settings',
        'settings'    => 'catalog_search_endpoint',
        'type'        => 'select',
        'choices'     => [
            'encore'  => __('Encore (MVLS)', 'sage'),
            'polaris' => __('Polaris (SALS)', 'sage'),
        ],
    ]);

    $wp_customize->add_setting('catalog_search_endpoint_url', [
        'default'   => '',
        'type'      => 'theme_mod',
        'transport' => 'refresh',
    ]);

    $wp_customize->add_control('catalog_search_endpoint_url', [
        'label'             => __('Catalog Search Endpoint URL', 'sage'),
        'description'       => __('This sets the destination URL specifically for Polaris catalog searches.', 'sage'),
        'section'           => 'catalog_settings',
        'settings'          => 'catalog_search_endpoint_url',
        'type'              => 'url',
        'sanitize_callback' => '',
    ]);

    $wp_customize->add_setting('catalog_account_url', [
        'default'   => '',
        'type'      => 'theme_mod',
        'transport' => 'refresh',
    ]);

    $wp_customize->add_control('catalog_account_url', [
        'label'             => __('Account URL', 'sage'),
        'description'       => __('This sets the destination URL for the My Account call to action in the site header.', 'sage'),
        'section'           => 'catalog_settings',
        'settings'          => 'catalog_account_url',
        'type'              => 'url',
        'sanitize_callback' => '',
    ]);

    /**
     * Alert Bar
     */
    $wp_customize->add_section('alert_bar_settings', [
        'title'      => __('Alert Bar', 'sage'),
        'priority'   => 30,
        'capability' => 'edit_theme_options',
    ]);

    $wp_customize->add_setting('alert_bar_enable', [
        'default'   => '',
        'type'      => 'theme_mod',
        'transport' => 'refresh',
    ]);

    $wp_customize->add_control('alert_bar_enable', [
        'label'       => __('Enable the alert bar.', 'sage'),
        'section'     => 'alert_bar_settings',
        'settings'    => 'alert_bar_enable',
        'type'        => 'checkbox',
    ]);

    $wp_customize->add_setting('alert_bar_message', [
        'default'   => '',
        'type'      => 'theme_mod',
        'transport' => 'refresh',
    ]);

    $wp_customize->add_control('alert_bar_message', [
        'label'       => __('Message', 'sage'),
        'description' => __('Provide an important message here to display it across the top of every page of your website.', 'sage'),
        'section'     => 'alert_bar_settings',
        'settings'    => 'alert_bar_message',
        'type'        => 'textarea',
    ]);

    /**
     * Remove The Events Calendar customizations.
     */
    $wp_customize->remove_panel('tribe_customizer');
}, 25);

/**
 * Advanced Custom Fields.
 */
add_action('acf/init', function () {
    /**
     * Register custom fields.
     */
    if (function_exists('acf_add_local_field_group')) {
        acf_add_local_field_group(array(
            'key' => 'group_626adabd27251',
            'title' => 'Event Info',
            'fields' => array(
                array(
                    'key' => 'field_626adac000a15',
                    'label' => 'Event Registration URL',
                    'name' => 'event_registration_url',
                    'type' => 'link',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'return_format' => 'array',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'tribe_events',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'side',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
            'show_in_rest' => 0,
        ));
    }
});

if (!class_exists('WP_Customize_Control'))
    return NULL;
/**
 * Class to create a custom post control.
 */
class CDLC_Customize_Posts_Dropdown extends WP_Customize_Control
{
    private $posts = false;

    public function __construct($manager, $id, $args = [], $options = [])
    {
        $postargs = wp_parse_args($options, [
            'numberposts' => '-1',
        ]);
        $this->posts = get_posts($postargs);

        parent::__construct($manager, $id, $args);
    }

    public function render_content()
    {
        if (!empty($this->posts)) {
            ?>
                <label>
                    <div class="customize-post-dropdown"><?php echo esc_html($this->label); ?></div>
                    <em class="customize-post-dropdown-desc"><?php echo esc_html($this->description); ?></em>
                    <select name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>">
                    <?php
                        foreach ($this->posts as $post) {
                            printf('<option value="%s" %s>%s</option>', $post->ID, selected($this->value(), $post->ID, false), $post->post_title);
                        }
                    ?>
                    </select>
                </label>
            <?php
        }
    }
}
