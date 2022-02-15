<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Item_ListView_Model extends Vtiger_ListView_Model {
	
	//根据参数显示数据   #移动crm模拟$request请求---2015-12-16 罗志坚
	public function getListViewEntries($pagingModel,$request=array()) {
		$db = PearDatabase::getInstance();
		$moduleName ='Item';

		
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

			$orderBy = 'soncateid';

			$sortOrder = 'DESC';
		}
		$this->getSearchWhere();
        $listQuery = $this->getQuery();
        if(strstr($listQuery,'vtiger_soncate.parentcate')){
            $listQuery = str_replace('vtiger_soncate.parentcate','vtiger_parentcate.parentcate',$listQuery);
        }
        if(strstr($listQuery,'FROM vtiger_soncate')){
            $listQuery = str_replace('FROM vtiger_soncate','FROM vtiger_soncate left join vtiger_parentcate on vtiger_soncate.parentcate = vtiger_parentcate.parentcateid',$listQuery);
        }
        $listQuery = str_replace("vtiger_soncate.special","if(vtiger_soncate.special=1,'是','否') as special",$listQuery);
        $listQuery.=$this->getUserWhere();
        global $current_user;


		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();
        $listQuery .= ' AND deleted = 0 ';
        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
		
		$viewid = ListViewSession::getCurrentView($moduleName);
	
		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
	
		$listQuery .= " LIMIT $startIndex,".($pageLimit);


        //echo $listQuery;//die();
//		echo $listQuery;die;
		$listResult = $db->pquery($listQuery, array());
//dd($listResult);

		$index = 0;
		while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['soncateid'];
			$listViewRecordModels[$rawData['soncateid']] = $rawData;
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

            $public = $_REQUEST['public'];
		
            foreach($list as $fields){
            	if ($public == 'unaudited') {
					if ($fields['fieldlabel'] != 'insuredtype' && $fields['fieldlabel'] != 'name') {
            			$temp[$fields['fieldlabel']]=$fields;
            		}
				} else {
					$temp[$fields['fieldlabel']]=$fields;
				}
                
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


        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('Channels','List', true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            $listQuery .= ' and vtiger_channels.channelownerid in ('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){

                $listQuery .= ' and vtiger_channels.channelownerid '.$where . ' ';
            }
        }

        //echo $listQuery;
        //exit;
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
        $listQuery .= ' AND deleted = 0 ';
        //echo $listQuery.'<br>';die();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }

}