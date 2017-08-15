<?php
/**
 * Created by PhpStorm.
 * User: leave
 * Date: 2017/8/11
 * Time: 18:54
 */

namespace Home\Controller;


class RepairController extends HomeController
{
    //添加在线报修
    public function add()
    {
        if(IS_POST){
            $Repair = D('Repair');
            //create 相当于yii中的load方法
            $data = $Repair->create();
            if($data){
                $Repair->create_time = time();
                $Repair->sn = uniqid();
                $id = $Repair->add();
                if($id){
                    $this->success('新增成功', U('index'));
                    //记录行为
                    action_log('update_repair', 'repair', $id, UID);
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Repair->getError());
            }
        } else {
            $pid = i('get.pid', 0);
            if(!empty($pid)){
                $parent = M('Repair')->where(array('id'=>$pid))->field('title')->find();
                $this->assign('parent', $parent);
            }

            $this->assign('pid', $pid);
            $this->assign('info',null);
            $this->meta_title = '新增报修';
            $this->display('add');
        }
    }
}