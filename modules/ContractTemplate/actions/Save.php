<?php
class ContractTemplate_Save_Action extends Vtiger_Save_Action {
	public function saveRecord($request) {
        $adb =PearDatabase::getInstance();
        if($request->get('record')>0){

        }else{

        }
        if(count($_REQUEST['file'])!=1)
        {
            echo '模板有且只能一个';
            exit;
        }
        if($this->checkContractTemplete($request->get('contract_template'),$request->get('record')))
        {
            echo '模板已经存在';
            exit;
        }
        $recordModel = $this->getRecordModelFromRequest($request);
		$recordModel->save();

		return $recordModel;
	}
	public function checkContractTemplete($contratname,$recrod)
    {
        $adb =PearDatabase::getInstance();
        $query='SELECT 1 FROM `vtiger_contract_template` WHERE contract_template=? AND contract_templateid!=?';
        $result=$adb->pquery($query,array($contratname,$recrod));
        if($adb->num_rows($result))
        {
            return true;
        }
        return false;
    }
}
