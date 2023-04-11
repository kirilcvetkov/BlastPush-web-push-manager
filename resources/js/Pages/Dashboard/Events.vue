<template>
  <div class="col-md-7 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title float-left">Events</h4>
        <div class="rounded-legend legend-horizontal legend-top-right float-right" v-html="legend"></div>
        <line-chart
          class="mt-4"
          v-if="loaded"
          :styles="styles"
          :chartdata="chartData"
          :options="chartOptions"
          @legend="setLegend"/>
      </div>
    </div>
  </div>
</template>

<script>
  import LineChart from './LineChart.vue'

  export default {
    components: { LineChart },

    data() {
      return {
        loaded: false,
        styles: {
          height: '371px',
          position: 'relative'
        },
        legend: null,
        chartData: {
          labels: [],
          datasets: [{
              label: 'Delivered',
              pointHoverBorderWidth: 5,
              borderColor: 'rgba(54, 162, 235, 1)',
              hoverBackgroundColor: 'rgba(54, 162, 235, 0.1)',
              legendColor: 'linear-gradient(to right, #90caf9, #0070CC)',
              data: []
            }, {
              label: 'Clicked',
              pointHoverBorderWidth: 5,
              borderColor: 'rgba(6, 185, 157, 1)',
              backgroundColor: 'rgba(6, 185, 157, 0.1)',
              legendColor: 'linear-gradient(to right, #38E5CB, #17B59D)',
              data: []
            }, {
              label: 'Closed',
              pointHoverBorderWidth: 5,
              borderColor: 'rgba(242, 142, 42, 1)',
              backgroundColor: 'rgba(242, 142, 42, 0.1)',
              legendColor: 'linear-gradient(to right, #FCD8B5, #F28E2A)',
              data: []
            }, {
              label: 'Denied',
              pointHoverBorderWidth: 5,
              borderColor: '#FE7096',
              backgroundColor: 'rgba(255, 99, 132, 0.1)',
              legendColor: 'linear-gradient(to right, #ffbf96, #D41330)',
              data: []
          }]
        },
        legend: null,
        chartOptions: {
          type: 'line',
          responsive: true,
          maintainAspectRatio: false,
          legend: false,
          scales: {
            yAxes: [{
              gridLines: { drawBorder: false, color: 'rgba(235,237,242,1)', zeroLineColor: 'rgba(235,237,242,1)' },
              ticks: { padding: 10, precision: 0 },
            }],
            xAxes: [{
              gridLines: { display: false, drawBorder: false, color: 'rgba(0,0,0,1)', zeroLineColor: 'rgba(235,237,242,1)' },
              ticks: { padding: 10, fontColor: "#9c9fa6", autoSkip: true },
              categoryPercentage: 0.5,
              barPercentage: 0.5
            }]
          },
          elements: {
            line: { borderWidth: 2 },
            point: { radius: 0, hitRadius: 25, hoverRadius: 5 },
          },
          tooltips: { mode: 'nearest', intersect: false },
          hover: { intersect: false },
        }
      }
    },

    async mounted () {
      this.loaded = false;

      axios.get('/dashboard/events')
        .then((response) => {
          this.chartData.labels = response.data.labels;
          this.chartData.datasets.forEach(( dataset, index ) => {
            dataset.data = response.data.data[index];
          });
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
