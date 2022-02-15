<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/11/6
 * Time: 15:09
 */

$dir = trim(__DIR__, DIRECTORY_SEPARATOR);
$dir = trim(__DIR__, 'cron');
ini_set("include_path", $dir);
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");
error_reporting(0);


include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';

require('crmcache/indicatorsetting.php');

vglobal('default_language', $default_language);
$currentLanguage = 'zh_cn';
vglobal('current_language', $currentLanguage);

echo '插入开始\r\n '.time();
timingLog('--------------------------------统计日期:'.date("Y-m-d").' start------------------------------------------');
timingLog('执行开始时间:'.date("Y-m-d H:i:s",time()));
$insertStartTime = time();
$result1 = array();
$lastThreeYear = date("Y-01-01", strtotime("-3 year"));
$db=PearDatabase::getInstance();
/**STRAT新签客户数**/
$db->pquery("TRUNCATE TABLE vtiger_signaccount", array());
$db->pquery("INSERT INTO vtiger_signaccount(userid,
                        servicecontractsid,
                        scalling,
                        edate
                        ) SELECT 
                        divide.receivedpaymentownid AS userid,
                        divide.servicecontractid,
                        divide.scalling,
                        left(signdate,10) AS edate
                        FROM 
                        (SELECT receivedpaymentownid,servicecontractid,scalling
                        FROM vtiger_servicecontracts_divide AS a WHERE
                        servicecontractid>0 AND
                        receivedpaymentownid>0 AND
                        (SELECT  COUNT(1) FROM vtiger_servicecontracts_divide AS b WHERE b.servicecontractid = a.servicecontractid AND b.scalling >= a.scalling) <= 2
                        ORDER BY a.servicecontractid ASC , a.scalling DESC) AS divide
                        LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=divide.servicecontractid LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid=vtiger_crmentity.crmid 
                        WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.modulestatus='c_complete' AND vtiger_servicecontracts.signdate!='' AND vtiger_servicecontracts.signdate IS NOT NULL AND signdate>?", array($lastThreeYear));
/***END新签客户数**/
/**STRAT新增客户数**/
$db->pquery("TRUNCATE TABLE vtiger_addacounts", array());
$db->pquery("INSERT INTO vtiger_addacounts(userid,
                        accountid,
                        edate
                        ) SELECT smcreatorid,
                        crmid,
                        left(createdtime,10)
                         FROM vtiger_crmentity WHERE setype='Accounts' AND deleted=0 AND createdtime>?", array($lastThreeYear));
/***END新增客户数**/
/***STRAT划转客户**/
$db->pquery("TRUNCATE TABLE vtiger_transferaccount", array());
$db->pquery("INSERT INTO vtiger_transferaccount(userid,
                            accountid,
                            edate
                            )
                            SELECT newsmownerid AS userid,
                            accountid,
                            left(createdtime,10) AS edate
                            FROM vtiger_accountsmowneridhistory WHERE accountid>0 AND newsmownerid!=modifiedby AND createdtime>? GROUP BY left(createdtime,10),accountid", array($lastThreeYear));
/***END划转客户**/
/***STRAT公海领取客户**/
$db->pquery("TRUNCATE TABLE vtiger_highseaaccount", array());
$db->pquery("INSERT INTO vtiger_highseaaccount(userid,
                            accountid,
                            edate
                            )
                            SELECT smownerid AS userid,
                            accountid,
                            left(createdtime,10) AS edate
                            FROM vtiger_accountsfromtemporary WHERE accountid>0 AND createdtime>?  GROUP BY left(createdtime,10),accountid", array($lastThreeYear));

