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

class ContractsAgreement extends CRMEntity
{
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_contractsagreement';
    var $table_index = 'contractsagreementid';
    var $tab_name = Array('vtiger_crmentity', 'vtiger_contractsagreement');
    var $tab_name_index = Array('vtiger_crmentity' => 'crmid', 'vtiger_contractsagreement' => 'contractsagreementid');
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
     * 更新后处理
     * @param unknown $module
     */
    function save_module($module)
    {
    }
    function saveentity($module, $fileid = '') {
        /*parent::saveentity($module, $fileid = '');
        $id=$_REQUEST['record']>0?$_REQUEST['record']:$this->id;
        global $current_user;
        $arr=array(date('Y-m-d H:i:s'),$id);
        $this->db->pquery("UPDATE vtiger_contractsagreement SET dateofapp=?,modulestatus='b_actioning' WHERE contractsagreementid=?",$arr);

        //提取查询，再更新 gaocl add 2018/03/22 (解决工作流节点打回，编辑后节点无权限审核问题)
        $query_departmentid = "SELECT departmentid FROM vtiger_user2department WHERE userid=?";
        $result_departmentid = $this->db->pquery($query_departmentid,array($current_user->id));
        $num = $this->db->num_rows($result_departmentid);
        $departmentid = "";
        if ($num > 0) {
            $departmentid = $this->db->query_result($result_departmentid,0,'departmentid');
        }
        if(empty($departmentid)) return;

        $query = "SELECT T.departmentid,M.oneaudituid FROM vtiger_auditsettings M
                INNER JOIN 
                (SELECT departmentid,depth FROM vtiger_departments WHERE
                FIND_IN_SET(departmentid,(SELECT REPLACE(a.parentdepartment,'::',',') FROM vtiger_departments a WHERE a.departmentid = ?))
                ) T ON(M.department=T.departmentid)
                WHERE M.auditsettingtype='ContractsAgreement' 
                ORDER BY T.depth DESC LIMIT 1";
        $result=$this->db->pquery($query,array($departmentid));
        $num = $this->db->num_rows($result);
        if ($num) {
            $row = $this->db->raw_query_result_rowdata($result);
            $higherid = $row['oneaudituid'];
            $this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=? WHERE vtiger_salesorderworkflowstages.sequence=1 AND vtiger_salesorderworkflowstages.salesorderid=? AND isaction=1 AND vtiger_salesorderworkflowstages.modulename='ContractsAgreement'",array($higherid,$id));
        }*/
        //$this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=(SELECT vtiger_auditsettings.oneaudituid FROM `vtiger_auditsettings` INNER JOIN (SELECT vtiger_departments.parentdepartment,vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.departmentid='{$current_user->departmentid}') AS tempdepart ON FIND_IN_SET(vtiger_auditsettings.department,REPLACE(tempdepart.parentdepartment,'::',',')) LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_auditsettings.department  WHERE vtiger_auditsettings.auditsettingtype='ContractsAgreement' ORDER BY abs(LENGTH(IFNULL(tempdepart.parentdepartment,0))-LENGTH(IFNULL(vtiger_departments.parentdepartment,0))) LIMIT 1) WHERE vtiger_salesorderworkflowstages.sequence=1 AND vtiger_salesorderworkflowstages.salesorderid=? AND isaction=1 AND vtiger_salesorderworkflowstages.modulename='ContractsAgreement'",array($id));

        parent::saveentity($module, $fileid = '');
        $id=$_REQUEST['record']>0?$_REQUEST['record']:$this->id;
        global $current_user;
        $arr=array(date('Y-m-d H:i:s'),$id);
        $eleccontractstatus=$_POST['signaturetype']=='eleccontract'?",eleccontractstatus='a_elec_sending'":'';
        $this->db->pquery("UPDATE vtiger_contractsagreement SET dateofapp=?,modulestatus='b_check'".$eleccontractstatus." WHERE contractsagreementid=?",$arr);
        //$departmentid=empty($current_user->departmentid)?'H1':$current_user->departmentid;
        //$result=$this->db->pquery("SELECT vtiger_auditsettings.oneaudituid FROM `vtiger_auditsettings` INNER JOIN (SELECT vtiger_departments.parentdepartment,vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.departmentid=?) AS tempdepart ON FIND_IN_SET(vtiger_auditsettings.department,REPLACE(tempdepart.parentdepartment,'::',',')) LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_auditsettings.department  WHERE vtiger_auditsettings.auditsettingtype='ContractsAgreement' ORDER BY abs(LENGTH(IFNULL(tempdepart.parentdepartment,0))-LENGTH(IFNULL(vtiger_departments.parentdepartment,0))) LIMIT 1",array($departmentid));
        //$data=$this->db->query_result_rowdata($result,0);
        $departmentid=$_SESSION['userdepartmentid'];
        $this->setAudituid("ContractsAgreement",$departmentid,$id,$_POST['workflowsid']);
        $recordModel = Vtiger_Record_Model::getInstanceById($_REQUEST['servicecontractsid'], 'ServiceContracts');

        if(in_array($recordModel->get('companycode'),$recordModel->Kllcompanycode)){
            $query='SELECT vtiger_departments.parentdepartment FROM vtiger_departments WHERE departmentid=? limit 1';
            $result=$this->db->pquery($query,array($departmentid));
            $data=$this->db->raw_query_result_rowdata($result,0);
            $parentdepartment=$data['parentdepartment'];
            $parentdepartment.='::';
            $needle='H283::';
            if(strpos($parentdepartment,$needle)!==false) {
                $data = $this->getAudituid('ContractsAgreement', $departmentid);
                $userid1 = $data['audituid4'] > 0 ? $data['audituid4'] : $data['oneaudituid'];
                $userid2 = $data['audituid4'] > 0 ? $data['oneaudituid'] : $data['towaudituid'];
                $userid3 = $data['audituid4'] > 0 ? $data['towaudituid'] : $data['audituid3'];
                $updateSql = "UPDATE vtiger_salesorderworkflowstages SET ishigher = 1,higherid =? WHERE salesorderid =? AND workflowstagesflag='AUDIT_VERIFICATION' AND workflowsid =?";
                $this->db->pquery($updateSql, array($userid1, $id, $_REQUEST['workflowsid']));
                $updateSql = "UPDATE vtiger_salesorderworkflowstages SET ishigher = 1,higherid =? WHERE salesorderid =? AND workflowstagesflag='TWO_VERIFICATION' AND workflowsid =?";
                $this->db->pquery($updateSql, array($userid2, $id, $_REQUEST['workflowsid']));
                $updateSql = "UPDATE vtiger_salesorderworkflowstages SET ishigher = 1,higherid =? WHERE salesorderid =? AND workflowstagesflag='THREE_VERIFICATION' AND workflowsid =?";
                $this->db->pquery($updateSql, array($userid3, $id, $_REQUEST['workflowsid']));
                $updateSql = "UPDATE vtiger_salesorderworkflowstages SET workflowstagesname='审核生成合同编号',ishigher = 1,higherid =? WHERE salesorderid =? AND workflowstagesflag='CREATE_CODE' AND workflowsid =?";
                $this->db->pquery($updateSql, array(792, $id, $_POST['workflowsid']));
            }
            //2021.06.03 把初级财务主管删掉
//            $deleteSql="DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowstagesflag IN('TREASURER_ONE','TREASURER_TWO')";
            $deleteSql="DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowstagesflag IN('TREASURER_ONE')";
            $this->db->pquery($deleteSql,array($id));//删除财务主管的节点
        }elseif(in_array($recordModel->get('companycode'),$recordModel->TREASURER_TWO)){
            $deleteSql="DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowstagesflag IN('TREASURER_ONE')";
            $this->db->pquery($deleteSql,array($id));//删除财务主管的节点
        }else{
//            $deleteSql="DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowstagesflag IN('TREASURER_TWO')";
//            $this->db->pquery($deleteSql,array($id));//删除财务主管的节点
        }
        //新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$id,'salesorderworkflowstagesid'=>0));
        /*$data=$this->getAudituid("ContractsAgreement",$departmentid);
        $this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=? WHERE vtiger_salesorderworkflowstages.sequence=1 AND vtiger_salesorderworkflowstages.salesorderid=? AND isaction=1 AND vtiger_salesorderworkflowstages.modulename='ContractsAgreement'",array($data['oneaudituid'],$id));*/
    }
    /**节点审核时到了指定节点抓取时间
     * 后置事件
     * @param Vtiger_Request $request
     */
    function workflowcheckafter(Vtiger_Request $request){
        $stagerecordid=$request->get('stagerecordid');
        $record=$request->get('record');

        $query="SELECT
                    vtiger_salesorderworkflowstages.workflowstagesflag,
        		vtiger_salesorderworkflowstages.workflowsid
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'ContractsAgreement'";
        $result=$this->db->pquery($query,array($stagerecordid));
        $currentflag=$this->db->query_result($result,0,'workflowstagesflag');
        $workflowsid=$this->db->query_result($result,0,'workflowsid');
        $recordModel = Vtiger_Record_Model::getInstanceById($record,'ContractsAgreement',true);
        $entity=$recordModel->entity->column_fields;
        $currentflag=trim($currentflag);
        $datetime=date('Y-m-d H:i:s');
        switch($currentflag){
            case 'CREATE_CODE':
                //生成合同编号
                if(empty($entity['newservicecontractsno'])) {
                    $contractRecordModel = Vtiger_Record_Model::getInstanceById($entity['servicecontractsid'], 'ServiceContracts');
                    $contractentity = $contractRecordModel->entity->column_fields;
                    //$result = $this->db->pquery('SELECT 1 FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.contract_no LIKE \'' . $contractentity['contract_no'] . '%\'', array());
                    $result = $this->db->pquery("SELECT servicecontracts_no FROM vtiger_servicecontracts_print WHERE vtiger_servicecontracts_print.servicecontracts_no LIKE '{$contractentity['contract_no']}-%' ORDER BY servicecontractsprintid DESC limit 1", array());
                    $num = $this->db->num_rows($result);
                    if ($num) {
                        $row=$this->db->raw_query_result_rowdata($result);
                        $contractTemp=$row['servicecontracts_no'];
                        $contractArray=explode("-",$contractTemp);
                        $contractnum=end($contractArray);
                        $contractnum=trim($contractnum);
                        $contractnum++;
                    }else{
                       $contractnum=1;
                    }
                    $servicecontracts_no =$this->recursiveContractNumber($contractentity['contract_no'],$contractnum);
                    $sql = 'INSERT INTO vtiger_servicecontracts_print(nostand,servicecontracts_no,createdtime,constractsstatus,smownerid) VALUES(1,?,?,?,?)';
                    $this->db->pquery($sql, array($servicecontracts_no, $datetime, 'c_generated',$entity['assigned_user_id']));
                    $insertidresult=$this->db->pquery('SELECT max(servicecontractsprintid) AS id FROM `vtiger_servicecontracts_print`',array());
                    $idresult=$this->db->query_result_rowdata($insertidresult,0);
                    $sql='UPDATE `vtiger_contractsagreement` SET servicecontractsprintid=?,newservicecontractsno=? WHERE contractsagreementid=?';
                    $this->db->pquery($sql,array($idresult['id'],$servicecontracts_no,$record));
                    //   审核通过创建合同编号后把编号同时更新到       vtiger_salesorderworkflowstages  总的审核表
                    $this->db->pquery('UPDATE `vtiger_salesorderworkflowstages` SET salesorder_nono=? WHERE vtiger_salesorderworkflowstages.salesorderid=?', array($servicecontracts_no,$record));
                }
                break;
            case 'AUDIT_VERIFICATION':
                //第二个节点指定审核人
                $this->AuditAuditNodeJump($record,$entity['workflowsid'],$entity['assigned_user_id'],'ContractsAgreement','ContractsAgreement',1);
                break;
            case 'TWO_VERIFICATION':
                //第二个节点指定审核人
                $this->AuditAuditNodeJump($record,$entity['workflowsid'],$entity['assigned_user_id'],'ContractsAgreement','ContractsAgreement',2);
                break;
            case 'DO_PRINT':
                global $current_user;

                //合同打印节点
                $this->db->pquery("UPDATE vtiger_servicecontracts_print,vtiger_contractsagreement SET constractsstatus='c_print',printer=?,printtime=? WHERE vtiger_servicecontracts_print.servicecontractsprintid=vtiger_contractsagreement.servicecontractsprintid AND vtiger_contractsagreement.contractsagreementid=?",array($current_user->id,$datetime,$record));

            default :
                break;
        }
        $this->db->pquery("UPDATE vtiger_contractsagreement SET workflowsnode=(SELECT vtiger_salesorderworkflowstages.workflowstagesname FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.isaction=1 AND vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='ContractsAgreement' LIMIT 1) WHERE contractsagreementid=?",array($record,$record));//取待审核节点的名称

        if($entity['modulestatus']=='c_complete')
        {
            $query='SELECT vtiger_servicecontracts_print.servicecontractsprintid,vtiger_servicecontracts_print.servicecontracts_no FROM `vtiger_servicecontracts_print` LEFT JOIN vtiger_contractsagreement ON vtiger_contractsagreement.servicecontractsprintid=vtiger_servicecontracts_print.servicecontractsprintid
                    WHERE vtiger_contractsagreement.contractsagreementid=? limit 1';
            $dataPrintResult=$this->db->pquery($query,array($record));
            $dataPrintRes=$this->db->query_result_rowdata($dataPrintResult,0);
            $checkquery='SELECT 1 FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.contract_no=?';
            $checkresult=$this->db->pquery($checkquery,array($dataPrintRes['servicecontracts_no']));
            $contractRecordModel = Vtiger_Record_Model::getInstanceById($entity['servicecontractsid'], 'ServiceContracts');
            if($this->db->num_rows($checkresult)==0)
            {

                $contractentity = $contractRecordModel->entity->column_fields;
                unset($_REQUEST);//防止信息干扰
                $_REQUES['record'] = '';
                $request = new Vtiger_Request($_REQUES, $_REQUES);
                $request->set('contract_no', $dataPrintRes['servicecontracts_no']);
                $request->set('assigned_user_id', $entity['assigned_user_id']);
                $request->set('Receiveid', $entity['assigned_user_id']);
                $request->set('modulestatus', 'c_stamp');
                $request->set('isautoclose', 1);
                $request->set('sc_related_to', $entity['account_id']);
                $request->set('remark', '补充协议');
                $_REQUEST['parent_contracttypeid'] = $contractentity['parent_contracttypeid'];
                $request->set('parent_contracttypeid', $contractentity['parent_contracttypeid']);
                $request->set('contract_type', $contractentity['contract_type']);
                $request->set('signaturetype', $entity['signaturetype']);
                if($entity['signaturetype']=='eleccontract'){
                    $request->set('originator', $entity['originator']);
                    $request->set('originatormobile', $entity['originatormobile']);
                    $request->set('elereceiver', $entity['elereceiver']);
                    $request->set('elereceivermobile', $entity['elereceivermobile']);
                    $request->set('eleccontracttpl', $entity['eleccontracttpl']);
                    $request->set('invoicecompany', $entity['invoicecompany']);
                    $request->set('eleccontractid', $entity['eleccontractid']);
                    $request->set('eleccontracttplid', $entity['eleccontracttplid']);
                    $request->set('file', $entity['eleccontracttpl']);
                }
                $request->set('supplementarytype', $entity['supplementarytype']);
                $request->set('companycode', $contractentity['companycode']);
                $request->set('invoicecompany', $contractentity['invoicecompany']);
                $request->set('module', 'ServiceContracts');
                $request->set('view', 'Edit');
                $request->set('action', 'Save');
                $ressorder = new ServiceContracts_Save_Action();
                $ressorderecord = $ressorder->saveRecord($request);
                $serviceconrecord = $ressorderecord->getId();
                if($entity['signaturetype']=='eleccontract'){
                    $argsData=array(
                        "contractId"=>$entity['eleccontractid'],
                        "isPass"=>1,
                        //"number"=>$dataPrintRes['servicecontracts_no'],
                        "contractNumber"=>$dataPrintRes['servicecontracts_no'],
                        "reason"=>""
                    );
                    $jsonData=$ressorderecord->setAuditStatus($argsData);
                    $array=json_decode($jsonData,true);
                    $eleccontractstatus='b_elec_actioning';
                    if($array['success']){
                        //$file=$recordModel->get('file');
                        $file=$recordModel->get('eleccontracttpl');
                        $files=explode('##',$file);
                        $filesname=trim($files[0],'.doc');
                        $filesname=trim($filesname,'.docx');
                        $filesname=trim($filesname,'.pdf');
                        $ressorderecord->fileSave($array['data'],'files_style8','');
                        $ressorderecord->sendSMS(array('statustype'=>'','mobile'=>$ressorderecord->get('elereceivermobile'),'eleccontracttpl'=>$filesname,'url'=>$ressorderecord->elecContractUrl));
                        $query='SELECT label FROM vtiger_crmentity WHERE crmid=?';
                        $accountResult=$this->db->pquery($query,array($entity['account_id']));
                        $body2='';
                        $body2 .= "<span style='font-weight:bold;'>系统已发送电子合同给客户，请及时与客户确认并跟进客户完成合同签署！</span><br>";
                        $body2 .= "<span style='font-weight:bold'>合同编号:&nbsp;</span>".$dataPrintRes['servicecontracts_no'].'<br>';
                        $body2 .= "<span style='font-weight:bold'>客户:&nbsp;</span>".$accountResult->fields['label'].'<br>';
                        $body2 .= "<span style='font-weight:bold'>发送时间:&nbsp;</span>".date('Y-m-d H:i:s').'<br>';
                        $body2 .= "<span style='font-weight:bold'>联系人:&nbsp;</span>".$entity['elereceiver'].'<br>';
                        $body2 .= "<span style='font-weight:bold'>联系人手机号:&nbsp;</span>".$entity['elereceivermobile'].'<br>';
                        $query='SELECT last_name,email1 FROM vtiger_users WHERE id=? LIMIT 1';
                        $useResult=$this->db->pquery($query,array($entity['assigned_user_id']));
                        Vtiger_Record_Model::sendMail('电子合同发送通知---"'.$dataPrintRes['servicecontracts_no'].'"',$body2,array(array('name'=>$useResult->fields['last_name'],'mail'=>$useResult->fields['email1'])));
                    }else{
                        $eleccontractstatus='a_elec_actioning_fail';
                    }
                    $this->db->pquery('UPDATE vtiger_servicecontracts SET modulestatus=\'已发放\',signaturetype=\'eleccontract\',eleccontractstatus=?,isstandard=1,sideagreement=1,creatorid=?,receiptorid=?,contract_no=?,servicecontractsprintid=?,servicecontractsprint=? WHERE servicecontractsid=?', array($eleccontractstatus,$entity['assigned_user_id'],$entity['receiptorid'],$dataPrintRes['servicecontracts_no'], $dataPrintRes['servicecontractsprintid'], $dataPrintRes['servicecontractsprintid'] . '-8', $serviceconrecord));
                    $this->db->pquery("UPDATE vtiger_servicecontracts_print SET constractsstatus='c_receive',stamptime=?,receivedtime=? WHERE servicecontractsprintid=?", array($datetime,$datetime, $dataPrintRes['servicecontractsprintid']));
                    $file=$entity['file'];
                    $fileArr=explode('*|*',$file);
                    $filestr='';
                    foreach($fileArr as $value){
                        $eplodevalue=explode('##',$value);
                        $filestr.=(int)$eplodevalue[1].',';
                    }
                    $filestr=trim($filestr,',');
                    if(!empty($filestr)){
                        $this->db->pquery("UPDATE `vtiger_files` SET description='ServiceContracts',filestate='filestate1',relationid=? WHERE attachmentsid in(".$filestr.")",array($serviceconrecord));
                    }
                    $this->db->pquery("UPDATE vtiger_contractsagreement SET eleccontractstatus='b_elec_actioning' WHERE contractsagreementid=?",array($record));//取待审核节点的名称
                }else{
                    $this->db->pquery('UPDATE vtiger_servicecontracts SET modulestatus=\'c_stamp\',isstandard=1,sideagreement=1,creatorid=?,receiptorid=?,contract_no=?,servicecontractsprintid=?,servicecontractsprint=? WHERE servicecontractsid=?', array($entity['assigned_user_id'],$entity['receiptorid'],$dataPrintRes['servicecontracts_no'], $dataPrintRes['servicecontractsprintid'], $dataPrintRes['servicecontractsprintid'] . '-8', $serviceconrecord));
                    $attachments = explode('##', $entity['file']);
                    if (!empty($attachments[1])) {
                        $this->db->pquery("UPDATE `vtiger_files` SET description='ServiceContracts',style='files_style7',filestate='filestate1',relationid=? WHERE attachmentsid=?", array($serviceconrecord, $attachments[1]));
                    }
                    $this->db->pquery("UPDATE vtiger_servicecontracts_print SET constractsstatus='c_stamp',stamptime=? WHERE servicecontractsprintid=?", array($datetime, $dataPrintRes['servicecontractsprintid']));
                    $Subject = '合同领取';
                    $body = '您提交的合同补充协议已通过审核且打印盖章完成,<br>合同编号:' . $dataPrintRes['servicecontracts_no'] . '<br>请到财务部领取';
                    $user = new Users();
                    $current_user1 = $user->retrieveCurrentUserInfoFromFile($entity['receiptorid']);
                    $address = array(array('mail' => $current_user1->column_fields['email1'], 'name' => $current_user1->column_fields['last_name']));
                    Vtiger_Record_Model::sendMail($Subject, $body, $address);
                }
                $this->db->pquery('UPDATE vtiger_contractsagreement SET newservicecontractsid=?,newservicecontractsno=? WHERE contractsagreementid=?',array($serviceconrecord,$dataPrintRes['servicecontracts_no'],$record));
            }
            if($entity['supplementarytype']=='tovoid'){
                $contractRecordModel->getModule()->doCancelNew($contractRecordModel->get('contract_no'));//作废订单
            }

        }
        // cxh 2019-08-02 添加 如果该审核需要修改审核列表中的modulestatus（审核流程状态）审核完后走下面代码
        $params['salesorderid']=$request->get('record');
        $params['workflowsid']=$workflowsid;
        $this->hasAllAuditorsChecked($params);
    }

