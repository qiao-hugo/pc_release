<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
class IdcRecords_ListAjax_Action extends Vtiger_Save_Action {
    /**
     * 列表页面更新，状态值
     * @param Vtiger_Request $request
     */

    public function process(Vtiger_Request $request) {

        $recordid = $request->get('recordid');
        $stateName = $request->get('stateName');  //暂时没用
        $stateValue = $request->get('stateValue');
        $stateValue = urldecode($stateValue);      //接收js返回值，解析编码

        if($recordid!=""){
            $db=PearDatabase::getInstance();
            $recordid=rtrim($recordid,',');
            $sql="update vtiger_idcrecords set idcstate='$stateValue',modifiedtime=sysdate() where idcrecordsid IN ($recordid)";
            $db->pquery($sql,array());
            $success =true;
        }else{
            $success = false;
        }
        echo json_encode(array('success'=>$success));
	}
}
