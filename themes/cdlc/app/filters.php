<?php

namespace App;

/**
 * Remove archive title prefixes.
 *
 * @param  string  $title  The archive title from get_the_archive_title();
 * @return string          The cleaned title.
 */
add_filter('get_the_archive_title', function ($title) {
    return preg_replace('#^[\w\d\s]+:\s*#', '', strip_tags($title));
});

/**
 * Add classes to <body>.
 *
 * @param array $classes
 */
add_filter('body_class', function ($classes) {
    if (get_theme_mod('theme_logo_ignore_dark_mode')) {
        $classes[] = 'logo-ignore-dark-mode';
    }

    return $classes;
}, 10, 1);
