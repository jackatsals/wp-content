<?php

class CDLCContentFeed extends FLBuilderModule {
    public function __construct() {
        parent::__construct([
            'name'        => __( 'Content Feed', 'cdlc' ),
            'description' => __( 'A feed of content.', 'cdlc' ),
            'icon'        => 'button.svg',
            'category'    => __( 'Custom', 'cdlc' ),
            'dir'         => CBB_CDLC_DIR . 'modules/cdlc-content-feed/',
            'url'         => CBB_CDLC_DIR . 'modules/cdlc-content-feed/',
        ]);

        /**
         * CSS
         */
        $this->add_css('cdlc-content-feed-css', asset_path('styles/cdlc-content-feed.css'));
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

FLBuilder::register_module('CDLCContentFeed', [
    'cdlc-content-feed-general' => [
        'title'    => __( 'General', 'cdlc' ),
        'sections' => [
            'header' => [
                'title'  => __( 'Header', 'cdlc' ),
                'fields' => [
                    'title' => [
                        'type' => 'text',
                        'label' => __( 'Title', 'cdlc' ),
                    ],
                    'icon' => [
                        'type' => 'icon',
                        'label' => __( 'Icon', 'cdlc' ),
                    ],
                ],
            ],
            'footer' => [
                'title'  => __( 'Footer', 'cdlc' ),
                'fields' => [
                    'link' => [
                        'type'  => 'link',
                        'label' => __( 'Link', 'cdlc' ),
                    ],
                    'link_text' => [
                        'type'  => 'text',
                        'label' => __( 'Link Text', 'cdlc' ),
                    ],
                ],
            ],
        ],
    ],
    'cdlc-content-feed-settings' => [
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
