<?php
$dir= __DIR__;
$dir=rtrim($dir,'/cron');
ini_set("include_path", $dir);
//ini_set("include_path", "../");
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");
error_reporting(0);
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
set_time_limit(0);
global $adb;
// 定时修改工单类没有获取绩效的订单然后计算绩效 并更新绩效。 定时每天会执行几次吧
//$query=" SELECT so.performanceoftime,aas.matchdate,aas.reality_date,aas.achievementallotid,aas.scalling,aas.servicecontractid ,aas.receivedpaymentsid,aas.shareuser,s.contract_no,s.total,r.unit_price,s.productid ,s.extraproductid,s.servicecontractstype FROM vtiger_achievementallot_statistic as aas LEFT JOIN  vtiger_servicecontracts as s ON s.servicecontractsid=aas.servicecontractid  LEFT JOIN  vtiger_receivedpayments as r ON r.receivedpaymentsid=aas.receivedpaymentsid LEFT JOIN  vtiger_salesorder as so ON so.servicecontractsid=aas.servicecontractid WHERE   servicecontractid= 198994 ";
$query=" SELECT aas.receivedpaymentownid,aas.achievementtype,s.multitype,so.performanceoftime,aas.matchdate,aas.reality_date,aas.achievementallotid,aas.scalling,aas.servicecontractid ,aas.receivedpaymentsid,aas.shareuser,s.contract_no,s.total,r.unit_price,s.productid,s.extraproductid,s.servicecontractstype,s.parent_contracttypeid,s.contract_type,s.contract_no FROM vtiger_achievementallot_statistic as aas LEFT JOIN  vtiger_servicecontracts as s ON s.servicecontractsid=aas.servicecontractid  LEFT JOIN  vtiger_receivedpayments as r ON r.receivedpaymentsid=aas.receivedpaymentsid LEFT JOIN  vtiger_salesorder as so ON so.servicecontractsid=aas.servicecontractid WHERE  aas.isover=0  AND  (aas.achievementmonth  IS NULL OR aas.achievementmonth='') AND so.performanceoftime IS NOT NULL ORDER BY achievementallotid ASC ";
$result = $adb->run_query_allrecords($query);
$yikouchengben=array();
foreach($result as $row){
    //院校版不生成业绩  start  ac.status IN(0,1)
    $collegeedition=$adb->pquery("SELECT  iscollegeedition  FROM vtiger_activationcode WHERE  contractid=? AND status IN(0,1) AND iscollegeedition=1 ",array($row['servicecontractid']));
    //如果是院校版订单则不生成业绩
    if($adb->num_rows($collegeedition)>0){
        $paramers['contract_no']=$row['contract_no'];
        $paramers['marks']='院校版则不生成业绩。工单脚本';
        $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
        $Matchreceivements_Record_Model->noCalculationAchievementRecord($paramers);
        continue;
    }
    // 做个过滤处理 如果是 搜索引擎类型google竞价合同  则不再生成业绩
    if($row['parent_contracttypeid']==4 && $row['contract_type']=='GOOGLE竞价合同'){
        $paramers['contract_no']=$row['contract_no'];
        $paramers['marks']='搜索引擎类型google竞价合同。工单脚本';
        $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
        $Matchreceivements_Record_Model->noCalculationAchievementRecord($paramers);
        continue;
    }
    //查询业绩是否下单  查询已扣减成本金额
    if($row['multitype']==1){
        $sqlsalesorder=" SELECT costreduction,performanceoftime,modulestatus  FROM vtiger_salesorder as so,vtiger_salesorderrayment as sd   WHERE  so.servicecontractsid=? AND sd.receivedpaymentsid=? AND sd.salesorderid=so.salesorderid  AND  so.iscancel=0 LIMIT 1 ";
        $salesorderInfo=$adb->pquery($sqlsalesorder,array($row['servicecontractid'],$row['receivedpaymentsid']));
    }else{
        $sqlsalesorder=" SELECT  costreduction,performanceoftime,modulestatus  FROM vtiger_salesorder WHERE  servicecontractsid=? AND iscancel=0 LIMIT 1 ";
        $salesorderInfo=$adb->pquery($sqlsalesorder,array($row['servicecontractid']));
    }

    $salesorderInfo=$adb->query_result_rowdata($salesorderInfo,0);
    if(!empty($salesorderInfo['performanceoftime']) && $salesorderInfo['modulestatus']!='c_lackpayment'){//  工单审核 且 回款不为  回款不足
        $tsite=array(609236,609234,609237,609704,609230,609228,528504,609733,609735);
        //非saas 类 数组产品
        $noSaaS=array(361005,377277,361594,362103,361001,362103,362104,391124,2143595,2226512,2115476,462151);
        if ($salesorderInfo['performanceoftime']<$row['matchdate']){
            $salesorderInfo['performanceoftime']=$row['matchdate'];//解决第二次回款 日期 比 审核时间大且不再同一月的问题
        }
        $achievementmonth=getAchievementmonth($row['reality_date'],$salesorderInfo['performanceoftime']);
        //人力成本   工单外采成本   额外成本
        if($row['multitype']==1){
            $queryc="SELECT IFNULL(sum(vtiger_salesorderproductsrel.costing),0) AS costing,IFNULL(sum(vtiger_salesorderproductsrel.purchasemount),0) AS purchasemount,IFNULL(sum(vtiger_salesorderproductsrel.extracost),0) AS extracost FROM vtiger_salesorderproductsrel,vtiger_salesorderrayment WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND vtiger_salesorderrayment.receivedpaymentsid=? AND vtiger_salesorderproductsrel.salesorderid=vtiger_salesorderrayment.salesorderid AND multistatus=3 ";
            $costingdata=$adb->pquery($queryc,array($row['servicecontractid'],$row['receivedpaymentsid']));
        }else{
            $queryc="SELECT IFNULL(sum(vtiger_salesorderproductsrel.costing),0) AS costing,IFNULL(sum(vtiger_salesorderproductsrel.purchasemount),0) AS purchasemount,IFNULL(sum(vtiger_salesorderproductsrel.extracost),0) AS extracost FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND multistatus=3 ";
            $costingdata=$adb->pquery($queryc,array($row['servicecontractid']));
        }
        $costingdata=$adb->query_result_rowdata($costingdata,0);
        // 有关 沙龙 外采 媒介充值 其他
        $queryc="SELECT SUM(extra_price) as extra_price FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid=?";
        $otherdata=$adb->pquery($queryc,array($row['receivedpaymentsid']));
        $otherdata=$adb->query_result_rowdata($otherdata,0);
        $scalling=$row['scalling'];
        $costing=$costingdata['costing'];
        $purchasemount=$costingdata['purchasemount'];
        $extracost = $costingdata['extracost'];
        $allother=$otherdata['extra_price'];
        // 新增 分成字段显示
        $worksheetcost=($costingdata['costing']+$costingdata['purchasemount']+$costingdata['extracost']);
        $divideworksheetcost=$worksheetcost*($scalling/100);
        $dividecosting=$costingdata['costing']*($scalling/100);
        $dividepurchasemount=$costingdata['purchasemount']*($scalling/100);
        $divideextracost=$costingdata['extracost']*($scalling/100);
        $divideother=$allother*($scalling/100);
        // 新增 分成字段结束
        $marketingPrice=0;
        $costprice=0;
        //查询市场价格
        //① 查询套餐市场价 和 成本
        $marketingPriceTaoCan=0;
        $costpriceTaoCan=0;
        $firstMarketingPriceTaoCan=0;
        $firstCostpriceTaoCan=0;
        if(!empty($row['productid'])){
            if($row['multitype']==1){
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid LEFT JOIN vtiger_salesorderrayment as sd ON sd.salesorderid=vtiger_salesorderproductsrel.salesorderid   WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND sd.receivedpaymentsid=? AND productcomboid IN(".$row['productid'].") AND multistatus=3 LIMIT 1 ";
                $product=$adb->pquery($sql,array($row['servicecontractid'],$row['receivedpaymentsid']));
            }else{
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid  WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND productcomboid IN(".$row['productid'].") AND multistatus=3 LIMIT 1 ";
                $product=$adb->pquery($sql,array($row['servicecontractid']));
            }
            while ($rowsDat=$adb->fetch_array($product)) {
                if ((strpos($row['contract_no'], "XF") != false || $row['servicecontractstype'] != '新增')) {
                    $marketingPriceTaoCan += $rowsDat['renewalfee'] * $rowsDat['agelife'];
                    $costpriceTaoCan += $rowsDat['renewalcost'] * $rowsDat['agelife'];
                    $firstMarketingPriceTaoCan+=$rowsDat['unit_price'];
                    $firstCostpriceTaoCan+=$rowsDat['realprice'];
                } else {
                    $marketingPriceTaoCan += $rowsDat['unit_price'] + $rowsDat['renewalfee'] * ($rowsDat['agelife'] - 1);
                    $costpriceTaoCan += $rowsDat['realprice'] + $rowsDat['renewalcost'] * ($rowsDat['agelife'] - 1);
                    $firstMarketingPriceTaoCan+=$rowsDat['unit_price'];
                    $firstCostpriceTaoCan+=$rowsDat['realprice'];
                }
            }
        }
        //② 额外产品市场价  和  成本
        $marketingPriceExtra=0;
        $costpriceExtra=0;
        $firstMarketingPriceExtra=0;
        $firstCostpriceExtra=0;
        if(!empty($row['extraproductid'])){
            if($row['multitype']==1){
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid LEFT JOIN vtiger_salesorderrayment as sd ON sd.salesorderid=vtiger_salesorderproductsrel.salesorderid WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND sd.receivedpaymentsid=? AND productcomboid IN(".$row['extraproductid'].") AND multistatus=3  ";
                $product=$adb->pquery($sql,array($row['servicecontractid'],$row['receivedpaymentsid']));
            }else{
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid  WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND productcomboid IN(".$row['extraproductid'].") AND multistatus=3  ";
                $product=$adb->pquery($sql,array($row['servicecontractid']));
            }
            while ($rowsDat=$adb->fetch_array($product)){
                if((strpos($row['contract_no'],"XF")!=false || $row['servicecontractstype']!='新增')){
                    $marketingPriceExtra+=$rowsDat['renewalfee']*$rowsDat['agelife'];
                    $costpriceExtra+=$rowsDat['renewalcost']*$rowsDat['agelife'];
                    $firstMarketingPriceExtra+=$rowsDat['unit_price'];
                    $firstCostpriceExtra+=$rowsDat['realprice'];
                }else{
                    $marketingPriceExtra+=$rowsDat['unit_price']+$rowsDat['renewalfee']*($rowsDat['agelife']-1);
                    $costpriceExtra+=$rowsDat['realprice']+$rowsDat['renewalcost']*($rowsDat['agelife']-1);
                    $firstMarketingPriceExtra+=$rowsDat['unit_price'];
                    $firstCostpriceExtra+=$rowsDat['realprice'];
                }
            }
        }
        $marketingPrice=$marketingPriceExtra+$marketingPriceTaoCan;
        $costprice=$costpriceExtra+$costpriceTaoCan;
        $costdeduction=$costprice;
        $firstMarketingPrice=$firstMarketingPriceExtra+$firstMarketingPriceTaoCan;
        $firstCostdeduction=$firstCostpriceExtra+$firstCostpriceTaoCan;
        $unit_price=$row['unit_price'];
        $unit_prices=$unit_price*$row['scalling']/100;
        $scalling=$row['scalling'];
        $row['marketprice']=$marketingPrice;
        //公式四 到账业绩(百度V认证)
        if(strpos($row['contract_no'],"VRZ")){
            //到账业绩
            $arriveachievement=$unit_prices * 0.4;
            $arriveachievement=$arriveachievement-$otherdata['extra_price']*$scalling/100;
            $effectiverefund=$row['unit_price']*$scalling/100;
            if($arriveachievement<0){
                $arriveachievement=0;
            }
            $dataArray=[];
            $dataArray[]=$row['performanceoftime'];
            $dataArray[]=$achievementmonth;
            $dataArray[]=$effectiverefund;
            $dataArray[]=$arriveachievement;
            $dataArray[]=$arriveachievement;
            $dataArray[]=$row['marketprice'];
            $dataArray[]=$row['marketprice']*$scalling/100;
            $dataArray[]=$costdeduction;
            $dataArray[]=$costdeduction*$scalling/100;
            $dataArray[]='脚本百度V认证';
            $dataArray[]=$costing;
            $dataArray[]=$purchasemount;
            $dataArray[]=$extracost;
            $dataArray[]=$divideworksheetcost;
            $dataArray[]=$dividecosting;
            $dataArray[]=$dividepurchasemount;
            $dataArray[]=$divideextracost;
            $dataArray[]=$divideother;
            $dataArray[]=$allother;
            $dataArray[]=$worksheetcost;
            $dataArray[]=$row['achievementallotid'];
            $sql="UPDATE  vtiger_achievementallot_statistic SET  workorderdate=? ,achievementmonth=?,effectiverefund=?,arriveachievement=?,adjustbeforearriveachievement=?,marketprice=?,dividemarketprice=?,costdeduction=?,dividecostdeduction=?,remarks=?,costing=?,purchasemount=?,extracost=?,divideworksheetcost=?,dividecosting=?,dividepurchasemount=?,divideextracost=?,divideother=?,other=?,worksheetcost=? WHERE achievementallotid=? ";
            $adb->pquery($sql,$dataArray);
            AchievementSummary_Record_Model::newAchievementSummary(array($row['achievementallotid']));
        }else if(in_array($row['productid'],$tsite)){
            $isTsiteDiscount=0;
            if(in_array($row['productid'],array(528504,609228,609230,609733,609735))){
                $isTsiteDiscount=1;
            }

            // 做个过滤处理 如果是 搜索引擎类型google竞价合同  则不再生成业绩
            if($row['servicecontractstype']=='续费'){
                $paramers['contract_no']=$row['contract_no'];
                $paramers['marks']='Tsite续费合同类型的不生成业绩脚本判定 以防以前正常生成时生成了数据 在这里又更新';
                $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
                $Matchreceivements_Record_Model->noCalculationAchievementRecord($paramers);
                continue;
            }
            $remark='脚本Tsite';
            $deductionremark='脚本Tsite';
            //查询 是否为不符合产品  如果工单额外成本存在未 不符合产品 市场价成本扣除数用填写的工单额外成本
            //查询回款对应工单成本   市场价格  外采成本   额外成本

            //如果是多工单则直接走重新生成业绩
            if($row['multitype']==1){
                /*$queryc=" SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife FROM vtiger_salesorderproductsrel,vtiger_salesorderrayment WHERE vtiger_salesorderproductsrel.servicecontractsid=? AND vtiger_salesorderrayment.receivedpaymentsid=? AND vtiger_salesorderproductsrel.salesorderid=vtiger_salesorderrayment.salesorderid AND multistatus=3 ORDER BY vtiger_salesorderproductsrel.agelife DESC LIMIT 1";
                $resultdatapayments=$adb->pquery($queryc,array($row['servicecontractid'],$row['receivedpaymentsid']));

                $sql=" SELECT SUM(sd.marketprice) as marketprice ,SUM(sd.purchasemount) as purchasemount , SUM(sd.extracost) as extracost FROM vtiger_salesorderrayment as sr ,vtiger_salesorder as so,vtiger_salesorderproductsrel as sd  WHERE sr.receivedpaymentsid=? AND sr.salesorderid=so.salesorderid AND so.servicecontractsid=? AND so.salesorderid=sd.salesorderid  AND   sd.multistatus=3 ";
                $isNoMatch=$adb->pquery($sql,array($row['receivedpaymentsid'],$row['servicecontractid']));

                $queryc=" SELECT p.productid,p.productname,ssp.productcomboid,ssp.thepackage,count(1) as counts FROM vtiger_salesorderproductsrel as ssp LEFT JOIN vtiger_products as p ON p.productid=ssp.productid LEFT JOIN vtiger_salesorderrayment as sd ON sd.salesorderid=ssp.salesorderid   WHERE ssp.servicecontractsid =? AND sd.receivedpaymentsid=? AND ssp.multistatus=3 group by ssp.productcomboid ";
                $productdatas=$adb->pquery($queryc,array($row['servicecontractid'],$row['receivedpaymentsid']));*/
                $Matchreceivements_BasicAjax_Action=new Matchreceivements_BasicAjax_Action();
                $Matchreceivements_BasicAjax_Action->commonInsertAchievementallotStatisticjioaben($row['receivedpaymentsid'],$row['unit_price'],0,0,$row['servicecontractid'],0,$row['matchdate']);
                continue;
            }else{
                $queryc=" SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid= ? AND multistatus in(0,1)  ORDER BY vtiger_salesorderproductsrel.agelife DESC LIMIT 1";
                $resultdatapayments=$adb->pquery($queryc,array($row['servicecontractid']));

                $sql=" SELECT SUM(sd.marketprice) as marketprice ,SUM(sd.purchasemount) as purchasemount , SUM(sd.extracost) as extracost FROM vtiger_salesorderproductsrel as sd  WHERE  sd.servicecontractsid=?  AND   sd.multistatus=3  ";
                $isNoMatch=$adb->pquery($sql,array($row['servicecontractid']));

                $queryc=" SELECT p.productid,p.productname,ssp.productcomboid,ssp.thepackage,count(1) as counts FROM vtiger_salesorderproductsrel as ssp LEFT JOIN vtiger_products as p ON p.productid=ssp.productid  WHERE ssp.servicecontractsid =? AND ssp.multistatus=3 group by ssp.productcomboid ";
                $productdatas=$adb->pquery($queryc,array($row['servicecontractid']));
            }
            $agelife=$adb->query_result_rowdata($resultdatapayments,0);
            $isMoreYears=$agelife['agelife']>1?1:0;
            $isNoMatch=$adb->query_result_rowdata($isNoMatch,0);
            $productname='';
            while ($rowDatas=$adb->fetch_array($productdatas)){
                if($rowDatas['productcomboid']==$row['productid']){//  解决数量问题 是变量$row 写成了 $rp
                    $productname.=$rowDatas['thepackage']."(1),";
                }else{
                    $productname.=$rowDatas['thepackage']."(".$rowDatas['counts']."),";
                }
            }
            $productname=trim($productname,',');

            if($isNoMatch['extracost']>0){
                $deductionremark.="不符合";
                // 删除改当前回款已经扣减记录
                $adb->pquery(" DELETE FROM vtiger_oldachievement_hasdeduction WHERE  receivedpaymentsid=? ",array($row['receivedpaymentsid']));
                // 查询已经扣减
                $alreadydeduction=" SELECT deductionmoney FROM vtiger_oldachievement_hasdeduction WHERE servicecontractsid=? AND achievementtype='extra'  ORDER BY  id DESC LIMIT 1  ";
                $alreadydeduction=$adb->pquery($alreadydeduction,array($row['servicecontractid']));
                $alreadydeduction=$adb->query_result_rowdata($alreadydeduction,0);
                $lastExtracost=$isNoMatch['extracost']-$alreadydeduction['deductionmoney'];
                $marketingPrice=$isNoMatch['marketprice'];
                if($isMoreYears==1){
                    $remark.="不符合产品工单多年单";
                    $deductionremark.="多年单";
                    /*$totalToMarketprice=$row['total']/$isNoMatch['marketprice'];
                    $totalToMarketprice=$totalToMarketprice>1?1:$totalToMarketprice;
                    $arriveachievement=$totalToMarketprice*$row['unit_price']-$row['unit_price']/$row['total']*$costdeduction-$isNoMatch['extracost'];*/
                    $params['total']=$buyContractamount=$buyFirstMarketingPrice=$firstMarketingPrice;//7800
                    $params['marketprice']=$firstMarketingPrice;//7800
                    $params['costdeduction']=$buysplitcost=$firstCostdeduction;//800
                    //  2000  7800  30000
                    $params['unit_price']=$row['unit_price']*$firstMarketingPrice/$isNoMatch['marketprice'];//520
                    $params['extracost']=$buyLastExtracost=$lastExtracost;//8000
                    $buyArriveachievement=getArriveachievementTsiteNoMatch($params);
                    $params['total']=$renewContractamount=$row['total']-$firstMarketingPrice;
                    $params['marketprice']=$renewMarketingPrice=$isNoMatch['marketprice']-$firstMarketingPrice;
                    $params['unit_price']=$row['unit_price']-$params['unit_price'];//2000-520
                    $params['costdeduction']=$renewsplitcost=$costdeduction-$firstCostdeduction;
                    $params['extracost']=$buyArriveachievement>=0?0:-$buyArriveachievement;
                    $renewArriveachievement=getArriveachievementTsiteNoMatch($params);
                    if($isTsiteDiscount==1){
                        if( $row['total']/$isNoMatch['marketprice']<0.75 ){
                            $deductionremark.="合同金额除市场价格小于0.75";
                            $buyArriveachievement=0;
                            $renewArriveachievement=0;
                        }
                    }
                    //剩余未扣减额外成本
                    $lastExtracost=$renewArriveachievement>=0?0:-$renewArriveachievement;
                }else{
                    $remark.="不符合产品工单非多年单";
                    $deductionremark.="非多年单";
                    /*$marketingPrice=$isNoMatch['marketprice'];
                    $totalToMarketprice=$row['total']/$isNoMatch['marketprice'];
                    $totalToMarketprice=$totalToMarketprice>1?1:$totalToMarketprice;
                    $arriveachievement=$totalToMarketprice*$row['unit_price']-$row['unit_price']/$row['total']*$costdeduction-$lastExtracost;*/
                    $params['total']=$row['total'];
                    $params['marketprice']=$isNoMatch['marketprice'];
                    $params['costdeduction']=$costdeduction;
                    $params['unit_price']=$row['unit_price'];
                    $params['extracost']=$lastExtracost;
                    $arriveachievement=getArriveachievementTsiteNoMatch($params);
                    if($isTsiteDiscount==1){
                        if( $row['total']/$isNoMatch['marketprice']<0.75 ){
                            $deductionremark.="合同金额除市场价格小于0.75";
                            $arriveachievement=0;
                        }
                    }
                    // 剩余未扣减额外成本
                    $lastExtracost=$arriveachievement>=0?0:-$arriveachievement;
                }
                // 注释掉第一次的计算公式 $arriveachievement=($isNoMatch['marketprice']-($isNoMatch['purchasemount']+$isNoMatch['extracost']))*($row['unit_price']/$row['total']);
                $row['marketprice']=$marketingPrice;
            }else{
                if($isMoreYears==1){
                    $remark.="符合多年单";
                    // 如果是购买
                    $params['total']=$buyContractamount=$buyFirstMarketingPrice=$firstMarketingPrice;
                    $params['marketprice']=$firstMarketingPrice;
                    $params['unit_price']=$row['unit_price'];
                    $params['costdeduction']=$buysplitcost=$firstCostdeduction;
                    $buyArriveachievement=getArriveachievementTsiteMatch($params);
                    // 如果是续费
                    $params['total']=$renewContractamount=$row['total']-$firstMarketingPrice;
                    $params['marketprice']=$renewMarketingPrice=$row['marketprice']-$firstMarketingPrice;
                    $params['unit_price']=$row['unit_price'];
                    $params['costdeduction']=$renewsplitcost=$costdeduction-$firstCostdeduction;
                    $renewArriveachievement=getArriveachievementTsiteMatch($params);
                    if($isTsiteDiscount==1){
                        if( $row['total']/$row['marketprice']<0.75 ){
                            $deductionremark.="合同金额除市场价格小于0.75";
                            $buyArriveachievement=0;
                            $renewArriveachievement=0;
                        }
                    }
                }else{
                    $remark.="符合非多年单";
                    $params['total']=$row['total'];
                    $params['marketprice']=$row['marketprice'];
                    $params['unit_price']=$row['unit_price'];
                    $params['costdeduction']=$costdeduction;
                    //到账业绩
                    /*if($row['total']>=$row['marketprice']){
                        $arriveachievement=$row['unit_price']-$row['unit_price']/$row['total']*($costdeduction);
                    }else{
                        $arriveachievement=$row['total']/$row['marketprice']*$row['unit_price']-$row['unit_price']/$row['total']*($costdeduction);
                    }*/
                    $arriveachievement=getArriveachievementTsiteMatch($params);
                    if($isTsiteDiscount==1){
                        if( $row['total']/$row['marketprice']<0.75 ){
                            $deductionremark.="合同金额除市场价格小于0.75";
                            $arriveachievement=0;
                        }
                    }
                }

            }
            $arriveachievement=($arriveachievement-$otherdata['extra_price'])*$scalling/100;
            $effectiverefund=$row['unit_price']*$scalling/100;
            $arriveachievement=$arriveachievement<0?0:$arriveachievement;
            $dataArray=[];
            $dataArray['workorderdate']=$row['performanceoftime'];
            $dataArray['achievementmonth']=$achievementmonth;
            $dataArray['effectiverefund']=$effectiverefund;
            $dataArray['arriveachievement']=$arriveachievement;
            $dataArray['adjustbeforearriveachievement']=$arriveachievement;
            $dataArray['marketprice']=$row['marketprice'];
            $dataArray['dividemarketprice']=$row['marketprice']*$scalling/100;
            $dataArray['costdeduction']=$costdeduction;
            $dataArray['dividecostdeduction']=$costdeduction*$scalling/100;
            $dataArray['costing']=$costing;
            $dataArray['purchasemount']=$purchasemount;
            $dataArray['extracost']=$extracost;
            $dataArray['other']=$allother;
            $dataArray['worksheetcost']=$worksheetcost;
            $dataArray['remarks']=$remark;
            $dataArray['divideworksheetcost']=$divideworksheetcost;
            $dataArray['dividecosting']=$dividecosting;
            $dataArray['dividepurchasemount']=$dividepurchasemount;
            $dataArray['divideextracost']=$divideextracost;
            $dataArray['divideother']=$divideother;
            $dataArray['splitcontractamount']=0;
            $dataArray['splitmarketprice']=0;
            $dataArray['splitcost']=0;
            $dataArray['more_years_renew']=0;
            $dataArray['renewal_commission']=0;
            $dataArray['renewtimes']=0;
            $dataArray['commissionforrenewal']=0;
            $dataArray['productname']=$productname;
            //是多年单
            if($isMoreYears==1){
                $dataArray['more_years_renew']=1;
                // 属于新单业绩修改
                $buyArriveachievement=($buyArriveachievement-$otherdata['extra_price'])*$scalling/100;
                $buyArriveachievement=$buyArriveachievement<0?0:$buyArriveachievement;
                //$dataArray
                $dataArray['splitcontractamount']=$buyContractamount;
                $dataArray['splitmarketprice']=$buyFirstMarketingPrice;
                $dataArray['splitcost']=$buysplitcost;
                $dataArray['arriveachievement']=$buyArriveachievement;
                $dataArray['adjustbeforearriveachievement']=$buyArriveachievement;
                $dataArray['receivedpaymentsid']=$row['receivedpaymentsid'];
                $dataArray['achievementtype']='newadd';
                $dataArray['receivedpaymentownid']=$row['receivedpaymentownid'];
                updateAchievementallotSatstic($adb,$dataArray);
                $renewArriveachievement=$renewArriveachievement*$scalling/100;
                $renewArriveachievement=$renewArriveachievement<0?0:$renewArriveachievement;
                //属于续费业绩修改
                $dataArray['splitcontractamount']=$renewContractamount;
                $dataArray['splitmarketprice']=$renewMarketingPrice;
                $dataArray['splitcost']=$renewsplitcost;
                $dataArray['arriveachievement']=$renewArriveachievement;
                $dataArray['adjustbeforearriveachievement']=$renewArriveachievement;
                $dataArray['renewtimes']=1;// 由于Tsite只有一次没有续费账号一说 所以直接续费 次数1  续费提成点6%
                $dataArray['renewal_commission']=6;
                $dataArray['commissionforrenewal']=$renewArriveachievement*$dataArray['renewal_commission']/100;
                $dataArray['achievementtype']='renew';
                $dataArray['other']=0;
                $dataArray['divideother']=0;
                updateAchievementallotSatstic($adb,$dataArray);
            }else{
                if($row['achievementtype']=='renew'){
                    $dataArray['renewtimes']=1;
                    $dataArray['renewal_commission']=6;
                    $dataArray['commissionforrenewal']=$arriveachievement*$dataArray['renewal_commission']/100;
                }
                $dataArray['receivedpaymentsid']=$row['receivedpaymentsid'];
                $dataArray['achievementtype']=$row['achievementtype'];
                $dataArray['receivedpaymentownid']=$row['receivedpaymentownid'];
                updateAchievementallotSatstic($adb,$dataArray);
            }
            // 如果是不符合产品记录 已经扣减额外成本情况
            if($isNoMatch['extracost']>0){
                //已经扣减参数额外成本参数
                $param=array('contractid'=>$row['servicecontractid'],'oldcontractid'=>0,'receivepayid'=>$row['receivedpaymentsid'],'alreadydeduction'=>$isNoMatch['extracost']-$lastExtracost,'totaldeductionmoney'=>$isNoMatch['extracost'],'deductionremark'=>$deductionremark,'listAchievementType'=>'extra');
                hasdeduction($param,$adb);
            }
            $achievementallotids=[];
            if($isMoreYears==1){
                $sql="SELECT  achievementallotid  FROM vtiger_achievementallot_statistic  WHERE  receivedpaymentownid=? AND receivedpaymentsid=? AND servicecontractid=? ";
                $result=$adb->pquery($sql,array($row['receivedpaymentownid'],$row['receivedpaymentsid'],$row['servicecontractid']));
                while ($dataRows=$adb->fetch_array($result)){
                      $achievementallotids[]=$dataRows['achievementallotid'];
                }
            }else{
                $achievementallotids[]=$row['achievementallotid'];
            }
            AchievementSummary_Record_Model::newAchievementSummary(array($row['achievementallotid']));
            //公式三 到账业绩（非SAAS类）
        }else {
            //非saas类获取市场价 虽然业绩计算公式和市场价无关但还是查下 start
            $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
            $dataResult=$Matchreceivements_Record_Model->noSaaSGetMarketing($row);
            if($dataResult>0){
                $row['marketprice']=$dataResult;
            }
            //非saas类获取市场价 虽然业绩计算公式和市场价无关但还是查下 end
            // 已扣减工单成本差
            $gongdancha=$worksheetcost-$salesorderInfo['costreduction'];
            // 第一个分成明细重新计算分成时记录  已回款扣除成本
            if($yikouchengben[$row['receivedpaymentsid']]['costreduction']){

            }else{
                $yikouchengben[$row['receivedpaymentsid']]['costreduction']=$salesorderInfo['costreduction'];
            }
            // 防止多个分成人时  已扣减分成差取的不对
            if($yikouchengben[$row['receivedpaymentsid']]['gongdancha']){
            }else{
               $yikouchengben[$row['receivedpaymentsid']]['gongdancha']=$gongdancha;
            }
            //到账业绩
            $arriveachievement=($unit_price-$yikouchengben[$row['receivedpaymentsid']]['gongdancha'])*$scalling/100;
            // 到账业绩 - pos手续费
            $arriveachievement=$arriveachievement-$otherdata['extra_price']*$scalling/100;

            if($arriveachievement< 0 ){
                $arriveachievement=0;
            }
            //如果已减工单成本 + 目前回款金额 大于等于总成本说明 工单成本已经减完了 则直接把已减成本改成 总成本就ok了
            if($row['multitype']==1){
                if($row['unit_price']+$yikouchengben[$row['receivedpaymentsid']]['costreduction']>=$worksheetcost){
                    //更新已减成本
                    $updatesql = " UPDATE vtiger_salesorder SET costreduction=? WHERE servicecontractsid =?  AND  salesorderid=(SELECT salesorderid FROM vtiger_salesorderrayment WHERE receivedpaymentsid=? LIMIT 1 ) ";
                    $adb->pquery($updatesql,array($worksheetcost,$row['servicecontractid'],$row['receivedpaymentsid']));
                }else{              //更新已减成本
                    $updatesql = " UPDATE vtiger_salesorder SET costreduction=costreduction+? WHERE servicecontractsid =? AND  salesorderid=(SELECT salesorderid FROM vtiger_salesorderrayment WHERE receivedpaymentsid=? LIMIT 1 )";
                    $hasBackScalling=$row['unit_price']*$scalling/100;
                    $adb->pquery($updatesql,array($hasBackScalling,$row['servicecontractid'],$row['receivedpaymentsid']));
                }
            }else{
                if($row['unit_price']+$yikouchengben[$row['receivedpaymentsid']]['costreduction']>=$worksheetcost){
                    //更新已减成本
                    $updatesql = " UPDATE vtiger_salesorder SET costreduction=? WHERE servicecontractsid =? ";
                    $adb->pquery($updatesql,array($worksheetcost,$row['servicecontractid']));
                }else{              //更新已减成本
                    $updatesql = " UPDATE vtiger_salesorder SET costreduction=costreduction+? WHERE servicecontractsid =? ";
                    $hasBackScalling=$row['unit_price']*$scalling/100;
                    $adb->pquery($updatesql,array($hasBackScalling,$row['servicecontractid']));
                }
            }
            $dataArray=[];
            $dataArray[]=$row['performanceoftime'];
            $dataArray[]=$worksheetcost;
            $dataArray[]=$achievementmonth;
            $dataArray[]=$arriveachievement;
            $dataArray[]=$arriveachievement;
            $dataArray[]=$arriveachievement;
            $dataArray[]=$row['marketprice'];
            $dataArray[]=$row['marketprice']*$scalling/100;
            $dataArray[]=$costdeduction;
            $dataArray[]=$costdeduction*$scalling/100;
            $dataArray[]='脚本非saas';
            $dataArray[]=$costing;
            $dataArray[]=$purchasemount;
            $dataArray[]=$extracost;
            $dataArray[]=$divideworksheetcost;
            $dataArray[]=$dividecosting;
            $dataArray[]=$dividepurchasemount;
            $dataArray[]=$divideextracost;
            $dataArray[]=$divideother;
            $dataArray[]=$allother;
            $dataArray[]=$row['achievementallotid'];
            $sql="UPDATE  vtiger_achievementallot_statistic SET  workorderdate=? ,worksheetcost=?,achievementmonth=?,effectiverefund=?,arriveachievement=?,adjustbeforearriveachievement=?,marketprice=?,dividemarketprice=?,costdeduction=?,dividecostdeduction=?,remarks=?,costing=?,purchasemount=?,extracost=?,divideworksheetcost=?,dividecosting=?,dividepurchasemount=?,divideextracost=?,divideother=?,other=? WHERE achievementallotid=? ";
            $adb->pquery($sql,$dataArray);
            AchievementSummary_Record_Model::newAchievementSummary(array($row['achievementallotid']));

        }

    }
}

