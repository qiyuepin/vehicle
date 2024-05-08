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
                
                      <el-form-item label="车辆/人员" prop="info_id">
                          <el-select v-model="formData.info_id" filterable  placeholder="请选择车辆/人员" @change="infoChanged">
                            <el-option
                              v-for="item in infolist"
                              :key="item.value"
                              :label="item.info"
                              :value="item.id">
                            </el-option>
                          </el-select>
                      </el-form-item>
                      <el-form-item label="车头" prop="head_num">
                          <el-input v-model="formData.head_num" clearable placeholder="请输入车头"></el-input>
                      </el-form-item>
                      <el-form-item label="挂车" prop="trailer_num">
                          <el-input v-model="formData.trailer_num" clearable placeholder="请输入挂车"></el-input>
                      </el-form-item>
                      <el-form-item label="驾驶员" prop="driver_name">
                          <el-input v-model="formData.driver_name" clearable placeholder="请输入驾驶员"></el-input>
                      </el-form-item>
                      <el-form-item label="押运员" prop="escort_name">
                          <el-input v-model="formData.escort_name" clearable placeholder="请输入押运员"></el-input>
                      </el-form-item>
                      <el-form-item label="货品名称" prop="product_name">
                          <el-input v-model="formData.product_name" clearable placeholder="请输入货品名称"></el-input>
                      </el-form-item>
 
                      <el-form-item label="货品数量" prop="product_quantity">
                          <el-input v-model="formData.product_quantity" clearable placeholder="请输入货品数量"></el-input>
                      </el-form-item>


                      <el-form-item label="装货厂家" prop="load_factory">
                          <el-select v-model="formData.load_factory" filterable  clearable placeholder="请选择装货厂家" @change="loadFactoryChanged">
                            <el-option
                              v-for="item in factorylist"
                              :key="item.value"
                              :label="item.name"
                              :value="item.name">
                            </el-option>
                          </el-select>
                      </el-form-item>
                      <!-- <el-form-item label="装货厂家名字" prop="load_factory">
                          <el-input v-model="formData.load_factory" clearable placeholder="装货厂家名字"></el-input>
                      </el-form-item> -->
                      <el-form-item label="装货地址" prop="load_address">
                          <el-input v-model="formData.load_address" clearable placeholder="请输入装货地址"></el-input>
                      </el-form-item>
                      <!-- <el-form-item label="装货地址" prop="load_address">
                          
                        <el-input v-model="formData.load_address" clearable placeholder="请输入装货地址"></el-input>
                      </el-form-item> -->
                      <el-form-item label="卸货厂家" prop="unload_factory">
                          <el-select v-model="formData.unload_factory" filterable clearable placeholder="请选择卸货厂家" @change="unloadFactoryChanged">
                            <el-option
                              v-for="item in factorylist"
                              :key="item.value"
                              :label="item.name"
                              :value="item.name">
                            </el-option>
                          </el-select>
                      </el-form-item>
                      <!-- <el-form-item label="卸货厂家名字" prop="unload_factory">
                          <el-input v-model="formData.unload_factory" clearable placeholder="卸货厂家名字"></el-input>
                      </el-form-item> -->
                      <el-form-item label="卸货地址" prop="unload_address">
                          <el-input v-model="formData.unload_address" clearable placeholder="请输入卸货地址"></el-input>
                      </el-form-item>
                      <!-- <el-form-item label="新计费周期" prop="start_periodic">
                          <el-radio-group v-model="formData.start_periodic">
                            <el-radio :label=1>是</el-radio>
                            <el-radio :label=0>否</el-radio>
                          </el-radio-group>
                      </el-form-item> -->
                      <!-- <el-form-item label="结束计费周期" prop="end_periodic">
                          <el-radio-group v-model="formData.end_periodic">
                            <el-radio :label=1>是</el-radio>
                            <el-radio :label=0>否</el-radio>
                          </el-radio-group>
                      </el-form-item> -->
                  </el-tab-pane>
              </el-tabs>
          </el-form>
          <div class="demo-drawer__footer" style="position:fixed;top:15px;right:30px;">
              <el-button size="mini" @click="$refs.drawer.closeDrawer()">取 消</el-button>
              <el-button size="mini" type="primary" @click="saveData()">确 定</el-button>
          </div>
      </div>
  </el-drawer>
</template>

<script>

import { addnormal, editnormal, gettemporaryinfo, getplaninfo } from '@/api/plan.js'
import UploadImage from '@/components/Upload/SingleImage'
import { validPhone,validIDcard } from '@/utils/validate'



