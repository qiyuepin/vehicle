/**
 * Created by PanJiaChen on 16/11/18.
 */

/**
 * @param {string} path
 * @returns {Boolean}
 */
export function isExternal(path) {
  return /^(https?:|mailto:|tel:)/.test(path)
}

/**
 * 验证用户名
 * @param str
 * @returns {*|boolean}
 */
export function validUsername(str) {
  const reg = /^[\u4e00-\u9fa5a-zA-Z0-9]{2,10}$/; //必须是2-10个字符
  console.log(reg.test(str))
  return reg.test(str);
}

/**
 * 验证身道路运输证
 * @param {string} str - 待验证的道路运输证
 * @returns {boolean} - 如果正确，返回 true；否则，返回 false
 */
export function validCert(str) {
  const reg = /^\d{12}$/;
  return reg.test(str);
}

/**
 * 验证车牌号
 * @param {string} str - 待验证的车牌号
 * @returns {boolean} - 如果正确，返回 true；否则，返回 false
 */
export function validPlate(str) {

  // let input = str.replace(/^吉B/, '');
  const reg = /^[A-Z]{1}\d{4}$/;
  return reg.test(str);
}

export function validTrailerPlate(str) {

  // let input = str.replace(/^吉B/, '');
  const reg = /^[A-Z]{1}\d{3}$/;
  return reg.test(str);
}
/**
 * 验证身份证号码的合法性
 * @param {string} str - 待验证的身份证号码
 * @returns {boolean} - 如果身份证号码合法，返回 true；否则，返回 false
 */
export function validIDCard(str) {
  const reg = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/; 
  return reg.test(str);
}


/**
 * 验证手机号
 * @param str
 * @returns {*|boolean}
 */
export function validPhone(str) {
  const reg = /^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/;
  return reg.test(str);
}
export function validPhonenumber(str) {
  const reg = /^\d{11}$/;
  return reg.test(str);
}
/**
 * 验证邮箱
 * @param str
 * @returns {*|boolean}
 */
export function validEmail(str) {
  const reg = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return reg.test(str);
}

/**
 * 验证密码
 * @param str
 * @returns {*|boolean}
 */
export function validPassword(str) {
  const reg = /^[a-zA-Z_0-9]{6,18}$/; //必须是6-18个字母和数字下划线
  return reg.test(str);
}




/**
 * @param {string} url
 * @returns {Boolean}
 */
export function validURL(url) {
  const reg = /^(https?|ftp):\/\/([a-zA-Z0-9.-]+(:[a-zA-Z0-9.&%$-]+)*@)*((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9][0-9]?)(\.(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])){3}|([a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(:[0-9]+)*(\/($|[a-zA-Z0-9.,?'\\+&%$#=~_-]+))*$/
  return reg.test(url)
}

/**
 * @param {string} str
 * @returns {Boolean}
 */
export function validLowerCase(str) {
  const reg = /^[a-z]+$/
  return reg.test(str)
}

/**
 * @param {string} str
 * @returns {Boolean}
 */
export function validUpperCase(str) {
  const reg = /^[A-Z]+$/
  return reg.test(str)
}

/**
 * @param {string} str
 * @returns {Boolean}
 */
export function validAlphabets(str) {
  const reg = /^[A-Za-z]+$/
  return reg.test(str)
}

/**
 * @param {string} str
 * @returns {Boolean}
 */
export function isString(str) {
  if (typeof str === 'string' || str instanceof String) {
    return true
  }
  return false
}

/**
 * @param {Array} arg
 * @returns {Boolean}
 */
export function isArray(arg) {
  if (typeof Array.isArray === 'undefined') {
    return Object.prototype.toString.call(arg) === '[object Array]'
  }
  return Array.isArray(arg)
}

