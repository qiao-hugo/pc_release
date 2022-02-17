<?php
class ServiceContractsRule_Save_Action extends Vtiger_Save_Action {
	public function saveRecord($request) {
        $adb =PearDatabase::getInstance();
        if($request->get('record')>0){

        }else{

        }
        $recordModel = $this->getRecordModelFromRequest($request);
		$recordModel->save();
		return $recordModel;
	}
}
