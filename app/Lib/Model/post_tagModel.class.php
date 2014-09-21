<?php
class post_tagModel extends RelationModel
{
    protected $_link = array(
        'tag' => array(
            'mapping_type' => BELONGS_TO,
            'class_name' => 'tag',
            'foreign_key' => 'tag_id',
        ),   
    );    
}