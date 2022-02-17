<?php
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');

class ContractDelaySign extends CRMEntity
{
    var $log;
    var $db;
    var $table_name = "vtiger_contractdelaysign";
    var $table_index = 'contractdelaysignid';
    var $tab_name_index = Array('vtiger_crmentity' => 'crmid', 'vtiger_contractdelaysign' => 'contractdelaysignid');//'vtiger_crmentity' => 'crmid',
    var $tab_name = Array('vtiger_crmentity', 'vtiger_contractdelaysign');
    var $column_fields = Array();
    var $sortby_fields = Array();
    var $list_fields = Array();
    var $list_fields_name = Array();
    var $list_link_field = 'contractdelaysignid';
    var $search_fields = Array();
    var $search_fields_name = Array();
    var $required_fields = array();
    var $mandatory_fields = Array();
    var $emailTemplate_defaultFields = array();
    var $default_order_by = 'contractdelaysignid';
    var $default_sort_order = 'ASC';
    // For Alphabetical search
    var $def_basicsearch_col = 'contract_no';
    var $related_module_table_index = array();
    //关联模块的一些字段和数组;
    var $relatedmodule_list = array();
    var $relatedmodule_fields = array();

    function __construct()
    {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    /* * 节点审核时到了指定节点抓取时间
    * 后置事件
   * @param Vtiger_Request $request
   */
    public function save_module()
    {
    }

    /**
     * 重写工作流生成
     * @param unknown $modulename
     * @param unknown $workflowsid
     * @param unknown $salesorderid
     * @param string $isedit
     */
    public function makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit=''){
        parent::makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit='');
        $query="UPDATE vtiger_salesorderworkflowstages,
				 vtiger_contractdelaysign
				SET vtiger_salesorderworkflowstages.accountid=vtiger_contractdelaysign.accountid,
				  vtiger_salesorderworkflowstages.modulestatus='p_process',
				 vtiger_salesorderworkflowstages.accountname=(SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid=vtiger_contractdelaysign.accountid)
				WHERE vtiger_contractdelaysign.contractdelaysignid=vtiger_salesorderworkflowstages.salesorderid
				AND vtiger_salesorderworkflowstages.salesorderid=?  AND  vtiger_salesorderworkflowstages.workflowsid=? ";
        $this->db->pquery($query,array($salesorderid,$workflowsid));
        /*//新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$salesorderid,'salesorderworkflowstagesid'=>0));*/
    }

    function workflowcheckafter(Vtiger_Request $request)
    {
        // cxh 2019-08-02 添加 如果该审核需要修改审核列表中的modulestatus（审核流程状态）审核完后走下面代码
        $stagerecordid = $request->get('stagerecordid');
        $record = $request->get('record');
        $query = "SELECT
		    vtiger_salesorderworkflowstages.workflowsid
		    FROM
		    `vtiger_salesorderworkflowstages`
		    WHERE
		       vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ? ";
        $result = $this->db->pquery($query, array($stagerecordid));
        $workflowsid = $this->db->query_result($result, 0, 'workflowsid');
        $result = $this->db->pquery("select * from vtiger_contractdelaysign where contractdelaysignid=?", array($record));
        if ($this->db->num_rows($result)) {
            $row = $this->db->fetchByAssoc($result, 0);
            $lastSignDate = date("Y-m-d", strtotime($row['activedate']) + 60 * 24 * 60 * 60);
            $delaydays = 0;
            $isdelay = 0;
            if (time() > strtotime($lastSignDate)) {
                $delaydays = round((time() - strtotime($lastSignDate)) / (24 * 60 * 60));
                $isdelay = 1;
            }

            $this->db->pquery("update vtiger_contractdelaysign set modulestatus='c_apply_complete',applyconfirmdate=?,lastsigndate=?,delaydays=?,isdelay=? where contractdelaysignid=?", array(date("Y-m-d H:i:s"), $lastSignDate, $delaydays, $isdelay, $record));
        }


        $params['workflowsid'] = $workflowsid;
        $params['salesorderid'] = $request->get('record');
        $this->hasAllAuditorsChecked($params);
    }


    /**
     * 工作流打回后置处理事件
     * @param Vtiger_Request $request
     */
    public function backallAfter(Vtiger_Request $request)
    {
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
                AND vtiger_salesorderworkflowstages.modulename = 'ContractDelaySign'";
        $result = $this->db->pquery($query, array($stagerecordid));
        //$currentflag = $this->db->query_result($result, 0, 'workflowstagesflag');
        $workflowsid = $this->db->query_result($result, 0, 'workflowsid');
        $this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='ContractDelaySign' AND vtiger_salesorderworkflowstages.workflowsid=?", array($record, $workflowsid));
        $this->db->pquery("update vtiger_contractdelaysign set modulestatus='c_apply_stop' where contractdelaysignid=?", array($record));
    }
}

?>
