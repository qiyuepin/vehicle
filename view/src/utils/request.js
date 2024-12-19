import axios from 'axios'
import { MessageBox, Message } from 'element-ui'
import store from '@/store'
import { getToken, setToken } from '@/utils/auth'
import defaultSettings from '@/settings.js'
import { getSignParam } from './index'

// create an axios instance
const service = axios.create({
    baseURL: process.env.VUE_APP_BASE_URL + process.env.VUE_APP_BASE_API, // url = base url + request url
    // withCredentials: true, // send cookies when cross-domain requests
    timeout: 50000 // request timeout
})

// request interceptor
service.interceptors.request.use(
    config => {
        // do something before request is sent
        config.headers['X-Access-Appid'] = defaultSettings.appid
        if (store.getters.token) {
            config.headers['Authorization'] = getToken()
        }
        if (defaultSettings.whiteList.indexOf(process.env.VUE_APP_BASE_API + config.url) === -1) {
            if (config.method === 'post') {
                config.data = getSignParam(config.data)
            }
            if (config.method === 'get') {
                config.params = getSignParam(config.params)
            }
        }
        return config
    },
    error => {
        // do something with request error
        console.log(error) // for debug
        return Promise.reject(error)
    }
)

// response interceptor
service.interceptors.response.use(
    /**
     * If you want to get http information such as headers or status
     * Please return  response => response
     */

    /**
     * Determine the request status by custom code
     * Here is just an example
     * You can also judge the status by HTTP Status Code
     */
    response => {
        if (response.status === 200) {
            const res = response.data
            if (res.code) {
                if (res.code !== 200) {
                    if (res.code === 401) {
                        MessageBox.alert('登录已过期，请重新登录', '温馨提示', {
                            confirmButtonText: '确定',
                            callback: action => {
                                store.dispatch('user/resetToken').then(() => {
                                    location.reload()
                                })
                            }
                        })
                    } else if (res.code === 405) {
                        return refreshToken(response.config)
                    } else if(res.code === 400){
                        Message({
                            message: res.message || defaultSettings.errorMsg,
                            type: 'warning',
                            duration: 5 * 1000
                        })
                        return Promise.reject(new Error(res.message || 'Warn'))
                    }else {
                        Message({
                            message: res.message || defaultSettings.errorMsg,
                            type: 'error',
                            duration: 5 * 1000
                        })
                        return Promise.reject(new Error(res.message || 'Warn'))
                    }
                } else {
                    return res.data
                }
            }
        } else {
            return response
        }
    },
    error => {
        console.log(error.message)
        Message({
            message: error.message,
            type: 'error',
            duration: 5 * 1000
        })
        return Promise.reject(error)
    }
)

export function refreshToken(config) {
    axios.get(process.env.VUE_APP_BASE_URL + process.env.VUE_APP_BASE_API + '/auth/freshToken?fresh_token=' + localStorage.getItem('refresh_token'), {
        headers: {
            'Content-Type': 'multipart/form-data',
            'X-Access-Appid': defaultSettings.appid
        }
    }).then(response => {
        const res = response.data
        if (res.code === 200) {
            store.dispatch('user/setToken', res.data.token)
            setToken(res.data.token)
            return service(config)
        } else {
            store.dispatch('user/resetToken')
            return Promise.reject({ type: 'warning', msg: res.message }).catch(() => {
            })
        }
    })
}

export default service
