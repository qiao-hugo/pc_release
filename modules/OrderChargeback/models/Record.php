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
class OrderChargeback_Record_Model extends Inventory_Record_Model {
    /**
     * 对应回款所关联的工单产品信息
     * @param $servicecontantsrid
     * @return array
     * @throws Exception
     */
    static public function getRelateProduct($servicecontantsrid,$flag=1){

        $db = PearDatabase::getInstance();
        $sql="select vtiger_salesorder.*,( SELECT CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( vtiger_users.`status` = 'Active', '', '[离职]' ) ) ) AS last_name FROM vtiger_users LEFT JOIN vtiger_crmentity ON vtiger_users.id = vtiger_crmentity.smownerid  WHERE vtiger_crmentity.crmid=vtiger_salesorder.salesorderid) AS salesorderowner,vtiger_salesorderproductsrel.*,vtiger_products.productname,vtiger_products.realprice,vtiger_crmentity.createdtime as crmentitycreatedtime,IFNULL(( SELECT vtiger_products.productname FROM vtiger_products WHERE vtiger_products.productid = vtiger_salesorderproductsrel.productcomboid ), '--' ) AS productcomboname from vtiger_salesorderproductsrel LEFT JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid=vtiger_salesorderproductsrel.salesorderid left join vtiger_products on vtiger_salesorderproductsrel.productid=vtiger_products.productid left join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_products.productid where vtiger_salesorderproductsrel.servicecontractsid=? AND vtiger_salesorderproductsrel.multistatus=3";

        $result=$db->pquery($sql,array($servicecontantsrid));
        $temp=array();
        $temparr=array();
        for($i=0; $i<$db->num_rows($result); $i++) {
            $salesorderproductsrelid= $db->query_result($result, $i,'salesorderproductsrelid');
            $productid = $db->query_result($result, $i, 'productid');
            //$productform =	$db->query_result($result, $i, 'productform');
            $productname =	$db->query_result($result, $i, 'productname');
            //$realprice =	$db->query_result($result, $i, 'realprice');
            $marketprice =	$db->query_result($result, $i, 'marketprice');
            //$auditorid =	$db->query_result($result, $i, 'last_name');
            $createtime =	$db->query_result($result, $i, 'createtime');
            $productcomboname =	$db->query_result($result, $i, 'productcomboname');
            $agelife =$db->query_result($result, $i, 'agelife');
            $salesorderid =$db->query_result($result, $i, 'salesorderid');
            $subject =$db->query_result($result, $i, 'subject');
            $workflowsnode =$db->query_result($result, $i, 'workflowsnode');
            $salesorderowner =$db->query_result($result, $i, 'salesorderowner');
            $salesorder_no =$db->query_result($result, $i, 'salesorder_no');
            $productnumber =$db->query_result($result, $i, 'productnumber');
            $costing =$db->query_result($result, $i, 'costing');
            $purchasemount =$db->query_result($result, $i, 'purchasemount');
            $modulestatus =vtranslate($db->query_result($result, $i, 'modulestatus'),"SalesOrder");
            if(!in_array($salesorderid,$temparr)){
                $temp[$salesorderid]=array('salesorderid'=>$salesorderid,'subject'=>$subject,'workflowsnode'=>$workflowsnode,'salesorder_no'=>$salesorder_no,'modulestatus'=>$modulestatus,'salesorderowner'=>$salesorderowner);
                $temparr[]=$salesorderid;
            }
            $temp[$salesorderid]['productlist'][]=array('salesorderproductsrelid'=>$salesorderproductsrelid,'productid'=>$productid,'productname'=>$productname,'realprice'=>$marketprice,'createtime'=>$createtime,'productcomboname'=>$productcomboname,'productnumber'=>$productnumber,'agelife'=>$agelife,'costing'=>$costing,'purchasemount'=>$purchasemount);
        }
        return $temp;
    }