    //打回合同补充协议工作流，取消合同编号的判断 2021-8-3
    /**
     * 工作流打回处理事件
     * @param Vtiger_Request $request
     */
    /*public function backallBefore(Vtiger_Request $request){
        $recordid=$request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordid,'ContractsAgreement',true);
        $entityData = $recordModel->entity->column_fields;
        if(!empty($entityData['newservicecontractsno'])){
            $resultaa['success'] = 'false';
            $resultaa['error']['message'] = "合同编号已经生成,不能打回!";
            //若果是移动端请求则走这个返回
            if( $request->get('isMobileCheck')==1){
                return $resultaa;
            }else{
                echo json_encode($resultaa);
                exit;
            }
        }
    }*/
    /**
     * 合同作废打回中处理
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
                AND vtiger_salesorderworkflowstages.modulename = 'ContractsAgreement'";
        $result=$this->db->pquery($query,array($stagerecordid));
        $workflowsid=$this->db->query_result($result,0,'workflowsid');
        $this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='ContractsAgreement' AND vtiger_salesorderworkflowstages.workflowsid=?",array($record,$workflowsid));
        $recordModel=Vtiger_Record_Model::getInstanceById($record,'ContractsAgreement');
        if($recordModel->get('signaturetype')=='eleccontract'){
            $file=$recordModel->get('file');
            $fileArr=explode('*|*',$file);
            $filestr='';
            foreach($fileArr as $value){
                $eplodevalue=explode('##',$value);
                $filestr.=(int)$eplodevalue[1].',';
            }
            $filestr=trim($filestr,',');
            if(!empty($filestr)){
                $this->db->pquery("UPDATE `vtiger_files` SET description='ContractsAgreement',delflag=1,relationid=? WHERE attachmentsid in(".$filestr.")",array($record));
            }
            $this->db->pquery("UPDATE vtiger_contractsagreement SET eleccontractstatus='a_elec_withdraw' WHERE contractsagreementid=?",array($record));
            $argsData=array(
                "contractId"=>$recordModel->get('eleccontractid'),
                "isPass"=>0,
                "number"=>'',
                "reason"=>"审核不通过"
            );
            $contractModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
            $contractModel->setAuditStatus($argsData);
        }
    }
    /**
     * 递归检测合同编号是否重复
     * @param 原合同编号
     * @param 合同流水号
     * @return 合同编号
     */
    public function recursiveContractNumber($odcontract_no,$num){
        $contract_no=$odcontract_no."-".$num;
        $result=$this->db->pquery("SELECT servicecontracts_no FROM vtiger_servicecontracts_print WHERE vtiger_servicecontracts_print.servicecontracts_no=?",array($contract_no));
        if($this->db->num_rows($result)){
            ++$num;
            return self::recursiveContractNumber($odcontract_no,$num);
        }else{
            return $contract_no;
        }

    }
    /**
     * 重写工作流生成
     * @param unknown $modulename
     * @param unknown $workflowsid
     * @param unknown $salesorderid
     * @param string $isedit
     */
    public function makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit=''){
        global $current_user,$adb;
        parent::makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit='');
        $query='SELECT * FROM vtiger_users LEFT JOIN vtiger_user2department on vtiger_user2department.userid=vtiger_users.id LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid WHERE vtiger_users.id=?';
        $result=$adb->pquery($query,array($current_user->id));
        $parentdepartment=$result->fields['parentdepartment'];
        $parentdepartment.='::';
        if(strpos($parentdepartment,'H3::')===false || strpos($parentdepartment,'H23::')!==false){
            $deleteSQL="DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND vtiger_salesorderworkflowstages.workflowstagesflag='CUSTOMER_SERVICE' AND vtiger_salesorderworkflowstages.modulename='ContractsAgreement'";
            $adb->pquery($deleteSQL,array($salesorderid));
            $query="SELECT sequence FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND vtiger_salesorderworkflowstages.modulename='ContractsAgreement' ORDER BY sequence ASC LIMIT 1";
            $result=$adb->pquery($query,array($salesorderid));
            $sql="UPDATE vtiger_salesorderworkflowstages SET isaction=1 WHERE salesorderid=? AND vtiger_salesorderworkflowstages.modulename='ContractsAgreement' AND sequence=?";
            $adb->pquery($sql,array($salesorderid,$result->fields['sequence']));
        }
        $query="UPDATE vtiger_salesorderworkflowstages,
				 vtiger_contractsagreement
				SET vtiger_salesorderworkflowstages.accountid=vtiger_contractsagreement.accountid,
				     vtiger_salesorderworkflowstages.modulestatus='p_process',
				 vtiger_salesorderworkflowstages.accountname=(SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid=vtiger_contractsagreement.accountid)
				WHERE vtiger_contractsagreement.contractsagreementid=vtiger_salesorderworkflowstages.salesorderid
				AND vtiger_salesorderworkflowstages.salesorderid=? AND  vtiger_salesorderworkflowstages.workflowsid=? ";
        $this->db->pquery($query,array($salesorderid,$workflowsid));
        //新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$salesorderid,'salesorderworkflowstagesid'=>0));
    }
}
?>
