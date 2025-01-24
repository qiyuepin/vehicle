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
            <el-button type="success" v-permission="'admin.info.addcarhead'" size="mini" @click="handleAdd">新增</el-button>
            <el-button type="primary" size="mini" @click="searchShow = !searchShow">搜索</el-button>
            <el-button type="danger" v-permission="'admin.info.delcarhead'" :disabled="buttonDisabled" @click="handleDeleteAll" size="mini">删除</el-button>
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
                prop="new_version"
                label="版本号"
                align="center"
                width="200">
            </el-table-column>
            <el-table-column
                prop="type"
                label="系统"
                align="center"
                width="200">
            </el-table-column>
            <el-table-column
                    prop="name"
                    label="文件名称"
                    align="center"
                    width="200">
            </el-table-column>
            <el-table-column
                    prop="msg"
                    label="说明"
                    align="center"
                    width="200">
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
                    v-if="hasPermission('admin.info.addcarhead')"
                    fixed="right"
                    label="操作"
                    align="center"
                    min-width="150">
                <template slot-scope="scope">
                    <el-button size="mini" type="primary"  @click="handleEdit(scope.row)">编辑</el-button>
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

        <el-dialog :modal-append-to-body="false" top="0" class="dialogPdf" :fullscreen="true" :append-to-body="true" :visible.sync="dialogVisible">
            <iframe loading="lazy" id="pdf_container" :src="openpdf" frameborder="0" height="100%" width="100%"></iframe>
        </el-dialog>

    </div>
</template>

<script>

import { getversion, delcarhead } from '@/api/Info.js'
import checkPermission from '@/utils/checkpermission.js'
import myForm from './form.vue'
import detail from './detail.vue'
import { getArrByKey } from '@/utils'
import moment from 'moment'
import noImage from '@/assets/no_images/none.png';

export default {
  name: 'version',
  components: {
    myForm,
    detail
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
    this.getversion();
  },
  methods: {
      hasPermission(permission) {
        return checkPermission(permission);
      },
      years(val) {
          const now = new Date()
          // console.log(this.query)
          const valueStart = val === null ? 0 : val
              const valueEnd = val === null  ? 0 : now
              const yearStr = moment(valueEnd).diff(moment(valueStart), 'years')
              const monthStr = moment(valueEnd).diff(moment(valueStart), 'months') % 12
              // console.log(valueStart)
              // console.log(valueEnd)
              // console.log(yearStr)
              // console.log(monthStr)
              if (yearStr === 0 && monthStr === 0) {
                  // console.log('1')
                  return ''
              } else if (yearStr === 0 && monthStr > 0) {
                  // console.log('2')
                  console.log(monthStr + '月')
                  return monthStr + '月'
              } else if (yearStr < 0 || monthStr < 0) {
                  // console.log('3')
                  return ''
              } else {
                  // console.log('4')
                  return yearStr + '年' + ' ' + monthStr + '月'

              }
          // })
      },
    handlePreview(pdfUrl) {
      console.log([pdfUrl])
      this.openpdf = [pdfUrl];
      this.dialogVisible = true;
    },
    //查询列表
    getversion() {
      this.loading = true
      getversion(this.query).then(response => {
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
      this.getversion()
    },
    //刷新重置
    handleReload() {
      this.query.page = 1
      this.query.keywords = ''
      this.query.status = ''
      this.getversion()
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
      this.getversion()
    },
    //分页更改当前页
    handleCurrentChange(page) {
      this.query.page = page
      this.getversion()
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
      this.$refs.myAttrdetail.getversioninfo(raw.id)
      this.$refs.myAttrdetail.showDetail()
    },
    //编辑
    handleEdit(raw){
      console.log("edit"+raw.id);
      this.$refs.myAttr.getversioninfo(raw.id)
      this.$refs.myAttr.showForm()
    },
    //删除
    handleDelete(ids){
      this.$confirm('车头关联的‘人员/车辆管理’也会一并删除，您确定要删除吗?', '温馨提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        delcarhead({ ids: ids }).then(response => {
          this.getversion()
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
  background-color: #fc3b3b;
  color: white;
}
.datestatusinput>input{
  background-color: rgba(255, 0, 0, 0.767);
  color: white;
}
</style>

<style scoped lang="scss">
    ::v-deep .el-tabs__item:focus.is-active.is-focus:not(:active) {
        -webkit-box-shadow: none;
        box-shadow: none;
    }
    ::v-deep .custom-descriptions .el-descriptions-item {
        width: 200px; /* 设置固定宽度 */
    }

    ::v-deep .pdf-box {
        display: inline-block;
        height: 101px;
        width: 101px;
        position: relative;
        border: 1px dashed #d9d9d9;
        margin-right: 10px;
        text-align: center;
        vertical-align: top;
    }
</style>
