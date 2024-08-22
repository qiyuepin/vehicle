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

                        <el-form-item label="原挂车" prop="old_trailer">
                            <el-select v-model="formData.old_trailer" filterable  placeholder="请选择导入挂车"  @change="Selectoldtrailer">
                                <el-option
                                v-for="item in oldlist"
                                :key="item.value"
                                :label="item.trailer_plate"
                                :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="货品名称" prop="product_name">
                            <el-input v-model="formData.product_name" clearable placeholder="请输入货品名称"></el-input>
                        </el-form-item>
                        <el-form-item label="货品数量" prop="product_quantity">
                            <el-input-number  v-model.number="formData.product_quantity" clearable placeholder="请输入货品数量" :min="minquantity":max="maxquantity" @input="updateProductQuantity"></el-input-number>
                            <span>（ 吨 ）</span>
                        </el-form-item>
                        <el-form-item label="目标挂车" prop="new_trailer">
                          <el-select v-model="formData.new_trailer" filterable  placeholder="请选择导入挂车"  @change="Selectnewtrailer">
                            <el-option
                              v-for="item in newlist"
                              :key="item.value"
                              :label="item.trailer_plate"
                              :value="item.id">
                            </el-option>
                          </el-select>
                      </el-form-item>
                      <el-form-item label="目标挂车货品名称" prop="new_product_name">
                          <el-input v-model="new_product_name" disabled></el-input>
                      </el-form-item>
                      <el-form-item label="目标挂车货品数量" prop="new_product_quantity">
                          <el-input v-model.number="new_product_quantity" disabled></el-input><span>(现有产品数量)</span>
                      </el-form-item>
                      <!-- <el-form-item label="目标挂车货品总数" prop="all_product_quantity">
                          <el-input v-model="all_product_quantity" disabled></el-input><span>(预计倒料后货品总数)</span>
                      </el-form-item> -->
                      <p>预计倒料后目标挂车货品总数: {{ formData.all_product_quantity }}吨</p>
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
      minquantity: 0,
      maxquantity: 0,
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
        product_quantity: 0,
        product_name: '',
        old_trailer: '',
        new_trailer: '',
        all_product_quantity: 0,
      },
      new_product_name: '',
      new_product_quantity: 0,
    }
  },
  created() {
    this.getcarlist()
  },
  methods: {
    Selectoldtrailer(value){
      const selectedproduct = this.oldlist.find(item => item.id === value);
      console.log(this.formData.old_trailer)
      if (selectedproduct) {
        console.log(selectedproduct)
        this.formData.product_name = selectedproduct.product_name;
        this.formData.product_quantity = selectedproduct.product_quantity;
        this.maxquantity = selectedproduct.product_quantity;
        // const selectednewproduct = this.newlist.find(item => item.product_name === selectedproduct.product_name);
        // this.newlist = selectednewproduct;
        // console.log(this.newlist);
        console.log(selectedproduct.id);
        getcarlist({'product_name':selectedproduct.product_name,'id':selectedproduct.id}).then(response=>{
            if(response !== undefined){
                console.log(response.trailer)
                this.newlist = response.trailer;
            }
        })
      } else {
        this.formData.product_name = '';
        this.formData.product_quantity = '';
      }
      // console.log('Selected option:', value);
    },
    Selectnewtrailer(value){
      console.log(value);
      const selectednewproduct = this.newlist.find(item => item.id === value);
      console.log(this.new_product_name);
      if (selectednewproduct) {
        console.log(selectednewproduct)
        // this.formData.product_name = selectednewproduct.product_name;
        // this.formData.product_quantity = selectednewproduct.product_quantity;
        this.new_product_name = selectednewproduct.product_name;
        this.new_product_quantity = selectednewproduct.product_quantity?selectednewproduct.product_quantity:0;
        this.formData.all_product_quantity = parseFloat(this.new_product_quantity) + parseFloat(this.formData.product_quantity);
        console.log(this.formData.all_product_quantity);
        // getcarlist({'product_name':selectedproduct.product_name,'id':selectedproduct.id}).then(response=>{
        //     if(response !== undefined){
        //         console.log(response.trailer)
        //         this.newlist = response.trailer;
        //     }
        // })
      } else {
        this.new_product_name = '';
        this.new_product_quantity = '';
      }
      // console.log('Selected option:', value);
    },
    updateProductQuantity(value){
      console.log(this.maxquantity)
      const numericValue = parseFloat(value);
      if (value < 0) {
        this.formData.product_quantity = 0;
      } else if (!isNaN(numericValue)) {
        // this.product_quantity = this.maxquantity;
        value = Math.min(Math.max(numericValue, this.minquantity), this.maxquantity);
        this.formData.product_quantity = Math.min(Math.max(numericValue, this.minquantity), this.maxquantity);
      }
      // this.formData.product_quantity = value;
      this.formData.all_product_quantity = parseFloat(this.new_product_quantity) + parseFloat(this.formData.product_quantity);
    },
    getcarlist(){
        getcarlist({'trailer':1}).then(response=>{
            if(response !== undefined){
                console.log(response.trailer)
                this.oldlist = response.trailer;
            }
        })
        // getcarlist({'trailer':1}).then(response=>{
        //     if(response !== undefined){
        //         console.log(response.trailer)
        //         this.newlist = response.trailer;
        //     }
        // })
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
                    message: '倒料成功',
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
    ::v-deep .el-form-item__label{
      width: 140px!important;
    }
    ::v-deep .el-form-item__content{
      margin-left: 140px!important;
    }
    ::v-deep .el-input--small{
      max-width: 200px;
    }
</style>
