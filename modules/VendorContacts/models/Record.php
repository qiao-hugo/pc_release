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
 * ModComments Record Model
 */
class VendorContacts_Record_Model extends Vtiger_Record_Model {
	/**
	 * 更新跟进状态
	 * @author steel
	 * @param $visitingorderid
	 */
	public static function updateVisitingOrderFollowstatus($visitingorderid) {
		global $current_user;
		$db = PearDatabase::getInstance();
		$query='SELECT createdtime from vtiger_crmentity WHERE crmid=?';
		$result=$db->pquery($query,array($visitingorderid));
		$result_time=$db->query_result($result, 0, 'createdtime');
		$createtime=strtotime($result_time)+24*3600;
		$now=time();
		$updateSql="UPDATE vtiger_visitingorder SET followstatus='followup',followtime=?,followid=?";
		$updateSql.=$createtime>$now?",dayfollowup='是'":"";
		$datetime=date('Y-m-d H:i:s');
		$updateSql.=" WHERE visitingorderid=?";
		$db->pquery($updateSql, array($datetime,$current_user->id,$visitingorderid));
	}
	public function getSoundAndComments(Vtiger_Request $request){
        global $adb;
        $recordId=$request->get('record');
        $result=$adb->pquery('SELECT 
                                    vtiger_visitaccountcontractsheet.commentdatetime,
                                    vtiger_visitaccountcontractsheet.classic,
                                    vtiger_visitaccountcontractsheet.commentresult,
                                    vtiger_visitaccountcontractsheet.remark,
                                    vtiger_visitaccountcontractsheet.userid,
                                    vtiger_users.last_name as username 
                                FROM vtiger_visitaccountcontractsheet 
                                LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_visitaccountcontractsheet.userid
                                LEFT JOIN vtiger_visitaccountcontract ON vtiger_visitaccountcontract.visitaccountcontractid=vtiger_visitaccountcontractsheet.visitaccountcontractid
                                WHERE vtiger_visitaccountcontract.visitingorderid=? order by vtiger_visitaccountcontractsheet.userid,vtiger_visitaccountcontractsheet.classic,vtiger_visitaccountcontractsheet.commentresult,visitaccountcontractsheetid desc',array($recordId));
        $arr=array();
        while($row=$adb->fetch_row($result)){
            ++$arr[$row['userid']]['basic'][$row['classic']];
            $row['classict']=$row['classic'];
            $row['classic']=vtranslate($row['classic'],'VisitAccountContract');
            $row['commentresult']=vtranslate($row['commentresult'],'VisitAccountContract');
            $arr[$row['userid']]['basic']['name']=$row['username'];
            $arr[$row['userid']]['data'][]=$row;
        }
        $resultimprove=$adb->pquery('SELECT
                                        (SELECT last_name FROM vtiger_users WHERE id=vtiger_visitimprovement.userid) AS improvementname,
                                        vtiger_visitimprovement.datetime AS improvementdatetime,
                                        vtiger_visitimprovement.remark AS improvementdremark,
                                        vtiger_improveschedule.`schedule`,
                                        vtiger_improveschedule.createdtime,
                                        (SELECT last_name FROM vtiger_users WHERE id=vtiger_improveschedule.userid) AS improveschedulename,
                                        vtiger_improveschedule.remark,
                                        vtiger_visitimprovement.visitimprovementid 
                                    FROM
                                        vtiger_visitimprovement
                                    LEFT JOIN vtiger_improveschedule ON (vtiger_visitimprovement.visitimprovementid = vtiger_improveschedule.visitimprovementid AND vtiger_improveschedule.module=\'VisitingOrder\')
                                    WHERE
                                    vtiger_visitimprovement.module=\'VisitingOrder\'
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
            if(!empty($row['createdtime'])){
                $arri[$row['visitimprovementid']]['improveschedule'][]=array('schedule'=>$row['schedule'],'createdtime'=>$row['createdtime'],'improveschedulename'=>$row['improveschedulename'],'remark'=>$row['remark']);
            }
        }
        return array('visitaccountcontract'=>$arr,'visitimprovement'=>$arri);
	}
}