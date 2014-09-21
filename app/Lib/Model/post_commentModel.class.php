<?php

class post_commentModel extends RelationModel
{
    protected $_auto = array (array('add_time','time',1,'function'));
    //关联关系
    protected $_link = array(
        'user' => array(
            'mapping_type' => BELONGS_TO,
            'class_name' => 'user',
            'foreign_key' => 'uid',
        ),        
        'post'=> array(
            'mapping_type' => BELONGS_TO,
            'class_name' => 'post',
            'foreign_key' => 'post_id',
        ),   
    );
    /**
     * 新增评论更新商品评论数和评论缓存字段
     */
    protected function _after_insert($data,$options) {
        $post_mod = D('post');
        $post_mod->where(array('id'=>$data['post_id']))->setInc('comments');
    }
}