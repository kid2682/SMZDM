<?php
/**
 * 前台控制器基类
 *
 * @author andery
 */
class frontendAction extends baseAction {

    protected $visitor = null;
    protected $uid;
    public function _initialize() {
        parent::_initialize();
        //print_r($_SESSION);exit();
        //网站状态
        if (!C('pin_site_status')) {
            header('Content-Type:text/html; charset=utf-8');
            exit(C('pin_closed_reason'));
        }
        //初始化访问者
        $this->_init_visitor();
        //第三方登录模块
        $this->_assign_oauth();
        //网站导航选中
        $this->assign('nav_curr', '');
        $this->assign('recommend_cate',$this->post_cate_mod->where("pid=1 and status=1")->select());
        $this->assign('tese_cate',$this->post_cate_mod->where("pid=2 and status=1")->select());
        $this->assign('main_nav_list',$this->nav_mod->where("type='main' and status=1")->order('ordid')->select());
        $this->assign('bottom_nav_list',$this->nav_mod->where("type='bottom' and status=1")->order('ordid')->select());
        $this->assign('new_post_list',$this->post_mod->where("status=1")->limit("9")->order("id desc")->select());
        $this->assign('flink_list',$this->flink_mod->where("status=1")->order("ordid desc")->select());
        $help_list=$this->article_cate_mod->where("pid=1 and status=1")->select();
        foreach($help_list as $key=>$val){
            $help_list[$key]['articles']=$this->article_mod->where("cate_id=$val[id]")->select();
        }        
        $this->assign('help_list',$help_list);
        $this->assign('gonggao_list',$this->article_mod->where('cate_id=13 and status=1')->order("ordid desc")->select());
        $this->uid=$this->visitor->info['id'];     
        $this->assign('def',json_encode(array('m'=>MODULE_NAME,'a'=>ACTION_NAME)));   
        $this->assign('req',$_REQUEST);
    }
    
    /**
    * 初始化访问者
    */
    private function _init_visitor() {
        $this->visitor = new user_visitor();                
        $this->assign('visitor', $this->visitor->info);
    }

    /**
     * 第三方登录模块
     */
    private function _assign_oauth() {
        if (false === $oauth_list = F('oauth_list')) {
            $oauth_list = D('oauth')->oauth_cache();
        }        
        $this->assign('oauth_list', $oauth_list);
    }

    /**
     * SEO设置
     */
    protected function _config_seo($seo_info = array(), $data = array()) {
        $page_seo = array(
            'title' => C('pin_site_title'),
            'keywords' => C('pin_site_keyword'),
            'description' => C('pin_site_description')
        );
        $page_seo = array_merge($page_seo, $seo_info);
        //开始替换        
        $searchs = array('{site_name}', '{site_title}', '{site_keywords}', '{site_description}');
        $replaces = array(C('pin_site_name'), C('pin_site_title'), C('pin_site_keyword'), C('pin_site_description'));
        preg_match_all("/\{([a-z0-9_-]+?)\}/", implode(' ', array_values($page_seo)), $pageparams);        
        if ($pageparams) {
            foreach ($pageparams[1] as $var) {
                $searchs[] = '{' . $var . '}';
                $replaces[] = $data[$var] ? strip_tags($data[$var]) : '';
            }
            //符号
            $searchspace = array('((\s*\-\s*)+)', '((\s*\,\s*)+)', '((\s*\|\s*)+)', '((\s*\t\s*)+)', '((\s*_\s*)+)');
            $replacespace = array('-', ',', '|', ' ', '_');
            foreach ($page_seo as $key => $val) {
                $page_seo[$key] = trim(preg_replace($searchspace, $replacespace, str_replace($searchs, $replaces, $val)), ' ,-|_');
            }
        }
        if($page_seo['title']!=C('pin_site_title')){
            //$page_seo['title'].="_".C('pin_site_name');
        }
        $this->assign('page_seo', $page_seo);
    }

    /**
    * 连接用户中心
    */
    protected function _user_server() {
        $passport = new passport(C('pin_integrate_code'));
        return $passport;
    }

