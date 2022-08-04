@extends('layouts.app')

@section('content')
  @include('partials.page-header')
  @include('partials.breadcrumbs')

  <div class="container max-w-[1100px]">
    @includeWhen(!is_paged(), 'partials.content-featured')
    @include('partials.facets')

    <div class="facetwp-template transition-opacity">
      @if (!have_posts())
        <x-alert type="warning">
          {!! __('Sorry, no results were found.', 'sage') !!}
        </x-alert>
      @endif

      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 post-card">
        @while (have_posts()) @php(the_post())
          @includeFirst(['partials.content-' . get_post_type(), 'partials.content'], [
            'context' => 'archive',
          ])
        @endwhile
      </div>
    </div>
  </div>
  @include('partials.pagination')
@endsection
