<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

include_once dirname(__FILE__) . '/Connectors.php';
include_once dirname(__FILE__) . '/Memcached.php';
include_once dirname(__FILE__) . '/APC.php';

class Vtiger_Cache_Connector {
	protected $connection;

	protected function __construct() {
		if (!$this->connection) {
			/* if(extension_loaded('memcache')){//自动判断采用哪个缓存方式 
				$this->connection = new Vtiger_Memcached();
			}else{
				$this->connection = new Vtiger_Cache_Connector_Memory();
			} */
			//$this->connection = new Vtiger_Cache_Connector_Memory();
			$this->connection = new Vtiger_Cache_Connector_Memory();
			#$this->connection = new Vtiger_APC();
			#	$this->connection = new Vtiger_Memcached();
		}
	}

	protected function cacheKey($ns, $key) {
		if(is_array($key)) $key = implode('-', $key);
		return $ns . '-' . $key;
	}

	public function set($namespace, $key, $value,$time=null) {
		$this->connection->set($this->cacheKey($namespace, $key), $value,$time);
	}

	public function get($namespace, $key) {
		return $this->connection->get($this->cacheKey($namespace, $key));
	}

	public function has($namespace, $key) {
		return $this->get($namespace, $key) !== false;
	}

    public function flush(){
        $this->connection->flush();

        $time = time()+1; //one second future
        while(time() < $time) {
            //sleep
        }
    }
    public function clear(){
        $this->connection->clear();
    }
    public static function getInstance() {
		static $singleton = NULL;
		if ($singleton === NULL) {
			$singleton = new self();
		}
		return $singleton;
	}
}
