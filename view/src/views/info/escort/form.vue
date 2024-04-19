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
                        <el-form-item label="选择角色" prop="group">
                            <el-checkbox-group v-model="formData.group">
                                <el-checkbox v-for="item in roles" :key="item.id" :label="item.id" v-if="item.type == 2">{{item.title}}</el-checkbox>
                            </el-checkbox-group>
                        </el-form-item>
                        <el-form-item label="用户名" prop="username">
                            <el-input v-model="formData.username" clearable placeholder="请输入2-10个字符"></el-input>
                        </el-form-item>
                        <!-- <el-form-item label="昵称" prop="nickname">
                            <el-input v-model="formData.nickname" clearable placeholder="请输入20个以内的中文字符"></el-input>
                        </el-form-item> -->
                        <el-form-item label="手机号" prop="phone">
                            <el-input v-model="formData.phone" clearable placeholder="请输入正确的手机号"></el-input>
                        </el-form-item>
                        <el-form-item label="邮箱" prop="email">
                            <el-input v-model="formData.email" clearable
                                      placeholder="请输入正确的邮箱"></el-input>
                        </el-form-item>
                        <el-form-item label="登录密码" prop="password" :rules="formData.id===0?saveRules.password:[{require:false}]">
                            <el-input v-model="formData.password" clearable show-password
                                      autocomplete="new-password"
                                      placeholder="请输入6-18个字母和数字下划线"></el-input>
                        </el-form-item>
                        <el-form-item label="身份证">
                            <el-input v-model="formData.id_card_num" clearable
                                      placeholder="请输入身份证"></el-input>
                        </el-form-item>
                        <el-form-item label="驾驶证号">
                            <el-input v-model="formData.dirver_card_num" clearable
                                      placeholder="请输入驾驶证号"></el-input>
                        </el-form-item>
                        <el-form-item label="从业资格证号">
                            <el-input v-model="formData.cert_card_num" clearable
                                      placeholder="请输入从业资格证号"></el-input>
                        </el-form-item>
                        <el-form-item label="入职时间">
                          <el-input v-model="formData.employ_time" type="date" placeholder="选择日期"></el-input>
                        </el-form-item>
                        <el-form-item label="身份证正面" prop="card_front">
                            <UploadImage ref="Image_card_front" v-model="formData.card_front"></UploadImage>
                        </el-form-item>
                        <el-form-item label="身份证反面" prop="card_back">
                            <UploadImage ref="Image_card_back"" v-model="formData.card_back"></UploadImage>
                        </el-form-item>
                        <el-form-item label="驾驶证正面" prop="driver_card_front">
                            <UploadImage ref="Image_driver_card_front" v-model="formData.driver_card_front"></UploadImage>
                        </el-form-item>
                        <el-form-item label="驾驶证反面" prop="driver_card_back">
                            <UploadImage ref="Image_driver_card_back" v-model="formData.driver_card_back"></UploadImage>
                        </el-form-item>
                        <el-form-item label="从业资格证正面" prop="cert_front">
                            <UploadImage ref="Image_cert_front" v-model="formData.cert_front"></UploadImage>
                        </el-form-item>
                        <el-form-item label="从业资格证反面" prop="cert_back">
                            <UploadImage ref="Image_cert_back" v-model="formData.cert_back"></UploadImage>
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

import { getRole, adddriverAdmin, editdriverAdmin,getdriverInfo } from '@/api/admin.js'
import UploadImage from '@/components/Upload/SingleImage'
import { validUsername, validNickname, validPhone, validEmail, validPassword } from '@/utils/validate'

export default {
  name: "AdminForm",
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
      roles: [],
      drawerShow:false,
      saveRules: {
        group: [{ required: true, trigger: 'blur',validator: validateGroup }],
        username: [{ required: true, trigger: 'blur', validator: validateUsername }],
        // nickname: [{ required: true, trigger: 'blur', validator: validateNickname }],
        phone: [{ required: true, trigger: 'blur', validator: validatePhone }],
        // email: [{ required: true, trigger: 'blur', validator: validateEmail }],
        password: [{ required: true, trigger: 'blur', validator: validatePassword }]
      },
      formData: {
        id: 0,
        username: '',
        group: [],
        nickname: '',
        phone: '',
        email: '',
        password: '',
        card_front: '',
        card_back: '',
        driver_card_front: '',
        driver_card_back: '',
        cert_back: '',
        id_card_num: '',
        card_badirver_card_numck: '',
        cert_card_num: '',
        employ_time: '',
        type: 2
      },
    }
  },
  created() {
    this.getRole()
  },
  methods: {
    getRole() {
      getRole().then(response => {
          if(response !== undefined){
              this.roles = response.data
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
      this.title = '新增驾驶员'
      this.resetData()
    },
    resetData(){
      this.formData.id = 0
      this.formData.username = ''
      this.formData.nickname = ''
      this.formData.phone = ''
      this.formData.email = ''
      this.formData.avatar = ''
      this.formData.password = ''
      this.formData.autograph = ''
      this.formData.group = []
    },
    getdriverInfo(id){
      getdriverInfo({id:id}).then(response=>{
          if(response !== undefined){
              this.title = '编辑管理员'
              this.formData.id = response.id
              this.formData.username = response.username
              this.formData.nickname = response.nickname
              this.formData.phone = response.phone
              this.formData.email = response.email
              this.formData.id_card_num = response.id_card_num
              this.formData.dirver_card_num = response.dirver_card_num
              this.formData.cert_card_num = response.cert_card_num
              this.formData.employ_time = new Date(response.employ_time).toISOString().slice(0,10)
              this.formData.card_front = response.card_front
              this.$refs.Image_card_front.imgUrl = response.card_front
              this.formData.card_back = response.card_back
              this.$refs.Image_card_back.imgUrl = response.card_back
              this.formData.driver_card_front = response.driver_card_front
              this.$refs.Image_driver_card_front.imgUrl = response.driver_card_front
              this.formData.driver_card_back = response.driver_card_back
              this.$refs.Image_driver_card_back.imgUrl = response.driver_card_back
              this.formData.cert_front = response.cert_front
              this.$refs.Image_cert_front.imgUrl = response.cert_front
              this.formData.cert_back = response.cert_back
              this.$refs.Image_cert_back.imgUrl = response.cert_back
              response.roles.forEach((item,_) => {
                  this.formData.group.push(item.id)
              })
          }
      })
    },
    saveData() {
      this.$confirm('您确定要提交吗？', '温馨提示')
        .then(_ => {
          this.$refs.saveForm.validate(valid => {
            if (valid) {
              if(this.formData.id){
                editdriverAdmin(this.formData).then(_ => {
                  this.$message({
                    message: '编辑成功',
                    type: 'success',
                    duration: 5 * 1000
                  })
                  this.$emit('updateRow')
                  this.dialog = false
                })
              }else{
                adddriverAdmin(this.formData).then(_ => {
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
