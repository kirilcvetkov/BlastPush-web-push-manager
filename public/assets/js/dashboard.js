(function($) {
  'use strict';
  $(function() {

    var cardChartOptions = {
      responsive: true,
      maintainAspectRatio: false,
      legend: {
        display: false
      },
      scales: {
        xAxes: [{
          display: false
        }],
        yAxes: [{
          display: false,
          ticks: {
            beginAtZero: true
          }
        }],
      },
      elements: {
        line: {
          borderWidth: 2
        },
        point: {
          radius: 0,
          hitRadius: 15,
          hoverRadius: 5
        },
      },
      tooltips: {
        mode: 'nearest',
        intersect: false,
      },
      hover: {
        intersect: false
      }
    };

    if ($( '#subscribers-chart' ).length) {
      var chartSubs = new Chart(
        $( '#subscribers-chart' ), {
          type: 'line',
          responsive: true,
          data: {
            labels: [],
            datasets: []
          },
          options: cardChartOptions
        }
      );

      $( '#subscribers-select > a' ).click(function() {
        var timeframe = parseInt($( this ).data('value'));

        $( '.subscribers' ).text('-');
        $( '.subscribers-sub' ).text($( this ).data('label'));

        $.get('/dashboard/subscribers/1/' + timeframe, function( response ) {
          $( '.subscribers' ).text( parseInt( response ).toLocaleString('en'));
        });

        $.get('/dashboard/subscribersChart/1/' + timeframe, function( response ) {
          chartSubs.data.labels = response.labels;
          chartSubs.data.datasets = response.datasets;
          chartSubs.update();
        });
      });

      $( '#subscribers-select > a:first-child' ).click();
    }

    if ($( '#unsubscribers-chart' ).length) {
      var chartUnsubs = new Chart(
        $( '#unsubscribers-chart' ), {
          type: 'line',
          responsive: true,
          data: {
            labels: [],
            datasets: []
          },
          options: cardChartOptions
        }
      );

      $( '#unsubscribers-select > a' ).click(function() {
        var timeframe = parseInt($( this ).data('value'));

        $( '.unsubscribers' ).text('-');
        $( '.unsubscribers-sub' ).text($( this ).data('label'));

        $.get('/dashboard/subscribers/0/' + timeframe, function( response ) {
          $( '.unsubscribers' ).text( parseInt( response ).toLocaleString('en'));
        });

        $.get('/dashboard/subscribersChart/0/' + timeframe, function( response ) {
          chartUnsubs.data.labels = response.labels;
          chartUnsubs.data.datasets = response.datasets;
          chartUnsubs.update();
        });
      });

      $( '#unsubscribers-select > a:first-child' ).click();
    }

    if ($( '#push-chart' ).length) {
      var chartPush = new Chart(
        $( '#push-chart' ), {
          type: 'line',
          responsive: true,
          data: {
            labels: [],
            datasets: []
          },
          options: cardChartOptions
        }
      );

      $( '#push-select > a' ).click(function() {
        var timeframe = parseInt($( this ).data('value'));

        $( '.push' ).text('-');
        $( '.push-sub' ).text($( this ).data('label'));

        $.get('/dashboard/push/' + timeframe, function( response ) {
          $( '.push' ).text( parseInt( response ).toLocaleString('en'));
        });

        $.get('/dashboard/pushChart/' + timeframe, function( response ) {
          chartPush.data.labels = response.labels;
          chartPush.data.datasets = response.datasets;
          chartPush.update();
        });
      });

      $( '#push-select > a:first-child' ).click();
    }

    if ($( '#clicks-chart' ).length) {
      var chartClicks = new Chart(
        $( '#clicks-chart' ), {
          type: 'line',
          responsive: true,
          data: {
            labels: [],
            datasets: []
          },
          options: cardChartOptions
        }
      );

      $( '#clicks-select > a' ).click(function() {
        var timeframe = parseInt($( this ).data('value'));

        $( '.clicks' ).text('-');
        $( '.clicks-sub' ).text($( this ).data('label'));

        $.get('/dashboard/clicks/' + timeframe, function( response ) {
          $( '.clicks' ).text( parseInt( response ).toLocaleString('en'));
        });

        $.get('/dashboard/clicksChart/' + timeframe, function( response ) {
          chartClicks.data.labels = response.labels;
          chartClicks.data.datasets = response.datasets;
          chartClicks.update();
        });
      });

      $( '#clicks-select > a:first-child' ).click();
    }

    if ($( '#events-chart' ).length) {
      Chart.defaults.global.legend.labels.usePointStyle = true;
      var ctx = document.getElementById('events-chart').getContext("2d");
      const colors = createColors(ctx);
      const legend = createColors(ctx, true);

      var trafficChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: [],
          datasets: [
            {
              label: 'Delivered',
              borderColor: colors[4],
              hoverBackgroundColor: colors[4],
              pointHoverBorderWidth: 5,
              legendColor: legend[4],
              data: []
            }, {
              label: 'Clicked',
              borderColor: colors[0],
              hoverBackgroundColor: colors[0],
              legendColor: legend[0],
              data: []
            }, {
              label: 'Closed',
              borderColor: colors[2],
              hoverBackgroundColor: colors[2],
              legendColor: legend[2],
              data: []
            }, {
              label: 'Denied',
              borderColor: colors[1],
              hoverBackgroundColor: colors[1],
              legendColor: legend[1],
              data: []
            }
          ]
        },
        options: {
          responsive: true,
          legend: false,
          legendCallback: function(chart) {
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
          scales: {
            yAxes: [{
              gridLines: {
                drawBorder: false,
                color: 'rgba(235,237,242,1)',
                zeroLineColor: 'rgba(235,237,242,1)'
              },
              ticks: {
                padding: 10,
                precision: 0
              },
            }],
            xAxes: [{
              gridLines: {
                display: false,
                drawBorder: false,
                color: 'rgba(0,0,0,1)',
                zeroLineColor: 'rgba(235,237,242,1)'
              },
              ticks: {
                padding: 10,
                fontColor: "#9c9fa6",
                autoSkip: true,
              },
              categoryPercentage: 0.5,
              barPercentage: 0.5
            }]
          },
          elements: {
            line: {
              borderWidth: 2
            },
            point: {
              radius: 0,
              hitRadius: 25,
              hoverRadius: 5
            },
          },
          tooltips: {
            mode: 'nearest',
            intersect: false,
          },
          hover: {
            intersect: false
          },
        }
      });

      $.get('/dashboard/events', function( response ) {
        trafficChart.data.labels = response.labels;
        trafficChart.data.datasets.forEach(( dataset, index ) => {
          dataset.data = response.data[index];
        });
        trafficChart.update();
        $( '#events-chart-legend' ).html(trafficChart.generateLegend());
      });
    }

    if ($( '#subscriptions-chart' ).length) {
      var ctx = document.getElementById('subscriptions-chart').getContext("2d");

      var chartData = {
        datasets: [{
          data: [],
          backgroundColor: createColors(ctx),
          hoverBackgroundColor: createColors(ctx),
          borderColor: createColors(ctx),
          legendColor: createColors(ctx, true)
        }],

        // These labels appear in the legend and in the tooltips when hovering different arcs
        labels: [
          'Subscribed',
          'Unsubscribed',
        ]
      };

      var doughnutOptions = {
        responsive: true,
        animation: {
          animateScale: true,
          animateRotate: true
        },
        legend: false,
        legendCallback: function(chart) {
          var text = [];
          text.push('<ul>');
          for (var i = 0; i < chart.data.datasets[0].data.length; i++) {
            text.push(
              '<li><span class="legend-dots" style="background:' + chart.data.datasets[0].legendColor[i] + '"></span>'
            );
            if (chart.data.labels[i]) {
              text.push(
                '<span class="h5">' + chart.data.labels[i] + '</span>'
              );
            }
            text.push(
              '<span class="float-right h5 pr-3">' +
                chart.totals[i] + '% | ' +
                chart.data.datasets[0].data[i] +
              '</span>'
            );
            text.push('</li>');
          }
          text.push('</ul>');
          return text.join('');
        },
        plugins: {
          colorschemes: {
            scheme: 'tableau.Classic20'
          }
        }
      };

      var subsChart = new Chart($( '#subscriptions-chart' ).get(0).getContext("2d"), {
        type: 'doughnut',
        data: chartData,
        options: doughnutOptions
      });

      $.get('/dashboard/subscriptions', function( response ) {
        subsChart.data.datasets.forEach((dataset) => {
          dataset.data = response.data;
        });
        subsChart.totals = response.totals;
        subsChart.update();
        $( '#subscriptions-chart-legend' ).html(subsChart.generateLegend());
      });
    }

    if ($( '#browsers-chart' ).length) {
      var ctx = document.getElementById('browsers-chart').getContext("2d");

      var chartData = {
        datasets: [{
          data: [],
          backgroundColor: createColors(ctx),
          hoverBackgroundColor: createColors(ctx),
          borderColor: createColors(ctx),
          legendColor: createColors(ctx, true)
        }],

        // These labels appear in the legend and in the tooltips when hovering different arcs
        labels: [
        ]
      };

      var browsersChart = new Chart($( '#browsers-chart' ).get(0).getContext("2d"), {
        type: 'doughnut',
        data: chartData,
        options: doughnutOptions
      });

      $.get('/dashboard/browsers/0', function( response ) {
        response.data.forEach(( data, index ) => {
          addData(browsersChart, response.label[index], data)
        });
        browsersChart.totals = response.perc;
        $( '#browsers-chart-legend' ).html(browsersChart.generateLegend());
      });
    }

    if ($( '#platform-chart' ).length) {
      var ctx = document.getElementById('platform-chart').getContext("2d");

      var chartData = {
        datasets: [{
          data: [],
          backgroundColor: createColors(ctx),
          hoverBackgroundColor: createColors(ctx),
          borderColor: createColors(ctx),
          legendColor: createColors(ctx, true)
        }],

        // These labels appear in the legend and in the tooltips when hovering different arcs
        labels: [
        ]
      };

      var platformChart = new Chart($( '#platform-chart' ).get(0).getContext("2d"), {
        type: 'doughnut',
        data: chartData,
        options: doughnutOptions
      });

      $.get('/dashboard/browsers/1', function( response ) {
        response.data.forEach(( data, index ) => {
          addData(platformChart, response.label[index], data)
        });
        platformChart.totals = response.perc;
        $( '#platform-chart-legend' ).html(platformChart.generateLegend());
      });
    }

    if ($( '#devices-chart' ).length) {
      var ctx = document.getElementById('devices-chart').getContext("2d");

      var chartData = {
        datasets: [{
          data: [],
          backgroundColor: createColors(ctx),
          hoverBackgroundColor: createColors(ctx),
          borderColor: createColors(ctx),
          legendColor: createColors(ctx, true)
        }],

        // These labels appear in the legend and in the tooltips when hovering different arcs
        labels: [
        ]
      };

      var devicesChart = new Chart($( '#devices-chart' ).get(0).getContext("2d"), {
        type: 'doughnut',
        data: chartData,
        options: doughnutOptions
      });

      $.get('/dashboard/browsers/2', function( response ) {
        response.data.forEach(( data, index ) => {
          addData(devicesChart, response.label[index], data)
        });
        devicesChart.totals = response.perc;
        $( '#devices-chart-legend' ).html(devicesChart.generateLegend());
      });
    }

    if ($( '#inline-datepicker' ).length) {
      $( '#inline-datepicker' ).datepicker({
        enableOnReadonly: true,
        todayHighlight: true,
      });
    }
  });

function addData(chart, label, data) {
  chart.data.labels.push(label);
  chart.data.datasets.forEach((dataset) => {
    dataset.data.push(data);
  });
  chart.update();
}

function createColors(ctx, legendColors = false) {
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
})(jQuery);
