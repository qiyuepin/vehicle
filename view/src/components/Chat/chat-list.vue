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
            :destroy-on-close="true"
            ref="drawer"
    >
        <div class="demo-drawer__content" style="padding: 10px">
            <div class="bg-purple-dark" @click="searchShow"><i class="el-icon-search"></i>&nbsp;搜索</div>
            <div v-show="isSearch" class="search">
                <el-input ref="search" prefix-icon="el-icon-search" @clear="isSearch=!isSearch" clearable placeholder="请输入用户名" @keypress.native.enter="searchUser" v-model="search" class="input-with-select">
                </el-input>
                <div @click="isSearch=!isSearch" style="height: 100%">
                    <div class="searchData"   v-if="searchRes">
                        <template v-if="searchRes===1">
                            <div class="card" @click.stop="showApply(item)" v-for="item of searchData" :key="item.id">
                                <div style="display: inline-block;vertical-align:top;">
                                    <img :src="item.avatar" class="image">
                                </div>
                                <div class="card-info">
                                    <h5 class="username" v-text="item.nickname"></h5>
                                    <p class="nickname" v-text="item.sign"></p>
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
                    <div class="message-info" v-for="(item,index) of chatData" :key="item.id" @click="openChat(item,1,index)">
                        <el-badge :value="item.count>10?'•••':item.count" :hidden="item.count<1" class="item">
                            <img :src="item.avatar" width="40" height="40" alt="">
                        </el-badge>
                        <div class="message-content">
                            <h5 v-text="item.remark?item.remark:item.nickname" :title="item.remark?item.remark:item.nickname"></h5>
                            <span class="sign" v-text="item.last_msg" :title="item.last_msg"></span>
                        </div>
                    </div>
                </el-tab-pane>
                <el-tab-pane label="好友">
                    <div class="friend-info" v-for="item of friendData" :key="item.id" @click="openChat(item,2)">
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
            <chatApply ref="chatApplyAttr" @send="addFriend" />
            <chatBox ref="chatBoxAttr"/>
        </div>
    </el-drawer>
</template>

<script>

import { getUser,addFriend,getApplyList,agreeFriendApply,getFriendList,getSessionList } from '@/api/chat.js'
import chatBox from './chat-box'
import chatApply from './chat-apply'
export default {
  name: "chat-list",
  components: {
    chatBox,
    chatApply
  },
  data() {
    return {
      search:'',
      searchRes:0,
      isSearch:false,
      dialog: false,
      drawerShow:false,
      applyRes:false,
      applyData:[],
      friendData:[],
      searchData:[],
      chatData:[]
    }
  },
  created() {
    this.getApplyList();
    this.getFriendList();
  },
  methods: {
    showApply(item){
      this.$refs.chatApplyAttr.show(item)
    },
    openChat(item,type,index){
      if(type==1){
        let count = this.chatData[index].count
        this.chatData[index].count = 0
        this.$emit('bindCount',item.id,count)
      }
      this.$store.dispatch('chat/changeChatList',item)
      this.$refs.chatBoxAttr.show(item)
    },
    getApplyList(){
      getApplyList().then(response => {
        this.applyData = response.data
      }).catch(() => {

      });
    },
    initChatList(messages){
        this.chatData = messages
    },
    getFriendList(){
      getFriendList().then(response => {
        this.friendData = response.data
      }).catch(() => {

      });
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
          this.getFriendList();
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
    updateList(data){
      let self = this
      if(this.$store.getters.uid == data.touid){
        this.chatData.map(function (item,index) {
          if(item.id==data.sender.id){
            self.chatData[index].last_msg = data.message
            if(self.$refs.chatBoxAttr==undefined){
              self.chatData[index].count = data.count.message_count
            }else{
              self.$emit('bindCount',item.id,data.count.message_count)
              self.$refs.chatBoxAttr.addChat(data)
            }
          }
        })
      }else{
        this.chatData.map(function (item,index) {
          if(item.id==data.touid){
            self.chatData[index].last_msg = data.message
          }
        })
        this.$refs.chatBoxAttr.addChat(data)
      }
    },
    searchUser(e){
      if (e.keyCode == 13&&this.search) {
        getUser({
          account:this.search
        }).then(response => {
          if(response.data.length>0){
            this.searchData = response.data
            this.searchRes = 1
          }else{
            this.searchRes = 2
          }
        })
      }
    },
    addFriend(item){
      addFriend(item).then(response=>{
        this.$message({
          message: '发送成功',
          type: 'success',
          duration: 5 * 1000
        })
      }).catch(() => {
      })
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
  }
}
</script>

<style scoped lang="scss">
    ::v-deep .el-tabs__item:focus.is-active.is-focus:not(:active) {
        -webkit-box-shadow: none;
        box-shadow: none;
    }
    ::v-deep .el-tabs__header{
        margin:0 0 5px;
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
                .card-info{
                    display: inline-block;
                    padding: 5px 10px;
                    width:120px;
                    .username{
                        margin:0;
                        font-size: 13px;
                        font-weight: normal;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        word-wrap: break-word;
                        word-break: break-all;
                        display: block;
                        color:#333333;
                    }
                    .nickname{
                        margin:0;
                        font-size: 12px;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        -o-text-overflow: ellipsis;
                        white-space:nowrap;
                        word-wrap: break-word;
                        word-break: break-all;
                        display: block;
                        margin-top:5px;
                        color:#a2a2a2;
                    }
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
    .message-info{
        border-bottom: 1px solid #eee;
        padding: 5px 0px;
        overflow: hidden;
        .el-badge{
            img{
                vertical-align: top;
                width: 30px;
                height: 30px;
            }
            float: left;
            ::v-deep .el-badge__content{
                height: 14px;
                line-height: 14px;
                padding: 0px 4px;
            }
            ::v-deep .el-badge__content.is-fixed{
                right: 6px;
            }
        }
        .message-content{
            margin-left: 10px;
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
</style>
