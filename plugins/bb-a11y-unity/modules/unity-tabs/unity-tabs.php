<?php

use Spatie\Color\Hex;

class UnityTabsModule extends FLBuilderModule {
    public function __construct()
    {
        parent::__construct([
            'name'            => __( 'Tabs', 'bb-a11y-unity' ),
            'description'     => __( 'An accessible tabs pattern.', 'bb-a11y-unity' ),
            'icon'            => 'layout.svg',
            'category'        => __( 'Accessible', 'bb-a11y-unity' ),
            'partial_refresh' => false,
            'dir'             => BB_A11Y_UNITY_DIR . 'modules/unity-tabs/',
            'url'             => BB_A11Y_UNITY_URL . 'modules/unity-tabs/',
        ]);

        /**
         * CSS
         */
        $this->add_css('unity-tabs-css', Unity\A11Y\asset_path('styles/unity-tabs.css'));

        /**
         * JS
         */
        $this->add_js('unity-tabs-js', Unity\A11Y\asset_path('scripts/unity-tabs.js'), [], null, true);
    }

    /**
     * Retrieve attributes for tab.
     *
     * @param  int $index
     * @return array
     */
    public function get_tab_attributes($index)
    {
        return [
            'id'       => "{$this->node}-tab-{$index}",
            'controls' => "{$this->node}-panel-{$index}",
            'selected' => $index === 0 ? 'true' : 'false',
            'tabindex' => $index > 0 ? 'tabindex=-1' : '',
        ];
    }

    /**
     * Retrieve attributes for tabpanel.
     *
     * @param  int $index
     * @return array
     */
    public function get_tabpanel_attributes($index)
    {
        return [
            'id'         => "{$this->node}-panel-{$index}",
            'labelledby' => "{$this->node}-tab-{$index}",
            'hidden'     => $index > 0 ? 'hidden' : '',
        ];
    }

    /**
     * Retrieve the accent color selected in this module.
     *
     * @return string
     */
    public function get_module_accent_color()
    {
        return Hex::fromString('#' . $this->settings->accent_color);
    }
}

FLBuilder::register_module('UnityTabsModule', [
    'unity-tabs-general' => [
        'title'    => __( 'General', '' ),
        'sections' => [
            'content' => [
                'title'  => '',
                'fields' => [
                    'tabs' => [
                        'type'         => 'form',
                        'label'        => __( 'Tab', '' ),
                        'form'         => 'tabs_form',
                        'preview_text' => 'title',
                        'multiple'     => true,
                    ],
                    'aria_label' => [
                        'type'        => 'text',
                        'label'       => __( 'Label', '' ),
                        'description' => __( 'Provide an accessible text label to group these tabs. Screen readers will utilize this label.', '' ),
                    ],
                ],
            ],
        ],
    ],
    'unity-tabs-style' => [
        'title'    => __('Style', ''),
        'sections' => [
            'style' => [
                'title'  => '',
                'fields' => [
                    'aria_orientation' => [
                        'type'    => 'select',
                        'label'   => __('Orientation', ''),
                        'default' => 'horizontal',
                        'options' => [
                            'horizontal' => __('Horizontal', ''),
                            'vertical'   => __('Vertical', ''),
                        ],
                        'description' => __('Display the tabs in vertical or horizontal format.', ''),
                    ],
                    'accent_color' => [
                        'type'       => 'color',
                        'label'      => __('Accent Color', ''),
                        'help'       => __('By selecting the accent color, any text color will automatically update in order to meet WCAG 2.1 Level AA guidelines for color contrast ratios.', ''),
                        'show_reset' => true,
                        'show_alpha' => false,
                    ],
                ],
            ],
        ],
    ],
]);

/**
 * Register a settings form to use in the "form" field type above.
 */
FLBuilder::register_settings_form('tabs_form', [
    'title' => __( 'Add Tab', '' ),
    'tabs'  => [
        'general' => [
            'title'    => __( 'General', '' ),
            'sections' => [
                'content' => [
                    'title'  => '',
                    'fields' => [
                        'title' => [
                            'type'  => 'text',
                            'label' => __( 'Title', '' ),
                        ],
                        'icon' => [
                            'type'        => 'icon',
                            'label'       => __( 'Icon', '' ),
                            'show_remove' => true,
                        ],
                        'content' => [
                            'type'          => 'editor',
                            'label'         => __( 'Content', '' ),
                            'media_buttons' => false,
                            'wpautop'       => true,
                        ],
                    ],
                ],
            ],
        ],
    ],
]);
