<?php
/**
 * wangbin 2015-1-20 13:58:37 添加跟多回款的筛选项
 * */
class ReceivedPaymentsCollate_Module_Model extends Vtiger_Module_Model {
	public function getSideBarLinks($linkParams) {
        global $current_user;
        $userId=$current_user->id;
        $quickLink1 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '回款列表',
            'linkurl' =>'index.php?module=ReceivedPayments&view=List',
            'linkicon' => '',
        );
        $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink1);
        if($_REQUEST['record']){
            $recordModel=ReceivedPaymentsCollate_Record_Model::getInstanceById($_REQUEST['record'],'ReceivedPaymentsCollate');
            $paymentid=$recordModel->getReceivedPaymentSidByCollate($_REQUEST['record']);
            $quickLink2 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '回款详情',
                'linkurl' =>'index.php?module=ReceivedPayments&view=Detail&record='.$paymentid,
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
        }
		return $parentQuickLinks;
	}

}
