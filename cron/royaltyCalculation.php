<?php
ini_set("include_path", "../");
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");
//error_reporting(0);
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
set_time_limit(0);
global $adb;
// 定时任务计算提成    每天早上一点执行脚本
//查询当前业绩核算截止日期
$dateInfo=$adb->pquery("SELECT  date  FROM vtiger_closingdate WHERE id=2434264",array());
$dateInfo=$adb->query_result_rowdata($dateInfo,0);
$current_date=date("d");
$difference=$current_date-$dateInfo['date'];
//也就是过了业绩核算截止日期 第二天执行计算上个月的提成
//$difference=1;//####################测试标记
if($difference==1 || (!empty($_GET['update'])&& $_GET['update']==1)){
    ob_start();
    echo '-----汇总strat--------<hr>';
    // 要计算业绩的月份
    $calculation_year_month =date("Y-m",strtotime("-1 months",time()));
	//$calculation_year_month='2020-07';
    $calculation_year_month=!empty($_REQUEST['ym'])?$_REQUEST['ym']:$calculation_year_month;
    $calculation_month=date("m",strtotime($calculation_year_month));
    $calculation_year=date("Y",strtotime($calculation_year_month));

	//updateUserGradeRoyalty($calculation_year,$calculation_month);
    //$query=" SELECT a.achievementid,a.userid,a.achievementtype,a.realarriveachievement,ugr.staffrank,ugr.usergrade,ugr.newmanagersixmonths,ugr.gradename FROM vtiger_achievementsummary as a LEFT JOIN vtiger_usergraderoyalty as ugr  ON ugr.userid=a.userid WHERE ugr.usergrade>0 and ugr.staffrank=0 and a.achievementmonth='".$calculation_year_month."'";    echo $query;
    $query="SELECT 0 as achievementid,	ugr.userid,	0 as achievementtype,0 as realarriveachievement,ugr.staffrank,ugr.usergrade,ugr.newmanagersixmonths,ugr.gradename FROM vtiger_usergraderoyalty ugr LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=ugr.departmentid WHERE ugr.usergrade > 0 AND ugr.staffrank = 0 AND concat(vtiger_departments.parentdepartment,'::') NOT LIKE 'H1::H3::H23::%'";    echo $query;
    echo '-----普通员工strat--------<hr>';
    echo $query;
    echo '<hr>';
    $result = $adb->run_query_allrecords($query);
    $param['calculation_year_month']=$calculation_year_month;
    $param['calculation_year']=$calculation_year;
    $param['calculation_month']=$calculation_month;
    $achievementSummary_record_model=Vtiger_Record_Model::getCleanInstance('AchievementSummary');
    // 普通员工的提成计算
    foreach($result as $row){
        $param['achievementid']=$row['achievementid'];
        $param['userid']=$row['userid'];
        $param['achievementtype']=$row['achievementtype'];
        $param['staffrank']=$row['staffrank'];
        $param['usergrade']=$row['usergrade'];
        $param['gradename']=$row['gradename'];
        $param['newmanagersixmonths']=$row['newmanagersixmonths'];
        $param['realarriveachievement']=$row['realarriveachievement'];
        $query="DELETE FROM vtiger_achievementsummary WHERE vtiger_achievementsummary.userid=? AND vtiger_achievementsummary.achievementmonth=?";
        $adb->pquery($query,array($row['userid'],$param['calculation_year_month']));
        //普通员工
        //if($row['achievementtype']=='newadd'){
        $param['achievementtype']='newadd';
        $achievementSummary_record_model->calulateEmployeeNewCommission($param);
        //}else{
        $param['achievementtype']='renew';
        $achievementSummary_record_model->calulateEmployeeRenewCommission($param);
        $param['achievementtype']='newadd';
        $achievementSummary_record_model->calulateEmployeeNewCommissionNegative($param);
        //}
    }
    echo '-----普通员工end--------<hr>';
    echo '-----经理start--------<hr>';
    //查询所有的经理
    $managerInfo=$adb->pquery("SELECT * FROM vtiger_usergraderoyalty LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_usergraderoyalty.departmentid WHERE staffrank=1 AND usergrade in(9,10,11,23)  AND concat(vtiger_departments.parentdepartment,'::') NOT LIKE 'H1::H3::H23::%'",array());
    $managerInfoArray=array();
    while($rowdata=$adb->fetch_array($managerInfo)){
        $managerInfoArray[]=array("userid"=>$rowdata['userid'],"staffrank"=>$rowdata['staffrank'],"usergrade"=>$rowdata['usergrade'],"newmanagersixmonths"=>$rowdata['newmanagersixmonths'],"gradename"=>$rowdata['gradename']);
        $userids.=",".$rowdata['userid'];
    }
    $userids=trim($userids,',');
    //计算提成前删除要计算的月份的经理级别的汇总
    $adb->pquery("DELETE FROM vtiger_achievementsummary WHERE achievementmonth=? AND  userid IN(".$userids.")",array($calculation_year_month));
    $param['userids']=$userids;// 所有的经理
    //经理的有下属的提成计算
    foreach ($managerInfoArray as $key=>$value){
        $incumbencyNumber=$achievementSummary_record_model->getIncumbencyNumber(array('userid'=>$value['userid'],'activedate'=>$calculation_year_month));
        $param['achievementid']=0;
        $param['userid']=$value['userid'];
        $param['staffrank']=$value['staffrank'];
        $param['usergrade']=$value['usergrade'];
        $param['gradename']=$value['gradename'];
        $param['newmanagersixmonths']=$value['newmanagersixmonths'];
        $param['incumbencynumber']=$incumbencyNumber;//业绩月在职下属人数
        echo $value['userid'].'业绩月在职下属人数'.$incumbencyNumber.'----<br>';
        $param['realarriveachievement']=0;
        //新单提成的计算
        $param['achievementtype']='newadd';
        $achievementSummary_record_model->calulateManagerNewCommission($param);
        //续费提成的计算
        $param['achievementtype']='renew';
        $achievementSummary_record_model->calulateManagerReNewCommission($param);
    }
    echo '-----经理end--------<hr>';
    echo '-----总监start--------<hr>';
    //查询所有的总监
    /*
    $userids='';
    $managerInfo=$adb->pquery("SELECT * FROM vtiger_usergraderoyalty  WHERE  gradename in('商务总监','高级总监','资深总监')".$ugrassessmonth,array());
    $managerInfoArray=array();
    while($rowdata=$adb->fetch_array($managerInfo)){
        $managerInfoArray[]=array("userid"=>$rowdata['userid'],"staffrank"=>$rowdata['staffrank'],"usergrade"=>$rowdata['usergrade'],"newmanagersixmonths"=>$rowdata['newmanagersixmonths'],"gradename"=>$rowdata['gradename']);
        $userids.=",".$rowdata['userid'];
    }
    $userids=trim($userids,',');
    //计算提成前删除要计算的月份的总监级别的汇总
    $adb->pquery("DELETE FROM vtiger_achievementsummary WHERE achievementmonth=? AND  userid IN(".$userids.")",array($calculation_year_month));
    $param=array();
    $param['calculation_year_month']=$calculation_year_month;
    $param['calculation_year']=$calculation_year;
    $param['calculation_month']=$calculation_month;
    $param['userids']=$userids;// 所有的总监
    //总监的有下属的提成计算
    foreach ($managerInfoArray as $key=>$value){
        $incumbencyNumber=$achievementSummary_record_model->getIncumbencyNumber(array('userid'=>$value['userid'],'activedate'=>$calculation_year_month));
        $param['achievementid']=0;
        $param['userid']=$value['userid'];
        $param['staffrank']=$value['staffrank'];
        $param['usergrade']=$value['usergrade'];
        $param['gradename']=$value['gradename'];
        $param['newmanagersixmonths']=$value['newmanagersixmonths'];
        $param['incumbencynumber']=$incumbencyNumber;//业绩月在职下属人数
        $param['realarriveachievement']=0;
        //新单提成的计算
        $param['achievementtype']='newadd';
        $achievementSummary_record_model->calulateCommissionerNewCommission($param);
    }*/
    echo '-----总监end--------<hr>';
    //查找一下当月没有业绩但有20%提成或年度1%的人员
    $achievementSummary_record_model->noAchievementRoyalty(array(
    'calculation_year_month'=>$calculation_year_month,
        'calculation_month'=>$calculation_month,
        'calculation_year'=>$calculation_year
    ));
    ///更新业绩状态
    //$achievementSummary_record_model->updateStatusAch($calculation_year_month);
    updateUserDepartment($calculation_year_month);
    echo '------end--------<hr>';
    $info=ob_get_contents();
    $achievementSummary_record_model->comm_logs($info,'jiaobenachievementallot');
}
//经理的提成
if(!empty($_GET['update'])&& $_GET['update']==2){
    // 要计算业绩的月份
    ob_start();
    echo '-----------经理个人start----------<hr>';
    $calculation_year_month=!empty($_REQUEST['ym'])?$_REQUEST['ym']:$calculation_year_month;
    $calculation_month=date("m",strtotime($calculation_year_month));
    $calculation_year=date("Y",strtotime($calculation_year_month));
    $uidstringarray=array();
    $uidstringSQL='';
    $flag=false;
    if(!empty($_GET['uid'])){
        $uidstringarray=explode(',',$_GET['uid']);
        $uidstringSQL=' AND userid IN('.$_GET['uid'].')';
        $flag=true;
    }
    $param['calculation_year_month']=$calculation_year_month;
    $param['calculation_year']=$calculation_year;
    $param['calculation_month']=$calculation_month;
    $achievementSummary_record_model=Vtiger_Record_Model::getCleanInstance('AchievementSummary');
    //查询所有的经理
    $managerInfo=$adb->pquery("SELECT * FROM vtiger_usergraderoyalty  WHERE staffrank=1".$uidstringSQL,array());
    $managerInfoArray=array();
    $managerId=array();
    while($rowdata=$adb->fetch_array($managerInfo)){
        $managerId[]=$rowdata['userid'];
        $managerInfoArray[]=array("userid"=>$rowdata['userid'],"staffrank"=>$rowdata['staffrank'],"usergrade"=>$rowdata['usergrade'],"newmanagersixmonths"=>$rowdata['newmanagersixmonths'],"gradename"=>$rowdata['gradename']);
    }
    //计算提成前删除要计算的月份的经理级别的汇总
    $adb->pquery("DELETE FROM vtiger_achievementsummary WHERE achievementmonth=? AND  userid IN(".implode(',',$managerId).")",array($calculation_year_month));

    //经理的有下属的提成计算
    //$achievementSummary_record_model->setSubordinateUsers();
    //$subordinateUsers=$achievementSummary_record_model->subordinateUsers;
    $args=array('activedate'=>$calculation_year_month);
    foreach ($managerInfoArray as $key=>$value){
        $incumbencyNumber=$achievementSummary_record_model->getIncumbencyNumber(array('userid'=>$value['userid'],'activedate'=>$calculation_year_month));
        $param['achievementid']=0;
        $param['userid']=$value['userid'];
        $param['staffrank']=$value['staffrank'];
        $param['usergrade']=$value['usergrade'];
        $param['gradename']=$value['gradename'];
        $param['newmanagersixmonths']=$value['newmanagersixmonths'];
        $param['incumbencynumber']=$incumbencyNumber;//业绩月在职下属人数
        echo $value['userid'].'业绩月在职下属人数'.$incumbencyNumber.'----<br>';
        $param['realarriveachievement']=0;
        //新单提成的计算
        $param['achievementtype']='newadd';
        $achievementSummary_record_model->calulateManagerNewCommission($param);
        //续费提成的计算
        $param['achievementtype']='renew';
        $achievementSummary_record_model->calulateManagerReNewCommission($param);
    }
    updateUserDepartment($calculation_year_month);
    echo '-----------end----------<hr>';
    $info=ob_get_contents();
    $achievementSummary_record_model->comm_logs($info,'jiaobenachievementallot');
}
//员工的提成
if((!empty($_GET['update'])&& $_GET['update']==3)){
    ob_start();
    echo '------员工个人start-----<hr>';
    // 要计算业绩的月份
    $calculation_year_month =date("Y-m",strtotime("-1 months",time()));
    //$calculation_year_month='2020-09';
    $calculation_year_month=!empty($_REQUEST['ym'])?$_REQUEST['ym']:$calculation_year_month;
    $calculation_month=date("m",strtotime($calculation_year_month));
    $calculation_year=date("Y",strtotime($calculation_year_month));
    $uidstring='';
    if($_GET['uid']>0){
        $uidstring=' AND a.userid='.$_GET['uid'];
    }else{
        echo 'UID大于0';
        exit;
    }
    echo '------start-----<hr>';
    //先更新员工及级别
    //updateUserGradeRoyalty($calculation_year,$calculation_month);
    //$query=" SELECT a.achievementid,a.userid,a.achievementtype,a.realarriveachievement,ugr.staffrank,ugr.usergrade,ugr.newmanagersixmonths,ugr.gradename FROM vtiger_achievementsummary as a LEFT JOIN vtiger_usergraderoyalty as ugr  ON ugr.userid=a.userid WHERE  ugr.usergrade>0 and a.achievementmonth='".$calculation_year_month."'".$uidstring;
    $query="SELECT 0 as achievementid,	ugr.userid,	0 as achievementtype,0 as realarriveachievement,ugr.staffrank,ugr.usergrade,ugr.newmanagersixmonths,ugr.gradename FROM vtiger_usergraderoyalty ugr WHERE ugr.userid=".$_GET['uid']." AND ugr.usergrade > 0 AND ugr.staffrank = 0";
    //echo '<hr>';
    //echo $query;
    $result = $adb->run_query_allrecords($query);
    print_r($result);
    $param['calculation_year_month']=$calculation_year_month;
    $param['calculation_year']=$calculation_year;
    $param['calculation_month']=$calculation_month;
    $achievementSummary_record_model=Vtiger_Record_Model::getCleanInstance('AchievementSummary');

    // 普通员工的提成计算
    //foreach($result as $row){
    $query="DELETE FROM vtiger_achievementsummary WHERE vtiger_achievementsummary.userid=? AND vtiger_achievementsummary.achievementmonth=?";
    echo $query;
    print_r(array($result[0]['userid'],$param['calculation_year_month']));
    $adb->pquery($query,array($result[0]['userid'],$param['calculation_year_month']));
        $param['achievementid']=$result[0]['achievementid'];
        $param['userid']=$result[0]['userid'];
        $param['achievementtype']=$result[0]['achievementtype'];
        $param['staffrank']=$result[0]['staffrank'];
        $param['usergrade']=$result[0]['usergrade'];
        $param['gradename']=$result[0]['gradename'];
        $param['newmanagersixmonths']=$result[0]['newmanagersixmonths'];
        $param['realarriveachievement']=$result[0]['realarriveachievement'];
        //普同员工
        //$achievementSummary_record_model->calculationRoalty($param);

        $param['achievementtype']='newadd';
        $achievementSummary_record_model->calulateEmployeeNewCommission($param);

        $param['achievementtype']='renew';
        $achievementSummary_record_model->calulateEmployeeRenewCommission($param);
        $param['achievementtype']='newadd';
        $achievementSummary_record_model->calulateEmployeeNewCommissionNegative($param);

    //}
    //查找一下当月没有业绩但有20%提成或年度1%的人员
    $achievementSummary_record_model->noAchievementRoyalty(array(
        'calculation_year_month'=>$calculation_year_month,
        'calculation_month'=>$calculation_month,
        'calculation_year'=>$calculation_year
    ));
    echo '------end-----<hr>';
    $info=ob_get_contents();
    $achievementSummary_record_model->comm_logs($info,'jiaobenachievementallot');
    ///更新业绩状态
    //$achievementSummary_record_model->updateStatusAch($calculation_year_month);
}
//总监的提成
if(!empty($_GET['update'])&& $_GET['update']==4){
    ob_start();
    echo '------总监开始-----<hr>';
    // 要计算业绩的月份
    $calculation_year_month=!empty($_REQUEST['ym'])?$_REQUEST['ym']:$calculation_year_month;
    $calculation_month=date("m",strtotime($calculation_year_month));
    $calculation_year=date("Y",strtotime($calculation_year_month));
    $uidstringarray=array();
    $flag=false;
    if(!empty($_GET['uid'])){
        $uidstringarray=explode(',',$_GET['uid']);
        $flag=true;
    }

    $param['calculation_year_month']=$calculation_year_month;
    $param['calculation_year']=$calculation_year;
    $param['calculation_month']=$calculation_month;
    $achievementSummary_record_model=Vtiger_Record_Model::getCleanInstance('AchievementSummary');


    //查询所有的总监
    $managerInfo=$adb->pquery("SELECT * FROM vtiger_usergraderoyalty  WHERE  gradename in('商务总监','高级总监','资深总监')",array());
    $managerInfoArray=array();
    while($rowdata=$adb->fetch_array($managerInfo)){
        if($flag && in_array($rowdata['userid'],$uidstringarray)){
            $managerInfoArray[]=array("userid"=>$rowdata['userid'],"staffrank"=>$rowdata['staffrank'],"usergrade"=>$rowdata['usergrade'],"newmanagersixmonths"=>$rowdata['newmanagersixmonths'],"gradename"=>$rowdata['gradename']);
        }elseif(!$flag){
            $managerInfoArray[]=array("userid"=>$rowdata['userid'],"staffrank"=>$rowdata['staffrank'],"usergrade"=>$rowdata['usergrade'],"newmanagersixmonths"=>$rowdata['newmanagersixmonths'],"gradename"=>$rowdata['gradename']);
        }

        $userids.=",".$rowdata['userid'];
    }
    $userids=trim($userids,',');
    //计算提成前删除要计算的月份的总监级别的汇总
    $adb->pquery("DELETE FROM vtiger_achievementsummary WHERE achievementmonth=? AND  userid IN(".$_GET['uid'].")",array($calculation_year_month));

    $param['userids']=$userids;// 所有的总监
    //总监的有下属的提成计算
    $achievementSummary_record_model->setSubordinateUsers();
    $subordinateUsers=$achievementSummary_record_model->subordinateUsers;
    $args=array('activedate'=>$calculation_year_month);
    foreach ($managerInfoArray as $key=>$value){
        print_r($subordinateUsers[$value['userid']]);
        echo '<hr>';
        $args['subordinateusers']=$subordinateUsers[$value['userid']];
        if(count($subordinateUsers[$value['userid']])==0){
            echo 'no subordinateUsers';
            echo '<hr>';
            continue;
        }
        $incumbencyNumber=$achievementSummary_record_model->getIncumbencyNumber($args);
        if($incumbencyNumber==0){
            echo 'incumbencyNumber',$incumbencyNumber;
            echo '<hr>';
            //continue;
        }
        $param['achievementid']=0;
        $param['userid']=$value['userid'];
        $param['staffrank']=$value['staffrank'];
        $param['usergrade']=$value['usergrade'];
        $param['gradename']=$value['gradename'];
        $param['newmanagersixmonths']=$value['newmanagersixmonths'];
        $param['incumbencynumber']=$incumbencyNumber;//业绩月在职下属人数
        $param['realarriveachievement']=0;
        //新单提成的计算
        $param['achievementtype']='newadd';
        $achievementSummary_record_model->calulateCommissionerNewCommission($param);
    }
    echo '------end-----<hr>';
    $info=ob_get_contents();
    $achievementSummary_record_model->comm_logs($info,'jiaobenachievementallot');
}


