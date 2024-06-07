import { login, logout, getInfo } from '@/api/user'
import { getToken, setToken, removeToken } from '@/utils/auth'
import router, { resetRouter } from '@/router'
import store from '@/store'
import Vue from 'vue'
import socket from "socket.io-client"
const getDefaultState = () => {
  return {
    token: getToken(),
    refresh_token: localStorage.getItem('refresh_token'),
    uid:'',
    name: '',
    avatar: '',
    phone: '',
    username: '',
    // email: '',
    signature: '',
    roles:[]
  }
}

const state = getDefaultState()

const mutations = {
    SAVE_REFRESH_TOKEN: (state, refresh_token) => {
        state.refresh_token = refresh_token
    },
  RESET_STATE: (state) => {
    Object.assign(state, getDefaultState())
  },
    SET_TOKEN: (state, token) => {
        Vue.set(state, 'token', token)
    },
  SET_NAME: (state, name) => {
    Vue.set(state,'name',name)
  },
  SET_AVATAR: (state, avatar) => {
    Vue.set(state,'avatar',avatar)
  },
  SET_PHONE: (state, phone) => {
    Vue.set(state,'phone',phone)
  },
  // SET_EMAIL: (state, email) => {
  //   Vue.set(state,'email',email)
  // },
  SET_USERNAME: (state, username) => {
    Vue.set(state,'username',username)
  },
  SET_SIGNATURE: (state, signature) => {
    Vue.set(state,'signature',signature)
  },
  SET_ROLES: (state, roles) => {
    Vue.set(state,'roles',roles)
  },
  SET_UID: (state, uid) => {
    Vue.set(state,'uid',uid)
  },
}

const actions = {
  // user login
  login({ commit,dispatch }, userInfo) {
    const { username, password, code, key } = userInfo
    return new Promise((resolve, reject) => {
      login({ username: username.trim(), password: password, code: code, key: key }).then(response => {
          if(response !== undefined){
              commit('SET_TOKEN', response.token)
              setToken(response.token)
              dispatch('saveRefreshToken', response.fresh_token)
          }
          resolve()
      }).catch(error => {
        reject(error)
      })
    })
  },

  // get user info
  getInfo({ commit, state }) {
    return new Promise((resolve, reject) => {
      getInfo(state.token).then(response => {
        // console.log("----"+response)
          if(response !== undefined){
              commit('SET_UID', response.id)
              commit('SET_NAME', response.nickname)
              commit('SET_AVATAR', response.avatar)
              commit('SET_PHONE',response.phone)
              commit('SET_USERNAME',response.username)
              // commit('SET_EMAIL',response.email)
              commit('SET_ROLES',response.roles)
              commit('SET_SIGNATURE',response.sign)
              resolve(response)
          }
      }).catch(error => {
        reject(error)
      })
    })
  },

  saveInfo({commit},data){
    console.log(data)
    commit('SET_NAME', data.nickname)
    commit('SET_AVATAR', data.avatar)
    commit('SET_PHONE',data.phone)
    // commit('SET_EMAIL',data.email)
    commit('SET_SIGNATURE',data.signature)
  },

  // user logout
  logout({ commit, state, dispatch }) {
    return new Promise((resolve, reject) => {
        logout({fresh_token:state.refresh_token}).then(() => {
            dispatch('resetToken')
            store.dispatch('chat/RESET_CHAT')
            dispatch('tagsView/delAllViews', null, { root: true })
            resolve()
        }).catch(_ => {
        })
    })
  },

  // remove token
  resetToken({ commit }) {
    return new Promise(resolve => {
        removeToken() // must remove  token  first
        commit('RESET_STATE')
        commit('SET_TOKEN', '')
        commit('SAVE_REFRESH_TOKEN', '')
        localStorage.removeItem('refresh_token')
      resolve()
    })
  },

  setToken({commit},token){
    commit('SET_TOKEN',token)
  },

saveRefreshToken({ commit }, refresh_token) {
    commit('SAVE_REFRESH_TOKEN', refresh_token)
    localStorage.setItem('refresh_token', refresh_token)
    console.log(refresh_token)
},

  // dynamically modify permissions
  async changeRoles({ commit, dispatch }, role) {
    const token = role + '-token'

    commit('SET_TOKEN', token)
    setToken(token)

    const { roles } = await dispatch('getInfo')

    resetRouter()

    // generate accessible routes map based on roles
    const accessRoutes = await dispatch('permission/generateRoutes', roles, { root: true })
    // dynamically add accessible routes
    router.addRoutes(accessRoutes)

    // reset visited views and cached views
    dispatch('tagsView/delAllViews', null, { root: true })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
