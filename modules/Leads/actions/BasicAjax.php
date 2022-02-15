<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Leads_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('searchAccount');
		$this->exposeMethod('getfollowup');
		$this->exposeMethod('changcategroy');
		$this->exposeMethod('searchDeal');
		$this->exposeMethod('getUpdateUser');
		$this->exposeMethod('getListUser');
		$this->exposeMethod('getLeadsDeal');
		$this->exposeMethod('setLeadsSetting');
		$this->exposeMethod('setLeadsSettingDepart');
		$this->exposeMethod('deleteUser');
		$this->exposeMethod('getLeadsBatchList');
		$this->exposeMethod('setLeadsChangeUser');
		$this->exposeMethod('setLeadsChangeGonghai');
		$this->exposeMethod('filterSource');
		$this->exposeMethod('setShare');
		$this->exposeMethod('deleteShareSetting');
		$this->exposeMethod('getDepartmentUsers');
		$this->exposeMethod('setAssignPersonal');
		$this->exposeMethod('deleteAssignPersonal');
		$this->exposeMethod('updateAssignNum');
		$this->exposeMethod('filterSourceNum');
		$this->exposeMethod('getDepartmentsByDepth');
		$this->exposeMethod('getBelongSystem');
		$this->exposeMethod('isOverProtect');
	}
	
	function checkPermission(Vtiger_Request $request) {
		return;
	}


    /**
     * 2015-1-13 wangbin 商机客户查找
     * index.php?module=ServiceContracts&action=BasicAjax&record=合同id&mode=receivepay
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function searchDeal(Vtiger_Request $request){
        $subtimetype  = $request->get('subtimetype');
        $datetime  = $request->get('datatime');
        $enddatetime  = $request->get('enddatetime');

        if($subtimetype=='signtime'){
            $timetype='vtiger_servicecontracts.signdate';
        }else{
            $timetype='vtiger_servicecontracts.returndate';
        }
        if(strtotime($datetime)>strtotime($enddatetime)){
            $tempdate=" AND left({$timetype},10) BETWEEN '{$enddatetime}' AND '{$datetime}'";
        }elseif(strtotime($datetime)<strtotime($enddatetime)){
            $tempdate=" AND left({$timetype},10) BETWEEN '{$datetime}' AND '{$enddatetime}'";
        }else{
            $tempdate=" AND left({$timetype},10)='{$datetime}'";
        }
        if($datetime==''){

            $tempdate=" AND left({$timetype},10)='".date('Y-m-d')."'";
        }
        $db=PearDatabase::getInstance();
        $result = $db->pquery("SELECT vtiger_servicecontracts.contract_no,IFNULL(vtiger_account.accountname,'') AS accountname,{$timetype} as datetime,IFNULL((SELECT last_name FROM vtiger_users LEFT JOIN vtiger_crmentity ON vtiger_users.id=vtiger_crmentity.smownerid WHERE vtiger_crmentity.crmid=vtiger_servicecontracts.sc_related_to),'') AS username FROM vtiger_servicecontracts LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_servicecontracts.sc_related_to LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.modulestatus='c_complete' {$tempdate} order by vtiger_servicecontracts.servicecontractsid desc limit 2000",array());

        $row=$db->num_rows($result);
        $lis=array();
        if($row>0){
            for ($i=0; $i<$row; ++$i) {
                $li = $db->fetchByAssoc($result);
		$lis[]=$li;

            }
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($lis);
        $response->emit();
    }
    /**
     * 批量转换的商机列表
     * @param Vtiger_Request $request
     */
    public function setLeadsChangeUser(Vtiger_Request $request){

        $leadids  = $request->get('leadids');

        $userid = $request->get('userid');
        do{
            if(!Leads_Module_Model::exportGrouprt('Leads', 'leadbatch')){
                break;
            }

            if(!is_numeric($userid)) {
                break;

            }
            $db = PearDatabase::getInstance();
            $datetime=date("Y-m-d H:i:s");
            global $current_user;
            $leadRecordModel = Leads_Record_Model::getCleanInstance("Leads");
            $leadRecordModel->batchSendAllocateMail($leadids,$userid);
            foreach($leadids as $value){
                if(is_numeric($value)){
                    $result = $db->pquery("SELECT smownerid FROM vtiger_crmentity WHERE crmid=?", array($value));
                    $resultdata = $db->query_result($result, 0);

                    $db->pquery("update vtiger_leaddetails set cluefollowstatus='tobecontact' where leadid=?",array($value));

                    $db->pquery("UPDATE vtiger_crmentity SET smownerid=? WHERE crmid=?", array($userid, $value));
                    $id = $db->getUniqueId('vtiger_modtracker_basic');
                    $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                        array($id, $value, 'Leads', $current_user->id, $datetime, 0));

                    $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                        Array($id, 'assigned_user_id', $resultdata, $userid));
                    $recordModel=Vtiger_Record_Model::getInstanceById($value,'Leads');
                    $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                        Array($id, 'cluefollowstatus', $recordModel->entity->column_fields['cluefollowstatus'], 'tobecontact'));
                }
            }
        }while(0);
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array());
        $response->emit();
    }
    /**
     * 批量转换的商机列表公海
     * @param Vtiger_Request $request
     */
    public function setLeadsChangeGonghai(Vtiger_Request $request){

        $leadids  = $request->get('leadids');
        do{
            if(!Leads_Module_Model::exportGrouprt('Leads', 'leadbatch')){
                break;
            }
            $db = PearDatabase::getInstance();
            $datetime=date("Y-m-d H:i:s");
            global $current_user;
            foreach($leadids as $value){
                if(is_numeric($value)){
                    $db->pquery("UPDATE vtiger_leaddetails SET leadcategroy=2,cluefollowstatus='nostatus' WHERE leadid=?", array($value));
                    $id = $db->getUniqueId('vtiger_modtracker_basic');
                    $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                        array($id, $value, 'Leads', $current_user->id, $datetime, 0));

                    $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                        Array($id, 'leadcategroy', 0, 2));
                    $recordModel=Vtiger_Record_Model::getInstanceById($value,'Leads');
                    $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                        Array($id, 'cluefollowstatus', $recordModel->entity->column_fields['cluefollowstatus'], 'nostatus'));
                }
            }
        }while(0);
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array());
        $response->emit();
    }
    public function getLeadsBatchList(Vtiger_Request $request){
        $departmentid  = $request->get('departmentid');
        $status = $request->get('status');
        $userid = $request->get('userid');

        $userid=is_numeric($userid)?$userid:0;
        if(!empty($userid)){
            $sql=" AND vtiger_crmentity.smownerid={$userid}";
        }else{
            if(empty($departmentid)){
                $departmentid = 'H1';
            }
            $where=getAccessibleUsers('Leads','List',true);
            $userid=getDepartmentUser($departmentid);
            $sql = '';
            if(!empty($departmentid)){
                if(!empty($where)&&$where!='1=1'){
                    $where=array_intersect($where,$userid);
                }else{
                    $where=$userid;
                }
                $sql .= ' AND vtiger_crmentity.smownerid in ('.implode(',',$where).')';
            }else{
                $where=getAccessibleUsers();
                if($where!='1=1'){
                    $sql .= ' AND vtiger_crmentity.smownerid '.$where;
                }
            }
        }
        $statusarray=array('a_not_allocated','c_allocated');
        if(!in_array($status,$statusarray)){

            $status='c_allocated';
        }

        $db=PearDatabase::getInstance();

        $result = $db->pquery("SELECT
                                    vtiger_leaddetails.leadid,
                                    IFNULL(vtiger_account.accountname,'') AS accountname,
                                    IFNULL(vtiger_leaddetails.company,'') AS company,
                                    IFNULL(vtiger_users.last_name,'') AS last_name,
                                    IFNULL(vtiger_leaddetails.mapcreattime,'') AS mapcreattime,
                                    IFNULL(vtiger_leaddetails.commenttime,'') AS commenttime,
                                    IFNULL(vtiger_leaddetails.allocatetime,'') AS allocatetime,
                                    IFNULL(vtiger_leaddetails.assignerstatus,'') AS assignerstatus
                                FROM
                                    vtiger_leaddetails
                                LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_leaddetails.leadid
                                LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_leaddetails.accountid
                                LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
                                WHERE
                                    vtiger_crmentity.deleted = 0
                                AND vtiger_leaddetails.leadcategroy =0
                                AND vtiger_leaddetails.assignerstatus =?
                                {$sql} limit 2000",array($status));

        $row=$db->num_rows($result);
        $lis=array();
        if($row>0){
            for ($i=0; $i<$row; ++$i) {
                $li = $db->fetchByAssoc($result);
                $li['assignerstatus'] =vtranslate($li['assignerstatus'],'Leads');
                $lis[]=$li;

            }
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($lis);
        $response->emit();
    }

    /**成交商机列表
     * @param Vtiger_Request $request
     */
    public function getLeadsDeal(Vtiger_Request $request){
        $datetime  = $request->get('datatime');
        $enddatetime  = $request->get('enddatetime');


        if(strtotime($datetime)>strtotime($enddatetime)){
            $tempdate=" AND left(vtiger_servicecontracts.signdate,10) BETWEEN '{$enddatetime}' AND '{$datetime}'";
        }elseif(strtotime($datetime)<strtotime($enddatetime)){
            $tempdate=" AND left(vtiger_servicecontracts.signdate,10) BETWEEN '{$datetime}' AND '{$enddatetime}'";
        }else{
            $tempdate=" AND left(vtiger_servicecontracts.signdate,10)='{$datetime}'";
        }
        if($datetime==''){

            $tempdate=" AND left(vtiger_servicecontracts.signdate,10)='".date('Y-m-d')."'";
        }
        $db=PearDatabase::getInstance();

        $result = $db->pquery("SELECT
                                vtiger_account.accountname,
                                vtiger_servicecontracts.contract_no,
                                vtiger_servicecontracts.contract_type,
                                '是' AS firstfrommarket,
                                IF(firstcontract = 1,'是','否') AS firstcontract,
                                vtiger_servicecontracts.total,
                                (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_servicecontracts.signid=vtiger_users.id) as signid,
                                IFNULL(vtiger_servicecontracts.signdate,'') AS signdate,
                                IFNULL(vtiger_servicecontracts.signdempart,'') AS signdempart,
                                IFNULL((SELECT sum(IFNULL(vtiger_receivedpayments.unit_price,0)) FROM vtiger_receivedpayments WHERE vtiger_receivedpayments.receivedstatus='normal' AND vtiger_receivedpayments.relatetoid = vtiger_servicecontracts.servicecontractsid),0) AS allpayments
                            FROM
                                vtiger_servicecontracts
                            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid
                            LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_servicecontracts.sc_related_to

                            WHERE
                                vtiger_crmentity.deleted = 0
                            AND vtiger_servicecontracts.firstfrommarket = 1
                            AND vtiger_servicecontracts.modulestatus = 'c_complete'
                            AND vtiger_servicecontracts.signdate {$tempdate} order by vtiger_servicecontracts.servicecontractsid desc limit 2000",array());

        $row=$db->num_rows($result);
        $lis=array();
        if($row>0){
            include 'crmcache/departmentanduserinfo.php';
            for ($i=0; $i<$row; ++$i) {
                $li = $db->fetchByAssoc($result);
                $li['signdempart'] =  empty($cachedepartment[$li['signdempart']])?'':$cachedepartment[$li['signdempart']];
                $lis[]=$li;

            }
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($lis);
        $response->emit();
    }
    /**
     *
     * @param Vtiger_Request $request
     */
    public function deleteUser(Vtiger_Request $request){

        if(Leads_Module_Model::exportGrouprt('Leads', 'leadsetting')) {
            $user_id  = $request->get('id');
            $departmentid  = $request->get('departmentid');
            $db = PearDatabase::getInstance();
            $query="DELETE FROM vtiger_sendmail_leads WHERE status='c_allocated' AND module='Leads' AND userid=? AND departmentid=?";
            $db->pquery($query, array($user_id,$departmentid));

        }

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array());
        $response->emit();
    }
    /**
     *设置部门应对的收件人
     * index.php?module=ServiceContracts&action=BasicAjax&record=合同id&mode=receivepay
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function setLeadsSettingDepart(Vtiger_Request $request){

        if(Leads_Module_Model::exportGrouprt('Leads', 'leadsetting')) {
            $user_id  = $request->get('user_id');
            $departmentid  = $request->get('departmentid');
            if(is_array($user_id)){
                $str='';
                foreach($user_id as $key=>$value){
                    $str.="({$value},'c_allocated','Leads','{$departmentid}'),";
                }
                $str=rtrim($str,',');
                if(!empty($str)){
                    $db = PearDatabase::getInstance();
                    $query='INSERT INTO vtiger_sendmail_leads (userid,`status`,`module`,departmentid) VALUES'.$str.' ON DUPLICATE KEY UPDATE userid=userid';
                    $db->pquery($query, array());
                }
            }
        }

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array());
        $response->emit();
    }
    /**
     *setLeadsSettingDepart
     * index.php?module=ServiceContracts&action=BasicAjax&record=合同id&mode=receivepay
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function setLeadsSetting(Vtiger_Request $request){

        if(Leads_Module_Model::exportGrouprt('Leads', 'leadsetting')) {
            $allocationaftertracking  = $request->get('allocationaftertracking');
            $longesttracking  = $request->get('longesttracking');
            $smower  = $request->get('smower');
            $oldsmower  = $request->get('oldsmower');
            $reportto  = $request->get('reportto');
            $departmentdesignated  = $request->get('departmentdesignated');
            $fixedpersonnel  = $request->get('fixedpersonnel');
            $fixedpersonnellist  = $request->get('fixedpersonnellist');
            $protectday  = $request->get('protectday');
            $allocationaftertracking=is_numeric($allocationaftertracking)?abs($allocationaftertracking):0;
            $longesttracking=is_numeric($longesttracking)?abs($longesttracking):0;
            $smower=$smower==1?1:0;
            $oldsmower=$oldsmower==1?1:0;
            $reportto=$reportto==1?1:0;
            $departmentdesignated=$departmentdesignated==1?1:0;
            $fixedpersonnel=$fixedpersonnel==1?1:0;
            $db = PearDatabase::getInstance();
            $delsql="DELETE FROM vtiger_sendmail_leads WHERE status='c_fixed' AND module='Leads'";
            $db->pquery($delsql, array());
            if(is_array($fixedpersonnellist)){
                $str='';
                foreach($fixedpersonnellist as $key=>$value){
                    $str.="({$value},'c_fixed','Leads'),";
                }
                $str=rtrim($str,',');
                if(!empty($str)){
                    $query='INSERT INTO vtiger_sendmail_leads (userid,`status`,`module`) VALUES'.$str;
                    $db->pquery($query, array());
                }

            }

            $db->pquery("REPLACE INTO vtiger_sendmail_lead_setting(sendmailleadsettingid,`allocationaftertracking`,`longesttracking`,`smower`,`reportto`,`fixedpersonnel`,`departmentdesignated`,`oldsmower`,`protectday`) VALUES(?,?,?,?,?,?,?,?,?)", array(1,$allocationaftertracking,$longesttracking,$smower,$reportto,$fixedpersonnel,$departmentdesignated,$oldsmower,$protectday));




        }

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array());
        $response->emit();
    }
    /**
     * 2015-1-13 wangbin 商机客户查找
     * index.php?module=ServiceContracts&action=BasicAjax&record=合同id&mode=receivepay
     * @param Vtiger_Request $request
     * @throws Exception
     */
	public function getUpdateUser(Vtiger_Request $request){

        if(Leads_Module_Model::exportGrouprt('Leads', 'accountsearch')) {
            $accountid  = $request->get('id');
            $userid  = $request->get('userid');
            $datetime=date('Y-m-d H:i:s');
            global $current_user;
            $db = PearDatabase::getInstance();
            $result = $db->pquery("SELECT smownerid FROM vtiger_crmentity WHERE crmid=?", array($accountid));
            $resultdata = $db->query_result($result, 0);

            $db->pquery("UPDATE vtiger_crmentity SET smownerid=? WHERE crmid=?", array($userid, $accountid));
            $id = $db->getUniqueId('vtiger_modtracker_basic');
            $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                array($id, $accountid, 'Accounts', $current_user->id, $datetime, 0));

            $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, 'assigned_user_id', $resultdata, $userid));

        }

		$response = new Vtiger_Response();
		$response->setEmitType(Vtiger_Response::$EMIT_JSON);
		$response->setResult(array());
		$response->emit();
	}
    public function getListUser(){

        $db=PearDatabase::getInstance();
        $result = $db->pquery("SELECT vtiger_users.id,IFNULL(vtiger_departments.departmentname,'--') as departmentname,vtiger_users.last_name,vtiger_departments.departmentid FROM vtiger_users
                               LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id
                               LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid
                               WHERE vtiger_users.`status`='Active' and vtiger_users.id!=1 order by vtiger_departments.departmentid",array());

        $row=$db->num_rows($result);
        $lis=array();
        if($row>0){
            for ($i=0; $i<$row; ++$i) {
                $li = $db->fetchByAssoc($result);
                $lis[$i]['id']=$li['id'];
                $lis[$i]['departmentname']=$li['departmentname'];
                $lis[$i]['departmentid']=$li['departmentid'];
                $lis[$i]['username']=$li['last_name'];

            }
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($lis);
        $response->emit();
    }
    public function searchAccount(Vtiger_Request $request){
        $accountname  = $request->get('accountname');
        $db=PearDatabase::getInstance();
        $result = $db->pquery("SELECT vtiger_account.accountname,accountid,accountrank,IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id),'--') as smownerid FROM vtiger_account LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid WHERE vtiger_crmentity.deleted=0 AND vtiger_account.accountrank in('chan_notv','forp_notv') AND vtiger_account.accountname LIKE ? limit 10",array('%'.$accountname.'%'));

        $row=$db->num_rows($result);
        $lis=array();
        if($row>0){
            for ($i=0; $i<$row; ++$i) {
                $li = $db->fetchByAssoc($result);
                $lis[$i]['name']=$li['accountname'];
                $lis[$i]['rank']=vtranslate($li['accountrank'],'Vtiger');
                $lis[$i]['id']=$li['accountid'];
                $lis[$i]['owerid']=$li['smownerid'];
            }
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($lis);
        $response->emit();
    }
    public function getfollowup(Vtiger_Request $request){
        $id  = $request->get('id');
        $db=PearDatabase::getInstance();
        $result = $db->pquery("SELECT commentcontent,addtime,IFNULL((SELECT last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_modcomments.creatorid),'') AS username,IFNULL((if(vtiger_modcomments.related_to=vtiger_modcomments.contact_id,(SELECT vtiger_account.linkname FROM vtiger_account WHERE vtiger_account.accountid=vtiger_modcomments.contact_id),(SELECT vtiger_contactdetails.name FROM vtiger_contactdetails WHERE vtiger_contactdetails.contactid=vtiger_modcomments.contact_id))),'') AS contactname,IFNULL(vtiger_modcomments.modcommentpurpose,'') AS modcommentpurpose,IFNULL(vtiger_modcomments.modcommenttype,'') AS modcommenttype,IFNULL(vtiger_modcomments.modcommentmode,'') AS modcommentmode FROM `vtiger_modcomments` WHERE modulename='Accounts' AND related_to=? ORDER BY vtiger_modcomments.modcommentsid desc limit 10",array($id));

        $row=$db->num_rows($result);
        $lis=array();
        if($row>0){
            for ($i=0; $i<$row; ++$i) {
                $li = $db->fetchByAssoc($result);
                $lis[]=$li;
            }
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($lis);
        $response->emit();
    }

    /**
     * 商机线索领取
     * @param Vtiger_Request $request
     */
    public function changcategroy(Vtiger_Request $request){
        $recordId = $request->get('record');
        if(empty($recordId)){
            exit;
        }
        $type=$request->get('type');
        //数据权限与列表一致
        vglobal('currentView','List');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'Leads');
        $entity=$recordModel->entity->column_fields;
        //受保护的客户
        global  $current_user;
        $result1 = array('success'=>true);
        /*
         * 'c_transformation'=>'已转换',
         *'c_cancelled'=>'已作废',
         *'c_Forced_Related'=>'强制关联',
         *'c_Related'=>'已关联',
         *'c_complete'=>'已成交',
         */
        $flages=false;
        do {
            if ($entity['assignerstatus'] == 'c_transformation') {
                $result1 = array('success' => false, 'message' => '已转化的商机不允许进行此操作！');
                $flages=true;
                break;
            }
            /*if ($entity['assignerstatus'] == 'c_cancelled') {
                $result1 = array('success' => false, 'message' => '已作废的商机不允许进行此操作！');
                $flages=true;
                break;
            }*/
            if ($entity['assignerstatus'] == 'c_Forced_Related') {
                $result1 = array('success' => false, 'message' => '已关联的商机不允许进行此操作！');
                $flages=true;
                break;
            }
            if ($entity['assignerstatus'] == 'c_Related') {
                $result1 = array('success' => false, 'message' => '已关联的商机不允许进行此操作！');
                $flages=true;
                break;
            }
            if ($entity['assignerstatus'] == 'c_complete') {
                $result1 = array('success' => false, 'message' => '已成交的商机不允许进行此操作！');
                $flages=true;
                break;
            }
        }while(0);
        if($flages){
            echo json_encode($result1);
            exit;
        }
        if($type=='OVERT' && $entity['leadcategroy']!=2 ){

            $datetime=date('Y-m-d H:i:s');
            $db=PearDatabase::getInstance();
            $id = $db->getUniqueId('vtiger_modtracker_basic');
            $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                array($id , $recordId, 'Leads', $current_user->id, $datetime, 0));

            $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, 'leadcategroy',0, 2));
            $db->pquery('UPDATE `vtiger_leaddetails` SET leadcategroy=2,cluefollowstatus="nostatus" WHERE leadid=?',Array($recordId));
            $db->pquery("Update vtiger_crmentity set smownerid='' where crmid=?",array($recordId));
        }elseif ($type=='SELF' && $entity['leadcategroy']!=0){
            $datetime=date('Y-m-d H:i:s');
            $db=PearDatabase::getInstance();
            $id = $db->getUniqueId('vtiger_modtracker_basic');
            $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                array($id , $recordId, 'Leads', $current_user->id, $datetime, 0));
            $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, 'leadcategroy',2, 0));
            $db->pquery('UPDATE `vtiger_leaddetails` SET leadcategroy=0,cluefollowstatus="tobecontact" WHERE leadid=?',Array($recordId));
            $db->pquery("Update vtiger_crmentity set smownerid=? where crmid=?",array($current_user->id,$recordId));
        }else{
            $result1 = array('success'=>false,'message'=>'错误的操作！');
        }
        echo json_encode($result1);


    }

    /**
     * @param Vtiger_Request $request
     * @throws Exception
     */
	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}

	}

	public function filterSource(Vtiger_Request $request){
	    $leadsource=$request->get('leadsource');
        $recordModel= Leads_Record_Model::getCleanInstance("Leads");
        $data = $recordModel->filterSource($leadsource);
        $assignList = $recordModel->getAssignUsers();
        echo json_encode(array("success"=>true,'msg'=>'成功','data'=>array(
            "filterSource"=>$data,
            'assignList'=>$assignList
        )));
    }

    public function getDepartmentsByDepth(Vtiger_Request $request){
        $recordModel= Leads_Record_Model::getCleanInstance("Leads");
        $data = $recordModel->getDepartmentsByDepth();
        echo json_encode(array("success"=>true,'msg'=>'成功','data'=>$data));
    }

    public function filterSourceNum(Vtiger_Request $request)
    {
        $leadsourcetnum=$request->get('leadsourcetnum');
        $recordModel= Leads_Record_Model::getCleanInstance("Leads");
        $data = $recordModel->filterSourceNum($leadsourcetnum);

        echo json_encode(array("success"=>true,'msg'=>'成功','data'=>$data));
    }

    public function setShare(Vtiger_Request $request){
	    global $current_user;
	    if(!$request->get('starttime')){
	        $data = array("success"=>false,'msg'=>'启用时间必填');
	        echo json_encode($data);
	        exit();
        }
        $db = PearDatabase::getInstance();
        if($request->get("record")){
            $sql = "update vtiger_leadsharesetting set starttime=?,promotionsharing=?,salesharing=?,sharetype=?,remark=? where leadsharesettingid=?";
            $db->pquery($sql,array($request->get('starttime'),$request->get('promotionsharing'),
                $request->get('salesharing'),$request->get('sharetype'),$request->get('remark'),$request->get("record")));
            echo json_encode(array("success"=>true,'msg'=>'修改成功'));
        }else{
            $sql = "insert into vtiger_leadsharesetting (`userid`, `starttime`, `promotionsharing`,`salesharing`,`sharetype`,`createdtime`,`remark`) VALUES(?,?,?,?,?,?,?)";
            $db->pquery($sql,array($current_user->id,$request->get('starttime'),$request->get('promotionsharing'),
                $request->get('salesharing'),$request->get('sharetype'),date("Y-m-d H:i:s"),$request->get('remark')));
            echo json_encode(array("success"=>true,'msg'=>'创建成功'));
        }

    }

    public function deleteAssignPersonal(Vtiger_Request $request){
        $id =$request->get("id");
        $db=PearDatabase::getInstance();
        $db->pquery("delete from vtiger_leadassignpersonnel where leadassignpersonnelid=?",array($id));
        echo json_encode(array("success"=>true,'msg'=>'删除成功'));
    }

    public function deleteShareSetting(Vtiger_Request $request){
	    $id =$request->get("id");
	    $db=PearDatabase::getInstance();
        $result = $db->pquery("select starttime from vtiger_leadsharesetting where leadsharesettingid=?",array($id));
	    if(!$db->num_rows($result)){
            echo json_encode(array("success"=>false,'msg'=>'删除失败'));
            exit();
        }
	    $row=$db->fetchByAssoc($result,0);
	    if(strtotime($row['starttime'])<=time()){
            echo json_encode(array("success"=>false,'msg'=>'当前时间已大于启动时间'));
            exit();
        }


	    $db->pquery("delete from vtiger_leadsharesetting where leadsharesettingid=?",array($id));
        echo json_encode(array("success"=>true,'msg'=>'删除成功'));
    }

    public function getDepartmentUsers(Vtiger_Request $request){
	    $departmentid=$request->get("departmentid");
        $users = getDepartmentUser($departmentid);
        if(!count($users)){
            echo json_encode(array("success"=>false,'msg'=>'该部门下无员工'));
            exit();
        }
        $db=PearDatabase::getInstance();
        $result2 = $db->pquery("select userid from vtiger_leadassignpersonnel",array());
        $setedUserIds=array();
        if($db->num_rows($result2)){
            while ($row2=$db->fetchByAssoc($result2)){
                $setedUserIds[]=$row2['userid'];
            }
        }

        $sql = "SELECT id, CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department 
WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', IF ( `status` = 'Active', '', '[离职]' )) AS last_name FROM vtiger_users WHERE id in(".implode(",",$users).') and status="Active"';
        if(count($setedUserIds)){
            $sql .= ' and id not in('.implode(',',$setedUserIds).')';
        }
        $result = $db->pquery($sql,array());
        if(!$db->num_rows($result)){
            echo json_encode(array("success"=>false,'msg'=>'该部门下无员工或已设置'));
            exit();
        }
        while ($row=$db->fetchByAssoc($result)){
            $data[]=array(
                'id'=>$row['id'],
                'last_name'=>$row['last_name']
            );
        }
        echo json_encode(array("success"=>true,'msg'=>'','data'=>$data));
        exit();
    }

    public function setAssignPersonal(Vtiger_Request $request){
        $userIds =$request->get('userids');
        $departmentId=$request->get("departmentid");
        $recordModel = Leads_Record_Model::getCleanInstance("Leads");
        $recordModel->setAssignPersonal($userIds,$departmentId);
        echo json_encode(array("success"=>true,'msg'=>'设置成功'));
    }

    public function updateAssignNum(Vtiger_Request $request){
	    $id=$request->get("id");
	    $assignnum=$request->get('assignnum');
	    $db=PearDatabase::getInstance();
	    $db->pquery("update vtiger_leadassignpersonnel set assignnum=? where leadassignpersonnelid=?",array($assignnum,$id));
        echo json_encode(array("success"=>true,'msg'=>'修改成功'));
    }

    public function getBelongSystem(Vtiger_Request $request){
	    $userId = $request->get("userid");
        $db = PearDatabase::getInstance();
//        $result =$db->pquery("select b.parentdepartment from vtiger_leadassignpersonnel a left join vtiger_departments b on a.departmentid=b.departmentid where a.userid=?",array($userId));
        $result = $db->pquery("select b.parentdepartment from vtiger_user2department a left join vtiger_departments b on a.departmentid=b.departmentid where userid=?",array($userId));
        if(!$db->num_rows($result)){
            echo json_encode(array("success"=>false,'msg'=>'无对应数据'));
            exit();
        }
        $row=$db->fetchByAssoc($result,0);
        $parentdepartment=explode("::",$row['parentdepartment']);
        echo json_encode(array("success"=>true,'msg'=>'成功','departmentid'=>$parentdepartment[3]));
        exit();

    }

    public function isOverProtect(Vtiger_Request $request){
        $recordId=$request->get('accountid');
	    global $current_user;
        $db=PearDatabase::getInstance();
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'Accounts');
        $moduleModel = $recordModel->getModule();
        $entity=$recordModel->entity->column_fields;
        $salerank=$recordModel->getSaleRank($current_user->id);
        $accountRankStr = vtranslate($entity['accountrank'],'RankProtect');
        $userinfo =$db->pquery("SELECT u.user_entered,ud.departmentid FROM vtiger_users as u LEFT JOIN vtiger_user2department as ud ON ud.userid=u.id WHERE id = ?",
            array($current_user->id));
        $departmentid = $db->query_result($userinfo, 0,'departmentid');
        $user_entered = $db->query_result($userinfo, 0,'user_entered');
        $result=$recordModel->getRankDays(array($salerank,$entity['accountrank'],$departmentid,$user_entered));
        //已领取的客户总数
        $accountnum=$recordModel->getRankCounts(array($entity['accountrank'],0,(int)$current_user->id));

        if($result['protectnum']<=$accountnum){
            echo json_encode(array("success"=>false,'msg'=>$accountRankStr.'等级客户保护数量'.$result['protectnum'].'个，您当前已有'.$accountRankStr.'等级客户'.$accountnum.'个，已达保护数量，不可领取该等级客户。'));
        }else{
            echo json_encode(array("success"=>true,'msg'=>'成功'));

        }
    }
}
