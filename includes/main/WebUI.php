<?php
/*+*******
 * 主文件
 ********/
//require_once 'include/utils/utils.php';下面也包含了
require_once 'include/utils/CommonUtils.php';
require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

class Vtiger_WebUI extends Vtiger_EntryPoint {
    public $sso="";

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
                    $_SESSION['userdepartmentid']=$user->departmentid;//单独提出来避免更新缓存时出错
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
	    //判断是否是内嵌页
	    if($request->get('embedded') == 1) {
            $sessionid = $request->get(session_name());
            Vtiger_Session::init($sessionid); //产生sessionid(session_start)
        } else {
            Vtiger_Session::init(); //产生sessionid(session_start)
        }
		require_once 'libraries/csrf-magic/csrf-magic.php';
		// TODO - Get rid of global variable $current_user//common utils api called, depend on this variable right now
		$currentUser = $this->getLogin();//1.从本地文件获取已登录用户信息
        vglobal('current_user', $currentUser);
		global $default_language,$sso_URL,$site_URL;
		$this->sso=$sso_URL;
		// young.yang 2014-12-26  定义全局变量，哪些模块可以生成流程
		global $isallow;
		$isallow=array('SalesOrder','Invoice','Quotes','VisitingOrder','Vacate','OrderChargeback','RefillApplication',
		'ExtensionTrial', 'Suppcontractsextension','PurchaseInvoice','OrderChargeback', 'Newinvoice', 'Vendors', 'Schoolvisit','ServiceContracts','ContractsAgreement','SupplierContracts','SuppContractsAgreement','AccountPlatform','ProductProvider','ContractGuarantee'
        ,'RefundTimeoutAudit','SeparateInto','UserManger','AchievementallotStatistic','AchievementSummary','ClosingDate','Staypayment','EmployeeAbility','PreInvoiceDeferral','InputInvoice', 'CustomerStatement','SupplierStatement','ContractDelaySign','PayApply','Receipt','ReceivedPaymentsCollate');//'ServiceContracts',
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
		$this->_logs(array("1"=>$_SESSION['vt_param']));
        if(empty($_SESSION['vt_param'])){
            $this->_logs(array("2"=>$request->get('__vt_param__')));
            $__vt_param__=$request->get('__vt_param__');
            if(empty($__vt_param__)){
                $this->_logs(array("3"=>$__vt_param__));
                $backUrl=$site_URL.$_SERVER['REQUEST_URI'];
                //header("Location: ".$this->sso."login?backUrl=".urlencode($site_URL));
                header("Location: ".$this->sso."login?backUrl=".urlencode($backUrl));
            }else{
                $this->_logs(array("4"=>$__vt_param__));
                $this->getSSOinfo($__vt_param__);
                if(stripos($_SERVER['REQUEST_URI'],'module=IronAccount')===false && stripos($_SERVER['REQUEST_URI'],'embedded=1')===false){
                    header("Location: /index.php?from=login");
                }else{
                    $REQUEST_URI=trim($_SERVER['REQUEST_URI'],'/');
                    $REQUEST_URI=trim($REQUEST_URI,'/');
                    $REQUEST_URI_ARR=explode('&',$REQUEST_URI);

                    foreach ($REQUEST_URI_ARR as $k=>$row) {
                        if(stripos($row,'__vt_param__') !== false) {
                            unset($REQUEST_URI_ARR[$k]);
                        }
                    }
                    $REQUEST_URI=implode('&',$REQUEST_URI_ARR);
                    $REQUEST_URI .= '&'.session_name().'='.session_id();
                    header("Location: ".$site_URL.$REQUEST_URI);
                }
            }
            exit;
        }
        if(!empty($_SESSION['vt_param']) && empty($currentUser)){
            $this->_logs(array("6"=>$_SESSION['vt_param']));
            $this->getSSOinfo($_SESSION['vt_param']);
        }
        if($_SESSION['CHECKVT_PARAM']<time()){//2分钟验证一次
            $this->_logs(array("7"=>$_SESSION['vt_param']));
            $_SESSION['CHECKVT_PARAM']=time()+120;
            $this->getSSOinfo($_SESSION['vt_param'],2);
        }
        $view = $request->get('view');//5.根据URL获取视图控制器
        $action = $request->get('action');//6.获取action
        $mode = $request->get('mode');//6.获取action
        $actionArray = array('BasicAjax','DownloadFile','FileUpload','SaveAjax','ChangeAjax');//不需要验证超期和回款的action
        $modeArray=array('getProducts','getWorkflows','showRecentComments','showRecentActivities');//不需要验证超期和回款的mode
        /*检查当前登录人是否有为匹配回款的合同 如果有直接跳转到回款匹配页面*/
        $viewArray=array('FieldAjax','ListAjax');//不需要验证超期和回款的view
        $BugFreeQuery=$request->get('BugFreeQuery');//查找数据时不验证
        $arrayModule=array('WorkFlowCheck','Files','Users','ModComments','JobAlerts','SalesorderWorkflowStages', 'CustomView', 'Import', 'Export', 'Inventory', 'Vtiger','PriceBooks','Migration','Invoicesign');//不需要验证超期和回款的module
        if($_SESSION['CHECKLOADING']<time() && !empty($currentUser) && empty($BugFreeQuery) && !in_array($mode,$modeArray) &&!in_array($module,$arrayModule) && !in_array($view,$viewArray) && !in_array($action,$actionArray) && $action!='Save') {
        $currentid = $currentUser->id;
        $adb =PearDatabase::getInstance();
        $newInvoicesNeedsql="select invoiceno from vtiger_newinvoice left join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_newinvoice.invoiceid where vtiger_newinvoice.lockstatus=1 and vtiger_crmentity.smownerid=?";
        $newInvoicesNeedResult=$adb->pquery($newInvoicesNeedsql,array($currentid));
        if($adb->num_rows($newInvoicesNeedResult)>0){
            $invoice_no_str = '';
            while ($row=$adb->fetchByAssoc($newInvoicesNeedResult)){
                $invoice_no_str .=$row['invoiceno'].'、';
            }
            echo '<!DOCTYPE html><html><head><title>登录 - 珍岛ERP</title><meta name="viewport" content="width=device-width, initial-scale=1.0"><link href="libraries/bootstrap/css/jquery.bxslider.css" rel="stylesheet" /><script src="libraries/jquery/jquery.min.js"></script>   </head>   <body>   <div class="container-fluid login-container"><div class="row-fluid"><div class="span12"><div class="content-wrapper"><div class="container-fluid"> <div class="row-fluid"><div class="span12"> <div class="login-area" style="margin:0 auto;"><div class="logo"><img src="layouts/vlayout/skins/images/logo.png"></div><div class="login-box" id="loginDiv"><div class="login_t_text">  <h3 class="login-header">系统功能已关闭</h3></div>  <div style="text-align:center;color: #ff0000;font-size: 30px;font-weight: bold;margin-top: 10px;height: 250px;overflow: scroll;">因为你有超出时间未匹配回款的发票，本账号已锁定，请联系上级解锁。涉及发票编号为：'.rtrim($invoice_no_str,'、').'  </div></div> </div></div> </div></div></div></div></div></div></body></html>';
            exit;
        }
        // cxh  不等于1测试环境使用 莫上传以线上为准  且注释掉所有的 合同审查
            /*
        if($currentid!=1){
            $confirmdate= date("Y-m-d",strtotime("-30 days"));
            $querys="SELECT vtiger_servicecontracts.contract_no FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE deleted=0 AND vtiger_servicecontracts.modulestatus='已发放' AND ((vtiger_servicecontracts.isconfirm=1 AND vtiger_servicecontracts.confirmlasttime<?) OR (vtiger_servicecontracts.isconfirm=0 AND vtiger_servicecontracts.receivedate<?)) AND vtiger_crmentity.smownerid=".$currentid;
            $confirmResult=$adb->pquery($querys,array($confirmdate,$confirmdate));
            if($adb->num_rows($confirmResult)>0 && $currentid !=38 && $currentid !=43){
                $contract_no_str = '';
                while ($row=$adb->fetchByAssoc($confirmResult)){
                    $contract_no_str .=$row['contract_no'].'、';
                }
                echo '<!DOCTYPE html><html><head><title>登录 - 珍岛ERP</title><meta name="viewport" content="width=device-width, initial-scale=1.0"><link href="libraries/bootstrap/css/jquery.bxslider.css" rel="stylesheet" /><script src="libraries/jquery/jquery.min.js"></script>   </head>   <body>   <div class="container-fluid login-container"><div class="row-fluid"><div class="span12"><div class="content-wrapper"><div class="container-fluid"> <div class="row-fluid"><div class="span12"> <div class="login-area" style="margin:0 auto;"><div class="logo"><img src="layouts/vlayout/skins/images/logo.png"></div><div class="text_box"><img src="layouts/vlayout/skins/images/text.png"></div><div class="login-box" id="loginDiv"><div class="login_t_text">  <h3 class="login-header">系统功能已关闭</h3></div>  <div style="text-align:center;color: #ff0000;font-size: 30px;font-weight: bold;margin-top: 10px;height: 250px;overflow: scroll;"> 请将未归还的合同归还或进行审查,涉及到的合同有:'.rtrim($contract_no_str,'、').'  </div></div> </div></div> </div></div></div></div></div></div></body></html>';
                exit;
            }
            $canceltime=date('Y-m-d');
            $querys='SELECT vtiger_servicecontracts.contract_no FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid WHERE deleted = 0 AND vtiger_servicecontracts.modulestatus in(\'c_cancelings\',\'c_canceling\') AND (vtiger_crmentity.smownerid =? OR vtiger_servicecontracts.signid=? OR vtiger_servicecontracts.receiveid=?) AND vtiger_servicecontracts.canceltime<\''.$canceltime.'\'';
            $confirmResult = $adb->pquery($querys, array($currentid, $currentid,$currentid));

            if($adb->num_rows($confirmResult)>0 && $currentid !=38 && $currentid !=43){
                $contract_no_str = '';
                while ($row=$adb->fetchByAssoc($confirmResult)){
                    $contract_no_str .=$row['contract_no'].'、';
                }
                echo '<!DOCTYPE html><html><head><title>登录 - 珍岛ERP</title><meta name="viewport" content="width=device-width, initial-scale=1.0"><link href="libraries/bootstrap/css/jquery.bxslider.css" rel="stylesheet" /><script src="libraries/jquery/jquery.min.js"></script>   </head>   <body>   <div class="container-fluid login-container"><div class="row-fluid"><div class="span12"><div class="content-wrapper"><div class="container-fluid"> <div class="row-fluid"><div class="span12"> <div class="login-area" style="margin:0 auto;"><div class="logo"><img src="layouts/vlayout/skins/images/logo.png"></div><div class="text_box"><img src="layouts/vlayout/skins/images/text.png"></div><div class="login-box" id="loginDiv"><div class="login_t_text">  <h3 class="login-header">系统功能已关闭</h3></div>  <div style="text-align:center;color: #ff0000;font-size: 30px;font-weight: bold;margin-top: 10px;height: 250px;overflow: scroll;"> 请到财务部将待作废的合同处理,涉及到的合同有:'.rtrim($contract_no_str,'、').'  </div></div> </div></div> </div></div></div></div></div></div></body></html>';
                exit;
            }
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
            $dateTime=date('Y-m-d');
        $No_serviecsql = "SELECT
							1
						FROM
							vtiger_receivedpayments
						LEFT JOIN vtiger_servicecontracts ON (maybe_account = sc_related_to and vtiger_servicecontracts.modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed'))## 回款可能客户 = 服务合同里面的客户
						LEFT JOIN vtiger_crmentity ON maybe_account = crmid
						LEFT JOIN vtiger_account ON vtiger_receivedpayments.maybe_account = vtiger_account.accountid
						WHERE
						vtiger_receivedpayments.deleted=0 and 
							(
								(relatetoid = '' or relatetoid=0 or relatetoid is null)
								AND receivedstatus = 'normal'
								AND maybe_account != ''
								AND vtiger_account.serviceid = ?     ## ## 客服负责人事当前用户
								AND servicecontractsid != ''  ## 合同不能为空
								and contract_no != ''
								AND contractstate = '0'       ## 合同的关闭状态为正常
								and sideagreement=0
								and unit_price>0
								AND vtiger_servicecontracts.modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed')
								/*AND vtiger_servicecontracts.effectivetime >='{$dateTime}'*/
								AND receivedpaymentsid NOT IN (
									SELECT DISTINCT
										receivepaymentid
									FROM
										`vtiger_receivedpayments_throw`
									WHERE
										userid = ?
									AND vtiger_receivedpayments_throw.deleted=0
								)
							) OR

