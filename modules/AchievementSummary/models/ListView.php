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
class AchievementSummary_ListView_Model extends Vtiger_ListView_Model {


	public function getListViewEntries($pagingModel) {
		$db = PearDatabase::getInstance();
        $moduleName = 'AchievementSummary';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');
        if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
            $orderBy = 'vtiger_achievementsummary.achievementmonth DESC,vtiger_achievementsummary.createtime';
            $sortOrder = 'DESC';
        }
        $listQuery = $this->getQuery();
        //获取自定义语句拼接方法
        $this->getSearchWhere();
        $listQuery.=$this->getUserWhere();
        $listQuery=str_replace(',vtiger_achievementsummary.achievementid FROM vtiger_achievementsummary',',vtiger_achievementsummary.achievementid FROM vtiger_achievementsummary LEFT JOIN vtiger_achievementsupdate ON (vtiger_achievementsupdate.uuserid=vtiger_achievementsummary.userid AND vtiger_achievementsupdate.uachievementmonth=vtiger_achievementsummary.achievementmonth AND vtiger_achievementsupdate.uachievementtype=vtiger_achievementsummary.achievementtype AND vtiger_achievementsupdate.uperformancetype=vtiger_achievementsummary.performancetype AND vtiger_achievementsupdate.deleted=0)',$listQuery);
        $queryGenerator = $this->get('query_generator');
        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }
        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();
        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
        $viewid = ListViewSession::getCurrentView($moduleName);
        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
        $listQuery .= " LIMIT $startIndex,".($pageLimit);
        $getMonth=$this->getSearchWhereAchievementmonth();
        if($getMonth){
            $getMonths=$getMonth;
            $str="vtiger_achievementsummary.achievementmonth >= '".$getMonth." 00:00:00'";
            $getMonth="vtiger_achievementsummary.achievementmonth >= '".$getMonth."'";
            $listQuery=str_replace($str,$getMonth,$listQuery);
            $str="vtiger_achievementsummary.achievementmonth <= '".$getMonths." 00:00:00'";
            $getMonths="vtiger_achievementsummary.achievementmonth <= '".$getMonths."'";
            $listQuery=str_replace($str,$getMonths,$listQuery);
        }
