import request from '@/utils/request'

export function getcarscope(query) {
  return request({
    url:'/info/getcarscope',
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
export function getcartrailer(query) {
  return request({
    url:'/info/getcartrailer',
    method:'get',
    params:query
  })
}
export function getcartrailerInfo(query) {
  return request({
    url:'/info/getcartrailerInfo',
    method:'get',
    params:query
  })
}
export function addcartrailer(data) {
  return request({
    url:'/info/addcartrailer',
    method:'post',
    data
  })
}
export function editcartrailer(data) {
  return request({
    url:'/info/editcartrailer',
    method:'post',
    data
  })
}
export function delcartrailer(data) {
  return request({
    url:'/info/delcartrailer',
    method:'post',
    data
  })
}
export function getescort(query) {
  return request({
    url:'/info/getescort',
    method:'get',
    params:query
  })
}
export function getescortInfo(query) {
  return request({
    url:'/info/getescortInfo',
    method:'get',
    params:query
  })
}
export function addescort(data) {
  return request({
    url:'/info/addescort',
    method:'post',
    data
  })
}
export function editescort(data) {
  return request({
    url:'/info/editescort',
    method:'post',
    data
  })
}
export function delescort(data) {
  return request({
    url:'/info/delescort',
    method:'post',
    data
  })
}
export function getinfolist(query) {
  return request({
    url:'/info/getinfolist',
    method:'get',
    params:query
  })
}
export function getinfo(query) {
  return request({
    url:'/info/getinfo',
    method:'get',
    params:query
  })
}
export function addinfo(data) {
  return request({
    url:'/info/addinfo',
    method:'post',
    data
  })
}
export function editinfo(data) {
  return request({
    url:'/info/editinfo',
    method:'post',
    data
  })
}
export function delinfo(data) {
  return request({
    url:'/info/delinfo',
    method:'post',
    data
  })
}
export function getcarlist(query) {
  return request({
    url:'/info/getcarlist',
    method:'get',
    params:query
  })
}
export function getfactory(query) {
  return request({
    url:'/info/getfactory',
    method:'get',
    params:query
  })
}
export function getfactoryinfo(query) {
  return request({
    url:'/info/getfactoryinfo',
    method:'get',
    params:query
  })
}
export function addfactory(data) {
  return request({
    url:'/info/addfactory',
    method:'post',
    data
  })
}
export function editfactory(data) {
  return request({
    url:'/info/editfactory',
    method:'post',
    data
  })
}
export function delfactory(data) {
  return request({
    url:'/info/delfactory',
    method:'post',
    data
  })
}
