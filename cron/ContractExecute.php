<?php
$dir=trim(__DIR__,DIRECTORY_SEPARATOR);
$dir=trim(__DIR__,'cron');
ini_set("include_path", $dir);
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");
//error_reporting(0);


include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';


vglobal('default_language', $default_language);
$currentLanguage = 'zh_cn';
vglobal('current_language', $currentLanguage);
global $adb;
//小SaaS 合同应收表
/*$nowDate= date('Y-m-d',(time()-24*60*60));
$nextDate= date('Y-m-d');
$sql = "insert into vtiger_contract_receivable(`contractid`,`accountid`,`contract_no`,`bussinesstype`,`productid`,`signid`,
                                                 `isautoclose`,`contracttotal`,`contractreceivableamount`,`contractreceivablebalance`,
                                                 `contractinvoiceamount`,`contractpaidamount`,`collectionstatus`,`signdempart`,`createdate`)
        select
            b.servicecontractsid AS contractid,
            b.sc_related_to AS accountid,
            b.contract_no,
            e.bussinesstype,
            b.productid,
            b.signid,
            b.isautoclose,
            ifnull(b.total,0) as contracttotal,
            ifnull((select sum(receiveableamount) from vtiger_contracts_execution_detail where vtiger_contracts_execution_detail.contractid=a.contractid),0) as contractreceivableamount,
            ifnull((select sum(contractreceivable) from vtiger_contracts_execution_detail where vtiger_contracts_execution_detail.contractid=a.contractid),0) as contractreceivablebalance,
            ifnull((select sum(actualtotal) from vtiger_newinvoice where vtiger_newinvoice.contractid=a.contractid and vtiger_newinvoice.modulestatus='c_complete'),0) as contractinvoiceamount,
            ifnull((select sum(unit_price) from vtiger_receivedpayments where relatetoid=b.servicecontractsid and receivedstatus='normal' and deleted=0),0) as contractpaidamount,
         if((select count(1) from vtiger_contracts_execution_detail where vtiger_contracts_execution_detail.contractid=d.contractid and vtiger_contracts_execution_detail.receiverabledate<CURRENT_DATE and vtiger_contracts_execution_detail.collection='overdue')>0,'hasoverdue','normal') as status,
          b.signdempart,
          '".$nowDate."'
          from vtiger_activationcode a
        left join vtiger_account c on c.accountid = a.customerid
        left join vtiger_servicecontracts b on a.contractid = b.servicecontractsid
        left join vtiger_contracts_execution_detail d on d.contractid=a.contractid
        left join vtiger_contract_type e on e.contract_type=b.contract_type
        where b.servicecontractsid not in (select distinct contractid from vtiger_contract_receivable) and a.startdate>'".$nowDate."' and a.startdate<'".$nextDate."' and a.status in (0,1) and a.iscollegeedition= 0
        and a.onoffline='offline' and b.signaturetype='papercontract' and b.modulestatus!='c_cancel' group by servicecontractsid";

$adb->pquery($sql,array());
echo '小SaaS统计完成<br>';*/

$today = date('Y-m-d');
echo '开始插入合同应收数据<br>';
$sql="INSERT INTO vtiger_contract_receivable(
	`contractid`,
	`accountid`,
	`contract_no`,
	`bussinesstype`,
	`productid`,
	`signid`,
	`isautoclose`,
	`contracttotal`,
	`contractreceivableamount`,
	`contractreceivablebalance`,
	`contractinvoiceamount`,
	`contractpaidamount`,
	`collectionstatus`,
	`signdempart`,
	`createdate`)
