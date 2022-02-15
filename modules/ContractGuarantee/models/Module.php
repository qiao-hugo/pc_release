<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class ContractGuarantee_Module_Model extends Vtiger_Module_Model {

    /**
     * Function to get the Quick Links for the module
     * @param <Array> $linkParams
     * @return <Array> List of Vtiger_Link_Model instances
     */
    public function getSideBarLinks($linkParams) {
        $linkTypes = array('SIDEBARLINK', 'SIDEBARWIDGET');
        $links = Vtiger_Link_Model::getAllByType($this->getId(), $linkTypes, $linkParams);

        $quickLinks = array(
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'LBL_RECORDS_LIST',
                'linkurl' => $this->getListViewUrl(),
                'linkicon' => '',
            ),
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '导出担保合同',
                'linkurl' => $this->getListViewUrl().'&public=ExportRI',
                'linkicon' => '',
            )
        );


        foreach($quickLinks as $quickLink) {
            $links['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        }

        $quickWidgets = array(
            array(
                'linktype' => 'SIDEBARWIDGET',
                'linklabel' => 'LBL_RECENTLY_MODIFIED',
                'linkurl' => 'module='.$this->get('name').'&view=IndexAjax&mode=showActiveRecords',
                'linkicon' => ''
            ),
        );
        foreach($quickWidgets as $quickWidget) {
            $links['SIDEBARWIDGET'][] = Vtiger_Link_Model::getInstanceFromValues($quickWidget);
        }

        return $links;
    }
}
