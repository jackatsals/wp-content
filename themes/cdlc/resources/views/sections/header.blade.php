<header class="banner bg-white dark:bg-black">
  <div class="banner__utility dark:bg-neutral-800 dark:text-white py-1">
    <div class="container flex items-center justify-between">
      @if (shortcode_exists('open_text'))
        <div class="hidden md:inline-block">
          <span>{!! do_shortcode('[open_text]%if_open_today% Today’s hours: %hours_today% %end% %if_closed_today% Closed today. %end%[/open_text]') !!}</span>
        </div>
      @endif
      <div class="flex items-center">
        <div class="grid grid-cols-2 gap-1">
          <div class="dropdown">
            <button class="dropdown__toggle" aria-expanded="false" aria-haspopup="true">
              <svg class="svg-icon svg-icon-sm mr-3 fill-current dark:hidden" aria-hidden="true"><use xlink:href="#icon-theme-light" /></svg>
              <svg class="svg-icon svg-icon-sm mr-3 fill-current hidden dark:inline" aria-hidden="true"><use xlink:href="#icon-theme-dark" /></svg>
              {{ __('Switch Theme', 'sage') }}
            </button>
            <div class="dropdown__content">
              <a class="flex items-center mb-2 no-underline hover:underline" id="btn-light-theme" href="#">
                <svg class="svg-icon svg-icon-sm mr-3 fill-current" aria-hidden="true"><use xlink:href="#icon-theme-light" /></svg>
                {{ __('Light Theme', 'sage') }}
              </a>
              <a class="flex items-center no-underline hover:underline" id="btn-dark-theme" href="#">
                <svg class="svg-icon svg-icon-sm mr-3 fill-current" aria-hidden="true"><use xlink:href="#icon-theme-dark" /></svg>
                {{ __('Dark Theme', 'sage') }}
              </a>
            </div>
          </div>
          <div class="dropdown">
            <button class="dropdown__toggle bg-white dark:bg-black dark:border-[1px] flex items-center" aria-expanded="false" aria-haspopup="true">
              <svg class="svg-icon svg-icon-sm mr-3 stroke-current" aria-hidden="true"><use xlink:href="#icon-translate"/></svg><span>{{ __('Translate', 'sage') }}</span>
            </button>
            <div class="dropdown__content">
              <div id="google_translate_element"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="container flex items-center justify-between">
    <div class="p-4 -ml-4 max-w-xs">
      @if (has_custom_logo())
        {!! get_custom_logo() !!}
      @else
        <a href="{{ get_home_url('/') }}">
          <span class="font-bold">{!! $siteName !!}</span>
        </a>
      @endif
    </div>
    @if ($accountUrl = get_theme_mod('catalog_account_url'))
      <div class="hidden md:inline-block my-4">
        <a class="btn" href="{{ $accountUrl }}">{{ __('My Account', 'sage') }}</a>
      </div>
    @endif
    <button class="btn-menu-toggle inline-block md:hidden border-2 border-current py-2 px-3" id="menu-trigger" aria-label="{{ __('Menu', 'sage') }}" aria-expanded="false" aria-controls="menu-primary-navigation">
      <svg width="32" height="32" viewBox="0 0 100 100">
        <path class="line line--top" d="M 20,29.000046 H 80.000231 C 80.000231,29.000046 94.498839,28.817352 94.532987,66.711331 94.543142,77.980673 90.966081,81.670246 85.259173,81.668997 79.552261,81.667751 75.000211,74.999942 75.000211,74.999942 L 25.000021,25.000058" />
        <path class="line line--middle" d="M 20,50 H 80" />
        <path class="line line--bottom" d="M 20,70.999954 H 80.000231 C 80.000231,70.999954 94.498839,71.182648 94.532987,33.288669 94.543142,22.019327 90.966081,18.329754 85.259173,18.331003 79.552261,18.332249 75.000211,25.000058 75.000211,25.000058 L 25.000021,74.999942" />
      </svg>
    </button>
  </div>
  @if (has_nav_menu('primary_navigation'))
    <div class="nav-primary hidden md:block dark:bg-neutral-800 dark:text-white">
      <div class="container flex flex-col md:flex-row md:items-center justify-between">
        {!! wp_nav_menu([
          'theme_location'       => 'primary_navigation',
          'container'            => 'nav',
          'container_aria_label' => __('Primary Navigation', 'sage'),
          'container_class'      => 'nav',
          'depth'                => 2,
        ]) !!}
        <div class="dropdown">
          <button class="dropdown__toggle dropdown__toggle-search" aria-expanded="false" aria-pressed="false" aria-haspopup="true" aria-label="{{ __('Search', 'sage') }}">
            <svg class="open w-4" viewBox="0 0 515.558 515.558"><path class="fill-current" d="M378.344 332.78c25.37-34.645 40.545-77.2 40.545-123.333C418.889 93.963 324.928.002 209.444.002S0 93.963 0 209.447s93.961 209.445 209.445 209.445c46.133 0 88.692-15.177 123.337-40.547l137.212 137.212 45.564-45.564L378.344 332.78zm-168.899 21.667c-79.958 0-145-65.042-145-145s65.042-145 145-145 145 65.042 145 145-65.043 145-145 145z"/></svg>
            <svg class="close w-4" viewBox="0 0 298.667 298.667"><path class="fill-current" d="M298.667 30.187L268.48 0 149.333 119.147 30.187 0 0 30.187l119.147 119.146L0 268.48l30.187 30.187L149.333 179.52 268.48 298.667l30.187-30.187L179.52 149.333z"/></svg>
          </button>
          <div class="dropdown__content">
            <div class="mb-4">
              <p class="text-xl font-bold">{{ __('Search', 'sage') }}</p>
            </div>
            {!! get_search_form(false) !!}
          </div>
        </div>
      </div>
    </div>
  @endif
</header>

@if ($alertBar['enable'] && $alertBar['message'])
  <x-alert class="alert my-4 bg-transparent" type="warning" role="alert">
    <span class="text-red-700 dark:text-white text-lg font-bold uppercase mr-4">{{ __('Alert', 'sage') }}</span>
    <span class="text-black dark:text-white">{!! $alertBar['message'] !!}</span>
  </x-alert>
@endif