//保存已经扣额外成本  (有两个用处)① Tsite 额外成本扣减记录  ② T云升级  原到账业绩扣减记录。
function hasdeduction($params,$adb){
    if(!isset($params['salesorderid'])){
        $params['salesorderid']=0;
    }
    $adb->pquery("INSERT INTO `vtiger_oldachievement_hasdeduction` (`servicecontractsid`, `oldservicecontractsid`, `receivedpaymentsid`, `deductionmoney`, `createtime`, `totaldeductionmoney`, `marks`,`achievementtype`,lastdeductionmoney,salesorderid) VALUES (?,?,?,?,?,?,?,?,?,?)",
        array($params['contractid'],$params['oldcontractid'],$params['receivepayid'],$params['alreadydeduction'],date("Y-m-d H:i:s"),$params['totaldeductionmoney'],$params['deductionremark'],$params['listAchievementType'],$params['lastdeductionmoney'],$params['salesorderid']));
}

//更新业绩数据
function updateAchievementallotSatstic($adb,$dataArray){
    /*echo "<pre>";
    var_dump($dataArray);exit();*/
    $sql="UPDATE  vtiger_achievementallot_statistic SET  workorderdate=? ,achievementmonth=?,effectiverefund=?,arriveachievement=?,adjustbeforearriveachievement=?,marketprice=?,dividemarketprice=?,costdeduction=?,dividecostdeduction=?,costing=?,purchasemount=?,extracost=?,other=?,worksheetcost=?,remarks=?,divideworksheetcost=?,dividecosting=?,dividepurchasemount=?,divideextracost=?,divideother=?,splitcontractamount=?,splitmarketprice=?,splitcost=?,more_years_renew=?,renewal_commission=?,renewtimes=?,commissionforrenewal=?,productname=? WHERE  receivedpaymentsid=?  AND  achievementtype=? AND receivedpaymentownid=? ";
    $adb->pquery($sql,array_values($dataArray));
    AchievementSummary_Record_Model::newAchievementSummary(array($dataArray['achievementallotid']));
}
// tisite 获取到账业绩公式
function getArriveachievementTsiteNoMatch($params){
    //10200/22200
    $totalToMarketprice=$params['total']/$params['marketprice'];// 53.33     520-520/7800*800  680
    $totalToMarketprice=$totalToMarketprice>1?1:$totalToMarketprice;
                                                    //520                 520            7800          800       8000
    $arriveachievement=$totalToMarketprice*$params['unit_price']-$params['unit_price']/$params['total']*$params['costdeduction']-$params['extracost'];
    return  $arriveachievement;
}
// tsite 获取到账业绩公式 符合
function getArriveachievementTsiteMatch($params){
    if($params['total']>=$params['marketprice']){
        $arriveachievement=$params['unit_price']-$params['unit_price']/$params['total']*($params['costdeduction']);
    }else{
        $arriveachievement=$params['total']/$params['marketprice']*$params['unit_price']-$params['unit_price']/$params['total']*($params['costdeduction']);
    }
    return $arriveachievement;
}
//获取业绩所属月份
function getAchievementmonth($reality_date,$matchdate){
    $closingDate=Vtiger_Record_Model::getInstanceById(2434264,'ClosingDate');
    $date=$closingDate->get("date");
    $reality_dateYm=date("Y-m",strtotime($reality_date));
    //回款时间下月
    $reality_dateNm=date("Y-m",strtotime("+1 months",strtotime(date("Y-m",strtotime($reality_date)))));
    $matchdateYm=date("Y-m",strtotime($matchdate));
    $matchdateD=date("d",strtotime($matchdate));
    //匹配年月等于回款时间年月 或者 匹配时间年月等于回款时间年月的下一月且日期在1-3号之间 则
    if(($reality_dateYm==$matchdateYm) || ($reality_dateNm==$matchdateYm && $matchdateD<=$date)){
        $achievementmonth=$reality_dateYm;
        //如果匹配时间年月等于回款时间年月的下一月且 匹配日期大于3
    }else if($reality_dateNm==$matchdateYm && $matchdateD>$date){
        $achievementmonth=$matchdateYm;
        // 如果
    }else if($matchdateYm>$reality_dateNm){
        if($matchdateD<=$date){
            $achievementmonth=date("Y-m",strtotime("-1 months",strtotime(date("Y-m",strtotime($matchdate)))));
        }else{
            $achievementmonth=$matchdateYm;
        }
    }
    return $achievementmonth;
}




