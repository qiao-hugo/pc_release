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
class SalesDaily_Record_Model extends Vtiger_Record_Model {

	public function getVisitOrderDayList($arr){
        $db=PearDatabase::getInstance();
        $query='SELECT vtiger_account.accountid,vtiger_visitingorder.visitingorderid,vtiger_account.accountname,vtiger_account.leadsource,vtiger_visitingorder.contacts,if(vtiger_account.linkname=vtiger_visitingorder.contacts,vtiger_account.title,(SELECT vtiger_contactdetails.`title` FROM vtiger_contactdetails WHERE vtiger_account.accountid=vtiger_contactdetails.accountid AND vtiger_contactdetails.`name`=vtiger_visitingorder.contacts)) AS title,if(vtiger_account.linkname=vtiger_visitingorder.contacts,vtiger_account.mobile,(SELECT vtiger_contactdetails.`mobile` FROM vtiger_contactdetails WHERE vtiger_account.accountid=vtiger_contactdetails.accountid AND vtiger_contactdetails.`name`=vtiger_visitingorder.contacts)) AS mobile,vtiger_visitingorder.startdate FROM vtiger_account LEFT JOIN vtiger_crmentity ON vtiger_account.accountid=vtiger_crmentity.crmid LEFT JOIN vtiger_visitingorder ON vtiger_account.accountid=vtiger_visitingorder.related_to WHERE vtiger_account.accountrank=\'forp_notv\' AND vtiger_visitingorder.modulestatus=\'c_complete\' AND vtiger_crmentity.smownerid=? AND vtiger_crmentity.deleted=0 AND left(vtiger_visitingorder.workflowstime,10)=?';
        $result=$db->pquery($query,$arr);

        $arrtemp=array();
        while($rawData=$db->fetch_array($result)){
            $rawData['id']=$rawData['accountid'];
            $arrtemp[$rawData['accountid']]=$rawData;
        }
        return $arrtemp;
    }
    public function getCandealAccounts(){

        global $adb,$current_user;
        $query='SELECT vtiger_account.accountid,vtiger_account.accountname FROM vtiger_account LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid WHERE vtiger_crmentity.deleted=0 AND vtiger_account.accountcategory=0 AND vtiger_crmentity.smownerid=?';
        $result=$adb->pquery($query,array($current_user->id));

        $arrtemp=array();
        while($rawData=$adb->fetch_array($result)){
            $arrtemp['account'][$rawData['accountid']]=$rawData['accountname'];
        }
        $query='SELECT parentdepartment FROM `vtiger_departments` WHERE departmentid=?';
        $result=$adb->pquery($query,array($current_user->column_fields['departmentid']));
        $resultdata=$adb->query_result_rowdata($result,0);
        $otherpriceflag=1;
        $marketpricefalst=$resultdata['parentdepartment'].'::';
        //是否是上海,深圳的商务
        if(strpos($marketpricefalst,'H72::')===false && strpos($marketpricefalst,'H246::')===false){
            $otherpriceflag=2;
        }

        $query='SELECT vtiger_products.productid,vtiger_products.productname,vtiger_products.unit_price,vtiger_products.tranperformance,vtiger_products.otherunit_price FROM vtiger_products LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_products.productid WHERE vtiger_crmentity.deleted=0 AND vtiger_products.salesdailyshow=1 ORDER BY salesdailysort';
        $result=$adb->pquery($query,array());
        while($rawData=$adb->fetch_array($result)){
            $arrtemp['product'][$rawData['productid']]['id']=$rawData['productid'];
            $arrtemp['product'][$rawData['productid']]['name']=$rawData['productname'];
            $arrtemp['product'][$rawData['productid']]['marketprice']=$otherpriceflag==1?$rawData['unit_price']:$rawData['otherunit_price'];
            $arrtemp['product'][$rawData['productid']]['performance']=$rawData['tranperformance'];
        }
        return $arrtemp;
    }

