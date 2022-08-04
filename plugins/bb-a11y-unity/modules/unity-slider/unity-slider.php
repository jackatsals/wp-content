<?php

class UnitySliderModule extends FLBuilderModule {
    public function __construct()
    {
        parent::__construct([
            'name'            => __( 'Slider', 'bb-a11y-unity' ),
            'description'     => __( '', 'bb-a11y-unity' ),
            'icon'            => 'slides.svg',
            'category'        => __( 'Accessible', 'bb-a11y-unity' ),
            'partial_refresh' => true,
            'dir'             => BB_A11Y_UNITY_DIR . 'modules/unity-slider/',
            'url'             => BB_A11Y_UNITY_URL . 'modules/unity-slider/',
        ]);

        /**
         * CSS
         */
        $this->add_css('slick', 'https://cdn.jsdelivr.net/npm/@accessible360/accessible-slick@1.0.1/slick/slick.min.css');
        $this->add_css('slick-a11y-theme', 'https://cdn.jsdelivr.net/npm/@accessible360/accessible-slick@1.0.1/slick/accessible-slick-theme.min.css');
        $this->add_css('unity-slider-css', Unity\A11Y\asset_path('styles/unity-slider.css'));

        /**
         * JS
         */
        $this->add_js('unity-slider-js', Unity\A11Y\asset_path('scripts/unity-slider.js'), ['jquery'], null, true);
    }

    /**
     * Return all items for a slider.
     *
     * @param  array $settings
     * @return array
     */
    public function getSliderItems($settings)
    {
        return $settings->add_slider_item;
    }
}

FLBuilder::register_module('UnitySliderModule', [
    'unity-slider-general' => [
        'title'    => __('General', 'bb-a11y-unity'),
        'sections' => [
            'general' => [
                'title'  => '',
                'fields' => [
                    'add_slider_item' => [
                        'type'         => 'form',
                        'label'        => __('Slider Item', ''),
                        'form'         => 'slider_item_form',
                        'preview_text' => 'slider_item_title',
                        'multiple'     => true,
                    ],
                ],
            ],
            'layout' => [
                'title' => '',
                'fields' => [
                    'slider_responsive' => [
                        'type'        => 'unit',
                        'label'       => __('Responsive Settings', ''),
                        'description' => '',
                        'responsive'  => [
                            'placeholder' => [
                                'default'    =>  3,
                                'medium'     =>  2,
                                'responsive' =>  1,
                            ],
                        ],
                    ],
                    'slider_item_images_size' => [
                        'type'    => 'photo-sizes',
                        'label'   => __('Images Size', ''),
                        'default' => 'medium',
                    ],
                ],
            ]
        ],
    ],
]);

FLBuilder::register_settings_form('slider_item_form', [
    'title' => __('Add Slider Item', ''),
    'tabs' => [
        'slider_item_general' => [
            'title' => __('General', ''),
            'sections' => [
                'content' => [
                    'title' => __('Item', ''),
                    'fields' => [
                        'slider_item_image' => [
                            'type'        => 'photo',
                            'label'       => __('Image', ''),
                            'help'        => __('Be sure to add alt text to your image!', ''),
                            'show_remove' => false,
                        ],
                    ],
                ],
            ],
        ],
    ],
]);
