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
            <el-button type="warning" size="mini"  @click="handleReload">刷新</el-button>
            <el-button type="success" v-permission="'admin.info.addtrailer'" size="mini" @click="handleAdd">新增</el-button>
            <el-button type="primary" size="mini" @click="searchShow = !searchShow">搜索</el-button>
            <el-button @click="handlePouring" size="mini" style="background-color: #12bbab;border-color: #12bbab;color: #FFFFFF;">倒料</el-button>
            <el-button type="danger" v-permission="'admin.info.deltrailer'" :disabled="buttonDisabled" @click="handleDeleteAll" size="mini">删除</el-button>
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
            <!-- 【YB分类整理】问题描述20240726-2 No.81 顺序调整 by baolei start         -->
<!--            <el-table-column-->
<!--                prop="trailer_status"-->
<!--                label="挂车状态"-->
<!--                align="center"-->
<!--                width="110">-->
<!--                <template slot-scope="scope">-->

<!--                  <span style="color: #67C23A;" v-if="scope.row.trailer_status === 0" >空车</span>-->
<!--                  <span style="color: #409EFF;" v-else-if="scope.row.trailer_status === 1" >重车</span>-->
<!--                  &lt;!&ndash; <span style="color: #13ce66;" v-else-if="scope.row.plan_type === 2" >卸货任务</span> &ndash;&gt;-->
<!--                </template>-->
<!--            </el-table-column>-->
            <el-table-column
                prop="trailer_status"
                label="状态"
                align="center"
                width="110">
                <template slot-scope="scope">

                    <span style="color: #67C23A;" v-if="scope.row.trailer_status === 0" >空车</span>
                    <span style="color: #409EFF;" v-else-if="scope.row.trailer_status === 1" >重车</span>
                    <!-- <span style="color: #13ce66;" v-else-if="scope.row.plan_type === 2" >卸货任务</span> -->
                </template>
            </el-table-column>
            <!-- 【YB分类整理】问题描述20240726-2 No.81 顺序调整 by baolei end         -->
            <el-table-column
                prop="date_status"
                label="是否超期"
                align="center"
                width="120">
                <template slot-scope="scope">
                  <span style="color: #F56C6C;" v-if="scope.row.date_status > 0" >{{scope.row.date_status}}个证件过期</span>
                  <span style="color: #409EFF;" v-else >-</span>
                </template>

            </el-table-column>
            <el-table-column
                prop="trailer_plate"
                label="车（牌）号"
                align="center"
                width="130">
                <template slot-scope="scope">
                    <el-button size="mini"  @click="handleDetail(scope.row)">
                        {{ scope.row.trailer_plate }}
                    </el-button>
                </template>
            </el-table-column>
            <!-- 【YB分类整理】问题描述20240726-2 No.81 顺序调整 by baolei start         -->
            <el-table-column
                prop="trailer_material"
                label="罐体材质"
                align="center"
                width="120">
            </el-table-column>
            <el-table-column
                prop="trailer_volume"
                label="容积"
                align="center"
                width="150"
                :formatter="formatvolume">
            </el-table-column>
            <el-table-column
                prop="trailer_designcode"
                label="设计代码"
                align="center"
                width="120">
            </el-table-column>
            <el-table-column
                prop="trailer_keepwarm"
                label="保温性能"
                align="center"
                width="120">
            </el-table-column>
<!--            <el-table-column-->
<!--                    prop="trailer_brand"-->
<!--                    label="品牌"-->
<!--                    align="center"-->
<!--                    width="120">-->
<!--            </el-table-column>-->
<!--            <el-table-column-->
<!--                    prop="trailer_weight"-->
<!--                    label="自重"-->
<!--                    align="center"-->
<!--                    width="150"-->
<!--                    :formatter="formatWeight">-->
<!--            </el-table-column>-->
<!--            <el-table-column-->
<!--                    prop="trailer_volume"-->
<!--                    label="容积"-->
<!--                    align="center"-->
<!--                    width="150"-->
<!--                    :formatter="formatvolume">-->
<!--            </el-table-column>-->
            <!-- 【YB分类整理】问题描述20240726-2 No.81 顺序调整 by baolei end         -->
            <el-table-column
                    prop="transport_cert"
                    label="道路运输证号"
                    align="center"
                    width="200">
            </el-table-column>
            <!-- 【YB分类整理】问题描述20240726-2 No.81 顺序调整 by baolei start         -->
