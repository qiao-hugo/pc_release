<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Inventory Record Model Class
 */
class AchievementSummary_Record_Model extends Vtiger_Record_Model {
    public $subordinateUsers=array();//下属数组
    public $employeeBaseSalary=array(
        0=>0,
        1=>900,
        2=>5800,
        3=>6650,
        4=>8550,
        5=>30150
        );//员工提成底薪梯度
    public $employeeBaseAchievement=array(
        0=>0,
        1=>10000,
        2=>45000,
        3=>50000,
        4=>60000,
        5=>150000
    );//员工业绩保底梯度
    public $newrecruitsemploeeRoyaltyRatio=array( 0=>9);//商务顾问、储备干部，见习营销顾问员工业绩核算比例梯度Manager
    public $emploeeRoyaltyRatio=array(
        0=>9,//2021-02-02年调整为9原来为4
        1=>9,
        2=>14,
        3=>17,
        4=>19,
        5=>22,
        6=>24,
        7=>29
    );//营销顾问，高级营销顾问，资深营销顾问，客户经理，客户总监等其他所有,员工业绩核算比例梯度Manager
    public $managerBaseSalary=array(
        0=>0,
        1=>960,
        2=>1320,
        3=>2440,
        4=>3200,
        5=>0,
        6=>0
    );//经理提成底薪梯度
    public $managerBaseAchievement=array(
        0=>0,
        1=>120000,
        2=>140000,
        3=>180000,
        4=>200000,
        5=>0,
        6=>0
    );//经理业绩保底梯度
    public $managerRoyaltyRatio=array(
        0=>8,
        1=>18,
        2=>28,
        3=>38,
        4=>48,
        5=>48,
        6=>58
    );//经理业绩核算比例梯度千分计
    public $areaManagerRoyaltyRatio=array(
        1=>array('managerBaseSalary'=>array(
            0=>0,
            1=>960,
            2=>1320,
            3=>2440,
            4=>3200,
            5=>0,
            6=>0
        ),'baseAmountRatio'=>1,),//第一类
        2=>array('managerBaseSalary'=>array(
            0=>0,
            1=>864,
            2=>1188,
            3=>2196,
            4=>2880,
            5=>0,
            6=>0
        ),'baseAmountRatio'=>0.9),//第二类
        3=>array('managerBaseSalary'=>array(
            0=>0,
            1=>768,
            2=>1056,
            3=>1952,
            4=>2560,
            5=>0,
            6=>0
        ),'baseAmountRatio'=>0.8),//第二类
        4=>array('managerBaseSalary'=>array(
            0=>0,
            1=>672,
            2=>924,
            3=>1708,
            4=>2240,
            5=>0,
            6=>0
        ),'baseAmountRatio'=>0.7),//第二类
    );//地区经理的阶梯
    public $regionalLevel=array(
        11=>1,//	上海洞察力软件信息科技有限公司
        16=>1,//	凯丽隆（上海）软件信息科技有限公司
        14=>1,//	上海龙教信息技术有限公司
        15=>1,//	上海珍岛网络科技有限公司
        10=>1,//	AMERICAN KAILILONG INTERNATIONAL HOLDING (H.K.) LIMITED
        18=>1,//	凯丽隆（广州）信息科技有限公司
        19=>1,//	凯丽隆国际控股（香港）有限公司
        14=>1,//	上海珍岛云计算科技有限公司
        17=>1,//	无锡凯丽隆广告科技有限公司
        26=>1,//	上海珍岛智能技术集团有限公司
        27=>1,//	上海凯丽隆大数据科技集团有限公司



        //特级
        3=>1,//珍岛信息技术（上海）股份有限公司
        2=>1,//	深圳市珍岛信息技术有限公司
        28=>1,//上海珍岛智能技术集团有限公司广州分公司

        //一级
        1=>2,//	苏州珍岛信息技术有限公司
        5=>2,//	杭州珍岛信息技术有限公司
        4=>2,//	宁波珍岛信息技术有限公司
        8=>2,//	无锡珍岛数字生态服务平台技术有限公司
        20=>2,//无锡珍岛智能技术有限公司


        //二级
        21=>3,//	无锡珍岛数字生态服务平台技术有限公司江阴分公司
        22=>3,//	金华市珍岛信息技术有限公司
        23=>3,//	中山珍岛信息技术有限公司
        24=>3,//	台州珍岛信息技术有限公司
        29=>3,//	上海珍岛智能技术集团有限公司东莞分公司
        30=>3,//	上海珍岛智能技术集团有限公司佛山分公司

        //三级
        25=>4,//	昆山珍岛信息技术有限公司
        6=>4,//	成都珍岛信息技术有限公司
        7=>4,//	温州珍岛信息技术有限公司
            );
    public $achievementOwnerInfo=array();

