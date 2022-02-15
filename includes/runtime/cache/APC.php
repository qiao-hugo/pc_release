<?php
class Vtiger_APC{

    /**
     * 架构函数
     * @param array $options 缓存参数
     * @access public
     */
	private $handler=null;
	private $pix='crm_';
    function __construct() {
		if (!function_exists('apc_cache_info')) {
            throw new CacheException('apc extension didn\'t installed');
        }
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function get($name) {
        return apc_fetch($this->pix.$name);
    }

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param integer $expire  有效时间（秒）
     * @return boolen
     */
    public function set($name, $value,$time=null) {
		if($time==0) $time=null;
		apc_store($this->pix.$name, $value,$time);
    }

    /**
     * 清除缓存
     * @access public
     * @return boolen
     */
    public function clear() {
        apc_clear_cache('user');
        return $this;
    }
}