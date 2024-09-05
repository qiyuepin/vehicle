<template>
  <div class="app-container">
      <el-form v-if="searchShow" :inline="true" :model="query" class="demo-form-inline" size="small">
          <el-form-item label="关键字">
              <el-input v-model="query.keywords" placeholder="驾驶员|货品名称|厂家名称|挂车号|车头" clearable></el-input>
          </el-form-item>
          <el-form-item label="状态">
              <el-select v-model="query.status" placeholder="选择状态" clearable>
                  <el-option label="待接单" value=null></el-option>
                  <el-option label="进行中" value="6"></el-option>
                  <!-- <el-option label="回库" value='0'/>
                  <el-option label="在途" value="1"></el-option> -->
                  <el-option label="装货" value="2"></el-option>
                  <el-option label="装货完成" value="3"></el-option>
                  <el-option label="卸货" value="4"></el-option>
                  <el-option label="卸货完成" value="5"></el-option>
                  <el-option label="已作废" value="7"></el-option>
                  <el-option label="已完成" value="10"></el-option>
                  <el-option label="异常" value="4"></el-option>
              </el-select>
          </el-form-item>
          <el-date-picker
            v-model="query.date"
            type="daterange"
            range-separator="至"
            start-placeholder="开始日期"
            end-placeholder="结束日期"
            :default-time="['00:00:00', '23:59:59']"
            @change="handleDateChange">
          </el-date-picker>
          <el-form-item>
              <el-button type="primary" icon="el-icon-search" @click="handleSearch">查询</el-button>
              <el-button type="warning" icon="el-icon-refresh-left" @click="handleReload">重置</el-button>
          </el-form-item>
      </el-form>
      <el-row style="margin-bottom: 10px;">
          <el-button type="warning" size="mini"  @click="handleReload">刷新</el-button>
          <el-button type="success" v-permission="'admin.plans.addtemporary'" size="mini" @click="handleAdd">新增</el-button>
          <el-button type="primary" size="mini" @click="searchShow = !searchShow">搜索</el-button>
          <el-button type="danger" v-permission="'admin.plans.deltemporary'" :disabled="buttonDisabled" @click="handleDeleteAll" size="mini">删除</el-button>
          <el-button @click="exporttemporaryExcel" type="primary" size="mini">导出</el-button>
          <!-- <el-button type="success"  size="mini" @click="handlemap">map</el-button> -->
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
          <!-- <el-table-column
                  prop="id"
                  label="ID"
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
                <el-button  v-if="scope.row.driver_status === 3 || scope.row.status === 9"  type="info"  size="mini" plain @click="handleDetail(scope.row)">已作废</el-button>
                <el-button  v-else-if="scope.row.driver_status === 2"  type="success"  size="mini" plain @click="handleDetail(scope.row)">已完成</el-button>
                <el-button  v-else-if="scope.row.driver_status === 4"  type="info"  size="mini" plain @click="handleDetail(scope.row)">异常</el-button>
                <el-button  v-else-if="scope.row.driver_status === 1 && scope.row.status === null"  type="primary"  size="mini" plain @click="handleDetail(scope.row)">进行中</el-button>

                <el-button  v-else-if="scope.row.status === 2"  type="primary"  size="mini" plain @click="handleDetail(scope.row)"> 装货 </el-button>
                <el-button  v-else-if="scope.row.status === 3"  type="primary"  size="mini" plain @click="handleDetail(scope.row)"> 装货完成 </el-button>
                <el-button  v-else-if="scope.row.status === 4"  type="primary"  size="mini" plain @click="handleDetail(scope.row)">卸货</el-button>
                <el-button  v-else-if="scope.row.status === 5"  type="primary"  size="mini" plain @click="handleDetail(scope.row)"> 卸货完成</el-button>
                <el-button  v-else-if="scope.row.status === 8"  type="info"  size="mini" plain @click="handleDetail(scope.row)"> 异常</el-button>
                <el-button  v-else-if="scope.row.status === 9"  type="info"  size="mini" plain @click="handleDetail(scope.row)"> 作废</el-button>
                <el-button  v-else size="mini" @click="handleDetail(scope.row)">待接单</el-button>
                <!-- <span style="color: #67C23A;" v-if="scope.row.status === 0" >回库</span>
                <span style="color: #409EFF;" v-else-if="scope.row.status === 1" >在途</span>
                <span style="color: #409EFF;" v-else-if="scope.row.status === 2" >装货</span>
                <span style="color: #409EFF;" v-else-if="scope.row.status === 3" >装货完成</span>
                <span style="color: #409EFF;" v-else-if="scope.row.status === 4" >卸货</span>
                <span style="color: #409EFF;" v-else-if="scope.row.status === 5" >卸货完成</span>
                <span style="color: #F56C6C;" v-else >待接单</span> -->
              </template>
          </el-table-column>
          <el-table-column
              prop="escort_status"
              label="任务类别"
              align="center"
              width="110">
              <template slot-scope="scope">
                <span style="color: #E6A23C;font-size: 13px;" v-if="scope.row.plan_type === 1" >装车任务</span>
                <span style="color: #F56C6C;font-size: 13px;" v-else-if="scope.row.plan_type === 2" >卸车任务</span>
                <!-- <el-button  v-if="scope.row.plan_type === 1"  type="warning"  size="mini" plain @click="handleDetail(scope.row)">装车任务</el-button>
                <el-button  v-else-if="scope.row.plan_type === 2"  type="danger"  size="mini" plain @click="handleDetail(scope.row)">卸车任务</el-button> -->
                <!-- <el-tag v-if="scope.row.plan_type === 1" type="warning">装车任务</el-tag>
                <el-tag v-else-if="scope.row.plan_type === 2" type="danger">卸车任务</el-tag> -->
              </template>
          </el-table-column>
          <!-- <el-table-column
                  prop="head_num"
                  label="车头"
                  align="center"
                  width="120">
          </el-table-column> -->
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
          <!-- <el-table-column
                  prop="escort_name"
                  label="押运员"
                  align="center"
                  width="150">
          </el-table-column> -->
          <el-table-column
                  prop="product_name"
                  label="货品名称"
                  align="center"
                  width="220">
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
                  prop="initiator"
                  label="创建人"
                  align="center"
                  width="100"
                  show-overflow-tooltip>
          </el-table-column>

          <el-table-column
                  prop="update_time"
                  label="更新时间"
                  align="center"
                  width="200">
              <template slot-scope="scope">
                  <i class="el-icon-time"></i>
                  <span style="margin-left: 10px" v-text="scope.row.update_time"></span>
              </template>
          </el-table-column>
          <el-table-column
                  v-if="hasPermission('admin.plans.addtemporary')"
                  fixed="right"
                  label="操作"
                  align="center"
                  min-width="160">
              <template slot-scope="scope">

                  <el-button size="mini" type="primary"   @click="handleEdit(scope.row)">编辑</el-button>
                  <el-button v-if="scope.row.driver_status === 3 || scope.row.status === 9" size="mini" type="info" disabled @click="handleStatus(scope.$index,scope.row.id,scope.row.driver_status)">已作废</el-button>
                  <!-- <el-button v-else size="mini" type="danger" :disabled="isHandle(scope.row)" @click="handleStatus(scope.$index,scope.row.id,scope.row.driver_status)">作废</el-button> -->
                  <el-button v-else-if="scope.row.driver_status === 2" size="mini" type="info" disabled>完成</el-button>
                  <el-button v-else-if="scope.row.driver_status === 4" size="mini" type="info" disabled>异常</el-button>
                  <el-button v-else size="mini" type="danger" :disabled="isHandle(scope.row)" @click="handleStatus(scope.$index,scope.row.id,scope.row.driver_status)">作废</el-button>
                 
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

