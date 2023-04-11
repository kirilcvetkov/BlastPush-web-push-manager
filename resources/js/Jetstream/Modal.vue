<template>
  <div class="modal d-block" tabindex="-1" role="dialog" v-show="show">
    <div class="modal-dialog" role="document" style="width: fit-content" :class="maxWidthClass">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title"><slot name="title"></slot></h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <slot></slot>
        </div>
        <div class="modal-footer" v-if="hasFooterSlot">
          <slot name="footer">
          </slot>
          <!-- <button type="button" class="btn btn-secondary" @click="$emit('close')">Close</button> -->
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
      maxWidth: {
        default: '2xl'
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
      }
    },

    watch: {
      show: {
        immediate: true,
        handler: (show) => {
          if (show) {
            document.body.style.overflow = 'hidden'
          } else {
            document.body.style.overflow = null
          }
        }
      }
    },

    created() {
      const closeOnEscape = (e) => {
        if (e.key === 'Escape' && this.show) {
          this.close()
        }
      }

      document.addEventListener('keydown', closeOnEscape)

      this.$once('hook:destroyed', () => {
        document.removeEventListener('keydown', closeOnEscape)
      })
    },

    computed: {
      maxWidthClass() {
        return {
          'sm': 'modal-sm',
          'md': 'modal-md',
          'lg': 'modal-lg',
          'xl': 'modal-xl',
        }[this.maxWidth]
      },
      hasFooterSlot() {
        return !!this.$slots.footer
      }
    }
  }
</script>
