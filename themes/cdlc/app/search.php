<?php

namespace App;

/**
 * Perform a redirected search on an Encore-powered catalog.
 *
 * @param string $keyword
 * @return void
 */
function searchEncore($keyword) {
    $url = "https://catalog.uhls.org/iii/encore/search/C__S{$keyword}__Orightresult__U";

    wp_redirect($url);
    exit;
}

/**
 * Perform a redirected search on a Polaris-powered catalog.
 *
 * @param string $keyword
 * @return void
 */
function searchPolaris($keyword) {
    $url = get_theme_mod('catalog_search_endpoint_url') ?: 'https://pac.sals.edu/polaris/search/searchresults.aspx?ctx=1.1033.0.0.3&type=Keyword&by=KW&sort=MP&limit=TOM=*&query=&page=0';

    wp_redirect(add_query_arg([
        'term' => $keyword,
    ], $url));
    exit;
}

/**
 * Redirect external catalog searches.
 */
add_action('template_redirect', function () {
    if (is_search() && $_GET['type'] === 'catalog') {

        $keyword = get_search_query();

        if ($keyword && get_theme_mod('catalog_search_endpoint') === 'encore') {
            searchEncore($keyword);
        }

        if ($keyword && get_theme_mod('catalog_search_endpoint') === 'polaris') {
            searchPolaris($keyword);
        }
    }
});
