<?php
class post_baoliaoAction extends backendAction
{
    var $list_relation=true;
    public function _initialize() {
        parent::_initialize();
        $this->assign('type_list',array(
            '爆料','投稿','建议'
        ));
    }
    protected function _search() {
        $map = array();
        ($time_start = $this->_request('time_start', 'trim')) && $map['add_time'][] = array('egt', strtotime($time_start));
        ($time_end = $this->_request('time_end', 'trim')) && $map['add_time'][] = array('elt', strtotime($time_end)+(24*60*60-1));
        ($keyword = $this->_request('keyword', 'trim')) && $map['title'] = array('like', '%'.$keyword.'%');
        $type= $this->_request('type');        
        if($type!=null&&intval($type)>=0){
            $map['type'] =$type;  
        }
        
        $this->assign('search', array(
            'time_start' => $time_start,
            'time_end' => $time_end,
            'type' => $type,
            'keyword' => $keyword,
        ));
        return $map;
    }    
}