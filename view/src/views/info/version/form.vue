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
                        
                        <el-form-item label="版本号" prop="new_version">
                            <el-input v-model="formData.new_version" clearable placeholder="请输入版本号"></el-input>
                        </el-form-item>
                        <el-form-item label="说明" prop="msg">
                            <el-input v-model="formData.msg" clearable placeholder="请输入说明"></el-input>
                        </el-form-item>
                        
                        <el-form-item label="上传" prop="href">
                          <SingleFile ref="href" v-model="formData.href" @update:name="handleNameChange" ></SingleFile>
                          <input
                            type="hidden"
                            v-bind:value="formData.name"
                            @input="updateName($event)"
                          />
                          <!-- <el-button size="mini" v-bind:value="formData.name">{{formData.name}}</el-button> -->
                          <!-- <span v-bind:value="formData.name">{{formData.name}}</span> -->
                          <el-link :underline="false" v-bind:value="formData.name">{{formData.name}}</el-link>
                          <el-link :underline="false"  @click="downloadFile">点击下载</el-link>
                          <!-- <el-button size="mini" @click="$refs.drawer.closeDrawer()" v-bind:value="formData.name">下载</el-button> -->
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

import { addcarhead, uploadVersion, getversioninfo} from '@/api/Info.js'
import SingleFile from '@/components/Upload/SingleFile'

export default {
  name: "AdminForm",
  components: {
    SingleFile
  },
  data() {
    return {
      title:'',
      dialog: false,
      carhead_scope: [],

      drawerShow:false,

      formData: {
        id: 0,
        new_version: '',
        type: '',
        msg: '',
        href: '',
        name: ''
      },
    }
  },

  created() {
    // this.getcarscope()
    //   this.getheadbranch()
    //   this.getdischarge()
    //   this.getpowersupply()
  },
  methods: {
    downloadFile() {
      const link = document.createElement('a');
      
      // 设置文件的下载链接
      link.href = this.formData.href;
      
      // 设置文件下载时的名称
      link.download = this.formData.name;
      
      // 触发点击下载
      link.click();
    },
    handleNameChange(newName) {
      console.log(newName)
      this.formData.name = newName;  // 更新 name
    },
    handleClose() {
      this.dialog = false
      this.drawerShow = false
    },
    showForm() {
      this.dialog = true
      this.drawerShow = true
      this.title = '新增车头信息'
      // this.resetData()
    },
    // resetData(){
    //   this.formData.id = 0
    //   this.formData.carhead_plate = ''
    //   this.formData.carhead_brand = ''
    //   this.formData.carhead_weight = ''
    //   this.formData.transport_cert = ''
    //   this.formData.carhead_scope = []
    //   this.formData.regist_time = null
    //   this.formData.scrapp_time = null
    //   this.formData.inspection_time = null
    //   this.formData.validity_time = null
    //   this.formData.traffic_time = null
    //   this.formData.driving_license = []
    //   this.formData.transport_license = ''
    //   this.formData.traffic_insurance = ''
    //   this.formData.business_insurance = ''
    //   this.formData.discharge_level = ''
    //   this.formData.power_supply = ''
    //   this.formData.carbody_picture = []
    //   this.formData.Vaildplate = ''
    //   this.formData.scrapp_status = true
    //   this.formData.inspection_status = true
    //   this.formData.validity_status = true
    //   this.formData.traffic_status = true
    //   this.$refs.Image_carbody_picture.uploadFileList = []
    //   this.$refs.Image_driving_license.uploadFileList = []
    // },
    getversioninfo(id){
      getversioninfo({id:id}).then(response => {
        console.log(response.transport_license)
        if(response !== undefined) {
              this.title = '编辑车头信息'
              this.formData.id = response.id
              this.formData.href = response.href
              this.formData.name = response.name
              this.formData.type = response.type
              this.formData.new_version = response.new_version
              this.formData.msg = response.msg
              this.formData.transport_cert = response.transport_cert
             
          }
      })
    },

    saveData() {
      this.$confirm('您确定要提交吗？', '温馨提示')
        .then(_ => {
          this.$refs.saveForm.validate(valid => {
            if (valid) {
              if(this.formData.id){
                uploadVersion(this.formData).then(_ => {
      
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
    ::v-deep .el-input{
      width: 200px;
    }
</style>
