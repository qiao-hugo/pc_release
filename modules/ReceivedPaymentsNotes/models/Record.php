<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Inventory Record Model Class
 */
class ReceivedPaymentsNotes_Record_Model extends Vtiger_Record_Model {

    public function relieveMatchList($request){
        global $adb, $current_user;
        $realityDate=$request->get('realityDate');
        $paymentChannel=$request->get('paymentChannel');
        $payTitle=$request->get('payTitle');
        $paymentCode=$request->get('paymentCode');
        $standardMoney=$request->get('standardMoney');
        $pageNumber=$request->get('pageNumber');
        $size=$request->get('size');
        $matchDate=$request->get('matchDate');
        $userId=$request->get('userId');
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($userId);
        $sql="SELECT
	vtiger_receivedpayments.owncompany,
	vtiger_receivedpayments.paymentcode,
	vtiger_receivedpayments.paytitle,
	vtiger_receivedpayments.unit_price,
	vtiger_receivedpayments.paymentchannel,
	vtiger_receivedpayments.matchstatus,
	vtiger_servicecontracts.contract_no,
	vtiger_account.accountname,
	vtiger_receivedpayments.receivedpaymentsid
FROM
	( SELECT * FROM ( SELECT * FROM vtiger_receivedpayments_notes ORDER BY createtime DESC ) AS vtiger_receivedpayments_notes GROUP BY receivedpaymentsid ) AS vtiger_receivedpayments_notes
	LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid = vtiger_receivedpayments_notes.receivedpaymentsid
	LEFT JOIN vtiger_staypayment ON vtiger_staypayment.staypaymentid = vtiger_receivedpayments.staypaymentid
	LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_receivedpayments.relatetoid 
	LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_servicecontracts.sc_related_to
	LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_account.accountid
WHERE
	1 = 1 
	AND vtiger_receivedpayments.relatetoid > 0 
	and vtiger_receivedpayments.receivedstatus='normal'";
        if($realityDate){
            $sql .=" and vtiger_receivedpayments.reality_date='".$realityDate."'";
        }
        if($paymentChannel){
            $sql .=" and vtiger_receivedpayments.paymentchannel='".$paymentChannel."'";
        }
        if($payTitle){
            $sql .=" and vtiger_receivedpayments.paytitle =  '".$payTitle."'";
        }
        if($standardMoney){
            $sql .=" and vtiger_receivedpayments.standardmoney='".$standardMoney."'";
        }
        if($paymentCode){
            $sql .=" and vtiger_receivedpayments.paymentcode='".$paymentCode."'";
        }
        if($matchDate){
            $sql .=" and vtiger_receivedpayments.matchdate='".$matchDate."'";
        }
        $listQuery=$this->getUserWhere();
        $moduleModel = Vtiger_Module_Model::getInstance('ReceivedPaymentsNotes');
        $isShowButton=0;
        if($moduleModel->exportGrouprt('ReceivedPaymentsNotes','confirmrelieve')){
            //如果是财务，只展示
            $isShowButton=1;
        }
        $sql.=$listQuery;
        $sql.=" order by vtiger_receivedpayments_notes.receivedpaymentsnotesid desc LIMIT ".($pageNumber-1)*$size.",".$size;
        Matchreceivements_Record_Model::recordLog(array($sql),'sql');
        $listResult = $adb->pquery($sql, array());
        $listViewRecordModels = array();
        $recordArray=array();
        while($rawData=$adb->fetch_array($listResult)) {
            $recordArray['paymentCode']=$rawData['paymentcode'];
            $recordArray['accountName']=$rawData['accountname'];
            $recordArray['unitPrice']=$rawData['unit_price'];
            $recordArray['ownCompany']=$rawData['owncompany'];
            $recordArray['paymentChannel']=$rawData['paymentchannel'];
            $recordArray['pageNumber']=$pageNumber;
            $recordArray['size']=$size;
            $recordArray['id']=$rawData['receivedpaymentsid'];
            $recordArray['matchStatus']=$rawData['matchstatus'];
            $recordArray['contractNo']=$rawData['contract_no'];
            $recordArray['payTitle']=$rawData['paytitle'];
            $recordArray['isShowButton']=$isShowButton;
            $listViewRecordModels[] = $recordArray;
        }
        return $listViewRecordModels;
    }

