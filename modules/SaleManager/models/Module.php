<?php

class SaleManager_Module_Model extends Vtiger_Module_Model{

	 public function getSideBarLinks($linkParams) {
		$parentQuickLinks = array();
		$recordModel=Vtiger_Record_Model::getCleanInstance('SaleManager');

		$quickLink = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '商务列表',
				'linkurl' => $this->getListViewUrl(),
				'linkicon' => '',
		);
		$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);

		if($recordModel->personalAuthority('Accounts','AccountProtectSetting')){
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '特殊划转客户角色设置',
                 'linkurl' => $this->getListViewUrl() . '&public=AuditSettings',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
        }

		return $parentQuickLinks;
	}
    public function getRole(){
	     $query="SELECT roleid,rolename FROM `vtiger_role`";
	     $db=PearDatabase::getInstance();
	     $result=$db->pquery($query,array());
	     $data=array();
	     if($db->num_rows($result)){
            while($row=$db->fetch_array($result)){
                $data[]=$row;
            }
         }
	     return $data;
    }
    public function getAccountRole(){
	     $query="SELECT roleid,rolename FROM `vtiger_accountrole`";
	     $db=PearDatabase::getInstance();
	     $result=$db->pquery($query,array());
	     $data=array();
	     if($db->num_rows($result)){
             while($row=$db->fetch_array($result)){
                 $data[]=$row;
             }
         }
	     return $data;
    }

}
?>