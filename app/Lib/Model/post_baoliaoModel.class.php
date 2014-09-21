<?php
class post_baoliaoModel extends RelationModel
{
    //自动完成
    protected $_auto = array(
        array('add_time', 'time', 1, 'function'),
    );
    //自动验证
    protected $_validate = array(        
    );
    //关联关系
    protected $_link = array(
        'user' => array(
            'mapping_type' => BELONGS_TO,
            'class_name' => 'user',
            'foreign_key' => 'uid',
        ),           
    );
}