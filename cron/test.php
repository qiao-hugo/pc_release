<?php
/*
file_put_contents("test-account-rank-log",date('Y-m-d H:i:s'));

$mysql = mysqli_connect('192.168.1.3','crmuser','crmdbpasswd123','vtigercrm600new',3306);
$mysql->query('update vtiger_account set protectday=protectday-1 where accountcategory<2 and protected=0 and protectday>0');
$mysql->query('update vtiger_account set accountcategory=2  where accountcategory<2 and protected=0 and protectday=0');
$now = strtotime('-30');
$mysql->query("update vtiger_account set accountrank='chan_notv' WHERE protected=0 AND accountrank='forp_notv' AND visitingorderlastfollowtime<{$now}");
$mysql->close();
*/


$file = '/data/httpd/vtigerCRM/cron/test.txt';
file_put_contents($file,date('Y-m-d H:i:s'));
echo date('Y-m-d H:i:s');
