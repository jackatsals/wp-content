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
