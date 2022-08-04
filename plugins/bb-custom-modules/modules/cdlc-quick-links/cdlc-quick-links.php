<?php

class CDLCQuickLinks extends FLBuilderModule {
    public function __construct() {
        parent::__construct([
            'name'        => __( 'Quick Links', 'cdlc' ),
            'description' => __( 'Description forthcoming...', 'cdlc' ),
            'icon'        => 'button.svg',
            'category'    => __( 'Custom', 'cdlc' ),
            'dir'         => CBB_CDLC_DIR . 'modules/cdlc-quick-links/',
            'url'         => CBB_CDLC_DIR . 'modules/cdlc-quick-links/',
        ]);

        /**
         * CSS
         */
        $this->add_css('cdlc-quick-links-css', asset_path('styles/cdlc-quick-links.css'));
    }
}

FLBuilder::register_module('CDLCQuickLinks', [
    'cdlc-quick-links-general' => [
        'title'    => __( 'General', 'cdlc' ),
        'sections' => [
            'content' => [
                'title'  => __( 'Content', 'cdlc' ),
                'fields' => [
                    'title' => [
                        'type'  => 'text',
                        'label' => __( 'Title', 'cdlc' ),
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
                ],
            ],
        ],
    ],
]);
