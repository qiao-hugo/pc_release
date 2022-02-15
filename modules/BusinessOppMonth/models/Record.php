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
class BusinessOppMonth_Record_Model extends Vtiger_Record_Model {

    /**
     * 取得当前根据部门取权限
     * @param $str
     * @return array
     */
    public static function getuserinfo($str){
        $db=PearDatabase::getInstance();
        $query="SELECT id,last_name FROM vtiger_users WHERE 1=1 {$str}";
        return $db->run_query_allrecords($query);
    }
    public static function getyears(){
        $db=PearDatabase::getInstance();
        $query="SELECT left(vtiger_leaddetails.mapcreattime,4) AS datetimes FROM vtiger_leaddetails WHERE vtiger_leaddetails.mapcreattime IS NOT NULL GROUP BY left(vtiger_leaddetails.mapcreattime,4)";
        return $db->run_query_allrecords($query);
    }
    public static function department(){
        $db=PearDatabase::getInstance();
        $query="SELECT vtiger_leadsystem.leadsystem FROM vtiger_leadsystem ";
        return $db->run_query_allrecords($query);
    }
}