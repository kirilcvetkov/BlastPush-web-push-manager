<template>
  <div class="col-lg-3 col-sm-6 stretch-card grid-margin">
    <div :class="color" class="card card-img-holder text-white">
      <div class="card-body p-0">
        <h5 class="font-weight-normal mx-4 mt-4 mb-2">
          {{ title }} <small>(<span v-text="shortLabel"></span>)</small>
          <div class="btn-group float-right">
            <button type="button" class="btn dropdown-toggle p-0 text-white"
              data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i :class="icon" class="mdi mdi-24px"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
              <a class="dropdown-item" href="#" v-for="(timeframe, key) in timeframes" @click="changeRange(key, timeframe.short)">
                {{ timeframe.long }}
              </a>
            </div>
          </div>
        </h5>

        <h2 class="pl-5 h2">{{ mainNumbers }}</h2>

        <line-chart
          class="chart-container" style="position: relative; height: 70px;"
          v-if="loaded"
          :chartdata="chartData"
          :options="chartOptions"/>
      </div>
    </div>
  </div>
</template>

<script>
  import LineChart from './LineChart.vue'

  export default {
    components: { LineChart },

    props: [
      'routeNumbers',
      'routeChart',
      'title',
      'color',
      'icon'
    ],

    data() {
      return {
        loaded: false,
        mainNumbers: '-',
        shortLabel: '',
        timeframes: {
          0: {short: "24h", long: "Past 24 Hours"},
          1: {short: "7d", long: "Past 7 Days"},
          2: {short: "14d", long: "Past 14 Days"},
          3: {short: "30d", long: "Past 30 Days"},
          '-1': {short: "All", long: "All"},
        },
        chartData: null,
        chartOptions: {
          responsive: true,
          maintainAspectRatio: false,
          legend: { display: false },
          scales: {
            xAxes: [{ display: false }],
            yAxes: [{ display: false, ticks: { beginAtZero: true } }],
          },
          elements: {
            line: { borderWidth: 2 },
            point: { radius: 0, hitRadius: 15, hoverRadius: 5 },
          },
          tooltips: { mode: 'nearest', intersect: false },
          hover: { intersect: false }
        }
      }
    },

    async mounted () {
      let key = Object.keys(this.timeframes)[0];
      let short = this.timeframes[key].short;
      this.changeRange(key, short);
    },

    methods: {
      changeRange(key, short) {
        var timeframe = parseInt(key);

        this.loaded = false
        this.mainNumbers = '-';
        this.shortLabel = short;

        axios.get(this.routeNumbers + timeframe)
          .then((response) => {
            this.mainNumbers = response.data;
          })
          .catch(function (error) {
            console.log(error);
            alert(error);
          });

        axios.get(this.routeChart + timeframe)
          .then((response) => {
            this.chartData = response.data;
            this.loaded = true;
          })
          .catch(function (error) {
            console.log(error);
            alert(error);
          });
      }
    }
  }
</script>
