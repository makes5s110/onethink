<?php
/**
 * Created by PhpStorm.
 * User: leave
 * Date: 2017/8/14
 * Time: 19:40
 */

namespace Home\Controller;

use EasyWeChat\Foundation\Application;
use EasyWeChat\Menu\Menu;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Text;
use Think\App;

require "../../../vendor/autoload.php";
class WechatController extends HomeController
{
    public function index()
    {
        //将配置放入到配置文件中
        $app = new Application(C('wechat_config'));
        $server = $app->server;
        //消息的处理
        $server->setMessageHandler(function ($message) {
            /**
             * 基本属性：
             *  $message->ToUserName    接收方帐号（该公众号 ID）
                $message->FromUserName  发送方帐号（OpenID, 代表用户的唯一标识）
                $message->CreateTime    消息创建时间（时间戳）
                $message->MsgId         消息 ID（64位整型）去重
             */
            //文本消息
//            if($message->MsgType == "text")
//            {
//                return $message->Content;
//            }
            switch ($message->MsgType) {
                case 'event':
                    switch ($message->Event)//事件行为类型包括（订阅 subscribe  取消订阅  unsubscribe 点击 CLICK）
                    {
                        case 'subscribe'://订阅（关注事件）
                            return '欢迎关注我们的智能物业系统';
                            break;
                        case 'unsubscribe'://取消订阅事件，不用做任何操作
                            break;
                        case 'CLICK':
                            //自定义菜单点击事件
                            return $message->EventKey;
                            break;
                    }
                    break;
                case 'text':
                    //使用对象的方式处理文本消息
                    $content = $message->Content;
                    if($content)
                    {
                        preg_match("/^(\w)(.*)$/",$content,$matches);
                        switch ($matches[1])
                        {
                            case 's':
                                //基于位置的搜索
                                $query = urlencode($matches[2]);
                                //转义
                            //从数据库中查询出对应open_id的坐标
                            $user_location = M('location')->where(['open_id'=>$message->FromUserName])->find();
                            if(empty($user_location))
                            {
                                return "请先发送你的位置！";
                            }
                            $location = $user_location['x'].','.$user_location['y'];
                            $search_url = "http://api.map.baidu.com/place/search?query={$query} & location={$location} & radius = 1000 & output = xml";
                            //解析xml
                            $simpleXml = simplexml_load_file($search_url);

                            $news = [];
                            //所有的图文消息
                            $news_count = 0;
                            foreach ($simpleXml->results->result as $k=>$v)
                            {
                                /**
                                 * 'title'       => $title,
                                'description' => '...',
                                'url'         => $url,
                                'image'       => $image,
                                 */
                                $url = html_entity_decode($v->detail_url);
                                //将url中的实体符号转换回来
                                $lng = (string)$v->location->lng;
                                $lat = (string)$v->location->lat;
                                //获取百度静态图片
                                $image_url = "http://api.map.baidu.com/panorama/v2?ak=ryZH80XnumLfI5GQ9e1UAOvgcVH1YrK9&width=512&height=256&location={$lng},{$lat}&fov=180";
                                $new = new News(['title'=>(string)$v->name,'description'=>(string)$v->address,'url'=>$url,'image'=>$image_url]);
                                $news[] = $new;
                                $news_count++;
                                if($news_count >=8)
                                {
                                    break;
                                }
                            }
                            return $news;
                            break;
                            case 'l'://搜索天气
                                $simpleDocument=simplexml_load_file("http://flash.weather.com.cn/wmaps/xml/sichuan.xml");
                                $weathers=[];
                                foreach ($simpleDocument as $name=>$value){

                                    $weathers[(string)$value['cityname']]=(string)$value['stateDetailed']."\n".$weathers[(string)$value['tem']]='最低气温:'.$value['tem1'].'最高气温:'.$value['tem2'];
//                                    $weather['windPower']=(string)$value['windPower'];
//                                    $weather['time']=(string)$value['time'];
//                                    $weathers[]=$weather;
                                }
                                return $weathers[$matches[2]];
                                break;
                        }
                    }else
                    {
                        $text = new Text(['content' => '这是你自己发送的文本消息']);
                        return $text;
                    }
                    break;
                case 'image':
                    return '收到图片消息';
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    /**
                     * $message->Location_X  地理位置纬度
                    $message->Location_Y  地理位置经度
                    $message->Scale       地图缩放大小
                    $message->Label       地理位置信息
                     */
//                    return $message->Location_X.'=='.$message->Location_Y.'='.$message->Scale.'==='.$message->Label;
                    //将用户的位置信息保存到数据中 添加或更新
                    $sql = "insert into onethink_location (open_id,x,y,scale,label) values ('{$message->FromUserName}','$message->Location_X','$message->Location_Y',
'{$message->Scale}','{$message->Label}') ON DUPLICATE KEY UPDATE x='{$message->Location_X}',y='{$message->Location_Y}',scale='{$message->Scale}',label = '{$message->Label}'";
                    M()->execute($sql);
                    return $message->Label;
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;
            }

        });

        // 将响应输出
        $server->serve()->send(); // Laravel 里请使用：return $response;
    }
    //自定义微信菜单
    public function addMenu()
    {
        //获取菜单实例模型
        $app = new Application(C('wechat_config'));
        $menu = $app->menu;

        $buttons = [
            [
                "type" => "click",//点击事件
                "name" => "最新活动",
                "key" => "news_activity_list"
            ],
            [
                "name" => "菜单",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "便民服务",
                        "url" =>""
                    ],
                    [
                        "view" => "view",
                        "name" => "小区通知",
                        "url" => ""
                    ],
                    [
                        "view" => "view",
                        "name" => "在线维修",
                        "url" => ""
                    ],
                    [
                        "view" => "view",
                        "name" => "商家活动",
                        "url" => ""
                    ],
                ],
            ],
            [
                "name" => "个人中心",
                "type" => "view",
                "url" => ""
            ]
        ];
        $menu->add($buttons);
        //获取已经有的菜单
        $menus = $menu->all();
        dump($menus);

    }
    /**
     * 发起授权的方法
     */
    public static function getAccess()
    {
        if(!session('open_id'))
        {
            //没有发起授权
            $app = new Application(C('wechat_config'));
            $response = $app->oauth->scopes(['snsapi_base'])->redirect();
            //将请求的路由保存到session中
            session('request_uri',$_SERVER['PATH_INFO']);
            $response->send();
        }
    }
    //授权的回调页面  获取用户的open_id
    public function callback()
    {
        $app = new Application(C('wechat_config'));
        //获取用户信息
        $user = $app->oauth->user();
//        $user //可以用的方法
//        $user->getId(); //对应微信的OPENID
//        $user->getNickname();//对应微信的nickname
//        $user->getName();//对应微信的name
//        $user->getAvatar();//头像网址
//        $user->getOriginal();//原始API返回的结果
//        $user->getToken();//access_token,比如共享地址使用
        //将用户的open_id保存到session中
        session('open_id',$user->getId());
        $this->redirect(session('request_uri'));

    }
}