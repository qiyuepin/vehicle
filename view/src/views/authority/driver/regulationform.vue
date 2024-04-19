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
            <el-form ref="saveForm" :model="formData"  size="small" label-position="right"
                     label-width="90px"
                     style="width: 100%;">
                <el-tabs style="height: 200px;">
                    <el-tab-pane label="基本信息">
                        <el-form-item label="违章时间">
                          <el-input v-model="formData.regulation_time" type="date" placeholder="选择日期"></el-input>
                        </el-form-item>
                        <el-form-item label="违章地点" prop="regulation_place">
                            <el-input v-model="formData.regulation_place" clearable placeholder="违章地点"></el-input>
                        </el-form-item>
                        <el-form-item label="违法事实" prop="regulation_truth">
                            <el-input v-model="formData.regulation_truth" clearable placeholder="违法事实"></el-input>
                        </el-form-item>
                        <el-form-item label="记分情况" prop="regulation_code">
                            <el-input v-model="formData.regulation_code" clearable placeholder="请输入记分情况"></el-input>
                        </el-form-item>
                        <el-form-item label="处理情况" prop="regulation_deal">
                            <el-input v-model="formData.regulation_deal" clearable placeholder="请输入处理情况"></el-input>
                        </el-form-item>
                        <el-form-item label="备注">
                            <el-input v-model="formData.regulation_remark" clearable placeholder="备注信息"></el-input>
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

import { addregulation, editregulation,getregulationInfo } from '@/api/admin.js'
import UploadImage from '@/components/Upload/SingleImage'
import { validPhone} from '@/utils/validate'

export default {
  name: "AdminForm",
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
    // const validateEmail = (rule, value, callback) => {
    //   if (!validEmail(value)) {
    //     callback(new Error('请输入正确的邮箱地址'))
    //   } else {
    //     callback()
    //   }
    // }
    const validatePassword = (rule, value, callback) => {
      if (!validPassword(value)) {
        callback(new Error('登录密码必须是字母、数字、下划线组合，且长度6-18位'))
      } else {
        callback()
      }
    }
    return {
      title:'',
      dialog: false,
      drawerShow:false,
      formData: {
        id: 0,
        regulation_time: '',
        regulation_place: '',
        regulation_truth: '',
        regulation_code: '',
        regulation_deal: '',
        regulation_remark: '',
        driver_id: this.$route.query.id,
        type: 1
      },
    }
  },

  methods: {
    handleClose() {
      this.dialog = false
      this.drawerShow = false
    },
    showForm() {
      this.dialog = true
      this.drawerShow = true
      this.title = '新增违法记录'
      this.resetData()
    },
    resetData(){
      this.formData.id = 0
      this.formData.regulation_time = ''
      this.formData.regulation_place = ''
      this.formData.regulation_truth = ''
      this.formData.regulation_code = ''
      this.formData.regulation_deal = ''
      this.formData.regulation_remark = ''
    },
    getregulationInfo(id){
      getregulationInfo({id:id}).then(response=>{
          if(response !== undefined){
              this.title = '编辑管违章'
              this.formData.id = response.id
              this.formData.regulation_time = response.regulation_time
              this.formData.regulation_place = response.regulation_place
              this.formData.regulation_truth = response.regulation_truth
              this.formData.regulation_code = response.regulation_code
              this.formData.regulation_deal = response.regulation_deal
              this.formData.regulation_remark = response.regulation_remark
          }
      })
    },
    saveData() {
      this.$confirm('您确定要提交吗？', '温馨提示')
        .then(_ => {
          this.$refs.saveForm.validate(valid => {
            if (valid) {
              if(this.formData.id){
                editregulation(this.formData).then(_ => {
                  this.$message({
                    message: '编辑成功',
                    type: 'success',
                    duration: 5 * 1000
                  })
                  this.$emit('updateRow')
                  this.dialog = false
                })
              }else{
                addregulation(this.formData).then(_ => {
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
