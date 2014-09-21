<?php
class postAction extends backendAction
{
    var $list_relation=true;
    var $spec_chars=array('*','-',',','.','，','。','|','<','>','(',')','《','》','+','/');
    public function _initialize() {
        parent::_initialize();
        $this->_mod = D('post');
        $this->_cate_mod = D('post_cate');
        $this->assign('img_dir','./data/upload/post/');
        $this->py = new cls_pinyin();
    }

    public function _before_index() {        
        $res = $this->mall_mod->field('id,title')->select();
        $mall_list = array();
        foreach ($res as $val) {
            $mall_list[$val['id']] = $val['title'];
        }
        $this->assign('mall_list', $mall_list);        

        //默认排序
        $this->sort = 'id';
        $this->order = 'desc';                
    }

    protected function _search() {
        $map = array();
        ($time_start = $this->_request('time_start', 'trim')) && $map['post_time'][] = array('egt', strtotime($time_start));
        ($time_end = $this->_request('time_end', 'trim')) && $map['post_time'][] = array('elt', strtotime($time_end)+(24*60*60-1));
        $status = $this->_request('status'); 
        if($status!=null){
            $map['status'] = $status;
        }
        ($keyword = $this->_request('keyword', 'trim')) && $map['title'] = array('like', '%'.$keyword.'%');
        $cate_id = $this->_request('cate_id', 'intval');
        $selected_ids = '';
        if ($cate_id) {
            $id_arr = $this->_cate_mod->get_child_ids($cate_id, true);
            $cids="0";
            foreach($id_arr as $val){
                $cids.=','.$val;
            }
            $res=$this->post_cate_re_mod->where("cate_id in($cids)")->select();
            $ids="0";
            foreach($res as $val){
                $ids.=",".$val['post_id'];
            }
            $map['id'] = array('IN', $ids);
            $spid = $this->_cate_mod->where(array('id'=>$cate_id))->getField('spid');
            $selected_ids = $spid ? $spid . $cate_id : $cate_id;
        }
        $mall_id=$this->_request('mall_id','intval');
        if($mall_id>0){
            $map['mall_id']=$mall_id;   
        }
        $this->assign('search', array(
            'time_start' => $time_start,
            'time_end' => $time_end,
            'cate_id' => $cate_id,
            'selected_ids' => $selected_ids,
            'status'  => $status,
            'keyword' => $keyword,
            'mall_id'=>$mall_id
        ));
        return $map;
    }
    protected function _get_cate_tree($list,$checked_ids=array()){
        $html="";
        
        foreach($list as $key=>$val){
            $margin_left=$val['depth']*20;
            $html.="<div style='margin-left:".$margin_left."px;'>
                <input type='checkbox'";
            if(in_array($val['id'],$checked_ids)){
                $html.=" checked='checked' ";
            }                
            $html.=" name='cate_id[]' value='$val[id]'/>&nbsp;&nbsp;$val[name]</div>";
            $html.=$this->_get_cate_tree($val['child'],$checked_ids);
        }
        return $html;
    }
    public function _before_add()
    {              
        $info['author'] = $_SESSION['admin']['username'];  
                      
        $this->assign('info',$info);
        $cate_tree=$this->_get_cate_tree(get_cate_tree(M("post_cate")));              
        $this->assign('cate_tree',$cate_tree);                    
    }

