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
 * Vtiger Entity Record Model Class
 */
class ServiceContractsPrint_Record_Model extends Vtiger_Record_Model {

    public static function getMosaicSql($row){
        $sql='';
        $updatesql='';
        if(!empty($row['prefix'])){
            $sql.=" and prefix='".$row['prefix']."'";
            $prefix=$row['prefix'];
            $updatesql.="prefix='".$row['prefix']."',";
        }else{
            $sql.=" and prefix=''";
            $updatesql.="prefix='',";
            $prefix='';
        }
        if($row['company_code']==1){
            $sql.=" and company_code='".$_POST['company_code']."'";
            $company_code=$_POST['company_code'];
            $updatesql.="company_code='".$_POST['company_code']."',";
        }else{
            $sql.=" and company_code=''";
            $company_code='';
            $updatesql.="company_code='',";
        }
        if($row['products_code']==1){
            $sql.=" and products_code='".$_POST['products_code']."'";
            $products_code=$_POST['products_code'];
            $products_codeflag=1;
            $updatesql.="products_code='".$_POST['products_code']."',";
        }else{
            $sql.=" and products_code=''";
            $products_code='';
            $products_codeflag=2;
            $updatesql.="products_code='',";
        }
        if($row['year_code']==1){
            $sql.=' and year_code='.date("Y");
            $updatesql.='year_code='.date("Y").',';
            $year_code=date("Y");
        }else{
            $sql.=" and year_code=''";
            $updatesql.="year_code='',";
            $year_code='';
        }
        if($row['month_code']==1){
            $sql.=" and month_code='".date("m")."'";
            $updatesql.="month_code='".date("m")."',";
            $month_code=date("m");
        }else{
            $sql.=" and month_code=''";
            $updatesql.="month_code='',";
            $month_code='';
        }

        if($row['day_code']==1){
            $sql.=" and day_code='".date("d")."'";
            $updatesql.="day_code='".date("d")."',";
            $day_code=date("d");
        }else{
            $sql.=" and day_code=''";
            $updatesql.="day_code='',";
            $day_code='';
        }

        if($row['number']>0){
            $sql.=' and number='.$row['number'];
            $updatesql.='number='.$row['number'].',';
        }

        if(!empty($row['interval_code_one'])){
            $sql.=" and interval_code_one='".$row['interval_code_one']."'";
            $updatesql.="interval_code_one='".$row['interval_code_one']."',";
            $interval_code_one=$row['interval_code_one'];
        }else{
            $sql.=" and interval_code_one=''";
            $updatesql.="interval_code_one='',";
            $interval_code_one='';
        }
        if(!empty($row['interval_code'])){
            $sql.=" and interval_code='".$row['interval_code']."'";
            $updatesql.="interval_code='".$row['interval_code']."',";
            $interval_code=$row['interval_code'];
        }else{
            $sql.=" and interval_code=''";
            $updatesql.="interval_code='',";
            $interval_code='';
        }

        if(!empty($row['interval_code_two'])){
            $sql.=" and interval_code_two='".$row['interval_code_two']."'";
            $updatesql.="interval_code_two='".$row['interval_code_two']."',";
            $interval_code_two=$row['interval_code_two'];
        }else{
            $sql.=" and interval_code_two=''";
            $updatesql.="interval_code_two='',";
            $interval_code_two='';
        }
        $codeprefix=$prefix.$interval_code.$company_code.$interval_code_one.$products_code.$interval_code_two.$year_code.$month_code.$day_code;
        return array('codeprefix'=>$codeprefix,'updatesql'=>$updatesql,'sql'=>$sql,'products_codeflag'=>$products_codeflag);
    }
    public static function getistrue(){
        $db=PearDatabase::getInstance();
        $query="SELECT * FROM `vtiger_servicecontracts_rule` WHERE servicecontractsruleid=?";
        $result=$db->pquery($query,array($_POST['sc_related_to']));
        $num=$db->num_rows($result);
        if($num){
            $row=$db->query_result_rowdata($result);
            $MosaicSql=self::getMosaicSql($row);
            $query='SELECT maxnumber FROM `vtiger_scontractnogeneration` WHERE 1=1'.$MosaicSql['sql'].' ORDER BY scontractnogenerationid DESC limit 1';
            $result=$db->pquery($query,array());
            $num=$db->num_rows($result);
            $str='1';
            $max_limit=str_pad($str,$row['number'],1,STR_PAD_LEFT);
            $max_limit=$max_limit*9;
            $arr['products_codeflag']=$MosaicSql['products_codeflag'];
            if($num){
                $scrow=$db->query_result_rowdata($result);
                $maxnumber=$scrow['maxnumber'];
                $arr['max_limit']=$max_limit-$maxnumber;
                return $arr;
            }else{
                $arr['max_limit']=$max_limit;
               return  $arr;
            }
        }else{
            return array('max_limit'=>0);
        }
    }