<!--            <el-table-column-->
<!--                    prop="trailer_material"-->
<!--                    label="罐体材质"-->
<!--                    align="center"-->
<!--                    width="120">-->
<!--            </el-table-column>-->
<!--            <el-table-column-->
<!--                    prop="trailer_designcode"-->
<!--                    label="设计代码"-->
<!--                    align="center"-->
<!--                    width="120">-->
<!--            </el-table-column>-->
<!--            <el-table-column-->
<!--                    prop="trailer_keepwarm"-->
<!--                    label="保温性能"-->
<!--                    align="center"-->
<!--                    width="120">-->
<!--            </el-table-column>-->
            <!-- 【YB分类整理】问题描述20240726-2 No.81 顺序调整 by baolei end         -->
            <el-table-column
                    prop="trailer_scope"
                    label="经营范围"
                    align="center"
                    width="200">
            </el-table-column>
            <!-- 【YB分类整理】问题描述20240726-2 No.81 顺序调整 by baolei start         -->
            <el-table-column
                prop="trailer_brand"
                label="品牌"
                align="center"
                width="120">
            </el-table-column>
            <el-table-column
                prop="trailer_weight"
                label="自重"
                align="center"
                width="150"
                :formatter="formatWeight">
            </el-table-column>
            <!-- 【YB分类整理】问题描述20240726-2 No.81 顺序调整 by baolei end         -->
            <el-table-column
                    prop="product_name"
                    label="货品名称"
                    align="center"
                    width="150">
            </el-table-column>
            <el-table-column
                    prop="product_quantity"
                    label="货品数量"
                    align="center"
                    width="150"
                    :formatter="formatQuantity"
                    >
            </el-table-column>

            <!-- <el-table-column
                    prop="trailer_pressure"
                    label="压力等级"
                    align="center"
                    width="150">
            </el-table-column>
            <el-table-column
                    prop="frame_tank"
                    label="是否为框架罐"
                    align="center"
                    width="150">
            </el-table-column> -->




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
                    prop="regist_time"
                    label="已运营时间"
                    align="center"
                    width="200">
                <template  v-slot="scope">
                    <i class="el-icon-time"></i>
                    <!-- <span style="margin-left: 10px" v-text="scope.row.operation_time"></span> -->
                    <span style="margin-left: 10px">{{ getDateDifference(scope.row.regist_time) }}</span>
                </template>
            </el-table-column>
            <el-table-column
                    prop="scrapp_time"
                    label="强制报废日期"
                    align="center"
                    width="200"
                    >
                <template slot-scope="scope">
                  <i class="el-icon-time"></i>
                  <span style="margin-left: 10px" :class="{ datestatus: scope.row.scrapp_status ? false : true }" v-text="scope.row.scrapp_time"></span>
                </template>
            </el-table-column>
            <el-table-column prop="inspection_time" label="检验有效期"  align="center" width="200">
                <template slot-scope="scope">
                    <i class="el-icon-time"></i>
                    <span style="margin-left: 10px" :class="{ datestatus: scope.row.inspection_status ? false : true }" v-text="scope.row.inspection_time"></span>
                </template>
            </el-table-column>
            <el-table-column prop="validity_time" label="营运证有效期" align="center" width="200">
                <template slot-scope="scope">
                    <i class="el-icon-time"></i>
                    <span style="margin-left: 10px" :class="{ datestatus: scope.row.validity_status ? false : true}" v-text="scope.row.validity_time"></span>
                </template>
            </el-table-column>
            <el-table-column prop="frame_time" label="罐检报告有效期" align="center" width="200">
                <template slot-scope="scope">
                    <i class="el-icon-time"></i>
                    <span style="margin-left: 10px" :class="{ datestatus: scope.row.frame_status ? false : true}" v-text="scope.row.frame_time"></span>
                </template>
            </el-table-column>

            <el-table-column
                    prop="driving_license"
                    label="行驶证"
                    align="center"
                    width="150">
                <!-- <el-image
                        style="width: 40px; height: 30px"
                        :src="scope.row.driving_licenses[0].url"
                        :preview-src-list="[scope.row.driving_licenses[0].url]"
                        slot-scope="scope">
                </el-image> -->
                <template slot-scope="scope">
                  <el-image
                    style="width: 40px; height: 30px"
                    :src="scope.row.driving_licenses[0].url ? scope.row.driving_licenses[0].url : noImage"
                    :preview-src-list="[scope.row.driving_licenses[0].url ? scope.row.driving_licenses[0].url : noImage]">
                  </el-image>
                </template>
            </el-table-column>
            <el-table-column
                    prop="transport_license"
                    label="道路运输证"
                    align="center"
                    width="150">
                <el-image
                        style="width: 40px; height: 30px"
                        :src="scope.row.transport_license"
                        :preview-src-list="[scope.row.transport_license]"
                        slot-scope="scope">
                </el-image>
                <template slot-scope="scope">
                  <el-image
                    style="width: 40px; height: 30px"
                    :src="scope.row.transport_license ? scope.row.transport_license : noImage"
                    :preview-src-list="[scope.row.transport_license ? scope.row.transport_license : noImage]">
                  </el-image>
                  <!-- <el-image
                    style="width: 40px; height: 30px"
                    :src="scope.row.transport_license ? scope.row.transport_license : './assets/no_images/none.png'"
                    :preview-src-list="[scope.row.transport_license ? scope.row.transport_license : './assets/no_images/none.png']">
                  </el-image> -->
                </template>
            </el-table-column>
            <!-- <el-table-column
                    prop="pot_report"
                    label="罐检报告"
                    align="center"
                    width="150">
                <el-image
                        style="width: 40px; height: 30px"
                        :src="scope.row.pot_report"
                        :preview-src-list="[scope.row.pot_report]"
                        slot-scope="scope">
                </el-image>
            </el-table-column>
            <el-table-column
                    prop="cargo_insurance"
                    label="货检保单"
                    align="center"
                    width="150">
                <el-image
                        style="width: 40px; height: 30px"
                        :src="scope.row.cargo_insurance"
                        :preview-src-list="[scope.row.cargo_insurance]"
                        slot-scope="scope">
                </el-image>
            </el-table-column> -->
            <el-table-column
                    prop="pot_report"
                    label="罐检报告"
                    align="center"
                    width="150">
                    <template slot-scope="scope">
                      <el-button size="mini" plain @click="handlePreview(scope.row.pot_report)">点击查看罐检报告</el-button>
                    </template>
            </el-table-column>
            <el-table-column
                    prop="cargo_insurance"
                    label="货检保单"
                    align="center"
                    width="150">
                    <template slot-scope="scope">
                      <el-button size="mini" plain @click="handlePreview(scope.row.cargo_insurance)">点击查看货检保单</el-button>
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
                    v-if="hasPermission('admin.info.addfactory')"
                    fixed="right"
                    label="操作"
                    align="center"
                    min-width="150">
                <template slot-scope="scope">
                  <el-button size="mini" type="primary"   @click="handleEdit(scope.row)">编辑</el-button>
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
        <Pouring ref="myAttrPouring" @updateRow="handleReload"/>

        <el-dialog :modal-append-to-body="false" top="0" class="dialogPdf" :fullscreen="true" :append-to-body="true" :visible.sync="dialogVisible">
            <iframe loading="lazy" id="pdf_container" :src="openpdf" frameborder="0" height="100%" width="100%"></iframe>
        </el-dialog>
    </div>
