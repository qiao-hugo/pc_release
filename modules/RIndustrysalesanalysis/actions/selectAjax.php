<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * 
 *************************************************************************************/

class RIndustrysalesanalysis_selectAjax_Action extends Vtiger_Action_Controller {
    public function __construct(){
        parent::__construct();
        $this->exposeMethod('getCountsday');
        $this->exposeMethod('getdetaillist');
        $this->exposeMethod('getUsers');
    }
    function checkPermission(Vtiger_Request $request) {
        return true;
    }
    public function process(Vtiger_Request $request) {
		$mode=$request->getMode();
        if(!empty($mode)){
            echo $this->invokeExposedMethod($mode,$request);
            exit;
        }
	}
    //打开时加载
    public function getCountsday(Vtiger_Request $request){
        $datetime=$request->get('datetime');
        $enddatetime=$request->get('enddatetime');
        $userid=$request->get('userid');
        $pagenum=$request->get('pagenum');
        $departmentid=$request->get('department');
        $fliter=$request->get('fliter');
        if($fliter=='thisweek'){
            $datetime=$lastday=date('Y-m-d',strtotime("Sunday"));
            $firstday=date('Y-m-d',strtotime("$lastday -6 days"));
            $tempdate = " AND TO_DAYS(vtiger_crmentity.createdtime) BETWEEN TO_DAYS('{$firstday}') AND TO_DAYS('{$lastday}')";
            $tempdate1 = " AND TO_DAYS(vtiger_servicecontracts.signdate) BETWEEN TO_DAYS('{$firstday}') AND TO_DAYS('{$lastday}')";
            $tempdate2 = " AND TO_DAYS(vtiger_receivedpayments.reality_date) BETWEEN TO_DAYS('{$firstday}') AND TO_DAYS('{$lastday}')";

        }else if($fliter=='thismonth'){
            $datetime=$firstday = date('Y-m-01');
            $lastday = date('Y-m-d',strtotime("$firstday +1 month -1 day"));
            $tempdate = " AND TO_DAYS(vtiger_crmentity.createdtime) BETWEEN TO_DAYS('{$firstday}') AND TO_DAYS('{$lastday}')";
            $tempdate1 = " AND TO_DAYS(vtiger_servicecontracts.signdate) BETWEEN TO_DAYS('{$firstday}') AND TO_DAYS('{$lastday}')";
            $tempdate2 = " AND TO_DAYS(vtiger_receivedpayments.reality_date) BETWEEN TO_DAYS('{$firstday}') AND TO_DAYS('{$lastday}')";

        }else {
            if (strtotime($datetime) > strtotime($enddatetime)) {
                $tempdate = " AND TO_DAYS(vtiger_crmentity.createdtime) BETWEEN TO_DAYS('{$enddatetime}') AND TO_DAYS('{$datetime}')";
                $tempdate1 = " AND TO_DAYS(vtiger_servicecontracts.signdate) BETWEEN TO_DAYS('{$enddatetime}') AND TO_DAYS('{$datetime}')";
                $tempdate2 = " AND TO_DAYS(vtiger_receivedpayments.reality_date) BETWEEN TO_DAYS('{$enddatetime}') AND TO_DAYS('{$datetime}')";
            } elseif (strtotime($datetime) < strtotime($enddatetime)) {
                $tempdate = " AND TO_DAYS(vtiger_crmentity.createdtime) BETWEEN TO_DAYS('{$datetime}') AND TO_DAYS('{$enddatetime}')";
                $tempdate1 = " AND TO_DAYS(vtiger_servicecontracts.signdate) BETWEEN TO_DAYS('{$datetime}') AND TO_DAYS('{$enddatetime}')";
                $tempdate2 = " AND TO_DAYS(vtiger_receivedpayments.reality_date) BETWEEN TO_DAYS('{$datetime}') AND TO_DAYS('{$enddatetime}')";
            } else {
                $tempdate = " AND TO_DAYS(vtiger_crmentity.createdtime) =TO_DAYS('{$datetime}')";
                $tempdate1 = " AND TO_DAYS(vtiger_servicecontracts.signdate) =TO_DAYS('{$datetime}')";
                $tempdate2 = " AND TO_DAYS(vtiger_receivedpayments.reality_date) =TO_DAYS('{$datetime}')";
            }
        }
        if(empty($pagenum)||!in_array($pagenum,array(10,20))){
            $limit=20;
        }else{
            $limit=abs((int)$pagenum);
        }
        if($datetime==''){

            $tempdate="";
            $tempdate1=" AND vtiger_servicecontracts.signdate IS NOT NULL";
            $tempdate2=" AND vtiger_receivedpayments.reality_date IS NOT NULL";
        }
        if(empty($userid)){
            if(!empty($departmentid)&&$departmentid!='H1'){
                $userid=getDepartmentUser($departmentid);
                $where=getAccessibleUsers('RIndustrysalesanalysis','List',true);
                if($where!='1=1'){
                    $where=array_intersect($where,$userid);
                }else{
                    $where=$userid;
                }
                $sql = ' AND vtiger_crmentity.smownerid in('.implode(',',$where).')';
                $sql1 = ' AND vtiger_servicecontracts.receiveid in('.implode(',',$where).')';
                $sql2 = ' AND vtiger_crmentity.smownerid in('.implode(',',$where).')';
            }else{
                $where=getAccessibleUsers('RIndustrysalesanalysis','List',false);
                if($where!='1=1'){
                    $sql =' AND vtiger_crmentity.smownerid '.$where;
                    $sql1 =' AND vtiger_servicecontracts.receiveid '.$where;
                    $sql2 =' AND vtiger_crmentity.smownerid '.$where;
                }else{
                    $sql='';
                    $sql1='';
                    $sql2='';
                }
            }

        }else{
            $sql=" AND vtiger_crmentity.smownerid={$userid}";
            $sql1=" AND vtiger_servicecontracts.receiveid={$userid}";
            $sql2=" AND vtiger_crmentity.smownerid={$userid}";
        }
        //$datetime=date('Y-m-d');
        $db=PearDatabase::getInstance();
        $query="SELECT
                    vtiger_account.industry,
                    count(1) AS sumindustry
                FROM
                    vtiger_account
                LEFT JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid
                WHERE
                    vtiger_crmentity.deleted = 0
                AND vtiger_account.industry IS NOT NULL
                {$tempdate}
                {$sql}
                GROUP BY
                    vtiger_account.industry
                ORDER BY sumindustry DESC";
        //echo $query;
        $arr['numcounts']=self::getresultarray($query,array('limit'=>$limit,'one'=>'industry','one1'=>'industry','two'=>'sumindustry','two1'=>'totals'));
        /*$result=$db->pquery($query,array());
        $num=$db->num_rows($result);
        $arr=array();
        $nlimit=$limit;
        if($num<1){

            $arr['numcounts']=array();
        }else{
            $countnum=0;
            for($i=0;$i<$num;$i++){
                //排除其它有其它的都放到其它数组中
                if(vtranslate($db->query_result($result,$i,'industry'),"Accounts")=='其它'){
                    $limit++;
                }
                if($i<($limit-1) && vtranslate($db->query_result($result,$i,'industry'),"Accounts")!=='其它'){
                    if($nlimit==$limit){
                        $arr['numcounts'][$i]['industry']=vtranslate($db->query_result($result,$i,'industry'),"Accounts");
                        $arr['numcounts'][$i]['totals']=$db->query_result($result,$i,'sumindustry');
                    }else{
                        $arr['numcounts'][$i-1]['industry']=vtranslate($db->query_result($result,$i,'industry'),"Accounts");
                        $arr['numcounts'][$i-1]['totals']=$db->query_result($result,$i,'sumindustry');
                    }

                }else{
                    $countnum+=$db->query_result($result,$i,'sumindustry');
                }
            }
            if($i>$nlimit){
                $arr['numcounts'][$nlimit-1]['industry']='其它';
                $arr['numcounts'][$nlimit-1]['totals']=$countnum;
            }


        }*/

        $query1="SELECT
                    vtiger_account.industry,
                 sum(IFNULL(vtiger_servicecontracts.total,0)) AS totals
                FROM
                    vtiger_account
                LEFT JOIN vtiger_servicecontracts ON vtiger_account.accountid = vtiger_servicecontracts.sc_related_to
                WHERE
                    vtiger_servicecontracts.sc_related_to>0
                AND
                vtiger_servicecontracts.modulestatus='c_complete'
                {$tempdate1}
                {$sql1}
                GROUP BY
                vtiger_account.industry
                ORDER BY
                totals DESC";
        $arr['Contractedamount']=self::getresultarray($query1,array('limit'=>$limit,'one'=>'industry','one1'=>'industry','two'=>'totals','two1'=>'totals'));
         $query2="SELECT
                    vtiger_account.industry,
                    sum(IFNULL(vtiger_receivedpayments.unit_price,0)) AS totals
                FROM
                    vtiger_receivedpayments
                LEFT JOIN vtiger_servicecontracts ON vtiger_receivedpayments.relatetoid = vtiger_servicecontracts.servicecontractsid
                LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_servicecontracts.sc_related_to
                WHERE
                    vtiger_servicecontracts.sc_related_to > 0
                AND vtiger_servicecontracts.modulestatus = 'c_complete'
                {$tempdate2}
                {$sql1}
                GROUP BY
                    vtiger_account.industry
                ORDER BY
                    totals DESC";
        $arr['Payment']=self::getresultarray($query2,array('limit'=>$limit,'one'=>'industry','one1'=>'industry','two'=>'totals','two1'=>'totals'));
        //echo $query2;
        /*$result1=$db->pquery($query1,array());
        $num1=$db->num_rows($result1);
        if($num1<1){
            //$arr['Payment']=array();
            $arr['Contractedamount']=array();
        }else{
            $limit=$nlimit;
            $countnum=0;
            for($i=0;$i<$num1;$i++){
                //排除其它有其它的都放到其它数组中
                if(vtranslate($db->query_result($result1,$i,'industry'),"Accounts")=='其它'){
                    $limit++;
                }
                if($i<($limit-1) && vtranslate($db->query_result($result1,$i,'industry'),"Accounts")!='其它'){
                    if($nlimit==$limit){
                        $arr['Contractedamount'][$i]['industry']=vtranslate($db->query_result($result1,$i,'industry'),"Accounts");
                        $arr['Contractedamount'][$i]['totals']=$db->query_result($result1,$i,'totals');
                    }else{
                        $arr['Contractedamount'][$i-1]['industry']=vtranslate($db->query_result($result1,$i,'industry'),"Accounts");
                        $arr['Contractedamount'][$i-1]['totals']=$db->query_result($result1,$i,'totals');
                    }
                }else{
                    $countnum+=$db->query_result($result1,$i,'totals');
                }
            }
            if($i>$nlimit){
                $arr['Contractedamount'][$nlimit-1]['industry']='其它';
                $arr['Contractedamount'][$nlimit-1]['totals']=$countnum;
            }
        }*/
        //echo json_encode(array("success"=>true,"result"=>$arr),true);
        //exit;
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($arr);
        $response->emit();
    }
    private function getresultarray($query,$arrd){
        $db=PearDatabase::getInstance();
        $result=$db->pquery($query,array());
        $num=$db->num_rows($result);
        //echo $num;
        $arr=array();
        if($num<1){
            //$arr['Payment']=array();
            return array();
        }else{
            /*$limit=$nlimit=$arrd['limit'];
            $countnum=0;
            //array('limit'=>$limit,'one'=>'industry','one1'=>'industry','two'=>'totals','two1'=>'totals');
            for($i=0;$i<$num;$i++){
                //排除其它有其它的都放到其它数组中
                if(vtranslate($db->query_result($result,$i,$arrd['one']),"Accounts")=='其它'){
                    $limit++;
                }
                if($i<($limit-1) && vtranslate($db->query_result($result,$i,$arrd['one']),"Accounts")!='其它'){
                    if($nlimit==$limit){
                        $arr[$i][$arrd['one1']]=vtranslate($db->query_result($result,$i,$arrd['one']),"Accounts");
                        $arr[$i][$arrd['two1']]=$db->query_result($result,$i,$arrd['two']);
                    }else{
                        $arr[$i-1][$arrd['one1']]=vtranslate($db->query_result($result,$i,$arrd['one']),"Accounts");
                        $arr[$i-1][$arrd['two1']]=$db->query_result($result,$i,$arrd['two']);
                    }
                }else{
                    $countnum+=$db->query_result($result,$i,$arrd['two']);
                }
            }
            if($i>$nlimit){
                $arr[$nlimit-1][$arrd['one1']]='其它';
                $arr[$nlimit-1][$arrd['two1']]=$countnum;

            }
            return $arr;*/

            $limit=$nlimit=$arrd['limit'];
            $countnum=0;
            //array('limit'=>$limit,'one'=>'industry','one1'=>'industry','two'=>'totals','two1'=>'totals');
            $temparr=array();
            $j=0;
            for($i=0;$i<$num;$i++){
                //排除其它有其它的都放到其它数组中
                if(vtranslate($db->query_result($result,$i,$arrd['one']),"Accounts")=='其它'||in_array(vtranslate($db->query_result($result,$i,$arrd['one']),"Accounts"),$temparr)){
                    $limit++;
                }
                if(($i<($limit-1) && vtranslate($db->query_result($result,$i,$arrd['one']),"Accounts")!='其它')||in_array(vtranslate($db->query_result($result,$i,$arrd['one']),"Accounts"),$temparr)){
                    if(in_array(vtranslate($db->query_result($result,$i,$arrd['one']),"Accounts"),$temparr)){
                        $ntemparr=array_flip($temparr);
                        $m=$ntemparr[vtranslate($db->query_result($result,$i,$arrd['one']),"Accounts")];
                        $ss=$arr[$m][$arrd['two1']];
                        $arr[$m][$arrd['two1']]=$ss+$db->query_result($result,$i,$arrd['two']);
                    }else{
                        if($nlimit==$limit){
                            $j=$i;
                            $arr[$i][$arrd['one1']]=vtranslate($db->query_result($result,$i,$arrd['one']),"Accounts");
                            $arr[$i][$arrd['two1']]=$db->query_result($result,$i,$arrd['two']);
                        }else{
                            if(empty($arr)){
                                $j=0;
                            }else{
                                ++$j;
                            }

                            $arr[$j][$arrd['one1']]=vtranslate($db->query_result($result,$i,$arrd['one']),"Accounts");
                            $arr[$j][$arrd['two1']]=$db->query_result($result,$i,$arrd['two']);
                        }
                        $temparr[$j]=vtranslate($db->query_result($result,$i,$arrd['one']),"Accounts");
                    }
                }else{
                    $countnum+=$db->query_result($result,$i,$arrd['two']);
                }
            }
            if($i>$nlimit){
                $arr[$nlimit-1][$arrd['one1']]='其它';
                $arr[$nlimit-1][$arrd['two1']]=$countnum;
            }
        }
        //非标准数组直接转Json无法解析
        //return json_decode(json_encode($arr),true);

        $arrtemp=array();
        for($i=0;$i<count($arr);$i++){
            $arrtemp[$i][$arrd['one1']]=$arr[$i][$arrd['one1']];
            $arrtemp[$i][$arrd['two1']]=$arr[$i][$arrd['two1']];
        }
        /*echo "<pre>";
        print_r($arr);
        echo "</pre>";
        exit;*/
        return $arrtemp;
        //return $arr;
    }
    public function getUsers(Vtiger_Request $request){
        $departmentid=$request->get('department');
        if(!empty($departmentid)&&$departmentid!='H1'){
            $userid=getDepartmentUser($departmentid);
            $where=getAccessibleUsers('RIndustrysalesanalysis','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $listQuery = ' AND id in('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers('RIndustrysalesanalysis','List',false);
            if($where!='1=1'){
                $listQuery =' AND id '.$where;
            }else{
                $listQuery='';
            }
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(Reporting_Record_Model::getuserinfo($listQuery));
        $response->emit();
        //return Reporting_Record_Model::getuserinfo($listQuery);

    }

}
