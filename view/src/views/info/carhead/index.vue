<template>
    <div class="app-container">
        <el-form v-if="searchShow" :inline="true" :model="query" class="demo-form-inline" size="small">
            <el-form-item label="关键字">
                <el-input v-model="query.keywords" placeholder="车牌号|品牌" clearable></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" icon="el-icon-search" @click="handleSearch">查询</el-button>
                <el-button type="warning" icon="el-icon-refresh-left" @click="handleReload">重置</el-button>
            </el-form-item>
        </el-form>
        <el-row style="margin-bottom: 10px;">
            <el-tooltip class="item" effect="dark" content="刷新" placement="top">
                <el-button type="warning" size="mini"  @click="handleReload">刷新</el-button>
            </el-tooltip>
            <el-tooltip class="item" effect="dark" content="新增" placement="top">
                <el-button type="success" v-permission="'auth.admin.adddriver'" size="mini" @click="handleAdd">新增</el-button>
            </el-tooltip>
            <el-tooltip class="item" effect="dark" content="搜索" placement="top">
                <el-button type="primary" size="mini" @click="searchShow = !searchShow">搜索</el-button>
            </el-tooltip>
            <el-tooltip class="item" effect="dark" content="删除" placement="top">
                <el-button type="danger" v-permission="'auth.admin.delete'" :disabled="buttonDisabled" @click="handleDeleteAll" size="mini">删除</el-button>
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
                    prop="carhead_plate"
                    label="车（牌）号"
                    align="center"
                    width="120">
                    <el-button size="mini" type="primary" @click="scope.row && handledetail(scope.row)"></el-button>
                    
            </el-table-column> -->
            <el-table-column
                prop="carhead_plate"
                label="车（牌）号"
                align="center"
                width="120">
                <template slot-scope="scope">
                    <el-button size="mini"  @click="handleDetail(scope.row)">
                        {{ scope.row.carhead_plate }}
                    </el-button>
                </template>
            </el-table-column>
            <!-- <el-table-column
                    prop="nickname"
                    label="昵称"
                    align="center"
                    width="120">
            </el-table-column> -->
            <el-table-column
                    prop="carhead_brand"
                    label="品牌"
                    align="center"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="carhead_weight"
                    label="自重"
                    align="center"
                    width="200">
            </el-table-column>
            <el-table-column
                    prop="transport_cert"
                    label="道路运输证号"
                    align="center"
                    width="200">
            </el-table-column>
            <el-table-column
                    prop="carhead_scope"
                    label="经营范围"
                    align="center"
                    width="200">
            </el-table-column>

            <el-table-column
                    prop="regist_time"
                    label="注册日期"
                    align="center"
                    width="200">
                <template slot-scope="scope">
                    <i class="el-icon-time"></i>
                    <span style="margin-left: 10px" v-text="scope.row.regist_time"></span>
                </template>
            </el-table-column>
            <el-table-column
                    prop="scrapp_time"
                    label="强制报废日期"
                    align="center"
                    width="200">
                <template slot-scope="scope">
                    <i class="el-icon-time"></i>
                    <span style="margin-left: 10px" v-text="scope.row.scrapp_time"></span>
                </template>
            </el-table-column>
            <el-table-column
                    prop="inspection_time"
                    label="检验有效期"
                    align="center"
                    width="200">
                <template slot-scope="scope">
                    <i class="el-icon-time"></i>
                    <span style="margin-left: 10px" v-text="scope.row.inspection_time"></span>
                </template>
            </el-table-column>
            <el-table-column
                    prop="validity_time"
                    label="审验有效期"
                    align="center"
                    width="200">
                <template slot-scope="scope">
                    <i class="el-icon-time"></i>
                    <span style="margin-left: 10px" v-text="scope.row.validity_time"></span>
                </template>
            </el-table-column>
            <el-table-column
                    prop="traffic_time"
                    label="交强险有效期"
                    align="center"
                    width="200">
                <template slot-scope="scope">
                    <i class="el-icon-time"></i>
                    <span style="margin-left: 10px" v-text="scope.row.traffic_time"></span>
                </template>
            </el-table-column>
            <el-table-column
                    prop="driving_license"
                    label="行驶证"
                    align="center"
                    width="150">
                <el-image
                        style="width: 40px; height: 40px"
                        :src="scope.row.driving_licenses[0].url"
                        :preview-src-list="[scope.row.driving_licenses[0].url]"
                        slot-scope="scope">
                </el-image>
                <!-- <el-image
                  v-for="(image, index) in imageList"
                  :key="index"
                  :src="image.url"
                  style="width: 40px; height: 40px; margin-right: 10px;"
                >
                </el-image> -->
                 <!-- <el-image
                    v-for="(image, index) in scope.row.driving_licenses"
                    :key="index"
                        style="width: 40px; height: 40px"
                        :src="image.url"
                        :preview-src-list="[image.url]"
                        slot-scope="scope">
                </el-image> -->
            </el-table-column>
            <el-table-column
                    prop="transport_license"
                    label="道路运输证"
                    align="center"
                    width="150">
                <el-image
                        style="width: 40px; height: 40px"
                        :src="scope.row.transport_license"
                        :preview-src-list="[scope.row.transport_license]"
                        slot-scope="scope">
                </el-image>
            </el-table-column>
            <el-table-column
                    prop="traffic_insurance"
                    label="交强险保单"
                    align="center"
                    width="150">
                <el-image
                        style="width: 40px; height: 40px"
                        :src="scope.row.traffic_insurance"
                        :preview-src-list="[scope.row.traffic_insurance]"
                        slot-scope="scope">
                </el-image>
            </el-table-column>
            <el-table-column
                    prop="business_insurance"
                    label="商业险保单"
                    align="center"
                    width="150">
                <el-image
                        style="width: 40px; height: 40px"
                        :src="scope.row.business_insurance"
                        :preview-src-list="[scope.row.business_insurance]"
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
                    <el-tooltip class="item" effect="dark" content="编辑" placement="top">
                        <el-button size="mini" type="primary" v-permission="'auth.admin.edit'"  @click="handleEdit(scope.row)">编辑</el-button>
                    </el-tooltip>
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
    </div>
