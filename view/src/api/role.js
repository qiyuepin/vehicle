import request from '@/utils/request'

//获取角色列表
export function getList(query) {
  return request({
    url:'/role/getList',
    method:'get',
    params:query
  })
}

//获取角色
export function getInfo(query) {
  return request({
    url:'/role/getInfo',
    method:'get',
    params:query
  })
}

//获取权限树列表
export function getRules() {
  return request({
    url:'/auth/getRules',
    method:'get'
  })
}

//新增角色
export function addRole(data) {
  return request({
    url:'/role/addRole',
    method:'post',
    data
  })
}

//编辑角色
export function editRole(data) {
  return request({
    url:'/role/editRole',
    method:'post',
    data
  })
}

//更改角色状态
export function changeStatus(data) {
  return request({
    url:'/role/changeStatus',
    method:'post',
    data
  })
}

//删除角色
export function deleteRole(data) {
  return request({
    url:'/role/deleteRole',
    method:'post',
    data
  })
}
