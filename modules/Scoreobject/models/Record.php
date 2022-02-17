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
class Scoreobject_Record_Model extends Vtiger_Record_Model {

    // 获取组件参数数据
	static public function getScoreparas($id) {
        $sql = "select scoreparaid,scoreobjectid,scorepara_item,scorepara_score,scorepara_upper,scorepara_lower from vtiger_scorepara where scoreobjectid=? AND scorepara_deleted=0";
        $db = PearDatabase::getInstance();
        $sel_result = $db->pquery($sql, array($id));
        $res_cnt = $db->num_rows($sel_result);
        $res = array();
        if($res_cnt > 0) {
            while($rawData=$db->fetch_array($sel_result)) {
                $res[] = $rawData;
            }
        }
        return $res;
    }

    // 获取组件参数数据
    static public function getScoreobject($module) {
        $sql = "select * from vtiger_scoreobject where scoreobject_module=?";
        $db = PearDatabase::getInstance();
        $sel_result = $db->pquery($sql, array($module));
        $res_cnt = $db->num_rows($sel_result);
        $res = array();
        if($res_cnt > 0) {
            while($rawData=$db->fetch_array($sel_result)) {
                $res[$rawData['scoreobjectid']] = $rawData;
            }
        }
        return $res;
    }
}