@if ($featured_post)
  @php
  global $post;
  $post = get_post($featured_post);
  setup_postdata($featured_post);
  @endphp
  <article {{ post_class('featured-post entry bg-white dark:bg-neutral-800 p-6 mb-12 relative grid gap-4 md:grid-cols-1 lg:grid-cols-2') }}>
    <div class="entry-summary">
      <header>
        <h2 class="entry-title mb-2">
          <a class="entry-link a11y-link-wrap" href="{{ get_permalink() }}">
            {{ the_title() }}
          </a>
        </h2>
        <div class="featured"> Featured </div>
      </header>
      {{ the_excerpt() }}
      @include('partials.entry-meta')
    </div>
    <div class="entry-thumbnail-wrapper">
      {{ the_post_thumbnail('medium_large') }}
      @if ($showCategories && $category = App\get_primary_category())
        <span>{{ $category->name }}</span>
      @endif
    </div>
  </article>
  @php wp_reset_postdata() @endphp
@endif
