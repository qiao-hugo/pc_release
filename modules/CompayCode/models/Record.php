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
}