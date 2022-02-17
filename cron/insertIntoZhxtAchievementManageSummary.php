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
echo '<h1>-------<b>百度v臻信通总监'.$date.'提成计算<b>-------</h1><hr>';

echo '<h2>---------经理/总监提成计算开始--------------</h2><hr>';
//4.计算经理提成数据
$managerList = getZhxtManager();
if(empty($managerList)){
    echo "没有经理级别数据";
    exit;
}

$achievementSummary_record_model=Vtiger_Record_Model::getCleanInstance('AchievementSummary');
foreach ($managerList as $key=>$value){
    //业绩月在职下属人数
    $incumbencyNumber=$achievementSummary_record_model->getIncumbencyNumber(array('userid'=>$value['userid'],'activedate'=>$date));
    $param['achievementid']=0;
    $param['userid']=$value['userid'];
    $param['staffrank']=$value['staffrank'];
    $param['usergrade']=$value['usergrade'];
    $param['gradename']=$value['gradename'];
    $param['newmanagersixmonths']=$value['newmanagersixmonths'];
    $param['incumbencynumber']=$incumbencyNumber;
    $param['realarriveachievement']=0;
    $param['date'] = $date;
    //新单提成的计算
    $param['achievementtype']='newadd';
    echo '开始计算经理/总监id：'.$value['userid'].'的提成<br>';
    calulateZhxtManagerNewCommission($param);
    echo '经理/总监id：'.$value['userid'].'的提成计算完成--------------<hr>';
}
echo '<h2>---------经理/总监提成计算结束--------------</h2><hr>';

//判断是否是百度v员工
function isZhxtDepartment($user_id){
    $userInfo = getUserInfo($user_id);
    $parentdepartment = explode('::',$userInfo['parentdepartment']);
    if(!in_array('H82', $parentdepartment) && !in_array('H83', $parentdepartment)){
        return false;
    }
    return true;
}

//获取百度V经理id
function getZhxtManager(){
    global $adb;
    $sql = "SELECT * FROM vtiger_usergraderoyalty WHERE gradename in ('事业部总监')";
    //查询所有的经理
    $managerInfo=$adb->pquery($sql,array());
    $managerInfoArray=array();
    $managerId=array();
    while($rowdata=$adb->fetch_array($managerInfo)){
        //过滤非百度V的经理
        if(!isZhxtDepartment($rowdata['userid'])){
            continue;
        }
        
        $managerInfoArray[]=array("userid"=>$rowdata['userid'],"staffrank"=>$rowdata['staffrank'],"usergrade"=>$rowdata['usergrade'],"newmanagersixmonths"=>$rowdata['newmanagersixmonths'],"gradename"=>$rowdata['gradename']);
    }
    return $managerInfoArray;
}

//获取经理所有下级员工
function getSubordinateUsers($user_id, $date){
   global $adb;
   $query='SELECT DISTINCT subordinateid FROM vtiger_useractivemonthnew WHERE userid=? AND activedate=?';
   $result=$adb->pquery($query,array($user_id, $date));
   $returData=array();
   while($row=$adb->fetch_array($result)){
       $returData[]=$row['subordinateid'];
   }
   return $returData;

}

//获取员工所属公司部门等
function getUserInvoicecompany($user_id, $date){
    global $adb;
    $sql="SELECT
                vtiger_users.invoicecompany,
                vtiger_user2department.departmentid AS departmentid2,
                if(vtiger_useractivemonthnew.departmentid is null,vtiger_user2department.departmentid,vtiger_useractivemonthnew.departmentid) AS departmentid 
        FROM
            vtiger_users
        LEFT JOIN vtiger_user2department ON vtiger_user2department.userid = vtiger_users.id
        left join vtiger_useractivemonthnew ON (vtiger_useractivemonthnew.userid=vtiger_users.id AND vtiger_useractivemonthnew.userid=vtiger_useractivemonthnew.subordinateid AND vtiger_useractivemonthnew.activedate=?)
        WHERE
            vtiger_users.id =?";
    $userInfo=$adb->pquery($sql,array($date,$user_id));
    return $adb->query_result_rowdata($userInfo,0);
}

