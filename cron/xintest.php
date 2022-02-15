<?php
define("AUTO_TOKEN",md5(date('Y-m-d H:i:s')));
$dir=trim(__DIR__,DIRECTORY_SEPARATOR);
$dir=trim(__DIR__,'cron');
ini_set("include_path", $dir);
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");
error_reporting(-1);
ini_set("display_errors",1);
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
vglobal('default_language', $default_language);
$currentLanguage = 'zh_cn';
vglobal('current_language', $currentLanguage);

global $adb;
//$contract_no='ZD-TYUNV1FJ2021001919';
//    $sql="SELECT vtiger_receivedpayments.receivedpaymentsid FROM vtiger_servicecontracts LEFT JOIN vtiger_receivedpayments ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid WHERE vtiger_servicecontracts.contract_no='".$contract_no."'";
//    $idArray=array_column($adb->run_query_allrecords($sql),'receivedpaymentsid');
//    Matchreceivements_Record_Model::recordLog($contract_no.'查看所有匹配的回款','achievement');
//    //获取已匹配这里的所有回款，没有就不处理
//    if($idArray){
//        foreach ($idArray as $id){
//            $sql='select * FROM vtiger_achievementallot_statistic where receivedpaymentsid='.$id;
//            $resultAll=$adb->run_query_allrecords($sql);
//            //没业绩，不处理
//            if($resultAll){
//                Matchreceivements_Record_Model::recordLog($id.'处理此回款','achievement');
//                foreach ($resultAll as $result){
//                    foreach ($result as $key =>$value){
//                        if(is_numeric($key)){
//                            unset($result[$key]);
//                        }
//                    }
//                    Matchreceivements_Record_Model::recordLog($id.'此回款的业绩提成已发放','achievement');
//                    $status=$result['status'];
//                    $isover=$result['isover'];
//                    if($status||$isover){
//                        Matchreceivements_Record_Model::recordLog($id.'此回款的业绩提成已发放','achievement');
//                        //业绩提成已发放,新增业绩当负数
//                        unset($result['achievementallotid']);
//                        $result['achievementmonth']=date('Y-m');
//                        $result['arriveachievement'] *=-1;
//                        $result['effectiverefund'] *=-1;
//                        $result['adjustbeforearriveachievement'] *=-1;
//                        Matchreceivements_Record_Model::recordLog($adb->sql_insert_data('vtiger_achievementallot_statistic',$result),'achievement');
//                        $adb->run_insert_data('vtiger_achievementallot_statistic',$result);
//                        $this->recordIscheckachievement(0,$id);
//                    }else{
//                        //有业绩没发放，删掉业绩等重新计算
//                        Matchreceivements_Record_Model::recordLog($id.'此回款没业绩','achievement');
//                        $this->recordIscheckachievement(1,$id);
//                    }
//                }
//            }
//        }
//    }
//$datas=array();
//$sql="select company,CONCAT(IF(bank is null,'',bank),IF(subbank is null,'',CONCAT('-',subbank)),IF(account IS NULL,'',CONCAT('（',account,'）'))) as item from rececompany";
//$lists=$adb->run_query_allrecords($sql);
//foreach ($lists as $list){
//    $datas[$list['company']].=$list['item'].'|';
//}
//
//
//foreach ($datas as $key => $data){
//    echo "{name:'".$key."',subname: '".rtrim($data,'|')."'},".PHP_EOL;
//}


//$userModel=new Users_Record_Model();
//print_r($userModel->getDepartmentTree());


//$curl = curl_init();
//
//curl_setopt_array($curl, array(
//    CURLOPT_URL => 'http://pretyapi.71360.com/api/micro/aggregateservice-api/v1.0.0/api/Order/GetMinPaymentMoney',
//    CURLOPT_RETURNTRANSFER => true,
//    CURLOPT_ENCODING => '',
//    CURLOPT_MAXREDIRS => 10,
//    CURLOPT_TIMEOUT => 0,
//    CURLOPT_FOLLOWLOCATION => true,
//    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//    CURLOPT_CUSTOMREQUEST => 'POST',
//    CURLOPT_POSTFIELDS =>'{
//    "payCode": "ZF202110280503468506054026507606"
//}',
//    CURLOPT_HTTPHEADER => array(
//        'Content-Type: application/json',
//        'S-Request-Token: ded502bab7a679a7628f712d0d9a8751',
//        'S-Request-Time: 1635474527123'
//    ),
//));
//
//$response = curl_exec($curl);
//
//curl_close($curl);
//echo $response;

//
//
//
//
//$recordModel=new ServiceContracts_Record_Model();
//$result=$recordModel->leastPayMoney($recordId);
//$recordModel=new Matchreceivements_Record_Model();
//echo json_encode($recordModel->getTyunBasicInformation('3208143'));


//$params['contract_no']='ZD-TYUNSJ2021000105';
//$params['servicecontractstype']='upgrade';
//$params['activationcodeid']=78505;
//$params['usercode']='shjingjia';
//$params['customerid']=79435;
//$params['productlife']=1;
//$params['total']=29800;
//$params['contractid']=3208143;
//$params['renewmarketprice']=19800;
//$Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
//$dataResult=$Matchreceivements_Record_Model->renewOrNewadd($params);
//echo json_encode($dataResult);

//$inparams['contractamount']=29800+5880;// 总的合同金额
//$inparams['effectiveTotal']=29800;// 计算收购单时合同金额=首购市场价格
//$inparams['marketprice']=29800;//    首购市场价格
//$inparams['costdeduction']=2000;//  首购成本
//$inparams['unit_price']=29800;// 总回款金额
//$inparams['total']=29800;//
//$sqlquery=" SELECT more_years_renew,SUM(IF(achievementtype='newadd',1,0)) as ishasnewadd,SUM(IF(achievementtype='newadd',arriveachievement,0)) as newaddoldallarriveachievement,SUM(IF(achievementtype='newadd',1,0)) as ishasrenew,SUM(IF(achievementtype='renew',arriveachievement,0)) as renewoldallarriveachievement FROM vtiger_achievementallot_statistic WHERE servicecontractid =? ";
//$oldarriveachievement=$adb->pquery($sqlquery,array(2382121));
//$oldarriveachievement=$adb->query_result_rowdata($oldarriveachievement,0);
//$alreadydeduction=" SELECT deductionmoney FROM vtiger_oldachievement_hasdeduction WHERE servicecontractsid=? AND achievementtype='newadd'  ORDER BY  id DESC LIMIT 1  ";
//$alreadydeduction=$adb->pquery($alreadydeduction,array(3208143));
//$newaddalreadydeduction=$adb->query_result_rowdata($alreadydeduction,0);
//$newAddLastDeduction=$oldarriveachievement['newaddoldallarriveachievement']-$newaddalreadydeduction['deductionmoney'];
//$inparams['oldarriveachievement']=$newAddLastDeduction;// 新单剩余未减原到账业绩
//
//$Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
//$dataResult=$Matchreceivements_Record_Model->getArriveachievementByFormulaFour($params);
//echo json_encode($dataResult);


