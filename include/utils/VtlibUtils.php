<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/*
 * Check for image existence in themes orelse
 * use the common one.
 */
// Let us create cache to improve performance
if(!isset($__cache_vtiger_imagepath)) {
    $__cache_vtiger_imagepath = Array();
}
/**
 * 获取图片 地址，注意名称一致可能会被覆盖，查找顺序主题下模块images文件夹，主题下images文件夹
 * @param unknown $imagename
 * @param unknown $themename
 * @return Ambigous <boolean, string, unknown>
 */
function vtiger_imageurl($imagename, $themename) {
	global $__cache_vtiger_imagepath;
	if($__cache_vtiger_imagepath[$imagename]) {
        $imagepath = $__cache_vtiger_imagepath[$imagename];
    } else {
		$imagepath = false;
		// Check in theme specific folder
        if(file_exists("themes/$themename/images/$imagename")) {
            $imagepath =  "themes/$themename/images/$imagename";
		} else if(file_exists("themes/images/$imagename")) {
			// Search in common image folder
			$imagepath = "themes/images/$imagename";
		} else {
			// Not found anywhere? Return whatever is sent
			$imagepath = $imagename;
		}
        $__cache_vtiger_imagepath[$imagename] = $imagepath;
    }
	return $imagepath;
}

/**
 * 获取指定id的模块的名称
 * @todo有待改进的方法，没有必要每次都去读取数据库
 */
function vtlib_getModuleNameById($tabid) {
	global $adb;
	$sqlresult = $adb->pquery("SELECT name FROM vtiger_tab WHERE tabid = ?",array($tabid));
	if($adb->num_rows($sqlresult)) return $adb->query_result($sqlresult, 0, 'name');
	return null;
}

/**
 * Get module names for which sharing access can be controlled.
 * 获得共享，我们不使用，作废
 * NOTE: Ignore the standard modules which is already handled.
 */
function vtlib_getModuleNameForSharing() {
	global $adb;
	$std_modules = array('Calendar','Leads','Accounts','Contacts','Potentials',
			'HelpDesk','Campaigns','Quotes','PurchaseOrder','SalesOrder','Invoice','Events');
	$modulesList = getSharingModuleList($std_modules);
	return $modulesList;
}

/**
 * Cache the module active information for performance
 */
$__cache_module_activeinfo = Array();

/**
 * 将vtiger_tab所有的数据缓存起来，并返回，同时根据tabname讲presence缓存起来
 * @param string $force  (true or false)
 * @return Ambigous <array>
 */
function vtlib_prefetchModuleActiveInfo($force = true) {
	global $__cache_module_activeinfo;

	// Look up if cache has information
	$tabrows = VTCacheUtils::lookupAllTabsInfo();//获取所有的module信息，

	// Initialize from DB if cache information is not available or force flag is set
	if($tabrows === false || $force) {
		global $adb;
		$tabres = $adb->query("SELECT * FROM vtiger_tab");
		$tabrows = array();
		if($tabres) {
			while($tabresrow = $adb->fetch_array($tabres)) {
				$tabrows[] = $tabresrow;
				$__cache_module_activeinfo[$tabresrow['name']] = $tabresrow['presence'];//缓存
			}
			// Update cache for further re-use
			VTCacheUtils::updateAllTabsInfo($tabrows);//更新缓存，保存module的所有信息
		}
	}

	return $tabrows;
}

/**
 * Check if module is set active (or enabled)
 * 返回模块是否可用
 * @todo方法有问题，如果tabdata是跟vtiger_tab数据出现不一致的情况，可能导致判断出现失败,不知道做什么用处。
 */
function vtlib_isModuleActive($module) {
	global $adb, $__cache_module_activeinfo;
	//永久保留模块
	if(in_array($module, vtlib_moduleAlwaysActive())){
		return true;
	}
	//@todo 如果新增了tabdata里面不包含的module那么会出错
	//20150108 by joe.yi 关联到modelinfo缓存文件
	if(!isset($__cache_module_activeinfo[$module])) {
		include 'crmcache/modelinfo.php';
		//$tabId = $tab_info_array[$module];
		$presence = $modelinfo[$module]['presence'];
		$__cache_module_activeinfo[$module] = $presence;
	} else {
		$presence = $__cache_module_activeinfo[$module];
	}

	if($presence == 0)  return true;
	return false;
}

