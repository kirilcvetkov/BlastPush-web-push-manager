<template>
  <div class="modal d-block" tabindex="-1" role="dialog" v-if="show">
    <div class="modal-dialog" role="document" style="width: fit-content">
      <div class="modal-content shadow">
        <div class="modal-header">
          <h4 class="modal-title"><slot name="title"></slot></slot></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <slot name="content"></slot>
        </div>
        <div class="modal-footer" v-if="hasFooterSlot">
          <slot name="footer"></slot>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      show: {
        default: false
      },
      closeable: {
        default: true
      },
    },

    methods: {
      close() {
        if (this.closeable) {
          this.$emit('close')
        }
      },
      hasFooterSlot() {
        return !!this.$slots.footer
      }
    }
  }
</script>
