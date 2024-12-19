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
              

                    <el-form-item label="名称" prop="name">
                        <el-input v-model="formData.name" clearable placeholder="请输入名称"></el-input>
                    </el-form-item>
                    <el-form-item label="联系人" prop="contact">
                        <el-input v-model="formData.contact" clearable placeholder="请输入联系人"></el-input>
                    </el-form-item>
                    <el-form-item label="联系电话" prop="contact_phone">
                        <el-input v-model="formData.contact_phone" clearable placeholder="请输入联系电话"></el-input>
                    </el-form-item>
                    <el-form-item label="地址" prop="load_address">
                      <!-- <el-input id="mapInput" type="text" value="请输入关键字：(选定后搜索)" onfocus='this.value=""' placeholder="请输入活动地址" /> -->
                      <div id="mapdiv">
                        <div class="searchbutton">
                          <input type="input" placeholder="请输入关键字" v-model="keyWord" />
                          <button type="button" @click="onSearch">搜索</button>
                        </div>
                        <div id="container"></div>
                      </div>
                      
                      <div id="panel"></div>

                    </el-form-item>
                    <el-form-item label="所属省份" prop="pname">
                        <el-input v-model="formData.pname" clearable placeholder="请输入所属省份"></el-input>
                    </el-form-item>
                    <el-form-item label="所属城市" prop="city">
                        <el-input v-model="formData.city" clearable placeholder="请输入所属城市"></el-input>
                    </el-form-item>
                    <el-form-item label="具体地址" prop="address">
                        <el-input v-model="formData.address" clearable placeholder="请输入具体地址"></el-input>
                    </el-form-item>
                    <el-form-item label="经纬度" prop="location">
                      <el-input v-model="formData.location" size="small" placeholder="经度"></el-input>
                      <!-- <el-input v-model="location.lat" size="small" placeholder="纬度"></el-input> -->
                    </el-form-item>
                    <!-- <el-form-item label="regeocode_id" prop="regeocode_id">
                      <el-input v-model="formData.regeocode_id" size="small" placeholder="经度"></el-input>
                    </el-form-item> -->
                    <el-input v-model="formData.regeocode_id" size="small" placeholder="regeocode_id" type="hidden"></el-input>
                    <el-input v-model="formData.adcode" size="small" placeholder="adcode" type="hidden"></el-input>
           
                    
                </el-tab-pane>
            </el-tabs>
        </el-form>
        <div class="demo-drawer__footer" style="position:fixed;top:15px;right:30px;">
            <el-button size="mini" @click="$refs.drawer.closeDrawer()">取 消</el-button>
            <el-button size="mini" type="primary" @click="saveData()">确 定</el-button>
        </div>
    </div>
  </el-drawer>
</template>

<script>

