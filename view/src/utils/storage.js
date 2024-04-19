// 存储数据
export const setItem = (key, value) => {
  // 将数组、对象类型的数据转换为 JSON 格式字符串进行存储
  if (typeof value === 'object') {
    value = JSON.stringify(value)
  }
  window.localStorage.setItem(key, value)
}

// 获取数据
export const getItem = key => {
  const data = window.localStorage.getItem(key)
  try {
    return JSON.parse(data)
  } catch (err) {
    return data
  }
}

// 删除数据
export const removeItem = key => {
  window.localStorage.removeItem(key)
}

export const setChat = (value,time) => {
  let chatArr = getItem('chatList') || [];
  let isHas = false
  for(var i=0;i<chatArr.length;i++){
    if(chatArr[i]['id']==value.id){
      isHas = true
      chatArr[i]['count'] = 0
    }
  }
  if(!isHas){
    let chat = {
      id:value.id,
      // nickname:value.remark || value.nickname,
      username:value.username,
      avatar:value.avatar,
      type:1,
      last_msg:'',
      count:0,
      time:time
    }
    chatArr.unshift(chat)
  }
  setItem('chatList',chatArr)
}

export const updateChatTime = (id,msg,time) => {
  let chatArr = getItem('chatList') || [];
  for(var i=0;i<chatArr.length;i++){
    if(chatArr[i]['id']==id){
      chatArr[i]['time'] = time
      chatArr[i]['last_msg'] = msg
    }
  }
  setItem('chatList',chatArr)
}
