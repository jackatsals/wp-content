<article {{ post_class('entry bg-white dark:bg-neutral-800 p-6 relative') }}>
  <div>
    @if (has_post_thumbnail())
      <div class="entry-thumbnail-wrapper">
        {{ the_post_thumbnail('medium_large') }}
        @if ($showCategories && $category = App\get_primary_category())
          <span>{{ $category->name }}</span>
        @endif
      </div>
    @endif
    <header>
      @if ($context === 'archive')
        <h2 class="entry-title">
          <a class="entry-link a11y-link-wrap" href="{{ get_permalink() }}">
            {!! $title !!}
          </a>
        </h2>
      @else
        <h3 class="entry-title">
          <a class="entry-link a11y-link-wrap" href="{{ get_permalink() }}">
            {!! $title !!}
          </a>
        </h3>
      @endif
    </header>
    {!! get_the_excerpt() !!}
  </div>
  @include('partials.entry-meta')
</article>
