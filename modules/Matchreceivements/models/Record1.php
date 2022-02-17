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
class Matchreceivements_Record_Model extends Vtiger_Record_Model {
    /**
     * 获取T云的是新增单还是续费单
     * (a.productid>0 OR (a.productid=0 AND LENGTH(a.productid)>1))   有关vtiger_activationcode 表查询 时  套餐id 即 productid >0替换成 (a.productid>0 OR (a.productid=0 AND LENGTH(a.productid)>1))  因为有的id 是字符串
     */
    public static $ZHONGXIAOMANAGER= array("H2",'H78','H79','H80');
    public  function renewOrNewadd_bak($params){
        $adb = PearDatabase::getInstance();
        $rp['contract_no']=$params['contract_no'];
        $rp['servicecontractstype']=$params['servicecontractstype'];
        $rp['activationcodeid']=$params['activationcodeid'];
        $rp['usercode']=$params['usercode'];
        $rp['customerid']=$params['customerid'];
        $rp['productlife']=$params['productlife'];
        $rp['total']=$params['total'];
        $rp['contractid']=$params['contractid'];
        $rp['renewmarketprice']=$params['renewmarketprice'];
        $type=0;
        $arriveachievement=0;
        $updateachievementtype=0;
        $newusercode=$rp['usercode'];
        $sql=" SELECT activationcodeid,productclass,createdtime FROM vtiger_activationcode WHERE  contractid=?  AND status IN(0,1)";
        $result=$adb->pquery($sql,array($rp['contractid']));
        $currentActivationcodeid=$adb->query_result_rowdata($result,0);
        $productclass=$currentActivationcodeid['productclass'];
        $currentActivationcodeid=$currentActivationcodeid['activationcodeid'];
        $currentTime=$currentActivationcodeid['createdtime'];//下单时间
        //查询当前账号之前下的最近的一单
        // 如果是谷歌竞价去除 产品id 判定。
        if($params['contract_type']='GOOGLE竞价合同' && $params['parent_contracttypeid']=4){
            $sql="  SELECT productlife,expiredate ,usercode FROM vtiger_activationcode  WHERE usercode=? AND contractid!=?  AND activationcodeid<? AND productclass=? AND status IN(0,1)  ORDER BY expiredate DESC LIMIT 1 ";
        }else{
            //$sql="  SELECT productlife,expiredate ,usercode FROM vtiger_activationcode  WHERE usercode=? AND contractid!=?  AND activationcodeid<?  AND productclass=? AND status IN(0,1) AND (productid>0 OR (productid=0 AND LENGTH(productid)>1)) ORDER BY expiredate DESC LIMIT 1 ";
            $sql="  SELECT productlife,expiredate ,usercode FROM vtiger_activationcode  WHERE usercode=? AND contractid!=?  AND activationcodeid<?  AND productclass=? AND status IN(0,1)  ORDER BY expiredate DESC LIMIT 1 ";
        }

        $result=$adb->pquery($sql,array($newusercode,$rp['contractid'],$currentActivationcodeid,$productclass));
        $result=$adb->query_result_rowdata($result,0);
        //$currentTime=date("Y-m-d H:i:s");
        $remark='';
        $renewing=0;
        //如果该新单账户之前有过单子  老账户继续使用
        if(!empty($result)){
            $date=(strtotime($currentTime)-strtotime($result['expiredate']))/86400;
            //使用老账号 老账号 就是判断原定单里的产品是否过期
            if($date>90/* ||  $date<-90*/){
                //$remark="老账号内产品到期前三个月以上，或到期后3个月以上";
                $remark="";
                if($date>90){
                    $remark.=":老账到期后3个月以上";
                    $achievementtype='newadd';
                    //新单业绩计算
                }else{
                    $remark.="到期前3个月以上";
                    $achievementtype='renew';
                }
                $updateachievementtype=1;
            }elseif($date<-90){
                //g
                $sql="SELECT classtype,productid FROM vtiger_activationcode WHERE  contractid=? AND (classtype!='buy' OR productid>0) AND status IN(0,1)";
                $result=$adb->pquery($sql,array($rp['contractid']));
                if($adb->num_rows($result)){
                    $remark.=":老账号到期前3个月以上";
                    $achievementtype='renew';
                }else{
                    $remark.=":老账号到期前3个月以上，纯另购";
                    $achievementtype='newadd';
                }
            }else{ //老账号到期前3后3内
                $achievementtype='renew';
                $renewing=1;
                //算续费
            }
        }else{
            //查询是否存在老客户订单
            if($params['contract_type']='GOOGLE竞价合同' && $params['parent_contracttypeid']=4){
                $sql=" SELECT productlife,expiredate,renewmarketprice FROM vtiger_activationcode  WHERE  customerid=?  AND productclass=?  AND usercode!=?  AND activationcodeid<?  AND status IN(0,1)    ORDER BY expiredate DESC LIMIT 1  ";
            }else{
                $sql=" SELECT productlife,expiredate,renewmarketprice FROM vtiger_activationcode  WHERE  customerid=?   AND productclass=? AND usercode!=?  AND activationcodeid<?  AND status IN(0,1)  AND (productid>0 OR (productid=0 AND LENGTH(productid)>1))  ORDER BY expiredate DESC LIMIT 1  ";
            }
            $result=$adb->pquery($sql,array($rp['customerid'],$rp['usercode'],$currentActivationcodeid,$productclass));
            $result=$adb->query_result_rowdata($result,0);
            // 老客户不再继续使用的情况下 即用了新账户开了单子
            if(!empty($result)){
                $date=(strtotime($currentTime)-strtotime($result['expiredate']))/86400;
                if($date>90 ||  $date<-90)//最近老账号老账号内产品到期前3个月之前，到期了3个月以上
                {
                    $remark="新账户开了单子老账号老账号内产品到期前3个月之前，到期了3个月以上";
                    $achievementtype='newadd';
                }else{
                    $achievementtype='renew';
                    $renewing=1;
                    $remark.="新账户开了单子老账号老账号内产品到期前3个月之内，到期了3个月以内";
                    //$arriveachievement=0;
                    $arriveachievement = $rp['total'] - $result['renewmarketprice']*$rp['productlife'];// 算业绩实际用的合同金额
                    $arriveachievement<0?0:$arriveachievement;
                    $type=1;
                }
            }else{
                $remark.="没有老客户订单";
                $achievementtype='newadd';
            }
        }
        return array("achievementtype"=>$achievementtype,"arriveachievement"=>$arriveachievement,'type'=>$type,'remark'=>$remark,'date'=>$date,'updateachievementtype'=>$updateachievementtype,'renewing'=>$renewing);
    }
    public  function renewOrNewadd($params){
        $adb = PearDatabase::getInstance();
        $rp['contract_no']=$params['contract_no'];
        $rp['servicecontractstype']=$params['servicecontractstype'];
        $rp['activationcodeid']=$params['activationcodeid'];
        $rp['usercode']=$params['usercode'];
        $rp['customerid']=$params['customerid'];
        $rp['productlife']=$params['productlife'];
        $rp['total']=$params['total'];
        $rp['contractid']=$params['contractid'];
        $rp['renewmarketprice']=$params['renewmarketprice'];
        $type=0;
        $arriveachievement=0;
        $updateachievementtype=0;
        $renewing=0;
        $newusercode=$rp['usercode'];
        $sql=" SELECT activationcodeid,productclass,createdtime,productid,buyseparately,classtype,canrenew FROM vtiger_activationcode WHERE  contractid=?  AND status IN(0,1)";
        $result=$adb->pquery($sql,array($rp['contractid']));
        $currentproductids=array();
        $currentbuyseparately=array();
        $isdegrade=false;
        while($row=$adb->fetch_array($result)){
            if('degrade'==$row['classtype']){
                $isdegrade=true;
                break;
            }
            $productclass = $row['productclass'];
            $currentActivationcodeid = $row['activationcodeid'];
            $currentTime = $row['createdtime'];//下单时间
            if($row['productid']>0){
                $currentproductids[]=$row['productid'];
            }
            if($row['buyseparately']>0 && $row['canrenew']==1){
                $currentbuyseparately[]=$row['buyseparately'];
            }
        }
        if($isdegrade){
            return array("achievementtype"=>'renew',"arriveachievement"=>$arriveachievement,'type'=>$type,'remark'=>'降级合同','date'=>'','updateachievementtype'=>$updateachievementtype,'renewing'=>$renewing);
        }
        /*$currentActivationcodeid=$adb->query_result_rowdata($result,0);
        $productclass=$currentActivationcodeid['productclass'];
        $currentActivationcodeid=$currentActivationcodeid['activationcodeid'];
        $currentTime=$currentActivationcodeid['createdtime'];//下单时间*/
        //查询当前账号之前下的最近的一单
        // 如果是谷歌竞价去除 产品id 判定。
        if($params['contract_type']='GOOGLE竞价合同' && $params['parent_contracttypeid']=4){
            $sql="  SELECT productlife,expiredate ,usercode FROM vtiger_activationcode  WHERE usercode=? AND contractid!=?  AND activationcodeid<? AND productclass=? AND status IN(0,1)  ORDER BY expiredate DESC LIMIT 1 ";
        }else{
            //$sql="  SELECT productlife,expiredate ,usercode FROM vtiger_activationcode  WHERE usercode=? AND contractid!=?  AND activationcodeid<?  AND productclass=? AND status IN(0,1) AND (productid>0 OR (productid=0 AND LENGTH(productid)>1)) ORDER BY expiredate DESC LIMIT 1 ";
            $sql="  SELECT productlife,expiredate ,usercode FROM vtiger_activationcode  WHERE usercode=? AND contractid!=?  AND activationcodeid<?  AND productclass=? AND status IN(0,1)  ORDER BY expiredate DESC LIMIT 1 ";
        }

        $result=$adb->pquery($sql,array($newusercode,$rp['contractid'],$currentActivationcodeid,$productclass));
        $result=$adb->query_result_rowdata($result,0);
        //$currentTime=date("Y-m-d H:i:s");
        $remark='';

        //如果该新单账户之前有过单子  老账户继续使用
        $firstThreeMonth=date('Y-m-d 00:00:00',strtotime('-3 months'.$currentTime));
        $LastThreeMonth=date('Y-m-d 23:59:59',strtotime('3 months'.$currentTime));
        $currentproductids=!empty($currentproductids)?$currentproductids:array(0);
        $currentbuyseparately=!empty($currentbuyseparately)?$currentbuyseparately:array(0);
        if(!empty($result)){
           //查询当前账号之前下的最近的一单
            $querysql = "SELECT activationcodeid FROM vtiger_activationcode WHERE usercode=? AND contractid!=?  AND activationcodeid<?  AND productclass=? AND ((productid>0 AND productid in(".implode(',',$currentproductids).")) OR (buyseparately>0 AND buyseparately IN(".implode(',',$currentbuyseparately)."))) AND status IN(0,1) AND createdtime>=? AND createdtime<=?";
            $productResult = $adb->pquery($querysql, array($newusercode, $rp['contractid'], $currentActivationcodeid, $productclass, $firstThreeMonth, $LastThreeMonth));
            if($adb->num_rows($productResult)) {
                $remark.="老账号下单时间在前三后三内有下过同产品订单，或到期前3后3内有续费订单";
                $achievementtype='renew';
            }else {
                //$sql = "SELECT productlife,expiredate,usercode FROM vtiger_activationcode  WHERE usercode=? AND contractid!=?  AND activationcodeid<?  AND productclass=? AND status IN(0,1)  AND expiredate>=? AND expiredate<=?";
                //$result = $adb->pquery($sql, array($newusercode, $rp['contractid'], $currentActivationcodeid, $productclass, $firstThreeMonth, $LastThreeMonth));
                $sql = "SELECT productlife,expiredate,usercode FROM vtiger_activationcode  WHERE customerid=? AND contractid!=?  AND activationcodeid<?  AND productclass=? AND status IN(0,1)  AND expiredate>=? AND expiredate<=?";
                $result = $adb->pquery($sql, array($rp['customerid'],$rp['contractid'], $currentActivationcodeid, $productclass, $firstThreeMonth, $LastThreeMonth));

                if ($adb->num_rows($result)) {
                    $achievementtype='renew';
                    $renewing=1;
                }else{
                    //$currentproductids=!empty($currentproductids)?$currentproductids:array(0);
                    //$currentbuyseparately=!empty($currentbuyseparately)?$currentbuyseparately:array(0);
                    $sql="  SELECT productlife,expiredate ,usercode FROM vtiger_activationcode  WHERE usercode=? AND contractid!=?  AND activationcodeid<?  AND productclass=? AND status IN(0,1) AND ((productid>0 AND productid IN(".implode(',',$currentproductids).")) OR (buyseparately IN(".implode(',',$currentbuyseparately).") and buyseparately>0))   ORDER BY expiredate DESC LIMIT 1";
                    $result=$adb->pquery($sql,array($newusercode,$rp['contractid'],$currentActivationcodeid,$productclass));
                    if($adb->num_rows($result)){
                        $result=$adb->query_result_rowdata($result,0);
                        $date=(strtotime($currentTime)-strtotime($result['expiredate']))/86400;
                        $remark="";
                        if($date>90){
                            $remark.="：老账号，老产品到期后3个月以上";
                            $achievementtype='newadd';
                            //新单业绩计算
                        }else{
                            $remark.="：老账号，老产品到期前3个月以上";
                            $achievementtype='renew';
                        }
                        $updateachievementtype=1;
                    }else{
                        $achievementtype='newadd';
                    }
                }
            }
        }else{
            //查询是否存在老客户订单
           /* if($params['contract_type']='GOOGLE竞价合同' && $params['parent_contracttypeid']=4){
                $sql=" SELECT productlife,expiredate,renewmarketprice FROM vtiger_activationcode  WHERE  customerid=?  AND productclass=?  AND usercode!=?  AND activationcodeid<?  AND status IN(0,1)    ORDER BY expiredate DESC LIMIT 1  ";
                $sql=" SELECT productlife,expiredate,renewmarketprice FROM vtiger_activationcode  WHERE  customerid=?  AND productclass=?  AND usercode!=?  AND activationcodeid<?  AND status IN(0,1)    ORDER BY expiredate DESC LIMIT 1  ";
            }else{
                $sql=" SELECT productlife,expiredate,renewmarketprice FROM vtiger_activationcode  WHERE  customerid=?   AND productclass=? AND usercode!=?  AND activationcodeid<?  AND status IN(0,1)  AND (productid>0 OR (productid=0 AND LENGTH(productid)>1))  ORDER BY expiredate DESC LIMIT 1  ";
                $sql=" SELECT productlife,expiredate,renewmarketprice FROM vtiger_activationcode  WHERE  customerid=?   AND productclass=? AND usercode!=?  AND activationcodeid<?  AND status IN(0,1)  AND (productid>0 OR (productid=0 AND LENGTH(productid)>1))  ORDER BY expiredate DESC LIMIT 1  ";
            }*/
            $sql=" SELECT productlife,expiredate,renewmarketprice FROM vtiger_activationcode  WHERE  customerid=?  AND productclass=? AND usercode!=?  AND activationcodeid<? AND contractid!=? AND status IN(0,1) AND expiredate>=? AND expiredate<=?";
            $result=$adb->pquery($sql,array($rp['customerid'],$productclass,$rp['usercode'],$currentActivationcodeid,$rp['contractid'],$firstThreeMonth, $LastThreeMonth));
            $result=$adb->query_result_rowdata($result,0);
            // 老客户不再继续使用的情况下 即用了新账户开了单子
            if(!empty($result)){
                $achievementtype='renew';
                $renewing=1;
                $remark.="新账户开了单子老账号老账号内产品到期前3个月之内，到期了3个月以内";
            }else{
                $remark.="没有老客户订单";
                $achievementtype='newadd';
            }
        }
        return array("achievementtype"=>$achievementtype,"arriveachievement"=>$arriveachievement,'type'=>$type,'remark'=>$remark,'date'=>$date,'updateachievementtype'=>$updateachievementtype,'renewing'=>$renewing);
    }
    //  不计算业绩记录
    public function noCalculationAchievementRecord($params){
        $adb = PearDatabase::getInstance();
        $insertSql="INSERT INTO `vtiger_achievementallot_nocalculation` (`contract_no`, `marks`, `date`) VALUES (?,?,?)";
        $adb->pquery($insertSql,array($params['contract_no'],$params['marks'],date("Y-m-d H:i:s")));
    }

