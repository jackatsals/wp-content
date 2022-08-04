<?php

class UnityVideoModule extends FLBuilderModule {
    public function __construct()
    {
        parent::__construct([
            'name'            => __( 'Video', 'bb-a11y-unity' ),
            'description'     => __( 'An accessible video player powered by Plyr.', 'bb-a11y-unity' ),
            'icon'            => 'format-video.svg',
            'category'        => __( 'Accessible', 'bb-a11y-unity' ),
            'partial_refresh' => false,
            'dir'             => BB_A11Y_UNITY_DIR . 'modules/unity-video/',
            'url'             => BB_A11Y_UNITY_URL . 'modules/unity-video/',
        ]);

        /**
         * CSS
         */
        $this->add_css('unity-video-css-plyr', 'https://cdn.plyr.io/3.6.9/plyr.css');
        $this->add_css('unity-video-css', Unity\A11Y\asset_path('styles/unity-video.css'));

        /**
         * JS
         */
        $this->add_js('unity-video-js', Unity\A11Y\asset_path('scripts/unity-video.js'), [], null, true);
    }
}

FLBuilder::register_module('UnityVideoModule', [
    'unity-video-general' => [
        'title'    => __( 'General', 'bb-a11y-unity' ),
        'sections' => [
            'general' => [
                'title'  => '',
                'fields' => [
                    'video_type' => [
                        'type'    => 'select',
                        'label'   => __( 'Video Type', '' ),
                        'default' => 'html5',
                        'options' => [
                            'html5'   => __( 'HTML5', '' ),
                            'youtube' => __( 'YouTube', '' ),
                            'vimeo'   => __( 'Vimeo', '' ),
                        ],
                        'toggle'  => [
                            'html5' => [
                                'fields'   => [
                                    'video_mp4',
                                    'video_webm',
                                    'video_captions_webvtt',
                                    'video_poster',
                                ],
                            ],
                            'youtube' => [
                                'fields' => ['video_embed'],
                            ],
                            'vimeo' => [
                                'fields' => ['video_embed'],
                            ],
                        ],
                    ],
                    'video_mp4' => [
                        'type'        => 'video',
                        'label'       => __( 'Video (MP4)', '' ),
                        'help'        => __( 'A video in the MP4 format. Most modern browsers support this format.', '' ),
                        'show_remove' => true,
                    ],
                    'video_webm' => [
                        'type'        => 'video',
                        'label'       => __( 'Video (WebM)', '' ),
                        'help'        => __( 'A video in the WebM format to use as fallback. This format is required to support browsers such as Firefox and Opera.', '' ),
                        'show_remove' => true,
                    ],
                    'video_poster' => [
                        'type'        => 'photo',
                        'show_remove' => true,
                        'label'       => __( 'Poster Image' , '' ) ,
                        'help'        => __( '', '' ),
                    ],
                    'video_embed' => [
                        'type'  => 'link',
                        'label' => __( 'Embed', '' ),
                    ],
                ],
            ],
        ],
    ],
]);
