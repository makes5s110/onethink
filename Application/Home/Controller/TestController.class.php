<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017\8\15 0015
 * Time: 10:10
 */

namespace Home\Controller;


use Think\Controller;

class TestController extends Controller
{
    public function index(){

//        echo urldecode("http://api.map.baidu.com/place/detail?uid=9b10997e19634b063a13fda1&amp;output=html&amp;source=placeapi");exit;

        $str = "s酒店";
//        $tou = substr($str,0,1);
//        $hou = substr($str,1);
//        var_dump($tou,$hou);
        preg_match("/^(\w)(.*)$/",$str,$matches);
       // dump($matches);exit;
        switch ($matches[1]){
            case 's'://基于位置的搜索
                $query = urlencode($matches[2]);//转义
                $location = '30.587347,104.054499';
                $search_url = "http://api.map.baidu.com/place/search?query={$query}&location={$location}&radius=1000&output=xml";
                //解析xml
                $simpleXml = simplexml_load_file($search_url);
//                dump($simpleXml);
                foreach ($simpleXml->results->result as $k=>$v){
                    var_dump($k);
                    dump($v);
                }
                break;
            case 'l'://搜索天气
                break;
        }
    }
}