//function getTyunBasicInformation($contractid){
//    global $adb;
//    $sqls=" SELECT activitytype,orderamount,classtype,productnames,productlife,productname,noseparaterenewmarketprice,noseparaterenewcosttprice,separaterenewmarketprice,separaterenewcosttprice,marketprice,isdirectsellingtoprice,costprice,productid,canrenew,giveterm,buyseparately,onemarketprice,onemarketrenewprice,onecostprice,onecostrenewprice FROM  vtiger_activationcode WHERE contractid = ? AND status IN(0,1) ";
//    $results=$adb->pquery($sqls,array($contractid));
//    $productdatas =array();
//    $productdatas['othermarketprice']=0;//另购产品的续费总市场价  新购续费市场价 *（年限-1）
//    $productdatas['othermarketrenewprice']=0;//另购产品的续费总市场价  新购续费市场价 *（年限-1）
//    $productdatas['othercostrenewprice']=0;//另购产品的总成本价
//    $productdatas['othercostaddprice']=0;//另购产品的首购总成本价  另购首购成本
//    $productdatas['renewmarketrenewprice']=0;//续费产品的总成本价  续费市场价*续费年限
//    $productdatas['renewcostrenewprice']=0;//续费产品的总成本价
//    $productdatas['noseparaterenewmarketprice']=0;//待续费产品的市场价
//    $productdatas['noseparaterenewcosttprice']=0;//待续费产品的成本价
//    $productdatas['separaterenewmarketprice']=0;//已续费产品的总成本价
//    $productdatas['separaterenewcosttprice']=0;//已续费产品的总成本价
//    while ($dtaRows=$adb->fetch_array($results)){
//        $dtaRows['onecostprice']=$dtaRows['onecostprice']<0?0:$dtaRows['onecostprice'];//首购成本价
//        $dtaRows['onemarketprice']=$dtaRows['onemarketprice']<0?0:$dtaRows['onemarketprice'];//首购市场价
//        $dtaRows['costprice']=$dtaRows['costprice']<0?0:$dtaRows['costprice'];//总成本价
//        $dtaRows['onemarketrenewprice']=$dtaRows['onemarketrenewprice']<0?0:$dtaRows['onemarketrenewprice'];//续费市场价
//        $dtaRows['onecostrenewprice']=$dtaRows['onecostrenewprice']<0?0:$dtaRows['onecostrenewprice'];//续费成本价
//        $productdatas['noseparaterenewmarketprice']=$dtaRows['noseparaterenewmarketprice'];//待续费产品的市场价
//        $productdatas['noseparaterenewcosttprice']=$dtaRows['noseparaterenewcosttprice'];//待续费产品的成本价
//        $productdatas['separaterenewmarketprice']=$dtaRows['separaterenewmarketprice'];//已续费产品的总成本价
//        $productdatas['separaterenewcosttprice']=$dtaRows['separaterenewcosttprice'];//已续费产品的总成本价
//        $productdatas['productname'].=$dtaRows['productname'].",";
//        $productdatas['upgrademarketprice']+=$dtaRows['onemarketprice']+$dtaRows['onemarketrenewprice']*($dtaRows['productlife']-1-$dtaRows['giveterm']);// 升级用到的市场价格
//        //升级成本取值
//        $productdatas['upgradecostprice']+=$dtaRows['onecostprice']+$dtaRows['onecostrenewprice']*($dtaRows['productlife']-1);// 升级用到的总成本
//        $productdatas['onemarketprice']+=$dtaRows['onemarketprice'];
//        $productdatas['onecostprice']+=$dtaRows['onecostprice'];
//        $productdatas['renewmarketrenewprice'] += $dtaRows['onemarketrenewprice'] * ($dtaRows['productlife'] - 1 - $dtaRows['giveterm']);
//        $productdatas['onemarketrenewprice'] += $dtaRows['onemarketrenewprice'] * ($dtaRows['productlife'] - 1 - $dtaRows['giveterm']);
//        $productdatas['renewcostrenewprice']+=$dtaRows['onecostrenewprice']*($dtaRows['productlife']-1);
//        $productdatas['onecostrenewprice']+=$dtaRows['onecostrenewprice']*($dtaRows['productlife']-1);
//    }
//    $productdatas['marketprice']=$productdatas['onemarketprice']+$productdatas['renewmarketrenewprice'];
//    $productdatas['costprice']=$productdatas['onecostrenewprice']+$productdatas['onecostprice'];
//    return $productdatas;
//}
//
//
//
////tYunCalculationAchievement(1,3136642,0,15800,$adb,0,0);
//function tYunCalculationAchievement($receivepayid,$contractid,$shareuser,$total,$currentid,$adb,$matchdate=0,$salesorderid=0){
//    global $adb;
//    $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
//
//    $queryc="SELECT * from rp where servicecontractsid=?";
//    $resultdatapayments=$adb->pquery($queryc,array($contractid));
//    $rp=$adb->query_result_rowdata($resultdatapayments,0);
//    //算出总的合同金额
//    $contractInfo=array('contractamount'=>15800,'contractprice'=>15800,'upgradetransfer'=>0);
//
//    $insertValueStrArray=array();
//
//    $productdatas =getTyunBasicInformation(3187531);//取基础价格信息
//    $productdatas['productname']=trim($productdatas['productname'],',');
//    $productname=$productdatas['productname'];//产品名称
//    $marketingPrice=$productdatas['marketprice'];//总市场价
//    $costprice=$productdatas['costprice'];//总成本
//    $onemarketprice=$productdatas['onemarketprice'];//首购市场价
//    $onecostprice=$productdatas['onecostprice'];//首购成本价
//    $onemarketrenewprice=$productdatas['onemarketrenewprice'];//续费市价
//    $onecostrenewprice=$productdatas['onecostrenewprice'];//续费成本价
//    $renewmarketrenewprice=$productdatas['renewmarketrenewprice'];//除首购外续费市场价
//    $renewcostrenewprice=$productdatas['renewcostrenewprice'];//除首购外续费成本价
//    $renewalBase=9;//续费提成起步点
//    //@todo------
//    $rp['marketprice']=$marketingPrice;//总市场价
//    //总业绩市场价格（升级时用到的）原合同剩余+ 业绩市场价// cxh2020-08-06 以前的升级市场价格取值
//    $allmarketingPrice=$contractInfo['upgradetransfer']+$rp['marketprice'];
//    $modulestatus='a_normal';
//    //回款时间 即入账日期
//    $reality_date=$rp['reality_date'];
//    $matchdate=$rp['matchdatetime'];
//    //匹配时间
//    if(!empty($matchdate)){
//        $matchdate=$matchdate;
//    }else{
//        $matchdate=date('Y-m-d');
//    }
//
//    $achievementmonth='2021-10';
//    //成本扣除数
//    $costdeduction=$costprice;
//    //获取是新单还是续费业绩
//    $paramers['contractid']=$contractid;//合同ID
//    $paramers['contract_type']=$rp['contract_type'];//合同类型新单，续费，升级
//    $paramers['parent_contracttypeid']=$rp['parent_contracttypeid'];
//    $paramers['contract_no']=$rp['contract_no'];
//    $paramers['servicecontractstype']=$rp['servicecontractstype'];
//    $paramers['activationcodeid']=$rp['activationcodeid'];
//    $paramers['usercode']=$rp['usercode'];
//    $paramers['customerid']=$rp['customerid'];
//    $currentContractTotal=$paramers['total']=$rp['total'];
//    $paramers['productlife']=$rp['productlife'];
//    $paramers['renewmarketprice']=$rp['renewmarketprice'];
//    $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
//    $dataResult=$Matchreceivements_Record_Model->renewOrNewadd($paramers);
//    $renewing=$dataResult['renewing'];
//    //print_r($dataResult);
//    //$dataResult=
//    //array("achievementtype"=>新单，续费,
//    //"arriveachievement"=>‘续费到账业绩’
//    //,'type'=>使用老账号 老账号 就是判断原定单里的产品是否过期1，0,
//    //'remark'=>备注,'date'=>“到期的天数，>90或小<-90”,
//    //'updateachievementtype'=>1,0老客户不再继续使用的情况下 即用了新账户开了单子);
//    $achievementtype=$dataResult['achievementtype'];
//    // 这个是合同金额
//    $generatedamount=$dataResult['arriveachievement'];//续费的前三后三判断
//    $dateBusiness=$dataResult['date'];
//    $type=$dataResult['type'];
//    if($type==1){//前三后三
//        $remark=$dataResult['remark'];
//        $rp['total']=$generatedamount;//算业绩的合同金额
//    }else{
//        $remark=$dataResult['remark'];
//    }
//    //人力成本   工单外采成本  额外成本
//    if($rp['multitype']==1){
//        $queryc="SELECT IFNULL(sum(vtiger_salesorderproductsrel.costing),0) AS costing,IFNULL(sum(vtiger_salesorderproductsrel.purchasemount),0) AS purchasemount,IFNULL(sum(vtiger_salesorderproductsrel.extracost),0) AS extracost FROM vtiger_salesorderproductsrel,vtiger_salesorderrayment WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND vtiger_salesorderrayment.receivedpaymentsid=? AND vtiger_salesorderproductsrel.salesorderid=vtiger_salesorderrayment.salesorderid AND multistatus=3 ";
//        $costingdata=$adb->pquery($queryc,array($contractid,$receivepayid));
//    }else{
//        $queryc="SELECT IFNULL(sum(vtiger_salesorderproductsrel.costing),0) AS costing,IFNULL(sum(vtiger_salesorderproductsrel.purchasemount),0) AS purchasemount,IFNULL(sum(vtiger_salesorderproductsrel.extracost),0) AS extracost FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND multistatus=3 ";
//        $costingdata=$adb->pquery($queryc,array($contractid));
//    }
//    $costingdata=$adb->query_result_rowdata($costingdata,0);
//    // 有关 沙龙 外采 媒介充值 其他
//    $queryc="SELECT extra_type,extra_price FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid=?";
//    $otherdata=$adb->pquery($queryc,array($receivepayid));
//    $otherdata=$adb->query_result_rowdata($otherdata,0);
//    $otherDataTypeArray=array();
//    foreach ($otherdata as $key=>$val){
//        $otherDataTypeArray[$val['extra_type']]=$otherDataTypeArray[$val['extra_type']]+$val['extra_price'];
//    }
//    //该服务合同回款相关的总的之和
//    $queryc="SELECT SUM(extra_price) as extra_price FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid =? ";
//    $otherdatas=$adb->pquery($queryc,array($receivepayid));
//    $otherdatas=$adb->query_result_rowdata($otherdatas,0);
//    //查询该服务合同分成人
//    $queryc=" SELECT vtiger_servicecontracts_divide.*,vtiger_departments.departmentname FROM vtiger_servicecontracts_divide  LEFT JOIN vtiger_departments ON  vtiger_departments.departmentid=vtiger_servicecontracts_divide.signdempart WHERE vtiger_servicecontracts_divide.servicecontractid = ?";
//    $resultdatas=$adb->pquery($queryc,array($contractid));
//    $insertValueStr='';
//    $i=1;
//    //已防止 备注重复
//    $remarks=$remark;
//    //查询原合同所有到账业绩和 以及原单是否是多年单
//    //$sqlquery=" SELECT SUM(arriveachievement) as oldallarriveachievement FROM vtiger_achievementallot_statistic WHERE servicecontractid =?  ";
//    $sqlquery=" SELECT more_years_renew,SUM(IF(achievementtype='newadd',1,0)) as ishasnewadd,SUM(IF(achievementtype='newadd',arriveachievement,0)) as newaddoldallarriveachievement,SUM(IF(achievementtype='newadd',1,0)) as ishasrenew,SUM(IF(achievementtype='renew',arriveachievement,0)) as renewoldallarriveachievement FROM vtiger_achievementallot_statistic WHERE servicecontractid =? ";
//    $oldarriveachievement=$adb->pquery($sqlquery,array($rp['oldcontractid']));
//    $oldarriveachievement=$adb->query_result_rowdata($oldarriveachievement,0);
//    //删除该回款已扣减记录防止重新匹配不扣减
//    $adb->pquery("DELETE  FROM  vtiger_oldachievement_hasdeduction WHERE receivedpaymentsid= ? ",array($receivepayid));
//    //查询到该回款已经新单原业绩扣减了多少  （每次回款计算完都会以回款id合同id存储记录 已经扣减了多少）
//    $alreadydeduction=" SELECT deductionmoney FROM vtiger_oldachievement_hasdeduction WHERE servicecontractsid=? AND achievementtype='newadd'  ORDER BY  id DESC LIMIT 1  ";
//    $alreadydeduction=$adb->pquery($alreadydeduction,array($contractid));
//    $newaddalreadydeduction=$adb->query_result_rowdata($alreadydeduction,0);
//    //查询到该回款已经续费原业绩扣减了多少 （每次回款计算完都会以回款id和合同id存储记录 已经扣减了多少）
//    $alreadydeduction=" SELECT deductionmoney FROM vtiger_oldachievement_hasdeduction WHERE servicecontractsid=? AND achievementtype='renew' ORDER BY  id DESC  LIMIT 1 ";
//    $alreadydeduction=$adb->pquery($alreadydeduction,array($contractid));
//    $renewalreadydeduction=$adb->query_result_rowdata($alreadydeduction,0);
//    //剩余未扣减金额为
//    //$lastdeduction=$oldarriveachievement['oldallarriveachievement']-$alreadydeduction;
//    //  新单剩余未扣减
//    $newAddLastDeduction=$oldarriveachievement['newaddoldallarriveachievement']-$newaddalreadydeduction['deductionmoney'];
//    //  续费剩余未扣减
//    $renewLastDeduction=$oldarriveachievement['renewoldallarriveachievement']-$renewalreadydeduction['deductionmoney'];
//    $deductionremark='扣减备注';
//
//    /*//如果已经扣减金额小于0则需要再扣减金额=0
//    if($lastdeduction>0){
//        $remainingamount['oldallarriveachievement']=$lastdeduction;
//        $deductionremark.="剩余扣减金额大于0";
//    }else{
//        $deductionremark.="剩余扣减金额等于0";
//        $remainingamount['oldallarriveachievement']=0;
//    }*/
//    $isUpgradeAndDeduction=0;// 是否是升级 且走了扣减逻辑 0 否 没有走扣减逻辑
//    $isChange=1; // 到最后是否修改业绩负值为0   1修改为 0 不修改为零
//    //$total//本次回款金额
//    $isrenewflag=0;//纯续费单是否要拆单
//    $waitsubarriveachievement=0;
//    $alreadyarriveachievement=0;
//    while ($rowDatas=$adb->fetch_array($resultdatas)){
//        $remark=$remarks;
//        /*  续费类型  if($i>1 && $rp['classtype']=='renew'){
//            break;
//        }*/
//        $scalling=$rowDatas['scalling'];
//        $businessunit=$total*($scalling/100);//分成后的有回款
//        $receivedpaymentownid=$rowDatas['receivedpaymentownid'];//分成人ID
//        $i++;
//        // 如果是续费 直接改成分成人为客户负责人
//        //if($rp['classtype']=='renew'){
//        //查询客户负责人
//        //$sql="SELECT smownerid FROM vtiger_crmentity WHERE  crmid=? ";
//        //$crmentity=$adb->pquery($sql,array($rp['customerid']));
//        //$crmentity=$adb->query_result_rowdata($crmentity,0);
//        //$receivedpaymentownid=$crmentity['smownerid'];
//        //$rowDatas['receivedpaymentownid']=$crmentity['smownerid'];
//        //$scalling=100;
//        //$remark.="续费单子，";
//        //}
//        // 查询分成人 所在部门  以及属事业部查询
//        $queryc=" SELECT d.departmentid,d.departmentname,d.parentdepartment,u.last_name,u.personnelpositionid FROM vtiger_user2department ud  LEFT JOIN vtiger_departments as d  ON ud.departmentid=d.departmentid LEFT JOIN  vtiger_users as u ON  u.id=ud.userid  WHERE  ud.userid= ? LIMIT 1 ";
//        $resultdataDepartment=$adb->pquery($queryc,array($receivedpaymentownid));
//        $Department=$adb->query_result_rowdata($resultdataDepartment,0);
//        // 如果老账号前三后三 则 走下面 员工类别判定 然后再确认是新单 还是续费业绩
//        if(false && $dataResult['updateachievementtype']==1){
//            // 如果是商务下单
//            if ($Department['personnelpositionid']==10071){
//                $remark.="商务下单";
//                $achievementtype='newadd';
//                // 如果是客服下单
//            }if($Department['personnelpositionid']==10069){
//                if($rp['servicecontractstype']=='新增'){
//                    $remark.="客服下单新增";
//                    $achievementtype='newadd';
//                }else{
//                    $remark.="客服下单非新增";
//                    $achievementtype='renew';
//                }
//            }
//        }
//        // 由于 2020 年 7月三号 包含三号 以前下的单子 不拆单。以后下的单子拆单处理 所以加了个时间判定条件  当前是否是多年单
//        if($rp['productlife']>1 && $achievementtype=='newadd' && $rp['createdtime']>'2020-07-04'){
//            $isMoreYears=1;// 当前单子多年单
//        }else{
//            $isMoreYears=0;
//        }
//        $departmentInfo=$Matchreceivements_Record_Model->getDepartmentInfo($Department);
//        $groupname=$departmentInfo['groupname'];
//        $departmentname=$departmentInfo['departmentname'];
//        /*$departmentGradeArray= explode("::",$Department['parentdepartment']);
//        $countDepartmentGradeArray=count($departmentGradeArray);
//        if($countDepartmentGradeArray>3){
//            if($countDepartmentGradeArray==4){
//                $groupname=$departmentname;
//                $departmentname='';
//            }else{// 目前一定是五级部门 如果销售部门级别超过5级了 要根据需求改else里的代码 获取对应级别的
//                $str="::".$Department['departmentid'];
//                $Department['parentdepartment']= str_replace($str,"",$Department['parentdepartment']);
//                $parentdepartment = explode("::",$Department['parentdepartment']);
//                $parentdepartmentId = end($parentdepartment);
//                //查询父类
//                $queryc=" SELECT departmentname FROM vtiger_departments  WHERE  departmentid = ?  limit 1 ";
//                $resultdataDepartment=$adb->pquery($queryc,array($parentdepartmentId));
//                $Departments=$adb->query_result_rowdata($resultdataDepartment,0);
//                $groupname=$Departments['departmentname'];//四级部门
//            }
//        }else{
//            $groupname='';
//            $departmentname='';
//        }*/
//
//        $costing=0;
//        $purchasemount=0;
//        $extracost = 0;
//        // 重新计算沙龙  媒体外采  没接充值  和   其他 （其他包含所有之和）
//        $salong=$otherDataTypeArray['沙龙']*($scalling/100);
//        $waici=$otherDataTypeArray['外采']*($scalling/100);
//        $meijai=$otherDataTypeArray['媒介充值']*($scalling/100);
//        //  other 指 回款（沙龙外采媒体充值其他的总和）
//        $othercost=$otherdatas['extra_price'];
//        //工单成本合计
//        $worksheetcost=0;
//        $divideworksheetcost=0;
//        $dividecosting=0;
//        $dividepurchasemount=0;
//        $divideextracost=0;
//        $divideother=$othercost*($scalling/100);//分成后的成本
//        $other=$othercost;
//
//        //到账业绩
//        $arriveachievement=0;
//        // 已分成回款
//        $unit_prices=$rp['unit_price']*($scalling/100);//分成的回款
//        // 为了处理续费的 所以加了个倍数 默认1 续费的处理中有倍数是0.5的
//        $RoyaltyMultiplie=1;
//        //公式二到账业绩 (升级（T云系列）)
//        $sqlQuery="SELECT 1 FROM vtiger_activationcode WHERE `status` in(0,1) AND contractid=? AND oldproductname LIKE '%直客%'";
//        $tempResult=$adb->pquery($sqlQuery,array($contractid));
//        $falg=true;
//        if($adb->num_rows($tempResult)){
//            $achievementtype='renew';
//            $isMoreYears=0;
//            $remark.='直客下单直接按续费核算';
//            $renewing=0;
//            $falg=false;
//        }
//        if(in_array($rp['servicecontractstype'],array('upgrade')) && $falg){
//
//            //查询原合同订单过期时间
//            $sqlquery=" SELECT a.renewmarketprice,a.expiredate,a.productlife,s.total,s.signid as oldsignid FROM vtiger_activationcode as a LEFT JOIN vtiger_servicecontracts as s ON s.servicecontractsid=a.contractid WHERE a.contractid =? AND a.productid>0 AND  a.status IN(0,1) LIMIT 1 ";
//            $oldexpiredate=$adb->pquery($sqlquery,array($rp['oldcontractid']));
//            $oldexpiredate=$adb->query_result_rowdata($oldexpiredate,0);
//            $currentTime=date("Y-m-d H:i:s");
//            $date=(strtotime($currentTime)-strtotime($oldexpiredate['expiredate']))/86400;
//            // 升级转换市场价格
//            if(!empty($productdatas['upgrademarketprice'])){
//                $rp['marketprice']=$productdatas['upgrademarketprice'];
//            }else{
//                // 这个是废弃的 以防上面没有连算都没得算 ~~
//                $rp['marketprice']=$allmarketingPrice;
//            }
//            // 升级的成本扣除数转化
//            $costdeduction=$productdatas['upgradecostprice'];
//            //已分成成本扣除数
//            $dividecostdeduction=$costdeduction*$scalling/100;
//            // 原合同金额
//            $oldContractTotal=$adb->pquery("  SELECT  total  FROM  vtiger_servicecontracts  WHERE servicecontractsid=?  ",array($rp['oldcontractid']));
//            $oldContractTotal=$adb->query_result_rowdata($oldContractTotal,0);
//            //判断是否过期 原合同订单过期时间 和 新合同订单开始时间 比较  距离到期超过3个月以上
//            if($date<-90){
//                $isUpgradeAndDeduction=1;
//                // 查询已经扣减原合同
//                if($rp['oldcontract_usedtime']<30){
//                    $oldarriveachievement=0;
//                    $remark.=' T云升级 距离到期超过90天使用时间小于三十天';
//                    $deductionremark.="使用时间小于30天";
//                    $newAddLastDeduction=0;// 使用时间小于30天不扣减
//                    $renewLastDeduction=0;//  使用时间小于30天不扣减
//                    $bukoujianyuanyeji=1;
//                }else{
//                    $bukoujianyuanyeji=0;
//                    $deductionremark.="使用时间大于30天";
//                    //要修改的地方
//                    //$oldarriveachievement=$oldarriveachievement['oldallarriveachievement']*$rp['oldcontract_usedtime']/(365*$oldexpiredate['productlife']);
//                    //$oldarriveachievement=$remainingamount['oldallarriveachievement'];
//                    $remark.='T云升级距离到期超过90天使用时间大于等于三十天';
//                }
//                $contractInfo['contractamount']=$rp['total']+$oldContractTotal['total'];
//                if($isMoreYears==1){
//                    $deductionremark.="多年单";
//                    // 原单是多年单
//                    if($oldarriveachievement['more_years_renew']==1){
//                        // 查询是否回款回完了
//                        $allunitprice=$adb->pquery(" SELECT  SUM(unit_price)  as  allunitprice  FROM  vtiger_receivedpayments  WHERE relatetoid=? and ismatchdepart=1 ",array($contractid));
//                        $allunitprice=$adb->query_result_rowdata($allunitprice,0);
//                        if($allunitprice['allunitprice']>=$currentContractTotal){
//                            $isChange=0;
//                        }
////                                $listAchievementType=1;// 原 拆单了（买了多年） 新 拆单了（买了多年）
////                                // $inparams['contractamount']=$contractInfo['contractamount'];// 合同总金额  注释掉上版本合同金额取值
////                                $inparams['contractamount']=$rp['total']+$oldContractTotal['total'];// 总的合同金额
////                                $inparams['effectiveTotal']=$buysplitcontractamount=$onemarketprice;// 计算收购单时合同金额=首购市场价格
////                                $inparams['marketprice']=$buysplitmarketprice=$onemarketprice;//    首购市场价格
////                                $inparams['costdeduction']=$buysplitcost=$onecostprice;//  首购成本
////                                $inparams['unit_price']=$rp['unit_price'];// 总回款金额
////                                $inparams['total']=$currentContractTotal;//
////                                $inparams['oldarriveachievement']=$newAddLastDeduction;// 新单剩余未减原到账业绩
////                                //首购单
////                                $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaFour($inparams);
////                                $buyArriveachievement=$resultInfo['arriveachievement'];
////                                $remain['newAddLastDeduction']=$buyArriveachievement>=0?0:-$buyArriveachievement;
////
////                                //首单减过后剩余没减原到账业绩
////                                $inparams['oldarriveachievement']=$renewLastDeduction;  //
////                                $buyRemark=$resultInfo['remark'];
////                                //续费单业绩计算参数处理
////                                $inparams['effectiveTotal']=$renewsplitcontractamount=$inparams['contractamount']-$onemarketprice;//
////                                $inparams['marketprice']=$renewsplitmarketprice=$rp['marketprice']-$onemarketprice; //
////                                $inparams['costdeduction']=$renewsplitcost=($costdeduction-$onecostprice)>0?($costdeduction-$onecostprice):0; //
////                                $deductionremark.='剩余未减续费金额'.$renewLastDeduction.'续费有效合同金额'.$inparams['effectiveTotal']."续费市场价格".$inparams['marketprice']."续费成本扣除数".$inparams['costdeduction'];
////                                $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaFour($inparams);
////                                $renewArriveachievement=$resultInfo['arriveachievement'];
////                                $remain['renewLastDeduction']=$renewArriveachievement>=0?0:-$renewArriveachievement;
////                                $renewRemark=$resultInfo['remark'];
////                                // 如果续费剩余为零
////                                if($remain['renewLastDeduction']==0 && $remain['newAddLastDeduction']>0 && $renewArriveachievement>0 && $bukoujianyuanyeji!=1){
////                                    if($renewArriveachievement-$remain['newAddLastDeduction']>0){
////                                        $renewArriveachievement=$renewArriveachievement-$remain['newAddLastDeduction'];
////                                        $remain['newAddLastDeduction']=0;
////                                    }else{
////                                        $remain['newAddLastDeduction']=$remain['newAddLastDeduction']-$renewArriveachievement;
////                                        $renewArriveachievement=0;
////                                    }
////                                }
////                                // 如果续费剩余为零
////                                if($remain['newAddLastDeduction']!=0 && $remain['renewLastDeduction']>0 && $buyArriveachievement>0 && $bukoujianyuanyeji!=1){
////                                    if($buyArriveachievement-$remain['renewLastDeduction']>0){
////                                        $buyArriveachievement=$buyArriveachievement-$remain['renewLastDeduction'];
////                                        $remain['renewLastDeduction']=0;
////                                    }else{
////                                        $remain['renewLastDeduction']=$remain['renewLastDeduction']-$buyArriveachievement;
////                                        $buyArriveachievement=0;
////                                    }
////                                }
//                        //11.20号修改逻辑，原单是多年单，现单也是多年单，拆单，互相减
//                        //先算续费
//                        $inparams['contractamount']=$rp['total']+$oldContractTotal['total'];// 总的合同金额
//                        $inparams['effectiveTotal']=$renewsplitcontractamount=$inparams['contractamount']-$onemarketprice;//
//                        $inparams['marketprice']=$renewsplitmarketprice=$rp['marketprice']-$onemarketprice; //
//                        $inparams['costdeduction']=$renewsplitcost=($costdeduction-$onecostprice)>0?($costdeduction-$onecostprice):0; //
//                        $deductionremark.='剩余未减续费金额'.$renewLastDeduction.'续费有效合同金额'.$inparams['effectiveTotal']."续费市场价格".$inparams['marketprice']."续费成本扣除数".$inparams['costdeduction'];
//                        $inparams['unit_price']=$rp['unit_price'];// 总回款金额
//                        $inparams['total']=$currentContractTotal;
//                        $inparams['oldarriveachievement']=$renewLastDeduction;
//                        $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaFourWithUpgrade($inparams);
//                        $renewArriveachievement=$resultInfo['arriveachievement'];
//                        $renewRemark=$resultInfo['remark'];
//                        $remain['renewLastDeduction']=0;
//                        //算新单
//                        $inparams['contractamount']=$rp['total']+$oldContractTotal['total'];// 总的合同金额
//                        $inparams['effectiveTotal']=$buysplitcontractamount=$onemarketprice;// 计算收购单时合同金额=首购市场价格
//                        $inparams['marketprice']=$buysplitmarketprice=$onemarketprice;//    首购市场价格
//                        $inparams['costdeduction']=$buysplitcost=$onecostprice;//  首购成本
//                        $inparams['oldarriveachievement']=$newAddLastDeduction;// 新单剩余未减原到账业绩
//                        $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaFourWithUpgrade($inparams);
//                        $buyArriveachievement=$resultInfo['arriveachievement'];
//                        $buyRemark=$resultInfo['remark'];
//                        $remain['newAddLastDeduction']=0;
//                        if($renewArriveachievement<0&&$buyArriveachievement<0){
//                            //续费业绩为负数新单也是负的,不用抵扣了，都是负的
//                            $remain['renewLastDeduction']=$renewArriveachievement;
//                            $remain['newAddLastDeduction']=$buyArriveachievement;
//                        }else if($renewArriveachievement<0&&$buyArriveachievement>=0){
//                            //续费业绩为负数新单是正的，抵扣新单，先把新单扣到0再说
//                            if($buyArriveachievement-abs($renewArriveachievement)<0){
//                                //新单也不够扣续费欠的
//                                $renewArriveachievement=$buyArriveachievement=0;
//                                $remain['renewLastDeduction']=$buyArriveachievement-abs($renewArriveachievement);
//                            }else{
//                                //新单够续费欠的
//                                $buyArriveachievement=$buyArriveachievement-abs($renewArriveachievement);
//                            }
//                        }else if($buyArriveachievement<0&&$renewArriveachievement>=0){
//                            //续费业绩为正数新单是负的，抵扣续费，先把续费扣到0再说
//                            if($renewArriveachievement-abs($buyArriveachievement)<0){
//                                //续费也不够新单
//                                $renewArriveachievement=$buyArriveachievement=0;
//                                $remain['newAddLastDeduction']=$renewArriveachievement-abs($buyArriveachievement);
//                            }else{
//                                //续费够新单欠的
//                                $renewArriveachievement=$renewArriveachievement-abs($buyArriveachievement);
//                            }
//                        }
//                    }else{
//                        //原单不是多年单
//                        // 原单是新单业绩类型
////                                if($oldarriveachievement['ishasnewadd']>0){
////                                    $listAchievementType=2;//  原 没有拆单（买了1年） 原业绩类型 新单     新 拆单了（买了多年）
////                                    // $inparams['contractamount']=$contractInfo['contractamount'];// 合同总金额  注释掉上版本合同金额取值
////                                    $inparams['contractamount']=$rp['total']+$oldContractTotal['total'];// 总的合同金额
////                                    $inparams['effectiveTotal']=$buysplitcontractamount=$onemarketprice;// 计算收购单时合同金额=首购市场价格
////                                    $inparams['marketprice']=$buysplitmarketprice=$onemarketprice;//    首购市场价格
////                                    $inparams['costdeduction']=$buysplitcost=$onecostprice;//  首购成本
////                                    $inparams['unit_price']=$rp['unit_price'];// 总回款金额
////                                    $inparams['total']=$currentContractTotal;
////                                    $inparams['oldarriveachievement']=$newAddLastDeduction;
////                                    //  首购单
////                                    $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaFour($inparams);
////                                    $buyArriveachievement=$resultInfo['arriveachievement'];
////                                    //首单减过后剩余没减原到账业绩
////                                    $inparams['oldarriveachievement']=$buyArriveachievement>=0?0:-$buyArriveachievement;
////                                    $buyRemark=$resultInfo['remark'];
////                                    //续费单业绩计算参数处理
////                                    $inparams['effectiveTotal']=$renewsplitcontractamount=$inparams['contractamount']-$onemarketprice;
////                                    $inparams['marketprice']=$renewsplitmarketprice=$rp['marketprice']-$onemarketprice;
////                                    $inparams['costdeduction']=$renewsplitcost=($costdeduction-$onecostprice)>0?($costdeduction-$onecostprice):0;
////                                    $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaFour($inparams);
////                                    $renewArriveachievement=$resultInfo['arriveachievement'];
////                                    $remain['newAddLastDeduction']=$renewArriveachievement>=0?0:-$renewArriveachievement;
////                                    $renewRemark=$resultInfo['remark'];
////                                // 原单是续费业绩类型
////                                }else{
////                                    $listAchievementType=3;//  原 没有拆单（买了1年） 原业绩类型 续费     新 拆单了（买了多年）
////                                    // $inparams['contractamount']=$contractInfo['contractamount'];// 合同总金额  注释掉上版本合同金额取值
////                                    $inparams['contractamount']=$rp['total']+$oldContractTotal['total'];// 总的合同金额
////                                    $inparams['effectiveTotal']=$buysplitcontractamount=$onemarketprice;// 计算收购单时合同金额=首购市场价格
////                                    $inparams['marketprice']=$buysplitmarketprice=$onemarketprice;//    首购市场价格
////                                    $inparams['costdeduction']=$buysplitcost=$onecostprice;//  首购成本
////                                    $inparams['unit_price']=$rp['unit_price'];// 总回款金额
////                                    $inparams['total']=$currentContractTotal;
////                                    $inparams['oldarriveachievement']=0;
////                                    //首购单
////                                    $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaFour($inparams);
////                                    $buyArriveachievement=$resultInfo['arriveachievement'];
////                                    //首单减过后剩余没减原到账业绩
////                                    $inparams['oldarriveachievement']=$renewLastDeduction;
////                                    $buyRemark=$resultInfo['remark'];
////                                    //续费单业绩计算参数处理
////                                    $inparams['effectiveTotal']=$renewsplitcontractamount=$inparams['contractamount']-$onemarketprice;
////                                    $inparams['marketprice']=$renewsplitmarketprice=$rp['marketprice']-$onemarketprice;
////                                    $inparams['costdeduction']=$renewsplitcost=($costdeduction-$onecostprice)>0?($costdeduction-$onecostprice):0;
////                                    $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaFour($inparams);
////                                    $renewArriveachievement=$resultInfo['arriveachievement'];
////                                    $remain['renewLastDeduction']=$renewArriveachievement>=0?0:-$renewArriveachievement;
////                                    $renewRemark=$resultInfo['remark'];
////                                    // 如果续费 没减完 新单去减
////                                    if($remain['renewLastDeduction']>0 && $bukoujianyuanyeji!=1){
////                                        $buyArriveachievement=$buyArriveachievement-$remain['renewLastDeduction'];
////                                        $remain['renewLastDeduction']=$buyArriveachievement>=0?0:-$buyArriveachievement;
////                                    }
////                                }
//                        //11.18修改逻辑，现订单是多年单，原订单是单年的，看原订单是续费还是新购进行扣减，原单新购不够扣续费，续费不够扣新单
//                        if($oldarriveachievement['ishasnewadd']>0){
//                            //原订单是新购，所以减新单新购的业绩，不够再减续费
//                            $listAchievementType=2;
//                            //先开始算续费的
//                            $inparams['effectiveTotal']=$renewsplitcontractamount=$oldContractTotal['total']+$contractInfo['contractprice']-$onemarketprice;
//                            $inparams['marketprice']=$renewsplitmarketprice=$rp['marketprice']-$onemarketprice;
//                            $inparams['costdeduction']=$renewsplitcost=($costdeduction-$onecostprice)>0?($costdeduction-$onecostprice):0;
//                            $inparams['unit_price']=$rp['unit_price'];// 总回款金额
//                            $inparams['oldarriveachievement']=0;//原单没续费业绩
//                            $inparams['total']=$currentContractTotal;
//                            $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaFourWithUpgrade($inparams);
//                            $renewArriveachievement=$resultInfo['arriveachievement'];
//                            $remain['renewLastDeduction']=0;//新单是多年单，原单是新单没续费的，所以新单的续费单没有要减的
//                            $renewRemark=$resultInfo['remark'];
//                            //续费算完算新单
//                            $inparams['effectiveTotal']=$buysplitcontractamount=$onemarketprice;// 计算收购单时合同金额=首购市场价格
//                            $inparams['marketprice']=$buysplitmarketprice=$onemarketprice;//首购市场成本价格
//                            $inparams['costdeduction']=$buysplitcost=$onecostprice;//首购成本
//                            $inparams['oldarriveachievement']=$newAddLastDeduction;//新单上一次留下来要扣的
//                            $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaFourWithUpgrade($inparams);
//                            $buyArriveachievement=$resultInfo['arriveachievement'];//新单业绩
//                            $remain['newAddLastDeduction']=0;
//                            $buyRemark=$resultInfo['remark'];
//                            //如果新单业绩小于0，那就再扣原单业绩，然后给新单剩余未扣除
//                            if($buyArriveachievement<0){
//                                if($renewArriveachievement-abs($buyArriveachievement)>=0){
//                                    //如果续费业绩够减，那就减去续费业绩同时没剩余要扣的
//                                    $renewArriveachievement=$renewArriveachievement-abs($buyArriveachievement);
//                                    $buyArriveachievement=0;
//                                }else{
//                                    //如果续费业绩不够减,就算到新单业绩里
//                                    $renewArriveachievement=$buyArriveachievement=0;//新购续费业绩为0
//                                    $remain['newAddLastDeduction']=$renewArriveachievement-abs($buyArriveachievement);//新购剩余扣减
//                                }
//                            }
//                        }else{
//                            //原订单是新购，所以减新单续费的业绩，不够再减新单
//                            $listAchievementType=3;
//                            $inparams['effectiveTotal']=$renewsplitcontractamount=$oldContractTotal['total']+$contractInfo['contractprice']-$onemarketprice;
//                            $inparams['marketprice']=$renewsplitmarketprice=$rp['marketprice']-$onemarketprice;
//                            $inparams['costdeduction']=$renewsplitcost=($costdeduction-$onecostprice)>0?($costdeduction-$onecostprice):0;
//                            $inparams['unit_price']=$rp['unit_price'];// 总回款金额
//                            $inparams['oldarriveachievement']=$renewLastDeduction;//原单没续费业绩
//                            $inparams['total']=$currentContractTotal;
//                            $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaFourWithUpgrade($inparams);
//                            $renewArriveachievement=$resultInfo['arriveachievement'];
//                            $remain['renewLastDeduction']=0;//新单是多年单，原单是新单没续费的，所以新单的续费单没有要减的
//                            $renewRemark=$resultInfo['remark'];
//                            //如果续费业绩小于0，算出新单还要减的业绩
//                            if($renewArriveachievement<0){
//                                $inparams['oldarriveachievement']=abs($renewArriveachievement);
//                            }else{
//                                $inparams['oldarriveachievement']=0;
//                            }
//                            //续费算完算新单
//                            $inparams['effectiveTotal']=$buysplitcontractamount=$onemarketprice;// 计算收购单时合同金额=首购市场价格
//                            $inparams['marketprice']=$buysplitmarketprice=$onemarketprice;//首购市场成本价格
//                            $inparams['costdeduction']=$buysplitcost=$onecostprice;//首购成本
//                            $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaFourWithUpgrade($inparams);
//                            $buyArriveachievement=$resultInfo['arriveachievement'];//新单业绩
//                            $remain['newAddLastDeduction']=0;
//                            $buyRemark=$resultInfo['remark'];
//                            //如果新单业绩小于0，那就再扣原单业绩，然后给新单剩余未扣除
//                            if($buyArriveachievement<0){
//                                //新单也不够扣，新单和续费业绩都没有了
//                                $renewArriveachievement=$buyArriveachievement=0;//新购续费业绩为0
//                                $remain['renewLastDeduction']=-$buyArriveachievement;
//                            }
//                        }
//                    }
//                }else{
//                    // 原  新单 全是单年单 不管业绩类型 直接减原业绩
//                    //如果新单时新单业绩
//                    /*if($achievementtype=='newadd'){*/
////                            $listAchievementType=4;//  单年
////                            $deductionremark.="正常单";
////                            $inparams['contractamount']=$rp['total']+$oldContractTotal['total'];
////                            $inparams['effectiveTotal']=$rp['total']+$oldContractTotal['total'];
////                            $inparams['marketprice']=$rp['marketprice'];//
////                            $inparams['costdeduction']=$costdeduction;//
////                            $inparams['unit_price']=$rp['unit_price'];//  总回款金额
////                            $inparams['total']=$currentContractTotal;
////                            $inparams['oldarriveachievement']=$newAddLastDeduction+$renewLastDeduction;
////                            $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaFour($inparams);
////                            $arriveachievement=$resultInfo['arriveachievement'];
////                            $remain['allAddLastDeduction']=$arriveachievement>=0?0:-$arriveachievement;
////                            $remark.=$resultInfo['remark']."合同金额";
//                    /*}else{
//                        $listAchievementType=5;// 单年
//                        $deductionremark.="正常单";
//                        $inparams['contractamount']=$rp['total']+$oldContractTotal['total'];
//                        $inparams['effectiveTotal']=$rp['total']+$oldContractTotal['total'];
//                        $inparams['marketprice']=$rp['marketprice'];//
//                        $inparams['costdeduction']=$costdeduction;//
//                        $inparams['unit_price']=$rp['unit_price'];//  总回款金额
//                        $inparams['total']=$currentContractTotal;
//                        $inparams['oldarriveachievement']=$renewLastDeduction;
//                        $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaFour($inparams);
//                        $arriveachievement=$resultInfo['arriveachievement'];
//                        $remain['renewLastDeduction']=$arriveachievement>=0?0:-$arriveachievement;
//                        $remark.=$resultInfo['remark']."合同金额";
//                    }*/
//                    //11.15修改现订单非多年单升级逻辑
//                    $listAchievementType=4;//现单是单年，原单一定是单年不管原单是续费还是新单，统统直接全扣掉原业绩
//                    $deductionremark.="正常单";
//                    //如果在三个月以内升级的直接把两次的合同全加起来算出这单业绩然后把之前算出来的业绩（无论新单还是续费）减掉
//                    //  新单剩余未扣减
//                    $newAddLastDeduction=$oldarriveachievement['newaddoldallarriveachievement']-$newaddalreadydeduction['deductionmoney'];
//                    //  续费剩余未扣减
//                    $renewLastDeduction=$oldarriveachievement['renewoldallarriveachievement']-$renewalreadydeduction['deductionmoney'];
//                    $inparams['effectiveTotal']=$oldContractTotal['total']+$contractInfo['contractprice'];//老的合同金额+客户支付的差价
//                    $inparams['costdeduction']=$costdeduction;//新的成本扣除数，实际上是$productdatas['upgradecostprice'],升级新单和续费的成本和
//                    $inparams['unit_price']=$rp['unit_price'];//已回款金额
//                    $inparams['total']=$currentContractTotal;//总回款金额
//                    $inparams['marketprice']=$rp['marketprice'];//同成本扣除数
//                    $inparams['oldarriveachievement']=$newAddLastDeduction+$renewLastDeduction;
//                    $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaFourWithUpgrade($inparams);
//                    $arriveachievement=$resultInfo['arriveachievement'];
//                    $remain['allAddLastDeduction']=$arriveachievement>=0?0:-$arriveachievement;
//                    $remark.=$resultInfo['remark']."合同金额";
//                }
//                // 到期3个月以上的  过期后升级
//            }else if($date>90){
//                //在原订单过期后3个月后升级都按新单算，分为多年单和当年单，而且先算续费，之后再算新单，计算顺序倒了，相当于绩效全部按照新单来算
//                //与以前相比，现在续费是固定的绩效，新单是非固定的，商务的合同金额比市场价多的越多赚的越多
//                if($isMoreYears==1){
////                            $inparams['contractprice']=$onemarketprice;// 合同金额
////                            $inparams['contractamount']=$contractInfo['contractamount'];// 合同总金额 + 原合同剩余金额
////                            $inparams['effectiveTotal']=$buysplitcontractamount=$onemarketprice;// 计算收购单时合同金额=首购市场价格
////                            $inparams['marketprice']=$buysplitmarketprice=$onemarketprice;//    首购市场价格
////                            $inparams['costdeduction']=$buysplitcost=$onecostprice;//  首购成本
////                            $inparams['unit_price']=$rp['unit_price'];//  总回款金额
////                            //  首购单
////                            $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaThree($inparams);
////                            $buyArriveachievement=$resultInfo['arriveachievement'];
////                            $buyRemark=$resultInfo['remark'];
////                            //  续费单业绩
////                            $inparams['contractprice']=$contractInfo['contractprice']-$onemarketprice;
////                            $inparams['effectiveTotal']=$renewsplitcontractamount=$inparams['contractamount']-$onemarketprice;
////                            $inparams['marketprice']=$renewsplitmarketprice=$rp['marketprice']-$onemarketprice;
////                            $inparams['costdeduction']=$renewsplitcost=($costdeduction-$onecostprice)>0?($costdeduction-$onecostprice):0;
////                            $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaThree($inparams);
////                            $renewArriveachievement=$resultInfo['arriveachievement'];
////                            $renewRemark=$resultInfo['remark'];
//                    //2021.11.13修改升级规则,大于原单到期时间90天之后升级的多年单
//                    //规则是先算续费后算首购，以前是先算新单后算续费
//                    //先算续费
//                    $inparams['contractTotal']=$renewsplitcontractamount=$renewmarketrenewprice;//续费市场价固定
//                    $inparams['marketPrice']=$renewsplitmarketprice=$renewmarketrenewprice;
//                    $inparams['costDeduction']=$renewsplitcost=$renewcostrenewprice;
//                    $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaThreeWithUpgrade($inparams);
//                    $renewArriveachievement=$resultInfo['arriveachievement'];
//                    $renewRemark=$resultInfo['remark'];
//                    //后算新单
//                    $inparams['contractTotal']=bcsub($rp['total'],$renewmarketrenewprice,2);//新单合同价是总合同价减去一个续费合同价
//                    $inparams['marketPrice']=$buysplitcontractamount=$buysplitmarketprice=$onemarketprice;//新单市场价
//                    $inparams['costDeduction']=$buysplitcost=$renewcostrenewprice;//新单成本价
//                    $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaThreeWithUpgrade($inparams);
//                    $buyArriveachievement=$resultInfo['arriveachievement'];
//                    $buyRemark=$resultInfo['remark'];
//                }else{
//                    //不是多年单，就算首购价
////                            $inparams['contractprice']=$contractInfo['contractprice'];//现客户支付金额
////                            $inparams['contractamount']=$contractInfo['contractamount'];//现订单合同总金额
////                            $inparams['effectiveTotal']=$contractInfo['contractamount'];//现订单合同总金额
////                            $inparams['marketprice']=$rp['marketprice'];//非多年订单市场价就是首购成本价
////                            $inparams['costdeduction']=$onecostprice;//非多年订单成本价就是首购成本价
////                            $inparams['unit_price']=$rp['unit_price'];//回款额度
////                            $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaThree($inparams);
//                    //2021.11.13修改升级规则,大于原单到期时间90天之后升级的非多年单按照新购算业绩
//                    $inparams['contractTotal']=$rp['total'];//此时回款金额和合同金额是一样的
//                    $inparams['marketPrice']=$rp['marketprice'];//非多年订单市场价就是首购成本价
//                    $inparams['costDeduction']=$onecostprice;//非多年订单成本价就是首购成本价
//                    $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaThreeWithUpgrade($inparams);
//                    $arriveachievement=$resultInfo['arriveachievement'];
//                    $remark.=$resultInfo['remark'];
//                }
//                //到期前3个月内，到期后3个月内。
//            }else{
//                // 前三后三特殊处理合同金额
//                $generatedamount=$rp['contractprice']-$oldexpiredate['renewmarketprice']*$rp['productlife'];
//                $contractInfo['contractamount']=$generatedamount+$oldContractTotal['total'];
//                if($isMoreYears==1){
////                            $inparams['contractamount']=$contractInfo['contractamount'];// 合同总金额
////                            $inparams['effectiveTotal']=$buysplitcontractamount=$onemarketprice;// 计算收购单时合同金额=首购市场价格
////                            $inparams['marketprice']=$buysplitmarketprice=$onemarketprice;//    首购市场价格
////                            $inparams['costdeduction']=$buysplitcost=$onecostprice;//  首购成本
////                            $inparams['unit_price']=$rp['unit_price'];//  总回款金额
////                            $inparams['total']=$currentContractTotal;
////                            $inparams['oldarriveachievement']=0;
////
////                            // 首购单
////                            $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaTwo($inparams);
////                            $buyArriveachievement=$resultInfo['arriveachievement'];
////                            //首单减过后剩余没减原到账业绩
////                            $inparams['oldarriveachievement']=$remainingamount['oldallarriveachievement']=$buyArriveachievement>=0?0:-$buyArriveachievement;
////                            $buyRemark=$resultInfo['remark'];
////                            //续费单业绩
////                            $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaTwo($inparams);
////                            $renewArriveachievement=$resultInfo['arriveachievement'];
////                            $renewRemark=$resultInfo['remark'];
//
//                    //2021.11.20修改前三后三多年单
//                    $inparams['contractamount']=$contractInfo['contractamount'];// 总的合同金额
//                    $inparams['effectiveTotal']=$renewsplitcontractamount=$inparams['contractamount']-$onemarketprice;//
//                    $inparams['marketprice']=$renewsplitmarketprice=$rp['marketprice']-$onemarketprice; //
//                    $inparams['costdeduction']=$renewsplitcost=($costdeduction-$onecostprice)>0?($costdeduction-$onecostprice):0; //
//                    $deductionremark.='剩余未减续费金额'.$renewLastDeduction.'续费有效合同金额'.$inparams['effectiveTotal']."续费市场价格".$inparams['marketprice']."续费成本扣除数".$inparams['costdeduction'];
//                    $inparams['unit_price']=$rp['unit_price'];// 总回款金额
//                    $inparams['total']=$currentContractTotal;
//                    $inparams['oldarriveachievement']=$renewLastDeduction;
//                    $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaFourWithUpgrade($inparams);
//                    $renewArriveachievement=$resultInfo['arriveachievement'];
//                    $renewRemark=$resultInfo['remark'];
//                    $remain['renewLastDeduction']=0;
//                    //算新单
//                    $inparams['contractamount']=$rp['total']+$oldContractTotal['total'];// 总的合同金额
//                    $inparams['effectiveTotal']=$buysplitcontractamount=$onemarketprice;// 计算收购单时合同金额=首购市场价格
//                    $inparams['marketprice']=$buysplitmarketprice=$onemarketprice;//    首购市场价格
//                    $inparams['costdeduction']=$buysplitcost=$onecostprice;//  首购成本
//                    $inparams['oldarriveachievement']=$newAddLastDeduction;// 新单剩余未减原到账业绩
//                    $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaFourWithUpgrade($inparams);
//                    $buyArriveachievement=$resultInfo['arriveachievement'];
//                    $buyRemark=$resultInfo['remark'];
//                    $remain['newAddLastDeduction']=0;
//                    if($renewArriveachievement<0&&$buyArriveachievement<0){
//                        //续费业绩为负数新单也是负的,不用抵扣了，都是负的
//                        $remain['renewLastDeduction']=$renewArriveachievement;
//                        $remain['newAddLastDeduction']=$buyArriveachievement;
//                    }else if($renewArriveachievement<0&&$buyArriveachievement>=0){
//                        //续费业绩为负数新单是正的，抵扣新单，先把新单扣到0再说
//                        if($buyArriveachievement-abs($renewArriveachievement)<0){
//                            //新单也不够扣续费欠的
//                            $renewArriveachievement=$buyArriveachievement=0;
//                            $remain['renewLastDeduction']=$buyArriveachievement-abs($renewArriveachievement);
//                        }else{
//                            //新单够续费欠的
//                            $buyArriveachievement=$buyArriveachievement-abs($renewArriveachievement);
//                        }
//                    }else if($buyArriveachievement<0&&$renewArriveachievement>=0){
//                        //续费业绩为正数新单是负的，抵扣续费，先把续费扣到0再说
//                        if($renewArriveachievement-abs($buyArriveachievement)<0){
//                            //续费也不够新单
//                            $renewArriveachievement=$buyArriveachievement=0;
//                            $remain['newAddLastDeduction']=$renewArriveachievement-abs($buyArriveachievement);
//                        }else{
//                            //续费够新单欠的
//                            $renewArriveachievement=$renewArriveachievement-abs($buyArriveachievement);
//                        }
//                    }
//
//                }else{
////                            $inparams['contractamount']=$contractInfo['contractamount'];// 合同总金额
////                            $inparams['effectiveTotal']=$contractInfo['contractamount'];//
////                            $inparams['marketprice']=$rp['marketprice'];//
////                            $inparams['costdeduction']=$costdeduction;//
////                            $inparams['unit_price']=$rp['unit_price'];//
////                            $inparams['total']=$currentContractTotal;
////                            $inparams['oldarriveachievement']=0;
////                            $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaTwo($inparams);
////                            $arriveachievement=$resultInfo['arriveachievement'];
////                            $remark.=$resultInfo['remark'];
//                    //2021.11.15修改升级规则升级时在原订单到期前三个月和到期后三个月内，进行处理，此处为现订单非多年单
//                    $inparams['contractamount']=$contractInfo['contractamount'];// 合同总金额
//                    $inparams['effectiveTotal']=$contractInfo['contractamount'];//
//                    $inparams['marketprice']=$rp['marketprice'];//
//                    $inparams['costdeduction']=$costdeduction;//
//                    $inparams['unit_price']=$rp['unit_price'];//
//                    $inparams['total']=$currentContractTotal;
//                    $inparams['oldarriveachievement']=0;
//                    $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaTwoWithUpgrade($inparams);
//                    $arriveachievement=$resultInfo['arriveachievement'];
//                    $remark.=$resultInfo['remark'];
//                }
//            }
//            $producttype=2;
//            //公式一到账业绩 (T云系列计算)
//        }elseif($renewing==1){//前三后三内计算公式
//            $dividecostdeduction=$costdeduction*$scalling/100;
//            $renewsplitcontractamount = 0;
//            $buysplitcontractamount = 0;
//            $renewsplitmarketprice = 0;
//            $buysplitmarketprice = 0;
//            $renewsplitcost = 0;
//            $buysplitcost=0;
//            $tempproductlife=$rp['productlife']>0?$rp['productlife']:1;
//            $costdeduction=$onecostrenewprice;
//            $TempData=$Matchreceivements_Record_Model->calcTYUNFirstANDLastThreeMonths(array(
//                'noseparaterenewmarketprice'=>$tempproductlife*$productdatas['noseparaterenewmarketprice'],
//                'noseparaterenewcosttprice'=>$tempproductlife*$productdatas['noseparaterenewcosttprice'],
//                'separaterenewmarketprice'=>$productdatas['separaterenewmarketprice'],
//                'separaterenewcosttprice'=>$productdatas['separaterenewcosttprice'],
//                'effectiveTotal'=>$rp['total'],
//                'marketprice'=>$rp['marketprice'],
//                'costdeduction'=>$costdeduction,
//                'unit_price'=>$rp['unit_price']
//            ));
//            $remark.=$TempData['remark'];
//            $waitsubarriveachievement=$TempData['waitsubarriveachievement'];
//            $alreadyarriveachievement=$TempData['alreadyarriveachievement'];
//            if($TempData['type']==1){//没有拆单
//                $arriveachievement=$renewArriveachievement=$TempData['renewarriveachievement'];
//            }else{
//                $renewArriveachievement=$TempData['renewarriveachievement'];
//                $buyArriveachievement=$TempData['newaddarriveachievement'];
//                $neweffectiverefund=$TempData['neweffectiverefund'];
//                $reneweffectiverefund=$TempData['reneweffectiverefund'];
//                $isMoreYears=1;
//                $isrenewflag=1;
//            }
//
//        }else{
//            //已分成成本扣除数--总成本
//            $dividecostdeduction=$costdeduction*$scalling/100;
//            // 要修改的地方
//            if($rp['classtype']=='renew' OR $rp['classtype']=='degrade'){
//                //查询原合同信息
//                $sqlquery=" SELECT a.expiredate,a.productlife,s.total,s.signid as oldsignid,a.contractid FROM vtiger_activationcode as a LEFT JOIN vtiger_servicecontracts as s ON s.servicecontractsid=a.contractid WHERE a.usercode =? AND classtype='buy' AND a.productid>0 AND a.status IN(0,1)  AND  a.contractid<>?  LIMIT 1 ";
//                $oldContractInfo=$adb->pquery($sqlquery,array($rp['usercode'],$contractid));
//                $oldContractInfo=$adb->query_result_rowdata($oldContractInfo,0);
//                $currentContractTotal1=$rp['total'];//当前的合同金额
//                $currentunitPrice=$rp['unit_price'];//当前的回款金额
//                $currentunitPricediff=0;//当前的合同金额
//                if($isMoreYears==1){//多年单
//                    $inparams['total']=$currentContractTotal;// 合同总金额
//                    $inparams['effectiveTotal']=$buysplitcontractamount=($rp['total']-$onemarketrenewprice)>0?($rp['total']-$onemarketrenewprice):0;// 计算收购单时合同金额=首购市场价格
//                    $inparams['marketprice']=$buysplitmarketprice=$rp['marketprice']-$onemarketrenewprice;//    首购市场价格
//                    $inparams['costdeduction']=$buysplitcost=$costdeduction-$onecostprice;//  首购成本
//                    $inparams['unit_price']=$rp['unit_price'];//  总回款金额
//                    //拆分 首购单到账业绩
//                    //如果合同金额小于首购市场价格
//                    if(false && $rp['total']<$onemarketprice){
//                        /*$inparams['costdeduction']=$costdeduction;
//                        $buysplitcost=$costdeduction;
//                        $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaRenew($inparams);
//                        $buyArriveachievement=$resultInfo['arriveachievement'];
//                        $buyRemark=$resultInfo['remark'];
//                        // 拆分 续费单到账业绩
//                        $renewsplitcontractamount=0;
//                        $renewsplitmarketprice=0;
//                        $renewsplitcost=0;
//                        $renewArriveachievement=0;
//                        $renewRemark='续费单被判定多年单业绩合同金额小于首购市场价格 续费业绩为0';*/
//                    }else{
//                        $discount=$rp['total']/$rp['marketprice']>1?1:$rp['total']/$rp['marketprice'];
//                        $TempData=$Matchreceivements_Record_Model->calcTYUNRenewANDNewAddMoreYear(array(
//                            'contractid'=>$contractid,
//                            'receivepayid'=>$receivepayid,
//                            'costdeduction'=>$costdeduction,
//                            'onemarketrenewprice'=>$onemarketrenewprice,
//                            'onecostrenewprice'=>$onecostrenewprice,
//                            'unit_price'=>$rp['unit_price'],
//                            'total'=>$rp['total'],
//                            'marketprice'=>$rp['marketprice'],
//                            'extracost'=>$costingdata['extracost'],
//                            'extra_price'=>$otherdatas['extra_price'],
//                            'discount'=>$discount,
//                        ),'getArriveachievementByFormulaOne1');
//                        foreach($TempData as $key=>$value){
//                            $$key=$value;
//                        }
//                    }
//                }elseif($achievementtype=='newadd'){//续费按新单业绩计算
//                    $inparams['total']=$currentContractTotal;
//                    $inparams['effectiveTotal']=$rp['total'];
//                    $inparams['marketprice']=$rp['marketprice'];
//                    $inparams['costdeduction']=$costdeduction;
//                    $inparams['unit_price']=$rp['unit_price'];
//                    $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaOne($inparams);
//                    $arriveachievement=$resultInfo['arriveachievement'];
//                    $transRemark=$resultInfo['remark'];
//                    $remark.=$resultInfo['remark'];
//                }else{//纯续费
//                    if($productdatas['othermarketprice']>0){//有另购
//                        /* $productdatas['othermarketprice']=0;//另购产品的续费总市场价  新购续费市场价 *（年限-1）
//                         $productdatas['othermarketrenewprice']=0;//另购产品的续费总市场价  新购续费市场价 *（年限-1）
//                         $productdatas['othercostrenewprice']=0;//另购产品的总成本价
//                         $productdatas['othercostaddprice']=0;//另购产品的首购总成本价  另购首购成本
//                         $productdatas['renewmarketrenewprice']=0;//续费产品的总成本价  续费市场价*续费年限
//                         $productdatas['renewcostrenewprice']=0;//续费产品的总成本价*/
//                        $TempData=$Matchreceivements_Record_Model->getArriveachievementByFormulaTYUNReNew(array('contractid'=>$contractid,
//                            'othermarketprice'=>$productdatas['othermarketprice'],
//                            'othermarketrenewprice'=>$productdatas['othermarketrenewprice'],
//                            'othercostrenewprice'=>$productdatas['othercostrenewprice'],
//                            'othercostaddprice'=>$productdatas['othercostaddprice'],
//                            'renewmarketrenewprice'=>$productdatas['renewmarketrenewprice'],
//                            'renewcostrenewprice'=>$productdatas['renewcostrenewprice'],
//                            'receivepayid'=>$receivepayid,
//                            'costdeduction'=>$costdeduction,
//                            'onemarketrenewprice'=>$onemarketrenewprice,
//                            'onecostrenewprice'=>$onecostrenewprice,
//                            'unit_price'=>$rp['unit_price'],//回款总额
//                            'total'=>$rp['total'],//合同金额
//                            'marketprice'=>$rp['marketprice'],));
//                        foreach($TempData as $key=>$value){
//                            $$key=$value;
//                        }
//                        $remark.=$renewRemark;
//                        $isMoreYears=1;
//                        $isrenewflag=1;
//                    }else{
//                        $inparams['total']=$rp['total'];
//                        $inparams['effectiveTotal']=$rp['total'];
//                        $inparams['marketprice']=$rp['marketprice'];
//                        $inparams['costdeduction']=$costdeduction;
//                        $inparams['unit_price']=$rp['unit_price'];
//                        $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaOne($inparams);
//                        $arriveachievement=$resultInfo['arriveachievement'];
//                        $remark.=$resultInfo['remark'];
//                    }
//
//                }
//                $sqlquery=" SELECT GROUP_CONCAT(receivedpaymentownid) as receivedpaymentownids FROM vtiger_servicecontracts_divide WHERE servicecontractid=? ";
//                $oldContractDivide=$adb->pquery($sqlquery,array($oldContractInfo['contractid']));
//                $oldContractDivide=$adb->query_result_rowdata($oldContractDivide,0);
//                $divideArray=explode(',',$oldContractDivide['receivedpaymentownids']);
//
//
//                $sqlquery=" SELECT GROUP_CONCAT(receivedpaymentownid) as receivedpaymentownids FROM vtiger_servicecontracts_divide WHERE servicecontractid=? ";
//                $newContractDivide=$adb->pquery($sqlquery,array($contractid));
//                $newContractDivide=$adb->query_result_rowdata($newContractDivide,0);
//                $newdivideArray=explode(',',$newContractDivide['receivedpaymentownids']);
//
//                $tmmnewdivideArray=array_intersect($newdivideArray,$divideArray);
//
//                // 有没有是客户负责人
//                $sql="SELECT smownerid FROM vtiger_crmentity WHERE  crmid=? AND smownerid IN(SELECT receivedpaymentownid FROM vtiger_servicecontracts_divide WHERE servicecontractid =?)";
//                $crmentity=$adb->pquery($sql,array($rp['customerid'],$contractid));
//                $crmentityRows=$adb->num_rows($crmentity);
//                $currentTime=date("Y-m-d",strtotime("-2 months",time()));
//                //判断该客户负责人两个月内有没有变更记录
//                $sql="SELECT count(1) as counts  FROM vtiger_modtracker_basic as mb INNER JOIN vtiger_modtracker_detail as md  ON md.id=mb.id WHERE  mb.crmid=? AND md.fieldname='assigned_user_id' AND LEFT(mb.changedon,10) >= ? ";
//                $result=$adb->pquery($sql,array($rp['sc_related_to'],$currentTime));
//                $result=$adb->query_result_rowdata($result,0);
//                // 查询所有的记录
//                $sql="SELECT * FROM vtiger_modtracker_basic as mb INNER JOIN vtiger_modtracker_detail as md  ON md.id=mb.id WHERE  mb.crmid=? AND md.fieldname='assigned_user_id' ";
//                $allrows=$adb->pquery($sql,array($rp['sc_related_to']));
//                $allrows=$adb->num_rows($allrows,0);
//                //判断当前分成人和原合同分成人是否一致
//                if(!empty($tmmnewdivideArray)){
//                    $remark.="（当前分成人和原分成人一致）";
//
//                }elseif($crmentityRows>0 && $result['counts']<=0 && $allrows>1){
//                    $remark.="  （两个月内没有客户负责人变更记录提成给一半但与购买订单不是同一人）";
//                    $RoyaltyMultiplie=0.5;
//                    //不一致
//                }else{
//                    //分成人有没有是客户负责人的
//                    $sql=" SELECT *  FROM vtiger_modtracker_basic as mb INNER JOIN vtiger_modtracker_detail as md  ON md.id=mb.id WHERE  mb.crmid=? AND md.fieldname='assigned_user_id' AND  postvalue IN(SELECT receivedpaymentownid FROM vtiger_servicecontracts_divide WHERE servicecontractid =?) ";
//                    $numrows=$adb->pquery($sql,array($rp['sc_related_to'],$contractid));
//                    $numrows=$adb->num_rows($numrows);
//                    if($allrows==1 && !empty($numrows)){
//                        $remark.="客户负责人没有变更过";
//                        //如果两个月内有变更记录或者两个分成人都不是客户负责人  则不计算业绩
//                    }else if($result['counts']>0 || $numrows==0){
//                        $remark.="两个月内有变更记录或者两个分成人都不是客户负责人";
//                        $buyArriveachievement=0;
//                        $renewArriveachievement=0;
//                        $arriveachievement=0;
//                        //  如果两个月内没有客户负责人变更记录提成给一半
//                    }else{
//                        $RoyaltyMultiplie=0.5;
//                        $remark.="两个月内没有客户负责人变更记录提成给一半";
//                    }
//                }
//
//            }else{
//                // 如果是多年单子 则计算公式计算两种 一次俺首年合同金额和市场价格   一次俺续费合同金额和续费市场价格
//                if($isMoreYears==1){
//                    // 如果合同金额小于首购市场价格
//                    /* echo $contractInfo['total'],'aaaa',$rp['marketprice'];
//                    exit;*/
//                    if($rp['total']/$rp['marketprice']>=0.75) {
//                        if ($currentContractTotal < $onemarketprice) {
//                            $inparams['total'] = $currentContractTotal;
//                            $inparams['marketprice'] = $buysplitmarketprice = $onemarketprice;
//                            $inparams['costdeduction'] = $buysplitcost = $costdeduction;
//                            $inparams['unit_price'] = $rp['unit_price'];
//                            $resultInfo = $Matchreceivements_Record_Model->getArriveachievementByFormulaZero($inparams);
//                            $buyArriveachievement = $resultInfo['arriveachievement'];
//
//                            $renewsplitcontractamount = 0;
//                            $renewsplitmarketprice = 0;
//                            $renewsplitcost = 0;
//                            $renewArriveachievement = 0;
//                        } else {
//                            print_r(array(
//                                'contractid'=>$contractid,
//                                'costdeduction'=>$costdeduction,
//                                'onemarketrenewprice'=>$onemarketrenewprice,
//                                'onecostrenewprice'=>$onecostrenewprice,
//                                'unit_price'=>$rp['unit_price'],
//                                'total'=>$rp['total'],
//                                'marketprice'=>$rp['marketprice'],
//                                'extracost'=>$costingdata['extracost'],
//                                'extra_price'=>$otherdatas['extra_price'],
//                            ));
//                            $TempData=$Matchreceivements_Record_Model->calcTYUNRenewANDNewAddMoreYear(array(
//                                'contractid'=>$contractid,
//                                'receivepayid'=>$receivepayid,
//                                'costdeduction'=>$costdeduction,
//                                'onemarketrenewprice'=>$onemarketrenewprice,
//                                'onecostrenewprice'=>$onecostrenewprice,
//                                'unit_price'=>$rp['unit_price'],
//                                'total'=>$rp['total'],
//                                'marketprice'=>$rp['marketprice'],
//                                'extracost'=>$costingdata['extracost'],
//                                'extra_price'=>$otherdatas['extra_price'],
//                            ),'getArriveachievementByFormulaOne1');
//                            foreach($TempData as $key=>$value){
//                                $$key=$value;
//                            }
//                        }
//                    }else{
//                        //小于7.5拆
//                        $buyArriveachievement = 0;
//                        $renewsplitcontractamount = 0;
//                        $buysplitcontractamount = 0;
//                        $renewsplitmarketprice = 0;
//                        $buysplitmarketprice = 0;
//                        $renewsplitcost = 0;
//                        $buysplitcost=0;
//                        $renewArriveachievement = 0;
//                        $buyRemark=' 合同总额/总市场价小于0.75';
//                    }
//
//                }else{
//                    $inparams['total']=$currentContractTotal;
//                    $inparams['effectiveTotal']=$rp['total'];
//                    $inparams['marketprice']=$rp['marketprice'];
//                    $inparams['costdeduction']=$costdeduction;
//                    $inparams['unit_price']=$rp['unit_price'];
//                    $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaOne($inparams);
//                    $arriveachievement=$resultInfo['arriveachievement'];
//                    $transRemark=$resultInfo['remark'];
//                    $remark.=$resultInfo['remark'];
//                }
//
//                $remark.="T云非升级非续费订单正常业绩计算公式";
//            }
//
//            $contractInfo['contractamount']=$rp['total'];
//            $producttype=1;
//        }
//        $dividetotal=$contractInfo['contractamount']*$scalling/100;
//        //如果是多年单
//        if($isMoreYears==1){
//            $remark.="多年单";
//            $more_years_renew=1;
//            for ($i=0;$i<2;$i++){
//                $effectiverefund=0;
//                if($rp['total']/$rp['marketprice']>=0.75){
//                    $effectiverefund=$rp['unit_price']*$scalling/100;
//                }
//                // 拆单首购
//                if($i==0){
//                    $arriveachievement=$buyArriveachievement;
//                    $updateremark=$remark.$buyRemark;
//                    $updateachievementtype='newadd';
//                    $splitcontractamount=$buysplitcontractamount;
//                    $splitmarketprice=$buysplitmarketprice;
//                    $splitcost=$buysplitcost;
//                    $others=$other;
//                    $divideothers=$divideother;
//                    $splitbusinessunit=$newaddsplitbusinessunit;
//                    if($rp['total']/$rp['marketprice']>=0.75 && $isrenewflag==1){
//                        $effectiverefund=$neweffectiverefund*$scalling/100;
//                    }
//                    // 拆单续费
//                }else if($i==1){
//                    $arriveachievement=$renewArriveachievement;
//                    $updateremark=$remark.$renewRemark;
//                    $updateachievementtype='renew';
//                    $splitcontractamount=$renewsplitcontractamount;
//                    $splitmarketprice=$renewsplitmarketprice;
//                    $splitcost=$renewsplitcost;
//                    $others=0;
//                    $divideothers=0;
//                    $splitbusinessunit=$renewsplitbusinessunit;
//                    if($rp['total']/$rp['marketprice']>=0.75 && $isrenewflag==1){
//                        $effectiverefund=$reneweffectiverefund*$scalling/100;
//                    }
//                }
//                //多年单循环拆单。
//                //已分成业绩市场价
//                $dividemarketprice=$rp['marketprice']*$scalling/100;
//                $receivedpaymentown=$Department['last_name'];
//                if(in_array($rp['productid'],array(24,25))){
//                    $arriveachievement=$RoyaltyMultiplie*($arriveachievement- $costingdata['extracost'])*$scalling/100;
//                }else{
//                    $arriveachievement=$RoyaltyMultiplie*$arriveachievement*$scalling/100;
//                }
//                // 到账业绩减去pos手续费  如果是首年减去手续费
//                if(($i==0 && $buyArriveachievement>0) || ($i==1 && $buyArriveachievement==0 && $renewArriveachievement>0)){
//                    $arriveachievement=$arriveachievement-$otherdatas['extra_price']*$scalling/100;
//                    $others=$other>0?$other:0;
//                    $divideothers=$divideother;
//                }else{
//                    $others=0;
//                    $divideothers=0;
//                }
//                // 最后判断下到账回款 是否为负值 如果为负 则设为零
//                if($arriveachievement<0 && $isChange==1){ // 是否要修改为零 如果升级且原单拆单，新单拆单回款完了还是负值那么不修改
//                    $arriveachievement=0;
//                }
//                // 如果是多年单续费，或者 直接续费单需要计算提成点
//                if($more_years_renew==1 && $updateachievementtype=='renew'){
//                    // 查询该客户有几次续费单了
//                    //$sql="SELECT count(1) as numbers  FROM	vtiger_activationcode AS a LEFT JOIN vtiger_achievementallot_statistic AS aas ON a.contractid = aas.servicecontractid WHERE	a.usercode =?  AND aas.achievementmonth>0  AND a.contractid <> ? AND ( aas.achievementtype = 'renew' OR ( aas.achievementtype = 'newadd' and  aas.more_years_renew=1 ) )GROUP BY a.contractid";
//                    $sql="SELECT count(1) as numbers  FROM	vtiger_activationcode AS a LEFT JOIN vtiger_achievementallot_statistic AS aas ON a.contractid = aas.servicecontractid WHERE	a.usercode =?  AND aas.achievementmonth>0  AND a.contractid <> ? AND ( aas.achievementtype = 'renew' AND aas.more_years_renew=0 )GROUP BY a.contractid";
//                    $severalRenewals=$adb->pquery($sql,array($rp['usercode'],$contractid));
//                    $severalRenewals=$adb->num_rows($severalRenewals);
//                    $severalRenewals=$severalRenewals;
//                    if($isrenewflag==0){
//                        $renewal_commission=$renewalBase;
//                    }else{
//                        $renewal_commission=6*pow(0.5 ,$severalRenewals);
//                    }
//                    $renewtimes=$severalRenewals+1;
//                    $commissionforrenewal=$arriveachievement*$renewal_commission/100;
//                }else{
//                    $commissionforrenewal=0;
//                    $renewal_commission=0;
//                    $renewtimes=0;
//                }
//                $ismoreYears=$more_years_renew;
//                if($isrenewflag==1){
//                    $ismoreYears=0;
//                }
//
//                $discountRate=$Matchreceivements_Record_Model->isArriveachievementDiscount($receivepayid,$arriveachievement);
//                if($discountRate==0){
//                    $arriveachievement=0;
//                    $updateremark.='跨月业绩为0';
//                }else if($discountRate==0.9){
//                    $arriveachievement*=0.9;
//                    $updateremark.='超时业绩打9折';
//                }
//                $datavalue=array();
//                $datavalue['owncompanys']=$rowDatas['owncompanys'];
//                $datavalue['receivedpaymentownid']=$receivedpaymentownid;
//                $datavalue['scalling']=$scalling;
//                $datavalue['servicecontractid']=$rowDatas['servicecontractid'];
//                $datavalue['receivedpaymentsid']=$receivepayid;
//                $datavalue['businessunit']=$businessunit;
//                $datavalue['matchdate']=$matchdate;
//                $datavalue['departmentid']=$Department['departmentid']?$Department['departmentid']:0;
//                $datavalue['owncompany']=$rp['owncompany'];
//                $datavalue['createtime']=$rp['createtime'];
//                $datavalue['reality_date']=$rp['reality_date'];
//                $datavalue['paytitle']=$rp['paytitle'];
//                $datavalue['unit_price']=$rp['unit_price'];
//                $datavalue['unit_prices']=$unit_prices;
//                $datavalue['department']=$rowDatas['departmentname']?$rowDatas['departmentname']:'';
//                $datavalue['groupname']=$groupname?$groupname:' ';
//                $datavalue['departmentname']=$departmentname?$departmentname:' ';
//                $datavalue['receivedpaymentown']=$receivedpaymentown;
//                $datavalue['servicecontractstype']=$rp['servicecontractstype']?$rp['servicecontractstype']:' ';
//                $datavalue['accountname']=$rp['accountname']?$rp['accountname']:' ';
//                $datavalue['signdate']=$rp['signdate'];
//                $datavalue['contract_no']=$rp['contract_no'];
//                $datavalue['total']=$contractInfo['contractamount']?$contractInfo['contractamount']:0;
//                $datavalue['dividetotal']=$dividetotal;
//                $datavalue['costing']=$costing;
//                $datavalue['purchasemount']=$purchasemount?$purchasemount:0;
//                $datavalue['worksheetcost']=$worksheetcost?$worksheetcost:0;
//                $datavalue['productlife']=$rp['productlife']?$rp['productlife']:0;
//                $datavalue['marketprice']=$rp['marketprice']?$rp['marketprice']:0;
//                $datavalue['dividemarketprice']=$dividemarketprice;
//                $datavalue['costdeduction']=$costdeduction;
//                $datavalue['dividecostdeduction']=$dividecostdeduction;
//                $datavalue['other']=$others;
//                $datavalue['effectiverefund']=$effectiverefund;
//                $datavalue['arriveachievement']=$arriveachievement;
//                $datavalue['achievementmonth']=$achievementmonth;
//                $datavalue['modulestatus']=$modulestatus;
//                $datavalue['productname']=$productname;
//                $datavalue['achievementtype']=$updateachievementtype?$updateachievementtype:0;
//                $datavalue['producttype']=$producttype?$producttype:0;
//                $datavalue['extracost']=$extracost;
//                $datavalue['salong']=$salong;
//                $datavalue['waici']=$waici;
//                $datavalue['meijai']=$meijai;
//                $datavalue['othercost']=$othercost;
//                $datavalue['shareuser']=$shareuser;
//                $datavalue['remarks']=$updateremark;
//                $datavalue['generatedamount']=$generatedamount;
//                $datavalue['adjustbeforearriveachievement']=$arriveachievement;
//                $datavalue['divideworksheetcost']=$divideworksheetcost;
//                $datavalue['dividecosting']=$dividecosting;
//                $datavalue['dividepurchasemount']=$dividepurchasemount;
//                $datavalue['divideextracost']=$divideextracost;
//                $datavalue['divideother']=$divideothers;
//                $datavalue['more_years_renew']=$ismoreYears;
//                $datavalue['renewal_commission']=$renewal_commission;
//                $datavalue['renewtimes']=$renewtimes;
//                //$datavalue['splitcontractamount']=$splitcontractamount*$scalling/100;
//                $datavalue['splitcontractamount']=$splitcontractamount;
//                $datavalue['splitmarketprice']=$splitmarketprice*$scalling/100;
//                $datavalue['splitcost']=$splitcost*$scalling/100;
//                $datavalue['commissionforrenewal']=$commissionforrenewal;
//                $datavalue['splitbusinessunit']=$splitbusinessunit*$scalling/100;//拆分回款
//                $datavalue['activityname']=$rp['activityname'];//活动名称
//                $datavalue['activitytype']=$rp['activitytype'];//活动类型
//                $datavalue['waitsubarriveachievement']=$waitsubarriveachievement;//扣减业绩
//                $datavalue['alreadyarriveachievement']=$alreadyarriveachievement;//已扣减业绩
//
//                $insertValueStrArray[]=$datavalue;
//                //$insertValueStr.='(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?),';
//            }
//        }else{
//            //已分成业绩市场价
//            $dividemarketprice=$rp['marketprice']*$scalling/100;
//            $receivedpaymentown=$Department['last_name'];
//            if(in_array($rp['productid'],array(24,25))){
//                $arriveachievement=$RoyaltyMultiplie*($arriveachievement- $costingdata['extracost'])*$scalling/100;
//            }else{
//                $arriveachievement=$RoyaltyMultiplie*$arriveachievement*$scalling/100;
//            }
//
//            // 到账业绩减去pos手续费
//            $arriveachievement=$arriveachievement-$otherdatas['extra_price']*$scalling/100;
//
//            // 最后判断下到账回款 是否为负值 如果为负 则设为零
//            if($arriveachievement<0){
//                $arriveachievement=0;
//            }
//            // 是否多年续费单
//            $more_years_renew=0;
//            // 如果业绩类型是续费，或者 直接续费单需要计算提成点
//            if($achievementtype=='renew'){
//                // 查询该客户有几次续费单了
//                //$sql="SELECT count(1) as numbers  FROM	vtiger_activationcode AS a LEFT JOIN vtiger_achievementallot_statistic AS aas ON a.contractid = aas.servicecontractid WHERE	a.usercode =?  AND aas.achievementmonth>0  AND a.contractid <> ? AND ( aas.achievementtype = 'renew' OR ( aas.achievementtype = 'newadd' and  aas.more_years_renew=1 ) )GROUP BY	a.contractid ";
//                $sql="SELECT count(1) as numbers  FROM	vtiger_activationcode AS a LEFT JOIN vtiger_achievementallot_statistic AS aas ON a.contractid = aas.servicecontractid WHERE	a.usercode =?  AND aas.achievementmonth>0  AND a.contractid <> ? AND  aas.achievementtype = 'renew' and  aas.more_years_renew=0 GROUP BY	a.contractid ";
//                $severalRenewals=$adb->pquery($sql,array($rp['usercode'],$contractid));
//                $severalRenewals=$adb->num_rows($severalRenewals);
//                $severalRenewals=$severalRenewals;
//                $renewal_commission=6*pow(0.5 ,$severalRenewals);
//                $renewtimes=$severalRenewals+1;
//                $commissionforrenewal=$arriveachievement*$renewal_commission/100;
//            }else{
//                $renewal_commission=0;
//                $renewtimes=0;
//                $commissionforrenewal=0;
//            }
//
//            $effectiverefund=$rp['unit_price']*$scalling/100;
//            if($achievementtype!='renew' && $rp['total']/$rp['marketprice']<0.75){
//                $effectiverefund=0;
//            }
//
//            $discountRate=$Matchreceivements_Record_Model->isArriveachievementDiscount($receivepayid,$arriveachievement);
//            if($discountRate==0){
//                $arriveachievement=0;
//                $updateremark.='跨月业绩为0';
//            }else if($discountRate==0.9){
//                $arriveachievement*=0.9;
//                $updateremark.='超时业绩打9折';
//            }
//
//            $splitcontractamount=0;
//            $splitmarketprice=0;
//            $splitcost=0;
//            $datavalue=array();
//            $datavalue['owncompanys']=$rowDatas['owncompanys'];
//            $datavalue['receivedpaymentownid']=$receivedpaymentownid;
//            $datavalue['scalling']=$scalling;
//            $datavalue['servicecontractid']=$rowDatas['servicecontractid'];
//            $datavalue['receivedpaymentsid']=$receivepayid;
//            $datavalue['businessunit']=$businessunit;
//            $datavalue['matchdate']=$matchdate;
//            $datavalue['departmentid']=$Department['departmentid']?$Department['departmentid']:0;
//            $datavalue['owncompany']=$rp['owncompany'];
//            $datavalue['createtime']=$rp['createtime'];
//            $datavalue['reality_date']=$rp['reality_date'];
//            $datavalue['paytitle']=$rp['paytitle'];
//            $datavalue['unit_price']=$rp['unit_price'];
//            $datavalue['unit_prices']=$unit_prices;
//            $datavalue['department']=$rowDatas['departmentname']?$rowDatas['departmentname']:'';
//            $datavalue['groupname']=$groupname?$groupname:' ';
//            $datavalue['departmentname']=$departmentname?$departmentname:' ';
//            $datavalue['receivedpaymentown']=$receivedpaymentown;
//            $datavalue['servicecontractstype']=$rp['servicecontractstype']?$rp['servicecontractstype']:' ';
//            $datavalue['accountname']=$rp['accountname']?$rp['accountname']:' ';
//            $datavalue['signdate']=$rp['signdate'];
//            $datavalue['contract_no']=$rp['contract_no'];
//            $datavalue['total']=$contractInfo['contractamount']?$contractInfo['contractamount']:0;
//            $datavalue['dividetotal']=$dividetotal;
//            $datavalue['costing']=$costing;
//            $datavalue['purchasemount']=$purchasemount?$purchasemount:0;
//            $datavalue['worksheetcost']=$worksheetcost?$worksheetcost:0;
//            $datavalue['productlife']=$rp['productlife']?$rp['productlife']:0;
//            $datavalue['marketprice']=$rp['marketprice']?$rp['marketprice']:0;
//            $datavalue['dividemarketprice']=$dividemarketprice;
//            $datavalue['costdeduction']=$costdeduction;
//            $datavalue['dividecostdeduction']=$dividecostdeduction;
//            $datavalue['other']=$other;
//            $datavalue['effectiverefund']=$effectiverefund;
//            $datavalue['arriveachievement']=$arriveachievement;
//            $datavalue['achievementmonth']=$achievementmonth;
//            $datavalue['modulestatus']=$modulestatus;
//            $datavalue['productname']=$productname;
//            $datavalue['achievementtype']=$achievementtype?$achievementtype:0;
//            $datavalue['producttype']=$producttype?$producttype:0;
//            $datavalue['extracost']=$extracost;
//            $datavalue['salong']=$salong;
//            $datavalue['waici']=$waici;
//            $datavalue['meijai']=$meijai;
//            $datavalue['othercost']=$othercost;
//            $datavalue['shareuser']=$shareuser;
//            $datavalue['remarks']=$remark;
//            $datavalue['generatedamount']=$generatedamount;
//            $datavalue['adjustbeforearriveachievement']=$arriveachievement;
//            $datavalue['divideworksheetcost']=$divideworksheetcost;
//            $datavalue['dividecosting']=$dividecosting;
//            $datavalue['dividepurchasemount']=$dividepurchasemount;
//            $datavalue['divideextracost']=$divideextracost;
//            $datavalue['divideother']=$divideother;
//            $datavalue['more_years_renew']=$more_years_renew;
//            $datavalue['renewal_commission']=$renewal_commission;
//            $datavalue['renewtimes']=$renewtimes;
//            $datavalue['splitcontractamount']=$splitcontractamount;
//            $datavalue['splitmarketprice']=$splitmarketprice;
//            $datavalue['splitcost']=$splitcost;
//            $datavalue['commissionforrenewal']=$commissionforrenewal;
//            $datavalue['splitbusinessunit']=$unit_prices;//拆分回款
//            $datavalue['activityname']=$rp['activityname'];//活动名称
//            $datavalue['activitytype']=$rp['activitytype'];//活动类型
//            $datavalue['waitsubarriveachievement']=$waitsubarriveachievement;//扣减业绩
//            $datavalue['alreadyarriveachievement']=$alreadyarriveachievement;//已扣减业绩
//            $insertValueStrArray[]=$datavalue;
//            //$insertValueStr.='(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?),';
//        }
//    }
//    // 是升级的
//    if(in_array($rp['servicecontractstype'],array('upgrade'))){
//        // 且走了   扣减原业绩
//        if($isUpgradeAndDeduction==1){
//            $param=array('contractid'=>$contractid,'oldcontractid'=>$rp['oldcontractid'],'receivepayid'=>$receivepayid,'alreadydeduction'=>$oldarriveachievement['newaddoldallarriveachievement']-$remain['newAddLastDeduction'],'totaldeductionmoney'=>$oldarriveachievement['newaddoldallarriveachievement'],'deductionremark'=>$deductionremark,'listAchievementType'=>'newadd');
//            //原单多年单  新单多年单
//            if($listAchievementType==1){
//                $param['deductionremark']=$deductionremark."type=1新单";
//
//                $param['lastdeductionmoney']=$remain['newAddLastDeduction'];
//                //老的剩余未扣减到账业绩 新单
//                $this->hasdeduction($param,$adb);
//                //老的剩余未扣减到账业绩 续费
//                $param['deductionremark']=$deductionremark."type=1续费";
//                $param['alreadydeduction']=$oldarriveachievement['renewoldallarriveachievement']-$remain['renewLastDeduction'];
//                $param['listAchievementType']='renew';
//                $param['totaldeductionmoney']=$oldarriveachievement['renewoldallarriveachievement'];
//                $param['lastdeductionmoney']=$remain['renewLastDeduction'];
//                $this->hasdeduction($param,$adb);
//                //原单单年 原单业绩类型新购  新单多年
//            }elseif ($listAchievementType==2){
//                $param['deductionremark']=$deductionremark."type=2";
//                $param['lastdeductionmoney']=$remain['newAddLastDeduction'];
//                $param['alreadydeduction']=$oldarriveachievement['newaddoldallarriveachievement']-$remain['newAddLastDeduction'];
//                $this->hasdeduction($param,$adb);
//                //原单单年  原单业绩类型续费  新单多年
//            }elseif ($listAchievementType==3){
//                $param['deductionremark']=$deductionremark."type=3";
//                $param['alreadydeduction']=$oldarriveachievement['renewoldallarriveachievement']-$remain['renewLastDeduction'];
//                $param['listAchievementType']='renew';
//                $param['lastdeductionmoney']=$remain['newAddLastDeduction'];
//                $param['totaldeductionmoney']=$oldarriveachievement['renewoldallarriveachievement'];
//                $this->hasdeduction($param,$adb);
//                // 原  新单 全是单年单 不管业绩类型 直接减原业绩
//            }elseif($listAchievementType==4){
//                // 这个相当于是  业绩类型  单年 对 单年的扣减  所以只要减原业绩 就行了  这个就把已经扣减原业绩 放到了 新单里  前面获取剩余未扣减的金额 可以看做是 原来的 续费 和 新单和 - 已扣减 等于剩余未扣减 可以看 $listAchievementType==4 扣减原业绩的值 就明白了。
//                $param['deductionremark']=$deductionremark."type=4";
//                $param['lastdeductionmoney']=$remain['allAddLastDeduction'];
//                $param['alreadydeduction']=($oldarriveachievement['newaddoldallarriveachievement']+$oldarriveachievement['renewoldallarriveachievement'])-$remain['allAddLastDeduction'];
//                $this->hasdeduction($param,$adb);
//            }
//        }
//    }
//
//    if($this->judgeIsNewOrder($contractid)){
//        //是2021.5月后的订单，直接isneworder=1
//        $isneworder=array('isneworder' => 1);
//        array_walk($insertValueStrArray, function (&$value, $key, $isneworder) {
//            $value = array_merge($value, $isneworder);
//        },$isneworder);
//    }
//
//    return $returnData=$this->returnDataValue($insertValueStrArray);
//    return array("datavalue"=>$returnData['datavalue'],"insertValueStr"=>$returnData['insertValueStr']);
//}

