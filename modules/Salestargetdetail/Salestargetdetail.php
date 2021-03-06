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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Accounts/Accounts.php,v 1.53 2005/04/28 08:06:45 rank Exp $
 * Description:  Defines the Account SugarBean Account entity with the necessary
 * methods and variables.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

include_once('config.php');
require_once('include/logging.php');
require_once('modules/Contacts/Contacts.php');
require_once('modules/Potentials/Potentials.php');
require_once('modules/Calendar/Activity.php');
require_once('modules/Documents/Documents.php');
require_once('modules/Emails/Emails.php');
require_once('include/utils/utils.php');
require_once('user_privileges/default_module_view.php');

// Account is used to store vtiger_account information.
class Salestargetdetail extends CRMEntity {
	var $log;
	var $db;
	var $table_name = "vtiger_salestargetdetail";
	var $table_index= 'salestargetdetailid';
	//var $tab_name = Array('vtiger_crmentity','vtiger_account','vtiger_accountbillads','vtiger_accountscf','vtiger_accountshipads');
	//var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_account'=>'accountid','vtiger_accountbillads'=>'accountaddressid','vtiger_accountscf'=>'accountid','vtiger_accountshipads'=>'accountaddressid');
    var $tab_name = Array('vtiger_salestargetdetail');
    var $tab_name_index = Array('vtiger_salestargetdetail'=>'salestargetdetailid');
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('weeknum', 'salestargetdetailid');
	var $entity_table = "vtiger_crmentity";

	var $column_fields = Array();

	var $sortby_fields = Array();

	//var $groupTable = Array('vtiger_accountgrouprelation','accountid');

	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(
			);

	var $list_fields_name = Array(
			);
	var $list_link_field= '';

	var $search_fields = Array(
			);

	var $search_fields_name = Array(
			);
	// This is the list of vtiger_fields that are required
	var $required_fields =  array();

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array();

	//Default Fields for Email Templates -- Pavani
	var $emailTemplate_defaultFields = array();

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'salestargetdetailid';
	var $default_sort_order = 'DESC';

	// For Alphabetical search
	var $def_basicsearch_col = '';
	var $related_module_table_index = array(
	);
	
	var $relatedmodule_list=array();
	//'Potentials','Quotes'
	var $relatedmodule_fields=array(
	
	);

	function Salestargetdetail() {
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('Salestargetdetail');
	}

