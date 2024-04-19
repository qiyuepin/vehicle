import request from '@/utils/request'

// export function captcha() {
//   return request({
//     url: '/getCaptcha',
//     method: 'get'
//   })
// }

export function login(data) {
  return request({
    url: '/auth/login',
    method: 'post',
    data
  })
}

export function getInfo() {
  return request({
    url: '/user/info',
    method: 'get'
  })
}

export function logout() {
  return request({
    url: '/user/logout',
    method: 'post'
  })
}

export function saveInfo(data) {
  return request({
    url:'/user/saveInfo',
    method:'post',
    data
  })
}

export function updatePass(data) {
  return request({
    url: '/user/updatePass',
    method: 'post',
    data
  })
}

export function author() {
  return request({
    url: '/user/author',
    method: 'get'
  })
}

export function updateAuthor(data) {
  return request({
    url: '/user/updateAuthor',
    method: 'post',
    data
  })
}
