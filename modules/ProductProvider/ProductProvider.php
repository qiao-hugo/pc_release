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
class ProductProvider extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_productprovider';
    var $table_index= 'productproviderid';
    var $column_fields = Array();
    var $entity_table = "vtiger_crmentity";

    /** Indicator if this is a custom module or standard module */
    var $IsCustomModule = true;

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array();

    //var $tab_name = Array('vtiger_crmentity','vtiger_account','vtiger_accountbillads','vtiger_accountscf','vtiger_accountshipads');
    //var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_account'=>'accountid','vtiger_accountbillads'=>'accountaddressid','vtiger_accountscf'=>'accountid','vtiger_accountshipads'=>'accountaddressid');
    var $tab_name = Array('vtiger_crmentity','vtiger_productprovider',);
    var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_productprovider'=>'productproviderid');

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
        //'schoolname'=> Array('School', 'schoolname')
    );
    var $search_fields_name = Array(
        /* Format: Field Label => fieldname */
        //'schoolname'=> 'schoolname'
    );

    /*var $search_fields = Array(
        'schoolname'=> Array('School', 'schoolname')
    );
    var $search_fields_name = Array(
    /* Format: Field Label => fieldname 
        'schoolname'=> 'schoolname'
    );*/


    // For Popup window record selection
    var $popup_fields = Array('productproviderid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'productproviderid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'productproviderid';

    // Required Information for enabling Import feature
    var $required_fields = Array('productproviderid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('productproviderid');

    var $relatedmodule_list=array();
    var $relatedmodule_fields=array(
        //'Schoolcontacts'=>
                    //array('schoolcontactsname'=>'联系人','position'=>'职位','gendertype'=>'性别','phone'=>'手机','email'=>'email'),
    );

    var $related_module_table_index = array(
        //'ServiceContracts' => array('table_name' => 'vtiger_servicecontracts', 'table_index' => 'servicecontractsid', 'rel_index' => 'sc_related_to'),

        //'Schoolrecruit'=>array('table_name' => 'vtiger_schoolrecruit', 'table_index' => 'schoolrecruitid', 'rel_index' => 'schoolid')

    );


    var $default_order_by = 'productproviderid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('productproviderid');

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
        global $current_user;
        // cxh - start 2020-04-07
        $currentTime=date("Y-m-d H:i:s");
        $accountzhArray=$_REQUEST['accountzh'];
        //如果属于编辑
        if(!empty($_REQUEST['record'])){
            $productprovide_detail_id=$_REQUEST['productprovide_detail_id'];
            $str='';
            $updateStatus=$_REQUEST['updateStatus'];
            $oldidaccount=$_REQUEST['oldidaccount'];
            $oldaccountzh=$_REQUEST['oldaccountzh'];
            $param['record']=$_REQUEST['record'];
            $param['module']='ProductProvider';
            $param['userid']=$current_user->id;
            $param['status']=0;
            $param['strArray']=array();
            $i=1;
            foreach ($_REQUEST['idaccount'] as $key=>$value){
                $j=$i+1;
                //新建保存
                if(!$productprovide_detail_id[$key]){
                    $str.="(".$this->id.",'".$value."','".$accountzhArray[$key]."','".$currentTime."','".$currentTime."'),";
                    $param['strArray'][$i]['fieldname']='idaccount';
                    $param['strArray'][$i]['prevalue']=null;
                    $param['strArray'][$i]['postvalue']=$value;
                    $param['strArray'][$j]['fieldname']='accountzh';
                    $param['strArray'][$j]['prevalue']=null;
                    $param['strArray'][$j]['postvalue']=$accountzhArray[$key];
                }else{
                     if($updateStatus[$key]==1){
                         // idaccount 字段修改变更记录
                         if($value!=$oldidaccount['oldidaccount']){
                             $param['strArray'][$i]['fieldname']='idaccount';
                             $param['strArray'][$i]['prevalue']=$oldidaccount[$key];
                             $param['strArray'][$i]['postvalue']=$value;
                         }
                         //  accountzh  字段修改变更记录
                         if($accountzhArray[$key]!=$oldaccountzh[$key]){
                             $param['strArray'][$j]['fieldname']='accountzh';
                             $param['strArray'][$j]['prevalue']=$oldaccountzh[$key];
                             $param['strArray'][$j]['postvalue']=$accountzhArray[$key];
                         }
                         $this->updateOldDetail(array($value,$accountzhArray[$key],$currentTime,$productprovide_detail_id[$key]));
                     }
                }
                $i=$i+2;
            }
            //如果修改变更记录存在则插入变更记录
            if(!empty($param['strArray'])){
                $recordModel= Vtiger_Record_Model::getCleanInstance("ProductProvider");
                $recordModel->addLogs($param);
            }
            if(!empty($str)){
                $str=trim($str,',');
                $this->addDetail($str);
            }
        //新建
        }else{
            $str='';
            foreach ($_REQUEST['idaccount'] as $key=>$value){
                $str.="(".$this->id.",'".$value."','".$accountzhArray[$key]."','".$currentTime."','".$currentTime."'),";
            }
            if(!empty($str)){
                $str=trim($str,',');
                $this->addDetail($str);
            }
        }
        // cxh - end
        $productid =  $_REQUEST['productid']; //复选框选中的产品id
        $products=$this->db->pquery('SELECT vtiger_products.productname, vtiger_products.productcategory, vtiger_products.realprice, vtiger_products.unit_price, vtiger_crmentity.smownerid, vtiger_products.productid FROM vtiger_products INNER JOIN vtiger_crmentity ON vtiger_products.productid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0 AND vtiger_products.productid=?',array($productid));
        $rows=$this->db->num_rows($products);
        $checkarray=array();
        for ($i=0; $i<$rows; ++$i) {
            $product = $this->db->fetchByAssoc($products);
            $checkarray[]=array('workflowstagesname'=> $product['productname'].'审核','smcreatorid'=>$product['smownerid'],'productid'=>$product['productid']);
        }
        vglobal('checkproducts',$checkarray);
    }
    // cxh 2020-04-08
    public function addDetail($str){
       $insert="INSERT INTO vtiger_productprovider_detail (`productproviderid`, `idaccount`, `accountzh`, `createtime`, `updatetime`) values  ".$str;
       $this->db->pquery($insert,array());
    }
    // cxh 2020-04-08
    public function updateOldDetail($params){
       $update="UPDATE vtiger_productprovider_detail SET idaccount=?,accountzh=?,updatetime=? WHERE productprovide_detail_id=? ";
       $this->db->pquery($update,array($params));
    }

    public function makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit=''){
        parent::makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit);
        $query=" UPDATE vtiger_salesorderworkflowstages,
				 vtiger_productprovider
				SET vtiger_salesorderworkflowstages.accountid=vtiger_productprovider.vendorid,vtiger_salesorderworkflowstages.salesorder_nono=?,
				     vtiger_salesorderworkflowstages.modulestatus='p_process',
				 vtiger_salesorderworkflowstages.accountname=(SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid=vtiger_productprovider.accountid)
				WHERE vtiger_productprovider.productproviderid=vtiger_salesorderworkflowstages.salesorderid
				AND vtiger_salesorderworkflowstages.salesorderid=?  AND  vtiger_salesorderworkflowstages.workflowsid=? ";
        $this->db->pquery($query,array($_REQUEST['idaccount'],$salesorderid,$workflowsid));
        if($workflowsid==2134993){
            global $current_user;
            $needle='H282::';
            $needletwo='H281::';
            $query='SELECT vtiger_departments.parentdepartment FROM vtiger_departments WHERE departmentid=?';
            $result=$this->db->pquery($query,array($current_user->departmentid));
            $data=$this->db->raw_query_result_rowdata($result,0);
            $parentdepartment=$data['parentdepartment'];
            $parentdepartment.='::';
            if(strpos($parentdepartment,$needle)===false && strpos($parentdepartment,$needletwo)===false){
                $query='DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid =? AND vtiger_salesorderworkflowstages.modulename=\'ProductProvider\' AND vtiger_salesorderworkflowstages.sequence=1 AND vtiger_salesorderworkflowstages.workflowsid=?';
                $this->db->pquery($query,array($salesorderid,$workflowsid));
                $query='UPDATE `vtiger_salesorderworkflowstages` SET isaction=1,actiontime=NOW() WHERE vtiger_salesorderworkflowstages.salesorderid =? AND vtiger_salesorderworkflowstages.modulename=\'ProductProvider\' AND vtiger_salesorderworkflowstages.sequence=2 AND vtiger_salesorderworkflowstages.workflowsid=?';
                $this->db->pquery($query,array($salesorderid,$workflowsid));
            }else{
                $query='UPDATE `vtiger_salesorderworkflowstages` SET ishigher=1,higherid=7629 WHERE vtiger_salesorderworkflowstages.salesorderid =? AND vtiger_salesorderworkflowstages.modulename=\'ProductProvider\' AND vtiger_salesorderworkflowstages.sequence=1 AND vtiger_salesorderworkflowstages.workflowsid=?';
                $this->db->pquery($query,array($salesorderid,$workflowsid));
            }
        }
        //新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$salesorderid,'salesorderworkflowstagesid'=>0));
    }
    /* 节点审核时到了指定节点抓取时间
     * 后置事件
     * @param Vtiger_Request $request
     */
    function workflowcheckafter(Vtiger_Request $request) {
        // cxh 2019-08-02 添加 如果该审核需要修改审核列表中的modulestatus（审核流程状态）审核完后走下面代码
        $stagerecordid=$request->get('stagerecordid');
        $query="SELECT
		    vtiger_salesorderworkflowstages.workflowsid
		    FROM
		    `vtiger_salesorderworkflowstages`
		    WHERE
		       vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ? ";
        $result=$this->db->pquery($query,array($stagerecordid));
        $workflowsid=$this->db->query_result($result,0,'workflowsid');
        $params['workflowsid']=$workflowsid;
        $params['salesorderid']=$request->get('record');
        $this->hasAllAuditorsChecked($params);
    }
    /**
     * 合同作废打回中处理
     * @param Vtiger_Request $request
     */
    public function backallAfter(Vtiger_Request $request)
    {
        $record=$request->get('record');
        $this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='ProductProvider'",array($record));
    }
    // 回款导入
    function importRecord($importdata_object, $fieldData){
        global $current_user;
        $datetime=date('Y-m-d H:i:s');
        $adb = PearDatabase::getInstance();
        $recorid = $adb->getUniqueID('vtiger_crmentity');
        $fieldData['accountid'] = getEntityId('Accounts', $fieldData['accountid']);
        $fieldData['vendorid'] = getEntityId('Vendors', $fieldData['vendorid']);
        $fieldData['suppliercontractsid'] = getEntityId('SupplierContracts', $fieldData['suppliercontractsid']);
        $fieldData['productid'] = getEntityId('Products', $fieldData['productid']);
        $fieldData['productproviderid']=$recorid;
        $fieldData['modulestatus']='c_complete';
        $fieldData['workflowsid']='2134993';
        $userid=$fieldData['workflowsnode'];
        $fieldData['servicestartdate']=$datetime;
        unset($fieldData['workflowsnode']);
        $fieldNames = array_keys($fieldData);
        $fieldValues = array_values($fieldData);

        $crmdata['crmid']=$recorid;
        $crmdata['smcreatorid']=$userid;
        $crmdata['smownerid']=$userid;
        $crmdata['modifiedby']=$userid;
        $crmdata['setype']='ProductProvider';
        $crmdata['createdtime']=$datetime;
        $crmdata['modifiedtime']=$datetime;
        $crmdata['version']=0;
        $crmdata['deleted']=0;
        $crmdata['label']=$fieldData['idaccount'];
        $crmdataNames = array_keys($crmdata);
        $crmdataValues = array_values($crmdata);
        $adb->pquery('INSERT INTO vtiger_productprovider ('. implode(',', $fieldNames).') VALUES ('. generateQuestionMarks($fieldValues) .')', $fieldValues);
        $adb->pquery('INSERT INTO vtiger_crmentity ('. implode(',', $crmdataNames).') VALUES ('. generateQuestionMarks($crmdataValues) .')', $crmdataValues);
        $result = $adb->pquery('SELECT * FROM vtiger_productprovider WHERE productproviderid =?',array($recorid));
        if($adb->num_rows($result)==1){
            return array('id'=>$recorid);
        }else{
            return "";
        };
    }
}
?>
