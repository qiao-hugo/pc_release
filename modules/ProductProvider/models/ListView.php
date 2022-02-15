<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ProductProvider_ListView_Model extends Vtiger_ListView_Model {
	
	//根据参数显示数据   #移动crm模拟$request请求---2015-12-16 罗志坚
	public function getListViewEntries($pagingModel,$request=array()) {
		$db = PearDatabase::getInstance();
		$moduleName ='ProductProvider';

		
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

			$orderBy = 'productproviderid';

			$sortOrder = 'DESC';
		}
		$this->getSearchWhere();
        // 如果包含 账户ID 和账户名称查询的获取 所有id
        $specialSearchWhere=$this->getSpecialSearchWhere();
        $productproviders='';
        if(!empty($specialSearchWhere)){
            $productproviders=" AND vtiger_productprovider.productproviderid IN(SELECT vtiger_productprovider_detail.productproviderid FROM vtiger_productprovider_detail WHERE 1=1  ".$specialSearchWhere." GROUP BY vtiger_productprovider_detail.productproviderid )";
        }
        $listQuery = $this->getQuery();
        $listQuery=str_replace('vtiger_productprovider.modulestatus,','vtiger_productprovider.modulestatus,(SELECT vtiger_users.email1 FROM vtiger_users WHERE vtiger_users.id=vtiger_crmentity.smownerid LIMIT 1) as email,',$listQuery);
        $listQuery.=$this->getUserWhere();
        global $current_user;

		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();
        $listQuery.=$productproviders;
        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
		
		$viewid = ListViewSession::getCurrentView($moduleName);
	
		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
	
		$listQuery .= " LIMIT $startIndex,".($pageLimit);


        //echo $listQuery;//die();
		//echo $listQuery;die;
		$listResult = $db->pquery($listQuery, array());


		$index = 0;
		while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['productproviderid'];
			$listViewRecordModels[$rawData['productproviderid']] = $rawData;
		}
		return $listViewRecordModels;
	}

