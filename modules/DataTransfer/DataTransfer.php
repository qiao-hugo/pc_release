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

class DataTransfer extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity
    var $table_name = 'vtiger_datatransfer';
    var $table_index= 'datatransferid';
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
    var $tab_name = Array('vtiger_datatransfer');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_datatransfer'   => 'datatransferid');

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
    var $popup_fields = Array('datatransferid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'datatransferid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'datatransferid';

    // Required Information for enabling Import feature
    var $required_fields = Array('datatransferid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('datatransferid');

    var $default_order_by = 'datatransferid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('datatransferid');

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

     /** Function to insert values in the specifed table for the specified module
     * @param $table_name -- table name:: Type varchar
     * @param $module -- module:: Type varchar
     */
    function insertIntoEntityTable($table_name, $module, $fileid = '') {
    	global $log;
    	global $current_user;
    	$log->info("function insertIntoEntityTable " . $module . ' vtiger_table name ' . $table_name);
    	//mysql_query("BEGIN");
    	//获取登录用户信息
    	$currentUser = Users_Record_Model::getCurrentUserModel();
    	$userid = $currentUser->get('id');
    	
    	//更新主表
    	$updateSql=array();
    	$updateLog=array();
    	
    	//转移字段和转移条件取得
    	$entitySql="select * from vtiger_entityname";
    	$result = $this->db->pquery($entitySql, array());
    	
    	$noOfRows = $this->db->num_rows($result);
    	
    	//更新其他表
    	for($i=0; $i<$noOfRows; ++$i) {
    		$row = $this->db->query_result_rowdata($result, $i);
    		$data=$this->getDataTransferUpdateSql($row);
    		if(count($data[0])>0){
    			//更新sql文
    			$updateSql=array_merge($updateSql,$data[0]);
    			//更新log
    			$updateLog=array_merge($updateLog,$data[1]);
    		}
    	}
    	
    	//更新处理
    	$upCount=count($updateSql);
    	//$this->db->query_batch($updateSql,array());
    	for($j=0; $j<$upCount; $j++) {
    		$this->db->pquery($updateSql[$j], array());
    	}

    	//更新数据转移表
    	//id取得
    	$this->id=$this->db->getUniqueID("vtiger_datatransfer");

    	//sql文
    	$insertSql = "insert into vtiger_datatransfer(
    				  datatransferid,subject,transfertime,transferid,sourceid,targetid,transfersql,transferlog,remark,transferedids)
    				  values(?,?,sysdate(),?,?,?,?,?,?,?)";
    	
    	//参数设置
    	$insertparams[]=$this->id;
    	$insertparams[]=$_REQUEST['subject'];
    	$insertparams[]=$userid;
    	$insertparams[]=$_REQUEST['sourceid'];
    	$insertparams[]=$_REQUEST['targetid'];
    	$insertparams[]=implode(";",$updateSql);
    	$insertparams[]=implode("\n",$updateLog);
    	$insertparams[]=$_REQUEST['remark'];
    	$insertparams[]=implode(',',$_REQUEST['accountids']);

        //执行更新
        $result=$this->db->pquery($insertSql, $insertparams);

        //%40客户划转其他商务等级变为机会客户
        if(count($_REQUEST['accountids'])>0){
            $this->db->pquery("update vtiger_account set accountrank='chan_notv' where accountid in(".implode(",",$_REQUEST['accountids']).") and accountrank='forp_notv'");
        }
//     	if(empty($result)){
//     		mysql_query("ROLLBACK");
//     	}else{
//     		mysql_query("COMMIT");
//     	}
//     	mysql_query("END");
    }
    
    /**
     * 获取数据转移更新sql文
     */
    function getDataTransferUpdateSql($row){
    	//表名
        global $current_user,$adb;
    	$tablename=$row['tablename'];
    	//是否关联主表(1:关联,0:不关联)
    	$isrelevancemaster=$row['isrelevancemaster'];
        //是否要更新关联表
        $isrelevanceself=$row['isrelevanceself'];
        //关联的模块
        $modulename=$row['modulename'];
        //更新字段
        $transferfield=$row['transferfield'];
        //更新条件
        $transfercondition=$row['transfercondition'];
        //关联表主键
        $entityidcolumn=$row['entityidfield'];
        $relateaccount=$row['relateaccount'];
        if($relateaccount){
            $relateaccount = ' and '.$tablename.'.'.$relateaccount.' in('.implode(',',$_REQUEST['accountids']).')';
        }

        $updateSql=array();
        $transferLog=array();

        if (!empty($transferfield)){
            if (!empty($transfercondition)){
                if ($isrelevancemaster =='1'){
                    //有关联主表的更新
                    //更新主表
                    $query="SELECT crmid FROM ".$tablename." INNER JOIN vtiger_crmentity ON (vtiger_crmentity.crmid=".$tablename.".".$entityidcolumn." AND ".
                        $transferfield."=".$_REQUEST['sourceid']." AND ".$transfercondition." AND vtiger_crmentity.deleted=0 AND vtiger_crmentity.setype='{$modulename}')".$relateaccount;
                    $list='';
                    foreach($adb->run_query_allrecords($query) as $value)
                    {
                        $list[]=$value[0];
                    }
                    if(empty($list)){
                        return array();
                    }
//触发器冲突导致无法运行
//    				$updateSql[]=" UPDATE vtiger_crmentity SET smownerid=".$_REQUEST['targetid'].",modifiedtime='".date("Y-m-d H:i:s")."',modifiedby={$current_user->id}
//    										 WHERE crmid IN (SELECT T.crmid FROM (SELECT vtiger_crmentity.crmid FROM ".$tablename." INNER JOIN vtiger_crmentity ON (vtiger_crmentity.crmid=".$tablename.".".$entityidcolumn." AND ".
//    				    										$transferfield."=".$_REQUEST['sourceid']." AND ".$transfercondition." AND vtiger_crmentity.deleted=0 AND vtiger_crmentity.setype='{$modulename}')) T)";
                    $updateSql[]=" UPDATE vtiger_crmentity SET smownerid=".$_REQUEST['targetid'].",modifiedtime='".date("Y-m-d H:i:s")."',modifiedby={$current_user->id}
    										 WHERE crmid IN (".implode(',',$list).")";

                    //转移log
    				$transferLog[]="更新表：vtiger_crmentity,更新字段：".$transferfield.",更新数据：".$_REQUEST['sourceid']."->".$_REQUEST['targetid'].",更新条件:".$transferfield."=".$_REQUEST['sourceid']." and ".$transfercondition." and vtiger_crmentity.deleted=0\n";
    				
    				//更新关联表
                    if($isrelevanceself=='1'){
                        $query="select crmid from ".$tablename." INNER JOIN vtiger_crmentity on(vtiger_crmentity.crmid=".$tablename.".".$entityidcolumn." and ".
                            $transferfield."=".$_REQUEST['sourceid']." and ".$transfercondition." and vtiger_crmentity.deleted=0) ".$relateaccount;
                        $list='';
                        foreach($adb->run_query_allrecords($query) as $value)
                            $list[]=$value[0];

                        if(empty($list)){
                            return array();
                        }
                        $updateSql[]=" update ".$tablename.
                            " set ".$transferfield."=".$_REQUEST['targetid'].
                            " where ".$entityidcolumn." in(".implode(',',$list).")".$relateaccount;
                        //转移log
                        $transferLog[]="更新表：".$tablename.",更新字段：".$transferfield.",更新数据：".$_REQUEST['sourceid']."->".$_REQUEST['targetid'].",更新条件:".$transferfield."=".$_REQUEST['sourceid']." and ".$transfercondition.$relateaccount."\n";
                    }

                }else{
                    //无关联主表的更新
                    $updateSql[]=" update ".$tablename." set ".$transferfield."=".$_REQUEST['targetid']." where ".$transferfield."=".$_REQUEST['sourceid']." and ".$transfercondition.$relateaccount;

                    //转移log
                    $transferLog[]="更新表：".$tablename.",更新字段：".$transferfield.",更新数据：".$_REQUEST['sourceid']."->".$_REQUEST['targetid'].",更新条件:".$transferfield."=".$_REQUEST['sourceid']." and ".$transfercondition.$relateaccount."\n";
                }
            }else{
                if ($isrelevancemaster =='1'){
                    //有关联主表的更新
                    $query="select crmid from ".$tablename." INNER JOIN vtiger_crmentity on(vtiger_crmentity.crmid=".$tablename.".".$entityidcolumn." and ".
                        $transferfield."=".$_REQUEST['sourceid']." and vtiger_crmentity.deleted=0)".$relateaccount;
                    $list='';
                    foreach($adb->run_query_allrecords($query) as $value)
                    {
                        $list[]=$value[0];
                    }
                    if(empty($list)){
                        return array();
                    }
                    //更新主表
                    $updateSql[]=" update vtiger_crmentity set smownerid=".$_REQUEST['targetid'].
                        ",modifiedby={$current_user->id}  where crmid in(".implode(',',$list).")";

                    //转移log
                    $transferLog[]="更新表：vtiger_crmentity,更新字段：".$transferfield.",更新数据：".$_REQUEST['sourceid']."->".$_REQUEST['targetid'].",更新条件:".$transferfield."=".$_REQUEST['sourceid']." and vtiger_crmentity.deleted=0\n";
                    $query="select crmid from ".$tablename." INNER JOIN vtiger_crmentity on(vtiger_crmentity.crmid=".$tablename.".".$entityidcolumn." and ".
                        $transferfield."=".$_REQUEST['sourceid']." and vtiger_crmentity.deleted=0)".$relateaccount;
                    $list='';
                    foreach($adb->run_query_allrecords($query) as $value)
                    {
                        $list[]=$value[0];
                    }
                    if(empty($list)){
                        return array();
                    }
                    //更新关联表
                    $updateSql[]=" update ".$tablename.
                        " set ".$transferfield."=".$_REQUEST['targetid'].
                        " where ".$entityidcolumn." in(".implode(',',$list).")".$relateaccount;

                    //转移log
                    $transferLog[]="更新表：".$tablename.",更新字段：".$transferfield.",更新数据：".$_REQUEST['sourceid']."->".$_REQUEST['targetid'].",更新条件:".$transferfield."=".$_REQUEST['sourceid'].$relateaccount."\n";

                }else{

                    $updateSql[]=" update ".$tablename." set ".$transferfield."=".$_REQUEST['targetid']." where ".$transferfield."=".$_REQUEST['sourceid'].$relateaccount;

                    //转移log
                    $transferLog[]="更新表：".$tablename.",更新字段：".$transferfield.",更新数据：".$_REQUEST['sourceid']."->".$_REQUEST['targetid'].",更新条件:".$transferfield."=".$_REQUEST['sourceid'].$relateaccount."\n";
                }
            }
        }
        return array($updateSql,$transferLog);
    }
    function retrieve_entity_info($record, $module){
    	parent::retrieve_entity_info($record, $module);
    	global $currentView;
    	if($this->column_fields['record_id']>0){
    		$where=getAccessibleUsers('DataTransfer','',true);
    		if($where!='1=1'&& $currentView=='Edit'){
    			echo '该记录添加完不可再修改<a href="javascript:history.back()">返回</a>';
                exit;
    		}
    	}
    }
}
?>
