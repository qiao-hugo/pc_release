<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/


include_once('modules\Workflows\models\Modulestatus.php');
class SupplierContracts_Record_Model extends Vtiger_Record_Model
{
    //public $workflowid=361027;
    /*
     * 详情页面显示产品明细
     * */
    static function getProductsById($record)
    {
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select *,IF(vtiger_salesorderproductsrel.standard IS NULL,'默认规格',(SELECT vtiger_products_standard.standardname FROM 	vtiger_products_standard WHERE vtiger_products_standard.standardid = vtiger_salesorderproductsrel.standard)) AS standard,IF(vtiger_salesorderproductsrel.isextra=0,'否','是') AS isextra,IF(vtiger_salesorderproductsrel.standard IS NULL,vtiger_products.realprice,vtiger_products_standard.realprice) AS realprice,IF(vtiger_salesorderproductsrel.standard IS NULL,vtiger_products.unit_price,vtiger_products_standard.singleprice) AS unit_price,IFNULL((SELECT vtiger_products.productname FROM vtiger_products WHERE vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid),'--') AS thepackage,vtiger_products.productid,IF( productcomboid is NULL or productcomboid=0,vtiger_salesorderproductsrel.productid,productcomboid) as tagid from vtiger_salesorderproductsrel left join vtiger_products on  vtiger_products.productid=vtiger_salesorderproductsrel.productid LEFT JOIN vtiger_products_standard ON vtiger_products_standard.standardid=vtiger_salesorderproductsrel.standard where servicecontractsid=? AND (multistatus=0 OR multistatus = 1) ORDER BY tagid", array($record));
        $rows = $db->num_rows($result);
        $product = array();
        if ($rows) {
            for ($i = 0; $i < $rows; ++$i) {
                $product[] = $db->fetchByAssoc($result);
            }
            return $product;
        }
        return false;
    }

    /**
     * 2015年4月22日 星期三 获取 货币符号
     * @param $recordId 合同id
     * @return string 返回货币类型，默认为人民币
     */
    public function getcurrencytype($recordId)
    {
        $data = "人民币";
        if (!empty($recordId)) {
            $db = PearDatabase::getInstance();
            $sql = 'SELECT currencytype FROM `vtiger_servicecontracts` WHERE servicecontractsid=?';
            $currencytype = $db->pquery($sql, array($recordId));
            if ($db->num_rows($currencytype) > 0) {
                $data = $currencytype->fields['currencytype'];
            }
        }
        return $data;
    }

