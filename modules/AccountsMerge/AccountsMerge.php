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

class AccountsMerge extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_accountsmerge';
    var $table_index= 'accountsmergeid';
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
    var $tab_name = Array('vtiger_accountsmerge');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_accountsmerge'   => 'accountsmergeid',);

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
    var $popup_fields = Array('accountsmergeid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'accountsmergeid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'accountsmergeid';

    // Required Information for enabling Import feature
    var $required_fields = Array('accountsmergeid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('accountsmergeid');

    var $default_order_by = 'accountsmergeid';
    var $default_sort_order='DESC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('accountsmergeid');

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
        if(($_REQUEST['related_to']>0 && $_REQUEST['accountid']>0)){
            if($_REQUEST['contacts']=='on'){
                $sql="update vtiger_contactdetails set accountid=? where accountid=?";
                $this->db->pquery($sql,array($_REQUEST['accountid'],$_REQUEST['related_to']));
            }
            // cxh  合并成交业务去掉。
            /*if($_REQUEST['salesorderproductsrel']=='on'){
                $sql="update vtiger_salesorderproductsrel set  accountid=? where accountid=?";
                $this->db->pquery($sql,array($_REQUEST['accountid'],$_REQUEST['related_to']));
            }*/
            // cxh  合并拜放单 start add
            if($_REQUEST['consolidatedcalllist']=='on'){
                $sql =" SELECT * FROM vtiger_visitingorder WHERE related_to=? AND modulestatus='c_complete' ";
                $result = $this->db->pquery($sql,array($_REQUEST['related_to']));
                $rowNumber = $this->db->num_rows($result);
                //如果存在原客户已经完成的拜访单那么继续
                if( $rowNumber>0 ){
                    // 查询目标客户是否为机会客户
                    $sql =" SELECT * FROM vtiger_account WHERE accountid=? AND accountrank='chan_notv' limit 1 ";
                    $result=$this->db->pquery($sql,array($_REQUEST['accountid']));
                    $rowNumber = $this->db->num_rows($result);
                    // 如果查到了是机会客户则改成40%意向客户
                    if($rowNumber>0){
                        $sql=" UPDATE  vtiger_account  SET accountrank='forp_notv' WHERE  accountid = ? ";
                        $this->db->pquery($sql,array($_REQUEST['accountid']));
                    }
                }
                $sql =" UPDATE  vtiger_visitingorder  SET related_to=?  WHERE  related_to = ? ";
                $this->db->pquery($sql,array($_REQUEST['accountid'],$_REQUEST['related_to']));
            }
            //添加一个
            $currentTime = date('Y-m-d H:i:s');
            $id = $this->db->getUniqueId('vtiger_modtracker_basic');
            $this->db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                array($id,$_REQUEST['accountid'], 'Accounts', $current_user->id,$currentTime, 0));
            $this->db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, 'description',$_REQUEST['related_to_display']." 合并",$_REQUEST['accountid_display']));
            // 默认未匹配回款可能客户变更
            $sql=" UPDATE  vtiger_receivedpayments  SET  maybe_account=?  WHERE  maybe_account=? AND deleted = 0 AND  relatetoid=0 ";
            $this->db->pquery($sql,array($_REQUEST['accountid'],$_REQUEST['related_to']));
            // cxh end add
            $sql="UPDATE vtiger_modcomments SET related_to=? WHERE related_to=? ";
            $this->db->pquery($sql,array($_REQUEST['accountid'],$_REQUEST['related_to']));

            $sql="update vtiger_accountsmerge set createdtime=?,smownerid=?  where accountsmergeid=?";
            $this->db->pquery($sql,array($datetime,$current_user->id,$this->id));

        }
    }
    function retrieve_entity_info($record, $module){
    	parent::retrieve_entity_info($record, $module);
    	global $currentView;
    	//$where=getAccessibleUsers('AccountsMerge','',true);

    	if($currentView=='Edit' ){
    		throw new AppException('该记录不能修改！');
    		exit;
    	}

    }

}
?>
