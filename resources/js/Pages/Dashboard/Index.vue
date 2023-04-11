<template>
  <app-layout>
    <template #title>Dashboard</template>
    <template #icon>
      <i class="mdi mdi-view-dashboard mdi-18px"></i>
    </template>

    <div class="row">
      <card
        routeNumbers="/dashboard/subscribers/1/"
        routeChart="/dashboard/subscribersChart/1/"
        title="Subscribers"
        color="bg-gradient-primary"
        icon="mdi-account-multiple-plus"
      />

      <card
        routeNumbers="/dashboard/subscribers/0/"
        routeChart="/dashboard/subscribersChart/0/"
        title="Unsubscribers"
        color="bg-gradient-danger"
        icon="mdi-account-multiple-minus"
      />

      <card
        routeNumbers="/dashboard/push/"
        routeChart="/dashboard/pushChart/"
        title="Push"
        color="bg-gradient-info"
        icon="mdi-send"
      />

      <card
        routeNumbers="/dashboard/clicks/"
        routeChart="/dashboard/clicksChart/"
        title="Clicks"
        color="bg-gradient-success"
        icon="mdi-cursor-default-click"
      />
    </div>

    <div class="row">
      <events />
      <pie-chart class="col-md-5" title="Subscriptions" routeChart="/dashboard/subscriptions" />
    </div>

    <div class="row">
      <pie-chart class="col-md-4" title="Browsers" routeChart="/dashboard/browsers/0" />
      <pie-chart class="col-md-4" title="Platforms" routeChart="/dashboard/browsers/1" />
      <pie-chart class="col-md-4" title="Devices" routeChart="/dashboard/browsers/2" />
    </div>

  </app-layout>
</template>

<script>
  import AppLayout from '../../Layouts/AppLayout'
  import Card from './Card'
  import Events from './Events'
  import PieChart from './PieChart'

  export default {
    components: {
      AppLayout,
      Card,
      Events,
      PieChart,
    },
  }
</script>
