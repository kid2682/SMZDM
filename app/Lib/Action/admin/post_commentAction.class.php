<?php
class post_commentAction extends backendAction
{
    var $list_relation=true;
    protected function _search() {
        ($keyword = $this->_request('keyword', 'trim')) && $map['info|uname'] = array('like', '%'.$keyword.'%');      
        $this->assign('search', array(
            'keyword' => $keyword,
        ));
        return $map;
    }        
}