	/** Function to handle module specific operations when saving a entity
	*/
	function save_module($module) {
		global $current_user;

		$recordid=$this->id;

		//??????
        if(!empty($_POST['inserti'])){
            $insertistr='';
            $t = array();
            foreach($_POST['inserti'] as $key=>$value){
            	$tarray=array();
            	$tarray['salestargetid']=$recordid;
                $tarray['weeknum']=$_POST['weeknum'][$value];
                $tarray['startdate']=$_POST['startdate'][$value];
                $tarray['enddate']=$_POST['enddate'][$value];
                $tarray['weekinvitationtarget']=$_POST['weekinvitationtarget'][$value];
                $tarray['weekinvitation']=$_POST['weekinvitation'][$value];
                $tarray['weekinvitationrate']=$_POST['weekinvitationrate'][$value];
                $tarray['weekinvitationremarks']=$_POST['weekinvitationremarks'][$value];
                $tarray['weekvisittarget']=$_POST['weekvisittarget'][$value];
                $tarray['weekvisit']=$_POST['weekvisit'][$value];
                $tarray['weekvisitrate']=$_POST['weekvisitrate'][$value];
                $tarray['weekvisitrateremarks']=$_POST['weekvisitrateremarks'][$value];
                $tarray['weekachievementtargt']=$_POST['weekachievementtargt'][$value];
                $tarray['weekachievement']=$_POST['weekachievement'][$value];
                $tarray['weekachievementrate']=$_POST['weekachievementrate'][$value];
                $tarray['weekachievementremarks']=$_POST['weekachievementremarks'][$value];
                $tarray['programme']=$_POST['programme'][$value];

                $t[] = $tarray;
            }

            // ??????????????????
            $t_temp_arr = array();
            foreach($t as $k=>$v) {
                $t_temp_arr[$v['weeknum']] = $v;
            }
            ksort($t_temp_arr);
            $t = array_values($t_temp_arr);

            // ?????????????????????????????????????????????????????? ??????????????????????????????
            foreach($t as $k=>$v) {
                if ($v['weekinvitationtarget'] > 0 || $v['weekvisittarget'] > 0 || $v['weekachievementtargt'] > 0 
                    || !empty($v['weekinvitationremarks']) || !empty($v['weekvisitrateremarks']) || !empty($v['weekachievementremarks'])
                    || !empty($v['programme'])) {

                    $t[$k]['ismodify'] = 1;
                } else {
                    $t[$k]['ismodify'] = 0;
                }
            }

            $invalue='';
            foreach($t as $value){
                $invalue.="('{$value['salestargetid']}', '{$value['weeknum']}', '{$value['startdate']}' , '{$value['enddate']}' , '{$value['weekinvitationtarget']}' , '{$value['weekinvitation']}' , '{$value['weekinvitationrate']}' , '{$value['weekinvitationremarks']}' , '{$value['weekvisittarget']}' , '{$value['weekvisit']}' , '{$value['weekvisitrate']}' , '{$value['weekvisitrateremarks']}', '{$value['weekachievementtargt']}', '{$value['weekachievement']}', '{$value['weekachievementrate']}', '{$value['weekachievementremarks']}', '{$value['programme']}',  '{$value['ismodify']}'),";
            }
            $invalue = rtrim($invalue,',');
            $keySql = array(
            	'salestargetid',
            	'weeknum', 
            	'startdate', 
            	'enddate', 
            	'weekinvitationtarget', 
            	'weekinvitation', 
            	'weekinvitationrate',
            	'weekinvitationremarks',
            	'weekvisittarget',
            	'weekvisit',
            	'weekvisitrate',
            	'weekvisitrateremarks',
            	'weekachievementtargt',
            	'weekachievement',
            	'weekachievementrate',
            	'weekachievementremarks',
            	'programme',
                'ismodify'
            );

            // ???????????????????????????4??????
            $sql = "select * from vtiger_salestargetdetail where salestargetid=?";
			$db=PearDatabase::getInstance();
			$sel_result = $db->pquery($sql, array($record));
			$res_cnt = $db->num_rows($sel_result);
			if ($res_cnt < 4) {
				$sql="INSERT INTO vtiger_salestargetdetail(" . implode(',', $keySql) . ") VALUES{$invalue}";
            	$db->pquery($sql, array());
			}
            // ??????????????????
            $sql = "update vtiger_salestarget set createtime=? where salestargetid=?";
            $db->pquery($sql, array(date('Y-m-d H:i:s'), $recordid));
        }

        //??????
        if(!empty($_REQUEST['updatei'])){
            //$salestargetid='salestargetid=CASE salestargetid ';
            $weeknum='weeknum=CASE salestargetdetailid ';
            $startdate='startdate=CASE salestargetdetailid ';
            $enddate='enddate=CASE salestargetdetailid ';
            $weekinvitationtarget='weekinvitationtarget=CASE salestargetdetailid ';
            $weekinvitation='weekinvitation=CASE salestargetdetailid ';
            $weekinvitationrate='weekinvitationrate=CASE salestargetdetailid ';
            $weekinvitationremarks='weekinvitationremarks=CASE salestargetdetailid ';
            $weekvisittarget='weekvisittarget=CASE salestargetdetailid ';
            $weekvisit='weekvisit=CASE salestargetdetailid ';
            $weekvisitrate='weekvisitrate=CASE salestargetdetailid ';
            $weekvisitrateremarks='weekvisitrateremarks=CASE salestargetdetailid ';
            $weekachievementtargt='weekachievementtargt=CASE salestargetdetailid ';
            $weekachievement='weekachievement=CASE salestargetdetailid ';
            $weekachievementrate='weekachievementrate=CASE salestargetdetailid ';
            $weekachievementremarks='weekachievementremarks=CASE salestargetdetailid ';
            $programme='programme=CASE salestargetdetailid ';
            $ismodify='ismodify=CASE salestargetdetailid ';

            foreach($_REQUEST['updatei'] as $value){
                $valueid = $_REQUEST['salestargetdetailid'][$value];

                $weeknum.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['weeknum'][$value]);
                $startdate.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['startdate'][$value]);
                $enddate.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['enddate'][$value]);
                $weekinvitationtarget.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['weekinvitationtarget'][$value]);
                $weekinvitation.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['weekinvitation'][$value]);
                $weekinvitationrate.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['weekinvitationrate'][$value]);
                $weekinvitationremarks.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['weekinvitationremarks'][$value]);
                $weekvisittarget.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['weekvisittarget'][$value]);
                $weekvisit.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['weekvisit'][$value]);
                $weekvisitrate.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['weekvisitrate'][$value]);
                $weekvisitrateremarks.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['weekvisitrateremarks'][$value]);
                $weekachievementtargt.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['weekachievementtargt'][$value]);
                $weekachievement.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['weekachievement'][$value]);
                $weekachievementrate.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['weekachievementrate'][$value]);
                $weekachievementremarks.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['weekachievementremarks'][$value]);
                $programme.=sprintf(" WHEN %d THEN '%s'",$valueid,$_REQUEST['programme'][$value]);

                // ??????????????????
                if ($_REQUEST['weekinvitationtarget'][$value] > 0 || $_REQUEST['weekvisittarget'][$value] > 0 || $_REQUEST['weekachievementtargt'][$value] > 0 
                    || !empty($_REQUEST['weekinvitationremarks'][$value]) || !empty($_REQUEST['weekvisitrateremarks'][$value]) 
                    || !empty($_REQUEST['weekachievementremarks'][$value])|| !empty($_REQUEST['programme'][$value])) {

                    $ismodify.=sprintf(" WHEN %d THEN '%s'",$valueid, '1');
                } else {
                    $ismodify.=sprintf(" WHEN %d THEN '%s'",$valueid, '0');
                }
            }

            $sql="UPDATE vtiger_salestargetdetail SET
                       {$weeknum} ELSE weeknum END,
                        {$startdate} ELSE startdate END,
                        {$enddate} ELSE enddate END,
                        {$weekinvitationtarget} ELSE weekinvitationtarget END,
                        {$weekinvitation} ELSE weekinvitation END,
                        {$weekinvitationrate} ELSE weekinvitationrate END,
                        {$weekinvitationremarks} ELSE weekinvitationremarks END,
                        {$weekvisittarget} ELSE weekvisittarget END,
                        {$weekvisit} ELSE weekvisit END,
                        {$weekvisitrate} ELSE weekvisitrate END,
                        {$weekvisitrateremarks} ELSE weekvisitrateremarks END,
                        {$weekachievementtargt} ELSE weekachievementtargt END,
                        {$weekachievement} ELSE weekachievement END,
                        {$weekachievementrate} ELSE weekachievementrate END,
                        {$weekachievementremarks} ELSE weekachievementremarks END,
                        {$programme} ELSE programme END,
                        {$ismodify} ELSE ismodify END
                        WHERE salestargetid={$recordid}";
            $this->db->pquery($sql,array());
        }


        // ???????????????????????????????????????????????? ????????????????????????????????????
        $sql = "select * from vtiger_salestargetdetail where salestargetid=? AND ismodify='1'";
        $db=$this->db;
        $sel_result = $db->pquery($sql, array($recordid));
        $res_cnt = $db->num_rows($sel_result);
        if ($res_cnt > 0) {
            $sql="update vtiger_salestarget set ismodify=1 where salestargetid=?";
            $db->pquery($sql, array($recordid));
        }/* else if($res_cnt = 0) {
            $sql="update vtiger_salestarget set ismodify=0 where salestargetid=?";
            $db->pquery($sql, array($recordid));
        }*/
	}

    // ?????? ??????????????????????????????
    public function timedTask() {
        $sql = "UPDATE vtiger_salestargetdetail  s
            SET weekinvitation = (
                SELECT
                    count(*)
                FROM
                    vtiger_visitingorder
                LEFT JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid = vtiger_crmentity.crmid
              LEFT JOIN vtiger_visitsign ON vtiger_visitsign.visitingorderid = vtiger_visitingorder.visitingorderid
                LEFT JOIN vtiger_salestarget ON vtiger_visitsign.userid = vtiger_salestarget.businessid
                WHERE s.salestargetid=vtiger_salestarget.salestargetid 
              AND str_to_date(vtiger_visitsign.signtime, '%Y-%m-%d') BETWEEN str_to_date(s.startdate, '%Y-%m-%d') AND str_to_date(s.enddate,'%Y-%m-%d')
            ),
            weekvisit = (
                SELECT
                    count(*)
                FROM
                    vtiger_visitingorder
                LEFT JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid = vtiger_crmentity.crmid
                LEFT JOIN vtiger_salestarget ON vtiger_visitingorder.extractid = vtiger_salestarget.businessid
                WHERE s.salestargetid=vtiger_salestarget.salestargetid
              AND vtiger_crmentity.createdtime BETWEEN str_to_date(s.startdate,'%Y-%m-%d') AND str_to_date(s.enddate,'%Y-%m-%d')
            ),
            weekachievement = (
                SELECT SUM(vtiger_salesdailydaydeal.arrivalamount) 
              FROM vtiger_salesdaily_basic 
              LEFT JOIN vtiger_salesdailydaydeal ON vtiger_salesdailydaydeal.salesdailybasicid=vtiger_salesdaily_basic.salesdailybasicid
                LEFT JOIN vtiger_salestarget ON vtiger_salesdaily_basic.smownerid = vtiger_salestarget.businessid
                WHERE s.salestargetid=vtiger_salestarget.salestargetid
                AND str_to_date(vtiger_salesdaily_basic.createdtime, '%Y-%m-%d') BETWEEN str_to_date(s.startdate,'%Y-%m-%d') AND str_to_date(s.enddate,'%Y-%m-%d')
            ),
            weekinvitationrate = 
                CONCAT( FORMAT(weekinvitation/weekinvitationtarget, 2) * 100, '%'),
            weekvisitrate = 
                CONCAT( FORMAT(weekvisit/weekvisittarget, 2) * 100, '%'),
            weekachievementrate = 
                CONCAT( FORMAT(weekachievement/weekachievementtargt, 2) * 100, '%')
            WHERE date_sub(curdate(),interval 1 day) BETWEEN str_to_date(s.startdate,'%Y-%m-%d') AND str_to_date(s.enddate,'%Y-%m-%d')";


        $sql = "UPDATE vtiger_salestarget s set 
                invitationnum = (
                    select sum(vtiger_salestargetdetail.weekinvitation) from vtiger_salestargetdetail
                    where s.salestargetid=vtiger_salestargetdetail.salestargetid
                ),
                visitnum = (
                    select sum(vtiger_salestargetdetail.weekvisit) from vtiger_salestargetdetail
                    where s.salestargetid=vtiger_salestargetdetail.salestargetid
                ),
                achievementnum = (
                    select sum(vtiger_salestargetdetail.weekachievement) from vtiger_salestargetdetail
                    where s.salestargetid=vtiger_salestargetdetail.salestargetid
                ),
                invitationrate = 
                    CONCAT( FORMAT(invitationnum/invitationtarget, 2) * 100, '%'),
                visitrate = 
                    CONCAT( FORMAT(visitnum/visittarget, 2) * 100, '%'),
                achievementrate = 
                    CONCAT( FORMAT(achievementnum/achievementtargt, 2) * 100, '%')


                WHERE date_sub(curdate(), interval 1 day) 
                BETWEEN str_to_date( CONCAT(s.`year`,'-', s.`month`,'-','01' ) ,'%Y-%m-%d') 
                AND date_add( str_to_date( CONCAT(s.`year`,'-', s.`month`,'-','01') ,'%Y-%m-%d'), interval 1 month)";

    }
}

?>
