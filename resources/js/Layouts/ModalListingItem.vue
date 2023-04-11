<template>
  <modal v-if="modalShow" @close="modalShow = false" v-on:keyup="keyboardEvent">
    <template #title></template>
    <div class="card-body">
    </div>
  </modal>
</template>

<script>
  import Modal from 'Modal'

  export default {
    components: {
      Modal
    },
    mounted() {
    },
    methods: {
      buildModal(id) {
        this.modalHref = route('events.view', id);
        this.modalTitle = "Viewing Event # " + id;
        this.modalShow = true;
      },
      keyboardEvent (e) {
        if (e.which === 27) {
          this.modalShow = false;
        }
      },
    },
    data() {
      return {
        modalShow: false,
        modalHref: '',
        modalTitle: ''
      }
    }
  }
</script>
