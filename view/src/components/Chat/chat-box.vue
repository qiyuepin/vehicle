<template>
    <el-dialog
            ref="dialog"
            :modal="false"
            :title="title"
            :visible.sync="dialogVisible"
            :width="width"
            custom-class="chat-box"
            :close-on-click-modal="false"
            :lock-scroll="false"
            :fullscreen="isFullScreen"
            :show-close="dialogShow"
            :minimize="isMiniMize"
            :modal-append-to-body="false"
            :destroy-on-close="true"
            v-if="dialogVisible"
            v-dialogDrag
            class="ZDialog"
            :class="isMiniMize ? 'isminimize' : (isFullScreen ? 'fullscreen' : '')"
            center>
        <div v-show="!isMiniMize" class="el-dialog-header" slot="title" style="cursor: move;">
            <span class="el-dialog__title" v-text="title"></span>
            <div class="el-dialog-header-btns">
                <button v-show="!isMiniMize" type="button" class="el-dialog__headerbtn" @click="minimize">
                    <i class="el-icon-minus"></i></button>
                <button type="button" class="el-dialog__headerbtn">
                    <i :class="isFullScreen?'el-icon-copy-document':'el-icon-full-screen'" @click="fullscreen"></i></button>
            </div>
        </div>
        <div v-show="isMiniMize" class="el-dialog-header" slot="title" style="cursor: move;">
            <span class="el-dialog__title" v-text="title" style="text-align: left;"></span>
            <div class="el-dialog-header-btns">
                <button type="button" class="el-dialog__headerbtn">
                    <i class="el-icon-copy-document" @click="resetscreen"></i></button>
                <button type="button" class="el-dialog__headerbtn">
                    <i class="el-icon-full-screen" @click="fullscreen"></i></button>
            </div>
        </div>
        <div class="chat-content" ref="chatContent" id="chat-content" v-show="!isMiniMize">
            <div style="height: 20px;text-align: center;color:#a1a1a1;" v-if="scrollSwitch">已经到顶啦</div>
            <div style="height: 30px" v-if="loading" v-loading="loading" element-loading-spinner="el-icon-loading"
                 element-loading-text="拼命加载中"></div>
            <div v-bind:class="item.position==1 ? 'chat-cleft' : 'chat-cright'" v-for="(item,index) of chatData"
                 :key="index">
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
        <span slot="footer" class="dialog-footer" v-show="!isMiniMize">
                    <textarea class="chat-msg" ref="message" v-model="content" @keydown.enter="enterMsg"></textarea>
                    <div class="chat-btn">
                        <el-popover
                                placement="top-end"
                                trigger="manual"
                                content="不能发送空白信息"
                                v-model="sendSwitch"
                                popper-class="popover-self">
                            <el-button slot="reference" type="primary" size="mini" @click="sendMsg">发 送</el-button>
                        </el-popover>
                    </div>
                </span>
    </el-dialog>
</template>

<script>
import { getHistoryMsg } from '@/api/chat.js'

