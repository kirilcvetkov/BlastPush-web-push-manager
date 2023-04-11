<template>
  <app-layout>
    <template #title>{{ pageTitle }}</template>
    <template #icon>
      <i class="mdi mdi-web mdi-18px"></i>
    </template>
    <div v-for="row in stats">
      <div v-for="details in row" class="card mb-4">
        <div class="card-body">
          <div class="card-title text-muted">
            {{ details.campaign.name || 'Non-Campaign Push' }} -
            <span :class='details.campaign.campaignColor'>
              {{ details.campaign.type | capitalize }}
            </span>
          </div>

          <table class="table">
            <thead>
              <tr>
                <th>Schedule</th>
                <th>Date</th>
                <th class="text-right">Sent</th>
                <th class="text-right">Delivered</th>
                <th class="text-right">Clicked</th>
                <th class="text-right">Closed</th>
                <th class="text-right">Denied</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, date) in details.stats">
                <td>{{ details.campaign.name }}</td>
                <td>{{ date }}</td>
                <td class="text-right">{{ item['Sent'] || 0 }}</td>
                <td class="text-right">{{ item['Delivered'] || 0 }}</td>
                <td class="text-right">{{ item['Clicked'] || 0 }}</td>
                <td class="text-right">{{ item['Closed'] || 0 }}</td>
                <td class="text-right">{{ item['Denied'] || 0 }}</td>
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

  export default {
    components: {
      AppLayout,
    },

    data() {
      return {
        pageTitle: 'Campaign Report',
        stats: this.$page.stats,
      }
    },

    filters: {
      capitalize: function (value) {
        if (! value) return ''
        value = value.toString()
        return value.charAt(0).toUpperCase() + value.slice(1)
      }
    }
  }
</script>
