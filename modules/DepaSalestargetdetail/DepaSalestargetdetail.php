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
class DepaSalestargetdetail extends CRMEntity {
	var $log;
	var $db;
	var $table_name = "vtiger_depasalestargetdetail";
	var $table_index= 'salestargetdetailid';
	//var $tab_name = Array('vtiger_crmentity','vtiger_account','vtiger_accountbillads','vtiger_accountscf','vtiger_accountshipads');
	//var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_account'=>'accountid','vtiger_accountbillads'=>'accountaddressid','vtiger_accountscf'=>'accountid','vtiger_accountshipads'=>'accountaddressid');
    var $tab_name = Array('vtiger_depasalestargetdetail');
    var $tab_name_index = Array('vtiger_depasalestargetdetail'=>'salestargetdetailid');
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('weeknum', 'salestargetid');
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

	function DepaSalestargetdetail() {
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('DepaSalestargetdetail');
	}

	/** Function to handle module specific operations when saving a entity
	*/
	function save_module($module) {
	}

    // 定时 更新每个用的实际数据
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
