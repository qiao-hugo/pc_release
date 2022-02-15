<?php
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');

class SupplierStatement extends CRMEntity {
	var $log;
	var $db;
	var $table_name = "vtiger_supplierstatement";
	var $table_index= 'supplierstatementid';
    var $tab_name_index = Array('vtiger_crmentity' => 'crmid','vtiger_supplierstatement'=>'supplierstatementid');//'vtiger_crmentity' => 'crmid',
	var $tab_name = Array('vtiger_crmentity','vtiger_supplierstatement');
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
	var $default_order_by = 'supplierstatementid';
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
        if(!$_REQUEST['record']) {
            $result = $this->db->pquery("select contract_no,statementno from vtiger_suppliercontracts where suppliercontractsid=?", array($_REQUEST['suppliercontractsid']));
            $row = $this->db->fetchByAssoc($result, 0);
            $statementNo = $row['contract_no'] . '-JSD' . date("Ym") . '-' . (1 + $row['statementno']);
            $sql = 'UPDATE vtiger_supplierstatement SET statementno=? WHERE supplierstatementid=?';
            $this->db->pquery($sql, array($statementNo, $this->id));

            $this->db->pquery("update vtiger_suppliercontracts set statementno=? where suppliercontractsid=?", array(($row['statementno'] + 1), $_REQUEST['suppliercontractsid']));
        }

        //2015-2-12 新增产品负责人
        $result = $this->db->pquery("SELECT vtiger_crmentity.smcreatorid, vtiger_products.productname,vtiger_products.productid,vtiger_products.productman FROM `vtiger_vendorsrebate` LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_vendorsrebate.productid LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_vendorsrebate.productid WHERE suppliercontractsid =? ",array($_REQUEST['suppliercontractsid']));
        while($product=$this->db->fetch_row($result)){
            $checkarray[]=array('workflowstagesname'=> $product['productname'].'审核','smcreatorid'=>0,'productid'=>$product['productid'],'productman'=>$product['productman']);
        }
        vglobal('checkproducts',$checkarray);
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
//        $recordModel =Vtiger_Record_Model::getInstanceById($salesorderid, 'SupplierStatement', TRUE);
//        $companyCode=$this->getContractsCompanyCode($recordModel->get('modulename'),$recordModel->get('suppliercontractsid'));
        $result = $this->db->pquery("select workflowstagesname from vtiger_salesorderworkflowstages where salesorderid=? and isaction=1",array($salesorderid));
        $data = $this->db->fetchByAssoc($result,0);

        $query = "UPDATE vtiger_salesorderworkflowstages,
				 vtiger_supplierstatement
				SET vtiger_salesorderworkflowstages.accountid=vtiger_supplierstatement.vendorid,
				     vtiger_salesorderworkflowstages.modulestatus='p_process',
				 vtiger_salesorderworkflowstages.accountname=(SELECT vtiger_vendor.vendorname FROM vtiger_vendor WHERE vtiger_vendor.vendorid=vtiger_supplierstatement.vendorid),
				    vtiger_supplierstatement.modulestatus='b_check',
				    vtiger_supplierstatement.workflowsnode=?
				WHERE vtiger_supplierstatement.supplierstatementid=vtiger_salesorderworkflowstages.salesorderid
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
                AND vtiger_salesorderworkflowstages.modulename = 'SupplierStatement'";
        $result = $this->db->pquery($query, array($stagerecordid));
        $currentflag = $this->db->query_result($result, 0, 'workflowstagesflag');
        $workflowsid = $this->db->query_result($result, 0, 'workflowsid');
        $recordModel = Vtiger_Record_Model::getInstanceById($record, 'SupplierStatement', TRUE);
        $entity = $recordModel->entity->column_fields;
        $currentflag = trim($currentflag);

        $this->db->pquery("UPDATE vtiger_supplierstatement SET workflowsnode=(SELECT vtiger_salesorderworkflowstages.workflowstagesname FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.isaction=1 AND vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='SupplierStatement' LIMIT 1) WHERE supplierstatementid=?", array($record, $record));
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
                AND vtiger_salesorderworkflowstages.modulename = 'SupplierStatement'";
        $result = $this->db->pquery($query, array($stagerecordid));

        $currentflag = $this->db->query_result($result, 0, 'workflowstagesflag');
        $workflowsid = $this->db->query_result($result, 0, 'workflowsid');
        $recordModel = Vtiger_Record_Model::getInstanceById($record, 'SupplierStatement');

        $this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='SupplierStatement' AND vtiger_salesorderworkflowstages.workflowsid=?", array($record, $workflowsid));
        $this->db->pquery("UPDATE vtiger_supplierstatement SET workflowsnode='打回中',modulestatus='a_exception' WHERE supplierstatementid=?", array($record));
    }

}
?>