// 特殊处理搜索条件
    public function getSearchWhere(){
        $searchKey = $this->get('search_key');
        $queryGenerator = $this->get('query_generator');
        $queryGenerator -> addSearchWhere('');//置空
        $searchValue = $this->get('search_value');
        $operator = $this->get('operator');
        if(!empty($searchKey)) {
            $queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator ,'leftkh'=>'','rightkh'=>'','andor'=>''));
        }
        $BugFreeQuery=isset($_REQUEST['BugFreeQuery'])?$_REQUEST['BugFreeQuery']:'';
        if(!empty($BugFreeQuery)){
            $BugFreeQuery=json_decode($BugFreeQuery,true);
            if(isset($BugFreeQuery['BugFreeQuery[queryRowOrder]'])){
                $SearchConditionRow=$BugFreeQuery['BugFreeQuery[queryRowOrder]'];
                $SearchConditionRow=explode(',',$SearchConditionRow);
                $counts=count($SearchConditionRow);
                if(is_array($SearchConditionRow)&&!empty($SearchConditionRow)){
                    foreach($SearchConditionRow as $key=>$val){
                        $val=str_replace('SearchConditionRow','',$val);
                        $leftkh=$BugFreeQuery['BugFreeQuery[leftParenthesesName'.$val.']'];
                        $rightkh=$BugFreeQuery['BugFreeQuery[rightParenthesesName'.$val.']'];
                        $andor=$BugFreeQuery['BugFreeQuery[andor'.$val.']'];
                        $searchKey=$BugFreeQuery['BugFreeQuery[field'.$val.']'];
                        $operator=$BugFreeQuery['BugFreeQuery[operator'.$val.']'];
                        $searchValue=$BugFreeQuery['BugFreeQuery[value'.$val.']'];
                        if($searchKey!='department' && !strpos($searchKey,'tiger_productprovider.accountzh') && !strpos($searchKey,'tiger_productprovider.idaccount') ){
                            if(($key+2)==$counts){
                                $queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator ,'leftkh'=>$leftkh,'rightkh'=>$rightkh,'andor'=>'',"counts"=>$counts));
                            }else{
                                $queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator ,'leftkh'=>$leftkh,'rightkh'=>$rightkh,'andor'=>$andor,"counts"=>$counts));
                            }
                        }
                    }
                }
            }
        }
    }

    // 获取 账户id 或者 账户名称的查询条件
    public function getSpecialSearchWhere(){
        $BugFreeQuery=isset($_REQUEST['BugFreeQuery'])?$_REQUEST['BugFreeQuery']:'';
        $searchWhere='';
        if(!empty($BugFreeQuery)){
            $BugFreeQuery=json_decode($BugFreeQuery,true);
            if(isset($BugFreeQuery['BugFreeQuery[queryRowOrder]'])){
                $SearchConditionRow=$BugFreeQuery['BugFreeQuery[queryRowOrder]'];
                $SearchConditionRow=explode(',',$SearchConditionRow);
                if(is_array($SearchConditionRow)&&!empty($SearchConditionRow)){
                    foreach($SearchConditionRow as $key=>$val){
                        $val=str_replace('SearchConditionRow','',$val);
                        $searchKey=$BugFreeQuery['BugFreeQuery[field'.$val.']'];
                        $searchValue=$BugFreeQuery['BugFreeQuery[value'.$val.']'];
                        $searchValue=trim($searchValue);
                        if(strpos($searchKey,'tiger_productprovider.accountzh')){
                            $searchWhere.=" AND vtiger_productprovider_detail.accountzh like '%".$searchValue."%'";
                        }
                        if(strpos($searchKey,'tiger_productprovider.idaccount') ){
                            $searchWhere.=" AND vtiger_productprovider_detail.idaccount like '%".$searchValue."%'";
                        }
                    }
                }
            }
        }
        return $searchWhere;
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

        if($sourceModule=='RefillApplication'){
            $date=date("Y-m-d");
            $listQuery.=" AND vtiger_productprovider.modulestatus='c_complete' 
                          AND vtiger_productprovider.effectivestartaccount>='".$date."' 
                          AND vtiger_productprovider.effectiveendaccount<='".$date."'";
        }else{
            $accountQuery=' OR EXISTS(SELECT 1 FROM vtiger_crmentity as crmtable WHERE crmtable.crmid=vtiger_productprovider.accountid AND crmtable.smownerid='.$current_user->id.')';
            if (!empty($searchDepartment) && $searchDepartment != 'H1') {
                $userid = getDepartmentUser($searchDepartment);
                $where = getAccessibleUsers('ProductProvider', 'List', true);
                if ($where != '1=1') {
                    $where = array_intersect($where, $userid);
                } else {
                    $where = $userid;
                }
                $where=!empty($where)?$where:array(-1);
                $listQuery .= ' and (vtiger_crmentity.smownerid in (' . implode(',', $where) . ')'.$accountQuery.')';
            } else {
                $where = getAccessibleUsers();
                if ($where != '1=1') {

                    $listQuery .= ' and (vtiger_crmentity.smownerid ' . $where .$accountQuery.')';
                }
            }
        }
        return $listQuery;
    }
    public function getListViewCount() {
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
		//print_r(debug_backtrace(0));
        //搜索条件
        //$this->getSearchWhere();
        // 如果包含 账户ID 和账户名称查询的获取 所有id
        $specialSearchWhere=$this->getSpecialSearchWhere();
        $productproviders='';
        if(!empty($specialSearchWhere)){
            $productproviders=" AND vtiger_productprovider.productproviderid IN(SELECT vtiger_productprovider_detail.productproviderid FROM vtiger_productprovider_detail WHERE 1=1  ".$specialSearchWhere." GROUP BY vtiger_productprovider_detail.productproviderid )";
        }
        //用户条件
        $where=$this->getUserWhere();
        //$where.= ' AND accountname is NOT NULL';
        $queryGenerator->addUserWhere($where);
        $listQuery = $queryGenerator->getQueryCount();
        $listQuery.= $productproviders;
        //echo $listQuery.'<br>';die();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }

}