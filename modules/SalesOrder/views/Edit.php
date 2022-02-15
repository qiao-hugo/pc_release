<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Class SalesOrder_Edit_View extends Vtiger_Edit_View {

	/*public function checkPermission(Vtiger_Request $request) {
        return true;
    }*/
    public function process(Vtiger_Request $request) {
		//+young.yang 2015-1-4 屏蔽验证，做全局验证
		//$this->checkAuth($request);
		//+end
		parent::process($request);
	}
	/**
	 * 状态编辑权限验证
	 */
	/* function checkAuth(Vtiger_Request $request){
		$recordId=$request->get('record');
		if(!empty($recordId)){
			$db=PearDatabase::getInstance();
			$result=$db->pquery("select sostatus from vtiger_salesorder where salesorderid=?",array($recordId));
			if($db->num_rows($result)){
				$sostatus=$db->query_result($result, 0,'sostatus');
				$auth=getWorkflowsStatus('edit',$sostatus);
				if(empty($auth)){
					throw new AppException('工单状态不允许修改');
				}
			}else{
				throw new AppException('访问记录不存在');
			}
		}
	} */
}