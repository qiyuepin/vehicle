module.exports = {
  title: '后台管理系统',

  appid: 'ty9fd2848a039ab554',

  appSecret: 'ec32286d0718118861afdbf6e401ee81',

  errorMsg: '请求异常',

  whiteList: [
    '/admin/getCaptcha'
  ],

  uploadImgActive: '/uploadImage',

  uploadFileActive: '/uploadFile',

  readFileActive: '/readFile',

  uploadImgsActive: '/uploadImages',

  /**
   * @type {boolean} true | false
   * @description Whether show the settings right-panel
   */
  showSettings: true,

  /**
   * @type {boolean} true | false
   * @description Whether need tagsView
   */
  tagsView: true,

  /**
   * @type {boolean} true | false
   * @description Whether fix the header
   */
  fixedHeader: false,

  /**
   * @type {boolean} true | false
   * @description Whether show the logo in sidebar
   */
  sidebarLogo: false,

  /**
   * @type {boolean} true | false
   * @description Whether support pinyin search in headerSearch
   * Bundle size minified 47.3kb,minified + gzipped 63kb
   */
  supportPinyinSearch: true,

  /**
   * @type {string | array} 'production' | ['production', 'development']
   * @description Need show err logs component.
   * The default is only used in the production env
   * If you want to also use it in dev, you can pass ['production', 'development']
   */
  errorLog: 'production'
}
