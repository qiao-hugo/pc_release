<?php
$dir = dirname(__FILE__);
ini_set("include_path", $dir."/../");
require_once('include/utils/utils.php');
require_once('include/logging.php');

global $adb, $log;


// 销售周报
$sql = "UPDATE vtiger_salestargetdetail  s
            SET weekvisit = (
                SELECT
                    count(*)
                FROM
                    vtiger_visitingorder
                LEFT JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid = vtiger_crmentity.crmid
              LEFT JOIN vtiger_visitsign ON vtiger_visitsign.visitingorderid = vtiger_visitingorder.visitingorderid
                LEFT JOIN vtiger_salestarget ON vtiger_visitsign.userid = vtiger_salestarget.businessid
                WHERE s.salestargetid=vtiger_salestarget.salestargetid 
              AND str_to_date(vtiger_visitsign.signtime, '%Y-%m-%d') BETWEEN str_to_date(s.startdate, '%Y-%m-%d') AND str_to_date(s.enddate,'%Y-%m-%d')
            ),
            weekinvitation = (
                SELECT
                    count(*)
                FROM
                    vtiger_visitingorder
                LEFT JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid = vtiger_crmentity.crmid
                LEFT JOIN vtiger_salestarget ON vtiger_visitingorder.extractid = vtiger_salestarget.businessid
                WHERE s.salestargetid=vtiger_salestarget.salestargetid
                AND vtiger_visitingorder.modulestatus='c_complete'
                AND vtiger_crmentity.createdtime BETWEEN str_to_date(s.startdate,'%Y-%m-%d') AND str_to_date(s.enddate,'%Y-%m-%d')
            ),
            weekachievement = (
                SELECT SUM(vtiger_salesdailydaydeal.arrivalamount) 
              FROM vtiger_salesdaily_basic 
              LEFT JOIN vtiger_salesdailydaydeal ON vtiger_salesdailydaydeal.salesdailybasicid=vtiger_salesdaily_basic.salesdailybasicid
                LEFT JOIN vtiger_salestarget ON vtiger_salesdaily_basic.smownerid = vtiger_salestarget.businessid
                WHERE s.salestargetid=vtiger_salestarget.salestargetid
                AND str_to_date(vtiger_salesdaily_basic.createdtime, '%Y-%m-%d') BETWEEN str_to_date(s.startdate,'%Y-%m-%d') AND str_to_date(s.enddate,'%Y-%m-%d')
            ),
            weekinvitationrate = 
                CONCAT( FORMAT(FORMAT(weekinvitation/weekinvitationtarget, 2) * 100, 0), '%'),
            weekvisitrate = 
                CONCAT( FORMAT(FORMAT(weekvisit/weekvisittarget, 2) * 100, 0), '%'),
            weekachievementrate = 
                CONCAT( FORMAT(FORMAT(weekachievement/weekachievementtargt, 2) * 100, 0), '%')
            WHERE date_sub(curdate(),interval 1 day) BETWEEN str_to_date(s.startdate,'%Y-%m-%d') AND str_to_date(s.enddate,'%Y-%m-%d')";
$adb->pquery($sql, array());


$sql = "UPDATE vtiger_salestarget s set 
                invitationnum = (
                    select sum(vtiger_salestargetdetail.weekinvitation) from vtiger_salestargetdetail
                    where s.salestargetid=vtiger_salestargetdetail.salestargetid
                ),
                visitnum = (
                    select sum(vtiger_salestargetdetail.weekvisit) from vtiger_salestargetdetail
                    where s.salestargetid=vtiger_salestargetdetail.salestargetid
                ),
                achievementnum = (
                    select sum(vtiger_salestargetdetail.weekachievement) from vtiger_salestargetdetail
                    where s.salestargetid=vtiger_salestargetdetail.salestargetid
                ),
                invitationrate = 
                    CONCAT( FORMAT(FORMAT(invitationnum/invitationtarget, 2) * 100, 0), '%'),
                visitrate = 
                    CONCAT( FORMAT(FORMAT(visitnum/visittarget, 2) * 100, 0), '%'),
                achievementrate = 
                    CONCAT( FORMAT(FORMAT(achievementnum/achievementtargt, 2) * 100, 0), '%')


                WHERE date_sub(curdate(), interval 1 day) 
                BETWEEN str_to_date( CONCAT(s.`year`,'-', s.`month`,'-','01' ) ,'%Y-%m-%d') 
                AND date_add( str_to_date( CONCAT(s.`year`,'-', s.`month`,'-','01') ,'%Y-%m-%d'), interval 1 month)";
$adb->pquery($sql, array());



