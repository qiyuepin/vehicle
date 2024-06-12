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
            <el-form ref="saveForm" :model="formData" :rules="saveRules" size="small" label-position="right"
                     label-width="110px"
                     style="width: 100%;">
                <el-tabs style="height: 200px;">
                    <el-tab-pane label="基本信息">

                        <el-form-item label="车牌号" prop="trailer_plate">
                            <el-input v-model="formData.trailer_plate" clearable placeholder="请输入车牌号"></el-input>
                        </el-form-item>

                        <el-form-item label="品牌" prop="trailer_brand">
                            <el-input v-model="formData.trailer_brand" clearable placeholder="品牌"></el-input>
                        </el-form-item>
                        <el-form-item label="材质" prop="trailer_material">
                            <el-input v-model="formData.trailer_material" clearable placeholder="请输入材质"></el-input>
                        </el-form-item>
                        <el-form-item label="自重" prop="trailer_weight">
                            <el-input v-model="formData.trailer_weight" clearable placeholder="请输入自重"></el-input>
                        </el-form-item>
                        <el-form-item label="容积" prop="trailer_volume">
                            <el-input v-model="formData.trailer_volume" clearable placeholder="请输入容积"></el-input>
                        </el-form-item>
                        <!-- <el-form-item label="压力" prop="trailer_pressure">
                            <el-input v-model="formData.trailer_pressure" clearable placeholder="请输入压力"></el-input>
                        </el-form-item> -->
                        <el-form-item label="压力等级" prop="trailer_pressure">
                          <el-radio-group v-model="formData.trailer_pressure">
                            <el-radio label="常压"></el-radio>
                            <el-radio label="低压"></el-radio>
                            <el-radio label="高压"></el-radio>
                          </el-radio-group>
                        </el-form-item>
                        <el-form-item label="是否为框架罐" prop="frame_tank">
                            <!-- <el-input v-model="formData.frame_tank" clearable placeholder="请输入框架罐"></el-input> -->
                            <el-radio-group v-model="formData.frame_tank">
                              <el-radio label="是"></el-radio>
                              <el-radio label="否"></el-radio>
                            </el-radio-group>
                        </el-form-item>




                        <el-form-item label="道路运输证号">
                            <el-input v-model="formData.transport_cert" clearable placeholder="请输入道路运输证号"></el-input>
                        </el-form-item>
                        <el-form-item label="经营范围" prop="trailer_scope">
                            <el-checkbox-group v-model="formData.trailer_scope">
                                <el-checkbox v-for="item in trailer_scope" :key="item.id" :label="item.id">{{item.name}}</el-checkbox>
                            </el-checkbox-group>
                        </el-form-item>
                        <el-form-item label="注册日期">
                            <el-input v-model="formData.regist_time" type="date" clearable placeholder="选择注册日期"></el-input>
                        </el-form-item>
                        <el-form-item label="强制报废日期">
                          <el-input v-model="formData.scrapp_time" type="date" clearable placeholder="选择强制报废日期" :class="{ datestatusinput: formData.scrapp_status ? false : true}"></el-input>
                        </el-form-item>
                        <el-form-item label="检验有效期">
                          <el-input v-model="formData.inspection_time" type="date" clearable placeholder="选择检验有效期" :class="{ datestatusinput: formData.inspection_status ? false : true}"></el-input>
                        </el-form-item>
                        <el-form-item label="审验有效期">
                          <el-input v-model="formData.validity_time" type="date" clearable placeholder="选择审验有效期" :class="{ datestatusinput: formData.validity_status ? false : true}"></el-input>
                        </el-form-item>
                        <el-form-item label="罐检报告有效期">
                          <el-input v-model="formData.frame_time" type="date" clearable placeholder="选择罐检报告有效期" :class="{ datestatusinput: formData.frame_status ? false : true}"></el-input>
                        </el-form-item>
                        <el-form-item label="行驶证" prop="driving_license">
                            <!-- <MultiImage ref="Image_driving_license" v-model="formData.driving_license"></MultiImage> -->
                            <MultiImage ref="Image_driving_license":images="formData.driving_license" clearable v-model="formData.driving_license"></MultiImage>
                        </el-form-item>
                        <el-form-item label="道路运输证" prop="transport_license">
                            <UploadImage ref="Image_transport_license" clearable v-model="formData.transport_license"></UploadImage>
                        </el-form-item>
                        <el-form-item label="罐检报告" prop="pot_report">
                            <UploadImage ref="Image_pot_report" clearable v-model="formData.pot_report"></UploadImage>
                        </el-form-item>
                        <el-form-item label="货检保单" prop="cargo_insurance">
                            <UploadImage ref="Image_cargo_insurance" clearable v-model="formData.cargo_insurance"></UploadImage>
                        </el-form-item>

                    </el-tab-pane>
                </el-tabs>
            </el-form>
            <div class="demo-drawer__footer" style="position:fixed;top:15px;right:30px;">
                <el-button size="mini" @click="$refs.drawer.closeDrawer()">取 消</el-button>
                <el-button size="mini" type="primary" @click="saveData()">确 定
                </el-button>
            </div>
        </div>
    </el-drawer>
</template>

<script>

import { addcartrailer, editcartrailer, getcartrailerInfo, getcarscope} from '@/api/Info.js'
import UploadImage from '@/components/Upload/SingleImage'
import MultiImage from '@/components/Upload/MultiImage'



