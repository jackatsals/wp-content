<?php

namespace App;

add_shortcode('cdlc-post-cards', function ($atts) {
    ob_start();

    $template = 'partials/post-cards';
    $data = collect(get_body_class())->reduce(function ($data, $class) use ($template) {
        return apply_filters("sage/template/{$class}/data", $data, $template);
    }, []);

    $atts = shortcode_atts([
        'post_type'      => 'post',
        'posts_per_page' => 3,
    ], $atts, 'cdlc-post-cards');

    $cards = [];

    if ($atts['post_type'] === 'tribe_events') {
        $cards = tribe_get_events([
            'posts_per_page' => $atts['posts_per_page'],
            'no_found_rows'  => true,
            'start_date'     => 'now',
        ], true);
    } else {
        $cards = new \WP_Query([
            'post_type'      => 'post',
            'posts_per_page' => $atts['posts_per_page'],
            'no_found_rows'  => true,
        ]);
    }

    $data['cards'] = $cards;

    echo view($template, $data);

    return ob_get_clean();
});
