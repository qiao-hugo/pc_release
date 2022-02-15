<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/


class SupplierContracts_Record_Model extends Vtiger_Record_Model{
    public $Kllcompanycode=array('KLL','WXKLL','KLLDSHJKJJT','GZKLL');//凯丽隆审核的合同主体公司
    public $TREASURER_TWO=array('ZD','DCL','ZDWL','YJSKJ','HKKLLGJ');//上海财务审核的合同主体公司
    public $Kllneedle='H283::';//凯丽隆部节门点
    public $WXKLLneedle='H349::';//无锡凯丽隆部节门点
    public $purchaseWorkFlowSid=793975;  //采购合同审核流
    public $costWorkFlowSid=2970157;     //费用合同审核流
	// 修改
	static function setContractsNO($id, $n) {
		$db = PearDatabase::getInstance();
		$sql = "update vtiger_suppliercontracts set contract_no=? where suppliercontractsid=?";
		$db->pquery($sql, array($n, $id));
	}
    // 获取产品返点
    public function getVendorsrebate($suppliercontractsid) {
        $sql = "select vtiger_vendorsrebate.* from vtiger_vendorsrebate LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_vendorsrebate.productid where vtiger_vendorsrebate.suppliercontractsid=? AND vtiger_vendorsrebate.deleted=0";
        global $adb;
        $listResult = $adb->pquery($sql, array($suppliercontractsid));
        $res_cnt = $adb->num_rows($listResult);
        $data = array();
        if($res_cnt > 0) {
            while($rawData=$adb->fetch_array($listResult)) {
                $data[] = $rawData;
            }
        }
        return $data;
    }
    public function checksign($recordid,$setype='SupplierContractOne'){
        $db=PearDatabase::getInstance();
        $result=$db->pquery("select 1 from vtiger_invoicesign where vtiger_invoicesign.setype=? AND vtiger_invoicesign.invoiceid=?",array($setype,$recordid));
        if($db->num_rows($result)>0){
            return false;
        }
        return true;
    }
    public function checkWorkflows($stagerecordid,$CREATESIGN) {
        $db=PearDatabase::getInstance();

        $query="SELECT
                    vtiger_workflowstages.workflowstagesflag
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'SupplierContracts'
                AND vtiger_salesorderworkflowstages.isaction=1";
        $result=$db->pquery($query,array($stagerecordid));
        $currentflag=$db->query_result($result, 0, 'workflowstagesflag');
        if ($currentflag == $CREATESIGN) {  //发票领取阶段
            return true;
        }
        return false;
    }
    /**
     * 根据合同的ID判断当前登陆人是否是创建人
     * @param $recordid
     * @return boolean
     */
    public function checkCreator($recordId){
        global $current_user;
        $where=getAccessibleUsers('SupplierContracts','List',true);
        if($where=='1=1'){
            return true;
        }
        $where[]=$current_user->id;
        $query='select 1 from vtiger_suppliercontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_suppliercontracts.suppliercontractsid WHERE vtiger_crmentity.smcreatorid IN('.implode(',',$where).') AND vtiger_suppliercontracts.suppliercontractsid=?';
        $db=PearDatabase::getInstance();
        $dataResult=$db->pquery($query,array($recordId));
        if($db->num_rows($dataResult))
        {
            return true;
        }
        return false;
    }
    /**
     * @return array
     * @author: steel.liu
     * @Date:xxx
     * 合同状态权限设置
     */
    public function getSettingStatus(){
        $query="SELECT vtiger_supplierstatus.supplierstatusid,vtiger_supplierstatus.userid,
                if(suppliercontractsstatus='GY','业务供应商合同','行政供应商合同') AS contractstatus,vtiger_users.last_name
                 FROM vtiger_supplierstatus 
                LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_supplierstatus.userid
                WHERE vtiger_supplierstatus.deleted=0";
        $db=PearDatabase::getInstance();
        $result=$db->pquery($query,array());
        $arr=array();
        while($row=$db->fetch_array($result)){
            $arr[]=$row;
        }
        return $arr;
    }

    /**
     * 获取已报销的数据信息
     * @param $record
     * @return array
     */
    public function getReimbursementStatement($record){
        global $adb;
        $query='SELECT 
            reimbursementstatementid,
            suppliercontractsid,
            flownumberofpaymentform,
            paymentamount,
            sourcedata,
            paymentdate
         FROM `vtiger_reimbursementstatement` WHERE deleted=0 AND suppliercontractsid=? ORDER BY reimbursementstatementid DESC';
        $result=$adb->pquery($query,array($record));
        $return=array();
        if($adb->num_rows($result)){
            while($row=$adb->fetch_array($result)){
                $return[]=$row;
            }
        }
        return $return;
    }

