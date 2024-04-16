<template>
    <div>
        <el-row class="chatButton">
            <el-badge :value="chatCount" :hidden="chatCount<1" :max="99" class="item">
                <el-button type="success" title="聊天" @click="openList" icon="el-icon-s-comment" circle></el-button>
            </el-badge>
        </el-row>
        <chatList ref="chatAttr" @bindCount="bindCount"/>
    </div>
</template>

<script>
import { Message, MessageBox } from "element-ui";
import defaultSettings from '@/settings.js'
import { getToken } from '@/utils/auth'
import chatList from '@/components/Chat/chat-list'
import store from '@/store'
export default {
  name: 'hover-btn',
  components: {
    chatList
  },
  data(){
    return {
      chatCount:0
    }
  },
  created() {
    this.$socket.emit('login', {
      'token': getToken()
    }, function () {
      return true;
    });
  },
  sockets: {
    open(res) {
      if (!res.status) {
        Message({
          message: res.message || defaultSettings.errorMsg,
          type: 'error',
          duration: 5 * 1000
        })
      }
    },

    disconnect() {
      if(getToken()){
        this.$socket.emit('login', {
          'token': getToken()
        }, function () {
          return true;
        });
      }
    },

    chat(res){
      let data = res.data;
      if (res.status) {
        this.$store.dispatch('chat/changeChatItem', {
          id: this.$store.getters.uid,
          msg: data.message,
          count:this.$store.getters.uid == data.touid?data.count.message_count:0,
          time: data.time
        })
        if(this.$store.getters.uid == data.touid){
          this.chatCount = data.count.total
          this.$store.dispatch('chat/changeCount',data.count.total)
        }
        this.$refs.chatAttr.updateList(data)
      }
    },

    connect() {
      console.log('连接成功')
    },

    login(res) {
      if (!res.status) {
        MessageBox.alert('登录已过期，请重新登录', '温馨提示', {
          confirmButtonText: '确定',
          callback: action => {
            store.dispatch('user/resetToken').then(() => {
              location.reload()
            })
          }
        });
      }else{
        this.$store.dispatch('chat/changeCount',res.data.count)
        this.$store.dispatch('chat/setChatList',res.data.messages)
        this.chatCount = res.data.count
        this.$refs.chatAttr.initChatList(res.data.messages)
      }

    },
  },
  methods: {
    openList() {
      this.$refs.chatAttr.showForm()
    },
    bindCount(touid,count){
      this.chatCount = this.chatCount-count
      this.$store.dispatch('chat/changeCount',this.chatCount)
      this.$socket.emit('tips', {
        'to': touid,
      });//离线消息
    }
  }
}
</script>

<style scoped>
    .chatButton {
        position: fixed;
        bottom: 60px;
        right: 20px;
    }
</style>
