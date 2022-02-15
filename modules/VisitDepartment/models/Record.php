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
class VisitDepartment_Record_Model extends Inventory_Record_Model {

    /**
     * 部门分析
     * @return bool
     */
    public function getVisitCommentanalysis($id){
        global $current_user;
        $db=PearDatabase::getInstance();
        $query="SELECT visitdepartmentid,visitingnum,visitingcommnum,classic,commentresult,poornumber,poorproportion,createdtime FROM vtiger_visitcommentanalysis WHERE visitdepartmentid={$id} AND deleted=0 ORDER BY classic";
        $result= $db->run_query_allrecords($query);
        $data=array();
        foreach($result as $value){
            ++$data[$value[classic]];
        }
        $column_field=$this->getEntity()->column_fields;
        $yearAndMonth=$column_field['year'].'-'.$column_field['month'];
        $userid=getDepartmentUser($column_field['deparmentid']);
        $query2="SELECT
                    vtiger_visitingorder.startdate,
                    vtiger_visitingorder.enddate,
                    vtiger_visitingorder.modulestatus,
                    vtiger_visitingorder.`subject`,
                    vtiger_visitingorder.`purpose`,
                (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_visitingorder.extractid=vtiger_users.id) AS username,
                (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_visitaccountcontractsheet.userid=vtiger_users.id) AS dusername,
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
        $resultd= $db->run_query_allrecords($query2);
        return array('data'=>$result,'nums'=>$data,'datadetail'=>$resultd);

    }

    /**
     * 部门改进意见
     * @param Vtiger_Request $request
     * @return array
     */
    public function getVisitDImprovement(Vtiger_Request $request){
        global $adb;
        $recordId=$request->get('record');
        $resultimprove=$adb->pquery('SELECT
                                        (SELECT last_name FROM vtiger_users WHERE id=vtiger_visitimprovement.userid) AS improvementname,
                                        vtiger_visitimprovement.datetime AS improvementdatetime,
                                        vtiger_visitimprovement.remark AS improvementdremark,
                                        vtiger_visitimprovement.schedule AS improvementdschedule,
                                        vtiger_improveschedule.`schedule`,
                                        vtiger_improveschedule.createdtime,
                                        (SELECT last_name FROM vtiger_users WHERE id=vtiger_improveschedule.userid) AS improveschedulename,
                                        vtiger_improveschedule.remark,
                                        vtiger_visitimprovement.visitimprovementid 
                                    FROM
                                        vtiger_visitimprovement
                                    LEFT JOIN vtiger_improveschedule ON (vtiger_visitimprovement.visitimprovementid = vtiger_improveschedule.visitimprovementid AND vtiger_improveschedule.module=\'VisitDepartment\')
                                    WHERE
                                    vtiger_visitimprovement.module=\'VisitDepartment\'
                                    AND
                                        vtiger_visitimprovement.visitingorderid = ?
                                    ORDER BY vtiger_visitimprovement.visitimprovementid DESC, vtiger_improveschedule.improvescheduleid DESC',array($recordId));

        $arri=array();
        while($row=$adb->fetch_row($resultimprove)){
            $row['visitimprovementid']=$row['visitimprovementid'];
            $arri[$row['visitimprovementid']]['visitimprovement']['improvementname']=$row['improvementname'];
            $arri[$row['visitimprovementid']]['visitimprovement']['improvementdatetime']=$row['improvementdatetime'];
            $arri[$row['visitimprovementid']]['visitimprovement']['improvementdremark']=$row['improvementdremark'];
            $arri[$row['visitimprovementid']]['visitimprovement']['visitimprovementid']=$row['visitimprovementid'];
            $arri[$row['visitimprovementid']]['visitimprovement']['improvementdschedule']=$row['improvementdschedule'];
            if(!empty($row['createdtime'])){
                $arri[$row['visitimprovementid']]['improveschedule'][]=array('schedule'=>$row['schedule'],'createdtime'=>$row['createdtime'],'improveschedulename'=>$row['improveschedulename'],'remark'=>$row['remark']);
            }
        }
        return array('visitimprovement'=>$arri);
    }
}
