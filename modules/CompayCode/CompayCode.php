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

class CompayCode extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_company_code';
    var $table_index= 'companyid';
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
    var $tab_name = Array('vtiger_company_code');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_company_code'   => 'companyid',);

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array (
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
       // 'Rel Name'=> Array('salerankid', 'rank'),
        

    );
    var $list_fields_name = Array(
        /* Format: Field Label => fieldname */

       
    );

    // Make the field link to detail view from list view (Fieldname)
    var $list_link_field = 'companyid';

    // For Popup listview and UI type support
    var $search_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        //'Rel Name'=> Array('rankid', 'rank'),
       
    );
    var $search_fields_name = Array(
        /* Format: Field Label => fieldname */
        'Rel Name'=> 'companyid',
       
    );

    // For Popup window record selection
    var $popup_fields = Array('companyid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'companyid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'companyid';

    // Required Information for enabling Import feature
    var $required_fields = Array('rank'=>1);

    // Callback function list during Importing
    var $special_functions = Array('set_import_assigned_user');

    var $default_order_by = 'companyid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('companyid');

    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

   function save_module($module) {
       //更改发票主体表内容
       $record=$this->id;
       $invoicecompany=trim($_REQUEST['companyfullname']);
       $company_codeno=trim($_REQUEST['company_codeno']);
       // 不管是新增还是编辑 只要生成新的公司名称那么就在分成表 和 公司主体表里 插入新的公司名称
       $sql=" SELECT * FROM vtiger_invoicecompany  as a  WHERE  invoicecompany=? ";
       $result = $this->db->pquery($sql,array($invoicecompany));
       // 如果编辑时表中有了 就不用操作了 说明不是新的公司名称
       if($this->db->num_rows($result)>0){
           /*$sql = " UPDATE `vtiger_invoicecompany` SET `invoicecompany`=? ,`companycode`=?  WHERE companyid=?  LIMIT 1 ";
           $this->db->pquery($sql,array(trim($_REQUEST['companyfullname']),trim($_REQUEST['company_codeno']),$record));
           $sql = " UPDATE `vtiger_owncompany` SET `owncompany`=? WHERE companyid=?  LIMIT 1 ";
           $this->db->pquery($sql,array(trim($_REQUEST['companyfullname']),$record));*/
           //  如果该名称的不存在说明 说明编辑了该公司名称那么 生成该公司新的合同主体即新公司名称
       }else{
           $sql = " INSERT INTO `vtiger_invoicecompany` ( `companyid`, `invoicecompany`,`companycode`) VALUES (?,?,?) ";
           $this->db->pquery($sql,array($record,$invoicecompany,$company_codeno));
           $sql = " INSERT INTO `vtiger_owncompany` ( `companyid`, `owncompany`) VALUES (?,?) ";
           $this->db->pquery($sql,array($record,$invoicecompany));
       }
    }


}
?>
