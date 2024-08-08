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
          <el-form ref="saveForm" :model="formData" size="small" label-position="right"
                   label-width="110px"
                   style="width: 100%;">
              <el-tabs style="height: 200px;">
                  <el-tab-pane label="基本信息">
                      <!-- <el-form-item label="车头" prop="head_id">
                          <el-input v-model="formData.phone" clearable placeholder="请输入正确的手机号"></el-input>
                      </el-form-item> -->
                      <el-form-item label="车头" prop="head_id">
                          <el-select v-model="formData.head_id" filterable  placeholder="请选择车头" disabled>
                            <el-option
                              v-for="item in headlist"
                              :key="item.value"
                              :label="item.carhead_plate"
                              :value="item.id">
                            </el-option>
                          </el-select>
                      </el-form-item>
                      <!-- 【YB分类整理】问题描述20240726 No.37 顺序调整 by baolei start         -->
                      <el-form-item label="挂车" prop="trailer_id">
                          <el-select v-model="formData.trailer_id" filterable  placeholder="请选择挂车">
                              <el-option
                                  v-for="item in trailerlist"
                                  :key="item.value"
                                  :label="item.trailer_plate"
                                  :value="item.id">
                              </el-option>
                          </el-select>
                      </el-form-item>
                      <!-- 【YB分类整理】问题描述20240726 No.37 顺序调整 by baolei end         -->
                      <el-form-item label="驾驶员" prop="driver_id">
                          <el-select v-model="formData.driver_id" filterable  placeholder="请选择驾驶员">
                            <el-option
                              v-for="item in driverlist"
                              :key="item.value"
                              :label="item.username"
                              :value="item.id">
                            </el-option>
                          </el-select>
                      </el-form-item>
                      <el-form-item label="押运员" prop="escort_id">
                          <el-select v-model="formData.escort_id" filterable  placeholder="请选择押运员">
                            <el-option
                              v-for="item in escortlist"
                              :key="item.value"
                              :label="item.name"
                              :value="item.id">
                            </el-option>
                          </el-select>
                      </el-form-item>
                      <!-- 【YB分类整理】问题描述20240726 No.37 顺序调整 by baolei start         -->
<!--                      <el-form-item label="挂车" prop="trailer_id">-->
<!--                          <el-select v-model="formData.trailer_id" filterable  placeholder="请选择挂车">-->
<!--                            <el-option-->
<!--                              v-for="item in trailerlist"-->
<!--                              :key="item.value"-->
<!--                              :label="item.trailer_plate"-->
<!--                              :value="item.id">-->
<!--                            </el-option>-->
<!--                          </el-select>-->
<!--                      </el-form-item>-->
                      <!-- 【YB分类整理】问题描述20240726 No.37 顺序调整 by baolei start         -->
                      <!-- <el-form-item label="驾驶员" prop="driver_id">
                          <el-input v-model="formData.name" clearable placeholder="请输入姓名"></el-input>
                      </el-form-item>

                      <el-form-item label="押运员" prop="escort_id">
                          <el-input v-model="formData.phone" clearable placeholder="请输入正确的手机号"></el-input>
                      </el-form-item>
                      <el-form-item label="挂车" prop="trailer_id">
                          <el-input v-model="formData.phone" clearable placeholder="请输入正确的手机号"></el-input>
                      </el-form-item> -->


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

import { addinfo, editinfo, getinfo, getcarlist } from '@/api/Info.js'



export default {
name: "myForm",
components: {

},
data() {
  return {
    title:'',
    dialog: false,
    roles: [],
    drawerShow:false,
    headlist:[],
    escortlist:[],
    trailerlist:[],
    driverlist:[],
    head: {
      page: 1,
      limit: 100
    },
    formData: {
      id: 0,
      driver_id: '',
      head_id: '',
      escort_id: '',
      trailer_id: '',
      status: ''
    },
  }
},
created() {
  this.getcarlist()
},
methods: {
  getcarlist() {
    getcarlist().then(response => {
        if(response !== undefined){
          console.log(response.data)
          this.driverlist = response.driver
          this.headlist = response.head
          this.escortlist = response.escort
          this.trailerlist = response.trailer
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
    this.title = '新增'
    this.resetData()
  },
  resetData(){
    this.formData.id = 0
    this.formData.driver_id = ''
    this.formData.driver_name = ''
    this.formData.escort_id = ''
    this.formData.escort_name = ''
    this.formData.head_id = ''
    this.formData.trailer_id = ''
  },
  getinfo(id){
    getinfo({id:id}).then(response=>{
        if(response !== undefined){
            this.title = '编辑'
            this.formData.id = response.id
            this.formData.driver_id = response.driver_id
            this.formData.escort_id = response.escort_id
            this.formData.head_id = response.head_id
            this.formData.trailer_id = response.trailer_id
        }
    })
  },
  saveData() {
    this.$confirm('您确定要提交吗？', '温馨提示')
      .then(_ => {
        this.$refs.saveForm.validate(valid => {
          if (valid) {
            if(this.formData.id){
              editinfo(this.formData).then(_ => {
                this.$message({
                  message: '编辑成功',
                  type: 'success',
                  duration: 5 * 1000
                })
                this.$emit('updateRow')
                this.dialog = false
              })
            }else{
              addinfo(this.formData).then(_ => {
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
