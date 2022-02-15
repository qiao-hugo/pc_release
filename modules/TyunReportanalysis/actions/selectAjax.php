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

class TyunReportanalysis_selectAjax_Action extends Vtiger_Action_Controller {
    public function __construct(){
        parent::__construct();
        $this->exposeMethod('getTyunReportData');
        //$this->exposeMethod('getdetaillist');
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

	public function getTyunReportData(Vtiger_Request $request){
        $tyunReportQuery = $request->get('tyunReportQuery');
        $stat_index = $request->get('stat_index');//统计指标
        $stat_dim = $request->get('stat_dim');//统计维度 1:按部门 2:按负责人
        $stat_type_index = $request->get('stat_type_index');//显示方式
        $stat_date_type = $request->get('stat_date_type');//统计方式
        $departmentid=$request->get('department');
        $ownerid = $request->get('ownerid');

        global $adb;

        $arr_result = array();
        $query_sql = "
            SELECT 
            vtiger_user2department.departmentid,
            IFNULL(vtiger_departments.departmentname,'--') AS departmentname,
            vtiger_crmentity.smownerid,
            vtiger_users.last_name,
            vtiger_servicecontracts.signdate,
            vtiger_receivedpayments.reality_date,
            vtiger_servicecontracts.servicecontractsid,
            vtiger_servicecontracts.contract_no,
            vtiger_servicecontracts.total as servicecontractstotal,	
            vtiger_receivedpayments.unit_price as paymenttotal,
            vtiger_servicecontracts.sc_related_to as accountid,
            vtiger_account.accountname,
            vtiger_products.productid,
            IFNULL(vtiger_products.productname,'未知') as productname,
            vtiger_receivedpayments.allowinvoicetotal
            FROM
                vtiger_receivedpayments
            LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_receivedpayments.relatetoid
            LEFT JOIN vtiger_account ON(vtiger_account.accountid=vtiger_servicecontracts.sc_related_to)
            LEFT JOIN vtiger_products ON(vtiger_servicecontracts.productid=vtiger_products.productid)
            LEFT JOIN vtiger_crmentity ON(vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid)
            LEFT JOIN vtiger_user2department ON(vtiger_user2department.userid=vtiger_crmentity.smownerid)
            LEFT JOIN vtiger_departments ON(vtiger_departments.departmentid=vtiger_user2department.departmentid)
            LEFT JOIN vtiger_users ON(vtiger_users.id=vtiger_crmentity.smownerid)
            WHERE
              vtiger_receivedpayments.deleted=0
              AND vtiger_crmentity.deleted=0
              AND vtiger_servicecontracts.modulestatus='c_complete'
              AND vtiger_receivedpayments.receivedstatus='normal'
              AND vtiger_servicecontracts.parent_contracttypeid=2
              AND NOT ISNULL(vtiger_servicecontracts.signdate)";

        $condition = "";
        foreach ($tyunReportQuery as $key => $value) {
            $data_type = explode("##",$value);
            $data_type = $data_type[0];
            $data_value = explode("##",$value);
			$data_value = $data_value[1];
            if(strtolower($data_type) == "datetime" || strtolower($data_type) == "date"){
                $arr_data = explode("&&",$data_value);
                foreach ($arr_data as $value2) {
                    $start_date = explode("|",$value2);
					$start_date = $start_date[0];
					
                    $end_date = explode("|",$value2);
					$end_date = $end_date[1];
                    if(!empty($start_date) && !empty($end_date)){
                        $condition .= " AND ".$key .' BETWEEN "'.$start_date .'" AND "'.$end_date .' 23:59:59"';
                    }
                }
            }else{
                $arr_data = explode("&&",$data_value);
                foreach ($arr_data as $value1) {
                    if(!empty($value1)){
                        $condition .= " AND ".$key .'='.$value1;
                    }
                }

            }
        }

        if(!empty($condition)){
            $query_sql.= $condition;
        }

        //查询列表数据
        $query_sql_list = $query_sql;

        $group_comm_field = "";
        //$stat_comm_value = "";
        $get_comm_field = "";
        if($stat_index == '2'){
            //合同金额
            //$stat_comm_value = "SUM(servicecontractstotal)";
            $get_comm_field .= "SUM(servicecontractstotal) as total";
        }else if ($stat_index == '3') {
            //回款金额
            //$stat_comm_value = "SUM(paymenttotal)";
            $get_comm_field .= "SUM(paymenttotal) as total";
        }else if ($stat_index == '4') {
            //未回款金额
            //$stat_comm_value = "SUM(paymenttotal)";
            $get_comm_field .= "(T.servicecontractstotal-(SELECT SUM(M.unit_price) FROM vtiger_receivedpayments M WHERE M.relatetoid=T.servicecontractsid AND M.deleted=0)) as total";
        }else if ($stat_index == '5') {
            //开票金额
            //$stat_comm_value = "SUM(allowinvoicetotal)";
            $get_comm_field .= "SUM(allowinvoicetotal) as total";
        }else if ($stat_index == '6') {
            //未开票金额
            //$stat_comm_value = "SUM(allowinvoicetotal)";
            $get_comm_field .= "(T.servicecontractstotal-(SELECT SUM(M.allowinvoicetotal) FROM vtiger_receivedpayments M WHERE M.relatetoid=T.servicecontractsid AND M.deleted=0)) as total";
        }else{
            //客户数量
            //$stat_comm_value = "COUNT(accountid)";
            $get_comm_field .= "COUNT(accountid) as total";
        }

        if($stat_date_type == '2'){
            //按回款日期
            if($stat_type_index == '5'){
                //按版本
                $group_comm_field = 'productid';
                $get_comm_field .= ",productname as stat_date";
            }else if($stat_type_index == '3'){
                $group_comm_field = 'LEFT(reality_date,7)';
                $get_comm_field .= ",LEFT(reality_date,7) as stat_date";
            }else{
                $group_comm_field = 'reality_date';
                $get_comm_field .= ",reality_date as stat_date";
            }
        }else{
            //按签单日期
            if($stat_type_index == '5'){
                //按版本
                $group_comm_field = 'productid';
                $get_comm_field .= ",productname as stat_date";
            }else if($stat_type_index == '3'){
                $group_comm_field = 'LEFT(signdate,7)';
                $get_comm_field .= ",LEFT(signdate,7) as stat_date";
            }else{
                $group_comm_field = "signdate";
                $get_comm_field .= ",signdate as stat_date";
            }
        }

        //统计维度
        if($stat_dim == '2'){
            //按负责人
            $group_comm_field.=',smownerid';
            $get_comm_field .= ",smownerid as stat_dim_value";
        }else{
            //按部门
            $group_comm_field.=',stat_dim_value';
            //$get_comm_field .= ",departmentid as stat_dim_value";
        }

        $list_query = "";
        $query_data = "";

        if($stat_dim == "1") {
            //按部门
            if ($departmentid == "null" || empty($departmentid)) {
                $departmentid = array();
                $departmentid[] = 'H1';
            }
            $cachedepartment = getDepartment();

            //部门
            for ($i = 0; $i < count($departmentid); ++$i) {
                $arr_result['newdepartmentid'][] = strtolower($departmentid[$i]);
                $arr_result['newdepartment'][strtolower($departmentid[$i])] = str_replace(array('|', '—'), array('', ''), $cachedepartment[$departmentid[$i]]);
                $arr_stat_dim_value[] = $departmentid[$i];

                $query_data = $query_sql." AND FIND_IN_SET('$departmentid[$i]',REPLACE(vtiger_departments.parentdepartment,'::',','))";
                $list_query .= "SELECT ".$get_comm_field.",'{$departmentid[$i]}' as stat_dim_value FROM (".$query_data.") T  GROUP BY ".$group_comm_field;
                
                if($i < count($departmentid) -1 ){
                    $list_query .= " UNION ALL ";
                }
            }
            //echo $list_query;die();
            $uresult_data=$adb->pquery($list_query);
        }else{
            $list_query = "SELECT ".$get_comm_field.",last_name  FROM (".$query_sql.") T  GROUP BY ".$group_comm_field." ORDER BY ".$group_comm_field.",stat_dim_value DESC LIMIT 20";
            //echo $list_query;
            $uresult_data=$adb->pquery($list_query);
            $num_data=$adb->num_rows($uresult_data);
            if($num_data>0){
                for($i=0;$i<$num_data;++$i){
                    $arr_result['newdepartmentid'][]= $adb->query_result($uresult_data,$i,'stat_dim_value');
                    $arr_result['newdepartment'][$adb->query_result($uresult_data,$i,'stat_dim_value')]=$adb->query_result($uresult_data,$i,'last_name');
                    $arr_stat_dim_value[] = $adb->query_result($uresult_data,$i,'stat_dim_value');
                }
            }
        }
        //echo $list_query;die();
        $num_data=$adb->num_rows($uresult_data);
        if($num_data>0){
            $startdate="";
            $enddate="";
            $arr_date=array();
            //print_r($list_query);
            for($i=0;$i<$num_data;++$i){
                $cur_value = $adb->query_result($uresult_data,$i,'stat_date');
                //print_r($cur_value);
                if(in_array($cur_value,$arr_date)) continue;
                $arr_date[]['stat_date']=$cur_value;
            }
            //$arr_date.rsort();
            usort($arr_date, 'date_compare');

            //print_r($arr_date);die();

            //print_r($arr_date);
            $startdate = $arr_date[0]['stat_date'];
            $enddate = $arr_date[count($arr_date)-1]['stat_date'];

            //$s_date = strtotime(date("Y-m-d",strtotime($startdate)));
            //$e_date = strtotime(date("Y-m-d",strtotime($enddate)));
            //$date_diff_days =  round(($e_date-$s_date)/86400);

            if($stat_type_index == '5') {
                //按版本
            }else if($stat_type_index == '3'){
                //按月显示
                $arr_date = TyunReportanalysis_Record_Model::getMonthFromRange($startdate,$enddate);
            }else {
                //按日显示
                $arr_date = TyunReportanalysis_Record_Model::getDateFromRange($startdate, $enddate);
            }
            //print_r($arr_date);die();
            for($i=0;$i<count($arr_date);$i++){
                $arr_result['stat_date'][]= $arr_date[$i]['stat_date'];

                for($u=0;$u<count($arr_stat_dim_value);$u++){
                    $cur_dim_id = $arr_stat_dim_value[$u];
                    $check = false;
                    for($j=0;$j<$num_data;++$j){
                        $cur_dt = $adb->query_result($uresult_data,$j,'stat_date');
                        $cur_userid_tmp = $adb->query_result($uresult_data,$j,'stat_dim_value');
                        if($cur_dt == $arr_date[$i]['stat_date'] && $arr_stat_dim_value[$u] == $cur_userid_tmp){
                            $arr_result['tyun_reports_'.strtolower($cur_dim_id)][]= $adb->query_result($uresult_data,$j,'total');
                            $check = true;
                            break;
                        }
                    }
                    if($check == false){
                        $arr_result['tyun_reports_'.strtolower($cur_dim_id)][]= 0;
                    }
                }
            }

            //查询列表数据
            $group_list_field="";
            $get_list_field ="";
            $arr_col_name = array();
            if($stat_date_type == '2'){
                //按签单日期
                if($stat_type_index == '5'){
                    //按版本
                    $group_list_field = 'T.productid';
                    $get_list_field = ",T.productname";
                    $arr_col_name["productname"] = "版本";
                }else if($stat_type_index == '3'){
                    $group_list_field = 'LEFT(T.reality_date,7)';
                    $get_list_field = "LEFT(T.reality_date,7) AS reality_date";
                    $arr_col_name["reality_date"] = "回款月";
                }else{
                    $group_list_field = 'T.reality_date';
                    $get_list_field = "T.reality_date AS reality_date";
                    $arr_col_name["reality_date"] = "回款日期";
                }
            }else{
                //按签单日期
                if($stat_type_index == '5'){
                    //按版本
                    $group_list_field = 'T.productid';
                    $get_list_field = "T.productname";
                    $arr_col_name["productname"] = "版本";
                }else if($stat_type_index == '3'){
                    $group_list_field = 'LEFT(T.signdate,7)';
                    $get_list_field = "LEFT(T.signdate,7) AS signdate";
                    $arr_col_name["signdate"] = "签单月";
                }else{
                    $group_list_field = 'T.signdate';
                    $get_list_field = "T.signdate AS signdate";
                    $arr_col_name["signdate"] = "签单日期";
                }
            }

            $query_list_data_sql = "";
            if($stat_dim == "1") {
                //按部门
                $arr_col_name["departmentname"] = "部门";
                //$group_list_field .= ",T.departmentid";
                //$get_list_field .= ",T.departmentid,T.departmentname";

                //部门
                for ($i = 0; $i < count($arr_stat_dim_value); ++$i) {
                    $query_list_data = $query_sql_list." AND FIND_IN_SET('$arr_stat_dim_value[$i]',REPLACE(vtiger_departments.parentdepartment,'::',','))";
                    $department_name = $arr_result['newdepartment'][strtolower($arr_stat_dim_value[$i])];
                    $query_list_data_sql .= "SELECT '".$arr_stat_dim_value[$i]. "' AS departmentid,'".$department_name."' AS departmentname,".$get_list_field.",
                    COUNT(accountid) AS accountcount,
                    SUM(T.servicecontractstotal) AS servicecontractstotal,
                    SUM(T.paymenttotal) AS paymenttotal,
                    (T.servicecontractstotal-(SELECT SUM(M.unit_price) FROM vtiger_receivedpayments M WHERE M.relatetoid=T.servicecontractsid AND M.deleted=0)) as nopaymenttotal,
                    SUM(T.allowinvoicetotal) AS allowinvoicetotal,
                    (T.servicecontractstotal-(SELECT SUM(M.allowinvoicetotal) FROM vtiger_receivedpayments M WHERE M.relatetoid=T.servicecontractsid AND M.deleted=0)) as noallowinvoicetotal
                    FROM (".$query_list_data.") T  GROUP BY ".$group_list_field.",'$arr_stat_dim_value[$i]'";

                    if($i < count($departmentid) -1 ){
                        $query_list_data_sql .= " UNION ALL ";
                    }
                }

            }else{
                $arr_col_name["last_name"] = "负责人";
                $group_list_field .= ",T.smownerid";
                $get_list_field .= ",T.smownerid,T.last_name";
                $query_list_data_sql = "SELECT ".$get_list_field. ",
                COUNT(accountid) AS accountcount,
                SUM(T.servicecontractstotal) AS servicecontractstotal,
                SUM(T.paymenttotal) AS paymenttotal,
                (T.servicecontractstotal-(SELECT SUM(M.unit_price) FROM vtiger_receivedpayments M WHERE M.relatetoid=T.servicecontractsid AND M.deleted=0)) as nopaymenttotal,
                SUM(T.allowinvoicetotal) AS allowinvoicetotal,
                (T.servicecontractstotal-(SELECT SUM(M.allowinvoicetotal) FROM vtiger_receivedpayments M WHERE M.relatetoid=T.servicecontractsid AND M.deleted=0)) as noallowinvoicetotal
                FROM (".$query_sql_list.") T  GROUP BY ".$group_list_field;
            }

            $arr_col_name["accountcount"] = "客户数量";
            $arr_col_name["servicecontractstotal"] = "合同金额";
            $arr_col_name["paymenttotal"] = "回款金额";
            $arr_col_name["nopaymenttotal"] = "未回款金额";
            $arr_col_name["allowinvoicetotal"] = "开票金额";
            $arr_col_name["noallowinvoicetotal"] = "未开票金额";

            //echo $query_list_data_sql;die();
            $report_list =$adb->run_query_allrecords($query_list_data_sql);
            $arr_result["report_list_data"] = $report_list;
            $arr_result["report_list_col"] = $arr_col_name;
        }else{
            $arr_result = Array('newdepartmentid' => Array('hno'),'newdepartment' => Array('hno' => '暂无数据'),'tyun_reports_hno' => Array(0),'stat_date' => Array('暂无数据'),'report_list'=>Array());
        }

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($arr_result);
        $response->emit();
    }

    //打开时加载
    public function getCountsday(Vtiger_Request $request){
        $startdate=$request->get('startdate');
        $enddate=$request->get('enddate');
        $userid=$request->get('userid');
        $stat_index = $request->get('stat_index');//统计指标
        $radio_stat_date_index = $request->get('radio_stat_date_index');//统计日期
        $stat_dim = $request->get('stat_dim');//统计维度 1:按部门 2:按负责人
        $departmentid=$request->get('department');

        $s_date = strtotime(date("Y-m-d",strtotime($startdate)));
        $e_date = strtotime(date("Y-m-d",strtotime($enddate)));
        $date_diff_days =  round(($e_date-$s_date)/86400);

        //选择部门或人员最大个数
        $max_col_count = 10;

        //处理时间(默认按签单日期)
        $stat_comm_date = "  AND signdate BETWEEN '{$startdate}' AND '{$enddate} 23:59:59'";
        $group_comm_field = "a.signdate";
        $order_comm_field = "a.signdate";

        if($radio_stat_date_index == '2'){
            //按创建日期
            $stat_comm_date = "  AND s.createdtime BETWEEN '{$startdate}' AND '{$enddate} 23:59:59'";
            $group_comm_field = "s.createdtime";
            $order_comm_field = "s.createdtime";
        }

        //处理部门
        $db=PearDatabase::getInstance();
        $arr=array();
        $arr_stat_dim_value =array();
        $date_len = $date_diff_days > 60 ? 7:10;

        $table_name = "";
        $stat_value = "";
        $get_field = "";
        $rel_table_name = "";
        $rel_main_table_id = "";
        $rel_slave_table_id = "";
        if($stat_index == '2'){
            //合同金额
            $table_name = "vtiger_contractactivacode";
            $get_field = "IFNULL(a.servicecontractstotal,0)";
            $stat_value = "SUM(T.total)";
            $rel_main_table_id = "servicecontractsid";

            $rel_table_name = "SELECT vtiger_servicecontracts.servicecontractsid,vtiger_crmentity.createdtime FROM vtiger_servicecontracts 
                            INNER JOIN vtiger_crmentity ON(vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid)
                            WHERE vtiger_crmentity.deleted=0";
            $rel_slave_table_id = "servicecontractsid";
        }else if ($stat_index == '3'){
            //回款金额
            $table_name = "vtiger_contractactivacode";
            $get_field = "IFNULL(s.paymenttotal,0)";
            $stat_value = "SUM(T.total)";

            $rel_main_table_id = "servicecontractsid";
            $rel_table_name = "SELECT vtiger_servicecontracts.servicecontractsid,vtiger_receivedpayments.unit_price as paymenttotal,vtiger_receivedpayments.createdtime FROM vtiger_receivedpayments
                            INNER JOIN vtiger_achievementallot ON vtiger_receivedpayments.receivedpaymentsid = vtiger_achievementallot.receivedpaymentsid
                            LEFT JOIN vtiger_servicecontracts ON(vtiger_servicecontracts.servicecontractsid=vtiger_achievementallot.servicecontractid)
                            WHERE vtiger_receivedpayments.deleted=0";
            $rel_slave_table_id = "servicecontractsid";
        }else if($stat_index == '4'){
            //未回款金额
            $table_name = "vtiger_contractactivacode";
            $get_field = "IFNULL(servicecontractstotal,0)-IFNULL(paymenttotal,0)";
            $stat_value = "SUM(T.total)";

            $rel_main_table_id = "servicecontractsid";
            $rel_table_name = "SELECT vtiger_servicecontracts.servicecontractsid,vtiger_receivedpayments.unit_price as paymenttotal,vtiger_receivedpayments.createdtime FROM vtiger_receivedpayments
                            INNER JOIN vtiger_achievementallot ON vtiger_receivedpayments.receivedpaymentsid = vtiger_achievementallot.receivedpaymentsid
                            LEFT JOIN vtiger_servicecontracts ON(vtiger_servicecontracts.servicecontractsid=vtiger_achievementallot.servicecontractid)
                            WHERE vtiger_receivedpayments.deleted=0";
            $rel_slave_table_id = "servicecontractsid";
        }else if($stat_index == '5'){
            //开票金额
            $table_name = "vtiger_contractactivacode";
            $get_field = "IFNULL(s.invoicetotal,0)";
            $stat_value = "SUM(T.total)";

            $rel_main_table_id = "servicecontractsid";
            $rel_table_name = "SELECT vtiger_servicecontracts.servicecontractsid,vtiger_newinvoice.taxtotal as invoicetotal,vtiger_crmentity.createdtime FROM vtiger_newinvoice 
                            INNER JOIN vtiger_crmentity ON(vtiger_crmentity.crmid=vtiger_newinvoice.invoiceid AND vtiger_newinvoice.modulestatus='c_complete')
                            LEFT JOIN vtiger_servicecontracts ON(vtiger_servicecontracts.servicecontractsid=vtiger_newinvoice.contractid)
                            WHERE vtiger_crmentity.deleted=0";
            $rel_slave_table_id = "servicecontractsid";
        }else if($stat_index == '6'){
            //未开票金额
            $table_name = "vtiger_contractactivacode";
            $get_field = "IFNULL(servicecontractstotal,0) - IFNULL(invoicetotal,0)";
            $stat_value = "SUM(T.total)";
        }else if($stat_index == '7'){
            //拜访单数
            $table_name = "vtiger_contractactivacode";
            $get_field = "IFNULL(visitingorderid,0)";
            $stat_value = "COUNT(1)";

            $rel_main_table_id = "accountid";
            $rel_table_name = "SELECT vtiger_account.accountid,vtiger_visitingorder.visitingorderid,vtiger_crmentity.createdtime FROM vtiger_visitingorder 
                            INNER JOIN vtiger_crmentity ON(vtiger_crmentity.crmid=vtiger_visitingorder.visitingorderid AND vtiger_visitingorder.modulestatus='c_complete')
                            INNER JOIN vtiger_account ON(vtiger_visitingorder.related_to=vtiger_account.accountid and vtiger_visitingorder.modulename='Accounts')
                            WHERE vtiger_crmentity.deleted=0";
            $rel_slave_table_id = "accountid";
        }else{
            //客户数量
            $table_name = "vtiger_contractactivacode";
            $get_field = "IFNULL(a.accountid,0)";
            $stat_value = "COUNT(1)";

            $rel_main_table_id = "accountid";
            $rel_table_name = "SELECT vtiger_account.accountid,vtiger_crmentity.createdtime FROM vtiger_account 
                            INNER JOIN vtiger_crmentity ON(vtiger_crmentity.crmid=vtiger_account.accountid)
                            WHERE vtiger_crmentity.deleted=0";
            $rel_slave_table_id = "accountid";
        }

        $query_data = "";
        if($stat_dim == "1"){
            //按部门
            if($departmentid=="null"||empty($departmentid)){
                $departmentid=array();
                $departmentid[]='H1';
            }
            $cachedepartment=getDepartment();

            //部门不能超过10个
            for($i=0;$i<count($departmentid)&&$i<$max_col_count;++$i){
                $arr['newdepartmentid'][]=strtolower($departmentid[$i]);
                $arr['newdepartment'][strtolower($departmentid[$i])]=str_replace(array('|','—'),array('',''),$cachedepartment[$departmentid[$i]]);
                $arr_stat_dim_value[] = $departmentid[$i];

                if($radio_stat_date_index == '2'){
                    $query_data .= " SELECT {$group_comm_field} as stat_date,'{$departmentid[$i]}' as stat_dim_value,{$get_field} AS total FROM vtiger_contractactivacode a 
                              RIGHT OUTER JOIN ({$rel_table_name}) s ON (s.{$rel_slave_table_id}=a.{$rel_main_table_id})
                              LEFT JOIN vtiger_departments b ON(b.departmentid=a.signdempart)
                              WHERE 1=1 {$stat_comm_date} 
                              AND FIND_IN_SET('$departmentid[$i]',REPLACE(b.parentdepartment,'::',','))";
                }else{
                    if($stat_index == 7){
                        //拜访单数
                        $query_data .= " SELECT {$group_comm_field} as stat_date,'{$departmentid[$i]}' as stat_dim_value,{$get_field} AS total FROM vtiger_visitingorder b
                              INNER JOIN vtiger_crmentity c ON(b.visitingorderid=c.crmid)
                              RIGHT OUTER JOIN vtiger_contractactivacode a ON(b.related_to=a.accountid and b.modulename='Accounts')
                              LEFT JOIN vtiger_departments d ON(d.departmentid=a.signdempart)
                              WHERE c.deleted=0 {$stat_comm_date} 
                              AND FIND_IN_SET('$departmentid[$i]',REPLACE(d.parentdepartment,'::',','))";
                    }else{
                        $query_data .= " SELECT {$group_comm_field} as stat_date,'{$departmentid[$i]}' as stat_dim_value,{$get_field} AS total FROM vtiger_contractactivacode a
                              LEFT JOIN vtiger_departments b ON(b.departmentid=a.signdempart)
                              WHERE 1=1 {$stat_comm_date} 
                              AND FIND_IN_SET('$departmentid[$i]',REPLACE(b.parentdepartment,'::',','))";
                    }
                }

                if($i < count($departmentid) -1 ){
                    $query_data .= " UNION ALL ";
                }
            }
            //获取统计数据
            $query_data = "SELECT LEFT(T.stat_date,{$date_len}) AS stat_date,T.stat_dim_value,{$stat_value} as total FROM ($query_data) T
                    GROUP BY LEFT(T.stat_date,{$date_len}),T.stat_dim_value 
                    ORDER BY LEFT(T.stat_date,{$date_len})";

            //echo $query_data;
            $uresult_data=$db->pquery($query_data);

        }else{
            //按负责人
            if($userid!='null'&&!empty($userid)){
                //获取用户信息
                $query_user="SELECT id,last_name FROM vtiger_users WHERE id in(".implode(',',$userid).") limit 20";
                $uresult_user=$db->pquery($query_user);
                $num_user=$db->num_rows($uresult_user);
                if($num_user>0){
                    for($i=0;$i<$num_user&&$i<$max_col_count;++$i){
                        if(in_array($db->query_result($uresult_user,$i,'id'),$arr['newdepartmentid'])){
                            continue;
                        }
                        $arr['newdepartmentid'][]= $db->query_result($uresult_user,$i,'id');
                        $arr['newdepartment'][$db->query_result($uresult_user,$i,'id')]=$db->query_result($uresult_user,$i,'last_name');
                        $arr_stat_dim_value[] = $db->query_result($uresult_user,$i,'id');
                    }
                }

                //获取统计数据
                if($radio_stat_date_index == '2'){
                    $query_data .= " SELECT {$group_comm_field} as stat_date,a.signid as stat_dim_value,{$get_field} AS total FROM vtiger_contractactivacode a 
                              RIGHT OUTER JOIN ({$rel_table_name}) s ON (s.{$rel_slave_table_id}=a.{$rel_main_table_id})
                              LEFT JOIN vtiger_departments b ON(b.departmentid=a.signdempart)
                              WHERE 1=1 {$stat_comm_date} 
                              AND a.signid IN(" . implode(',', $userid) . ")";
                }else {
                    if ($stat_index == 7) {
                        //拜访单数
                        $query_data = " SELECT {$group_comm_field} as stat_date,a.signid as stat_dim_value,{$get_field} AS total FROM vtiger_visitingorder b
                              INNER JOIN vtiger_crmentity c ON(b.visitingorderid=c.crmid)
                              RIGHT OUTER JOIN vtiger_contractactivacode a ON(b.related_to=a.accountid and modulename='Accounts')
                              LEFT JOIN vtiger_departments d ON(d.departmentid=a.signdempart)
                              WHERE c.deleted=0  {$stat_comm_date} 
                              AND a.signid IN(" . implode(',', $userid) . ")
                              AND c.smownerid=a.signid";
                    } else {
                        $query_data = " SELECT {$group_comm_field} as stat_date,a.signid as stat_dim_value,{$get_field} AS total FROM vtiger_contractactivacode a
                               WHERE 1=1 {$stat_comm_date} 
                               AND a.signid IN(" . implode(',', $userid) . ")";
                    }
                }
                $query_data = "SELECT LEFT(T.stat_date,{$date_len}) AS stat_date,T.stat_dim_value,{$stat_value} as total FROM ($query_data)  T
                        GROUP BY LEFT(T.stat_date,{$date_len}),T.stat_dim_value 
                        ORDER BY LEFT(T.stat_date,{$date_len})";

                $uresult_data=$db->pquery($query_data);
            }else{
                if($departmentid=="null"||empty($departmentid)){
                    $departmentid=array();
                    $departmentid[]='H1';
                }

                //部门不能超过10个
                for($i=0;$i<count($departmentid)&&$i<$max_col_count;++$i){
                    if($radio_stat_date_index == '2'){
                        $query_data .= " SELECT {$group_comm_field} as stat_date,a.signid as stat_dim_value,c.last_name,{$get_field} AS total FROM vtiger_contractactivacode a 
                              RIGHT OUTER JOIN ({$rel_table_name}) s ON (s.{$rel_slave_table_id}=a.{$rel_main_table_id})
                              LEFT JOIN vtiger_departments b ON(b.departmentid=a.signdempart)
                              LEFT JOIN vtiger_users c ON(a.signid = c.id)
                              WHERE 1=1 {$stat_comm_date} 
                              AND FIND_IN_SET('$departmentid[$i]',REPLACE(b.parentdepartment,'::',','))";

                    }else {
                        if ($stat_index == 7) {
                            //拜访单数
                            $query_data .= " SELECT {$group_comm_field} as stat_date,a.signid as stat_dim_value,e.last_name,{$get_field} AS total FROM vtiger_visitingorder b
                              INNER JOIN vtiger_crmentity c ON(b.visitingorderid=c.crmid)
                              RIGHT OUTER JOIN vtiger_contractactivacode a ON(b.related_to=a.accountid and b.modulename='Accounts')
                              LEFT JOIN vtiger_departments d ON(d.departmentid=a.signdempart)
                              LEFT JOIN vtiger_users e ON(a.signid = e.id)
                              WHERE c.deleted=0  {$stat_comm_date} 
                              AND FIND_IN_SET('$departmentid[$i]',REPLACE(d.parentdepartment,'::',','))
                              AND c.smownerid=a.signid";
                        } else {
                            $query_data .= " SELECT {$group_comm_field} as stat_date,a.signid as stat_dim_value,c.last_name,{$get_field} AS total FROM vtiger_contractactivacode a
                              LEFT JOIN vtiger_departments b ON(b.departmentid=a.signdempart)
                              LEFT JOIN vtiger_users c ON(a.signid = c.id)
                              WHERE 1=1 {$stat_comm_date} 
                              AND FIND_IN_SET('$departmentid[$i]',REPLACE(b.parentdepartment,'::',','))";
                        }
                    }

                    if($i < count($departmentid) -1 ){
                        $query_data .= " UNION ALL ";
                    }
                }
                $query_data = "SELECT LEFT(T.stat_date,{$date_len}) AS stat_date,T.stat_dim_value,T.last_name,{$stat_value} as total FROM ($query_data)  T
                        GROUP BY LEFT(T.stat_date,{$date_len}),T.stat_dim_value 
                        ORDER BY LEFT(T.stat_date,{$date_len})";

                //echo $query_data;
                $uresult_data=$db->pquery($query_data);

                $num_user=$db->num_rows($uresult_data);
                if($num_user>0){
                    for($i=0;$i<$num_user&&$i<$max_col_count;++$i){
                        if(in_array($db->query_result($uresult_data,$i,'stat_dim_value'),$arr['newdepartmentid'])){
                            continue;
                        }
                        $arr['newdepartmentid'][]=$db->query_result($uresult_data,$i,'stat_dim_value');
                        $arr['newdepartment'][$db->query_result($uresult_data,$i,'stat_dim_value')]=$db->query_result($uresult_data,$i,'last_name');
                        $arr_stat_dim_value[] = $db->query_result($uresult_data,$i,'stat_dim_value');
                    }
                }
            }
        }

        // echo $query_data;
        $num1=$db->num_rows($uresult_data);
        if($num1>0){
            if($date_diff_days > 60){
                //按月显示
                $arr_date = TyunReportanalysis_Record_Model::getMonthFromRange($startdate,$enddate);
            }else {
                //按日显示
                $arr_date = TyunReportanalysis_Record_Model::getDateFromRange($startdate, $enddate);
            }

            for($i=0;$i<count($arr_date);$i++){
                $arr['stat_date'][]= $arr_date[$i];

                for($u=0;$u<count($arr_stat_dim_value);$u++){
                    $cur_dim_id = $arr_stat_dim_value[$u];
                    $check = false;
                    for($j=0;$j<$num1;++$j){
                        $cur_dt = $db->query_result($uresult_data,$j,'stat_date');
                        $cur_userid_tmp = $db->query_result($uresult_data,$j,'stat_dim_value');
                        if(strtotime($cur_dt) == strtotime($arr_date[$i]) && $arr_stat_dim_value[$u] == $cur_userid_tmp){
                            $arr['tyun_reports_'.strtolower($cur_dim_id)][]= $db->query_result($uresult_data,$j,'total');
                            $check = true;
                            break;
                        }
                    }
                    if($check == false){
                        $arr['tyun_reports_'.strtolower($cur_dim_id)][]= 0;
                    }
                }
            }
            $flag=1;
        }

        if($flag==0){
            $arr=Array('newdepartmentid' => Array('hno'),'newdepartment' => Array('hno' => '暂无数据'),'tyun_reports_hno' => Array(0),'stat_date' => Array('暂无数据'));
            /*$response = new Vtiger_Response();
            $response->setEmitType(Vtiger_Response::$EMIT_JSON);
            $response->setResult($arr);
            $response->emit();
            exit;*/
        }

        /*//echo $query;
        $result=$db->pquery($query,array());
        $num=$db->num_rows($result);
        //$arr=array();
        if($num>0){
            for($i=0;$i<$num;$i++){
                //print_r($db->query_result_rowdata($result,$i));
                foreach($db->query_result_rowdata($result,$i) as $key=>$value){
                    if(is_numeric($key)){
                        continue;
                    }else{
                        $arr[$key][]=$value;
                    }
                }
            }
        }else{
            $arr=Array('newdepartmentid' => Array('hno'),'newdepartment' => Array('hno' => '暂无数据'),'daycounts_hno' => Array(0),'dayforp_hno' => Array(0),'daysaler_hno' => Array(0),'dayvisiting_hno' => Array(0),'createdtime' => Array('暂无数据'));
        }*/
        //print_r($arr);
        //exit;
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($arr);
        $response->emit();
    }

    public function getUsers(Vtiger_Request $request){
        $departmentid=$request->get('department');
        if(!empty($departmentid)&&$departmentid!='H1'){
            $userid=getDepartmentUser($departmentid);
            $where=getAccessibleUsers('TyunReportanalysis','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $listQuery = ' AND id in('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers('TyunReportanalysis','List',false);
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
    private function checkDate($date,$format='Y-m-d'){
        if(!strtotime($date)){
           return false;
        }
        if(date($format,strtotime($date))==$date){
            return true;
        }
        return false;
    }
    //单击取得
    public function getdetaillist(Vtiger_Request $request){
        $dataIndex=$request->get('seriesIndex');
        $datatime=$request->get('datetime');
        $userid=$request->get('datauserid');
        if(strlen($datatime)==7){
            $tempdate=" left(####,7)='{$datatime}'";
        }else{
            $tempdate=" left(####,10)='{$datatime}'";
        }
        $fliter=$request->get('fliter');
        if($fliter=='thisweek'){
            $lastday=date('Y-m-d',strtotime("Sunday"));
            $firstday=date('Y-m-d',strtotime("$lastday -6 days"));
            $tempdate = "  AND LEFT(####,10) BETWEEN '{$firstday}' AND '{$lastday}'";
        }else if($fliter=='thismonth'){
            $firstday = date('Y-m-01');
            $lastday = date('Y-m-d',strtotime("$firstday +1 month -1 day"));
            $tempdate = "  AND LEFT(####,10) BETWEEN '{$firstday}' AND '{$lastday}'";
        }
        $userid=ltrim($userid,'user');
        if(is_numeric($userid)){
            $sql=" AND vtiger_crmentity.smownerid={$userid}";
        }else{
            //if(!empty($userid)&&$userid!='H1'){
                $userid=strtoupper($userid);
                $usersid=getDepartmentUser($userid);
                $where=getAccessibleUsers('TyunReportanalysis','List',true);
                if($where!='1=1'){
                    $where=array_intersect($where,$usersid);
                }else{
                    $where=$usersid;
                }
                $sql = ' AND vtiger_crmentity.smownerid in('.implode(',',$where).')';
            /*}else{
                $where=getAccessibleUsers('TyunReportanalysis','List',false);
                if($where!='1=1'){
                    $sql =' AND vtiger_crmentity.smownerid '.$where;
                }else{
                    $sql='';
                }
            }*/

        }
        switch($dataIndex){
            //每日新增客户数
            case 0:
                $query="SELECT
                            IFNULL(vtiger_account.accountname,'') AS accountname,
                            IFNULL(vtiger_account.servicetype,'') AS servicetype,
                            IFNULL(vtiger_account.accountrank,'') AS accountrank,
                            IFNULL(vtiger_account.linkname,'') AS linkname,
                            IFNULL(vtiger_departments.departmentname,'--') AS department,
                            IFNULL(vtiger_users.last_name,'--'	) AS smownerid,
                            IFNULL(vtiger_account.industry,'') AS industry,
                            IFNULL(vtiger_account.annual_revenue,'') AS annual_revenue,
                            IFNULL(vtiger_account.address,'') AS address,
                            IFNULL(vtiger_account.makedecision,'') AS makedecision,
                            IFNULL(vtiger_account.business,'') AS business,
                            IFNULL(vtiger_account.regionalpartition,'') AS regionalpartition,
                            IFNULL(vtiger_account.title,'') AS title,
                            IFNULL(vtiger_account.leadsource,'') AS leadsource,
                            IFNULL(vtiger_account.linkname,'') AS linkname,
                            IFNULL(vtiger_account.businessarea,'') AS businessarea,
                            IFNULL(vtiger_crmentity.createdtime,'') AS createdtime,
                            IFNULL(vtiger_account.customerproperty,'') AS customerproperty,
                            IFNULL(vtiger_account.accountid,'') AS accountid
                        FROM
                            vtiger_account
                        LEFT JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid
                        LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
                        LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id
                        LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                        WHERE
                            1 = 1
                        AND vtiger_crmentity.deleted = 0
                        AND {$tempdate}
                        {$sql}
                        ORDER BY
                            vtiger_account.mtime DESC LIMIT 1000";
                $query=str_replace('####','vtiger_crmentity.createdtime',$query);
                break;


            //每日新增40%客户数
            case 1:
                $query="SELECT
                            IFNULL(vtiger_crmentity.createdtime,'') AS createdtime,
                            IFNULL(vtiger_account.accountname,'') AS accountname,
                            IFNULL(vtiger_account.accountid,'') AS accountid,
                            IFNULL(vtiger_account.makedecision,'') AS makedecision,
                            IFNULL((SELECT (SELECT departmentname	FROM vtiger_departments WHERE	departmentid =(SELECT	departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id	LIMIT 1)) AS last_name FROM vtiger_users	WHERE	vtiger_crmentity.smownerid = vtiger_users.id),'--'	) AS department,
                            IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname	FROM vtiger_departments WHERE	departmentid =(SELECT	departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id	LIMIT 1)),''),']',(IF(`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users	WHERE	vtiger_crmentity.smownerid = vtiger_users.id),'--'	) AS smownerid,
                            IFNULL((SELECT startdate FROM vtiger_visitingorder WHERE vtiger_visitingorder.related_to=vtiger_account.accountid ORDER BY startdate ASC limit 1),'') AS firstvisittime
                        FROM
                            vtiger_account
                        LEFT JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid
                        LEFT JOIN vtiger_accountrankhistory ON vtiger_accountrankhistory.accountid = vtiger_account.accountid
                        WHERE
                            vtiger_accountrankhistory.newaccountrank = 'forp_notv'
                        AND {$tempdate}
                        {$sql} LIMIT 1000";
                $query=str_replace('####','vtiger_accountrankhistory.createdtime',$query);
                break;
            //每日拜访客户数
            case 2:
                $query="SELECT
                            IFNULL(vtiger_departments.departmentname,''	) AS department,
                            IFNULL(CONCAT(vtiger_users.last_name,if(vtiger_users.`status`='Active','','[离职]')),'') AS smownerid,
                            IFNULL(left(vtiger_visitingorder.startdate,10),'') AS visitingtime,
                            IFNULL(vtiger_account.accountname,'') AS accountname,
                            IFNULL(vtiger_account.visitingtimes,'') AS visitingtimes,
                            IFNULL(vtiger_visitingorder.contacts,'') AS contacts,
                            IFNULL(vtiger_visitingorder.purpose,'') AS purpose,
                            vtiger_visitingorder.related_to AS accountid,
                            IFNULL(if(vtiger_account.linkname=vtiger_visitingorder.contacts,vtiger_account.title,(SELECT title FROM vtiger_contactdetails WHERE vtiger_contactdetails.name LIKE concat(vtiger_visitingorder.contacts,'%') LIMIT 1)),'') as title,
                            IFNULL(if(vtiger_account.linkname=vtiger_visitingorder.contacts,vtiger_account.makedecision,(SELECT makedecision FROM vtiger_contactdetails WHERE vtiger_contactdetails.name LIKE concat(vtiger_visitingorder.contacts,'%') LIMIT 1)),'') as makedecision,
                            IFNULL((SELECT GROUP_CONCAT(vtiger_products.productname) FROM vtiger_products WHERE  FIND_IN_SET(vtiger_products.productid,REPLACE(vtiger_account.servicetype,' |##| ',','))),'') as servicetypename
                        FROM
                                vtiger_visitingorder
                        LEFT JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid = vtiger_crmentity.crmid
                        LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_visitingorder.related_to
                        LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_visitingorder.extractid
                        LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id
                        LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                        WHERE
                            vtiger_visitingorder.modulestatus='c_complete'
                        AND vtiger_visitingorder.related_to>0
                        AND vtiger_crmentity.deleted = 0
                        AND {$tempdate}{$sql} LIMIT 1000";
                $query=str_replace('vtiger_crmentity.smownerid','vtiger_visitingorder.extractid',$query);
                $query=str_replace('####','vtiger_visitingorder.startdate',$query);
                break;
            //每日成交客户数
            case 3:
                $query="SELECT
                            IFNULL(vtiger_account.accountname,'') AS accountname,
                            vtiger_servicecontracts.sc_related_to AS accountid,
                            IFNULL(left(vtiger_users.user_entered,10),'') AS user_entered,
                            IFNULL(vtiger_departments.departmentname,'') AS department,
                            IFNULL(vtiger_account.industry,'') AS industry,
                            IFNULL(vtiger_account.visitingtimes,'') AS visitingtimes,
                            IFNULL(CONCAT(vtiger_users.last_name,IF(vtiger_users.`status` = 'Active','','[离职]')),'') as smownerid,
                            IFNULL((SELECT ss.last_name FROM vtiger_users ss WHERE ss.id=vtiger_users.reports_to_id),'') AS report_name,
                            IFNULL(vtiger_servicecontracts.total,'') AS salescommission,
                            IF(vtiger_servicecontracts.total-(IFNULL(vtiger_receivedpayments.unit_price,0))>0,'否','是') AS until_price,
                            replace(IFNULL(vtiger_servicecontracts.productsearchid,''),'<br>',',　') AS productname,
                            IFNULL(vtiger_servicecontracts.firstreceivepaydate,'') as saleorderlastdealtime,
                            IFNULL(vtiger_servicecontracts.servicecontractsid,'') as c_id,
                            IFNULL(vtiger_servicecontracts.contract_no,'') as c_no
                        FROM
                            vtiger_servicecontracts
                        LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid
                        LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_servicecontracts.sc_related_to
                        LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_servicecontracts.productid
                        LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.relatetoid = vtiger_servicecontracts.servicecontractsid
                        LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_servicecontracts.receiveid
                        LEFT JOIN vtiger_user2department ON vtiger_user2department.userid = vtiger_users.id
                        LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid = vtiger_departments.departmentid
                        WHERE
                            1 = 1
                        AND vtiger_crmentity.deleted = 0
                        AND vtiger_servicecontracts.modulestatus='c_complete'
                        AND vtiger_servicecontracts.firstreceivepaydate IS NOT NULL
                        AND vtiger_servicecontracts.firstreceivepaydate != ''
                        AND vtiger_account.accountid>0
                        AND {$tempdate}
                        {$sql}
                        GROUP BY vtiger_servicecontracts.servicecontractsid LIMIT 1000";
                $query=str_replace('vtiger_crmentity.smownerid','vtiger_servicecontracts.receiveid',$query);
                $query=str_replace('####','vtiger_servicecontracts.firstreceivepaydate',$query);
                break;
            default:
                break;

        }

        $db=PearDatabase::getInstance();
        $result=$db->pquery($query,array());
        $num=$db->num_rows($result);
        if($num<1){
            $arrlist=array();
            $response = new Vtiger_Response();
            $response->setEmitType(Vtiger_Response::$EMIT_JSON);
            $response->setResult($arrlist);
            $response->emit();
            return;
        }
        $arrlist=array();
        switch($dataIndex){
            case 0:
                for($i=0;$i<$num;$i++){
                    $arrlist[$i]=$db->fetchByAssoc($result);
                    $arrlist[$i]['industry']=vtranslate($arrlist[$i]['industry'],'Accounts');
                    $arrlist[$i]['regionalpartition']=vtranslate($arrlist[$i]['regionalpartition'],'Accounts');
                    $arrlist[$i]['createdtime']=substr($arrlist[$i]['createdtime'],0,10);
                    $arrlist[$i]['accountrank']=vtranslate($arrlist[$i]['accountrank']);
                }
                break;
            case 1:
                for($i=0;$i<$num;$i++){
                    $arrlist[$i]=$db->fetchByAssoc($result);
                    $arrlist[$i]['makedecision']=vtranslate($arrlist[$i]['makedecision'],'Accounts');
                    $arrlist[$i]['createdtime']=substr($arrlist[$i]['createdtime'],0,10);
                    $arrlist[$i]['firstvisittime']=substr($arrlist[$i]['firstvisittime'],0,10);
                }
                break;
            case 2:
                for($i=0;$i<$num;$i++){
                    $arrlist[$i]=$db->fetchByAssoc($result);
                    $arrlist[$i]['makedecision']=vtranslate($arrlist[$i]['makedecision'],'Accounts');
                    //$arrlist[$i]['purpose']=substr($arrlist[$i]['purpose'],0,10)==false?'':substr($arrlist[$i]['purpose'],0,10);
                }
                break;
            case 3:
                for($i=0;$i<$num;$i++){
                    $arrlist[$i]=$db->fetchByAssoc($result);
                    $arrlist[$i]['industry']=vtranslate($arrlist[$i]['industry'],'Accounts');
                    $arrlist[$i]['data_entered']=substr($arrlist[$i]['data_entered'],0,10);
                    $arrlist[$i]['saleorderlastdealtime']=substr($arrlist[$i]['saleorderlastdealtime'],0,10);
                    $arrlist[$i]['visitingtimes']=$arrlist[$i]['visitingtimes']==null?0:$arrlist[$i]['visitingtimes'];
                }
                break;


            default:
                break;

        }

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($arrlist);
        $response->emit();
    }

    function date_compare($a, $b)
    {
        $t1 = strtotime($a['stat_date']);
        $t2 = strtotime($b['stat_date']);
        return $t1 - $t2;
    }
}
