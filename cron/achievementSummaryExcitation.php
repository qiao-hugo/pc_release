<?php
/**
 * 注：12月份业绩提成激励,脚本只使用一次
 * 
 * */
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

echo '<h1>-------<b>业绩提成激励计算<b>-------</h1><hr>';
echo '<h2>员工业绩提成激励开始</h2><br>';
//1.获取中小所有商务,
$list = getAllAchievement();
$insertDatas = [];
foreach($list as $val){
    echo '员工id:'.$val['userid'].'的业绩提成激励计算开始<br>';
    $lastarriveachievement = empty($val['lastarriveachievement']) ? 0 : $val['lastarriveachievement'];
    $arriveachievement = empty($val['arriveachievement']) ? 0 : $val['arriveachievement'];
    $userid = $val['userid'];
    $royalty = empty($val['royalty']) ? 0 : $val['royalty'];
    echo '----------11月份到账业绩：'.$lastarriveachievement.'，12月份到账业绩:'.$arriveachievement.'<br>';
    //2.计算由11月份业绩产生的提成激励
    $ro = novemberExcitationRatio($lastarriveachievement, $arriveachievement);
    $novemberExcSummary = bcdiv(bcmul($arriveachievement,$ro,4),100,2);
    echo '----------11月业绩:'.$lastarriveachievement.',获得的激励提成点：'.$ro.',12月业绩：'.$arriveachievement.',激励提成：'.$novemberExcSummary.'<br>';
    //3.计算由12月份业绩产生的提成激励
    $octoberExcSummary = octoberExcitation($arriveachievement);
    $total = $novemberExcSummary + $octoberExcSummary;
    echo '----------总的激励提成：'.$total.'<br>';
    //------------数据入表
    $insertData = [];
    $insertData['userid'] = $userid;
    $insertData['lastarriveachievement'] = $lastarriveachievement;
    $insertData['arriveachievement'] = $arriveachievement;
    $insertData['royalty'] = $royalty;
    $insertData['totalroyalty'] = $total;
    $insertData['lastheightratio'] = $ro;
    $insertData['heightratio'] = octoberExcitationRatio($arriveachievement);
    $insertData['ismanager'] = 0;
    $insertData['date'] = '2021-12';
    $insertData['createdtime'] = date('Y-m-d H:i:s');
    $insertData['excitation_a'] = $novemberExcSummary;
    $insertData['excitation_b'] = $octoberExcSummary;
    $insertDatas[] = $insertData;
    echo '员工id:'.$val['userid'].'的业绩提成激励计算结束<hr>';
}
if(!empty($insertDatas)){
    //避免数据重复执行入表，需先删除员工的数据
    deleteExcitationData($insertDatas);
    insertExcitationData($insertDatas);
}
echo '<h2>员工业绩提成激励结束</h2><br>';

