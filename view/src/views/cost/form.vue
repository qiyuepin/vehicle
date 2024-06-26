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
                
                      <el-form-item label="驾驶员" prop="driver_id">
                          <el-select v-model="formData.driver_id" filterable  placeholder="请选择驾驶员"  @change="driverChanged">
                            <el-option
                              v-for="item in driverlist"
                              :key="item.value"
                              :label="item.username"
                              :value="item.id">
                            </el-option>
                          </el-select>
                      </el-form-item>
                      <el-form-item label="计费周期" prop="period_id">
                          <el-select v-model="formData.period_id" filterable  placeholder="请选择计费周期"  @change="periodChanged">
                            <el-option
                              v-for="item in period_idlist"
                              :key="item.value"
                              :label="item.period_id_driver"
                              :value="item.id">
                            </el-option>
                          </el-select>
                      </el-form-item>
                      <el-form-item label="费用类别" prop="type_name">
                          <el-select v-model="formData.type_name" filterable clearable placeholder="请选择费用类别">
                            <el-option
                              v-for="item in typelist"
                              :key="item.value"
                              :label="item.type_name"
                              :value="item.type_name">
                            </el-option>
                          </el-select>
                      </el-form-item>
                      <el-form-item label="费用金额">
                          <el-input v-model="formData.cost_money" clearable
                                    placeholder="请输入费用金额"></el-input>
                      </el-form-item>

                      <el-form-item label="费用照片" prop="cost_img">
                          <UploadImage ref="Image_cost_img" v-model="formData.cost_img"></UploadImage>
                      </el-form-item>
                      <el-form-item label="备注">
                          <el-input v-model="formData.remark" clearable ></el-input>
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

import { addcost, editcost, getinfo, getperiod, getcosttype } from '@/api/cost.js'
import UploadImage from '@/components/Upload/SingleImage'
import { validPhone,validIDcard } from '@/utils/validate'
import { getcarlist } from '@/api/Info.js'

export default {
driver_name: "myForm",
components: {
  UploadImage
},
data() {


  const validatePhone = (rule, value, callback) => {
    if (!validPhone(value)) {
      callback(new Error('请输入正确的手机号'))
    } else {
      callback()
    }
  }
  const validid_card = (rule, value, callback) => {
    if (!validIDcard(value)) {
      callback(new Error('请输入正确的身份证号'))
    } else {
      callback()
    }
  }

  return {
    title:'',
    dialog: false,
    roles: [],
    drawerShow:false,
    driverlist:[],
    period_idlist:[],
    driver:[],
    saveRules: {
      driver_id: [{ required: true, trigger: 'blur'}],
      cost_money: [{ required: true, trigger: 'blur'}]
    },
    formData: {
      id: 0,
      driver_name: '',
      remark: '',
      cost_img: '',
      period_id_driver: '',
      cost_money: '',
      trailer_num: '',
      year: '',
      type_name: ''
    },
  }
},
created() {
  this.getcarlist()
  this.getcosttype()
  // this.getperiod(this.driver)
},
methods: {
  getcosttype() {
    getcosttype().then(response => {
        if(response !== undefined){
          console.log(response)
          // this.infolist = response.data
          this.typelist = response
        }
    })
  },
  getperiod(driver) {
    getperiod({driver_name:driver}).then(response => {
      console.log(driver)
        if(response !== undefined){
          console.log(response)
          this.period_idlist = response
        }
    })
  },
  getcarlist() {
    getcarlist().then(response => {
        if(response !== undefined){
          console.log(response.data)
          this.driverlist = response.driver
          // 
        }
    })
  },
  driverChanged() {
    const selectedinfo = this.driverlist.find(item => item.id === this.formData.driver_id);
    if (selectedinfo) {
      // console.log(selectedinfo)
      this.formData.period_id_driver = selectedinfo.period_id;
      this.formData.driver_name = selectedinfo.username;
      console.log(selectedinfo.username)
      this.getperiod(selectedinfo.username)
      console.log(this.formData)
    } else {

    }
  
  },
  periodChanged() {
    const selectedinfo = this.period_idlist.find(item => item.id === this.formData.period_id);
    if (selectedinfo) {
      console.log(selectedinfo)
      this.formData.period_id_driver = selectedinfo.period_id_driver;
      this.formData.trailer_num = selectedinfo.trailer_num;
      this.formData.year = selectedinfo.year;
      console.log(this.formData)
  
    } else {

    }
    // this.load_address = this.load_factory.factory;
  },
  handleClose() {
    this.dialog = false
    this.drawerShow = false
  },
  showForm() {
    this.dialog = true
    this.drawerShow = true
    this.title = '新增费用'
    this.resetData()
  },
  resetData(){
    this.formData.id = 0
    this.formData.driver_name = ''
    this.formData.remark = ''
    this.formData.cost_money = ''
    this.formData.cost_img = ''
    this.formData.period_id_driver = ''
    this.formData.trailer_num = ''
    this.type_name = ''
  },
  getinfo(id){
    getinfo({id:id}).then(response=>{
        if(response !== undefined){
            this.title = '编辑费用'
            this.formData.id = response.id
            this.formData.driver_name = response.driver_name
            this.formData.remark = response.remark
            this.formData.type_name = response.type_name
            this.formData.period_id_driver = response.period_id_driver
            this.formData.cost_money = response.cost_money
            this.formData.cost_img = response.cost_img
            this.$refs.Image_cost_img.imgUrl = response.cost_img
          
        }
    })
  },
  saveData() {
    this.$confirm('您确定要提交吗？', '温馨提示')
      .then(_ => {
        this.$refs.saveForm.validate(valid => {
          if (valid) {
            if(this.formData.id){
              editcost(this.formData).then(_ => {
                this.$message({
                  message: '编辑成功',
                  type: 'success',
                  duration: 5 * 1000
                })
                this.$emit('updateRow')
                this.dialog = false
              })
            }else{
              addcost(this.formData).then(_ => {
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
