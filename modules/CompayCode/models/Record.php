<?php

class CompayCode_Record_Model extends Vtiger_Record_Model
{
    public function isExistByCompanyName($companyFullName)
    {
        global $adb;
        $sql = "select 1 from vtiger_company_code where companyfullname = ?";
        $res = $adb->pquery($sql, array($companyFullName));
        if ($adb->num_rows($res)) {
            return true;
        }
        return false;
    }

    public static function companyUserIds($modulename){
        $db = PearDatabase::getInstance();
        $sql = "select userid from vtiger_invoicecompanyuser where modulename=?";
        $result =$db->pquery($sql,array($modulename));
        if(!$db->num_rows($result)){
            return array();
        }

        while ($row = $db->fetchByAssoc($result)){
            $data[] = $row['userid'];
        }
        return $data;
    }

    public function getSealCode($invoicecompany,$company_code){
        $db=PearDatabase::getInstance();
        if($invoicecompany){
            $sql = "select b.sealcode from vtiger_invoicecompany a left join vtiger_company_code b on a.companyid=b.companyid where a.invoicecompany=?";
            $result =$db->pquery($sql,array($invoicecompany));
        }else{
            $sql = "select sealcode from  vtiger_company_code  where company_code=?";
            $result =$db->pquery($sql,array($company_code));
        }
         if(!$db->num_rows($result)){
            return '';
        }
        $row=$db->fetchByAssoc($result,0);
        return $row['sealcode'];
    }
}
