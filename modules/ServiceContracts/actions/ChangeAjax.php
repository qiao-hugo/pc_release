<?php

class ServiceContracts_ChangeAjax_Action extends Vtiger_Action_Controller {
    private $cancelworkflowsid=614638;//作废工作流ID
    private $nostandworkflowsid=372;//非标工作流ID
    private $tobackstatus=2175125;//合同状态修改工作流ID
    private $Kllcompanycode=array('KLL','WXKLL','KLLDSHJKJJT','GZKLL');//凯丽隆审核的合同主体公司
    public $TREASURER_TWO=array('ZD','DCL','ZDWL','YJSKJ','HKKLLGJ');//上海财务审核的合同主体公司
    private $Kllneedle='H283::';//凯丽隆部节门点
    private $WXKLLneedle='H249::';//无锡凯丽隆部节门点
    function __construct(){
        parent::__construct();
        $this->exposeMethod('getproducts');
        $this->exposeMethod('getproductlist');
        $this->exposeMethod('getextaproducts');
        $this->exposeMethod('serviceconfirm');
        $this->exposeMethod('getservicecontracts_reviced');
        $this->exposeMethod('getuserlist');
        $this->exposeMethod('changereceived');
        $this->exposeMethod('checkProductAYear');
        $this->exposeMethod('getCancelInfo');
        $this->exposeMethod('ContractCancel');
        $this->exposeMethod('makeWorkflowStages');
        $this->exposeMethod('chuNaDoContractCancel');
        $this->exposeMethod('getSalesOrderStatus');
        $this->exposeMethod('assignreceiptor');
        $this->exposeMethod('getAccountStatus');
        $this->exposeMethod('getContractsAgreement');
        $this->exposeMethod('searchTyunBuyServiceInfo');
        $this->exposeMethod('ToVoidActivationCode');
        $this->exposeMethod('toBackStatus');
        $this->exposeMethod('checkcancel');
      $this->exposeMethod('confirmPayment');
        $this->exposeMethod('userMobile');
        $this->exposeMethod('sendElecContract');
        $this->exposeMethod('checkContractno');// cxh
//        $this->exposeMethod('checkCustomer');
//        $this->exposeMethod('checkMainContract');
        $this->exposeMethod('confirmDelivery');//产品完全交付
        $this->exposeMethod('isExistMainCompany');//判断合同主体公司是否存在
        $this->exposeMethod('agentList');
        $this->exposeMethod('getTyunWebCategory');
        $this->exposeMethod('leastPayMoney');
        $this->exposeMethod('changeStage');
        $this->exposeMethod('collateContract');//核对合同
        $this->exposeMethod('collateLog');//核对记录
        $this->exposeMethod('batchCollateContract');//批量核对合同
        $this->exposeMethod('exportData');//导出数据
        $this->exposeMethod('exportFile');//导出数据
    }

    function checkPermission(Vtiger_Request $request) {
        return true;
    }

