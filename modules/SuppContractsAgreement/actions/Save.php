<?php
/* +****************
 *合同保存验证
 *新增产品必填，打回后产品不可编辑
 * ******************* */

class SuppContractsAgreement_Save_Action extends Vtiger_Save_Action {

    private function returnError($message = 'error')
    {
        echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>拒绝操作</h2><p class="text">'.$message.'!!</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
        exit;
    }

    public function saveRecord($request) {

        $servicecontractsid=$request->get('suppliercontractsid');
        $account_id=$request->get('vendorid');
	    $recordid=$request->get('record');

	    if($servicecontractsid<=0) $this->returnError('合同编号必填');

        if(!$account_id && $servicecontractsid){
            global $adb;
            $sql = "select total,type from vtiger_suppliercontracts where suppliercontractsid=?";
            $result = $adb->pquery($sql,array($servicecontractsid));
            $_re = $adb->raw_query_result_rowdata($result, 0);
            if($_re['total'] > 0 && $_re['type'] != 'cost')  $this->returnError('合同金额大于0必须选择对应供应商');
        }

        if($account_id&&$account_id<=0) $this->returnError('客户必填');
        if($account_id&&$this->checkAccount($request))  $this->returnError('客户与合同中的客户不一致');

        $recordModel = $this->getRecordModelFromRequest($request);

        if($recordid && $recordModel->entity->column_fields['suppliercontractsid']!=$servicecontractsid){
            $this->returnError('合同编号不允许修改');
        }

		$recordModel->save();

		return $recordModel;
	}
	public function checkAccount(Vtiger_Request $request)
    {
        global $adb;
        $servicecontractsid=$request->get('suppliercontractsid');
        $account_id=$request->get('vendorid');
        $recordid=$request->get('record');
        $query='SELECT vtiger_suppliercontracts.vendorid,vtiger_suppliercontracts.contract_no FROM vtiger_suppliercontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_suppliercontracts.suppliercontractsid WHERE vtiger_crmentity.deleted=0 AND vtiger_suppliercontracts.suppliercontractsid=?';
        $result=$adb->pquery($query,array($servicecontractsid));
        $mdata=$adb->query_result_rowdata($result,0);
        $query='SELECT vtiger_suppliercontracts.vendorid,vtiger_suppliercontracts.contract_no FROM vtiger_suppliercontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_suppliercontracts.suppliercontractsid WHERE vtiger_crmentity.deleted=0 AND vtiger_suppliercontracts.suppliercontractsid=? AND vtiger_suppliercontracts.contract_no LIKE ?';
        $cresult=$adb->pquery($query,array($servicecontractsid,$mdata['contract_no'].'%'));
        $query='SELECT vtiger_suppcontractsagreement.vendorid 
                FROM vtiger_suppcontractsagreement 
                LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_suppcontractsagreement.contractsagreementid 
                WHERE vtiger_crmentity.deleted=0 AND vtiger_suppcontractsagreement.suppliercontractsid=? AND vtiger_suppcontractsagreement.contractsagreementid!=?';
        $dresult=$adb->pquery($query,array($servicecontractsid,$recordid));
        if(empty($mdata['vendorid']) && !$adb->num_rows($cresult) && !$adb->num_rows($dresult))
        {//如果主合同客户不存在且没有其他的补充协议则不验证
            return false;
        }
        elseif(!empty($mdata['vendorid']) && $mdata['vendorid']==$account_id)
        {
            return false;
        }
        elseif($adb->num_rows($cresult))
        {//补充协议中只要有一个不一样就
            while($row=$adb->fetch_array($cresult))
            {
                if($row['vendorid']!=$account_id)
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
                if($row['vendorid']!=$account_id)
                {
                    return true;
                }

            }
            return false;
        }
        return true;
    }
}
