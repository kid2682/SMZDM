<?php
class articleAction extends frontendAction {     
    public function index() {  
        $id=$this->_get('id','intval');
        if($res=$this->article_mod->where(array('id'=>$id))->find()){
            $this->assign('info',$res);
            $this->_config_seo(C('pin_seo_config.article'),array('article_title'=>$res['title'],
                'seo_title'=>$res['seo_title'],
                'keywords'=>$res['seo_keys'],
                'description'=>$res['seo_desc']));            
        }else{
            $this->redirect('/');
        }
        $this->display();        
    }
}