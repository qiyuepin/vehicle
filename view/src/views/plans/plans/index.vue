<template>
  <div class="app-container">
      <el-form v-if="searchShow" :inline="true" :model="query" class="demo-form-inline" size="small">
          <el-form-item label="关键字">
              <el-input v-model="query.keywords" placeholder="货品名|厂家名称" clearable></el-input>
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
        <el-button type="success" v-permission="'admin.plans.addplan'" size="mini" @click="handleAdd">新增</el-button>
        <el-button type="primary" size="mini" @click="searchShow = !searchShow">搜索</el-button>
        <el-button type="danger" v-permission="'admin.plans.delplan'" :disabled="buttonDisabled" @click="handleDeleteAll" size="mini">删除</el-button>
        <el-button @click="handleExcelAll" type="primary" size="mini">导出</el-button>
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
              prop="escort_status"
              label="状态"
              align="center"
              width="110">
              <template slot-scope="scope">
                <span style="color: #909399;" v-if="scope.row.status === 0" >未完成</span>
                <span style="color: #13ce66;" v-else-if="scope.row.status === 1" >已完成</span>
              </template>
          </el-table-column>
          <el-table-column
              prop="start_periodic"
              label="始发任务"
              align="center"
              width="110">
              <template slot-scope="scope">
                <i class="el-icon-share" v-if="scope.row.plan_type !== 0" style="display: none;"></i>
                <i class="el-icon-success" v-else-if="scope.row.start_periodic === 1" style="color: #42d885;font-size: 20px;" ></i>
              </template>
          </el-table-column>
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
              prop="unload_factory"
              label="卸货厂家"
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
                  prop="unload_address"
                  label="卸货厂家地址"
                  align="center"
                  width="200"
                  show-overflow-tooltip>
          </el-table-column>
          <el-table-column
                  prop="update_time"
                  label="更新时间"
                  align="center"
                  width="200">
              <template slot-scope="scope">
                  <i class="el-icon-time"></i>
                  <span style="margin-left: 10px" v-text="scope.row.create_time"></span>
              </template>
          </el-table-column>
          <el-table-column
                  v-if="hasPermission('admin.plans.addplan')"
                  fixed="right"
                  label="操作"
                  align="center"
                  min-width="250">
              <template slot-scope="scope">
                  <el-button size="mini" type="danger" v-if="scope.row.status==0" v-permission="'admin.plans.distplan'"  @click="handleDist(scope.row)">分配</el-button>
                  <el-button size="mini" type="danger" v-if="scope.row.status==1" v-permission="'admin.plans.distplan'"  @click="handleDist(scope.row)" disabled>分配</el-button>
                  <el-button size="mini" type="info" plain v-if="scope.row.status==0" v-permission="'admin.plans.editplan'" :disabled="isHandle(scope.row)" @click="handleStatus(scope.$index,scope.row.id,scope.row.status)">未完成</el-button>
                  <el-button size="mini" type="success" plain v-if="scope.row.status==1" v-permission="'admin.plans.editplan'" :disabled="isHandle(scope.row)" @click="handleStatus(scope.$index,scope.row.id,scope.row.status)">已完成</el-button>
              </template>
          </el-table-column>
      </el-table>
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
      <myForm ref="myAttr" @updateRow="handleReload"/>
      <detail ref="myAttrdetail" @updateRow="handleReload"/>
      <planDist ref="myAttrDist" @updateRow="handleReload"/>
  </div>
