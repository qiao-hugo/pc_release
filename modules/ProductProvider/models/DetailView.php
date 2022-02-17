<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ProductProvider_DetailView_Model extends Inventory_DetailView_Model {
    /**
     * 详细页面加上受控制的连接
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

        $detailViewLink = array();

        if(Users_Privileges_Model::isPermitted($moduleName, 'EditView')) {
            $detailViewLinks[] = array(
                'linktype' => 'LISTVIEWBASIC',
                'linklabel' => 'LBL_ADD_RECORD',
                'linkurl' => $moduleModel->getCreateRecordUrl(),
                'linkicon' => ''
            );
        }
        if(Users_Privileges_Model::isPermitted($moduleName, 'EditView', $recordId)) {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_EDIT',
                'linkurl' => $recordModel->getEditViewUrl(),
                'linkicon' => ''
            );
        }
        $detailViewLinks[] = array(
            'linktype' => 'LISTVIEWBASIC',
            'linklabel' => 'LBL_CHANGE_STATUS',
            'linkurl' => '',
            'linkicon' => ''
        );
        global $current_user;
        /**
         * 修改审请
         */
        if($recordModel->entity->column_fields['modulestatus']=='c_complete') {
            $accountidflag=false;
            if($recordModel->get('accountid')>0) {
                try {
                    $accountRecordModel = Vtiger_Record_Model::getInstanceById($recordModel->get('accountid'), "Accounts");
                    $accountidflag = $accountRecordModel->get('assigned_user_id') == $current_user->id ? true : false;
                }catch(Exception $e){

                }
            }
            if ($recordModel->entity->column_fields['assigned_user_id'] == $current_user->id || $current_user->is_admin == 'on' || $accountidflag || $recordModel->personalAuthority('AccountPlatform',"doedit")) {
                $detailViewLinks[] = array(
                    'linktype' => 'DETAILVIEWBASIC',
                    'linklabel' => 'LBL_UPDATERECEIVED',
                    'linkurl' => '',
                    'linkicon' => ''
                );
            }
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
