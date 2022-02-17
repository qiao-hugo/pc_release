<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

/**
 * Abstract Controller Class
 */
abstract class Vtiger_Controller {

	function __construct() { }

	function loginRequired() {
		return true;
	}

	abstract function getViewer(Vtiger_Request $request);
	abstract function process (Vtiger_Request $request);

	function validateRequest(Vtiger_Request $request) {}
	function preProcess(Vtiger_Request $request) {}
	function postProcess(Vtiger_Request $request) {}

	// Control the exposure of methods to be invoked from client (kind-of RPC)
	protected $exposedMethods = array();

	/**
	 * Function that will expose methods for external access
	 * @param <String> $name - method name
	 */
	protected function exposeMethod($name) {
		if(!in_array($name, $this->exposedMethods)) {
			$this->exposedMethods[] = $name;
		}
	}

	/**
	 * Function checks if the method is exposed for client usage
	 * @param string $name - method name
	 * @return boolean
	 */
	function isMethodExposed($name) {
		if(in_array($name, $this->exposedMethods)) {
			return true;
		}
		return false;
	}

	/**
	 * Function invokes exposed methods for this class
	 * @param string $name - method name
	 * @param Vtiger_Request $request
	 * @throws Exception
	 */
	function invokeExposedMethod() {
		$parameters = func_get_args();
		$name = array_shift($parameters);
		if (!empty($name) && $this->isMethodExposed($name)) {
			return call_user_func_array(array($this, $name), $parameters);
		}
		throw new Exception(vtranslate('LBL_NOT_ACCESSIBLE'));
	}
}

/**
 * Abstract Action Controller Class
 * @todo hello
 */
abstract class Vtiger_Action_Controller extends Vtiger_Controller {
	function __construct() {
		parent::__construct();
	}

	function getViewer(Vtiger_Request $request) {
		throw new AppException ('Action - implement getViewer - JSONViewer');
	}

	function validateRequest(Vtiger_Request $request) {
		return $request->validateReadAccess();
	}
	
	function preProcess(Vtiger_Request $request) {
		return true;
	}

	protected function preProcessDisplay(Vtiger_Request $request) {
	}

	protected function preProcessTplName(Vtiger_Request $request=null) {
		return false;
	}

	//TODO: need to revisit on this as we are not sure if this is helpful
	/*function preProcessParentTplName(Vtiger_Request $request) {
		return false;
	}*/

	function postProcess(Vtiger_Request $request) {
		return true;
	}
}

/**
 * Abstract View Controller Class
 */
abstract class Vtiger_View_Controller extends Vtiger_Action_Controller {

	function __construct() {
		parent::__construct();
	}
	// 视图
	function getViewer(Vtiger_Request $request) {
		if(empty($this->viewer)) {
			global $vtiger_current_version;
			$viewer = new Vtiger_Viewer();
			$viewer->assign('APPTITLE', getTranslatedString('APPTITLE'));
			$viewer->assign('VTIGER_VERSION', $vtiger_current_version);
			$this->viewer = $viewer;
		}
		return $this->viewer;
	}
	// 翻译当前的title
	function getPageTitle(Vtiger_Request $request) {
		return vtranslate($request->getModule(), $request->get('module'));
	}
	// 前置条件
	function preProcess(Vtiger_Request $request, $display=true) {
	//	$currentUser = Users_Record_Model::getCurrentUserModel();
		$viewer = $this->getViewer($request);
		$viewer->assign('PAGETITLE', $this->getPageTitle($request));
		//缓存模块加载的JS文件信息 20150504 By Joe
		global $currentModule;
		global $currentView;
		$currentjs=$currentModule.'-'.$currentView.'-js';
		$SCRIPTS=Vtiger_Cache::get('user',$currentjs);
		if(empty($SCRIPTS)){
			$SCRIPTS=$this->getHeaderScripts($request);
			Vtiger_Cache::set('user',$currentjs,$SCRIPTS);
		}
		$viewer->assign('SCRIPTS',$SCRIPTS);
		$viewer->assign('STYLES',$this->getHeaderCss($request));
		$viewer->assign('SKIN_PATH', 'softed');
		//$viewer->assign('LANGUAGE_STRINGS', $this->getJSLanguageStrings($request));
		$viewer->assign('LANGUAGE', 'zh-cn');
		if($display) {
			$this->preProcessDisplay($request);//默认的模板，竟然是header.tpl
		}
	}

	protected function preProcessTplName(Vtiger_Request $request) {
		return 'Header.tpl';
	}

	//Note : To get the right hook for immediate parent in PHP,
	// specially in case of deep hierarchy
	//TODO: Need to revisit this.
	/*function preProcessParentTplName(Vtiger_Request $request) {
		return parent::preProcessTplName($request);
	}*/

	protected function preProcessDisplay(Vtiger_Request $request) {
		$viewer = $this->getViewer($request);
		
		
		$displayed = $viewer->view($this->preProcessTplName($request), $request->getModule());
		
		/*if(!$displayed) {
			$tplName = $this->preProcessParentTplName($request);
			if($tplName) {
				$viewer->view($tplName, $request->getModule());
			}
		}*/
	}

