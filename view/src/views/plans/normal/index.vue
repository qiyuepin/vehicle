<template>
  <div class="app-container">
      <el-form v-if="searchShow" :inline="true" :model="query" class="demo-form-inline" size="small">
          <el-form-item label="关键字">
              <el-input v-model="query.keywords" placeholder="驾驶员|厂家名称|挂车号" clearable></el-input>
          </el-form-item>
          <el-form-item label="状态">
              <el-select v-model="query.status" placeholder="选择状态" clearable>
                  <el-option label="待接单" value=null></el-option>
                  <el-option label="回库" value='0'/>
                  <el-option label="在途" value="1"></el-option>
                  <el-option label="装货" value="2"></el-option>
                  <el-option label="装货完成" value="3"></el-option>
                  <el-option label="卸货" value="4"></el-option>
                  <el-option label="卸货完成" value="5"></el-option>
              </el-select>
          </el-form-item>
          <el-form-item>
              <el-button type="primary" icon="el-icon-search" @click="handleSearch">查询</el-button>
              <el-button type="warning" icon="el-icon-refresh-left" @click="handleReload">重置</el-button>
          </el-form-item>
      </el-form>
      <el-row style="margin-bottom: 10px;">
          <el-button type="warning" size="mini"  @click="handleReload">刷新</el-button>
          <el-button type="success" v-permission="'auth.admin.adddriver'" size="mini" @click="handleAdd">新增</el-button>
          <el-button type="primary" size="mini" @click="searchShow = !searchShow">搜索</el-button>
          <el-button type="danger" v-permission="'auth.admin.delete'" :disabled="buttonDisabled" @click="handleDeleteAll" size="mini">删除</el-button>
          <!-- <el-tooltip class="item" effect="dark" content="map" placement="top">
              <el-button type="success"  size="mini" @click="handlemap">map</el-button>
          </el-tooltip> -->
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
                  prop="escort_status"
                  label="状态"
                  align="center"
                  width="100">
              <template slot-scope="scope">
                  <el-tag :type="scope.row.escort_status==1?'danger':'success'" v-text="scope.row.escort_status==1?'禁用':'启用'"></el-tag>
              </template>
          </el-table-column> -->
          <el-table-column
              prop="escort_status"
              label="状态"
              align="center"
              width="110">
              <template slot-scope="scope">
                <!-- <el-tag type="success"  v-if="scope.row.status === 0" >回库</el-tag>
                <el-tag type="warning" v-else-if="scope.row.status === 1">已分配</el-tag>
                <el-tag type="info" v-else-if="scope.row.status === 2">装货</el-tag>
                <el-tag type="info" v-else-if="scope.row.status === 3">在途</el-tag>
                <el-tag type="info" v-else-if="scope.row.status === 4">卸货</el-tag>
                <el-tag type="info" v-else-if="scope.row.status === 5">在途</el-tag>
                <el-tag type="info" v-else >空闲</el-tag> -->
                <!-- <el-button  v-if="scope.row.status === 0"  type="success"  size="mini" plain @click="handleDetail(scope.row)">回库</el-button>
                <el-button  v-else-if="scope.row.status === 1"  type="primary"  size="mini" plain @click="handleDetail(scope.row)"> 在途</el-button>
                <el-button  v-else-if="scope.row.status === 2"  type="primary"  size="mini" plain @click="handleDetail(scope.row)"> 装货 </el-button>
                <el-button  v-else-if="scope.row.status === 3"  type="primary"  size="mini" plain @click="handleDetail(scope.row)"> 装货完成 </el-button>
                <el-button  v-else-if="scope.row.status === 4"  type="primary"  size="mini" plain @click="handleDetail(scope.row)">卸货</el-button>
                <el-button  v-else-if="scope.row.status === 5"  type="primary"  size="mini" plain @click="handleDetail(scope.row)"> 卸货完成</el-button>
                <span style="color: #F56C6C;"  @click="handleDetail(scope.row)" v-else >待接单</span> -->
                <el-button  v-if="scope.row.driver_status === 2"  type="success"  size="mini" plain @click="handleDetail(scope.row)">已完成</el-button>
                <el-button  v-else-if="scope.row.status === 0"  type="success"  size="mini" plain @click="handleDetail(scope.row)">回库</el-button>
                <el-button  v-else-if="scope.row.status === 1"  type="primary"  size="mini" plain @click="handleDetail(scope.row)"> 在途</el-button>
                <el-button  v-else-if="scope.row.status === 2"  type="primary"  size="mini" plain @click="handleDetail(scope.row)"> 装货 </el-button>
                <el-button  v-else-if="scope.row.status === 3"  type="primary"  size="mini" plain @click="handleDetail(scope.row)"> 装货完成 </el-button>
                <el-button  v-else-if="scope.row.status === 4"  type="primary"  size="mini" plain @click="handleDetail(scope.row)">卸货</el-button>
                <el-button  v-else-if="scope.row.status === 5"  type="primary"  size="mini" plain @click="handleDetail(scope.row)"> 卸货完成</el-button>
                <el-button  v-else size="mini" @click="handleDetail(scope.row)">待确认</el-button>
              </template>
          </el-table-column>
          <el-table-column
              prop="start_periodic"
              label="新周期"
              align="center"
              width="110">
              <template slot-scope="scope">
             
                <i class="el-icon-share" v-if="scope.row.plan_type !== 0" style="display: none;"></i>
                <i class="el-icon-success" v-else-if="scope.row.start_periodic === 1" style="color: #42d885;font-size: 20px;" ></i>
                <i class="el-icon-remove" v-else-if="scope.row.start_periodic === 0" style="color: #ffc833;font-size: 20px;" ></i>
    
              </template>
          </el-table-column>
          <el-table-column
                  prop="head_num"
                  label="车头"
                  align="center"
                  width="120">
          </el-table-column>
          <el-table-column
                  prop="trailer_num"
                  label="挂车"
                  align="center"
                  width="120">
          </el-table-column>
          <el-table-column
                  prop="driver_name"
                  label="驾驶员"
                  align="center"
                  width="150">
          </el-table-column>
          <el-table-column
                  prop="escort_name"
                  label="押运员"
                  align="center"
                  width="150">
          </el-table-column>
          <el-table-column
                  prop="product_name"
                  label="货品名称"
                  align="center"
                  width="200">
          </el-table-column>
          <el-table-column
                  prop="product_quantity"
                  label="货品数量"
                  align="center"
                  width="150">
          </el-table-column>
          <el-table-column
                  prop="load_factory"
                  label="装货厂家"
                  align="center"
                  width="200"
                  show-overflow-tooltip>
          </el-table-column>
          <el-table-column
                  prop="load_address"
                  label="装货厂家地址"
                  align="center"
                  width="200"
                  show-overflow-tooltip>
          </el-table-column>
          <el-table-column
                  prop="unload_factory"
                  label="卸货厂家"
                  align="center"
                  width="200"
                  show-overflow-tooltip>
          </el-table-column>
          <el-table-column
                  prop="unload_address"
                  label="卸货厂家地址"
                  align="center"
                  width="200"
                  show-overflow-tooltip>
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
                  min-width="150">
              <template slot-scope="scope">
                  <el-button size="mini" type="primary" v-permission="'admin.info.editescort'"  @click="handleEdit(scope.row)">编辑</el-button>
                
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
      <detail ref="myAttrdetail" @updateRow="handleReload"/>
      <test ref="myAttrtest" @updateRow="handleReload"/>
  </div>
