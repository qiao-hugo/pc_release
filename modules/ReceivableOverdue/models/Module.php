<?php

class ReceivableOverdue_Module_Model extends Vtiger_Module_Model{
    
	 public function getSideBarLinks($linkParams) {
         $recordModel=Vtiger_Record_Model::getCleanInstance('ContractReceivable');
         if($_REQUEST['public']=='statistical'){
             $statistical = '<div style="border-bottom: 1px solid #006FB6;"> 运营应收统计表</div>';
             $overDueTitle = '逾期应收明细表';
             $warnTitle = '预警设置';
         }elseif($_REQUEST['public']=='EarlyWarningSetting'){
             $statistical = '运营应收统计表';
             $overDueTitle = '逾期应收明细表';
             $warnTitle = '<div style="border-bottom: 1px solid #006FB6;">预警设置</div>';
         }else{
             $statistical = '运营应收统计表';
             $overDueTitle = '<div style="border-bottom: 1px solid #006FB6;">逾期应收明细表</div>';
             $warnTitle = '预警设置';
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
             'linklabel' => '合同运营应收(大SaaS)',
             'linkurl' => 'index.php?module=ContractReceivable&view=List&bussinesstype=bigsass',
             'linkicon' => '',
         );
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);

         $quickLink = array(
             'linktype' => 'SIDEBARLINK',
             'linklabel' => '合同运营应收(小SaaS)',
             'linkurl' => 'index.php?module=ContractReceivable&view=List&bussinesstype=smallsass',
             'linkicon' => '',
         );
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);

         $quickLink = array(
             'linktype' => 'SIDEBARLINK',
             'linklabel' => $statistical,
             'linkurl' => 'index.php?module=ContractReceivable&view=List&public=statistical',
             'linkicon' => '',
         );
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);

         $quickLink = array(
             'linktype' => 'SIDEBARLINK',
             'linklabel' => $overDueTitle,
             'linkurl' => $this->getListViewUrl(),
             'linkicon' => '',
         );
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        if($recordModel->personalAuthority('ContractReceivable','EarlyWarningSetting')) {
            $quickLink = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => $warnTitle,
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
