<?php

class UnityJumpLinkModule extends FLBuilderModule {
    public function __construct()
    {
        parent::__construct([
            'name'        => __( 'Jump Link', 'bb-a11y-unity' ),
            'description' => __( 'A better jump link with accessible focus management.', 'bb-a11y-unity' ),
            'icon'        => 'button.svg',
            'category'    => __( 'Accessible', 'bb-a11y-unity' ),
            'dir'         => BB_A11Y_UNITY_DIR . 'modules/unity-jump-link/',
            'url'         => BB_A11Y_UNITY_URL . 'modules/unity-jump-link/',
        ]);

        /**
         * CSS
         */
        $this->add_css('unity-jump-link-css', Unity\A11Y\asset_path('styles/unity-jump-link.css'));

        /**
         * JS
         */
        $this->add_js('unity-jump-link-js', Unity\A11Y\asset_path('scripts/unity-jump-link.js'), ['jquery'], null, true);
    }
}

FLBuilder::register_module('UnityJumpLinkModule', [
    'unity-jump-link-general' => [
        'title'    => __( 'General', 'bb-a11y-unity' ),
        'sections' => [
            'content' => [
                'title'  => __( 'Content', 'bb-a11y-unity' ),
                'fields' => [
                    'cta_text' => [
                        'type'  => 'text',
                        'label' => __( 'CTA Text', 'bb-a11y-unity' ),
                    ],
                    'cta_link' => [
                        'type'  => 'link',
                        'label' => __( 'CTA Link', 'bb-a11y-unity' ),
                    ]
                ],
            ],
        ],
    ],
]);
