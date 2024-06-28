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
            

            <el-descriptions :column="2" border>
                <el-descriptions-item label="车牌号" class="custom-descriptions">{{formData.trailer_plate}}</el-descriptions-item>
                <el-descriptions-item label="品牌" class="custom-descriptions">{{formData.trailer_brand}}</el-descriptions-item>
                <el-descriptions-item label="自重" class="custom-descriptions">{{formData.trailer_weight}} （ t ）</el-descriptions-item>
                <el-descriptions-item label="容积" class="custom-descriptions">{{formData.trailer_volume}} （ m³ ）</el-descriptions-item>
                <el-descriptions-item label="道路运输证号" class="custom-descriptions">{{formData.transport_cert}}</el-descriptions-item>
                <el-descriptions-item label="罐体材质" class="custom-descriptions">{{formData.trailer_material}}</el-descriptions-item>
                <el-descriptions-item label="设计代码" class="custom-descriptions">{{formData.trailer_designcode}}</el-descriptions-item>
                <el-descriptions-item label="保温性能" class="custom-descriptions">{{formData.trailer_keepwarm}}</el-descriptions-item>
                <el-descriptions-item label="经营范围">{{formData.trailer_scope_name}}</el-descriptions-item>
                <!-- <el-descriptions-item label="压力等级" class="custom-descriptions">{{formData.trailer_pressure}}</el-descriptions-item>
                <el-descriptions-item label="是否为框架罐" class="custom-descriptions">{{formData.frame_tank}}</el-descriptions-item> -->
                
                

                <el-descriptions-item label="注册日期">{{formData.regist_time}}</el-descriptions-item>
                <el-descriptions-item label="已运营时间">{{formData.operation_time}}</el-descriptions-item>

                <el-descriptions-item label="强制报废日期" :content-class-name="{ datestatus: formData.scrapp_status ? false : true }">
                    {{formData.scrapp_time}}
                </el-descriptions-item>
                <el-descriptions-item label="检验有效期" :content-class-name="{ datestatus: formData.inspection_status ? false : true }">
                    {{formData.inspection_time}}
                </el-descriptions-item>
                <el-descriptions-item label="审验有效期" :content-class-name="{ datestatus: formData.validity_status ? false : true }">
                    {{formData.validity_time}}
                </el-descriptions-item>
                <el-descriptions-item label="罐检报告有效期" :content-class-name="{ datestatus: formData.frame_status ? false : true }">
                  {{formData.frame_time}}
                </el-descriptions-item>
                <!-- <el-descriptions-item label="创建日期">{{formData.create_time}}</el-descriptions-item> -->
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
                <el-descriptions-item label="罐检报告">
                    <div class="demo-image__preview">
                    <div v-if="formData.pot_report !=''" class="pdf-box">
                        <svg-icon icon-class="pdf" class-name="pdf" />
                        <span class="el-upload-list__item-actions">
                              <span
                                      class="el-upload-list__item-preview"
                                      title="预览"
                                      @click.prevent="handlePreview(formData.pot_report)">
                                      <i class="el-icon-zoom-in"></i>
                              </span>
                        </span>
                    </div>
                    </div>
                </el-descriptions-item>
                <!-- <el-descriptions-item label="罐检报告">
                    <div class="demo-image__preview">
                    <el-image 
                        style="width: 100px; height: 100px"
                        :src="formData.pot_report[0]" 
                        :preview-src-list="formData.pot_report">
                    </el-image>
                    </div>
                </el-descriptions-item> -->
                <!-- <el-descriptions-item label="货检保单">
                    <div class="demo-image__preview">
                    <el-image 
                        style="width: 100px; height: 100px"
                        :src="formData.cargo_insurance[0]" 
                        :preview-src-list="formData.cargo_insurance">
                    </el-image>
                    </div>
                </el-descriptions-item> -->
                <el-descriptions-item label="货检保单">
                    <div class="demo-image__preview">
                    <div v-if="formData.cargo_insurance !=''" class="pdf-box">
                        <svg-icon icon-class="pdf" class-name="pdf" />
                        <span class="el-upload-list__item-actions">
                              <span
                                      class="el-upload-list__item-preview"
                                      title="预览"
                                      @click.prevent="handlePreview(formData.cargo_insurance)">
                                      <i class="el-icon-zoom-in"></i>
                              </span>
                        </span>
                    </div>
                    </div>
                </el-descriptions-item>
            </el-descriptions>
            <div class="demo-drawer__footer" style="position:fixed;top:15px;right:30px;">
                <el-button size="mini" @click="$refs.drawer.closeDrawer()">关 闭</el-button>
                <!-- <el-button size="mini" type="primary" @click="saveData()">确 定
                </el-button> -->
            </div>
            <el-dialog :modal-append-to-body="false" top="0" class="dialogPdf" :fullscreen="true" :append-to-body="true" :visible.sync="dialogVisible">
                <iframe loading="lazy" id="pdf_container" :src="openpdf" frameborder="0" height="100%" width="100%"></iframe>
            </el-dialog>
        </div>
    </el-drawer>
