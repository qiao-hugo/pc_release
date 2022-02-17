<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class SalesDaily_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
        $this->exposeMethod('getDayFoutNotv');
        $this->exposeMethod('getCanDealContacts');
        $this->exposeMethod('getDayDealContent');
        $this->exposeMethod('getNextDayVisit');
        $this->exposeMethod('updateMContent');
        $this->exposeMethod('getAppenddailycandeal');
        $this->exposeMethod('getNodaily');
        $this->exposeMethod('getAccountStatistics');
        $this->exposeMethod('saveAccountStatistics');
	}
	
	function checkPermission(Vtiger_Request $request) {
		return;
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
    /**
     * 新增40%的客户
     * @param Vtiger_Request $request
     */
    public function getDayFoutNotv(Vtiger_Request $request){
        $recordid=$request->get('record');
        $dailydate=$request->get('dailydate');
        global $current_user,$adb;

        do{

            if(empty($recordid)){
                $query='SELECT 1 FROM vtiger_salesdaily_basic WHERE vtiger_salesdaily_basic.dailydatetime=? AND vtiger_salesdaily_basic.smownerid=?';
                $result=$adb->pquery($query,array($dailydate,$current_user->id));
                if($adb->num_rows($result)){
                    $data['fournotv']['status']=false;
                    $data['fournotv']['msg']='对应日期日报已经添加';
                    break;
                }
                $where=getAccessibleUsers('SalesDaily','List',true);
                if($where!='1=1'){
                    $userid = getDepartmentUser($current_user->departmentid);
                    $where=array_intersect($where,$userid);
                }else{
                    $userid=getDepartmentUser();
                    $where=$userid;
                }
                $where  = array_merge($where,array($current_user->id));
                //$query='SELECT vtiger_account.accountid,vtiger_account.accountname,vtiger_visitingorder.visitingorderid,(SELECT vtiger_crmentity.smownerid FROM vtiger_crmentity WHERE crmid=vtiger_account.accountid AND vtiger_crmentity.setype=\'Accounts\') as smownerid,vtiger_account.accountname,vtiger_account.leadsource,vtiger_visitingorder.contacts,if(vtiger_account.linkname=vtiger_visitingorder.contacts,vtiger_account.title,(SELECT vtiger_contactdetails.`title` FROM vtiger_contactdetails WHERE vtiger_account.accountid=vtiger_contactdetails.accountid AND vtiger_contactdetails.`name`=vtiger_visitingorder.contacts)) AS title,if(vtiger_account.linkname=vtiger_visitingorder.contacts,vtiger_account.mobile,(SELECT vtiger_contactdetails.`mobile` FROM vtiger_contactdetails WHERE vtiger_account.accountid=vtiger_contactdetails.accountid AND vtiger_contactdetails.`name`=vtiger_visitingorder.contacts)) AS mobile,vtiger_visitingorder.startdate FROM vtiger_account LEFT JOIN vtiger_crmentity ON vtiger_account.accountid=vtiger_crmentity.crmid LEFT JOIN vtiger_visitingorder ON vtiger_account.accountid=vtiger_visitingorder.related_to WHERE vtiger_account.accountrank=\'forp_notv\' AND vtiger_visitingorder.modulestatus=\'c_complete\' AND vtiger_crmentity.smownerid=? AND vtiger_crmentity.deleted=0 AND left(vtiger_visitingorder.workflowstime,10)=?';
                $query="SELECT vtiger_account.accountid,vtiger_account.accountname,vtiger_visitingorder.visitingorderid,vtiger_account.commentcontent,
       (SELECT vtiger_crmentity.smownerid FROM vtiger_crmentity WHERE crmid=vtiger_account.accountid AND vtiger_crmentity.setype='Accounts') as smownerid,
       vtiger_account.accountname,vtiger_account.newleadsource,vtiger_visitingorder.contacts,
       if(vtiger_account.linkname=vtiger_visitingorder.contacts,vtiger_account.title,(SELECT vtiger_contactdetails.`title` FROM vtiger_contactdetails WHERE vtiger_account.accountid=vtiger_contactdetails.accountid AND vtiger_contactdetails.`name`=vtiger_visitingorder.contacts)) AS title,
       if(vtiger_account.linkname=vtiger_visitingorder.contacts,vtiger_account.mobile,(SELECT vtiger_contactdetails.`mobile` FROM vtiger_contactdetails WHERE vtiger_account.accountid=vtiger_contactdetails.accountid AND vtiger_contactdetails.`name`=vtiger_visitingorder.contacts)) AS mobile,
       vtiger_visitingorder.startdate FROM `vtiger_accountrankhistory` 
         LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_accountrankhistory.accountid 
         LEFT JOIN vtiger_crmentity ON vtiger_account.accountid=vtiger_crmentity.crmid LEFT JOIN 
         vtiger_visitingorder ON vtiger_account.accountid=vtiger_visitingorder.related_to 
WHERE vtiger_accountrankhistory.newaccountrank='forp_notv' AND vtiger_visitingorder.modulestatus='c_complete' 
  AND vtiger_crmentity.smownerid in(".implode(',',$where).") AND vtiger_crmentity.deleted=0 AND left(vtiger_accountrankhistory.createdtime,10)=? GROUP BY vtiger_accountrankhistory.accountid";
                $result=$adb->pquery($query,array($dailydate));
                //            echo $query;die;
                $arrtemp=array();
                while($rawData=$adb->fetch_array($result)){
                    $rawData['id']=$rawData['accountid'];
                    $rawData['leadsource']=vtranslate($rawData['newleadsource'],'Accounts');
                    $rawData['leadsourceen']=$rawData['newleadsource']?$rawData['newleadsource']:'';
                    $mangerreturnendtime = date("Y-m-d H:00:00",(strtotime("+ 2 day",strtotime(substr($rawData['startdate'],0,10)))+12*60*60));
                    $rawData['mangereturnendtime']=$mangerreturnendtime;
                    $rawData['accountname']=$rawData['accountname']?$rawData['accountname']:'';
                    $rawData['commentcontent']=$rawData['commentcontent']?$rawData['commentcontent']:'';
                    $rawData['smownerid']=$rawData['smownerid']?$rawData['smownerid']:'';
                    $rawData['contacts']=$rawData['contacts']?$rawData['contacts']:'';
                    $rawData['title']=$rawData['title']?$rawData['title']:'';
                    $rawData['mobile']=$rawData['mobile']?$rawData['mobile']:'';
                    $rawData['startdate']=$rawData['startdate']?$rawData['startdate']:'';
                    $arrtemp[$rawData['accountid']]=$rawData;
                }
            }else{
                $recordModel=Vtiger_Record_Model::getInstanceById($recordid,'SalesDaily');
                $column_fields=$recordModel->entity->column_fields;
                if($column_fields['dailydatetime']!=$dailydate){
                    $data['fournotv']['status']=false;
                    $data['fournotv']['msg']='日报日期不允许修改';
                    break;
                }
                $query = "SELECT
	vtiger_salesdailyfournotv.accountid,vtiger_salesdailyfournotv.accountname,vtiger_account.commentcontent,
	( SELECT vtiger_crmentity.smownerid FROM vtiger_crmentity WHERE crmid = vtiger_account.accountid AND vtiger_crmentity.setype = 'Accounts' ) AS smownerid,
vtiger_salesdailyfournotv.leadsource as newleadsource,
vtiger_salesdailyfournotv.linkname as contacts,
vtiger_salesdailyfournotv.title,
vtiger_salesdailyfournotv.mobile,
vtiger_salesdailyfournotv.mangereturnendtime,
vtiger_salesdailyfournotv.startdatetime as startdate
FROM
	vtiger_salesdailyfournotv
	LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_salesdailyfournotv.accountid 
WHERE
	salesdailybasicid =?";
                $result = $adb->pquery($query,array($recordid));
                //            echo $query;die;
                $arrtemp=array();
                while($rawData=$adb->fetch_array($result)){
                    $rawData['id']=$rawData['accountid'];
                    $rawData['leadsourceen']=$rawData['newleadsource']?$rawData['newleadsource']:'';
                    $rawData['leadsource']=$rawData['newleadsource']?$rawData['newleadsource']:'';
                    $rawData['accountname']=$rawData['accountname']?$rawData['accountname']:'';
                    $rawData['commentcontent']=$rawData['commentcontent']?$rawData['commentcontent']:'';
                    $rawData['smownerid']=$rawData['smownerid']?$rawData['smownerid']:'';
                    $rawData['contacts']=$rawData['contacts']?$rawData['contacts']:'';
                    $rawData['title']=$rawData['title']?$rawData['title']:'';
                    $rawData['mobile']=$rawData['mobile']?$rawData['mobile']:'';
                    $rawData['mangereturnendtime']=$rawData['mangereturnendtime']?$rawData['mangereturnendtime']:'';
                    $rawData['startdate']=$rawData['startdate']?$rawData['startdate']:'';
                    $arrtemp[$rawData['accountid']]=$rawData;
                }
            }
            $data['fournotv']['status']=true;
            $data['fournotv']['result']=$arrtemp;
            $data['fournotv']['num']=count($arrtemp);
        }while(0);

        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 取可成交客户的联系人信息
     * @param Vtiger_Request $request
     */
    public function getCanDealContacts(Vtiger_Request $request){
        $recordid=$request->get('record');
        global $current_user,$adb;

        do{
            if(empty($recordid)){
                $data['status']=false;
                $data['msg']='数据出错';
                break;
            }
            $query='SELECT vtiger_account.accountid AS contactid,vtiger_account.linkname,vtiger_account.title,vtiger_account.mobile FROM vtiger_account WHERE accountid=?'; //UNION ALL SELECT vtiger_contactdetails.contactid,vtiger_contactdetails.`name`,vtiger_contactdetails.title,vtiger_contactdetails.mobile FROM vtiger_contactdetails WHERE vtiger_contactdetails.accountid=?';
            $result=$adb->pquery($query,array($recordid));

            $arrtemp=array();
            while($rawData=$adb->fetch_array($result)){
                $arrtemp[$rawData['contactid']]=$rawData;
            }
            $data['status']=true;
            $data['result']=$arrtemp;
            $data['num']=count($arrtemp);
        }while(0);

        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    /**
     * 每日成交客户的
     * @param Vtiger_Request $request
     */
    public function getDayDealContent(Vtiger_Request $request){
        $current_date=$request->get('datetime');
        global $current_user,$adb;

        do{
            if(empty($current_date)){
                $data['status']=false;
                $data['msg']='数据出错';
                break;
            }
            //$query='SELECT vtiger_account.industry,(SELECT count(1) FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.modulestatus=\'c_complete\' AND vtiger_servicecontracts.sc_related_to=vtiger_account.accountid) AS oldcust,(SELECT count(1) FROM vtiger_visitingorder LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_visitingorder.visitingorderid WHERE vtiger_crmentity.deleted=0 AND vtiger_visitingorder.visitingorderid AND vtiger_visitingorder.modulestatus=\'c_complete\' AND vtiger_visitingorder.related_to=vtiger_account.accountid) AS visitingordernum,(SELECT vtiger_visitingorder.contacts FROM vtiger_visitingorder LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_visitingorder.visitingorderid WHERE vtiger_crmentity.deleted=0 AND vtiger_visitingorder.visitingorderid AND vtiger_visitingorder.modulestatus=\'c_complete\' AND vtiger_visitingorder.related_to=vtiger_account.accountid ORDER BY vtiger_visitingorder.visitingorderid DESC limit 1) AS visitingordercontacts,(SELECT (SELECT GROUP_CONCAT(vtiger_users.last_name) FROM vtiger_users WHERE FIND_IN_SET(vtiger_users.id,replace(vtiger_visitingorder.accompany,\' |##| \',\',\'))) FROM vtiger_visitingorder LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_visitingorder.visitingorderid WHERE vtiger_crmentity.deleted=0 AND vtiger_visitingorder.visitingorderid AND vtiger_visitingorder.modulestatus=\'c_complete\' AND vtiger_visitingorder.related_to=vtiger_account.accountid ORDER BY vtiger_visitingorder.visitingorderid DESC limit 1) AS visitingorderwithvisitor FROM vtiger_account WHERE accountid=?';
            $query="SELECT
                    (SELECT last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_achievementallot.receivedpaymentownid) AS user_name,
                    vtiger_achievementallot.receivedpaymentownid AS receiveid,
										vtiger_account.accountid,
                    sum(vtiger_achievementallot.businessunit) AS unit_price,
                    vtiger_receivedpayments.reality_date,
                    vtiger_account.accountname,
                    vtiger_servicecontracts.contract_no,
                    vtiger_account.industry,
                        (
                            SELECT
                                count(1)
                            FROM
                                vtiger_servicecontracts
                            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid
                            WHERE
                                vtiger_crmentity.deleted = 0
                            AND vtiger_servicecontracts.modulestatus = 'c_complete'
                            AND vtiger_servicecontracts.sc_related_to = vtiger_account.accountid
                        ) AS oldcust,
                        (
                            SELECT
                                count(1)
                            FROM
                                vtiger_visitingorder
                            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_visitingorder.visitingorderid
                            WHERE
                                vtiger_crmentity.deleted = 0
                            AND vtiger_visitingorder.visitingorderid
                            AND vtiger_visitingorder.modulestatus = 'c_complete'
                            AND vtiger_visitingorder.related_to = vtiger_account.accountid
                        ) AS visitingordernum,
                        (
                            SELECT
                                vtiger_visitingorder.contacts
                            FROM
                                vtiger_visitingorder
                            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_visitingorder.visitingorderid
                            WHERE
                                vtiger_crmentity.deleted = 0
                            AND vtiger_visitingorder.visitingorderid
                            AND vtiger_visitingorder.modulestatus = 'c_complete'
                            AND vtiger_visitingorder.related_to = vtiger_account.accountid
                            ORDER BY
                                vtiger_visitingorder.visitingorderid DESC
                            LIMIT 1
                        ) AS visitingordercontacts,
                        (
                            SELECT
                                (
                                    SELECT
                                        GROUP_CONCAT(vtiger_users.last_name)
                                    FROM
                                        vtiger_users
                                    WHERE
                                        FIND_IN_SET(
                                            vtiger_users.id,
                                            REPLACE (
                                                vtiger_visitingorder.accompany,
                                                ' |##| ',
                                                ','
                                            )
                                        )
                                )
                            FROM
                                vtiger_visitingorder
                            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_visitingorder.visitingorderid
                            WHERE
                                vtiger_crmentity.deleted = 0
                            AND vtiger_visitingorder.visitingorderid
                            AND vtiger_visitingorder.modulestatus = 'c_complete'
                            AND vtiger_visitingorder.related_to = vtiger_account.accountid
                            ORDER BY
                                vtiger_visitingorder.visitingorderid DESC
                            LIMIT 1
                        ) AS visitingorderwithvisitor,

                    vtiger_servicecontracts.total,
                    vtiger_servicecontracts.productid,
                    ifnull((SELECT CONCAT(vtiger_products.productname) FROM vtiger_products WHERE vtiger_products.productid IN(vtiger_servicecontracts.productid)),'') AS productname,
                    ifnull((SELECT sum(IFNULL(vtiger_products.unit_price,0)) FROM vtiger_products WHERE vtiger_products.productid IN(vtiger_servicecontracts.productid)),0) AS marketprice,
                    (SELECT sum(IFNULL(vtiger_products.tranperformance,0)) FROM vtiger_products WHERE vtiger_products.productid IN(vtiger_servicecontracts.productid)) AS allcost,
              
                    vtiger_achievementallot.matchdate as matchdate                FROM
                    vtiger_achievementallot
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_achievementallot.servicecontractid
                LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_servicecontracts.sc_related_to
                LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid
                LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_achievementallot.receivedpaymentownid
                LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                WHERE
                vtiger_receivedpayments.receivedpaymentsid>0
                AND vtiger_achievementallot.matchdate=?
                AND vtiger_achievementallot.matchdate!=''
                AND vtiger_achievementallot.matchdate IS NOT NULL 
                AND vtiger_achievementallot.receivedpaymentownid=?
                GROUP BY vtiger_account.accountid";
            $result=$adb->pquery($query,array($current_date,$current_user->id));
            //$result=$adb->pquery($query,array('2017-04-20',2236));

            $arrtemp=array();
            while($rawData=$adb->fetch_array($result)){
                $arrtemps=$rawData;
                $arrtemps['industry']=vtranslate($rawData['industry'],'Accounts');
                $arrtemps['visitingordernum']=$rawData['visitingordernum'];
                $arrtemps['visitingorderwithvisitor']=$rawData['visitingorderwithvisitor']==null?"":$rawData['visitingorderwithvisitor'];
                $arrtemps['visitingordercontacts']=$rawData['visitingordercontacts']==null?"":$rawData['visitingordercontacts'];
                $arrtemps['oldcust']=$rawData['oldcust']>=1?1:0;
                $arrtemps['oldcustmsg']=$rawData['oldcust']>=1?'是':'否';
                $arrtemp[]=$arrtemps;
            }
            $data['status']=true;
            $data['result']=$arrtemp;
            $data['num']=count($arrtemp);
        }while(0);

        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    public function getNextDayVisit(Vtiger_Request $request){
        global $adb,$current_user;
        $recordid=$request->get('record');
        $dailydate=$request->get('dailydate');
        $query='SELECT
                    vtiger_visitingorder.visitingorderid,
                    vtiger_visitingorder.contacts,
                    if(vtiger_account.linkname=vtiger_visitingorder.contacts,vtiger_account.title,(SELECT vtiger_contactdetails.`title` FROM vtiger_contactdetails WHERE vtiger_account.accountid=vtiger_contactdetails.accountid AND vtiger_contactdetails.`name`=vtiger_visitingorder.contacts)) AS title,
                    (SELECT count(1) FROM vtiger_visitingorder LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_visitingorder.visitingorderid WHERE vtiger_crmentity.deleted=0 AND vtiger_visitingorder.visitingorderid AND vtiger_visitingorder.modulestatus!=\'a_exception\' AND vtiger_visitingorder.related_to=vtiger_account.accountid) AS visitingordernum,
                    vtiger_account.accountname,
                    vtiger_visitingorder.purpose,
                    vtiger_account.accountid,
                    vtiger_visitingorder.modulestatus,
                    IFNULL((SELECT GROUP_CONCAT(vtiger_users.last_name) FROM vtiger_users WHERE FIND_IN_SET(vtiger_users.id,replace(vtiger_visitingorder.accompany,\' |##| \',\',\'))),\'\') AS visitingorderwithvisitor
                        FROM vtiger_visitingorder
                LEFT JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid=vtiger_crmentity.crmid
                LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_visitingorder.related_to
                WHERE vtiger_crmentity.deleted=0
                    AND vtiger_visitingorder.modulestatus!=\'a_exception\'
                    AND vtiger_crmentity.smownerid=? AND left(vtiger_visitingorder.startdate,10)=?';
        $datetime=date('Y-m-d',strtotime("+1 day",strtotime($dailydate)));
        $result=$adb->pquery($query,array($current_user->id,$datetime));

        $arrtemp=array();
        while($rawData=$adb->fetch_array($result)){
            $arrtemp[$rawData['visitingorderid']]=$rawData;
            $arrtemp[$rawData['visitingorderid']]['zhmodulestatus']=vtranslate($rawData['modulestatus'],'VisitingOrder');;
        }
        $data['num']=count($arrtemp);
        $data['result']=$arrtemp;
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    public function updateMContent(Vtiger_Request $request){
        global $adb,$current_user;

        $adb->pquery("update vtiger_salesdailyfournotv set mangereturntime=?,mcontent=?,mangerid=? where mangerid=0 and salesdailyfournotvid=?",array($request->get('date'),$request->get('content'),$current_user->id,$request->get('id')));

    }
    public function getAppenddailycandeal(Vtiger_Request $request){
        $dailydate=$request->get('dailydate');
        $newdailydate=date("Y-m-d",strtotime('-1 day',strtotime($dailydate)));
        global $current_user,$adb;
        $query='SELECT salesdailybasicid FROM vtiger_salesdaily_basic WHERE smownerid=? and vtiger_salesdaily_basic.dailydatetime <=? ORDER BY salesdailybasicid DESC LIMIT 1';

        $result=$adb->pquery($query,array($current_user->id,$newdailydate));
        $rowdata=$adb->query_result_rowdata($result,0);
        $query='SELECT vtiger_salesdailycandeal.*,vtiger_account.accountname FROM vtiger_salesdailycandeal LEFT JOIN vtiger_salesdaily_basic ON vtiger_salesdaily_basic.salesdailybasicid=vtiger_salesdailycandeal.salesdailybasicid LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_salesdailycandeal.accountid WHERE vtiger_salesdailycandeal.isupdatedeleted=0 AND vtiger_salesdailycandeal.issigncontract=0 AND vtiger_salesdaily_basic.salesdailybasicid=?';
        $result=$adb->pquery($query,array($rowdata['salesdailybasicid']));
        $arrtemp=array();
        while($rawData=$adb->fetch_array($result)){
            $arrtemp[]=$rawData;
        }
        $data['num']=count($arrtemp);
        $data['result']=$arrtemp;
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    public function getNodaily(Vtiger_Request $request){

        $start=$request->get('start');
        $end=$request->get('end');


        if(empty($start) || empty($end)){
            $stratdate=date('Y-m').'-01';
            $days=date('t');
            $enddate=date('Y-m').'-'.$days;
        }else{
            $stratdate=date('Y-m-d',$start);
            $enddate=date('Y-m-d',$end);

        }
        $where=getAccessibleUsers('Accounts','List',true);

        if($where!='1=1'){
            $listQuery = ' and vtiger_nosalesdaily.userid in ('.implode(',',$where).')';
        }else{
            $listQuery = '';
        }

        $db=PearDatabase::getInstance();
        $result=$db->pquery("SELECT vtiger_nosalesdaily.workday,vtiger_departments.departmentid,IFNULL(vtiger_departments.departmentname,'--') as departmentname,CONCAT(vtiger_users.last_name,'[',IFNULL(vtiger_departments.departmentname,'--'),']',IF(vtiger_users.`status`!='Active','[离职]','')) as username FROM vtiger_nosalesdaily LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_nosalesdaily.userid LEFT JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid
                            LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid WHERE 1=1 {$listQuery}AND vtiger_nosalesdaily.workday>=? AND vtiger_nosalesdaily.workday<=? order by vtiger_nosalesdaily.workday,vtiger_departments.departmentid",array($stratdate,$enddate));

        $arrtemp=array();
        $i=1;
        $departmentid='AAA';
        $departmentname='';
        $chageday='';
        $sumnums=0;
        while($rawData=$db->fetch_array($result)){
            if(empty($rawData['departmentid'])){
                continue;
            }
            if(empty($rawData['username'])){
               continue;
            }
            if($rawData['departmentid']!=$departmentid){
                if($i!=1){
                    $temp['title'] = $departmentname.'--结束--共'.$sumnums;
                    $temp['start'] = date("Y-m-d H:i:s",(strtotime($chageday)+$i));
                    $temp['backgroundColor'] = '#468847';
                    $temp['textColor'] = '#ffffff';
                    $temp['id'] =$i++;
                    $arrtemp[] = $temp;
                }
                $temp['backgroundColor'] = '#b94a48';
                $temp['textColor'] = '#ffffff';
                $departmentname=$rawData['departmentname'];
                $chageday=$rawData['workday'];
                $departmentid=$rawData['departmentid'];
                $temp['title'] = $rawData['departmentname'].'--开始';
                //$temp['start'] = $rawData['workday'];
                $temp['start'] = date("Y-m-d H:i:s",(strtotime( $rawData['workday'])+$i));//方便排序
                $temp['id'] =$i++;
                $arrtemp[] = $temp;
                $sumnums=0;
            }
            $temp['backgroundColor'] = '#36c';
            $temp['textColor'] = '#ffffff';
            $temp['title'] = $rawData['username'];
            $temp['start'] = date("Y-m-d H:i:s",(strtotime( $rawData['workday'])+$i));
            $temp['id'] =$i++;
            $arrtemp[] = $temp;
            $sumnums++;
        }
        if($i!=1){
            $temp['title'] = $departmentname.'--结束--共'.$sumnums;
            $temp['start'] = date("Y-m-d H:i:s",(strtotime($chageday)+$i));
            $temp['backgroundColor'] = '#468847';
            $temp['textColor'] = '#ffffff';
            $temp['id'] =$i++;
            $arrtemp[] = $temp;
        }
        return json_encode($arrtemp);
    }

    function getAccountStatistics(Vtiger_Request $request){
        $record = $request->get("record");
        $dailydate = $request->get('dailydate');
        $isMobile = $request->get("ismobile")?1:0;
        //获取上周和上月的信息
        global $current_user;
        global $adb;
        if($record){
            $sql = "select * from vtiger_accountstatistics where salesdailyid=? limit 1";
            $result = $adb->pquery($sql,array($record));
            if($adb->num_rows($result)){
                $row = $adb->fetchByAssoc($result,0);
                $recordModel = Vtiger_Record_Model::getCleanInstance("SalesDaily");
                $wxNumberArray = SalesDaily_Record_Model::getWxNumber($row['userid'],$dailydate);
                $todayvisitnum = $row['todayvisitnum'];
                $telData  = TelStatistics_Record_Model::getTelStasInfo($row['userid'],$dailydate);
                $data = array(
                    "success"=>true,
                    'data'=>array(
                        "todayvisitnum"=>$todayvisitnum,
                        'total_telnumber'=>$telData['total_telnumber']?$telData['total_telnumber']:0,
                        'telnumber'=>$telData['telnumber']?$telData['telnumber']:0,
                        'tel_connect_rate'=>$telData['tel_connect_rate']?$telData['tel_connect_rate']:0,
                        'wxnumberlastweeknumber'=>$wxNumberArray['wxnumberlastweeknumber']?$wxNumberArray['wxnumberlastweeknumber']:0,
                        'wxnumberlastmonthnumber'=>$wxNumberArray['wxnumberlastmonthnumber']?$wxNumberArray['wxnumberlastmonthnumber']:0,
                        'wxnumber'=>$row['wxnumber']?$row['wxnumber']:0,
                        'wxnewlyaddnumber'=>$row['wxnewlyaddnumber']?$row['wxnewlyaddnumber']:0,
                        'wxnumberweek'=>$row['wxnumberweek']?$row['wxnumberweek']:0,
                        'wxnumberweekaddnumber'=>$row['wxnumberweekaddnumber']?$row['wxnumberweekaddnumber']:0,
                        'wxnumbermonth'=>$row['wxnumbermonth']?$row['wxnumbermonth']:0,
                        'wxnumbermonthaddnumber'=>$row['wxnumbermonthaddnumber']?$row['wxnumbermonthaddnumber']:0
                    )
                );
            }
        }else{

            $recordModel = Vtiger_Record_Model::getCleanInstance("SalesDaily");
            $wxNumberArray = $recordModel::getWxNumber($current_user->id,$dailydate);
            $todayvisitnum = VisitingOrder_Record_Model::todayVisitingNum($current_user->id,$dailydate);
            $telData  = TelStatistics_Record_Model::getTelStasInfo($current_user->id,$dailydate);
            $data = array(
                "success"=>true,
                'data'=>array(
                    "todayvisitnum"=>$todayvisitnum,
                    'total_telnumber'=>$telData['total_telnumber']?$telData['total_telnumber']:0,
                    'telnumber'=>$telData['telnumber']?$telData['telnumber']:0,
                    'tel_connect_rate'=>$telData['tel_connect_rate']?$telData['tel_connect_rate']:0,
                    'wxnumberlastweeknumber'=>$wxNumberArray['wxnumberlastweeknumber']?$wxNumberArray['wxnumberlastweeknumber']:0,
                    'wxnumberlastmonthnumber'=>$wxNumberArray['wxnumberlastmonthnumber']?$wxNumberArray['wxnumberlastmonthnumber']:0,
                    'wxnumber'=>0,
                    'wxnewlyaddnumber'=>0,
                    'wxnumberweek'=>0,
                    'wxnumberweekaddnumber'=>0,
                    'wxnumbermonth'=>0,
                    'wxnumbermonthaddnumber'=>0
                )
            );
        }
        if($isMobile){
            return $data;
        }
        echo json_encode($data);
    }

    public function saveAccountStatistics(Vtiger_Request $request){
        global $current_user,$adb;
        $record = $request->get("record");
        if($record){
            $sql = "update vtiger_accountstatistics set `todayvisitnum`=?,`total_telnumber`=?,`telnumber`=?,
                                     `tel_connect_rate`=?,`wxnumber`=?,`wxnewlyaddnumber`=?,`wxnumberweek`=?,`wxnumberweekaddnumber`=?,`wxnumbermonth`=?,`wxnumbermonthaddnumber`=? where salesdailyid=?";
            $adb->pquery($sql,array($request->get('todayvisitnum'),$request->get('total_telnumber'),$request->get('telnumber'),
                $request->get('tel_connect_rate'),$request->get('wxnumber'),$request->get('wxnewlyaddnumber'),
                $request->get('wxnumberweek'),$request->get('wxnumberweekaddnumber'),$request->get('wxnumbermonth'),
                $request->get('wxnumbermonthaddnumber'),$record));
        }else{
            $sql = "insert into vtiger_accountstatistics(`userid`,`dailydatetime`,`todayvisitnum`,`total_telnumber`,`telnumber`,
                                     `tel_connect_rate`,`wxnumber`,`wxnewlyaddnumber`,`wxnumberweek`,`wxnumberweekaddnumber`,`wxnumbermonth`,`wxnumbermonthaddnumber`) 
                                     values(?,?,?,?,?,?,?,?,?,?,?,?)";
            $adb->pquery($sql,array($current_user->id,$request->get("dailydatetime"),$request->get('todayvisitnum'),$request->get('total_telnumber'),$request->get('telnumber'),
                $request->get('tel_connect_rate'),$request->get('wxnumber'),$request->get('wxnewlyaddnumber'),
                $request->get('wxnumberweek'),$request->get('wxnumberweekaddnumber'),$request->get('wxnumbermonth'),
                $request->get('wxnumbermonthaddnumber')));
        }
        $data = array(
            "success"=>true,
        );
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
}
