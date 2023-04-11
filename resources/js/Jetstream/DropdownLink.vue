<template>
  <div>
    <button type="submit" class="btn" v-if="as == 'button'">
      <i class="mdi " v-if="icon" :class="icon"></i> <slot></slot>
    </button>

    <inertia-link :href="href" class="nav-link" v-else>
      <i class="mdi " v-if="icon" :class="icon"></i> <slot></slot>
    </inertia-link>
  </div>
</template>

<script>
  export default {
    props: ['icon', 'href', 'as']
  }
</script>