SELECT
servicecontracts.servicecontractsid AS contractid,
servicecontracts.sc_related_to AS accountid,
servicecontracts.contract_no,
ctype.bussinesstype,
servicecontracts.productid,
servicecontracts.signid,
servicecontracts.isautoclose,
IFNULL(servicecontracts.total, 0) AS contracttotal,
IFNULL(contracts_execution.receiveableamount, IFNULL(servicecontracts.total, 0)) AS receiveableamount,
IFNULL(contracts_execution.contractreceivable, 0) AS contractreceivable,
IFNULL(newinvoice.actualtotal, 0) AS actualtotal,
IFNULL(receivedpayments.unit_price, 0) AS unit_price,
IFNULL(contracts_execution.collectionstatus, 'normal') AS collectionstatus,
servicecontracts.signdempart,
'{$today}'
FROM vtiger_servicecontracts servicecontracts
LEFT JOIN vtiger_contract_type ctype ON ctype.contract_type = servicecontracts.contract_type
LEFT JOIN (SELECT sum(receiveableamount) AS receiveableamount, sum(contractreceivable) AS contractreceivable,
IF(sum(IF(receiverabledate < CURRENT_DATE AND collection = 'overdue', 1, 0)) > 0, 'hasoverdue', 'normal') AS collectionstatus,
contractid FROM vtiger_contracts_execution_detail GROUP BY contractid) contracts_execution ON contracts_execution.contractid = servicecontracts.servicecontractsid
LEFT JOIN (SELECT sum(unit_price) AS unit_price, relatetoid FROM vtiger_receivedpayments WHERE relatetoid > 0 AND ismatchdepart = 1 GROUP BY relatetoid) receivedpayments ON receivedpayments.relatetoid = servicecontracts.servicecontractsid
LEFT JOIN (SELECT sum(actualtotal) AS actualtotal, contractid FROM vtiger_newinvoice WHERE contractid>0 AND vtiger_newinvoice.modulestatus = 'c_complete' GROUP BY contractid) newinvoice ON newinvoice.contractid = servicecontracts.servicecontractsid
WHERE servicecontracts.servicecontractsid NOT IN(SELECT DISTINCT contractid FROM vtiger_contract_receivable)
AND servicecontracts.sideagreement=0
AND servicecontracts.signaturetype='papercontract'
AND servicecontracts.sc_related_to>0
AND servicecontracts.modulestatus IN('c_complete', 'c_history', 'b_actioning')
AND (contracts_execution.contractid IS NOT NULL OR receivedpayments.relatetoid IS NOT NULL)";
$adb->pquery($sql,[]);
echo '插入合同应收数据完成<br>';

echo '开始更新合同应收金额<br>';
$sql="UPDATE vtiger_contract_receivable a,
 (SELECT * FROM (SELECT contract_receivable.contract_no, contract_receivable.contracttotal, contract_receivable.contractid, contractreceivableamount, IFNULL(receiveableamount, 0) AS receiveableamount FROM vtiger_contract_receivable contract_receivable
 LEFT JOIN (SELECT sum(receiveableamount) AS receiveableamount, contractid FROM vtiger_contracts_execution_detail GROUP BY contractid) contracts_execution 
ON contract_receivable.contractid = contracts_execution.contractid) receivable
WHERE contractreceivableamount != receiveableamount AND receiveableamount>0) b SET a.contractreceivableamount = b.receiveableamount WHERE a.contractid = b.contractid";
$adb->pquery($sql,[]);
echo '更新合同应收金额完成<br>';

echo '开始更新合同回款状态<br>';
$sql="UPDATE vtiger_contract_receivable a,
 (SELECT * FROM (SELECT contract_receivable.collectionstatus, IFNULL(contracts_execution.status, 'normal') AS status, contract_receivable.contractid FROM vtiger_contract_receivable contract_receivable
LEFT JOIN (SELECT IF(SUM(IF(receiverabledate < CURRENT_DATE AND collection = 'overdue', 1, 0)) > 0, 'hasoverdue', 'normal') AS status, contractid FROM vtiger_contracts_execution_detail GROUP BY contractid) contracts_execution 
ON contract_receivable.contractid = contracts_execution.contractid) receivable
WHERE collectionstatus != status) b SET a.collectionstatus = b.status WHERE a.contractid = b.contractid";
$adb->pquery($sql,[]);
echo '更新合同回款状态完成<br>';