    /**
     * 前台分页统一
     */
    protected function _pager($count, $pagesize) {
        $pager = new Page($count, $pagesize);
        $pager->rollPage = 5;
        $pager->setConfig('prev', '<');
        $pager->setConfig('theme', '%upPage% %first% %linkPage% %end% %downPage%');
        return $pager;
    }
    protected function _parse_post($list){
        foreach($list as $key=>$val){
            $list[$key]['cate_list']=$this->post_cate_re_mod->relation(true)->where(array('post_id'=>$val['id']))->select();                        
        }        
        return $list;
    }    
    /*
     * 商品瀑布流
     * */
    protected function _waterfall($mod,$where='',$order = "",$pagesize=5) {        
        import("ORG.Util.Page");    

        $p = !empty($_GET['p']) ? intval($_GET['p']) : 1;
        $sp = !empty($_GET['sp']) ? intval($_GET['sp']) : 1;
        $ajax = !empty($_GET['ajax']) ? true : false;
        $sp > C('pin_wall_spage_max') && exit;

        $list_rows = C('pin_wall_spage_max') * C('pin_wall_spage_size');
        $s_list_rows = C('pin_wall_spage_size');
        $show_sp = 0;
        
        $count=$mod->where($where)->count();
        $count > $s_list_rows && $show_sp = 1;
        $pager = new Page($count, $list_rows);
        $pager->setConfig('theme','%first% %upPage% %linkPage% %downPage% %end%');
        
        $first_row = $pager->firstRow + $s_list_rows * ($sp - 1);
        $items_list = $mod->relation(true)->where($where)
                ->limit($first_row . ',' . $s_list_rows)->order($order)
                ->select();
        //print_r($mod->getLastSql());exit();
        $this->assign('page', $pager->show());
        $this->assign('p', $p);
        $this->assign('show_sp', $show_sp);
        $this->assign('sp', $sp);
        $_parse='_parse_'.$mod->getModelName();
                
        if(method_exists($this, $_parse)){            
            eval('$items_list=$this->'.$_parse.'($items_list);');            
        }
        $_before='_before_'.ACTION_NAME;
        if(method_exists($this, $_before)){
            eval('$items_list=$this->'.$_before.'($items_list);');
        }
        $this->assign('show_load', 1);
        //总数大于单页数才显示分页
        $this->assign('page_bar', $pager->fshow());        
        //print_r($items_list);exit();
        $this->assign($mod->getModelName().'_list', $items_list);
        if (IS_AJAX&& $sp >=2 || $ajax) {
            $resp = $this->fetch('public:ajax_'.$mod->getModelName().'_list');
            //print_r();
            $data = array(
                'isfull' => 1,
                'html' => $resp
            );            
            $this->ajaxReturn(1, '', $data);
        }
        else {
            $this->display();
        }
    }
    /**
     * 瀑布显示
     */
    public function waterfall($where = array(), $order = 'id DESC', $field = '', $page_max = '', $target = '') {
        $spage_size = C('pin_wall_spage_size'); //每次加载个数
        $spage_max = C('pin_wall_spage_max'); //每页加载次数
        $page_size = $spage_size * $spage_max; //每页显示个数
        
        $item_mod = M('item');
        $where_init = array('status'=>'1');
        $where = $where ? array_merge($where_init, $where) : $where_init;
        $count = $item_mod->where($where)->count('id');
        //控制最多显示多少页
        if ($page_max && $count > $page_max * $page_size) {
            $count = $page_max * $page_size;
        }
        //查询字段
        $field == '' && $field = 'id,uid,uname,title,intro,img,price,likes,comments,comments_cache';
        //分页
        $pager = $this->_pager($count, $page_size);
        $target && $pager->path = $target;
        $item_list = $item_mod->field($field)->where($where)->order($order)->limit($pager->firstRow.','.$spage_size)->select();
        foreach ($item_list as $key=>$val) {
            isset($val['comments_cache']) && $item_list[$key]['comment_list'] = unserialize($val['comments_cache']);
        }
        $this->assign('item_list', $item_list);
        //当前页码
        $p = $this->_get('p', 'intval', 1);
        $this->assign('p', $p);
        //当前页面总数大于单次加载数才会执行动态加载
        if (($count - ($p-1) * $page_size) > $spage_size) {
            $this->assign('show_load', 1);
        }
        //总数大于单页数才显示分页
        $count > $page_size && $this->assign('page_bar', $pager->fshow());
        //最后一页分页处理
        if ((count($item_list) + $page_size * ($p-1)) == $count) {
            $this->assign('show_page', 1);
        }
    }

    /**
     * 瀑布加载
     */
    public function wall_ajax($where = array(), $order = 'id DESC', $field = '') {
        $spage_size = C('pin_wall_spage_size'); //每次加载个数
        $spage_max = C('pin_wall_spage_max'); //加载次数
        $p = $this->_get('p', 'intval', 1); //页码
        $sp = $this->_get('sp', 'intval', 1); //子页

        //条件
        $where_init = array('status'=>'1');
        $where = array_merge($where_init, $where);
        //计算开始
        $start = $spage_size * ($spage_max * ($p - 1) + $sp);
        $item_mod = M('item');
        $count = $item_mod->where($where)->count('id');
        $field == '' && $field = 'id,uid,uname,title,intro,img,price,likes,comments,comments_cache';
        $item_list = $item_mod->field($field)->where($where)->order($order)->limit($start.','.$spage_size)->select();
        foreach ($item_list as $key=>$val) {
            //解析评论
            isset($val['comments_cache']) && $item_list[$key]['comment_list'] = unserialize($val['comments_cache']);
        }
        $this->assign('item_list', $item_list);
        $resp = $this->fetch('public:waterfall');
        $data = array(
            'isfull' => 1,
            'html' => $resp
        );
        $count <= $start + $spage_size && $data['isfull'] = 0;
        $this->ajaxReturn(1, '', $data);
    }
    protected function _assign_hot_list(){
        $hot_list=$this->post_mod->where("is_hot=1 and status=1 and post_time<=".time())->order("ordid")->limit("0,8")->select();
        $this->assign('hot_list',$hot_list);        
    }
    protected function _assign_recommend_list(){
        $recommend_list=$this->post_mod->where("is_recommend=1 and status=1 and post_time<=".time())->order("ordid")->limit("0,8")->select();
        $this->assign('recommend_list',$recommend_list);        
    }
    protected function _assign_list($mod, $where, $page_size = 15, $relation = false) {
        import("ORG.Util.Page");
        $count = $mod->where($where)->count();

        $pager =$this->_pager($count, $page_size);
        $select = $mod->where($where)->order('id desc')->limit($pager->firstRow . ',' .
                $pager->listRows);
        if ($relation) {
            $select = $select->relation($relation);
        }
        $list = $select->select();        
        $this->assign('list', $list);
        $this->assign('page', $pager->show());
        return $list;
    }    
}