    /**
     * 取得合同对应的发票
     * @param $servicecontantsrid
     * @return array
     * @throws Exception
     */
    static public function getRelateInvoice($servicecontantsrid){

        $db = PearDatabase::getInstance();
        $sql="SELECT vtiger_invoice.invoiceid,vtiger_invoiceextend.invoiceextendid,vtiger_invoiceextend.billingtimeextend,vtiger_invoiceextend.invoicecodeextend,vtiger_invoiceextend.invoice_noextend,vtiger_invoiceextend.commoditynameextend,vtiger_invoiceextend.totalandtaxextend,vtiger_invoiceextend.processstatus,vtiger_invoiceextend.invoicestatus,(SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname FROM vtiger_departments WHERE departmentid = (SELECT	departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users WHERE vtiger_invoiceextend.operator = vtiger_users.id) AS operator,vtiger_invoiceextend.operatortime FROM vtiger_invoiceextend LEFT JOIN vtiger_invoice ON vtiger_invoice.invoiceid=vtiger_invoiceextend.invoiceid WHERE vtiger_invoiceextend.deleted=0 AND vtiger_invoice.contractid=?";
        $result=$db->pquery($sql,array($servicecontantsrid));
        $temp=array();
        for($i=0; $i<$db->num_rows($result); $i++) {
            $invoiceid= $db->query_result($result, $i,'invoiceid');
            $invoiceextendid = $db->query_result($result, $i, 'invoiceextendid');
            $billingtimeextend =	$db->query_result($result, $i, 'billingtimeextend');
            $invoicecodeextend =	$db->query_result($result, $i, 'invoicecodeextend');
            $invoice_noextend =	$db->query_result($result, $i, 'invoice_noextend');
            $commoditynameextend =	$db->query_result($result, $i, 'commoditynameextend');
            $invoicestatus =$db->query_result($result, $i, 'invoicestatus');
            $operatortime =$db->query_result($result, $i, 'operatortime');
            $processstatus =$db->query_result($result, $i, 'processstatus');
            $operator =$db->query_result($result, $i, 'operator');
            $totalandtaxextend =$db->query_result($result, $i, 'totalandtaxextend');

            $temp[]=array('invoiceid'=>$invoiceid,'invoiceextendid'=>$invoiceextendid,'billingtimeextend'=>$billingtimeextend,'invoicecodeextend'=>$invoicecodeextend,'invoice_noextend'=>$invoice_noextend,'commoditynameextend'=>$commoditynameextend,'totalandtaxextend'=>$totalandtaxextend,'operatortime' =>$operatortime,'processstatus' =>$processstatus,'operator' =>$operator,'invoicestatus'=>$invoicestatus);
        }
        return $temp;
    }

    /**
     * 返回要修改的列表
     * @param $servicecontractsid
     * @return array
     */
    static  public function getInvoiceSalesorderL($servicecontractsid){
        $invoicelist=array();
        $salesorderlist=array();
        $receivepayments=array();
        if($servicecontractsid>0){
            $invoicelist=self::getRelateInvoice($servicecontractsid);
            $salesorderlist=self::getRelateProduct($servicecontractsid);
            $receivepayments=ReceivedPayments_Record_Model::getAllReceivedPayments($servicecontractsid);
        }
        return array('invoicelist'=>$invoicelist,'salesorderlist'=>$salesorderlist,'receivepayments'=>$receivepayments);
    }

    static public function getorderinvoicesaleorderlist($recordid){
        $db=PearDatabase::getInstance();
        $query="SELECT * FROM vtiger_orderchargeproducts WHERE orderchargebackid={$recordid}";
        $oldarray=$db->run_query_allrecords($query);
        $salesoldorderid=array();
        $invoiceoldorderid=array();
        if(!empty($oldarray)){
            foreach($oldarray as $value){
                if('SalesOrder'==$value['setype']){
                    if(!in_array($value['oldorderid'],$salesoldorderid)){
                        $salesoldorderid[]=$value['oldorderid'];
                    }
                    $salesoldorderid[]=$value['oldproductid'];
                }else if('Invoice'==$value['setype']){
                    $invoiceoldorderid[]=$value['oldproductid'];
                }
            }
        }
        return array('salesoldorderid'=>$salesoldorderid,'invoiceoldorderid'=>$invoiceoldorderid);
    }
    function getProducts($salesorderid){
        //return null;
        $relateProducts=OrderChargeback_Record_Model::getRelateProducts($salesorderid,2);
        $temp = array();
        foreach($relateProducts as $rp){
            $salesorderProducts=SalesorderProductsrel_Record_Model::getInstanceById($rp['salesorderproductsrelid'],'SalesorderProductsrel');
            $salesorderData = $salesorderProducts->getData();
            $temp[]=array_merge($salesorderData,$rp);
        }
        return $temp;
    }

