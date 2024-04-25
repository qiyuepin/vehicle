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
                     label-width="90px"
                     style="width: 100%;">
                <el-tabs style="height: 200px;">
                    <el-tab-pane label="基本信息">
                        <el-form-item label="事故时间">
                            <el-input v-model="formData.accident_time" type="date" placeholder="选择日期"></el-input>
                        </el-form-item>
                        <el-form-item label="事故地点" prop="accident_place">
                            <el-input v-model="formData.accident_place" clearable placeholder="事故地点"></el-input>
                        </el-form-item>
                        <el-form-item label="事故描述" prop="accident_des">
                            <el-input v-model="formData.accident_des" clearable placeholder="事故描述"></el-input>
                        </el-form-item>
                        <el-form-item label="事故责任" prop="accident_respons">
                            <!--<el-input v-model="formData.accident_respons" clearable placeholder="事故责任"></el-input>-->
                            <el-select
                                v-model="formData.accident_respons"
                                clearable
                                placeholder="选择事故责任"
                                style="width: 240px"
                            >
                                <el-option
                                    v-for="item in responsoptions"
                                    :key="item.value"
                                    :label="item.label"
                                    :value="item.value"
                                />
                            </el-select>
                        </el-form-item>
                        <el-form-item label="事故类别" prop="accident_kind">
                            <!--<el-input v-model="formData.accident_kind" clearable placeholder="事故类别"></el-input>-->
                            <el-select
                                v-model="formData.accident_kind"
                                clearable
                                placeholder="选择事故类别"
                                style="width: 240px"
                            >
                                <el-option
                                    v-for="item in kindoptions"
                                    :key="item.value"
                                    :label="item.label"
                                    :value="item.value"
                                />
                            </el-select>
                        </el-form-item>
                        <el-form-item label="损失情况" prop="accident_loss">
                            <!--<el-input v-model="formData.accident_loss" clearable placeholder="损失情况"></el-input>-->
                            <el-select
                                v-model="formData.accident_loss"
                                clearable
                                placeholder="选择损失情况"
                                style="width: 240px"
                            >
                                <el-option
                                    v-for="item in lossoptions"
                                    :key="item.value"
                                    :label="item.label"
                                    :value="item.value"
                                />
                            </el-select>
                        </el-form-item>
                        <el-form-item label="备注">
                            <el-input v-model="formData.accident_remark" clearable placeholder="事故备注"></el-input>
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

import { addaccident, editaccident, getaccidentInfo } from '@/api/admin.js'
import UploadImage from '@/components/Upload/SingleImage'
//import { validUsername, validNickname, validPhone, validEmail, validPassword } from '@/utils/validate'
import { ref } from 'vue'
import item from "../../../layout/components/Sidebar/Item.vue";

export default {
  name: "AdminForm",
    computed: {
        item() {
            return item
        }
    },
  components: {
    UploadImage
  },
  data() {
    const validateGroup = (rule,value,callback)=>{
      if(value.lenght==0){
        callback(new Error('请选择角色'))
      }else{
        callback()
      }
    }
    const validateUsername = (rule, value, callback) => {
      if (!validUsername(value)) {
        callback(new Error('用户名称长度2-10位'))
      } else {
        callback()
      }
    }
    // const validateNickname = (rule, value, callback) => {
    //   if (!validNickname(value)) {
    //     callback(new Error('昵称必须是20个以内的中文字符'))
    //   } else {
    //     callback()
    //   }
    // }
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
              accident_time: '',
              accident_place: '',
              accident_des: '',
              accident_respons: '',
              accident_kind: '',
              accident_loss: '',
              accident_remark: '',
              driver_id: this.$route.query.id,
              type: 1
          },
          responsoptions: [
              { value: '全责', text: '全责' },
              { value: '主责', text: '主责' },
              { value: '同责', text: '同责' },
              { value: '次责', text: '次责' },
              { value: '无责', text: '无责' }
          ],
          kindoptions: [
              { value: '一般', text: '一般' },
              { value: '较大', text: '较大' },
              { value: '重大', text: '重大' },
              { value: '特大', text: '特大' }
          ],
          lossoptions: [
              { value: '人员伤亡', text: '人员伤亡' },
              { value: '经济损失', text: '经济损失' },
              { value: '环境影响', text: '环境影响' }
          ],
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
          this.title = '新增事故记录'
          this.resetData()
      },
      resetData(){
          this.formData.id = 0
          this.formData.accident_time = ''
          this.formData.accident_place = ''
          this.formData.accident_des = ''
          this.formData.accident_respons = ''
          this.formData.accident_kind = ''
          this.formData.accident_loss = ''
          this.formData.accident_remark = ''
          this.formData.responsselected = ''
      },
      getaccidentInfo(id){
          getaccidentInfo({id:id}).then(response=>{
              if(response !== undefined){
                  this.title = '编辑事故'
                  this.formData.id = response.id
                  this.formData.accident_time = response.accident_time
                  this.formData.accident_place = response.accident_place
                  this.formData.accident_des = response.accident_des
                  this.formData.accident_respons = response.accident_respons
                  this.formData.accident_kind = response.accident_kind
                  this.formData.accident_loss = response.accident_loss
                  this.formData.accident_remark = response.accident_remark
                  this.formData.type = 2
              }
          })
      },
      saveData() {
          this.$confirm('您确定要提交吗？', '温馨提示')
              .then(_ => {
                  this.$refs.saveForm.validate(valid => {
                      if (valid) {
                          if(this.formData.id){
                              editaccident(this.formData).then(_ => {
                                  this.$message({
                                      message: '编辑成功',
                                      type: 'success',
                                      duration: 5 * 1000
                                  })
                                  this.$emit('updateRow')
                                  this.dialog = false
                              })
                          }else{
                              addaccident(this.formData).then(_ => {
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
