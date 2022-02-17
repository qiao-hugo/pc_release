<?php

class ReceivedPaymentsClassify_Module_Model extends Vtiger_Module_Model {
	public function getSideBarLinks($linkParams) {
        $recordModel=Vtiger_Record_Model::getCleanInstance('AccountReceivable');
		$parentQuickLinks = [];
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
            'linklabel' => '运营应收统计表',
            'linkurl' =>'index.php?module=ContractReceivable&view=List&public=statistical',
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
                'linkurl' =>'index.php?module=ContractReceivable&view=List&public=EarlyWarningSetting',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        }
        if(!isset($_REQUEST['public'])){
            $linklabel = '<div style="border-bottom: 1px solid #006FB6;">回款统计明细</div>';
        } else {
            $linklabel = '回款统计明细';
        }
        $quickLink = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => $linklabel,
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
        if($this->exportGrouprt('ReceivedPayments','PermissonSet')){
            $quickLink8 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '查询权限设置',
                'linkurl' =>'index.php?module=ReceivedPaymentsClassify&view=List&public=PermissonSet',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink8);
        }
		return $parentQuickLinks;
	}
    /**
     * 可导出数据的权限
     * @return bool
     */
    public function exportGroupri(){
        global $current_user;
        $id=$current_user->id;
        $db=PearDatabase::getInstance();
        //不必过滤是否在职因为离职的根本就登陆不了系统
        $query="select vtiger_user2department.userid from vtiger_user2department LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid WHERE CONCAT(vtiger_departments.parentdepartment,'::') REGEXP 'H25::'";
        $result=$db->run_query_allrecords($query);
        $userids=array();
        foreach($result as $values){
            $userids[]=$values['userid'];
        }
        $userids[]=1;
        //$userids=array(1,2155,323,1923);//有访问权限的
        if(in_array($id,$userids)){
            return true;
        }
        return false;
    }
    public function exportGrouprt($module,$classname){
        global $current_user;
        $id=$current_user->id;
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
