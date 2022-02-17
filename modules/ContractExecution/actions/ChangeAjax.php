<?php

class ContractExecution_ChangeAjax_Action extends Vtiger_Action_Controller {
    function __construct(){
        parent::__construct();
        $this->exposeMethod('newExecutionNode');
        $this->exposeMethod('searchContractNo');
        $this->exposeMethod('canAdd');
    }

	function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process(Vtiger_Request $request) {
       $mode=$request->getMode();
        if(!empty($mode)){
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
	}

	public function canAdd(Vtiger_Request $request){
        $response = new Vtiger_Response();
        $record = $request->get('record');
        $recordModel = ContractExecution_Record_Model::getInstanceById($record,'ContractExecution');
        $contractNo = $recordModel->getContractNoById($record);
        $serviceContractRecordModel = ServiceContracts_Record_Model::getCleanInstance("ServiceContracts");
        $serviceContractInfo  = $serviceContractRecordModel->getAbleExecutionContractByNo($contractNo);
        if(empty($serviceContractInfo)){
            $response->setError(-1, '暂不可添加新的执行节点');
            $response->emit();
            exit;
        }
        $serviceContractInfo['contract_no'] = $contractNo;
        $response = new Vtiger_Response();
        $response->setResult($serviceContractInfo);
        $response->emit();
    }

	public function searchContractNo(Vtiger_Request $request){
        $response = new Vtiger_Response();
        $contractNo = trim($request->get('contractno'));
        if(!$contractNo){
            $response->setError(-1, '请输入合同编号');
            $response->emit();
            exit;
        }

        $serviceContractRecordModel = ServiceContracts_Record_Model::getCleanInstance("ServiceContracts");
        $serviceContractInfo  = $serviceContractRecordModel->getAbleExecutionContractByNo($contractNo);
        if(empty($serviceContractInfo)){
            $response->setError(-1, '请输入正确的合同编号');
            $response->emit();
            exit;
        }

        $response = new Vtiger_Response();
        $response->setResult($serviceContractInfo);
        $response->emit();
    }

    public function newExecutionNode(Vtiger_Request $request){
        $response = new Vtiger_Response();
        $contractNo = trim($request->get('contractno'));
        $contractId = $request->get('contractid');
        $receiveAbleAmount = $request->get("receiveableamount");
        $collectionDescription =$request->get('collectiondescription');
        $accountid = $request->get('accountid');
        $stage = $request->get('stage');
        $receiveAbleAmount  = $receiveAbleAmount?$receiveAbleAmount:0;
        if(!$contractNo || !$receiveAbleAmount || !$collectionDescription){
            $response->setError(-1, '必填项未填写');
            $response->emit();
            exit();
        }

        if(!$accountid){
            $response->setError(-1, '未搜索到对应合同的客户');
            $response->emit();
            exit();
        }

        $isPass = $request->get('ispass');
        $recordModel = ContractExecution_Record_Model::getCleanInstance("ContractExecution");
        $contractexecutionid = $recordModel->getExecutionId($contractId);

        if($contractexecutionid){
            //校验当前合同最近一个节点是否已执行通过
            $isExecuted = $recordModel->isLastExecuted($contractId);
            if(!$isExecuted){
                if($isPass){
                    $response->setError(-1, '合同最近一次节点未通过，此节点不可默认执行通过');
                }else{
                    $response->setError(-1, '合同最近一次节点未通过，不可添加新的执行节点');
                }
                $response->emit();
                exit();
            }
        }

        $params = array(
            'stage'=>$stage,
            'stageshow'=>'第'.$stage.'阶段',
            'receiveableamount'=>$receiveAbleAmount,
            'collectiondescription'=>$collectionDescription,
            'executestatus'=>'a_no_execute',
            'stagetype'=>'手动添加',
        );
        global $current_user;
        //是否已存在合同执行的数据
        if(!$contractexecutionid){
            //原不存在在合同执行列表的合同，创建合同执行节点后，合同信息同步生成（合同基本信息+合同收款结算+工作流节点）
            $data = array(
                'userid'=>$current_user->id,
                'contractid'=>$contractId,
                'accountid'=>$accountid,
                'contractno'=>$contractNo
            );
            $contractexecutionid = $recordModel->newContractExecution($data);
        }

        $params['contractexecutionid'] = $contractexecutionid;
        $params['accountid'] = $accountid;
        $params['contractid'] = $contractId;
        $params['contractreceivable'] = $receiveAbleAmount;
        $executiondetailid = $recordModel->executionDetail($contractexecutionid,$contractId,$params,$isPass);
        if($isPass){
            //执行通过
            $data = array(
                'executor'=>$current_user->id,
                'executiondetailid'=>$executiondetailid,
                'contractexecutionid'=>$contractexecutionid,
                'voucher'=>$request->get('filename').'##'.$request->get('fileid'),
                'fileid'=>$request->get('fileid'),
            );
            $recordModel->autoExecute($contractexecutionid,$data);
        }

        $response->setResult(array('contractexecutionid'=>$contractexecutionid));
        $response->emit();
    }

}
