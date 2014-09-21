<?php

class post_favsModel extends RelationModel
{
    protected $_auto = array (array('add_time','time',1,'function'));
    //关联关系
    protected $_link = array(
        'user' => array(
            'mapping_type' => BELONGS_TO,
            'class_name' => 'user',
            'foreign_key' => 'uid',
        ),     
        'post' => array(
            'mapping_type' => BELONGS_TO,
            'class_name' => 'post',
            'foreign_key' => 'post_id',
        ),      
    );
}