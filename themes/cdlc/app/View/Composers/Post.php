<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class Post extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'partials.page-header',
        'partials.content',
        'partials.content-*',
    ];

    /**
     * Data to be passed to view before rendering, but after merging.
     *
     * @return array
     */
    public function override()
    {
        return [
            'title' => $this->title(),
            'related_posts' => $this->relatedPosts(),
        ];
    }

    /**
     * Returns the post title.
     *
     * @return string
     */
    public function title()
    {
        if ($this->view->name() !== 'partials.page-header') {
            return get_the_title();
        }

        if (is_home()) {
            if ($home = get_option('page_for_posts', true)) {
                return get_the_title($home);
            }

            return __('Latest Posts', 'sage');
        }

        if (is_archive()) {
            return get_the_archive_title();
        }

        if (is_search()) {
            return sprintf(
                /* translators: %s is replaced with the search query */
                __('Search Results for “%s”', 'sage'),
                get_search_query()
            );
        }

        if (is_404()) {
            return __('Not Found', 'sage');
        }

        return get_the_title();
    }

    /**
     * Related posts (by category) on the single post template.
     */
    public function relatedPosts()
    {
        $post_id = get_the_ID();
        $terms = get_the_terms($post_id, 'category');

        if (empty($terms)) {
            $terms = [];
        }

        $term_list = wp_list_pluck($terms, 'slug');

        $related_args = [
            'post_type'      => 'post',
            'posts_per_page' => 3,
            'post_status'    => 'publish',
            'post__not_in'   => [$post_id],
            'orderby'        => 'rand',
            'tax_query'      => [
                [
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => $term_list,
                ],
            ],
        ];

        return new \WP_Query($related_args);
    }
}