    public static function getRelateProducts($salesorderid,$flag=1){

        $db = PearDatabase::getInstance();
        //2014-12-20更新start
        $sql="select vtiger_salesorderproductsrel.*,vtiger_products.productname,vtiger_products.realprice,vtiger_crmentity.createdtime as crmentitycreatedtime,vtiger_users.last_name,IFNULL(( SELECT vtiger_products.productname FROM vtiger_products WHERE vtiger_products.productid = vtiger_salesorderproductsrel.productcomboid ), '--' ) AS productcomboname from vtiger_salesorderproductsrel left join vtiger_products on vtiger_salesorderproductsrel.productid=vtiger_products.productid left join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_products.productid left join vtiger_users on vtiger_crmentity.smownerid=vtiger_users.id where vtiger_salesorderproductsrel.salesorderid=?";
        if(1==$flag){
                    $sql.=' AND vtiger_salesorderproductsrel.multistatus IN (0,3)';
        }else{
            $sql.=' AND vtiger_salesorderproductsrel.multistatus=5';
        }
        //2014-12-20日更新end
        //$sql="select vtiger_salesorderproductsrel.*,vtiger_products.productname from vtiger_salesorderproductsrel left join vtiger_products on vtiger_salesorderproductsrel.productid=vtiger_products.productid where vtiger_salesorderproductsrel.salesorderid=?";
        $result=$db->pquery($sql,array($salesorderid));
        //print_r($result);die;
        $temp=array();
        for($i=0; $i<$db->num_rows($result); $i++) {
            $salesorderproductsrelid= $db->query_result($result, $i,'salesorderproductsrelid');
            $productid = $db->query_result($result, $i, 'productid');
            $productform =	$db->query_result($result, $i, 'productform');
            $productname =	$db->query_result($result, $i, 'productname');
            //2014-12-20更新start
            $marketprice =	$db->query_result($result, $i, 'realprice');
            $auditorid =	$db->query_result($result, $i, 'last_name');
            $createtime =	$db->query_result($result, $i, 'createtime');
            $productcomboname =	$db->query_result($result, $i, 'productcomboname');
            //2015-11-16
            $agelife =$db->query_result($result, $i, 'agelife');
            $productnumber =$db->query_result($result, $i, 'productnumber');
            //2015-11-16
            //echo $createtime;
            $temp[$productid]=array('salesorderproductsrelid'=>$salesorderproductsrelid,'productid'=>$productid,'notecontent'=>$productform,'productname'=>$productname,'realprice'=>$marketprice,'auditorid'=>$auditorid,'createtime'=>$createtime,'productcomboname'=>$productcomboname,'productnumber'=>$productnumber,'agelife'=>$agelife);
            //2014-12-20日更新end
            /*
			$marketprice =	$db->query_result($result, $i, 'marketprice');
			$temp[$productid]=array('salesorderproductsrelid'=>$salesorderproductsrelid,'productid'=>$productid,'notecontent'=>$productform,'productname'=>$productname,'realprice'=>$marketprice);
            */
        }
        //var_dump($temp);
        return $temp;
    }


    /**
     * 可导出数据的权限
     * @return bool
     */
    static public function exportGroupri(){
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

    /**
     * 根据合同编号查询是否存在退款申请
     * @return bool
     */
    public function hasOrderChargeback(Vtiger_Request $request){
        $contractno= $request->get('contractno');
        $db = PearDatabase::getInstance();
        $sql = "select 1 from vtiger_orderchargeback as ocb join vtiger_servicecontracts as sc on ocb.servicecontractsid = sc.servicecontractsid where sc.contract_no = ? and ocb.modulestatus != 'a_normal'";
        $result = $db->pquery($sql,array($contractno));
        if(!$db->num_rows($result)){
            $return=array('success'=>false,'msg'=>'没有相关数据');
        }else{
            $data=array();
            $return=array('success'=>true,'data'=>$data,'msg'=>'有相关数据');
        }
        return $return;
    }

}