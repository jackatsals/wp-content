@if ($showPublish || $showAuthor)
  <div class="meta-wrapper">
    @if ($showPublish)
      <div>
        <svg class="svg-icon-meta"><use xlink:href="#icon-calendar" /></svg>
        <span class="sr-only">{{ __('Published on', 'sage') }}</span>
        <time class="updated" datetime="{{ get_post_time('c', true) }}">
          {{ get_the_date() }}
        </time>
      </div>
    @endif
    @if ($showAuthor)
      <div>
        <svg class="svg-icon-meta"><use xlink:href="#icon-person" /></svg>
        <p class="byline author vcard">
          <span class="sr-only">{{ __('Published by', 'sage') }}</span>
          {{ get_the_author() }}
        </p>
      </div>
    @endif
  </div>
@endif
