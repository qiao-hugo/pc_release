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
class TyunWebBuyService_Record_Model extends Vtiger_Record_Model {
    //private $tyunweburl='http://121.46.194.176/';//web端地址线上
    //private $tyunweburl='http://121.46.194.176/';//web端地址测试
    /**
     * @param $request
     */
    public $ProductType=array(1=>'buy',2=>'upgrade',5=>'upgrade',3=>'degrade',6=>'degrade',4=>'renew',7=>'renew');
    public function getCrmUserBasicInfo($request){
        global $adb;
        $lastName=$request->get('lastName');
        $query="SELECT
                vtiger_users.user_name,
                vtiger_users.is_admin,
                vtiger_users.last_name,
                vtiger_user2role.roleid,
                vtiger_users.email1,
                vtiger_users.`status`,
                vtiger_users.title,
                vtiger_users.phone_work,
                vtiger_users.department,
                vtiger_users.phone_mobile,
                vtiger_users.reports_to_id,
                vtiger_users.phone_other,
                vtiger_users.email2,
                vtiger_users.phone_fax,
                vtiger_users.secondaryemail,
                vtiger_users.phone_home,
                vtiger_users.address_city,
                vtiger_users.address_state,
                vtiger_users.address_postalcode,
                vtiger_users.user_sys,
                vtiger_user2department.departmentid,
                vtiger_user2role.secondroleid,
                vtiger_users.usermodifiedtime,
                vtiger_users.usercode,
                vtiger_users.user_entered,
                vtiger_users.fillinsales,
                vtiger_users.brevitycode,
                vtiger_users.leavedate,
                vtiger_users.isdimission,
                vtiger_users.stafftype,
                vtiger_users.graduatetime as graduation_time,
                vtiger_users.postintime as new_barrack_time,
                vtiger_users.id
            FROM
                vtiger_users
            INNER JOIN vtiger_user2role ON vtiger_users.id = vtiger_user2role.userid
            INNER JOIN vtiger_user2department ON vtiger_users.id = vtiger_user2department.userid
            WHERE
                vtiger_users.id >0
            AND departmentid != ''
            AND last_name like ?
            AND `status`='Active'";
        $result=$adb->pquery($query,array($lastName.'%'));
        $rows = $adb->num_rows($result);
        $ret_lists = array();
        if ($rows>0){
            while ($row = $adb->fetchByAssoc($result)) {
                $lists = array();
                $lists['user_name'] = $row['user_name'];
                $lists['is_admin'] = $row['is_admin'];
                $lists['last_name'] = $row['last_name'];
                $lists['email'] = $row['email1'];
                $lists['status'] = $row['status'];
                $lists['title'] = $row['title'];
                $lists['phone_work'] = $row['phone_work'];
                $lists['department'] = $row['department'];
                $lists['phone_mobile'] = $row['phone_mobile'];
                $lists['department'] = $row['department'];
                $lists['reports_to_id'] = $row['reports_to_id'];
                $lists['phone_other'] = $row['phone_other'];
                $lists['usercode'] = $row['usercode'];
                $lists['user_entered'] = $row['user_entered'];
                $lists['brevitycode'] = $row['brevitycode'];
                $lists['leavedate'] = $row['leavedate'];
                $lists['isdimission'] = $row['isdimission'];
                $lists['graduation_time'] = $row['graduation_time'];
                $lists['new_barrack_time'] = $row['new_barrack_time'];
                $lists['departmentid'] = $row['departmentid'];
                $lists['stafftype'] = $row['stafftype'];
                $lists['id'] = $row['id'];
                $ret_lists[] = $lists;
            }
        }
        return $ret_lists;
    }
    /**
     * cxh 2020-07-27
     * 获取首购购和续费  市场价格 成本
     * */
    public function getMarketPriceBuyAndRenew(&$detailProducts,&$firstPurchase,&$order,&$detail,$type){
        $content=$detailProducts['Content'];
        $content=json_decode($content,true);
        if($firstPurchase['firstPurchaseCostPrice']<0){
            $firstPurchase['firstPurchaseCostPrice']=0;
        }
        $ProductType=$this->ProductType[$detail['ProductType']];
        $count=$detailProducts['Count'];
        $count=$count>1?$count:1;
        $IsGift=0;
        $actualMarketPrice=0;
        $actualCostPrice=0;
        //`ActivityModel` int(11) NOT NULL COMMENT '活动模式 1新购活动 2升级活动 4续费活动',
        //`ActivityType` int(11) NOT NULL COMMENT '活动类型 1优惠组合 2赠送产品 3赠送时间 4限时打折',
        // 如果是否是直销改价判定  然后修改首购市场价格
        $isdirectsellingtoprice=0;//是否直销改价，0 否，1 是
        if($order['Money']!=$order['OriginalMoney'] && empty($detailProducts['ActivityID'])){
            //如果是续费修改市场价格 为当前实际市场价格 和 实际成本
            $originalMoney=$detailProducts['MarketPrice']+($detailProducts['BuyTerm']-1)*$detailProducts['RenewPrice'];
            $actualMarketPrice=$order['Money'];
            if($ProductType=='renew') {//续费的年限*成本
                $actualCostPrice =$detailProducts['BuyTerm']  * $detailProducts['CostRenewPrice'];
                $firstPurchase['firstPurchasePrice']=$detailProducts['RevisionPrice'];//直销改价首购市场价
                $firstPurchase['firstPurchaseRenewPrice']=$detailProducts['RevisionPrice'];//直销改价续费市场价
            }else{
                $actualCostPrice = $detailProducts['CostPrice'] + ($detailProducts['BuyTerm'] - 1) * $detailProducts['CostRenewPrice'];
                $firstPurchase['firstPurchasePrice']=$detailProducts['RevisionPrice'];//直销改价首购市场价
                $firstPurchase['firstPurchaseRenewPrice']=$actualMarketPrice-$detailProducts['RevisionPrice'];//直销改价续费市场价
            }
            $isdirectsellingtoprice=1;
            //$firstPurchase['firstPurchasePrice']=($firstPurchase['firstPurchasePrice']/$originalMoney) * $order['Money'];
            //$firstPurchase['firstPurchasePrice']=$detailProducts['RevisionPrice'];//直销改价首购市场价
            //$firstPurchase['firstPurchaseRenewPrice']=$actualMarketPrice-$detailProducts['RevisionPrice'];//直销改价续费市场价
        }

        // 如果赠送产品
        if($detailProducts['ActivityType']==2){
            switch($detailProducts['ActivityModel']){
                case 4:
                    if($detailProducts['IsGift']==true){
                        $IsGift=1;//
                        if(isset($content['package'])){
                            $actualCostPrice=$detailProducts['BuyTerm']*$content['package']['CostRenewPrice'];//
                        }else{
                            $actualCostPrice=$detailProducts['BuyTerm']*$content['renewpackage']['CostRenewPrice'];//
                        }
                    }else{
                        $actualMarketPrice=$detailProducts['BuyTerm']*$firstPurchase['firstPurchaseRenewPrice'];//
                        $actualCostPrice=$detailProducts['BuyTerm']*$firstPurchase['firstPurchaseCostRenewPrice'];//
                    }
                    break;
                default:
                    if($detailProducts['IsGift']==true){
                        $IsGift=1;//
                        if(isset($content['package'])){
                            $actualCostPrice=$content['package']['CostPrice']+($detailProducts['BuyTerm']-1)*$content['package']['CostRenewPrice'];//
                        }else{
                            $actualCostPrice=$content['renewpackage']['CostPrice']+($detailProducts['BuyTerm']-1)*$content['renewpackage']['CostRenewPrice'];//
                        }
                    }else{
                        $actualMarketPrice=$firstPurchase['firstPurchasePrice']+($detailProducts['BuyTerm']-1)*$firstPurchase['firstPurchaseRenewPrice'];//
                        $actualCostPrice=$firstPurchase['firstPurchaseCostPrice']+($detailProducts['BuyTerm']-1)*$firstPurchase['firstPurchaseCostRenewPrice'];//
                    }
            }

        }
        //限时折扣
        if($detailProducts['ActivityType']==4){
            switch($detailProducts['ActivityModel']){
                case 4:
                    $actualMarketPrice=$detailProducts['BuyTerm']*$firstPurchase['firstPurchaseRenewPrice'];//
                    $actualCostPrice=$detailProducts['BuyTerm']*$firstPurchase['firstPurchaseCostRenewPrice'];//
                    break;
                default:
                    $actualMarketPrice=$firstPurchase['firstPurchasePrice']+($detailProducts['BuyTerm']-1)*$firstPurchase['firstPurchaseRenewPrice'];//
                    $actualCostPrice=$firstPurchase['firstPurchaseCostPrice']+($detailProducts['BuyTerm']-1)*$firstPurchase['firstPurchaseCostRenewPrice'];//
            }

        }
        // 如果是优惠组合 重新计算 首购市场价格
        if(in_array($detailProducts['ActivityType'],array(1))){
            switch($detailProducts['ActivityModel']){
                case 1:
                case 2:
                    break;
                case 4:
                    $actualMarketPrice=$detailProducts['BuyTerm']*$firstPurchase['firstPurchaseRenewPrice'];//
                    $actualCostPrice=$detailProducts['BuyTerm']*$firstPurchase['firstPurchaseCostRenewPrice'];//
                    break;
                default:
                    $firstPurchase['firstPurchasePrice']=($firstPurchase['firstPurchasePrice']/$order['OriginalMoney']) * $order['Money'];
                    $firstPurchase['firstPurchaseRenewPrice']=($firstPurchase['firstPurchaseRenewPrice']/$order['OriginalMoney']) * $order['Money'];
            }

        }
        if(in_array($detailProducts['ActivityType'],array(5))){
            switch($detailProducts['ActivityModel']){
                case 1:
                case 2:
                    break;
                case 4:
                    $actualMarketPrice=$detailProducts['BuyTerm']*$firstPurchase['firstPurchaseRenewPrice'];//
                    $actualCostPrice=$detailProducts['BuyTerm']*$firstPurchase['firstPurchaseCostRenewPrice'];//
                    break;
                default:
                    //$firstPurchase['firstPurchasePrice']=($firstPurchase['firstPurchasePrice']/$order['OriginalMoney']) * $order['Money'];
                    //$firstPurchase['firstPurchaseRenewPrice']=($firstPurchase['firstPurchaseRenewPrice']/$order['OriginalMoney']) * $order['Money'];
            }

        }
        // 如果是赠送时间市场价格修改  重新计算 市场价格 成本
        if($detailProducts['ActivityType']==3){
            $activityYears= $detailProducts['ActivityThresholdBuyTerm']+$detailProducts['GiveTerm']; //买几年赠几年之和
            // 购买送的市场价格不计算就可以了
            /*$activityMarketPrice=$firstPurchase['firstPurchasePrice']+($detailProducts['BuyTerm']-$detailProducts['GiveTerm']-1)*$firstPurchase['firstPurchaseRenewPrice'];// 只算非送的市场价格总和
            $activityCostPrice=$firstPurchase['firstPurchaseCostPrice']+($activityYears-1)*$firstPurchase['firstPurchaseCostRenewPrice'];
            // 原价购买
            $noActivityCostPrice=($detailProducts['BuyTerm']-$activityYears)*$content['package']['CostRenewPrice'];
            // 汇总
            $actualMarketPrice=$activityMarketPrice;
            $actualCostPrice=$activityCostPrice+$noActivityCostPrice;*/


            switch($detailProducts['ActivityModel']){
                case 4:
                    $activityMarketPrice=($detailProducts['BuyTerm']-$detailProducts['GiveTerm'])*$firstPurchase['firstPurchaseRenewPrice'];// 只算非送的市场价格总和
                    $activityCostPrice=$activityYears*$firstPurchase['firstPurchaseCostRenewPrice'];
                    // 原价购买
                    //$noActivityCostPrice=($detailProducts['BuyTerm']-$activityYears)*$content['package']['CostRenewPrice'];
                    $noActivityCostPrice=0;
                    // 汇总
                    $actualMarketPrice=$activityMarketPrice;
                    $actualCostPrice=$activityCostPrice+$noActivityCostPrice;
                    break;
                default:
                    // 购买送的市场价格不计算就可以了
                    $activityMarketPrice=$firstPurchase['firstPurchasePrice']+($detailProducts['BuyTerm']-$detailProducts['GiveTerm']-1)*$firstPurchase['firstPurchaseRenewPrice'];// 只算非送的市场价格总和
                    $activityCostPrice=$firstPurchase['firstPurchaseCostPrice']+($activityYears-1)*$firstPurchase['firstPurchaseCostRenewPrice'];
                    // 原价购买
                    $noActivityCostPrice=($detailProducts['BuyTerm']-$activityYears)*$content['package']['CostRenewPrice'];
                    // 汇总
                    $actualMarketPrice=$activityMarketPrice;
                    $actualCostPrice=$activityCostPrice+$noActivityCostPrice;
            }
        }
        /*if($detailProducts['PackageID']>0){
            if(isset($content['package'])){
                return array('oneMarketPrice'=>$content['package']['DirectPrice'],'oneMarketRenewPrice'=>$content['package']['DirectRenewPrice'],'oneCostPrice'=>$content['package']['CostPrice'],'oneCostRenewPrice'=>$content['package']['CostRenewPrice'],'CanRenew'=>false);
                return array('oneMarketPrice'=>$firstPurchase['firstPurchasePrice'],'oneMarketRenewPrice'=>$firstPurchase['firstPurchaseRenewPrice'],'oneCostPrice'=>$firstPurchase['firstPurchaseCostPrice'],'oneCostRenewPrice'=>$firstPurchase['firstPurchaseCostRenewPrice'],'CanRenew'=>false);
            }else{
                return array('oneMarketPrice'=>$content['renewpackage']['DirectPrice'],'oneMarketRenewPrice'=>$content['renewpackage']['DirectRenewPrice'],'oneCostPrice'=>$content['renewpackage']['CostPrice'],'oneCostRenewPrice'=>$content['renewpackage']['CostRenewPrice'],'CanRenew'=>false);
                return array('oneMarketPrice'=>$firstPurchase['firstPurchasePrice'],'oneMarketRenewPrice'=>$firstPurchase['firstPurchaseRenewPrice'],'oneCostPrice'=>$firstPurchase['firstPurchaseCostPrice'],'oneCostRenewPrice'=>$firstPurchase['firstPurchaseCostRenewPrice'],'CanRenew'=>false);
            }
            // 另购单品
        }else{
            return array('oneMarketPrice'=>$content['Specification']['DirectPrice'],'oneMarketRenewPrice'=>$content['Specification']['DirectRenewPrice'],'oneCostPrice'=>$content['Specification']['CostPrice'],'oneCostRenewPrice'=>$content['Specification']['CostRenewPrice'],'CanRenew'=>$content['Product']['CanRenew']);
            return array('oneMarketPrice'=>$firstPurchase['firstPurchasePrice'],'oneMarketRenewPrice'=>$firstPurchase['firstPurchaseRenewPrice'],'oneCostPrice'=>$firstPurchase['firstPurchaseCostPrice'],'oneCostRenewPrice'=>$firstPurchase['firstPurchaseCostRenewPrice'],'CanRenew'=>$content['Product']['CanRenew']);
        }*/
        // 套餐
        if($detailProducts['PackageID']>0){
            return array(
                'oneMarketPrice'=>$firstPurchase['firstPurchasePrice']*$count,
                'oneMarketRenewPrice'=>$firstPurchase['firstPurchaseRenewPrice']*$count,
                'oneCostPrice'=>$firstPurchase['firstPurchaseCostPrice']*$count,
                'oneCostRenewPrice'=>$firstPurchase['firstPurchaseCostRenewPrice']*$count,
                'CanRenew'=>false,
                'actualMarketPrice'=>$actualMarketPrice*$count,
                'actualCostPrice'=>$actualCostPrice*$count,
                'isdirectsellingtoprice'=>$isdirectsellingtoprice,
                'IsGift'=>$IsGift);
            /*if(isset($content['package'])){
            }else{
                return array('oneMarketPrice'=>$firstPurchase['firstPurchasePrice'],'oneMarketRenewPrice'=>$firstPurchase['firstPurchaseRenewPrice'],'oneCostPrice'=>$firstPurchase['firstPurchaseCostPrice'],'oneCostRenewPrice'=>$firstPurchase['firstPurchaseCostRenewPrice'],'CanRenew'=>false,'actualMarketPrice'=>$actualMarketPrice,'actualCostPrice'=>$actualCostPrice);
            }*/
            // 另购单品
        }else{
            return array('oneMarketPrice'=>$firstPurchase['firstPurchasePrice']*$count,'oneMarketRenewPrice'=>$firstPurchase['firstPurchaseRenewPrice']*$count,'oneCostPrice'=>$firstPurchase['firstPurchaseCostPrice']*$count,'oneCostRenewPrice'=>$firstPurchase['firstPurchaseCostRenewPrice']*$count,'CanRenew'=>$content['Product']['CanRenew'],'actualMarketPrice'=>$actualMarketPrice*$count,'actualCostPrice'=>$actualCostPrice*$count,'IsGift'=>$IsGift,'isdirectsellingtoprice'=>$isdirectsellingtoprice,);
        }
    }
    public function NewAddbuyOrder(Vtiger_Request $request){
        $tempdata = $request->get('tempdata');
        $tyunparams = $request->get('tyunparams');
        $request_params = $request->get('request_params');
        $type = $request->get('type');
        $is_first_order = $this->isFirstOrderByUserCode($request_params['usercode']);
        //记录上一份合同使用天数和合同id
        $this->recordLastServiceContractInfo($request_params['usercode'],$request_params['servicecontractsid']);
        foreach ($tempdata['data'] as $temp) {
            $order_detail = $temp['detail']['OrderDetail'];
            // cxh  2020-07-27 start
            $order_detailProducts=$temp['detailProducts'][0];
            $firstPurchaseData = $this->getFirstPurchase($temp['order']['OrderCode']);
            $this->_logs(array('firstPurchaseData',$firstPurchaseData));
            $firstPurchase = !empty($firstPurchaseData)?$firstPurchaseData:$temp['firstPurchase'];
            $this->_logs(array('firstPurchase',$firstPurchase));
            $result=$this->getMarketPriceBuyAndRenew($order_detailProducts,$firstPurchase,$temp['order'],$temp['detail'],$type);
            list($productname, $buyseparately, $productnames,$packageid) = $this->handleProductName($order_detail, $temp['detail']['ProductType'],$temp['detailProducts'][0]['ProductTypeTitle']);
            $params = array(
                'payCode' => $tempdata['payCode'],
                'totalPrice' => $tempdata['totalPrice'],
                'productname' => $productname,
                'buyseparately' => $buyseparately,
                'productnames' => $productnames,
                'oneMarketPrice'=>$result['oneMarketPrice'],
                'oneMarketRenewPrice'=>$result['oneMarketRenewPrice'],
                'oneCostPrice'=>$result['oneCostPrice'],
                'oneCostRenewPrice'=>$result['oneCostRenewPrice'],
                'CanRenew'=>$result['CanRenew'],// 没有用了 应为直接返回首购市场价格了。
                'actualMarketPrice'=>$result['actualMarketPrice'],
                'actualCostPrice'=>$result['actualCostPrice'],
                'isdirectsellingtoprice'=>$result['isdirectsellingtoprice'],
                'IsGifts'=>$result['IsGift']
            );
            if($request->get('is_clientmigrate')){
                $params['packageID'] = $packageid;
            }
            $params=array_merge($temp,$params);
            if($tyunparams){
                $params=array_merge($params,$tyunparams);
            }

            $res[] = $this->AddbuyOrderByData(array_merge($request_params,$params),$type);
        }

        //获取上一份合同的编号和使用
        $this->sendNotice($res,$type,$request_params['signaturetype'],$tyunparams['contractMoney'],$is_first_order,$request_params['servicecontractsid']);

        // 如果是院校版则不生成电子合同 cxh 添加院校版不生成电子合同。
        if($request_params['iscollegeedition']!=1){
            //电子合同时生成合同
            if($request_params['signaturetype']=='eleccontract'){
                $serviceRecordModel = ServiceContracts_Record_Model::getCleanInstance("ServiceContracts");
                $request->set('paycode',$tempdata['payCode']);
                $request->set('accountid',$request_params['accountid']);
                $request->set('servicecontractsid',$request_params['servicecontractsid']);
                $serviceRecordModel->createSalesorderproductsrel($request);
            }
        }
    }

    public function getLastOrdercode($usercode){
        global $adb;
        $sql = "select ordercode from vtiger_activationcode where usercode = ? and productid != '' order by createdtime desc limit 1";
        $result = $adb->pquery($sql,array($usercode));
        if($adb->num_rows($result)){
            $data  = $adb->raw_query_result_rowdata($result,0);
            return $data['ordercode'];
        }
        return '';
    }

    public function recordLastServiceContractInfo($usercode,$servicecontractsid){
        global $adb;
        $sql = "SELECT a.contractid,a.startdate,a.expiredate FROM vtiger_activationcode  a left join vtiger_servicecontracts b on a.contractid=b.servicecontractsid WHERE a.usercode=? and a.productid !='' and contractstatus=1 order by a.createdtime desc";
        $result = $adb->pquery($sql,array($usercode));
        while ($row=$adb->fetch_row($result)){
            if(time()<strtotime($row['startdate'])){
                continue;
            }
            if(time()>=strtotime($row['startdate']) && time()<=strtotime($row['expiredate'])){
                $usedtime = ceil((time() - strtotime($row['startdate']))/(24*60*60));
                $sql1 = 'UPDATE vtiger_servicecontracts set oldcontractid=?,oldcontract_usedtime=? where servicecontractsid=?';
                $adb->pquery($sql1,array($row['contractid'],$usedtime,$servicecontractsid));
                return;
            }
        }
    }

    private function sendNotice($ress,$type,$signaturetype,$contractMoney,$is_first_order=false,$servicecontractsid=0){
        $servicecontractsid_display = '';
        $productname = '';
        $canSend = false;
        foreach ($ress as $key=>$res){
            if($res['success']!=1){
                continue;
            }
            $productname .= $res['email_data']['productname'].'、';
            $userid = $res['email_data']['userid'];
            $accountid_display = $res['email_data']['accountid_display'];
            $buyterm = $res['email_data']['buyTerm'];
            $servicecontractsid_display = $res['email_data']['servicecontractsid_display'];

            $usercode = $res['sms_data']['usercode'];
            $customername = $res['sms_data']['customername'];
            $mobile = $res['sms_data']['mobile'];
            $flag = $res['sms_data']['flag'];

            $canSend = $res['canSend'];
        }
        $email_data = array(
            'userid'=>$userid,
            'accountid_display'=>$accountid_display,
            'servicecontractsid_display'=>$servicecontractsid_display,
            'productname'=>rtrim($productname,'、'),
            'buyTerm'=>$buyterm,
            'classtype'=>$type,
            "signaturetype"=>$signaturetype,
            "contractmoney"=>$contractMoney
        );

        $sms_data = array(
            "usercode"=>$usercode,
            "productname"=>rtrim($productname,'、'),
            "customername"=>$customername,
            "mobile"=>$mobile,
            "flag"=>$flag,
            "is_first_order"=>$is_first_order
        );
        $this->NewTwebSendMail($email_data);
        $this->orderSuccessSms($sms_data,$servicecontractsid_display);
        if($signaturetype=='papercontract'&&$canSend){
            $this->web71360SendSMS($sms_data);
            $db= PearDatabase::getInstance();
            $db->pquery("update vtiger_servicecontracts set issendsms=1 where servicecontractsid=?",array($servicecontractsid));
        }
    }


