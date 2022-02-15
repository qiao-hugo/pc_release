<?php
class ContractsProducts_ChangeAjax_Action extends Vtiger_Action_Controller {

    function __construct(){
        parent::__construct();
        $this->exposeMethod('addInvoicecompany');
        $this->exposeMethod('getInvoicecompany');
        $this->exposeMethod('delInvoicecompany');
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
		$contract_typeName = $request->get('contract_typeName');
		if(empty($contract_typeName)){
			exit;
		}
        $sql="SELECT vtiger_contractsproductsrel.relproductid FROM vtiger_contractsproductsrel
        WHERE vtiger_contractsproductsrel.contract_type=(SELECT contract_typeid FROM `vtiger_contract_type` WHERE vtiger_contract_type.contract_type=".$contract_typeName.")";
        $db = PearDatabase::getInstance();
        $relproductid = $db->pquery($sql);
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
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     * 获取开票公司列表
     */
	public function getInvoicecompany(Vtiger_Request $request){
	    global $adb;
	    $query="SELECT invoicecompany,companycode FROM vtiger_invoicecompany";
	    $result=$adb->pquery($query,array());
	    $data=array();
	    while($row=$adb->fetch_array($result)){
            $data[]=$row;
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     * 添加开票内容，开票公司
     */
    public function addInvoicecompany(Vtiger_Request $request){
	    global $current_user,$adb;
        $record=$request->get('record');
        $billcontent = $request->get('billcontent');
        $invoicecompany = $request->get('invoicecompany');
        $sql='UPDATE vtiger_invoicecompanybill SET deleted=1,deletedid=?,deletedtime=? WHERE companycode=? AND relcontractsproductsid=?';
        $datetime=date('Y-m-d H:i:s');
        $adb->pquery($sql,array($current_user->id,$datetime,$invoicecompany,$record));
        $sql="INSERT INTO `vtiger_invoicecompanybill` (`invoicecompany`, `companycode`, `relcontractsproductsid`, `billingcontent`,  `createdtime`, `catedtedid`) 
              SELECT vtiger_invoicecompany.invoicecompany,?,?,?,?,? FROM vtiger_invoicecompany WHERE vtiger_invoicecompany.companycode=? limit 1";
        $adb->pquery($sql,array($invoicecompany,$record,$billcontent,$datetime,$current_user->id,$invoicecompany));
    }

    /**
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     * 删除开票工司开票内容
     */
    public function delInvoicecompany(Vtiger_Request $request){
        global $current_user,$adb;
        $record=$request->get('record');
        $sql='UPDATE vtiger_invoicecompanybill SET deleted=1,deletedid=?,deletedtime=? WHERE invoicecompanybillid=?';
        $datetime=date('Y-m-d H:i:s');
        $adb->pquery($sql,array($current_user->id,$datetime,$record));
    }
}
