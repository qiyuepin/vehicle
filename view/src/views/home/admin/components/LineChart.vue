<template>
  <div :class="className" :style="{height:height,width:width}" />
</template>

<script>
import echarts from '@/utils/echarts'
import resize from './mixins/resize'

export default {
  mixins: [resize],
  props: {
    className: {
      type: String,
      default: 'chart'
    },
    width: {
      type: String,
      default: '100%'
    },
    height: {
      type: String,
      default: '350px'
    },
    autoResize: {
      type: Boolean,
      default: true
    },
    chartData: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      chart: null
    }
  },
  watch: {
    chartData: {
      deep: true,
      handler(val) {
        this.setOptions(val)
      }
    }
  },
  mounted() {
    this.$nextTick(() => {
      this.initChart()
    })
  },
  beforeDestroy() {
    if (!this.chart) {
      return
    }
    this.chart.dispose()
    this.chart = null
  },
  methods: {
    initChart() {
      this.chart = echarts.init(this.$el)
      this.setOptions(this.chartData)
      console.log(this.chartData)
    },
    setOptions({ colorData, actualData } = {}) {
      console.log(colorData)
      this.chart.setOption({
        title: {
          text: '本年度任务数量统计'
        },
        xAxis: {
          data: ['1月', '2月', '3月', '4月', '5月', '6月', '7月','8月', '9月', '10月', '11月', '12月'],
          boundaryGap: true,
          axisTick: {
            show: true
          }
        },
        grid: {
          left: 10,
          right: 10,
          bottom: 20,
          top: 40,
          containLabel: true
        },
        tooltip: {
          trigger: 'axis',
          axisPointer: {
            type: 'cross'
          },
          padding: [5, 10]
        },
        yAxis: {
          axisTick: {
            show: false
          }
        },
        legend: {
          data: ['expected', '任务数量']
        },
        series: [
        //   {
        //   name: 'expected', itemStyle: {
        //     normal: {
        //       color: '#FF005A',
        //       lineStyle: {
        //         color: '#FF005A',
        //         width: 2
        //       }
        //     }
        //   },
        //   smooth: true,
        //   type: 'line',
        //   data: expectedData,
        //   animationDuration: 2800,
        //   animationEasing: 'cubicInOut'
        // },
        {
          name: '任务数量',
          smooth: true,
          type: 'line',
          itemStyle: {
            normal: {
              color: colorData[0],
              lineStyle: {
                color: colorData[0],
                width: 1
              }
            }
          },
          showSymbol: false,
          areaStyle: {
            opacity: 0.8,
            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
              {
                offset: 0,
                color: colorData[0]
              },
              {
                offset: 1,
                color: colorData[1]
              }
            ])
          },
          emphasis: {
            focus: 'series'
          },
          data: actualData,
          animationDuration: 2800,
          animationEasing: 'quadraticOut'
        }]
      })
    }
  }
}
</script>
