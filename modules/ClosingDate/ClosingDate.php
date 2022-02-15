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

class ClosingDate extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity
    var $table_name = 'vtiger_closingdate';
    var $table_index= 'id';
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
    var $tab_name = Array('vtiger_closingdate');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_closingdate'   => 'id',);

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array (

         // Format: Field Label => Array(tablename, columnname)
         //  tablename should not have prefix 'vtiger_'


        //'Rel Operate'=> Array('receivedpayments', 'reloperate'),

        );

    
    var $list_fields_name = Array(
       //Format: Field Label => fieldname
        //'Rel Operate'=>'reloperate',

    );

    // Make the field link to detail view from list view (Fieldname)
    var $list_link_field = 'relmodule';

    // For Popup listview and UI type support 弹出框的列表字段
    var $search_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'

    );
    var $search_fields_name = Array(
        /* Format: Field Label => fieldname */

    );

    // For Popup window record selection
    var $popup_fields = Array('relmodule');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'relmodule';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'relmodule';

    // Required Information for enabling Import feature
    var $required_fields = Array('relmodule'=>1);

    // Callback function list during Importing
    var $special_functions = Array('set_import_assigned_user');

    var $default_order_by = 'relmodule';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('relmodule');

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

    /**
     * 重写工作流生成
     * @param unknown $modulename
     * @param unknown $workflowsid
     * @param unknown $salesorderid
     * @param string $isedit
     */
    public function makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit=true){
        parent::makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit);
        $query="UPDATE vtiger_salesorderworkflowstages
				SET vtiger_salesorderworkflowstages.ishigher=1,
				vtiger_salesorderworkflowstages.higherid=16,
				vtiger_salesorderworkflowstages.modulestatus='p_process'
				WHERE  vtiger_salesorderworkflowstages.salesorderid=?";
        $this->db->pquery($query,array($salesorderid,$salesorderid));
        $query="UPDATE vtiger_closingdate
				SET vtiger_closingdate.workflowsid=?,
				    vtiger_closingdate.workflowstime=?,
                    vtiger_closingdate.workflowsnode=?,
				    vtiger_closingdate.modulestatus=?
				WHERE  vtiger_closingdate.id=?";
        $this->db->pquery($query,array($workflowsid,date("Y-m-d H:i:s"),'财务审核','b_actioning',$salesorderid));
        //新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$salesorderid,'salesorderworkflowstagesid'=>0));
    }
    /**
     * 工单打回的后置事件处理机制
     * 微信提醒
     * @param Vtiger_Request $request
     */
    public function backallAfter(Vtiger_Request $request){
        $recordid=$request->get('record');
        $sql=" SELECT * FROM vtiger_closingdate WHERE id=? ";
        $recordinfo=$this->db->pquery($sql,array($recordid));
        $recordinfo=$this->db->query_result_rowdata($recordinfo,0);
        /*echo json_encode($entity);die();*/
        $stagerecordid=$request->get('isrejectid');
        $query="SELECT
                    vtiger_workflowstages.workflowstagesflag,
                    vtiger_workflowstages.workflowsid
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'ClosingDate'";
        $result=$this->db->pquery($query,array($stagerecordid));
        $workflowsid=$this->db->query_result($result,0,'workflowsid');
        $this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='ClosingDate' AND vtiger_salesorderworkflowstages.workflowsid=?",array($recordid,$workflowsid));
        //更新日志记录
        $currentTime = date('Y-m-d H:i:s');
        global $current_user;
        //更新记录
        $id = $this->db->getUniqueId('vtiger_modtracker_basic');
        $this->db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
            array($id, $recordid, 'ClosingDate', $current_user->id,$currentTime, 0));
        $this->db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?),(?,?,?,?),(?,?,?,?)',
            Array($id,'date',"每月".$recordinfo['date']."号","每月".$recordinfo['recorddate']."号",$id,'modulestatus','','a_exception',$id,'remarks','',$recordinfo['remarks']));
    }

    /**
     * 充值申请单这申核后置事件
     * @param $request
     */
    public function workflowcheckafter(Vtiger_Request $request){
        $recordid = $request->get('record');
        $sql=" SELECT * FROM vtiger_closingdate WHERE id=? ";
        $recordinfo=$this->db->pquery($sql,array($recordid));
        $recordinfo=$this->db->query_result_rowdata($recordinfo,0);
        // cxh 2020-01-09 添加 如果该审核需要修改审核列表中的modulestatus（审核流程状态）审核完后走下面代码
        $params['workflowsid']=$recordinfo['workflowsid'];
        $params['salesorderid']=$recordid;
        $this->hasAllAuditorsChecked($params);
        //更新日志记录
        $currentTime = date('Y-m-d H:i:s');
        global $current_user;
        //更新记录
        $id = $this->db->getUniqueId('vtiger_modtracker_basic');
        $this->db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
            array($id, $recordid, 'ClosingDate', $current_user->id,$currentTime, 0));
        $this->db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?),(?,?,?,?),(?,?,?,?)',
            Array($id,'date',"每月".$recordinfo['date']."号","每月".$recordinfo['recorddate']."号",$id,'modulestatus','','c_complete',$id,'remarks','',$recordinfo['remarks']));
        $this->db->pquery('UPDATE `vtiger_closingdate` SET `date`=? WHERE (`id`=?) LIMIT 1',
            array($recordinfo['recorddate'],$recordid));
    }
}
?>
