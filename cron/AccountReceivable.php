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

//处理以前的老数据
global $adb;
$nowDate = date("Y-m-d");
//$adb->pquery("CREATE TABLE `vtiger_contract_receivable_temp`  (
//  `contractreceivableid` int(19) NOT NULL AUTO_INCREMENT,
//  `contract_no` varchar(320) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '合同编号',
//  `accountid` int(19) NOT NULL COMMENT '客户id',
//  `contracttotal` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '总合同额',
//  `bussinesstype` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '业务类型',
//  `productid` int(11) NOT NULL COMMENT '产品类型',
//  `contractid` int(19) NOT NULL COMMENT '合同id',
//  `contractpaidamount` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '合计实收金额',
//  `contractinvoiceamount` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '合计开票金额',
//  `contractreceivableamount` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '合计应收金额',
//  `contractreceivablebalance` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '合计应收余额',
//  `signid` int(11) NOT NULL COMMENT '签订人',
//  `status` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '状态',
//  `signdempart` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '合同所属部门',
//  `isautoclose` int(9) NULL DEFAULT NULL COMMENT '非框架合同',
//  `collectionstatus` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '状态',
//  `createdate` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '创建时间',
//  `iscancel` tinyint(1) NULL DEFAULT 0 COMMENT '是否作废 0未作废，1作废',
//  `startdate` varchar(32) ,
//  `modulestatus` varchar(32),
//  PRIMARY KEY (`contractreceivableid`) USING BTREE
//) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic");
$adb->pquery("truncate table vtiger_contract_receivable");
$adb->pquery("delete from vtiger_contracts_execution_detail where collectiondescription='系统生成'");

$limit = 0;
$step = 300;
//$sql = "  insert into vtiger_contract_receivable_temp(`contractid`,`accountid`,`contract_no`,`bussinesstype`,`productid`,`signid`,
//                                                         `isautoclose`,`contracttotal`,`contractreceivableamount`,`contractreceivablebalance`,
//                                                         `contractinvoiceamount`,`contractpaidamount`,`collectionstatus`,`signdempart`,`startdate`,`modulestatus`)
//                 select
//                        a.contractid,a.customerid as accountid,b.contract_no,
//                       e.bussinesstype,
//                       b.productid,
//                       b.signid,
//                       b.isautoclose,
//                       ifnull(b.total,0) as contracttotal,
//                      ifnull(b.total,0) as contractreceivableamount,
//                      ifnull(b.total,0) as contractreceivablebalance,
//                       ifnull((select sum(actualtotal) from vtiger_newinvoice where vtiger_newinvoice.contractid=a.contractid and vtiger_newinvoice.modulestatus='c_complete'),0) as contractinvoiceamount,
//                      ifnull((select sum(unit_price) from vtiger_receivedpayments where vtiger_receivedpayments.relatetoid=b.servicecontractsid and vtiger_receivedpayments.ismatchdepart=1),0) as contractpaidamount,
//                 if((select count(1) from vtiger_contracts_execution_detail where vtiger_contracts_execution_detail.contractid=d.contractid and vtiger_contracts_execution_detail.receiverabledate<CURRENT_DATE and vtiger_contracts_execution_detail.collection='overdue')>0,'hasoverdue','normal') as status,
//                  b.signdempart,
//                  a.startdate,
//                  b.modulestatus
//                  from vtiger_activationcode a
//                left join vtiger_account c on c.accountid = a.customerid
//                left join vtiger_servicecontracts b on a.contractid = b.servicecontractsid
//                left join vtiger_contracts_execution_detail d on d.contractid=a.contractid
//                left join vtiger_contract_type e on e.contract_type=b.contract_type
//                where a.contractid!='' and a.contractid is not null and a.startdate is not null and a.startdate!='0000-00-00 00:00:00'
//                and a.onoffline='offline' and a.signaturetype='papercontract' and a.status in (0,1) and b.modulestatus!='c_cancel'
//                and a.startdate<'2020-08-01 23:59:59'
//                  and a.iscollegeedition= 0 and e.bussinesstype='smallsassdirect' group by contract_no ";
////插入临时表
//$adb->pquery($sql,array());

//从临时表中查出对应数据
$result = $adb->pquery("select * from vtiger_contract_receivable_temp");

