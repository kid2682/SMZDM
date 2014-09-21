<?php
class postAction extends frontendAction {     
    public function index() {        
        $id=intval($_REQUEST['id']);
        $post_key=$this->_get('post_key','trim');
        
        if(empty($id)){
            $where=array('post_key'=>$post_key);
        }else{
            $where=array('id'=>$id);
        }
        $where['post_time'] = array('elt',time());
        $where['status'] = 1;        
        $res=$this->post_mod->relation(true)->where($where)->find();
        if($res){               
            $res['cate_list']=$this->post_cate_re_mod->relation(true)->where(array('post_id'=>$res['id']))->select();
            $this->assign('info',$res);         
            $tag_list=$this->post_tag_mod->relation(true)->where("post_id=$res[id]")->select();
            
            $this->assign('tag_list',$tag_list);            
            $this->assign('prev_post',$this->post_mod->where("id>$res[id] and status=1 and post_time<=".time())->order("id asc")->find());            
            $this->assign('next_post',$this->post_mod->where("id<$res[id] and status=1 and post_time<=".time())->order("id desc")->find());
            $where="id in(select post_id from ".table('post_tag')." where 
                tag_id in(select tag_id from ".table('post_tag')." where post_id=$res[id]) 
                and post_id!=$res[id])";
           
            $this->assign('like_list',$this->post_mod->where($where)->limit(4)->select());
            $post_tag='';
            foreach($tag_list as $val){
                $post_tag.=$val['tag']['name'];
            }
            $this->_config_seo(C('pin_seo_config.post'),array('post_title'=>$res['title'],
                'post_tag'=>$post_tag,
                'user_name'=>$res['uname'],
                'seo_title'=>$res['seo_title'],
                'seo_keywords'=>$res['seo_keys'],
                'seo_description'=>$res['seo_desc']));
            $this->comment_list($res['id']);
        }else{
            $this->error("作品不存在");
        }
        