/**
 * 更新员工等级
 */
if(!empty($_GET['update'])&& $_GET['update']==5){
    ob_start();
    //updateUserGradeRoyaltyhuman($_REQUEST['ym']);
    updateUserGradeRoyaltyhuman();
    $info=ob_get_contents();
    $achievementSummary_record_model=Vtiger_Record_Model::getCleanInstance('AchievementSummary');
    $achievementSummary_record_model->comm_logs($info,'jiaobenachievementallot');
}
/**
 * 更新业绩状态
 */
if(!empty($_GET['update'])&& $_GET['update']==6){
    ob_start();
    $achievementSummary_record_model=Vtiger_Record_Model::getCleanInstance('AchievementSummary');
    $achievementSummary_record_model->updateStatusAch($_REQUEST['ym']);
    $info=ob_get_contents();
    $achievementSummary_record_model->comm_logs($info,'jiaobenachievementallot');
}
/**
 * 更新提成中部门姓名
 */
if(!empty($_GET['update'])&& $_GET['update']==7){
    updateUserDepartment($_REQUEST['ym']);
}

/**
 *  下面是调取员工的级别的处理
 */

function http_request($url, $data = null,$curlset=array()){
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
    curl_close($curl);
    return $output;
}

/**
 * 调用绩效系统的员工等级信息
 * 解析格式:[
{
"userId": 0,用户ID　　商务ID
"lastName": "string",用户名　　
"assessMonth": "2020-03-03",考核月份
"gradeId": 0,级别ID（１－...）商务等级
"gradeName": "string",（对应的中文名称）
"isManager": 0,是否经理（０普通商务，１是经理）员工等级
"isNewManager": 0（６个月内是否是新晋经理）０不是，１是　　上任6个月以内的新经理（见习经理，经理，高级经理）
}
]
 * gradeId[1商务顾问,2储备干部,3见习营销顾问,4营销顾问,5高级营销顾问,6资深营销顾问,7客户经理,8客户总监,9见习经理,10经理,11高级经理,]
 * 珍选生（实习级）  ID:20,管培生（实习级） ID:18,实习智能营销顾问（实习级）ID: 22,见习智能营销顾问（员工级） ID:21,营长（经理级）ID:23
 */
