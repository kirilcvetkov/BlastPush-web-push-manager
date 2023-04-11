<script>
  import { Doughnut, mixins } from "vue-chartjs";
  import { Tableau20 } from 'chartjs-plugin-colorschemes/src/colorschemes/colorschemes.tableau';

  export default {
    extends: Doughnut,
    props: {
      chartdata: { type: Object, default: null },
      options: { type: Object, default: null },
    },
    data() {
      return {
        colors: [
          "#17B59D", //"#59A14F", // green
          "#D41330", //"#E15759", // red
          "#0070CC", //"#4E79A7", // blue
          "#9a55ff", // purple
          "#F28E2A", // orange
          "#A0CBE8", // light blue
          "#ffd500", // "#FFBE7D", // yellow
          "#8CD17D", // light green
          "#B6992D", // dirty yellow
          "#F1CE63", // light yellow
          "#499894", // dirty teal
          "#86BCB6", // light teal
          "#FF9D9A", // coral
          "#D37295", // dirty pink
          "#FABFD2", // light pink
          "#B07AA1", // dirty purple
          "#D4A6C8", // light purple
          "#79706E", // grey
          "#BAB0AC", // light grey
          "#9D7660", // brown
          "#D7B5A6", // light brown
        ],
        gradients: [
          'linear-gradient(to right, #38E5CB, #17B59D)', // green
          'linear-gradient(to right, #ffbf96, #D41330)', // red
          'linear-gradient(to right, #90caf9, #0070CC)', // blue
          'linear-gradient(to right, #da8cff, #9a55ff)', // purple
          'linear-gradient(to right, #FCD8B5, #F28E2A)', // orange
          'linear-gradient(to right, #f6e384, #ffd500)', // yellow
          'linear-gradient(to right, #e7ebf0, #868e96)', // grey
          "#A0CBE8", // light blue
          "#ffd500", // "#FFBE7D", // yellow
        ]
      }
    },
    mounted () {
      this.$emit('legend', this.genLegend(this.chartdata));
      this.renderChart(this.chartdata, this.getChartOptions(this.options));
    },
    methods: {
      genColors(schemeColors) {
        return this.colors;
      },
      getChartOptions(userOptions) {
        if (userOptions) {
          return userOptions;
        }
        return {
          maintainAspectRatio: false,
          responsive: true,
          animation: { animateScale: true, animateRotate: true },
          legend: { display: false },
          plugins: {
            colorschemes: {
              scheme: Tableau20,
              custom: this.genColors
            }
          }
        };
      },
      genLegend(data) {
        var text = [];
        var chart = data.datasets[0];
        text.push('<ul>');
        for (var i = 0; i < chart.data.length; i++) {
          var color = this.gradients[i] ? this.gradients[i] : this.colors[i];
          text.push(
            '<li><span class="legend-dots" style="background:' + color + '"></span>'
          );
          if (data.labels[i]) {
            text.push(
              '<span class="h5">' + data.labels[i] + '</span>'
            );
          }
          text.push(
            '<span class="float-right h5 pr-3">' + chart.totals[i] + '% | ' + chart.data[i] + '</span>'
          );
          text.push('</li>');
        }
        text.push('</ul>');
        return text.join('');
      },
    }
  }
</script>