</template>

<script>

import { getcartrailerInfo, getcarscope} from '@/api/Info.js'
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
      openpdf:'',
      dialogVisible:false,
      title:'',
      dialog: false,
      trailer_scope: [],
      driving_license: [],
      drawerShow:false,
      saveRules: {
        trailer_plate: [{ required: true, trigger: 'blur'}],
        transport_cert: [{ required: true, trigger: 'blur'}]
      },
      formData: {
        id: 0,
        trailer_plate: '',
        carhead_brand: '',
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
        transport_license: [],
        pot_report: [],
        cargo_insurance: [],
        create_time: '',
        update_time: ''
      },
    }
  },
  created() {
    this.getcarscope()
  },
  methods: {
    handlePreview(openpdf){
      this.openpdf = openpdf;
      this.dialogVisible = true;
    },
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
    showDetail() {
        
      this.dialog = true
      this.drawerShow = true
      this.title = '挂车信息详情'
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
      this.formData.transport_cert = ''
      this.formData.trailer_scope = []
      this.formData.regist_time = ''
      this.formData.scrapp_time = ''
      this.formData.inspection_time = ''
      this.formData.validity_time = ''
      this.formData.frame_time = ''
      this.formData.driving_license = []
      this.formData.transport_license = []
      this.formData.pot_report = []
      this.formData.cargo_insurance = []
      this.create_time = ''
      this.update_time = ''
    },
    getcartrailerInfo(id){
      getcartrailerInfo({id:id}).then(response=>{
        console.log(response.transport_license)
          if(response !== undefined){
            
              this.title = '挂车信息详情'
              this.formData.id = response.id
              this.formData.trailer_plate = response.trailer_plate
              this.formData.trailer_brand = response.trailer_brand
              this.formData.trailer_material = response.trailer_material
              this.formData.trailer_weight = response.trailer_weight
              this.formData.trailer_volume = response.trailer_volume
              this.formData.trailer_pressure = response.trailer_pressure
              this.formData.frame_tank = response.frame_tank
              this.formData.transport_cert = response.transport_cert
              // this.formData.trailer_scope = response.trailer_scope
              // response.scope.forEach((item,_) => {
              //     this.formData.trailer_scope.push(item)
              // })
              const dateDifference = this.calculateDifference(response.regist_time);
              // this.formData.operation_time = dateDifference.years>0?dateDifference.years + "年" +  dateDifference.months +"个月":dateDifference.months +"个月";
              this.formData.operation_time = dateDifference.years + "年" +  dateDifference.months +"个月";
              // console.log(this.formData.operation_time);
              this.formData.trailer_designcode = response.trailer_designcode
              this.formData.trailer_keepwarm = response.trailer_keepwarm
              this.formData.trailer_scope_name = response.trailer_scope_name
            //   this.formData.trailer_scope.push(...response.trailer_scope);
              this.formData.regist_time = new Date(response.regist_time).toISOString().slice(0,10)
              this.formData.scrapp_time = new Date(response.scrapp_time).toISOString().slice(0,10)
              this.formData.inspection_time = new Date(response.inspection_time).toISOString().slice(0,10)
              this.formData.validity_time = new Date(response.validity_time).toISOString().slice(0,10)
              this.formData.frame_time = new Date(response.frame_time).toISOString().slice(0,10)
              this.driving_license = response.driving_license.split(',').map(item => item.trim());
              // console.log(this.driving_license)
              this.formData.transport_license = response.transport_license.split(',').map(item => item.trim());
              this.formData.pot_report = response.pot_report.split(',').map(item => item.trim());
              this.formData.cargo_insurance = response.cargo_insurance.split(',').map(item => item.trim());
              this.formData.create_time = response.create_time
              this.formData.update_time = response.update_time
              this.formData.scrapp_status  = response.scrapp_status 
              this.formData.inspection_status  = response.inspection_status 
              this.formData.validity_status  = response.validity_status 
              this.formData.frame_status  = response.frame_status 
          }
      })
    },
    calculateDifference(dateString) {
        // 创建特定日期的 Date 对象
        const specificDate = new Date(dateString);
        
        // 获取当前时间的 Date 对象
        const currentDate = new Date();
        
        // 获取年份和月份
        const specificYear = specificDate.getFullYear();
        const specificMonth = specificDate.getMonth(); // 注意：getMonth 返回的月份是从 0 开始的
        const currentYear = currentDate.getFullYear();
        const currentMonth = currentDate.getMonth(); // 注意：getMonth 返回的月份是从 0 开始的

        // 计算年份和月份的差异
        let yearsDifference = currentYear - specificYear;
        let monthsDifference = currentMonth - specificMonth;

        // 如果月份差为负数，则向前借一年
        if (monthsDifference < 0) {
          yearsDifference -= 1;
          monthsDifference += 12;
        }

        return {
          years: yearsDifference,
          months: monthsDifference
        };
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
