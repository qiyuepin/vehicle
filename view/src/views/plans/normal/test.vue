<template>
  <el-drawer
          v-if="drawerShow"
          :before-close="handleClose"
          :with-header="false"
          :wrapperClosable="false"
          :visible.sync="dialog"
          size="50%"
          direction="rtl"
          custom-class="demo-drawer"
          ref="drawer"
  >
  
  <div class="demo-drawer__content" style="padding: 10px">
        <h3 style="margin: 7px 0px;font-weight: 600;font-size: 20px;" v-text="title"></h3>
        <el-form ref="saveForm" :model="formData" :rules="saveRules" size="small" label-position="right"
                 label-width="110px"
                 style="width: 100%;">
            <el-tabs style="height: 200px;">
                <el-tab-pane label="基本信息">
              
                    <el-form-item label="车辆/人员" prop="info_id">
                        <el-select v-model="formData.info_id" filterable  placeholder="请选择车辆/人员" @change="infoChanged">
                          <el-option
                            v-for="item in infolist"
                            :key="item.value"
                            :label="item.info"
                            :value="item.id">
                          </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="车头" prop="head_num">
                        <el-input v-model="formData.head_num" clearable placeholder="请输入车头"></el-input>
                    </el-form-item>
                    <el-form-item label="挂车" prop="trailer_num">
                        <el-input v-model="formData.trailer_num" clearable placeholder="请输入挂车"></el-input>
                    </el-form-item>
                    <el-form-item label="驾驶员" prop="driver_name">
                        <el-input v-model="formData.driver_name" clearable placeholder="请输入驾驶员"></el-input>
                    </el-form-item>
                    <el-form-item label="押运员" prop="escort_name">
                        <el-input v-model="formData.escort_name" clearable placeholder="请输入押运员"></el-input>
                    </el-form-item>
                    <el-form-item label="货品名称" prop="product_name">
                        <el-input v-model="formData.product_name" clearable placeholder="请输入货品名称"></el-input>
                    </el-form-item>

                    <el-form-item label="货品数量" prop="product_quantity">
                        <el-input v-model="formData.product_quantity" clearable placeholder="请输入货品数量"></el-input>
                    </el-form-item>


                    <el-form-item label="装货厂家" prop="load_factory">
                        <el-select v-model="formData.load_factory" filterable  clearable placeholder="请选择装货厂家" @change="loadFactoryChanged">
                          <el-option
                            v-for="item in factorylist"
                            :key="item.value"
                            :label="item.name"
                            :value="item.name">
                          </el-option>
                        </el-select>
                    </el-form-item>
                    <!-- <el-form-item label="装货厂家名字" prop="load_factory">
                        <el-input v-model="formData.load_factory" clearable placeholder="装货厂家名字"></el-input>
                    </el-form-item> -->
                    <el-form-item label="装货地址" prop="load_address">
                        <el-input v-model="formData.load_address" clearable placeholder="请输入装货地址"></el-input>
                    </el-form-item>
                    <el-form-item label="装货地址" prop="load_address">
                      <el-input v-model="location.lng" size="small" placeholder="经度"></el-input>
                      <el-input v-model="location.lat" size="small" placeholder="纬度"></el-input>
                    </el-form-item>
                    <el-form-item label="装货地址" prop="load_address">
                      <!-- <el-input id="mapInput" type="text" value="请输入关键字：(选定后搜索)" onfocus='this.value=""' placeholder="请输入活动地址" /> -->
                      <div id="container">
                        <div class="search">
                          <input type="input" placeholder="请输入关键字" v-model="keyWord" />
                          <button @click="onSearch">搜索</button>
                        </div>
                      </div>
                      
                      <div id="panel"></div>

                    </el-form-item>
                    <el-form-item label="卸货厂家" prop="unload_factory">
                        <el-select v-model="formData.unload_factory" filterable clearable placeholder="请选择卸货厂家">
                          <el-option
                            v-for="item in factorylist"
                            :key="item.value"
                            :label="item.name"
                            :value="item.name">
                          </el-option>
                        </el-select>
                    </el-form-item>
                    <!-- <el-form-item label="卸货厂家名字" prop="unload_factory">
                        <el-input v-model="formData.unload_factory" clearable placeholder="卸货厂家名字"></el-input>
                    </el-form-item> -->
                    <el-form-item label="卸货地址" prop="unload_address">
                        <el-input v-model="formData.unload_address" clearable placeholder="请输入卸货地址"></el-input>
                    </el-form-item>
                    <el-form-item label="新计费周期" prop="start_periodic">
                        <el-radio-group v-model="formData.start_periodic">
                          <el-radio v-model="formData.start_periodic" label="1">是</el-radio>
                          <el-radio v-model="formData.start_periodic" label="0">否</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="结束计费周期" prop="end_periodic">
                        <el-radio-group v-model="formData.end_periodic">
                          <el-radio v-model="formData.end_periodic" label="1">是</el-radio>
                          <el-radio v-model="formData.end_periodic" label="0">否</el-radio>
                        </el-radio-group>
                    </el-form-item>
                </el-tab-pane>
            </el-tabs>
        </el-form>
        <div class="demo-drawer__footer" style="position:fixed;top:15px;right:30px;">
            <el-button size="mini" @click="$refs.drawer.closeDrawer()">取 消</el-button>
            <el-button size="mini" type="primary" @click="saveData()">确 定
            </el-button>
        </div>
    </div>
  </el-drawer>
