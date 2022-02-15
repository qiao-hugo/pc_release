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
 * JobAlerts ListView Model Class
 */
class JobAlerts_ListView_Model extends Vtiger_ListView_Model {
	
    public function getListViewEntries($pagingModel) {
        $db = PearDatabase::getInstance();
        $moduleName ='JobAlerts';


        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        //List view will be displayed on recently created/modified records
        //列表视图将显示最近的创建修改记录  ---做什么用处
        if(empty($orderBy) && empty($sortOrder)){

            $orderBy = 'jobalertsid';
            //$orderBy = 'vtiger_crmentity.modifiedtime';
            $sortOrder = 'DESC';
        }
        $this->getSearchWhere();
        $listQuery = $this->getQuery();

        $listQuery.=$this->getUserWhere();
        $type = $_REQUEST['public'];
        //检索条件获取
        $listQuery.= JobAlerts_Record_Model::getWhereCondition($type);

        global $current_user;


        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();

        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;

        $viewid = ListViewSession::getCurrentView($moduleName);

        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,

        $listQuery .= " LIMIT $startIndex,".($pageLimit);
        $listResult = $db->pquery($listQuery, array());


        $index = 0;
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['jobalertsid'];
            $listViewRecordModels[$rawData['jobalertsid']] = $rawData;
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
                if($fields['fieldlabel']=='State'){   //20150720 adatian 已读未读状态不显示在列表，但需要 已读未读值
                    continue;
                }
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

        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('JobAlerts','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $listQuery .= ' and vtiger_jobalerts.creatorid in ('.implode(',',$where).')';
        }else{
           /* $where=getAccessibleUsers();
            if($where!='1=1'){
                $listQuery .= ' and vtiger_jobalerts.creatorid '.$where;

            }*/
        }
        return $listQuery;
    }
    public function getListViewCount() {
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
//print_r(debug_backtrace(0));
        //搜索条件
        //$this->getSearchWhere();

        $listQuery =  "SELECT
                        count(1) as counts
                        FROM
                            vtiger_jobalerts
                        LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_jobalerts.accountid
                        WHERE
                            1 = 1";
        $listQuery.=$this->getUserWhere();
        //检索条件获取
        $type = $_REQUEST['public'];
        $listQuery.= JobAlerts_Record_Model::getWhereCondition($type);
       // echo $listQuery;
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
}
