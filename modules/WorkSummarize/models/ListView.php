<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class WorkSummarize_ListView_Model extends Vtiger_ListView_Model {


	//根据参数显示数据
	public function getListViewEntries($pagingModel) {
		$db = PearDatabase::getInstance();
		$moduleName ='WorkSummarize';


		$orderBy = $this->getForSql('orderby');
		$sortOrder = $this->getForSql('sortorder');

		//List view will be displayed on recently created/modified records
		//列表视图将显示最近的创建修改记录  ---做什么用处
		if(empty($orderBy) && empty($sortOrder)){

			$orderBy = 'vtiger_worksummarize.worksummarizeid';
			$sortOrder = 'DESC';
		}
        /*
        $listQuery="SELECT
						vtiger_worksummarize.worksummarizename,
						IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT	departmentname	FROM vtiger_departments	WHERE	departmentid = (SELECT	departmentid	FROM	vtiger_user2department WHERE	userid = vtiger_users.id LIMIT 1)),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name	FROM vtiger_users	WHERE	vtiger_worksummarize.smownerid = vtiger_users.id),'--') AS smownerid,
						IFNULL((SELECT GROUP_CONCAT(last_name,'[',IFNULL((SELECT	departmentname	FROM vtiger_departments	WHERE	departmentid = (SELECT	departmentid	FROM	vtiger_user2department WHERE	userid = vtiger_users.id LIMIT 1)),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name	FROM vtiger_users	WHERE	 FIND_IN_SET(vtiger_users.id,REPLACE(vtiger_worksummarize.touser,' |##| ',','))),'--') AS touser,

						vtiger_worksummarize.todaycontent,
						vtiger_worksummarize.tommorrowcontent,
						vtiger_worksummarize.replytimes,
						vtiger_worksummarize.createdtime,
						vtiger_worksummarize.file,
						vtiger_worksummarize.worksummarizeid
					FROM
						vtiger_worksummarize
					WHERE 1=1 ";


        $this->getSearchWhere();
        $listQuery.=$this->getUserWhere();

        $queryGenerator = $this->get('query_generator');
        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }*/
        $this->getSearchWhere();
        $listQuery = $this->getQuery();
        $listQuery=str_replace('FROM',',(SELECT last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_worksummarize.smownerid) as lastname FROM',$listQuery);
        $listQuery.=$this->getUserWhere();
        global $current_user;


        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();

        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
		
		$viewid = ListViewSession::getCurrentView($moduleName);
	
		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
	
		$listQuery .= " LIMIT $startIndex,".($pageLimit);

        //echo $listQuery;//die();

		$listResult = $db->pquery($listQuery, array());


		$index = 0;
		while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['worksummarizeid'];
			$listViewRecordModels[$rawData['worksummarizeid']] = $rawData;
		}
		//echo $listQuery;
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
                $temp[$fields['fieldlabel']]=$fields;
            }
           return $temp;
        }
        return $queryGenerator->getFocus()->list_fields_name;
        
    }
    public function getUserWhere(){
       global $current_user;
        $searchDepartment = $_REQUEST['department'];
        $sourceModule = $this->get('src_module');
        $listQuery=' ';
		$where=getAccessibleUsers();
		if($_REQUEST['filter']=='owner'){
			$listQuery .= " AND vtiger_worksummarize.smownerid ='{$current_user->id}'";
		}elseif($_REQUEST['filter']=='reply'){
			if($where!='1=1'){

                $listQuery.=" AND vtiger_worksummarize.replytimes = 0 ";
				$today=date("Y-m-d");
				$threeday=date("Y-m-d",strtotime("-7 day"));
				$listQuery.=" AND TO_DAYS(vtiger_worksummarize.createdtime) BETWEEN TO_DAYS('{$threeday}') AND TO_DAYS('{$today}')";
				$listQuery .= "  AND vtiger_worksummarize.smownerid!={$current_user->id}";

			}
		}
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150610 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('','',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            $arr1=WorkSummarize_Record_Model::getUserNowrite('nowrite');

            if(count($arr1['nowriteuserid'])>0){
                //直接求两个数组的差集
                $where=array_diff($where,$arr1['nowriteuserid']);
            }
            if(count($where)==0){
                //给一个不存在的账号防止报错
                $where=array(-1);
            }
            $listQuery .= " AND vtiger_worksummarize.smownerid in(".implode(',',$where).")  ";

        }else{
            $where=getAccessibleUsers('WorkSummarize','List',true);
            if($where!='1=1'){
                $arr1=WorkSummarize_Record_Model::getUserNowrite('nowrite');

                if(count($arr1['nowriteuserid'])>0){
                    //直接求两个数组的差集
                    $where=array_diff($where,$arr1['nowriteuserid']);
                }
                if(count($where)==0){
                    //给一个不存在的账号防止报错
                    $where=array(-1);
                }
                $listQuery .= " AND (vtiger_worksummarize.smownerid in(".implode(',',$where).") OR  FIND_IN_SET({$current_user->id},replace(touser,' |##| ',',')))";

            }
        }
        //过滤掉离职人员
        $listQuery.=" AND EXISTS (SELECT 1 FROM vtiger_users WHERE STATUS='Active' AND vtiger_users.id=vtiger_worksummarize.smownerid) ";


        return $listQuery;
    }
    public function getListViewCount() {
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
//print_r(debug_backtrace(0));
        //搜索条件
        //$this->getSearchWhere();
        //用户条件
        $where=$this->getUserWhere();
        //$where.= ' AND accountname is NOT NULL';
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();

        //echo $listQuery.'<br>';
        //die();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }


}