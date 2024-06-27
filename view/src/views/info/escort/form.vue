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
                
                      <el-form-item label="姓名" prop="name">
                          <el-input v-model="formData.name" clearable placeholder="请输入姓名"></el-input>
                      </el-form-item>

                      <el-form-item label="手机号" prop="phone">
                          <el-input v-model="formData.phone" clearable placeholder="请输入正确的手机号"></el-input>
                      </el-form-item>
 
                      <el-form-item label="手机号2" prop="phone2">
                          <el-input v-model="formData.phone2" clearable placeholder="请输入正确的手机号"></el-input>
                      </el-form-item>
                      <el-form-item label="身份证号" prop="id_card">
                          <el-input v-model="formData.id_card" clearable placeholder="请输入正确的身份证号"  maxLength='18'></el-input>
                      </el-form-item>

                      <el-form-item label="从业资格证号" prop="cert_card_num">
                          <el-input v-model="formData.cert_card_num" clearable
                                    placeholder="请输入从业资格证号"  maxLength='18'></el-input>
                      </el-form-item>
                      <el-form-item label="是否离职" prop="escort_status">
                          <el-radio-group v-model="formData.escort_status">
                            <el-radio v-model="formData.escort_status" label="2">是</el-radio>
                            <el-radio v-model="formData.escort_status" label="0">否</el-radio>
                          </el-radio-group>
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

import { addescort, editescort,getescortInfo } from '@/api/Info.js'
import UploadImage from '@/components/Upload/SingleImage'
import { validPhone,validIDCard } from '@/utils/validate'

export default {
name: "myForm",
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
  const validCard = (rule, value, callback) => {
    console.log(value)
    if (!validIDCard(value)) {
      callback(new Error('请输入正确身份证号（18位数字）'))
    } else {
      callback()
    }
  }
  const validcertCard = (rule, value, callback) => {
    console.log(value)
    if (!validIDCard(value)) {
      callback(new Error('请输入正确从业资格证号（18位数字）'))
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

      name: [{ required: true, message: '姓名不能为空', trigger: 'blur'}],
      id_card: [{ required: true, trigger: 'blur', validator: validCard}],
      cert_card_num: [{ required: true, trigger: 'blur', validator: validcertCard}],
      phone: [{ required: true, trigger: 'blur', validator: validatePhone }],
      phone2: [{ required: true, trigger: 'blur', validator: validatePhone }],
    },
    formData: {
      id: 0,
      name: '',
      phone: '',
      phone2: '',
      id_card: '',
      card_front: '',
      card_back: '',
      // driver_card_front: '',
      // driver_card_back: '',
      cert_back: '',
      // card_badirver_card_numck: '',
      cert_card_num: '',
      employ_time: '',
      escort_status: ''
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
    this.title = '新增押运员'
    this.resetData()
  },
  resetData(){
    this.formData.id = 0
    this.formData.name = ''
    this.formData.phone = ''
    this.formData.phone2 = ''
    this.formData.id_card = ''
    this.formData.cert_card_num = ''
    this.formData.employ_time = null
    this.formData.card_front = ''
    this.formData.card_back = ''
    this.formData.cert_front = ''
    this.formData.cert_back = ''
    this.formData.escort_status = ''
    this.$refs.Image_card_front.imgUrl = ''
    this.$refs.Image_card_back.imgUrl = ''
    this.$refs.Image_cert_front.imgUrl = ''
    this.$refs.Image_cert_back.imgUrl = ''
  },
  getescortInfo(id){
    getescortInfo({id:id}).then(response=>{
        if(response !== undefined){
            this.title = '编辑押运员'
            this.formData.id = response.id
            this.formData.name = response.name
            this.formData.phone = response.phone
            this.formData.phone2 = response.phone2
            this.formData.id_card = response.id_card
            this.formData.cert_card_num = response.cert_card_num
            this.formData.employ_time = new Date(response.employ_time).toISOString().slice(0,10)
            this.formData.escort_status = response.escort_status
            this.formData.card_front = response.card_front
            this.$refs.Image_card_front.imgUrl = response.card_front
            this.formData.card_back = response.card_back
            this.$refs.Image_card_back.imgUrl = response.card_back
            this.formData.cert_front = response.cert_front
            this.$refs.Image_cert_front.imgUrl = response.cert_front
            this.formData.cert_back = response.cert_back
            this.$refs.Image_cert_back.imgUrl = response.cert_back
        }
    })
  },
  saveData() {
    this.$confirm('您确定要提交吗？', '温馨提示')
      .then(_ => {
        this.$refs.saveForm.validate(valid => {
          if (valid) {
            if(this.formData.id){
              editescort(this.formData).then(_ => {
                this.$message({
                  message: '编辑成功',
                  type: 'success',
                  duration: 5 * 1000
                })
                this.$emit('updateRow')
                this.dialog = false
              })
            }else{
              addescort(this.formData).then(_ => {
                // resetData();
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
