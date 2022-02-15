<?php
class WorkFlowCheck_Save_Action extends Vtiger_Save_Action {
	public function checkPermission(Vtiger_Request $request) {
		throw new AppException('LBL_PERMISSION_DENIED');
	}
}
