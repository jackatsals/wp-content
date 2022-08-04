@extends('layouts.app')

@section('content')
  @include('partials.page-header')

  <div class="container max-w-[1100px]">
    @if (! have_posts())
      <x-alert type="warning">
        {!! __('Sorry, no results were found.', 'sage') !!}
      </x-alert>
    @endif

    @while(have_posts()) @php(the_post())
      @include('partials.content-search')
    @endwhile

    {!! get_the_posts_navigation() !!}
  </div>
@endsection
