<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class IdcRecords_List_View extends Vtiger_KList_View {
    protected $listViewEntries = false;
    protected $listViewCount = false;
    protected $listViewLinks = false;
    protected $listViewHeaders = false;
        function __construct() {
            parent::__construct();
        }



        function process(Vtiger_Request $request){
            $viewer = $this->getViewer ($request);
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据



            $this->viewName = $request->get('viewname');

            if(empty($this->viewName)){
                //If not view name exits then get it from custom view
                //This can return default view id or view id present in session
                $customView = new CustomView();
                $this->viewName = $customView->getViewId($moduleName);
            }
            $this->initializeListViewContents($request, $viewer);//竟然调用两次，

            //读取状态
            //begin
            $db = PearDatabase::getInstance();
            $querysql = "SELECT idcstate FROM `vtiger_idcstate`";
            $result = $db->pquery($querysql,array());
            if ($db->num_rows($result)>0){
                for ($i=0; $i<$db->num_rows($result); $i++) {
                    $idcstateArr[] = $db->fetchByAssoc($result);
                }
            }
            $viewer->assign('IDCSTATEARR', $idcstateArr);
            //end
            //对字段进行相应权限控制
            $cvId = $this->viewName;
            $listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $cvId);
            $LISTVIEW_FIELDS = $listViewModel->getSelectFields();
            $viewer->assign('IS_VIEWS_LISTBTNADD', $listViewModel->getModule()->isPermitted('IdcEDIT'));

            $viewer->assign('VIEW', $request->get('view'));
            $viewer->assign('MODULE_MODEL', $moduleModel);
            $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());


            $cv=new CustomView_EditAjax_View;
            $vr=new Vtiger_Request;
            $vr->set('source_module',$moduleName);
            $vr->set('module','CustomView');
            $vr->set('view','EditAjax');
            $vr->set('record',$request->get('viewname'));
            $cv->getSearch($vr,$viewer);

            $viewer->view('ListViewContents.tpl', $moduleName);



        }
}