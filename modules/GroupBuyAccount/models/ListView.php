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
 * ServiceComments ListView Model Class
 */
class GroupBuyAccount_ListView_Model extends Vtiger_ListView_Model {
	//根据参数显示数据
    public function __construct(array $values = array())
    {
        parent::__construct($values);
        $_REQUEST["filter"]="groupbuy";
        $_REQUEST["module"]="ServiceComments";
        $this->set("module","ServiceComments");
    }

    public function getListViewEntries($pagingModel) {
        $db = PearDatabase::getInstance();
        $moduleName = 'ServiceComments';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');


        if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
            $orderBy = 'servicecommentsid';
            $sortOrder = 'DESC';
        }


		
		$listQuery1="select
				( SELECT GROUP_CONCAT(productsearchid, '##') FROM vtiger_servicecontracts WHERE sc_related_to = related_to LIMIT 2 ORDER BY servicecontractid DESC ) AS 'productlist',
				vtiger_servicecomments.assigntype,
				IFNULL((select vtiger_modcomments.addtime from vtiger_modcomments
							where vtiger_modcomments.modulename='Accounts'
							and vtiger_modcomments.moduleid=vtiger_servicecomments.related_to
					    	and vtiger_modcomments.creatorid=vtiger_servicecomments.serviceid
					    	ORDER BY vtiger_modcomments.addtime desc LIMIT 1),vtiger_servicecomments.addtime) as lastfollowtime,
				vtiger_servicecomments.allocatetime,
				vtiger_servicecomments.servicecommentsid,
				vtiger_servicecomments.salesorderproductsrelid,
				vtiger_servicecomments.related_to as accountid,
				(vtiger_account.accountname) as related_to,
				vtiger_servicecomments.addtime,
				(select leadsource from vtiger_account where vtiger_account.accountid=vtiger_servicecomments.related_to ) as leadsource,
				'--' as productid,
				vtiger_servicecomments.starttime,
				vtiger_servicecomments.endtime,
				'--' as serviceamount,
				IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where id=vtiger_servicecomments.serviceid),'--') as serviceid,

				(select last_name from vtiger_users where id=vtiger_servicecomments.assignerid) as assignerid,
				(select accountrank from vtiger_account where vtiger_account.accountid=vtiger_servicecomments.related_to) as accountrank,
				(select IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id),'--') as smownerid from vtiger_crmentity where vtiger_crmentity.crmid=vtiger_servicecomments.related_to and vtiger_crmentity.deleted=0) as ownerid,
				(select departmentname from vtiger_departments where vtiger_departments.departmentid=(select departmentid from vtiger_user2department where vtiger_user2department.userid=(select vtiger_crmentity.smownerid from vtiger_crmentity where vtiger_crmentity.crmid=vtiger_servicecomments.related_to and vtiger_crmentity.deleted=0))) as departmentid,
				'--' as schedule,
				vtiger_servicecomments.nofollowday,
				vtiger_servicecomments.remark
				from  vtiger_servicecomments
					LEFT  join vtiger_account ON vtiger_account.accountid = vtiger_servicecomments.related_to
					LEFT JOIN  vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecomments.related_to
					LEFT JOIN vtiger_servicecomments_returnplan ON vtiger_servicecomments_returnplan.commentsid = vtiger_servicecomments.servicecommentsid
					where 1=1  ";
		

		$listQuery = $listQuery1;


        $this->getSearchWhere();
        //$listQuery = $this->getQuery();
        $listQuery.=$this->getUserWhere();

        $queryGenerator = $this->get('query_generator');
        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }
		//按最后跟进日期排序(降序) //20150625改为升序
		//$listQuery.=' order by '.$orderBy.' '.$sortOrder ;
        $listQuery.='  GROUP BY vtiger_servicecomments.servicecommentsid';
        // nofollowday 字段排序 sql 语句运行太慢处理 20170310
        $listQuery.=' order by  vtiger_servicecomments.servicecommentsid DESC ';
		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();
		
	
		$viewid = ListViewSession::getCurrentView($moduleName);
	
		ListViewSession::setSessionQuery($moduleName, $listQuery, 0);//session缓存查询条件,

        $listQuery .= " LIMIT $startIndex,".($pageLimit);
        //echo $listQuery;die;

		$listResult = $db->pquery($listQuery, array());
		$listViewRecordModels = array();
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['servicecommentsid'];
            $listViewRecordModels[$rawData['servicecommentsid']] = $rawData;
        }
        //echo $listQuery;die;
        //print_r($listViewRecordModels);die;
        return $listViewRecordModels;
	}
    public function getUserWhere(){
        $searchDepartment = $_REQUEST['department'];//部门
        //print_r($_REQUEST);
        //exit;
        $sower=$_REQUEST['smown'];
        $where=getAccessibleUsers('ServiceComments','List',true);
        $userid=getDepartmentUser($searchDepartment);
        $listQuery = '';
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){
            if(!empty($where)&&$where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            $listQuery .= " AND vtiger_crmentity.smownerid IN(".implode(',', $where).")";
        }

        $listQuery .= " and vtiger_servicecomments.assigntype='accountby'";
        //非管理员
//         if(!empty($searchDepartment)){
//             if(!empty($where)&&$where!='1=1'){
//                 $where=$userid;
//             }else{
//                 $where=array_intersect($where,$userid);
//             }
//             $where=implode(',',$where);
//             $listQuery .= ' and vtiger_servicecomments.serviceid'.$where;
//         }
        if($searchDepartment!='nodo' && in_array($searchDepartment,array('chan_notv','forp_notv','norm_notv','spec_isv','eigp_notv','sixp_notv','visp_isv','wlad_isv','wlvp_isv','wlbr_isv','wlsi_isv','wlgo_isv','iron_isv','bras_isv','silv_isv','gold_isv'))){
        	$listQuery .= " AND EXISTS (SELECT accountrank FROM vtiger_account WHERE vtiger_account.accountid = vtiger_servicecomments.related_to AND vtiger_account.accountrank='{$searchDepartment}') ";
        }
        if($sower!='nodi' && !empty($sower)){
        	$listQuery .= " AND EXISTS(SELECT crmid FROM vtiger_crmentity WHERE vtiger_crmentity.crmid = vtiger_servicecomments.related_to AND vtiger_crmentity.deleted = 0  AND vtiger_crmentity.smownerid={$sower}) ";
        }
        $where=getAccessibleUsers();
        if($where!='1=1'){
        	$listQuery .= ' and vtiger_servicecomments.serviceid'.$where;
        }
        if(!empty($_REQUEST['filter']) && $_REQUEST['filter']=='groupbuy'){
            $listQuery .=" AND vtiger_account.groupbuyaccount=1";
        }
        if($_REQUEST['public']=='productby'){
            $listQuery .=" and vtiger_servicecomments.assigntype='productby'";
        }elseif($_REQUEST['public']=='accountby'){
            $listQuery .=" and vtiger_servicecomments.assigntype='accountby'";
        }elseif($_REQUEST['public']=='follow'){
            $listQuery .=" and vtiger_servicecomments.nofollowday=0";
        }elseif($_REQUEST['public']=='nofollow'){
            $listQuery .=" and vtiger_servicecomments.nofollowday>0";
        }elseif($_REQUEST['public']=='7daynofollow'){
            $listQuery .=" and vtiger_servicecomments.nofollowday between 1 and 7";
        }elseif($_REQUEST['public']=='15daynofollow'){
            $listQuery .=" and vtiger_servicecomments.nofollowday between 1 and 15";
        }elseif($_REQUEST['public']=='30daynofollow'){
            $listQuery .=" and vtiger_servicecomments.nofollowday between 1 and 30";
        }elseif($_REQUEST['public']=='exceednofollow'){
            $listQuery .=" and vtiger_servicecomments.nofollowday = 0 ";
        }elseif($_REQUEST['public']=='allnofollowday'){
            $listQuery .=" and vtiger_servicecomments.allnofollowday = 0 ";
        }elseif($_REQUEST['public']=='todayneedfollow'){ //当天需跟进
            $listQuery .=" and SYSDATE() BETWEEN updatetime AND lowertime";
        }elseif($_REQUEST['public']=='todaynofollow'){ //当天未跟进
            $listQuery .=" and SYSDATE() BETWEEN updatetime AND lowertime and isfollow !=1";
        }elseif($_REQUEST['public']=='overnofollow'){ //过期未跟进
            $listQuery .=" and SYSDATE()>lowertime AND isfollow !=1";
        }else{

        }
        return $listQuery;
    }
    public function getListViewHeaders() {
        $sourceModule = $this->get('src_module');
        $queryGenerator = $this->get('query_generator');
        if(!empty($sourceModule)){
            return $queryGenerator->getModule()->getPopupFields();
        }else{

            $list=$queryGenerator->getModule()->getListFields();
            $temp=array();
            foreach($list as $fields){
                $temp[$fields['fieldlabel']]=$fields;
            }

            return $temp;
        }
        return $queryGenerator->getFocus()->list_fields_name;
    }
