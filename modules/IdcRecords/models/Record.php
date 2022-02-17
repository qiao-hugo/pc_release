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
 * Vtiger Entity Record Model Class
 */
class IdcRecords_Record_Model extends Vtiger_Record_Model {
    //获取类型
    public static function getIdcRecordsType($id) {
        if (!empty($id)){
            $db = PearDatabase::getInstance();
            $sqlQuery="SELECT idctype FROM `vtiger_idcrecords` WHERE idcrecordsid =?";
            $result = $db->pquery($sqlQuery, array($id));
            $idcrecordstype=$db->query_result($result,0,'idctype');
        }
        if($idcrecordstype =='china'){
            return 'LBL_CHINA_AIDCTYPE';
        }else{
            return 'LBL_ROREGIN_AIDCTYPE';
        }
    }
    //详情，获取域名
    public static function getIdcRecordsName($id){
        if (!empty($id)){
            $db = PearDatabase::getInstance();
            $sqlQuery="SELECT domainname FROM `vtiger_idcrecords` WHERE idcrecordsid =?";
            $result = $db->pquery($sqlQuery, array($id));
            $domainname=$db->query_result($result,0,'domainname');
            $value = str_replace(' |##| ','<br>',$domainname);
            return $value;
        }else{
            return ;
        }
    }
    //编辑,获取文本框内域名
    public static function getIdcRecordsDomainName($id){
        if (!empty($id)){
            $db = PearDatabase::getInstance();
            $sqlQuery="SELECT domainname FROM `vtiger_idcrecords` WHERE idcrecordsid =?";
            $result = $db->pquery($sqlQuery, array($id));
            $domainname=$db->query_result($result,0,'domainname');
            $value = str_replace(' |##| ',"\n",$domainname);
            return $value;
        }else{
            return ;
        }
    }

}
