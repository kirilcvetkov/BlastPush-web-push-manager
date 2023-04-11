@php

$menus = config('menu');
$selected = reset($menus);

foreach ($menus as $wildcard => $menu) {
  if (request()->route()->named($wildcard . '*')) {
    $selected = $menu;
    break;
  }

  if (isset($menu['sub-menu'])) {
    foreach ($menu['sub-menu'] as $subWildcard => $sub) {
      if (request()->route()->named($subWildcard . '*')) {
        $selected = $sub;
        break;
      }
    }
  }
}

$title = $selected['title'];
$icon = $selected['icon'];
@endphp

<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white mr-2">
      <i class="{{ $icon }} mdi-18px"></i>
    </span>
    {{ $pageTitle ?? $title ?? ucwords(join(" ", explode(".", \Route::current()->getName()))) }}
  </h3>

  <nav aria-label="breadcrumb">
    <ul class="breadcrumb">
      <li class="breadcrumb-item active" aria-current="page">
        {{ $pageTitle ?? ucwords(join(" / ", explode(".", request()->route()->getName()))) }}
        <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
      </li>
    </ul>
  </nav>
</div>
