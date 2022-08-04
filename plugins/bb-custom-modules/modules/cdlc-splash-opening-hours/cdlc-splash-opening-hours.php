<?php

class CDLCSplashOpeningHours extends FLBuilderModule {
    public function __construct() {
        parent::__construct([
            'name'        => __( 'Splash: Opening Hours', 'cdlc' ),
            'description' => __( 'Homepage splash section with opening hours.', 'cdlc' ),
            'icon'        => 'button.svg',
            'category'    => __( 'Homepage', 'cdlc' ),
            'dir'         => CBB_CDLC_DIR . 'modules/cdlc-splash-opening-hours/',
            'url'         => CBB_CDLC_DIR . 'modules/cdlc-splash-opening-hours/',
        ]);

        /**
         * CSS
         */
        $this->add_css('cdlc-splash-opening-hours-css', asset_path('styles/cdlc-splash-opening-hours.css'));
    }
}

FLBuilder::register_module('CDLCSplashOpeningHours', [
    'cdlc-splash-opening-hours-general' => [
        'title'    => __( 'General', 'cdlc' ),
        'sections' => [
            'main' => [
                'title'  => __( 'Main', 'cdlc' ),
                'fields' => [
                    'title' => [
                        'type'        => 'text',
                        'label'       => __( 'Title', 'cldc' ),
                        'description' => __( 'Overrides the default text containing today’s hours.', 'cdlc' ),
                    ],
                    'text' => [
                        'type' => 'textarea',
                        'label' => __( 'Text', 'cldc' ),
                    ],
                    'background_image' => [
                        'type'        => 'photo',
                        'label'       => __( 'Background Image', 'cdlc' ),
                        'show_remove' => true,
                    ],
                ],
            ],
            'side' => [
                'title' => __( 'Hours (Side)', 'cdlc' ),
                'fields' => [
                    'hours_title' => [
                        'type'  => 'text',
                        'label' => __( 'Title', 'cdlc' ),
                    ],
                    'hours_icon' => [
                        'type'        => 'icon',
                        'label'       => __( 'Icon', 'cdlc' ),
                        'show_remove' => true,
                    ],
                    'hours_link' => [
                        'type'  => 'link',
                        'label' => __( 'Link', 'cdlc' ),
                    ],
                    'hours_link_text' => [
                        'type'  => 'text',
                        'label' => __( 'Link Text', 'cdlc' ),
                    ],
                ]
            ]
        ],
    ],
]);