    public function process(Vtiger_Request $request) {
        $mode=$request->getMode();
        if(!empty($mode)){
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    /**
     *合同类型对应的产品
     */
    public function getproducts_BACK(Vtiger_Request $request){
        global $configcontracttypeName;
        $contract_typeName = $request->get('contract_typeName');
        $tyunproductid = $request->get('tyunproductid');
        $contract_typeName = urldecode($contract_typeName);    //接收js返回值，解析编码
        if(empty($contract_typeName)){
            exit;
        }
        $sql="SELECT vtiger_contractsproductsrel.relproductid,vtiger_contractsproductsrel.isstandard FROM vtiger_contractsproductsrel
        WHERE vtiger_contractsproductsrel.contract_type=(SELECT contract_typeid FROM `vtiger_contract_type` WHERE vtiger_contract_type.contract_type=?)";
        $db = PearDatabase::getInstance();
        $relproductid = $db->pquery($sql,array($contract_typeName));
        //echo $db->num_rows($relproductid)."---";

        $isstandard = 0;  //非标合同

        if ($db->num_rows($relproductid)>0) {
            $row = $db->query_result_rowdata($relproductid, 0);
            //$result_relproductid = $db->query_result($relproductid, 'relproductid');
            //$isstandard = $db->query_result($relproductid, 'isstandard');
            $result_relproductid = $row['relproductid'];
            $isstandard = $row['isstandard'];

            // print_r($result_relproductid) ;die;
            if($result_relproductid !=""){
                $productid=explode(' |##| ', $result_relproductid);
                $sql = "SELECT productid,istyun,parentid,tyunproductid,productname FROM `vtiger_products` WHERE 1=1";
                if(!empty($tyunproductid)){
                    $recordModel=Vtiger_Record_Model::getCleanInstance('ActivationCode');
                    $arr_upgradeproduct_id = $recordModel->searchTyunUpgradeProduct($request);
                    if($arr_upgradeproduct_id && count($arr_upgradeproduct_id)>0){
                        $sql.=" AND FIND_IN_SET(tyunproductid,'"+implode(",",$arr_upgradeproduct_id)+"') ORDER BY sequence";
                        $product_result = $db->pquery($sql);
                        $product_list[] = $db->fetchByAssoc($product_result);
                    }
                }else{
                    foreach($productid as $value){
                        $sql.=" AND productid=".$value." ORDER BY sequence";
                        $product_result = $db->pquery($sql);
                        $product_list[] = $db->fetchByAssoc($product_result);
                    }
                }
            }
        }else{
            $product_list=array();
        }

        $type = $request->get('type');
        if ($type === 'serviceContractsEdit') {
            echo json_encode(array('product_list'=>$product_list, 'isstandard'=>$isstandard));
        } else {
            echo json_encode($product_list);
        }


    }

    public function getproducts(Vtiger_Request $request){
        global $configcontracttypeNameTYUN;
        $contract_typeName = $request->get('contract_typeName');
        $tyunproductid = $request->get('p_productid');
        $record = $request->get('record');
        $buytype = $request->get('buytype');
        $parent_contracttypeid = $request->get("parent_contracttypeid");
        $category = $request->get("category");
        $contract_typeName = urldecode($contract_typeName);    //接收js返回值，解析编码
        if(empty($contract_typeName)){
            exit;
        }
//        if(in_array($parent_contracttypeid,array('12','13','14','15','17')) || in_array($contract_typeName,$configcontracttypeNameTYUN)){
        if(in_array($contract_typeName,$configcontracttypeNameTYUN)){
            $this->getTyunProducts($request);
            return ;
        }
        global $adb;
        $otherproduct_list=$this->getExtraproduct();
        //升级产品获取
        if($buytype == "upgrade" || $buytype == "degrade"){
            if(!empty($tyunproductid)) {
                //判断是否降级
                if($buytype == 'degrade'){
                    $request->set("is_degrade",'1');
                }
                $recordACModel = Vtiger_Record_Model::getCleanInstance('ActivationCode');
                $arr_upgradeproduct_id = $recordACModel->searchTyunUpgradeProduct($request);

                if ($arr_upgradeproduct_id && count($arr_upgradeproduct_id) > 0) {
                    $upgradeproduct_ids=implode(",", $arr_upgradeproduct_id);
                    $sql = "SELECT productid,istyun,parentid,tyunproductid,productname FROM `vtiger_products` WHERE FIND_IN_SET(tyunproductid,'".$upgradeproduct_ids."') ORDER BY sequence";
                    $product_list = $adb->run_query_allrecords($sql);
                }
            }else{
                $product_list=array();
            }
            echo json_encode(array('product_list'=>$product_list, 'isstandard'=>0,'otherproduct_list'=>$otherproduct_list,'otherproducttype'=>1));
            exit;
        }

        $sql="SELECT REPLACE(vtiger_contractsproductsrel.relproductid,' |##| ',',') AS relproductid,vtiger_contractsproductsrel.isstandard FROM vtiger_contractsproductsrel
        WHERE vtiger_contractsproductsrel.contract_type=(SELECT contract_typeid FROM `vtiger_contract_type` WHERE vtiger_contract_type.contract_type=?)";

        $relproductid = $adb->pquery($sql,array($contract_typeName));
        //echo $db->num_rows($relproductid)."---";

        $isstandard = 0;  //非标合同


        if ($adb->num_rows($relproductid)>0) {
            $row = $adb->query_result_rowdata($relproductid, 0);
            //$result_relproductid = $db->query_result($relproductid, 'relproductid');
            //$isstandard = $db->query_result($relproductid, 'isstandard');
            $result_relproductid = $row['relproductid'];
            $isstandard = $row['isstandard'];

            if($result_relproductid !=""){
                $sql = " SELECT productid,istyun,parentid,tyunproductid,productname FROM `vtiger_products` WHERE FIND_IN_SET(productid,'".$result_relproductid."') ORDER BY sequence";
                $product_list = $adb->run_query_allrecords($sql);
            }
        }else{
            $product_list=array();
        }

        $type = $request->get('type');
        if ($type === 'serviceContractsEdit') {
            echo json_encode(array('product_list'=>$product_list, 'isstandard'=>$isstandard,'otherproduct_list'=>$otherproduct_list,'otherproducttype'=>1));
        } else {
            echo json_encode($product_list);
        }


    }

    /**
     * 产品的类型
     * @param Vtiger_Request $request
     */
    function getproductlist(Vtiger_Request $request){
        $parent_contracttypeid=$request->get('parent_contracttypeid');
        $db=PearDatabase::getInstance();
        $query = 'SELECT vtiger_contract_type.contract_type FROM vtiger_parent_contracttype_contracttyprel JOIN vtiger_contract_type ON vtiger_contract_type.contract_typeid=vtiger_parent_contracttype_contracttyprel.contract_typeid WHERE vtiger_contract_type.deleted=0 AND vtiger_parent_contracttype_contracttyprel.parent_contracttypeid='.$parent_contracttypeid;
        $arrrecords = $db->run_query_allrecords($query);
        $arrlist=array();
        if(!empty($arrrecords)){
            foreach($arrrecords as $value){
                $arrlist[]=$value['contract_type'];
            }
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($arrlist);
        $response->emit();
    }

    /**
     * 额外产品的选择
     * @param Vtiger_Request $request
     */
    function getextaproducts(Vtiger_Request $request){
        $contract_typeName = $request->get('contract_typeName');
        $contract_typeName = urldecode($contract_typeName);    //接收js返回值，解析编码
        if(empty($contract_typeName)){
            exit;
        }
        $sql="SELECT productid,productname FROM vtiger_products WHERE productcategory=?";
        $db = PearDatabase::getInstance();
        $result = $db->pquery($sql,array($contract_typeName));
        $row=$db->num_rows($result);
        if ($row>0) {
            for($i=0;$i<$row;$i++){
                $product_list[] =$db->fetchByAssoc($result);
            }
        }else{
            $product_list=array();
        }
        echo json_encode($product_list);

    }
    /**
     * 更新发放审查
     * @param Vtiger_Request $request
     */
    public function serviceconfirm(Vtiger_Request $request){
        $recordId = $request->get('recordid');//合同的id
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'ServiceContracts');
        $db=PearDatabase::getInstance();
        global $current_user;
        $message='';
        if(0==$recordModel->entity->column_fields['isconfirm']){
            $flag=true;
        }else{
            $newtemp=$recordModel->entity->column_fields['confirmvalue'];
            $temp=explode("##",$newtemp);
            $tempn=explode(',',$temp[0]);
            $flag=true;
            if(substr($tempn[1],0,10)==date('Y-m-d')){
                $flag=false;
                $message="该合同今天已经审查过了！";
            }
        }
        if($recordModel->getModule()->exportGrouprt('ServiceContracts','Received') &&$recordModel->entity->column_fields['modulestatus']=='已发放'&& $flag){
            $sql="UPDATE vtiger_servicecontracts SET vtiger_servicecontracts.confirmvalue=TRIM(TRAILING '##' FROM CONCAT('".$current_user->column_fields['last_name'].",".date('Y-m-d H:i:s')."##',IFNULL(confirmvalue,''))),isconfirm=1,confirmlasttime='".date('Y-m-d H:i:s')."' WHERE servicecontractsid=?";
            $db->pquery($sql,array($recordId));
        }
        // 审查合同添加 cxh start
        $data=array('flag'=>$flag,"message"=>$message);
        // 审查合同添加 cxh end
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    function getservicecontracts_reviced(Vtiger_Request $request){
        $receiveid = $request->get('ownerid');
        $num=ServiceContracts_Record_Model::servicecontracts_reviced($receiveid);
        $num=empty($num)?1:$num;
        $response = new Vtiger_Response();
        $response->setResult(array($num));
        $response->emit();

    }

    function isExistMainCompany(Vtiger_Request $request){
        $invoiceCompany = $request->get('invoicecompany');
        if(!$invoiceCompany){
            $response = new Vtiger_Response();
            $response->setResult(array(0));
            $response->emit();
            exit();
        }
        $compayCodeRecordModel = CompayCode_Record_Model::getCleanInstance("CompayCode");
        $res = $compayCodeRecordModel->isExistByCompanyName($invoiceCompany);
        $response = new Vtiger_Response();
        $response->setResult(array($res));
        $response->emit();
    }
    //获取用户名称,ID
    public function getCancelInfo(Vtiger_Request $request){
        $recordid=$request->get('recordid');
        $db=PearDatabase::getInstance();
        $query="SELECT
                    (SELECT CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ) ) ) AS last_name FROM vtiger_users WHERE cancelid = vtiger_users.id ) AS cancelname,
                    ifnull(canceltime,'') as canceltime,
                    ifnull(docanceltime,'') as docanceltime,
                    ifnull(cancelvoid,'') as cancelvoid,
                    ifnull(cancelfeeid,'') as cancelfeeid,
                    ifnull(cancelmoney,'') as cancelmoney,
                    ifnull(cancelremark,'') as cancelremark,
                    ifnull(pagenumber,'') as pagenumber,
                    ifnull(accountsdue,'') as accountsdue,
                    ifnull(receiptnumber,'') AS receiptnumber

                FROM
                    `vtiger_servicecontracts`
                WHERE
                    servicecontractsid =?";
        $result = $db->pquery($query, array($recordid));
        $response = new Vtiger_Response();
        $response->setResult($db->query_result_rowdata($result));
        $response->emit();

    }
    //获取用户名称,ID
    public function getuserlist(Vtiger_Request $request){
        $recordId = $request->get('recordid');
        global $current_user;
        if($recordId){
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'ServiceContracts');
            $accessibleUsers = getAccessibleUsers('ServiceContracts','List',true);
            $receiveid = $recordModel->entity->column_fields['Receiveid'];
            $response = new Vtiger_Response();
            if($accessibleUsers != '1=1' && !in_array($receiveid, $accessibleUsers) || $receiveid == $current_user->id && $accessibleUsers != '1=1'){
                $response->setError(-1, '需要原合同提单人上级才可更改提单人');
                $response->emit();
                exit;
            };
        }

        $db=PearDatabase::getInstance();
        $query="SELECT id,CONCAT(vtiger_users.last_name,'[',IFNULL(vtiger_departments.departmentname,'--'),']',IF(vtiger_users.`status`!='Active','[离职]','')) as username FROM  vtiger_users LEFT JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid
                            LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid WHERE vtiger_users.`status`='Active'";
        $result = $db->pquery($query, array());
        $arr=array();
        while($row= $db->fetchByAssoc($result)){$arr[]=$row;};
        $response = new Vtiger_Response();
        $response->setResult($arr);
        $response->emit();

    }
    //更改提单人
    public function changereceived(Vtiger_Request $request)
    {
        $recordId = $request->get('recordid');
        $userid = $request->get('userid');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'ServiceContracts');
        $entity = $recordModel->entity->column_fields;
        global $current_user;
        $arr = array();
        $response = new Vtiger_Response();
        $receiveid = $recordModel->entity->column_fields['Receiveid'];
        $accessibleUsers = getAccessibleUsers('ServiceContracts','List',true);
        if($accessibleUsers != '1=1' && !in_array($receiveid, $accessibleUsers) || $receiveid == $current_user->id && $accessibleUsers != '1=1'){
            $response->setError(-1, '需要原合同提单人上级才可更改提单人');
            $response->emit();
            exit;
        };

        if ($entity['modulestatus']=='c_complete' && is_numeric($userid) && $userid>0) {
            global $current_user;
            $user=Users_Privileges_Model::getInstanceById($entity['Receiveid']);
//            if($current_user->id==$user->reports_to_id || $current_user->is_admin=='on') {
            $db = PearDatabase::getInstance();
            $datetime=date("Y-m-d H:i:s");
            $id = $db->getUniqueId('vtiger_modtracker_basic');
            $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                array($id, $recordId, 'ServiceContracts', $current_user->id, $datetime, 0));
            $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, 'Receiveid', $entity['Receiveid'], $userid));
            $query = "UPDATE vtiger_servicecontracts SET receiveid=? WHERE servicecontractsid=?";
            $db->pquery($query, array($userid,$recordId));