    /**
     * @param $serviceContractId  合同id
     * @param $receivedPaymentId  回款id
     * @param $multitype  是否多工单
     * @return $marketingPrice 市场价格
     */
    public static function noSaaSGetMarketing($row){
        $adb=PearDatabase::getInstance();
        $marketingPrice=0;
        if($row['multitype']==1){
            $sql="SELECT vtiger_salesorderproductsrel.marketprice FROM vtiger_salesorderproductsrel   LEFT JOIN vtiger_salesorderrayment as sd ON sd.salesorderid=vtiger_salesorderproductsrel.salesorderid WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND sd.receivedpaymentsid=?  AND multistatus=3  ";
            $product=$adb->pquery($sql,array($row['servicecontractid'],$row['receivedpaymentsid']));
        }else{
            $sql="SELECT vtiger_salesorderproductsrel.marketprice FROM vtiger_salesorderproductsrel   WHERE vtiger_salesorderproductsrel.servicecontractsid =?  AND  multistatus=3  ";
            $product=$adb->pquery($sql,array($row['servicecontractid']));
        }
        while ($rowsDat=$adb->fetch_array($product)){
                $marketingPrice+=$rowsDat['marketprice'];
        }
        return $marketingPrice;
    }
    // 续费判定为新单  合同金额小于首购市场价格公式
    public  function  getArriveachievementByFormulaZero($params){
        return ($params['total']*$params['total']/$params['marketprice'] -$params['costdeduction'])*($params['unit_price']/$params['total']);
    }
    // 续费 合同金额 小于首购市场价格
    public  function  getArriveachievementByFormulaRenew($params){
        $arriveachievement=($params['total']*$params['total']/$params['marketprice']-$params['costdeduction'])*($params['unit_price']/$params['total']);
        return array('arriveachievement'=>$arriveachievement,"remark"=>'续费单被判定多年单业绩合同金额小于首购市场价格');
    }

    // T云非升级订单 计算公式
    public  function  getArriveachievementByFormulaOne($params){
        if(($params['effectiveTotal']/$params['marketprice'])<0.75){
            //$arriveachievement=0;
            $remark="T云非升级 合同金额除以市场价 小于0.75"."合同金额".$params['effectiveTotal']."市场价格".$params['marketprice'];
            $arriveachievement=0;
        }else{
            $remark='';
            if($params['effectiveTotal']>=$params['marketprice']){
                $arriveachievement=($params['effectiveTotal']-$params['costdeduction'])*($params['unit_price']/$params['total']);
            }else if($params['effectiveTotal']<$params['marketprice']){
                $arriveachievement=($params['effectiveTotal']/$params['marketprice']*$params['effectiveTotal']-$params['costdeduction'])*($params['unit_price']/$params['total']);
            }
        }
        return  array('arriveachievement'=>$arriveachievement,"remark"=>$remark);
    }
    public  function  getArriveachievementByFormulaTYUNReNew($params){
        $buysplitcost=0;
        $renewsplitcost=0;
        $newaddsplitbusinessunit=0;
        $renewsplitbusinessunit=0;
        if(($params['total']/$params['marketprice'])<0.75){
            //$arriveachievement=0;
            $remark="T云非升级 合同金额除以市场价 小于0.75"."合同金额".$params['effectiveTotal']."市场价格".$params['marketprice'];
            $renewarriveachievement=0;
            $buyarriveachievement=0;
        }else{
            $remark='纯续费加另购';
            if($params['total']>=$params['marketprice']){
                $buyarriveachievement=(($params['total']-$params['renewmarketrenewprice']-$params['othermarketrenewprice'])-$params['othercostaddprice'])*($params['unit_price']/$params['total']);
                $renewarriveachievement=(($params['renewmarketrenewprice']+$params['othermarketrenewprice'])-$params['renewcostrenewprice']-$params['othercostrenewprice'])*($params['unit_price']/$params['total']);
            }else if($params['effectiveTotal']<$params['marketprice']){
                $buyarriveachievement=(($params['total']-$params['renewmarketrenewprice']-$params['othermarketrenewprice'])*$params['total']/$params['marketprice']-$params['othercostaddprice'])*($params['unit_price']/$params['total']);
                $renewarriveachievement=(($params['renewmarketrenewprice']+$params['othermarketrenewprice'])*$params['total']/$params['marketprice']-$params['renewcostrenewprice']-$params['othercostrenewprice'])*($params['unit_price']/$params['total']);
            }
            //$buysplitcost=$params['othercostaddprice']*$params['unit_price']/$params['total'];
            //$renewsplitcost=($params['renewcostrenewprice']+$params['othercostrenewprice'])*$params['unit_price']/$params['total'];
            //$newaddsplitbusinessunit=$params['unit_price']*$buyarriveachievement/($renewarriveachievement+$buyarriveachievement);
            //$renewsplitbusinessunit=$params['unit_price']*$renewarriveachievement/($renewarriveachievement+$buyarriveachievement);
        }
        return array('renewsplitcost'=>$renewsplitcost,
            'buysplitcost'=>$buysplitcost,
            'renewArriveachievement'=>$renewarriveachievement,
            'renewRemark'=>'纯续费加另购',
            'buyArriveachievement'=>$buyarriveachievement,
            'buysplitcontractamount'=>0,
            'buyRemark'=>$remark,
            'newaddsplitbusinessunit'=>$newaddsplitbusinessunit,
            'renewsplitbusinessunit'=>$renewsplitbusinessunit,
            'buysplitmarketprice'=>0,
            'renewsplitmarketprice'=>0,
            'renewsplitcontractamount'=>0,
        );
        //return  array('renewarriveachievemen'=>$renewarriveachievement,"remark"=>$remark,'buyarriveachievement'=>$buyarriveachievement);
    }
	public  function  getArriveachievementByFormulaOne1($params){
        if(false && ($params['effectiveTotal']/$params['marketprice'])<0.75){//不做75折判断
            //$arriveachievement=0;
            $remark="T云非升级 合同金额除以市场价 小于0.75"."合同金额".$params['effectiveTotal']."市场价格".$params['marketprice'];
            $arriveachievement=0;
        }else{
            $remark='';
            /*if($params['effectiveTotal']>=$params['marketprice']){
                $arriveachievement=($params['effectiveTotal']-$params['costdeduction'])*($params['unit_price']/$params['effectiveTotal']);
            }else if($params['effectiveTotal']<$params['marketprice']){*/
                $arriveachievement=($params['effectiveTotal']*$params['discount']-$params['costdeduction'])*($params['unit_price']/$params['effectiveTotal']);
            //}
        }
        return  array('arriveachievement'=>$arriveachievement,"remark"=>$remark);
    }
    // T云升级订单 到期前3个月内，到期后3个月内 计算公式
    public function getArriveachievementByFormulaTwo($params){
        if($params['effectiveTotal']>=$params['marketprice']){
            $arriveachievement=($params['effectiveTotal']-$params['costdeduction'])*($params['unit_price']/$params['total'])-$params['oldarriveachievement'];
            $remark="T云升级到期前3个月内，到期后3个月内合同金额大于等于市场价格";
        }else if($params['effectiveTotal']<$params['marketprice']){
            $arriveachievement=($params['effectiveTotal']/$params['marketprice']*$params['effectiveTotal']-$params['costdeduction'])*($params['unit_price']/$params['total'])-$params['oldarriveachievement'];
            $remark="T云升级到期前3个月内，到期后3个月内合同金额小于市场价格";
        }
        return  array('arriveachievement'=>$arriveachievement,"remark"=>$remark);
    }
    // T云升级订单 到期3个月以上的  过期后升级 计算公式
    public  function getArriveachievementByFormulaThree($params){
        if($params['contractprice']>=$params['marketprice']){
            $arriveachievement=($params['effectiveTotal']-$params['costdeduction'])*($params['unit_price']/$params['contractamount']);
            $remark='T云升级已到期超过90天合同金额大于市场价格';
        }else if($params['contractprice']<$params['marketprice']){
            $arriveachievement=($params['effectiveTotal']/$params['marketprice']*$params['effectiveTotal']-$params['costdeduction'])*($params['unit_price']/$params['contractamount']);
            $remark='T云升级已到期超过90天合同金额合同金额小于市场价格';
        }
        return  array('arriveachievement'=>$arriveachievement,"remark"=>$remark);
    }
    // T云升级订单 距离到期超过3个月以上
    public function getArriveachievementByFormulaFour($params){
        if($params['effectiveTotal']>=$params['marketprice']){
            $arriveachievement=($params['effectiveTotal']-$params['costdeduction'])*($params['unit_price']/$params['total'])-$params['oldarriveachievement'];
            $remark="合同金额大于等于市场价格";
        }else if($params['effectiveTotal']<$params['marketprice']){
            $arriveachievement=($params['effectiveTotal']/$params['marketprice']*$params['effectiveTotal']-$params['costdeduction'])*($params['unit_price']/$params['total'])-$params['oldarriveachievement'];
            $remark="合同金额小于市场价格";
        }
        return  array('arriveachievement'=>$arriveachievement,"remark"=>$remark);
    }
    // tisite 获取到账业绩公式  不符合公式
    public function getArriveachievementTsiteNoMatch($params){
        $totalToMarketprice=$params['total']/$params['marketprice'];
        $totalToMarketprice=$totalToMarketprice>1?1:$totalToMarketprice;
        $arriveachievement=$totalToMarketprice*$params['unit_price']-$params['unit_price']/$params['total']*$params['costdeduction']-$params['extracost'];
        return  $arriveachievement;
    }

