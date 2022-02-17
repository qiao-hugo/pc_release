<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class VisitDepartment_Module_Model extends Vtiger_Module_Model {

    public function getaddVCommentanalysis(Vtiger_Request $request){
        $deparmentid=$request->get('deparmentid');
        $year=$request->get('year');
        $month=$request->get('month');
        $yearAndMonth=$year.'-'.$month;
        $userid=getDepartmentUser($deparmentid);
        $query="SELECT
            (SELECT count(1) FROM
                vtiger_visitingorder
            LEFT JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid = vtiger_crmentity.crmid
            WHERE vtiger_crmentity.deleted = 0
            AND vtiger_visitingorder.related_to>0
            AND left(vtiger_visitingorder.startdate,7)='{$yearAndMonth}'
            AND vtiger_visitingorder.extractid in(".implode(',',$userid).")
            AND vtiger_visitingorder.modulestatus = 'c_complete') as visitingnum,
            (SELECT count(1) FROM vtiger_visitaccountcontract AS a WHERE LEFT(a.vstartdate,7) = '{$yearAndMonth}' AND a.commentstaus!='' AND a.commentstaus IS NOT NULL AND a.vextractid IN(".implode(',',$userid).")) AS visitingcommnum,
            count(1) AS poornumber,
            vtiger_visitaccountcontractsheet.classic,
            vtiger_visitaccountcontractsheet.commentresult
        FROM vtiger_visitaccountcontractsheet
        LEFT JOIN vtiger_visitaccountcontract ON vtiger_visitaccountcontractsheet.visitaccountcontractid = vtiger_visitaccountcontract.visitaccountcontractid
        WHERE  LEFT(vtiger_visitaccountcontract.vstartdate,7) = '{$yearAndMonth}' AND vtiger_visitaccountcontract.vextractid IN(".implode(',',$userid).")
        GROUP BY classic,commentresult";
        global $adb;
        $result= $adb->run_query_allrecords($query);
        $data=array();
        foreach($result as $value){
            ++$data[$value[classic]];
        }
        $query2="SELECT
                    vtiger_visitingorder.startdate,
                    vtiger_visitingorder.enddate,
                    vtiger_visitingorder.modulestatus,
                    vtiger_visitingorder.`subject`,
                    vtiger_visitingorder.`purpose`,
                (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[ÀëÖ°]'))) as last_name from vtiger_users where vtiger_visitingorder.extractid=vtiger_users.id) AS username,
                (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[ÀëÖ°]'))) as last_name from vtiger_users where vtiger_visitaccountcontractsheet.userid=vtiger_users.id) AS dusername,
                (SELECT accountname FROM vtiger_account WHERE vtiger_account.accountid=vtiger_visitingorder.related_to limit 1) AS accname,
                    vtiger_visitaccountcontractsheet.commentdatetime,
                    vtiger_visitaccountcontractsheet.classic,
                vtiger_visitaccountcontractsheet.remark,
                vtiger_visitaccountcontract.commentstaus,
                    vtiger_visitaccountcontractsheet.commentresult
                FROM
                    vtiger_visitaccountcontractsheet
                LEFT JOIN vtiger_visitaccountcontract ON vtiger_visitaccountcontractsheet.visitaccountcontractid = vtiger_visitaccountcontract.visitaccountcontractid
                LEFT JOIN vtiger_visitingorder ON vtiger_visitingorder.visitingorderid = vtiger_visitaccountcontract.visitingorderid
                WHERE  LEFT(vtiger_visitaccountcontract.vstartdate,7) = '{$yearAndMonth}' AND vtiger_visitaccountcontract.vextractid IN(".implode(',',$userid).")
                order by vtiger_visitaccountcontract.vextractid,vtiger_visitaccountcontractsheet.classic
                ";
        $resultd= $adb->run_query_allrecords($query2);
        return array('data'=>$result,'nums'=>$data,'datadetail'=>$resultd);
    }
}
