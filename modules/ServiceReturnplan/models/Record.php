<?php
/*+********
 *客户信息管理
 **********/

class ServiceReturnplan_Record_Model extends Vtiger_Record_Model {
    static function getReturnplan_detail($Returnplanid){
        $adb=PearDatabase::getInstance();
        if($Returnplanid<1){return ;}
        return $adb->run_query_allrecords("SELECT * FROM `vtiger_servicereturnplan_detail` WHERE returnplanid={$Returnplanid} ORDER BY sequence");
    }
}