echo '开始更新合同已回款金额<br>';
$sql="UPDATE vtiger_contract_receivable a,
 (SELECT * FROM (SELECT contractid, contractpaidamount, IFNULL(unit_price, 0) AS unit_price FROM vtiger_contract_receivable contract_receivable
 LEFT JOIN (SELECT sum(unit_price)AS unit_price, relatetoid FROM vtiger_receivedpayments WHERE relatetoid>0 AND receivedstatus='normal' AND deleted=0 GROUP BY relatetoid) receivedpayments 
ON contract_receivable.contractid = receivedpayments.relatetoid) receivable
WHERE contractpaidamount != unit_price) b SET a.contractpaidamount = b.unit_price, a.contractreceivablebalance = a.contractreceivableamount-b.unit_price WHERE a.contractid = b.contractid";
$adb->pquery($sql,[]);
echo '更新合同已回款金额完成<br>';

echo '开始更新合同应收余额<br>';
$sql="UPDATE vtiger_contract_receivable SET contractreceivablebalance = contractreceivableamount-contractpaidamount
WHERE contractreceivablebalance != contractreceivableamount-contractpaidamount";
$adb->pquery($sql,[]);
echo '更新合同应收余额完成<br>';

echo '开始更新合同开票金额<br>';
$sql="UPDATE vtiger_contract_receivable a,
 (SELECT * FROM (SELECT contract_receivable.contractid, IFNULL(contract_receivable.contractinvoiceamount, 0) AS contractinvoiceamount, IFNULL(newinvoice.actualtotal, 0) AS actualtotal FROM vtiger_contract_receivable contract_receivable
 LEFT JOIN (SELECT sum(actualtotal) AS actualtotal, contractid FROM vtiger_newinvoice WHERE contractid>0 AND vtiger_newinvoice.modulestatus = 'c_complete' GROUP BY contractid) newinvoice 
ON contract_receivable.contractid = newinvoice.contractid) receivable
WHERE contractinvoiceamount != actualtotal) b SET a.contractinvoiceamount = b.actualtotal WHERE a.contractid = b.contractid";
$adb->pquery($sql,[]);
echo '更新合同开票金额完成<br>';

