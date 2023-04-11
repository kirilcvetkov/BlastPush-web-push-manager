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
            <inertia-link class="btn btn-sm btn-gradient-success" :href="route('users.create')">
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
                <th>ID</th>
                <th> </th>
                <th>User</th>
                <th>Comapny</th>
                <th class="text-center">Plan</th>
                <th>Country</th>
                <th>Account Details</th>
                <th>Updated</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in $page.payload.data">
                <td>
                  <inertia-link class="btn btn-inverse-info btn-icon-sm" :href="route('users.edit', user.id)">
                    <i class="mdi mdi-lead-pencil"></i>
                  </inertia-link>
                  <button class="d-inline-block btn btn-inverse-danger btn-icon-sm" tabindex="-1" type="button" @click="destroy(route('users.destroy', user.id))">
                    <i class="mdi mdi-delete"></i>
                  </button>
                </td>
                <td>{{ user.id }}</td>
                <td><img v-if="user.profile_photo_url" class="rounded" :src="user.profile_photo_url" /></td>
                <td>
                  {{ user.name }}<br />
                  <span class="small text-muted">{{ user.email }}</span><br />
                  <span class="small text-muted">{{ user.phone }}</span>
                </td>
                <td>{{ user.company }}</td>
                <td class="text-center">
                  <span class="btn btn-sm" :class="'btn-' + user.plan.color">{{ user.plan.name }}</span>
                </td>
                <td>
                  {{ $page.countries[user.country].nicename || user.country }}<br />
                  <span class="small text-muted">{{ user.timezone }}</span>
                </td>
                <td>
                  <span class="d-block pb-1 small text-muted">Websites: {{ user.websites_count }}</span>
                  <span class="d-block pb-1 small text-muted">Subscribers: {{ user.subscribers_count }}</span>
                  <span class="d-block pb-1 small text-muted">Campaigns: {{ user.campaigns_count }}</span>
                  <span class="d-block pb-1 small text-muted">Messages: {{ user.messages_count }}</span>
                  <span class="d-block pb-1 small text-muted">Pushes: {{ user.pushes_count }}</span>
                </td>
                <td>
                  <div class="text-nowrap" v-tooltip="user.updated">
                    {{ user.updatedHuman }}
                  </div>
                  <div class="text-nowrap small text-muted pt-1" v-tooltip="user.created">
                    Created {{ user.createdHuman }}
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
        pageTitle: 'Users',
      }
    }
  }
</script>
