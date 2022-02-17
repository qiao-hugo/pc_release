<?php

class SupplierContracts_ChangeAjax_Action extends Vtiger_Action_Controller {
    function __construct(){
        parent::__construct();
        $this->exposeMethod('getproducts');
        $this->exposeMethod('getproductlist');
        $this->exposeMethod('getextaproducts');
	$this->exposeMethod('serviceconfirm');
	$this->exposeMethod('getservicecontracts_reviced');
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
    public function getproducts(Vtiger_Request $request){
        $contract_typeName = $request->get('contract_typeName');
        $contract_typeName = urldecode($contract_typeName);    //接收js返回值，解析编码
        if(empty($contract_typeName)){
            exit;
        }
        $sql="SELECT vtiger_contractsproductsrel.relproductid FROM vtiger_contractsproductsrel
        WHERE vtiger_contractsproductsrel.contract_type=(SELECT contract_typeid FROM `vtiger_contract_type` WHERE vtiger_contract_type.contract_type=?)";
        $db = PearDatabase::getInstance();
        $relproductid = $db->pquery($sql,array($contract_typeName));
        //echo $db->num_rows($relproductid)."---";
        if ($db->num_rows($relproductid)>0) {
            $result_relproductid = $db->query_result($relproductid,'relproductid');
            // print_r($result_relproductid) ;die;
            if($result_relproductid !=""){
                $productid=explode(' |##| ', $result_relproductid);
                foreach($productid as $value){
                    $product_result = $db->pquery(" SELECT productid,productname FROM `vtiger_products` WHERE productid=".$value."");
                    $product_list[] = $db->fetchByAssoc($product_result);
                }
            }
        }else{
            $product_list=array();
        }
        echo json_encode($product_list);

    }

    /**
     * 产品的类型
     * @param Vtiger_Request $request
     */
    function getproductlist(Vtiger_Request $request){
        $parent_contracttypeid=$request->get('parent_contracttypeid');
        $db=PearDatabase::getInstance();
        $query = 'SELECT vtiger_contract_type.contract_type FROM vtiger_parent_contracttype_contracttyprel JOIN vtiger_contract_type ON vtiger_contract_type.contract_typeid=vtiger_parent_contracttype_contracttyprel.contract_typeid WHERE  vtiger_parent_contracttype_contracttyprel.parent_contracttypeid='.$parent_contracttypeid;
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
        if(0==$recordModel->entity->column_fields['isconfirm']){
            $flag=true;
        }else{
            $newtemp=$recordModel->entity->column_fields['confirmvalue'];
            $temp=explode("##",$newtemp);
            $tempn=explode(',',$temp[0]);
            $flag=true;
            if(substr($tempn[1],0,10)==date('Y-m-d')){
                $flag=false;
            }
        }
        if(ServiceContracts_DetailView_Model::exportGroupri()&&$recordModel->entity->column_fields['modulestatus']=='已发放'&& $flag){
            $sql="UPDATE vtiger_servicecontracts SET vtiger_servicecontracts.confirmvalue=TRIM(TRAILING '##' FROM CONCAT('".$current_user->column_fields['last_name'].",".date('Y-m-d H:i:s')."##',IFNULL(confirmvalue,''))),isconfirm=1,confirmlasttime='".date('Y-m-d H:i:s')."' WHERE servicecontractsid=?";
            $db->pquery($sql,array($recordId));
        }
        $response = new Vtiger_Response();
        $response->setResult(array());
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
}