export default {
  name: "chat-box",
  data() {
    return {
      title: '',
      dialogVisible: false,
      dialogShow:true,
      isFullScreen:false,
      isMiniMize:false,
      content: '',
      width:'600px',
      page: 1,
      limit: 10,
      totalPage: 1,
      chatData: [],
      loading: false,
      scrollSwitch: false,
      scrollType: 1,
      step: 20,
      sendSwitch: false,
      user: {
        id: '',
        nickname: '',
        avatar: ''
      }
    }
  },
  mounted() {
    window.addEventListener('scroll', this.handleScroll, true)
  },
  destroyed() {
    window.removeEventListener('scroll', this.handleScroll)
  },
  methods: {
    minimize(){
      this.isMiniMize = !this.isMiniMize
      if (this.isFullScreen) this.isFullScreen = !this.isFullScreen
    },
    fullscreen(){
      this.isFullScreen = !this.isFullScreen
      if (this.isMiniMize) this.isMiniMize = !this.isMiniMize
    },
    resetscreen(){
      this.isFullScreen = false
      if (this.isMiniMize) this.isMiniMize = !this.isMiniMize
    },
    toBottom() {
      this.$nextTick(function () {
        let ele = document.getElementsByClassName('el-dialog__body')[0];
        let distance = ele.scrollTop
        let scrollHeight = 0;
        let self = this
        if (this.scrollType == 1) {
          scrollHeight = ele.scrollHeight;
        } else {
          scrollHeight = 60;
        }

        function jump() {
          if (distance <= scrollHeight) {
            distance += self.step
            ele.scrollTo(0, distance)
            setTimeout(jump, 10)
          }
        }

        setTimeout(jump, 10)
      })
    },
    handleScroll(event) {
      if (!event.bubbles) {  //子组件滚动
        let scrollTop = event.target.scrollTop;
        if (scrollTop <= 0 && this.totalPage > this.page) {
          this.page += 1
          let self = this
          this.loading = true
          this.scrollType = 2
          setTimeout(function () {
            self.getHistoryMsg()
          }, 500)
        } else if (this.totalPage <= this.page) {
          this.scrollSwitch = true
        }
      }
    },
    addChat(data){
      let msg = {
        position: this.$store.getters.uid == data.sender.id ? 2 : 1,
        uid: data.sender.id,
        avatar: data.sender.avatar,
        name: data.sender.nickname,
        type: data.type,
        time: data.time,
        message: data.message
      }
      this.chatData.push(msg)
      this.toBottom()
    },
    show(item) {
      this.dialogVisible = true
      this.user = item
      this.title = item.remark ? item.remark : item.nickname
      this.page = 1
      this.scrollType = 1
      this.scrollSwitch = false
      this.totalPage = 1
      this.chatData = []
      this.getHistoryMsg()
      this.$nextTick(function () {
        this.$refs.message.focus();
      });
    },
    getHistoryMsg() {
      getHistoryMsg({ touid: this.user.id, page: this.page, limit: this.limit }).then(response => {
        this.totalPage = response.last_page
        let self = this
        response.data.map(function (item) {
          let msg = {
            position: self.$store.getters.uid == item.uid ? 2 : 1,
            uid: item.uid,
            avatar: self.$store.getters.uid == item.uid ? self.$store.getters.avatar : self.user.avatar,
            name: self.$store.getters.uid == item.uid ? self.$store.getters.name : self.user.nickname,
            type: item.type,
            time: item.create_time,
            message: item.message
          }
          self.chatData.unshift(msg);
        })
        this.loading = false
        this.toBottom();
      }).catch(() => {

      })
    },
    enterMsg(e) {
      if (!((e.keyCode == 13 && e.ctrlKey) || (e.keyCode == 13 && e.shiftKey))) {
        this.sendMsg();
        e.preventDefault();
      }
    },
    sendMsg() {
      this.sendSwitch = false
      if (this.content) {
        this.$socket.emit('chat', {
          'to': this.user.id,
          'type': 1,
          'message': this.content,
          'time': this.$moment().format('YYYY-MM-DD HH:mm:ss')
        });
        this.content = ''
        this.scrollType = 1
      } else {
        this.sendSwitch = true
        let self = this
        setTimeout(function () {
          self.sendSwitch = false
        }, 1500)
      }
      this.$nextTick(function () {
        this.$refs.message.focus();
      });
    },
  }
}
</script>
<style lang="scss">
    .popover-self.el-popover--plain {
        padding: 8px 10px;
    }

    .popover-self.el-popover {
        text-align: center;
        color: #333;
    }
    .ZDialog{
        pointer-events:none;
        .el-dialog{
            pointer-events:auto;
        }
        .el-dialog__header {
            padding: 15px;
            padding-bottom: 15px;
            .el-dialog-header-btns {
                position: absolute;
                top: 18px;
                right: 35px;

                .el-dialog__headerbtn {
                    position: static;
                    margin-right: 10px;
                    i {
                        color: #333333;
                    }
                }
            }
            .el-dialog__headerbtn {
                top: 18px;

                .el-dialog__close {
                    color: #333333;
                }
            }
        }
        .el-dialog__body{
            height: 300px;
        }
    }
    .ZDialog.fullscreen{
        .el-dialog__body{
            height: 500px;
        }
    }
    .ZDialog.isminimize {
        top: auto;
        right: auto;
        overflow: hidden;

        .el-dialog {
            margin: 0 !important;
            width: 200px !important;
            height: 43px;
            top: 0 !important;
            left: 0 !important;
        }
        .el-dialog__header {
            cursor: auto !important;
            text-align: left;
            padding:10px;
            .el-dialog__title{
                font-size: 14px;
                display: block;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                word-wrap: break-word;
                word-break: break-all;
            }
            .el-dialog-header-btns{
                top:13px;
            }
            .el-dialog__headerbtn {
                top: 13px;
            }
        }
        .el-dialog__body{
            display: none!important;
        }
        .el-dialog__footer{
            display: none;
        }
    }