/***END公海领取客户**/
/***STRAT陪访客户数**/
$db->pquery("TRUNCATE TABLE vtiger_accompanyingvisits", array());
$db->pquery("INSERT INTO vtiger_accompanyingvisits(userid,
                            visitingorderid,
                            edate
                            )
                            SELECT SUBSTRING_INDEX(accompany,' |##| ',1) AS userid,
                            visitingorderid,
                            left(startdate,10) AS edate
                            
                            FROM vtiger_visitingorder LEFT JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid=vtiger_crmentity.crmid 
                            WHERE deleted=0 AND modulestatus='c_complete' AND accompany IS NOT NULL AND accompany!='' AND SUBSTRING_INDEX(accompany,' |##| ',1)!='' AND extractid!=SUBSTRING_INDEX(accompany,' |##| ',1)  AND related_to>0 AND extractid>0 AND startdate>? GROUP BY related_to,left(startdate,10)", array($lastThreeYear));
/***END陪访客户数**/

//            /***STRAT新开拜访数**/
$db->pquery("TRUNCATE TABLE vtiger_newvisitingnum", array());
$db->pquery("INSERT INTO vtiger_newvisitingnum(userid,
                            visitingorderid,
                            edate
                            )
                            SELECT a.extractid AS userid,
                            a.visitingorderid,
                            left(b.createdtime,10) AS edate
                            FROM vtiger_visitingorder a 
                            left join vtiger_crmentity b on b.crmid = a.visitingorderid
                            WHERE  a.modulestatus in('c_complete','a_normal') AND  a.related_to>0 AND 
                                  a.extractid>0 AND a.isfirstvisit=1 AND b.createdtime>? GROUP BY a.related_to,left(b.createdtime,10)", array($lastThreeYear));
/***END新开拜访数**/

/***STRAT审核次数**/
$db->pquery("TRUNCATE TABLE vtiger_verifynum", array());
$db->pquery("INSERT INTO vtiger_verifynum(userid,
                             salesorderworkflowstagesid,
                             edate
                             )
                             SELECT 
                             auditorid AS userid,
                            salesorderworkflowstagesid,
                            left(auditortime,10) AS edate
                            FROM vtiger_salesorderworkflowstages
                            WHERE isaction=2 AND auditortime>?", array($lastThreeYear)
);
/***END审核次数**/

/***STRAT跟进客户数**/
$db->pquery("TRUNCATE TABLE vtiger_followupaccountnum", array());
$db->pquery("INSERT INTO vtiger_followupaccountnum(userid,
                             modcommentsid,
                             accountid,
                             edate
                             )
                             SELECT 
                             creatorid AS userid,
                            modcommentsid,
                            related_to as accountid,
                            left(addtime,10) AS edate
                            FROM vtiger_modcomments
                            WHERE addtime>?
                            group by edate,accountid,userid order by addtime desc", array($lastThreeYear)
);
/***END跟进客户数**/

/***STRAT评论客户数**/
$db->pquery("TRUNCATE TABLE vtiger_commentaccountnum", array());
$db->pquery("INSERT INTO vtiger_commentaccountnum(userid,
                             submodcommentsid,
                             accountid,
                             edate
                             )
                             SELECT 
                             a.creatorid AS userid,
                            a.modcommentsid,
                            b.related_to as accountid,
                            left(a.createdtime,10) AS edate
                            FROM vtiger_submodcomments a left join vtiger_modcomments b 
                            on a.modcommentsid=b.modcommentsid
                            where a.createdtime>?
                            group by edate,accountid order by createdtime desc", array($lastThreeYear)
);
/***END评论客户数**/
echo '数据准备完毕时间'.time().' 耗时:'.(time()-$insertStartTime).'\r\n';
$preparedDuration = time()-$insertStartTime;
timingLog('数据准备完毕时间'.time().' 耗时:'.(time()-$insertStartTime));