/**
 * 重新建立用户权限文件，前提条件是deleted=0
 */
function vtlib_RecreateUserPrivilegeFiles() {
	global $adb;
	$userres = $adb->query('SELECT id FROM vtiger_users WHERE deleted = 0');
	if($userres && $adb->num_rows($userres)) {
		while($userrow = $adb->fetch_array($userres)) {
			createUserPrivilegesfile($userrow['id']);
		}
	}
}

/**
 * 获取有效的module名称列表
 * Get list module names which are always active (cannot be disabled)
 */
function vtlib_moduleAlwaysActive() {
	$modules = Array (
		'Administration', 'CustomView', 'Settings', 'Users', 'Migration',
		'Utilities', 'uploads', 'Import', 'System', 'com_vtiger_workflow', 'PickList'
	);
	return $modules;
}

/**
 * 模块触发器（enable/disable）
 * Toggle the module (enable/disable)
 * @param unknown $module
 * @param unknown $enable_disable
 */
function vtlib_toggleModuleAccess($module, $enable_disable) {
	global $adb, $__cache_module_activeinfo;

	include_once('vtlib/Vtiger/Module.php');

	$event_type = false;

	if($enable_disable === true) {
		$enable_disable = 0;
		$event_type = Vtiger_Module::EVENT_MODULE_ENABLED;
	} else if($enable_disable === false) {
		$enable_disable = 1;
		$event_type = Vtiger_Module::EVENT_MODULE_DISABLED;
	}

	$adb->pquery("UPDATE vtiger_tab set presence = ? WHERE name = ?", array($enable_disable,$module));

	$__cache_module_activeinfo[$module] = $enable_disable;

	create_tab_data_file();//tabdata.php
	create_parenttab_data_file();

	// UserPrivilege file needs to be regenerated if module state is changed from
	// vtiger 5.1.0 onwards
	global $vtiger_current_version;
	if(version_compare($vtiger_current_version, '5.0.4', '>')) {
		vtlib_RecreateUserPrivilegeFiles();
	}

	Vtiger_Module::fireEvent($module, $event_type);
}

/**
 * Get list of module with current status which can be controlled.
 * 获取当前可被控制的模块的当前的状态,presence in(0,1),作废的方法，没有地方使用到
 */
function vtlib_getToggleModuleInfo() {
	global $adb;

	$modinfo = Array();

	$sqlresult = $adb->query("SELECT name, presence, customized, isentitytype FROM vtiger_tab WHERE name NOT IN ('Users','Home') AND presence IN (0,1) ORDER BY name");
	$num_rows  = $adb->num_rows($sqlresult);
	for($idx = 0; $idx < $num_rows; ++$idx) {
		$module = $adb->query_result($sqlresult, $idx, 'name');
		$presence=$adb->query_result($sqlresult, $idx, 'presence');
		$customized=$adb->query_result($sqlresult, $idx, 'customized');
		$isentitytype=$adb->query_result($sqlresult, $idx, 'isentitytype');
		$hassettings=file_exists("modules/$module/Settings.php");

		$modinfo[$module] = Array( 'customized'=>$customized, 'presence'=>$presence, 'hassettings'=>$hassettings, 'isentitytype' => $isentitytype );
	}
	return $modinfo;
}

/**
 * 作废没有找到使用的地方
 * Get list of language and its current status.
 */
function vtlib_getToggleLanguageInfo() {
	global $adb;

	// The table might not exists!
	$old_dieOnError = $adb->dieOnError;
	$adb->dieOnError = false;

	$langinfo = Array();
	$sqlresult = $adb->query("SELECT * FROM vtiger_language");
	if($sqlresult) {
		for($idx = 0; $idx < $adb->num_rows($sqlresult); ++$idx) {
			$row = $adb->fetch_array($sqlresult);
			$langinfo[$row['prefix']] = Array( 'label'=>$row['label'], 'active'=>$row['active'] );
		}
	}
	$adb->dieOnError = $old_dieOnError;
	return $langinfo;
}

/**
 * 作废没有找到使用的地方
 * Toggle the language (enable/disable)
 */
