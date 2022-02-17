<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class AccountReceivable_List_View extends Vtiger_KList_View {
    function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    public function process(Vtiger_Request $request)
    {
        $strPublic = $request->get('public');
        if($strPublic=='export'){
            global $site_URL,$current_user;
            header('location:'.$site_URL.'temp/'.'客户运营应收总表'.date('Ymd').$current_user->id.'.csv');
            exit;
        }elseif ($strPublic=="all"){
            global $adb;

            //小SaaS 合同应收表
            $nowDate= date('Y-m-d');
            $nextDate= date('Y-m-d',(time()+24*60*60));
            //删掉当天的小SaaS的合同应收
            $adb->pquery("delete from vtiger_contract_receivable  where bussinesstype='smallsassdirect' and createdate='".$nowDate."'",array());
            $sql = "
                  insert into vtiger_contract_receivable(`contractid`,`accountid`,`contract_no`,`bussinesstype`,`productid`,`signid`,
                                                         `isautoclose`,`contracttotal`,`contractreceivableamount`,`contractreceivablebalance`,
                                                         `contractinvoiceamount`,`contractpaidamount`,`collectionstatus`,`signdempart`,`createdate`)
                select 
                        a.contractid,a.customerid as accountid,b.contract_no,
                       e.bussinesstype,
                       b.productid,
                       b.signid,
                       b.isautoclose,
                       ifnull(b.total,0) as contracttotal,
                      ifnull(sum(d.receiveableamount),0) as contractreceivableamount,
                      ifnull(sum(d.receiveableamount),0) as contractreceivablebalance,
                       ifnull((select sum(actualtotal) from vtiger_newinvoice where vtiger_newinvoice.contractid=a.contractid and vtiger_newinvoice.modulestatus='c_complete'),0) as contractinvoiceamount,
                      ifnull((select sum(unit_price) from vtiger_receivedpayments where vtiger_receivedpayments.relatetoid=b.servicecontractsid and vtiger_receivedpayments.ismatchdepart=1),0) as contractpaidamount,
                 if((select count(1) from vtiger_contracts_execution_detail where vtiger_contracts_execution_detail.contractid=d.contractid and vtiger_contracts_execution_detail.receiverabledate<CURRENT_DATE and vtiger_contracts_execution_detail.collection='overdue')>0,'hasoverdue','normal') as status,
                  b.signdempart,
                  '".$nowDate."'
                  from vtiger_activationcode a  
                left join vtiger_account c on c.accountid = a.customerid
                left join vtiger_servicecontracts b on a.contractid = b.servicecontractsid 
                left join vtiger_contracts_execution_detail d on d.contractid=a.contractid
                left join vtiger_contract_type e on e.contract_type=b.contract_type
                where a.contractid!='' and a.contractid is not null and a.startdate is not null and a.startdate>'".$nowDate."' and a.startdate<'".$nextDate."' and a.startdate!='0000-00-00 00:00:00'
                and a.onoffline='offline' and a.signaturetype='papercontract' and a.status in (0,1) and b.modulestatus!='c_cancel' and a.iscollegeedition= 0 and e.bussinesstype='smallsassdirect' group by contract_no";
            $adb->pquery($sql,array());
            echo '小SaaS统计完成<br>';


            $sql = "select * from vtiger_contracts_execution_detail where receiverabledate<CURRENT_DATE and collection!='overduereceived' and iscancel=0";
            $result = $adb->pquery($sql);
            if($adb->num_rows($result)){
                //合同执行详情表中的逾期时间
                while ($row= $adb->fetchByAssoc($result)){
                    $sql2 = 'update vtiger_contracts_execution_detail set ';
                    $Date_1 = date("Y-m-d");
                    $Date_2 = $row['receiverabledate'];
                    $d1 = strtotime($Date_1);
                    $d2 = strtotime($Date_2);
                    $Days = empty($Date_2)?0:round(($d1-$d2)/3600/24);
                    $sql2 .= ' overduedays=?,collection=? where executiondetailid=?';
                    $collection = 'overdue';
                    if($row['contractreceivable']==0){
                        $collection = 'overduereceived';
                    }
                    $adb->pquery($sql2,array($Days,$collection,$row['executiondetailid']));
                }
            }



            //处理应收余额
            $sql = "select * from vtiger_contracts_execution_detail where ischeck=0 and iscancel=0";
            $result = $adb->pquery($sql,array());
            $datas = array();
            $contractreceivableamount = 0;
            while ($row = $adb->fetchByAssoc($result)){
                $datas[$row['contractid']][$row['stage']]['contractreceivable'] = $row['contractreceivable'];
                $datas[$row['contractid']][$row['stage']]['receiveableamount'] = $row['receiveableamount'];

            }
            echo '<pre>';
            var_dump($datas);

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

                $res2 = $adb->pquery("select contractreceivableamount from vtiger_contract_receivable where contractid = ?",array($key));
                $contractreceivableamount = $adb->fetchByAssoc($res2);
                //处理合同应收表中数据
                $sql4 = "update vtiger_contract_receivable set contractpaidamount=?,contractreceivablebalance=? where contractid = ?";
                $adb->pquery($sql4,array($row['totalpayments'],($contractreceivableamount['contractreceivableamount']-$row['totalpayments']),$key));
                echo '修改合同应手表中的数据,合同id：'.$key.' contractreceivableamount='.$row['totalpayments'].' contractreceivablebalance='.($contractreceivableamount['contractreceivableamount']-$row['totalpayments']);

            }


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
                $j++;
                $max = $j*$max;
            }

            echo '同应收表数据<br>';


            $adb->pquery('truncate table vtiger_account_receivable');
//客户应收
            $sql2 = "
  insert into vtiger_account_receivable(`accountid`,`accountname`,`contractnum`,`bussinesstypenum`,`contracttotal`,`contractreceivableamount`,
                                        `contractreceivablebalance`,`contractinvoiceamount`,`contractpaidamount`,`contractoverduebalance`,`receivestatus`)
          select 
            b.accountid,b.accountname,
            ifnull(count(distinct c.contract_no),0) as contractnum,
            ifnull(count(distinct c.bussinesstype),0) as bussinesstypenum,
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
         where  c.modulestatus!='c_cancel' and a.iscancel=0 group by a.accountid";
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
                   d.bussinesstype,
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
        where a.overduedays>0 and d.modulestatus!='c_cancel' and a.collection!='overduereceived' and a.iscancel=0";
            $adb->pquery($sql4,array());

            echo '逾期表统计完成<br>';
            exit('执行结束');

        } elseif ($strPublic=='autosmallsass'){
            global $adb;
            //小SaaS 合同应收表
            $nowDate= date('Y-m-d');
            $nextDate= date('Y-m-d',(time()+24*60*60));
            //删掉当天的小SaaS的合同应收
            $adb->pquery("delete from vtiger_contract_receivable  where bussinesstype='smallsassdirect' and createdate='".$nowDate."'",array());
            $sql = "
                  insert into vtiger_contract_receivable(`contractid`,`accountid`,`contract_no`,`bussinesstype`,`productid`,`signid`,
                                                         `isautoclose`,`contracttotal`,`contractreceivableamount`,`contractreceivablebalance`,
                                                         `contractinvoiceamount`,`contractpaidamount`,`collectionstatus`,`signdempart`,`createdate`)
                select 
                        a.contractid,a.customerid as accountid,b.contract_no,
                       e.bussinesstype,
                       b.productid,
                       b.signid,
                       b.isautoclose,
                       ifnull(b.total,0) as contracttotal,
                      ifnull(sum(d.receiveableamount),0) as contractreceivableamount,
                      ifnull(sum(d.receiveableamount),0) as contractreceivablebalance,
                       ifnull((select sum(actualtotal) from vtiger_newinvoice where vtiger_newinvoice.contractid=a.contractid and vtiger_newinvoice.modulestatus='c_complete'),0) as contractinvoiceamount,
                      ifnull((select sum(unit_price) from vtiger_receivedpayments where vtiger_receivedpayments.relatetoid=b.servicecontractsid and vtiger_receivedpayments.ismatchdepart=1),0) as contractpaidamount,
                 if((select count(1) from vtiger_contracts_execution_detail where vtiger_contracts_execution_detail.contractid=d.contractid and vtiger_contracts_execution_detail.receiverabledate<CURRENT_DATE and vtiger_contracts_execution_detail.collection='overdue')>0,'hasoverdue','normal') as status,
                  b.signdempart,
                  '".$nowDate."'
                  from vtiger_activationcode a  
                left join vtiger_account c on c.accountid = a.customerid
                left join vtiger_servicecontracts b on a.contractid = b.servicecontractsid 
                left join vtiger_contracts_execution_detail d on d.contractid=a.contractid
                left join vtiger_contract_type e on e.contract_type=b.contract_type
                where a.contractid!='' and a.contractid is not null and a.startdate is not null and a.startdate>'".$nowDate."' and a.startdate<'".$nextDate."' and a.startdate!='0000-00-00 00:00:00'
                and a.onoffline='offline' and a.signaturetype='papercontract' and a.status in (0,1) and b.modulestatus!='c_cancel' group by contract_no";
            $adb->pquery($sql,array());
            exit('小SaaS统计完毕'.$nowDate);
    }elseif ($strPublic=='autoreceive'){
            global $adb;
            //处理应收余额
            $sql = "select * from vtiger_contracts_execution_detail where ischeck=0";
            $result = $adb->pquery($sql,array());
            $datas = array();
            $contractreceivableamount = 0;
            while ($row = $adb->fetchByAssoc($result)){
                $datas[$row['contractid']][$row['stage']]['contractreceivable'] = $row['contractreceivable'];
                $datas[$row['contractid']][$row['stage']]['receiveableamount'] = $row['receiveableamount'];

            }
            echo '<pre>';
            var_dump($datas);

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
                ksort($data);
                $sql3 = "update vtiger_contracts_execution_detail set contractreceivable=?,ischeck=?,accountingamount=? where contractid=? and stage=?";

                foreach ($data as $key2=>$value){
                    if($totalpayments<=0){
                        continue;
                    }
                    echo '剩余待减回款'.$totalpayments.' 应回款.'.$value['contractreceivable'].'<br>';
                    if($totalpayments>=$value['contractreceivable']){
                        $adb->pquery($sql3,array(0,1,$value['receiveableamount'],$key,$key2));
                        $totalpayments -= $value['contractreceivable'];
                    }else{
                        $adb->pquery($sql3,array(($value['contractreceivable']-$totalpayments),0,$totalpayments,$key,$key2));
                        $totalpayments -= $value;
                    }
                }

                $res2 = $adb->pquery("select contractreceivableamount from vtiger_contract_receivable where contractid = ?",array($key));
                $contractreceivableamount = $adb->fetchByAssoc($res2);
                //处理合同应收表中数据
                $sql4 = "update vtiger_contract_receivable set contractpaidamount=?,contractreceivablebalance=? where contractid = ?";
                $adb->pquery($sql4,array($row['totalpayments'],($contractreceivableamount['contractreceivableamount']-$row['totalpayments']),$key));
            }


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
                $j++;
                $max = $j*$max;
            }


            $adb->pquery('truncate table vtiger_account_receivable');
