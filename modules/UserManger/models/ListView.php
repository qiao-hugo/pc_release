<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class UserManger_ListView_Model extends Vtiger_ListView_Model {
	//根据参数显示数据   #移动crm模拟$request请求---2015-12-16 罗志坚
	public function getListViewEntries($pagingModel,$request=array()) {
		$db = PearDatabase::getInstance();
		$moduleName ='UserManger';

		
		if(!empty($request)){
			if(isset($request['BugFreeQuery'])){
				$_REQUEST['BugFreeQuery'] = $request['BugFreeQuery'];
			}
			if(isset($request['public'])){
				$_REQUEST['public'] = $request['public'];
			}
		}

		$orderBy = $this->getForSql('orderby');
		$sortOrder = $this->getForSql('sortorder');

		//List view will be displayed on recently created/modified records
		//列表视图将显示最近的创建修改记录  ---做什么用处
		if(empty($orderBy) && empty($sortOrder)){

			$orderBy = 'usermangerid';
            //$orderBy = 'vtiger_crmentity.modifiedtime';
			$sortOrder = 'DESC';
		}
		$this->getSearchWhere();
        $listQuery = $this->getQuery();
        
        $listQuery.=$this->getUserWhere();
        global $current_user;
        $listQuery = str_replace("SELECT","SELECT vtiger_usermanger.companyid,vtiger_usermanger.ownornot,",$listQuery);
        //$listQuery=str_replace("vtiger_usermanger.secondroleid,","(SELECT GROUP_CONCAT(asrole.rolename SEPARATOR '<br>') FROM vtiger_role as asrole WHERE FIND_IN_SET(asrole.roleid,REPLACE(vtiger_usermanger.secondroleid,' |##| ',','))) as secondroleid,",$listQuery);
        $listQuery=str_replace(",vtiger_usermanger.usermangerid",",vtiger_usermanger.userid,vtiger_usermanger.usermangerid",$listQuery);
		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();
		$viewid = ListViewSession::getCurrentView($moduleName);
		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,

		$listQuery .= '  ORDER BY '. $orderBy . ' ' .$sortOrder;;
		$listQuery .= " LIMIT $startIndex,".($pageLimit);

        //echo $listQuery;die();

		$listResult = $db->pquery($listQuery, array());


		$index = 0;
		while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['usermangerid'];
            $rawData['secondroleid']=$this->secordRoleName($rawData['secondroleid']);
			$listViewRecordModels[$rawData['usermangerid']] = $rawData;
		}
		return $listViewRecordModels;
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
                if($fields['fieldname']=='status'){
                    $fields['uitype']=15;
                }
                $temp[$fields['fieldlabel']]=$fields;
            }
           return $temp;
        }
        return $queryGenerator->getFocus()->list_fields_name;
    }
    public function getUserWhere(){
        $searchDepartment = $_REQUEST['department'];
        $listQuery=' ';
        $sourceModule = $this->get('src_module');
        if($sourceModule=='UserManger'){
            $listQuery=" AND vtiger_usermanger.userid>0 AND vtiger_usermanger.`status`='Active' AND vtiger_usermanger.isdimission=0 AND vtiger_usermanger.modulestatus='c_complete'";
        }
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('UserManger','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            $listQuery .= ' and (vtiger_crmentity.smownerid in ('.implode(',',$where).') OR (vtiger_usermanger.userid>0 AND vtiger_usermanger.userid in('.implode(',',$where).')))';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                $listQuery .= ' and (vtiger_crmentity.smownerid '.$where . ' OR (vtiger_usermanger.userid>0 AND vtiger_usermanger.userid '.$where.'))';
            }
        }
        return $listQuery;
    }
    public function getListViewCount() {
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
        //$where=$this->getUserWhere();
        //$queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
        $listQuery.=  $this->getUserWhere();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
    public function secordRoleName($value){
        if(!empty($value)){
            global $roles;
            require 'crmcache/role.php';
            $value=explode(' |##| ',$value);
            $value=array_map(function($v){
                global $roles;
                return str_replace(array('|','—'),array(''),$roles[$v]);},$value);
            return implode(',',$value);
        }
        return '';

    }
}