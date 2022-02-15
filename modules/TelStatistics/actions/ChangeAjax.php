<?php
class TelStatistics_ChangeAjax_Action extends Vtiger_Action_Controller {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('saveUserData');
        $this->exposeMethod('batchSave');
        $this->exposeMethod('getUserdata');
        $this->exposeMethod('getRecordData');
        $this->exposeMethod('changeUserData');
        $this->exposeMethod('geteworkstatisticsfresh');
        $this->exposeMethod('geteworkstatisticsdata');
        $this->exposeMethod('getdetaileworkstatistics');
        $this->exposeMethod('geteworksituationtrendsdata');
        $this->exposeMethod('geteworksituationtrendslist');

    }



	function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if(!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
	}

    /**
     * 获取用户信息
     * @param Vtiger_Request $request
     */
	public function getUserdata(Vtiger_Request $request){
        $telnumberdate=$request->get('telnumberdate');
        $departmentid=$request->get('departmentid');
        global $adb,$current_user;
        $arr['flag']=true;
        do{
            if(!Users_Privileges_Model::isPermitted('TelStatistics', 'EditView')) {
                $arr['flag']=false;
                $arr['msg']='没有权限';
                break;
            }
            $where=getAccessibleUsers('Accounts','List',true);//根据客户权限走不用再另配权限
            $userid=getDepartmentUser($departmentid);
            if($where=='1=1'){
                $where=$userid;
            }else{
                $where=array_intersect($where,$userid);
            }
            if(empty($where)){
                $arr['msg']='没有要填写的人员的信息';
                $arr['flag']=false;
                break;
            }
            $data=array();
            $query="SELECT vtiger_telstatistics.*,id,last_name,vtiger_departments.departmentname AS department 
  FROM vtiger_telstatistics 
  LEFT JOIN vtiger_users on vtiger_telstatistics.useid=vtiger_users.id
  LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id 
  LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid 
WHERE (`status`='Active' or (`status` !='Active' and leavedate>=?))  AND vtiger_telstatistics.telnumberdate=? AND vtiger_telstatistics.useid in(".implode(',',$where).")";
            $result=$adb->pquery($query,array($telnumberdate." 00:00:00",$telnumberdate));
            $userids = array();
            if($adb->num_rows($result)){
                while($row=$adb->fetchByAssoc($result)){
                    $temp=array();
                    $temp['id']=$row['useid'];
                    $temp['username']=$row['last_name']?$row['last_name']:'';
                    $temp['department']=$row['department']?$row['department']:'';
                    $temp['total_telnumber']=$row['total_telnumber']?$row['total_telnumber']:'';
                    $temp['telnumber']=$row['telnumber']?$row['telnumber']:'';
                    $temp['tel_connect_rate']=$row['tel_connect_rate']?$row['tel_connect_rate']:'';
                    $temp['telduration']=$row['telduration']?$row['telduration']:'';
                    $temp['departmentid'] = $row['departmentid']?$row['departmentid']:'';
                    $data[]=$temp;
                    $userids[] = $row['useid'];
                }
            }

            $where = array_diff($where,$userids);
            if(empty($where)){
                $arr['msg']=$adb->num_rows($result);
                $arr['data']=$data;
                break;
            }

            $query="SELECT id,last_name,vtiger_departments.departmentname AS department ,vtiger_departments.departmentid
  FROM vtiger_users 
  LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id 
  LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid 
WHERE (`status`='Active' or (`status` !='Active' and leavedate>=?)) AND id in(".implode(',',$where).") ";
            $result=$adb->pquery($query,array($telnumberdate." 00:00:00"));
            while($row=$adb->fetchByAssoc($result)){
                $temp=array();
                $temp['id']=$row['id'];
                $temp['username']=$row['last_name']?$row['last_name']:'';
                $temp['department']=$row['department']?$row['department']:'';
                $temp['total_telnumber']=$row['total_telnumber']?$row['total_telnumber']:'';
                $temp['telnumber']=$row['telnumber']?$row['telnumber']:'';
                $temp['tel_connect_rate']=$row['tel_connect_rate']?$row['tel_connect_rate']:'';
                $temp['telduration']=$row['telduration']?$row['telduration']:'';
                $temp['departmentid'] = $row['departmentid']?$row['departmentid']:'';
                $data[]=$temp;
            }
            $arr['msg']='';
            $arr['data']=$data;
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($arr);
        $response->emit();
    }

    /**
     * 保存电话量信息
     * @param Vtiger_Request $request
     * @throws AppException
     */
    public function saveUserData(Vtiger_Request $request){
        $telnumberdate=$request->get('telnumberdate');
        $telnumber=$request->get('telnumber');
        $telduration=$request->get('telduration');
        $userid=$request->get('userid');
        $departmentid=$request->get('departmentid');
        $tel_connect_rate = $request->get('tel_connect_rate');
        $total_telnumber = $request->get('total_telnumber');

        global $adb,$current_user;
        $returnData['flag']=false;
        do{
            if(!Users_Privileges_Model::isPermitted('TelStatistics', 'EditView')) {
                $returnData['msg']='没有权限';
                break;
            }
            if($userid<1){
                $returnData['msg']='用户有误！';
                break;
            }
            if($telnumberdate==''){
                $returnData['msg']='日期有误！';
                break;
            }
            if($telnumber<1){
                $returnData['msg']='电话量不能小于1';
                break;
            }
            if($telduration<1){
                $returnData['msg']='电话时长不能小于1';
                break;
            }
//            if($total_telduration<1){
//                $returnData['msg']='总电话时长不能小于1';
//                break;
//            }
            if($total_telnumber<1){
                $returnData['msg']='总电话量不能小于1';
                break;
            }
            if($tel_connect_rate<=0 || $tel_connect_rate>100){
                $returnData['msg'] = '电话接通率须大于0小于100';
                break;
            }
            $userids=getDepartmentUser($departmentid);
            $where=getAccessibleUsers('Accounts','List',true);
            if($where=='1=1'){
                $where=$userids;
            }else{
                $where=array_intersect($where,$userids);
            }
            if(!in_array($userid,$where)){
                $returnData['msg']='权限设置有误！';
                break;
            }
            $query="SELECT telstatisticsid FROM `vtiger_telstatistics` WHERE useid=? AND telnumberdate=?";
            $result=$adb->pquery($query,array($userid,$telnumberdate));
            if($adb->num_rows($result)){
                $sql = "update vtiger_telstatistics set telnumber=?,telduration=?,total_telnumber=?,
                                tel_connect_rate=? where  useid=? AND telnumberdate=?";
                $adb->pquery($sql,array(
                   $telnumber,$telduration,$total_telnumber,$tel_connect_rate,$userid,$telnumberdate
                ));
                $returnData['flag']=true;
                break;
            }
            $sql='INSERT INTO vtiger_telstatistics(useid,telnumberdate,telnumber,telduration,departmentid,smownerid,createdtime,
                                 deleted,total_telnumber,tel_connect_rate) 
SELECT ?,?,?,?,departmentid,?,?,?,?,? FROM vtiger_user2department WHERE userid=?';
            $adb->pquery($sql,array($userid,$telnumberdate,$telnumber,$telduration,$current_user->id,date('Y-m-d H:i:s'),
                0,$total_telnumber,$tel_connect_rate,$userid));
            $returnData['flag']=true;
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($returnData);
        $response->emit();
    }

    /**
     * 电话量保存
     * @param Vtiger_Request $request
     */
    public function getRecordData(Vtiger_Request $request){
        $recordid=$request->get('record');
        global $current_user;
        $returnData['flag']=false;
        do{
            if(!Users_Privileges_Model::isPermitted('TelStatistics', 'EditView')) {
                $returnData['msg']='没有权限';
                break;
            }
            if($recordid<1){
                $returnData['msg']='数据有误！';
                break;
            }
            $recordModel=Vtiger_Record_Model::getInstanceById($recordid,'TelStatistics');
            $smownerid=$recordModel->get('smownerid');
            $reportsModel = Users_Privileges_Model::getInstanceById($smownerid);
//            if($current_user->id!=$reportsModel->reports_to_id){
//                $returnData['msg']='只有创建人上级才能修改！';
//                break;
//            }
            $createdtime=$recordModel->get('createdtime');
            $createdtime=substr($createdtime,0,10);
            $createdtime=str_replace('-','',$createdtime);
            $createdtime+=1;
            $currentDate=date('Ymd');
//            if($currentDate>$createdtime){
//                $returnData['msg']='超时不允许修改！';
//                break;
//            }
            $returnData['flag']=true;
            $returnData['data']=array(
                'telnumber'=>$recordModel->get('telnumber'),
                'telduration'=>$recordModel->get('telduration'),
                'total_telnumber'=>$recordModel->get('total_telnumber'),
                'tel_connect_rate'=>$recordModel->get('tel_connect_rate'),
                );
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($returnData);
        $response->emit();
    }

    /**
     * 修改电话量数据
     * @param Vtiger_Request $request
     */
    public function changeUserData(Vtiger_Request $request){
        $recordId=$request->get('record');
        $telnumber=$request->get('telnumber');
        $telduration=$request->get('telduration');
        $total_telnumber = $request->get('total_telnumber');
        $tel_connect_rate = round(($telnumber/$total_telnumber)*100,2);
        global $adb,$current_user;
        $returnData['flag']=false;
        do{
            if(!Users_Privileges_Model::isPermitted('TelStatistics', 'EditView')){
                $returnData['msg']='没有权限';
                break;
            }
            if(!is_numeric($telnumber)){
                $returnData['msg']='电话量不是有效的数字';
                break;
            }
            if(!is_numeric($telduration)){
                $returnData['msg']='电话时长不是有效的数字';
                break;
            }
            if($telnumber<0){
                $returnData['msg']='电话量不能小于0';
                break;
            }
            if($telduration<0){
                $returnData['msg']='电话时长不能小于0';
                break;
            }

            if($total_telnumber<1){
                $returnData['msg']='总电话量不能小于1';
                break;
            }
            if($tel_connect_rate<=0 || $tel_connect_rate>100){
                $returnData['msg'] = '电话接通率须大于0小于100';
                break;
            }
            $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'TelStatistics');
            $smownerid=$recordModel->get('smownerid');
            $reportsModel = Users_Privileges_Model::getInstanceById($smownerid);
//            if($current_user->id!=$reportsModel->reports_to_id){
//                $returnData['msg']='只有创建人上级才能修改！';
//                break;
//            }
            $createdtime=$recordModel->get('createdtime');
            $createdtime=substr($createdtime,0,10);
            $createdtime=str_replace('-','',$createdtime);
            $createdtime+=1;
            $currentDate=date('Ymd');
//            if($currentDate>$createdtime){
//                $returnData['msg']='超时不允许修改！';
//                break;
//            }
            $sql='UPDATE vtiger_telstatistics SET telduration=?,telnumber=?,modifiedby=?,modifiedtime=?,total_telnumber=?,
                                tel_connect_rate=? WHERE telstatisticsid=?';
            $adb->pquery($sql,array($telduration,$telnumber,$current_user->id,date('Y-m-d H:i:s'),$total_telnumber,
                $tel_connect_rate,$recordId));
            $returnData['flag']=true;
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($returnData);
        $response->emit();
    }

    /**
     * 报表基础数据更新
     * @throws Exception
     */
    public function geteworkstatisticsfresh(){
        ignore_user_abort(true);//浏览器关闭后脚本还执行
        set_time_limit(0);
        $db=PearDatabase::getInstance();
        $query="SELECT refreshtime FROM `vtiger_refreshtime` WHERE module='eworkstatistics' limit 1";
        $result=$db->pquery($query,array());
        $resulttime=$db->query_result($result,0,'refreshtime');
        $nowtime=time();
//        $interval=30*60;//间隔时间
        $interval=0;
        $result1=array();
        $lastThreeYear = date("Y-01-01",strtotime("-3 year"));
        if($nowtime-$resulttime>$interval){
            /**STRAT新签客户数**/
            $db->pquery("TRUNCATE TABLE vtiger_signaccount",array());
            $db->pquery("INSERT INTO vtiger_signaccount(userid,
                        servicecontractsid,
                        scalling,
                        edate
                        ) SELECT 
                        divide.receivedpaymentownid AS userid,
                        divide.servicecontractid,
                        divide.scalling,
                        left(signdate,10) AS edate
                        FROM 
                        (SELECT receivedpaymentownid,servicecontractid,scalling
                        FROM vtiger_servicecontracts_divide AS a WHERE
                        servicecontractid>0 AND
                        receivedpaymentownid>0 AND
                        (SELECT  COUNT(1) FROM vtiger_servicecontracts_divide AS b WHERE b.servicecontractid = a.servicecontractid AND b.scalling >= a.scalling) <= 2
                        ORDER BY a.servicecontractid ASC , a.scalling DESC) AS divide
                        LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=divide.servicecontractid LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid=vtiger_crmentity.crmid 
                        WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.modulestatus='c_complete' AND vtiger_servicecontracts.signdate!='' AND vtiger_servicecontracts.signdate IS NOT NULL AND signdate>?",array($lastThreeYear));
            /***END新签客户数**/
            /**STRAT新增客户数**/
            $db->pquery("TRUNCATE TABLE vtiger_addacounts",array());
            $db->pquery("INSERT INTO vtiger_addacounts(userid,
                        accountid,
                        edate
                        ) SELECT smcreatorid,
                        crmid,
                        left(createdtime,10)
                         FROM vtiger_crmentity WHERE setype='Accounts' AND deleted=0 AND createdtime>?",array($lastThreeYear));
            /***END新增客户数**/
            /***STRAT划转客户**/
            $db->pquery("TRUNCATE TABLE vtiger_transferaccount",array());
            $db->pquery("INSERT INTO vtiger_transferaccount(userid,
                            accountid,
                            edate
                            )
                            SELECT newsmownerid AS userid,
                            accountid,
                            left(createdtime,10) AS edate
                            FROM vtiger_accountsmowneridhistory WHERE accountid>0 AND newsmownerid!=modifiedby AND createdtime>? GROUP BY left(createdtime,10),accountid",array($lastThreeYear));
            /***END划转客户**/
            /***STRAT公海领取客户**/
            $db->pquery("TRUNCATE TABLE vtiger_highseaaccount",array());
            $db->pquery("INSERT INTO vtiger_highseaaccount(userid,
                            accountid,
                            edate
                            )
                            SELECT smownerid AS userid,
                            accountid,
                            left(createdtime,10) AS edate
                            FROM vtiger_accountsfromtemporary WHERE accountid>0 AND createdtime>?  GROUP BY left(createdtime,10),accountid",array($lastThreeYear));

            /***END公海领取客户**/
            /***STRAT陪访客户数**/
            $db->pquery("TRUNCATE TABLE vtiger_accompanyingvisits",array());
            $db->pquery("INSERT INTO vtiger_accompanyingvisits(userid,
                            visitingorderid,
                            edate
                            )
                            SELECT SUBSTRING_INDEX(accompany,' |##| ',1) AS userid,
                            visitingorderid,
                            left(startdate,10) AS edate
                            
                            FROM vtiger_visitingorder LEFT JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid=vtiger_crmentity.crmid 
                            WHERE deleted=0 AND modulestatus='c_complete' AND accompany IS NOT NULL AND accompany!='' AND SUBSTRING_INDEX(accompany,' |##| ',1)!='' AND extractid!=SUBSTRING_INDEX(accompany,' |##| ',1)  AND related_to>0 AND extractid>0 AND startdate>? GROUP BY related_to,left(startdate,10)",array($lastThreeYear));
            /***END陪访客户数**/

//            /***STRAT新开拜访数**/
            $db->pquery("TRUNCATE TABLE vtiger_newvisitingnum",array());
            $db->pquery("INSERT INTO vtiger_newvisitingnum(userid,
                            visitingorderid,
                            edate
                            )
                            SELECT a.extractid AS userid,
                            a.visitingorderid,
                            left(b.createdtime,10) AS edate
                            FROM vtiger_visitingorder a 
                            left join vtiger_crmentity b on b.crmid = a.visitingorderid
                            WHERE  a.modulestatus in('c_complete','a_normal') AND  a.related_to>0 AND 
                                  a.extractid>0 AND a.isfirstvisit=1 AND b.createdtime>? GROUP BY a.related_to,left(b.createdtime,10)",array($lastThreeYear));
            /***END新开拜访数**/

            /***STRAT审核次数**/
            $db->pquery("TRUNCATE TABLE vtiger_verifynum",array());
            $db->pquery("INSERT INTO vtiger_verifynum(userid,
                             salesorderworkflowstagesid,
                             edate
                             )
                             SELECT 
                             auditorid AS userid,
                            salesorderworkflowstagesid,
                            left(auditortime,10) AS edate
                            FROM vtiger_salesorderworkflowstages
                            WHERE isaction=2 AND auditortime>?",array($lastThreeYear)
            );
            /***END审核次数**/

            /***STRAT跟进客户数**/
            $db->pquery("TRUNCATE TABLE vtiger_followupaccountnum",array());
            $db->pquery("INSERT INTO vtiger_followupaccountnum(userid,
                             modcommentsid,
                             accountid,
                             edate
                             )
                             SELECT 
                             creatorid AS userid,
                            modcommentsid,
                            related_to as accountid,
                            left(addtime,10) AS edate
                            FROM vtiger_modcomments
                            WHERE addtime>?
                            group by edate,accountid,userid order by addtime desc",array($lastThreeYear)
            );
            /***END跟进客户数**/

            /***STRAT评论客户数**/
            $db->pquery("TRUNCATE TABLE vtiger_commentaccountnum",array());
            $db->pquery("INSERT INTO vtiger_commentaccountnum(userid,
                             submodcommentsid,
                             accountid,
                             edate
                             )
                             SELECT 
                             a.creatorid AS userid,
                            a.modcommentsid,
                            b.related_to as accountid,
                            left(a.createdtime,10) AS edate
                            FROM vtiger_submodcomments a left join vtiger_modcomments b 
                            on a.modcommentsid=b.modcommentsid
                            where a.createdtime>?
                            group by edate,accountid order by createdtime desc",array($lastThreeYear)
            );
            /***END评论客户数**/


            /***STRAT总表**/
            $db->pquery("TRUNCATE TABLE vtiger_eworkstatistics",array());
            $db->pquery("INSERT INTO vtiger_eworkstatistics(
                            userid,
                            edate,
                            telnumber,
                            total_telnumber,
                            tel_connect_rate,
                            telduration,
                            addacounts,
                            transferaccount,
                            highseaaccount,
                            billvisits,
                            numbervisitors,
                            accompanyingvisits,
                            nactualvisitors,
                            signaccount,
                            amountpaid,
                            verifynum,
                            newvisitingnum,
                            commentaccountnum,
                            followupaccountnum,
                            enterednum,
                            department
                            )
                            SELECT 
                            stemp.userid,
                            edate,
                            sum(telnumber),
                            sum(total_telnumber),
                            sum(telnumber)/sum(total_telnumber),
                            sum(telduration),
                            sum(addacounts),
                            sum(transferaccount),
                            sum(highseaaccount),
                            sum(billvisits),
                            sum(numbervisitors),
                            sum(accompanyingvisits),
                            sum(numbervisitors+accompanyingvisits),
                            sum(signaccount),
                            sum(amountpaid),
                            sum(verifynum),
                            sum(newvisitingnum),
                            sum(commentaccountnum),
                            sum(followupaccountnum),
                            if(user_entered IS NULL,13,TIMESTAMPDIFF(MONTH,if(day(user_entered)>15,DATE_ADD( DATE_ADD(user_entered,interval -day(user_entered)+1 day), interval +1 month),user_entered),edate)),
                            SUBSTRING_INDEX(vtiger_departments.parentdepartment,'::' ,- 2)
                            from (
							SELECT useid AS userid,
                            vtiger_telstatistics.telnumberdate AS edate,
                            telnumber,
                            total_telnumber,
                            telnumber/total_telnumber,
                            telduration,
                            0 AS addacounts,
                            0 AS transferaccount,
                            0 AS highseaaccount,
                            0 AS billvisits,
                            0 AS numbervisitors,
                            0 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            0 AS signaccount,
                            0 AS amountpaid,
                            0 AS verifynum,
                            0 AS newvisitingnum,
                            0 AS commentaccountnum,
                            0 AS followupaccountnum
                             FROM vtiger_telstatistics 
                             where vtiger_telstatistics.telnumberdate>'{$lastThreeYear}'
                            UNION ALL 
                            
                            SELECT userid,
                             edate,
                            0 AS telnumber,
                            0 AS total_telnumber,
                            0 AS tel_connect_rate,
                            0 AS telduration,
                            count(1) AS addacounts,
                            0 AS transferaccount,
                            0 AS highseaaccount,
                            0 AS billvisits,
                            0 AS numbervisitors,
                            0 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            0 AS signaccount,
                            0 AS amountpaid ,
                            0 AS verifynum,
                            0 AS newvisitingnum,
                            0 AS commentaccountnum,
                            0 AS followupaccountnum
                            FROM vtiger_addacounts  
                            GROUP BY userid,edate
                            UNION ALL 
                            
                            SELECT userid,
                             edate,
                            0 AS telnumber,
                            0 AS total_telnumber,
                            0 AS tel_connect_rate,
                            0 AS telduration,
                            0 AS addacounts,
                            count(1) AS transferaccount,
                            0 AS highseaaccount,
                            0 AS billvisits,
                            0 AS numbervisitors,
                            0 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            0 AS signaccount,
                            0 AS amountpaid,
                            0 AS verifynum,
                            0 AS newvisitingnum,
                            0 AS commentaccountnum,
                            0 AS followupaccountnum
                             FROM vtiger_transferaccount 
                             GROUP BY userid,edate
                            UNION ALL
                            
                            SELECT userid,
                             edate,
                            0 AS telnumber,
                            0 AS total_telnumber,
                            0 AS tel_connect_rate,
                            0 AS telduration,                            
                            0 AS addacounts,
                            0 AS transferaccount,
                            count(1) AS highseaaccount,
                            0 AS billvisits,
                            0 AS numbervisitors,
                            0 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            0 AS signaccount,
                            0 AS amountpaid,
                            0 AS verifynum,
                            0 AS newvisitingnum,
                            0 AS commentaccountnum,
                            0 AS followupaccountnum
                             FROM vtiger_highseaaccount GROUP BY userid,edate
                            UNION ALL
                            SELECT extractid AS userid,
                            left(vtiger_crmentity.createdtime,10) AS edate,
                            0 AS telnumber,
                            0 AS total_telnumber,                            
                            0 AS tel_connect_rate,
                            0 AS telduration,
                            0 AS addacounts,
                            0 AS transferaccount,
                            0 AS highseaaccount,
                            sum(1) AS billvisits,
                            0 AS numbervisitors,
                            0 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            0 AS signaccount,
                            0 AS amountpaid,
                            0 AS verifynum,
                            0 AS newvisitingnum,
                            0 AS commentaccountnum,
                            0 AS followupaccountnum
                            FROM vtiger_visitingorder LEFT JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid=vtiger_crmentity.crmid 
                            WHERE deleted=0 AND extractid>0 AND related_to>0 AND vtiger_crmentity.createdtime>'{$lastThreeYear}' GROUP BY extractid,left(vtiger_crmentity.createdtime,10)
                            UNION ALL
                            SELECT extractid AS userid,
                            left(startdate,10) AS edate,
                            0 AS telnumber,
                            0 AS total_telnumber,
                            0 AS tel_connect_rate,
                            0 AS telduration,
                            0 AS addacounts,
                            0 AS transferaccount,
                            0 AS highseaaccount,
                            0 AS billvisits,
                            sum(if((accompany IS NULL OR accompany=''),1,0.5)) AS numbervisitors,
                            0 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            0 AS signaccount,
                            0 AS amountpaid,
                            0 AS verifynum,
                            0 AS newvisitingnum,
                            0 AS commentaccountnum,
                            0 AS followupaccountnum
                            FROM vtiger_visitingorder LEFT JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid=vtiger_crmentity.crmid 
                            WHERE deleted=0 AND extractid>0 AND related_to>0 AND modulestatus='c_complete'  AND startdate>'{$lastThreeYear}' GROUP BY extractid,left(startdate,10)
                            UNION ALL
                            SELECT userid,
                            edate,
                            0 AS telnumber,
                            0 AS total_telnumber,
                            0 AS tel_connect_rate,
                            0 AS telduration,
                            0 AS addacounts,
                            0 AS transferaccount,
                            0 AS highseaaccount,
                            0 AS billvisits,
                            0 AS numbervisitors,
                            sum(1)/2 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            0 AS signaccount,
                            0 AS amountpaid,
                            0 AS verifynum,
                            0 AS newvisitingnum,
                            0 AS commentaccountnum,
                            0 AS followupaccountnum
                            FROM vtiger_accompanyingvisits GROUP BY userid,edate
                            UNION ALL
                            
                            
                            SELECT 
                            userid,
                            edate,
                            0 AS telnumber,
                            0 AS total_telnumber,
                            0 AS tel_connect_rate,
                            0 AS telduration,
                            0 AS addacounts,
                            0 AS transferaccount,
                            0 AS highseaaccount,
                            0 AS billvisits,
                            0 AS numbervisitors,
                            0 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            sum(if(scalling=100,1,0.5)) AS signaccount,
                            0 AS amountpaid,
                            0 AS verifynum,
                            0 AS newvisitingnum,
                            0 AS commentaccountnum,
                            0 AS followupaccountnum
                            FROM 
                            vtiger_signaccount
                            GROUP BY userid,edate
                            
                            UNION ALL
                            SELECT 
                            receivedpaymentownid AS userid,
                            left(vtiger_receivedpayments.reality_date,10) AS edate,
                            0 AS telnumber,
                            0 AS total_telnumber,
                            0 AS tel_connect_rate,
                            0 AS telduration,
                            0 AS addacounts,
                            0 AS transferaccount,
                            0 AS highseaaccount,
                            0 AS billvisits,
                            0 AS numbervisitors,
                            0 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            0 AS signaccount,
                            sum(businessunit) AS amountpaid ,
                            0 AS verifynum,
                            0 AS newvisitingnum,
                            0 AS commentaccountnum,
                            0 AS followupaccountnum
                            FROM vtiger_achievementallot 
                            LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid
							WHERE vtiger_achievementallot.receivedpaymentownid>0 AND  vtiger_receivedpayments.reality_date >'{$lastThreeYear}'
							GROUP BY vtiger_achievementallot.receivedpaymentownid,vtiger_receivedpayments.reality_date
                            
                             
                            UNION ALL
                            SELECT 
                            userid,
                            edate,
                            0 AS telnumber,
                            0 AS total_telnumber,
                            0 AS tel_connect_rate,
                            0 AS telduration,
                            0 AS addacounts,
                            0 AS transferaccount,
                            0 AS highseaaccount,
                            0 AS billvisits,
                            0 AS numbervisitors,
                            0 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            0 AS signaccount,
                            0 AS amountpaid,
                            count(*) AS verifynum,
                            0 AS newvisitingnum,
                            0 AS commentaccountnum,
                            0 AS followupaccountnum
                            FROM 
                            vtiger_verifynum
                            GROUP BY userid,edate
                            
                                                        
                            UNION ALL
                            SELECT 
                            userid,
                            edate,
                            0 AS telnumber,
                            0 AS total_telnumber,
                            0 AS tel_connect_rate,
                            0 AS telduration,
                            0 AS addacounts,
                            0 AS transferaccount,
                            0 AS highseaaccount,
                            0 AS billvisits,
                            0 AS numbervisitors,
                            0 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            0 AS signaccount,
                            0 AS amountpaid,
                            0 AS verifynum,
                            count(*) AS newvisitingnum,
                            0 AS commentaccountnum,
                            0 AS followupaccountnum
                            FROM 
                            vtiger_newvisitingnum
                            GROUP BY userid,edate
                            
                                                        
                            UNION ALL
                            SELECT 
                            userid,
                            edate,
                            0 AS telnumber,
                            0 AS total_telnumber,
                            0 AS tel_connect_rate,
                            0 AS telduration,
                            0 AS addacounts,
                            0 AS transferaccount,
                            0 AS highseaaccount,
                            0 AS billvisits,
                            0 AS numbervisitors,
                            0 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            0 AS signaccount,
                            0 AS amountpaid,
                            0 AS verifynum,
                            0 AS newvisitingnum,
                            count(*) AS commentaccountnum,
                            0 AS followupaccountnum
                            FROM 
                            vtiger_commentaccountnum
                            GROUP BY userid,edate
                            
                            
                                                       
                            UNION ALL
                            SELECT 
                            userid,
                            edate,
                            0 AS telnumber,
                            0 AS total_telnumber,
                            0 AS tel_connect_rate,
                            0 AS telduration,
                            0 AS addacounts,
                            0 AS transferaccount,
                            0 AS highseaaccount,
                            0 AS billvisits,
                            0 AS numbervisitors,
                            0 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            0 AS signaccount,
                            0 AS amountpaid,
                            0 AS verifynum,
                            0 AS newvisitingnum,
                            0 AS commentaccountnum,
                            count(*) AS followupaccountnum
                            FROM 
                            vtiger_followupaccountnum
                            GROUP BY userid,edate
                            ) as stemp 
                            LEFT JOIN vtiger_users ON vtiger_users.id=stemp.userid 
                            LEFT JOIN vtiger_user2department ON vtiger_user2department.userid = vtiger_users.id
                            LEFT JOIN vtiger_departments ON vtiger_departments.departmentid = vtiger_user2department.departmentid
                            GROUP BY userid,edate",array());
            /***END总表**/

            $nowtime=time();
            $db->pquery("replace into vtiger_refreshtime(refreshtime,module) VALUES(?,?)",array($nowtime,'eworkstatistics'));
            $result1['msg']='更新完成......';
        }else{
            $interval=30-ceil(($nowtime-$resulttime)/60);
            if(floor($interval/60)==0){
                $result1['msg']="请在{$interval}分钟后再更新";
            }else{
                $result1['msg']="请在".floor($interval/60)."小时".($interval%60)."分钟后再更新";
            }
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($result1);
        $response->emit();
    }

    /**
     * 报表数据显示
     * @param Vtiger_Request $request
     */
    public function geteworkstatisticsdata(Vtiger_Request $request){
        ini_set('memory_limit','1024M');
        $data=$this->getwstsdata($request);
        if(!empty($data)){
            require('crmcache/indicatorsetting.php');
            $array=$data['data'];
            $userdata=$data['userdata'];
            $startdate=$request->get('datetime');
            $enddate=$request->get('enddatetime');
            $startdate=str_replace('-','',$startdate);
            $enddate=str_replace('-','',$enddate);
            //$holiday=$userdata['holiday'];
            if($startdate>$enddate){
                $temp=$startdate;
                $startdate=$enddate;
                $enddate=$temp;
            }
            $text='';
            $cachedepartment=getDepartmentName();
            foreach($userdata['pdepartment'] as $key1=>$pdepartment){
                $ydepartnamestr='<td rowspan="ynamedepart">'.$cachedepartment[$key1].'</td>';
                $chang_yderpart=0;
                $campColumnNum = 0;
                $columns = array(
                    "total_telnumber","total_total_telnumber","total_tel_connect_rate","total_telduration","total_addacounts","total_transferaccount",
                    "total_highseaaccount","total_billvisits","total_numbervisitors","total_accompanyingvisits","total_nactualvisitors","total_signaccount",
                    "total_amountpaid","total_verifynum","total_newvisitingnum","total_commentaccountnum","total_followupaccountnum","total_enterednum"
                );
                foreach ($columns as $column){
                    $$column = 0;
                }
                foreach($pdepartment as $value2){
                    $bdepartnamestr='<td rowspan="bnamedepart">'.$cachedepartment[$value2].'</td>';
                    $change_mderpart=0;
                    foreach($userdata[$value2] as $userdatet=>$userdatavalue){
                        $userdate=explode('-',$userdatet);
                        if (!function_exists('cal_days_in_month'))
                        {
                            function cal_days_in_month($calendar, $month, $year)
                            {
                                return date('t', mktime(0, 0, 0, $month, 1, $year));
                            }
                        }
                        $daysmonth=cal_days_in_month(CAL_GREGORIAN,$userdate[1],$userdate[0]);
                        $departuser=$userdatavalue['userdata'];
                        $currentdatenoformat=str_replace('-','',$userdatet);
                        $columns = array(
                            "telnumber","total_telnumber","tel_connect_rate","telduration","addacounts","transferaccount",
                            "highseaaccount","billvisits","numbervisitors","accompanyingvisits","nactualvisitors","signaccount",
                            "amountpaid","verifynum","newvisitingnum","commentaccountnum","followupaccountnum","enterednum"
                        );
                        foreach ($columns as $column){
                            $$column = 0;
                        }
                        $columnname=0;
                        for($d=1;$d<=$daysmonth;$d++){
                            $currentdate=$userdatet.'-'.($d>9?$d:'0'.$d);
                            $currentdatenum=$currentdatenoformat.($d>9?$d:'0'.$d);
                            if($startdate>$currentdatenum|| $enddate<$currentdatenum){
                                continue;
                            }
                            /*if(in_array($currentdate,$holiday)){
                                continue;
                            }*/
                            $changd=0;
                            foreach($departuser as $key=>$value3){
                                //todo
                                //过滤该部门下的领导层 【商务总监、商务经理、商务助理、销售体系部门负责人/总监、 许可副总裁助理】
                                if(strtotime($currentdate)<strtotime($value3['entered']) || ($value3['leavedate']&&strtotime($currentdate)>strtotime($value3['leavedate']))){
                                    continue;
                                }
                                $ydepartnamestr=$chang_yderpart==0?$ydepartnamestr:'';
                                $bdepartnamestr=$change_mderpart==0?$bdepartnamestr:'';
                                $changdstr=$changd==0?'<td rowspan="changedaynum">'.$currentdate.'</td>':'';
                                $entered=explode('-',$value3['entered']);
                                if($entered[2]>15){
                                    $entered[1]=$entered[1]+1;
                                    if($entered[1]<13){
                                        $enteredday=$entered[0].'-'.$entered[1].'-01';
                                    }else{
                                        $enteredday=($entered[0]+1).'-01-01';
                                    }
                                }else{
                                    $enteredday=$entered[0].'-'.$entered[1].'-01';;
                                }

                                $currentDiffMonth=$this->getMonthNum($enteredday,$currentdate);
                                $valueecho=$array[$key1][$value2][$currentdate][$value3['userid']];
                                if(!empty($valueecho)){
                                    //判定是否达标
                                    $valueecho['is_pass'] = TelStatistics_Record_Model::isReachStandard($indicatorsetting,$specialoperation,$value2, $valueecho, $currentDiffMonth);
                                    //如果没有则将结果插入到表中
                                    $adb = PearDatabase::getInstance();
                                    $sql = 'update vtiger_eworkstatistics set is_pass =? where eworkstatisticsid=?';
                                    $adb->pquery($sql,array($valueecho['is_pass'],$valueecho['eworkstatisticsid']));

                                    $matchingStandard = TelStatistics_Record_Model::matchingStandard($value2,$currentDiffMonth,$valueecho);
                                    $countmatchingstandard= count($matchingStandard);
                                    $verifynum += $valueecho['verifynum'];
                                    $commentaccountnum += $valueecho['commentaccountnum'];
                                    $followupaccountnum += $valueecho['followupaccountnum'];
                                    $telnumber += $valueecho['telnumber'];
                                    $tel_connect_rate += $valueecho['tel_connect_rate'];
                                    $telduration += $valueecho['telduration'];
                                    $addacounts += $valueecho['addacounts'];
                                    $transferaccount += $valueecho['transferaccount'];
                                    $highseaaccount += $valueecho['highseaaccount'];
                                    $billvisits += $valueecho['billvisits'];
                                    $newvisitingnum += $valueecho['newvisitingnum'];
                                    $numbervisitors += $valueecho['numbervisitors'];
                                    $accompanyingvisits += $valueecho['accompanyingvisits'];
                                    $nactualvisitors += $valueecho['nactualvisitors'];
                                    $signaccount += $valueecho['signaccount'];
                                    $amountpaid += $valueecho['amountpaid'];


                                    $total_verifynum += $valueecho['verifynum'];
                                    $total_commentaccountnum += $valueecho['commentaccountnum'];
                                    $total_followupaccountnum += $valueecho['followupaccountnum'];
                                    $total_telnumber += $valueecho['telnumber'];
                                    $total_tel_connect_rate += $valueecho['tel_connect_rate'];
                                    $total_telduration += $valueecho['telduration'];
                                    $total_addacounts += $valueecho['addacounts'];
                                    $total_transferaccount += $valueecho['transferaccount'];
                                    $total_highseaaccount += $valueecho['highseaaccount'];
                                    $total_billvisits += $valueecho['billvisits'];
                                    $total_newvisitingnum += $valueecho['newvisitingnum'];
                                    $total_numbervisitors += $valueecho['numbervisitors'];
                                    $total_accompanyingvisits += $valueecho['accompanyingvisits'];
                                    $total_nactualvisitors += $valueecho['nactualvisitors'];
                                    $total_signaccount += $valueecho['signaccount'];
                                    $total_amountpaid += $valueecho['amountpaid'];

                                    $text.='<tr class="text-center">
                                    '.$ydepartnamestr.$bdepartnamestr.$changdstr.'
                                    <td class="username" data-username="'.$value3['username'].'">'.$value3['username'].'</td>
                                    <td >'.$value3['entered'].'</td>
                                    <td >'.($currentDiffMonth>=0?
                                            ($currentDiffMonth>1?
                                                ($currentDiffMonth>3?
                                                    ($currentDiffMonth>6?
                                                        ($currentDiffMonth>12?'12个月以上':'6~12个月')
                                                        :'3~6个月')
                                                    :'1~3个月')
                                                :'1个月内')
                                            :'12个月以上').'</td>
                                     
                                     
                                     
                                     
           
                                            
                                    
                                    <td style="cursor:pointer;" '.($valueecho['verifynum']>0?'class="getdetaileworkstatistics" clickfalg="1" data-module="verifynum" data-uid="'.base64_encode('verifynum#'.$valueecho['userid'].'#'.$valueecho['edate']).'"':'').'>'.$valueecho['verifynum'].'</td>
                                    <td style="cursor:pointer;" '.($valueecho['commentaccountnum']>0?'class="getdetaileworkstatistics" clickfalg="1" data-module="commentaccountnum" data-uid="'.base64_encode('commentaccountnum#'.$valueecho['userid'].'#'.$valueecho['edate']).'"':'').'>'.$valueecho['commentaccountnum'].'</td>
                                    <td style="cursor:pointer;" '.($valueecho['followupaccountnum']>0?'class="getdetaileworkstatistics" clickfalg="1" data-module="followupaccountnum" data-uid="'.base64_encode('followupaccountnum#'.$valueecho['userid'].'#'.$valueecho['edate']).'"':'').'>'.$valueecho['followupaccountnum'].'</td>

                                    <td  class="edate" data-edate="'.$currentdate.'" style="'.($countmatchingstandard?($matchingStandard['telnumber']?'color:green;':'color:red;'):'').'">'.$valueecho['telnumber'].'</td>
                                    <td  class="edate" data-edate="'.$currentdate.'">'.$valueecho['tel_connect_rate'].'</td>
                                    <td style="'.($countmatchingstandard?($matchingStandard['telduration']?'color:green;':'color:red;'):'').'">'.$valueecho['telduration'].'</td>
                                    <td style="cursor:pointer;'.($countmatchingstandard?($matchingStandard['intended_number']?'color:green;':'color:red;'):'').'" '.($valueecho['addacounts']>0?'class="getdetaileworkstatistics" clickfalg="1" data-module="" data-uid="'.base64_encode('addacounts#'.$valueecho['userid'].'#'.$valueecho['edate']).'"':'').'>'.$valueecho['addacounts'].'</td>
                                    <td style="cursor:pointer;'.($countmatchingstandard?($matchingStandard['intended_number']?'color:green;':'color:red;'):'').'" '.($valueecho['transferaccount']>0?'class="getdetaileworkstatistics" clickfalg="1" data-module=""  data-uid="'.base64_encode('transferaccount#'.$valueecho['userid'].'#'.$valueecho['edate']).'"':'').'>'.$valueecho['transferaccount'].'</td>
                                    <td style="cursor:pointer;'.($countmatchingstandard?($matchingStandard['intended_number']?'color:green;':'color:red;'):'').'" '.($valueecho['highseaaccount']>0?'class="getdetaileworkstatistics" clickfalg="1" data-module=""  data-uid="'.base64_encode('highseaaccount#'.$valueecho['userid'].'#'.$valueecho['edate']).'"':'').'>'.$valueecho['highseaaccount'].'</td>
                                    
                                    
                                    
                                    <td style="cursor:pointer;" '.(($valueecho['addacounts']+$valueecho['transferaccount']+$valueecho['highseaaccount'])>0?'class="getdetaileworkstatistics" clickfalg="1" data-module=""  data-uid="'.base64_encode('potentialcustomer#'.$valueecho['userid'].'#'.$valueecho['edate']).'"':'').'>'.($valueecho['addacounts']+$valueecho['transferaccount']+$valueecho['highseaaccount']).'</td>

                                    
                                    <td style="cursor:pointer;'.($countmatchingstandard?($matchingStandard['invite_number']?'color:green;':'color:red;'):'').'" '.($valueecho['billvisits']>0?'class="getdetaileworkstatistics" clickfalg="1" data-module=""  data-uid="'.base64_encode('billvisits#'.$valueecho['userid'].'#'.$valueecho['edate']).'"':'').'>'.$valueecho['billvisits'].'</td>
                                    
                                    <td style="cursor:pointer;" '.($valueecho['newvisitingnum']>0?'class="getdetaileworkstatistics" clickfalg="1" data-module="newvisitingnum" data-module=""  data-uid="'.base64_encode('newvisitingnum#'.$valueecho['userid'].'#'.$valueecho['edate']).'"':'').'>'.$valueecho['newvisitingnum'].'</td>

                                    <td style="cursor:pointer;" '.($valueecho['numbervisitors']>0?'class="getdetaileworkstatistics" clickfalg="1" data-module=""  data-uid="'.base64_encode('numbervisitors#'.$valueecho['userid'].'#'.$valueecho['edate']).'"':'').'>'.$valueecho['numbervisitors'].'</td>
                                    <td style="cursor:pointer;" '.($valueecho['accompanyingvisits']>0?'class="getdetaileworkstatistics" clickfalg="1" data-module=""  data-uid="'.base64_encode('accompanyingvisits#'.$valueecho['userid'].'#'.$valueecho['edate']).'"':'').'>'.$valueecho['accompanyingvisits'].'</td>
                                    <td style="'.($countmatchingstandard?($matchingStandard['visit_number']?'color:green;':'color:red;'):'').'" >'.$valueecho['nactualvisitors'].'</td>
                                    <td style="cursor:pointer;" data-placement="left" title="新签客户数" '.($valueecho['signaccount']>0?'class="getdetaileworkstatistics" clickfalg="1" data-module=""  data-uid="'.base64_encode('signaccount#'.$valueecho['userid'].'#'.$valueecho['edate']).'"':'').'>'.$valueecho['signaccount'].'</td>
                                    <td style="cursor:pointer;'.($countmatchingstandard?($matchingStandard['returned_money']?'color:green;':'color:red;'):'').'" data-placement="left" title="到账金额" '.($valueecho['amountpaid']>0?'class="getdetaileworkstatistics" clickfalg="1" data-uid="'.base64_encode('amountpaid#'.$valueecho['userid'].'#'.$valueecho['edate']).'"':'').'>'.$valueecho['amountpaid'].'</td>
                                    
                                    
                      
                                   
                                    <td style="cursor:pointer;" data-placement="left" title="是否达标" >'.$valueecho['is_pass'].'</td>
                                    </tr>';
                                }else{
                                    $text.='<tr>
                                    '.$ydepartnamestr.$bdepartnamestr.$changdstr.'
                                    <td>'.$value3['username'].'</td>
                                    <td>'.$value3['entered'].'</td>
                                    <td>'.($currentDiffMonth>=0?
                                            ($currentDiffMonth>1?
                                                ($currentDiffMonth>3?
                                                    ($currentDiffMonth>6?
                                                        ($currentDiffMonth>12?'12个月以上':'6~12个月')
                                                        :'3~6个月')
                                                    :'1~3个月')
                                                :'1个月内')
                                            :'12个月以上').'</td><td colspan="19"></td></tr>';
                                }
                                $columnname++;
                                $campColumnNum++;
                                ++$changd;
                                ++$chang_yderpart;
                                ++$change_mderpart;
                            }
                            $text=str_replace('changedaynum',$changd,$text);
                        }
                    }
                    $text=str_replace('bnamedepart',$change_mderpart,$text);
                    $text.='<tr><td><span class="label label-a_normal">'.$cachedepartment[$value2].'</span></td>
                                <td  colspan="4">部门小计</td>      
                                <td  colspan="3" style="text-align: center">---</td>              
                                <td>'.$telnumber.'</td>              
                                <td>平均值:'.(round($tel_connect_rate/$columnname,2)).'</td>              
                                <td>'.$telduration.'</td>              
                                <td>'.$addacounts.'</td>              
                                <td>'.$transferaccount.'</td>              
                                <td>'.$highseaaccount.'</td>              
                                <td  style="text-align: center">---</td>              
                                <td>'.$billvisits.'</td>              
                                <td>'.$newvisitingnum.'</td>              
                                <td>'.$numbervisitors.'</td>              
                                <td>'.$accompanyingvisits.'</td>              
                                <td>'.$nactualvisitors.'</td>              
                                <td>'.$signaccount.'</td>              
                                <td>'.$amountpaid.'</td>              
                                <td  style="text-align: center">---</td>
                            </tr>';
                    ++$chang_yderpart;
                }
                ++$chang_yderpart;
                $text=str_replace('ynamedepart',$chang_yderpart,$text);
//                $text.='<tr class="text-center"><td><span class="label label-success text-center">'.$cachedepartment[$key1].'</span></td><td colspan="31"></td></tr>';
//                                <td>平均值:'.(round($total_tel_connect_rate/$campColumnNum,2)).'</td>

                $text.='<tr class="text-center"><td><span class="label label-success text-center">'.$cachedepartment[$key1].'</span></td>
                                <td  colspan="4">营总计</td>      
                                <td  colspan="3" style="text-align: center">---</td>              
                                <td>'.$total_telnumber.'</td>              
                                <td style="text-align: center">---</td>              
                                <td>'.$total_telduration.'</td>              
                                <td>'.$total_addacounts.'</td>              
                                <td>'.$total_transferaccount.'</td>              
                                <td>'.$total_highseaaccount.'</td>              
                                <td   style="text-align: center">---</td>              
                                <td>'.$total_billvisits.'</td>              
                                <td>'.$total_newvisitingnum.'</td>              
                                <td>'.$total_numbervisitors.'</td>              
                                <td>'.$total_accompanyingvisits.'</td>              
                                <td>'.$total_nactualvisitors.'</td>              
                                <td>'.$total_signaccount.'</td>              
                                <td>'.$total_amountpaid.'</td>              
                                <td  style="text-align: center">---</td>
                            </tr>';

            }
            $table='
                <div id="fixscrollrf" class="hide" style="overflow:hidden;z-index:1033;">
                <table class="table table-bordered" id="flalted" style="position:relative;overflow-y: auto">
                    <thead>
                    <tr id="flalte1"  style="background-color:#ffffff;">
                        <th style="text-align: center;vertical-align:middle;">中心</th>
                        <th style="text-align: center;vertical-align:middle;">部门</th>
                        <th style="text-align: center;vertical-align:middle;">日期</th>
                        <th style="text-align: center;vertical-align:middle;">姓名</th>
                        <th style="text-align: center;vertical-align:middle;">入职日期</th>
                        <th style="text-align: center;vertical-align:middle;">员工阶段</th>
                        <th style="text-align: center;vertical-align:middle;" title="在系统内参与审核的次数">审核次数</th>
                        <th style="text-align: center;vertical-align:middle;" title="当天添加客户跟进评论的客户数量">评论客户数</th>
                        <th style="text-align: center;vertical-align:middle;" title="当天添加客户跟进的客户数量">跟进客户数</th>
                        <th style="text-align: center;vertical-align:middle;">电话数量</th>
                        <th style="text-align: center;vertical-align:middle;">接通率(%)</th>
                        <th style="text-align: center;vertical-align:middle;">电话时长</th>
                        <th style="text-align: center;vertical-align:middle;">新增客户数</th>
                        <th style="text-align: center;vertical-align:middle;">划转客户数</th>
                        <th style="text-align: center;vertical-align:middle;" title="公海捡到正常区加上临时区捡到正常区的客户总数">公海捡客户数</th>
                        <th style="text-align: center;vertical-align:middle;">意向客户数</th>
                        <th style="text-align: center;vertical-align:middle;">提单数(邀约面谈数)</th>
                        <th style="text-align: center;vertical-align:middle;" title="当天提交的新客户的拜访单数">新开拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访客户数(当天面谈数)</th>
                        <th style="text-align: center;vertical-align:middle;">陪访客户数</th>
                        <th style="text-align: center;vertical-align:middle;" title="当天拜访加陪访客户总数">实际拜访单客户数</th>
                        <th style="text-align: center;vertical-align:middle;">新签客户数</th>
                        <th style="text-align: center;vertical-align:middle;">到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">是否达标</th>
                    </tr>
                    </thead>
                </table></div>';
            $table.='
                <div id="scrollrf" style="overflow: auto;">
                <table class="table table-bordered table-striped" id="one1">
                    <thead>
                    <tr id="flaltt1">
                        <th style="text-align: center;vertical-align:middle;">中心</th>
                        <th style="text-align: center;vertical-align:middle;">部门</th>
                        <th style="text-align: center;vertical-align:middle;">日期</th>
                        <th style="text-align: center;vertical-align:middle;">姓名</th>
                        <th style="text-align: center;vertical-align:middle;">入职日期</th>
                        <th style="text-align: center;vertical-align:middle;">员工阶段</th>
                        <th style="text-align: center;vertical-align:middle;" title="在系统内参与审核的次数">审核次数</th>
                        <th style="text-align: center;vertical-align:middle;" title="当天添加客户跟进评论的客户数量">评论客户数</th>
                        <th style="text-align: center;vertical-align:middle;" title="当天添加客户跟进的客户数量">跟进客户数</th>
                        <th style="text-align: center;vertical-align:middle;">电话数量</th>
                        <th style="text-align: center;vertical-align:middle;">接通率(%)</th>
                        <th style="text-align: center;vertical-align:middle;">电话时长</th>
                        <th style="text-align: center;vertical-align:middle;">新增客户数</th>
                        <th style="text-align: center;vertical-align:middle;">划转客户数</th>
                        <th style="text-align: center;vertical-align:middle;"  title="公海捡到正常区加上临时区捡到正常区的客户总数">公海捡客户数</th>
                        <th style="text-align: center;vertical-align:middle;">意向客户数</th>
                        <th style="text-align: center;vertical-align:middle;">提单数(邀约面谈数)</th>
                        <th style="text-align: center;vertical-align:middle;" title="当天提交的新客户的拜访单数">新开拜访数</th>
                        <th style="text-align: center;vertical-align:middle;">拜访客户数(当天面谈数)</th>
                        <th style="text-align: center;vertical-align:middle;">陪访客户数</th>
                        <th style="text-align: center;vertical-align:middle;" title="当天拜访加陪访客户总数">实际拜访单客户数</th>
                        <th style="text-align: center;vertical-align:middle;">新签客户数</th>
                        <th style="text-align: center;vertical-align:middle;">到账金额</th>
                        <th style="text-align: center;vertical-align:middle;">是否达标</th>
                    </tr>
                    
                    </thead>
                    <tbody>
                    '.$text.'
                    </tbody>
                </table></div>';
            echo $table;
            exit;


        }else{
            echo '<table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th style="text-align: center;vertical-align:middle;">没有记录</th>
                    </tr></thead></table>';
            exit;
        }
    }

    /**
     * 获取报表数据
     * @param Vtiger_Request $request
     * @return array
     * @throws AppException
     */
    public function getwstsdata(Vtiger_Request $request){
        $datetime=$request->get('datetime');
        $enddatetime=$request->get('enddatetime');
        $userid=$request->get('userid');
        $departmentid=$request->get('department');
        $querySql='';
        if(empty($userid)||!is_numeric($userid)){
            $departmentarr=array();
            if(!empty($departmentid)&&$departmentid!='null'){
                $where1=getAccessibleUsers('Accounts','List',true);
                if($where1!='1=1'){
                    $where2=getAccessibleUsers('TelStatistics','List',true);
                    if($where2!='1=1'){
                        $where1=array_merge($where1,$where2);
                    }else{
                        $where1= $where2;
                    }
                }
                foreach($departmentid as $value){
                    $userid=getDepartmentUser($value);
                    if($where1!='1=1'){
                        $where=array_intersect($where1,$userid);
                    }else{
                        $where=$userid;
                    }
                    if(empty($where)||count($where)==0){
                        continue;
                    }
                    $departmentarr=array_merge($departmentarr,$where);
                }
                if(empty($departmentarr)){
                    $departmentarr=array(-1);
                }
                $querySql=' AND userid IN('.implode(',',$departmentarr).')';
                $querySqlActive=' AND vtiger_users.id IN('.implode(',',$departmentarr).')';
//                $querySqlActive=' AND vtiger_useractivemonth.userid IN('.implode(',',$departmentarr).')';
            }else{
                $where=getAccessibleUsers('Accounts','List',false);
                if($where!='1=1'){
                    $querySql=' AND userid IN('.implode(',',$where).')';
                    $querySqlActive=' AND vtiger_users.id IN('.implode(',',$where).')';
//                    $querySqlActive=' AND vtiger_useractivemonth.userid IN('.implode(',',$where).')';
                }
            }
        }else{

            $querySql=' AND userid='.$userid;
            $querySqlActive=' AND vtiger_users.id='.$userid;
//            $querySqlActive=' AND vtiger_useractivemonth.userid='.$userid;
        }
        if (strtotime($datetime) > strtotime($enddatetime)) {
            $tempdate = " AND edate BETWEEN '{$enddatetime}' AND '{$datetime}'";
            $workday = " AND dateday= BETWEEN '{$enddatetime}' AND '{$datetime}'";
        } elseif (strtotime($datetime) < strtotime($enddatetime)) {
            $tempdate = " AND edate BETWEEN '{$datetime}' AND '{$enddatetime}'";
            $workday = " AND dateday BETWEEN '{$datetime}' AND '{$enddatetime}'";
        } else {
            $tempdate = " AND edate='{$datetime}'";
            $workday = " AND dateday='" . $datetime . "'";
        }
        $datetimeMonth=substr($datetime,0,7);
        $enddatetimeMonth=substr($enddatetime,0,7);
        if (strtotime($datetimeMonth) > strtotime($enddatetimeMonth)) {
            $tempdateActive = " AND vtiger_useractivemonth.activedate BETWEEN '{$enddatetimeMonth}' AND '{$datetimeMonth}'";

        } elseif (strtotime($datetimeMonth) < strtotime($enddatetimeMonth)) {
            $tempdateActive = " AND vtiger_useractivemonth.activedate BETWEEN '{$datetimeMonth}' AND '{$enddatetimeMonth}'";

        } else {
            $tempdateActive = " AND vtiger_useractivemonth.activedate='{$datetimeMonth}'";

        }
        if ($datetime == '') {
            $tempdateActive= " AND vtiger_useractivemonth.activedate='" . date('Y-m') . "'";
            $tempdate = " AND edate='" . date('Y-m-d') . "'";
            $workday = " AND dateday='" . date('Y-m-d') . "'";
        }
        $query='SELECT 
                eworkstatisticsid,
                userid,
                edate,
                telnumber,
                total_telnumber,
                telduration,
                addacounts,
                transferaccount,
                highseaaccount,
                billvisits,
                numbervisitors,
                accompanyingvisits,
                nactualvisitors,
                signaccount,
                amountpaid,
                enterednum,
                is_pass,
                vtiger_eworkstatistics.department,
                vtiger_users.user_entered as entered,
                vtiger_users.last_name as username,
                verifynum,
                newvisitingnum,
                commentaccountnum,
                followupaccountnum
                 FROM `vtiger_eworkstatistics`
                 LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_eworkstatistics.userid
                WHERE 1=1'.$querySql.$tempdate.'
                ORDER BY department,edate limit 10000';
        $db=PearDatabase::getInstance();
        $result=$db->pquery($query,array());
        $num=$db->num_rows($result);
        if($num){
            $array=array();
            for($i=0;$i<$num;$i++){
                $thisresultdata=$db->query_result_rowdata($result,$i);
                $thisresultdata=array(
                    'eworkstatisticsid'=>$thisresultdata['eworkstatisticsid'],
                    'userid'=>$thisresultdata['userid'],
                    'edate'=>$thisresultdata['edate'],
                    'telnumber'=>$thisresultdata['telnumber'],
                    'total_telnumber'=>$thisresultdata['total_telnumber'],
                    'tel_connect_rate'=>round(($thisresultdata['telnumber']/$thisresultdata['total_telnumber'])*100,2),
                    'telduration'=>$thisresultdata['telduration'],
                    'addacounts'=>$thisresultdata['addacounts'],
                    'transferaccount'=>$thisresultdata['transferaccount'],
                    'highseaaccount'=>$thisresultdata['highseaaccount'],
                    'billvisits'=>$thisresultdata['billvisits'],
                    'numbervisitors'=>$thisresultdata['numbervisitors']>0 ?$thisresultdata['numbervisitors'] : 0,
                    'accompanyingvisits'=>$thisresultdata['accompanyingvisits']>0 ?$thisresultdata['accompanyingvisits']:0,
                    'nactualvisitors'=>$thisresultdata['nactualvisitors']>0?$thisresultdata['nactualvisitors']:0,
                    'signaccount'=>$thisresultdata['signaccount']>0?$thisresultdata['signaccount']:0,
                    'amountpaid'=>$thisresultdata['amountpaid']>0?$thisresultdata['amountpaid']:0,
                    'department'=>$thisresultdata['department'],
                    'entered'=>$thisresultdata['entered'],
                    'is_pass'=>$thisresultdata['is_pass'],
                    'verifynum'=>$thisresultdata['verifynum'],
                    'newvisitingnum'=>$thisresultdata['newvisitingnum'],
                    'commentaccountnum'=>$thisresultdata['commentaccountnum'],
                    'followupaccountnum'=>$thisresultdata['followupaccountnum'],


                );
                $depart=$thisresultdata['department'];
                $depart=explode('::',$depart);
                if(!empty($departmentid)&&$departmentid!='null'){
                    $array[$depart[0]][$depart[1]][$thisresultdata['edate']][$thisresultdata['userid']]=$thisresultdata;
                }else{
                    $array[$depart[0]][$depart[1]][$thisresultdata['edate']][$thisresultdata['userid']]=$thisresultdata;
                }
            }
            return array('data'=>$array,'userdata'=>$this->getUserActive(array($querySqlActive,$tempdateActive,$workday),$datetime,$enddatetime,$request->get('userid')));
        }else{
            return array('userdata'=>$this->getUserActive(array($querySqlActive,$tempdateActive,$workday),$datetime,$enddatetime,$request->get('userid')));
        }
    }
    public function getUserActive($data,$datetime,$enddatetime,$userid){
//        $query="SELECT vtiger_useractivemonth.activedate,vtiger_users.user_entered,vtiger_users.last_name,vtiger_useractivemonth.userid,vtiger_user2department.departmentid,SUBSTRING_INDEX(vtiger_departments.parentdepartment,'::' ,- 2) AS pdepartment,departmentname,vtiger_user2role.roleid FROM vtiger_useractivemonth
//                LEFT JOIN vtiger_users ON vtiger_useractivemonth.userid=vtiger_users.id
//                LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id
//                LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
//                LEFT JOIN vtiger_user2role ON vtiger_users.id=vtiger_user2role.userid
//                WHERE 1=1 ".$data[0].$data[1]." ORDER BY vtiger_useractivemonth.activedate,vtiger_useractivemonth.userid";
        $skip_sql = '';
        if(!$userid){
            $skiproles = TelStatistics_Module_Model::getSkipRoles('TelStatistics','eworkstatistics');
            if($skiproles){
                $role = '';
                foreach ($skiproles as $skiprole){
                    $role .= "'".$skiprole."'".',';
                }
                $skip_sql = 'AND vtiger_user2role.roleid not in('.trim($role,',').') ';
            }
        }

        $query="select vtiger_users.user_entered,vtiger_users.last_name,vtiger_users.id,vtiger_user2department.departmentid,
	SUBSTRING_INDEX( vtiger_departments.parentdepartment, '::',- 2 ) AS pdepartment,
	departmentname,vtiger_users.leavedate,vtiger_users.status,
	vtiger_user2role.roleid  from vtiger_users LEFT JOIN vtiger_user2department ON vtiger_user2department.userid = vtiger_users.id
	LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid = vtiger_departments.departmentid
	LEFT JOIN vtiger_user2role ON vtiger_users.id = vtiger_user2role.userid  where 1=1 ".$data[0].$skip_sql." ORDER BY vtiger_users.id";
        global $adb;
        $result=$adb->pquery($query,array());
        $return=array();
        if($adb->num_rows($result)){
            $temp=array();

            while($row=$adb->fetch_array($result)){
                $leavedate = date('Y-m-d',strtotime($row['leavedate']));
                $user_entered = date('Y-m-d',strtotime($row['user_entered']));
                $starttime = $datetime;
                if($row['leavedate']&&$leavedate<$datetime || ((!$row['leavedate'])  && ($row['status']=='Inactive')) ){
                    continue;
                }

                $pdepartment=explode('::',$row['pdepartment']);
                if(!in_array($row['departmentid'],$temp)){
                    $return['pdepartment'][$pdepartment[0]][]=$row['departmentid'];
                    $temp[]=$row['departmentid'];
                }

                if($user_entered > $datetime){
                    $starttime = $user_entered;
                }
                $endtime = $enddatetime;

                $start    = new \DateTime($starttime.' 00:00:00');
                $end      = new \DateTime($endtime.' 24:59:59');
                $interval = \DateInterval::createFromDateString('1 month');
                $period   = new \DatePeriod($start, $interval, $end);
                foreach ($period as $dt) {
                    $active_dates[] =  $dt->format("Y-m");
                }
                if(!in_array(substr($endtime,0,7),$active_dates)){
                    $active_dates[] = substr($endtime,0,7);
                }
                foreach ($active_dates as $active_date){
                    $return[$row['departmentid']][$active_date]['userdata'][$row['id']]=array('username'=>$row['last_name'],'entered'=>$row['user_entered'],'userid'=>$row['id'],'roleid'=>$row['roleid'],'leavedate'=>$row['leavedate'],'');
//                    $return[$row['departmentid']][$row['activedate']]['userdata'][$row['userid']]=array('username'=>$row['last_name'],'entered'=>$row['user_entered'],'userid'=>$row['userid'],'roleid'=>$row['roleid'],'');

                }
            }
        }
        /**
         * 假期
         */
       /* $query="SELECT dateday FROM `vtiger_workday` WHERE datetype='holiday'".$data[2];
        $result=$adb->pquery($query,array());
        $return['holiday']=array();
        if($adb->num_rows($result)){
            while($row=$adb->fetch_array($result)){
                $return['holiday'][]=$row['dateday'];
            }
        }*/
        return $return;
    }

    /**
     * 报有数据页点击显示
     * @param Vtiger_Request $request
     */
    public function getdetaileworkstatistics(Vtiger_Request $request){
        $data=$request->get('datas');
        $return['flag']=false;
        do{
            if(empty($data)){
                break;
            }
            $data=base64_decode($data);
            $data=explode('#',$data);
            switch($data[0]){
                case 'addacounts':
                case 'transferaccount':
                case 'highseaaccount':
                    $query="SELECT vtiger_".$data[0].".accountid AS id,vtiger_account.accountname AS name FROM vtiger_".$data[0]." LEFT JOIN vtiger_account ON vtiger_".$data[0].".accountid=vtiger_account.accountid WHERE userid=? AND edate=?";
                    $return=$this->getDataWstList(array($query,array($data[1],$data[2]),'Accounts'));
                    break;
                case 'billvisits':
                case 'numbervisitors':
                    $strQuery=$data[0]=='numbervisitors'?" AND vtiger_visitingorder.modulestatus='c_complete' AND startdate>? AND startdate<?":' AND vtiger_crmentity.createdtime>? AND vtiger_crmentity.createdtime<?';
                    $query="SELECT vtiger_visitingorder.visitingorderid AS id,vtiger_account.accountname AS name 
                            FROM vtiger_visitingorder 
                            LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_visitingorder.related_to
                            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_visitingorder.visitingorderid 
                            WHERE deleted=0 AND related_to>0 AND extractid=? ".$strQuery;
                    $date1=$data[2].' 00:00:00';
                    $date2=$data[2].' 23:59:59';
                    $return=$this->getDataWstList(array($query,array($data[1],$date1,$date2),'VisitingOrder'));
                    break;
                case 'accompanyingvisits':
                    $query="SELECT vtiger_accompanyingvisits.visitingorderid AS id,vtiger_account.accountname AS name
                            FROM `vtiger_accompanyingvisits` 
                            LEFT JOIN vtiger_visitingorder ON vtiger_visitingorder.visitingorderid=vtiger_accompanyingvisits.visitingorderid
                            LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_visitingorder.related_to
                            WHERE vtiger_accompanyingvisits.userid=? AND edate=?";
                    $return=$this->getDataWstList(array($query,array($data[1],$data[2]),'VisitingOrder'));
                    break;
                case 'signaccount':
                    $query="SELECT vtiger_signaccount.servicecontractsid AS id,vtiger_servicecontracts.contract_no AS name FROM vtiger_signaccount 
                            LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_signaccount.servicecontractsid 
                            WHERE vtiger_signaccount.userid=? AND edate=?";
                    $return=$this->getDataWstList(array($query,array($data[1],$data[2]),'ServiceContracts'));
                    break;
                case 'amountpaid':
                    $query="SELECT vtiger_achievementallot.receivedpaymentsid AS id,concat(vtiger_receivedpayments.paytitle,'-',businessunit) AS name 
                            FROM vtiger_achievementallot 
                            LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid 
                            WHERE vtiger_achievementallot.receivedpaymentownid=?
                            AND vtiger_receivedpayments.reality_date=?";
                    $return=$this->getDataWstList(array($query,array($data[1],$data[2]),'ReceivedPayments'));
                    break;
                case 'verifynum':
                    $query="SELECT b.modulename AS modulename,b.salesorderid as id,b.accountname,b.salesorder_nono as name
                    FROM vtiger_verifynum a 
                    LEFT JOIN vtiger_salesorderworkflowstages b ON a.salesorderworkflowstagesid = b.salesorderworkflowstagesid 
                    WHERE a.userid=? 
                    AND a.edate=?";
                    $return=$this->getDataWstList(array($query,array($data[1],$data[2]),'ReceivedPayments','VerifyNum'));
                    break;
                case 'newvisitingnum':
                    $query = "SELECT b.accountnamer as name, b.related_to as id
                    FROM vtiger_newvisitingnum a 
                    LEFT JOIN vtiger_visitingorder b on a.visitingorderid = b.visitingorderid 
                    WHERE a.userid=? 
                    AND a.edate=?";
                    $return=$this->getDataWstList(array($query,array($data[1],$data[2]),'Accounts','NewVisitingNum'));
                    break;
                case 'commentaccountnum':
                    $query = "SELECT d.accountname as name, a.accountid as id,b.modcommenthistory
                    FROM vtiger_commentaccountnum a 
                    LEFT JOIN vtiger_submodcomments b on a.submodcommentsid = b.modcommentsid 
                    LEFT JOIN vtiger_modcomments c on b.modcommentsid = c.modcommentsid 
                    LEFT JOIN vtiger_account d on c.related_to = d.accountid 
                    WHERE a.userid=? 
                    AND a.edate=?";
                    $return=$this->getDataWstList(array($query,array($data[1],$data[2]),'Accounts','CommentAccountNum'));
                    break;
                case 'followupaccountnum':
                    $query = "SELECT 
                    c.accountname as name,a.accountid as id,b.commentcontent
                     FROM vtiger_followupaccountnum a 
                     LEFT JOIN vtiger_modcomments b on a.modcommentsid = b.modcommentsid 
                     LEFT JOIN vtiger_account c on a.accountid = c.accountid
                    WHERE a.userid=? 
                    AND a.edate=?";
                    $return=$this->getDataWstList(array($query,array($data[1],$data[2]),'Accounts','FollowUpAccountNum'));
                    break;
                case 'potentialcustomer':
                    $return = $this->getPotentialCustomer($data);
                    break;
            }

        }while(0);
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($return);
        $response->emit();
    }
    public function getMonthNum($date1,$date2){
        if(strtotime($date1)>strtotime($date2)){
            $tmp=$date2;
            $date2=$date1;
            $date1=$tmp;
        }
        list($Y1,$m1,$d1)=explode('-',$date1);
        list($Y2,$m2,$d2)=explode('-',$date2);
        $Y=$Y2-$Y1;
        $m=$m2-$m1;
        $d=$d2-$d1;
        if($d<0){
            $d+=(int)date('t',strtotime("-1 month $date2"));
            $m--;
        }
        if($m<0){
            $m+=12;
            $Y--;
        }
        return $Y*12+$m+($d>0?1:0);
    }
    /**
     * 求两个日期相差的月份
     * @param $date1
     * @param $date2
     * @param string $tags
     * @return number
     */

    public function getMonthNumBak($date1,$date2){
        $datetime1 = new DateTime($date1);
        $datetime2 = new DateTime($date2);
        $interval = $datetime1->diff($datetime2);
        $time['y']         = $interval->format('%Y');
        $time['m']         = $interval->format('%m');
        $time['d']         = $interval->format('%d');
        $time['h']         = $interval->format('%H');
        $time['i']         = $interval->format('%i');
        $time['s']         = $interval->format('%s');
        $time['a']         = $interval->format('%a');    // 两个时间相差总天数
        if($time['y']==0){
            $time['m']++;
        }
        if($time['d']>27){
            $time['m']++;
        }
        return $time;
    }

    public function getPotentialCustomer($data){
        $tables = array('addacounts','transferaccount','highseaaccount');
        $returnData = array();
        $flag = false;
        $module = '';
        foreach ($tables as $table){
            $query="SELECT vtiger_".$table.".accountid AS id,vtiger_account.accountname AS name FROM vtiger_".$table." LEFT JOIN vtiger_account ON vtiger_".$table.".accountid=vtiger_account.accountid WHERE userid=? AND edate=?";
            $return=$this->getDataWstList(array($query,array($data[1],$data[2]),'Accounts'));
            if(count($return['data'])<1){
                continue;
            }
            $flag = $return['flag'];
            $module = $return['module'];
            $returnData = array_merge($returnData,$return['data']);
        }

        return array(
            'flag'=>$flag,
            'module'=>$module,
            'data'=>$returnData
        );
    }

    /**
     * 报表显示处理数据
     * @param $data
     * @return mixed
     */
    public function getDataWstList($data){
        global $adb;
        $result=$adb->pquery($data[0],$data[1]);
        $return['flag']=false;
        if($adb->num_rows($result)){
            $return['flag']=true;
            $return['module']=$data[2];
            $request_module = $data[3];
            if(!$data[3]){
                while($row=$adb->fetch_array($result)){
                    $return['data'][]=array(
                        'showid'=>$row['id'],
                        'showname'=>$row['name'],
                    );
                }
            }else{
               switch ($request_module){
                   case 'VerifyNum':
                       while($row=$adb->fetch_array($result)){
                           $return['data'][]=array(
                               'showid'=>$row['id'],
                               'showname'=>$row['name'],
                               'modulename'=>$row['modulename'],
                               'transmodulename'=>vtranslate($row['modulename']),
                               'accountname'=>$row['accountname'],
                           );
                       }
                       break;
                   case 'NewVisitingNum':
                       while($row=$adb->fetch_array($result)){
                           $return['data'][]=array(
                               'showid'=>$row['id'],
                               'showname'=>$row['name'],
                           );
                       }
                       break;

                   case 'CommentAccountNum':
                       while($row=$adb->fetch_array($result)){
                           $return['data'][]=array(
                               'showid'=>$row['id'],
                               'showname'=>$row['name'],
                               'modcommenthistory'=>$row['modcommenthistory']
                           );
                       }
                       break;

                   case 'FollowUpAccountNum':
                       while($row=$adb->fetch_array($result)){
                           $return['data'][]=array(
                               'showid'=>$row['id'],
                               'showname'=>$row['name'],
                               'commentcontent'=>$row['commentcontent']
                           );
                       }
                       break;
               }
            }
        }
        return $return;
    }

    /**
     * 报表图表获总显示
     * @param Vtiger_Request $request
     * @throws AppException
     */
    public function geteworksituationtrendsdata(Vtiger_Request $request){
        $startdatetime=$request->get('datetime');
        $enddatetime=$request->get('enddatetime');
        $userid=$request->get('userid');
        $departmentid=$request->get('department');
        if(!$this->checkDate($startdatetime)||!$this->checkDate($enddatetime)){
            $nowtime=date("Y-m-d");
            $tempdate=" AND edate='{$nowtime}'";
        }else{
            if($startdatetime<$enddatetime){
                $tempdate=" AND edate BETWEEN '{$startdatetime}' AND '{$enddatetime}'";
            }elseif($startdatetime>$enddatetime){
                $tempdate=" AND edate BETWEEN '{$enddatetime}' AND '{$startdatetime}'";
            }else{
                $tempdate=" AND edate='{$startdatetime}'";
            }
        }
        //处理部门
        $db=PearDatabase::getInstance();
        $query="SELECT ";
        $arr=array();
        $skiproles = TelStatistics_Module_Model::getSkipRoles('TelStatistics','eworksituationtrends');
        if($skiproles){
            $role = '';
            foreach ($skiproles as $skiprole){
                $role .= "'".$skiprole."'".',';
            }
            $skip_sql = ' AND b.roleid not in('.trim($role,',').') ';
        }
        if($userid!='null'&&!empty($userid)){
            $query1="SELECT a.id,a.last_name FROM vtiger_users a left join vtiger_user2role b on a.id=b.userid WHERE a.id in(".implode(',',$userid).")".$skip_sql." limit 20";
            $uresult=$db->pquery($query1);
            $num1=$db->num_rows($uresult);
            if($num1>0){
                for($i=0;$i<$num1;++$i){
                    $arr['newdepartmentid'][]='user'.$db->query_result($uresult,$i,'id');
                    $tempid=$db->query_result($uresult,$i,'id');
                    $arr['newdepartment']['user'.$tempid]=$db->query_result($uresult,$i,'last_name');
                    $query.="sum(if(userid IN (".$tempid."),telnumber,0)) as telnumber_user".$tempid.",
                            sum(if(userid IN (".$tempid."),telduration,0)) as telduration_user".$tempid.",
                            sum(if(userid IN (".$tempid."),addacounts,0)) as addacounts_user".$tempid.",
                            sum(if(userid IN (".$tempid."),transferaccount,0)) as transferaccount_user".$tempid.",
                            sum(if(userid IN (".$tempid."),highseaaccount,0)) as highseaaccount_user".$tempid.",
                            sum(if(userid IN (".$tempid."),billvisits,0)) as billvisits_user".$tempid.",
                            sum(if(userid IN (".$tempid."),numbervisitors,0)) as numbervisitors_user".$tempid.",
                            sum(if(userid IN (".$tempid."),accompanyingvisits,0)) as accompanyingvisits_user".$tempid.",
                            sum(if(userid IN (".$tempid."),nactualvisitors,0)) as nactualvisitors_user".$tempid.",
                            sum(if(userid IN (".$tempid."),signaccount,0)) as signaccount_user".$tempid.",
                            sum(if(userid IN (".$tempid."),amountpaid,0)) as amountpaid_user".$tempid.",
                            ";
                }
                $flag=1;
            }
        }else{
            if($departmentid=="null"||empty($departmentid)){
                $departmentid=array();
                $departmentid[]='H1';
            }
            $cachedepartment=getDepartment();
            $arrnum=array();//部门中有多少个人
            //部门不能超过20个
            $currnetNum=count($departmentid);
            for($i=0;$i<$currnetNum&&$i<20;++$i){
                $userid=getDepartmentUser($departmentid[$i]);
                $before_where=getAccessibleUsers('Accounts','List',true);
                if($before_where!='1=1'){
                    $before_where=array_intersect($before_where,$userid);
                }else{
                    $before_where=$userid;
                }
                //没有负责人的部门直接不查询该部门
                if(empty($before_where)||count($before_where)==0){
                    continue;
                }

                $query1="SELECT a.id,a.status,a.leavedate,a.user_entered FROM vtiger_users a left join vtiger_user2role b on a.id=b.userid WHERE a.id in(".implode(',',$before_where).")".$skip_sql;
                $uresult=$db->pquery($query1);
                $num1=$db->num_rows($uresult);
                if(!$num1){
                    continue;
                }
                while ($row = $db->fetch_row($uresult)){
                    if((!$row['leavedate'] && $row['status']=='Inactive') || ($row['leavedate']&&$row['leavedate']<$startdatetime) ||
                    $row['user_entered']>$enddatetime){
                        continue;
                    }
                    $where[] = $row['id'];
                }
                if(empty($where) || count($where)==0){
                    continue;
                }
                $flag=1;
                $arrnum[strtolower($departmentid[$i])]=count($where);
                $arr['newdepartmentid'][]=strtolower($departmentid[$i]);
                $arr['newdepartment'][strtolower($departmentid[$i])]=str_replace(array('|','—'),array('',''),$cachedepartment[$departmentid[$i]]);
                $query.="sum(if(userid IN (".implode(',',$where)."),telnumber,0)) as telnumber_{$departmentid[$i]},
                sum(if(userid IN (".implode(',',$where)."),telduration,0)) as telduration_{$departmentid[$i]},
                sum(if(userid IN (".implode(',',$where)."),addacounts,0)) as addacounts_{$departmentid[$i]},
                sum(if(userid IN (".implode(',',$where)."),transferaccount,0)) as transferaccount_{$departmentid[$i]},
                sum(if(userid IN (".implode(',',$where)."),highseaaccount,0)) as highseaaccount_{$departmentid[$i]},
                sum(if(userid IN (".implode(',',$where)."),billvisits,0)) as billvisits_{$departmentid[$i]},
                sum(if(userid IN (".implode(',',$where)."),numbervisitors,0)) as numbervisitors_{$departmentid[$i]},
                sum(if(userid IN (".implode(',',$where)."),accompanyingvisits,0)) as accompanyingvisits_{$departmentid[$i]},
                sum(if(userid IN (".implode(',',$where)."),nactualvisitors,0)) as nactualvisitors_{$departmentid[$i]},
                sum(if(userid IN (".implode(',',$where)."),signaccount,0)) as signaccount_{$departmentid[$i]},
                sum(if(userid IN (".implode(',',$where)."),amountpaid,0)) as amountpaid_{$departmentid[$i]},
                ";
                unset($where);
            }
        }
        if($flag==0){
            $arr=Array('newdepartmentid' => Array('hno'),'newdepartment' => Array('hno' => '暂无数据'),'daycounts_hno' => Array(0),'dayforp_hno' => Array(0),'daysaler_hno' => Array(0),'dayvisiting_hno' => Array(0),'edate' => Array('暂无数据'));
            $response = new Vtiger_Response();
            $response->setEmitType(Vtiger_Response::$EMIT_JSON);
            $response->setResult($arr);
            $response->emit();
            exit;
        }
        $db=PearDatabase::getInstance();
        $query.="edate
                FROM
                    vtiger_eworkstatistics
                WHERE 1=1
                {$tempdate}
                GROUP BY
                    edate
                ";

        $result=$db->pquery($query,array());
        $num=$db->num_rows($result);
        if($num>0){
            for($i=0;$i<$num;$i++){
                foreach($db->query_result_rowdata($result,$i) as $key=>$value){
                    if(is_numeric($key)){
                        continue;
                    }else{
                        $arr[$key][]=$value;
                    }
                }
            }
        }else{
            $arr=Array('newdepartmentid' => Array('hno'),'newdepartment' => Array('hno' => '暂无数据'),'daycounts_hno' => Array(0),'dayforp_hno' => Array(0),'daysaler_hno' => Array(0),'dayvisiting_hno' => Array(0),'createdtime' => Array('暂无数据'));
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($arr);
        $response->emit();
    }
    private function checkDate($date,$format='Y-m-d'){
        if(!strtotime($date)){
            return false;
        }
        if(date($format,strtotime($date))==$date){
            return true;
        }
        return false;
    }

    /**
     * 柱状图点击显示
     * @param Vtiger_Request $request
     * @throws AppException
     */
    public function geteworksituationtrendslist(Vtiger_Request $request){
        $classtype=$request->get('classtype');
        $edate=$request->get('datetime');
        $userid=$request->get('datauserid');

        if(!$this->checkDate($edate)){
            $edate=date("Y-m-d");
        }

        $return['flag']=false;
        do{
            $userid=ltrim($userid,'user');
            $where=getAccessibleUsers('Accounts','List',true);
            if(is_numeric($userid)){
                if($where!=''){
                    if(!in_array($userid,$where)){
                        break;
                    }
                }
                $where=array($userid);
            }else{
                $userid=strtoupper($userid);
                $usersid=getDepartmentUser($userid);
                if($where!='1=1'){
                    $where=array_intersect($where,$usersid);
                }else{
                    $where=$usersid;
                }
            }
            if(empty($where)){
                break;
            }
            $skiproles = TelStatistics_Module_Model::getSkipRoles('TelStatistics','eworksituationtrends');
            if($skiproles){
                $role = '';
                foreach ($skiproles as $skiprole){
                    $role .= "'".$skiprole."'".',';
                }
                $skip_sql = ' AND b.roleid not in('.trim($role,',').') ';
            }
            $db=PearDatabase::getInstance();
            $query1="SELECT a.id FROM vtiger_users a left join vtiger_user2role b on a.id=b.userid WHERE a.id in(".implode(',',$where).")".$skip_sql;
            $uresult=$db->pquery($query1);
            $num1=$db->num_rows($uresult);
            if(!$num1){
                continue;
            }
            while ($row = $db->fetch_row($uresult)){
                $data[] = $row['id'];
            }

            $execSqlflag=true;
            $param=$data;
            $param[]=$edate;
            switch($classtype){
                case 'telnumber':
                case 'telduration':
                    $query=" SELECT vtiger_telstatistics.telduration,vtiger_telstatistics.telnumber,(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_telstatistics.useid=vtiger_users.id) as useid,vtiger_telstatistics.telnumberdate,(select departmentname from vtiger_departments where departmentid=vtiger_telstatistics.departmentid limit 1) AS departmentid,(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_telstatistics.smownerid=vtiger_users.id) as smownerid,vtiger_telstatistics.createdtime,vtiger_telstatistics.telstatisticsid as id FROM vtiger_telstatistics  WHERE 1=1 AND vtiger_telstatistics.useid in(".generateQuestionMarks($data).") AND vtiger_telstatistics.telnumberdate=? ORDER BY telstatisticsid DESC";
                    $module='TelStatistics';
                    $header=array(
                        'useid',
                        'telnumber',
                        'telduration',
                        'telnumberdate',
                        'departmentid');
                    break;
                case 'addacounts':
                case 'transferaccount':
                case 'highseaaccount':
                    $query="SELECT vtiger_".$classtype.".edate,
                    IFNULL(vtiger_account.accountname,'') AS accountname,
                            IFNULL(vtiger_account.servicetype,'') AS servicetype,
                            IFNULL(vtiger_account.accountrank,'') AS accountrank,
                            IFNULL(vtiger_account.linkname,'') AS linkname,
                            IFNULL(vtiger_account.industry,'') AS industry,
                            IFNULL(vtiger_account.annual_revenue,'') AS annual_revenue,
                            IFNULL(vtiger_account.address,'') AS address,
                            IFNULL(vtiger_account.makedecision,'') AS makedecision,
                            IFNULL(vtiger_account.business,'') AS business,
                            IFNULL(vtiger_account.regionalpartition,'') AS regionalpartition,
                            IFNULL(vtiger_account.title,'') AS title,
                            IFNULL(vtiger_account.leadsource,'') AS leadsource,
                            IFNULL(vtiger_account.linkname,'') AS linkname,
                            IFNULL(vtiger_account.businessarea,'') AS businessarea,
                            IFNULL(vtiger_crmentity.createdtime,'') AS createdtime,
                            IFNULL(vtiger_account.customerproperty,'') AS customerproperty,
                            IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname	FROM vtiger_departments WHERE	departmentid =(SELECT	departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id	LIMIT 1)),''),']',(IF(`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users	WHERE	vtiger_crmentity.smownerid = vtiger_users.id),'--'	) AS smownerid,
                            IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname	FROM vtiger_departments WHERE	departmentid =(SELECT	departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id	LIMIT 1)),''),']',(IF(`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users	WHERE	vtiger_".$classtype.".userid = vtiger_users.id),'--'	) AS cuserid,
                            IFNULL(vtiger_account.accountid,'') AS accountid,vtiger_".$classtype.".accountid AS id FROM vtiger_".$classtype." LEFT JOIN vtiger_account ON vtiger_".$classtype.".accountid=vtiger_account.accountid 
                            LEFT JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid
                            WHERE userid in(".generateQuestionMarks($data).") AND edate=?";
                    $module='Accounts';
                    $header=array(
                        'accountname',
                        'accountrank',
                        'industry',
                        'smownerid',
                        'cuserid',
                        'linkname',
                        'regionalpartition',
                        'edate',
                    );
                    break;
                case 'billvisits':
                case 'numbervisitors':
                    $strQuery=$classtype=='numbervisitors'?" AND vtiger_visitingorder.modulestatus='c_complete'":'';
                    $query="SELECT vtiger_account.accountname,
                            (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_visitingorder.extractid=vtiger_users.id) as vuserid,
                            IFNULL((SELECT GROUP_CONCAT(vtiger_users.last_name) FROM vtiger_users WHERE FIND_IN_SET(vtiger_users.id,REPLACE(vtiger_visitingorder.accompany,' |##| ',','))),'') AS pfr,
                            vtiger_visitingorder.destination,
                            vtiger_visitingorder.startdate,
                            vtiger_visitingorder.enddate,
                            vtiger_visitingorder.`subject`,
                            vtiger_visitingorder.modulestatus,
                            vtiger_visitingorder.followstatus,
                            vtiger_visitingorder.outobjective,
                            vtiger_visitingorder.purpose,
                            vtiger_visitingorder.visitingorderid AS id 
                            FROM vtiger_visitingorder 
                            LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_visitingorder.related_to
                            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_visitingorder.visitingorderid 
                            WHERE deleted=0 AND related_to>0 AND extractid in(".generateQuestionMarks($data).") AND startdate>? AND startdate<? ".$strQuery;
                    $param=$where;
                    $param[]=$edate.' 00:00';
                    $param[]=$edate.' 23:59';
                    $module='VisitingOrder';
                    $header=array(
                        'accountname',
                        'vuserid',
                        'pfr',
                        'startdate',
                        'enddate',
                        'subject',
                        'modulestatus',
                        'followstatus',
                        'outobjective',
                        'purpose',
                    );
                    break;
                case 'accompanyingvisits':
                    $query="SELECT 
                            vtiger_account.accountname,
                            (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_visitingorder.extractid=vtiger_users.id) as vuserid,
                            IFNULL((SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accompanyingvisits.userid),'') AS pfr,
                            vtiger_visitingorder.destination,
                            vtiger_visitingorder.startdate,
                            vtiger_visitingorder.enddate,
                            vtiger_visitingorder.`subject`,
                            vtiger_visitingorder.modulestatus,
                            vtiger_visitingorder.followstatus,
                            vtiger_visitingorder.outobjective,
                            vtiger_visitingorder.purpose,
                            vtiger_visitingorder.visitingorderid AS id 
                            FROM `vtiger_accompanyingvisits` 
                            LEFT JOIN vtiger_visitingorder ON vtiger_visitingorder.visitingorderid=vtiger_accompanyingvisits.visitingorderid
                            LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_visitingorder.related_to
                            WHERE vtiger_accompanyingvisits.userid in(".generateQuestionMarks($data).") AND edate=?";
                    $module='VisitingOrder';
                    $header=array(
                        'accountname',
                        'vuserid',
                        'pfr',
                        'startdate',
                        'enddate',
                        'subject',
                        'modulestatus',
                        'followstatus',
                        'outobjective',
                        'purpose',
                    );
                    break;
                case 'signaccount':
                    $query="SELECT vtiger_signaccount.servicecontractsid AS id,vtiger_servicecontracts.contract_no,
                            (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_servicecontracts.signid=vtiger_users.id) as signid,
                            (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_servicecontracts.receiveid=vtiger_users.id) as receiveid,
                            (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_signaccount.userid=vtiger_users.id) as seruserid,
                            vtiger_servicecontracts.signdate,
                            vtiger_servicecontracts.returndate,
                            vtiger_account.accountname,
                            if(vtiger_signaccount.scalling=100,1,0.5) as scalling
                            FROM vtiger_signaccount 
                            LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_signaccount.servicecontractsid 
                            LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_servicecontracts.sc_related_to
                            WHERE vtiger_signaccount.userid in(".generateQuestionMarks($data).") AND edate=?";
                    $module='ServiceContracts';
                    $header=array(
                        'contract_no',
                        'accountname',
                        'signid',
                        'signdate',
                        'receiveid',
                        'returndate',
                        'seruserid',
                        'scalling'
                    );
                    break;
                case 'amountpaid':
                    $query="SELECT vtiger_receivedpayments.paytitle,
                                vtiger_achievementallot.businessunit,
                                vtiger_achievementallot.postingdate,
                                vtiger_receivedpayments.unit_price,
                                vtiger_receivedpayments.receivedstatus
                            FROM vtiger_achievementallot 
                            LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid 
                            WHERE vtiger_achievementallot.receivedpaymentownid in(".generateQuestionMarks($data).")
                            AND vtiger_achievementallot.postingdate=?";
                    $module='ReceivedPayments';
                    $header=array(
                        'paytitle',
                        'postingdate',
                        'unit_price',
                        'businessunit',
                        'receivedstatus'
                    );
                    break;
                default:
                    $execSqlflag=false;
            }
            if($execSqlflag){
                $return=$this->geteworksituationtrendslistdata(array($query,$param,$module));
                $return['module']=$module;
                $return['thead']=$header;
            }

        }while(0);
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($return);
        $response->emit();
    }

    /**
     * 报表显示数据处理
     * @param $data
     * @return mixed
     */
    public function geteworksituationtrendslistdata($data){
        global $adb;
        $result=$adb->pquery($data[0],$data[1]);
        $return['flag']=false;
        if($adb->num_rows($result)){
            $return['flag']=true;
            while($row=$adb->fetch_array($result)){

                if($data[2]='Accounts'){
                    $row['industry']=vtranslate($row['industry'],'Accounts');
                    $row['regionalpartition']=vtranslate($row['regionalpartition'],'Accounts');
                    $row['makedecision']=vtranslate($row['makedecision'],'Accounts');
                    $row['createdtime']=substr($row['createdtime'],0,10);
                    $row['accountrank']=vtranslate($row['accountrank']);
                }
                if($data[2]='VisitingOrder'){
                    $row['modulestatus']=vtranslate($row['modulestatus']);
                    $row['followstatus']=vtranslate($row['followstatus'],'VisitingOrder');
                }
                $return['data'][]=$row;


            }
        }
        return $return;
    }

    /**
     * 批量保存电话量信息
     *
     * @param Vtiger_Request $request
     * @throws AppException
     */
    public function batchSave(Vtiger_Request $request){
        $telnumberdate=$request->get('telnumberdate');
        $telnumber=$request->get('telnumber');
        $telduration=$request->get('telduration');
        $userid=$request->get('userid');
        $departmentid=$request->get('departmentid');
        $departmentids=$request->get('departmentids');
        $tel_connect_rate = $request->get('tel_connect_rate');
        $total_telnumber = $request->get('total_telnumber');

        global $adb,$current_user;
        $returnData['flag']=false;
        do{
            if(!Users_Privileges_Model::isPermitted('TelStatistics', 'EditView')) {
                $returnData['msg']='没有权限';
                break;
            }
            $userids=getDepartmentUser($departmentid);
            $where=getAccessibleUsers('Accounts','List',true);
            if($where=='1=1'){
                $where=$userids;
            }else{
                $where=array_intersect($where,$userids);
            }

            $insertSql = 'INSERT INTO vtiger_telstatistics(useid,telnumberdate,telnumber,telduration,departmentid,smownerid,createdtime,
                                 deleted,total_telnumber,tel_connect_rate) Values';
            $isInset = false;
            foreach ($userid as $key=>$useid){
//                if(!in_array($useid,$where)){
//                    continue;
//                }
                $query="SELECT telstatisticsid FROM `vtiger_telstatistics` WHERE useid=? AND telnumberdate=?";
                $result=$adb->pquery($query,array($useid,$telnumberdate));
                if($adb->num_rows($result)){
                    $sql = "update vtiger_telstatistics set telnumber=?,telduration=?,total_telnumber=?,
                                tel_connect_rate=? where  useid=? AND telnumberdate=?";
                    $adb->pquery($sql,array(
                        $telnumber[$key],$telduration[$key],$total_telnumber[$key],$tel_connect_rate[$key],$useid,$telnumberdate
                    ));
                    continue;
                }
                $isInset = true;
                $insertSql .= "(".$useid.",'".$telnumberdate."',".$telnumber[$key].','.$telduration[$key].",'".$departmentids[$key]."',".$current_user->id.",'".date('Y-m-d H:i:s')."',".
                    '0,'.$total_telnumber[$key].','.$tel_connect_rate[$key]."),";
            }
            if($isInset){
                $adb->pquery(rtrim($insertSql,','),array());
            }

            $returnData['flag']=true;
            $returnData['msg']=$where;
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($returnData);
        $response->emit();
    }
}