export default {
  name: "AdminForm",
  components: {
    UploadImage,
    MultiImage
  },
  data() {
    return {
      title:'',
      dialog: false,
      trailer_scope: [],
      drawerShow:false,
      saveRules: {
        trailer_plate: [{ required: true, message: '车牌号不能为空', trigger: 'blur'}],
        transport_cert: [{ required: true, message: '道路运输证', trigger: 'blur'}]
      },
      formData: {
        id: 0,
        trailer_plate: '',
        trailer_brand: '',
        trailer_material: '',
        trailer_weight: '',
        trailer_volume: '',
        trailer_pressure: '',
        frame_tank: '',
        transport_cert: '',
        trailer_scope: [],
        regist_time: '',
        scrapp_time: '',
        inspection_time: '',
        validity_time: '',
        frame_time: '',
        driving_license: [],
        transport_license: '',
        pot_report: '',
        cargo_insurance: ''
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
            this.trailer_scope = response.data;
          }
      })
    },
    handleClose() {
      this.dialog = false
      this.drawerShow = false
    },
    showForm() {
      this.dialog = true
      this.drawerShow = true
      this.title = '新增车头信息'
      this.resetData()
    },
    resetData(){
      this.formData.id = 0
      this.formData.trailer_plate = ''
      this.formData.trailer_brand = ''
      this.formData.trailer_material = ''
      this.formData.trailer_weight = ''
      this.formData.trailer_volume = ''
      this.formData.trailer_pressure = ''
      this.formData.frame_tank = ''
      this.formData.transport_cert = ''
      this.formData.trailer_scope = []
      this.formData.regist_time = ''
      this.formData.scrapp_time = ''
      this.formData.inspection_time = ''
      this.formData.validity_time = ''
      this.formData.frame_time = ''
      this.formData.driving_license = []
      this.formData.transport_license = ''
      this.formData.pot_report = ''
      this.formData.cargo_insurance = ''
      this.$refs.Image_driving_license.uploadFileList=[]
      this.$refs.Image_driving_license.uploadFiles=''
    },
    getcartrailerInfo(id){
      getcartrailerInfo({id:id}).then(response=>{
        console.log(response.transport_license)
          if(response !== undefined){
              this.title = '编辑车头信息'
              this.formData.id = response.id
              this.formData.trailer_plate = response.trailer_plate
              this.formData.trailer_brand = response.trailer_brand
              this.formData.trailer_material = response.trailer_material
              this.formData.trailer_weight = response.trailer_weight
              this.formData.trailer_volume = response.trailer_volume
              this.formData.trailer_pressure = response.trailer_pressure
              this.formData.frame_tank = response.frame_tank
              
              this.formData.transport_cert = response.transport_cert

              this.formData.trailer_scope.push(...response.trailer_scope);
              this.formData.regist_time = new Date(response.regist_time).toISOString().slice(0,10)
              this.formData.scrapp_time = new Date(response.scrapp_time).toISOString().slice(0,10)
              this.formData.inspection_time = new Date(response.inspection_time).toISOString().slice(0,10)
              this.formData.validity_time = new Date(response.validity_time).toISOString().slice(0,10)
              this.formData.frame_time = new Date(response.frame_time).toISOString().slice(0,10)
              // this.formData.driving_license = response.driving_license
              // this.$refs.Image_driving_license.uploadFileList.push(...response.driving_license);
              // this.$refs.Image_driving_license.uploadFiles = this.$refs.Image_driving_license.uploadFileList.map(item => {
              //   console.log(item);
              //   return item
              // });
              this.formData.driving_license = response.driving_licenses
              this.$refs.Image_driving_license.uploadFileList.push(...response.driving_licenses)
              this.$refs.Image_driving_license.uploadFiles = this.$refs.Image_driving_license.uploadFileList.map(item => {
                return item
              });
          
              this.$refs.Image_driving_license.imgUrl = response.driving_licenses
              this.formData.transport_license = response.transport_license
              this.$refs.Image_transport_license.imgUrl = response.transport_license
              this.formData.pot_report = response.pot_report
              this.$refs.Image_pot_report.imgUrl = response.pot_report
              this.formData.cargo_insurance = response.cargo_insurance
              this.$refs.Image_cargo_insurance.imgUrl = response.cargo_insurance

              this.formData.scrapp_status  = response.scrapp_status 
              this.formData.inspection_status  = response.inspection_status 
              this.formData.validity_status  = response.validity_status 
              this.formData.frame_status  = response.frame_status 
          }
      })
    },
    
    saveData() {
      this.$confirm('您确定要提交吗？', '温馨提示')
        .then(_ => {
          this.$refs.saveForm.validate(valid => {
            if (valid) {
              if(this.formData.id){
                editcartrailer(this.formData).then(_ => {
                  this.$message({
                    message: '编辑成功',
                    type: 'success',
                    duration: 5 * 1000
                  })
                  this.$emit('updateRow')
                  this.dialog = false
                })
              }else{
                addcartrailer(this.formData).then(_ => {
                  this.$message({
                    message: '新增成功',
                    type: 'success',
                    duration: 5 * 1000
                  })
                  this.$emit('updateRow')
                  this.dialog = false
                })
              }
            }
          })
        })
        .catch(_ => {
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
</style>