function vtlib_toggleLanguageAccess($langprefix, $enable_disable) {
	global $adb;

	// The table might not exists!
	$old_dieOnError = $adb->dieOnError;
	$adb->dieOnError = false;

	if($enable_disable === true) $enable_disable = 1;
	else if($enable_disable === false) $enable_disable = 0;

	$adb->pquery('UPDATE vtiger_language set active = ? WHERE prefix = ?', Array($enable_disable, $langprefix));

	$adb->dieOnError = $old_dieOnError;
}

/**
 * 作废没有找到使用的地方
 * Get help information set for the module fields.
 */
function vtlib_getFieldHelpInfo($module) {
	global $adb;
	$fieldhelpinfo = Array();
	if(in_array('helpinfo', $adb->getColumnNames('vtiger_field'))) {
		$result = $adb->pquery('SELECT fieldname,helpinfo FROM vtiger_field WHERE tabid=?', Array(getTabid($module)));
		if($result && $adb->num_rows($result)) {
			while($fieldrow = $adb->fetch_array($result)) {
				$helpinfo = decode_html($fieldrow['helpinfo']);
				if(!empty($helpinfo)) {
					$fieldhelpinfo[$fieldrow['fieldname']] = getTranslatedString($helpinfo, $module);
				}
			}
		}
	}
	return $fieldhelpinfo;
}

/**
 * 设置托管模块的类的变量值，即如果要新增一个模块变量这里同样需要修改
 * Setup mandatory (requried) module variable values in the module class.
 */
function vtlib_setup_modulevars($module, $focus) {

	$checkfor = Array('table_name', 'table_index', 'related_tables', 'popup_fields', 'IsCustomModule');
	foreach($checkfor as $check) {
		if(!isset($focus->$check)) $focus->$check = __vtlib_get_modulevar_value($module, $check);
	}
}
/**
 * 默认的模块类的变量值，如果不设置的话
 * @param unknown $module
 * @param unknown $varname
 * @return array
 */
