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
                        <!-- <el-form-item label="选择角色" prop="group">
                            <el-checkbox-group v-model="formData.group">
                                <el-checkbox v-for="item in roles" :key="item.id" :label="item.id" v-if="item.type == 2">{{item.title}}</el-checkbox>
                            </el-checkbox-group>
                        </el-form-item> -->
                        <el-form-item label="车牌号" prop="carhead_plate">
                            <el-input v-model="formData.carhead_plate" clearable placeholder="请输入车牌号"
                                      style="width: 200px"></el-input>
                        </el-form-item>
                        <!-- <el-form-item label="昵称" prop="nickname">
                            <el-input v-model="formData.nickname" clearable placeholder="请输入20个以内的中文字符"></el-input>
                        </el-form-item> -->
                        <el-form-item label="品牌" prop="carhead_brand">
<!--                            <el-input v-model="formData.carhead_brand" clearable placeholder="品牌"></el-input>-->
                            <el-select v-model="formData.carhead_brand" filterable  clearable placeholder="请选择品牌">
                                <el-option
                                    v-for="item in branchnamelist"
                                    :key="item.value"
                                    :label="item.branch_name"
                                    :value="item.branch_name">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="自重" prop="carhead_weight">
<!--                            <el-input v-model="formData.carhead_weight" clearable placeholder="请输入自重"></el-input>-->

                            <el-input  v-model="formData.carhead_weight" clearable placeholder="请输入自重"
                                      maxlength="10"
                                      onkeyup="value=value.replace(/[^\d^\.]+/g,'')"
                                      v-on:input="clearNoNum(formData.carhead_weight)"
                                      @blur.native.capture="clearnumber(formData.carhead_weight)" v-input.float="2"
                                       style="width: 150px">
                                     </el-input>
                            <span>（ t ）保留两位小数</span>
                        </el-form-item>
                        <el-form-item label="道路运输证号" prop="transport_cert">
                            <el-input v-model="formData.transport_cert" clearable placeholder="请输12位入道路运输证号"
                                      style="width: 200px" ></el-input>
                        </el-form-item>
                        <el-form-item label="排放等级" prop="discharge_level">
                            <el-select v-model="formData.discharge_level" filterable  clearable placeholder="请选择排放等级">
                                <el-option
                                    v-for="item in dischargelevellist"
                                    :key="item.value"
                                    :label="item.discharge_level"
                                    :value="item.discharge_level">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="经营范围" prop="carhead_scope">
                            <!-- <el-input v-model="formData.carhead_scope" clearable placeholder="请输入经营范围"></el-input> -->
                            <el-checkbox-group v-model="formData.carhead_scope">
                                <el-checkbox v-for="item in carhead_scope" :key="item.id" :label="item.id">{{item.name}}</el-checkbox>
                            </el-checkbox-group>
                        </el-form-item>
                        <el-form-item label="注册日期">
                            <el-input v-model="formData.regist_time" type="date" clearable placeholder="选择注册日期"
                                      style="width: 150px;"></el-input>
                        </el-form-item>
                        <el-form-item label="已运营时间">
                            <el-label > {{ years }}</el-label>
                        </el-form-item>
                        <el-form-item label="强制报废日期">
                          <el-input v-model="formData.scrapp_time" type="date" clearable placeholder="选择强制报废日期" :class="{ datestatusinput: formData.scrapp_status ? false : true}"
                                    style="width: 150px;"></el-input>
                        </el-form-item>
                        <el-form-item label="检验有效期">
                          <el-input v-model="formData.inspection_time" type="date" clearable placeholder="选择检验有效期" :class="{ datestatusinput: formData.inspection_status ? false : true}"
                                    style="width: 150px;"></el-input>
                        </el-form-item>
                        <el-form-item label="营运证有效期">
                          <el-input v-model="formData.validity_time" type="date" clearable placeholder="选择营运证有效期" :class="{ datestatusinput: formData.validity_status ? false : true}"
                                    style="width: 150px;"></el-input>
                        </el-form-item>
                        <el-form-item label="交强险有效期">
                          <el-input v-model="formData.traffic_time" type="date" clearable placeholder="选择交强险有效期" :class="{ datestatusinput: formData.traffic_status ? false : true}"
                                    style="width: 150px;"></el-input>
                        </el-form-item>
                        <el-form-item label="动力源" prop="power_supply">
                            <el-select v-model="formData.power_supply" filterable  clearable placeholder="请选择动力源">
                                <el-option
                                    v-for="item in powersupplyllist"
                                    :key="item.value"
                                    :label="item.power_supply"
                                    :value="item.power_supply">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="车体照片" prop="carbody_picture">
                            <MultiImage ref="Image_carbody_picture" :images="formData.carbody_picture" v-model="formData.carbody_picture"></MultiImage>
                        </el-form-item>
                        <el-form-item label="行驶证" prop="driving_license">
                            <!-- <MultiImage ref="Image_driving_license" v-model="formData.driving_license"></MultiImage> -->
                            <MultiImage ref="Image_driving_license" :images="formData.driving_license" v-model="formData.driving_license"></MultiImage>
                        </el-form-item>
                        <el-form-item label="道路运输证" prop="transport_license">
                            <UploadImage ref="Image_transport_license" v-model="formData.transport_license"></UploadImage>
                        </el-form-item>
                        <!-- <el-form-item label="交强险保单" prop="traffic_insurance">
                            <UploadImage ref="Image_traffic_insurance" v-model="formData.traffic_insurance"></UploadImage>
                        </el-form-item> -->
                        <el-form-item label="交强险保单" prop="traffic_insurance">
                            <UploadPdf ref="Image_traffic_insurance" v-model="formData.traffic_insurance"></UploadPdf>
                        </el-form-item>
                        <!-- <el-form-item label="商业险保单" prop="business_insurance">
                            <UploadImage ref="Image_business_insurance" v-model="formData.business_insurance"></UploadImage>
                        </el-form-item> -->
                        <el-form-item label="商业险保单" prop="business_insurance">
                            <UploadPdf ref="Image_business_insurance" v-model="formData.business_insurance"></UploadPdf>
                            <!-- <iframe loading="lazy" id="pdf_container" :src="pdfUrl" frameborder="0" height="100%" width="100%"></iframe> -->
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

