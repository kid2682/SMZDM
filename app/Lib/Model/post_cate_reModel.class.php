<?php
class post_cate_reModel extends RelationModel
{
    protected $_link = array(
        'cate' => array(
            'mapping_type' => BELONGS_TO,
            'class_name' => 'post_cate',
            'foreign_key' => 'cate_id',
        ),   
    );    
}