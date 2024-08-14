<template>
  <div class="app-container">
      <el-form v-if="searchShow" :inline="true" :model="query" class="demo-form-inline" size="small">
          <el-form-item label="关键字">
              <el-input v-model="query.keywords" placeholder="车牌号|驾驶员" clearable></el-input>
          </el-form-item>
          <el-form-item label="状态">
              <el-select v-model="query.status" placeholder="选择牵引车状态" clearable>
                  <el-option label="全部" value=""/>
                  <el-option label="回库" value="0"></el-option>
                  <el-option label="装货" value="1"></el-option>
                  <el-option label="卸货" value="2"></el-option>
                  <el-option label="在途" value="3"></el-option>
                  <el-option label="停运" value="4"></el-option>
              </el-select>
          </el-form-item>
          <el-form-item>
              <el-button type="primary" icon="el-icon-search" @click="handleSearch">查询</el-button>
              <el-button type="warning" icon="el-icon-refresh-left" @click="handleReload">重置</el-button>
          </el-form-item>
      </el-form>
      <el-row style="margin-bottom: 10px;">
        <el-button type="warning" size="mini"  @click="handleReload">刷新</el-button>
        <!-- <el-button type="success" v-permission="'auth.admin.adddriver'" size="mini" @click="handleAdd">新增</el-button> -->
        <el-button type="primary" size="mini" @click="searchShow = !searchShow">搜索</el-button>
        <!-- <el-button type="danger" v-permission="'auth.admin.delete'" :disabled="buttonDisabled" @click="handleDeleteAll" size="mini">删除</el-button> -->
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
          <!-- <el-table-column
                label="id"
                  type="index"
                  align="center"
                  width="80">
          </el-table-column> -->
          <!-- <el-table-column
                  prop="escort_status"
                  label="状态"
                  align="center"
                  width="100">
              <template slot-scope="scope">
                  <el-tag :type="scope.row.escort_status==1?'danger':'success'" v-text="scope.row.escort_status==1?'禁用':'启用'"></el-tag>
              </template>
          </el-table-column> -->
          <!-- <el-table-column
              prop="head_status"
              label="状态"
              align="center"
              width="100">
              <template slot-scope="scope">
                <el-tag type="success"  v-if="scope.row.head_status === 0" >回库</el-tag>
                <el-tag type="warning" v-else-if="scope.row.head_status === 1">装货</el-tag>
                <el-tag type="warning" v-else-if="scope.row.head_status === 2">卸货</el-tag>
                <el-tag v-else-if="scope.row.head_status === 3">在途</el-tag>
                <el-tag type="info" v-else >停运</el-tag>

              </template>
          </el-table-column> -->
          <el-table-column
                  prop="head_num"
                  label="牵引车车牌号"
                  align="center"
                  width="200">
          </el-table-column>
          <!-- <el-table-column
                  prop="head_status"
                  label="牵引车状态"
                  align="center"
                  width="200">
          </el-table-column> -->
          <el-table-column
              prop="head_status"
              label="牵引车状态"
              align="center"
              width="110">
              <template slot-scope="scope">
                
                <span style="color: #67C23A;" v-if="scope.row.head_status === 0" >回库</span>
                <span style="color: #e6a23c;" v-else-if="scope.row.head_status === 1" >装货</span>
                <span style="color: #f56c6c;" v-else-if="scope.row.head_status === 2" >卸货</span>
                <span style="color: #409EFF;" v-else-if="scope.row.head_status === 3" >在途</span>
                <span style="color: #909399;" v-else-if="scope.row.head_status === 4" >停运</span>
                <!-- <span style="color: #13ce66;" v-else-if="scope.row.plan_type === 2" >卸货任务</span> -->
              </template>
          </el-table-column>
          <el-table-column
                  prop="trailer_num"
                  label="挂车车牌号"
                  align="center"
                  width="200">
          </el-table-column>
          <el-table-column
              prop="trailer_status"
              label="挂车状态"
              align="center"
              width="110">
              <template slot-scope="scope">
                
                <span style="color: #67C23A;" v-if="scope.row.trailer_status === 0" >空车</span>
                <span style="color: #e6a23c;" v-else-if="scope.row.trailer_status === 1" >重车</span>
                <!-- <span style="color: #13ce66;" v-else-if="scope.row.plan_type === 2" >卸货任务</span> -->
              </template>
          </el-table-column>
 
          <el-table-column
                  prop="driver_name"
                  label="驾驶员"
                  align="center"
                  width="200">
          </el-table-column>
          <el-table-column
                  prop="escort_name"
                  label="押运员"
                  align="center"
                  width="200">
          </el-table-column>
 
          
          
          
          
          <el-table-column
                  prop="update_time"
                  label="修改时间"
                  align="center"
                  width="200">
              <template slot-scope="scope">
                  <i class="el-icon-time"></i>
                  <span style="margin-left: 10px" v-text="scope.row.update_time"></span>
              </template>
          </el-table-column>
          <el-table-column
                  v-if="hasPermission('admin.info.addinfo')"
                  fixed="right"
                  label="操作"
                  align="center"
                  min-width="150">
              <template slot-scope="scope">
             
                  <el-button size="mini" type="primary" v-permission="'admin.info.editinfo'"  @click="handleEdit(scope.row)">编辑</el-button>
                 
                  <!-- <el-tooltip v-if="scope.row.status==1" class="item" effect="dark" content="启用" placement="top">
                      <el-button size="mini" type="success" v-permission="'auth.admin.change'" :disabled="isHandle(scope.row)" @click="handleStatus(scope.$index,scope.row.id,scope.row.status)">启用</el-button>
                  </el-tooltip>
                  <el-tooltip v-if="scope.row.status==2" class="item" effect="dark" content="禁用" placement="top">
                      <el-button size="mini" type="warning" v-permission="'auth.admin.change'" :disabled="isHandle(scope.row)" @click="handleStatus(scope.$index,scope.row.id,scope.row.status)">禁用</el-button>
                  </el-tooltip> -->
                  <!-- <el-tooltip class="item" effect="dark" content="删除" placement="top">
                      <el-button size="mini" type="danger"  v-permission="'auth.admin.delete'" :disabled="isHandle(scope.row)" icon="el-icon-delete"
                                 circle @click="handleDelete([scope.row.id])"></el-button>
                  </el-tooltip> -->
                  <!-- <el-tooltip class="item" effect="dark" content="违章信息" placement="top">
                      <el-button size="mini" type="danger" v-permission="'admin.driver.regulation'"  icon="el-icon-warning-outline" circle @click="handleRegulation([scope.row.id])"></el-button>
                  </el-tooltip>
                  <el-tooltip class="item" effect="dark" content="事故信息" placement="top">
                      <el-button size="mini" type="danger" v-permission="'admin.driver.accident'" icon="el-icon-warning" circle @click="handleAccident([scope.row.id])"></el-button>
                  </el-tooltip> -->
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

