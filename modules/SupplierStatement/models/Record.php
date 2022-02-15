<?php
/*+********
 *客户信息管理
 **********/

class SupplierStatement_Record_Model extends Vtiger_Record_Model {
    static function createStaypayment($fake_request){
        $db=PearDatabase::getInstance();
        //$result=$db->run_query_allrecords('');
        $ressorder=new Vtiger_Save_Action();
        $ressorder->saveRecord($fake_request);
        //$crmid=$db->getUniqueID('vtiger_crmentity');求表ID当前最大的
    }

    static function getaccinfoBYcontractid($contractid){
        $adb=PearDatabase::getInstance();
        $query="SELECT * FROM vtiger_crmentity WHERE crmid=? limit 1";
        $result=$adb->pquery($query,array($contractid));
        $resultdata=$adb->query_result_rowdata($result,0);
        if($resultdata['setype']=='ServiceContracts'){
            $sql = "SELECT accountid, accountname,effectivetime,companycode FROM vtiger_servicecontracts INNER JOIN vtiger_account ON sc_related_to = accountid WHERE servicecontractsid =? limit 1";
        }else{
            $sql = "SELECT vtiger_suppliercontracts.vendorid AS  accountid,vtiger_vendor.vendorname AS accountname,effectivetime,companycode FROM vtiger_suppliercontracts INNER JOIN vtiger_vendor ON vtiger_suppliercontracts.vendorid = vtiger_vendor.vendorid WHERE vtiger_suppliercontracts.suppliercontractsid =? limit 1";
        }

        $result = $adb->pquery($sql,array($contractid));
        if($adb->num_rows($result)>0){
            $temp = $adb->query_result_rowdata($result,0);
        }else{
            $temp = array();
        }
        return $temp;
    }


}