    /**
     * 购买订单
     * @param Vtiger_Request $request
     * @return array
     */
    public function AddbuyOrderByData($data,$type=''){
        $this->_logs(array('AddbuyOrderByData',$data));
        $db=PearDatabase::getInstance();
        $order=$data['order'];
        $detail=$data['detail'];
        $detailproducts = $data['detailProducts'];
        //$OrderDetail =$detail['OrderDetail'];
        //$OrderDetail=json_decode($OrderDetail,true);
        $inputdata=array();
        $inputdata['activationcodeid']=$db->getUniqueID('vtiger_activationcode');
        $inputdata['activedate']=$order['AddDate'];//激活日期
        $inputdata['contractid']=$data['servicecontractsid'];//合同ID
        $inputdata['contractname']=$data['servicecontractsid_display'];//合同编号
        $inputdata['customerid']=$data['accountid'];//客户ID
        $inputdata['customername']=$data['accountid_display'];//客户名称
        $inputdata['agents']=$order['AgentIdentity'];//代理商ID
        $inputdata['productlife']=$detail['BuyTerm'];//年限
//        $inputdata['productlife']=$data['buyTerm'];//年限
        $inputdata['productid']=$detail['PackageID'];//产品套餐
//        $inputdata['productid']=$request->get('packageID');//产品套餐
        $inputdata['mobile']=$data['mobile'];//手机号
        $inputdata['salesname']=$data['customer_name'];//商务名称
        $inputdata['salesphone']=$data['phone_mobile'];//商务手机号
        $inputdata['customerstype']=$data['customerstype'];//客户类型(普通,迁移)
        // cxh 2020-07-28 start
        $inputdata['onemarketprice']=$data['oneMarketPrice'];
        $inputdata['onemarketrenewprice']=$data['oneMarketRenewPrice'];
        $inputdata['onecostprice']=$data['oneCostPrice'];
        $inputdata['onecostrenewprice']=$data['oneCostRenewPrice'];
        $inputdata['canrenew']=$data['CanRenew'];
        $inputdata['isdirectsellingtoprice']=$data['isdirectsellingtoprice'];

        $inputdata['couponcode']=$data['couponCode'];//优惠券编码
        $inputdata['couponname']=$data['couponName'];//优惠券用户名
        // cxh 2020-07-28 end
        $status=$data['classtype']=='buy'?0:1;
        $orderstatus=$data['classtype']=='buy'?'ordernotused':'orderdoused';
        if($data['signaturetype']=='eleccontract'){
            $orderstatus='ordernotused';
        }
        $isclientmigration=0;
        $OrderDetailToArray=json_decode($detail['OrderDetail'],true);
        switch ($detail['ProductType']){
            case 1:
                $classtype = 'buy';
                $inputdata['renewmarketprice'] =$OrderDetailToArray['package']['DirectRenewPrice'];
                $inputdata['renewcostprice'] =$OrderDetailToArray['package']['CostRenewPrice'];
                break;
            case 2:
            case 5:
                $classtype = 'upgrade';
                $inputdata['isupgrade'] = 1;
                $inputdata['renewmarketprice'] =$OrderDetailToArray['package']['DirectRenewPrice'];
                $inputdata['renewcostprice'] =$OrderDetailToArray['package']['CostRenewPrice'];
                break;
            case 3:
            case 6:
                $classtype = 'degrade';
                $inputdata['renewmarketprice'] =$OrderDetailToArray['package']['DirectRenewPrice'];
                $inputdata['renewcostprice'] =$OrderDetailToArray['package']['CostRenewPrice'];
                break;
            case 4:
            case 7:
                $classtype = 'renew';
                $inputdata['renewmarketprice'] =$OrderDetailToArray['renewpackage']['DirectRenewPrice'];
                $inputdata['renewcostprice'] =$OrderDetailToArray['renewpackage']['CostRenewPrice'];
                break;
        }

        //集团版和院校版的数据
        switch ($detail['CategoryID']){
            case 7:
                $iscollegeedition=1;
                break;
            case 9:
                $iscollegeedition=2;
                break;
            default:
                $iscollegeedition=0;
        }

        $inputdata['iscollegeedition']=$iscollegeedition;// 是院校版标明
        $inputdata['packageamount'] = 0;
        if($inputdata['productid']){
            $inputdata['packageamount'] = $detail['Count'];
        }

        if($inputdata['customerstype']=='clientmigration'){
            $surplusmoney=$data['surplusmoney'];
            //$oldsurplusmoney=$request->get('oldsurplusmoney');
            //$upgradecost=$request->get('upgradecost');//市场支付金额
            //$inputdata['sumsurplusmoney']=$surplusmoney;
            //$inputdata['upgradecontractprice']=bcadd($surplusmoney,$upgradecost,2);//升级后的合同金额
            $inputdata['surplusmoney']=0;//预付款
            //$inputdata['oldsurplusmoney']=$oldsurplusmoney;
            $inputdata['upgradecontractprice']=0;//升级后的合同金额
            $inputdata['upgradetransfer']=$surplusmoney;//升级转款=预付款
            $isclientmigration=1;//是否做迁移升级,续费降级
            $inputdata['oldproductid']=$data['oldproductid'];//原产品ID
            if($type !='buy'){
                $inputdata['orderordercode']=$data['orderordercode'];//原产品订单号即激活码
                $inputdata['oldproductname']=$data['oldproductname'];//原产品名称
            }
            $inputdata['classtype']='c'.($type?$type:$classtype);//类型buy:购买、upgrade:升级、renew:续费、againbuy:另购
//            $inputdata['classtype']='c'.$classtype;//类型buy:购买、upgrade:升级、renew:续费、againbuy:另购
        }else{
            if(!in_array($classtype ,array('buy','renew'))){
                $oldorderordercode = $this->getLastOrdercode($data['usercode']);
                $inputdata['orderordercode']=$oldorderordercode;//原产品订单号即激活码
                $inputdata['oldproductname']=$data['oldproductname'];//原产品名称
            }
            $inputdata['customerstype']='commoncustomers';
            $inputdata['classtype']=$classtype;//类型buy:购买、upgrade:升级、renew:续费、againbuy:另购
            if($classtype=='upgrade'){
                $surplusmoney  = $order['SurplusMoney'];
                $inputdata['upgradetransfer'] =$surplusmoney;
            }
        }
        if(substr($detail['OpenDate'],0,4)>0){
            $orderstatus='orderdoused';
            $status=1;
        }
        $usercode=$data['usercode'];
        $inputdata['status']=$status;//状态

        $inputdata['usercode']=$usercode;//T云账户
        $inputdata['receivetime']=$order['AddDate'];//开始时间
        $inputdata['createdtime']=$data['addDate']?$data['addDate']:$detail['AddDate'];//创建时间
//        $inputdata['createdtime']=$detail['AddDate'];//创建时间
        //$productInfo=$request->get('productdata');
        //$inputdata['buyserviceinfo']=json_encode($productInfo,JSON_UNESCAPED_UNICODE);//另购项
        $inputdata['checkstatus']=0;
        $inputdata['pushstatus']=0;
        $inputdata['contractstatus']=0;
        $inputdata['startdate']=$detail['OpenDate'];//开始时间
        $inputdata['expiredate']=$detail['CloseDate'];//到期时间
        $inputdata['creator']=$data['userid'];//创建人
        $inputdata['ordercode']=$order['OrderCode'];//订单号
//        $inputdata['productname']=$detail['ProductTitle'];//产品名称
        $inputdata['productname']=$data['productname'];//产品名称
        $inputdata['paycode']=$order['PayCode'];//付款码
        $inputdata['buyseparately']=$data['buyseparately'];//另购产品的ID

        $inputdata['comeformtyun']=1;//来源(0tyun产品,1Tyunweb产品)
        $inputdata['onoffline']='offline';//0线上,1线下
        $productnames=$data['productnames'];
//        $inputdata['productclass']=$data['categoryID'];//产品名称数据
        $inputdata['productnames']=json_encode($productnames,JSON_UNESCAPED_UNICODE);//产品名称数据
        $inputdata['orderstatus']=$orderstatus;//订单
        //状态ordernotused
        $inputdata['paymentno']=$order['OutTradeNo'];//付款流水号
//        $inputdata['contractprice']=$request->get('contractprice');//合同金额
//        $inputdata['orderamount']=$request->get('totalPrice');//订单金额
//        $inputdata['orderamount'] = $order['OriginalMoney'];//订单金额
        $inputdata['orderamount'] = $order['Money'];//订单金额(市场价 如果是活动是活动市场价)
        $inputdata['contractprice'] = $order['ContractMoney'];//支付金额
        $inputdata['contractamount'] = $order['ContractMoney'] +$surplusmoney; //合同金额
        $inputdata['usercodeid']=$data['usercodeid'];//用户id
        $inputdata['oldcustomerid']=$data['oldcustomerid'];
        $inputdata['oldcustomername'] = $data['oldcustomername'];
        $inputdata['agentsaletype'] = $order['AgentSaleType'];//售卖类型

        // cxh 2020-08-26
        if(!empty($data['actualCostPrice'])){
            $inputdata['costprice'] = $data['actualCostPrice'];
        }else{
            $inputdata['costprice'] = $order['CostPrice'];
        }
        $inputdata['tyun_costprice'] = $order['CostPrice'];


        // 如果是赠送产品市场价格为0
        if($data['IsGifts']){
            $inputdata['marketprice']=0;
        }
        $inputdata['tyun_marketprice'] = $order['OriginalMoney'];
        //'actualMarketPrice'=>$result['actualMarketPrice'],'actualCostPrice'=>$result['actualCostPrice']
        $inputdata['marketprice'] = $order['OriginalMoney'];
        //算业绩用(市场价 如果是活动是活动市场价)
        if($detailproducts[0]&&$detailproducts[0]['ActivityID']){
            $inputdata['activityid'] = $data['activityid'];
            $inputdata['activitysubprice'] = $data['activityid']?($order['OriginalMoney']-$order['ActiveMoney']):'';
            $inputdata['activemoney'] = $order['ActiveMoney'];
            $inputdata['activityname'] = $data['activityname'];
            $inputdata['activitytype'] = $data['activitytype'];
            $inputdata['activityno'] = $data['activityno'];
            $inputdata['marketprice'] = $order['MaketMoney'];
            $inputdata['giveterm'] = $detailproducts[0]['GiveTerm'];
        }
	   // cxh 2020-08-26
        if(!empty($data['actualMarketPrice'])){
            $inputdata['marketprice'] = $data['actualMarketPrice'];
        }
	// 如果是赠送产品市场价格为0
        if($data['IsGifts']){
            $inputdata['marketprice']=0;
        }
        $inputdata['fromactivity']=0;
        if($data['activityid']){
            $inputdata['fromactivity'] = 1;
        }

        $inputdata['productclass'] = $detail['CategoryID'];//0国内版,1一带一路

        /**记录相关的购买产品信息start**/
        $detailOrderDetail = is_array($detail['OrderDetail'])?$detail['OrderDetail']:json_decode($detail['OrderDetail'],true);
        $detailproducts2 = array();
        if($detail['PackageID']){
            switch ($detail['ProductType']){
                case 4:
                    $packagetitle = $detailOrderDetail['renewpackage']['Title'];
                    $products = $detailOrderDetail['renewpackageproducts'];
                    break;
                default:
                    $packagetitle = $detailOrderDetail['package']['Title'];
                    $products = $detailOrderDetail['packageProducts'];
                    break;
            }
            foreach ($products as  $product){
                $detailproducts2[] = array(
                    'buyTerm'=>intval($detail['BuyTerm']),
                    'packageTitle'=>$packagetitle,
                    'categoryID'=>$product['Product']['CategoryID'],
                    'productTitle'=>$product['Product']['Title']
                );
            }
        }else{
            switch ($detail['ProductType']) {
                case 4:
                    $products = $detailOrderDetail['renewproducts'];
                    break;
                default:
                    $products = $detailOrderDetail['products'];
                    break;
            }
            foreach ($products as  $product){
                $detailproducts2[] = array(
                    'buyTerm'=>intval($detail['BuyTerm']),
                    'packageTitle'=>'',
                    'categoryID'=>$product['Product']['CategoryID'],
                    'productTitle'=>$product['Product']['Title']
                );
            }
        }

        $inputdata['detailproducts'] = json_encode($detailproducts2);
        /**记录相关的购买产品信息end**/
$inputdata['signaturetype'] = $data['signaturetype']?$data['signaturetype']:'papercontract';
        $inputdata['elereceivermobile'] = $data['elereceivermobile'];
        $inputdata['owncompany'] = $data['owncompany'];
        $inputdata['owncompanyid'] = $data['owncompanyid'];
//        $inputdata['marketprice'] = $order['Money'];
        $sql="INSERT INTO vtiger_activationcode(".implode(',',array_keys($inputdata)).") values(".generateQuestionMarks($inputdata).")";
        $db->pquery($sql,$inputdata);
        $db->pquery("INSERT INTO vtiger_activationcode_tyunres(contractno,classtype,tyunurl,crminput,tyunoutput,success,createdtime)VALUES(?,?,?,?,?,?,NOW())",
            array($inputdata['contractname'],$inputdata['classtype'],$data["tyunurl"],'',json_encode($data['res']),1));
        $flag=1;
        if(1==$isclientmigration){
            $sql='UPDATE vtiger_activationcode SET isclientmigration=1 WHERE usercode=? AND comeformtyun=0';
            $db->pquery($sql,array($usercode));
            $classtype = $inputdata['classtype'];
            $flag=2;
        }

        global $configcontracttypeName, $configcontracttypeNameYUN,$configcontracttypeNameJT;
        //修改合同的contract_type和bussinesstype
        switch ($data['iscollegeedition']){
            case 1:
                $contract_type = $configcontracttypeNameYUN;
                $bussinesstype = 'bigsass';
                break;
            case 2:
                $contract_type = $configcontracttypeNameJT;
                $bussinesstype = 'bigsass';
                break;
            default:
                $contract_type = $configcontracttypeName;
                $bussinesstype = 'smallsassdirect';
                break;
        }
        $db->pquery("update vtiger_servicecontracts set contract_type=?,bussinesstype=?,parent_contracttypeid=2 where servicecontractsid=?",array($contract_type,$bussinesstype,$data['servicecontractsid']));
        $sms_data = array(
            "usercode"=>$usercode,
            "productname"=>$inputdata['productname'],
            "customername"=>$inputdata['customername'],
            "mobile"=>$inputdata['mobile'],
            "flag"=>$flag,
        );

        $email_data = array(
            'userid'=>$data['userid'],
            'accountid_display'=>$data['accountid_display'],
            'servicecontractsid_display'=>$data['servicecontractsid_display'],
            'productname'=>$data['productname'],
            'buyTerm'=>$detail['BuyTerm']
        );

        $this->writeExpireRenewPriceAndCostPrice(array('usercode'=>$usercode,'contractid'=>$data['servicecontractsid'],'productid'=>$inputdata['productid']));
        return array('success'=>1,'sms_data'=>$sms_data,'email_data'=>$email_data,'canSend'=>true);
    }


    private function handleProductName($order_detail,$ProductType,$productTitle){
        if(empty($order_detail)) {
            return '';
        }
        $productname = '';
        $OrderDetail = preg_replace(array('/(^")|("$)/'), '', $order_detail);
        $OrderDetail = str_replace("\\", '', $OrderDetail);
        $OrderDetail = json_decode($OrderDetail, true);
        $buyseparately = '';

        switch ($ProductType){
            case 4:
                if(!empty($OrderDetail['renewpackage'])){
                    $productname.=$OrderDetail['renewpackage']['Title']."(1)";
                    break;
                }
                if($OrderDetail['renewpackageproducts']){
                    foreach($OrderDetail['renewpackageproducts'] as $value){
                        $renewproductsnum=array();
                        $renewproductstempid=array();
                        $renewproductids = array();
                        $preproductnamess = array();
                        $id=$value['Product']['ID'];
                        if(!in_array("/DV".$id."VD/",$renewproductstempid)){
                            $renewproductstempid[]="/DV".$id."VD/";
                            $renewproductsnum["DV".$id."VD"]=1;
//                            $productname.=','.$value['Product']['Title']."(DV".$id."VD)";
                            $productname.=','.$value['Product']['Title']."(DV".$id."VD";
                            if(!$value['Product']['CanRenew']){
                                $productname.=",无年限";
                            }
                            $productname.=")";
                        }else{
                            $renewproductsnum["DV".$id."VD"]+=1;
                        }

                        if(!in_array($id,$renewproductids)){
                            $preproductnamess[$id] = array(
                                'productID'=>$id,
                                'productTitle'=>$value['Product']['Title'],
                                'productCount'=>$value['Count'],
                                'specificationId'=>$value['Specification']['ID'],
                                'specificationTitle'=>$value['Specification']['Title']
                            );
                            $buyseparately .= $id.',';
                            $renewproductids = array_merge(array($id),$renewproductids);
                        }else{
                            $count = $preproductnamess[$id]['productCount'] +$value['Count'];
                            $specifications = explode(',',$preproductnamess[$id]['Specification']['ID']);
                            $specificationtitle = $preproductnamess[$id]['specificationTitle'];
                            if(!in_array($value['Specification']['ID'],$specifications)){
                                $specifications = array_merge($specifications,array($value['Specification']['ID']));
                                $specificationtitle .= $value['Specification']['Title'];
                            }
                            $preproductnamess[$id] = array(
                                'productID'=>$id,
                                'productTitle'=>$preproductnamess[$id]['productTitle'],
                                'productCount'=>$count,
                                'specificationId'=>implode(',',$specifications),
                                'specificationTitle'=>$specificationtitle
                            );
                        }
                    }
                    $productnames = $preproductnamess;
                    $productname=preg_replace($renewproductstempid,$renewproductsnum,$productname);
                }
                if($OrderDetail['renewproducts']){
                    foreach($OrderDetail['renewproducts'] as $value){
                        $renewproductsnum=array();
                        $renewproductstempid=array();
                        $renewproductids = array();
                        $preproductnamess = array();
                        $id=$value['Product']['ID'];
                        if(!in_array("/DV".$id."VD/",$renewproductstempid)){
                            $renewproductstempid[]="/DV".$id."VD/";
                            $renewproductsnum["DV".$id."VD"]=$value['Count'];
//                            $productname.=','.$value['Product']['Title']."(DV".$id."VD)";
                            $productname.=','.$value['Product']['Title']."(DV".$id."VD";
                            if(!$value['Product']['CanRenew']){
                                $productname.=",无年限";
                            }
                            $productname.=")";
                        }else{
                            $renewproductsnum["DV".$id."VD"]+=$value['Count'];
                        }

                        if(!in_array($id,$renewproductids)){
                            $preproductnamess[$id] = array(
                                'productID'=>$id,
                                'productTitle'=>$value['Product']['Title'],
                                'productCount'=>$value['Count'],
                                'specificationId'=>$value['Specification']['ID'],
                                'specificationTitle'=>$value['Specification']['Title']
                            );
                            $buyseparately .= $id.',';
                            $renewproductids = array_merge(array($id),$renewproductids);
                        }else{
                            $count = $preproductnamess[$id]['productCount'] +$value['Count'];
                            $specifications = explode(',',$preproductnamess[$id]['Specification']['ID']);
                            $specificationtitle = $preproductnamess[$id]['specificationTitle'];
                            if(!in_array($value['Specification']['ID'],$specifications)){
                                $specifications = array_merge($specifications,array($value['Specification']['ID']));
                                $specificationtitle .= $value['Specification']['Title'];
                            }
                            $preproductnamess[$id] = array(
                                'productID'=>$id,
                                'productTitle'=>$preproductnamess[$id]['productTitle'],
                                'productCount'=>$count,
                                'specificationId'=>implode(',',$specifications),
                                'specificationTitle'=>$specificationtitle
                            );
                        }
                    }
                    $productnames = $preproductnamess;
                    $productname=preg_replace($renewproductstempid,$renewproductsnum,$productname);
                }
                break;
            case 1:
                if(!empty($OrderDetail['package'])){
                    $productname.=$OrderDetail['package']['Title']."(1)";
                }
                if($OrderDetail['products']){
                    foreach($OrderDetail['products'] as $value){
                        //                        $productname.=','.$value['Product']['Title']."(".$value['Count'].")";
                        $productname.=','.$value['Product']['Title']."(".$value['Count'];
                        if(!$value['Product']['CanRenew']){
                            $productname.=",无年限";
                        }
                        $productname.=")";
                        $productnames[] = array(
                            'productID'=>$value['Product']['ID'],
                            'productTitle'=>$value['Product']['Title'],
                            'productCount'=>$value['Count'],
                            'specificationId'=>$value['Specification']['ID'],
                            'specificationTitle'=>$value['Specification']['Title'],
                            'canRenew'=>$value['Product']['CanRenew'],
                            'productTypeTitle'=>$productTitle
                        );
                        $buyseparately .= $value['Product']['ID'].',';
                    }
                }
                break;
            default:
                if(!empty($OrderDetail['package'])){
                    $productname.=$OrderDetail['package']['Title']."(1)";
                }
                if($OrderDetail['products']){
                    foreach($OrderDetail['products'] as $value){
//                        $productname.=','.$value['Product']['Title']."(".$value['Count'].")";
                        $productname.=','.$value['Product']['Title']."(".$value['Count'];
                        if(!$value['Product']['CanRenew']){
                            $productname.=",无年限";
                        }
                        $productname.=")";
                        $productnames[] = array(
                            'productID'=>$value['Product']['ID'],
                            'productTitle'=>$value['Product']['Title'],
                            'productCount'=>$value['Count'],
                            'specificationId'=>$value['Specification']['ID'],
                            'specificationTitle'=>$value['Specification']['Title'],
                            'canRenew'=>$value['Product']['CanRenew'],
                            'productTypeTitle'=>$productTitle
                        );
                        $buyseparately .= $value['Product']['ID'].',';
                    }
                }
                break;
        }

        $packageid=$OrderDetail['package']['ID'];
        return array(trim($productname,','),trim($buyseparately,','),array_values($productnames),$packageid);
    }

