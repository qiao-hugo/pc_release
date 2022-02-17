<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Inventory Record Model Class
 */
class AccountPlatform_Record_Model extends Vtiger_Record_Model {
    /**
     * 验证用户ID和账号信息是不能重复
     * @param Vtiger_Request $request
     */
    public function checkIdAndAccountplatform(Vtiger_Request $request){
        $recordId=$request->get('record');
        //$accountplatform=$request->get('accountplatform');
        $idaccount=$request->get('idaccount');
        $effectivestartaccount=$request->get('effectivestartaccount');//账户有效开始日期
        $effectiveendaccount=$request->get('effectiveendaccount'); //账户有效结束日期
        global $adb;
        //$accountplatform=trim($accountplatform);
        $idaccount=trim($idaccount);
        $recordId=$recordId>0?$recordId:0;
        //$result=$adb->pquery("SELECT 1 FROM `vtiger_accountplatform` LEFT JOIN vtiger_crmentity ON vtiger_accountplatform.accountplatformid=vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0 AND vtiger_accountplatform.idaccount=?  AND vtiger_accountplatform.accountplatformid<>?",array($idaccount,$accountplatform,$recordId));
        /*$sql = "SELECT	1 FROM	`vtiger_accountplatform`
              LEFT JOIN vtiger_crmentity ON vtiger_accountplatform.accountplatformid = vtiger_crmentity.crmid
              WHERE vtiger_crmentity.deleted = 0  AND vtiger_accountplatform.idaccount = ? 
              AND ((effectivestartaccount BETWEEN '{$effectivestartaccount}' AND '{$effectiveendaccount}') 
                  OR (effectiveendaccount BETWEEN '{$effectivestartaccount}' AND '{$effectiveendaccount}')
                  OR ('{$effectiveendaccount}' BETWEEN effectivestartaccount AND effectiveendaccount))
              AND vtiger_accountplatform.accountplatformid <>?";*/

        $sql = "SELECT	1 FROM	`vtiger_accountplatform`
              LEFT JOIN vtiger_crmentity ON vtiger_accountplatform.accountplatformid = vtiger_crmentity.crmid
              WHERE vtiger_crmentity.deleted = 0  AND vtiger_accountplatform.idaccount = ? AND vtiger_accountplatform.accountplatformid <> ?";
        $result=$adb->pquery($sql,array($idaccount,$recordId));
        $res=false;
        if($adb->num_rows($result)){
            $res=true;
        }
        return $res;
    }

