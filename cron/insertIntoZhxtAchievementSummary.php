<?php
ini_set("include_path", "../");
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/utils/UserInfoUtil.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");
//error_reporting(0);
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
set_time_limit(0);
global $adb;
$date=$_REQUEST['date'];

if(empty($date)){
    $date = date('Y-m', strtotime('-1 month'));
}
echo '<h1>-------<b>百度v臻信通'.$date.'提成计算<b>-------</h1><hr>';
//1.查询某月臻信通生成业绩的业务员
$ownids = getAchievementStatisticUser($date);
comm_logs('计算业绩的业务员：'.json_encode($ownids), 'zhxtlogs_');
if(empty($ownids)){
    echo "无计算提成的业务员<br>";
    exit;
}
$user_ids = array_column($ownids,'receivedpaymentownid');
echo '有提成的业务员id：'.implode(',',$user_ids).'<hr>';

//2.查询业务员的某月臻信通的业绩信息
$summaryInfo = getAchievementSummaryByUserId($user_ids, $date);
if(empty($summaryInfo)){
    echo "业务员没有产生业绩数据";
    exit;
}
echo '<h2>---------开始计算员工提成--------------</h2><hr>';
//3.计算员工提成数据
foreach($summaryInfo as $row){
    //-----------------------------过滤非百度v部门的员工
    if(!isZhxtDepartment($row['userid'])){
        echo '员工id：'.$row['userid']."是非百度V部门，不计算提成<hr>";
        continue;
    }

    $param['achievementid']=$row['achievementid'];
    $param['userid']=$row['userid'];
    $param['achievementtype']=$row['achievementtype'];
    $param['staffrank']=$row['staffrank'];
    $param['usergrade']=$row['usergrade'];
    $param['gradename']=$row['gradename'];
    $param['newmanagersixmonths']=$row['newmanagersixmonths'];
    $param['realarriveachievement']=$row['realarriveachievement'];
    $param['achievementmonth'] = $row['achievementmonth'];
    echo '开始计算员工id：'.$row['userid'].'的'.$row['achievementtype'].'提成，到账业绩：'.$row['realarriveachievement'].'<br>';
    //普通员工
    if($row['achievementtype']=='newadd'){
        calulateZhxtEmployeeNewCommission($param);
    }else{
        calulateZhxtEmployeeRenewCommission($param);
    }
    echo '员工id：'.$row['userid'].'的提成计算完成--------------<hr>';
}
echo '<h2>---------员工提成计算结束--------------</h2><hr>';

//判断是否是百度v员工
function isZhxtDepartment($user_id){
    $userInfo = getUserInfo($user_id);
    $parentdepartment = explode('::',$userInfo['parentdepartment']);
    if(!in_array('H82', $parentdepartment) && !in_array('H83', $parentdepartment)){
        return false;
    }
    return true;
}

//查询某月臻信通生成业绩的业务员
function getAchievementStatisticUser($date){
    global $adb;
    $sql = 'SELECT a.receivedpaymentownid  FROM vtiger_zhxtadvancedwords AS zw LEFT JOIN vtiger_achievementallot_statistic AS a ON zw.contractid = a.servicecontractid WHERE zw.achievementmonth = "'.$date.'" and producttype=7 GROUP BY a.receivedpaymentownid';
    return $adb->run_query_allrecords($sql);
}

//查询业务员的某月臻信通的业绩信息
function getAchievementSummaryByUserId($user_ids, $date){
    global $adb;
    $query=" SELECT a.achievementid,a.userid,a.achievementtype,a.realarriveachievement,ugr.staffrank,ugr.usergrade,ugr.newmanagersixmonths,ugr.gradename,a.achievementmonth FROM vtiger_achievementsummary as a LEFT JOIN vtiger_usergraderoyalty as ugr  ON ugr.userid=a.userid WHERE a.achievementmonth='".$date."' and a.userid in (".implode(',',$user_ids).")";
    $result = $adb->run_query_allrecords($query);
    return $result;
}