    // tsite 获取到账业绩公式 符合公式
    function getArriveachievementTsiteMatch($params){
        if($params['total']>=$params['marketprice']){
            $arriveachievement=$params['unit_price']-$params['unit_price']/$params['total']*($params['costdeduction']);
        }else{
            $arriveachievement=$params['total']/$params['marketprice']*$params['unit_price']-$params['unit_price']/$params['total']*($params['costdeduction']);
        }
        return $arriveachievement;
    }
    // 获取业绩所属人部门信息
    public function getDepartmentInfo(&$Department){
        $adb=PearDatabase::getInstance();
        $departmentGradeArray= explode("::",$Department['parentdepartment']);
        $countDepartmentGradeArray=count($departmentGradeArray);
        if($countDepartmentGradeArray>3){
            if($countDepartmentGradeArray==4){
                $groupname=$Department['departmentname'];
                $departmentname='';
            }else{//  目前一定是五级部门 如果销售部门级别超过5级了 要根据需求改else里的代码 获取对应级别的
                $str="::".$Department['departmentid'];
                $Department['parentdepartment']= str_replace($str,"",$Department['parentdepartment']);
                $parentdepartment = explode("::",$Department['parentdepartment']);
                $parentdepartmentId = end($parentdepartment);
                //查询父类
                $queryc=" SELECT departmentname FROM vtiger_departments  WHERE  departmentid = ?  limit 1 ";
                $resultdataDepartment=$adb->pquery($queryc,array($parentdepartmentId));
                $Departments=$adb->query_result_rowdata($resultdataDepartment,0);
                $groupname=$Departments['departmentname'];// 四级部门
            }
        }else{
            $groupname='';
            $departmentname='';
        }
        return  array("groupname"=>$groupname,"departmentname"=>$departmentname);
    }
    /**
     * 业绩核算的配置
     * @param $contractid
     * @return array
     */
    public function getContractAchSetting($contractid,$salesorderid=0){
        global $adb;
        if($salesorderid>0){
            $contractid=$salesorderid;
            $query='SELECT * FROM `vtiger_contractperformancecostnew` WHERE salesorderid=? limit 1';
        }else{
            $query='SELECT * FROM `vtiger_contractperformancecostnew` WHERE servicecontractsid=? limit 1';
        }

        $result=$adb->pquery($query,array($contractid));
        $returnData=array();
        if($adb->num_rows($result)){
            $returnData=$result->fields;
        }
        return $returnData;
    }
    /**
     *清除当前回款占用的记录
     */
    public function subContractAchSetting($servicecontractid,$receivedpayid,$unit_price,$salesorderid=0){
        global $adb;
        $receivedpaymentsids=$receivedpayid.',';
        $sql='servicecontractsid=?';
        if($salesorderid>0){
            $servicecontractid=$salesorderid;
            $sql='salesorderid=?';
        }
        $delSql="DELETE FROM vtiger_contractperformancecostnew WHERE ".$sql." and receivedpaymentsids=?";
        $adb->pquery($delSql, array($servicecontractid,$receivedpaymentsids));
        $sql = "UPDATE vtiger_contractperformancecostnew SET repuntilprice=if(repuntilprice-?<=0,0,repuntilprice-?),receivedpaymentsids=REPLACE(receivedpaymentsids,'".$receivedpaymentsids."','') WHERE ".$sql." AND FIND_IN_SET(?,receivedpaymentsids)";
        $adb->pquery($sql, array($unit_price,$unit_price,$servicecontractid,$receivedpayid));
    }
    /**
     * 业绩核算的配置
     * @param $contractid
     * @return array
     */
    public function setContractAchSetting($params){
        global $adb;
        $marketpricesadd=0;
        $marketpricesrenew=0;
        $receivedpaymentsids='';
        $receivedpayid=0;
        foreach($params as $value) {
            $unit_price=$value['unit_price'];
            $receivedpaymentsids=$value['receivedpaymentsid'].',';
            $receivedpayid=$value['receivedpaymentsid'];
            $servicecontractid=$value['servicecontractid'];
            $salesorderid=$value['salesorderid'];
            if($value['achievementtype']=='newadd'){
                $marketpricesadd=$value['splitcontractamount'];
            }else{
                $marketpricesrenew=$value['splitcontractamount'];
            }
        }
        $sqlstr='servicecontractsid=?';
        $servicecontractidtemp=$servicecontractid;
        if($salesorderid>0){
            $servicecontractidtemp=$salesorderid;
            $sqlstr='salesorderid=?';
        }else{
            $salesorderid=0;
        }
        $delSql="DELETE FROM vtiger_contractperformancecostnew WHERE ".$sqlstr." and receivedpaymentsids=?";
        $adb->pquery($delSql, array($servicecontractidtemp,$receivedpaymentsids));
        $sql = "UPDATE vtiger_contractperformancecostnew SET repuntilprice=if(repuntilprice-?<=0,0,repuntilprice-?),receivedpaymentsids=REPLACE(receivedpaymentsids,'".$receivedpaymentsids."','') WHERE ".$sqlstr." and FIND_IN_SET(?,receivedpaymentsids)";
        $adb->pquery($sql, array($unit_price,$unit_price,$servicecontractidtemp,$receivedpayid));
        if (!$this->checkContractAchSetting($value['servicecontractid'],$salesorderid)) {
            $sql = 'INSERT INTO vtiger_contractperformancecostnew(repuntilprice,marketpricesadd,marketpricesrenew,receivedpaymentsids,servicecontractsid,salesorderid) values(?,?,?,?,?,?)';
            $adb->pquery($sql,array($unit_price,$marketpricesadd,$marketpricesrenew,$receivedpaymentsids,$servicecontractid,$salesorderid));
        } else {
            $sql = "UPDATE vtiger_contractperformancecostnew SET repuntilprice=repuntilprice+?,receivedpaymentsids=CONCAT(receivedpaymentsids,'".$receivedpaymentsids."') WHERE ".$sqlstr;
            $adb->pquery($sql,array($unit_price,$servicecontractidtemp));
        }
    }
    public function checkContractAchSetting($servicecontractid,$salesorderid=0){
        global $adb;
        $query='SELECT 1 FROM `vtiger_contractperformancecostnew` WHERE servicecontractsid=? limit 1';
        if($salesorderid>0){
            $servicecontractid=$salesorderid;
            $query='SELECT 1 FROM `vtiger_contractperformancecostnew` WHERE salesorderid=? limit 1';
        }
        $result=$adb->pquery($query,array($servicecontractid));
        $flag=false;
        if($adb->num_rows($result)){
            $flag=true;
        }
        return $flag;
    }