/*    public function getListViewCount() {
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
//print_r(debug_backtrace(0));
        //搜索条件
        //$this->getSearchWhere();
        //用户条件
        $where=$this->getUserWhere();
        //$where.= ' AND accountname is NOT NULL';
        $queryGenerator->getSelectClauseColumnSQL();
        $queryGenerator->addUserWhere($where);
       // $listQuery =  $queryGenerator->getQueryCount();
        $listQuery = 'SELECT count(*) AS counts FROM vtiger_servicecomments LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_servicecomments.related_to LEFT JOIN  vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecomments.related_to WHERE 1 = 1';

        //echo $listQuery.'<br>';

        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }*/
    public function getListViewCount() {
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
//print_r(debug_backtrace(0));
        //搜索条件
        //$this->getSearchWhere();
        //用户条件
        $where=$this->getUserWhere();
        //$where.= ' AND accountname is NOT NULL';
        //wangbin 注释2016-5-6
        //$listQuery = 'SELECT count(*) AS counts FROM vtiger_servicecomments LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_servicecomments.related_to LEFT JOIN  vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecomments.related_to WHERE 1 = 1';
          $listQuery = 'SELECT COUNT(*) AS counts FROM vtiger_servicecomments LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_servicecomments.related_to LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecomments.related_to LEFT JOIN vtiger_servicecomments_returnplan ON vtiger_servicecomments_returnplan.commentsid = vtiger_servicecomments.servicecommentsid WHERE 1 = 1';
        $listQuery.=$this->getUserWhere();

        $queryGenerator = $this->get('query_generator');
        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }
        $listQuery.=' GROUP BY vtiger_servicecomments.servicecommentsid';
        //steel 2015-05-15
        $listResult = $db->pquery($listQuery, array());
        //return $db->query_result($listResult,0,'counts');
        return $db->num_rows($listResult);
    }
}
