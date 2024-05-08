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
            <el-table-column
                prop="driver_status"
                label="状态"
                align="center"
                width="110">
                <template slot-scope="scope">
                  <!-- <span style="color: #67C23A;" v-if="scope.row.driver_status === 0" >空闲</span> -->
                  <el-tag type="success" v-if="scope.row.driver_status === 0">空闲</el-tag>
                  <el-tag type="danger" v-else-if="scope.row.driver_status === 1">出车</el-tag>
                  <el-tag type="info" v-else-if="scope.row.driver_status === 2">离职</el-tag>
                  <!-- <span style="color: #F56C6C;" v-else-if="scope.row.driver_status === 1" >出车</span>
                  <span style="color: #909399;" v-else-if="scope.row.driver_status === 2" >离职</span> -->
                  <!-- <span style="color: #13ce66;" v-else-if="scope.row.plan_type === 2" >卸货任务</span> -->
                </template>
            </el-table-column>
            <el-table-column
                    prop="username"
                    label="用户名"
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
                    prop="phone"
                    label="手机号"
                    align="center"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="id_card_num"
                    label="身份证号"
                    align="center"
                    width="200">
            </el-table-column>
            <el-table-column
                    prop="dirver_card_num"
                    label="驾驶证号"
                    align="center"
                    width="200">
            </el-table-column>
            <el-table-column
                    prop="id_card_num"
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
            <!-- <el-table-column
                    prop="card_front"
                    label="身份证正面"
                    align="center"
                    width="100">
                <el-avatar :size="40" :src="scope.row.avatar" shape="square" slot-scope="scope">
                </el-avatar>
            </el-table-column> -->
            <el-table-column
                    prop="card_front"
                    label="身份证正面"
                    align="center"
                    width="150">
                <el-image
                        style="width: 40px; height: 30px"
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
                        style="width: 40px; height: 30px"
                        :src="scope.row.card_back"
                        :preview-src-list="[scope.row.card_back]"
                        slot-scope="scope">
                </el-image>
            </el-table-column>
            <el-table-column
                    prop="driver_card_front"
                    label="驾驶证正面"
                    align="center"
                    width="150">
                <el-image
                        style="width: 40px; height: 30px"
                        :src="scope.row.driver_card_front"
                        :preview-src-list="[scope.row.driver_card_front]"
                        slot-scope="scope">
                </el-image>
            </el-table-column>
            <el-table-column
                    prop="driver_card_back"
                    label="驾驶证反面"
                    align="center"
                    width="150">
                <el-image
                        style="width: 40px; height: 30px"
                        :src="scope.row.driver_card_back"
                        :preview-src-list="[scope.row.driver_card_back]"
                        slot-scope="scope">
                </el-image>
            </el-table-column>
            <el-table-column
                    prop="cert_front"
                    label="从业资格证正面"
                    align="center"
                    width="150">
                <el-image
                        style="width: 40px; height: 30px"
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
                        style="width: 40px; height: 30px"
                        :src="scope.row.cert_back"
                        :preview-src-list="[scope.row.cert_back]"
                        slot-scope="scope">
                </el-image>
            </el-table-column>
            
            <el-table-column
                    prop="status"
                    label="违章信息"
                    align="center"
                    width="150">
                <template slot-scope="scope">
                    <el-button size="mini"  @click="handleRegulation([scope.row.id])">点击查看违章信息</el-button>
                </template>
            </el-table-column>
            <el-table-column
                    prop="status"
                    label="事故信息"
                    align="center"
                    width="150">
                <template slot-scope="scope">
                    <el-button size="mini"  @click="handleAccident([scope.row.id])">点击查看事故信息</el-button>
                </template>
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
            <!-- <el-table-column
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
            </el-table-column> -->
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
                  <el-button size="mini" type="primary" v-permission="'auth.admin.edit'"  @click="handleEdit(scope.row)">编辑</el-button>
                  <el-button size="mini" v-if="scope.row.status==1" type="success" v-permission="'auth.admin.change'" :disabled="isHandle(scope.row)" @click="handleStatus(scope.$index,scope.row.id,scope.row.status)">启用</el-button>
                  <el-button size="mini" v-if="scope.row.status==2" type="warning" v-permission="'auth.admin.change'" :disabled="isHandle(scope.row)" @click="handleStatus(scope.$index,scope.row.id,scope.row.status)">禁用</el-button>
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

import { getdriverList, changeStatus,deleteAdmin } from '@/api/admin.js'
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
    this.getdriverList();
  },
  methods: {
    //查询列表
    getdriverList() {
      this.loading = true
      getdriverList(this.query).then(response => {
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
      this.getdriverList()
    },
    //刷新重置
    handleReload() {
      this.query.page = 1
      this.query.keywords = ''
      this.query.status = ''
      this.getdriverList()
    },
    handleRegulation(raw) {
      this.$router.push({ path: '/driver/regulation', query: { id: raw[0] }});
    },
    handleAccident(raw) {
      this.$router.push({ path: '/driver/accident', query: { id: raw[0] }});
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
      this.getdriverList()
    },
    //分页更改当前页
    handleCurrentChange(page) {
      this.query.page = page
      this.getdriverList()
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
      this.$refs.myAttr.getdriverInfo(raw.id)
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
          this.getdriverList()
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
    handleStatus(index, id, status, driver_status) {
      let handlerMsg = status === 1 ? '启用' : '禁用';
      this.$confirm('您确定要' + handlerMsg + '该用户吗?', '温馨提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        changeStatus({ id: id, status: 3 - status, driver_status }).then(response => {
          this.tableData[index]['status'] = 3 - status
          this.tableData[index]['driver_status'] = 2
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
