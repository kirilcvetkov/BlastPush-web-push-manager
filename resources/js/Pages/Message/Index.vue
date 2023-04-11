<template>
  <app-layout>
    <template #title>{{ pageTitle }}</template>
    <template #icon>
      <i class="mdi mdi-web mdi-18px"></i>
    </template>
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-sm-6">
            <inertia-link class="btn btn-sm btn-gradient-success" :href="route('messages.create')">
              <i class="mdi mdi-plus"></i> Create
            </inertia-link>
          </div>
          <div class="col-sm-6">
            <pagination :links="payload.links" class="d-flex justify-content-end" />
          </div>
        </div>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th> </th>
                <th class="d-none d-xl-table-cell"> </th>
                <th>Title</th>
                <th class="text-center" v-tooltip="'Sent Push Notifications'">Sent</th>
                <th>Updated</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="message in payload.data">
                <td>
                  <inertia-link class="btn btn-inverse-info btn-icon-sm" :href="route('messages.edit', message.id)">
                    <i class="mdi mdi-lead-pencil"></i>
                  </inertia-link>
                  <button class="d-inline-block btn btn-inverse-danger btn-icon-sm" tabindex="-1" type="button" @click="destroy(route('messages.destroy', message.id))">
                    <i class="mdi mdi-delete"></i>
                  </button>
                </td>
                <td class="d-none d-xl-table-cell">
                  <img v-tooltip="'Message Icon'" v-if="message.icon" class="img-fluid rounded" :src="message.icon" />
                  <img v-tooltip="'Message Image'" v-if="message.image" class="img-fluid rounded" :src="message.image" />
                  <img v-tooltip="'Message Badge'" v-if="message.badge" class="img-fluid rounded" :src="message.badge" />
                </td>
                <td>{{ message.title }}</td>
                <td class="text-center">{{ message.pushes_count }}</td>
                <td>
                  <div v-tooltip="'Updated at ' + message.updated">
                    {{ message.updatedHuman }}
                  </div>
                  <div class="small text-muted pt-2" v-tooltip="'Created at ' + message.created">
                    Created {{ message.createdHuman }}
                  </div>
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
  </app-layout>
</template>

<script>
  import AppLayout from '../../Layouts/AppLayout';
  import Pagination from '../../Layouts/Pagination'

  export default {
    components: {
      AppLayout,
      Pagination,
    },

    methods: {
      destroy(route) {
        if (confirm('Are you sure?')) {
          this.$inertia.delete(route);
        }
      },
    },

    data() {
      return {
        payload: this.$page.payload,
        statusIcons: this.$page.statusIcons,
        statusColors: this.$page.statusColors,
        statusNames: this.$page.statusNames,
        pageTitle: 'Messages',
      }
    }
  }
</script>