//客户应收
            $sql2 = "
  insert into vtiger_account_receivable(`accountid`,`accountname`,`contractnum`,`bussinesstypenum`,`contracttotal`,`contractreceivableamount`,
                                        `contractreceivablebalance`,`contractinvoiceamount`,`contractpaidamount`,`contractoverduebalance`,`status`)
          select
            b.accountid,b.accountname,
            ifnull(count(distinct c.contract_no),0) as contractnum,
            ifnull(count(distinct c.bussinesstype),0) as bussinesstypenum,
            ifnull(sum(c.total),0) as contracttotal,
            ifnull(sum(a.contractreceivableamount),0) as contractreceivableamount,
            ifnull(sum(a.contractreceivablebalance),0) as contractreceivablebalance,
            ifnull(sum(a.contractinvoiceamount),0) as contractinvoiceamount,
            ifnull(sum(a.contractpaidamount),0) as contractpaidamount,
            ifnull((select sum(contractreceivable) from vtiger_contracts_execution_detail where vtiger_contracts_execution_detail.accountid=a.accountid and vtiger_contracts_execution_detail.receiverabledate<CURRENT_DATE and vtiger_contracts_execution_detail.collection='overdue'),0) as contractoverduebalance,
            if((select count(1) from vtiger_contracts_execution_detail where vtiger_contracts_execution_detail.accountid=a.accountid and vtiger_contracts_execution_detail.receiverabledate<CURRENT_DATE  and vtiger_contracts_execution_detail.collection='overdue')>0,'hasoverdue','normal') as status
        from vtiger_contract_receivable a
        left join vtiger_account b on a.accountid = b.accountid
        left join vtiger_servicecontracts c on a.contractid=c.servicecontractsid
         where  c.modulestatus!='c_cancel' group by a.accountid";
            $adb->pquery($sql2,array());
            exit('执行完成');



        }elseif ($strPublic=='autowarn'){
            global $adb;

//逾期
            $sql = "select * from vtiger_earlywarningsetting where isclose=0 and remindertype='Overduewarning' limit 1";
            $result = $adb->pquery($sql, array());

            if ($adb->num_rows($result)) {
                //逾期的
                $sql2 = "SELECT
                            ( vtiger_servicecontracts.contract_no ) AS contract_no,
                            vtiger_receivable_overdue.bussinesstype,
                            vtiger_receivable_overdue.contracttotal,
                            vtiger_receivable_overdue.stageshow,
                            IFNULL(vtiger_receivable_overdue.receiveableamount,0) as receiveableamount,
                            ifnull(vtiger_receivable_overdue.contractreceivable,0) as contractreceivable,
                            ifnull(vtiger_receivable_overdue.overduedays,0) as overduedays,
                            concat(vtiger_users.last_name,'[',vtiger_departments.departmentname,']') as signname,
                            vtiger_receivable_overdue.signdate,
                            vtiger_receivable_overdue.receiverabledate,
                            ifnull( vtiger_products.productname,vtiger_servicecontracts.contract_type ) AS productid,
                            ( vtiger_account.accountname ) AS accountid,
                            vtiger_receivable_overdue.signid
                        FROM
                            vtiger_receivable_overdue
                            LEFT JOIN vtiger_modcomments ON vtiger_modcomments.moduleid = vtiger_receivable_overdue.contractid
                            LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_receivable_overdue.contractid
                            LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_receivable_overdue.productid
                            LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_receivable_overdue.accountid 
                            LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_receivable_overdue.signid 
                            LEFT JOIN vtiger_user2department ON vtiger_user2department.userid = vtiger_users.id 
                            LEFT JOIN vtiger_departments ON vtiger_departments.departmentid = vtiger_user2department.departmentid 
                        WHERE
                            1 = 1 and vtiger_receivable_overdue.iscancel=0
                            group by contract_no,signid,stageshow";
                $result2 = $adb->pquery($sql2,array());
                $data = array();
                if($adb->num_rows($result2)){
                    while ($row2 = $adb->fetchByAssoc($result2)){
                        $data[$row2['signid']][] = $row2;
                    }
                }
                echo '<pre>';
                var_dump($data);
                if(!empty($data)){
                    $recordModel = Vtiger_Record_Model::getCleanInstance("ReceivableOverdue");
                    $row = $adb->fetchByAssoc($result,0);
                    $alertChannels = explode(',',$row['alertchannels']);
                    foreach ($alertChannels as $alertChannel){
                        switch ($alertChannel) {
                            case 'email':
                                echo 'warnningemailstart';
                                $recordModel->sendWarningEmail($data);
                                echo 'warnningemailend';
                                break;
                            case 'assistant':
                                echo 'assistantstart';
                                $recordModel->sendWarningWx($data);
                                echo 'assistantend';
                                break;
                        }
                    }
                }

            }


            $sql3 = "select * from vtiger_earlywarningsetting where isclose=0 and remindertype='rbexp' limit 1";
            $result3 = $adb->pquery($sql3, array());
            if($adb->num_rows($result3)) {
                $row3 = $adb->fetchByAssoc($result3, 0);
                $days = $row3['forwardday'];
                $date = date('Y-m-d', (time() + $days * 24 * 60 * 60));
                $sql4 = "select 
                c.contract_no,
                d.accountname,
                c.bussinesstype,
                c.total as contracttotal,
                ifnull(e.productname,c.contract_type) as productname,
                a.stageshow,
                concat(f.last_name,'[',h.departmentname,']') as signname,
                c.signdate,
                ifnull(a.receiveableamount,0) as receiveableamount,
                a.receiverabledate,
                c.signid
                from vtiger_contracts_execution_detail a 
                left join vtiger_contracts_execution b on a.contractexecutionid = b.contractexecutionid
                left join vtiger_servicecontracts c on a.contractid = c.servicecontractsid
                left join vtiger_account d on d.accountid = a.accountid
                LEFT JOIN vtiger_products e ON e.productid = c.productid
                LEFT JOIN vtiger_users f ON f.id = c.signid 
                LEFT JOIN vtiger_user2department g ON g.userid = f.id 
                LEFT JOIN vtiger_departments h ON h.departmentid = g.departmentid 
                where a.contractreceivable>0 and a.receiverabledate=? and a.iscancel=0";
                $result4 = $adb->pquery($sql4, array($date));
                $data = array();
                if ($adb->num_rows($result4)) {
                    while ($row4 = $adb->fetchByAssoc($result4)) {
                        $data[$row4['signid']][] = $row4;
                    }
                }
                echo '<br>即将逾期提醒';
                var_dump($data);

                if (!empty($data)) {
                    $recordModel = Vtiger_Record_Model::getCleanInstance("ContractExecution");
                    $alertChannels = explode(',', $row3['alertchannels']);
                    foreach ($alertChannels as $alertChannel) {
                        switch ($alertChannel) {
                            case 'email':
                                $recordModel->sendWarningEmail($data);
                                break;
                            case 'assistant':
                                $recordModel->sendWarningWx($data);
                                break;
                        }
                    }
                }
            }
            exit('已发送');
        }elseif ($strPublic == 'autooverdue'){
            global $adb;

            $sql = "select * from vtiger_contracts_execution_detail where receiverabledate<CURRENT_DATE and collection!='overduereceived'";
            $result = $adb->pquery($sql);
            if(!$adb->num_rows($result)){
                exit('无逾期的阶段');

            }

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
                    $collection = 'overduereceived';
                }
                $adb->pquery($sql2,array($Days,$collection,$row['executiondetailid']));
            }


