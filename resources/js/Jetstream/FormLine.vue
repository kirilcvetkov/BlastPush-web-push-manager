<template>
  <div class="form-group row">
    <label v-if="label" :for="id" class="col-md-3 col-form-label">{{ label }}</label>
    <div class="col-md-9">
      <div class="form-check" :class="inputdivclass">
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    props: [
      'id',
      'label',
      'inputdivclass',
    ],
  }
</script>