export default {
name: "myForm",
components: {
  UploadImage,
  Map
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
    infolist: [],
    factorylist: [],
    load_factory: null,
    load_address:'',
    drawerShow:false,
    map: null,
    saveRules: {

      info_id: [{ required: true, trigger: 'blur'}],
      product_name: [{ required: true, trigger: 'blur'}],
      product_quantity: [{ required: true, trigger: 'blur'}],
      load_factory: [{ required: true, trigger: 'blur'}],
      unload_factory: [{ required: true, trigger: 'blur'}],

    },
    formData: {
      id: 0,
      info_id: '',
      product_name: '',
      product_quantity: '',
      load_factory: '',
      load_address: '',
      unload_factory: '',
      unload_address: '',
      head_num: '',
      trailer_num: '',
      driver_name: '',
      escort_name: '',
      start_periodic: '',
      end_periodic: '',
      platform: 'pc'
    },
  }
},
created() {
  this.getplaninfo()
},
destroyed () {
    if (this.map != null) {
      this.map.destroy();
    }
  },
methods: {
  getplaninfo() {
    getplaninfo().then(response => {
        if(response !== undefined){
          // console.log(response.data)
          this.infolist = response.data
          this.factorylist = response.factory
        }
    })
  },
  infoChanged() {
    const selectedinfo = this.infolist.find(item => item.id === this.formData.info_id);
    if (selectedinfo) {
      console.log(selectedinfo)
      // this.formData.head_num = selectedinfo.head_num;
      this.formData.trailer_num = selectedinfo.trailer_num;
      this.formData.driver_name = selectedinfo.driver_name;
      // this.formData.escort_name = selectedinfo.escort_name;
    } else {
      this.formData.head_num = '';
      this.formData.trailer_num = '';
      this.formData.driver_name = '';
      this.formData.escort_name = '';
    }
    // this.load_address = this.load_factory.factory;
  },
  loadFactoryChanged() {
    const selectedFactory = this.factorylist.find(item => item.name === this.formData.load_factory);
    if (selectedFactory) {
      console.log(selectedFactory)
      this.formData.load_address = selectedFactory.address;
    } else {
      this.formData.load_address = '';
    }
    // this.load_address = this.load_factory.factory;
  },
  unloadFactoryChanged() {
    const selectedFactory = this.factorylist.find(item => item.name === this.formData.unload_factory);
    if (selectedFactory) {
      console.log(selectedFactory)
      this.formData.unload_address = selectedFactory.address;
    } else {
      this.formData.unload_address = '';
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
    this.title = '新增任务'
    this.resetData()
  },
  resetData(){
    this.formData.id = 0
    this.formData.info_id = ''
    this.formData.product_name = ''
    this.formData.product_quantity = ''
    this.formData.load_address = ''
    this.formData.load_factory = ''
    this.formData.unload_address = ''
    this.formData.unload_factory = ''
    this.formData.head_num = ''
    this.formData.trailer_num = ''
    this.formData.driver_name = ''
    this.formData.escort_name = ''
    this.formData.start_periodic = ''
    this.formData.end_periodic = ''
  },
  gettemporaryinfo(id){
    gettemporaryinfo({id:id}).then(response=>{
        if(response !== undefined){
          console.log('response.start_periodic---'+response.start_periodic)
            this.title = '编辑'
            this.formData.id = response.id
            this.formData.info_id = response.info_id
            this.formData.product_name = response.product_name
            this.formData.product_quantity = response.product_quantity
            this.formData.load_factory = response.load_factory
            this.formData.load_address = response.load_address
            this.formData.unload_address = response.unload_address
            this.formData.unload_factory = response.unload_factory
            this.formData.head_num = response.head_num
            this.formData.trailer_num = response.trailer_num
            this.formData.driver_name = response.driver_name
            this.formData.escort_name = response.escort_name
        
            this.formData.start_periodic = response.start_periodic
     
            this.formData.end_periodic = response.end_periodic
        }
    })
  },
  saveData() {
    this.$confirm('您确定要提交吗？', '温馨提示')
      .then(_ => {
        this.$refs.saveForm.validate(valid => {
          if (valid) {
            if(this.formData.id){
              editnormal(this.formData).then(_ => {
                this.$message({
                  message: '编辑成功',
                  type: 'success',
                  duration: 5 * 1000
                })
                this.$emit('updateRow')
                this.dialog = false
              })
            }else{
              addnormal(this.formData).then(_ => {
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
  .container {
  width: 500px;
  height: 300px;
}

</style>