</template>

<script>

import { addnormal, editnormal, getnormalinfo, getplaninfo } from '@/api/plan.js'
import AMapLoader from '@amap/amap-jsapi-loader';

window._AMapSecurityConfig = {
    securityJsCode: 'b64b7df6be939d822d7380f63fb22f31',
}
export default {
name: "test",
components: {
  Map
},
data() {
  return {
    title:'',
    dialog: false,
    roles: [],
    infolist: [],
    factorylist: [],
    load_factory: null,
    load_address:'',
    drawerShow:false,
    map: null,
    traffic: null, // 实时交通
    keyWord: null, //输入搜索的关键字
    location:{
      lat:'',
      lng:''
    },
    searchResults: [], // 存储搜索结果
    showSearchResults: true,
    saveRules: {
      info_id: [{ required: true, trigger: 'blur'}],
      product_name: [{ required: true, trigger: 'blur'}],
      product_quantity: [{ required: true, trigger: 'blur'}],
      load_factory: [{ required: true, trigger: 'blur'}],
      unload_factory: [{ required: true, trigger: 'blur'}],
    },
    formData: {
      id: 0,
      info_id: '',
      product_name: '',
      product_quantity: '',
      load_factory: '',
      load_address: '',
      unload_factory: '',
      unload_address: '',
      head_num: '',
      trailer_num: '',
      driver_name: '',
      escort_name: '',
      start_periodic: '',
      end_periodic: '',
      platform: 'pc'
    },
  }
},

mounted() {
  this.initAMap();
},
beforeUnmount() {
  this.map?.destroy();
},
methods: {
  initAMap() {
    AMapLoader.load({
      key: "4cfd1e1cfdc8b0e77e0aaaaade6aee50", // 申请好的Web端开发者Key，首次调用 load 时必填
      version: "2.0", // 指定要加载的 JSAPI 的版本，缺省时默认为 1.4.15
      plugins: ["AMap.Scale", 'AMap.PlaceSearch', 'AMap.AutoComplete','AMap.Geolocation'], //需要使用的的插件列表，如比例尺'AMap.Scale'，支持添加多个如：['...','...']
    })
      .then((AMap) => {
        // this.map = new AMap.Map("container", {
        //   // 设置地图容器id
        //   viewMode: "3D", // 是否为3D地图模式
        //   zoom: 11, // 初始化地图级别
        //   center: [116.397428, 39.90923], // 初始化地图中心点位置
        // });
        
        this.placeSearch = new AMap.PlaceSearch({
          map: this.map
        });
        const layer = new AMap.createDefaultLayer({
          zooms: [3, 20], //可见级别
          visible: true, //是否可见
          opacity: 1, //透明度
          zIndex: 0, //叠加层级
        });

        this.map = new AMap.Map("container", {
          viewMode: "3D", //默认使用 2D 模式，平面模式
          center: [121.045332, 31.19884], //地图中心点
          zoom: 8.8, //地图级别
          mapStyle: "amap://styles/normal", //设置地图的显示样式
          layers: [layer], //layer为创建的默认图层3-2:展示图层
        });
        this.map.on('click', this.clickMapHandler)
        // AMap.event.addListener(placeSearch, 'select', (selectInfo) => {
        //   // 选中地点后的处理逻辑
        //   const address = selectInfo.poi.name; // 获取选中的地址
        //   console.log('选中的地址:', address);
        //   // 可以在这里执行其他逻辑，例如更新组件的数据等
        // });
  

      })
      .catch((e) => {
        console.log(e);
      });
  },
  onSearch() {
    const placeSearch = new AMap.PlaceSearch({
      pageSize: 3, //单页显示结果条数
      pageIndex: 1, //页码
      city: "", //兴趣点城市
      citylimit: true, //是否强制限制在设置的城市内搜索
      map: this.map, //展现结果的地图实例
      panel: "panel", //结果列表将在此容器中进行展示。
      autoFitView: true, //是否自动调整地图视野使绘制的 Marker 点都处于视口的可见范围
    });
    placeSearch.search(this.keyWord, (status, result) => {
      //查询成功时，result 即对应匹配的 POI 信息
      const panel = document.getElementById('panel');
    console.log(panel);
    panel.style.display = 'block';
      console.log(result)
      // this.showSearchResults = true;
        // this.handleMarkerClick(e.data);
    
      // this.onSelectAddress(result.poiList.pois[0]);
    });
    placeSearch.on('markerClick', (e) => {
        // 处理搜索结果选中事件
      this.handleMarkerClick(e.data);

    });

  },
  handleMarkerClick(markerData) {
      // 处理搜索结果选中事件，获取选中地点的位置信息
    console.log(markerData);
    const panel = document.getElementById('panel');
    console.log(panel);
    panel.style.display = 'none';

  },
  getLocationDetail(placeId) {
    AMap.plugin('AMap.PlaceSearch', () => {
      const placeSearch = new AMap.PlaceSearch({
        // 配置 PlaceSearch 的选项
      });
      placeSearch.getDetails(placeId, (status, result) => {
        if (status === 'complete' && result.info === 'OK') {
          // 处理位置的详情信息
          console.log(result);
        }
      });
    });
  },
  clickMapHandler(e) {
    console.log(e)
    // console.log('位置名称:', result.regeocode.formattedAddress)
      this.location.lng = e.lnglat.getLng()
      this.location.lat = e.lnglat.getLat()


      //getLocationDetail(lnglat);
      // geocoder.getAddress(lnglat, (status, result) => {
      //   if (status === 'complete' && result.info === 'OK') {
      //     if (result && result.regeocode) {
      //       console.log('位置名称:', result.regeocode.formattedAddress);
      //       // 可以将位置名称显示在页面上或进行其他操作
      //     }
      //   }
      // });
  },
  infoChanged() {
    const selectedinfo = this.infolist.find(item => item.id === this.formData.info_id);
    if (selectedinfo) {
      console.log(selectedinfo)
      this.formData.head_num = selectedinfo.head_num;
      this.formData.trailer_num = selectedinfo.trailer_num;
      this.formData.driver_name = selectedinfo.driver_name;
      this.formData.escort_name = selectedinfo.escort_name;
    } else {
      this.formData.head_num = '';
      this.formData.trailer_num = '';
      this.formData.driver_name = '';
      this.formData.escort_name = '';
    }
    // this.load_address = this.load_factory.factory;
  },
  loadFactoryChanged() {
    const selectedFactory = this.factorylist.find(item => item.name === this.formData.load_factory);
    if (selectedFactory) {
      console.log(selectedFactory)
      this.formData.load_address = selectedFactory.address;
    } else {
      this.formData.load_address = '';
    }
    // this.load_address = this.load_factory.factory;
  },
  unloadFactoryChanged() {
    const selectedFactory = this.factorylist.find(item => item.name === this.formData.unload_factory);
    if (selectedFactory) {
      console.log(selectedFactory)
      this.formData.unload_address = selectedFactory.address;
    } else {
      this.formData.unload_address = '';
    }
    // this.load_address = this.load_factory.factory;
  },
  handleClose() {
    this.dialog = false
    this.drawerShow = false
  },
  showForm() {
    this.dialog = true
    this.drawerShow = true
    this.title = '新增任务'
    this.resetData()
  },
  resetData(){
    this.formData.id = 0
    this.formData.info_id = ''
    this.formData.product_name = ''
    this.formData.product_quantity = ''
    this.formData.load_address = ''
    this.formData.load_factory = ''
    this.formData.unload_address = ''
    this.formData.unload_factory = ''
    this.formData.head_num = ''
    this.formData.trailer_num = ''
    this.formData.driver_name = ''
    this.formData.escort_name = ''
    this.formData.start_periodic = ''
    this.formData.end_periodic = ''
  }
}
}
</script>

<style scoped lang="scss">
  ::v-deep .el-tabs__item:focus.is-active.is-focus:not(:active) {
      -webkit-box-shadow: none;
      box-shadow: none;
  }
  #container {
    margin: 0;
    padding: 0;
    width: 100%;
    height: 100%;
    min-height: 300px;
    background: pink;
  }
  .search {
    position: fixed;
    top: 50px;
    right: 10px;
    background-color: white;
  }
  #panel {
    position: absolute;
    background-color: white;
    max-height: 90%;
    overflow-y: auto;
    top: 30px;
    right: 10px;
    width: 280px;
  }


</style>