$preparedTime = time();
/***STRAT总表**/
$db->pquery("TRUNCATE TABLE vtiger_eworkstatistics", array());
$db->pquery("INSERT INTO vtiger_eworkstatistics(
                            userid,
                            edate,
                            telnumber,
                            total_telnumber,
                            tel_connect_rate,
                            telduration,
                            addacounts,
                            transferaccount,
                            highseaaccount,
                            billvisits,
                            numbervisitors,
                            accompanyingvisits,
                            nactualvisitors,
                            signaccount,
                            amountpaid,
                            verifynum,
                            newvisitingnum,
                            commentaccountnum,
                            followupaccountnum,
                            enterednum,
                            department
                            )
                            SELECT 
                            stemp.userid,
                            edate,
                            sum(telnumber),
                            sum(total_telnumber),
                            sum(telnumber)/sum(total_telnumber),
                            sum(telduration),
                            sum(addacounts),
                            sum(transferaccount),
                            sum(highseaaccount),
                            sum(billvisits),
                            sum(numbervisitors),
                            sum(accompanyingvisits),
                            sum(numbervisitors+accompanyingvisits),
                            sum(signaccount),
                            sum(amountpaid),
                            sum(verifynum),
                            sum(newvisitingnum),
                            sum(commentaccountnum),
                            sum(followupaccountnum),
                            if(user_entered IS NULL,13,TIMESTAMPDIFF(MONTH,if(day(user_entered)>15,DATE_ADD( DATE_ADD(user_entered,interval -day(user_entered)+1 day), interval +1 month),user_entered),edate)),
                            SUBSTRING_INDEX(vtiger_departments.parentdepartment,'::' ,- 2)
                            from (
							SELECT useid AS userid,
                            vtiger_telstatistics.telnumberdate AS edate,
                            telnumber,
                            total_telnumber,
                            telnumber/total_telnumber,
                            telduration,
                            0 AS addacounts,
                            0 AS transferaccount,
                            0 AS highseaaccount,
                            0 AS billvisits,
                            0 AS numbervisitors,
                            0 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            0 AS signaccount,
                            0 AS amountpaid,
                            0 AS verifynum,
                            0 AS newvisitingnum,
                            0 AS commentaccountnum,
                            0 AS followupaccountnum
                             FROM vtiger_telstatistics 
                             where vtiger_telstatistics.telnumberdate>'{$lastThreeYear}'
                            UNION ALL 
                            
                            SELECT userid,
                             edate,
                            0 AS telnumber,
                            0 AS total_telnumber,
                            0 AS tel_connect_rate,
                            0 AS telduration,
                            count(1) AS addacounts,
                            0 AS transferaccount,
                            0 AS highseaaccount,
                            0 AS billvisits,
                            0 AS numbervisitors,
                            0 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            0 AS signaccount,
                            0 AS amountpaid ,
                            0 AS verifynum,
                            0 AS newvisitingnum,
                            0 AS commentaccountnum,
                            0 AS followupaccountnum
                            FROM vtiger_addacounts  
                            GROUP BY userid,edate
                            UNION ALL 
                            
                            SELECT userid,
                             edate,
                            0 AS telnumber,
                            0 AS total_telnumber,
                            0 AS tel_connect_rate,
                            0 AS telduration,
                            0 AS addacounts,
                            count(1) AS transferaccount,
                            0 AS highseaaccount,
                            0 AS billvisits,
                            0 AS numbervisitors,
                            0 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            0 AS signaccount,
                            0 AS amountpaid,
                            0 AS verifynum,
                            0 AS newvisitingnum,
                            0 AS commentaccountnum,
                            0 AS followupaccountnum
                             FROM vtiger_transferaccount 
                             GROUP BY userid,edate
                            UNION ALL
                            
                            SELECT userid,
                             edate,
                            0 AS telnumber,
                            0 AS total_telnumber,
                            0 AS tel_connect_rate,
                            0 AS telduration,                            
                            0 AS addacounts,
                            0 AS transferaccount,
                            count(1) AS highseaaccount,
                            0 AS billvisits,
                            0 AS numbervisitors,
                            0 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            0 AS signaccount,
                            0 AS amountpaid,
                            0 AS verifynum,
                            0 AS newvisitingnum,
                            0 AS commentaccountnum,
                            0 AS followupaccountnum
                             FROM vtiger_highseaaccount GROUP BY userid,edate
                            UNION ALL
                            SELECT extractid AS userid,
                            left(vtiger_crmentity.createdtime,10) AS edate,
                            0 AS telnumber,
                            0 AS total_telnumber,                            
                            0 AS tel_connect_rate,
                            0 AS telduration,
                            0 AS addacounts,
                            0 AS transferaccount,
                            0 AS highseaaccount,
                            sum(1) AS billvisits,
                            0 AS numbervisitors,
                            0 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            0 AS signaccount,
                            0 AS amountpaid,
                            0 AS verifynum,
                            0 AS newvisitingnum,
                            0 AS commentaccountnum,
                            0 AS followupaccountnum
                            FROM vtiger_visitingorder LEFT JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid=vtiger_crmentity.crmid 
                            WHERE deleted=0 AND extractid>0 AND related_to>0 AND vtiger_crmentity.createdtime>'{$lastThreeYear}' GROUP BY extractid,left(vtiger_crmentity.createdtime,10)
                            UNION ALL
                            SELECT extractid AS userid,
                            left(startdate,10) AS edate,
                            0 AS telnumber,
                            0 AS total_telnumber,
                            0 AS tel_connect_rate,
                            0 AS telduration,
                            0 AS addacounts,
                            0 AS transferaccount,
                            0 AS highseaaccount,
                            0 AS billvisits,
                            sum(if((accompany IS NULL OR accompany=''),1,0.5)) AS numbervisitors,
                            0 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            0 AS signaccount,
                            0 AS amountpaid,
                            0 AS verifynum,
                            0 AS newvisitingnum,
                            0 AS commentaccountnum,
                            0 AS followupaccountnum
                            FROM vtiger_visitingorder LEFT JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid=vtiger_crmentity.crmid 
                            WHERE deleted=0 AND extractid>0 AND related_to>0 AND modulestatus='c_complete'  AND startdate>'{$lastThreeYear}' GROUP BY extractid,left(startdate,10)
                            UNION ALL
                            SELECT userid,
                            edate,
                            0 AS telnumber,
                            0 AS total_telnumber,
                            0 AS tel_connect_rate,
                            0 AS telduration,
                            0 AS addacounts,
                            0 AS transferaccount,
                            0 AS highseaaccount,
                            0 AS billvisits,
                            0 AS numbervisitors,
                            sum(1)/2 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            0 AS signaccount,
                            0 AS amountpaid,
                            0 AS verifynum,
                            0 AS newvisitingnum,
                            0 AS commentaccountnum,
                            0 AS followupaccountnum
                            FROM vtiger_accompanyingvisits GROUP BY userid,edate
                            UNION ALL
                            
                            
                            SELECT 
                            userid,
                            edate,
                            0 AS telnumber,
                            0 AS total_telnumber,
                            0 AS tel_connect_rate,
                            0 AS telduration,
                            0 AS addacounts,
                            0 AS transferaccount,
                            0 AS highseaaccount,
                            0 AS billvisits,
                            0 AS numbervisitors,
                            0 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            sum(if(scalling=100,1,0.5)) AS signaccount,
                            0 AS amountpaid,
                            0 AS verifynum,
                            0 AS newvisitingnum,
                            0 AS commentaccountnum,
                            0 AS followupaccountnum
                            FROM 
                            vtiger_signaccount
                            GROUP BY userid,edate
                            
                            UNION ALL
                            SELECT 
                            receivedpaymentownid AS userid,
                            left(vtiger_receivedpayments.reality_date,10) AS edate,
                            0 AS telnumber,
                            0 AS total_telnumber,
                            0 AS tel_connect_rate,
                            0 AS telduration,
                            0 AS addacounts,
                            0 AS transferaccount,
                            0 AS highseaaccount,
                            0 AS billvisits,
                            0 AS numbervisitors,
                            0 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            0 AS signaccount,
                            sum(businessunit) AS amountpaid ,
                            0 AS verifynum,
                            0 AS newvisitingnum,
                            0 AS commentaccountnum,
                            0 AS followupaccountnum
                            FROM vtiger_achievementallot 
                            LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid
							WHERE vtiger_achievementallot.receivedpaymentownid>0 AND  vtiger_receivedpayments.reality_date >'{$lastThreeYear}'
							GROUP BY vtiger_achievementallot.receivedpaymentownid,vtiger_receivedpayments.reality_date
                            
                             
                            UNION ALL
                            SELECT 
                            userid,
                            edate,
                            0 AS telnumber,
                            0 AS total_telnumber,
                            0 AS tel_connect_rate,
                            0 AS telduration,
                            0 AS addacounts,
                            0 AS transferaccount,
                            0 AS highseaaccount,
                            0 AS billvisits,
                            0 AS numbervisitors,
                            0 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            0 AS signaccount,
                            0 AS amountpaid,
                            count(*) AS verifynum,
                            0 AS newvisitingnum,
                            0 AS commentaccountnum,
                            0 AS followupaccountnum
                            FROM 
                            vtiger_verifynum
                            GROUP BY userid,edate
                            
                                                        
                            UNION ALL
                            SELECT 
                            userid,
                            edate,
                            0 AS telnumber,
                            0 AS total_telnumber,
                            0 AS tel_connect_rate,
                            0 AS telduration,
                            0 AS addacounts,
                            0 AS transferaccount,
                            0 AS highseaaccount,
                            0 AS billvisits,
                            0 AS numbervisitors,
                            0 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            0 AS signaccount,
                            0 AS amountpaid,
                            0 AS verifynum,
                            count(*) AS newvisitingnum,
                            0 AS commentaccountnum,
                            0 AS followupaccountnum
                            FROM 
                            vtiger_newvisitingnum
                            GROUP BY userid,edate
                            
                                                        
                            UNION ALL
                            SELECT 
                            userid,
                            edate,
                            0 AS telnumber,
                            0 AS total_telnumber,
                            0 AS tel_connect_rate,
                            0 AS telduration,
                            0 AS addacounts,
                            0 AS transferaccount,
                            0 AS highseaaccount,
                            0 AS billvisits,
                            0 AS numbervisitors,
                            0 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            0 AS signaccount,
                            0 AS amountpaid,
                            0 AS verifynum,
                            0 AS newvisitingnum,
                            count(*) AS commentaccountnum,
                            0 AS followupaccountnum
                            FROM 
                            vtiger_commentaccountnum
                            GROUP BY userid,edate
                            
                            
                                                       
                            UNION ALL
                            SELECT 
                            userid,
                            edate,
                            0 AS telnumber,
                            0 AS total_telnumber,
                            0 AS tel_connect_rate,
                            0 AS telduration,
                            0 AS addacounts,
                            0 AS transferaccount,
                            0 AS highseaaccount,
                            0 AS billvisits,
                            0 AS numbervisitors,
                            0 AS accompanyingvisits,
                            0 AS nactualvisitors,
                            0 AS signaccount,
                            0 AS amountpaid,
                            0 AS verifynum,
                            0 AS newvisitingnum,
                            0 AS commentaccountnum,
                            count(*) AS followupaccountnum
                            FROM 
                            vtiger_followupaccountnum
                            GROUP BY userid,edate
                            ) as stemp 
                            LEFT JOIN vtiger_users ON vtiger_users.id=stemp.userid 
                            LEFT JOIN vtiger_user2department ON vtiger_user2department.userid = vtiger_users.id
                            LEFT JOIN vtiger_departments ON vtiger_departments.departmentid = vtiger_user2department.departmentid
                            GROUP BY userid,edate", array());
