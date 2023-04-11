<template>
  <app-layout>
    <template #title>{{ pageTitle }}</template>
    <template #icon>
      <i class="mdi mdi-web mdi-18px"></i>
    </template>
    <div class="card">
      <div class="card-body">
        <div class="float-right">
          <pagination :links="events.links" class="d-flex justify-content-end" />
        </div>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th> </th>
                <th>ID</th>
                <th>Type</th>
                <th>Website</th>
                <th class="text-center" v-tooltip="'Subscriber ID'">SubID</th>
                <th>Platform</th>
                <th>Browser</th>
                <th>Created</th>
                <th>Webhook</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="event in events.data">
                <td>
                  <button type="button" class="btn btn-icon-sm btn-inverse-primary" @click="buildModal(event)">
                    <i class="mdi mdi-eye-outline"></i>
                  </button>
                </td>
                <td>{{ event.id }}</td>
                <td :class="eventTypeColor(event)">
                  <i :class="eventTypeIcon(event)"></i>
                  {{ eventTypeName(event) }}
                </td>
                <td v-tooltip="event.website.domain">
                  {{ event.website.name }}
                </td>
                <td class="text-center">
                  {{ event.subscriber.id }}
                </td>
                <td><span v-tooltip="event.device">{{ event.platform }}</span></td>
                <td>{{ event.browser }}</td>
                <td v-tooltip="event.created">
                  {{ event.createdHuman }}
                </td>
                <td>
                  <button v-if="event.webhook" type="button" class="btn btn-icon-sm" :class="webhookStatusColor(event.webhook)"
                      v-tooltip="event.webhook.statusName" @click="buildModal(event.webhook)">
                    <i :class="webhookStatusIcon(event.webhook)"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="float-right">
          <pagination :links="events.links" class="d-flex justify-content-end" />
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
      buildModal(event) {
        this.modalTitle = "Viewing Event # " + event.id;
        this.modalShow = true;

        var html = '';

        for (const [key, value] of Object.entries(event)) {
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
      msg(msg) {
        alert(msg);
      },
      eventTypeName: function(event) {
        return this.eventTypesDetails[event.type_id].name;
      },
      eventTypeColor: function(event) {
        return this.eventTypesDetails[event.type_id].color;
      },
      eventTypeIcon: function(event) {
        return this.eventTypesDetails[event.type_id].icon;
      },
      webhookStatusColor: function(webhook) {
        return "btn-inverse-" + this.$page.webhookStatusColors[webhook.status];
      },
      webhookStatusIcon: function(webhook) {
        return this.$page.webhookStatusIcons[webhook.status];
      },
    },

    data() {
      return {
        events: this.$page.events,
        eventTypesDetails: this.$page.eventTypesDetails,
        pageTitle: 'Events',
        modalShow: false,
        modalTitle: '',
        modalBody: ''
      }
    }
  }
</script>
