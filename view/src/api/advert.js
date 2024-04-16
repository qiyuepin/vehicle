import request from '@/utils/request'

//获取管理员列表
export function getList(query) {
  return request({
    url:'/advert/list',
    method:'get',
    params:query
  })
}

export function changeStatus(data) {
  return request({
    url:'/advert/change',
    method:'post',
    data
  })
}

export function addAdvert(data) {
  return request({
    url:'/advert/add',
    method:'post',
    data
  })
}

export function editAdvert(data) {
  return request({
    url:'/advert/edit',
    method:'post',
    data
  })
}

export function getInfo(query) {
  return request({
    url:'/advert/info',
    method:'get',
    params:query
  })
}

export function deleteAdvert(data) {
  return request({
    url:'/advert/delete',
    method:'post',
    data
  })
}

