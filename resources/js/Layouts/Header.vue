<template>
  <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
      <a class="navbar-brand brand-logo text-gradient-primary" href="/">
        BlastPush <i class="mdi mdi-rocket mdi-logo"></i>
      </a>
      <a class="navbar-brand brand-logo-mini" href="/">
        <i class="mdi mdi-rocket mdi-logo text-gradient-primary"></i></a>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-stretch">
      <button class="navbar-toggler" type="button" @click.prevent="toggleSidebar">
        <span class="mdi mdi-menu"></span>
      </button>
      <div class="search-field d-none d-md-block">
        <form class="d-flex align-items-center h-100" action="#">
          <div class="input-group">
            <div class="input-group-prepend bg-transparent">
              <i class="input-group-text border-0 mdi mdi-magnify"></i>
            </div>
            <input type="text" class="form-control bg-transparent border-0" placeholder="Search">
          </div>
        </form>
      </div>
      <ul class="navbar-nav navbar-nav-right">
        <li class="nav-item nav-profile dropdown">
          <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
            <div class="nav-profile-img">
              <img :src="$page.user.profile_photo_url" :alt="$page.user.name" />
              <span class="availability-status online"></span>
            </div>
          </a>
          <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
            <inertia-link class="dropdown-item" :href="route('profile.show')">
              <i class="mdi mdi-shield-account mr-2 text-success"></i> Profile
            </inertia-link>
            <a class="dropdown-item" href="#">
              <i class="mdi mdi-view-list mr-2 text-success"></i> Activity Log
            </a>
            <div class="dropdown-divider"></div>
            <inertia-link class="dropdown-item" href="#" @click.prevent="logout">
              <i class="mdi mdi-power mr-2 text-danger"></i> Logout
            </inertia-link>
          </div>
        </li>
        <li class="nav-item d-none d-lg-block full-screen-link">
          <jet-dropdown-link href="#" icon="mdi-fullscreen" id="fullscreen-button"></jet-dropdown-link>
        </li>
        <li class="nav-item nav-logout d-none d-lg-block">
          <jet-dropdown-link href="#" icon="mdi-power" @click.native.prevent="logout" />
        </li>
      </ul>
      <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" @click.prevent="toggleSidebarActive">
        <span class="mdi mdi-menu"></span>
      </button>
    </div>
  </nav>
</template>

<script>
  import JetApplicationLogo from './../Jetstream/ApplicationLogo'
  import JetApplicationMark from './../Jetstream/ApplicationMark'
  import JetDropdown from './../Jetstream/Dropdown'
  import JetDropdownLink from './../Jetstream/DropdownLink'
  import JetNavLink from './../Jetstream/NavLink'
  import JetResponsiveNavLink from './../Jetstream/ResponsiveNavLink'

  export default {
    components: {
      JetApplicationLogo,
      JetApplicationMark,
      JetDropdown,
      JetDropdownLink,
      JetNavLink,
      JetResponsiveNavLink,
    },

    data() {
      return {
        showingNavigationDropdown: false,
        sidebarIconsOnly: true,
        sidebarActive: false,
      }
    },

    methods: {
      switchToTeam(team) {
        this.$inertia.put(route('current-team.update'), {
          'team_id': team.id
        }, {
          preserveState: false
        })
      },

      toggleSidebar() {
        this.$page.sidebarIconsOnly = ! this.$page.sidebarIconsOnly;
        let sidebarIconsOnly = this.$page.sidebarIconsOnly ? 1 : 0;
        this.$cookies.set('sidebarIconsOnly', sidebarIconsOnly, '1y'); // 1 year
      },

      toggleSidebarActive() {
        this.sidebarActive = ! this.sidebarActive;
        this.$page.sidebarActive = this.sidebarActive;
        console.log(this.$page.sidebarActive);
      },

      logout() {
        axios.post(route('logout').url()).then(response => {
          window.location = '/';
        })
      },
    },

    computed: {
      path() {
        return window.location.pathname
      }
    }
  }
</script>
