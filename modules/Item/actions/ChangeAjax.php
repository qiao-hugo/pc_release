<?php
class Item_ChangeAjax_Action extends Vtiger_Action_Controller {
    public $purchaseWorkFlowSid;  //采购合同审核流
    public $costWorkFlowSid;     //费用合同审核流
    function __construct() {
        $recordModel = SupplierContracts_Record_Model::getCleanInstance("SupplierContracts");
        $this->costWorkFlowSid=$recordModel->costWorkFlowSid;
        $this->purchaseWorkFlowSid=$recordModel->purchaseWorkFlowSid;
        parent::__construct();
        $this->exposeMethod('batchCreateFilterWorkFlow');
        $this->exposeMethod('newItemWorkFlow');
        $this->exposeMethod('updateFilterWorkFlow');
        $this->exposeMethod('deleteRecordWorkFlow');
        $this->exposeMethod('checkCreateFilterWorkFlow');
    }



	function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
	}
	public function batchCreateFilterWorkFlow(Vtiger_Request $request){
        $recordid=$request->get("recordid");
        $workflowstageids=$request->get("workflowstageids");
        $companynames=$request->get("companynames");
        $companycodes=$request->get("companycodes");
        $departmentid=$request->get("departmentid");
        $ceocheck=$request->get("ceocheck");
        $workflowstageids = implode(",",$workflowstageids);
        global $current_user,$adb;
        $creator = $current_user->id;
        $result=$adb->pquery("SELECT departmentid,departmentname FROM vtiger_departments WHERE departmentid IN('".implode("','",$departmentid)."')");
        $departmentidArray=array();
        while($row=$adb->fetch_array($result)){
            $departmentidArray[$row['departmentid']]=$row['departmentname'];
        }
        $workflowRecordModel = Workflows_Record_Model::getCleanInstance("Workflows");
        $workflowRecordModel->createFilterWorkFlowStages(array('sourceid'=>$recordid,
            'modulename'=>'SupplierContracts',
            'workflowsid'=>$this->costWorkFlowSid,
            'workflowstageids'=>$workflowstageids,
            'creator'=>$creator,
            'ceocheck'=>$ceocheck,
            'departmentid'=>implode(',',$departmentid),
            'department'=>implode(',',$departmentidArray)));
        /*foreach ($departmentid as $department){
            $workflowRecordModel->createFilterWorkFlowStages(array('sourceid'=>$recordid,
                'modulename'=>'SupplierContracts',
                'workflowsid'=>$this->costWorkFlowSid,
                'workflowstageids'=>$workflowstageids,
                'creator'=>$creator,
                'ceocheck'=>$ceocheck,
                'departmentid'=>$department,
                'department'=>$departmentidArray[$department]));
            //$workflowRecordModel->createFilterWorkFlowStages($recordid,'SupplierContracts',$this->costWorkFlowSid,$companynames[$key],$companycode,$workflowstageids,$creator,$ceocheck);
        }*/
        $data = array(
            'success'=>true,
            'msg'=>'创建成功'
        );
        echo json_encode($data);
    }

    public function updateFilterWorkFlow(Vtiger_Request $request){
        global $adb;
        $filterworkflowstageid =$request->get('filterworkflowstageid');
        $workflowstageids=$request->get("workflowstageids");
        $workflowstageids = implode(",",$workflowstageids);
        $departmentid=$request->get("departmentid");
        $result=$adb->pquery("SELECT departmentid,departmentname FROM vtiger_departments WHERE departmentid IN('".implode("','",$departmentid)."')");
        $departmentidArray=array();
        while($row=$adb->fetch_array($result)){
            $departmentidArray[$row['departmentid']]=$row['departmentname'];
        }
        $ceocheck=$request->get("ceocheck");
        global $current_user;
        $workflowRecordModel = Workflows_Record_Model::getCleanInstance("Workflows");
        $workflowRecordModel->updateFilterWorkFlow($filterworkflowstageid,$workflowstageids,$current_user->id,$ceocheck,implode(',',$departmentid),
            implode(',',$departmentidArray));
        $data = array(
            'success'=>true,
            'msg'=>'修改'
        );
        echo json_encode($data);
    }

    /**
     * 删除工作流
     * @param Vtiger_Request $request
     */
    public function deleteRecordWorkFlow(Vtiger_Request $request){
        $filterworkflowstageid =$request->get('filterworkflowstageid');
        $workflowRecordModel = Workflows_Record_Model::getCleanInstance("Workflows");
        global $current_user;
        $workflowRecordModel->deleteRecordWorkFlow($filterworkflowstageid,$current_user->id);
        $data = array(
            'success'=>true,
            'msg'=>'删除成功'
        );
        echo json_encode($data);
    }


    public function newItemWorkFlow(Vtiger_Request $request){
        $recordId=$request->get('recordId');

        $db=PearDatabase::getInstance();
        $workflowstagesRecordModel = Workflows_Record_Model::getCleanInstance("Workflows");
        //$invoicecompany = $db->run_query_allrecords("SELECT * FROM vtiger_invoicecompany");
        $invoicecompany = getDepartment();

        $data=array(
            'success'=>true,
            'data'=>'',
            'workflowstages'=>$workflowstagesRecordModel->getWorkFlowStage($this->costWorkFlowSid),
            'invoicecompanys'=>$invoicecompany
        );
        echo json_encode($data);
    }

    /**
     * 验证同ID部门是否存在
     * @param Vtiger_Request $request
     */
    public function checkCreateFilterWorkFlow(Vtiger_Request $request){
        $recordid =$request->get('recordid');
        $departmentid =$request->get('departmentid');
        $filterworkflowstageid =$request->get('filterworkflowstageid');
        $workflowRecordModel = Workflows_Record_Model::getCleanInstance("Workflows");
        $response = new Vtiger_Response();
        $response->setResult($workflowRecordModel->checkCreateFilterWorkFlow($recordid,$departmentid,$filterworkflowstageid));
        $response->emit();
    }
}