//获取员工阶段提成
function getRoyaltyRatio($realarriveachievement){
    if(100000 < $realarriveachievement ){
        $royaltyRatio=20;
    }else if(80000<$realarriveachievement && $realarriveachievement<=100000){
        $royaltyRatio=18;
    }else if(50000<$realarriveachievement && $realarriveachievement<=80000){
        $royaltyRatio=16;
    }else if(30000<$realarriveachievement && $realarriveachievement<=50000){
        $royaltyRatio=14;
    }else if(15000<$realarriveachievement && $realarriveachievement<=30000){
        $royaltyRatio=12;
    }else if($realarriveachievement<=15000){
        $royaltyRatio=8;
    }
    return $royaltyRatio;
}

//员工的新单提成核算
function calulateZhxtEmployeeNewCommission($param){
    global $adb;
    // 年度折扣
    $annualdiscount=0;//$param['realarriveachievement']*0.01;
    $annualpayment=0;
    //提成比例
    $royaltyRatio=0;
    
    $realarriveachievement=$param['realarriveachievement'];
    echo '-------新单提成------<br>基数业绩：',$realarriveachievement,' 员工userid：',$param['userid'],'<br>';

    $royalty=0;
    $royaltyRatio = getRoyaltyRatio($realarriveachievement);
    echo '提成比例级别',$royaltyRatio,'<br>';
    echo '提成=0';
    do{
        if($realarriveachievement <= 0){
            break;
        }
        $royaltyRatioValue = getRoyaltyRatio($realarriveachievement);
        if(100000 < $realarriveachievement ){
            $exceedPart = bcsub($realarriveachievement,100000,4);
            $realarriveachievement = 100000;
        }else if(80000<$realarriveachievement && $realarriveachievement<=100000){
            $exceedPart = bcsub($realarriveachievement,80000,4);
            $realarriveachievement = 80000;
        }else if(50000<$realarriveachievement && $realarriveachievement<=80000){
            $exceedPart = bcsub($realarriveachievement,50000,4);
            $realarriveachievement = 50000;
        }else if(30000<$realarriveachievement && $realarriveachievement<=50000){
            $exceedPart = bcsub($realarriveachievement,30000,4);
            $realarriveachievement = 30000;
        }else if(15000<$realarriveachievement && $realarriveachievement<=30000){
            $exceedPart = bcsub($realarriveachievement,15000,4);
            $realarriveachievement = 15000;
        }else if($realarriveachievement<=15000){
            $exceedPart = bcsub($realarriveachievement,0,4);
            $realarriveachievement = 0;
        }
        echo ' + '.$exceedPart.'*'.$royaltyRatioValue.'%';
        $royalty += bcdiv(bcmul($exceedPart,$royaltyRatioValue,6),100,4);
    }while(true);

    $subWtidholdPrecenTwenty=0;
    $addWtidholdPrecenTwenty=0;
    $sumActualRoyalty=bcadd(bcsub($royalty,$subWtidholdPrecenTwenty,6),$addWtidholdPrecenTwenty,6);
    //增加标准合同的新单提成
    $otherRoyalty = calulateEmployeeNewRoyalty($param['userid'], $param['achievementmonth']);
    echo '----------标准合同的新单总提成：'.$otherRoyalty.'<br>';
    $actualroyalty=$sumActualRoyalty+$annualpayment+$otherRoyalty;
    //个人的多年单续费业绩
    $mrenewarriveachievement=0;
    $sql=" UPDATE vtiger_achievementsummary SET performancetype='persontype',grantdetain=?,royalty=?,actualroyalty=?,annualdiscount=?,annualpayment=?,employeelevel=?,withholdroyaltyratio=?,deliverdetain=?,mrenewarriveachievement=? WHERE achievementid=? ";
    $adb->pquery($sql,array($addWtidholdPrecenTwenty,$royalty,$actualroyalty,$annualdiscount,$annualpayment,$param['gradename'],$royaltyRatio,$subWtidholdPrecenTwenty,$mrenewarriveachievement,$param['achievementid']));
    echo '<br>----------新单提成：'.$royalty.'<br>';
}

