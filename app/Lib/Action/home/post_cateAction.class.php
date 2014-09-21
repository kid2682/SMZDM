<?php
class post_cateAction extends frontendAction {     
    public function index() {
        $this->_assign_hot_list();
        $cate_id=intval($_REQUEST['id']);
        $title=trim($_REQUEST['title']);
        $this->assign('id',$cate_id);
        $info=$this->post_cate_mod->where(array('id'=>$cate_id))->find();
        $this->assign('info',$info);
        $this->_config_seo(C('pin_seo_config.cate'),array('cate_name'=>$info['name'],
                'seo_title'=>$info['seo_title'],
                'seo_keywords'=>$info['seo_keys'],
                'seo_description'=>$info['seo_desc']));         
        $where="1 ";
        if($cate_id>0){
            $where.=" and id=$cate_id ";
        }
        if($this->post_cate_mod->where($where)->getField("pid")==0){
            $cids=$this->post_cate_mod->where("pid=$cate_id")->select();
            foreach($cids as $val){
                $cate_id.=",$val[id]";
            }    
        }
        
        $ids=$this->post_cate_re_mod->where("cate_id in($cate_id)")->select();
        $post_ids="0";
        foreach($ids as $val){
            $post_ids.=",$val[post_id]";
        }
               
        $this->_waterfall($this->post_mod,"id in($post_ids) and status=1 and post_time<=".time(),'post_time desc');        
    }
}