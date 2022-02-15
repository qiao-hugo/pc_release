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
class Workday_Record_Model extends Vtiger_Record_Model {
    //传入日期，查询是否上班;
    function get_daytype($date){
        $adb = PearDatabase::getInstance();
        $date = date("Y-m-d",$date);
        $sql = "SELECT datetype FROM vtiger_workday WHERE dateday = ? LIMIT 1";
        $result_li = $adb->pquery($sql,array($date));
        $result = $adb->query_result($result_li, 0, 'datetype');
        return $result;
    }

    public function getNextWorkDay(Vtiger_Request $request){
        $db=PearDatabase::getInstance();
        $datetime=$request->get('datetime');
        $datetype=$request->get('datetype','holiday');
        $sql = "Select * from vtiger_workday where datetype = ? and dateday>? order by dateday asc limit 20";
        $result = $db->pquery($sql,array($datetype,$datetime));
        if($db->num_rows($result)){
            while ($row = $db->fetch_array($result)) {
                $datas[] =  $row['dateday'];
            }
        }
        while (true){
            $next_day = date('Y-m-d',strtotime($datetime)+24*60*60);
            if(in_array($next_day,$datas)){
                $datetime = $next_day;
                continue;
            }
            return $next_day;
        }
        return '';
    }
}