import request from '@/utils/request'

//获取管理员列表
export function getcost(query) {
  return request({
    url:'/cost/getcost',
    method:'get',
    params:query
  })
}

export function changeStatus(data) {
  return request({
    url:'/cost/change',
    method:'post',
    data
  })
}

export function addcost(data) {
  return request({
    url:'/cost/addcost',
    method:'post',
    data
  })
}

export function editcost(data) {
  return request({
    url:'/cost/editcost',
    method:'post',
    data
  })
}

export function getinfo(query) {
  return request({
    url:'/cost/getinfo',
    method:'get',
    params:query
  })
}

export function delcost(data) {
  return request({
    url:'/cost/delcost',
    method:'post',
    data
  })
}

