<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ContractReceivable_DetailView_Model extends Vtiger_DetailView_Model {

	protected $module = false;
	protected $record = false;
	
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

            $relatedLinks[] = array(
                'linktype' => 'DETAILVIEWTAB',
                'linklabel' => 'ModComments',
                'linkurl' => $recordModel->getDetailViewUrl().'&mode=showRecentComments',
                'linkicon' => ''
            );

		if(!empty($detailViewLinks)){
			foreach ($detailViewLinks as $detailViewLink) {
				$linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLink);
			}
		}

		//$linkModelListDetails = Vtiger_Link_Model::getAllByType($moduleModel->getId(),$linkTypes,$linkParams);
		//Mark all detail view basic links as detail view links.
		//Since ui will be look ugly if you need many basic links
		//$detailViewBasiclinks = $linkModelListDetails['DETAILVIEWBASIC'];
		//unset($linkModelListDetails['DETAILVIEWBASIC']);
		//删除去掉 gaocl 2015/01/30
// 		if(Users_Privileges_Model::isPermitted($moduleName, 'Delete', $recordId)) {
// 			$deletelinkModel = array(
// 					'linktype' => 'DETAILVIEW',
// 					'linklabel' => sprintf("%s %s", getTranslatedString('LBL_DELETE', $moduleName), vtranslate('SINGLE_'. $moduleName, $moduleName)),
// 					'linkurl' => 'javascript:Vtiger_Detail_Js.deleteRecord("'.$recordModel->getDeleteUrl().'")',
// 					'linkicon' => ''
// 			);
// 			$linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($deletelinkModel);
// 		}

		//复制去掉 gaocl 2015/01/30
// 		if(Users_Privileges_Model::isPermitted($moduleName, 'EditView', $recordId)) {
// 			$duplicateLinkModel = array(
// 						'linktype' => 'DETAILVIEWBASIC',
// 						'linklabel' => 'LBL_DUPLICATE',
// 						'linkurl' => $recordModel->getDuplicateRecordUrl(),
// 						'linkicon' => ''
// 				);
// 			$linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($duplicateLinkModel);
// 		}

	/* 	if(!empty($detailViewBasiclinks)) {
			foreach($detailViewBasiclinks as $linkModel) {
				// Remove view history, needed in vtiger5 to see history but not in vtiger6
				if($linkModel->linklabel == 'View History') {
					continue;
				}
				$linkModelList['DETAILVIEW'][] = $linkModel;
			}
		} */

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
		//前台屏蔽设置链接
		/* $currentUserModel = Users_Record_Model::getCurrentUserModel();
		if($currentUserModel->isAdminUser()) {
			$settingsLinks = $moduleModel->getSettingLinks();
			foreach($settingsLinks as $settingsLink) {
				$linkModelList['DETAILVIEWSETTING'][] = Vtiger_Link_Model::getInstanceFromValues($settingsLink);
			}
		} */
		return $linkModelList;
	}
	
	
}
