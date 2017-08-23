<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017\8\15 0015
 * Time: 16:37
 */

namespace Home\Controller;


class LoginController extends HomeController
{
    public function _initialize()
    {
        parent::_initialize();
        //所有需要验证登录的控制器集成该控制器
        if(!is_login()){//验证session中的登录信息
            if(session('open_id')){
                $userinfo = M('member')->where(['openid'=>session('open_id')])->find();
                if($userinfo){
                    //自动登录
                    $Member = D('Member');
                    if($Member->login($userinfo['uid'])){ //登录用户
                        //重定向到原始页面
                        $this->redirect($_SERVER['PATH_INFO']);
                    }else{
                        $this->error($Member->getError());
                    }
                }
            }
            $this->error('您还没有登录，请先登录！', U('User/login'));
        }
        //不做任何操作
    }
}