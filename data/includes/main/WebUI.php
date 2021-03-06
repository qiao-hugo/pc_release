<?php
/*+*******
 * 主文件
 ********/
//require_once 'include/utils/utils.php';下面也包含了
require_once 'include/utils/CommonUtils.php';
require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

class Vtiger_WebUI extends Vtiger_EntryPoint {

	/**
	 * 验证用户是否登录
	 */
	protected function checkLogin (Vtiger_Request $request) {
		if (!$this->hasLogin()) {	//执行下面的getLogin方法
			header('Location: index.php');//throw new AppException('Login is required');
		}
	}

	/**
	 * 用户登录实例化(用户配置权限等信息)
	 * @return Users object
	 */
	function getLogin() {

		$user = parent::getLogin();//返回false
		if (!$user) {
			$userid = Vtiger_Session::get('AUTHUSERID', $_SESSION['authenticated_user_id']);
            //$time=microtime(true);
			if ($userid) {
				$usercache = Vtiger_Cache::get('user','userid_'.$userid);
                if(!$usercache){
                    $user = CRMEntity::getInstance('Users');
                    //实例化User->modules/$module/$modName.php  加载用户文件 user_privileges/user_privileges_USERID
                    $user->retrieveCurrentUserInfoFromFile($userid);
                    Vtiger_Cache::set('user','userid_'.$userid,$user,86400);
                }else{
                    $user=$usercache;
                }
				$this->setLogin($user);
			}
            //echo microtime(true)-$time;die();
		}
		return $user;
	}

	/**
	 *验证权限[新增缓存模块信息]
	 */
	protected function triggerCheckPermission($handler, $request) {
		//获取index.php?module=Inv9oice&view=List中module的值
		$moduleName = $request->getModule();
		//根据模块名获取对象ID[涉及数据库]//$moduleModel = Vtiger_Module_Model::getInstance($moduleName);//$moduleModel->getId()\
		//require('crmcache/modelinfo.php');
		global $modelinfo;
		global $current_user;
		if (empty($modelinfo[$moduleName]) || !in_array($modelinfo[$moduleName]['presence'],array(0,2)) ) {throw new AppException(vtranslate('LBL_HANDLER_NOT_FOUND'));}
	
		//根据模块ID判断用户是否管理员或者有权限访问活动的模块(active)
		//$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		//$permission = $userPrivilegesModel->hasModulePermission($modelinfo[$moduleName]['tabid']);
		//echo 900090;
       // var_dump($current_user->is_admin);die;
		if ($current_user->is_admin=='on' || in_array($modelinfo[$moduleName]['tabid'],$current_user->profile_tabs_permission)) {
			//详细的权限控制[模块自定义 默认isPermitted]
			$handler->checkPermission($request);
			return;
		}
		throw new AppException(vtranslate($moduleName).' '.vtranslate('LBL_NOT_ACCESSIBLE'));
	}

	/**
	 *判断pjax请求返回相应内容
	 */
	protected function triggerPreProcess($handler, $request) {
		if($request->isAjax()){return true;}
		//加载视图(有权限的菜单显示等)
		$handler->preProcess($request);
	}

	protected function triggerPostProcess($handler, $request) {
		if($request->isAjax()){return true;}
		$handler->postProcess($request);
	}
	
	//初次安装使用！可删除function isInstalled() {global $dbconfig;if (empty($dbconfig) || empty($dbconfig['db_name']) || $dbconfig['db_name'] == '_DBC_TYPE_') {return false;}return true;}

