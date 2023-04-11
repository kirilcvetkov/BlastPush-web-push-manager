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
                <th class="text-center" v-tooltip="'Subscriber ID'">SubID</th>
                <th>Campaign</th>
                <th>Message</th>
                <th>Status</th>
                <th>Tracking Key</th>
                <th>Created</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="push in payload.data">
                <td>
                  <button type="button" class="btn btn-icon-sm btn-inverse-primary" @click="buildModal(push)">
                    <i class="mdi mdi-eye-outline"></i>
                  </button>
                </td>
                <td>{{ push.id }}</td>
                <td v-tooltip="push.subscriber && push.subscriber.website ? push.subscriber.website.domain : ''">
                  {{ push.subscriber && push.subscriber.website ? push.subscriber.website.name : '' }}
                </td>
                <td class="text-center" :class="push.subscriber && push.subscriber.deleted_at ? 'text-muted' : ''"
                  v-tooltip="push.subscriber && push.subscriber.deleted_at ? 'Unsubscribed on ' + push.subscriber.deleted_at : ''">
                  {{ push.subscriber ? push.subscriber.id : '' }}
                </td>
                <td v-tooltip="campaignTooltip(push)">
                  <i class="mdi" :class="campaignIcon(push)"></i>
                  {{ push.campaign ? push.campaign.name : '' }}
                  <i v-if="push.schedule && push.schedule.is_trigger" class="mdi mdi-redo-variant text-success"></i>
                </td>
                <td>{{ push.message ? push.message.title : '' }}</td>
                <td>
                  <div class="d-flex flex-row">
                    <button type="button" class="btn btn-icon-sm" :class="statusColor(push.is_success, 'btn-inverse-')">
                      <i :class="statusIcon(push.is_success)"></i>
                    </button>
                    <span class="pl-1" :class="statusColor(push.is_success)" v-tooltip="statusTooltip(push)">
                      {{ status(push.is_success) }}
                      <div class="pt-1">
                        {{ push.sentHuman ? push.sentHuman : (push.scheduledToSend ? push.scheduledToSend : '') }}
                      </div>
                    </span>
                  </div>
                </td>
                <td>{{ push.uuid }}</td>
                <td v-tooltip="push.created">{{ push.createdHuman }}</td>
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
        this.modalTitle = "Viewing Event # " + subscriber.id;
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
      campaignTooltip(push) {
        var type = push.campaign ? push.campaign.type : '';
        var order = push.schedule ? push.schedule.order + 1 : null;

        return type.charAt(0).toUpperCase() + type.slice(1) + " " +
          (type == 'waterfall' && order
            ? 'schedule #' + order
            : (type == 'reoccurring' && push.schedule
              ? push.schedule.reoccurring_frequency
              : '')
          ) +
          (push.schedule && push.schedule.is_trigger ? ' trigger' : '')
      },
      campaignIcon(push) {
        var type = push.campaign ? push.campaign.type : '';

        return type == 'waterfall' ? 'mdi-water text-info'
          : (type == 'scheduled' ? 'mdi-calendar text-success'
            : (type == 'reoccurring' ? 'mdi-repeat text-primary'
              : ''
            )
          )
      },
      statusColor(is_success, prepend = 'text-') {
        is_success = is_success || 0;
        return prepend + this.statusColors[is_success];
      },
      statusIcon(is_success) {
        is_success = is_success || 0;
        return this.statusIcons[is_success];
      },
      status(is_success) {
        is_success = is_success || 0;
        return this.statusNames[is_success];
      },
      statusTooltip(push) {
        return push.sent ? 'Sent at ' + push.sent
          : (push.scheduledToSend ? 'Scheduled to send at ' + push.scheduledToSend
            : '')
      }
    },

    data() {
      return {
        payload: this.$page.payload,
        statusIcons: this.$page.statusIcons,
        statusColors: this.$page.statusColors,
        statusNames: this.$page.statusNames,
        pageTitle: 'Subscribers',
        modalShow: false,
        modalTitle: '',
        modalBody: ''
      }
    }
  }
</script>
