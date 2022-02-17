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
 * ServiceMaintenance ListView Model Class
 */
class ServiceProducts_ListView_Model extends Vtiger_ListView_Model {
	//根据参数显示数据
	public function getListViewEntries($pagingModel) {
        $db = PearDatabase::getInstance();

        $moduleName = 'ServiceProducts';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');


        if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
            $orderBy = 'serviceid';
            $sortOrder = 'DESC';
        }

     /*   $noassignQuery="select * from (SELECT
				'--' as lastfollowtime,
				CONCAT('N',vtiger_salesorderproductsrel.salesorderproductsrelid) AS servicecommentsid,
				vtiger_salesorderproductsrel.salesorderproductsrelid,
				vtiger_account.accountid as related_to,
				 '' AS addtime,
				vtiger_account.leadsource,
				vtiger_salesorderproductsrel.productid,
				vtiger_salesorderproductsrel.starttime,
				vtiger_salesorderproductsrel.endtime,
				vtiger_salesorderproductsrel.serviceamount,
				'' as serviceid,
				'' as allocatetime,
				'' as assignerid,
				vtiger_account.accountrank,
				vtiger_salesorderproductsrel.ownerid,
				(select departmentname from vtiger_departments where vtiger_departments.departmentid=(select departmentid from vtiger_user2department where vtiger_user2department.userid=vtiger_salesorderproductsrel.ownerid)) as departmentid,
				vtiger_salesorderproductsrel.schedule,
				'' as remark
				from vtiger_salesorderproductsrel
				left JOIN vtiger_account on(vtiger_account.accountid=vtiger_salesorderproductsrel.accountid)
				where not EXISTS(select vtiger_servicecomments.salesorderproductsrelid from vtiger_servicecomments
				where vtiger_servicecomments.salesorderproductsrelid=cast(vtiger_salesorderproductsrel.salesorderproductsrelid as CHAR)))";*/
        //已分配客服
        $listQuery="SELECT
                            IFNULL(
                                (  SELECT  vtiger_modcomments.addtime  FROM vtiger_modcomments WHERE vtiger_modcomments.modulename = 'ServiceComments'
                                    AND vtiger_modcomments.moduleid = vtiger_servicecomments.servicecommentsid
                                    AND vtiger_modcomments.creatorid = vtiger_servicecomments.serviceid
                                    ORDER BY  vtiger_modcomments.addtime DESC LIMIT 1  ),
                                vtiger_servicecomments.addtime
                            ) AS lastfollowtime,
                            vtiger_servicecomments.servicecommentsid,
                            vtiger_servicecomments.salesorderproductsrelid,
                            vtiger_account.accountname as related_to,
                            vtiger_account.accountid,
                            vtiger_servicecomments.addtime,
                            vtiger_servicecomments.starttime,
                            vtiger_servicecomments.endtime,
                            vtiger_account.leadsource,
                        (select productname from vtiger_products where vtiger_products.productid=vtiger_salesorderproductsrel.productid) as productid,
                            vtiger_salesorderproductsrel.serviceamount,
                        IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname	FROM	vtiger_departments	WHERE	departmentid = (SELECT departmentid	FROM vtiger_user2department	WHERE	userid = vtiger_users.id
                                                        LIMIT 1)),''),']',(IF (`status` = 'Active',	'','[离职]'))) AS last_name FROM vtiger_users WHERE vtiger_servicecomments.serviceid = vtiger_users.id ),'--') AS serviceid,
                            vtiger_servicecomments.allocatetime,
                            vtiger_servicecomments.assignerid,
                            vtiger_account.accountrank,
                        IFNULL((SELECT GROUP_CONCAT(last_name,'[',IFNULL((SELECT departmentname	FROM	vtiger_departments	WHERE	departmentid = (SELECT departmentid	FROM vtiger_user2department	WHERE	userid = vtiger_users.id
                                                        LIMIT 1)),''),']',(IF (`status` = 'Active',	'','[离职]'))) AS last_name FROM vtiger_users WHERE FIND_IN_SET(vtiger_users.id,REPLACE(vtiger_salesorderproductsrel.ownerid,' |##| ',',')) ),'--') AS ownerid,
                            ( SELECT departmentname FROM vtiger_departments WHERE vtiger_departments.departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE   vtiger_user2department.userid = vtiger_salesorderproductsrel.ownerid  )
                            ) AS departmentid,
                            vtiger_salesorderproductsrel. SCHEDULE,
                            vtiger_servicecomments.remark
                        FROM
                            vtiger_servicecomments
                        LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_servicecomments.related_to

                        LEFT JOIN vtiger_salesorderproductsrel ON (
                            vtiger_servicecomments.salesorderproductsrelid = vtiger_salesorderproductsrel.salesorderproductsrelid
                        )
                        LEFT JOIN vtiger_products ON vtiger_salesorderproductsrel.productid = vtiger_products.productid

                        WHERE vtiger_servicecomments.assigntype = 'productby'";

        //young.yang 2015-1-31 bug#7583

       // $listQuery = "select * from (".$assignQuery." vtiger_servicecomments union all ".$noassignQuery." vtiger_servicecomments) ";
        //end
        //echo $listQuery;die();

        //print_r($overrideQuery);die();

      /*  if($_REQUEST['public']=='assign'){
            $listQuery =$assignQuery;
        }elseif($_REQUEST['public']=='noassign'){
            $listQuery =$noassignQuery;
        }
        $fromwhere=$this->getQuery();
        $fromwhere=split('FROM', $fromwhere);
        if(count($fromwhere)==2&&isset($_REQUEST['viewname'])){
            $fromwhere=$fromwhere[1];
            //$fromwhere=str_replace('vtiger_servicecomments.serviceid', 'vtiger_servicecomments.ownerid', $fromwhere);
        }else{
            $fromwhere="vtiger_servicecomments where 1=1";
        }

        $listQuery .= $fromwhere;*/
        $this->getSearchWhere();
        $listQuery.=$this->getUserWhere();
        $queryGenerator = $this->get('query_generator');

        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }
        $listQuery.=' order by '.$orderBy.' '.$sortOrder;
        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();

        $viewid = ListViewSession::getCurrentView($moduleName);

        ListViewSession::setSessionQuery($moduleName, $listQuery, 0);//session缓存查询条件,

        $listQuery .= " LIMIT $startIndex,".($pageLimit);
        //echo $listQuery;
        $listResult = $db->pquery($listQuery, array());


        $listViewRecordModels = array();
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['servicecommentsid'];
            $listViewRecordModels[$rawData['servicecommentsid']] = $rawData;
        }
        //var_dump($listViewRecordModels);
        return $listViewRecordModels;
	}

    public function getUserWhere(){
        $searchDepartment = $_REQUEST['department'];//部门
        //$where=getAccessibleUsers('ServiceMaintenance','List',true);
        $userid=getDepartmentUser($searchDepartment);
        $listQuery = '';
//         if(!empty($searchDepartment)){
//             if(!empty($where)&&$where!='1=1'){
//                 $where=array_intersect($where,$userid);
//             }else{
//                 $where=$userid;
//             }
//             $where=implode(',',$where);
//             $listQuery .= ' and vtiger_servicemaintenance.serviceid'.$where;
//         }

        $where=getAccessibleUsers();
        if($where!='1=1'){
        	$listQuery .= ' and vtiger_servicecomments.assignerid'.$where;
        }
//
//        if($_REQUEST['public']=='untreated'){
//            $listQuery = $listQuery." and vtiger_servicemaintenance.processstate='untreated'";
//        }if($_REQUEST['public']=='processing'){
//            $listQuery = $listQuery." and vtiger_servicemaintenance.processstate='processing'";
//        }elseif($_REQUEST['public']=='processed'){
//            $listQuery = $listQuery." and vtiger_servicemaintenance.processstate='processed'";
//        }elseif($_REQUEST['public']=='unabletoprocess'){
//            $listQuery = $listQuery." and vtiger_servicemaintenance.processstate='unabletoprocess'";
//        }elseif($_REQUEST['public']=='cancellation'){
//            $listQuery = $listQuery." and vtiger_servicemaintenance.processstate='cancellation'";
//        }
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
    public function getListViewCount() {
        $db = PearDatabase::getInstance();

        //20150430 young 自定义的sql需要重写计算总数语句
       // $listQuery= ServiceMaintenance_Record_Model::getServiceMaintenanceListSql();
        //$listQuery=$this->getTableSQL();
        //$listQuery=" WHERE vtiger_servicecomments.assigntype = 'productby'";
        $listQuery="SELECT count(1) as counts FROM vtiger_servicecomments
                     LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_servicecomments.related_to

                    LEFT JOIN vtiger_salesorderproductsrel ON  vtiger_servicecomments.salesorderproductsrelid = vtiger_salesorderproductsrel.salesorderproductsrelid
                    LEFT JOIN vtiger_products ON vtiger_salesorderproductsrel.productid = vtiger_products.productid

                        WHERE vtiger_servicecomments.assigntype = 'productby'";
        $this->getSearchWhere();
        $listQuery.=$this->getUserWhere();

        $queryGenerator = $this->get('query_generator');
        $queryGenerator->getSelectClauseColumnSQL();
        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }//end
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }

}
