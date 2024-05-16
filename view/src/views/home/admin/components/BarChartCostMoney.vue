<template>
    <div :class="className" :style="{height:height,width:width}" />
  </template>
  
  <script>
  import echarts from '@/utils/echarts'
  import resize from './mixins/resize'
  
  const animationDuration = 6000
  
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
        default: '300px'
      },
      barData: {
        type: Array,
        required: true
      }
    },
    data() {
      return {
        chart: null
      }
    },
    watch: {
      barData: {
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
        this.setOptions(this.barData)
        console.log(this.barData)
      },
      setOptions(barData = {}) {
        console.log(barData)
        this.chart.setOption({
           title: {
            text: '本年度费用统计'
          },
          tooltip: {
            trigger: 'axis',
            axisPointer: { // 坐标轴指示器，坐标轴触发有效
              type: 'shadow' // 默认为直线，可选为：'line' | 'shadow'
            }
          },
          grid: {
            top: 40,
            left: '2%',
            right: '2%',
            bottom: '3%',
            containLabel: true
          },
          xAxis: [{
            type: 'category',
            data: ['1月', '2月', '3月', '4月', '5月', '6月', '7月','8月', '9月', '10月', '11月', '12月'],
            axisTick: {
              alignWithLabel: true
            }
          }],
          yAxis: [{
            type: 'value',
            axisTick: {
              show: false
            }
          }],
          series: [{
            name: '总费用',
            type: 'bar',
            stack: 'vistors',
            barWidth: '60%',
            data: barData,
            itemStyle: {
                // 设置渐变色
                normal: {
                color: {
                    type: 'linear',
                    x: 0,
                    y: 0,
                    x2: 0,
                    y2: 1,
                    colorStops: [{
                    offset: 0, color: 'rgb(0, 221, 255)' // 0% 处的颜色
                    }, {
                    offset: 1, color: 'rgb(77, 119, 255)' // 100% 处的颜色
                    }],
                    global: false // 缺省为 false
                },
                }
            },
            animationDuration
          }]
        })
      }
    }
  }
  </script>
  