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

class IronAccount extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_ironaccount';
    var $table_index= 'ironid';
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
    var $tab_name = Array('vtiger_ironaccount');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_ironaccount'   => 'ironid',);

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
    var $popup_fields = Array('ironid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'ironid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'ironid';

    // Required Information for enabling Import feature
    var $required_fields = Array('ironid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('ironid');

    var $default_order_by = 'ironid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('ironid');

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
        if ($insertion_mode == 'edit'&& $_REQUEST['record'] > 0) {
            //编辑


        }else{
            //新增
            $update_query = "update vtiger_ironaccount set operater=?,addtime=sysdate() where ironid=?";
            $this->db->pquery($update_query, array($current_user->id,$this->id));

            //更新客户表客户等级 转为铁牌客户
            $accountid = $_REQUEST['accountid'];
            $sql = 'UPDATE vtiger_account SET accountrank = ? WHERE accountid = ?';
            $this->db->pquery($sql, array('iron_isv',$accountid));
        }
    }
   }
?>