echo '<h2>经理业绩提成激励开始</h2><br>';
//1.获取中小所有经理,
$managerList = getAllAchievementManage();
$managerInsertDatas = [];
foreach($managerList as $val){
    echo '经理id:'.$val['userid'].'的业绩提成激励计算开始<br>';
    $november = empty($val['lastarriveachievement']) ? 0 : $val['lastarriveachievement'];
    $arriveachievement = empty($val['arriveachievement']) ? 0 : $val['arriveachievement'];
    $userid = $val['userid'];
    $royalty = empty($val['royalty']) ? 0 : $val['royalty'];
    echo '--------11月份到账业绩：'.$november.'，12月份到账业绩:'.$arriveachievement.'，12月份提成：'.$val['royalty'].'<br>';

    $baseAmountRatio = getAreaManagerRoyaltyRatio($userid);
    //第一部分
    $excitationRoyalty = manageExcitationRoyalty($userid, $arriveachievement,$baseAmountRatio);
    $excitationSummary = bcdiv(bcmul($arriveachievement,$excitationRoyalty,4),100,2);
    echo '---------------A的第一部分激励提成：'.$excitationSummary.'<br>';
    //第二部分
    $hightRoyalty = getManageHightRoyalty($userid, $november,$baseAmountRatio);
    $octoberSummary = octoberManageExcitation($arriveachievement, $hightRoyalty,$baseAmountRatio);
    $managerTotal = $excitationSummary + $octoberSummary;
    echo '--------经理'.$userid.'计算获得的提成A:'.$excitationSummary.'+'.$octoberSummary.'='.$total.'<br>';

    $excitation = $managerTotal - $royalty;
    //------------数据入表
    $insertData = [];
    $insertData['userid'] = $userid;
    $insertData['lastarriveachievement'] = $november;
    $insertData['arriveachievement'] = $arriveachievement;
    $insertData['totalroyalty'] = $excitation;
    $insertData['royalty'] = $royalty;
    $insertData['lastheightratio'] = $hightRoyalty;
    $insertData['heightratio'] = getManageHightRoyalty($userid, $arriveachievement,$baseAmountRatio);
    $insertData['ismanager'] = 1;
    $insertData['date'] = '2021-12';
    $insertData['createdtime'] = date('Y-m-d H:i:s');
    $insertData['excitation_a'] = $excitationSummary;
    $insertData['excitation_b'] = $octoberSummary;
    $managerInsertDatas[] = $insertData;
    echo '--------经理'.$userid.'最终的激励提成:'.$managerTotal.'-'.$royalty.'='.$excitation.'<br>';
    echo '经理id:'.$val['userid'].'的业绩提成激励计算结束<hr>';
}
if(!empty($managerInsertDatas)){
    //避免数据重复执行入表，需先删除员工的数据
    deleteExcitationData($managerInsertDatas);
    insertExcitationData($managerInsertDatas);
}
echo '<h2>经理业绩提成激励结束</h2><br>';

//获取所有员工id、11月的到账业绩、12月的到账业绩
function getAllAchievement(){
    global $adb;
    $sql = "SELECT ugr.userid,SUM(IF(va.`achievementmonth`='2021-11',va.arriveachievement,0)) AS lastarriveachievement,SUM(IF(va.`achievementmonth`='2021-12',va.arriveachievement,0)) AS arriveachievement,SUM(IF(va.`achievementmonth`='2021-12',va.royalty,0)) AS royalty FROM vtiger_achievementsummary AS va LEFT JOIN vtiger_usergraderoyalty AS ugr ON ugr.userid = va.userid WHERE ugr.staffrank = 0 AND va.`achievementmonth` IN ('2021-11', '2021-12') AND va.achievementtype IN ('newadd','mrenew') AND ugr.usergrade > 0 GROUP BY ugr.userid";
    $result = $adb->run_query_allrecords($sql);
    return $result;
}

//计算11月份提成激励
function novemberExcitationRatio($november){
    //获取激励提成点
    $ro = 0;
    if($november<=10000){
        $ro = 0;
    }elseif($november>10000 && $november<=30000){
        $ro = 3;
    }elseif($november>30000 && $november<=60000){
        $ro = 4;
    }elseif($november>60000 && $november<=120000){
        $ro = 5;
    }elseif($november>120000){
        $ro = 20;
    }
    return $ro;
}

//计算员工12月份最高提成点
function octoberExcitationRatio($october){
    $royaltyRatioValue = 0;
    if(100000 < $october ){
        $royaltyRatioValue = 20;
    }else if(60000<$october && $october<=100000){
        $royaltyRatioValue = 18;
    }else if(40000<$october && $october<=60000){
        $royaltyRatioValue = 12;
    }else if(22000<$october && $october<=40000){
        $royaltyRatioValue = 8;
    }else if(10000<$october && $october<=22000){
        $royaltyRatioValue = 3;
    }else if($october<=10000){
        $royaltyRatioValue = 0;
    }
    return $royaltyRatioValue;
}

