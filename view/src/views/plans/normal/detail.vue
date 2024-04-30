<template>
    <el-drawer
            v-if="drawerShow"
            :before-close="handleClose"
            :with-header="false"
            :wrapperClosable="false"
            :visible.sync="dialog"
            size="50%"
            direction="rtl"
            custom-class="demo-drawer"
            ref="drawer"
    >
        <div class="demo-drawer__content" style="padding: 10px">
            <h3 style="margin: 7px 0px;font-weight: 600;font-size: 20px;" v-text="title"></h3>
            <!-- <el-tag type="info" style="height: 40px;font-size: 15px;line-height: 40px;">周期编号：{{formData.period_id}}</el-tag> -->
            <el-descriptions class="margin-top" title="" :column="3" :size="size" border>
              <!-- <template slot="extra">
                {{formData.period_id}}
              </template> -->
              <el-descriptions-item><template slot="label">周期编号</template>{{formData.period_id}}</el-descriptions-item>
              <el-descriptions-item><template slot="label">车头</template>{{formData.head_num}}</el-descriptions-item>
              <el-descriptions-item><template slot="label">挂车</template>{{formData.trailer_num}}</el-descriptions-item>
              <el-descriptions-item><template slot="label">驾驶员</template>{{formData.driver_name}}</el-descriptions-item>
              <el-descriptions-item><template slot="label">押运员</template>{{formData.escort_name}}</el-descriptions-item>
              <el-descriptions-item><template slot="label">装货厂家</template>{{formData.load_factory}}</el-descriptions-item>
              <el-descriptions-item><template slot="label">装货地址</template>{{formData.load_address}}</el-descriptions-item>
              <el-descriptions-item><template slot="label">卸货厂家</template>{{formData.unload_factory}}</el-descriptions-item>
              <el-descriptions-item><template slot="label">卸货地址</template>{{formData.unload_address}}</el-descriptions-item>
              <el-descriptions-item><template slot="label">货品名称</template>{{formData.product_name}}</el-descriptions-item>
              <el-descriptions-item><template slot="label">货品数量</template>{{formData.product_quantity}}</el-descriptions-item>
              <el-descriptions-item><template slot="label">里程数</template>{{formData.mileage}}</el-descriptions-item>
              <el-descriptions-item><template slot="label">柴油</template>{{formData.diesel_oil}}</el-descriptions-item>
              <el-descriptions-item><template slot="label">里程数</template>{{formData.mileage}}</el-descriptions-item>
              <el-descriptions-item><template slot="label">装货预计等待时长</template>{{formData.load_waiting_time}}</el-descriptions-item>
              <el-descriptions-item><template slot="label">装货备注</template>{{formData.load_waiting_remark}}</el-descriptions-item>
              <el-descriptions-item><template slot="label">装货检斤单</template>{{formData.load_weight_inspection}}</el-descriptions-item>
              <el-descriptions-item><template slot="label">装货数量</template>{{formData.load_product_quantity}}</el-descriptions-item>
              <el-descriptions-item><template slot="label">卸货预计等待时长</template>{{formData.unload_wait_time}}</el-descriptions-item>
              <el-descriptions-item><template slot="label">卸货备注</template>{{formData.unload_wait_remark}}</el-descriptions-item>
              <el-descriptions-item><template slot="label">卸货检斤单</template>{{formData.unload_weight_inspection}}</el-descriptions-item>
              <el-descriptions-item><template slot="label">卸货数量</template>{{formData.unload_product_quantity}}</el-descriptions-item>

              <el-descriptions-item><template slot="label">回库里程数</template>{{formData.back_mileage_img}}</el-descriptions-item>
              <el-descriptions-item><template slot="label">回库里程数</template>{{formData.back_mileage}}</el-descriptions-item>
              <el-descriptions-item><template slot="label">创建日期</template>{{formData.create_time}}</el-descriptions-item>
              <el-descriptions-item><template slot="label">更新日期</template>{{formData.update_time}}</el-descriptions-item>
            </el-descriptions>
            <!-- <el-descriptions  direction="vertical" :column="4" border>
                <el-descriptions-item label="车头">{{formData.head_num}}</el-descriptions-item>
                <el-descriptions-item label="挂车">{{formData.trailer_num}}</el-descriptions-item>
                <el-descriptions-item label="驾驶员">{{formData.driver_name}}</el-descriptions-item>
                <el-descriptions-item label="押运员">{{formData.escort_name}}</el-descriptions-item>
                <el-descriptions-item label="装货厂家">{{formData.load_factory}}</el-descriptions-item>
                <el-descriptions-item label="装货地址">{{formData.load_address}}</el-descriptions-item>
                <el-descriptions-item label="卸货厂家">{{formData.unload_factory}}</el-descriptions-item>
                <el-descriptions-item label="卸货地址">{{formData.unload_address}}</el-descriptions-item>
                <el-descriptions-item label="货品名称">{{formData.product_name}}</el-descriptions-item>
                <el-descriptions-item label="货品数量">{{formData.product_quantity}}</el-descriptions-item>
                <el-descriptions-item label="里程数">{{formData.mileage}}</el-descriptions-item>
                <el-descriptions-item label="柴油">{{formData.diesel_oil}}</el-descriptions-item>
                <el-descriptions-item label="里程数">{{formData.mileage}}</el-descriptions-item>
                <el-descriptions-item label="装货预计等待时长">{{formData.load_waiting_time}}</el-descriptions-item>
                <el-descriptions-item label="装货备注">{{formData.load_waiting_remark}}</el-descriptions-item>
                <el-descriptions-item label="装货检斤单">{{formData.load_weight_inspection}}</el-descriptions-item>
                <el-descriptions-item label="装货数量">{{formData.load_product_quantity}}</el-descriptions-item>
                <el-descriptions-item label="卸货预计等待时长">{{formData.unload_wait_time}}</el-descriptions-item>
                <el-descriptions-item label="卸货备注">{{formData.unload_wait_remark}}</el-descriptions-item>
                <el-descriptions-item label="卸货检斤单">{{formData.unload_weight_inspection}}</el-descriptions-item>
                <el-descriptions-item label="卸货数量">{{formData.unload_product_quantity}}</el-descriptions-item>
                <el-descriptions-item label="回库里程数">{{formData.back_mileage_img}}</el-descriptions-item>
                <el-descriptions-item label="回库里程数">{{formData.back_mileage}}</el-descriptions-item>
                <el-descriptions-item label="创建日期">{{formData.create_time}}</el-descriptions-item>
                <el-descriptions-item label="更新日期">{{formData.update_time}}</el-descriptions-item>
            </el-descriptions> -->
            <div class="demo-drawer__footer" style="position:fixed;top:15px;right:30px;">
                <el-button size="mini" @click="$refs.drawer.closeDrawer()">关 闭</el-button>
                <!-- <el-button size="mini" type="primary" @click="saveData()">确 定
                </el-button> -->
            </div>
        </div>
    </el-drawer>
