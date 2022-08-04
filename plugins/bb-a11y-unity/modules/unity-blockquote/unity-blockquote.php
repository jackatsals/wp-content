<?php

class UnityBlockquoteModule extends FLBuilderModule {
    public function __construct() {
        parent::__construct([
            'name'        => __( 'Blockquote', 'bb-a11y-unity' ),
            'description' => __( 'A semantic blockquote module.', 'bb-a11y-unity' ),
            'icon'        => 'format-quote.svg',
            'category'    => __( 'Accessible', 'bb-a11y-unity' ),
            'dir'         => BB_A11Y_UNITY_DIR . 'modules/unity-blockquote/',
            'url'         => BB_A11Y_UNITY_URL . 'modules/unity-blockquote/',
        ]);

        /**
         * CSS
         */
        $this->add_css('unity-blockquote-css', Unity\A11Y\asset_path('styles/unity-blockquote.css'));
    }
}

FLBuilder::register_module('UnityBlockquoteModule', [
    'unity-blockquote-general' => [
        'title'    => __( 'General', 'bb-a11y-unity' ),
        'sections' => [
            'general' => [
                'title'  => '',
                'fields' => [
                    'blockquote' => [
                        'type'  => 'textarea',
                        'label' => __( 'Blockquote', 'bb-a11y-unity' ),
                        'rows'  => '6'
                    ],
                    'cite' => [
                        'type'  => 'text',
                        'label' => __( 'Cite', 'bb-a11y-unity' ),
                    ]
                ],
            ],
        ],
    ],
    'unity-blockquote-style' => [
        'title'    => __('Style', ''),
        'sections' => [
            'style'  => [
                'title'  => '',
                'fields' => [
                    'align' => [
                        'type'       => 'align',
                        'label'      => __('Align', ''),
                        'default'    => 'left',
                    ],
                    'accent_color' => [
                        'type'       => 'color',
                        'label'      => __('Accent Color', ''),
                        'show_reset' => true,
                    ],
                ],
            ],
        ],
    ],
]);
