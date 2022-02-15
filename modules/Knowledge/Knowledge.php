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
class Knowledge extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_knowledge';
    var $table_index= 'knowledgeid';
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
    var $tab_name = Array('vtiger_knowledge');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_knowledge'   => 'knowledgeid',);

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array (
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
  

    );
    var $list_fields_name = Array(
        /* Format: Field Label => fieldname */
 
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
    var $popup_fields = Array('knowledgeid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'knowledgeid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'knowledgeid';

    // Required Information for enabling Import feature
    var $required_fields = Array('knowledgeid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('knowledgeid');

    var $default_order_by = 'knowledgeid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('knowledgeid');

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
    	if(empty($_REQUEST['record'])){
	    	$date=date('Y-m-d H:i:s');
	    	global $current_user;
           
	    	$sql="update vtiger_knowledge set knowledgedate='{$date}',author={$current_user->id} where knowledgeid='{$this->id}'";
	    	$this->db->pquery($sql,array());
    	}
    }
    function retrieve_entity_info($record, $module){
    	parent::retrieve_entity_info($record, $module);
    	global $currentView,$current_user;

    	if($_REQUEST['view']=="Detail" && $this->column_fields['author']!=$current_user->id){
    		global $adb;
    		$readcount=$adb->run_query_allrecords("select id from vtiger_knowledgecount where userid='{$current_user->id}' AND relate_id={$record}");
    		if(empty($readcount)){
    			$adb->pquery("INSERT INTO vtiger_knowledgecount (`userid`,`relate_id`,`readtime`) VALUES('{$current_user->id}','{$record}',now())",array());
    			$adb->pquery("update  vtiger_knowledge set knowledgecount=knowledgecount+1 where knowledgeid={$record}",array());
    		}
    	}
    	if($currentView=='Edit' && $current_user->id!=$this->column_fields['author'] && !$current_user->superadmin){
    		//throw new AppException('你没有操作权限！');
    		//exit;
    	}
    }
}
?>
