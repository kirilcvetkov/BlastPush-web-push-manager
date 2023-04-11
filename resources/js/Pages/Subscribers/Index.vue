<template>
  <app-layout>
    <template #title>{{ pageTitle }}</template>
    <template #icon>
      <i class="mdi mdi-web mdi-18px"></i>
    </template>
    <div class="card">
      <div class="card-body">
        <div class="float-right">
          <pagination :links="payload.links" class="d-flex justify-content-end" />
        </div>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th> </th>
                <th>ID</th>
                <th>Website</th>
                <th class="text-center">Subscribed</th>
                <th class="text-center">Sent</th>
                <th class="text-center">Events</th>
                <th>Platform</th>
                <th>Browser</th>
                <th>Updated</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="subscriber in payload.data">
                <td>
                  <button type="button" class="btn btn-icon-sm btn-inverse-primary" @click="buildModal(subscriber)">
                    <i class="mdi mdi-eye-outline"></i>
                  </button>
                </td>
                <td>{{ subscriber.id }}</td>
                <td v-tooltip="subscriber.website ? subscriber.website.domain : ''">
                  {{ subscriber.website ? subscriber.website.name : '' }}
                </td>
                <td class="d-flex flex-row">
                  <button type="button" class="btn btn-icon-sm" :class="subscriber.subscribed ? 'btn-inverse-success' : 'btn-inverse-danger'">
                    <i class="mdi" :class="subscriber.subscribed ? 'mdi-checkbox-marked-circle-outline' : 'mdi-close-circle-outline'"></i>
                  </button>
                  <span class="pl-1" :class="subscriber.subscribed ? 'text-success' : 'text-danger'"
                    v-tooltip="subscriber.subscribed ? subscriber.created : (subscriber.deleted ? subscriber.deleted : '' )">
                    {{ subscriber.subscribed ? 'Subscribed' : 'Unsubscribed' }}
                    <div class="pt-1">
                      {{ subscriber.subscribed ? subscriber.createdHuman : (subscriber.deletedHuman ? subscriber.deletedHuman : '' ) }}
                    </div>
                  </span>
                </td>
                <td class="text-center">{{ subscriber.pushes_count }}</td>
                <td class="text-center">{{ subscriber.events_count }}</td>
                <td>
                  <span v-tooltip="subscriber.event ? subscriber.event.device : ''">
                    {{ subscriber.event ? subscriber.event.platform : '' }}
                  </span>
                </td>
                <td>{{ subscriber.event ? subscriber.event.browser : '' }}</td>
                <td v-tooltip="subscriber.updated ? subscriber.updated : ''">
                  {{ subscriber.updatedHuman }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="float-right">
          <pagination :links="payload.links" class="d-flex justify-content-end" />
        </div>
      </div>
    </div>

    <modal v-if="modalShow" @close="modalShow = false" v-on:keyup="keyboardEvent">
      <template #title>{{ modalTitle }}</template>
      <div class="table-responsive" v-html="modalBody"></div>
    </modal>
  </app-layout>
</template>

<script>
  import AppLayout from '../../Layouts/AppLayout';
  import Pagination from '../../Layouts/Pagination'
  import Modal from '../../Layouts/Modal'

  export default {
    components: {
      AppLayout,
      Pagination,
      Modal,
    },

    methods: {
      buildModal(subscriber) {
        this.modalTitle = "Viewing Subscriber # " + subscriber.id;
        this.modalShow = true;

        var html = '';

        for (const [key, value] of Object.entries(subscriber)) {
          if (key.indexOf('Human') != -1) {
            continue;
          }
          html += "<th scope='row'>" + key + "</th><td class='text-wrap'>" +
            (typeof value == 'object' ? JSON.stringify(value) : value) +
            "</td></tr>\n";
        }

        this.modalBody = "<table class='table table-hover table-sm'>" + html + "</table>"
      },
      keyboardEvent (e) {
        if (e.which === 27) {
          this.modalShow = false;
        }
      },
      destroy(route) {
        if (confirm('Are you sure?')) {
          this.$inertia.delete(route);
        }
      },
    },

    data() {
      return {
        payload: this.$page.payload,
        pageTitle: 'Subscribers',
        modalShow: false,
        modalTitle: '',
        modalBody: ''
      }
    }
  }
</script>
