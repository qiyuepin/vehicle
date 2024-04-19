import request from '@/utils/request'

//获取管理员列表
export function getList(query) {
  return request({
    url:'/admin/getList',
    method:'get',
    params:query
  })
}
export function getdriverList(query) {
  return request({
    url:'/admin/getdriverList',
    method:'get',
    params:query
  })
}
export function regulation(query) {
  return request({
    url:'/driver/regulation',
    method:'get',
    params:query
  })
}
export function getregulation(query) {
  return request({
    url:'/driver/getregulation',
    method:'get',
    params:query
  })
}
export function getregulationInfo(query) {
  return request({
    url:'/driver/getregulationInfo',
    method:'get',
    params:query
  })
}
export function addregulation(data) {
  return request({
    url:'/driver/addregulation',
    method:'post',
    data
  })
}
export function editregulation(data) {
  return request({
    url:'/driver/editregulation',
    method:'post',
    data
  })
}
export function delregulation(data) {
  return request({
    url:'/driver/delregulation',
    method:'post',
    data
  })
}
export function getaccident(query) {
  return request({
    url:'/driver/getaccident',
    method:'get',
    params:query
  })
}
export function changeStatus(data) {
  return request({
    url:'/admin/changeStatus',
    method:'post',
    data
  })
}

export function getRole() {
  return request({
    url:'/admin/getRole',
    method:'get'
  })
}

export function addAdmin(data) {
  return request({
    url:'/admin/addAdmin',
    method:'post',
    data
  })
}

export function editAdmin(data) {
  return request({
    url:'/admin/editAdmin',
    method:'post',
    data
  })
}
export function editdriverAdmin(data) {
  return request({
    url:'/admin/editdriverAdmin',
    method:'post',
    data
  })
}
export function adddriverAdmin(data) {
  return request({
    url:'/admin/adddriverAdmin',
    method:'post',
    data
  })
}
export function getInfo(query) {
  return request({
    url:'/admin/getInfo',
    method:'get',
    params:query
  })
}

export function getdriverInfo(query) {
  return request({
    url:'/admin/getdriverInfo',
    method:'get',
    params:query
  })
}

export function deleteAdmin(data) {
  return request({
    url:'/admin/deleteAdmin',
    method:'post',
    data
  })
}

export function getLoginLog(query) {
  return request({
    url:'/admin/getLoginLog',
    method:'get',
    params:query
  })
}

export function getHandleLog(query) {
  return request({
    url:'/admin/getHandleLog',
    method:'get',
    params:query
  })
}

export function getcarhead(query) {
  return request({
    url:'/info/getcarhead',
    method:'get',
    params:query
  })
}
export function getcarheadInfo(query) {
  return request({
    url:'/info/getcarheadInfo',
    method:'get',
    params:query
  })
}
export function addcarhead(data) {
  return request({
    url:'/info/addcarhead',
    method:'post',
    data
  })
}
export function editcarhead(data) {
  return request({
    url:'/info/editcarhead',
    method:'post',
    data
  })
}
export function delcarhead(data) {
  return request({
    url:'/info/delcarhead',
    method:'post',
    data
  })
}
