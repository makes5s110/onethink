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

require "./vendor/autoload.php";
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
                    //获取发送的文本信息
                    $content = $message->Content;
                    if($content)
                    {
                        //多文本消息发送
                        $new1 = new News([
                                'title' => '第一个图文消息',//图文消息标题
                                'description' => '这是第一个图文消息的描述',//图文消息的描述
                                'url' => 'http://www.baidu.com',//点击图文之后跳转的页面
                                'image' => 'http://imgsrc.baidu.com/imgad/pic/item/267f9e2f07082838b5168c32b299a9014c08f1f9.jpg'//图文消息中图片的网络地址
                        ]);
                        $new2 = new News([
                            'title' => '第二个图文消息',
                            'description' => '第二个图文消息的描述',
                            'url' => 'http://www.qq.com',
                            'image' => 'http://imgsrc.baidu.com/image/c0%3Dshijue1%2C0%2C0%2C294%2C40/sign=7b4ffe42b919ebc4d4757edaea4fa589/b64543a98226cffc1c697e95b3014a90f603ea52.jpg'
                        ]);
                        $new3 = new News([
                            'title' => '第三个图文消息',
                            'description' => '第三个图文消息的描述',
                            'url' => 'http://www.17173.com',
                            'image' =>'http://img3.utuku.china.com/0x0/mili/20160918/b6a0827a-8d38-4433-bf69-955770c1863c.jpg'
                        ]);
                        //发送图文消息
                        return [$new1,$new2,$new3];
                    }else
                    {
                        $text = new Text(['content' => '这是你访问的文本消息']);
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
                    return '收到坐标消息';
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
                "name" => "便民服务",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "小区通知",
                        "url" =>""
                    ],
                    [],
                    [],
                ],
            ],
            []
        ];

    }
}