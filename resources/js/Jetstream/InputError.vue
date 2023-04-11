<template>
  <div v-show="message">
    <p class="small text-danger">
      {{ message }}
    </p>
  </div>
</template>

<script>
  export default {
    props: ['message']
  }
</script>
