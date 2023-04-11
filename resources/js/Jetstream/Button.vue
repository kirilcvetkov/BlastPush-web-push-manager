<template>
  <button :type="type" class="btn mr-2" :class="color">
    <i class="mdi mdi-18px" v-if="icon" :class="icon"></i> <slot></slot>
  </button>
</template>

<script>
  export default {
    props: {
      type: { type: String, default: 'submit' },
      color: { type: String, default: 'btn-gradient-primary' },
      icon: { type: String, default: 'mdi-content-save' },
    }
  }
</script>

<style>
  .opacity-25 { opacity: .25; }
</style>
