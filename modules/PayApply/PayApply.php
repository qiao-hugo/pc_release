<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header$
 * Description:  Defines the Account SugarBean Account entity with the necessary
 * methods and variables.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('data/CRMEntity.php');
require_once('data/Tracker.php');
// Faq is used to store vtiger_faq information.
class PayApply extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_payapply';
    var $table_index= 'payapplyid';
    var $tab_name_index = Array('vtiger_crmentity' => 'crmid','vtiger_payapply'=>'payapplyid');//'vtiger_crmentity' => 'crmid',
    var $tab_name = Array('vtiger_crmentity','vtiger_payapply');
    var $column_fields = Array();

    /** Indicator if this is a custom module or standard module */
    var $IsCustomModule = true;

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array();

    //var $tab_name = Array('vtiger_crmentity','vtiger_account','vtiger_accountbillads','vtiger_accountscf','vtiger_accountshipads');
    //var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_account'=>'accountid','vtiger_accountbillads'=>'accountaddressid','vtiger_accountscf'=>'accountid','vtiger_accountshipads'=>'accountaddressid');
//    var $tab_name = Array('vtiger_payapply');
//    var $tab_name_index = Array('vtiger_payapply'=>'payapplyid');

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
        //'schoolname'=> Array('School', 'schoolname')
    );
    var $search_fields_name = Array(
        /* Format: Field Label => fieldname */
        //'schoolname'=> 'schoolname'
    );

    /*var $search_fields = Array(
        'schoolname'=> Array('School', 'schoolname')
    );
    var $search_fields_name = Array(
    /* Format: Field Label => fieldname
        'schoolname'=> 'schoolname'
    );*/


    // For Popup window record selection
    var $popup_fields = Array('payapplyid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'payapplyid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'payapplyid';

    // Required Information for enabling Import feature
    var $required_fields = Array('payapplyid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('payapplyid');

    var $relatedmodule_list=array();
    var $relatedmodule_fields=array(
        //'Schoolcontacts'=>
                    //array('schoolcontactsname'=>'联系人','position'=>'职位','gendertype'=>'性别','phone'=>'手机','email'=>'email'),
    );

    var $related_module_table_index = array(
        //'ServiceContracts' => array('table_name' => 'vtiger_servicecontracts', 'table_index' => 'servicecontractsid', 'rel_index' => 'sc_related_to'),

        //'Schoolrecruit'=>array('table_name' => 'vtiger_schoolrecruit', 'table_index' => 'schoolrecruitid', 'rel_index' => 'schoolid')

    );


    var $default_order_by = 'payapplyid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('payapplyid');

    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    /**
     * 重写工作流生成
     * @param unknown $modulename
     * @param unknown $workflowsid
     * @param unknown $salesorderid
     * @param string $isedit
     */
    public function makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit=''){
        parent::makeWorkflows($modulename, $workflowsid, $salesorderid, $isedit = '');
        $query = "UPDATE vtiger_salesorderworkflowstages,
				 vtiger_payapply
				SET vtiger_salesorderworkflowstages.modulestatus='p_process',
				vtiger_salesorderworkflowstages.salesorder_nono=vtiger_payapply.payapply_no
				WHERE vtiger_payapply.payapplyid=vtiger_salesorderworkflowstages.salesorderid
				AND vtiger_salesorderworkflowstages.salesorderid=?  AND  vtiger_salesorderworkflowstages.workflowsid=? ";
        $this->db->pquery($query, array($salesorderid,$workflowsid));
        //新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$salesorderid,'salesorderworkflowstagesid'=>0));
    }

    /**
     * 审核打回中处理
     * @param Vtiger_Request $request
     */
    public function backallAfter(Vtiger_Request $request) {
        $stagerecordid = $request->get('isrejectid');
        $record = $request->get('record');
        $query = "SELECT
                    vtiger_workflowstages.workflowstagesflag,
                    vtiger_workflowstages.workflowsid
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'PayApply'";
        $result = $this->db->pquery($query, array($stagerecordid));
        $workflowsid = $this->db->query_result($result, 0, 'workflowsid');
        $this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='PayApply' AND vtiger_salesorderworkflowstages.workflowsid=?", array($record, $workflowsid));
//        $this->db->pquery("UPDATE vtiger_payapply SET modulestatus='a_normal' WHERE payapplyid=?", array($record));
    }

    /**
     * 更新后处理
     * @param unknown $module
     */
    function save_module($module) {

        if(empty($_REQUEST['record'])){
            // 更改最新的 流程节点
            $sql = "select companycode from vtiger_invoicecompany where invoicecompany=?";
            $sel_result = $this->db->pquery($sql, array($_REQUEST['invoicecompany']));
            $res_cnt = $this->db->num_rows($sel_result);
            if ($res_cnt > 0) {
                $row = $this->db->query_result_rowdata($sel_result, 0);
                $payapplyNo = 'ZC-'.$row['companycode'].'-'.date('YmdHis').mt_rand(10,99);
                $this->db->pquery('UPDATE vtiger_payapply SET payapply_no=? WHERE payapplyid=?',array($payapplyNo,$this->id));
            }
        }

        if(!empty($_REQUEST['soncate'])){
            $this->db->pquery('UPDATE vtiger_payapply SET soncate=? WHERE payapplyid=?',array($_REQUEST['soncate'],$this->id));
        }
        $this->db->pquery('UPDATE vtiger_payapply SET startdate=?,enddate=? WHERE payapplyid=?',array($_REQUEST['startdate'],$_REQUEST['enddate'],$this->id));
        $this->db->pquery('UPDATE vtiger_payapply SET modulestatus=? WHERE payapplyid=?',array('b_check',$this->id));

    }

        /**节点审核时到了指定节点抓取时间
     * 后置事件
     * @param Vtiger_Request $request
     */
    function workflowcheckafter(Vtiger_Request $request)
    {
        $stagerecordid = $request->get('stagerecordid');
        $record = $request->get('record');

        $query = "SELECT
                    vtiger_workflowstages.workflowstagesflag,
                    vtiger_salesorderworkflowstages.workflowsid
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'PayApply'";
        $result = $this->db->pquery($query, array($stagerecordid));
        $workflowsid = $this->db->query_result($result, 0, 'workflowsid');
        // cxh 2019-08-02 添加 如果该审核需要修改审核列表中的modulestatus（审核流程状态）审核完后走下面代码
        $params['salesorderid']=$request->get('record');
        $params['workflowsid']=$workflowsid;
        $this->hasAllAuditorsChecked($params);

    }
}
?>