import { gettemporary, delnormal, gettemporaryinfo, deltemporary } from '@/api/plan.js'
import checkPermission from '@/utils/checkpermission.js'
import myForm from './form.vue'
import detail from './detail.vue'
import { getArrByKey } from '@/utils'
// import FileSaver from "file-saver";
// import XLSX from "xlsx";
import { exportExcel } from '@/utils/export'

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
    excelData: [],
    multipleSelection: null,
    loading: true,
    searchShow: false,
    total: 0,
    query: {
      page: 1,
      limit: 10,
      keywords: '',
      status: '',
      date: ''
    },
    excelquery: {
      keywords: '',
      type: 'excel',
      status: '',
      date: ''
    }
  }
},
created() {
  this.gettemporary();
},
methods: {
  handleDateChange() {
    if (this.query.date.length) {
      console.log(this.query.date)
      console.log(this.query.date[0])
      const localDate = new Date(this.query.date[0]); // 从接口接收到的 UTC 时间
      this.query.date[0] = localDate.toLocaleString(); // 显示给用户的本地时间
      const localDateend = new Date(this.query.date[1]); // 从接口接收到的 UTC 时间
      this.query.date[1] = localDateend.toLocaleString(); // 显示给用户的本地时间
        const [startDate, endDate] = this.query.date;

        // 将日期对象转换为本地时间
        const localStartDate = new Date(startDate);
        const localEndDate = new Date(endDate);

        // 调整时区偏移
        const offset = localStartDate.getTimezoneOffset() * 60000;

        const correctedStartDate = new Date(localStartDate.getTime() - offset);
        const correctedEndDate = new Date(localEndDate.getTime() - offset);
        // this.query.date[0] = correctedStartDate.toISOString();
        // this.query.date[1] = correctedEndDate.toISOString();
        console.log(this.query)
        console.log('Start Date:', correctedStartDate.toISOString());
        console.log('End Date:', correctedEndDate.toISOString());
    }
  },
  hasPermission(permission) {
    return checkPermission(permission);
  },
  //查询列表
  gettemporary() {
    this.loading = true
    gettemporary(this.query).then(response => {
        if(response !== undefined){
            this.tableData = response.data
            this.total = response.total
        }
        this.loading = false
    })
    this.excelquery.keywords = this.query.keywords
    this.excelquery.status = this.query.status
    this.excelquery.date = this.query.date
    gettemporary(this.excelquery).then(response => {
        if(response !== undefined){
          console.log(response)
            this.excelData = response
        }
    })
  },
  exporttemporaryExcel () {
    this.gettemporary();
    const data = this.excelData.map((item) => {
      // 创建一个新的对象，包含原对象的所有键值对以及新的参数
      return {
        id: item.id,
        "状态": this.status(item.status,item.driver_status),
        "任务类别": this.type(item.plan_type),
        "车头": item.head_num,
        "挂车": item.trailer_num,
        "驾驶员": item.driver_name,
        "押运员": item.escort_name,
        "货品名称": item.product_name,
        "货品数量": item.product_quantity,
        "装货厂家": item.load_factory,
        "装货厂家地址": item.load_address,
        "卸货厂家": item.unload_factory,
        "卸货厂家地址": item.unload_address,
        "创建人": item.initiator,
        "更新时间": item.update_time
      };
    });
// 【YB分类整理】问题描述20240726-2 No.65 顺序调整 by baolei start
    // exportExcel(data,"临时任务");
      exportExcel(data,"装卸任务");
// 【YB分类整理】问题描述20240726-2 No.65 顺序调整 by baolei end
  },
  status(statusnum,driver_status) {
    if(statusnum == 9){
      return "已作废";
    }
    else if(driver_status == 2){
      return "已完成";
    }
    else if(driver_status == 3){
      return "已作废";
    }
    else if(driver_status == 4){
      return "异常";
    }
    else if(driver_status == 1 && statusnum == null){
      return "进行中";
    }
    else if(statusnum == 1){
      return "进行中";
    }
    // else if(statusnum == 0){
    //   return "回库";
    // }
    // else if(statusnum == 1){
    //   return "在途";
    // }
    else if(statusnum == 2){
      return "装货";
    }
    else if(statusnum == 3){
      return "装货完成";
    }
    else if(statusnum == 4){
      return "卸货";
    }
    else if(statusnum == 5){
      return "卸货完成";
    }
    else if(statusnum == 8){
      return "异常";
    }
    else{
      return "待接单";
    }
  },
  type(plan_type) {
    if(plan_type == 1){
      return "装车任务";
    }
    else if(plan_type == 2){
      return "卸车任务";
    }
  },
  //搜索
  handleSearch() {
    this.query.page = 1
    this.gettemporary()
  },
  //刷新重置
  handleReload() {
    this.query.page = 1
    this.query.keywords = ''
    this.query.status = ''
    this.query.date = ''
    this.gettemporary()
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
    this.gettemporary()
  },
  //分页更改当前页
  handleCurrentChange(page) {
    this.query.page = page
    this.gettemporary()
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
        if(raw.driver_status === 2) {
            return false
        } else {
            return true
        }
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
    this.$refs.myAttr.gettemporaryinfo(raw.id)
    this.$refs.myAttr.showForm()
  },
  handleDetail(raw){
    this.$refs.myAttrdetail.getnormalinfo(raw.id)
    this.$refs.myAttrdetail.showDetail()
  },
  //删除
  handleDelete(ids){
    this.$confirm('您确定要删除该任务吗?', '温馨提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    }).then(() => {
      delnormal({ ids: ids }).then(response => {
        this.gettemporary()
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
    let handlerMsg = status === 3 ? '已终止' : '终止';
    this.$confirm('您确定要' + handlerMsg + '该任务吗?', '温馨提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    }).then(() => {
      console.log(555)
      deltemporary({ id: id, status: 3 - status }).then(response => {
        this.tableData[index]['status'] = 3 - status
          this.handleReload()
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
  // handleStatus(index, id, status) {
  //   let handlerMsg = status === 1 ? '作废' : '禁用';
  //   this.$confirm('您确定要' + handlerMsg + '该用户吗?', '温馨提示', {
  //     confirmButtonText: '确定',
  //     cancelButtonText: '取消',
  //     type: 'warning'
  //   }).then(() => {
  //     changeStatus({ id: id, status: 3 - status }).then(response => {
  //       this.tableData[index]['status'] = 3 - status
  //       this.$message({
  //         type: 'success',
  //         message: handlerMsg + '成功!'
  //       });
  //     })
  //   }).catch(() => {
  //     this.$message({
  //       type: 'info',
  //       message: '已取消操作'
  //     });
  //   })
  // }
},
}
</script>
