<?php

namespace App;

/**
 * Modifications for FacetWP facets.
 */
add_filter('facetwp_facet_html', function ($output, $params) {
    if ($params['facet']['type'] === 'dropdown') {
        // Add an ID attribute and associated label element
        $label = '<label for="facetwp-'. $params['facet']['name'] .'">'. $params['facet']['label'] .'</label><select name="facetwp-'. $params['facet']['name'] .'" id="facetwp-'. $params['facet']['name'] .'"';
        $output = str_replace('<select', $label, $output);

        // Remove count from display values
        $output = preg_replace("/( \([0-9]+\))/m", '', $output);
    }

    if ($params['facet']['type'] === 'search') {
        // Add an ID attribute and associated label element
        $label = '<label for="facetwp-'. $params['facet']['name'] .'">'. $params['facet']['label'] .'</label><input type="text" class="facetwp-search name="facetwp-'. $params['facet']['name'] .'" id="facetwp-'. $params['facet']['name'] .'"';
        $output = str_replace('<input type="text" class="facetwp-search', $label, $output);
    }

    // Remove icons
    $output = str_replace('<i class="facetwp-icon"></i>', '', $output);

    return $output;
}, 10, 2);

/**
 * Modifications for FacetWP pagination.
 */
add_filter('facetwp_pager_html', function($output, $params) {
    // Add a valid href attribute
    $output = str_replace('class', 'href="javascript:void(0);" class', $output);

    // Add aria-current to active page
    $output = str_replace(' active"', '" aria-current="page"', $output);

    // Previous and next text
    $output = str_replace('&lt;&lt;', __('Previous', 'sage'), $output);
    $output = str_replace('&gt;&gt;', __('Next', 'sage'), $output);

    return $output;
}, 10, 2);
