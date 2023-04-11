<template>
  <div class="grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">{{ title }}</h4>
        <doughnut-chart
          class="mt-4"
          v-if="loaded"
          :styles="styles"
          :chartdata="chartData"
          @legend="setLegend" />
        <div class="rounded-legend legend-vertical legend-bottom-left pt-4" v-html="legend"></div>
      </div>
    </div>
  </div>
</template>

<script>
  import DoughnutChart from './DoughnutChart.vue'

  export default {
    components: { DoughnutChart },

    props: ['routeChart', 'title'],

    data() {
      return {
        loaded: false,
        styles: {
          height: '248px',
          position: 'relative'
        },
        chartData: {},
        legend: null
      }
    },

    async mounted () {
      this.loaded = false;

      axios.get(this.routeChart)
        .then((response) => {
          this.chartData = response.data;
          this.loaded = true;
        })
        .catch(function (error) {
          console.log(error);
          alert(error);
        });
    },
    methods: {
      setLegend (html) {
        this.legend = html
      }
    }
  }
</script>
