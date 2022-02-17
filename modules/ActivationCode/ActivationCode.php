<?php
include_once('config.php');
require_once('include/logging.php');
require_once('modules/Contacts/Contacts.php');
require_once('modules/Potentials/Potentials.php');
require_once('modules/Calendar/Activity.php');
require_once('modules/Documents/Documents.php');
require_once('modules/Emails/Emails.php');
require_once('include/utils/utils.php');
require_once('user_privileges/default_module_view.php');

class ActivationCode extends CRMEntity {
	var $log;
	var $db;
	var $table_name = "vtiger_activationcode";
	var $table_index= 'activationcodeid';
    var $tab_name_index = Array('vtiger_activationcode'=>'activationcodeid');//'vtiger_crmentity' => 'crmid',
	var $tab_name = Array('vtiger_activationcode');
	var $column_fields = Array();
	var $sortby_fields = Array();
	var $list_fields = Array();
	var $list_fields_name = Array();
	var $list_link_field= 'activecode';
	var $search_fields = Array();
	var $search_fields_name = Array();
	var $required_fields =  array();
	var $mandatory_fields = Array();
	var $emailTemplate_defaultFields = array();
	var $default_order_by = 'activationcodeid';
	var $default_sort_order = 'desc';
	// For Alphabetical search
	var $def_basicsearch_col = 'activecode';
	var $related_module_table_index = array();
	//关联模块的一些字段和数组;
	var $relatedmodule_list=array();
	var $relatedmodule_fields=array();
    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }
	/** Function to handle module specific operations when saving a entity
	*/
	function save_module() {

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
                $from_clause  = $this->table_name;
            }


            $params[] = $record;

            $sql = sprintf('SELECT %s FROM %s WHERE %s %s', $column_clause, $from_clause, $where_clause, $limit_clause);
            $sql1 = "SELECT * FROM vtiger_activationcode WHERE activationcodeid=?";
            $result1 = $adb->pquery($sql1, array($record));
            $data_row = $adb->query_result_rowdata($result1,0);
            $classtype = $data_row['classtype'];
            if($classtype != 'buy'){
                $sql="SELECT
                    P.activedate AS vtiger_activationcodeactivedate,
                    P.activecode AS vtiger_activationcodeactivecode,
                    M.contractid AS vtiger_activationcodecontractid,
                    M.contractname AS vtiger_activationcodecontractname,
                    P.expiredate AS vtiger_activationcodeexpiredate,
                    P.customerid AS vtiger_activationcodecustomerid,
                    P.customername AS vtiger_activationcodecustomername,
                    P.agents AS vtiger_activationcodeagents,
                    M.productlife AS vtiger_activationcodeproductlife,
                    IF(LENGTH(M.productid)=0 OR ISNULL(M.productid),
                      (SELECT MM.productid FROM vtiger_activationcode MM WHERE MM.status IN(0,1) AND (MM.buyid=M.buyid OR MM.activationcodeid=M.buyid) AND  MM.classtype IN('buy','upgrade','degrade') ORDER BY MM.receivetime DESC LIMIT 1)
                      ,M.productid) AS vtiger_activationcodeproductid,
                    P.address AS vtiger_activationcodeaddress,
                    P.mobile AS vtiger_activationcodemobile,
                    P.salesname AS vtiger_activationcodesalesname,
                    P.salesphone AS vtiger_activationcodesalesphone,
                    M.status AS vtiger_activationcodestatus,
                    IF(LENGTH(P.usercode)=0,M.usercode,P.usercode) AS vtiger_activationcodeusercode,
                    M.reason AS vtiger_activationcodereason,
                    M.checkstatus AS vtiger_activationcodecheckstatus,
                    M.classtype AS vtiger_activationcodeclasstype,
                    M.buyserviceinfo AS vtiger_activationcodebuyserviceinfo,
                    M.buyid AS vtiger_activationcodebuyid,
                    M.activationcodeid,DATE_FORMAT(IFNULL(M.receivetime,NOW()),'%Y-%m-%d') AS vtiger_activationcodeadddate,
                    M.receivetime
                FROM vtiger_activationcode M
                LEFT JOIN vtiger_activationcode P ON(M.buyid=P.activationcodeid)
                WHERE
                    M.status IN(0,1) AND M.activationcodeid = ?
                LIMIT 1";
            }else{
                $buy_sql="SELECT 
                    M.activationcodeid,DATE_FORMAT(IFNULL(M.receivetime,NOW()),'%Y-%m-%d') AS vtiger_activationcodeadddate,
                    M.receivetime FROM vtiger_activationcode M WHERE M.status IN(0,1) AND M.activationcodeid = ?";
                $result_buy = $adb->pquery($buy_sql, $params);
            }


            $result = $adb->pquery($sql, $params);
            if (!$result || $adb->num_rows($result) < 1) {
                throw new Exception($app_strings['LBL_RECORD_NOT_FOUND'], -1);
            } else {
                $resultrow = $adb->query_result_rowdata($result);
                if (!empty($resultrow['deleted'])) {
                    throw new Exception($app_strings['LBL_RECORD_DELETE'], 1);
                }

                if ($_REQUEST['view']=='Detail'){
                    $info=$adb->pquery('SELECT productname FROM vtiger_products WHERE LENGTH(tyunproductid)>0 AND tyunproductid=? limit 1',array($resultrow['vtiger_activationcodeproductid']));
                    $data=$adb->query_result_rowdata($info);
                    $resultrow['vtiger_activationcodeproductid'] =empty($data['productname'])?'--':$data['productname'];
                    if($classtype != 'buy') {
                        $resultrow['vtiger_activationcodeadddate'] = $resultrow['receivetime'];
                    }else{
                        $resultrow_buy = $adb->query_result_rowdata($result_buy);
                        $resultrow['vtiger_activationcodeadddate'] = $resultrow_buy['receivetime'];
                    }
                }else{
                    if($classtype == 'buy') {
                        $resultrow_buy = $adb->query_result_rowdata($result_buy);
                        $resultrow['vtiger_activationcodeadddate'] = $resultrow_buy['vtiger_activationcodeadddate'];
                    }
                }
                //服务类型
                if ($_REQUEST['view']=='Detail' && !empty($resultrow['vtiger_activationcodeclasstype'])) {
                    if($classtype == 'buy'){
                        $resultrow['vtiger_activationcodeclasstype'] = '首购';
                    }
                    if($classtype == 'upgrade'){
                        $resultrow['vtiger_activationcodeclasstype'] = '升级';
                    }
                    if($classtype == 'degrade'){
                        $resultrow['vtiger_activationcodeclasstype'] = '降级';
                    }
                    if($classtype == 'renew'){
                        $resultrow['vtiger_activationcodeclasstype'] = '续费';
                    }
                    if($classtype == 'againbuy'){
                        $resultrow['vtiger_activationcodeclasstype'] = '另购';
                    }
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
