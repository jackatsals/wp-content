<a class="sr-only btn btn-skippy" href="#main">
  {{ __('Skip to content') }}
</a>

@include('sections.header')

<main id="main" class="main">
  @yield('content')
</main>

@include('sections.footer')
@include('partials.icons')
