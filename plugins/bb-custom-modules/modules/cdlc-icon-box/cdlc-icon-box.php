<?php

class CDLCIconBox extends FLBuilderModule {
    public function __construct() {
        parent::__construct([
            'name'        => __( 'Icon Box', 'cdlc' ),
            'description' => __( 'Box containing an icon and link.', 'cdlc' ),
            'icon'        => 'button.svg',
            'category'    => __( 'Custom', 'cdlc' ),
            'dir'         => CBB_CDLC_DIR . 'modules/cdlc-icon-box/',
            'url'         => CBB_CDLC_DIR . 'modules/cdlc-icon-box/',
        ]);

        /**
         * CSS
         */
        $this->add_css('cdlc-icon-box-css', asset_path('styles/cdlc-icon-box.css'));
    }
}

FLBuilder::register_module('CDLCIconBox', [
    'cdlc-icon-box-general' => [
        'title'    => __( 'General', 'cdlc' ),
        'sections' => [
            'content' => [
                'title'  => __( 'Content', 'cdlc' ),
                'fields' => [
                    'text' => [
                        'type'  => 'text',
                        'label' => __( 'Text', 'cldc' ),
                    ],
                    'link' => [
                        'type'  => 'link',
                        'label' => __( 'Link', 'cdlc' ),
                    ],
                    'icon' => [
                        'type'        => 'icon',
                        'label'       => __( 'Icon', 'cldc' ),
                        'show_remove' => true,
                    ],
                ],
            ],
        ],
    ],
]);