    /**
     * 日报详情列表
     * @param $id
     * @return array
     */
    public function getDetailList($id){
        global $adb;
        $query='SELECT vtiger_salesdailyfournotv.*,vtiger_account.commentcontent FROM vtiger_salesdailyfournotv left join vtiger_account on vtiger_salesdailyfournotv.accountid=vtiger_account.accountid WHERE salesdailybasicid=?';
        $result=$adb->pquery($query,array($id));

        $arrtemp=array();
        while($rawData=$adb->fetch_array($result)){
            $arrtemp['foutnotv'][]=$rawData;
        }
        $query='SELECT vtiger_salesdailycandeal.*,vtiger_account.accountname FROM vtiger_salesdailycandeal LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_salesdailycandeal.accountid WHERE vtiger_salesdailycandeal.isupdatedeleted=0 AND salesdailybasicid=?';
        $result=$adb->pquery($query,array($id));

        while($rawData=$adb->fetch_array($result)){
            $rawData['datacolor']=$rawData['deleted']==1?' style="color:#b94a48;"':($rawData['issigncontract']==1?' style="color:#468847;"':'');
            $rawData['issigncontract']=$rawData['issigncontract']==0?'否':'是';
            $arrtemp['candeal'][]=$rawData;
        }
        $query='SELECT * FROM vtiger_salesdailynextdayvisit WHERE salesdailybasicid=?';
        $result=$adb->pquery($query,array($id));


        while($rawData=$adb->fetch_array($result)){
            $rawData['isvisitor']=empty($rawData['withvisitor'])?'没有':'有';
            $arrtemp['nextdayvisit'][]=$rawData;
        }
        $query='SELECT vtiger_salesdailydaydeal.*,vtiger_account.accountname,vtiger_products.productname FROM vtiger_salesdailydaydeal LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_salesdailydaydeal.accountid LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_salesdailydaydeal.productid WHERE vtiger_salesdailydaydeal.deleted=0 and salesdailybasicid=?';
        $result=$adb->pquery($query,array($id));


        while($rawData=$adb->fetch_array($result)){
            $rawData['oldcustomers']=$rawData['oldcustomers']==1?'是':'否';
            $rawData['allamount']=$rawData['allamount']==1?'是':'否';
            $rawData['paymentnature']=$rawData['paymentnature']=='firstpaymentnature'?'首付款':($rawData['paymentnature']=='lastpaymentnature'?'尾款':'');
            $rawData['isvisitor']=empty($rawData['withvisitor'])?'没有':'有';
            $rawData['discount']=$rawData['marketprice']==0?'':($rawData['dealamount']==0?0:(($rawData['dealamount']/$rawData['marketprice']*10)>=10?'不打折':number_format($rawData['dealamount']/$rawData['marketprice']*10,1).'折'));
            $arrtemp['daydeal'][]=$rawData;
        }
        /*echo "<pre>";
        print_r($arrtemp['nextdayvisit']);
        exit;*/
        return $arrtemp;
    }

