<?php
// 应用公共文件

//curl-get请求
if(!function_exists('httpGet')){
    function httpGet($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }
}

//curl-post请求
if(!function_exists('httpPost')) {
    function httpPost($url, $data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}

if(!function_exists('paramsToString')){
    function paramsToString($param)
    {
        $buff = "";
        foreach ($param as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }
        return trim($buff, "&");
    }
}

if(!function_exists('getNonceStr')) {
    function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
}

//高德获取省市区地址
if(!function_exists('getRegion')){
    function getRegion(){
        $url = 'https://restapi.amap.com/v3/config/district?subdistrict=3&extensions=base&key=100fa00d8bf676d8042b3081de300a92';
        $result = json_decode(httpGet($url),true);
        $data = [];
        if($result['status']==1){
            $districts = $result['districts'][0]['districts'];
            foreach($districts as $province){
                if($province['level']=='province'){
                    $region = [];
                    $region['name'] = $province['name'];
                    $region['adcode'] = $province['adcode'];
                    $region['center'] = $province['center'];
                    $region['citycode'] = 0;
                    $region['parent'] = 0;
                    $region['level'] = 1;
                    $region['create_time'] = date('Y-m-d H:i:s');
                    $region['update_time'] = date('Y-m-d H:i:s');
                    $data[] = $region;
                    if(isset($province['districts'])){
                        foreach($province['districts'] as $city){
                            if($city['level']=='city'){
                                $regionCity = [];
                                $regionCity['name'] = $city['name'];
                                $regionCity['adcode'] = $city['adcode'];
                                $regionCity['center'] = $city['center'];
                                $regionCity['citycode'] = is_array($city['citycode'])?'':$city['citycode'];
                                $regionCity['parent'] = $province['adcode'];
                                $regionCity['level'] = 2;
                                $regionCity['create_time'] = date('Y-m-d H:i:s');
                                $regionCity['update_time'] = date('Y-m-d H:i:s');
                                $data[] = $regionCity;
                                if(isset($city['districts'])){
                                    foreach($city['districts'] as $district){
                                        if($district['level']=='district'){
                                            $regionCity = [];
                                            $regionCity['name'] = $district['name'];
                                            $regionCity['adcode'] = $district['adcode'];
                                            $regionCity['center'] = $district['center'];
                                            $regionCity['citycode'] = is_array($district['citycode'])?'':$district['citycode'];
                                            $regionCity['parent'] = $city['adcode'];
                                            $regionCity['level'] = 3;
                                            $regionCity['create_time'] = date('Y-m-d H:i:s');
                                            $regionCity['update_time'] = date('Y-m-d H:i:s');
                                            $data[] = $regionCity;
                                        }
                                    }
                                }
                            }
                            if($city['level']=='district'){
                                $regionCity = [];
                                $regionCity['name'] = $city['name'];
                                $regionCity['adcode'] = $city['adcode'];
                                $regionCity['center'] = $city['center'];
                                $regionCity['citycode'] = is_array($city['citycode'])?'':$city['citycode'];
                                $regionCity['parent'] = $province['adcode'];
                                $regionCity['level'] = 3;
                                $regionCity['create_time'] = date('Y-m-d H:i:s');
                                $regionCity['update_time'] = date('Y-m-d H:i:s');
                                $data[] = $regionCity;
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }
}
