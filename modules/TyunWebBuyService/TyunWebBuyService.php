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

class TyunWebBuyService extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity
    var $table_name = 'vtiger_activationcode';
    var $table_index= 'activationcodeid';
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
    var $tab_name = Array('vtiger_tyuncontractactivacode','vtiger_activationcode');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_activationcode'   => 'activationcodeid','vtiger_tyuncontractactivacode'=>'activationcodeid');

    // Make the field link to detail view from list view (Fieldname)
    var $list_link_field = 'relmodule';

    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }
    function retrieve_entity_info($record, $module) {
        global $adb, $log, $app_strings;
        $join_type = 'LEFT JOIN';
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
        $cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
        if ($cachedModuleFields === false) {
            $tabid = getTabid($module);
            $sql0 = "SELECT fieldname, fieldid, fieldlabel, columnname, tablename, uitype, typeofdata,presence FROM vtiger_field WHERE tabid=?";
            $result0 = $adb->pquery($sql0, array($tabid));
            if ($adb->num_rows($result0)) {
                while ($resultrow = $adb->fetch_array($result0)) {
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
                if(($module == 'Invoice' || $module == 'Quotes' || $module == 'SalesOrder' || $module == 'PurchaseOrder')
                    && stripos($fieldinfo['columnname'], 'shtax') !== false) {
                    continue;
                }
                $column_clause .=  $fieldinfo['tablename'].'.'.$fieldinfo['columnname'].' AS '.$this->createColumnAliasForField($fieldinfo).',';
            }
            if (isset($required_tables['vtiger_crmentity'])) {
                // 2014-10-29 young 如果是单独的表，就不需要这个字段
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
                $from_clause  = 'vtiger_activationcode LEFT JOIN vtiger_tyuncontractactivacode ON vtiger_activationcode.activationcodeid=vtiger_tyuncontractactivacode.activationcodeid';
            }


            $params[] = $record;

            $sql = sprintf('SELECT %s FROM %s WHERE %s %s', $column_clause, $from_clause, $where_clause, $limit_clause);
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
        $serviceidResult=$this->db->pquery('SELECT vtiger_account.serviceid FROM vtiger_account LEFT JOIN vtiger_activationcode ON vtiger_activationcode.customerid=vtiger_account.accountid WHERE `status`!=2 AND vtiger_activationcode.activationcodeid=? LIMIT 1',array($record));
        if($adb->num_rows($serviceidResult)){
            $serviceidData=$this->db->raw_query_result_rowdata($serviceidResult,0);
            $this->column_fields['serviceid']=$serviceidData[serviceid];
        }
    }
}
?>
