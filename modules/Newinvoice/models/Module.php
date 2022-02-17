<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Newinvoice_Module_Model extends Inventory_Module_Model {
	public function getSideBarLinks($linkParams) {
		global $current_user;

		$quickLink = array();
		if(Users_Privileges_Model::isPermitted('Newinvoice', 'Export')) {
			$parentQuickLinks = parent::getSideBarLinks($linkParams);
			$quickLink[] = array(
					'linktype' => 'SIDEBARLINK',
					'linklabel' => '预开票发票导出',
					'linkurl' => $this->getListViewUrl().'&filter=export',
					'linkicon' => '',
			);

			$parentQuickLinks = parent::getSideBarLinks($linkParams);
			$quickLink[] = array(
					'linktype' => 'SIDEBARLINK',
					'linklabel' => '合同发票查询',
					'linkurl' => $this->getListViewUrl().'&filter=search_invoice',
					'linkicon' => '',
			);

			$parentQuickLinks = parent::getSideBarLinks($linkParams);
			$quickLink[] = array(
					'linktype' => 'SIDEBARLINK',
					'linklabel' => '发票查询',
					'linkurl' => $this->getListViewUrl().'&filter=search_newinvoiceextende',
					'linkicon' => '',
			);

			$parentQuickLinks = parent::getSideBarLinks($linkParams);
			$quickLink[] = array(
					'linktype' => 'SIDEBARLINK',
					'linklabel' => '预开票未匹配金额导出',
					'linkurl' => $this->getListViewUrl().'&filter=search_billingNotMatch',
					'linkicon' => '',
			);

			$quickLink[] = array(
					'linktype' => 'SIDEBARLINK',
					'linklabel' => '票据发票导出',
					'linkurl' => $this->getListViewUrl().'&filter=all_export',
					'linkicon' => '',
			);
            $quickLink[] = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '不需要发票客户导出',
                'linkurl' => $this->getListViewUrl().'&filter=need_invoice',
                'linkicon' => '',
            );
            $quickLink[] = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '合同变更发票导出',
                'linkurl' => $this->getListViewUrl().'&filter=contract_change_invoice',
                'linkicon' => '',
            );
            $quickLink[] = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '预开票审核设置',
                'linkurl' => $this->getListViewUrl().'&filter=pre_invoice_audit',
                'linkicon' => '',
            );

            $quickLink[] = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '预开票回款提醒设置',
                'linkurl' => $this->getListViewUrl().'&filter=pre_invoice_remind',
                'linkicon' => '',
            );
            $quickLink[] = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '预开票回款延期申请',
                'linkurl' => $this->getListViewUrl().'&filter=pre_invoice_delay',
                'linkicon' => '',
            );
            $quickLink[] = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '预开票回款延期信息导出',
                'linkurl' => $this->getListViewUrl().'&filter=pre_invoice_delay_export',
                'linkicon' => '',
            );
		}

		

		//Check profile permissions for Dashboards 
		$moduleModel = Vtiger_Module_Model::getInstance('Dashboard');
		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());
		if($permission) {
			foreach ($quickLink as $val){
				$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($val);
			}
		}
		                                      
		return $parentQuickLinks;
	}

    /**
     * 合同担保list 也用到了这里改动需谨慎
     * @param $module
     * @param $classname
     * @param int $id
     * @return bool
     */
    public function exportGrouprt($module,$classname,$id=0){
        if($id==0)
        {
            global $current_user;
            $id = $current_user->id;
        }
        $db=PearDatabase::getInstance();
        $query="SELECT 1 FROM vtiger_exportmanage WHERE deleted=0 AND userid=? AND module=? AND classname=?";
        $result=$db->pquery($query,array($id,$module,$classname));
        $num=$db->num_rows($result);
        if($num){
            return true;
        }
        return false;
    }
}