</template>

<script>

import { getcartrailer, delcartrailer } from '@/api/Info.js'
import checkPermission from '@/utils/checkpermission.js'
import myForm from './form.vue'
import detail from './detail.vue'
import Pouring from './pouring.vue'
import { getArrByKey } from '@/utils'
import noImage from '@/assets/no_images/none.png';

export default {
  name: 'Admin',
  components: {
    myForm,
    detail,
    Pouring
  },
  data() {
    return {
      noImage,
      dialogVisible: false,
      openpdf: '',
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
    this.getcartrailer();
  },
  methods: {
    formatQuantity(row, column, cellValue) {
        const numberValue = parseFloat(cellValue);
        return !isNaN(numberValue) ? (numberValue != 0 ? numberValue.toFixed(2) : ''): '';
    },
    hasPermission(permission) {
      return checkPermission(permission);
    },
    handlePreview(openPdf){
      console.log(openPdf)
      this.openpdf = openPdf;
      this.dialogVisible = true;
    },
    formatWeight(row, column, cellValue) {
      return `${cellValue} t`;
    },
    formatvolume(row, column, cellValue) {
      return `${cellValue} m³`;
    },
    //查询列表
    getcartrailer() {
      this.loading = true
      getcartrailer(this.query).then(response => {
          if(response !== undefined){
            console.log(response.data)
              this.tableData = response.data
              this.total = response.total
          }
          this.loading = false
      })
    },
    getDateDifference(dateString) {
      // console.log(dateString);
      const specificDate = new Date(dateString);
      const currentDate = new Date();

      const specificYear = specificDate.getFullYear();
      const specificMonth = specificDate.getMonth();
      const currentYear = currentDate.getFullYear();
      const currentMonth = currentDate.getMonth();

      let yearsDifference = currentYear - specificYear;
      let monthsDifference = currentMonth - specificMonth;

      if (monthsDifference < 0) {
        yearsDifference -= 1;
        monthsDifference += 12;
      }
      // if(yearsDifference>0){
      //   return `${yearsDifference}年${monthsDifference}个月`;
      // }
      // else{
      //   return `${monthsDifference}个月`;
      // }
      return `${yearsDifference}年${monthsDifference}个月`;
    },
    //搜索
    handleSearch() {
      this.query.page = 1
      this.getcartrailer()
    },
    //刷新重置
    handleReload() {
      this.query.page = 1
      this.query.keywords = ''
      this.query.status = ''
      this.getcartrailer()
    },
    //倒料
    handlePouring() {
      this.$refs.myAttrPouring.showForm()
    },
    //新增
    handleAdd() {
      this.$refs.myAttr.showForm()
    },
    //分页更改页数
    handleSizeChange(limit) {
      this.query.limit = limit
      this.getcartrailer()
    },
    //分页更改当前页
    handleCurrentChange(page) {
      this.query.page = page
      this.getcartrailer()
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
      // console.log("handleDetail"+raw.id);
      this.$refs.myAttrdetail.getcartrailerInfo(raw.id)
      this.$refs.myAttrdetail.showDetail()
    },
    //编辑
    handleEdit(raw){
      // console.log("edit"+raw.id);
      this.$refs.myAttr.getcartrailerInfo(raw.id)
      this.$refs.myAttr.showForm()
    },
    //删除
    handleDelete(ids){
      this.$confirm('您确定要删除该挂车吗?', '温馨提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        delcartrailer({ ids: ids }).then(response => {
          console.log(ids)
          this.getcartrailer()
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
<style>
.datestatus{
  background-color: red;
  color: white;
}
.datestatusinput>input{
  background-color: rgba(255, 0, 0, 0.767);
  color: white;
}
</style>