function updateUserGradeRoyalty($current_year,$current_month){
    return false;
    global $adb;
    $url='https://in-jx.71360.com/rule/indicator/findUserStatus/'.date('Y').'/'.date('m');// 线上
    $DataJson=http_request($url);
    $data=json_decode($DataJson,true);
    if($data){
        $createdtime=date('Y-m-d H:i:s');
        $sql="INSERT INTO vtiger_usergraderoyaltybak(userid,username,usergrade,gradename,newmanagersixmonths,staffrank,assessmonth,createdtime,baktime) SELECT userid,username,usergrade,gradename,newmanagersixmonths,staffrank,assessmonth,createdtime,'".$createdtime."' FROM vtiger_usergraderoyalty";
        $adb->pquery($sql,array());
        $sql='TRUNCATE TABLE vtiger_usergraderoyalty';
        $adb->pquery($sql,array());
        $sql='INSERT INTO vtiger_usergraderoyalty(userid,username,usergrade,gradename,newmanagersixmonths,staffrank,assessmonth,createdtime) VALUES';
        $total=count($data);
        $intorecord=500;
        $sqlValue='';
        $i=1;
        foreach($data AS $value){
            $sqlValue.="(".$value['userId'].",'".$value['lastName']."',".$value['gradeId'].",'".$value['gradeName']."',".$value['isNewManager'].",".$value['isManager'].",'".$value['assessMonth']."','".$createdtime."'),";
            if($i%$intorecord==0 || $i==$total){
                $sqlValue=trim($sqlValue,',');
                $adb->pquery($sql.$sqlValue,array());
                $sqlValue='';
            }
            $i++;
        }
    }
}

