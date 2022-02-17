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
class Scoremodel_Record_Model extends Vtiger_Record_Model {


    static public function getScoremodelContent($id) {
        $sql = "select scoremodel_content from vtiger_scoremodel where scoremodelid=?";
        $db = PearDatabase::getInstance();
        $sel_result = $db->pquery($sql, array($id));
        $res_cnt = $db->num_rows($sel_result);
        if($res_cnt > 0) {
            $row = $db->query_result_rowdata($sel_result, 0);
            return json_decode(stripslashes(htmlspecialchars_decode($row['scoremodel_content'])), true);
        }
        return array();
    }

    static public function scoremodelContentDecode($content) {
        return json_decode(stripslashes(htmlspecialchars_decode($content)), true);
    }

    static public function getScoremodel($id) {
        $sql = "select * from vtiger_scoremodel where scoremodelid=?";
        $db = PearDatabase::getInstance();
        $sel_result = $db->pquery($sql, array($id));
        $res_cnt = $db->num_rows($sel_result);
        if($res_cnt > 0) {
            $row = $db->query_result_rowdata($sel_result, 0);
            $row['scoremodel_content'] = json_decode(stripslashes(htmlspecialchars_decode($row['scoremodel_content'])), true);
            return $row;
        }
        return array();
    }
}