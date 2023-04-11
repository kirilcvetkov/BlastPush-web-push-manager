<template>
  <div class="container-scroller" :class="{'sidebar-icon-only': $page.sidebarIconsOnly}">
    <layout-header></layout-header>

    <div class="container-fluid page-body-wrapper">
      <layout-sidebar></layout-sidebar>

      <div class="main-panel">
        <!-- @hasSection('errors')
          @yield('errors')
        @else -->
          <div class="content-wrapper">
            <layout-page-header title="Dashbaord" icon="mdi-view-dashboard">
              <template #icon><slot name="icon"></slot></template>
              <template #title><slot name="title"></slot></template>
            </layout-page-header>

            <layout-alert v-for="(error, key) in $page.errors" :key="key" :message="error" type="alert-danger"></layout-alert>
            <layout-alert v-if="$page.flash.success" :message="$page.flash.success" type="alert-success"></layout-alert>
            <layout-alert v-if="$page.flash.error" :message="$page.flash.error" type="alert-danger"></layout-alert>

            <!-- Page Heading -->
            <header class="bg-white shadow">
              <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <slot name="header"></slot>
              </div>
            </header>

            <!-- Page Content -->
            <main>
              <slot></slot>
            </main>

            <!-- Modal Portal -->
            <portal-target name="modal" multiple>
            </portal-target>
          </div>
        <!-- @endif -->

        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
              <a href="https://slicksky.com">BlastPush</a> © 2020 slicksky.com. All rights reserved.
            </span>
            <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-muted">Hand-crafted &amp; made with
              <i class="mdi mdi-heart text-danger"></i> by BootstrapDash</span>
          </div>
        </footer>
      </div>
    </div>
  </div>
</template>

<script>
  import LayoutHeader from './Header'
  import LayoutSidebar from './Sidebar'
  import LayoutPageHeader from './PageHeader'
  import LayoutAlert from './Alert'

  import JetApplicationLogo from './../Jetstream/ApplicationLogo'
  import JetApplicationMark from './../Jetstream/ApplicationMark'
  import JetDropdown from './../Jetstream/Dropdown'
  import JetDropdownLink from './../Jetstream/DropdownLink'
  import JetNavLink from './../Jetstream/NavLink'
  import JetResponsiveNavLink from './../Jetstream/ResponsiveNavLink'

  export default {
    components: {
      LayoutHeader,
      LayoutSidebar,
      LayoutPageHeader,
      LayoutAlert,

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
        isModalShown: false,
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
