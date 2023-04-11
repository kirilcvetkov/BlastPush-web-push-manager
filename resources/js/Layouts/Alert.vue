<template>
  <div :class="type" class="alert alert-dismissible fade show" role="alert" v-show="isVisible">
    {{ message }}
    <button type="button" class="close" @click="isVisible = false" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
</template>

<script>
  export default {
    props: {
      message: { default: null },
      type: { default: null },
    },

    data() {
      return {
        isVisible: true,
      }
    },
  }
</script>
