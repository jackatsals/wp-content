<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class Blog extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'partials.facets',
        'partials.content-featured'
    ];

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with()
    {
        return [
            'facets'        => ['category', 'search'],
            'featured_post' => $this->featuredPost(),
        ];
    }

    /**
     * One featured post to rule them all.
     */
    public function featuredPost()
    {
        return get_field('blog_featured_post', 'option');
    }
}