$max = $adb->num_rows($result);
echo '总共'.$max.'条数据<br>';
if($max){
    $preSql1 = "insert into vtiger_contract_receivable(`contractid`,`accountid`,`contract_no`,`bussinesstype`,`productid`,`signid`,
                                                         `isautoclose`,`contracttotal`,`contractreceivableamount`,`contractreceivablebalance`,
                                                         `contractinvoiceamount`,`contractpaidamount`,`collectionstatus`,`signdempart`,`createdate`) values ";
    $preSql2 = "insert into vtiger_contracts_execution_detail(`stage`,`stageshow`,`receiveableamount`,`collectiondescription`,`receiverabledate`,
          `executestatus`,`stagetype`,`contractreceivable`,`collection`,`accountid`,`contractid`,`bussinesstype`,`executedate`,`executor`) values ";

    for($i=0;$i<$max;$i++){
        $row = $adb->fetchByAssoc($result,$i);
        $date = date("Y-m-d",strtotime($row['startdate']));
        $preSql1 .= "(".$row['contractid'].",".$row['accountid'].",'".$row['contract_no']."','".$row['bussinesstype']."',".($row['productid']?$row['productid']:0).",".$row['signid'].",".$row['isautoclose'].
            ",". $row['contracttotal'].",". $row['contractreceivableamount'].",".$row['contractreceivablebalance'].",". $row['contractinvoiceamount'].",". $row['contractpaidamount'].
            ",'". $row['status']."','". $row['signdempart']."','".date("Y-m-d")."'),";

        if($row['modulestatus']=='c_complete' && strtotime($date)<strtotime('2020-08-02')){
            $preSql2 .= "(1,'第1阶段',".$row['contractreceivableamount'].",'系统生成','".$date.
                "','c_executed','合同生成',".$row['contractreceivableamount'].",'normal',".$row['accountid'].",".$row['contractid'].",'".$row['bussinesstype']."','".$date."',6934),";
        }

        if((($i+1)%50)==0){
            $preSql2 = rtrim($preSql2,',');
            $preSql1 = rtrim($preSql1,',');
            //小sass进入合同应收表
            $adb->pquery($preSql1,array());
            //小sass进入合同应收阶段
            $adb->pquery($preSql2,array());

            $preSql1 = "insert into vtiger_contract_receivable(`contractid`,`accountid`,`contract_no`,`bussinesstype`,`productid`,`signid`,
                                                         `isautoclose`,`contracttotal`,`contractreceivableamount`,`contractreceivablebalance`,
                                                         `contractinvoiceamount`,`contractpaidamount`,`collectionstatus`,`signdempart`,`createdate`) values ";
            $preSql2 = "insert into vtiger_contracts_execution_detail(`stage`,`stageshow`,`receiveableamount`,`collectiondescription`,`receiverabledate`,
          `executestatus`,`stagetype`,`contractreceivable`,`collection`,`accountid`,`contractid`,`bussinesstype`,`executedate`,`executor`) values ";
        }
    }

    $preSql2 = rtrim($preSql2,',');
    $preSql1 = rtrim($preSql1,',');
//小sass进入合同应收表
    $adb->pquery($preSql1,array());
//小sass进入合同应收阶段
    $adb->pquery($preSql2,array());

}
echo '小sass数据插入完成<br>';
$adb->pquery("drop table vtiger_contract_receivable_temp");
echo '小sass数据临时表清空<br>';

