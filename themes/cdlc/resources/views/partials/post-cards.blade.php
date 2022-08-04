@if ($cards->have_posts())
  @while ($cards->have_posts())
    @php $cards->the_post(); @endphp
    @includeFirst(['partials.content-' . get_post_type(), 'partials.content'], [
      'context' => 'default',
    ])
  @endwhile
@endif
@php wp_reset_postdata() @endphp