    /**
     * 前一天可能成交的客户;
     */
    public function getPrevCanDeal(){
        global $adb,$current_user;
        $query='SELECT salesdailybasicid FROM vtiger_salesdaily_basic WHERE smownerid=? ORDER BY salesdailybasicid DESC LIMIT 1';
        $result=$adb->pquery($query,array($current_user->id));
        $arrtemp['candeal']=array();
        $arrtemp['foutnotv']=array();
        $arrtemp['daydeal']=array();
        $arrtemp['nextdayvisit']=array();
        do{
            if($adb->num_rows($result)==0){
                break;
            }
            $rowdata=$adb->query_result_rowdata($result,0);
            $query='SELECT vtiger_salesdailycandeal.*,vtiger_account.accountname,vtiger_account.commentcontent FROM vtiger_salesdailycandeal LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_salesdailycandeal.accountid WHERE vtiger_salesdailycandeal.deleted=0 AND vtiger_salesdailycandeal.issigncontract=0 AND vtiger_salesdailycandeal.salesdailybasicid=?';
            $result=$adb->pquery($query,array($rowdata['salesdailybasicid']));


            while($rawData=$adb->fetch_array($result)){
                $arrtemp['candeal'][]=$rawData;
            }

        }while(0);
       return $arrtemp;
    }
    /**
     * 日报修改列表
     * @param $id
     * @return array
     */
    public function getEditlList($id){
        global $adb;
        $query='SELECT * FROM vtiger_salesdailyfournotv WHERE salesdailybasicid=?';
        $result=$adb->pquery($query,array($id));

        $arrtemp=array();
        while($rawData=$adb->fetch_array($result)){
            $arrtemp['foutnotv'][]=$rawData;
        }
        $query='SELECT vtiger_salesdailycandeal.*,vtiger_account.accountname FROM vtiger_salesdailycandeal LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_salesdailycandeal.accountid WHERE salesdailybasicid=?';
        $result=$adb->pquery($query,array($id));


        while($rawData=$adb->fetch_array($result)){
            $arrtemp['candeal'][]=$rawData;
        }
        $query='SELECT * FROM vtiger_salesdailynextdayvisit WHERE salesdailybasicid=?';
        $result=$adb->pquery($query,array($id));


        while($rawData=$adb->fetch_array($result)){
            $rawData['isvisitor']=empty($rawData['withvisitor'])?'没有':'有';
            $arrtemp['nextdayvisit'][]=$rawData;
        }
        $query='SELECT vtiger_salesdailydaydeal.*,vtiger_account.accountname,vtiger_products.productname FROM vtiger_salesdailydaydeal LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_salesdailydaydeal.accountid LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_salesdailydaydeal.productid WHERE salesdailybasicid=?';
        $result=$adb->pquery($query,array($id));


        while($rawData=$adb->fetch_array($result)){
            $rawData['oldcustomers']=$rawData['oldcustomers']==1?'是':'否';
            $rawData['allamount']=$rawData['allamount']==1?'是':'否';
            //$rawData['paymentnature']=$rawData['paymentnature']=='firstpaymentnature'?'首付款':($rawData['paymentnature']=='lastpaymentnature'?'尾款':'');
            $rawData['isvisitor']=empty($rawData['withvisitor'])?'没有':'有';
            $rawData['discount']=$rawData['marketprice']==0?'':($rawData['dealamount']==0?0:(($rawData['dealamount']/$rawData['marketprice']*10)>=10?'不打折':number_format($rawData['dealamount']/$rawData['marketprice']*10,1).'折'));
            $arrtemp['daydeal'][]=$rawData;
        }
        /*echo "<pre>";
        print_r($arrtemp['nextdayvisit']);
        exit;*/
        return $arrtemp;
    }
    public function getCurrentMonth($arr){
        global $adb;
        $datetime=substr($arr['dailydatetime'],0,7);

        $query='SELECT * FROM `vtiger_salesdaily_basic` WHERE left(dailydatetime,7)=? AND smownerid=? ORDER BY salesdailybasicid';
        $result=$adb->pquery($query,array($datetime,$arr['smownerid']));

        $arrtemp=array();
        while($rawData=$adb->fetch_array($result)){
            $temp['start']=$rawData['dailydatetime'];
            $temp['title']=$rawData['dailydatetime'];
            $temp['url']='/index.php?module=SalesDaily&view=Detail&record='.$rawData['salesdailybasicid'];
            $temp['id']=$rawData['salesdailybasicid'];
            $arrtemp[]=$temp;
        }

        return json_encode($arrtemp);
    }