</style>
<style scoped lang="scss">


    ::v-deep .el-dialog.el-dialog--center.chat-box {
        /*min-height: 500px;*/

        ::-webkit-scrollbar {
            width: 10px;
            height: 16px;
            background-color: #FFFFFF;
        }

        ::-webkit-scrollbar-track {
            -webkit-box-shadow: inset 0 0 6px #ffffff;
            background-color: #FFFFFF;
        }

        ::-webkit-scrollbar-thumb {
            border-radius: 10px;
            background-color: #c1c1c1;
        }

        .el-dialog__header {
            background: #80c7ed;
        }

        .el-dialog__body {
            /*height: 300px;*/
            padding: 10px;
            overflow-y: auto;

            .chat-content {
                .chat-cleft {
                    text-align: left;
                    margin-top: 15px;

                    .chat-time {
                        text-align: center;
                        color: #a1a1a1;
                    }

                    .chat-info {
                        img {
                            width: 35px;
                            height: 35px;
                            border-radius: 50%;
                            vertical-align: top;
                        }

                        .chat-text {
                            display: inline-block;
                            margin-left: 5px;

                            .chat-name {
                                color: #a0a0a0;
                            }

                            .chat-message {
                                border: 1px solid;
                                padding: 5px;
                                border-radius: 5px;
                                max-width: 300px;
                                margin-top: 2px;
                                display: inline-block;
                            }
                        }
                    }
                }

                .chat-cright {
                    text-align: right;
                    margin-top: 15px;

                    .chat-time {
                        text-align: center;
                        color: #a1a1a1;
                    }

                    .chat-info {
                        img {
                            width: 35px;
                            height: 35px;
                            border-radius: 50%;
                            vertical-align: top;
                        }

                        .chat-text {
                            display: inline-block;
                            margin-right: 5px;

                            .chat-name {
                                color: #a0a0a0;
                            }

                            .chat-message {
                                border: 1px solid;
                                padding: 5px;
                                border-radius: 5px;
                                max-width: 300px;
                                margin-top: 2px;
                                display: inline-block;
                            }
                        }
                    }
                }
            }
        }

        .el-dialog__footer {
            border-top: 1px solid #aaaaaa;
            position: relative;

            .dialog-footer {
                display: inline-block;
                height: 135px;
                width: 100%;
            }

            .chat-msg {
                border: 0;
                height: 100%;
                width: 100%;
                resize: none;
                outline: none;
                font-size: 14px;
            }

            .chat-btn {
                text-align: right;
                position: absolute;
                bottom: 10px;
                right: 10px;
            }

            ::-webkit-scrollbar {
                width: 10px;
                height: 16px;
                background-color: #FFFFFF;
            }

            ::-webkit-scrollbar-track {
                -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
                border-radius: 10px;
                background-color: #FFFFFF;
            }

            ::-webkit-scrollbar-thumb {
                border-radius: 10px;
                -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, .3);
                background-color: #80c7ed;
            }
        }
    }
</style>
