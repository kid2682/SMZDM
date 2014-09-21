<?php
class baseAction extends Action
{
protected function _initialize() {
$this->_domain_auth();
Input::noGPC();
if (false === $setting = F('setting')) {
$setting = D('setting')->setting_cache();
}
C($setting);
$this->assign('async_sendmail',session('async_sendmail'));
$this->mod = new Model();
S('table_list') == null &&S('table_list',$this->mod->db->getTables());
foreach (S('table_list') as $val) {
$name = substr($val,strlen(C('DB_PREFIX')));
$mod_name = $name .'_mod';
$evalstr = "\$this->$mod_name=D('$name');\n";
eval($evalstr);
}
$this->assign('server',$_SERVER);
}
public function _empty() {
$this->_404();
}
protected function _404($url = '') {
if ($url) {
redirect($url);
}else {
send_http_status(404);
$this->display(TMPL_PATH .'404.html');
exit;
}
}
protected function _mail_queue($to,$subject,$body,$priority = 1) {
$to_emails = is_array($to) ?$to : array($to);
$mails = array();
$time = time();
foreach ($to_emails as $_email) {
$mails[] = array(
'mail_to'=>$_email,
'mail_subject'=>$subject,
'mail_body'=>$body,
'priority'=>$priority,
'add_time'=>$time,
'lock_expiry'=>$time,
);
}
M('mail_queue')->addAll($mails);
$this->send_mail(false);
}
public function send_mail($is_sync = true) {
if (!$is_sync) {
session('async_sendmail',true);
return true;
}else {
session('async_sendmail',null);
return D('mail_queue')->send();
}
}
protected function _upload_init($upload) {
$allow_max = C('pin_attr_allow_size');
$allow_exts = explode(',',C('pin_attr_allow_exts'));
$allow_max &&$upload->maxSize = $allow_max * 1024;
$allow_exts &&$upload->allowExts = $allow_exts;
$upload->saveRule = 'uniqid';
return $upload;
}
protected function _upload($file,$dir = '',$thumb = array(),$save_rule='uniqid') {
$upload = new UploadFile();
if ($dir) {
$upload_path = C('pin_attach_path') .$dir .'/';
$upload->savePath = $upload_path;
}
if ($thumb) {
$upload->thumb = true;
$upload->thumbMaxWidth = $thumb['width'];
$upload->thumbMaxHeight = $thumb['height'];
$upload->thumbPrefix = '';
$upload->thumbSuffix = isset($thumb['suffix']) ?$thumb['suffix'] : '_thumb';
$upload->thumbExt = isset($thumb['ext']) ?$thumb['ext'] : '';
$upload->thumbRemoveOrigin = isset($thumb['remove_origin']) ?true : false;
}
$upload = $this->_upload_init($upload);
if( $save_rule!='uniqid'){
$upload->saveRule = $save_rule;
}
if ($result = $upload->uploadOne($file)) {
return array('error'=>0,'info'=>$result);
}else {
return array('error'=>1,'info'=>$upload->getErrorMsg());
}
}
protected function ajaxReturn($status=1,$msg='',$data='',$dialog='') {
parent::ajaxReturn(array(
'status'=>$status,
'msg'=>$msg,
'data'=>$data,
'dialog'=>$dialog,
));
}
public function ajax_gettags() {
$title = $this->_get('title','trim');
if ($title) {
$tags = D('tag')->get_tags_by_title($title);
$tags = implode(' ',$tags);
$this->ajaxReturn(1,L('operation_success'),$tags);
}else {
$this->ajaxReturn(0,L('operation_failure'));
}
}
protected function update_tag($mod,$where,$title=''){
$tags = $this->_post('tags','trim');
$id=$this->_post('id','intval');
if (!isset($tags) ||empty($tags)) {
$tag_list = D('tag')->get_tags_by_title($title);
}else {
$tag_list = explode(' ',$tags);
}
if ($tag_list) {
$item_tag_arr = $tag_cache = array();
$tag_mod = M('tag');
foreach ($tag_list as $_tag_name) {
$tag_id = $tag_mod->where(array('name'=>$_tag_name))->getField('id');
!$tag_id &&$tag_id = $tag_mod->add(array('name'=>$_tag_name));
$item_tag_arr[] =array_merge($where,array('tag_id'=>$tag_id));
$tag_cache[$tag_id] = $_tag_name;
}
}
return $tag_cache;
}
private function _domain_auth() {
$auth_file = PIN_DATA_PATH .'domain.key';
if (!is_file($auth_file)) {
header( "Content-Type: text/html; charset=utf-8");
exit('没有授权文件');
}
$auth_str = file_get_contents($auth_file);
$host = $_SERVER['HTTP_HOST'];
$host = explode(":",$host);
$host = $host[0];
$auth_arr = explode('#',$auth_str);
$passed = false;
foreach ($auth_arr as $val) {
$auth_domain = base64_decode(substr(unserialize(base64_decode($val)),0,-10));
if(substr($auth_domain,0,2) == '*.') {
$preg_str = substr($auth_domain,2);
if(preg_match("/".$preg_str ."$/",$host) >0) {
$passed = true;
break;
}
}else if($host==$host){
$passed = true;
break;
}
}
if (!$passed) {
header( "Content-Type: text/html; charset=utf-8");
exit('本域名没有经过授权，请到http;//bbs.52jscn.com获得授权！');
}
}
}