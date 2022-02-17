<?php
/*******************
 * Contains a variety of utility functions used to display UI
 * 菜单读取
 *******************/

abstract class Vtiger_Basic_View extends Vtiger_Footer_View {

	function __construct() {
		parent::__construct();
	}

	function preProcess (Vtiger_Request $request, $display=true) {
		parent::preProcess($request, false);
		$viewer = $this->getViewer($request);
		$selectedModule = $request->getModule(); //当前显示的模块名称
		//缓存用户权限菜单 By Joe@20150417
		global $current_user,$adb;
		$user_name=$current_user->user_name;
		$version = $request->get('version');
		if($version=='v2') {
            $menuLists = Vtiger_Cache::get('menu_v2', $user_name);
            if (empty($menuLists)) {
                $menuIcons = [
                    'MARKETING_AND_SALES'=>'customer.png', //客户
                    'SUPPORT'=>'finance.png', //财务
                    'INVENTORY'=>'work-order.png', //工单
                    'TOOLS'=>'tools.png', //工具
                    'ANALYTICS'=>'hr.png', //人事
                    'SERVICE'=>'customerservice.png', //客服
                    'WORKFLOWS'=>'department.png', //部门
                    'VENDORS'=>'supply.png', //供应商
                    'REPORT'=>'reports.png', //分析报表
                    'SETTING'=>'setting.png'//设置
                ];
                $menuModelsList = Vtiger_Menu_Model::getAll(true);
                $menuStructure = Vtiger_MenuStructure_Model::getInstanceFromMenuList($menuModelsList, $selectedModule);
                $moreMenus = $menuStructure->getMore();
                $menuLists = [];
                foreach ($moreMenus as $parent => $moduleList) {
                    $menu = [
                        'name' => vtranslate('LBL_' . $parent, $selectedModule),
                        'icon' => $menuIcons[$parent],
                        'children' => []
                    ];
                    foreach ($moduleList as $moduleName => $moduleModel) {
                        $menu['children'][] = [
                            'name'=>vtranslate($moduleModel->get('label'), $moduleName),
                            'url'=>$moduleModel->getDefaultUrl()
                        ];
                    }
                    $menuLists[] = $menu;
                }
                //当前判断账号是否有登录权限
                if ($current_user->is_admin=='on' || $current_user->showbackstage==1) {
                    $menuLists[] = [
                        'name' => '设置',
                        'icon' => $menuIcons['SETTING'],
                        'children' => [[
                            'name'=>'CRM设置',
                            'url'=>'?module=Vtiger&parent=Settings&view=Index'
                        ]]
                    ];
                }
                Vtiger_Cache::set('menu_v2', $user_name, $menuLists);
            }
            $viewer->assign('MENULISTS', $menuLists);
        } else {
            $menuhtml = Vtiger_Cache::get('menu', $user_name);
            if (empty($menuhtml)) {
                $menuModelsList = Vtiger_Menu_Model::getAll(true);  //加载所有有权限的菜单
                $menuStructure = Vtiger_MenuStructure_Model::getInstanceFromMenuList($menuModelsList, $selectedModule);  //返回
                $moreMenus = $menuStructure->getMore();
                $menuhtml = '';
                foreach ($moreMenus as $parent => $moduleList) {
                    $menuhtml .= '<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">' . vtranslate('LBL_' . $parent, $selectedModule) . '<b class="caret"></b></a><ul class="dropdown-menu">';
                    foreach ($moduleList as $moduleName => $moduleModel) {
                        //追加用车审核页面调用 gaocl add 2018/04/26
                        if ($moduleName == 'CarApplication') {
                            $arr_userid = array(998, 3618);
                            if (in_array($current_user->id, $arr_userid) || $current_user->is_admin) {
                                $menuhtml .= '<li><a  href="http://192.168.44.222/Examine/examine.html" target="_blank">用车审核</a></li>';
                                //$menuhtml.='<li><a  href="http://192.168.40.239:8080/Examine/examine.html" target="_blank">用车审核</a></li>';
                            }
                        } else {
                            if ($moduleName == 'WorkSummarize') {
                                $menuhtml .= '<li><a  href="http://192.168.7.231:8901/" target="_blank">工作总结（新）</a></li>';
                            } else if ($moduleName == 'VisitingOrder') {
                                $menuhtml .= '<li><a href="https://xxh-gw.71360.com/visit-center/login?__vt_param__=' . $_SESSION['vt_param'] . '&callback=' . urlencode('https://xxh-web.71360.com/visitcenterweb?original=4001') . '" target="_blank">拜访中心</a></li>';
                            }
                            $menuhtml .= '<li><a  href="' . $moduleModel->getDefaultUrl() . '">' . vtranslate($moduleModel->get('label'), $moduleName) . '</a></li>';
                        }
                    }
                    $menuhtml .= '</ul></li>';
                }
                Vtiger_Cache::set('menu', $user_name, $menuhtml);
            }
        }

		$viewer->assign('CURRENTDATE', date('Y-n-j'));
		$viewer->assign('MODULE', $selectedModule);
		$viewer->assign('QUALIFIED_MODULE', $selectedModule);
		$viewer->assign('MODULE_NAME', $selectedModule);
		$viewer->assign('PARENT_MODULE', $request->get('parent'));
		$viewer->assign('VIEW', $request->get('view'));
		$viewer->assign('MYMENU', $menuhtml);
		$homeModuleModel = Vtiger_Module_Model::getInstance('Home');
		$viewer->assign('HOME_MODULE_MODEL', $homeModuleModel);
		$viewer->assign('HEADER_LINKS',$this->getHeaderLinks());
        $viewer->assign('token',$_SESSION['vt_param']);
		// 如果存在
		if(!empty($current_user->usercode)){
            $waterText=$current_user->last_name.($current_user->usercode+0);
        // 不存在
		}else{
            //存在
		    if(!empty($_SESSION['authenticated_user_id'])){
                $userInfo=$adb->pquery(" SELECT last_name,usercode FROM vtiger_users WHERE id=? ",array($_SESSION['authenticated_user_id']));
                $userInfo=$adb->query_result_rowdata($userInfo,0);
                $waterText=$userInfo['last_name'].($userInfo['usercode']+0);
            // 不存在
		    }else{
                // 如果session 再没有那么就 在处理吧
            }
        }
		$viewer->assign('waterText',$waterText);
		if($display) {
			$this->preProcessDisplay($request);
		}
		//$companyDetails = Vtiger_CompanyDetails_Model::getInstanceById();  //公司信息
		//$companyLogo = $companyDetails->getLogo();$currentDate  = Vtiger_Date_UIType::getDisplayDateValue(date('Y-n-j'));
		// Order by pre-defined automation process for QuickCreate.
		//uksort($menuModelsList, array('Vtiger_MenuStructure_Model', 'sortMenuItemsByProcess'));
		//$viewer->assign('MENUS', $menuModelsList);$viewer->assign('MENU_STRUCTURE', $menuStructure);
		//$viewer->assign('MENU_SELECTED_MODULENAME', $selectedModule);$viewer->assign('MENU_TOPITEMS_LIMIT',20);
		//$viewer->assign('COMPANY_LOGO','');$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		//取消读取公告
		//$viewer->assign('ANNOUNCEMENT', $this->getAnnouncement());
		//$viewer->assign('SEARCHABLE_MODULES', Vtiger_Module_Model::getSearchableModules());
	}

