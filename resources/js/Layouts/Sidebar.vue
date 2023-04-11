<template>
  <nav class="sidebar sidebar-offcanvas" :class="$page.sidebarActive ? 'active' : ''">
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" @click.prevent="toggleSidebarActive">
        <span class="mdi mdi-menu"></span>
      </button>
    <ul class="nav">
      <li class="nav-item nav-profile">
        <inertia-link href="account.show" class="nav-link" :href="route('profile.show')">
          <div class="nav-profile-image">
            <img v-if="$page.user.profile_photo_url" class="rounded-full" :src="$page.user.profile_photo_url" :alt="$page.user.name" />
            <i v-else class="mdi mdi-shield-account text-dark" alt="profile" style="font-size: 2.5rem;"></i>
            <span class="login-status online"></span>
          </div>
          <div class="nav-profile-text d-flex flex-column">
            <span class="font-weight-bold mb-2">
              {{ $page.user.name }}
            </span>
            <span class="text-secondary text-small">{{ $page.user.email }}</span>
          </div>
        </inertia-link>
      </li>
      <menu-item v-for="(menu, key) in $page.menu" v-if="! menu.nonmenu" :key="key" :menu="menu"></menu-item>
    </ul>
  </nav>
</template>

<script>
  import MenuItem from './MenuItem'

  export default {
    components: {
      MenuItem,
    },
    data() {
      return {
        hover: false
      }
    },
    methods: {
      toggleSidebarActive() {
        console.log(this.$page.sidebarActive);
      }
    }
  }
</script>