    /**
     * T云多年单业绩计算
     * @param $params
     * @param $actionName 调用的计算公式方法名称
     * @return array
     */
    public function calcTYUNRenewANDNewAddMoreYear($params,$actionName){
        $contractid=$params['contractid'];
        $onecostrenewprice=$params['onecostrenewprice'];//续费总成本
        $onemarketrenewprice=$params['onemarketrenewprice'];//续费总市场价
        $costdeduction=$params['costdeduction'];//总成本
        $rp['unit_price']=$params['unit_price'];//回款金额
        $rp['total']=$params['total'];//合同金额
        $rp['marketprice']=$params['marketprice'];//市场价总价
        $sumextracost=$params['extracost'];//其他扣除成本
        $sumextra_price=$params['extra_price'];//其他成
        $reutrndata=$this->getRepPirce($params);
        foreach($reutrndata as $key=>$value){
            $$key=$value;
        }
        // 拆分 续费单到账业绩
        $inparams['effectiveTotal']=$renewsplitcontractamount=$onemarketrenewprice;// 续费合同金额=合同总金额
        $inparams['marketprice']=$renewsplitmarketprice=$onemarketrenewprice;//续费市场价格=总市场价格-首购市场价格
        $inparams['costdeduction']=$onecostrenewprice;// 续费成本=总成本-首购成本
        $inparams['unit_price']=$renewsplitbusinessunit;
        $inparams['discount']=($rp['total']/$rp['marketprice']>1)?1:$rp['total']/$rp['marketprice'];
        $resultInfo=$this->$actionName($inparams);
        $renewArriveachievement=$resultInfo['arriveachievement'];
        $renewRemark=$resultInfo['remark'];

        $inparams['effectiveTotal']=$buysplitcontractamount=($rp['total']-$onemarketrenewprice)>0?($rp['total']-$onemarketrenewprice):0;// 计算收购单时合同金额=首购市场价格
        $inparams['marketprice']=$buysplitmarketprice=$rp['marketprice']-$onemarketrenewprice;//    首购市场价格
        $inparams['costdeduction']=$costdeduction-$onecostrenewprice;//  首购成本
        $inparams['unit_price']=$newaddsplitbusinessunit;//  总回款金额
        $resultInfo=$this->$actionName($inparams);
        $buyArriveachievement=$resultInfo['arriveachievement'];
        print_r(array('renewsplitcost'=>$renewsplitcost,
            'buysplitcost'=>$buysplitcost,
            'renewArriveachievement'=>$renewArriveachievement,
            'renewRemark'=>$renewRemark,
            'buyArriveachievement'=>$buyArriveachievement,
            'buysplitcontractamount'=>$buysplitcontractamount,
            'buyRemark'=>$buyRemark,
            'newaddsplitbusinessunit'=>$newaddsplitbusinessunit,
            'renewsplitbusinessunit'=>$renewsplitbusinessunit,
            'buysplitmarketprice'=>$buysplitmarketprice,
            'renewsplitmarketprice'=>$renewsplitmarketprice,
            'renewsplitcontractamount'=>$renewsplitcontractamount,
        ));
        $buyRemark=$resultInfo['remark'];
        return array('renewsplitcost'=>$renewsplitcost,
            'buysplitcost'=>$buysplitcost,
            'renewArriveachievement'=>$renewArriveachievement,
            'renewRemark'=>$renewRemark,
            'buyArriveachievement'=>$buyArriveachievement,
            'buysplitcontractamount'=>$buysplitcontractamount,
            'buyRemark'=>$buyRemark,
            'newaddsplitbusinessunit'=>$newaddsplitbusinessunit,
            'renewsplitbusinessunit'=>$renewsplitbusinessunit,
            'buysplitmarketprice'=>$buysplitmarketprice,
            'renewsplitmarketprice'=>$renewsplitmarketprice,
            'renewsplitcontractamount'=>$renewsplitcontractamount,
        );
    }
    /**
     * TSite多年单业绩计算
     * @param $params
     * @param $actionName 调用的计算公式方法名称
     * @return array
     */
    public function calcTSiteRenewANDNewAddMoreYear($params,$actionName){
        $contractid=$params['contractid'];
        $onecostrenewprice=$params['onecostrenewprice'];//续费总成本
        $onemarketrenewprice=$params['onemarketrenewprice'];//续费总市场价
        $costdeduction=$params['costdeduction'];//总成本
        $rp['unit_price']=$params['unit_price'];//回款金额
        $rp['total']=$params['total'];//合同金额
        $rp['marketprice']=$params['marketprice'];//市场价总价
        $lastExtracost=$params['lastExtracost'];//核减成本
        $isTsiteDiscount=$params['isTsiteDiscount'];//
        $sumextracost=$params['extracost'];//其他扣除成本
        $sumextra_price=$params['extra_price'];//其他成本
        $reutrndata=$this->getRepPirce($params);
        foreach($reutrndata as $key=>$value){
            $$key=$value;
        }
        $buysplitcontractamount=$rp['total']-$onemarketrenewprice;//拆分后新单占用的合同金额；
        $buysplitmarketprice=$rp['marketprice']-$onemarketrenewprice;//拆分后新单占用的市场价


        $deductionremark1='';
        $params['total']=$buysplitcontractamount;
        $params['marketprice']=$buysplitmarketprice;
        $params['costdeduction']=$costdeduction-$onecostrenewprice;
        $params['unit_price']=$newaddsplitbusinessunit;//拆分的回款
        $params['extracost']=0;
        $buyArriveachievement=0;
        if($newaddsplitbusinessunit>0){
            $buyArriveachievement=$this->$actionName($params);
            if($buyArriveachievement<=$lastExtracost){
                $lastExtracost-=$buyArriveachievement;
                $deductionremark1='额外成本扣减'.$buyArriveachievement;
                $buyArriveachievement=0;
            }else{
                $buyArriveachievement-=$lastExtracost;
                if(0==$lastExtracost){
                    $deductionremark1='额外成本扣减'.$lastExtracost;
                }
                $lastExtracost=0;
            }
        }
        $params['total']=$renewsplitcontractamount=$onemarketrenewprice;//2000
        $params['marketprice']=$renewsplitmarketprice=$onemarketrenewprice;//15000
        $params['unit_price']=$renewsplitbusinessunit;//拆分的回款
        $params['costdeduction']=$onecostrenewprice;
        $params['extracost']=0;
        $renewArriveachievement=$this->$actionName($params);
        // 剩余未扣减额外成本

        $templastExtracost=$lastExtracost;
        if($isTsiteDiscount==1){
            if( $rp['total']/$rp['marketprice']<0.75 ){
                $deductionremark1.="合同金额除市场价格小于0.75";
                $buyArriveachievement=0;
                $renewArriveachievement=0;
            }else{
                if($buysplitcontractamount/$buysplitmarketprice<0.75){//新单合同金额/新单市场价<0.75
                    $deductionremark1.="新购合同金额除市场价格小于0.75";
                    $buyArriveachievement=0;
                    $renewArriveachievement=$renewArriveachievement-$lastExtracost;
                    if($renewArriveachievement<0){
                        $templastExtracost=$buyArriveachievement*-1;
                        $renewArriveachievement=0;
                    }
                }else{
                    $buyArriveachievement=$buyArriveachievement-$lastExtracost;
                    if($buyArriveachievement<0){
                        $templastExtracost=$buyArriveachievement*-1;
                        $buyArriveachievement=0;
                    }
                }
            }
        }

        return array('renewsplitcost'=>$renewsplitcost,//续费拆分成本
            'buysplitcost'=>$buysplitcost,//新单拆分成本
            'renewArriveachievement'=>$renewArriveachievement,//续费业绩
            'deductionremark1'=>$deductionremark1,//备注
            'buyArriveachievement'=>$buyArriveachievement,//新购业绩
            'buysplitcontractamount'=>$buysplitcontractamount,//
            'newaddsplitbusinessunit'=>$newaddsplitbusinessunit,
            'renewsplitbusinessunit'=>$renewsplitbusinessunit,
            'buysplitmarketprice'=>$buysplitmarketprice,
            'renewsplitmarketprice'=>$renewsplitmarketprice,
            'renewsplitcontractamount'=>$renewsplitcontractamount,
            'lastExtracost'=>$templastExtracost,
        );
    }
    /**
     * 获取可用有效回款
     * @param $params
     * @return array
     */
    public function getRepPirce($params){
        $contractid=$params['contractid'];
        $onecostrenewprice=$params['onecostrenewprice'];//续费总成本
        $onemarketrenewprice=$params['onemarketrenewprice'];//续费总市场价
        $costdeduction=$params['costdeduction'];//总成本
        $rp['unit_price']=$params['unit_price'];//回款金额
        $rp['total']=$params['total'];//合同金额
        $rp['marketprice']=$params['marketprice'];//市场价总价
        $sumextracost=$params['extracost'];//其他扣除成本
        $sumextra_price=$params['extra_price'];//其他成本
        $splitcontractamount=$rp['total']-$onemarketrenewprice;//首购合同金额
        $receivedpayid=$params['receivepayid'];
        $salesorderid=$params['salesorderid'];
        $this->subContractAchSetting($contractid,$receivedpayid,$params['unit_price'],$salesorderid);
        $ContractAchSetting=$this->getContractAchSetting($contractid,$salesorderid);
        $flag=true;
        if(empty($ContractAchSetting)){
            $flag=false;
        }
        if($flag){
            $marketpricesrenew=$ContractAchSetting['marketpricesrenew'];//续费总市场
            $repuntilprice=$ContractAchSetting['repuntilprice'];//续费总额
            $sumextracost+=$ContractAchSetting['extracost'];//成本1
            $sumextra_price+=$ContractAchSetting['othercost'];//成本2
            $diffrenewprice=$repuntilprice-$marketpricesrenew;
            if($diffrenewprice>=0){//说明已经全部算完
                $renewsplitbusinessunit=0;
                $newaddsplitbusinessunit=$rp['unit_price'];
            }else{
                $diffrenewprice=abs($diffrenewprice);
                if($diffrenewprice>=$rp['unit_price']){
                    $renewsplitbusinessunit=$rp['unit_price'];//续费金额
                    $newaddsplitbusinessunit=0;//新购金额
                }else{
                    $renewsplitbusinessunit=$diffrenewprice;//续费金额
                    $newaddsplitbusinessunit=$rp['unit_price']-$diffrenewprice;//新购金额
                }
            }
            $renewsplitcost = $renewsplitbusinessunit*$onecostrenewprice/$onemarketrenewprice;//拆分续费成本
            $buysplitcost=$newaddsplitbusinessunit*($costdeduction-$onecostrenewprice)/$splitcontractamount;//拆分的新单成本
        }else{
            if($rp['unit_price']<=$onemarketrenewprice){
                $renewsplitbusinessunit=$rp['unit_price'];//续费金额
                $newaddsplitbusinessunit=0;//新购金额
                $renewsplitcost = $onecostrenewprice * $renewsplitbusinessunit/$onemarketrenewprice;
                $buysplitcost=0;
            }else{
                $renewsplitbusinessunit=$onemarketrenewprice;//续费金额
                $newaddsplitbusinessunit=$rp['unit_price']-$onemarketrenewprice;//新购金额
                $renewsplitcost = $onecostrenewprice;
                $buysplitcost=$newaddsplitbusinessunit*($costdeduction-$onecostrenewprice)/$splitcontractamount;
            }
        }
        return array('buysplitcost'=>$buysplitcost,
            'renewsplitcost'=>$renewsplitcost,
            'newaddsplitbusinessunit'=>$newaddsplitbusinessunit,
            'splitcontractamount'=>$splitcontractamount,
            'buysplitmarketprice'=>$rp['marketprice']-$onemarketrenewprice,
            'renewsplitbusinessunit'=>$renewsplitbusinessunit
        );
    }

