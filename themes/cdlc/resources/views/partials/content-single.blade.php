
<article @php(post_class())>
  <header class="page-header">
    <div class="container max-w-[1100px]">
      <h1 class="entry-title">{!! $title !!}</h1>
    </div>
  </header>
  @include('partials.breadcrumbs')
  <div class="container max-w-[1100px]">
    <div class="entry-content text-lg">
      @php(the_content())
      @include('partials.entry-meta')
    </div>
  </div>
  <footer>
    @include('partials.related-posts')
  </footer>
</article>