</template>

<script>

import { getcarhead, delcarhead } from '@/api/Info.js'
import myForm from './form.vue'
import detail from './detail.vue'
import { getArrByKey } from '@/utils'

export default {
  name: 'Admin',
  components: {
    myForm,
    detail
  },
  data() {
    return {
      buttonDisabled: true,
      tableData: [],
      imageList: [],
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
    this.getcarhead();
  },
  methods: {
    //查询列表
    getcarhead() {
      this.loading = true
      getcarhead(this.query).then(response => {
          if(response !== undefined){
            console.log(response.data)
              this.tableData = response.data
              this.total = response.total
          }
          this.loading = false
      })
    },
    //搜索
    handleSearch() {
      this.query.page = 1
      this.getcarhead()
    },
    //刷新重置
    handleReload() {
      this.query.page = 1
      this.query.keywords = ''
      this.query.status = ''
      this.getcarhead()
    },
    handleRegulation(raw) {
      this.$router.push({ path: '/driver/regulation', query: { id: raw[0] }});
    },
    handleAccident() {
      this.$router.push('/driver/accident');
    },
 
    //新增
    handleAdd() {
      this.$refs.myAttr.showForm()
    },
    //分页更改页数
    handleSizeChange(limit) {
      this.query.limit = limit
      this.getcarhead()
    },
    //分页更改当前页
    handleCurrentChange(page) {
      this.query.page = page
      this.getcarhead()
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
    handleDetail(raw){
      console.log("handleDetail"+raw.id);
      this.$refs.myAttrdetail.getcarheadInfo(raw.id)
      this.$refs.myAttrdetail.showDetail()
    },
    //编辑
    handleEdit(raw){
      // console.log("edit"+raw.id);
      this.$refs.myAttr.getcarheadInfo(raw.id)
      this.$refs.myAttr.showForm()
    },
    //删除
    handleDelete(ids){
      this.$confirm('您确定要删除该用户吗?', '温馨提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        delcarhead({ ids: ids }).then(response => {
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