    /**
     * 获取T云的基本信息
     */
    public function getTyunBasicInformation($contractid){
        global $adb;
        $sqls=" SELECT activitytype,orderamount,classtype,productnames,productlife,productname,noseparaterenewmarketprice,noseparaterenewcosttprice,separaterenewmarketprice,separaterenewcosttprice,marketprice,isdirectsellingtoprice,costprice,productid,canrenew,giveterm,buyseparately,onemarketprice,onemarketrenewprice,onecostprice,onecostrenewprice FROM  vtiger_activationcode WHERE contractid = ? AND status IN(0,1) ";
        $results=$adb->pquery($sqls,array($contractid));
        $productdatas =array();
        $productdatas['othermarketprice']=0;//另购产品的续费总市场价  新购续费市场价 *（年限-1）
        $productdatas['othermarketrenewprice']=0;//另购产品的续费总市场价  新购续费市场价 *（年限-1）
        $productdatas['othercostrenewprice']=0;//另购产品的总成本价
        $productdatas['othercostaddprice']=0;//另购产品的首购总成本价  另购首购成本
        $productdatas['renewmarketrenewprice']=0;//续费产品的总成本价  续费市场价*续费年限
        $productdatas['renewcostrenewprice']=0;//续费产品的总成本价
        $productdatas['noseparaterenewmarketprice']=0;//待续费产品的市场价
        $productdatas['noseparaterenewcosttprice']=0;//待续费产品的成本价
        $productdatas['separaterenewmarketprice']=0;//已续费产品的总成本价
        $productdatas['separaterenewcosttprice']=0;//已续费产品的总成本价
        $productdatas['allonemarketprice']=0;//新单累计市场价
        $productdatas['allonemarketrenewprice']=0;//续费累计市场价
        $productdatas['allonecostprice']=0;//新单累计成本
        $productdatas['allonecostrenewprice']=0;//续费累计成本
        while ($dtaRows=$adb->fetch_array($results)){
            $dtaRows['onecostprice']=$dtaRows['onecostprice']<0?0:$dtaRows['onecostprice'];//首购成本价
            $dtaRows['onemarketprice']=$dtaRows['onemarketprice']<0?0:$dtaRows['onemarketprice'];//首购市场价
            $dtaRows['costprice']=$dtaRows['costprice']<0?0:$dtaRows['costprice'];//总成本价
            $dtaRows['onemarketrenewprice']=$dtaRows['onemarketrenewprice']<0?0:$dtaRows['onemarketrenewprice'];//续费市场价
            $dtaRows['onecostrenewprice']=$dtaRows['onecostrenewprice']<0?0:$dtaRows['onecostrenewprice'];//续费成本价
            print_r($dtaRows);

            $productdatas['noseparaterenewmarketprice']=$dtaRows['noseparaterenewmarketprice'];//待续费产品的市场价
            $productdatas['noseparaterenewcosttprice']=$dtaRows['noseparaterenewcosttprice'];//待续费产品的成本价
            $productdatas['separaterenewmarketprice']=$dtaRows['separaterenewmarketprice'];//已续费产品的总成本价
            $productdatas['separaterenewcosttprice']=$dtaRows['separaterenewcosttprice'];//已续费产品的总成本价
            $allonecostrenewpriceyear=$dtaRows['productlife'] - 1;
            $allonecostrenewpriceyear=($allonecostrenewpriceyear<2?1:($allonecostrenewpriceyear-1));
            $productdatas['allonemarketprice']+=$dtaRows['onemarketprice'];//新单累计市场价
            $productdatas['allonemarketrenewprice']+=$dtaRows['onemarketrenewprice'];//续费累计市场价
            $productdatas['allonecostprice']+=$dtaRows['onecostprice'];//新单累计成本
            $productdatas['allonecostrenewprice']+=$dtaRows['onecostrenewprice']*$allonecostrenewpriceyear;//续费累计成本
            $productdatas['productname'].=$dtaRows['productname'].",";
            /*续费场景start**/
            if($dtaRows['classtype'] == 'renew'){
                if($dtaRows['isdirectsellingtoprice']==1){
                    $productdatas['renewmarketrenewprice']+=$dtaRows['marketprice'];
                }else{
                    $productdatas['renewmarketrenewprice']+=$dtaRows['onemarketrenewprice']*($dtaRows['productlife']- $dtaRows['giveterm']);
                }
                $productdatas['renewcostrenewprice']+=$dtaRows['onecostrenewprice']*$dtaRows['productlife'];
            }elseif($dtaRows['classtype'] == 'buy'){
                if($dtaRows['canrenew']){
                    $productdatas['othermarketrenewprice']+=$dtaRows['onemarketrenewprice']*($dtaRows['productlife']-1);
                    $productdatas['othercostrenewprice']+=$dtaRows['onecostrenewprice']*($dtaRows['productlife']-1);
                }
                $productdatas['othercostaddprice']+=$dtaRows['onecostprice'];
                $productdatas['othermarketprice']+=$dtaRows['onemarketprice'];
            }
            /*续费场景end**/
            if($dtaRows['isdirectsellingtoprice']==1){ //是否直销改价
                $productdatas['onemarketrenewprice'] += $dtaRows['onemarketrenewprice'];
                if($dtaRows['classtype'] == 'renew'){
                    $productdatas['onecostrenewprice'] += $dtaRows['onecostrenewprice'];
                }else{
                    if ($dtaRows['canrenew'] || $dtaRows['productid'] > 0) {
                        $productlife = $dtaRows['productlife'] - 1;//如果是续费
                        $productdatas['onecostrenewprice'] += $dtaRows['onecostrenewprice'] * $productlife;
                    } else {
                        $productdatas['onemarketrenewprice'] += 0;
                        $productdatas['onecostrenewprice'] += 0;
                    }
                }

            }else {
                // 不知道是否可以存储是 替换掉 marketprice  T云返回的续费的不是正常的市场价格  但是 替换可能导致前面市场价格不对 以前下单的数据可能要修复
                if ($dtaRows['classtype'] == 'renew') {//如果是续费，将续费的续费市场价*年限-1，作为首购市场价
                    $productdatas['onemarketrenewprice'] += $dtaRows['onemarketrenewprice'];
                    $productdatas['onecostrenewprice'] += $dtaRows['onecostrenewprice'];
                } else {
                    if ($dtaRows['canrenew'] || $dtaRows['productid'] > 0) {
                        $productlife = $dtaRows['productlife'] - 1;//如果是续费
                        $productdatas['onemarketrenewprice'] += $dtaRows['onemarketrenewprice'] * ($productlife - $dtaRows['giveterm']);
                        $productdatas['onecostrenewprice'] += $dtaRows['onecostrenewprice'] * $productlife;
                    } else {
                        $productdatas['onemarketrenewprice'] += 0;
                        $productdatas['onecostrenewprice'] += 0;
                    }

                }
            }

            $productdatas['marketprice']+=$dtaRows['marketprice'];//总市场成本价（首购市场价+续费市场价*（n-1））
            $productdatas['costprice']+=$dtaRows['costprice'];//总成本价（首购成本价+续费成本价*（n-1））

            if($dtaRows['buyseparately']>0){
                $productnames=$dtaRows['productnames'];
                $productnames=htmlspecialchars_decode($productnames);
                $separatelyProducts=json_decode($productnames,true);
                $productdatas['onemarketprice']+=$separatelyProducts[0]['productCount']*$dtaRows['onemarketprice'];
                $productdatas['onecostprice']+=$separatelyProducts[0]['productCount']*$dtaRows['onecostprice'];
                // 如果能续费
                if($separatelyProducts[0]['canRenew']){
                    //升级市场价格取值
                    if($dtaRows['activitytype']=='赠送时间'){
                        $productdatas['upgrademarketprice']+=$dtaRows['marketprice'];
                        $productdatas['upgradecostprice']+=$dtaRows['costprice'];
                    }else{
                        $productdatas['upgrademarketprice']+=($dtaRows['onemarketprice']+$dtaRows['onemarketrenewprice']*($dtaRows['productlife']-1))*$separatelyProducts[0]['productCount'];// 升级用到的市场价格
                        $productdatas['upgradeonecostrenewprice']+=($dtaRows['onemarketrenewprice']*($dtaRows['productlife']-1))*$separatelyProducts[0]['productCount'];// 升级用到的市场价格
                        //升级成本取值
                        $productdatas['upgradecostprice']+=($dtaRows['onecostprice']+$dtaRows['onecostrenewprice']*($dtaRows['productlife']-1))*$separatelyProducts[0]['productCount'];// 升级用到的总成本
                        $productdatas['upgradeonecostrenewprice']+=($dtaRows['onecostprice']+$dtaRows['onecostrenewprice']*($dtaRows['productlife']-1))*$separatelyProducts[0]['productCount'];// 升级用到的总成本
                    }

                    // 不能续费
                }else{
                    //升级市场价格取值
                    if($dtaRows['activitytype']=='赠送时间'){
                        $productdatas['upgrademarketprice']+=$dtaRows['marketprice'];
                        $productdatas['upgradecostprice']+=$dtaRows['costprice'];
                    }else {
                        //升级市场价格取值
                        $productdatas['upgrademarketprice'] += $dtaRows['onemarketprice'] * $separatelyProducts[0]['productCount'];// 升级用到的市场价格
                        //升级成本取值
                        $productdatas['upgradecostprice'] += $dtaRows['onecostprice'] * $separatelyProducts[0]['productCount'];// 升级用到的总成本
                    }
                }
            }else{
                if($dtaRows['activitytype']=='赠送时间'){
                    //升级市场价格取值
                    $productdatas['upgrademarketprice']+=$dtaRows['marketprice'];// 升级用到的市场价格
                    //升级成本取值
                    $productdatas['upgradecostprice']+=$dtaRows['costprice'];// 升级用到的总成本
                }else{
                    //升级市场价格取值
                    $productdatas['upgrademarketprice']+=$dtaRows['onemarketprice']+$dtaRows['onemarketrenewprice']*($dtaRows['productlife']-1);// 升级用到的市场价格
                    //升级成本取值
                    $productdatas['upgradecostprice']+=$dtaRows['onecostprice']+$dtaRows['onecostrenewprice']*($dtaRows['productlife']-1);// 升级用到的总成本
                }
                $productdatas['onemarketprice']+=$dtaRows['onemarketprice'];
                $productdatas['onecostprice']+=$dtaRows['onecostprice'];

            }
        }
        return $productdatas;
    }

