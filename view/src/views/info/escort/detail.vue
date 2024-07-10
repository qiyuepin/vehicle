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
            <!-- <el-form ref="saveForm" :model="formData" :rules="saveRules" size="small" label-position="right"
                     label-width="110px"
                     style="width: 100%;">
                <el-tabs style="height: 200px;">
                    <el-tab-pane label="基本信息">

                        <el-form-item label="车牌号" prop="carhead_plate">
                            <el-input v-model="formData.carhead_plate" clearable disabled></el-input>
                        </el-form-item>
                        <el-form-item label="品牌" prop="carhead_brand">
                            <el-input v-model="formData.carhead_brand" clearable disabled></el-input>
                        </el-form-item>
                        <el-form-item label="自重" prop="carhead_weight">
                            <el-input v-model="formData.carhead_weight" clearable disabled></el-input>
                        </el-form-item>
                        <el-form-item label="道路运输证号">
                            <el-input v-model="formData.transport_cert" clearable disabled></el-input>
                        </el-form-item>
                        <el-form-item label="经营范围" prop="carhead_scope">
                            <el-checkbox-group v-model="formData.carhead_scope">
                                <el-checkbox v-for="item in carhead_scope" :key="item.id" :label="item.id" disabled>{{item.name}}</el-checkbox>
                            </el-checkbox-group>
                        </el-form-item>
                        <el-form-item label="注册日期">
                            <el-input v-model="formData.regist_time" type="date" clearable disabled></el-input>
                        </el-form-item>
                        <el-form-item label="强制报废日期">
                          <el-input v-model="formData.scrapp_time" type="date" clearable disabled></el-input>
                        </el-form-item>
                        <el-form-item label="检验有效期">
                          <el-input v-model="formData.inspection_time" type="date" clearable disabled></el-input>
                        </el-form-item>
                        <el-form-item label="审验有效期">
                          <el-input v-model="formData.validity_time" type="date" clearable disabled></el-input>
                        </el-form-item>
                        <el-form-item label="交强险有效期">
                          <el-input v-model="formData.traffic_time" type="date" clearable disabled></el-input>
                        </el-form-item>
                        <el-form-item label="行驶证" prop="driving_license">
                            <MultiImage ref="Image_driving_license":images="formData.driving_license" v-model="formData.driving_license" disabled></MultiImage>
                        </el-form-item>
                        <el-form-item label="道路运输证" prop="transport_license">
                            <UploadImage ref="Image_transport_license" v-model="formData.transport_license" readonly></UploadImage>
                        </el-form-item>
                        <el-form-item label="交强险保单" prop="traffic_insurance">
                            <UploadImage ref="Image_traffic_insurance" v-model="formData.traffic_insurance" readonly></UploadImage>
                        </el-form-item>
                        <el-form-item label="商业险保单" prop="business_insurance">
                            <Image ref="Image_business_insurance" v-model="formData.business_insurance" disabled></Image>
                        </el-form-item>

                    </el-tab-pane>
                </el-tabs>
            </el-form> -->

            <el-descriptions  direction="vertical" :column="4" border>
                <el-descriptions-item label="车牌号" class="custom-descriptions">{{formData.carhead_plate}}</el-descriptions-item>
                <el-descriptions-item label="品牌" class="custom-descriptions">{{formData.carhead_plate}}</el-descriptions-item>
                <el-descriptions-item label="自重" class="custom-descriptions">{{formData.carhead_weight}}</el-descriptions-item>
                <el-descriptions-item label="道路运输证号" class="custom-descriptions">{{formData.transport_cert}}</el-descriptions-item>
                <el-descriptions-item label="经营范围">{{formData.carhead_scope_name}}</el-descriptions-item>
                <el-descriptions-item label="注册日期">{{formData.regist_time}}</el-descriptions-item>
                <el-descriptions-item label="强制报废日期">{{formData.scrapp_time}}</el-descriptions-item>
                <el-descriptions-item label="检验有效期">{{formData.inspection_time}}</el-descriptions-item>
                <el-descriptions-item label="审验有效期">{{formData.validity_time}}</el-descriptions-item>
                <el-descriptions-item label="交强险有效期">{{formData.traffic_time}}</el-descriptions-item>
                <el-descriptions-item label="创建日期">{{formData.create_time}}</el-descriptions-item>
                <el-descriptions-item label="更新日期">{{formData.update_time}}</el-descriptions-item>
            
                <el-descriptions-item label="行驶证">
                    <div class="demo-image__preview">
                    <el-image 
                        style="width: 100px; height: 100px"
                        :src="driving_license[0]" 
                        :preview-src-list="driving_license">
                    </el-image>
                    </div>
                </el-descriptions-item>
                <el-descriptions-item label="道路运输证">
                    <div class="demo-image__preview">
                    <el-image 
                        style="width: 100px; height: 100px"
                        :src="formData.transport_license[0]" 
                        :preview-src-list="formData.transport_license">
                    </el-image>
                    </div>
                </el-descriptions-item>
                <el-descriptions-item label="交强险保单">
                    <div class="demo-image__preview">
                    <el-image 
                        style="width: 100px; height: 100px"
                        :src="formData.traffic_insurance[0]" 
                        :preview-src-list="formData.traffic_insurance">
                    </el-image>
                    </div>
                </el-descriptions-item>
                <el-descriptions-item label="商业险保单">
                    <div class="demo-image__preview">
                    <el-image 
                        style="width: 100px; height: 100px"
                        :src="formData.business_insurance[0]" 
                        :preview-src-list="formData.business_insurance">
                    </el-image>
                    </div>
                </el-descriptions-item>
            </el-descriptions>
            <div class="demo-drawer__footer" style="position:fixed;top:15px;right:30px;">
                <el-button size="mini" @click="$refs.drawer.closeDrawer()">关 闭</el-button>
                <!-- <el-button size="mini" type="primary" @click="saveData()">确 定
                </el-button> -->
            </div>
        </div>
    </el-drawer>
