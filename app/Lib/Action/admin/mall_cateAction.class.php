<?php
class mall_cateAction extends backendAction {
    public function _initialize() {
        parent::_initialize();
        $this->_mod = D(MODULE_NAME);
    }
    function _before_index(){
        //bigmenu (标题，地址，弹窗ID，宽，高)
        $big_menu = array(
            'title' => L('add_cate'),
            'iframe' => U(MODULE_NAME.'/add'),
            'id' => 'add',
            'width' => '500',
            'height' => '360'
        );
        $this->assign('big_menu', $big_menu);        
    }  
    public function ajax_getchilds() {
        $id = $this->_get('id', 'intval');
        $return = $this->_mod->field('id,title')->where(array('pid'=>$id))->select();
        if ($return) {
            $this->ajaxReturn(1, L('operation_success'), $return);
        } else {
            $this->ajaxReturn(0, L('operation_failure'));
        }
    }    
}