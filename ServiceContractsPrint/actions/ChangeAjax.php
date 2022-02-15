<?php
class ServiceContractsPrint_ChangeAjax_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
		return true;
	}
    function __construct() {

        $this->exposeMethod('changPrintStatus');
        $this->exposeMethod('doStamp');
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
            $entity=$recordModel->entity->column_fields;
            global  $current_user;
            if($entity['constractsstatus']=='c_print'){
                $dataresult=$db->pquery('SELECT 1 FROM `vtiger_servicecontracts` LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid=vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.contract_no=?',array($entity['servicecontracts_no']));

                if($db->num_rows($dataresult)==0) {
                    $datetime = date('Y-m-d H:i:s');
                    $modifiedlog = '*|*' . $datetime . '#c_stamp#' . $current_user->id;
                    $db->pquery('UPDATE vtiger_servicecontracts_print SET constractsstatus=\'c_stamp\',stamptime=\''.$datetime.'\',modifiedlog=CONCAT(IFNULL(modifiedlog,\'\'),\'' . $modifiedlog . '\'),modifiedtime=?,modifiedby=? WHERE constractsstatus=\'c_print\' AND servicecontractsprintid=?', array($datetime, $current_user->id, $recordId));
                    $query = "SELECT vtiger_parent_contracttype_contracttyprel.parent_contracttypeid,vtiger_contract_type.contract_type FROM vtiger_parent_contracttype_contracttyprel LEFT JOIN vtiger_servicecontracts_print ON (FIND_IN_SET(vtiger_servicecontracts_print.contract_template,vtiger_parent_contracttype_contracttyprel.contract_template) and vtiger_servicecontracts_print.contract_template !='') LEFT JOIN vtiger_contract_type ON vtiger_contract_type.contract_typeid=vtiger_parent_contracttype_contracttyprel.contract_typeid WHERE vtiger_servicecontracts_print.servicecontractsprintid=? limit 1";
                    $result = $db->pquery($query, array($recordId));//取合同的类型

                    unset($_REQUEST);//防止信息干扰
                    $_REQUES['record'] = '';
                    $request = new Vtiger_Request($_REQUES, $_REQUES);
                    $request->set('contract_no', $entity['servicecontracts_no']);
                    $request->set('assigned_user_id', $current_user->id);
                    $request->set('modulestatus', 'c_stamp');
                    $request->set('isautoclose', 1);

                    if ($db->num_rows($result)) {
                        $contracttypearr = $db->raw_query_result_rowdata($result);
                        $_REQUEST['parent_contracttypeid'] = $contracttypearr['parent_contracttypeid'];
                        $request->set('contract_type', $contracttypearr['contract_type']);
                        $request->set('parent_contracttypeid', $contracttypearr['parent_contracttypeid']);
                    }
                    $request->set('module', 'ServiceContracts');
                    $request->set('view', 'Edit');
                    $request->set('action', 'Save');
                    $ressorder = new ServiceContracts_Save_Action();
                    $ressorderecord = $ressorder->saveRecord($request);
                    $serviceconrecord=$ressorderecord->getId();
                    $db->pquery('UPDATE vtiger_servicecontracts SET modulestatus=\'c_stamp\',contract_no=?,servicecontractsprintid=?,servicecontractsprint=? WHERE servicecontractsid=?', array($entity['servicecontracts_no'],$recordId, $recordId . '-8',$serviceconrecord ));
                    $db->pquery('UPDATE vtiger_crmentity SET label=? WHERE crmid=?', array($entity['servicecontracts_no'],$serviceconrecord));
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
}
