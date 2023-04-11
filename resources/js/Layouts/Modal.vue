<template>
  <div class="modal d-block" tabindex="-1" role="dialog" :id="id">
    <div class="modal-dialog modal-lg" role="document" style="width: fit-content">
      <div class="modal-content shadow">
        <div class="modal-header">
          <h3 class="modal-title"><slot name="title"></slot></h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="hideModal">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <slot></slot>
        </div>
        <div class="modal-footer">
          <slot name="footer"></slot>
          <button type="button" class="btn btn-gradient-light" ref="closeButton" @click="hideModal" autofocus>
            <i class="mdi mdi-close"></i> Close
          </button>
        </div>
      </div>
    </div>
    <div class="modal-backdrop show" style="z-index: -1;" @click="hideModal"></div>
  </div>
</template>

<script>
  export default {
    props: {
      id: String
    },
    created() {
      const component = this;
      this.handler = function (e) {
        component.$emit('keyup', e);
      }
      window.addEventListener('keyup', this.handler);
    },
    mounted() {
      if (this.$refs.actionButton) {
        this.$refs.actionButton.focus();
      } else {
        this.$refs.closeButton.focus();
      }
    },
    beforeDestroy() {
      window.removeEventListener('keyup', this.handler);
    },
    methods: {
      hideModal() {
        this.$emit('close');
      },
      keyboardEvent (e) {
        if (e.which === 27) {
          this.scriptsModal = false;
        }
      },
    },
  }
</script>