	// post方法竟然需要footer.tpl
	function postProcess(Vtiger_Request $request) {
		$viewer = $this->getViewer($request);
	
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$viewer->assign('ACTIVITY_REMINDER', $currentUser->getCurrentUserActivityReminderInSeconds());//当前用户的提醒，不知道做啥用的
		//young.yang  调试方法加入
		
		//echo $aaadad;
		
	/* 	if(DEVELAPOR){
	//关闭调试
				//print_r($GLOBALS['develapor_trace']);die();
			krsort($GLOBALS['sql_trace_all_array']);
			
			//echo implode('\',\'',array_keys($GLOBALS['log_array']));
			//die();
			$viewer->assign('logs_array',$GLOBALS['log_array']);
			$viewer->assign('sql_trace_all_array', $GLOBALS['sql_trace_all_array']);
			$viewer->assign('develapor_trace', $GLOBALS['develapor_trace']);
			$viewer->assign('files_include', get_included_files());
			$viewer->assign('page_load_info', page_inf());
			$viewer->assign('class_loader_all', $GLOBALS['class_loader_all']);
			$viewer->assign('log_htmls', $GLOBALS['log_htmls']);
		} */
		$viewer->assign('is_debug', DEVELAPOR);
		$viewer->view('Footer.tpl');
	}

	/**
	 * Retrieves headers scripts that need to loaded in the page
	 * 验证引擎
	 * @param Vtiger_Request $request - request model
	 * @return <array> - array of Vtiger_JsScript_Model
	 */
	function getHeaderScripts(Vtiger_Request $request){
		$headerScriptInstances = array();
		$languageHandlerShortName = Vtiger_Language_Handler::getShortLanguageName();
		$languageHandlerShortName = "zh_CN";
		$fileName = "libraries/jquery/posabsolute-jQuery-Validation-Engine/js/languages/jquery.validationEngine-$languageHandlerShortName.js";
		if (!file_exists($fileName)) {
			$fileName = "~libraries/jquery/posabsolute-jQuery-Validation-Engine/js/languages/jquery.validationEngine-en.js";
		} else {
			$fileName = "~libraries/jquery/posabsolute-jQuery-Validation-Engine/js/languages/jquery.validationEngine-$languageHandlerShortName.js";
		}
		
		$jsFileNames = array($fileName);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = $jsScriptInstances;
		return $headerScriptInstances;
	}
	
	function checkAndConvertJsScripts($jsFileNames) {
		$fileExtension = 'js';

		$jsScriptInstances = array();
		foreach($jsFileNames as $jsFileName) {
			$jsScript = new Vtiger_JsScript_Model();

			// external javascript source file handling
			if(strpos($jsFileName, 'http://') === 0 || strpos($jsFileName, 'https://') === 0) {
				$jsScriptInstances[$jsFileName] = $jsScript->set('src', $jsFileName);
				continue;
			}

			$completeFilePath = Vtiger_Loader::resolveNameToPath($jsFileName, $fileExtension);

			if(file_exists($completeFilePath)) {
				if (strpos($jsFileName, '~') === 0) {
					$filePath = ltrim(ltrim($jsFileName, '~'), '/');
				} else {
					$filePath = str_replace('.','/', $jsFileName) . '.'.$fileExtension;
				}

				$jsScriptInstances[$jsFileName] = $jsScript->set('src', $filePath);
			} else {
				$fallBackFilePath = Vtiger_Loader::resolveNameToPath(Vtiger_JavaScript::getBaseJavaScriptPath().'/'.$jsFileName, 'js');
				if(file_exists($fallBackFilePath)) {
					$filePath = str_replace('.','/', $jsFileName) . '.js';
					$jsScriptInstances[$jsFileName] = $jsScript->set('src', Vtiger_JavaScript::getFilePath($filePath));
				}
			}
		}
		return $jsScriptInstances;
	}

	/**
	 * Function returns the css files
	 * @param <Array> $cssFileNames
	 * @param <String> $fileExtension
	 * @return <Array of Vtiger_CssScript_Model>
	 *
	 * First check if $cssFileName exists
	 * if not, check under layout folder $cssFileName eg:layouts/vlayout/$cssFileName
	 */
	function checkAndConvertCssStyles($cssFileNames, $fileExtension='css') {
		$cssStyleInstances = array();
		foreach($cssFileNames as $cssFileName) {
			$cssScriptModel = new Vtiger_CssScript_Model();

			if(strpos($cssFileName, 'http://') === 0 || strpos($cssFileName, 'https://') === 0) {
				$cssStyleInstances[] = $cssScriptModel->set('href', $cssFileName);
				continue;
			}
			$completeFilePath = Vtiger_Loader::resolveNameToPath($cssFileName, $fileExtension);
			$filePath = NULL;
			if(file_exists($completeFilePath)) {
				if (strpos($cssFileName, '~') === 0) {
					$filePath = ltrim(ltrim($cssFileName, '~'), '/');
				} else {
					$filePath = str_replace('.','/', $cssFileName) . '.'.$fileExtension;
					$filePath = Vtiger_Theme::getStylePath($filePath);
				}
				$cssStyleInstances[] = $cssScriptModel->set('href', $filePath);
			}
		}
		return $cssStyleInstances;
	}

	/**
	 * Retrieves css styles that need to loaded in the page
	 * @param Vtiger_Request $request - request model
	 * @return <array> - array of Vtiger_CssScript_Model
	 */
	function getHeaderCss(Vtiger_Request $request){
		return array();
	}

	/**
	 * Function returns the Client side language string
	 * @param Vtiger_Request $request
	 */
	function getJSLanguageStrings(Vtiger_Request $request) {
		$moduleName = $request->getModule(false);
		return Vtiger_Language_Handler::export($moduleName, 'jsLanguageStrings');
	}
}