//        echo $listQuery;exit;
        $listResult = $db->pquery($listQuery, array());
        $listViewRecordModels = array();
        while($rawData=$db->fetch_array($listResult)) {
            if($rawData['signdate']=='0000-00-00'){
                $rawData['signdate']='';
            }
            if($rawData['createtime']=='0000-00-00'){
                $rawData['createtime']='';
            }
            if($rawData['reality_date']=='0000-00-00'){
                $rawData['reality_date']='';
            }
            if($rawData['achievementmonth']=='0000-00-00'){
                $rawData['achievementmonth']='';
            }
            $listViewRecordModels[] = $rawData;
        }

        return $listViewRecordModels;

	}

    public function getUserWhere(){
        $listQuery='';
        $searchDepartment = $_REQUEST['department'];
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){
            $departments=getChildDepartment($searchDepartment);
            $where=getAccessibleUsers('AchievementSummary','List',true);
            if($where!='1=1'){
                $query=' and vtiger_achievementsummary.userid in ('.implode(',',$where).')  AND vtiger_achievementsummary.departmentid in (\''.implode("','",$departments).'\')';
            }else{
                $query=' AND vtiger_achievementsummary.departmentid in (\''.implode("','",$departments).'\')';;
            }
            $listQuery .= $query;
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                $listQuery .= ' and vtiger_achievementsummary.userid'.$where;
            }
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
        $where=$this->getUserWhere();
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
        $getMonth=$this->getSearchWhereAchievementmonth();
        if($getMonth){
            $str="vtiger_achievementallot_statistic.achievementmonth >= '".$getMonth." 00:00:00'";
            $getMonth="vtiger_achievementallot_statistic.achievementmonth >= '".$getMonth."'";
            $listQuery=str_replace($str,$getMonth,$listQuery);
        }
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
    function getQuery() {
        $queryGenerator = $this->get('query_generator');
        $UpdatequeryGenerator=new UpdatequeryGenerator($queryGenerator);
        $listQuery = $UpdatequeryGenerator->getQuery();
        return $listQuery;
    }
}
global $root_directory;
include_once $root_directory.'include/QueryGenerator/KQueryGenerator.php';
class UpdatequeryGenerator extends KQueryGenerator{
    function __construct($queryGenerator)
    {
        global $current_user;
        parent::__construct($current_user,$queryGenerator->getFocus(), $queryGenerator->getModule());
    }
    public function getSQLColumn($field)
    {
        $column=$field['columnname'];
        $baseTable = $this->focus->table_name;
        $baseTableIndex = $this->focus->table_index;
        if($field['uitype']==56){
            return "IF(".$field['tablename'].".".$column."=1,'是','否') as ".$column;
        }
        //2015-05-28 自定义字段
        if(!empty($field['reldefaultfield'])){
            return $field['reldefaultfield'];
        }
        if($field['fieldtype']=='reference'&&!empty($field['reltablename'])){
            if($field['uitype']==53){
                return '(select last_name from vtiger_users where '.$field['reltablename'].'.'.$field['reltablefield'].'=vtiger_users.id) as ' . $column;
            }else{
                if(!isset($this->leftjoin[$field['reltablename']])) {  //reltablefield 关联表主键  relentityidfield 原表主键 reltablecol关联表要显示的字段
                    if($field['listtabid']!=2){
                        if($field['relleftjoin']){  // 当需要关联另外一个leftjoin 的表使用
                            $this->leftjoin[$field['reltablename']] = " LEFT JOIN " . $field['reltablename'] . " ON  " . $field['reltablename'] . "." . $field['reltablefield'] . "=" . $field['relleftjoin']."";
                        }else{
                            $this->leftjoin[$field['reltablename']] = ' LEFT JOIN ' . $field['reltablename'] . ' ON ' . $field['reltablename'] . '.' . $field['reltablefield'] . '=' . $field['tablename'] . '.' . $field['relentityidfield'];
                        }
                    }

                }
                if(in_array($field['uitype'],array(15,16))){   //下拉类型不产生关联
                    return ' ('.$field['reltablename'].'.'.$field['reltablecol'].') as '.$column.'';
                }
                if($field['listtabid']==2){
                    return ' ('.$field['tablename'].'.'.$column.'_name) as '.$column.','.$field['tablename'].'.'.$column.' as '.$column.'_'.$field['fieldtype'];
                }
                return ' ('.$field['reltablename'].'.'.$field['reltablecol'].') as '.$column.','.$field['tablename'].'.'.$column.' as '.$column.'_'.$field['fieldtype'];

            }

        }
        if($field['uitype']==73){
            return "(select GROUP_CONCAT(label) from vtiger_crmentity where vtiger_crmentity.crmid in(replace(".$field['tablename'] . '.' . $column.",' |##| ',','))) as ".$column;
        }

        if ($field['tablename'] == 'vtiger_crmentity') {
            if (in_array($column, array('smownerid', 'modifiedby', 'smcreatorid'))) {
                return 'IFNULL((select CONCAT(last_name,\'[\',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),\'\'),\']\',(if(`status`=\'Active\',\'\',\'[离职]\'))) as last_name from vtiger_users where vtiger_crmentity.' . $column . '=vtiger_users.id),\'--\') as ' . $column . ',vtiger_crmentity.' . $column . ' as ' . $column . '_' . $field['fieldtype'];
            }else{
                //return '(select ' . $column . ' from vtiger_crmentity where vtiger_crmentity.crmid=' .$baseTable . '.' . $baseTableIndex . ' and vtiger_crmentity.deleted=0) as ' . $column;
                return 'vtiger_crmentity.'.$column;
            }

        }

        if($field['uitype']==53){
            return '(select last_name from vtiger_users where '.$baseTable.'.'.$column.'=vtiger_users.id) as ' . $column;
        }//end

        //20150430 young 解决51的关联问题
        if($field['uitype']==51){
            return ' IFNULL((select label from vtiger_crmentity where crmid='.$baseTable.'.'.$column.'),\'--\') as ' . $column;
        }//end


        if($field['tablename']==$baseTable){
            return $field['tablename'] . '.' . $column;
        }else{
            return $field['tablename'] . '.' . $column;
        }

    }
    public function getQuery()
    {
        if (empty($this->query)) {

            $query = "SELECT ";
            $query .= $this->getSelectClauseColumnSQL();
            $query .= $this->getFromClause();
            $query .= $this->getWhereClause();


            $this->query = $query;
            return $query;
        } else {
            return $this->query;
        }
    }

}