function cbc_decrypt($data,$key,$iv) {
    $data = base64_decode($data);
    $data = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);
    $padding = ord($data[strlen($data) - 1]);
    return substr($data, 0, -$padding);
}
function updateUserGradeRoyaltyhumanbak(){
    global $adb;
    $url='https://xxhoa.71360.com/cache/employeeAllInfo/getAllEmployee?sign=453c02843968f87c41cd963a3d4a8bf8';
    $DataJson=http_request($url);
    $data=json_decode($DataJson,true);
    if($data['success']==1) {
        $createdtime=date('Y-m-d H:i:s');
        $assessMonth=date('Y-m-d');
        $query='SELECT * FROM vtiger_usergradedic';
        $result=$adb->pquery($query);
        $usergradedic=array();
        while($row=$adb->fetch_array($result)){
            $usergradedic[md5(trim($row['gradename']))]=$row;
        }
        $jsonData = cbc_decrypt($data['data'], 'f4k9f5w7f8g4er26', '5e8y6w45juem1234');
        $arrayData = json_decode($jsonData, true);
        $total=count($arrayData);
        $intorecord=500;
        $sqlValue='';
        $i=1;
        $sql="INSERT INTO vtiger_usergraderoyaltybak(userid,username,usergrade,gradename,newmanagersixmonths,staffrank,assessmonth,createdtime,baktime) SELECT userid,username,usergrade,gradename,newmanagersixmonths,staffrank,assessmonth,createdtime,'".$createdtime."' FROM vtiger_usergraderoyalty";
        $adb->pquery($sql,array());
        $sql='TRUNCATE TABLE vtiger_usergraderoyalty';
        $adb->pquery($sql,array());
        $sql='INSERT INTO vtiger_usergraderoyalty(userid,username,usergrade,gradename,newmanagersixmonths,staffrank,assessmonth,createdtime) VALUES';
        foreach ($arrayData as $value) {
            $md5gradeName=md5(trim($value['position']));
            $gradeId=$usergradedic[$md5gradeName]['usergrade']>0?$usergradedic[$md5gradeName]['usergrade']:0;
            $isManager=$usergradedic[$md5gradeName]['usergrade']>0?$usergradedic[$md5gradeName]['ismanager']:0;
            $isNewManager=$isManager>0?0:0;
            $sqlValue.="(".$value['crmEmployeeId'].",'".$value['name']."',".$gradeId.",'".$value['position']."',".$isNewManager.",".$isManager.",'".$assessMonth."','".$createdtime."'),";
            if(false && $i%$intorecord==0 || $i==$total){
                $sqlValue=trim($sqlValue,',');
                $adb->pquery($sql.$sqlValue,array());
                $sqlValue='';
            }
            $i++;
        }
    }
}

