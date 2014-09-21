<?php
/*禁止IP访问*/

defined('THINK_PATH') or exit();

class check_ipbanBehavior extends Behavior {

    public function run(&$params){
        if (false === $setting = F('setting')) {
            $setting = D('setting')->setting_cache();
        }
        if (!$setting['pin_ipban_switch']) return false;
        $ip = get_client_ip();
        $ipban_mod = D('ipban');
        $ipban_mod->clear(); //清除过期数据
        $isban = $ipban_mod->where(array('type'=>'ip', 'name'=>$ip))->count();
        $isban && exit('对不起，您的IP被禁止访问本站！');
        
        session_start();
        //if(!$_SESSION['check_ipban']){
            $user_info=M("user")->where("id=".intval($_SESSION['user_info']['id']))->find();
            if($ipban_mod->where("name='$user_info[username]' and type='uname'")->count()>0){                   
                _exit("用户名被列入黑名单");
            }
            if($ipban_mod->where("name='$user_info[email]' and type='email'")->count()>0){                
                _exit("邮箱被列入黑名单");
            }
        //    $_SESSION['check_ipban']=true;            
        //}
    }
}