<?php
/*+********
 *客户信息管理
 **********/

class ServiceContractsRule_Record_Model extends Vtiger_Record_Model {
    static function createStaypayment($fake_request){
        $db=PearDatabase::getInstance();
        //$result=$db->run_query_allrecords('');
        $ressorder=new Vtiger_Save_Action();
        $ressorder->saveRecord($fake_request);
        //$crmid=$db->getUniqueID('vtiger_crmentity');求表ID当前最大的
    }

    static function getaccinfoBYcontractid($contractid){
        $adb=PearDatabase::getInstance();
        $sql = "SELECT accountid, accountname FROM vtiger_servicecontracts INNER JOIN vtiger_account ON sc_related_to = accountid WHERE servicecontractsid =? limit 1";
        $result = $adb->pquery($sql,array($contractid));
        if($adb->num_rows($result)>0){
            $temp = $adb->query_result_rowdata($result,0);
        }else{
            $temp = array();
        }
        return $temp;
    }
}
