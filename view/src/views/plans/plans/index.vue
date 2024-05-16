<template>
  <div class="app-container">
      <el-form v-if="searchShow" :inline="true" :model="query" class="demo-form-inline" size="small">
          <el-form-item label="关键字">
              <el-input v-model="query.keywords" placeholder="驾驶员|厂家名称|挂车号" clearable></el-input>
          </el-form-item>
          <el-form-item label="任务类别">
              <el-select v-model="query.plan_type" placeholder="选择类别" clearable>
                  <el-option label="运输任务" value="0"></el-option>
                  <el-option label="装货任务" value="1"></el-option>
                  <el-option label="卸货任务" value="2"></el-option>
              </el-select>
          </el-form-item>
          <el-form-item label="状态">
              <el-select v-model="query.status" placeholder="选择状态" clearable>
                  <el-option label="未完成" value="0"></el-option>
                  <el-option label="已完成" value="1"></el-option>
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
        <el-button @click="exportExcel" type="primary" size="mini">导出</el-button>
          <!-- <el-tooltip class="item" effect="dark" content="map" placement="top">
              <el-button type="success"  size="mini" @click="handlemap">map</el-button>
          </el-tooltip> -->
      </el-row>
      <el-table
              ref="multipleTable"
              :data="tableData"
              id="datatable"
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
              prop="escort_status"
              label="状态"
              align="center"
              width="110">
              <template slot-scope="scope">
                <span style="color: #909399;" v-if="scope.row.status === 0" >未完成</span>
                <span style="color: #13ce66;" v-else-if="scope.row.status === 1" >已完成</span>
                <!-- <el-button  v-if="scope.row.status === 0"  type="primary"  size="mini" plain @click="handleDetail(scope.row)">未完成</el-button>
                <el-button  v-else-if="scope.row.status === 1"  type="success"  size="mini" plain @click="handleDetail(scope.row)"> 已完成</el-button> -->
              </template>
          </el-table-column>
          
          <el-table-column
              prop="plan_type"
              label="任务类别"
              align="center"
              width="110">
              <template slot-scope="scope">
                
                <span style="color: #409EFF;" v-if="scope.row.plan_type === 0" >运输任务</span>
                <span style="color: #E6A23C;" v-else-if="scope.row.plan_type === 1" >装货任务</span>
                <span style="color: #F56C6C;" v-else-if="scope.row.plan_type === 2" >卸货任务</span>
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
          
          <!-- <el-table-column
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
          </el-table-column> -->
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
                  min-width="250">
              <template slot-scope="scope">
                  <el-button size="mini" type="danger" v-if="scope.row.status==0" v-permission="'admin.plans.distplan'"  @click="handleDist(scope.row)">分配</el-button>
                  <el-button size="mini" type="danger" v-if="scope.row.status==1" v-permission="'admin.plans.distplan'"  @click="handleDist(scope.row)" disabled>分配</el-button>
                  <!-- <el-tooltip class="item" effect="dark" content="编辑" placement="top"> -->
                  <el-button size="mini" type="primary" v-if="scope.row.status==0" v-permission="'admin.plans.editplan'"  @click="handleEdit(scope.row)">编辑</el-button>
                  <el-button size="mini" type="primary" v-if="scope.row.status==1" v-permission="'admin.plans.editplan'"  @click="handleEdit(scope.row)" disabled>编辑</el-button>
                  
                  <!-- <el-button size="mini" type="success" :disabled="isHandle(scope.row)" @click="handleStatus(scope.$index,scope.row.id,scope.row.status)">启用</el-button> -->
                  <el-button size="mini" type="info" plain v-if="scope.row.status==0" v-permission="'admin.plans.editplan'" :disabled="isHandle(scope.row)" @click="handleStatus(scope.$index,scope.row.id,scope.row.status)">未完成</el-button>
               
                  <el-button size="mini" type="success" plain v-if="scope.row.status==1" v-permission="'admin.plans.editplan'" :disabled="isHandle(scope.row)" @click="handleStatus(scope.$index,scope.row.id,scope.row.status)">已完成</el-button>
               
                  <!-- </el-tooltip> -->
                  <!-- <el-tooltip class="item" effect="dark" content="分配" placement="top">
                      <el-button size="mini" type="warning" v-permission="'admin.plans.editplan'"  @click="handleDist(scope.row)">分配</el-button>
                  </el-tooltip> -->
                  
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
      <planDist ref="myAttrDist" @updateRow="handleReload"/>
  </div>
</template>

<script>

import { getplans, delplan, getplansinfo, editplan } from '@/api/plan.js'
import myForm from './form.vue'
import detail from './detail.vue'
import planDist from './dist.vue'
import { getArrByKey } from '@/utils'
import FileSaver from "file-saver";
import XLSX from "xlsx";

export default {
name: 'plans',
components: {
  myForm,
  detail,
  planDist
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
  this.getplans();
},
methods: {
  //查询列表
  getplans() {
    this.loading = true
    getplans(this.query).then(response => {
        if(response !== undefined){
            this.tableData = response.data
            this.total = response.total
        }
        this.loading = false
    })
  },
  exportExcel () {
    var xlsxParam = { raw: true }
    var wb = XLSX.utils.table_to_book(
      document.querySelector('#datatable'),
      xlsxParam
    )
    var wbout = XLSX.write(wb, {
      bookType: 'xlsx',
      bookSST: true,
      type: 'array'
    })
    try {
      FileSaver.saveAs(
        new Blob([wbout], { type: 'application/octet-stream' }),
        '临时任务.xlsx'
      )
    } catch (e) {
      if (typeof console !== 'undefined') console.log(e, wbout)
    }
    return wbout
  },
  //搜索
  handleSearch() {
    this.query.page = 1
    this.getplans()
  },
  //刷新重置
  handleReload() {
    this.query.page = 1
    this.query.keywords = ''
    this.query.status = ''
    this.query.plan_type = ''
    this.getplans()
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
    this.getplans()
  },
  //分页更改当前页
  handleCurrentChange(page) {
    this.query.page = page
    this.getplans()
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
    this.$refs.myAttr.getplansinfo(raw.id)
    this.$refs.myAttr.showForm()
  },
  handleDist(raw){
    // console.log("edit"+raw.id);
    this.$refs.myAttrDist.getplansinfo(raw.id)
    this.$refs.myAttrDist.showForm()
  },
  handleDetail(raw){
    this.$refs.myAttrdetail.getplansinfo(raw.id)
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
        this.getplans()
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
    let handlerMsg = status === 1 ? '完成' : '未完成';
    this.$confirm('您确定改任务已完成吗?', '温馨提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    }).then(() => {
      editplan({ id: id, status: 1 - status }).then(response => {
        this.tableData[index]['status'] = 1 - status
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
