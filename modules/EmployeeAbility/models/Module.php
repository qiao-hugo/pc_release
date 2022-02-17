<?php

class EmployeeAbility_Module_Model extends Vtiger_Module_Model{
    public function getSideBarLinks($linkParams) {
        $parentQuickLinks = array();
        global $current_user,$zhongxiaojingli,$zhongxiaozongjian;
        if(in_array($current_user->roleid,array($zhongxiaojingli,$zhongxiaozongjian)) || $this->exportGrouprt('EmployeeAbility','seteduurl')) {
            $quickLink = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '员工能力列表',
                'linkurl' => $this->getListViewUrl(),
                'linkicon' => '',
            );
        }
        $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        if($this->exportGrouprt('EmployeeAbility','seteduurl')) {
            $quickLink2 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '课程设置',
                'linkurl' => $this->getListViewUrl() . '&public=seteduurl',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
        }
        return $parentQuickLinks;
    }

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
?>