    /**
     * 报销单系统获取合同信息
     * @param $request
     * @return array
     * @throws Exception
     */
    public function getContractInfoByNo($request){
        global $adb,$site_URL;
        $contractno=$request->get('field0038');
        if(!empty($contractno)){
            $query="SELECT
                    vtiger_suppliercontracts.contract_no,
                    vtiger_suppliercontracts.modulestatus,
                    vtiger_suppliercontracts.invoicecompany,
                 vtiger_suppliercontracts.residualamount,
                 vtiger_suppliercontracts.paymentclause,
                 vtiger_suppliercontracts.returndate,
                 vtiger_suppliercontracts.currencytype,
                 vtiger_suppliercontracts.bankcode,
                 vtiger_suppliercontracts.bankaccount,
                 vtiger_suppliercontracts.banknumber,
                 vtiger_suppliercontracts.bankname,
                 vtiger_suppliercontracts.effectivetime,
                 vtiger_suppliercontracts.total,
                 vtiger_suppliercontracts.file,
                 vtiger_suppliercontracts.contract_name,
                 vtiger_suppliercontracts.amountpaid,
                 vtiger_suppliercontracts.suppliercontractsid
                FROM
                    vtiger_suppliercontracts
                LEFT JOIN vtiger_crmentity ON vtiger_suppliercontracts.suppliercontractsid = vtiger_crmentity.crmid
                WHERE vtiger_crmentity.deleted = 0
                #AND (vtiger_suppliercontracts.modulestatus='c_complete' OR vtiger_suppliercontracts.isvirtualnumber=1)
                AND vtiger_suppliercontracts.contract_no=? limit 1";
            $result=$adb->pquery($query,array($contractno));
            if($adb->num_rows($result)){
                $data=array();
                $row=$adb->query_result_rowdata($result,0);

                $query="SELECT `name`,attachmentsid,newfilename,path FROM `vtiger_files` WHERE relationid=? and delflag=0 AND description='SupplierContracts'";
                $file=array();
                $result=$adb->pquery($query,array($row['suppliercontractsid']));
                if($adb->num_rows($result)){
                    while($rowData=$adb->fetch_array($result)){
                        $filePath = $rowData['path'];
                        $fileName = html_entity_decode($rowData['name'], ENT_QUOTES, vglobal('default_charset'));
                        if($rowData['newfilename']>0){
                            $savedFile = $rowData['attachmentsid'] . "_" . $rowData['newfilename'];
                        }else{
                            $t_fileName = base64_encode($fileName);
                            $t_fileName = str_replace('/', '', $t_fileName);
                            $savedFile = $rowData['attachmentsid']."_".$t_fileName;
                        }
                        if(file_exists($filePath.$savedFile)){
                            $urlPath=$site_URL.$filePath.$savedFile;
                            $extt=explode('.',$rowData['name']);
                            $file[]=array('name'=>$rowData['name'],'url'=>$urlPath,'extension'=>end($extt));
                        }
                    }
                }
                $data['field0042']=$row['suppliercontractsid'];//合同ID
                $data['field0038']=$row['contract_no'];//合同编号
                $data['field0014']=!empty($row['bankname'])?$row['bankname']:'';//供应商名称
                $data['field0015']=!empty($row['bankaccount'])?$row['bankaccount']:'';//供应商开户银行
                $data['field0018']=!empty($row['banknumber'])?$row['banknumber']:'';//供应商账户
                $data['field0037']=!empty($row['invoicecompany'])?$row['invoicecompany']:'';//付款方名称
                $data['field0016']=!empty($row['total'])?$row['total']:'';//合同金额
                $data['field0017']=!empty($row['amountpaid'])?$row['amountpaid']:'';//已付款金额
                $data['field0039']=bcsub($row['total'],$row['amountpaid'],2);//剩余金额
                $data['field0051']=$file;//合同附件
                $data['field0013']=!empty($row['paymentclause'])?$row['paymentclause']:'';//合同条款
                $data['field0012']=!empty($row['contract_name'])?$row['contract_name']:'';//合同名称
                $data['field0055']=!empty($row['effectivetime'])?$row['effectivetime']:'';//合同有效期
                $data['field0070']=$row['modulestatus'];//合同状态
                $return=array('success'=>true,'data'=>$data);
            }else{
                $return=array('success'=>false,'msg'=>'没有相关数据');
            }
        }else{
            $return=array('success'=>false,'msg'=>'无效参数');
        }
        return $return;
    }

