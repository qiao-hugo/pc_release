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

class PreInvoiceDeferral extends CRMEntity
{
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_preinvoicedeferral';
    var $table_index = 'deferralid';
    var $tab_name = Array('vtiger_crmentity', 'vtiger_preinvoicedeferral');
    var $tab_name_index = Array('vtiger_crmentity' => 'crmid', 'vtiger_preinvoicedeferral' => 'deferralid');
    var $column_fields = Array();

    /** Indicator if this is a custom module or standard module */
    var $IsCustomModule = true;

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array();


    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'


    );
    var $list_fields_name = Array(/* Format: Field Label => fieldname */

    );

    // Make the field link to detail view from list view (Fieldname)
    var $list_link_field = 'relmodule';

    // For Popup listview and UI type support
    var $search_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'

    );
    var $search_fields_name = Array(/* Format: Field Label => fieldname */

    );

    // For Popup window record selection
    var $popup_fields = Array('deferralid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'deferralid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'deferralid';

    // Required Information for enabling Import feature
    var $required_fields = Array('deferralid' => 1);

    // Callback function list during Importing
    var $special_functions = Array('deferralid');

    var $default_order_by = 'deferralid';
    var $default_sort_order = 'DESC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('deferralid');

    function __construct()
    {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    /**
     * 更新后处理
     * @param unknown $module
     */
    function save_module($module)
    {

    }

    /**
     * 生成工作流
     * @param unknown $modulename
     * @param unknown $workflowsid
     * @param unknown $salesorderid
     * @param string $isedit
     * @throws Exception
     */
    function makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit=''){
        parent::makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit='');
        $query="UPDATE vtiger_salesorderworkflowstages,
				 vtiger_newinvoice,
				 vtiger_preinvoicedeferral
				SET vtiger_salesorderworkflowstages.accountid=vtiger_newinvoice.accountid, vtiger_salesorderworkflowstages.salesorder_nono = vtiger_newinvoice.invoiceno,
				  vtiger_salesorderworkflowstages.modulestatus='p_process',
				 vtiger_salesorderworkflowstages.accountname=(SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid=vtiger_newinvoice.accountid)
				WHERE vtiger_preinvoicedeferral.deferralid=vtiger_salesorderworkflowstages.salesorderid
				and vtiger_newinvoice.invoiceid=vtiger_preinvoicedeferral.invoiceid
				AND vtiger_salesorderworkflowstages.salesorderid=?  AND  vtiger_salesorderworkflowstages.workflowsid=? ";
        $this->db->pquery($query,array($salesorderid,$workflowsid));
        //更改工作流节点指定审核人
        $departmentid=$_SESSION['userdepartmentid'];
        $focus=CRMEntity::getInstance('PreInvoiceDeferral');
        $focus->setAudituid('PreInvoiceDelay',$departmentid,$salesorderid,$workflowsid);
        //修改数据库
        $sql="update vtiger_preinvoicedeferral set workflowsid=?,modulestatus=?,workflowstime=?,workflowsnode=? where deferralid=?";
        $this->db->pquery($sql,array($workflowsid,'b_check',date('Y-m-d H:i:s'),'审核中',$salesorderid));
    }

    /**
     * @审核工作流程后置触发
     * @param Vtiger_Request $request
     */
    function workflowcheckafter(Vtiger_Request $request)
    {
        global $current_user;
        //更新表
        $sql="update vtiger_preinvoicedeferral set reviewerid=?,reviewerdate=?  where deferralid=?";
        $this->db->pquery($sql,array($current_user->id,date('Y-m-d H:i:s'),$request->get('record')));
        //更新整个工作流阶段
        $query="SELECT
		    vtiger_salesorderworkflowstages.workflowsid
		    FROM
		    `vtiger_salesorderworkflowstages`
		    WHERE
		       vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ? ";
        $result=$this->db->pquery($query,array($request->get('stagerecordid')));
        $workflowsid=$this->db->query_result($result,0,'workflowsid');
        $params['workflowsid']=$workflowsid;
        $params['salesorderid']=$request->get('record');
        $this->hasAllAuditorsChecked($params);
        //解锁发票表
        $sql="select invoiceid from vtiger_preinvoicedeferral where deferralid=?";
        $result=$this->db->pquery($sql,array($request->get('record')));
        $invoiceid=$this->db->query_result($result,0,'invoiceid');
        $sql="update vtiger_newinvoice set matchtimeover=?,lockstatus=?  where invoiceid=?";
        $this->db->pquery($sql,array(0,0,$invoiceid));
    }
}
?>
