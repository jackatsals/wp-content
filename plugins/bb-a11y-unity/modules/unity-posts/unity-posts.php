<?php

class UnityPostsModule extends FLBuilderModule {
    public function __construct()
    {
        parent::__construct([
            'name'            => __( 'Posts', 'bb-a11y-unity' ),
            'description'     => __( 'A feed of posts.', 'bb-a11y-unity' ),
            'icon'            => 'schedule.svg',
            'category'        => __( 'Accessible', 'bb-a11y-unity' ),
            'partial_refresh' => true,
            'dir'             => BB_A11Y_UNITY_DIR . 'modules/unity-posts/',
            'url'             => BB_A11Y_UNITY_URL . 'modules/unity-posts/',
        ]);

        /**
         * CSS
         */
        $this->add_css('unity-posts-css', Unity\A11Y\asset_path('styles/unity-posts.css'));
    }
}

FLBuilder::register_module('UnityPostsModule', [
    'unity-posts-general' => [
        'title'    => __( 'General', 'bb-a11y-unity' ),
        'sections' => [
            'general' => [
                'title'  => __('Options', ''),
                'fields' => [
                    'display_excerpt' => [
                        'type'    => 'button-group',
                        'label'   => __('Display Excerpt', ''),
                        'default' => 'show',
                        'options' => [
                            'show' => __('Show Excerpt', ''),
                            'hide' => __('Hide Excerpt', ''),
                        ],
                    ],
                    'display_author' => [
                        'type'    => 'button-group',
                        'label'   => __('Display Author', ''),
                        'default' => 'show',
                        'options' => [
                            'show' => __('Show Author', ''),
                            'hide' => __('Hide Author', ''),
                        ],
                    ],
                    'display_date' => [
                        'type'    => 'button-group',
                        'label'   => __('Display Date', ''),
                        'default' => 'show',
                        'options' => [
                            'show' => __('Show Date', ''),
                            'hide' => __('Hide Date', ''),
                        ],
                    ],
                    'display_thumbnail' => [
                        'type'    => 'button-group',
                        'label'   => __('Display Thumbnail', ''),
                        'default' => 'show',
                        'options' => [
                            'show' => __('Show Thumbnail', ''),
                            'hide' => __('Hide Thumbnail', ''),
                        ],
                    ],
                    'thumbnail_size' => [
                        'type'    => 'button-group',
                        'label'   => __('Thumbnail Size', ''),
                        'default' => 'thumbnail',
                        'options' => [
                            'full'      => __('Full', ''),
                            'large'     => __('Large', ''),
                            'medium'    => __('Medium', ''),
                            'thumbnail' => __('Thumbnail', ''),
                        ],
                    ],
                ],
            ],
        ],
    ],
    'settings' => [
        'title' => __( 'Loop Settings', '' ),
        'file'  => FL_BUILDER_DIR . 'includes/loop-settings.php',
    ],
]);
