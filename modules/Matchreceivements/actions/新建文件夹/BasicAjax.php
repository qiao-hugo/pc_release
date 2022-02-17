<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Matchreceivements_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('getserviceinfo');
		$this->exposeMethod('throwreceivement');
	}

	function checkPermission(Vtiger_Request $request) {
		return;
	}

    public function getserviceinfo(Vtiger_Request $request){
        $contractid = $request->get('contractid');
        $servicecontracts_divide = ServiceContracts_Record_Model::servicecontracts_divide($contractid);
        if(!empty($servicecontracts_divide)){
            foreach($servicecontracts_divide as $v){

            }
        }

        $response = new Vtiger_Response();
        $response->setResult('');
        $response->emit();
    }

    public function throwreceivement (Vtiger_Request $request){
        $adb = PearDatabase::getInstance();
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $currentid = $currentUser->get('id');
        $receivepayid=$request->get('receivepayid');
        $sql = "INSERT INTO `vtiger_receivedpayments_throw` (`id`, `userid`, `receivepaymentid`, `date`, `deleted`) VALUES (NULL, ?, ?, ?, '0');";
        $adb->pquery($sql, array($currentid, $receivepayid, date('Y-m-d H:i:s')));
    }

    /**
     * tYun类业绩计算结束
     */
    public function tYunCalculationAchievement($receivepayid,$contractid,$shareuser,$total,$currentid,$adb,$matchdate=0,$salesorderid=0){
        // cxh add start
        $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
        //① 查询回款相关数据  vtiger_receivedpaymentsIFNULL(sum(vtiger_salesorderproductsrel.costing),0)()
        $queryc=" SELECT ac.usercode,ac.contractprice,IFNULL(ac.customerid,acs.customerid) as customerid,ac.activityname,ac.activitytype,IFNULL(max(ac.productlife),max(acs.productlife)) as productlife,ac.activationcodeid,IFNULL(ac.classtype,acs.classtype) as classtype,IFNULL(ac.createdtime,acs.createdtime) as createdtime,ac.contractamount,IFNULL(ac.usercode,acs.usercode) as usercode ,ac.oldsurplusmoney,ac.startdate,s.signid as newsignid,s.contract_type,s.sc_related_to,s.contract_no,s.oldcontract_usedtime,s.oldcontractid,s.productid,s.invoicecompany,s.parent_contracttypeid,s.servicecontractsid,s.total,r.owncompany,(SELECT vtiger_receivedpayments_notes.createtime FROM vtiger_receivedpayments_notes WHERE vtiger_receivedpayments_notes.receivedpaymentsid=r.receivedpaymentsid ORDER BY vtiger_receivedpayments_notes.receivedpaymentsnotesid DESC LIMIT 1) AS matchdatetime,LEFT (r.createtime,10) AS createtime,r.reality_date,r.matchdate,r.paytitle,r.unit_price,d.departmentname as department,s.servicecontractstype,left(s.signdate,10) AS signdate,s.contract_no,a.accountname,IFNULL(ac.marketprice,acs.marketprice) AS marketprice,ac.renewmarketprice FROM vtiger_receivedpayments  as r LEFT JOIN vtiger_departments as d ON r.departmentid=d.departmentid LEFT JOIN vtiger_servicecontracts as s ON s.servicecontractsid=r.relatetoid LEFT JOIN vtiger_account as a ON a.accountid=s.sc_related_to LEFT JOIN vtiger_activationcode as ac ON (ac.contractid=s.servicecontractsid AND ac.productid>0 AND ac.status IN(0,1) ) LEFT JOIN vtiger_activationcode as acs ON (acs.contractid=s.servicecontractsid  AND acs.status IN(0,1) ) WHERE receivedpaymentsid = ?  ORDER BY receivedpaymentsid DESC LIMIT 1 ";
        $resultdatapayments=$adb->pquery($queryc,array($receivepayid));
        $rp=$adb->query_result_rowdata($resultdatapayments,0);
        $result=$adb->run_query_allrecords(" SELECT  *  FROM  vtiger_activationcode  WHERE  contractid=".$contractid." AND  comeformtyun=1  LIMIT 1 ");
        //查询该合同是否存在T云订单
        if(empty($result)){
            $paramers['contract_no']=$rp['contract_no'];
            $paramers['marks']='T云系列合同但是T云WEB订单管理没生成订单,contractid'.$contractid;
            $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
            $Matchreceivements_Record_Model->noCalculationAchievementRecord($paramers);
            return ;
        }

        //算出总的合同金额
        $queryc="SELECT SUM(contractamount) as contractamount,SUM(contractprice) as contractprice,SUM(upgradetransfer) as upgradetransfer FROM vtiger_activationcode WHERE contractid=?  AND status IN(0,1) " ;
        $contractInfo=$adb->pquery($queryc,array($contractid));
        $contractInfo=$adb->query_result_rowdata($contractInfo,0);
            $productname='';
            //查询该服务合同对应的产品
            /*$queryc=" SELECT productname,marketprice,costprice FROM vtiger_activationcode WHERE contractid = ? AND status IN(0,1)";
            $productdatas=$adb->pquery($queryc,array($contractid));
            $marketingPrice=0;
            $costprice=0;
            while ($rowDatas=$adb->fetch_array($productdatas)){
                $productname.=",".$rowDatas['productname'];
                $marketingPrice+=$rowDatas['marketprice'];
                $costprice+=$rowDatas['costprice'];

            }*/
            $insertValueStrArray=array();
            //$queryc="SELECT GROUP_CONCAT(productname) AS productname, SUM(marketprice) AS marketprice, SUM(costprice) AS costprice,(SUM(IF(canrenew=0 AND buyseparately>0,onemarketprice,0))+SUM(IF(buyseparately>0,0,onemarketprice))) AS onemarketprice,(SUM(IF(canrenew=0 AND buyseparately>0,onemarketrenewprice,0))+SUM(IF(buyseparately>0,0,onemarketrenewprice))) AS onemarketrenewprice,(SUM(IF(canrenew=0 AND buyseparately>0,onecostprice,0))+SUM(IF(buyseparately>0,0,onecostprice))) AS onecostprice,(SUM(IF(canrenew=0 AND buyseparately>0,onecostrenewprice,0))+SUM(IF(buyseparately>0,0,onecostrenewprice))) AS onecostrenewprice FROM vtiger_activationcode WHERE contractid = ? AND status IN(0,1)";
           /* $sqls=" SELECT activitytype,orderamount,classtype,productnames,productlife,productname,marketprice,costprice,canrenew,buyseparately,onemarketprice,onemarketrenewprice,onecostprice,onecostrenewprice FROM  vtiger_activationcode WHERE contractid = ? AND status IN(0,1) ";
            $results=$adb->pquery($sqls,array($contractid));*/
            $productdatas =$Matchreceivements_Record_Model->getTyunBasicInformation($contractid);//取基础价格信息

           /* while ($dtaRows=$adb->fetch_array($results)){
                $dtaRows['onecostprice']=$dtaRows['onecostprice']<0?0:$dtaRows['onecostprice'];//首购成本价
                $dtaRows['onemarketprice']=$dtaRows['onemarketprice']<0?0:$dtaRows['onemarketprice'];//首购市场价
                $dtaRows['costprice']=$dtaRows['costprice']<0?0:$dtaRows['costprice'];//总成本价
                $dtaRows['onemarketrenewprice']=$dtaRows['onemarketrenewprice']<0?0:$dtaRows['onemarketrenewprice'];//续费市场价
                $dtaRows['onecostrenewprice']=$dtaRows['onecostrenewprice']<0?0:$dtaRows['onecostrenewprice'];//续费成本价
                $productdatas['productname'].=$dtaRows['productname'].",";

                if($dtaRows['classtype']=='renew' && $dtaRows['productlife']>1){//如果是续费，将续费的
                    $productlife=$dtaRows['productlife']-1;
                    $dtaRows['onemarketprice']=$dtaRows['onemarketrenewprice']*$productlife;
                    $dtaRows['onecostprice']=$dtaRows['onecostrenewprice']*$productlife;
                }
                $productdatas['marketprice']+=$dtaRows['marketprice'];//总市场成本价（首购市场价+续费市场价*（n-1））
                $productdatas['costprice']+=$dtaRows['costprice'];//总成本价（首购成本价+续费成本价*（n-1））
                $productdatas['onemarketrenewprice']+=$dtaRows['onemarketrenewprice']*($dtaRows['productlife']-1);
                $productdatas['onecostrenewprice']+=$dtaRows['onecostrenewprice']*($dtaRows['productlife']-1);
                if($dtaRows['buyseparately']>0){
                    $productnames=$dtaRows['productnames'];
                    $productnames=htmlspecialchars_decode($productnames);
                    $separatelyProducts=json_decode($productnames,true);
                    $productdatas['onemarketprice']+=$separatelyProducts[0]['productCount']*$dtaRows['onemarketprice'];
                    $productdatas['onecostprice']+=$separatelyProducts[0]['productCount']*$dtaRows['onecostprice'];
                    // 如果能续费
                    if($separatelyProducts[0]['canRenew']){
                        //升级市场价格取值
                        if($dtaRows['activitytype']=='赠送时间'){
                            $productdatas['upgrademarketprice']+=$dtaRows['marketprice'];
                            $productdatas['upgradecostprice']+=$dtaRows['costprice'];
                        }else{
                            $productdatas['upgrademarketprice']+=($dtaRows['onemarketprice']+$dtaRows['onemarketrenewprice']*($dtaRows['productlife']-1))*$separatelyProducts[0]['productCount'];// 升级用到的市场价格
                            $productdatas['upgradeonecostrenewprice']+=($dtaRows['onemarketrenewprice']*($dtaRows['productlife']-1))*$separatelyProducts[0]['productCount'];// 升级用到的市场价格
                            //升级成本取值
                            $productdatas['upgradecostprice']+=($dtaRows['onecostprice']+$dtaRows['onecostrenewprice']*($dtaRows['productlife']-1))*$separatelyProducts[0]['productCount'];// 升级用到的总成本
                            $productdatas['upgradeonecostrenewprice']+=($dtaRows['onecostprice']+$dtaRows['onecostrenewprice']*($dtaRows['productlife']-1))*$separatelyProducts[0]['productCount'];// 升级用到的总成本
                        }

                    // 不能续费
                    }else{
                        //升级市场价格取值
                        if($dtaRows['activitytype']=='赠送时间'){
                            $productdatas['upgrademarketprice']+=$dtaRows['marketprice'];
                            $productdatas['upgradecostprice']+=$dtaRows['costprice'];
                        }else {
                            //升级市场价格取值
                            $productdatas['upgrademarketprice'] += $dtaRows['onemarketprice'] * $separatelyProducts[0]['productCount'];// 升级用到的市场价格
                            //升级成本取值
                            $productdatas['upgradecostprice'] += $dtaRows['onecostprice'] * $separatelyProducts[0]['productCount'];// 升级用到的总成本
                        }
                    }
                }else{
                    if($dtaRows['activitytype']=='赠送时间'){
                        //升级市场价格取值
                        $productdatas['upgrademarketprice']+=$dtaRows['marketprice'];// 升级用到的市场价格
                        //升级成本取值
                        $productdatas['upgradecostprice']+=$dtaRows['costprice'];// 升级用到的总成本
                    }else{
                        //升级市场价格取值
                        $productdatas['upgrademarketprice']+=$dtaRows['onemarketprice']+$dtaRows['onemarketrenewprice']*($dtaRows['productlife']-1);// 升级用到的市场价格
                        //升级成本取值
                        $productdatas['upgradecostprice']+=$dtaRows['onecostprice']+$dtaRows['onecostrenewprice']*($dtaRows['productlife']-1);// 升级用到的总成本
                    }

                    $productdatas['onemarketprice']+=$dtaRows['onemarketprice'];
                    $productdatas['onecostprice']+=$dtaRows['onecostprice'];

                }
            }*/
            $productdatas['productname']=trim($productdatas['productname'],',');
            //$productdatas=$adb->query_result_rowdata($productdatas,0);
            $productname=$productdatas['productname'];//产品名称
            $marketingPrice=$productdatas['marketprice'];//总市场价
            $costprice=$productdatas['costprice'];//总成本
            $onemarketprice=$productdatas['onemarketprice'];//首购市场价
            $onecostprice=$productdatas['onecostprice'];//首购成本价
            $onemarketrenewprice=$productdatas['onemarketrenewprice'];//续费市价
            $onecostrenewprice=$productdatas['onecostrenewprice'];//续费成本价
            $renewalBase=10;//续费提成起步点
            //@todo------
            $rp['marketprice']=$marketingPrice;//总市场价
            //总业绩市场价格（升级时用到的）原合同剩余+ 业绩市场价// cxh2020-08-06 以前的升级市场价格取值
            $allmarketingPrice=$contractInfo['upgradetransfer']+$rp['marketprice'];
            $modulestatus='a_normal';
            //回款时间 即入账日期
            $reality_date=$rp['reality_date'];
            $matchdate=$rp['matchdatetime'];
            //匹配时间
            if(!empty($matchdate)){
                $matchdate=$matchdate;
            }else{
                $matchdate=date('Y-m-d');
            }

            //T云系列 业绩日期计算
            $achievementmonth=$this->getAchievementmonth($reality_date,$matchdate);
            //成本扣除数
            $costdeduction=$costprice;
            //获取是新单还是续费业绩
            $paramers['contractid']=$contractid;//合同ID
            $paramers['contract_type']=$rp['contract_type'];//合同类型新单，续费，升级
            $paramers['parent_contracttypeid']=$rp['parent_contracttypeid'];
            $paramers['contract_no']=$rp['contract_no'];
            $paramers['servicecontractstype']=$rp['servicecontractstype'];
            $paramers['activationcodeid']=$rp['activationcodeid'];
            $paramers['usercode']=$rp['usercode'];
            $paramers['customerid']=$rp['customerid'];
            $currentContractTotal=$paramers['total']=$rp['total'];
            $paramers['productlife']=$rp['productlife'];
            $paramers['renewmarketprice']=$rp['renewmarketprice'];
            $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
            $dataResult=$Matchreceivements_Record_Model->renewOrNewadd($paramers);
            $renewing=$dataResult['renewing'];
            //print_r($dataResult);
            //$dataResult=
            //array("achievementtype"=>新单，续费,
            //"arriveachievement"=>‘续费到账业绩’
            //,'type'=>使用老账号 老账号 就是判断原定单里的产品是否过期1，0,
            //'remark'=>备注,'date'=>“到期的天数，>90或小<-90”,
            //'updateachievementtype'=>1,0老客户不再继续使用的情况下 即用了新账户开了单子);
            $achievementtype=$dataResult['achievementtype'];
            // 这个是合同金额
            $generatedamount=$dataResult['arriveachievement'];//续费的前三后三判断
            $dateBusiness=$dataResult['date'];
            $type=$dataResult['type'];
            if($type==1){//前三后三
                $remark=$dataResult['remark'];
                $rp['total']=$generatedamount;//算业绩的合同金额
            }else{
                $remark=$dataResult['remark'];
            }
            //人力成本   工单外采成本  额外成本
            if($rp['multitype']==1){
                $queryc="SELECT IFNULL(sum(vtiger_salesorderproductsrel.costing),0) AS costing,IFNULL(sum(vtiger_salesorderproductsrel.purchasemount),0) AS purchasemount,IFNULL(sum(vtiger_salesorderproductsrel.extracost),0) AS extracost FROM vtiger_salesorderproductsrel,vtiger_salesorderrayment WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND vtiger_salesorderrayment.receivedpaymentsid=? AND vtiger_salesorderproductsrel.salesorderid=vtiger_salesorderrayment.salesorderid AND multistatus=3 ";
                $costingdata=$adb->pquery($queryc,array($contractid,$receivepayid));
            }else{
                $queryc="SELECT IFNULL(sum(vtiger_salesorderproductsrel.costing),0) AS costing,IFNULL(sum(vtiger_salesorderproductsrel.purchasemount),0) AS purchasemount,IFNULL(sum(vtiger_salesorderproductsrel.extracost),0) AS extracost FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND multistatus=3 ";
                $costingdata=$adb->pquery($queryc,array($contractid));
            }
            $costingdata=$adb->query_result_rowdata($costingdata,0);
            // 有关 沙龙 外采 媒介充值 其他
            $queryc="SELECT extra_type,extra_price FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid=?";
            $otherdata=$adb->pquery($queryc,array($receivepayid));
            $otherdata=$adb->query_result_rowdata($otherdata,0);
            $otherDataTypeArray=array();
            foreach ($otherdata as $key=>$val){
                $otherDataTypeArray[$val['extra_type']]=$otherDataTypeArray[$val['extra_type']]+$val['extra_price'];
            }
            //该服务合同回款相关的总的之和
            $queryc="SELECT SUM(extra_price) as extra_price FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid =? ";
            $otherdatas=$adb->pquery($queryc,array($receivepayid));
            $otherdatas=$adb->query_result_rowdata($otherdatas,0);
            //查询该服务合同分成人
            $queryc=" SELECT vtiger_servicecontracts_divide.*,vtiger_departments.departmentname FROM vtiger_servicecontracts_divide  LEFT JOIN vtiger_departments ON  vtiger_departments.departmentid=vtiger_servicecontracts_divide.signdempart WHERE vtiger_servicecontracts_divide.servicecontractid = ?";
            $resultdatas=$adb->pquery($queryc,array($contractid));
            $insertValueStr='';
            $i=1;
            //已防止 备注重复
            $remarks=$remark;
            //查询原合同所有到账业绩和 以及原单是否是多年单
            //$sqlquery=" SELECT SUM(arriveachievement) as oldallarriveachievement FROM vtiger_achievementallot_statistic WHERE servicecontractid =?  ";
            $sqlquery=" SELECT more_years_renew,SUM(IF(achievementtype='newadd',1,0)) as ishasnewadd,SUM(IF(achievementtype='newadd',arriveachievement,0)) as newaddoldallarriveachievement,SUM(IF(achievementtype='newadd',1,0)) as ishasrenew,SUM(IF(achievementtype='renew',arriveachievement,0)) as renewoldallarriveachievement FROM vtiger_achievementallot_statistic WHERE servicecontractid =? ";
            $oldarriveachievement=$adb->pquery($sqlquery,array($rp['oldcontractid']));
            $oldarriveachievement=$adb->query_result_rowdata($oldarriveachievement,0);
            //删除该回款已扣减记录防止重新匹配不扣减
            $adb->pquery("DELETE  FROM  vtiger_oldachievement_hasdeduction WHERE receivedpaymentsid= ? ",array($receivepayid));
            //查询到该回款已经新单原业绩扣减了多少  （每次回款计算完都会以回款id合同id存储记录 已经扣减了多少）
            $alreadydeduction=" SELECT deductionmoney FROM vtiger_oldachievement_hasdeduction WHERE servicecontractsid=? AND achievementtype='newadd'  ORDER BY  id DESC LIMIT 1  ";
            $alreadydeduction=$adb->pquery($alreadydeduction,array($contractid));
            $newaddalreadydeduction=$adb->query_result_rowdata($alreadydeduction,0);
            //查询到该回款已经续费原业绩扣减了多少 （每次回款计算完都会以回款id和合同id存储记录 已经扣减了多少）
            $alreadydeduction=" SELECT deductionmoney FROM vtiger_oldachievement_hasdeduction WHERE servicecontractsid=? AND achievementtype='renew' ORDER BY  id DESC  LIMIT 1 ";
            $alreadydeduction=$adb->pquery($alreadydeduction,array($contractid));
            $renewalreadydeduction=$adb->query_result_rowdata($alreadydeduction,0);
            //剩余未扣减金额为
            //$lastdeduction=$oldarriveachievement['oldallarriveachievement']-$alreadydeduction;
            //  新单剩余未扣减
            $newAddLastDeduction=$oldarriveachievement['newaddoldallarriveachievement']-$newaddalreadydeduction['deductionmoney'];
            //  续费剩余未扣减
            $renewLastDeduction=$oldarriveachievement['renewoldallarriveachievement']-$renewalreadydeduction['deductionmoney'];
            $deductionremark='扣减备注';

            /*//如果已经扣减金额小于0则需要再扣减金额=0
            if($lastdeduction>0){
                $remainingamount['oldallarriveachievement']=$lastdeduction;
                $deductionremark.="剩余扣减金额大于0";
            }else{
                $deductionremark.="剩余扣减金额等于0";
                $remainingamount['oldallarriveachievement']=0;
            }*/
            $isUpgradeAndDeduction=0;// 是否是升级 且走了扣减逻辑 0 否 没有走扣减逻辑
            $isChange=1; // 到最后是否修改业绩负值为0   1修改为 0 不修改为零
            //$total//本次回款金额
            $isrenewflag=0;//纯续费单是否要拆单
            $waitsubarriveachievement=0;
            $alreadyarriveachievement=0;
            while ($rowDatas=$adb->fetch_array($resultdatas)){
                $remark=$remarks;
                /*  续费类型  if($i>1 && $rp['classtype']=='renew'){
                    break;
                }*/
                $scalling=$rowDatas['scalling'];
                $businessunit=$total*($scalling/100);//分成后的有回款
                $receivedpaymentownid=$rowDatas['receivedpaymentownid'];//分成人ID
                $i++;
                // 如果是续费 直接改成分成人为客户负责人
                //if($rp['classtype']=='renew'){
                    //查询客户负责人
                //$sql="SELECT smownerid FROM vtiger_crmentity WHERE  crmid=? ";
                //$crmentity=$adb->pquery($sql,array($rp['customerid']));
                //$crmentity=$adb->query_result_rowdata($crmentity,0);
                //$receivedpaymentownid=$crmentity['smownerid'];
                    //$rowDatas['receivedpaymentownid']=$crmentity['smownerid'];
                    //$scalling=100;
                    //$remark.="续费单子，";
                //}
                // 查询分成人 所在部门  以及属事业部查询
                $queryc=" SELECT d.departmentid,d.departmentname,d.parentdepartment,u.last_name,u.personnelpositionid FROM vtiger_user2department ud  LEFT JOIN vtiger_departments as d  ON ud.departmentid=d.departmentid LEFT JOIN  vtiger_users as u ON  u.id=ud.userid  WHERE  ud.userid= ? LIMIT 1 ";
                $resultdataDepartment=$adb->pquery($queryc,array($receivedpaymentownid));
                $Department=$adb->query_result_rowdata($resultdataDepartment,0);
                // 如果老账号前三后三 则 走下面 员工类别判定 然后再确认是新单 还是续费业绩
                if(false && $dataResult['updateachievementtype']==1){
                    // 如果是商务下单
                    if ($Department['personnelpositionid']==10071){
                        $remark.="商务下单";
                        $achievementtype='newadd';
                        // 如果是客服下单
                    }if($Department['personnelpositionid']==10069){
                        if($rp['servicecontractstype']=='新增'){
                            $remark.="客服下单新增";
                            $achievementtype='newadd';
                        }else{
                            $remark.="客服下单非新增";
                            $achievementtype='renew';
                        }
                    }
                }
                // 由于 2020 年 7月三号 包含三号 以前下的单子 不拆单。以后下的单子拆单处理 所以加了个时间判定条件  当前是否是多年单
                if($rp['productlife']>1 && $achievementtype=='newadd' && $rp['createdtime']>'2020-07-04'){
                    $isMoreYears=1;// 当前单子多年单
                }else{
                    $isMoreYears=0;
                }
                $departmentInfo=$Matchreceivements_Record_Model->getDepartmentInfo($Department);
                $groupname=$departmentInfo['groupname'];
                $departmentname=$departmentInfo['departmentname'];
                /*$departmentGradeArray= explode("::",$Department['parentdepartment']);
                $countDepartmentGradeArray=count($departmentGradeArray);
                if($countDepartmentGradeArray>3){
                    if($countDepartmentGradeArray==4){
                        $groupname=$departmentname;
                        $departmentname='';
                    }else{// 目前一定是五级部门 如果销售部门级别超过5级了 要根据需求改else里的代码 获取对应级别的
                        $str="::".$Department['departmentid'];
                        $Department['parentdepartment']= str_replace($str,"",$Department['parentdepartment']);
                        $parentdepartment = explode("::",$Department['parentdepartment']);
                        $parentdepartmentId = end($parentdepartment);
                        //查询父类
                        $queryc=" SELECT departmentname FROM vtiger_departments  WHERE  departmentid = ?  limit 1 ";
                        $resultdataDepartment=$adb->pquery($queryc,array($parentdepartmentId));
                        $Departments=$adb->query_result_rowdata($resultdataDepartment,0);
                        $groupname=$Departments['departmentname'];//四级部门
                    }
                }else{
                    $groupname='';
                    $departmentname='';
                }*/

                $costing=0;
                $purchasemount=0;
                $extracost = 0;
                // 重新计算沙龙  媒体外采  没接充值  和   其他 （其他包含所有之和）
                $salong=$otherDataTypeArray['沙龙']*($scalling/100);
                $waici=$otherDataTypeArray['外采']*($scalling/100);
                $meijai=$otherDataTypeArray['媒介充值']*($scalling/100);
                //  other 指 回款（沙龙外采媒体充值其他的总和）
                $othercost=$otherdatas['extra_price'];
                //工单成本合计
                $worksheetcost=0;
                $divideworksheetcost=0;
                $dividecosting=0;
                $dividepurchasemount=0;
                $divideextracost=0;
                $divideother=$othercost*($scalling/100);//分成后的成本
                $other=$othercost;

                //到账业绩
                $arriveachievement=0;
                // 已分成回款
                $unit_prices=$rp['unit_price']*($scalling/100);//分成的回款
                // 为了处理续费的 所以加了个倍数 默认1 续费的处理中有倍数是0.5的
                $RoyaltyMultiplie=1;
                //公式二到账业绩 (升级（T云系列）)
                if(in_array($rp['servicecontractstype'],array('upgrade'))){
                    $sqlQuery="SELECT 1 FROM vtiger_activationcode WHERE `status` in(0,1) AND contractid=? AND oldproductname LIKE '%直客%'";
                    $tempResult=$adb->pquery($sqlQuery,array($contractid));
                    if($adb->num_rows($tempResult)){
                        $achievementtype='renew';
                        $isMoreYears=0;
                        $remark.='直客升级直接按续费核算';
                    }
                    //查询原合同订单过期时间
                    $sqlquery=" SELECT a.renewmarketprice,a.expiredate,a.productlife,s.total,s.signid as oldsignid FROM vtiger_activationcode as a LEFT JOIN vtiger_servicecontracts as s ON s.servicecontractsid=a.contractid WHERE a.contractid =? AND a.productid>0 AND  a.status IN(0,1) LIMIT 1 ";
                    $oldexpiredate=$adb->pquery($sqlquery,array($rp['oldcontractid']));
                    $oldexpiredate=$adb->query_result_rowdata($oldexpiredate,0);
                    $currentTime=date("Y-m-d H:i:s");
                    $date=(strtotime($currentTime)-strtotime($oldexpiredate['expiredate']))/86400;
                    // 升级转换市场价格
                    if(!empty($productdatas['upgrademarketprice'])){
                        $rp['marketprice']=$productdatas['upgrademarketprice'];
                    }else{
                        // 这个是废弃的 以防上面没有连算都没得算 ~~
                        $rp['marketprice']=$allmarketingPrice;
                    }
                    // 升级的成本扣除数转化
                    $costdeduction=$productdatas['upgradecostprice'];
                    //已分成成本扣除数
                    $dividecostdeduction=$costdeduction*$scalling/100;
                    // 原合同金额
                    $oldContractTotal=$adb->pquery("  SELECT  total  FROM  vtiger_servicecontracts  WHERE servicecontractsid=?  ",array($rp['oldcontractid']));
                    $oldContractTotal=$adb->query_result_rowdata($oldContractTotal,0);
                    //判断是否过期 原合同订单过期时间 和 新合同订单开始时间 比较  距离到期超过3个月以上
                    if($date<-90){
                        $isUpgradeAndDeduction=1;
                        // 查询已经扣减原合同
                        if($rp['oldcontract_usedtime']<30){
                            $oldarriveachievement=0;
                            $remark.=' T云升级 距离到期超过90天使用时间小于三十天';
                            $deductionremark.="使用时间小于30天";
                            $newAddLastDeduction=0;// 使用时间小于30天不扣减
                            $renewLastDeduction=0;//  使用时间小于30天不扣减
                            $bukoujianyuanyeji=1;
                        }else{
                            $bukoujianyuanyeji=0;
                            $deductionremark.="使用时间大于30天";
                            //要修改的地方
                            //$oldarriveachievement=$oldarriveachievement['oldallarriveachievement']*$rp['oldcontract_usedtime']/(365*$oldexpiredate['productlife']);
                            //$oldarriveachievement=$remainingamount['oldallarriveachievement'];
                            $remark.='T云升级距离到期超过90天使用时间大于等于三十天';
                        }
                        $contractInfo['contractamount']=$rp['total']+$oldContractTotal['total'];
                        if($isMoreYears==1){
                            $deductionremark.="多年单";
                            // 原单是多年单
                            if($oldarriveachievement['more_years_renew']==1){
                                // 查询是否回款回完了
                                $allunitprice=$adb->pquery(" SELECT  SUM(unit_price)  as  allunitprice  FROM  vtiger_receivedpayments  WHERE relatetoid=? and ismatchdepart=1 ",array($contractid));
                                $allunitprice=$adb->query_result_rowdata($allunitprice,0);
                                if($allunitprice['allunitprice']>=$currentContractTotal){
                                     $isChange=0;
                                }
                                $listAchievementType=1;// 原 拆单了（买了多年） 新 拆单了（买了多年）
                                // $inparams['contractamount']=$contractInfo['contractamount'];// 合同总金额  注释掉上版本合同金额取值
                                $inparams['contractamount']=$rp['total']+$oldContractTotal['total'];// 总的合同金额
                                $inparams['effectiveTotal']=$buysplitcontractamount=$onemarketprice;// 计算收购单时合同金额=首购市场价格
                                $inparams['marketprice']=$buysplitmarketprice=$onemarketprice;//    首购市场价格
                                $inparams['costdeduction']=$buysplitcost=$onecostprice;//  首购成本
                                $inparams['unit_price']=$rp['unit_price'];// 总回款金额
                                $inparams['total']=$currentContractTotal;//
                                $inparams['oldarriveachievement']=$newAddLastDeduction;// 新单剩余未减原到账业绩
                                //首购单
                                $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaFour($inparams);
                                $buyArriveachievement=$resultInfo['arriveachievement'];
                                $remain['newAddLastDeduction']=$buyArriveachievement>=0?0:-$buyArriveachievement;

                                //首单减过后剩余没减原到账业绩
                                $inparams['oldarriveachievement']=$renewLastDeduction;  //
                                $buyRemark=$resultInfo['remark'];
                                //续费单业绩计算参数处理
                                $inparams['effectiveTotal']=$renewsplitcontractamount=$inparams['contractamount']-$onemarketprice;//
                                $inparams['marketprice']=$renewsplitmarketprice=$rp['marketprice']-$onemarketprice; //
                                $inparams['costdeduction']=$renewsplitcost=($costdeduction-$onecostprice)>0?($costdeduction-$onecostprice):0; //
                                $deductionremark.='剩余未减续费金额'.$renewLastDeduction.'续费有效合同金额'.$inparams['effectiveTotal']."续费市场价格".$inparams['marketprice']."续费成本扣除数".$inparams['costdeduction'];
                                $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaFour($inparams);
                                $renewArriveachievement=$resultInfo['arriveachievement'];
                                $remain['renewLastDeduction']=$renewArriveachievement>=0?0:-$renewArriveachievement;
                                $renewRemark=$resultInfo['remark'];
                                // 如果续费剩余为零
                                if($remain['renewLastDeduction']==0 && $remain['newAddLastDeduction']>0 && $renewArriveachievement>0 && $bukoujianyuanyeji!=1){
                                    if($renewArriveachievement-$remain['newAddLastDeduction']>0){
                                        $renewArriveachievement=$renewArriveachievement-$remain['newAddLastDeduction'];
                                        $remain['newAddLastDeduction']=0;
                                    }else{
                                        $remain['newAddLastDeduction']=$remain['newAddLastDeduction']-$renewArriveachievement;
                                        $renewArriveachievement=0;
                                    }
                                }
                                // 如果续费剩余为零
                                if($remain['newAddLastDeduction']!=0 && $remain['renewLastDeduction']>0 && $buyArriveachievement>0 && $bukoujianyuanyeji!=1){
                                    if($buyArriveachievement-$remain['renewLastDeduction']>0){
                                        $buyArriveachievement=$buyArriveachievement-$remain['renewLastDeduction'];
                                        $remain['renewLastDeduction']=0;
                                    }else{
                                        $remain['renewLastDeduction']=$remain['renewLastDeduction']-$buyArriveachievement;
                                        $buyArriveachievement=0;
                                    }
                                }
                            //原单不是多年单
                            }else{
                                // 原单是新单业绩类型
                                if($oldarriveachievement['ishasnewadd']>0){
                                    $listAchievementType=2;//  原 没有拆单（买了1年） 原业绩类型 新单     新 拆单了（买了多年）
                                    // $inparams['contractamount']=$contractInfo['contractamount'];// 合同总金额  注释掉上版本合同金额取值
                                    $inparams['contractamount']=$rp['total']+$oldContractTotal['total'];// 总的合同金额
                                    $inparams['effectiveTotal']=$buysplitcontractamount=$onemarketprice;// 计算收购单时合同金额=首购市场价格
                                    $inparams['marketprice']=$buysplitmarketprice=$onemarketprice;//    首购市场价格
                                    $inparams['costdeduction']=$buysplitcost=$onecostprice;//  首购成本
                                    $inparams['unit_price']=$rp['unit_price'];// 总回款金额
                                    $inparams['total']=$currentContractTotal;
                                    $inparams['oldarriveachievement']=$newAddLastDeduction;
                                    //  首购单
                                    $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaFour($inparams);
                                    $buyArriveachievement=$resultInfo['arriveachievement'];
                                    //首单减过后剩余没减原到账业绩
                                    $inparams['oldarriveachievement']=$buyArriveachievement>=0?0:-$buyArriveachievement;
                                    $buyRemark=$resultInfo['remark'];
                                    //续费单业绩计算参数处理
                                    $inparams['effectiveTotal']=$renewsplitcontractamount=$inparams['contractamount']-$onemarketprice;
                                    $inparams['marketprice']=$renewsplitmarketprice=$rp['marketprice']-$onemarketprice;
                                    $inparams['costdeduction']=$renewsplitcost=($costdeduction-$onecostprice)>0?($costdeduction-$onecostprice):0;
                                    $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaFour($inparams);
                                    $renewArriveachievement=$resultInfo['arriveachievement'];
                                    $remain['newAddLastDeduction']=$renewArriveachievement>=0?0:-$renewArriveachievement;
                                    $renewRemark=$resultInfo['remark'];
                                // 原单是续费业绩类型
                                }else{
                                    $listAchievementType=3;//  原 没有拆单（买了1年） 原业绩类型 续费     新 拆单了（买了多年）
                                    // $inparams['contractamount']=$contractInfo['contractamount'];// 合同总金额  注释掉上版本合同金额取值
                                    $inparams['contractamount']=$rp['total']+$oldContractTotal['total'];// 总的合同金额
                                    $inparams['effectiveTotal']=$buysplitcontractamount=$onemarketprice;// 计算收购单时合同金额=首购市场价格
                                    $inparams['marketprice']=$buysplitmarketprice=$onemarketprice;//    首购市场价格
                                    $inparams['costdeduction']=$buysplitcost=$onecostprice;//  首购成本
                                    $inparams['unit_price']=$rp['unit_price'];// 总回款金额
                                    $inparams['total']=$currentContractTotal;
                                    $inparams['oldarriveachievement']=0;
                                    //首购单
                                    $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaFour($inparams);
                                    $buyArriveachievement=$resultInfo['arriveachievement'];
                                    //首单减过后剩余没减原到账业绩
                                    $inparams['oldarriveachievement']=$renewLastDeduction;
                                    $buyRemark=$resultInfo['remark'];
                                    //续费单业绩计算参数处理
                                    $inparams['effectiveTotal']=$renewsplitcontractamount=$inparams['contractamount']-$onemarketprice;
                                    $inparams['marketprice']=$renewsplitmarketprice=$rp['marketprice']-$onemarketprice;
                                    $inparams['costdeduction']=$renewsplitcost=($costdeduction-$onecostprice)>0?($costdeduction-$onecostprice):0;
                                    $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaFour($inparams);
                                    $renewArriveachievement=$resultInfo['arriveachievement'];
                                    $remain['renewLastDeduction']=$renewArriveachievement>=0?0:-$renewArriveachievement;
                                    $renewRemark=$resultInfo['remark'];
                                    // 如果续费 没减完 新单去减
                                    if($remain['renewLastDeduction']>0 && $bukoujianyuanyeji!=1){
                                        $buyArriveachievement=$buyArriveachievement-$remain['renewLastDeduction'];
                                        $remain['renewLastDeduction']=$buyArriveachievement>=0?0:-$buyArriveachievement;
                                    }
                                }
                            }
                        }else{
                            // 原  新单 全是单年单 不管业绩类型 直接减原业绩
                            //如果新单时新单业绩
                            /*if($achievementtype=='newadd'){*/
                            $listAchievementType=4;//  单年
                            $deductionremark.="正常单";
                            $inparams['contractamount']=$rp['total']+$oldContractTotal['total'];
                            $inparams['effectiveTotal']=$rp['total']+$oldContractTotal['total'];
                            $inparams['marketprice']=$rp['marketprice'];//
                            $inparams['costdeduction']=$costdeduction;//
                            $inparams['unit_price']=$rp['unit_price'];//  总回款金额
                            $inparams['total']=$currentContractTotal;
                            $inparams['oldarriveachievement']=$newAddLastDeduction+$renewLastDeduction;
                            $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaFour($inparams);
                            $arriveachievement=$resultInfo['arriveachievement'];
                            $remain['allAddLastDeduction']=$arriveachievement>=0?0:-$arriveachievement;
                            $remark.=$resultInfo['remark']."合同金额";
                            /*}else{
                                $listAchievementType=5;// 单年
                                $deductionremark.="正常单";
                                $inparams['contractamount']=$rp['total']+$oldContractTotal['total'];
                                $inparams['effectiveTotal']=$rp['total']+$oldContractTotal['total'];
                                $inparams['marketprice']=$rp['marketprice'];//
                                $inparams['costdeduction']=$costdeduction;//
                                $inparams['unit_price']=$rp['unit_price'];//  总回款金额
                                $inparams['total']=$currentContractTotal;
                                $inparams['oldarriveachievement']=$renewLastDeduction;
                                $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaFour($inparams);
                                $arriveachievement=$resultInfo['arriveachievement'];
                                $remain['renewLastDeduction']=$arriveachievement>=0?0:-$arriveachievement;
                                $remark.=$resultInfo['remark']."合同金额";
                            }*/

                        }
                        // 到期3个月以上的  过期后升级
                    }else if($date>90){
                        if($isMoreYears==1){
                            $inparams['contractprice']=$onemarketprice;// 合同金额
                            $inparams['contractamount']=$contractInfo['contractamount'];// 合同总金额 + 原合同剩余金额
                            $inparams['effectiveTotal']=$buysplitcontractamount=$onemarketprice;// 计算收购单时合同金额=首购市场价格
                            $inparams['marketprice']=$buysplitmarketprice=$onemarketprice;//    首购市场价格
                            $inparams['costdeduction']=$buysplitcost=$onecostprice;//  首购成本
                            $inparams['unit_price']=$rp['unit_price'];//  总回款金额
                            //  首购单
                            $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaThree($inparams);
                            $buyArriveachievement=$resultInfo['arriveachievement'];
                            $buyRemark=$resultInfo['remark'];
                            //  续费单业绩
                            $inparams['contractprice']=$contractInfo['contractprice']-$onemarketprice;
                            $inparams['effectiveTotal']=$renewsplitcontractamount=$inparams['contractamount']-$onemarketprice;
                            $inparams['marketprice']=$renewsplitmarketprice=$rp['marketprice']-$onemarketprice;
                            $inparams['costdeduction']=$renewsplitcost=($costdeduction-$onecostprice)>0?($costdeduction-$onecostprice):0;
                            $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaThree($inparams);
                            $renewArriveachievement=$resultInfo['arriveachievement'];
                            $renewRemark=$resultInfo['remark'];
                        }else{
                            $inparams['contractprice']=$contractInfo['contractprice'];
                            $inparams['contractamount']=$contractInfo['contractamount'];// 合同总金额
                            $inparams['effectiveTotal']=$contractInfo['contractamount'];//
                            $inparams['marketprice']=$rp['marketprice'];//
                            $inparams['costdeduction']=$onecostprice;//  非多年直接首购成本
                            $inparams['unit_price']=$rp['unit_price'];//
                            $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaThree($inparams);
                            $arriveachievement=$resultInfo['arriveachievement'];
                            $remark.=$resultInfo['remark'];
                        }
                    //到期前3个月内，到期后3个月内。
                    }else{
                        // 前三后三特殊处理合同金额
                        $generatedamount=$rp['contractprice']-$oldexpiredate['renewmarketprice']*$rp['productlife'];
                        $contractInfo['contractamount']=$generatedamount+$oldContractTotal['total'];
                        if($isMoreYears==1){
                            $inparams['contractamount']=$contractInfo['contractamount'];// 合同总金额
                            $inparams['effectiveTotal']=$buysplitcontractamount=$onemarketprice;// 计算收购单时合同金额=首购市场价格
                            $inparams['marketprice']=$buysplitmarketprice=$onemarketprice;//    首购市场价格
                            $inparams['costdeduction']=$buysplitcost=$onecostprice;//  首购成本
                            $inparams['unit_price']=$rp['unit_price'];//  总回款金额
                            $inparams['total']=$currentContractTotal;
                            $inparams['oldarriveachievement']=0;

                            // 首购单
                            $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaTwo($inparams);
                            $buyArriveachievement=$resultInfo['arriveachievement'];
                            //首单减过后剩余没减原到账业绩
                            $inparams['oldarriveachievement']=$remainingamount['oldallarriveachievement']=$buyArriveachievement>=0?0:-$buyArriveachievement;
                            $buyRemark=$resultInfo['remark'];
                            //续费单业绩
                            $inparams['effectiveTotal']=$renewsplitcontractamount=($inparams['contractamount']-$onemarketprice)>0?($inparams['contractamount']-$onemarketprice):0;
                            $inparams['marketprice']=$renewsplitmarketprice=$rp['marketprice']-$onemarketprice;
                            $inparams['costdeduction']=$renewsplitcost=($costdeduction-$onecostprice)>0?($costdeduction-$onecostprice):0;
                            $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaTwo($inparams);
                            $renewArriveachievement=$resultInfo['arriveachievement'];
                            $renewRemark=$resultInfo['remark'];
                        }else{
                            $inparams['contractamount']=$contractInfo['contractamount'];// 合同总金额
                            $inparams['effectiveTotal']=$contractInfo['contractamount'];//
                            $inparams['marketprice']=$rp['marketprice'];//
                            $inparams['costdeduction']=$costdeduction;//
                            $inparams['unit_price']=$rp['unit_price'];//
                            $inparams['total']=$currentContractTotal;
                            $inparams['oldarriveachievement']=0;
                            $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaTwo($inparams);
                            $arriveachievement=$resultInfo['arriveachievement'];
                            $remark.=$resultInfo['remark'];
                        }
                    }
                    $producttype=2;
                    //公式一到账业绩 (T云系列计算)
                }elseif($renewing==1){//前三后三内计算公式
                    $dividecostdeduction=$costdeduction*$scalling/100;
                    $renewsplitcontractamount = 0;
                    $buysplitcontractamount = 0;
                    $renewsplitmarketprice = 0;
                    $buysplitmarketprice = 0;
                    $renewsplitcost = 0;
                    $buysplitcost=0;
                    $TempData=$Matchreceivements_Record_Model->calcTYUNFirstANDLastThreeMonths(array(
                        'noseparaterenewmarketprice'=>$productdatas['noseparaterenewmarketprice'],
                        'noseparaterenewcosttprice'=>$productdatas['noseparaterenewcosttprice'],
                        'separaterenewmarketprice'=>$productdatas['separaterenewmarketprice'],
                        'separaterenewcosttprice'=>$productdatas['separaterenewcosttprice'],
                        'effectiveTotal'=>$rp['total'],
                        'marketprice'=>$rp['marketprice'],
                        'costdeduction'=>$costdeduction,
                        'unit_price'=>$rp['unit_price']
                    ));
                    $remark.=$TempData['remark'];
                    $waitsubarriveachievement=$TempData['waitsubarriveachievement'];
                    $alreadyarriveachievement=$TempData['alreadyarriveachievement'];
                    if($TempData['type']==1){
                        $arriveachievement=$renewArriveachievement=$TempData['renewarriveachievement'];
                    }else{
                        $renewArriveachievement=$TempData['renewarriveachievement'];
                        $buyArriveachievement=$TempData['newaddarriveachievement'];
                        $isMoreYears=1;
                        $isrenewflag=1;
                    }

                }else{
                    //已分成成本扣除数--总成本
                    $dividecostdeduction=$costdeduction*$scalling/100;
                    // 要修改的地方
                    if($rp['classtype']=='renew' OR $rp['classtype']=='degrade'){
                        //查询原合同信息
                        $sqlquery=" SELECT a.expiredate,a.productlife,s.total,s.signid as oldsignid,a.contractid FROM vtiger_activationcode as a LEFT JOIN vtiger_servicecontracts as s ON s.servicecontractsid=a.contractid WHERE a.usercode =? AND classtype='buy' AND a.productid>0 AND a.status IN(0,1)  AND  a.contractid<>?  LIMIT 1 ";
                        $oldContractInfo=$adb->pquery($sqlquery,array($rp['usercode'],$contractid));
                        $oldContractInfo=$adb->query_result_rowdata($oldContractInfo,0);
                        $currentContractTotal1=$rp['total'];//当前的合同金额
                        $currentunitPrice=$rp['unit_price'];//当前的回款金额
                        $currentunitPricediff=0;//当前的合同金额
                        if($isMoreYears==1){//多年单
                            $inparams['total']=$currentContractTotal;// 合同总金额
                            $inparams['effectiveTotal']=$buysplitcontractamount=($rp['total']-$onemarketrenewprice)>0?($rp['total']-$onemarketrenewprice):0;// 计算收购单时合同金额=首购市场价格
                            $inparams['marketprice']=$buysplitmarketprice=$rp['marketprice']-$onemarketrenewprice;//    首购市场价格
                            $inparams['costdeduction']=$buysplitcost=$costdeduction-$onecostprice;//  首购成本
                            $inparams['unit_price']=$rp['unit_price'];//  总回款金额
                            //拆分 首购单到账业绩
                            //如果合同金额小于首购市场价格
                            if(false && $rp['total']<$onemarketprice){
                                /*$inparams['costdeduction']=$costdeduction;
                                $buysplitcost=$costdeduction;
                                $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaRenew($inparams);
                                $buyArriveachievement=$resultInfo['arriveachievement'];
                                $buyRemark=$resultInfo['remark'];
                                // 拆分 续费单到账业绩
                                $renewsplitcontractamount=0;
                                $renewsplitmarketprice=0;
                                $renewsplitcost=0;
                                $renewArriveachievement=0;
                                $renewRemark='续费单被判定多年单业绩合同金额小于首购市场价格 续费业绩为0';*/
                            }else{
                                $discount=$rp['total']/$rp['marketprice']>1?1:$rp['total']/$rp['marketprice'];
                                $TempData=$Matchreceivements_Record_Model->calcTYUNRenewANDNewAddMoreYear(array(
                                    'contractid'=>$contractid,
                                    'receivepayid'=>$receivepayid,
                                    'costdeduction'=>$costdeduction,
                                    'onemarketrenewprice'=>$onemarketrenewprice,
                                    'onecostrenewprice'=>$onecostrenewprice,
                                    'unit_price'=>$rp['unit_price'],
                                    'total'=>$rp['total'],
                                    'marketprice'=>$rp['marketprice'],
                                    'extracost'=>$costingdata['extracost'],
                                    'extra_price'=>$otherdatas['extra_price'],
                                    'discount'=>$discount,
                                ),'getArriveachievementByFormulaOne1');
                                foreach($TempData as $key=>$value){
                                    $$key=$value;
                                }
                            }
                        }elseif($achievementtype=='newadd'){//续费按新单业绩计算
                            $inparams['total']=$currentContractTotal;
                            $inparams['effectiveTotal']=$rp['total'];
                            $inparams['marketprice']=$rp['marketprice'];
                            $inparams['costdeduction']=$costdeduction;
                            $inparams['unit_price']=$rp['unit_price'];
                            $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaOne($inparams);
                            $arriveachievement=$resultInfo['arriveachievement'];
                            $transRemark=$resultInfo['remark'];
                            $remark.=$resultInfo['remark'];
                        }else{//纯续费
                            if($productdatas['othermarketprice']>0){//有另购
                               /* $productdatas['othermarketprice']=0;//另购产品的续费总市场价  新购续费市场价 *（年限-1）
                                $productdatas['othermarketrenewprice']=0;//另购产品的续费总市场价  新购续费市场价 *（年限-1）
                                $productdatas['othercostrenewprice']=0;//另购产品的总成本价
                                $productdatas['othercostaddprice']=0;//另购产品的首购总成本价  另购首购成本
                                $productdatas['renewmarketrenewprice']=0;//续费产品的总成本价  续费市场价*续费年限
                                $productdatas['renewcostrenewprice']=0;//续费产品的总成本价*/
                                $TempData=$Matchreceivements_Record_Model->getArriveachievementByFormulaTYUNReNew(array('contractid'=>$contractid,
                                    'othermarketprice'=>$productdatas['othermarketprice'],
                                    'othermarketrenewprice'=>$productdatas['othermarketrenewprice'],
                                    'othercostrenewprice'=>$productdatas['othercostrenewprice'],
                                    'othercostaddprice'=>$productdatas['othercostaddprice'],
                                    'renewmarketrenewprice'=>$productdatas['renewmarketrenewprice'],
                                    'renewcostrenewprice'=>$productdatas['renewcostrenewprice'],
                                    'receivepayid'=>$receivepayid,
                                    'costdeduction'=>$costdeduction,
                                    'onemarketrenewprice'=>$onemarketrenewprice,
                                    'onecostrenewprice'=>$onecostrenewprice,
                                    'unit_price'=>$rp['unit_price'],//回款总额
                                    'total'=>$rp['total'],//合同金额
                                    'marketprice'=>$rp['marketprice'],));
                                foreach($TempData as $key=>$value){
                                    $$key=$value;
                                }
                                $remark.=$renewRemark;
                                $isMoreYears=1;
                                $isrenewflag=1;
                            }else{
                                $inparams['total']=$rp['total'];
                                $inparams['effectiveTotal']=$rp['total'];
                                $inparams['marketprice']=$rp['marketprice'];
                                $inparams['costdeduction']=$costdeduction;
                                $inparams['unit_price']=$rp['unit_price'];
                                $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaOne($inparams);
                                $arriveachievement=$resultInfo['arriveachievement'];
                                $remark.=$resultInfo['remark'];
                            }

                        }
                        $sqlquery=" SELECT GROUP_CONCAT(receivedpaymentownid) as receivedpaymentownids FROM vtiger_servicecontracts_divide WHERE servicecontractid=? ";
                        $oldContractDivide=$adb->pquery($sqlquery,array($oldContractInfo['contractid']));
                        $oldContractDivide=$adb->query_result_rowdata($oldContractDivide,0);
                        $divideArray=explode(',',$oldContractDivide['receivedpaymentownids']);


                        $sqlquery=" SELECT GROUP_CONCAT(receivedpaymentownid) as receivedpaymentownids FROM vtiger_servicecontracts_divide WHERE servicecontractid=? ";
                        $newContractDivide=$adb->pquery($sqlquery,array($contractid));
                        $newContractDivide=$adb->query_result_rowdata($newContractDivide,0);
                        $newdivideArray=explode(',',$newContractDivide['receivedpaymentownids']);

                        $tmmnewdivideArray=array_intersect($newdivideArray,$divideArray);

                        // 有没有是客户负责人
                        $sql="SELECT smownerid FROM vtiger_crmentity WHERE  crmid=? AND smownerid IN(SELECT receivedpaymentownid FROM vtiger_servicecontracts_divide WHERE servicecontractid =?)";
                        $crmentity=$adb->pquery($sql,array($rp['customerid'],$contractid));
                        $crmentityRows=$adb->num_rows($crmentity);
                        $currentTime=date("Y-m-d",strtotime("-2 months",time()));
                        //判断该客户负责人两个月内有没有变更记录
                        $sql="SELECT count(1) as counts  FROM vtiger_modtracker_basic as mb INNER JOIN vtiger_modtracker_detail as md  ON md.id=mb.id WHERE  mb.crmid=? AND md.fieldname='assigned_user_id' AND LEFT(mb.changedon,10) >= ? ";
                        $result=$adb->pquery($sql,array($rp['sc_related_to'],$currentTime));
                        $result=$adb->query_result_rowdata($result,0);
                        // 查询所有的记录
                        $sql="SELECT * FROM vtiger_modtracker_basic as mb INNER JOIN vtiger_modtracker_detail as md  ON md.id=mb.id WHERE  mb.crmid=? AND md.fieldname='assigned_user_id' ";
                        $allrows=$adb->pquery($sql,array($rp['sc_related_to']));
                        $allrows=$adb->num_rows($allrows,0);
                        //判断当前分成人和原合同分成人是否一致
                        if(!empty($tmmnewdivideArray)){
                            $remark.="（当前分成人和原分成人一致）";

                        }elseif($crmentityRows>0 && $result['counts']<=0 && $allrows>1){
                            $remark.="  （两个月内没有客户负责人变更记录提成给一半但与购买订单不是同一人）";
                            $RoyaltyMultiplie=0.5;
                            //不一致
                        }else{
                            //分成人有没有是客户负责人的
                            $sql=" SELECT *  FROM vtiger_modtracker_basic as mb INNER JOIN vtiger_modtracker_detail as md  ON md.id=mb.id WHERE  mb.crmid=? AND md.fieldname='assigned_user_id' AND  postvalue IN(SELECT receivedpaymentownid FROM vtiger_servicecontracts_divide WHERE servicecontractid =?) ";
                            $numrows=$adb->pquery($sql,array($rp['sc_related_to'],$contractid));
                            $numrows=$adb->num_rows($numrows);
                            if($allrows==1 && !empty($numrows)){
                                $remark.="客户负责人没有变更过";
                                //如果两个月内有变更记录或者两个分成人都不是客户负责人  则不计算业绩
                            }else if($result['counts']>0 || $numrows==0){
                                $remark.="两个月内有变更记录或者两个分成人都不是客户负责人";
                                $buyArriveachievement=0;
                                $renewArriveachievement=0;
                                $arriveachievement=0;
                            //  如果两个月内没有客户负责人变更记录提成给一半
                            }else{
                                $RoyaltyMultiplie=0.5;
                                $remark.="两个月内没有客户负责人变更记录提成给一半";
                            }
                        }

                    }else{
                         // 如果是多年单子 则计算公式计算两种 一次俺首年合同金额和市场价格   一次俺续费合同金额和续费市场价格
                        if($isMoreYears==1){
                            // 如果合同金额小于首购市场价格
                            /* echo $contractInfo['total'],'aaaa',$rp['marketprice'];
                            exit;*/
                            if($rp['total']/$rp['marketprice']>=0.75) {
                                if ($currentContractTotal < $onemarketprice) {
                                    $inparams['total'] = $currentContractTotal;
                                    $inparams['marketprice'] = $buysplitmarketprice = $onemarketprice;
                                    $inparams['costdeduction'] = $buysplitcost = $costdeduction;
                                    $inparams['unit_price'] = $rp['unit_price'];
                                    $resultInfo = $Matchreceivements_Record_Model->getArriveachievementByFormulaZero($inparams);
                                    $buyArriveachievement = $resultInfo['arriveachievement'];

                                    $renewsplitcontractamount = 0;
                                    $renewsplitmarketprice = 0;
                                    $renewsplitcost = 0;
                                    $renewArriveachievement = 0;
                                } else {
                                    /*print_r(array(
                                        'contractid'=>$contractid,
                                        'costdeduction'=>$costdeduction,
                                        'onemarketrenewprice'=>$onemarketrenewprice,
                                        'onecostrenewprice'=>$onecostrenewprice,
                                        'unit_price'=>$rp['unit_price'],
                                        'total'=>$rp['total'],
                                        'marketprice'=>$rp['marketprice'],
                                        'extracost'=>$costingdata['extracost'],
                                        'extra_price'=>$otherdatas['extra_price'],
                                    ));*/
                                    $TempData=$Matchreceivements_Record_Model->calcTYUNRenewANDNewAddMoreYear(array(
                                        'contractid'=>$contractid,
                                        'receivepayid'=>$receivepayid,
                                        'costdeduction'=>$costdeduction,
                                        'onemarketrenewprice'=>$onemarketrenewprice,
                                        'onecostrenewprice'=>$onecostrenewprice,
                                        'unit_price'=>$rp['unit_price'],
                                        'total'=>$rp['total'],
                                        'marketprice'=>$rp['marketprice'],
                                        'extracost'=>$costingdata['extracost'],
                                        'extra_price'=>$otherdatas['extra_price'],
                                    ),'getArriveachievementByFormulaOne1');
                                    foreach($TempData as $key=>$value){
                                        $$key=$value;
                                    }
                                }
                            }else{
                                //小于7.5拆
                                $buyArriveachievement = 0;
                                $renewsplitcontractamount = 0;
                                $buysplitcontractamount = 0;
                                $renewsplitmarketprice = 0;
                                $buysplitmarketprice = 0;
                                $renewsplitcost = 0;
                                $buysplitcost=0;
                                $renewArriveachievement = 0;
                                $buyRemark=' 合同总额/总市场价小于0.75';
                            }

                        }else{
                            $inparams['total']=$currentContractTotal;
                            $inparams['effectiveTotal']=$rp['total'];
                            $inparams['marketprice']=$rp['marketprice'];
                            $inparams['costdeduction']=$costdeduction;
                            $inparams['unit_price']=$rp['unit_price'];
                            $resultInfo=$Matchreceivements_Record_Model->getArriveachievementByFormulaOne($inparams);
                            $arriveachievement=$resultInfo['arriveachievement'];
                            $transRemark=$resultInfo['remark'];
                            $remark.=$resultInfo['remark'];
                        }

                        $remark.="T云非升级非续费订单正常业绩计算公式";
                    }

                    $contractInfo['contractamount']=$rp['total'];
                    $producttype=1;
                }
            $dividetotal=$contractInfo['contractamount']*$scalling/100;
            //如果是多年单
            if($isMoreYears==1){
                 $remark.="多年单";
                 $more_years_renew=1;
                 for ($i=0;$i<2;$i++){
                    // 拆单首购
                    if($i==0){
                        $arriveachievement=$buyArriveachievement;
                        $updateremark=$remark.$buyRemark;
                        $updateachievementtype='newadd';
                        $splitcontractamount=$buysplitcontractamount;
                        $splitmarketprice=$buysplitmarketprice;
                        $splitcost=$buysplitcost;
                        $others=$other;
                        $divideothers=$divideother;
                        $splitbusinessunit=$newaddsplitbusinessunit;
                    // 拆单续费
                    }else if($i==1){
                        $arriveachievement=$renewArriveachievement;
                        $updateremark=$remark.$renewRemark;
                        $updateachievementtype='renew';
                        $splitcontractamount=$renewsplitcontractamount;
                        $splitmarketprice=$renewsplitmarketprice;
                        $splitcost=$renewsplitcost;
                        $others=0;
                        $divideothers=0;
                        $splitbusinessunit=$renewsplitbusinessunit;
                    }
                    //多年单循环拆单。
                     //已分成业绩市场价
                     $dividemarketprice=$rp['marketprice']*$scalling/100;
                     $receivedpaymentown=$Department['last_name'];
                     if(in_array($rp['productid'],array(24,25))){
                         $arriveachievement=$RoyaltyMultiplie*($arriveachievement- $costingdata['extracost'])*$scalling/100;
                     }else{
                         $arriveachievement=$RoyaltyMultiplie*$arriveachievement*$scalling/100;
                     }
                     // 到账业绩减去pos手续费  如果是首年减去手续费
                     if(($i==0 && $buyArriveachievement>0) || ($i==1 && $buyArriveachievement==0 && $renewArriveachievement>0)){
                         $arriveachievement=$arriveachievement-$otherdatas['extra_price']*$scalling/100;
                         $others=$other>0?$other:0;
                         $divideothers=$divideother;
                     }else{
                         $others=0;
                         $divideothers=0;
                     }
                     // 最后判断下到账回款 是否为负值 如果为负 则设为零
                     if($arriveachievement<0 && $isChange==1){ // 是否要修改为零 如果升级且原单拆单，新单拆单回款完了还是负值那么不修改
                         $arriveachievement=0;
                     }
                     // 如果是多年单续费，或者 直接续费单需要计算提成点
                     if($more_years_renew==1 && $updateachievementtype=='renew'){
                         // 查询该客户有几次续费单了
                         //$sql="SELECT count(1) as numbers  FROM	vtiger_activationcode AS a LEFT JOIN vtiger_achievementallot_statistic AS aas ON a.contractid = aas.servicecontractid WHERE	a.usercode =?  AND aas.achievementmonth>0  AND a.contractid <> ? AND ( aas.achievementtype = 'renew' OR ( aas.achievementtype = 'newadd' and  aas.more_years_renew=1 ) )GROUP BY a.contractid";
                         $sql="SELECT count(1) as numbers  FROM	vtiger_activationcode AS a LEFT JOIN vtiger_achievementallot_statistic AS aas ON a.contractid = aas.servicecontractid WHERE	a.usercode =?  AND aas.achievementmonth>0  AND a.contractid <> ? AND ( aas.achievementtype = 'renew' AND aas.more_years_renew=0 )GROUP BY a.contractid";
                         $severalRenewals=$adb->pquery($sql,array($rp['usercode'],$contractid));
                         $severalRenewals=$adb->num_rows($severalRenewals);
                         $severalRenewals=$severalRenewals;
                         if($isrenewflag==0){
                             $renewal_commission=$renewalBase;
                         }else{
                             $renewal_commission=6*pow(0.5 ,$severalRenewals);
                         }
                         $renewtimes=$severalRenewals+1;
                         $commissionforrenewal=$arriveachievement*$renewal_commission/100;
                     }else{
                         $commissionforrenewal=0;
                         $renewal_commission=0;
                         $renewtimes=0;
                     }
                     $ismoreYears=$more_years_renew;
                     if($isrenewflag==1){
                         $ismoreYears=0;
                     }

                     $effectiverefund=$rp['unit_price']*$scalling/100;
                     $datavalue=array();
                     $datavalue['owncompanys']=$rowDatas['owncompanys'];
                     $datavalue['receivedpaymentownid']=$receivedpaymentownid;
                     $datavalue['scalling']=$scalling;
                     $datavalue['servicecontractid']=$rowDatas['servicecontractid'];
                     $datavalue['receivedpaymentsid']=$receivepayid;
                     $datavalue['businessunit']=$businessunit;
                     $datavalue['matchdate']=$matchdate;
                     $datavalue['departmentid']=$Department['departmentid']?$Department['departmentid']:0;
                     $datavalue['owncompany']=$rp['owncompany'];
                     $datavalue['createtime']=$rp['createtime'];
                     $datavalue['reality_date']=$rp['reality_date'];
                     $datavalue['paytitle']=$rp['paytitle'];
                     $datavalue['unit_price']=$rp['unit_price'];
                     $datavalue['unit_prices']=$unit_prices;
                     $datavalue['department']=$rowDatas['departmentname']?$rowDatas['departmentname']:'';
                     $datavalue['groupname']=$groupname?$groupname:' ';
                     $datavalue['departmentname']=$departmentname?$departmentname:' ';
                     $datavalue['receivedpaymentown']=$receivedpaymentown;
                     $datavalue['servicecontractstype']=$rp['servicecontractstype']?$rp['servicecontractstype']:' ';
                     $datavalue['accountname']=$rp['accountname']?$rp['accountname']:' ';
                     $datavalue['signdate']=$rp['signdate'];
                     $datavalue['contract_no']=$rp['contract_no'];
                     $datavalue['total']=$contractInfo['contractamount']?$contractInfo['contractamount']:0;
                     $datavalue['dividetotal']=$dividetotal;
                     $datavalue['costing']=$costing;
                     $datavalue['purchasemount']=$purchasemount?$purchasemount:0;
                     $datavalue['worksheetcost']=$worksheetcost?$worksheetcost:0;
                     $datavalue['productlife']=$rp['productlife']?$rp['productlife']:0;
                     $datavalue['marketprice']=$rp['marketprice']?$rp['marketprice']:0;
                     $datavalue['dividemarketprice']=$dividemarketprice;
                     $datavalue['costdeduction']=$costdeduction;
                     $datavalue['dividecostdeduction']=$dividecostdeduction;
                     $datavalue['other']=$others;
                     $datavalue['effectiverefund']=$effectiverefund;
                     $datavalue['arriveachievement']=$arriveachievement;
                     $datavalue['achievementmonth']=$achievementmonth;
                     $datavalue['modulestatus']=$modulestatus;
                     $datavalue['productname']=$productname;
                     $datavalue['achievementtype']=$updateachievementtype?$updateachievementtype:0;
                     $datavalue['producttype']=$producttype?$producttype:0;
                     $datavalue['extracost']=$extracost;
                     $datavalue['salong']=$salong;
                     $datavalue['waici']=$waici;
                     $datavalue['meijai']=$meijai;
                     $datavalue['othercost']=$othercost;
                     $datavalue['shareuser']=$shareuser;
                     $datavalue['remarks']=$updateremark;
                     $datavalue['generatedamount']=$generatedamount;
                     $datavalue['adjustbeforearriveachievement']=$arriveachievement;
                     $datavalue['divideworksheetcost']=$divideworksheetcost;
                     $datavalue['dividecosting']=$dividecosting;
                     $datavalue['dividepurchasemount']=$dividepurchasemount;
                     $datavalue['divideextracost']=$divideextracost;
                     $datavalue['divideother']=$divideothers;
                     $datavalue['more_years_renew']=$ismoreYears;
                     $datavalue['renewal_commission']=$renewal_commission;
                     $datavalue['renewtimes']=$renewtimes;
                     //$datavalue['splitcontractamount']=$splitcontractamount*$scalling/100;
                     $datavalue['splitcontractamount']=$splitcontractamount;
                     $datavalue['splitmarketprice']=$splitmarketprice*$scalling/100;
                     $datavalue['splitcost']=$splitcost*$scalling/100;
                     $datavalue['commissionforrenewal']=$commissionforrenewal;
                     $datavalue['splitbusinessunit']=$splitbusinessunit*$scalling/100;//拆分回款
                     $datavalue['activityname']=$rp['activityname'];//活动名称
                     $datavalue['activitytype']=$rp['activitytype'];//活动类型
                     $datavalue['waitsubarriveachievement']=$waitsubarriveachievement;//扣减业绩
                     $datavalue['alreadyarriveachievement']=$alreadyarriveachievement;//已扣减业绩

                     $insertValueStrArray[]=$datavalue;
                     //$insertValueStr.='(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?),';
                 }
            }else{
                //已分成业绩市场价
                $dividemarketprice=$rp['marketprice']*$scalling/100;
                $receivedpaymentown=$Department['last_name'];
                if(in_array($rp['productid'],array(24,25))){
                    $arriveachievement=$RoyaltyMultiplie*($arriveachievement- $costingdata['extracost'])*$scalling/100;
                }else{
                    $arriveachievement=$RoyaltyMultiplie*$arriveachievement*$scalling/100;
                }

                // 到账业绩减去pos手续费
                $arriveachievement=$arriveachievement-$otherdatas['extra_price']*$scalling/100;

                // 最后判断下到账回款 是否为负值 如果为负 则设为零
                if($arriveachievement<0){
                    $arriveachievement=0;
                }
                // 是否多年续费单
                $more_years_renew=0;
                // 如果业绩类型是续费，或者 直接续费单需要计算提成点
                if($achievementtype=='renew'){
                    // 查询该客户有几次续费单了
                    //$sql="SELECT count(1) as numbers  FROM	vtiger_activationcode AS a LEFT JOIN vtiger_achievementallot_statistic AS aas ON a.contractid = aas.servicecontractid WHERE	a.usercode =?  AND aas.achievementmonth>0  AND a.contractid <> ? AND ( aas.achievementtype = 'renew' OR ( aas.achievementtype = 'newadd' and  aas.more_years_renew=1 ) )GROUP BY	a.contractid ";
                    $sql="SELECT count(1) as numbers  FROM	vtiger_activationcode AS a LEFT JOIN vtiger_achievementallot_statistic AS aas ON a.contractid = aas.servicecontractid WHERE	a.usercode =?  AND aas.achievementmonth>0  AND a.contractid <> ? AND  aas.achievementtype = 'renew' and  aas.more_years_renew=0 GROUP BY	a.contractid ";
                    $severalRenewals=$adb->pquery($sql,array($rp['usercode'],$contractid));
                    $severalRenewals=$adb->num_rows($severalRenewals);
                    $severalRenewals=$severalRenewals;
                    $renewal_commission=6*pow(0.5 ,$severalRenewals);
                    $renewtimes=$severalRenewals+1;
                    $commissionforrenewal=$arriveachievement*$renewal_commission/100;
                }else{
                    $renewal_commission=0;
                    $renewtimes=0;
                    $commissionforrenewal=0;
                }
                $effectiverefund=$rp['unit_price']*$scalling/100;
                $splitcontractamount=0;
                $splitmarketprice=0;
                $splitcost=0;
                $datavalue=array();
                $datavalue['owncompanys']=$rowDatas['owncompanys'];
                $datavalue['receivedpaymentownid']=$receivedpaymentownid;
                $datavalue['scalling']=$scalling;
                $datavalue['servicecontractid']=$rowDatas['servicecontractid'];
                $datavalue['receivedpaymentsid']=$receivepayid;
                $datavalue['businessunit']=$businessunit;
                $datavalue['matchdate']=$matchdate;
                $datavalue['departmentid']=$Department['departmentid']?$Department['departmentid']:0;
                $datavalue['owncompany']=$rp['owncompany'];
                $datavalue['createtime']=$rp['createtime'];
                $datavalue['reality_date']=$rp['reality_date'];
                $datavalue['paytitle']=$rp['paytitle'];
                $datavalue['unit_price']=$rp['unit_price'];
                $datavalue['unit_prices']=$unit_prices;
                $datavalue['department']=$rowDatas['departmentname']?$rowDatas['departmentname']:'';
                $datavalue['groupname']=$groupname?$groupname:' ';
                $datavalue['departmentname']=$departmentname?$departmentname:' ';
                $datavalue['receivedpaymentown']=$receivedpaymentown;
                $datavalue['servicecontractstype']=$rp['servicecontractstype']?$rp['servicecontractstype']:' ';
                $datavalue['accountname']=$rp['accountname']?$rp['accountname']:' ';
                $datavalue['signdate']=$rp['signdate'];
                $datavalue['contract_no']=$rp['contract_no'];
                $datavalue['total']=$contractInfo['contractamount']?$contractInfo['contractamount']:0;
                $datavalue['dividetotal']=$dividetotal;
                $datavalue['costing']=$costing;
                $datavalue['purchasemount']=$purchasemount?$purchasemount:0;
                $datavalue['worksheetcost']=$worksheetcost?$worksheetcost:0;
                $datavalue['productlife']=$rp['productlife']?$rp['productlife']:0;
                $datavalue['marketprice']=$rp['marketprice']?$rp['marketprice']:0;
                $datavalue['dividemarketprice']=$dividemarketprice;
                $datavalue['costdeduction']=$costdeduction;
                $datavalue['dividecostdeduction']=$dividecostdeduction;
                $datavalue['other']=$other;
                $datavalue['effectiverefund']=$effectiverefund;
                $datavalue['arriveachievement']=$arriveachievement;
                $datavalue['achievementmonth']=$achievementmonth;
                $datavalue['modulestatus']=$modulestatus;
                $datavalue['productname']=$productname;
                $datavalue['achievementtype']=$achievementtype?$achievementtype:0;
                $datavalue['producttype']=$producttype?$producttype:0;
                $datavalue['extracost']=$extracost;
                $datavalue['salong']=$salong;
                $datavalue['waici']=$waici;
                $datavalue['meijai']=$meijai;
                $datavalue['othercost']=$othercost;
                $datavalue['shareuser']=$shareuser;
                $datavalue['remarks']=$remark;
                $datavalue['generatedamount']=$generatedamount;
                $datavalue['adjustbeforearriveachievement']=$arriveachievement;
                $datavalue['divideworksheetcost']=$divideworksheetcost;
                $datavalue['dividecosting']=$dividecosting;
                $datavalue['dividepurchasemount']=$dividepurchasemount;
                $datavalue['divideextracost']=$divideextracost;
                $datavalue['divideother']=$divideother;
                $datavalue['more_years_renew']=$more_years_renew;
                $datavalue['renewal_commission']=$renewal_commission;
                $datavalue['renewtimes']=$renewtimes;
                $datavalue['splitcontractamount']=$splitcontractamount;
                $datavalue['splitmarketprice']=$splitmarketprice;
                $datavalue['splitcost']=$splitcost;
                $datavalue['commissionforrenewal']=$commissionforrenewal;
                $datavalue['splitbusinessunit']=$unit_prices;//拆分回款
                $datavalue['activityname']=$rp['activityname'];//活动名称
                $datavalue['activitytype']=$rp['activitytype'];//活动类型
                $datavalue['waitsubarriveachievement']=$waitsubarriveachievement;//扣减业绩
                $datavalue['alreadyarriveachievement']=$alreadyarriveachievement;//已扣减业绩
                $insertValueStrArray[]=$datavalue;
                //$insertValueStr.='(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?),';
            }
        }
        // 是升级的
        if(in_array($rp['servicecontractstype'],array('upgrade'))){
              // 且走了   扣减原业绩
              if($isUpgradeAndDeduction==1){
                   $param=array('contractid'=>$contractid,'oldcontractid'=>$rp['oldcontractid'],'receivepayid'=>$receivepayid,'alreadydeduction'=>$oldarriveachievement['newaddoldallarriveachievement']-$remain['newAddLastDeduction'],'totaldeductionmoney'=>$oldarriveachievement['newaddoldallarriveachievement'],'deductionremark'=>$deductionremark,'listAchievementType'=>'newadd');
                   //原单多年单  新单多年单
                   if($listAchievementType==1){
                       $param['deductionremark']=$deductionremark."type=1新单";
                       $param['lastdeductionmoney']=$remain['newAddLastDeduction'];
                       //老的剩余未扣减到账业绩 新单
                       $this->hasdeduction($param,$adb);
                       //老的剩余未扣减到账业绩 续费
                       $param['deductionremark']=$deductionremark."type=1续费";
                       $param['alreadydeduction']=$oldarriveachievement['renewoldallarriveachievement']-$remain['renewLastDeduction'];
                       $param['listAchievementType']='renew';
                       $param['totaldeductionmoney']=$oldarriveachievement['renewoldallarriveachievement'];
                       $param['lastdeductionmoney']=$remain['renewLastDeduction'];
                       $this->hasdeduction($param,$adb);
                   //原单单年 原单业绩类型新购  新单多年
                   }elseif ($listAchievementType==2){
                       $param['deductionremark']=$deductionremark."type=2";
                       $param['lastdeductionmoney']=$remain['newAddLastDeduction'];
                       $param['alreadydeduction']=$oldarriveachievement['newaddoldallarriveachievement']-$remain['newAddLastDeduction'];
                       $this->hasdeduction($param,$adb);
                   //原单单年  原单业绩类型续费  新单多年
                   }elseif ($listAchievementType==3){
                       $param['deductionremark']=$deductionremark."type=3";
                       $param['alreadydeduction']=$oldarriveachievement['renewoldallarriveachievement']-$remain['renewLastDeduction'];
                       $param['listAchievementType']='renew';
                       $param['lastdeductionmoney']=$remain['newAddLastDeduction'];
                       $param['totaldeductionmoney']=$oldarriveachievement['renewoldallarriveachievement'];
                       $this->hasdeduction($param,$adb);
                       // 原  新单 全是单年单 不管业绩类型 直接减原业绩
                   }elseif($listAchievementType==4){
                       // 这个相当于是  业绩类型  单年 对 单年的扣减  所以只要减原业绩 就行了  这个就把已经扣减原业绩 放到了 新单里  前面获取剩余未扣减的金额 可以看做是 原来的 续费 和 新单和 - 已扣减 等于剩余未扣减 可以看 $listAchievementType==4 扣减原业绩的值 就明白了。
                       $param['deductionremark']=$deductionremark."type=4";
                       $param['lastdeductionmoney']=$remain['allAddLastDeduction'];
                       $param['alreadydeduction']=($oldarriveachievement['newaddoldallarriveachievement']+$oldarriveachievement['renewoldallarriveachievement'])-$remain['allAddLastDeduction'];
                       $this->hasdeduction($param,$adb);
                   }
              }
        }
        return $returnData=$this->returnDataValue($insertValueStrArray);
        return array("datavalue"=>$returnData['datavalue'],"insertValueStr"=>$returnData['insertValueStr']);
    }

    //保存已经扣减业绩信息
    private function hasdeduction($params,$adb){
        if(!isset($params['salesorderid'])){
            $params['salesorderid']=0;
        }
        $adb->pquery("INSERT INTO `vtiger_oldachievement_hasdeduction` (`servicecontractsid`, `oldservicecontractsid`, `receivedpaymentsid`, `deductionmoney`, `createtime`, `totaldeductionmoney`, `marks`,`achievementtype`,lastdeductionmoney,salesorderid) VALUES (?,?,?,?,?,?,?,?,?,?)",
            array($params['contractid'],$params['oldcontractid'],$params['receivepayid'],$params['alreadydeduction'],date("Y-m-d H:i:s"),$params['totaldeductionmoney'],$params['deductionremark'],$params['listAchievementType'],$params['lastdeductionmoney'],$params['salesorderid']));
    }
    /**
     * 百度V认证
     */
    public function VRZCalculationAchievement($rp,$contractid,$receivepayid,$shareuser,$total,$currentid,$remark,$adb,$matchdate=0,$salesorderid=0){
        $generatedamount=0;
        //②查询非Tyun类年限
        if($rp['multitype']==1){
            $queryc=" SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife FROM vtiger_salesorderproductsrel,vtiger_salesorderrayment WHERE vtiger_salesorderproductsrel.servicecontractsid=? AND vtiger_salesorderrayment.receivedpaymentsid=? AND vtiger_salesorderproductsrel.salesorderid=vtiger_salesorderrayment.salesorderid AND multistatus in(0,1) LIMIT 1";
            $resultdatapayments=$adb->pquery($queryc,array($rp['servicecontractsid'],$receivepayid));
        }else{
            $queryc=" SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid= ? AND multistatus in(0,1) LIMIT 1";
            $resultdatapayments=$adb->pquery($queryc,array($rp['servicecontractsid']));
        }
        $agelife=$adb->query_result_rowdata($resultdatapayments,0);
        $marketingPrice=0;
        $costprice=0;
        $remark.="百度V认证正常公式回款*0.4";
        //人力成本   工单外采成本  额外成本
        if($rp['multitype']==1){
            $queryc="SELECT IFNULL(sum(vtiger_salesorderproductsrel.costing),0) AS costing,IFNULL(sum(vtiger_salesorderproductsrel.purchasemount),0) AS purchasemount,IFNULL(sum(vtiger_salesorderproductsrel.extracost),0) AS extracost FROM vtiger_salesorderproductsrel,vtiger_salesorderrayment WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND vtiger_salesorderrayment.receivedpaymentsid=? AND vtiger_salesorderproductsrel.salesorderid=vtiger_salesorderrayment.salesorderid AND multistatus=3 ";
            $costingdata=$adb->pquery($queryc,array($contractid,$receivepayid));
        }else{
            $queryc="SELECT IFNULL(sum(vtiger_salesorderproductsrel.costing),0) AS costing,IFNULL(sum(vtiger_salesorderproductsrel.purchasemount),0) AS purchasemount,IFNULL(sum(vtiger_salesorderproductsrel.extracost),0) AS extracost FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND multistatus=3 ";
            $costingdata=$adb->pquery($queryc,array($contractid));
        }
        $costingdata=$adb->query_result_rowdata($costingdata,0);
        // 有关 沙龙 外采 媒介充值 其他
        $queryc="SELECT extra_type,extra_price FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid=?";
        $otherdata=$adb->pquery($queryc,array($receivepayid));
        $otherdata=$adb->query_result_rowdata($otherdata,0);
        $otherDataTypeArray=array();
        foreach ($otherdata as $key=>$val){
            $otherDataTypeArray[$val['extra_type']]=$otherDataTypeArray[$val['extra_type']]+$val['extra_price'];
        }
        //该服务合同回款相关的总的之和
        $queryc="SELECT SUM(extra_price) as extra_price FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid =? ";
        $otherdatas=$adb->pquery($queryc,array($receivepayid));
        $otherdatas=$adb->query_result_rowdata($otherdatas,0);
        //查询该服务合同对应的产品
        if($rp['multitype']==1){
            $queryc=" SELECT p.productid,p.productname,ssp.productcomboid,ssp.thepackage,count(1) as counts FROM vtiger_salesorderproductsrel as ssp LEFT JOIN vtiger_products as p ON p.productid=ssp.productid LEFT JOIN vtiger_salesorderrayment as sd ON sd.salesorderid=ssp.salesorderid   WHERE ssp.servicecontractsid =? AND sd.receivedpaymentsid=? AND ssp.multistatus=3 group by ssp.productcomboid ";
            $productdatas=$adb->pquery($queryc,array($contractid,$receivepayid));
        }else{
            $queryc=" SELECT p.productid,p.productname,ssp.productcomboid,ssp.thepackage,count(1) as counts FROM vtiger_salesorderproductsrel as ssp LEFT JOIN vtiger_products as p ON p.productid=ssp.productid  WHERE ssp.servicecontractsid =? AND ssp.multistatus=3 group by ssp.productcomboid ";
            $productdatas=$adb->pquery($queryc,array($contractid));
        }
        $productname='';
        while ($rowDatas=$adb->fetch_array($productdatas)){
            if($rowDatas['productid']==$rp['productid']){
                $productname.=$rowDatas['thepackage']."(1),";
            }else{
                $productname.=$rowDatas['thepackage']."(".$rowDatas['counts']."),";
            }
        }
        $productname=trim($productname,',');
        // 查询市场价格
        /*$queryc=" SELECT pmarketprice,realmarketprice,FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife, (costing+purchasemount+extracost)as costprice ,costofuse FROM vtiger_salesorderproductsrel  WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND multistatus=3  ";
        $price=$adb->pquery($queryc,array($contractid));
        while ($rowDatas=$adb->fetch_array($price)){
            if((strpos($rp['contract_no'],"XF")!=false || $rp['servicecontractstype']!='新增')){
                $marketingPrice+=$rowDatas['pmarketprice']*$rowDatas['agelife'];
                $costprice+=$rowDatas['costofuse']*$rowDatas['agelife'];
            }else{
                $marketingPrice+=$rowDatas['realmarketprice']+$rowDatas['renewmarketprice']*($rowDatas['agelife']-1);
                $costprice+=$rowDatas['costprice']+$rowDatas['renewcostprice']*($rowDatas['agelife']-1);
            }
        }*/
        //查询市场价格
        //① 查询套餐市场价 和 成本
        $marketingPriceTaoCan=0;
        $costpriceTaoCan=0;
        if(!empty($rp['productid'])){
            if($rp['multitype']==1){
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid LEFT JOIN vtiger_salesorderrayment as sd ON sd.salesorderid=vtiger_salesorderproductsrel.salesorderid  WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND  sd.receivedpaymentsid=?  AND productcomboid IN(".$rp['productid'].") AND multistatus=3  LIMIT  1  ";
                $product=$adb->pquery($sql,array($contractid,$receivepayid));
            }else{
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid  WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND productcomboid IN(".$rp['productid'].") AND multistatus=3 LIMIT  1 ";
                $product=$adb->pquery($sql,array($contractid));
            }
            while ($rowsDat=$adb->fetch_array($product)) {
                if ((strpos($rp['contract_no'], "XF") != false || $rp['servicecontractstype'] != '新增')) {
                    $marketingPriceTaoCan += $rowsDat['renewalfee'] * $rowsDat['agelife'];
                    $costpriceTaoCan += $rowsDat['renewalcost'] * $rowsDat['agelife'];
                } else {
                    $marketingPriceTaoCan += $rowsDat['unit_price'] + $rowsDat['renewalfee'] * ($rowsDat['agelife'] - 1);
                    $costpriceTaoCan += $rowsDat['realprice'] + $rowsDat['renewalcost'] * ($rowsDat['agelife'] - 1);
                }
            }
        }
        //② 额外产品市场价  和 成本
        $marketingPriceExtra=0;
        $costpriceExtra=0;
        if(!empty($rp['extraproductid'])){
            if($rp['multitype']==1){
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid LEFT JOIN vtiger_salesorderrayment as sd ON sd.salesorderid=vtiger_salesorderproductsrel.salesorderid WHERE vtiger_salesorderproductsrel.servicecontractsid =?  AND sd.receivedpaymentsid=?  AND productcomboid IN(".$rp['extraproductid'].") AND multistatus=3  ";
                $product=$adb->pquery($sql,array($contractid,$receivepayid));
            }else{
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid  WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND productcomboid IN(".$rp['extraproductid'].") AND multistatus=3  ";
                $product=$adb->pquery($sql,array($contractid));
            }
            while ($rowsDat=$adb->fetch_array($product)){
                if((strpos($rp['contract_no'],"XF")!=false || $rp['servicecontractstype']!='新增')){
                    $marketingPriceExtra+=$rowsDat['renewalfee']*$rowsDat['agelife'];
                    $costpriceExtra+=$rowsDat['renewalcost']*$rowsDat['agelife'];
                }else{
                    $marketingPriceExtra+=$rowsDat['unit_price']+$rowsDat['renewalfee']*($rowsDat['agelife']-1);
                    $costpriceExtra+=$rowsDat['realprice']+$rowsDat['renewalcost']*($rowsDat['agelife']-1);
                }
            }
        }
        $marketingPrice=$marketingPriceExtra+$marketingPriceTaoCan;
        $costprice=$costpriceExtra+$costpriceTaoCan;
        $rp['marketprice']=$marketingPrice;
        $modulestatus='a_normal';
        //回款时间 即入账日期
        $reality_date=$rp['reality_date'];
        //匹配时间
        if(empty($matchdate)){
            $matchdate=date('Y-m-d');
        }
        $tsiteperformanceoftimeIsExists=1;
        //查询业绩是否下单  查询已扣减成本金额
        if($rp['multitype']==1){
            $sqlsalesorder=" SELECT costreduction,performanceoftime  FROM vtiger_salesorder as so,vtiger_salesorderrayment as sd   WHERE  so.servicecontractsid=? AND sd.receivedpaymentsid=? AND sd.salesorderid=so.salesorderid  AND  so.iscancel=0 LIMIT 1  ";
            $salesorderInfo=$adb->pquery($sqlsalesorder,array($contractid,$receivepayid));
        }else{
            $sqlsalesorder=" SELECT  costreduction,performanceoftime  FROM vtiger_salesorder WHERE  servicecontractsid=? AND iscancel=0 LIMIT 1  ";
            $salesorderInfo=$adb->pquery($sqlsalesorder,array($contractid));
        }
        $salesorderInfo=$adb->query_result_rowdata($salesorderInfo,0);
        if(empty($salesorderInfo['performanceoftime'])){
            $achievementmonth=null;
            $tsiteperformanceoftimeIsExists=0;
        }else{
            $achievementmonth=$this->getspecialAchievementmonth($matchdate,$reality_date,$matchdate);
        }
        //成本扣除数
        $costdeduction=$costprice;
        // 新增业务
        if($rp['productid']==2226512){
            $achievementtype='newadd';
            // 新增业务
        }else if(strpos($rp['contract_no'],"VRZ")){
            $achievementtype='newadd';
            //续费业务
        }else if((strpos($rp['contract_no'],"XF")!=false || $rp['servicecontractstype']!='新增') ){
            $achievementtype='renew';
            // 新增业务
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
            $businessunit=$total*($scalling/100);
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
            /*$departmentname=$Department['departmentname'];
            if($Department['departmentid']==$Department['parentdepartment']){
                $groupname=$departmentname;
            }else{
                $str="::".$Department['departmentid'];
                $Department['parentdepartment']= str_replace($str,"",$Department['parentdepartment']);
                $parentdepartment = explode("::",$Department['parentdepartment']);
                $parentdepartmentId = end($parentdepartment);
                //查询父类
                $queryc=" SELECT departmentname FROM vtiger_departments  WHERE  departmentid = ?  limit 1 ";
                $resultdataDepartment=$adb->pquery($queryc,array($parentdepartmentId));
                $Departments=$adb->query_result_rowdata($resultdataDepartment,0);
                $groupname=$Departments['departmentname'];
            }*/
            $costing=$costingdata['costing']*($scalling/100)*$rp['unit_price']/$rp['total'];
            $purchasemount=$costingdata['purchasemount']*($scalling/100)*$rp['unit_price']/$rp['total'];
            $extracost = $costingdata['extracost']*($scalling/100)*$rp['unit_price']/$rp['total'];
            $allother=$otherdata['extra_price']*($scalling/100);
            // 重新计算沙龙  媒体外采  没接充值  和   其他 （其他包含所有之和）
            $salong=$otherDataTypeArray['沙龙']*($scalling/100);
            $waici=$otherDataTypeArray['外采']*($scalling/100);
            $meijai=$otherDataTypeArray['媒介充值']*($scalling/100);
            //  other 指 回款（沙龙外采媒体充值其他的总和）
            $othercost=$otherdatas['extra_price'];
            //工单成本合计
            $worksheetcost=($costingdata['costing']+$costingdata['purchasemount']+$costingdata['extracost'])*($scalling/100)*$rp['unit_price']/$rp['total']+$allother;
            $divideworksheetcost=$worksheetcost*($scalling/100);
            $dividecosting=$costingdata['costing']*($scalling/100);
            $dividepurchasemount=$costingdata['purchasemount']*($scalling/100);
            $divideextracost=$costingdata['extracost']*($scalling/100);
            $divideother=$othercost*($scalling/100);
            //已分成业绩市场价
            $dividemarketprice=$rp['marketprice']*$scalling/100;
            //已分成成本扣除数
            $dividecostdeduction=$costprice*$scalling/100;
            $other=$othercost;

            // 已分成回款
            $unit_prices=$rp['unit_price']*($scalling/100);
            //到账业绩
            $arriveachievement=$unit_prices * 0.4;
            // 到账业绩 减去pos手续费
            $arriveachievement=$arriveachievement-$otherdatas['extra_price']*($scalling/100);
            $producttype=4;
            $receivedpaymentown=$Department['last_name'];
            // 最后判断下到账回款 是否为负值 如果为负 则设为零
            if($arriveachievement < 0 || $tsiteperformanceoftimeIsExists!=1){
                $arriveachievement=0;
            }
            $effectiverefund=$rp['unit_price']*$scalling/100;
            $more_years_renew=0;
            $renewal_commission=0;
            $renewtimes=0;
            $splitcontractamount=0;
            $splitmarketprice=0;
            $splitcost=0;
            $commissionforrenewal=0;
            $datavalue[]=$rowDatas['owncompanys'];
            $datavalue[]=$receivedpaymentownid;
            $datavalue[]=$scalling;
            $datavalue[]=$rowDatas['servicecontractid'];
            $datavalue[]=$receivepayid;
            $datavalue[]=$businessunit;
            $datavalue[]=$matchdate;
            $datavalue[]=$Department['departmentid']?$Department['departmentid']:0;
            $datavalue[]=$rp['owncompany'];
            $datavalue[]=$rp['createtime'];
            $datavalue[]=$rp['reality_date'];
            $datavalue[]=$rp['paytitle'];
            $datavalue[]=$rp['unit_price'];
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
            $datavalue[]=$agelife['agelife']?$agelife['agelife']:0;
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
            $datavalue[]=$generatedamount;$datavalue[]=$arriveachievement;$datavalue[]=$divideworksheetcost;$datavalue[]=$dividecosting;$datavalue[]=$dividepurchasemount;$datavalue[]=$divideextracost;$datavalue[]=$divideother;$datavalue[]=$more_years_renew;$datavalue[]=$renewal_commission;
            $datavalue[]=$renewtimes;$datavalue[]=$splitcontractamount;$datavalue[]=$splitmarketprice;$datavalue[]=$splitcost;$datavalue[]=$commissionforrenewal;
            $insertValueStr.='(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?),';
        }
        return array("datavalue"=>$datavalue,"insertValueStr"=>$insertValueStr,'isTyun'=>0);
    }
    /**
     * 非SaaS
     */
    public function noSaaSCalculationAchievement($rp,$contractid,$receivepayid,$shareuser,$total,$currentid,$remark,$adb,$matchdate=0,$salesorderid=0){
        $generatedamount=0;
        //②查询非Tyun类年限
        if($rp['multitype']==1){
            $queryc=" SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife FROM vtiger_salesorderproductsrel,vtiger_salesorderrayment WHERE vtiger_salesorderproductsrel.servicecontractsid=? AND vtiger_salesorderrayment.receivedpaymentsid=? AND vtiger_salesorderproductsrel.salesorderid=vtiger_salesorderrayment.salesorderid AND multistatus in(0,1) LIMIT 1";
            $resultdatapayments=$adb->pquery($queryc,array($rp['servicecontractsid'],$receivepayid));
        }else{
            $queryc=" SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid= ? AND multistatus in(0,1) LIMIT 1";
            $resultdatapayments=$adb->pquery($queryc,array($rp['servicecontractsid']));
        }
        $agelife=$adb->query_result_rowdata($resultdatapayments,0);
        $marketingPrice=0;
        $costprice=0;
        $remark.="非SaaS类";
        //人力成本   工单外采成本  额外成本
        if($rp['multitype']==1){
            $queryc="SELECT IFNULL(sum(vtiger_salesorderproductsrel.costing),0) AS costing,IFNULL(sum(vtiger_salesorderproductsrel.purchasemount),0) AS purchasemount,IFNULL(sum(vtiger_salesorderproductsrel.extracost),0) AS extracost FROM vtiger_salesorderproductsrel,vtiger_salesorderrayment WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND vtiger_salesorderrayment.receivedpaymentsid=? AND vtiger_salesorderproductsrel.salesorderid=vtiger_salesorderrayment.salesorderid AND multistatus=3 ";
            $costingdata=$adb->pquery($queryc,array($contractid,$receivepayid));
        }else{
            $queryc="SELECT IFNULL(sum(vtiger_salesorderproductsrel.costing),0) AS costing,IFNULL(sum(vtiger_salesorderproductsrel.purchasemount),0) AS purchasemount,IFNULL(sum(vtiger_salesorderproductsrel.extracost),0) AS extracost FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND multistatus=3 ";
            $costingdata=$adb->pquery($queryc,array($contractid));
        }
        $costingdata=$adb->query_result_rowdata($costingdata,0);
        // 有关 沙龙 外采 媒介充值 其他
        $queryc="SELECT extra_type,extra_price FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid=? ";
        $otherdata=$adb->pquery($queryc,array($receivepayid));
        $otherdata=$adb->query_result_rowdata($otherdata,0);
        $otherDataTypeArray=array();
        foreach ($otherdata as $key=>$val){
            $otherDataTypeArray[$val['extra_type']]=$otherDataTypeArray[$val['extra_type']]+$val['extra_price'];
        }
        //该服务合同回款相关的总的之和
        $queryc="SELECT SUM(extra_price) as extra_price FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid =? ";
        $otherdatas=$adb->pquery($queryc,array($receivepayid));
        $otherdatas=$adb->query_result_rowdata($otherdatas,0);

        //查询该服务合同对应的产品
        if($rp['multitype']==1){
            $queryc=" SELECT p.productid,p.productname,ssp.productcomboid,ssp.thepackage,count(1) as counts FROM vtiger_salesorderproductsrel as ssp LEFT JOIN vtiger_products as p ON p.productid=ssp.productid LEFT JOIN vtiger_salesorderrayment as sd ON sd.salesorderid=ssp.salesorderid   WHERE ssp.servicecontractsid =? AND sd.receivedpaymentsid=? AND ssp.multistatus=3 group by ssp.productcomboid ";
            $productdatas=$adb->pquery($queryc,array($contractid,$receivepayid));
        }else{
            $queryc=" SELECT p.productid,p.productname,ssp.productcomboid,ssp.thepackage,count(1) as counts FROM vtiger_salesorderproductsrel as ssp LEFT JOIN vtiger_products as p ON p.productid=ssp.productid  WHERE ssp.servicecontractsid =? AND ssp.multistatus=3 group by ssp.productcomboid ";
            $productdatas=$adb->pquery($queryc,array($contractid));
        }
        $productname='';
        while ($rowDatas=$adb->fetch_array($productdatas)){
            if($rowDatas['productid']==$rp['productid']){
                $productname.=$rowDatas['thepackage']."(1),";
            }else{
                $productname.=$rowDatas['thepackage']."(".$rowDatas['counts']."),";
            }
        }
        $productname=trim($productname,',');
        // 查询市场价格
        /*$queryc=" SELECT pmarketprice,realmarketprice,FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife, (costing+purchasemount+extracost)as costprice ,costofuse FROM vtiger_salesorderproductsrel  WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND multistatus=3  ";
        $price=$adb->pquery($queryc,array($contractid));
        while ($rowDatas=$adb->fetch_array($price)){
            if((strpos($rp['contract_no'],"XF")!=false || $rp['servicecontractstype']!='新增')){
                $marketingPrice+=$rowDatas['pmarketprice']*$rowDatas['agelife'];
                $costprice+=$rowDatas['costofuse']*$rowDatas['agelife'];
            }else{
                $marketingPrice+=$rowDatas['realmarketprice']+$rowDatas['renewmarketprice']*($rowDatas['agelife']-1);
                $costprice+=$rowDatas['costprice']+$rowDatas['renewcostprice']*($rowDatas['agelife']-1);
            }
        }*/
        //查询市场价格
        //① 查询套餐市场价 和 成本
        $marketingPriceTaoCan=0;
        $costpriceTaoCan=0;
        if(!empty($rp['productid'])){
            if($rp['multitype']==1){
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid LEFT JOIN vtiger_salesorderrayment as sd ON sd.salesorderid=vtiger_salesorderproductsrel.salesorderid  WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND  sd.receivedpaymentsid=?  AND productcomboid IN(".$rp['productid'].") AND multistatus=3  LIMIT  1  ";
                $product=$adb->pquery($sql,array($contractid,$receivepayid));
            }else{
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid  WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND productcomboid IN(".$rp['productid'].") AND multistatus=3 LIMIT  1 ";
                $product=$adb->pquery($sql,array($contractid));
            }
            while ($rowsDat=$adb->fetch_array($product)) {
                if ((strpos($rp['contract_no'], "XF") != false || $rp['servicecontractstype'] != '新增')) {
                    $marketingPriceTaoCan += $rowsDat['renewalfee'] * $rowsDat['agelife'];
                    $costpriceTaoCan += $rowsDat['renewalcost'] * $rowsDat['agelife'];
                } else {
                    $marketingPriceTaoCan += $rowsDat['unit_price'] + $rowsDat['renewalfee'] * ($rowsDat['agelife'] - 1);
                    $costpriceTaoCan += $rowsDat['realprice'] + $rowsDat['renewalcost'] * ($rowsDat['agelife'] - 1);
                }
            }
        }
        //② 额外产品市场价  和 成本
        $marketingPriceExtra=0;
        $costpriceExtra=0;
        if(!empty($rp['extraproductid'])){
            if($rp['multitype']==1){
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid LEFT JOIN vtiger_salesorderrayment as sd ON sd.salesorderid=vtiger_salesorderproductsrel.salesorderid WHERE vtiger_salesorderproductsrel.servicecontractsid =?  AND sd.receivedpaymentsid=?  AND productcomboid IN(".$rp['extraproductid'].") AND multistatus=3  ";
                $product=$adb->pquery($sql,array($contractid,$receivepayid));
            }else{
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid  WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND productcomboid IN(".$rp['extraproductid'].") AND multistatus=3  ";
                $product=$adb->pquery($sql,array($contractid));
            }
            while ($rowsDat=$adb->fetch_array($product)){
                if((strpos($rp['contract_no'],"XF")!=false || $rp['servicecontractstype']!='新增')){
                    $marketingPriceExtra+=$rowsDat['renewalfee']*$rowsDat['agelife'];
                    $costpriceExtra+=$rowsDat['renewalcost']*$rowsDat['agelife'];
                }else{
                    $marketingPriceExtra+=$rowsDat['unit_price']+$rowsDat['renewalfee']*($rowsDat['agelife']-1);
                    $costpriceExtra+=$rowsDat['realprice']+$rowsDat['renewalcost']*($rowsDat['agelife']-1);
                }
            }
        }
        $marketingPrice=$marketingPriceExtra+$marketingPriceTaoCan;
        $costprice=$costpriceExtra+$costpriceTaoCan;
        $rp['marketprice']=$marketingPrice;
        //非saas类获取市场价 虽然业绩计算公式和市场价无关但还是查下 start
        $rp['servicecontractid']=$contractid;
        $rp['receivedpaymentsid']=$receivepayid;
        $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
        $dataResult=$Matchreceivements_Record_Model->noSaaSGetMarketing($rp);
        if($dataResult>0){
            $rp['marketprice']=$dataResult;
        }
        // 非saas类获取市场价 虽然业绩计算公式和市场价无关但还是查下  end
        $modulestatus='a_normal';
        //回款时间 即入账日期
        $reality_date=$rp['reality_date'];
        //匹配时间
        if(empty($matchdate)){
            $matchdate=date('Y-m-d');
        }

        //查询业绩是否下单  查询已扣减成本金额
        if($rp['multitype']==1){
            $sqlsalesorder=" SELECT costreduction,performanceoftime  FROM vtiger_salesorder as so,vtiger_salesorderrayment as sd   WHERE  so.servicecontractsid=? AND sd.receivedpaymentsid=? AND sd.salesorderid=so.salesorderid  AND  so.iscancel=0 LIMIT 1  ";
            $salesorderInfo=$adb->pquery($sqlsalesorder,array($contractid,$receivepayid));
        }else{
            $sqlsalesorder=" SELECT  costreduction,performanceoftime  FROM vtiger_salesorder WHERE  servicecontractsid=? AND iscancel=0 LIMIT 1  ";
            $salesorderInfo=$adb->pquery($sqlsalesorder,array($contractid));
        }
        $salesorderInfo=$adb->query_result_rowdata($salesorderInfo,0);
        if(empty($salesorderInfo['performanceoftime'])){
            $achievementmonth=null;
            $tsiteperformanceoftimeIsExists=0;
        }else{
            $achievementmonth=$this->getspecialAchievementmonth($matchdate,$reality_date,$matchdate);
        }

        $tsiteperformanceoftimeIsExists=1;
        //成本扣除数
        $costdeduction=$costprice;
        // 新增业务
        if($rp['productid']==2226512){
            $remark.="2226512固定业绩类型为新单";
            $achievementtype='newadd';
            // 新增业务
        }else if(strpos($rp['contract_no'],"VRZ")){
            $achievementtype='newadd';
            //续费业务
        }else if((strpos($rp['contract_no'],"XF")!=false || $rp['servicecontractstype']!='新增') ){
            $achievementtype='renew';
            // 新增业务
        }else{
            $achievementtype='newadd';
        }
        //工单成本合计
        $worksheetcost=($costingdata['costing']+$costingdata['purchasemount']+$costingdata['extracost']);
        //查询该服务合同分成人
        $queryc=" SELECT vtiger_servicecontracts_divide.*,vtiger_departments.departmentname FROM vtiger_servicecontracts_divide  LEFT JOIN vtiger_departments ON  vtiger_departments.departmentid=vtiger_servicecontracts_divide.signdempart WHERE vtiger_servicecontracts_divide.servicecontractid = ?";
        $resultdatas=$adb->pquery($queryc,array($contractid));
        $insertValueStr='';
        $i=1;
        while ($rowDatas=$adb->fetch_array($resultdatas)){

            $scalling=$rowDatas['scalling'];
            $businessunit=$total*($scalling/100);
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
            /*$departmentname=$Department['departmentname'];
            if($Department['departmentid']==$Department['parentdepartment']){
                $groupname=$departmentname;
            }else{
                $str="::".$Department['departmentid'];
                $Department['parentdepartment']= str_replace($str,"",$Department['parentdepartment']);
                $parentdepartment = explode("::",$Department['parentdepartment']);
                $parentdepartmentId = end($parentdepartment);
                //查询父类
                $queryc=" SELECT departmentname FROM vtiger_departments  WHERE  departmentid = ?  limit 1 ";
                $resultdataDepartment=$adb->pquery($queryc,array($parentdepartmentId));
                $Departments=$adb->query_result_rowdata($resultdataDepartment,0);
                $groupname=$Departments['departmentname'];
            }*/
            $costing=$costingdata['costing'];
            $purchasemount=$costingdata['purchasemount'];
            $extracost = $costingdata['extracost'];
            // 重新计算沙龙  媒体外采  没接充值  和   其他 （其他包含所有之和）
            $salong=$otherDataTypeArray['沙龙']*($scalling/100);
            $waici=$otherDataTypeArray['外采']*($scalling/100);
            $meijai=$otherDataTypeArray['媒介充值']*($scalling/100);
            //  other 指 回款（沙龙外采媒体充值其他的总和）
            $othercost=$otherdatas['extra_price'];
            $divideworksheetcost=$worksheetcost*($scalling/100);
            $dividecosting=$costingdata['costing']*($scalling/100);
            $dividepurchasemount=$costingdata['purchasemount']*($scalling/100);
            $divideextracost=$costingdata['extracost']*($scalling/100);
            $divideother=$othercost*($scalling/100);
            //已分成业绩市场价
            $dividemarketprice=$rp['marketprice']*$scalling/100;
            //已分成成本扣除数
            $dividecostdeduction=$costprice*$scalling/100;
            $other=$othercost;
            // 已分成回款
            $unit_prices=$rp['unit_price']*($scalling/100);
            $remark.="工单总成本（".$worksheetcost."）"."已扣减成本（".$salesorderInfo['costreduction']."）";
            //到账业绩
            $arriveachievement=($rp['unit_price']-($worksheetcost-$salesorderInfo['costreduction']))*$scalling/100;
            // 到账业绩减去pos手续费
            $arriveachievement=$arriveachievement-$otherdatas['extra_price']*$scalling/100;

            if($arriveachievement< 0 ){
                $arriveachievement=0;
            }

            $producttype=3;
            $receivedpaymentown=$Department['last_name'];
            // 最后判断下到账回款 是否为负值 如果为负 则设为零
            if($arriveachievement < 0 || $tsiteperformanceoftimeIsExists!=1){
                $arriveachievement=0;
            }
            $worksheetcosts=$worksheetcost;
            $effectiverefund=$arriveachievement;
            $more_years_renew=0;
            $renewal_commission=0;
            $renewtimes=0;
            $splitcontractamount=0;
            $splitmarketprice=0;
            $splitcost=0;
            $commissionforrenewal=0;
            $datavalue[]=$rowDatas['owncompanys'];
            $datavalue[]=$receivedpaymentownid;
            $datavalue[]=$scalling;
            $datavalue[]=$rowDatas['servicecontractid'];
            $datavalue[]=$receivepayid;
            $datavalue[]=$businessunit;
            $datavalue[]=$matchdate;
            $datavalue[]=$Department['departmentid']?$Department['departmentid']:0;
            $datavalue[]=$rp['owncompany'];
            $datavalue[]=$rp['createtime'];
            $datavalue[]=$rp['reality_date'];
            $datavalue[]=$rp['paytitle'];
            $datavalue[]=$rp['unit_price'];
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
            $datavalue[]=$worksheetcosts?$worksheetcosts:0;
            $datavalue[]=$agelife['agelife']?$agelife['agelife']:0;
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
            $datavalue[]=$generatedamount;$datavalue[]=$arriveachievement;$datavalue[]=$divideworksheetcost;$datavalue[]=$dividecosting;$datavalue[]=$dividepurchasemount;$datavalue[]=$divideextracost;$datavalue[]=$divideother;$datavalue[]=$more_years_renew;$datavalue[]=$renewal_commission;
            $datavalue[]=$renewtimes;$datavalue[]=$splitcontractamount;$datavalue[]=$splitmarketprice;$datavalue[]=$splitcost;$datavalue[]=$commissionforrenewal;
            $insertValueStr.='(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?),';
        }
        // 如果工单成本 没有审核通过没有生成时间则不更改已扣减成本
        if(!empty($salesorderInfo['performanceoftime'])) {
            //如果已减工单成本 + 目前回款金额 大于等于总成本说明 工单成本已经减完了 则直接把已减成本改成 总成本就ok了
            if($rp['multitype']==1){
                if ($rp['unit_price'] + $salesorderInfo['costreduction'] > $worksheetcost) {
                    //更新已减成本
                    $updatesql = " UPDATE vtiger_salesorder SET costreduction=? WHERE servicecontractsid =? AND salesorderid=(SELECT salesorderid FROM vtiger_salesorderrayment WHERE receivedpaymentsid=? LIMIT 1 ) ";
                    $adb->pquery($updatesql, array($worksheetcost, $contractid,$receivepayid));
                } else {

                    //更新已减成本
                    $updatesql = " UPDATE vtiger_salesorder SET costreduction=costreduction+? WHERE servicecontractsid =? AND salesorderid=(SELECT salesorderid FROM vtiger_salesorderrayment WHERE receivedpaymentsid=? LIMIT 1 ) ";
                    $adb->pquery($updatesql, array($rp['unit_price'], $contractid,$receivepayid));
                }
            }else{
                if ($rp['unit_price'] + $salesorderInfo['costreduction'] > $worksheetcost) {
                    //更新已减成本
                    $updatesql = " UPDATE vtiger_salesorder SET costreduction=? WHERE servicecontractsid =? ";
                    $adb->pquery($updatesql, array($worksheetcost, $contractid));
                } else {
                    //更新已减成本
                    $updatesql = " UPDATE vtiger_salesorder SET costreduction=costreduction+? WHERE servicecontractsid =? ";
                    $adb->pquery($updatesql, array($rp['unit_price'], $contractid));
                }
            }

        }
        return array("datavalue"=>$datavalue,"insertValueStr"=>$insertValueStr,'isTyun'=>0);
    }
    /**
     * TSITE
     */
    public function tsiteCalculationAchievement($rp,$contractid,$receivepayid,$shareuser,$total,$currentid,$remark,$adb,$matchdate=0,$salesorderid=0){
        $generatedamount=0;
        //②查询非Tyun类年限
        if(0<$salesorderid){//存在工单则以工单为准
            $queryc=" SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife FROM vtiger_salesorderproductsrel WHERE multistatus=3 AND vtiger_salesorderproductsrel.salesorderid=?  ORDER BY vtiger_salesorderproductsrel.agelife DESC LIMIT 1";
            $resultdatapayments=$adb->pquery($queryc,array($salesorderid));
        }else{//以合同为准
            if($rp['multitype']==1){//是否是多公单
                $queryc=" SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_salesorderproductsrel.salesorderid FROM vtiger_salesorderproductsrel,vtiger_salesorderrayment WHERE vtiger_salesorderproductsrel.servicecontractsid=? AND vtiger_salesorderrayment.receivedpaymentsid=? AND vtiger_salesorderproductsrel.salesorderid=vtiger_salesorderrayment.salesorderid AND multistatus=3 ORDER BY vtiger_salesorderproductsrel.agelife DESC LIMIT 1";
                $resultdatapayments=$adb->pquery($queryc,array($rp['servicecontractsid'],$receivepayid));
            }else{
                $queryc=" SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid= ? AND multistatus in(0,1)  ORDER BY vtiger_salesorderproductsrel.agelife DESC LIMIT 1";
                $resultdatapayments=$adb->pquery($queryc,array($rp['servicecontractsid']));
            }
        }

        $agelife=$adb->query_result_rowdata($resultdatapayments,0);
        $isMoreYears=$agelife['agelife']>1?1:0;// 是否是多年单
        $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
        $remark="TSITE产品";
        //人力成本   工单外采成本  额外成本
        if(0<$salesorderid){//当前工单的成本
            $queryc="SELECT IFNULL(sum(vtiger_salesorderproductsrel.costing),0) AS costing,vtiger_salesorderproductsrel.productnumber,IFNULL(sum(vtiger_salesorderproductsrel.purchasemount),0) AS purchasemount,IFNULL(sum(vtiger_salesorderproductsrel.extracost),0) AS extracost FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.salesorderid=? AND multistatus=3 ";
            $costingdata=$adb->pquery($queryc,array($contractid));
        }else{
            if($rp['multitype']==1){
                $queryc="SELECT IFNULL(sum(vtiger_salesorderproductsrel.costing),0) AS costing,vtiger_salesorderproductsrel.productnumber,IFNULL(sum(vtiger_salesorderproductsrel.purchasemount),0) AS purchasemount,IFNULL(sum(vtiger_salesorderproductsrel.extracost),0) AS extracost FROM vtiger_salesorderproductsrel,vtiger_salesorderrayment WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND vtiger_salesorderrayment.receivedpaymentsid=? AND vtiger_salesorderproductsrel.salesorderid=vtiger_salesorderrayment.salesorderid AND multistatus=3 ";
                $costingdata=$adb->pquery($queryc,array($contractid,$receivepayid));
            }else{
                $queryc="SELECT IFNULL(sum(vtiger_salesorderproductsrel.costing),0) AS costing,vtiger_salesorderproductsrel.productnumber,IFNULL(sum(vtiger_salesorderproductsrel.purchasemount),0) AS purchasemount,IFNULL(sum(vtiger_salesorderproductsrel.extracost),0) AS extracost FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND multistatus=3 ";
                $costingdata=$adb->pquery($queryc,array($contractid));
            }
        }

        $costingdata=$adb->query_result_rowdata($costingdata,0);
        // 有关 沙龙 外采 媒介充值 其他
        $queryc="SELECT extra_type,extra_price FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid=?";
        $otherdata=$adb->pquery($queryc,array($receivepayid));
        $otherdata=$adb->query_result_rowdata($otherdata,0);
        $otherDataTypeArray=array();
        foreach ($otherdata as $key=>$val){
            $otherDataTypeArray[$val['extra_type']]=$otherDataTypeArray[$val['extra_type']]+$val['extra_price'];
        }
        //该服务合同回款相关的总的之和
        $queryc="SELECT SUM(extra_price) as extra_price FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid =? ";
        $otherdatas=$adb->pquery($queryc,array($receivepayid));
        $otherdatas=$adb->query_result_rowdata($otherdatas,0);
        //查询该服务合同对应的产品
        if(0<$salesorderid){
            $queryc=" SELECT p.productid,p.productname,ssp.productcomboid,ssp.thepackage,ssp.productnumber,count(1) as counts FROM vtiger_salesorderproductsrel as ssp LEFT JOIN vtiger_products as p ON p.productid=ssp.productid  WHERE ssp.servicecontractsid =? AND ssp.multistatus=3 group by ssp.productcomboid ";
            $productdatas=$adb->pquery($queryc,array($contractid));
        }else{
            if($rp['multitype']==1){
                $queryc=" SELECT p.productid,p.productname,ssp.productcomboid,ssp.thepackage,ssp.productnumber,count(1) as counts FROM vtiger_salesorderproductsrel as ssp LEFT JOIN vtiger_products as p ON p.productid=ssp.productid LEFT JOIN vtiger_salesorderrayment as sd ON sd.salesorderid=ssp.salesorderid   WHERE ssp.servicecontractsid =? AND sd.receivedpaymentsid=? AND ssp.multistatus=3 group by ssp.productcomboid ";
                $productdatas=$adb->pquery($queryc,array($contractid,$receivepayid));
            }else{
                $queryc=" SELECT p.productid,p.productname,ssp.productcomboid,ssp.thepackage,ssp.productnumber,count(1) as counts FROM vtiger_salesorderproductsrel as ssp LEFT JOIN vtiger_products as p ON p.productid=ssp.productid  WHERE ssp.servicecontractsid =? AND ssp.multistatus=3 group by ssp.productcomboid ";
                $productdatas=$adb->pquery($queryc,array($contractid));
            }
        }

        $productname='';
        while ($rowDatas=$adb->fetch_array($productdatas)){
            if($rowDatas['productcomboid']==$rp['productid']){
                $productname.=$rowDatas['thepackage']."(1),";
            }else{
                $productname.=$rowDatas['thepackage']."(".$rowDatas['counts']."),";
            }
        }
        $productname=trim($productname,',');
        //查询市场价格
        //① 查询套餐市场价 和 成本
        $marketingPriceTaoCan=0;//总的市场价
        $costpriceTaoCan=0;//总的成本价
        $firstMarketingPriceTaoCan=0;//首购市场价
        $firstCostpriceTaoCan=0;//首购成本价
        $renewalfee=0;//首购成本价
        $realprice=0;//首购成本价
        if(!empty($rp['productid'])){
            if(0<$salesorderid){
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_salesorderproductsrel.productnumber,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid  WHERE vtiger_salesorderproductsrel.salesorderid =? AND productcomboid IN(".$rp['productid'].") AND multistatus=3 LIMIT  1 ";
                $product=$adb->pquery($sql,array($salesorderid));
            }else{
                if($rp['multitype']==1){
                    $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_salesorderproductsrel.productnumber,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid LEFT JOIN vtiger_salesorderrayment as sd ON sd.salesorderid=vtiger_salesorderproductsrel.salesorderid  WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND  sd.receivedpaymentsid=?  AND productcomboid IN(".$rp['productid'].") AND multistatus=3  LIMIT  1  ";
                    $product=$adb->pquery($sql,array($contractid,$receivepayid));
                }else{
                    $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_salesorderproductsrel.productnumber,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid  WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND productcomboid IN(".$rp['productid'].") AND multistatus=3 LIMIT  1 ";
                    $product=$adb->pquery($sql,array($contractid));
                }
            }

            while ($rowsDat=$adb->fetch_array($product)) {
                $productnumber=$rowsDat['productnumber']>1?$rowsDat['productnumber']:1;
                if ((strpos($rp['contract_no'], "XF") != false || $rp['servicecontractstype'] != '新增')) {
                    $marketingPriceTaoCan += $rowsDat['renewalfee'] * $rowsDat['agelife']*$productnumber;
                    $costpriceTaoCan += $rowsDat['renewalcost'] * $rowsDat['agelife']*$productnumber;
                    $firstMarketingPriceTaoCan+=$rowsDat['unit_price']*$productnumber;
                    $firstCostpriceTaoCan+=$rowsDat['realprice']*$productnumber;
                } else {
                    $marketingPriceTaoCan += ($rowsDat['unit_price'] + $rowsDat['renewalfee'] * ($rowsDat['agelife'] - 1))*$productnumber;
                    $costpriceTaoCan += ($rowsDat['realprice'] + $rowsDat['renewalcost'] * ($rowsDat['agelife'] - 1))*$productnumber;
                    $firstMarketingPriceTaoCan+=$rowsDat['unit_price']*$productnumber;
                    $firstCostpriceTaoCan+=$rowsDat['realprice']*$productnumber;
                    $renewalfee+=$rowsDat['renewalfee'] * ($rowsDat['agelife'] - 1)*$productnumber;
                    $realprice+=$rowsDat['renewalcost'] * ($rowsDat['agelife'] - 1)*$productnumber;
                }
            }
        }
        //② 额外产品市场价  和 成本
        $marketingPriceExtra=0;//续费市场价
        $costpriceExtra=0;//续费成本价
        $firstMarketingPriceExtra=0;//首购市场价
        $firstCostpriceExtra=0;//首购成本价
        $renewalfeeExtra=0;//续费市场价
        $realpriceExtra=0;//续费成本价
        if(!empty($rp['extraproductid'])){
            if(0<$salesorderid){
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_salesorderproductsrel.productnumber,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid  WHERE vtiger_salesorderproductsrel.salesorderid =? AND productcomboid IN(".$rp['extraproductid'].") AND multistatus=3  ";
                $product=$adb->pquery($sql,array($salesorderid));
            }else{
                if($rp['multitype']==1){
                    $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_salesorderproductsrel.productnumber,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid LEFT JOIN vtiger_salesorderrayment as sd ON sd.salesorderid=vtiger_salesorderproductsrel.salesorderid WHERE vtiger_salesorderproductsrel.servicecontractsid =?  AND sd.receivedpaymentsid=?  AND productcomboid IN(".$rp['extraproductid'].") AND multistatus=3  ";
                    $product=$adb->pquery($sql,array($contractid,$receivepayid));
                }else{
                    $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_salesorderproductsrel.productnumber,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid  WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND productcomboid IN(".$rp['extraproductid'].") AND multistatus=3  ";
                    $product=$adb->pquery($sql,array($contractid));
                }
            }

            while ($rowsDat=$adb->fetch_array($product)){
                $productnumber=$rowsDat['productnumber']>1?$rowsDat['productnumber']:1;
                if((strpos($rp['contract_no'],"XF")!=false || $rp['servicecontractstype']!='新增')){
                    $marketingPriceExtra+=$rowsDat['renewalfee']*$rowsDat['agelife']*$productnumber;//续费市场价
                    $costpriceExtra+=$rowsDat['renewalcost']*$rowsDat['agelife']*$productnumber;//续费成本价
                    $firstMarketingPriceExtra +=$rowsDat['unit_price']*$productnumber;//首购市场价
                    $firstCostpriceExtra +=$rowsDat['realprice']*$productnumber;//首购成本价
                }else{
                    $marketingPriceExtra+=($rowsDat['unit_price']+$rowsDat['renewalfee']*($rowsDat['agelife']-1))*$productnumber;
                    $costpriceExtra+=($rowsDat['realprice']+$rowsDat['renewalcost']*($rowsDat['agelife']-1))*$productnumber;
                    $firstMarketingPriceExtra+=$rowsDat['unit_price']*$productnumber;
                    $firstCostpriceExtra+=$rowsDat['realprice']*$productnumber;
                    $renewalfeeExtra+=($rowsDat['renewalfee']*($rowsDat['agelife']-1))*$productnumber;//续费市场价
                    $realpriceExtra+=($rowsDat['renewalcost']*($rowsDat['agelife']-1))*$productnumber;//续费成本价
                }
            }
        }

        $firstMarketingPrice=$firstMarketingPriceExtra+$firstMarketingPriceTaoCan;
        $firstCostdeduction=$firstCostpriceExtra+$firstCostpriceTaoCan;
        $renewalfee+=$renewalfeeExtra;//续费市场价总价
        $realprice+=$realpriceExtra;//续费成价总价
        //查询 是否为不符合产品  如果工单额外成本存在未 不符合产品 市场价成本扣除数用填写的工单额外成本
        //查询回款对应工单成本   市场价格  外采成本   额外成本
        if(0<$salesorderid){
            $sql = "SELECT SUM(sd.marketprice*if(sd.productnumber>1,sd.productnumber,1)) as marketprice ,SUM(sd.purchasemount*if(sd.productnumber>1,sd.productnumber,1)) as purchasemount , SUM(sd.extracost*if(sd.productnumber>1,sd.productnumber,1)) as extracost FROM vtiger_salesorderproductsrel as sd WHERE sd.salesorderid=? AND sd.multistatus=3 ";
            $isNoMatch = $adb->pquery($sql, array($salesorderid));
        }else {
            if ($rp['multitype'] == 1) {
                $sql = " SELECT SUM(sd.marketprice*if(sd.productnumber>1,sd.productnumber,1)) as marketprice ,SUM(sd.purchasemount*if(sd.productnumber>1,sd.productnumber,1)) as purchasemount , SUM(sd.extracost*if(sd.productnumber>1,sd.productnumber,1)) as extracost FROM vtiger_salesorderrayment as sr ,vtiger_salesorder as so,vtiger_salesorderproductsrel as sd  WHERE sr.receivedpaymentsid=? AND sr.salesorderid=so.salesorderid AND so.servicecontractsid=? AND so.salesorderid=sd.salesorderid  AND   sd.multistatus=3 ";
                $isNoMatch = $adb->pquery($sql, array($receivepayid, $contractid));
            } else {
                $sql = " SELECT SUM(sd.marketprice*if(sd.productnumber>1,sd.productnumber,1)) as marketprice ,SUM(sd.purchasemount*if(sd.productnumber>1,sd.productnumber,1)) as purchasemount , SUM(sd.extracost*if(sd.productnumber>1,sd.productnumber,1)) as extracost FROM vtiger_salesorderproductsrel as sd  WHERE  sd.servicecontractsid=?  AND   sd.multistatus=3 ";
                $isNoMatch = $adb->pquery($sql, array($contractid));
            }
        }
        $isNoMatch=$adb->query_result_rowdata($isNoMatch,0);
        if($isNoMatch['extracost']>0){
            $remark="不符合产品工单";
            $marketingPrice=$isNoMatch['marketprice'];//不符合的标准产品的市场总价
        }else{
            $marketingPrice=$marketingPriceExtra+$marketingPriceTaoCan;//符合标位产品的市场总价
        }
        $costprice=$costpriceExtra+$costpriceTaoCan;//总成本
        $rp['marketprice']=$marketingPrice;//总市场价
        $modulestatus='a_normal';
        //回款时间 即入账日期
        $reality_date=$rp['reality_date'];
        //匹配时间
        if(empty($matchdate)){
            $matchdate=date('Y-m-d');
        }
        $tsiteperformanceoftimeIsExists=1;
        //查询业绩是否下单  查询已扣减成本金额
        if(0<$salesorderid){
            $sqlsalesorder=" SELECT costreduction,performanceoftime,modulestatus FROM vtiger_salesorder WHERE  salesorderid=? AND iscancel=0 LIMIT 1  ";
            $salesorderInfo=$adb->pquery($sqlsalesorder,array($salesorderid));
        }else{
            if($rp['multitype']==1){
                $sqlsalesorder=" SELECT costreduction,performanceoftime,modulestatus  FROM vtiger_salesorder as so,vtiger_salesorderrayment as sd   WHERE  so.servicecontractsid=? AND sd.receivedpaymentsid=? AND sd.salesorderid=so.salesorderid  AND  so.iscancel=0 LIMIT 1  ";
                $salesorderInfo=$adb->pquery($sqlsalesorder,array($contractid,$receivepayid));
            }else{
                $sqlsalesorder=" SELECT  costreduction,performanceoftime,modulestatus  FROM vtiger_salesorder WHERE  servicecontractsid=? AND iscancel=0 LIMIT 1  ";
                $salesorderInfo=$adb->pquery($sqlsalesorder,array($contractid));
            }
        }

        $salesorderInfo=$adb->query_result_rowdata($salesorderInfo,0);
        // 如果工单performanceoftime时间为不存在  或者回款不足不生成到账业绩但是要生成业绩明细记录但是不指定月份 （之后脚本会处理） 则 不生成业绩月份  存在则按当前匹配时间
        if(empty($salesorderInfo['performanceoftime']) || $salesorderInfo['modulestatus']=='c_lackpayment'){
            $achievementmonth=null;
            $tsiteperformanceoftimeIsExists=0;
        }else{
            $achievementmonth=$this->getspecialAchievementmonth($matchdate,$reality_date,$matchdate);
        }
        //成本扣除数
        $costdeduction=$costprice;
        // 新增业务
        if($rp['productid']==2226512){
            $achievementtype='newadd';
            // 新增业务
        }else if(strpos($rp['contract_no'],"VRZ")){
            $achievementtype='newadd';
            //续费业务
        }else if((strpos($rp['contract_no'],"XF")!=false || $rp['servicecontractstype']!='新增') ){
            $achievementtype='renew';
            // 新增业务
        }else{
            $achievementtype='newadd';
        }
        //查询该服务合同分成人
        $queryc=" SELECT vtiger_servicecontracts_divide.*,vtiger_departments.departmentname FROM vtiger_servicecontracts_divide  LEFT JOIN vtiger_departments ON  vtiger_departments.departmentid=vtiger_servicecontracts_divide.signdempart WHERE vtiger_servicecontracts_divide.servicecontractid = ?";
        $resultdatas=$adb->pquery($queryc,array($contractid));
        $insertValueStr='';
        $isTsiteDiscount=0;
        if(in_array($rp['productid'],array(528504,609228,609230,609733,609735))){
            $isTsiteDiscount=1;
        }
        $i=1;
        $remark.="正常匹配Tsite";
        $insertValueStrArray=array();
        while ($rowDatas=$adb->fetch_array($resultdatas)){
            $scalling=$rowDatas['scalling'];
            $businessunit=$total*($scalling/100);
            $receivedpaymentownid=$rowDatas['receivedpaymentownid'];
            $i++;
            $dividetotal=$rp['total']*$scalling/100;
            // 查询分成人 所在部门  以及属事业部查询
            $queryc=" SELECT d.departmentid,d.departmentname,d.parentdepartment,u.last_name FROM vtiger_user2department ud  LEFT JOIN vtiger_departments as d  ON ud.departmentid=d.departmentid LEFT JOIN  vtiger_users as u ON  u.id=ud.userid  WHERE  ud.userid= ? LIMIT 1 ";
            $resultdataDepartment=$adb->pquery($queryc,array($receivedpaymentownid));
            $Department=$adb->query_result_rowdata($resultdataDepartment,0);
            $departmentname=$Department['departmentname'];
            if($Department['departmentid']==$Department['parentdepartment']){
                $groupname=$departmentname;
            }else{
                $str="::".$Department['departmentid'];
                $Department['parentdepartment']= str_replace($str,"",$Department['parentdepartment']);
                $parentdepartment = explode("::",$Department['parentdepartment']);
                $parentdepartmentId = end($parentdepartment);
                //查询父类
                $queryc=" SELECT departmentname FROM vtiger_departments  WHERE  departmentid = ?  limit 1 ";
                $resultdataDepartment=$adb->pquery($queryc,array($parentdepartmentId));
                $Departments=$adb->query_result_rowdata($resultdataDepartment,0);
                $groupname=$Departments['departmentname'];
            }
            $costing=$costingdata['costing'];
            $purchasemount=$costingdata['purchasemount'];
            $extracost = $costingdata['extracost']+$isNoMatch['extracost'];
            // 重新计算沙龙  媒体外采  没接充值  和   其他 （其他包含所有之和）
            $salong=$otherDataTypeArray['沙龙']*($scalling/100);
            $waici=$otherDataTypeArray['外采']*($scalling/100);
            $meijai=$otherDataTypeArray['媒介充值']*($scalling/100);
            //  other 指 回款（沙龙外采媒体充值其他的总和）
            $othercost=$otherdatas['extra_price'];
            //工单成本合计
            $worksheetcost=($costingdata['costing']+$costingdata['purchasemount']+$costingdata['extracost']);
            $divideworksheetcost=$worksheetcost*($scalling/100);
            $dividecosting=$costingdata['costing']*($scalling/100);
            $dividepurchasemount=$costingdata['purchasemount']*($scalling/100);
            $divideextracost=$costingdata['extracost']*($scalling/100);
            $divideother=$othercost*($scalling/100);
            //已分成业绩市场价
            $dividemarketprice=$rp['marketprice']*$scalling/100;
            //已分成成本扣除数
            $dividecostdeduction=$costprice*$scalling/100;
            $other=$othercost;

            // 已分成回款
            $unit_prices=$rp['unit_price']*($scalling/100);
            // Tsite的额外 换成产品上的额外
            $remark.="正常下单";
            //到账业绩 如果是不符合产品
            if($isNoMatch['extracost']>0){
                $deductionremark=$remark;
                // 删除改当前回款已经扣减记录
                $adb->pquery(" DELETE FROM vtiger_oldachievement_hasdeduction WHERE  receivedpaymentsid=? ",array($receivepayid));
                if(0<$salesorderid){
                    $alreadydeduction=" SELECT deductionmoney FROM vtiger_oldachievement_hasdeduction WHERE salesorderid=? AND achievementtype='extra'  ORDER BY  id DESC LIMIT 1  ";
                    $alreadydeduction=$adb->pquery($alreadydeduction,array($salesorderid));
                }else{
                    // 查询已经扣减
                    if($rp['multitype']==1){
                        $alreadydeduction=" SELECT deductionmoney FROM vtiger_oldachievement_hasdeduction WHERE servicecontractsid=? AND salesorderid=?  AND achievementtype='extra'  ORDER BY  id DESC LIMIT 1  ";
                        $alreadydeduction=$adb->pquery($alreadydeduction,array($contractid,$agelife['salesorderid']));
                    }else{
                        $alreadydeduction=" SELECT deductionmoney FROM vtiger_oldachievement_hasdeduction WHERE servicecontractsid=? AND achievementtype='extra'  ORDER BY  id DESC LIMIT 1  ";
                        $alreadydeduction=$adb->pquery($alreadydeduction,array($contractid));
                    }
                }
                $lastExtracost=$isNoMatch['extracost'];
                if($adb->num_rows($alreadydeduction)){
                    $alreadydeduction=$adb->query_result_rowdata($alreadydeduction,0);
                    $lastExtracost=$isNoMatch['extracost']-$alreadydeduction['deductionmoney'];
                }
                if($isMoreYears==1){
                    /*$params['total']=$buysplitcontractamount=$buyFirstMarketingPrice=$firstMarketingPrice;
                    $params['marketprice']=$buysplitmarketprice=$firstMarketingPrice;
                    $params['costdeduction']=$buysplitcost=$firstCostdeduction;
                    $params['unit_price']=$rp['unit_price']*$firstMarketingPrice/$isNoMatch['marketprice'];
                    $params['extracost']=$buyLastExtracost=$lastExtracost;
                    $buyArriveachievement=$Matchreceivements_Record_Model->getArriveachievementTsiteNoMatch($params);
                    $params['total']=$renewsplitcontractamount=$rp['total']-$firstMarketingPrice;//2000
                    $params['marketprice']=$renewsplitmarketprice=$isNoMatch['marketprice']-$firstMarketingPrice;//15000
                    $params['unit_price']=$rp['unit_price']-$params['unit_price'];
                    $params['costdeduction']=$renewsplitcost=$costdeduction-$firstCostdeduction;
                    $params['extracost']=$buyArriveachievement>=0?0:-$buyArriveachievement;
                    $renewArriveachievement=$Matchreceivements_Record_Model->getArriveachievementTsiteNoMatch($params);
                    // 剩余未扣减额外成本
                    $lastExtracost=$renewArriveachievement>=0?0:-$renewArriveachievement;
                    if($isTsiteDiscount==1){
                        if( $rp['total']/$isNoMatch['marketprice']<0.75 ){
                            $deductionremark.="合同金额除市场价格小于0.75";
                            $buyArriveachievement=0;
                            $renewArriveachievement=0;
                        }
                    }*/
                    //续费
                    $extracost=$lastExtracost;
                    $TempData=$Matchreceivements_Record_Model->calcTSiteRenewANDNewAddMoreYear(array(
                        'contractid'=>$contractid,
                        'receivepayid'=>$receivepayid,
                        'salesorderid'=>$salesorderid,//工单的ID
                        'costdeduction'=>$costdeduction,
                        'onemarketrenewprice'=>$renewalfee,//续费市场价
                        'onecostrenewprice'=>$realprice,//续费成本价
                        //'unit_price'=>$rp['unit_price'],//本次回款金额
                        'unit_price'=>$total,//本次回款金额
                        'total'=>$rp['total'],//合同总额
                        'marketprice'=>$rp['marketprice'],//市场总价
                        'extracost'=>$costingdata['extracost'],
                        'extra_price'=>$otherdatas['extra_price'],
                        'lastExtracost'=>$lastExtracost,
                        'isTsiteDiscount'=>$isTsiteDiscount,
                    ),'getArriveachievementTsiteNoMatch');
                    foreach($TempData as $key=>$value){
                        $$key=$value;
                    }
                    $buysplitcost=$costdeduction-$realprice;
                    $renewsplitcost=$realprice;
                    $deductionremark.=$deductionremark1;
                    $extracost-=$lastExtracost;
                    $divideextracost=$extracost*$scalling/100;
                }else{
                    $params['total']=$rp['total'];
                    $params['marketprice']=$isNoMatch['marketprice'];
                    $params['costdeduction']=$costdeduction;
                    $params['unit_price']=$rp['unit_price'];
                    $params['extracost']=$lastExtracost;
                    $arriveachievement=$Matchreceivements_Record_Model->getArriveachievementTsiteNoMatch($params);
                    // 剩余未扣减额外成本
                    $lastExtracost=$arriveachievement>=0?0:-$arriveachievement;
                    if($isTsiteDiscount==1){
                        if($rp['total']/$isNoMatch['marketprice']<0.75){
                            $arriveachievement=0;
                            $remark.="合同金额除市场价格小于0.75";
                        }
                    }
                }
                //update achievementallot
                /*$totalToMarketprice=$rp['total']/$isNoMatch['marketprice'];
                if($totalToMarketprice>1){
                    $totalToMarketprice=1;
                }
                $arriveachievement=$totalToMarketprice*$rp['unit_price']-$rp['unit_price']/$rp['total']*$costdeduction-$isNoMatch['extracost'];*/
                //$arriveachievement=($rp['marketprice']-$costdeduction)*($rp['unit_price']/$rp['total']);
            // 符合产品
            }else{
                if($isMoreYears==1){
                    // 如果是购买
                    /*$params['total']=$buysplitcontractamount=$buyFirstMarketingPrice=$firstMarketingPrice;
                    $params['marketprice']=$buysplitmarketprice=$firstMarketingPrice;
                    $params['unit_price']=$rp['unit_price'];
                    $params['costdeduction']=$buysplitcost=$firstCostdeduction;
                    $buyArriveachievement=$Matchreceivements_Record_Model->getArriveachievementTsiteMatch($params);
                    // 如果是续费
                    $params['total']=$renewsplitcontractamount=$renewContractamount=$rp['total']-$firstMarketingPrice;
                    $params['marketprice']=$renewsplitmarketprice=$renewMarketingPrice=$rp['marketprice']-$firstMarketingPrice;
                    $params['unit_price']=$rp['unit_price'];
                    $params['costdeduction']=$renewsplitcost=$costdeduction-$firstCostdeduction;
                    $renewArriveachievement=$Matchreceivements_Record_Model->getArriveachievementTsiteMatch($params);
                    if($isTsiteDiscount==1){
                        if($rp['total']/$rp['marketprice']<0.75){
                            $buyArriveachievement=0;
                            $renewArriveachievement=0;
                            $remark.="合同金额除市场价格小于0.75";
                        }
                    }*/
                    $TempData=$Matchreceivements_Record_Model->calcTSiteRenewANDNewAddMoreYear(array(
                        'contractid'=>$contractid,
                        'salesorderid'=>$salesorderid,//工单的ID
                        'receivepayid'=>$receivepayid,
                        'costdeduction'=>$costdeduction,
                        'onemarketrenewprice'=>$renewalfee,//续费市场价
                        'onecostrenewprice'=>$realprice,//续费成本价
                        //'unit_price'=>$rp['unit_price'],//本次回款金额
                        'unit_price'=>$total,//本次回款金额
                        'total'=>$rp['total'],//合同总额
                        'marketprice'=>$rp['marketprice'],//市场总价
                        'extracost'=>$costingdata['extracost'],
                        'extra_price'=>$otherdatas['extra_price'],
                        'lastExtracost'=>0,
                        'isTsiteDiscount'=>$isTsiteDiscount,
                    ),'getArriveachievementTsiteMatch');
                    //echo '<pre>';
                    //print_r($TempData);
                    foreach($TempData as $key=>$value){
                        $$key=$value;
                    }
                    $buysplitcost=$costdeduction-$realprice;
                    $renewsplitcost=$realprice;
                    $deductionremark.=$deductionremark1;
                    $remark.=$deductionremark1;

                }else{
                    $remark.="符合非多年单";
                    $params['total']=$rp['total'];
                    $params['marketprice']=$rp['marketprice'];
                    $params['unit_price']=$rp['unit_price'];
                    $params['costdeduction']=$costdeduction;
                    $arriveachievement=$Matchreceivements_Record_Model->getArriveachievementTsiteMatch($params);
                    if($isTsiteDiscount==1){
                        if($rp['total']/$rp['marketprice']<0.75){
                            $arriveachievement=0;
                            $remark.="合同金额除市场价格小于0.75";
                        }
                    }
                }
                /*if($rp['total']>=$rp['marketprice']){
                    $arriveachievement=$rp['unit_price']-$rp['unit_price']/$rp['total']*$costdeduction;
                }else{
                    $arriveachievement=$rp['total']/$rp['marketprice']*$rp['unit_price']-$rp['unit_price']/$rp['total']*$costdeduction;
                }*/
            }
            if($isMoreYears==1){
                $more_years_renew=1;
                for ($i=0;$i<2;$i++){
                    // 拆单首购
                    if($i==0){
                        $arriveachievement=$buyArriveachievement;
                        $updateremark=$remark.$deductionremark;
                        $updateachievementtype='newadd';
                        $splitcontractamount=$buysplitcontractamount;
                        $splitmarketprice=$buysplitmarketprice;
                        $splitcost=$buysplitcost;
                        $others=$other>0?$other:0;
                        $divideothers=$divideother;
                        $splitbusinessunit=$newaddsplitbusinessunit;
                        // 拆单续费
                    }else if($i==1){
                        $arriveachievement=$renewArriveachievement;
                        $updateremark=$remark.$deductionremark;
                        $updateachievementtype='renew';
                        $splitcontractamount=$renewsplitcontractamount;
                        $splitmarketprice=$renewsplitmarketprice;
                        $splitcost=$renewsplitcost;
                        $extracost=0;
                        $divideextracost=0;
                        $others=0;
                        $divideothers=0;
                        $splitbusinessunit=$renewsplitbusinessunit;
                    }
                    // 新单到账业绩 减去pos手续费
                    if($i==0){
                        $arriveachievement=($arriveachievement-$otherdatas['extra_price']);
                    }
                    $producttype=5;
                    $receivedpaymentown=$Department['last_name'];
                    // 最后判断下到账回款 是否为负值 如果为负 则设为零
                    if($arriveachievement < 0 || $tsiteperformanceoftimeIsExists!=1){
                        $arriveachievement=0;
                    }
                    $arriveachievement=$arriveachievement*$scalling/100;
                    $effectiverefund=$rp['unit_price']*$scalling/100;
                    //$worksheetcost=$worksheetcost;
                    // 如果是多年单续费，或者 直接续费单需要计算提成点
                    if($more_years_renew==1 && $updateachievementtype=='renew'){
                        // 查询该客户有几次续费单了
                        $sql="SELECT count(1) as numbers  FROM	vtiger_activationcode AS a LEFT JOIN vtiger_achievementallot_statistic AS aas ON a.contractid = aas.servicecontractid WHERE	a.usercode =?  AND aas.achievementmonth>0  AND a.contractid <> ? AND ( aas.achievementtype = 'renew' OR ( aas.achievementtype = 'newadd' and  aas.more_years_renew=1 ) )GROUP BY a.contractid";
                        $severalRenewals=$adb->pquery($sql,array($rp['usercode'],$contractid));
                        $severalRenewals=$adb->num_rows($severalRenewals);
                        $severalRenewals=$severalRenewals;
                        $renewal_commission=6*pow(0.5 ,$severalRenewals);
                        $renewtimes=$severalRenewals+1;
                        $commissionforrenewal=$arriveachievement*$renewal_commission/100;
                    }else{
                        $renewal_commission=0;
                        $renewtimes=0;
                        $commissionforrenewal=0;
                    }
                    $datavalue['owncompanys']=$rowDatas['owncompanys'];
                    $datavalue['receivedpaymentownid']=$receivedpaymentownid;
                    $datavalue['scalling']=$scalling;
                    $datavalue['servicecontractid']=$rowDatas['servicecontractid'];
                    $datavalue['receivedpaymentsid']=$receivepayid;
                    $datavalue['businessunit']=$businessunit;
                    $datavalue['matchdate']=$matchdate;
                    $datavalue['departmentid']=$Department['departmentid']?$Department['departmentid']:0;
                    $datavalue['owncompany']=$rp['owncompany'];
                    $datavalue['createtime']=$rp['createtime'];
                    $datavalue['reality_date']=$rp['reality_date'];
                    $datavalue['paytitle']=$rp['paytitle'];
                    $datavalue['unit_price']=$rp['unit_price'];
                    $datavalue['unit_prices']=$unit_prices;
                    $datavalue['department']=$rowDatas['departmentname']?$rowDatas['departmentname']:'';
                    $datavalue['groupname']=$groupname?$groupname:' ';
                    $datavalue['departmentname']=$departmentname?$departmentname:' ';
                    $datavalue['receivedpaymentown']=$receivedpaymentown;
                    $datavalue['servicecontractstype']=$rp['servicecontractstype']?$rp['servicecontractstype']:' ';
                    $datavalue['accountname']=$rp['accountname']?$rp['accountname']:' ';
                    $datavalue['signdate']=$rp['signdate'];
                    $datavalue['contract_no']=$rp['contract_no'];
                    $datavalue['total']=$rp['total']?$rp['total']:0;
                    $datavalue['dividetotal']=$dividetotal;
                    $datavalue['costing']=$costing;
                    $datavalue['purchasemount']=$purchasemount?$purchasemount:0;
                    $datavalue['worksheetcost']=$worksheetcost?$worksheetcost:0;
                    $datavalue['productlife']=$agelife['agelife']?$agelife['agelife']:0;
                    $datavalue['marketprice']=$rp['marketprice']?$rp['marketprice']:0;
                    $datavalue['dividemarketprice']=$dividemarketprice;
                    $datavalue['costdeduction']=$costdeduction;
                    $datavalue['dividecostdeduction']=$dividecostdeduction;
                    $datavalue['other']=$others;
                    $datavalue['effectiverefund']=$effectiverefund;
                    $datavalue['arriveachievement']=$arriveachievement;
                    $datavalue['achievementmonth']=$achievementmonth;
                    $datavalue['modulestatus']=$modulestatus;
                    $datavalue['productname']=$productname;
                    $datavalue['achievementtype']=$updateachievementtype?$updateachievementtype:0;
                    $datavalue['producttype']=$producttype?$producttype:0;
                    $datavalue['extracost']=$extracost;
                    $datavalue['salong']=$salong;
                    $datavalue['waici']=$waici;
                    $datavalue['meijai']=$meijai;
                    $datavalue['othercost']=$othercost;
                    $datavalue['shareuser']=$shareuser;
                    $datavalue['remarks']=$updateremark;
                    $datavalue['generatedamount']=$generatedamount;
                    $datavalue['adjustbeforearriveachievement']=$arriveachievement;
                    $datavalue['divideworksheetcost']=$divideworksheetcost;
                    $datavalue['dividecosting']=$dividecosting;
                    $datavalue['dividepurchasemount']=$dividepurchasemount;
                    $datavalue['divideextracost']=$divideextracost;
                    $datavalue['divideother']=$divideothers;
                    $datavalue['more_years_renew']=$more_years_renew;
                    $datavalue['renewal_commission']=$renewal_commission;
                    $datavalue['renewtimes']=$renewtimes;
                    $datavalue['splitcontractamount']=$splitcontractamount;
                    $datavalue['splitmarketprice']=$splitmarketprice;
                    $datavalue['splitcost']=$splitcost;
                    $datavalue['commissionforrenewal']=$commissionforrenewal;
                    $datavalue['splitbusinessunit']=$splitbusinessunit;//拆分回款
                    $datavalue['salesorderid']=$salesorderid;//工单ID
                    $insertValueStrArray[]=$datavalue;
                    //$insertValueStr.='(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?),';

                }
            }else{
                // 到账业绩 减去pos手续费
                $arriveachievement=($arriveachievement-$otherdatas['extra_price']);
                $producttype=5;
                $receivedpaymentown=$Department['last_name'];
                // 最后判断下到账回款 是否为负值 如果为负 则设为零
                if($arriveachievement < 0 || $tsiteperformanceoftimeIsExists!=1){
                    $arriveachievement=0;
                }
                $arriveachievement=$arriveachievement*$scalling/100;
                $effectiverefund=$rp['unit_price']*$scalling/100;
                $worksheetcost=$worksheetcost;
                $more_years_renew=0;
                $renewal_commission=0;
                $renewtimes=0;
                $splitcontractamount=0;
                $splitmarketprice=0;
                $splitcost=0;
                $commissionforrenewal=0;
                $datavalue['owncompanys']=$rowDatas['owncompanys'];
                $datavalue['receivedpaymentownid']=$receivedpaymentownid;
                $datavalue['scalling']=$scalling;
                $datavalue['servicecontractid']=$rowDatas['servicecontractid'];
                $datavalue['receivedpaymentsid']=$receivepayid;
                $datavalue['businessunit']=$businessunit;
                $datavalue['matchdate']=$matchdate;
                $datavalue['departmentid']=$Department['departmentid']?$Department['departmentid']:0;
                $datavalue['owncompany']=$rp['owncompany'];
                $datavalue['createtime']=$rp['createtime'];
                $datavalue['reality_date']=$rp['reality_date'];
                $datavalue['paytitle']=$rp['paytitle'];
                $datavalue['unit_price']=$rp['unit_price'];
                $datavalue['unit_prices']=$unit_prices;
                $datavalue['department']=$rowDatas['departmentname']?$rowDatas['departmentname']:'';
                $datavalue['groupname']=$groupname?$groupname:' ';
                $datavalue['departmentname']=$departmentname?$departmentname:' ';
                $datavalue['receivedpaymentown']=$receivedpaymentown;
                $datavalue['servicecontractstype']=$rp['servicecontractstype']?$rp['servicecontractstype']:' ';
                $datavalue['accountname']=$rp['accountname']?$rp['accountname']:' ';
                $datavalue['signdate']=$rp['signdate'];
                $datavalue['contract_no']=$rp['contract_no'];
                $datavalue['total']=$rp['total']?$rp['total']:0;
                $datavalue['dividetotal']=$dividetotal;
                $datavalue['costing']=$costing;
                $datavalue['purchasemount']=$purchasemount?$purchasemount:0;
                $datavalue['worksheetcost']=$worksheetcost?$worksheetcost:0;
                $datavalue['productlife']=$agelife['agelife']?$agelife['agelife']:0;
                $datavalue['marketprice']=$rp['marketprice']?$rp['marketprice']:0;
                $datavalue['dividemarketprice']=$dividemarketprice;
                $datavalue['costdeduction']=$costdeduction;
                $datavalue['dividecostdeduction']=$dividecostdeduction;
                $datavalue['other']=$other;
                $datavalue['effectiverefund']=$effectiverefund;
                $datavalue['arriveachievement']=$arriveachievement;
                $datavalue['achievementmonth']=$achievementmonth;
                $datavalue['modulestatus']=$modulestatus;
                $datavalue['productname']=$productname;
                $datavalue['achievementtype']=$achievementtype?$achievementtype:0;
                $datavalue['producttype']=$producttype?$producttype:0;
                $datavalue['extracost']=$extracost;
                $datavalue['salong']=$salong;
                $datavalue['waici']=$waici;
                $datavalue['meijai']=$meijai;
                $datavalue['othercost']=$othercost;
                $datavalue['shareuser']=$shareuser;
                $datavalue['remarks']=$remark;
                $datavalue['generatedamount']=$generatedamount;
                $datavalue['adjustbeforearriveachievement']=$arriveachievement;
                $datavalue['divideworksheetcost']=$divideworksheetcost;
                $datavalue['dividecosting']=$dividecosting;
                $datavalue['dividepurchasemount']=$dividepurchasemount;
                $datavalue['divideextracost']=$divideextracost;
                $datavalue['divideother']=$divideother;
                $datavalue['more_years_renew']=$more_years_renew;
                $datavalue['renewal_commission']=$renewal_commission;
                $datavalue['renewtimes']=$renewtimes;
                $datavalue['splitcontractamount']=$splitcontractamount;
                $datavalue['splitmarketprice']=$splitmarketprice;
                $datavalue['splitcost']=$splitcost;
                $datavalue['commissionforrenewal']=$commissionforrenewal;
                $datavalue['splitbusinessunit']=$unit_prices;
                $datavalue['salesorderid']=$salesorderid;//工单ID
                $insertValueStrArray[]=$datavalue;
                //$insertValueStr.='(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?),';
            }
        }
        // 如果是不符合 则添加已经扣减记录
        if($isNoMatch['extracost']>0 && !empty($salesorderInfo['performanceoftime'])){
            //已经扣减参数额外成本参数
            $param=array('contractid'=>$contractid,'oldcontractid'=>0,'receivepayid'=>$receivepayid,'alreadydeduction'=>$isNoMatch['extracost']-$lastExtracost,'totaldeductionmoney'=>$isNoMatch['extracost'],'deductionremark'=>$deductionremark,'listAchievementType'=>'extra');
            // 如果是多工单
            if($rp['multitype']==1){
                $param['salesorderid']=$agelife['salesorderid'];
            }
            if(0<$salesorderid){
                $param['salesorderid']=$salesorderid;
            }
            $this->hasdeduction($param,$adb);
        }
        return array_merge($this->returnDataValue($insertValueStrArray),array('isTyun'=>0));
        return array("datavalue"=>$datavalue,"insertValueStr"=>$insertValueStr,'isTyun'=>0);
    }
    /**
     * 非TYun的产品没给的产品走这里计算
     */
    public function othersExtraCalculationAchievement($rp,$contractid,$receivepayid,$shareuser,$total,$currentid,$remark,$adb,$matchdate=0,$salesorderid=0){
        $generatedamount=0;
        //②查询非Tyun类年限
        if($rp['multitype']==1){
            $queryc=" SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife FROM vtiger_salesorderproductsrel,vtiger_salesorderrayment WHERE vtiger_salesorderproductsrel.servicecontractsid=? AND vtiger_salesorderrayment.receivedpaymentsid=? AND vtiger_salesorderproductsrel.salesorderid=vtiger_salesorderrayment.salesorderid AND multistatus in(0,1) LIMIT 1";
            $resultdatapayments=$adb->pquery($queryc,array($rp['servicecontractsid'],$receivepayid));
        }else{
            $queryc=" SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid= ? AND multistatus in(0,1) LIMIT 1";
            $resultdatapayments=$adb->pquery($queryc,array($rp['servicecontractsid']));
        }
        $agelife=$adb->query_result_rowdata($resultdatapayments,0);
        $marketingPrice=0;
        $costprice=0;
        $remark.="非T云类通用公式";
        //人力成本   工单外采成本  额外成本
        if($rp['multitype']==1){
            $queryc="SELECT IFNULL(sum(vtiger_salesorderproductsrel.costing),0) AS costing,IFNULL(sum(vtiger_salesorderproductsrel.purchasemount),0) AS purchasemount,IFNULL(sum(vtiger_salesorderproductsrel.extracost),0) AS extracost FROM vtiger_salesorderproductsrel,vtiger_salesorderrayment WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND vtiger_salesorderrayment.receivedpaymentsid=? AND vtiger_salesorderproductsrel.salesorderid=vtiger_salesorderrayment.salesorderid AND multistatus=3 ";
            $costingdata=$adb->pquery($queryc,array($contractid,$receivepayid));
        }else{
            $queryc="SELECT IFNULL(sum(vtiger_salesorderproductsrel.costing),0) AS costing,IFNULL(sum(vtiger_salesorderproductsrel.purchasemount),0) AS purchasemount,IFNULL(sum(vtiger_salesorderproductsrel.extracost),0) AS extracost FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND multistatus=3 ";
            $costingdata=$adb->pquery($queryc,array($contractid));
        }
        $costingdata=$adb->query_result_rowdata($costingdata,0);
        // 有关 沙龙 外采 媒介充值 其他
        $queryc="SELECT extra_type,extra_price FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid=? ";
        $otherdata=$adb->pquery($queryc,array($receivepayid));
        $otherdata=$adb->query_result_rowdata($otherdata,0);
        $otherDataTypeArray=array();
        foreach ($otherdata as $key=>$val){
            $otherDataTypeArray[$val['extra_type']]=$otherDataTypeArray[$val['extra_type']]+$val['extra_price'];
        }
        //该服务合同回款相关的总的之和
        $queryc="SELECT SUM(extra_price) as extra_price FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid =? ";
        $otherdatas=$adb->pquery($queryc,array($receivepayid));
        $otherdatas=$adb->query_result_rowdata($otherdatas,0);
        //查询该服务合同对应的产品
        if($rp['multitype']==1){
            $queryc=" SELECT p.productid,p.productname,ssp.productcomboid,ssp.thepackage,count(1) as counts FROM vtiger_salesorderproductsrel as ssp LEFT JOIN vtiger_products as p ON p.productid=ssp.productid LEFT JOIN vtiger_salesorderrayment as sd ON sd.salesorderid=ssp.salesorderid   WHERE ssp.servicecontractsid =? AND sd.receivedpaymentsid=? AND ssp.multistatus=3 group by ssp.productcomboid ";
            $productdatas=$adb->pquery($queryc,array($contractid,$receivepayid));
        }else{
            $queryc=" SELECT p.productid,p.productname,ssp.productcomboid,ssp.thepackage,count(1) as counts FROM vtiger_salesorderproductsrel as ssp LEFT JOIN vtiger_products as p ON p.productid=ssp.productid  WHERE ssp.servicecontractsid =? AND ssp.multistatus=3 group by ssp.productcomboid ";
            $productdatas=$adb->pquery($queryc,array($contractid));
        }
        $productname='';
        while ($rowDatas=$adb->fetch_array($productdatas)){
            if($rowDatas['productid']==$rp['productid']){
                $productname.=$rowDatas['thepackage']."(1),";
            }else{
                $productname.=$rowDatas['thepackage']."(".$rowDatas['counts']."),";
            }
        }
        $productname=trim($productname,',');
        // 查询市场价格
        /*$queryc=" SELECT pmarketprice,realmarketprice,FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife, (costing+purchasemount+extracost)as costprice ,costofuse FROM vtiger_salesorderproductsrel  WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND multistatus=3  ";
        $price=$adb->pquery($queryc,array($contractid));
        while ($rowDatas=$adb->fetch_array($price)){
            if((strpos($rp['contract_no'],"XF")!=false || $rp['servicecontractstype']!='新增')){
                $marketingPrice+=$rowDatas['pmarketprice']*$rowDatas['agelife'];
                $costprice+=$rowDatas['costofuse']*$rowDatas['agelife'];
            }else{
                $marketingPrice+=$rowDatas['realmarketprice']+$rowDatas['renewmarketprice']*($rowDatas['agelife']-1);
                $costprice+=$rowDatas['costprice']+$rowDatas['renewcostprice']*($rowDatas['agelife']-1);
            }
        }*/
        //查询市场价格
        //① 查询套餐市场价 和 成本
        $marketingPriceTaoCan=0;
        $costpriceTaoCan=0;
        if(!empty($rp['productid'])){
            if($rp['multitype']==1){
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid LEFT JOIN vtiger_salesorderrayment as sd ON sd.salesorderid=vtiger_salesorderproductsrel.salesorderid  WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND  sd.receivedpaymentsid=?  AND productcomboid IN(".$rp['productid'].") AND multistatus=3  LIMIT  1  ";
                $product=$adb->pquery($sql,array($contractid,$receivepayid));
            }else{
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid  WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND productcomboid IN(".$rp['productid'].") AND multistatus=3 LIMIT  1 ";
                $product=$adb->pquery($sql,array($contractid));
            }
            while ($rowsDat=$adb->fetch_array($product)) {
                if ((strpos($rp['contract_no'], "XF") != false || $rp['servicecontractstype'] != '新增')) {
                    $marketingPriceTaoCan += $rowsDat['renewalfee'] * $rowsDat['agelife'];
                    $costpriceTaoCan += $rowsDat['renewalcost'] * $rowsDat['agelife'];
                } else {
                    $marketingPriceTaoCan += $rowsDat['unit_price'] + $rowsDat['renewalfee'] * ($rowsDat['agelife'] - 1);
                    $costpriceTaoCan += $rowsDat['realprice'] + $rowsDat['renewalcost'] * ($rowsDat['agelife'] - 1);
                }
            }
        }
        //② 额外产品市场价  和 成本
        $marketingPriceExtra=0;
        $costpriceExtra=0;
        if(!empty($rp['extraproductid'])){
            if($rp['multitype']==1){
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid LEFT JOIN vtiger_salesorderrayment as sd ON sd.salesorderid=vtiger_salesorderproductsrel.salesorderid WHERE vtiger_salesorderproductsrel.servicecontractsid =?  AND sd.receivedpaymentsid=?  AND productcomboid IN(".$rp['extraproductid'].") AND multistatus=3  ";
                $product=$adb->pquery($sql,array($contractid,$receivepayid));
            }else{
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid  WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND productcomboid IN(".$rp['extraproductid'].") AND multistatus=3  ";
                $product=$adb->pquery($sql,array($contractid));
            }
            while ($rowsDat=$adb->fetch_array($product)){
                if((strpos($rp['contract_no'],"XF")!=false || $rp['servicecontractstype']!='新增')){
                    $marketingPriceExtra+=$rowsDat['renewalfee']*$rowsDat['agelife'];
                    $costpriceExtra+=$rowsDat['renewalcost']*$rowsDat['agelife'];
                }else{
                    $marketingPriceExtra+=$rowsDat['unit_price']+$rowsDat['renewalfee']*($rowsDat['agelife']-1);
                    $costpriceExtra+=$rowsDat['realprice']+$rowsDat['renewalcost']*($rowsDat['agelife']-1);
                }
            }
        }
        $marketingPrice=$marketingPriceExtra+$marketingPriceTaoCan;
        $costprice=$costpriceExtra+$costpriceTaoCan;
        $rp['marketprice']=$marketingPrice;
        //非saas类获取市场价 虽然业绩计算公式和市场价无关但还是查下 start
        $rp['servicecontractid']=$contractid;
        $rp['receivedpaymentsid']=$receivepayid;
        $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
        $dataResult=$Matchreceivements_Record_Model->noSaaSGetMarketing($rp);
        if($dataResult>0){
            $rp['marketprice']=$dataResult;
        }
        // 非saas类获取市场价 虽然业绩计算公式和市场价无关但还是查下  end
        $modulestatus='a_normal';
        //回款时间 即入账日期
        $reality_date=$rp['reality_date'];
        //匹配时间
        if(empty($matchdate)){
            $matchdate=date('Y-m-d');
        }
        //查询业绩是否下单  查询已扣减成本金额
        if($rp['multitype']==1){
            $sqlsalesorder=" SELECT costreduction,performanceoftime  FROM vtiger_salesorder as so,vtiger_salesorderrayment as sd   WHERE  so.servicecontractsid=? AND sd.receivedpaymentsid=? AND sd.salesorderid=so.salesorderid  AND  so.iscancel=0 LIMIT 1  ";
            $salesorderInfo=$adb->pquery($sqlsalesorder,array($contractid,$receivepayid));
        }else{
            $sqlsalesorder=" SELECT  costreduction,performanceoftime  FROM vtiger_salesorder WHERE  servicecontractsid=? AND iscancel=0 LIMIT 1  ";
            $salesorderInfo=$adb->pquery($sqlsalesorder,array($contractid));
        }
        $salesorderInfo=$adb->query_result_rowdata($salesorderInfo,0);
        if(empty($salesorderInfo['performanceoftime'])){
            $achievementmonth=null;
            $tsiteperformanceoftimeIsExists=0;
        }else{
            $achievementmonth=$this->getspecialAchievementmonth($matchdate,$reality_date,$matchdate);
        }

        $tsiteperformanceoftimeIsExists=1;
        //成本扣除数
        $costdeduction=$costprice;
        // 新增业务
        if($rp['productid']==2226512){
            $achievementtype='newadd';
            // 新增业务
        }else if(strpos($rp['contract_no'],"VRZ")){
            $achievementtype='newadd';
            //续费业务
        }else if((strpos($rp['contract_no'],"XF")!=false || $rp['servicecontractstype']!='新增') ){
            $achievementtype='renew';
            // 新增业务
        }else{
            $achievementtype='newadd';
        }
        //工单成本合计
        $worksheetcost=($costingdata['costing']+$costingdata['purchasemount']+$costingdata['extracost']);
        //查询该服务合同分成人
        $queryc=" SELECT vtiger_servicecontracts_divide.*,vtiger_departments.departmentname FROM vtiger_servicecontracts_divide  LEFT JOIN vtiger_departments ON  vtiger_departments.departmentid=vtiger_servicecontracts_divide.signdempart WHERE vtiger_servicecontracts_divide.servicecontractid = ?";
        $resultdatas=$adb->pquery($queryc,array($contractid));
        $insertValueStr='';
        $i=1;
        while ($rowDatas=$adb->fetch_array($resultdatas)){

            $scalling=$rowDatas['scalling'];
            $businessunit=$total*($scalling/100);
            $receivedpaymentownid=$rowDatas['receivedpaymentownid'];

            $i++;
            $dividetotal=$rp['total']*$scalling/100;
            // 查询分成人 所在部门  以及属事业部查询
            $queryc=" SELECT d.departmentid,d.departmentname,d.parentdepartment,u.last_name FROM vtiger_user2department ud  LEFT JOIN vtiger_departments as d  ON ud.departmentid=d.departmentid LEFT JOIN  vtiger_users as u ON  u.id=ud.userid  WHERE  ud.userid= ? LIMIT 1 ";
            $resultdataDepartment=$adb->pquery($queryc,array($receivedpaymentownid));
            $Department=$adb->query_result_rowdata($resultdataDepartment,0);
            $departmentname=$Department['departmentname'];
            if($Department['departmentid']==$Department['parentdepartment']){
                $groupname=$departmentname;
            }else{
                $str="::".$Department['departmentid'];
                $Department['parentdepartment']= str_replace($str,"",$Department['parentdepartment']);
                $parentdepartment = explode("::",$Department['parentdepartment']);
                $parentdepartmentId = end($parentdepartment);
                //查询父类
                $queryc=" SELECT departmentname FROM vtiger_departments  WHERE  departmentid = ?  limit 1 ";
                $resultdataDepartment=$adb->pquery($queryc,array($parentdepartmentId));
                $Departments=$adb->query_result_rowdata($resultdataDepartment,0);
                $groupname=$Departments['departmentname'];
            }
            $costing=$costingdata['costing'];
            $purchasemount=$costingdata['purchasemount'];
            $extracost = $costingdata['extracost'];
            // 重新计算沙龙  媒体外采  没接充值  和   其他 （其他包含所有之和）
            $salong=$otherDataTypeArray['沙龙']*($scalling/100);
            $waici=$otherDataTypeArray['外采']*($scalling/100);
            $meijai=$otherDataTypeArray['媒介充值']*($scalling/100);
            //  other 指 回款（沙龙外采媒体充值其他的总和）
            $othercost=$otherdatas['extra_price'];
            $divideworksheetcost=$worksheetcost*($scalling/100);
            $dividecosting=$costingdata['costing']*($scalling/100);
            $dividepurchasemount=$costingdata['purchasemount']*($scalling/100);
            $divideextracost=$costingdata['extracost']*($scalling/100);
            $divideother=$othercost*($scalling/100);
            //已分成业绩市场价
            $dividemarketprice=$rp['marketprice']*$scalling/100;
            //已分成成本扣除数
            $dividecostdeduction=$costprice*$scalling/100;
            $other=$othercost;
            //已分成回款
            $unit_prices=$rp['unit_price']*($scalling/100);
            $remark.="工单总成本（".$worksheetcost."）"."已扣减成本（".$salesorderInfo['costreduction']."）";
            //到账业绩
            $arriveachievement=($rp['unit_price']-($worksheetcost-$salesorderInfo['costreduction']))*$scalling/100;
            $arriveachievement=$arriveachievement-$otherdatas['extra_price']*$scalling/100;
            if($arriveachievement< 0 ){
                $arriveachievement=0;
            }

            $producttype=3;
            $receivedpaymentown=$Department['last_name'];
            // 最后判断下到账回款 是否为负值 如果为负 则设为零
            if($arriveachievement < 0 || $tsiteperformanceoftimeIsExists!=1){
                $arriveachievement=0;
            }
            $worksheetcosts=$worksheetcost;
            $effectiverefund=$arriveachievement;//到账业绩等于有效回效
            $more_years_renew=0;
            $renewal_commission=0;
            $renewtimes=0;
            $splitcontractamount=0;
            $splitmarketprice=0;
            $splitcost=0;
            $commissionforrenewal=0;
            $datavalue[]=$rowDatas['owncompanys'];
            $datavalue[]=$receivedpaymentownid;
            $datavalue[]=$scalling;
            $datavalue[]=$rowDatas['servicecontractid'];
            $datavalue[]=$receivepayid;
            $datavalue[]=$businessunit;
            $datavalue[]=$matchdate;
            $datavalue[]=$Department['departmentid']?$Department['departmentid']:0;
            $datavalue[]=$rp['owncompany'];
            $datavalue[]=$rp['createtime'];
            $datavalue[]=$rp['reality_date'];
            $datavalue[]=$rp['paytitle'];
            $datavalue[]=$rp['unit_price'];
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
            $datavalue[]=$worksheetcosts?$worksheetcosts:0;
            $datavalue[]=$agelife['agelife']?$agelife['agelife']:0;
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
            $datavalue[]=$generatedamount;$datavalue[]=$arriveachievement;$datavalue[]=$divideworksheetcost;$datavalue[]=$dividecosting;$datavalue[]=$dividepurchasemount;$datavalue[]=$divideextracost;$datavalue[]=$divideother;$datavalue[]=$more_years_renew;$datavalue[]=$renewal_commission;
            $datavalue[]=$renewtimes;$datavalue[]=$splitcontractamount;$datavalue[]=$splitmarketprice;$datavalue[]=$splitcost;$datavalue[]=$commissionforrenewal;
            $insertValueStr.='(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?),';
        }
		// 如果工单成本 没有审核通过没有生成时间则不更改已扣减成本
        if(!empty($salesorderInfo['performanceoftime'])) {
			//如果已减工单成本 + 目前回款金额 大于等于总成本说明 工单成本已经减完了 则直接把已减成本改成 总成本就ok了
            if($rp['multitype']==1){
                if ($rp['unit_price'] + $salesorderInfo['costreduction'] > $worksheetcost) {
                    //更新已减成本
                    $updatesql = " UPDATE vtiger_salesorder SET costreduction=? WHERE servicecontractsid =? AND salesorderid=(SELECT salesorderid FROM vtiger_salesorderrayment WHERE receivedpaymentsid=? LIMIT 1 ) ";
                    $adb->pquery($updatesql, array($worksheetcost, $contractid,$receivepayid));
                } else {

                    //更新已减成本
                    $updatesql = " UPDATE vtiger_salesorder SET costreduction=costreduction+? WHERE servicecontractsid =? AND salesorderid=(SELECT salesorderid FROM vtiger_salesorderrayment WHERE receivedpaymentsid=? LIMIT 1 ) ";
                    $adb->pquery($updatesql, array($rp['unit_price'], $contractid,$receivepayid));
                }
            }else{
                if($rp['unit_price']+$salesorderInfo['costreduction']>$worksheetcost){
                    //更新已减成本
                    $updatesql = " UPDATE vtiger_salesorder SET costreduction=? WHERE servicecontractsid =? ";
                    $adb->pquery($updatesql,array($worksheetcost,$contractid));
                }else{
                    //更新已减成本
                    $updatesql = " UPDATE vtiger_salesorder SET costreduction=costreduction+? WHERE servicecontractsid =? ";
                    $adb->pquery($updatesql,array($rp['unit_price'],$contractid));
                }
            }
		}
        return array("datavalue"=>$datavalue,"insertValueStr"=>$insertValueStr,'isTyun'=>0);
    }

    /**
     * T云系列补充协议（非标）
     */
    public function tYunNonstandardCalculationAchievement($rp,$contractid,$receivepayid,$shareuser,$total,$currentid,$remark,$adb,$matchdate=0){
        $generatedamount=0;
        //②查询非Tyun类年限
        if($rp['multitype']==1){
            $queryc=" SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife FROM vtiger_salesorderproductsrel,vtiger_salesorderrayment WHERE vtiger_salesorderproductsrel.servicecontractsid=? AND vtiger_salesorderrayment.receivedpaymentsid=? AND vtiger_salesorderproductsrel.salesorderid=vtiger_salesorderrayment.salesorderid AND multistatus in(0,1) LIMIT 1";
            $resultdatapayments=$adb->pquery($queryc,array($rp['servicecontractsid'],$receivepayid));
        }else{
            $queryc=" SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid= ? AND multistatus in(0,1) LIMIT 1";
            $resultdatapayments=$adb->pquery($queryc,array($rp['servicecontractsid']));
        }
        $agelife=$adb->query_result_rowdata($resultdatapayments,0);
        $remark.="T云系列补充协议（非标）";
        //人力成本   工单外采成本  额外成本
        if($rp['multitype']==1){
            $queryc="SELECT IFNULL(sum(vtiger_salesorderproductsrel.costing),0) AS costing,IFNULL(sum(vtiger_salesorderproductsrel.purchasemount),0) AS purchasemount,IFNULL(sum(vtiger_salesorderproductsrel.extracost),0) AS extracost FROM vtiger_salesorderproductsrel,vtiger_salesorderrayment WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND vtiger_salesorderrayment.receivedpaymentsid=? AND vtiger_salesorderproductsrel.salesorderid=vtiger_salesorderrayment.salesorderid AND multistatus=3 ";
            $costingdata=$adb->pquery($queryc,array($contractid,$receivepayid));
        }else{
            $queryc="SELECT IFNULL(sum(vtiger_salesorderproductsrel.costing),0) AS costing,IFNULL(sum(vtiger_salesorderproductsrel.purchasemount),0) AS purchasemount,IFNULL(sum(vtiger_salesorderproductsrel.extracost),0) AS extracost FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND multistatus=3 ";
            $costingdata=$adb->pquery($queryc,array($contractid));
        }
        $costingdata=$adb->query_result_rowdata($costingdata,0);
        // 有关 沙龙 外采 媒介充值 其他
        $queryc="SELECT extra_type,extra_price FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid=? ";
        $otherdata=$adb->pquery($queryc,array($receivepayid));
        $otherdata=$adb->query_result_rowdata($otherdata,0);
        $otherDataTypeArray=array();
        foreach ($otherdata as $key=>$val){
            $otherDataTypeArray[$val['extra_type']]=$otherDataTypeArray[$val['extra_type']]+$val['extra_price'];
        }
        //该服务合同回款相关的总的之和
        $queryc="SELECT SUM(extra_price) as extra_price FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid =? ";
        $otherdatas=$adb->pquery($queryc,array($receivepayid));
        $otherdatas=$adb->query_result_rowdata($otherdatas,0);

        //查询该服务合同对应的产品
        if($rp['multitype']==1){
            $queryc=" SELECT p.productid,p.productname,ssp.productcomboid,ssp.thepackage,count(1) as counts FROM vtiger_salesorderproductsrel as ssp LEFT JOIN vtiger_products as p ON p.productid=ssp.productid LEFT JOIN vtiger_salesorderrayment as sd ON sd.salesorderid=ssp.salesorderid   WHERE ssp.servicecontractsid =? AND sd.receivedpaymentsid=? AND ssp.multistatus=3 group by ssp.productcomboid ";
            $productdatas=$adb->pquery($queryc,array($contractid,$receivepayid));
        }else{
            $queryc=" SELECT p.productid,p.productname,ssp.productcomboid,ssp.thepackage,count(1) as counts FROM vtiger_salesorderproductsrel as ssp LEFT JOIN vtiger_products as p ON p.productid=ssp.productid  WHERE ssp.servicecontractsid =? AND ssp.multistatus=3 group by ssp.productcomboid ";
            $productdatas=$adb->pquery($queryc,array($contractid));
        }
        $productname='';
        while ($rowDatas=$adb->fetch_array($productdatas)){
            if($rowDatas['productid']==$rp['productid']){
                $productname.=$rowDatas['thepackage']."(1),";
            }else{
                $productname.=$rowDatas['thepackage']."(".$rowDatas['counts']."),";
            }
        }
        $productname=trim($productname,',');
        //查询市场价格
        //① 查询套餐市场价 和 成本
        $marketingPriceTaoCan=0;
        $costpriceTaoCan=0;
        if(!empty($rp['productid'])){
            if($rp['multitype']==1){
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid LEFT JOIN vtiger_salesorderrayment as sd ON sd.salesorderid=vtiger_salesorderproductsrel.salesorderid  WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND  sd.receivedpaymentsid=?  AND productcomboid IN(".$rp['productid'].") AND multistatus=3  LIMIT  1  ";
                $product=$adb->pquery($sql,array($contractid,$receivepayid));
            }else{
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid  WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND productcomboid IN(".$rp['productid'].") AND multistatus=3 LIMIT  1 ";
                $product=$adb->pquery($sql,array($contractid));
            }
            while ($rowsDat=$adb->fetch_array($product)) {
                if ((strpos($rp['contract_no'], "XF") != false || $rp['servicecontractstype'] != '新增')) {
                    $marketingPriceTaoCan += $rowsDat['renewalfee'] * $rowsDat['agelife'];
                    $costpriceTaoCan += $rowsDat['renewalcost'] * $rowsDat['agelife'];
                } else {
                    $marketingPriceTaoCan += $rowsDat['unit_price'] + $rowsDat['renewalfee'] * ($rowsDat['agelife'] - 1);
                    $costpriceTaoCan += $rowsDat['realprice'] + $rowsDat['renewalcost'] * ($rowsDat['agelife'] - 1);
                }
            }
        }
        //② 额外产品市场价  和 成本
        $marketingPriceExtra=0;
        $costpriceExtra=0;
        if(!empty($rp['extraproductid'])){
            if($rp['multitype']==1){
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid LEFT JOIN vtiger_salesorderrayment as sd ON sd.salesorderid=vtiger_salesorderproductsrel.salesorderid WHERE vtiger_salesorderproductsrel.servicecontractsid =?  AND sd.receivedpaymentsid=?  AND productcomboid IN(".$rp['extraproductid'].") AND multistatus=3  ";
                $product=$adb->pquery($sql,array($contractid,$receivepayid));
            }else{
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid  WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND productcomboid IN(".$rp['extraproductid'].") AND multistatus=3  ";
                $product=$adb->pquery($sql,array($contractid));
            }
            while ($rowsDat=$adb->fetch_array($product)){
                if((strpos($rp['contract_no'],"XF")!=false || $rp['servicecontractstype']!='新增')){
                    $marketingPriceExtra+=$rowsDat['renewalfee']*$rowsDat['agelife'];
                    $costpriceExtra+=$rowsDat['renewalcost']*$rowsDat['agelife'];
                }else{
                    $marketingPriceExtra+=$rowsDat['unit_price']+$rowsDat['renewalfee']*($rowsDat['agelife']-1);
                    $costpriceExtra+=$rowsDat['realprice']+$rowsDat['renewalcost']*($rowsDat['agelife']-1);
                }
            }
        }
        $marketingPrice=$marketingPriceExtra+$marketingPriceTaoCan;
        $costprice=$costpriceExtra+$costpriceTaoCan;
        $rp['marketprice']=$marketingPrice;
        //非saas类获取市场价 虽然业绩计算公式和市场价无关但还是查下 start
        $rp['servicecontractid']=$contractid;
        $rp['receivedpaymentsid']=$receivepayid;
        $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
        $dataResult=$Matchreceivements_Record_Model->noSaaSGetMarketing($rp);
        if($dataResult>0){
            $rp['marketprice']=$dataResult;
        }
        // 非saas类获取市场价 虽然业绩计算公式和市场价无关但还是查下  end
        $modulestatus='a_normal';
        //回款时间 即入账日期
        $reality_date=$rp['reality_date'];
        //匹配时间
        if(empty($matchdate)){
            $matchdate=date('Y-m-d');
        }

        //查询业绩是否下单  查询已扣减成本金额
        if($rp['multitype']==1){
            $sqlsalesorder=" SELECT costreduction,performanceoftime  FROM vtiger_salesorder as so,vtiger_salesorderrayment as sd   WHERE  so.servicecontractsid=? AND sd.receivedpaymentsid=? AND sd.salesorderid=so.salesorderid  AND  so.iscancel=0 LIMIT 1  ";
            $salesorderInfo=$adb->pquery($sqlsalesorder,array($contractid,$receivepayid));
        }else{
            $sqlsalesorder=" SELECT  costreduction,performanceoftime  FROM vtiger_salesorder WHERE  servicecontractsid=? AND iscancel=0 LIMIT 1  ";
            $salesorderInfo=$adb->pquery($sqlsalesorder,array($contractid));
        }
        $salesorderInfo=$adb->query_result_rowdata($salesorderInfo,0);
        if(empty($salesorderInfo['performanceoftime'])){
            $achievementmonth=null;
        }else{
            $achievementmonth=$this->getspecialAchievementmonth($matchdate,$reality_date,$matchdate);
        }

        $tsiteperformanceoftimeIsExists=1;
        //成本扣除数
        $costdeduction=$costprice;
        if($rp['servicecontractstype']=='新增'){
            $achievementtype='newadd';
        }else{
            $achievementtype='renew';
        }
        //工单成本合计
        $worksheetcost=($costingdata['costing']+$costingdata['purchasemount']+$costingdata['extracost']);
        //查询该服务合同分成人
        $queryc=" SELECT vtiger_servicecontracts_divide.*,vtiger_departments.departmentname FROM vtiger_servicecontracts_divide  LEFT JOIN vtiger_departments ON  vtiger_departments.departmentid=vtiger_servicecontracts_divide.signdempart WHERE vtiger_servicecontracts_divide.servicecontractid = ?";
        $resultdatas=$adb->pquery($queryc,array($contractid));
        $insertValueStr='';
        $i=1;
        while ($rowDatas=$adb->fetch_array($resultdatas)){

            $scalling=$rowDatas['scalling'];
            $businessunit=$total*($scalling/100);
            $receivedpaymentownid=$rowDatas['receivedpaymentownid'];

            $i++;
            $dividetotal=$rp['total']*$scalling/100;
            // 查询分成人 所在部门  以及属事业部查询
            $queryc=" SELECT d.departmentid,d.departmentname,d.parentdepartment,u.last_name FROM vtiger_user2department ud  LEFT JOIN vtiger_departments as d  ON ud.departmentid=d.departmentid LEFT JOIN  vtiger_users as u ON  u.id=ud.userid  WHERE  ud.userid= ? LIMIT 1 ";
            $resultdataDepartment=$adb->pquery($queryc,array($receivedpaymentownid));
            $Department=$adb->query_result_rowdata($resultdataDepartment,0);
            $departmentname=$Department['departmentname'];
            if($Department['departmentid']==$Department['parentdepartment']){
                $groupname=$departmentname;
            }else{
                $str="::".$Department['departmentid'];
                $Department['parentdepartment']= str_replace($str,"",$Department['parentdepartment']);
                $parentdepartment = explode("::",$Department['parentdepartment']);
                $parentdepartmentId = end($parentdepartment);
                //查询父类
                $queryc=" SELECT departmentname FROM vtiger_departments  WHERE  departmentid = ?  limit 1 ";
                $resultdataDepartment=$adb->pquery($queryc,array($parentdepartmentId));
                $Departments=$adb->query_result_rowdata($resultdataDepartment,0);
                $groupname=$Departments['departmentname'];
            }
            $costing=$costingdata['costing'];
            $purchasemount=$costingdata['purchasemount'];
            $extracost = $costingdata['extracost'];
            // 重新计算沙龙  媒体外采  没接充值  和   其他 （其他包含所有之和）
            $salong=$otherDataTypeArray['沙龙']*($scalling/100);
            $waici=$otherDataTypeArray['外采']*($scalling/100);
            $meijai=$otherDataTypeArray['媒介充值']*($scalling/100);
            //  other 指 回款（沙龙外采媒体充值其他的总和）
            $othercost=$otherdatas['extra_price'];
            $divideworksheetcost=$worksheetcost*($scalling/100);
            $dividecosting=$costingdata['costing']*($scalling/100);
            $dividepurchasemount=$costingdata['purchasemount']*($scalling/100);
            $divideextracost=$costingdata['extracost']*($scalling/100);
            $divideother=$othercost*($scalling/100);
            //已分成业绩市场价
            $dividemarketprice=$rp['marketprice']*$scalling/100;
            //已分成成本扣除数
            $dividecostdeduction=$costprice*$scalling/100;
            $other=$othercost;
            // 已分成回款
            $unit_prices=$rp['unit_price']*($scalling/100);
            $remark.="工单总成本（".$worksheetcost."）"."已扣减成本（".$salesorderInfo['costreduction']."）";
            //到账业绩
            $arriveachievement=($rp['unit_price']-($worksheetcost-$salesorderInfo['costreduction']))*$scalling/100;
            // 到账业绩减去pos手续费
            $arriveachievement=$arriveachievement-$otherdatas['extra_price']*$scalling/100;

            if($arriveachievement< 0 ){
                $arriveachievement=0;
            }

            $producttype=6;
            $receivedpaymentown=$Department['last_name'];
            // 最后判断下到账回款 是否为负值 如果为负 则设为零
            if($arriveachievement < 0 || $tsiteperformanceoftimeIsExists!=1){
                $arriveachievement=0;
            }
            $worksheetcosts=$worksheetcost;
            $effectiverefund=$arriveachievement;
            $more_years_renew=0;
            $renewal_commission=0;
            $renewtimes=0;
            $splitcontractamount=0;
            $splitmarketprice=0;
            $splitcost=0;
            $commissionforrenewal=0;
            $datavalue[]=$rowDatas['owncompanys'];
            $datavalue[]=$receivedpaymentownid;
            $datavalue[]=$scalling;
            $datavalue[]=$rowDatas['servicecontractid'];
            $datavalue[]=$receivepayid;
            $datavalue[]=$businessunit;
            $datavalue[]=$matchdate;
            $datavalue[]=$Department['departmentid']?$Department['departmentid']:0;
            $datavalue[]=$rp['owncompany'];
            $datavalue[]=$rp['createtime'];
            $datavalue[]=$rp['reality_date'];
            $datavalue[]=$rp['paytitle'];
            $datavalue[]=$rp['unit_price'];
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
            $datavalue[]=$worksheetcosts?$worksheetcosts:0;
            $datavalue[]=$agelife['agelife']?$agelife['agelife']:0;
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
            $datavalue[]=$generatedamount;$datavalue[]=$arriveachievement;$datavalue[]=$divideworksheetcost;$datavalue[]=$dividecosting;$datavalue[]=$dividepurchasemount;$datavalue[]=$divideextracost;$datavalue[]=$divideother;$datavalue[]=$more_years_renew;$datavalue[]=$renewal_commission;
            $datavalue[]=$renewtimes;$datavalue[]=$splitcontractamount;$datavalue[]=$splitmarketprice;$datavalue[]=$splitcost;$datavalue[]=$commissionforrenewal;
            $insertValueStr.='(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?),';
        }
        // 如果工单成本 没有审核通过没有生成时间则不更改已扣减成本
        if(!empty($salesorderInfo['performanceoftime'])) {
            //如果已减工单成本 + 目前回款金额 大于等于总成本说明 工单成本已经减完了 则直接把已减成本改成 总成本就ok了
            if($rp['multitype']==1){
                if ($rp['unit_price'] + $salesorderInfo['costreduction'] > $worksheetcost) {
                    //更新已减成本
                    $updatesql = " UPDATE vtiger_salesorder SET costreduction=? WHERE servicecontractsid =? AND salesorderid=(SELECT salesorderid FROM vtiger_salesorderrayment WHERE receivedpaymentsid=? LIMIT 1 ) ";
                    $adb->pquery($updatesql, array($worksheetcost, $contractid,$receivepayid));
                } else {

                    //更新已减成本
                    $updatesql = " UPDATE vtiger_salesorder SET costreduction=costreduction+? WHERE servicecontractsid =? AND salesorderid=(SELECT salesorderid FROM vtiger_salesorderrayment WHERE receivedpaymentsid=? LIMIT 1 ) ";
                    $adb->pquery($updatesql, array($rp['unit_price'], $contractid,$receivepayid));
                }
            }else{
                if ($rp['unit_price'] + $salesorderInfo['costreduction'] > $worksheetcost) {
                    //更新已减成本
                    $updatesql = " UPDATE vtiger_salesorder SET costreduction=? WHERE servicecontractsid =? ";
                    $adb->pquery($updatesql, array($worksheetcost, $contractid));
                } else {
                    //更新已减成本
                    $updatesql = " UPDATE vtiger_salesorder SET costreduction=costreduction+? WHERE servicecontractsid =? ";
                    $adb->pquery($updatesql, array($rp['unit_price'], $contractid));
                }
            }

        }
       return array("datavalue"=>$datavalue,"insertValueStr"=>$insertValueStr,'isTyun'=>0);
    }
    /**
     * 总的公式
     * @param int $receivepayid 回款id
     * @param int $total  回款 unit_price
     * @param int $shareuser 是否是共享商务独占业绩  1 是
     * @param int $currentid  如果是 共享商务有用
     * @param int $contractid  服务合同id
     * @param int $contractid  服务合同id
     * @param int $isCalcAchievement 0默认规则，1来源与充值申请单
     * @throws Exception $params array 参数（分成人信息）
     */
    public function commonInsertAchievementallotStatistic($receivepayid=0,$total=0,$shareuser=0,$currentid=0,$contractid=0,$params=0,$isCalcAchievement=0)
    {
        $adb = PearDatabase::getInstance();
        //① 查询回款相关数据  vtiger_receivedpayments
        $queryc = "SELECT s.multitype,s.contract_no,s.oldcontract_usedtime,s.oldcontractid,s.extraproductid,s.productid,s.invoicecompany,s.parent_contracttypeid,s.contract_type,s.servicecontractsid,s.total,r.owncompany,r.matchdate,LEFT (r.createtime,10) AS createtime,r.reality_date,r.matchdate,r.paytitle,r.unit_price,d.departmentname as department,s.servicecontractstype,left(s.signdate,10) AS signdate,s.contract_no,a.accountname FROM vtiger_receivedpayments  as r LEFT JOIN vtiger_departments as d ON r.departmentid=d.departmentid LEFT JOIN vtiger_servicecontracts as s ON s.servicecontractsid=r.relatetoid LEFT JOIN vtiger_account as a ON a.accountid=s.sc_related_to  WHERE receivedpaymentsid = ?  ORDER BY receivedpaymentsid DESC LIMIT 1 ";
        $resultdatapayments = $adb->pquery($queryc, array($receivepayid));
        $rp = $adb->query_result_rowdata($resultdatapayments, 0);
        // 如果是（4）单纯的域名空间维护费续费不计入续费业绩（① 网站建设系列->TSITE续费合同,② IDC类->(“域名、珍岛云、邮箱合同”,"服务器运行维护合同")）
        if($isCalcAchievement==0 &&
            (
                $rp['contract_type']=='媒介.Yandex' ||
                $rp['contract_type']=='媒介.GOOGLE'
            )
        ){//指定类型合同，匹配回款时不生成业绩，特定情况下生成业绩
            return ;
        }
        if ((($rp['parent_contracttypeid'] == 9) || (($rp['contract_type'] == '域名、珍岛云、邮箱合同' || $rp['contract_type'] == '服务器运行维护合同' || $rp['contract_type'] == '珍岛云计算合同' || $rp['contract_type'] == 'IDC增值服务') && $rp['parent_contracttypeid'] == 1)) && (strpos($rp['contract_no'], "XF") != false || $rp['servicecontractstype'] == '续费')) {
            $paramers['contract_no'] = $rp['contract_no'];
            $paramers['marks'] = '（4）单纯的域名空间维护费续费不计入续费业绩（① 网站建设系列->TSITE续费合同,② IDC类->(“域名、珍岛云、邮箱合同”,"服务器运行维护合同")）&& (strpos($rp[\'contract_no\'],"XF")!=false || $rp[\'servicecontractstype\']==\'续费\')';
            $Matchreceivements_Record_Model = Vtiger_Record_Model::getCleanInstance("Matchreceivements");
            $Matchreceivements_Record_Model->noCalculationAchievementRecord($paramers);
            return;
        }
        //院校版不生成业绩  start  ac.status IN(0,1)
        $collegeedition = $adb->pquery("SELECT  iscollegeedition  FROM vtiger_activationcode WHERE  contractid=? AND status IN(0,1) AND iscollegeedition=1 ", array($contractid));
        //如果是院校版订单则不生成业绩
        if ($adb->num_rows($collegeedition) > 0) {
            $paramers['contract_no'] = $rp['contract_no'];
            $paramers['marks'] = '院校版则不生成业绩。';
            $Matchreceivements_Record_Model = Vtiger_Record_Model::getCleanInstance("Matchreceivements");
            $Matchreceivements_Record_Model->noCalculationAchievementRecord($paramers);
            return;
        }
        //院校版不生成业绩 end
        $tsite = array(609236, 609234, 609237, 609704, 609230, 609228, 528504, 609733, 609735,25,24,136,137);
        //非saas类数组产品  头四个公式六已确认
        $noSaaS = array(361005, 377277, 361594, 362103, 361001, 362103, 362104, 391124, 2143595, 2226512, 2115476, 462151);//现在只有三个还剩余两个（两个已确认不算了）
        if (!empty($rp['extraproductid'])) {
            $extraprod = explode(",", $rp['extraproductid']);
            $otherTypeTrue = array_intersect($noSaaS, $extraprod);
            if (!empty($otherTypeTrue)) {
                $otherTypeTrue = true;
            }

        } else {
            $otherTypeTrue = false;
        }
        $remark = '';
        // 默认为零  只的是是否要走 数据插入处理
        $type = 0;
        if($rp['parent_contracttypeid']==3 && ($rp['contract_type']=='媒介.Yandex' || $rp['contract_type']=='媒介.GOOGLE')){
            //如果是充值单的特殊处理
            //$this->rechargeCalculation($rp,$contractid,$receivepayid,$shareuser,$total,$currentid,$remark,$adb,$params,0);
            return ;
        //公式四 到账业绩(百度V认证)
        }else if(strpos($rp['contract_no'],"VRZ")){
            $data=$this->VRZCalculationAchievement($rp,$contractid,$receivepayid,$shareuser,$total,$currentid,$remark,$adb,0);
        //公式三 到账业绩（非SAAS类）
        }else if(in_array($rp['productid'],$noSaaS) || $otherTypeTrue){
            return ;
            $data=$this->noSaaSCalculationAchievement($rp,$contractid,$receivepayid,$shareuser,$total,$currentid,$remark,$adb,0);
        //公式五 到账业绩 TSITE产品
        }else if(in_array($rp['productid'],$tsite)){
            return ;
            // 做个过滤处理 如果是 搜索引擎类型google竞价合同  则不再生成业绩
            if($rp['servicecontractstype']=='续费'){
                $paramers['contract_no']=$rp['contract_no'];
                $paramers['marks']='Tsite续费合同类型的不生成业绩正常匹配判定 以防以前正常生成时生成了数据 在这里又更新';
                $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
                $Matchreceivements_Record_Model->noCalculationAchievementRecord($paramers);
                return;
            }
            $data=$this->tsiteCalculationAchievement($rp,$contractid,$receivepayid,$shareuser,$total,$currentid,$remark,$adb,0);
         //如果合同属于T云系列 和 产品是搜索引擎类型google竞价合同 的  走 T云公式。
        }else if($rp['parent_contracttypeid']==2){
            // T云非标产品
            if($rp['contract_type']=='T云系列补充协议（非标）'){
                return;
                $data=$this->tYunNonstandardCalculationAchievement($rp,$contractid,$receivepayid,$shareuser,$total,$currentid,$remark,$adb,0);
            }else{
                $result=$adb->run_query_allrecords(" SELECT  *  FROM  vtiger_activationcode  WHERE  contractid=".$contractid."  AND  comeformtyun=1 LIMIT 1 ");
                //查询该合同是否存在T云订单
                if(empty($result)){
                    $paramers['contract_no']=$rp['contract_no'];
                    $paramers['marks']='T云系列合同但是T云WEB订单管理没生成订单,contractid'.$contractid;
                    $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
                    $Matchreceivements_Record_Model->noCalculationAchievementRecord($paramers);
                    return ;
                }
                $data=$this->tYunCalculationAchievement($receivepayid,$contractid,$shareuser,$total,$currentid,$adb,0);
            }
            //没有给的产品计算  计算公式都走
        }else{
            return;
           $data=$this->othersExtraCalculationAchievement($rp,$contractid,$receivepayid,$shareuser,$total,$currentid,$remark,$adb,0);
        }
        $insertValueStr=$data['insertValueStr'];
        $datavalue=$data['datavalue'];
        $defaultSQlField='owncompanys,receivedpaymentownid,scalling,servicecontractid,receivedpaymentsid,businessunit,matchdate,departmentid,owncompany,createtime,reality_date,paytitle,unit_price,unit_prices,department,groupname,departmentname,receivedpaymentown,servicecontractstype,accountname,signdate,contract_no,total,dividetotal,costing,purchasemount,worksheetcost,productlife,marketprice,dividemarketprice,costdeduction,dividecostdeduction,other,effectiverefund,arriveachievement,achievementmonth,modulestatus,productname,achievementtype,producttype,extracost,salong,waici,meijai,othercost,shareuser,remarks,generatedamount,adjustbeforearriveachievement,divideworksheetcost,dividecosting,dividepurchasemount,divideextracost,divideother,more_years_renew,renewal_commission,renewtimes,splitcontractamount,splitmarketprice,splitcost,commissionforrenewal';
        $field=!empty($data['field'])?$data['field']:$defaultSQlField;
        // 最后如果不为空则进行处理数据
        if(!empty($insertValueStr) && isset($type) && $type!=1){
            $sqlquery=" SELECT  achievementallotid FROM vtiger_achievementallot_statistic WHERE receivedpaymentsid=? AND achievementmonth > 0 ";
            $result = $adb->pquery($sqlquery,array($receivepayid));
            $deleteArray=[];
            while ($rowdatas=$adb->fetch_array($result)){
                $deleteArray[]=$rowdatas['achievementallotid'];
            }
            //如果本来有有效数据 则删除汇总表数据
            if(!empty($deleteArray)){
                AchievementSummary_Record_Model::delAchievementSummary($deleteArray);
            }
            $insertValueStr = trim($insertValueStr,",");
            //$addStastic_sql = "INSERT INTO vtiger_achievementallot_statistic (owncompanys,receivedpaymentownid,scalling,servicecontractid,receivedpaymentsid,businessunit,matchdate,departmentid,owncompany,createtime,reality_date,paytitle,unit_price,unit_prices,department,groupname,departmentname,receivedpaymentown,servicecontractstype,accountname,signdate,contract_no,total,dividetotal,costing,purchasemount,worksheetcost,productlife,marketprice,dividemarketprice,costdeduction,dividecostdeduction,other,effectiverefund,arriveachievement,achievementmonth,modulestatus,productname,achievementtype,producttype,extracost,salong,waici,meijai,othercost,shareuser,remarks,generatedamount,adjustbeforearriveachievement,divideworksheetcost,dividecosting,dividepurchasemount,divideextracost,divideother,more_years_renew,renewal_commission,renewtimes,splitcontractamount,splitmarketprice,splitcost,commissionforrenewal) VALUES ";
            $addStastic_sql = "INSERT INTO vtiger_achievementallot_statistic (".$field.") VALUES ";
            $deleteStastic_sql='DELETE  FROM  vtiger_achievementallot_statistic WHERE receivedpaymentsid = ?';
            $adb->pquery($deleteStastic_sql,array($receivepayid));
            //插入做销售业绩明细表数据
            $adb->pquery($addStastic_sql.$insertValueStr,array($datavalue));
            //插入汇总表数据
            $sqlquery=" SELECT  achievementallotid FROM vtiger_achievementallot_statistic WHERE receivedpaymentsid=? AND achievementmonth > 0 ";
            $result = $adb->pquery($sqlquery,array($receivepayid));
            $newArray=[];
            while ($rowdatas=$adb->fetch_array($result)){
                $newArray[]=$rowdatas['achievementallotid'];
            }
            //如果有有效数据 插入处理汇总表数据
            if(!empty($newArray)){
                AchievementSummary_Record_Model::newAchievementSummary($newArray);
            }
        }
    }

    /**
     * @param int $receivepayid
     * @param int $total
     * @param int $shareuser
     * @param int $currentid
     * @param int $contractid
     * @param int $params
     * @param int $matchdate
     * @throws Exception
     *  用于脚本重新生成业绩时 使用  充值单类型计算还没有做重新生成
     */
    public function commonInsertAchievementallotStatisticjioaben($receivepayid=0,$total=0,$shareuser=0,$currentid=0,$contractid=0,$params=0,$matchdate=0){
        $adb = PearDatabase::getInstance();
        //① 查询回款相关数据  vtiger_receivedpayments
        $queryc="SELECT s.multitype,s.contract_no,s.oldcontract_usedtime,s.oldcontractid,s.extraproductid,s.productid,s.invoicecompany,s.parent_contracttypeid,s.contract_type,s.servicecontractsid,s.total,r.owncompany,LEFT (r.createtime,10) AS createtime,r.reality_date,r.matchdate,r.paytitle,r.unit_price,d.departmentname as department,s.servicecontractstype,left(s.signdate,10) AS signdate,s.contract_no,a.accountname FROM vtiger_receivedpayments  as r LEFT JOIN vtiger_departments as d ON r.departmentid=d.departmentid LEFT JOIN vtiger_servicecontracts as s ON s.servicecontractsid=r.relatetoid LEFT JOIN vtiger_account as a ON a.accountid=s.sc_related_to  WHERE receivedpaymentsid = ?  ORDER BY receivedpaymentsid DESC LIMIT 1 ";
        $resultdatapayments=$adb->pquery($queryc,array($receivepayid));
        $rp=$adb->query_result_rowdata($resultdatapayments,0);
        // 如果是（4）单纯的域名空间维护费续费不计入续费业绩（① 网站建设系列->TSITE续费合同,② IDC类->(“域名、珍岛云、邮箱合同”,"服务器运行维护合同")）
        if((($rp['parent_contracttypeid']==9) ||(($rp['contract_type']=='域名、珍岛云、邮箱合同'||$rp['contract_type']=='服务器运行维护合同') && $rp['parent_contracttypeid']==1)) && (strpos($rp['contract_no'],"XF")!=false || $rp['servicecontractstype']=='续费')){
            $paramers['contract_no']=$rp['contract_no'];
            $paramers['marks']='（4）单纯的域名空间维护费续费不计入续费业绩（① 网站建设系列->TSITE续费合同,② IDC类->(“域名、珍岛云、邮箱合同”,"服务器运行维护合同")）&& (strpos($rp[\'contract_no\'],"XF")!=false || $rp[\'servicecontractstype\']==\'续费\')';
            $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
            $Matchreceivements_Record_Model->noCalculationAchievementRecord($paramers);
            return ;
        }
        //院校版不生成业绩  start  ac.status IN(0,1)
        $collegeedition=$adb->pquery("SELECT  iscollegeedition  FROM vtiger_activationcode WHERE  contractid=? AND status IN(0,1) AND iscollegeedition=1 ",array($contractid));
        //如果是院校版订单则不生成业绩
        if($adb->num_rows($collegeedition)>0){
            $paramers['contract_no']=$rp['contract_no'];
            $paramers['marks']='院校版则不生成业绩。';
            $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
            $Matchreceivements_Record_Model->noCalculationAchievementRecord($paramers);
            return;
        }
        //院校版不生成业绩 end
        $tsite=array(609236,609234,609237,609704,609230,609228,528504,609733,609735);
        //非saas类数组产品  头四个公式六已确认
        $noSaaS=array(361005,377277,361594,362103,361001,362103,362104,391124,2143595,2226512,2115476,462151);//现在只有三个还剩余两个（两个已确认不算了）
        if(!empty($rp['extraproductid'])){
            $extraprod=explode(",",$rp['extraproductid']);
            $otherTypeTrue=array_intersect($noSaaS,$extraprod);
            if(!empty($otherTypeTrue)){
                $otherTypeTrue=true;
            }

        }else{
            $otherTypeTrue =false;
        }
        $remark='';
        // 默认为零  只的是是否要走 数据插入处理
        $type=0;
        //Tyun
        //$TyunID=array(19,20,21,22,23,25,24,26,27,79,12,9,10,11,38,7,66);//列
        /*if((in_array($rp['productid'],$TyunID) && $rp['parent_contracttypeid']==2) || (in_array($rp['servicecontractstype'],array('upgrade')) && $rp['parent_contracttypeid']==2)){*/
        if($rp['parent_contracttypeid']==4 && ($rp['contract_type']=='Yandex竞价' || $rp['contract_type']=='GOOGLE竞价合同')){
            //如果是充值单的特殊处理
            //$this->rechargeCalculation($rp,$contractid,$receivepayid,$shareuser,$total,$currentid,$remark,$adb,$params,$matchdate);
            return ;
            //公式四 到账业绩(百度V认证)
        }else if(strpos($rp['contract_no'],"VRZ")){
            $data=$this->VRZCalculationAchievement($rp,$contractid,$receivepayid,$shareuser,$total,$currentid,$remark,$adb,$matchdate);
        //公式三 到账业绩（非SAAS类）
        }else if(in_array($rp['productid'],$noSaaS) || $otherTypeTrue){
            $data=$this->noSaaSCalculationAchievement($rp,$contractid,$receivepayid,$shareuser,$total,$currentid,$remark,$adb,$matchdate);
            //公式五 到账业绩 TSITE产品
        }else if(in_array($rp['productid'],$tsite)){
            // 做个过滤处理 如果是 搜索引擎类型google竞价合同  则不再生成业绩
            if($rp['servicecontractstype']=='续费'){
                $paramers['contract_no']=$rp['contract_no'];
                $paramers['marks']='Tsite续费合同类型的不生成业绩正常匹配判定 以防以前正常生成时生成了数据 在这里又更新';
                $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
                $Matchreceivements_Record_Model->noCalculationAchievementRecord($paramers);
            }
            $data=$this->tsiteCalculationAchievement($rp,$contractid,$receivepayid,$shareuser,$total,$currentid,$remark,$adb,$matchdate);
            //如果合同属于T云系列
        }else if($rp['parent_contracttypeid']==2){
            // T云非标产品
            if($rp['contract_type']=='T云系列补充协议（非标）'){
                $data=$this->tYunNonstandardCalculationAchievement($rp,$contractid,$receivepayid,$shareuser,$total,$currentid,$remark,$adb,$matchdate);
            }else{
                $result=$adb->run_query_allrecords(" SELECT  *  FROM  vtiger_activationcode  WHERE  contractid=".$contractid."  AND  comeformtyun=1 LIMIT 1 ");
                //查询该合同是否存在T云订单
                if(empty($result)){
                    $paramers['contract_no']=$rp['contract_no'];
                    $paramers['marks']='T云系列合同但是T云WEB订单管理没生成订单,contractid'.$contractid;
                    $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
                    $Matchreceivements_Record_Model->noCalculationAchievementRecord($paramers);
                    return ;
                }
                $data=$this->tYunCalculationAchievement($receivepayid,$contractid,$shareuser,$total,$currentid,$adb,$matchdate);
            }
            //没有给的产品计算  计算公式都走
        }else{
            $data=$this->othersExtraCalculationAchievement($rp,$contractid,$receivepayid,$shareuser,$total,$currentid,$remark,$adb,$matchdate);
        }
        $insertValueStr=$data['insertValueStr'];
        $datavalue=$data['datavalue'];
        $defaultSQlField='owncompanys,receivedpaymentownid,scalling,servicecontractid,receivedpaymentsid,businessunit,matchdate,departmentid,owncompany,createtime,reality_date,paytitle,unit_price,unit_prices,department,groupname,departmentname,receivedpaymentown,servicecontractstype,accountname,signdate,contract_no,total,dividetotal,costing,purchasemount,worksheetcost,productlife,marketprice,dividemarketprice,costdeduction,dividecostdeduction,other,effectiverefund,arriveachievement,achievementmonth,modulestatus,productname,achievementtype,producttype,extracost,salong,waici,meijai,othercost,shareuser,remarks,generatedamount,adjustbeforearriveachievement,divideworksheetcost,dividecosting,dividepurchasemount,divideextracost,divideother,more_years_renew,renewal_commission,renewtimes,splitcontractamount,splitmarketprice,splitcost,commissionforrenewal';
        $field=!empty($data['field'])?$data['field']:$defaultSQlField;
        // 最后如果不为空则进行处理数据
        if(!empty($insertValueStr) && isset($type) && $type!=1){
            $achievementSummaryRecordModel = Vtiger_Record_Model::getCleanInstance("AchievementSummary");
            $sqlquery=" SELECT  achievementallotid FROM vtiger_achievementallot_statistic WHERE receivedpaymentsid=? AND achievementmonth > 0 ";
            $result = $adb->pquery($sqlquery,array($receivepayid));
            $deleteArray=[];
            while ($rowdatas=$adb->fetch_array($result)){
                $deleteArray[]=$rowdatas['achievementallotid'];
            }
            //如果本来有有效数据 则删除汇总表数据
            if(!empty($deleteArray)){
                $achievementSummaryRecordModel->delAchievementSummary($deleteArray);
            }
            $insertValueStr = trim($insertValueStr,",");
            //$addStastic_sql = "INSERT INTO vtiger_achievementallot_statistic (owncompanys,receivedpaymentownid,scalling,servicecontractid,receivedpaymentsid,businessunit,matchdate,departmentid,owncompany,createtime,reality_date,paytitle,unit_price,unit_prices,department,groupname,departmentname,receivedpaymentown,servicecontractstype,accountname,signdate,contract_no,total,dividetotal,costing,purchasemount,worksheetcost,productlife,marketprice,dividemarketprice,costdeduction,dividecostdeduction,other,effectiverefund,arriveachievement,achievementmonth,modulestatus,productname,achievementtype,producttype,extracost,salong,waici,meijai,othercost,shareuser,remarks,generatedamount,adjustbeforearriveachievement,divideworksheetcost,dividecosting,dividepurchasemount,divideextracost,divideother,more_years_renew,renewal_commission,renewtimes,splitcontractamount,splitmarketprice,splitcost,commissionforrenewal) VALUES ";
            $addStastic_sql = "INSERT INTO vtiger_achievementallot_statistic (".$field.") VALUES ";
            $deleteStastic_sql='DELETE  FROM  vtiger_achievementallot_statistic WHERE receivedpaymentsid = ?';
            $adb->pquery($deleteStastic_sql,array($receivepayid));
            //插入做销售业绩明细表数据
            $adb->pquery($addStastic_sql.$insertValueStr,array($datavalue));
            //插入汇总表数据
            $sqlquery=" SELECT  achievementallotid FROM vtiger_achievementallot_statistic WHERE receivedpaymentsid=? AND achievementmonth > 0 ";
            $result = $adb->pquery($sqlquery,array($receivepayid));
            $newArray=[];
            while ($rowdatas=$adb->fetch_array($result)){
                $newArray[]=$rowdatas['achievementallotid'];
            }
            //如果有有效数据 插入处理汇总表数据
            if(!empty($newArray)){
                $achievementSummaryRecordModel->newAchievementSummary($newArray);
            }
        }
    }
    /**
     *
     * 谷歌充值
     *
     */
    public  function rechargeCalculation(&$rp,&$contractid,&$receivepayid,&$shareuser,&$total,$currentid,&$remark,&$adb,&$params,$matchdate=0,$salesorderid=0){
        $generatedamount=0;
        $remark.="充值单类计算";
        $queryc=" SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid= ? AND multistatus in(0,1) LIMIT 1";
        $resultdatapayments=$adb->pquery($queryc,array($rp['servicecontractsid']));
        $agelife=$adb->query_result_rowdata($resultdatapayments,0);
        // 有关 沙龙 外采 媒介充值 其他
        $queryc="SELECT extra_type,extra_price FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid=?";
        $otherdata=$adb->pquery($queryc,array($receivepayid));
        $otherdata=$adb->query_result_rowdata($otherdata,0);
        $otherDataTypeArray=array();
        foreach ($otherdata as $key=>$val){
            $otherDataTypeArray[$val['extra_type']]=$otherDataTypeArray[$val['extra_type']]+$val['extra_price'];
        }
        //该服务合同回款相关的总的之和
        $queryc="SELECT SUM(extra_price) as extra_price FROM vtiger_receivedpayments_extra WHERE vtiger_receivedpayments_extra.receivementid =? ";
        $otherdatas=$adb->pquery($queryc,array($receivepayid));
        $otherdatas=$adb->query_result_rowdata($otherdatas,0);
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
        $marketingPriceTaoCan=0;
        $costpriceTaoCan=0;
        if(!empty($rp['productid'])){
            if($rp['multitype']==1){
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid LEFT JOIN vtiger_salesorderrayment as sd ON sd.salesorderid=vtiger_salesorderproductsrel.salesorderid  WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND  sd.receivedpaymentsid=?  AND productcomboid IN(".$rp['productid'].") AND multistatus=3  LIMIT  1  ";
                $product=$adb->pquery($sql,array($contractid,$receivepayid));
            }else{
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid  WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND productcomboid IN(".$rp['productid'].") AND multistatus=3 LIMIT  1 ";
                $product=$adb->pquery($sql,array($contractid));
            }
            while ($rowsDat=$adb->fetch_array($product)) {
                if ((strpos($rp['contract_no'], "XF") != false || $rp['servicecontractstype'] != '新增')) {
                    $marketingPriceTaoCan += $rowsDat['renewalfee'] * $rowsDat['agelife'];
                    $costpriceTaoCan += $rowsDat['renewalcost'] * $rowsDat['agelife'];
                } else {
                    $marketingPriceTaoCan += $rowsDat['unit_price'] + $rowsDat['renewalfee'] * ($rowsDat['agelife'] - 1);
                    $costpriceTaoCan += $rowsDat['realprice'] + $rowsDat['renewalcost'] * ($rowsDat['agelife'] - 1);
                }
            }
        }
        //② 额外产品市场价  和 成本
        $marketingPriceExtra=0;
        $costpriceExtra=0;
        if(!empty($rp['extraproductid'])){
            if($rp['multitype']==1){
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid LEFT JOIN vtiger_salesorderrayment as sd ON sd.salesorderid=vtiger_salesorderproductsrel.salesorderid WHERE vtiger_salesorderproductsrel.servicecontractsid =?  AND sd.receivedpaymentsid=?  AND productcomboid IN(".$rp['extraproductid'].") AND multistatus=3  ";
                $product=$adb->pquery($sql,array($contractid,$receivepayid));
            }else{
                $sql="SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) as agelife,vtiger_products.realprice,vtiger_products.unit_price,vtiger_products.renewalfee,vtiger_products.renewalcost FROM vtiger_salesorderproductsrel  LEFT JOIN  vtiger_products  ON  vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid  WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND productcomboid IN(".$rp['extraproductid'].") AND multistatus=3  ";
                $product=$adb->pquery($sql,array($contractid));
            }
            while ($rowsDat=$adb->fetch_array($product)){
                if((strpos($rp['contract_no'],"XF")!=false || $rp['servicecontractstype']!='新增')){
                    $marketingPriceExtra+=$rowsDat['renewalfee']*$rowsDat['agelife'];
                    $costpriceExtra+=$rowsDat['renewalcost']*$rowsDat['agelife'];
                }else{
                    $marketingPriceExtra+=$rowsDat['unit_price']+$rowsDat['renewalfee']*($rowsDat['agelife']-1);
                    $costpriceExtra+=$rowsDat['realprice']+$rowsDat['renewalcost']*($rowsDat['agelife']-1);
                }
            }
        }
        $marketingPrice=$marketingPriceExtra+$marketingPriceTaoCan;
        $costprice=$costpriceExtra+$costpriceTaoCan;
        $rp['marketprice']=$marketingPrice;
        $modulestatus='a_normal';
        //回款时间 即入账日期
        $reality_date=$rp['reality_date'];
        //匹配时间
        if(empty($matchdate)){
            $matchdate=date('Y-m-d H:i:s');
        }
        $achievementmonth=$this->getAchievementmonth($reality_date,$matchdate);
        $matchdate=date('Y-m-d',strtotime($matchdate));
        //成本扣除数
        $costdeduction=$costprice;
        $achievementtype='newadd';
        $sql=" SELECT ra.refillapplicationid,GROUP_CONCAT(ra.receivedpaymentsid) AS receivedpaymentsidstr,r.exchangerate,SUM(ra.refillapptotal) AS refillapptotal FROM vtiger_refillapprayment AS ra,vtiger_receivedpayments AS r WHERE ra.refillapplicationid =? AND ra.receivedpaymentsid=r.receivedpaymentsid AND ra.deleted = 0 AND ra.refillapptotal > 0 ";
        $resultData=$adb->pquery($sql,array($params['refillapplicationid']));
        $resultData=$adb->query_result_rowdata($resultData,0);
        $rp['unit_price']=$resultData['refillapptotal'];
        //充值申请单毛利 * 回款利率的和
        $sql=" SELECT SUM(totalgrossprofit * ?) as totalgrossprofit FROM vtiger_rechargesheet  WHERE refillapplicationid= ? ";
        $totalgrossprofit=$adb->pquery($sql,array($resultData['exchangerate'],$params['refillapplicationid']));
        $totalgrossprofit=$adb->query_result_rowdata($totalgrossprofit,0);
        $totalgrossprofit=$totalgrossprofit['totalgrossprofit'];
        $refillapplicationId=$params['refillapplicationid'];
        $receivedpaymentsidstr=$resultData['receivedpaymentsidstr'];
        //if(!empty($resultData['refillapplicationid'])){
        //查询该服务合同分成人
        $queryc=" SELECT vtiger_servicecontracts_divide.*,vtiger_departments.departmentname FROM vtiger_servicecontracts_divide  LEFT JOIN vtiger_departments ON  vtiger_departments.departmentid=vtiger_servicecontracts_divide.signdempart WHERE vtiger_servicecontracts_divide.servicecontractid = ?";
        $resultdatas=$adb->pquery($queryc,array($contractid));
        $insertValueStr='';
        $dataArray=array();
        while ($rowDatas=$adb->fetch_array($resultdatas)){
            $scalling=$rowDatas['scalling']/100;
            $businessunit=$total*$scalling;
            $receivedpaymentownid=$rowDatas['receivedpaymentownid'];
            $dividetotal=$rp['total']*$scalling;
            // 查询分成人 所在部门  以及属事业部查询
            $queryc=" SELECT d.departmentid,d.departmentname,d.parentdepartment,u.last_name FROM vtiger_user2department ud  LEFT JOIN vtiger_departments as d  ON ud.departmentid=d.departmentid LEFT JOIN  vtiger_users as u ON  u.id=ud.userid  WHERE  ud.userid= ? LIMIT 1 ";
            $resultdataDepartment=$adb->pquery($queryc,array($receivedpaymentownid));
            $Department=$adb->query_result_rowdata($resultdataDepartment,0);
            $departmentname=$Department['departmentname'];
            if($Department['departmentid']==$Department['parentdepartment']){
                $groupname=$departmentname;
            }else{
                $str="::".$Department['departmentid'];
                $Department['parentdepartment']= str_replace($str,"",$Department['parentdepartment']);
                $parentdepartment = explode("::",$Department['parentdepartment']);
                $parentdepartmentId = end($parentdepartment);
                //查询父类
                $queryc=" SELECT departmentname FROM vtiger_departments  WHERE  departmentid = ?  limit 1 ";
                $resultdataDepartment=$adb->pquery($queryc,array($parentdepartmentId));
                $Departments=$adb->query_result_rowdata($resultdataDepartment,0);
                $groupname=$Departments['departmentname'];
            }
            // 重新计算沙龙  媒体外采  没接充值  和   其他 （其他包含所有之和）
            $salong=$otherDataTypeArray['沙龙']*$scalling;
            $waici=$otherDataTypeArray['外采']*$scalling;
            $meijai=$otherDataTypeArray['媒介充值']*$scalling;
            //  other 指 回款（沙龙外采媒体充值其他的总和）
            $othercost=$otherdatas['extra_price'];
            $divideother=$othercost*$scalling;
            //已分成业绩市场价
            $dividemarketprice=$rp['marketprice']*$scalling;
            //已分成成本扣除数
            $dividecostdeduction=$costprice*$scalling;
            $other=$othercost;
            // 已分成回款
            $unit_prices=$rp['unit_price']*$scalling;
            if(!empty($resultData['refillapplicationid'])){
                //到账业绩  减去pos手续费
                $arriveachievement=($totalgrossprofit-$otherdatas['extra_price'])*$scalling;
            }else{
                $arriveachievement=0;
                $achievementmonth='';
            }
            $producttype=4;
            $receivedpaymentown=$Department['last_name'];
            // 最后判断下到账回款 是否为负值 如果为负 则设为零
            if($arriveachievement < 0){
                $arriveachievement=0;
            }
            //$effectiverefund=$rp['unit_price']*$scalling;
            $effectiverefund=$arriveachievement;//非标有效回款=到账业绩
            $costing=0;
            $purchasemount=0;
            $dividepurchasemount=0;
            $worksheetcost=0;
            $divideworksheetcost=0;
            $extracost=0;
            $divideextracost=0;
            $dividecosting=0;
            $more_years_renew=0;
            $renewal_commission=0;
            $renewtimes=0;
            $splitcontractamount=0;
            $splitmarketprice=0;
            $splitcost=0;
            $commissionforrenewal=0;
            $datavalue['owncompanys']=$rowDatas['owncompanys'];
            $datavalue['receivedpaymentownid']=$receivedpaymentownid;
            $datavalue['scalling']=$scalling*100;
            $datavalue['servicecontractid']=$rowDatas['servicecontractid'];
            $datavalue['receivedpaymentsid']=$receivepayid;
            $datavalue['businessunit']=$businessunit;
            $datavalue['matchdate']=$matchdate;
            $datavalue['departmentid']=$Department['departmentid']?$Department['departmentid']:0;
            $datavalue['owncompany']=$rp['owncompany'];
            $datavalue['createtime']=$rp['createtime'];
            $datavalue['reality_date']=$rp['reality_date'];
            $datavalue['paytitle']=$rp['paytitle'];
            $datavalue['unit_price']=$rp['unit_price'];
            $datavalue['unit_prices']=$unit_prices;
            $datavalue['department']=$rowDatas['departmentname']?$rowDatas['departmentname']:'';
            $datavalue['groupname']=$groupname?$groupname:' ';
            $datavalue['departmentname']=$departmentname?$departmentname:' ';
            $datavalue['receivedpaymentown']=$receivedpaymentown;
            $datavalue['servicecontractstype']=$rp['servicecontractstype']?$rp['servicecontractstype']:' ';
            $datavalue['accountname']=$rp['accountname']?$rp['accountname']:' ';
            $datavalue['signdate']=$rp['signdate'];
            $datavalue['contract_no']=$rp['contract_no'];
            $datavalue['total']=$rp['total']?$rp['total']:0;
            $datavalue['dividetotal']=$dividetotal;
            $datavalue['costing']=$costing;
            $datavalue['purchasemount']=$purchasemount?$purchasemount:0;
            $datavalue['worksheetcost']=$worksheetcost?$worksheetcost:0;
            $datavalue['productlife']=$agelife['agelife']?$agelife['agelife']:0;
            $datavalue['marketprice']=$rp['marketprice']?$rp['marketprice']:0;
            $datavalue['dividemarketprice']=$dividemarketprice;
            $datavalue['costdeduction']=$costdeduction;
            $datavalue['dividecostdeduction']=$dividecostdeduction;
            $datavalue['other']=$other;
            $datavalue['effectiverefund']=$effectiverefund;
            $datavalue['arriveachievement']=$arriveachievement;
            $datavalue['achievementmonth']=$achievementmonth;
            $datavalue['modulestatus']=$modulestatus;
            $datavalue['productname']=$productname;
            $datavalue['achievementtype']=$achievementtype?$achievementtype:0;
            $datavalue['producttype']=$producttype?$producttype:0;
            $datavalue['extracost']=$extracost;
            $datavalue['salong']=$salong;
            $datavalue['waici']=$waici;
            $datavalue['meijai']=$meijai;
            $datavalue['othercost']=$othercost;
            $datavalue['shareuser']=$shareuser;
            $datavalue['remarks']=$remark;
            $datavalue['generatedamount']=$generatedamount;
            $datavalue['adjustbeforearriveachievement']=$arriveachievement;
            $datavalue['divideworksheetcost']=$divideworksheetcost;
            $datavalue['dividecosting']=$dividecosting;
            $datavalue['dividepurchasemount']=$dividepurchasemount;
            $datavalue['divideextracost']=$divideextracost;
            $datavalue['divideother']=$divideother;
            $datavalue['more_years_renew']=$more_years_renew;
            $datavalue['renewal_commission']=$renewal_commission;
            $datavalue['renewtimes']=$renewtimes;
            $datavalue['splitcontractamount']=$splitcontractamount;
            $datavalue['splitmarketprice']=$splitmarketprice;
            $datavalue['splitcost']=$splitcost;
            $datavalue['commissionforrenewal']=$commissionforrenewal;
            $datavalue['refillapplicationid']=$refillapplicationId;
            $datavalue['receivedpaymentsidstr']=$receivedpaymentsidstr;
            //$insertValueStr.='(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?),';
            $dataArray[]=$datavalue;
            //$dataValuearray=array_map(function($v){return '?';},$datavalue);
            //$insertValueStr.='('.implode(',',$dataValuearray).'),';
        }
        // 最后如果不为空则进行处理数据
        //if(!empty($insertValueStr)){
        if(!empty($dataArray)){
            $fieldArr=array_keys($dataArray[0]);
            $fieldstr=implode(',',$fieldArr);
            $placeholderArr=array_map(function($b){return '?';},$fieldArr);
            $placeholderstr='';
            $tempArrayCount=count($dataArray);
            for($i=0;$i<$tempArrayCount;$i++){
                $placeholderstr.='('.implode(',',$placeholderArr).'),';
            }
            $placeholderstr=trim($placeholderstr,',');
            $fieldValue=array();
            foreach($dataArray as $value){
                $fieldValue=array_merge($fieldValue,array_values($value));
            }
            $sqlquery=" SELECT  achievementallotid FROM vtiger_achievementallot_statistic WHERE refillapplicationid=? AND achievementmonth > 0 ";
            $result = $adb->pquery($sqlquery,array($params['refillapplicationid']));
            $deleteArray=[];
            while ($rowdatas=$adb->fetch_array($result)){
                $deleteArray[]=$rowdatas['achievementallotid'];
            }
            //如果本来有有效数据 则删除汇总表数据
            if(!empty($deleteArray)){
                AchievementSummary_Record_Model::delAchievementSummary($deleteArray);
            }
            $insertValueStr = trim($insertValueStr,",");
            //$addStastic_sql = "INSERT INTO vtiger_achievementallot_statistic (owncompanys,receivedpaymentownid,scalling,servicecontractid,receivedpaymentsid,businessunit,matchdate,departmentid,owncompany,createtime,reality_date,paytitle,unit_price,unit_prices,department,groupname,departmentname,receivedpaymentown,servicecontractstype,accountname,signdate,contract_no,total,dividetotal,costing,purchasemount,worksheetcost,productlife,marketprice,dividemarketprice,costdeduction,dividecostdeduction,other,effectiverefund,arriveachievement,achievementmonth,modulestatus,productname,achievementtype,producttype,extracost,salong,waici,meijai,othercost,shareuser,remarks,generatedamount,adjustbeforearriveachievement,divideworksheetcost,dividecosting,dividepurchasemount,divideextracost,divideother,more_years_renew,renewal_commission,renewtimes,splitcontractamount,splitmarketprice,splitcost,commissionforrenewal,refillapplicationid,receivedpaymentsidstr) VALUES ";
            $addStastic_sql = "INSERT INTO vtiger_achievementallot_statistic (".$fieldstr.") VALUES ".$placeholderstr;
            $deleteStastic_sql='DELETE  FROM  vtiger_achievementallot_statistic WHERE refillapplicationid = ?';
            $adb->pquery($deleteStastic_sql,array($params['refillapplicationid']));
            //插入做销售业绩明细表数据
            $adb->pquery($addStastic_sql.$insertValueStr,array($fieldValue));
            //插入汇总表数据
            $sqlquery=" SELECT  achievementallotid FROM vtiger_achievementallot_statistic WHERE refillapplicationid=? AND achievementmonth > 0 ";
            $result = $adb->pquery($sqlquery,array($params['refillapplicationid']));
            $newArray=[];
            while ($rowdatas=$adb->fetch_array($result)){
                $newArray[]=$rowdatas['achievementallotid'];
            }
            //如果有有效数据 插入处理汇总表数据
            if(!empty($newArray)){
                AchievementSummary_Record_Model::newAchievementSummary($newArray);
            }
        }

    }

    //获取业绩所属月份
    public function getspecialAchievementmonth($performanceoftime,$reality_date,$matchdate){
        if($performanceoftime<=$reality_date && $performanceoftime<=$matchdate){
            //查询出 设置的业绩月份
            $closingDate=Vtiger_Record_Model::getInstanceById(2434264,'ClosingDate');
            $date=$closingDate->get("date");
            $reality_dateYm=date("Y-m",strtotime($reality_date));
            //回款时间下月
            $reality_dateNm=date("Y-m",strtotime("+1 months",strtotime(date("Y-m",strtotime($reality_date)))));
            $matchdateYm=date("Y-m",strtotime($matchdate));
            $matchdateD=date("d",strtotime($matchdate));
            //匹配年月等于回款时间年月 或者 匹配时间年月等于回款时间年月的下一月且日期在1-3号之间 则
            if(($reality_dateYm==$matchdateYm) || ($reality_dateNm==$matchdateYm && $matchdateD<=$date)){
                $achievementmonth=$reality_dateYm;
                //如果匹配时间年月等于回款时间年月的下一月且 匹配日期大于3
            }else if($reality_dateNm==$matchdateYm && $matchdateD>$date){
                $achievementmonth=$matchdateYm;
                // 如果
            }else if($matchdateYm>$reality_dateNm){
                if($matchdateD<=$date){
                    $achievementmonth=date("Y-m",strtotime("-1 months",strtotime(date("Y-m",strtotime($matchdate)))));
                }else{
                    $achievementmonth=$matchdateYm;
                }
            }
            return $achievementmonth;
        }else{
            return null;
        }


    }
    //获取业绩所属月份
    public function getAchievementmonth($reality_date,$matchdate){
        $closingDate=Vtiger_Record_Model::getInstanceById(2434264,'ClosingDate');
        $date=$closingDate->get("date");
        $reality_dateYm=date("Y-m",strtotime($reality_date));
        $matchdateYm=date("Y-m",strtotime($matchdate));
        $madate=$matchdateYm.'-'.$date.' 09:00:00';
        if($reality_dateYm==$matchdateYm) {
            $achievementmonth = $reality_dateYm;
            //如果匹配时间年月等于回款时间年月的下一月且 匹配日期大于3
        }else if($matchdateYm>$reality_dateYm){
            //if($matchdateD<=$date){
            if(strtotime($matchdate)<=strtotime($madate)){
                $achievementmonth=date("Y-m",strtotime("-1 months",strtotime(date("Y-m",strtotime($matchdate)))));
            }else{
                $achievementmonth=$matchdateYm;
            }
        }
        return $achievementmonth;
        /*//查询出 设置的业绩月份
        $closingDate=Vtiger_Record_Model::getInstanceById(2434264,'ClosingDate');
        $date=$closingDate->get("date");

        $reality_dateYm=date("Y-m",strtotime($reality_date));
        //回款时间下月
        $reality_dateNm=date("Y-m",strtotime("+1 months",strtotime(date("Y-m",strtotime($reality_date)))));
        $matchdateYm=date("Y-m",strtotime($matchdate));
        $matchdateD=date("d",strtotime($matchdate));
        //匹配年月等于回款时间年月 或者 匹配时间年月等于回款时间年月的下一月且日期在1-3号之间 则
        if(($reality_dateYm==$matchdateYm) || ($reality_dateNm==$matchdateYm && $matchdateD<=$date)){
            $achievementmonth=$reality_dateYm;
            //如果匹配时间年月等于回款时间年月的下一月且 匹配日期大于3
        }else if($reality_dateNm==$matchdateYm && $matchdateD>$date){
            $achievementmonth=$matchdateYm;
            // 如果
        }else if($matchdateYm>$reality_dateNm){
            if($matchdateD<=$date){
                $achievementmonth=date("Y-m",strtotime("-1 months",strtotime(date("Y-m",strtotime($matchdate)))));
            }else{
                $achievementmonth=$matchdateYm;
            }
        }
        return $achievementmonth;*/

    }
    /**
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:2018/4/15 14:31
     *
     */
	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
        $adb = PearDatabase::getInstance();
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $currentid = $currentUser->get('id');
        $last_name = $currentUser->last_name;
        $user_departments = $currentUser->get('current_user_departments');//匹配部门
        $receivepayid=$request->get('receivepayid');
        $contractid=$request->get('contractid');
        $total = $request->get('total');//回
        $input = $request->get('inputalready');
        $shareuser=$request->get('shareuser');
        $receivedstatus=$request->get('receivedstatus');
	$staypaymentid = $request->get("staypaymentid");
        //本地测试用的 测试1 38926
        /*$receivepayid=39060;
        $total=600;
        $shareuser=0;
        $currentid=1;
        $contractid=2206870;*/
        /*$receivepayid=38926;
        $total=1000;
        $shareuser=0;
        $currentid=1;
        $contractid=2206558;*/
        do {
            $query="SELECT receivedpaymentsid FROM vtiger_achievementallot_statistic WHERE receivedpaymentsid= ? AND isover=1 ";
            $resultdata=$adb->pquery($query,array($receivepayid));
            if($adb->num_rows($resultdata)>0){
                $datad=array('flag'=>false,'msg'=>'该回款已匹配且业绩已确认完结!');
                break;
            }
            $query="SELECT vtiger_crmentity.* FROM vtiger_crmentity WHERE crmid=?";
            $result=$adb->pquery($query,array($contractid));
            $resultdata=$adb->query_result_rowdata($result,0);
            $rPaymentsRceordModel=Vtiger_Record_Model::getInstanceById($receivepayid,'ReceivedPayments');
            if($rPaymentsRceordModel->get('relatetoid')>0){
                $datad=array('flag'=>false,'msg'=>'合同已匹配,不允许重新匹配!');
                break;
            }
            if($resultdata['setype']=='SupplierContracts'){
                $sql = "UPDATE vtiger_receivedpayments SET modulename='SupplierContracts',ismatchdepart=1,matchdate='" . date('Y-m-d') . "',relatetoid = ?,newdepartmentid=?,staypaymentid=? WHERE receivedpaymentsid = ?";
                $adb->pquery($sql, array($contractid, $user_departments,$staypaymentid, $receivepayid));
                $modtrackerBasicId = $adb->getUniqueID("vtiger_modtracker_basic");
                $modtrackerBasicData['id'] = $modtrackerBasicId;
                $modtrackerBasicData['crmid'] = $receivepayid;
                $modtrackerBasicData['module'] = 'ReceivedPayments';
                $modtrackerBasicData['whodid'] = $currentid;
                $modtrackerBasicData['changedon'] = date('Y-m-d H:i:s');
                $modtrackerBasicData['status'] = '0';
                $divideNames = array_keys($modtrackerBasicData);
                $divideValues = array_values($modtrackerBasicData);
                $adb->pquery('INSERT INTO `vtiger_modtracker_basic` (' . implode(',', $divideNames) . ') VALUES (' . generateQuestionMarks($divideValues) . ')', $divideValues);

                $modtrackerDetailData = array();
                $modtrackerDetailData['id'] = $modtrackerBasicId;
                $modtrackerDetailData['fieldname'] = 'overdue';
                $modtrackerDetailData['prevalue'] = '';
                $modtrackerDetailData['postvalue'] = $last_name . ' 匹配回款，采购合同编号=' . $resultdata['label'];
                $divideNames = array_keys($modtrackerDetailData);
                $divideValues = array_values($modtrackerDetailData);
                $adb->pquery('INSERT INTO `vtiger_modtracker_detail` (' . implode(',', $divideNames) . ') VALUES (' . generateQuestionMarks($divideValues) . ')', $divideValues);
                $datad=array('flag'=>true,'module'=>'none','msg'=>'供应商合同匹配成功!');

                //记录代付款更新日志
                if($staypaymentid){
                    $recordModelStaypayment = Staypayment_Record_Model::getInstanceById($staypaymentid,'Staypayment');
                    if($recordModelStaypayment->get('staypaymenttype')=='fixation'){
                        $prevalue = $recordModelStaypayment->get('surplusmoney');
                        $postvalue = ($recordModelStaypayment->get('surplusmoney')-$rPaymentsRceordModel->get('unit_price'));
                        $array[0]=array('fieldname'=>'surplusmoney','prevalue'=>$prevalue, 'postvalue'=>$postvalue);
                        $array[1]=array('fieldname'=>'staypaymentname','prevalue'=>'', 'postvalue'=>$rPaymentsRceordModel->get('paytitle'));
                        $this->setModTracker($staypaymentid,$array,'Staypayment');
                        $adb->pquery("update vtiger_staypayment set surplusmoney=? where staypaymentid=?",array($postvalue,$staypaymentid));
                    }
                }

                break;
            }
            $recordModelContract=Vtiger_Record_Model::getInstanceById($contractid,"ServiceContracts");
            if($recordModelContract->get('contractstate')==1){
                $datad=array('flag'=>false,'msg'=>'合同已关闭不允许匹配!');
                break;
            }
            $contractTotal=$recordModelContract->get('total');//合同金额

            $query="SELECT sum(unit_price) AS unit_price FROM vtiger_receivedpayments WHERE relatetoid=? AND receivedstatus='normal' AND deleted=0";
            $resultdata=$adb->pquery($query,array($contractid));
            $totalReceivedpayments=0;

            if($adb->num_rows($resultdata)>0){
                $contractdata=$adb->query_result_rowdata($resultdata,0);
                $totalReceivedpayments=$contractdata['unit_price'];
            }
            $totalReceivedpayments=bcadd($total,$totalReceivedpayments,2);
            if(bccomp($contractTotal,$totalReceivedpayments,2)<0 && $contractTotal>0 && $recordModelContract->get('isautoclose')==1){
                $datad=array('flag'=>false,'msg'=>'回款金额大于合同金额,请联系相关人员进行拆分!');
                break;
            }
            if($receivedstatus=='deposit'){
                $sql = "UPDATE vtiger_receivedpayments SET modulename='ServiceContracts',receivedstatus='deposit',ismatchdepart=1,matchdate='" . date('Y-m-d') . "',relatetoid = ?,newdepartmentid=?,staypaymentid=? WHERE receivedpaymentsid = ?";
                $adb->pquery($sql, array($contractid, $user_departments,$staypaymentid, $receivepayid));
                $modtrackerBasicId = $adb->getUniqueID("vtiger_modtracker_basic");
                $modtrackerBasicDatad['id'] = $modtrackerBasicId;
                $modtrackerBasicDatad['crmid'] = $receivepayid;
                $modtrackerBasicDatad['module'] = 'ReceivedPayments';
                $modtrackerBasicDatad['whodid'] = $currentid;
                $modtrackerBasicDatad['changedon'] = date('Y-m-d H:i:s');
                $modtrackerBasicDatad['status'] = '0';
                $divideNamesd = array_keys($modtrackerBasicDatad);
                $divideValuesd = array_values($modtrackerBasicDatad);
                $adb->pquery('INSERT INTO `vtiger_modtracker_basic` (' . implode(',', $divideNamesd) . ') VALUES (' . generateQuestionMarks($divideValuesd) . ')', $divideValuesd);
                $adb->pquery("INSERT INTO `vtiger_modtracker_detail` (id,fieldname,prevalue,postvalue) VALUES ($modtrackerBasicId,'receivedstatus','normal','deposit')", array());
                $adb->pquery("INSERT INTO `vtiger_modtracker_detail` (id,fieldname,prevalue,postvalue) VALUES ($modtrackerBasicId,'relatetoid','0',{$contractid})", array());
                $datad=array('flag'=>true,'module'=>'none','msg'=>'保证金匹配成功!');

                //记录代付款更新日志
                if($staypaymentid){
                    $recordModelStaypayment = Staypayment_Record_Model::getInstanceById($staypaymentid,'Staypayment');
                    if($recordModelStaypayment->get('staypaymenttype')=='fixation'){
                        $prevalue = $recordModelStaypayment->get('surplusmoney');
                        $postvalue = ($recordModelStaypayment->get('surplusmoney')-$rPaymentsRceordModel->get('unit_price'));
                        $array[0]=array('fieldname'=>'surplusmoney','prevalue'=>$prevalue, 'postvalue'=>$postvalue);
                        $array[1]=array('fieldname'=>'staypaymentname','prevalue'=>'', 'postvalue'=>$rPaymentsRceordModel->get('paytitle'));
                        $this->setModTracker($staypaymentid,$array,'Staypayment');
                        $adb->pquery("update vtiger_staypayment set surplusmoney=? where staypaymentid=?",array($postvalue,$staypaymentid));
                    }
                }
                break;
            }
            if ($shareuser == 1) {
                //共享商务独占业绩
                $update_achieve = "INSERT INTO vtiger_achievementallot (achievementallotid,owncompanys,receivedpaymentownid,scalling,servicecontractid,receivedpaymentsid,businessunit,matchdate,departmentid)
SELECT NULL ,owncompanys,{$currentid},100,servicecontractid,?,?,'" . date('Y-m-d') . "',signdempart FROM vtiger_servicecontracts_divide WHERE servicecontractid = ? limit 1";
            } else {
                $update_achieve = "INSERT INTO vtiger_achievementallot (achievementallotid,owncompanys,receivedpaymentownid,scalling,servicecontractid,receivedpaymentsid,businessunit,matchdate,departmentid)
SELECT NULL ,owncompanys,receivedpaymentownid,scalling,servicecontractid,?,?*(scalling/100),'" . date('Y-m-d') . "',signdempart FROM vtiger_servicecontracts_divide WHERE servicecontractid = ? ";
            }
            $sql = "UPDATE vtiger_receivedpayments SET modulename='ServiceContracts',receivedstatus='normal',ismatchdepart=1,matchdate='" . date('Y-m-d') . "',relatetoid = ?,newdepartmentid=?,staypaymentid=? WHERE receivedpaymentsid = ?";
            $sql_type = "UPDATE vtiger_receivedpayments SET newrenewa = ? WHERE receivedpaymentsid = ?";

            $deltet_sql = "DELETE  FROM  vtiger_achievementallot WHERE receivedpaymentsid = ?";
            $insert_history = "INSERT INTO vtiger_receivedpayments_matchhistory  (time,creatid,contractid,receivement) VALUES(NOW(),?,?,?)";

            if ($receivepayid && $contractid && $total) {
                $receivepayment_data = Vtiger_Record_Model::getInstanceById($receivepayid, 'ReceivedPayments');
                $ttt = $receivepayment_data->getdata();
                $reality_date = $ttt['reality_date']; //回款的信息；
                //$contra_data = Vtiger_Record_Model::getInstanceById($receivepayid, 'ReceivedPayments');
                //$tttt = $contra_data->getdata();
                $contract_type = $ttt['servicecontractstype'];

                //首次回款的判定
                $tempResult=$adb->pquery('SELECT 1 FROM vtiger_receivedpayments WHERE relatetoid = ? AND receivedstatus=\'normal\' AND deleted=0', array($contractid));
                $tempNumRows=$adb->num_rows($tempResult);

                $adb->pquery($sql, array($contractid, $user_departments,$staypaymentid, $receivepayid));
                $adb->pquery($deltet_sql, array($receivepayid));
                $adb->pquery($update_achieve, array($receivepayid, $total, $contractid));//跟新分成历史
                $adb->pquery($insert_history, array($currentid, $contractid, $receivepayid));//匹配历史

                //更新首次回款时间;
                if (!empty($contractid)) {
<<<<<<< .mine
                    $tempResult=$adb->pquery('SELECT 1 FROM vtiger_receivedpayments WHERE relatetoid = ? AND receivedstatus=\'normal\' AND deleted=0', array($contractid));
                    $tempNumRows=$adb->num_rows($tempResult);
||||||| .r15484
                    $tempResult=$adb->pquery('SELECT * FROM vtiger_receivedpayments WHERE relatetoid = ? AND receivedstatus=\'normal\' AND deleted=0', array($contractid));
                    $tempNumRows=$adb->num_rows($tempResult);
=======
>>>>>>> .r15507
                    if ($tempNumRows== 0) {
                        $adb->pquery('UPDATE vtiger_servicecontracts SET firstreceivepaydate = ? WHERE servicecontractsid = ?', array($reality_date, $contractid));
                    }

                    if ($contract_type == '新增' && $tempNumRows == 0) {
                        $adb->pquery($sql_type, array('新增', $receivepayid));
                    } else {
                        $adb->pquery($sql_type, array('续费', $receivepayid));
                    }

                }
                ReceivedPayments_Record_Model::save_modules($receivepayid, $contractid, $input);


                // 更新 是否正常状态 2016-10-10 周海
                // 1)   当合同金额>0的情况下，若合同的收款金额>=合同金额，则合同自动关闭
                $sql = "select * from vtiger_servicecontracts where servicecontractsid=? AND isautoclose='1'";
                $sel_result = $adb->pquery($sql, array($contractid));
                $res_cnt = $adb->num_rows($sel_result);
                if ($res_cnt > 0) {
                    $row = $adb->query_result_rowdata($sel_result, 0);

                    // 回款的金额
                    $sql = "select sum(unit_price) AS unit_price_total  from vtiger_receivedpayments where receivedstatus='normal' AND relatetoid=?";
                    $sel_result = $adb->pquery($sql, array($contractid));
                    $res_cnt = $adb->num_rows($sel_result);
                    if ($res_cnt > 0) {
                        $receivedpayments_row = $adb->query_result_rowdata($sel_result, 0);
                    }

                    // 合同金额>0  回款金额>=合同金额
                    if (intval($row['total']) > 0 && intval($receivedpayments_row['unit_price_total']) >= intval($row['total'])) {
                        // 合同自动关闭
                        $sql = "update vtiger_servicecontracts set contractstate=? where servicecontractsid=? AND isautoclose='1'";
                        $adb->pquery($sql, array('1', $contractid));
                    }
                }

                // 匹配回款时，对回款做更新记录 vtiger_modtracker_basic
                $modtrackerBasicData = array();

                $modtrackerBasicId = $adb->getUniqueID("vtiger_modtracker_basic");
                $modtrackerBasicData['id'] = $modtrackerBasicId;
                $modtrackerBasicData['crmid'] = $receivepayid;
                $modtrackerBasicData['module'] = 'ReceivedPayments';
                $modtrackerBasicData['whodid'] = $currentid;
                $modtrackerBasicData['changedon'] = date('Y-m-d H:i:s');
                $modtrackerBasicData['status'] = '0';
                $divideNames = array_keys($modtrackerBasicData);
                $divideValues = array_values($modtrackerBasicData);
                $adb->pquery('INSERT INTO `vtiger_modtracker_basic` (' . implode(',', $divideNames) . ') VALUES (' . generateQuestionMarks($divideValues) . ')', $divideValues);

                $sql = "select * from vtiger_servicecontracts left join vtiger_account on vtiger_servicecontracts.sc_related_to=vtiger_account.accountid where vtiger_servicecontracts.servicecontractsid=?";
                $sel_result = $adb->pquery($sql, array($contractid));
                $res_cnt = $adb->num_rows($sel_result);
                $contract_no = '';
                $accountid = 0;
                $accountname = '';
                if ($res_cnt > 0) {
                    $row = $adb->query_result_rowdata($sel_result, 0);
                    $contract_no = $row['contract_no'];
                    $accountid = $row['sc_related_to'];
                    $invoicecompany = $row['invoicecompany'];
                    $intentionality = $row['intentionality'];
                    $accountname = $row['accountname'];
                }
                $adb->pquery("update vtiger_receivedpayments set accountname=? where receivedpaymentsid = ?",array($accountname,$receivepayid));

                // vtiger_modtracker_detail
                $modtrackerDetailData = array();
                $modtrackerDetailData['id'] = $modtrackerBasicId;
                $modtrackerDetailData['fieldname'] = 'overdue';
                $modtrackerDetailData['prevalue'] = '';
                $modtrackerDetailData['postvalue'] = $last_name . ' 匹配回款，合同编号=' . $contract_no;


                //插入客户更新
                $array[0]=array('fieldname'=>'intentionality','prevalue'=>'', 'postvalue'=>$last_name.' 客户合同已回款，客户意向度由“'.vtranslate($intentionality,'Accounts').'”变更成“0%”');
                $this->setModTracker($accountid,$array);
                $adb->pquery("update vtiger_account set intentionality='zeropercentage' where accountid = ?",array($accountid));


                $divideNames = array_keys($modtrackerDetailData);
                $divideValues = array_values($modtrackerDetailData);
                $adb->pquery('INSERT INTO `vtiger_modtracker_detail` (' . implode(',', $divideNames) . ') VALUES (' . generateQuestionMarks($divideValues) . ')', $divideValues);

                // 回款记录
                $receivedpaymentsNotesId = $adb->getUniqueID("vtiger_receivedpayments_notes");
                $receivedpaymentsNotesData = array(
                    'createtime' => date('Y-m-d H:i:s'),
                    'smownerid' => $currentid,
                    'receivedpaymentsid' => $receivepayid,
                    'notestype' => 'notestype1',
                    'receivedpaymentsnotesid' => $receivedpaymentsNotesId
                );
                $divideNames = array_keys($receivedpaymentsNotesData);
                $divideValues = array_values($receivedpaymentsNotesData);
                $adb->pquery('INSERT INTO `vtiger_receivedpayments_notes` (' . implode(',', $divideNames) . ') VALUES (' . generateQuestionMarks($divideValues) . ')', $divideValues);
                $datad=array('flag'=>true,'module'=>'none','msg'=>'');
                // 回款匹配修改客户的垫款
                if ($accountid > 0) {
                    //steel.liu
                    //Accounts_Record_Model::setAdvancesmoney($accountid, - $total, '(回款直接匹配合同)');
                    $datadd = $this->getCanNewIvoiceInfo($accountid, $invoicecompany);
                    if (!empty($datadd)) {
                        $msg = '<table class="table table-bordered equalSplit detailview-table"><caption><h3>预开票匹配</h3></caption>' . $datadd . '</table>';
                        $datad=array('flag'=>true,'module'=>'invoice','msg'=>$msg);
                    }
                }
                //记录代付款更新日志
                if($staypaymentid){
                    $recordModelStaypayment = Staypayment_Record_Model::getInstanceById($staypaymentid,'Staypayment');
                    if($recordModelStaypayment->get('staypaymenttype')=='fixation'){
                        $prevalue = $recordModelStaypayment->get('surplusmoney');
                        $postvalue = ($recordModelStaypayment->get('surplusmoney')-$rPaymentsRceordModel->get('unit_price'));
                        $array[0]=array('fieldname'=>'surplusmoney','prevalue'=>$prevalue, 'postvalue'=>$postvalue);
                        $array[1]=array('fieldname'=>'staypaymentname','prevalue'=>'', 'postvalue'=>$rPaymentsRceordModel->get('paytitle'));
                        $this->setModTracker($staypaymentid,$array,'Staypayment');
                        $adb->pquery("update vtiger_staypayment set surplusmoney=? where staypaymentid=?",array($postvalue,$staypaymentid));
                    }
                }

                $recordModel = Matchreceivements_Record_Model::getCleanInstance("Matchreceivements");
                $recordModel->matchToRanking($receivepayid);

		// 修改新增分成记录 vtiger_achievementallot_statistic
                $this->commonInsertAchievementallotStatistic($receivepayid,$total,$shareuser,$currentid,$contractid);
            }
        }while(0);
		$response = new Vtiger_Response();
		$response->setResult($datad);
		$response->emit();
	}
	public function getCanNewIvoiceInfo($accountid,$invoicecompany){
        $db=PearDatabase::getInstance();
        $query="SELECT 
                  vtiger_newinvoice.actualtotal,
                  vtiger_newinvoice.invoiceid,
                  vtiger_newinvoice.invoiceno,
                  vtiger_newinvoice.trialtime,
                  vtiger_newinvoice.businessnames,
                  (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id) as ownerid,
                  IFNULL((SELECT sum(vtiger_newinvoicerayment.invoicetotal) FROM vtiger_newinvoicerayment WHERE vtiger_newinvoicerayment.deleted=0 AND vtiger_newinvoicerayment.invoiceid=vtiger_newinvoice.invoiceid),0) AS suminvoicetotal 
                FROM 
                  vtiger_newinvoice 
                LEFT JOIN 
                  vtiger_crmentity ON 
                    vtiger_crmentity.crmid=vtiger_newinvoice.invoiceid 
                WHERE 
                  vtiger_crmentity.deleted=0 AND 
                  vtiger_newinvoice.accountid=? AND 
                  vtiger_newinvoice.invoicecompany=? AND 
                  vtiger_newinvoice.invoicetype='c_billing' 
                  AND  vtiger_newinvoice.modulestatus!='c_cancel'
                  GROUP BY vtiger_newinvoice.invoiceid";
        $result=$db->pquery($query,array($accountid,$invoicecompany));
        $datas='';
        $num=$db->num_rows($result);
        for($i=0;$i<$num;$i++){
            $resultData=$db->query_result_rowdata($result, $i);
            if($resultData['actualtotal']==0 || ($resultData['actualtotal']>$resultData['suminvoicetotal'])){
                $datas.='<tr class="fieldValue medium"><td><a href="/index.php?module=Newinvoice&view=Detail&record='.$resultData['invoiceid'].'" target="_blank">'.$resultData['invoiceno'].'['.$resultData['trialtime'].']'.'['.$resultData['businessnames'].']'.'['.$resultData['ownerid'].']'.'['.$resultData['actualtotal'].']'.'</a></td></tr>';
            }
        }

        return $datas;
    }

    public function setModTracker($recordId,$array,$module='Accounts'){
        $db=PearDatabase::getInstance();
        global $current_user;
        $datetime=date('Y-m-d H:i:s');
        $id= $db->getUniqueId('vtiger_modtracker_basic');
        $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
            array($id , $recordId, $module, $current_user->id, $datetime, 0));
        foreach($array as $value){
            $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, $value['fieldname'],$value['prevalue'], $value['postvalue']));
        }
    }

    /**
     * 数据的转换
     * @param $tempArray
     * @return array
     */
    public function returnDataValue($tempArray){
        $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
        $Matchreceivements_Record_Model->setContractAchSetting($tempArray);
	    $fieldArr=array_keys($tempArray[0]);
        $fieldstr=implode(',',$fieldArr);
        $placeholderArr=array_map(function($b){return '?';},$fieldArr);
	    $placeholderstr='';
        $tempArrayCount=count($tempArray);
        for($i=0;$i<$tempArrayCount;$i++){
            $placeholderstr.='('.implode(',',$placeholderArr).'),';
        }
	    $fieldValue=array();
	    foreach($tempArray as $value){
            $fieldValue=array_merge($fieldValue,array_values($value));
        }
        return array('field'=>$fieldstr,'insertValueStr'=>trim($placeholderstr,','),'datavalue'=>$fieldValue);
	}

    /**
     * 获取回款的相关系统
     * @param $receivepayid
     * @throws Exception
     */
	public function getReceivedpaymentsInfo($receivepayid){
        $adb = PearDatabase::getInstance($receivepayid);
        //① 查询回款相关数据  vtiger_receivedpayments
        $queryc = "SELECT s.multitype,s.contract_no,s.oldcontract_usedtime,s.oldcontractid,s.extraproductid,s.productid,s.invoicecompany,s.parent_contracttypeid,s.contract_type,s.servicecontractsid,s.total,r.owncompany,r.matchdate,LEFT (r.createtime,10) AS createtime,r.reality_date,r.matchdate,r.paytitle,r.unit_price,d.departmentname as department,s.servicecontractstype,left(s.signdate,10) AS signdate,s.contract_no,a.accountname FROM vtiger_receivedpayments  as r LEFT JOIN vtiger_departments as d ON r.departmentid=d.departmentid LEFT JOIN vtiger_servicecontracts as s ON s.servicecontractsid=r.relatetoid LEFT JOIN vtiger_account as a ON a.accountid=s.sc_related_to  WHERE receivedpaymentsid = ?  ORDER BY receivedpaymentsid DESC LIMIT 1 ";
        $resultdatapayments = $adb->pquery($queryc, array($receivepayid));
        return $adb->query_result_rowdata($resultdatapayments, 0);
    }
}
