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
class ApanageManagement_ListView_Model extends Vtiger_ListView_Model {


    public function getListViewEntries($pagingModel) {
        $db = PearDatabase::getInstance();
        $moduleName = 'ApanageManagement';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');
        if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
            $orderBy = 'vtiger_apanagemanagement.apanagemanagementid';
            $sortOrder = 'DESC';
        }
        $listQuery = $this->getQuery();
        //获取自定义语句拼接方法
        $this->getSearchWhere();
        $listQuery.=$this->getUserWhere();
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

        //echo $listQuery;exit;
        $listResult = $db->pquery($listQuery, array());
        $listViewRecordModels = array();
        while($rawData=$db->fetch_array($listResult)) {
            $listViewRecordModels[] = $rawData;
        }
        return $listViewRecordModels;
    }

    public function getUserWhere(){
        $listQuery='';
        $searchDepartment = $_REQUEST['department'];
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){
            $departments=getChildDepartment($searchDepartment);
            $listQuery=' AND vtiger_apanagemanagement.departmentid in (\''.implode("','",$departments).'\')';;
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
    }
    public function getListViewCount() {
        if(0==$this->isAllCount && 0==$this->isFromMobile){
            return 0;
        }
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
        $where=$this->getUserWhere();
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
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
    public function getSelectClauseColumnSQL(){
        $query=parent::getSelectClauseColumnSQL();
        return str_replace(','.$this->focus->table_name.'.'.$this->focus->table_index,','.$this->focus->table_name.'.'.$this->focus->table_index.' AS recordid',$query);
    }

}
