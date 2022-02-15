<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');

class Double extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity
    var $table_name = 'vtiger_servicecomments';
    var $table_index= 'servicecommentsid';
    var $column_fields = Array();

    /** Indicator if this is a custom module or standard module */
    var $IsCustomModule = true;

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array();

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    var $tab_name = Array('vtiger_servicecomments');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_servicecomments'   => 'servicecommentsid',);

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array (
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
  

    );
    var $list_fields_name = Array(
        /* Format: Field Label => fieldname */
 
    );

    // Make the field link to detail view from list view (Fieldname)
    var $list_link_field = 'relmodule';

    // For Popup listview and UI type support
    var $search_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
 
    );
    var $search_fields_name = Array(
        /* Format: Field Label => fieldname */
    
    );

    // For Popup window record selection
    var $popup_fields = Array('servicecommentsid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'servicecommentsid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'servicecommentsid';

    // Required Information for enabling Import feature
    var $required_fields = Array('servicecommentsid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('servicecommentsid');

    var $default_order_by = 'servicecommentsid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('servicecommentsid');

    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    /**
     * 更新后处理
     * @param unknown $module
     */
    function save_module($module) {

    }
    
    /**
     * Retrieve record information of the module
     * @param <Integer> $record - crmid of record
     * @param <String> $module - module name
     */
    function retrieve_entity_info($record, $module) {
    	global $adb, $log, $app_strings;
    	
    	$sql="select
				IFNULL((select vtiger_modcomments.addtime from vtiger_modcomments 
					where vtiger_modcomments.modulename='ServiceComments' 
					and vtiger_modcomments.moduleid=vtiger_servicecomments.servicecommentsid 
			    	and vtiger_modcomments.creatorid=vtiger_servicecomments.serviceid 
			    	ORDER BY vtiger_modcomments.addtime desc LIMIT 1),vtiger_servicecomments.addtime) as lastfollowtime,
				vtiger_servicecomments.servicecommentsid,
				vtiger_servicecomments.salesorderproductsrelid,
				vtiger_servicecomments.related_to,
				vtiger_servicecomments.addtime,
				vtiger_account.leadsource,
				vtiger_salesorderproductsrel.productid,
				vtiger_servicecomments.starttime,
				vtiger_servicecomments.endtime,
				vtiger_salesorderproductsrel.serviceamount,
				vtiger_servicecomments.serviceid,
				vtiger_servicecomments.allocatetime,
				vtiger_servicecomments.assignerid,
				vtiger_account.accountrank,
				vtiger_salesorderproductsrel.ownerid,
    			(select departmentname from vtiger_departments where vtiger_departments.departmentid=(select departmentid from vtiger_user2department where vtiger_user2department.userid=vtiger_salesorderproductsrel.ownerid)) as departmentid,
				vtiger_salesorderproductsrel.schedule,
    			vtiger_servicecomments.remark
				from vtiger_servicecomments
				left JOIN vtiger_account on(vtiger_account.accountid=vtiger_servicecomments.related_to)
				left JOIN vtiger_salesorderproductsrel on(vtiger_salesorderproductsrel.salesorderproductsrelid=vtiger_servicecomments.salesorderproductsrelid) 
				where vtiger_servicecomments.assigntype='productby' and vtiger_servicecomments.servicecommentsid=?";
    	
    	$params[] = str_replace('N','',$record);
    	$result = $adb->pquery($sql, $params);

    	if (!$result || $adb->num_rows($result) < 1) {
    		//查询不到的场合、根据产品关联表查询
    		$sql="SELECT
				'--' as lastfollowtime,
				CONCAT('N',vtiger_salesorderproductsrel.salesorderproductsrelid) AS servicecommentsid,
				vtiger_salesorderproductsrel.salesorderproductsrelid,
				vtiger_account.accountid as related_to,
				 '' AS addtime,
				vtiger_account.leadsource,
				vtiger_salesorderproductsrel.productid,
				vtiger_servicecomments.starttime,
				vtiger_servicecomments.endtime,
				vtiger_salesorderproductsrel.serviceamount,
				'' as serviceid,
				'' as allocatetime,
				'' as assignerid,
				vtiger_account.accountrank,
				vtiger_salesorderproductsrel.ownerid,
    			(select departmentname from vtiger_departments where vtiger_departments.departmentid=(select departmentid from vtiger_user2department where vtiger_user2department.userid=vtiger_salesorderproductsrel.ownerid)) as departmentid,
				vtiger_salesorderproductsrel.schedule
				from vtiger_salesorderproductsrel 
				left JOIN vtiger_account on(vtiger_account.accountid=vtiger_salesorderproductsrel.accountid)
				where not EXISTS(select vtiger_servicecomments.salesorderproductsrelid from vtiger_servicecomments 
				where vtiger_servicecomments.salesorderproductsrelid=vtiger_salesorderproductsrel.salesorderproductsrelid)
    			and vtiger_salesorderproductsrel.salesorderproductsrelid=?";
	    		$result = $adb->pquery($sql, $params);
    	} 
    	$resultrow = $adb->query_result_rowdata($result);

    	if (!empty($resultrow['deleted'])) {
    		throw new Exception($app_strings['LBL_RECORD_DELETE'], 1);
    	}
    	
    	$cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
    	foreach ($cachedModuleFields as $fieldinfo) {
    		
    		$fieldvalue = '';
    		$fieldkey = $this->createColumnAliasForField($fieldinfo);
    		
    		//Note : value is retrieved with a tablename+fieldname as we are using alias while building query
    		if (isset($resultrow[$fieldkey])) {
    			$fieldvalue = $resultrow[$fieldkey];
    		}
    		//分配人设置
    		if ($fieldkey=='assignerid'){
    			//获取登录用户信息
    			$currentUser = Users_Record_Model::getCurrentUserModel();
    			$fieldvalue=$currentUser->get('id');
    		}
    		//客户等级设置
    		if ($fieldkey=='accountrank'){
    			$fieldvalue=vtranslate($fieldvalue);
    		}
    		
    		$this->column_fields[$fieldinfo['fieldname']] = $fieldvalue;
    	}
    	$this->column_fields['record_id'] = $record;
    	$this->column_fields['record_module'] = $module;
    
    }
    /**
     * Function returns the column alias for a field
     * @param <Array> $fieldinfo - field information
     * @return <String> field value
     */
    protected function createColumnAliasForField($fieldinfo) {
    	return strtolower($fieldinfo['fieldname']);
    }
    
    /** Function to insert values in the specifed table for the specified module
     * @param $table_name -- table name:: Type varchar
     * @param $module -- module:: Type varchar
     */
    function insertIntoEntityTable($table_name, $module, $fileid = '') {
    	global $log;
    	global $current_user;
    	$log->info("function insertIntoEntityTable " . $module . ' vtiger_table name ' . $table_name);
    	global $adb;
    	if (stristr($this->id,'N')) {
    		//新增的场合
    		$arr_servicecomments['assigntype']='productby';
    		$arr_servicecomments['related_to']=$this->column_fields['related_to'];
    		$arr_servicecomments['salesorderproductsrelid']=str_replace('N','',$this->id);
    		$arr_servicecomments['serviceid']=$_REQUEST['serviceid'];
    		$arr_servicecomments['assignerid']=$current_user->id;
    		$arr_servicecomments['remark']=$_REQUEST['remark'];
            $arr_servicecomments['starttime']=$_REQUEST['starttime'];
            $arr_servicecomments['endtime']=$_REQUEST['endtime'];
    		
    		ServiceComments_Record_Model::insertServiceComments($arr_servicecomments);
    		
    		//获取id
    		$sql="select servicecommentsid from vtiger_servicecomments where assigntype='productby' and related_to=? and salesorderproductsrelid=?";
    		$result=$adb->pquery($sql, array($this->column_fields['related_to'],str_replace('N','',$this->id)));
    		$this->id=$adb->query_result($result, 0,'servicecommentsid');
    	}else{
    		//更新的场合
    		$updateSql = "update vtiger_servicecomments set serviceid=?,allocatetime=?,assignerid=?,remark=?,starttime=?,endtime=? where servicecommentsid=?";
    		$updateparams[]=$_REQUEST['serviceid'];
    		$updateparams[]=$_REQUEST['allocatetime'];
    		$updateparams[]=$current_user->id;
    		$updateparams[]=$_REQUEST['remark'];
            $updateparams[]=$_REQUEST['starttime'];
            $updateparams[]=$_REQUEST['endtime'];
    		$updateparams[]=$this->id;
    		$adb->pquery($updateSql, $updateparams);
    	}
    }
}
?>