function __vtlib_get_modulevar_value($module, $varname) {
	$mod_var_mapping =
		Array(
			'Accounts' =>
			Array(
				'IsCustomModule'=>false,
				'table_name'  => 'vtiger_account',
				'table_index' => 'accountid',
				// related_tables variable should define the association (relation) between dependent tables
				// FORMAT: related_tablename => Array ( related_tablename_column[, base_tablename, base_tablename_column] )
				// Here base_tablename_column should establish relation with related_tablename_column
				// NOTE: If base_tablename and base_tablename_column are not specified, it will default to modules (table_name, related_tablename_column)
				'related_tables' => Array(
					'vtiger_accountbillads' => Array ('accountaddressid', 'vtiger_account', 'accountid'),
					'vtiger_accountshipads' => Array ('accountaddressid', 'vtiger_account', 'accountid'),
					'vtiger_accountscf' => Array ('accountid', 'vtiger_account', 'accountid'),
				),
				'popup_fields' => Array('accountname'), // TODO: Add this initialization to all the standard module
			),
			'Contacts' =>
			Array(
				'IsCustomModule'=>false,
				'table_name'  => 'vtiger_contactdetails',
				'table_index' => 'contactid',
				'related_tables'=> Array( 
					'vtiger_account' => Array ('accountid' ),
					//REVIEW: Added these tables for displaying the data into relatedlist (based on configurable fields)
					'vtiger_contactaddress' => Array('contactaddressid', 'vtiger_contactdetails', 'contactid'),
					'vtiger_contactsubdetails' => Array('contactsubscriptionid', 'vtiger_contactdetails', 'contactid'),
					'vtiger_customerdetails' => Array('customerid', 'vtiger_contactdetails', 'contactid'),
					'vtiger_contactscf' => Array('contactid', 'vtiger_contactdetails', 'contactid')
					),
				'popup_fields' => Array ('lastname'),
			),
			'Leads' =>
			Array(
				'IsCustomModule'=>false,
				'table_name'  => 'vtiger_leaddetails',
				'table_index' => 'leadid',
				'related_tables' => Array (
					'vtiger_leadsubdetails' => Array ( 'leadsubscriptionid', 'vtiger_leaddetails', 'leadid' ),
					'vtiger_leadaddress'    => Array ( 'leadaddressid', 'vtiger_leaddetails', 'leadid' ),
					'vtiger_leadscf'    => Array ( 'leadid', 'vtiger_leaddetails', 'leadid' ),
				),
				'popup_fields'=> Array ('lastname'),
			),
			'Campaigns' =>
			Array(
				'IsCustomModule'=>false,
				'table_name'  => 'vtiger_campaign',
				'table_index' => 'campaignid',
				'popup_fields' => Array ('campaignname'),
			),
			'Potentials' =>
			Array(
				'IsCustomModule'=>false,
				'table_name' => 'vtiger_potential',
				'table_index'=> 'potentialid',
				// NOTE: UIType 10 is being used instead of direct relationship from 5.1.0
				//'related_tables' => Array ('vtiger_account' => Array('accountid')),
				'popup_fields'=> Array('potentialname'),
				'related_tables' => Array (
					'vtiger_potentialscf'    => Array ( 'potentialid', 'vtiger_potential', 'potentialid' ),
				),
			),
			'Quotes' =>
			Array(
				'IsCustomModule'=>false,
				'table_name' => 'vtiger_quotes',
				'table_index'=> 'quoteid',
				'related_tables' => Array ('vtiger_account' => Array('accountid')),
				'popup_fields'=>Array('subject'),
			),
			'SalesOrder'=>
			Array(
				'IsCustomModule'=>false,
				'table_name' => 'vtiger_salesorder',
				'table_index'=> 'salesorderid',
				'related_tables'=> Array ('vtiger_account' => Array('accountid')),
				'popup_fields'=>Array('subject'),
			),
			'PurchaseOrder'=>
			Array(
				'IsCustomModule'=>false,
				'table_name' => 'vtiger_purchaseorder',
				'table_index'=> 'purchaseorderid',
				'popup_fields'=>Array('subject'),
			),
			'Invoice'=>
			Array(
				'IsCustomModule'=>false,
				'table_name' => 'vtiger_invoice',
				'table_index'=> 'invoiceid',
				'popup_fields'=> Array('subject'),
			),
			'HelpDesk'=>
			Array(
				'IsCustomModule'=>false,
				'table_name' => 'vtiger_troubletickets',
				'table_index'=> 'ticketid',
				'related_tables'=> Array ('vtiger_ticketcf' => Array('ticketid')),
				'popup_fields'=> Array('ticket_title')
			),
			'Faq'=>
			Array(
				'IsCustomModule'=>false,
				'table_name' => 'vtiger_faq',
				'table_index'=> 'id',
			),
			'Documents'=>
			Array(
				'IsCustomModule'=>false,
				'table_name' => 'vtiger_notes',
				'table_index'=> 'notesid',
			),
			'Products'=>
			Array(
				'IsCustomModule'=>false,
				'table_name' => 'vtiger_products',
				'table_index'=> 'productid',
				'popup_fields'=> Array('productname'),
			),
			'PriceBooks'=>
			Array(
				'IsCustomModule'=>false,
				'table_name' => 'vtiger_pricebook',
				'table_index'=> 'pricebookid',
			),
			'Vendors'=>
			Array(
				'IsCustomModule'=>false,
				'table_name' => 'vtiger_vendor',
				'table_index'=> 'vendorid',
				'popup_fields'=>Array('vendorname'),
			),
			'Project' => 
			Array(
				'IsCustomModule'=>false,
				'table_name' => 'vtiger_project',
				'table_index'=> 'projectid',
				'related_tables'=> Array( 
					'vtiger_projectcf' => Array('projectid', 'vtiger_project', 'projectid')
					),
			),
            'ProjectMilestone' =>
            Array(
                'IsCustomModule'=>false,
				'table_name' => 'vtiger_projectmilestone',
				'table_index'=> 'projectmilestoneid',
				'related_tables'=> Array( 
					'vtiger_projectmilestonecf' => Array('projectmilestoneid', 'vtiger_projectmilestone', 'projectmilestoneid')
					),
            ),
            'ProjectTask' => 
            Array(
                'IsCustomModule'=>false,
				'table_name' => 'vtiger_projecttask',
				'table_index'=> 'projecttaskid',
				'related_tables'=> Array( 
					'vtiger_projecttaskcf' => Array('projecttaskid', 'vtiger_projecttask', 'projecttaskid')
					),
            ),
			'Services' => 
			Array(
				'IsCustomModule'=>false,
				'table_name' => 'vtiger_service',
				'table_index'=> 'serviceid',
				'related_tables'=> Array( 
					'vtiger_servicecf' => Array('serviceid')
					),
			),
			'ServiceContracts' => 
			Array(
				'IsCustomModule'=>false,
				'table_name' => 'vtiger_servicecontracts',
				'table_index'=> 'servicecontractsid',
				'related_tables'=> Array( 
					'vtiger_servicecontractscf' => Array('servicecontractsid')
					),
			)
		);
	return $mod_var_mapping[$module][$varname];
}


