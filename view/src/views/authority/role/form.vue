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
                        <el-form-item label="角色名称" prop="title">
                            <el-input v-model="formData.title" clearable placeholder="请输入20个以内的字符"></el-input>
                        </el-form-item>
                        <el-form-item label="菜单权限" prop="rules">
                            <el-tree
                                    ref="tree"
                                    :data="treeData"
                                    :show-checkbox="true"
                                    node-key="id"
                                    :highlight-current="true"
                                    :check-on-click-node="true"
                                    :expand-on-click-node="false"
                                    :default-expand-all="true"
                                    :default-expanded-keys="treeData"
                                    :default-checked-keys="checkedData"
                                    :props="defaultProps">
                            </el-tree>
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

import { getRules,getInfo,addRole,editRole } from '@/api/role.js'

export default {
  name: "RoleForm",
  data() {
    const validateName = (rule, value, callback) => {
      if (value.length>20) {
        callback(new Error('角色名称必须是20个以内的字符'))
      } else {
        callback()
      }
    }
    return {
      treeData:[],
      checkedData:[],
      defaultProps: {
        children: 'children',
        label: 'label'
      },
      title:'',
      dialog: false,
      drawerShow:false,
      saveRules: {
        title: [{ required: true, trigger: 'blur', validator: validateName }],
      },
      formData: {
        id: 0,
        title: '',
        rules: [],
      },
    }
  },
  created() {
    this.getRules()
  },
  methods: {
    getRules(){
      getRules().then(response => {
          if(response !== undefined){
            console.log(response);
              this.treeData = response
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
      this.title = '新增角色'
      this.resetData()
    },
    resetData(){
      this.formData.id = 0
      this.formData.title = ''
      this.formData.rules = []
      this.checkedData = []
    },
    getInfo(id){
      getInfo({id:id}).then(response=>{
          if(response !== undefined){
              this.title = '编辑角色'
              this.formData.id = response.id
              this.formData.title = response.title
              let checkDatas = []
              response.rules.forEach(id=>{
                  this.IscheckThird(id,this.treeData,checkDatas)
              })
              this.checkedData = checkDatas
          }
      })
    },
    IscheckThird(id,data,arr){
      data.forEach(val=>{
        if(id===val['id']){
          if(!val.children){
            arr.push(id)
          }
        }else{
          if(val.children){
            this.IscheckThird(id,val.children,arr)
          }
        }
      })
    },
    saveData() {
      const childs = this.$refs.tree.getCheckedKeys()
      const parents = this.$refs.tree.getHalfCheckedKeys()
      this.formData.rules = parents.concat(childs)
      this.$confirm('您确定要提交吗？', '温馨提示')
        .then(_ => {
          this.$refs.saveForm.validate(valid => {
            if (valid) {
              if(this.formData.id){
                editRole(this.formData).then(_ => {
                  this.$message({
                    message: '编辑成功',
                    type: 'success',
                    duration: 5 * 1000
                  })
                  this.$emit('updateRow')
                  this.dialog = false
                })
              }else{
                addRole(this.formData).then(_ => {
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
