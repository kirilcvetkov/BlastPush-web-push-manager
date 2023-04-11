<template>
  <div class="row">
    <jet-section-title class="col-md-3">
      <template #title><slot name="title"></slot></template>
      <template #description><slot name="description"></slot></template>
    </jet-section-title>

    <div class="card card-body col-md-9">
      <slot name="content"></slot>
    </div>
  </div>
</template>

<script>
  import JetSectionTitle from './SectionTitle'

  export default {
    components: {
      JetSectionTitle,
    }
  }
</script>
