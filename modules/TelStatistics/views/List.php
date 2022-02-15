<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class TelStatistics_List_View extends Vtiger_KList_View {

    public function process (Vtiger_Request $request) {

        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
        $this->viewName = $request->get('viewname');
        $viewer->assign('VIEWNAME', $this->viewName);
        if (in_array($request->get('public'), array('eworkstatistics', 'eworksituationtrends'))) {
            global $current_user;
//            $department = TelStatistics_Module_Model::getDepartmentByUser($current_user->id);
            include "crmcache/departmentanduserinfo.php";
            $where = getAccessibleUsers('Accounts', 'List', true);
            if ($where != '1=1') {
                $where1=getAccessibleUsers('TelStatistics','List',true);
                if($where1!='1=1'){
                    $where=array_merge($where,$where1);
                    $str =' AND id in('.implode(',',$where).')';
            } else {
                $str = '';
            }

            }else{
                $str='';
            }
            $viewer->assign('MODEFROM', $request->get('public'));
            $viewer->assign('DEPARTMENTUSER', $departlevel);
//            if ($department) {
//                $viewer->assign('DEPARTMENTUSER', array($department=>$departlevel[$department]));
//                $userids = getDepartmentUser($department);
//                $userids = implode(',', $userids);
//                $str .= " AND  id in({$userids})";
//            }
            $viewer->assign('USERDEPARTMENT', $moduleModel->getuserinfo($str));
            $viewer->view('ListExpand.tpl', $moduleName);
            return;
        }

        if ($request->get('public') == 'checkpermissionset') {
            $viewer->assign('ROLES', getRoles());
            $viewer->assign('RECOEDS', TelStatistics_Record_Model::getReportPermissions());
            $viewer->assign('ModuleName', TelStatistics_Record_Model::getModulePicklist());
            $viewer->assign('CLASSNAME', TelStatistics_Record_Model::getSetPermissions());
            $classnamedata = json_decode(TelStatistics_Record_Model::getSetPermissions(),true);
            $viewer->assign('INIT_TELSTATISTICS',$classnamedata['TelStatistics']);
            $viewer->view('checkpermissionset.tpl', $moduleName);
            return;
        }
        if ($request->get('public') == 'indicatorsetting') {
            $viewer->view('indicatorsetting.tpl', $moduleName);
            return;
        }

        if ($request->isAjax()) {
            $this->initializeListViewContents($request, $viewer);//竟然调用两次，这边其实是ajax调用的，哈哈！！
            $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        }

        $viewer->assign('VIEW', $request->get('view'));
        $viewer->assign('MODULE_MODEL', $moduleModel);


        $viewer->view('ListViewContents.tpl', $moduleName);

    }
    public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer) {
        $moduleName = $request->getModule();
        $cvId = 0;

        $pageNumber = $request->get('page');//页数
        $orderBy = $request->get('orderby');//排序
        $sortOrder = $request->get('sortorder');//排序
        $pageLimit = $request->get('limit');//排序
        if($sortOrder == "ASC"){
            $nextSortOrder = "DESC";
            $sortImage = "icon-chevron-down";
        }else{
            $nextSortOrder = "ASC";
            $sortImage = "icon-chevron-up";
        }

        if(empty ($pageNumber)){
            $pageNumber = '1';
        }
        //20150416 young 每页显示数量
        if(empty ($pageLimit)){
            $pageLimit = '20';
        }
        $listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $cvId);//初始化各种数据,在这里其实初始化的是module_listview_model类，次类又同时将QueryGenerator,CustomView包含了

        $linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'), 'CVID'=>$cvId);

        $pagingModel = new Vtiger_Paging_Model();   //分页
        $pagingModel->set('page', $pageNumber);
        $pagingModel->set('limit', $pageLimit);//20150416 young 每页显示数量
        if(!empty($orderBy)) {
            $listViewModel->set('orderby', $orderBy);
            $listViewModel->set('sortorder',$sortOrder);
        }
        $searchKey = $request->get('search_key');
        $searchValue = $request->get('search_value');
        $operator = $request->get('operator');
        if(!empty($operator)) {
            $listViewModel->set('operator', $operator);
            $viewer->assign('OPERATOR',$operator);
            $viewer->assign('ALPHABET_VALUE',$searchValue);
        }
        if(!empty($searchKey) && !empty($searchValue)) {
            $listViewModel->set('search_key', $searchKey);
            $listViewModel->set('search_value', $searchValue);
        }
        if(!$this->listViewHeaders && !in_array($request->get('public'),array('eworkstatistics','eworksituationtrends'))){
            $this->listViewHeaders = $listViewModel->getListViewHeaders();
        }

        if(!$this->listViewEntries && !in_array($request->get('public'),array('eworkstatistics','eworksituationtrends'))){
            $this->listViewEntries = $listViewModel->getListViewEntries($pagingModel);
        }


        $noOfEntries = $listViewModel->getListViewCount();

        $num_acc=0;
        if($moduleName=='MaintainerAccount'){
            $num_acc = $listViewModel->getDistinctAcc();
            if(!$num_acc){
                $num_acc = 0;
            }
        }
        $viewer->assign('MODULE', $moduleName);

        if(!$this->listViewLinks){
            $this->listViewLinks = $listViewModel->getListViewLinks($linkParams);
        }
        $viewer->assign('LISTVIEW_LINKS', $this->listViewLinks);

        // $viewer->assign('LISTVIEW_MASSACTIONS', $linkModels['LISTVIEWMASSACTION']);

        $viewer->assign('PAGING_MODEL', $pagingModel);
        $viewer->assign('PAGE_NUMBER',$pageNumber);

        $viewer->assign('ORDER_BY',$orderBy);
        $viewer->assign('SORT_ORDER',$sortOrder);
        $viewer->assign('NEXT_SORT_ORDER',$nextSortOrder);
        $viewer->assign('SORT_IMAGE',$sortImage);
        $viewer->assign('COLUMN_NAME',$orderBy);

        $viewer->assign('LISTVIEW_ENTIRES_COUNT',$noOfEntries);
        $viewer->assign('NUM_ACC',$num_acc);
        //插入字段信息
        //20150428 young 将模板的字段验证转移到后台验证，便于控制
        $LISTVIEW_FIELDS = $listViewModel->getSelectFields();
        $listViewHeaders = $this->listViewHeaders;
        $temp = array();
        if(!empty($LISTVIEW_FIELDS)){
            foreach($LISTVIEW_FIELDS as $key=>$val){
                if(isset($listViewHeaders[$key])){
                    $temp[$key]=$listViewHeaders[$key];
                }
            }
        }
        if(empty($temp)){
            $temp = $listViewHeaders;
        }

        $viewer->assign('LISTVIEW_HEADERS', $temp);
        //$viewer->assign('LISTVIEW_FIELDS', $listViewModel->getSelectFields());
        $viewer->assign('LISTVIEW_ENTRIES', $this->listViewEntries);
        //end



        $pageLimit = $pagingModel->getPageLimit();
        $pageCount = ceil((int) $noOfEntries / (int) $pageLimit);

        if($pageCount == 0){
            $pageCount = 1;
        }
        $viewer->assign('PAGE_COUNT', $pageCount);
        $viewer->assign('PAGE_CU', $pageNumber);
        $viewer->assign('LISTVIEW_COUNT', $noOfEntries);
        $viewer->assign('IS_MODULE_EDITABLE', $listViewModel->getModule()->isPermitted('EditView',1));
        $viewer->assign('IS_MODULE_DELETABLE', $listViewModel->getModule()->isPermitted('Delete'));


    }
}