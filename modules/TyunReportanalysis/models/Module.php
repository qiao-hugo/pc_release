<?php

class TyunReportanalysis_Module_Model extends Vtiger_Module_Model{
    
	 public function getSideBarLinks($linkParams) {
        $parentQuickLinks = array();

         $quickLink1 = array(
             'linktype' => 'SIDEBARLINK',
             'linklabel' => '今天',
             'linkurl' => $this->getListViewUrl() . '&public=today',
             'linkicon' => '',
         );
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink1);

         $quickLink2 = array(
             'linktype' => 'SIDEBARLINK',
             'linklabel' => '昨天',
             'linkurl' => $this->getListViewUrl() . '&public=yesterday',
             'linkicon' => '',
         );
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);

         $quickLink3 = array(
             'linktype' => 'SIDEBARLINK',
             'linklabel' => '本周',
             'linkurl' => $this->getListViewUrl() . '&public=thisweek',
             'linkicon' => '',
         );
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink3);

         $quickLink4 = array(
             'linktype' => 'SIDEBARLINK',
             'linklabel' => '上周',
             'linkurl' => $this->getListViewUrl() . '&public=preweek',
             'linkicon' => '',
         );
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink4);

         $quickLink5 = array(
             'linktype' => 'SIDEBARLINK',
             'linklabel' => '本月',
             'linkurl' => $this->getListViewUrl() . '&public=thismonth',
             'linkicon' => '',
         );
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink5);

         $quickLink6 = array(
             'linktype' => 'SIDEBARLINK',
             'linklabel' => '上月',
             'linkurl' => $this->getListViewUrl() . '&public=premonth',
             'linkicon' => '',
         );
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink6);

         $quickLink7 = array(
             'linktype' => 'SIDEBARLINK',
             'linklabel' => '今年',
             'linkurl' => $this->getListViewUrl() . '&public=thisyear',
             'linkicon' => '',
         );
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink7);

         $quickLink8 = array(
             'linktype' => 'SIDEBARLINK',
             'linklabel' => '去年',
             'linkurl' => $this->getListViewUrl() . '&public=preyear',
             'linkicon' => '',
         );
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink8);

         /*$quickLink7 = array(
             'linktype' => 'SIDEBARLINK',
             'linklabel' => '自定义',
             'linkurl' => $this->getListViewUrl() . '&public=custom',
             'linkicon' => '',
         );
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink7);*/

         return $parentQuickLinks;
	}
}
?>