/***END总表**/

echo '插入完成 '.time().' 总耗时:'.(time()-$insertStartTime).'秒 插入耗时: '.(time()-$preparedTime).' 秒';
$insertDuration = time()-$insertStartTime;
timingLog('插入完成 '.date("Y-m-d H:i:s",time()).' 插入总耗时:'.(time()-$insertStartTime).'秒 插入耗时: '.(time()-$preparedTime).' 秒');

$flag = true;
$limit = 10000;

$startTime = time();
echo '开始修改状态 '.$startTime.' \r\n';
$reached=array();
$noReached=array();
$noAim=array();
$maxeworkstatisticsid = 0;
while ($flag){
    $cycleStartTime = time();
    $sql = "select a.eworkstatisticsid,a.edate,a.telnumber,a.telduration,a.addacounts,a.transferaccount,a.highseaaccount,a.billvisits,a.numbervisitors,a.nactualvisitors,a.amountpaid,b.user_entered,c.departmentid from vtiger_eworkstatistics a left join vtiger_users b on a.userid=b.id left join vtiger_user2department c on b.id=c.userid where eworkstatisticsid>".$maxeworkstatisticsid." limit 0,".$limit;
    $result = $db->pquery($sql,array());
    $j=0;
    while ($row = $db->fetchByAssoc($result)){
        $j++;
        $entered=explode('-',$row['user_entered']);
        if($entered[2]>15){
            $entered[1]=$entered[1]+1;
            if($entered[1]<13){
                $enteredday=$entered[0].'-'.$entered[1].'-01';
            }else{
                $enteredday=($entered[0]+1).'-01-01';
            }
        }else{
            $enteredday=$entered[0].'-'.$entered[1].'-01';;
        }
        $telStatisticsChangeAjax = new TelStatistics_ChangeAjax_Action();
        $currentDiffMonth=$telStatisticsChangeAjax->getMonthNum($enteredday,$row['edate']);
        $is_pass = TelStatistics_Record_Model::isReachStandard($indicatorsetting,$specialoperation,$row['departmentid'], $row, $currentDiffMonth);
        switch ($is_pass){
            case '达标':
                $reached[] = $row['eworkstatisticsid'];
                break;
            case "不达标":
                $noReached[] = $row['eworkstatisticsid'];
                break;
            default:
                $noAim[] = $row['eworkstatisticsid'];
                break;
        }
        $maxeworkstatisticsid = $row['eworkstatisticsid'];
    }
    if($j<10000){
        $flag=false;
    }

    if(count($reached)){
        $db->pquery( 'update vtiger_eworkstatistics set is_pass ="达标" where eworkstatisticsid in ('.implode(',',$reached).')',array());
        $reached=array();
    }
    if(count($noReached)){
        $db->pquery( 'update vtiger_eworkstatistics set is_pass ="不达标" where eworkstatisticsid in ('.implode(',',$noReached).')',array());
        $noReached=array();
    }
    if(count($noAim)){
        $db->pquery( 'update vtiger_eworkstatistics set is_pass ="未知" where eworkstatisticsid in ('.implode(',',$noAim).')',array());
        $noAim=array();
    }
    echo "执行到".$maxeworkstatisticsid.',耗费时间:'.(time()-$cycleStartTime).' 秒\r\n';
    timingLog("执行到".$maxeworkstatisticsid.',耗费时间:'.(time()-$cycleStartTime).' 秒');
}


timingLog('执行结束 '.date("Y-m-d H:i:s",time()).'插入总耗时:'.$insertDuration.' 统计是否达标耗时:'.(time()-$startTime).' 秒'.' 本次执行总耗时:'.(time()-$preparedTime));
timingLog('--------------------------------统计日期:'.date("Y-m-d").' end------------------------------------------');

function timingLog($str){
    global $root_directory;
    $dir	= $root_directory.'/logs/statistics/' . date("Y") ;
    if(!is_dir($dir)) {
        mkdir($dir,0755,true);
    }
    file_put_contents($dir.'/'.date("m").".log",$str."\r\n",FILE_APPEND);
}





