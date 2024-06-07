<template>
  <div class="dashboard-editor-container">

    <panel-group @handleSetLineChartData="handleSetLineChartData" />

    <el-row style="background:#fff;padding:16px 16px 0;margin-bottom:16px;">
      <line-chart :chart-data="lineChartData" />
    </el-row>

    <el-row :gutter="32" style="margin-bottom:32px;">
      <el-col :xs="24" :sm="24" :lg="8">
        <div class="chart-wrapper">
          <!-- <raddar-chart /> -->
          <bar-chart-cost-money :bar-data="this.BarChartData" />
        </div>
      </el-col>
      <!-- <el-col :xs="24" :sm="24" :lg="8">
        <div class="chart-wrapper">
          <pie-chart />
        </div>
      </el-col> -->
      <el-col :xs="24" :sm="24" :lg="16">
        <div class="chart-wrapper">
          <bar-chart-cost-type :bar-data="this.BarTypeData"  />
        </div>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import PanelGroup from './components/PanelGroup'
import LineChart from './components/LineChart'
import RaddarChart from './components/RaddarChart'
import PieChart from './components/PieChart'
import BarChartCostType from './components/BarChartCostType'
import BarChartCostMoney from './components/BarChartCostMoney'
import BarChart from './components/BarChart'
import { getChartData } from '@/api/plan.js'

const lineChartData = {
  newVisitis: {
    expectedData: [100, 120, 161, 134, 105, 160, 165],
    actualData: [120, 82, 91, 154, 162, 140, 145]
  },
  plan_normal_data: {
    // expectedData: [200, 192, 120, 144, 160, 130, 140],
    actualData: [180, 160, 151, 106, 145, 150, 130]
  },
  plan_load_data: {
    expectedData: [80, 100, 121, 104, 105, 90, 100],
    actualData: [120, 90, 100, 138, 142, 130, 130]
  },
  plan_unload_data: {
    chartData: [130, 140, 141, 142, 145, 150, 160],
    actualData: [120, 82, 91, 154, 162, 140, 130]
  }
}

export default {
  name: 'DashboardAdmin',
  components: {
    PanelGroup,
    LineChart,
    RaddarChart,
    PieChart,
    BarChart,
    BarChartCostType,
    BarChartCostMoney
  },
  created() {
    this.getChartData();
  },
  data() {
    return {
      // lineChartData: lineChartData.newVisitis,
      BarChartData: [],
      BarTypeData: {},
      lineChartData: {},
      lineChart: {
        plan_driver_data:{
          actualData: [],
          colorData: []
        },
        plan_normal_data:{
          actualData: [],
          colorData: []
        },
        plan_load_data:{
          actualData: [],
          colorData: []
        },
        plan_unload_data:{
          actualData: [],
          colorData: []
        },
      }
    }
  },
  methods: {
    getChartData() {
      // this.loading = true
      getChartData(this.query).then(response => {
          if(response !== undefined){
            // console.log(response.data)
            // console.log(lineChartData.plan_normal_data.actualData)
            //   this.driver_count = response.data.driver_count
            //   this.plan_normal = response.data.plan_normal
            //   this.lineChartData = response.data.plan_normal_data#ffba00#ff6d6d

              this.lineChartData = {actualData:response.data.plan_normal_data,colorData:['rgb(0, 221, 255)','rgb(77, 119, 255)']}

              this.lineChart.plan_normal_data = {actualData:response.data.plan_normal_data,colorData:['rgb(0, 221, 255)','rgb(77, 119, 255)']}
              this.lineChart.plan_load_data = {actualData:response.data.plan_load_data,colorData:['#f9e764','#dfb005']}
              this.lineChart.plan_unload_data = {actualData:response.data.plan_unload_data,colorData:['#fb9d9d','#e11a1a']}
              // this.lineChart.plan_normal_data.actualData = response.data.plan_normal_data
              // this.lineChart.plan_load_data.actualData = response.data.plan_load_data
              // this.lineChart.plan_unload_data.actualData = response.data.plan_unload_data
              this.BarChartData = response.data.cost_money
              this.BarTypeData = response.data.cost_type
              console.log(this.BarChartData)
              // lineChartData['plan_normal_data'] = this.lineChart.plan_load_data
          }
          // this.loading = false
      })
    },
    handleSetLineChartData(type) {
      // console.log(type)
      // console.log(this.lineChart[type])
      // console.log(lineChartData[type])
      this.lineChartData = this.lineChart[type]
    }
  }
}
</script>

<style lang="scss" scoped>
  .dashboard-editor-container {
    padding: 28px;
    background-color: rgb(240, 242, 245);
    position: relative;

    .github-corner {
      position: absolute;
      top: 0px;
      border: 0;
      right: 0;
    }

    .chart-wrapper {
      background: #fff;
      padding: 16px 16px 0;
      margin-bottom: 16px;
    }
  }

  @media (max-width:1024px) {
    .chart-wrapper {
      padding: 8px;
    }
  }
</style>
