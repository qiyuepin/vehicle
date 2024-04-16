<template>
    <el-drawer
            v-if="drawerShow"
            :before-close="handleClose"
            :with-header="false"
            :visible.sync="dialog"
            :wrapperClosable="true"
            size="200px"
            direction="rtl"
            custom-class="demo-drawer"
            ref="drawer"
    >
        <div class="demo-drawer__content" style="padding: 10px">
            <div class="bg-purple-dark" @click="searchShow"><i class="el-icon-search"></i>&nbsp;搜索</div>
            <div v-show="isSearch" class="search">
                <el-input ref="search" prefix-icon="el-icon-search" @clear="isSearch=!isSearch" clearable placeholder="请输入用户名" @keypress.native.enter="searchData" v-model="search" class="input-with-select">
                </el-input>
                <div @click="isSearch=!isSearch" style="height: 100%">
                    <div class="searchData"   v-if="searchRes">
                        <template v-if="searchRes===1">
                            <div class="card" @click.stop="dialogVisible = true">
                                <div style="display: inline-block;vertical-align:top;">
                                    <img :src="user.avatar" class="image">
                                </div>
                                <div style="display: inline-block;padding: 5px 10px;">
                                    <h5 class="username" v-text="user.username"></h5>
                                    <p class="nickname" v-text="user.nickname"></p>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <el-empty description="查无结果"></el-empty>
                        </template>
                    </div>
                </div>
            </div>
            <el-tabs v-show="!isSearch" style="height: 200px;">
                <el-tab-pane label="消息">
                </el-tab-pane>
                <el-tab-pane label="好友">
                    <div class="friend-info" v-for="item of friendData" :key="item.id" @click="openChat(item)">
                        <img :src="item.avatar" width="40" height="40" alt="">
                        <div class="friend-content">
                            <h5 v-text="item.remark?item.remark:item.nickname" :title="item.remark?item.remark:item.nickname"></h5>
                            <span class="sign" v-text="item.sign" :title="item.sign"></span>
                        </div>
                    </div>
                </el-tab-pane>
                <el-tab-pane label="申请" class="apply-list">
                    <div class="apply-info" v-for="item of applyData" :key="item.id">
                        <img :src="item.avatar" width="40" height="40" alt="">
                        <div class="apply-content">
                            <h5 v-text="item.nickname" :title="item.nickname"></h5>
                            <span class="remark" v-text="item.remark" :title="item.remark"></span>
                        </div>
                        <div class="apply-btn">
                            <template v-if="item.status == 1">
                                <button @click.stop="agreeApply(item)">同意</button>
                            </template>
                            <template v-else>
                                <span class="apply-status" v-if="item.status==2">已添加</span>
                                <span class="apply-status" v-if="item.status==4">已过期</span>
                            </template>
                        </div>
                    </div>
                </el-tab-pane>
            </el-tabs>
            <div class="demo-drawer__footer" style="position:fixed;bottom:0px;">
                <el-button size="mini" @click="$refs.drawer.closeDrawer()">取 消</el-button>
                <el-button size="mini" type="primary" @click="saveData()">确 定
                </el-button>
            </div>
            <el-dialog
                    title="添加好友"
                    :modal="false"
                    :show-close="false"
                    :visible.sync="dialogVisible"
                    :center="true"
                    width="30%">
                <el-form label-position="top" label-width="80px" :model="apply">
                    <el-form-item label="信息介绍">
                        <div class="info">
                            <img :src="user.avatar" class="image">
                            <div class="info-content">
                                <h5 class="username" v-text="user.username"></h5>
                                <p class="nickname" v-text="user.nickname"></p>
                            </div>
                        </div>
                    </el-form-item>
                    <el-form-item label="申请说明">
                        <el-input :autosize="{ minRows: 4}" :maxlength="100" show-word-limit type="textarea" placeholder="我是..." v-model="apply.remark"></el-input>
                    </el-form-item>
                </el-form>
                <span slot="footer" class="dialog-footer">
                <el-button size="mini" @click="dialogVisible = false">取 消</el-button>
                <el-button type="success" size="mini" @click="addFriend">发 送</el-button>
              </span>
            </el-dialog>
            <el-dialog
                    :modal="false"
                    :title="chat.title"
                    :visible.sync="chat.dialogVisible"
                    width="600px"
                    custom-class="chat-box"
                    :close-on-click-modal="false"
                    :lock-scroll="false"
                    center>
                <div class="chat-content">
                    <div v-bind:class="item.position==1 ? 'chat-cleft' : 'chat-cright'" v-for="(item,index) of chatData" :key="index">
                        <div class="chat-time" v-text="item.time"></div>
                        <div class="chat-info">
                            <img v-if="item.position==1" :src="item.avatar" alt="">
                            <div class="chat-text">
                                <div class="chat-name" v-text="item.name"></div>
                                <div class="chat-message" v-text="item.message"></div>
                            </div>
                            <img v-if="item.position==2" :src="item.avatar" alt="">
                        </div>
                    </div>
                </div>
                <span slot="footer" class="dialog-footer">
                    <textarea class="chat-msg" ref="message" v-model="chat.content" @keydown.enter="enterMsg"></textarea>
                    <div class="chat-btn">
                        <el-button type="primary" size="mini" @click="sendMsg">发 送</el-button>
                    </div>
                </span>
            </el-dialog>
        </div>
    </el-drawer>
