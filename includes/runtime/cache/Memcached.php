<?php
class Vtiger_Memcached{

    /**
     * 架构函数
     * @param array $options 缓存参数
     * @access public
     */
	private $handler=null;
	private $pix='vtiger_';
    function __construct() {
        $this->handler	=	new Memcache;
        $this->handler->connect('127.0.0.1',11211);
		
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function get($name) {
        return unserialize($this->handler->get($this->pix.$name));
    }

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param integer $expire  有效时间（秒）
     * @return boolen
     */
    public function set($name, $value) {
        $this->handler->set($this->pix.$name,serialize($value));
    }
	
	public function getExtendedStats($cmd){
		return $this->handler->getExtendedStats($cmd);
	}
    /**
     * 清除缓存
     * @access public
     * @return boolen
     */
    public function clear() {
        return $this->handler->flush();
    }
}
