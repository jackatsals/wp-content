<?php

class UnityNumbersModule extends FLBuilderModule {
    public function __construct() {
        parent::__construct([
            'name'            => __( 'Numbers', '' ),
            'description'     => __( '' ),
            'icon'            => 'chart-bar.svg',
            'category'        => __( 'Accessible', 'bb-a11y-unity' ),
            'partial-refresh' => true,
            'dir'             => BB_A11Y_UNITY_DIR . 'modules/unity-numbers/',
            'url'             => BB_A11Y_UNITY_DIR . 'modules/unity-numbers/',
        ]);

        /**
         * CSS
         */
        $this->add_css('unity-numbers-css', Unity\A11Y\asset_path('styles/unity-numbers.css'));

        /**
         * JS
         */
        $this->add_js('unity-numbers-js', Unity\A11Y\asset_path('scripts/unity-numbers.js'), [], '', true);
    }
}

FLBuilder::register_module('UnityNumbersModule', [
    'unity-numbers-general' => [
        'title'    => __( 'General', '' ),
        'sections' => [
            'content' => [
                'title'  => '',
                'fields' => [
                    'numbers' => [
                        'type'         => 'form',
                        'label'        => __( 'Number', '' ),
                        'form'         => 'unity_numbers_form',
                        'preview_text' => '',
                        'multiple'     => true,
                    ],
                ],
            ],
        ],
    ],
    'unity-numbers-style' => [
        'title' => __('Style', ''),
        'sections' => [
            'style' => [
                'title'  => '',
                'fields' => [
                    'align' => [
                        'type'    => 'align',
                        'label'   => __('Align', ''),
                        'default' => 'left',
                        'preview' => [
                            'type'     => 'css',
                            'selector' => '.unity-numbers',
                            'property' => 'text-align',
                        ],
                    ],
                    'number_font_size' => [
                        'type'    => 'unit',
                        'label'   => __('Font Size', ''),
                        'units'   => ['px', 'vw', 'em', 'rem'],
                        'default' => 'px',
                        'preview' => [
                            'type'     => 'css',
                            'selector' => 'span.unity-numbers__count',
                            'property' => 'font-size',
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
FLBuilder::register_settings_form('unity_numbers_form', [
    'title' => __( 'Add Number', '' ),
    'tabs'  => [
        'general' => [
            'title'    => __( 'General', '' ),
            'sections' => [
                'content' => [
                    'title'  => '',
                    'fields' => [
                        'number' => [
                            'type'        => 'unit',
                            'label'       => __( 'Number', '' ),
                            'size'        => '1',
                            'default'     => '100',
                            'placeholder' => '100',
                            'connections' => ['custom_field'],
                        ],
                        'text' => [
                            'type' => 'text',
                            'label' => __( 'Text', '' ),
                        ],
                        'prefix' => [
                            'type' => 'text',
                            'label' => __( 'Prefix', '' ),
                        ],
                        'suffix' => [
                            'type' => 'text',
                            'label' => __( 'Suffix', '' ),
                        ],
                        'decimal_places' => [
                            'type'        => 'unit',
                            'label'       => __( 'Decimal Places', '' ),
                            'size'        => '1',
                            'default'     => '0',
                            'placeholder' => '1',
                            'connections' => ['custom_field'],
                        ],
                        'aria_label' => [
                            'type' => 'text',
                            'label' => __( 'Accessible Text', '' ),
                        ],
                    ],
                ],
            ],
        ],
    ],
]);