</template>

<script>

import { getcarheadInfo, getcarscope} from '@/api/Info.js'
import UploadImage from '@/components/Upload/SingleImage'
import MultiImage from '@/components/Upload/MultiImage'
import noImage from '@/assets/no_images/none.png';


export default {
  name: "Detail",
  components: {
    UploadImage,
    MultiImage
  },
  data() {
    return {
      noImage,
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
        carhead_plate: '',
        carhead_brand: '',
        carhead_weight: '',
        transport_cert: '',
        carhead_scope: [],
        regist_time: '',
        scrapp_time: '',
        inspection_time: '',
        validity_time: '',
        traffic_time: '',
        driving_license: [],
        transport_license: [],
        traffic_insurance: [],
        business_insurance: [],
        create_time: '',
        update_time: ''
      },
    }
  },
  created() {
    this.getcarscope()
  },
  methods: {
    getcarscope(){
      getcarscope().then(response=>{
          if(response !== undefined){
            console.log(response.data)
            this.carhead_scope = response.data;
          }
      })
    },
    handleClose() {
      this.dialog = false
      this.drawerShow = false
    },
    showDetail() {
        
      this.dialog = true
      this.drawerShow = true
      this.title = '车头信息详情'
      this.resetData()
    },
    resetData(){
      this.formData.id = 0
      this.formData.carhead_plate = ''
      this.formData.carhead_brand = ''
      this.formData.carhead_weight = ''
      this.formData.transport_cert = ''
      this.formData.carhead_scope = []
      this.formData.regist_time = ''
      this.formData.scrapp_time = ''
      this.formData.inspection_time = ''
      this.formData.validity_time = ''
      this.formData.traffic_time = ''
      this.formData.driving_license = []
      this.formData.transport_license = []
      this.formData.traffic_insurance = []
      this.formData.business_insurance = []
      this.create_time = ''
      this.update_time = ''
    },
    getcarheadInfo(id){
      getcarheadInfo({id:id}).then(response=>{
        console.log(response.transport_license)
          if(response !== undefined){
              this.title = '车头信息详情'
              this.formData.id = response.id
              this.formData.carhead_plate = response.carhead_plate
              this.formData.carhead_brand = response.carhead_brand
              this.formData.carhead_weight = response.carhead_weight
              this.formData.transport_cert = response.transport_cert
              // this.formData.carhead_scope = response.carhead_scope
              // response.scope.forEach((item,_) => {
              //     this.formData.carhead_scope.push(item)
              // })
              
              this.formData.carhead_scope_name = response.carhead_scope_name
            //   this.formData.carhead_scope.push(...response.carhead_scope);
              this.formData.regist_time = new Date(response.regist_time).toISOString().slice(0,10)
              this.formData.scrapp_time = new Date(response.scrapp_time).toISOString().slice(0,10)
              this.formData.inspection_time = new Date(response.inspection_time).toISOString().slice(0,10)
              this.formData.validity_time = new Date(response.validity_time).toISOString().slice(0,10)
              this.formData.traffic_time = new Date(response.traffic_time).toISOString().slice(0,10)
              this.driving_license = response.driving_license.split(',').map(item => item.trim());
              console.log(this.driving_license)
              this.formData.transport_license = response.transport_license.split(',').map(item => item.trim());
              this.formData.traffic_insurance = response.traffic_insurance.split(',').map(item => item.trim());
              this.formData.business_insurance = response.business_insurance.split(',').map(item => item.trim());
              this.formData.create_time = response.create_time
              this.formData.update_time = response.update_time
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
