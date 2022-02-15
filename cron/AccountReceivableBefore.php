<?php
ini_set("include_path", "../");

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
$adb->pquery("CREATE TABLE `vtiger_contract_receivable_temp`  (
  `contractreceivableid` int(19) NOT NULL AUTO_INCREMENT,
  `contract_no` varchar(320) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '合同编号',
  `accountid` int(19) NOT NULL COMMENT '客户id',
  `contracttotal` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '总合同额',
  `bussinesstype` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '业务类型',
  `productid` int(11) NOT NULL COMMENT '产品类型',
  `contractid` int(19) NOT NULL COMMENT '合同id',
  `contractpaidamount` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '合计实收金额',
  `contractinvoiceamount` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '合计开票金额',
  `contractreceivableamount` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '合计应收金额',
  `contractreceivablebalance` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '合计应收余额',
  `signid` int(11) NOT NULL COMMENT '签订人',
  `status` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '状态',
  `signdempart` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '合同所属部门',
  `isautoclose` int(9) NULL DEFAULT NULL COMMENT '非框架合同',
  `collectionstatus` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '状态',
  `createdate` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '创建时间',
  `iscancel` tinyint(1) NULL DEFAULT 0 COMMENT '是否作废 0未作废，1作废',
  `startdate` varchar(32) ,
  `modulestatus` varchar(32),
  PRIMARY KEY (`contractreceivableid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic");

$sql = "  insert into vtiger_contract_receivable_temp(`contractid`,`accountid`,`contract_no`,`bussinesstype`,`productid`,`signid`,
                                                         `isautoclose`,`contracttotal`,`contractreceivableamount`,`contractreceivablebalance`,
                                                         `contractinvoiceamount`,`contractpaidamount`,`collectionstatus`,`signdempart`,`startdate`,`modulestatus`)
                 select 
                        a.contractid,a.customerid as accountid,b.contract_no,
                       e.bussinesstype,
                       b.productid,
                       b.signid,
                       b.isautoclose,
                       ifnull(b.total,0) as contracttotal,
                      ifnull(b.total,0) as contractreceivableamount,
                      ifnull(b.total,0) as contractreceivablebalance,
                       ifnull((select sum(actualtotal) from vtiger_newinvoice where vtiger_newinvoice.contractid=a.contractid and vtiger_newinvoice.modulestatus='c_complete'),0) as contractinvoiceamount,
                      ifnull((select sum(unit_price) from vtiger_receivedpayments where vtiger_receivedpayments.relatetoid=b.servicecontractsid and vtiger_receivedpayments.ismatchdepart=1),0) as contractpaidamount,
                 if((select count(1) from vtiger_contracts_execution_detail where vtiger_contracts_execution_detail.contractid=d.contractid and vtiger_contracts_execution_detail.receiverabledate<CURRENT_DATE and vtiger_contracts_execution_detail.collection='overdue')>0,'hasoverdue','normal') as status,
                  b.signdempart,
                  a.startdate,
                  b.modulestatus
                  from vtiger_activationcode a  
                left join vtiger_account c on c.accountid = a.customerid
                left join vtiger_servicecontracts b on a.contractid = b.servicecontractsid 
                left join vtiger_contracts_execution_detail d on d.contractid=a.contractid
                left join vtiger_contract_type e on e.contract_type=b.contract_type
                where a.contractid!='' and a.contractid is not null and a.startdate is not null and a.startdate!='0000-00-00 00:00:00'
                and a.onoffline='offline' and a.signaturetype='papercontract' and a.status in (0,1) and b.modulestatus!='c_cancel' 
                and a.startdate<'2020-08-06 23:59:59'
                  and a.iscollegeedition= 0 and e.bussinesstype='smallsassdirect' group by contract_no ";
//插入临时表
$adb->pquery($sql,array());
echo '执行成功';