        $this->display();     
    }
    public function rate(){
        $type=$this->_post('type','trim');
        $id=$this->_post('id','intval');
        if(in_array($type,array('rate_best','rate_good','rate_bad'))){
            $where=array('id'=>$id);
            $this->post_mod->where($where)->setInc($type);
            $res=$this->post_mod->where($where)->find();
            $this->ajaxReturn(1,'',array(
                'total'=>$res['rate_best']+$res['rate_good']+$res['rate_bad'],
                'valid'=>$res['rate_best']+$res['rate_good']
            ));
        }
    }
    /**
     * 评论一个商品
     */
    public function comment() {
        foreach ($_POST as $key=>$val) {
            $_POST[$key] = Input::deleteHtmlTags($val);
        }
        $data = array();
        $data['post_id'] = $this->_post('id', 'intval');
        !$data['post_id'] && $this->ajaxReturn(0, L('invalid_item'));
        $data['info'] = $this->_post('content', 'trim');
        !$data['info'] && $this->ajaxReturn(0, L('please_input') . L('comment_content'));
        //敏感词处理
        $check_result = D('badword')->check($data['info']);
        switch ($check_result['code']) {
            case 1: //禁用。直接返回
                $this->ajaxReturn(0, L('has_badword'));
                break;
            case 3: //需要审核
                $data['status'] = 0;
                break;
        }
        $data['info'] = $check_result['content'];
        $data['uid'] = $this->visitor->info['id'];
        $data['uname'] = $this->visitor->info['username'];
        $data['add_time']=time();
        $data['pid']=$this->_post('pid','intval');
        //验证商品        
        $item = $this->post_mod->field('id,uid,uname')->where(array('id' => $data['post_id'], 'status' => '1'))->find();
        !$item && $this->ajaxReturn(0, L('invalid_item'));
        //写入评论
        if (false === $this->post_comment_mod->create($data)) {
            $this->ajaxReturn(0, $this->post_comment_mod->getError());
        }
        $comment_id = $this->post_comment_mod->add(filter_data($data));
        if ($comment_id) {
            $tag_arg = array('uid'=>$this->visitor->info['id'], 
                'uname'=>$this->visitor->info['username'], 
                'action'=>'comment');
            tag('comment_end', $tag_arg); 
                        
            $to_id=$this->_post('to_id','intval');
            if($to_id>0){
                $this->message_mod->add(array(
                    'ftid'=>$data['uid'],
                    'from_id'=>$data['uid'],
                    'from_name'=>$data['uname'],
                    'to_id'=>$this->_post('to_id','intval'),
                    'to_name'=>$this->_post('to_name','trim'),
                    'add_time'=>time(),
                    'info'=>$data['info'],
                ));    
            }
            $this->assign('cmt_list', array(
                array(
                    'id'=>$comment_id,
                    'uid' => $data['uid'],
                    'uname' => $data['uname'],
                    'info' => $data['info'],
                    'add_time' => time(),
                    'digg'=>0,
                    'burn'=>0,
                    'quote'=>$this->post_comment_mod->where(array('id'=>$data['pid']))->find(),
                    'user'=>$this->user_mod->where(array('id'=>$data['uid']))->find(),
                )
            ));
            $resp['html'] = $this->fetch('ajax_comment_list');
            $resp['total']=$this->post_comment_mod->where(array('post_id' => $data['post_id']))->count('id');  
                                 
            $this->ajaxReturn(1, L('comment_success'), $resp);
        } else {
            $this->ajaxReturn(0, L('comment_failed'));
        }
    }    
    public function comment_list($id){
        if(empty($id)){
            $id = $this->_get('id', 'intval');    
        }        
        !$id && $this->ajaxReturn(0, L('invalid_item'));        
        $post = $this->post_mod->where(array('id' => $id, 'status' => '1'))->count('id');
        !$post && $this->ajaxReturn(0, L('invalid_item'));        
        $pagesize = 8;
        $map = array('post_id' => $id);
        $count = $this->post_comment_mod->where($map)->count('id');
        $pager = $this->_pager($count, $pagesize);
        $pager->path = 'comment_list';
        $cmt_list = $this->post_comment_mod->relation(true)
            ->where($map)->order('id DESC')
            ->limit($pager->firstRow . ',' . $pager->listRows)->select();
        $floor=$count-$pager->firstRow;            
        foreach($cmt_list as $key=>$val){
            $cmt_list[$key]['quote']=$this->post_comment_mod->where(array('id'=>$val['pid']))->find();
            $cmt_list[$key]['floor']=$floor;
            $floor--;
        }
        $this->assign('cmt_list', $cmt_list);
        $data = array();
        $data['list'] = $this->fetch('ajax_comment_list');
        $data['page'] = $pager->fshow();
        $data['total']=$count;
        $this->assign('cmt_page',$data['page']);
        $this->assign('cmt_total',$data['total']); 
          
        if(IS_AJAX){                 
            $this->ajaxReturn(1,'',$data);    
        }
    }
    public function digg_burn(){        
        $id=$this->_get('id','intval');
        $type=$this->_get('type','trim');
        
        if(in_array($type,array('digg','burn'))){        
            $this->post_comment_mod->where(array('id'=>$id))->setInc($type);
            $this->ajaxReturn(1,'',$this->post_comment_mod->where(array('id'=>$id))->getField($type));    
        }
    }
    public function submit(){
        if(!$this->visitor->is_login){
            header("Location:".u('user/login'));
            //header("Location:./login.html");
            //print_r("Location:".u('user/login'));exit();  
        }  //$this->redirect('user/login');
        if(IS_POST){
            $data=$this->post_baoliao_mod->create();
            $type=intval($data['type']); 
               
            if($type==1){
                $data['title']='我要投稿';
            }
            elseif($type==2){
                $data['title']='改进建议';
            }            
            $data['info']=htmlentities($this->_post("info_$data[type]",'trim'));
            $data['uid']=$this->visitor->info['id'];
            $this->post_baoliao_mod->add(filter_data($data));   
             
            $tag_arg = array('uid'=>$this->visitor->info['id'], 
                'uname'=>$this->visitor->info['username'], 
                'action'=>'submit');
            tag('submit_end', $tag_arg);   
                             
            $this->ajaxReturn(1);
        }     
        $this->assign('page_seo',array('title'=>'用户爆料'));          
        $this->display();
    }
}