    /**
     * @param $param
     */
    public function calculationRoalty($param){

        echo $param['achievementtype'];echo "\n";
        echo $param['staffrank'];echo "\n";
        global $adb;
        //如果计算的是12 月份的业绩 同时要累加之前的 年度折扣作为年度发放
        $royalty=0;// 提成
        if($param['achievementtype']=='newadd'){
             //是经理
             if($param['staffrank']==1) {
                 $subordinate_users=$this->subordinateUsers;
                 //include('crmcache/subordinateusers.php');
                 $userIds=$subordinate_users[$param['userid']];// 该经理的所有下属包含所有的经理
                 $userIds=array(6698);
                 echo $param['userids'];echo "\n";
                 $param['userids']=explode(",",$param['userids']);
                 var_dump($param['userids']);echo "\n";
                 $userIds=array_diff($userIds,$param['userids']);// 去掉该下属中包含的经理。
                 if(empty($userIds)){
                     return ;
                 }
                 var_dump($userIds);echo "\n";
                 $userIds=" userid IN (".implode(",",$userIds).")";
                 var_dump($userIds);echo "\n";
                 //汇总该经理所有下属员工的到账业绩  回款金额
                 $sql=" SELECT SUM(realarriveachievement) as realarriveachievement,SUM(arriveachievement) as arriveachievement,SUM(unit_price) as unit_price,SUM(effectiverefund) as effectiverefund FROM `vtiger_achievementsummary` WHERE ".$userIds." AND achievementmonth=? AND achievementtype='newadd' LIMIT 1 ";
                 $result=$adb->pquery($sql,array($param['calculation_year_month']));
                 $data=$adb->query_result_rowdata($result,0);
                 // 六个月以上的老经理    见习经理/经理：H18 < 80000;高级经理：H18 < 160000； 提成 = 0；
                 if(($data['realarriveachievement']<80000 && $param['newmanagersixmonths']!=1 && ($param['usergrade']==9 || $param['usergrade']==10)) || ($data['realarriveachievement']<160000 && $param['newmanagersixmonths']!=1 && $param['usergrade']==11)){
                     $royalty=0;
                 // 除了上面特殊的 下面为正常计算流程
                 }else if(300000 < $data['realarriveachievement']){
                     $royalty=$data['realarriveachievement']*0.998*0.06;
                 }else if(250000<=$data['realarriveachievement'] && $data['realarriveachievement']<=300000){
                     $royalty=$data['realarriveachievement']*0.998*0.05;
                 }else if(200000<$data['realarriveachievement'] && $data['realarriveachievement']<250000){
                     $royalty=($data['realarriveachievement']-20000)*0.998*0.05+3600;
                 }else if(180000<$data['realarriveachievement'] && $data['realarriveachievement']<=200000){
                     $royalty=($data['realarriveachievement']-180000)*0.998*0.04+2800;
                 }else if(140000<$data['realarriveachievement'] && $data['realarriveachievement']<=180000){
                     $royalty=($data['realarriveachievement']-140000)*0.998*0.03+1600;
                 }else if(120000<$data['realarriveachievement'] && $data['realarriveachievement']<=140000){
                     $royalty=($data['realarriveachievement']-120000)*0.998*0.02+1200;
                 }else if(0<$data['realarriveachievement'] && $data['realarriveachievement']<=120000){
                     $royalty=$data['realarriveachievement']*0.998*0.01;
                 }
                 $sql=" SELECT vtiger_users.invoicecompany,vtiger_user2department.departmentid FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id  WHERE vtiger_users.id=? ";
                 $userInfo=$adb->pquery($sql,array($param['userid']));
                 $userInfo=$adb->query_result_rowdata($userInfo,0);

                 $annualdiscount=$data['realarriveachievement']*0.002;// 年度折扣
                 // 如果计算提成的月份是12月份 则计算年度发放 否则 年度发放为0
                 if($param['calculation_month']==12){
                     $annualpayment=$annualdiscount+$this->getOneToElevenAnnualdiscount($param);
                 }else{
                     $annualpayment=0;
                 }

                 $params['invoicecompany']=$userInfo['invoicecompany'];
                 $params['departmentid']=$userInfo['invoicecompany'];
                 $params['userid']=$param['userid'];
                 $params['unit_price']=$data['unit_price'];
                 $params['arriveachievement']=$data['arriveachievement'];
                 $params['adjustachievement']=0;
                 $params['realarriveachievement']=$data['realarriveachievement'];
                 $params['annualdiscount']=$annualdiscount;// 年度折扣
                 $params['annualpayment']=$annualpayment;// 年度发放
                 $params['royalty']=$royalty;// 提成
                 $params['actualroyalty']=$royalty+$annualpayment;// 实际提成=提成+年度发放
                 $params['effectiverefund']=$data['effectiverefund'];
                 $params['achievementmonth']=$param['calculation_year_month'];
                 $params['confirmstatus']='tobeconfirm';
                 $params['modulestatus']='a_normal';
                 $sql="INSERT INTO `vtiger_achievementsummary` (`invoicecompany`, `departmentid`, `userid`, `unit_price`, `arriveachievement`, `realarriveachievement`, `effectiverefund`, `achievementmonth`, `createtime`, `adjustachievement`, `employeelevel`, `royalty`, `actualroyalty`, `achievementtype`, `confirmstatus`, `modulestatus`,`annualdiscount`, `annualpayment`) 
                       VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                 $adb->pquery($sql,array($params['invoicecompany'],$params['departmentid'],$params['userid'],$params['unit_price'],$params['arriveachievement'],$params['realarriveachievement'],$params['effectiverefund'],$params['achievementmonth'],date("Y-m-d H:i:s"),0,$param['gradename'],$params['royalty'],$params['actualroyalty'],$param['achievementtype'],$params['confirmstatus'],$params['modulestatus'],$params['annualdiscount'],$params['annualpayment']));
             //普通员工
             }else{
                  $annualdiscount=$param['realarriveachievement']*0.01;// 年度折扣
                  // 如果计算提成的月份是12月份 则计算年度发放 否则 年度发放为0
                  if($param['calculation_month']==12){
                      $annualpayment=$annualdiscount+$this->getOneToElevenAnnualdiscount($param);
                  }else{
                      $annualpayment=0;
                  }
                  // 员工等级分别为什么的时候
                  if($param['usergrade']==1 || $param['usergrade']==2 || $param['usergrade']==3){
                      $royalty=$param['realarriveachievement']*0.1;
                  }else if($param['usergrade']==4 || $param['usergrade']==5 || $param['usergrade']==6 || $param['usergrade']==7 || $param['usergrade']==8){
                      if(150000< $param['realarriveachievement'] ){
                          $royalty=($param['realarriveachievement']-150000)*0.99*0.3+29900;
                      }else if(60000<$param['realarriveachievement'] && $param['realarriveachievement']<=150000){
                          $royalty=($param['realarriveachievement']-60000)*0.99*0.25+7400;
                      }else if(50000<$param['realarriveachievement'] && $param['realarriveachievement']<=60000){
                          $royalty=($param['realarriveachievement']-50000)*0.99*0.2+5400;
                      }else if(45000<$param['realarriveachievement'] && $param['realarriveachievement']<=50000){
                          $royalty=($param['realarriveachievement']-45000)*0.99*0.18+4500;
                      }else if(35000<$param['realarriveachievement'] && $param['realarriveachievement']<=45000){
                          $royalty=($param['realarriveachievement']-35000)*0.99*0.15+3000;
                      }else if(0<$param['realarriveachievement'] && $param['realarriveachievement']<=35000){
                          $royalty=$param['realarriveachievement']*0.99*0.1;
                      }
                  }
                  $actualroyalty=$royalty+$annualpayment;
                  $sql=" UPDATE vtiger_achievementsummary SET royalty=?,actualroyalty=?,annualdiscount=?,annualpayment=?,employeelevel=? WHERE achievementid=? ";
                  $adb->pquery($sql,array($royalty,$actualroyalty,$annualdiscount,$annualpayment,$param['gradename'],$param['achievementid']));
             }
         //续费
        }else if($param['achievementtype']=='renew'){
              // 是经理级别有下属
             if($param['staffrank']==1){
                 //查出部门所有用户userid
                 $subordinate_users=$this->subordinateUsers;
                 /*if (empty($subordinate_users)){
                     $this->setSubordinateUsers();
                     $subordinate_users=$this->subordinateUsers;
                 }*/
                 //include('crmcache/subordinateusers.php');
                 $userIds=$subordinate_users[$param['userid']];// 该经理的所有下属包含所有的经理
                 $userIds=array(6698);
                 echo $param['userids'];echo "\n";
                 $param['userids']=explode(",",$param['userids']);
                 var_dump($param['userids']);echo "\n";
                 $userIds=array_diff($userIds,$param['userids']);// 去掉该下属中包含的经理。
                 if(empty($userIds)){
                     return ;
                 }
                 var_dump($userIds);echo "\n";
                 $userIds=" userid IN (".implode(",",$userIds).")";
                 var_dump($userIds);echo "\n";
                 //汇总该经理所有下属员工的到账业绩  回款金额
                 $sql=" SELECT SUM(realarriveachievement) as realarriveachievement,SUM(arriveachievement) as arriveachievement,SUM(unit_price) as unit_price,SUM(effectiverefund) as effectiverefund FROM `vtiger_achievementsummary` WHERE ".$userIds." AND achievementmonth=? AND achievementtype='renew' LIMIT 1 ";
                 $result=$adb->pquery($sql,array($param['calculation_year_month']));
                 $data=$adb->query_result_rowdata($result,0);
                 //查询他手下的所有实际到账业绩
                 $royalty=$data['realarriveachievement']*0.998*0.01;
                 $sql=" SELECT vtiger_users.invoicecompany,vtiger_user2department.departmentid FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id  WHERE vtiger_users.id=? ";
                 $userInfo=$adb->pquery($sql,array($param['userid']));
                 $userInfo=$adb->query_result_rowdata($userInfo,0);
                 $annualdiscount=$data['realarriveachievement']*0.002;// 年度折扣
                 //如果计算提成的月份是12月份 则计算年度发放 否则 年度发放为0
                 if($param['calculation_month']==12){
                     $annualpayment=$annualdiscount+$this->getOneToElevenAnnualdiscount($param);
                 }else{
                     $annualpayment=0;
                 }
                 $params['invoicecompany']=$userInfo['invoicecompany'];
                 $params['departmentid']=$userInfo['invoicecompany'];
                 $params['userid']=$param['userid'];
                 $params['unit_price']=$data['unit_price'];
                 $params['arriveachievement']=$data['arriveachievement'];
                 $params['adjustachievement']=0;
                 $params['realarriveachievement']=$data['realarriveachievement'];
                 $params['annualdiscount']=$annualdiscount;// 年度折扣
                 $params['annualpayment']=$annualpayment;// 年度发放
                 $params['royalty']=$royalty;// 提成
                 $params['actualroyalty']=$royalty+$annualpayment;// 实际提成=提成+年度发放
                 $params['effectiverefund']=$data['effectiverefund'];
                 $params['achievementmonth']=$param['calculation_year_month'];
                 $params['confirmstatus']='tobeconfirm';
                 $params['modulestatus']='a_normal';
                 $sql="INSERT INTO `vtiger_achievementsummary` (`invoicecompany`, `departmentid`, `userid`, `unit_price`, `arriveachievement`, `realarriveachievement`, `effectiverefund`, `achievementmonth`, `createtime`, `adjustachievement`, `employeelevel`, `royalty`, `actualroyalty`, `achievementtype`, `confirmstatus`, `modulestatus`,`annualdiscount`, `annualpayment`) 
                       VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                 $adb->pquery($sql,array($params['invoicecompany'],$params['departmentid'],$params['userid'],$params['unit_price'],$params['arriveachievement'],$params['realarriveachievement'],$params['effectiverefund'],$params['achievementmonth'],date("Y-m-d H:i:s"),0,$param['gradename'],$params['royalty'],$params['actualroyalty'],$param['achievementtype'],$params['confirmstatus'],$params['modulestatus'],$params['annualdiscount'],$params['annualpayment']));
                 //普通员工
             }else{
                 //seo系列、等需要长期维护的服务 的提成计算
                 $sql=" SELECT SUM(a.arriveachievement*0.99*0.05) as royaltyone  FROM `vtiger_achievementallot_statistic` as a INNER JOIN vtiger_servicecontracts as s ON s.servicecontractsid=a.servicecontractid  WHERE   a.receivedpaymentownid=?  AND  a.achievementmonth=?  AND  a.achievementtype='renew' AND s.parent_contracttypeid IN(4,6) AND  s.contract_type IN('百度微整站优化续费服务合同','百度微整站优化服务合同','搜索引擎左侧优化续费协议书','KA搜索引擎优化','T-网营销销售合同','整合营销合同','网络口碑营销合同书')  LIMIT 1 ";
                 $result=$adb->pquery($sql,array($param['userid'],$param['calculation_year_month']));
                 $data=$adb->query_result_rowdata($result,0);
                 $royaltyOne=$data['royaltyone'];
                 //T- SITE系列、T云系列、微网站系列、空间服务器系列等一次性交付类产品  除了 seo系列、等需要长期维护的服务 的提成计算 和 域名空间类型续费，商务不核算提成；
                 $sql=" SELECT SUM(a.arriveachievement*0.99*0.06) as royaltytwo  FROM `vtiger_achievementallot_statistic` as a INNER JOIN vtiger_servicecontracts as s ON s.servicecontractsid=a.servicecontractid  WHERE   a.receivedpaymentownid=?  AND  a.achievementmonth=?  AND  a.achievementtype='renew'  AND  s.contract_no NOT LIKE ? AND (s.parent_contracttypeid NOT IN(4,6) || s.contract_type NOT IN('百度微整站优化续费服务合同','百度微整站优化服务合同','搜索引擎左侧优化续费协议书','KA搜索引擎优化','T-网营销销售合同','整合营销合同','网络口碑营销合同书'))  LIMIT 1 ";
                 $result=$adb->pquery($sql,array($param['userid'],$param['calculation_year_month'],'TSITEXF'));
                 $data=$adb->query_result_rowdata($result,0);
                 $royaltyTwo=$data['royaltytwo'];
                 $royalty=$royaltyOne+$royaltyTwo;
                 $annualdiscount=$param['realarriveachievement']*0.01;
                 //如果计算提成的月份是12月份 则计算年度发放 否则 年度发放为0
                 if($param['calculation_month']==12){
                     $annualpayment=$annualdiscount+$this->getOneToElevenAnnualdiscount($param);
                 }else{
                     $annualpayment=0;
                 }
                 $actualroyalty=$royalty+$annualpayment;
                 $sql=" UPDATE vtiger_achievementsummary SET royalty=?,actualroyalty=?,annualdiscount=?,annualpayment=?,employeelevel=? WHERE achievementid=? ";
                 $adb->pquery($sql,array($royalty,$actualroyalty,$annualdiscount,$annualpayment,$param['gradename'],$param['achievementid']));
             }
        }
    }

