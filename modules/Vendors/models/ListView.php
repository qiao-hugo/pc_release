<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vendors_ListView_Model extends Vtiger_ListView_Model {

    public function getListViewEntries($pagingModel) {

        $db = PearDatabase::getInstance();

        $moduleName = 'Vendors';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
            $orderBy = 'vendorid';
            $sortOrder = 'DESC';
        }
        //$this->getSearchWhere();
        //wangbin 注释，改用自定义的列表sql 表头字段，总记录数，以及搜索字段，都需要更改。
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
        //echo $listQuery;die;
        $listResult = $db->pquery($listQuery, array());
        $listViewRecordModels = array();
        global $current_user;
        //3.在进行一次转化，目的何在
        $index = 0;
        $rechargeplatformData = Vendors_Record_Model::getRechargeplatform();

        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['vendorid'];
            //$rawData['deleted'] = ($rawData['smownerid']==$current_user->id&&in_array($rawData['modulestatus'],array('a_exception','a_normal')))?1:0;
            if ($rawData['vendortype'] == 'medium') {
                $rawData['mainplatform'] = $this->filterPlatform($rawData['mainplatform'], $rechargeplatformData);
            }
            $listViewRecordModels[$rawData['vendorid']] = $rawData;
        }
        return $listViewRecordModels;
    }

    public function filterPlatform($str, $rechargeplatformData) {
        $tt = explode(' # ', $str);
        $aa = array();
        foreach ($tt as $v) {
            $aa[] = $rechargeplatformData[$v];
        }
        return implode('，', $aa);
    }

    public function getUserWhere(){
        global $current_user;
        $searchDepartment = $_REQUEST['department'];
        $sourceModule = $this->get('src_module');
        $sourceRecord = $this->get('src_record');
        $listQuery=' ';
        if(!empty($_REQUEST['filter']) && $_REQUEST['filter']=='myvendors'){
            return ' and vtiger_crmentity.smownerid='.$current_user->id;
        }
        $query=" OR EXISTS(SELECT 1 FROM vtiger_sharevendors WHERE vtiger_sharevendors.sharestatus=1 AND vtiger_sharevendors.vendorsid=vtiger_vendor.vendorid AND vtiger_sharevendors.userid={$current_user->id})";
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('Vendors','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            $listQuery .= ' and (vtiger_crmentity.smownerid in ('.implode(','.$where).')'.$query.')';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                $listQuery .= ' and (vtiger_crmentity.smownerid '.$where.$query.')';
            }
        }
        $sourceModuleArray=array('SupplierContracts','ContractsAgreement','RefillApplication','ProductProvider','AccountPlatform');
        if(!empty($sourceModule) && in_array($sourceModule,$sourceModuleArray)){
            $listQuery .= ' and vtiger_vendor.vendorstate=\'al_approval\'';
        }
        /*$MediaProvider=array('ProductProvider','AccountPlatform');
        if(in_array($sourceModule,$MediaProvider)){
            //$listQuery .= ' and vtiger_vendor.vendorstate=\'al_approval\' and vtiger_vendor.vendortype=\'MediaProvider\' ';
        }*/
        if(!empty($sourceModule) && $sourceModule=='Vendors'){
            if($sourceRecord>0){
                $listQuery .= " and vtiger_vendor.vendorid!={$sourceRecord}";
            }
            $listQuery .= " and vtiger_vendor.vendorstate='al_approval'";
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
            //$this->_logs($list);
            foreach($list as $fields){
                $temp[$fields['fieldlabel']]=$fields;
            }
            return $temp;
        }
        return $queryGenerator->getFocus()->list_fields_name;
    }
    public function getListViewCount() {
        // 原来的记录数计算，不敢删掉
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');

        $where=$this->getUserWhere();

        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
        //echo $listQuery;die();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
}
?>