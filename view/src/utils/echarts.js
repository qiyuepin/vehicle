// 加载echarts，注意引入文件的路径
// echarts 配置

// 引入 ECharts 模块
const echarts = require('echarts/lib/echarts')

//引入图表组件
require('echarts/lib/chart/bar')
require("echarts/lib/chart/line")
require("echarts/lib/chart/pie")
require("echarts/lib/chart/radar")

//引入提示框，标题等组件
require('echarts/lib/component/tooltip')
require("echarts/lib/component/legend")
export default echarts

