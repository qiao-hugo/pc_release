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

// 定时任务计算提成    每天早上一点执行脚本
echo '<h1>-------<b>中小体系--总监的'.$date.'提成计算<b>-------</h1><br>';
//1.查询所有商务总监、高级总监、资深总监、总经理
$list = getDirectors();
foreach($list as $val){
    echo '<hr>';
    $Department = getDirectorDepartment($val['userid']);
    //非百度v不产生业绩
    $parentdepartmentArr = explode('::',$Department['parentdepartment']);
    if(!in_array('H3', $parentdepartmentArr)){
        echo '-----总监id：'.$val['userid'].'非中小体系部门不产生提成<br>';
        continue;
    }
    echo '-----计算总监id：'.$val['userid'].'提成开始<br>';
    //2.获取自己及其所有下属的新单+多年单续费的总和
    $data = getDirectirAllSubordinateStatistic($val['userid'],$date);
    $realarriveachievement = empty($data['realarriveachievement']) ? 0 : $data['realarriveachievement'];
    echo '-----所有下属生成的总业绩（包括自己）：'.$realarriveachievement.'，所属角色：'.$val['gradename'].'<br>';
    //3.根据总监角色计算总监的提成
    $royalty = calculationDirectorCommission($realarriveachievement, $val['gradename']);
    //4.生成入表数据
    echo '-----生成的提成：'.$royalty.'<br>';
    $userInfo = getUserInfo($val['userid']);
    $params = array();
    $params['invoicecompany']=$userInfo['invoicecompany'];
    $params['departmentid']=$Department['departmentid'];
    $params['userid']=$val['userid'];
    $params['unit_price']=$data['unit_price'];
    $params['arriveachievement']=$realarriveachievement;
    $params['adjustachievement']=0;
    $params['realarriveachievement']=$realarriveachievement;
    $params['annualdiscount']=0;// 年度折扣
    $params['annualpayment']=0;// 年度发放
    $params['royalty']=$royalty;// 提成
    $params['actualroyalty']=$royalty;// 实际提成=提成+年度发放
    $params['effectiverefund']=$data['effectiverefund'];
    $params['achievementmonth']=$date;
    $params['confirmstatus']='tobeconfirm';
    $params['modulestatus']='a_normal';
    $params['createtime'] = date('Y-m-d H:i:s');
    $params['achievementtype'] = 'newadd';
    $params['performancetype']='departmenttype';
    $params['employeelevel'] = $val['gradename'];
    $insertData[] = $params;
    echo '-----计算总监id：'.$val['userid'].'提成结束<br>';
}

//5.数据入表
if(!empty($insertData)){
    $userids = array_column($insertData,'userid');
    $deleteSql = 'delete from vtiger_achievementsummary where achievementmonth="'.$date.'" and performancetype="departmenttype" and userid in ('.implode(',',$userids).')';
    $adb->pquery($deleteSql,array());
    insertSummaryData($insertData);
}

//将处理后的高级词数据入表
function insertSummaryData($data){
    if(empty($data)){
        return false;
    }
    $field = array_keys($data[0]);
    $values = array();
    $valuesStr = '';
    foreach ($data as $val) {
        $values = array_merge($values, array_values($val));
        $valuesStr .= '('.implode(',', array_fill(0,count($val),'?')).'),';
    }
    $insertWordsSql = 'INSERT INTO vtiger_achievementsummary ('.implode(',',$field).') VALUES '.trim($valuesStr,',');
    global $adb;
    $adb->pquery($insertWordsSql,$values);
    return true;
}

//查询所有商务总监、高级总监、资深总监、总经理
function getDirectors(){
    global $adb;
    $sql = "SELECT * FROM vtiger_usergraderoyalty  WHERE  gradename in ('商务总监','高级总监','资深总监','总经理')";
    $directors =$adb->run_query_allrecords($sql);
    return $directors;
}