</template>

<script>

import { getnormalinfo} from '@/api/plan.js'
import UploadImage from '@/components/Upload/SingleImage'
import MultiImage from '@/components/Upload/MultiImage'



export default {
  name: "Detail",
  components: {
    UploadImage,
    MultiImage
  },
  data() {
    return {
      title:'',
      dialog: false,
      carhead_scope: [],
      driving_license: [],
      drawerShow:false,
      saveRules: {
        carhead_plate: [{ required: true, trigger: 'blur'}],
        transport_cert: [{ required: true, trigger: 'blur'}]
      },
      formData: {
        id: 0,
        head_num: '',
        trailer_num: '',
        driver_name: '',
        escort_name: '',
        load_factory: [],
        load_address: '',
        unload_address: '',
        unload_factory: '',
        edit_unload_factory: '',
        product_quantity: '',
        product_name:'',
        mileage:'',
        diesel_oil:'',
        mileage_img:'',
        load_waiting_time:'',
        create_time: '',
        update_time: ''
      },
    }
  },

  methods: {

    handleClose() {
      this.dialog = false
      this.drawerShow = false
    },
    showDetail() {
        
      this.dialog = true
      this.drawerShow = true
      this.title = '任务详情'
  
    },
    getnormalinfo(id){
      getnormalinfo({id:id}).then(response=>{

          if(response !== undefined){
              this.title = '任务详情'
              this.formData.id = response.id
              this.formData.head_num = response.head_num
              this.formData.trailer_num = response.trailer_num
              this.formData.driver_name = response.driver_name
              this.formData.escort_name = response.escort_name
              this.formData.load_factory = response.load_factory
              this.formData.load_address = response.load_address
              this.formData.unload_address = response.unload_address
              this.formData.unload_factory = response.unload_factory
              this.formData.edit_unload_factory = response.edit_unload_factory
              this.formData.product_name = response.product_name
              this.formData.product_quantity = response.product_quantity
              this.formData.mileage = response.mileage
              this.formData.diesel_oil = response.diesel_oil
              this.formData.load_waiting_time = response.load_waiting_time
              this.formData.load_waiting_remark = response.load_waiting_remark
              this.formData.load_weight_inspection = response.load_weight_inspection
              this.formData.load_product_quantity = response.load_product_quantity
              this.formData.unload_wait_time = response.unload_wait_time
              this.formData.unload_wait_remark = response.unload_wait_remark
              this.formData.unload_weight_inspection = response.unload_weight_inspection
              this.formData.unload_product_quantity = response.unload_product_quantity
              this.formData.back_mileage = response.back_mileage
              this.formData.driver_status = response.driver_status
              this.formData.period_id = response.period_id
          }
      })
    }
  }
}
</script>

<style scoped lang="scss">
    ::v-deep .el-tabs__item:focus.is-active.is-focus:not(:active) {
        -webkit-box-shadow: none;
        box-shadow: none;
    }
    ::v-deep .custom-descriptions .el-descriptions-item {
        width: 200px; /* 设置固定宽度 */
    }
</style>
