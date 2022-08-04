@if (is_post_type_archive('tribe_events'))
  <div class="container max-w-[1100px]">
    <div class="tribe-events-view-links">
      <div class="dropdown">
        <button class="dropdown__toggle bg-white dark:bg-black dark:border-[1px] flex items-center" aria-expanded="false" aria-haspopup="true">
          <svg class="svg-icon svg-icon-sm mr-3 fill-current" aria-hidden="true"><use xlink:href="#icon-calendar"/></svg><span>{{ __('Calendar Views', 'sage') }}</span>
        </button>
        <div class="dropdown__content">
          <a class="flex items-center mb-2 no-underline hover:underline" href="{{ tribe_get_events_link() . 'list' }}">List View</a>
          <a class="flex items-center no-underline hover:underline" href="{{ tribe_get_events_link() . 'month' }}">Month View</a>
        </div>
      </div>
    </div>
    @if (tribe_is_month())
      <h2 class="mb-8">{{ tribe_get_events_title() }}</h2>
    @endif
  </div>
@endif
