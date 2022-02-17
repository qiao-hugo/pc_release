<?php
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');

class CustomerStatement extends CRMEntity {
	var $log;
	var $db;
	var $table_name = "vtiger_customerstatement";
	var $table_index= 'customerstatementid';
    var $tab_name_index = Array('vtiger_crmentity' => 'crmid','vtiger_customerstatement'=>'customerstatementid');//'vtiger_crmentity' => 'crmid',
	var $tab_name = Array('vtiger_crmentity','vtiger_customerstatement');
	var $column_fields = Array();
	var $sortby_fields = Array();
	var $list_fields = Array();
	var $list_fields_name = Array();
	var $list_link_field= 'statementno';
	var $search_fields = Array();
	var $search_fields_name = Array();
	var $required_fields =  array();
	var $mandatory_fields = Array();
	var $emailTemplate_defaultFields = array();
	var $default_order_by = 'customerstatementid';
	var $default_sort_order = 'ASC';
	// For Alphabetical search
	var $def_basicsearch_col = 'statementno';
	var $related_module_table_index = array();
	//关联模块的一些字段和数组;
    var $relatedmodule_list = array('Files');
    var $relatedmodule_fields = array(
        'Files'=>array(
            'name'=>'name',
            'uploader'=>'uploader',
            'uploadtime'=>'uploadtime',
            'style'=>'style',
            'filestate'=>'filestate',
            'deliversuserid'=>'deliversuserid',
            'delivertime'=>'delivertime',
            'remarks'=>'remarks'
        ),
    );
    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    public function save_module($module){
        if(!$_REQUEST['record']){
            $result =$this->db->pquery("select contract_no,statementno,signaturetype from vtiger_servicecontracts where servicecontractsid=?",array($_REQUEST['contractid']));
            $row = $this->db->fetchByAssoc($result,0);
            $statementNo = $row['contract_no'].'-JSD'.date("Ym").'-'.($row['statementno']+1);
            $sql='UPDATE vtiger_customerstatement SET statementno=?,signaturetype=? WHERE vtiger_customerstatement.customerstatementid=?';
            $this->db->pquery($sql,array($statementNo,$row['signaturetype'],$this->id));

            $this->db->pquery("update vtiger_servicecontracts set statementno=? where servicecontractsid=?",array(($row['statementno']+1),$_REQUEST['contractid']));
        }

    }

    /**
     * 重写工作流生成
     * @param unknown $modulename
     * @param unknown $workflowsid
     * @param unknown $salesorderid
     * @param string $isedit
     */
    public function makeWorkflows($modulename, $workflowsid, $salesorderid, $isedit = '') {
        parent::makeWorkflows($modulename, $workflowsid, $salesorderid, $isedit = '');
        $recordModel =Vtiger_Record_Model::getInstanceById($salesorderid, 'CustomerStatement', TRUE);
//        $companyCode=$this->getContractsCompanyCode('CustomerStatement',$recordModel->get('contractid'));
//        $log1 =& LoggerManager::getLogger('VT');
//        $log1->info("定位companyCode ->".print_r($companyCode,true));
        $result = $this->db->pquery("select workflowstagesname from vtiger_salesorderworkflowstages where salesorderid=? and isaction=1",array($salesorderid));
        $data = $this->db->fetchByAssoc($result,0);
        $query = "UPDATE vtiger_salesorderworkflowstages,
				 vtiger_customerstatement
				SET vtiger_salesorderworkflowstages.accountid=vtiger_customerstatement.sc_related_to,
				     vtiger_salesorderworkflowstages.modulestatus='p_process',
				 vtiger_salesorderworkflowstages.accountname=(SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid=vtiger_customerstatement.sc_related_to),
				    vtiger_customerstatement.modulestatus='b_check',
				    vtiger_customerstatement.workflowsnode=?
				WHERE vtiger_customerstatement.customerstatementid=vtiger_salesorderworkflowstages.salesorderid
				AND vtiger_salesorderworkflowstages.salesorderid=? AND  vtiger_salesorderworkflowstages.workflowsid=? ";
        $this->db->pquery($query, array($data['workflowstagesname'],$salesorderid,$workflowsid));
        //新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$salesorderid,'salesorderworkflowstagesid'=>0));
    }

       /** 节点审核时到了指定节点抓取时间
     * 后置事件
     * @param Vtiger_Request $request
     */

    function workflowcheckafter(Vtiger_Request $request){
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
                AND vtiger_salesorderworkflowstages.modulename = 'CustomerStatement'";
        $result = $this->db->pquery($query, array($stagerecordid));
        $currentflag = $this->db->query_result($result, 0, 'workflowstagesflag');
        $workflowsid = $this->db->query_result($result, 0, 'workflowsid');
        $recordModel = Vtiger_Record_Model::getInstanceById($record, 'CustomerStatement', TRUE);
        $entity = $recordModel->entity->column_fields;
        $currentflag = trim($currentflag);

        $this->db->pquery("UPDATE vtiger_customerstatement SET workflowsnode=(SELECT vtiger_salesorderworkflowstages.workflowstagesname FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.isaction=1 AND vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='CustomerStatement' LIMIT 1) WHERE customerstatementid=?", array($record, $record));
        // cxh 2019-08-02 添加 如果该审核需要修改审核列表中的modulestatus（审核流程状态）审核完后走下面代码
        $params['salesorderid'] = $request->get('record');
        $params['workflowsid'] = $workflowsid;
        $this->hasAllAuditorsChecked($params);

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
                AND vtiger_salesorderworkflowstages.modulename = 'CustomerStatement'";
        $result = $this->db->pquery($query, array($stagerecordid));

        $currentflag = $this->db->query_result($result, 0, 'workflowstagesflag');
        $workflowsid = $this->db->query_result($result, 0, 'workflowsid');
        $recordModel = Vtiger_Record_Model::getInstanceById($record, 'CustomerStatement');

        $this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='CustomerStatement' AND vtiger_salesorderworkflowstages.workflowsid=?", array($record, $workflowsid));
        $this->db->pquery("UPDATE vtiger_customerstatement SET workflowsnode='打回中',modulestatus='a_exception' WHERE customerstatementid=?", array($record));
    }

}
?>
