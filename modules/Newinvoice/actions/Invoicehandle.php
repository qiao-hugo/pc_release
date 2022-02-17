<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Newinvoice_Invoicehandle_Action extends Vtiger_Action_Controller {

	
	function checkPermission(Vtiger_Request $request) {

        return;
	}

    /**设当前发票的状态是作废还是退票
     * @ruthor steel
     * @time 2015-05-04
     * @param Vtiger_Request $request
     * @throws Exception
     */
	public function process(Vtiger_Request $request) {


        $type=$request->get('type');
        $recordId = $request->get('record');//合同的id
        Vtiger_Record_Model::getInstanceById($recordId,'Invoice');
        $db=PearDatabase::getInstance();
        global $current_user;
        //针对按钮加入权限
        if(($type=="tovoid" && (is_admin($current_user) || isPermitted('Newinvoice', 'ToVoid')=='yes'))|| ($type=="returnticket" && (is_admin($current_user) || isPermitted('Newinvoice', 'ReturnTicket')=='yes'))){
            $sql="UPDATE vtiger_Newinvoice SET invoicestatustime=SYSDATE(),statusdoid=?";
            if($type=="tovoid"){
                $sql.=",invoicestatus='tovoid',modulestatus='c_cancel'";
            }elseif($type=="returnticket"){
                $sql.=",invoicestatus='returnticket',modulestatus='c_returnTicket'";
                $query="DELETE FROM vtiger_newinvoicerelatedreceive WHERE invoiceid=?";
                $db->pquery($query,array($recordId));
            }
            $sql.=" WHERE invoiceid=?";
            $db->pquery($sql,array($current_user->id,$recordId));
            $return=array();
        }elseif($type=='comtaxt'){
            $recordinovice=$request->get('recordinovice');
            $sql='';
            if(!empty($recordinovice)&& is_numeric($recordinovice)){
                $sql=" AND vtiger_newinvoice.invoiceid!={$recordinovice}";
            }
            $query="SELECT total, IFNULL( ( SELECT sum(IFNULL(taxtotal, 0)) FROM vtiger_newinvoice WHERE vtiger_newinvoice.contractid = vtiger_servicecontracts.servicecontractsid {$sql} ), 0 ) AS taxtotal, IFNULL( ( SELECT sum( IFNULL(totalandtaxextend, 0) ) FROM vtiger_newinvoice WHERE vtiger_newinvoiceextend.invoicestatus = 'tovoid' AND vtiger_newinvoiceextend.deleted = 0 AND vtiger_newinvoiceextend.invoiceid IN ( SELECT vtiger_newinvoice.invoiceid FROM vtiger_newinvoice WHERE vtiger_newinvoice.contractid = vtiger_servicecontracts.servicecontractsid {$sql} ) ), 0 ) AS tovoid, IFNULL( ( SELECT sum( IFNULL( vtiger_newnegativeinvoice.negativetotalandtaxextend, 0 ) ) FROM vtiger_newnegativeinvoice WHERE vtiger_newnegativeinvoice.deleted = 0 AND vtiger_newnegativeinvoice.invoiceid IN ( SELECT vtiger_newinvoice.invoiceid FROM vtiger_newinvoice WHERE vtiger_newinvoice.contractid = vtiger_servicecontracts.servicecontractsid {$sql} ) ), 0 ) AS redinvoice FROM vtiger_servicecontracts WHERE servicecontractsid =?";
            $resultdata=$db->pquery($query,array($recordId));
            $return['total']=$db->query_result($resultdata,0,'total');
            $return['taxtotal']=$db->query_result($resultdata,0,'taxtotal');
            $return['tovoid']=$db->query_result($resultdata,0,'tovoid');
            $return['redinvoice']=$db->query_result($resultdata,0,'redinvoice');
        }else{
            $resultaa['success'] = 'false';
            $resultaa['error']['message'] = ':你无权操作!';
            echo json_encode($resultaa);
            exit;
        }
		$response = new Vtiger_Response();
		$response->setResult($return);
		$response->emit();
	}
}
