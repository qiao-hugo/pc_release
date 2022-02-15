<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Vtiger_Request {

	// Datastore
	private $valuemap;
	private $rawvaluemap;
	private $defaultmap = array();

	/**
	 * Default constructor
	 */
	function __construct($values, $rawvalues = array(), $stripifgpc=true) {
		//防止重复提交的问题发生，设置为1秒
		//young.yang 2014-12-15
		/* $limittime=1;
		$isrepeatsubmit=true;
		
		//过滤条件参数 gaocl/2014-12-29 edit start
		$checkflg=$_POST['checkflg'];
		//if($_SERVER['REQUEST_METHOD']=='POST'&&$isrepeatsubmit) {
		//增加action验证,防止post请求之类的view非写入的请求被阻止
		if($_SERVER['REQUEST_METHOD']=='POST'&&$isrepeatsubmit &&$checkflg!=1&&$_POST['action']&&1==2) {
		//过滤条件参数 gaocl/2014-12-29 edit end
			$parent = $_POST['parent'];
			if($parent!='Settings'){
				
				$moduleName=$_POST['module'].$_POST['action'].$_POST['view'];//获取用户模块
				$currentUserModel = Users_Record_Model::getCurrentUserModel();
				
				$filedir='logs/users';
				if (!file_exists($filedir)) {
					mkdir($filedir);
				}
				$ip=getip();//获取用户ip
				if(empty($ip)){
					$ip='127.0.0.1';
				}
				$file=$filedir.'/'.$ip.$moduleName.'.txt';
				$postlog=$filedir.'/post.txt';

				//$content=file_get_contents($file);
				if(!file_exists($file)){
					file_put_contents($file, time());
				}else{
					$content=file_get_contents($file);
					if(time()-$content>$limittime){
						file_put_contents($file, time());
					}else{
						
						file_put_contents($postlog,$ip.'请求时间过于频繁'.var_export($_POST,true)."\r\n",FILE_APPEND);
						exit;
					}
				}
			} 
		} */
		$this->valuemap = $values;
		$this->rawvaluemap = $rawvalues;
		if ($stripifgpc && !empty($this->valuemap) && get_magic_quotes_gpc()) {
			$this->valuemap = $this->stripslashes_recursive($this->valuemap);
            $this->rawvaluemap = $this->stripslashes_recursive($this->rawvaluemap);
		}
	}

	/**
	 * Strip the slashes recursively on the values.
	 */
	function stripslashes_recursive($value) {
		$value = is_array($value) ? array_map(array($this, 'stripslashes_recursive'), $value) : stripslashes($value);
		return $value;
	}

	/**
	 * Get key value (otherwise default value)
	 */
	function get($key, $defvalue = '') {
		$value = $defvalue;
		if(isset($this->valuemap[$key])) {
			$value = $this->valuemap[$key];
		}
		if($value === '' && isset($this->defaultmap[$key])) {
			$value = $this->defaultmap[$key];
		}

		$isJSON = false;
		if (is_string($value)) {
			// NOTE: Zend_Json or json_decode gets confused with big-integers (when passed as string)
			// and convert them to ugly exponential format - to overcome this we are performin a pre-check
			if (strpos($value, "[") === 0 || strpos($value, "{") === 0) {
				$isJSON = true;
			}
		}
		if($isJSON) {
			$oldValue = Zend_Json::$useBuiltinEncoderDecoder;
			Zend_Json::$useBuiltinEncoderDecoder = false;
			$decodeValue = Zend_Json::decode($value);
			if(isset($decodeValue)) {
				$value = $decodeValue;
			}
			Zend_Json::$useBuiltinEncoderDecoder  = $oldValue;
		}

        //Handled for null because vtlib_purify returns empty string
        if(!empty($value)){
            $value = vtlib_purify($value);
        }
        
        $parameter=array('module','src_module');//get请求，安全验证
        $record=array('record','viewname','src_record','sourceRecord');//数组验证
        $char=array('src_field','view','action');//字母验证
        
        if(!isset($_REQUEST['parent'])&&$_REQUEST['module']!='CustomView'){
	        if(!empty($value)&&in_array($key, $parameter)&&$key!='CustomView'){
	        	//global $modelinfo;
	        	
	        	//if(!empty($modelinfo)&&!isset($modelinfo[$value])){
	        	//	sqllogwriter();
	        	//}
	        	$pattern = '/^[a-zA-Z]*$/';
	        	$i=preg_match($pattern, $value);
	        	if(empty($i)){
	        		sqllogwriter();
	        	}
	        }elseif(!empty($value)&&in_array($key, $record)){
	        	$pattern = '/^[0-9N]*$/';
	        	$i=preg_match($pattern, $value);
	        	if(empty($i)){
	        		sqllogwriter();
	        	}
	        }elseif(!empty($value)&&in_array($key, $char)){
	        	$pattern = '/^[a-zA-Z\_]*$/';
	        	$i=preg_match($pattern, $value);
	        	if(empty($i)){
	        		sqllogwriter();
	        	}
	        } 
        }
        //end
		return $value;
	}
	
	/**
	 * Get value for key as boolean
	 */
	function getBoolean($key, $defvalue = '') {
		return strcasecmp('true', $this->get($key, $defvalue).'') === 0;
	}

	/**
	 * Function to get the value if its safe to use for SQL Query (column).
	 * @param <String> $key
	 * @param <Boolean> $skipEmpty - Skip the check if string is empty
	 * @return Value for the given key
	 */
	public function getForSql($key, $skipEmtpy=true) {
		return Vtiger_Util_Helper::validateStringForSql($this->get($key), $skipEmtpy);
	}

	/**
	 * Get data map
	 */
	function getAll() {
		return $this->valuemap;
	}

	/**
	 * Check for existence of key
	 */
	function has($key) {
		return isset($this->valuemap[$key]);
	}

	/**
	 * Is the value (linked to key) empty?
	 */
	function isEmpty($key) {
		$value = $this->get($key);
		return empty($value);
	}

	/**
	 * Get the raw value (if present) ignoring primary value.
	 */
	function getRaw($key, $defvalue = '') {
		if (isset($this->rawvaluemap[$key])) {
			return $this->rawvaluemap[$key];
		}
		return $this->get($key, $defvalue);
	}

	/**
	 * Set the value for key
	 */
	function set($key, $newvalue) {
		$this->valuemap[$key]= $newvalue;
	}

	/**
	 * Set the value for key, both in the object as well as global $_REQUEST variable
	 */
	function setGlobal($key, $newvalue) {
		$this->set($key, $newvalue);
		// TODO - This needs to be cleaned up once core apis are made independent of REQUEST variable.
		// This is added just for backward compatibility
		$_REQUEST[$key] = $newvalue;
	}

	/**
	 * Set default value for key
	 */
	function setDefault($key, $defvalue) {
		$this->defaultmap[$key] = $defvalue;
	}

	/**
	 * Shorthand function to get value for (key=_operation|operation)
	 */
	function getOperation() {
		return $this->get('_operation', $this->get('operation'));
	}

	/**
	 * Shorthand function to get value for (key=_session)
	 */
	function getSession() {
		return $this->get('_session', $this->get('session'));
	}

	/**
	 * Shorthand function to get value for (key=mode)
	 */
	function getMode() {
		return $this->get('mode');
	}

	function getModule($raw=true) {
		$moduleName = $this->get('module');
		if(!$raw) {
			$parentModule = $this->get('parent');
			if(!empty($parentModule)) {
				$moduleName = $parentModule.':'.$moduleName;
			}
		}
		return $moduleName;
	}

	function isAjax() {
		if(!empty($_SERVER['HTTP_X_PJAX']) && $_SERVER['HTTP_X_PJAX'] == true) {
			return true;
		} elseif(!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
			return true;
		}
		return false;
	}
// 	function repeatsubmit(){
// 		$ip=getip();//获取用户ip
// 		$moduleName=$_POST['module'];//获取用户模块
		
// 		if($_SERVER['REQUEST_METHOD']=='POST') {
// 			//echo $moduleName;
// 			if(!empty($_SESSION[$moduleName])){
// 				//echo '11';
// 				if((time()-$_SESSION[$moduleName])>5){
// 					$_SESSION[$moduleName]=time();
// 				}else{
// 					echo '1'.$_SESSION[$moduleName].'<br>';
// 				}
// 			}else{
// 				$_SESSION[$moduleName]=time();
// 			}
// 			echo '2'.$_SESSION[$moduleName].'<br>'; 
			
// 		}
		
// 	}

	//禁止非法来源访问
	/**
	 * Validating incoming request.
	 */	
	function validateReadAccess() {
		$this->validateReferer();
		// TODO validateIP restriction?
		return true;
	}
	function getHistoryUrl(){
		if(!$this->isAjax()&&$_SERVER['REQUEST_METHOD'] == 'GET'){
			if($this->validateReferer()){
				$_SESSION['historyurl']=$_SERVER['HTTP_REFERER'];
			}
		}
		if(isset($_SESSION['historyurl'])){
			return $_SESSION['historyurl'];
		}else{
			return null;
		} 
	}
	function validateWriteAccess($skipRequestTypeCheck = false) {
        if(!$skipRequestTypeCheck) {
            if ($_SERVER['REQUEST_METHOD'] != 'POST') throw new Exception('Invalid request');
        }
		$this->validateReadAccess();
		$this->validateCSRF();
		return true;
	}

	protected function validateReferer() {
		// Referer check if present - to over come 
		if(empty($_REQUEST['module'])){
			return true;
		}
		if('IronAccount'==$_REQUEST['module'] || $_REQUEST['embedded']==1){
            return true;
        }
		if (isset($_SERVER['HTTP_REFERER'])) {
			global $site_URL;
			//不正确的来源跳转提示
			if ((stripos($_SERVER['HTTP_REFERER'], $site_URL) !== 0) && ($this->get('module') != 'Install')) {
				throw new Exception('请求跳转中...！<script>setTimeout("window.location.href=\'index.php\'",1000)</script>');
			}
		}
		return true;
	}
	
	protected function validateCSRF() {
		if (function_exists('csrf_check')&&!csrf_check(false)) {
			throw new Exception('Unsupported request');
		}
	}
}