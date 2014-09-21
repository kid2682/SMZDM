<?php
/**
 * TOP API: taobao.widget.loginstatus.get request
 * 
 * @author auto create
 * @since 1.0, 2012-11-05 12:39:25
 */
class WidgetLoginstatusGetRequest
{
	/** 
	 * 指定判断当前浏览器登录用户是否此昵称用户，并且返回是否登录。如果用户不一致返回未登录，如果用户一致且已登录返回已登录
	 **/
	private $nick;
	
	private $apiParas = array();
	
	public function setNick($nick)
	{
		$this->nick = $nick;
		$this->apiParas["nick"] = $nick;
	}

	public function getNick()
	{
		return $this->nick;
	}

	public function getApiMethodName()
	{
		return "taobao.widget.loginstatus.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
