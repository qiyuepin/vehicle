<template>
    <el-drawer
            v-if="drawerShow"
            :before-close="handleClose"
            :with-header="false"
            :visible.sync="dialog"
            :wrapperClosable="false"
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
                        <el-form-item label="选择上级" prop="pid">
                            <el-cascader
                                    v-model="formData.pid"
                                    clearable
                             
                                    :options="options"
                                    :props="selectProps"
                                    @change="handleChange"></el-cascader>
                        </el-form-item>
                        <el-form-item label="权限标识" prop="name">
                            <el-input v-model="formData.name" clearable placeholder="auth.admin.add"></el-input>
                        </el-form-item>
                        <el-form-item label="权限名称" prop="title">
                            <el-input v-model="formData.title" clearable placeholder="请输入20个以内的中文字符"></el-input>
                        </el-form-item>
                        <el-form-item label="权限路径">
                            <el-input v-model="formData.path" maxlength="50" clearable placeholder="一级：/authority 二级：user 三级：add"></el-input>
                        </el-form-item>
                        <el-form-item label="路由地址">
                            <el-input v-model="formData.route" maxlength="50" clearable placeholder="路由地址（接口地址）"></el-input>
                        </el-form-item>
                        <el-form-item label="权限图标" prop="icon">
                            <el-input v-model="formData.icon" clearable placeholder="导航或菜单图标"></el-input>
                        </el-form-item>
                        <el-form-item label="权限组件">
                            <el-input v-model="formData.component" clearable placeholder="一级：layout 二级：authority/user"></el-input>
                        </el-form-item>
                        <el-form-item label="根路由显示">
                            <el-radio-group v-model="formData.always_show">
                                <el-radio :label="0">否</el-radio>
                                <el-radio :label="1">是</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        <el-form-item label="权限排序">
                            <el-input type="number" min="0" v-model="formData.sort" clearable placeholder="数字越大越靠前"></el-input>
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

import { getSecondRules,getInfo,addRule,editRule } from '@/api/auth.js'
import { validNickname } from '@/utils/validate.js'

export default {
  name: "RoleForm",
  data() {
    // const validateTitle = (rule, value, callback) => {
    //   if (!validNickname(value)) {
    //     callback(new Error('权限名称必须是20个以内的中文字符'))
    //   } else {
    //     callback()
    //   }
    // }
    return {
      title:'',
      dialog: false,
      drawerShow:false,
      saveRules: {
        name: [{ required: true, trigger: 'blur', message:'权限标识不能为空' }],
        // title: [{ required: true, trigger: 'blur', validator: validateTitle }],
        icon: [{ required: true, trigger: 'blur', message:'权限图标不能为空' }],
      },
      formData: {
        id:0,
        pid: 0,
        title: '',
        name: '',
        sort:0,
        component:'',
        always_show:0,
        icon:'',
        path:'',
        route:''
      },
      options: [],
      cascaderDisabled:false,
      selectProps:{
        value:'id',
        emitPath:false,
        checkStrictly: true
      }
    }
  },
  methods: {
    getRules(){
      getSecondRules().then(response => {
        this.options = response
      })
    },
    handleChange(){
    },
    handleClose() {
      this.dialog = false
      this.drawerShow = false
    },
    showForm() {
      this.dialog = true
      this.drawerShow = true
      this.title = '新增权限'
      this.getRules()
      this.resetData()
    },
    resetData(){
      this.formData.id = 0
      this.formData.pid = 0
      this.formData.title = ''
      this.formData.name = ''
      this.formData.sort = 0
      this.formData.path = ''
      this.formData.route = ''
      this.formData.component = ''
      this.formData.icon = ''
      this.formData.always_show = 0
      this.cascaderDisabled = false
      console.log(this.formData)
    },
    getInfo(id){
      getInfo({id:id}).then(response=>{
              if(response !== undefined){
                  this.title = '编辑权限'
                  this.formData.id = response.id
                  this.formData.title = response.title
                  this.formData.pid = response.pid
                  this.formData.name = response.name
                  this.formData.sort = response.sort
                  this.formData.path = response.path
                  this.formData.route = response.route
                  this.formData.component = response.component
                  this.formData.icon = response.icon
                  this.formData.always_show = response.always_show
                  this.cascaderDisabled = true
              }
      })
    },
    saveData() {
      this.$confirm('您确定要提交吗？', '温馨提示')
        .then(_ => {
          this.$refs.saveForm.validate(valid => {
            if (valid) {
              if(this.formData.id){
                editRule(this.formData).then(_ => {
                  this.$message({
                    message: '编辑成功',
                    type: 'success',
                    duration: 5 * 1000
                  })
                  this.$emit('updateRow')
                  this.dialog = false
                })
              }else{
                addRule(this.formData).then(_ => {
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