    /**
     * 获取负责人当月的用详情
     */
    public function getCurrentMonthList($params){
        global $adb;
        $userid=$params['smownerid'];
        $datemonth=substr($params['dailydatetime'],0,7);
        $query='SELECT vtiger_salesdailyfournotv.*,vtiger_salesdaily_basic.dailydatetime FROM vtiger_salesdailyfournotv LEFT JOIN vtiger_salesdaily_basic ON vtiger_salesdaily_basic.salesdailybasicid=vtiger_salesdailyfournotv.salesdailybasicid
                WHERE LEFT(vtiger_salesdaily_basic.dailydatetime,7)=? AND vtiger_salesdaily_basic.smownerid=? ORDER BY vtiger_salesdaily_basic.dailydatetime DESC';
        $result=$adb->pquery($query,array($datemonth,$userid));

        $arrtemp=array();
        while($rawData=$adb->fetch_array($result)){
            $arrtemp['foutnotv'][]=$rawData;
        }
        $query='SELECT vtiger_salesdailycandeal.*,vtiger_account.accountname,vtiger_salesdaily_basic.dailydatetime FROM vtiger_salesdailycandeal LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_salesdailycandeal.accountid LEFT JOIN vtiger_salesdaily_basic ON vtiger_salesdaily_basic.salesdailybasicid=vtiger_salesdailycandeal.salesdailybasicid
                WHERE vtiger_salesdailycandeal.isupdatedeleted=0 AND LEFT(vtiger_salesdaily_basic.dailydatetime,7)=? AND vtiger_salesdaily_basic.smownerid=? GROUP BY vtiger_salesdailycandeal.accountid,vtiger_salesdaily_basic.smownerid ORDER BY vtiger_salesdaily_basic.dailydatetime DESC';
        $result=$adb->pquery($query,array($datemonth,$userid));

        while($rawData=$adb->fetch_array($result)){
            $rawData['datacolor']=$rawData['deleted']==1?' style="color:#b94a48;"':($rawData['issigncontract']==1?' style="color:#468847;"':'');
            $rawData['issigncontract']=$rawData['issigncontract']==0?'否':'是';
            $arrtemp['candeal'][]=$rawData;
        }
        $query='SELECT vtiger_salesdailynextdayvisit.*,vtiger_salesdaily_basic.dailydatetime FROM vtiger_salesdailynextdayvisit LEFT JOIN vtiger_salesdaily_basic ON vtiger_salesdaily_basic.salesdailybasicid=vtiger_salesdailynextdayvisit.salesdailybasicid
                WHERE  LEFT(vtiger_salesdaily_basic.dailydatetime,7)=? AND vtiger_salesdaily_basic.smownerid=? ORDER BY vtiger_salesdaily_basic.dailydatetime DESC';
        $result=$adb->pquery($query,array($datemonth,$userid));


        while($rawData=$adb->fetch_array($result)){
            $rawData['isvisitor']=empty($rawData['withvisitor'])?'没有':'有';
            $arrtemp['nextdayvisit'][]=$rawData;
        }
        $query='SELECT vtiger_salesdailydaydeal.*,vtiger_account.accountname,vtiger_products.productname,vtiger_salesdaily_basic.dailydatetime FROM vtiger_salesdailydaydeal LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_salesdailydaydeal.accountid LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_salesdailydaydeal.productid LEFT JOIN vtiger_salesdaily_basic ON vtiger_salesdaily_basic.salesdailybasicid=vtiger_salesdailydaydeal.salesdailybasicid
                WHERE vtiger_salesdailydaydeal.deleted=0 AND LEFT(vtiger_salesdaily_basic.dailydatetime,7)=? AND vtiger_salesdaily_basic.smownerid=? ORDER BY vtiger_salesdaily_basic.dailydatetime DESC';
        $result=$adb->pquery($query,array($datemonth,$userid));

        $arrivalamount=0;
        while($rawData=$adb->fetch_array($result)){
            $rawData['oldcustomers']=$rawData['oldcustomers']==1?'是':'否';
            $rawData['allamount']=$rawData['allamount']==1?'是':'否';
            $rawData['paymentnature']=$rawData['paymentnature']=='firstpaymentnature'?'首付款':($rawData['paymentnature']=='lastpaymentnature'?'尾款':'');
            $rawData['isvisitor']=empty($rawData['withvisitor'])?'没有':'有';
            $rawData['discount']=$rawData['marketprice']==0?'':($rawData['dealamount']==0?0:(($rawData['dealamount']/$rawData['marketprice']*10)>=10?'不打折':number_format($rawData['dealamount']/$rawData['marketprice']*10,1).'折'));
            $arrtemp['daydeal'][]=$rawData;
            $arrivalamount+=$rawData['arrivalamount'];
        }
        $arrtemp['daydealarrivalamount']=$arrivalamount;
        return $arrtemp;
    }
    /**
     * 获取部门当月的用详情
     */
    public function getDepartmentMonthList(Vtiger_Request $request){
        global $adb;
        $searchDepartment = $_REQUEST['department'];//部门
        $listQuery='';
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('SalesDaily','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $listQuery .= ' and vtiger_salesdaily_basic.smownerid in ('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                $listQuery .= ' and vtiger_salesdaily_basic.smownerid '.$where;

            }
        }
        $datemonth=date('Y-m');

        $query="SELECT vtiger_salesdailyfournotv.*,(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_salesdaily_basic.smownerid=vtiger_users.id) as username,vtiger_salesdaily_basic.dailydatetime FROM vtiger_salesdailyfournotv LEFT JOIN vtiger_salesdaily_basic ON vtiger_salesdaily_basic.salesdailybasicid=vtiger_salesdailyfournotv.salesdailybasicid
                WHERE LEFT(vtiger_salesdaily_basic.dailydatetime,7)=?".$listQuery." ORDER BY vtiger_salesdaily_basic.dailydatetime DESC";
        //echo $query;
        $result=$adb->pquery($query,array($datemonth));

        $arrtemp=array();
        while($rawData=$adb->fetch_array($result)){
            $arrtemp['foutnotv'][]=$rawData;
        }
        $query="SELECT vtiger_salesdailycandeal.*,(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_salesdaily_basic.smownerid=vtiger_users.id) as username,vtiger_account.accountname,vtiger_salesdaily_basic.dailydatetime FROM vtiger_salesdailycandeal LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_salesdailycandeal.accountid LEFT JOIN vtiger_salesdaily_basic ON vtiger_salesdaily_basic.salesdailybasicid=vtiger_salesdailycandeal.salesdailybasicid
                WHERE vtiger_salesdailycandeal.isupdatedeleted=0 AND LEFT(vtiger_salesdaily_basic.dailydatetime,7)=? ".$listQuery." GROUP BY vtiger_salesdailycandeal.accountid,vtiger_salesdaily_basic.smownerid ORDER BY vtiger_salesdaily_basic.dailydatetime DESC";
        $result=$adb->pquery($query,array($datemonth));
        $candealcontract=0;
        $candealdelete=0;
        while($rawData=$adb->fetch_array($result)){
            if($rawData['issigncontract']!=0){
                ++$candealcontract;
            }
            if($rawData['deleted']==1){
                ++$candealdelete;
            }
            $rawData['datacolor']=$rawData['deleted']==1?' style="color:#b94a48;"':($rawData['issigncontract']==1?' style="color:#468847;"':'');
            $rawData['issigncontract']=$rawData['issigncontract']==0?'否':'是';

            $arrtemp['candeal'][]=$rawData;
        }
        $arrtemp['candealcontract']=$candealcontract;
        $arrtemp['candealdelete']=$candealdelete;
        /*
        $query="SELECT vtiger_salesdailynextdayvisit.*,(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_salesdaily_basic.smownerid=vtiger_users.id) as username,vtiger_salesdaily_basic.dailydatetime FROM vtiger_salesdailynextdayvisit LEFT JOIN vtiger_salesdaily_basic ON vtiger_salesdaily_basic.salesdailybasicid=vtiger_salesdailynextdayvisit.salesdailybasicid
                WHERE  LEFT(vtiger_salesdaily_basic.dailydatetime,7)=? ".$listQuery." ORDER BY vtiger_salesdaily_basic.dailydatetime DESC";
        $result=$adb->pquery($query,array($datemonth));


        while($rawData=$adb->fetch_array($result)){
            $rawData['isvisitor']=empty($rawData['withvisitor'])?'没有':'有';
            $arrtemp['nextdayvisit'][]=$rawData;
        }*/
        $query="SELECT vtiger_salesdailydaydeal.*,vtiger_account.accountname,(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_salesdaily_basic.smownerid=vtiger_users.id) as username,vtiger_products.productname,vtiger_salesdaily_basic.dailydatetime FROM vtiger_salesdailydaydeal LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_salesdailydaydeal.accountid LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_salesdailydaydeal.productid LEFT JOIN vtiger_salesdaily_basic ON vtiger_salesdaily_basic.salesdailybasicid=vtiger_salesdailydaydeal.salesdailybasicid
                WHERE vtiger_salesdailydaydeal.deleted=0 AND LEFT(vtiger_salesdaily_basic.dailydatetime,7)=? ".$listQuery." ORDER BY vtiger_salesdaily_basic.dailydatetime DESC";
        $result=$adb->pquery($query,array($datemonth));

        $arrivalamount=0;
        while($rawData=$adb->fetch_array($result)){
            $rawData['oldcustomers']=$rawData['oldcustomers']==1?'是':'否';
            $rawData['allamount']=$rawData['allamount']==1?'是':'否';
            $rawData['paymentnature']=$rawData['paymentnature']=='firstpaymentnature'?'首付款':($rawData['paymentnature']=='lastpaymentnature'?'尾款':'');
            $rawData['isvisitor']=empty($rawData['withvisitor'])?'没有':'有';
            $rawData['discount']=$rawData['marketprice']==0?'':($rawData['dealamount']==0?0:(($rawData['dealamount']/$rawData['marketprice']*10)>=10?'不打折':number_format($rawData['dealamount']/$rawData['marketprice']*10,1).'折'));
            $arrtemp['daydeal'][]=$rawData;
            $arrivalamount+=$rawData['arrivalamount'];
        }
        $arrtemp['daydealarrivalamount']=$arrivalamount;
        return $arrtemp;
    }

