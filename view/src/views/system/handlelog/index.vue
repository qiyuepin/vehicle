<template>
    <div class="app-container">
        <el-form v-if="searchShow" :inline="true" :model="query" class="demo-form-inline" size="small">
            <el-form-item label="关键字">
                <el-input v-model="query.keywords" placeholder="用户名|昵称" clearable></el-input>
            </el-form-item>
            <el-form-item label="登录时间">
                <el-date-picker
                        v-model="query.time"
                        type="daterange"
                        range-separator="至"
                        @change="changeTimeZone"
                        start-placeholder="开始日期"
                        end-placeholder="结束日期">
                </el-date-picker>
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
            <el-tooltip class="item" effect="dark" content="搜索" placement="top">
                <el-button type="primary" icon="el-icon-search" circle @click="searchShow = !searchShow"></el-button>
            </el-tooltip>
        </el-row>
        <el-table
                ref="multipleTable"
                :data="tableData"
                tooltip-effect="dark"
                border
                stripe
                style="width: 100%"
                v-loading="loading">
            <el-table-column type="expand">
                <template slot-scope="props">
                    <el-form label-position="left" inline class="demo-table-expand">
                        <el-form-item label="头像">
                            <el-avatar :size="40" :src="props.row.avatar" shape="square">
                            </el-avatar>
                        </el-form-item>
                        <el-form-item label="用户名">
                            <span>{{ props.row.username }}</span>
                        </el-form-item>
                        <el-form-item label="请求路由">
                            <span>{{ props.row.route }}</span>
                        </el-form-item>
                        <el-form-item label="请求方式">
                            <span>{{ props.row.method }}</span>
                        </el-form-item>
                        <el-form-item label="请求IP">
                            <span>{{ props.row.ip }}</span>
                        </el-form-item>
                        <el-form-item label="请求状态">
                            <el-tag :type="props.row.status==1?'danger':'success'" v-text="props.row.status==1?'失败':'成功'"></el-tag>
                        </el-form-item>
                        <el-form-item label="操作时间">
                            <i class="el-icon-time"></i>
                            <span style="margin-left: 5px;">{{ props.row.create_time }}</span>
                        </el-form-item>
                    </el-form>
                    <el-form label-position="left" inline class="demo-table-expand" v-if="props.row.status==1">
                        <el-form-item label="请求参数">
                            <span>{{props.row.param}}</span>
                        </el-form-item>
                        <el-form-item label="错误信息">
                            <span>{{props.row.error.message}}</span>
                        </el-form-item>
                        <el-form-item label="错误文件">
                            <span>{{props.row.error.file}}</span>
                        </el-form-item>
                        <el-form-item label="错误行数">
                            <span>{{props.row.error.line}}</span>
                        </el-form-item>
                    </el-form>
                </template>
            </el-table-column>
            <el-table-column
                    prop="avatar"
                    label="头像"
                    align="center"
                    width="80">
                <el-avatar :size="40" :src="scope.row.avatar" shape="square" slot-scope="scope">
                </el-avatar>
            </el-table-column>
            <el-table-column
                    prop="username"
                    label="用户名"
                    align="center"
                    min-width="120">
            </el-table-column>
            <el-table-column
                    prop="route"
                    label="请求路由"
                    align="center"
                    min-width="200">
            </el-table-column>
            <el-table-column
                    prop="method"
                    label="请求方式"
                    align="center"
                    min-width="120">
            </el-table-column>
            <el-table-column
                    prop="ip"
                    label="请求IP"
                    align="center"
                    min-width="120">
            </el-table-column>
            <el-table-column
                    prop="status"
                    label="状态"
                    align="center"
                    min-width="120">
                <template slot-scope="scope">
                    <el-tag :type="scope.row.status==1?'danger':'success'" v-text="scope.row.status==1?'失败':'成功'"></el-tag>
                </template>
            </el-table-column>
            <el-table-column
                    prop="create_time"
                    label="操作时间"
                    align="center"
                    min-width="200">
                <template slot-scope="scope" v-if="scope.row.create_time">
                    <i class="el-icon-time"></i>
                    <span style="margin-left: 10px" v-text="scope.row.create_time"></span>
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
    </div>
</template>

<script>

import { getHandleLog } from '@/api/admin.js'

export default {
  name: 'Handlelog',
  data() {
    return {
      tableData: [],
      loading: true,
      searchShow: false,
      total: 0,
      query: {
        page: 1,
        limit: 10,
        keywords: '',
        time: []
      }
    }
  },
  created() {
    this.getList();
  },
  methods: {
    //查询列表
    getList() {
      this.loading = true
      getHandleLog(this.query).then(response => {
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
      this.getList()
    },
    changeTimeZone(){
      if(this.query.time&&this.query.time.length>0){
        this.query.time[0] = this.$moment(this.query.time[0]).format("YYYY-MM-DD HH:mm:ss")
        this.query.time[1] = this.$moment(this.query.time[1]).format("YYYY-MM-DD HH:mm:ss")
      }
    },
    //刷新重置
    handleReload() {
      this.query.page = 1
      this.query.keywords = ''
      this.query.time = []
      this.getList()
    },
    //分页更改页数
    handleSizeChange(limit) {
      this.query.limit = limit
      this.getList()
    },
    //分页更改当前页
    handleCurrentChange(page) {
      this.query.page = page
      this.getList()
    },
  },
}
</script>
<style scoped lang="scss">
    .demo-table-expand {
        font-size: 0;
        width: 50%;
        vertical-align: top;
        display: inline-block;
        word-break:break-all;
    }
    ::v-deep .demo-table-expand label {
        width: 90px;
        color: #99a9bf;
    }
    .demo-table-expand .el-form-item {
        margin-right: 50px;
        margin-bottom: 0;
        display: block;
        margin-left: 50px;
        word-break:break-all;
    }
</style>
