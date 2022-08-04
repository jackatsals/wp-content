<?php

class CDLCCallToAction extends FLBuilderModule {
    public function __construct() {
        parent::__construct([
            'name'        => __( 'Call To Action', 'cdlc' ),
            'description' => __( 'A call to action.', 'cdlc' ),
            'icon'        => 'button.svg',
            'category'    => __( 'Custom', 'cdlc' ),
            'dir'         => CBB_CDLC_DIR . 'modules/cdlc-call-to-action/',
            'url'         => CBB_CDLC_DIR . 'modules/cdlc-call-to-action/',
        ]);

        /**
         * CSS
         */
        $this->add_css('cdlc-call-to-action-css', asset_path('styles/cdlc-call-to-action.css'));
    }
}

FLBuilder::register_module('CDLCCallToAction', [
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
                    'link_text' => [
                        'type'  => 'text',
                        'label' => __( 'Link Text', 'cdlc' ),
                    ],
                    'background_image' => [
                        'type'        => 'photo',
                        'label'       => __( 'Background Image', 'cldc' ),
                        'show_remove' => true,
                    ],
                ],
            ],
        ],
    ],
]);