// 部门销售周报
$sql = "UPDATE vtiger_depasalestargetdetail  s
                SET weekvisit = (
                        SELECT
                                count(*)
                        FROM
                                vtiger_visitingorder
                        LEFT JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid = vtiger_crmentity.crmid
                        LEFT JOIN vtiger_visitsign ON vtiger_visitsign.visitingorderid = vtiger_visitingorder.visitingorderid
                        WHERE vtiger_visitsign.userid IN (SELECT vtiger_user2department.userid FROM vtiger_departments 
                            LEFT JOIN vtiger_user2department ON vtiger_user2department.departmentid=vtiger_departments.departmentid 
                            WHERE vtiger_departments.departmentname=s.department
                        )
                        AND str_to_date(vtiger_visitsign.signtime, '%Y-%m-%d') BETWEEN str_to_date(s.startdate, '%Y-%m-%d') AND str_to_date(s.enddate,'%Y-%m-%d')
                ),
                weekinvitation = (
                        SELECT
                                count(*)
                        FROM
                                vtiger_visitingorder
                        LEFT JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid = vtiger_crmentity.crmid
                        WHERE vtiger_visitingorder.extractid IN (SELECT vtiger_user2department.userid FROM vtiger_departments 
                                                                    LEFT JOIN vtiger_user2department ON vtiger_user2department.departmentid=vtiger_departments.departmentid 
                                                                    WHERE vtiger_departments.departmentname=s.department
                                                                )
                        AND vtiger_visitingorder.modulestatus='c_complete'
                        AND vtiger_crmentity.createdtime BETWEEN str_to_date(s.startdate,'%Y-%m-%d') AND str_to_date(s.enddate,'%Y-%m-%d')
                ),
                weekachievement = (
                        SELECT SUM(vtiger_salestargetdetail.weekachievement) 
                    FROM vtiger_salestargetdetail 
                    WHERE vtiger_salestargetdetail.businessid IN (SELECT vtiger_user2department.userid FROM vtiger_departments 
                                                                    LEFT JOIN vtiger_user2department ON vtiger_user2department.departmentid=vtiger_departments.departmentid 
                                                                    WHERE vtiger_departments.departmentname=s.department
                                                                )
                    AND vtiger_salestargetdetail.year=s.year AND vtiger_salestargetdetail.month=s.month
                ),
                weekinvitationrate = 
                        CONCAT( FORMAT(FORMAT(weekinvitation/weekinvitationtarget, 2) * 100,0), '%'),
                weekvisitrate = 
                        CONCAT( FORMAT(FORMAT(weekvisit/weekvisittarget, 2) * 100, 0), '%'),
                weekachievementrate = 
                        CONCAT( FORMAT(FORMAT(weekachievement/weekachievementtargt, 2) * 100,0), '%')
                WHERE date_sub(curdate(),interval 1 day) BETWEEN str_to_date(s.startdate,'%Y-%m-%d') AND str_to_date(s.enddate,'%Y-%m-%d')";

$adb->pquery($sql, array());
        $sql = "UPDATE vtiger_depasalestarget s set 
                invitationnum = (
                    select sum(vtiger_depasalestargetdetail.weekinvitation) from vtiger_depasalestargetdetail
                    where s.salestargetid=vtiger_depasalestargetdetail.salestargetid
                ),
                visitnum = (
                    select sum(vtiger_depasalestargetdetail.weekvisit) from vtiger_depasalestargetdetail
                    where s.salestargetid=vtiger_depasalestargetdetail.salestargetid
                ),
                achievementnum = (
                    select sum(vtiger_depasalestargetdetail.weekachievement) from vtiger_depasalestargetdetail
                    where s.salestargetid=vtiger_depasalestargetdetail.salestargetid
                ),
                invitationrate = 
                    CONCAT( FORMAT(FORMAT(invitationnum/invitationtarget, 2) * 100, 0), '%'),
                visitrate = 
                    CONCAT( FORMAT(FORMAT(visitnum/visittarget, 2) * 100, 0), '%'),
                achievementrate = 
                    CONCAT( FORMAT(FORMAT(achievementnum/achievementtargt, 2) * 100, 0), '%')


                WHERE date_sub(curdate(), interval 1 day) 
                BETWEEN str_to_date( CONCAT(s.`year`,'-', s.`month`,'-','01' ) ,'%Y-%m-%d') 
                AND date_add( str_to_date( CONCAT(s.`year`,'-', s.`month`,'-','01') ,'%Y-%m-%d'), interval 1 month)";
$adb->pquery($sql, array());

echo 'ok';
?>