    /**
     * 更新账号审核状态
     * @param $recordid
     */
    public function updataModuleStatus($recordid){
        global $adb;
        $adb->pquery('UPDATE vtiger_accountplatform SET modulestatus=\'b_actioning\' WHERE accountplatformid=?',array($recordid));
    }
    public function doResubmit(Vtiger_Request $request){
        $recordid=$request->get('record');
        $params['recordId']=$recordid;
        $params['fieldName']='modulestatus';
        $params['oldValue']='c_complete';
        $params['module']='AccountPlatform';
        $params['Value']='a_normal';
        $this->setModTrack($params);
        global $adb;
        $adb->pquery('UPDATE vtiger_accountplatform SET modulestatus=\'a_normal\' WHERE accountplatformid=?',array($recordid));
        $adb->pquery('DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename=?',array($recordid,'AccountPlatform'));


    }
    public function setModTrack($params){
        $datetime=date('Y-m-d H:i:s');
        global $adb,$current_user;
        $id = $adb->getUniqueId('vtiger_modtracker_basic');
        $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
            array($id , $params['recordId'], $params['module'], $current_user->id, $datetime, 0));
        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
            Array($id, $params['fieldName'],$params['oldValue'], $params['Value']));
    }
    public function getVendorInfos(Vtiger_Request $request){
        $productid=$request->get('productid');
        $datetime=date('Y-m-d');
        global $adb;
        //$query="SELECT vtiger_vendorproduct.*,(SELECT vtiger_vendor.vendorname FROM vtiger_vendor WHERE vtiger_vendor.vendorid=vtiger_vendorproduct.vendorid) as vendorname FROM vtiger_vendorproduct LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_vendorproduct.vendorproductid WHERE vtiger_crmentity.deleted=0 AND vtiger_vendorproduct.modulestatus='c_complete' AND vtiger_vendorproduct.productid=?";
        $query="SELECT vtiger_vendorsrebate.*,vtiger_vendor.vendorname,IFNULL(vtiger_suppliercontracts.contract_no,'采购合同担保') AS contract_no FROM vtiger_vendorsrebate 
                    LEFT JOIN vtiger_vendor ON vtiger_vendor.vendorid=vtiger_vendorsrebate.vendorid 
                    LEFT JOIN vtiger_suppliercontracts ON vtiger_vendorsrebate.suppliercontractsid=vtiger_suppliercontracts.suppliercontractsid 
                    LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_vendorsrebate.suppliercontractsid
                    WHERE vtiger_crmentity.deleted=0 
                    AND vtiger_vendorsrebate.deleted=0
                    AND ((vtiger_suppliercontracts.modulestatus = 'c_complete' AND vtiger_suppliercontracts.effectivetime>='{$datetime}') OR (vtiger_suppliercontracts.isguarantee = 1 AND (vtiger_suppliercontracts.effectivetime IS NULL OR vtiger_suppliercontracts.effectivetime='' OR vtiger_suppliercontracts.effectivetime>='{$datetime}') AND vtiger_suppliercontracts.modulestatus IN('a_normal','b_check','b_actioning','c_stamp','c_recovered','c_receive')))
                    AND vtiger_vendorsrebate.productid=?
                    AND vtiger_vendorsrebate.enddate>=?";
        $result=$adb->pquery($query,array($productid,$datetime));
        $data=array();
        while($row=$adb->fetch_array($result)){
            $row['rebatetypename']=vtranslate($row['rebatetype'], 'ProductProvider');
            $data[]=$row;
        }

        return array('countnum'=>count($data),'data'=>$data);
    }
    /**
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     * 移动端调用
     */
    public function updataModuleStatusM(Vtiger_Request $request){
        $recordId=$request->get('record');
        $this->updataModuleStatus($recordId);
        return 0;
    }

    /**
     * @param Vtiger_Request $request
     * @return bool
     * @author: steel.liu
     * @Date:xxx
     * 移动端调用
     */
    public function checkIdAndAccountplatformM(Vtiger_Request $request){
        $flag=$this->checkIdAndAccountplatform($request);
        $return=array('success'=>false,'msg'=>"");
        if($flag){
            $return=array('success'=>true,'msg'=>"账号ID重复!");
        }
        return $return;
    }
    //获取所有的明细数据 cxh 2020-04-08
    public function getProductProvideDetail($id){
        global $adb;
        $result=$adb->run_query_allrecords("SELECT  *  FROM  vtiger_accountplatform_detail WHERE  accountplatformid=".$id);
        return $result;
    }
    //获取所有的明细数据 cxh 2020-04-08 移动端
    public function getAccountPlatformDetailM(Vtiger_Request $request){
        global $adb;
        $start= ($request->get('num')-1)*20;
        $end  = $request->get('num')*20;
        $id=$request->get('id');
        $idaccount=$request->get('idaccount');
        if($idaccount){
            $idaccount="(idaccount like '%".$idaccount."%' OR accountplatform like '%".$idaccount."%') AND ";
        }else{
            $idaccount='';
        }
        $result=$adb->run_query_allrecords("SELECT  *  FROM  vtiger_accountplatform_detail WHERE ".$idaccount." accountplatformid=".$id."  ORDER BY  accountplatform_detail_id DESC LIMIT  ".$start.",".$end);
        return array("success"=>1,'result'=>$result);
    }
    //删除一条数据
    public function deleteOneDetailM(Vtiger_Request $request){
        global $adb;
        $accountplatform_detail_id=$request->get("id");
        $record=$request->get("recordId");
        $sql=" DELETE FROM vtiger_accountplatform_detail WHERE accountplatform_detail_id=? AND accountplatformid=? ";
        $adb->pquery($sql,array($accountplatform_detail_id,$record));
        return array('success'=>1);
    }
    // 更新一条数据
    public function updateDetailOneM(Vtiger_Request $request){
        global $adb;
        $curren_time=date("Y-m-d");
        $accountplatform_detail_id=$request->get("id");
        $idaccount=$request->get("idaccount");
        $accountplatform=$request->get("accountplatform");
        $recordID=$request->get("recordId");
        $userid=$request->get("userid");
        $param['record']=$recordID;
        $param['module']='AccountPlatform';
        $param['userid']=$userid;
        $param['status']=0;
        $param['strArray']=array();
        $result=$adb->pquery("SELECT 1 FROM `vtiger_accountplatform` LEFT JOIN vtiger_crmentity ON vtiger_accountplatform.accountplatformid=vtiger_crmentity.crmid LEFT JOIN vtiger_accountplatform_detail ON vtiger_accountplatform_detail.accountplatformid=vtiger_accountplatform.accountplatformid WHERE vtiger_crmentity.deleted=0 AND vtiger_accountplatform_detail.idaccount=?  AND vtiger_accountplatform_detail.accountplatformid<>?",array($idaccount,$recordID));
        if ($adb->num_rows($result) > 0) {
            return array("success" =>0, "message" => "当前ID,账号重复不允许添加!!");
        }
        // 如果原数据id存在则 更新
        if ($accountplatform_detail_id) {
            $result=$adb->pquery("SELECT * FROM vtiger_accountplatform_detail WHERE idaccount=?  AND accountplatform_detail_id<>? limit 1",array($idaccount,$accountplatform_detail_id));
            if ($adb->num_rows($result) > 0) {
                return array("success" =>0, "message" => "当前ID,账号重复不允许添加!!");
            }
            $sql = "SELECT * FROM vtiger_accountplatform_detail WHERE  accountplatform_detail_id=? limit 1";
            $result = $adb->pquery($sql, array($accountplatform_detail_id));
            $result = $adb->query_result_rowdata($result, 0);
            if ($result['idaccount'] != $idaccount) {
                $param['strArray'][0]['fieldname'] = 'idaccount';
                $param['strArray'][0]['prevalue'] = $result['idaccount'];
                $param['strArray'][0]['postvalue'] = $idaccount;
            }
            if ($result['accountplatform'] != $accountplatform) {
                $param['strArray'][1]['fieldname'] = 'accountplatform';
                $param['strArray'][1]['prevalue'] = $result['accountplatform'];
                $param['strArray'][1]['postvalue'] = $accountplatform;
            }
            if (!empty($param['strArray'])) {
                $recordModel = Vtiger_Record_Model::getCleanInstance("ProductProvider");
                $recordModel->addLogs($param);
                $update = " UPDATE vtiger_accountplatform_detail SET idaccount=?,accountplatform=?,updatetime=? WHERE accountplatform_detail_id=? ";
                $adb->pquery($update, array($idaccount, $accountplatform, $curren_time, $accountplatform_detail_id));
            }
            //走插入
        } else {
            $result=$adb->pquery("SELECT * FROM vtiger_accountplatform_detail WHERE idaccount=? limit 1",array($idaccount));
            if ($adb->num_rows($result) > 0) {
                return array("success" =>0, "message" => "当前ID,账号重复不允许添加!!");
            }
            $param['strArray'][0]['fieldname']='idaccount';
            $param['strArray'][0]['prevalue']=null;
            $param['strArray'][0]['postvalue']=$idaccount;
            $param['strArray'][1]['fieldname']='accountplatform';
            $param['strArray'][1]['prevalue']=null;
            $param['strArray'][1]['postvalue']=$accountplatform;
            $recordModel= Vtiger_Record_Model::getCleanInstance("ProductProvider");
            $recordModel->addLogs($param);
            $insert = "INSERT INTO vtiger_accountplatform_detail (`accountplatformid`, `idaccount`, `accountplatform`, `createtime`, `updatetime`) values(?,?,?,?,?)";
            $adb->pquery($insert, array($recordID, $idaccount, $accountplatform, $curren_time, $curren_time));
        }

        return  array("success" =>1);

    }
}