/**
 * 更新业绩所属部门
 * @param $calculation_year_month
 */
function updateUserDepartment($calculation_year_month){
    global $adb;
    $query='UPDATE vtiger_achievementsummary SET vtiger_achievementsummary.departmentid=(select vtiger_useractivemonthnew.departmentid from vtiger_useractivemonthnew WHERE vtiger_useractivemonthnew.userid=vtiger_useractivemonthnew.subordinateid AND vtiger_useractivemonthnew.userid=vtiger_achievementsummary.userid AND vtiger_useractivemonthnew.activedate=vtiger_achievementsummary.achievementmonth limit 1) WHERE vtiger_achievementsummary.achievementmonth=?';
    $adb->pquery($query,array($calculation_year_month));
    $adb->pquery('UPDATE vtiger_achievementsummary SET userfullname=(SELECT last_name FROM vtiger_users WHERE id=vtiger_achievementsummary.userid LIMIT 1) WHERE achievementmonth=?',array($calculation_year_month));
    $adb->pquery("UPDATE vtiger_achievementallot_statistic set departmentid=(select vtiger_useractivemonthnew.departmentid from vtiger_useractivemonthnew WHERE vtiger_useractivemonthnew.userid=vtiger_useractivemonthnew.subordinateid AND vtiger_useractivemonthnew.userid=vtiger_achievementallot_statistic.receivedpaymentownid AND vtiger_useractivemonthnew.activedate=vtiger_achievementallot_statistic.achievementmonth limit 1) WHERE vtiger_achievementallot_statistic.achievementmonth=?",array($calculation_year_month));
    $adb->pquery("UPDATE vtiger_achievementallot_statistic SET department=(SELECT vtiger_departments.departmentname FROM vtiger_departments WHERE vtiger_departments.departmentid=vtiger_achievementallot_statistic.departmentid) WHERE vtiger_achievementallot_statistic.achievementmonth=?",array($calculation_year_month));
}
function updateUserGradeRoyaltyhumanbak2($ym){
    global $adb;
    $calculation_year_month=!empty($ym)?$ym:date('Ym',strtotime('-1 months',strtotime(date('Y-m'.'-01'))));
    $calculation_year=substr($calculation_year_month,0,4);
    $calculation_month=substr($calculation_year_month,4,2);
    $assessMonth=$calculation_year.'-'.$calculation_month.'-01';
    $url='https://in-hr.71360.com/entry/api/api/queryAllEmployee?salaryTime='.$calculation_year_month;
    $curlset=array(CURLOPT_HTTPHEADER=>array(
        "Content-Type:application/json"));
    $DataJson=http_request($url,"[]",$curlset);
    $data=json_decode($DataJson,true);
    echo $data;
    $createdtime=date('Y-m-d H:i:s');
    if($data['success']==1) {
        $sql="INSERT INTO vtiger_usergraderoyaltybak(userid,username,usergrade,gradename,newmanagersixmonths,staffrank,assessmonth,createdtime,baktime) SELECT userid,username,usergrade,gradename,newmanagersixmonths,staffrank,assessmonth,createdtime,'".$createdtime."' FROM vtiger_usergraderoyalty";
        $adb->pquery($sql,array());
        $sql='TRUNCATE TABLE vtiger_usergraderoyalty';
        $adb->pquery($sql,array());
        $arrayData=$data['data'];
        $total=count($arrayData);
        $intorecord=500;
        $sqlValue='';
        $sqlValue1='';
        $i=1;
        $sql='INSERT INTO vtiger_usergraderoyalty(userid,username,usergrade,gradename,newmanagersixmonths,staffrank,assessmonth,createdtime) VALUES';
        $sql1='REPLACE INTO vtiger_usergraderoyaltyupdatelog(userid,positionLevel,ismanager,usergradedate,updatedate) VALUES';
        $query='SELECT * FROM vtiger_usergraderoyaltyupdatelog WHERE updatedate=?';
        $prevcalculation_year_month=date('Y-m',strtotime('-1 months',strtotime(date($calculation_year.'-'.$calculation_month.'-01')))).'-01';
        $result=$adb->pquery($query,array($prevcalculation_year_month));
        $userarray=array();
        while($row=$adb->fetchByAssoc($result)){
            $userarray[$row['userid']]=$row;
        }
        $query='SELECT userid FROM vtiger_usergraderoyalty WHERE assessmonth=?';
        $result=$adb->pquery($query,array($calculation_year.'-'.$calculation_month.'-01'));
        $usercurrentmontharray=array();//当月存的等级
        while($row=$adb->fetchByAssoc($result)){
            $usercurrentmontharray[]=$row['userid'];
        }
        $usergradedate1=$calculation_year.'-'.$calculation_month.'-01';
        //24,25,26,27,
        $employeePositionLevel=array(18,20,21,22,24,25,26,27);
        foreach ($arrayData as $value){
            if(in_array($value['crmEmployeeId'],$usercurrentmontharray)){//记录存在不插入
                continue;
            }
            $usergradedate=$usergradedate1;
            $i++;
            if($value['positionLevel']>0){

            }else{
                continue;
            }
            $isManager=($value['positionLevel']>8 && !in_array($value['positionLevel'],$employeePositionLevel))?1:0;

            if(in_array($value['positionLevel'],array(18,20,21,22))){//实习级
                $value['positionLevel']=1;
            }
            if(in_array($value['positionLevel'],array(24,25,26,27))){//员工级
                $value['positionLevel']=4;
            }
            $isNewManager=0;
            if($isManager==1){
                $entryTime=$value['entryTime'];
                $entryTime=substr($entryTime,0,7);
                $calculation_year=substr($calculation_year_month,0,4);
                $calculation_month=substr($calculation_year_month,4,2);
                $calculation_year_month=$calculation_year.'-'.$calculation_month;
                if($entryTime==$calculation_year_month){//当月入职即经理的
                    $isNewManager=1;
                    //$usergradedate='0000-00-00';
                }else {
                    if (!empty($userarray[$value['crmEmployeeId']])) {
                        $tempuserarray = $userarray[$value['crmEmployeeId']];
                        if (0 == $tempuserarray['ismanager']) {//上个月不是经理这个月为经理
                            $isNewManager = 1;
                        } else if ($tempuserarray['positionlevel'] != $value['positionLevel']) {//与上个月等级不一样
                            $isNewManager = 1;
                        } else {//与上个月等级一样
                            $prevsixthmonth = date('Y-m', strtotime('-6 months', strtotime(date($usergradedate)))) . '-01';
                            if ($prevsixthmonth > $tempuserarray['usergradedate']) {//当前月之前6个月是
                                $isNewManager = 0;
                            } else {
                                $isNewManager = 1;
                            }
                            $usergradedate = $tempuserarray['usergradedate'];
                        }
                    } else {//直接为经理
                        $isNewManager = 1;
                    }
                }

            }else{
                $usergradedate='0000-00-00';
            }
            $sqlValue.="(".$value['crmEmployeeId'].",'".$value['name']."','".$value['positionLevel']."','".$value['position']."',".$isNewManager.",".$isManager.",'".$assessMonth."','".$createdtime."'),";
            $sqlValue1.="(".$value['crmEmployeeId'].",'".$value['positionLevel']."',".$isManager.",'".$usergradedate."','".$calculation_year.'-'.$calculation_month."-01'),";
            if($i%$intorecord==0 || $i==$total){
                $sqlValue=trim($sqlValue,',');
                $adb->pquery($sql.$sqlValue,array());
                $sqlValue='';
                $sqlValue1=trim($sqlValue1,',');
                $adb->pquery($sql1.$sqlValue1,array());
                $sqlValue1='';
            }
        }
    }
}

