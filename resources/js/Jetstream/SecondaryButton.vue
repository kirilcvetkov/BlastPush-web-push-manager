<template>
  <button :type="type" class="btn" :class="color">
    <i v-if="icon" class="mdi mdi-18px" :class="icon"></i> <slot></slot>
  </button>
</template>

<script>
  export default {
    props: {
      type: { type: String, default: 'button', },
      color: { type: String, default: 'btn-gradient-light' },
      icon: { type: String, default: 'mdi-close' },
    }
  }
</script>
