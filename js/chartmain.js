"use strict"

if (window.location.toString().indexOf('https') === -1) {
    setTimeout(() => {
        window.location.replace(window.location.toString().replace('http://', 'https://'));
    }, 500); 
}



$(function() {
  var globalOptions = {
    responsive: true,
    displayModeBar: false
  };

  var globalSettings = {
    font: {
      family: 'Courier New, monospace',
      size: 18,
      color: '#7f7f7f'
    },
    fontTitle: {
      family: 'Courier New, monospace',
      size: 24
    },
    xref: 'paper',
    xref_x: 0.05,
    paper_bgcolor: 'rgba(0,0,0,0)',
    plot_bgcolor: 'rgba(0,0,0,0)'
  }

  var user_stats_pie_charts_layout = {
    title: "Message Statistics",
    grid: {
      rows: 2,
      columns: 2
    },
    annotations: [
      {
        text: "Messages",
        showarrow: false,
        x: 0.4,
        y: 0.8
      }, {
        text: "Words",
        showarrow: false,
        x: 0.6,
        y: 0.8
      }, {
        text: "Characters",
        showarrow: false,
        x: 0.4,
        y: 0.2
      }, {
        text: "Punctuation",
        showarrow: false,
        x: 0.6,
        y: 0.2
      }
    ],
    paper_bgcolor: globalSettings.paper_bgcolor,
    plot_bgcolor: globalSettings.plot_bgcolor
  };
  var userStatsPieCharts = Plotly.newPlot('user-text-stats', user_stats_pie_charts, user_stats_pie_charts_layout);


  // Hourly Chat Volume
  var hourly_chat_volume_layout = {
    title: {
      text:'Chat volume by hour',
      font: globalSettings.fontTitle,
      xref: globalSettings.xref,
      x: globalSettings.xref_x,
    },
    xaxis: {
      title: {
        text: 'Hour',
        font: globalSettings.font
      },
    },
    yaxis: {
      title: {
        text: 'Messages',
        font: globalSettings.font
      }
    },
    paper_bgcolor: globalSettings.paper_bgcolor,
    plot_bgcolor: globalSettings.plot_bgcolor
  };
  var hourlyVolume = Plotly.newPlot('chat-volume-hourly', hourly_chat_volume, hourly_chat_volume_layout, globalOptions);


  // Daily Chat Volume
  var daily_chat_volume_layout = {
    title: {
      text:'Chat volume by Weekday',
      font: globalSettings.fontTitle,
      xref: globalSettings.xref,
      x: globalSettings.xref_x,
    },
    xaxis: {
      title: {
        text: 'Weekday',
        font: globalSettings.font
      },
    },
    yaxis: {
      title: {
        text: 'Messages',
        font: globalSettings.font
      }
    },
    paper_bgcolor: globalSettings.paper_bgcolor,
    plot_bgcolor: globalSettings.plot_bgcolor
  };
  var dailyVolume = Plotly.newPlot('chat-volume-daily', daily_chat_volume, daily_chat_volume_layout, globalOptions);


  // Monthly Chat Volume
  var monthly_chat_volume_layout = {
    title: {
      text:'Chat volume by month',
      font: globalSettings.fontTitle,
      xref: globalSettings.xref,
      x: globalSettings.xref_x,
    },
    xaxis: {
      title: {
        text: 'Month',
        font: globalSettings.font
      },
    },
    yaxis: {
      title: {
        text: 'Messages',
        font: globalSettings.font
      }
    },
    paper_bgcolor: globalSettings.paper_bgcolor,
    plot_bgcolor: globalSettings.plot_bgcolor
  };
  var monthlyVolume = Plotly.newPlot('chat-volume-monthly', monthly_chat_volume, monthly_chat_volume_layout, globalOptions);


  // Weekday Daytime Heatmap
  var daytime_weekday_heatmap_layout = {
    title: {
      text:'Chat heatmap weekday/hour',
      font: globalSettings.fontTitle,
      xref: globalSettings.xref,
      x: globalSettings.xref_x,
    },
    xaxis: {
      title: {
        text: 'Hour',
        font: globalSettings.font
      },
    },
    yaxis: {
      title: {
        text: 'Weekday',
        font: globalSettings.font
      }
    },
    paper_bgcolor: globalSettings.paper_bgcolor,
    plot_bgcolor: globalSettings.plot_bgcolor
  };
  var weekdayDaytimeHeatmap = Plotly.newPlot('weekday-daytime-heatmap', daytime_weekday_heatmap, daytime_weekday_heatmap_layout, globalOptions);

  // Response Heatmap
  var response_heatmap_layout = {
    title: {
      text:'Responses heatmap',
      font: globalSettings.fontTitle,
      xref: globalSettings.xref,
      x: globalSettings.xref_x,
    },
    xaxis: {
      title: {
        text: 'User...',
        font: globalSettings.font
      },
    },
    yaxis: {
      title: {
        text: '..answers to',
        font: globalSettings.font
      }
    },
    paper_bgcolor: globalSettings.paper_bgcolor,
    plot_bgcolor: globalSettings.plot_bgcolor
  };
  var responseHeatmap = Plotly.newPlot('response-heatmap', response_heatmap, response_heatmap_layout, globalOptions);
});