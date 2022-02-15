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
class ProductProvider_Record_Model extends Vtiger_Record_Model {
    /**
     * 验证用户ID和账号信息是不能重复
     * @param Vtiger_Request $request
     */
    public function checkIdAndProductProvider(Vtiger_Request $request){
        $recordId=$request->get('record');
        $productid=$request->get('productid');
        $vendorid=$request->get('vendorid');
        $idaccount=$request->get('idaccount');
        global $adb;
        $recordId=$recordId>0?$recordId:0;
        //$result=$adb->pquery("SELECT 1 FROM `vtiger_productprovider` LEFT JOIN vtiger_crmentity ON vtiger_productprovider.productproviderid=vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0 AND vtiger_productprovider.productid=? AND vtiger_productprovider.vendorid=? AND vtiger_productprovider.productproviderid<>?",array($productid,$vendorid,$recordId));
        $result=$adb->pquery("SELECT 1 FROM `vtiger_productprovider` LEFT JOIN vtiger_crmentity ON vtiger_productprovider.productproviderid=vtiger_crmentity.crmid LEFT JOIN  WHERE vtiger_crmentity.deleted=0 AND vtiger_productprovider.idaccount=?  AND vtiger_productprovider.productproviderid<>?",array($idaccount,$recordId));
        $res=false;
        if($adb->num_rows($result)){
            $res=true;
        }
        return $res;
    }
    //获取所有的明细数据 cxh 2020-04-08
    public function getProductProvideDetail($id){
        global $adb;
        $result=$adb->run_query_allrecords("SELECT  *  FROM  vtiger_productprovider_detail WHERE  productproviderid=".$id);
        return $result;
    }
    /**
     * 更新账号审核状态
     * @param $recordid
     */
    public function updataModuleStatus($recordid){
        global $adb;
        $adb->pquery('UPDATE vtiger_productprovider SET modulestatus=\'b_actioning\' WHERE productproviderid=?',array($recordid));
    }
    public function doResubmit(Vtiger_Request $request){
        $recordid=$request->get('record');
        $params['recordId']=$recordid;
        $params['fieldName']='modulestatus';
        $params['oldValue']='c_complete';
        $params['module']='ProductProvider';
        $params['Value']='a_normal';
        $this->setModTrack($params);
        global $adb;
        $adb->pquery('UPDATE vtiger_productprovider SET modulestatus=\'a_normal\' WHERE productproviderid=?',array($recordid));
        $adb->pquery('DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename=?',array($recordid,'ProductProvider'));


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
        $dateTime=date("Y-m-d");
        global $adb;
        //$query="SELECT vtiger_vendorproduct.*,vtiger_vendor.vendorname,vtiger_suppliercontracts.suppliercontractsid,vtiger_suppliercontracts.contract_no FROM vtiger_vendorproduct LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_vendorproduct.vendorproductid LEFT JOIN vtiger_vendor ON vtiger_vendor.vendorid=vtiger_vendorproduct.vendorid LEFT JOIN vtiger_suppliercontracts ON vtiger_suppliercontracts.vendorid=vtiger_vendorproduct.vendorid WHERE vtiger_crmentity.deleted=0 AND vtiger_vendorproduct.modulestatus='c_complete' AND (vtiger_suppliercontracts.modulestatus='c_complete' OR vtiger_suppliercontracts.isguarantee=1) AND vtiger_vendorproduct.productid=?";
        $query="SELECT vtiger_vendorsrebate.*,vtiger_vendor.vendorname,IFNULL(vtiger_suppliercontracts.contract_no,'采购合同担保') AS contract_no FROM vtiger_vendorsrebate 
                    LEFT JOIN vtiger_vendor ON vtiger_vendor.vendorid=vtiger_vendorsrebate.vendorid 
                    LEFT JOIN vtiger_suppliercontracts ON vtiger_vendorsrebate.suppliercontractsid=vtiger_suppliercontracts.suppliercontractsid 
                    LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_vendorsrebate.suppliercontractsid
                    WHERE vtiger_crmentity.deleted=0 
                    AND vtiger_vendorsrebate.deleted=0
                    AND ((vtiger_suppliercontracts.modulestatus = 'c_complete' AND vtiger_suppliercontracts.effectivetime>='{$dateTime}') OR (vtiger_suppliercontracts.isguarantee = 1 AND (vtiger_suppliercontracts.effectivetime IS NULL OR vtiger_suppliercontracts.effectivetime='' OR vtiger_suppliercontracts.effectivetime>='{$dateTime}') AND vtiger_suppliercontracts.modulestatus IN('a_normal','b_check','b_actioning','c_stamp','c_recovered','c_receive')))
                    AND vtiger_vendorsrebate.productid=?
                    AND vtiger_vendorsrebate.enddate>=?";
        $result=$adb->pquery($query,array($productid,$dateTime));
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
    public function checkIdAndProductProviderM(Vtiger_Request $request){
        $flag=$this->checkIdAndProductProvider($request);
        $return=array('success'=>false,'msg'=>"");
        if($flag){
            $return=array('success'=>true,'msg'=>"账号ID重复!");
        }
        return $return;
    }
    // 添加字段日志记录
    public function addLogs($params){
        global $adb;
        $id = $adb->getUniqueId('vtiger_modtracker_basic');
        $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
            array($id, $params['record'],$params['module'], $params['userid'], date('Y-m-d H:i:s'), $params['status']));
        $str='';
        foreach ($params['strArray'] as $key=>$value){
            $str.="(".$id.",'".$value['fieldname']."','".$value['prevalue']."','".$value['postvalue']."'),";
        }
        $str=trim($str,",");
        if($str){
            $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES'.$str, Array());
        }
    }