	function process (Vtiger_Request $request) {
		Vtiger_Session::init();  //产生sessionid(session_start)
		require_once 'libraries/csrf-magic/csrf-magic.php';
		// TODO - Get rid of global variable $current_user//common utils api called, depend on this variable right now
		$currentUser = $this->getLogin();//1.从本地文件获取已登录用户信息
        vglobal('current_user', $currentUser);
		global $default_language;
		// young.yang 2014-12-26  定义全局变量，哪些模块可以生成流程
		global $isallow;
		$isallow=array('SalesOrder','Invoice','Quotes','VisitingOrder','Vacate','OrderChargeback','RefillApplication',
		'ExtensionTrial', 'Suppcontractsextension','PurchaseInvoice','OrderChargeback', 'Newinvoice', 'Vendors', 'Schoolvisit');//'ServiceContracts',
		// end
		vglobal('default_language', $default_language);
		$currentLanguage = 'zh_cn';
		//Vtiger_Language_Handler::getLanguage();//2.语言设置
		vglobal('current_language',$currentLanguage);

		$module = $request->getModule();//3.1获取参数module的值
		$qualifiedModuleName = $request->getModule(false);//3.2获取module以及父级parent，返回parent:module如module=Vtiger&parent=Settings|Settings:Vtiger

		//4.返回当前用户的语言设置
		if ($currentUser && $qualifiedModuleName) {
			$moduleLanguageStrings = Vtiger_Language_Handler::getModuleStringsFromFile($currentLanguage,$qualifiedModuleName);
			vglobal('mod_strings', $moduleLanguageStrings['languageStrings']);
		}
		//4.2获取系统默认的应用语言设置
		if ($currentUser) {
			$moduleLanguageStrings = Vtiger_Language_Handler::getModuleStringsFromFile($currentLanguage);
			vglobal('app_strings', $moduleLanguageStrings['languageStrings']);
		}

        /*检查当前登录人是否有为匹配回款的合同 如果有直接跳转到回款匹配页面*/
        $currentid = $currentUser->id;
        $adb =PearDatabase::getInstance();
        $confirmdate= date("Y-m-d",strtotime("-30 days"));
        $querys="SELECT 1 FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE deleted=0 AND vtiger_servicecontracts.modulestatus='已发放' AND ((vtiger_servicecontracts.isconfirm=1 AND vtiger_servicecontracts.confirmlasttime<?) OR (vtiger_servicecontracts.isconfirm=0 AND vtiger_servicecontracts.receivedate<?)) AND vtiger_crmentity.smownerid=".$currentid;
        $confirmResult=$adb->pquery($querys,array($confirmdate,$confirmdate));
        /*if($adb->num_rows($confirmResult)>0 && $currentid !=38){
            echo '<!DOCTYPE html><html><head><title>登录 - 珍岛ERP</title><meta name="viewport" content="width=device-width, initial-scale=1.0"><link href="libraries/bootstrap/css/jquery.bxslider.css" rel="stylesheet" /><script src="libraries/jquery/jquery.min.js"></script>   </head>   <body>   <div class="container-fluid login-container"><div class="row-fluid"><div class="span12"><div class="content-wrapper"><div class="container-fluid"> <div class="row-fluid"><div class="span12"> <div class="login-area" style="margin:0 auto;"><div class="logo"><img src="layouts/vlayout/skins/images/logo.png"></div><div class="text_box"><img src="layouts/vlayout/skins/images/text.png"></div><div class="login-box" id="loginDiv"><div class="login_t_text">  <h3 class="login-header">账号已冻结</h3></div>  <div style="text-align:center;color: #ff0000;font-size: 30px;font-weight: bold;margin-top: 30px;"> 请将未归还的合同归还或进行审查  </div></div> </div></div> </div></div></div></div></div></div></body></html>';
            exit;
        }*/
    /*    $No_serviecsql = "SELECT
	vtiger_receivedpayments.*,label,sc_related_to,servicecontractsid,contract_no
FROM
	vtiger_receivedpayments
LEFT JOIN vtiger_servicecontracts ON maybe_account = sc_related_to
LEFT JOIN vtiger_crmentity ON maybe_account = crmid
WHERE
	relatetoid = '' AND  receiveid = ? AND receivedpaymentsid NOT IN (SELECT DISTINCT receivepaymentid FROM `vtiger_ReceivedPayments_throw` WHERE userid = ?)";*/
        // 2016-8-30 回款匹配的客户的负责人是当前用户，跳到回款匹配中 周海   这里合并到了 $No_servicesql1中去了 新添加了 sql 中的 OR 后面条件 
		// 匹配的是 receiveid=?  是服务合同的提单人
        $No_serviecsql = "SELECT
							vtiger_receivedpayments.*, label,
							sc_related_to,
							servicecontractsid,
							contract_no
						FROM
							vtiger_receivedpayments
						LEFT JOIN vtiger_servicecontracts ON maybe_account = sc_related_to ## 回款可能客户 = 服务合同里面的客户
						LEFT JOIN vtiger_crmentity ON maybe_account = crmid
						LEFT JOIN vtiger_servicecomments ON related_to = sc_related_to
						WHERE
							(
								(relatetoid = '' or relatetoid=0 or relatetoid is null)
								AND receivedstatus = 'normal'
								AND maybe_account != ''
								AND vtiger_servicecomments.serviceid = ?     ## ## 客服负责人事当前用户
								AND servicecontractsid != ''  ## 合同不能为空
								AND contractstate = '0'       ## 合同的关闭状态为正常
								AND receivedpaymentsid NOT IN (
									SELECT DISTINCT
										receivepaymentid
									FROM
										`vtiger_ReceivedPayments_throw`
									WHERE
										userid = ?
									AND vtiger_ReceivedPayments_throw.deleted=0
								)
							) OR

							(
								(relatetoid = '' or relatetoid=0 or relatetoid is null)
								AND receivedstatus = 'normal'
								AND receiveid = ?
								AND maybe_account != ''
								AND contractstate = '0'       ## 合同的关闭状态为正常
								AND receivedpaymentsid NOT IN (
									SELECT DISTINCT
										receivepaymentid
									FROM
										`vtiger_ReceivedPayments_throw`
									WHERE
										userid = ?
									AND vtiger_ReceivedPayments_throw.deleted=0
								)
							) OR
							(
								(relatetoid = '' or relatetoid=0 or relatetoid is null)
								AND receivedstatus = 'normal'
								AND maybe_account != ''
								AND vtiger_crmentity.smownerid = ?     ## ## 可能客户的负责人是当前用户
								AND servicecontractsid != ''  ## 合同不能为空
								AND contractstate = '0'       ## 合同的关闭状态为正常
								AND receivedpaymentsid NOT IN (
									SELECT DISTINCT
										receivepaymentid
									FROM
										`vtiger_ReceivedPayments_throw`
									WHERE
										userid = ?
									AND vtiger_ReceivedPayments_throw.deleted=0
								)
							)";
        //$No_serviecsql2 = "SELECT vtiger_receivedpayments.*, vtiger_staypayment.staypaymentname,vtiger_servicecontracts.contract_no,vtiger_servicecontracts.servicecontractsid FROM vtiger_receivedpayments LEFT JOIN vtiger_staypayment ON vtiger_staypayment.staypaymentname = vtiger_receivedpayments.paytitle LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_staypayment.contractid INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_staypayment.staypaymentid WHERE relatetoid = '' AND receiveid = ? AND staypaymentname != '' AND deleted = 0 AND receivedpaymentsid NOT IN (SELECT DISTINCT receivepaymentid FROM `vtiger_ReceivedPayments_throw` WHERE userid = ?)";
        /*$No_serviecsql2 = "SELECT vtiger_receivedpayments.*, vtiger_staypayment.staypaymentname,vtiger_servicecontracts.contract_no,vtiger_servicecontracts.servicecontractsid FROM vtiger_receivedpayments LEFT JOIN vtiger_staypayment ON vtiger_staypayment.staypaymentname = vtiger_receivedpayments.paytitle LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_staypayment.contractid INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_staypayment.staypaymentid WHERE relatetoid = '' AND receiveid = ? AND staypaymentname != '' AND deleted = 0 AND( overdute  IS NULL OR overdute > SYSDATE()) AND receivedpaymentsid NOT IN ( SELECT DISTINCT receivepaymentid FROM `vtiger_ReceivedPayments_throw` WHERE userid = ? )";*/
        // 20161108周海
        $No_serviecsql2 = "SELECT
								vtiger_receivedpayments.*, vtiger_staypayment.staypaymentname,
								vtiger_servicecontracts.contract_no,
								vtiger_servicecontracts.servicecontractsid
							FROM
								vtiger_receivedpayments
							LEFT JOIN vtiger_staypayment ON vtiger_staypayment.staypaymentname = vtiger_receivedpayments.paytitle
							LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_staypayment.contractid
							INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_staypayment.staypaymentid
							LEFT JOIN vtiger_servicecomments ON related_to = sc_related_to ## 客服的客户id等于合同的客户id
							WHERE
								relatetoid = ''
							AND (receiveid = ? OR vtiger_servicecomments.serviceid = ?)     ## ## 客服负责人事当前用户
							AND staypaymentname != ''
							AND vtiger_crmentity.deleted = 0
							AND contractstate = '0'       ## 合同的关闭状态为正常
							AND (
								overdute IS NULL
								OR overdute > SYSDATE()
							)
							AND receivedpaymentsid NOT IN (
								SELECT DISTINCT
									receivepaymentid
								FROM
									`vtiger_ReceivedPayments_throw`
								WHERE
									userid = ?
								AND vtiger_ReceivedPayments_throw.deleted=0
							)";
        
        // 2016-8-30 回款匹配的客户的负责人是当前用户，跳到回款匹配中 周海   这里合并到了 $No_servicesql1中去了
        /*SELECT
								vtiger_receivedpayments.*, vtiger_staypayment.staypaymentname,
								vtiger_servicecontracts.contract_no,
								vtiger_servicecontracts.servicecontractsid
							FROM
								vtiger_receivedpayments
							LEFT JOIN vtiger_staypayment ON vtiger_staypayment.staypaymentname = vtiger_receivedpayments.paytitle
							LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_staypayment.contractid
							INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_staypayment.staypaymentid
							LEFT JOIN vtiger_servicecomments ON related_to = sc_related_to ## 客服的客户id等于合同的客户id
							WHERE
								relatetoid = ''
							AND (receiveid = ? OR vtiger_servicecomments.serviceid = ?)     ## ## 客服负责人事当前用户
							AND staypaymentname != ''
							AND deleted = 0
							AND contractstate = '0'       ## 合同的关闭状态为正常
							AND (
								overdute IS NULL
								OR overdute > SYSDATE()
							)
							AND receivedpaymentsid NOT IN (
								SELECT DISTINCT
									receivepaymentid
								FROM
									`vtiger_ReceivedPayments_throw`
								WHERE
									userid = ?
							)*/


        $adb =PearDatabase::getInstance();
        
        $result = $adb->pquery($No_serviecsql, array($currentid,$currentid, $currentid, $currentid, $currentid, $currentid));
        $result2 = $adb->pquery($No_serviecsql2, array($currentid,$currentid,$currentid));

        if($adb->num_rows($result)>0 || $adb->num_rows($result2)>0){
	        if($currentUser && $request->get('module') !== 'Matchreceivements' && !$request->isAjax()){
	            header('Location:index.php?module=Matchreceivements&view=List');
	        }
        }

        $view = $request->get('view');//5.根据URL获取视图控制器
        $action = $request->get('action');//6.获取action
        $response = false;

        try {
			//安装 76行 if($this->isInstalled() === false && $module != 'Install') {header('Location:index.php?module=Install&view=Index');exit;}
			
			if(empty($module)) {	//空模块时验证用户是否登录跳转默认模块或登录
				if ($this->hasLogin()) {
					$defaultModule = vglobal('default_module');//配置不为home时
					if(!empty($defaultModule) && $defaultModule != 'Home') {
						$module = $defaultModule; $qualifiedModuleName = $defaultModule; $view = 'List';
                        if($module == 'Calendar') { 
                            // To load MyCalendar instead of list view for calendar//TODO: see if it has to enhanced and get the default view from module model
                            $view = 'Calendar';
                        }
					} else {
						$module = 'Home'; $qualifiedModuleName = 'Home'; $view = 'DashBoard';
					}
				} else {
					$module = 'Users'; $qualifiedModuleName = 'Settings:Users'; $view = 'Login';//跳转登录控制
				}
				$request->set('module', $module);
				$request->set('view', $view);
			}

			if (!empty($action)) {
				$componentType = 'Action';//post提交action
				$componentName = $action;
			} else {
				$componentType = 'View';//get获取view
				if(empty($view)) {
					$view = 'Index';
				}
				$componentName = $view;
			}
			$handlerClass = Vtiger_Loader::getComponentClassName($componentType, $componentName, $qualifiedModuleName);
			//like :Home_DashBoard_View or Users_Login_Actions
			$handler = new $handlerClass();
			
			if ($handler) {
				vglobal('currentModule', $module);
				vglobal('currentView', $view);
				if($module=='CustomView'){
					vglobal('currentModule', $request->get('source_module'));
					//=Accounts
					vglobal('currentView', 'List');
				
				}else{
					vglobal('currentView', $view);
				    vglobal('currentAction', $action);
				}

				$handler->validateRequest($request);//验证请求来源
				if ($handler->loginRequired()) {	//判断是否需要登录访问 [默认需要] except 登录模块//$this->checkLogin ($request);//登录验证
					if(empty($currentUser)){
						header('Location: index.php');//throw new AppException('Login is required');
					}
				}
				//公共模块*不验证权限
				$skipList = array('Users','ModComments','JobAlerts','SalesorderWorkflowStages', 'Home', 'CustomView', 'Import', 'Export', 'Inventory', 'Vtiger','PriceBooks','Migration');

				if(!in_array($module, $skipList) && stripos($qualifiedModuleName, 'Settings') === false) {
					$this->triggerCheckPermission($handler, $request);//后台配置
				}
				
				// Settings开头 设置相关的必须验证
				if(stripos($qualifiedModuleName, 'Settings') === 0) {
					$handler->checkPermission($request);
				}
				// 禁止访问的列表（其他应用）
				$notPermittedModules = array('ModComments','RSS','Portal','Integration','PBXManager','DashBoard');
				if(in_array($module, $notPermittedModules) && $view == 'List'){
					header('Location:index.php?module=Home&view=DashBoard');
				}
				
				//判断是否ajax请求
				$this->triggerPreProcess($handler, $request);
				$response = $handler->process($request);

				$this->triggerPostProcess($handler, $request);
			} else {
				throw new AppException(vtranslate('LBL_HANDLER_NOT_FOUND'));
			}
		} catch(Exception $e) {
			if ($view) {
				// Log for developement.
				error_log($e->getTraceAsString(), E_NOTICE);
				$viewer = new Vtiger_Viewer();
				$viewer->assign('MESSAGE', $e->getMessage());
				$viewer->view('OperationNotPermitted.tpl', 'Vtiger');
			} else {
				$response = new Vtiger_Response();
				$response->setEmitType(Vtiger_Response::$EMIT_JSON);
				$response->setError($e->getMessage());
			}
		}
		
		if ($response) {
			$response->emit();
		}
	}
}
