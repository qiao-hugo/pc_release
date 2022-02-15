<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Medium_ListView_Model extends Vtiger_ListView_Model {
	
	//根据参数显示数据   #移动crm模拟$request请求---2015-12-16 罗志坚
	public function getListViewEntries($pagingModel,$request=array()) {
		$db = PearDatabase::getInstance();
		$moduleName ='Medium';

		
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

			$orderBy = 'mediumid';

			$sortOrder = 'DESC';
		}
		$this->getSearchWhere();
        $listQuery = $this->getQuery();
        
        $listQuery.=$this->getUserWhere();
        global $current_user;


		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();

        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
		
		$viewid = ListViewSession::getCurrentView($moduleName);
	
		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
	
		$listQuery .= " LIMIT $startIndex,".($pageLimit);
		$search=array(
		    'vtiger_medium.majoradvertising,',
            'vtiger_medium.adsname,',
            'vtiger_medium.channelposition,',
            'vtiger_medium.recentmaintenancetime,',
            'vtiger_medium.billingmode,',
            'vtiger_medium.unitprice,',
            'vtiger_medium.cpcaverageprice,',
            'vtiger_medium.cpr,',
            'vtiger_medium.consumetaskcompletion,',
            'vtiger_medium.returnproportion,',
            'vtiger_medium.salesauthority,',
            'vtiger_medium.salesdirectorauthority,',
            'vtiger_medium.vpauthority,',
            'vtiger_medium.remarks,');
		$replace=array(
		    "(SELECT GROUP_CONCAT(vtiger_adsname.majoradvertising SEPARATOR '<br>') FROM vtiger_adsname WHERE  deleted=0 AND vtiger_adsname.mediumid=vtiger_medium.mediumid) AS majoradvertising,",
            "(SELECT GROUP_CONCAT(vtiger_adsname.adsname SEPARATOR '<br>') FROM vtiger_adsname WHERE  deleted=0 AND vtiger_adsname.mediumid=vtiger_medium.mediumid) AS adsname,",
            "(SELECT GROUP_CONCAT(vtiger_adsname.channelposition SEPARATOR '<br>') FROM vtiger_adsname WHERE  deleted=0 AND vtiger_adsname.mediumid=vtiger_medium.mediumid) AS channelposition,",
            "(SELECT GROUP_CONCAT(vtiger_adsname.recentmaintenancetime SEPARATOR '<br>') FROM vtiger_adsname WHERE  deleted=0 AND vtiger_adsname.mediumid=vtiger_medium.mediumid) AS recentmaintenancetime,",
            "(SELECT GROUP_CONCAT(vtiger_adsname.billingmode SEPARATOR '<br>') FROM vtiger_adsname WHERE  deleted=0 AND vtiger_adsname.mediumid=vtiger_medium.mediumid) AS billingmode,",
            "(SELECT GROUP_CONCAT(vtiger_adsname.unitprice SEPARATOR '<br>') FROM vtiger_adsname WHERE  deleted=0 AND vtiger_adsname.mediumid=vtiger_medium.mediumid) AS unitprice,",
            "(SELECT GROUP_CONCAT(vtiger_adsname.cpcaverageprice SEPARATOR '<br>') FROM vtiger_adsname WHERE  deleted=0 AND vtiger_adsname.mediumid=vtiger_medium.mediumid) AS cpcaverageprice,",
            "(SELECT GROUP_CONCAT(vtiger_adsname.cpr SEPARATOR '<br>') FROM vtiger_adsname WHERE  deleted=0 AND vtiger_adsname.mediumid=vtiger_medium.mediumid) AS cpr,",
            "(SELECT GROUP_CONCAT(vtiger_firmpolicy.consumetaskcompletion SEPARATOR '<br>') FROM vtiger_firmpolicy WHERE  deleted=0 AND vtiger_firmpolicy.mediumid=vtiger_medium.mediumid) AS consumetaskcompletion,",
            "(SELECT GROUP_CONCAT(vtiger_firmpolicy.returnproportion SEPARATOR '<br>') FROM vtiger_firmpolicy WHERE  deleted=0 AND vtiger_firmpolicy.mediumid=vtiger_medium.mediumid) AS returnproportion,",
            "(SELECT GROUP_CONCAT(vtiger_firmpolicy.salesauthority SEPARATOR '<br>') FROM vtiger_firmpolicy WHERE  deleted=0 AND vtiger_firmpolicy.mediumid=vtiger_medium.mediumid) AS salesauthority,",
            "(SELECT GROUP_CONCAT(vtiger_firmpolicy.salesdirectorauthority SEPARATOR '<br>') FROM vtiger_firmpolicy WHERE  deleted=0 AND vtiger_firmpolicy.mediumid=vtiger_medium.mediumid) AS salesdirectorauthority,",
            "(SELECT GROUP_CONCAT(vtiger_firmpolicy.vpauthority SEPARATOR '<br>') FROM vtiger_firmpolicy WHERE  deleted=0 AND vtiger_firmpolicy.mediumid=vtiger_medium.mediumid) AS vpauthority,",
            "(SELECT GROUP_CONCAT(vtiger_firmpolicy.remarks SEPARATOR '<br>') FROM vtiger_firmpolicy WHERE  deleted=0 AND vtiger_firmpolicy.mediumid=vtiger_medium.mediumid) AS remarks,");

        $listQuery=str_replace($search,$replace,$listQuery);
        //echo $listQuery;die();
		//echo $listQuery;die;
		$listResult = $db->pquery($listQuery, array());


		$index = 0;
		while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['mediumid'];
			$listViewRecordModels[$rawData['mediumid']] = $rawData;
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
            $where=getAccessibleUsers('Medium','List', true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            $listQuery .= ' and vtiger_medium.mediadocking in ('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){

                $listQuery .= ' and vtiger_medium.mediadocking '.$where . ' ';
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
        //echo $listQuery.'<br>';die();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }

}