$limit =0 ;
$step = 200;
$page = 0;
while (true) {
//处理应收余额
    $sql = "select * from vtiger_contracts_execution_detail where ischeck=0 and iscancel=0 order by contractid,stage asc limit ".$limit.",".$step;
    $result = $adb->pquery($sql, array());
    if(!$adb->num_rows($result)){
        break;
    }
    $datas = array();
    $contractreceivableamount = 0;
    while ($row = $adb->fetchByAssoc($result)) {
        $datas[$row['contractid']][$row['stage']]['contractreceivable'] = $row['contractreceivable'];
        $datas[$row['contractid']][$row['stage']]['receiveableamount'] = $row['receiveableamount'];

    }
    echo '<pre>';

    foreach ($datas as $key => $data) {
        $sql2 = "select ifnull(sum(unit_price),0) as totalpayments from vtiger_receivedpayments where relatetoid=? and receivedstatus='normal' and deleted=0";
        $result2 = $adb->pquery($sql2, array($key));
        $row = $adb->fetchByAssoc($result2, 0);
        if ($row['totalpayments'] <= 0) {
            echo '关联的回款为0' . "<br>";
            continue;
        }
        $res = $adb->pquery("select ifnull(sum(receiveableamount),0) as totalamount,ifnull(sum(accountingamount),0) as accountingamount from vtiger_contracts_execution_detail where  contractid=?", array($key));
        $row2 = $adb->fetchByAssoc($res, 0);

        $totalpayments = $row['totalpayments'] - $row2['accountingamount'];
        echo '去掉已匹配的回款，剩余' . $totalpayments . '<br/>';
        ksort($data);
        $sql3 = "update vtiger_contracts_execution_detail set contractreceivable=?,ischeck=?,accountingamount=? where contractid=? and stage=?";

        foreach ($data as $key2 => $value) {
            if ($totalpayments <= 0) {
                break;
            }
            echo '剩余待减回款' . $totalpayments . ' 应回款.' . $value['contractreceivable'] . '<br>';
            if ($totalpayments >= $value['contractreceivable']) {
                $adb->pquery($sql3, array(0, 1, $value['receiveableamount'], $key, $key2));
                $totalpayments -= $value['contractreceivable'];
            } else {
                $adb->pquery($sql3, array(($value['contractreceivable'] - $totalpayments), 0, $totalpayments, $key, $key2));
                $totalpayments = 0;
            }
        }

        //$res2 = $adb->pquery("select contractreceivableamount from vtiger_contract_receivable where contractid = ?", array($key));
        //$contractreceivableamount = $adb->fetchByAssoc($res2);
        //处理合同应收表中数据
        //$sql4 = "update vtiger_contract_receivable set contractpaidamount=?,contractreceivablebalance=? where contractid = ?";
        //$adb->pquery($sql4, array($row['totalpayments'], ($contractreceivableamount['contractreceivableamount'] - $row['totalpayments']), $key));
        //echo '修改合同应手表中的数据,合同id：' . $key . ' contractreceivableamount=' . $row['totalpayments'] . ' contractreceivablebalance=' . ($contractreceivableamount['contractreceivableamount'] - $row['totalpayments']);

    }
    $page++;
    $limit = $page*$step;
}
//判定是否逾期
//生成合同应收阶段
$limit1 =0 ;
$step1 = 500;
$page1 = 0;
while (true) {
    $sql = "select * from vtiger_contracts_execution_detail where receiverabledate<CURRENT_DATE and collection!='overduereceived' and receiverabledate is not null and collection!='overduereceived' and iscancel=0  limit ".$limit1.",".$step1;
    $result = $adb->pquery($sql);
    if ($adb->num_rows($result)) {
        //合同执行详情表中的逾期时间
        while ($row = $adb->fetchByAssoc($result)) {
            $sql2 = 'update vtiger_contracts_execution_detail set ';
            $Date_1 = date("Y-m-d");
            $Date_2 = $row['receiverabledate'];
            $d1 = strtotime($Date_1);
            $d2 = strtotime($Date_2);
            $Days = empty($Date_2) ? 0 : round(($d1 - $d2) / 3600 / 24);
            $sql2 .= ' overduedays=?,collection=? where executiondetailid=?';
            $collection = 'overdue';
            if ($row['contractreceivable'] == 0) {
                $collection = 'overduereceived';
            }
            $adb->pquery($sql2, array($Days, $collection, $row['executiondetailid']));
        }
        $page1++;
        $limit1 = $page1*$step1;
    }else{
        break;
    }
}


//处理合同应收表数据
/*$max=1000;
$j=0;
$page2 =0;
while (true){
    $sql = "SELECT
                        vtiger_contracts_execution_detail.contractid,
                    IF
                        (
                        (
                    SELECT
                        count( 1 )
                    FROM
                        vtiger_contracts_execution_detail
                    WHERE
                        vtiger_contracts_execution_detail.receiverabledate < CURRENT_DATE AND vtiger_contracts_execution_detail.collection = 'overdue' and vtiger_contract_receivable.contractid=vtiger_contracts_execution_detail.contractid and vtiger_contracts_execution_detail.iscancel=0 ) > 0,
                        'hasoverdue',
                        'normal'
                        ) AS status
                    FROM
                        vtiger_contract_receivable
                        LEFT JOIN vtiger_contracts_execution_detail ON vtiger_contracts_execution_detail.contractid = vtiger_contract_receivable.contractid
                        where vtiger_contracts_execution_detail.iscancel=0
                        group by contractid
                        ORDER BY
                        contractid limit ";
    $sql .= $j.','.$max;
    $result = $adb->pquery($sql,array());
    if(!$adb->num_rows($result)){
        break;
    }
    while ($row = $adb->fetchByAssoc($result)){
        $invoicedata = $adb->pquery( "select sum(actualtotal) as actualtotal from vtiger_newinvoice where vtiger_newinvoice.contractid=? and vtiger_newinvoice.modulestatus='c_complete'",array($row['contractid']));
        $invoice = $adb->fetchByAssoc($invoicedata,0);
        $actualtotal = $invoice['actualtotal'];
        $adb->pquery( "update vtiger_contract_receivable set contractinvoiceamount=?,collectionstatus=? where contractid = ?",array($actualtotal,$row['status'],$row['contractid']));
    }
    $page2++;
    $j = $page2*$max;
}

echo '同应收表数据<br>';
*/