    /*
     * 获取产品名称
     */
    static function getProductsId($recordId)
    {
        $db = PearDatabase::getInstance();
        if (!empty($recordId)) {
            $sql = '  SELECT relproductid FROM `vtiger_contractsproductsrel` WHERE vtiger_contractsproductsrel.contract_type =(
                      SELECT vtiger_contract_type.contract_typeid FROM `vtiger_contract_type` WHERE vtiger_contract_type.contract_type=(
                      SELECT vtiger_servicecontracts.contract_type FROM vtiger_servicecontracts WHERE vtiger_servicecontracts.servicecontractsid = ?))';
            $result = $db->pquery($sql, array($recordId));
            $relproductid =  $db->query_result($result, 'relproductid');
            $productid=explode(' |##| ', $relproductid);
            foreach($productid as $value){

                $product_result = $db->pquery(" SELECT productid,productname FROM `vtiger_products` WHERE productid=".$value."");

                for ($i=0; $i<$db->num_rows($product_result); ++$i) {
                    $productname = $db->fetchByAssoc($product_result);
                    $products[]=$productname;
                }

            }
//            $sql = 'select vtiger_products.productname,vtiger_products.productid,vtiger_salesorderproductsrel.salesorderproductsrelid,vtiger_salesorderproductsrel.servicecontractsid from vtiger_salesorderproductsrel left join vtiger_products on  vtiger_products.productid=vtiger_salesorderproductsrel.productid  where servicecontractsid=?';
//            $result = $db->pquery($sql, array($recordId));
//            for ($i=0; $i<$db->num_rows($result); ++$i) {
//                $product = $db->fetchByAssoc($result);
//                $products[]=$product;
//            }
//            for($i=0; $i<$db->num_rows($result); $i++) {
//                $salesorderproductsrelid = $db->query_result($result, $i, 'salesorderproductsrelid');
//                $servicecontractsid = $db->query_result($result, $i, 'servicecontractsid');
//                $productid = $db->query_result($result, $i, 'productid');
//                $productname = $db->query_result($result, $i, 'productname');
//            }
        }
        return $products;
    }
    /*
     * //获取当前的合同类型对应产品名称
     *
     */
    static function getContractType($recordId)
    {
        $db = PearDatabase::getInstance();
        if (!empty($recordId)) {
            $sql = 'SELECT productid FROM vtiger_servicecontracts WHERE servicecontractsid=?';
            $result = $db->pquery($sql, array($recordId));
            $productid =  $db->query_result($result, 'productid');
            $productid=explode(',', $productid);
            /*$sql = '  SELECT relproductid FROM `vtiger_contractsproductsrel` WHERE vtiger_contractsproductsrel.contract_type =(
                      SELECT vtiger_contract_type.contract_typeid FROM `vtiger_contract_type` WHERE vtiger_contract_type.contract_type=(
                      SELECT vtiger_servicecontracts.contract_type FROM vtiger_servicecontracts WHERE vtiger_servicecontracts.servicecontractsid = ?))';
            $result = $db->pquery($sql, array($recordId));
            $relproductid =  $db->query_result($result, 'relproductid');
            $productid=explode(' |##| ', $relproductid);
            foreach($productid as $value){

                $product_result = $db->pquery(" SELECT productid,productname FROM `vtiger_products` WHERE productid=".$value."");

                for ($i=0; $i<$db->num_rows($product_result); ++$i) {
                    $productname = $db->fetchByAssoc($product_result);
                    $product_namelist[]=$productname;
                }
                //chnap
            }*/
        }
        return $productid;
    }

    /**
     *合同关联的产品联动
     */
    static function productcategory($record){
        $db = PearDatabase::getInstance();
        //取得合同类型的第一个联动框的内容列表

        $query = 'SELECT * FROM vtiger_parent_contracttype ';
        $result['parent'] = $db->run_query_allrecords($query);
        //第一个联动框已经选中的项
        $nparentcontracttypeid=0;
        if($record>0){
            $query = 'SELECT parent_contracttypeid FROM vtiger_servicecontracts WHERE servicecontractsid=? limit 1';
            $data=$db->pquery($query,array($record));
            $nparentcontracttypeid=$db->query_result($data,0,'parent_contracttypeid');
            //是否为新建给个1
            $result['nparentid']=$nparentcontracttypeid;
        }


        $nparentcontracttypeid=$nparentcontracttypeid>0?$nparentcontracttypeid:1;
        //取得第二个框中的内容列表
        $query = 'SELECT vtiger_contract_type.contract_type FROM vtiger_parent_contracttype_contracttyprel JOIN vtiger_contract_type ON vtiger_contract_type.contract_typeid=vtiger_parent_contracttype_contracttyprel.contract_typeid WHERE  vtiger_parent_contracttype_contracttyprel.parent_contracttypeid='.$nparentcontracttypeid;
        $arrrecords = $db->run_query_allrecords($query);
        if(!empty($arrrecords)){
            $arrlist=array();
            foreach($arrrecords as $value){
                $arrlist[]=$value['contract_type'];
            }
            $result['ischild']=$arrlist;
        }
        return $result;
    }

    /**
     * 额外产品下列项
     * @param $recordId
     * @return array
     */
    static public function getextraproduct($recordId){
        $db = PearDatabase::getInstance();
        //if (!empty($recordId)) {
            //$query = "SELECT productid,productname FROM vtiger_products WHERE productcategory=(SELECT vtiger_servicecontracts.productcategory FROM vtiger_servicecontracts WHERE servicecontractsid={$recordId})";
            $query = "SELECT productid,productname FROM vtiger_products WHERE customer=1";
            //$query = "SELECT productid,productname FROM vtiger_products WHERE productid in(361935)";
            return $db->run_query_allrecords($query);

        //}
    }

    /**
     * 合同状态改变来触发提醒
     * @param string $status
     * @param array() $newarr
     */
    static public function setSalesorderandAlert($status,$newarr=array(),$id=0){
        return;
        $db=PearDatabase::getInstance();
        $contractarr=array('contract_no'=>$_REQUEST['contract_no'],'Receiveid'=>$_REQUEST['Receiveid'],'sc_related_to'=>$_REQUEST['sc_related_to'],'assigned_user_id'=>$_REQUEST['assigned_user_id']);
        //$newarr 是通过saveajax传过来的值下面做一个判断是否是savaajax传过来的值
        $contractarr=empty($newarr)?$contractarr:$newarr;
        switch($status){
            case Workflows_Module_Model::$moudulestatus['c_complete']:
                //echo  'c_complete';
                $arralert=array('合同单号:【'.$contractarr['contract_no'].'】已经完成,工单已经生成','合同单号:【'.$contractarr['contract_no'].'】跟进工单',$contractarr['Receiveid'],$contractarr['Receiveid'],$contractarr['sc_related_to']);
                //指定的产品生成工单
                if(self::createIsWorkflows($_REQUEST['productid'])){
                    self::createSaleorder($id);
                    JobAlerts_Record_Model::saveAlert($arralert);
                }

                break;
            case Workflows_Module_Model::$moudulestatus['c_contract_n_account']:
                $arralert=array('合同单号:【'.$contractarr['contract_no'].'】已交回合同,款未到账,请跟进','合同单号:【'.$contractarr['contract_no'].'】跟进到账',$contractarr['assigned_user_id'],$contractarr['assigned_user_id'],$contractarr['sc_related_to']);

                JobAlerts_Record_Model::saveAlert($arralert);
                break;
            case Workflows_Module_Model::$moudulestatus['c_account_n_contract']:
                $arralert=array('合同单号:【'.$contractarr['contract_no'].'】已收到回款,合同尚未交还,请及时交还合同','合同单号:【'.$contractarr['contract_no'].'】跟进合同',$contractarr['assigned_user_id'],$contractarr['assigned_user_id'],$contractarr['sc_related_to']);

                JobAlerts_Record_Model::saveAlert($arralert);
                break;
            default:
        }
    }



    /**
     *合同生成工单
     */
    private function createSaleorder($id){
	 return;
        //作废
        $db=PearDatabase::getInstance();
        $result=$db->run_query_allrecords("SELECT servicecontractsid,concat(accountname,'的合同工单') as accountename,total,receiveid,accountid,account_no FROM vtiger_servicecontracts LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_servicecontracts.sc_related_to WHERE servicecontractsid={$id} LIMIT 1");
        unset($_REQUEST);//删掉$_REQUEST该 数据影响下面的工单生成数据
        $_REQUES['record']='';
        //$_REQUEST['record_id']='';
        //$_REQUEST['workflowsid']=361027;

        $request=new Vtiger_Request($_REQUES, $_REQUES);
        $request->set('subject',$result[0]['accountename']);
        $request->set('servicecontractsid',$result[0]['servicecontractsid']);
        $request->set('customerno',$result[0]['account_no']);
        $request->set('assigned_user_id',$result[0]['receiveid']);
        //根据回款和成本之间来确定是否是回款不足
        if(self::receivedayprice($id)) {
            $request->set('modulestatus', 'a_normal');
        }else{
            //回款不足
            $request->set('modulestatus', 'c_lackpayment');
        }
        $request->set('account_id',$result[0]['accountid']);//
        $request->set('workflowsid',self::selectWorkfows());
        $request->set('salescommission',$result[0]['total']);
        $request->set('issubmit',1);
        $request->set('module','SalesOrder');
        $request->set('view','Edit');
        $request->set('action','Save');
        $ressorder=new SalesOrder_Save_Action();

        $ressorder->saveRecord($request);
        //$crmid=$db->getUniqueID('vtiger_crmentity');求表ID当前最大的
        //求生成后对应工单的ID
        $salesorderid=self::getSalesorderid($id);
        $db->pquery("INSERT INTO vtiger_salesorder_productdetail SELECT ?,relateid,'',formid FROM `vtiger_customer_modulefields` WHERE relatedmodule='Products' AND relateid in(SELECT  vtiger_salesorderproductsrel.productid FROM vtiger_salesorderproductsrel WHERE servicecontractsid=?)",array($salesorderid,$id));

        self::contractsMakeWorkflows($salesorderid,$result[0]['servicecontractsid']);//生成工单对应的工作流
        //看一下是回款总额是否大于成本总和.如果大于则生成工作流
        if(self::receiveDayprice($id)){
            //第一个节点自动审核
            self::setWorkflowNode($salesorderid);
            //首款的自动审核
            //self::setWorkflowNodeFirst($salesorderid);
        }
        if(self::receiveDayprice($id,2)){
            //尾款的自动审核
            self::setWorkflowNodeFirst($salesorderid,'last_payment');
        }
        //生成工流

    }

    /**
     * 合同对应成本之和和回款总和之间比较备份
     * @param $contractid 合同的ID号
     * @return bool
     * @throws Exception
     */
    static public function receiveDaypricebak($contractid,$checkcount=1){
        $db=PearDatabase::getInstance();
        $query="SELECT sum(vtiger_receivedpayments.unit_price) AS sumtotal FROM `vtiger_receivedpayments` WHERE relatetoid =?";
        if($checkcount==1){
            //$query.=" AND isdownpayment!=1";订金
        }
         $results=$db->pquery($query,array($contractid));
        $result=$db->query_result($results,0,'sumtotal');//所有回款的之合
        if($checkcount==1) {
            $query = "SELECT sum(IFNULL(vtiger_salesorderproductsrel.costing,0)+IFNULL(vtiger_salesorderproductsrel.purchasemount,0)) AS realprice,vtiger_salesorderproductsrel.salesorderid,vtiger_servicecontracts.receiveid FROM vtiger_salesorderproductsrel LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_salesorderproductsrel.servicecontractsid WHERE vtiger_salesorderproductsrel.servicecontractsid =?";//回款总合
        }else{
            $query = "SELECT total AS realprice,0 AS salesorderid,receiveid FROM vtiger_servicecontracts WHERE servicecontractsid= ?";//求出合同价
        }
        $realprices=$db->pquery($query,array($contractid));
        $realprice=$db->query_result($realprices,0,'realprice');//所有产品的成本之合数量*年限*成本单价
        $salesorderid=$db->query_result($realprices,0,'salesorderid');//生工单的id
        //$receiveid=$db->query_result($realprices,0,'receiveid');//合同的提单人

        /*if($receiveid>0){
            $query='SELECT IFNULL(sum(total),0) as totals FROM vtiger_guarantee WHERE deleted=0 AND userid=? AND contractid!=?';
            $guarantee=$db->pquery($query,array($receiveid,$contractid));
            $guaranteetal=$db->query_result($guarantee,0,'totals');//商务已经担保总的担保金额
        }*/
        $datetime=date('Y-m-d H:i:s');
        if($result>=$realprice && $realprice>=0){
            //回款大于成本
            if($salesorderid>0){
                $sql="UPDATE vtiger_salesorder SET vtiger_salesorder.guaranteetotal=0 WHERE vtiger_salesorder.salesorderid=?";
                $db->pquery($sql,array($salesorderid));
                $sql="UPDATE `vtiger_guarantee` SET vtiger_guarantee.deleted=1,delta=total,deltatime='{$datetime}' WHERE vtiger_guarantee.contractid=? AND vtiger_guarantee.salesorderid=?";
                $db->pquery($sql,array($contractid,$salesorderid));
            }
            return true;
        }elseif($salesorderid>0){
            //看一下有没有回款没有回款直接退出不向下走
            $Guaranteesalesorderguarante=Guarantee_Record_Model::getGuarantecurrent($salesorderid);//对应工单已担保的总成本
            if($Guaranteesalesorderguarante==0){
                //没有直担保直接返回false;
                return false;
            }
            $Guaranteereceiveprice=Guarantee_Record_Model::getreceivedayprice($contractid);//对应回款的总金额
            $Guaranteerealprice=Guarantee_Record_Model::getrealprice($salesorderid);//对应的总成本
            $temptotal=$Guaranteereceiveprice+$Guaranteesalesorderguarante-$Guaranteerealprice;
            if($temptotal==0){
                //担保金额+回款正好等于成本时不用更新担保直接走工作流
                return true;
            }elseif($temptotal<0){
                //担保金额+回款小于成本时后面无行走直接退出
                return false;
            }elseif($temptotal>0){
                //回款比较足可以用来冲掉部分担保
                $query="SELECT  vtiger_guarantee.guaranteeid,vtiger_guarantee.userid,vtiger_guarantee.contractid, vtiger_guarantee.salesorderid,vtiger_guarantee.total,vtiger_guarantee.presence,vtiger_guarantee.guaranteeid,vtiger_guarantee.createdtime FROM vtiger_guarantee  LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_guarantee.contractid  LEFT JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid=vtiger_guarantee.salesorderid  WHERE 1=1 AND deleted=0 AND vtiger_guarantee.salesorderid={$salesorderid} ORDER BY vtiger_guarantee.presence ASC,vtiger_guarantee.guaranteeid ASC";
                $resultddddd=$db->run_query_allrecords($query);
                $guaranteeids='';
                $insertid='';
                if(!empty($resultddddd)){
                    foreach($resultddddd as $value){
                        $newmoney=$temptotal-$value['total'];
                        if($newmoney>=0){
                            $temptotal=$newmoney;
                            $guaranteeids.=$value['guaranteeid'].',';
                            if($newmoney==0){
                                break;
                            }
                        }else{
                            $insertid=$value['guaranteeid'];
                            $inserttotal=$value['total']-$temptotal;
                            $newresult=$value;
                            break;
                        }
                    }
                    if(!empty($guaranteeids)){
                        $guaranteeids=rtrim($guaranteeids,',');
                        $query="UPDATE `vtiger_guarantee` SET vtiger_guarantee.deleted=1,delta=total,deltatime=? WHERE vtiger_guarantee.guaranteeid in({$guaranteeids})";
                        $db->pquery($query,array($datetime));
                    }
                    if($insertid>0){
                        $db->pquery("UPDATE `vtiger_guarantee` SET vtiger_guarantee.deleted=1,delta=?,deltatime=? WHERE vtiger_guarantee.guaranteeid=?",array($temptotal,$datetime,$insertid));
                        $db->pquery("INSERT INTO vtiger_guarantee(userid,contractid,salesorderid,total,presence,createdtime) VALUES(?,?,?,?,?,?)",array($newresult['userid'],$newresult['contractid'],$newresult['salesorderid'],$inserttotal,$newresult['presence'],$newresult['createdtime']));
                    }
                    $Guaranteesalesorderguarante=Guarantee_Record_Model::getGuarantecurrent($salesorderid);;//对应回款的总金额
                    Guarantee_Record_Model::updatesalesordertotal($Guaranteesalesorderguarante,$salesorderid);//更新对应工单的担保金额
                }

                return true;
            }
        }

        return false;
    }
