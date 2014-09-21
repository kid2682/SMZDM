<?php
class helpAction extends frontendAction {     
    public function _initialize() {
        parent::_initialize();
        $this->assign('href',trim($_SERVER['REDIRECT_URL'],'/'));         
    }
    public function index() {
        $id=$this->_get('id','intval');
        
        $res=$this->article_mod->where('id='.$id)->find();
        if($res){
            $this->assign('info',$res);
            $this->_config_seo(array('title'=>$res['title'],
                'keywords'=>$res['seo_keys'],
                'description'=>$res['seo_desc']));               
        }else{
            header("Location:/");
        }
        $this->display();     
    }
    public function page(){
        $id=$this->_get('id','intval');
        
        $res=$this->article_page_mod->where('cate_id='.$id)->find();
        if($res){
            $this->assign('info',$res);
            $this->_config_seo(array('title'=>$res['title'],
                'keywords'=>$res['seo_keys'],
                'description'=>$res['seo_desc']));               
        }else{
            header("Location:/");
        }
        $this->display('index');             
    }
    public function faq(){
        $cate_id=$this->_get('cate_id','intval');
        $this->assign('cate_id',$cate_id);
        $cate_info=$this->article_cate_mod->where(array('id'=>$cate_id))->find();
        $this->assign('cate_info',$cate_info);
        $this->_config_seo(C('pin_seo_config.article'),array('article_title'=>$cate_info['name'],
                'seo_title'=>$res['seo_title'],
                'keywords'=>$res['seo_keys'],
                'description'=>$res['seo_desc']));          
        $res=$this->article_mod->where(array('cate_id'=>$cate_id))->select();
        $this->assign('faq_list',$res);
        $this->display();
    }   
    public function flink(){
        $this->_config_seo(C('pin_seo_config.article'),array('article_title'=>'友情链接',
                'seo_title'=>$res['seo_title'],
                'keywords'=>$res['seo_keys'],
                'description'=>$res['seo_desc']));            
        $res=$this->flink_mod->where("status=1")->order('ordid desc')->select();
        $this->assign('flink_list',$res);
        $this->display();
    } 
}