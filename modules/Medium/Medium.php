<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header$
 * Description:  Defines the Account SugarBean Account entity with the necessary
 * methods and variables.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('data/CRMEntity.php');
require_once('data/Tracker.php');
// Faq is used to store vtiger_faq information.
class Medium extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_medium';
    var $table_index= 'mediumid';
    var $column_fields = Array();

    /** Indicator if this is a custom module or standard module */
    var $IsCustomModule = true;

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array();

    //var $tab_name = Array('vtiger_crmentity','vtiger_account','vtiger_accountbillads','vtiger_accountscf','vtiger_accountshipads');
    //var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_account'=>'accountid','vtiger_accountbillads'=>'accountaddressid','vtiger_accountscf'=>'accountid','vtiger_accountshipads'=>'accountaddressid');
    var $tab_name = Array('vtiger_medium');
    var $tab_name_index = Array('vtiger_medium'=>'mediumid');

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
        //'schoolname'=> Array('School', 'schoolname')
    );
    var $search_fields_name = Array(
        /* Format: Field Label => fieldname */
        //'schoolname'=> 'schoolname'
    );

    /*var $search_fields = Array(
        'schoolname'=> Array('School', 'schoolname')
    );
    var $search_fields_name = Array(
    /* Format: Field Label => fieldname 
        'schoolname'=> 'schoolname'
    );*/


    // For Popup window record selection
    var $popup_fields = Array('mediumid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'mediumid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'mediumid';

    // Required Information for enabling Import feature
    var $required_fields = Array('mediumid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('mediumid');

    var $relatedmodule_list=array();
    var $relatedmodule_fields=array(
        //'Schoolcontacts'=>
                    //array('schoolcontactsname'=>'联系人','position'=>'职位','gendertype'=>'性别','phone'=>'手机','email'=>'email'),
    );

    var $related_module_table_index = array(
        //'ServiceContracts' => array('table_name' => 'vtiger_servicecontracts', 'table_index' => 'servicecontractsid', 'rel_index' => 'sc_related_to'),

        //'Schoolrecruit'=>array('table_name' => 'vtiger_schoolrecruit', 'table_index' => 'schoolrecruitid', 'rel_index' => 'schoolid')

    );


    var $default_order_by = 'mediumid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('mediumid');

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
        if(empty($_REQUEST['record'])) {
            $recordid = $this->id;
        }else{
            $recordid=$_REQUEST['record'];
        }
        if(!empty($_POST['updateads'])){
            $adsname='adsname=CASE adsnameid ';
            $channelposition='channelposition=CASE adsnameid ';
            $majoradvertising='majoradvertising=CASE adsnameid ';
            $recentmaintenancetime='recentmaintenancetime=CASE adsnameid ';
            $billingmode='billingmode=CASE adsnameid ';
            $unitprice='unitprice=CASE adsnameid ';
            $cpcaverageprice='cpcaverageprice=CASE adsnameid ';
            $cpr='cpr=CASE adsnameid ';
            foreach($_REQUEST['updateads'] as $value){
                $adsname.=sprintf(" WHEN %d THEN '%s'",$value,$_REQUEST['uadsname'][$value]);
                $channelposition.=sprintf(" WHEN %d THEN '%s'",$value,$_REQUEST['uchannelposition'][$value]);
                $majoradvertising.=sprintf(" WHEN %d THEN '%s'",$value,$_REQUEST['umajoradvertising'][$value]);
                $recentmaintenancetime.=sprintf(" WHEN %d THEN '%s'",$value,$_REQUEST['urecentmaintenancetime'][$value]);
                $billingmode.=sprintf(" WHEN %d THEN '%s'",$value,$_REQUEST['ubillingmode'][$value]);
                $unitprice.=sprintf(" WHEN %d THEN '%s'",$value,$_REQUEST['uunitprice'][$value]);
                $cpcaverageprice.=sprintf(" WHEN %d THEN '%s'",$value,$_REQUEST['ucpcaverageprice'][$value]);
                $cpr.=sprintf(" WHEN %d THEN '%s'",$value,$_REQUEST['ucpr'][$value]);
            }
            $this->db->query('UPDATE vtiger_adsname SET deleted=1 WHERE adsnameid NOT IN('.implode(',',$_REQUEST['updateads']).')');
            $sql="UPDATE vtiger_adsname SET
                           {$adsname} ELSE adsname END,
                            {$channelposition} ELSE channelposition END,
                            {$majoradvertising} ELSE majoradvertising END,
                            {$recentmaintenancetime} ELSE recentmaintenancetime END,
                            {$billingmode} ELSE billingmode END,
                            {$unitprice} ELSE unitprice END,
                            {$cpcaverageprice} ELSE cpcaverageprice END,
                            {$cpr} ELSE cpr END
                            WHERE mediumid={$recordid}";
            $this->db->pquery($sql,array());

        }
        if(!empty($_POST['updatefirmpolicy'])){
            $consumetaskcompletion='consumetaskcompletion=CASE firmpolicyid ';
            $returnproportion='returnproportion=CASE firmpolicyid ';
            $salesauthority='salesauthority=CASE firmpolicyid ';
            $salesdirectorauthority='salesdirectorauthority=CASE firmpolicyid ';
            $vpauthority='vpauthority=CASE firmpolicyid ';
            $remarks='remarks=CASE firmpolicyid ';
            foreach($_REQUEST['updatefirmpolicy'] as $value){
                $consumetaskcompletion.=sprintf(" WHEN %d THEN '%s'",$value,$_REQUEST['uconsumetaskcompletion'][$value]);
                $returnproportion.=sprintf(" WHEN %d THEN '%s'",$value,$_REQUEST['ureturnproportion'][$value]);
                $salesauthority.=sprintf(" WHEN %d THEN '%s'",$value,$_REQUEST['usalesauthority'][$value]);
                $salesdirectorauthority.=sprintf(" WHEN %d THEN '%s'",$value,$_REQUEST['usalesdirectorauthority'][$value]);
                $vpauthority.=sprintf(" WHEN %d THEN '%s'",$value,$_REQUEST['uvpauthority'][$value]);
                $remarks.=sprintf(" WHEN %d THEN '%s'",$value,$_REQUEST['uremarks'][$value]);
            }
            $this->db->query('UPDATE vtiger_firmpolicy SET deleted=1 WHERE firmpolicyid NOT IN('.implode(',',$_REQUEST['updatefirmpolicy']).')');
            $sql="UPDATE vtiger_firmpolicy SET
                           {$consumetaskcompletion} ELSE consumetaskcompletion END,
                            {$returnproportion} ELSE returnproportion END,
                            {$salesauthority} ELSE salesauthority END,
                            {$salesdirectorauthority} ELSE salesdirectorauthority END,
                            {$vpauthority} ELSE vpauthority END,
                            {$remarks} ELSE remarks END
                            WHERE mediumid={$recordid}";
            $this->db->pquery($sql,array());

        }
        if(!empty($_POST['insetrads'])){
            $insertistr='';
            $tarray=array();
            foreach($_POST['insetrads'] as $key=>$value){
                $tarray[]=$recordid;
                $tarray[]=$_POST['adsname'][$value];
                $tarray[]=$_POST['channelposition'][$value];
                $tarray[]=$_POST['majoradvertising'][$value];
                $tarray[]=$_POST['recentmaintenancetime'][$value];
                $tarray[]=$_POST['billingmode'][$value];
                $tarray[]=$_POST['unitprice'][$value];
                $tarray[]=$_POST['cpcaverageprice'][$value];
                $tarray[]=$_POST['cpr'][$value];
                $insertistr.='(?,?,?,?,?,?,?,?,?),';
            }
            $insertistr=rtrim($insertistr,',');
            $sql="INSERT INTO vtiger_adsname(mediumid,adsname,channelposition,majoradvertising,recentmaintenancetime,billingmode,unitprice,cpcaverageprice,cpr) VALUES{$insertistr}";
            $this->db->pquery($sql,$tarray);
        }
        if(!empty($_POST['insertfirmpolicy'])){
            $insertistr='';
            $tarray=array();
            foreach($_POST['insertfirmpolicy'] as $key=>$value){
                $tarray[]=$recordid;
                $tarray[]=$_POST['consumetaskcompletion'][$value];
                $tarray[]=$_POST['returnproportion'][$value];
                $tarray[]=$_POST['salesauthority'][$value];
                $tarray[]=$_POST['salesdirectorauthority'][$value];
                $tarray[]=$_POST['vpauthority'][$value];
                $tarray[]=$_POST['remarks'][$value];
                $insertistr.='(?,?,?,?,?,?,?),';
            }
            $insertistr=rtrim($insertistr,',');
            $sql="INSERT INTO vtiger_firmpolicy(mediumid,consumetaskcompletion,returnproportion,salesauthority,salesdirectorauthority,vpauthority,remarks) VALUES{$insertistr}";
            $this->db->pquery($sql,$tarray);
        }

    }


}
?>
