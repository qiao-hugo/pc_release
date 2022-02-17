<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Item_Detail_View extends Vtiger_Detail_View {

    function __construct(){
        parent::__construct();
    }
    function showModuleBasicView($request) {
        return $this->showModuleDetailView($request);
    }
    /**
     * 跟进信息
     */
    function showRecentComments(Vtiger_Request $request) {
        $parentId = $request->get('record');
        $pageNumber =(int)$request->get('page');
        $limit = $request->get('limit');
        $record = $request->get('record');
        $moduleName = $request->getModule();
        if(empty($pageNumber)){
            $pageNumber = 1;
        }

        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        if(!empty($limit)) {
            $pagingModel->set('limit', $limit);
        }

        //$recentComments = ModComments_Record_Model::getRecentComments($parentId, $pagingModel, $moduleName);

        //$pagingModel->calculatePageRange($recentComments);
        //$currentUserModel = Users_Record_Model::getCurrentUserModel();
        $recentComments = Schoolcomments_Record_Model::getRecentComments($parentId, $pagingModel, $moduleName);

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

        $viewer = $this->getViewer($request);
        $viewer->assign('TCOMMENTS', $recentComments);
        $viewer->assign('ACCOUNTID', $accountid);
        //$viewer->assign('COMMENTSMODE', ModComments_Record_Model::getModcommentmode());
        //$viewer->assign('COMMENTSTYPE',ModComments_Record_Model::getModcommenttype());
        $viewer->assign('COMMENTS', $this->getRecentComments($record));
        $viewer->assign('CURRENTUSER', $currentUserModel);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('PAGING_MODEL', $pagingModel);
        $viewer->assign('RECORDID', $parentId);

        return $viewer->view('RecentComments.tpl', $moduleName, 'true');
    }


    function showModuleDetailView(Vtiger_Request $request) {
        $recordId = $request->get('record');

        $moduleName = $request->getModule();
        //young.yang 2014-12-26 工作流
        global $isallow;
        if(in_array($moduleName, $isallow)){
            echo $this->getWorkflowsM($request);
        }
        //end
        if(!$this->record){
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }

        $recordModel = $this->record->getRecord();
//        dd($recordModel);
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        //var_dump($recordStrucure);die;
        $structuredValues = $recordStrucure->getStructure();
        $moduleModel = $recordModel->getModule();

        //项目分类
        $adb =PearDatabase::getInstance();
        $sql = "select (select parentcate from vtiger_parentcate where vtiger_parentcate.parentcateid = vtiger_soncate.parentcate) as parentcate,soncate,if(special=1,'是','否') as special FROM vtiger_soncate where soncateid=?";
        $sel_result = $adb->pquery($sql, array($recordId));
        $rawData = $adb->query_result_rowdata($sel_result, 0);
        $parentcate = $rawData['parentcate'];
        $soncate = $rawData['soncate'];
        $special = $rawData['special'];

        $viewer = $this->getViewer($request);
        $viewer->assign('PARENTCATE', $parentcate);
        $viewer->assign('SONCATE', $soncate);
        $viewer->assign('SPECIAL', $special);

        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));

        // 上面的都是在 vtiger_detail_view 的 showModuleDetailView方法copy的
        // 取出增值模块的详细信息
        $viewer->assign('COMMENTS', $this->getRecentComments($recordId));
                $viewer->assign('SONCATE', $recordModel->get("soncate"));
        $viewer->assign('SONCATEID',$recordId );
//        $viewer->assign('PARENTCATE',$recordModel->get("parentcate") );

        $viewer->assign('SONCATEWORKFLOWS', $recordModel->getSonCateWorkFlows($recordId));
        //$viewer->assign('MOREINVOICES', Invoice_Record_Model::getMoreinvoice($recordId));
        //$viewer->assign('MOREINVOICES', array('a'=>'1', 'b'=>'2'));
//        echo "<pre>";
//        var_dump($recordModel->getSonCateWorkFlows($recordId));die;
        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }

    public function getRecentComments($parentRecordId){
        $db = PearDatabase::getInstance();

        $query = "SELECT vtiger_channelcomment.commentid,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id = vtiger_channelcomment.smownerid) AS creatorid,hasaccess,vtiger_channelcomment.fllowdate,vtiger_channelcomment.nextdate,vtiger_channelcomment.currentprogess,vtiger_channelcomment.nextwork,vtiger_channelcomment.policeindicator,vtiger_channelcomment.createdtime FROM vtiger_channelcomment WHERE vtiger_channelcomment.channelid =? ORDER BY vtiger_channelcomment.commentid DESC";
        $result = $db->pquery($query, array($parentRecordId));
        $rows = $db->num_rows($result);
        $data = array();
        for ($i=0; $i<$rows; $i++) {
            $row = $db->query_result_rowdata($result, $i);
            $data[] = $row;
        }
        return $data;
    }
}
