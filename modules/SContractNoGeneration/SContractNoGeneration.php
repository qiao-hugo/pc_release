<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');

class SContractNoGeneration extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_scontractnogeneration';
    var $table_index= 'scontractnogenerationid';
    var $column_fields = Array();

    /** Indicator if this is a custom module or standard module */
    var $IsCustomModule = true;

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array();

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    var $tab_name = Array('vtiger_scontractnogeneration');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_scontractnogeneration'   => 'scontractnogenerationid',);

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array (
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
    	//'Subject'=> Array('visitingorder', 'subject'),

    );
    var $list_fields_name = Array(
        /* Format: Field Label => fieldname */
    	//'Subject'=> 'subject',
    );

    // Make the field link to detail view from list view (Fieldname)
    var $list_link_field = 'relmodule';

    // For Popup listview and UI type support
    var $search_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
 
    );
    var $search_fields_name = Array(
        /* Format: Field Label => fieldname */
    
    );

    // For Popup window record selection
    var $popup_fields = Array('scontractnogenerationid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'scontractnogenerationid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'scontractnogenerationid';

    // Required Information for enabling Import feature
    var $required_fields = Array('scontractnogenerationid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('scontractnogenerationid');

    var $default_order_by = 'scontractnogenerationid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('scontractnogenerationid');

    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    /**
     * 更新后处理
     * @param unknown $module
     */
    function save_module($module) {
        $query="SELECT * FROM `vtiger_servicecontracts_rule` WHERE servicecontractsruleid=?";
        $result=$this->db->pquery($query,array($_POST['sc_related_to']));
        $num=$this->db->num_rows($result);
        $contractclassification = $_POST['contractclassification'];
        $contract_classification = $_POST['contract_classification'];
        if($num>0){
            global $current_user;
            $smownerid=$current_user->id;
            $row=$this->db->query_result_rowdata($result);
            $MosaicSql=SContractNoGeneration_Record_Model::getMosaicSql($row);
            $updatesql=$MosaicSql['updatesql'];
            $query='SELECT maxnumber FROM `vtiger_scontractnogeneration` WHERE 1=1'.$MosaicSql['sql'].' ORDER BY scontractnogenerationid DESC limit 1';
            $result=$this->db->pquery($query,array());
            $num=$this->db->num_rows($result);
            $str='1';
            $max_limit=str_pad($str,$row['number'],1,STR_PAD_LEFT);
            $max_limit=$max_limit*9;
            $quantity=$_POST['quantity'];
            $insertsql='';
	    $constractsstatus='c_generated';
            if($_POST['signstatus']=='on'){
                $constractsstatus='c_print';
            }elseif (isset($_POST['signstatus']) && $_POST['signstatus']==2){
                $constractsstatus='c_stamp';
            }
            $datetime=date('Y-m-d H:i:s');
            $products_code=$MosaicSql['products_codeflag']==1?$_POST['products_code']:'';
            if($num){
                $scrow=$this->db->query_result_rowdata($result);
                $maxnumber=$scrow['maxnumber'];
                for($i=1;$i<=$quantity;$i++){
                    $newsequence=$maxnumber+$i;
                    if($newsequence>$max_limit){
                        $newsequence=$newsequence-1;
                        break;
                    }
                    $sequence=$MosaicSql['codeprefix'].str_pad($newsequence,$row['number'],0,STR_PAD_LEFT);
                    $insertsql.='('.$this->id.',\''.$_POST['company_codeno'].'\',\''.$products_code.'\',\''.$sequence.'\',\''.$_POST['contract_template'].'\',\''.$datetime.'\',\''.$constractsstatus.'\','.$smownerid.',\''.$contractclassification.'\',\''.$contract_classification.'\'),';
                }

            }else{
                for($i=1;$i<=$quantity;$i++){
                    $newsequence=$i;
                    if($i>$max_limit){
                        $newsequence=$i-1;
                        break;
                    }
                    $sequence=$MosaicSql['codeprefix'].str_pad($i,$row['number'],0,STR_PAD_LEFT);
                    $insertsql.='('.$this->id.',\''.$_POST['company_codeno'].'\',\''.$products_code.'\',\''.$sequence.'\',\''.$_POST['contract_template'].'\',\''.$datetime.'\',\''.$constractsstatus.'\','.$smownerid.',\''.$contractclassification.'\',\''.$contract_classification.'\'),';
                }
            }
            $insertsql=rtrim($insertsql,',');
            if(!empty($insertsql)){
                $sql='INSERT INTO vtiger_servicecontracts_print(scontractnogenerationid,company_code,products_code,servicecontracts_no,contract_template,createdtime,constractsstatus,smownerid,contractclassification,contract_classification) VALUES'.$insertsql;
                $this->db->pquery($sql,array());
            }
            $updatesql.='maxnumber='.$newsequence.',';
            $updatesql.='createdtime=\''.date('Y-m-d H:i:s').'\',';
            $generatednum=$this->id;
            $generatednum='scn'.str_pad($generatednum,9,0,STR_PAD_LEFT);
            $updatesql.='generatednum=\''.$generatednum.'\',';
            global $current_user;
            $updatesql.='contract_template=\''.$_POST['contract_template']."',";
            $updatesql.='smownerid='.$current_user->id;
            $sql='UPDATE vtiger_scontractnogeneration SET '.$updatesql.' WHERE scontractnogenerationid=?';
            $this->db->pquery($sql,array($this->id));
        }
    }
    function retrieve_entity_info($record, $module){
        parent::retrieve_entity_info($record, $module);
        global $currentView;
        if($currentView=='Edit'|| $currentView=='Detail'){
            throw new AppException('请在列表查看!');
            exit;
        }
    }
}
?>
