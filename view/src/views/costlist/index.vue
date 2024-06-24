<template>
  <div class="app-container">
      <el-form v-if="searchShow" :inline="true" :model="query" class="demo-form-inline" size="small">
          <el-form-item label="关键字">
              <el-input v-model="query.keywords" placeholder="用户名|昵称|手机号|邮箱" clearable></el-input>
          </el-form-item>
          <el-form-item label="费用类别">
              <!-- <el-select v-model="query.status" placeholder="选择类别" clearable>
                  <el-option label="全部" value="0"/>
                  <el-option label="启用" value="2"></el-option>
                  <el-option label="禁用" value="1"></el-option>
              </el-select> -->
              <el-select v-model="query.type_name" clearable placeholder="请选择费用类别">
                <el-option
                  v-for="item in typelist"
                  :key="item.value"
                  :label="item.type_name"
                  :value="item.type_name">
                </el-option>
              </el-select>
          </el-form-item>
          <el-form-item>
              <el-button type="primary" icon="el-icon-search" @click="handleSearch">查询</el-button>
              <el-button type="warning" icon="el-icon-refresh-left" @click="handleReload">重置</el-button>
          </el-form-item>
      </el-form>
      <el-row style="margin-bottom: 10px;">
          <el-button type="warning" size="mini"  @click="handleReload">刷新</el-button>
          <el-button type="success" v-permission="'admin.cost.addcost'" size="mini" @click="handleAdd">新增</el-button>
          <el-button type="primary" size="mini" @click="searchShow = !searchShow">搜索</el-button>
          <el-button type="danger" v-permission="'admin.cost.delcost'" :disabled="buttonDisabled" @click="handleDeleteAll" size="mini">删除</el-button>
          <el-button @click="exportExcel" type="primary" size="mini">导出</el-button>
      </el-row>
      <el-table
              ref="multipleTable"
              :data="tableData"
              id="datatable"
              tooltip-effect="dark"
              :summary-method="getSummaries"
              show-summary
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
                  prop="year"
                  label="年份"
                  align="center"
                  width="80">
          </el-table-column>
          
          <!-- <el-table-column
              prop="period_id_driver"
              label="费用周期"
              align="center"
              width="220">
              <template slot-scope="scope">
                <el-button size="mini" type="primary" v-permission="'admin.cost.editcost'" plain  @click="costlist(scope.row)">{{ scope.row.period_id_driver }}</el-button>
 
              </template>
          </el-table-column> -->
          <el-table-column
                  prop="type_name"
                  label="费用类别"
                  align="center"
                  width="150">
          </el-table-column>
          <el-table-column
                  prop="other_type"
                  label="其他类别"
                  align="center"
                  width="150">
          </el-table-column>
          <el-table-column
                  prop="cost_money"
                  label="费用金额"
                  align="center"
                  width="150">
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
                  width="150">
          </el-table-column>
          <el-table-column
                  prop="escort_name"
                  label="押运员"
                  align="center"
                  width="150">
          </el-table-column> -->
          
          <el-table-column
                  prop="cost_img"
                  label="费用照片1"
                  align="center"
                  width="150">
              <!-- <el-image
                      style="width: 40px; height: 30px"
                      :src="scope.row.cost_img"
                      :preview-src-list="[scope.row.cost_img]"
                      slot-scope="scope">
              </el-image> -->
              <template slot-scope="scope">
                <el-image
                  style="width: 40px; height: 30px"
                  :src="scope.row.cost_img ? scope.row.cost_img : noImage"
                  :preview-src-list="[scope.row.cost_img ? scope.row.cost_img : noImage]">
                </el-image>
              </template>
          </el-table-column>
          <el-table-column
                  prop="cost_creater"
                  label="添加人"
                  align="center"
                  width="150">
          </el-table-column>
          <el-table-column
                  prop="remark"
                  label="备注"
                  align="center"
                  width="200">
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
                  <el-button size="mini" type="primary" v-permission="'admin.cost.editcost'"  @click="handleEdit(scope.row)">编辑</el-button>
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
      <costForm ref="myCost" @updateRow="handleReload"/>
  </div>