    protected function _before_insert($data) {                
        //上传图片
        if (!empty($_FILES['img']['name'])) {
            $art_add_time = date('ym/d');
            $result = $this->_upload($_FILES['img'], 'post/' . $art_add_time);
            if ($result['error']) {
                $this->error($result['info']);
            } else {
                $data['img'] = $art_add_time .'/'. $result['info'][0]['savename'];
            }
        }
        $data['post_time']=strtotime($this->_request('post_time','trim'));
        if(empty($data['post_key'])){
            $data['post_key']=$this->py->tourl($data['title']);
        }
        if($this->post_mod->where(array('post_key'=>trim($data['post_key'])))->count()>0){
            $data['post_key'].='_'.time();
        }
        $data['post_key']=str_replace($this->spec_chars,'',$data['post_key']);
        return $data;
    }
    protected function _after_insert($id) {
        $cids=$_REQUEST['cate_id'];
        foreach($cids as $key=>$val){
            M("post_cate_re")->add(array(
                'post_id'=>$id,
                'cate_id'=>$val,
            ));
        }
        //tag
        $where=array('post_id'=>$id);
        $tags=$this->update_tag(M("post_tag"),$where,$data['title']);
        $this->post_tag_mod->where($where)->delete();
        foreach($tags as $key=>$val){
            $this->post_tag_mod->add(array(
                'post_id'=>$id,
                'tag_id'=>$key,
            ));
        }             
    }
    public function _after_edit($data){
        $where=array('post_id'=>$data['id']);
        $ids=array();                
        $list=M("post_cate_re")->where($where)->select();        
        foreach($list as $key=>$val){
            $ids[]=$val['cate_id'];
        }        
        $cate_tree=$this->_get_cate_tree(get_cate_tree(M("post_cate")),$ids);
                      
        $this->assign('cate_tree',$cate_tree);
                 
        $this->assign("mall_index",$this->mall_mod->where(array('id'=>$data['mall_id']))->getField("index")); 
        //tag
        $tag_list=D("post_tag")->relation(true)->where($where)->select();
        
        foreach($tag_list as $key=>$val){
            $tags.=" ".$val['tag']['name']." ";
        }            
        $this->assign("tags",$tags);
    }

    protected function _before_update($data) {        
        M("post_cate_re")->where(array('post_id'=>$data['id']))->delete();
        $cids=$_REQUEST['cate_id'];
        foreach($cids as $key=>$val){
           M("post_cate_re")->add(array(
                'post_id'=>$data['id'],
                'cate_id'=>$val,
            ));
        }        
        //tag
        $where=array('post_id'=>$data['id']);
        $tags=$this->update_tag(M("post_tag"),$where,$data['title']);
        $this->post_tag_mod->where($where)->delete();
        foreach($tags as $key=>$val){
            $this->post_tag_mod->add(array(
                'post_id'=>$data['id'],
                'tag_id'=>$key,
            ));
        }
        if (!empty($_FILES['img']['name'])) {
            $art_add_time = date('ym/d');
            //删除原图
            $old_img = $this->_mod->where(array('id'=>$data['id']))->getField('img');
            $old_img = $this->_get_imgdir() . $old_img;
            is_file($old_img) && @unlink($old_img);
            
            //上传新图
            $result = $this->_upload($_FILES['img'], 'post/' . $art_add_time);
            if ($result['error']) {
                $this->error($result['info']);
            } else {
                $data['img'] = $art_add_time .'/'. $result['info'][0]['savename'];
            }
        } else {
            unset($data['img']);
        }
        $data['post_time']=strtotime($this->_request('post_time','trim'));
        //print_r($data);exit();
        if(empty($data['post_key'])){
            $data['post_key']=$this->py->tourl($data['title']);
        }
        if($this->post_mod->where("post_key='$data[post_key]' and id!=$data[id]")->count()>0){
            $data['post_key'].='_'.time();
        }        
        $data['post_key']=str_replace($this->spec_chars,'',$data['post_key']);
        return $data;
    }
    public function _before_drop($ids){
        foreach ($ids as $val) {
            if ($info=M(MODULE_NAME)->where(array('id'=>$val))->find()) {                
                @unlink(attach($info['img'],MODULE_NAME,true));
            }
        }        
    }
    private function _get_imgdir() {
        static $dir = null;
        if ($dir === null) {
            $dir = './data/upload/post/';
        }
        return $dir;
    }  
    public function ajax_mall_list(){
        $index=$this->_post('index','trim');
        $res=$this->mall_mod->where(array('index'=>$index))->select();
        $data="";
        foreach($res as $key=>$val){
            $data.="<option value='$val[id]'>$val[title]</option>";
        }
        $this->ajaxReturn(1,'',$data);
    }  
    public function ajax_post_key(){        
        echo $this->py->tourl($this->_post('title'));         
    }
}