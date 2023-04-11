<template>
  <div :class="type" class="alert d-inline-block" role="alert" v-if="on">
    <slot />
  </div>
</template>

<script>
  export default {
    props: {
      on: { default: false },
      message: { default: null },
      type: { default: 'alert-success' },
    }
  }
</script>