import { addtank, edit, getinfo} from '@/api/tank.js'
import { editfactory } from '@/api/Info.js'
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
    // location:{
    //   lat:121.614786,
    //   lng:38.913962
    // },
    location: [121.614786,38.913962],
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
      name: '',
      address: '',
      pname: '',
      location: '',
      regeocode_id: '',
      adcode: '',
      city: '',
      contact: '',
      contact_phone: ''
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
  getinfo(id){
    getinfo({id:id}).then(response=>{
        if(response !== undefined){
          console.log(response)
            this.title = '编辑地址'
            this.formData.id = response.id
            this.formData.name = response.name
            this.formData.address = response.address
            this.formData.adcode = response.adcode
            this.formData.location = response.location
            this.formData.regeocode_id = response.regeocode_id
            this.formData.pname = response.pname
            this.formData.city = response.city
            this.formData.contact = response.contact
            this.formData.contact_phone = response.contact_phone
            this.initAMap(response.location)
        }
    })
  },
  initAMap(location) {
    console.log(location)
    AMapLoader.load({
      key: "4cfd1e1cfdc8b0e77e0aaaaade6aee50", // 申请好的Web端开发者Key，首次调用 load 时必填
      version: "2.0", // 指定要加载的 JSAPI 的版本，缺省时默认为 1.4.15
      plugins: ["AMap.Scale", 'AMap.PlaceSearch', 'AMap.AutoComplete','AMap.Geolocation'], //需要使用的的插件列表，如比例尺'AMap.Scale'，支持添加多个如：['...','...']
    })
      .then((AMap) => {

        const arrlocation = location.split(',').map(Number);

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
          center: arrlocation, //地图中心点
          zoom: 18, //地图级别
          mapStyle: "amap://styles/normal", //设置地图的显示样式
          layers: [layer], //layer为创建的默认图层3-2:展示图层
        });
        // const test= arrlocation.join(',');
        // console.log(arrlocation)
        // console.log(test)
        // console.log(new AMap.LngLat(test))
        const marker = new AMap.Marker({
          position: new AMap.LngLat(arrlocation[0],arrlocation[1]), // 标记的位置
          map: this.map // 将标记添加到地图上
        });
        this.map.on('click', this.clickMapHandler)
      })
      .catch((e) => {
        console.log(e);
      });
  },
  onSearch() {
    // e.preventDefault();
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

    });
    placeSearch.on('markerClick', (e) => {
        // 处理搜索结果选中事件
      this.handleMarkerClick(e.data);

    });
  },
  handleMarkerClick(markerData) {
      // 处理搜索结果选中事件，获取选中地点的位置信息
    console.log(markerData);
    this.formData.address = markerData.pname + markerData.cityname + markerData.adname + markerData.address;
    console.log(this.address);
    this.formData.pname = markerData.pname;

    this.formData.city = markerData.cityname?markerData.cityname:markerData.pname;
    // location = markerData.location;
    this.formData.location = markerData.location.lng + ',' + markerData.location.lat;
    const panel = document.getElementById('panel');
    console.log(panel);
    panel.style.display = 'none';

  },
  
  clickMapHandler(e) {
    console.log(e)
    // console.log('位置名称:', result.regeocode.formattedAddress)
      this.location.lng = e.lnglat.getLng()
      this.location.lat = e.lnglat.getLat()
      this.formData.location = this.location.lng + ',' + this.location.lat;
    
      const lnglat = e.lnglat;
      // let regeocode = '';
      // 调用逆地理编码方法，传入经纬度坐标
      const selfs = this;
      AMap.plugin('AMap.Geolocation', function () {
        const geolocation = new AMap.Geolocation({
            enableHighAccuracy: true, // 是否使用高精度定位
            timeout: 10000, // 超时时间
            buttonOffset: new AMap.Pixel(10, 20), // 定位按钮偏移量
            zoomToAccuracy: true, // 定位成功后是否自动调整地图视野到定位点
            buttonPosition: 'RB' // 定位按钮位置
        });
        
        // 逆地理编码，通过经纬度获取位置信息
        AMap.plugin('AMap.Geocoder', function () {
            const geocoder = new AMap.Geocoder();
            
            geocoder.getAddress(lnglat, (status, result) => {
                if (status === 'complete' && result.info === 'OK') {
                    // 成功获取到位置信息
                    console.log(result)
                    const regeocode = result.regeocode.addressComponent;
                    const address = regeocode.province + regeocode.city + regeocode.district + regeocode.township + regeocode.street + regeocode.streetNumber;
                    const address0 = result.regeocode.formattedAddress;
                    console.log(regeocode)
                    // 使用箭头函数确保正确的上下文
                    selfs.formData.pname = regeocode.province;
                    selfs.formData.address = address;
                    selfs.formData.regeocode_id = regeocode.regeocode_id;
                    selfs.formData.adcode = regeocode.adcode;
                    selfs.formData.city = regeocode.city?regeocode.city:regeocode.province;
                    // 你可以将位置信息存储到需要的地方
                } else {
                    console.error('逆地理编码失败');
                }
            });
        });
    });
    
  },
  saveData() {
    this.$confirm('您确定要提交吗？', '温馨提示')
      .then(_ => {
        this.$refs.saveForm.validate(valid => {
          if (valid) {
            if(this.formData.id){
              console.log(77777777777777)
              edit(this.formData).then(_ => {
                console.log(this.formData)
                this.$message({
                  message: '编辑成功',
                  type: 'success',
                  duration: 5 * 1000
                })
                this.$emit('updateRow')
                this.dialog = false
              })
            }else{
              addtank(this.formData).then(_ => {
                this.$message({
                  message: '新增成功',
                  type: 'success',
                  duration: 5 * 1000
                })
                this.$emit('updateRow')
                this.dialog = false
              })
            }
          }
        })
      })
      .catch(_ => {
      })
  },
  handleClose() {
    this.dialog = false
    this.drawerShow = false
  },
  showForm() {
    this.dialog = true
    this.drawerShow = true
    this.title = '新增'
    this.resetData()
  },
  resetData(){
    this.formData.id = 0
    this.formData.name = ''
    this.formData.address = ''
    this.formData.pname = ''
    this.formData.location = ''
    this.formData.city = ''
    this.formData.contact = ''
    this.formData.contact_phone = ''
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
  .searchbutton {
    z-index: 100;
    position: absolute;
    top: 5px;
    right: 10px;
    // background-color: white;
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
