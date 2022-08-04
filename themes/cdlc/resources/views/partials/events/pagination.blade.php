@if (is_post_type_archive('tribe_events'))
  <div class="container max-w-[1100px] mb-8">
    <div class="tribe-events-view-nav grid gap-4 sm:flex flex-col sm:flex-row sm:items-center sm:justify-between w-full">
      @if (tribe_is_month())
        {!! App\get_events_prev_month_link() !!}
        {!! App\get_events_next_month_link() !!}
      @else
        <div class="text-center">
          @if (tribe_has_previous_event())
            <a class="btn btn-prev" href="{{ tribe_get_listview_prev_link() }}"><span aria-hidden="true">&laquo; </span>{{ __('Previous Events', 'sage') }}</a>
          @endif
        </div>
        <div class="text-center">
          @if (tribe_has_next_event())
            <a class="btn btn-next" href="{{ tribe_get_listview_next_link() }}">{{ __('Next Events', 'sage') }} <span aria-hidden="true">&raquo;</span></a>
          @endif
        </div>
      @endif
    </div>
  </div>
@endif
