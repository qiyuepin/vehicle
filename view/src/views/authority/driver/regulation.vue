<template>
    <div class="app-container">
        <el-form v-if="searchShow" :inline="true" :model="query" class="demo-form-inline" size="small">
            <el-form-item label="关键字">
                <el-input v-model="query.keywords" placeholder="违章地点|违法事实" clearable></el-input>
            </el-form-item>
            <!-- <el-form-item label="状态">
                <el-select v-model="query.status" placeholder="选择状态" clearable>
                    <el-option label="全部" value="0"/>
                    <el-option label="启用" value="2"></el-option>
                    <el-option label="禁用" value="1"></el-option>
                </el-select>
            </el-form-item> -->
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
                <el-button type="success" v-permission="'admin.driver.addregulation'" icon="el-icon-plus" circle @click="handleAdd"></el-button>
            </el-tooltip>
  
            <el-tooltip class="item" effect="dark" content="搜索" placement="top">
                <el-button type="primary" icon="el-icon-search" circle @click="searchShow = !searchShow"></el-button>
            </el-tooltip>
            <el-tooltip class="item" effect="dark" content="删除" placement="top">
                <el-button type="danger" v-permission="'admin.driver.delregulation'" :disabled="buttonDisabled" @click="handleDeleteAll" icon="el-icon-delete" circle></el-button>
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
                    width="50">
            </el-table-column>
            <el-table-column
                    prop="regulation_time"
                    label="违章时间"
                    align="center"
                    width="150">
                <template slot-scope="scope">
                    <i class="el-icon-time"></i>
                    <span style="margin-left: 10px" v-text="scope.row.regulation_time"></span>
                </template>
            </el-table-column>
            <!-- <el-table-column
                    prop="regulation_time"
                    label="时间"
                    align="center"
                    width="120">
            </el-table-column> -->
            <el-table-column
                    prop="regulation_place"
                    label="违章地点"
                    align="center"
                    width="150">
            </el-table-column>
            <el-table-column
                    prop="regulation_truth"
                    label="违法事实"
                    align="center"
                    width="150">
            </el-table-column>
            <el-table-column
                    prop="regulation_code"
                    label="记分情况"
                    align="center"
                    width="200">
            </el-table-column>
            <el-table-column
                    prop="regulation_deal"
                    label="处理情况"
                    align="center"
                    width="200">
            </el-table-column>
            <el-table-column
                    prop="regulation_remark"
                    label="备注"
                    align="center"
                    width="200">
            </el-table-column>

            
            <!-- <el-table-column
                    prop="status"
                    label="状态"
                    align="center"
                    width="100">
                <template slot-scope="scope">
                    <el-tag :type="scope.row.status==1?'danger':'success'" v-text="scope.row.status==1?'禁用':'启用'"></el-tag>
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
                    min-width="100">
                <template slot-scope="scope">
                    <el-tooltip class="item" effect="dark" content="编辑" placement="top">
                        <el-button size="mini" type="primary" v-permission="'admin.driver.editregulation'" icon="el-icon-edit-outline" circle
                                   @click="handleEdit(scope.row)"></el-button>
                    </el-tooltip>
                    <el-tooltip class="item" effect="dark" content="删除" placement="top">
                        <el-button size="mini" type="danger"  v-permission="'admin.driver.delregulation'" :disabled="isHandle(scope.row)" icon="el-icon-delete"
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

import { getregulation, getregulationinfo,editregulation, delregulation } from '@/api/admin.js'
import myForm from './regulationform.vue'
import { getArrByKey } from '@/utils'

export default {
  name: 'Regulation',
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
        id: this.$route.query.id,
        status: ''
      }
    }
  },
  created() {
    this.getregulation();
  },
  methods: {
    //查询列表
    getregulation() {
      this.loading = true
      getregulation(this.query).then(response => {
        // console.log(response);
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
      this.getregulation()
    },
    //刷新重置
    handleReload() {
      this.query.page = 1
      this.query.keywords = ''
      this.query.status = ''
      this.getregulation()
    },
    //新增
    handleAdd() {
      this.$refs.myAttr.showForm()
    },
    //分页更改页数
    handleSizeChange(limit) {
      this.query.limit = limit
      this.getregulation()
    },
    //分页更改当前页
    handleCurrentChange(page) {
      this.query.page = page
      this.getregulation()
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
      console.log('--raw.id--')
      console.log(raw.id)
      this.$refs.myAttr.getregulationInfo(raw.id)
      this.$refs.myAttr.showForm()
    },
    //删除
    handleDelete(ids){
      this.$confirm('您确定要删除该条违章吗?', '温馨提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        delregulation({ ids: ids }).then(response => {
          this.getregulation()
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
    }
  },
}
</script>
