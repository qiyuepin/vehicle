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
          <el-button type="warning" size="mini"  @click="handleReload">刷新</el-button>
          <el-button type="success" v-permission="'auth.admin.adddriver'" size="mini" @click="handleAdd">新增</el-button>
          <el-button type="primary" size="mini" @click="searchShow = !searchShow">搜索</el-button>
          <el-button type="danger" v-permission="'auth.admin.delete'" :disabled="buttonDisabled" @click="handleDeleteAll" size="mini">删除</el-button>
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
                  prop="cost_status"
                  label="状态"
                  align="center"
                  width="100">
              <template slot-scope="scope">
                  <el-tag :type="scope.row.cost_status==1?'danger':'success'" v-text="scope.row.cost_status==1?'禁用':'启用'"></el-tag>
              </template>
          </el-table-column> -->
          <el-table-column
              prop="cost_status"
              label="状态"
              align="center"
              width="100">
              <template slot-scope="scope">
                <el-tag type="success"  v-if="scope.row.cost_status === '0'" >空闲</el-tag>
                <el-tag type="warning" v-else-if="scope.row.cost_status === '1'">出车</el-tag>
                <el-tag type="info" v-else >离职</el-tag>
                  <!-- <el-button 
                      v-if="scope.row.cost_status === '0'" 
                      type="success"  size="mini" plain>
                      空闲
                  </el-button> -->
                  
                  <!-- <el-button 
                      v-else-if="scope.row.cost_status === '1'" 
                      type="warning"  size="mini" plain>
                      出车
                  </el-button>
                  <el-button 
                      v-else 
                      size="mini" round>
                      离职
                  </el-button> -->
              </template>
          </el-table-column>
          <el-table-column
                  prop="name"
                  label="姓名"
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
                  prop="id_card"
                  label="身份证号"
                  align="center"
                  width="200">
          </el-table-column>
          <el-table-column
                  prop="cert_card_num"
                  label="从业资格证号"
                  align="center"
                  width="200">
          </el-table-column>

          <el-table-column
                  prop="employ_time"
                  label="入职时间"
                  align="center"
                  width="200">
              <template slot-scope="scope">
                  <i class="el-icon-time"></i>
                  <span style="margin-left: 10px" v-text="scope.row.employ_time"></span>
              </template>
          </el-table-column>
          <el-table-column
                  prop="card_front"
                  label="身份证正面"
                  align="center"
                  width="150">
              <el-image
                      style="width: 40px; height: 40px"
                      :src="scope.row.card_front"
                      :preview-src-list="[scope.row.card_front]"
                      slot-scope="scope">
              </el-image>
          </el-table-column>
          <el-table-column
                  prop="card_back"
                  label="身份证反面"
                  align="center"
                  width="150">
              <el-image
                      style="width: 40px; height: 40px"
                      :src="scope.row.card_back"
                      :preview-src-list="[scope.row.card_back]"
                      slot-scope="scope">
              </el-image>
          </el-table-column>
         
          <el-table-column
                  prop="cert_front"
                  label="从业资格证正面"
                  align="center"
                  width="150">
              <el-image
                      style="width: 40px; height: 40px"
                      :src="scope.row.cert_front"
                      :preview-src-list="[scope.row.cert_front]"
                      slot-scope="scope">
              </el-image>
          </el-table-column>
          <el-table-column
                  prop="cert_back"
                  label="从业资格证反面"
                  align="center"
                  width="150">
              <el-image
                      style="width: 40px; height: 40px"
                      :src="scope.row.cert_back"
                      :preview-src-list="[scope.row.cert_back]"
                      slot-scope="scope">
              </el-image>
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
                  <el-button size="mini" type="primary" v-permission="'admin.info.editcost'"  @click="handleEdit(scope.row)">编辑</el-button>
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

import { getcost,delcost } from '@/api/cost.js'
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
  this.getcost();
},
methods: {
  //查询列表
  getcost() {
    this.loading = true
    getcost(this.query).then(response => {
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
    this.getcost()
  },
  //刷新重置
  handleReload() {
    this.query.page = 1
    this.query.keywords = ''
    this.query.status = ''
    this.getcost()
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
    this.getcost()
  },
  //分页更改当前页
  handleCurrentChange(page) {
    this.query.page = page
    this.getcost()
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
    this.$refs.myAttr.getcostInfo(raw.id)
    this.$refs.myAttr.showForm()
  },
  //删除
  handleDelete(ids){
    this.$confirm('您确定要删除该用户吗?', '温馨提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    }).then(() => {
      delcost({ ids: ids }).then(response => {
        this.getcost()
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
