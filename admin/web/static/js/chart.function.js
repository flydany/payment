/**
 * init chart common function js
 * 公用js函数定义
 */

var chart_option = {chart: {type: 'column'}, title: {text: ''}, subtitle: {text: '单位：万元', align: 'left'}, plotOptions: {serials: {dataLabels: {enabled: false}}}, exporting: {enabled: true}, yAxis: {title: {text: ''}}, legend: {enabled: false}, credits: {enabled: false}};
function extend_option(n, o, override)
{
  var new_opt = o;
  for(var p in n) {
    if( ! new_opt.hasOwnProperty(p)) {
      new_opt[p]=n[p];
    }
  }
  return new_opt;
}
function unit_set(type)
{
  var  unit = {};
  switch (type) {
    case 'investor': case 'member-count': case 'total-member-count':
      unit.unit = '人';
      unit.ext = ' 人';
    break;
  
    case 'full-scale-time':
      unit.unit = '元/秒';
      unit.ext = ' 元/秒';
    break;
    
    case 'volume': case 'member-volume': case 'money': case 'loan-rate': case 'regist-tender':
    default:
      unit.unit = '万元';
      unit.ext = ' 万';
  }
  return unit;
}

function init_index(url, id, sub_id, unit)
{
  $.get(url, function(ret_data) {
    var data = ret_data; // $.parseJSON(ret_data);
    ret_data = null;
    if (data.code == 200) {
      var ext_option = {
        subtitle: {
          text: '单位：' + unit.unit,
          align: 'left'
        },
        xAxis: {
          categories: data.data.categories,
          lineWidth: '2',
          lineColor: '#1c9090',
          tickWidth: '2',
          tickColor: '#1c9090'
        },
        plotOptions: {
          column: {
            cursor: 'pointer',
            point: {
              events: {
                click: function() {
                  var drilldown = this.drilldown;
                  if (drilldown) {
                    setNewChart([drilldown], sub_id, unit);
                  }
                }
              }
            },
            dataLabels: {
              enabled: true,
              color: '#1c9090',
              style: {
                fontWeight: 'bold',
              },
              formatter: function() {
                return this.y + ' ' + unit.ext;
              }
            }
          }
        },
        tooltip: {
          formatter: function() {
            var point = this.point,
              s = this.x +': <b>'+ this.y +' ' + unit.ext + '</b><br/>';
              if (this.drilldown) {
                s += '点击查看 '+ point.category +' 每月分布';
              }
            return s;
          }
        },
        series: [{
          data: data.data.data
        }]
      };
      var volume_chart = $(id).highcharts(extend_option(chart_option, ext_option)).highcharts();
      $(id).data('status', 'success');
    }
    else {
      load_error(id, data.msg);
      $(id).data('status', 'error');
    }
  });
}
/* ---------------------------------------------------
 *  总统计的柱图点击事件
 * --------------------------------------------------- */
function setNewChart(data, sub_id, unit)
{
  $('.chart-window').animate({'height': '1000px'}, 'fast', function() {
    document.getElementById('pg-footer').scrollIntoView();
  });
  var ext_option = {
    chart: {
      type: 'spline'
    },
    subtitle: {
      text: '单位：' + unit.unit,
      align: 'left'
    },
    legend: {
      enabled: true,
      align: 'right'
    },
    rangeSelector: {
      inputDateFormat: time_format,
      selected: 3
    },
    yAxis: {
      labels: {
        formatter: function () {
          return this.value + ' ' + unit.ext;
        }
      },
      plotLines: [{
        value: 0,
        width: 2,
        color: 'silver'
      }],
      min: 0
    },
    plotOptions: {
      series: {
        compare: ''
      }
    },
    tooltip: {
      pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} ' + unit.ext + '</b><br/>',
      dateTimeLabelFormats: {
        millisecond: time_format,
        second: time_format,
        minute: time_format,
        hour: time_format,
        day: time_format,
        week: time_format,
        month: '%Y-%m',
        year: '%Y',
      }
    },
    series: data
  };
  $(sub_id).highcharts('StockChart', extend_option(chart_option, ext_option));
}
/* ---------------------------------------------------
 *  折线图初始化 分布统计
 * --------------------------------------------------- */
