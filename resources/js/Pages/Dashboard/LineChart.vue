<script>
import { Line } from 'vue-chartjs'

export default {
  extends: Line,
  props: {
    genColors: { type: Boolean, default: false },
    chartdata: { type: Object, default: null },
    options: { type: Object, default: null },
  },
  mounted () {
    this.renderChart(this.chartdata, this.options);

    if (this.genColors) {
      const colors = this.generateColors(this.$data._chart.ctx);
      const legend = this.generateColors(this.$data._chart.ctx, true);

      this.chartdata.datasets.forEach(( dataset, index ) => {
        dataset.borderColor = colors[index];
        dataset.hoverBackgroundColor = colors[index];
        dataset.legendColor = legend[index];
      });

      this.renderChart(this.chartdata, this.options);
    }

    this.$emit('legend', this.genLegend(this.$data._chart));
  },
  methods: {
    genLegend(chart) {
      var text = [];
      text.push('<ul>');
      for (var i = 0; i < chart.data.datasets.length; i++) {
        text.push(
          '<li><span class="legend-dots" style="background:' + chart.data.datasets[i].legendColor + '"></span>'
        );
        if (chart.data.datasets[i].label) {
          text.push(chart.data.datasets[i].label);
        }
        text.push('</li>');
      }
      text.push('</ul>');
      return text.join('');
    },
    generateColors(ctx, legendColors = false) {
      var gradientStrokeGreen = ctx.createLinearGradient(0, 0, 0, 300);
      gradientStrokeGreen.addColorStop(0, 'rgba(6, 185, 157, 1)');
      gradientStrokeGreen.addColorStop(1, 'rgba(132, 217, 210, 1)');
      var gradientLegendGreen = 'linear-gradient(to right, rgba(6, 185, 157, 1), rgba(132, 217, 210, 1))';

      var gradientStrokeBlue = ctx.createLinearGradient(0, 0, 0, 360);
      gradientStrokeBlue.addColorStop(0, 'rgba(54, 215, 232, 1)');
      gradientStrokeBlue.addColorStop(1, 'rgba(177, 148, 250, 1)');
      var gradientLegendBlue = 'linear-gradient(to right, rgba(54, 215, 232, 1), rgba(177, 148, 250, 1))';

      var gradientStrokeRed = ctx.createLinearGradient(0, 0, 0, 50);
      gradientStrokeRed.addColorStop(0, '#FFBF96');
      gradientStrokeRed.addColorStop(1, '#FE7096');
      var gradientLegendRed = 'linear-gradient(to right, #FFBF96, #FE7096)';

      var gradientStrokePurple = ctx.createLinearGradient(0, 0, 0, 181);
      gradientStrokePurple.addColorStop(0, '#FFF');
      gradientStrokePurple.addColorStop(1, '#FF00FF');
      var gradientLegendPurple = 'linear-gradient(to right, #FFF, #FF00FF)';

      var gradientStroke1 = ctx.createLinearGradient(0, 0, 0, 90);
      gradientStroke1.addColorStop(0, '#7B8F7A');
      gradientStroke1.addColorStop(1, '#FF8F7A');
      var gradientLegend1 = 'linear-gradient(to right, #D38F7A, #FF8F7A)';

      var gradientStroke2 = ctx.createLinearGradient(0, 0, 0, 190);
      gradientStroke2.addColorStop(0, '#837BD0');
      gradientStroke2.addColorStop(1, '#36D7E8');
      var gradientLegend2 = 'linear-gradient(to right, #837BD0, #36D7E8)';

      var gradientStrokeViolet = ctx.createLinearGradient(0, 0, 0, 181);
      gradientStrokeViolet.addColorStop(0, 'rgba(218, 140, 255, 1)');
      gradientStrokeViolet.addColorStop(1, 'rgba(154, 85, 255, 1)');
      var gradientLegendViolet = 'linear-gradient(to right, rgba(218, 140, 255, 1), rgba(154, 85, 255, 1))';

      if (legendColors) {
        return [
          gradientLegendGreen,
          gradientLegendRed,
          gradientLegendBlue,
          gradientLegendPurple,
          gradientLegendViolet,
          gradientLegend1,
          gradientLegend2
        ];
      }
      return [
        gradientStrokeGreen,
        gradientStrokeRed,
        gradientStrokeBlue,
        gradientStrokePurple,
        gradientStrokeViolet,
        gradientStroke1,
        gradientStroke2
      ];
    }
  }
}
</script>
