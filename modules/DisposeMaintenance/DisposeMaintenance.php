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

class DisposeMaintenance extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity
    var $table_name = 'vtiger_servicemaintenance';
    var $table_index= 'servicemaintenanceid';
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
    var $tab_name = Array('vtiger_servicemaintenance');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_servicemaintenance'   => 'servicemaintenanceid',);

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array (
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
			'serviceid'=>Array('servicemaintenance', 'serviceid'),
    		'productname'=>Array('servicemaintenance', 'productname'),
    		'ownerid'=>Array('servicemaintenance', 'ownerid'),
    		'related_to'=>Array('servicemaintenance', 'related_to'),
    		'IsOptimize'=>Array('servicemaintenance', 'isoptimize'),
    		'IssueType'=>Array('servicemaintenance', 'issuetype'),
    		'ProcessState'=>Array('servicemaintenance', 'processstate'),
    		'AddTime'=>Array('servicemaintenance', 'addtime'),

    );
    var $list_fields_name = Array(
        /* Format: Field Label => fieldname */
    		'serviceid'=>'serviceid',
    		'productname'=>'productname',
    		'ownerid'=>'ownerid',
    		'related_to'=>'related_to',
    		'IsOptimize'=>'isoptimize',
    		'IssueType'=>'issuetype',
    		'ProcessState'=>'processstate',
    		'AddTime'=>'addtime',
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
    var $popup_fields = Array('servicemaintenanceid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'servicemaintenanceid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'servicemaintenanceid';

    // Required Information for enabling Import feature
    var $required_fields = Array('servicemaintenanceid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('servicemaintenanceid');

    var $default_order_by = 'servicemaintenanceid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('servicemaintenanceid');

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
    	
    	$checkResult = $adb->pquery('SELECT relationoperation FROM vtiger_servicemaintenance WHERE servicemaintenanceid = ?', array($record));
    	$relationOperation = $adb->query_result($checkResult, 0, 'relationoperation');
    	if ($relationOperation==1 || $_REQUEST['relationOperation']){
    		$sql="select
				if(isnull(vtiger_servicemaintenance.servicemaintenanceid),CONCAT('N',vtiger_servicecomments.servicecommentsid),vtiger_servicemaintenance.servicemaintenanceid) as servicemaintenanceid,
				vtiger_servicemaintenance.addtime,
    			vtiger_servicemaintenance.disposeid,
    			vtiger_servicecomments.serviceid,
				vtiger_salesorderproductsrel.productid,
    			vtiger_salesorderproductsrel.ownerid,
    			(select vtiger_products.productman from vtiger_products where vtiger_products.productid=vtiger_salesorderproductsrel.productid) as productman,	
    			(select vtiger_products.productmaintainer from vtiger_products where vtiger_products.productid=vtiger_salesorderproductsrel.productid) as productmaintainer,
    			vtiger_servicecomments.related_to,
				vtiger_servicemaintenance.isoptimize,
				vtiger_servicemaintenance.issuetype,
				vtiger_servicemaintenance.content,
    			vtiger_servicemaintenance.file,
				(if(ISNULL(finishtime),null,
				ROUND(TIMESTAMPDIFF(SECOND,STR_TO_DATE(vtiger_servicemaintenance.addtime,'%Y-%m-%d %H:%i:%s'),STR_TO_DATE(vtiger_servicemaintenance.finishtime,'%Y-%m-%d %H:%i:%s'))/60/60,2)
				)) as timeconsuming,
				if(isnull(vtiger_servicemaintenance.processstate),'untreated',vtiger_servicemaintenance.processstate) as processstate,
    			vtiger_servicemaintenance.salesorderserviceamount,
    			vtiger_servicemaintenance.finishtime,
    			vtiger_servicemaintenance.disposeresult,
				vtiger_servicemaintenance.remark
				from vtiger_servicecomments
    			left JOIN vtiger_servicemaintenance on(vtiger_servicemaintenance.servicecommentsid=vtiger_servicecomments.servicecommentsid)
				left JOIN vtiger_salesorderproductsrel on(vtiger_salesorderproductsrel.salesorderproductsrelid=vtiger_servicecomments.salesorderproductsrelid)
				where vtiger_servicemaintenance.servicemaintenanceid=?";
    	}else{
    		$sql="select
				vtiger_servicemaintenance.servicemaintenanceid,
				vtiger_servicemaintenance.addtime,
    			vtiger_servicemaintenance.disposeid,
    			vtiger_servicemaintenance.serviceid,
				vtiger_servicemaintenance.productid,
    			(select vtiger_crmentity.smownerid from vtiger_crmentity where vtiger_crmentity.crmid=vtiger_servicemaintenance.productid) as ownerid,
    			(select vtiger_products.productman from vtiger_products where vtiger_products.productid=vtiger_servicemaintenance.productid) as productman,
    			(select vtiger_products.productmaintainer from vtiger_products where vtiger_products.productid=vtiger_servicemaintenance.productid) as productmaintainer,
    			vtiger_servicemaintenance.related_to,
				vtiger_servicemaintenance.isoptimize,
				vtiger_servicemaintenance.issuetype,
				vtiger_servicemaintenance.content,
    			vtiger_servicemaintenance.file,
				(if(ISNULL(finishtime),null,
				ROUND(TIMESTAMPDIFF(SECOND,STR_TO_DATE(vtiger_servicemaintenance.addtime,'%Y-%m-%d %H:%i:%s'),STR_TO_DATE(vtiger_servicemaintenance.finishtime,'%Y-%m-%d %H:%i:%s'))/60/60,2)
				)) as timeconsuming,
				if(isnull(vtiger_servicemaintenance.processstate),'untreated',vtiger_servicemaintenance.processstate) as processstate,
    			vtiger_servicemaintenance.salesorderserviceamount,
    			vtiger_servicemaintenance.finishtime,
    			vtiger_servicemaintenance.disposeresult,
				vtiger_servicemaintenance.remark
				from vtiger_servicemaintenance
				where vtiger_servicemaintenance.servicemaintenanceid=?";
    	}
    	
    	$params[] = $record;
    	$result = $adb->pquery($sql, $params);
    	
    	if (!$result || $adb->num_rows($result) < 1) {
    		throw new Exception($app_strings['LBL_RECORD_NOT_FOUND'], -1);
    	} 
    	$resultrow = $adb->query_result_rowdata($result);
    	
    	$cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
    	foreach ($cachedModuleFields as $fieldinfo) {
    		
    		$fieldvalue = '';
    		$fieldkey = $this->createColumnAliasForField($fieldinfo);
    		
    		//Note : value is retrieved with a tablename+fieldname as we are using alias while building query
    		if (isset($resultrow[$fieldkey])) {
    			$fieldvalue = $resultrow[$fieldkey];
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
    	
    	$insertFlg=false;
    	
    	$sql="select * from vtiger_servicemaintenance where servicemaintenanceid=?";
    	$result = $adb->pquery($sql, array($this->id));	
    	if (!$result || $adb->num_rows($result) < 1) {
    		$insertFlg=true;
    	}
    	
    	if ($insertFlg) {
/*     		//新增的场合
    		$servicemaintenanceid=$adb->getUniqueID("vtiger_servicemaintenance");
    		//id赋值
    		//$this->id=str_replace('N','',$this->id);
    		$insertSql = "insert into vtiger_servicemaintenance(
    				servicemaintenanceid,servicecommentsid,addtime,isoptimize,issuetype,content,processstate,salesorderserviceamount,remark)
    				 values(?,?,sysdate(),?,?,?,?,?,?)";
    		$insertparams[]=$servicemaintenanceid;
    		$insertparams[]=$_REQUEST['sourceRecord'];
    		$insertparams[]=$_REQUEST['isoptimize'];
    		$insertparams[]=$_REQUEST['issuetype'];
    		$insertparams[]=$_REQUEST['content'];
    		$insertparams[]=$_REQUEST['processstate'];
    		$insertparams[]=empty($_REQUEST['salesorderserviceamount'])?'0':$_REQUEST['salesorderserviceamount'];
    		$insertparams[]=$_REQUEST['remark'];
    		$adb->pquery($insertSql, $insertparams); */
    	}else{
    		//更新的场合
    		$updateSql = "update vtiger_servicemaintenance set disposeid=?,disposeresult=?,processstate=?,remark=? where servicemaintenanceid=?";
    		$updateparams[]=$current_user->id;
    		$updateparams[]=$_REQUEST['disposeresult'];
    		$updateparams[]=$_REQUEST['processstate'];
    		$updateparams[]=$_REQUEST['remark'];
    		$updateparams[]=$this->id;

    		$adb->pquery($updateSql, $updateparams);
    	}
    	//处理状态时结束的场合、更新结束时间
    	//$updateFlag=false;
    	$finishFlag=$_REQUEST['processstate'] =='processed' || $_REQUEST['processstate'] =='unabletoprocess';
//     	if (!$result){
//     		if ($finishFlag){
//     			$updateFlag=true;
//     		}
//     	}else{
//     		$finishtime=$adb->query_result($result, 0,'finishtime');
//     		if(empty($finishtime) && $finishFlag){
//     			$updateFlag=true;
//     		}
//     	}

    	if($finishFlag){
	    	$updateSql = "update vtiger_servicemaintenance set finishtime=sysdate() where servicemaintenanceid=?";
	    	$adb->pquery($updateSql, array($this->id));
    	}
    }
}
?>