//计算12月份提成激励
function octoberExcitation($october){
    $royalty=0;
    echo '----------12月份激励提成：0';
    do{
        if($october <= 0){
            break;
        }
        if(100000 < $october ){
            $exceedPart = bcsub($october,100000,4);
            $october = 100000;
            $royaltyRatioValue = 20;
        }else if(60000<$october && $october<=100000){
            $exceedPart = bcsub($october,60000,4);
            $october = 60000;
            $royaltyRatioValue = 18;
        }else if(40000<$october && $october<=60000){
            $exceedPart = bcsub($october,40000,4);
            $october = 40000;
            $royaltyRatioValue = 12;
        }else if(22000<$october && $october<=40000){
            $exceedPart = bcsub($october,22000,4);
            $october = 22000;
            $royaltyRatioValue = 8;
        }else if(10000<$october && $october<=22000){
            $exceedPart = bcsub($october,10000,4);
            $october = 10000;
            $royaltyRatioValue = 3;
        }else if($october<=10000){
            $exceedPart = bcsub($october,0,4);
            $october = 0;
            $royaltyRatioValue = 0;
        }
        echo ' + '.$exceedPart.'*'.$royaltyRatioValue.'%';
        $royalty += bcdiv(bcmul($exceedPart,$royaltyRatioValue,6),100,4);
    }while(true);
    echo '='.$royalty.'<br>';
    return $royalty;
}

//获取所有经理id
function getAllAchievementManage(){
    global $adb;
    //查询所有的经理
    $sql = "SELECT 
  ugr.userid,
  SUM(IF(va.`achievementmonth`='2021-11',va.arriveachievement,0)) AS lastarriveachievement,
  SUM(IF(va.`achievementmonth`='2021-12',va.arriveachievement,0)) AS arriveachievement,
  SUM(IF(va.`achievementmonth`='2021-12',va.royalty,0)) AS royalty
FROM
  vtiger_usergraderoyalty AS ugr 
  LEFT JOIN vtiger_achievementsummary AS va 
    ON ugr.userid = va.userid 
WHERE ugr.staffrank = 1 
  AND va.`achievementmonth` IN ('2021-11', '2021-12') 
  AND va.achievementtype IN ('newadd','mrenew')
  AND usergrade IN (9,10,11,23) GROUP BY ugr.userid";
    $result = $adb->run_query_allrecords($sql);
    return $result;
}

//获取属地系数
function getAreaManagerRoyaltyRatio($userid){
    //地区的比例
    $achievementSummary_record_model=Vtiger_Record_Model::getCleanInstance('AchievementSummary');
    $regionalLevel=$achievementSummary_record_model->getRegionalLevel($userid);//获取等级
    $areaManagerRoyaltyRatioArr=array(
        1=>1,//第一类
        2=>0.9,//第二类
        3=>0.8,//第三类
        4=>0.7,//第四类
    );
    $baseAmountRatio = isset($areaManagerRoyaltyRatioArr[$regionalLevel]) ? $areaManagerRoyaltyRatioArr[$regionalLevel] : 0;
    echo '--------属地等级:'.$regionalLevel.',属地系数:'.$baseAmountRatio.'<br>';
    return $areaManagerRoyaltyRatioArr[$regionalLevel];
}

//获取奖励提成点
function manageExcitationRoyalty($userid,$october,$baseAmountRatio){
    $royalty = 0;
    if (300000*$baseAmountRatio < $october) {
        $royalty = 3;
    } else if (250000*$baseAmountRatio <= $october && $october <= 300000*$baseAmountRatio) {
        $royalty = 1;
    } else if ($october < 250000*$baseAmountRatio) {
        $royalty = 0;
    }
    echo '--------业绩：'.$october.',A的第一部分奖励提成点:'.$royalty.'<br>';
    return $royalty;
}

//经理最高提成点
function getManageHightRoyalty($userid, $arriveachievement,$baseAmountRatio){
    $royalty=0;
    if (300000*$baseAmountRatio < $arriveachievement) {
        $royalty = 6;
    } else if (250000*$baseAmountRatio <= $arriveachievement && $arriveachievement <= 300000*$baseAmountRatio) {
        $royalty = 5;
    } else if (200000*$baseAmountRatio < $arriveachievement && $arriveachievement < 250000*$baseAmountRatio) {
        $royalty = 5;
    } else if (180000*$baseAmountRatio < $arriveachievement && $arriveachievement <= 200000*$baseAmountRatio) {
        $royalty = 4;
    } else if (140000*$baseAmountRatio < $arriveachievement && $arriveachievement <= 180000*$baseAmountRatio) {
        $royalty = 3;
    } else if (120000*$baseAmountRatio < $arriveachievement && $arriveachievement <= 140000*$baseAmountRatio) {
        $royalty = 2;
    } else if (0 < $arriveachievement && $arriveachievement <= 120000*$baseAmountRatio) {
        $royalty = 1;
    }
    echo '--------业绩:'.$arriveachievement.',最高提成点:'.$royalty.'<br>';
    return $royalty;
}

