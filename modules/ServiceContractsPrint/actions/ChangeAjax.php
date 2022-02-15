<?php
class ServiceContractsPrint_ChangeAjax_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
		return true;
	}
    function __construct() {

        $this->exposeMethod('changPrintStatus');
        $this->exposeMethod('doStamp');
        $this->exposeMethod('createContractNo');
    }
	public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if(!empty($mode) && $this->isMethodExposed($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
	}

    /**
     * 更改合同为初始已生成状态
     * @param Vtiger_Request $request
     */
	public function changPrintStatus(Vtiger_Request $request){
		$recordId = $request->get('record');
		if(empty($recordId)){
			exit;
		}
		//数据权限与列表一致
		vglobal('currentView','List');
		$recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'ServiceContractsPrint');
		$moduleModel = $recordModel->getModule();
		$entity=$recordModel->entity->column_fields;
		//受保护的客户
		global  $current_user;
		
		$result1 = array('success'=>true);
		if($entity['constractsstatus']=='c_print'){
			$db=PearDatabase::getInstance();
            $datetime=date('Y-m-d H:i:s');
            $modifiedlog='*|*'.$datetime.'#c_print#'.$current_user->id;
            $db->pquery('UPDATE vtiger_servicecontracts_print SET constractsstatus=\'c_generated\',modifiedlog=CONCAT(IFNULL(modifiedlog,\'\'),\''.$modifiedlog.'\'),modifiedtime=?,modifiedby=? WHERE constractsstatus=\'c_print\' AND servicecontractsprintid=?',array($datetime,$current_user->id,$recordId));
		}
		echo json_encode($result1);
	}
    /**
     * 合同的盖章操作
     * @param Vtiger_Request $request
     */
    public function doStamp(Vtiger_Request $request){
        set_time_limit(0);
        $recordIds = $request->get('records');
        if(empty($recordIds)){
            $result1=array();
            $response = new Vtiger_Response();
            $response->setEmitType(Vtiger_Response::$EMIT_JSON);
            $response->setResult($result1);
            $response->emit();
            exit;
        }
        //数据权限与列表一致
        $ids=explode(',',$recordIds);
        $db=PearDatabase::getInstance();
        foreach($ids as $recordId){
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'ServiceContractsPrint');
            //$entity=$recordModel->entity->column_fields;
            $query='SELECT * FROM vtiger_servicecontracts_print WHERE servicecontractsprintid=?';
            $resultData=$db->pquery($query,array($recordId));
            $entity=$resultData->fields;
            global  $current_user;
            if($entity['constractsstatus']=='c_print'){
                $contractclassification = $entity['contractclassification'];
                if($contractclassification=='ServiceContracts'){
                    $dataresult=$db->pquery('SELECT 1 FROM `vtiger_servicecontracts` LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid=vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.contract_no=?',array(trim($entity['servicecontracts_no'])));
                }else{
                    $dataresult=$db->pquery('SELECT 1 FROM `vtiger_suppliercontracts` LEFT JOIN vtiger_crmentity ON vtiger_suppliercontracts.suppliercontractsid=vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0 AND vtiger_suppliercontracts.contract_no=?',array(trim($entity['servicecontracts_no'])));
                }
                $dataresult1=$db->pquery('SELECT 1 FROM vtiger_crmentity WHERE setype=? AND label=? AND  deleted=0 LIMIT 1',array($contractclassification,$entity['servicecontracts_no']));
                if($db->num_rows($dataresult)==0 && $db->num_rows($dataresult1)==0) {
                    $datetime = date('Y-m-d H:i:s');
                    $modifiedlog = '*|*' . $datetime . '#c_stamp#' . $current_user->id;

                    unset($_REQUEST);//防止信息干扰

                    //同步数据到服务合同和采购合同列表
                    $serviceconrecord = $recordModel->pushToContractList($entity,$current_user->id,$recordId);

                    $db->pquery('UPDATE vtiger_crmentity SET label=? WHERE crmid=?', array($entity['servicecontracts_no'],$serviceconrecord));
                    $db->pquery('UPDATE vtiger_servicecontracts_print SET constractsstatus=\'c_stamp\',stamptime=\''.$datetime.'\',modifiedlog=CONCAT(IFNULL(modifiedlog,\'\'),\'' . $modifiedlog . '\'),modifiedtime=?,modifiedby=? WHERE constractsstatus=\'c_print\' AND servicecontractsprintid=?', array($datetime, $current_user->id, $recordId));
                    $result1[]=array('no'=>$entity['servicecontracts_no'],'msg'=>'盖章成功!');
                }else{
                    $result1[]=array('no'=>$entity['servicecontracts_no'],'msg'=>'合同已存无法加盖章!');
                }
            }else{
                $result1[]=array('no'=>$entity['servicecontracts_no'],'msg'=>'操作错误');
            }

        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($result1);
        $response->emit();
    }
    /**
     * 合同编号手动创建(不走编码规则)
     * @param Vtiger_Request $request
     */
    public function createContractNo(Vtiger_Request $request){
        $contractno=$request->get("contractno");
        $contractno=trim($contractno);
        $result=array('flag'=>false);
        do{
            if(empty($contractno)){
                $result['msg']='添加失败,合同编号不存在';
                break;
            }
            $moduleModel=Vtiger_Module_Model::getInstance("ServiceContracts");
            if(!$moduleModel->exportGrouprt("ServiceContracts","addcontractno")){
                $result['msg']='无权操作';
                break;
            }
            $db=PearDatabase::getInstance();
            $query="SELECT 1 FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.contract_no=?";
            $resultData=$db->pquery($query,array($contractno));
            if($db->num_rows($resultData)){
                $result['msg']='合同编号已存在';
                break;
            }
            $query="SELECT 1 FROM vtiger_servicecontracts_print WHERE servicecontracts_no=?";
            $resultData=$db->pquery($query,array($contractno));
            if($db->num_rows($resultData)){
                $result['msg']='合同编号已存在';
                break;
            }
            $datetime=date('Y-m-d H:i:s');
            $sql="INSERT INTO `vtiger_servicecontracts_print` 
                (`servicecontracts_no`, `constractsstatus`,`createdtime`,`smownerid`) VALUES 
                (?, 'c_print',?,6934)";
            $db->pquery($sql,array($contractno,$datetime));
            $result['msg']='添加成功';
            $result['flag']=true;
        }while(0);
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($result);
        $response->emit();
    }
}
