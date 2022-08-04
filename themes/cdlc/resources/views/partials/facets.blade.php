@if ($facets && class_exists('FacetWP'))
  <div class="bg-white dark:bg-neutral-800 p-8 mb-8">
    <div class="grid gap-4 lg:gap-12 md:grid-cols-2">
      @foreach ($facets as $facet)
        {!! do_shortcode("[facetwp facet='$facet']") !!}
      @endforeach
    </div>
    <div class="flex align-center justify-end mt-8">
      <button class="text-sm underline mr-4" onclick="FWP.reset()">{{ __('Reset', 'search') }}</button>
      <button class="btn btn--sm" onclick="FWP.refresh()">{{ __('Submit', 'search') }}</button>
    </div>
  </div>
@endif