    /**
     * 购买订单
     * @param Vtiger_Request $request
     * @return array
     */
    public function AddbuyOrder(Vtiger_Request $request){
        $db=PearDatabase::getInstance();
        $order=$request->get('order');
        $detail=$request->get('detail');
        //$OrderDetail =$detail['OrderDetail'];
        //$OrderDetail=json_decode($OrderDetail,true);
        $inputdata=array();
        $inputdata['activationcodeid']=$db->getUniqueID('vtiger_activationcode');
        $inputdata['activedate']=$order['AddDate'];//激活日期
        $inputdata['contractid']=$request->get('servicecontractsid');//合同ID
        $inputdata['contractname']=$request->get('servicecontractsid_display');//合同编号
        $inputdata['customerid']=$request->get('accountid');//客户ID
        $inputdata['customername']=$request->get('accountid_display');//客户名称
        $inputdata['agents']=$order['AgentIdentity'];//代理商ID
        $inputdata['productlife']=$request->get('buyTerm');//年限
        $inputdata['productid']=$detail['PackageID'];//产品套餐
//        $inputdata['productid']=$request->get('packageID');//产品套餐
        $inputdata['mobile']=$request->get('mobile');//手机号
        $inputdata['salesname']=$request->get('customer_name');//商务名称
        $inputdata['salesphone']=$request->get('phone_mobile');//商务手机号
        $inputdata['customerstype']=$request->get('customerstype');//客户类型(普通,迁移)
        $status=$request->get('classtype')=='buy'?0:1;
        $orderstatus=$request->get('classtype')=='buy'?'ordernotused':'orderdoused';
        $isclientmigration=0;
        switch ($detail['ProductType']){
            case 1:
                $classtype = 'buy';
                break;
            case 2:
                $classtype = 'upgrade';
//                $inputdata['isupgrade'] = 1;
                break;
            case 3:
                $classtype = 'degrade';
                break;
            case 4:
                $classtype = 'renew';
                break;
        }

        if($inputdata['customerstype']=='clientmigration'){
            $surplusmoney=$request->get('surplusmoney');
            //$oldsurplusmoney=$request->get('oldsurplusmoney');
            //$upgradecost=$request->get('upgradecost');//市场支付金额
            //$inputdata['sumsurplusmoney']=$surplusmoney;
            //$inputdata['upgradecontractprice']=bcadd($surplusmoney,$upgradecost,2);//升级后的合同金额
            $inputdata['surplusmoney']=0;//预付款
            //$inputdata['oldsurplusmoney']=$oldsurplusmoney;
            $inputdata['upgradecontractprice']=0;//升级后的合同金额
            $inputdata['upgradetransfer']=$surplusmoney;//升级转款=预付款
            $isclientmigration=1;//是否做迁移升级,续费降级
            $inputdata['oldproductid']=$request->get('oldproductid');//原产品ID
            $inputdata['classtype']='c'.$classtype;//类型buy:购买、upgrade:升级、renew:续费、againbuy:另购
        }else{
            $inputdata['customerstype']='commoncustomers';
            $inputdata['classtype']=$classtype;//类型buy:购买、upgrade:升级、renew:续费、againbuy:另购
        }
        if(substr($detail['OpenDate'],0,4)>0){
            $orderstatus='orderdoused';
            $status=1;
        }
        $usercode=$request->get('usercode');
        $inputdata['status']=$status;//状态

        $inputdata['orderordercode']=$request->get('orderordercode');//原产品订单号即激活码
        $inputdata['oldproductname']=$request->get('oldproductname');//原产品名称
        $inputdata['usercode']=$usercode;//T云账户
        $inputdata['receivetime']=$order['AddDate'];//开始时间
        $inputdata['createdtime']=$detail['AddDate'];//创建时间
        //$productInfo=$request->get('productdata');
        //$inputdata['buyserviceinfo']=json_encode($productInfo,JSON_UNESCAPED_UNICODE);//另购项
        $inputdata['checkstatus']=0;
        $inputdata['pushstatus']=0;
        $inputdata['contractstatus']=0;
        $inputdata['startdate']=$detail['OpenDate'];//开始时间
        $inputdata['expiredate']=$detail['CloseDate'];//到期时间
        $inputdata['creator']=$request->get('userid');//创建人
        $inputdata['ordercode']=$order['OrderCode'];//订单号
//        $inputdata['productname']=$detail['ProductTitle'];//产品名称
        $inputdata['productname']=$request->get('productname');//产品名称
        $inputdata['paycode']=$order['PayCode'];//付款码
        $inputdata['buyseparately']=$request->get('buyseparately');//另购产品的ID
        $inputdata['comeformtyun']=1;//来源(0tyun产品,1Tyunweb产品)
        $inputdata['onoffline']='offline';//0线上,1线下
        $productnames=$request->get('productnames');
        $inputdata['productclass']=$request->get('categoryID');//产品名称数据
        $inputdata['productnames']=json_encode($productnames,JSON_UNESCAPED_UNICODE);//产品名称数据
        $inputdata['orderstatus']=$orderstatus;//订单
        //状态ordernotused
        $inputdata['paymentno']=$order['OutTradeNo'];//付款流水号
//        $inputdata['contractprice']=$request->get('contractprice');//合同金额
//        $inputdata['orderamount']=$request->get('totalPrice');//订单金额
        $inputdata['orderamount'] = $order['Money'];//订单金额
        $inputdata['contractprice'] = $order['ContractMoney'];//订单金额
        $inputdata['contractamount'] = $order['ContractMoney'] +$inputdata['upgradetransfer']; //合同金额
        $inputdata['usercodeid']=$request->get('usercodeid');//用户id
        $inputdata['costprice'] = $order['CostPrice'];
        $inputdata['marketprice'] = $order['Money'];


        $inputdata['activemoney'] = $order['ActiveMoney'];
        $inputdata['activitysubprice'] = $order['OriginalMoney']-$order['ActiveMoney'];
        $inputdata['activityid'] = $order['activityid'];

        $sql="INSERT INTO vtiger_activationcode(".implode(',',array_keys($inputdata)).") values(".generateQuestionMarks($inputdata).")";
        $db->pquery($sql,$inputdata);
        $db->pquery("INSERT INTO vtiger_activationcode_tyunres(contractno,classtype,tyunurl,crminput,tyunoutput,success,createdtime)VALUES(?,?,?,?,?,?,NOW())",
            array($inputdata['contractname'],$inputdata['classtype'],$request->get("tyunurl"),'',json_encode($request->get('res')),1));
        $flag=1;
        if(1==$isclientmigration){
            $sql='UPDATE vtiger_activationcode SET isclientmigration=1 WHERE usercode=? AND comeformtyun=0';
            $db->pquery($sql,array($usercode));
            $request->set('classtype',$inputdata['classtype']);
            $flag=2;
        }

        $sms_data = array(
            "usercode"=>$usercode,
            "productname"=>$inputdata['productname'],
            "customername"=>$inputdata['customername'],
            "mobile"=>$inputdata['mobile'],
            "flag"=>$flag,
        );

        $email_data = array(
            'userid'=>$request->get('userid'),
            'accountid_display'=>$request->get('accountid_display'),
            'servicecontractsid_display'=>$request->get('servicecontractsid_display'),
            'productname'=>$request->get('productname'),
            'buyTerm'=>$request->get('buyTerm')
        );
//        $this->TwebSendMail($request);
        return array('success'=>1,'sms_data'=>$sms_data,'email_data'=>$email_data);
    }
    /**
     * T云web端调用
     * @param $data
     * @return array
     * @throws AppException
     */
    public function getAccountList($request){
        global $adb,$current_user,$log;
        $querySql='';
        $is_cs_admin=$request->get('is_cs_admin');
        $customerid=$request->get('customerid');
        $userid=$request->get('userid');
        $searchValue=$request->get('searchValue');
        if(!$is_cs_admin){
            $user = new Users();
            $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
            /*if($customerid>0){
                $querySql=' AND (vtiger_crmentity.smownerid='.$customerid.' OR vtiger_account.serviceid='.$customerid.')';
            }else{*/
                $where=getAccessibleUsers('Accounts','List',true);
                if($where!='1=1'){
                    $querySql=' AND (vtiger_crmentity.smownerid in('.implode(',',$where).') OR vtiger_account.serviceid='.$userid.')';
                    $log->debug('cccccccc'.$customerid);
                }
           // }
        }
        $query="SELECT 
            vtiger_account.accountname as mname,
            accountid AS mid,
            vtiger_crmentity.smownerid AS userid,
            IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id),'--') as username FROM vtiger_account LEFT JOIN vtiger_crmentity ON vtiger_account.accountid=vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0 and
             vtiger_account.accountcategory=0".$querySql." AND vtiger_account.accountname like ? LIMIT 50";
        $result=$adb->pquery($query,array('%'.$searchValue.'%'));
        $data=array();
        if($adb->num_rows($result)){
            while($row=$adb->fetch_array($result)){
                $data[]=$row;
            }
        }
        return $data;
    }
    /**
     * T云web端调用
     * @param $data
     * @return array
     * @throws AppException
     */
    public function getServiceContractsList($request){
        global $adb,$current_user,$log;
        $querySql='';
        $userid=$request->get('userid');
        $customerid=$request->get('customerid');
        $searchValue=$request->get('searchValue');
        $is_cs_admin=$request->get('is_cs_admin');
        $querySql='';
        if(!$is_cs_admin){
            //if($userid==2824){
                //$querySql = ' AND vtiger_crmentity.smownerid in(1179,2824,7871)';
            //}else {
                $user = new Users();
                $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
                //if ($customerid > 0) {
                    //$querySql = ' AND vtiger_crmentity.smownerid=' . $customerid;
                //} else {
                    $where = getAccessibleUsers('ServiceContracts', 'List', true);
                    if ($where != '1=1') {
                        $querySql = ' AND (vtiger_crmentity.smownerid in (' . implode(',', $where) . ') or vtiger_servicecontracts.receiveid in ('.implode(",",$where).'))';
                    }
               // }
            //}
        }
        $data=array();
        if(empty($searchValue)){
            return $data;
        }
        $query="SELECT 
                    vtiger_servicecontracts.invoicecompany as invoicecompany,
                    vtiger_servicecontracts.contract_no as mname,
                    vtiger_servicecontracts.categoryid as categoryid,
                    vtiger_servicecontracts.servicecontractsid AS mid,
                    vtiger_servicecontracts.actualeffectivetime AS actualeffectivetime,
                    vtiger_crmentity.smownerid AS userid,
                    IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id),'--') as username 
                    FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid=vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0 and
                    vtiger_servicecontracts.signaturetype='papercontract'
                    AND  (vtiger_servicecontracts.modulestatus in('已发放','c_recovered') or (vtiger_servicecontracts.modulestatus='c_complete' and vtiger_servicecontracts.contract_type='T云WEB版')) ".$querySql." AND vtiger_servicecontracts.contract_no like ? 
                     AND NOT EXISTS(SELECT 1 FROM vtiger_activationcode WHERE vtiger_activationcode.contractid=vtiger_servicecontracts.servicecontractsid AND vtiger_activationcode.status IN(0,1)) 
	                AND NOT EXISTS(SELECT 1 FROM vtiger_tyunstationsale WHERE vtiger_tyunstationsale.contractid=vtiger_servicecontracts.servicecontractsid)
	                AND vtiger_servicecontracts.sideagreement!=1
                     LIMIT 50";
        $result=$adb->pquery($query,array('%'.$searchValue.'%'));
        if($adb->num_rows($result)){
            while($row=$adb->fetch_array($result)){
                $data[]=$row;
            }
        }
        return $data;
    }

      public function AddOnlineOrder($data){
        global $tyunweburl;
        $rddata=$data['rddata'];
        $rowdata0=base64_decode($rddata);
        $rowdata = str_replace('&quot;','"',$rowdata0);
        $resultData=json_decode($rowdata,true);
        $return=array('success'=>0);
        $this->_logs(array("AddbuyOrderOnLineRabbitMQData",$resultData));
        do{
                $this->_logs(array("AddbuyOrderOnLineRabbitMQData2"));
            if(empty($resultData)){
                $this->_logs(array("结果为空"));
                break;
            }
            $order=$resultData['order'];
            $detail=$resultData['orderDetail'];
            $detailproducts = $resultData['detailProducts'];
            $ProductType=array(1=>'buy',2=>'upgrade',3=>'degrade',4=>'renew');
            $classtype=$ProductType[$detail['ProductType']];
            try{
                $db=PearDatabase::getInstance();
                $query = 'SELECT contractid,creator FROM `vtiger_activationcode` WHERE ordercode=?';
                $dataResult = $db->pquery($query, array($order['OrderCode']));
                $lastData = $db->query_result_rowdata($dataResult, 0);

            }catch (Exception $exception){
                $db=new PearDatabase();
                $db->connect();
                $query = 'SELECT contractid,creator FROM `vtiger_activationcode` WHERE ordercode=?';
                $dataResult = $db->pquery($query, array($order['OrderCode']));
                $lastData = $db->query_result_rowdata($dataResult, 0);
            }

            $db->pquery("INSERT INTO vtiger_activationcode_tyunres(contractno,classtype,tyunurl,crminput,tyunoutput,success,createdtime) VALUES(?,?,?,?,?,?,NOW())",
                array($order['ContractCode'],$classtype,'线上推送','',$rowdata0,1));

            if ($db->num_rows($dataResult) && $order['AgentSaleType']!=4) {
                $startdate = date("Y-m-d H:i:s",strtotime($detail['OpenDate']));//开始时间
                $expiredate = date("Y-m-d H:i:s",strtotime($detail['CloseDate']));//到期时间
                if($order['TradingStatus']==4){
                    $db->pquery('update vtiger_listenorder set deleted=1 where servicecontractsid=? ',$lastData['contractid']);
                    $sql = 'UPDATE `vtiger_activationcode` SET orderstatus=\'orderstop\' WHERE ordercode=?';
                    $db->pquery($sql, array( $order['OrderCode']));
                    $db->pquery("update vtiger_servicecontracts set modulestatus='c_stop' where servicecontractsid=?",array($lastData['contractid']));
                    $db->pquery('update vtiger_listenorder set deleted=1 where servicecontractsid=? ',$lastData['contractid']);
                    $return = array('success' => 1);
                    continue;
                }
                if($order['TradingStatus']==5){
//                    $db->pquery('update vtiger_listenorder set deleted=1 where servicecontractsid=? ',$lastData['contractid']);
                    $sql = 'UPDATE `vtiger_activationcode` SET orderstatus=\'orderbreak\' WHERE ordercode=?';
                    $db->pquery($sql, array( $order['OrderCode']));
//                    $db->pquery("update vtiger_servicecontracts set modulestatus='c_stop' where servicecontractsid=?",array($lastData['contractid']));
                    $return = array('success' => 1);
                    continue;
                }

                if(substr($detail['OpenDate'],0,4)>0){
                    $serviceContractModel = ServiceContracts_Record_Model::getCleanInstance("ServiceContracts");
                    $serviceContractModel->inListenOrder($lastData['contractid'],$startdate,0,$lastData['creator']);
                    $sql = 'UPDATE `vtiger_activationcode` SET activedate=?,startdate=?,expiredate=?,`status`=1,orderstatus=\'orderdoused\' WHERE ordercode=?';
                    $db->pquery($sql, array($startdate,$startdate, $expiredate, $order['OrderCode']));
                    $contractData = $db->pquery("select contractid,creator from vtiger_activationcode where ordercode = ?",array($order['OrderCode']));
                    if($db->num_rows($contractData)){
                        $receiverabledate = date("Y-m-d",strtotime($startdate));
                        $row = $db->fetchByAssoc($contractData,0);
                        $db->pquery("update vtiger_contracts_execution_detail set receiverabledate=?,executestatus='c_executed' where contractid=?",array($receiverabledate,$row['contractid']));
                    }
                }
                $return = array('success' => 1);
                continue;
            }

            //线下下单不往下走
            if($order['Type']==1&& $order['AgentSaleType']!=4){
                $return = array('success' => 1);
                continue;
            }
            $inputdata['activationcodeid'] = $db->getUniqueID('vtiger_activationcode');
            $inputdata['activedate'] = '';//激活日期
            $inputdata['usercodeid'] = $order['UserID'];//用户ID
            $inputdata['usercode'] = $order['OperatorName'];//用户ID
            $inputdata['contractid'] = 0;//合同ID
            $inputdata['onoffline'] = 'line';//0线上,1线下

            if($order['AgentSaleType']!=4){
                $tyunweburl1 = $tyunweburl . 'api/app/tcloud-account/v1.0.0/account/getAccountInfoById?accountId=' . $order['UserID'];
                $time = time() . '123';
                global $sault;
                $sault1 = $time . $sault;
                $token = md5($sault1);
                $curlset = array(CURLOPT_HTTPHEADER => array(
                    "Content-Type:application/json",
                    "S-Request-Token:" . $token,
                    "S-Request-Time:" . $time));
                $AccountRepson = $this->https_request($tyunweburl1, array(), $curlset);
                $AccountOBJ = json_decode($AccountRepson, true);
                $AccountOBJData = $AccountOBJ['data'];
                $this->_logs(array("AddbuyOrderOnLineRabbitMQData 非外贸订单客户信息",$AccountOBJData));
                if (!empty($AccountOBJData)) {
                    $inputdata['customerid'] = $AccountOBJData['cid'];//客户ID
                    $inputdata['customername'] = $AccountOBJData['customerName'];//客户名称
                    $inputdata['usercode'] = $AccountOBJData['loginName'];//用户名
                    $inputdata['mobile'] = $AccountOBJData['phoneNumber'];//电话号码
                    $inputdata['usercodeid'] = $AccountOBJData['id'];//用户名
                }
            }else{
                //代理商订单 则去查询是否有客户，无客户则创建客户
                $customerName = $order['CustomerName'];
                $accountRecordModel = Accounts_Record_Model::getCleanInstance("Accounts");
                $accountRecordModel->set('accountname', $customerName);
                $isExist = $accountRecordModel->checkDuplicate();
                if(!$isExist){
                    global $current_user;
                    $user = new Users();
                    $current_user = $user->retrieveCurrentUserInfoFromFile(6934);
                    $accountname=preg_replace('/^(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+|(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+$/u','',$customerName);
                    $_REQUEST['record']='';
                    $_REQUEST['accountname']=$accountname;
                    $accountRecordModel->set("accountname", $accountname);
                    $accountRecordModel->save();
                    $inputdata['customerid'] = $accountRecordModel->getId();//客户ID
                    $inputdata['customername'] = $accountname;//客户名称
                }else{
                    $accountInfo = $accountRecordModel->getAccountInfoByAccountName($customerName);
                    $inputdata['customerid'] = $accountInfo['accountid'];//客户ID
                    $inputdata['customername'] = $accountInfo['accountname'];//客户名称
                }

                //代理商客户获取合同id
                $contractNo = $order['ContractCode'];
                $serviceContractModel = ServiceContractsPrint_Record_Model::getCleanInstance("ServiceContracts");
                $request2 = new Vtiger_Request(array());
                $request2->set("contractno",$contractNo);
                $serviceContractData = $serviceContractModel->getContractByContractNo($request2);
                $inputdata['contractid'] = $serviceContractData['data']['servicecontractsid'];//合同ID
                $inputdata['onoffline'] = 'offline';//0线上,1线下
            }
            $inputdata['agentsaletype'] = $order['AgentSaleType'];//售卖类型
            $inputdata['agents'] = $order['AgentIdentity'];//代理商ID
            $inputdata['productlife'] = $detail['BuyTerm'];//年限
            $inputdata['classtype'] = $classtype;//类型buy:购买、upgrade:升级、renew:续费、againbuy:另购
            $inputdata['status'] = 1;//状态
            $inputdata['receivetime'] = $order['AddDate'];//开始时间
            $inputdata['createdtime'] = $order['AddDate'];//创建时间
            $inputdata['checkstatus'] = 0;
            $inputdata['pushstatus'] = 0;
            $inputdata['contractstatus'] = 1;
            $inputdata['customerstype']='commoncustomers';//客户类型
            $inputdata['paymentno'] = $order['OutTradeNo'];//付款流水号
            $inputdata['startdate'] = $detail['OpenDate'];//开始时间
            $inputdata['expiredate'] = $detail['CloseDate'];//到期时间
            $inputdata['ordercode'] = $order['OrderCode'];//订单号
            $inputdata['paycode'] = $order['PayCode'];//付款码
            $inputdata['comeformtyun'] = 1;//来源(0tyun产品,1Tyunweb产品)
            $inputdata['productclass'] = $detail['CategoryID'];//0国内版,1一带一路
            //如果是有合同编号的则插入到订单表中
            if($order['ContractCode']){
                $result2 = $db->pquery("select servicecontractsid from vtiger_servicecontracts a left join vtiger_crmentity b on a.servicecontractsid=b.crmid where contract_no = ? and b.deleted=0 limit 1",array($order['ContractCode']));
                if($db->num_rows($result2)){
                    $row = $db->fetchByAssoc($result2,0);
                    $inputdata['contractid'] = $row['servicecontractsid'];
                }
                $inputdata['contractname'] = $order['ContractCode'];
            }
            $inputdata['orderstatus'] = 'orderdoused';//订单状态
            $inputdata['orderamount'] = $order['Money'];//订单金额(市场价 如果是活动是活动市场价)
            $inputdata['contractprice'] = $order['ContractMoney']?$order['ContractMoney']:$order['Money'];//支付金额
            $inputdata['contractamount'] = $order['ContractMoney']?$order['ContractMoney']:$order['Money']; //合同金额
            //$inputdata['buyserviceinfo'] = $detail['OrderDetail'];//订单产品信息
            $orderDetail = json_decode($detail['OrderDetail'], true);
            $inputdata['fromactivity']=0;
            if($this->isActiveOrder($detailproducts)){
                $inputdata['activityid'] = "";
                $inputdata['activitysubprice'] = $detailproducts[0]['ActivityID']?($order['OriginalMoney']-$order['ActiveMoney']):'';
                $inputdata['activemoney'] = $order['ActiveMoney'];
                $inputdata['activityname'] = $detailproducts[0]['ActivityTitle'];
                $inputdata['activitytype'] = $detailproducts[0]['ActivityType'];
                $inputdata['activityno'] = $detailproducts[0]['ActivityID'];
                $inputdata['marketprice'] = $order['Money'];
                $inputdata['fromactivity'] = 1;
            }

            $productname = '';
            if (!empty($orderDetail['package'])) {
                $inputdata['productid'] = $orderDetail['package']['ID'];//产品套餐
                $productname .= $orderDetail['package']['Title'] . '(1)';
            }
            if (!empty($orderDetail['products'])) {
                $renewproducts = array();
                $buyseparately = '';
                foreach ($orderDetail['products'] as $value) {
                    $productname .= ',' . $value['Product']['Title'] . "(" . $value['Count'] . ")";
                    $buyseparately .= $value['Product']['ID'] . ',';

                    $renewproducts[] = array("productID" => $value['Product']['ID'],
                        "productTitle" => $value['Product']['Title'],
                        "productCount" => $value['Count'],
                        "specificationId" => $value['Specification']['ID'],
                        "specificationTitle" => $value['Specification']['Title'],
                    );
                }
                $buyseparately = trim($buyseparately, ',');
                $inputdata['productnames'] = json_encode($renewproducts, JSON_UNESCAPED_UNICODE);//另购项
                $inputdata['buyseparately'] = $buyseparately;//另购产品的ID
            }
            if (!empty($orderDetail['renewpackage'])) {
                $productname .= $orderDetail['renewpackage']['Title'] . '(1)';
                $inputdata['productid'] = $orderDetail['renewpackage']['ID'];//产品套餐
            }
            if (!empty($orderDetail['renewproducts'])) {
                $renewproductst = array();
                $buyseparately = '';
                $renewproductsnum = array();
                $renewproductstempid = array();
                foreach ($orderDetail['renewproducts'] as $value) {
                    $id = $value['Product']['ID'];
                    if (!in_array("/DV" . $id . "VD/", $renewproductstempid)) {
                        $productname .= ',' . $value['Product']['Title'] . "(DV" . $id . "VD)";
                        $buyseparately .= $value['Product']['ID'] . ',';
                        $renewproductstempid[] = "/DV" . $id . "VD/";
                        $renewproductsnum["DV" . $id . "VD"] = 1;
                        $renewproductst[$id] = array("productID" => $value['Product']['ID'],
                            "productTitle" => $value['Product']['Title'],
                            "productCount" => 1,
                            "specificationId" => $value['UserProductID'],
                            "specificationTitle" => $value['Specification']['Title'],
                        );
                    } else {
                        $renewproductst[$id]["productCount"] += 1;
                        $renewproductst[$id]["specificationId"] .= ',' . $value['UserProductID'];
                        $renewproductst[$id]["specificationTitle"] .= ',' . $value['Specification']['Title'];
                        $renewproductsnum["DV" . $id . "VD"] += 1;
                    }
                }
                $renewproducts = array_values($renewproductst);
                $productname = preg_replace($renewproductstempid, $renewproductsnum, $productname);
                $buyseparately = trim($buyseparately, ',');
                $inputdata['productnames'] = json_encode($renewproducts, JSON_UNESCAPED_UNICODE);//另购项
                $inputdata['buyseparately'] = $buyseparately;//另购产品的ID
            }
            $inputdata['productname'] = trim($productname, ',');
            $query = "SELECT smownerid,serviceid FROM `vtiger_crmentity` LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_crmentity.crmid WHERE deleted=0 AND crmid=?";
            $result = $db->pquery($query, array($AccountOBJData['cid']));
            if ($db->num_rows($result)) {
                $resultData = $db->raw_query_result_rowdata($result, 0);
                if ($resultData['serviceid'] > 0) {
                    $inputdata['serviceid'] = $resultData['serviceid'];//创建人
                } else {
                    $inputdata['serviceid'] = $resultData['smownerid'];//创建人
                }
            }

            $inputdata['creator'] = 6934;
            /**记录相关的购买产品信息start**/
            $detailOrderDetail = is_array($detail['OrderDetail'])?$detail['OrderDetail']:json_decode($detail['OrderDetail'],true);
            $detailproducts2 = array();
            if($detail['PackageID']){
                switch ($detail['ProductType']){
                    case 4:
                        $packagetitle = $detailOrderDetail['renewpackage']['Title'];
                        $products = $detailOrderDetail['renewpackageproducts'];
                        break;
                    default:
                        $packagetitle = $detailOrderDetail['package']['Title'];
                        $products = $detailOrderDetail['packageProducts'];
                        break;
                }
                foreach ($products as  $product){
                    $detailproducts2[] = array(
                        'buyTerm'=>intval($detail['BuyTerm']),
                        'packageTitle'=>$packagetitle,
                        'categoryID'=>$product['Product']['CategoryID'],
                        'productTitle'=>$product['Product']['Title']
                    );
                }
            }else{
                switch ($detail['ProductType']) {
                    case 4:
                        $products = $detailOrderDetail['renewproducts'];
                        break;
                    default:
                        $products = $detailOrderDetail['products'];
                        break;
                }
                foreach ($products as  $product){
                    $detailproducts2[] = array(
                        'buyTerm'=>intval($detail['BuyTerm']),
                        'packageTitle'=>'',
                        'categoryID'=>$product['Product']['CategoryID'],
                        'productTitle'=>$product['Product']['Title']
                    );
                }
            }
            if(!$inputdata['productid']){
                $inputdata['productid'] = 0;
            }

            $inputdata['detailproducts'] = json_encode($detailproducts2);
            if(!$order['ContractCode']) {
                $inputdata['signaturetype'] = 'eleccontract';
            }
            /**记录相关的购买产品信息end**/
            $this->_logs(array("AddbuyOrderOnLineinputdata",$inputdata));

            $sql = "INSERT INTO vtiger_activationcode(" . implode(',', array_keys($inputdata)) . ") values(" . generateQuestionMarks($inputdata) . ")";
            $db->pquery($sql, $inputdata);

            //如果是没有合同号的则创建电子合同
            if(!$order['ContractCode'] && $order['AgentSaleType']!=4) {
                $request = new Vtiger_Request(array());
                //调用合同里创建T云电子合同的方法
                $request->set('ordercode', $order['OrderCode']);
                $serviceContractModel = Vtiger_Record_Model::getCleanInstance("ServiceContracts");
                $result = $serviceContractModel->createTyunServiceContracts($request);
                //记录是否成功生成了合同
                $this->_logs(array('ordercode' => $order['OrderCode'], 'result' => $result));
            }
            $return=array('success'=>1);
        }while(0);
        return $return;
    }

    /**
     * 线上购买订单
     * @param Vtiger_Request $request
     * @return array
     */
    public function isActiveOrder($detailproducts){
        if(count($detailproducts)<1){
            return false;
        }
        foreach ($detailproducts as $detailproduct){
            if($detailproduct['ActivityID']){
                return true;
            }
        }
        return false;
    }

    public function AddbuyOrderOnLine(Vtiger_Request $request){
        $data=array('module'=>'TyunWebBuyService',
            'action'=>'AddOnlineOrder',
            'mqdata'=>array(
                'rddata'=>$request->get('rddata'),
            )
        );
        $db=PearDatabase::getInstance();
        $rddata=$request->get('rddata');
        $rowdata=base64_decode($rddata);
        $rowdata = str_replace('&quot;','"',$rowdata);
        $resultData=json_decode($rowdata,true);
        sleep(1);
        if($resultData['order']['AgentSaleType']!=4){
            $result = $db->pquery("SELECT 1 FROM `vtiger_activationcode` WHERE ordercode=?",array($resultData['order']['OrderCode']));
            //  `Type` int(11) NOT NULL COMMENT '订单类型(0线上1线下)',
            //   `AgentSaleType` int(11) NOT NULL DEFAULT '0' COMMENT '售卖类型（0渠道 1直销 2OEM运营商 3渠道直销部 4外贸代理）'
            if(!$db->num_rows($result) && $resultData['order']['Type']==1){
                $this->_logs(array("AddbuyOrderOnLineFail",$resultData));
                return false;
            }
        }

        $this->_logs(array("AddbuyOrderOnLineSuccess",$resultData));
        $jsonData=json_encode($data);
        $return=array('success'=>0,'msg'=>'进入队列失败');
        $recordModel=new Vtiger_Record_Model();
        $flag  = $recordModel->rabbitMQPublisher($jsonData);
        return $flag;
    }

    public function updatePaymentCode(Vtiger_Request $request){
        global $adb;
        $usercode=$request->get('usercode');
        $paycode=$request->get('paycode');
        $paymentcode=$request->get('paymentcode');
        $query='SELECT 1 FROM vtiger_activationcode WHERE usercode=? AND paycode=? AND `status` in(0,1)';
        $result=$adb->pquery($query,array($usercode,$paycode));
        $array=array('status'=>1,'msg'=>'没有找到相关订单信息');
        if($adb->num_rows($result)){
            $sql='UPDATE vtiger_activationcode SET paymentcode=? WHERE usercode=? AND paycode=?';
            $adb->pquery($sql,array($paymentcode,$usercode,$paycode));
            $array=array('status'=>2,'msg'=>'');
        }
        return array($array);
    }

    /**
     * 根据客户ID找客户
     * @param $request
     */
    public function getAccountServiceInfo($request){
        global $adb;
        $accountid=$request->get('accountid');
        $query='SELECT (SELECT email1 from vtiger_users WHERE id=serviceid) AS email,(SELECT email1 from vtiger_users WHERE id=vtiger_crmentity.smownerid) AS businessemail FROM vtiger_account LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid WHERE vtiger_crmentity.deleted=0 AND accountid=? limit 1';
        $result=$adb->pquery($query,array($accountid));
        $array=array();
        if($adb->num_rows($result)){
            $data=$adb->query_result_rowdata($result,0);
            $array=array(array('email'=>$data['email'],'businessEmail'=>$data['businessemail']));
        }else{

        }
        return $array;
    }
    /**
     * @param $request
     */
    public function listCrmUserBasic($request){
        global $adb;
        $notIncludeUserIds=$request->get('notIncludeUserIds');
        $content=$request->get('content');
        $pageSize=$request->get('pageSize');
        $pageNum=$request->get('pageNum');
        $ids=$request->get('ids');
        $all=$request->get('all');
        $limit=' limit '.(($pageNum-1)*$pageSize).','.$pageSize;
        $field='';
        $fieldArray=array();
        if(!empty($content)){
            $field=' AND (vtiger_users.last_name like ? OR vtiger_users.user_name like ?)';
            $fieldArray=array($content.'%',$content.'%');
        }
        if(!empty($notIncludeUserIds)){
            $notIncludeUserIds=implode(',',array_map(function($v){return is_numeric($v)?$v:0;},explode(',',$notIncludeUserIds)));
            $field.= " AND vtiger_users.id NOT IN(".$notIncludeUserIds.")";
        }
        if(!empty($ids)){
            $ids=implode(',',array_map(function($v){return is_numeric($v)?$v:0;},explode(',',$ids)));
            $field.=' AND vtiger_users.id in('.$ids.')';
        }
        if(!empty($all) && $all == '1') {
        }else{
            $field.=" AND `status`='Active'";
        }
        $query="SELECT
                vtiger_users.user_name,
                vtiger_users.status,
                vtiger_users.isdimission,
                vtiger_users.last_name,
                vtiger_user2role.roleid,
                vtiger_role.rolename,
                vtiger_users.email1,
                vtiger_users.phone_mobile,
                vtiger_users.title,
                vtiger_users.department,
                vtiger_users.reports_to_id,
				(SELECT ru.last_name FROM vtiger_users ru WHERE ru.id=vtiger_users.reports_to_id LIMIT 1) AS reports_to_name,
                vtiger_user2department.departmentid,
				vtiger_departments.departmentname,
                vtiger_users.id
            FROM
                vtiger_users
            INNER JOIN vtiger_user2role ON vtiger_users.id = vtiger_user2role.userid
            INNER JOIN vtiger_role ON vtiger_role.roleid= vtiger_user2role.roleid
            INNER JOIN vtiger_user2department ON vtiger_users.id = vtiger_user2department.userid
            INNER JOIN vtiger_departments ON vtiger_user2department.departmentid = vtiger_departments.departmentid
            WHERE
                vtiger_users.id >1
            AND vtiger_user2department.departmentid != ''
            ".$field;
        $result=$adb->pquery($query.$limit,$fieldArray);
        $rows = $adb->num_rows($result);
        $ret_lists = array();
        $return=array();
        if ($rows>0){
            while ($row = $adb->fetchByAssoc($result)) {
                $lists = array();
                $lists['userName'] = $row['user_name'];
                $lists['lastName'] = $row['last_name'];
                $lists['email'] = $row['email1'];
                $lists['rolename'] = $row['rolename'];
                $lists['position'] = $row['rolename'];
                //$lists['groupName'] = $row['department'];
                $lists['groupName'] = $row['departmentname'];
                $lists['phone_mobile'] = $row['phone_mobile'];
                $lists['department'] = $row['departmentname'];
                $lists['reports_to_id'] = $row['reports_to_id'];
                $lists['supervisor'] = $row['reports_to_name'];
                $lists['departmentid'] = $row['departmentid'];
                $lists['roleid'] = $row['roleid'];
                $lists['status'] = $row['status'];
                $lists['isdimission'] = $row['isdimission'];
                $lists['id'] = $row['id'];
                $ret_lists[] = $lists;
            }
            $query="SELECT
                count(1) as counts
            FROM
                vtiger_users
            INNER JOIN vtiger_user2role ON vtiger_users.id = vtiger_user2role.userid
            INNER JOIN vtiger_role ON vtiger_role.roleid= vtiger_user2role.roleid
            INNER JOIN vtiger_user2department ON vtiger_users.id = vtiger_user2department.userid
            INNER JOIN vtiger_departments ON vtiger_user2department.departmentid = vtiger_departments.departmentid
            WHERE
                vtiger_users.id >1
            AND vtiger_user2department.departmentid != ''
            ".$field;
            $result=$adb->pquery($query,$fieldArray);
            $total=$adb->query_result($result,'counts',0);
            $return=array('list'=>$ret_lists,'total'=>$total);
        }
        return $return;
    }
    public function listCrmUserByRoleId($request){
        global $adb,$log;
        $roleids=$request->get('roleids');
        $fieldArray=array();
        $fieldIds='';
        if(!empty($roleids)){
            $roleidsmark=implode(',',array_map(function($v){return '?';},explode(',',$roleids)));
            $fieldIds=' AND vtiger_user2role.roleid in('.$roleidsmark.')';
            $fieldArray=explode(',',$roleids);
        }
        $query="SELECT vtiger_users.id,
                  vtiger_users.last_name 
                FROM vtiger_users 
                LEFT JOIN vtiger_user2role ON vtiger_users.id=vtiger_user2role.userid 
                WHERE vtiger_users.`status`='Active' 
                ".$fieldIds;
        $result=$adb->pquery($query,$fieldArray);
        $rows = $adb->num_rows($result);
        $ret_lists = array();
        $return=array();
        if ($rows>0){
            while ($row = $adb->fetchByAssoc($result)) {
                $lists = array();
                $lists['id'] = $row['id'];
                $lists['last_name'] = $row['last_name'];
                $ret_lists[] = $lists;
            }
            $return=array('list'=>$ret_lists);
        }
        return $return;
    }

    /**
     * 取得当前用的的下级
     * @param $request
     * @return array
     */
    public function findSubUserIdByUserId($request){
        $fromuserid=$request->get('fromuserid');
        $return=array();
        $SUBORDINATEUSERS = Vtiger_Cache::get('globalData','SUBORDINATEUSERS');
        if($SUBORDINATEUSERS){
            if(!empty($SUBORDINATEUSERS[$fromuserid])){
                $return=array('listids'=>$SUBORDINATEUSERS[$fromuserid]);
            }
        }else{
            global $root_directory;
            include $root_directory.'crmcache/subordinateusers.php';
            Vtiger_Cache::set('globalData','SUBORDINATEUSERS',$subordinate_users);
            if(!empty($subordinate_users[$fromuserid])){
                $return=array('listids'=>$subordinate_users[$fromuserid]);
            }
        }
        return $return;
        /*global $adb;
        $fromuserid=$request->get('fromuserid');
        $isall=$request->get('isall');
        if(1==$isall){
            $query="SELECT id,reports_to_id as rid FROM vtiger_users";
        }else{
            $query="SELECT id,reports_to_id as rid FROM vtiger_users WHERE `status`='Active'";
        }
        $result=$adb->pquery($query,array());
        $return=array();
        if($adb->num_rows($result)){
            $tempdata=array();
            while($row=$adb->fetch_array($result)){
                $tempdata[$row['rid']][]=array('id'=>$row['id'],'rid'=>$row['rid']);
            }
            $userIDs=$this->findSubUserID($tempdata,$fromuserid);
            if(count($userIDs)){
                $return=array('listids'=>$userIDs);
            }
        }
        return $return;*/
    }
    public function findSubUserID(&$tempdata,$fromuserid,&$arry=array()){
        if(empty($tempdata[$fromuserid])){
            return $arry;
        }
        foreach($tempdata[$fromuserid] as $value){
            $arry[]=$value['id'];
            $this->findSubUserID($tempdata,$value['id'],$arry);
        }
        return $arry;
    }
    public function https_request($url, $data = null,$curlset=array()){
        $curl = curl_init();
        if(!empty($curlset)){
            foreach($curlset as $key=>$value){
                curl_setopt($curl, $key, $value);
            }
        }
        $this->_logs(array($url,$data));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        $this->_logs(array('curl:',$output));
        curl_close($curl);
        return $output;
    }
    public function getInvoiceList($servicecontractsid){
        /*$query="SELECT
                    vtiger_newinvoice.invoiceid,vtiger_newinvoiceextend.invoiceextendid,vtiger_newinvoiceextend.billingtimeextend,vtiger_newinvoiceextend.invoicecodeextend,vtiger_newinvoiceextend.invoice_noextend,vtiger_newinvoiceextend.commoditynameextend,vtiger_newinvoiceextend.totalandtaxextend,vtiger_newinvoiceextend.processstatus,vtiger_newinvoiceextend.invoicestatus,(SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname FROM vtiger_departments WHERE departmentid = (SELECT	departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users WHERE vtiger_newinvoiceextend.operator = vtiger_users.id) AS operator,vtiger_newinvoiceextend.operatortime
                FROM
                    vtiger_newinvoiceextend
                LEFT JOIN vtiger_newinvoice ON vtiger_newinvoice.invoiceid = vtiger_newinvoiceextend.invoiceid
                LEFT JOIN vtiger_crmentity ON vtiger_newinvoiceextend.invoiceid = vtiger_crmentity.crmid
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.sc_related_to=vtiger_newinvoice.accountid
                WHERE
                vtiger_crmentity.deleted = 0
                AND vtiger_newinvoice.modulestatus = 'c_complete'
                AND vtiger_newinvoiceextend.invoicestatus = 'normal'
                AND
                vtiger_servicecontracts.servicecontractsid=?";*/
        $query="SELECT
                    vtiger_newinvoice.invoiceid,vtiger_newinvoiceextend.invoiceextendid,vtiger_newinvoiceextend.billingtimeextend,vtiger_newinvoiceextend.invoicecodeextend,vtiger_newinvoiceextend.invoice_noextend,vtiger_newinvoiceextend.commoditynameextend,vtiger_newinvoiceextend.totalandtaxextend,vtiger_newinvoiceextend.processstatus,vtiger_newinvoiceextend.invoicestatus,(SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname FROM vtiger_departments WHERE departmentid = (SELECT	departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users WHERE vtiger_newinvoiceextend.operator = vtiger_users.id) AS operator,vtiger_newinvoiceextend.operatortime
                FROM
                    vtiger_newinvoiceextend
                LEFT JOIN vtiger_newinvoice ON vtiger_newinvoice.invoiceid = vtiger_newinvoiceextend.invoiceid
                LEFT JOIN vtiger_crmentity ON vtiger_newinvoiceextend.invoiceid = vtiger_crmentity.crmid
                WHERE
                vtiger_crmentity.deleted = 0
                AND vtiger_newinvoice.modulestatus = 'c_complete'
                AND vtiger_newinvoiceextend.invoicestatus = 'normal'
                AND vtiger_newinvoice.contractid>0
                AND vtiger_newinvoice.contractid=?";
        $db = PearDatabase::getInstance();
        $result=$db->pquery($query,array($servicecontractsid));
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
     * 取得回款列表
     * @param $servicecontractsid
     */
    public function getReceivedPaymentsList($servicecontractsid){
        $db = PearDatabase::getInstance();
        $query="SELECT vtiger_receivedpayments.*,IFNULL((SELECT sum(vtiger_receivedpayments_extra.extra_price) FROM `vtiger_receivedpayments_extra` WHERE vtiger_receivedpayments_extra.receivementid=vtiger_receivedpayments.receivedpaymentsid),0) AS sumextra_price FROM `vtiger_receivedpayments` where receivedstatus='normal' AND vtiger_receivedpayments.deleted=0 AND relatetoid=? AND relatetoid>0";
        $result = $db->pquery($query,array($servicecontractsid));
        $stages=array();
        $receivedpaymentsid=array();
        $num=$db->num_rows($result);
        for($i=0; $i<$num; $i++) {
            $row=$db->query_result_rowdata($result, $i);
            $stages[]=$row;
            $receivedpaymentsid[]=$row['receivedpaymentsid'];
        }
        $receivedpaymentsid=empty($receivedpaymentsid)?array(0):$receivedpaymentsid;
        $sql = "SELECT achievementallotid, owncompanys, receivedpaymentsid, ( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_achievementallot.receivedpaymentownid = vtiger_users.id ) AS receivedpaymentownid, businessunit,scalling FROM `vtiger_achievementallot` WHERE receivedpaymentsid in(".implode(',',$receivedpaymentsid).")";
        $achievementallot = $db->pquery("$sql",array());
        $nums = $db->num_rows($achievementallot);
        $achievementallotdata = array();
        if($nums > 0) {
            for($i=0; $i<$nums; ++$i) {
                $row = $db->query_result_rowdata($achievementallot, $i);
                $achievementallotdata[$row['receivedpaymentsid']][] = $row;
            }
        }
        return  array('receivedpaymentlist'=>$stages,'achievementallotdata'=>$achievementallotdata);
    }

    /**
     * @param $request
     */
    public function getProductsServicescontract($request){
        global $adb;
        $userID=$request->get('tyunusercode');
        $tyunusername=$request->get('tyunusername');
        $categoryid=$request->get('categoryid');
        $classtype=$request->get('classtype');

        $query="SELECT * FROM vtiger_activationcode WHERE usercodeid=? AND usercode=? AND productclass=? AND comeformtyun=1 AND vtiger_activationcode.`status`!=2 order by createdtime asc";
        $result=$adb->pquery($query,array($userID,$tyunusername,$categoryid));
        if($adb->num_rows($result)){
            $array=array();
            while($row=$adb->fetch_array($result)){
                if($row['classtype']=='buy'){
//                    $temp['updateinfo']='';
//                    $temp['updateinfo']=$row['contractname'].'/'.$row['createdtime'];
                    $array['contractname']=$row['contractname'];
                }
                if($row['classtype']==$classtype){
                    $array['updateinfo']=$row['contractname'].'/'.$row['createdtime'];
                    //$array=$row;
                }
            }
            return array($array);
        }
        return array(array());
    }
    public function _logs($data, $file = 'tweblog_'){
        $year	= date("Y");
        $month	= date("m");
        $dir	= './logs/tyun/' . $year . '/' . $month . '/';
        if(!is_dir($dir)) {
            mkdir($dir,0755,true);
        }
        $file = $dir . $file . date('Y-m-d').'.txt';
        @file_put_contents($file, '----------------' . date('H:i:s') . '--------------------'.PHP_EOL.var_export($data,true).PHP_EOL, FILE_APPEND);
    }
    public function doOrderCancelByContractNo($contract_no,$user_id,$usercode,$is_update=true){
        global $tyunweburl,$sault,$adb;
        $url=$tyunweburl.'api/micro/order-basic/v1.0.0/api/Order/CancelOrderByContractCode';
        $time=time().'123';
        $sault1=$time.$sault;
        $token=md5($sault1);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $canceldata=json_encode(array("userID"=>$user_id,"contractCode"=>$contract_no));
        $this->_logs(array('orderdocancelbycontractno:',$canceldata));
        $data=$this->https_request($url,$canceldata,$curlset);
        $this->_logs(array($url,$data));
        $jsonData=json_decode($data,true);
        if($jsonData['code']=='200'&&$is_update){
            $sql="UPDATE  vtiger_activationcode SET `status`=2,orderstatus='ordercancel',canceldatetime=?,isdisabled=1 WHERE contractname=?";
            $adb->pquery($sql,array(date('Y-m-d H:i:s'),$contract_no));
            $sql=" UPDATE  vtiger_activationcode SET  isclientmigration=0 WHERE usercode=? AND comeformtyun=0 ";
            $adb->pquery($sql,array($usercode));
            $this->sendCancelWx($contract_no);

            //取消订单成功后重新计算业绩
            $receiveRecordModel=Vtiger_Record_Model::getCleanInstance('ReceivedPayments');
            $receiveRecordModel->modifyAchievement($contract_no);
        }
        return $data;
    }

    public function sendCancelWx($contract_no){
        $db =PearDatabase::getInstance();
        //发给客户负责人、提单人、领取人 去解绑回款
        $result = $db->pquery("select b.servicecontractsid,c.smownerid,b.signid,b.receiveid from vtiger_receivedpayments a 
  left join vtiger_servicecontracts b on a.relatetoid=b.servicecontractsid 
  left join vtiger_crmentity c on b.servicecontractsid=c.crmid 
  where b.contract_no=? and c.deleted=0 and a.ismatchdepart=1",array($contract_no));
        if(!$db->num_rows($result)){
            return;
        }
        $row = $db->fetchByAssoc($result,0);
        $notifyUserId=array_unique(array($row['smownerid'],$row['signid'],$row['receiveid']));
        $result2 = $db->pquery("select email1,wechatid from vtiger_users where id in(".implode(',',$notifyUserId).')',array());
        if(!$db->num_rows($result2)){
            return;
        }
        while ($row2=$db->fetchByAssoc($result2)){
            $email = $row2['email1'].'|';
        }

        $title = '提醒：订单作废成功';
        $content = '订单作废成功，但所属合同'.$contract_no.'名下存在已匹配的回款，请前往回款匹配记录中进行解绑，谢谢';
        $this->_logs(array('email'=>trim($email),'description'=>$content,'dataurl'=>'#','title'=>$title,'flag'=>7));
        $this->sendWechatMessage(array('email'=>trim($email),'description'=>$content,'dataurl'=>'#','title'=>$title,'flag'=>7));


    }

    public function doOrderCancel($resultData){
        global $tyunweburl,$sault,$adb;
        $query='SELECT 1 FROM vtiger_activationcode WHERE activationcodeid>? AND contractid != ? AND usercodeid=? AND `status`!=2';
        $result=$adb->pquery($query,array($resultData['activationcodeid'],$resultData['contractid'],$resultData['usercodeid']));
        if($adb->num_rows($result)){
            return '{"success":false,"code":500,"message":"请先将该账号对应的续费,或升降级合同作废掉再进行操作"}';
        }
        $url=$tyunweburl.'api/micro/order-basic/v1.0.0/api/Order/CancelOrder';
        $time=time().'123';
        $sault1=$time.$sault;
        $token=md5($sault1);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $canceldata=json_encode(array("userID"=>$resultData['usercodeid'],"OrderCode"=>$resultData['ordercode']));
        $this->_logs(array('orderdocancel:',$canceldata));
        $data=$this->https_request($url,$canceldata,$curlset);
        $this->_logs(array($url,$data));
        $jsonData=json_decode($data,true);
        if($jsonData['code']=='200'){
            $sql="UPDATE  vtiger_activationcode SET `status`=2,orderstatus='ordercancel',canceldatetime=?,isdisabled=1 WHERE activationcodeid=?";
            $adb->pquery($sql,array(date('Y-m-d H:i:s'),$resultData['activationcodeid']));
            $sql=" UPDATE  vtiger_activationcode SET  isclientmigration=0 WHERE usercode=? AND comeformtyun=0 ";
            $adb->pquery($sql,array($resultData['usercode']));
        }
        return $data;
    }
    public function NewTwebSendMail($data){
        $userid=$data['userid'];
        global $adb,$current_user,$isDev;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
        $sql = "SELECT * FROM `vtiger_users` WHERE id=? LIMIT 1";
        $sel_result = $adb->pquery($sql, array($current_user->reports_to_id));
        $res_cnt = $adb->num_rows($sel_result);
        $agent_email = '';
        $agent_last_name = '';
        if($res_cnt > 0) {
            $row = $adb->query_result_rowdata($sel_result, 0);
            $agent_email = $row['email1'];
            $agent_last_name = $row['last_name'];
        }

        $email = $current_user->email1;
        $last_name = $current_user->last_name;
        $departmentDataResult =  $adb->pquery("select c.departmentname from vtiger_users a  left join vtiger_user2department b on a.id=b.userid left join  vtiger_departments c on c.departmentid=b.departmentid where a.id=? limit 1",array($current_user->id));
        $departmentDataRow = $adb->fetchByAssoc($departmentDataResult,0);
        $department = $departmentDataRow['departmentname'];
        $this->_logs(array('department'=>$department));

        $upmail=$this->getBranchEmail($userid);
        $Subject = '71360下单成功通知';
        $ProductType=array('buy'=>'购买','upgrade'=>'升级','degrade'=>'降级','renew'=>'续费');
        $classtype=$data['classtype'];
        $type = $ProductType[$classtype];
        if($classtype == 'buy'){
            //升级
            $body =  "员工：{$last_name}<br>部门：{$department}<br>客户：{$data['accountid_display']}<br>合同编号：{$data['servicecontractsid_display']}<br>购买版本：{$data['productname']}<br>年限：{$data['buyTerm']} 年<br>";
        }else if($classtype == 'renew'){
            //续费
            $body = "员工：{$last_name}<br>部门：{$department}<br>客户：{$data['accountid_display']}<br>合同编号：{$data['servicecontractsid_display']}<br>续费版本：{$data['productname']}<br>续费年限：{$data['buyTerm']} 年<br>";
        }else if(in_array($classtype,array('cupgrade','cdegrade','crenew'))){
            $body =  "员工：{$last_name}<br>部门：{$department}<br>客户：{$data['accountid_display']}<br>合同编号：{$data['servicecontractsid_display']}<br>购买版本：{$data['productname']}<br>年限：{$data['buyTerm']} 年<br>";
        }else{
            $body =  "员工：{$last_name}<br>部门：{$department}<br>客户：{$data['accountid_display']}<br>合同编号：{$data['servicecontractsid_display']}<br>{$type}版本：{$data['productname']}<br>{$type}年限：{$data['buyTerm']} 年<br>";
        }
        if($data['signaturetype']=='eleccontract'){
            $body.="订单状态：未生效<br>";
        }
        if($data['contractmoney']){
            $body.="合同金额：".$data['contractmoney']."<br>";
        }
        $upmail=!empty($upmail)?$upmail:$email;
        $address = array(
            array('mail'=>$upmail, 'name'=>''),//营总监
            array('mail'=>$email, 'name'=>$last_name),//负责人
            array('mail'=>$agent_email, 'name'=>$agent_last_name),//负责人上级
        );
        $this->_logs(array('body'=>$body,'address'=>$address));
        Vtiger_Record_Model::sendMail($Subject,$body,$address);
    }
    public function TwebSendMail($request){
        $userid=$request->get('userid');
        global $adb,$current_user;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
        $sql = "SELECT * FROM `vtiger_users` WHERE id=? LIMIT 1";
        $sel_result = $adb->pquery($sql, array($current_user->reports_to_id));
        $res_cnt = $adb->num_rows($sel_result);
        $agent_email = '';
        $agent_last_name = '';
        if($res_cnt > 0) {
            $row = $adb->query_result_rowdata($sel_result, 0);
            $agent_email = $row['email1'];
            $agent_last_name = $row['last_name'];
        }

        $email = $current_user->email1;
        $last_name = $current_user->last_name;
        $departmentDataResult =  $adb->pquery("select c.departmentname from vtiger_users a  left join vtiger_user2department b on a.id=b.userid left join  vtiger_departments c on c.departmentid=b.departmentid where a.id=? limit 1",array($current_user->id));
        $departmentDataRow = $adb->fetchByAssoc($departmentDataResult,0);
        $department = $departmentDataRow['departmentname'];
        $upmail=$this->getBranchEmail($userid);


        $Subject = '71360下单成功通知';
        $ProductType=array('buy'=>'购买','upgrade'=>'升级','degrade'=>'降级','renew'=>'续费');
        $classtype=$request->get('classtype');
        $type = $ProductType[$classtype];
        if($classtype == 'buy'){
            //升级
            $body =  "员工：{$last_name}<br>部门：{$department}<br>客户：{$request->get('accountid_display')}<br>合同编号：{$request->get('servicecontractsid_display')}<br>购买版本：{$request->get('productname')}<br>年限：{$request->get('buyTerm')} 年<br>";
        }else if($classtype == 'renew'){
            //续费
            $body = "员工：{$last_name}<br>部门：{$department}<br>客户：{$request->get('accountid_display')}<br>合同编号：{$request->get('servicecontractsid_display')}<br>续费版本：{$request->get('productname')}<br>续费年限：{$request->get('buyTerm')} 年<br>";
        }else if(in_array($classtype,array('cupgrade','cdegrade','crenew'))){
            $body =  "员工：{$last_name}<br>部门：{$department}<br>客户：{$request->get('accountid_display')}<br>合同编号：{$request->get('servicecontractsid_display')}<br>购买版本：{$request->get('productname')}<br>年限：{$request->get('buyTerm')} 年<br>";
        }else{
            $body =  "员工：{$last_name}<br>部门：{$department}<br>客户：{$request->get('accountid_display')}<br>合同编号：{$request->get('servicecontractsid_display')}<br>{$type}版本：{$request->get('productname')}<br>{$type}年限：{$request->get('buyTerm')} 年<br>";
        }
        $upmail=!empty($upmail)?$upmail:$email;
        $address = array(
            array('mail'=>$upmail, 'name'=>''),//营总监
            array('mail'=>$email, 'name'=>$last_name),//负责人
            array('mail'=>$agent_email, 'name'=>$agent_last_name),//负责人上级
        );
        Vtiger_Record_Model::sendMail($Subject,$body,$address);

    }
    public function rebindContract($request){
        global $adb;
        $record=$request->get('record');
        $contractno=$request->get('newrecord');
        $contractno=trim($contractno);
        $query='SELECT * FROM vtiger_activationcode WHERE activationcodeid=? LIMIT 1';
        $result=$adb->pquery($query,array($record));
        $data=$adb->raw_query_result_rowdata($result,0);
        global $tyunweburl;
        $tyunweburl1=$tyunweburl.'api/micro/order-basic/v1.0.0/api/Order/UpdateContractCode';
        $time=time().'123';
        global $sault;
        $sault1=$time.$sault;
        $token=md5($sault1);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $rebinddata=json_encode(array("userID"=>$data['usercodeid'],"OrderCode"=>$data['ordercode'],"ContractCode"=>$contractno));
        $Repson=$this->https_request($tyunweburl1,$rebinddata,$curlset);
        return $Repson;

    }

    public function rebindContractByContractNo($request){
        global $adb;
        $record=$request->get('record');
        $contractno=$request->get('newrecord');
        $contractno=trim($contractno);
        $query='SELECT * FROM vtiger_activationcode WHERE activationcodeid=? LIMIT 1';
        $result=$adb->pquery($query,array($record));
        $data=$adb->raw_query_result_rowdata($result,0);
        global $tyunweburl;
        $tyunweburl1=$tyunweburl.'api/micro/order-basic/v1.0.0/api/Order/UpdateContractCode';
        $time=time().'123';
        global $sault;
        $sault1=$time.$sault;
        $token=md5($sault1);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $rebinddata=json_encode(array("userID"=>$data['usercodeid'],"oldContractCode"=>$data['contractname'],"ContractCode"=>$contractno));
        $Repson=$this->https_request($tyunweburl1,$rebinddata,$curlset);
        return $Repson;

    }

    /**
     * 每个月的第一个工作日
     * @return int
     */
    public function getFirstWorkDay(){
        global $adb;
        $year=date('Ym').'01';
        $query="SELECT * FROM `vtiger_workday` WHERE workdayid>=? AND datetype='holiday' limit 20";
        $result=$adb->pquery($query,array($year));
        $data=array();
        while($row=$adb->fetch_array($result)){
            $data[]=$row['workdayid'];
        }
        $reality_date=date('Ym').'02';
        $currentDay=date('Ymd');
        if(!in_array($reality_date,$data)){
            if($reality_date>=$currentDay){
                return 2;
            }
            return 1;
        }
        $legalHoliday=$this->LegalHoliday($reality_date,$data);
        $legalHoliday=$legalHoliday+1;
        if(!in_array($legalHoliday,$data)){
            if($legalHoliday>=$currentDay){
                return 2;
            }
            return 1;
        }
        $legalHoliday=$this->LegalHoliday($legalHoliday,$data);
        if($currentDay<=$legalHoliday){
            return 2;
        }
        return 1;
    }

    /**
     * @param $currentday
     * @param $data
     * @return false|string
     * @author: steel.liu
     * @Date:xxx
     * 法定假日内
     */
    public function LegalHoliday($currentday,$data){
        $year=substr($currentday,0,4);
        $month=substr($currentday,4,2);
        $day=substr($currentday,6,2);
        $currentday=$year.'-'.$month.'-'.$day;
        $workdayid=date('Ymd', strtotime ($currentday." +1 day"));
        if(in_array($workdayid,$data)){
            return $this->LegalHoliday($workdayid,$data);
        }else{
            return $workdayid;
        }
    }
    public function getSalesANDCustomerServiceBYCID($request){
        $db=PearDatabase::getInstance();
        $rddata=$request->get('rddata');
        $rowdata=base64_decode($rddata);
        $resultData=json_decode($rowdata,true);
        if(empty($resultData)){
            return array(array());
        }
        $cid=$resultData['cid'];
        $cid=array_map(function($v){return is_numeric($v)?$v:-1;},$cid);
        $cidstr=array_map(function($v){return '?';},$cid);
        $query="SELECT sales.last_name as salesname,sales.phone_mobile AS salesphone,service.last_name as customerservicename,service.phone_mobile AS customerservicephone,crmid FROM vtiger_account 
                LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid
                LEFT JOIN vtiger_users sales ON sales.id=vtiger_crmentity.smownerid
                LEFT JOIN vtiger_users service ON service.id=vtiger_account.serviceid WHERE vtiger_crmentity.deleted=0 AND vtiger_account.accountid in(".implode(',',$cidstr).")";
        $result=$db->pquery($query,array($cid));
        $array=array();
        if($db->num_rows($result)){
            while($row=$db->fetch_array($result)){
                $array[]=array('salesName'=>$row['salesname'],
                    'salesPhone'=>$row['salesphone'],
                    'customerServiceName'=>$row['customerservicename'],
                    'customerServicePhone'=>$row['customerservicephone'],
                    'cid'=>(int)$row['crmid']
                    );
            }
        }
        return $array;

    }
    public function getClientMigration($request){
        global $adb;
        $accountid=$request->get('accountid');
        $query='SELECT distinct usercode FROM vtiger_activationcode WHERE customerid=? AND comeformtyun=0 AND `status` in(0,1) AND classtype in(\'buy\',\'upgrade\',\'degrade\') AND isclientmigration=0 AND usercode !="" AND usercode is not NULL';
//        $query='SELECT usercode FROM vtiger_activationcode WHERE customerid=? AND comeformtyun=0 AND `status` in(0,1) AND classtype in(\'buy\',\'upgrade\',\'degrade\') AND isclientmigration=0 limit 1';
        $result=$adb->pquery($query,array($accountid));
        $data=array();
        if($adb->num_rows($result)){
            while ($row = $adb->fetch_row($result)){
                $data[] = $row['usercode'];
            }
        }
        return $data;
    }

    public function getOldClientMigration($request){
        global $adb;
        $usercode=$request->get('usercode');
//        $query='SELECT customerid,customername,mobile FROM vtiger_activationcode WHERE usercode=?  AND `status` in(0,1) AND classtype in(\'buy\',\'upgrade\',\'degrade\') AND isclientmigration=0 limit 1';
        $query = 'SELECT a.customerid,b.accountname as customername FROM vtiger_activationcode a LEFT JOIN vtiger_account b on a.customerid=b.accountid WHERE a.usercode=?  AND a.status in(0,1) AND a.classtype in(\'buy\',\'upgrade\',\'degrade\') AND a.isclientmigration=0  AND comeformtyun=0 limit 1';
        $result=$adb->pquery($query,array($usercode));
        if($adb->num_rows($result)){
            $array=$adb->raw_query_result_rowdata($result,0);
            if(!empty($array['customerid']) && !empty($array['customername'])){
                return array('customerid'=>$array['customerid'],'customername'=>$array['customername']);
            }
        }
        return array();
    }
    //获取T云升级/降级产品
    public function searchTyunUpgradeProduct(Vtiger_Request $request)
    {
        global $adb,$activation_code_tyun_upgrade_product_url;
        $parm_productid = trim($request->get('p_productid'));
        $is_getname = trim($request->get('is_getname'));
        //是否降级
        $is_degrade = trim($request->get('is_degrade'));
        $contentxml = simplexml_load_file($activation_code_tyun_upgrade_product_url);
        $tyun_products = (array)$contentxml->Client_Products;
        $tyun_products = $tyun_products["Product"];

        $products_count = count($tyun_products);
        $arr_product = array();

        for($i=0;$i<$products_count;$i++){
            $arr_product[$i] = $contentxml->Client_Products->Product[$i];
        }

        $arr_new_product = array();
        $arr_c_productid = array();
        foreach($arr_product as $k=>$v){
            $p_productId = (array)$v["ID"];
            $p_productId = $p_productId[0];
            if($p_productId == $parm_productid) {
                if($is_degrade == '1'){
                    $xml_c_product = (array)$v->DegradeVersions;
                    $xml_c_product = (array)$xml_c_product["DegradeVersion"];
                }else{
                    $xml_c_product = (array)$v->UpgradeVersions;
                    $xml_c_product = (array)$xml_c_product["UpgradeVersion"];
                }

                foreach ($xml_c_product as $k1 => $v1) {
                    $c_productid = (array)$v1["ID"];
                    $c_productid = $c_productid[0];
                    $name=(array)$v1["Name"];
                    $name=$name[0];
                    $arr_c_productid[] = array("ID"=>$c_productid,'Title'=>$name);
                }
            }
        }
        return $arr_c_productid;
    }

    /**
     * 计算预付款
     * @param $request
     * @return array
     * @throws Exceptionq
     */
    public function calcSurplusMoney($request){
        global $adb;
        $usercode=$request->get('usercode');
        $query='SELECT left(expiredate,10) as expiredate,contractid,productlife,classtype,left(receivetime,10) AS receivetime,left(startdate,10) AS startdate FROM vtiger_activationcode WHERE usercode=? AND `status` in(0,1) AND comeformtyun=0 AND left(expiredate,10)>?';
        $result=$adb->pquery($query,array($usercode,date('Y-m-d')));
        //$result=$adb->pquery($query,array('34625020',date('Y-m-d')));
        $SurplusMoney=0;
        if($adb->num_rows($result)){
            $currenttime=strtotime(date('Y-m-d'));
            while($row=$adb->fetch_array($result)){
                $query='SELECT vtiger_servicecontracts.total FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE deleted=0 AND vtiger_servicecontracts.servicecontractsid=? and vtiger_servicecontracts.modulestatus=\'c_complete\'';
                $serResult=$adb->pquery($query,array($row['contractid']));
                if($adb->num_rows($serResult)){
                    $resultData=$adb->raw_query_result_rowdata($serResult,0);
                    if(in_array($row['classtype'],array('buy','','degrade','upgrade'))){
                        $starttime=strtotime($row['receivetime']);
                    }else{
                        $starttime=strtotime($row['startdate']);
                    }
                    if($currenttime<=$starttime){
                        $SurplusMoney=bcadd($SurplusMoney,$resultData['total'],6);
                    }else{
                        $starttime=strtotime($row['expiredate']);
                        if($starttime>$currenttime){
                            $starttime=$starttime-$currenttime;
                            $starttime=$starttime/(24*60*60);
                            $productlife=$row['productlife']*365;
                            $SurplusMoney=bcadd($SurplusMoney,bcmul(bcdiv($resultData['total'],$productlife,6),$starttime,6),6);
                        }
                    }
                }
            }
        }
        return round($SurplusMoney,2);
    }

    /**
     * 数据迁移成发短信和邮件
     * @param $request
     * @return bool
     * @throws Exception
     */
    public function clientMigrationBySendMail($request){
        global $adb;
        $accountid=$request->get('ordercode');
        $query='SELECT usercode,customername,creator,contractname,productname,productlife,mobile FROM vtiger_activationcode WHERE ordercode=? AND comeformtyun=1 AND `status` in(0,1) limit 1';
        $result=$adb->pquery($query,array($accountid));
        if($adb->num_rows($result)){
            $rawData=$adb->raw_query_result_rowdata($result,0);
            $this->web71360SendSMS(array("usercode"=>$rawData['usercode'],
                    "productname"=>$rawData['productname'],
                    "customername"=>$rawData['customername'],
                    "mobile"=>$rawData['mobile'],
                    "flag"=>3,
                )
            );
            $sql = "SELECT vtiger_users.*,vtiger_departments.* FROM `vtiger_users` LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid WHERE id=? LIMIT 1";
            $sel_result = $adb->pquery($sql, array($rawData['creator']));
            $res_cnt = $adb->num_rows($sel_result);
            if($res_cnt > 0) {
                $row = $adb->query_result_rowdata($sel_result, 0);
                $agent_email = trim($row['email1']);
                $last_name = $row['last_name'];
                $department = $row['departmentname'];
                $Subject = '71360迁移完成通知';
                $body =  "员工：{$last_name}<br>部门：{$department}<br>客户：{$rawData['customername']}<br>合同编号：{$rawData['contractname']}<br>购买版本：{$rawData['productname']}<br>年限：{$rawData['productlife']} 年<br/><br/>以上，已成功将服务迁移到Web平台。<br>";
                $address = array(
                    array('mail'=>trim($agent_email), 'name'=>$last_name)
                );
                Vtiger_Record_Model::sendMail($Subject,$body,$address);
            }
            return true;
        }
        return false;
    }

    public function isFirstOrderByUserCode($usercode){
        global $adb;
        $query='SELECT 1 FROM `vtiger_activationcode` WHERE usercode=?';
        $result=$adb->pquery($query,array($usercode));
        if($adb->num_rows($result)){
            return false;
        }
        return true;
    }

    /**
     * 71360平台发短信
     */
    public function web71360SendSMS($data=array()){
        global $adb,$tyunweburlsms,$sault;
        if($data['flag']==1){
//            $query='SELECT 1 FROM `vtiger_activationcode` WHERE usercode=?';
//            $result=$adb->pquery($query,array($data['usercode']));
            $flag=2;
            if($data['is_first_order']){
                $query='SELECT * FROM `vtiger_tyunregpasswd` WHERE usercode=?';
                $result=$adb->pquery($query,array($data['usercode']));
                if($adb->num_rows($result)){
                    $resultData=$adb->raw_query_result_rowdata($result,0);
                    $flag=1;
                }
            }
            $productname=$data['productname'];
            $productname=preg_replace('/\(\d+\)/','',$productname);
            if($flag==1){//新用户下单完成
                $content='【珍岛集团】亲爱的'.$data['customername'].'，您购买的'.$productname.'已下单成功，您的用户名：'.$data['usercode'].'，密码为:'.$resultData['passwd'].'。为了您的账号安全，建议尽快修改登录密码。如您在使用过程中有任何疑问，可联系您的专属客服或拨打服务热线：400-880-0762；感谢您的支持！';
            }elseif($flag==2) {//老用户下单完成
                $content='【珍岛集团】亲爱的'.$data['usercode'].'，您购买的'.$productname.'已下单成功，赶快去体验吧！如您在使用过程中有任何疑问，可联系您的专属客服或拨打服务热线：400-880-0762；感谢您的支持！';
            }else{
                return ;
            }
        }elseif($data['flag']==2){
            $productname=$data['productname'];
            $productname=preg_replace('/\(\d+\)/','',$productname);
            $content='【珍岛集团】亲爱的'.$data['usercode'].'，您购买的'.$productname.'已下单成功，迁移成功后会以短信提醒！如您在使用过程中有任何疑问，可联系您的专属客服或拨打服务热线：400-880-0762；感谢您的支持！';
        }else {//数据迁称完成发短信
            $content='【珍岛集团】亲爱的'.$data['customername'].'，您购买的服务已开通，赶快去体验吧！并已将您的用户名：'.$data['usercode'].'的服务迁移到Web平台，请登录：https://www.71360.com/，开启T云之旅。';
        }
        $tyunweburl1=$tyunweburlsms.'api/app/aggregateservice-api/v1.0.0/api/SMS/SendMobileMessage';
        $time=time().'123';
        $sault1=$time.$sault;
        $token=md5($sault1);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));

        $rebinddata=json_encode(array("mobile"=>$data['mobile'],"content"=>$content));
        $Repson=$this->https_request($tyunweburl1,$rebinddata,$curlset);
    }


/*
* 71360平台发短信
*/
    public function web71360SendSMSNew(Vtiger_Request $request){
        $data = array(
            'usercode'=>$request->get('usercode'),
            'productname'=>$request->get('productname'),
            'customername'=>$request->get('customername'),
            'passwd'=>$request->get('passwd'),
            'mobile'=>$request->get('mobile'),
            'flag'=>$request->get('flag')
        );

        global $adb,$tyunweburlsms,$sault;
        if($data['flag']==1){
            $query='SELECT 1 FROM `vtiger_activationcode` WHERE usercode=?';
            $result=$adb->pquery($query,array($data['usercode']));
            $flag=2;
            if($adb->num_rows($result)==1){
                $query='SELECT * FROM `vtiger_tyunregpasswd` WHERE usercode=?';
                $result=$adb->pquery($query,array($data['usercode']));
                if($adb->num_rows($result)){
                    $resultData=$adb->raw_query_result_rowdata($result,0);
                    $flag=1;
                }
            }
            $productname=$data['productname'];
            $productname=preg_replace('/\(\d+\)/','',$productname);
            if($flag==1){//新用户下单完成
                $content='【珍岛集团】亲爱的'.$data['customername'].'，您购买的'.$productname.'已下单成功，您的用户名：'.$data['usercode'].'，密码为:'.$resultData['passwd'].'。为了您的账号安全，建议尽快修改登录密码。如您在使用过程中有任何疑问，可联系您的专属客服或拨打服务热线：400-880-0762；感谢您的支持！';
            }elseif($flag==2) {//老用户下单完成
                $content='【珍岛集团】亲爱的'.$data['usercode'].'，您购买的'.$productname.'已下单成功，赶快去体验吧！如您在使用过程中有任何疑问，可联系您的专属客服或拨打服务热线：400-880-0762；感谢您的支持！';
            }else{
                return ;
            }
        }elseif($data['flag']==2){
            $productname=$data['productname'];
            $productname=preg_replace('/\(\d+\)/','',$productname);
            $content='【珍岛集团】亲爱的'.$data['usercode'].'，您购买的'.$productname.'已下单成功，迁移成功后会以短信提醒！如您在使用过程中有任何疑问，可联系您的专属客服或拨打服务热线：400-880-0762；感谢您的支持！';
        }else {//数据迁称完成发短信
            $content='【珍岛集团】亲爱的'.$data['customername'].'，您购买的服务已开通，赶快去体验吧！并已将您的用户名：'.$data['usercode'].'的服务迁移到Web平台，请登录：https://www.71360.com/，开启T云之旅。';
        }
        $tyunweburl1=$tyunweburlsms.'api/app/aggregateservice-api/v1.0.0/api/SMS/SendMobileMessage';
        $time=time().'123';
        $sault1=$time.$sault;
        $token=md5($sault1);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));

        $rebinddata=json_encode(array("mobile"=>$data['mobile'],"content"=>$content));
        $Repson=$this->https_request($tyunweburl1,$rebinddata,$curlset);
    }

    /**
     * 保存第一次注册的用户名密码
     * @param $request
     * @return int
     */
    public function putTyunUsercodeAndPasswd($request){
        global $adb;
        $tyunusercode =$request->get('tyunusercode');
        $tyunpassword=$request->get('tyunpassword');
        $sql='REPLACE INTO vtiger_tyunregpasswd(usercode,passwd) values(?,?)';
        $adb->pquery($sql,array($tyunusercode,$tyunpassword));
        return 1;
    }
    /**
     * cxh 2019-10-16 add
     * 得到 该详情的有关已归档数据信息
     */
    public function getOneFiledData($activationcodeid){
        global $adb;
        if($activationcodeid){
            $sql = " SELECT * FROM vtiger_activationcode_file WHERE activationcodeid = ? LIMIT 1 ";
            $result = $adb->pquery($sql,array($activationcodeid));
            $data = $adb->raw_query_result_rowdata($result,0);
            return  $data;
        }else{
            return false;
        }
    }

    /**
     * 获取产品列表
     */
    public static function getProductNamesByContractId($contractid){
        global $adb;
        $productnames = '';
        $query="SELECT * FROM vtiger_activationcode WHERE contractid=? AND comeformtyun=1 AND pushstatus=0 AND `status`!=2";
        $result=$adb->pquery($query,array($contractid));
        if($adb->num_rows($result)>0){
            while ($row = $adb->fetch_row($result)){
                $productnames .= $row['productname'].'、';
            }
        }
        return rtrim($productnames,'、');
    }
    /**
     * 根据合同ID获取订单支付编号
     *
     * juwei.nie 20200420
     */
    public function getPayCodeByContractId($contractid){
        global $adb;
        $payCode = '';
        $query="SELECT payCode FROM vtiger_activationcode WHERE contractid=? AND comeformtyun=1 AND pushstatus=0 AND `status`!=2";
        $result=$adb->pquery($query,array($contractid));
        if($adb->num_rows($result)>0){
            while ($row = $adb->fetch_row($result)){
                $payCode = $row['payCode'];
            }
        }
        return $payCode;
    }
	/**
     * 根据客户名获取CID
     */
    public function tyunWebGetACIDByAccountName($request){
        global $adb;
        $accountname=$request->get('accountname');
        $accountname=trim($accountname);
        $label=preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;|&quot;|　|&apos;|&amp;|&lt;|&gt;|&#039;|&ldquo;|&rdquo;|&lsquo;|&rsquo;|&hellip;|\“|\”|\‘|\〉|\〈|\’|\〖|\〗|\【|\】|\、|\·|\…|\——|\＋|\－|\＝|\,|\<|\.|\>|\/|\?|\;|\:|\\\'|\"|\[|\{|\]|\}|\\|\||\`|\~|\!|\@|\#|\$|\%|\^|\\&|\*|\(|\)|\-|\_|\=|\+|\，|\＜|\．|\＞|\／|\？|\；|\：|\＇|\＂|\［|\{|\］|\}|\＼|\｜|\｀|\￣|\！|\＠|\#|\＄|\％|\＾|\＆|\＊|\（|\）|\－|\＿|\＝|\＋|\，|\《|\．|\》|\、|\？|\；|\：|\’|\”|\【|\｛|\】|\｝|\＼|\｜|\·|\～|\！|\＠|\＃|\￥|\％|\…|\＆|\×|\（|\）|\－|\——|\—|\＝|\＋|\，|\《|\。|\》|\、|\？|\；|\：|\‘|\“|\【|\｛|\】|\｝|\、|\||\·|\~|\！|\@|\#|\￥|\%|\&|\*|\（|\）|\-|\——|\=|\+/u','',$accountname);
        $labelname=strtoupper($label);
        $sql = "SELECT
				vtiger_crmentity.label,
				vtiger_crmentity.crmid
			FROM
				vtiger_uniqueaccountname
			LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_uniqueaccountname.accountid
			WHERE
				deleted = 0
			AND vtiger_uniqueaccountname.accountname =?
			LIMIT 1";
        $listResult = $adb->pquery($sql, array($labelname));
        $res=array();
        if($adb->num_rows($listResult)){
            $resultData=$adb->query_result_rowdata($listResult,0);
            $res=array("cid"=>$resultData['crmid'],'accountname'=>$resultData['label']);
        }
        return $res;
    }

        /**
     * 是否显示手动创建电子合同按钮
     *
     * @param $recordid
     * @param $userid
     * @return bool
     */


 public static function isShowCreateServiceContract($recordid,$userid){
        global $adb;
        $sql = "select * from vtiger_activationcode where activationcodeid = ? ";
        $result = $adb->pquery($sql,array($recordid));
        if($adb->num_rows($result)){
            $TyunWebBuyService_Module_Model = TyunWebBuyService_Module_Model::getCleanInstance("TyunWebBuyService");
            while ($row=$adb->fetch_row($result)){
                if($row['signaturetype']=='eleccontract' && !$row['contractid'] && $TyunWebBuyService_Module_Model->exportGrouprt('TyunWebBuyService','manuallycreatecontracts')){
                    return true;
                }
            }
        }
        return false;
    }
    /**
     * 更新T云平台对应订单的合同编号
     */
    public function  updateOrderContractNo($userId,$payCode,$contractCode,$contractid=''){
        global $sault,$BindContractCode;
        $postData=json_encode(array(
                'userID'=>intval($userId),
                'payCode'=>$payCode,
                'contractCode'=>$contractCode)
        );
        $time=time().'123';
        $sault=$time.$sault;
        $token=md5($sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $this->_logs(array("BindContractCode request data:",$postData));
        $res = $this->https_request($BindContractCode, $postData,$curlset);
        $data = json_decode($res,true);
        $resultdata = array();
        if($data['success']){
            $sql = "update vtiger_activationcode set contractid=?,contractname=? where paycode=?";
            global $adb;
            $adb->pquery($sql,array($contractid,$contractCode,$payCode));
            $resultdata = $data;
        }
        return $resultdata;
    }

    /**
     * 发送提醒短信
     *
     * @param Vtiger_Request $request
     * @return mixed
     */
    function sendSMS(Vtiger_Request $request){
        global $tyunweburlsms,$sault;
        $tyunweburl1=$tyunweburlsms.'api/app/aggregateservice-api/v1.0.0/api/SMS/SendMobileMessage';
        $time=time().'123';
        $sault1=$time.$sault;
        $token=md5($sault1);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));

        $rebinddata=json_encode(array("mobile"=>$request->get('mobile'),"content"=>'【珍岛集团】'.$request->get('content')));
        $this->_logs(array('url'=>$tyunweburl1,'params'=>$rebinddata));
        $Repson=$this->https_request($tyunweburl1,$rebinddata,$curlset);
        $this->_logs(json_decode($Repson,true));
        return json_decode($Repson,true);
    }

        /**
     * 获取套餐和单品产品列表
     */
    public function allPackageAndProduct(){
        global $sault,$GetPackagePageData,$GetProductPageData,$adb;
        $postData=json_encode(array(
                'pageSize'=>intval(999999),
                'pageIndex'=>1,
        ));
        $time=time().'123';
        $sault=$time.$sault;
        $token=md5($sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $res = $this->https_request($GetPackagePageData, $postData,$curlset);
        $data = json_decode($res,true);
        $this->_logs(array($GetPackagePageData,$data));
        $products = array();
        if($data['success']){
          foreach ($data['data'] as $row){
              if($row['SellStatus']!=1){
                  continue;
              }
              $products[] = array(
                  'id'=>$row['ID'],
                  "productCode"=>'c'.$row["ID"],
                  'productName'=>$row['Title'],
                  'ispackage'=>1
              );
              $products[] = array(
                  'id'=>$row['ID'],
                  "productCode"=>'co'.$row["ID"],
                  'productName'=>$row['Title'].'(线上)',
                  'ispackage'=>1
              );
          }
        }

        $res2 = $this->https_request($GetProductPageData, $postData,$curlset);
        $data2 = json_decode($res2,true);
        $this->_logs(array($GetProductPageData,$data2));
        if($data2['success']){
            foreach ($data2['data'] as $row){
                if($row['SellStatus']!=1){
                    continue;
                }
                $products[] = array(
                    "id"=>$row['ID'],
                    "productCode"=>'s'.$row["ID"],
                    'productName'=>$row['Title'],
                    'ispackage'=>0
                );
                $products[] = array(
                    "id"=>$row['ID'],
                    "productCode"=>'so'.$row["ID"],
                    'productName'=>$row['Title'].'(线上)',
                    'ispackage'=>0
                );
            }
        }

        //数字威客的合同
        $result = $adb->pquery("select * from vtiger_wkproductcode ",array());
        if($adb->num_rows($result)){
            while ($row = $adb->fetchByAssoc($result)){
                $products[] = array(
                    'id'=>$row['id'],
                    "productCode"=>$row['wkproductcode'],
                    'productName'=>$row['productname'],
                    'ispackage'=>$row['ispackage']
                );
            }
        }
//        $wkProducts = array(
//            array(
//                'id'=>'001',
//                "productCode"=>'wk001',
//                'productName'=>'数字威客服务合同',
//                'ispackage'=>0
//            ),
//            array(
//                'id'=>'002',
//                "productCode"=>'wk002',
//                'productName'=>'数字威客服务合同-外部公司',
//                'ispackage'=>0
//            )
//        );
//        foreach ($wkProducts as $wkProduct){
//            $products[] = $wkProduct;
//        }

        return  array('products'=>$products);
    }
     /**
     * 根据TYUN账户去查客户ID和名称，按订单的下单时降序排取第一个返回客户名称和客户ID
     */
    public function tyunWebGetACIDByTyunUserCode($request){
        global $adb;
        $usercode=$request->get('usercode');
        $query='SELECT customerid FROM vtiger_activationcode WHERE usercode=? AND customerid>0 ORDER BY activationcodeid DESC LIMIT 1';
        $result=$adb->pquery($query,array($usercode));
        $res=array();
        if($adb->num_rows($result)){
            $sql = "SELECT
				vtiger_crmentity.label,
				vtiger_crmentity.crmid
			FROM
				vtiger_crmentity
			WHERE
				deleted = 0
			AND vtiger_crmentity.crmid=?
			LIMIT 1";
            $listResult = $adb->pquery($sql, array($result->fields['customerid']));
            $res=array();
            if($adb->num_rows($listResult)){
                $resultData=$adb->query_result_rowdata($listResult,0);
                $res=array("cid"=>$resultData['crmid'],'accountname'=>$resultData['label']);
            }
        }
        return $res;
    }

    public function elecContractStatusSendMail($data){
        $userid=$data['userid'];
        global $adb,$current_user;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
        $sql = "SELECT * FROM `vtiger_users` WHERE id=? LIMIT 1";
        $sel_result = $adb->pquery($sql, array($current_user->reports_to_id));
        $res_cnt = $adb->num_rows($sel_result);
        $agent_email = '';
        $agent_last_name = '';
        if($res_cnt > 0) {
            $row = $adb->query_result_rowdata($sel_result, 0);
            $agent_email = $row['email1'];
            $agent_last_name = $row['last_name'];
        }

        $email = $current_user->email1;
        $last_name = $current_user->last_name;
        $department = $current_user->department;

        $query='SELECT * FROM vtiger_departmentragentid WHERE FIND_IN_SET(?,userids) limit 1';
        $result=$adb->pquery($query,array($userid));
        if($adb->num_rows($result)){
            $row = $adb->query_result_rowdata($result, 0);
            $upmail=$row['email'];
        }else{
            $query = "SELECT parentdepartment FROM `vtiger_departments` WHERE departmentid = (SELECT departmentid FROM `vtiger_user2department` WHERE userid=? LIMIT 1)";
            $listResult = $adb->pquery($query, array($current_user->id));
            $res_cnt = $adb->num_rows($listResult);
            if($res_cnt > 0) {
                $row = $adb->query_result_rowdata($listResult, 0);
                $upmail='';
                $query='SELECT * FROM vtiger_departmentragentid';
                $result=$adb->pquery($query,array());
                while($rowdepart=$adb->fetch_array($result)){
                    if(false !== strpos($row['parentdepartment'], $rowdepart['pdepartmentid'])){
                        $upmail=$rowdepart['email'];
                        break;
                    }
                }
            }
        }

        $body2='';
        $body2 .= "<span style='font-weight:bold'>合同编号:</span>".$data['contract_no'].'<br>';
        $body2 .= "<span style='font-weight:bold'>客户:</span>".$data['customer_name'].'<br>';
        switch ($data['modulestatus']){
            case "c_complete":
                $Subject = "电子合同签署完成通知——".$data['contract_no'];
                $body = "<span style='font-weight: bold;'>客户已完成电子合同签署，请跟进后续工作</span><br><br>";
                $body2 .= "<span style='font-weight:bold'>签署时间:</span>".$data['signdate'].'<br>';
                break;
            case "c_cancel":
            case "a_normal":
                $Subject = "电子合同拒签通知——".$data['contract_no'];
                $body = "<span style='font-weight: bold;'>客户已拒签你的电子合同，拒签原因：".$data['elechandreason']."</span><br>
<span style='font-weight: bold'>如需再次发送， 可以新开合同或者编辑合同重新发起（T云合同不支持编辑），请知晓！</span><br><br>";
                $body2 .= "<span style='font-weight:bold'>拒签时间:</span>".$data['docanceltime'].'<br>';
                break;
            default:
                $body = "<span style='font-weight: bold;'>系统已发送电子合同给客户，请及时与客户确认并跟进客户完成合同签署！</span><br><br>";
                $Subject = "电子合同发送通知——".$data['contract_no'];
                $body2 .= "<span style='font-weight:bold'>发送时间:</span>".$data['receivedate'].'<br>';
                if($data['comeformtyun']){
                    if($data['fromactivity']){
                        $body .="<span style='font-weight: bold;color: orange;'>此份电子合同为“T云活动合同”，需要在活动结束时间前在ERP电脑端服务合同内完成确认到款，否则不可确认到款，会影响订单生效，请知晓！</span><br>";
                    }else if($data['servicecontractstype']=='upgrade'){
                        $body .="<span style='font-weight: bold;color: orange;'>此份电子合同为“T云升级合同”，需要在5个自然日内在ERP电脑端服务合同内完成确认到款，否则不可确认到款，会影响订单生效，请知晓！</span><br>";
                    }else{
                        $body .="<span style='font-weight: bold;color: orange;'>此份电子合同为“T云合同”，需要在30天内在ERP电脑端服务合同内完成确认到款，否则不可确认到款，会影响订单生效，请知晓！</span><br>";
                    }
                }
        }

        $body2 .= "<span style='font-weight:bold'>联系人:</span>".$data['elereceiver'].'<br>';
        $body2 .= "<span style='font-weight:bold'>联系人手机号:</span>".$data['elereceivermobile'].'<br>';
        $lastBody = $body.$body2;
        $upmail=!empty($upmail)?$upmail:$email;
        $address = array(
            array('mail'=>$upmail, 'name'=>''),//营总监
            array('mail'=>$email, 'name'=>$last_name),//负责人
            array('mail'=>$agent_email, 'name'=>$agent_last_name),//负责人上级
        );
        Vtiger_Record_Model::sendMail($Subject,$lastBody,$address);
    }
    public function elecContractStatusSendMail2(Vtiger_Request $request){
        $userid=$request->get('userid');
        global $adb,$current_user;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
        $sql = "SELECT * FROM `vtiger_users` WHERE id=? LIMIT 1";
        $sel_result = $adb->pquery($sql, array($current_user->reports_to_id));
        $res_cnt = $adb->num_rows($sel_result);
        $agent_email = '';
        $agent_last_name = '';
        if($res_cnt > 0) {
            $row = $adb->query_result_rowdata($sel_result, 0);
            $agent_email = $row['email1'];
            $agent_last_name = $row['last_name'];
        }

        $email = $current_user->email1;
        $last_name = $current_user->last_name;
        $department = $current_user->department;

        $query='SELECT * FROM vtiger_departmentragentid WHERE FIND_IN_SET(?,userids) limit 1';
        $result=$adb->pquery($query,array($userid));
        if($adb->num_rows($result)){
            $row = $adb->query_result_rowdata($result, 0);
            $upmail=$row['email'];
        }else{
            $query = "SELECT parentdepartment FROM `vtiger_departments` WHERE departmentid = (SELECT departmentid FROM `vtiger_user2department` WHERE userid=? LIMIT 1)";
            $listResult = $adb->pquery($query, array($current_user->id));
            $res_cnt = $adb->num_rows($listResult);
            if($res_cnt > 0) {
                $row = $adb->query_result_rowdata($listResult, 0);
                $upmail='';
                $query='SELECT * FROM vtiger_departmentragentid';
                $result=$adb->pquery($query,array());
                while($rowdepart=$adb->fetch_array($result)){
                    if(false !== strpos($row['parentdepartment'], $rowdepart['pdepartmentid'])){
                        $upmail=$rowdepart['email'];
                        break;
                    }
                }
            }
        }

        $contract_no = $request->get('contract_no');

        $body2='';
        $body2 .= "<span style='font-weight:bold'>合同编号:</span>".$contract_no.'<br>';
        $body2 .= "<span style='font-weight:bold'>客户:</span>".$request->get('customer_name').'<br>';
        switch ($request->get('modulestatus')){
            case "c_complete":
                $Subject = "电子合同签署完成通知——".$contract_no;
                $body = "<span style='font-weight: bold;'>客户已完成电子合同签署，请跟进后续工作</span><br><br>";
                $body2 .= "<span style='font-weight:bold'>签署时间:</span>".$request->get('signdate').'<br>';
                break;
            case "c_cancel":
                $Subject = "电子合同拒签通知——".$contract_no;
                $body = "<span style='font-weight: bold;'>客户已拒签你的电子合同，拒签原因：".$request->get('elechandreason')."</span><br>
<span style='font-weight: bold'>如需再次发送， 可以新开合同或者编辑合同重新发起（T云合同不支持编辑），请知晓！</span><br><br>";
                $body2 .= "<span style='font-weight:bold'>拒签时间:</span>".$request->get('docanceltime').'<br>';
                break;
            default:
                $body = "<span style='font-weight: bold;'>系统已发送电子合同给客户，请及时与客户确认并跟进客户完成合同签署！</span><br><br>";
                $Subject = "电子合同发送通知——".$contract_no;
                $body2 .= "<span style='font-weight:bold'>发送时间:</span>".$request->get('receivedate').'<br>';
                if($request->get('comeformtyun')){
                    if($request->get('fromactivity')){
                        $body .="<span style='font-weight: bold;color: orange;'>此份电子合同为“T云活动合同”，需要在活动结束时间前在ERP电脑端服务合同内完成确认到款，否则不可确认到款，会影响订单生效，请知晓！</span><br>";
                    }else if($request->get('servicecontractstype')=='upgrade'){
                        $body .="<span style='font-weight: bold;color: orange;'>此份电子合同为“T云升级合同”，需要在5个自然日内在ERP电脑端服务合同内完成确认到款，否则不可确认到款，会影响订单生效，请知晓！</span><br>";
                    }else{
                        $body .="<span style='font-weight: bold;color: orange;'>此份电子合同为“T云合同”，需要在30天内在ERP电脑端服务合同内完成确认到款，否则不可确认到款，会影响订单生效，请知晓！</span><br>";
                    }
                }
        }

        $body2 .= "<span style='font-weight:bold'>联系人:</span>".$request->get('elereceiver').'<br>';
        $body2 .= "<span style='font-weight:bold'>联系人手机号:</span>".$request->get('elereceivermobile').'<br>';
        $lastBody = $body.$body2;
        $upmail=!empty($upmail)?$upmail:$email;

	$this->_logs(array("elecContractStatusSendMail2",$upmail,$email,$agent_email));
        $address = array(
            array('mail'=>$upmail, 'name'=>''),//营总监
            array('mail'=>$email, 'name'=>$last_name),//负责人
            array('mail'=>$agent_email, 'name'=>$agent_last_name),//负责人上级
        );
        Vtiger_Record_Model::sendMail($Subject,$lastBody,$address);
    }

    /**
     * 获取分公司的部门邮箱
     * @param $userid
     * @throws Exception
     */
    public function getBranchEmail($userid){
        global $current_user,$adb;
        $query='SELECT * FROM vtiger_departmentragentid WHERE FIND_IN_SET(?,userids) limit 1';
        $result=$adb->pquery($query,array($userid));
        $upmail='';
        if($adb->num_rows($result)){
            $row = $adb->query_result_rowdata($result, 0);
            $upmail=$row['email'];
        }else{
            $query = "SELECT parentdepartment FROM `vtiger_departments` WHERE departmentid = (SELECT departmentid FROM `vtiger_user2department` WHERE userid=? LIMIT 1)";
            $listResult = $adb->pquery($query, array($current_user->id));
            $res_cnt = $adb->num_rows($listResult);
            if($res_cnt > 0) {
                $row = $adb->query_result_rowdata($listResult, 0);
                $parentdepartment=$row['parentdepartment'].'::';
                $upmail='';
                $query='SELECT * FROM vtiger_departmentragentid WHERE LENGTH(pdepartmentid)>0 ORDER BY LENGTH(pdepartmentid) DESC';
                $result=$adb->pquery($query,array());
                while($rowdepart=$adb->fetch_array($result)){
                    $pdepartmentid=$rowdepart['pdepartmentid'];
                    if(empty($pdepartmentid)){
                        continue;
                    }
                    $pdepartmentid=$rowdepart['pdepartmentid'].'::';
                    if(false !== strpos($parentdepartment, $pdepartmentid)){
                        $upmail=$rowdepart['email'];
                        break;
                    }
                }
            }
        }
        return $upmail;
    }

    /**
     * 退款
     * @param $contract_no
     * @param $user_id
     * @param $usercode
     * @param bool $is_update
     * @return bool|string
     */
    public function doOrderRefundByContractNo($contract_no,$user_id,$usercode,$refundMoney,$remark,$is_update=true){
        global $tyunweburl,$sault,$adb;
        $url=$tyunweburl.'api/micro/order-basic/v1.0.0/api/Order/ERPRefund';
        $time=time().'123';
        $sault1=$time.$sault;
        $token=md5($sault1);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $canceldata=json_encode(array("userId"=>$user_id,"contractCode"=>$contract_no,'refundMoney'=>$refundMoney,'remark'=>$remark));
        $this->_logs(array('doOrderRefundByContractNo:',$canceldata));
        $data=$this->https_request($url,$canceldata,$curlset);
        $this->_logs(array($url,$data));
        $jsonData=json_decode($data,true);
        if($jsonData['code']=='200'&& $is_update){
            $sql="UPDATE  vtiger_activationcode SET `status`=2,orderstatus='ordercancel',canceldatetime=?,isdisabled=1 WHERE contractname=?";
            $adb->pquery($sql,array(date('Y-m-d H:i:s'),$contract_no));
            //$sql=" UPDATE  vtiger_activationcode SET  isclientmigration=0 WHERE usercode=? AND comeformtyun=0 ";
            //$adb->pquery($sql,array($usercode));
        }
        return $jsonData;
    }

    public function AddOnlineOrder2($data){
        global $tyunweburl;
        $rddata=$data['rddata'];
        $creator=$data['userid'];
        $rowdata0=base64_decode($rddata);
        $rowdata = str_replace('&quot;','"',$rowdata0);
        $resultData=json_decode($rowdata,true);
        $return=array('success'=>0);
        $this->_logs(array("AddbuyOrderOnLineRabbitMQData",$resultData,'userid'=>$creator));
        do{
            $this->_logs(array("AddbuyOrderOnLineRabbitMQData2"));
            if(empty($resultData)){
                $this->_logs(array("结果为空"));
                break;
            }
            $order=$resultData['order'];
            $detail=$resultData['orderDetail'];
            $detailproducts = $resultData['detailProducts'];
            $ProductType=array(1=>'buy',2=>'upgrade',3=>'degrade',4=>'renew');
            $classtype=$ProductType[$detail['ProductType']];
            try{
                $db=PearDatabase::getInstance();
                $query = 'SELECT contractid,creator FROM `vtiger_activationcode` WHERE ordercode=?';
                $dataResult = $db->pquery($query, array($order['OrderCode']));
                $lastData = $db->query_result_rowdata($dataResult, 0);

            }catch (Exception $exception){
                $db=new PearDatabase();
                $db->connect();
                $query = 'SELECT contractid,creator FROM `vtiger_activationcode` WHERE ordercode=?';
                $dataResult = $db->pquery($query, array($order['OrderCode']));
                $lastData = $db->query_result_rowdata($dataResult, 0);
            }

            $db->pquery("INSERT INTO vtiger_activationcode_tyunres(contractno,classtype,tyunurl,crminput,tyunoutput,success,createdtime) VALUES(?,?,?,?,?,?,NOW())",
                array($order['ContractCode'],$classtype,'线上推送','',$rowdata0,1));

            if ($db->num_rows($dataResult) && $order['AgentSaleType']!=4) {
                $startdate = date("Y-m-d H:i:s",strtotime($detail['OpenDate']));//开始时间
                $expiredate = date("Y-m-d H:i:s",strtotime($detail['CloseDate']));//到期时间
                if($order['TradingStatus']==4){
                    $db->pquery('update vtiger_listenorder set deleted=1 where servicecontractsid=? ',$lastData['contractid']);
                    $sql = 'UPDATE `vtiger_activationcode` SET orderstatus=\'orderstop\' WHERE ordercode=?';
                    $db->pquery($sql, array( $order['OrderCode']));
                    $db->pquery("update vtiger_servicecontracts set modulestatus='c_stop' where servicecontractsid=?",array($lastData['contractid']));
                    $db->pquery('update vtiger_listenorder set deleted=1 where servicecontractsid=? ',$lastData['contractid']);
                    $return = array('success' => 1);
                    continue;
                }
                if($order['TradingStatus']==5){
//                    $db->pquery('update vtiger_listenorder set deleted=1 where servicecontractsid=? ',$lastData['contractid']);
                    $sql = 'UPDATE `vtiger_activationcode` SET orderstatus=\'orderbreak\' WHERE ordercode=?';
                    $db->pquery($sql, array( $order['OrderCode']));
//                    $db->pquery("update vtiger_servicecontracts set modulestatus='c_stop' where servicecontractsid=?",array($lastData['contractid']));
                    $return = array('success' => 1);
                    continue;
                }

                if(substr($detail['OpenDate'],0,4)>0){
                    $serviceContractModel = ServiceContracts_Record_Model::getCleanInstance("ServiceContracts");
                    $serviceContractModel->inListenOrder($lastData['contractid'],$startdate,0,$lastData['creator']);
                    $sql = 'UPDATE `vtiger_activationcode` SET activedate=?,startdate=?,expiredate=?,`status`=1,orderstatus=\'orderdoused\' WHERE ordercode=?';
                    $db->pquery($sql, array($startdate,$startdate, $expiredate, $order['OrderCode']));
                    $contractData = $db->pquery("select contractid,creator from vtiger_activationcode where ordercode = ?",array($order['OrderCode']));
                    if($db->num_rows($contractData)){
                        $receiverabledate = date("Y-m-d",strtotime($startdate));
                        $row = $db->fetchByAssoc($contractData,0);
                        $db->pquery("update vtiger_contracts_execution_detail set receiverabledate=?,executestatus='c_executed' where contractid=?",array($receiverabledate,$row['contractid']));
                    }
                }
                $return = array('success' => 1);
                continue;
            }
            if($db->num_rows($dataResult)){
                $return = array('success' => 1);
                continue;
            }

            //创建线下订单
            $inputdata['activationcodeid'] = $db->getUniqueID('vtiger_activationcode');
            $inputdata['activedate'] = $detail['OpenDate']?date("Y-m-d H:i:s",strtotime($detail['OpenDate'])):'';;//激活日期
            $inputdata['usercodeid'] = $order['UserID'];//用户ID
            $inputdata['usercode'] = $order['OperatorName'];//用户ID
            $inputdata['contractid'] = 0;//合同ID
            $inputdata['onoffline'] = 'line';//0线上,1线下
            if($order['Type']==1){
                $inputdata['onoffline'] = 'offline';//0线上,1线下
            }

            if($order['AgentSaleType']!=4){
                $tyunweburl1 = $tyunweburl . 'api/app/tcloud-account/v1.0.0/account/getAccountInfoById?accountId=' . $order['UserID'];
                $time = time() . '123';
                global $sault;
                $sault1 = $time . $sault;
                $token = md5($sault1);
                $curlset = array(CURLOPT_HTTPHEADER => array(
                    "Content-Type:application/json",
                    "S-Request-Token:" . $token,
                    "S-Request-Time:" . $time));
                $AccountRepson = $this->https_request($tyunweburl1, array(), $curlset);
                $AccountOBJ = json_decode($AccountRepson, true);
                $AccountOBJData = $AccountOBJ['data'];
                $this->_logs(array("AddbuyOrderOnLineRabbitMQData 非外贸订单客户信息",$AccountOBJData));
                if (!empty($AccountOBJData)) {
                    $inputdata['customerid'] = $AccountOBJData['cid'];//客户ID
                    $inputdata['customername'] = $AccountOBJData['customerName'];//客户名称
                    $inputdata['usercode'] = $AccountOBJData['loginName'];//用户名
                    $inputdata['mobile'] = $AccountOBJData['phoneNumber'];//电话号码
                    $inputdata['usercodeid'] = $AccountOBJData['id'];//用户名
                }
            }else{
                //代理商订单 则去查询是否有客户，无客户则创建客户
                $customerName = $order['CustomerName'];
                $accountRecordModel = Accounts_Record_Model::getCleanInstance("Accounts");
                $accountRecordModel->set('accountname', $customerName);
                $isExist = $accountRecordModel->checkDuplicate();
                if(!$isExist){
                    global $current_user;
                    $user = new Users();
                    $current_user = $user->retrieveCurrentUserInfoFromFile(6934);
                    $accountname=preg_replace('/^(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+|(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+$/u','',$customerName);
                    $_REQUEST['record']='';
                    $_REQUEST['accountname']=$accountname;
                    $accountRecordModel->set("accountname", $accountname);
                    $accountRecordModel->save();
                    $inputdata['customerid'] = $accountRecordModel->getId();//客户ID
                    $inputdata['customername'] = $accountname;//客户名称
                }else{
                    $accountInfo = $accountRecordModel->getAccountInfoByAccountName($customerName);
                    $inputdata['customerid'] = $accountInfo['accountid'];//客户ID
                    $inputdata['customername'] = $accountInfo['accountname'];//客户名称
                }

                //代理商客户获取合同id
                $contractNo = $order['ContractCode'];
                $serviceContractModel = ServiceContractsPrint_Record_Model::getCleanInstance("ServiceContracts");
                $request2 = new Vtiger_Request(array());
                $request2->set("contractno",$contractNo);
                $serviceContractData = $serviceContractModel->getContractByContractNo($request2);
                $inputdata['contractid'] = $serviceContractData['data']['servicecontractsid'];//合同ID
                $inputdata['onoffline'] = 'offline';//0线上,1线下
            }
            $inputdata['agentsaletype'] = $order['AgentSaleType'];//售卖类型
            $inputdata['agents'] = $order['AgentIdentity'];//代理商ID
            $inputdata['productlife'] = $detail['BuyTerm'];//年限
            $inputdata['classtype'] = $classtype;//类型buy:购买、upgrade:升级、renew:续费、againbuy:另购
            $inputdata['status'] = 1;//状态
            $inputdata['receivetime'] = $order['AddDate'];//开始时间
            $inputdata['createdtime'] = $order['AddDate'];//创建时间
            $inputdata['checkstatus'] = 0;
            $inputdata['pushstatus'] = 1;
            $inputdata['contractstatus'] = 0;
            $inputdata['customerstype']='commoncustomers';//客户类型
            $inputdata['paymentno'] = $order['OutTradeNo'];//付款流水号
            $inputdata['startdate'] = $detail['OpenDate'];//开始时间
            $inputdata['expiredate'] = $detail['CloseDate'];//到期时间
            $inputdata['ordercode'] = $order['OrderCode'];//订单号
            $inputdata['paycode'] = $order['PayCode'];//付款码
            $inputdata['comeformtyun'] = 1;//来源(0tyun产品,1Tyunweb产品)
            $inputdata['productclass'] = $detail['CategoryID'];//0国内版,1一带一路
            //如果是有合同编号的则插入到订单表中
            if($order['ContractCode']){
                $result2 = $db->pquery("select servicecontractsid,modulestatus from vtiger_servicecontracts a left join vtiger_crmentity b on a.servicecontractsid=b.crmid where contract_no = ? and b.deleted=0 limit 1",array($order['ContractCode']));
                if($db->num_rows($result2)){
                    $row = $db->fetchByAssoc($result2,0);
                    $inputdata['contractid'] = $row['servicecontractsid'];
                    if($row['modulestatus']=='c_complete'){
                        $inputdata['contractstatus'] = 1;
                    }
                }
                $inputdata['contractname'] = $order['ContractCode'];
            }
            $inputdata['orderstatus'] = 'orderdoused';//订单状态
            $inputdata['orderamount'] = $order['Money'];//订单金额(市场价 如果是活动是活动市场价)
            $inputdata['contractprice'] = $order['ContractMoney']?$order['ContractMoney']:$order['Money'];//支付金额
            $inputdata['contractamount'] = $order['ContractMoney']?$order['ContractMoney']:$order['Money']; //合同金额
            //$inputdata['buyserviceinfo'] = $detail['OrderDetail'];//订单产品信息
            $orderDetail = json_decode($detail['OrderDetail'], true);
            $inputdata['fromactivity']=0;
            if($this->isActiveOrder($detailproducts)){
                $inputdata['activityid'] = "";
                $inputdata['activitysubprice'] = $detailproducts[0]['ActivityID']?($order['OriginalMoney']-$order['ActiveMoney']):'';
                $inputdata['activemoney'] = $order['ActiveMoney'];
                $inputdata['activityname'] = $detailproducts[0]['ActivityTitle'];
                $inputdata['activitytype'] = $detailproducts[0]['ActivityType'];
                $inputdata['activityno'] = $detailproducts[0]['ActivityID'];
                $inputdata['marketprice'] = $order['Money'];
                $inputdata['fromactivity'] = 1;
            }

            $productname = '';
            if (!empty($orderDetail['package'])) {
                $inputdata['productid'] = $orderDetail['package']['ID'];//产品套餐
                $productname .= $orderDetail['package']['Title'] . '(1)';
            }
            if (!empty($orderDetail['products'])) {
                $renewproducts = array();
                $buyseparately = '';
                foreach ($orderDetail['products'] as $value) {
                    $productname .= ',' . $value['Product']['Title'] . "(" . $value['Count'] . ")";
                    $buyseparately .= $value['Product']['ID'] . ',';

                    $renewproducts[] = array("productID" => $value['Product']['ID'],
                        "productTitle" => $value['Product']['Title'],
                        "productCount" => $value['Count'],
                        "specificationId" => $value['Specification']['ID'],
                        "specificationTitle" => $value['Specification']['Title'],
                    );
                }
                $buyseparately = trim($buyseparately, ',');
                $inputdata['productnames'] = json_encode($renewproducts, JSON_UNESCAPED_UNICODE);//另购项
                $inputdata['buyseparately'] = $buyseparately;//另购产品的ID
            }
            if (!empty($orderDetail['renewpackage'])) {
                $productname .= $orderDetail['renewpackage']['Title'] . '(1)';
                $inputdata['productid'] = $orderDetail['renewpackage']['ID'];//产品套餐
            }
            if (!empty($orderDetail['renewproducts'])) {
                $renewproductst = array();
                $buyseparately = '';
                $renewproductsnum = array();
                $renewproductstempid = array();
                foreach ($orderDetail['renewproducts'] as $value) {
                    $id = $value['Product']['ID'];
                    if (!in_array("/DV" . $id . "VD/", $renewproductstempid)) {
                        $productname .= ',' . $value['Product']['Title'] . "(DV" . $id . "VD)";
                        $buyseparately .= $value['Product']['ID'] . ',';
                        $renewproductstempid[] = "/DV" . $id . "VD/";
                        $renewproductsnum["DV" . $id . "VD"] = 1;
                        $renewproductst[$id] = array("productID" => $value['Product']['ID'],
                            "productTitle" => $value['Product']['Title'],
                            "productCount" => 1,
                            "specificationId" => $value['UserProductID'],
                            "specificationTitle" => $value['Specification']['Title'],
                        );
                    } else {
                        $renewproductst[$id]["productCount"] += 1;
                        $renewproductst[$id]["specificationId"] .= ',' . $value['UserProductID'];
                        $renewproductst[$id]["specificationTitle"] .= ',' . $value['Specification']['Title'];
                        $renewproductsnum["DV" . $id . "VD"] += 1;
                    }
                }
                $renewproducts = array_values($renewproductst);
                $productname = preg_replace($renewproductstempid, $renewproductsnum, $productname);
                $buyseparately = trim($buyseparately, ',');
                $inputdata['productnames'] = json_encode($renewproducts, JSON_UNESCAPED_UNICODE);//另购项
                $inputdata['buyseparately'] = $buyseparately;//另购产品的ID
            }
            $inputdata['productname'] = trim($productname, ',');
            $query = "SELECT smownerid,serviceid FROM `vtiger_crmentity` LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_crmentity.crmid WHERE deleted=0 AND crmid=?";
            $result = $db->pquery($query, array($AccountOBJData['cid']));
            if ($db->num_rows($result)) {
                $resultData = $db->raw_query_result_rowdata($result, 0);
                if ($resultData['serviceid'] > 0) {
                    $inputdata['serviceid'] = $resultData['serviceid'];//创建人
                } else {
                    $inputdata['serviceid'] = $resultData['smownerid'];//创建人
                }
            }

            $inputdata['creator'] = $creator?$creator:6934;
            /**记录相关的购买产品信息start**/
            $detailOrderDetail = is_array($detail['OrderDetail'])?$detail['OrderDetail']:json_decode($detail['OrderDetail'],true);
            $detailproducts2 = array();
            if($detail['PackageID']){
                switch ($detail['ProductType']){
                    case 4:
                        $packagetitle = $detailOrderDetail['renewpackage']['Title'];
                        $products = $detailOrderDetail['renewpackageproducts'];
                        break;
                    default:
                        $packagetitle = $detailOrderDetail['package']['Title'];
                        $products = $detailOrderDetail['packageProducts'];
                        break;
                }
                foreach ($products as  $product){
                    $detailproducts2[] = array(
                        'buyTerm'=>intval($detail['BuyTerm']),
                        'packageTitle'=>$packagetitle,
                        'categoryID'=>$product['Product']['CategoryID'],
                        'productTitle'=>$product['Product']['Title']
                    );
                }
            }else{
                switch ($detail['ProductType']) {
                    case 4:
                        $products = $detailOrderDetail['renewproducts'];
                        break;
                    default:
                        $products = $detailOrderDetail['products'];
                        break;
                }
                foreach ($products as  $product){
                    $detailproducts2[] = array(
                        'buyTerm'=>intval($detail['BuyTerm']),
                        'packageTitle'=>'',
                        'categoryID'=>$product['Product']['CategoryID'],
                        'productTitle'=>$product['Product']['Title']
                    );
                }
            }
            if(!$inputdata['productid']){
                $inputdata['productid'] = 0;
            }

            $inputdata['detailproducts'] = json_encode($detailproducts2);
            if(!$order['ContractCode']) {
                $inputdata['signaturetype'] = 'eleccontract';
            }
            /**记录相关的购买产品信息end**/
            $this->_logs(array("AddbuyOrderOnLineinputdata",$inputdata));

            $sql = "INSERT INTO vtiger_activationcode(" . implode(',', array_keys($inputdata)) . ") values(" . generateQuestionMarks($inputdata) . ")";
            $db->pquery($sql, $inputdata);
            $return=array('success'=>1);
        }while(0);
        return $return;
    }



    public function AddbuyOrderOnLine2(Vtiger_Request $request){
        $data=array('module'=>'TyunWebBuyService',
            'action'=>'AddOnlineOrder2',
            'mqdata'=>array(
                'rddata'=>$request->get('rddata'),
                'userid'=>$request->get('userid')
            )
        );
        $rddata=$request->get('rddata');
        $rowdata=base64_decode($rddata);
        $rowdata = str_replace('&quot;','"',$rowdata);
        $resultData=json_decode($rowdata,true);
        sleep(1);
        $this->_logs(array("AddbuyOrderOnLineSuccess",$resultData));
        $jsonData=json_encode($data);
        $recordModel=new Vtiger_Record_Model();
        $flag  = $recordModel->rabbitMQPublisher($jsonData);
        return $flag;
    }
    /**
     * 获取前三后三期内的产品续费市场价，成本
     * @param $rp
     * @throws Exception
     */
    public function writeExpireRenewPriceAndCostPrice($rp){
        global $adb,$tyunweburl,$sault;
        $newusercode = $rp['usercode'];
        $sql = " SELECT activationcodeid,productclass,createdtime,customerid,productid,buyseparately,canrenew,productlife FROM vtiger_activationcode WHERE  contractid=?  AND status IN(0,1)";
        $result = $adb->pquery($sql, array($rp['contractid']));
        $currentproductids=array();
        $currentbuyseparately=array();
        while($row=$adb->fetch_array($result)){
            $productclass = $row['productclass'];
            $productlife = $row['productlife'];
            $customerid = $row['customerid'];
            $currentActivationcodeid = $row['activationcodeid'];
            $currentTime = $row['createdtime'];//下单时间
            if($row['productid']>0){
                $currentproductids[]=$row['productid'];
            }
            if($row['buyseparately']>0 && $row['canrenew']==1){
                $currentbuyseparately[]=$row['buyseparately'];
            }
        }

        $firstThreeMonth=date('Y-m-d 00:00:00',strtotime('-3 months'.$currentTime));
        $LastThreeMonth=date('Y-m-d 23:59:59',strtotime('3 months'.$currentTime));
        //查询当前账号之前下的最近的一单
        $sql = "SELECT productlife,expiredate,usercode FROM vtiger_activationcode  WHERE customerid=? AND contractid!=?  AND activationcodeid<?  AND productclass=? AND status IN(0,1)  AND expiredate>=? AND expiredate<=?";
        $result = $adb->pquery($sql, array($customerid, $rp['contractid'], $currentActivationcodeid, $productclass,$firstThreeMonth,$LastThreeMonth));
        $result = $adb->query_result_rowdata($result, 0);
        //如果该新单账户之前有过单子  老账户继续使用

        $productidsArray=array();
        $buyseparatelyArray=array();
        $productPrice=array(
            'marketprice'=>0,
            'costprice'=>0
        );
        if (!empty($result)) {//同账号前三后三段内判断
            /*if($rp['productid']>0) {//已经续过费了不用再扣减
                $querysql = "SELECT activationcodeid FROM vtiger_activationcode WHERE usercode=? AND contractid!=?  AND activationcodeid<?  AND productclass=? AND productid=? AND status IN(0,1) AND createdtime>=? AND createdtime<=?";
                $productResult = $adb->pquery($querysql, array($newusercode, $rp['contractid'], $currentActivationcodeid, $productclass,$rp['productid'], $firstThreeMonth, $LastThreeMonth));
                if($adb->num_rows($productResult)) {
                    return ;
                }
            }*/
            $querysql = "SELECT productlife,expiredate,usercode,productid,buyseparately,productnames,canrenew FROM vtiger_activationcode  WHERE customerid=? AND contractid!=?  AND activationcodeid<?  AND productclass=? AND status IN(0,1) AND expiredate>=? AND expiredate<=?";
            $productResult=$adb->pquery($querysql,array($customerid, $rp['contractid'], $currentActivationcodeid, $productclass,$firstThreeMonth,$LastThreeMonth));
            while($row=$adb->fetch_array($productResult)){
                if($row['productid']>0){
                    $productidsArray[]= $row['productid'];
                }elseif($row['buyseparately']>0 && $row['canrenew']==1){
                    $jsonData= $row['productnames'];
                    $jsonData=html_entity_decode($jsonData);
                    $productnames=json_decode($jsonData,true);
                    $buyseparatelyArray[]= array('id'=>$row['buyseparately'],'spid'=>$productnames[0]['specificationId']);
                }
            }
            $querysql = "SELECT marketprice,costprice FROM vtiger_activationcode  WHERE customerid=? AND contractid!=?  AND activationcodeid<?  AND productclass=? AND status IN(0,1) AND createdtime>=? AND createdtime<=?";
            $productResult=$adb->pquery($querysql,array($customerid, $rp['contractid'], $currentActivationcodeid, $productclass,$firstThreeMonth,$LastThreeMonth));
            if($adb->num_rows($productResult)){
                while ($row = $adb->fetch_array($productResult)) {
                    $productPrice['marketprice']+=$row['marketprice'];
                    $productPrice['costprice']+=$row['costprice'];
                }
            }
        }else{//前三前，后三后判断不用管你新老账号
            return ;
            //查询是否存在老客户订单
            $sql = " SELECT productlife,expiredate,renewmarketprice FROM vtiger_activationcode  WHERE  customerid=?  AND productclass=?  AND usercode!=?  AND activationcodeid<?  AND contractid!=? AND status IN(0,1)  AND expiredate>=? AND expiredate<=?";
            $result = $adb->pquery($sql, array($customerid,$productclass, $rp['usercode'], $currentActivationcodeid, $rp['contractid'], $firstThreeMonth, $LastThreeMonth));
            //$result = $adb->query_result_rowdata($result, 0);
            // 老客户不再继续使用的情况下 即用了新账户开了单子
            if ($adb->num_rows($result)) {
                /*if($rp['productid']>0){
                    $querysql = "SELECT 1 FROM vtiger_activationcode  WHERE customerid=? AND contractid!=?  AND activationcodeid<?  AND productclass=? AND productid=? AND status IN(0,1) AND createdtime>=? AND createdtime<=?";
                    $productResult=$adb->pquery($querysql,array($rp['customerid'], $rp['contractid'], $currentActivationcodeid, $productclass,$rp['productid'],$firstThreeMonth,$LastThreeMonth));
                    if($adb->num_rows($productResult)){//已经续费过了就不用再看他
                        return ;
                    }
                }*/
                $querysql = "SELECT productlife,expiredate,usercode,productid,buyseparately,productnames,canrenew FROM vtiger_activationcode  WHERE customerid=? AND contractid!=?  AND activationcodeid<?  AND productclass=? AND status IN(0,1) AND expiredate>=? AND expiredate<=?";
                $productResult=$adb->pquery($querysql,array($customerid, $rp['contractid'], $currentActivationcodeid, $productclass,$firstThreeMonth,$LastThreeMonth));
                while($row=$adb->fetch_array($productResult)){
                    if($row['productid']>0){
                        $productidsArray[]= $row['productid'];
                    }elseif($row['buyseparately']>0 && $row['canrenew']==1){
                        $jsonData= $row['productnames'];
                        $jsonData=html_entity_decode($jsonData);
                        $productnames=json_decode($jsonData,true);
                        $buyseparatelyArray[]= array('id'=>$row['buyseparately'],'spid'=>$productnames[0]['specificationId']);
                    }
                }
                $querysql = "SELECT marketprice,costprice FROM vtiger_activationcode  WHERE customerid=? AND contractid!=?  AND activationcodeid<?  AND productclass=? AND status IN(0,1) AND createdtime>=? AND createdtime<=?";
                $productResult=$adb->pquery($querysql,array($customerid, $rp['contractid'], $currentActivationcodeid, $productclass,$firstThreeMonth,$LastThreeMonth));
                if($adb->num_rows($productResult)){
                    while ($row = $adb->fetch_array($productResult)) {
                        $productPrice['marketprice']+=$row['marketprice'];
                        $productPrice['costprice']+=$row['costprice'];
                    }
                }

            }else{
                return;
            }
        }
        $noseparaterenewmarketprice=0;
        $noseparaterenewcosttprice=0;
        if(!empty($productidsArray)){
            $url=$tyunweburl.'api/micro/order-basic/v1.0.0/api/Package/GetModel';
            foreach($productidsArray as $value){
                $time=time().'123';
                $sault1=$time.$sault;
                $token=md5($sault1);
                $curlset=array(CURLOPT_HTTPHEADER=>array(
                    "Content-Type:application/json",
                    "S-Request-Token:".$token,
                    "S-Request-Time:".$time));
                $canceldata=json_encode(array("ID"=>$value));
                $data=$this->https_request($url,$canceldata,$curlset);
                $jsonData=json_decode($data,true);
                if($jsonData['code']=='200'){
                    $noseparaterenewmarketprice+=$jsonData['data']['model']['DirectRenewPrice']*$productlife;
                    $noseparaterenewcosttprice+=$jsonData['data']['model']['CostRenewPrice']*$productlife;
                }
            }

        }
        if(!empty($buyseparatelyArray)){
            $url=$tyunweburl.'api/micro/order-basic/v1.0.0/api/Product/GetModel';
            foreach($buyseparatelyArray as $value){
                $time=time().'123';
                $sault1=$time.$sault;
                $token=md5($sault1);
                $curlset=array(CURLOPT_HTTPHEADER=>array(
                    "Content-Type:application/json",
                    "S-Request-Token:".$token,
                    "S-Request-Time:".$time));
                $canceldata=json_encode(array("ID"=>$value['id']));
                $data=$this->https_request($url,$canceldata,$curlset);
                $jsonData=json_decode($data,true);
                if($jsonData['code']=='200'){
                    $specificationList=$jsonData['data']['specificationList'];
                    foreach($specificationList as $valuekey){
                        if($value['spid']==$valuekey['ID']){
                            $noseparaterenewmarketprice+=$valuekey['DirectRenewPrice']*$productlife;
                            $noseparaterenewcosttprice+=$valuekey['CostRenewPrice']*$productlife;
                        }
                    }
                }
            }

        }
        $sql='UPDATE vtiger_activationcode SET noseparaterenewmarketprice=?,noseparaterenewcosttprice=?,separaterenewmarketprice=?,separaterenewcosttprice=? WHERE contractid=? AND `status` IN (0,1)';
        $adb->pquery($sql,array($noseparaterenewmarketprice,$noseparaterenewcosttprice,$productPrice['marketprice'],$productPrice['costprice'],$rp['contractid']));
    }

    /**
     * 移动端创建订单
     * 移动端购买、续费、升级、降级T云产品，下单之后生成和T云相订单对应的订单并更新合同信息
     * @param Vtiger_Request $request
     * @date 2021-1-8
     */
    public function appAddOrder(Vtiger_Request $request)
    {
        $tempdata = $request->get('tempdata');
        $tyunparams = $request->get('tyunparams');
        $request_params = $request->get('request_params');
        $type = $request->get('type');
        $is_first_order = $this->isFirstOrderByUserCode($request_params['usercode']);
        //记录上一份合同使用天数和合同id
        $this->recordLastServiceContractInfo($request_params['usercode'],$request_params['servicecontractsid']);
        foreach ($tempdata['data'] as $temp) {
            $order_detail = $temp['detail']['OrderDetail'];
            // cxh  2020-07-27 start
            $order_detailProducts=$temp['detailProducts'][0];
            $firstPurchaseData = $this->getFirstPurchase($temp['order']['OrderCode']);
            $firstPurchase = !empty($firstPurchaseData)?$firstPurchaseData:$temp['firstPurchase'];
            $result=$this->getMarketPriceBuyAndRenew($order_detailProducts,$firstPurchase,$temp['order'],$temp['detail'],$type);
            list($productname, $buyseparately, $productnames,$packageid) = $this->handleProductName($order_detail, $temp['detail']['ProductType'],$temp['detailProducts'][0]['ProductTypeTitle']);
            $params = array(
                'payCode' => $tempdata['payCode'],
                'totalPrice' => $tempdata['totalPrice'],
                'productname' => $productname,
                'buyseparately' => $buyseparately,
                'productnames' => $productnames,
                'oneMarketPrice'=>$result['oneMarketPrice'],
                'oneMarketRenewPrice'=>$result['oneMarketRenewPrice'],
                'oneCostPrice'=>$result['oneCostPrice'],
                'oneCostRenewPrice'=>$result['oneCostRenewPrice'],
                'CanRenew'=>$result['CanRenew'],// 没有用了 应为直接返回首购市场价格了。
                'actualMarketPrice'=>$result['actualMarketPrice'],
                'actualCostPrice'=>$result['actualCostPrice'],
                'isdirectsellingtoprice'=>$result['isdirectsellingtoprice'],
                'IsGifts'=>$result['IsGift']
            );
            if($request->get('is_clientmigrate')){
                $params['packageID'] = $packageid;
            }
            $params=array_merge($temp,$params);
            if($tyunparams){
                $params=array_merge($params,$tyunparams);
            }

            $res[] = $this->appAddOrderByData(array_merge($request_params,$params),$type);
        }
        //获取上一份合同的编号和使用
        $this->sendNotice($res,$type,$request_params['signaturetype'],$tyunparams['contractMoney'],$is_first_order,$request_params['servicecontractsid']);

        // 如果是院校版则不生成电子合同 cxh 添加院校版不生成电子合同。
        $serviceRecordModel = ServiceContracts_Record_Model::getInstanceById($request_params['servicecontractsid'],"ServiceContracts");
        if($request_params['iscollegeedition']!=1){
            //电子合同时生成合同
            if($request_params['signaturetype']=='eleccontract'){
                $request->set('paycode',$tempdata['payCode']);
                $request->set('accountid',$request_params['accountid']);
                $request->set('servicecontractsid',$request_params['servicecontractsid']);
                $serviceRecordModel->createSalesorderproductsrel($request);
            }
        }

        $classType = array(
            "buy"=>'新增',
            "upgrade"=>'upgrade',
            "renew"=>'续费',
            "degrade"=>'degrade',
        );
        //记录合同的类型
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select b.smownerid from vtiger_account a left join vtiger_crmentity b on a.accountid=b.crmid where a.accountid=?",array($request_params['accountid']));
        $row = $db->fetchByAssoc($result,0);
//        $db->pquery("update vtiger_servicecontracts set servicecontractstype=?,signid=?,receiveid=?,categoryid=?,issendsms=0 where servicecontractsid=?",array($classType[$request_params['classtype']],$row['smownerid'],$row['smownerid'],$request_params['categoryid'],$request_params['servicecontractsid']));
        if($request_params['couponName']){
            $remark=$serviceRecordModel->get("remark")." 券码:" . $request_params['couponCode'] . ' 券码用户名:' . $request_params['couponName'];
            $db->pquery("update vtiger_servicecontracts set servicecontractstype=?,signid=?,receiveid=?,total=?,categoryid=?,issendsms=0,remark=? where servicecontractsid=?",array($classType[$request_params['classtype']],$row['smownerid'],$row['smownerid'],$request_params['contractprice'],$request_params['categoryid'],$remark,$request_params['servicecontractsid']));

        }else{
            $db->pquery("update vtiger_servicecontracts set servicecontractstype=?,signid=?,receiveid=?,total=?,categoryid=?,issendsms=0 where servicecontractsid=?",array($classType[$request_params['classtype']],$row['smownerid'],$row['smownerid'],$request_params['contractprice'],$request_params['categoryid'],$request_params['servicecontractsid']));
        }

        $serviceRecordModel->serviceContractDivide($request_params['servicecontractsid'],$row['smownerid'],$request_params['accountid']);
        //查找已匹配到合同上的回款，确认到款到T云
        $this->orderToConfirm($request_params['servicecontractsid'],$request_params['classtype'],$request_params['contractprice']);
        return $res;
    }

    /**
     * 生成订单
     * @param $data
     * @param $type 购买类型
     * @return array
     * @throws Exception
     */
    public function appAddOrderByData($data, $type)
    {
        $db=PearDatabase::getInstance();
        $order=$data['order'];
        $detail=$data['detail'];
        $detailproducts = $data['detailProducts'];
        $inputdata=array();
        $inputdata['activationcodeid']=$db->getUniqueID('vtiger_activationcode');
        $inputdata['activedate']=$detail['OpenDate']?date("Y-m-d H:i:s",strtotime($detail['OpenDate'])):'';//激活日期
        $inputdata['contractid']=$data['servicecontractsid'];//合同ID
        $inputdata['contractname']=$data['servicecontractsid_display'];//合同编号
        $inputdata['customerid']=$data['accountid'];//客户ID
        $inputdata['customername']=$data['accountid_display'];//客户名称
        $inputdata['agents']=$order['AgentIdentity'];//代理商ID
        $inputdata['productlife']=$detail['BuyTerm'];//年限
        $inputdata['productid']=$detail['PackageID'];//产品套餐
        $inputdata['mobile']=$data['mobile'];//手机号
        $inputdata['salesname']=$data['customer_name'];//商务名称
        $inputdata['salesphone']=$data['phone_mobile'];//商务手机号
        $inputdata['customerstype']=$data['customerstype'];//客户类型(普通,迁移)
        // cxh 2020-07-28 start
        $inputdata['onemarketprice']=$data['oneMarketPrice'];
        $inputdata['onemarketrenewprice']=$data['oneMarketRenewPrice'];
        $inputdata['onecostprice']=$data['oneCostPrice'];
        $inputdata['onecostrenewprice']=$data['oneCostRenewPrice'];
        $inputdata['canrenew']=$data['CanRenew'];
        $inputdata['isdirectsellingtoprice']=$data['isdirectsellingtoprice'];
        // cxh 2020-07-28 end
        $status=$data['classtype']=='buy'?0:1;
        $orderstatus=$data['classtype']=='buy'?'ordernotused':'orderdoused';
        if($data['signaturetype']=='eleccontract'){
            $orderstatus='ordernotused';
        }
        $isclientmigration=0;
        $OrderDetailToArray=json_decode($detail['OrderDetail'],true);
        switch ($detail['ProductType']){
            case 1:
                $classtype = 'buy';
                $inputdata['renewmarketprice'] =$OrderDetailToArray['package']['DirectRenewPrice'];
                $inputdata['renewcostprice'] =$OrderDetailToArray['package']['CostRenewPrice'];
                break;
            case 2:
            case 5:
                $classtype = 'upgrade';
                $inputdata['isupgrade'] = 1;
                $inputdata['renewmarketprice'] =$OrderDetailToArray['package']['DirectRenewPrice'];
                $inputdata['renewcostprice'] =$OrderDetailToArray['package']['CostRenewPrice'];
                break;
            case 3:
            case 6:
                $classtype = 'degrade';
                $inputdata['renewmarketprice'] =$OrderDetailToArray['package']['DirectRenewPrice'];
                $inputdata['renewcostprice'] =$OrderDetailToArray['package']['CostRenewPrice'];
                break;
            case 4:
            case 7:
                $classtype = 'renew';
                $inputdata['renewmarketprice'] =$OrderDetailToArray['renewpackage']['DirectRenewPrice'];
                $inputdata['renewcostprice'] =$OrderDetailToArray['renewpackage']['CostRenewPrice'];
                break;
        }

        //集团版和院校版的数据
        switch ($detail['CategoryID']){
            case 7:
                $iscollegeedition=1;
                break;
            case 9:
                $iscollegeedition=2;
                break;
            default:
                $iscollegeedition=0;
        }

        $inputdata['iscollegeedition']=$iscollegeedition;// 是院校版标明
        $inputdata['packageamount'] = 0;
        if($inputdata['productid']){
            $inputdata['packageamount'] = $detail['Count'];
        }

        if($inputdata['customerstype']=='clientmigration'){
            $surplusmoney=$data['surplusmoney'];
            $inputdata['surplusmoney']=0;//预付款
            $inputdata['upgradecontractprice']=0;//升级后的合同金额
            $inputdata['upgradetransfer']=$surplusmoney;//升级转款=预付款
            $isclientmigration=1;//是否做迁移升级,续费降级
            $inputdata['oldproductid']=$data['oldproductid'];//原产品ID
            if($type !='buy'){
                $inputdata['orderordercode']=$data['orderordercode'];//原产品订单号即激活码
                $inputdata['oldproductname']=$data['oldproductname'];//原产品名称
            }
            $inputdata['classtype']='c'.($type?$type:$classtype);//类型buy:购买、upgrade:升级、renew:续费、againbuy:另购
        }else{
            if(!in_array($classtype ,array('buy','renew'))){
                $oldorderordercode = $this->getLastOrdercode($data['usercode']);
                $inputdata['orderordercode']=$oldorderordercode;//原产品订单号即激活码
                $inputdata['oldproductname']=$data['oldproductname'];//原产品名称
            }
            $inputdata['customerstype']='commoncustomers';
            $inputdata['classtype']=$classtype;//类型buy:购买、upgrade:升级、renew:续费、againbuy:另购
            if($classtype=='upgrade'){
                $surplusmoney  = $order['SurplusMoney'];
                $inputdata['upgradetransfer'] =$surplusmoney;
            }
        }
        if(substr($detail['OpenDate'],0,4)>0){
            $orderstatus='orderdoused';
            $status=1;
        }
        $usercode=$data['usercode'];
        $inputdata['status']=$status;//状态

        $inputdata['usercode']=$usercode;//T云账户
        $inputdata['receivetime']=$order['AddDate'];//开始时间
        $inputdata['createdtime']=$data['addDate']?$data['addDate']:$detail['AddDate'];//创建时间
        $inputdata['couponcode']=$data['couponCode'];//优惠券编码
        $inputdata['couponname']=$data['couponName'];//优惠券用户名
        $inputdata['checkstatus']=0;
        $inputdata['pushstatus']=0;

        //已签收的合同 订单标注已签收
        $contractRecordModel=ServiceContracts_Record_Model::getInstanceById($data['servicecontractsid'],'ServiceContracts');
        $inputdata['contractstatus']=0;
        if($contractRecordModel->get("modulestatus")=='c_complete'){
            $inputdata['contractstatus']=1;
        }
        $inputdata['startdate']=$detail['OpenDate'];//开始时间
        $inputdata['expiredate']=$detail['CloseDate'];//到期时间
        $inputdata['creator']=$data['userid'];//创建人
        $inputdata['ordercode']=$order['OrderCode'];//订单号
        $inputdata['productname']=$data['productname'];//产品名称
        $inputdata['paycode']=$order['PayCode'];//付款码
        $inputdata['buyseparately']=$data['buyseparately'];//另购产品的ID

        $inputdata['comeformtyun']=1;//来源(0tyun产品,1Tyunweb产品)
        $inputdata['onoffline']='offline';//0线上,1线下
        $productnames=$data['productnames'];
        $inputdata['productnames']=json_encode($productnames,JSON_UNESCAPED_UNICODE);//产品名称数据
        $inputdata['orderstatus']=$orderstatus;//订单
        //状态ordernotused
        $inputdata['paymentno']=$order['OutTradeNo'];//付款流水号
        $inputdata['orderamount'] = $order['Money'];//订单金额(市场价 如果是活动是活动市场价)
        $inputdata['contractprice'] = $order['ContractMoney'];//支付金额
        $inputdata['contractamount'] = $order['ContractMoney'] +$surplusmoney; //合同金额
        $inputdata['usercodeid']=$data['usercodeid'];//用户id
        $inputdata['oldcustomerid']=$data['oldcustomerid'];
        $inputdata['oldcustomername'] = $data['oldcustomername'];
        $inputdata['agentsaletype'] = $order['AgentSaleType'];//售卖类型
        $inputdata['halfprice'] = $order['halfPrice'];
        $inputdata['paymentstatus'] = $order['PaymentStatus'];
        $inputdata['firstpaymoney'] = $order['FirstPayMoney'];
        // cxh 2020-08-26
        if(!empty($data['actualCostPrice'])){
            $inputdata['costprice'] = $data['actualCostPrice'];
        }else{
            $inputdata['costprice'] = $order['CostPrice'];
        }
        $inputdata['tyun_costprice'] = $order['CostPrice'];

        // 如果是赠送产品市场价格为0
        if($data['IsGifts']){
            $inputdata['marketprice']=0;
        }
        $inputdata['tyun_marketprice'] = $order['OriginalMoney'];
        $inputdata['marketprice'] = $order['OriginalMoney'];
        //是否来自活动，CRM管理后台签收合同时，判断年限会用到此字段
        $inputdata['fromactivity'] = 0;
        //算业绩用(市场价 如果是活动是活动市场价)
        if($detailproducts[0] && !empty($detailproducts[0]['ActivityID'])) {
            //T云下单接口返回的ActivityID是活动编号，不是活动ID
            $activityNO = $detailproducts[0]['ActivityID'];
            $inputdata['activityno'] = $activityNO;
            $inputdata['activitysubprice'] = '';
            if (isset($data['activitylist'][$activityNO])) {
//                $inputdata['activityid'] = $data['activitylist'][$activityNO]['activityID'];
//                $inputdata['activityname'] = $data['activitylist'][$activityNO]['activityTitle'];
                $inputdata['activitytype'] = $data['activitylist'][$activityNO]['activityTypeTitle'];
                $inputdata['activitysubprice'] = $order['OriginalMoney']-$order['ActiveMoney'];
            }
            $inputdata['activityid']=$detailproducts[0]['ActivityID'];
            $activityTitle = $detailproducts[0]['ActivityTitle'];
            if($detailproducts[0]['ActivityType']==3){
                $childIds = explode("-",$detailproducts[0]['ActivityChildID']);
                $activityTitle = $detailproducts[0]['ActivityTitle'].' 买'.$childIds[0].'年送'.$childIds[1].'年';
            }
            $inputdata['activityname']=$activityTitle;

            $inputdata['activemoney'] = $order['ActiveMoney'];
            $inputdata['marketprice'] = $order['MaketMoney'];
            $inputdata['giveterm'] = $detailproducts[0]['GiveTerm'];
            $inputdata['fromactivity'] = 1;
        }
        // cxh 2020-08-26
        if(!empty($data['actualMarketPrice'])){
            $inputdata['marketprice'] = $data['actualMarketPrice'];
        }
        // 如果是赠送产品市场价格为0
        if($data['IsGifts']){
            $inputdata['marketprice']=0;
        }
        /* $inputdata['fromactivity']=0;
            if($data['activityid']){
            $inputdata['fromactivity'] = 1;
        }*/

        $inputdata['productclass'] = $detail['CategoryID'];//0国内版,1一带一路

        /**记录相关的购买产品信息start**/
        $detailOrderDetail = is_array($detail['OrderDetail'])?$detail['OrderDetail']:json_decode($detail['OrderDetail'],true);
        $detailproducts2 = array();
        if($detail['PackageID']){
            switch ($detail['ProductType']){
                case 4:
                    $packagetitle = $detailOrderDetail['renewpackage']['Title'];
                    $products = $detailOrderDetail['renewpackageproducts'];
                    break;
                default:
                    $packagetitle = $detailOrderDetail['package']['Title'];
                    $products = $detailOrderDetail['packageProducts'];
                    break;
            }
            foreach ($products as  $product){
                $detailproducts2[] = array(
                    'buyTerm'=>intval($detail['BuyTerm']),
                    'packageTitle'=>$packagetitle,
                    'categoryID'=>$product['Product']['CategoryID'],
                    'productTitle'=>$product['Product']['Title']
                );
            }
        }else{
            switch ($detail['ProductType']) {
                case 4:
                    $products = $detailOrderDetail['renewproducts'];
                    break;
                default:
                    $products = $detailOrderDetail['products'];
                    break;
            }
            foreach ($products as  $product){
                $detailproducts2[] = array(
                    'buyTerm'=>intval($detail['BuyTerm']),
                    'packageTitle'=>'',
                    'categoryID'=>$product['Product']['CategoryID'],
                    'productTitle'=>$product['Product']['Title']
                );
            }
        }

        $inputdata['detailproducts'] = json_encode($detailproducts2);
        /**记录相关的购买产品信息end**/
        $inputdata['signaturetype'] = $data['signaturetype']?$data['signaturetype']:'papercontract';
        $inputdata['elereceivermobile'] = $data['elereceivermobile'];
        $inputdata['owncompany'] = $data['owncompany'];
        $inputdata['owncompanyid'] = $data['owncompanyid'];
        $sql="INSERT INTO vtiger_activationcode(".implode(',',array_keys($inputdata)).") values(".generateQuestionMarks($inputdata).")";
        $db->pquery($sql,$inputdata);
        $db->pquery("INSERT INTO vtiger_activationcode_tyunres(contractno,classtype,tyunurl,crminput,tyunoutput,success,createdtime)VALUES(?,?,?,?,?,?,NOW())",
            array($inputdata['contractname'],$inputdata['classtype'],$data["tyunurl"],'',json_encode($data['res']),1));
        $flag=1;
        if(1==$isclientmigration){
            $sql='UPDATE vtiger_activationcode SET isclientmigration=1 WHERE usercode=? AND comeformtyun=0';
            $db->pquery($sql,array($usercode));
            $classtype = $inputdata['classtype'];
            $flag=2;
        }

        global $configcontracttypeName, $configcontracttypeNameYUN,$configcontracttypeNameJT;
        //修改合同的contract_type和bussinesstype
        switch ($data['iscollegeedition']){
            case 1:
                $contract_type = $configcontracttypeNameYUN;
                $bussinesstype = 'bigsass';
                break;
            case 2:
                $contract_type = $configcontracttypeNameJT;
                $bussinesstype = 'bigsass';
                break;
            default:
                $contract_type = $configcontracttypeName;
                $bussinesstype = 'smallsassdirect';
                break;
        }

        $isstage = $data['isstage']?$data['isstage']:0;
        if($inputdata['fromactivity']&&$inputdata['signaturetype']=='eleccontract'){
            $db->pquery("update vtiger_servicecontracts set contract_type=?,bussinesstype=?,parent_contracttypeid=2,isstage=?,sc_related_to=?,isjoinactivity=1 where servicecontractsid=?",array($contract_type,$bussinesstype,$isstage,$data['accountid'],$data['servicecontractsid']));
        }else{
            $db->pquery("update vtiger_servicecontracts set contract_type=?,bussinesstype=?,parent_contracttypeid=2,isstage=?,sc_related_to=? where servicecontractsid=?",array($contract_type,$bussinesstype,$isstage,$data['accountid'],$data['servicecontractsid']));
        }
//        $db->pquery("update vtiger_servicecontracts set contract_type=?,bussinesstype=?,parent_contracttypeid=2,isstage=? where servicecontractsid=?",array($contract_type,$bussinesstype,$isstage,$data['servicecontractsid']));
        $sms_data = array(
            "usercode"=>$usercode,
            "productname"=>$inputdata['productname'],
            "customername"=>$inputdata['customername'],
            "mobile"=>$inputdata['mobile'],
            "flag"=>$flag,
        );

        $email_data = array(
            'userid'=>$data['userid'],
            'accountid_display'=>$data['accountid_display'],
            'servicecontractsid_display'=>$data['servicecontractsid_display'],
            'productname'=>$data['productname'],
            'buyTerm'=>$detail['BuyTerm']
        );
        $canSend =false;
        if($detail['OpenDate']){
            $canSend =true;
        }
        $this->writeExpireRenewPriceAndCostPrice(array('usercode'=>$usercode,'contractid'=>$data['servicecontractsid'],'productid'=>$inputdata['productid']));
        return array('success'=>1,'sms_data'=>$sms_data,'email_data'=>$email_data,'canSend'=>$canSend);
    }

    /**
     * 根据客户名称批量获取客户ID
     */
    public function getIDsByAccountNames($request) {
        global $adb;
        $accountNames = $request->get('accountNames');
        $regularRule = '/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;|&quot;|　|&apos;|&amp;|&lt;|&gt;|&#039;|&ldquo;|&rdquo;|&lsquo;|&rsquo;|&hellip;|\“|\”|\‘|\〉|\〈|\’|\〖|\〗|\【|\】|\、|\·|\…|\——|\＋|\－|\＝|\,|\<|\.|\>|\/|\?|\;|\:|\\\'|\"|\[|\{|\]|\}|\\|\||\`|\~|\!|\@|\#|\$|\%|\^|\\&|\*|\(|\)|\-|\_|\=|\+|\，|\＜|\．|\＞|\／|\？|\；|\：|\＇|\＂|\［|\{|\］|\}|\＼|\｜|\｀|\￣|\！|\＠|\#|\＄|\％|\＾|\＆|\＊|\（|\）|\－|\＿|\＝|\＋|\，|\《|\．|\》|\、|\？|\；|\：|\’|\”|\【|\｛|\】|\｝|\＼|\｜|\·|\～|\！|\＠|\＃|\￥|\％|\…|\＆|\×|\（|\）|\－|\——|\—|\＝|\＋|\，|\《|\。|\》|\、|\？|\；|\：|\‘|\“|\【|\｛|\】|\｝|\、|\||\·|\~|\！|\@|\#|\￥|\%|\&|\*|\（|\）|\-|\——|\=|\+/u';
        $labelNames = [];
        $namesList = [];
        foreach ($accountNames as $v) {
            $labelName = preg_replace($regularRule,'', $v);
            if ($labelName != '') {
                $labelName = strtoupper($labelName);
                $labelNames[] = $labelName;
                $namesList[md5($labelName)] = $v;
            }
        }
        if (empty($labelNames)) {
            return [];
        }
        $sql = "SELECT vtiger_crmentity.crmid, vtiger_account.accountname, vtiger_uniqueaccountname.accountname as labelname
            FROM vtiger_uniqueaccountname
            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_uniqueaccountname.accountid
            LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_uniqueaccountname.accountid
            WHERE vtiger_crmentity.deleted = 0
            AND vtiger_uniqueaccountname.accountname IN ('" . implode("', '", $labelNames)  . "')";
        $result = $adb->pquery($sql, []);
        $list = [];
        if ($adb->num_rows($result) > 0) {
            while($row = $adb->fetch_array($result)) {
                $name = isset($namesList[md5($row['labelname'])]) ? $namesList[md5($row['labelname'])]: $row['accountname'];
                $list[] = [
                    'cid' => $row['crmid'],
                    'accountname' => $name
                ];
            }
        }
        return $list;
    }

    /**
     * 批量插入客户（渠道甩单时创建客户）
     */
    public function batchInsertAccount($request) {
        global $adb;
        $accountInfos = $request->get('accountInfos');
        //客户的拥有人
        $ownerid = $request->get('ownerid');
        if (!is_array($accountInfos) || empty($accountInfos)) {
            return ['success'=>false, 'msg'=>'客户信息不合法'];
        }
        $labelRule = '/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;|&quot;|　|&apos;|&amp;|&lt;|&gt;|&#039;|&ldquo;|&rdquo;|&lsquo;|&rsquo;|&hellip;|\“|\”|\‘|\〉|\〈|\’|\〖|\〗|\【|\】|\、|\·|\…|\——|\＋|\－|\＝|\,|\<|\.|\>|\/|\?|\;|\:|\\\'|\"|\[|\{|\]|\}|\\|\||\`|\~|\!|\@|\#|\$|\%|\^|\\&|\*|\(|\)|\-|\_|\=|\+|\，|\＜|\．|\＞|\／|\？|\；|\：|\＇|\＂|\［|\{|\］|\}|\＼|\｜|\｀|\￣|\！|\＠|\#|\＄|\％|\＾|\＆|\＊|\（|\）|\－|\＿|\＝|\＋|\，|\《|\．|\》|\、|\？|\；|\：|\’|\”|\【|\｛|\】|\｝|\＼|\｜|\·|\～|\！|\＠|\＃|\￥|\％|\…|\＆|\×|\（|\）|\－|\——|\—|\＝|\＋|\，|\《|\。|\》|\、|\？|\；|\：|\‘|\“|\【|\｛|\】|\｝|\、|\||\·|\~|\！|\@|\#|\￥|\%|\&|\*|\（|\）|\-|\——|\=|\+/u';
        $accountRule = '/^(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+|(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+$/u';
        $labelNames = [];
        $dataList = [];
        foreach ($accountInfos as $key => $row) {
            if (!isset($row['accountname']) || $row['accountname'] == '') {
                return array('success'=>false, 'msg'=>'客户名称不能为空');
            }
            $labelName = preg_replace($labelRule,'', $row['accountname']);
            if ($labelName == '') {
                return array('success'=>false, 'msg'=>'客户名称不合法');
            }
            $labelName = strtoupper($labelName);
            $labelNames[] = $labelName;
            $dataList[md5($labelName)] = $row;
        }
        $list = [];
        $sql = "SELECT vtiger_crmentity.crmid, vtiger_account.accountname, vtiger_uniqueaccountname.accountname as labelname
            FROM vtiger_uniqueaccountname
            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_uniqueaccountname.accountid
            LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_uniqueaccountname.accountid
            WHERE vtiger_crmentity.deleted = 0
            AND vtiger_uniqueaccountname.accountname IN ('" . implode("', '", $labelNames)  . "')";
        $query = $adb->pquery($sql, []);
        if ($adb->num_rows($query) > 0) {
            while($row = $adb->fetch_array($query)) {
                $md5Key = md5($row['labelname']);
                if (isset($dataList[$md5Key])) {
                    $list[] = [
                        'cid' => $row['crmid'],
                        'accountname' => $dataList[$md5Key]['accountname']
                    ];
                    //把已存在的客户从列表移除，以免重复创建
                    unset($dataList[$md5Key]);
                }
            }
        }
        global $current_user;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($ownerid);

        foreach ($dataList as $row) {
            $labelname = preg_replace($labelRule,'', $row['accountname']);
            $labelname = strtoupper($labelname);
            $accountname = preg_replace($accountRule,'', $row['accountname']);
            $address = $row['province'] . '#' . $row['city'] . '#' . $row['area'] . '#' . $row['address'];
            $req = new Vtiger_Request([], []);
            $req->set('accountname', $accountname);
            $req->set('module','Accounts');
            $req->set('view','Edit');
            $req->set('action','Save');
            $req->set('makedecisiontype','Decisionmakers');
            $req->set('address', $address);
            $req->set('phone', $row['phone']);
            $req->set('linkname', $row['linkname']);
            $req->set('title', $row['title']);
            $req->set('email1', $row['email']);
            $req->set('mobile', $row['mobile']);
            $req->set('weixin', $row['weixin']);
            $req->set('customertype', $row['customertype']);
            $req->set('website', $row['website']);
            $req->set('gendertype', $row['gendertype']);
            $ressorder = new Vtiger_Save_Action();
            $recordModel = $ressorder->saveRecord($req);
            $crmid = $recordModel->getId();
            $sql = 'REPLACE INTO vtiger_uniqueaccountname(accountid, accountname) VALUES(?, ?)';
            $adb->pquery($sql, [$crmid, $labelname]);
            //accountrank=bras_isv 铜牌成交客户, accountflag=1 渠道甩单客户
            $sql = 'UPDATE vtiger_account SET protectday=?, effectivedays=?, accountrank=?, accountflag=? WHERE accountid=?';
            $adb->pquery($sql, array(30, 30, 'bras_isv', 1, $crmid));
            $list[] = [
                'cid' => $crmid,
                'accountname' => $row['accountname']
            ];
        }
        return ['success'=>true, 'list'=>$list];
    }

    public function getUserInfo(){
        global $adb;
        $query="SELECT id,CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ) ) ) AS last_name FROM vtiger_users WHERE `status` = 'Active'";
        return $adb->run_query_allrecords($query);
    }

    public function getDepartmentData(){
        global $adb;
        $query="SELECT (SELECT GROUP_CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE vtiger_departments.departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ) ) ) AS last_name FROM vtiger_users WHERE FIND_IN_SET(id,userids)) AS 'useridsname',departmentname,vtiger_departmentragentid.* FROM vtiger_departmentragentid LEFT JOIN vtiger_departments  dm ON dm.departmentid=vtiger_departmentragentid.departmentid";
        return $adb->run_query_allrecords($query);
    }

    public function addFirstPurchase(Vtiger_Request $request){
        $this->_logs(array("addFirstPurchaseRequest",$request));
        $db = PearDatabase::getInstance();
        $datas = $request->get('firstpurchasedata');
        $this->_logs(array("addFirstPurchase",$datas));
        $sql = "insert into vtiger_firstpurchase (`ordercode`,`firstPurchaseMarketPrice`,`firstPurchaseRenewPrice`,`firstPurchaseCostPrice`,`firstPurchaseCostRenewPrice`,`firstPurchaseMarketRenewPrice`,`firstPurchasePrice`) values ";
        $this->_logs(array("sql1",$sql));
        foreach ($datas as $data){
            $sql .= "('".$data['OrderCode']."','".$data['firstPurchase']['firstPurchaseMarketPrice']."','".$data['firstPurchase']['firstPurchaseRenewPrice']."','".$data['firstPurchase']['firstPurchaseCostPrice']."','".$data['firstPurchase']['firstPurchaseCostRenewPrice']."','".$data['firstPurchase']['firstPurchaseMarketRenewPrice']."','".$data['firstPurchase']['firstPurchasePrice']."'),";
        }
        $sql = trim($sql,",");
        $this->_logs(array("sql",$sql));
        $db->pquery($sql,array());
    }

    public function getFirstPurchase($orderCode){
        $db = PearDatabase::getInstance();
        $sql = "select * from vtiger_firstpurchase where ordercode=? limit 1";
        $result = $db->pquery($sql,array($orderCode));
        if(!$db->num_rows($result)){
            return array();
        }
        $row = $db->fetchByAssoc($result,0);
        $data = array(
            'firstPurchaseMarketPrice' => $row['firstpurchasemarketprice'],
            'firstPurchaseRenewPrice' => $row['firstpurchaserenewprice'],
            'firstPurchaseCostPrice' => $row['firstpurchasecostprice'],
            'firstPurchaseCostRenewPrice' => $row['firstpurchasecostrenewprice'],
            'firstPurchaseMarketRenewPrice' => $row['firstpurchasemarketrenewprice'],
            'firstPurchasePrice' => $row['firstpurchaseprice'],
        );
        return $data;
    }

    /**
     * 是否存在待完善订单
     *
     * @param $request
     */
    public function isExistImperfectOrder($request){
        $db=PearDatabase::getInstance();
        $result = $db->pquery("select imperfectorderid from vtiger_imperfectorder where deleted=0 and servicecontractsid=?",array($request->get('servicecontractsid')));
        if($db->num_rows($result)){
            $row = $db->fetchByAssoc($result,0);
            return array('success'=>1,'imperfectorderid'=>$row['imperfectorderid'],'isExist'=>true);
        }
        return array('success'=>1,'msg'=>'获取成功','imperfectorderid'=>'','isExist'=>false);
    }

    public function imperfectOrderList($request){
        $db=PearDatabase::getInstance();
        $result = $db->pquery("select imperfectorderid,contract_no,customername,createdat from vtiger_imperfectorder where deleted=0 and userid=?",array($request->get('userid')));
        $data = array();
        if($db->num_rows($result)){
            while ($row = $db->fetchByAssoc($result)){
                $data[] = array(
                    'imperfectorderid'=>$row['imperfectorderid'],
                    'contract_no'=>$row['contract_no'],
                    'customername'=>$row['customername'],
                    'createdat'=>$row['createdat'],
                );
            }
        }
        return array('success'=>1,'msg'=>'获取成功','data'=>$data);
    }

    public function imperfectOrderDetail($request){
        $db=PearDatabase::getInstance();
        $sql = "select * from vtiger_imperfectorder where imperfectorderid=?";
        $result = $db->pquery($sql,array($request->get('imperfectorderid')));
        $row=$db->fetchByAssoc($result,0);
        return array(
            "success"=>1,
            'msg'=>'获取成功',
            'servicecontractsid'=>$row['servicecontractsid'],
            'signaturetype'=>$row['signaturetype'],
            'accountid'=>$row['accountid'],
            'contract_no'=>$row['contract_no'],
            'customername'=>$row['customername'],
            'bulletContent'=>$row['bulletcontent'],
            'orderContent'=>$row['ordercontent'],
            'accountInfo'=>$row['accountinfo'],
            'formData'=>$row['formdata']
        );
    }

    public function cleanImperfectOrder($request){
        $db=PearDatabase::getInstance();
        $result = $db->pquery("update vtiger_imperfectorder set deleted=1 where imperfectorderid=?",array($request->get('imperfectorderid')));
        return array('success'=>1,'msg'=>'清除成功');

    }

    public function cleanImperfectOrderByServiceContractsId($request){
        $db=PearDatabase::getInstance();
        $result = $db->pquery("update vtiger_imperfectorder set deleted=1 where servicecontractsid=?",array($request->get('servicecontractsid')));
        return array('success'=>1,'msg'=>'清除成功');

    }

    public function imperfectOrderNum($request){
        $db=PearDatabase::getInstance();
        $result = $db->pquery("select count(1) as total from vtiger_imperfectorder where deleted=0 and userid=?",array($request->get("userid")));
        $total = $db->fetchByAssoc($result);
        return $total['total'];
    }

    public function createImperfectOrder($request){
        $db= PearDatabase::getInstance();
        $inputdata = array(
            "userid"=>$request->get("userid"),
            "customername"=>$request->get("customername"),
            "accountid"=>$request->get("accountid"),
            "contract_no"=>$request->get("contract_no"),
            "signaturetype"=>$request->get("signaturetype"),
            "servicecontractsid"=>$request->get("servicecontractsid"),
            "createdat"=>date("Y-m-d H:i:s"),
            "bulletContent"=>$request->get("bulletContent"),
            "orderContent"=>$request->get("orderContent"),
            "accountInfo"=>$request->get("accountInfo"),
            "formData"=>$request->get("formData"),
        );
        $db->pquery("delete from vtiger_imperfectorder where contract_no=?",array($request->get("contract_no")));
        $sql="INSERT INTO vtiger_imperfectorder(".implode(',',array_keys($inputdata)).") values(".generateQuestionMarks($inputdata).")";
        $db->pquery($sql,array(array_values($inputdata)));

        return array('success'=>1,'msg'=>'创建成功');
    }

    public function newBillInfo($request){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select * from vtiger_taxtype order by sortorderid asc",array());
        while ($row = $db->fetchByAssoc($result)){
            $taxtype[] = array(
                "key"=>$row['taxtype'],
                'value'=>translateLng("Newinvoice")[$row['taxtype']]
            );
        }
        $result2 = $db->pquery("select * from vtiger_invoicetype  order by sortorderid asc",array());
        while ($row2=$db->fetchByAssoc($result2)){
            if($request->get('signaturetype')=='eleccontract' && $row2['invoicetype']=='c_normal'){
                continue;
            }
            $invoicetype[] = array(
                "key"=>$row2['invoicetype'],
                'value'=>translateLng("Newinvoice")[$row2['invoicetype']]
            );
        }

        $contractid = $request->get("servicecontractsid");
        $accountid = $request->get("accountid");
        $invoiceid = $request->get("invoiceid");
        $invoiceInfo = array();
        $actualtotal=0;
        $billingcontent='';
        if($contractid){
            $result3 = $db->pquery("SELECT
	a.*,
	b.accountname 
FROM
	vtiger_newinvoice a
	LEFT JOIN vtiger_account b ON a.accountid = b.accountid 
WHERE
	a.contractid =? 
	AND b.accountid =? 
	AND a.modulestatus != 'c_cancel' 
ORDER BY
	a.invoiceid DESC",array($contractid,$accountid));
            while ($row3=$db->fetchByAssoc($result3)){
//                if($row3['accoutname']!=$row3['businessnamesone']){
//                    continue;
//                }
                $actualtotal +=$row3['actualtotal'];
//                $invoiceInfo[] = array(
//                    "invoiceid"=>$row3['invoiceid'],
//                    "invoiceno"=>$row3['invoiceno'],
//                    "businessnamesone"=>$row3['businessnamesone'],
//                    "taxpayers_no"=>$row3['taxpayers_no'],
//                    "registeraddress"=>$row3['registeraddress'],
//                    "telephone"=>$row3['telephone'],
//                    "depositbank"=>$row3['depositbank'],
//                    "accountnumber"=>$row3['accountnumber'],
//                    "addressee"=>$row3['addressee'],
//                    "addresseephone"=>$row3['addresseephone'],
//                    "address"=>$row3['address'],
//                    "email"=>$row3['email'],
//                    "billingid"=>$row3['billingid'],
//                    "modulestatus"=>$row3['modulestatus'],
//                    "modulestatuslng"=>translateLng("Newinvoice")[$row3['modulestatus']],
//
//                    "invoicetypelng"=>translateLng("Newinvoice")[$row3['invoicetype']],
//                    "invoicetype"=>$row3['invoicetype'],
//                    "invoicecompany"=>$row3['invoicecompany'],
//                    "taxtype"=>$row3['taxtype'],
//                    "taxtotal"=>$row3['taxtotal'],
//                    "actualtotal"=>$row3['actualtotal'],
//                );
            }

            $querylist="SELECT
                            vtiger_account.accountname,
                            vtiger_account.accountid,
                            vtiger_servicecontracts.invoicecompany,
                            vtiger_servicecontracts.billcontent
                        FROM
                            vtiger_account
                        LEFT JOIN vtiger_servicecontracts ON vtiger_account.accountid = vtiger_servicecontracts.sc_related_to
                        WHERE
                            vtiger_servicecontracts.servicecontractsid ={$contractid} ";
            $result4 = $db->pquery($querylist,array());
            if($db->num_rows($result4)){
                $queryResult =$db->fetchByAssoc($result4,0);
                $billingcontent=$queryResult['billcontent'];
            }
        }

        $billingcontentarr=array();
        $billingcontentlists=Newinvoice_Record_Model::getAllBillingcontent();
        foreach ($billingcontentlists as $billingcontentlist){
            if(!$billingcontentlist['billingcontent']){
                continue;
            }
            $billingcontentarr[]=array(
              'key'=>$billingcontentlist['billingcontent'],
              'value'=>$billingcontentlist['billingcontent'],
            );
        }
//        if($invoiceid){
//            $result5 = $db->pquery("select billingcontent from vtiger_newinvoice where invoiceid=?",array($invoiceid));
//            if($db->num_rows($result5)){
//                $row5=$db->fetchByAssoc($result5,0);
//                $billingcontent=$row5['billingcontent'];
//            }
//        }

        return array(
            "success"=>1,
            "msg"=>"获取成功",
            'taxtype'=>$taxtype,
            'invoicetype'=>$invoicetype,
            'invoiceInfo'=>$invoiceInfo,
            'actualtotal'=>$actualtotal,
            'billingcontentlist'=>$billingcontentarr,
            'billingcontent'=>$billingcontent
        );
    }

    public function billingList($request){
        $db= PearDatabase::getInstance();
        //获取客户的税务信息
        $accountid =$request->get("accountid");
        $result4 = $db->pquery("select * from vtiger_billing where accountid = ?",array($accountid));
        while ($row4=$db->fetchByAssoc($result4)){
            $billInfo[] = array(
                "billingid"=>$row4['billingid'],
                "businessnamesone"=>$row4['businessnamesone'],
                "taxpayers_no"=>$row4['taxpayers_no'],
                "registeraddress"=>$row4['registeraddress'],
                "telephone"=>$row4['telephone'],
                "depositbank"=>$row4['depositbank'],
                "accountnumber"=>$row4['accountnumber'],
            );
        }
        return array('success'=>1,'msg'=>'获取成功','invoiceInfo'=>$billInfo);
    }

    public function getAllowInvoiceTotal($request){
        $servicecontractsidnum = $request->get('servicecontractsid');
        global $adb;
        $query="SELECT * FROM vtiger_crmentity WHERE crmid=? LIMIT 1";
        $result=$adb->pquery($query,array($servicecontractsidnum));
        $resultdata=$adb->query_result_rowdata($result,0);
        if($resultdata['setype']!='SupplierContracts'){
            $invoicecompany='vtiger_servicecontracts.invoicecompany';
            $servicecontractsid='vtiger_servicecontracts.servicecontractsid';
            $servicecontractsid1='vtiger_servicecontracts.servicecontractsid';
            $contract_no='vtiger_servicecontracts.contract_no';
            $billcontent='vtiger_servicecontracts.billcontent';
            $tablename='vtiger_servicecontracts';
            $receivedstatus='normal';
        }else{
            $invoicecompany='vtiger_suppliercontracts.invoicecompany';
            $servicecontractsid='vtiger_suppliercontracts.suppliercontractsid AS servicecontractsid';
            $servicecontractsid1='vtiger_suppliercontracts.suppliercontractsid';
            $contract_no='vtiger_suppliercontracts.contract_no';
            $billcontent='vtiger_suppliercontracts.billcontent';
            $tablename='vtiger_suppliercontracts';
            $receivedstatus='RebateAmount';
        }

        //通过合同匹配金额大于0的回款
        $query_sql = "SELECT
                    vtiger_receivedpayments.allowinvoicetotal,
                    vtiger_receivedpayments.unit_price
                FROM
                    {$tablename}
                LEFT JOIN vtiger_receivedpayments ON ({$servicecontractsid1} = vtiger_receivedpayments.relatetoid)
                WHERE
                  vtiger_receivedpayments.deleted=0
                AND vtiger_receivedpayments.receivedstatus = '{$receivedstatus}'
                AND {$servicecontractsid1}=?";
        $sel_result = $adb->pquery($query_sql, array($servicecontractsidnum));
        $res_cnt = $adb->num_rows($sel_result);
        $allowTotal = '0';
        if($res_cnt > 0) {
            while($rawData=$adb->fetch_array($sel_result)) {
                $allowTotal = bcadd($allowTotal,strval( $rawData['unit_price']));
            }
        }
        if($allowTotal<=0){
            return array('success'=>0,'msg'=>'该合同下没有可开发票金额的回款，您可以申请预开票--可能原因：客户到款未匹配到该合同上或者已经申请过开票','allowTotal'=>$allowTotal);
        }

//        //已申请完成开票的金额
        $totalSql="select sum(taxtotal) as total from vtiger_newinvoice where contractid=? and  iscancel!=1 and modulestatus in('c_complete','b_check')";
        $totalResult = $adb->pquery($totalSql,array($servicecontractsidnum));
        $totalArr=$adb->fetchByAssoc($totalResult,0);

        return array('success'=>1,'msg'=>'获取成功','allowTotal'=>($allowTotal-$totalArr['total']));
    }


    /**
     * 移动端保存发票信息
     *
     * @param $request
     */
    public function mobileCreateInvoice($request){
        $data = $request->get('rdata');
        $data = base64_decode($data);
        $data = json_decode($data,true);
	$this->_logs(array('mobileCreateData'=>$data));
        $invoiceid = $data["invoiceid"];
        $invoicetype = $data["invoicetype"];
        $contractid = $data["contractid"];
        $billingsourcedata = $data["billingsourcedata"];
        $invoicecompany = $data["invoicecompany"];
        $taxtype = $data["taxtype"];
        $accountid = $data["accountid"];
        $businessnamesone = $data["businessnamesone"];
        $taxtotal = $data["taxtotal"];
        $actualtotal = $data["actualtotal"];
        $email = $data["email"];
        $taxpayers_no = $data["taxpayers_no"];
        $registeraddress = $data["registeraddress"];
        $depositbank = $data["depositbank"];
        $telephone = $data["telephone"];
        $accountnumber = $data["accountnumber"];
        $billingid = $data["billingid"];
        $billingcontent=$data['billingcontent'];
//        if($contractid){
//            $db = PearDatabase::getInstance();
//            $result = $db->pquery("select billcontent from vtiger_servicecontracts where servicecontractsid=?",array($contractid));
//            if($db->num_rows($result)){
//                $row = $db->fetchByAssoc($result,0);
//                $billingcontent = $row['billcontent'];
//            }
//        }
        global $current_user;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($request->get('userid'));
        $request_a=array(
            "module"	=>'Newinvoice',
            "action"	=>'Save',
            "view"	=>'Edit',
            'billingsourcedata'	=>'contractsource',
            'record'	=>$invoiceid,
            'invoicetype'	=>$invoicetype,
            'contractid'	=>$contractid,
            'invoicecompany'	=>$invoicecompany,
            'taxtype'	=>$taxtype,
            'account_id'	=>$accountid,
            'accountid'	=>$accountid,
            'businessnamesone'	=>$businessnamesone,
            'taxtotal'	=>$taxtotal,
            'actualtotal'	=>$actualtotal,
            'email'	=>$email,
            'isaccountinvoice'	=>'yesneed',
            'taxpayers_no'	=>$taxpayers_no,
            'registeraddress'	=>$registeraddress,
            'depositbank'	=>$depositbank,
            'telephone'	=>$telephone,
            'accountnumber'	=>$accountnumber,
            'billingid'	=>$billingid,
            'isFromMobile'	=>1,
            'billingcontent'	=>$billingcontent,
            'assigned_user_id'	=>$request->get("userid"),
            'taxrate'	=>'6%',
            'businessnames'	=>$businessnamesone,
            "addressee"=>$data['addressee'],
            "addresseephone"=>$data['addresseephone'],
            "address"=>$data['address'],
            "modulename"=>'ServiceContracts',
            "attachmentsid"=>$data['attachmentsid'],
            "zizhifile"=>$data['zizhifile'],
        );

        if($invoicetype=='c_normal'){
            $request_b = $this->getRelationReceivedpayments($contractid,$request_a);
            $this->_logs(array("创建发票res-request_b",'request_b'=>$request_b));
            $request_a['moreinvoice']=md5(date("Y-m-d"));
            $request_a=array_merge($request_a,$request_b);
            $this->_logs(array("创建发票res-request_a",'request_b'=>$request_a));
        }

        $_REQUEST=$request_a;
        $saveAction = new Newinvoice_Save_Action();
        $recordModel  = $saveAction->saveRecord(new Vtiger_Request($request_a, $request_a));
        $_REQUEST['record']=$recordModel->getId();
        $this->_logs(array("创建发票res",'contractid'=>$contractid,'data'=>$recordModel->getId()));
        // 1-开票并提交 2-仅开票 3-不开票
        if($recordModel->getId() && $data['issubmitverify']==1){
            $request->set("recordid",$recordModel->getId());
            $this->_logs(array("创建发票res4",'issubmitverify'=>$data['issubmitverify']));
            $returnData = $recordModel->makeWorkFlow($recordModel->getId());
            $this->_logs(array("提交工作流",'contractid'=>$contractid,'data'=>$returnData));
        }
        return array();
    }

    public function checkInvoice($request){
        $taxtype=$request->get('taxtype');
        $invoicecompany = $request->get('invoicecompany');
        $issubmitverify = $request->get('issubmitverify');
        $contractid = $request->get('contractid');
        $invoicetype = $request->get('invoicetype');
        $customer_name = $request->get('customer_name');
        $contract_money = $request->get('contract_money');
        $newInvoiceRecordModel = Newinvoice_Record_Model::getCleanInstance("Newinvoice");
        $checkData = $newInvoiceRecordModel->checkBusinessnamesAndCustomerNameIsSame($contractid,$customer_name);
        if($checkData['success']){
            return array('success'=>0,'msg'=>$checkData['msg']);
        }
        $sumTaxTotal = $newInvoiceRecordModel->getSumTaxTotal($contractid);
        if($sumTaxTotal && $sumTaxTotal>$contract_money){
            return array('success'=>0,'msg'=>'已开票金额>合同金额，请前往PC端进行发票作废或者红冲后再完成下单；');
        }

//        if($issubmitverify==3){
//            return array('success'=>1,'msg'=>'');
//        }
//        $billingsourcedata='contractsource';
//        $electronInvoiceCompany=Newinvoice_Record_Model::$ELECTRONINVOICECOMPANY;
//        if($taxtype=='electronicinvoice' && !in_array($invoicecompany, $electronInvoiceCompany)) {
//            $msginfo = "当前只支持'".implode("','",$electronInvoiceCompany)."',其他公司暂不支持开票，如有疑问，请联系财务部相关人员！";
//            return array('success' => 0, 'msg' => $msginfo);
//        }
//        $saveAction = new Newinvoice_Save_Action();
//        if($billingsourcedata=='contractsource'&&$saveAction->checkInvoicecompany($request)){
//            return array('success'=>0,'msg'=>'合同主体与开票公司不一致');
//        }
//        $newInvoiceBasicAction = new Newinvoice_BasicAjax_Action();
//        if($issubmitverify==1 && $invoicetype=='c_normal' &&!$newInvoiceBasicAction->isSignedWithContractAndStayPayment($contractid)){
//            return array('success'=>0,'msg'=>'正常开票需合同和代付款签收后方可提交审批');
//        }

        return array('success'=>1,'msg'=>'可以创建发票');
    }


    public function getRelationReceivedpayments($servicecontractsidnum,$request_a){
        global $adb;
        $query="SELECT * FROM vtiger_crmentity WHERE crmid=? LIMIT 1";
        $result=$adb->pquery($query,array($servicecontractsidnum));
        $resultdata=$adb->query_result_rowdata($result,0);
        if($resultdata['setype']!='SupplierContracts'){
            $invoicecompany='vtiger_servicecontracts.invoicecompany';
            $servicecontractsid='vtiger_servicecontracts.servicecontractsid';
            $servicecontractsid1='vtiger_servicecontracts.servicecontractsid';
            $contract_no='vtiger_servicecontracts.contract_no';
            $billcontent='vtiger_servicecontracts.billcontent';
            $tablename='vtiger_servicecontracts';
            $receivedstatus='normal';
        }else{
            $invoicecompany='vtiger_suppliercontracts.invoicecompany';
            $servicecontractsid='vtiger_suppliercontracts.suppliercontractsid AS servicecontractsid';
            $servicecontractsid1='vtiger_suppliercontracts.suppliercontractsid';
            $contract_no='vtiger_suppliercontracts.contract_no';
            $billcontent='vtiger_suppliercontracts.billcontent';
            $tablename='vtiger_suppliercontracts';
            $receivedstatus='RebateAmount';
        }

        //通过合同匹配金额大于0的回款
        $query_sql = "SELECT
                    {$invoicecompany},
                    vtiger_receivedpayments.paytitle AS t_paytitle,
                    vtiger_receivedpayments.receivedpaymentsid,
                    CONCAT(
                        vtiger_receivedpayments.reality_date,
                        '【',
                        vtiger_receivedpayments.receivedpaymentsid,
                        '】',
                        ' ￥',
                        vtiger_receivedpayments.unit_price,
                        ' ',
                        vtiger_receivedpayments.paytitle,
                        ' [',
                        {$contract_no},
                        ']'
                    ) AS paytitle,
                    vtiger_receivedpayments.unit_price,
                    vtiger_receivedpayments.reality_date,
                    {$servicecontractsid},
                    {$contract_no},
                    {$billcontent} AS billingcontent,
                    vtiger_receivedpayments.allowinvoicetotal
                FROM
                    {$tablename}
                LEFT JOIN vtiger_receivedpayments ON ({$servicecontractsid1} = vtiger_receivedpayments.relatetoid)
                WHERE
                  vtiger_receivedpayments.deleted=0
                AND vtiger_receivedpayments.receivedstatus = '{$receivedstatus}'
                AND vtiger_receivedpayments.allowinvoicetotal>0
                AND {$servicecontractsid1}=?";
        $sel_result = $adb->pquery($query_sql, array($servicecontractsidnum));
        $res_cnt = $adb->num_rows($sel_result);

        if($res_cnt > 0) {
            $i=1;
            while($rawData=$adb->fetch_array($sel_result)) {
                $request_a['insertii'][$i] = $i;
                $request_a['receivedpaymentsid_display'][$i] = $rawData['t_paytitle'];
                $request_a['receivedpaymentsid'][$i] = $rawData['receivedpaymentsid'];
                $request_a['servicecontractsid'][$i] = $rawData['servicecontractsid'];
                $request_a['servicecontractsid_display'][$i] = $rawData['contract_no'];
                $request_a['total'][$i] = $rawData['unit_price'];
                $request_a['arrivaldate'][$i] = $rawData['reality_date'];
                $request_a['allowinvoicetotal'][$i] = $rawData['allowinvoicetotal'];
                $request_a['invoicetotal'][$i] = $rawData['allowinvoicetotal'];
                $request_a['invoicecontent'][$i] = $rawData['billingcontent'];
                $request_a['remarks'][$i] = '';
                $i++;
            }
        }

        return $request_a;
    }

    /**
     * 发送下单成功短信
     *
     * @param $data
     * @param $contract_no
     */
    public function orderSuccessSms($data,$contract_no){
        return;
        $productname=$data['productname'];
        $productname=preg_replace('/\(\d+\)/','',$productname);
        $content = '【珍岛集团】亲爱的'.$data['customername'].'，你购买的'.$productname.'产品已提交下单申请，合同编号:'.$contract_no.'，为了不影响您的正常使用，建议您尽快交费并填写该合同编号。如您在使用过程中有任何疑问，可联系您的专属客服或拨打服务热线：400-880-0762；感谢您的支持！若已交费，请忽略此短信。';
        $requestData = array();
        $request = new Vtiger_Request($requestData,$requestData);
        $request->set("mobile",$data['mobile']);
        $request->set("content",$content);
        $this->sendSMS($request);
    }

    /**
     * 下单后 向T云确认到款
     *
     * @param $contractid
     * @param $classtype
     */

    public function orderToConfirm($contractid,$classtype,$total){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT sum(unit_price) as sumunitprice FROM vtiger_receivedpayments WHERE relatetoid=? AND receivedstatus='normal' AND deleted=0 AND ismatchdepart=1",array($contractid));
        $row = $db->fetchByAssoc($result,0);
        if($row['sumunitprice']>0 && $row['sumunitprice']<$total){
            $db->pquery("update vtiger_servicecontracts set contractstate=0,ispay=0,paytotyun=? where servicecontractsid=?",array($row['sumunitprice'],$contractid));
        }
        if($classtype=='upgrade'){
            $serviceContractRecordModel = ServiceContracts_Record_Model::getCleanInstance("ServiceContracts");
            $serviceContractRecordModel->payAfterMatch($contractid,0,false);
            return;
        }

        if(!$row['sumunitprice']){
            return;
        }

        $serviceContractRecordModel = ServiceContracts_Record_Model::getCleanInstance("ServiceContracts");
        $serviceContractRecordModel->payAfterMatch($contractid,$row['sumunitprice'],false);
    }

    public function isExistActiveOrder($contractid){
        $db =PearDatabase::getInstance();
        $sql = "select * from vtiger_activationcode where contractid=? and status in(0,1)";
        $result = $db->pquery($sql,array($contractid));
        if(!$db->num_rows($result)){
            return false;
        }
        if($db->num_rows($result)){
            $row = $db->fetchByAssoc($result,0);
            if($row['startdate'] && $row['startdate']!='0000-00-00 00:00:00'){
                return false;
            }
        }
        return true;
    }

    /**
     * 有没有在取消审核中的订单
     *
     * @param Vtiger_Request $request
     * @return array
     */
    public function isUnderReview(Vtiger_Request $request){
        $usercode = $request->get('usercode');
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select contractid from vtiger_activationcode where usercode=? and status in(0,1)",array($usercode));
        if(!$db->num_rows($result)){
            return array(true);
        }
        while ($row = $db->fetchByAssoc($result)){
            $contractid[] = $row['contractid'];
        }
        $serviceContractsModuleModel = ServiceContracts_Module_Model::getInstance("ServiceContracts");
        $result2 = $db->pquery("select 1 from vtiger_salesorderworkflowstages where salesorderid in(".implode(',',$contractid).") and isaction<2 and workflowsid=?",
            array($serviceContractsModuleModel->cancelOrderWorkFlowsid));
        if($db->num_rows($result2)){
            return array(false);
        }
        return array(true);
    }

    public function isNewInvoice(Vtiger_Request $request){
        $contractid=$request->get("servicecontractsid");
        $accountid=$request->get("accountid");
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select 1 from vtiger_newinvoice where contractid=? and accountid=? and modulestatus in ('c_complete','a_normal','b_check','a_exception')",array($contractid,$accountid));
        if(!$db->num_rows($result)){
            return array(0);
        }
        return array(1);
    }

    public function billingInfo(Vtiger_Request $request){
        $billingid = $request->get('billingid');
        $db = PearDatabase::getInstance();
        $result =$db->pquery("select a.*,b.accountname from vtiger_billing a left join vtiger_account b on a.accountid=b.accountid where billingid=? limit 1",array($billingid));
        if(!$db->num_rows($result)){
            return array(
                'success'=>0,
                'msg'=>'获取失败'
            );
        }
        $row =$db->fetchByAssoc($result,0);
        return array(
            'success'=>1,
            'msg'=>'获取成功',
            'billingInfo'=>array(
                "billingid"=>$row['billingid'],
                "businessnamesone"=>$row['businessnamesone'],
                "accountname"=>$row['accountname'],
                "taxpayers_no"=>$row['taxpayers_no'],
                "registeraddress"=>$row['registeraddress'],
                "telephone"=>$row['telephone'],
                "depositbank"=>$row['depositbank'],
                "accountnumber"=>$row['accountnumber'],
            )
        );

    }


    public function noMatchMoney(Vtiger_Request $request){
        global $adb,$limitDate;
        $tyun_account = $request->get('tyun_account');
        $query="SELECT b.servicecontractsid,b.total,b.contract_no,a.couponcode FROM vtiger_activationcode a left join vtiger_servicecontracts b on a.contractid=b.servicecontractsid WHERE a.status IN(0,1) AND a.usercode=? AND b.ispay=0 AND b.modulestatus !='c_stop' AND a.createdtime>? order by a.createdtime desc";
        $listResult = $adb->pquery($query, array($tyun_account,$limitDate));
        if($adb->num_rows($listResult)){
            $row = $adb->fetchByAssoc($listResult,0);
            $query="SELECT sum(vtiger_receivedpayments.unit_price) AS sumtotal FROM `vtiger_receivedpayments` WHERE relatetoid =? AND receivedstatus='normal'";
            $result = $adb->pquery($query,array($row['servicecontractsid']));
            $row2 = $adb->fetchByAssoc($result,0);
            $leastMoney=$row['total']-$row2['sumtotal'];
            if($row['couponcode']){
                $result3 = $adb->pquery("select * from vtiger_coupontocontract where couponcode=?",array($row['couponcode']));
                if($adb->num_rows($result3)){
                    $row3=$adb->fetchByAssoc($result3,0);
                    $contractids = explode(",",ltrim($row3['contractids'],','));
                    if(count($contractids) && in_array($row['servicecontractsid'],$contractids)){
                        $leastMoney=$leastMoney-$row3['facevalue'];
                    }
                }
            }
            if($leastMoney>0){
                return array('success'=>false,'msg'=>$row['contract_no'].'合同还剩'.$leastMoney.'元尚未支付，不能下单');
            }
        }
        return array("success"=>true,'msg'=>'');
    }


    public function billInfoList($request){
        $db = PearDatabase::getInstance();
        $contractid = $request->get("servicecontractsid");
        $accountid = $request->get("accountid");
        $actualinvoiced = array();
        $otherinvoiced = array();
        $actualtotal=0;
        $otherinvoicedtotal=0;
        if($contractid){
            $result3 = $db->pquery("SELECT
	a.*,
	b.accountname 
FROM
	vtiger_newinvoice a
	LEFT JOIN vtiger_account b ON a.accountid = b.accountid 
WHERE
	a.contractid =? 
	AND a.modulestatus in ('c_complete','b_check') 
    AND a.iscancel!=1
ORDER BY
	a.invoiceid DESC",array($contractid));
            while ($row3=$db->fetchByAssoc($result3)){
                if(in_array($row3['modulestatus'],array('c_complete','b_check'))){
                    $actualtotal +=$row3['taxtotal'];
                    $actualinvoiced[] = array(
                        "invoiceid"=>$row3['invoiceid'],
                        "invoiceno"=>$row3['invoiceno'],
                        "businessnamesone"=>$row3['businessnamesone'],
                        "taxpayers_no"=>$row3['taxpayers_no'],
                        "registeraddress"=>$row3['registeraddress'],
                        "telephone"=>$row3['telephone'],
                        "depositbank"=>$row3['depositbank'],
                        "accountnumber"=>$row3['accountnumber'],
                        "accountname"=>$row3['accountname'],
                        "addressee"=>$row3['addressee'],
                        "addresseephone"=>$row3['addresseephone'],
                        "address"=>$row3['address'],
                        "email"=>$row3['email'],
                        "billingid"=>$row3['billingid'],
                        "modulestatus"=>$row3['modulestatus'],
                        "modulestatuslng"=>translateLng("Newinvoice")[$row3['modulestatus']],

                        "invoicetypelng"=>translateLng("Newinvoice")[$row3['invoicetype']],
                        "invoicetype"=>$row3['invoicetype'],
                        "invoicecompany"=>$row3['invoicecompany'],
                        "taxtype"=>$row3['taxtype'],
                        "taxtotal"=>$row3['taxtotal'],
                        "actualtotal"=>$row3['actualtotal'],
                        "billingcontent"=>$row3['billingcontent'],
                    );
                }
//                else{
//                    $otherinvoicedtotal +=$row3['taxtotal'];
//                    $otherinvoiced[] = array(
//                        "invoiceid"=>$row3['invoiceid'],
//                        "invoiceno"=>$row3['invoiceno'],
//                        "businessnamesone"=>$row3['businessnamesone'],
//                        "taxpayers_no"=>$row3['taxpayers_no'],
//                        "registeraddress"=>$row3['registeraddress'],
//                        "telephone"=>$row3['telephone'],
//                        "depositbank"=>$row3['depositbank'],
//                        "accountnumber"=>$row3['accountnumber'],
//                        "addressee"=>$row3['addressee'],
//                        "addresseephone"=>$row3['addresseephone'],
//                        "address"=>$row3['address'],
//                        "email"=>$row3['email'],
//                        "billingid"=>$row3['billingid'],
//                        "modulestatus"=>$row3['modulestatus'],
//                        "modulestatuslng"=>translateLng("Newinvoice")[$row3['modulestatus']],
//
//                        "invoicetypelng"=>translateLng("Newinvoice")[$row3['invoicetype']],
//                        "invoicetype"=>$row3['invoicetype'],
//                        "invoicecompany"=>$row3['invoicecompany'],
//                        "taxtype"=>$row3['taxtype'],
//                        "taxtotal"=>$row3['taxtotal'],
//                        "actualtotal"=>$row3['actualtotal'],
//                        "billingcontent"=>$row3['billingcontent'],
//                    );
//                }

            }
        }
        return array(
            "success"=>1,
            "msg"=>"获取成功",
            'actualinvoiced'=>$actualinvoiced,
            'actualtotal'=>$actualtotal,
//            'otherinvoiced'=>$otherinvoiced,
//            'otherinvoicedtotal'=>$otherinvoicedtotal
        );
    }
}