//清空逾期应收表
            $adb->pquery("truncate table vtiger_receivable_overdue");

//逾期应收明细表
            $sql4 = "insert into vtiger_receivable_overdue (`contract_no`,`bussinesstype`,`contracttotal`,`stage`,
                                       `stageshow`,`productid`,`signid`,`signdate`,`receiveableamount`,`contractreceivable`,
                                       `overduedays`,`executiondetailid`,`contractexecutionid`,`accountid`,`contractid`,
                                       `receiverabledate`,`collection`,`commentcontent`,`lastfollowtime`) 
            select 
                   d.contract_no,
                   d.bussinesstype,
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
        where a.overduedays>0 and d.modulestatus!='c_cancel' and a.collection!='overduereceived' and a.iscancel=0";
            $adb->pquery($sql4,array());
            exit('统计完成');

        }elseif ($strPublic=='clearall'){
            global $adb;
            $adb->pquery('truncate table vtiger_contracts_execution',array());
            $adb->pquery('truncate table vtiger_contracts_execution_detail',array());
            $adb->pquery('truncate table vtiger_contract_receivable',array());
            $adb->pquery('truncate table vtiger_account_receivable',array());
            $adb->pquery('truncate table vtiger_receivable_overdue',array());
            exit('清空完毕');
        }elseif ($strPublic=='autoaccount'){
            global $adb;
            $adb->pquery('truncate table vtiger_account_receivable');
//客户应收
            $sql2 = "
  insert into vtiger_account_receivable(`accountid`,`accountname`,`contractnum`,`bussinesstypenum`,`contracttotal`,`contractreceivableamount`,
                                        `contractreceivablebalance`,`contractinvoiceamount`,`contractpaidamount`,`contractoverduebalance`,`receivestatus`)
          select 
            b.accountid,b.accountname,
            ifnull(count(distinct c.contract_no),0) as contractnum,
            ifnull(count(distinct c.bussinesstype),0) as bussinesstypenum,
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
         where  c.modulestatus!='c_cancel' group by a.accountid";
            $adb->pquery($sql2,array());

            exit('客户应收表统计完毕');
        }
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
        $this->viewName = $request->get('viewname');
        if (empty($this->viewName)) {
            //If not view name exits then get it from custom view
            //This can return default view id or view id present in session
            $customView = new CustomView();
            $this->viewName = $customView->getViewId($moduleName);
        }
        $this->initializeListViewContents($request, $viewer);//竟然调用两次，
        $viewer->assign('VIEW', $request->get('view'));
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $cv = new CustomView_EditAjax_View;
        $vr = new Vtiger_Request;
        $vr->set('source_module', $moduleName);
        $vr->set('module', 'CustomView');
        $vr->set('view', 'EditAjax');
        $vr->set('record', $request->get('viewname'));
        $cv->getSearch($vr, $viewer);
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $this->viewName = $request->get('viewname');
        require 'crmcache/departmentanduserinfo.php';
        $viewer->assign('CACHEDEPARTMENT', $cachedepartment);
        $viewer->assign('VIEWNAME', $this->viewName);
        $viewer->view('ListViewContents.tpl', $moduleName);
    }
}