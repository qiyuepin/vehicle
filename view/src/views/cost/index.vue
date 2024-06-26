<template>
  <div class="app-container">
      <el-form v-if="searchShow" :inline="true" :model="query" class="demo-form-inline" size="small">
          <el-form-item label="关键字">
              <el-input v-model="query.keywords" placeholder="驾驶员|挂车号" clearable></el-input>
          </el-form-item>
          <el-form-item label="状态">
              <el-select v-model="query.status" placeholder="选择状态" clearable>
                  <el-option label="全部" value=""/>
                  <el-option label="进行中" value="0"></el-option>
                  <el-option label="已结束" value="1"></el-option>
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
          <!-- <el-button type="danger" v-permission="'auth.admin.delete'" :disabled="buttonDisabled" @click="handleDeleteAll" size="mini">删除</el-button> -->
          <el-button @click="exportExcel" type="primary" size="mini">导出所有费用</el-button>
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
                  prop="year"
                  label="年份"
                  align="center"
                  width="80">
          </el-table-column>
          <el-table-column
                  prop="total"
                  label="总金额"
                  align="center"
                  width="100">
          </el-table-column>
          <el-table-column
              prop="cost_status"
              label="状态"
              align="center"
              width="100">
              <template slot-scope="scope">
                <el-tag type="info"  v-if="scope.row.status === 0" >未开始</el-tag>
                <el-tag type="primary" v-else-if="scope.row.status === 1">进行中</el-tag>
                <el-tag type="success" v-else-if="scope.row.status === 2">已结束</el-tag>
                <el-tag type="info" v-else >-</el-tag>
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
          <!-- <el-table-column
                  prop="period_id_driver"
                  label="费用周期"
                  align="center"
                  width="200">
          </el-table-column> -->
          <el-table-column
              prop="period_id_driver"
              label="费用周期"
              align="center"
              width="220">
              <template slot-scope="scope">
                <el-button size="mini" type="primary" v-permission="'admin.cost.editcost'" plain  @click="costlist(scope.row)">{{ scope.row.period_id_driver }}</el-button>
 
              </template>
          </el-table-column>
          
          
          <el-table-column
                  prop="initiator"
                  label="创建人"
                  align="center"
                  width="120">
          </el-table-column>
          <el-table-column
                  prop="dispatcher"
                  label="分配人"
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
                  prop="trailer_num"
                  label="挂车号"
                  align="center"
                  width="150">
          </el-table-column>
          <!-- <el-table-column
                  prop="head_num"
                  label="车头号"
                  align="center"
                  width="200">
          </el-table-column>
          <el-table-column
                  prop="escort_name"
                  label="押运员"
                  align="center"
                  width="200">
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
                  <!-- <el-button size="mini" type="primary" v-permission="'admin.cost.editcost'"  @click="handleEdit(scope.row)">上传费用</el-button> -->
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
import FileSaver from "file-saver";
import XLSX from "xlsx";

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
    excelData:[],
    query: {
      page: 1,
      limit: 10,
      platform: 'pc',
      keywords: '',
      status: ''
    },
    excelquery: {
      keywords: '',
      platform: 'excelall',
      status: ''
    }
  }
},
created() {
  this.getcost();
  this.getexcel();
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
    });
    
    this.excelquery.keywords = this.query.keywords
    this.excelquery.status = this.query.status
    console.log(this.excelquery)
    getcost(this.excelquery).then(response => {
        if(response !== undefined){
          this.excelData = response
        }
    }) 
  },
  getexcel() {

    
  },
  //搜索
  handleSearch() {
    this.query.page = 1
    this.getcost()
    // this.getexcel()
  },
  //刷新重置
  handleReload() {
    this.query.page = 1
    this.query.keywords = ''
    this.query.status = ''
    this.query.platform = 'pc'
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
  costlist(raw){
    console.log("raw.id--"+raw.id);
    // this.$refs.myAttr.costlist(raw[0])
    this.$router.push({ path: '/costlist/index', query: { period_id: raw.id }});
    
    // this.$router.push({ path: '/driver/regulation', query: { id: raw[0] }});
  },
  exportExcel () {
    const data = this.excelData.map((item) => {
      // 创建一个新的对象，包含原对象的所有键值对以及新的参数
      return {
        id: item.id,
        "费用周期": item.period_id_driver,
        "费用类别": item.type_name,
        "其他类别": item.other_type,
        "费用金额": item.cost_money,
        "驾驶员": item.driver_name,
        "挂车号": item.trailer_num,
        "费用照片": item.cost_img,
        "备注": item.remark,
        "添加人": item.cost_creater
      };
    });

    // const data = this.excelData;


    // 构建 Workbook
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.json_to_sheet(data);
   

    // 添加图片
    data.forEach((item, index) => {
      const img = new Image();
      img.src = item.image;
      const ctx = document.createElement('canvas').getContext('2d');
      img.onload = () => {
        ctx.canvas.width = img.width;
        ctx.canvas.height = img.height;
        ctx.drawImage(img, 0, 0);
        const dataURI = ctx.canvas.toDataURL('image/jpeg');
        XLSX.utils.sheet_addImage(ws, `A${index + 2}`, dataURI, { extension: 'png' });
      };
    });

    // 将 Worksheet 添加到 Workbook
    XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');

    // 导出 Excel 文件
    const wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
    try {
      FileSaver.saveAs(
        new Blob([wbout], { type: 'application/octet-stream' }),
        '费用列表.xlsx'
      );
    } catch (e) {
      console.log(e);
    }
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
