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
            <el-table-column
                    type="index"
                    label="序号"
                    align="center"
                    width="80">
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
                    prop="nickname"
                    label="昵称"
                    align="center"
                    min-width="120">
            </el-table-column>
            <el-table-column
                    prop="login_ip"
                    label="登录IP"
                    align="center"
                    min-width="150">
            </el-table-column>
            <el-table-column
                    prop="login_time"
                    label="登录时间"
                    align="center"
                    min-width="200">
                <template slot-scope="scope" v-if="scope.row.login_time">
                    <i class="el-icon-time"></i>
                    <span style="margin-left: 10px" v-text="scope.row.login_time"></span>
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

import { getLoginLog } from '@/api/admin.js'

export default {
  name: 'Loginlog',
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
      getLoginLog(this.query).then(response => {
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
