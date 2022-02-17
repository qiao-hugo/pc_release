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

class VendorContacts extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_vendorcontacts';
    var $table_index= 'contactid';
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
    var $tab_name = Array('vtiger_crmentity', 'vtiger_vendorcontacts');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
    	'vtiger_crmentity' => 'crmid',
        'vtiger_vendorcontacts'   => 'contactid',);

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array (
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
    	//'Subject'=> Array('visitingorder', 'subject'),

    );
    var $list_fields_name = Array(
        /* Format: Field Label => fieldname */
    	//'Subject'=> 'subject',
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
    var $popup_fields = Array('contactid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'contactid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'contactid';

    // Required Information for enabling Import feature
    var $required_fields = Array('contactid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('contactid');

    var $default_order_by = 'contactid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('contactid');
    /*var $relatedmodule_list=array('VisitImprovement');
    //'Potentials','Quotes'
    var $relatedmodule_fields=array(
        'VisitImprovement'=>array('userid'=>'userid','datetime'=>'datetime','remark'=>'remark'),
    );*/
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
   		//获取登录用户信息
	   	$currentUser = Users_Record_Model::getCurrentUserModel();
	   	$userid = $currentUser->get('id');
	   	//更新添加人，添加时间，状态
        if(empty($_REQUEST['record'])){
            $update_query = "update vtiger_visitingorder set examinestatus='unaudited',followstatus='notfollow' where visitingorderid=?";
            $update_params = array($this->id);
            $this->db->pquery($update_query, $update_params);
            $result=$this->db->pquery("SELECT 1 FROM vtiger_crmentity WHERE setype='Accounts' AND crmid=?",array($_REQUEST['related_to']));
            if($this->db->num_rows($result)) {
                $visitaccountcontractid = $this->db->getUniqueId('vtiger_visitaccountcontract');
                $this->db->pquery('INSERT INTO `vtiger_visitaccountcontract`(visitaccountcontractid,accountid,visitingorderid,vextractid,vaccompany,vstartdate) SELECT ?,vtiger_visitingorder.related_to,vtiger_visitingorder.visitingorderid,vtiger_visitingorder.extractid,vtiger_visitingorder.accompany,vtiger_visitingorder.startdate FROM vtiger_visitingorder WHERE vtiger_visitingorder.related_to>0 AND visitingorderid=?', array($visitaccountcontractid, $this->id));
            }
        }else{
            //拜访单打回后重新编辑,修改其状态
            $this->db->pquery("update vtiger_visitingorder set modulestatus=if(modulestatus='a_exception','a_normal',modulestatus) where visitingorderid=?",array($this->id));
        }
        $this->db->pquery("UPDATE vtiger_crmentity,vtiger_visitingorder SET vtiger_visitingorder.accountnamer=vtiger_crmentity.label,vtiger_visitingorder.modulename=vtiger_crmentity.setype WHERE vtiger_visitingorder.related_to=vtiger_crmentity.crmid AND vtiger_visitingorder.visitingorderid=?",array($this->id));
    }

}
?>
