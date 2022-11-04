<footer class="content-info dark:bg-neutral-800 dark:text-white">
  <div class="content-info__main">
    <div class="container grid md:grid-cols-12 gap-12 lg:gap-5 text-base">
      <div class="col md:col-span-12 lg:col-span-4 lg:border-r-[1px] border-current">
        @if (has_custom_logo())
          <div class="max-w-[16rem] mb-12">{!! get_custom_logo() !!}</div>
        @endif
        <div class="max-w-[24rem]">
          {!! wp_nav_menu([
            'theme_location' => 'social_links',
            'container'      => 'nav',
            'depth'          => 1,
          ]) !!}
        </div>
      </div>
      <div class="col md:col-span-6 lg:col-span-4 md:border-r-[1px] border-current">
        @if ($phoneNumber)
          <div class="flex items-center mb-6">
            <svg class="svg-icon fill-current mr-6" aria-label="{{ __('Phone', 'sage') }}"><use xlink:href="#icon-phone" /></svg>
            <span>{{ $phoneNumber }}</span>
          </div>
        @endif
        @if ($email)
          <div class="flex items-center mb-6">
            <svg class="svg-icon fill-current mr-6" aria-label="{{ __('Email', 'sage') }}"><use xlink:href="#icon-email" /></svg>
            <a href="mailto:{{ $email }}">{{ $email }}</a>
          </div>
        @endif
        @if ($address)
          <div class="flex items-center">
            <svg class="svg-icon fill-current mr-6" aria-label="{{ __('Address', 'sage') }}"><use xlink:href="#icon-address" /></svg>
            <span>{!! wpautop($address) !!}</span>
          </div>
        @endif
      </div>
      <div class="col md:col-span-6 lg:col-span-4">
        @if (shortcode_exists('open'))
          <div class="flex items-start mb-6 text-left">
            <svg class="svg-icon inherit-stroke mr-6" aria-label="{{ __('Hours', 'sage') }}"><use xlink:href="#icon-clock" /></svg>
            {!! do_shortcode('[open]') !!}
          </div>
        @endif
      </div>
    </div>
  </div>
  <div class="content-info__copyright">
    <div class="container grid lg:grid-cols-12 gap-5 text-sm">
      <div class="text-center lg:text-left col-span-6">
        Copyright &copy; {!! current_time('Y') !!} {{ $siteName }}
      </div>
      <div class="text-center lg:text-right col-span-6">
        @if (has_nav_menu('utility_navigation'))
          <nav aria-label="{{ wp_get_nav_menu_name('utility_navigation') }}">
            {!! wp_nav_menu([
              'theme_location' => 'utility_navigation',
              'menu_class'     => 'nav',
              'depth'          => 1,
            ]) !!}
          </nav>
        @endif
      </div>
    </div>
  </div>
</footer>