    /**前三后三时间段内计算
     * @param $params
     * @return array
     */
    public function calcTYUNFirstANDLastThreeMonths($params){
        $type=0;
        if(($params['effectiveTotal']/$params['marketprice'])<0.75){
            //$arriveachievement=0;
            $remark="T云非升级 前三后个月内，合同金额除以市场价 小于0.75"."合同金额".$params['effectiveTotal']."市场价格".$params['marketprice'];
            $renewarriveachievement=0;
            $newaddarriveachievement=0;
            $type=1;
        }else{
            $remark='前三后三个月内，';

            $waitarriveachievement=$params['noseparaterenewmarketprice']-$params['noseparaterenewcosttprice']-$params['separaterenewmarketprice']+$params['separaterenewcosttprice'];//续费业绩=待续费市场价-待续费成本-（已续费的市价-已续费的成本）
            $remark.='原单业绩'.$waitarriveachievement;
            $waitarriveachievement=$waitarriveachievement>0?$waitarriveachievement:0;
            if($params['effectiveTotal']>=$params['marketprice']){
                $currentarriveachievement=$params['effectiveTotal']-$params['costdeduction'];//总业绩
                if($currentarriveachievement<=$waitarriveachievement){
                    $renewarriveachievement=$currentarriveachievement;
                    $newaddarriveachievement=0;
                    $type=1;
                }else{
                    $renewarriveachievement=$waitarriveachievement;
                    $newaddarriveachievement=$currentarriveachievement-$waitarriveachievement;
                };
            }else{
                $currentarriveachievement=$params['effectiveTotal']*$params['effectiveTotal']/$params['marketprice']-$params['costdeduction'];//总业绩
                if($currentarriveachievement<=$waitarriveachievement){
                    //$renewarriveachievement=$currentarriveachievement*$params['effectiveTotal']/$params['marketprice'];
                    $renewarriveachievement=$currentarriveachievement;
                    $newaddarriveachievement=0;
                    $type=1;
                }else{
                    $renewarriveachievement=$waitarriveachievement;
                    $newaddarriveachievement=$currentarriveachievement-$waitarriveachievement;
                };
            }
        }
        return array('renewarriveachievement'=>$renewarriveachievement*$params['unit_price']/$params['effectiveTotal'],
            'newaddarriveachievement'=>$newaddarriveachievement*$params['unit_price']/$params['effectiveTotal'],
            "waitsubarriveachievement"=>$params['noseparaterenewmarketprice']-$params['noseparaterenewcosttprice'],
            "alreadyarriveachievement"=>$params['separaterenewmarketprice']-$params['separaterenewcosttprice'],
            "remark"=>$remark,
            'type'=>$type
        );
    }
    public function test(){
        ini_set('display_errors','on'); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        $recordModel=Vtiger_Record_Model::getCleanInstance('TyunWebBuyService');
        $recordModel->writeExpireRenewPriceAndCostPrice(array('usercode'=>$_REQUEST['usercode'],'contractid'=>$_REQUEST['cid']));
    }


    /**
     * 根据回款id  批量匹配后进入到排行榜表
     * @param $receivedpaymentsid
     */
    public function matchToRanking($receivedpaymentsid,$isSkip=false){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select receivedpaymentownid,achievementmonth,achievementtype from vtiger_achievementallot_statistic where receivedpaymentsid=?",array($receivedpaymentsid));
        if(!$db->num_rows($result)){
            return;
        }
//        require('crmcache/departmentanduserinfo.php');
//        global $zhongxiaodepartment;
//        $userIds = explode(",",$user2departmentinfo[$zhongxiaodepartment]);
//        $achievementMonth = !empty($achievementMonth) ? $achievementMonth : date("Y-m");
        while ($row = $db->fetchByAssoc($result)){
//            if(!in_array($row['receivedpaymentownid'],$userIds)){
//                continue;
//            }
            if($row['achievementtype']=='renew'){
                continue;
            }
            $this->receivePaymentsRanking($row['receivedpaymentownid'],$receivedpaymentsid,$row['achievementmonth'],'add',$isSkip);
        }
    }

    /**
     * 匹配后 进入排行榜
     *
     * @param $userId
     * @param $receivedpaymentsid
     * @param $achievementMonth
     * @param string $type
     * @param bool $isSkip
     */
    public function receivePaymentsRanking($userId,$receivedpaymentsid,$achievementMonth,$type='add',$isSkip=false){
        require('crmcache/departmentanduserinfo.php');
        global $zhongxiaodepartment;
        $userIds = explode(",",$user2departmentinfo[$zhongxiaodepartment]);
        if(!in_array($userId,$userIds)){
            return;
        }

        if($this->isZhongXiaoManager($userId)){
            return;
        }

        if(!$this->isShangWu($userId)){
            return;
        }

        $statData = $this->statReceivePayments($userId,$receivedpaymentsid,$achievementMonth,$type);
        if(empty($statData)){
            return;
        }
        $userRecordModel = Users_Record_Model::getCleanInstance("Users");
        $params = array(
            'userid'=>$userId,
            'collectamount'=>$statData['collectamount'],
            'completednumber'=>$statData['completednumber'],
            'departmentid'=>$userRecordModel->getDepartmentIdById($userId),
            'datamonth' => $achievementMonth
        );
        if(!$this->isInReceivePaymentsRanking($params['userid'],$achievementMonth) && $type=='add'){
            $this->insertReceivePaymentsRanking($params);
        }else{
            $this->updateReceivePaymentsRanking($params,$type);
        }
        $this->changeTopTen($params['userid'],$params['collectamount'],$achievementMonth,$isSkip);
    }

    public function statCompleteNumber($userId,$achievementMonth,$type,$receivedpaymentsid){
        $db = PearDatabase::getInstance();
        $sql1 = "select achievementallotid,receivedpaymentsid,servicecontractid from vtiger_achievementallot_statistic where  receivedpaymentownid=? AND achievementmonth=?  and is_deduction=0  and achievementtype='newadd'";
        $result = $db->pquery($sql1,array($userId,$achievementMonth));
        $achievementallotid= array();
        $passAchievementallotid = array();
        $servicecontractid = array();
        if(!$db->num_rows($result)){
            return 0;
        }
        while ($row = $db->fetchByAssoc($result)){
            $achievementallotid[] = $row['achievementallotid'];
            $servicecontractid[] = $row['servicecontractid'];
        }
        $achievementallotid = array_unique($achievementallotid);
        $servicecontractid = array_unique($servicecontractid);

        $result2 = $db->pquery("select achievementallotid,achievementmonth from vtiger_achievementallot_statistic where receivedpaymentownid=? and servicecontractid in (".implode(",",$servicecontractid).")  and is_deduction=0  and achievementtype='newadd' group by servicecontractid",array($userId));
        $numRow2 = $db->num_rows($result2);
        if(!$numRow2){
            return 0;
        }

        while ($row2 = $db->fetchByAssoc($result2)){
            if(date("Y-m",strtotime($row2['achievementmonth']))==$achievementMonth){
                $lastAchievementallotid[] = $row2['achievementallotid'];
            }
        }
        if(!count($lastAchievementallotid)){
            return 0;
        }

        $sql3 = "select sum(scalling/100) as completednumber from vtiger_achievementallot_statistic where  receivedpaymentownid=?  and achievementmonth=?  and is_deduction=0  and achievementtype='newadd' and achievementallotid in(".implode(",",$lastAchievementallotid).")";
        if($type=='cancel'){
            $sql3 .= ' and receivedpaymentsid!='.$receivedpaymentsid;
        }
        $result3 = $db->pquery($sql3,array($userId,$achievementMonth));
        if(!$db->num_rows($result3)){
            return 0;
        }
        $row3 = $db->fetchByAssoc($result3,0);
        return $row3['completednumber'];
    }

    /**
     * 统计商务本月回款金额、回款次数
     * @param $userId
     * @param $receivedpaymentsid
     * @param $achievementMonth
     * @param $type
     * @return array
     */
    public function statReceivePayments($userId,$receivedpaymentsid,$achievementMonth,$type){
        $db = PearDatabase::getInstance();
        $sql = "select b.firstreceivepaydate,a.achievementmonth,a.scalling,a.servicecontractid from vtiger_achievementallot_statistic a left join vtiger_servicecontracts b on a.servicecontractid=b.servicecontractsid where  a.receivedpaymentownid=? and a.receivedpaymentsid=? and a.achievementtype='newadd'";
        $result =$db->pquery($sql,array($userId,$receivedpaymentsid));
        if(!$db->num_rows($result)){
            return array();
        }
        $row = $db->fetchByAssoc($result,0);
        if(!$row['firstreceivepaydate'] || in_array($type,array('cancel'))){
//        if(!$row['firstreceivepaydate'] || ($row['firstreceivepaydate'] && $row['firstreceivepaydate']== $row['matchdate']) || in_array($type,array('cancel'))){
            //首次匹配 计算完成单量 完成金额
            $sql = "select sum(effectiverefund) as collectamount,sum(scalling/100) as completednumber from vtiger_achievementallot_statistic where  receivedpaymentownid=?  and achievementmonth=? and is_deduction=0 and achievementtype='newadd'";
        }else{
            //非首次匹配 只计算完成金额
            $sql = "select sum(effectiverefund) as collectamount,0 as completednumber from vtiger_achievementallot_statistic where  receivedpaymentownid=?  and achievementmonth=?   and is_deduction=0  and achievementtype='newadd'";
        }

        if($type=='cancel'){
            $sql .= ' and receivedpaymentsid !='.$receivedpaymentsid;
        }

        $completedNumber = $this->statCompleteNumber($userId,$achievementMonth,$type,$receivedpaymentsid);

        $result = $db->pquery($sql,array($userId,$achievementMonth));
        $row = $db->fetchByAssoc($result,0);
        return array(
            'collectamount'=>$row['collectamount'],
            'completednumber'=>(floatval($completedNumber)<0) ? 0 :$completedNumber
        );

    }

    /**
     * 是否在回款排行表中
     * @param $userId
     * @return bool
     */
    public function isInReceivePaymentsRanking($userId,$achievementMonth){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select 1 from vtiger_receivedpaymentsranking where userid=? and datamonth=?",array($userId,$achievementMonth));
        if($db->num_rows($result)){
            return true;
        }
        return false;
    }


    /**
     * 插入或修改回款排行表记录
     * @param $params
     * @return bool
     */
    public function insertReceivePaymentsRanking($params){
        $data = array(
            $params['userid'],
            $params['departmentid'],
            $params['completednumber'],
            $params['collectamount'],
            date("Y-m-d H:i:s"),
            $params['datamonth']
        );
        $db  = PearDatabase::getInstance();
        $sql = "insert into vtiger_receivedpaymentsranking (`userid`,`departmentid`,`completednumber`,`collectamount`,`lastupdateat`,`datamonth`) values (?,?,?,?,?,?)";
        $result =$db->pquery($sql,$data);
        return true;
    }


    /**
     *修改回款排行表记录
     * @param $params
     * @return bool
     */
    public function updateReceivePaymentsRanking($params,$type){
        $db  = PearDatabase::getInstance();
        if($params['completednumber'] || in_array($type,array('cancel'))){
            $data = array(
                $params['completednumber'],
                $params['collectamount'],
                date("Y-m-d H:i:s"),
                $params['userid'],
                $params['datamonth']
            );
            $sql = "update vtiger_receivedpaymentsranking set completednumber=?,collectamount=?,lastupdateat=? where userid=? and datamonth=?";
        }else{
            $data = array(
                $params['collectamount'],
                date("Y-m-d H:i:s"),
                $params['userid'],
                $params['datamonth']
            );
            $sql = "update vtiger_receivedpaymentsranking set collectamount=?,lastupdateat=? where userid=? and datamonth=?";
        }
        $result =$db->pquery($sql,$data);
        return true;
    }


