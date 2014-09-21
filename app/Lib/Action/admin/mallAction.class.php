<?php
class mallAction extends backendAction {
    var $list_relation=true;
    public function _initialize() {
        parent::_initialize();
        $this->img_dir="./data/upload/mall/";
        $this->assign('img_dir',$this->img_dir);
        $this->assign('cate_list',$this->_get_cate_list());
        $this->assign('index_list',get_index());
    }    
    protected function _search() {
        $map = array();
        ($cid = $this->_get('cid', 'intval'))>0 && $map['cid'] = array('eq', $cid);
        $status = $this->_get('status');
        if ($status!=null) {
            $map['status'] = array('eq', intval($status));
        }
        ($keyword = $this->_get('keyword', 'trim')) && $map['title|domain'] = array('like', '%'.$keyword.'%');
        $url = $this->_get('url', 'trim');
        if(!empty($url)&&$url!=-1){
            if($url=='empty'){
                $map['url']=array('eq',"");   
            }else{
                $map['url'] =$url;    
            }
        }         
        $this->assign('search', array(
            'keyword' => $keyword,
            'cid' => $cid,
            'status' => $status,
            'url'=>$url,
        ));        
        return $map;
    }
    
    protected function _before_insert($data) {                
        //上传图片
        if (!empty($_FILES['img']['name'])) {
            $art_add_time = date('ym/d');
            $result = $this->_upload($_FILES['img'], 'mall/' . $art_add_time);
            if ($result['error']) {
                $this->error($result['info']);
            } else {                
                $data['img'] = $art_add_time .'/'.$result['info'][0]['savename'];
            }
        }
        ($post_time=$this->_get('post_time','trim'))&&$data['post_time']=strtotime($post_time);
        return $data;
    }   
    protected function _before_update($data) {
        if (!empty($_FILES['img']['name'])) {
            $art_add_time = date('ym/d');
            //删除原图
            $old_img = $this->mall_mod->where(array('id'=>$data['id']))->getField('img');
            $old_img = $this->img_dir. $old_img;
            is_file($old_img) && @unlink($old_img);
            
            //上传新图
            $result = $this->_upload($_FILES['img'], 'mall/' . $art_add_time);
            if ($result['error']) {
                $this->error($result['info']);
            } else {
                $data['img'] = $art_add_time .'/'.$result['info'][0]['savename'];
            }
        } else {
            unset($data['img']);
        }
        ($post_time=$this->_get('post_time','trim'))&&$data['post_time']=strtotime($post_time);
        return $data;
    }   
    public function _before_drop($ids){
        foreach ($ids as $val) {
            if ($info=M(MODULE_NAME)->where(array('id'=>$val))->find()) {                
                @unlink(attach($info['img'],MODULE_NAME,true));
            }
        }        
    }      
    protected function _get_cate_list(){
        $res=$this->mall_cate_mod->where("status=1")->order("ordid")->select();
        $list=array();
        foreach($res as $key=>$val){
            $list[$val['id']]=$val['title'];
        }
        return $list;
    }           
}