<template>
    <div class="app-container">
        <el-row style="margin-bottom: 10px;">
            <el-tooltip class="item" effect="dark" content="刷新" placement="top">
                <el-button type="warning" icon="el-icon-refresh" circle @click="handleReload"></el-button>
            </el-tooltip>
            <el-tooltip class="item" effect="dark" content="新增" placement="top">
                <el-button type="success" v-permission="'auth.auth.add'" icon="el-icon-plus" circle @click="handleAdd"></el-button>
            </el-tooltip>
            <el-tooltip class="item" effect="dark" content="删除" placement="top">
                <el-button type="danger" v-permission="'auth.auth.delete'" :disabled="buttonDisabled" @click="handleDeleteAll" icon="el-icon-delete" circle></el-button>
            </el-tooltip>
        </el-row>
        <el-table
                ref="multipleTable"
                :data="tableData"
                tooltip-effect="dark"
                row-key="id"
                border
                stripe
                style="width: 100%"
                v-loading="loading"
                :tree-props="{children: 'children', hasChildren: 'hasChildren'}">
            <el-table-column
                    width="150">
                <template slot="header" slot-scope="scope">
                    <el-checkbox v-model="isCheckedAll" @change="selectAll" >ID</el-checkbox>
                </template>
                <template slot-scope="scope">
                    <el-checkbox v-model="scope.row.selected==0?false:true" true-label="1" false-label="0" @change="handleSelect(scope.row)">{{scope.row.id}}</el-checkbox>
                </template>
            </el-table-column>
            <el-table-column
                    prop="name"
                    label="标识"
                    sortable
                    align="center"
                    width="150">
            </el-table-column>
            <el-table-column
                    prop="title"
                    width="150"
                    label="名称">
            </el-table-column>
            <el-table-column
                    prop="icon"
                    align="center"
                    width="100"
                    label="图标">
                <template slot-scope="scope">
                    <i :class="scope.row.icon" v-if="scope.row.menu==2"></i>
                    <svg-icon :icon-class="scope.row.icon" v-if="scope.row.menu==1"></svg-icon>
                </template>
            </el-table-column>
            <el-table-column
                    prop="menu"
                    align="center"
                    width="100"
                    label="类型">
                <template slot-scope="scope">
                    <el-tag :type="scope.row.menu===1?'primary':'success'" v-text="scope.row.menu===1?'导航':'菜单'"></el-tag>
                </template>
            </el-table-column>
            <el-table-column
                    prop="path"
                    align="center"
                    width="180"
                    label="路径">
            </el-table-column>
            <el-table-column
                    prop="component"
                    align="center"
                    width="180"
                    label="组件">
            </el-table-column>
            <el-table-column
                    prop="route"
                    align="center"
                    width="180"
                    label="路由地址">
            </el-table-column>
            <el-table-column
                    fixed="right"
                    label="操作"
                    align="center"
                    min-width="200">
                <template slot-scope="scope">
                    <el-tooltip class="item" effect="dark" content="编辑" placement="top">
                        <el-button size="mini" type="primary" v-permission="'auth.auth.edit'" icon="el-icon-edit-outline" circle
                                   @click="handleEdit(scope.row)"></el-button>
                    </el-tooltip>
                    <el-tooltip class="item" effect="dark" content="删除" placement="top">
                        <el-button size="mini" type="danger"  v-permission="'auth.auth.delete'" icon="el-icon-delete"
                                   circle @click="handleDelete([scope.row.id])"></el-button>
                    </el-tooltip>
                </template>
            </el-table-column>
        </el-table>
        <!--表单-->
        <myForm ref="myAttr" @updateRow="handleReload"/>
    </div>
</template>

<script>
import { getList,deleteRule } from "@/api/auth.js";
import myForm from './form.vue'

export default {
  name:'Auth',
  components:{
    myForm
  },
  data() {
    return {
      buttonDisabled: true,
      tableData: [],
      total:0,
      multipleSelection: null,
      loading: true,
      searchShow: false,
      isCheckedAll:false
    }
  },
  created() {
    this.getList();
  },
  methods: {
    getList(){
      this.loading = true
      getList().then(response => {
          if(response !== undefined){
              this.tableData = response
              this.total = this.tableData.length
          }
          this.loading = false
      })
    },
    selectAll(){
      let selected = this.isCheckedAll?1:0
      this.tableData.forEach(item=>{
        this.handleSelect(item,selected)
      })
    },
    handleSelect(row,selected=2,first=true) {
      if(selected==2){
        row.selected = !row.selected
      }else{
        row.selected = selected
      }
      if(row.children){
        row.children.forEach(val=>{
          this.handleSelect(val,row.selected,false)
        })
      }
      if(row.pid){
        this.reverseSelect(this.tableData,row,row.selected)
      }
      if(first){
        this.watchAll(row.selected)
      }
    },
    watchAll(selected){
      let num = 0
      this.tableData.forEach(val=>{
        if(val.selected==1){
          num++
        }
      })
      if(selected==0){
        this.isCheckedAll = false
      }
      if(num==0){
        this.buttonDisabled = true
      }else{
        this.buttonDisabled = false
        if(num==this.total&&selected==1){
          this.isCheckedAll = true
        }
      }
    },
    reverseSelect(data,row,selected){
      data.forEach(val=>{
        if(val.id === row.pid){
            if(selected){
              val.selected = selected
              this.reverseSelect(this.tableData,val,selected)
            }else{
              if(this.isChecked(val.children,row.id)){
                val.selected = selected
                this.reverseSelect(this.tableData,val,selected)
              }
            }
        }else{
            if(val.children){
              this.reverseSelect(val.children,row,selected)
            }
        }
      })
    },
    isChecked(data,id){
      let state = true
      data.forEach(val=>{
        if(val.selected&&val.id!==id){
          state = false
        }
      })
      return state
    },
    //新增
    handleAdd() {
      this.$refs.myAttr.showForm()
    },
    //刷新重置
    handleReload() {
      this.getList()
    },
    //编辑
    handleEdit(raw){
      this.$refs.myAttr.getInfo(raw.id)
      this.$refs.myAttr.showForm()
    },
    //删除
    handleDelete(ids){
      this.$confirm('您确定要删除该权限吗?', '温馨提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        deleteRule({ ids: ids }).then(response => {
          this.getList()
          this.$message({
            type: 'success',
            message: '删除成功!'
          });
        })
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '已取消操作'
        });
      })
    },
    getSelected(data,ids){
      data.forEach(item=>{
        if(item.selected==1){
          ids.push(item.id)
        }
        if(item.children){
          ids = this.getSelected(item.children,ids)
        }
      })
      return ids
    },
    //批量删除
    handleDeleteAll(){
      const ids = this.getSelected(this.tableData,[])
      this.handleDelete(ids)
    },
  },
}
</script>
