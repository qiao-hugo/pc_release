<?php
/**
 * 注：百度V是以高级词来区分是否是新单还是续费，不确定哪个回款是属于新单还是续费（又因百度V是全额打款 不存在分批打款的情况），所以业绩以合同为主来算，回款id只作为
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
global $tyunweburl,$sault;
$limit=500;
$page = 1;
$date=$_REQUEST['date'];
if(empty($date)){
    $date = date('Y-m', strtotime('-1 month'));
}
echo '<h1>-------<b>百度v臻信通审核通过但以前没有匹配回款,且'.$date.'匹配回款的业绩计算<b>-------</h1><hr>';

echo '<h2>处理回款金额小于审核总金额的合同</h2><br>';
$contractList = getNoMatchGroupContract($date);
$insertSub = [];
foreach($contractList as $val){
    //查询合同对应续费和新单金额
    $contractPrice = getNoMatchContractNewAndRenew($val['contractid']);
    $receivedInfo = getContractReceivedPayment($val['contractid']);
    if(empty($receivedInfo['receivedpaymentsid'])){
        //修改合同的高级词表的是否匹配回款信息
        echo '合同编号：'.$val['contractno'].'无匹配回款信息<br>';
        continue;
    }
    //***********注意：合同匹配的回款总金额（有可能有多条回款）
    $receivedInfo['unit_price'] = getContractReceivedPaymentTotal($val['contractid']);
    echo '合同编号：'.$val['contractno'].'的审核通过金额:（新单：'.$contractPrice['newadd'].'，续费：'.$contractPrice['renew'].'）,匹配回款金额：'.$receivedInfo['unit_price'].'<br>';
    //判断回款金额是否大于等于审核通过的总金额
    $totalPrice = bcadd($contractPrice['newadd'], $contractPrice['renew'], 4);
    if(bccomp($totalPrice, $receivedInfo['unit_price'], 4) == 1){
        $diff = bcsub($totalPrice, $receivedInfo['unit_price'],4);
        $subNewadd = bcmul(bcdiv($contractPrice['newadd'], $totalPrice, 6),$diff,4);
        $subRenew = bcsub($diff, $subNewadd,4);
        $advancedData = [];
        $advancedData["ismatchreceive"] = $val['ismatchreceive'];
        $advancedData["receivedpaymentsid"] = $val['receivedpaymentsid'];
        $advancedData["achievementmonth"] = $val['achievementmonth'];
        $advancedData["contractid"] = $val['contractid'];
        $advancedData["activationcodeid"] = $val['activationcodeid'];
        $advancedData["word"] = '';
        $advancedData["examinedate"] = $val["examinedate"];
        $advancedData["contractno"] = $val["contractno"];
        $advancedData["status"] = $val["status"];
        $advancedData["ordercode"] = $val["ordercode"];  
        $advancedData['zhxtwordid'] = $val['zhxtwordid'];
        $advancedData["orderprice"] = $val["orderprice"];
        
        if($subNewadd > 0){
            $advancedData["price"] = 0 - $subNewadd;
            $advancedData["achievementtype"] = 1;
            $insertSub[] = $advancedData;
        }

        if($subRenew > 0){
            $advancedData["price"] = 0 - $subRenew;
            $advancedData["achievementtype"] = 2;
            $insertSub[] = $advancedData;
        }
        echo '合同编号：'.$val['contractno'].'的扣减业绩:（新单扣减：'.$subNewadd.'，续费扣减：'.$subRenew.'）<br>';
    }
    $adb->pquery('DELETE FROM vtiger_zhxtadvancedwords where contractno=? and price < 0',array($val['contractno']));
}
if(!empty($insertSub)){
    insertWordData($insertSub);
}
echo '<h2>处理回款金额小于审核总金额的合同结束<h2><hr>';


//1.查询以前没有匹配回款的高级词数据
//2.1 查询需要计算业绩的数据
echo '<h2>-------开始业绩计算--------</h2><br>';
$advancedWordsData = getNoMatchAdvancedWordsData($date);
if(empty($advancedWordsData)){
    echo '------没有需要计算业绩的数据<br>';
    comm_logs('没有需要计算业绩的数据', 'zhxtlogs_');
    exit;
}
//2.2 计算每条数据业绩
foreach($advancedWordsData as $rp){
    echo '开始计算合同编号：'.$rp['contract_no'].'，审核通过金额:'.$rp['examine_price'].',业绩类型：'.$rp['achievementtype'].'（1：新增 2：续费） 的员工业绩<br>';
    //获取产生的业绩数据
    $data = zhxtWordNoMatchCalculationAchievement($rp,$adb,$date);
    //将业绩数据保存至数据表中
    if($rp['achievementtype'] == 2){
        $achievementtype='renew';
    }else{
        $achievementtype='newadd';
    }
    advancedWordsNoMatchAchievement($data,$rp['servicecontractsid'],$date,$achievementtype,$adb);
    echo '结束计算合同编号：'.$rp['contract_no'].'，业绩类型：'.$rp['achievementtype'].'（1：新增 2：续费） 的员工业绩<hr>';
}
echo '<h2>-------业绩计算结束--------</h2><br>';

//获取高级词订单和合同信息，用于计算业绩
function getNoMatchAdvancedWordsData($date){
    global $adb;

    $querysql = "SELECT zw.achievementtype,zw.achievementmonth,s.multitype,s.contract_no,s.oldcontract_usedtime,s.oldcontractid,s.extraproductid,s.productid,s.invoicecompany,s.parent_contracttypeid,s.contract_type,s.servicecontractsid,s.total,sum(zw.price) as examine_price,s.servicecontractstype,left(s.signdate,10) AS signdate,s.contract_no,a.accountname FROM vtiger_zhxtadvancedwords as zw LEFT JOIN vtiger_servicecontracts as s ON s.servicecontractsid=zw.contractid LEFT JOIN vtiger_account as a ON a.accountid=s.sc_related_to  WHERE zw.achievementmonth <= '".$date."' and zw.ismatchreceive=0 group by zw.contractno,zw.achievementtype";
    return $adb->run_query_allrecords($querysql);
}

//处理数据得到入vtiger_achievementallot_statistic表的数据
function zhxtWordNoMatchCalculationAchievement($rp,$adb,$date){
    comm_logs('--------计算业绩的数据：'.json_encode($rp), 'zhxtlogs_');
    $contractid = $rp['servicecontractsid'];
    $shareuser = 0;
    $generatedamount=0;
    $remark = "臻信通高级词";

    //查询服务合同对应的回款信息
    $receivedInfo = getContractReceivedPayment($contractid);
    if(empty($receivedInfo['receivedpaymentsid'])){
        //修改合同的高级词表的是否匹配回款信息
        echo '无匹配回款信息<br>';
        updateNoMatchAdvancedWords($contractid,array('ismatchreceive'=>0,'receivedpaymentsid'=>0));
        return array("datavalue"=>'',"insertValueStr"=>'');
    }
    echo '有匹配回款信息：id---'.$receivedInfo['receivedpaymentsid'].'<br>';
    //匹配时间
    $matchdate = $receivedInfo['matchdate'];
    if(empty($matchdate)){
        $matchdate=date('Y-m-d H:i:s');
    }
    $matchdate=date('Y-m-d',strtotime($matchdate));
    $achievementmonth=date('Y-m',strtotime($matchdate));
    if(strtotime($achievementmonth) <= strtotime($date)){
        $achievementmonth = $date;
    }
    //如果有匹配回款，就修改月份和标记
    updateNoMatchAdvancedWords($contractid,array('ismatchreceive'=>1,'receivedpaymentsid'=>$receivedInfo['receivedpaymentsid'],'achievementmonth'=>$achievementmonth));

    if(strtotime($achievementmonth) > strtotime($date)){
        echo '当前匹配回款的月份'.$achievementmonth.',不计算到'.$date.'的业绩<br>';
        return array("datavalue"=>'',"insertValueStr"=>'');
    }

    //查询该服务合同对应的产品
    $queryc=" SELECT p.productid,p.productname,ssp.productcomboid,ssp.thepackage,count(1) as counts FROM vtiger_salesorderproductsrel as ssp LEFT JOIN vtiger_products as p ON p.productid=ssp.productid  WHERE ssp.servicecontractsid =? AND ssp.multistatus=3 group by ssp.productcomboid ";
    $productdatas=$adb->pquery($queryc,array($contractid));
    $productname='';
    while ($rowDatas=$adb->fetch_array($productdatas)){
        if($rowDatas['productid']==$rp['productid']){
            $productname.=$rowDatas['thepackage']."(1),";
        }else{
            $productname.=$rowDatas['thepackage']."(".$rowDatas['counts']."),";
        }
    }
    $productname=trim($productname,',');

    $rp['marketprice']=0;
    $modulestatus='a_normal';
    
    //成本扣除数
    $costdeduction=0;
    
    if($rp['achievementtype'] == 2){
        $achievementtype='renew';
    }else{
        $achievementtype='newadd';
    }
    //查询该服务合同分成人
    $queryc=" SELECT vtiger_servicecontracts_divide.*,vtiger_departments.departmentname FROM vtiger_servicecontracts_divide  LEFT JOIN vtiger_departments ON  vtiger_departments.departmentid=vtiger_servicecontracts_divide.signdempart WHERE vtiger_servicecontracts_divide.servicecontractid = ?";
    $resultdatas=$adb->pquery($queryc,array($contractid));
    $insertValueStr='';
    $i=1;
    while ($rowDatas=$adb->fetch_array($resultdatas)){

        $scalling=$rowDatas['scalling'];
        $businessunit=0;
        $receivedpaymentownid=$rowDatas['receivedpaymentownid'];
        $i++;
        $dividetotal=$rp['total']*$scalling/100;
        // 查询分成人 所在部门  以及属事业部查询
        $queryc=" SELECT d.departmentid,d.departmentname,d.parentdepartment,u.last_name FROM vtiger_user2department ud  LEFT JOIN vtiger_departments as d  ON ud.departmentid=d.departmentid LEFT JOIN  vtiger_users as u ON  u.id=ud.userid  WHERE  ud.userid= ? LIMIT 1 ";
        $resultdataDepartment=$adb->pquery($queryc,array($receivedpaymentownid));
        $Department=$adb->query_result_rowdata($resultdataDepartment,0);
        $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
        $departmentInfo=$Matchreceivements_Record_Model->getDepartmentInfo($Department);
        $groupname=$departmentInfo['groupname'];
        $departmentname=$departmentInfo['departmentname'];

        //非百度v不产生业绩
        $parentdepartmentArr = explode('::',$Department['parentdepartment']);
        if(!in_array('H82', $parentdepartmentArr) && !in_array('H83', $parentdepartmentArr)){
            echo '-----员工id：'.$receivedpaymentownid.'非百度v部门不产生业绩<br>';
            continue;
        }
        
        $costing=0;
        $purchasemount=0;
        $extracost = 0;
        $allother=0;
        // 重新计算沙龙  媒体外采  没接充值  和   其他 （其他包含所有之和）
        $salong=0;
        $waici=0;
        $meijai=0;
        //  other 指 回款（沙龙外采媒体充值其他的总和）
        $othercost=0;
        //工单成本合计
        $worksheetcost=0;
        $divideworksheetcost=0;
        $dividecosting=0;
        $dividepurchasemount=0;
        $divideextracost=0;
        $divideother=0;
        //已分成业绩市场价
        $dividemarketprice=0;
        //已分成成本扣除数
        $dividecostdeduction=0;
        $other=$othercost;

        // 已分成审核的金额
        $unit_prices=$rp['examine_price']*($scalling/100);
        //到账业绩
        $arriveachievement=$unit_prices;
        $producttype=7;
        $receivedpaymentown=$Department['last_name'];
        // 最后判断下到账回款 是否为负值 如果为负 则设为零
        // if($arriveachievement < 0){
        //     $arriveachievement=0;
        // }
        $effectiverefund=$rp['examine_price']*$scalling/100;
        $more_years_renew=0;
        $renewal_commission=0;
        $renewtimes=0;
        $splitcontractamount=0;
        $splitmarketprice=0;
        $splitcost=0;
        $commissionforrenewal=0;

        $datavalue[]=$receivedInfo['reality_date'];
        $datavalue[]=$receivedInfo['paytitle']?$receivedInfo['paytitle']:'';  
        $datavalue[]=$receivedInfo['createtime'];
        $datavalue[]=$receivedInfo['owncompany'];
        $datavalue[]=$rowDatas['owncompanys'];
        $datavalue[]=$receivedpaymentownid;
        $datavalue[]=$scalling;
        $datavalue[]=$rowDatas['servicecontractid'];
        $datavalue[]=$receivedInfo['receivedpaymentsid']?$receivedInfo['receivedpaymentsid']:0;
        $datavalue[]=$businessunit;
        $datavalue[]=$matchdate;
        $datavalue[]=$Department['departmentid']?$Department['departmentid']:0;
        $datavalue[]=$receivedInfo['unit_price']?$receivedInfo['unit_price']:0;
        $datavalue[]=$unit_prices;
        $datavalue[]=$rowDatas['departmentname']?$rowDatas['departmentname']:'';
        $datavalue[]=$groupname?$groupname:' ';
        $datavalue[]=$departmentname?$departmentname:' ';
        $datavalue[]=$receivedpaymentown;
        $datavalue[]=$rp['servicecontractstype']?$rp['servicecontractstype']:' ';
        $datavalue[]=$rp['accountname']?$rp['accountname']:' ';
        $datavalue[]=$rp['signdate'];
        $datavalue[]=$rp['contract_no'];
        $datavalue[]=$rp['total']?$rp['total']:0;
        $datavalue[]=$dividetotal;
        $datavalue[]=$costing;
        $datavalue[]=$purchasemount?$purchasemount:0;
        $datavalue[]=$worksheetcost?$worksheetcost:0;
        $datavalue[]=0;
        $datavalue[]=$rp['marketprice']?$rp['marketprice']:0;
        $datavalue[]=$dividemarketprice;
        $datavalue[]=$costdeduction;
        $datavalue[]=$dividecostdeduction;
        $datavalue[]=$other;
        $datavalue[]=$effectiverefund;
        $datavalue[]=$arriveachievement;
        $datavalue[]=$achievementmonth;
        $datavalue[]=$modulestatus;
        $datavalue[]=$productname;
        $datavalue[]=$achievementtype?$achievementtype:0;
        $datavalue[]=$producttype?$producttype:0;
        $datavalue[]=$extracost;
        $datavalue[]=$salong;
        $datavalue[]=$waici;
        $datavalue[]=$meijai;
        $datavalue[]=$othercost;
        $datavalue[]=$shareuser;
        $datavalue[]=$remark;
        $datavalue[]=$generatedamount;
        $datavalue[]=$arriveachievement;
        $datavalue[]=$divideworksheetcost;
        $datavalue[]=$dividecosting;
        $datavalue[]=$dividepurchasemount;
        $datavalue[]=$divideextracost;
        $datavalue[]=$divideother;
        $datavalue[]=$more_years_renew;
        $datavalue[]=$renewal_commission;
        $datavalue[]=$renewtimes;
        $datavalue[]=$splitcontractamount;
        $datavalue[]=$splitmarketprice;
        $datavalue[]=$splitcost;
        $datavalue[]=$commissionforrenewal;
        $insertValueStr.='(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?),';
        echo '-----员工id：'.$receivedpaymentownid.'-----------到账业绩：'.$arriveachievement.'----<br>';
    }
    comm_logs('--------计算业绩后入表的数据：'.json_encode($datavalue), 'zhxtlogs_');
    return array("datavalue"=>$datavalue,"insertValueStr"=>$insertValueStr);
}

//计算业绩入表
function advancedWordsNoMatchAchievement($data,$contractid,$date,$achievementtype,$adb){
    $insertValueStr=$data['insertValueStr'];
    $datavalue=$data['datavalue'];
    $field='reality_date,paytitle,createtime,owncompany,owncompanys,receivedpaymentownid,scalling,servicecontractid,receivedpaymentsid,businessunit,matchdate,departmentid,unit_price,unit_prices,department,groupname,departmentname,receivedpaymentown,servicecontractstype,accountname,signdate,contract_no,total,dividetotal,costing,purchasemount,worksheetcost,productlife,marketprice,dividemarketprice,costdeduction,dividecostdeduction,other,effectiverefund,arriveachievement,achievementmonth,modulestatus,productname,achievementtype,producttype,extracost,salong,waici,meijai,othercost,shareuser,remarks,generatedamount,adjustbeforearriveachievement,divideworksheetcost,dividecosting,dividepurchasemount,divideextracost,divideother,more_years_renew,renewal_commission,renewtimes,splitcontractamount,splitmarketprice,splitcost,commissionforrenewal';
    // 最后如果不为空则进行处理数据
    if(!empty($insertValueStr)){
        $insertValueStr = trim($insertValueStr,",");
        $addStastic_sql = "INSERT INTO vtiger_achievementallot_statistic (".$field.") VALUES ";
        $deleteStastic_sql='DELETE  FROM  vtiger_achievementallot_statistic WHERE servicecontractid = ? and achievementtype=? and achievementmonth=?';
        $adb->pquery($deleteStastic_sql,array($contractid,$achievementtype,$date));
        //插入做销售业绩明细表数据
        $adb->pquery($addStastic_sql.$insertValueStr,array($datavalue));
        //插入汇总表数据
        $sqlquery=" SELECT achievementallotid,receivedpaymentownid,reality_date FROM vtiger_achievementallot_statistic WHERE servicecontractid=? and achievementtype=? AND achievementmonth = ? ";
        $result = $adb->pquery($sqlquery,array($contractid, $achievementtype, $date));

        $newArray=[];
        while ($rowdatas=$adb->fetch_array($result)){
            $query="SELECT left(leavedate,10) AS leavedate,id FROM vtiger_users WHERE id=? AND isdimission=1";
            $userResult=$adb->pquery($query,array($rowdatas['receivedpaymentownid']));
            if($adb->num_rows($userResult)){
                $leavedate=$userResult->fields['leavedate'];
                if($rowdatas['reality_date']<=$leavedate){
                    $newArray[]=$rowdatas['achievementallotid'];
                }else{
                    $sql='UPDATE vtiger_achievementallot_statistic SET isleave=1 WHERE achievementallotid=?';
                    $adb->pquery($sql,array($rowdatas['achievementallotid']));
                }
            }else{
                $newArray[]=$rowdatas['achievementallotid'];
            }
            //插入汇总表数据
            insertAchievementSummany($rowdatas['receivedpaymentownid'], $achievementtype, $date);
        }

        $sql='UPDATE vtiger_achievementallot_statistic SET userfullname=(SELECT last_name FROM vtiger_users WHERE id=receivedpaymentownid) WHERE servicecontractid=?';
        $adb->pquery($sql,array($contractid));
        // $recordModel = Matchreceivements_Record_Model::getCleanInstance("Matchreceivements");
        // $recordModel->matchToRanking($receivepayid);
    }
}

//查询该服务合同匹配的回款id
function getContractReceivedPayment($contractid){
    global $adb;
    $sql = 'select r.receivedpaymentsid,r.owncompany,r.matchdate,LEFT (r.createtime,10) AS createtime,r.reality_date,r.matchdate,r.paytitle,r.unit_price from vtiger_receivedpayments  as r left join vtiger_servicecontracts as s on s.servicecontractsid=r.relatetoid WHERE r.relatetoid = ?  ORDER BY r.receivedpaymentsid DESC LIMIT 1';
  
    $result=$adb->pquery($sql,array($contractid));
    if($adb->num_rows($result) <= 0){
        return [];
    }
    $info = $adb->query_result_rowdata($result,0);
    return $info;

}

//修改高级词表的是否回款信息
function updateNoMatchAdvancedWords($contractid,$data){
    global $adb;
    if(empty($contractid) || empty($data)){
        return false;
    }
    $setValue = array();
    $values = array_values($data);
    $values[] = $contractid;
    foreach($data as $key => $val){
        $setValue[] = $key.'=?';
    }
    $sql = 'update vtiger_zhxtadvancedwords set '.implode(',',$setValue).' where contractid=? and ismatchreceive=0';
    return $adb->pquery($sql,$values);
}

//汇总
function insertAchievementSummany($userid,$achievementtype,$date){
    global $adb;
    $sql = "delete from vtiger_achievementsummary where achievementmonth=? and userid=? and achievementtype=?";
    $result = $adb->pquery($sql, array($date, $userid, $achievementtype));

    $statisticSql ='select * from vtiger_achievementallot_statistic where achievementmonth=? and receivedpaymentownid=? and achievementtype=? and producttype = 7 and isleave = 0';
    $resultS = $adb->pquery($statisticSql, array($date, $userid, $achievementtype));
    if ($adb->num_rows($result)) {
        return false;
    }

    $unit_price = 0;
    $arriveachievement = 0;
    $effectiverefund = 0;
    $achievementmonth = $date;
    $departmentid = '';
    $realarriveachievement = 0;
    $invoicecompany = '';
    $confirmstatus = 'tobeconfirm';
    $createtime = date('Y-m-d H:i:s');

    while ($row = $adb->fetch_row($resultS)){
        $invoicecompany = $row['owncompanys'];
        $departmentid = $row['departmentid'];
        $unit_price += $row['unit_price'];
        $arriveachievement += $row['arriveachievement'];
        $effectiverefund += $row['effectiverefund'];
        $realarriveachievement += $row['arriveachievement'];
    }
    echo '-----员工id：'.$userid.'-----------汇总到账业绩：'.$arriveachievement.'----<br>';
    $insertSql = 'INSERT INTO vtiger_achievementsummary (unit_price,arriveachievement,effectiverefund,achievementmonth,departmentid,achievementtype,realarriveachievement,invoicecompany,confirmstatus,createtime,userid) VALUES (?,?,?,?,?,?,?,?,?,?,?)';

    $adb->pquery($insertSql,array($unit_price,$arriveachievement,$effectiverefund,$achievementmonth,$departmentid,$achievementtype,$realarriveachievement,$invoicecompany,$confirmstatus,$createtime,$userid)); 
    return true;
}

//获取合同的高级词的新增和续费的金额
function getNoMatchContractNewAndRenew($contractid){
    global $adb;

    $querysql = "SELECT sum(if(zw.achievementtype=1,if(zw.price >= 0,zw.price,0),0)) as newadd,sum(if(zw.achievementtype=2,if(zw.price >= 0,zw.price,0),0)) as renew FROM vtiger_zhxtadvancedwords as zw WHERE zw.contractid = ?";
    $result=$adb->pquery($querysql,array($contractid));
    if($adb->num_rows($result) <= 0){
        return ['newadd'=>0,'renew'=>0];
    }
    return $adb->query_result_rowdata($result, 0);
}

//获取当月的审核高级词的信息以合同分组
function getNoMatchGroupContract($date){
    global $adb;

    $querysql = "SELECT zw.word,zw.examinedate,zw.contractno,zw.contractid,zw.ordercode,zw.activationcodeid,zw.price,zw.achievementmonth,zw.zhxtwordid,zw.createdtime,zw.status,zw.orderprice,zw.achievementtype,zw.ismatchreceive,zw.receivedpaymentsid FROM vtiger_zhxtadvancedwords as zw WHERE zw.achievementmonth <='".$date."' and zw.ismatchreceive=0 group by zw.contractid";
    return $adb->run_query_allrecords($querysql);
}

//将处理后的高级词数据入表
function insertWordData($data){
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
    $insertWordsSql = 'INSERT INTO vtiger_zhxtadvancedwords ('.implode(',',$field).') VALUES '.trim($valuesStr,',');
    global $adb;
    $adb->pquery($insertWordsSql,$values);
    return true;
}

//查询该服务合同匹配的回款总金额
function getContractReceivedPaymentTotal($contractid){
    global $adb;
    $sql = 'select sum(r.unit_price) as unit_price from vtiger_receivedpayments  as r left join vtiger_servicecontracts as s on s.servicecontractsid=r.relatetoid WHERE r.relatetoid = ? LIMIT 1';
    $result=$adb->pquery($sql,array($contractid));
    $unit_price = $adb->query_result($result,0,'unit_price');
    return empty($unit_price) ? 0 : $unit_price;

}