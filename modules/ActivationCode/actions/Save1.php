<?php
class ActivationCode_Save_Action extends Vtiger_Save_Action {
	

	public function saveRecord($request) {

		$t = $this->check($request);
		if ($t > 0) {
			$msg = '';
			if ( in_array($t, array(1,2,3)) ) {
				$msg = '激活码格式不规范';
			}
			if ( in_array($t, array(4,5)) ) {
				$msg = '激活码输入重复';
			}
			echo '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html;charset=UTF-8"><title>错误提示</title></head><body><div>'.$msg.'</div><script type="text/javascript">setTimeout(function(){window.history.go(-1)},3000);</script></body></html>';die;
		}

        $recordModel = $this->getRecordModelFromRequest($request);
		$recordModel->save();
		return $recordModel;
	}


	public function check($request) {
		$activecode = $request->get('activecode');
		if (strlen($activecode) != 36) {
			return 1;
		}

		if (! preg_match('/^[a-z0-9-]+$/', $activecode)) {  
		    return 2;
		}

		$t = explode('-', $activecode);
		if(count($t) != 5) {
			return 3;
		}

		// 判断重复
		$db = PearDatabase::getInstance();
		$record = $request->get('record');
		if (empty($record)) { //添加
			// 判断激活码是否重复
			$sql = "select activecode from vtiger_activationcode where activecode=?";
			$sel_result = $db->pquery($sql, array($activecode));
			$res_cnt = $db->num_rows($sel_result);
			if ($res_cnt > 0) {
				return 4;
			}
		} else {  //修改
			$sql = "select activationcodeid from vtiger_activationcode where activecode=?";
			$sel_result = $db->pquery($sql, array($activecode));
			$row = $db->query_result_rowdata($sel_result, 0);
			if ($row > 0) {
				if($record != $row['activationcodeid']) {
					return 5;
				}
			}
		}

		// 判断激活码是否重复
		$sql = "select activecode from vtiger_activationcode where activecode=?";
		$db = PearDatabase::getInstance();
		$sel_result = $db->pquery($sql, array($activecode));
		$res_cnt = $db->num_rows($sel_result);
		if ($res_cnt > 0) {
			$record = $request->get('record');
			if (empty($record)) { //添加
				return 4;
			} else {  //修改
				$row = $db->query_result_rowdata($sel_result, 0);
				if($activecode != $row['activecode']) {

				}
			}
		}
		return 0;
	}
}
