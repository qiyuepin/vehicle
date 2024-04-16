import store from '@/store'
import moment from 'moment'

const state = {
  count:0,
  chatList:[]
}

const mutations = {
  SET_COUNT: (state,count) => {
    state.count = count
  },
  SET_CHATLIST: (state,data) => {
    state.chatList = data
  }
}

const actions = {
  changeCount({ commit }, count) {
    commit('SET_COUNT', count)
  },
  setChatList({ commit }, data) {
    console.log(data)
    commit('SET_CHATLIST', data)
  },
  RESET_CHAT({ commit }) {
    commit('SET_COUNT', 0)
    commit('SET_CHATLIST', [])
  },
  changeChatList({ commit }, value){
    let chatArr = store.getters.chatList || []
    let isHas = false
    let time = moment().format('YYYY-MM-DD HH:mm:ss')
    for(var i=0;i<chatArr.length;i++){
      if(chatArr[i]['id']==value.id){
        isHas = true
        chatArr[i]['count'] = 0
      }
    }
    if(!isHas){
      let chat = {
        id:value.id,
        nickname:value.remark || value.nickname,
        username:value.username,
        avatar:value.avatar,
        type:1,
        last_msg:'',
        count:0,
        time:time
      }
      chatArr.unshift(chat)
    }
    commit('SET_CHATLIST', chatArr)
  },
  changeChatItem({ commit },{id,msg,count,time}){
    let chatArr = store.getters.chatList || []
    for(var i=0;i<chatArr.length;i++){
      if(chatArr[i]['id']==id){
        chatArr[i]['time'] = time
        chatArr[i]['last_msg'] = msg
        chatArr[i]['last_msg'] = count
      }
    }
    commit('SET_CHATLIST', chatArr)
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
