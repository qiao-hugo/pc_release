<?php

class ContractReceivable_Module_Model extends Vtiger_Module_Model{
    
	 public function getSideBarLinks($linkParams) {
         $recordModel=Vtiger_Record_Model::getCleanInstance('ContractReceivable');
         $smalltitle = '合同运营应收(小SaaS)';
         $bigtitle = '合同运营应收(大SaaS)';
         if($_REQUEST['bussinesstype']=='bigsass'){
	         $bigtitle = '<div style="border-bottom: 1px solid #006FB6;">合同运营应收(大SaaS)</div>';
	         $smalltitle = '合同运营应收(小SaaS)';
	     }elseif($_REQUEST['bussinesstype']=='smallsass'){
             $bigtitle = '合同运营应收(大SaaS)';
             $smalltitle = '<div style="border-bottom: 1px solid #006FB6;">合同运营应收(小SaaS)</div>';
         }
		$parentQuickLinks = array();
		$quickLink = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '客户运营应收总表',
				'linkurl' => 'index.php?module=AccountReceivable&view=List',
				'linkicon' => '',
		);
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);

         $quickLink = array(
             'linktype' => 'SIDEBARLINK',
             'linklabel' => $bigtitle,
             'linkurl' => $this->getListViewUrl().'&bussinesstype=bigsass',
             'linkicon' => '',
         );
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);

         $quickLink = array(
             'linktype' => 'SIDEBARLINK',
             'linklabel' => $smalltitle,
             'linkurl' => $this->getListViewUrl().'&bussinesstype=smallsass',
             'linkicon' => '',
         );
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);

         $quickLink = array(
             'linktype' => 'SIDEBARLINK',
             'linklabel' => '运营应收统计表',
             'linkurl' => 'index.php?module=ContractReceivable&view=List&public=statistical',
             'linkicon' => '',
         );
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);

         $quickLink = array(
             'linktype' => 'SIDEBARLINK',
             'linklabel' => '逾期应收明细表',
             'linkurl' => 'index.php?module=ReceivableOverdue&view=List',
             'linkicon' => '',
         );
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        if($recordModel->personalAuthority('ContractReceivable','EarlyWarningSetting')){
             $quickLink = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '预警设置',
                 'linkurl' => 'index.php?module=ContractReceivable&view=List&public=EarlyWarningSetting',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
         }
         $quickLink = array(
             'linktype' => 'SIDEBARLINK',
             'linklabel' => '回款统计明细',
             'linkurl' => 'index.php?module=ReceivedPaymentsClassify&view=List',
             'linkicon' => '',
         );
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
         $quickLink = array(
             'linktype' => 'SIDEBARLINK',
             'linklabel' => '回款统计分类配置',
             'linkurl' => 'index.php?module=ReceivedPaymentsClassify&view=List&public=configuration',
             'linkicon' => '',
         );
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
         if($recordModel->personalAuthority('ReceivedPayments','PermissonSet')){
             $quickLink = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '查询权限设置',
                 'linkurl' =>'index.php?module=ReceivedPaymentsClassify&view=List&public=PermissonSet',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
         }
         return $parentQuickLinks;
	}
}
?>
