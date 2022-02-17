<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ServiceComments_Detail_View extends Vtiger_Detail_View {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('showRecentComments');
        $this->exposeMethod('showreturnplainlist');
    }
    function showRecentComments(Vtiger_Request $request) {
        $parentId = $request->get('record');
        $pageNumber =(int)$request->get('page');
        $limit = $request->get('limit');
        $moduleName = $request->getModule();
        if(empty($pageNumber)){
            $pageNumber = 1;
        }

        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        if(!empty($limit)) {
            $pagingModel->set('limit', $limit);
        }
        $recentComments = ModComments_Record_Model::getRecentComments($parentId, $pagingModel,$moduleName);
        $pagingModel->calculatePageRange($recentComments);
        $currentUserModel = Users_Record_Model::getCurrentUserModel();

        //获取客户id
        $accountid="";
        if ($moduleName !="Accounts"){
            if(!$this->record){
                $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $parentId);
            }
            $recordModel = $this->record->getRecord();
            $accountid=$recordModel->get('related_to');
        }else{
            $accountid=$parentId;
        }

        $query ="SELECT * FROM `vtiger_servicecomments_returnplan` WHERE commentsid=? AND SYSDATE() BETWEEN uppertime AND lowertime limit 1";
        $adb = PearDatabase::getInstance();
        $result = $adb->pquery($query,array($parentId));
        $return_data = array();
        if($adb->num_rows($result)>0){
            while($row = $adb->fetchByAssoc($result)){
                $return_data[] = $row;
            }
        }
       // echo html_entity_decode($return_data[0]['reviewcontent'], ENT_QUOTES); // 转换双引号和单引号
        //echo $return_data[0]['reviewcontent'];die;
        $viewer = $this->getViewer($request);
        //wangbin 2016-5-19

        $recentActivities = Accounts_Record_Model::getservicecomments($accountid, $pagingModel);
        $recentActivitiesandsmowner = Accounts_Record_Model::getservicecommentsandsmower($accountid, $pagingModel);
        $RECENT_HEADS = Accounts_Record_Model::getheads($accountid, $pagingModel);
        $pagingModel->calculatePageRange($recentActivities);
        /////将客户表的联系人拿出来
        $recordModel = Vtiger_Record_Model::getInstanceById($accountid, 'Accounts');
        $entity=$recordModel->entity->column_fields;
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_SUMMARY);
        ///////读客户对应联系人表里的联系人信息
        $allcontacts = Accounts_Record_Model::getContactsToIndex($accountid);
        $viewer->assign('ENTITY_FIRST',$entity);
        $viewer->assign('ALLCONTACTS',$allcontacts);
        $viewer->assign('RECENT_ACTIVITIES', $recentActivities);
        $viewer->assign('RECENT_ACTIVITIESAND', $recentActivitiesandsmowner);
        $viewer->assign('SUMMARY_RECORD_STRUCTURE', $recordStrucure->getStructure());
        $viewer->assign('RECENT_HEADS', $RECENT_HEADS);
        //end


        $viewer->assign('COMMENTS', $recentComments);
        $viewer->assign('ACCOUNTID', $accountid);
        $viewer->assign('COMMENTSMODE', ModComments_Record_Model::getModcommentmode());
        $viewer->assign('COMMENTSTYPE',ModComments_Record_Model::getModcommenttype());
        $viewer->assign('MODCOMMENTCONTACTS',ModComments_Record_Model::getModcommentContacts($accountid));
        $viewer->assign('CURRENTUSER', $currentUserModel);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('PAGING_MODEL', $pagingModel);
        $viewer->assign('CURRENTPLAN', $return_data);
        return $viewer->view('RecentComments.tpl', $moduleName, 'true');
    }

    public function showreturnplainlist(Vtiger_Request $request){
        $date = strtotime(date("Y-m-d",time()));
        $record=$request->get('record');
        $moduleName = $request->getModule();
        $query ="SELECT * FROM `vtiger_servicecomments_returnplan` WHERE commentsid=? ORDER BY sort";
        $adb = PearDatabase::getInstance();
        $result = $adb->pquery($query,array($record));
        $return_data = array();
        if($adb->num_rows($result)>0){
            while($row = $adb->fetchByAssoc($result)){
                if($date<strtotime($row['uppertime'])) {
                    $row['status']='未开始';
                }elseif($date>=strtotime($row['uppertime'] )&& $date<strtotime($row['lowertime'])){
                     $row['status']='进行中';
                }else{
                    if($row['isfollow']=='1'){
                        $row['status']='已完成';
                    }else{
                        $row['status']='已超期';
                    }
                }
                $return_data[] = $row;
            }
        }
        $viewer = $this->getViewer($request);
        $viewer->assign('RETURNPLAN',$return_data);
        return $viewer->view('ReturnPlanlist.tpl', $moduleName, 'true');
    }
}
	