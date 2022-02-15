<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
class Invoice_Save_Action extends Vtiger_Save_Action {

	public function process(Vtiger_Request $request) {
		//print_r($_REQUEST);
        $db=PearDatabase::getInstance();
        //如果有勾选回款,则要验证开票金额与勾选回款金额之合相等
        if(!empty($_REQUEST['receivedid'])){
            $receivedids=implode(',',$_REQUEST['receivedid']);

            $sql="SELECT sum(unit_price) AS sumcount FROM vtiger_receivedpayments WHERE receivedpaymentsid IN({$receivedids})";
            $resultdata=$db->pquery($sql,array());
            $result=$db->query_result($resultdata,0,'sumcount');
            if($request->get('record')>0 && empty($_REQUEST['taxtotal'])){
                $sql="SELECT vtiger_invoice.taxtotal FROM vtiger_invoice WHERE invoiceid=?";
                $resultdat=$db->pquery($sql,array($request->get('record')));
                $_REQUEST['taxtotal']=$db->query_result($resultdat,0,'taxtotal');
            }
            $_REQUEST['taxtotal']=str_replace(',','',$_REQUEST['taxtotal']);
            if($result!=$_REQUEST['taxtotal']){
                //echo '所选回款金额与开票不等<a href="javascript:history.go(-1);">返回</a>';
                //exit;
            }
        }
        //编辑修改处理先看一下有没有关联回款,如果有则判断金票是否修改

        /*
        if($_REQUEST['record']>0){
            $query='SELECT sum(unit_price) AS sumcount FROM vtiger_receivedpayments WHERE receivedpaymentsid IN (select receivedpaymentsid from vtiger_invoicerelatedreceive where invoiceid=?)';
            $resultdata=$db->pquery($query,array($_REQUEST['record']));
            $result=$db->query_result($resultdata,0,'sumcount');
            if($result!=0 && $result!=$_REQUEST['taxtotal']){
                echo '该发票开票金额已经和相关回款关联,若要修改开票金额请先删除该发票再重新开票<a href="javascript:history.go(-1);">返回</a>';
                exit;
            }
        }*/
         //新增保存给税率加个6%初始值
        if(empty($_REQUEST['taxrate'])){
            $request->set('taxrate','6%');
        }
        //给财务购方企业名称加个初始值
        if(!empty($_REQUEST['businessnamesone'])){
            $request->set('businessnames',$_REQUEST['businessnamesone']);
        }
        //print_r($_REQUEST);
        //exit;

		$recordModel = $this->saveRecord($request);
		
		$loadUrl = $recordModel->getDetailViewUrl();
		if(empty($loadUrl)){
			$loadUrl="index.php";
		}
		header("Location: $loadUrl");
	}
}
