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
 * 2015-1-20 18:12:01 王斌 增加客户筛选列表
 */
class AchievementSummaryManager_ListView_Model extends Vtiger_ListView_Model {


	public function getListViewEntries($pagingModel) {
		$db = PearDatabase::getInstance();
        $moduleName = 'AchievementSummaryManager';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        $listQuery = $this->getQuery();
        //获取自定义语句拼接方法
        $this->getSearchWhere();
        $queryGenerator = $this->get('query_generator');
        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }
        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();
        // 重新组装SQL
        $getMonth=$this->getSearchWhereAchievementmonth();
        if($getMonth){
            $getMonths=$getMonth;
            $str="vtiger_achievementsummary_managerpercent.achievementmonth >= '".$getMonth." 00:00:00'";
            $getMonth="vtiger_achievementsummary_managerpercent.achievementmonth >= '".$getMonth."'";
            $listQuery=str_replace($str,$getMonth,$listQuery);
            $str="vtiger_achievementsummary_managerpercent.achievementmonth <= '".$getMonths." 00:00:00'";
            $getMonths="vtiger_achievementsummary_managerpercent.achievementmonth <= '".$getMonths."'";
            $listQuery=str_replace($str,$getMonths,$listQuery);
        }

        if(empty($orderBy) && empty($sortOrder)){
            $orderBy = ' vtiger_achievementsummary_managerpercent.achievementmonth ';
            $sortOrder = 'DESC';
        }
        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
        $viewid = ListViewSession::getCurrentView($moduleName);
        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
        $listQuery .= " LIMIT $startIndex,".($pageLimit);


        $listResult = $db->pquery($listQuery, array());
        $listViewRecordModels = array();
        while($rawData=$db->fetch_array($listResult)) {
            $listViewRecordModels[] = $rawData;
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
	        foreach($list as $fields){
	            $temp[$fields['fieldlabel']]=$fields;
	        }
	        return $temp;
	    }
	    return $queryGenerator->getFocus()->list_fields_name;
	}

    public function getListViewLinks($linkParams) {
        $links=array();
        return $links;
        exit;
    }
//全局搜索
    public function getSearchWhereAchievementmonth(){

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
                if(is_array($SearchConditionRow)&&!empty($SearchConditionRow)){
                    foreach($SearchConditionRow as $key=>$val){

                        $val=str_replace('SearchConditionRow','',$val);
                        $searchKey=$BugFreeQuery['BugFreeQuery[field'.$val.']'];
                        $operator=$BugFreeQuery['BugFreeQuery[operator'.$val.']'];
                        $searchValue=$BugFreeQuery['BugFreeQuery[value'.$val.']'];
                        if(strpos($searchKey,'achievementmonth') && ($operator=='>='||$operator=='<=')){
                            return $searchValue;
                        }
                    }
                }
            }
        }
        return false;
    }
    public function getListViewCount() {
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
        $listQuery =  $queryGenerator->getQueryCount();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }


}
