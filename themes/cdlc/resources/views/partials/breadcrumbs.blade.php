@if (!is_front_page() && function_exists('breadcrumb_trail'))
  <div class="breadcrumbs">
    <div class="container max-w-[1100px]">
      @php breadcrumb_trail() @endphp
    </div>
  </div>
@endif
