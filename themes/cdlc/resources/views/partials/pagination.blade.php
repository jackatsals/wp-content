<div class="text-center my-6">
  @if (class_exists('FacetWP'))
    <nav class="facetwp-pager-wrapper pagination" id="facetwp-nav" role="navigation" aria-label="{{ __('Pagination Navigation', 'sage') }}">
      {!! do_shortcode('[facetwp pager="true"]') !!}
    </nav>
  @else
    @php
      the_posts_pagination([
        'prev_text'          => __('Previous', 'sage'),
        'next_text'          => __('Next', 'sage'),
        'before_page_number' => '<span class="screen-reader-text">Page</span>',
        'aria_label'         => __('Pagination', 'sage'),
      ]);
    @endphp
  @endif
</div>
