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

class IdcRecords extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_idcrecords';
    var $table_index= 'idcrecordsid';
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
    var $tab_name = Array('vtiger_idcrecords');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_idcrecords'   => 'idcrecordsid',);

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
    var $popup_fields = Array('idcrecordsid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'idcrecordsid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'idcrecordsid';

    // Required Information for enabling Import feature
    var $required_fields = Array('idcrecordsid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('idcrecordsid');

    var $default_order_by = 'idcrecordsid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('idcrecordsid');

    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    /** Function to insert values in the specifed table for the specified module
     * @param $table_name -- table name:: Type varchar
     * @param $module -- module:: Type varchar
     */

    
    /**
     * 更新后处理
     * @param unknown $module
     */
    function save_module($module) {
   		//获取登录用户信息
	   	global $current_user;
	   	//更新通用字段
	   	$insertion_mode=$this->mode;

        $old_domainname = explode(PHP_EOL,$_REQUEST['domainname']);  //以回车分割成数组
        $domainname['domainname']=implode(' |##| ',$old_domainname);
	   	if ($insertion_mode == 'edit'&& $_REQUEST['record'] > 0) {
            //编辑，类型
            if($_REQUEST['idctype'] == 'foreign'){
                $update_query = "update vtiger_idcrecords set record_no=NULL,recordnature=NULL,recordfrom=NULL,recordtype=NULL,recordportal=NULL,recordtime=NULL,remark=NULL where idcrecordsid=?";
                $this->db->pquery($update_query, array($this->id));
            }
            $update_query = "update vtiger_idcrecords set domainname=?,modifiedtime=sysdate() where idcrecordsid=".$_REQUEST['record']."";
            $this->db->pquery($update_query,$domainname);
	   	}else{
            //新增
            $update_query = "update vtiger_idcrecords set domainname=?,createdtime=sysdate(),modifiedtime=sysdate() where idcrecordsid=".$_REQUEST['record']."";
            $this->db->pquery($update_query,$domainname);
	   	}
    }
   }
?>