function drow_line(url, id, unit, time_format)
{
  $.get(url, function(ret_data) {
    var data = ret_data; // $.parseJSON(ret_data);
    ret_data = null;
    if (data.code == 200) {
      var ext_option = {
        chart: {
          type: 'spline'
        },
        subtitle: {
          text: '单位：' + unit.unit,
          align: 'left'
        },
        legend: {
          enabled: true,
          align: 'right'
        },
        tooltip: {
          valueSuffix: unit.ext,
          dateTimeLabelFormats: {
            millisecond: time_format,
            second: time_format,
            minute: time_format,
            hour: time_format,
            day: time_format,
            week: time_format,
            month: '%Y-%m',
            year: '%Y',
          }
        },
        rangeSelector: {
          inputDateFormat: time_format,
          selected: 3
        },
        xAxis: {
          categories: data.data.categories,
          lineWidth: '2',
          lineColor: '#1c9090',
          tickWidth: '2',
          tickColor: '#1c9090',
          tickInterval: data.data.step
        },
        yAxis: {
          title: {
            text: ''
          },
          plotLines: [{
            value: 0,
            width: 2,
            color: '#808080'
          }],
          min: 0
        },
        series: data.data.data
      };
      $(id).highcharts(extend_option(chart_option, ext_option));
      $(id).data('status', 'success');
    }
    else {
      load_error(id, data.msg);
      $(id).data('status', 'error');
    }
  });
}
/* ---------------------------------------------------
 *  柱状图初始化 分布统计
 * --------------------------------------------------- */
function drow_column(url, id, unit)
{
  $.get(url, function(ret_data) {
    var data = ret_data; // $.parseJSON(ret_data);
    ret_data = null;
    if (data.code == 200) {
      var ext_option = {
        chart: {
          type: 'column'
        },
        subtitle: {
          text: '单位：' + unit.unit,
          align: 'left'
        },
        legend: {
          enabled: true,
          align: 'right'
        },
        tooltip: {
          valueSuffix: unit.ext,
        },
        xAxis: {
          categories: data.data.categories,
          lineWidth: '2',
          lineColor: '#1c9090',
          tickWidth: '2',
          tickColor: '#1c9090',
          // tickInterval: data.data.step
        },
        yAxis: {
          title: {
            text: ''
          },
          plotLines: [{
            value: 0,
            width: 2,
            color: '#808080'
          }],
          min: 0
        },
        series: data.data.data
      };
      $(id).highcharts(extend_option(chart_option, ext_option));
      $(id).data('status', 'success');
    }
    else {
      load_error(id, data.msg);
      $(id).data('status', 'error');
    }
  });
}

/* -----------------------------------------------------------------------------------------------
 *  可选时间段的曲线图初始化 分布统计
 * ----------------------------------------------------------------------------------------------- */
function drow_stock(url, id, unit, time_format)
{
    $.get(url, function(ret_data) {
    var data = ret_data; // $.parseJSON(ret_data);
    ret_data = null;
    if (data.code == 200) {
      var ext_option = {
        chart: {
          type: 'spline'
        },
        subtitle: {
          text: '单位：' + unit.unit,
          align: 'left'
        },
        legend: {
          enabled: true,
          align: 'right'
        },
        rangeSelector: {
          inputDateFormat: time_format,
          selected: 3
        },
        yAxis: {
          labels: {
            formatter: function () {
              return this.value + unit.ext;
            },
            x: 0,
            align: 'left'
          },
          plotLines: [{
            value: 0,
            width: 2,
            color: 'silver'
          }],
          min: 0
        },
        tooltip: {
          pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} ' + unit.ext + '</b><br/>',
          dateTimeLabelFormats: {
            millisecond: time_format,
            second: time_format,
            minute: time_format,
            hour:time_format,
            day: time_format,
            week: time_format,
            month: '%Y-%m',
            year: '%Y',
          }
        },
        series: data.data.data
      };
      $(id).highcharts('StockChart', extend_option(chart_option, ext_option));
      $(id).data('status', 'success');
    }
    else {
      load_error(id, data.msg);
      $(id).data('status', 'error');
    }
    });
}
