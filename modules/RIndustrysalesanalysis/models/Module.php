<?php

class RIndustrysalesanalysis_Module_Model extends Vtiger_Module_Model{
    
	 public function getSideBarLinks($linkParams) {
        $parentQuickLinks = array();
        $quickLink = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '销售报表',
            'linkurl' => $this->getListViewUrl(),
            'linkicon' => '',
        );
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);


         $quickLink2 = array(
             'linktype' => 'SIDEBARLINK',
             'linklabel' => '本周报表',
             'linkurl' => $this->getListViewUrl() . '&public=thisweek',
             'linkicon' => '',
         );
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         $quickLink2 = array(
             'linktype' => 'SIDEBARLINK',
             'linklabel' => '本月报表',
             'linkurl' => $this->getListViewUrl() . '&public=thismonth',
             'linkicon' => '',
         );
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
		return $parentQuickLinks;
	}
}
?>
