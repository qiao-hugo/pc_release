<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ContractsAgreement_BasicAjax_Action extends Vtiger_Action_Controller {
    public $getSuppleAgreement='common/get_supple_agreement';
    public $save_and_replace='erp/save_and_replace';
    private $backEdit='erp/back_edit';//数据保存并替换接口
    public $erp_edit='erp/edit';
	function __construct() {
		parent::__construct();
		$this->exposeMethod('getAccount');
        $this->exposeMethod('getElecContractTable');//得到合同对应回款
        $this->exposeMethod('getElecTPLList');//获取电子合同对应的模板
        $this->exposeMethod('saveAndReplace');//电子合同保存替换
        $this->exposeMethod('elecErpEdit');//电子合同保存替换
        $this->exposeMethod('checkAccountid');//验证与主合同是否是同一合同
        $this->exposeMethod('checkOrderIsCancel');//客户已签署且已确认到款的电子合同，无法直接提交合同作废流程

	}

	function checkPermission(Vtiger_Request $request) {
		return;
	}

	function getAccount(Vtiger_Request $request){

	    $record = $request->get('record');
        $dataResult=array('flag'=>false);
        do {
            global $adb;
            $query="SELECT vtiger_account.accountid,vtiger_account.accountname,vtiger_servicecontracts.invoicecompany,vtiger_crmentity.deleted FROM vtiger_servicecontracts 
                    LEFT JOIN vtiger_account ON (vtiger_servicecontracts.sc_related_to=vtiger_account.accountid AND vtiger_servicecontracts.sc_related_to>0)
                    LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid
                    WHERE vtiger_servicecontracts.servicecontractsid=?";
            $result=$adb->pquery($query,array($record));
            if(!$adb->num_rows($result)){
                break;
            }


            $row = $adb->query_result_rowdata($result, 0);
            $accountid=($row['accountid']>0&& $row['deleted']==0 && $row['deleted']!='')?$row['accountid']:0;
            $accountname=($row['accountid']>0&& $row['deleted']==0 && $row['deleted']!='')?$row['accountname']:'';
            $invoicecompany=empty($row['invoicecompany'])?'':$row['invoicecompany'];
            $dataResult=array('flag'=>true,'accountid'=>$accountid,'accountname'=>$accountname,'invoicecompany'=>$invoicecompany);

        }while(0);
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($dataResult);
        $response->emit();
	}

	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
	}
    /**
     * 获取模板列表
     * @param $request
     */
    public function getElecTPLList($request){
        global $fangxinqian_url;
        $recordModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
        $viewURL=$fangxinqian_url.$this->getSuppleAgreement;
        echo $recordModel->https_requestcomm($viewURL,null,$recordModel->getCURLHeader(false));
    }
    public function saveAndReplace($request){
        global $fangxinqian_url,$current_user;
        $record=$request->get('updateRecord');
        $servicecontractsid=$request->get('servicecontractsid');
        $recordModel=Vtiger_Record_Model::getInstanceById($servicecontractsid,'ServiceContracts');
        $invoicecompany=$request->get('invoicecompany');
        $invoicecompanyInfo=$recordModel->getInvoicecompanyInfo($invoicecompany);
        $totalprice=$request->get('total');
        $totalprice=$totalprice>0?$totalprice:' ';
        $chinatotalprice=$totalprice>0?$recordModel->toChinaMoney($totalprice):' ';
        $accountRecordModel=Vtiger_Record_Model::getCleanInstance('Accounts');
        $accountInfo=$accountRecordModel->getAccountInfo($request);
        $effectivetime=$recordModel->get('effectivetime');
        $clientpropertyArr=array('enterprise'=>0,'personal'=>1,'government'=>0,'otherorg'=>0);
        $clientproperty=$recordModel->get('clientproperty');
        $clientproperty=$clientpropertyArr[$clientproperty]>0?$clientpropertyArr[$clientproperty]:0;
        $sendArr=array("needAudit"=>$request->get('needAudit'),
            "sender"=>array(
                "name"=>$request->get('senderName'),
                "phone"=>$request->get('senderPhone')
            ),
            "receiver"=>array(
                "name"=>$request->get('receiverName'),
                "phone"=>$request->get('receiverPhone'),
                "type"=>$clientproperty  //0.企业 1.个人
            ),
            "companyCode"=>$invoicecompanyInfo['company_codeno'], //商务所属分公司编号
            "templateId"=>$request->get('templateId'), //合同模板id
            "expirationTime"=>!empty($effectivetime)?$effectivetime:date('Y-m-d',strtotime('+1 year')),//合同过期时间
            "replaces"=>array(
                "address"=>$invoicecompanyInfo['address'],
                "company"=>$invoicecompanyInfo['companyfullname'],
                "bank"=>$invoicecompanyInfo["bank_account"],
                "banknumber"=>$invoicecompanyInfo["numbered_accounts"],
                "phone"=>$invoicecompanyInfo["telphone"],
                "taxnumber"=>$invoicecompanyInfo["taxnumber"],//纳税人别号
                "fax"=>"",//传真
                "name"=>$current_user->last_name,//商务的名字
                "email"=>$current_user->email1,//商务的EMAIL
                "totalprice"=>$totalprice,//总额
                "chinatotalprice"=>$chinatotalprice,//中文大写
                "firstcompany"=>$accountInfo['accountname'],//客户名称
                "firstaddress"=>implode('',explode('#',$accountInfo['address'])),//客户通信地址
                "firstname"=>$request->get('receiverName'),//客户联系人
                "firstemail"=>$accountInfo['email1']?$accountInfo['email1']:' ',//客户EMAIL
                "firstphone"=>$request->get('receiverPhone'),//客户电话
                "firstfax"=>$accountInfo['fax']?$accountInfo['fax']:' ',//客户传真
                "firstbank"=>$accountInfo['bank_account'],
                "firstbanknumber"=>$accountInfo['numbered_accounts'],
                'signdate'=>date("Y年m月d日"),//我方的签定时间
                'firstsigndate'=>date("Y年m月d日"),//客户的签订时间
            )
        );
        if($record>0){
            $ContractsAgreementRecordModel=Vtiger_Record_Model::getInstanceById($record,'ContractsAgreement');
            //if($ContractsAgreementRecordModel->get('eleccontractstatus')=='b_elec_actioning'){
                $sendArr['contractId']=$ContractsAgreementRecordModel->get('eleccontractid');//放心签合同id
                $viewURL=$fangxinqian_url.$this->backEdit;
                $returnJsonData= $recordModel->https_requestcomm($viewURL,json_encode($sendArr),$recordModel->getCURLHeader(),true);
                $returnData=json_encode($returnJsonData,true);
                if($returnData['success']){
                    echo json_encode(array("success"=> true,
                        "errorCode"=>null,
                        "msg"=>"success",
                        "data"=>array(
                            "contractId"=>$ContractsAgreementRecordModel->get('eleccontractid'),
                            "contractUrl"=> null
                        )));
                }else{
                    echo $returnJsonData;
                }
            /*}else{
                $sendArr["templateId"]=$request->get('templateId'); //合同模板id
                $viewURL=$fangxinqian_url.$this->save_and_replace;
                echo $recordModel->https_requestcomm($viewURL,json_encode($sendArr),$recordModel->getCURLHeader(),true);
            }*/
        }else{
            $sendArr["templateId"]=$request->get('templateId'); //合同模板id
            $viewURL=$fangxinqian_url.$this->save_and_replace;
            echo $recordModel->https_requestcomm($viewURL,json_encode($sendArr),$recordModel->getCURLHeader(),true);
        }
    }

    /**
     * @param $request
     * @return mixed
     */
    public function elecErpEdit($request){
        global $fangxinqian_url;
        $contractId=$request->get('contractId');
        $udata=$request->get('udata');
        if(empty($udata)){
            echo json_encode(array("success"=>true,"errorCode"=>null,"msg"=>"success","data"=>null));
            exit;
        }
        $arrayData=array('contractId'=>$contractId,'itd'=>$udata);
        $recordModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
        $url=$fangxinqian_url.$this->erp_edit;
        return $recordModel->https_requestcomm($url,json_encode($arrayData),$recordModel->getCURLHeader());
    }

    /**
     * 验证与主合同客户是否是同一个客户
     * @param $request
     */
    public function checkAccountid($request){
        $recordModel=Vtiger_Record_Model::getCleanInstance('ContractsAgreement');
        $response=new Vtiger_Response();
        $response->setResult($recordModel->checkAccountid($request));
        $response->emit();
    }

    public function checkOrderIsCancel($request){
        $recordModel=Vtiger_Record_Model::getCleanInstance('ContractsAgreement');
        $response=new Vtiger_Response();
        $response->setResult($recordModel->checkOrderIsCancel($request));
        $response->emit();
    }
}
