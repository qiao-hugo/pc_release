<?php 

class Products_BasicAjax_Action extends Vtiger_Action_Controller{
	function __construct(){
		parent::__construct();	
		$this->exposeMethod('getproductstandard');
	}
	function checkPermission(Vtiger_Request $request) {
		return;
	}
	function process(Vtiger_Request $request){
	    $mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
	}
	/**
	 * @author wangbin 2015-07-02 读取产品规格下拉值,15-8-25新增产品的可选规格;
	 * @access public
	 * @param  int $productid 产品id;
	 * @return json
	 */
	function getproductstandard(Vtiger_Request $request){
        $productid = $request->get('productid');//产品id;
        $db= PearDatabase::getInstance();
        $strSelectstandardsql = "SELECT * FROM vtiger_products_standard WHERE productid = ? AND `delete` !=1";
	   // echo $strSelectstandardsql;
        if(!empty($productid)){
    	    $rsStandard = $db->pquery($strSelectstandardsql,array($productid));
    	    if($db->num_rows($rsStandard)>0){
    	        $arrLis = array();
    	        for($i=0;$i<$db->num_rows($rsStandard);$i++){
    	            $arrLis[] = $db->query_result_rowdata($rsStandard, $i);    	            
    	        }
    	        
    	        $strStandard1 = '<select class="chzn-select" name="defaultstand[]" data-validation-engine="validate[required]"><option value="">选择一个选项</option>';
                $strStandard2 = '';
    	        $choosablestand1 = '<select class="chzn-select"  multiple="true" name="choosablestand['.$productid.'][]" value = ""><option>请选择一个选项</option>';
                $choosablestand2 = '';
    	        foreach($arrLis as $value){
    	           //var_dump($value);die;
    	           $strStandard2.= '<option value="'.$value['standardid'].'" data-picklistvalue="'.$value['standardid'].'">'.$value['standardname'].'</option>';
                   $choosablestand2.='<option value="'.$value['standardid'].'" data-picklistvalue="'.$value['standardid'].'">'.$value['standardname'].'</option>';
                }
    	        $strStandardreturn1 = $strStandard1.$strStandard2."</select>";
    	        $strStandardreturn2 = $choosablestand1.$choosablestand2."</select>";

    	        //var_dump($arrLis);die;
    	    }else{
    	        $strStandardreturn1 = '<select class="chzn-select" name="defaultstand[]" value = ""><option>该产品无多规格</option></select>';
    	        $strStandardreturn2 = '<select class="chzn-select" multiple="true" name="choosablestand['.$productid.'][]" value = ""></select>';
    	    }
	    }

        $standard = array($strStandardreturn1,$strStandardreturn2);
	    $response = new Vtiger_Response();
	    $response->setResult($standard);
	    $response->emit();
	}
}