    /**
     * 排行变动 top10记录表变更
     * @param $userId
     * @param $collectAmount
     * @param $achievementMonth
     */
    public function changeTopTen($userId,$collectAmount,$achievementMonth,$isSkip=false){
        $db = PearDatabase::getInstance();
        $dataMonth  = $achievementMonth;
        $lastUpdateAt = date("Y-m-d H:i:s");
        $rankNum = $this->rankNum($collectAmount,$userId,$achievementMonth);
        $myRankData = $this->myRankData($userId,$achievementMonth);
        if($this->isInTopTen($userId,$achievementMonth)){
            $db->pquery("update vtiger_receivedpaymentstopten set lastupdateat=?,collectamount=?,rank=? where userid=? and datamonth=?",array($lastUpdateAt,$collectAmount,$rankNum,$userId,$dataMonth));
            if($rankNum<$myRankData['rank']){
                $db->pquery("update vtiger_receivedpaymentstopten set lastupdateat=?,rank=rank+1 where userid!=? and datamonth=? and rank>=? and rank<?",array($lastUpdateAt,$userId,$dataMonth,$rankNum,$myRankData['rank']));
            }elseif ($rankNum>$myRankData['rank']){
                $db->pquery("update vtiger_receivedpaymentstopten set lastupdateat=?,rank=rank-1 where userid!=? and datamonth=? and rank>? and rank<=?",array($lastUpdateAt,$userId,$dataMonth,$myRankData['rank'],$rankNum));
            }
        }else{
            $db->pquery("update vtiger_receivedpaymentstopten set rank=rank+1 where rank>=? and  datamonth=?", array($rankNum, $dataMonth));
            //获取记录表的id
            $rankResult =$db->pquery("select receivedpaymentsrankingid from vtiger_receivedpaymentsranking where userid=? and datamonth=? limit 1" ,array($userId,$dataMonth));
            $data = $db->fetchByAssoc($rankResult,0);
            $db->pquery("insert into vtiger_receivedpaymentstopten (`receivedpaymentsrankingid`,`userid`,`collectamount`,`lastupdateat`,`datamonth`,`rank`) values (?,?,?,?,?,?)",array($data['receivedpaymentsrankingid'],$userId,$collectAmount,$lastUpdateAt,$dataMonth,$rankNum));
        }
        if(floatval($collectAmount)<=0){
            $db->pquery("delete from vtiger_receivedpaymentstopten where userid=? and datamonth=?",array($userId,$dataMonth));
            $db->pquery("delete from vtiger_receivedpaymentsranking where userid=? and datamonth=?",array($userId,$dataMonth));
        }

        //排行变动发送排行变动消息
        if(false && $this->isSendRankChangeWxMsg($isSkip,$rankNum,$myRankData['rank'],$achievementMonth)) {
            $this->sendRankChangeWxMsg();
        }
    }

    /**
     * 是否发送排行榜变动消息
     *
     * @param $isSkip
     * @param $rankNum
     * @param $myRank
     * @param $achievementMonth
     * @return bool
     */
    public function isSendRankChangeWxMsg($isSkip,$rankNum,$myRank,$achievementMonth){
        if(!$isSkip && $rankNum!=$myRank && (($rankNum >0 && $rankNum<=10 )|| ($myRank>0 && $myRank<=10)) && $achievementMonth==date("Y-m")){
            return true;
        }
        return false;
    }

    /**
     * 是否已进入排行榜表中
     * @param $userId
     * @param $achievementMonth
     * @return bool
     */
    public function isInTopTen($userId,$achievementMonth){
        $db = PearDatabase::getInstance();
        $result =$db->pquery("select 1 from vtiger_receivedpaymentstopten where userid=? and datamonth=?",array($userId,$achievementMonth));
        if($db->num_rows($result)){
            return true;
        }
        return false;
    }

    /**
     * 能否进入排行旁表
     *
     * @param $userId
     * @param $collectAmount
     * @return bool
     */
    public function canInTopTen($userId,$collectAmount){
        $dataMonth = date("Y-m");
        $db = PearDatabase::getInstance();
        $result =$db->pquery("select collectamount from vtiger_receivedpaymentstopten where userid=? and datamonth=? and rank=10 limit 1",array($userId,$dataMonth));
        if(!$db->num_rows($result)){
            return true;
        }
        $row = $db->fetchByAssoc($result,0);
        if(floatval($collectAmount)>floatval($row['collectamount'])){
            return true;
        }
        return false;
    }

    /**
     * 商务在排行前十榜单中的可插入的位置
     * @param $collectAmount
     * @param $userId
     * @return int
     */
    public function rankNum($collectAmount,$userId,$achievementMonth){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select count(1) as ranknum from vtiger_receivedpaymentstopten where datamonth=? and collectamount>=? and userid !=?",array($achievementMonth,floatval($collectAmount),$userId));
        $row = $db->fetchByAssoc($result,0);
        return $row['ranknum']+1;
    }

    /**
     * 用户当前月的排行表中的信息
     *
     * @param $userId
     * @return array
     */
    public function myRankData($userId,$achievementMonth){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select rank,collectamount from vtiger_receivedpaymentstopten where datamonth=? and userid=?",array($achievementMonth,$userId));
        if(!$db->num_rows($result)){
            return array(
                'rank'=>0,
                'collectamount'=>0
            );
        }
        $row = $db->fetchByAssoc($result,0);
        return $row;
    }

    /**
     * 发送微信消息 商务排名调用
     */
    public function sendRankChangeWxMsg(){
        global $zhongxiaodepartment,$m_crm_url,$isDev;
        $content = '中小商务排行榜又更新啦，赶紧进入查看您的排名是不是又上升啦';
        $userRecordModel = Users_Record_Model::getCleanInstance("Users");
        $allEmail =$userRecordModel->getAllEmailsByDepartmentId($zhongxiaodepartment);
        $dataUrl = $m_crm_url.'/index.php?module=IncomeRank&action=index#/rank';
        $picurl = $m_crm_url.'/resources/paihangpicurl.png';
        $email = '';
        foreach ($allEmail as $all){
            $email .= $all['mail'].'|';
        }
        if($isDev){
            $email = 'jingjing.li@71360.com|junwei.nie@71360.com';
        }
        $this->sendWechatMessage(array('email'=>trim($email),'picurl'=>$picurl,'description'=>$content,'dataurl'=>$dataUrl,'title'=>'排名更新','flag'=>12,'ERPDOIT'=>456321));
    }

    /**
     * 获取可查看的部门
     *
     * @param $myDepartmentId
     * @param bool $isShow
     * @return array
     */
    public function departmentData($myDepartmentId,$departmentinfo,$departmenttoparent,$cachedepartment,$isShow=false){
        global $zhongxiaodepartment;
        $departmentIds = $departmentinfo[$zhongxiaodepartment];
        global $current_user;
        $matchReceiveModuleRecord = Matchreceivements_Module_Model::getCleanInstance("Matchreceivements");
        if($matchReceiveModuleRecord->exportGrouprt('Matchreceivements','chooseRank',$current_user->id) || in_array($current_user->roleid,array("H2",'H78'))){
            $myDepartmentId = $zhongxiaodepartment;
            $isShow = true;
        }
        $canDepartment  =  $departmentinfo[$myDepartmentId];
        $parentMine = explode("::",$departmenttoparent[$myDepartmentId]);
        foreach ($departmentIds as $departmentId){
            if($departmentId==$zhongxiaodepartment){
                continue;
            }
            if(in_array($departmentId,$canDepartment)  || in_array($departmentId,$parentMine) || $isShow){
                $parentDepartment = explode("::",$departmenttoparent[$departmentId]);
                $parent = $parentDepartment[count($parentDepartment)-2];
                $data[] = array(
                    "name"=>$departmentId,
                    "value"=>$cachedepartment[$departmentId],
                    "parent"=> ($parent==$zhongxiaodepartment ? '' : $parent),
                );
            }
        }
        return $data;
    }

    /**
     * 排行榜前三名的信息
     * @return array
     */
    public function frontRank(){
        $day = date("d");
        $datamonth = date("Y-m");
        if(intval($day)<=3){
            $datamonth = date("Y-m",strtotime("-1 month"));
        }
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select a.userid,b.last_name,c.picturepath,a.collectamount,a.completednumber,d.rank from vtiger_receivedpaymentsranking a left join vtiger_users b on a.userid=b.id left join vtiger_wexinpicture c on a.userid=c.userid left join vtiger_receivedpaymentstopten d on d.receivedpaymentsrankingid=a.receivedpaymentsrankingid where  a.datamonth=? and a.collectamount>0 order by d.rank limit 3",array($datamonth));
        if(!$db->num_rows($result)){
            return array(array());
        }
        while ($row=$db->fetchByAssoc($result)){
            $data[] = array(
                "userId"=>intval($row['userid']),
                "userName"=>$row['last_name'],
                "avatar"=>$row['picturepath'],
                "completedNumber"=>$row['completednumber'],
                "collectAmount"=>$row['collectamount'],
                "rank"=>intval($row['rank'])
            );
        }
        return $data;
    }

    /**
     * 获取本月本部门已进入排行列表的商务id
     *
     * @param $departmentId
     * @param $datamonth
     * @return array
     */
    public function getMatchedUserIds($departmentId,$datamonth,$user2departmentinfo){
        $userIds = $user2departmentinfo[$departmentId];
        $db = PearDatabase::getInstance();
        $result =$db->pquery("select a.userid from vtiger_receivedpaymentsranking a left join vtiger_receivedpaymentstopten b on a.receivedpaymentsrankingid=b.receivedpaymentsrankingid where a.datamonth=? and a.userid in (".$userIds.")",array($datamonth));
        if(!$db->num_rows($result)){
            return array();
        }
        $data = array();
        while ($row = $db->fetchByAssoc($result)){
            $data[] = $row['userid'];
        }
        return $data;
    }

    /**
     * 获取个人的排行榜信息
     *
     * @param $userId
     * @return array
     */
    public function mineRank($userId,$user2departmentinfo){
        global $current_user,$zhongxiaodepartment;
        $zhongxiaoUserIds = explode(",",$user2departmentinfo[$zhongxiaodepartment]);
        if($this->isZhongXiaoManager() || !in_array($current_user->id,$zhongxiaoUserIds)){

            return array();
        }
        $day = date("d");
        $datamonth = date("Y-m");
        if(intval($day)<=3){
            $datamonth = date("Y-m",strtotime("-1 month"));
        }
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select a.userid,b.last_name,a.collectamount,a.completednumber,c.rank from vtiger_receivedpaymentsranking a left join vtiger_users b on a.userid=b.id  left join vtiger_receivedpaymentstopten c on a.receivedpaymentsrankingid=c.receivedpaymentsrankingid where b.status='Active' and  a.datamonth=? and a.userid=?",array($datamonth,$userId ));
        if(!$db->num_rows($result)){
            $completednumber=0;
            $collectamount=0;
            $result =$db->pquery("select user_entered from vtiger_users where id=? limit 1",array($userId));
            $userData = $db->fetchByAssoc($result,0);
            $ids = array_diff($zhongxiaoUserIds,$this->getMatchedUserIds($zhongxiaodepartment,$datamonth,$user2departmentinfo));
            $matchNum = $this->getMatchNum($datamonth);
            $result2 = $db->pquery("select count(1) as num from vtiger_users a left join vtiger_user2role b on a.id=b.userid where a.status='Active' and a.id in(".implode(",",$ids).") and a.user_entered<=? and b.roleid in ('".implode("','",$this->zhongxiaoRoleIds())."')",array($userData['user_entered']));
            $numData = $db->fetchByAssoc($result2,0);
            $rank=($matchNum+$numData['num']);
        }else{
            $row = $db->fetchByAssoc($result,0);
            $completednumber=$row['completednumber'];
            $collectamount=$row['collectamount'];
            $rank=$row['rank'];
        }

        return array(
            "userId"=>intval($userId),
            "userName"=>$current_user->last_name,
            "completedNumber"=>$completednumber,
            "collectAmount"=>$collectamount,
            "rank"=>$rank
        );
    }

