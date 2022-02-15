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
class SContractNoGeneration_Record_Model extends Vtiger_Record_Model {

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
            global $adb;
            $query="SELECT company_codeno FROM `vtiger_company_code` WHERE company_code=?";
            $result=$adb->pquery($query,array($_POST['company_code']));
            $dataresult=$adb->query_result_rowdata($result,0);
            $sql.=" and company_codeno='".$dataresult['company_codeno']."'";
            $company_code=$dataresult['company_codeno'];
            $_POST['company_codeno']=$dataresult['company_codeno'];
            $updatesql.="company_code='".$_POST['company_code']."',";
            $updatesql.="company_codeno='".$dataresult['company_codeno']."',";
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
}
