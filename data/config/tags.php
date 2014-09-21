<?php

// 行为插件
return array(
    /**
     +------------------------------------------------------------------------------
     * 系统标签
     +------------------------------------------------------------------------------
     */
    'app_begin' => array(
        'load_lang', //语言
    ),
    'app_end'=>array(
        'check_ipban', //禁止IP  
    ),
    'view_template' => array(
        'basic_template','_overlay'=>1, //自动定位模板文件
    ),
    'view_filter' => array(
        'content_replace', //路径替换
    ),

    /**
     +------------------------------------------------------------------------------
     * 用户行为标签
     +------------------------------------------------------------------------------
     */
    //登录
    'login_begin' => array(
        'check_ipban', //禁止IP 
    ),
    'login_end' => array(
        'alter_score', // 积分
    ),
    //注册
    'register_begin' => array(
    ),
    'register_end' => array(
        'alter_score', // 积分
    ),
    //爆料
    'submit_end'=>array(
        'alter_score', // 积分
    ),
    //评论
    'comment_end'=>array(
        'alter_score', // 积分
    ),
    //收藏
    'favs_end'=>array(
        'alter_score', // 积分
    ),  
);