<?php

class ContractsAgreement_Module_Model extends Vtiger_Module_Model{
    
	 public function getSideBarLinks($linkParams) {
		$parentQuickLinks = array();
		$quickLink = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '合同列表',
				'linkurl' => $this->getListViewUrl(),
				'linkicon' => '',
		);
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);

         if($this->exportGrouprt('ContractsAgreement','dempartConfirm'))
         {
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '合同补充协议审核设置',
                 'linkurl' => $this->getListViewUrl() .'&public=dempartConfirm',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }

		return $parentQuickLinks;
	}

    public function getAuditsettings($auditsettingtype)
    {
        $db=PearDatabase::getInstance();
        $sql = "SELECT auditsettingsid,auditsettingtype,
           (select vtiger_departments.departmentname FROM vtiger_departments WHERE vtiger_departments.departmentid=vtiger_auditsettings.department) AS department,
           (SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_auditsettings.oneaudituid) AS oneaudituid, 
           IFNULL((SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_auditsettings.towaudituid ),'--') AS towaudituid, 
           IFNULL((SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_auditsettings.audituid3 ),'--') AS audituid3
           FROM vtiger_auditsettings WHERE auditsettingtype=? ORDER BY auditsettingsid DESC";
        //return $db->run_query_allrecords($sql,array($auditsettingtype));
        return $db->pquery($sql,array($auditsettingtype));
    }


}
?>