</template>
<script>
import { getplans, delplan, getplansinfo, editplan, addhisplan } from '@/api/plan.js'
import checkPermission from '@/utils/checkpermission.js'
import myForm from './form.vue'
import detail from './detail.vue'
import planDist from './dist.vue'
import { getArrByKey } from '@/utils'
import { exportExcel } from '@/utils/export'
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
      arrSelection: {
          id: 0,
          status: '',
          plan_type: '',
          start_periodic: '',
          product_name: '',
          product_quantity: '',
          load_factory: '',
          load_address: '',
          unload_factory: '',
          unload_address: '',
          create_time: ''
      },
    loading: true,
    searchShow: false,
    total: 0,
    query: {
      page: 1,
      limit: 10,
      keywords: '',
      status: ''
    },
    excelquery: {
      keywords: '',
      type: 'excel',
      status: ''
    }
  }
},
created() {
  this.getplans();
},
methods: {
  hasPermission(permission) {
    return checkPermission(permission);
  },
  getplans() {
    this.loading = true
    getplans(this.query).then(response => {
        if(response !== undefined){
            this.tableData = response.data
            this.total = response.total
        }
        this.loading = false
    })
    this.excelquery.keywords = this.query.keywords
    this.excelquery.status = this.query.status

  },
  handleExcel(ids){
    
    getplans({ ids: ids, type:'excel' }).then(response => {
        if(response !== undefined){
          console.log(response)
            this.exportnormalExcel (response)
        }
    })
  },
  handleExcelAll(){
    console.log(this.multipleSelection)
    if (this.multipleSelection == null) {
      this.$message({
        type: 'error',
        message: '未选择计划'
      });
    }else{
      const ids = getArrByKey(this.multipleSelection,'id')
      if (!ids || ids.length === 0) {
        this.$message({
          type: 'error',
          message: '未选择计划'
        });
      } else {
 
          this.handleExcel(ids)
      }
    }
  },
  exportnormalExcel (excelData) {
    const data = excelData.map((item) => {
      return {
        id: item.id,
        "状态": this.status(item.status),
        "始发任务": item.start_periodic==1?"是":"否",
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

    this.exportExcelWithWrap(data, "运输计划");
  },
  exportExcelWithWrap(data, fileName) {
    const ws = XLSX.utils.json_to_sheet(data); 
    Object.keys(ws).forEach(cell => {
      const cellObj = ws[cell];

      if (cellObj.v && typeof cellObj.v === 'string') {
        if (!cellObj.s) cellObj.s = {};
        cellObj.s.alignment = {
          vertical: 'center', 
          wrapText: true  
        };
      }
    });
    const wscols = [
      { wch: 5 }, 
      { wch: 8 }, 
      { wch: 10 },
      { wch: 15 }, 
      { wch: 8 },
      { wch: 25 },
      { wch: 30 },
      { wch: 25 },
      { wch: 30 },
      { wch: 10 },
      { wch: 20 },
      { wch: 10 },
      { wch: 30 },
      { wch: 30 },
      { wch: 30 },
      { wch: 30 },
      { wch: 10 },
      { wch: 10 },
      { wch: 15 },
      { wch: 15 },
      { wch: 15 }
    ];
    ws['!cols'] = wscols; 
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Sheet1"); 

    XLSX.writeFile(wb, fileName + ".xlsx");
  },
  exportplansExcel () {
    this.getplans();
    const data = this.excelData.map((item) => {
      return {
        id: item.id,
        "状态": this.status(item.status),
        "始发任务": item.start_periodic==1?"是":"否",
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

    exportExcel(data,"运输计划");
  },
  status(statusnum) {
    if(statusnum == 0){
      return "未完成";
    }
    else if(statusnum == 1){
      return "完成";
    }

  },

  handleSearch() {
    this.query.page = 1
    this.getplans()
  },

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
    this.$confirm('您确定要删除该计划吗?', '温馨提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    }).then(() => {
        console.log(this.multipleSelection)
        console.log(this.arrSelection)
        //addhisplan(this.arrSelection)
        delplan({ ids: ids }).then(response => {
            this.multipleSelection.forEach(row=>{
                this.arrSelection.id = row.id;
                this.arrSelection.status = row.status;
                this.arrSelection.plan_type = row.plan_type;
                this.arrSelection.start_periodic = row.start_periodic;
                this.arrSelection.product_name = row.product_name;
                this.arrSelection.product_quantity = row.product_quantity;
                this.arrSelection.load_factory = row.load_factory;
                this.arrSelection.load_address = row.load_address;
                this.arrSelection.unload_factory = row.unload_factory;
                this.arrSelection.unload_address = row.unload_address;
                this.arrSelection.create_time = row.create_time;
                addhisplan(this.arrSelection)
            })
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
    // 【YB分类整理】问题描述20240726 No.43 顺序调整 by baolei start
    const confirmTmp = '您确定改任务为' + (status === 1 ? '未完成' : '完成') + '吗?'
    // this.$confirm('您确定改任务已完成吗?', '温馨提示', {
      this.$confirm(confirmTmp, '温馨提示', {
    // 【YB分类整理】问题描述20240726 No.43 顺序调整 by baolei end
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    }).then(() => {
      editplan({ id: id, status: 1 - status }).then(response => {
        this.tableData[index]['status'] = 1 - status
        this.$message({
          type: 'success',
          message: '成功!'
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
