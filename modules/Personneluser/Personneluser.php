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

class Personneluser extends CRMEntity {
    var $log;
    /**
     * @var PearDatabase
     */
    var $db;
    // Stored fields
    var $id;
    var $authenticated = false;
    var $error_string;
    var $is_admin;
    var $deleted;

    var $tab_name = Array('vtiger_users');
    var $tab_name_index = Array('vtiger_users'=>'id');

    var $table_name = "vtiger_users";
    var $table_index= 'id';

    // This is the list of fields that are in the lists.
    var $list_link_field= 'last_name';

    var $list_mode;
    var $popup_type;
    var $last_modifiedtime;  //最后的修改时间，用于判断是否要重新生成文件
    var $search_fields = Array(
        'Name'=>Array('vtiger_users'=>'last_name'),
        'Email'=>Array('vtiger_users'=>'email1')
    );
    var $search_fields_name = Array(
        'Name'=>'last_name',
        'Email'=>'email1',
        'Department'=>'department'
    );

    var $module_name = "Users";

    var $object_name = "User";
    var $user_preferences;
    var $homeorder_array = array('HDB','ALVT','PLVT','QLTQ','CVLVT','HLT','GRT','OLTSO','ILTI','MNL','OLTPO','LTFAQ', 'UA', 'PA');

    var $encodeFields = Array("first_name", "last_name", "description");

    // This is used to retrieve related fields from form posts.
    var $additional_column_fields = Array('reports_to_name');

    var $sortby_fields = Array('status','email1','email2','phone_work','is_admin','user_name','last_name');

    // This is the list of vtiger_fields that are in the lists.
    var $list_fields = Array(
        'Last Name'=>Array('vtiger_users'=>'last_name'),
        'Role Name'=>Array('vtiger_user2role'=>'roleid'),
        //'Role Name'=>Array('vtiger_user2departmentid'=>'departmentid'),
        'User Name'=>Array('vtiger_users'=>'user_name'),
        'Status'=>Array('vtiger_users'=>'status'),
        'Email'=>Array('vtiger_users'=>'email1'),
        'Admin'=>Array('vtiger_users'=>'is_admin'),
        'Phone'=>Array('vtiger_users'=>'phone_work'),
        'Department'=>array('vtiger_departments'=>'departmentid')
    );
    var $list_fields_name = Array(
        'Last Name'=>'last_name',

        'Role Name'=>'roleid',
        'User Name'=>'user_name',
        'Status'=>'status',
        'Email'=>'email1',

        'Admin'=>'is_admin',
        'Phone'=>'phone_work',
        'Department'=>'departmentid'
    );

    //Default Fields for Email Templates -- Pavani
    var $emailTemplate_defaultFields = array('first_name','last_name','title','department','phone_home','phone_mobile','signature','email1','email2','address_street','address_city','address_state','address_country','address_postalcode');

    var $popup_fields = array('last_name');

    // This is the list of fields that are in the lists.
    var $default_order_by = "user_name";
    var $default_sort_order = 'ASC';

    var $record_id;
    var $new_schema = true;

    var $DEFAULT_PASSWORD_CRYPT_TYPE; //'BLOWFISH', /* before PHP5.3*/ MD5;

    //Default Widgests
    var $default_widgets = array('PLVT', 'CVLVT', 'UA');
    var $datasource='';
    var $current_user_roles='';


    /** constructor function for the main user class
    instantiates the Logger class and PearDatabase Class
     *
     */

    function Personneluser() {
        /*$this->log = LoggerManager::getLogger('user');
        $this->log->debug("Entering Users() method ...");
        $this->db = PearDatabase::getInstance();
        $this->DEFAULT_PASSWORD_CRYPT_TYPE = (version_compare(PHP_VERSION, '5.3.0') >= 0)?
            'PHP5.3MD5': 'MD5';
        $this->column_fields = getColumnFields('Users');


        $this->column_fields['ccurrency_name'] = '';
        $this->column_fields['currency_code'] = '';
        $this->column_fields['currency_symbol'] = '';
        $this->column_fields['conv_rate'] = '';
        $this->log->debug("Exiting Users() method ...");*/
    }
}
?>
