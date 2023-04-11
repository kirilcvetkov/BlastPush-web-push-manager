<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="description" content="BlastPush - comprehensive push notifications platform">
    <meta name="keyword" content="Push Notifications,Webpush,Notifications">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>{{ config('app.name', 'BlastPush') }}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/images/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ asset('assets/images/safari-pinned-tab.svg') }}" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    @yield('head.css')
  </head>
  <body @if ($_COOKIE['sidebar-icon-only'] ?? false) class="sidebar-icon-only" @endif>
    <div class="container-scroller">

  @guest

    @hasSection('errors')
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        @yield('errors')
      </div>
    @else
      @yield('content')
    @endif

  @else

    @include('layouts.header')

      <div class="container-fluid page-body-wrapper">

        @include('layouts.sidebar')

        <div class="main-panel">

          @hasSection('errors')
            @yield('errors')
          @else

            <div class="content-wrapper">
              @include('layouts.page-header', ['pageTitle' => $pageTitle ?? null])
              @include('layouts.alerts', ['warning' => $warning ?? null])
              @yield('content')
            </div>

          @endif

          @include('layouts.footer')

        </div>

      </div>

  @endguest

    </div>

    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}?v={{ config('constants.jsVersion') }}"></script>
    <script src="{{ asset('assets/js/off-canvas.js') }}?v={{ config('constants.jsVersion') }}"></script>
    <script src="{{ asset('assets/js/hoverable-collapse.js') }}?v={{ config('constants.jsVersion') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/js-cookie@rc/dist/js.cookie.min.js"></script>

    <script src="{{ asset('assets/js/misc.js') }}?v={{ config('constants.jsVersion') }}"></script>

    {{-- @yield('content.js') --}}

  </body>
</html>