//经理新单
function calulateZhxtManagerNewCommission($param){
    global $adb;
    $userInfo = getUserInfo($param['userid']);
    $department = 'H23';//$userInfo['departmentid'];
    // 通过部门id获取对应的满月员工数  满月未交社保实际工资成本  未满月员工实际工资成本
    $setupData = getSummarySetupDataToDepartment($department,$param['date']);
    extract($setupData);
    $wageCost = $staffWageCost + $NmstaffWageCost;

    //主管数
    $directorNum = getDirectorNum($param['userid'], $param['date']);
    //自己工资
    $ownerPrice = getUserSalary($param['userid'],$param['date']);

    // 该经理的所有下属包含所有的经理
    $userIds=getSubordinateUsers($param['userid'],$param['date']);
    //计算提成前删除要计算的月份的经理级别的汇总
    $adb->pquery("DELETE FROM vtiger_achievementsummary WHERE achievementmonth=? AND userid=?",array($param['date'],$param['userid']));
    
    $proportionofyears=0;//多年单占比
    
    //汇总该经理所有下属员工的到账业绩
    $sql="SELECT sum(a.arriveachievement) AS realarriveachievement,sum(a.effectiverefund) AS effectiverefund FROM `vtiger_achievementallot_statistic` AS a WHERE a.receivedpaymentownid in (SELECT subordinateid FROM vtiger_useractivemonthnew WHERE vtiger_useractivemonthnew.userid=? AND vtiger_useractivemonthnew.activedate=a.achievementmonth) AND a.achievementmonth =? AND a.producttype = 7 AND isleave=0 LIMIT 1";
    $result=$adb->pquery($sql,array($param['userid'], $param['date']));

    $data=$adb->query_result_rowdata($result,0);
    //  回款金额  过滤掉重复的回款单
    $data['unit_price'] = getTotalUnitPrice($param['userid'], $param['date']);

    $money=0;
    echo '下属的业绩+经理的业绩之和',$data['realarriveachievement'],'<br>';
    $royaltyRatioG = 40;
    $groupRoyalty = bcdiv(bcmul($data['realarriveachievement'], $royaltyRatioG, 6),100,6);
    //下属提成
    $incumbencyRoyalty = sumIncumbencySummary($param['userid'], $param['date']);
    echo '下属的总提成（只有百度V的新增和续费提成）：',$incumbencyRoyalty,'<br>';
    //提成基数
    $groupTotal = $groupRoyalty - $mouthUserNum*4810 - $directorNum*6900 - $ownerPrice - $incumbencyRoyalty - $wageCost;
    echo '提成基数:',$data['realarriveachievement'],' * ',$royaltyRatioG,'% - ',$mouthUserNum,'*4810 - ',$directorNum,'*6900 - ',$ownerPrice,' - ',$incumbencyRoyalty,' - ',$wageCost,' = ',$groupTotal,'<br>';
    //提成点
    if(200000 <= $groupTotal ){
        $royaltyRatio=10;
    }else if(150000<=$groupTotal && $groupTotal<200000){
        $royaltyRatio=8;
    }else if(100000<$groupTotal && $groupTotal<150000){
        $royaltyRatio=7;
    }else if(50000<$groupTotal && $groupTotal<=100000){
        $royaltyRatio=6;
    }else if($groupTotal<=50000){
        $royaltyRatio=5;
    }
    echo '提成点：'.$royaltyRatio.'<br>';
    //提成
    $royalty = bcdiv(bcmul($groupTotal, $royaltyRatio, 6), 100, 6);
    echo '----获得的提成：'.$royalty.'<br>';
    if($royalty <= 0){
        $royalty = 0;
    }
    $userInfo=getUserInvoicecompany($param['userid'],$param['date']);
    // 年度折扣
    $annualdiscount=0;
    // 年度发放
    $annualpayment=0;
    $grantdetain=0;
    $params['quarterlytasks'] = $baiduQuarterlyTasks;
    $params['invoicecompany']=$userInfo['invoicecompany'];
    $params['departmentid']=$userInfo['departmentid'];
    $params['userid']=$param['userid'];
    $params['unit_price']=$data['unit_price'];
    $params['arriveachievement']=$data['arriveachievement'];
    $params['adjustachievement']=0;
    $params['realarriveachievement']=$data['realarriveachievement'];
    $params['annualdiscount']=$annualdiscount;// 年度折扣
    $params['annualpayment']=$annualpayment;
    $params['royalty']=$royalty;// 提成
    $params['actualroyalty']=$royalty+$annualpayment+$grantdetain;// 实际提成=提成+年度发放+交付发放
    $params['effectiverefund']=$data['effectiverefund'];
    $params['achievementmonth']=$param['date'];
    $params['confirmstatus']='tobeconfirm';
    $params['modulestatus']='a_normal';
    $params['proportionofyears']=$proportionofyears;
    $params['myearachievement']=$money;
    $params['performancetype']='departmenttype';
    $params['grantdetain']=$grantdetain;
    $params['incumbency']=$param['incumbencynumber'];
    $params['mrenewarriveachievement']=0;
    //$id=$adb->getUniqueID('vtiger_achievementsummary');
    $sql="INSERT INTO `vtiger_achievementsummary` (`invoicecompany`, `departmentid`, `userid`, `unit_price`, `arriveachievement`, `realarriveachievement`, `effectiverefund`, `achievementmonth`, `createtime`, `adjustachievement`, `employeelevel`, `royalty`, `actualroyalty`, `achievementtype`, `confirmstatus`, `modulestatus`,`annualdiscount`, `annualpayment`, `proportionofyears`,myearachievement,performancetype,incumbency,grantdetain,mrenewarriveachievement,quarterlytasks) 
                   VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

    $adb->pquery($sql,array($params['invoicecompany'],$params['departmentid'],$params['userid'],$params['unit_price'],$params['arriveachievement'],$params['realarriveachievement'],$params['effectiverefund'],$params['achievementmonth'],date("Y-m-d H:i:s"),0,$param['gradename'],$params['royalty'],$params['actualroyalty'],$param['achievementtype'],$params['confirmstatus'],$params['modulestatus'],$params['annualdiscount'],$params['annualpayment'],$params['proportionofyears'],$params['myearachievement'],$params['performancetype'],$param['incumbencynumber'],$params['grantdetain'],$params['mrenewarriveachievement'],$params['quarterlytasks']));
}