$limit =0 ;
$step = 200;
echo '处理余额开始时间'.time().'<br>';
while (true){
    //处理应收余额
    $sql = "select * from vtiger_contracts_execution_detail where ischeck=0 and iscancel=0 order by contractid,stage asc limit ".$limit.",".$step;
    $result = $adb->pquery($sql,array());
    if(!$adb->num_rows($result)){
        break;
    }
    $datas = array();
    $contractreceivableamount = 0;
    while ($row = $adb->fetchByAssoc($result)){
        $datas[$row['contractid']][$row['stage']]['contractreceivable'] = $row['contractreceivable'];
        $datas[$row['contractid']][$row['stage']]['receiveableamount'] = $row['receiveableamount'];

    }

    foreach ($datas as $key=>$data){
        $sql2 = "select ifnull(sum(unit_price),0) as totalpayments from vtiger_receivedpayments where relatetoid=?";
        $result2 = $adb->pquery($sql2,array($key));
        $row = $adb->fetchByAssoc($result2,0);
        if($row['totalpayments']<=0){
            echo '关联的回款为0'."<br>";
            continue;
        }
        $res = $adb->pquery("select ifnull(sum(receiveableamount),0) as totalamount,ifnull(sum(accountingamount),0) as accountingamount from vtiger_contracts_execution_detail where  contractid=?",array($key));
        $row2 =  $adb->fetchByAssoc($res,0);

        $totalpayments = $row['totalpayments']-$row2['accountingamount'];
        echo '去掉已匹配的回款，剩余'.$totalpayments.'<br/>';
        if($totalpayments>0){
            ksort($data);
            $sql3 = "update vtiger_contracts_execution_detail set contractreceivable=?,ischeck=?,accountingamount=? where contractid=? and stage=?";

            foreach ($data as $key2=>$value){
                if($totalpayments<=0){
                    break;
                }
                echo '剩余待减回款'.$totalpayments.' 应回款.'.$value['contractreceivable'].'<br>';
                if($totalpayments>=$value['contractreceivable']){
                    $adb->pquery($sql3,array(0,1,$value['receiveableamount'],$key,$key2));
                    $totalpayments -= $value['contractreceivable'];
                }else{
                    $adb->pquery($sql3,array(($value['contractreceivable']-$totalpayments),0,$totalpayments,$key,$key2));
                    $totalpayments = 0;
                }
            }
            $res2 = $adb->pquery("select contractreceivableamount,contract_no from vtiger_contract_receivable where contractid = ?",array($key));
            $contractreceivableamount = $adb->fetchByAssoc($res2);
            //处理合同应收表中数据
            echo '处理的合同编号'.$contractreceivableamount['contract_no'];
            $sql4 = "update vtiger_contract_receivable set contractpaidamount=?,contractreceivablebalance=? where contractid = ?";
            $adb->pquery($sql4,array($row['totalpayments'],($contractreceivableamount['contractreceivableamount']-$row['totalpayments']),$key));
        }
    }
    $limit = ($limit+1)*$step;
}
echo '处理余额结束时间'.time().'<br>';

echo '处理是否超时开始'.time()."<br>";
//生成合同应收阶段
$limit1 =0 ;
$step1 = 500;
while (true){
    $sql = "select * from vtiger_contracts_execution_detail where receiverabledate<CURRENT_DATE and receiverabledate !='' and receiverabledate is not null and collection!='overduereceived' and iscancel=0 limit ".$limit1.",".$step1;
    $result = $adb->pquery($sql);
    if($adb->num_rows($result)){
        //合同执行详情表中的逾期时间
        while ($row= $adb->fetchByAssoc($result)){
            $sql2 = 'update vtiger_contracts_execution_detail set ';
            $Date_1 = date("Y-m-d");
            $Date_2 = $row['receiverabledate'];
            $d1 = strtotime($Date_1);
            $d2 = strtotime($Date_2);
            $Days = round(($d1-$d2)/3600/24);
            $sql2 .= ' overduedays=?,collection=? where executiondetailid=?';
            $collection = 'overdue';
            if($row['contractreceivable']==0){
                $collection = 'normal';
                $Days = 0;
            }
            $adb->pquery($sql2,array($Days,$collection,$row['executiondetailid']));
        }
        $limit1 = ($limit1+1)*$step1;
    }else{
        break;
    }
}
echo '处理是否超时结束'.time()."<br>";



echo '处理应收表收款状态开始'.time()."<br>";
//处理合同应收表数据
$max=1000;
$j=0;
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
    $j = ($j+1)*$max;
}
echo '处理应收表收款状态结束'.time()."<br>";

echo '同应收表数据<br>';

echo '处理客户应收表开始'.time()."<br>";
$adb->pquery('truncate table vtiger_account_receivable');
//客户应收
$sql2 = "
  insert into vtiger_account_receivable(`accountid`,`accountname`,`contractnum`,`bussinesstypenum`,`contracttotal`,`contractreceivableamount`,
                                        `contractreceivablebalance`,`contractinvoiceamount`,`contractpaidamount`,`contractoverduebalance`,`receivestatus`)
          select
            b.accountid,b.accountname,
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
        left join vtiger_account b on a.accountid = b.accountid
        left join vtiger_servicecontracts c on a.contractid=c.servicecontractsid
        left join vtiger_contract_type d on d.contract_type = c.contract_type
         where  c.modulestatus!='c_cancel' and a.iscancel=0 group by a.accountid";
$adb->pquery($sql2,array());
echo '处理客户应收表结束'.time()."<br>";
echo '客户应收表统计完成<br>';


echo '处理逾期应收表开始'.time()."<br>";
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
                   a.accountid,
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
echo '处理客户应收表结束'.time()."<br>";

echo '逾期表统计完成<br>';


