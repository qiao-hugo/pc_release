<?php
/* +****************
 *合同保存验证
 *新增产品必填，打回后产品不可编辑
 * ******************* */

class ContractsAgreement_Save_Action extends Vtiger_Save_Action {

	public function saveRecord($request) {
	    global $adb;
        $servicecontractsid=$request->get('servicecontractsid');
        $account_id=$request->get('account_id');
	    $recordid=$request->get('record');
	    if($servicecontractsid<=0){
            $this->showMsg('合同编号必填!!');
            exit;
        }
        if($account_id<=0)
        {
            $this->showMsg('客户必填!!');
            exit;
        }

        if($this->checkAccount($request))
        {
            $this->showMsg('客户与合同中的客户不一致!!');
            exit;
        }
        $signaturetype=$request->get('signaturetype');
        if($signaturetype=='eleccontract'){
            $file=$request->get('file');
            $attachmentsid=$request->get('attachmentsid');
            $request->set('eleccontracttpl',current($file).'##'.current($attachmentsid));
            $this->saveFileData($request);
        }
        $recordModel = $this->getRecordModelFromRequest($request);
        if($recordid && $recordModel->entity->column_fields['servicecontractsid']!=$servicecontractsid){
            $this->showMsg('合同编号不允许修改!!');
            exit;
        }
        if($this->checkInvoicecompany($request)){
            $this->showMsg('合同主体与原合同主体不一致!!');
            exit;
        }

        if($recordid>0 && $signaturetype=='eleccontract'){
            $recordModel->set('eleccontractid',$recordModel->entity->column_fields['eleccontractid']);
        }
		$recordModel->save();
        if($request->get('signaturetype')=='eleccontract'){
            $Query="SELECT sequence FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowstagesflag='CREATE_CODE' limit 1";
            $seqResult=$adb->pquery($Query,array($recordModel->getId()));
            $sql="DELETE FROM vtiger_salesorderworkflowstages WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.sequence>?";
            $adb->pquery($sql,array($recordModel->getId(),$seqResult->fields['sequence']));
            $object = new SalesorderWorkflowStages_SaveAjax_Action();
            $object->sendWxRemind(array('salesorderid'=>$recordModel->getId(),'salesorderworkflowstagesid'=>0));
        }
		return $recordModel;
	}
	public function checkAccount(Vtiger_Request $request)
    {
        global $adb;
        $servicecontractsid=$request->get('servicecontractsid');
        $account_id=$request->get('account_id');
        $recordid=$request->get('record');
        $query='SELECT vtiger_servicecontracts.sc_related_to,vtiger_servicecontracts.contract_no FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.servicecontractsid=?';
        $result=$adb->pquery($query,array($servicecontractsid));
        $mdata=$adb->query_result_rowdata($result,0);
        $query='SELECT vtiger_servicecontracts.sc_related_to,vtiger_servicecontracts.contract_no FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.servicecontractsid!=? AND vtiger_servicecontracts.contract_no LIKE ?';
        $cresult=$adb->pquery($query,array($servicecontractsid,$mdata['contract_no'].'%'));
        $query='SELECT vtiger_contractsagreement.accountid FROM vtiger_contractsagreement LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_contractsagreement.contractsagreementid WHERE vtiger_crmentity.deleted=0
                AND vtiger_contractsagreement.servicecontractsid=? AND vtiger_contractsagreement.contractsagreementid !=?';
        $dresult=$adb->pquery($query,array($servicecontractsid,$recordid));
        if(empty($mdata['sc_related_to']) && !$adb->num_rows($cresult) && !$adb->num_rows($dresult))
        {//如果主合同客户不存在且没有其他的补充协议则不验证
            return false;
        }
        elseif(!empty($mdata['sc_related_to']) && $mdata['sc_related_to']==$account_id)
        {
            return false;
        }
        elseif($adb->num_rows($cresult))
        {//补充协议中只要有一个不一样就
            while($row=$adb->fetch_array($cresult))
            {
                if($row['sc_related_to']!=$account_id)
                {
                    return true;
                }

            }
            return false;
        }
        elseif($adb->num_rows($dresult))
        {
            while($row=$adb->fetch_array($dresult))
            {
                if($row['accountid']!=$account_id)
                {
                    return true;
                }
            }
            return false;
        }
        return true;
    }
    /**
     * 文件保存
     * @param $request
     */
    public function saveFileData($request){
        global $current_user;
        $servicecontractsid=$request->get('servicecontractsid');
        $eleccontractidurl=$this->getElecTPLView($request);
        $recordModel=Vtiger_Record_Model::getInstanceById($servicecontractsid,'ServiceContracts');
        $data2=$recordModel->fileSave($eleccontractidurl,'files_style7','审核件');
        $array=array($data2['fileid']=>$data2['fileName']);
        $array1=array($data2['fileid']=>$data2['fileid']);
        $_POST['file']=$array;
        $_POST['attachmentsid']=$array1;
        $_POST['receiptorid']=array($current_user->id);
        $request->set('file',$array);
        $request->set('attachmentsid',$array1);
        $request->set('receiptorid',array($current_user->id));
    }

    /**
     * 消息提醒
     * @param $msg
     */
    public function showMsg($msg){
        echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>拒绝操作</h2><p class="text">'.$msg.'</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
    }

    /**
     * 获取合同URL
     * @param $request
     * @return mixed
     */
    public function getElecTPLView($request){
        $recordModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
        $request->set('contractId',$request->get('eleccontractid'));
        $jsonoutput=$recordModel->getElecTPLView($request);
        $data=json_decode($jsonoutput,true);
        return $data['data']['contract'];
    }
    public function checkInvoicecompany($request){
        $invoicecompany=$request->get('invoicecompany');
        if(empty($invoicecompany)){
            return true;
        }
        $servicecontractsid=$request->get('servicecontractsid');
        global $adb;
        $result=$adb->pquery('SELECT 1 FROM vtiger_servicecontracts WHERE invoicecompany=? AND servicecontractsid=?',array($invoicecompany,$servicecontractsid));
        if($adb->num_rows($result)){
            return false;
        }
        return true;
    }
}
