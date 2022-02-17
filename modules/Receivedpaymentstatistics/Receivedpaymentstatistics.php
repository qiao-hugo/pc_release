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

class Receivedpaymentstatistics extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity
    var $table_name = 'Receivedpaymentstatistics';
    var $table_index= 'statisticsid';
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
    var $tab_name = Array('vtiger_receivedpaymentstatistics');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_receivedpayments'   => 'statisticsid',);

    // Make the field link to detail view from list view (Fieldname)
    var $list_link_field = 'relmodule';

    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }  
}
?>
