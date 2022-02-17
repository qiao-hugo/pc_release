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
class Receipt extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_receipt';
    var $table_index= 'receiptid';
    var $tab_name_index = Array('vtiger_crmentity' => 'crmid','vtiger_receipt'=>'receiptid');
    var $tab_name = Array('vtiger_crmentity','vtiger_receipt');
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
    var $popup_fields = Array('receiptid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'receiptid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'receiptid';

    // Required Information for enabling Import feature
    var $required_fields = Array('receiptid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('receiptid');

    var $relatedmodule_list=array();
    var $relatedmodule_fields=array(

    );

    var $related_module_table_index = array(

    );


    var $default_order_by = 'receiptid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('receiptid');

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
				 vtiger_receipt
				SET vtiger_salesorderworkflowstages.modulestatus='p_process',
				vtiger_salesorderworkflowstages.salesorder_nono=vtiger_receipt.receiptno
				WHERE vtiger_receipt.receiptid=vtiger_salesorderworkflowstages.salesorderid
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
                AND vtiger_salesorderworkflowstages.modulename = 'Receipt'";
        $result = $this->db->pquery($query, array($stagerecordid));
        $workflowsid = $this->db->query_result($result, 0, 'workflowsid');
        $this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='Receipt' AND vtiger_salesorderworkflowstages.workflowsid=?", array($record, $workflowsid));
        $this->db->pquery("UPDATE vtiger_receipt SET workflowsnode='未提交' WHERE receiptid=?", array($record));
    }

    /**
     * 更新后处理
     * @param unknown $module
     */
    function save_module($module) {
        $recordid = $_REQUEST['record'];
        if(empty($_REQUEST['record'])||$_REQUEST['record']<1){
            $recordid=$this->id;
        }

    }

    /**
     * @审核工作流程触发
     * @发票领取结点!!!关联发票领取人
     * @指定结点有
     * @param Vtiger_Request $request
     */
    function workflowcheckbefore(Vtiger_Request $request){
        $stagerecordid=$request->get('stagerecordid');
        $record=$request->get('record');
        $db=PearDatabase::getInstance();

        $query="SELECT
                    vtiger_workflowstages.workflowstagesflag
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'Receipt'";
        $result=$db->pquery($query,array($stagerecordid));
        $currentflag=$db->query_result($result, 0, 'workflowstagesflag');

        //发票管理员开立
        if($currentflag=='open_receipt'){
            $query="SELECT receiptno FROM vtiger_receipt WHERE receiptid=?";
            $sel_result=$db->pquery($query,array($record));
            $receiptno=$db->query_result($sel_result, 0, 'receiptno');
            if(empty($receiptno)){
                $resultaa['success'] = 'false';
                $resultaa['error']['message'] = ":请先“编辑”收据编号再进行审核!";
                //若果是移动端请求则走这个返回
                if( $request->get('isMobileCheck')==1){
                    return $resultaa;
                }else{
                    echo json_encode($resultaa);
                    exit;
                }
            }
            $sql="UPDATE vtiger_receipt SET opentime=? WHERE receiptid=?";
            $db->pquery($sql,array(date('Y-m-d H:i:s'),$record));
        }
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
                AND vtiger_salesorderworkflowstages.modulename = 'Receipt'";
        $result = $this->db->pquery($query, array($stagerecordid));
        $workflowsid = $this->db->query_result($result, 0, 'workflowsid');
        $currentflag = $this->db->query_result($result, 0, 'workflowstagesflag');
        // cxh 2019-08-02 添加 如果该审核需要修改审核列表中的modulestatus（审核流程状态）审核完后走下面代码
        $params['salesorderid']=$request->get('record');
        $params['workflowsid']=$workflowsid;
        $this->hasAllAuditorsChecked($params);

        //商务领取
//        if($currentflag=='receive_receipt'){
//            $sql="UPDATE vtiger_receipt SET workflowsnode=? WHERE receiptid=?";
//            $this->db->pquery($sql,array('已领取',$record));
//        }
    }

}
?>
