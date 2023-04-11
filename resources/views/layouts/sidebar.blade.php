<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile">
      <a href="account.show" class="nav-link">
        <div class="nav-profile-image">
          <i class="mdi mdi-shield-account text-dark" alt="profile" style="font-size: 2.5rem;"></i>
          <span class="login-status online"></span>
        </div>
        <div class="nav-profile-text d-flex flex-column">
          <span class="font-weight-bold mb-2">
            {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
          </span>
          <span class="text-secondary text-small">{{ Auth::user()->email }}</span>
        </div>
      </a>
    </li>

    @if (Auth::user()->hasVerifiedEmail())
      @foreach (config('menu') as $wildcard => $menu)
        @if (isset($menu['menu']) && $menu['menu'] !== true)
          @continue;
        @endif

        @if ($menu['role'] == 'any' || auth()->user()->can($menu['role']))
          @if (isset($menu['sub-menu']))
            @php
              $active = request()->route()->named($wildcard . '*');
              if ($active != true) {
                foreach (array_keys($menu['sub-menu']) as $subWildcard) {
                  $active = request()->route()->named($subWildcard . '*');
                  if ($active) break;
                }
              }
            @endphp

            <li class="nav-item">
              <a class="nav-link {{ $active ? 'text-primary' : '' }}" data-toggle="collapse" href="#ui-basic" aria-expanded="true" aria-controls="ui-basic">
                <span class="menu-title">{{ $menu['title'] }}</span>
                <i class="menu-arrow {{ $active ? 'text-primary' : '' }}"></i>
                <i class="{{ $menu['icon'] }} menu-icon {{ $active ? 'text-primary' : '' }}"></i>
              </a>
              <div class="collapse {{ $active ? 'show' : '' }}" id="ui-basic" style="">
                <ul class="nav flex-column sub-menu">
                  @foreach ($menu['sub-menu'] as $subWildcard => $sub)
                    <li class="nav-item">
                      <a class="nav-link {{ request()->route()->named($subWildcard . '*') ? 'active font-weight-bold' : '' }}"
                        href="{{ $sub['route'] }}">
                        {{ $sub['title'] }}
                      </a>
                    </li>
                  @endforeach
                </ul>
              </div>
            </li>
          @else
            <li class="nav-item {{ request()->route()->named($wildcard . '*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ $menu['route'] }}">
                <span class="menu-title">{{ $menu['title'] }}</span>
                <i class="{{ $menu['icon'] }} menu-icon"></i>
              </a>
            </li>
          @endif
        @endif
      @endforeach
    @endif
  </ul>
</nav>
