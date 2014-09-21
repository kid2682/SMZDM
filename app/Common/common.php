<?php
function addslashes_deep($value) {
    $value = is_array($value) ? array_map('addslashes_deep', $value) : addslashes($value);
    return $value;
}

function stripslashes_deep($value) {
    if (is_array($value)) {
        $value = array_map('stripslashes_deep', $value);
    } elseif (is_object($value)) {
        $vars = get_object_vars($value);
        foreach ($vars as $key => $data) {
            $value->{$key} = stripslashes_deep($data);
        }
    } else {
        $value = stripslashes($value);
    }

    return $value;
}

function todaytime() {
    return mktime(0, 0, 0, date('m'), date('d'), date('Y'));
}

/**
 * 友好时间
 */
function fdate($time) {
    if (!$time)
        return false;
    $fdate = '';
    $d = time() - intval($time);
    $ld = $time - mktime(0, 0, 0, 0, 0, date('Y')); //年
    $md = $time - mktime(0, 0, 0, date('m'), 0, date('Y')); //月
    $byd = $time - mktime(0, 0, 0, date('m'), date('d') - 2, date('Y')); //前天
    $yd = $time - mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')); //昨天
    $dd = $time - mktime(0, 0, 0, date('m'), date('d'), date('Y')); //今天
    $td = $time - mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')); //明天
    $atd = $time - mktime(0, 0, 0, date('m'), date('d') + 2, date('Y')); //后天
    if ($d == 0) {
        $fdate = '刚刚';
    } else {
        switch ($d) {
            case $d < $atd:
                $fdate = date('Y年m月d日', $time);
                break;
            case $d < $td:
                $fdate = '后天' . date('H:i', $time);
                break;
            case $d < 0:
                $fdate = '明天' . date('H:i', $time);
                break;
            case $d < 60:
                $fdate = $d . '秒前';
                break;
            case $d < 3600:
                $fdate = floor($d / 60) . '分钟前';
                break;
            case $d < $dd:
                $fdate = floor($d / 3600) . '小时前';
                break;
            case $d < $yd:
                $fdate = '昨天' . date('H:i', $time);
                break;
            case $d < $byd:
                $fdate = '前天' . date('H:i', $time);
                break;
            case $d < $md:
                $fdate = date('m月d H:i', $time);
                break;
            case $d < $ld:
                $fdate = date('m月d', $time);
                break;
            default:
                $fdate = date('Y年m月d日', $time);
                break;
        }
    }
    return $fdate;
}

/**
 * 获取用户头像
 */
function avatar($uid, $size=40) {
    $avatar_size = explode(',', C('pin_avatar_size'));
    $size = in_array($size, $avatar_size) ? $size : '100';
    $avatar_dir = avatar_dir($uid);
    $avatar_file = $avatar_dir . md5($uid) . "_{$size}.jpg";
    if (!is_file(C('pin_attach_path') . 'avatar/' . $avatar_file)) {
        $avatar_file = "default_{$size}.jpg";
    }
    return __ROOT__ . '/' . C('pin_attach_path') . 'avatar/' . $avatar_file;
}

function avatar_dir($uid) {
    $uid = abs(intval($uid));
    $suid = sprintf("%09d", $uid);
    $dir1 = substr($suid, 0, 3);
    $dir2 = substr($suid, 3, 2);
    $dir3 = substr($suid, 5, 2);
    return $dir1 . '/' . $dir2 . '/' . $dir3 . '/';
}

function attach($attach, $type,$flg=false) {
    if(empty($attach)){
        return false;
    }    
    if (false === strpos($attach, 'http://')) {
        //本地附件
        if($flg){
            return C('pin_attach_path') . $type . '/' . $attach;
        }else{
            return __ROOT__ . '/' . C('pin_attach_path') . $type . '/' . $attach;    
        }        
        //远程附件        
    } else {
        //URL链接
        return $attach;
    }
}

/*
 * 获取缩略图
 */

function get_thumb($img, $suffix = '_thumb') {
    if (false === strpos($img, 'http://')) {
        $ext = array_pop(explode('.', $img));
        $thumb = str_replace('.' . $ext, $suffix . '.' . $ext, $img);
    } else {
        if (false !== strpos($img, 'taobaocdn.com') || false !== strpos($img, 'taobao.com')) {
            //淘宝图片 _s _m _b
            switch ($suffix) {
                case '_s':
                    $thumb = $img . '_100x100.jpg';
                    break;
                case '_m':
                    $thumb = $img . '_210x1000.jpg';
                    break;
                case '_b':
                    $thumb = $img . '_480x480.jpg';
                    break;
            }
        }
    }
    return $thumb;
}

/**
 * 对象转换成数组
 */
