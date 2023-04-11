<template>
  <li class="nav-item" @mouseover="hover = true" @mouseleave="hover = false" :class="{ 'hover-open': hover, 'active': menuIsActive(menu.route) }">
    <a v-if="menu.submenu" :href="getHref" class="nav-link" :class="{ 'collapsed': ! menuActive }" data-toggle="collapse" aria-expanded="true" aria-controls="general-pages">
      <span class="menu-title">{{ menu.title }}</span>
      <i class="menu-arrow" v-if="menu.submenu"></i>
      <i class="mdi menu-icon" :class="menu.icon"></i>
    </a>
    <inertia-link v-else :href="route(menu.route)" class="nav-link">
      <span class="menu-title">{{ menu.title }}</span>
      <i class="menu-arrow" v-if="menu.submenu"></i>
      <i class="mdi menu-icon" :class="menu.icon"></i>
    </inertia-link>
    <div class="collapse" :class="{ 'show': menuActive }" v-if="menu.submenu" :id="$vnode.key">
      <ul class="nav flex-column sub-menu">
        <li class="nav-item" v-for="sub in menu.submenu">
          <inertia-link class="nav-link" :href="route(sub.route)" :class="{ 'active': menuIsActive(sub.route) }">
            {{ sub.title }}
          </inertia-link>
        </li>
      </ul>
    </div>
  </li>
</template>

<script>
  export default {
    props: {
      menu: Object,
    },
    data() {
      return {
        hover: false,
        menuActive: false,
      }
    },
    methods: {
      menuIsActive(route) {
        if (this.$page.currentRouteName == route) {
          this.menuActive = true;
          return true;
        }
        return false;
      },
    },
    computed: {
      getHref() {
        return "#" + this.$vnode.key;
      },
    }
  }
</script>
