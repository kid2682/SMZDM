<?php
class mallAction extends frontendAction {     
    public function index() {
        $cate_list=$this->mall_cate_mod->where("status=1")->order('ordid')->select();
        foreach($cate_list as $key=>$val){
            $cate_list[$key]['child']=$this->mall_mod->where("status=1 and cid=$val[id]")->order('ordid')->select();            
        }        
        $this->assign('list',$cate_list);
        
        $this->_config_seo(C('pin_seo_config.mall'));           
        $this->display();     
    }
    public function info(){
        $id=intval($_REQUEST['id']);
        $res=$this->mall_mod->where("id=$id")->find();
        if($res){
            $res['post_list']=$this->post_mod->where("mall_id=$res[id] and status=1 and post_time<=".time())->limit('0,10')->select();
            $this->assign('info',$res);
            $this->_config_seo(C('pin_seo_config.mall_info'),array('mall_title'=>$res['title'],
                'seo_title'=>$res['seo_title'],
                'seo_keywords'=>$res['seo_keys'],
                'seo_description'=>$res['seo_desc']));   
        }else{
            $this->error("商城不存在",u('index/index'));
        }
        $this->display();
    }
}