function updateUserGradeRoyaltyhuman(){
    global $adb;
    $calculation_year_month=!empty($_REQUEST['ym'])?$_REQUEST['ym']:date('Ym',strtotime('-1 months',strtotime(date('Y-m'.'-01'))));
    $calculation_year=substr($calculation_year_month,0,4);
    $calculation_month=substr($calculation_year_month,4,2);
    $url='https://in-hr.71360.com/entry/api/api/queryAllEmployee?salaryTime='.$calculation_year_month;
    echo $url;
    $curlset=array(CURLOPT_HTTPHEADER=>array(
        "Content-Type:application/json"));
    $DataJson=http_request($url,"[]",$curlset);
    //echo $DataJson;
    echo '<br>--------start-------<hr>';
    $data=json_decode($DataJson,true);
    if($data['success']==1) {
        $createdtime=date('Y-m-d H:i:s');
        $sql="INSERT INTO vtiger_usergraderoyaltybak(userid,username,usergrade,gradename,newmanagersixmonths,staffrank,assessmonth,createdtime,departmentid,baktime) SELECT userid,username,usergrade,gradename,newmanagersixmonths,staffrank,assessmonth,createdtime,departmentid,'".$createdtime."' FROM vtiger_usergraderoyalty";
        $adb->pquery($sql,array());
        $sql='TRUNCATE TABLE vtiger_usergraderoyalty';
        $adb->pquery($sql,array());
        $assessMonth=date('Y-m-d');
        $arrayData=$data['data'];
        $total=count($arrayData);
        $intorecord=500;
        $sqlValue='';
        $sqlValue1='';
        $i=1;
        $sql='INSERT INTO vtiger_usergraderoyalty(userid,username,usergrade,gradename,newmanagersixmonths,staffrank,assessmonth,createdtime,departmentid) VALUES';
        $sql1='REPLACE INTO vtiger_usergraderoyaltyupdatelog(userid,positionLevel,ismanager,usergradedate,updatedate) VALUES';
        $query='SELECT * FROM vtiger_usergraderoyaltyupdatelog WHERE updatedate=?';
        $prevcalculation_year_month=date('Y-m',strtotime('-1 months',strtotime(date($calculation_year.'-'.$calculation_month.'-01')))).'-01';
        $result=$adb->pquery($query,array($prevcalculation_year_month));
        $userarray=array();
        while($row=$adb->fetchByAssoc($result)){
            $userarray[$row['userid']]=$row;
        }
        $usergradedate1=$calculation_year.'-'.$calculation_month.'-01';
        $employeePositionLevel=array(18,20,21,22,24,25,26,27,29);//大于8商务等级
        $practicePositionLevel=array(18,20,22,);//实习商务等级
        $staffositionLevel=array(21,24,25,26,27,29);//员工商务等级
        foreach ($arrayData as $value){
            $usergradedate=$usergradedate1;
            $i++;
            if($value['positionLevel']>0){

            }else{
                //continue;
            }
            $isManager=($value['positionLevel']>8  && !in_array($value['positionLevel'],$employeePositionLevel))?1:0;

            if(in_array($value['positionLevel'],$practicePositionLevel)){
                $value['positionLevel']=1;
            }
            if(in_array($value['positionLevel'],$staffositionLevel)){
                $value['positionLevel']=4;
            }
            $isNewManager=0;
            if($isManager==1){
                $entryTime=$value['entryTime'];//入职日期
                if(!empty($userarray[$value['crmEmployeeId']])){
                    $tempuserarray=$userarray[$value['crmEmployeeId']];
                    if(0==$tempuserarray['ismanager']){//上个月不是经理这个月为经理
                        $isNewManager=1;
                    }else{//与上个月等级一样
                        $prevsixthmonth=date('Y-m',strtotime('-5 months',strtotime(date($usergradedate)))).'-01';
                        if($prevsixthmonth>$tempuserarray['usergradedate']){//当前月之前6个月是
                            $isNewManager=0;
                        }else{
                            $isNewManager=1;
                        }
                        $usergradedate=$tempuserarray['usergradedate'];
                    }
                }else{//直接为经理
                    $isNewManager=1;
                    $calculation_year_month=$calculation_year.'-'.$calculation_month;
                    if($calculation_year_month==substr($entryTime,0,7)){//入账即经理
                        $day=date('d',strtotime($entryTime));
                        if($day>15){//15号后入职的算下个月
                            $usergradedate=date('Y-m',strtotime('+1 months',strtotime($entryTime))).'-01';
                        }else{
                            $usergradedate=$calculation_year_month.'-01';
                        }
                    }else{
                        $usergradedate='';
                    }
                }

            }else{
                $usergradedate='0000-00-00';
            }
            $sqlValue.="(".$value['crmEmployeeId'].",'".$value['name']."','".$value['positionLevel']."','".$value['position']."',".$isNewManager.",".$isManager.",'".$assessMonth."','".$createdtime."','".$value['departmentId']."'),";
            $sqlValue1.="(".$value['crmEmployeeId'].",'".$value['positionLevel']."',".$isManager.",'".$usergradedate."','".$calculation_year.'-'.$calculation_month."-01'),";
            if($i%$intorecord==0 || $i==$total){
                $sqlValue=trim($sqlValue,',');
                $adb->pquery($sql.$sqlValue,array());
                $sqlValue='';
                $sqlValue1=trim($sqlValue1,',');
                $adb->pquery($sql1.$sqlValue1,array());
                $sqlValue1='';
            }
        }
        $query='UPDATE vtiger_achievementallot_statistic,vtiger_usergraderoyalty SET vtiger_achievementallot_statistic.departmentid=if((vtiger_usergraderoyalty.departmentid is null OR vtiger_usergraderoyalty.departmentid=\'\'),vtiger_achievementallot_statistic.departmentid,vtiger_usergraderoyalty.departmentid) WHERE vtiger_usergraderoyalty.userid=vtiger_achievementallot_statistic.receivedpaymentownid AND vtiger_achievementallot_statistic.achievementmonth=?';
        $adb->pquery($query,array($calculation_year.'-'.$calculation_month));
        $adb->pquery('UPDATE vtiger_achievementallot_statistic SET userfullname=(SELECT last_name FROM vtiger_users WHERE id=vtiger_achievementsummary.userid LIMIT 1) WHERE achievementmonth=?',array($calculation_year.'-'.$calculation_month));
    }

    echo '--------end-------<hr>';
}