    /**
     * 员工的新单提成核算
     * @param $param
     */
    public function calulateEmployeeNewCommission($param){
        global $adb;
        $annualdiscount=$param['realarriveachievement']*0.01;// 年度折扣

        //多年单续费暂扣1%
        $query="SELECT SUM(a.arriveachievement*1)/100 as royaltytwo  FROM `vtiger_achievementallot_statistic` as a  WHERE   a.receivedpaymentownid=?  AND  a.achievementmonth=?  AND  a.achievementtype='renew' AND more_years_renew=1  AND a.renewal_commission=10 LIMIT 1";
        $result=$adb->pquery($query,array($param['userid'],$param['calculation_year_month']));
        if($adb->num_rows($result)){
            $data=$adb->query_result_rowdata($result,0);
            $annualdiscount+=$data['royaltytwo'];
        }

        // 如果计算提成的月份是12月份 则计算年度发放 否则 年度发放为0
        /*if($param['calculation_month']==12){
            //$annualpayment=$annualdiscount+$this->getOneToElevenAnnualdiscount($param);
            //$annualdiscount=0;
        }else{
            $annualpayment=0;
        }*/
        $annualpayment=0;
        $royaltyRatio=0;//提成比例
        // 员工等级分别为什么的时候
        $currentEmployeeBaseAchievement=0;
        $currentEmployeeBaseSalary=0;
        if($param['usergrade']==1 || $param['usergrade']==2 || $param['usergrade']==18){
            $royaltyRatio=$this->emploeeRoyaltyRatio[1];
            $royalty=bcdiv(bcmul($param['realarriveachievement'],$royaltyRatio,6),100,6);
            $subWtidholdPrecenTwenty=$this->subWithholdTwentyAchievement(array('userid'=>$param['userid'],'royaltyratio'=>$royaltyRatio,'currentDate'=>$param['calculation_year_month']));
            $addWtidholdPrecenTwenty=$this->addWithholdPrecenTwentyAchievement(array('userid'=>$param['userid'],'currentDate'=>$param['calculation_year_month']));
            $sumActualRoyalty=bcadd(bcsub($royalty,$subWtidholdPrecenTwenty,6),$addWtidholdPrecenTwenty,6);
            echo '<br>无阶梯等级基数业绩',$param['realarriveachievement'],'<br> 用户userid',$param['userid'],'<hr>';
        }else if($param['usergrade']==3 || $param['usergrade']==4 || $param['usergrade']==5 || $param['usergrade']==6 || $param['usergrade']==7 || $param['usergrade']==8 || $param['usergrade']==13){
            $realarriveachievement=$this->getLastMonthPerformance($param);
            echo '<br>基数业绩',$realarriveachievement,'<br> 用户userid',$param['userid'],'<hr>';
            if(120000< $realarriveachievement ){
                $currentKey=7;
            }else if(80000<$realarriveachievement && $realarriveachievement<=120000){
                $currentKey=6;
            }else if(60000<$realarriveachievement && $realarriveachievement<=80000){
                $currentKey=5;
            }else if(50000<$realarriveachievement && $realarriveachievement<=60000){
                $currentKey=4;
            }else if(40000<$realarriveachievement && $realarriveachievement<=50000){
                $currentKey=3;
            }else if(22000<$realarriveachievement && $realarriveachievement<=40000){
                $currentKey=2;
            }else if(10000<$realarriveachievement && $realarriveachievement<=22000){
                $currentKey=1;
            }else if($realarriveachievement<=10000){
                $currentKey=0;
            }
            //echo '<hr><====>',$currentKey;
            $royaltyRatio=$this->emploeeRoyaltyRatio[$currentKey];

            //$currentEmployeeBaseAchievement=$this->employeeBaseAchievement[$currentKey];
            //$currentEmployeeBaseSalary=$this->employeeBaseSalary[$currentKey];
            //$sumRoyalty=bcdiv(bcmul(bcsub($param['realarriveachievement'],$currentEmployeeBaseAchievement,6),$royaltyRatio,6),100,4);
            //$royalty=bcadd($sumRoyalty,$currentEmployeeBaseSalary,6);//减1%的业绩提成总和(含底薪)
            $royalty=bcdiv(bcmul($param['realarriveachievement'],$royaltyRatio,6),100,4);
			$subWtidholdPrecenTwenty=$this->subWithholdTwentyAchievement(array('userid'=>$param['userid'],'royaltyratio'=>$royaltyRatio,'currentDate'=>$param['calculation_year_month']));
			$addWtidholdPrecenTwenty=$this->addWithholdPrecenTwentyAchievement(array('userid'=>$param['userid'],'currentDate'=>$param['calculation_year_month']));
			$sumActualRoyalty=bcadd(bcsub($royalty,$subWtidholdPrecenTwenty,6),$addWtidholdPrecenTwenty,6);

        }
        //$actualroyalty=$royalty+$annualpayment;
        $actualroyalty=$sumActualRoyalty+$annualpayment;
        $sql=" UPDATE vtiger_achievementsummary SET grantdetain=0,royalty=?,actualroyalty=?,annualdiscount=?,annualpayment=?,employeelevel=?,withholdroyaltyratio=?,deliverdetain=? WHERE achievementid=? ";
        $adb->pquery($sql,array($royalty,$actualroyalty,$annualdiscount,$annualpayment,$param['gradename'],$royaltyRatio,$subWtidholdPrecenTwenty,$param['achievementid']));
    }
    public function calulateEmployeeRenewCommission($param){
        global $adb;
        //seo系列、等需要长期维护的服务 的提成计算
        $sql=" SELECT SUM(a.arriveachievement*0.05) as royaltyone  FROM `vtiger_achievementallot_statistic` as a INNER JOIN vtiger_servicecontracts as s ON s.servicecontractsid=a.servicecontractid  WHERE   a.receivedpaymentownid=?  AND  a.achievementmonth=?  AND  a.achievementtype='renew' AND s.parent_contracttypeid IN(4,6) AND  s.contract_type IN('百度微整站优化续费服务合同','百度微整站优化服务合同','搜索引擎左侧优化续费协议书','KA搜索引擎优化','T-网营销销售合同','整合营销合同','网络口碑营销合同书')  LIMIT 1 ";

        $result=$adb->pquery($sql,array($param['userid'],$param['calculation_year_month']));
        $data=$adb->query_result_rowdata($result,0);
        $royaltyOne=$data['royaltyone'];
        //T- SITE系列、T云系列、微网站系列、空间服务器系列等一次性交付类产品  除了 seo系列、等需要长期维护的服务 的提成计算 和 域名空间类型续费，商务不核算提成；
        //$sql=" SELECT SUM(a.arriveachievement*0.06) as royaltytwo  FROM `vtiger_achievementallot_statistic` as a INNER JOIN vtiger_servicecontracts as s ON s.servicecontractsid=a.servicecontractid  WHERE   a.receivedpaymentownid=?  AND  a.achievementmonth=?  AND  a.achievementtype='renew'  AND  s.contract_no NOT LIKE ? AND (s.parent_contracttypeid NOT IN(4,6) || s.contract_type NOT IN('百度微整站优化续费服务合同','百度微整站优化服务合同','搜索引擎左侧优化续费协议书','KA搜索引擎优化','T-网营销销售合同','整合营销合同','网络口碑营销合同书'))  LIMIT 1 ";
        //$result=$adb->pquery($sql,array($param['userid'],$param['calculation_year_month'],'%TSITEXF%'));

        //$sql=" SELECT SUM(a.arriveachievement*6/pow(2,if(renewtimes>0,(renewtimes-1),0))/100) as royaltytwo  FROM `vtiger_achievementallot_statistic` as a INNER JOIN vtiger_servicecontracts as s ON s.servicecontractsid=a.servicecontractid  WHERE   a.receivedpaymentownid=?  AND  a.achievementmonth=?  AND  a.achievementtype='renew' AND (s.parent_contracttypeid NOT IN(4,6) || s.contract_type NOT IN('百度微整站优化续费服务合同','百度微整站优化服务合同','搜索引擎左侧优化续费协议书','KA搜索引擎优化','T-网营销销售合同','整合营销合同','网络口碑营销合同书'))  LIMIT 1 ";
        $sql=" SELECT SUM(a.arriveachievement*a.renewal_commission/100) as royaltytwo  FROM `vtiger_achievementallot_statistic` as a INNER JOIN vtiger_servicecontracts as s ON s.servicecontractsid=a.servicecontractid  WHERE   a.receivedpaymentownid=? AND a.renewal_commission!=10 AND a.renewal_commission!=9 AND  a.achievementmonth=?  AND  a.achievementtype='renew' AND (s.parent_contracttypeid NOT IN(4,6) || s.contract_type NOT IN('百度微整站优化续费服务合同','百度微整站优化服务合同','搜索引擎左侧优化续费协议书','KA搜索引擎优化','T-网营销销售合同','整合营销合同','网络口碑营销合同书'))  LIMIT 1 ";
        //echo $sql; echo '<hr>';
        $result=$adb->pquery($sql,array($param['userid'],$param['calculation_year_month']));
        $data=$adb->query_result_rowdata($result,0);
        $royaltyTwo=$data['royaltytwo'];
        $royalty=$royaltyOne+$royaltyTwo;
        //$annualdiscount=$param['realarriveachievement']*0.01;
        $annualdiscount=0;
        //如果计算提成的月份是12月份 则计算年度发放 否则 年度发放为0
        $annualpayment=0;
        /*if($param['calculation_month']==12){
            $annualpayment=$annualdiscount+$this->getOneToElevenAnnualdiscount($param);
        }else{
            $annualpayment=0;
        }*/
        //多年单暂扣1%
        $query="SELECT SUM(a.arriveachievement*9/100) as royaltytwo  FROM `vtiger_achievementallot_statistic` as a  WHERE   a.receivedpaymentownid=?  AND  a.achievementmonth=?  AND  a.achievementtype='renew' AND more_years_renew=1 AND a.renewal_commission in(9,10) LIMIT 1";
        $result=$adb->pquery($query,array($param['userid'],$param['calculation_year_month']));
        if($adb->num_rows($result)){
            $data=$adb->query_result_rowdata($result,0);
            $royalty+=$data['royaltytwo'];
        }

        $actualroyalty=$royalty+$annualpayment;
        $sql=" UPDATE vtiger_achievementsummary SET royalty=?,actualroyalty=?,annualdiscount=?,annualpayment=?,employeelevel=? WHERE achievementid=? ";
        $adb->pquery($sql,array($royalty,$actualroyalty,$annualdiscount,$annualpayment,$param['gradename'],$param['achievementid']));
    }