function object_to_array($obj) {
    $_arr = is_object($obj) ? get_object_vars($obj) : $obj;
    foreach ($_arr as $key => $val) {
        $val = (is_array($val) || is_object($val)) ? object_to_array($val) : $val;
        $arr[$key] = $val;
    }
    return $arr;
}
function get_child_ids($mod,$id){
    $ids='';
    $list=$mod->where("pid=$id")->select();
    if($list){
        foreach($list as $key=>$val){
            $ids.=$val['id'].',';
            $ids.=get_child_ids($mod,$val['id']);
        }    
    }else{
        return '';
    }
    return trim($ids,',');    
}
function get_cate_tree($mod,$id){
    $where=array();
    if($id>0){
        $where['id']=$id;
    }else{
        $where['pid']=0;
    }
    $list=$mod->where($where)->select();
    foreach($list as $key=>$val){
        $list[$key]['depth']=0;
        $list[$key]['child']=get_child_tree($mod,$val['id'],0);
    }
    return $list;
}
function get_child_tree($mod,$pid,$depth=0){
    $where['pid']=$pid;
    $list=$mod->where($where)->select();
    if($list){
        $depth++;
        foreach($list as $key=>$val){
            $list[$key]['depth']=$depth;
            $list[$key]['child']=get_child_tree($mod,$val['id'],$depth);
        }    
    }else{
        return false;
    }
    return $list;  
}
function html_select($name,$list,$id=-1){  
    if($id==null){
        $id=-1;
    }
    $html="<select name='$name' id='$name'>";    
    $html.="<option value='-1'>请选择...</option>";    
    foreach($list as $key=>$val){
        $html.="<option value='$key'";
        if($key==$id){
            $html.=" selected='selected'";
        }
        $html.=">$val</option>";
    }
    $html.="</select>";
    return $html;
}
function html_radio($name,$list,$id=-1){
    $html="";
    //file_put_contents('./data.txt',var_dump($list));
    foreach($list as $key=>$val){
        $html.="<span><input type='radio' name='$name' value='$key'";
        if($key==$id){
            $html.=" checked='checked'";
        }
        $html.="/>$val</span>";
    }
    return $html;
}
function topinyin($title){
    include(APP_PATH.'Lib/Pinlib/pinyin.class.php');
    $py=new cls_pinyin();
    return $py->tourl($title);
}
function get_index(){
    $list=array();    
    $list[9]='0~9';
    for($i=65;$i<91;$i++){
        $list[chr($i)]=chr($i);
    }
    return $list;
}
function table($table) {
    return C('DB_PREFIX') . $table;
}
function get_baoliao_type($cid){
    switch($cid){
        case 0:return '爆料';
        case 1:return '投稿';
        case 2:return '建议';
    }
}
function user_level($level){    
    $sun_img='<img src="'.__ROOT__.'/static/css/default/images/sun.png'.'"/>';//16
    $moon_img='<img src="'.__ROOT__.'/static/css/default/images/moon.png'.'"/>';//4
    $star_img='<img src="'.__ROOT__.'/static/css/default/images/star.png'.'"/>';//1
    if(empty($level)){
        return $star_img;
    }    
    $sun=$level/16;
    $moon=($level%16)/4;
    $star=$level%16%4;
    return str_repeat($sun_img,$sun).str_repeat($moon_img,$moon).str_repeat($star_img,$star);
}
function post_url($id,$post_key){    
    if(empty($post_key)){
        return U('post/index',array('id'=>$id));
    }else{
        return U('post/index',array('post_key'=>$post_key));
    }
}
function parse_uri($url){
    $res=parse_url($url); 
    $list=explode('&',$res['query']);    
    foreach($list as $item){
        $kv=explode('=',$item);        
        $res['_query'][$kv[0]]=$kv[1];
    }
    return $res;
}
function mall_url($mall_info){
    if(empty($mall_info['url'])||empty($mall_info['url_'.$mall_info['url']])){
        return $mall_info['domain'];
    }
    if($mall_info['url']=='yqf'){
        $urls=parse_uri($mall_info['url_'.$mall_info['url']]);
        $url=$urls['scheme']."://".$urls['host'].$urls['path']."?";
        foreach(array('s','w','c','i','l','e','t') as $val){
            if($val=='w'){
                $url.="$val=".C('pin_cps_'.$mall_info['url'])."&";
            }else{
                $url.="$val=".$urls['_query'][$val]."&";    
            }
        }
        return trim($url,'&');
    }else{
        return $mall_info['url_'.$mall_info['url']].'&sid='.C('pin_cps_'.$mall_info['url']);    
    }
        
}
function _exit($str){
    header("Content-Type: text/html; charset=utf-8");
    exit("<script>alert('$str');window.location.href='".u('user/logout')."';</script>");
}
function filter_data($data){
    foreach($data as $key=>$val){
        $data[$key]=strip_tags($val);
    }
    return $data;
}