    public static function getAccountStatisticsByRecord($recordId){
        global $adb;
        $sql = "select * from vtiger_accountstatistics where salesdailyid=?";
        $result = $adb->pquery($sql,array($recordId));
        if(!$adb->num_rows($result)){
            return array(
                "todayvisitnum"=>0,
                'total_telnumber'=>0,
                'telnumber'=>0,
                'tel_connect_rate'=>0,
                'wxnumberlastweeknumber'=>0,
                'wxnumberlastmonthnumber'=>0,
                'wxnumber'=>0,
                'wxnewlyaddnumber'=>0,
                'wxnumberweek'=>0,
                'wxnumberweekaddnumber'=>0,
                'wxnumbermonth'=>0,
                'wxnumbermonthaddnumber'=>0
            );
        }
        $row = $adb->fetchByAssoc($result,0);
        return $row;
    }

    public static function getWxNumber($userid,$dailydate){
        global $adb;
        $sql = "select wxnumber from vtiger_accountstatistics where dailydatetime>=? and dailydatetime<=? and userid=? order by dailydatetime desc limit 1 ";
        $lastWeekStart= date("Y-m-d",strtotime("-1 week Monday",strtotime($dailydate)));
        $lastWeekEnd= date("Y-m-d",strtotime("-1 week Sunday",strtotime($dailydate)));
        $lastMonthStart =date("Y-m-01",strtotime("-1 month",strtotime($dailydate)));
        $lastMonthEnd = date("Y-m-d",strtotime("-1 day",strtotime(date("Y-m-01",strtotime($dailydate)))));
        $result1 = $adb->pquery($sql,array($lastWeekStart,$lastWeekEnd,$userid));
        $result2 = $adb->pquery($sql,array($lastMonthStart,$lastMonthEnd,$userid));
        $lastWeekData = $adb->fetchByAssoc($result1,0);
        $lastMonthData = $adb->fetchByAssoc($result2,0);
        return array(
            'wxnumberlastweeknumber'=>$lastWeekData['wxnumber'],
            'wxnumberlastmonthnumber'=>$lastMonthData['wxnumber']
        );

    }

    public function sendWx($commentContent,$replyName)
    {
        $db = PearDatabase::getInstance();
        $sql = "select b.last_name,b.email1 from vtiger_salesdaily_basic a left join vtiger_users b on a.smownerid=b.id where salesdailybasicid=? limit 1";
        $result = $db->pquery($sql,array($this->getId()));
        if(!$db->num_rows($result)){
            return;
        }
        $row = $db->fetchByAssoc($result,0);
        $content = '姓名:'.$row['last_name'].'<br>批复人:'.$replyName.'<br>批复内容:'.$commentContent;
        $this->sendWechatMessage(array('email'=>trim($row['email1']),'description'=>$content,'dataurl'=>'#','title'=>'【日报批复提醒】您的日报已经批复了','flag'=>7));
    }

    public function canReply($smownerid,$userid){
        global $current_user;
        if($userid==$current_user->id && $current_user->is_admin=='on'){
            return true;
        }
        $allSuperior = getAllSuperiorIds($smownerid);
        if(in_array($userid,$allSuperior)){
            return true;
        }
        return false;
    }

}