							(
								(relatetoid = '' or relatetoid=0 or relatetoid is null)
								AND receivedstatus = 'normal'
								AND receiveid = ?
								AND maybe_account != ''
								AND servicecontractsid != ''
								and contract_no != ''
								AND vtiger_servicecontracts.modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed')
								/*AND vtiger_servicecontracts.effectivetime >='{$dateTime}'*/
								AND contractstate = '0'       ## 合同的关闭状态为正常
								and sideagreement=0
								and unit_price>0
								AND receivedpaymentsid NOT IN (
									SELECT DISTINCT
										receivepaymentid
									FROM
										`vtiger_receivedpayments_throw`
									WHERE
										userid = ?
									AND vtiger_receivedpayments_throw.deleted=0
								)
							) OR
							(
								(relatetoid = '' or relatetoid=0 or relatetoid is null)
								AND receivedstatus = 'normal'
								AND maybe_account != ''
								AND servicecontractsid != ''
								AND vtiger_servicecontracts.modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed')
								AND vtiger_crmentity.smownerid = ?     ## ## 可能客户的负责人是当前用户
								AND servicecontractsid != ''  ## 合同不能为空
								and contract_no != ''
								AND contractstate = '0'       ## 合同的关闭状态为正常
								and sideagreement=0
								and unit_price>0
								/*AND vtiger_servicecontracts.effectivetime >='{$dateTime}'*/
								AND receivedpaymentsid NOT IN (
									SELECT DISTINCT
										receivepaymentid
									FROM
										`vtiger_receivedpayments_throw`
									WHERE
										userid = ?
									AND vtiger_receivedpayments_throw.deleted=0
								)
							) OR
							(
								(relatetoid = '' or relatetoid=0 or relatetoid is null)
								AND receivedstatus = 'normal'
								AND maybe_account != ''
								AND servicecontractsid != ''
								AND vtiger_servicecontracts.modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed')
								AND EXISTS(SELECT 1 FROM vtiger_shareaccount WHERE vtiger_shareaccount.userid=? and vtiger_shareaccount.sharestatus=1 AND vtiger_shareaccount.accountid=vtiger_servicecontracts.sc_related_to)     ## ## 共享商务
								AND servicecontractsid != ''  ## 合同不能为空
								and contract_no != ''
								AND contractstate = '0'       ## 合同的关闭状态为正常
								and sideagreement=0
								and unit_price>0
								/*AND vtiger_servicecontracts.effectivetime >='{$dateTime}'*/
								AND receivedpaymentsid NOT IN (
									SELECT DISTINCT
										receivepaymentid
									FROM
										`vtiger_receivedpayments_throw`
									WHERE
										userid = ?
									AND vtiger_receivedpayments_throw.deleted=0
								)
							)";
        //$No_serviecsql2 = "SELECT vtiger_receivedpayments.*, vtiger_staypayment.staypaymentname,vtiger_servicecontracts.contract_no,vtiger_servicecontracts.servicecontractsid FROM vtiger_receivedpayments LEFT JOIN vtiger_staypayment ON vtiger_staypayment.staypaymentname = vtiger_receivedpayments.paytitle LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_staypayment.contractid INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_staypayment.staypaymentid WHERE relatetoid = '' AND receiveid = ? AND staypaymentname != '' AND deleted = 0 AND receivedpaymentsid NOT IN (SELECT DISTINCT receivepaymentid FROM `vtiger_ReceivedPayments_throw` WHERE userid = ?)";
        /*$No_serviecsql2 = "SELECT vtiger_receivedpayments.*, vtiger_staypayment.staypaymentname,vtiger_servicecontracts.contract_no,vtiger_servicecontracts.servicecontractsid FROM vtiger_receivedpayments LEFT JOIN vtiger_staypayment ON vtiger_staypayment.staypaymentname = vtiger_receivedpayments.paytitle LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_staypayment.contractid INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_staypayment.staypaymentid WHERE relatetoid = '' AND receiveid = ? AND staypaymentname != '' AND deleted = 0 AND( overdute  IS NULL OR overdute > SYSDATE()) AND receivedpaymentsid NOT IN ( SELECT DISTINCT receivepaymentid FROM `vtiger_ReceivedPayments_throw` WHERE userid = ? )";*/
        // 20161108周海
        $No_serviecsql2 = "SELECT
								1
							FROM
								vtiger_receivedpayments
                            LEFT JOIN vtiger_staypayment ON  ( (vtiger_staypayment.staypaymentname = vtiger_receivedpayments.paytitle and vtiger_staypayment.staypaymentname!='' ) or (vtiger_staypayment.payer like REPLACE(vtiger_receivedpayments.paytitle,'*','_')))
							LEFT JOIN vtiger_servicecontracts ON (vtiger_servicecontracts.servicecontractsid = vtiger_staypayment.contractid and vtiger_servicecontracts.modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed'))
							INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_staypayment.staypaymentid
							LEFT JOIN vtiger_account ON vtiger_receivedpayments.maybe_account = vtiger_account.accountid
							WHERE   
									vtiger_receivedpayments.paymentchannel='支付宝转账' AND
							    vtiger_receivedpayments.deleted=0 and 
								(((vtiger_receivedpayments.relatetoid = '' OR vtiger_receivedpayments.relatetoid = 0 OR vtiger_receivedpayments.relatetoid is null) AND vtiger_receivedpayments.receivedstatus = 'normal') OR (vtiger_receivedpayments.relatetoid>0 AND vtiger_receivedpayments.receivedstatus= 'NonPayCertificate'))
							AND (vtiger_servicecontracts.receiveid = ? OR vtiger_account.serviceid = ? OR vtiger_servicecontracts.signid=?
							OR EXISTS (SELECT 1 FROM vtiger_crmentity AS crmcontract WHERE crmcontract.crmid=vtiger_servicecontracts.servicecontractsid AND crmcontract.smownerid=?)
							OR EXISTS (SELECT 1 FROM vtiger_crmentity AS crmaccount WHERE crmaccount.crmid=vtiger_servicecontracts.sc_related_to AND crmaccount.smownerid=?)
							)     ## ## 客服负责人事当前用户
							AND vtiger_crmentity.deleted = 0
							AND servicecontractsid != ''
							and contract_no != ''
							AND vtiger_servicecontracts.modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed')
							AND contractstate = '0'       ## 合同的关闭状态为正常
							and sideagreement=0
							and unit_price>0
							and (vtiger_staypayment.payer is not null and vtiger_staypayment.payer!='')
							AND receivedpaymentsid NOT IN (
								SELECT DISTINCT
									receivepaymentid
								FROM
									`vtiger_receivedpayments_throw`
								WHERE
									userid = ?
								AND vtiger_receivedpayments_throw.deleted=0
							)
							AND vtiger_staypayment.modulestatus !='a_exception'
                          AND
                              (
                                (vtiger_staypayment.staypaymenttype='nofixation' and vtiger_receivedpayments.reality_date>=vtiger_staypayment.startdate and vtiger_receivedpayments.reality_date<=vtiger_staypayment.enddate) 
                                  or 
                                (vtiger_staypayment.staypaymenttype='fixation' and vtiger_receivedpayments.unit_price<=vtiger_staypayment.surplusmoney  and vtiger_staypayment.surplusmoney>0)
                              )
						UNION all 
		                    SELECT
								1
							FROM
								vtiger_receivedpayments
                            LEFT JOIN vtiger_staypayment ON  ( (vtiger_staypayment.staypaymentname = vtiger_receivedpayments.paytitle and vtiger_staypayment.staypaymentname!='' ) or (vtiger_staypayment.payer = vtiger_receivedpayments.paytitle))
							LEFT JOIN vtiger_servicecontracts ON (vtiger_servicecontracts.servicecontractsid = vtiger_staypayment.contractid and vtiger_servicecontracts.modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed'))
							INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_staypayment.staypaymentid
							LEFT JOIN vtiger_account ON vtiger_receivedpayments.maybe_account = vtiger_account.accountid
							WHERE   
									IFNULL(vtiger_receivedpayments.paymentchannel,'')!='支付宝转账' AND
							    vtiger_receivedpayments.deleted=0 and 
								(((vtiger_receivedpayments.relatetoid = '' OR vtiger_receivedpayments.relatetoid = 0 OR vtiger_receivedpayments.relatetoid is null) AND vtiger_receivedpayments.receivedstatus = 'normal') OR (vtiger_receivedpayments.relatetoid>0 AND vtiger_receivedpayments.receivedstatus= 'NonPayCertificate'))
							AND (vtiger_servicecontracts.receiveid = ? OR vtiger_account.serviceid = ? OR vtiger_servicecontracts.signid=?
							OR EXISTS (SELECT 1 FROM vtiger_crmentity AS crmcontract WHERE crmcontract.crmid=vtiger_servicecontracts.servicecontractsid AND crmcontract.smownerid=?)
							OR EXISTS (SELECT 1 FROM vtiger_crmentity AS crmaccount WHERE crmaccount.crmid=vtiger_servicecontracts.sc_related_to AND crmaccount.smownerid=?)
							)     ## ## 客服负责人事当前用户
							AND vtiger_crmentity.deleted = 0
							AND servicecontractsid != ''
							and contract_no != ''
							AND vtiger_servicecontracts.modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed')
							AND contractstate = '0'       ## 合同的关闭状态为正常
							and sideagreement=0
							and unit_price>0
							and (vtiger_staypayment.payer is not null and vtiger_staypayment.payer!='')
							AND receivedpaymentsid NOT IN (
								SELECT DISTINCT
									receivepaymentid
								FROM
									`vtiger_receivedpayments_throw`
								WHERE
									userid = ?
								AND vtiger_receivedpayments_throw.deleted=0
							)
							AND vtiger_staypayment.modulestatus !='a_exception'
                          AND
                              (
                                (vtiger_staypayment.staypaymenttype='nofixation' and vtiger_receivedpayments.reality_date>=vtiger_staypayment.startdate and vtiger_receivedpayments.reality_date<=vtiger_staypayment.enddate) 
                                  or 
                                (vtiger_staypayment.staypaymenttype='fixation' and vtiger_receivedpayments.unit_price<=vtiger_staypayment.surplusmoney  and vtiger_staypayment.surplusmoney>0)
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

        $No_serviecsql3 ="SELECT 1 FROM vtiger_receivedpayments
                            LEFT JOIN vtiger_suppliercontracts ON vtiger_suppliercontracts.vendorid=vtiger_receivedpayments.accountid
                            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_suppliercontracts.suppliercontractsid
                            WHERE 
                            vtiger_receivedpayments.deleted=0 and 
                                vtiger_receivedpayments.receivedstatus='RebateAmount'
                            AND vtiger_receivedpayments.deleted=0
                            AND vtiger_receivedpayments.relatetoid=0 
                            AND vtiger_receivedpayments.accountid>0
                            AND vtiger_crmentity.deleted=0
                            /*AND vtiger_suppliercontracts.effectivetime>='{$dateTime}'*/
                            AND (vtiger_suppliercontracts.signid=? OR vtiger_crmentity.smownerid=?
                            OR EXISTS (SELECT 1 FROM vtiger_crmentity AS crmaccount WHERE crmaccount.crmid=vtiger_receivedpayments.accountid AND crmaccount.smownerid=?)
                            )
                            AND receivedpaymentsid NOT IN (
                            SELECT DISTINCT receivepaymentid
                            FROM `vtiger_receivedpayments_throw`
                            WHERE userid = ?
                            AND vtiger_receivedpayments_throw.deleted=0)
                            ";
            $No_serviecsql4 ="SELECT 1 FROM vtiger_receivedpayments
                            LEFT JOIN vtiger_staypayment ON vtiger_staypayment.staypaymentname =vtiger_receivedpayments.paytitle
                            LEFT JOIN vtiger_suppliercontracts ON (vtiger_suppliercontracts.suppliercontractsid = vtiger_staypayment.contractid  and vtiger_suppliercontracts.modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed'))
                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_suppliercontracts.suppliercontractsid
                            WHERE
                                vtiger_receivedpayments.deleted=0 and 
                                (vtiger_receivedpayments.relatetoid = '' OR vtiger_receivedpayments.relatetoid = 0) AND vtiger_receivedpayments.receivedstatus = 'RebateAmount'
                            AND (vtiger_suppliercontracts.signid=? OR vtiger_crmentity.smownerid=?
                            OR EXISTS (SELECT 1 FROM vtiger_crmentity AS crmaccount WHERE crmaccount.crmid=vtiger_receivedpayments.accountid AND crmaccount.smownerid=?)
                            )
                            AND staypaymentname != ''
                            AND vtiger_crmentity.deleted = 0
                            AND vtiger_suppliercontracts.modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed')
							AND receivedpaymentsid NOT IN (
                                SELECT DISTINCT receivepaymentid
                                FROM `vtiger_receivedpayments_throw`
                                WHERE userid = ?
                                AND vtiger_receivedpayments_throw.deleted=0)
                          AND
                              (
                                (vtiger_staypayment.staypaymenttype='nofixation' and vtiger_receivedpayments.reality_date>=vtiger_staypayment.startdate and vtiger_receivedpayments.reality_date<=vtiger_staypayment.enddate) 
                                  or 
                                (vtiger_staypayment.staypaymenttype='fixation' and vtiger_receivedpayments.unit_price<=vtiger_staypayment.surplusmoney  and vtiger_staypayment.surplusmoney>0)
                              )
                         AND 
                            vtiger_staypayment.staypaymentname=vtiger_receivedpayments.paytitle
                         /*AND vtiger_suppliercontracts.effectivetime>='{$dateTime}'*/
";
        $adb =PearDatabase::getInstance();
            /*
            $result = $adb->pquery($No_serviecsql, array($currentid,$currentid, $currentid, $currentid, $currentid, $currentid, $currentid, $currentid));
            $result2 = $adb->pquery($No_serviecsql2, array($currentid,$currentid,$currentid,$currentid,$currentid,$currentid));
            $result3 = $adb->pquery($No_serviecsql3, array($currentid,$currentid,$currentid,$currentid));
            $result4 = $adb->pquery($No_serviecsql4, array($currentid,$currentid,$currentid,$currentid));

            if($adb->num_rows($result)>0 || $adb->num_rows($result2)>0 || $adb->num_rows($result3)>0 || $adb->num_rows($result4)>0){
                if($currentUser && $request->get('module') !== 'Matchreceivements' && !$request->isAjax()){
                    header('Location:index.php?module=Matchreceivements&view=List');
                }
                }*/
            $gotoMatchreceivements=false;

            $result = $adb->pquery($No_serviecsql, array($currentid,$currentid, $currentid, $currentid, $currentid, $currentid, $currentid, $currentid));
            do{
                if($adb->num_rows($result)>0){
                    $gotoMatchreceivements=true;
                    break;
                }
                $result2 = $adb->pquery($No_serviecsql2, array($currentid,$currentid,$currentid,$currentid,$currentid,$currentid,$currentid,$currentid,$currentid,$currentid,$currentid,$currentid));
                if($adb->num_rows($result2)>0){
                    $gotoMatchreceivements=true;
                    break;
                }
                $result3 = $adb->pquery($No_serviecsql3, array($currentid,$currentid,$currentid,$currentid));
                if($adb->num_rows($result3)>0){
                    $gotoMatchreceivements=true;
                    break;
                }
                $result4 = $adb->pquery($No_serviecsql4, array($currentid,$currentid,$currentid,$currentid));
                if($adb->num_rows($result4)>0){
                    $gotoMatchreceivements=true;
                }
            }while(0);
            if($gotoMatchreceivements){
                if($currentUser && $request->get('module') !== 'Matchreceivements' && !$request->isAjax()){
//                    header('Location:index.php?module=Matchreceivements&view=List');
//                    exit;
                }
            }else{
                $_SESSION['CHECKLOADING']=time()+180;
            }
        }

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
	public function http_request($url,$data=array()){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        $contents = curl_exec($ch);
        curl_close($ch);
        return $contents;
    }
    public function getSSOinfo($__vt_param__,$doSession=1){
        $url=$this->sso."validate/findAuthInfo?token=".$__vt_param__;
        $data=$this->http_request($url);
        $data=json_decode($data);
        $this->_logs(array("5"=>$data));
        if($data->resultCode==200){
            $flag=false;
            if($doSession==1){
                $this->_logs(array("51"=>$data,'doSession'=>$doSession));
                $flag=true;
            }
            if($doSession==2){
                $this->_logs(array("52"=>$data,'doSession'=>$doSession));
                if($data->result->userid!=$_SESSION['authenticated_user_id']){
                    if($data->result->userid>0){
                        $flag=true;
                    }else{
                        global $site_URL;
                        Vtiger_Session::destroy();
                        setcookie("token", "", time() - 3600);
                        header("Location: ".$this->sso."login?backUrl=".urlencode($site_URL));
                        exit;
                    }
                }
            }
            if($flag){
                $_SESSION['authenticated_user_id']=$data->result->userid;
                $_SESSION['userdepartmentid']=$data->result->departmentid;
                $_SESSION['vt_param']=$__vt_param__;
                $user = CRMEntity::getInstance('Users');
                $userid=$data->result->userid;
                $username=$data->result->username;
                $user->delUserprivileges($userid);
                $user->checkUserprivileges($userid,$user->last_modifiedtime);
                $user->retrieveCurrentUserInfoFromFile($data->result->userid);
                Vtiger_Session::set('AUTHUSERID', $_SESSION['authenticated_user_id']);
                Vtiger_Session::set('userdepartmentid', $_SESSION['userdepartmentid']);
                Vtiger_Session::set('vt_param', $__vt_param__);
                $_SESSION['KCFINDER'] = array();
                $_SESSION['KCFINDER']['disabled'] = false;
                $_SESSION['KCFINDER']['uploadURL'] = "test/upload";
                $_SESSION['KCFINDER']['uploadDir'] = "test/upload";
                $deniedExts = implode(" ", vglobal('upload_badext'));
                $_SESSION['KCFINDER']['deniedExts'] = $deniedExts;
                //$cookie=cookiecode($username.'##'.$userid,'ENCODE');
                //setcookie("tlcrm",base64_encode($cookie),NULL,NULL,NULL,NULL,true);
                setcookie("token",$__vt_param__,NULL,NULL,NULL,NULL,true);
                $moduleModel = Users_Module_Model::getInstance('Users');
                $moduleModel->saveLoginHistory($data->result->username);
            }
        }else{
            global $site_URL;
            Vtiger_Session::destroy();
            setcookie("token", "", time() - 3600);
            header("Location: ".$this->sso."login?backUrl=".urlencode($site_URL));
            exit;
        }
    }
    public function setUserSession(){

    }

    public function _logs($data, $file = 'logs_'){
        $year	= date("Y");
        $month	= date("m");
        $dir	= './logs/session/' . $year . '/' . $month . '/';
        if(!is_dir($dir)) {
            mkdir($dir,0755,true);
        }
        $file = $dir . $file . date('Y-m-d').'.txt';
        @file_put_contents($file, '----------------' . date('H:i:s') . '--------------------'.PHP_EOL.var_export($data,true).PHP_EOL, FILE_APPEND);
    }
}
