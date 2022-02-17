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
class Invoicesign extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_invoicesign';
    var $table_index= 'invoicesignid';
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
    var $tab_name = Array('vtiger_invoicesign');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_invoicesign'   => 'invoicesignid',);

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
    var $popup_fields = Array('invoicesignid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'invoicesignid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'invoicesignid';

    // Required Information for enabling Import feature
    var $required_fields = Array('invoicesignid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('invoicesignid');

    var $default_order_by = 'invoicesignid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('invoicesignid');

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
       /* if(empty($_REQUEST['record'])){
            $date=date('Y-m-d H:i:s');
            global $current_user;
            $sql="update vtiger_billing set createdtime='{$date}',smownerid={$current_user->id},modifiedtime='{$date}',modifiedby={$current_user->id} where billingid={$this->id}";
            $this->db->pquery($sql,array());
        }else{
            $date=date('Y-m-d H:i:s');
            global $current_user;
            $sql="update vtiger_billing set modifiedby={$current_user->id},modifiedtime='{$date}' where billingid={$_REQUEST['record']}";
            $this->db->pquery($sql,array());
        }*/
    }
   function retrieve_entity_info($record, $module){
        parent::retrieve_entity_info($record, $module);
       throw new AppException('无效访问');
       exit;
        global $currentView,$current_user;
        //print_r($this->column_fields);
        if($this->column_fields['modulestatus']=='c_complete'){
            if($currentView=='Edit'){
                //编辑或详细视图状态下非管理员的权限验证
                $id=$current_user->id;
                $userids=getDepartmentUser('H25');
                if(!in_array($id,$userids) && $id!=1){
                    throw new AppException('开票信息已锁定,请联系财务人员修改！');
                    exit;
                }

            }
        }else{
            $query="SELECT * FROM vtiger_invoice WHERE taxpayers_no=? AND registeraddress=? AND telephone=? AND accountnumber=? AND modulestatus='c_complete'";
            $result=$this->db->pquery($query,array($this->column_fields['taxpayers_no'],$this->column_fields['registeraddress'],$this->column_fields['telephone'],$this->column_fields['accountnumber']));
            $num=$this->db->num_rows($result);
            if($num>0){
                $date=date('Y-m-d H:i:s');
                $query="update vtiger_billing set locktime=?,modulestatus=? where billingid=?";
                $this->db->pquery($query,array($date,'c_complete',$record));
            }
        }
    }
}
?>
