<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ServiceReturnList_ListView_Model extends Vtiger_ListView_Model {
    //去除添加按钮
    public function getListViewLinks($linkParams) {
        return $links;
        exit;
    }
	//根据参数显示数据
	public function getListViewEntries($pagingModel) {
        $db = PearDatabase::getInstance();
        $moduleName = 'ServiceReturnList';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');
        if(empty($orderBy) && empty($sortOrder)){
            $orderBy = 'vtiger_servicecomments_returnplan.commentsid, vtiger_servicecomments_returnplan.sort';
        }
        $this->getSearchWhere();
        $listQuery = $this->getQuery();
        $listQuery.=$this->getUserWhere();
        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();
        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
        $viewid = ListViewSession::getCurrentView($moduleName);
        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
        $listQuery .= " LIMIT $startIndex,".($pageLimit);
        //echo $listQuery;die;
        $listResult = $db->pquery($listQuery, array());
        $listViewRecordModels = array();
        global $current_user;
        $index = 0;
        $date = strtotime(date("Y-m-d",time()));
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['commentreturnplanid'];
            $rawData['deleted'] = ($current_user->is_admin=='on')?1:0;
            if($date<strtotime($rawData['uppertime'])) {
                $rawData['isfollow']='未开始';
            }elseif($date>=strtotime($rawData['uppertime'] )&& $date<strtotime($rawData['lowertime'])){
                $rawData['isfollow']='进行中';
            }else{
                if($rawData['isfollow']=='是'){
                    $rawData['isfollow']='已完成';
                }else{
                    $rawData['isfollow']='已超期';
                }
            }

            $listViewRecordModels[$rawData['commentreturnplanid']] = $rawData;
        }
        return $listViewRecordModels;
	}
    public function getUserWhere(){
        $where = getAccessibleUsers('ServiceContracts','List',false);
        $query="";
        if($where!=='1=1'){
            $query.=" AND vtiger_servicecomments.serviceid ".$where;
        }
        return $query;
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
    public function getListViewCount() {
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
        $where=$this->getUserWhere();
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
}