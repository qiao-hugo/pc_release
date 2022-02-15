<?php
/*************************************************************************************
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/6
 * Time: 10:52
 ***********************************************************************************/
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');
class AutoTask extends CRMEntity {
    var $db, $log;

    var $table_name = 'vtiger_autoworkflowentitys';
    var $table_index= 'autoworkflowentityid';
    var $column_fields = Array();
    var $IsCustomModule = true;
    var $customFieldTable = Array();
    var $tab_name = Array('vtiger_accountsmerge');
    var $tab_name_index = Array(
        'vtiger_autoworkflowentitys'   => 'autoworkflowentityid',);
    var $list_fields = Array (
        /* Format: Field Label => Array(tablename, columnname) */
    );
    var $list_fields_name = Array(
        /* Format: Field Label => fieldname */
    );
    var $list_link_field = 'relmodule';
    var $search_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
    );
    var $search_fields_name = Array(
        /* Format: Field Label => fieldname */
    );
    var $popup_fields = Array();
    var $sortby_fields = Array();
    var $def_basicsearch_col = '';
    var $def_detailview_recname = '';
    var $required_fields = Array();
    var $special_functions = Array();
    var $default_order_by = '';
    var $default_sort_order='';
    var $mandatory_fields = Array('');

    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    function save_module($module) {

    }
}