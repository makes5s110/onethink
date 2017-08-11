<?php
/**
 * Created by PhpStorm.
 * User: leave
 * Date: 2017/8/10
 * Time: 14:22
 */

namespace Admin\Controller;

//物业管理控制器
class RepairController extends AdminController
{
    //首页
    public function index()
    {
        //自动获取传入的参数
//        $pid = I('get.pid',0);
//        //列表
//        $map  = array('status' => array('gt', -1), 'pid'=>$pid);
        $list = M('Repair')->order('id asc')->select();
        //var_dump($list);exit;
        $this->assign('list', $list);
        //$this->assign('pid', $pid);
        //->meta_title = '报修管理';
        $this->display();
    }
    //添加报修单
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
            //获取父导航
            if(!empty($pid)){
                $parent = M('Repair')->where(array('id'=>$pid))->field('title')->find();
                $this->assign('parent', $parent);
            }

            $this->assign('pid', $pid);
            $this->assign('info',null);
            $this->meta_title = '新增报修';
            $this->display('edit');
        }
    }
    //修改报修单
    public function Edit($id = 0)
    {
        if(IS_POST){
            $Repair = D('Repair');
            //create 相当于yii中的load方法
            $data = $Repair->create();
            if($data){
                if($Repair->save()){
                    //记录行为
                    action_log('update_repair', 'repair', $data['id'], UID);
                    $this->success('编辑成功', U('index'));
                } else {
                    $this->error('编辑失败');
                }

            } else {
                $this->error($Repair->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('Repair')->find($id);

            if(false === $info){
                $this->error('获取配置信息错误');
            }

            $pid = i('get.pid', 0);
            //获取父导航
            if(!empty($pid)){
                $parent = M('Repair')->where(array('id'=>$pid))->field('title')->find();
                $this->assign('parent', $parent);
            }

            $this->assign('pid', $pid);
            $this->assign('info', $info);
            $this->meta_title = '编辑报修单';
            $this->display();
        }
    }
    //删除保修单
    public function Del()
    {
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('Repair')->where($map)->delete()){
            //记录行为
            action_log('update_repair', 'repair', $id, UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
}