<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class WorkSummarize_SaveAjax_Action extends Vtiger_SaveAjax_Action {

	public function process(Vtiger_Request $request) {
		vglobal('currentView','List');
		$db=PearDatabase::getInstance();
		global $current_user;
		$datetime=date("Y-m-d H:i:s");
		if($_REQUEST['record']>0){
			$sql="insert into vtiger_reply (`relatedid`,`replycontent`,`replyuser`,`createdtime`) values(?,?,'{$current_user->id}','$datetime')";
			$db->pquery($sql,array($_REQUEST['record'],$_REQUEST['content']));
			$query="UPDATE vtiger_worksummarize SET replytimes=replytimes+1 WHERE worksummarizeid=?";
			$db->pquery($query,array($_REQUEST['record']));
		}
		$result['datd']=1;
		$response = new Vtiger_Response();
		$response->setEmitType(Vtiger_Response::$EMIT_JSON);
		$response->setResult($result);
		$response->emit();
	}
}
