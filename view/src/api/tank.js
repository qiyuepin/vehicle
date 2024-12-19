import request from '@/utils/request'

export function gettank(query) {
  return request({
    url:'/tank/gettank',
    method:'get',
    params:query
  })
}
export function getlist(query) {
  return request({
    url:'/tank/getlist',
    method:'get',
    params:query
  })
}
export function getinfo(query) {
  return request({
    url:'/tank/getinfo',
    method:'get',
    params:query
  })
}
export function addtank(data) {
  return request({
    url:'/tank/add',
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
export function edit(data) {
  return request({
    url:'/tank/edit',
    method:'post',
    data
  })
}
export function del(data) {
  return request({
    url:'/tank/del',
    method:'post',
    data
  })
}