    //获取所有的明细数据 cxh 2020-04-08 移动端
    public function getProductProvideDetailM(Vtiger_Request $request){
        global $adb;
        $start= ($request->get('num')-1)*20;
        $end  = $request->get('num')*20;
        $id=$request->get('id');
        $idaccount=$request->get('idaccount');
        if($idaccount){
            $idaccount=" (idaccount like '%".$idaccount."%' OR accountzh like '%".$idaccount."%') AND ";
        }else{
            $idaccount='';
        }
        $result=$adb->run_query_allrecords("SELECT  *  FROM  vtiger_productprovider_detail WHERE ".$idaccount." productproviderid=".$id."  ORDER BY  productprovide_detail_id  DESC  LIMIT  ".$start.",".$end);
        return array("success"=>1,'result'=>$result);
    }
    //删除一条数据
    public function deleteOneDetailM(Vtiger_Request $request){
        global $adb;
        $productprovide_detail_id=$request->get("id");
        $record=$request->get("recordId");
        $sql=" DELETE FROM vtiger_productprovider_detail WHERE productprovide_detail_id=? AND productproviderid=? ";
        $adb->pquery($sql,array($productprovide_detail_id,$record));
        return array('success'=>1);
    }
    // 更新一条数据
    public function updateDetailOneM(Vtiger_Request $request){
        global $adb;
        $curren_time=date("Y-m-d");
        $productprovide_detail_id=$request->get("id");
        $idaccount=$request->get("idaccount");
        $accountzh=$request->get("accountzh");
        $recordID=$request->get("recordId");
        $userid=$request->get("userid");
        $param['record']=$recordID;
        $param['module']='AccountPlatform';
        $param['userid']=$userid;
        $param['status']=0;
        $param['strArray']=array();
        $result=$adb->pquery("SELECT 1 FROM `vtiger_productprovider` LEFT JOIN vtiger_crmentity ON vtiger_productprovider.productproviderid=vtiger_crmentity.crmid LEFT JOIN vtiger_productprovider_detail ON vtiger_productprovider_detail.productproviderid=vtiger_productprovider.productproviderid WHERE vtiger_crmentity.deleted=0 AND vtiger_productprovider_detail.idaccount=?  AND vtiger_productprovider_detail.productproviderid<>?",array($idaccount,$recordID));
        if ($adb->num_rows($result) > 0) {
            return array("success" =>0, "message" => "当前ID,账号重复不允许添加!!");
        }
        // 如果原数据id存在则 更新
        if ($productprovide_detail_id) {
            $result=$adb->pquery("SELECT * FROM vtiger_productprovider_detail WHERE idaccount=?  AND productprovide_detail_id<>? limit 1",array($idaccount,$productprovide_detail_id));
            if ($adb->num_rows($result) > 0) {
                return array("success" =>0, "message" => "当前ID,账号重复不允许添加!!");
            }
            $sql = "SELECT * FROM vtiger_productprovider_detail WHERE  productprovide_detail_id=? limit 1";
            $result = $adb->pquery($sql, array($productprovide_detail_id));
            $result = $adb->query_result_rowdata($result, 0);
            if ($result['idaccount'] != $idaccount) {
                $param['strArray'][0]['fieldname'] = 'idaccount';
                $param['strArray'][0]['prevalue'] = $result['idaccount'];
                $param['strArray'][0]['postvalue'] = $idaccount;
            }
            if ($result['accountzh'] != $accountzh) {
                $param['strArray'][1]['fieldname'] = 'accountzh';
                $param['strArray'][1]['prevalue'] = $result['accountzh'];
                $param['strArray'][1]['postvalue'] = $accountzh;
            }
            if (!empty($param['strArray'])) {
                $recordModel = Vtiger_Record_Model::getCleanInstance("ProductProvider");
                $recordModel->addLogs($param);
                $update = " UPDATE vtiger_productprovider_detail SET idaccount=?,accountzh=?,updatetime=? WHERE productprovide_detail_id=? ";
                $adb->pquery($update, array($idaccount, $accountzh, $curren_time, $productprovide_detail_id));
            }
            //走插入
        } else {
            $result=$adb->pquery("SELECT * FROM vtiger_productprovider_detail WHERE idaccount=? limit 1",array($idaccount));
            if ($adb->num_rows($result) > 0) {
                return array("success" =>0, "message" => "当前ID,账号重复不允许添加!!");
            }
            $param['strArray'][0]['fieldname']='idaccount';
            $param['strArray'][0]['prevalue']=null;
            $param['strArray'][0]['postvalue']=$idaccount;
            $param['strArray'][1]['fieldname']='accountzh';
            $param['strArray'][1]['prevalue']=null;
            $param['strArray'][1]['postvalue']=$accountzh;
            $recordModel= Vtiger_Record_Model::getCleanInstance("ProductProvider");
            $recordModel->addLogs($param);
            $insert = "INSERT INTO vtiger_productprovider_detail (`productproviderid`, `idaccount`, `accountzh`, `createtime`, `updatetime`) values(?,?,?,?,?)";
            $adb->pquery($insert, array($recordID, $idaccount, $accountzh, $curren_time, $curren_time));
        }

        return  array("success" =>1);

    }
}