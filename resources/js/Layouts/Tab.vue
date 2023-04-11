<template>
  <div v-show="isActive" :class="{'active': isActive}" class="tab-pane show">
    <slot></slot>
  </div>
</template>

<script>
  export default {
    props: {
      name: { required: true },
      icon: { default: '' },
      counter: { default: null },
      selected: { default: false }
    },

    data() {
      return {
        isActive: false,
      }
    },

    computed: {
      href() {
        return '#' + this.name.toLowerCase().replace(/ /g, '-');
      },
      active(id) {
        this.$cookies.set("tab", 1, "24d");
      }
    },

    mounted() {
      this.isActive = this.selected;
    }
  }
</script>
