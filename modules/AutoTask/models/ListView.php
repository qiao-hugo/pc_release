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
 * Vtiger ListView Model Class
 */
class AutoTask_ListView_Model extends Vtiger_ListView_Model {

	public function getListViewEntries($pagingModel) {

		$db = PearDatabase::getInstance();
        //跟新一些需要使用的数据；$this->updatedata();
        $moduleName = 'autoworkflowentitys';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
            $orderBy = 'createdtime';
            $sortOrder = 'DESC';
        }
        //根据这里生成基本SQL语句
        $listQuery = $this->getQuery();
        //获取 自定义语句拼接方法
        $this->getSearchWhere();
        $listQuery.=$this->getUserWhere();
        $queryGenerator = $this->get('query_generator');
        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }

        $startIndex = $pagingModel->getStartIndex();

        $pageLimit = $pagingModel->getPageLimit();

        //echo $listQuery;die;
        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
        
        $viewid = ListViewSession::getCurrentView($moduleName);
        
        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,

        $listQuery .= " LIMIT $startIndex,".($pageLimit);
         
        //echo $listQuery;die();
        $listResult = $db->pquery($listQuery, array());
        $listViewRecordModels = array();
        global $current_user;
        $index = 0;
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['autoworkflowentityid'];
            $listViewRecordModels[$rawData['autoworkflowentityid']] = $rawData;
        }
        return $listViewRecordModels;
	}

    public function updatedata(){
        $adb = PearDatabase::getInstance();
        $selectsql = "SELECT
	aa.autoworkflowentityid,
	(SELECT vtiger_servicecomments.serviceid FROM vtiger_servicecomments WHERE related_to= aa.accountid LIMIT 1) AS service,
	(SELECT bb.smownerid FROM vtiger_crmentity bb WHERE bb.crmid= aa.accountid) AS 'assigner',
	(select GROUP_CONCAT(cc.autoworkflowtaskname SEPARATOR '<br>') from vtiger_autoworkflowtaskentitys cc WHERE cc.autoworkflowentityid = aa.autoworkflowentityid AND isaction='1') AS 'taskentitynames',
	(SELECT dd.Receiveid FROM vtiger_servicecontracts dd WHERE aa.crmid = dd.servicecontractsid) AS 'receiveid'
FROM
	vtiger_autoworkflowentitys aa";
        $updatesql = 'UPDATE vtiger_autoworkflowentitys aa SET service = ?,taskentitynames = ?,receiveid=?,assigner=? WHERE aa.autoworkflowentityid = ? ';
        $sel_result = $adb->pquery($selectsql);
        if($adb->num_rows($sel_result)>0){
            for ($i=0;$i<$adb->num_rows($sel_result);$i++){
                $temp=$adb->fetchByAssoc($sel_result);
              //  $adb->pquery($updatesql,array($temp['service'],htmlspecialchars_decode($temp['taskentitynames']),$temp['receiveid'],$temp['assigner'],$temp['autoworkflowentityid']));
            }
        }
    }
	public function getUserWhere(){
	    $listQuery='';
        global $current_user;
        //添加权限控制；
        $where=getAccessibleUsers('AutoTask','List',false);
       // var_dump($where);die;
        if($where!='1=1'){
            $autoflowid = AutoTask_Detail_View::userflowid();
            $wherestring =  implode(",",$autoflowid);
            if(empty($autoflowid)){
                $listQuery .="  AND  vtiger_autoworkflowentitys.service ".$where;
            }else{
                $listQuery .="  AND vtiger_autoworkflowentitys.autoworkflowid IN (".$wherestring.")"." OR vtiger_autoworkflowentitys.service ".$where;
            }
            //角色
            //$where = ' (roleid ='.$current_user->current_user_roles;
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
	        foreach($list as $fields){
	            $temp[$fields['fieldlabel']]=$fields;
	        }
	        return $temp;
	    }
	    return $queryGenerator->getFocus()->list_fields_name;
	}
	public function getListViewCount() {
	      $db = PearDatabase::getInstance();
	    $queryGenerator = $this->get('query_generator');
	
	    $where=$this->getUserWhere();
	
	    $queryGenerator->addUserWhere($where);
	    $listQuery =  $queryGenerator->getQueryCount();
	    $listResult = $db->pquery($listQuery, array());
	    return $db->query_result($listResult,0,'counts'); 
	    
	    $listResult = $db->pquery($listQuery, array());
	    return $db->num_rows($listResult);
	}
	
    }
