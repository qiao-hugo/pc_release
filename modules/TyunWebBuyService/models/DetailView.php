<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class TyunWebBuyService_DetailView_Model extends Vtiger_DetailView_Model {
	/**
	 * Function to get the detail view widgets
	 * @return <Array> - List of widgets , where each widget is an Vtiger_Link_Model
	 */
	public function getWidgets() {
		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		//$widgetLinks = parent::getWidgets();
		$widgets = array();
		$widgetLinks=array();

		$moduleModel = $this->getModule();
		$modCommentsModel = Vtiger_Module_Model::getInstance('ModComments');
		
		//if($moduleModel->isCommentEnabled() && $modCommentsModel->isPermitted('EditView')) {
			$widgets[] = array(
					'linktype' => 'DETAILVIEWWIDGET',
					'linklabel' => 'ModComments',
					'linkurl' => 'module='.$this->getModuleName().'&view=Detail&record='.$this->getRecord()->getId().
					'&mode=showRecentComments&page=1&limit=5'
			);
		//}
			
		foreach ($widgets as $widgetDetails) {
			$widgetLinks[] = Vtiger_Link_Model::getInstanceFromValues($widgetDetails);
		}
		//print_r($widgetLinks);die();
		return $widgetLinks;
	}
	/**
     * Function to get the detail view related links
     * @return <array> - list of links parameters
     */
    public function getDetailViewRelatedLinks() {
        $recordModel = $this->getRecord();
        $moduleName = $recordModel->getModuleName();
        $parentModuleModel = $this->getModule();
        $relatedLinks = array();

        if($parentModuleModel->isSummaryViewSupported()) {
            $relatedLinks = array(array(
                'linktype' => 'DETAILVIEWTAB',
                'linklabel' => vtranslate('SINGLE_' . $moduleName, $moduleName) . ' ' . vtranslate('LBL_SUMMARY', $moduleName),
                'linkKey' => 'LBL_RECORD_SUMMARY',
                'linkurl' => $recordModel->getDetailViewUrl() . '&mode=showDetailViewByMode&requestMode=summary',
                'linkicon' => ''
            ));
        }
        //link which shows the summary information(generally detail of record)
        $relatedLinks[] = array(
            'linktype' => 'DETAILVIEWTAB',
            'linklabel' => vtranslate('SINGLE_'.$moduleName, $moduleName).' '. vtranslate('LBL_DETAILS', $moduleName),
            'linkurl' => $recordModel->getDetailViewUrl().'&mode=showDetailViewByMode&requestMode=full',
            'linkicon' => ''
        );
        /*
                if($parentModuleModel->isCommentEnabled()) {
                    $relatedLinks[] = array(
                        'linktype' => 'DETAILVIEWTAB',
                        'linklabel' => 'ModComments',
                        'linkurl' => $recordModel->getDetailViewUrl().'&mode=showRecentComments',
                        'linkicon' => ''
                    );
                }
        */
        if($parentModuleModel->isTrackingEnabled()) {
            $relatedLinks[] = array(
                'linktype' => 'DETAILVIEWTAB',
                'linklabel' => 'LBL_UPDATES_FILED',
                'linkurl' => $recordModel->getDetailViewUrl().'&mode=showRecentActivities&page=1',
                'linkicon' => ''
            );
        }
        return $relatedLinks;

    }
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

        $detailViewLink = array();


        global $current_user;
        $isShowCreateServiceContract  = TyunWebBuyService_Record_Model::isShowCreateServiceContract($recordId,$current_user->id);
        if($isShowCreateServiceContract){
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'manuallycreatecontracts',
                'linkurl' =>'',
                'linkicon' => ''
            );
        }

        //合同状态已发放  电子合同状态待发送 且是创建人登录可操作
        global $current_user;
        if($recordModel->entity->column_fields['modulestatus']=='已发放' && $recordModel->entity->column_fields['eleccontractstatus']=='a_elec_sending' &&
            $current_user->id ==$recordModel->entity->column_fields['signid']){
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'manuallysendcontract',
                'linkurl' =>'',
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