</template>

<script>

import { getcostlist, delcost, getcosttype } from '@/api/cost.js'
import costForm from './costform.vue'
import { getArrByKey } from '@/utils'
import FileSaver from "file-saver";
import XLSX from "xlsx";
import noImage from '../../assets/no_images/none.png';

export default {
name: '',
components: {
  costForm
},
data() {
  return {
    buttonDisabled: true,
    tableData: [],
    multipleSelection: null,
    loading: true,
    searchShow: false,
    total: 0,
    typelist: [],
    excelData: [],
    noImage,
    query: {
      page: 1,
      limit: 10,
      keywords: '',
      platform: 'pc',
      type_name: '',
      period_id: this.$route.query.period_id,
      status: ''
    },
    excelquery: {
      keywords: '',
      platform: 'app',
      type_name: '',
      period_id: this.$route.query.period_id,
      status: ''
    }
  }
},
created() {
  this.getcostlist();
  this.getcosttype();
},
methods: {
  getcosttype() {
    getcosttype().then(response => {
        if(response !== undefined){
          console.log(response)
          // this.infolist = response.data
          this.typelist = response
        }
    })
  },
  //查询列表
  getcostlist() {
    this.loading = true
    getcostlist(this.query).then(response => {
        if(response !== undefined){
          console.log(response);
            this.tableData = response.data
            this.total = response.total
        }
        this.loading = false
    })
    getcostlist(this.excelquery).then(response => {
        if(response !== undefined){
          console.log(response);
          this.excelData = response
            // this.tableData = response.data
            // this.total = response.total
        }
        
    })
  },
  exportExcel() {
    console.log(this.excelData)
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

  exportExcel0 () {
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
        '费用列表.xlsx'
      )
    } catch (e) {
      if (typeof console !== 'undefined') console.log(e, wbout)
    }
    return wbout
  },
  getSummaries(param) {
    const { columns, data } = param;
    const sums = [];
    columns.forEach((column, index) => {
      if (index === 1) {
        sums[index] = '合计费用';
        return;
      }
      // console.log(column);
      // cost_money
      const values = data.map(item => Number(item[column.property]));
      if (!values.every(value => isNaN(value)) && column.property == "cost_money") {
        sums[index] = values.reduce((prev, curr) => {
          const value = Number(curr);
          if (!isNaN(value)) {
            return prev + curr;
          } else {
            return prev;
          }
        }, 0);
        // console.log(index);
        sums[index] += ' 元';
      } else {
        // sums[index] = 'N/A';
      }
    });

    return sums;
  },
  //搜索
  handleSearch() {
    this.query.page = 1
    this.excelquery.type_name = this.query.type_name
    this.getcostlist()
  },
  //刷新重置
  handleReload() {
    this.query.page = 1
    this.query.keywords = ''
    this.query.status = ''
    this.query.platform = 'pc'
    this.query.type_name = ''
    this.query.period_id = this.$route.query.period_id
    this.excelquery.type_name = ''
    this.excelquery.period_id = this.$route.query.period_id
    this.excelquery.platform = 'app'
    this.getcostlist()
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
    console.log("period_id--"+this.$route.query.period_id)
    this.$refs.myCost.getaddinfo(this.$route.query.period_id)
    this.$refs.myCost.showForm()
  },
  //分页更改页数
  handleSizeChange(limit) {
    this.query.limit = limit
    this.getcostlist()
  },
  //分页更改当前页
  handleCurrentChange(page) {
    this.query.page = page
    this.getcostlist()
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
    this.$refs.myCost.getinfo(raw.id)
    this.$refs.myCost.showForm()
  },
  //删除
  handleDelete(ids){
    this.$confirm('您确定要删除该用户吗?', '温馨提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    }).then(() => {
      delcost({ ids: ids }).then(response => {
        this.getcostlist()
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
