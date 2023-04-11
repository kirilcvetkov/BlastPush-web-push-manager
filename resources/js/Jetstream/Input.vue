<template>
  <div class="form-group row">
    <label :for="id" class="col-md-3 col-form-label">{{ label }}</label>
    <div class="col-md-9">
      <input :id="id" :type="type" class="form-control" :class="{'is-invalid': error}" :value="value" :placeholder="label" :autocomplete="id"
        @input="$emit('input', $event.target.value)" ref="input" :autofocus="autofocus">
      <slot></slot>
      <span class="invalid-feedback d-block" role="alert" v-if="error">
        <strong>{{ error }}</strong>
      </span>
    </div>
  </div>
</template>

<script>
  export default {
    props: [
      'id',
      'type',
      'label',
      'value',
      'error',
      'autofocus'
    ],
  }
</script>