import { getinfolist, getinfo,delinfo } from '@/api/Info.js'
import checkPermission from '@/utils/checkpermission.js'
import myForm from './form.vue'
import { getArrByKey } from '@/utils'

export default {
name: 'info',
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
  this.getinfolist();
},
methods: {
  hasPermission(permission) {
    return checkPermission(permission);
  },
  //查询列表
  getinfolist() {
    this.loading = true
    getinfolist(this.query).then(response => {
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
    this.getinfolist()
  },
  //刷新重置
  handleReload() {
    this.query.page = 1
    this.query.keywords = ''
    this.query.status = ''
    this.getinfolist()
  },
  handleRegulation(raw) {
    this.$router.push({ path: '/driver/regulation', query: { id: raw[0] }});
  },
  handleAccident() {
    this.$router.push('/driver/accident');
  },
  handletest() {
    this.$router.push('/driver/test');
  },
  //新增
  handleAdd() {
    this.$refs.myAttr.showForm()
  },
  //分页更改页数
  handleSizeChange(limit) {
    this.query.limit = limit
    this.getinfolist()
  },
  //分页更改当前页
  handleCurrentChange(page) {
    this.query.page = page
    this.getinfolist()
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
    console.log("edit"+raw.id);
    this.$refs.myAttr.getinfo(raw.id)
    this.$refs.myAttr.showForm()
  },
  //删除
  handleDelete(ids){
    this.$confirm('您确定要删除该用户吗?', '温馨提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    }).then(() => {
      delinfo({ ids: ids }).then(response => {
        this.getinfolist()
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