/**
     * 合同对应成本之和和回款总和之间比较
     * @param $contractid 合同的ID号
     * @return bool
     * @throws Exception
     */
    static public function receiveDayprice($contractid,$salesorderid){
        $db=PearDatabase::getInstance();
        //对应工单的总成本
        $query = "SELECT sum(IFNULL(vtiger_salesorderproductsrel.costing,0)+IFNULL(vtiger_salesorderproductsrel.purchasemount,0)) AS realprice,vtiger_salesorder.alreadycalculate,vtiger_salesorder.guaranteetotal FROM vtiger_salesorderproductsrel LEFT JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid = vtiger_salesorderproductsrel.salesorderid WHERE vtiger_salesorderproductsrel.salesorderid =? AND vtiger_salesorderproductsrel.multistatus in(0,3)";
        $realprices=$db->pquery($query,array($salesorderid));
        $salesorderprice=$db->query_result($realprices,0,'realprice');//当前对应工单的总成本
        $salesordeflag=$db->query_result($realprices,0,'alreadycalculate');//当前工单是否已经有计算成本
        $salesordeguaranteetotal=$db->query_result($realprices,0,'guaranteetotal');//当前对应工单的担保金
        if(1==$salesordeflag && $salesordeguaranteetotal==0){
            //工单已计算
            return true;
        }
        $query="SELECT sum(vtiger_receivedpayments.unit_price) AS sumtotal FROM `vtiger_receivedpayments` WHERE relatetoid =?";
        //对应合同的总回款数
        $results=$db->pquery($query,array($contractid));
        $receivedpaymentsprice=$db->query_result($results,0,'sumtotal');//所有回款的之合
        //对应合同的所有工单总成本
        $query = "SELECT sum(IFNULL(vtiger_salesorderproductsrel.costing,0)+IFNULL(vtiger_salesorderproductsrel.purchasemount,0)) AS realprice FROM vtiger_salesorderproductsrel LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_salesorderproductsrel.servicecontractsid WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND vtiger_salesorderproductsrel.multistatus in(0,3)";
        $realprices=$db->pquery($query,array($contractid));
        $allrealprice=$db->query_result($realprices,0,'realprice');//对应合同的所有工单总成本
        //对应已计算工单的总成本
        $query = "SELECT IFNULL(sum(IFNULL(vtiger_salesorderproductsrel.costing,0)+IFNULL(vtiger_salesorderproductsrel.purchasemount,0)),0) AS realprice FROM vtiger_salesorderproductsrel LEFT JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid = vtiger_salesorderproductsrel.salesorderid WHERE vtiger_salesorderproductsrel.servicecontractsid=? AND vtiger_salesorderproductsrel.salesorderid!=? AND vtiger_salesorder.alreadycalculate=1 AND vtiger_salesorderproductsrel.multistatus in(0,3)";
        $realprices=$db->pquery($query,array($contractid,$salesorderid));
        $salesordealreadycalculate=$db->query_result($realprices,0,'realprice');//对应已计算工单的总成本
        $datetime=date('Y-m-d H:i:s');
        $effectiveamount=$receivedpaymentsprice-$salesordealreadycalculate;//总回款-已经计算过的总成本=可用的回款
        //1:总回款大于总成本直接走
        //2:总回款-当前已经计算过回款的工单大于当前的工单成本直接走
        //3:查担保是否满足条件
        /*echo $receivedpaymentsprice,"receivedpaymentsprice<hr>";
        echo $allrealprice,"allrealprice<hr>";
        echo $effectiveamount,"effectiveamount<hr>";
        echo $salesorderprice,"salesorderprice<hr>";*/

        if($receivedpaymentsprice>=$allrealprice||$effectiveamount>=$salesorderprice){
            //回款大于成本
            $sql="UPDATE vtiger_salesorder SET vtiger_salesorder.guaranteetotal=0,alreadycalculate=1,occupancyamount=? WHERE vtiger_salesorder.salesorderid=?";
            $db->pquery($sql,array($salesorderprice,$salesorderid));
            $sql="UPDATE `vtiger_guarantee` SET vtiger_guarantee.deleted=1,delta=total,deltatime='{$datetime}' WHERE vtiger_guarantee.salesorderid=?";
            $db->pquery($sql,array($salesorderid));
            return true;
        }else{
            //看一下有没有回款,没有回款直接退出不向下走
            $Guaranteesalesorderguarante=Guarantee_Record_Model::getGuarantecurrent($salesorderid);//对应工单已担保的总成本
            //echo $Guaranteesalesorderguarante,"<hr>";
            if($Guaranteesalesorderguarante==0){
                //没有直担保直接返回false;
                return false;
            }
            $query = " SELECT sum(vtiger_salesorder.occupancyamount) AS occupancyamount FROM `vtiger_salesorder` WHERE servicecontractsid=? AND salesorderid!=?";
            $realprices=$db->pquery($query,array($contractid,$salesorderid));
            $occupancyamount=$db->query_result($realprices,0,'occupancyamount');//对应已计算工单的占用的回款
            //可用的有效回款+对应工单的担保-对应工单的成本
            $temptotal=$receivedpaymentsprice+$Guaranteesalesorderguarante-$salesorderprice-$occupancyamount;
            $tempoccupancyamount=$temptotal;
            if($temptotal==0){
                //担保金额+回款正好等于成本时不用更新担保直接走工作流
                return true;
            }elseif($temptotal<0){
                //担保金额+回款小于成本时后面无行走直接退出
                return false;
            }elseif($temptotal>0){
                //回款比较足可以用来冲掉部分担保
                $query="SELECT  vtiger_guarantee.guaranteeid,vtiger_guarantee.userid,vtiger_guarantee.contractid, vtiger_guarantee.salesorderid,vtiger_guarantee.total,vtiger_guarantee.presence,vtiger_guarantee.guaranteeid,vtiger_guarantee.createdtime FROM vtiger_guarantee  LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_guarantee.contractid  LEFT JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid=vtiger_guarantee.salesorderid  WHERE 1=1 AND deleted=0 AND vtiger_guarantee.salesorderid={$salesorderid} ORDER BY vtiger_guarantee.presence ASC,vtiger_guarantee.guaranteeid ASC";
                $resultddddd=$db->run_query_allrecords($query);
                $guaranteeids='';
                $insertid='';
                if(!empty($resultddddd)){
                    foreach($resultddddd as $value){
                        $newmoney=$temptotal-$value['total'];
                        if($newmoney>=0){
                            $temptotal=$newmoney;
                            $guaranteeids.=$value['guaranteeid'].',';
                            if($newmoney==0){
                                break;
                            }
                        }else{
                            $insertid=$value['guaranteeid'];
                            $inserttotal=$value['total']-$temptotal;
                            $newresult=$value;
                            break;
                        }
                    }
                    if(!empty($guaranteeids)){
                        $guaranteeids=rtrim($guaranteeids,',');
                        $query="UPDATE `vtiger_guarantee` SET vtiger_guarantee.deleted=1,delta=total,deltatime=? WHERE vtiger_guarantee.guaranteeid in({$guaranteeids})";
                        $db->pquery($query,array($datetime));
                    }
                    if($insertid>0){
                        $db->pquery("UPDATE `vtiger_guarantee` SET vtiger_guarantee.deleted=1,delta=?,deltatime=? WHERE vtiger_guarantee.guaranteeid=?",array($temptotal,$datetime,$insertid));
                        $db->pquery("INSERT INTO vtiger_guarantee(userid,contractid,salesorderid,total,presence,createdtime) VALUES(?,?,?,?,?,?)",array($newresult['userid'],$newresult['contractid'],$newresult['salesorderid'],$inserttotal,$newresult['presence'],$newresult['createdtime']));
                        $db->pquery("UPDATE `vtiger_guarantee_seq` SET id=(SELECT guaranteeid FROM vtiger_guarantee ORDER BY guaranteeid DESC limit 1)",array());//更新表ID防止添加记录时数据不同步出错
                    }
                    $tempoccupancyamount=$tempoccupancyamount>$salesorderprice?$salesorderprice:$tempoccupancyamount;
                    $Guaranteesalesorderguarante=Guarantee_Record_Model::getGuarantecurrent($salesorderid);;//对应回款的总金额
                    Guarantee_Record_Model::updatesalesordertotal($Guaranteesalesorderguarante,$tempoccupancyamount,$salesorderid);//更新对应工单的担保金额
                }
                return true;
            }
        }
        return false;
    }

    /**
     * 生成工单的工作流
     * @param $workflowsid
     */
    static public function contractsMakeWorkflows($salesorderid,$servicecontractsid,$falg=0){
        $db=PearDatabase::getInstance();
        if($falg!=0){
            $db->pquery("UPDATE vtiger_salesorder SET modulestatus=(IF(modulestatus='c_lackpayment','a_normal',modulestatus)) WHERE vtiger_salesorder.salesorderid=?",array($salesorderid));
        }

        //$db->pquery('UPDATE vtiger_salesorderproductsrel SET productform=(SELECT vtiger_productcf.notecontent FROM vtiger_productcf WHERE vtiger_productcf.productid=vtiger_salesorderproductsrel.productid) WHERE vtiger_salesorderproductsrel.servicecontractsid=?',array($servicecontractsid));

        //2015-2-12 新增产品负责人

        //$result = $db->pquery("SELECT vtiger_crmentity.smcreatorid, vtiger_products.productname,vtiger_products.productid,vtiger_products.productman FROM `vtiger_salesorderproductsrel` LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_salesorderproductsrel.productid LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_salesorderproductsrel.productid WHERE salesorderid =? ",array($salesorderid));
        $result = $db->pquery("SELECT vtiger_products.productname, vtiger_products.productcategory, vtiger_products.realprice, vtiger_products.unit_price, vtiger_crmentity.smownerid, vtiger_products.productid FROM `vtiger_salesorderproductsrel` LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_salesorderproductsrel.productid LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_salesorderproductsrel.productid WHERE salesorderid =? ",array($salesorderid));
        while($product=$db->fetch_row($result)){
            $checkarray[]=array('workflowstagesname'=> $product['productname'].'审核','smcreatorid'=>$product['smownerid'],'productid'=>$product['productid']);
            //$checkarray[]=array('workflowstagesname'=> $product['productname'].'审核','smcreatorid'=>0,'productid'=>$product['productid'],'productman'=>$product['productman']);
        }
        vglobal('checkproducts',$checkarray);

        $on_focus = CRMEntity::getInstance('SalesOrder');
        $on_focus->makeWorkflows('SalesOrder',self::selectWorkfows(), $salesorderid,'edit');

        //更新客户的等级
        /*
        $recordModel = Vtiger_Record_Model::getInstanceById($servicecontractsid, 'ServiceContracts');

        $entity=$recordModel->entity->column_fields;
        //$accountid='';sc_related_to
        if($entity['sc_related_to']>0){
            Accounts_Record_Model::updateAccountsDealtime($entity['sc_related_to']);
        }
        */


    }

    /**
     * 判断是是否有工作流生成
     * @param $salesorderid//对应工单的ID
     * @throws Exception
     */
    static public function getWorkflows($salesorderid){

        $db=PearDatabase::getInstance();
        $query="SELECT count(1) AS counts FROM vtiger_salesorderworkflowstages WHERE salesorderid= ?";
        $result=$db->pquery($query,array($salesorderid));
        $result=$db->query_result($result,0,'counts');//是否有工作流生成
        if($result>0){
            //已经生成了工作流
            return false;
        }
        return true;
    }

    /**
     * 求合同对应的工单ID
     * @param $id合同对应的ID
     * @return mixed|string
     * @throws Exception
     */
    static public function getSalesorderid($contractid){
        $db=PearDatabase::getInstance();
         $query="SELECT salesorderid FROM vtiger_salesorder WHERE servicecontractsid={$contractid} ORDER BY salesorderid";
//从小到大
        return $db->run_query_allrecords($query);        
	$salesorderid=$db->pquery($query,array($contractid));
        return $db->query_result($salesorderid,0,'salesorderid');//新生的工单的ID
    }
    /**
     * 工单中对应的工作流ID
     * @param $salesorderid的ID
     * @return mixed|string
     * @throws Exception
     */
    static public function getSalesorderworkflowsid($salesorderid){
        $db=PearDatabase::getInstance();
        $query="SELECT workflowsid FROM vtiger_salesorder WHERE salesorderid=? ";
        $workflowsid=$db->pquery($query,array($salesorderid));
        return $db->query_result($workflowsid,0,'workflowsid');//对应工单中的工作流ID
    }

    /**
     * 选择要生成的工作流
     */
    static public function selectWorkfows(){
        return 361027;
    }
    /**
     * 保留工单工作流前两个节点要也审核
     */
    static public function keepNode(){
        return array(378331);
    }

    /**
     * 修改合同金额同时修改工单中的金额
     * @param $salesorderid对工应工的id
     * @param $total工单的修改的值
     * @param $fieldname工单的修改的字段
     */
    static public function setSalesordertotal($contractid,$total,$fieldname){
        $db=PearDatabase::getInstance();
        $fieldname=$fieldname=='remark'?'pending':'salescommission';
        $query="UPDATE vtiger_salesorder SET {$fieldname}=? WHERE servicecontractsid=? ";
        $db->pquery($query,array($total,$contractid));

    }

    /**
     * 工作流的成本审核节点节点当前节点是第一个节点且处于激活状态合同的成本大于0
     */
    static public function setWorkflowNode($salesorderid){
        $db=PearDatabase::getInstance();
        $query="SELECT
                    vtiger_salesorderworkflowstages.productid,(IFNULL(vtiger_salesorderproductsrel.costing,0)+IFNULL(vtiger_salesorderproductsrel.purchasemount,0)) AS costing,vtiger_salesorderproductsrel.productcomboid
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_salesorderproductsrel ON (vtiger_salesorderworkflowstages.salesorderid = vtiger_salesorderproductsrel.salesorderid AND vtiger_salesorderproductsrel.productid=vtiger_salesorderworkflowstages.productid )
                WHERE
                    vtiger_salesorderworkflowstages.salesorderid = {$salesorderid}
                AND vtiger_salesorderworkflowstages.modulename='SalesOrder'
                AND vtiger_salesorderworkflowstages.isaction = 1
                AND vtiger_salesorderworkflowstages.sequence = 1";
        $result=$db->run_query_allrecords($query);
        $allsubmit=false;//标识第一个节点全部通过
        $autosubmit=false;//用来标识第一个节点中部分产品要自动审核
        $salesorderids='';
        if(!empty($result)){
            $allsubmit=true;//标识财务节点自动审核
            $keepNode=self::keepNode();
            foreach($result as $value){
                if(in_array($value['productcomboid'],$keepNode)){
                    return '';
                }
                if($value['costing']>0){
                    $autosubmit=true;
                    $salesorderids.=$value['productid'].',';
                }else{
                    $allsubmit=false;
                }
            }
        }
        if($autosubmit){
            //global $current_user;
            $salesorderids=rtrim($salesorderids,',');
            $datetime=date('Y-m-d H:i:s');
            //审核节点
            //$sql="UPDATE `vtiger_salesorderworkflowstages` SET auditorid=?,auditortime=?,`schedule`=100,isaction=2 WHERE vtiger_salesorderworkflowstages.salesorderid =?
            //      AND vtiger_salesorderworkflowstages.modulename='SalesOrder'";
            //删除节点
            $sqld="DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid =?
                  AND vtiger_salesorderworkflowstages.modulename='SalesOrder'";
            //$sql1=$sql." AND vtiger_salesorderworkflowstages.productid in({$salesorderids}) AND vtiger_salesorderworkflowstages.isaction=1 AND vtiger_salesorderworkflowstages.sequence=1";
            //审核节点
            //$db->pquery($sql1,array($current_user->id,$datetime,$salesorderid));
            $sql1=$sqld." AND vtiger_salesorderworkflowstages.productid in({$salesorderids}) AND vtiger_salesorderworkflowstages.isaction=1 AND vtiger_salesorderworkflowstages.sequence=1";
            //删除节点
            $db->pquery($sql1,array($salesorderid));
            //修改工单的状态有回款不足变为审核中
            $db->pquery("UPDATE vtiger_salesorder SET modulestatus=(IF(modulestatus='c_lackpayment','b_actioning',IF(modulestatus='a_normal','b_actioning',modulestatus))) WHERE vtiger_salesorder.salesorderid=?",array($salesorderid));
            //$sql2=$sql."  AND vtiger_salesorderworkflowstages.sequence=2";
            //财务审核节点
            //$db->pquery($sql2,array($current_user->id,$datetime,$salesorderid));
            $sql2=$sqld."  AND vtiger_salesorderworkflowstages.sequence=2";
            //删除财务节点
            $db->pquery($sql2,array($salesorderid));
            if($allsubmit){
                //将审核节点下移激活节点
                $sql3="UPDATE `vtiger_salesorderworkflowstages` SET actiontime=?,isaction=1 WHERE vtiger_salesorderworkflowstages.salesorderid =?
                  AND vtiger_salesorderworkflowstages.modulename='SalesOrder'  AND vtiger_salesorderworkflowstages.productid in({$salesorderids})  AND vtiger_salesorderworkflowstages.sequence=3";
                $db->pquery($sql3,array($datetime,$salesorderid));
            }
        }
    }

    /**
     * 当前工作节点是否是财务首款审核,如果是则将当前节点自动审核,不是则不做处理
     * @param $salesorderid
     */
    static public function setWorkflowNodeFirst($salesorderid,$repayment='first_payment'){
        //首款first_payment 尾款last_payment
        $db=PearDatabase::getInstance();
        $query="SELECT
                    vtiger_workflowstages.sequence,vtiger_salesorderworkflowstages.isaction
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderid = {$salesorderid}
                AND vtiger_salesorderworkflowstages.modulename = 'SalesOrder'
                AND vtiger_workflowstages.workflowstagesflag = '{$repayment}'
              AND vtiger_salesorderworkflowstages.isaction  in(0,1) limit 1";
        $result=$db->run_query_allrecords($query);//当前节点是否是首款审核节点

        if(!empty($result)){
            global $current_user;
            $num=$result[0]['sequence'];
            $datetime=date('Y-m-d H:i:s');
            $sql="UPDATE `vtiger_salesorderworkflowstages` SET auditorid=?,auditortime=?,`schedule`=100,isaction=2 WHERE vtiger_salesorderworkflowstages.salesorderid =?
                  AND vtiger_salesorderworkflowstages.modulename='SalesOrder'  AND vtiger_salesorderworkflowstages.sequence=?";

            $db->pquery($sql,array($current_user->id,$datetime,$salesorderid,$num));
            if($result[0]['isaction']==1){
                //当首款节点为当前审核状态时则将审核状态下移一个节点,当前节点不为审核状态时不做处理
                ++$num;
                //节点下移
                $sql3="UPDATE `vtiger_salesorderworkflowstages` SET actiontime=?,isaction=1 WHERE vtiger_salesorderworkflowstages.salesorderid =?
                  AND vtiger_salesorderworkflowstages.modulename='SalesOrder'   AND vtiger_salesorderworkflowstages.sequence=?";
                $db->pquery($sql3,array($datetime,$salesorderid,$num));
            }
        }

    }


    /**
     *
     * @param $arr提交的数组中是否是有要生成工单的产品只要含有一个就生成工单
     * @return bool
     */
    static public function createIsWorkflows($arr,$flag=false){
        //指定产品类型生成工单
        $tempArr=self::tCloudPackage();
        if(!$flag){
            //合同提交过来的
            if(empty($arr)) {
                return false;
            }
        }else{
            //回款提交过来的
            $db=PearDatabase::getInstance();
            $query="SELECT vtiger_salesorderproductsrel.productcomboid FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid =?";
            $result=$db->pquery($query,array($flag));
            $num=$db->num_rows($result);
            if($num==0)return false;
            $arr=array();
            for($i=0;$i<$num;$i++){
                if($db->query_result($result,$i,'productcomboid')==0)continue;
                $arr[]=$db->query_result($result,$i,'productcomboid');
            }
            if(empty($arr))return false;

        }
        foreach ($arr as $value) {
            if (in_array($value, $tempArr)) {
                return true;
            }
        }
        return false;
    }

    /**
     * T云5个套餐
	 * 396796,396797,396798 new ids
     * @return array
     */
    static public function tCloudPackage(){
         return array(401,374,850,929,837,396796,396797,396798,417052,417059,417060,436259,436250,436258,436247,426342,426335,426337,426340,426322,422785); //add new standard product ids, by young.yang 2015-09-06
    }

    /*
     * 传入产品id 判断是哪一种产品套餐;
     * */
    static  function what_doublepush($products){
        $common = array();      //双推普及版
        $yellow_glod = array(); //双推黄金班
        $white_gold = array();  //双推白金版
        return 'common';

    }
    //wangbin 根据客户id查找当前客户购买的双推产品套餐
    static public function  search_double($accountid){
        $db=PearDatabase::getInstance();
       $sql = "SELECT productid FROM vtiger_servicecontracts WHERE vtiger_servicecontracts.sc_related_to = ? AND productid IN(?,?,?)";
       $result =  $db->pquery($sql,array($accountid,'396796','396797','396798'));
		if($db->num_rows($result)>0) {
            $product = $db->fetchByAssoc($result, 0);
			$products = $product['productid'];
		}
        if($products == "396796"){
            return 'common';
        }elseif($products == '396798'){
            return 'yellow_glod';
        }elseif($products =='396797'){
            return 'white_gold';
        }else{
            return false;
        }
    }
    static public  function servicecontracts_divide($contractid){
        $db=PearDatabase::getInstance();
        $sql = "SELECT *,( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_servicecontracts_divide.receivedpaymentownid = vtiger_users.id ) AS receivedpaymentownname FROM `vtiger_servicecontracts_divide` WHERE servicecontractid =?";
        $result = $db->pquery($sql,array($contractid));
        $result_li = array();
        if($db->num_rows($result)>0){
            for($i=0;$i<$db->num_rows($result);$i++){
                $result_li[] = $db->fetchByAssoc($result);
            }
        }
       return $result_li;
    }
    static public function servicecontracts_reviced($receiveid){
        $db=PearDatabase::getInstance();
        $sql = "SELECT IFNULL(sum(1),0) AS totals FROM `vtiger_servicecontracts` LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE vtiger_crmentity.smownerid=? AND vtiger_servicecontracts.modulestatus='已发放' AND vtiger_crmentity.deleted=0";
        $result = $db->pquery($sql,array($receiveid));
        $num=$db->query_result($result,0,'totals');
        if($num>=3){
            return $num;
        }else{
            return false;
        }
    }


}