</template>

<script>

import { getUser,addFriend,getApplyList,agreeFriendApply,getFriendList } from '@/api/chat.js'
import { Message, MessageBox } from "element-ui";

export default {
  name: "ChatList",
  data() {
    return {
      search:'',
      searchRes:0,
      isSearch:false,
      dialog: false,
      drawerShow:false,
      dialogVisible: false,
      applyRes:false,
      apply:{
        remark:''
      },
      user:{
        id:'',
        username:'',
        nickname:'',
        avatar:''
      },
      chat:{
        title:'',
        dialogVisible:false,
        content:'',
        touid:0,
        toavatar:'',
        toname:'',
      },
      chatData:[],
      applyData:[],
      friendData:[]
    }
  },
  created() {
    this.getApplyList();
    this.getFriendList();
  },
  sockets: {
    chat(res) {                                 //监听message事件，方法是后台定义和提供的
      if(res.status){
        let data = res.data;
        let msg = {
          position:this.$store.getters.uid==data.sender.id?2:1,
          uid:data.sender.id,
          avatar:data.sender.avatar,
          name:data.sender.nickname,
          type:data.type,
          time:data.time,
          message:data.message
        }
        this.chatData.push(msg)
      }else{
        this.$message({
          type: 'error',
          message: res.message
        });
      }

    },
  },
  methods: {
    enterMsg(e){
        if(!((e.keyCode == 13&&e.ctrlKey)||(e.keyCode == 13&&e.shiftKey))){
            this.sendMsg();
            e.preventDefault();
        }
    },
    sendMsg(){
      this.$socket.emit('chat', {
        'to':this.chat.touid,
        'type':1,
        'message':this.chat.content,
        'time':this.$moment().format('YYYY-MM-DD HH:mm:ss')
      });
      this.chat.content = ''
    },
    openChat(item){
      this.chat.dialogVisible = true
      this.chat.title = item.remark ? item.remark : item.nickname
      this.chat.touid = item.id
      this.chat.toavatar = item.avatar
      this.chat.toname = this.chat.title
      this.$nextTick(function () {
        this.$refs.message.focus();
      });
    },
    getApplyList(){
      getApplyList().then(response => {
        this.applyData = response.data
      })
    },
    getFriendList(){
      getFriendList().then(response => {
        this.friendData = response.data
      })
    },
    agreeApply(item){
      this.$prompt('同意好友申请？','温馨提示',{
        confirmButtonText: '同意',
        showClose:false,
        showCancelButton:true,
        cancelButtonText: '取消',
        type:'warning',
        inputPattern: /^[\u4e00-\u9fa5]{0,20}$/,
        inputPlaceholder:'备注姓名',
        inputErrorMessage: '备注不能为空',
        inputValue:item.nickname
      }).then(({ value }) => {
        agreeFriendApply({id:item.id,remark:value}).then(response => {
          this.getApplyList();
          this.$message({
            type: 'success',
            message: '添加成功!'
          });
        })
      }).catch(() => {

      });
    },
    searchShow(){
      this.isSearch = true;
      this.$nextTick(function () {
        this.$refs.search.$el.querySelector('input').focus();
      });
    },
    searchData(e){
      if (e.keyCode == 13&&this.search) {
        getUser({
          account:this.search
        }).then(response => {
            if(response !== undefined){
                if(response.status){
                    this.user.id=response.user.id
                    this.user.username=response.user.username
                    this.user.nickname=response.user.nickname
                    this.user.avatar=response.user.avatar
                    this.searchRes = 1
                }else{
                    this.searchRes = 2
                }
            }
        })
      }
    },
    addFriend(){
      addFriend({touid:this.user.id,remark:this.apply.remark}).then(response=>{
        this.$message({
          message: '发送成功',
          type: 'success',
          duration: 5 * 1000
        })
        this.dialogVisible = false
      })
    },
    handleChange(){

    },
    handleClose() {
      this.dialog = false
      this.drawerShow = false
      this.isSearch = false;
    },
    showForm() {
      this.dialog = true
      this.drawerShow = true
    },
    resetData(){
    },
  }
}
</script>

