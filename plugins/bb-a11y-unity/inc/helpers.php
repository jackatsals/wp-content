<?php

namespace Unity\A11Y;

use Illuminate\Support\Arr;
use Spatie\Color\Hex;

/**
 * Process a Hex color value into an RGB object for the accessible color calc() function.
 *
 * @param string $str
 * @return Spatie\Color\Hex
 */
function process_hex_to_rgb($str) {
    return Hex::fromString('#' . $str)->toRgb();
}

/**
 * Determine a color's luminosity value.
 *
 * @param string $hex
 * @return int $luminosity
 *
 * @source https://www.w3.org/TR/WCAG20-TECHS/G18.html#G18-tests
 */
function get_color_luminosity($hex) {
    $rgb = Hex::fromString($hex)->toRgb();

    $r = $rgb->red() / 255;
    $g = $rgb->green() / 255;
    $b = $rgb->blue() / 255;

    if ($r <= 0.03928) {
        $r = $r / 12.92;
    } else {
        $r = pow((($r + 0.055) / 1.055), 2.4);
    }

    if ($g <= 0.03928) {
        $g = $g / 12.92;
    } else {
        $g = pow((($g + 0.055) / 1.055), 2.4);
    }

    if ($b <= 0.03928) {
        $b = $b / 12.92;
    } else {
        $b = pow((($b + 0.055) / 1.055), 2.4);
    }

    $luminosity = 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;

    return $luminosity;
}

/**
 * Tests color contrast against white (#FFF) for light or dark text adjustments.
 *
 * @param $color_1
 * @param $color_2
 *
 * @source https://www.w3.org/TR/WCAG20-TECHS/G18.html#G18-tests
 */
function get_a11y_text_color($color_1, $color_2 = '#FFFFFF') {
    $l1 = get_color_luminosity($color_1);
    $l2 = get_color_luminosity($color_2);

    if ($l1 > $l2) {
        $ratio = (($l1 + 0.05) / ($l2 + 0.05));
    } else {
        $ratio = (($l2 + 0.05) / ($l1 + 0.05));
    }

    // The text color should be light or dark.
    return ($ratio <= 4.5) ? '#000000' : '#FFFFFF';
}
