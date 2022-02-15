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
        ),'baseAmountRatio'=>0.8),//第三类
        4=>array('managerBaseSalary'=>array(
            0=>0,
            1=>672,
            2=>924,
            3=>1708,
            4=>2240,
            5=>0,
            6=>0
        ),'baseAmountRatio'=>0.7),//第四类
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
        31=>1,//广东珍岛信息技术有限公司

        //一级
        5=>2,//	杭州珍岛信息技术有限公司
        1=>2,//	苏州珍岛信息技术有限公司
        4=>2,//	宁波珍岛信息技术有限公司
        8=>2,//	无锡珍岛数字生态服务平台技术有限公司
        20=>2,//无锡珍岛智能技术有限公司
        33=>2,//上海珍岛智能技术集团有限公司南京分公司


        //二级
        //常州
        21=>3,//	无锡珍岛数字生态服务平台技术有限公司江阴分公司
        36=>3,//	上海珍岛智能技术集团有限公司南通分公司
        23=>3,//	中山珍岛信息技术有限公司
        30=>3,//	上海珍岛智能技术集团有限公司佛山分公司
        29=>3,//	上海珍岛智能技术集团有限公司东莞分公司
        22=>3,//	金华市珍岛信息技术有限公司
        24=>3,//	台州珍岛信息技术有限公司
        32=>3,//	上海珍岛智能技术集团有限公司顺德分公司
        35=>3,//	上海珍岛智能技术集团有限公司义乌分公司
        7=>3,//	    温州珍岛信息技术有限公司
        34=>3,//	上海珍岛智能技术集团有限公司扬州分公司

        //三级
        25=>4,//	昆山珍岛信息技术有限公司
        6=>4,//	成都珍岛信息技术有限公司
        42=>4,//上海珍岛智能技术集团有限公司绍兴分公司
            );
    public $achievementOwnerInfo=array();


    /**
     * 总监的提成计算公式
     * 1-商务总监  2-高级总监  3-资深总监
     * @var array
     */
    public $commissionerLevel= array(
        1=>array(
            "limitAmount"=>"200000",    //多少以下无提成
            "level"=>array(
                "200000"=>"0",          //200000以下提成为0
                "400000"=>"2000",       //400000可获取提成(400000-200000)*0.01
                "800000"=>"8000",       //800000可获取提成(800000-400000)*0.15+(400000-200000)*0.01 以此类推
                "1000000"=>"12000",
                "1200000"=>"17000",
                "1500000"=>"26000"
            ),
            "ratio"=>array(
                "200000"=>"0.01",         //20w-40w之间提成系数为0.01
                "400000"=>'0.015',     //40w-80w之间提成系数为0.15
                "800000"=>'0.02',
                "1000000"=>'0.025',
                "1200000"=>'0.03',
                "1500000"=>'0.06'
            )
        ),
        2=>array(
            "limitAmount"=>"600000",
            "level"=>array(
                "200000"=>"0",
                "400000"=>"2000",
                "800000"=>"8000",
                "1000000"=>"12000",
                "1200000"=>"17000",
                "1500000"=>"26000"
            ),
            "ratio"=>array(
                "200000"=>"0.01",         //200000以下无提成
                "400000"=>'0.015',     //20w-40w之间提成系数为0.01
                "800000"=>'0.02',
                "1000000"=>'0.025',
                "1200000"=>'0.03',
                "1500000"=>'0.06'
            )
        ),
        3=>array(
            "limitAmount"=>"800000",
            "level"=>array(
                "800000"=>'0',
                "1000000"=>"4000",
                "1200000"=>"9000",
                "1500000"=>"18000"
            ),
            "ratio"=>array(
                "800000"=>'0.02',
                "1000000"=>'0.025',
                "1200000"=>'0.03',
                "1500000"=>'0.06'
            )
        ),
    );
    public $productIds=array();//建站类另购产品id
    public $packageIds=array();//建站类套餐ID
    public $TyunTwentyProduct=array(24,25,136,137);//T云类暂扣20%的产品
    public $NTyunTwentyProduct=array(24,28,136,137);//非T云类暂扣20%的产品

    /**
     * @param $param
     */
    public function calculationRoalty($param){
        return false;
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
        $UserArriveAchievement=$this->getUserArriveAchievement($param);
        echo '<hr>';
        echo '当月业绩';
        print_r($UserArriveAchievement);
        if(empty($UserArriveAchievement)){//没有业绩不生成
            return ;
        }
        $param['realarriveachievement']=$UserArriveAchievement['arriveachievement'];
        $annualdiscount=$param['realarriveachievement']*0.01;// 年度折扣

        //多年单续费暂扣1%
        $query="SELECT SUM(a.arriveachievement*1)/100 as royaltytwo  FROM `vtiger_achievementallot_statistic` as a  WHERE   a.receivedpaymentownid=?  AND  a.achievementmonth=?  AND  a.achievementtype='renew' AND more_years_renew=1  AND a.renewal_commission in(9,10) AND isleave=0 AND a.arriveachievement>0 LIMIT 1";
        $result=$adb->pquery($query,array($param['userid'],$param['calculation_year_month']));
        if($adb->num_rows($result)){
            $data=$adb->query_result_rowdata($result,0);
            $annualdiscount+=$data['royaltytwo'];
        }
        echo $query;
        print_r(array($param['userid'],$param['calculation_year_month'],$annualdiscount));
        echo '年度拆扣<hr>';
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
        if($param['usergrade']==1 || $param['usergrade']==2 || in_array($param['usergrade'],array(18))){
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
            echo $param['realarriveachievement'];//当前业绩
            echo '当前业绩<hr>';
            $royalty=bcdiv(bcmul($param['realarriveachievement'],$royaltyRatio,6),100,4);
			$subWtidholdPrecenTwenty=$this->subWithholdTwentyAchievement(array('userid'=>$param['userid'],'royaltyratio'=>$royaltyRatio,'currentDate'=>$param['calculation_year_month']));
			$addWtidholdPrecenTwenty=$this->addWithholdPrecenTwentyAchievement(array('userid'=>$param['userid'],'currentDate'=>$param['calculation_year_month']));
			$sumActualRoyalty=bcadd(bcsub($royalty,$subWtidholdPrecenTwenty,6),$addWtidholdPrecenTwenty,6);
        }
        //$actualroyalty=$royalty+$annualpayment;
        $adjustmentAchievement=$this->adjustmentAchievement(array('uuserid'=>$param['userid'],'uachievementmonth'=>$param['calculation_year_month'],'uachievementtype'=>'','uperformancetype'=>'persontype'));
        $actualroyalty=$sumActualRoyalty+$annualpayment;
        $mrenewarriveachievement=$this->getAllReNewaddArriveachievement($param,0);//个人的多年单续费业绩
        //$sql=" UPDATE vtiger_achievementsummary SET performancetype='persontype',grantdetain=?,royalty=?,actualroyalty=?,annualdiscount=?,annualpayment=?,employeelevel=?,withholdroyaltyratio=?,deliverdetain=?,mrenewarriveachievement=? WHERE achievementid=? ";
        //$adb->pquery($sql,array($addWtidholdPrecenTwenty,$royalty,$actualroyalty,$annualdiscount,$annualpayment,$param['gradename'],$royaltyRatio,$subWtidholdPrecenTwenty,$mrenewarriveachievement,$param['achievementid']));
        $userInfo=$this->getUserInfo($param);
        $id=$adb->getUniqueID('vtiger_achievementsummary');
        $arhData['achievementid']=$id;
        $arhData['invoicecompany']=$userInfo['invoicecompany'];//合同主体公司
        $arhData['departmentid']=$userInfo['departmentid'];//部门
        $arhData['userid']=$param['userid'];//用户id
        $arhData['unit_price']=$UserArriveAchievement['unit_price'];//回款
        $arhData['arriveachievement']=$UserArriveAchievement['arriveachievement'];//到账业绩
        $arhData['realarriveachievement']=$UserArriveAchievement['arriveachievement'];//实际到账业绩
        $arhData['effectiverefund']=$UserArriveAchievement['effectiverefund'];//有效回款
        $arhData['achievementmonth']=$param['calculation_year_month'];//业绩月份
        $arhData['confirmstatus']='tobeconfirm';//状态 tobeconfirm待确认完结  confirmed 确认完结
        $arhData['workflowsid']='0';//工作流id
        //$arhData['workflowstime']='';//工作流节点激活时间
        //$arhData['workflowsnode']='';//工作流节点名称
        $arhData['modulestatus']='a_normal';//该条绩效状态
        //$arhData['remarks']='';//业绩调整备注
        //$arhData['adjustachievementrecord']='';
        $arhData['crmid']=0;//工作流生成记录id
        $arhData['createtime']=date('Y-m-d H:i:s');//创建时间
        $arhData['achievementtype']='newadd';//业绩类型
        //$arhData['adjustachievement']='';//业绩调整金额
        $arhData['employeelevel']=$param['gradename'];//级别
        $arhData['royalty']=$royalty;//提成
        $arhData['incumbency']=1;//业绩月在职人数
        $arhData['monthlysuspension']=0;//月度暂扣(当月总业绩的1%)
        $arhData['bonus']=0;//奖金
        $arhData['quarterlyaward']=0;//季度奖
        $arhData['halfyearlyaward']=0;//半年度奖金
        $arhData['deliverdetain']=$subWtidholdPrecenTwenty;//交付暂扣20%的业绩
        $arhData['grantdetain']=$addWtidholdPrecenTwenty;//交付发放20%的暂扣金额
        $arhData['withholdroyaltyratio']=$royaltyRatio;//提成比例
        $arhData['actualroyalty']=$actualroyalty;//实际提成
        $arhData['annualdiscount']=$annualdiscount;//年度折扣
        $arhData['annualpayment']=$annualpayment;//年度发放
        //$arhData['proportionofyears']='';//本月多年单占比是否超过30% 0 没超过 1超过 该字段不用了
        //$arhData['myearachievement']='';//多年单业绩该字段不用了
        //$arhData['usercode']='';//工号关联占位用
        $arhData['performancetype']='persontype';//业绩所属类型个人，部门
        $arhData['mrenewarriveachievement']=$mrenewarriveachievement;//多年单续费业绩
        //$arhData['quarterlytasks']='';//百度季度任务
        $keyarray=array_keys($arhData);
        $placeholder=array_map(function($v){return '?';},$keyarray);
        $sql='INSERT INTO `vtiger_achievementsummary` ('.implode(',',$keyarray).')VALUES('.implode(',',$placeholder).')';
        $adb->pquery($sql,array($arhData));
        print_r(array($addWtidholdPrecenTwenty,$royalty,$actualroyalty,$annualdiscount,$annualpayment,$param['gradename'],$royaltyRatio,$subWtidholdPrecenTwenty,$id));
        echo '<hr>';
    }

    /**
     * 员工负数提成
     * @param $paramN
     * @throws Exception
     */
    public function calulateEmployeeNewCommissionNegative($param){
        global $adb;
        $query='SELECT sum(commissionforrenewal) as commissionforrenewal FROM `vtiger_achievementallot_statistic` WHERE receivedpaymentownid=? AND achievementmonth=? AND arriveachievement<0';
        $result=$adb->pquery($query,array($param['userid'],$param['calculation_year_month']));
        if($adb->num_rows($result)==0){
            return ;
        }
        $commissionforrenewal=$result->fields['commissionforrenewal'];
        if($commissionforrenewal==0){
            return;
        }
        $commissionforrenewal=abs($commissionforrenewal);
        $remarks='订单作跨月取消扣提成'.$commissionforrenewal;
        $query="SELECT achievementid FROM vtiger_achievementsummary WHERE userid=? AND achievementmonth=? AND achievementtype='newadd'";
        $result=$adb->pquery($query,array($param['userid'],$param['calculation_year_month']));
        if($adb->num_rows($result)){
            $achievementid=$result->fields['achievementid'];
            $sql='UPDATE vtiger_achievementsummary SET actualroyalty=actualroyalty-?,remarks=CONCAT(IFNULL(remarks,\'\'),?) WHERE achievementid=?';
            $adb->pquery($sql,array($commissionforrenewal,'=>'.$remarks,$achievementid));
            return ;
        }
        $commissionforrenewal*=-1;
        $userInfo=$this->getUserInfo($param);
        $id=$adb->getUniqueID('vtiger_achievementsummary');
        $arhData['achievementid']=$id;
        $arhData['invoicecompany']=$userInfo['invoicecompany'];//合同主体公司
        $arhData['departmentid']=$userInfo['departmentid'];//部门
        $arhData['userid']=$param['userid'];//用户id
        $arhData['unit_price']=0;//回款
        $arhData['arriveachievement']=0;//到账业绩
        $arhData['realarriveachievement']=0;//实际到账业绩
        $arhData['effectiverefund']=0;//有效回款
        $arhData['achievementmonth']=$param['calculation_year_month'];//业绩月份
        $arhData['confirmstatus']='tobeconfirm';//状态 tobeconfirm待确认完结  confirmed 确认完结
        $arhData['workflowsid']='0';//工作流id
        //$arhData['workflowstime']='';//工作流节点激活时间
        //$arhData['workflowsnode']='';//工作流节点名称
        $arhData['modulestatus']='a_normal';//该条绩效状态
        $arhData['remarks']=$remarks;//业绩调整备注
        //$arhData['adjustachievementrecord']='';
        $arhData['crmid']=0;//工作流生成记录id
        $arhData['createtime']=date('Y-m-d H:i:s');//创建时间
        $arhData['achievementtype']='newadd';//业绩类型
        //$arhData['adjustachievement']='';//业绩调整金额
        $arhData['employeelevel']=$param['gradename'];//级别
        $arhData['royalty']=0;//提成
        $arhData['incumbency']=1;//业绩月在职人数
        $arhData['monthlysuspension']=0;//月度暂扣(当月总业绩的1%)
        $arhData['bonus']=0;//奖金
        $arhData['quarterlyaward']=0;//季度奖
        $arhData['halfyearlyaward']=0;//半年度奖金
        $arhData['deliverdetain']=0;//交付暂扣20%的业绩
        $arhData['grantdetain']=0;//交付发放20%的暂扣金额
        $arhData['withholdroyaltyratio']=0;//提成比例
        $arhData['actualroyalty']=$commissionforrenewal;//实际提成
        $arhData['annualdiscount']=0;//年度折扣
        $arhData['annualpayment']=0;//年度发放
        //$arhData['proportionofyears']='';//本月多年单占比是否超过30% 0 没超过 1超过 该字段不用了
        //$arhData['myearachievement']='';//多年单业绩该字段不用了
        //$arhData['usercode']='';//工号关联占位用
        $arhData['performancetype']='persontype';//业绩所属类型个人，部门
        $arhData['mrenewarriveachievement']=0;//多年单续费业绩
        //$arhData['quarterlytasks']='';//百度季度任务
        $keyarray=array_keys($arhData);
        $placeholder=array_map(function($v){return '?';},$keyarray);
        $sql='INSERT INTO `vtiger_achievementsummary` ('.implode(',',$keyarray).')VALUES('.implode(',',$placeholder).')';
        $adb->pquery($sql,array($arhData));
        echo '<hr>';



    }
    public function calulateEmployeeRenewCommission($param){
        global $adb;
        //seo系列、等需要长期维护的服务 的提成计算
        $sql=" SELECT SUM(a.arriveachievement*0.05) as royaltyone,sum(a.arriveachievement) as arriveachievement,sum(a.unit_price) as unit_price,sum(a.effectiverefund) as effectiverefund FROM `vtiger_achievementallot_statistic` as a INNER JOIN vtiger_servicecontracts as s ON s.servicecontractsid=a.servicecontractid  WHERE   a.receivedpaymentownid=?  AND  a.achievementmonth=?  AND  a.achievementtype='renew' AND s.parent_contracttypeid IN(4,6) AND  s.contract_type IN('百度微整站优化续费服务合同','百度微整站优化服务合同','搜索引擎左侧优化续费协议书','KA搜索引擎优化','T-网营销销售合同','整合营销合同','网络口碑营销合同书') AND isleave=0 AND arriveachievement>0 LIMIT 1 ";

        $result=$adb->pquery($sql,array($param['userid'],$param['calculation_year_month']));
        $data=$adb->query_result_rowdata($result,0);
        $royaltyOne=$data['royaltyone'];
        $arriveachievementOne=$data['arriveachievement'];
        $unit_pricetOne=$data['unit_price'];
        $effectiverefundOne=$data['effectiverefund'];
        //T- SITE系列、T云系列、微网站系列、空间服务器系列等一次性交付类产品  除了 seo系列、等需要长期维护的服务 的提成计算 和 域名空间类型续费，商务不核算提成；
        //$sql=" SELECT SUM(a.arriveachievement*0.06) as royaltytwo  FROM `vtiger_achievementallot_statistic` as a INNER JOIN vtiger_servicecontracts as s ON s.servicecontractsid=a.servicecontractid  WHERE   a.receivedpaymentownid=?  AND  a.achievementmonth=?  AND  a.achievementtype='renew'  AND  s.contract_no NOT LIKE ? AND (s.parent_contracttypeid NOT IN(4,6) || s.contract_type NOT IN('百度微整站优化续费服务合同','百度微整站优化服务合同','搜索引擎左侧优化续费协议书','KA搜索引擎优化','T-网营销销售合同','整合营销合同','网络口碑营销合同书'))  LIMIT 1 ";
        //$result=$adb->pquery($sql,array($param['userid'],$param['calculation_year_month'],'%TSITEXF%'));

        //$sql=" SELECT SUM(a.arriveachievement*6/pow(2,if(renewtimes>0,(renewtimes-1),0))/100) as royaltytwo  FROM `vtiger_achievementallot_statistic` as a INNER JOIN vtiger_servicecontracts as s ON s.servicecontractsid=a.servicecontractid  WHERE   a.receivedpaymentownid=?  AND  a.achievementmonth=?  AND  a.achievementtype='renew' AND (s.parent_contracttypeid NOT IN(4,6) || s.contract_type NOT IN('百度微整站优化续费服务合同','百度微整站优化服务合同','搜索引擎左侧优化续费协议书','KA搜索引擎优化','T-网营销销售合同','整合营销合同','网络口碑营销合同书'))  LIMIT 1 ";
        $sql=" SELECT SUM(a.arriveachievement*a.renewal_commission/100) as royaltytwo,sum(a.arriveachievement) as arriveachievement,sum(a.unit_price) as unit_price,sum(a.effectiverefund) as effectiverefund  FROM `vtiger_achievementallot_statistic` as a INNER JOIN vtiger_servicecontracts as s ON s.servicecontractsid=a.servicecontractid  WHERE   a.receivedpaymentownid=? AND a.renewal_commission!=10 AND a.renewal_commission!=9 AND  a.achievementmonth=?  AND  a.achievementtype='renew' AND (s.parent_contracttypeid NOT IN(4,6) || s.contract_type NOT IN('百度微整站优化续费服务合同','百度微整站优化服务合同','搜索引擎左侧优化续费协议书','KA搜索引擎优化','T-网营销销售合同','整合营销合同','网络口碑营销合同书'))  AND isleave=0 AND arriveachievement>0 LIMIT 1 ";
        //echo $sql; echo '<hr>';
        $result=$adb->pquery($sql,array($param['userid'],$param['calculation_year_month']));
        $data=$adb->query_result_rowdata($result,0);
        $royaltyTwo=$data['royaltytwo'];
        $arriveachievementTwo=$data['arriveachievement'];
        $unit_pricetTwo=$data['unit_price'];
        $effectiverefundTwo=$data['effectiverefund'];
        $royalty=$royaltyOne+$royaltyTwo;
        $arriveachievement=$arriveachievementOne+$arriveachievementTwo;
        $unit_price=$unit_pricetOne+$unit_pricetTwo;
        $effectiverefund=$effectiverefundOne+$effectiverefundTwo;
        $userInfo=$this->getUserInfo($param);
        if($royalty>0){
            $id=$adb->getUniqueID('vtiger_achievementsummary');
            $arhData['achievementid']=$id;
            $arhData['invoicecompany']=$userInfo['invoicecompany'];//合同主体公司
            $arhData['departmentid']=$userInfo['departmentid'];//部门
            $arhData['userid']=$param['userid'];//用户id
            $arhData['unit_price']=$unit_price;//回款
            $arhData['arriveachievement']=$arriveachievement;//到账业绩
            $arhData['realarriveachievement']=$arriveachievement;//实际到账业绩
            $arhData['effectiverefund']=$effectiverefund;//有效回款
            $arhData['achievementmonth']=$param['calculation_year_month'];//业绩月份
            $arhData['confirmstatus']='tobeconfirm';//状态 tobeconfirm待确认完结  confirmed 确认完结
            $arhData['workflowsid']='0';//工作流id
            //$arhData['workflowstime']='';//工作流节点激活时间
            //$arhData['workflowsnode']='';//工作流节点名称
            $arhData['modulestatus']='a_normal';//该条绩效状态
            //$arhData['remarks']='';//业绩调整备注
            //$arhData['adjustachievementrecord']='';
            $arhData['crmid']=0;//工作流生成记录id
            $arhData['createtime']=date('Y-m-d H:i:s');//创建时间
            $arhData['achievementtype']='renew';//业绩类型
            //$arhData['adjustachievement']='';//业绩调整金额
            $arhData['employeelevel']=$param['gradename'];//级别
            $arhData['royalty']=$royalty;//提成
            $arhData['incumbency']=1;//业绩月在职人数
            $arhData['monthlysuspension']=0;//月度暂扣(当月总业绩的1%)
            $arhData['bonus']=0;//奖金
            $arhData['quarterlyaward']=0;//季度奖
            $arhData['halfyearlyaward']=0;//半年度奖金
            $arhData['deliverdetain']=0;//交付暂扣20%的业绩
            $arhData['grantdetain']=0;//交付发放20%的暂扣金额
            $arhData['withholdroyaltyratio']=0;//提成比例
            $arhData['actualroyalty']=$royalty;//实际提成
            $arhData['annualdiscount']=0;//年度折扣
            $arhData['annualpayment']=0;//年度发放
            //$arhData['proportionofyears']='';//本月多年单占比是否超过30% 0 没超过 1超过 该字段不用了
            //$arhData['myearachievement']='';//多年单业绩该字段不用了
            //$arhData['usercode']='';//工号关联占位用
            $arhData['performancetype']='persontype';//业绩所属类型个人，部门
            //$arhData['mrenewarriveachievement']=0;//多年单续费业绩
            //$arhData['quarterlytasks']='';//百度季度任务
            $keyarray=array_keys($arhData);
            $placeholder=array_map(function($v){return '?';},$keyarray);
            $sql='INSERT INTO `vtiger_achievementsummary` ('.implode(',',$keyarray).')VALUES('.implode(',',$placeholder).')';
            $adb->pquery($sql,array($arhData));
        }


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
        $query="SELECT SUM(a.arriveachievement*9/100) as royaltytwo,sum(a.arriveachievement) as arriveachievement,sum(a.unit_price) as unit_price,sum(a.effectiverefund) as effectiverefund  FROM `vtiger_achievementallot_statistic` as a  WHERE   a.receivedpaymentownid=?  AND  a.achievementmonth=?  AND  a.achievementtype='renew' AND more_years_renew=1 AND a.renewal_commission in(9,10) AND isleave=0 AND arriveachievement>0 LIMIT 1";
        $result=$adb->pquery($query,array($param['userid'],$param['calculation_year_month']));
        $royalty=0;
        $arriveachievement=0;
        if($adb->num_rows($result)){
            $data=$adb->query_result_rowdata($result,0);
            $royalty+=$data['royaltytwo'];
            $arriveachievement+=$data['arriveachievement'];
            $unit_price=$data['arriveachievement'];
            $effectiverefund=$data['arriveachievement'];
        }

        $actualroyalty=$royalty+$annualpayment;
        if($actualroyalty==0){
            return;
        }
        $id=$adb->getUniqueID('vtiger_achievementsummary');
        $arhData['achievementid']=$id;
        $arhData['invoicecompany']=$userInfo['invoicecompany'];//合同主体公司
        $arhData['departmentid']=$userInfo['departmentid'];//部门
        $arhData['userid']=$param['userid'];//用户id
        $arhData['unit_price']=$unit_price;//回款
        $arhData['arriveachievement']=$arriveachievement;//到账业绩
        $arhData['realarriveachievement']=$arriveachievement;//实际到账业绩
        $arhData['effectiverefund']=$effectiverefund;//有效回款
        $arhData['achievementmonth']=$param['calculation_year_month'];//业绩月份
        $arhData['confirmstatus']='tobeconfirm';//状态 tobeconfirm待确认完结  confirmed 确认完结
        $arhData['workflowsid']='0';//工作流id
        //$arhData['workflowstime']='';//工作流节点激活时间
        //$arhData['workflowsnode']='';//工作流节点名称
        $arhData['modulestatus']='a_normal';//该条绩效状态
        //$arhData['remarks']='';//业绩调整备注
        //$arhData['adjustachievementrecord']='';
        $arhData['crmid']=0;//工作流生成记录id
        $arhData['createtime']=date('Y-m-d H:i:s');//创建时间
        $arhData['achievementtype']='mrenew';//业绩类型
        //$arhData['adjustachievement']='';//业绩调整金额
        $arhData['employeelevel']=$param['gradename'];//级别
        $arhData['royalty']=$royalty;//提成
        $arhData['incumbency']=1;//业绩月在职人数
        $arhData['monthlysuspension']=0;//月度暂扣(当月总业绩的1%)
        $arhData['bonus']=0;//奖金
        $arhData['quarterlyaward']=0;//季度奖
        $arhData['halfyearlyaward']=0;//半年度奖金
        $arhData['deliverdetain']=0;//交付暂扣20%的业绩
        $arhData['grantdetain']=0;//交付发放20%的暂扣金额
        $arhData['withholdroyaltyratio']=9;//提成比例
        $arhData['actualroyalty']=$actualroyalty;//实际提成
        $arhData['annualdiscount']=$annualdiscount;//年度折扣
        $arhData['annualpayment']=$annualpayment;//年度发放
        //$arhData['proportionofyears']='';//本月多年单占比是否超过30% 0 没超过 1超过 该字段不用了
        //$arhData['myearachievement']='';//多年单业绩该字段不用了
        //$arhData['usercode']='';//工号关联占位用
        $arhData['performancetype']='persontype';//业绩所属类型个人，部门
        //$arhData['mrenewarriveachievement']=$mrenewarriveachievement;//多年单续费业绩
        //$arhData['quarterlytasks']='';//百度季度任务
        $keyarray=array_keys($arhData);
        $placeholder=array_map(function($v){return '?';},$keyarray);
        $sql='INSERT INTO `vtiger_achievementsummary` ('.implode(',',$keyarray).')VALUES('.implode(',',$placeholder).')';
        $adb->pquery($sql,array($arhData));
        //$sql=" UPDATE vtiger_achievementsummary SET performancetype='persontype',royalty=?,actualroyalty=?,annualdiscount=?,annualpayment=?,employeelevel=? WHERE achievementid=? ";
        //$adb->pquery($sql,array($royalty,$actualroyalty,$annualdiscount,$annualpayment,$param['gradename'],$param['achievementid']));
    }

    /**
     * 经理的新单提成
     * @param $param
     * @throws Exception
     */
    public function calulateManagerNewCommission($param){
        global $adb;
        $userIds=$this->getSubordinateUsers($param);// 该经理的所有下属包含所有的经理
        print_r(array($param['userid']=>$userIds));
        $adb->pquery("DELETE FROM vtiger_achievementsummary WHERE achievementmonth=? AND userid=?",array($param['calculation_year_month'],$param['userid']));
        //$queryperson="SELECT sum(a.arriveachievement) AS arriveachievement,sum(if(a.achievementtype = 'renew' AND a.more_years_renew = 1,0,a.unit_price)) AS unit_price,sum(if(a.achievementtype = 'renew' AND a.more_years_renew = 1,0,a.effectiverefund)) AS effectiverefund FROM `vtiger_achievementallot_statistic` AS a WHERE ((a.receivedpaymentownid =? AND a.isleave=0) OR (a.receivedpaymentownid IN (".implode(",",$userIds).") AND a.isleave=1)) AND a.achievementmonth =? AND ((a.achievementtype = 'renew' AND a.more_years_renew = 1) OR a.achievementtype = 'newadd')  LIMIT 1";
        $queryperson="SELECT sum(a.arriveachievement) AS arriveachievement,sum(if(a.achievementtype = 'renew' AND a.more_years_renew = 1,0,if(a.arriveachievement>0,a.unit_price,0))) AS unit_price,sum(if(a.achievementtype = 'renew' AND a.more_years_renew = 1,0,a.effectiverefund)) AS effectiverefund FROM `vtiger_achievementallot_statistic` AS a WHERE ((a.receivedpaymentownid =? AND a.isleave=0) OR (a.receivedpaymentownid IN (SELECT subordinateid FROM vtiger_useractivemonthnew WHERE vtiger_useractivemonthnew.userid=? AND vtiger_useractivemonthnew.activedate=a.achievementmonth) AND a.isleave=1)) AND a.achievementmonth =? AND ((a.achievementtype = 'renew' AND a.more_years_renew = 1) OR a.achievementtype = 'newadd') LIMIT 1";
        $resultperson=$adb->pquery($queryperson,array($param['userid'],$param['userid'],$param['calculation_year_month']));
        $persionarriveachievement=$resultperson->fields['arriveachievement'];//个人的业绩
        $persionunit_price=$resultperson->fields['unit_price'];//个人的回款
        $persioneffectiverefund=$resultperson->fields['effectiverefund'];//个人的有效回款
        $proportionofyears=$this->proportionOfSingleYear($userIds,$param['calculation_year_month']);//多年单占比
        $regionalLevel=$this->getRegionalLevel($param['userid']);//获取等级
        $areaManagerRoyaltyRatio=$this->areaManagerRoyaltyRatio[$regionalLevel];
        $managerBaseSalary=$areaManagerRoyaltyRatio['managerBaseSalary'];
        $baseAmountRatio=$areaManagerRoyaltyRatio['baseAmountRatio'];
        //$userIds=" a.receivedpaymentownid IN (".implode(",",$userIds).")";
        //汇总该经理所有下属员工的到账业绩  回款金额


        //$sql="SELECT sum(a.arriveachievement) AS realarriveachievement,sum(if(a.achievementtype = 'renew' AND a.more_years_renew = 1,0,a.unit_price)) AS unit_price,sum(if(a.achievementtype = 'renew' AND a.more_years_renew = 1,0,a.effectiverefund)) AS effectiverefund FROM `vtiger_achievementallot_statistic` AS a WHERE ".$userIds." AND a.achievementmonth =? AND ((a.achievementtype = 'renew' AND a.more_years_renew = 1) OR a.achievementtype = 'newadd')  AND isleave=0 LIMIT 1";
        $sql="SELECT sum(a.arriveachievement) AS realarriveachievement,sum(if(a.achievementtype = 'renew' AND a.more_years_renew = 1,0,if(a.arriveachievement>0,a.unit_price,0))) AS unit_price,sum(if(a.achievementtype = 'renew' AND a.more_years_renew = 1,0,a.effectiverefund)) AS effectiverefund FROM `vtiger_achievementallot_statistic` AS a WHERE a.receivedpaymentownid in(SELECT subordinateid FROM vtiger_useractivemonthnew WHERE vtiger_useractivemonthnew.userid=? AND vtiger_useractivemonthnew.activedate=a.achievementmonth) AND a.achievementmonth =? AND ((a.achievementtype = 'renew' AND a.more_years_renew = 1) OR a.achievementtype = 'newadd')  AND isleave=0 LIMIT 1";
        //$sql="SELECT SUM(realarriveachievement) as realarriveachievement,SUM(arriveachievement) as arriveachievement,SUM(unit_price) as unit_price,SUM(effectiverefund) as effectiverefund FROM `vtiger_achievementsummary` WHERE ".$userIds." AND achievementmonth=? AND achievementtype='newadd' LIMIT 1 ";
        $result=$adb->pquery($sql,array($param['userid'],$param['calculation_year_month']));
        echo $sql,$param['calculation_year_month'];
        echo '<hr>';
        $data=$adb->query_result_rowdata($result,0);
        $money=0;
        if($proportionofyears){
            //$money=$this->getAllNewaddArriveachievement($userIdsArray,$param['calculation_year_month'],'newadd');//多年单的第一年业绩
            //$data['realarriveachievement']=$data['realarriveachievement']-$money;//减掉多年单的第一年业绩
            //$money+=$this->getAllNewaddArriveachievement($userIdsArray,$param['calculation_year_month'],'renew');//多年单的续费业绩
        }
        //echo '下属的业绩之和'.$data['realarriveachievement'],'<hr>';
        //$data['realarriveachievement']+=$persionarriveachievement;
        echo '经理的个人业绩',$persionarriveachievement;
        echo '<hr>';
        echo '下属的业绩+经理的业绩之和',$data['realarriveachievement'];
        echo '<hr>';
        // 六个月以上的老经理    见习经理/经理：H18 < 80000;高级经理：H18 < 160000； 提成 = 0；
        $currentKey=0;
        $incumbencynumber=$param['incumbencynumber'];
        if(($data['realarriveachievement']<80000*$baseAmountRatio && $param['newmanagersixmonths']!=1 && in_array($param['usergrade'],array(9,10,23))) || ($data['realarriveachievement']<160000*$baseAmountRatio && $param['newmanagersixmonths']!=1 && $param['usergrade']==11)){
            $royalty=0;
            // 除了上面特殊的 下面为正常计算流程
        }else{
            if (300000*$baseAmountRatio < $data['realarriveachievement']) {
                if ($incumbencynumber > 12) {
                    $currentKey = 4;
                } else {
                    $currentKey = 6;
                }
            } else if (250000*$baseAmountRatio <= $data['realarriveachievement'] && $data['realarriveachievement'] <= 300000*$baseAmountRatio) {
                if ($incumbencynumber > 12) {
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
        $userInfo=$this->getUserInfo($param);
        $annualdiscount=$royalty>0?bcdiv(bcmul($data['realarriveachievement'],2,6),1000,6):0;// 年度折扣
        // 如果计算提成的月份是12月份 则计算年度发放 否则 年度发放为0
        /*if($param['calculation_month']==12){
            $annualpayment=$annualdiscount+$this->getOneToElevenAnnualdiscount($param);
        }else{
            $annualpayment=0;
        }*/
        $annualpayment=0;
        $grantdetain=$this->addWithholdPrecenTwentyAchievement(array('userid'=>$param['userid'],'currentDate'=>$param['calculation_year_month']));
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
        $params['actualroyalty']=$royalty+$annualpayment+$grantdetain;// 实际提成=提成+年度发放+交付发放
        $params['effectiverefund']=$data['effectiverefund'];
        $params['achievementmonth']=$param['calculation_year_month'];
        $params['confirmstatus']='tobeconfirm';
        $params['modulestatus']='a_normal';
        $params['proportionofyears']=$proportionofyears;
        $params['myearachievement']=$money;
        $params['performancetype']='departmenttype';
        $params['grantdetain']=$grantdetain;
        $params['incumbency']=$incumbencynumber;
        $params['mrenewarriveachievement']=$this->getAllReNewaddArriveachievement($param,1);
        $id=$adb->getUniqueID('vtiger_achievementsummary');
        $sql="INSERT INTO `vtiger_achievementsummary` (achievementid,`invoicecompany`, `departmentid`, `userid`, `unit_price`, `arriveachievement`, `realarriveachievement`, `effectiverefund`, `achievementmonth`, `createtime`, `adjustachievement`, `employeelevel`, `royalty`, `actualroyalty`, `achievementtype`, `confirmstatus`, `modulestatus`,`annualdiscount`, `annualpayment`, `proportionofyears`,myearachievement,performancetype,incumbency,grantdetain,mrenewarriveachievement) 
                       VALUES (".$id.",?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        echo $sql,'<hr>';
        $adb->pquery($sql,array($params['invoicecompany'],$params['departmentid'],$params['userid'],$params['unit_price'],$params['arriveachievement'],$params['realarriveachievement'],$params['effectiverefund'],$params['achievementmonth'],date("Y-m-d H:i:s"),0,$param['gradename'],$params['royalty'],$params['actualroyalty'],$param['achievementtype'],$params['confirmstatus'],$params['modulestatus'],$params['annualdiscount'],$params['annualpayment'],$params['proportionofyears'],$params['myearachievement'],$params['performancetype'],$param['incumbencynumber'],$params['grantdetain'],$params['mrenewarriveachievement']));
        //经理的个人新单业绩

        $params=array();
        $grantdetain=$this->getManagerGrantdetain($param['userid'],$param['calculation_year_month']);
        $params['invoicecompany']=$userInfo['invoicecompany'];
        $params['departmentid']=$userInfo['departmentid'];
        $params['userid']=$param['userid'];
        $params['unit_price']=$persionunit_price;
        $params['arriveachievement']=$persionarriveachievement;
        $params['adjustachievement']=0;
        $params['realarriveachievement']=$persionarriveachievement;
        $params['annualdiscount']=0;// 年度折扣
        $params['annualpayment']=0;// 年度发放
        $params['royalty']=$persionarriveachievement*0.1;// 提成
        $params['actualroyalty']=$persionarriveachievement*0.1+$grantdetain;// 实际提成=提成+年度发放
        $params['effectiverefund']=$persioneffectiverefund;
        $params['achievementmonth']=$param['calculation_year_month'];
        $params['confirmstatus']='tobeconfirm';
        $params['modulestatus']='a_normal';
        $params['proportionofyears']=0;
        $params['myearachievement']=0;
        $params['performancetype']='mpersontype';
        $params['grantdetain']=$grantdetain;
        $params['mrenewarriveachievement']=0;

        $id=$adb->getUniqueID('vtiger_achievementsummary');
        $sql="INSERT INTO `vtiger_achievementsummary` (achievementid,`invoicecompany`, `departmentid`, `userid`, `unit_price`, `arriveachievement`, `realarriveachievement`, `effectiverefund`, `achievementmonth`, `createtime`, `adjustachievement`, `employeelevel`, `royalty`, `actualroyalty`, `achievementtype`, `confirmstatus`, `modulestatus`,`annualdiscount`, `annualpayment`, `proportionofyears`,myearachievement,remarks,performancetype,grantdetain,mrenewarriveachievement) 
                       VALUES (".$id.",?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        echo '经理的个人业绩';
        print_r(array($params['invoicecompany'],$params['departmentid'],$params['userid'],$params['unit_price'],$params['arriveachievement'],$params['realarriveachievement'],$params['effectiverefund'],$params['achievementmonth'],date("Y-m-d H:i:s"),0,$param['gradename'],$params['royalty'],$params['actualroyalty'],$param['achievementtype'],$params['confirmstatus'],$params['modulestatus'],$params['annualdiscount'],$params['annualpayment'],$params['proportionofyears'],$params['myearachievement'],$params['performancetype'],$param['grantdetain']));
        echo '<hr>';
        $adb->pquery($sql,array($params['invoicecompany'],$params['departmentid'],$params['userid'],$params['unit_price'],$params['arriveachievement'],$params['realarriveachievement'],$params['effectiverefund'],$params['achievementmonth'],date("Y-m-d H:i:s"),0,$param['gradename'],$params['royalty'],$params['actualroyalty'],$param['achievementtype'],$params['confirmstatus'],$params['modulestatus'],$params['annualdiscount'],$params['annualpayment'],$params['proportionofyears'],$params['myearachievement'],'',$params['performancetype'],$param['grantdetain'],$params['mrenewarriveachievement']));
    }
    public function calulateManagerReNewCommission($param){
        global $adb;
        //查出部门所有用户userid
        /*$userIds=$this->getSubordinateUsers($param);// 该经理的所有下属包含所有的经理
        $query="SELECT
                    sum(a.arriveachievement) AS arriveachievement,
                    sum(a.unit_price) AS unit_price,
                    sum(a.effectiverefund) AS effectiverefund
                FROM
                    `vtiger_achievementallot_statistic` AS a
                WHERE
                    a.receivedpaymentownid =?
                AND a.achievementmonth =?
                AND a.achievementtype = 'renew' AND a.more_years_renew = 0
                LIMIT 1";
        $result=$adb->pquery($query,array($param['userid'],$param['calculation_year_month']));
        echo $query,'<=>',$param['userid'],'<=>',$param['calculation_year_month'],'<hr>';

        $arriveachievement=$result->fields['arriveachievement'];
        $proportionofyears=$this->proportionOfSingleYear($userIds,$param['calculation_year_month']);
        //$userIdstring=" userid IN (".implode(",",$userIds).")";
        */
        //$userIds[]=$param['userid'];
        //$userIdstring=" a.receivedpaymentownid IN (".implode(",",$userIds).")";
        //汇总该经理所有下属员工的到账业绩  回款金额
        //$sql=" SELECT SUM(realarriveachievement) as realarriveachievement,SUM(arriveachievement) as arriveachievement,SUM(unit_price) as unit_price,SUM(effectiverefund) as effectiverefund FROM `vtiger_achievementsummary` WHERE ".$userIdstring." AND achievementmonth=? AND achievementtype='renew' LIMIT 1 ";
        /*$sql="SELECT
                    sum(a.arriveachievement) AS arriveachievement,
                    sum(a.unit_price) AS unit_price,
                    sum(a.effectiverefund) AS effectiverefund
                FROM
                    `vtiger_achievementallot_statistic` AS a
                WHERE
                   ".$userIdstring."
                AND a.achievementmonth =?
                AND a.achievementtype = 'renew' AND a.more_years_renew = 0
                LIMIT 1";*/
        $sql="SELECT  sum(a.arriveachievement) AS arriveachievement,
                    sum(a.unit_price) AS unit_price,
                    sum(a.effectiverefund) AS effectiverefund
                FROM
                    `vtiger_achievementallot_statistic` AS a
                WHERE
                   a.receivedpaymentownid in(SELECT subordinateid FROM vtiger_useractivemonthnew WHERE vtiger_useractivemonthnew.userid=? AND vtiger_useractivemonthnew.activedate=a.achievementmonth)
                AND a.achievementmonth =?
                AND a.achievementtype = 'renew' AND a.more_years_renew = 0  AND a.arriveachievement>0
                LIMIT 1";
        echo '经理续费的',$param['userid'],'-----',$param['calculation_year_month'],'<hr>';
        echo $sql;
        echo '<hr>';
        $result=$adb->pquery($sql,array($param['userid'],$param['calculation_year_month']));
        $data=$adb->query_result_rowdata($result,0);

        $money=0;
        //if($proportionofyears){
            //$money=$this->getAllNewaddArriveachievement($userIds,$param['calculation_year_month'],'renew');
            //$data['realarriveachievement']=$data['realarriveachievement']-$money;//多年单续费业绩已核算到新单里了
        //}
        //$data['realarriveachievement']=+$arriveachievement;
        //查询他手下的所有实际到账业绩
        //$royalty=bcdiv($data['realarriveachievement'],100,6);
        $royalty=bcdiv($data['arriveachievement'],100,6);
        $userInfo=$this->getUserInfo($param);
        $departmentid=!empty($userInfo['departmentid'])?$userInfo['departmentid']:$userInfo['departmentid2'];
        $annualpayment=0;
        $annualdiscount=0;
        $params['invoicecompany']=$userInfo['invoicecompany'];
        $params['departmentid']=$departmentid;
        $params['userid']=$param['userid'];
        $params['unit_price']=$data['unit_price'];
        $params['arriveachievement']=$data['arriveachievement'];
        $params['adjustachievement']=0;
        $params['realarriveachievement']=$data['arriveachievement'];
        $params['annualdiscount']=$annualdiscount;// 年度折扣
        $params['annualpayment']=$annualpayment;// 年度发放
        $params['royalty']=$royalty;// 提成
        $params['actualroyalty']=$royalty+$annualpayment;// 实际提成=提成+年度发放
        $params['effectiverefund']=$data['effectiverefund'];
        $params['achievementmonth']=$param['calculation_year_month'];
        $params['confirmstatus']='tobeconfirm';
        $params['modulestatus']='a_normal';
        $params['proportionofyears']=0;
        $params['myearachievement']=$money;
        $params['performancetype']='departmenttype';
        $id=$adb->getUniqueID('vtiger_achievementsummary');
        $sql="INSERT INTO `vtiger_achievementsummary` (achievementid,`invoicecompany`, `departmentid`, `userid`, `unit_price`, `arriveachievement`, `realarriveachievement`, `effectiverefund`, `achievementmonth`, `createtime`, `adjustachievement`, `employeelevel`, `royalty`, `actualroyalty`, `achievementtype`, `confirmstatus`, `modulestatus`,`annualdiscount`, `annualpayment`,`proportionofyears`,myearachievement,performancetype) 
                       VALUES ($id,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $adb->pquery($sql,array($params['invoicecompany'],$params['departmentid'],$params['userid'],$params['unit_price'],$params['arriveachievement'],$params['realarriveachievement'],$params['effectiverefund'],$params['achievementmonth'],date("Y-m-d H:i:s"),0,$param['gradename'],$params['royalty'],$params['actualroyalty'],$param['achievementtype'],$params['confirmstatus'],$params['modulestatus'],$params['annualdiscount'],$params['annualpayment'],$params['proportionofyears'],$params['myearachievement'],$params['performancetype']));
    }
    // 获取下属多年单到账业绩
    private function getAllNewaddArriveachievement($userIds,$achievementmonth,$achievementType){
        global $adb;
        $query="SELECT SUM(arriveachievement) as money FROM vtiger_achievementallot_statistic WHERE achievementmonth=? and  more_years_renew=1  AND achievementtype=? and receivedpaymentownid IN (".implode(",",$userIds)." )  AND arriveachievement>0";
        $result=$adb->pquery($query,array($achievementmonth,$achievementType));
        $result=$adb->query_result_rowdata($result,0);
        return $result['money'];
    }
    /**
     * 获取经理或个人的多年单续费到账业绩
     *
     ***/
    private function getAllReNewaddArriveachievement($param,$isManager=0){
        global $adb;
        if(1==$isManager){
            $receivedpaymentownid=' in(SELECT subordinateid FROM vtiger_useractivemonthnew WHERE vtiger_useractivemonthnew.userid=? AND vtiger_useractivemonthnew.activedate=a.achievementmonth)';
        }else{
            $receivedpaymentownid='=?';
        }
        $query="SELECT 
                    sum(a.arriveachievement) AS realarriveachievement
                FROM 
                    `vtiger_achievementallot_statistic` AS a 
                WHERE 
                    a.receivedpaymentownid".$receivedpaymentownid."
                    AND a.achievementmonth =? 
                    AND a.achievementtype = 'renew' 
                    AND a.more_years_renew = 1  
                    AND isleave=0 AND arriveachievement>0 LIMIT 1;";
        $result=$adb->pquery($query,array($param['userid'],$param['calculation_year_month']));
        $realarriveachievement=$result->fields['realarriveachievement']>0?$result->fields['realarriveachievement']:0;
        return $realarriveachievement;
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
        $assigner='';
        if(!empty($params['assigner'])){
            $assigner='  AND userid in('.$params['assigner'].')';
        }
        $managerInfo=$adb->pquery("SELECT * FROM vtiger_usergraderoyalty WHERE usergrade>0".$assigner,array());
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
        $usergraderoyalty=empty($usergraderoyalty)?array(-1):$usergraderoyalty;
        $query="SELECT userid FROM vtiger_achievementsummary WHERE achievementtype='newadd' AND userid in(".implode(',',$usergraderoyalty).") AND achievementmonth=?";
        $result=$adb->pquery($query,array($params['calculation_year_month']));
        $userAchievementSummary=array();//有新单业绩人数
        if($adb->num_rows($result)){
            while($row=$adb->fetch_array($result)){
                $userAchievementSummary[]=$row['userid'];
            }
        }
        $withholdTwentyusers=array();//存在20%暂扣交付的人员
        foreach($usergraderoyalty as $value){
            $argv=array('userid'=>$value,'currentDate'=>$params['calculation_year_month']);
            if(!in_array($value,$userAchievementSummary)){//不存提成的人员
                $withholdTwentyAchievement=$this->addWithholdPrecenTwentyAchievement($argv);
                $performancetype=$usergraderoyaltyinfo[$value]['staffrank']==1?'departmenttype':'persontype';
                $amount=$this->adjustmentAchievement(array('uuserid'=>$value,'uachievementmonth'=>$params['calculation_year_month'],'uachievementtype'=>'newadd','uperformancetype'=>$performancetype));
                if($withholdTwentyAchievement>0 || $amount>0) {
                    $withholdTwentyusers[] = $value;
                    $userInfo=$this->getUserInfo(array('userid'=>$value,'calculation_year_month'=>$params['calculation_year_month']));
                    $thisArray=array(
                        'achievementid'=>$adb->getUniqueID('vtiger_achievementsummary'),
                        'invoicecompany'=>$userInfo['invoicecompany'],
                        'departmentid'=>$userInfo['departmentid'],
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
                        'actualroyalty'=>$withholdTwentyAchievement+$amount,
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
                        'performancetype'=>$performancetype,
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
            $assigner='';
            if(!empty($params['assigner'])){
                $assigner='  AND userid in('.$params['assigner'].')';
            }
            $query="SELECT distinct userid FROM vtiger_achievementsummary WHERE  achievementtype='newadd' AND achievementmonth=?".$assigner;
            $result=$adb->pquery($query,array($params['calculation_year_month']));
            $userAchievementSummary=array();//有新单业绩人数
            if($adb->num_rows($result)){
                while($row=$adb->fetch_array($result)){
                    $userAchievementSummary[]=$row['userid'];
                }
            }
            $argv=array('achievementtype'=>'newadd','calculation_year'=>$params['calculation_year']);
            foreach($usergraderoyalty as $value){
                $argv['userid']=$value;
                $toElevenAnnualdiscount=$this->getOneToElevenAnnualdiscount($argv);//计算年度暂扣1%或2/千发放
                if($toElevenAnnualdiscount>0){
                    $performancetype=$usergraderoyaltyinfo[$value]['staffrank']==1?'departmenttype':'persontype';
                    if(in_array($value,$userAchievementSummary)){
                        //update
                        $query='UPDATE vtiger_achievementsummary SET annualpayment='.$toElevenAnnualdiscount.',actualroyalty=actualroyalty+'.$toElevenAnnualdiscount.' WHERE userid=? AND achievementmonth=? AND achievementtype=\'newadd\' AND performancetype=\''.$performancetype.'\'';
                        $adb->pquery($query,array($value,$params['calculation_year_month']));
                    }else{
                        $userInfo=$this->getUserInfo(array('userid'=>$value,'calculation_year_month'=>$params['calculation_year_month']));
                        $amount=$this->adjustmentAchievement(array('uuserid'=>$value,'uachievementmonth'=>$params['calculation_year_month'],'uachievementtype'=>'newadd','uperformancetype'=>$performancetype));//调整金额
                        //insert
                        $thisArray=array(
                            'achievementid'=>$adb->getUniqueID('vtiger_achievementsummary'),
                            'invoicecompany'=>$userInfo['invoicecompany'],
                            'departmentid'=>$userInfo['departmentid'],
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
                            'actualroyalty'=>$toElevenAnnualdiscount+$amount,
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
                            'performancetype'=>$performancetype,
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
        $assigner='';
        if(!empty($params['assigner'])){
            $assigner='  AND userid in('.$params['assigner'].')';
        }
        if(in_array($currentMonth,array(3,6,9,12))){
            $managerInfo=$adb->pquery("SELECT * FROM vtiger_usergraderoyalty WHERE usergrade in(9,10,11,23) and staffrank=1".$assigner,array());
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
            $quarterQuery="SELECT sum(realarriveachievement) as sumrealarriveachievement,sum(actualroyalty) as sumactualroyalty FROM vtiger_achievementsummary WHERE achievementtype='newadd' AND performancetype='departmenttype' AND userid=?  AND achievementmonth in('".implode("','",$arraymapData1)."','".$currentDay."')";
            //半年度
            $halfYearQuery="SELECT sum(realarriveachievement) as sumrealarriveachievement,sum(actualroyalty) as sumactualroyalty FROM vtiger_achievementsummary WHERE achievementtype='newadd' AND performancetype='departmenttype' AND userid=? AND achievementmonth in('".implode("','",$arraymapData2)."','".$currentDay."')";//年度
            //当月
            $monthlyQuery="SELECT sum(realarriveachievement) as sumrealarriveachievement,sum(actualroyalty) as sumactualroyalty FROM vtiger_achievementsummary WHERE achievementtype='newadd' AND performancetype='departmenttype' AND userid=? AND achievementmonth='".$currentDay."'";//月度金额

            $achievementsummaryQuery="SELECT * FROM vtiger_achievementsummary WHERE achievementtype='newadd' AND performancetype='departmenttype' AND userid=? AND achievementmonth='".$currentDay."'";
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
                                $userInfo=$this->getUserInfo(array('userid'=>$value,'calculation_year_month'=>$params['calculation_year_month']));;
                                $thisArray=array(
                                    'achievementid'=>$adb->getUniqueID('vtiger_achievementsummary'),
                                    'invoicecompany'=>$userInfo['invoicecompany'],
                                    'departmentid'=>$userInfo['departmentid'],
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
                                    'performancetype'=>'departmenttype',
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
                            $userInfo=$this->getUserInfo(array('userid'=>$value,'calculation_year_month'=>$params['calculation_year_month']));;
                            $thisArray=array(
                                'achievementid'=>$adb->getUniqueID('vtiger_achievementsummary'),
                                'invoicecompany'=>$userInfo['invoicecompany'],
                                'departmentid'=>$userInfo['departmentid'],
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
                                'actualroyalty'=>$quarterroyalty,
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
                                'performancetype'=>'departmenttype',
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
     * 获取下级
     * @param $params
     * @return array
     */
    public function getSubordinateUsers($params){
       global $adb;
       $query='SELECT DISTINCT subordinateid FROM vtiger_useractivemonthnew WHERE userid=? AND activedate=?';
       $result=$adb->pquery($query,array($params['userid'],$params['calculation_year_month']));
       $returData=array();
       while($row=$adb->fetch_array($result)){
           $returData[]=$row['subordinateid'];
       }
       return $returData;
    }

    /**
     * 需要扣除的暂扣20%的业绩提成金额
     * @param $params
     * @return int
     */
    public function subWithholdTwentyAchievement($params){
        global $adb;
        $wtidholdPrecenTwenty=0;
        $query="SELECT * FROM vtiger_achievementallot_statistic WHERE vtiger_achievementallot_statistic.receivedpaymentownid=? AND vtiger_achievementallot_statistic.achievementtype='newadd' AND istwentyroyalty=0 AND isleave=0 AND vtiger_achievementallot_statistic.achievementmonth=? AND arriveachievement>0";
        $result = $adb->pquery($query,array($params['userid'],$params['currentDate']));
        if(!$adb->num_rows($result)){
            return 0;
        }
        $query='SELECT * FROM vtiger_servicecontracts WHERE servicecontractsid=? limit 1';
        $twentyroyaltysql='UPDATE vtiger_achievementallot_statistic SET istwentyroyalty=?,twentyroyalty=?,commissionforrenewal=?,renewal_commission=? WHERE achievementallotid=? AND arriveachievement>0';
        $withholdroyaltysql='INSERT INTO `vtiger_withholdroyalty`(`userid`,`achievementallotid`,`amountofmoney`,`confirmationdate`, `createdtime`, `iscalculate`) VALUES (?, ?, ?, ?, ?, ?)';
        while($row=$adb->fetch_array($result)){
            $serviceResult=$adb->pquery($query,array($row['servicecontractid']));
            $twentyroyalty=bcdiv(bcmul(bcmul($row['arriveachievement'],$params['royaltyratio'],6),20,6),10000,6);
            $arriveachievement=bcdiv(bcmul($row['arriveachievement'],$params['royaltyratio'],6),100,6);
            $istwentyroyalty=0;
            if($this->checkContactClass($serviceResult)){
                if($serviceResult->fields['isfulldelivery']==1){
                    $sql='DELETE FROM vtiger_withholdroyalty WHERE achievementallotid=?';
                    //$adb->pquery($sql,array($row['']));
                    //$adb->pquery($withholdroyaltysql,array($params['userid'],$row['achievementallotid'],$twentyroyalty,$params['currentDate'].'-01',date('Y-m-d H:i:s'),0));
                    $istwentyroyalty=1;
                    $twentyroyalty=0;
                }
                $wtidholdPrecenTwenty=bcadd($wtidholdPrecenTwenty,$twentyroyalty,6);
            }else{
                $istwentyroyalty=1;
            }
            echo $twentyroyaltysql;
            print_r(array($istwentyroyalty,$twentyroyalty,$arriveachievement-$twentyroyalty,$params['royaltyratio'],$row['achievementallotid'],$arriveachievement));
            $adb->pquery($twentyroyaltysql,array($istwentyroyalty,$twentyroyalty,$arriveachievement-$twentyroyalty,$params['royaltyratio'],$row['achievementallotid']));
        }
        return $wtidholdPrecenTwenty;
    }

    /**
     * 客服确认产品交付20%的业绩核算
     */
    public function customerServiceConfirmDelivery($servicecontractid){
        global $adb;
        $query="SELECT * FROM vtiger_achievementallot_statistic WHERE servicecontractid=? AND arriveachievement>0";
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
        if($resultData->fields['contract_type']=='T云WEB版' || $this->checkOrderExist($resultData->fields['servicecontractsid'])){
            if(empty($this->productIds) || empty($this->packageIds)){
                $this->getWithholdTwentyProduct();
            }
            $productid=$resultData->fields['productid'];
            $packageIdsArrs=explode(',',$productid);
            foreach($packageIdsArrs as $value){
                if(in_array($value,$this->packageIds)){
                    return true;
                }
                if(in_array($value,$this->TyunTwentyProduct)){
                    return true;
                }
            }
            if($resultData->fields['productid']==0 || empty($resultData->fields['productid'])){
                $productid=$resultData->fields['extraproductid'];
                $productidArrs=explode(',',$productid);
                foreach($productidArrs as $value){
                    if(in_array($value,$this->productIds)){
                        return true;
                    }
                }
            }
            return false;
        }
        $productid=$resultData->fields['productid'];//非T云类暂扣
        $packageIdsArrs=explode(',',$productid);
        foreach($packageIdsArrs as $value){
            if(in_array($value,$this->NTyunTwentyProduct)){
                return true;
            }
        }

        /*if(in_array($resultData->fields['productid'],array(38,7,126,106,125,105,0,124))){//词霸 宝盟,+,plus,另购项不扣,124——臻采购标准版——套餐，223——臻采购——单品，
            return false;
        }*/
        /*if($resultData->fields['contract_type']=='T云WEB版'){
            return true;
        }*/
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
        $query='SELECT sum(IFNULL(amountofmoney,0)) AS sumamountofmoney FROM vtiger_withholdroyalty  LEFT JOIN vtiger_achievementallot_statistic ON vtiger_achievementallot_statistic.achievementallotid=vtiger_withholdroyalty.achievementallotid WHERE vtiger_achievementallot_statistic.achievementallotid>0 AND userid=? AND left(confirmationdate,7)=? AND arriveachievement>0';
        $result=$adb->pquery($query,array($params['userid'],$params['currentDate']));
        if(0==$adb->num_rows($result)){
            return 0;
        }
        $sumamountofmoney=$result->fields['sumamountofmoney'];
        return $sumamountofmoney>0?$sumamountofmoney:0;
    }
    public function updateAchievementMonth($achieve_statistic_records){
        return;
        global $adb;
        $sql = "select * from vtiger_achievementallot_statistic where achievementallotid in(".implode(',',$achieve_statistic_records).') AND arriveachievement>0';
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
        return;
        global $adb;
        $sql = "select * from vtiger_achievementallot_statistic where achievementallotid in(".implode(',',$achieve_statistic_records).') AND arriveachievement>0';
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
        return ;
        global $adb;
        $sql = "select * from vtiger_achievementallot_statistic where achievementallotid in(".implode(',',$achieve_statistic_records).') AND arriveachievement>0';
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
        $query='SELECT * FROM vtiger_useractivemonthnew WHERE userid='.$params['userid'].' AND activedate="'.$params['activedate'].'" AND `status`=0 AND userid!=subordinateid';
        $result=$adb->run_query_allrecords($query);
        if(count($result)>0){
            return round(bcdiv(array_sum(array_column($result,'shouldworkday')),'21.75',2));
        }
        return 0;
    }
    /**
     * 获取业绩核算月的上个月的业绩
     * @param $params
     * @return int
     */
    public function getLastMonthPerformance($params){
        global $adb;
        $calculation_year_month=$this->getLastMonth($params);
        //上个月是否是经理
        $calculation_year_monthtemp =date("Y-m",strtotime("-1 months",strtotime($params['calculation_year_month'])));//默认的业绩核算月的前一个月
        $query='SELECT 1 FROM vtiger_usergraderoyaltyupdatelog WHERE userid=? AND ismanager=1 AND updatedate=?';
        $result=$adb->pquery($query,array($params['userid'],$calculation_year_monthtemp.'-01'));
        if($adb->num_rows($result)){ //上个月是经理这个月取基数直接
            $calculation_year_month=$params['calculation_year_month'];
        }
        echo $calculation_year_month,'基数核算月份<hr>';
        //$query='SELECT realarriveachievement FROM vtiger_achievementsummary WHERE  achievementtype=\'newadd\' AND userid=? AND achievementmonth=?';
        $query='SELECT sum(if(arriveachievement>0,arriveachievement,0)) as realarriveachievement FROM vtiger_achievementallot_statistic WHERE  (achievementtype=\'newadd\' OR (achievementtype=\'renew\' AND more_years_renew=1)) AND receivedpaymentownid=? AND achievementmonth=? AND isleave=0 AND arriveachievement>0';
        $result=$adb->pquery($query,array($params['userid'],$calculation_year_month));
        $realarriveachievement=0;
        if($adb->num_rows($result)){
            $realarriveachievement=$result->fields['realarriveachievement'];
        }
        if($params['calculation_month']==3 && $calculation_year_month!=$params['calculation_year_month']){//排除3月份入职
            $query='SELECT sum(if(arriveachievement>0,arriveachievement,0)) as realarriveachievement FROM vtiger_achievementallot_statistic WHERE  (achievementtype=\'newadd\' OR (achievementtype=\'renew\' AND more_years_renew=1)) AND receivedpaymentownid=? AND achievementmonth=? AND isleave=0 AND arriveachievement>0';
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
        return $calculation_year_month;
    }

    /**
     * 获取当月业绩之合
     * @param $params
     * @return int
     */
    public function getUserRealAchievement($params){
        global $adb;
        $query='SELECT sum(if(arriveachievement>0,arriveachievement,0)) AS arriveachievement FROM vtiger_achievementallot_statistic WHERE achievementmonth=? AND achievementtype=? AND receivedpaymentownid=? AND arriveachievement>0 ';
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
        $query='SELECT ucitynameid,citynameid FROM vtiger_apanagemanagement LEFT JOIN vtiger_amanagementrelate ON vtiger_apanagemanagement.userid=vtiger_amanagementrelate.userid WHERE vtiger_apanagemanagement.userid=?';
        $result=$adb->pquery($query,array($userid));
        $regionalLevel=$result->fields['ucitynameid']>0?$result->fields['ucitynameid']:$result->fields['citynameid'];
        return !empty($regionalLevel)?$regionalLevel:1;
    }

    /**
     * 获取总监级别
     *
     * @param $userId
     */
    public function getCommissionerGradeLevel($userId){
        global $adb;
        $sql = "select * from vtiger_usergraderoyalty where userid=?";
        $result = $adb->pquery($sql,array($userId));
        $gradeName = $result->fields['gradename'];
        switch ($gradeName){
            case "商务总监":
                $commissionerKey=1;
                break;
            case "高级总监":
                $commissionerKey=2;
                break;
            case "资深总监":
                $commissionerKey=3;
                break;
        }
        return $this->commissionerLevel[$commissionerKey];
    }

    public function commissionerRoyalty($userId,$realarriveachievement){
        $realarriveachievement = strval($realarriveachievement);
        $commissionerGradeLevel = $this->getCommissionerGradeLevel($userId);
        if(bccomp($realarriveachievement,strval($commissionerGradeLevel['limitAmount']),6)!='1'){
            return 0;
        }
        $levels = $commissionerGradeLevel['level'];
        $ratios = $commissionerGradeLevel['ratio'];
        krsort($levels);
        foreach ($levels as $key=>$level){
            if(bccomp($realarriveachievement,strval($key),6)=='1'){
                return bcadd($level,bcmul(bcsub($realarriveachievement,$key,6),$ratios[$key],6),6);
            }
        }
        return 0;
    }

    /**
     * 总监的新单提成
     * @param $param
     * @throws Exception
     */
    public function calulateCommissionerNewCommission($param){
        global $adb;
        $subordinate_users=$this->subordinateUsers;
        $userIds=$subordinate_users[$param['userid']];
        $param['userids']=explode(",",$param['userids']);
        print_r(array($param['userid']=>$userIds));
        $adb->pquery("DELETE FROM vtiger_achievementsummary WHERE achievementmonth=? AND userid=?",array($param['calculation_year_month'],$param['userid']));
        $queryperson="SELECT sum(a.arriveachievement) AS arriveachievement,sum(if(a.achievementtype = 'renew' AND a.more_years_renew = 1,0,a.unit_price)) AS unit_price,sum(if(a.achievementtype = 'renew' AND a.more_years_renew = 1,0,a.effectiverefund)) AS effectiverefund FROM `vtiger_achievementallot_statistic` AS a WHERE a.receivedpaymentownid =? AND a.achievementmonth =? AND ((a.achievementtype = 'renew' AND a.more_years_renew = 1) OR a.achievementtype = 'newadd') AND arriveachievement>0 LIMIT 1";
        $resultperson=$adb->pquery($queryperson,array($param['userid'],$param['calculation_year_month']));
        if(empty($userIds) && $adb->num_rows($resultperson)==0){
            return ;
        }
        $userIds[] = $param['userid'];
        $persionarriveachievement=$resultperson->fields['arriveachievement'];
        $persionunit_price=$resultperson->fields['unit_price'];
        $persioneffectiverefund=$resultperson->fields['effectiverefund'];
        $proportionofyears=0;
//        $proportionofyears=$this->proportionOfSingleYear($userIds,$param['calculation_year_month']);
//        $regionalLevel=$this->getRegionalLevel($param['userid']);//获取等级
//        $areaManagerRoyaltyRatio=$this->areaManagerRoyaltyRatio[$regionalLevel];
//        $managerBaseSalary=$areaManagerRoyaltyRatio['managerBaseSalary'];
//        $baseAmountRatio=$areaManagerRoyaltyRatio['baseAmountRatio'];
//        $userIdsArray=$userIds;
        //$userIds=" userid IN (".implode(",",$userIds).")";
        $userIds=" a.receivedpaymentownid IN (".implode(",",$userIds).")";
        //汇总该经理所有下属员工的到账业绩  回款金额


        $sql="SELECT sum(a.arriveachievement) AS realarriveachievement,sum(if(a.achievementtype = 'renew' AND a.more_years_renew = 1,0,a.unit_price)) AS unit_price,sum(if(a.achievementtype = 'renew' AND a.more_years_renew = 1,0,a.effectiverefund)) AS effectiverefund FROM `vtiger_achievementallot_statistic` AS a WHERE ".$userIds." AND a.achievementmonth =? AND ((a.achievementtype = 'renew' AND a.more_years_renew = 1) OR a.achievementtype = 'newadd') AND arriveachievement>0 LIMIT 1";

        //$sql=" SELECT SUM(realarriveachievement) as realarriveachievement,SUM(arriveachievement) as arriveachievement,SUM(unit_price) as unit_price,SUM(effectiverefund) as effectiverefund FROM `vtiger_achievementsummary` WHERE ".$userIds." AND achievementmonth=? AND achievementtype='newadd' LIMIT 1 ";
        $result=$adb->pquery($sql,array($param['calculation_year_month']));
        echo $sql,$param['calculation_year_month'];
        echo '<hr>';
        $data=$adb->query_result_rowdata($result,0);
        $money=0;
//        if($proportionofyears){
            //$money=$this->getAllNewaddArriveachievement($userIdsArray,$param['calculation_year_month'],'newadd');//多年单的第一年业绩
            //$data['realarriveachievement']=$data['realarriveachievement']-$money;//减掉多年单的第一年业绩
            //$money+=$this->getAllNewaddArriveachievement($userIdsArray,$param['calculation_year_month'],'renew');//多年单的续费业绩
//        }
        echo '下属的业绩之和'.$data['realarriveachievement'],'<hr>';
        //$data['realarriveachievement']+=$persionarriveachievement;
        echo '总监的个人业绩',$persionarriveachievement;
        echo '<hr>';
        echo '下属的业绩+总监的业绩之和',$data['realarriveachievement'];
        echo '<hr>';


        $royalty = $this->commissionerRoyalty($param['userid'],$data['realarriveachievement']);
        $sql=" SELECT vtiger_users.invoicecompany,vtiger_user2department.departmentid FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id  WHERE vtiger_users.id=? ";
        $userInfo=$adb->pquery($sql,array($param['userid']));
        $userInfo=$adb->query_result_rowdata($userInfo,0);

//        $annualdiscount=$royalty>0?bcdiv(bcmul($data['realarriveachievement'],2,6),1000,6):0;// 年度折扣
//        // 如果计算提成的月份是12月份 则计算年度发放 否则 年度发放为0
//        if($param['calculation_month']==12){
//            $annualpayment=$annualdiscount+$this->getOneToElevenAnnualdiscount($param);
//        }else{
//            $annualpayment=0;
//        }
        //年度发放暂时为0
        $annualdiscount=0;
        $annualpayment=0;

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
        //print_r(array($params['invoicecompany'],$params['departmentid'],$params['userid'],$params['unit_price'],$params['arriveachievement'],$params['realarriveachievement'],$params['effectiverefund'],$params['achievementmonth'],date("Y-m-d H:i:s"),0,$param['gradename'],$params['royalty'],$params['actualroyalty'],$param['achievementtype'],$params['confirmstatus'],$params['modulestatus'],$params['annualdiscount'],$params['annualpayment'],$params['proportionofyears'],$params['myearachievement']));
        echo '<hr>';
        $adb->pquery($sql,array($params['invoicecompany'],$params['departmentid'],$params['userid'],$params['unit_price'],$params['arriveachievement'],$params['realarriveachievement'],$params['effectiverefund'],$params['achievementmonth'],date("Y-m-d H:i:s"),0,$param['gradename'],$params['royalty'],$params['actualroyalty'],$param['achievementtype'],$params['confirmstatus'],$params['modulestatus'],$params['annualdiscount'],$params['annualpayment'],$params['proportionofyears'],$params['myearachievement']));
        //总监的个人新单业绩


        if($adb->num_rows($result)==0){
            return ;
        }

        $params['invoicecompany']=$userInfo['invoicecompany'];
        $params['departmentid']=$userInfo['departmentid'];
        $params['userid']=$param['userid'];
        $params['unit_price']=$persionunit_price;
        $params['arriveachievement']=$persionarriveachievement;
        $params['adjustachievement']=0;
        $params['realarriveachievement']=$persionarriveachievement;
        $params['annualdiscount']=0;// 年度折扣
        $params['annualpayment']=0;// 年度发放
        $params['royalty']=$persionarriveachievement*0.1;// 提成
        $params['actualroyalty']=$persionarriveachievement*0.1;// 实际提成=提成+年度发放
        $params['effectiverefund']=$persioneffectiverefund;
        $params['achievementmonth']=$param['calculation_year_month'];
        $params['confirmstatus']='tobeconfirm';
        $params['modulestatus']='a_normal';
        $params['proportionofyears']=0;
        $params['myearachievement']=0;
        $params['performancetype']='mpersontype';
        $id=$adb->getUniqueID('vtiger_achievementsummary');
        $sql="INSERT INTO `vtiger_achievementsummary` (achievementid,`invoicecompany`, `departmentid`, `userid`, `unit_price`, `arriveachievement`, `realarriveachievement`, `effectiverefund`, `achievementmonth`, `createtime`, `adjustachievement`, `employeelevel`, `royalty`, `actualroyalty`, `achievementtype`, `confirmstatus`, `modulestatus`,`annualdiscount`, `annualpayment`, `proportionofyears`,myearachievement,remarks,performancetype) 
                       VALUES (".$id.",?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        echo '总监的个人业绩';
        print_r(array($params['invoicecompany'],$params['departmentid'],$params['userid'],$params['unit_price'],$params['arriveachievement'],$params['realarriveachievement'],$params['effectiverefund'],$params['achievementmonth'],date("Y-m-d H:i:s"),0,$param['gradename'],$params['royalty'],$params['actualroyalty'],$param['achievementtype'],$params['confirmstatus'],$params['modulestatus'],$params['annualdiscount'],$params['annualpayment'],$params['proportionofyears'],$params['myearachievement']));
        echo '<hr>';
        $adb->pquery($sql,array($params['invoicecompany'],$params['departmentid'],$params['userid'],$params['unit_price'],$params['arriveachievement'],$params['realarriveachievement'],$params['effectiverefund'],$params['achievementmonth'],date("Y-m-d H:i:s"),0,$param['gradename'],$params['royalty'],$params['actualroyalty'],$param['achievementtype'],$params['confirmstatus'],$params['modulestatus'],$params['annualdiscount'],$params['annualpayment'],$params['proportionofyears'],$params['myearachievement'],'总监个人业绩','mpersontype'));
    }
    /**
     * 获取71360暂扣的产品
     */
    public function getWithholdTwentyProduct(){
        global $tyunweburl,$sault;
        $url=$tyunweburl.'api/micro/order-basic/v1.0.0/api/Product/GetWebSiteProducts';
        //$url='http://pretyapi.71360.com/api/micro/order-basic/v1.0.0/api/Product/GetWebSiteProducts';
        $time=time().'123';
        $sault1=$time.$sault;
        $token=md5($sault1);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $data=$this->https_requestcomm($url,NULL,$curlset);
        $jsonData=json_decode($data,true);
        if($jsonData['success']){
            $this->productIds=$jsonData['data']['productIds'];
            $this->packageIds=$jsonData['data']['packageIds'];
        }
        return $data;
    }
    public function getUserActiveInfo($param){
        /*global $adb;
        $sql='SELECT vtiger_users.invoicecompany,vtiger_user2department.departmentid FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id  WHERE vtiger_users.id=?';
        $userInfo=$adb->pquery($sql,array($param['userid']));
        $userInfo=$adb->query_result_rowdata($userInfo,0);
        $query='SELECT departmentid FROM vtiger_useractivemonthnew WHERE userid=subordinateid AND userid=? AND activedate=?';
        $result=$adb->pquery($query,array());*/

    }

    /**
     * 部门离职员工的暂扣。作为部门经费
     * @param $userid
     * @param $activeMonth
     * @return int
     */
    public function getManagerGrantdetain($userid,$activeMonth){
        global $adb;
        $query='SELECT sum(if(vtiger_withholdroyalty.amountofmoney>0,vtiger_withholdroyalty.amountofmoney,0)) as sumamountofmoney FROM vtiger_withholdroyalty LEFT JOIN vtiger_useractivemonthnew ON vtiger_useractivemonthnew.subordinateid=vtiger_withholdroyalty.userid 
                WHERE left(vtiger_withholdroyalty.confirmationdate,7)=vtiger_useractivemonthnew.activedate AND `status`=1 AND  vtiger_useractivemonthnew.userid=? AND left(vtiger_withholdroyalty.confirmationdate,7)=?';
        $result=$adb->pquery($query,array($userid,$activeMonth));
        $sumamountofmoney=0;
        if($adb->num_rows($result)){
            $sumamountofmoney=$result->fields['sumamountofmoney'];
        }
        return $sumamountofmoney;
    }

    /**
     * 获取当前用户的所属公司，业绩月所在的部门，当前的所在部门
     * @param $param
     * @return array
     * @throws Exception
     */
    public function getUserInfo($param){
        global $adb;
        $sql="SELECT
                    vtiger_users.invoicecompany,
                    vtiger_user2department.departmentid AS departmentid2,
                    if(vtiger_useractivemonthnew.departmentid is null,vtiger_user2department.departmentid,vtiger_useractivemonthnew.departmentid) AS departmentid 
            FROM
                vtiger_users
            LEFT JOIN vtiger_user2department ON vtiger_user2department.userid = vtiger_users.id
            left join vtiger_useractivemonthnew ON (vtiger_useractivemonthnew.userid=vtiger_users.id AND vtiger_useractivemonthnew.userid=vtiger_useractivemonthnew.subordinateid AND vtiger_useractivemonthnew.activedate=?)
            WHERE
                vtiger_users.id =?";
        $userInfo=$adb->pquery($sql,array($param['calculation_year_month'],$param['userid']));
        return $adb->query_result_rowdata($userInfo,0);
    }

    /**
     * 调整金额
     * @param $params
     * @return int
     */
    public function adjustmentAchievement($params){
        global $adb;
        $query="SELECT sum(IFNULL(vtiger_achievementsupdate.uroyalty,0)) as sumamount FROM `vtiger_achievementsupdate` WHERE uuserid=? AND uachievementmonth=? AND uachievementtype=? AND uperformancetype=? AND deleted=0";
        $result=$adb->pquery($query,array($params['uuserid'],$params['uachievementmonth'],$params['uachievementtype'],$params['uperformancetype']));
        $sumamount=0;
        if($adb->num_rows($result)){
            $sumamount=$result->fields['sumamount'];
        }
        return $sumamount;
    }

    /**
     * 员工提成
     */
    public function calulateEmployee($params){
        global $adb;
        $calculation_year_month =$params['achievementmonth'];
        $calculation_month=date("m",strtotime($calculation_year_month));
        $calculation_year=date("Y",strtotime($calculation_year_month));
        $uidstring=' AND ugr.userid='.$params['userid'];
        //$query=" SELECT a.achievementid,a.userid,a.achievementtype,a.realarriveachievement,ugr.staffrank,ugr.usergrade,ugr.newmanagersixmonths,ugr.gradename FROM vtiger_achievementsummary as a LEFT JOIN vtiger_usergraderoyalty as ugr  ON ugr.userid=a.userid WHERE  ugr.usergrade>0 AND ugr.staffrank=0 and a.achievementmonth='".$calculation_year_month."'".$uidstring;
        $query="SELECT 0 as achievementid,0 as userid,0 AS achievementtype,0 as realarriveachievement,ugr.staffrank,ugr.usergrade,ugr.newmanagersixmonths,ugr.gradename FROM vtiger_usergraderoyalty as ugr  WHERE  ugr.usergrade>0 AND ugr.staffrank=0 ".$uidstring;
        $result = $adb->run_query_allrecords($query);
        $param['calculation_year_month']=$calculation_year_month;
        $param['calculation_year']=$calculation_year;
        $param['calculation_month']=$calculation_month;
        $adb->pquery("DELETE FROM vtiger_achievementsummary WHERE achievementmonth=? AND  userid IN(".$params['userid'].")",array($calculation_year_month));
        foreach($result as $row){
            $param['achievementid']=$row['achievementid'];
            $param['userid']=$row['userid'];
            $param['achievementtype']=$row['achievementtype'];
            $param['staffrank']=$row['staffrank'];
            $param['usergrade']=$row['usergrade'];
            $param['gradename']=$row['gradename'];
            $param['newmanagersixmonths']=$row['newmanagersixmonths'];
            $param['realarriveachievement']=$row['realarriveachievement'];

            $param['achievementtype']='newadd';
            $this->calulateEmployeeNewCommission($param);

            $param['achievementtype']='renew';
            $this->calulateEmployeeRenewCommission($param);
        }
    }

    /**
     * 经理的提成核算
     * @param $params
     * @throws Exception
     */
    public function calulateManager($params){
        global $adb;
        $calculation_year_month =$params['achievementmonth'];
        $calculation_month=date("m",strtotime($calculation_year_month));
        $calculation_year=date("Y",strtotime($calculation_year_month));
        $param['calculation_year_month']=$calculation_year_month;
        $param['calculation_year']=$calculation_year;
        $param['calculation_month']=$calculation_month;
        $uidstringSQL=' AND userid IN('.$params['userids'].')';
        //查询所有的经理
        $managerInfo=$adb->pquery("SELECT * FROM vtiger_usergraderoyalty  WHERE staffrank=1  AND usergrade in(9,10,11,23)".$uidstringSQL,array());
        $managerInfoArray=array();
        $managerId=array();
        while($rowdata=$adb->fetch_array($managerInfo)){
            $managerId[]=$rowdata['userid'];
            $managerInfoArray[]=array("userid"=>$rowdata['userid'],"staffrank"=>$rowdata['staffrank'],"usergrade"=>$rowdata['usergrade'],"newmanagersixmonths"=>$rowdata['newmanagersixmonths'],"gradename"=>$rowdata['gradename']);
        }
        //计算提成前删除要计算的月份的经理级别的汇总
        $adb->pquery("DELETE FROM vtiger_achievementsummary WHERE achievementmonth=? AND  userid IN(".implode(',',$managerId).")",array($calculation_year_month));
        //经理的有下属的提成计算
        foreach ($managerInfoArray as $key=>$value){
            $incumbencyNumber=$this->getIncumbencyNumber(array('userid'=>$value['userid'],'activedate'=>$calculation_year_month));
            $param['achievementid']=0;
            $param['userid']=$value['userid'];
            $param['staffrank']=$value['staffrank'];
            $param['usergrade']=$value['usergrade'];
            $param['gradename']=$value['gradename'];
            $param['newmanagersixmonths']=$value['newmanagersixmonths'];
            $param['incumbencynumber']=$incumbencyNumber;//业绩月在职下属人数
            $param['realarriveachievement']=0;
            //新单提成的计算
            $param['achievementtype']='newadd';
            $this->calulateManagerNewCommission($param);
            //续费提成的计算
            $param['achievementtype']='renew';
            $this->calulateManagerReNewCommission($param);
        }
    }

    /**
     * 更新业绩月的部门
     * @param $calculation_year_month
     */
    public function updateUserDepartment($calculation_year_month){
        global $adb;
        $query='UPDATE vtiger_achievementsummary SET vtiger_achievementsummary.departmentid=(select vtiger_useractivemonthnew.departmentid from vtiger_useractivemonthnew WHERE vtiger_useractivemonthnew.userid=vtiger_useractivemonthnew.subordinateid AND vtiger_useractivemonthnew.userid=vtiger_achievementsummary.userid AND vtiger_useractivemonthnew.activedate=vtiger_achievementsummary.achievementmonth limit 1) WHERE vtiger_achievementsummary.achievementmonth=?';
        $adb->pquery($query,array($calculation_year_month));
        $adb->pquery('UPDATE vtiger_achievementsummary SET userfullname=(SELECT last_name FROM vtiger_users WHERE id=vtiger_achievementsummary.userid LIMIT 1) WHERE achievementmonth=?',array($calculation_year_month));
    }
    /**
     * 是否存在订单
     */
    public function checkOrderExist($contractid){
        global $adb;
        $result=$adb->pquery('SELECT 1 FROM vtiger_activationcode WHERE `status` in(0,1) AND contractid=?',array($contractid));
        if($adb->num_rows($result)){
            return true;
        }
        return false;
    }
    public function getUserArriveAchievement($params){
        global $adb;
        $query='SELECT sum(vtiger_achievementallot_statistic.unit_price) AS unit_price
                ,sum(vtiger_achievementallot_statistic.effectiverefund) AS effectiverefund
                ,sum(vtiger_achievementallot_statistic.arriveachievement) AS arriveachievement
                 FROM vtiger_achievementallot_statistic 
                WHERE vtiger_achievementallot_statistic.receivedpaymentownid=? AND vtiger_achievementallot_statistic.achievementmonth =? AND vtiger_achievementallot_statistic.achievementtype=? AND vtiger_achievementallot_statistic.isleave=0 AND arriveachievement>0';
        echo $query;
        print_r($params);
        $result=$adb->pquery($query,array($params['userid'],$params['calculation_year_month'],$params['achievementtype']));
        $returnData=array();
        if($adb->num_rows($result)){
            $returnData=$result->fields;
        }
        return $returnData;
    }
    public function insertrArriveAchievementData($params){
        $keyarray=array_keys($params);
        $placeholder=array_map(function($v){return '?';},$keyarray);
        $sql='INSERT INTO `vtiger_achievementsummary` ('.implode(',',$keyarray).')VALUES('.implode(',',$placeholder).')';
        return $sql;
    }

}