import { addcarhead, editcarhead, getcarheadInfo, getcarscope, infonotice,getheadbranch,getdischarge,getpowersupply} from '@/api/Info.js'
import UploadImage from '@/components/Upload/SingleImage'
import MultiImage from '@/components/Upload/MultiImage'
import UploadPdf from '@/components/Upload/SinglePdf'
import moment from 'moment'

export default {
  name: "AdminForm",
  components: {
    UploadImage,
    MultiImage,
    UploadPdf
  },
  data() {
    return {
      title:'',
      dialog: false,
      carhead_scope: [],
        branchnamelist:[],
        dischargelevellist:[],
        powersupplyllist:[],
      drawerShow:false,
      saveRules: {
        carhead_plate: [{ required: true, trigger: 'blur',message: '请输入车牌号'}],
        transport_cert: [{ message: '请输入数字',trigger: 'blur'},
            {pattern: /^[0-9]{12}$/, message: '输入内容不是有效的数字'}]
      },
      formData: {
        id: 0,
        carhead_plate: '',
        carhead_brand: '',
        carhead_weight: '',
        transport_cert: '',
        discharge_level: '',
        carhead_scope: [],
        regist_time: '',
        scrapp_time: '',
        inspection_time: '',
        validity_time: '',
        traffic_time: '',
        power_supply: '',
        driving_license: [],
        transport_license: '',
        traffic_insurance: '',
        business_insurance: '',
        carbody_picture: [],
        scrapp_status: '',
        inspection_status: '',
        validity_status: '',
        traffic_status: '',
      },
    }
  },
    computed: {
        years() {
            const now = new Date()
            const valueStart = this.formData.regist_time === null ? 0 : this.formData.regist_time
            const valueEnd = this.formData.regist_time === null? 0 : now
            const yearStr = moment(valueEnd).diff(moment(valueStart), 'years')
            const monthStr = moment(valueEnd).diff(moment(valueStart), 'months') % 12

            if (yearStr === 0 && monthStr === 0) {
                return ''
            }
            else if (yearStr === 0 && monthStr > 0) {
                return monthStr + '月'
            }
            else if (yearStr < 0 || monthStr < 0) {
                return ''
            }
            else{
                return yearStr + '年' + ' ' + monthStr + '月'
            }
        }
    },
  created() {
    this.getcarscope()
      this.getheadbranch()
      this.getdischarge()
      this.getpowersupply()
  },
  methods: {
      clearNoNum(value) {
          if (value) {
              let value1 = parseFloat(value);
              // value = value1 == 0 ? "" : value;
              let i = value.indexOf(".");
              value = i == 0 ? "" : value;
              if (value.indexOf(".") > 0) {
                  value = value.replace(/\.{2,}/g, ".");
                  let arr = value.split(".");
                  value = arr.length == 3 ? value.substr(0, value.length - 1) : value;
              }
          }
          // this.list[index].child[indexs].rate = value;
          this.formData.carhead_weight = value;
      },
      //离开输入框后增加小数点
      clearnumber(value) {
          if (value) {
              console.log(value.length)
              // value = value.length === 1 ? value + ".00" : value;
              // value =
              //     value.length == 2 && value.indexOf(".") < 0 ? value + ".0" : value;
              // value =
              //     value.length == 3 && value.indexOf(".") > 0 ? value + "0" : value;
              value = value.indexOf(".") < 0 ? value + ".00" : value;
          }
          console.log(value)
          // this.list[index].child[indexs].rate = value;
          this.formData.carhead_weight = value;
      },
      getheadbranch(){
          getheadbranch().then(response => {
              if(response !== undefined){
                  console.log(response.data)
                  this.branchnamelist = response.data
              }
          })
      },
      getdischarge(){
          getdischarge().then(response => {
              if(response !== undefined){
                  console.log(response.data)
                  this.dischargelevellist = response.data
              }
          })
      },
      getpowersupply(){
          getpowersupply().then(response => {
              if(response !== undefined){
                  console.log(response.data)
                  this.powersupplyllist = response.data
              }
          })
      },
    infonotice() {
      infonotice().then(response => {
          if(response !== undefined){
            console.log(response)
            // this.infolist = response.data
            this.noticelist = response.data
            this.noticecount = response.count
          }
      })
    },
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
    showForm() {
      this.dialog = true
      this.drawerShow = true
      this.title = '新增车头信息'
      this.resetData()
    },
    resetData(){
      this.formData.id = 0
      this.formData.carhead_plate = ''
      this.formData.carhead_brand = ''
      this.formData.carhead_weight = ''
      this.formData.transport_cert = ''
      this.formData.carhead_scope = []
      this.formData.regist_time = null
      this.formData.scrapp_time = null
      this.formData.inspection_time = null
      this.formData.validity_time = null
      this.formData.traffic_time = null
      this.formData.driving_license = []
      this.formData.transport_license = ''
      this.formData.traffic_insurance = ''
      this.formData.business_insurance = ''
      this.formData.discharge_level = ''
      this.formData.power_supply = ''
      this.formData.carbody_picture = []
    },
    getcarheadInfo(id){
      getcarheadInfo({id:id}).then(response=>{
        console.log(response.transport_license)
          if(response !== undefined){
              this.title = '编辑车头信息'
              this.formData.id = response.id
              this.formData.carhead_plate = response.carhead_plate
              this.formData.carhead_brand = response.carhead_brand
              this.formData.carhead_weight = response.carhead_weight
              this.formData.transport_cert = response.transport_cert
              // this.formData.carhead_scope = response.carhead_scope
              // response.scope.forEach((item,_) => {
              //     this.formData.carhead_scope.push(item)
              // })
              this.formData.carhead_scope.push(...response.carhead_scope);
              this.formData.regist_time = new Date(response.regist_time).toISOString().slice(0,10)
              this.formData.scrapp_time = new Date(response.scrapp_time).toISOString().slice(0,10)
              this.formData.inspection_time = new Date(response.inspection_time).toISOString().slice(0,10)
              this.formData.validity_time = new Date(response.validity_time).toISOString().slice(0,10)
              this.formData.traffic_time = new Date(response.traffic_time).toISOString().slice(0,10)
              // this.formData.driving_license = response.driving_license
              // this.$refs.Image_driving_license.uploadFileList.push(...response.driving_license);
              // this.$refs.Image_driving_license.uploadFiles = this.$refs.Image_driving_license.uploadFileList.map(item => {
              //   console.log(item);
              //   return item
              // });
              //车体照片 start
              this.formData.carbody_picture = response.carbody_pictures
              console.log(response.carbody_pictures)
              this.$refs.Image_carbody_picture.uploadFileList.push(...response.carbody_pictures)
              this.$refs.Image_carbody_picture.uploadFiles = this.$refs.Image_carbody_picture.uploadFileList.map(itemcarbody => {
                  return itemcarbody
              });
              this.$refs.Image_carbody_picture.imgUrl = response.carbody_pictures
              //车体照片 end
                console.log('测试')
              this.formData.driving_license = response.driving_licenses
              this.$refs.Image_driving_license.uploadFileList.push(...response.driving_licenses)
              this.$refs.Image_driving_license.uploadFiles = this.$refs.Image_driving_license.uploadFileList.map(item => {
                return item
              });

              this.$refs.Image_driving_license.imgUrl = response.driving_licenses
              this.formData.transport_license = response.transport_license
              this.$refs.Image_transport_license.imgUrl = response.transport_license
              this.formData.traffic_insurance = response.traffic_insurance
              this.$refs.Image_traffic_insurance.pdfUrl = response.traffic_insurance
              this.formData.business_insurance = response.business_insurance
              this.$refs.Image_business_insurance.pdfUrl = response.business_insurance

              this.formData.scrapp_status  = response.scrapp_status
              this.formData.inspection_status  = response.inspection_status
              this.formData.validity_status  = response.validity_status
              this.formData.traffic_status  = response.traffic_status
              this.formData.discharge_level = response.discharge_level
              this.formData.power_supply = response.power_supply
          }
      })
    },

    saveData() {
      this.$confirm('您确定要提交吗？', '温馨提示')
        .then(_ => {
          this.$refs.saveForm.validate(valid => {
            if (valid) {
              if(this.formData.id){
                editcarhead(this.formData).then(_ => {
                  this.infonotice()
                  this.$message({
                    message: '编辑成功',
                    type: 'success',
                    duration: 5 * 1000
                  })
                  this.$emit('updateRow')
                  this.dialog = false
                })
              }else{
                addcarhead(this.formData).then(_ => {
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
