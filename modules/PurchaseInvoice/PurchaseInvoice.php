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

include_once('config.php');
require_once('include/logging.php');
require_once('include/utils/utils.php');
require_once('user_privileges/default_module_view.php');

// Account is used to store vtiger_account information.
class PurchaseInvoice extends CRMEntity {
	var $log;
	var $db;

	var $table_name = "vtiger_purchaseinvoice";
	var $table_index= 'purchaseinvoiceid';
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
    var $tab_name = Array('vtiger_crmentity', 'vtiger_purchaseinvoice');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_purchaseinvoice'   => 'purchaseinvoiceid');

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
    var $popup_fields = Array('purchaseinvoiceid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'purchaseinvoiceid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'purchaseinvoiceid';

    // Required Information for enabling Import feature
    var $required_fields = Array('purchaseinvoiceid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('purchaseinvoiceid');

    var $default_order_by = 'purchaseinvoiceid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('purchaseinvoiceid');
	
	/**	Constructor which will set the column_fields in this object
	 */
	function __construct() {
		$this->log =LoggerManager::getLogger('PurchaseInvoice');
		$this->log->debug("Entering Invoice() method ...");
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('PurchaseInvoice');
		$this->log->debug("Exiting Invoice method ...");

	}


	/** Function to handle the module specific save operations

	*/

	function save_module($module) {
       
	}


    /**节点审核时到了指定节点抓取时间
     * 后置事件
     * @param Vtiger_Request $request
     */
    function workflowcheckafter(Vtiger_Request $request){
        $stagerecordid=$request->get('stagerecordid');
        $record=$request->get('record');
        $db=PearDatabase::getInstance();
        /*$recordModel = Vtiger_Record_Model::getInstanceById($record, 'PurchaseInvoice');
        $entity=$recordModel->entity->column_fields;*/

        $query="SELECT
                    vtiger_workflowstages.workflowstagesflag
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'PurchaseInvoice'";
        $result=$db->pquery($query,array($stagerecordid));

        $currentflag=$db->query_result($result,0,'workflowstagesflag');
        switch($currentflag){
            // 责任会计审核
            case 'responsibility_accounting':
                global $current_user;
                $examine = $request->get('examine');
                $month = $request->get('month');
                // 选择签收，发票状态改为“记账联已签收”；选择无需认证，发票状态改为“无需认证”
                if ($examine == '1') {
                    $purchasestatus = 'p_sign';
                } else {
                    $purchasestatus = 'p_noauthentication';
                }

                $sql = "UPDATE vtiger_purchaseinvoice SET purchasestatus=?, authenticationmonth=? WHERE purchaseinvoiceid=?";
                $db->pquery($sql, array($purchasestatus, $month, $record));
                break;
            case 'invoice_manager' :   //合同管理员审核 第二步
                $sql = "UPDATE vtiger_purchaseinvoice SET purchasestatus=? WHERE purchaseinvoiceid=?";
                $db->pquery($sql, array('p_buckleconnecting', $record));
                break;
            case 'authentication' :   //第三步审核
                $sql = "UPDATE vtiger_purchaseinvoice SET purchasestatus=? WHERE purchaseinvoiceid=?";
                $db->pquery($sql, array('p_authentication', $record));
                break;
            default :
                break;
        }
    }








    function retrieve_entity_info($record, $module){
        parent::retrieve_entity_info($record, $module);
        global $currentView,$current_user;

        if($currentView=='Edit') {
            if($this->column_fields['issubmit']==1) {
                if (Users_Privileges_Model::isPermitted('PurchaseInvoice', ' IdcEDIT')) {
                    if($this->column_fields['isidentified']==1){
                        throw new AppException('已认证状态不允许修改!');
                        exit;
                    }

                }else{
                    throw new AppException('审核状态不允许修改!');
                    exit;
                }
            }
        }

    }


}

?>