    /**
     * 经理的新单提成
     * @param $param
     * @throws Exception
     */
    public function calulateManagerNewCommission($param){
        global $adb;
        $subordinate_users=$this->subordinateUsers;
        $userIds=$subordinate_users[$param['userid']];// 该经理的所有下属包含所有的经理
        $param['userids']=explode(",",$param['userids']);
        $userIds=array_diff($userIds,$param['userids']);// 去掉该下属中包含的经理。
        $adb->pquery("DELETE FROM vtiger_achievementsummary WHERE achievementmonth=? AND userid=?",array($param['calculation_year_month'],$param['userid']));
        if(empty($userIds)){
            return ;
        }
        $proportionofyears=$this->proportionOfSingleYear($userIds,$param['calculation_year_month']);
        $regionalLevel=$this->getRegionalLevel($param['userid']);//获取等级
        $areaManagerRoyaltyRatio=$this->areaManagerRoyaltyRatio[$regionalLevel];
        $managerBaseSalary=$areaManagerRoyaltyRatio['managerBaseSalary'];
        $baseAmountRatio=$areaManagerRoyaltyRatio['baseAmountRatio'];
        $userIdsArray=$userIds;
        $userIds=" userid IN (".implode(",",$userIds).")";
        //汇总该经理所有下属员工的到账业绩  回款金额
        $sql=" SELECT SUM(realarriveachievement) as realarriveachievement,SUM(arriveachievement) as arriveachievement,SUM(unit_price) as unit_price,SUM(effectiverefund) as effectiverefund FROM `vtiger_achievementsummary` WHERE ".$userIds." AND achievementmonth=? AND achievementtype='newadd' LIMIT 1 ";
        $result=$adb->pquery($sql,array($param['calculation_year_month']));
        echo $sql,$param['calculation_year_month'];
        echo '<hr>';
        $data=$adb->query_result_rowdata($result,0);
        $money=0;
        if($proportionofyears){
            $money=$this->getAllNewaddArriveachievement($userIdsArray,$param['calculation_year_month'],'newadd');//多年单的第一年业绩
            $data['realarriveachievement']=$data['realarriveachievement']-$money;//减掉多年单的第一年业绩
            //$money+=$this->getAllNewaddArriveachievement($userIdsArray,$param['calculation_year_month'],'renew');//多年单的续费业绩
        }
        echo '下属的业绩之和',$data['realarriveachievement'];
        echo '<hr>';
        // 六个月以上的老经理    见习经理/经理：H18 < 80000;高级经理：H18 < 160000； 提成 = 0；
        $currentKey=0;
        if(($data['realarriveachievement']<80000*$baseAmountRatio && $param['newmanagersixmonths']!=1 && ($param['usergrade']==9 || $param['usergrade']==10)) || ($data['realarriveachievement']<160000*$baseAmountRatio && $param['newmanagersixmonths']!=1 && $param['usergrade']==11)){
            $royalty=0;
            // 除了上面特殊的 下面为正常计算流程
        }else{
            if (300000*$baseAmountRatio < $data['realarriveachievement']) {
                if ($param['incumbencynumber'] > 12) {
                    $currentKey = 4;
                } else {
                    $currentKey = 6;

                }
            } else if (250000*$baseAmountRatio <= $data['realarriveachievement'] && $data['realarriveachievement'] <= 300000*$baseAmountRatio) {
                if ($param['incumbencynumber'] > 12) {
                    $currentKey = 4;
                } else {
                    $currentKey = 5;
                }
            } else if (200000*$baseAmountRatio < $data['realarriveachievement'] && $data['realarriveachievement'] < 250000*$baseAmountRatio) {
                $currentKey = 4;
            } else if (180000*$baseAmountRatio < $data['realarriveachievement'] && $data['realarriveachievement'] <= 200000*$baseAmountRatio) {
                $currentKey = 3;
            } else if (140000*$baseAmountRatio < $data['realarriveachievement'] && $data['realarriveachievement'] <= 180000*$baseAmountRatio) {
                $currentKey = 2;
            } else if (120000*$baseAmountRatio < $data['realarriveachievement'] && $data['realarriveachievement'] <= 140000*$baseAmountRatio) {
                $currentKey = 1;
            } else if (0 < $data['realarriveachievement'] && $data['realarriveachievement'] <= 120000*$baseAmountRatio) {
                $currentKey = 0;
            }
            $royaltyRatio = $this->managerRoyaltyRatio[$currentKey];
            $currentEmployeeBaseAchievement = $this->managerBaseAchievement[$currentKey]*$baseAmountRatio;
            //$currentEmployeeBaseSalary = $this->managerBaseSalary[$currentKey];
            $currentEmployeeBaseSalary = $managerBaseSalary[$currentKey];
            $sumRoyalty = bcdiv(bcmul(bcsub($data['realarriveachievement'], $currentEmployeeBaseAchievement, 6), $royaltyRatio, 6), 1000, 6);
            $royalty = bcadd($sumRoyalty, $currentEmployeeBaseSalary, 6);//减1%的业绩提成总和(含底薪)
        }
        $sql=" SELECT vtiger_users.invoicecompany,vtiger_user2department.departmentid FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id  WHERE vtiger_users.id=? ";
        $userInfo=$adb->pquery($sql,array($param['userid']));
        $userInfo=$adb->query_result_rowdata($userInfo,0);

        $annualdiscount=$royalty>0?bcdiv(bcmul($data['realarriveachievement'],2,6),1000,6):0;// 年度折扣
        // 如果计算提成的月份是12月份 则计算年度发放 否则 年度发放为0
        if($param['calculation_month']==12){
            $annualpayment=$annualdiscount+$this->getOneToElevenAnnualdiscount($param);
        }else{
            $annualpayment=0;
        }

        $params['invoicecompany']=$userInfo['invoicecompany'];
        $params['departmentid']=$userInfo['departmentid'];
        $params['userid']=$param['userid'];
        $params['unit_price']=$data['unit_price'];
        $params['arriveachievement']=$data['arriveachievement'];
        $params['adjustachievement']=0;
        $params['realarriveachievement']=$data['realarriveachievement'];
        $params['annualdiscount']=$annualdiscount;// 年度折扣
        $params['annualpayment']=$annualpayment;// 年度发放
        $params['royalty']=$royalty;// 提成
        $params['actualroyalty']=$royalty+$annualpayment;// 实际提成=提成+年度发放
        $params['effectiverefund']=$data['effectiverefund'];
        $params['achievementmonth']=$param['calculation_year_month'];
        $params['confirmstatus']='tobeconfirm';
        $params['modulestatus']='a_normal';
        $params['proportionofyears']=$proportionofyears;
        $params['myearachievement']=$money;
        $id=$adb->getUniqueID('vtiger_achievementsummary');
        $sql="INSERT INTO `vtiger_achievementsummary` (achievementid,`invoicecompany`, `departmentid`, `userid`, `unit_price`, `arriveachievement`, `realarriveachievement`, `effectiverefund`, `achievementmonth`, `createtime`, `adjustachievement`, `employeelevel`, `royalty`, `actualroyalty`, `achievementtype`, `confirmstatus`, `modulestatus`,`annualdiscount`, `annualpayment`, `proportionofyears`,myearachievement) 
                       VALUES (".$id.",?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        echo $sql,'<hr>';
        print_r(array($params['invoicecompany'],$params['departmentid'],$params['userid'],$params['unit_price'],$params['arriveachievement'],$params['realarriveachievement'],$params['effectiverefund'],$params['achievementmonth'],date("Y-m-d H:i:s"),0,$param['gradename'],$params['royalty'],$params['actualroyalty'],$param['achievementtype'],$params['confirmstatus'],$params['modulestatus'],$params['annualdiscount'],$params['annualpayment'],$params['proportionofyears'],$params['myearachievement']));
        echo '<hr>';
        $adb->pquery($sql,array($params['invoicecompany'],$params['departmentid'],$params['userid'],$params['unit_price'],$params['arriveachievement'],$params['realarriveachievement'],$params['effectiverefund'],$params['achievementmonth'],date("Y-m-d H:i:s"),0,$param['gradename'],$params['royalty'],$params['actualroyalty'],$param['achievementtype'],$params['confirmstatus'],$params['modulestatus'],$params['annualdiscount'],$params['annualpayment'],$params['proportionofyears'],$params['myearachievement']));
        //经理的个人新单业绩

        $query="SELECT sum(a.arriveachievement) AS arriveachievement,sum(if(a.achievementtype = 'renew' AND a.more_years_renew = 1,0,a.unit_price)) AS unit_price,sum(if(a.achievementtype = 'renew' AND a.more_years_renew = 1,0,a.effectiverefund)) AS effectiverefund FROM `vtiger_achievementallot_statistic` AS a WHERE a.receivedpaymentownid =? AND a.achievementmonth =? AND ((a.achievementtype = 'renew' AND a.more_years_renew = 1) OR a.achievementtype = 'newadd') LIMIT 1";
        $result=$adb->pquery($query,array($params['userid'],$param['calculation_year_month']));
        if($adb->num_rows($result)==0){
            return ;
        }
        $params['unit_price']=$result->fields['unit_price'];
        $params['arriveachievement']==$result->fields['arriveachievement'];
        $params['adjustachievement']=0;
        $params['realarriveachievement']==$result->fields['realarriveachievement'];
        $params['annualdiscount']=0;// 年度折扣
        $params['annualpayment']=0;// 年度发放
        $params['royalty']=$result->fields['realarriveachievement']*0.1;// 提成
        $params['actualroyalty']=$result->fields['realarriveachievement']*0.1;
        $params['effectiverefund']==$result->fields['effectiverefund'];
        $params['achievementmonth']=$param['calculation_year_month'];
        $params['confirmstatus']='tobeconfirm';
        $params['modulestatus']='a_normal';
        $params['proportionofyears']=0;
        $params['myearachievement']=0;
        $id=$adb->getUniqueID('vtiger_achievementsummary');
        $sql="INSERT INTO `vtiger_achievementsummary` (achievementid,`invoicecompany`, `departmentid`, `userid`, `unit_price`, `arriveachievement`, `realarriveachievement`, `effectiverefund`, `achievementmonth`, `createtime`, `adjustachievement`, `employeelevel`, `royalty`, `actualroyalty`, `achievementtype`, `confirmstatus`, `modulestatus`,`annualdiscount`, `annualpayment`, `proportionofyears`,myearachievement,remarks) 
                       VALUES (".$id.",?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $adb->pquery($sql,array($params['invoicecompany'],$params['departmentid'],$params['userid'],$params['unit_price'],$params['arriveachievement'],$params['realarriveachievement'],$params['effectiverefund'],$params['achievementmonth'],date("Y-m-d H:i:s"),0,$param['gradename'],$params['royalty'],$params['actualroyalty'],$param['achievementtype'],$params['confirmstatus'],$params['modulestatus'],$params['annualdiscount'],$params['annualpayment'],$params['proportionofyears'],$params['myearachievement'],'经理个人业绩'));
    }
    public function calulateManagerReNewCommission($param){
        global $adb;
        //查出部门所有用户userid
        $subordinate_users=$this->subordinateUsers;
        $userIds=$subordinate_users[$param['userid']];// 该经理的所有下属包含所有的经理
        $param['userids']=explode(",",$param['userids']);
        $userIds=array_diff($userIds,$param['userids']);// 去掉该下属中包含的经理。
        if(empty($userIds)){
            return ;
        }
        $proportionofyears=$this->proportionOfSingleYear($userIds,$param['calculation_year_month']);
        $userIdstring=" userid IN (".implode(",",$userIds).")";
        //汇总该经理所有下属员工的到账业绩  回款金额
        $sql=" SELECT SUM(realarriveachievement) as realarriveachievement,SUM(arriveachievement) as arriveachievement,SUM(unit_price) as unit_price,SUM(effectiverefund) as effectiverefund FROM `vtiger_achievementsummary` WHERE ".$userIdstring." AND achievementmonth=? AND achievementtype='renew' LIMIT 1 ";
        $result=$adb->pquery($sql,array($param['calculation_year_month']));
        $data=$adb->query_result_rowdata($result,0);

        $money=0;
        if($proportionofyears){
            $money=$this->getAllNewaddArriveachievement($userIds,$param['calculation_year_month'],'renew');
            $data['realarriveachievement']=$data['realarriveachievement']-$money;//多年单续费业绩已核算到新单里了
        }
        //查询他手下的所有实际到账业绩
        $royalty=bcdiv($data['realarriveachievement'],100,6);
        $sql=" SELECT vtiger_users.invoicecompany,vtiger_user2department.departmentid FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id  WHERE vtiger_users.id=? ";
        $userInfo=$adb->pquery($sql,array($param['userid']));
        $userInfo=$adb->query_result_rowdata($userInfo,0);
        //$annualdiscount=$data['realarriveachievement']*0.002;// 年度折扣
        //如果计算提成的月份是12月份 则计算年度发放 否则 年度发放为0
        /*if($param['calculation_month']==12){
            $annualpayment=$annualdiscount+$this->getOneToElevenAnnualdiscount($param);
        }else{
            $annualpayment=0;
        }*/
        $annualpayment=0;
        $annualdiscount=0;
        $params['invoicecompany']=$userInfo['invoicecompany'];
        $params['departmentid']=$userInfo['departmentid'];
        $params['userid']=$param['userid'];
        $params['unit_price']=$data['unit_price'];
        $params['arriveachievement']=$data['arriveachievement'];
        $params['adjustachievement']=0;
        $params['realarriveachievement']=$data['realarriveachievement'];
        $params['annualdiscount']=$annualdiscount;// 年度折扣
        $params['annualpayment']=$annualpayment;// 年度发放
        $params['royalty']=$royalty;// 提成
        $params['actualroyalty']=$royalty+$annualpayment;// 实际提成=提成+年度发放+个人的
        $params['effectiverefund']=$data['effectiverefund'];
        $params['achievementmonth']=$param['calculation_year_month'];
        $params['confirmstatus']='tobeconfirm';
        $params['modulestatus']='a_normal';
        $params['proportionofyears']=$proportionofyears;
        $params['myearachievement']=$money;
        $id=$adb->getUniqueID('vtiger_achievementsummary');
        $sql="INSERT INTO `vtiger_achievementsummary` (achievementid,`invoicecompany`, `departmentid`, `userid`, `unit_price`, `arriveachievement`, `realarriveachievement`, `effectiverefund`, `achievementmonth`, `createtime`, `adjustachievement`, `employeelevel`, `royalty`, `actualroyalty`, `achievementtype`, `confirmstatus`, `modulestatus`,`annualdiscount`, `annualpayment`,`proportionofyears`,myearachievement) 
                       VALUES ($id,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $adb->pquery($sql,array($params['invoicecompany'],$params['departmentid'],$params['userid'],$params['unit_price'],$params['arriveachievement'],$params['realarriveachievement'],$params['effectiverefund'],$params['achievementmonth'],date("Y-m-d H:i:s"),0,$param['gradename'],$params['royalty'],$params['actualroyalty'],$param['achievementtype'],$params['confirmstatus'],$params['modulestatus'],$params['annualdiscount'],$params['annualpayment'],$params['proportionofyears'],$params['myearachievement']));

    }
    // 获取下属多年单到账业绩
    private function getAllNewaddArriveachievement($userIds,$achievementmonth,$achievementType){
        global $adb;
        $query="SELECT SUM(arriveachievement) as money FROM vtiger_achievementallot_statistic WHERE achievementmonth=? and  more_years_renew=1  AND achievementtype=? and receivedpaymentownid IN (".implode(",",$userIds)." ) ";
        $result=$adb->pquery($query,array($achievementmonth,$achievementType));
        $result=$adb->query_result_rowdata($result,0);
        return $result['money'];
    }
    /**
     *  多年单占比是否大于30%
     */
    private function  proportionOfSingleYear($userIds,$achievementmonth){
        global $adb;
        $result=$adb->pquery(" SELECT count(1) FROM vtiger_achievementallot_statistic WHERE achievementmonth=? and  more_years_renew=1  and receivedpaymentownid IN (".implode(",",$userIds)." )  GROUP BY servicecontractid ",array($achievementmonth));
        $proportionOfSingleYearNumber=$adb->num_rows($result);
        $result=$adb->pquery(" SELECT count(1) FROM vtiger_achievementallot_statistic WHERE achievementmonth=?  and receivedpaymentownid IN (".implode(",",$userIds)." )  GROUP BY servicecontractid ",array($achievementmonth));
        $allNumber=$adb->num_rows($result);
        if($allNumber>0){
            if($proportionOfSingleYearNumber/$allNumber>0.3){
                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    /**
     * 当月无业绩但(有20%的暂扣金额,或12月份要算当年的全年1%商务,2‰经理每月的暂扣金额)
     * @param $params
     */
    public function noAchievementRoyalty($params){
        global $adb;
        $managerInfo=$adb->pquery("SELECT * FROM vtiger_usergraderoyalty WHERE usergrade>0",array());
        $usergraderoyalty=array();
        $usergraderoyaltyinfo=array();
        $usermangergraderoyalty=array();
        while($row=$adb->fetch_array($managerInfo)){
            $usergraderoyaltyinfo[$row['userid']]=$row;
            $usergraderoyalty[]=$row['userid'];//总人数
            if($row['staffrank']){
                $usermangergraderoyalty[]=$row['userid'];//经理人数
            }
        }
        $query="SELECT userid FROM vtiger_achievementsummary WHERE  achievementtype='newadd' AND userid in(".implode(',',$usergraderoyalty).") AND achievementmonth=?";
        $result=$adb->pquery($query,array($params['calculation_year_month']));
        $userAchievementSummary=array();//有新单业绩人数
        if($adb->num_rows($result)){
            while($row=$adb->fetch_array($result)){
                $userAchievementSummary[]=$row['userid'];
            }
        }
        $withholdTwentyusers=array();//存在20%暂扣的人员
        $userinfoQuery=" SELECT vtiger_users.invoicecompany,vtiger_user2department.departmentid FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id  WHERE vtiger_users.id=? ";
        $argv=array('currentDate'=>$params['calculation_year_month']);
        //$sql="UPDATE vtiger_achievementsummary SET deliverdetain=0,grantdetain=?,actualroyalty=actualroyalty+? WHERE userid=? AND achievementtype='newadd' AND achievementmonth=?";
        foreach($usergraderoyalty as $value){
            $argv['userid']=$value;
            $withholdTwentyAchievement=$this->addWithholdPrecenTwentyAchievement($argv);
            if($withholdTwentyAchievement>0){
                if(in_array($value,$userAchievementSummary)){
                    //$adb->pquery($sql,array($withholdTwentyAchievement,$withholdTwentyAchievement,$value,$params['calculation_year_month']));
                }else {
                    $withholdTwentyusers[] = $value;
                    $userInfo = $adb->pquery($userinfoQuery, array($value));
                    $thisArray=array(
                        'achievementid'=>$adb->getUniqueID('vtiger_achievementsummary'),
                        'invoicecompany'=>$userInfo->fields['invoicecompany'],
                        'departmentid'=>$userInfo->fields['departmentid'],
                        'userid'=>$value,
                        'unit_price'=>0,
                        'arriveachievement'=>0,
                        'realarriveachievement'=>0,
                        'effectiverefund'=>0,
                        'achievementmonth'=>$params['calculation_year_month'],
                        'createtime'=>date("Y-m-d H:i:s"),
                        'adjustachievement'=>0,
                        'employeelevel'=>$usergraderoyaltyinfo[$value]['gradename'],
                        'royalty'=>$withholdTwentyAchievement,
                        'actualroyalty'=>$withholdTwentyAchievement,
                        'deliverdetain'=>0,
                        'grantdetain'=>$withholdTwentyAchievement,
                        'achievementtype'=>'newadd',
                        'confirmstatus'=>'tobeconfirm',
                        'modulestatus'=>'a_normal',
                        'annualdiscount'=>0,
                        'annualpayment'=>0,
                        'bonus'=>0,
                        'quarterlyaward'=>0,
                        'halfyearlyaward'=>0,
                    );
                    $valuePlaceholder=array_map(function($v){return '?';},$thisArray);
                    $SQl='INSERT INTO `vtiger_achievementsummary` ('.implode(',',array_keys($thisArray)).') values ('.implode(',',$valuePlaceholder).')';
                    $adb->pquery($SQl,$thisArray);
                }
            }
        }
        $this->calculateAnnualpayment($params,$usergraderoyalty,$usergraderoyaltyinfo);//计算年度暂扣
        $this->calculateBonus($params);//计算季度奖,半年度奖
    }

    /**
     * 年度每月暂扣发放
     * @param $params
     */
    public function calculateAnnualpayment($params,$usergraderoyalty,$usergraderoyaltyinfo){
        global $adb;
        if($params['calculation_month']==12){
            $query="SELECT distinct userid FROM vtiger_achievementsummary WHERE  achievementtype='newadd' AND achievementmonth=?";
            $result=$adb->pquery($query,array($params['calculation_year_month']));
            $userAchievementSummary=array();//有新单业绩人数
            if($adb->num_rows($result)){
                while($row=$adb->fetch_array($result)){
                    $userAchievementSummary[]=$row['userid'];
                }
            }
            $argv=array('achievementtype'=>'newadd','calculation_year'=>$params['calculation_year']);
            $sql=" SELECT vtiger_users.invoicecompany,vtiger_user2department.departmentid FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id  WHERE vtiger_users.id=? ";
            foreach($usergraderoyalty as $value){
                $argv['userid']=$value;
                $toElevenAnnualdiscount=$this->getOneToElevenAnnualdiscount($argv);//计算年度暂扣1%或2/千发放
                if($toElevenAnnualdiscount>0){
                    if(in_array($value,$userAchievementSummary)){
                        //update
                        $query='UPDATE vtiger_achievementsummary SET annualpayment='.$toElevenAnnualdiscount.',actualroyalty=actualroyalty+'.$toElevenAnnualdiscount.' WHERE userid=? AND achievementmonth=? AND achievementtype=\'newadd\'';
                        $adb->pquery($query,array($value,$params['calculation_year_month']));
                    }else{
                        $userInfo=$adb->pquery($sql,array($value));
                        //insert
                        $thisArray=array(
                            'achievementid'=>$adb->getUniqueID('vtiger_achievementsummary'),
                            'invoicecompany'=>$userInfo->fields['invoicecompany'],
                            'departmentid'=>$userInfo->fields['departmentid'],
                            'userid'=>$value,
                            'unit_price'=>0,
                            'arriveachievement'=>0,
                            'realarriveachievement'=>0,
                            'effectiverefund'=>0,
                            'achievementmonth'=>$params['calculation_year_month'],
                            'createtime'=>date("Y-m-d H:i:s"),
                            'adjustachievement'=>0,
                            'employeelevel'=>$usergraderoyaltyinfo[$value]['gradename'],
                            'royalty'=>0,
                            'actualroyalty'=>$toElevenAnnualdiscount,
                            'achievementtype'=>'newadd',
                            'confirmstatus'=>'tobeconfirm',
                            'modulestatus'=>'a_normal',
                            'deliverdetain'=>0,
                            'grantdetain'=>0,
                            'annualdiscount'=>0,
                            'annualpayment'=>$toElevenAnnualdiscount,
                            'bonus'=>0,
                            'quarterlyaward'=>0,
                            'halfyearlyaward'=>0,
                        );
                        $valuePlaceholder=array_map(function($v){return '?';},$thisArray);
                        $SQl='INSERT INTO `vtiger_achievementsummary` ('.implode(',',array_keys($thisArray)).') values ('.implode(',',$valuePlaceholder).')';
                        $adb->pquery($SQl,$thisArray);
                    }
                }
            }
        }
    }

    /**
     * 半年度奖,季度奖
     * @param $params
     */
    public function calculateBonus($params){
        global $adb;
        $currentMonth=(int)$params['calculation_month'];

        if(in_array($currentMonth,array(3,6,9,12))){
            $managerInfo=$adb->pquery("SELECT * FROM vtiger_usergraderoyalty WHERE usergrade>0 and staffrank=1",array());
            $usergraderoyaltyinfo=array();
            $usermangergraderoyalty=array();
            while($row=$adb->fetch_array($managerInfo)){
                $usergraderoyaltyinfo[$row['userid']]=$row;
                $usermangergraderoyalty[]=$row['userid'];//经理人数
            }
            $temparray=array(3=>array(1,2),6=>array(1,2,3,4,5),9=>array(1,2),12=>array(1,2,3,4,5));
            $tarray=$temparray[$currentMonth];
            global $currentDay;
            $currentDay=$params['calculation_year_month'];
            $arraymapData1=array_map(function($v){ global $currentDay; return date('Y-m',strtotime($currentDay.'-01 -'.$v.' month'));},array(1,2));//季度
            $arraymapData2=array_map(function($v){ global $currentDay; return date('Y-m',strtotime($currentDay.'-01 -'.$v.' month'));},$tarray);//半年度
            //季度
            $quarterQuery="SELECT sum(realarriveachievement) as sumrealarriveachievement,sum(actualroyalty) as sumactualroyalty FROM vtiger_achievementsummary WHERE achievementtype='newadd' AND userid=?  AND achievementmonth in('".implode("','",$arraymapData1)."','".$currentDay."')";
            //半年度
            $halfYearQuery="SELECT sum(realarriveachievement) as sumrealarriveachievement,sum(actualroyalty) as sumactualroyalty FROM vtiger_achievementsummary WHERE achievementtype='newadd' AND userid=? AND achievementmonth in('".implode("','",$arraymapData2)."','".$currentDay."')";//年度
            //当月
            $monthlyQuery="SELECT sum(realarriveachievement) as sumrealarriveachievement,sum(actualroyalty) as sumactualroyalty FROM vtiger_achievementsummary WHERE achievementtype='newadd' AND userid=? AND achievementmonth='".$currentDay."'";//月度金额

            $achievementsummaryQuery="SELECT * FROM vtiger_achievementsummary WHERE achievementtype='newadd' AND userid=? AND achievementmonth='".$currentDay."'";
            $achievementsummarySQL="UPDATE vtiger_achievementsummary SET bonus=?,quarterlyaward=?,halfyearlyaward=? WHERE achievementid=?";
            foreach($usermangergraderoyalty as $value){
                $quarterResult=$adb->pquery($quarterQuery,array($value));
                $quarterRealarriveachievement=$quarterResult->fields['sumrealarriveachievement'];//季度总业绩
                $thisResult=$adb->pquery($achievementsummaryQuery,array($value));
                $numflag=false;
                if($adb->num_rows($thisResult)){
                    $numflag=true;
                }
                if(in_array($currentMonth,array(6,12))){//半年度
                    if($quarterRealarriveachievement<750000){//最后一个季度总业绩小于75万
                        $halfYearResult=$adb->pquery($halfYearQuery,array($value));
                        $halfYearRealarriveachievement=$halfYearResult->fields['sumrealarriveachievement'];//半年度总业绩
                        if($halfYearRealarriveachievement>1500000){//算半年度
                            $halfYearsumactualroyalty=$halfYearResult->fields['sumactualroyalty'];//半年度总提成
                            $halfYearroyalty=bcsub(bcdiv(bcmul($halfYearRealarriveachievement,48,6),1000,6),$halfYearsumactualroyalty,6);
                            if($numflag){
                                $adb->pquery($achievementsummarySQL,array($halfYearroyalty,0,$halfYearroyalty,$thisResult->fields['achievementid']));
                            }else{
                                $sql=" SELECT vtiger_users.invoicecompany,vtiger_user2department.departmentid FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id  WHERE vtiger_users.id=? ";
                                $userInfo=$adb->pquery($sql,array($value));
                                $thisArray=array(
                                    'achievementid'=>$adb->getUniqueID('vtiger_achievementsummary'),
                                    'invoicecompany'=>$userInfo->fields['invoicecompany'],
                                    'departmentid'=>$userInfo->fields['departmentid'],
                                    'userid'=>$value,
                                    'unit_price'=>0,
                                    'arriveachievement'=>0,
                                    'realarriveachievement'=>0,
                                    'effectiverefund'=>0,
                                    'achievementmonth'=>$params['calculation_year_month'],
                                    'createtime'=>date("Y-m-d H:i:s"),
                                    'adjustachievement'=>0,
                                    'employeelevel'=>$usergraderoyaltyinfo[$value]['gradename'],
                                    'royalty'=>0,
                                    'actualroyalty'=>0,
                                    'achievementtype'=>'newadd',
                                    'confirmstatus'=>'tobeconfirm',
                                    'modulestatus'=>'a_normal',
                                    'annualdiscount'=>0,
                                    'annualpayment'=>0,
                                    'deliverdetain'=>0,
                                    'grantdetain'=>0,
                                    'bonus'=>$halfYearroyalty,
                                    'quarterlyaward'=>0,
                                    'halfyearlyaward'=>$halfYearroyalty,
                                );
                                $valuePlaceholder=array_map(function($v){return '?';},$thisArray);
                                $SQl='INSERT INTO `vtiger_achievementsummary` ('.implode(',',array_keys($thisArray)).') values ('.implode(',',$valuePlaceholder).')';
                                $adb->pquery($SQl,$thisArray);
                            }
                            continue;
                        }
                    }
                }
                $monthlyResult=$adb->pquery($monthlyQuery,array($value));
                if($monthlyResult->fields['sumrealarriveachievement']<250000){
                    if($quarterRealarriveachievement>=750000){//算季度
                        $quarterroyalty=bcsub(bcdiv(bcmul($quarterRealarriveachievement,48,6),1000,6),$quarterResult->fields['sumactualroyalty'],6);
                        if($numflag){
                            $adb->pquery($achievementsummarySQL,array($quarterroyalty,$quarterroyalty,0,$thisResult->fields['achievementid']));
                        }else{
                            $sql=" SELECT vtiger_users.invoicecompany,vtiger_user2department.departmentid FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id  WHERE vtiger_users.id=? ";
                            $userInfo=$adb->pquery($sql,array($value));
                            $thisArray=array(
                                'achievementid'=>$adb->getUniqueID('vtiger_achievementsummary'),
                                'invoicecompany'=>$userInfo->fields['invoicecompany'],
                                'departmentid'=>$userInfo->fields['departmentid'],
                                'userid'=>$value,
                                'unit_price'=>0,
                                'arriveachievement'=>0,
                                'realarriveachievement'=>0,
                                'effectiverefund'=>0,
                                'achievementmonth'=>$params['calculation_year_month'],
                                'createtime'=>date("Y-m-d H:i:s"),
                                'adjustachievement'=>0,
                                'employeelevel'=>$usergraderoyaltyinfo[$value]['gradename'],
                                'royalty'=>0,
                                'actualroyalty'=>0,
                                'achievementtype'=>'newadd',
                                'confirmstatus'=>'tobeconfirm',
                                'modulestatus'=>'a_normal',
                                'annualdiscount'=>0,
                                'annualpayment'=>0,
                                'bonus'=>$quarterroyalty,
                                'deliverdetain'=>0,
                                'grantdetain'=>0,
                                'quarterlyaward'=>$quarterroyalty,
                                'halfyearlyaward'=>0,
                                );
                            $valuePlaceholder=array_map(function($v){return '?';},$thisArray);
                            $SQl='INSERT INTO `vtiger_achievementsummary` ('.implode(',',array_keys($thisArray)).') values ('.implode(',',$valuePlaceholder).')';
                            $adb->pquery($SQl,$thisArray);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $param
     * 获取1-11月的折扣和
     */
    public function getOneToElevenAnnualdiscount($param){
        global $adb;
        $sql=" SELECT SUM(annualdiscount) as oneToEleven FROM `vtiger_achievementsummary` WHERE  userid=?  AND achievementtype=?  AND  left(achievementmonth,4)=?   LIMIT 1 ";
        $annualdiscountInfo=$adb->pquery($sql,array($param['userid'],$param['achievementtype'],$param['calculation_year']));
        if(0==$adb->num_rows($annualdiscountInfo)){
            return 0;
        }
        return $annualdiscountInfo->fields['oneToEleven'];//年度发放= 12月份年度折扣+该年1-11月份年度折扣和
    }
    public function setSubordinateUsers(){
        include('crmcache/subordinateusers.php');
        $this->subordinateUsers=$subordinate_users;// 该经理的所有下属包含所有的经理
    }

    /**
     * 需要扣除的暂扣20%的业绩提成金额
     * @param $params
     * @return int
     */
    public function subWithholdTwentyAchievement($params){
        global $adb;
        $wtidholdPrecenTwenty=0;
        $query="SELECT * FROM vtiger_achievementallot_statistic WHERE vtiger_achievementallot_statistic.receivedpaymentownid=? AND vtiger_achievementallot_statistic.achievementtype='newadd' AND istwentyroyalty=0 AND vtiger_achievementallot_statistic.achievementmonth=?";
        $result = $adb->pquery($query,array($params['userid'],$params['currentDate']));
        if(!$adb->num_rows($result)){
            return 0;
        }
        $query='SELECT * FROM vtiger_servicecontracts WHERE servicecontractsid=? limit 1';
        $twentyroyaltysql='UPDATE vtiger_achievementallot_statistic SET istwentyroyalty=?,twentyroyalty=? WHERE achievementallotid=?';
        $withholdroyaltysql='INSERT INTO `vtiger_withholdroyalty`(`userid`,`achievementallotid`,`amountofmoney`,`confirmationdate`, `createdtime`, `iscalculate`) VALUES (?, ?, ?, ?, ?, ?)';
        while($row=$adb->fetch_array($result)){
            $serviceResult=$adb->pquery($query,array($row['servicecontractid']));
            $twentyroyalty=bcdiv(bcmul(bcmul($row['arriveachievement'],$params['royaltyratio'],6),20,6),10000,6);
            $istwentyroyalty=0;
            //if($serviceResult->fields['contract_type']=='T云WEB版'){
            if($this->checkContactClass($serviceResult)){
                $wtidholdPrecenTwenty=bcadd($wtidholdPrecenTwenty,$twentyroyalty,6);
                if($serviceResult->fields['isfulldelivery']==1){
                    $adb->pquery($withholdroyaltysql,array($params['userid'],$row['achievementallotid'],$twentyroyalty,date('Y-m-d',strtotime('+1 month',strtotime($params['currentDate'].'-01'))),date('Y-m-d H:i:s'),0));
                    $istwentyroyalty=1;
                }
            }else{
                $istwentyroyalty=1;
            }
            $adb->pquery($twentyroyaltysql,array($istwentyroyalty,$twentyroyalty,$row['achievementallotid']));
        }
        return $wtidholdPrecenTwenty;
    }

    /**
     * 客服确认产品交付20%的业绩核算
     */
    public function customerServiceConfirmDelivery($servicecontractid){
        global $adb;
        $query="SELECT * FROM vtiger_achievementallot_statistic WHERE servicecontractid=?";
        $result = $adb->pquery($query,array($servicecontractid));
        if(!$adb->num_rows($result)){
            return 0;
        }
        $query='SELECT * FROM vtiger_servicecontracts WHERE servicecontractsid=?';
        $serviceResult=$adb->pquery($query,array($servicecontractid));
        $achievementOwner=$this->getAchievementOwner($serviceResult->fields['sc_related_to']);
        if($this->checkContactClass($serviceResult)){
            $achievementOwnerInfo=$this->achievementOwnerInfo;
            while($row=$adb->fetch_array($result)){
                if($row['istwentyroyalty']==1){
                    continue;
                }
                if(empty($achievementOwnerInfo[$row['receivedpaymentownid']])){
                    $temp=$this->geAachievementOwnerInfo($row['receivedpaymentownid']);
                    $achievementOwnerInfo[$row['receivedpaymentownid']]=$temp;
                    $this->achievementOwnerInfo[$row['receivedpaymentownid']]=$temp;
                }
                if($achievementOwnerInfo[$row['receivedpaymentownid']]['isdimission']==1){//当前业绩所属人离职
                    if($achievementOwner['id']==0 || $achievementOwner['isdimission']==1){//(客户被删或合并)或负责人也离职了
                        $userid=$row['receivedpaymentownid'];
                    }else{
                        $userid=$achievementOwner['id'];//负责人没有离职
                    }
                }else{
                    $userid=$row['receivedpaymentownid'];
                }
                $withholdroyaltysql='INSERT INTO vtiger_withholdroyalty(`userid`,`achievementallotid`,`amountofmoney`,`confirmationdate`,`createdtime`) 
                                        SELECT ?,`achievementallotid`,`twentyroyalty`,?,? FROM vtiger_achievementallot_statistic WHERE achievementallotid=?';
                $adb->pquery($withholdroyaltysql,array($userid,date('Y-m-d'),date('Y-m-d H:i:s'),$row['achievementallotid']));
            }
        }
        $twentyroyaltysql='UPDATE vtiger_achievementallot_statistic SET istwentyroyalty=1 WHERE servicecontractid=?';
        $adb->pquery($twentyroyaltysql,array($servicecontractid));

    }
    public function getAchievementOwner($accountid){
        global $adb;
        $query='SELECT * FROM vtiger_crmentity LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid WHERE vtiger_crmentity.deleted=0 AND vtiger_crmentity.crmid=?';
        $result=$adb->pquery($query,array($accountid));
        if(!$adb->num_rows($result)){
            return array('id'=>0);
        }
        return $result->fields;
    }

    /**
     * 获取用户的信息
     * @param $userid
     * @return mixed
     */
    public function geAachievementOwnerInfo($userid){
        global $adb;
        $query='SELECT * FROM vtiger_users 
                LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id
                 LEFT JOIN vtiger_user2role ON vtiger_user2role.userid=vtiger_users.id 
                WHERE vtiger_users.id=?';
        $result=$adb->pquery($query,array($userid));
        return $result->fields;
    }
    /**是否暂扣20%的产品
     * @param $result
     * @return bool
     */
    public function checkContactClass($resultData){
        if(stripos($resultData->fields['contract_no'],'VRZ')!==false){//百度V的跳过
            return false;
        }
        echo '<pre>'; print_r($resultData->fields['productid']);
        if(in_array($resultData->fields['productid'],array(38,7,126,106,125,105,0,124))){//词霸 宝盟,+,plus,另购项不扣,124——臻采购标准版——套餐，223——臻采购——单品，
            return false;
        }
        if($resultData->fields['contract_type']=='T云WEB版'){
            return true;
        }
        return (in_array($resultData->fields['productid'],array(61,9,10,11,20,21,22,23,25,24,26,27)) ||
            ($resultData->fields['parent_contracttypeid']==9 && in_array($resultData->fields['contract_type'],array('TSITE标准合同','TSITE合同','TSITE续费合同','TSITE新增协议','TSITE响应式合同','定制系统开发合同','云电商建站')))
        );
        /*return (
            $result->fields['contract_type']=='T云WEB版' ||
            ($result->field['parent_contracttypeid']==9 &&
                in_array($result->fields['contract_type'],array('TSITE标准合同','TSITE合同','TSITE续费合同','TSITE新增协议','TSITE响应式合同','定制系统开发合同','云电商建站')))
        );*/
    }
    /**
     * 需要加上的暂扣20%的业绩提成金额
     * @param $params
     * @return int
     */
    public function addWithholdPrecenTwentyAchievement($params){
        global $adb;
        $query='SELECT sum(IFNULL(amountofmoney,0)) AS sumamountofmoney FROM vtiger_withholdroyalty WHERE userid=? AND left(confirmationdate,7)=?';
        $result=$adb->pquery($query,array($params['userid'],$params['currentDate']));
        if(0==$adb->num_rows($result)){
            return 0;
        }
        return $result->fields['sumamountofmoney'];
    }
    public function updateAchievementMonth($achieve_statistic_records){
        global $adb;
        $sql = "select * from vtiger_achievementallot_statistic where achievementallotid in(".implode(',',$achieve_statistic_records).')';
        $result = $adb->pquery($sql);
        if(!$adb->num_rows($result)){
            return;
        }
        while ($row = $adb->fetch_row($result)){
            $rowDatas[] = $row;
        }

        foreach ($rowDatas as $rowData){
            $sql2 = "select * from vtiger_achievementsummary where achievementmonth=? and userid=? and achievementtype=?";
            $result2 = $adb->pquery($sql2,array($rowData['achievementmonth'],$rowData['receivedpaymentownid'],$rowData['achievementtype']));
            if($adb->num_rows($result2)){
                $rowData2 = $adb->query_result_rowdata($result2,0);
                $recorderModel2 = Vtiger_Record_Model::getInstanceById($rowData2['achievementid'],'AchievementSummary');
                $fieldsarray = array(
                    'unit_price',
                    'arriveachievement',
                    'effectiverefund',
                    'achievementmonth',
                );
                foreach ($fieldsarray as $fielddata){
                    if($rowData[$fielddata]){
                        $recorderModel2->set($fielddata,$rowData[$fielddata]+$rowData2[$fielddata]);
                    }
                    if($fielddata=='arriveachievement'){
                        $recorderModel2->set('realarriveachievement', $rowData[$fielddata] + $rowData2[$fielddata]);
                    }
                }
                $recorderModel2->set('achievementid',$rowData2['achievementid']);
                $recorderModel2->set('mode','edit');
                $recorderModel2->save();
            }
        }
    }

    //新增统计记录
    public function newAchievementSummary($achieve_statistic_records){
        global $adb;
        $sql = "select * from vtiger_achievementallot_statistic where achievementallotid in(".implode(',',$achieve_statistic_records).')';
        $result = $adb->pquery($sql);
        if(!$adb->num_rows($result)){
            return;
        }
        while ($row = $adb->fetch_row($result)){
            $rowDatas[] = $row;
        }

        foreach ($rowDatas as $rowData) {
            $sql2 = "select * from vtiger_achievementsummary where achievementmonth=? and userid=? and achievementtype=?";
            $result2 = $adb->pquery($sql2, array($rowData['achievementmonth'], $rowData['receivedpaymentownid'], $rowData['achievementtype']));
            if ($adb->num_rows($result2)) {
                $rowData2 = $adb->query_result_rowdata($result2, 0);
                $recorderModel2 = Vtiger_Record_Model::getInstanceById($rowData2['achievementid'], 'AchievementSummary');
                $fieldsarray = array(
                    'unit_price',
                    'arriveachievement',
                    'effectiverefund',
//                    'achievementmonth',
                );
                foreach ($fieldsarray as $fielddata) {
                    if ($rowData[$fielddata]) {
                        $recorderModel2->set($fielddata, $rowData[$fielddata] + $rowData2[$fielddata]);
                    }
                    if($fielddata=='arriveachievement'){
                        $recorderModel2->set('realarriveachievement', $rowData[$fielddata] + $rowData2[$fielddata]);
                    }
                }
                $recorderModel2->set('achievementid', $rowData2['achievementid']);
                $recorderModel2->set('mode', 'edit');
                $recorderModel2->save();
            } else {
                $recorderModel2 = Vtiger_Record_Model::getCleanInstance('AchievementSummary');
                $fieldsarray = array(
                    'unit_price',
                    'arriveachievement',
                    'effectiverefund',
                    'achievementmonth',
                    'departmentid',
                    'achievementtype'
                );
                foreach ($fieldsarray as $fielddata) {
                    if ($rowData[$fielddata]) {
                        $recorderModel2->set($fielddata, $rowData[$fielddata]);
                    }
                    if($fielddata=='arriveachievement'){
                        $recorderModel2->set('realarriveachievement', $rowData[$fielddata]);
                    }
                }
                $recorderModel2->set('userid', $rowData['receivedpaymentownid']);
                $recorderModel2->set('invoicecompany', $rowData['owncompanys']);
                $recorderModel2->set('confirmstatus', 'tobeconfirm');
                $recorderModel2->set('createtime', date('Y-m-d H:i:s'));
                $recorderModel2->save();
            }
        }
    }

    public function delAchievementSummary($achieve_statistic_records){
        global $adb;
        $sql = "select * from vtiger_achievementallot_statistic where achievementallotid in(".implode(',',$achieve_statistic_records).')';
        $result = $adb->pquery($sql);
        if(!$adb->num_rows($result)){
            return;
        }
        while ($row = $adb->fetch_row($result)){
            $rowDatas[] = $row;
        }
        foreach ($rowDatas as $rowData) {
            $sql2 = "select * from vtiger_achievementsummary where achievementmonth=? and userid=? and achievementtype=?";
            $result2 = $adb->pquery($sql2, array($rowData['achievementmonth'], $rowData['receivedpaymentownid'], $rowData['achievementtype']));
            if ($adb->num_rows($result2)) {
                $rowData2 = $adb->query_result_rowdata($result2, 0);
                if($rowData2['unit_price'] - $rowData['unit_price']<=0){
                    $sql='DELETE FROM vtiger_achievementsummary WHERE achievementid=?';
                    $adb->pquery($sql,array($rowData2['achievementid']));
                }else {
                    $recorderModel2 = Vtiger_Record_Model::getInstanceById($rowData2['achievementid'], 'AchievementSummary');
                    $fieldsarray = array(
                        'unit_price',
                        'arriveachievement',
                        'effectiverefund',
//                    'achievementmonth',
                    );
                    foreach ($fieldsarray as $fielddata) {
                        if ($rowData[$fielddata]) {
                            $recorderModel2->set($fielddata, $rowData2[$fielddata] - $rowData[$fielddata]);
                        }
                        if ($fielddata == 'arriveachievement') {
                            $recorderModel2->set('realarriveachievement', $rowData2[$fielddata] - $rowData[$fielddata]);
                        }
                    }
                    $recorderModel2->set('achievementid', $rowData2['achievementid']);
                    $recorderModel2->set('mode', 'edit');
                    $recorderModel2->save();

                }
            }
        }
    }

    /**
     * 获取在职员工人数
     * @param $params
     * @return int|mixed
     */
    public function getIncumbencyNumber($params){
        global $adb;
        $query="SELECT userid FROM vtiger_useractivemonth WHERE userid in(".implode(',',$params['subordinateusers']).") AND activedate='".$params['activedate']."' UNION SELECT id AS userid FROM vtiger_users WHERE LEFT(user_entered,7)='".$params['activedate']."' AND id in(".implode(',',$params['subordinateusers']).")";
        echo $query,'<br>';
		return $adb->num_rows($adb->pquery($query,array()));
    }
    /**
     * 获取业绩核算月的上个月的业绩
     * @param $params
     * @return int
     */
    public function getLastMonthPerformance($params){
        global $adb;
        $calculation_year_month=$this->getLastMonth($params);
        echo $calculation_year_month,'基数核算月份<hr>';
        //$query='SELECT realarriveachievement FROM vtiger_achievementsummary WHERE  achievementtype=\'newadd\' AND userid=? AND achievementmonth=?';
        $query='SELECT sum(if(arriveachievement>0,arriveachievement,0)) as realarriveachievement FROM vtiger_achievementallot_statistic WHERE  (achievementtype=\'newadd\' OR (achievementtype=\'renew\' AND more_years_renew=1)) AND receivedpaymentownid=? AND achievementmonth=?';
        $result=$adb->pquery($query,array($params['userid'],$calculation_year_month));
        $realarriveachievement=0;
        if($adb->num_rows($result)){
            $realarriveachievement=$result->fields['realarriveachievement'];
        }
        if($params['calculation_month']==3 && $calculation_year_month!=$params['calculation_year_month']){//排除3月份入职
            $query='SELECT sum(if(arriveachievement>0,arriveachievement,0)) as realarriveachievement FROM vtiger_achievementallot_statistic WHERE  (achievementtype=\'newadd\' OR (achievementtype=\'renew\' AND more_years_renew=1)) AND receivedpaymentownid=? AND achievementmonth=?';
            $result=$adb->pquery($query,array($params['userid'],$params['calculation_year_month']));
            $currentMontharriveachievement=0;
            if($adb->num_rows($result)){
                $currentMontharriveachievement=$result->fields['realarriveachievement'];
            }
            echo '<hr>';
            echo "当月的业绩",$currentMontharriveachievement,'上一个月的业绩',$realarriveachievement;
            $realarriveachievement*=2;
            $realarriveachievement=($realarriveachievement>$currentMontharriveachievement)?$realarriveachievement:$currentMontharriveachievement;
            echo '<hr>';
            echo '实际月的业绩',$realarriveachievement;
        }
        return $realarriveachievement;
    }

    /**
     *首次入职员工：新入职销售，根据入职时间判断首月，15号前入职包含当天，入职当月为首月。15号后入职，次月为首月。（首月业绩，作为次月判断提成系数的条件）
    例如：小王11月15前入职，11月作为首月，11月份干了5万，那么11月份18%提成点，12月份也是18%提成点。
    小王11月15号后入职，11月作为非首月，11月份干了5万，那么11月份18%提成点，12月份干了6万，12月份20%提成点，1月份20%提成点
     */
    public function getLastMonth($params){
        global $adb;
        $calculation_year_month =date("Y-m",strtotime("-1 months",strtotime($params['calculation_year_month'])));//默认的业绩核算月的前一个月
        $query='SELECT user_entered FROM vtiger_users WHERE id=?';
        $result=$adb->pquery($query,array($params['userid']));
        $userEntered=$result->fields['user_entered'];
        //echo '入职日期',$userEntered;

        $userEnteredArray=explode('-',$userEntered);
        $userEnteredYearMonth=$userEnteredArray[0].'-'.$userEnteredArray[1];
        if($calculation_year_month<=$userEnteredYearMonth){
            if($userEnteredArray[2]<=15){
                $calculation_year_month=$userEnteredYearMonth;
            }else{
                $calculation_year_month=$params['calculation_year_month'];
            }
        }
        /*echo '<hr>';
        //print_r($params);
        echo '基数月份',$calculation_year_month;
        echo '<hr>';*/
        return $calculation_year_month;
    }

    /**
     * 获取当月业绩之合
     * @param $params
     * @return int
     */
    public function getUserRealAchievement($params){
        global $adb;
        $query='SELECT sum(if(arriveachievement>0,arriveachievement,0)) AS arriveachievement FROM vtiger_achievementallot_statistic WHERE achievementmonth=? AND achievementtype=? AND receivedpaymentownid=?';
        $result=$adb->pquery($query,array($params['calculation_year_month'],$params['achievementtype'],$params['userid']));
        $arriveachievement=0;
        if($adb->num_rows($result)){
            $arriveachievement=$result->fields['arriveachievement'];
        }
        return $arriveachievement;
    }
        /**
     * 根据月份获取员工提成
     *
     * @param Vtiger_Request $request
     * @return array
     */
    public function getPercentageByMonth(Vtiger_Request $request){
        global $adb;
        $rawData=file_get_contents('php://input');
        $jsonData=(array)json_decode($rawData,true);
        $salay_year_month=$jsonData['assessMonth'];
        $month=substr($salay_year_month,0,7);
        $sql = "select userid,sum(actualroyalty) as percentage from vtiger_achievementsummary where achievementmonth=? group by userid";
        $result = $adb->pquery($sql,array($month));
        if($adb->num_rows($result)){
            $data=array();
            while($row=$adb->fetchByAssoc($result)){
                $data[]=$row;
            }
            return $return=array('success'=>true,'data'=>$data,'msg'=>'获取成功');
        }
        return $return=array('success'=>false,'msg'=>'没有相关数据');
    }

    /**
     * 将业绩明细表中状态更改为已核算业绩
     */
    public function updateStatusAch($ym){
        global $adb;
        $SQL='UPDATE vtiger_achievementallot_statistic set `status`=1 WHERE achievementmonth=?';
        $adb->pquery($SQL,array($ym));
    }

    /**
     * 获取地区等级
     * @param $userid
     * @return int|mixed
     */
    public function getRegionalLevel($userid){
        global $adb;
        $query='SELECT companyid FROM vtiger_users WHERE id=?';
        $result=$adb->pquery($query,array($userid));
        $regionalLevel=$this->regionalLevel[$result->fields['companyid']];
        return !empty($regionalLevel)?$regionalLevel:1;
    }
}