	protected function preProcessTplName(Vtiger_Request $request=NULL) {
		return 'BasicHeader.tpl';
	}

	//Note: To get the right hook for immediate parent in PHP,
	// specially in case of deep hierarchy
	/*function preProcessParentTplName(Vtiger_Request $request) {
		return parent::preProcessTplName($request);
	}*/

	function postProcess(Vtiger_Request $request){
		$viewer = $this->getViewer($request);
		//$viewer->assign('GUIDERSJSON', Vtiger_Guider_Model::toJsonList($this->getGuiderModels($request)));
		parent::postProcess($request);
	}

	/**
	 * Function to get the list of Script models to be included
	 * 加载资源 减少JS Edit By Joe@20150528
	 * @param Vtiger_Request $request
	 * @return <Array> - List of Vtiger_JsScript_Model instances
	 */
	function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();

		$jsFileNames = array(
			//2014-12-23 young 去掉老的时间和日期空间，换成新的  start
			//'libraries.bootstrap.js.eternicode-bootstrap-datepicker.js.bootstrap-datepicker',
			//'~libraries/bootstrap/js/eternicode-bootstrap-datepicker/js/locales/bootstrap-datepicker.'.Vtiger_Language_Handler::getShortLanguageName().'.js',
			//'~libraries/jquery/timepicker/jquery.timepicker.min.js',
			//end
            'modules.Vtiger.resources.Header',
			'modules.Vtiger.resources.Edit',
			"modules.$moduleName.resources.Edit",
			'modules.Vtiger.resources.Popup',
			"modules.$moduleName.resources.Popup",
			'modules.Vtiger.resources.Field',
			"modules.$moduleName.resources.Field",
			'modules.Vtiger.resources.validator.BaseValidator',
			'modules.Vtiger.resources.validator.FieldValidator',
			"modules.$moduleName.resources.validator.FieldValidator",
			'libraries.jquery.jquery_windowmsg',
			/* 'modules.Vtiger.resources.BasicSearch',
			"modules.$moduleName.resources.BasicSearch",
			'modules.Vtiger.resources.AdvanceFilter',
			"modules.$moduleName.resources.AdvanceFilter",
			'modules.Vtiger.resources.SearchAdvanceFilter',
			"modules.$moduleName.resources.SearchAdvanceFilter",
			'modules.Vtiger.resources.AdvanceSearch',
			"modules.$moduleName.resources.AdvanceSearch", */
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

	//应该不用重写 By Joe@20150520
	/* public function getHeaderCss(Vtiger_Request $request) {
		$headerCssInstances = parent::getHeaderCss($request);
		print_r($headerCssInstances);
		exit;
		//$cssFileNames = array(
			//'~/libraries/jquery/timepicker/jquery.timepicker.css',
		//);
		//$cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
		//$headerCssInstances = array_merge($headerCssInstances, $cssInstances);

		return $headerCssInstances;
	} */

	function getGuiderModels(Vtiger_Request $request) {
		return array();
	}

}
