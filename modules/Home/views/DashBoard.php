<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Home_DashBoard_View extends Vtiger_DashBoard_View {
    function __construct() {
		parent::__construct();
	}

	function preProcess(Vtiger_Request $request, $display = true)
    {
        $request->set('version', 'v2');
        parent::preProcess($request, $display);
    }

    function process(Vtiger_Request $request)
    {
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();

        //$dashBoardModel = Vtiger_DashBoard_Model::getInstance($moduleName);


        //check profile permissions for Dashboards
        $moduleModel = Vtiger_Module_Model::getInstance('Dashboard');
        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        //$permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());

        //2015-03-12 14:51:39 wangbin 读取用户最近客户变化
        $db = PearDatabase::getInstance();
        $currentuser = $userPrivilegesModel->getId();//当前用户id
        //$sql = "SELECT accs.accountname, acc.createdtime, olduser.last_name AS olsuser, newuser.last_name AS newuser, modiuser.last_name AS modiuser FROM `vtiger_accountsmowneridhistory` acc LEFT JOIN vtiger_users olduser ON acc.oldsmownerid = olduser.id LEFT JOIN vtiger_users newuser ON acc.newsmownerid = newuser.id LEFT JOIN vtiger_users modiuser ON acc.modifiedby = modiuser.id LEFT JOIN vtiger_account accs ON acc.accountid = accs.accountid WHERE (acc.newsmownerid = ? OR acc.oldsmownerid = ?) AND datediff(CURDATE(),DATE(acc.createdtime))<=3 ORDER BY acc.id DESC ";
        $isperamid = isPermitted('Accounts', 'DetailView');
        if ($isperamid == 'yes') {
            $sql = "SELECT accs.accountname, acc.createdtime, olduser.last_name AS olsuser, newuser.last_name AS newuser, modiuser.last_name AS modiuser FROM `vtiger_accountsmowneridhistory` acc LEFT JOIN vtiger_users olduser ON acc.oldsmownerid = olduser.id LEFT JOIN vtiger_users newuser ON acc.newsmownerid = newuser.id LEFT JOIN vtiger_users modiuser ON acc.modifiedby = modiuser.id LEFT JOIN vtiger_account accs ON acc.accountid = accs.accountid WHERE acc.accountid>0";


            $where = getAccessibleUsers('Accounts', 'List', true);
            if ($where != '1=1') {
                $sql .= " AND (acc . newsmownerid in (" . implode(',', $where) . ") OR acc . oldsmownerid  in (" . implode(',', $where) . ") ) ";
            }

            $sql .= " ORDER BY acc.createdtime DESC LIMIT 7";
            $accountchange = $db->pquery($sql, array());
            $noofrows = $db->num_rows($accountchange);
            if ($noofrows > 0) {
                $cacheresult = array();
                for ($i = 0; $i < $noofrows; ++$i) {
                    $cacheresult[] = $db->fetch_array($accountchange);
                }
            }
        }else{
            $cacheresult=array();
        }
        //end
        global $current_user;
        //wangbin 当前客户是否是客服；
        $roleid = $current_user->roleid;
        if($roleid=='H85'){
            $if_service = true;
        }
        $roleDatas=array("H79","H80","H81","H82","H95","H78");
        //if(in_array($currentuser,$userarray)||$current_user->is_admin =='on'){
        if(false && in_array($currentuser,$userarray) && in_array($roleid,$roleDatas)){
            if(date('j')==1){
                //1号显示上个月的有效回款
                $datetime = date('Y-m',strtotime('-1 day'));

            }else{
                $datetime = date('Y-m');
            }
        }else{
            $sortresult=array();
            //当前登陆的用户不是中小商务体系的人
            $sortresult['display']=0;
        }
        //end 中小商务本月有效回款业绩排行榜
        $from_login = false;
        // 是否冻结账户
        $is_frozen = 0;
        if($request->get('from')=='login'){
            $from_login = true;
            $query="SELECT 1 FROM vtiger_exportmanage WHERE deleted=0 AND userid=? AND module=? AND classname=?";
            $result=$db->pquery($query,array($current_user->id,'ServiceContracts_alert','altersconfirm'));
            $num=$db->num_rows($result);
            $sql='';
            if($num){
                $where = getAccessibleUsers('ServiceContracts', 'List', true);
                if ($where != '1=1') {
                    $sql .= " AND vtiger_crmentity.smownerid in (" . implode(',', $where) . ")";
                }
            }else{
                $where = getAccessibleUsers('Accounts', 'List', true);
                if ($where != '1=1') {
                    $sql .= " AND vtiger_crmentity.smownerid in (" . implode(',', $where) . ")";
                }
            }
            $confirmlist=array();
            if($current_user->id!=38){
                $confirmdate= date("Y-m-d",strtotime("-25 days"));
                $overdue=date("Y-m-d",strtotime("-31 days"));
                /*$querys="SELECT vtiger_servicecontracts.contract_no,IFNULL(vtiger_extensiontrial.extensiontrialid,0) AS extensiontrialid,vtiger_servicecontracts.servicecontractsid,vtiger_servicecontracts.servicecontractsid,vtiger_servicecontracts.receivedate,vtiger_servicecontracts.delayuserid,vtiger_servicecontracts.confirmlasttime,if(vtiger_servicecontracts.isconfirm=1,DATEDIFF(?,vtiger_servicecontracts.confirmlasttime),DATEDIFF(?,vtiger_servicecontracts.receivedate)) AS diffdate,( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid= vtiger_users.id ) AS userid,vtiger_crmentity.smownerid FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid LEFT JOIN vtiger_extensiontrial ON vtiger_extensiontrial.servicecontractsid=vtiger_crmentity.crmid WHERE deleted=0 AND vtiger_servicecontracts.modulestatus='已发放' AND ((vtiger_servicecontracts.isconfirm=1 AND vtiger_servicecontracts.confirmlasttime<?) OR (vtiger_servicecontracts.isconfirm=0 AND vtiger_servicecontracts.receivedate<?))".$sql.' order by diffdate';*/
                $querys="SELECT (SELECT COUNT(vtiger_extensiontrial.extensiontrialid) FROM vtiger_extensiontrial WHERE vtiger_extensiontrial.servicecontractsid=vtiger_servicecontracts.servicecontractsid) AS extensionnum,vtiger_servicecontracts.contract_no, vtiger_servicecontracts.isconfirm AS isconfirm, vtiger_servicecontracts.servicecontractsid, vtiger_servicecontracts.servicecontractsid, vtiger_servicecontracts.receivedate, vtiger_servicecontracts.delayuserid, vtiger_servicecontracts.confirmlasttime, IF ( vtiger_servicecontracts.isconfirm > 0, DATEDIFF( ?, vtiger_servicecontracts.confirmlasttime ), DATEDIFF( ?, vtiger_servicecontracts.receivedate )) AS diffdate, ( SELECT CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ) ) ) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid = vtiger_users.id ) AS userid, vtiger_crmentity.smownerid FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid WHERE deleted = 0 AND vtiger_servicecontracts.modulestatus = '已发放' AND ( ( vtiger_servicecontracts.isconfirm = 1 AND vtiger_servicecontracts.confirmlasttime <? ) OR ( vtiger_servicecontracts.isconfirm = 0 AND vtiger_servicecontracts.receivedate <? ))".$sql.' order by diffdate';

                $confirmResult=$db->pquery($querys,array($overdue,$overdue,$confirmdate,$confirmdate));
                $confirmNum=$db->num_rows($confirmResult);
                if($confirmNum>0){
                    for($i=0;$i<$confirmNum;$i++){
                        $row=$db->fetch_array($confirmResult);
                        //$row['diffdate']=$row['diffdate']=='-5'?'<span class="label label-success">'.$row['diffdate'].'</span>':($row['diffdate']=='-4'?'<span class="label label-b_check">'.$row['diffdate'].'</span>':($row['diffdate']=='-3'?'<span class="label label-a_normal">'.$row['diffdate'].'</span>':($row['diffdate']=='-2'?'<span class="label label-warning">'.$row['diffdate'].'</span>':($row['diffdate']=='-1'?'<span class="label label-a_exception">'.$row['diffdate'].'</span>':'<span class="label label-inverse">'.$row['diffdate'].'</span>'))));

                        //是否可以延期申请
                        if (in_array($row['isconfirm'], array('0', '1')) && ($row['extensionnum'] < 2) && $row['smownerid']==$current_user->id) {
                            $row['add'] = '1';
                        }
                        if ($row['isconfirm'] == 0 && $row['extensionnum'] == 1) {
                            $row['add'] = '0';
                        }

                        $row['type'] = '服务合同';

                        $confirmlist[]=$row;
                    }
                }

            }

            $viewer->assign('CONFIRMLIST', $confirmlist);


            global $current_user;
            $roles = array(
                'H154','H153','H79','H80'
            );
            $re_sql = '';
            if(in_array($current_user->roleid,$roles)){
                $myusers = getAccessibleUsers('ServiceContracts', 'List', true);
                if ($myusers != '1=1') {
                    $re_sql = ','.implode(',',$myusers);
                }
            }

            //获取被驳回的合同
            $reject_sql = "SELECT
	a.id,
	b.servicecontractsid,
	b.contract_no,
	f.accountname,
	concat (c.last_name,'[',c.department,']',( IF ( c.status = 'Active', '', '[离职]' ) )) AS rejectname,
	concat (d.last_name,'[',d.department,']',( IF ( d.status = 'Active', '', '[离职]' ) )) AS receivename,
	concat (e.last_name,'[',e.department,']',( IF ( e.status = 'Active', '', '[离职]' ) )) AS signname,
	b.receiveid,
	b.signid,
	a.rejectid,
	a.rejecttime,
	a.reason,
	a.status,
	a.relationid 
FROM
	vtiger_servicecontract_relation a
	LEFT JOIN vtiger_servicecontracts b ON a.servicecontractsid = b.servicecontractsid
	LEFT JOIN vtiger_users c ON c.id = a.rejectid
	LEFT JOIN vtiger_users d ON d.id = b.receiveid
	LEFT JOIN vtiger_users e ON e.id = b.signid 
	left join vtiger_account f on b.sc_related_to = f.accountid
WHERE
	a.status = 0
	AND
	( b.receiveid IN ( ".$current_user->id.$re_sql." ) OR b.signid IN ( ".$current_user->id.$re_sql." ) )";
            $servicecontract_relations = $db->pquery($reject_sql);
            $data = array();
            if($db->num_rows($servicecontract_relations)){
                while ($relation_row = $db->fetch_row($servicecontract_relations)){
                    $data[] = $relation_row;
                }
            }
            $viewer->assign('SERVICECONTRACT_RELATION',$data);
        };

        $sel_no_contractid = "SELECT * FROM vtiger_receivedpayments WHERE relatetoid=0 AND maybe_account=0 AND receivedstatus='normal'";
        $no_contractid_result = $db->pquery($sel_no_contractid,array());
        $no_contractli = array();
        if($db->num_rows($no_contractid_result)>0){
            for($i=0;$i<$db->num_rows($no_contractid_result);$i++){
                $no_contractli[]=$db->fetch_array($no_contractid_result);
            }
            $sum_no_contractid = "SELECT SUM(unit_price) AS sum FROM vtiger_receivedpayments WHERE relatetoid=0 AND maybe_account=0 AND receivedstatus='normal'";
            $sum_no_contractid_result = $db->pquery($sum_no_contractid,array());
            $sum_lis = $db->fetchByAssoc($sum_no_contractid_result,0);
            $sum_li = $sum_lis['sum'];
        }
        if(empty($sum_li)){
            $sum_li = 0;
        }

        // 一周内新增商机列表
        $t_time = strtotime('-7 day');
        $leads_sql = "SELECT
            company,
            industry,
            leadsource,
            lastname,
            phone,
            vtiger_leadaddress.mobile
        FROM
            vtiger_leaddetails
        LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_leaddetails.leadid
        LEFT JOIN vtiger_leadaddress ON vtiger_leadaddress.leadaddressid = vtiger_leaddetails.leadid
        WHERE vtiger_crmentity.smownerid=" . $current_user->id ." AND unix_timestamp(createdtime)>".$t_time." ORDER BY createdtime DESC LIMIT 10";
        $leadsdata = $db->run_query_allrecords($leads_sql);
        // 超过7天未跟进商机列表
        $searchDepartment = null;
        if(empty($searchDepartment)){
            $searchDepartment = 'H1';
        }
        $where=getAccessibleUsers('Leads','List',true);
        $userid=getDepartmentUser($searchDepartment);
        $listQuery = '';
        if(!empty($searchDepartment)){
            if(!empty($where)&&$where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $listQuery .= ' and (vtiger_crmentity.smownerid in ('.implode(',',$where).') or vtiger_leaddetails.assigner='.$current_user->id.')';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                $listQuery .= ' and (vtiger_crmentity.smownerid '.$where.' or vtiger_leaddetails.assigner='.$current_user->id.')';
            }
        }
        $listQuery = "SELECT
                        vtiger_leaddetails.company,
                        vtiger_leaddetails.lastname,
                        vtiger_leadaddress.phone,
                        vtiger_leadaddress.mobile,
                        vtiger_leaddetails.leadsource
                    FROM
                        vtiger_leaddetails
                    LEFT JOIN vtiger_crmentity ON vtiger_leaddetails.leadid = vtiger_crmentity.crmid
                    LEFT JOIN vtiger_leadsubdetails ON vtiger_leaddetails.leadid = vtiger_leadsubdetails.leadsubscriptionid
                    LEFT JOIN vtiger_leadaddress ON vtiger_leaddetails.leadid = vtiger_leadaddress.leadaddressid
                    LEFT JOIN vtiger_leadscf ON vtiger_leaddetails.leadid = vtiger_leadscf.leadid
                    LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_leaddetails.accountid
                    WHERE
                        1 = 1
                    AND vtiger_crmentity.deleted = 0 " . $listQuery;
        $listQuery = $listQuery . " AND vtiger_leaddetails.mapcreattime >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
                                    AND vtiger_leaddetails.assignerstatus != 'c_cancelled'
                                    AND vtiger_leaddetails.assignerstatus != 'c_complete'
                                    AND (vtiger_leaddetails.commenttime <= DATE_SUB(CURDATE(), INTERVAL 7 DAY) OR ISNULL(vtiger_leaddetails.commenttime) )
                                    AND vtiger_crmentity.createdtime < DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                                    ORDER BY
                                        vtiger_leaddetails.leadid DESC
                                    LIMIT 0,10";
        $more_leadsdata = $db->run_query_allrecords($listQuery);


        //屏蔽客服详情跟进展示，因为客服系统不在erp了，系统里仅仅只是备份
//        $sel_returnlist_sql ="SELECT vtiger_servicecomments_returnplan.uppertime, vtiger_servicecomments_returnplan.lowertime, vtiger_servicecomments_returnplan.reviewcontent, IF (isfollow = 1, '是', '否') AS isfollow, vtiger_servicecomments_returnplan.sort, (vtiger_account.accountname) AS accountid, vtiger_servicecomments_returnplan.accountid AS accountid_reference, ( vtiger_servicecomments.allocatetime ) AS commentsid, vtiger_servicecomments_returnplan.commentsid AS commentsid_reference, ( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_servicecomments.serviceid = vtiger_users.id ) AS serviced, vtiger_servicecomments_returnplan.commentreturnplanid FROM vtiger_servicecomments_returnplan LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_servicecomments_returnplan.accountid LEFT JOIN vtiger_servicecomments ON vtiger_servicecomments.servicecommentsid = vtiger_servicecomments_returnplan.commentsid WHERE 1 = 1 AND vtiger_servicecomments.serviceid =".$currentuser. " AND SYSDATE() BETWEEN uppertime AND lowertime ORDER BY vtiger_servicecomments_returnplan.commentsid, vtiger_servicecomments_returnplan.sort";
//        $reutnlist = $db->run_query_allrecords($sel_returnlist_sql);
        $reutnlist=array();
        $query="SELECT vtiger_knowledge.knowledgetitle,vtiger_knowledge.knowledgeid,knowledgedate FROM vtiger_knowledge WHERE knowledgecolumns='updateannouncement' AND `open`=1 ORDER BY knowledgeid DESC LIMIT 1";
        $knowledgeResult=$db->pquery($query,array());
        $knowledgeData=array();
        if($db->num_rows($knowledgeResult)){
            $knowledgeData=$db->raw_query_result_rowdata($knowledgeResult,0);
        }
        //T云问答平台 链接数据
        $salt = 'sq0429';//盐值(不用变)
        $check_str = md5(md5($current_user->user_name . $current_user->email1) . $salt);//验证字符串
        $viewer->assign('USER_NAME', $current_user->user_name);
        $viewer->assign('EMAIL', $current_user->email1);
        $viewer->assign('CHECK_STR', $check_str);
        $viewer->assign('LEADSDATA', $leadsdata);
        $viewer->assign('MORE_LEADSDATA', $more_leadsdata);

        $viewer->assign('KNOWLEDGEDATA', $knowledgeData);

        $record=Home_Module_Model::getAccountNoSeven();
        $viewer->assign('ACCCHANGE', $cacheresult);
        $viewer->assign('RECORDNOSEVEN',$record);
        $viewer->assign('SORTARR',$sortresult);
        $viewer->assign('IF_SERVICE',$if_service);
        $viewer->assign("KNOWLEDGERECORD",Knowledge_Record_Model::getindexlist());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('NO_CONTRACTLI',$no_contractli);
        $viewer->assign('FROM_LOGIN',$from_login);
        $viewer->assign('SUM_LI',$sum_li);
        $viewer->assign('REUTNLIST',$reutnlist);
        $viewer->assign('CURRENT_USER', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('FROM', $request->get('from'));





        $viewer->view('dashboards/DashBoardContents.tpl', $moduleName);
    }

    public function postProcess(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $viewer = $this->getViewer($request);
        $viewer->view('dashboards/DashBoardPostProcess.tpl', $moduleName);
    }
}