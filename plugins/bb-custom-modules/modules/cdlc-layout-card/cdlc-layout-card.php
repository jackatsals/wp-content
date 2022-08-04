<?php

class CDLCLayoutCard extends FLBuilderModule {
    public function __construct() {
        parent::__construct([
            'name'        => __( 'Layout Card', 'cdlc' ),
            'description' => __( 'Layout Card', 'cdlc' ),
            'icon'        => 'button.svg',
            'category'    => __( 'Custom', 'cdlc' ),
            'dir'         => CBB_CDLC_DIR . 'modules/cdlc-layout-card/',
            'url'         => CBB_CDLC_DIR . 'modules/cdlc-layout-card/',
        ]);

        /**
         * CSS
         */
        $this->add_css('cdlc-layout-card-css', asset_path('styles/cdlc-layout-card.css'));

         }
}

FLBuilder::register_module('CDLCLayoutCard', [
    'cdlc-layout-card-general' => [
        'title'    => __( 'General', 'cdlc' ),
        'sections' => [
            'content' => [
                'title'  => __( 'Content', 'cdlc' ),
                'fields' => [
                    'title' => [
                        'type' => 'text',
                        'label' => __( 'Title', 'cldc' ),
                    ],
                    'image' => [
                        'type' => 'photo',
                        'label' => __( 'Image', 'cdlc' ),
                    ],
                    'text' => [
                      'type' => 'textarea',
                      'label' => __( 'Text', 'cdlc'),
                    ],
                    'button_text' => [
                      'type' => 'text',
                      'label' => __('Button Text', 'cdlc'),
                    ],
                    'button_url' => [
                      'type' => 'link',
                      'label' => __('Button URL', 'cdlc'),
                    ],
                      'direction' => [
                        'type' => 'select',
                        'label' => __('Content direction', 'cdlc'),
                        'options' => [
                            'horizontal' => __( 'horizontal', 'cdlc' ),
                            'vertical' => __( 'vertical', 'cdlc' ),
                        ],
                    ],
                ],
            ],
        ],
    ],
]);
