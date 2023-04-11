<template>
  <app-layout>
    <template #title>{{ pageTitle }}</template>
    <template #icon>
      <i class="mdi mdi-web mdi-18px"></i>
    </template>
      <div class="card">
        <div class="card-body">
          <table class="table">
            <thead>
              <tr>
                <th>Website</th>
                <th>Date</th>
                <th class="text-right">Subscribes</th>
                <th class="text-right">Unsubscribes</th>
                <th class="text-right">Visits</th>
                <th class="text-right"><span title="Subscribes / Visits" data-toggle="tooltip">Visits %</span></th>
                <th class="text-right">Sends</th>
                <th class="text-right">Deliveries</th>
                <th class="text-right">Clicks</th>
                <th class="text-right"><span title="Clicks / Deliveries" data-toggle="tooltip">Clicks %</span></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="row in stats">
                <td>{{ websites[row.website_id].name || 'Unknown' }} <span class="text-muted">{{ websites[row.website_id].domain || '' }}</span></td>
                <td>{{ row.date }}</td>
                <td class="text-right">{{ row.subscribes || 0 }}</td>
                <td class="text-right">{{ row.unsubscribes || 0 }}</td>
                <td class="text-right">{{ row.visits || 0 }}</td>
                <td class="text-right">{{ (row.visits > 0 ? ((row.subscribes || 0) * 100 / row.visits) : 0) | format_number }}</td>
                <td class="text-right">{{ row.sent || 0 }}</td>
                <td class="text-right">{{ row.deliveries || 0 }}</td>
                <td class="text-right">{{ row.clicks || 0 }}</td>
                <td class="text-right">{{ (row.deliveries > 0 ? ((row.clicks || 0) * 100 / row.deliveries) : 0) | format_number }}</td>
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
  var numeral = require("numeral");

  export default {
    components: {
      AppLayout,
    },

    data() {
      return {
        pageTitle: 'Website Report',
        stats: this.$page.stats,
        websites: this.$page.websites,
      }
    },

    filters: {
      capitalize: function (value) {
        if (! value) return ''
        value = value.toString()
        return value.charAt(0).toUpperCase() + value.slice(1)
      },

      format_number: function (value) {
        return numeral(value).format("0.00");
        // return Number(value).toLocaleString();
        // return new Intl.NumberFormat("en-US", {style: "currency", currency: "USD"}).format(value);
      }
    }
  }
</script>
