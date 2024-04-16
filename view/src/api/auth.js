import request from '@/utils/request'

//获取角色列表
export function getList(query) {
  return request({
    url:'/auth/getList',
    method:'get',
    params:query
  })
}

//获取二级权限树
export function getSecondRules() {
  return request({
    url:'/auth/getSecondRules',
    method:'get',
  })
}

//新增权限
export function addRule(data) {
  return request({
    url:'/auth/addRule',
    method:'post',
    data
  })
}

//获取权限
export function getInfo(query) {
  return request({
    url:'/auth/getInfo',
    method:'get',
    params:query
  })
}

//编辑权限
export function editRule(data) {
  return request({
    url:'/auth/editRule',
    method:'post',
    data
  })
}

//删除权限
export function deleteRule(data) {
  return request({
    url:'/auth/deleteRule',
    method:'post',
    data
  })
}

