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
class TyunReportanalysis_Record_Model extends Vtiger_Record_Model {

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
        $query="SELECT LEFT(vtiger_crmentity.createdtime,4) AS datetimes FROM	vtiger_account LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid WHERE	vtiger_crmentity.createdtime IS NOT NULL AND left(vtiger_crmentity.createdtime,4)>2014 GROUP BY	LEFT(vtiger_crmentity.createdtime,4)";
        return $db->run_query_allrecords($query);
    }

    /**
     * 获取指定日期段内每一天的日期
     * @param  Date  $startdate 开始日期
     * @param  Date  $enddate   结束日期
     * @return Array
     */
    public static  function getDateFromRange($startdate, $enddate){
        $stimestamp = strtotime($startdate);
        $etimestamp = strtotime($enddate);
        // 计算日期段内有多少天
        $days = ($etimestamp-$stimestamp)/86400+1;

        // 保存每天日期
        $date = array();

        for($i=0; $i<$days; $i++){

            $date[]['stat_date'] = date('Y-m-d', $stimestamp+(86400*$i));
        }

        return $date;
    }
    /**
     * 获取指定日期段内月份
     * @param  Date  $startdate 开始日期
     * @param  Date  $enddate   结束日期
     * @return Array
     */
    public static  function getMonthFromRange($startdate, $enddate){
        $stimestamp = strtotime($startdate);
        $etimestamp = strtotime($enddate);
        $i            = false; //开始标示
        // 保存月份
        $month = array();
        if($stimestamp == $etimestamp){
            $month[]['stat_date'] =$startdate;
            return $month;
        }
        while( $stimestamp < $etimestamp ) {
            $newMonth = !$i ? date('Y-m', strtotime('+0 Month', $stimestamp)) : date('Y-m', strtotime('+1 Month', $stimestamp));
            $stimestamp = strtotime( $newMonth );
            $i = true;
            if($stimestamp < $etimestamp){
                $month[]['stat_date'] =$newMonth;
            }
        }
        return $month;
    }
}