import request from '@/utils/request'

export function getplaninfo(query) {
  return request({
    url:'/plan/getplaninfo',
    method:'get',
    params:query
  })
}
export function getnormal(query) {
  return request({
    url:'/plan/getnormal',
    method:'get',
    params:query
  })
}
export function getnormalinfo(query) {
  return request({
    url:'/plan/getnormalinfo',
    method:'get',
    params:query
  })
}
export function getnormalplan(query) {
  return request({
    url:'/plan/getnormalplan',
    method:'get',
    params:query
  })
}
export function addnormal(data) {
  return request({
    url:'/plan/addnormal',
    method:'post',
    data
  })
}
export function editnormal(data) {
  return request({
    url:'/plan/editnormal',
    method:'post',
    data
  })
}
export function delnormal(data) {
  return request({
    url:'/plan/delnormal',
    method:'post',
    data
  })
}
export function gettemporary(query) {
  return request({
    url:'/plan/gettemporary',
    method:'get',
    params:query
  })
}
export function gettemporaryplan(query) {
  return request({
    url:'/plan/gettemporaryplan',
    method:'get',
    params:query
  })
}
export function addtemporary(data) {
  return request({
    url:'/plan/addtemporary',
    method:'post',
    data
  })
}
export function edittemporary(data) {
  return request({
    url:'/plan/edittemporary',
    method:'post',
    data
  })
}
export function gettemporaryinfo(query) {
  return request({
    url:'/plan/gettemporaryinfo',
    method:'get',
    params:query
  })
}
export function deltemporary(data) {
  return request({
    url:'/plan/deltemporary',
    method:'post',
    data
  })
}
export function getplans(query) {
  return request({
    url:'/plan/getplans',
    method:'get',
    params:query
  })
}
export function getplansinfo(query) {
  return request({
    url:'/plan/getplansinfo',
    method:'get',
    params:query
  })
}
export function addplan(data) {
  return request({
    url:'/plan/addplan',
    method:'post',
    data
  })
}
export function editplan(data) {
  return request({
    url:'/plan/editplan',
    method:'post',
    data
  })
}
export function distplan(data) {
  return request({
    url:'/plan/distplan',
    method:'post',
    data
  })
}
export function delplan(data) {
  return request({
    url:'/plan/delnplan',
    method:'post',
    data
  })
}

