<form role="search" method="get" class="search-form grid md:gap-8 md:grid-cols-12 mt-8 md:mt-0" action="{{ home_url('/') }}">
  <fieldset class="flex items-center md:col-span-5">
    <legend>{{ __('Choose one', 'sage') }}</legend>
    <div class="flex mr-8">
      <input type="radio" id="searchTypeWebsite" name="type" value="website" checked />
      <label for="searchTypeWebsite">{{ __('Website', 'sage') }}</label>
    </div>
    <div class="flex">
      <input type="radio" id="searchTypeCatalog" name="type" value="catalog" />
      <label for="searchTypeCatalog">{{ __('Catalog', 'sage') }}</label>
    </div>
  </fieldset>
  <div class="flex md:col-span-7 mt-4 md:mt-0">
    <div class="flex grow flex-col">
      <label for="search">{{ __('Keyword', 'sage') }}</label>
      <input type="text" name="s" id="search" class="h-10 text-black dark:text-white" value="{{ get_search_query(false) }}" autocomplete="off" required />
    </div>
    <button class="btn" type="submit" aria-label="{{ __('Submit', 'sage') }}">
      <svg class="w-4" viewBox="0 0 515.558 515.558"><path class="fill-current" d="M378.344 332.78c25.37-34.645 40.545-77.2 40.545-123.333C418.889 93.963 324.928.002 209.444.002S0 93.963 0 209.447s93.961 209.445 209.445 209.445c46.133 0 88.692-15.177 123.337-40.547l137.212 137.212 45.564-45.564L378.344 332.78zm-168.899 21.667c-79.958 0-145-65.042-145-145s65.042-145 145-145 145 65.042 145 145-65.043 145-145 145z"></path></svg>
    </button>
  </div>
</form>
