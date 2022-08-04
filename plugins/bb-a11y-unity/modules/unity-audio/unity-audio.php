<?php

class UnityAudioModule extends FLBuilderModule {
    public function __construct()
    {
        parent::__construct([
            'name'            => __( 'Audio', 'bb-a11y-unity' ),
            'description'     => __( 'An accessible audio player powered by Plyr.', 'bb-a11y-unity' ),
            'icon'            => 'format-audio.svg',
            'category'        => __( 'Accessible', 'bb-a11y-unity' ),
            'partial_refresh' => false,
            'dir'             => BB_A11Y_UNITY_DIR . 'modules/unity-audio/',
            'url'             => BB_A11Y_UNITY_URL . 'modules/unity-audio/',
        ]);

        /**
         * CSS
         */
        $this->add_css('unity-audio-css-plyr', 'https://cdn.plyr.io/3.6.8/plyr.css');
        $this->add_css('unity-audio-css', Unity\A11Y\asset_path('styles/unity-audio.css'));

        /**
         * JS
         */
        $this->add_js('unity-audio-js', Unity\A11Y\asset_path('scripts/unity-audio.js'), [], null, true);
    }
}

FLBuilder::register_module('UnityAudioModule', [
    'unity-audio-general' => [
        'title'    => __( 'General', 'bb-a11y-unity' ),
        'sections' => [
            'general' => [
                'title'  => '',
                'fields' => [
                    'audio_mp3' => [
                        'type'        => 'text',
                        'label'       => __( 'MP3 URL', 'bb-a11y-unity' ),
                        'connections' => ['url'],
                    ],
                    'transcript_text' => [
                        'type' => 'editor',
                        'label' => __( 'Transcript Text', 'bb-a11y-unity' ),
                    ],
                ],
            ],
        ],
    ],
]);