//            }

        }

        $response->setResult($arr);
        $response->emit();

    }

    /**
     * 查询T云购买信息
     * @param Vtiger_Request $request
     */
    public function searchTyunBuyServiceInfo(Vtiger_Request $request){
        $recordModel = new ServiceContracts_Record_Model();
        $response = new Vtiger_Response();
        $response->setResult($recordModel->searchTyunBuyServiceInfo($request));
        $response->emit();
    }

    /**
     * 检查合同中的Tyun年限和产品是否与T云中一致
     * @param Vtiger_Request $request
     */
    public function checkProductAYear(Vtiger_Request $request){
        $modulestatus=$request->get('modulestatus');
        $parent_contracttypeid = $request->get('parent_contracttypeid');//前面类型
        $contract_type=$request->get('contract_type');

        $arr=0;
        if(($modulestatus ==1) && (in_array($parent_contracttypeid,array('2','12','13','14','15','17'))||$contract_type=='SaaS新零售系统')){
            $record = $request->get('record');
            $recordModel = new ServiceContracts_Record_Model();

            $isTyunSite = $recordModel->checkTyunCrmSiteProduct($request);
            $result = array('success'=>true);
            $hasorder = $request->get("hasorder");
            if($isTyunSite == false && $hasorder){
                //if($recordModel->IsTyunProduct($request->get('productid'))){
                $result = $recordModel->checkTyunProductActivationCode($request);
                if(!$result['success']){
                    $msg = $result['msg'];
                    //更新状态，后续做变更处理
                    if($result['ismodify']){
                        $recordModel->updateRejectionReason($result);
                    }
                }
                //$result=$recordModel->checkTyunProductAndyear($request);
                //}
            }
        }
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
    /**
     * 合同作废
     * @param Vtiger_Request $request
     *
     */
    public function ContractCancel(Vtiger_Request $request){
        $recordId = $request->get('recordid');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'ServiceContracts');
        $entity=$recordModel->entity->column_fields;
        //$isFinance=$recordModel->getModule()->exportGrouprt('ServiceContracts','concancel');
        $arr=true;
        if($entity['signaturetype']=='eleccontract'){
            return '';
        }
        if(($entity['modulestatus'] == 'c_complete'|| $entity['modulestatus'] == '已发放')/* && $isFinance*/){
            global $current_user;
            $userid = $current_user->id;
            $remark = $request->get('remark');
            $pagenumber = $request->get('pagenumber');
            $reasoncan = $request->get('reasoncan');
            $yjmoney=$reasoncan=='losevoid'?300:$pagenumber;
            $db=PearDatabase::getInstance();
            $result=$db->pquery("SELECT 1 FROM vtiger_servicecontracts WHERE parent_contracttypeid=2 AND servicecontractsid=?",array($recordId));
            $numRows=$db->num_rows($result);
            //$modulestatus=$numRows==0?'c_cancelings':'c_canceling';
//            $modulestatus='c_cancelings';
            $modulestatus='c_canceling';//5.38版本搜索去掉c_cancelings
            $query='UPDATE vtiger_servicecontracts SET cancelid=?,canceltime=?,cancelvoid=?,pagenumber=?,cancelmoney=?,cancelremark=?,modulestatus=?,backstatus=? WHERE servicecontractsid=?';
            $db->pquery($query,array($userid,date('Y-m-d H:i:s'),$reasoncan,$pagenumber,$yjmoney,$remark,$modulestatus,$entity['modulestatus'],$recordId));
            $moduleInstance=CRMEntity::getInstance('ServiceContracts');
            $_REQUEST['workflowsid']=$this->cancelworkflowsid;
            //$_REQUEST['workflowsid']=614638;//线上的ID
            $moduleInstance->makeWorkflows('ServiceContracts', $_REQUEST['workflowsid'], $recordId,false);
            //$db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=16 WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.workflowsid=? AND sequence=1 AND vtiger_salesorderworkflowstages.modulename='ServiceContracts'",array($recordId,$_REQUEST['workflowsid']));//更新作废审核人
            //DO_RETURN_TCLOUD

            if($numRows==0){
                $db->pquery("DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND vtiger_salesorderworkflowstages.workflowsid=? AND workflowstagesflag='DO_RETURN_TCLOUD' AND vtiger_salesorderworkflowstages.modulename='ServiceContracts'",array($recordId,$_REQUEST['workflowsid']));
                //$db->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=1 WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.workflowsid=? AND vtiger_salesorderworkflowstages.modulename='ServiceContracts' ORDER BY sequence ASC LIMIT 1",array($recordId,$_REQUEST['workflowsid']));
            }
        }
        $response = new Vtiger_Response();
        $response->setResult($arr);
        $response->emit();
    }
    /**
     * 出纳审核
     * @param Vtiger_Request $request
     *
     */
    public function chuNaDoContractCancel(Vtiger_Request $request){
        $recordId = $request->get('recordid');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'ServiceContracts');
        $entity=$recordModel->entity->column_fields;
        $isFinance=$recordModel->getModule()->exportGrouprt('ServiceContracts','concancel');
        $arr=true;
        if(($entity['modulestatus'] == 'c_cancelings') && $isFinance){
            global $current_user;
            $userid = $current_user->id;
            $remark = $request->get('remark');
            $pagenumber = $request->get('pagenumber');
            $souje = $request->get('souje');
            $soujbanhao = $request->get('soujbanhao');
            $reasoncan = $request->get('reasoncan');
            $yjmoney=$reasoncan=='losevoid'?300:$pagenumber;
            $db=PearDatabase::getInstance();
            $query='UPDATE vtiger_servicecontracts SET cancelfeeid=?,docanceltime=?,cancelvoid=?,pagenumber=?,cancelmoney=?,cancelremark=?,modulestatus=?,accountsdue=?,receiptnumber=? WHERE servicecontractsid=?';
            $db->pquery($query,array($userid,date('Y-m-d H:i:s'),$reasoncan,$pagenumber,$yjmoney,$remark,'c_canceling',$souje,$soujbanhao,$recordId));
            $db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=? WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.workflowsid=? AND workflowstagesflag='DO_RETURN_CANCEL' AND vtiger_salesorderworkflowstages.modulename='ServiceContracts'",array($userid,$recordId,$this->cancelworkflowsid));//更新作废审核人
        }
        $response = new Vtiger_Response();
        $response->setResult($arr);
        $response->emit();
    }

    /**
     * 合同生成工作流
     * @param Vtiger_Request $request
     */
    public function makeWorkflowStages(Vtiger_Request $request)
    {
        $recordId=$request->get('recordid');
        $detailModel=Vtiger_DetailView_Model::getInstance('ServiceContracts',$recordId);
        global $current_user;
        $recordModel=$detailModel->getRecord();
        if($recordModel->entity->column_fields['modulestatus']=='a_normal'
            && $recordModel->entity->column_fields['assigned_user_id']== $current_user->id
        )
        {
            if($detailModel->getFileStyle($recordId))
            {
                $_REQUEST['workflowsid']=$this->nostandworkflowsid;
                $focus = CRMEntity::getInstance('ServiceContracts');
                $focus->makeWorkflows('ServiceContracts', $_REQUEST['workflowsid'], $recordId,'edit');
                //$focus->db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=(SELECT vtiger_auditsettings.oneaudituid FROM`vtiger_auditsettings` INNER JOIN (SELECT vtiger_departments.parentdepartment,vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.departmentid='{$current_user->departmentid}') AS tempdepart ON FIND_IN_SET(vtiger_auditsettings.department,REPLACE(tempdepart.parentdepartment,'::',',')) LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_auditsettings.department  WHERE vtiger_auditsettings.auditsettingtype='ContractsAuditset' ORDER BY abs(LENGTH(IFNULL(tempdepart.parentdepartment,0))-LENGTH(IFNULL(vtiger_departments.parentdepartment,0))) LIMIT 1) WHERE vtiger_salesorderworkflowstages.sequence=1 AND vtiger_salesorderworkflowstages.salesorderid=? AND isaction=1 AND vtiger_salesorderworkflowstages.modulename='ServiceContracts'",array($recordId));
                $focus->db->pquery("UPDATE vtiger_servicecontracts SET modulestatus='b_actioning',isstandard=1,creatorid=? WHERE servicecontractsid=?",array($current_user->id,$recordId));
                //$focus->db->pquery("UPDATE vtiger_servicecontracts_print SET nostand=1,smownerid=? WHERE servicecontracts_no=?",array($current_user->id,$recordModel->entity->column_fields['contract_no']));

                //$departmentid=empty($current_user->departmentid)?'H1':$current_user->departmentid;
                $departmentid=$_SESSION['userdepartmentid'];
                /*$result=$focus->db->pquery("SELECT vtiger_auditsettings.oneaudituid FROM `vtiger_auditsettings` INNER JOIN (SELECT vtiger_departments.parentdepartment,vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.departmentid=?) AS tempdepart ON FIND_IN_SET(vtiger_auditsettings.department,REPLACE(tempdepart.parentdepartment,'::',',')) LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_auditsettings.department  WHERE vtiger_auditsettings.auditsettingtype='ContractsAuditset' ORDER BY abs(LENGTH(IFNULL(tempdepart.parentdepartment,0))-LENGTH(IFNULL(vtiger_departments.parentdepartment,0))) LIMIT 1",array($departmentid));
                $data=$focus->db->query_result_rowdata($result,0);*/
                $data=$focus->setAudituid('ContractsAuditset',$departmentid,$recordId,$_REQUEST['workflowsid']);
                //$focus->db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=? WHERE vtiger_salesorderworkflowstages.sequence=1 AND vtiger_salesorderworkflowstages.salesorderid=? AND isaction=1 AND vtiger_salesorderworkflowstages.modulename='ServiceContracts'",array($data['oneaudituid'],$recordId));
                /*$query='SELECT vtiger_invoicecompanyuser.userid FROM `vtiger_invoicecompanyuser` WHERE invoicecompany=?';
                $datacode=$focus->db->pquery($query,array($recordModel->entity->column_fields['companycode']));
                if($focus->db->num_rows($datacode)>0){
                    $row = $focus->db->query_result_rowdata($datacode, 0);
                    $focus->db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=? WHERE vtiger_salesorderworkflowstages.workflowstagesflag IN('DO_PRINT','CLOSE_WORKSTREAM') AND vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='ServiceContracts'",array($row['userid'],$recordId));
                }*/

                if(in_array($recordModel->get('companycode'),$this->Kllcompanycode)){
                    $query='SELECT vtiger_departments.parentdepartment FROM vtiger_departments WHERE departmentid=? limit 1';
                    $result=$focus->db->pquery($query,array($departmentid));
                    $data=$focus->db->raw_query_result_rowdata($result,0);
                    $parentdepartment=$data['parentdepartment'];
                    $parentdepartment.='::';
                    //$needle='H283::';
                    if(strpos($parentdepartment,$this->Kllneedle)!==false || strpos($parentdepartment,$this->WXKLLneedle)!==false){
                        $data=$focus->getAudituid('ContractsAuditset',$departmentid);
                        $userid1=$data['audituid4']>0?$data['audituid4']:$data['oneaudituid'];
                        $userid2=$data['audituid4']>0?$data['oneaudituid']:$data['towaudituid'];
                        $userid3=$data['audituid4']>0?$data['towaudituid']:$data['audituid3'];
                        $updateSql="UPDATE vtiger_salesorderworkflowstages SET ishigher = 1,higherid =? WHERE salesorderid =? AND workflowstagesflag='AUDIT_VERIFICATION' AND workflowsid =?";
                        $focus->db->pquery($updateSql,array($userid1,$recordId,$_REQUEST['workflowsid']));
                        $updateSql="UPDATE vtiger_salesorderworkflowstages SET ishigher = 1,higherid =? WHERE salesorderid =? AND workflowstagesflag='TWO_VERIFICATION' AND workflowsid =?";
                        $focus->db->pquery($updateSql,array($userid2,$recordId,$_REQUEST['workflowsid']));
                        $updateSql="UPDATE vtiger_salesorderworkflowstages SET ishigher = 1,higherid =? WHERE salesorderid =? AND workflowstagesflag='THREE_VERIFICATION' AND workflowsid =?";
                        $focus->db->pquery($updateSql,array($userid3,$recordId,$_REQUEST['workflowsid']));
                        $updateSql="UPDATE vtiger_salesorderworkflowstages SET workflowstagesname='审核生成合同编号',ishigher = 1,higherid =? WHERE salesorderid =? AND workflowstagesflag='CREATE_CODE' AND workflowsid =?";
                        $focus->db->pquery($updateSql,array(792,$recordId,$_REQUEST['workflowsid']));
                    }
                    //2021.06.03 把初级财务主管删掉
//                  $deleteSql="DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowstagesflag IN('TREASURER_ONE','TREASURER_TWO')";
                    $deleteSql="DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowstagesflag IN('TREASURER_ONE')";
                    $focus->db->pquery($deleteSql,array($recordId));//删除财务主管的节点
                }elseif(in_array($recordModel->get('companycode'),$this->TREASURER_TWO)){
                    $deleteSql="DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowstagesflag IN('TREASURER_ONE')";
                    $focus->db->pquery($deleteSql,array($recordId));//删除财务主管的节点
                }else{
//                    $deleteSql="DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowstagesflag IN('TREASURER_TWO')";
//                    $focus->db->pquery($deleteSql,array($recordId));//删除财务主管的节点
                }
            }
        }
        //新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$recordId,'salesorderworkflowstagesid'=>0));

    }
    /*public function getSalesOrderStatus(Vtiger_Request $request)
    {
        $urlGetactiveStatus="http://tyunapi.71360.com/api/cms/UserServerState";
        $recordid=$request->get('recordid');
        $usercode=$request->get('usercode');
        do{
            $usercode=trim($usercode);
            if(empty($usercode)){
                $result=array('success'=>false,'message'=>'用户名不能为空!');
                break;
            }
            $detailModel=Vtiger_DetailView_Model::getInstance('ServiceContracts',$recordid);
            $moduleModel=$detailModel->getModule();
            $recordModel=$detailModel->getRecord();
            $statusArray=array('已发放','c_complete');
            $column_fields=$recordModel->entity->column_fields;
            $result=array('success'=>false,'message'=>'合同状态为签收的才能查看!');
            //if(in_array($column_fields['modulestatus'],$statusArray))
            {
                $LoginName=$usercode;
                //$LoginName='tsl301';
                $myData=['LoginName'=>$LoginName];
                $tempData['data'] = $moduleModel->encrypt(json_encode($myData));
                $postData = http_build_query($tempData);//传参数
                $res = $moduleModel->https_request($urlGetactiveStatus, $postData);
                $result = json_decode($res, true);
                $result = json_decode($result, true);
            }

        }while(0);

        echo json_encode($result);
    }*/
    public function getSalesOrderStatus(Vtiger_Request $request)
    {
        global $url_getactive_status;
        $urlGetactiveStatus=$url_getactive_status;
//        $urlGetactiveStatus="http://tyunapi.71360.com/api/cms/UserServerState";
        $recordid=$request->get('recordid');
        do{
            $detailModel=Vtiger_DetailView_Model::getInstance('ServiceContracts',$recordid);
            $moduleModel=$detailModel->getModule();
            $recordModel=$detailModel->getRecord();
            $statusArray=array('已发放','c_complete');
            $column_fields=$recordModel->entity->column_fields;
            $result=array('success'=>false,'message'=>'合同状态为签收的才能查看!');
            $db=PearDatabase::getInstance();
            $query='SELECT usercode FROM vtiger_activationcode WHERE contractid=? limit 1';
            $usercoderesult=$db->pquery($query,array($recordid));
            $userdataresult=$db->query_result_rowdata($usercoderesult,0);
            //if(in_array($column_fields['modulestatus'],$statusArray) && !empty($userdataresult['usercode']))
            {
                $LoginName=$userdataresult['usercode'];
                //$LoginName='tsl301';
                $myData=array('LoginName'=>$LoginName);
                $tempData['data'] = $moduleModel->encrypt(json_encode($myData));
                $postData = http_build_query($tempData);//传参数
                $res = $moduleModel->https_request($urlGetactiveStatus, $postData);
                $result = json_decode($res, true);
                $result = json_decode($result, true);
            }

        }while(0);

        echo json_encode($result);
    }

    /**
     * 指定代领人
     * @param Vtiger_Request $request
     */
    public function assignreceiptor(Vtiger_Request $request)
    {
        $recordId = $request->get('recordid');
        $userid = $request->get('userid');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'ServiceContracts');
        $entity=$recordModel->entity->column_fields;
        $arr = array();
        global $current_user;
        if (($entity['modulestatus']=='a_normal' && $current_user->id==$entity['assigned_user_id']) || ($entity['modulestatus']=='c_stamp' && ($recordModel->checkCreator($recordId) || $current_user->is_admin=='on'))) {
            $db = PearDatabase::getInstance();
            $datetime=date("Y-m-d H:i:s");
            $id = $db->getUniqueId('vtiger_modtracker_basic');
            $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                array($id, $recordId, 'ServiceContracts', $current_user->id, $datetime, 0));
            $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, 'receiptorid', $entity['receiptorid'], $userid));
            $isstandard=$entity['modulestatus']=='a_normal'?"isstandard=1,":'';
            $query = "UPDATE vtiger_servicecontracts SET {$isstandard}receiptorid=? WHERE servicecontractsid=?";
            $db->pquery($query, array($userid,$recordId));

        }

        $response = new Vtiger_Response();
        $response->setResult($arr);
        $response->emit();
    }
    /**
     * @param Vtiger_Request $reques
     *
     */
    public function getAccountStatus(Vtiger_Request $request)
    {
        $moduleModel=Vtiger_Module_Model::getInstance('ServiceContracts');
        $value=$moduleModel->checkAccount($request);
        $response = new Vtiger_Response();
        $response->setResult($value);
        $response->emit();
    }

    /**
     * 取得当前的合同的补充协议
     * @param Vtiger_Request $request
     */
    public function getContractsAgreement(Vtiger_Request $request)
    {
        $recordid=$request->get('recordid');
        $db=PearDatabase::getInstance();
        $query='SELECT vtiger_contractsagreement.newservicecontractsno FROM vtiger_contractsagreement LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_contractsagreement.contractsagreementid
                WHERE vtiger_crmentity.deleted=0
                AND vtiger_contractsagreement.servicecontractsid=?';
        $result=$db->pquery($query,array($recordid));
        $array=array();
        while($row=$db->fetch_array($result))
        {
            $array[]=array('contractsno'=>$row['newservicecontractsno']);
        }
        $num=count($array);
        echo json_encode(array('num'=>$num,'result'=>$array));
    }

    /**
     * 激活码作废
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function ToVoidActivationCode(Vtiger_Request $request){
        $recordId = $request->get('recordid');
        global $current_user,$adb;
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'ServiceContracts');
        $entity=$recordModel->entity->column_fields;
        $resultaa['success'] = false;
        $resultaa['message'] = ":只有已发放、已收回、已完成状态下的合同才能作废!";
        if(in_array($entity['modulestatus'],array('已发放','c_recovered','c_complete'))){
            $resultaa['message'] = ":合同领取人才能操作!";
            if($entity['assigned_user_id']== $current_user->id || $current_user->is_admin=='on'){
                $resultaa['success'] = true;
                $resultaa['message'] = ":合同激活码作废成功!";
                $thisdata=$recordModel->getModule()->cancelActivationCode($entity['contract_no']);
//                $thisdata=$recordModel->getModule()->doCancelNew($entity['contract_no']);
//                $thisdata=$recordModel->getModule()->doCancel($entity['contract_no']);
                if($thisdata['success']==false){
                    $resultaa['success'] = false;
                    $resultaa['message'] = $thisdata['message'];
                }else{
                    if($thisdata['success'] &&  $thisdata['code']){
                        $resultaa['success'] = true;
                        $resultaa['message'] = $thisdata['message'];
                    }else{
                        $adb->pquery("UPDATE vtiger_activationcode SET `status`=2 WHERE contractname=?",array($entity['contract_no']));

                        if($entity['modulestatus']=='c_complete'){
                            $adb->pquery("update vtiger_contracts_execution_detail set iscancel=1 where contractid=?",array($recordId));
                            $adb->pquery("update vtiger_contract_receivable set iscancel=1 where contractid=?",array($recordId));
                            $adb->pquery("update vtiger_receivable_overdue set iscancel=1 where contractid=?",array($recordId));
                        }else{
                            //订单取消 删除应对合同收表中数据
                            $adb->pquery("delete from vtiger_contract_receivable where contractid=?",array($recordId));
                        }
                    }

                }
            }
        }
        $response = new Vtiger_Response();
        $response->setResult($resultaa);
        $response->emit();

    }
    /**
     * 合同生成打回修改状态工作流
     * @param Vtiger_Request $request
     */
    public function toBackStatus(Vtiger_Request $request)
    {
        $recordId=$request->get('recordid');
        $detailModel=Vtiger_DetailView_Model::getInstance('ServiceContracts',$recordId);
        global $current_user;
        $recordModel=$detailModel->getRecord();
        $moduleModel=$detailModel->getModule();
        $modulestatus=$recordModel->get('modulestatus');
        /*echo "<pre>";
        print_r($recordModel->entity->column_fields);
        exit;*/
        $returnData=array('flag'=>false,'msg'=>'');
        do{

            if(!in_array($modulestatus,array('c_recovered'))){
                $returnData['msg']='该合同状态非“已收回”，不能发起流程!';
                break;
            }
            if($recordModel->get('signaturetype')=='eleccontract'){
                $returnData['msg']='电子合同不允许该操作!';
                break;
            }
            if(!$moduleModel->exportGrouprt('ServiceContracts','Received')){
                $returnData['msg']='没有相关操作权限!';
                break;
            }
            //“签收不成功”操作不需要判断 合同的工单、充值申请单有没有作废之类的
            // if(!$detailModel->getContractVoid($recordId)){
            //     $returnData['msg']='合同已使用,不能进行该操作!';
            //     break;
            // }
            /*if(in_array($modulestatus,array('c_recovered','c_complete'))
                && $moduleModel->exportGrouprt('ServiceContracts','Received')
                && $detailModel->getContractVoid($recordId)
                && $recordModel->get('signaturetype')!='eleccontract'
            )
            {*/
            $_REQUEST['workflowsid']=$this->tobackstatus;
            $focus = CRMEntity::getInstance('ServiceContracts');
            $sql='DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowsid=?';
            $focus->db->pquery($sql,array($recordId,$this->tobackstatus));
            $sql='UPDATE vtiger_salesorderworkflowstages SET isaction=if(isaction=1,0,isaction) WHERE salesorderid=?';
            $focus->db->pquery($sql,array($recordId));
            $focus->makeWorkflows('ServiceContracts', $_REQUEST['workflowsid'], $recordId,'edit');
            $higherid=$recordModel->get('assigned_user_id');
            if($modulestatus=='c_complete'){
                $higherid=$recordModel->get('Receiveid');
            }
            $focus->db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=? WHERE  vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.workflowsid=? AND workflowstagesflag='BACKCHANGESTATUS' AND vtiger_salesorderworkflowstages.modulename='ServiceContracts'",array($higherid,$recordId,$this->tobackstatus));
            $focus->db->pquery("UPDATE vtiger_servicecontracts SET modulestatus='b_actioning',backstatus='{$modulestatus}' WHERE servicecontractsid=?",array($recordId));
            $id = $focus->db->getUniqueId('vtiger_modtracker_basic');
            $currentTime=date('Y-m-d H:i:s');
            $focus->db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                array($id, $recordId, 'ServiceContracts', $current_user->id, $currentTime, 0));
            $focus->db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, 'modulestatus', $modulestatus, '状态打回执行中'));
            $returnData=array('flag'=>true,'msg'=>'');
            //}
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($returnData);
        $response->emit();
    }

    /**
     * 获取TWEb线上产品
     * @param $request
     * @throws Exception
     */
    public function getTyunProducts($request){
        global $adb;
        $record=$request->get('record');
//        if($record>0){
            $reocrdModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
            $ProductsOnline=$reocrdModel->getTyunProductsOnline($record, $request->get('contract_typeName'),$request->get("servicecontractstype"),$request->get("contract_classification"),$request->get("agents"),$request->get("category"));
            echo json_encode($ProductsOnline);
            return ;
//        }
//        echo json_encode(array('product_list'=>array()));
    }

    /**
     * 获取额外产品
     * @return string
     */
    public function getExtraproduct(){
        $ServiceContractsRecordModel = Vtiger_Record_Model::getCleanInstance('ServiceContracts');
        $arr_extraproduct=$ServiceContractsRecordModel->getextraproduct(1);
        $arr_extraproduct1 = array();
        $arr_extraproduct2 = array();
        $arr_extraproduct3 = array();
        for ($i=0; $i<count($arr_extraproduct); $i++) {
            if($arr_extraproduct[$i]["groupflag"] == '1'){
                $arr_extraproduct1[] = $arr_extraproduct[$i];
            }
            if($arr_extraproduct[$i]["groupflag"] == '2'){
                $arr_extraproduct2[] = $arr_extraproduct[$i];
            }
            if($arr_extraproduct[$i]["groupflag"] == '3'){
                $arr_extraproduct3[] = $arr_extraproduct[$i];
            }
        }
        $str='<table class="table table-bordered">
                                <thead>
                                    <tr><td>';
        foreach($arr_extraproduct1 as $extraValue){
            $str.='<div style="line-height: 30px;float: left;width: 260px; border: 1px solid  rgba(57, 15, 40, 0.18); margin: 2px;  border-radius: 5px;padding-bottom:5px;">
                        <label class="checkbox inline">
                            <input type="checkbox"
                                   value="'.$extraValue['productid'].'" name="extraproductid[]" data-name="extraproductid" data-istyun="'.$extraValue['istyun'].'" class="extraproductid entryCheckBox" >
                            &nbsp;'.$extraValue['productname'].'
                            <input type="hidden" name="eproducttypename['.$extraValue['productid'].']" value="'.$extraValue['productname'].'"/>
                        </label>
                    </div>';
        }
        $str.='</td></tr> <tr><td>';
        foreach($arr_extraproduct2  as $extraValue){
            $str.='<div style="line-height: 30px;float: left;width: 260px; border: 1px solid  rgba(57, 15, 40, 0.18); margin: 2px;  border-radius: 5px;padding-bottom:5px;">
                        <label class="checkbox inline">
                            <input type="checkbox"
                                   value="'.$extraValue['productid'].'" name="extraproductid[]" data-istyun="'.$extraValue['istyun'].'" data-name="extraproductid" class="extraproductid entryCheckBox" >
                            &nbsp;'.$extraValue['productname'].'
                            <input type="hidden" name="eproducttypename['.$extraValue['productid'].']" value="'.$extraValue['productname'].'"/>
                        </label>
                    </div>';
        }
        $str.='</td></tr> <tr><td>';
        foreach ($arr_extraproduct3 as $extraValue){
            $str.='<div style="line-height: 30px;float: left;width: 260px; border: 1px solid  rgba(57, 15, 40, 0.18); margin: 2px;  border-radius: 5px;padding-bottom:5px;">
                        <label class="checkbox inline">
                            <input type="checkbox"
                                   value="'.$extraValue['productid'].'" name="extraproductid[]" data-name="extraproductid" class="extraproductid entryCheckBox" >
                            &nbsp;'.$extraValue['productname'].'
                            <input type="hidden" name="eproducttypename['.$extraValue['productid'].']" value="'.$extraValue['productname'].'"/>
                        </label>
                    </div>';
        }
        $str.='</td></tr>
                </thead>
                <tbody>
                </tbody>
            </table>';
        return $str;
    }

    /**
     * 检测是否可以发起作废申请
     */
    public function checkcancel(Vtiger_Request $request){
        $recordId = $request->get('recordid');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'ServiceContracts');
        $entity=$recordModel->entity->column_fields;
        $arr = array();
        global $current_user;
        $response = new Vtiger_Response();
        if(!in_array($entity['modulestatus'],array('c_complete','已发放'))){
            $response->setError(-1, '合同不是已完成或已发放状态');
            $response->emit();
            exit;
        }

        $superior_ids = getAllSuperiorIds($entity['assigned_user_id']);
        $user_ids = array_merge(array($entity['assigned_user_id'],$entity['Receiveid'],$entity['Signid']),$superior_ids);
        if(!in_array($current_user->id,$user_ids)){
            $response->setError(-1, '您不是当前合同的提单人/领取人/领取人上级，不能发起作废申请');
            $response->emit();
            exit;
        }
        $detailModel=Vtiger_DetailView_Model::getInstance('ServiceContracts',$recordId);
        if(!$detailModel->getContractVoid($recordId) || !$detailModel->getContractVoidToActivationcode($recordId)){
            $response->setError(-1,'该合同暂不可发起作废 <br/>可能原因：该合同可能存在未作废的发票/充值申请单/回款/工单/订单');
            $response->emit();
            exit;
        }

        $response = new Vtiger_Response();
        $response->setResult($arr);
        $response->emit();
    }
    /**
     * 确认产品交付
     * @param Vtiger_Request $request
     */
    public function confirmDelivery(Vtiger_Request $request){
        global $current_user,$adb;
        $record=$request->get('recordid');
        $recordModel=Vtiger_Record_Model::getInstanceById($record,'ServiceContracts');
        $returnData=array('flag'=>false);
        do{
            if($recordModel->get('modulestatus')!='c_complete'){
                $returnData['msg']='只有签收的合同才能操作';
                break;
            }
            $accountid=$recordModel->get('sc_related_to');
            if($accountid<=0){
                $returnData['msg']='请先完善合同上的客户信息！';
                break;
            }
            if(!$recordModel->personalAuthority('ServiceContracts','isfulldelivery')){
                $returnData['msg']='没有权限操作！请联系客服部相关人员操作';
                break;
            }
            if(0==$recordModel->get('isfulldelivery')){
                $sql='UPDATE vtiger_servicecontracts SET isfulldelivery=1,fulldeliverytime=?,fulldeliveryid=? WHERE servicecontractsid=?';
                $adb->pquery($sql,array(date('Y-m-d H:i:s'),$current_user->id,$record));
                $recordModel->setModTracker('ServiceContracts',$record,array('remark'=>array('currentValue'=>'已确认','oldValue'=>'确认产品完全交付')));
                $AchievementSummaryRecordModel=Vtiger_Record_Model::getCleanInstance('AchievementSummary');
                $AchievementSummaryRecordModel->customerServiceConfirmDelivery($record);
                $returnData=array('flag'=>true,'msg'=>'操作成功');
            }else{
                $query="SELECT * FROM vtiger_achievementallot_statistic WHERE servicecontractid=?";
                $result = $adb->pquery($query,array($record));
                $current_date=date('Y-m');
                if($adb->num_rows($result)){
                    $query="SELECT 1 FROM vtiger_withholdroyalty 
                            left join vtiger_achievementallot_statistic on vtiger_withholdroyalty.achievementallotid=vtiger_achievementallot_statistic.achievementallotid 
                            where vtiger_achievementallot_statistic.servicecontractid=? AND vtiger_withholdroyalty.amountofmoney>0 and left(confirmationdate,7)<?";
                    $result1=$adb->pquery($query,array($current_date));
                    if($adb->num_rows($result1)){
                        $returnData=array('flag'=>false,'msg'=>'暂扣已发放或跨月不允许撤销！');
                        break;
                    }
                    $array=array();
                    while($row=$adb->fetch_array($result)){
                        $array[]=$row['achievementallotid'];
                    }
                    $sql='delete from vtiger_withholdroyalty where vtiger_withholdroyalty.achievementallotid in('.implode(',',$array).')';
                    $adb->pquery($sql,array());
                }
                $sql='UPDATE vtiger_servicecontracts SET isfulldelivery=0,fulldeliverytime=NULL,fulldeliveryid=NULL,firstfulldelivery=1 WHERE servicecontractsid=?';
                $adb->pquery($sql,array($record));
                $recordModel->setModTracker('ServiceContracts',$record,array('remark'=>array('currentValue'=>'撤销确认','oldValue'=>'已确认产品完全交付')));
                $returnData=array('flag'=>true,'msg'=>'操作成功');
            }
        }while(0);
        $response=new Vtiger_Response();
        $response->setResult($returnData);
        $response->emit();
    }
    /**
     * 确认到款
     */
    public function confirmPayment(Vtiger_Request $request){
        $recordId = $request->get('recordid');
        $mobile = $request->get('mobile');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'ServiceContracts');
        $entity=$recordModel->entity->column_fields;
        $arr = array();
        global $current_user;
        $response = new Vtiger_Response();
        if(!in_array($entity['modulestatus'],array('c_complete','已发放'))){
            $response->setError(-1, '合同不是已完成或已发放状态');
            $response->emit();
            exit;
        }

        if($entity['ispay']){
            $response->setError(-1, '不可重复确认付款');
            $response->emit();
            exit;
        }

        global $adb;
        $sql = "select * from vtiger_servicecontracts where servicecontractsid=?";
        $result = $adb->pquery($sql,array($recordId));
        if(!$adb->num_rows($result)){
            $response->setError(-1, '没找到合同');
            $response->emit();
            exit;
        }

        $res = $recordModel->confirmPayment($current_user->id,$recordId,$mobile);
        if(!$res['success']){
            $response->setError(-1,$res['msg']);
            $response->emit();
            exit;
        }
        $response = new Vtiger_Response();
        $response->setResult($arr);
        $response->emit();
    }

    public function userMobile(Vtiger_Request $request){
        $recordId = $request->get('recordid');
        global $adb;
        $sql = "select mobile from vtiger_activationcode where contractid = ? order by createdtime desc limit 1";
        $result = $adb->pquery($sql,array($recordId));
        $response = new Vtiger_Response();

        if($adb->num_rows($result)){
            $rowData = $adb->query_result_rowdata($result,0);
            $mobile = $rowData['mobile'];
            $response->setResult(array('mobile'=>$mobile));
            $response->emit();
            exit();
        }
        $response->setError(-1, '缺少客户手机号');
        $response->emit();
    }
    //检查合同编号 cxh 新增
    public function checkContractno(Vtiger_Request $request){
        global $adb;
        $contract_no=trim($request->get("contractno"));
        $codenumbertemp = preg_replace('/-8$/', '', $contract_no);
        $codenumbertemp = is_numeric($codenumbertemp) ? $codenumbertemp : $contract_no;
        if(is_numeric($codenumbertemp)){
            $sql=" SELECT vtiger_servicecontracts.servicecontractsid,modulestatus,vtiger_servicecontracts.contract_no FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0 AND vtiger_servicecontracts.servicecontractsprintid=? AND modulestatus='已发放' ";
        }else{
            $sql="SELECT vtiger_servicecontracts.servicecontractsid,modulestatus,vtiger_servicecontracts.contract_no FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0 AND vtiger_servicecontracts.contract_no=? AND modulestatus='已发放' ";
        }
        $result=$adb->pquery($sql,array($codenumbertemp));
        if($adb->num_rows($result)>0){
            $result=$adb->query_result_rowdata($result,0);
            $data=array("success"=>true,"data"=>$result);
        }else{
            $data=array("success"=>false,"message"=>'没查到该已发放合同！');
        }
        $response=new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

        public function agentList(Vtiger_Request $request){
        $recordId = $request->get("recordid");
        $module = $request->getModule();
        $recordModel = ServiceContractsPrint_Record_Model::getCleanInstance($module);
        $agentList = $recordModel->getAgentList();
        $agent = json_decode($agentList,true);
        $response = new Vtiger_Response();
        if(!$agent['success']){
            $response->setError(-1, '获取代理商列表失败');
            $response->emit();
            exit();
        }
        $response->setResult($agent['data']);
        $response->emit();
        exit();
    }

    public function getTyunWebCategory(Vtiger_Request $request){
        $module = $request->getModule();
        $recordModel = ServiceContractsPrint_Record_Model::getCleanInstance($module);
        $data = $recordModel->getTyunWebCategory();
        echo json_encode($data);
    }



    /**
     * 确认到款
     */
    public function manualConfirmPayment(Vtiger_Request $request){
        $recordId = $request->get('recordid');
        $mobile = $request->get('mobile');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'ServiceContracts');
        $entity=$recordModel->entity->column_fields;
        $arr = array();
        global $current_user;
        $response = new Vtiger_Response();
        if(!in_array($entity['modulestatus'],array('c_complete','已发放'))){
            $response->setError(-1, '合同不是已完成或已发放状态');
            $response->emit();
            exit;
        }

        if($entity['ispay']){
            $response->setError(-1, '不可重复确认付款');
            $response->emit();
            exit;
        }

        global $adb;
        $sql = "select * from vtiger_servicecontracts where servicecontractsid=?";
        $result = $adb->pquery($sql,array($recordId));
        if(!$adb->num_rows($result)){
            $response->setError(-1, '没找到合同');
            $response->emit();
            exit;
        }

        $res = $recordModel->confirmPayment($current_user->id,$recordId,$mobile);
        if(!$res['success']){
            $response->setError(-1,$res['msg']);
            $response->emit();
            exit;
        }
        $response = new Vtiger_Response();
        $response->setResult($arr);
        $response->emit();
    }

    public function leastPayMoney(Vtiger_Request $request){
        $recordId = $request->get("recordid");
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'ServiceContracts');
        $entity=$recordModel->entity->column_fields;
        if(!$entity['isstage']){
            echo json_encode(array('success'=>false,'msg'=>'不是分期合同'));
            exit();
        }
        $data = $recordModel->leastPayMoney($recordId);
        echo json_encode($data);
    }

    /**
     * 全款分期切换
     *
     * @param Vtiger_Request $request
     */
    public function changeStage(Vtiger_Request $request){
        $recordId = $request->get("recordid");
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'ServiceContracts');
        $entity = $recordModel->entity->column_fields;
        $total = $entity['total'];
        $stage = $request->get('stage');
        $data = $recordModel->changeStage($recordId,$stage,$total);
        echo json_encode($data);
    }

    /**
     * 核对合同
     */
    public function collateContract(Vtiger_Request $request)
    {
        $contractid = $request->get('contractid');
        $checkresult = $request->get('checkresult');
        $remark = $request->get('remark');
        global $current_user,$adb;
        $query = 'SELECT collate_num, first_collate_status FROM vtiger_servicecontracts WHERE servicecontractsid=?';
        $result = $adb->pquery($query, [$contractid]);
        $num = $adb->num_rows($result);
        if ($num >0) {
            $now = date('Y-m-d H:i:s');
            $contract = $adb->query_result_rowdata($result);
            //判断是否是首次核对
            if ( $contract['collate_num']>=1) {
                $query = 'UPDATE vtiger_servicecontracts SET collate_num=?, last_collate_status=?, last_collate_time=?, last_collate_operator=?, last_collate_remark=? WHERE servicecontractsid=?';
                $adb->pquery($query, [$contract['collate_num']+1, $checkresult, $now, $current_user->id, $remark, $contractid]);
            } else {
                $query = 'UPDATE vtiger_servicecontracts SET collate_num=?, first_collate_status=?, first_collate_time=?, first_collate_operator=?, first_collate_remark=? WHERE servicecontractsid=?';
                $adb->pquery($query, [1, $checkresult, $now, $current_user->id, $remark, $contractid]);
            }
            //插入核对日志
            $query = 'INSERT INTO vtiger_servicecontact_collate_log(contractid, status, collate_time, remark, collator) VALUES (?, ?, ?, ?, ?)';
            $adb->pquery($query, [$contractid, $checkresult, $now, $remark, $current_user->id]);
            $data = ['status' => 'success', 'msg' => '成功核对'];
        } else {
            $data = ['status' => 'error', 'msg' => '合同不存在'];
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 核对记录
     * @param Vtiger_Request $request
     */
    public function collateLog(Vtiger_Request $request)
    {
        global $adb;
        $contractid = $request->get('contractid');
        $query = "SELECT id, IF(status='fit', '符合', '不符合') AS status, remark, (SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname FROM vtiger_departments WHERE departmentid = (SELECT departmentid FROM vtiger_user2department WHERE userid=vtiger_users.id LIMIT 1)),''),']',(IF(`status`='Active' AND isdimission=0,'','[离职]'))) AS last_name FROM vtiger_users WHERE vtiger_servicecontact_collate_log.collator=vtiger_users.id) AS collator, collate_time FROM vtiger_servicecontact_collate_log WHERE contractid = ? ORDER BY id DESC";
        $result = $adb->pquery($query, [$contractid]);
        $num = $adb->num_rows($result);
        $list = [];
        if ($num > 0) {
            while ($row = $adb->fetchByAssoc($result)) {
                $list[]= $row;
            }
        }
        $response = new Vtiger_Response();
        $response->setResult($list);
        $response->emit();
    }

    /**
     * 批量核对
     */
    public function batchCollateContract(Vtiger_Request $request)
    {
        global $current_user, $adb, $currentView;
        $currentView = 'List';
        $checkresult = $request->get('checkresult');
        $remark = $request->get('remark');
        $listViewModel = Vtiger_ListView_Model::getInstance('ServiceContracts');
        $listViewModel->getSearchWhere();
        $queryGenerator =$listViewModel->get('query_generator');
        //用户条件
        $where = $listViewModel->getUserWhere();
        $queryGenerator->addUserWhere($where);
        $listQuery = $queryGenerator->getQueryCount();
        //$listQuery = $listViewModel->replaceSQL($listQuery);
        $listQuery = str_replace('count(1) as counts',
            'servicecontractsid, collate_num',
            $listQuery);
        $result = $adb->pquery($listQuery, []);
        $num = $adb->num_rows($result);
        if ($num <= 0) {
            $data = ['status'=>'error', 'msg'=>'未查到需核对的数据'];
        } elseif($num>1000) {
            $data = ['status'=>'error', 'msg'=>sprintf('当前共%d条数据,超过单次允许核对的最大记录数(1000)', $num)];
        } else {
            $now = date('Y-m-d H:i:s');
            while ($row = $adb->fetchByAssoc($result)) {
                //判断之前是否核对过
                if ($row['collate_num'] >= 1) {
                    $query = 'UPDATE vtiger_servicecontracts SET collate_num=?, last_collate_status=?, last_collate_time=?, last_collate_operator=?, last_collate_remark=? WHERE servicecontractsid=?';
                    $adb->pquery($query, [$row['collate_num']+1, $checkresult, $now, $current_user->id, $remark, $row['servicecontractsid']]);
                } else {
                    $query = 'UPDATE vtiger_servicecontracts SET collate_num=?, first_collate_status=?, first_collate_time=?, first_collate_operator=?, first_collate_remark=? WHERE servicecontractsid=?';
                    $adb->pquery($query, [1, $checkresult, $now, $current_user->id, $remark,  $row['servicecontractsid']]);
                }
                //插入核对日志
                $query = 'INSERT INTO vtiger_servicecontact_collate_log(contractid, status, collate_time, remark, collator) VALUES (?, ?, ?, ?, ?)';
                $adb->pquery($query, [$row['servicecontractsid'], $checkresult, $now, $remark, $current_user->id]);
            }
            $data = ['status'=>'success', 'msg'=>sprintf('成功核对%d条数据', $num)];
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    public function exportData(Vtiger_Request $request) {
        set_time_limit(0);
        global $current_user,$root_directory,$adb,$currentView;
        $currentView='List';
        $listViewModel = Vtiger_ListView_Model::getInstance('ServiceContracts');
        $listQuery = $listViewModel->getQuery();
        $listViewModel->getSearchWhere();
        $listQuery.=$listViewModel->getUserWhere();
        $queryGenerator = $listViewModel->get('query_generator');

        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }
        if(strstr($listQuery,',vtiger_servicecontracts.accountownerid')){
            $listQuery = str_replace(',vtiger_servicecontracts.accountownerid',',(select last_name from vtiger_users where id= (select smownerid from vtiger_crmentity where crmid=vtiger_account.accountid limit 1)) as accountownerid',$listQuery);
        }
        //待签收合同记录
        if($request->get('public') == 'NoComplete'){
            $listQuery=str_replace('vtiger_servicecontracts.last_collate_remark,','vtiger_servicecontracts.last_collate_remark,(SELECT sum(unit_price) FROM vtiger_receivedpayments WHERE vtiger_receivedpayments.relatetoid = vtiger_servicecontracts.servicecontractsid) AS receivedtotal,',$listQuery);
            $listQuery .= " AND vtiger_servicecontracts.servicecontractsid in(SELECT relatetoid FROM vtiger_receivedpayments WHERE relatetoid>0) AND vtiger_servicecontracts.iscomplete = 0 and vtiger_servicecontracts.modulestatus not in('c_cancel','c_recovered','c_canceling','c_stop','c_complete') order by vtiger_servicecontracts.servicecontractsid desc";
        }
//        dd($listQuery);
        //$listQuery = $listViewModel->replaceSQL($listQuery);
        $LISTVIEW_FIELDS = $listViewModel->getSelectFields();
        $listViewHeaders = $listViewModel->getListViewHeaders();
        $temp = array();
        if (!empty($LISTVIEW_FIELDS)) {
            foreach ($LISTVIEW_FIELDS as $key => $val) {
                if (isset($listViewHeaders[$key])) {
                    if($listViewHeaders[$key]['ishidden']){
                        continue;
                    }
                    $temp[$key] = $listViewHeaders[$key];
                }
            }
        }
        if(empty($temp)) {
            $temp = $listViewHeaders;
        }

        //待签收合同记录
        if($request->get('public') == 'NoComplete'){
            $noComplete = ['Modulestatus','Contract No','signaturetype','contract_classification','Related to','Type','Servicecontractstype','Priority','accountownerid','invoicecompany','Assigned To','Receive Date','Sign Id','Total','receivedtotal','firstreceivepaydate'];
            foreach($noComplete as $v){
                $tempArr[$v] = $temp[$v];
            }
            $headerArray = $tempArr;
        }else{
            $headerArray = $temp;
        }

        ini_set('memory_limit','1024M');
        $path = $root_directory.'temp/';
        !is_dir($path) && mkdir($path,'0755',true);
        $filename = $path.'服务合同'.date('Ymd').$current_user->id.'.csv';
        $array= array();
        foreach ($headerArray as $key => $value) {
            if (vtranslate($key, 'ServiceContracts') == '总额') {
                if($request->get('public') == 'NoComplete'){
                    $array[] = iconv('utf-8', 'gb2312', '合同金额');
                }else{
                    $array[] = iconv('utf-8', 'gb2312', vtranslate($key, 'ServiceContracts'));
                }
            } else {
                $array[] = iconv('utf-8', 'gb2312', vtranslate($key, 'ServiceContracts'));
            }
        }
        $fp = fopen($filename,'w');
        fputcsv($fp, $array);
        $limit = 5000;
        $i = 0;
        while(true){
            $limitSQL = " limit " . $i * $limit . ",". $limit;
            $i++;
            $result = $adb->pquery($listQuery . $limitSQL, array());
            if($adb->num_rows($result)){
                while ($value = $adb->fetch_array($result)) {
                    $array = array();
                    foreach ($headerArray as $keyheader => $valueheader) {
                        if (in_array($valueheader['columnname'], ['first_collate_status', 'last_collate_status'])) {
                            if ($value[$valueheader['columnname']]=='fit') {
                                $currnetValue = '符合';
                            } elseif($value[$valueheader['columnname']]=='unfit') {
                                $currnetValue = '不符合';
                            } else {
                                $currnetValue = '';
                            }
                        } else {
                            $currnetValue = uitypeformat($valueheader, $value[$valueheader['columnname']], 'ServiceContracts');
                        }
                        $currnetValue=preg_replace('/<[^>]*>/','',$currnetValue);
                        $currnetValue = iconv('utf-8', 'GBK//IGNORE', $currnetValue);
                        $array[] = $currnetValue;
                    }
                    fputcsv($fp, $array);
                }
                ob_flush();
                flush();
            }else{
                break;
            }
        }
        fclose($fp);
        $response=new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }

    public function exportFile() {
        global $site_URL,$current_user;
        header('location:'.$site_URL.'temp/'.'服务合同'.date('Ymd').$current_user->id.'.csv');
        exit;
    }
}
