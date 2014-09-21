<?php
class mallModel extends RelationModel
{
    //自动完成
    protected $_auto = array(
        array('add_time', 'time', 1, 'function'),
    );
    //自动验证
    protected $_validate = array(
        array('title', 'require', '商城标题不能为空'),
    );
    //关联关系
    protected $_link = array(
        'cate' => array(
            'mapping_type' => BELONGS_TO,
            'class_name' => 'mall_cate',
            'foreign_key' => 'cid',
        ),           
    );
    public function addtime()
    {
        return date("Y-m-d H:i:s",time());
    }
}