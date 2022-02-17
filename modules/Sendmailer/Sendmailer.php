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

class Sendmailer extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_sendmail';
    var $table_index= 'sendmailid';
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
    var $tab_name = Array('vtiger_sendmail');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_sendmail'   => 'sendmailid',);

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
    var $popup_fields = Array('sendmailid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'sendmailid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'sendmailid';

    // Required Information for enabling Import feature
    var $required_fields = Array('sendmailid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('sendmailid');

    var $default_order_by = 'sendmailid';
    var $default_sort_order='DESC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('sendmailid');

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

        if($_REQUEST['action']!='SaveAjax') {
            global $current_user;
            $datetime = date('Y-m-d H:i:s');
            $id = $_REQUEST['record'] > 0 ? $_REQUEST['record'] : $this->id;
            $post=str_replace("'","",$_POST['body']);//过滤掉这个'
            if ($_REQUEST['record'] > 0) {
                //删除原来的记录
                $sql = "DELETE FROM vtiger_mailaccount WHERE sendmailid=?";
                $this->db->pquery($sql, array($id));
                $sql = "update vtiger_sendmail set body='{$post}' where sendmailid={$id}";
                $this->db->pquery($sql,array());
            } else {
                $sql = "update vtiger_sendmail set createdtime='{$datetime}',assigned_user_email={$current_user->id},body='{$post}' where sendmailid={$this->id}";
                $this->db->pquery($sql,array());
            }
            /* if ($_REQUEST['record'] > 0) {
                //删除原来的记录
                $sql = "DELETE FROM vtiger_mailaccount WHERE sendmailid=?";
                $this->db->pquery($sql, array($id));
            } else {
                //
                $sql = "update vtiger_sendmail set createdtime=?,assigned_user_email=? where sendmailid=?";
                $this->db->pquery($sql, array($datetime, $current_user->id, $this->id));
            } */

            /*//if (!empty($_POST['reviceid'])) {
                //$str = implode(',', $_POST['reviceid']);
                if ($_POST['inorout'] == 'inner') {
                    //$query = "SELECT {$id},id, email1  FROM `vtiger_users` WHERE id IN ({$str})";
                    $query = "SELECT {$id},".$this->getsql();
                } else {
                    //$query = "SELECT {$id},accountid, email1  FROM `vtiger_account` WHERE accountid IN ({$str})";
                    $query = "SELECT {$id},".$this->getsql();
                }

                $query = "INSERT INTO vtiger_mailaccount(sendmailid,accountid,email) {$query}";
            //echo $query;
                //exit;
                $this->db->pquery($query, array());
            //}*/



            $query = "SELECT {$id},".$this->getsql();
            $query = "INSERT INTO vtiger_mailaccount(sendmailid,accountid,email) {$query}";

            $this->db->pquery($query, array());
        }
    }
    function retrieve_entity_info($record, $module){

    	parent::retrieve_entity_info($record, $module);
    	global $currentView,$current_user;
    	$where=getAccessibleUsers('Sendmailer','',true);

    	if($where!='1=1' && $currentView=='Edit'&&$this->column_fields['email_flag']!=''&&$this->column_fields['email_flag']!='nosender'&&$this->column_fields['email_flag']!='notsender'){
    		throw new AppException('邮件不允许编辑！');
    		exit;
    	}
    }
    public function getsql(){
        $departmentid=$_POST['departmentid'];
        if ($_POST['inorout'] == 'inner') {
            $sqlQuery=" vtiger_users.id,vtiger_users.email1 FROM vtiger_users WHERE vtiger_users.`status`='Active'";
            if($departmentid!='H1'){
                $userid=getDepartmentUser($departmentid);
                $sqlQuery.=' AND vtiger_users.id in ('.implode(',',$userid).')';
            }
            return $sqlQuery;
        }else{
            //$sqlQuery=" vtiger_account.accountid,vtiger_account.email1 from vtiger_account INNER JOIN vtiger_crmentity ON (vtiger_crmentity.crmid = vtiger_account.accountid AND vtiger_crmentity.deleted = 0) INNER JOIN vtiger_user2department ON vtiger_crmentity.smownerid=vtiger_user2department.userid INNER JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid WHERE vtiger_account.emailoptout = '0' AND vtiger_departments.parentdepartment like '%{$_POST['departmentid']}%';";
            $sqlQuery=" vtiger_account.accountid,vtiger_account.email1 from vtiger_account INNER JOIN vtiger_crmentity ON (vtiger_crmentity.crmid = vtiger_account.accountid AND vtiger_crmentity.deleted = 0) WHERE vtiger_account.emailoptout = '0' and vtiger_account.accountrank in('bras_isv','eigp_notv','gold_isv','visp_isv')";
            if($departmentid!='H1') {
                $userid = getDepartmentUser($departmentid);
                $sqlQuery .= ' and vtiger_crmentity.smownerid in (' . implode(',', $userid) . ')';
            }
            return $sqlQuery;
        }

    }

}
?>
