<template>
  <el-row :gutter="40" class="panel-group">
    <el-col :xs="12" :sm="12" :lg="6" class="card-panel-col">
      <div class="card-panel">
        <div class="card-panel-icon-wrapper icon-people">
          <svg-icon icon-class="peoples" class-name="card-panel-icon" />
        </div>
        <div class="card-panel-description">
          <div class="card-panel-text">
            驾驶员
          </div>
          <count-to :start-val="0" :end-val=this.driver_count :duration="2600" class="card-panel-num" />
        </div>
      </div>
    </el-col>
    <el-col :xs="12" :sm="12" :lg="6" class="card-panel-col">
      <div class="card-panel" @click="handleSetLineChartData('plan_normal_data')">
        <div class="card-panel-icon-wrapper icon-message">
          <svg-icon icon-class="truck-box" class-name="card-panel-icon" />
        </div>
        <div class="card-panel-description">
          <div class="card-panel-text">
            运输任务
          </div>
          <count-to :start-val="0" :end-val=this.plan_normal_count :duration="3000" class="card-panel-num" />
        </div>
      </div>
    </el-col>
    <el-col :xs="12" :sm="12" :lg="6" class="card-panel-col">
      <div class="card-panel" @click="handleSetLineChartData('plan_load_data')">
        <div class="card-panel-icon-wrapper icon-money">
          <svg-icon icon-class="truck-arrow-right" class-name="card-panel-icon" />
        </div>
        <div class="card-panel-description">
          <div class="card-panel-text">
            装货任务
          </div>
          <count-to :start-val="0" :end-val=this.plan_load_count :duration="3200" class="card-panel-num" />
        </div>
      </div>
    </el-col>
    <el-col :xs="12" :sm="12" :lg="6" class="card-panel-col">
      <div class="card-panel" @click="handleSetLineChartData('plan_unload_data')">
        <div class="card-panel-icon-wrapper icon-shopping">
          <svg-icon icon-class="truck-arrow-left" class-name="card-panel-icon" />
        </div>
        <div class="card-panel-description">
          <div class="card-panel-text">
            卸货任务
          </div>
          <count-to :start-val="0" :end-val=this.plan_unload_count :duration="3600" class="card-panel-num" />
        </div>
      </div>
    </el-col>
  </el-row>
</template>

<script>
import CountTo from 'vue-count-to'
import { getChartData } from '@/api/plan.js'

export default {
  components: {
    CountTo
  },
  data() {
    return {
      driver_count: 0,
      plan_normal_count: 0,
      plan_load_count: 0,
      plan_unload_count: 0
    }
  },
  created() {
    this.getChartData();
  },
  methods: {
    getChartData() {
      // this.loading = true
      getChartData(this.query).then(response => {
          if(response !== undefined){
            // console.log(response.data)

              this.driver_count = response.data.driver_count
              this.plan_normal_count = response.data.plan_normal_count
              this.plan_load_count = response.data.plan_load_count
              this.plan_unload_count = response.data.plan_unload_count
          }
          // this.loading = false
      })
    },
    handleSetLineChartData(type) {
      this.$emit('handleSetLineChartData', type)
    }
  }
}
</script>

<style lang="scss" scoped>
.panel-group {
  margin-top: 2px;

  .card-panel-col {
    margin-bottom: 16px;
  }

  .card-panel {
    height: 108px;
    cursor: pointer;
    font-size: 12px;
    position: relative;
    overflow: hidden;
    color: #666;
    background: #fff;
    box-shadow: 4px 4px 40px rgba(0, 0, 0, .05);
    border-color: rgba(0, 0, 0, .05);

    &:hover {
      .card-panel-icon-wrapper {
        color: #fff;
      }

      .icon-people {
        background: #40c9c6;
      }

      .icon-message {
        background: #36a3f7;
      }

      .icon-money {
        background: #ffba00;
      }

      .icon-shopping {
        background: #ff6d6d
      }
    }

    .icon-people {
      color: #40c9c6;
    }

    .icon-message {
      color: #36a3f7;
    }

    .icon-money {
      color: #ffba00;
    }

    .icon-shopping {
      color: #ff6d6d
    }

    .card-panel-icon-wrapper {
      float: left;
      margin: 14px 0 0 14px;
      padding: 16px;
      transition: all 0.38s ease-out;
      border-radius: 6px;
    }

    .card-panel-icon {
      float: left;
      font-size: 48px;
    }

    .card-panel-description {
      float: right;
      font-weight: bold;
      margin: 26px;
      margin-left: 0px;

      .card-panel-text {
        line-height: 18px;
        color: rgba(0, 0, 0, 0.45);
        font-size: 16px;
        margin-bottom: 12px;
      }

      .card-panel-num {
        font-size: 20px;
      }
    }
  }
}

@media (max-width:550px) {
  .card-panel-description {
    display: none;
  }

  .card-panel-icon-wrapper {
    float: none !important;
    width: 100%;
    height: 100%;
    margin: 0 !important;

    .svg-icon {
      display: block;
      margin: 14px auto !important;
      float: none !important;
    }
  }
}
</style>