    public function pushToContractList($entity,$userid,$recordId){
        $db=PearDatabase::getInstance();
        $contractclassification = $entity['contractclassification'];
        if($contractclassification=='SupplierContracts'){
            $_REQUES['record'] = '';
            $request = new Vtiger_Request($_REQUES, $_REQUES);
            $request->set('contract_no', $entity['servicecontracts_no']);
            $request->set('assigned_user_id', $userid);
            $request->set('modulestatus', 'c_stamp');
            $request->set('contractattribute', 'standard');
            $request->set('contract_classification', $entity['contract_classification']);

            $request->set('module', 'SupplierContracts');
            $request->set('view', 'Edit');
            $request->set('action', 'Save');
            //$ressorder = new ServiceContracts_Save_Action();
            $ressorder = new Vtiger_Save_Action();
            $ressorderecord = $ressorder->saveRecord($request);
            $serviceconrecord=$ressorderecord->getId();
            $db->pquery('UPDATE vtiger_suppliercontracts SET modulestatus=\'c_stamp\',companycode=?,invoicecompany=(SELECT invoicecompany FROM `vtiger_invoicecompany` WHERE companycode=? LIMIT 1),contract_no=?,servicecontractsprintid=?,servicecontractsprint=? WHERE suppliercontractsid=?', array($entity['company_code'],$entity['company_code'],$entity['servicecontracts_no'],$recordId, $recordId . '-8',$serviceconrecord ));

        }else{
            $query = "SELECT vtiger_parent_contracttype_contracttyprel.parent_contracttypeid,vtiger_contract_type.contract_type,vtiger_contract_type.bussinesstype FROM vtiger_parent_contracttype_contracttyprel LEFT JOIN vtiger_servicecontracts_print ON (FIND_IN_SET(vtiger_servicecontracts_print.contract_template,vtiger_parent_contracttype_contracttyprel.contract_template) and vtiger_servicecontracts_print.contract_template !='') LEFT JOIN vtiger_contract_type ON vtiger_contract_type.contract_typeid=vtiger_parent_contracttype_contracttyprel.contract_typeid WHERE vtiger_servicecontracts_print.servicecontractsprintid=? limit 1";
            $result = $db->pquery($query, array($recordId));//取合同的类型
            $_REQUES['record'] = '';
            $request = new Vtiger_Request($_REQUES, $_REQUES);
            $request->set('contract_no', $entity['servicecontracts_no']);
            $request->set('assigned_user_id', $userid);
            $request->set('modulestatus', 'c_stamp');
            $request->set('contract_classification', $entity['contract_classification']);
            $request->set('isautoclose', 1);
            $request->set('signaturetype','papercontract');
            $request->set('contractattribute','standard');
            $bussinesstype='';
            if ($db->num_rows($result)) {
                $contracttypearr = $db->raw_query_result_rowdata($result);
                $_REQUEST['parent_contracttypeid'] = $contracttypearr['parent_contracttypeid'];
                $request->set('contract_type', $contracttypearr['contract_type']);
                $request->set('parent_contracttypeid', $contracttypearr['parent_contracttypeid']);
                $request->set('bussinesstype', $contracttypearr['bussinesstype']);
                $bussinesstype=$contracttypearr['bussinesstype'];
            }
            $request->set('module', 'ServiceContracts');
            $request->set('view', 'Edit');
            $request->set('action', 'Save');
            //$ressorder = new ServiceContracts_Save_Action();
            $ressorder = new Vtiger_Save_Action();
            $ressorderecord = $ressorder->saveRecord($request);
            $serviceconrecord=$ressorderecord->getId();
            $db->pquery('UPDATE vtiger_servicecontracts SET modulestatus=\'c_stamp\',companycode=?,invoicecompany=(SELECT invoicecompany FROM `vtiger_invoicecompany` WHERE companycode=? LIMIT 1),contract_no=?,servicecontractsprintid=?,servicecontractsprint=?,bussinesstype=? WHERE servicecontractsid=?', array($entity['company_code'],$entity['company_code'],$entity['servicecontracts_no'],$recordId, $recordId . '-8',$bussinesstype,$serviceconrecord ));
        }
        return $serviceconrecord;
    }
}
