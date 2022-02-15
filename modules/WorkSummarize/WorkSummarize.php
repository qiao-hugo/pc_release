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

class WorkSummarize extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_worksummarize';
    var $table_index= 'worksummarizeid';
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
    var $tab_name = Array('vtiger_worksummarize');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_worksummarize'   => 'worksummarizeid',);

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
    var $popup_fields = Array('worksummarizeid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'worksummarizeid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'worksummarizeid';

    // Required Information for enabling Import feature
    var $required_fields = Array('worksummarizeid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('worksummarizeid');

    var $default_order_by = 'worksummarizeid';
    var $default_sort_order='DESC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('worksummarizeid');

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
    	global $current_user;
    	$datetime=date('Y-m-d H:i:s');
    	if($_REQUEST['record']>0){
    		$sql="update vtiger_worksummarize set modifiedtime=? where worksummarizeid=?";
    		$this->db->pquery($sql,array($datetime,$_REQUEST['record']));
    	}else{
    		$sql="update vtiger_worksummarize set createdtime=? ,modifiedtime=?,smownerid=? where worksummarizeid=?";
    		$this->db->pquery($sql,array($datetime,$datetime,$current_user->id,$this->id));
    	}
   		
    }
    function retrieve_entity_info($record, $module){
    	parent::retrieve_entity_info($record, $module);
    	global $currentView,$current_user;
    	$nowtime=time()-60*60;
    	
    	$where=getAccessibleUsers('WorkSummarize','',true);
    	if($where!='1=1'&& $currentView=='Edit' && strtotime($this->column_fields['createdtime'])<$nowtime){
    		throw new AppException('超过一小时不可修改！');
    		exit;
    	}
    	if($where!='1=1'&& $currentView=='Edit' && $this->column_fields['assigned_user_id']!=$current_user->id){
    		throw new AppException('你不可修改不是自已的工作总结！');
    		exit;
    	}
    }

}
?>
