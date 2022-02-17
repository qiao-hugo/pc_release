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

class TyunUpgradeRule extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_tyunupgraderule';
    var $table_index= 'tyunupgraderuleid';
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
    var $tab_name = Array('vtiger_tyunupgraderule');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_tyunupgraderule'   => 'tyunupgraderuleid',);

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
    var $popup_fields = Array('tyunupgraderuleid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'tyunupgraderuleid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'tyunupgraderuleid';

    // Required Information for enabling Import feature
    var $required_fields = Array('tyunupgraderuleid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('tyunupgraderuleid');

    var $default_order_by = 'tyunupgraderuleid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('tyunupgraderuleid');
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
     * 更新后处理
     * @param unknown $module
     */
    function save_module($module) {
   		//获取登录用户信息
	   	$currentUser = Users_Record_Model::getCurrentUserModel();
	   	$userid = $currentUser->get('id');
	
	   	//更新添加人，添加时间，状态
        $productid=$_REQUEST['productid'];
        $datetime=date('Y-m-d H:i:s');
        $tyundownup=$_REQUEST['tyundownup'];
        $update_query = "UPDATE vtiger_productdownupgrade SET deleted=1,deletedid=?,deletedtime=? WHERE sproduct=? AND tyundownup=? AND deleted=0";
        $update_params = array($userid,$datetime,$productid,$tyundownup);
        $this->db->pquery($update_query, $update_params);
        $sproductid=vtranslate($productid,'TyunUpgradeRule');

        foreach($_REQUEST['productdlist'] as $value)
        {
            $arr=array();
            $arr[]=$productid;
            $arr[]=$value;
            $arr[]=$sproductid;
            $arr[]=vtranslate($value,'TyunUpgradeRule');
            $arr[]=$tyundownup;
            $arr[]=$userid;
            $arr[]=$datetime;
            $this->db->pquery('REPLACE INTO vtiger_productdownupgrade(sproduct,dproduct,slabel,dlabel,tyundownup,smcreatorid,createdtime) VALUES(?,?,?,?,?,?,?)',$arr);
        }
        if(empty($_REQUEST['record'])) {
            $this->db->pquery('UPDATE `vtiger_tyunupgraderule` SET smcreatorid=?,createdtime=? WHERE tyunupgraderuleid=?',
                array($userid, $datetime, $this->id));
        }else{
            $this->db->pquery('UPDATE `vtiger_tyunupgraderule` SET modifiedtime=? WHERE tyunupgraderuleid=?',
                array($datetime, $this->id));
        }
    }

    /**
     * 删除记录的前置事件
     * @param $module
     * @param $entityId
     */
    public function deleteBefore($module,$entityId)
    {
        global $current_user;
        $recordModule=Vtiger_Record_Model::getInstanceById($entityId,$module);
        $entity=$recordModule->getEntity();
        $column_fields=$entity->column_fields;
        $productid=$column_fields['productid'];
        $datetime=date('Y-m-d H:i:s');
        $tyundownup=$column_fields['tyundownup'];
        $update_query = "UPDATE vtiger_productdownupgrade SET deleted=1,deletedid=?,deletedtime=? WHERE sproduct=? AND tyundownup=? AND deleted=0";
        $update_params = array($current_user->id,$datetime,$productid,$tyundownup);
        $this->db->pquery($update_query, $update_params);
    }

}
?>