    /**
     * 写入报销单
     * @param $request
     * @return array
     */
    public function putReimbursement($request){
        global $adb,$current_user;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile(1);
        $record=$request->get('field0042');//合同ID
        try{
            $recordModel=Vtiger_Record_Model::getInstanceById($record,'SupplierContracts');
            $flag=false;
        }catch (Exception $e){
            $flag=true;
        }
        $return=array('success'=>false);
        do{
            if($flag){
                $return['msg']='field0042无效';
                break;
            }
            $field0024=$request->get('field0024');//付款单金额
            if(!is_numeric($field0024) || $field0024<=0){
                $return['msg']='field0024付款单金额无效';
                break;
            }
            $amountpaid=$recordModel->get('amountpaid');//已付款金额
            $total=$recordModel->get('total');
            $Addamountpaid=bcadd($amountpaid,$field0024,2);
            if($total>0 && bccomp($total,$Addamountpaid,2)<0){
                $return['msg']='已付款总额大于合同金额!';
                break;
            }
            $_REQUEST['action']='SaveAjax';
            $field0026=$request->get('field0026');//付款单日期
            $field0001=$request->get('field0001');//付款单流水号
            $subresidualamount=$total>0?bcsub($total,$Addamountpaid,2):0;
            $recordModel->set('id', $record);
            $recordModel->set('mode', 'edit');
            $recordModel->set('amountpaid',$Addamountpaid);
            $recordModel->set('residualamount',$subresidualamount);
            $recordModel->save();
            $params=array(
                'suppliercontractsid'=>$record,
                'flownumberofpaymentform'=>$field0001,
                'paymentamount'=>$field0024,
                'sourcedata'=>'OA付款回传',
                'paymentdate'=>$field0026
            );
            $sql='INSERT INTO `vtiger_reimbursementstatement`(suppliercontractsid,flownumberofpaymentform,paymentamount,sourcedata,paymentdate) VALUES (?,?,?,?,?)';
            $adb->pquery($sql,array($params));
            $return=array('success'=>true,'msg'=>'接收成功');
        }while(0);
        return $return;
    }

    /**
     * 是否可以作废合同
     */
    public function canContractCancel()
    {
        $recordid = $this->getId();
        $ardb = PearDatabase::getInstance();
        $data = array('success' => true, 'message' => '');
        $sql1 = "select 1 from vtiger_rechargesheet a left join vtiger_refillapplication b on a.refillapplicationid=b.refillapplicationid 
 where a.suppliercontractsid=? and b.modulestatus != ? ";
        $result1 = $ardb->pquery($sql1, array($recordid, 'c_cancel'));
        if ($ardb->num_rows($result1)) {
            return $data = array('success' => false, 'message' => '当前采购合同存在非作废状态的充值申请单！如需作废，请先作废充值申请单或者替换充值单采购合同');
        }

        $sql2 = 'select 1 from vtiger_newinvoice a left join vtiger_crmentity b on  a.invoiceid =b.crmid where a.contractid = ? and a.modulestatus !=? and b.deleted=0';
        $result2 = $ardb->pquery($sql2, array($recordid, 'c_cancel'));
        if ($ardb->num_rows($result2)) {
            return $data = array('success' => false, 'message' => '当前采购合同存在非作废状态的发票！如需作废，请先作废发票');
        }

        $sql3 = "select 1 from vtiger_receivedpayments where relatetoid=? and receivedstatus in(?,?)";
        $result3 = $ardb->pquery($sql3, array($recordid, 'RebateAmount','Rebate'));
        if ($ardb->num_rows($result3)) {
            return $data = array('success' => false, 'message' => '当前采购合同存在返点款类型的回款，请联系财务处理清除回款匹配或者作废回款');
        }
        return $data;
    }

    /**
     * 是否可以变更供应商
     */
    public function getVendorid()
    {
        $recordid = $this->getId();
        $ardb = PearDatabase::getInstance();
            $sql1 = "select * from vtiger_rechargesheet a left join vtiger_refillapplication b on a.refillapplicationid=b.refillapplicationid left join vtiger_crmentity c on b.refillapplicationid=c.crmid
 where  a.suppliercontractsid=? and b.modulestatus !=? and c.deleted=0";
        $result1 = $ardb->pquery($sql1, array($recordid, 'c_cancel'));
        if ($ardb->num_rows($result1)) {
            while ($row = $ardb->fetchByAssoc($result1)) {
                $data[] = $row['vendorid'];
            }
            return $data[0];
        }

        $sql2 = "select a.* from vtiger_accountplatform a left join vtiger_crmentity b on a.accountplatformid=b.crmid where a.suppliercontractsid = ? and b.deleted=0";
        $result2 = $ardb->pquery($sql2, array($recordid));
        if ($ardb->num_rows($result2)) {
            while ($row = $ardb->fetchByAssoc($result2)) {
                $data[] = $row['vendorid'];
            }
            return $data[0];
        }

        $sql3 = "select a.* from vtiger_productprovider a  left join vtiger_crmentity b on a.productproviderid=b.crmid where  a.suppliercontractsid = ? and b.deleted=0";
        $result3 = $ardb->pquery($sql3, array($recordid));
        if ($ardb->num_rows($result3)) {
            while ($row = $ardb->fetchByAssoc($result3)) {
                $data[] = $row['vendorid'];
            }
            return $data[0];
        }

        $sql4 = 'select a.* from vtiger_newinvoice a left join vtiger_crmentity b on  a.invoiceid =b.crmid where a.contractid = ? and a.modulestatus !=? and b.deleted=0';
        $result4 = $ardb->pquery($sql4, array($recordid, 'c_cancel'));
        if ($ardb->num_rows($result4)) {
            while ($row = $ardb->fetchByAssoc($result4)) {
                $data[] = $row['accountid'];
            }
            return $data[0];
        }
        return '';
    }

