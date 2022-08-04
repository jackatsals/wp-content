<?php

namespace App;

use Illuminate\Support\Str;

/**
 * Adjustments to menu attributes to support WCAG 2.0 recommendations
 * for flyout and dropdown menus.
 *
 * @link https://www.w3.org/WAI/tutorials/menus/flyout/
 */
add_filter('nav_menu_link_attributes', function ($atts, $item, $args, $depth) {
    if (in_array('menu-item-has-children', $item->classes) && $depth === 0) {
        $atts['href'] = '#';
        $atts['class'] = 'menu-link menu-toggle';
        $atts['aria-expanded'] = 'false';
        $atts['aria-haspopup'] = 'true';
    }

    if ($args->menu->slug === 'social-links' && $depth === 0) {
        $atts['class'] = 'inline-block';
    }

    return $atts;
}, 10, 4);

/**
 * Add SVG icons for social links menu.
 * Must have matching symbol in partials/icons.blade.php.
 *
 * @link https://developer.wordpress.org/reference/hooks/walker_nav_menu_start_el/
 */
add_filter('walker_nav_menu_start_el', function ($item_output, $item, $depth, $args) {
    if ($args->menu->slug === 'social-links' && $depth === 0) {
        $title = $item->title;
        $slug = Str::slug($title);

        $title_and_icon = "<span class='screen-reader-text'>{$title}</span><svg class='svg-icon fill-current'><use xlink:href='#icon-{$slug}'/></svg>";

        // Replace the regular title with a screen-reader friendly title and SVG icon.
        $item_output = Str::replaceFirst($title, $title_and_icon, $item_output);
    }

    return $item_output;
}, 10, 4);