//查询员工部门
function getDirectorDepartment($userid){
    global $adb;
    // 查询分成人 所在部门  以及属事业部查询
    $queryc=" SELECT d.departmentid,d.departmentname,d.parentdepartment,u.last_name FROM vtiger_user2department ud  LEFT JOIN vtiger_departments as d  ON ud.departmentid=d.departmentid LEFT JOIN  vtiger_users as u ON  u.id=ud.userid  WHERE  ud.userid= ? LIMIT 1 ";
    $resultdataDepartment=$adb->pquery($queryc,array($userid));
    $Department=$adb->query_result_rowdata($resultdataDepartment,0);
    return $Department;
}

//查询总监及其所有下属的业绩和
function getDirectirAllSubordinateStatistic($userid,$date){
    global $adb;
    $sql="SELECT sum(a.arriveachievement) AS realarriveachievement,sum(a.unit_price) AS unit_price,sum(a.effectiverefund) AS effectiverefund FROM `vtiger_achievementallot_statistic` AS a WHERE a.receivedpaymentownid in (SELECT subordinateid FROM vtiger_useractivemonthnew WHERE vtiger_useractivemonthnew.userid=? AND vtiger_useractivemonthnew.activedate=a.achievementmonth) AND a.achievementmonth =? AND a.achievementtype in ('mrenew','newadd') AND isleave=0 LIMIT 1";
    $result=$adb->pquery($sql,array($userid,$date));
    $data=$adb->query_result_rowdata($result,0);
    return $data;
}

//根据总监角色计算对应提成
function calculationDirectorCommission($arriveachievement, $type){
    $royalty = 0;
    echo '-----根据提成计算公式:0';
    if(in_array($type, array('商务总监','总经理'))){
        $royalty = calculationBusinessCommission($arriveachievement);
    }else if($type == '高级总监'){
        $royalty = calculationSeniorCommission($arriveachievement);
    }else if($type == '资深总监'){
        $royalty = calculationVeteranCommission($arriveachievement);
    }
    echo '='.$royalty.'<br>';
    return $royalty;
}

//商务总监、总经理提成计算
function calculationBusinessCommission($arriveachievement){
    $royalty = ladderAlgorithm($arriveachievement, 200000);
    return $royalty;
}

//高级总监提成计算
function calculationSeniorCommission($arriveachievement){
    $royalty = 0;
    if($arriveachievement <= 600000){
        return 0;
    }
    $royalty = ladderAlgorithm($arriveachievement, 200000);
    return $royalty;
}

//资深总监提成计算
function calculationVeteranCommission($arriveachievement){
    $royalty = ladderAlgorithm($arriveachievement, 800000);
    return $royalty;
}

//阶梯提成计算(由于总监的阶梯数据是一样的，可以用一个函数兼容，如果有一个总监的阶梯数据改变了，就需要再写一个对应的函数)
function ladderAlgorithm($arriveachievement, $limit = 200000){
    $royalty = 0;
    do{
        if($arriveachievement <= 0 || $arriveachievement <= $limit){
            break;
        }
        if($arriveachievement > 1500000){
            $minLimit = 1500000;
            $ratio = 6;
        }else if($arriveachievement <= 1500000 && $arriveachievement > 1200000){
            $minLimit = 1200000;
            $ratio = 3;
        }else if($arriveachievement <= 1200000 && $arriveachievement > 1000000){
            $minLimit = 1000000;
            $ratio = 2.5;
        }else if($arriveachievement <= 1000000 && $arriveachievement > 800000){
            $minLimit = 800000;
            $ratio = 2;
        }else if($arriveachievement <= 800000 && $arriveachievement > 400000){
            $minLimit = 400000;
            $ratio = 1.5;
        }else if($arriveachievement <= 400000 && $arriveachievement > 200000){
            $minLimit = 200000;
            $ratio = 1;
        }else if($arriveachievement <= 200000 && $arriveachievement > 0){
            $minLimit = 0;
            $ratio = 0;
        }

        $exceedPart = bcsub($arriveachievement,$minLimit,4);
        $sub = '('.$arriveachievement.'-'.$minLimit.')';
        $arriveachievement = $minLimit;
        $royalty += bcdiv(bcmul($exceedPart, $ratio, 4), 100, 6);
        echo '+'.$sub.'X'.$ratio.'%';
    }while(true);
    return $royalty;
}