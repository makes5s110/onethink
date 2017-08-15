<?php
/**
 * Created by PhpStorm.
 * User: leave
 * Date: 2017/8/10
 * Time: 16:59
 */

namespace Admin\Model;


use Think\Model;

class RepairModel extends Model
{
    //定义验证规则，自动验证
    protected $_validate = array(
        array('name', 'require', '报修人不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('tel', 'require', '电话不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('intro', 'require', '报修问题不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('address', 'require', '报修地址不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
    );
    //设置默认,自动完成
    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('status', '1', self::MODEL_BOTH),
    );

}