<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Newinvoice_DetailView_Model extends Inventory_DetailView_Model {
    /**
     * 详细页面加上受控制的连接
     * @param <array> $linkParams - parameters which will be used to calicaulate the params
     * @return <array> - array of link models in the format as below
     *                   array('linktype'=>list of link models);
     */
    public function getDetailViewLinks($linkParams) {
        $linkTypes = array('DETAILVIEWBASIC','DETAILVIEW');
        $moduleModel = $this->getModule();
        $recordModel = $this->getRecord();

        $moduleName = $moduleModel->getName();
        $recordId = $recordModel->getId();

        $detailViewLink = array();

        if(Users_Privileges_Model::isPermitted($moduleName, 'EditView')) {
            $detailViewLinks[] = array(
                'linktype' => 'LISTVIEWBASIC',
                'linklabel' => 'LBL_ADD_RECORD',
                'linkurl' => $moduleModel->getCreateRecordUrl(),
                'linkicon' => ''
            );
        }
        if(Users_Privileges_Model::isPermitted($moduleName, 'EditView', $recordId)) {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_EDIT',
                'linkurl' => $recordModel->getEditViewUrl(),
                'linkicon' => ''
            );
        }
        global $current_user;
        if(false && $recordModel->entity->column_fields['modulestatus']=='c_complete') {
            $user=Users_Privileges_Model::getInstanceById($recordModel->entity->column_fields['assigned_user_id']);
            if($user->reports_to_id == $current_user->id || $current_user->is_admin=='on'){
                $detailViewLinks[] = array(
                    'linktype' => 'DETAILVIEWBASIC',
                    'linklabel' => 'LBL_UPDATERECEIVED',
                    'linkurl' =>'',
                    'linkicon' => ''
                );
            }

        }
        /**
         * 生成工作流
         */

        if(in_array($recordModel->entity->column_fields['modulestatus'],array('a_normal','a_exception'))
            && $recordModel->entity->column_fields['assigned_user_id']==$current_user->id
        )
        {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_STDAPPLY',
                'linkurl' => '',
                'linkicon' => '');
        }
        /**
         * 商务作废发票
         */
        if(in_array($recordModel->entity->column_fields['modulestatus'],array('a_normal','b_check','b_actioning'))
            && $recordModel->entity->column_fields['assigned_user_id']==$current_user->id
            && $this->fillInInvoice($recordId)
        )
        {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_NFILLCANCEL',
                'linkurl' => '',
                'linkicon' => '');
        }
        /*if(Users_Privileges_Model::isPermitted($moduleName, 'EditView', $recordId)) {
            if(Users_Privileges_Model::isPermitted($moduleName, 'NegativeEdit', $recordId)){
                $detailViewLinks[] = array(
                    'linktype' => 'DETAILVIEWBASIC',
                    'linklabel' => 'LBL_EDIT_NEGATIVE',
                    'linkurl' => 'index.php?module='.$moduleName.'&view=Edit&record='.$recordId.'&Negative=NegativeEdit',
                    'linkicon' => ''
                );
            }
        }*/
        /*if(Users_Privileges_Model::isPermitted($moduleName, 'DuplicatesHandling', $recordId)
            && $recordModel->entity->column_fields['modulestatus']=='c_complete' 
            && Invoice_Record_Model::checksign($recordId)) {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_SIGN',
                'linkurl' => '',
                'linkicon' => ''
            );
        }*/

        if(Users_Privileges_Model::isPermitted($moduleName, 'DuplicatesHandling', $recordId) 
            && Newinvoice_Record_Model::checksign($recordId) && Newinvoice_Record_Model::checkWorkflows($recordId) ) {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_SIGN',
                'linkurl' => '',
                'linkicon' => ''
            );
        }


        /*测试领取签名*/
        /*$detailViewLinks[] = array(
            'linktype' => 'DETAILVIEWBASIC',
            'linklabel' => 'LBL_SIGN',
            'linkurl' => '',
            'linkicon' => ''
        );*/

        //变更合同 gaocl add
        if(Newinvoice_Record_Model::hasRepeatServiceContracts($recordId)
            && $this->exportGrouprt('Newinvoice','changeContracts')) {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_CHANGE_CONTRACTS',
                'linkurl' => '',
                'linkicon' => ''
            );
        }

        if(Users_Privileges_Model::isPermitted($moduleName, 'DuplicatesHandling', $recordId)&&$recordModel->entity->column_fields['taxtype']=='specialinvoice'&&$recordModel->entity->column_fields['billingid']>0) {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_BILLING',
                'linkurl' => '',
                'linkicon' => ''
            );
        }
        /*更新开票信息*/
        $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_BILLING',
                'linkurl' => '',
                'linkicon' => ''
            );

        if($recordModel->entity->column_fields['modulestatus']=='c_complete'){
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_BANDONED',
                'linkurl' => '',
                'linkicon' => ''
            );
        }

        if(!empty($detailViewLinks)){
            foreach ($detailViewLinks as $detailViewLink) {
                $linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLink);
            }
        }



        $relatedLinks = $this->getDetailViewRelatedLinks();
        foreach($relatedLinks as $relatedLinkEntry) {
            $relatedLink = Vtiger_Link_Model::getInstanceFromValues($relatedLinkEntry);
            $linkModelList[$relatedLink->getType()][] = $relatedLink;
        }

        $linkModelList['DETAILVIEWRELATED']=$moduleModel->makeRelatedurl($recordId);

        $widgets = $this->getWidgets();
        foreach($widgets as $widgetLinkModel) {
            $linkModelList['DETAILVIEWWIDGET'][] = $widgetLinkModel;
        }


        return $linkModelList;
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
    public function fillInInvoice($record){
        global $adb;
        $query="SELECT invoicecodeextend,invoice_noextend FROM vtiger_newinvoiceextend WHERE deleted=0 AND invoiceid={$record}";
        $sel_result=$adb->pquery($query,array());
        $res_cnt = $adb->num_rows($sel_result);
        if ($res_cnt == 0) {
            return true;
        }
        return false;
    }
}
