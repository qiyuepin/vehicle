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
