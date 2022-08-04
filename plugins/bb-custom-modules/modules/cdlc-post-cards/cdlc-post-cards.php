<?php

class CDLCPostCards extends FLBuilderModule {
    public function __construct() {
        parent::__construct([
            'name'        => __( 'Post Cards', 'cdlc' ),
            'description' => __( 'A feed of post cards.', 'cdlc' ),
            'icon'        => 'button.svg',
            'category'    => __( 'Custom', 'cdlc' ),
            'dir'         => CBB_CDLC_DIR . 'modules/cdlc-post-cards/',
            'url'         => CBB_CDLC_DIR . 'modules/cdlc-post-cards/',
        ]);

        /**
         * CSS
         */
        $this->add_css('cdlc-post-cards-css', asset_path('styles/cdlc-post-cards.css'));
    }
}

FLBuilder::register_module('CDLCPostCards', [
    'cdlc-post-cards-settings' => [
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
                    'posts_per_page' => [
                        'type'    => 'unit',
                        'label'   => __( 'How many posts do you want to show?', 'cdlc' ),
                        'default' => 4,
                    ],
                ],
            ],
        ],
    ],
]);