//员工的续费提成核算
function calulateZhxtEmployeeRenewCommission($param){
    global $adb;
    echo '-------续费提成------<br>基数业绩：',$param['realarriveachievement'],' 员工userid：',$param['userid'],'<br>';
    $royalty = bcdiv(bcmul($param['realarriveachievement'],6,6),100,4);
    $annualdiscount=0;
    $annualpayment=0;
    //增加标准合同的续费提成
    $otherRoyalty = calulateEmployeeRenewRoyalty($param['userid'], $param['achievementmonth']);
    echo '----------标准合同的续费总提成：'.$otherRoyalty.'<br>';
    $actualroyalty=$royalty+$annualpayment+$otherRoyalty;

    $sql=" UPDATE vtiger_achievementsummary SET performancetype='persontype',royalty=?,actualroyalty=?,annualdiscount=?,annualpayment=?,employeelevel=? WHERE achievementid=? ";
    $adb->pquery($sql,array($royalty,$actualroyalty,$annualdiscount,$annualpayment,$param['gradename'],$param['achievementid']));
    echo '----------续费提成：'.$royalty.'(到账业绩*0.06)<br>';
}

//员工的非百度V续费提成
function calulateEmployeeRenewRoyalty($userid, $date){
    global $adb;
    //seo系列、等需要长期维护的服务 的提成计算
    $sql=" SELECT SUM(a.arriveachievement*0.05) as royalty  FROM `vtiger_achievementallot_statistic` as a INNER JOIN vtiger_servicecontracts as s ON s.servicecontractsid=a.servicecontractid  WHERE   a.receivedpaymentownid=?  AND  a.achievementmonth=?  AND  a.achievementtype='renew' and a.producttype != 7 AND s.parent_contracttypeid IN(4,6) AND  s.contract_type IN('百度微整站优化续费服务合同','百度微整站优化服务合同','搜索引擎左侧优化续费协议书','KA搜索引擎优化','T-网营销销售合同','整合营销合同','网络口碑营销合同书') AND isleave=0 LIMIT 1 ";
    $result=$adb->pquery($sql,array($userid,$date));
    $data=$adb->query_result_rowdata($result,0);
    $royaltyOne=empty($data['royalty']) ? 0 : $data['royalty'];
    echo '----------seo系列等的续费提成：'.$royaltyOne.'<br>';
    
    $sql=" SELECT SUM(a.arriveachievement*a.renewal_commission/100) as royalty  FROM `vtiger_achievementallot_statistic` as a INNER JOIN vtiger_servicecontracts as s ON s.servicecontractsid=a.servicecontractid  WHERE   a.receivedpaymentownid=? AND a.renewal_commission!=10 AND a.renewal_commission!=9 AND  a.achievementmonth=?  AND  a.achievementtype='renew' and a.producttype != 7  AND (s.parent_contracttypeid NOT IN(4,6) || s.contract_type NOT IN('百度微整站优化续费服务合同','百度微整站优化服务合同','搜索引擎左侧优化续费协议书','KA搜索引擎优化','T-网营销销售合同','整合营销合同','网络口碑营销合同书'))  AND isleave=0 LIMIT 1 ";
    $result=$adb->pquery($sql,array($userid,$date));
    $data=$adb->query_result_rowdata($result,0);
    $royaltyTwo=empty($data['royalty']) ? 0 : $data['royalty'];
    echo '----------非seo系列等的续费提成：'.$royaltyTwo.'<br>';
    
    $query="SELECT SUM(a.arriveachievement*9/100) as royalty  FROM `vtiger_achievementallot_statistic` as a  WHERE   a.receivedpaymentownid=?  AND  a.achievementmonth=?  AND  a.achievementtype='renew' AND more_years_renew=1 AND a.renewal_commission in(9,10) AND isleave=0 and a.producttype != 7 LIMIT 1";
    $result=$adb->pquery($query,array($userid,$date));
    $data=$adb->query_result_rowdata($result,0);
    $royaltyThree=empty($data['royalty']) ? 0 : $data['royalty'];
    echo '----------多年单的续费提成：'.$royaltyThree.'<br>';
    
    return $royaltyOne + $royaltyTwo + $royaltyThree;
}

/**
 * 员工的新单提成核算
 * @param $param
 */
