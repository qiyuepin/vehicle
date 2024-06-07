<template>
    <div class="app-container">
        <el-form v-if="searchShow" :inline="true" :model="query" class="demo-form-inline" size="small">
            <el-form-item label="关键字">
                <el-input v-model="query.keywords" placeholder="事故地点|事故责任" clearable></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" icon="el-icon-search" @click="handleSearch">查询</el-button>
                <el-button type="warning" icon="el-icon-refresh-left" @click="handleReload">重置</el-button>
            </el-form-item>
        </el-form>
        <el-row style="margin-bottom: 10px;">
          <el-button type="warning" size="mini" @click="handleReload">刷新</el-button>
          <el-button type="success" v-permission="'admin.driver.addaccident'" size="mini"  @click="handleAdd">新增</el-button>
          <el-button type="primary" size="mini" @click="searchShow = !searchShow">搜索</el-button>
          <el-button type="danger" v-permission="'admin.driver.delaccident'" :disabled="buttonDisabled" @click="handleDeleteAll" size="mini">删除</el-button>
        </el-row>
        <el-table
                ref="multipleTable"
                :data="tableData"
                tooltip-effect="dark"
                border
                stripe
                style="width: 100%"
                v-loading="loading"
                @selection-change="handleSelectionChange">
            <el-table-column
                    type="selection"
                    width="40"
                    :selectable="isSelected">
            </el-table-column>
            <el-table-column
                    prop="id"
                    label="ID"
                    align="center"
                    width="80">
            </el-table-column>

            <el-table-column
                    prop="accident_time"
                    label="事故时间"
                    align="center"
                    width="120">
            </el-table-column>
            <!-- <el-table-column
                    prop="nickname"
                    label="昵称"
                    align="center"
                    width="120">
            </el-table-column> -->
            <el-table-column
                    prop="accident_place"
                    label="事故地点"
                    align="center"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="accident_des"
                    label="事故描述"
                    align="center"
                    width="200">
            </el-table-column>
            <el-table-column
                    prop="accident_respons"
                    label="事故责任"
                    align="center"
                    width="200">
            </el-table-column>

            <el-table-column
                    prop="accident_kind"
                    label="事故类别"
                    align="center"
                    width="200">
            </el-table-column>

            <el-table-column
                    prop="accident_loss"
                    label="损失情况"
                    align="center"
                    width="150">
            </el-table-column>
            <el-table-column
                    prop="accident_remark"
                    label="事故备注"
                    align="center"
                    width="150">
            </el-table-column>
          <!--  <el-table-column
                    prop="create_time"
                    label="创建时间"
                    align="center"
                    width="200">
                <template slot-scope="scope">
                    <i class="el-icon-time"></i>
                    <span style="margin-left: 10px" v-text="scope.row.create_time"></span>
                </template>
            </el-table-column>-->
            <el-table-column
                    fixed="right"
                    label="操作"
                    align="center"
                    min-width="200">
                <template slot-scope="scope">
                  <el-button size="mini" type="primary" v-permission="'admin.driver.editaccident'" @click="handleEdit(scope.row)">编辑</el-button>
                  <el-button size="mini" type="danger"  v-permission="'admin.driver.delaccident'" :disabled="isHandle(scope.row)" @click="handleDelete([scope.row.id])">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <!--分页-->
        <div class="pagination-container">
            <el-pagination
                    @size-change="handleSizeChange"
                    @current-change="handleCurrentChange"
                    :current-page="query.page"
                    :page-sizes="[10, 20, 30, 40]"
                    :page-size="query.limit"
                    layout="total, sizes, prev, pager, next, jumper"
                    background
                    :total="total">
            </el-pagination>
        </div>
        <!--表单-->
        <myForm ref="myAttr" @updateRow="handleReload"/>
    </div>
</template>

<script>

import { getaccident, getaccidentInfo, delaccident } from '@/api/admin.js'
import myForm from './accidentform.vue'
import { getArrByKey } from '@/utils'

export default {
  name: 'Accident',
  components: {
    myForm
  },
  data() {
    return {
      buttonDisabled: true,
      tableData: [],
      multipleSelection: null,
      loading: true,
      searchShow: false,
      total: 0,
      query: {
          page: 1,
          limit: 10,
          keywords: '',
          id: this.$route.query.id,
          status: ''
      }
    }
  },
  created() {
    this.getaccident();
  },
  methods: {
    //查询列表
    //   getaccident() {
    //   this.loading = true
    //       getaccident(this.query).then(response => {
    //           console.log(response);
    //       if(response !== undefined){
    //           this.tableData = response.data
    //           this.total = response.total
    //       }
    //       this.loading = false
    //   })
    //       this.loading = false
    // },
    getaccident() {
      this.loading = true
      getaccident(this.query).then(response => {
        console.log(this.query);
          if(response !== undefined){
              this.tableData = response.data
              this.total = response.total
          }
          this.loading = false
      })
    },
    //搜索
    handleSearch() {
      this.query.page = 1
      this.getaccident()
    },
    //刷新重置
    handleReload() {
      this.query.page = 1
      this.query.keywords = ''
      this.query.status = ''
      this.getaccident()
    },
    //新增
    handleAdd() {
      this.$refs.myAttr.showForm()
    },
    //分页更改页数
    handleSizeChange(limit) {
      this.query.limit = limit
      this.getaccident()
    },
    //分页更改当前页
    handleCurrentChange(page) {
      this.query.page = page
      this.getaccident()
    },
    //复选框事件
    handleSelectionChange(val) {
      if (val.length > 0) {
        this.buttonDisabled = false
      } else {
        this.buttonDisabled = true
      }
      this.multipleSelection = val
    },
    //item是否可选
    isSelected(raw) {
      if (raw.username === this.$store.getters.username) {
        return false
      } else {
        return true
      }
    },
    //按钮是否可禁用
    isHandle(raw) {
      if (raw.username === this.$store.getters.username) {
        return true
      } else {

        return false
      }
    },
    //编辑
    handleEdit(raw){
      this.$refs.myAttr.getaccidentInfo(raw.id)
      this.$refs.myAttr.showForm()
    },
    //删除
    handleDelete(ids){
      this.$confirm('您确定要删除该条事故吗?', '温馨提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
          delaccident({ ids: ids }).then(response => {
          this.getaccident()
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
    //批量删除
    handleDeleteAll(){
      const ids = getArrByKey(this.multipleSelection,'id')
      this.handleDelete(ids)
    },
  },
}
</script>
