<?php
class OrderChargeback_Save_Action extends Vtiger_Save_Action {

	public function saveRecord($request) {
		$record=$request->get('record');
		$servicecontractsid=$request->get('servicecontractsid');
        $recordModel = $this->getRecordModelFromRequest($request);
		if($record && $servicecontractsid){
			//有合同//不考虑合同被修改//如果变更了合同
			if($recordModel->entity->column_fields['servicecontractsid']!=$servicecontractsid){
				echo '编辑中合同不允许改变';
				exit;
			}
		}

       	$recordModel->save();
        if($_REQUEST['issubmit']&&!empty($_REQUEST['issubmit'])){
		//工单确定认提交后更改其状态防止通过审核时第一个节点审核人通过编辑查看工单详情导致负责人更改的情况
            global $adb;
            $adb->pquery("UPDATE vtiger_orderchargeback SET modulestatus=(IF(modulestatus='a_normal' OR modulestatus='a_exception','b_actioning',modulestatus)) WHERE vtiger_orderchargeback.orderchargebackid=?",array($recordModel->getId()));
        }
		return $recordModel;
	}
}
