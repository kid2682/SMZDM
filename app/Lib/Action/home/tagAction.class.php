<?php
class tagAction extends frontendAction {     
    public function index() {    
        $id=$this->_get('id','intval');
        $res=$this->post_tag_mod->where(array('tag_id'=>$id))->select();
                
        $post_ids="0";
        foreach($res as $key=>$val){
            $post_ids.=",$val[post_id]";
        }     
        $this->assign('id',$id);  
        $info=$this->tag_mod->where(array('id'=>$id))->find(); 
        $this->_config_seo(array('title'=>$info['name'].'_标签'));          
        $this->_waterfall($this->post_mod,"id in($post_ids)",'post_time desc');        
    }
}