function calulateEmployeeNewRoyalty($userid, $date){
    global $adb;

    //计算非百度v合同的总业绩
     $sql=" SELECT SUM(a.arriveachievement) as realarriveachievement, ugr.usergrade  FROM `vtiger_achievementallot_statistic` as a LEFT JOIN vtiger_usergraderoyalty as ugr  ON ugr.userid=a.receivedpaymentownid WHERE   a.receivedpaymentownid=?  AND  a.achievementmonth=?  AND  a.achievementtype='newadd' and a.producttype != 7 AND isleave=0 LIMIT 1 ";
    $result=$adb->pquery($sql,array($userid,$date));
    $param=$adb->query_result_rowdata($result,0);

    if(empty($param)){
        return 0;
    }
    echo '----------标准合同的新单总业绩：'.$param['realarriveachievement'].'，员工级别：'.$param['usergrade'].'<br>';
    if($param['usergrade']==1 || $param['usergrade']==2 || in_array($param['usergrade'],array(18))){
        $royaltyRatio=9;
    }else if($param['usergrade']==3 || $param['usergrade']==4 || $param['usergrade']==5 || $param['usergrade']==6 || $param['usergrade']==7 || $param['usergrade']==8 || $param['usergrade']==13){
        $realarriveachievement=getLastMonthAchievement($userid,$date);
        if(120000< $realarriveachievement ){
            $royaltyRatio==29;
        }else if(80000<$realarriveachievement && $realarriveachievement<=120000){
            $royaltyRatio=24;
        }else if(60000<$realarriveachievement && $realarriveachievement<=80000){
            $royaltyRatio=22;
        }else if(50000<$realarriveachievement && $realarriveachievement<=60000){
            $royaltyRatio=19;
        }else if(40000<$realarriveachievement && $realarriveachievement<=50000){
            $royaltyRatio=17;
        }else if(22000<$realarriveachievement && $realarriveachievement<=40000){
            $royaltyRatio=14;
        }else if(10000<$realarriveachievement && $realarriveachievement<=22000){
            $royaltyRatio=9;
        }else if($realarriveachievement<=10000){
            $royaltyRatio=9;
        }
    }
    $royalty=bcdiv(bcmul($param['realarriveachievement'],$royaltyRatio,6),100,6);
    echo '----------标准合同的新单提成点：'.$royaltyRatio.'<br>';
    return $royalty;
}



/**
 * 获取业绩核算月的上个月的业绩
 * @return int
 */
function getLastMonthAchievement($userid,$date){
    global $adb;
    $achievementSummary_record_model=Vtiger_Record_Model::getCleanInstance('AchievementSummary');
    $month = date("m",strtotime($date));
    $calculation_year_month=$achievementSummary_record_model->getLastMonth(array('userid'=>$userid,'calculation_year_month'=>$date));
    $query='SELECT sum(if(arriveachievement>0,arriveachievement,0)) as realarriveachievement FROM vtiger_achievementallot_statistic WHERE  (achievementtype=\'newadd\' OR (achievementtype=\'renew\' AND more_years_renew=1)) AND receivedpaymentownid=? AND achievementmonth=? AND isleave=0 and producttype !=7';
    $result=$adb->pquery($query,array($userid,$calculation_year_month));
    $realarriveachievement=0;
    if($adb->num_rows($result)){
        $realarriveachievement=$result->fields['realarriveachievement'];
    }
    if($month == 3 && $calculation_year_month!=$date){//排除3月份入职
        $query='SELECT sum(if(arriveachievement>0,arriveachievement,0)) as realarriveachievement FROM vtiger_achievementallot_statistic WHERE  (achievementtype=\'newadd\' OR (achievementtype=\'renew\' AND more_years_renew=1)) AND receivedpaymentownid=? AND achievementmonth=? AND isleave=0 and producttype !=7';
        $result=$adb->pquery($query,array($userid,$date));
        $currentMontharriveachievement=0;
        if($adb->num_rows($result)){
            $currentMontharriveachievement=$result->fields['realarriveachievement'];
        }
        $realarriveachievement*=2;
        $realarriveachievement=($realarriveachievement>$currentMontharriveachievement)?$realarriveachievement:$currentMontharriveachievement;
    }
    return $realarriveachievement;
}