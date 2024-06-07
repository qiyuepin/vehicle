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
                    <el-tab-pane label="倒料">

                        <el-form-item label="从" prop="old_trailer">
                            <el-select v-model="formData.old_trailer" filterable  placeholder="请选择导入挂车">
                                <el-option
                                v-for="item in oldlist"
                                :key="item.value"
                                :label="item.trailer_plate"
                                :value="item.trailer_plate">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="导入" prop="new_trailer">
                          <el-select v-model="formData.new_trailer" filterable  placeholder="请选择导入挂车">
                            <el-option
                              v-for="item in newlist"
                              :key="item.value"
                              :label="item.trailer_plate"
                              :value="item.trailer_plate">
                            </el-option>
                          </el-select>
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

import { Pouring, getcartrailerInfo, getcarlist} from '@/api/Info.js'




export default {
  name: "AdminForm",
  components: {

  },
  data() {
    return {
      title:'',
      dialog: false,
      trailer_scope: [],
      oldlist: [],
      newlist: [],
      drawerShow:false,
      saveRules: {
        old_trailer: [{ required: true, message: '导出挂车不能为空', trigger: 'blur'}],
        new_trailer: [{ required: true, message: '导入挂车不能为空', trigger: 'blur'}]
      },
      formData: {
        old_trailer: '',
        new_trailer: ''
      },
    }
  },
  created() {
    this.getcarlist()
  },
  methods: {
    getcarlist(){
        getcarlist({'trailer':1}).then(response=>{
            if(response !== undefined){
                console.log(response.trailer)
                this.oldlist = response.trailer;
            }
        })
        getcarlist({'trailer':0}).then(response=>{
            if(response !== undefined){
                console.log(response.trailer)
                this.newlist = response.trailer;
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
      this.title = '倒料'
      this.resetData()
    },
    resetData(){
      this.formDataold_trailer = ''
      this.formDatanew_trailer = ''
    },

    saveData() {
      this.$confirm('您确定要提交吗？', '温馨提示')
        .then(_ => {
          this.$refs.saveForm.validate(valid => {
            if (valid) {
                
                Pouring(this.formData).then(_ => {
                  this.$message({
                    message: '新增成功',
                    type: 'success',
                    duration: 5 * 1000
                  })
                  this.$emit('updateRow')
                  this.dialog = false
                  this.getcarlist()
                })
              
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
