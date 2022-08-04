<?php

namespace App;

/**
 * Remove the title completely (we're adding it through a blade template).
 */
add_filter('tribe_events_single_event_title_html', '__return_false');

/**
 * Remove calendar links (we're re-adding them in another section of the single event page).
 */
add_filter('tec_views_v2_subscribe_link_gcal_visibility', '__return_false', 10);
add_filter('tec_views_v2_subscribe_link_ical_visibility', '__return_false', 10);

/**
 * Add a view more events link after the single event meta.
 */
add_filter('tribe_events_single_event_after_the_meta', function () {
    echo '<div class="back-to-calendar"><a class="btn" href='. tribe_get_events_link() . 'month' .'>'. __('View More Events', 'sage') .'</a></div>';
});

/**
 * Change heading to regular element in schedule details for semantics.
 */
add_filter('tribe_events_event_schedule_details', function ($schedule, $event_id, $before, $after) {
    return str_replace('h2', 'div', $schedule);
}, 10, 4);

/**
 * Style links like buttons (.btn).
 */
add_filter('tribe_events_ical_single_event_links', function ($calendar_links) {
    $calendar_links = str_replace('+', '', $calendar_links);
    $calendar_links = str_replace('tribe-events-button', 'btn', $calendar_links);

    return $calendar_links;
}, 25, 1);

/**
 * Add an event registration button after the event details.
 */
add_action('tribe_events_single_meta_details_section_end', function () {
    if ($link = get_field('event_registration_url')) {
        echo '<dt>' . __('Register:', 'sage') . '</dt>';
        echo '<dd><a class="btn" href="' . $link['url'] . '">' . $link['title'] . '</a></dd>';
    }

    echo '<dt>' . __('Calendar Links:', 'sage') . '</dt>';
    echo '<dd><a href="' . tribe_get_single_ical_link() . '">' . __('Add To iCalendar', 'sage') . '</a></dd>';
    echo '<dd><a href="' . tribe_get_gcal_link() . '">' . __('Add To Google Calendar', 'sage') . '</a></dd>';
});

/**
 * Add Tailwind CSS prose (typography classes to content).
 */
add_filter('tribe_events_single_event_before_the_content', function () {
    echo '<div class="text-lg prose prose-theme dark:prose-invert">';
});
add_filter('tribe_events_single_event_after_the_content', function () {
    echo '</div>';
});

/**
 * Custom prev month link.
 */
function get_events_prev_month_link() {
    $url = tribe_get_previous_month_link();
    $text = tribe_get_previous_month_text();
    $date = \Tribe__Events__Main::instance()->previousMonth(tribe_get_month_view_date());

    return '<div class="text-center"><a class="btn" data-month="' . $date . '" href="' . $url . '" rel="prev"><span aria-hidden="true">&laquo;</span> Events in ' . $text . '</a></div>';
}

/**
 * Custom next month link.
 */
function get_events_next_month_link() {
    $url = tribe_get_next_month_link();
    $text = tribe_get_next_month_text();
    $date = \Tribe__Events__Main::instance()->nextMonth(tribe_get_month_view_date());

    return '<div class="text-center"><a class="btn" data-month="' . $date . '" href="' . $url . '" rel="next">Events in ' . $text . ' <span aria-hidden="true">&raquo;</span></a></div>';
}