//echo getAchievementmonthWithContract('2021-10-29','2021-10-29',3073786,80073);
function getAchievementmonthWithContract($reality_date,$matchdate,$contractid,$receivepayid){
    global $adb;
    $closingDate=Vtiger_Record_Model::getInstanceById(2434264,'ClosingDate');
    $recordModel=Vtiger_Record_Model::getCleanInstance('ReceivedPayments');
    $date=$closingDate->get("date");
    //取计划业绩月份，如果上次解过绑用最后解绑时间
    $reality_dateYm=$recordModel->getCompareTime($receivepayid,$reality_date);
    $reality_dateYm=date("Y-m",strtotime($reality_dateYm));//计划绩效月份
    //取匹配时间
    $sql="select createtime from vtiger_receivedpayments_notes where receivedpaymentsid=? order by receivedpaymentsnotesid desc limit 1";
    $result=$adb->pquery($sql,array($receivepayid));
    $matchTime=$adb->query_result($result,0,'createtime');
    $matchTime=$matchTime?$matchTime:$matchdate.' 09:00:00';
    $madate=date("Y-m").'-'.$date.' 09:00:00';//最后签收截止时间
    if($reality_dateYm==date('Y-m',strtotime("-1 month"))){
        //计划月份是当前日期的上个月
        if(strtotime($matchTime)<=strtotime($madate)){
            //签订时间在配置时间前，就是上个月的
            $isLastMonth=true;
        }else{
            $isLastMonth=false;
        }
        if($isLastMonth){
            //是上个月的，判断合同签收时间
            $sql="select signfor_date from vtiger_servicecontracts where servicecontractsid=?";
            $result=$adb->pquery($sql,array($contractid));
            $signfor_date=$adb->query_result($result,0,'signfor_date');
            if(strtotime($signfor_date)<=strtotime($madate)){
                //签订时间在配置时间前，就是上个月的
                $isLastMonth=true;
            }else{
                $isLastMonth=false;
            }
            if($isLastMonth){
                //上个月判断订单下单时间
                $sql="select createdtime from vtiger_activationcode where contractid=? and status!=2 order by createdtime desc";
                $result=$adb->pquery($sql,array($contractid));
                $orderDate=$adb->query_result($result,0,'createdtime');
                if(strtotime($orderDate)<=strtotime($madate)){
                    //签订时间在配置时间前，就是上个月的
                    $isLastMonth=true;
                }else{
                    $isLastMonth=false;
                }
                if($isLastMonth){
                    $sql="SELECT vtiger_staypayment.workflowstime FROM vtiger_receivedpayments LEFT JOIN vtiger_staypayment ON vtiger_receivedpayments.staypaymentid=vtiger_staypayment.staypaymentid WHERE vtiger_receivedpayments.receivedpaymentsid=? AND vtiger_staypayment.modulestatus='c_complete' order by workflowstime desc limit 1";
                    $result=$adb->pquery($sql,array($receivepayid));
                    if($adb->num_rows($result)>0){
                        $workflowstime=$adb->query_result($result,0,'workflowstime');
                        //代付款签收时间
                        if(strtotime($workflowstime)<=strtotime($madate)){
                            //签订时间在配置时间前，就是上个月的
                            $isLastMonth=true;
                        }else{
                            $isLastMonth=false;
                        }
                    }
                }
            }
        }
    }else{
        //实际月份是当月
        $isLastMonth=false;
    }
    if($isLastMonth){
        $achievementmonth=date('Y-m',strtotime("-1 month"));
    }else{
        $achievementmonth=date('Y-m');
    }
    return $achievementmonth;
}

$request1=new Vtiger_Request(array());
$request1->set('bankAccount','511902796210201');
$request1->set('currency','人民币');
$request1->set('exchangeRate',1);
$request1->set('entryDate','2021-11-17');
$request1->set('payTitle','测试推送');
$request1->set('paymentCode',454545);
$request1->set('money',20.22);
$request1->set('remark',1);

$recordObject=new ReceivedPayments_Record_Model();
$recordObject->addReceivedPaymentsFromCBS($request1);