</template>

<script>

import { getnormal, delnormal, getnormalinfo } from '@/api/plan.js'
import myForm from './form.vue'
import detail from './detail.vue'
import test from './test.vue'
import { getArrByKey } from '@/utils'

export default {
name: 'Admin',
components: {
  myForm,
  detail,
  test
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
  this.getnormal();
},
methods: {
  //查询列表
  getnormal() {
    this.loading = true
    getnormal(this.query).then(response => {
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
    this.getnormal()
  },
  //刷新重置
  handleReload() {
    this.query.page = 1
    this.query.keywords = ''
    this.query.status = ''
    this.getnormal()
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
  handlemap() {
    this.$refs.myAttrtest.showForm()
    this.$refs.myAttrtest.initAMap()
  },
  //分页更改页数
  handleSizeChange(limit) {
    this.query.limit = limit
    this.getnormal()
  },
  //分页更改当前页
  handleCurrentChange(page) {
    this.query.page = page
    this.getnormal()
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
    this.$refs.myAttr.getnormalinfo(raw.id)
    this.$refs.myAttr.showForm()
  },
  handleDetail(raw){
    this.$refs.myAttrdetail.getnormalinfo(raw.id)
    this.$refs.myAttrdetail.showDetail()
  },
  //删除
  handleDelete(ids){
    this.$confirm('您确定要删除该用户吗?', '温馨提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    }).then(() => {
      delescort({ ids: ids }).then(response => {
        this.getnormal()
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
