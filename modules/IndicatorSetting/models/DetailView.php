<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class IndicatorSetting_DetailView_Model extends Vtiger_DetailView_Model
{

    /**
     * Function to get the detail view related links
     * @return <array> - list of links parameters
     */
    public function getDetailViewRelatedLinks()
    {
        $recordModel = $this->getRecord();
        $moduleName = $recordModel->getModuleName();
        $parentModuleModel = $this->getModule();
        $relatedLinks = array();

        if ($parentModuleModel->isSummaryViewSupported()) {
            $relatedLinks = array(array(
                'linktype' => 'DETAILVIEWTAB',
                'linklabel' => vtranslate('SINGLE_' . $moduleName, $moduleName) . ' ' . vtranslate('LBL_SUMMARY', $moduleName),
                'linkKey' => 'LBL_RECORD_SUMMARY',
                'linkurl' => $recordModel->getDetailViewUrl() . '&mode=showDetailViewByMode&requestMode=summary',
                'linkicon' => ''
            ));
        }

        $relatedLinks[] = array(
            'linktype' => 'DETAILVIEWTAB',
            'linklabel' => 'LBL_UPDATES',
            'linkurl' => $recordModel->getDetailViewUrl() . '&mode=showRecentActivities&page=1',
            'linkicon' => ''
        );
        return $relatedLinks;

    }
}