<style scoped lang="scss">
    ::v-deep .el-tabs__item:focus.is-active.is-focus:not(:active) {
        -webkit-box-shadow: none;
        box-shadow: none;
    }
    .el-dialog--center .el-dialog__body{
        padding: 10px 25px 0px;
    }
    .el-form-item{
        margin-bottom:5px;
    }
    .info{
        .image{
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-block;
            vertical-align: top;
        }
        .info-content{
            display: inline-block;
            .username{
                margin:0;
                font-size: 13px;
                line-height: 20px;
            }
            .nickname{
                font-size: 12px;
                margin:0;
                line-height: 25px;
            }
        }
    }
    .search{
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #fff;
        padding: 10px;
        ::v-deep .el-input--medium .el-input__inner{
            height: 30px;
            line-height: 30px;
        }
        ::v-deep .el-input--medium .el-input__icon {
            line-height: 30px;
        }
        .input-with-select{
            border:0;
            border-radius: 4px;
            font-size: 12px;
            color:#a2a2a2;
        }
        .searchData{
            .card{
                padding: 5px;
                margin-top: 10px;
                .image{
                    border-radius: 50%;
                    width: 40px;
                    height: 40px;
                }
                .username{
                    margin:0;
                    font-size: 13px;
                }
                .nickname{
                    font-size: 12px;
                    margin:0;
                    line-height: 25px;
                }
            }
        }
    }
    .bg-purple-dark{
        background: #d3dce6;
        border-radius: 4px;
        width: 100%;
        display: inline-block;
        position: relative;
        font-size: 12px;
        height: 30px;
        line-height: 30px;
        padding: 0px 10px;
        color:#a2a2a2;
    }

    .apply-info{
        border-bottom: 1px solid #eee;
        padding: 5px 0px;
        overflow: hidden;
        img{
            vertical-align: top;
            width: 30px;
            height: 30px;
            float: left;
        }
        .apply-content{
            margin-left: 5px;
            float: left;
            width:100px;
            h5{
                margin:0;
                font-size: 12px;
                font-weight: normal;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                word-wrap: break-word;
                word-break: break-all;
                display: block;
                color:#333333;
            }
            .remark{
                margin:0;
                font-size: 12px;
                transform: scale(0.80) ;
                -webkit-transform-origin-x: 0;
                -webkit-transform: scale(0.80);
                *font-size: 10px;
                overflow: hidden;
                text-overflow: ellipsis;
                -o-text-overflow: ellipsis;
                white-space:nowrap;
                word-wrap: break-word;
                word-break: break-all;
                display: block;
                width:120%;
                margin-top:2px;
                color:#a2a2a2;
            }
        }
        .apply-btn{
            float: left;
            margin-top:4px;
            button{
                text-align: center;
                background: #13ce66;
                color:#ffffff;
                display: inline-block;
                line-height: 1;
                white-space: nowrap;
                cursor: pointer;
                border-radius: 3px;
                outline: none;
                -webkit-appearance:none;
                font-size: 12px;
                padding: 5px 5px;
                border:none;
            }
            .apply-status{
                color:#aaaaaa;
                font-size: 12px;
                transform: scale(0.80) ;
                -webkit-transform-origin-x: 0;
                -webkit-transform: scale(0.80);
                *font-size: 10px;
            }
        }
    }
    .friend-info{
        border-bottom: 1px solid #eee;
        padding: 5px 0px;
        overflow: hidden;
        img{
            vertical-align: top;
            width: 30px;
            height: 30px;
            float: left;
        }
        .friend-content{
            margin-left: 5px;
            float: left;
            width:140px;
            h5{
                margin:0;
                font-size: 12px;
                font-weight: normal;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                word-wrap: break-word;
                word-break: break-all;
                display: block;
                color:#333333;
            }
            .sign{
                margin:0;
                font-size: 12px;
                transform: scale(0.80) ;
                -webkit-transform-origin-x: 0;
                -webkit-transform: scale(0.80);
                *font-size: 10px;
                overflow: hidden;
                text-overflow: ellipsis;
                -o-text-overflow: ellipsis;
                white-space:nowrap;
                word-wrap: break-word;
                word-break: break-all;
                display: block;
                width:120%;
                margin-top:2px;
                color:#a2a2a2;
            }
        }
    }
    ::v-deep .el-dialog.el-dialog--center.chat-box{
        height: 500px;
        ::-webkit-scrollbar
        {
            width: 10px;
            height: 16px;
            background-color: #FFFFFF;
        }
        ::-webkit-scrollbar-track
        {
            -webkit-box-shadow: inset 0 0 6px #ffffff;
            background-color: #FFFFFF;
        }
        ::-webkit-scrollbar-thumb
        {
            border-radius: 10px;
            background-color: #c1c1c1;
        }
        .el-dialog__header{
            background: #80c7ed;
        }
        .el-dialog__body{
            height: 300px;
            padding: 10px;
            overflow-y: auto;
            .chat-content{
                .chat-cleft{
                    text-align: left;
                    margin-top:15px;
                    .chat-time{
                        text-align: center;
                        color: #a1a1a1;
                    }
                    .chat-info{
                        img{
                            width: 35px;
                            height: 35px;
                            border-radius: 50%;
                            vertical-align: top;
                        }
                        .chat-text{
                            display: inline-block;
                            margin-left: 5px;
                            .chat-name{
                                color: #a0a0a0;
                            }
                            .chat-message{
                                border: 1px solid;
                                padding: 5px;
                                border-radius: 5px;
                                max-width: 300px;
                                margin-top:2px;
                                display: inline-block;
                            }
                        }
                    }
                }
                .chat-cright{
                    text-align: right;
                    margin-top:15px;
                    .chat-time{
                        text-align: center;
                        color: #a1a1a1;
                    }
                    .chat-info{
                        img{
                            width: 35px;
                            height: 35px;
                            border-radius: 50%;
                            vertical-align: top;
                        }
                        .chat-text{
                            display: inline-block;
                            margin-right: 5px;
                            .chat-name{
                                color: #a0a0a0;
                            }
                            .chat-message{
                                border: 1px solid;
                                padding: 5px;
                                border-radius: 5px;
                                max-width: 300px;
                                margin-top:2px;
                                display: inline-block;
                            }
                        }
                    }
                }
            }
        }
        .el-dialog__footer{
            border-top:1px solid #aaaaaa;
            position: relative;
            .dialog-footer{
                display: inline-block;
                height: 135px;
                width: 100%;
            }
            .chat-msg{
                border: 0;
                height: 95px;
                width: 100%;
                resize: none;
                outline: none;
                font-size: 14px;
            }
            .chat-btn{
                width: 100%;
                height: 20px;
                text-align: right;
            }
            ::-webkit-scrollbar
            {
                width: 10px;
                height: 16px;
                background-color: #FFFFFF;
            }
            ::-webkit-scrollbar-track
            {
                -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
                border-radius: 10px;
                background-color: #FFFFFF;
            }
            ::-webkit-scrollbar-thumb
            {
                border-radius: 10px;
                -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
                background-color: #80c7ed;
            }
        }
    }
</style>
