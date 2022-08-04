@if ($related_posts->have_posts())
  <div class="related-posts my-12">
    <div class="container max-w-[1100px]">
      <h2>{{ __('Related Posts', 'sage') }}</h2>
      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 pt-2">
        @while ($related_posts->have_posts())
          @php $related_posts->the_post(); @endphp
          @include('partials.content', [
            'context' => 'default',
          ])
        @endwhile
      </div>
    </div>
  </div>
@endif
@php wp_reset_postdata(); @endphp
