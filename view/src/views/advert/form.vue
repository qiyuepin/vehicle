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
                        <el-form-item label="广告位" prop="position">
                            <el-select v-model="formData.position" style="width: 100%" placeholder="请选择广告位">
                                <el-option
                                        v-for="item in positions"
                                        :key="item.value"
                                        :label="item.label"
                                        :value="item.value">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="标题" prop="title">
                            <el-input v-model="formData.title" maxlength="20" clearable placeholder="请输入广告标题"></el-input>
                        </el-form-item>
                        <el-form-item label="排序" prop="sort">
                            <el-input v-model="formData.sort" type="number" min="0" clearable placeholder="请输入排序，数字越大越靠前"></el-input>
                        </el-form-item>
                        <el-form-item label="广告图片" prop="thumb">
                            <UploadImage ref="Image" v-model="formData.thumb"></UploadImage>
                        </el-form-item>
                        <el-form-item label="PDF文件" prop="pdf">
                            <UploadPdf ref="Pdf" v-model="formData.pdf"></UploadPdf>
                        </el-form-item>
                        <el-form-item label="多图上传" prop="images">
                            <MultiImage ref="Images" v-model="formData.images"></MultiImage>
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

import { addAdvert, editAdvert,getInfo } from '@/api/advert.js'
import UploadImage from '@/components/Upload/SingleImage'
import UploadPdf from '@/components/Upload/SinglePdf'
import MultiImage from '@/components/Upload/MultiImage'

export default {
  name: "AdminForm",
  components: {
    UploadImage,
    UploadPdf,
    MultiImage
  },
  data() {
    const validatePosition = (rule,value,callback)=>{
      if(value.lenght==0){
        callback(new Error('请选择广告位'))
      }else{
        callback()
      }
    }
    return {
      title:'',
      dialog: false,
      drawerShow:false,
      saveRules: {
        position: [{ required: true, trigger: 'blur',validator: validatePosition }],
        title: [{ required: true, length:20, trigger: 'blur', message:'请输入20个以内的字符' }],
        sort: [{ required: true, trigger: 'blur', message:'请输入排序' }],
        thumb: [{ required: true, trigger: 'blur', message: '请上传图片' }],
      },
      positions:[{
        value: 1,
        label: '首页'
      }],
      formData: {
        position:1,
        id: 0,
        title: '',
        sort: 0,
        thumb: '',
        pdf: '',
        images: []
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
      this.title = '新增广告'
      this.resetData()
      this.$nextTick(() => {
        this.$refs.Pdf.initPdf('https://qiniu.chengzhigang.cn/%E7%A8%8B%E5%BF%97%E5%88%9A-PHP-%E4%B8%AA%E4%BA%BA%E7%AE%80%E5%8E%86.pdf')
      })
    },
    resetData(){
      this.formData.id = 0
      this.formData.position = 1
      this.formData.title = ''
      this.formData.sort = 0
      this.formData.thumb = ''
      this.formData.pdf = ''
      this.formData.images = []
    },
    getInfo(id){
      getInfo({id:id}).then(response=>{
          if(response !== undefined){
              this.title = '编辑广告'
              this.formData.id = response.id
              this.formData.position = 1
              this.formData.title = response.title
              this.formData.sort = response.sort
              this.formData.thumb = response.thumb
              this.$refs.Image.imgUrl = response.thumb
              this.formData.images = [
                  "http://192.168.28.229:81/storage/image/20230817/2ed57293bf0022a8aa5447b34f444dd9.jpg"
              ]
              this.$refs.Images.init([
                  "http://192.168.28.229:81/storage/image/20230817/2ed57293bf0022a8aa5447b34f444dd9.jpg"
              ])
          }
      })
    },
    saveData() {
      this.$confirm('您确定要提交吗？', '温馨提示')
        .then(_ => {
          this.$refs.saveForm.validate(valid => {
            if (valid) {
              if(this.formData.id){
                editAdvert(this.formData).then(_ => {
                  this.$message({
                    message: '编辑成功',
                    type: 'success',
                    duration: 5 * 1000
                  })
                  this.$emit('updateRow')
                  this.dialog = false
                })
              }else{
                addAdvert(this.formData).then(_ => {
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
