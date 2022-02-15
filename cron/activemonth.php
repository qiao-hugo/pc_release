<?php
/**
 * 求当月员工所属的下级
 * 每月1号执行
 */
set_time_limit(0);
ini_set('memory_limit',"1024M");
$dir=trim(__DIR__,DIRECTORY_SEPARATOR);
$dir=trim(__DIR__,'cron');
ini_set("include_path", $dir);
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");
ini_set('display_errors','off');

include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
date_default_timezone_set("PRC");
$currentDay=date('d');
if(1!=$currentDay && empty($_REQUEST['update'])){
    echo "no first day\n";
    exit;
}
echo "求当月的员工的下级\n";
echo "脚本开始执行start\n";
$start_time = microtime(true);
global $adb;
$leavedate=date('Y-m',strtotime('-2 years'));
$query="SELECT id,reports_to_id AS 'pid',`status`,leavedate,isdimission,vtiger_user2department.departmentid FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id WHERE id>1 AND id!=reports_to_id AND (vtiger_users.`status`='Active' OR left(vtiger_users.leavedate,7)>='".$leavedate."')";
echo $query;
$result=$adb->pquery($query);
$array=array();
while($row=$adb->fetchByAssoc($result)){
    $array[$row['id']]=$row;
}
/*
*2.获取某个会员的无限下级方法  非递归方法
*$members是所有会员数据表,$mid是用户的id
*/
function GetTeamMember($members, $mid) {
    $Teams=array();//最终结果
    $mids=array($mid);//第一次执行时候的用户id
    do {
        $othermids=array();
        $state=false;
        foreach ($mids as $valueone) {
            foreach ($members as $key => $valuetwo) {
                if($valuetwo['pid']==$valueone){
                    $Teams[]=$valuetwo;//找到我的下级立即添加到最终结果中
                    $othermids[]=$valuetwo['id'];//将我的下级id保存起来用来下轮循环他的下级
                    //array_splice($members,$key,1);//从所有会员中删除他
                    $state=true;
                }
            }
        }
        $mids=$othermids;//foreach中找到的我的下级集合,用来下次循环
    } while ($state==true);

    return $Teams;
}
$arraytemp=array();
$activedate=date('Y-m',strtotime("-1 months"));
$DELSQL='DELETE FROM vtiger_useractivemonthnew WHERE activedate=?';
$adb->pquery($DELSQL,array($activedate));
$SQL='INSERT INTO `vtiger_useractivemonthnew`(`userid`, `activedate`, `leavedate`, `departmentid`, `subordinateid`, `status`, `isdimission`) VALUES';
foreach($array as $key=>$value){
    if(38==$key){//跳过
        continue;
    }
    $arraytemp=GetTeamMember($array ,$key);
    $SQLstr='';
    foreach($arraytemp as $svalue){
        $status=$svalue['status']=='Active'?0:1;
        $isdimission=$status==1?1:$svalue['isdimission'];
        if($status || $isdimission){
            $leavedate=substr($svalue['leavedate'],0,7);
            if($activedate<=$leavedate){//如果是当月离职则还算在职人员
                $status=0;
            }else{
                $status=1;
                $isdimission=1;
            }
        }
        $SQLstr.='('.$key.",'".$activedate."','".$svalue['leavedate']."','".$svalue['departmentid']."',".$svalue['id'].",".$status.",".$isdimission."),";
    }
    $status=$value['status']=='Active'?0:1;
    $isdimission=$status==1?1:$value['isdimission'];
    if($status || $isdimission){
        $leavedate=substr($value['leavedate'],0,7);
        if($activedate<=$leavedate){//如果是当月离职则还算在职人员
            $status=0;
        }else{
            $status=1;
            $isdimission=1;
        }
    }
    $SQLstr.='('.$key.",'".$activedate."','".$value['leavedate']."','".$value['departmentid']."',".$value['id'].",".$status.",".$isdimission.")";
    $SQLstr=trim($SQLstr,',');
    $adb->pquery( $SQL.$SQLstr,array());
}
$end_time = microtime(true);

$execution_time = ($end_time - $start_time);
echo "脚本执行时间".$execution_time."秒\n";
echo "脚本执行完成end\n";
echo 'complete';
//print_r($arraytemp);
exit;
/*$member = array(
    array('id'=>1, 'pid'=>0, 'nickname' => 'A'),
    array('id'=>2, 'pid'=>1, 'nickname' => 'B'),
    array('id'=>3, 'pid'=>1, 'nickname' => 'C'),
    array('id'=>4, 'pid'=>8, 'nickname' => 'D'),
    array('id'=>5, 'pid'=>3, 'nickname' => 'E'),
    array('id'=>6, 'pid'=>3, 'nickname' => 'F'),
    array('id'=>7, 'pid'=>3, 'nickname' => 'G'),
    array('id'=>8, 'pid'=>8, 'nickname' => 'H')
);
function GetTeamMember($members, $mid) {
    $Teams=array();//最终结果
    $mids=array($mid);//第一次执行时候的用户id
    do {
        $othermids=array();
        $state=false;
        foreach ($mids as $valueone) {
            foreach ($members as $key => $valuetwo) {
                if($valuetwo['pid']==$valueone){
                    $Teams[]=$valuetwo[id];//找到我的下级立即添加到最终结果中
                    $othermids[]=$valuetwo['id'];//将我的下级id保存起来用来下轮循环他的下级
                    array_splice($members,$key,1);//从所有会员中删除他
                    $state=true;
                }
            }
        }
        $mids=$othermids;//foreach中找到的我的下级集合,用来下次循环
    } while ($state==true);

    return $Teams;
}
$res=GetTeamMember($member ,1);
//递归方法
//获取用户的所有下级ID
function get_downline($members,$mid,$level=0){
    $arr=array();
    foreach ($data as $key => $v) {
        if($v['pid']==$mid){  //pid为0的是顶级分类
            $v['level'] = $level+1;
            $arr[]=$v;
            $arr = array_merge($arr,get_downline($data,$v['id'],$level+1));
        }
    }
    return $arr;
}

*/