$adb->pquery('truncate table vtiger_account_receivable');
//客户应收
$sql2 = "insert into vtiger_account_receivable(`accountid`,`accountname`,`contractnum`,`bussinesstypenum`,`contracttotal`,`contractreceivableamount`,
                                        `contractreceivablebalance`,`contractinvoiceamount`,`contractpaidamount`,`contractoverduebalance`,`receivestatus`)
          select 
            b.accountid,
            b.accountname,
            ifnull(count(distinct c.contract_no),0) as contractnum,
            ifnull(count(distinct d.bussinesstype),0) as bussinesstypenum,
            ifnull(sum(c.total),0) as contracttotal,
            ifnull(sum(a.contractreceivableamount),0) as contractreceivableamount,
            ifnull(sum(a.contractreceivablebalance),0) as contractreceivablebalance,
            ifnull(sum(a.contractinvoiceamount),0) as contractinvoiceamount,
            ifnull(sum(a.contractpaidamount),0) as contractpaidamount,
            ifnull((select sum(contractreceivable) from vtiger_contracts_execution_detail where vtiger_contracts_execution_detail.accountid=a.accountid and vtiger_contracts_execution_detail.receiverabledate<CURRENT_DATE and vtiger_contracts_execution_detail.collection='overdue'),0) as contractoverduebalance,
            if((select count(1) from vtiger_contracts_execution_detail where vtiger_contracts_execution_detail.accountid=a.accountid and vtiger_contracts_execution_detail.receiverabledate<CURRENT_DATE  and vtiger_contracts_execution_detail.collection='overdue' and vtiger_contracts_execution_detail.iscancel=0)>0,'hasoverdue','normal') as status
        from vtiger_contract_receivable a 
        left join vtiger_servicecontracts c on a.contractid=c.servicecontractsid
        left join vtiger_account b on c.sc_related_to = b.accountid
        left join vtiger_contract_type d on d.contract_type = c.contract_type
         where  c.modulestatus!='c_cancel' and a.iscancel=0 group by c.sc_related_to";
$adb->pquery($sql2,array());
echo '客户应收表统计完成<br>';



//清空逾期应收表
$adb->pquery("truncate table vtiger_receivable_overdue");

//逾期应收明细表
$sql4 = "insert into vtiger_receivable_overdue (`contract_no`,`bussinesstype`,`contracttotal`,`stage`,
                                       `stageshow`,`productid`,`signid`,`signdate`,`receiveableamount`,`contractreceivable`,
                                       `overduedays`,`executiondetailid`,`contractexecutionid`,`accountid`,`contractid`,
                                       `receiverabledate`,`collection`,`commentcontent`,`lastfollowtime`) 
        select 
           d.contract_no,
           e.bussinesstype,
           d.total as contracttotal,
           a.stage,
           a.stageshow,
           d.productid,
           d.signid,
           d.signdate,
           ifnull(a.receiveableamount,0),
           ifnull(a.contractreceivable,0),
           ifnull(a.overduedays,0),
           a.executiondetailid,
           b.contractexecutionid,
           d.sc_related_to AS accountid,
           a.contractid,
           a.receiverabledate,
           a.collection,
           a.commentcontent,
           a.lastfollowtime
        from vtiger_contracts_execution_detail a 
        left join vtiger_contracts_execution b on a.contractexecutionid =b.contractexecutionid
        left join vtiger_servicecontracts d on d.servicecontractsid=a.contractid
        left join vtiger_contract_type e on e.contract_type = d.contract_type
        where a.overduedays>0 and d.modulestatus!='c_cancel' and a.collection!='overduereceived' and a.iscancel=0 and a.receiverabledate !='' and a.receiverabledate is not null";
$adb->pquery($sql4,array());

echo '逾期表统计完成<br>';