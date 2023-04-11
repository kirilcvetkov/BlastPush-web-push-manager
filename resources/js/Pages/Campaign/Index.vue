<template>
  <app-layout>
    <template #title>{{ pageTitle }}</template>
    <template #icon><i class="mdi mdi-group mdi-18px"></i></template>

    <!-- Waterfall Campaigns -->
    <div class="card mb-3">
      <div class="card-body tab-content pt-4 pb-1">
        <div class="row">
          <div class="col-sm-6">
            <inertia-link v-if="! waterfall.data" class="btn btn-sm btn-gradient-info" :href="route('campaigns.create', 0)">
              <i class="mdi mdi-plus"></i> Create Waterfall Campaign
            </inertia-link>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th> </th>
                <th>Name</th>
                <th class="text-center"> </th>
                <th class="text-center">Schedules</th>
                <th class="text-center">Websites</th>
                <th>Updated</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(campaign, key) in waterfall.data">
                <td>
                  <inertia-link class="btn btn-inverse-info btn-icon-sm" :href="route('campaigns.edit', campaign.id)">
                    <i class="mdi mdi-lead-pencil"></i>
                  </inertia-link>
                  <button class="d-inline-block btn btn-inverse-danger btn-icon-sm" tabindex="-1" type="button" @click="destroy(route('campaigns.destroy', campaign.id))">
                    <i class="mdi mdi-delete"></i>
                  </button>
                </td>
                <td>{{ campaign.name }}</td>
                <td><i class="mdi mdi-24px" :class="campaign.enabled ? 'mdi-play-circle text-success' : 'mdi-pause-circle text-danger'"></i></td>
                <td class="text-center">
                  <span class="text-info" v-tooltip="'Waterfall Schedules'">
                    <i class="mdi mdi-water"></i> {{ campaign.schedules_count }}
                  </span>
                  <span class="text-muted">/</span>
                  <span class="text-success" v-tooltip="'Triggers'">
                    <i class="mdi mdi-redo-variant"></i> {{ campaign.triggers_count }}
                  </span>
                  <!-- <button type="button" class="btn btn-inverse-primary btn-icon-sm ml-1 preview"
                    data-route="{{ route('dialogs.preview.id', $campaign.id) }}">
                    <i class="mdi mdi-chart-areaspline"></i>
                  </button> -->
                </td>
                <td class="text-center">{{ campaign.websites_count }}</td>
                <td>
                  <div v-tooltip="'Updated at ' + campaign.updated">
                    {{ campaign.updatedHuman }}
                  </div>
                  <div class="small text-muted pt-2" v-tooltip="'Created at ' + campaign.created">
                    Created {{ campaign.createdHuman }}
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer text-muted py-2">
        <i class="mdi mdi-alert text-warning"></i>
        Waterfall campaigns will be sent first. Once they go through, all other campaigns will be scheduled.
      </div>
    </div>

    <!-- Scheduled Campaigns -->
    <div class="card mb-3">
      <div class="card-body tab-content pt-4 pb-1">
        <div class="row">
          <div class="col-sm-6">
            <inertia-link class="btn btn-sm btn-gradient-success" :href="route('campaigns.create', 1)">
              <i class="mdi mdi-plus"></i> Create Scheduled Campaign
            </inertia-link>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th> </th>
                <th>Name</th>
                <th class="text-center"> </th>
                <th class="text-center">Scheduled at</th>
                <th class="text-center">Websites</th>
                <th>Updated</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(campaign, key) in scheduled.data">
                <td>
                  <inertia-link class="btn btn-inverse-info btn-icon-sm" :href="route('campaigns.edit', campaign.id)">
                    <i class="mdi mdi-lead-pencil"></i>
                  </inertia-link>
                  <button class="d-inline-block btn btn-inverse-danger btn-icon-sm" tabindex="-1" type="button" @click="destroy(route('campaigns.destroy', campaign.id))">
                    <i class="mdi mdi-delete"></i>
                  </button>
                </td>
                <td>{{ campaign.name }}</td>
                <td><i class="mdi mdi-play-circle mdi-24px text-success" :class="campaign.enabled ? 'text-success' : 'text-danger'"></i></td>
                <td class="text-center" >
                  <div v-for="schedule in campaign.schedules">
                    <div v-if="schedule && schedule.scheduled_at">
                      <span v-tooltip="schedule.is_trigger ? 'With trigger, delayed by ' + trigger.delay + ' minute' + (trigger.delay > 1 ? 's' : '') : ''">
                        {{ schedule.scheduled }}
                        <i v-if="schedule.is_trigger" class="mdi mdi-redo-variant"></i>
                      </span>
                      <div class='pt-1 text-muted'>{{ schedule.scheduledHuman }}</div>
                    </div>
                  </div>
                </td>
                <td class="text-center">{{ campaign.websites_count }}</td>
                <td>
                  <div v-tooltip="'Updated at ' + campaign.updated">
                    {{ campaign.updatedHuman }}
                  </div>
                  <div class="small text-muted pt-2" v-tooltip="'Created at ' + campaign.created">
                    Created {{ campaign.createdHuman }}
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Reoccurring Campaigns -->
    <div class="card mb-3">
      <div class="card-body tab-content pt-4 pb-1">
        <div class="row">
          <div class="col-sm-6">
            <inertia-link class="btn btn-sm btn-gradient-success" :href="route('campaigns.create', 1)">
              <i class="mdi mdi-plus"></i> Create Reoccurring Campaign
            </inertia-link>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th> </th>
                <th>Name</th>
                <th class="text-center"> </th>
                <th class="text-center">Reoccurring</th>
                <th class="text-center">Websites</th>
                <th>Updated</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(campaign, key) in reoccurring.data">
                <td>
                  <inertia-link class="btn btn-inverse-info btn-icon-sm" :href="route('campaigns.edit', campaign.id)">
                    <i class="mdi mdi-lead-pencil"></i>
                  </inertia-link>
                  <button class="d-inline-block btn btn-inverse-danger btn-icon-sm" tabindex="-1" type="button" @click="destroy(route('campaigns.destroy', campaign.id))">
                    <i class="mdi mdi-delete"></i>
                  </button>
                </td>
                <td>{{ campaign.name }}</td>
                <td><i class="mdi mdi-play-circle mdi-24px text-success" :class="campaign.enabled ? 'text-success' : 'text-danger'"></i></td>
                <td class="text-center" >
                  <div v-for="schedule in campaign.schedules">
                    <div v-if="schedule">
                      <span class="badge" :class="badge(schedule.reoccurringFrequencyColor)"
                        v-tooltip="schedule.is_trigger ? 'With trigger, delayed by ' + trigger.delay + ' minute' + (trigger.delay > 1 ? 's' : '') : ''">
                        {{ schedule.reoccurring_frequency }}
                        <i v-if="schedule.is_trigger" class="mdi mdi-redo-variant"></i>
                      </span>
                      <div class="pt-1">{{ schedule.dayLabel }} {{ schedule.hourMinuteLabel }}</div>
                    </div>
                  </div>
                </td>
                <td class="text-center">{{ campaign.websites_count }}</td>
                <td>
                  <div v-tooltip="'Updated at ' + campaign.updated">
                    {{ campaign.updatedHuman }}
                  </div>
                  <div class="small text-muted pt-2" v-tooltip="'Created at ' + campaign.created">
                    Created {{ campaign.createdHuman }}
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
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
      badge(color) {
        return 'badge-' + color;
      }
    },

    data() {
      return {
        waterfall: this.$page.waterfall,
        scheduled: this.$page.scheduled,
        reoccurring: this.$page.reoccurring,
        statusIcons: this.$page.statusIcons,
        statusColors: this.$page.statusColors,
        statusNames: this.$page.statusNames,
        pageTitle: 'Campaigns',
      }
    }
  }
</script>
