import request from '@/utils/request'

//获取角色列表
export function getUser(query) {
  return request({
    url:'/chat/getUser',
    method:'get',
    params:query
  })
}

//添加朋友
export function addFriend(data) {
  return request({
    url:'/chat/addFriend',
    method:'post',
    data
  })
}

//获取申请列表
export function getApplyList(query) {
  return request({
    url:'/chat/getApplyList',
    method:'get',
    params:query
  })
}

//同意好友申请
export function agreeFriendApply(data) {
  return request({
    url:'/chat/agreeFriendApply',
    method:'post',
    data
  })
}

//获取好友列表
export function getFriendList(query) {
  return request({
    url:'/chat/getFriendList',
    method:'get',
    params:query
  })
}

//获取历史消息
export function getHistoryMsg(query) {
  return request({
    url:'/chat/getHistoryMsg',
    method:'get',
    params:query
  })
}

//获取会话消息
export function getSessionList(query) {
  return request({
    url:'/chat/getSessionList',
    method:'get',
    params:query
  })
}


