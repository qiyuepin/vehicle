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
            <el-tooltip class="item" effect="dark" content="刷新" placement="top">
                <el-button type="warning" icon="el-icon-refresh" circle @click="handleReload"></el-button>
            </el-tooltip>
            <el-tooltip class="item" effect="dark" content="新增" placement="top">
                <el-button type="success" v-permission="'auth.admin.adddriver'" icon="el-icon-plus" circle @click="handleAdd"></el-button>
            </el-tooltip>
            <el-tooltip class="item" effect="dark" content="违章信息" placement="top">
                <el-button type="danger" v-permission="'admin.driver.regulation'" icon="el-icon-warning-outline" circle @click="handleRegulation"></el-button>
            </el-tooltip>
            <el-tooltip class="item" effect="dark" content="事故信息" placement="top">
                <el-button type="danger" v-permission="'admin.driver.test'" icon="el-icon-warning" circle @click="handleAccident"></el-button>
            </el-tooltip>
            <el-tooltip class="item" effect="dark" content="test" placement="top">
                <el-button type="danger" v-permission="'admin.driver.test'" icon="el-icon-message" circle @click="handletest"></el-button>
            </el-tooltip>
            <el-tooltip class="item" effect="dark" content="搜索" placement="top">
                <el-button type="primary" icon="el-icon-search" circle @click="searchShow = !searchShow"></el-button>
            </el-tooltip>
            <el-tooltip class="item" effect="dark" content="删除" placement="top">
                <el-button type="danger" v-permission="'auth.admin.delete'" :disabled="buttonDisabled" @click="handleDeleteAll" icon="el-icon-delete" circle></el-button>
            </el-tooltip>
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
                    label="cert_card_num"
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
                        style="width: 100px; height: 100px"
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
                        style="width: 100px; height: 100px"
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
                        style="width: 100px; height: 100px"
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
                        style="width: 100px; height: 100px"
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
                        style="width: 100px; height: 100px"
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
                        style="width: 100px; height: 100px"
                        :src="scope.row.cert_back"
                        :preview-src-list="[scope.row.cert_back]"
                        slot-scope="scope">
                </el-image>
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
                    <el-tooltip class="item" effect="dark" content="编辑" placement="top">
                        <el-button size="mini" type="primary" v-permission="'auth.admin.edit'" icon="el-icon-edit-outline" circle
                                   @click="handleEdit(scope.row)"></el-button>
                    </el-tooltip>
                    <el-tooltip v-if="scope.row.status==1" class="item" effect="dark" content="启用" placement="top">
                        <el-button size="mini" type="success" v-permission="'auth.admin.change'" :disabled="isHandle(scope.row)" icon="el-icon-circle-check" circle
                                   @click="handleStatus(scope.$index,scope.row.id,scope.row.status)"></el-button>
                    </el-tooltip>
                    <el-tooltip v-if="scope.row.status==2" class="item" effect="dark" content="禁用" placement="top">
                        <el-button size="mini" type="warning" v-permission="'auth.admin.change'" :disabled="isHandle(scope.row)" icon="el-icon-circle-close" circle
                                   @click="handleStatus(scope.$index,scope.row.id,scope.row.status)"></el-button>
                    </el-tooltip>
                    <el-tooltip class="item" effect="dark" content="删除" placement="top">
                        <el-button size="mini" type="danger"  v-permission="'auth.admin.delete'" :disabled="isHandle(scope.row)" icon="el-icon-delete"
                                   circle @click="handleDelete([scope.row.id])"></el-button>
                    </el-tooltip>
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
    handleRegulation() {
      this.$router.push('/driver/regulation')
      // this.$router.push(`driver/regulation`)
      // this.$router.push({ 
      //   path: this.redirect || '/', 
      //   query: { 
      //     authority: 'driver', 
      //     page: 'regulation' 
      //   }
      // })
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
