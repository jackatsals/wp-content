<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class EntryMeta extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'partials.entry-meta',
        'partials.content',
        'partials.content-featured',
    ];

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with()
    {
        return [
            'showAuthor'     => get_theme_mod('show_author_name'),
            'showCategories' => get_theme_mod('show_categories'),
            'showPublish'    => get_theme_mod('show_publish_date'),
        ];
    }
}
