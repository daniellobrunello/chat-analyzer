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
    xref_x: 0.05
  }

  debugger;
  var user_stats_pie_charts_layout = {
    title: "Text Statistiken",
    grid: {
      rows: 2,
      columns: 2
    },
    annotations: [
      {
        text: "Nachrichten",
        showarrow: false,
        x: 0.4,
        y: 0.8
      }, {
        text: "Wörter",
        showarrow: false,
        x: 0.6,
        y: 0.8
      }, {
        text: "Zeichen",
        showarrow: false,
        x: 0.4,
        y: 0.2
      }, {
        text: "Interpunktion",
        showarrow: false,
        x: 0.6,
        y: 0.2
      }
    ]
  };
  var userStatsPieCharts = Plotly.newPlot('user-text-stats', user_stats_pie_charts, user_stats_pie_charts_layout);


  // Hourly Chat Volume
  var hourly_chat_volume_layout = {
    title: {
      text:'Chatvolumen nach Stunde',
      font: globalSettings.fontTitle,
      xref: globalSettings.xref,
      x: globalSettings.xref_x,
    },
    xaxis: {
      title: {
        text: 'Stunde',
        font: globalSettings.font
      },
    },
    yaxis: {
      title: {
        text: 'Nachrichten',
        font: globalSettings.font
      }
    }
  };
  var hourlyVolume = Plotly.newPlot('chat-volume-hourly', hourly_chat_volume, hourly_chat_volume_layout, globalOptions);


  // Daily Chat Volume
  var daily_chat_volume_layout = {
    title: {
      text:'Chatvolumen nach Wochentag',
      font: globalSettings.fontTitle,
      xref: globalSettings.xref,
      x: globalSettings.xref_x,
    },
    xaxis: {
      title: {
        text: 'Wochentag',
        font: globalSettings.font
      },
    },
    yaxis: {
      title: {
        text: 'Nachrichten',
        font: globalSettings.font
      }
    }
  };
  var dailyVolume = Plotly.newPlot('chat-volume-daily', daily_chat_volume, daily_chat_volume_layout, globalOptions);


  // Monthly Chat Volume
  var monthly_chat_volume_layout = {
    title: {
      text:'Chatvolumen nach Monat',
      font: globalSettings.fontTitle,
      xref: globalSettings.xref,
      x: globalSettings.xref_x,
    },
    xaxis: {
      title: {
        text: 'Monat',
        font: globalSettings.font
      },
    },
    yaxis: {
      title: {
        text: 'Nachrichten',
        font: globalSettings.font
      }
    }
  };
  var monthlyVolume = Plotly.newPlot('chat-volume-monthly', monthly_chat_volume, monthly_chat_volume_layout, globalOptions);


  // Weekday Daytime Heatmap
  var daytime_weekday_heatmap_layout = {
    title: {
      text:'Chat Heatmap Wochentag/Uhrzeit',
      font: globalSettings.fontTitle,
      xref: globalSettings.xref,
      x: globalSettings.xref_x,
    },
    xaxis: {
      title: {
        text: 'Uhrzeit',
        font: globalSettings.font
      },
    },
    yaxis: {
      title: {
        text: 'Wochentag',
        font: globalSettings.font
      }
    }
  };
  var weekdayDaytimeHeatmap = Plotly.newPlot('weekday-daytime-heatmap', daytime_weekday_heatmap, daytime_weekday_heatmap_layout, globalOptions);

  // Response Heatmap
  var response_heatmap_layout = {
    title: {
      text:'Antworten Heatmap',
      font: globalSettings.fontTitle,
      xref: globalSettings.xref,
      x: globalSettings.xref_x,
    },
    xaxis: {
      title: {
        text: 'Nutzer...',
        font: globalSettings.font
      },
    },
    yaxis: {
      title: {
        text: '..antwortet auf',
        font: globalSettings.font
      }
    }
  };
  var responseHeatmap = Plotly.newPlot('response-heatmap', response_heatmap, response_heatmap_layout, globalOptions);
});