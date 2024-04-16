import { constantRoutes } from '@/router'
import { loadView } from '@/utils';
import Layout from '@/layout'

/**
 * Use meta.role to determine if the current user has permission
 * @param roles
 * @param route
 */
function hasPermission(roles, route) {
  if (route.meta && route.meta.roles) {
    return roles.some(role => route.meta.roles.includes(role))
  } else {
    return true
  }
}

/**
 * Filter asynchronous routing tables by recursion
 * @param routes asyncRoutes
 * @param roles
 */
export function filterAsyncRoutes(routes, roles) {
  const res = []

  routes.forEach(route => {
    const tmp = { ...route }
    if (hasPermission(roles, tmp)) {
      if (tmp.children) {
        tmp.children = filterAsyncRoutes(tmp.children, roles)
      }
      res.push(tmp)
    }
  })

  return res
}

const state = {
  routes: [],
  buttons: [],
  addRoutes: []
}

const mutations = {
  SET_ROUTES: (state, routes) => {
    state.addRoutes = routes
    state.routes = constantRoutes.concat(routes)
  },
  SET_BUTTONS: (state, buttons) => {
    state.buttons = buttons
  }
}

const actions = {
  generateRoutes({ commit }, data) {
    return new Promise(resolve => {
      const accessedRouters = []
      const accessedButtons = []
      data.map(function (item) {
        if (item.menu === 1) {
          const access = {}
          access.path = item.path
          if(item.always_show){
            access.name = item.name
            access.meta = { 'icon': item.icon, 'title': item.title }
          }
          access.alwaysShow = item.always_show
          if(item.redirect){
            access.redirect = item.redirect
          }
          if (item.component === 'layout') {
            access.component = Layout;
          } else {
            access.component = loadView(item.component);
          }
          if (item.children) {
            const childList = []
            item.children.map(function (child) {
              if (child.menu === 1) {
                const child_access = {}
                child_access.path = child.path
                child_access.name = child.name
                child_access.meta = { 'icon': child.icon, 'title': child.title }
                child_access.alwaysShow = child.always_show
                if(child_access.redirect){
                  child_access.redirect = child.redirect
                }
                if (child.component === 'layout') {
                  child_access.component = Layout
                } else {
                  child_access.component = loadView(child.component);
                }
                if (child.children) {
                  const childList1 = []
                  child.children.map(function (children) {
                    if (children.menu === 1) {
                      console.log(children)
                      const children_access = {}
                      children_access.path = children.path
                      children_access.name = children.name
                      children_access.meta = { 'icon': children.icon, 'title': children.title }
                      children_access.alwaysShow = children.always_show
                      if(children_access.redirect){
                        children_access.redirect = children.redirect
                      }
                      children_access.component = loadView(children.component);
                      if (children.children) {
                        const childList2 = []
                        children.children.map(function (buttons) {
                          if(buttons.menu==1){
                            const buttons_access = {}
                            buttons_access.path = buttons.path
                            buttons_access.name = buttons.name
                            buttons_access.meta = { 'icon': buttons.icon, 'title': buttons.title }
                            buttons_access.alwaysShow = buttons.always_show
                            if(buttons.redirect){
                              buttons_access.redirect = buttons.redirect
                            }
                            buttons_access.component = loadView(buttons.component);
                            childList2.push(buttons_access)
                          }else{
                            accessedButtons.push(buttons.name)
                          }
                        })
                        if(childList2.length>0){
                          children_access.children = childList2
                        }
                      }
                      childList1.push(children_access)
                    }else{
                      accessedButtons.push(children.name)
                    }
                  })
                  if(childList1.length>0){
                    child_access.children = childList1
                  }
                }
                console.log(child_access)
                childList.push(child_access)
              }else{
                accessedButtons.push(child.name)
              }
            })
            if(childList.length>0){
              access.children = childList;
            }
          }
          accessedRouters.push(access)
        }
      })
      console.log(accessedRouters);
      accessedRouters.push({ path: '*', redirect: '/404', hidden: true })
      commit('SET_ROUTES', accessedRouters)
      commit('SET_BUTTONS',accessedButtons)//按钮权限
      resolve(accessedRouters)
    })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
