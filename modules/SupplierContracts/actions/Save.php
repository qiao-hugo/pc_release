<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class SupplierContracts_Save_Action extends Vtiger_Action_Controller {

	public function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$record = $request->get('record');

		if(!Users_Privileges_Model::isPermitted($moduleName, 'Save', $record)) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}
	}

	public function process(Vtiger_Request $request1) {
        //临时添加方法
        $iscomplete=$request1->get('iscomplete');
        if($iscomplete=='on') {
            $request = new Vtiger_Request(array());
            $requestAllData = $request1->getAll();
            foreach ($requestAllData as $key => $value) {
                if (!in_array($key, array('parentcate','soncate','payapplyids','frameworkcontract'))) {
                    $request->set($key, $value);
                } else {
                    if (!empty($value)) {
                        $request->set($key, $value);
                    }else{
                        unset($_REQUEST[$key]);
                        unset($_POST[$key]);
                    }
                }
            }
        }else{
            $request=$request1;
        }
		$recordModel = $this->saveRecord($request);

		if($request->get('relationOperation')) {
			$loadUrl = $this->getParentRelationsListViewUrl($request);
		} else if ($request->get('returnToList')) {
			$loadUrl = $recordModel->getModule()->getListViewUrl();
		} else {
			$loadUrl = $recordModel->getDetailViewUrl();
		}
		if(empty($loadUrl)){
			if($request->getHistoryUrl()){
				$loadUrl=$request->getHistoryUrl();
			}else{
				$loadUrl="index.php";
			}
		}
        if($request->isAjax()){

        }else{
            header("Location: $loadUrl");
        }
	}

	/**
	 * Function to save record
	 * @param <Vtiger_Request> $request - values of the record
	 * @return <RecordModel> - record Model of saved record
	 */
	public function saveRecord($request) {
		$recordModel = $this->getRecordModelFromRequest($request);
        $amountpaid=$recordModel->get('amountpaid');
        $total=$request->get('total');
        $total=str_replace(',','',$total);
        $residualamount=bcsub($total,$amountpaid,2);
        $residualamount=$residualamount>0?$residualamount:0;
        $recordModel->set('residualamount',$residualamount);
        if($request->get('effectivetime')){
            $actualeffectivetime=$recordModel->get('actualeffectivetime');
            if(empty($actualeffectivetime) || $request->get('effectivetime')!=$recordModel->get('effectivetime')){
                $recordModel->set('actualeffectivetime',$request->get('effectivetime'));
            }

        }
        if($request->get("soncateid")){
            $filterWorkFlow = $recordModel->getFilterWorkFlow($request->get("soncateid"),$total);
//        $payapplyids = $request->get('payapplyids');
//        $recordModel->set("payapplyids",$payapplyids);
//        if(is_array($payapplyids)){
//            $recordModel->set("payapplyids",implode(',',$payapplyids));
//        }
            $_REQUEST['type']=$filterWorkFlow['type'];
            $request->set("type",$filterWorkFlow['type']);
        }

        $recordModel->save();
		//$this->setContractNo($request, $recordModel);
//        if($filterWorkFlow['type']=='cost'){
//        }

		if($request->get('relationOperation')) {
			$parentModuleName = $request->get('sourceModule');
			$parentModuleModel = Vtiger_Module_Model::getInstance($parentModuleName);
			$parentRecordId = $request->get('sourceRecord');
			$relatedModule = $recordModel->getModule();
			$relatedRecordId = $recordModel->getId();

			$relationModel = Vtiger_Relation_Model::getInstance($parentModuleModel, $relatedModule);
			$relationModel->addRelation($parentRecordId, $relatedRecordId);
		}
		return $recordModel;
	}

	public function createContractNo($request,$recordModel){
        /*
    编码原则：
    业务类采购合同编码规则：GY(业务类供应商合同的简称）+各公司拼音简称(如无锡珍岛为WX)+ZD+签订年月+序列号（4位数）
    行政类采购合同编码规则：GX(行政类供应商合同的简称）+各公司拼音简称(如无锡珍岛为WX)+ZD+签订年月+序列号（4位数）
    框架合作协议：GC(框架合作协议的简称）+各公司拼音简称(如无锡珍岛为WX)+ZD+签订年月+序列号（4位数）
    保存时自动生成采购合同编号；修改采购公司或者合同类型不会修改采购合同编号。
    */
        $suppliercontractsstatus = $request->get('suppliercontractsstatus');
        $invoicecompany = $request->get('invoicecompany');
        $record=$recordModel->getId();

        $db=PearDatabase::getInstance();
        $entity=$recordModel->entity->column_fields;
        //生成合同编号
        $year=date('Y');
        $monthn=date('m');
        $day=date('d');
        //求合同主体的编码
        $query="SELECT company_codeno FROM `vtiger_company_code` WHERE companyfullname=? limit 1";
        $result=$db->pquery($query,array($invoicecompany));
        $company_codeno=$db->query_result($result,0,'company_codeno');
        $company_codeno=!empty($company_codeno)?$company_codeno:'ZD';
        $splitcontNO=explode('-',$entity['contract_no']);
        if(empty($entity['contract_no']) || $splitcontNO[0]!=$suppliercontractsstatus || $splitcontNO[1]!=$company_codeno) {
            $query = "SELECT suppliercontractsstatus,invoicecompany,meter FROM vtiger_suppliercontractsnodefect WHERE suppliercontractsstatus=? AND invoicecompany=? LIMIT 1";
            $result = $db->pquery($query, array($suppliercontractsstatus, $company_codeno));
            if ($db->num_rows($result)) {

                $meter = $db->query_result($result, 0, "meter");
                $db->pquery("DELETE FROM vtiger_suppliercontractsnodefect WHERE suppliercontractsstatus=? AND invoicecompany=? AND meter=?", array($suppliercontractsstatus, $company_codeno, $meter));
            } else {
                $query = "SELECT suppliercontractsstatus,invoicecompany,meter FROM vtiger_suppliercontractsnometer WHERE suppliercontractsstatus=? AND invoicecompany=? LIMIT 1";
                $result = $db->pquery($query, array($suppliercontractsstatus, $company_codeno));
                if ($db->num_rows($result)) {
                    $meter = $db->query_result($result, 0, "meter");
                    $meter = 1 + $meter;
                    $meter = str_pad($meter, 4, '0', STR_PAD_LEFT);
                } else {
                    $meter = '0001';
                }
                $db->pquery('REPLACE INTO vtiger_suppliercontractsnometer(suppliercontractsstatus,invoicecompany,meter) VALUES(?,?,?)', array($suppliercontractsstatus, $company_codeno, $meter));
            }
            $contract_no = $suppliercontractsstatus . '-' . $company_codeno . '-' . $year . $monthn . $day . $meter;
            if (!empty($entity['contract_no'])) {
                $db->pquery("INSERT INTO vtiger_suppliercontractsnodefect(suppliercontractsstatus,invoicecompany,meter) SELECT '{$splitcontNO[0]}','{$splitcontNO[1]}',meter FROM vtiger_suppliercontracts WHERE suppliercontractsid=?", array($record));
            }
            //取供应商的类型是行政采购GX还是业务采购GY
            //按供应商类型+合同主体+年份+月份+日期+序号生成合同编号
            $sql = "UPDATE vtiger_suppliercontracts SET contract_no=?,meter=? WHERE suppliercontractsid=?";
            $db->pquery($sql, array($contract_no,$meter, $record));
            $sql = "UPDATE vtiger_crmentity SET label=? WHERE crmid=?";
            $db->pquery($sql, array($contract_no, $record));
            $db->pquery(" UPDATE vtiger_salesorderworkflowstages SET vtiger_salesorderworkflowstages.salesorder_nono=? WHERE  vtiger_salesorderworkflowstages.salesorderid=? ",array($contract_no,$record));
        }
    }

	// 获取合同编号。
	public function setContractNo($request, $recordModel){
		/*
		编码原则：
业务类采购合同编码规则：GY(业务类供应商合同的简称）+各公司拼音简称(如无锡珍岛为WX)+ZD+签订年月+序列号（4位数）
行政类采购合同编码规则：GX(行政类供应商合同的简称）+各公司拼音简称(如无锡珍岛为WX)+ZD+签订年月+序列号（4位数）
保存时自动生成采购合同编号；修改采购公司或者合同类型不会修改采购合同编号。
		*/
		$suppliercontractsstatus = $request->get('suppliercontractsstatus');
		$shuupliercompany = $request->get('shuupliercompany');
		$signdate = $request->get('signdate');
		$signdate = str_replace('-', '', $signdate);

		$recordId = $recordModel->getId();
		$id = $recordId;
		if (strlen($recordId) > 4) {
			$recordId = substr($recordId, -4);
		} else if(strlen($recordId) < 4) {
			$recordId = str_pad($recordId, 4, "0", STR_PAD_LEFT);
		}

		$code = $suppliercontractsstatus . $shuupliercompany.'ZD'.$signdate . $recordId;
		SupplierContracts_Record_Model::setContractsNO($id, $code);
	}

	/**
	 * Function to get the record model based on the request parameters
	 * @param Vtiger_Request $request
	 * @return Vtiger_Record_Model or Module specific Record Model instance
	 */
	protected function getRecordModelFromRequest(Vtiger_Request $request) {


		$moduleName = $request->getModule();
		$recordId = $request->get('record');

		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);

		if(!empty($recordId)) {
			$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
			$modelData = $recordModel->getData();
			$recordModel->set('modcommentsid', $recordId);

			$recordModel->set('mode', 'edit');
		} else {
			$recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
			$modelData = $recordModel->getData();
			$recordModel->set('mode', '');
		}


		$fieldModelList = $moduleModel->getFields();

		foreach ($fieldModelList as $fieldName => $fieldModel) {
			$fieldValue = $request->get($fieldName, null);
			$fieldDataType = $fieldModel->getFieldDataType();
			if($fieldDataType == 'time'){
				$fieldValue = Vtiger_Time_UIType::getTimeValueWithSeconds($fieldValue);
			}
			if($fieldValue !== null) {
				if(!is_array($fieldValue)) {
					$fieldValue = trim($fieldValue);
				}
				$recordModel->set($fieldName, $fieldValue);

			}

		}


		return $recordModel;
	}

	//gaocl 2015-01-05 add start
	/**
	 * 关联模块编辑提交后返回一览页面URL取得
	 * @param Vtiger_Request $request
	 * @return 返回一览页面URL
	 */
	public function getParentRelationsListViewUrl(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$parentModuleName = $request->get('sourceModule');
		$parentRecordId = $request->get('sourceRecord');
		return 'index.php?module='.$parentModuleName.'&relatedModule='.$moduleName.'&view=Detail&record='.$parentRecordId.'&mode=showRelatedList';
	}
	//gaocl 2015-01-05 add end
}
