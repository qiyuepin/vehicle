<template>
    <div class="app-container">
        <el-form v-if="searchShow" :inline="true" :model="query" class="demo-form-inline" size="small">
            <el-form-item label="关键字">
                <el-input v-model="query.keywords" placeholder="用户名|昵称|手机号|邮箱" clearable></el-input>
            </el-form-item>
            <el-form-item label="状态">
                <el-select v-model="query.status" placeholder="选择状态" clearable>
                    <el-option label="全部" value="0"/>
                    <el-option label="启用" value="2"></el-option>
                    <el-option label="禁用" value="1"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" icon="el-icon-search" @click="handleSearch">查询</el-button>
                <el-button type="warning" icon="el-icon-refresh-left" @click="handleReload">重置</el-button>
            </el-form-item>
        </el-form>
        <el-row style="margin-bottom: 10px;">
           
                <el-button type="warning"  @click="handleReload" size="mini">刷新</el-button>
                <el-button type="success" v-permission="'auth.admin.add'" size="mini"  @click="handleAdd">新增</el-button>

                <el-button type="primary"  @click="searchShow = !searchShow" size="mini">搜索</el-button>

                <el-button type="danger" v-permission="'auth.admin.delete'" :disabled="buttonDisabled" @click="handleDeleteAll"  size="mini">删除</el-button>
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
                    width="55"
                    :selectable="isSelected">
            </el-table-column>
            <el-table-column
                    prop="id"
                    label="ID"
                    align="center"
                    width="80">
            </el-table-column>
            <el-table-column
                    prop="avatar"
                    label="头像"
                    align="center"
                    width="80">
                <el-avatar :size="40" :src="scope.row.avatar" shape="square" slot-scope="scope">
                </el-avatar>
            </el-table-column>
            <el-table-column
                    prop="username"
                    label="用户名"
                    align="center"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="nickname"
                    label="昵称"
                    align="center"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="phone"
                    label="手机号"
                    align="center"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="email"
                    label="邮箱"
                    align="center"
                    width="200">
            </el-table-column>
            <el-table-column
                    prop="status"
                    label="状态"
                    align="center"
                    width="100">
                <template slot-scope="scope">
                    <el-tag :type="scope.row.status==1?'danger':'success'" v-text="scope.row.status==1?'禁用':'启用'"></el-tag>
                </template>
            </el-table-column>
            <el-table-column
                    prop="login_ip"
                    label="登录IP"
                    align="center"
                    width="150">
            </el-table-column>
            <el-table-column
                    prop="login_time"
                    label="登录时间"
                    align="center"
                    width="200">
                <template slot-scope="scope" v-if="scope.row.login_time">
                    <i class="el-icon-time"></i>
                    <span style="margin-left: 10px" v-text="scope.row.login_time"></span>
                </template>
            </el-table-column>
            <el-table-column
                    prop="create_time"
                    label="创建时间"
                    align="center"
                    width="200">
                <template slot-scope="scope">
                    <i class="el-icon-time"></i>
                    <span style="margin-left: 10px" v-text="scope.row.create_time"></span>
                </template>
            </el-table-column>
            <el-table-column
                    fixed="right"
                    label="操作"
                    align="center"
                    min-width="200">
                <template slot-scope="scope">
                  <el-button size="mini" type="primary" v-permission="'auth.admin.edit'"  @click="handleEdit(scope.row)">编辑</el-button>
                  <el-button v-if="scope.row.status==1" size="mini" type="success" v-permission="'auth.admin.change'" :disabled="isHandle(scope.row)"
                                   @click="handleStatus(scope.$index,scope.row.id,scope.row.status)">启用</el-button>
                  <el-button  v-if="scope.row.status==2" size="mini" type="warning" v-permission="'auth.admin.change'" :disabled="isHandle(scope.row)" 
                                   @click="handleStatus(scope.$index,scope.row.id,scope.row.status)">禁用</el-button>
                  <el-button size="mini" type="danger"  v-permission="'auth.admin.delete'" :disabled="isHandle(scope.row)"  @click="handleDelete([scope.row.id])">删除</el-button>
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

import { getList, changeStatus,deleteAdmin } from '@/api/admin.js'
import myForm from './form.vue'
import { getArrByKey } from '@/utils'

export default {
  name: 'Admin',
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
        status: ''
      }
    }
  },
  created() {
    this.getList();
  },
  methods: {
    //查询列表
    getList() {
      this.loading = true
      getList(this.query).then(response => {
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
      this.getList()
    },
    //刷新重置
    handleReload() {
      this.query.page = 1
      this.query.keywords = ''
      this.query.status = ''
      this.getList()
    },
    //新增
    handleAdd() {
      this.$refs.myAttr.showForm()
    },
    //分页更改页数
    handleSizeChange(limit) {
      this.query.limit = limit
      this.getList()
    },
    //分页更改当前页
    handleCurrentChange(page) {
      this.query.page = page
      this.getList()
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
      this.$refs.myAttr.getInfo(raw.id)
      this.$refs.myAttr.showForm()
    },
    //删除
    handleDelete(ids){
      this.$confirm('您确定要删除该用户吗?', '温馨提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        deleteAdmin({ ids: ids }).then(response => {
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
    //批量删除
    handleDeleteAll(){
      const ids = getArrByKey(this.multipleSelection,'id')
      this.handleDelete(ids)
    },
    //启用禁用操作
    handleStatus(index, id, status) {
      let handlerMsg = status === 1 ? '启用' : '禁用';
      this.$confirm('您确定要' + handlerMsg + '该用户吗?', '温馨提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        changeStatus({ id: id, status: 3 - status }).then(response => {
          this.tableData[index]['status'] = 3 - status
          this.$message({
            type: 'success',
            message: handlerMsg + '成功!'
          });
        })
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '已取消操作'
        });
      })
    }
  },
}
</script>
