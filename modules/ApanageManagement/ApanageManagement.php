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

class ApanageManagement extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_apanagemanagement';
    var $table_index= 'userid';
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
    var $tab_name = Array('vtiger_apanagemanagement');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_amanagementrelate'   => 'userid',
        'vtiger_apanagemanagement'   => 'userid',
        );

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
    var $popup_fields = Array('apanagemanagementid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'apanagemanagementid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'apanagemanagementid';

    // Required Information for enabling Import feature
    var $required_fields = Array('apanagemanagementid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('apanagemanagementid');

    var $default_order_by = 'apanagemanagementid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('apanagemanagementid');
    /*var $relatedmodule_list=array('VisitImprovement');
    //'Potentials','Quotes'
    var $relatedmodule_fields=array(
        'VisitImprovement'=>array('userid'=>'userid','datetime'=>'datetime','remark'=>'remark'),
    );*/
    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    /**
     * ???????????????
     * @param unknown $module
     */
    function save_module($module)
    {
    }
    function retrieve_entity_info($record, $module) {
        global $adb, $log, $app_strings;

        // INNER JOIN is desirable if all dependent table has entries for the record.
        // LEFT JOIN is desired if the dependent tables does not have entry.
        $join_type = 'LEFT JOIN';

        // Tables which has multiple rows for the same record
        // will be skipped in record retrieve - need to be taken care separately.
        $multirow_tables = NULL;
        if (isset($this->multirow_tables)) {
            $multirow_tables = $this->multirow_tables;
        } else {
            $multirow_tables = array(
                'vtiger_campaignrelstatus',
                'vtiger_attachments',
                //'vtiger_inventoryproductrel',
                //'vtiger_cntactivityrel',
                'vtiger_email_track'
            );
        }

        // Lookup module field cache
        $cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
        if ($cachedModuleFields === false) {
            // Pull fields and cache for further use
            $tabid = getTabid($module);

            $sql0 = "SELECT fieldname, fieldid, fieldlabel, columnname, tablename, uitype, typeofdata,presence FROM vtiger_field WHERE tabid=?";
            // NOTE: Need to skip in-active fields which we will be done later.
            $result0 = $adb->pquery($sql0, array($tabid));
            if ($adb->num_rows($result0)) {
                while ($resultrow = $adb->fetch_array($result0)) {
                    // Update cache
                    VTCacheUtils::updateFieldInfo(
                        $tabid, $resultrow['fieldname'], $resultrow['fieldid'], $resultrow['fieldlabel'], $resultrow['columnname'], $resultrow['tablename'], $resultrow['uitype'], $resultrow['typeofdata'], $resultrow['presence']
                    );
                }
                // Get only active field information
                $cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
            }
        }

        if ($cachedModuleFields) {
            $column_clause = '';
            $from_clause   = '';
            $where_clause  = '';
            $limit_clause  = ' LIMIT 1'; // to eliminate multi-records due to table joins.

            $params = array();
            $required_tables = $this->tab_name_index; // copies-on-write

            foreach ($cachedModuleFields as $fieldinfo) {
                if (in_array($fieldinfo['tablename'], $multirow_tables)) {
                    continue;
                }
                // Added to avoid picking shipping tax fields for Inventory modules, the shipping tax detail are stored in vtiger_inventoryshippingrel
                // table, but in vtiger_field table we have set tablename as vtiger_inventoryproductrel.
                if(($module == 'Invoice' || $module == 'Quotes' || $module == 'SalesOrder' || $module == 'PurchaseOrder')
                    && stripos($fieldinfo['columnname'], 'shtax') !== false) {
                    continue;
                }

                // Alias prefixed with tablename+fieldname to avoid duplicate column name across tables
                // fieldname are always assumed to be unique for a module
                $column_clause .=  $fieldinfo['tablename'].'.'.$fieldinfo['columnname'].' AS '.$this->createColumnAliasForField($fieldinfo).',';
            }


            if (isset($required_tables['vtiger_crmentity'])) {
                // 2014-10-29 young ????????????????????????????????????????????????
                $column_clause .= 'vtiger_crmentity.deleted';

                $from_clause  = ' vtiger_crmentity';
                unset($required_tables['vtiger_crmentity']);
                foreach ($required_tables as $tablename => $tableindex) {
                    if (in_array($tablename, $multirow_tables)) {
                        // Avoid multirow table joins.
                        continue;
                    }
                    $from_clause .= sprintf(' %s %s ON %s.%s=%s.%s', $join_type,
                        $tablename, $tablename, $tableindex, 'vtiger_crmentity', 'crmid');
                }
                $where_clause .= ' vtiger_crmentity.crmid=?';
            }else{
                $column_clause .= $this->table_name.'.'.$this->table_index;
                $where_clause .= ' '.$this->table_name.'.'.$this->table_index.'=?';
                $from_clause  = $this->table_name;
            }


            $params[] = $record;
            $lefeJoin='LEFT JOIN vtiger_amanagementrelate ON vtiger_apanagemanagement.userid=vtiger_amanagementrelate.userid';
            $sql = sprintf('SELECT %s FROM %s %s WHERE %s %s', $column_clause, $from_clause,$lefeJoin, $where_clause, $limit_clause);
            //echo $sql;
            //exit;

            $result = $adb->pquery($sql, $params);
            if (!$result || $adb->num_rows($result) < 1) {
                throw new Exception($app_strings['LBL_RECORD_NOT_FOUND'], -1);
            } else {
                $resultrow = $adb->query_result_rowdata($result);
                if (!empty($resultrow['deleted'])) {
                    throw new Exception($app_strings['LBL_RECORD_DELETE'], 1);
                }

                foreach ($cachedModuleFields as $fieldinfo) {
                    $fieldvalue = '';
                    $fieldkey = $this->createColumnAliasForField($fieldinfo);
                    //Note : value is retrieved with a tablename+fieldname as we are using alias while building query
                    if (isset($resultrow[$fieldkey])) {
                        $fieldvalue = $resultrow[$fieldkey];
                    }
                    $this->column_fields[$fieldinfo['fieldname']] = $fieldvalue;
                }
            }
        }

        $this->column_fields['record_id'] = $record;
        $this->column_fields['record_module'] = $module;
    }
}
?>
