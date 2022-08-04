<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class App extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        '*',
    ];

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with()
    {
        return [
            'siteName'     => $this->siteName(),
            'phoneNumber'  => get_theme_mod('library_phone_number'),
            'email'        => get_theme_mod('library_email_address'),
            'address'      => get_theme_mod('library_address'),
            'alertBar'     => [
                'enable'  => get_theme_mod('alert_bar_enable'),
                'message' => get_theme_mod('alert_bar_message'),
            ],
            'isBuilder' => $this->isBuilder(),
        ];
    }

    /**
     * Returns the site name.
     *
     * @return string
     */
    public function siteName()
    {
        return get_bloginfo('name', 'display');
    }

    /**
     * Check to see if a page is a Beaver Builder page.
     *
     * @return boolean
     */
    public function isBuilder()
    {
        if (!class_exists('FLBuilderModel')) {
            return false;
        }

        return \FLBuilderModel::is_builder_enabled();
    }
}
