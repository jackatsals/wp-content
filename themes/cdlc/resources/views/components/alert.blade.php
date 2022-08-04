<div {{ $attributes->merge(['class' => $type]) }}>
  <div class="relative container max-w-screen-lg font-mono flex items-center px-12 py-4 z-10 {{ $type }}">
    {!! $message ?? $slot !!}
  </div>
</div>
