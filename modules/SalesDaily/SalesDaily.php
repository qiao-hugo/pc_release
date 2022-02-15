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
class SalesDaily extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_salesdaily_basic';
    var $table_index= 'salesdailybasicid';
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
    var $tab_name = Array('vtiger_salesdaily_basic');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_salesdaily_basic'   => 'salesdailybasicid',);

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

    /*var $relatedmodule_list=array('Approval');
    var $relatedmodule_fields=array(
        'Approval'=>array(
            'createid'=>'批复人',
            'createtime'=>'批复时间',
            'description'=>'批复内容'
    ));*/

   var $relatedmodule_list=array('SalesSummaryReport');
    var $relatedmodule_fields=array(
        'SalesSummaryReport'=>array('taxpayers_no'=>'签名'),

    );


    // For Popup window record selection
    var $popup_fields = Array('salesdailybasicid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'salesdailybasicid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'salesdailybasicid';

    // Required Information for enabling Import feature
    var $required_fields = Array('salesdailybasicid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('salesdailybasicid');

    var $default_order_by = 'salesdailybasicid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('salesdailybasicid');
    

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
        global $current_user;
        $datetime=$_POST['dailydatetime'];
        $datetimenow=date("Y-m-d H:i:s");
        if(empty($_REQUEST['record'])){
            $recordid=$this->id;
            if(!empty($_POST['prevcandealrecordid'])){
                foreach($_POST['prevcandealrecordid'] as $key=>$value){
                    $prevcandealdeleted=$_POST['prevcandealdeleted'][$value]==1?1:0;
                    if($prevcandealdeleted){
                        $sql='INSERT INTO `vtiger_salesdailycandeal`(salesdailybasicid,accountid,contactname,mobile,title,accountcontent,productname,quote,firstpayment,issigncontract,deleted,dailydatetime,deleteddate) select ?,accountid,contactname,mobile,title,accountcontent,productname,quote,firstpayment,?,?,?,? from vtiger_salesdailycandeal where salesdailycandealid=?';
                        $tempcanddeal=array($recordid,$_POST['prevcandealissigncontract'][$value],$_POST['prevcandealdeleted'][$value],$datetime,$datetimenow,$value);
                    }else{
                        $sql='INSERT INTO `vtiger_salesdailycandeal`(salesdailybasicid,accountid,contactname,mobile,title,accountcontent,productname,quote,firstpayment,issigncontract,dailydatetime) select ?,accountid,contactname,mobile,title,accountcontent,productname,quote,firstpayment,?,? from vtiger_salesdailycandeal where salesdailycandealid=?';
                        $tempcanddeal=array($recordid,$_POST['prevcandealissigncontract'][$value],$datetime,$value);
                    }
                    $this->db->pquery($sql,$tempcanddeal);
                }
            }
        }else{
            $recordid=$_POST['record'];
            $this->db->pquery('DELETE FROM `vtiger_salesdailydaydeal` WHERE vtiger_salesdailydaydeal.salesdailybasicid=?',array($recordid));
            $this->db->pquery('DELETE FROM `vtiger_salesdailyfournotv` WHERE salesdailybasicid=?',array($recordid));
            $this->db->pquery('DELETE FROM `vtiger_salesdailynextdayvisit` WHERE salesdailybasicid=?',array($recordid));
            if(!empty($_POST['editcandeal'])){
                foreach($_POST['editcandeal'] as $key=>$value){
                    $editcandealdeleted=$_POST['editcandealdeleted'][$value]==1?1:0;
                    $sql='UPDATE `vtiger_salesdailycandeal` SET accountcontent=?,isupdatedeleted=?,productname=?,quote=?,firstpayment=?,issigncontract=?,deleted=? WHERE salesdailycandealid=?';
                    $this->db->pquery($sql,array($_POST['editcandealaccountcontent'][$value],$_POST['editcandealdeleted'][$value],$_POST['editcandealproduct'][$value],$_POST['editcandealquote'][$value],$_POST['editcandealfirstpayment'][$value],$_POST['editcandealissigncontract'][$value],$editcandealdeleted,$value));
                }
            }
            /*
            if(!empty($_POST['editdaydeal'])){
                foreach($_POST['editdaydeal'] as $key=>$value){
                    $editdealmarketprice=$_POST['editdaydealmarketprice'][$value];//市场价
                    $editdealdealamount=$_POST['editdaydealdealamount'][$value];//成交价
                    $editdealfirstpayment=$_POST['editdaydealfirstpayment'][$value];//首付款
                    $editdealstepprice=$_POST['editdealstepprice'][$value];//成本价
                    if($editdealmarketprice <=0 || $editdealdealamount <=0 || $editdealfirstpayment<=0){
                        //防止意外小于0的情况
                        $editdaydealarrivalamounts=0;
                    }
                    //成交价大于市场价
                    if($editdealdealamount>=$editdealmarketprice){
                        if($editdealstepprice>1){
                            $editdaydealarrivalamounts=$editdealfirstpayment-$editdealfirstpayment/$editdealdealamount*$editdealstepprice;
                        }else{
                            $editdaydealarrivalamounts=$editdealfirstpayment;
                        }
                    }else{
                        $discount=$editdealdealamount/$editdealmarketprice;
                        if($discount>=0.75){
                            if($editdealstepprice>1){
                                $editdaydealarrivalamounts=$editdealfirstpayment*$discount-$editdealfirstpayment/$editdealdealamount*$editdealstepprice;
                            }else{
                                $editdaydealarrivalamounts=$editdealfirstpayment*$discount;
                            }
                        }else{
                            $editdaydealarrivalamounts=0;
                        }
                    }
                    $editdaydealisupdate=$_POST['editdaydealisupdate'][$value]==1?1:0;
                    if(!empty($editdaydealisupdate)){
                        $sql='update `vtiger_salesdailydaydeal` SET productid=?,marketprice=?,dealamount=?,firstpayment=?,arrivalamount=?,arrivalamountc=?,costprice=?,paymentnature=?,allamount=?,isupdatedeleted=1,deletedid=?,deleteddate=?,deleted=1 WHERE salesadailydaydealid=?';
                        $temparr=array($_POST['editdaydealproduct'][$value],$_POST['editdaydealmarketprice'][$value],$_POST['editdaydealdealamount'][$value],$_POST['editdaydealfirstpayment'][$value],$editdaydealarrivalamounts,$_POST['editdaydealarrivalamount'][$value],$_POST['editdealstepprice'][$value],$_POST['editdaypaymentnature'][$value],$_POST['editdaydealallamount'][$value],$current_user->id,$datetimenow,$value);
                    }else{
                        $sql='update `vtiger_salesdailydaydeal` SET productid=?,marketprice=?,dealamount=?,firstpayment=?,arrivalamount=?,arrivalamountc=?,costprice=?,paymentnature=?,allamount=? WHERE salesadailydaydealid=?';
                        $temparr=array($_POST['editdaydealproduct'][$value],$_POST['editdaydealmarketprice'][$value],$_POST['editdaydealdealamount'][$value],$_POST['editdaydealfirstpayment'][$value],$editdaydealarrivalamounts,$_POST['editdaydealarrivalamount'][$value],$_POST['editdealstepprice'][$value],$_POST['editdaypaymentnature'][$value],$_POST['editdaydealallamount'][$value],$value);
                    }
                    $this->db->pquery($sql,$temparr);
                }
            }*/
        }

        if(!empty($_POST['fnvaccount'])) {
            $Notv = '';

            foreach ($_POST['fnvaccount'] as $key => $value) {
                $mangerreturnendtime = date("Y-m-d H:00:00",(strtotime("+ 2 day",strtotime(substr($_POST['fnvstartdate'][$value],0,10)))+12*60*60));

                $Notv .= '(' . $recordid . ',' . $key . ",'" .
                    $_POST['fnvaccountsmownerid'][$value] . "','" .
                    $_POST['fnvvisitingorder'][$value] . "','" .
                    vtranslate($_POST['fnvleadsource'][$value],'Accounts') . "','" .
                    $_POST['fnvcontacts'][$value] . "','" .
                    $_POST['fnvmobile'][$value] . "','" .
                    $_POST['fnvtitle'][$value] . "','" .
                    $_POST['fnvaccountname'][$value] . "','" .
                    $_POST['fnvstartdate'][$value] . "','" .
                    $mangerreturnendtime . "'),";
            }
            $Notv = trim($Notv, ',');
            if (!empty($Notv)) {
                $sql = 'INSERT INTO vtiger_salesdailyfournotv(salesdailybasicid,accountid,accountsmownerid,visitingorderid,leadsource,linkname,mobile,title,accountname,startdatetime,mangereturnendtime) VALUES' . $Notv;
                $this->db->pquery($sql, array());
            }
        }
        if(!empty($_POST['candealaccount'])) {
            $candeal = '';
            $arrcandealaccount=array();
            foreach ($_POST['candealaccount'] as $key => $value) {
                if(!in_array($value,$arrcandealaccount)) {
                    $arrcandealaccount[]=$value;
                    $candeal .= '(' . $recordid . ',' . $key . ",'" .
                        $_POST['candeallinkname'][$value] . "','" .
                        $_POST['candealmobile'][$value] . "','" .
                        $_POST['candealtitle'][$value] . "','" .
                        $_POST['candealaccountcontent'][$value] . "','" .
                        $_POST['candealproduct'][$value] . "','" .
                        $_POST['candealquote'][$value] . "','" .
                        $_POST['candealfirstpayment'][$value] . "',0,'" .
                        $datetime . "'),";
                }
            }
            $candeal = trim($candeal, ',');
            if (!empty($candeal)) {
                $sql = 'INSERT INTO `vtiger_salesdailycandeal`(salesdailybasicid,accountid,contactname,mobile,title,accountcontent,productname,quote,firstpayment,issigncontract,dailydatetime) values' . $candeal;
                $this->db->pquery($sql, array());
            }

        }
        if(!empty($_POST['daydealaccountid'])) {
            $daydeal = '';
            foreach ($_POST['daydealaccountid'] as $key => $value) {
                $daydealmarketprice=$_POST['daydealmarketprice'][$value];//市场价
                $daydealdealamount=$_POST['daydealdealamount'][$value];//成交价
                $daydealfirstpayment=$_POST['daydealfirstpayment'][$value];//首付款
                $daydealstepprice=$_POST['daydealstepprice'][$value];//成本价
                if($daydealmarketprice <=0 || $daydealdealamount <=0 || $daydealfirstpayment<=0){
                    //防止意外小于0的情况
                    $daydealarrivalamounts=$_POST['daydealarrivalamount'][$value];

                }else {
                    //成交价大于市场价
                    if ($daydealdealamount >= $daydealmarketprice) {
                        if ($daydealstepprice > 1) {
                            $daydealarrivalamounts = $daydealfirstpayment - $daydealfirstpayment / $daydealdealamount * $daydealstepprice;
                        } else {
                            $daydealarrivalamounts = $daydealfirstpayment;
                        }

                    } else {
                        $discount = $daydealdealamount / $daydealmarketprice;
                        if ($discount >= 0.75) {
                            if ($daydealstepprice > 1) {
                                $daydealarrivalamounts = $daydealfirstpayment * $discount - $daydealfirstpayment / $daydealdealamount * $daydealstepprice;
                            } else {
                                $daydealarrivalamounts = $daydealfirstpayment * $discount;
                            }
                        } else {
                            $daydealarrivalamounts = 0;
                        }

                    }
                }
                $daydeal .= '(' . $recordid . ',' . $key . ",'" .
                    $_POST['daydealproduct'][$value] . "','" .
                    $_POST['daydealmarketprice'][$value] . "','" .
                    $_POST['daydealdealamount'][$value] . "','" .
                    $_POST['daydealpaymentnature'][$value] . "','" .
                    $_POST['datdealallamount'][$value] . "','" .
                    $_POST['daydealfirstpayment'][$value] . "','" .
                    $_POST['daydealvisitingordernum'][$value] . "','" .
                    $_POST['daydealoldcustomers'][$value] . "','" .
                    $_POST['daydealindustry'][$value] . "','" .
                    $_POST['daydealvisitingobj'][$value] . "','" .
                    $_POST['daydealwithvisitor'][$value] . "','" .
                    $_POST['daydealproductname'][$value] . "','" .
                    $_POST['daydealstepprice'][$value] . "','" .
                    $daydealarrivalamounts . "','" .
                    $_POST['daydealarrivalamount'][$value] . "'),";
            }
            $daydeal = trim($daydeal, ',');
            if (!empty($daydeal)) {
                $sql = 'INSERT INTO vtiger_salesdailydaydeal(salesdailybasicid,accountid,productid,marketprice,dealamount,paymentnature,allamount,firstpayment,visitingordercount,oldcustomers,industry,visitingobj,withvisitor,productname,costprice,arrivalamount,arrivalamountc) VALUES' . $daydeal;
                $this->db->pquery($sql, array());
            }
        }
        if(!empty($_POST['ndvvisitingorder'])) {
            $nextdayvisit = '';
            $nextday = date('Y-m-d', strtotime("+1 day"));
            foreach ($_POST['ndvvisitingorder'] as $key => $value) {
                $nextdayvisit .= '(' . $recordid . ',' . $key . ",'" .
                    $nextday . "','" .
                    $_POST['ndvcontacts'][$value] . "','" .
                    $_POST['ndvtitle'][$value] . "','" .
                    $_POST['ndvvisitingordernum'][$value] . "','" .
                    $_POST['ndvaccount'][$value] . "','" .
                    $_POST['ndvaccountname'][$value] . "','" .
                    $_POST['ndvpurpose'][$value] . "','" .
                    $_POST['ndvvisitingorderwithvisitor'][$value] . "'),";
            }
            $nextdayvisit = trim($nextdayvisit, ',');
            if (!empty($nextdayvisit)) {
                $sql = 'INSERT INTO vtiger_salesdailynextdayvisit(salesdailybasicid,visitingorderid,visitingorderstartdate,contacts,title,visitingordernum,accountid,accountname,purpose,withvisitor) VALUES' . $nextdayvisit;
                $this->db->pquery($sql, array());
            }
        }
    }
    function retrieve_entity_info($record, $module){
        parent::retrieve_entity_info($record, $module);
        global $currentView,$current_user;

        $time=strtotime(date("Y-m-d",strtotime($this->column_fields['createdtime'])))+24*3600+9*3600;
        $now=time();
        if($currentView=='Edit') {
            if($this->column_fields['isguarantee']==1){
                throw new AppException('日报已批复,不允许修改!');
                exit;
            }
            if (($time < $now) ) {
                throw new AppException('超过最后允许修改时间!');
                exit;
            }
            $allSuperior = getAllSuperiorIds($this->column_fields['smownerid']);

            if ($current_user->is_admin=='off' && !in_array($current_user->id,array_merge($allSuperior,array($this->column_fields['smownerid'])))) {
                throw new AppException('不允许修改别人的日报');
                exit;
            }
        }

    }


}
?>