    /**
     * 移动端排行榜基础信息
     *
     * @param Vtiger_Request $request
     * @return array
     */
    public function rankInfo(Vtiger_Request $request){
        $userId = $request->get("userid");
        global $current_user,$zhongxiaodepartment;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($userId);
        $userRecordModel = Users_Record_Model::getCleanInstance("Users");
//        $myDepartmentId = $current_user->departmentid;
        $myDepartmentId = $userRecordModel->getDepartmentIdById($userId);
        if(!$myDepartmentId){
            $myDepartmentId=$zhongxiaodepartment;
        }
        $dataMonth = date("Y-m");
        $day = date("d");
        if(intval($day)<=3 ){
            $dataMonth = date("Y-m",strtotime("-1 month"));
        }

        $isZhongXiaoManager = $this->isZhongXiaoManager();
        include "crmcache/departmentanduserinfo.php";
        $mineRank = $this->mineRank($userId,$user2departmentinfo);
        $data = array(
            'success'=>true,
            'msg'=>'获取成功',
            'rankFront'=>$this->frontRank(),
            'department'=>$this->departmentData($myDepartmentId,$departmentinfo,$departmenttoparent,$cachedepartment),
            'mineRank'=>array($mineRank),
            "canFilter"=> 1,
            "showNoIncome"=>$isZhongXiaoManager ? 1 : 0,
            "minFilterNum"=>intval($this->minFilterNum($myDepartmentId,$departmenttoparent)),
            "topTenList"=>$this->getRankList($zhongxiaodepartment,$dataMonth,0,0,10,$departmentinfo,$user2departmentinfo)

        );
        return $data;
    }

    public function minFilterNum($myDepartmentId,$departmenttoparent){
        global $current_user;
        $matchReceiveModuleRecord = Matchreceivements_Module_Model::getCleanInstance("Matchreceivements");
        if($matchReceiveModuleRecord->exportGrouprt('Matchreceivements','chooseRank',$current_user->id)){
            return 0;
        }
        $parentDepartmentIds = explode("::",$departmenttoparent[$myDepartmentId]);
        $fileterNum = count($parentDepartmentIds)-2;
        return $fileterNum<1 ? 0 : $fileterNum;
    }

    public function isZhongXiaoManager($userId=0){
        if($userId){
            $db = PearDatabase::getInstance();
            $result =$db->pquery("select b.roleid from vtiger_users a left join vtiger_user2role b on a.id=b.userid where a.id=?",array($userId));
            if(!$db->num_rows($result)){
                return false;
            }
            $row = $db->fetchByAssoc($result,0);
            return in_array($row['roleid'],self::$ZHONGXIAOMANAGER);
        }
        $matchReceiveModuleRecord = Matchreceivements_Module_Model::getCleanInstance("Matchreceivements");
        global $current_user;
        if(in_array($current_user->roleid,self::$ZHONGXIAOMANAGER) ||
            $matchReceiveModuleRecord->exportGrouprt('Matchreceivements','chooseRank',$current_user->id) ||
            $current_user->is_admin=='on'){
            return true;
        }
        return false;
    }

    /**
     * 获取本月已匹配的人数
     * @param $dataMonth
     * @return mixed
     */
    public function getMatchNum($dataMonth){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select count(1) as matchNum from vtiger_receivedpaymentsranking where datamonth=?",array($dataMonth));
        $row = $db->fetchByAssoc($result,0);
        return$row['matchNum'];
    }

    public function getRankNum($department,$dataMonth,$type,$user2departmentinfo,$departmentinfo){
        $db = PearDatabase::getInstance();
        $departmentIds = $departmentinfo[$department];
        if($type==0){
            $result =$db->pquery("select count(1) as num from vtiger_receivedpaymentsranking a left join vtiger_receivedpaymentstopten b on a.receivedpaymentsrankingid=b.receivedpaymentsrankingid left join vtiger_users c on a.userid=c.id where  a.datamonth=? and a.departmentid in('".implode("','",$departmentIds)."') ",array($dataMonth));
        }else{
            $orderedUserIds = $this->getMatchedUserIds($department,$dataMonth,$user2departmentinfo);
            $zhongxiaoUserIds = explode(",",$user2departmentinfo[$department]);
            $userIds = array_diff($zhongxiaoUserIds,$orderedUserIds);
            $result = $db->pquery("select count(1) as num from vtiger_users a left join vtiger_user2role b on a.id=b.userid where a.personnelpositionid=10071 and b.roleid in ('".implode("','",$this->zhongxiaoRoleIds())."') and a.status='Active' and a.id in(".implode(",",$userIds).") ",array());
        }
        if(!$db->num_rows($result)){
            return 0;
        }
        $row = $db->fetchByAssoc($result,0);
        return $row['num'];
    }

    public function getRankList($department,$dataMonth,$type,$pageNo,$size,$departmentinfo,$user2departmentinfo){
        $departmentIds = $departmentinfo[$department];
        $db = PearDatabase::getInstance();
        if($type==0){
            $result =$db->pquery("select a.userid,c.last_name,a.completednumber,a.collectamount,b.rank from vtiger_receivedpaymentsranking a left join vtiger_receivedpaymentstopten b on a.receivedpaymentsrankingid=b.receivedpaymentsrankingid left join vtiger_users c on a.userid=c.id where  a.datamonth=? and a.departmentid in('".implode("','",$departmentIds)."') and a.collectamount>0 order by rank limit ".($pageNo*$size).",".$size."",array($dataMonth));
        }else{
            $orderedUserIds = $this->getMatchedUserIds($department,$dataMonth,$user2departmentinfo);
            $zhongxiaoUserIds = explode(",",$user2departmentinfo[$department]);
            $userIds = array_diff($zhongxiaoUserIds,$orderedUserIds);
            $result = $db->pquery("select a.id as userid,a.last_name,0 as completednumber,0 as collectamount from vtiger_users a left join vtiger_user2role b on a.id=b.userid where a.personnelpositionid=10071 and b.roleid in ('".implode("','",$this->zhongxiaoRoleIds())."') and a.status='Active' and a.id in(".implode(",",$userIds).")  order by a.user_entered,a.id limit ".($pageNo*$size).",".$size."",array());
        }
        if(!$db->num_rows($result)){
            return array(array());
        }
        while ($row = $db->fetchByAssoc($result)){
            $data[] = array(
                "userId"=>intval($row['userid']),
                "userName"=>$row['last_name'],
                "completedNumber"=>$row['completednumber'],
                "collectAmount"=>$row['collectamount'],
                "rank"=>intval($row['rank'])
            );
        }
        return $data;
    }

    /**
     * 可查看的中小角色 中小商务  商务主管
     * @return array
     */
    public function zhongxiaoRoleIds(){
        global $zhongxiaoshangwuroleid,$zhongxiaoshangwuzhuguanroleid;
        return array(
            $zhongxiaoshangwuroleid,$zhongxiaoshangwuzhuguanroleid
        );
    }

    /**
     * 排行榜列表
     *
     * @param Vtiger_Request $request
     * @return array
     */
    public function rankList(Vtiger_Request $request){
        $dataYear = $request->get("dataYear")?$request->get("dataYear"):date("Y");
        $dataMonth = $request->get("dataMonth")?$request->get("dataMonth"):date("m");
        $day = date("d");
        if(intval($day)<=3 && !$request->get("dataYear") && !$request->get("dataMonth")){
            $dataMonth = date("m",strtotime("-1 month"));
        }
        if(intval($dataMonth)<10){
            $dataMonth = '0'.intval($dataMonth);
        }

        $department = $request->get("department")?$request->get("department"):'H3';
        $type = $request->get("type")?$request->get("type"):0;
        $pageNo = $request->get("pageNo")?$request->get("pageNo"):0;
        $size = $request->get("size")?$request->get("size"):10;
        $userId =$request->get("userid");
        global $current_user,$zhongxiaodepartment;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($userId);
        include "crmcache/departmentanduserinfo.php";

        $matchReceiveModuleRecord = Matchreceivements_Module_Model::getCleanInstance("Matchreceivements");
        if(!$request->get("department") && !$matchReceiveModuleRecord->exportGrouprt('Matchreceivements','chooseRank',$current_user->id)){
            $userRecordModel = Users_Record_Model::getCleanInstance("Users");
            $department = $userRecordModel->getDepartmentIdById($userId);
        }
        if($type=='-1'){
            $rankList = $this->getRankList($zhongxiaodepartment,$dataYear.'-'.$dataMonth,0,0,10,$departmentinfo,$user2departmentinfo);
            $total = 10;
        }else{
            $rankList = $this->getRankList($department,$dataYear.'-'.$dataMonth,$type,$pageNo,$size,$departmentinfo,$user2departmentinfo);
            $total =$this->getRankNum($department,$dataYear.'-'.$dataMonth,$type,$user2departmentinfo,$departmentinfo);
        }
        return array('success'=>true,'list'=>$rankList,'total'=>$total,'msg'=>'获取成功');
    }

    /**
     * 取消回款后更新排行榜
     * @param $recordId
     */
    public function cancelMatchUpdateRank($recordId){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select receivedpaymentownid,achievementmonth,achievementtype from vtiger_achievementallot_statistic where receivedpaymentsid=? ",array($recordId));
        if(!$db->num_rows($result)){
            return;
        }
        require('crmcache/departmentanduserinfo.php');
        global $zhongxiaodepartment;
        $userIds = explode(",",$user2departmentinfo[$zhongxiaodepartment]);
        while ($row = $db->fetchByAssoc($result)){
            if(!in_array($row['receivedpaymentownid'],$userIds) || !$row['achievementmonth'] || $row['achievementtype']=='renew'){
                continue;
            }
            $this->receivePaymentsRanking($row['receivedpaymentownid'],$recordId,$row['achievementmonth'],'cancel');
        }
    }

    public function isShangWu($userId){
        $db=PearDatabase::getInstance();
        $result = $db->pquery("select 1 from vtiger_users a left join vtiger_user2role b on a.id=b.userid where a.personnelpositionid=10071 and b.roleid in ('".implode("','",$this->zhongxiaoRoleIds())."')  and a.id=?",array($userId));
        if($db->num_rows($result)){
            return true;
        }
        return false;
    }

}
