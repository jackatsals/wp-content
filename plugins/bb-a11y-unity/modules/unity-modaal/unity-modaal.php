<?php

use Spatie\Color\Hex;

class UnityModaalModule extends FLBuilderModule {
    public function __construct()
    {
        parent::__construct([
            'name'        => __( 'Modaal', 'bb-a11y-unity' ),
            'description' => __( 'A button that opens an accessible Modaal dialog window.', 'bb-a11y-unity' ),
            'icon'        => 'button.svg',
            'category'    => __( 'Accessible', 'bb-a11y-unity' ),
            'dir'         => BB_A11Y_UNITY_DIR . 'modules/unity-modaal/',
            'url'         => BB_A11Y_UNITY_URL . 'modules/unity-modaal/',
        ]);

        /**
         * CSS
         */
        $this->add_css('modaal-css', 'https://cdnjs.cloudflare.com/ajax/libs/Modaal/0.4.4/css/modaal.min.css');
        $this->add_css('unity-modaal-css', Unity\A11Y\asset_path('styles/unity-modaal.css'));


        /**
         * JS
         */
        $this->add_js('modaal-js', 'https://cdnjs.cloudflare.com/ajax/libs/Modaal/0.4.4/js/modaal.min.js', ['jquery']);
        $this->add_js('unity-modaal-js', Unity\A11Y\asset_path('scripts/unity-modaal.js'), ['jquery', 'modaal-js'], null, true);
    }

    /**
     * Process a Hex color value into an RGB object for the accessible color calc() function in the module's CSS.
     *
     * @param string $str
     * @return Spatie\Color\Hex
     */
    public function processHexToRgb($str)
    {
        return Hex::fromString('#' . $str)->toRgb();
    }
}

FLBuilder::register_module( 'UnityModaalModule', [
    'unity-modaal-general' => [
        'title'    => __( 'General', 'bb-a11y-unity' ),
        'sections' => [
            'content' => [
                'title'  => __( 'Content', 'bb-a11y-unity' ),
                'fields' => [
                    'cta_text' => [
                        'type'  => 'text',
                        'label' => __( 'CTA Text', 'bb-a11y-unity' ),
                    ],
                    'cta_icon' => [
                        'type'        => 'icon',
                        'label'       => __( 'CTA Icon', 'bb-a11y-unity' ),
                        'show_remove' => true,
                    ],
                    'cta_target' => [
                        'type'  => 'text',
                        'help'  => 'Optional: provide an ID to target from the call to action button. This overrides the default auto-generated ID for the link’s href.',
                        'label' => __( 'CTA Target', 'bb-a11y-unity' ),
                    ],
                    'modaal_id' => [
                        'type'  => 'text',
                        'help'  => 'Optional: set an ID for targeting the inline content. This overrides the default auto-generated ID for targeting the inline content.',
                        'label' => __( 'Modaal ID', 'bb-a11y-unity' ),
                    ],
                    'modaal_content' => [
                        'type'          => 'editor',
                        'media_buttons' => false,
                        'wpautop'       => false,
                        'label'         => __( 'Modaal Content', 'bb-a11y-unity' ),
                    ],
                ],
            ],
        ],
    ],
    'unity-modaal-style' => [
        'title'    => __('Style', ''),
        'sections' => [
            'style'  => [
                'title'  => '',
                'fields' => [
                    'button_width' => [
                        'type'    => 'select',
                        'label'   => __('Button Width', ''),
                        'default' => 'auto',
                        'options' => [
                            'auto'   => __('Auto', ''),
                            'full'   => __('Full Width', ''),
                            'custom' => __('Custom', ''),
                        ],
                        'toggle' => [
                            'auto' => [
                                'fields' => ['button_align'],
                            ],
                            'full'   => [],
                            'custom' => [
                                'fields' => ['button_align', 'button_custom_width'],
                            ],
                        ],
                    ],
                    'button_custom_width' => [
                        'type'    => 'unit',
                        'label'   => __('Custom Width', ''),
                        'default' => '200',
                        'slider'  => [],
                        'px' => [
                            'min'  => 0,
                            'max'  => 1000,
                            'step' => 10,
                        ],
                        'units' => ['px', 'vw', '%'],
                        'preview' => [
                            'type'     => 'css',
                            'selector' => 'a.unity-modaal__button',
                            'property' => 'width',
                        ],
                    ],
                    'button_align' => [
                        'type'       => 'align',
                        'label'      => __('Button Align', ''),
                        'responsive' => true,
                        'default'    => 'left',
                    ],
                    'button_padding' => [
                        'type'       => 'dimension',
                        'label'      => __('Button Padding', ''),
                        'responsive' => true,
                        'units'      => ['px', 'em', 'rem'],
                        'preview'    => [
                            'type'     => 'css',
                            'selector' => 'a.unity-modaal__button',
                            'property' => 'padding',
                            'important' => true,
                        ],
                    ],
                    'button_background_color' => [
                        'type'       => 'color',
                        'label'      => __('Button Background Color', ''),
                        'help'       => __('By selecting the background color, the text color will automatically update in order to meet WCAG 2.1 Level AA guidelines for color contrast ratios.', ''),
                        'show_reset' => true,
                        'show_alpha' => false,
                    ],
                    'button_background_color_interact' => [
                        'type'       => 'color',
                        'label'      => __('Button Background Color (Hover / Focus)', ''),
                        'help'       => __('By selecting the background color, the text color will automatically update in order to meet WCAG 2.1 Level AA guidelines for color contrast ratios.', ''),
                        'show_reset' => true,
                        'show_alpha' => false,
                    ],
					'button_typography' => [
                        'type'       => 'typography',
                        'label'      => __('Button Typography', ''),
                        'responsive' => true,
                        'preview'    => [
                            'type'     => 'css',
                            'selector' => 'a.unity-modaal__button',
                        ],
                    ],
                    'button_border' => [
						'type'       => 'border',
						'label'      => __('Button Border', ''),
						'responsive' => true,
						'preview'    => [
							'type'      => 'css',
							'selector' => 'a.unity-modaal__button',
                            'property' => 'border',
							'important' => true,
                        ],
                    ],
                ],
            ],
        ],
    ],
] );
