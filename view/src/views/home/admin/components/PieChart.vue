<template>
  <div :class="className" :style="{height:height,width:width}" />
</template>

<script>
import echarts from '@/utils/echarts'
import resize from './mixins/resize'
import { getChartData } from '@/api/plan.js'

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
    }
  },
  data() {
    return {
      chart: null,
      plan_normal_count: null,
      plan_load_count: null,
      plan_unload_count: null
    }
  },
  created() {
    this.getChartData();
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
    getChartData() {
      // this.loading = true
      getChartData(this.query).then(response => {
          if(response !== undefined){
            // console.log(response.data.plan_normal_count)
              this.plan_normal_count = response.data.plan_normal_count
              this.plan_load_count = response.data.plan_load_count
              this.plan_unload_count = response.data.plan_unload_count
          }
          // this.loading = false
      })
    },
    initChart() {
      this.chart = echarts.init(this.$el)
      getChartData(this.query).then(response => {
          if(response !== undefined){
            // console.log(response.data.plan_normal_count)
              this.plan_normal_count = response.data.plan_normal_count
              this.plan_load_count = response.data.plan_load_count
              this.plan_unload_count = response.data.plan_unload_count
          }
          // this.loading = false
          this.chart.setOption({
            tooltip: {
              trigger: 'item',
              formatter: '{a} <br/>{b} : {c} ({d}%)'
            },
            legend: {
              left: 'center',
              bottom: '10',
              data: ['运输任务', '装货任务', '卸货任务']
            },
            series: [
              {
                name: 'WEEKLY WRITE ARTICLES',
                type: 'pie',
                roseType: 'radius',
                radius: [15, 95],
                center: ['50%', '38%'],
                data: [
                  { value: this.plan_normal_count, name: '运输任务' },
                  { value: this.plan_load_count, name: '装货任务' },
                  { value: this.plan_unload_count, name: '卸货任务' }
                ],
                animationEasing: 'cubicInOut',
                animationDuration: 2600
              }
            ]
          })
      })
      // console.log(this.plan_normal_count)
      
    }
  }
}
</script>
