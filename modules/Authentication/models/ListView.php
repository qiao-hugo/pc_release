<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
//error_reporting(-1);
//ini_set("display_errors",1);
class Authentication_ListView_Model extends Vtiger_ListView_Model {


    //根据参数显示数据
    public function getListViewEntries($pagingModel) {
        $db = PearDatabase::getInstance();
        $moduleName ='Authentication';


        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        //List view will be displayed on recently created/modified records
        //列表视图将显示最近的创建修改记录  ---做什么用处
        if(empty($orderBy) && empty($sortOrder)){

            $orderBy = 'vtiger_authentication.createdtime';
            $sortOrder = 'DESC';
        }

        $this->getSearchWhere();
//        $listQuery = $this->getQuery();
        $listQuery="select * from vtiger_authentication WHERE 1=1";
        $listQuery.=$this->getUserWhere();
        global $current_user;


        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();

        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;

        $viewid = ListViewSession::getCurrentView($moduleName);

        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,

        $listQuery .= " LIMIT $startIndex,".($pageLimit);

//        echo $listQuery;
//        die();

        $listResult = $db->pquery($listQuery, array());

        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['authenticationid'];

            $listViewRecordModels[$rawData['authenticationid']] = $rawData;
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
                $temp[$fields['fieldlabel']]=$fields;
            }
            return $temp;
        }
        return $queryGenerator->getFocus()->list_fields_name;

    }
    public function getUserWhere(){
        $bugFreeQueryArray=json_decode($_REQUEST['BugFreeQuery'],true);
        $searchType=$bugFreeQueryArray['BugFreeQuery[field0]'];
        $searchValue=$bugFreeQueryArray['BugFreeQuery[value0]'];
        $listQuery=" and isdelete=0";
        if($searchType&&$searchValue){
            $listQuery.=" and $searchType like '%".$searchValue."%'";
        }
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