//计算下属提成总数(只包含百度V合同的新单和续费提成)
function sumIncumbencySummary($user_id, $date)
{
    global $adb;
    //汇总该经理所有下属员工的提成
    $sql="SELECT sum(a.royalty) AS totalroyalty FROM `vtiger_achievementsummary` AS a WHERE a.userid in (SELECT subordinateid FROM vtiger_useractivemonthnew WHERE vtiger_useractivemonthnew.userid=? AND vtiger_useractivemonthnew.activedate=a.achievementmonth) AND a.achievementmonth =? LIMIT 1";
    $result=$adb->pquery($sql,array($user_id, $date));

    $data=$adb->query_result_rowdata($result,0);
    return empty($data['totalroyalty']) ? 0 : $data['totalroyalty'];
}

//通过部门获取工资成本等
function getSummarySetupDataToDepartment($department, $date){
    global $adb;
    $sql = 'select peoplenum,monthpeoplemoney,unmonthpeoplemoney,quarterlytasks from vtiger_baiduvsetting where department = ? and settingmonth = ? limit 1';
    $result=$adb->pquery($sql,array($department, $date));
    $setupData=$adb->query_result_rowdata($result,0);

    //工资成本
    $staffWageCost = (empty($setupData['monthpeoplemoney'])) ? 0 : $setupData['monthpeoplemoney'];
    $NmstaffWageCost = (empty($setupData['unmonthpeoplemoney'])) ? 0 : $setupData['unmonthpeoplemoney'];
    //满月员工数
    $mouthUserNum = (empty($setupData['peoplenum'])) ? 0 : $setupData['peoplenum'];
    $baiduQuarterlyTasks = (empty($setupData['quarterlytasks'])) ? 0 : $setupData['quarterlytasks'];
    return array(
        'staffWageCost' => $staffWageCost,
        'NmstaffWageCost' => $NmstaffWageCost,
        'mouthUserNum' => $mouthUserNum,
        'baiduQuarterlyTasks' => $baiduQuarterlyTasks
    );
}

//总监下主管人数
function getDirectorNum($user_id, $date){
    global $adb;
    $sql = 'SELECT subordinateid FROM vtiger_useractivemonthnew as uan left join vtiger_usergraderoyalty as ug on ug.userid = uan.subordinateid WHERE uan.userid=? AND uan.activedate=? and ug.staffrank = 1 group by uan.subordinateid';
    $result=$adb->pquery($sql,array($user_id, $date));
    $total =$adb->num_rows($result);
    return empty($total) ? 0 : $total;
}

//获取员工的工资
function getUserSalary($user_id,$date){
    global $adb;
    $sql = 'select staffwages from vtiger_baiduvstaffwages where userid = ? and setmonth = ? limit 1';
    $result=$adb->pquery($sql,array($user_id, $date));
    $staffwages = $adb->query_result($result,0,'staffwages');
    return empty($staffwages) ? 0 : $staffwages;
}

//获取员工的所有回款
function getTotalUnitPrice($user_id, $date){
    global $adb;
    $sql = "select SUM(n.unit_price) as unit_price from (SELECT a.unit_price FROM `vtiger_achievementallot_statistic` AS a WHERE a.receivedpaymentownid IN (SELECT subordinateid FROM vtiger_useractivemonthnew WHERE vtiger_useractivemonthnew.userid = ? AND vtiger_useractivemonthnew.activedate = a.achievementmonth) AND a.achievementmonth = ? AND a.producttype = 7 AND isleave = 0 group by a.receivedpaymentsid) as n limit 1";
    $result=$adb->pquery($sql,array($user_id, $date));
    $unit_price = $adb->query_result($result,0,'unit_price');
    return empty($unit_price) ? 0 : $unit_price;
}