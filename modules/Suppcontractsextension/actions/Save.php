<?php
/* +****************
 *合同保存验证
 *新增产品必填，打回后产品不可编辑
 * ******************* */

class Suppcontractsextension_Save_Action extends Vtiger_Save_Action {

	public function saveRecord($request) {
        $suppcontractsextensionid=$request->get('suppcontractsextensionid');
	    $recordid=$request->get('record');

        $recordModel = $this->getRecordModelFromRequest($request);
        if($recordid && $recordModel->entity->column_fields['suppcontractsextensionid']!=$suppcontractsextensionid){
            echo '编辑中合同不允许改变';
            exit;
        }

		$recordModel->save();
		return $recordModel;
	}
}
