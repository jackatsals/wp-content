<?php

namespace App;

use Illuminate\Support\Str;

/**
 * Customize Breadcrumbs plugin.
 */
add_filter('breadcrumb_trail_object', function () {
    global $post;

    $breadcrumbs = new \Breadcrumb_Trail([
        'container'     => 'nav',
        'before'        => '',
        'after'         => '',
        'browse_tag'    => 'div',
        'list_tag'      => 'ul',
        'item_tag'      => 'li',
        'show_on_front' => true,
        'network'       => false,
        'show_title'    => true,
        'show_browse'   => false,
        'labels'        => [],
        'post_taxonomy' => [],
        'echo'          => true,
    ]);

    $post_type = $post->post_type ?? '';

    if (is_single() && in_array($post_type, ['post', 'event'])) {
        $url = get_post_type_archive_link($post_type);

        $title = $post_type === 'post' ? get_the_title(get_option('page_for_posts', true)) : ucwords(Str::plural($post_type));

        array_splice($breadcrumbs->items, 1, 1, '<a href="' . $url . '">'. $title . '</a>');
    }

    return $breadcrumbs;
});
