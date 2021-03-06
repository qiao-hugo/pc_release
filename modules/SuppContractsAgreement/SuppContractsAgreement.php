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

class SuppContractsAgreement extends CRMEntity
{
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_suppcontractsagreement';
    var $table_index = 'contractsagreementid';
    var $tab_name = Array('vtiger_crmentity', 'vtiger_suppcontractsagreement');
    var $tab_name_index = Array('vtiger_crmentity' => 'crmid', 'vtiger_suppcontractsagreement' => 'contractsagreementid');
    var $column_fields = Array();

    /** Indicator if this is a custom module or standard module */
    var $IsCustomModule = true;

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array();


    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'


    );
    var $list_fields_name = Array(/* Format: Field Label => fieldname */

    );

    // Make the field link to detail view from list view (Fieldname)
    var $list_link_field = 'relmodule';

    // For Popup listview and UI type support
    var $search_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'

    );
    var $search_fields_name = Array(/* Format: Field Label => fieldname */

    );

    // For Popup window record selection
    var $popup_fields = Array('contractsagreementid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'contractsagreementid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'contractsagreementid';

    // Required Information for enabling Import feature
    var $required_fields = Array('contractsagreementid' => 1);

    // Callback function list during Importing
    var $special_functions = Array('contractsagreementid');

    var $default_order_by = 'contractsagreementid';
    var $default_sort_order = 'DESC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('contractsagreementid');

    function __construct()
    {
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
        $products=$this->db->pquery('SELECT vtiger_products.productname, vtiger_products.productcategory, vtiger_products.realprice, vtiger_products.unit_price, vtiger_crmentity.smownerid, vtiger_products.productid FROM vtiger_vendorsrebate INNER JOIN vtiger_products ON vtiger_vendorsrebate.productid=vtiger_products.productid INNER JOIN vtiger_crmentity ON vtiger_products.productid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0 AND vtiger_vendorsrebate.deleted=0 AND vtiger_vendorsrebate.suppliercontractsid=?',array($_POST['suppliercontractsid']));

        $rows=$this->db->num_rows($products);
        $checkarray=array();
        for ($i=0; $i<$rows; ++$i) {
            $product = $this->db->fetchByAssoc($products);
            $checkarray[]=array('workflowstagesname'=> $product['productname'].'??????','smcreatorid'=>$product['smownerid'],'productid'=>$product['productid']);
        }
        vglobal('checkproducts',$checkarray);
    }
    function saveentity($module, $fileid = '') {
        parent::saveentity($module, $fileid = '');
        $id=$_REQUEST['record']>0?$_REQUEST['record']:$this->id;
        global $current_user;
        $arr=array(date('Y-m-d H:i:s'),$id);
        $this->db->pquery("UPDATE vtiger_suppcontractsagreement SET dateofapp=?,modulestatus='b_actioning' WHERE contractsagreementid=?",$arr);
        //$this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=(SELECT vtiger_auditsettings.oneaudituid FROM `vtiger_auditsettings` INNER JOIN (SELECT vtiger_departments.parentdepartment,vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.departmentid='{$current_user->departmentid}') AS tempdepart ON FIND_IN_SET(vtiger_auditsettings.department,REPLACE(tempdepart.parentdepartment,'::',',')) LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_auditsettings.department  WHERE vtiger_auditsettings.auditsettingtype='SuppContractsAgreement' ORDER BY abs(LENGTH(IFNULL(tempdepart.parentdepartment,0))-LENGTH(IFNULL(vtiger_departments.parentdepartment,0))) LIMIT 1) WHERE vtiger_salesorderworkflowstages.sequence=1 AND vtiger_salesorderworkflowstages.salesorderid=? AND isaction=1 AND vtiger_salesorderworkflowstages.modulename='SuppContractsAgreement'",array($id));
        //$departmentid=empty($current_user->departmentid)?'H1':$current_user->departmentid;
        $departmentid=$_SESSION['userdepartmentid'];
        $this->setAudituid("SuppContractsAgreement",$departmentid,$id,$_POST['workflowsid']);
	$suppliercontractsid=$this->column_fields['suppliercontractsid'];
        if($suppliercontractsid>0){
            $supplierContractsRecordModel=Vtiger_Record_Model::getInstanceById($suppliercontractsid,'SupplierContracts');
            if(in_array($supplierContractsRecordModel->get('companycode'),$supplierContractsRecordModel->Kllcompanycode)){
                $query='SELECT vtiger_departments.parentdepartment FROM vtiger_departments WHERE departmentid=? limit 1';
                $result=$this->db->pquery($query,array($departmentid));
                $data=$this->db->raw_query_result_rowdata($result,0);
                $parentdepartment=$data['parentdepartment'];
                $parentdepartment.='::';
                //$needle='H283::';
                if(strpos($parentdepartment,$supplierContractsRecordModel->Kllneedle)!==false || strpos($parentdepartment,$supplierContractsRecordModel->WXKLLneedle)!==false){
                    $updateSql="UPDATE vtiger_salesorderworkflowstages SET workflowstagesname='????????????????????????',ishigher = 1,higherid =? WHERE salesorderid =? AND workflowstagesflag='CREATE_CODE' AND workflowsid =?";
                    $this->db->pquery($updateSql,array(11505,$id,$_REQUEST['workflowsid']));
                }
                //2021.06.03 ???????????????????????????
//                $deleteSql="DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowstagesflag IN('TREASURER_ONE','TREASURER_TWO')";
                $deleteSql="DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowstagesflag IN('TREASURER_ONE')";
                $this->db->pquery($deleteSql,array($id));//???????????????????????????
            }elseif(in_array($supplierContractsRecordModel->get('companycode'),$supplierContractsRecordModel->TREASURER_TWO)){
                $deleteSql="DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowstagesflag IN('TREASURER_ONE')";
                $this->db->pquery($deleteSql,array($id));//???????????????????????????
            }else{
//                $deleteSql="DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowstagesflag IN('TREASURER_TWO')";
//                $this->db->pquery($deleteSql,array($id));//???????????????????????????
            }
        }
        //????????? ???????????????????????????????????????
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$id,'salesorderworkflowstagesid'=>0));
    }
    /**?????????????????????????????????????????????
     * ????????????
     * @param Vtiger_Request $request
     */
    function workflowcheckafter(Vtiger_Request $request){
        $stagerecordid=$request->get('stagerecordid');
        $record=$request->get('record');

        $query="SELECT
                    vtiger_workflowstages.workflowstagesflag,
                    vtiger_salesorderworkflowstages.workflowsid,
                    vtiger_salesorderworkflowstages.sequence
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'SuppContractsAgreement'";
        $result=$this->db->pquery($query,array($stagerecordid));
        $currentflag=$this->db->query_result($result,0,'workflowstagesflag');
        $workflowsid=$this->db->query_result($result,0,'workflowsid');
        $sequence=$this->db->query_result($result,0,'sequence');
        $recordModel = Vtiger_Record_Model::getInstanceById($record,'SuppContractsAgreement');
        $entity=$recordModel->entity->column_fields;

        $currentflag=trim($currentflag);
        $datetime=date('Y-m-d H:i:s');
        switch($currentflag){
            case 'CREATE_CODE':
                //??????????????????
                if(empty($entity['newservicecontractsno'])) {
                    $contractRecordModel = Vtiger_Record_Model::getInstanceById($entity['suppliercontractsid'], 'SupplierContracts');
                    $contractentity = $contractRecordModel->entity->column_fields;
                    $result = $this->db->pquery("SELECT contract_no FROM vtiger_suppliercontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_suppliercontracts.suppliercontractsid WHERE vtiger_crmentity.deleted=0 AND vtiger_suppliercontracts.contract_no LIKE '{$contractentity['contract_no']}-%' ORDER BY suppliercontracts_no DESC LIMIT 1", array());
                    $num = $this->db->num_rows($result);
                    if ($num) {
                        $row=$this->db->raw_query_result_rowdata($result);
                        $contractTemp=$row['contract_no'];
                        $contractArray=explode("-",$contractTemp);
                        $contractnum=end($contractArray);
                        $contractnum=trim($contractnum);
                        $contractnum++;
                    }else{
                       $contractnum=1;
                    }
                    $servicecontracts_no =$this->recursiveContractNumber($contractentity['contract_no'],$contractnum);
                    $contractRecordModel = Vtiger_Record_Model::getInstanceById($entity['suppliercontractsid'], 'SupplierContracts');
                    $contractentity = $contractRecordModel->entity->column_fields;
                    unset($_REQUEST);//??????????????????
                    $_REQUES['record'] = '';
                    $request = new Vtiger_Request($_REQUES, $_REQUES);
                    $request->set('contract_no',$servicecontracts_no);
                    $request->set('assigned_user_id', $entity['assigned_user_id']);
                    $request->set('modulestatus', 'b_actioning');
                    $request->set('suppliercontractsstatus', $contractentity['suppliercontractsstatus']);
                    $request->set('invoicecompany', $contractentity['invoicecompany']);
                    $request->set('vendorid', $entity['vendorid']);
                    $request->set('remark', '????????????');
                    $request->set('module', 'SupplierContracts');
                    $request->set('view', 'Edit');
                    $request->set('action', 'Save');
                    $request->set('contract_name',$entity['contract_name']);
                    $request->set('total',$entity['total']);
                    $request->set('paymentclause',$entity['paymentclause']);
                    $request->set('bankaccount',$contractentity['bankaccount']);
                    $request->set('bankname',$contractentity['bankname']);
                    $request->set('bankcode',$contractentity['bankcode']);
                    $request->set('banknumber',$contractentity['banknumber']);

                    $ressorder = new SupplierContracts_Save_Action();
                    $ressorderecord = $ressorder->saveRecord($request);
                    $serviceconrecord = $ressorderecord->getId();
                    $this->db->pquery('UPDATE `vtiger_suppcontractsagreement` SET newservicecontractsno=?,newservicecontractsid=? WHERE contractsagreementid=?', array($servicecontracts_no,$serviceconrecord,$record));
                    $this->db->pquery('UPDATE vtiger_suppliercontracts SET modulestatus=\'b_actioning\',sideagreement=1,creatorid=?,receiptorid=?,contract_no=? WHERE vtiger_suppliercontracts.suppliercontractsid=?', array($entity['assigned_user_id'],$entity['receiptorid'],$servicecontracts_no,$serviceconrecord));
                    $this->db->pquery('UPDATE vtiger_crmentity SET label=? WHERE crmid=?', array($servicecontracts_no,$serviceconrecord));
                    //   ?????????????????????????????????????????????????????????       vtiger_salesorderworkflowstages  ???????????????
                    $this->db->pquery('UPDATE `vtiger_salesorderworkflowstages` SET salesorder_nono=? WHERE vtiger_salesorderworkflowstages.salesorderid=?', array($servicecontracts_no,$record));

                    //???????????????
                    $sql=" SELECT productid,productname,rebate,effectdate,enddate,vendorid,deleted,vexplain,rebatetype FROM vtiger_vendorsrebate WHERE suppliercontractsid = ? ";//
                    $result = $this->db->pquery($sql,array($entity['suppliercontractsid']));
                    $sql=" INSERT INTO vtiger_vendorsrebate (productid,productname,rebate,effectdate,enddate,vendorid,deleted,vexplain,rebatetype,suppliercontractsid) VALUES(?,?,?,?,?,?,?,?,?,?) ";
                    while ($rowDatas=$this->db->fetch_array($result)){
                        $this->db->pquery($sql,array($rowDatas['productid'],$rowDatas['productname'],$rowDatas['rebate'],$rowDatas['effectdate'],$rowDatas['enddate'],$rowDatas['vendorid'],$rowDatas['deleted'],$rowDatas['vexplain'],$rowDatas['rebatetype'],$serviceconrecord));
                    }
                }
                break;
            case 'AUDIT_VERIFICATION':
                //??????????????????????????????
                $this->AuditAuditNodeJump($record,$entity['workflowsid'],$entity['assigned_user_id'],'SuppContractsAgreement','SuppContractsAgreement',1);
                /*$user = new Users();
                $current_user = $user->retrieveCurrentUserInfoFromFile($entity['assigned_user_id']);
                $query="SELECT vtiger_auditsettings.oneaudituid,vtiger_auditsettings.towaudituid FROM`vtiger_auditsettings` INNER JOIN (SELECT vtiger_departments.parentdepartment,vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.departmentid='{$current_user->departmentid}') AS tempdepart ON FIND_IN_SET(vtiger_auditsettings.department,REPLACE(tempdepart.parentdepartment,'::',',')) LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_auditsettings.department  WHERE vtiger_auditsettings.auditsettingtype='SuppContractsAgreement' ORDER BY abs(LENGTH(IFNULL(tempdepart.parentdepartment,0))-LENGTH(IFNULL(vtiger_departments.parentdepartment,0))) LIMIT 1";
                $resultAuditSettings=$this->db->pquery($query,array());
                $oneaudituid=$this->db->query_result($resultAuditSettings,0,'oneaudituid');
                $towaudituid=$this->db->query_result($resultAuditSettings,0,'towaudituid');
                if($oneaudituid==$towaudituid)
                {//????????????????????????????????????????????????????????????????????????????????????????????????????????????
                    $isaction='isaction=2,';
                    $this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=1 WHERE vtiger_salesorderworkflowstages.salesorderid=? AND sequence=3 AND vtiger_salesorderworkflowstages.modulename='SuppContractsAgreement'",array($record));//?????????????????????
                }
                else
                {
                    $isaction='isaction=1,';
                }
                $sql="UPDATE vtiger_salesorderworkflowstages SET {$isaction}ishigher=1,higherid=? WHERE vtiger_salesorderworkflowstages.salesorderid=? AND sequence=2 AND vtiger_salesorderworkflowstages.modulename='SuppContractsAgreement'";
                $this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=16 WHERE vtiger_salesorderworkflowstages.salesorderid=? AND sequence=3 AND vtiger_salesorderworkflowstages.modulename='SuppContractsAgreement'",array($record));//?????????????????????????????????
                $this->db->pquery($sql,array($towaudituid,$record));//???????????????*/
                break;
            case 'TWO_VERIFICATION':
                //??????????????????????????????
                $this->AuditAuditNodeJump($record,$entity['workflowsid'],$entity['assigned_user_id'],'SuppContractsAgreement','SuppContractsAgreement',2);
                break;
            case 'DO_PRINT':
                global $current_user;

                //??????????????????
                //$this->db->pquery("UPDATE vtiger_servicecontracts_print,vtiger_contractsagreement SET constractsstatus='c_print',printer=?,printtime=? WHERE vtiger_servicecontracts_print.servicecontractsprintid=vtiger_contractsagreement.servicecontractsprintid AND vtiger_contractsagreement.contractsagreementid=?",array($current_user->id,$datetime,$record));

            default :
                break;
        }
        $this->db->pquery("UPDATE vtiger_suppcontractsagreement SET workflowsnode=(SELECT vtiger_salesorderworkflowstages.workflowstagesname FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.isaction=1 AND vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='SuppContractsAgreement' LIMIT 1) WHERE vtiger_suppcontractsagreement.contractsagreementid=?",array($record,$record));
        if($entity['modulestatus']=='c_complete')
        {

           /* $contractArray=explode("-",$entity['newservicecontractsno']);
            $contractnum=array_pop($contractArray);
            $contractnum=trim($contractnum);
            $contractno=implode('-',$contractArray);
            $contractno=$this->recursiveContractNumber($contractno,$contractnum);
            $contractRecordModel = Vtiger_Record_Model::getInstanceById($entity['suppliercontractsid'], 'SupplierContracts');
            $contractentity = $contractRecordModel->entity->column_fields;
            unset($_REQUEST);//??????????????????
            $_REQUES['record'] = '';
            $request = new Vtiger_Request($_REQUES, $_REQUES);
            $request->set('contract_no',$contractno);
            $request->set('assigned_user_id', $entity['assigned_user_id']);
            $request->set('modulestatus', 'c_stamp');
            $request->set('suppliercontractsstatus', $contractentity['suppliercontractsstatus']);
            $request->set('invoicecompany', $contractentity['invoicecompany']);
            $request->set('vendorid', $entity['vendorid']);
            $request->set('remark', '????????????');
            $request->set('module', 'SupplierContracts');
            $request->set('view', 'Edit');
            $request->set('action', 'Save');
            $ressorder = new SupplierContracts_Save_Action();
            $ressorderecord = $ressorder->saveRecord($request);
            $serviceconrecord = $ressorderecord->getId();
            $this->db->pquery('UPDATE `vtiger_suppcontractsagreement` SET newservicecontractsno=?,newservicecontractsid=? WHERE contractsagreementid=?', array($contractno,$serviceconrecord,$record));*/
            $this->db->pquery('UPDATE vtiger_suppliercontracts SET modulestatus=\'c_stamp\',contractattribute=\'customized\' WHERE vtiger_suppliercontracts.suppliercontractsid=(SELECT vtiger_suppcontractsagreement.newservicecontractsid FROM vtiger_suppcontractsagreement WHERE contractsagreementid=? LIMIT 1)', array($record));
            $attachments = explode('##', $entity['file']);
            if (!empty($attachments[1])) {
                $this->db->pquery("UPDATE `vtiger_files` SET description='SupplierContracts',style='files_style7',filestate='filestate1',relationid=(SELECT vtiger_suppcontractsagreement.newservicecontractsid FROM vtiger_suppcontractsagreement WHERE contractsagreementid=? LIMIT 1) WHERE attachmentsid=?", array($record, $attachments[1]));
            }
            $user = new Users();
            $current_users = $user->retrieveCurrentUserInfoFromFile($entity['receiptorid']);
            $Subject = '????????????';
            $body = '????????????????????????????????????????????????????????????????????????,<br> ??????????????????:' . $entity['newservicecontractsno'] . '<br>?????????????????????';
            $address = array(array('mail' => $current_users->column_fields['email1'], 'name' => $current_users->column_fields['last_name']));
            //$address=array(array('mail'=>'steel.liu@71360.com','name'=>$current_users->column_fields['last_name']));
            Vtiger_Record_Model::sendMail($Subject, $body, $address);

        }
        // cxh 2019-08-02 ?????? ?????????????????????????????????????????????modulestatus???????????????????????????????????????????????????
        $params['salesorderid']=$request->get('record');
        $params['workflowsid']=$workflowsid;
        $this->hasAllAuditorsChecked($params);

        $nextNodeFlag = $this->getNextNodeFlag($record,$sequence);
        if($nextNodeFlag=='DO_PRINT'){
            $recordModel = Vtiger_Record_Model::getInstanceById($record,'SuppContractsAgreement',true);
            $entity=$recordModel->entity->column_fields;

            $supplierContractRecordModel=Vtiger_Record_Model::getInstanceById($entity['suppliercontractsid'],'SupplierContracts',true);
            $entity2=$supplierContractRecordModel->entity->column_fields;

            //????????????????????????????????????????????????
            $sealParams=array(
                'uid'=>$entity['assigned_user_id'],
                'name'=>$entity['newservicecontractsno'],
                'sealapply_id'=>$record,
                'sealseq'=>$entity['sealseq'],
                'invoicecompany'=>$entity2['invoicecompany'],
            );
            $sContractnoGenerationRecordModel = SContractNoGeneration_Record_Model::getCleanInstance("SContractNoGeneration");
            $result=$sContractnoGenerationRecordModel->syncToSealHandler($sealParams,'SuppContractsAgreement',true);
            if(!$result['success']){
                echo json_encode(array('success'=>false,'error'=>array('message'=>$result['msg'])));
                exit();
            }

            $files=explode("*|*",$entity['file']);
            $attachmentsids=array();
            foreach ($files as $file){
                $attachmentsid = explode("##",$file);
                $attachmentsids[]=$attachmentsid[1];
            }
            //????????????????????????????????????????????????
            $sealParams=array(
                "sealapply_id"=>$record,
                "uid"=>$entity['assigned_user_id'],
                "attachmentsids"=>$attachmentsids,
                "module"=>'SuppContractsAgreement',
            );
            $sContractnoGenerationRecordModel = SContractNoGeneration_Record_Model::getCleanInstance("SContractNoGeneration");
            $data = $sContractnoGenerationRecordModel->sendFileToZhangGuanJia($sealParams);
            if($data['success']&&$data['fileStr']){
                $this->db->pquery("update vtiger_suppcontractsagreement set file=? where contractsagreementid=?",array($data['fileStr'],$record));
            }
        }
    }
    /**
     * ???????????????????????????
     * @param Vtiger_Request $request
     */
    /*public function backallBefore(Vtiger_Request $request){
        $recordid=$request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordid,'SuppContractsAgreement');
        $entityData = $recordModel->entity->column_fields;
        if(!empty($entityData['newservicecontractsno'])){
            $resultaa['success'] = 'false';
            $resultaa['error']['message'] = "????????????????????????,????????????!";
            //??????????????????????????????????????????
            if( $request->get('isMobileCheck')==1){
                return $resultaa;
            }else{
                echo json_encode($resultaa);
                exit;
            }
        }
    }*/
    /**
     * ???????????????????????????
     * @param Vtiger_Request $request
     */
    public function backallAfter(Vtiger_Request $request)
    {
        $stagerecordid=$request->get('isrejectid');
        $record=$request->get('record');
        $query="SELECT
                    vtiger_workflowstages.workflowstagesflag,
                    vtiger_workflowstages.workflowsid
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'SuppContractsAgreement'";
        $result=$this->db->pquery($query,array($stagerecordid));

        $currentflag=$this->db->query_result($result,0,'workflowstagesflag');
        $workflowsid=$this->db->query_result($result,0,'workflowsid');
        $this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='SuppContractsAgreement' AND vtiger_salesorderworkflowstages.workflowsid=?",array($record,$workflowsid));
        //$this->db->pquery("UPDATE vtiger_suppcontractsagreement SET modulestatus='a_normal' WHERE contractsagreementid=?",array($record));


    }

    /**
     * ????????????????????????????????????
     * @param ???????????????
     * @param ???????????????
     * @return ????????????
     */
    public function recursiveContractNumber($odcontract_no,$num){
        $contract_no=$odcontract_no."-".$num;
        $result=$this->db->pquery("SELECT contract_no FROM vtiger_suppliercontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_suppliercontracts.suppliercontractsid WHERE vtiger_crmentity.deleted=0 AND vtiger_suppliercontracts.contract_no=?",array($contract_no));
        if($this->db->num_rows($result)){
            ++$num;
            return self::recursiveContractNumber($odcontract_no,$num);
        }else{
            return $contract_no;
        }

    }
    /**
     * ?????????????????????
     * @param unknown $modulename
     * @param unknown $workflowsid
     * @param unknown $salesorderid
     * @param string $isedit
     */
    public function makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit=''){
        parent::makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit='');
        $query="UPDATE vtiger_salesorderworkflowstages,
				 vtiger_suppcontractsagreement
				SET vtiger_salesorderworkflowstages.accountid=vtiger_suppcontractsagreement.vendorid,
				 vtiger_salesorderworkflowstages.modulestatus='p_process',
				    vtiger_salesorderworkflowstages.accountname=(SELECT vtiger_vendor.vendorname FROM vtiger_vendor WHERE vtiger_vendor.vendorid=vtiger_suppcontractsagreement.vendorid)
				WHERE vtiger_suppcontractsagreement.contractsagreementid=vtiger_salesorderworkflowstages.salesorderid
				AND vtiger_salesorderworkflowstages.salesorderid=?  AND  vtiger_salesorderworkflowstages.workflowsid=?";
        $this->db->pquery($query,array($salesorderid,$workflowsid));
        global $current_user;
        $needletwo='H283::';
        $query='SELECT vtiger_departments.parentdepartment FROM vtiger_departments WHERE departmentid=?';
        $result=$this->db->pquery($query,array($current_user->departmentid));
        $data=$this->db->raw_query_result_rowdata($result,0);
        $parentdepartment=$data['parentdepartment'];
        $parentdepartment.='::';
        if(strpos($parentdepartment,$needletwo)===false){
            $deletedSql="DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowstagesflag=? AND workflowsid=?";
            $this->db->pquery($deletedSql,array($salesorderid,'COMPANY_MEDIA',$workflowsid));
        }else {
            $updateSql = "UPDATE vtiger_salesorderworkflowstages,
                 vtiger_workflowstages
                SET vtiger_salesorderworkflowstages.ishigher = 1,
                 vtiger_salesorderworkflowstages.higherid =?
                WHERE
                    vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                AND vtiger_salesorderworkflowstages.salesorderid =?
                AND vtiger_workflowstages.workflowstagesflag='COMPANY_MEDIA'
                AND vtiger_salesorderworkflowstages.workflowsid = vtiger_workflowstages.workflowsid
                AND vtiger_salesorderworkflowstages.workflowsid =?";
            $this->db->pquery($updateSql, array(7629, $salesorderid, $workflowsid));
        }
        //????????? ???????????????????????????????????????
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$salesorderid,'salesorderworkflowstagesid'=>0));
       /* die();*/
    }

    	/**
     * @??????????????????????????????
     * @???????????????
     * @param Vtiger_Request $request
     */
    function workflowcheckbefore(Vtiger_Request $request)
    {
        $stagerecordid = $request->get('stagerecordid');
        $record = $request->get('record');
        $db = PearDatabase::getInstance();

        $query = "SELECT
                    vtiger_workflowstages.workflowstagesflag,
                           vtiger_salesorderworkflowstages.sequence,
       vtiger_salesorderworkflowstages.higherid
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'SuppContractsAgreement'";
        $result = $db->pquery($query, array($stagerecordid));
        $data = $db->fetchByAssoc($result, 0);
        $currentflag = $db->query_result($result, 0, 'workflowstagesflag');
        $sequence = $db->query_result($result, 0, 'sequence');

        $recordModel = Vtiger_Record_Model::getInstanceById($record,'SuppContractsAgreement',true);
        $entity=$recordModel->entity->column_fields;
        $nextFlag = $this->getNextNodeFlag($record,$sequence);
        if($nextFlag=='DO_PRINT'){
            if(!$entity['file']){
                $resultaa['success'] = 'false';
                $resultaa['error']['message'] = "?????????????????????";
                //??????????????????????????????????????????
                if( $request->get('isMobileCheck')==1){
                    return $resultaa;
                }else{
                    echo json_encode($resultaa);
                    exit;
                }
            }
            $supplierContractRecordModel=Vtiger_Record_Model::getInstanceById($entity['suppliercontractsid'],'SupplierContracts',true);
            $entity2=$supplierContractRecordModel->entity->column_fields;

            //????????????????????????????????????????????????
            $sealParams=array(
                'uid'=>$entity['assigned_user_id'],
                'name'=>$record,
                'sealapply_id'=>$record,
                'sealseq'=>$entity['sealseq'],
                'invoicecompany'=>$entity2['invoicecompany'],
                'apply_status'=>0
            );
            $sContractnoGenerationRecordModel = SContractNoGeneration_Record_Model::getCleanInstance("SContractNoGeneration");
            $result=$sContractnoGenerationRecordModel->syncToSealHandler($sealParams,'SuppContractsAgreement');
            if(!$result['success']){
                $resultaa['success'] = 'false';
                $resultaa['error']['message'] = $result['msg'];
                //??????????????????????????????????????????
                if( $request->get('isMobileCheck')==1){
                    return $resultaa;
                }else{
                    echo json_encode($resultaa);
                    exit;
                }
            }
        }

    }

    function getNextNodeFlag($record,$sequence){
        $db=PearDatabase::getInstance();
        $sql="select workflowstagesflag from vtiger_salesorderworkflowstages where salesorderid=? and sequence>? order by sequence limit 1";
        $result = $db->pquery($sql,array($record,$sequence));
        if($db->num_rows($result)){
            $row=$db->fetchByAssoc($result,0);
            return $row['workflowstagesflag'];
        }
        return '';
    }
}
?>