    /**
     * 返回审核权限设置
     * @return array
     */
    public static function getAuditsettings($auditsettingtype=array("ContractsAuditset")) {
        $db=PearDatabase::getInstance();
        $sql = "SELECT auditsettingsid, IF(auditsettingtype='SupplierStatementCAuditset','供应商结算单审核设置',IF(auditsettingtype='ContractsAuditset','非标合同审核','采购合同')) AS auditsettingtype,
   (select vtiger_departments.departmentname FROM vtiger_departments WHERE vtiger_departments.departmentid=vtiger_auditsettings.department) AS department,
   (SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_auditsettings.oneaudituid) AS oneaudituid, 
   IFNULL((SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_auditsettings.towaudituid ),'--') AS towaudituid, 
   IFNULL((SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_auditsettings.audituid3 ),'--') AS audituid3,
   IFNULL((SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_auditsettings.audituid4 ),'--') AS audituid4,
   IFNULL((SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_auditsettings.audituid5 ),'--') AS audituid5
   FROM vtiger_auditsettings WHERE auditsettingtype in('".implode("','",$auditsettingtype)."') ORDER BY auditsettingsid DESC";
        //return $db->run_query_allrecords($sql,array($auditsettingtype));
        return $db->pquery($sql,array());
    }

    public function getContractInfo($request){
        $db = PearDatabase::getInstance();
        $sql = "select a.contract_no,b.vendorname,a.modulestatus,c.last_name as signname,a.suppliercontractsstatus,a.total from vtiger_suppliercontracts a 
  left join vtiger_vendor b on a.vendorid=b.vendorid left join vtiger_users c on c.id=a.signid left join vtiger_crmentity d on d.crmid=a.suppliercontractsid where contract_no=? and d.deleted=0";
        $result = $db->pquery($sql,array($request->get('contract_no')));
        if(!$db->num_rows($result)){
            return array('success'=>false,'msg'=>'未查询到该合同编号');
        }
        $row =$db->fetchByAssoc($result,0);
        $lng = translateLng("SupplierContracts");
        $data = array(
            'contractNo'=>$row['contract_no'],
            'vendorName'=>$row['vendorname'],
            'moduleStatus'=>$lng[$row['modulestatus']],
            'signName'=>$row['signname'],
            'supplierContractsStatus'=>$lng[$row['suppliercontractsstatus']],
            'total'=>$row['total'],
        );
        return array("success"=>true,'msg'=>'获取成功','data'=>$data);
    }

    public function getSonCate($parentCate){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select * from vtiger_soncate a left join vtiger_parentcate b on b.parentcateid=a.parentcate  where b.parentcate=? and a.deleted=0",array($parentCate));
        if(!$db->num_rows($result)){
            return array();
        }
        while ($row=$db->fetchByAssoc($result)){
            $sonCates[] = $row;
        }
        return $sonCates;
    }

    public function getFilterWorkFlow($soncateid,$total,$frameworkcontract){
        $db =PearDatabase::getInstance();
        $sql = "select * from vtiger_soncate where soncateid=? limit 1";
        $result = $db->pquery($sql,array($soncateid));
        if(!$db->num_rows($result)){
            return array();
        }

        $row =$db->fetchByAssoc($result,0);
        if($total>=$row['limitprice'] || $frameworkcontract=='yes'){
            return array(
                //'workflowsid'=>$this->purchaseWorkFlowSid,
                'workflowsid'=>$this->costWorkFlowSid,
                'ceocheck'=>0,
                'type'=>'purchase'
            );
        }
        return array(
            'workflowsid'=>$this->costWorkFlowSid,
            'ceocheck'=>$row['checkprice']>$total ?1 :0,
            'type'=>'cost'
        );
    }
}