    /**
     *
     * @throws AppException
     */
    public function getUserWhere(){
        global $current_user;
        $searchDepartment =$current_user->departmentid;
        $listQuery='';
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('ReceivedPaymentsNotes','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            //$listQuery= ' AND (EXISTS(SELECT 1 FROM vtiger_crmentity as crmtable WHERE  crmtable.deleted=0 AND crmtable.setype=\'Accounts\' AND crmtable.crmid=vtiger_servicecontracts.sc_related_to AND crmtable.smownerid in('.implode(',',$where).'))  OR vtiger_crmentity.smownerid in ('.implode(',',$where).'))';
//            $listQuery= ' AND vtiger_receivedpayments_notes.smownerid in ('.implode(',',$where).') ';
            $userImport=' or (vtiger_crmentity.smownerid in ('.implode(',',$where).') and vtiger_receivedpayments_notes.smownerid = 6934)';

            $listQuery= ' AND (vtiger_receivedpayments_notes.smownerid in ('.implode(',',$where).')'.$userImport.' )';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                //$listQuery= ' AND (EXISTS(SELECT 1 FROM vtiger_crmentity as crmtable WHERE  crmtable.deleted=0 AND crmtable.setype=\'Accounts\' AND crmtable.crmid=vtiger_servicecontracts.sc_related_to AND crmtable.smownerid '.$where.')  OR vtiger_crmentity.smownerid '.$where.')';
//                $listQuery= ' AND (vtiger_receivedpayments_notes.smownerid '.$where.')';
                $userImport=' or (vtiger_crmentity.smownerid '.$where.' and vtiger_receivedpayments_notes.smownerid = 6934)';
                $listQuery= ' AND (vtiger_receivedpayments_notes.smownerid '.$where.$userImport.')';
            }
        }
        return $listQuery;
    }


//    public function getUserWhere(){
//        global $current_user;
//        $searchDepartment = $_REQUEST['department'];
//        $listQuery='';
//        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
//            $userid=getDepartmentUser($searchDepartment);
//            $where=getAccessibleUsers('ReceivedPaymentsNotes','List',true);
//            if($where!='1=1'){
//                $where=array_intersect($where,$userid);
//            }else{
//                $where=$userid;
//            }
//            $where=!empty($where)?$where:array(-1);
//            //$listQuery= ' AND (EXISTS(SELECT 1 FROM vtiger_crmentity as crmtable WHERE  crmtable.deleted=0 AND crmtable.setype=\'Accounts\' AND crmtable.crmid=vtiger_servicecontracts.sc_related_to AND crmtable.smownerid in('.implode(',',$where).'))  OR vtiger_crmentity.smownerid in ('.implode(',',$where).'))';
////            //处理客户匹配
//            $userImport=' or (vtiger_crmentity.smownerid in ('.implode(',',$where).') and vtiger_receivedpayments_notes.smownerid = 6934)';
//
//            $listQuery= ' AND (vtiger_receivedpayments_notes.smownerid in ('.implode(',',$where).').'.$userImport.' )';
//        }else{
//            $where=getAccessibleUsers();
//            if($where!='1=1'){
//                //$listQuery= ' AND (EXISTS(SELECT 1 FROM vtiger_crmentity as crmtable WHERE  crmtable.deleted=0 AND crmtable.setype=\'Accounts\' AND crmtable.crmid=vtiger_servicecontracts.sc_related_to AND crmtable.smownerid '.$where.')  OR vtiger_crmentity.smownerid '.$where.')';
//                $userImport=' or (vtiger_crmentity.smownerid '.$where.' and vtiger_receivedpayments_notes.smownerid = 6934)';
//                $listQuery= ' AND (vtiger_receivedpayments_notes.smownerid '.$where.$userImport.')';
//            }
//        }
//        Matchreceivements_Record_Model::recordLog(array($listQuery),'sql');
//        return $listQuery;
//    }

    /**
     * 解绑匹配
     * @param $request
     * @return array|bool[]
     * @throws Exception
     */
    public function matchRelieve($request){
        global $adb,$current_user;
        $user = new Users();
        $userId=$request->get('userId');
        $current_user = $user->retrieveCurrentUserInfoFromFile($userId);
        $recordId= $request->get('record');
        $sql="select * from vtiger_receivedpayments where receivedpaymentsid=".$recordId;
        $receivedPaymentArray=$adb->run_query_allrecords($sql);
        $receivedPaymentArray = $receivedPaymentArray[0];
        $resData = array('flag'=>true);
        if($receivedPaymentArray['matchstatus']){
            //当匹配状态有值时判断是否是财务有权限
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('ReceivedPaymentsNotes','confirmrelieve')){   //权限验证
                $resData = array('flag'=>false,'msg'=>'无权限解除跨月匹配');
            }
        }else{
            //当匹配状态没值时判断是否跨月，跨月的话直接打回，并且修改状态
            $matchDate=$receivedPaymentArray['matchdate'];
            if(date('Y-m')!=date('Y-m',strtotime($matchDate))){
                $sql="update vtiger_receivedpayments set matchstatus=1 where receivedpaymentsid=?";
                $adb->pquery($sql,array($recordId));
                $resData = array('flag'=>false,'msg'=>'根据财务部回款匹配制度要求，匹配后次月解除匹配须至财务部办理。是否确认到财务部解除回款匹配？');
            }
        }
        if($resData['flag']){
            //以上条件满足开始处理
            $basicOject=new ReceivedPaymentsNotes_BasicAjax_Action();
            $resData=$basicOject->cleanReceive($request);
        }
        $return['flag']=$resData['flag'];
        if(!$return['flag']){
            $return['data']['errorMsg']=$resData['msg'];
        }
        Matchreceivements_Record_Model::recordLog($resData,'stay');
        return $return;
    }
}