/**
 * 将text转化成单数
 * @param unknown $text
 * @return string|unknown
 */
function vtlib_tosingular($text) {
	$lastpos = strripos($text, 's');
	if($lastpos == strlen($text)-1)
		return substr($text, 0, -1);
	return $text;
}


//Get picklist values that is accessible by all roles.
/**
 * 从所有的角色中获取允许访问的picklist值,在导入报的时候有用处
 * @param unknown $field_columnname
 * @return array
 */
function vtlib_getPicklistValues_AccessibleToAll($field_columnname) {
	global $adb;
	$columnname =  $adb->sql_escape_string($field_columnname);
	$tablename = "vtiger_$columnname";

	// Gather all the roles (except H1 which is organization role)
	$roleres = $adb->query("SELECT roleid FROM vtiger_role WHERE roleid != 'H1'");
	$roleresCount= $adb->num_rows($roleres);
	$allroles = Array();
	if($roleresCount) {
		for($index = 0; $index < $roleresCount; ++$index)
			$allroles[] = $adb->query_result($roleres, $index, 'roleid');
	}
	sort($allroles);

	// Get all the picklist values associated to roles (except H1 - organization role).
	$picklistres = $adb->query(
		"SELECT $columnname as pickvalue, roleid FROM $tablename
		INNER JOIN vtiger_role2picklist ON $tablename.picklist_valueid=vtiger_role2picklist.picklistvalueid
		WHERE roleid != 'H1'");

	$picklistresCount = $adb->num_rows($picklistres);

	$picklistval_roles = Array();
	if($picklistresCount) {
		for($index = 0; $index < $picklistresCount; ++$index) {
			$picklistval = $adb->query_result($picklistres, $index, 'pickvalue');
			$pickvalroleid=$adb->query_result($picklistres, $index, 'roleid');
			$picklistval_roles[$picklistval][] = $pickvalroleid;
		}
	}
	// Collect picklist value which is associated to all the roles.
	$allrolevalues = Array();
	foreach($picklistval_roles as $picklistval => $pickvalroles) {
		sort($pickvalroles);
		$diff = array_diff($pickvalroles,$allroles);
		if(empty($diff)) $allrolevalues[] = $picklistval;
	}

	return $allrolevalues;
}

/**
 * 暂时没有用到，只有在mobile用到了
 * Get all picklist values for a non-standard picklist type.
 */
function vtlib_getPicklistValues($field_columnname) {
	global $adb;

	$columnname =  $adb->sql_escape_string($field_columnname);
	$tablename = "vtiger_$columnname";

	$picklistres = $adb->query("SELECT $columnname as pickvalue FROM $tablename");

	$picklistresCount = $adb->num_rows($picklistres);

	$picklistvalues = Array();
	if($picklistresCount) {
		for($index = 0; $index < $picklistresCount; ++$index) {
			$picklistvalues[] = $adb->query_result($picklistres, $index, 'pickvalue');
		}
	}
	return $picklistvalues;
}

/**
 * 暂时没有用
 * Check for custom module by its name.
 */
function vtlib_isCustomModule($moduleName) {
	$moduleFile = "modules/$moduleName/$moduleName.php";
	if(file_exists($moduleFile)) {
		if(function_exists('checkFileAccessForInclusion')) {
			checkFileAccessForInclusion($moduleFile);
		}
		include_once($moduleFile);
		$focus = new $moduleName();
		return (isset($focus->IsCustomModule) && $focus->IsCustomModule);
	}
	return false;
}

/**
 * 获取模块的临时模板
 * Get module specific smarty template path.
 */
function vtlib_getModuleTemplate($module, $templateName) {
	return ("modules/$module/$templateName");
}

/**
 * 是否有写权限
 * Check if give path is writeable.
 */
function vtlib_isWriteable($path) {
	if(is_dir($path)) {
		return vtlib_isDirWriteable($path);
	} else {
		return is_writable($path);
	}
}

/**
 * 测试是否有写权限
 * Check if given directory is writeable.
 * NOTE: The check is made by trying to create a random file in the directory.
 */
function vtlib_isDirWriteable($dirpath) {
	if(is_dir($dirpath)) {
		do {
			$tmpfile = 'vtiger' . time() . '-' . rand(1,1000) . '.tmp';
			// Continue the loop unless we find a name that does not exists already.
			$usefilename = "$dirpath/$tmpfile";
			if(!file_exists($usefilename)) break;
		} while(true);
		$fh = @fopen($usefilename,'a');
		if($fh) {
			fclose($fh);
			unlink($usefilename);
			return true;
		}
	}
	return false;
}

/** HTML Purifier global instance */
$__htmlpurifier_instance = false;
/**
 * 过滤输入恶意代码块，采用第三方的htmlpurifier源码
 * Purify (Cleanup) malicious snippets of code from the input
 *
 * @param String $value
 * @param Boolean $ignore Skip cleaning of the input
 * @return String
 */
function vtlib_purify($input, $ignore=false) {
	global $__htmlpurifier_instance, $root_directory, $default_charset;

	static $purified_cache = array();
	if(!is_array($input)) {
		$md5OfInput = md5($input); 
		if (array_key_exists($md5OfInput, $purified_cache)) { //不需要检查的代码
			$value =  $purified_cache[$md5OfInput]; 
            //to escape cleaning up again
            $ignore = true;
		} 
	}
	$use_charset = $default_charset;
	$use_root_directory = $root_directory;

	$value = $input;
	if(!$ignore) {
		// Initialize the instance if it has not yet done
		if($__htmlpurifier_instance == false) {
			if(empty($use_charset)) $use_charset = 'UTF-8';
			if(empty($use_root_directory)) $use_root_directory = dirname(__FILE__) . '/../..';

			include_once ('libraries/htmlpurifier/library/HTMLPurifier.auto.php');

			$config = HTMLPurifier_Config::createDefault();
	    	$config->set('Core', 'Encoding', $use_charset);
	    	$config->set('Cache', 'SerializerPath', "$use_root_directory/test/vtlib");

			$__htmlpurifier_instance = new HTMLPurifier($config);
		}
		if($__htmlpurifier_instance) {
			// Composite type
			if (is_array($input)) {
				$value = array();
				foreach ($input as $k => $v) {
					$value[$k] = vtlib_purify($v, $ignore);
				}
			} else { // Simple type
				$value = $__htmlpurifier_instance->purify($input);
			}
		}
		$purified_cache[$md5OfInput] = $value;
	}
	$value = str_replace('&amp;','&',$value);
	return $value;
}

/**
 * sql语句的验证
 * Function to return the valid SQl input.
 * @param <String> $string
 * @param <Boolean> $skipEmpty Skip the check if string is empty.
 * @return <String> $string/false
 */
function vtlib_purifyForSql($string, $skipEmpty=true) {
	$pattern = "/^[_a-zA-Z0-9.]+$/";
	if ((empty($string) && $skipEmpty) || preg_match($pattern, $string)) {
		return $string;
	}
	return false;
}

/**
 * 暂时没有用
 * Process the UI Widget requested
 * @param Vtiger_Link $widgetLinkInfo
 * @param Current Smarty Context $context
 * @return
 */
function vtlib_process_widget($widgetLinkInfo, $context = false) {
	if (preg_match("/^block:\/\/(.*)/", $widgetLinkInfo->linkurl, $matches)) {
		list($widgetControllerClass, $widgetControllerClassFile) = explode(':', $matches[1]);
		if (!class_exists($widgetControllerClass)) {
			checkFileAccessForInclusion($widgetControllerClassFile);
			include_once $widgetControllerClassFile;
		}
		if (class_exists($widgetControllerClass)) {
			$widgetControllerInstance = new $widgetControllerClass;
			$widgetInstance = $widgetControllerInstance->getWidget($widgetLinkInfo->linklabel);
			if ($widgetInstance) {
				return $widgetInstance->process($context);
			}
		}
	}
	return "";
}
/**
 * 没有用到
 * @param unknown $modulename
 * @return string
 */
function vtlib_module_icon($modulename){
	if($modulename == 'Events'){
		return "modules/Calendar/Events.png";
	}
	if(file_exists("modules/$modulename/$modulename.png")){
		return "modules/$modulename/$modulename.png";
	}
	return "modules/Vtiger/Vtiger.png";
}

?>
