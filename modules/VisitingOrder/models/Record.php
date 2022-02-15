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
class VisitingOrder_Record_Model extends Vtiger_Record_Model {
    public $managementRole=array('H78','H79','H80','H95');
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
    /**
     * 取得当前充值申请单的要提醒的用户信息
     * @param $recordid
     */
    public function getSendWinXinUser($recordid){
        /**
         * 充值申请单的微信消息提醒
         */
        $db=PearDatabase::getInstance();
        //$recordid = $request->get('record');
        $query="SELECT vtiger_salesorderworkflowstages.workflowstagesid,workflowsid,ishigher,higherid,platformids FROM vtiger_salesorderworkflowstages WHERE isaction=1 AND salesorderid= ?
                AND vtiger_salesorderworkflowstages.modulename = 'VisitingOrder'";
        $result=$db->pquery($query,array($recordid));
        $num=$db->num_rows($result);

        if($num){

            $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'VisitingOrder');
            $entity = $recordModel->entity->column_fields;
            for($j=0;$j<$num;$j++){
                $workflowstagesid=$db->query_result($result,$j,'workflowstagesid');
                $workflowsid=$db->query_result($result,$j,'workflowsid');
                $ishigher=$db->query_result($result,$j,'ishigher');
                $higherid=$db->query_result($result,$j,'higherid');
                if($ishigher==1 && $higherid>0){
                    //有指定的人员审核
                    $query="SELECT vtiger_users.email1 FROM vtiger_users LEFT JOIN vtiger_user2role ON vtiger_users.id=vtiger_user2role.userid WHERE vtiger_users.`status`='Active' AND vtiger_users.id=?";
                    $userresult=$db->pquery($query,array($higherid));
                    $usernum=$db->num_rows($userresult);
                    if($usernum){
                        $email=$db->query_result($userresult,0,'email1');
                        if(Vtiger_Record_Model::checkEmail(trim($email))){
                            $content='<div class=\"gray\">'.date('Y年m月d日').'</div><div class=\"normal\">与您相关的拜访单需要审核,目的地为:</div><div class=\"highlight\">'.$entity['destination'].'</div>请及时处理';
                            //$email='steel.liu@71360.com';
                            //$this->setweixincontracts(array('email'=>trim($email),'content'=>$content,'flag'=>6));
                            global $m_crm_domain_index_url;
                            $dataurl=$m_crm_domain_index_url.'?module=VisitingOrder&action=detail&record='.$recordid;
//                            $dataurl='http://m.crm.71360.com/index.php?module=VisitingOrder&action=detail&record='.$recordid;
                            $this->sendWechatMessage(array('email'=>trim($email),'description'=>$content,'dataurl'=>$dataurl,'title'=>'拜访单审核','flag'=>7));
                        }
                    }
                }else{
                    /*
                    global $root_directory;
                    //没有指定的人员审核查找该节点对应的角色
                    include $root_directory."crmcache".DIRECTORY_SEPARATOR."workflows".DIRECTORY_SEPARATOR."{$workflowsid}.php";
                    if(!empty($workflows['stage'])) {
                        foreach ($workflows['stage'] as $key=>$value){
                            //查找对应节点的审核角色
                            if($value['workflowstagesid']==$workflowstagesid){
                                if(!empty($value['isrole'])){
                                    $userrole="'";
                                    $userrole.=str_replace(' |##| ',"','",$value['isrole']);
                                    $userrole.="'";
                                    $query="SELECT vtiger_users.email1 FROM vtiger_users LEFT JOIN vtiger_user2role ON vtiger_users.id=vtiger_user2role.userid WHERE vtiger_users.`status`='Active' AND vtiger_user2role.roleid in({$userrole})";
                                    $userresult=$db->pquery($query,array());
                                    $usernum=$db->num_rows($userresult);
                                    if($usernum){
                                        $userstr='';
                                        for($i=0;$i<$usernum;$i++){
                                            $email=$db->query_result($userresult,$i,'email1');
                                            if($this->checkEmail(trim($email))){
                                                $userstr.=trim($email)."|";
                                            }
                                        }
                                        $userstr=rtrim($userstr,'|');
                                        //$userstr='steel.liu@71360.com';
                                        $content='与您相关的拜访单需要审核,目的地为:'.$entity['destination'].',请及时处理';
                                        $this->setweixincontracts(array('email'=>$userstr,'content'=>$content,'flag'=>6));
                                    }
                                }
                            }
                        }
                    }*/
                }
            }
        }
	$this->sendWechatMessageBYProposerANDAttendant($recordid);
    }
    public function number2chinese($num)
    {
        if (is_int($num)) {
            $char = array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九');
            $unit = array('', '十', '百', '千', '万');
            $return = '';
            if ($num < 10) {
                $return = $char[$num];
            } elseif ($num%10 == 0) {
                $firstNum = substr($num, 0, 1);
                if ($num != 10) $return .= $char[$firstNum];
                $return .= $unit[strlen($num) - 1];
            } elseif ($num < 20) {
                $return = $unit[substr($num, 0, -1)]. $char[substr($num, -1)];
            } else {
                $numData = str_split($num);
                $numLength = count($numData) - 1;
                foreach ($numData as $k => $v) {
                    if ($k == $numLength) continue;
                    $return .= $char[$v];
                    if ($v != 0) $return .= $unit[$numLength - $k];
                }
                $return .= $char[substr($num, -1)];
            }
            return $return;
        }
    }
    /**
     * 有效拜访数添加
     * @param Vtiger_Request $request
     */
    public function addEffectiveVisits(Vtiger_Request $request,$userid){
        global $adb;
        $related_to=$request->get('related_to');
        $visitingorderid=$request->get('visitingorderid');
        $accompany=$request->get('accompany');
        $adb->pquery('DELETE FROM vtiger_effective_visits WHERE visitingorderid=?',array($visitingorderid));
        $startdate=$request->get('startdate');
        $firstDayStartDate=substr($startdate,0,7).'-01';
        if($related_to>0){
            $beforeMarch=date('Y-m-d', strtotime($firstDayStartDate . ' -3 month'));
            $lastDayOfPreviousMonth=date('Y-m-d', strtotime($firstDayStartDate . ' -1 day'));
            $query="SELECT 1 FROM vtiger_effective_visits WHERE userid=? AND accountid={$related_to} AND signnum>0 AND iscomplete=1 AND visit_start_date BETWEEN '".$beforeMarch."' AND '".$lastDayOfPreviousMonth."'";
            $resultData=$adb->pquery($query,array($userid));
            $visitInMarch=0;
            if($adb->num_rows($resultData)){
                $visitInMarch=1;
            }
            $insertSQL='INSERT INTO vtiger_effective_visits (visitingorderid,accountid,iscomplete,userid,signnum,visit_in_march,visit_start_date,visitingclass,visitingsequence,ismanager) 
                    VALUES('.$visitingorderid.','.$related_to.',0,?,?,?,\''.$startdate.'\',?,?,?)';
            $adb->pquery($insertSQL,array($userid,0,$visitInMarch,0,1,0));
            if (!empty($accompany)) {
                $i=2;
                $roleid="'".implode("','",$this->managementRole)."'";
                foreach($accompany as $value){
                    if($value<1){
                        continue;
                    }
                    $ismanager=0;
                    $resultData=$adb->pquery($query,array($value));
                    $visitInMarch=0;
                    if($adb->num_rows($resultData)){
                        $visitInMarch=1;
                    }
                    $queryrole='SELECT 1 FROM vtiger_user2role WHERE userid=? AND roleid in('.$roleid.')';
                    $roleResult=$adb->pquery($queryrole,array($value));
                    if($adb->num_rows($roleResult)){
                        $ismanager=1;
                    }
                    /*$user = new Users();
                    $current_userTemp = $user->retrieveCurrentUserInfoFromFile($value);
                    if(in_array($current_userTemp->roleid,$this->managementRole)){
                        $ismanager=1;
                    }*/
                    $adb->pquery($insertSQL,array($value,0,$visitInMarch,1,$i,$ismanager));
                    $i++;
                }
            }
        }
    }
    public function setEffectiveVisits($record,$userid){
        global $adb;
        $query='SELECT 1 FROM `vtiger_effective_visits` WHERE visitingorderid=? AND userid=?';
        $result=$adb->pquery($query,array($record,$userid));
        $num=$adb->num_rows($result);
        if(!$num){//非提单人或陪同人
            return ;
        }
        $query='SELECT * FROM `vtiger_effective_visits` WHERE visitingorderid=? ORDER BY visitingsequence ASC';
        $result=$adb->pquery($query,array($record));
        $num=$adb->num_rows($result);
        if($num){
            if($num==1){//只有提单人
                $updateSQL='UPDATE vtiger_effective_visits SET signnum=2 WHERE visitingorderid=? AND signnum=0 AND userid=? AND visit_in_march=0';
                $adb->pquery($updateSQL,array($record,$userid));
            }else{//提单人+陪同人
                $array=array();
                $ismanager=0;//有无管理岗0,没有,1有
                $ismanagernum=0;//管理岗人数
                $signnumsequence=0;//第几个人签到
                while($row=$adb->fetch_array($result)){
                    $array[$row['userid']]=$row;
                    if($row['signnum'] && $signnumsequence==0 && $row['visitingsequence']>1){
                        //signnum没有签到的为0,签到的为1或2
                        $signnumsequence=$row['visitingsequence'];
                    }
                    if($row['ismanager']){
                        $ismanager=1;
                        if($row['visitingsequence']>1){
                            $ismanagernum++;
                        }
                    }
                }
                $currentData=$array[$userid];//当前签到人的信息
                //全是管理岗
                if($ismanager==1 && ($ismanagernum==$num || $ismanagernum==($num-1))){
                    if($currentData['visitingsequence']==1){//提单人签到
                        $updateSQL='UPDATE vtiger_effective_visits SET signnum=2 WHERE visitingorderid=? AND visitingsequence=1 AND visit_in_march=0';
                        $adb->pquery($updateSQL,array($record));
                    }else{
                        //提单人未签到或陪同人先签到
                        $updateSQL='UPDATE vtiger_effective_visits SET signnum=1 WHERE visitingorderid=? AND signnum=0 AND visitingsequence=1 AND visit_in_march=0';
                        $adb->pquery($updateSQL,array($record));//将提单人签到为1
                    }
                }else{//非管理岗,不管提单人签不签到只有陪同人签到就是1个拜访
                    $updateSQL='UPDATE vtiger_effective_visits SET signnum=1 WHERE visitingorderid=? AND visitingsequence=1 AND visit_in_march=0';
                    $adb->pquery($updateSQL,array($record));
                    if(($signnumsequence==0 || $currentData['visitingsequence']<$signnumsequence) && $currentData['ismanager']==0){
                        $updateSQL='UPDATE vtiger_effective_visits SET signnum=1 WHERE visitingorderid=? AND userid=? AND visit_in_march=0';
                        $adb->pquery($updateSQL,array($record,$userid));
                        $updateSQL='UPDATE vtiger_effective_visits SET signnum=0 WHERE visitingorderid=? AND visitingsequence>?';
                        $adb->pquery($updateSQL,array($record,$currentData['visitingsequence']));//将该签到人后面已签到的全部置为0
                    }
                }
            }
        }
    }
    public function addEffectiveVisitsOnce(){
        set_time_limit(0);
        global $adb;
        $query='SELECT vtiger_visitingorder.related_to,accompany,extractid,startdate,modulestatus,visitingorderid FROM vtiger_visitingorder LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_visitingorder.visitingorderid WHERE vtiger_crmentity.deleted=0 and startdate>\'2018-05\' ORDER BY visitingorderid ASC';
        $result=$adb->pquery($query,array());
        while($row=$adb->fetch_array($result)){
            if(!empty($row['accompany'])) {
                $row['accompany']=explode(' |##| ', $row['accompany']);
            }
            if($row['extractid']){
                $this->addEffectiveVisits(new Vtiger_Request($row, $row),$row['extractid']);
            }
            if($row['modulestatus']=='c_complete'){
                $query='UPDATE vtiger_effective_visits SET iscomplete=1 WHERE visitingorderid=?';
                $adb->pquery($query,array($row['visitingorderid']));
            }
            $query="SELECT userid FROM vtiger_visitsign WHERE issign=1 AND signnum=1 AND visitingorderid=?";
            $visitgnResult=$adb->pquery($query,array($row['visitingorderid']));
            if($adb->num_rows($visitgnResult)){
                while($rowData=$adb->fetch_array($visitgnResult)){
                    $this->setEffectiveVisits($row['visitingorderid'],$rowData['userid']);
                }
            }
        }
    }
    public function getVisitingOrderNum(Vtiger_Request $request){
        $rawData=file_get_contents('php://input');
        $jsonData=(array)json_decode($rawData,true);
        $return=array('success'=>false,'msg'=>'无效参数');
        do{
            if($jsonData['user_id']<=0){
                $return['msg']='无效user_id';
                break;
            }
            $sale_mounth=explode('-',$jsonData['sale_mounth']);
            if(!checkdate($sale_mounth[1],1,$sale_mounth[0])){
                $return['msg']='无效sale_mounth';
                break;
            }
            $firstday = date('Y-m-01', strtotime($jsonData['sale_mounth']));
            $lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day"));
            global $adb;
            $query='SELECT sum(visits_number)/2 as visits_number from (SELECT max(signnum) as visits_number FROM `vtiger_effective_visits` WHERE userid=? AND visit_start_date BETWEEN ? AND ? AND iscomplete=1 GROUP BY accountid) as st';
            $result=$adb->pquery($query,array($jsonData['user_id'],$firstday,$lastday));
            if($adb->num_rows($result)){
                $resultData=$adb->raw_query_result_rowdata($result,0);
            }
            $data=array(
                'new_order_amount'=>10,
                'ahand_amount'=>10,
                'new_order_re_amount'=>10,
                'ahand_re_amount'=>10,
                'new_order_achievement'=>10,
                'ahand_achievement'=>10,
                'saas_new_order_amount'=>10,
                'saas_new_order_re_amount'=>10,
                'saas_order_re_amount'=>10,
                'saas_ahand_re_amount'=>10,
                'user_id'=>$jsonData['user_id'],
                'sale_mounth'=>$jsonData['sale_mounth'],
                'visits_number'=>(float)$resultData['visits_number']
            );
            $return=array('success'=>true,'data'=>$data,'msg'=>'获取成功');
        }while(0);
        return $return;


    }

    public static function todayVisitingNum($userid,$dailydate){
        $db=PearDatabase::getInstance();
        $query="SELECT count(1) FROM `vtiger_accountrankhistory` 
         LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_accountrankhistory.accountid 
         LEFT JOIN vtiger_crmentity ON vtiger_account.accountid=vtiger_crmentity.crmid LEFT JOIN 
         vtiger_visitingorder ON vtiger_account.accountid=vtiger_visitingorder.related_to 
WHERE vtiger_accountrankhistory.newaccountrank='forp_notv' AND vtiger_visitingorder.modulestatus='c_complete' 
  AND vtiger_crmentity.smownerid =? AND vtiger_crmentity.deleted=0 AND left(vtiger_accountrankhistory.createdtime,10)=? GROUP BY vtiger_accountrankhistory.accountid";
        $result = $db->pquery($query,array($userid,$dailydate));
        return $db->num_rows($result)? $db->num_rows($result):0;
    }

    public function canSign(Vtiger_Request $request){
        global $adb;
        $visitingorderid = $request->get('visitingorderid');
        $result = $adb->pquery("select destinationcode,signtime,startdate,enddate,outobjective from vtiger_visitingorder where visitingorderid=? limit 1 ",array($visitingorderid));
        if(!$adb->num_rows($result)){
            return array(array('success'=>'false','msg'=>'查询不到该拜访单'));
        }
        $row = $adb->fetchByAssoc($result,0);
        $distance = $this->getdistance($row['destinationcode'], $request->get("signaddcode"));
        if($distance>1000 && $row['outobjective']!='出差' && !$request->get('unusualsign')){
            return array(array('success'=>false,'msg'=>'签到失败，您还未进入签到地点'));
        }


        $query='SELECT signtime,signnum FROM vtiger_visitsign_mulit WHERE visitingorderid=? AND userid=? AND issign=1 ORDER BY signnum DESC LIMIT 1';
        $result=$adb->pquery($query,array($visitingorderid, $request->get('userid')));
        $data = $adb->fetchByAssoc($result,0);
        if($data['signtime']&& ((time() -strtotime($data['signtime']))<600)){
            return array(array('success'=>false,'msg'=>'签到失败，距上次签到不足10分钟'));
        }

        return array(array('success'=>true,'msg'=>''));
    }

    /**
     * 求两个已知经纬度之间的距离,单位为米
     *
     * @param lng1 $ ,lng2 经度
     * @param lat1 $ ,lat2 纬度
     * @return float 距离，单位米
     * @author www.Alixixi.com
     */
    function getdistance($destioncode,$signcode) {
        $desLngLat = explode("***",$destioncode);
        $signLngLat = explode("***",$signcode);
        $lng1 = $desLngLat[0];
        $lat1 = $desLngLat[1];
        $lng2 = $signLngLat[0];
        $lat2 = $signLngLat[1];
        // 将角度转为狐度
        $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
        return $s;
    }
	function add_VisitingOrder(Vtiger_Request $request){
        //ini_set('display_errors','on'); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        global $adb,$current_user,$currentModule;
        $fieldnamedata=file_get_contents('php://input');
        $fieldname=json_decode($fieldnamedata,true);
        $currentModule = 'VisitingOrder';
        $fieldname['accompany']=str_replace(',',' |##| ', $fieldname['accompany']);
        $fieldname["popupReferenceModule"]		='Workflows';
        $fieldname["workflowsid"]				='400';
        $fieldname["workflowsid_display"]		='拜访单审核流程';
        $userid=$fieldname['userid'];
        $fieldname['module']='VisitingOrder';
        $fieldname['related_to']=is_numeric($fieldname['related_to'])?$fieldname['related_to']:0;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
        include_once('modules/Vtiger/actions/Save.php');
        $save = new Vtiger_Save_Action();
        $_REQUEST['related_to']=$fieldname['related_to'];
        $res  = $save->saveRecord(new Vtiger_Request($fieldname, $fieldname));
        $order_id = $res->getId();
        if($order_id){
            // 更新客户地址
            $sql = "update vtiger_visitingorder set customeraddress=? where visitingorderid=?";
            $adb->pquery($sql, array($fieldname['customeraddress'], $order_id));
            for($i=1; $i<=2; $i++) {
                // 添加到 拜访单签单详情表
                $sql = "INSERT INTO `vtiger_visitsign` (`visitsignid`, `visitingorderid`, `userid`, `visitsigntype`, `signtime`, `signaddress`, `issign`, `signnum`, `coordinate`) VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?);";
                $adb->pquery($sql, array($order_id, $current_user->id, '提单人', '', '', '0', $i, ''));
            }
            $accompany='';
            if ( ! empty($fieldname['accompany'])) {   //陪同人
                $accomptyArr = explode(' |##| ', $fieldname['accompany']);
                $t_arr = array();
                foreach ($accomptyArr as $key => $value) {
                    $t_arr[] = "(null, '$order_id', '$value', '陪同人', '', '', '0', '1', '')";
                    $t_arr[] = "(null, '$order_id', '$value', '陪同人', '', '', '0', '2', '')";
                }
                $sql = "INSERT INTO `vtiger_visitsign` (`visitsignid`, `visitingorderid`, `userid`, `visitsigntype`, `signtime`, `signaddress`, `issign`, `signnum`, `coordinate`) VALUES " . implode(',', $t_arr);
                $adb->pquery($sql, array());
                $query='SELECT GROUP_CONCAT(last_name) FROM vtiger_users WHERE id in('.generateQuestionMarks($accomptyArr).')';
                $result=$adb->pquery($query,$accomptyArr);
                if($adb->num_rows($result)) {
                    $accomptyArrdata = $adb->raw_query_result_rowdata($result, 0);
                    $accompany=$accomptyArrdata[0];
                }
            }
            $adb->pquery("DELETE FROM vtiger_visitsign_mulit WHERE visitingorderid=?",array($order_id));
            $sql='INSERT INTO vtiger_visitsign_mulit(visitingorderid,userid,visitsigntype,signtime,signaddress,issign,signnum,zhsignnum) SELECT visitingorderid,userid,visitsigntype,signtime,signaddress,issign,signnum,if(signnum=1,\'一\',\'二\') FROM vtiger_visitsign WHERE visitingorderid=?';
            $adb->pquery($sql,array($order_id));

            #添加审批流程
            $time = date('Y-m-d H:i:s');
            $workflow = array(
                'salesorderid'			=>$order_id,
                'workflowsid'				=>400,
                'modulename'				=>'VisitingOrder',
                'smcreatorid'				=>$userid,
                'createdtime'				=>$time,
                'ishigher'				=>1,
                'addtime'					=>$time
            );
            $workflow['sequence'] = 1;
            $workflow['workflowstagesname'] = '提单人上级审批';
            $workflow['workflowstagesid'] = 470;
            $workflow['isaction'] =1;
            $workflow['actiontime'] = $time;
            $workflow['higherid'] = $current_user->reports_to_id;

            $res = $adb->pquery("insert into vtiger_salesorderworkflowstages (salesorderid,workflowsid,
				modulename,smcreatorid,createdtime,ishigher,addtime,sequence,workflowstagesname,workflowstagesid,
				isaction,actiontime,higherid) values(?,?,?,?,?,?,?,?,?,?,?,?,?)",
                $workflow);
            //添加拜访跟进中
            $visitaccountcontractid = $adb->getUniqueId('vtiger_visitaccountcontract');
            $adb->pquery('INSERT INTO `vtiger_visitaccountcontract`(visitaccountcontractid,accountid,visitingorderid,vextractid,vaccompany,vstartdate) SELECT ?,vtiger_visitingorder.related_to,vtiger_visitingorder.visitingorderid,vtiger_visitingorder.extractid,vtiger_visitingorder.accompany,vtiger_visitingorder.startdate FROM vtiger_visitingorder WHERE vtiger_visitingorder.related_to>0 AND visitingorderid=?',array($visitaccountcontractid,$order_id));
            $adb->pquery('UPDATE vtiger_crmentity,vtiger_visitingorder SET vtiger_visitingorder.accountnamer=vtiger_crmentity.label,vtiger_visitingorder.modulename=vtiger_crmentity.setype WHERE vtiger_visitingorder.related_to=vtiger_crmentity.crmid AND vtiger_visitingorder.visitingorderid=?',array($order_id));
            $adb->pquery('UPDATE vtiger_salesorderworkflowstages,vtiger_visitingorder SET vtiger_salesorderworkflowstages.accountid=vtiger_visitingorder.related_to,vtiger_salesorderworkflowstages.accountname=(SELECT vtiger_crmentity.label FROM vtiger_crmentity WHERE vtiger_crmentity.crmid=vtiger_visitingorder.related_to) WHERE vtiger_visitingorder.visitingorderid=vtiger_salesorderworkflowstages.salesorderid AND vtiger_salesorderworkflowstages.salesorderid=?',array($order_id));

            $fieldname['visitingorderid']=$order_id;
            if ( ! empty($fieldname['accompany'])) {
                $fieldname['accompany']=explode(' |##| ', $fieldname['accompany']);
            }
            $this->addEffectiveVisits(new Vtiger_Request($fieldname, $fieldname),$userid);
        }
        return array($order_id);
    }

        /**
     *移动端图片上传
     * @param $request
     * @return array
     */
    public function doUploadImg($request){
        $model=$request->get('module');
        $file = $request->get('file');
        $name = $request->get('filename');
//        $files = explode("base64,",$file);
        $filestream = $file;
//        $filestream = $files[1];
        $size = strlen($filestream);

        if($name != '' && $size > 0){
            global $current_user;
            global $upload_badext;
            global $adb;
            $current_id = $adb->getUniqueID("vtiger_files");
            $file_name=preg_replace('/(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+|(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+/u','',$name);
            $binFile = sanitizeUploadFileName($file_name, $upload_badext);
            $uploadfile=time();
            $filename = ltrim(basename(" " . $binFile)); //allowed filename like UTF-8 characters
            $filetype = 'image/jpeg';
            $upload_file_path = decideFilePath();
            file_put_contents($upload_file_path . $current_id . "_" .$uploadfile,base64_decode($filestream));
            if(!file_exists($upload_file_path . $current_id . "_" .$uploadfile)){
                return array('success'=>false,'result'=>array('id'=>$current_id,'name'=>$filename));
            }
            $sizeArray = getimagesize($upload_file_path . $current_id . "_" .$uploadfile);
            $sql2 = "insert into vtiger_files(attachmentsid, name,description, type, path,uploader,uploadtime,newfilename) values(?, ?,?, ?, ?,?,?,?)";
            $params2 = array($current_id, $filename, $model,$filetype, $upload_file_path,$current_user->id,date('Y-m-d H:i:s'),$uploadfile);
            $adb->pquery($sql2, $params2);
            return array('success'=>true,'result'=>array('id'=>$current_id,'name'=>$filename,'width'=>$sizeArray[0],'hight'=>$sizeArray[1]));
        }else{
            return array('success'=>false,'msg'=>'上传失败','name'=>$name,'size'=>$filestream);
        }
    }

    /**
     * 删除图片
     */
    public function deleteFile(Vtiger_Request $request){
        $model=$request->get('module');
        $fileid = $request->get('fileid');
        global $adb;
        $result = $adb->pquery("select * from vtiger_files where attachmentsid=?",array($fileid));
        if($adb->num_rows($result)){
            $row = $adb->fetchByAssoc($result,0);
            $filePath = $row['path'];
            $fileName = $row['name'];
            $savedFile = $fileid."_".base64_encode($fileName);
            if(file_exists($filePath.$savedFile)){
                unlink($filePath.$savedFile);
            }
            $adb->pquery("update vtiger_files set deleter=?,deletertime=?,delflag=1 where attachmentsid=?",array($request->get('userid'),date("Y-m-d H:i:s"),$request->get('fileid')));
        }
        return array('success'=>true,'result'=>array('id'=>$fileid));
    }

    function getFileImg($fileid){
        if($fileid>0){
            global $adb;
            $result = $adb->pquery("SELECT * FROM vtiger_files WHERE attachmentsid=?", array($fileid));
            if($adb->num_rows($result)) {
                $fileDetails = $adb->query_result_rowdata($result);
                $filePath = $fileDetails['path'];
                $fileName = $fileDetails['newfilename'];
                $t_fileName = base64_encode($fileName);
                $t_fileName = str_replace('/', '', $t_fileName);
                $savedFile = $fileDetails['attachmentsid']."_".$t_fileName;
                if(!file_exists($filePath.$savedFile)){
                    $savedFile = $fileDetails['attachmentsid']."_".$fileName;
                }
                $fileSize = filesize($filePath.$savedFile);
                $fileSize = $fileSize + ($fileSize % 1024);

                if (fopen($filePath.$savedFile, "r")) {
                    $fileContent = fread(fopen($filePath.$savedFile, "r"), $fileSize);
                    $sizeArray = getimagesize($filePath.$savedFile);
                    return array(array(1,$fileDetails['type'],$fileName,base64_encode($fileContent),'width'=>$sizeArray[0],'hight'=>$sizeArray[1]));
                }

                return array(array(0,'文件不存在'));
            }else{
                return array(array(0,'文件不存在'));
            }
        }
        return array(array(0,'文件不存在111'));
        exit;
    }


    public function strangeVisitHistory($recordid,$host=''){
        if(!$host){
            $host = $_SERVER['HTTP_HOST'];
        }
        global $adb;
        $sql = "select visitsigntype,signtime,signaddress,unusualsign,unusualremark,a.file,c.last_name,b.accountname,a.userid from vtiger_strangevisit a 
  left join vtiger_account b on a.accountid=b.accountid 
  left join vtiger_users c on c.id=a.userid
where visitingorderid=? ";
        $result = $adb->pquery($sql,array($recordid));
        if(!$adb->num_rows($result)){
            return array();
        }
        while ($row=$adb->fetchByAssoc($result)){
            $visitHistories[] =$row;
        }
        $data = array();
//        $visitHistories = $adb->fetchByAssoc($result);
        foreach ($visitHistories as $visitHistory){
            $AllFileArr =  explode("*|*",$visitHistory['file']);
            foreach ($AllFileArr as $file){
                $fileData[] = explode("##",$file)[1];
            }
            $fileurl = array();
            foreach ($fileData as $fileid){
                if(!$fileid){
                    continue;
                }
                if($host==$_SERVER['HTTP_HOST']){
                    $fileurl[] = 'http://'.$host.'/index.php?module=VisitingOrder&action=ChangeAjax&mode=showImg&filename='.$fileid;
                }else{
                    $fileurl[] = 'http://'.$host.'/index.php?module=VisitingOrder&action=showImg&filename='.$fileid;
                }
            }
            $visitHistory['fileurl'] = $fileurl;
            $data[$visitHistory['userid']][] = $visitHistory;

            unset($fileData);
        }
        return $data;
    }

    public function canStrangeSign(Vtiger_Request $request){
        global $adb;
        $visitingorderid = $request->get('visitingorderid');
        $result = $adb->pquery("select destinationcode,signtime,startdate,enddate,outobjective from vtiger_visitingorder where visitingorderid=? limit 1 ",array($visitingorderid));
        if(!$adb->num_rows($result)){
            return array(array('success'=>false,'errcode'=>0,'msg'=>'查询不到该拜访单'));
        }

        $isHasSql = "select 1 from vtiger_visitsign WHERE visitingorderid=? AND userid=? ";
        $isHasResult = $adb->pquery($isHasSql, array($visitingorderid, $request->get('userid')));
        if(!$adb->num_rows($isHasResult)){
            return array(array('success'=>false,'errcode'=>0,'msg'=>'非提单人和陪同人不允许签到'));
        }

        $row = $adb->fetchByAssoc($result,0);
        $distance = $this->getdistance($row['destinationcode'], $request->get("signaddcode"));
        if($distance>2000 && $row['outobjective']!='出差' && !$request->get('unusualsign')){
            return array(array('success'=>false,'errcode'=>1,'msg'=>'签到失败，您还未进入签到地点'));
        }

        return array(array('success'=>true,'msg'=>''));
    }

    public function doStrangeSign(Vtiger_Request $request){
        $db = PearDatabase::getInstance();
        $result =$db->pquery("select extractid from vtiger_visitingorder where visitingorderid=?",array($request->get("visid")));
        if(!$db->num_rows($result)){
            return array(array('success'=>false,'msg'=>'不存在拜访单'));
        }
        $row = $db->fetchByAssoc($result,0);
        switch ($row['extractid']){
            case $request->get("userid"):
                $visitsigntype = '提单人';
                break;
            default:
                $visitsigntype= '陪同人';
                break;
        }

        $db->pquery("INSERT INTO vtiger_strangevisit( `userid`, `accountid`, `visitingorderid`, `visitsigntype`, `signtime`,`signaddress`, `coordinate`, `unusualsign`, `unusualremark`, `file`) 
                                VALUES (?,?,?,?,?,?,?,?,?,?)",array($request->get("userid"),$request->get("accountid"),
            $request->get("visid"),$visitsigntype,$request->get("time"),$request->get("adname"),
            $request->get("adcode"),$request->get("unusualsign"),$request->get("unusualremark"),$request->get("file")
        ));
        //记录陌拜次数
        $result2 = $db->pquery("select 1 from vtiger_strangevisit where visitingorderid=? group by accountid",array($request->get("visid")));
        if($db->num_rows($result2)){
            $db->pquery("update vtiger_visitingorder set strangvisitnum=? where visitingorderid=?",array($db->num_rows($result2),$request->get("visid")));
        }

        return array(array('success'=>true,'msg'=>''));
    }

    public function isSigned(Vtiger_Request $request){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select 1 from vtiger_visitsign_mulit where userid=? and visitingorderid=? and issign=1",array($request->get("userid"),$request->get("record")));
        if(!$db->num_rows($result)){
            return array(array('success'=>false,'msg'=>'请先去拜访单签到后再陌拜'));
        }
        return array(array('success'=>true,'msg'=>''));
    }


    function isStrangeVisitAccount(Vtiger_Request $request){
        $db = PearDatabase::getInstance();
        $visitingorderid = $request->get('visitingorderid');
        $result = $db->pquery("select  accountid,userid from vtiger_strangevisit where visitingorderid=?",array($visitingorderid));
        if(!$db->num_rows($result)){
            return array(array('success'=>false,'data'=>array()));
        }
        $userid = $request->get("userid");
        $accountIds = array();
        $otherAccountIds = array();
        while ($row =$db->fetchByAssoc($result)){
            if($row['userid']!=$userid){
                $accountIds[] = $row['accountid'];
            }else{
                $otherAccountIds[] = $row['accountid'];
            }
        }
        $otherAccountIds = array_unique($otherAccountIds);
        $accountIds = array_unique($accountIds);
        $data = array_diff($accountIds,$otherAccountIds);
        return array(array('success'=>true,'data'=>$data));
    }
    /**
     * 拜访日期开始前5分钟，企业微信通知提醒提单人，陪同人
     */
    public function sendWechatMessageBYProposerANDAttendant($recordid)
    {
        global $adb,$m_crm_domain_index_url;
        $query='SELECT * FROM vtiger_visitingorder WHERE visitingorderid=?';
        $result=$adb->pquery($query,array($recordid));


        $extractid=$result->fields['extractid'];//提单人
        $accompany=$result->fields['accompany'];//陪同人
        $accompany=str_replace(' |##| ',',',$accompany);
        if(!empty($accompany)){
            $extractid.=','.$accompany;
            $extractid=trim($extractid,',');
        }
        $startdate=$result->fields['startdate'];//开始时间
        $sendtime=date("Y-m-d H:i:s",strtotime($startdate)-300);//提前5分钟提醒

        $startdate=date('Y年m月d日H时i分',strtotime($startdate));
        $enddate=$result->fields['enddate'];//结束时间
        $enddate=date('Y年m月d日H时i分',strtotime($enddate));
        $content='<br>您提交了'.$startdate.'至'.$enddate.'的拜访单<br>请在到达拜访地后进行签到（超出拜访地1KM范围无法签到）<div class=\"highlight\">拜访单若在开始日23:59前未签到将无法生效，请知晓。</div><br>签到方式：<br>点击[移动 ERP]-点击[我的 ERP]-点击[拜访单]-选择相应的拜访单-点击[+]-点击[地点签到，签退]-点击[签到]<br>若有疑问，请立即联系当地人事，谢谢。';
        $query="SELECT vtiger_users.email1,vtiger_users.last_name,id FROM vtiger_users WHERE vtiger_users.`status`='Active' AND isdimission=0 AND vtiger_users.id in(".$extractid.")";
        $userresult=$adb->pquery($query,array());
        $usernum=$adb->num_rows($userresult);
        $dataurl=$m_crm_domain_index_url.'?module=VisitingOrder&action=detail&record='.$recordid;
        $mailcontent='您提交了'.$startdate.'至'.$enddate.'的拜访单<br><br>
            请在到达拜访地后进行签到（超出拜访地1KM范围无法签到）<br><br>
            <font color="#3366ff">拜访单若在开始日23:59前未签到将无法生效，请知晓。</font><br><br>
            签到方式：<br><br>
            点击【移动 ERP】-点击【我的 ERP】-点击【拜访单】-选择<br>相应的拜访单-点击【+】-点击【地点签到，签退】-点击【签到】<br>（注意签到地址必须是在拜访地址的 1KM 内）<br><br>
            <font color="#ff0000">以上邮件为ERP系统发送，请勿回复！若有疑问，请立即联系当地人事，谢谢。</font><br>';
        if($usernum>0){
            while($row=$adb->fetch_array($userresult)){
                $username=$row['last_name'].' 你好';
                $sql='INSERT INTO `vtiger_sendmail` (`sendmailid`,`body`, `assigned_user_email`, `email_flag`, `module`, `createdtime`,sendtime,moduleid) VALUES(?,?,?,?,?,?,?,?)';
                $jsonData=json_encode(array('email'=>trim($row['email1']),'description'=>$username.$content,'dataurl'=>$dataurl,'title'=>'拜访单签到提醒','flag'=>7));
                $jsonData=base64_encode($jsonData);
                $sendmailid=$adb->getUniqueID('vtiger_sendmail');
                $adb->pquery($sql,array($sendmailid,$jsonData,$row['id'],'nosender','sendWechatMessage',date('Y-m-d H:i:s'),$sendtime,$recordid));
                $sendmailid=$adb->getUniqueID('vtiger_sendmail');
                $Subject='拜访单签到提醒';
                $jsonData=json_encode(array('Subject'=>$Subject, 'body'=>'<font size="4">'.$username.'：<br><br>'.$mailcontent.'</font>', 'address'=>array(array('mail' => trim($row['email1']), 'name' =>$row['last_name'])),'fromname'=>'ERP系统','sysid'=>1,'cc'=>array()));
                $jsonData=base64_encode($jsonData);
                $adb->pquery($sql,array($sendmailid,$jsonData,$row['id'],'nosender','sendMailByMessageQuery',date('Y-m-d H:i:s'),$sendtime,$recordid));
            }
        }
    }

        public function getNewVisitingOrderData(){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select subject from vtiger_subject order by sortorderid asc",array());
        while ($row = $db->fetchByAssoc($result)){
            $suject[] = array(
                "key"=>$row['subject'],
                "value"=>$row['subject']
            );
        }

        $result = $db->pquery("select outobjective from vtiger_outobjective order by sortorderid asc",array());
        while ($row = $db->fetchByAssoc($result)){
            $outobjective[] = array(
                "key"=>$row['outobjective'],
                "value"=>$row['outobjective']
            );
        }
        return array("subject"=>$suject,'outobjective'=>$outobjective);
    }

    /**
     * 同步拜访单（拜访中心调用）
     * 拜访中心签退时，调用此接口，可多次签退，即多次调用此接口
     * 拜访单不存在新增，拜访单已存在更新签退记录
     * @param Vtiger_Request $request
     * @return string[]
     */
    public function syncVisitOrder(Vtiger_Request $request)
    {
        $requestData = $request->getAll();
        if (empty($requestData['visitNumber'])) {
            return ['success'=>'false', 'msg'=>'拜访单编号不能为空'];
        }
        $db = PearDatabase::getInstance();
        $sql = "SELECT visitingorderid FROM vtiger_visitingorder WHERE visitnumber=? LIMIT 1";
        $result = $db->pquery($sql, [$requestData['visitNumber']]);
        //判断记录是否已存在
        if ($db->num_rows($result)) {
            /* 更新签退数据 start */
            $visitingOrderData = $db->raw_query_result_rowdata($result,0);
            $visitsign_mulit_sql = "UPDATE vtiger_visitsign_mulit SET userid=?, signtime=?, signaddress=?, coordinate=?, issign=? WHERE visitingorderid=? AND signnum=?";
            //更新签到信息
            $params = [
                $requestData['ladingBy'],
                $requestData['checkInTime'],
                $requestData['checkInAddress'],
                $requestData['checkinLatitudeLongitude'],
                1,
                $visitingOrderData['visitingorderid'],
                1
            ];
            $db->pquery($visitsign_mulit_sql, $params);
            //更新签退信息
            $params = [
                $requestData['ladingBy'],
                $requestData['checkOutTime'],
                $requestData['checkOutAddress'],
                $requestData['checkoutLatitudeLongitude'],
                1,
                $visitingOrderData['visitingorderid'],
                2
            ];
            $db->pquery($visitsign_mulit_sql, $params);
            $visitsign_sql = "UPDATE vtiger_visitsign SET userid=?, signtime=?, signaddress=?, coordinate=?, issign=? WHERE visitingorderid=? AND signnum=?";
            //更新签到信息
            $params = [
                $requestData['ladingBy'],
                $requestData['checkInTime'],
                $requestData['checkInAddress'],
                $requestData['checkinLatitudeLongitude'],
                1,
                $visitingOrderData['visitingorderid'],
                1
            ];
            $db->pquery($visitsign_sql, $params);
            //更新签退信息
            $params = [
                $requestData['ladingBy'],
                $requestData['checkOutTime'],
                $requestData['checkOutAddress'],
                $requestData['checkoutLatitudeLongitude'],
                1,
                $visitingOrderData['visitingorderid'],
                2
            ];
            $db->pquery($visitsign_sql, $params);
            /* 更新签退数据 end */
        } else {
            $current_id = $db->getUniqueID("vtiger_crmentity");
            $crmentity_sql = "insert into vtiger_crmentity(crmid,smcreatorid,smownerid,setype,modifiedby,createdtime,modifiedtime,label) values(?,?,?,?,?,?,?,?)";
            $params = [
                $current_id,
                $requestData['createId'],
                $requestData['createId'],
                'VisitingOrder',
                $requestData['createId'],
                $requestData['createTime'],
                $requestData['createTime'],
                $requestData['visitTitle']
            ];
            $db->pquery($crmentity_sql, $params);
            $visitingorder_sql = "INSERT INTO vtiger_visitingorder(visitingorderid,visitnumber,extractid,auditorid,subject,purpose,remark,startdate,enddate,modulestatus,outobjective,destination,issign,signnum,signid,signtime,signaddress,source) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $params = [
                $current_id,
                $requestData['visitNumber'],
                $requestData['createId'], //提单人
                $requestData['approvalUserId'], //拜访单审核人
                $requestData['visitTitle'],
                $requestData['visitPurpose'],
                $requestData['visitRemark'],
                $requestData['visitStartAt'],
                $requestData['visitEndAt'],
                'c_complete', '拜访',
                $requestData['visitAddress'],
                1,
                2,
                $requestData['ladingBy'],
                $requestData['checkInTime'],
                $requestData['checkInAddress'],
                '拜访中心'
            ];
            //判断打卡人是提单人还是陪同人
            if ($requestData['createId'] == $requestData['ladingBy']) {
                $visitsigntype = '提单人';
            } else {
                $visitsigntype = '陪同人';
            }
            //判断是否签退
            if ($requestData['checkOutTime']) {
                $issignout = 1;
            } else {
                $issignout = 0;
            }
            $db->pquery($visitingorder_sql, $params);
            $visitsign_mulit_sql = "INSERT INTO vtiger_visitsign_mulit(visitingorderid,userid,visitsigntype,signtime,signaddress,coordinate,issign,signnum,zhsignnum) VALUES(?,?,?,?,?,?,?,?,?)";
            //插入签到记录
            $params = [
                $current_id,
                $requestData['ladingBy'],
                $visitsigntype,
                $requestData['checkInTime'],
                $requestData['checkInAddress'],
                $requestData['checkinLatitudeLongitude'],
                1,
                1,
                '一'
            ];
            $db->pquery($visitsign_mulit_sql, $params);
            //插入签退记录
            $params = [
                $current_id,
                $requestData['ladingBy'],
                $visitsigntype,
                $requestData['checkOutTime'],
                $requestData['checkOutAddress'],
                $requestData['checkoutLatitudeLongitude'],
                $issignout,
                2,
                '二'
            ];
            $db->pquery($visitsign_mulit_sql, $params);
            $visitsign_sql = "INSERT INTO vtiger_visitsign(visitingorderid,userid,visitsigntype,signtime,signaddress,coordinate,issign,signnum) VALUES(?,?,?,?,?,?,?,?)";
            //插入签到记录
            $params = [
                $current_id,
                $requestData['ladingBy'],
                $visitsigntype,
                $requestData['checkInTime'],
                $requestData['checkInAddress'],
                $requestData['checkinLatitudeLongitude'],
                1,
                1
            ];
            $db->pquery($visitsign_sql, $params);
            //插入签退记录
            $params = [
                $current_id,
                $requestData['ladingBy'],
                $visitsigntype,
                $requestData['checkOutTime'],
                $requestData['checkOutAddress'],
                $requestData['checkoutLatitudeLongitude'],
                $issignout,
                2
            ];

            $db->pquery($visitsign_sql, $params);
        }
        return ['success'=>'true', 'msg'=>'成功同步拜访单'];
    }

    /**
     * 作废拜访单（拜访中心调用）
     * @param Vtiger_Request $request
     * @return string[]
     */
    public function cancelledVisitOrder(Vtiger_Request $request)
    {
        $visitNumber = $request->get('visitNumber');
        if (empty($visitNumber)) {
            return ['success'=>'false', 'msg'=>'拜访单编号不能为空'];
        }
        $db = PearDatabase::getInstance();
        $sql = "UPDATE vtiger_visitingorder SET modulestatus=? WHERE visitnumber=?";
        $params = [
            'c_cancel',
            $visitNumber
        ];
        $db->pquery($sql, $params);
        return ['success'=>'true', 'msg'=>'成功作废拜访单'];
    }
}
