<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class TyunWebBuyService_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();

		$this->exposeMethod('CAExportData');
		$this->exposeMethod('rebindContract');
		$this->exposeMethod('cancelOrder');
		$this->exposeMethod('signContract');
		$this->exposeMethod('offlineReconciliation');
		$this->exposeMethod('exportdata');
	        $this->exposeMethod('getSearchWhereContent');
	        $this->exposeMethod('exportDataReconciliationResult');
	        $this->exposeMethod('startToFiled');
	        $this->exposeMethod('exportdataReconciliation');
        $this->exposeMethod('confirmCreateServiceContract');//确认手动创建电子合同
        $this->exposeMethod('sendElecContract');//手动发送电子合同
	$this->exposeMethod('addAgentid');
		$this->exposeMethod('delAgentid');
		$this->exposeMethod('updateAgentid');

	}
	
	function checkPermission(Vtiger_Request $request) {
		return;
	}
    /**
     * @param Vtiger_Request $request
     * @throws Exception
     */
	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}

	}
    /**
     * T云表格数据
     * @param Vtiger_Request $request
     */
    public function CAExportData(Vtiger_Request $request){
        $moduleModel=Vtiger_Module_Model::getInstance('ContractActivaCode');
        $moduleModel->CAExportDataExcel($request);
    }

    /**
     * 重绑合同
     */
    public function rebindContract(Vtiger_Request $request){
        $record=$request->get('record');
        $flag=false;
        try{
            $recordModule=Vtiger_Record_Model::getInstanceById($record,'TyunWebBuyService');
        }catch (Exception $e){
            $flag=true;
        }
        $returndata=array('flag'=>false);
        do{
            if($flag){
                $returndata['msg']='没有相关权限！';
                break;
            }
            if($this->checkPermissionData($recordModule->get('creator'))){
                $returndata['msg']='没有相关权限！';
                break;
            }
            $contractno=$request->get('newrecord');
            $contractno=trim($contractno);
            if(empty($contractno)){
                $returndata['msg']='合同编号不存在，不允许此操作！';
                break;
            }
            if(!in_array($recordModule->get('orderstatus'),array('orderdoused','ordernotused'))){
                $returndata['msg']='只有有效的订单才能进行此操作!';
                break;
            }
            $contractid=$recordModule->get('contractid');
            $customerid=$recordModule->get('customerid');
            global $adb,$current_user;
            if($contractid>0){
                $query='SELECT sc_related_to,total,contract_no,servicecontractsid,modulestatus FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid=vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0  AND vtiger_servicecontracts.servicecontractsid=? LIMIT 2';
                $result=$adb->pquery($query,array($contractid));
                $numold=$adb->num_rows($result);
                if($numold==0){
                    $returndata['msg']='原合同状态错误请先处理后再绑定！';
                    break;
                }
                $resultDataold=$adb->query_result_rowdata($result,0);
                if($resultDataold['modulestatus']!='已发放'){
                    $returndata['msg']='原合同是未签收状态的才能重绑！';
                    break;
                }
            }
            $query='SELECT sc_related_to,total,contract_no,servicecontractsid,modulestatus FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid=vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.contract_no=? LIMIT 2';
            $result=$adb->pquery($query,array($contractno));
            $num=$adb->num_rows($result);
            if($num==1){
                $resultData=$adb->query_result_rowdata($result,0);
                if($resultData['modulestatus']!='已发放'){
                    $returndata['msg']='已签收的合同不能绑定！';
                    break;
                }
            }elseif($num>1){
                $returndata['msg']='合同编号不存在，或存在未签合同!！';
                break;
            }else{
                $returndata['msg']='合同编号不存在，不允许此操作！';
                break;
            }
            if($contractid==$resultData['servicecontractsid']){
                $returndata['msg']='相同的合同不允许重绑！';
                break;
            }
            $query="SELECT 1 FROM vtiger_activationcode WHERE contractid=? AND `status`!=2";
            $result=$adb->pquery($query,array($resultData['servicecontractsid']));
            $num=$adb->num_rows($result);
            if($num>0){
                $returndata['msg']='合同已被其他订单使用,不允许重绑！';
                break;
            }
//            $rebindData=$recordModule->rebindContract($request);
            $rebindData=$recordModule->rebindContractByContractNo($request);
            $rebindData=json_decode($rebindData,true);
            if($rebindData["code"]!='200'){
                $returndata['msg']='71360平台绑定错误,请稍后再试！';
                break;
            }
            $id = $adb->getUniqueId('vtiger_modtracker_basic');
            $currentTime=date('Y-m-d H:i:s');
            $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                array($id, $record, 'TyunWebBuyService', $current_user->id, $currentTime, 0));
            $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, 'contractname', $recordModule->get('contractname'), $resultData['contract_no']));

            $sql1 = "select * from vtiger_activationcode where contractid=?";
            $result = $adb->pquery($sql1,array($contractid));
            if($adb->num_rows($result)){
                while ($row = $adb->fetch_row($result)){
                    $sql='UPDATE vtiger_activationcode SET contractid=?,contractname=? WHERE activationcodeid=?';
                    $adb->pquery($sql,array($resultData['servicecontractsid'],$resultData['contract_no'],$row['activationcodeid']));
                }
            }
            $returndata['msg']='绑定成功！';
            $returndata['flag']=true;
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($returndata);
        $response->emit();
    }

    /**
     * 数据权限验证
     * @param $userid
     * @return bool
     * @throws AppException
     */
    public function checkPermissionData($userid){
        $where=getAccessibleUsers('TyunWebBuyService','List',true);
        if($where=='1=1'){
            return false;
        }
        if(in_array($userid,$where)){
            return false;
        }
        return true;
    }

    /**
     * 取消订单
     * @param Vtiger_Request $request
     */
    public function cancelOrder(Vtiger_Request $request){
        global $adb,$tyunweburl,$sault;
        $record=$request->get('record');
        $refundamount=$request->get('refundamount');
        $query="SELECT * FROM vtiger_activationcode WHERE activationcodeid=? limit 1";
        $result=$adb->pquery($query,array($record));
        $return=array('flag'=>false);
        do{
            if($adb->num_rows($result)==0){
                $return['msg']="没有相找到相关的记录";
                break;
            }

            $resultData=$adb->raw_query_result_rowdata($result,0);
            if($resultData['status']==2){
                $return['msg']="订单已经作废!";
                break;
            }
            if($resultData['orderamount']<$refundamount){
                $return['msg']="退款金额大于订单金额";
                break;
            }
            if($resultData['orderstatus']=='orderdocancel'){
                $return['msg']="订单作废中...!";
                break;
            }
            if($resultData['contractid']>0){
                $sql = "SELECT activationcodeid FROM vtiger_activationcode WHERE contractid=?";
                $result1=$adb->pquery($sql,array($resultData['contractid']));
                if($adb->num_rows($result1) > 1){
                    $return['msg']="请在服务合同中点选'作废'或'取消激活'!";
                    break;
                }

                $exceptionflag=false;
                try{
                    $serviceceRecordModel=Vtiger_Record_Model::getInstanceById($resultData['contractid'],'ServiceContracts');
                }catch (Exception $e){
                    $exceptionflag=true;
                }
                if($exceptionflag){
                    $return['msg']="请在服务合同中点选'作废'或'取消激活'!";
                    break;
                }
                if($serviceceRecordModel->get('modulestatus')!='已发放'){
                    $return['msg']="请在服务合同中点选'作废'或'取消激活'!";
                    break;
                }
            }
            $recordModel=Vtiger_Record_Model::getCleanInstance('TyunWebBuyService');
            $Repson=$recordModel->doOrderCancel($resultData);
            $jsonData=json_decode($Repson,true);
            if($jsonData['code']=='200'){
                $sql="UPDATE  vtiger_activationcode SET `status`=2,orderstatus='ordercancel',refundamount=?,canceldatetime=?,isdisabled=1 WHERE activationcodeid=?";
                $adb->pquery($sql,array($refundamount,date('Y-m-d H:i:s'),$record));
                if(in_array($resultData['classtype'],array('cupgrade','cdegrade','crenew','cbuy'))){
                    $sql=" UPDATE  vtiger_activationcode SET  isclientmigration=0 WHERE usercode=? AND comeformtyun=0 ";
                    $adb->pquery($sql,array($resultData['usercode']));
                }
                if($resultData['contractid']>0){
                    if($serviceceRecordModel->get('modulestatus')=='c_complete'){
                        $adb->pquery("update vtiger_contracts_execution_detail set iscancel=1 where contractid=?",array($resultData['contractid']));
                        $adb->pquery("update vtiger_contract_receivable set iscancel=1 where contractid=?",array($resultData['contractid']));
                        $adb->pquery("update vtiger_receivable_overdue set iscancel=1 where contractid=?",array($resultData['contractid']));
                    }else{
                        //订单取消 删除应对合同收表中数据
                        $adb->pquery("delete from vtiger_contract_receivable where contractid=?",array($resultData['contractid']));
                    }
                }

                $return['msg']="订单取消成功!";
                $return['flag']=true;
            }else{
                $return['msg']="订单取消失败,请联相关人员处理!";
            }

        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($return);
        $response->emit();

    }
    public function signContract(Vtiger_Request $request){
        return false;
        global $adb;
        $record=$request->get('record');
        $flag=false;
        $query='SELECT * FROM vtiger_activationcode WHERE comeformtyun=1 AND `status` in(0,1) AND activationcodeid=? limit 1';
        $result=$adb->pquery($query,array($record));
        $returndata=array('flag'=>false);
        do{
            if(!$adb->num_rows($result)){
                $returndata['msg']='没有找到相关数据！';
                break;
            }
            $resultData=$adb->query_result_rowdata($result,0);
            if(empty($resultData['contractid'])){
                $returndata['msg']='合同不存在！';
                break;
            }
            if(empty($resultData['customerid'])){
                $returndata['msg']='客户不存在！';
                break;
            }
            $query='SELECT vtiger_servicecontracts.* FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid=vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.servicecontractsid=? limit 1';
            $result=$adb->pquery($query,array($resultData['contractid']));
            if(!$adb->num_rows($result)){
                $returndata['msg']='合同不存在！';
                break;
            }
            $serviceResultData=$adb->query_result_rowdata($result,0);
            if($serviceResultData['modulestatus']!='c_complete'){
                $returndata['msg']='纸质合同未签收！';
                break;
            }
            if($serviceResultData['sc_related_to']!=$resultData['customerid']){
                $returndata['msg']='不是同一客户！';
                break;
            }
        }while(0);
    }

    /**
     * 手工对对账
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function offlineReconciliation(Vtiger_Request $request){
        global $adb,$current_user;
        $record=$request->get('record');
        $flag=false;
        $returndata=array('flag'=>false);
        try{
            $recordModule=Vtiger_Record_Model::getInstanceById($record,'TyunWebBuyService');
        }catch (Exception $e){
            $flag=true;
        }
        do{
            if($flag){
                $returndata['msg']='找不到该记录!';
                break;
            }
            $paymentcode=$request->get('paymentcode');
            if(empty($paymentcode)){
                $returndata['msg']='回款流水号不存在!';
                break;
            }
            if($recordModule->get('orderstatus')!='orderdoused'){
                $returndata['msg']='只有有效的订单才能进行此操作!';
                break;
            }

            $paymentcode=array_map(function($v){return trim($v);},$paymentcode);
            $orderamount=$recordModule->get('orderamount');//订单金额
            $result=$adb->pquery('SELECT sum(amountofmoney) AS amountofmoney FROM vtiger_tyunwebpaymentcode WHERE deleted=0 AND activationcodeid=?',array($record));
            $amountofmoney=0;//已经匹配的金额
            if($adb->num_rows($result)){
                $resultData=$adb->query_result_rowdata($result,0);
                $amountofmoney=$resultData['amountofmoney'];
            }
            if(bccomp($orderamount,$amountofmoney)<1){
                $returndata['msg']='回款已匹配完!';
                break;
            }
            $existpaymentcode=array();//存在的流水号
            $newpaymentcode=array();//没有使用的流水号
            $matchpaymentcode=array();//匹配成功的流水号
            $newpaymentcodeMoney=array();
            $receivedpaymentsidArray=array();//回款ID数组
            foreach($paymentcode as $value){
                $result=$adb->pquery('SELECT activationcodeid FROM vtiger_tyunwebpaymentcode WHERE deleted=0 AND paymentcode=? AND activationcodeid=?',array($value,$record));
                if($adb->num_rows($result)){
                    $existpaymentcode[]=$value;
                    continue;
                }
                $newpaymentcode[]=$value;
                $receivedpaymentsidArray[$value]=array();
                $newpaymentcodeMoney[$value]=array('unit_price'=>0,'receivedpaymentsid'=>0);
                $query='SELECT * FROM vtiger_receivedpayments WHERE deleted=0 AND paymentcode=?';
                $result=$adb->pquery($query,array($value));
                if($adb->num_rows($result)){
                    $sum_unit_price=0;
                    $matchpaymentcode[]=$value;
                    while($row=$adb->fetch_array($result)){
                        $amountofmoney=bcadd($amountofmoney,$row['unit_price'],2);
                        $sum_unit_price=bcadd($sum_unit_price,$row['unit_price'],2);
                        $receivedpaymentsidArray[$value][]=array('unit_price'=>$row['unit_price'],'receivedpaymentsid'=>$row['receivedpaymentsid']);
                    }
                    $newpaymentcodeMoney[$value]=array('unit_price'=>$sum_unit_price,'receivedpaymentsid'=>0);
                }
            }
            if(bccomp($orderamount,$amountofmoney)<1){
                $returndata['msg']='回款已匹配完!!';
                break;
            }
            if(empty($newpaymentcode)){
                $returndata['msg']='流水号已使用,不允许重复使用!';
                break;
            }
            $datetime=date('Y-m-d H:i:s');
            foreach($newpaymentcode as $value){
                $sql="INSERT INTO `vtiger_tyunwebpaymentcode` (`paymentcode`, `activationcodeid`,receivedpaymentsid, `amountofmoney`, `createdid`, `createdtime`) VALUES (?,?,?,?,?,?)";
                if(empty($receivedpaymentsidArray[$value])){
                    $adb->pquery($sql,array($value,$record,$newpaymentcodeMoney[$value]['receivedpaymentsid'],$newpaymentcodeMoney[$value]['unit_price'],$current_user->id,$datetime));
                }else{
                    foreach($receivedpaymentsidArray[$value] as $rValue){
                        $adb->pquery($sql,array($value,$record,$rValue['receivedpaymentsid'],$rValue['unit_price'],$current_user->id,$datetime));
                    }
                }
            }
            $tempArray=array('existpaymentcode'=>$existpaymentcode,
                'matchpaymentcode'=>$matchpaymentcode,
                'insertpaymentcode'=>array_diff($newpaymentcode,$matchpaymentcode),
            );
            $msg='<hr>存在的流水号'.implode('<br>',$existpaymentcode).'<hr>没有使用的流水号'.implode('<br>',$matchpaymentcode).'<hr>匹配成功的流水号'.implode('<br>',array_diff($newpaymentcode,$matchpaymentcode));
            $returndata=array('flag'=>true,'data'=>$tempArray,'msg'=>$msg);
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($returndata);
        $response->emit();
    }
    public function https_request($url, $data = null,$curlset=array()){
        $this->_logs(array("发送到Tweb云服务端的url请求", $url));
        $curl = curl_init();
        if(!empty($curlset)){
            foreach($curlset as $key=>$value){
                curl_setopt($curl, $key, $value);
            }
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        $this->_logs(array("返回处理结果：", $output));
        curl_close($curl);
        return $output;
    }
    /**
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     * 自定义导出
     */
    public function exportdata(Vtiger_Request $request){
        set_time_limit(0);
        global $site_URL,$current_user,$root_directory,$adb;
        $recordModel = Vtiger_Record_Model::getCleanInstance('TyunWebBuyService');
        if(!$recordModel->personalAuthority('TyunWebBuyService','exportdatakai')){
            $response=new Vtiger_Response();
            $response->setError(500,"没有相关权限!");
            $response->emit();
            exit;
        }
        $listViewModel = Vtiger_ListView_Model::getInstance("TyunWebBuyService");
        $moduleMOdel=$listViewModel->getModule();
        $listViewModel->getSearchWhere();
        $query = $listViewModel->getQuery();
        $query = str_replace(",vtiger_activationcode.activationcodeid FROM vtiger_activationcode",",(SELECT GROUP_CONCAT(sted.productname SEPARATOR '**') FROM vtiger_activationcode sted WHERE sted.contractid=vtiger_activationcode.contractid AND sted.`status` in(0,1) limit 1) AS 'packproductname',vtiger_activationcode.activationcodeid FROM vtiger_activationcode",$query);
        $query.=$listViewModel->getUserWhere();
        $query.=' AND vtiger_activationcode.comeformtyun=1 AND agents<>10817';
        $LISTVIEW_FIELDS = $listViewModel->getSelectFields();
        $listViewHeaders = $listViewModel->getListViewHeaders();
        $temp = array();
        if (!empty($LISTVIEW_FIELDS)) {
            foreach ($LISTVIEW_FIELDS as $key => $val) {
                if (isset($listViewHeaders[$key])) {
                    $temp[$key] = $listViewHeaders[$key];
                }
            }
        }
        if(empty($temp)) {
            $temp = $listViewHeaders;
        }
        $headerArray=$temp;
        ini_set('memory_limit','512M');
        $path=$root_directory.'temp/';
        $filename=$path.'tweb'.$current_user->id.'.csv';
        !is_dir($path)&&mkdir($path,'0777',true);
        @unlink($filename);
        $array= array();
        foreach($headerArray as $key=>$value){
            $array[]=iconv('utf-8','gb2312',vtranslate($key,'TyunWebBuyService'));
        }
        $array[]=iconv('utf-8','gb2312','未开票金额');
        $array[]=iconv('utf-8','gb2312','未收款金额');
        $array[]=iconv('utf-8','gb2312','每月确认收入');
        $array[]=iconv('utf-8','gb2312','累计确认收入');
        $array[]=iconv('utf-8','gb2312','本月确认收入');
        $array[]=iconv('utf-8','gb2312','是否到期');
        $array[]=iconv('utf-8','gb2312','合同应收账款');
        $array[]=iconv('utf-8','gb2312','产品1');
        $array[]=iconv('utf-8','gb2312','产品2');
        $array[]=iconv('utf-8','gb2312','产品3');
        $array[]=iconv('utf-8','gb2312','产品4');
        $array[]=iconv('utf-8','gb2312','产品5');
        $array[]=iconv('utf-8','gb2312','产品6');
        $array[]=iconv('utf-8','gb2312','产品7');
        $array[]=iconv('utf-8','gb2312','产品8');
        $array[]=iconv('utf-8','gb2312','产品9');
        $array[]=iconv('utf-8','gb2312','产品10');
        $fp=fopen($filename,'w');
        fputcsv($fp,$array);
        $listViewModel->isAllCount=1;
        //$listCount = $listViewModel->getListViewCount();
        $limitStep=1000;
        //$num=ceil($listCount/$limitStep);
        $cnt = 0;
        $arrayint=array('orderamount','contractprice','marketprice','costprice','servicecontractstotal','invoicetotal','refundamount','paymenttotal');
        //for($i=0;$i<$num;$i++) {
        $i=0;
        while(true) {
            $limitSQL = " limit " . $cnt * $limitStep . "," . $limitStep;
            ++$cnt;
            $result = $adb->pquery($query . $limitSQL, array());
            if ($adb->num_rows($result)) {
                while ($value = $adb->fetch_array($result)) {
                    $array = array();
                    foreach ($headerArray as $keyheader => $valueheader) {
                        if ($valueheader['uitype'] == 10) {
                            $currnetValue = uitypeformat($valueheader, $value, 'TyunWebBuyService');
                            $pattern = '/<[^>]+>/';
                            $currnetValue = preg_replace($pattern, '', $currnetValue);
                        } elseif ($valueheader['uitype'] == 54) {
                            $currnetValue = uitypeformat($valueheader, $value, 'TyunWebBuyService');
                            $pattern = '/<[^>]+>/';
                            $currnetValue = preg_replace($pattern, '', $currnetValue);
                        } elseif ($valueheader['uitype'] == 15) {
                            $currnetValue = vtranslate($value[$keyheader], 'TyunWebBuyService');
                            $pattern = '/<[^>]+>/';
                            $currnetValue = preg_replace($pattern, '', $currnetValue);
                        } else {
                            $currnetValue = uitypeformat($valueheader, $value, 'TyunWebBuyService');
                        }
                        if ($keyheader == 'startdate' || $keyheader == 'expiredate') {
                            $currnetValue = substr($currnetValue, 0, 2) > 0 ? $currnetValue : '';
                        }
                        $currnetValue = strip_tags($currnetValue);
                        if (!in_array($keyheader, $arrayint)) {
                            $currnetValue = '="' . $currnetValue . '"';
                        }

                        $currnetValue = iconv('utf-8', 'GBK//IGNORE', $currnetValue);
                        $array[] = $currnetValue;
                    }
                    $orderstatus = $value['orderstatus'];
                    $startdate = $value['startdate'];
                    $current_date = date('Y-m', strtotime('+1 month')) . '-15';
                    if (!empty($startdate) && substr($startdate, 0, 2) > 0 && strtotime($startdate) < strtotime($current_date)) {
                        if ($orderstatus == 'ordercancel') {
                            $docanceltime = explode('-', $value['canceldatetime']);
                            $tempmonth = $docanceltime[1] + 1;
                            $docanceldate = $tempmonth > 12 ? (($docanceltime[0] + 1) . '-01') : ($docanceltime[0] . '-' . $tempmonth);
                            $current_date = $docanceldate;
                        }
                        $currentDiffMonth = $moduleMOdel->getMonthNum(substr($startdate, 0, 7) . '-01', substr($current_date, 0, 7) . '-15');
                        $currentDiffMonth = $currentDiffMonth['y'] * 12 + $currentDiffMonth['m'];
                        $maxMonth = $value['productlife'] * 12;
                        $diffMonth = $currentDiffMonth > $maxMonth ? $maxMonth : $currentDiffMonth;
                        $monthlyIncome = $value['contractprice'] / $maxMonth;
                        $monthlyIncome = number_format($monthlyIncome, 2, '.', '');
                        $cumulativeIncome = $diffMonth != $maxMonth ? $diffMonth * $monthlyIncome : $value['contractprice'];
                        $isMaturity = $currentDiffMonth > $maxMonth ? '是' : '否';
                        if ($orderstatus == 'ordercancel') {
                            $thisMonthlyIncome = '--';
                            $isMaturity = '是';
                        } else {
                            $thisMonthlyIncome = (empty($value['startdate']) || $currentDiffMonth > $maxMonth) ? 0.00 : $monthlyIncome;
                        }
                        $value['paymenttotal'] = ($value['paymenttotal'] <= $value['contractprice']) ? $value['paymenttotal'] : $value['contractprice'];
                        $value['thisMonthlyIncome'] = $thisMonthlyIncome;//本月确认收入
                        $value['isMaturity'] = $isMaturity;//是否到期
                        $value['monthlyIncome'] = $monthlyIncome;//每月确认收入
                        $value['cumulativeIncome'] = $cumulativeIncome;//累计确认收入
                        $value['temprepaty'] = bcsub($value['contractprice'], $value['paymenttotal'], 2);
                        $value['tempinvoice'] = bcsub($value['contractprice'], $value['invoicetotal'], 2);
                        $value['accountsreceivable'] = bcsub($cumulativeIncome, $value['contractprice'], 2);//合同应收账款
                    } elseif ($orderstatus == 'ordercancel') {
                        $rawData['thisMonthlyIncome'] = '--';//本月确认收入
                        $rawData['isMaturity'] = '是';//是否到期
                        $rawData['monthlyIncome'] = '--';//每月确认收入
                        $rawData['cumulativeIncome'] = '--';//累计确认收入
                        $isMaturity = '是';
                        $monthlyIncome = 0.00;
                        $cumulativeIncome = 0.00;
                        $thisMonthlyIncome = 0.00;
                        $rawData['temprepaty'] = 0;
                        $rawData['tempinvoice'] = 0;
                        $rawData['accountsreceivable'] = 0;//合同应收账款
                    } else {
                        $value['thisMonthlyIncome'] = '--';//本月确认收入
                        $value['isMaturity'] = '否';//是否到期
                        $value['monthlyIncome'] = '--';//每月确认收入
                        $value['cumulativeIncome'] = '--';//累计确认收入
                        $isMaturity = '否';
                        $monthlyIncome = 0.00;
                        $cumulativeIncome = 0.00;
                        $thisMonthlyIncome = 0.00;
                        $value['temprepaty'] = 0;
                        $value['tempinvoice'] = 0;
                        $value['accountsreceivable'] = 0;//合同应收账款
                    }
                    $array[] = iconv('utf-8', 'gb2312', $value['temprepaty']);
                    $array[] = iconv('utf-8', 'gb2312', $value['tempinvoice']);
                    $array[] = iconv('utf-8', 'gb2312', $monthlyIncome);
                    $array[] = iconv('utf-8', 'gb2312', $cumulativeIncome);
                    $array[] = iconv('utf-8', 'gb2312', $thisMonthlyIncome);
                    $array[] = iconv('utf-8', 'gb2312', $isMaturity);
                    $array[] = iconv('utf-8', 'gb2312', $value['accountsreceivable']);
                    $packproductname = explode('**', $value['packproductname']);
                    $countproductname = count($packproductname);
                    foreach ($packproductname as $value) {
                        $array[] = iconv('utf-8', 'gb2312', $value);
                    }
                    $countproduct = 10 - $countproductname;
                    for ($i = 0; $i < $countproduct; $i++) {
                        $array[] = iconv('utf-8', 'gb2312', '-');
                    }

                    fputcsv($fp, $array);
                    ob_flush();
                    flush();
                }
            }else{
                break;
            }
        }
        fclose($fp);
        $response=new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }
    /**
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     * 自定义导出
     */
    public function exportdataReconciliation(Vtiger_Request $request){
        set_time_limit(0);
        global $site_URL,$current_user,$root_directory,$adb;
        $recordModel = Vtiger_Record_Model::getCleanInstance('TyunWebBuyService');
        if(!$recordModel->personalAuthority('TyunWebBuyService','exportdatakai')){
            $response=new Vtiger_Response();
            $response->setError(500,"没有相关权限!");
            $response->emit();
            exit;
        }
        $listViewModel = Vtiger_ListView_Model::getInstance("TyunWebBuyService");

        $moduleMOdel=$listViewModel->getModule();
        $listViewModel->getSearchWhere();
        $query = $listViewModel->getQuery();
        $query.=$listViewModel->getUserWhere();
        // 线上需要这个打开 屏蔽测试代理商数据
        //$query.=' AND vtiger_activationcode.comeformtyun=1 AND agents<>10817';
        $query = str_replace("LEFT JOIN vtiger_tyuncontractactivacode ON vtiger_activationcode.activationcodeid = vtiger_tyuncontractactivacode.activationcodeid","",$query);
        $query = str_replace("vtiger_activationcode","vtiger_activationcode_file",$query);
        $query = str_replace("vtiger_tyuncontractactivacode","vtiger_activationcode_file",$query);
        $query.= " ORDER BY activationcodeid DESC ";
        $LISTVIEW_FIELDS = $listViewModel->getSelectFields();
        $listViewHeaders = $listViewModel->getListViewHeaders();
        $temp = array();
        if (!empty($LISTVIEW_FIELDS)) {
            foreach ($LISTVIEW_FIELDS as $key => $val) {
                if (isset($listViewHeaders[$key])) {
                    $temp[$key] = $listViewHeaders[$key];
                }
            }
        }
        if(empty($temp)) {
            $temp = $listViewHeaders;
        }
        $headerArray=$temp;
        ini_set('memory_limit','512M');
        $path=$root_directory.'temp/';
        $filename=$path.'tweb'.$current_user->id.'.csv';
        !is_dir($path)&&mkdir($path,'0777',true);
        @unlink($filename);
        $array= array();
        foreach($headerArray as $key=>$value){
            $array[]=iconv('utf-8','gb2312',vtranslate($key,'TyunWebBuyService'));
        }
        $fp=fopen($filename,'w');
        fputcsv($fp,$array);
        $listCount = $listViewModel->getListViewCount();
        $limitStep=1000;
        $num=ceil($listCount/$limitStep);
        $cnt = 0;
        $arrayint=array('orderamount','contractprice','marketprice','costprice','servicecontractstotal','invoicetotal','refundamount','paymenttotal');
        for($i=0;$i<$num;$i++) {
            $limitSQL=" limit ".$i*$limitStep.",".$limitStep;
            $result = $adb->pquery($query . $limitSQL, array());
            while ($value = $adb->fetch_array($result)) {
                $array = array();
                foreach ($headerArray as $keyheader => $valueheader){
                    if ($valueheader['uitype'] == 10) {
                        $currnetValue = uitypeformat($valueheader, $value, 'TyunWebBuyService');
                        $pattern = '/<[^>]+>/';
                        $currnetValue = preg_replace($pattern, '', $currnetValue);
                    } elseif ($valueheader['uitype'] == 54) {
                        $currnetValue = uitypeformat($valueheader, $value, 'TyunWebBuyService');
                        $pattern = '/<[^>]+>/';
                        $currnetValue = preg_replace($pattern, '',$currnetValue);
                    }elseif ($valueheader['uitype'] == 15) {
                        $currnetValue = vtranslate($value[$keyheader], 'TyunWebBuyService');
                        $pattern = '/<[^>]+>/';
                        $currnetValue = preg_replace($pattern, '', $currnetValue);
                    } else {
                        $currnetValue = uitypeformat($valueheader, $value, 'TyunWebBuyService');
                    }
                    if($keyheader=='startdate' || $keyheader=='expiredate'){
                        $currnetValue=substr($currnetValue,0,2)>0?$currnetValue:'';
                    }
                    if(!in_array($keyheader,$arrayint)){
                        $currnetValue='="'.$currnetValue.'"';
                    }

                    $currnetValue = iconv('utf-8', 'GBK//IGNORE', $currnetValue);
                    $array[] = $currnetValue;
                }
                fputcsv($fp, $array);
                ++$cnt;
                if ($limitStep == $cnt) {
                    ob_flush();
                    flush();
                    $cnt = 0;
                }
            }
        }
        fclose($fp);
        $response=new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }
    public function exportdatayun(Vtiger_Request $request){
        echo $this->getSearchWhere();
        exit;
        set_time_limit(0);
        global $site_URL,$current_user,$root_directory,$adb;
        $listViewModel = Vtiger_ListView_Model::getInstance("TyunWebBuyService");
        $moduleMOdel=$listViewModel->getModule();
        $listViewModel->getSearchWhere();
        $query = $listViewModel->getQuery();
        $query.=$listViewModel->getUserWhere();
        $query.=' AND vtiger_activationcode.comeformtyun=1';
        $LISTVIEW_FIELDS = $listViewModel->getSelectFields();
        $listViewHeaders = $listViewModel->getListViewHeaders();
        $temp = array();
        if (!empty($LISTVIEW_FIELDS)) {
            foreach ($LISTVIEW_FIELDS as $key => $val) {
                if (isset($listViewHeaders[$key])) {
                    $temp[$key] = $listViewHeaders[$key];
                }
            }
        }
        if(empty($temp)) {
            $temp = $listViewHeaders;
        }
        $headerArray=$temp;
        ini_set('memory_limit','512M');
        $path=$root_directory.'temp/';
        $filename=$path.'tweb'.$current_user->id.'.csv';
        !is_dir($path)&&mkdir($path,'0777',true);
        @unlink($filename);
        $array= array();
        foreach($headerArray as $key=>$value){
            $array[]=iconv('utf-8','gb2312',vtranslate($key,'TyunWebBuyService'));
        }
        $array[]=iconv('utf-8','gb2312','未开票金额');
        $array[]=iconv('utf-8','gb2312','未收款金额');
        $array[]=iconv('utf-8','gb2312','每月确认收入');
        $array[]=iconv('utf-8','gb2312','累计确认收入');
        $array[]=iconv('utf-8','gb2312','本月确认收入');
        $array[]=iconv('utf-8','gb2312','是否到期');
        $array[]=iconv('utf-8','gb2312','合同应收账款');
        $fp=fopen($filename,'w');
        fputcsv($fp,$array);
        $listViewModel->isAllCount=1;
        $listCount = $listViewModel->getListViewCount();
        $limitStep=1000;
        $num=ceil($listCount/$limitStep);
        $cnt = 0;
        for($i=0;$i<$num;$i++) {
            $limitSQL=" limit ".$i*$limitStep.",".$limitStep;
            $result = $adb->pquery($query . $limitSQL, array());
            while ($value = $adb->fetch_array($result)) {
                $array = array();
                foreach ($headerArray as $keyheader => $valueheader) {
                    if ($valueheader['uitype'] == 10) {
                        $currnetValue = uitypeformat($valueheader, $value, 'TyunWebBuyService');
                        $pattern = '/<[^>]+>/';
                        $currnetValue = preg_replace($pattern, '', $currnetValue);
                    } elseif ($valueheader['uitype'] == 54) {
                        $currnetValue = uitypeformat($valueheader, $value, 'TyunWebBuyService');
                        $pattern = '/<[^>]+>/';
                        $currnetValue = preg_replace($pattern, '',$currnetValue);
                    }elseif ($valueheader['uitype'] == 15) {
                        $currnetValue = vtranslate($value[$keyheader], 'TyunWebBuyService');
                        $pattern = '/<[^>]+>/';
                        $currnetValue = preg_replace($pattern, '', $currnetValue);
                    } else {
                        $currnetValue = uitypeformat($valueheader, $value, 'TyunWebBuyService');
                    }
                    $currnetValue = iconv('utf-8', 'GBK//IGNORE', $currnetValue);
                    $array[] = $currnetValue;
                }
                $orderstatus = $value['orderstatus'];
                $startdate=$value['startdate'];
                $current_date=date('Y-m',strtotime('+1 month')).'-15';
                if(!empty($startdate) && substr($startdate,0,2)>0 && strtotime($startdate)<strtotime($current_date)){
                    if($orderstatus == 'ordercancel'){
                        $docanceltime = explode('-',$value['canceldatetime']);
                        $tempmonth=$docanceltime[1]+1;
                        $docanceldate=$tempmonth>12?(($docanceltime[0]+1).'-01'):($docanceltime[0].'-'.$tempmonth);
                        $current_date=$docanceldate;
                    }
                    $currentDiffMonth=$moduleMOdel->getMonthNum(substr($startdate,0,7).'-01',substr($current_date,0,7).'-15');
                    $currentDiffMonth=$currentDiffMonth['y']*12+$currentDiffMonth['m'];
                    $maxMonth=$value['productlife']*12;
                    $diffMonth=$currentDiffMonth>$maxMonth?$maxMonth:$currentDiffMonth;
                    $monthlyIncome=$value['contractprice']/$maxMonth;
                    $monthlyIncome=number_format($monthlyIncome,2,'.','');
                    $cumulativeIncome=$diffMonth!=$maxMonth?$diffMonth*$monthlyIncome:$value['contractprice'];
                    $isMaturity = $currentDiffMonth > $maxMonth ? '是' : '否';
                    if($orderstatus == 'ordercancel') {
                        $thisMonthlyIncome = '--';
                        $isMaturity =  '是';
                    }else{
                        $thisMonthlyIncome = (empty($value['startdate']) || $currentDiffMonth > $maxMonth) ? 0.00 : $monthlyIncome;
                    }
                    $value['paymenttotal']=($value['paymenttotal']<=$value['contractprice'])?$value['paymenttotal']:$value['contractprice'];
                    $value['thisMonthlyIncome'] = $thisMonthlyIncome;//本月确认收入
                    $value['isMaturity'] = $isMaturity;//是否到期
                    $value['monthlyIncome'] = $monthlyIncome;//每月确认收入
                    $value['cumulativeIncome'] = $cumulativeIncome;//累计确认收入
                    $value['temprepaty']=bcsub($value['contractprice'],$value['paymenttotal'],2);
                    $value['tempinvoice']=bcsub($value['contractprice'],$value['invoicetotal'],2);
                    $value['accountsreceivable']=bcsub($cumulativeIncome,$value['contractprice'],2);//合同应收账款
                }elseif($orderstatus == 'ordercancel'){
                    $rawData['thisMonthlyIncome'] ='--';//本月确认收入
                    $rawData['isMaturity'] = '是';//是否到期
                    $rawData['monthlyIncome'] = '--';//每月确认收入
                    $rawData['cumulativeIncome'] = '--';//累计确认收入
                    $isMaturity='是';
                    $monthlyIncome=0.00;
                    $cumulativeIncome=0.00;
                    $thisMonthlyIncome=0.00;
                    $rawData['temprepaty']=0;
                    $rawData['tempinvoice']=0;
                    $rawData['accountsreceivable']=0;//合同应收账款
                }else{
                    $value['thisMonthlyIncome'] ='--';//本月确认收入
                    $value['isMaturity'] = '否';//是否到期
                    $value['monthlyIncome'] = '--';//每月确认收入
                    $value['cumulativeIncome'] = '--';//累计确认收入
                    $isMaturity='否';
                    $monthlyIncome=0.00;
                    $cumulativeIncome=0.00;
                    $thisMonthlyIncome=0.00;
                    $value['temprepaty']=0;
                    $value['tempinvoice']=0;
                    $value['accountsreceivable']=0;//合同应收账款
                }
                $array[]=iconv('utf-8','gb2312',$value['temprepaty']);
                $array[]=iconv('utf-8','gb2312',$value['tempinvoice']);
                $array[]=iconv('utf-8','gb2312',$monthlyIncome);
                $array[]=iconv('utf-8','gb2312',$cumulativeIncome);
                $array[]=iconv('utf-8','gb2312',$thisMonthlyIncome);
                $array[]=iconv('utf-8','gb2312',$isMaturity);
                $array[]=iconv('utf-8','gb2312',$value['accountsreceivable']);
                fputcsv($fp, $array);
                ++$cnt;
                if ($limitStep == $cnt) {
                    ob_flush();
                    flush();
                    $cnt = 0;
                }
            }
        }
        fclose($fp);
        $response=new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }
    // 获取 对账 归档提示的查询条件
    public function getSearchWhereContent(){
        $listViewModel = Vtiger_ListView_Model::getInstance("TyunWebBuyService");
        $result = $listViewModel->getSearchWhereContent();
        $response=new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
    //归档结果导出
    public function exportDataReconciliationResult(){
        set_time_limit(0);
        global $site_URL,$current_user,$root_directory,$adb;
        $recordModel = Vtiger_Record_Model::getCleanInstance('TyunWebBuyService');
        if(!$recordModel->personalAuthority('TyunWebBuyService','exportdatakai')){
            $response=new Vtiger_Response();
            $response->setError(500,"没有相关权限!");
            $response->emit();
            exit;
        }
        $listViewModel = Vtiger_ListView_Model::getInstance("TyunWebBuyService");
        $listViewModel->getSearchWhere();
        $query = $listViewModel->getQuery();
        $query.=$listViewModel->getUserWhere();
        $query.=' AND vtiger_activationcode.comeformtyun=1 AND agents<>10817';
        $LISTVIEW_FIELDS = $listViewModel->getSelectFields();
        $listViewHeaders = $listViewModel->getListViewHeaders();
        $temp = array();
        if (!empty($LISTVIEW_FIELDS)) {
            foreach ($LISTVIEW_FIELDS as $key => $val) {
                if (isset($listViewHeaders[$key])) {
                    $temp[$key] = $listViewHeaders[$key];
                }
            }
        }
        if(empty($temp)) {
            $temp = $listViewHeaders;
        }
        $headerArray=$temp;
        ini_set('memory_limit','512M');
        $path=$root_directory.'temp/';
        $filename=$path.'tweb'.$current_user->id.'.csv';
        !is_dir($path)&&mkdir($path,'0777',true);
        @unlink($filename);
        $array= array();
        foreach($headerArray as $key=>$value){
            $array[]=iconv('utf-8','gb2312',vtranslate($key,'TyunWebBuyService'));
        }
        $fp=fopen($filename,'w');
        fputcsv($fp,$array);
        $listCount = $listViewModel->getListViewCount();
        $limitStep=1000;
        $num=ceil($listCount/$limitStep);
        $cnt = 0;
        $arrayint=array('orderamount','contractprice','marketprice','costprice','servicecontractstotal','invoicetotal','refundamount','paymenttotal');

        $recorId = $_REQUEST['record'];
        $sql = " SELECT * FROM  vtiger_reconciliation_record WHERE id= ? LIMIT 1 ";
        $result = $adb->pquery($sql,array($recorId));
        $dataReconciliationResult=$adb->raw_query_result_rowdata($result,0);
          /* $dataReconciliationResult = file_get_contents("1.txt");*/
          $dataReconciliationResult =json_decode($dataReconciliationResult['content'],true);
          foreach ($dataReconciliationResult as $key=>$value){
            $array = array();
            foreach ($headerArray as $keyheader => $valueheader){
                if ($valueheader['uitype'] == 10) {
                    $currnetValue = uitypeformat($valueheader, $value, 'TyunWebBuyService');
                    $pattern = '/<[^>]+>/';
                    $currnetValue = preg_replace($pattern, '', $currnetValue);
                } elseif ($valueheader['uitype'] == 54) {
                    $currnetValue = uitypeformat($valueheader, $value, 'TyunWebBuyService');
                    $pattern = '/<[^>]+>/';
                    $currnetValue = preg_replace($pattern, '',$currnetValue);
                }elseif ($valueheader['uitype'] == 15) {
                    $currnetValue = vtranslate($value[$keyheader], 'TyunWebBuyService');
                    $pattern = '/<[^>]+>/';
                    $currnetValue = preg_replace($pattern, '', $currnetValue);
                } else {
                    $currnetValue = uitypeformat($valueheader, $value, 'TyunWebBuyService');
                }
                if($keyheader=='startdate' || $keyheader=='expiredate'){
                    $currnetValue=substr($currnetValue,0,2)>0?$currnetValue:'';
                }
                if(!in_array($keyheader,$arrayint)){
                    $currnetValue='="'.$currnetValue.'"';
                }
                $currnetValue = iconv('utf-8', 'GBK//IGNORE', $currnetValue);
                $array[] = $currnetValue;
            }
            fputcsv($fp, $array);
            ++$cnt;
            if ($limitStep == $cnt) {
                ob_flush();
                flush();
                $cnt = 0;
            }
        }
        fclose($fp);
        $response=new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }
    /**
     * 写日志，用于测试,可以开启关闭
     * @param data mixed
     */
    public function _logs($data, $file = 'logs_'){
        $year	= date("Y");
        $month	= date("m");
        $dir	= './Logs/' . $year . '/' . $month . '/';
        if(!is_dir($dir)) {
            mkdir($dir,0755,true);
        }
        $file = $dir . $file . date('Y-m-d').'.txt';
        @file_put_contents($file, '----------------' . date('H:i:s') . '--------------------'.PHP_EOL.var_export($data,true).PHP_EOL, FILE_APPEND);
    }
    //开始归档
    public function startToFiled()
    {
        $db = PearDatabase::getInstance();
        global  $current_user;
        $listViewModel = Vtiger_ListView_Model::getInstance("TyunWebBuyService");
        $orderBy = $listViewModel->getForSql('orderby');
        $sortOrder = $listViewModel->getForSql('sortorder');
        //List view will be displayed on recently created/modified records
        //列表视图将显示最近的创建修改记录  ---做什么用处
        if (empty($orderBy) && empty($sortOrder)) {
            $orderBy = 'activationcodeid';
            $sortOrder = 'DESC';
        }
        $listViewModel->getSearchWhere();
        $listQuery = $listViewModel->getQuery();
        $listQuery .= $listViewModel->getUserWhere();
        $listQuery = str_replace(',vtiger_activationcode.activationcodeid', ',vtiger_activationcode.contractid,vtiger_activationcode.activationcodeid', $listQuery);
        $listQuery .= " AND vtiger_activationcode.comeformtyun=1";
        $listQueryCount = $listQuery . ' AND (vtiger_activationcode.reconciliationresult=\'no_reconciliation\' or vtiger_activationcode.reconciliationresult=\'error_reconciliation\') ';
        // 根据一筛选条件查询 没有对账的 或者对账 未成功的
        $listResult = $db->pquery($listQueryCount, array());
        $count = $db->num_rows($listResult);
        //如果大于零则表示 包含未对账 或者对账失败的数据
        if ($count > 0) {
            $result = array("success"=>0,'data' => '未进行对账或者对账失败，不支持归档！');
        }else{
            //替换要查询的需要归档的数据字段切记不可用 select * 因为left join的表包含 相同字段
            $listQuery = preg_replace('/SELECT.*?FROM vtiger_activationcode/', 'SELECT 
              vtiger_activationcode.`activationcodeid`,       
              vtiger_activationcode.`activedate`,
              vtiger_activationcode.`activecode`,
              vtiger_activationcode.`contractid`,
              vtiger_activationcode.`expiredate`,
              vtiger_activationcode.`contractname`,
              vtiger_activationcode.`customerid`,
              vtiger_activationcode.`customername`,
              vtiger_activationcode.`agents`,
              vtiger_activationcode.`productlife`,
              vtiger_activationcode.`productid`,
              vtiger_activationcode.`address`,
              vtiger_activationcode.`mobile`,
              vtiger_activationcode.`salesname`,
              vtiger_activationcode.`salesphone`,
              vtiger_activationcode.`status`,
              vtiger_activationcode.`classtype`,
              vtiger_activationcode.`customerstype`,
              vtiger_activationcode.`companyname`,
              vtiger_activationcode.`originalcontractname`,
              vtiger_activationcode.`originalcontractid`,
              vtiger_activationcode.`usercode`,
              vtiger_activationcode.`oldproductid`,
              vtiger_activationcode.`resultmsg`,
              vtiger_activationcode.`upgradeDate`,
              vtiger_activationcode.`receivetime`,
              vtiger_activationcode.`onlinetime`,
              vtiger_activationcode.`createdtime`,
              vtiger_activationcode.`buyserviceinfo`,
              vtiger_activationcode.`buyid`,
              vtiger_activationcode.`checkstatus`,
              vtiger_activationcode.`reason`,
              vtiger_activationcode.`pushstatus`,
              vtiger_tyuncontractactivacode.`contractstatus`,
              vtiger_activationcode.`startdate`,
              vtiger_activationcode.`creator`,
              vtiger_activationcode.`adddate`,
              vtiger_activationcode.`ordercode`,
              vtiger_activationcode.`paycode`,
              vtiger_activationcode.`comeformtyun`,
              vtiger_activationcode.`onoffline`,
              vtiger_activationcode.`productclass`,
              vtiger_activationcode.`orderstatus`,
              vtiger_activationcode.`modulestatus`,
              vtiger_activationcode.`customerid` as `serviceid`,
              vtiger_tyuncontractactivacode.`signdate`,
              vtiger_activationcode.`paymentcode`,
              vtiger_activationcode.`orderamount`,
              vtiger_activationcode.`refundamount`,
              vtiger_activationcode.`iscomreconciliation`,
              vtiger_activationcode.`issign`,
              vtiger_activationcode.`paymentstatus`,
              vtiger_activationcode.`paymentamount`,
              vtiger_activationcode.`buyseparately`,
              vtiger_activationcode.`productnames`,
              vtiger_activationcode.`usercodeid`,
              vtiger_activationcode.`contractprice`,
              vtiger_activationcode.`productname`,
              vtiger_activationcode.`canceldatetime`,
              vtiger_activationcode.`paymentno`,
              vtiger_activationcode.`marketprice`,
              vtiger_activationcode.`costprice`,
              vtiger_activationcode.`isdisabled`,
              vtiger_activationcode.`isclientmigration`,
              vtiger_activationcode.`surplusmoney`,
              vtiger_activationcode.`upgradecontractprice`,
              vtiger_activationcode.`upgradetransfer`,
              vtiger_activationcode.`oldproductname`,
              vtiger_activationcode.`orderordercode`,
              vtiger_activationcode.`oldsurplusmoney`,
              vtiger_activationcode.`sumsurplusmoney`,
              vtiger_tyuncontractactivacode.`servicecontractsid`,
              vtiger_tyuncontractactivacode.`contract_no`,
              vtiger_tyuncontractactivacode.`signdempart`,
              vtiger_tyuncontractactivacode.`signid`,
              vtiger_tyuncontractactivacode.`accountname`,
              vtiger_tyuncontractactivacode.`accountid`,
              vtiger_tyuncontractactivacode.`productidt`,
              vtiger_tyuncontractactivacode.`tyunative`,
              vtiger_tyuncontractactivacode.`paymentsituation`,
              vtiger_tyuncontractactivacode.`invoicesituation`,
              vtiger_tyuncontractactivacode.`refund`,
              vtiger_tyuncontractactivacode.`servicecontractstotal`,
              vtiger_tyuncontractactivacode.`paymenttotal`,
              vtiger_tyuncontractactivacode.`invoicetotal`,
              vtiger_tyuncontractactivacode.`invoiceid`,
              vtiger_tyuncontractactivacode.`orderchargebackid`,
              vtiger_tyuncontractactivacode.`invoicestatus`,
              vtiger_tyuncontractactivacode.`secondstageroyalty`,
              vtiger_tyuncontractactivacode.`icollectiontime`,
              vtiger_tyuncontractactivacode.`dividedproportion`,
              vtiger_tyuncontractactivacode.`downpayment`,
              vtiger_tyuncontractactivacode.`dptcommission`,
              vtiger_tyuncontractactivacode.`downpaymenttime`,
              vtiger_tyuncontractactivacode.`retainage`,
              vtiger_tyuncontractactivacode.`taildeduction`,
              vtiger_tyuncontractactivacode.`tailtime`,
              vtiger_tyuncontractactivacode.`arrivedaccount`,
              vtiger_tyuncontractactivacode.`timeofarrival`,
              vtiger_tyuncontractactivacode.`unpaidaccount`,
              vtiger_tyuncontractactivacode.`accountrank`,
              vtiger_tyuncontractactivacode.`docanceltime`,
              vtiger_tyuncontractactivacode.`departmentname`,
              vtiger_tyuncontractactivacode.`invoicecompany`,
              vtiger_tyuncontractactivacode.`purchasequantity`,
              vtiger_tyuncontractactivacode.`paymentratio`,
              vtiger_tyuncontractactivacode.`lastpaymenttime`,
              vtiger_tyuncontractactivacode.`matchtime`,
              vtiger_tyuncontractactivacode.`paymentmethod`,
              vtiger_tyuncontractactivacode.`matchdepartmentid`,
              vtiger_tyuncontractactivacode.`camountrepayment`,
              vtiger_tyuncontractactivacode.`camountrepayment1`,
              vtiger_activationcode.`isitfiled`,
              vtiger_activationcode.`reconciliationresult`
              FROM vtiger_activationcode ', $listQuery);
            // 查询出所有满足条件的数据的id
            $listResult = $db->pquery($listQuery, array());
            $rowData =array();
            while($rawData=$db->fetch_array($listResult)) {
                $rowId[]=$rawData['activationcodeid'];
                $rowData[$rawData['activationcodeid']]=$rawData;
                if($rawData['isitfiled']=='yes_filed'){
                    $result = array("success"=>0,'data' => '不支持重复归档');
                    $response=new Vtiger_Response();
                    $response->setResult($result);
                    $response->emit();exit();
                }
            }
            $this->_logs(array("获取的所有要归档的数据的id1：", $rowId));
            //添加变量$rowIdStr ,连接$rowId
              $rowIdStr=implode(',',$rowId);
              //查询出已经归档过的数据
              $hasFiled =" SELECT * FROM  vtiger_activationcode_file WHERE   activationcodeid IN(".$rowIdStr.")";
              $hasFiled =$db->pquery($hasFiled,array());
              $hasFiledData= array();
              while($rawData=$db->fetch_array($hasFiled)) {
                  $hasFiledID[]=$rawData['activationcodeid'];
                  $hasFiledData[$rawData['activationcodeid']]=$rawData;
              }
              //  第一步开始归档数据
                $filedSql = "REPLACE INTO vtiger_activationcode_file  (
              `activationcodeid`,
              `activedate`,
              `activecode`,
              `contractid`,
              `expiredate`,
              `contractname`,
              `customerid`,
              `customername`,
              `agents`,
              `productlife`,
              `productid`,
              `address`,
              `mobile`,
              `salesname`,
              `salesphone`,
              `status`,
              `classtype`,
              `customerstype`,
              `companyname`,
              `originalcontractname`,
              `originalcontractid`,
              `usercode`,
              `oldproductid`,
              `resultmsg`,
              `upgradeDate`,
              `receivetime`,
              `onlinetime`,
              `createdtime`,
              `buyserviceinfo`,
              `buyid`,
              `checkstatus`,
              `reason`,
              `pushstatus`,
              `contractstatus`,
              `startdate`,
              `creator`,
              `adddate`,
              `ordercode`,
              `paycode`,
              `comeformtyun`,
              `onoffline`,
              `productclass`,
              `orderstatus`,
              `modulestatus`,
              `serviceid`,
              `signdate`,
              `paymentcode`,
              `orderamount`,
              `refundamount`,
              `iscomreconciliation`,
              `issign`,
              `paymentstatus`,
              `paymentamount`,
              `buyseparately`,
              `productnames`,
              `usercodeid`,
              `contractprice`,
              `productname`,
              `canceldatetime`,
              `paymentno`,
              `marketprice`,
              `costprice`,
              `isdisabled`,
              `isclientmigration`,
              `surplusmoney`,
              `upgradecontractprice`,
              `upgradetransfer`,
              `oldproductname`,
              `orderordercode`,
              `oldsurplusmoney`,
              `sumsurplusmoney`,
               `servicecontractsid`,
              `contract_no`,
              `signdempart`,
              `signid`,
              `accountname`,
              `accountid`,
              `productidt`,
              `tyunative`,
              `paymentsituation`,
              `invoicesituation`,
              `refund`,
              `servicecontractstotal`,
              `paymenttotal`,
              `invoicetotal`,
              `invoiceid`,
              `orderchargebackid`,
              `invoicestatus`,
              `secondstageroyalty`,
              `icollectiontime`,
              `dividedproportion`,
              `downpayment`,
              `dptcommission`,
              `downpaymenttime`,
              `retainage`,
              `taildeduction`,
              `tailtime`,
              `arrivedaccount`,
              `timeofarrival`,
              `unpaidaccount`,
              `accountrank`,
              `docanceltime`,
              `departmentname`,
              `invoicecompany`,
              `purchasequantity`,
              `paymentratio`,
              `lastpaymenttime`,
              `matchtime`,
              `paymentmethod`,
              `matchdepartmentid`,
              `camountrepayment`,
              `camountrepayment1`,
              `isitfiled`,
              `reconciliationresult`
              ) " . $listQuery;
             //归档插入更新
             $listResult = $db->pquery($filedSql, array());
            //组装所有字段
            $fieldColumn = array(activationcodeid,
                activedate,
                activecode,
                contractid,
                expiredate,
                contractname,
                customerid,
                customername,
                agents,
                productlife,
                productid,
                address,
                mobile,
                salesname,
                salesphone,
                status,
                classtype,
                customerstype,
                companyname,
                originalcontractname,
                originalcontractid,
                usercode,
                oldproductid,
                resultmsg,
                upgradeDate,
                receivetime,
                onlinetime,
                createdtime,
                buyserviceinfo,
                buyid,
                checkstatus,
                reason,
                pushstatus,
                contractstatus,
                startdate,
                creator,
                adddate,
                ordercode,
                paycode,
                comeformtyun,
                onoffline,
                productclass,
                orderstatus,
                modulestatus,
                serviceid,
                signdate,
                paymentcode,
                orderamount,
                refundamount,
                iscomreconciliation,
                issign,
                paymentstatus,
                paymentamount,
                buyseparately,
                productnames,
                usercodeid,
                contractprice,
                productname,
                canceldatetime,
                paymentno,
                marketprice,
                costprice,
                isdisabled,
                isclientmigration,
                surplusmoney,
                upgradecontractprice,
                upgradetransfer,
                oldproductname,
                orderordercode,
                oldsurplusmoney,
                sumsurplusmoney,
                servicecontractsid,
                contract_no,
                signdempart,
                signid,
                accountname,
                accountid,
                productidt,
                tyunative,
                paymentsituation,
                invoicesituation,
                refund,
                servicecontractstotal,
                paymenttotal,
                invoicetotal,
                invoiceid,
                orderchargebackid,
                invoicestatus,
                secondstageroyalty,
                icollectiontime,
                dividedproportion,
                downpayment,
                dptcommission,
                downpaymenttime,
                retainage,
                taildeduction,
                tailtime,
                arrivedaccount,
                timeofarrival,
                unpaidaccount,
                accountrank,
                docanceltime,
                departmentname,
                invoicecompany,
                purchasequantity,
                paymentratio,
                lastpaymenttime,
                matchtime,
                paymentmethod,
                matchdepartmentid,
                camountrepayment,
                camountrepayment1,isitfiled,reconciliationresult);
            // 日志记录表初始数据
            $valueData=array();
            $seizeASeat='';
            // 日志记录详情初始数据
            $valueDataDetail=array();
            $seizeASeatDetail='';
            $currentTime = date("Y-m-d H:i:s");
            //如果这些id的都已经归档过了则要 记录更新日志
            if(!empty($hasFiledID)){
                foreach ($hasFiledID as $key=>$value){
                      //先获取日志记录表的 一个唯一id
                      $id = $db->getUniqueId('vtiger_modtracker_basic');
                      // 组装日志记录表数据
                      $seizeASeat.='(?,?,?,?,?,?),';
                      $valueData[]=$id;
                      $valueData[]=$value;
                      $valueData[]='TyunWebBuyService';
                      $valueData[]=$current_user->id;
                      $valueData[]=$currentTime;
                      $valueData[]=0;
                      foreach ($fieldColumn as $key=>$val){
                           // 如果归档前的数据和已经归档的数据相比较 如果不相等 则记录字段更新日志
                           if($rowData[$value][$val]!=$hasFiledData[$value][$val] && $rowData[$value][$val]!==null){
                               // 组装日志详情数据
                               $seizeASeatDetail.='(?,?,?,?),';
                               $valueDataDetail[]=$id;
                               $valueDataDetail[]=$val;
                               $valueDataDetail[]=$hasFiledData[$value][$val];
                               $valueDataDetail[]=$rowData[$value][$val];
                           }
                      }
                }
                $seizeASeat=trim($seizeASeat,',');
                $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES '.$seizeASeat,$valueData);
                // 插入字段修改记录详情
                $seizeASeatDetail=trim($seizeASeatDetail,',');
                $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES  '.$seizeASeatDetail,$valueDataDetail);
            }
            // 获取$rowId中不包含$hasFiledID已经归档过的数据的id $noFiledID 则记录这些未归过档的保存日志记录
            if($hasFiledID){
                $noFiledID = array_diff($rowId,$hasFiledID);
            }else{
                $noFiledID=$rowId;
            }
            if(!empty($noFiledID)){
                $seizeASeat='';
                $valueData=array();
                foreach ($noFiledID as $key=>$value){
                    //先获取日志记录表的 一个唯一id
                    $id = $db->getUniqueId('vtiger_modtracker_basic');
                    // 组装日志记录表数据
                    $seizeASeat.='(?,?,?,?,?,?),';
                    $valueData[]=$id;
                    $valueData[]=$value;
                    $valueData[]='TyunWebBuyService';
                    $valueData[]=$current_user->id;
                    $valueData[]=$currentTime;
                    $valueData[]=0;
                }
                $seizeASeat=trim($seizeASeat,',');
                $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES '.$seizeASeat,$valueData);
            }
            /*$rowId = implode(',',$rowId);*/
            $this->_logs(array("最后更改时id2：", $rowId));
            $updateIsFiledSql=" UPDATE  vtiger_activationcode SET isitfiled='yes_filed' WHERE  activationcodeid IN(".$rowIdStr.")";
            $db->pquery($updateIsFiledSql,array());
            $updateIsFiledSql=" UPDATE  vtiger_activationcode_file SET isitfiled='yes_filed',reconciliationresult='success_reconciliation' WHERE activationcodeid IN(".$rowIdStr.")";
            $db->pquery($updateIsFiledSql,array());
            $result = array("success"=>1,'data' => '归档成功');
        }
        $response=new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }


    /**
     * 手动创建电子合同
     * 20200420
     * junwei.nie
     *
     * @param Vtiger_Request $request
     */
    public function confirmCreateServiceContract(Vtiger_Request $request){
        global $current_user;
        $recordId = $request->get('recordid');
        $response = new Vtiger_Response();

        $isShowCreateServiceContract = TyunWebBuyService_Record_Model::isShowCreateServiceContract($recordId,$current_user->id);
        if(!$isShowCreateServiceContract){
            $data = array(
                'success'=>false,
                "msg"=>"无权操作手动创建电子合同"
            );
            $response->setResult($data);
            $response->emit();
            exit;
        }
        $TyunRecordModel = TyunWebBuyService_Record_Model::getInstanceById($recordId,'TyunWebBuyService');
        $ordercode = $TyunRecordModel->get('ordercode');

        //创建电子合同
        $recordModel = ServiceContracts_Record_Model::getCleanInstance("ServiceContracts");
        $request->set('ordercode',$ordercode);
        $result = $recordModel->createTyunServiceContracts($request);

        if (!$result['success']){
            $data = array(
                'success'=>false,
                "msg"=>$result['message']
            );
            $response->setResult($data);
            $response->emit();
            exit();
        }

        $data = array(
            'success'=>true,
            "msg"=>"手动创建电子合同成功"
        );
        $response->setResult($data);
        $response->emit();
    }
    /**
     * 添加更新代理商ID
     * @param $request
     */
    public function addAgentid($request){
        global $adb;
        $recordModel = Vtiger_Record_Model::getCleanInstance('TyunWebBuyService');
        if(!$recordModel->personalAuthority('TyunWebBuyService','setagent')){
            exit;
        }
        $department=$request->get('department');
        $userid=$request->get('userid');
        $email=$request->get('email');
        $email=trim($email);
        $agentid=$request->get('agentid');
        $agentid=trim($agentid);
        $pdid=$request->get('pdid');
        $query='SELECT parentdepartment FROM vtiger_departments WHERE departmentid=?';
        $result=$adb->pquery($query,array($department));
        $pdepartmentid=$result->fields['parentdepartment'];
        if($userid!='null'){
            $userid=implode(',',$userid);
        }else{
            $userid='';
        }
        if($pdid>0){
            $sql="UPDATE vtiger_departmentragentid SET pdepartmentid=?,agentid=?,userids=?,email=?,departmentid=? WHERE departmentagentid=?";
            $adb->pquery($sql,array($pdepartmentid,$agentid,$userid,$email,$department,$pdid));
        }else{
            $result=$adb->pquery('SELECT * FROM vtiger_departmentragentid WHERE departmentid=?',array($department));
            if($adb->num_rows($result)){
                $pdid=$result->fields['departmentagentid'];
                $sql="UPDATE vtiger_departmentragentid SET pdepartmentid=?,agentid=?,userids=?,email=?,departmentid=? WHERE departmentagentid=?";
                $adb->pquery($sql,array($pdepartmentid,$agentid,$userid,$email,$department,$pdid));
            }else{
                $sql="INSERT INTO vtiger_departmentragentid (`pdepartmentid`,`agentid`,`userids`,`email`,`departmentid`) VALUES (?,?,?,?,?)";
                $adb->pquery($sql,array($pdepartmentid,$agentid,$userid,$email,$department));
            }
        }
        $response=new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }
    public function updateAgentid($request){
        global $adb;
        $sql="UPDATE vtiger_departmentragentid,vtiger_departments SET pdepartmentid=parentdepartment WHERE vtiger_departmentragentid.departmentid=vtiger_departments.departmentid";
        $adb->pquery($sql,array());
        $response=new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }

    /**
     * 删除代理商ID
     * @param $request
     */
    public function delAgentid($request){
        global $adb;
        $recordModel = Vtiger_Record_Model::getCleanInstance('TyunWebBuyService');
        if(!$recordModel->personalAuthority('TyunWebBuyService','setagent')){
            exit;
        }
        $did=$request->get('did');
        $query='DELETE FROM `vtiger_departmentragentid` WHERE departmentagentid=?';
        $adb->pquery($query,array($did));
    }
}
