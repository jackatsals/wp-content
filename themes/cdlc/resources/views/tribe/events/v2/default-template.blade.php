@php
  use Tribe\Events\Views\V2\Template_Bootstrap;
@endphp

@extends('layouts.app')

@section('content')
  @include('partials.page-header')
  @include('partials.breadcrumbs')
  @include('partials.events.view-nav')
  {!! tribe(Template_Bootstrap::class)->get_view_html(); !!}
  @include('partials.events.pagination')
@endsection
