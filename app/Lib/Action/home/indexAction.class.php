<?php
class indexAction extends frontendAction {     
    public function index() {     
        $this->_assign_hot_list();
        $this->_assign_recommend_list();
        $where=array();
        ($keyword=$this->_get('keyword','trim'))&&$where['title']=array('like',"%$keyword%");
        if(empty($keyword)){
            $this->_config_seo(C('pin_seo_config.home'));    
        }else{
            $this->_config_seo(C('pin_seo_config.search'),array('keyword'=>$keyword));
        }
        
        $this->assign('search',array(
            'keyword'=>$keyword
        ));
        $where['post_time'] = array('elt',time());
        $where['status'] = 1;                  
        $this->_waterfall($this->post_mod,$where,'post_time desc');        
    }
    public function go(){        
        $id=$this->_get('id','intval');
        $url=trim($this->post_mod->where("id=$id")->getField("url"));        
        if(!empty($url)){
            header("Location:$url");
        }else{
            $this->error("为提供商品直达链接",U("index/index"));
        }
    }    
}