//计算第二部分提成激励
function octoberManageExcitation($october, $hightRoyalty,$baseAmountRatio){
    $royalty=0;
    echo '---------------A的第二部分激励提成：0';
    do{
        if($october <= 0){
            break;
        }
        $currentKey=0;
        if (300000*$baseAmountRatio < $october) {
            $exceedPart = $october;
            $royaltyRatioValue = 6;
            $october = 0;
        } else if (250000*$baseAmountRatio <= $october && $october <= 300000*$baseAmountRatio) {
            $royaltyRatioValue = 5;
            if($hightRoyalty >= 5){
                $royaltyRatioValue = $hightRoyalty;
            }
            $exceedPart = $october;
            $october = 0;
        } else if (200000*$baseAmountRatio < $october && $october < 250000*$baseAmountRatio) {
            if($hightRoyalty >= 5){
                $royaltyRatioValue = $hightRoyalty;    
                $exceedPart = $october;
                $october = 0;
            }else{
                $exceedPart = bcsub($october,200000*$baseAmountRatio,4);
                $october = 200000*$baseAmountRatio;
                $royaltyRatioValue = 5;
            }
        } else if (180000*$baseAmountRatio < $october && $october <= 200000*$baseAmountRatio) {
            if($hightRoyalty >= 4){
                $royaltyRatioValue = $hightRoyalty;    
                $exceedPart = $october;
                $october = 0;
            }else{
                $exceedPart = bcsub($october,180000*$baseAmountRatio,4);
                $october = 180000*$baseAmountRatio;
                $royaltyRatioValue = 4;
            }
        } else if (140000*$baseAmountRatio < $october && $october <= 180000*$baseAmountRatio) {
            if($hightRoyalty >= 3){
                $royaltyRatioValue = $hightRoyalty;    
                $exceedPart = $october;
                $october = 0;
            }else{
                $exceedPart = bcsub($october,140000*$baseAmountRatio,4);
                $october = 140000*$baseAmountRatio;
                $royaltyRatioValue = 3;
            }
        } else if (120000*$baseAmountRatio < $october && $october <= 140000*$baseAmountRatio) {
            if($hightRoyalty >= 2){
                $royaltyRatioValue = $hightRoyalty;    
                $exceedPart = $october;
                $october = 0;
            }else{
                $exceedPart = bcsub($october,120000*$baseAmountRatio,4);
                $october = 120000*$baseAmountRatio;
                $royaltyRatioValue = 2;
            }
        } else if (0 < $october && $october <= 120000*$baseAmountRatio) {
            if($hightRoyalty >= 1){
                $royaltyRatioValue = $hightRoyalty;    
                $exceedPart = $october;
                $october = 0;
            }else{
                $exceedPart = bcsub($october,0,4);
                $october = 0;
                $royaltyRatioValue = 1;
            }
        }
        echo ' + '.$exceedPart.'*'.$royaltyRatioValue.'%';
        $royalty += bcdiv(bcmul($exceedPart,$royaltyRatioValue,6),100,4);
    }while(true);
    echo '='.$royalty.'<br>';
    return $royalty;
}

//插入数据
function insertExcitationData($data){
    global $adb;
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
    $insertWordsSql = 'INSERT INTO vtiger_achievementexcitation ('.implode(',',$field).') VALUES '.trim($valuesStr,',');
    global $adb;
    $adb->pquery($insertWordsSql,$values);
    return true;
}

//删除数据
function deleteExcitationData($data){
    global $adb;
    if(empty($data)){
        return false;
    }
    $userids = array_column($data,'userid');
    $insertWordsSql = 'delete from vtiger_achievementexcitation where userid in ('.implode(',',$userids).')';
    global $adb;
    $adb->pquery($insertWordsSql,array());
    return true;
}
