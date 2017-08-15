<?php

namespace Home\Controller;


class NoticeController extends HomeController
{
    //小区通知
    public function index()
    {
        $document = M('document')->alias('d')->join('onethink_picture da  ON d.id = da.id
')->field('d.id,d.title,d.description,d.view,da.path')->select();
//        $document = M('Document')->select();
//        $document = M('Document');
//        $list = $document->where('status=1')->page($_GET['p'].'2')->select();
//        $this->assign('list',$list);//赋值数据集
        $this->assign('list', $document);
//        $count = $document->where('status=1')->count();//满足条件的总条数
//        $Page = new \Think\Page($count,2);//实例化分页类，传入总条数和每页显示的记录数
//        $show = $Page->show();//分页显示输出
//        $this->assign('page',$show);//赋值分页输出
//        var_dump($document);exit;
        $this->display();
    }

    //小区通知详情

    public function detail($id)
    {
        $model = M('document')->alias('d')->join('onethink_document_article da  ON d.id = da.id
')->field('d.id,d.title,da.content,d.create_time')->where(['d.id' => $id])->select();
//        $list = $model->where($id)->select();
        $this->assign('list', $model);
//        var_dump($list);exit;
//        $model=M('document')->alias('d')->join('onethink_document_article da  ON d.id = da.id')
//            ->field('d.id,d.title,da.content,d.create_time')->where(['d.id'=>$id])->select();
////var_dump($model);exit;
//
//        $this->assign('list', $model);
        $this->display("notice-detail");
    }

}