<?php
echo date('Y-m',strtotime('-5 months',strtotime(date('2021-05-01')))).'-01';
exit;


global $adb, $log;
$ids=$adb->pquery('update vtiger_account set protectday=protectday-1 where accountcategory<2 and protected=0 and protectday>0', array());
echo $adb->num_rows($ids);

//新零售7天未跟进掉入公海
$nowtime = date('Y-m-d');
$adb->pquery("update vtiger_account set accountcategory=2  where DATEDIFF({$nowtime},DATE_FORMAT(from_unixtime(mtime),'%Y-%m-%d'))>7 and accountcategory<2 and protected=0 and follow=1", array());


?>