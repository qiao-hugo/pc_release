<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class UserManger_DetailView_Model extends Vtiger_DetailView_Model {
    /**
     * ��ϸҳ������ܿ��Ƶ�����
     * @param <array> $linkParams - parameters which will be used to calicaulate the params
     * @return <array> - array of link models in the format as below
     *                   array('linktype'=>list of link models);
     */
    public function getDetailViewLinks($linkParams) {
        $linkTypes = array('DETAILVIEWBASIC','DETAILVIEW');
        $moduleModel = $this->getModule();
        $recordModel = $this->getRecord();

        $moduleName = $moduleModel->getName();
        $recordId = $recordModel->getId();

        $ownornot = UserManger_Record_Model::getUsermangerInfo($recordId);
        $detailViewLink = array();
        if(Users_Privileges_Model::isPermitted($moduleName, 'EditView')) {
            $detailViewLinks[] = array(
                'linktype' => 'LISTVIEWBASIC',
                'linklabel' => 'LBL_ADD_RECORD',
                'linkurl' => $moduleModel->getCreateRecordUrl(),
                'linkicon' => ''
            );
        }
        if(Users_Privileges_Model::isPermitted($moduleName, 'EditView', $recordId) && $ownornot==1) {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_EDIT',
                'linkurl' => $recordModel->getEditViewUrl(),
                'linkicon' => ''
            );
        }
        if($recordModel->get('modulestatus')=='c_complete' && Users_Privileges_Model::isPermitted($moduleName, 'EditView', $recordId)) {
            /*if($ownornot==1){
                $detailViewLinks[] = array(
                    'linktype' => 'DETAILVIEWBASIC',
                    'linklabel' => 'LBL_CHANGEPASSWORD',
                    'linkurl' => '',
                    'linkicon' => ''
                );
            }*/
            if($recordModel->get('status')=='Active' && $recordModel->get('isdimission')==0){
                $detailViewLinks[] = array(
                    'linktype' => 'DETAILVIEWBASIC',
                    'linklabel' => 'LBL_UPDATEWEXIN',
                    'linkurl' => '',
                    'linkicon' => ''
                );
                //云课堂
                /*$detailViewLinks[] = array(
                    'linktype' => 'DETAILVIEWBASIC',
                    'linklabel' => 'LBL_UPDATEYXT',
                    'linkurl' => '',
                    'linkicon' => ''
                );*/
            }

        }
        if($recordModel->get('modulestatus')=='c_complete' && Users_Privileges_Model::isPermitted($moduleName, 'EditView', $recordId) && $ownornot==1){
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_USERSTATUS',
                'linkurl' => '',
                'linkicon' => ''
            );
        }

        if(!empty($detailViewLinks)){
            foreach ($detailViewLinks as $detailViewLink) {
                $linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLink);
            }
        }



        $relatedLinks = $this->getDetailViewRelatedLinks();
        foreach($relatedLinks as $relatedLinkEntry) {
            $relatedLink = Vtiger_Link_Model::getInstanceFromValues($relatedLinkEntry);
            $linkModelList[$relatedLink->getType()][] = $relatedLink;
        }

        $linkModelList['DETAILVIEWRELATED']=$moduleModel->makeRelatedurl($recordId);

        $widgets = $this->getWidgets();
        foreach($widgets as $widgetLinkModel) {
            $linkModelList['DETAILVIEWWIDGET'][] = $widgetLinkModel;
        }

        return $linkModelList;
    }
}
