<?php

class CDLCSplash extends FLBuilderModule {
    public function __construct() {
        parent::__construct([
            'name'        => __( 'Splash', 'cdlc' ),
            'description' => __( 'Homepage splash section.', 'cdlc' ),
            'icon'        => 'button.svg',
            'category'    => __( 'Homepage', 'cdlc' ),
            'dir'         => CBB_CDLC_DIR . 'modules/cdlc-splash/',
            'url'         => CBB_CDLC_DIR . 'modules/cdlc-splash/',
        ]);

        /**
         * CSS
         */
        $this->add_css('cdlc-splash-css', asset_path('styles/cdlc-splash.css'));
    }

    /**
     * Retrieve some posts for the module.
     *
     * @param string $post_type
     * @return \WP_Query
     */
    public function queryPosts($post_type)
    {
        if ($post_type === 'tribe_events') {
            return tribe_get_events([
                'posts_per_page' => 3,
                'no_found_rows'  => true,
                'start_date'     => 'now',
            ], true);
        }

        return new \WP_Query([
            'post_type'      => 'post',
            'posts_per_page' => 3,
            'no_found_rows'  => true,
        ]);
    }
}

FLBuilder::register_module('CDLCSplash', [
    'cdlc-splash-general' => [
        'title'    => __( 'General', 'cdlc' ),
        'sections' => [
            'main' => [
                'title'  => __( 'Main', 'cdlc' ),
                'fields' => [
                    'title' => [
                        'type' => 'text',
                        'label' => __( 'Title', 'cldc' ),
                    ],
                    'text' => [
                        'type' => 'textarea',
                        'label' => __( 'Text', 'cldc' ),
                    ],
                    'background_image' => [
                        'type' => 'photo',
                        'label' => __( 'Background Image', 'cdlc' ),
                    ],
                ],
            ],
            'quick_links' => [
                'title'  => __( 'Quick Links', 'cdlc' ),
                'fields' => [
                    'quick_links_title' => [
                        'type'  => 'text',
                        'label' => __( 'Title', 'cdlc' ),
                    ],
                    'quick_links_text' => [
                        'type'  => 'textarea',
                        'label' => __( 'Text', 'cdlc' ),
                    ],
                    'quick_links' => [
                        'type'         => 'form',
                        'label'        => __( 'Quick Links', 'cdlc' ),
                        'form'         => 'quick_link_form',
                        'preview_text' => 'text',
                        'multiple'     => true,
                    ],
                ],
            ],
            'side' => [
                'title'  => __( 'Side', 'cdlc' ),
                'fields' => [
                    'content_feed_title' => [
                        'type'  => 'text',
                        'label' => __( 'Title', 'cdlc' ),
                    ],
                    'content_feed_icon' => [
                        'type'        => 'icon',
                        'label'       => __( 'Icon', 'cdlc' ),
                        'show_remove' => true,
                    ],
                ],
            ],
        ],
    ],
    'cdlc-splash-settings' => [
        'title'    => __( 'Settings', 'cdlc' ),
        'sections' => [
            'settings' => [
                'title'  => __( 'Settings', 'cdlc' ),
                'fields' => [
                    'post_type' => [
                        'type'    => 'select',
                        'label'   => __( 'Post Type', 'cdlc' ),
                        'default' => 'post',
                        'options' => [
                            'post'         => __( 'Post', 'cdlc' ),
                            'tribe_events' => __( 'Event', 'cdlc' ),
                        ],
                    ],
                ],
            ],
        ],
    ],
]);

/**
 * Register a settings form to use in the "form" field type above.
 */
FLBuilder::register_settings_form('quick_link_form', [
    'title' => __( 'Add Quick Link', 'cdlc' ),
    'tabs' => [
        'general' => [
            'title'    => __( 'General', 'cdlc' ),
            'sections' => [
                'content' => [
                    'title'  => '',
                    'fields' => [
                        'text' => [
                            'type'  => 'text',
                            'label' => __( 'Text', 'cdlc' ),
                        ],
                        'icon' => [
                            'type'        => 'icon',
                            'label'       => __( 'Icon', 'cdlc' ),
                            'show_remove' => true,
                        ],
                        'link' => [
                            'type'  => 'link',
                            'label' => __( 'Link', 'cdlc' ),
                        ],
                    ],
                    'catalog_search_display' => [
                        'type' => 'select',
                        'label' => __( 'Catalog Search Display', 'cldc' ),
                        'options' => [
                            'false' => __( 'Hide Cataglog Search', 'cdlc' ),
                            'true' => __( 'Display Catalog Search', 'cdlc' ),
                        ],
                    ],
                ],
            ],
        ],
    ],
]);
