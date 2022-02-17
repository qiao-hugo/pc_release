<?php
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');
class Formdesign extends CRMEntity {
	var $db, $log; 
	var $table_name = 'vtiger_formdesign';
	var $table_index= 'formid';
	var $column_fields = Array();
	var $IsCustomModule = true;
	var $tab_name = Array('vtiger_formdesign');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_formdesign'=>'formid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array(
   		
	);
	var $list_fields_name = Array(
		
	);

	// Make the field link to detail view
	// For Popup listview and UI type support
	var $search_fields = Array(
		
	);
	var $search_fields_name = Array(
		
	);

	/**	Constructor which will set the column_fields in this object
	 */
	function __construct() {
		global $log;
		$this->column_fields = getColumnFields(get_class($this));
		$this->db = PearDatabase::getInstance();
		$this->log = $log;
	}

	function save_module(){}
	
	
	/**	function to save the service tax information in vtiger_servicetaxrel table
	 *	@param string $tablename - vtiger_tablename to save the service tax relationship (servicetaxrel)
	 *	@param string $module	 - current module name
	 *	$return void
	*/
	
//关闭旧模版//更新关联的模版
function insertIntoEntityTable($table_name, $module, $fileid = '') {
		global $current_user, $app_strings;
		global $adb;
		$insertion_mode = $this->mode;
		$tabid = getTabid($module);
		//$table_index_column = $this->tab_name_index[$table_name];
		/* $currentuser_id = $adb->getUniqueID($table_name);
		$this->id = $currentuser_id;
		$this->{$this->table_index}=$currentuser_id; */
		
		$this->column_fields['content']=$_POST['content'];
		$column = array('createdtime','smcreatorid');
		$value = array(date('Ymdhis'),$current_user->id);	
		$sql = "select fieldname,columnname,uitype,generatedtype,typeofdata from vtiger_field where tabid=? and tablename=? and displaytype in (1,3,4) and vtiger_field.presence in (0,2)";
		$params = array($tabid, $table_name);
		$result = $adb->pquery($sql, $params);
		$noofrows = $adb->num_rows($result);
	
		for ($i = 0; $i < $noofrows; $i++) {
			list($fieldname,$columname,$uitype,$generatedtype,$typeofdata)= $adb->fetch_array($result);
			$typeofdata_array = explode("~", $typeofdata);
			$datatype = $typeofdata_array[0];
			if($typeofdata_array[1]=='M' && isset($_POST[$fieldname]) && empty($this->column_fields[$fieldname]) && $this->column_fields[$fieldname] !=='0'){
			    throw new AppException('错误的数据格式！');
				exit;
			}
			$fldvalue = $this->column_fields[$fieldname];
			$fldvalue = from_html($fldvalue);
			if ($fldvalue == '') {
				$fldvalue = $this->get_column_value($columname, $fldvalue, $fieldname, $uitype, $datatype);
			}
			$column[]=$columname;
			$value[$fieldname]=$fldvalue;	
		}

		//print_r($value['content']);
		//include('Formdesign.class.php');
		$template_data=$template_parse =$value['content'];
		//$preg='/[formdesign=\"]{.*?\"/';
		$preg = "/\<span plugintype=\"(radios|checkboxs|select|text|textarea|listctrl)\"([^>]*) formdesign=\"(.*?)\"([^>]*)>(.*?)<\/span>/";
		preg_match_all($preg,$value['content'],$temparr);

        //$formdesign = new FormdesignClass;
        //$parse_content = $formdesign->parse_form($value['content']);
		$data=array();
		foreach($temparr[3] as $key=> $json){
			$inputinfo=json_decode(str_replace('&quot;','"',$json),true);
			if(is_array($inputinfo) && !empty($inputinfo['name'])){
				$data[]=$inputinfo;
				$template_parse=str_replace($temparr[0][$key],'<{'.$inputinfo['name'].'}>',$template_parse);
				$template_data=str_replace($temparr[0][$key],$temparr[5][$key],$template_data);
			}	
		}

		$column[]='content_parse';
		$value[]=$template_parse;
		$column[]='content_data';
		$value[]=str_replace('readonly','',$template_data);
		$column[]='field';
		$value[]=json_encode($data);
		$sql1 = "insert into $table_name(" . implode(",", $column) . ") values(" . generateQuestionMarks($value) . ")";
    	$adb->pquery($sql1, $value);
		
		$this->id=$adb->getLastInsertID();
		
		if(isset($_REQUEST['record'])){
			$adb->pquery("update $table_name set deleted=1 where formid=?",array($_REQUEST['record']));
			$adb->pquery("update vtiger_customer_modulefields set formid=? where formid=?",array($this->id,$_REQUEST['record']));	
		}
		
		
		
	}
	
//详情替换模版为解析后的数据
	function retrieve_entity_info($record, $module) {
		global $adb, $app_strings;
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
				$cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
			}
		}

		if ($cachedModuleFields) {
			$column_clause = '';
			$from_clause   = '';
			$where_clause  = '';
			$limit_clause  = ' LIMIT 1';
			$params = array();
			foreach ($cachedModuleFields as $fieldinfo) {
				$column_clause .=  $fieldinfo['tablename'].'.'.$fieldinfo['columnname'].' AS '.$this->createColumnAliasForField($fieldinfo).',';
			}
			
			global $currentView;
			
			if($currentView=='Detail'){
				$column_clause=str_replace('vtiger_formdesign.content AS','vtiger_formdesign.content_data AS',$column_clause);
			}
			
			$column_clause .= $this->table_name.'.'.$this->table_index;
			$where_clause .= ' '.$this->table_name.'.'.$this->table_index.'=?';
			$from_clause  = $this->table_name;
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
					if (isset($resultrow[$fieldkey])) {
						$fieldvalue = $resultrow[$fieldkey];
					}
					$this->column_fields[$fieldinfo['fieldname']] = $fieldvalue;
				}
			}
		}
		if($currentView=='Detail'){
			$this->column_fields['content'] =htmlspecialchars_decode($this->column_fields['content']);
		}
		$this->column_fields['record_id'] = $record;
		$this->column_fields['record_module'] = $module;
	}
	
	
//保留废弃的模版 	
	function real_deleted($module,$id){
		$sql="update $this->table_name set deleted=1 where $this->table_index =?";

		$this->db->pquery($sql,array($id));
	}
	
	
	








	

 	
}
?>
