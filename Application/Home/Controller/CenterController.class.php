<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017\8\14 0014
 * Time: 14:32
 */
namespace Home\Controller;

class CenterController extends LoginController
{
    /**
     * 个人中心，需要授权后才能访问
     */
    public function index(){
//        dump(U('Center/index'));
        //判断session中是否有open_id，如果有就不发起授权
//        dump($_SERVER);exit;
        $this->display();
    }
    public function Notice(){
        //需要授权
        echo "NOTICE";
    }
}