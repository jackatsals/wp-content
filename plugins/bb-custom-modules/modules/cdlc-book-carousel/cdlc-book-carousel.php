<?php

class CDLCBookCarousel extends FLBuilderModule {
    public function __construct() {
        parent::__construct([
            'name'        => __( 'Book Carousel', 'cdlc' ),
            'description' => __( 'A book carousel', 'cdlc' ),
            'icon'        => 'button.svg',
            'category'    => __( 'Custom', 'cdlc' ),
            'dir'         => CBB_CDLC_DIR . 'modules/cdlc-book-carousel/',
            'url'         => CBB_CDLC_DIR . 'modules/cdlc-book-carousel/',
        ]);

        /**
         * CSS
         */
        $this->add_css('cdlc-slick-core', 'https://cdn.jsdelivr.net/npm/@accessible360/accessible-slick@1.0.1/slick/slick.min.css');
        $this->add_css('cdlc-slick-theme', 'https://cdn.jsdelivr.net/npm/@accessible360/accessible-slick@1.0.1/slick/accessible-slick-theme.min.css');
        $this->add_css('cdlc-book-carousel-css', asset_path('styles/cdlc-book-carousel.css'));

        /**
         * JS
         */
        $this->add_js('cdlc-book-carousel-js', asset_path('scripts/cdlc-book-carousel.js'), ['jquery'], '', true);
    }

    /**
     * Helpful text for screen readers.
     *
     * @param object $book
     *
     * @return string
     */
    public function getBookScreenReaderText($book)
    {
        if (!empty($book->author)) {
            return $book->title . 'by' . $book->author;
        }

        return $book->title;
    }
}

FLBuilder::register_module('CDLCBookCarousel', [
    'cdlc-book-carousel-general' => [
        'title'    => __( 'General', 'cdlc' ),
        'sections' => [
            'content' => [
                'title'  => __( 'Content', 'cdlc' ),
                'fields' => [
                    'books' => [
                        'type'         => 'form',
                        'label'        => __( 'Books', 'cdlc' ),
                        'form'         => 'books_form',
                        'preview_text' => '',
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
FLBuilder::register_settings_form('books_form', [
    'title' => __( 'Add Book', 'cdlc' ),
    'tabs' => [
        'general' => [
            'title'    => __( 'General', 'cdlc' ),
            'sections' => [
                'content' => [
                    'title'  => '',
                    'fields' => [
                        'title' => [
                            'type'  => 'text',
                            'label' => __( 'Title', 'cdlc' ),
                        ],
                        'author' => [
                            'type'  => 'text',
                            'label' => __( 'Author', 'cdlc' ),
                        ],
                        'link' => [
                            'type'  => 'link',
                            'label' => __( 'Link', 'cdlc' ),
                        ],
                        'cover' => [
                            'type'        => 'photo',
                            'label'       => __( 'Book Cover', 'cdlc' ),
                            'show_remove' => true,
                        ],
                    ],
                ],
            ],
        ],
    ],
]);
