<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
class IdcRecords_ChangeAjax_Action extends Vtiger_Save_Action {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('autoIdcRecordsaccount');
		$this->exposeMethod('get_salesorder_relate');
	}
	
	public function checkPermission(Vtiger_Request $request) {
		return ;

	}

    /*2015年9月18日 根据工单id 查找客户以及 客户的负责人*/
    public function get_salesorder_relate(Vtiger_Request $request){
        $salesorderid = $request->get('salesorderid');
        $adb = PearDatabase::getInstance();
        $select_account_sql = 'SELECT smcreatorid,label,crmid FROM `vtiger_crmentity` WHERE crmid =(SELECT accountid FROM vtiger_salesorder WHERE salesorderid=?)';
        $result = $adb->pquery($select_account_sql,array($salesorderid));
        $accountli=array();
        if($adb->num_rows($result)>0){
            $accountli = $adb->fetchByAssoc($result,0);
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($accountli);
        $response->emit();
    }

    /**
     * @param Vtiger_Request $request
     * 获取公司负责人 20150706/adatian
     * 获取工单编号
     */

    public function autoIdcRecordsaccount (Vtiger_Request $request){
        $accountid = $request->get(accountid);
        $db=PearDatabase::getInstance();
        //查询公司负责人
        $smcreatorid = $db->pquery('SELECT smcreatorid FROM `vtiger_crmentity` WHERE crmid = ?',array($accountid));
       // $smcreatoridlist = $db->query_result($smcreatorid,0,'smcreatorid');
        $rowssmcreatorid = $db->num_rows($smcreatorid);
        for ($i=0; $i<$rowssmcreatorid; ++$i) {
            $num = $db->fetchByAssoc($smcreatorid);
            $smcreatoridlist = $num;
        }
        //查询工单编号
        $salesorder_no = $db->pquery('SELECT salesorder_no FROM `vtiger_salesorder` WHERE accountid = ?',array($accountid));

        if($db->num_rows($salesorder_no)) {
            for($i=0;$i<$db->num_rows($salesorder_no);$i++){
                $row = $db->fetchByAssoc($salesorder_no);
                $tmp['salesorder_no'] = $row['salesorder_no'];
                $arr[] = $tmp;
            }
        }
        $datalist=array($smcreatoridlist,$arr);
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($datalist);
        $response->emit();
    }
	
	public function process(Vtiger_Request $request) {
		$type = $request->get('type');
		$id = $request->get('record');
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}


		$result = array('label'=>decode_html(''));
		$response = new Vtiger_Response();
		$response->setResult($result);
		$response->emit();
	    return;
	}
}
