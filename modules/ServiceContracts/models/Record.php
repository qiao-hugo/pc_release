<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/


include_once('modules/Workflows/models/ModuleStatus.php');
class ServiceContracts_Record_Model extends Vtiger_Record_Model
{
//    //====线上地址========================================================
//    //T云合同签收通知
//    //private $tyun_contractconfirm_url = "http://tyunapi.71360.com/api/CRM/ContractConfirm";
//    //获取手机验证码
////    private $tyun_mobilecode_url = "http://tyunapi.71360.com/api/SMS/SendMobileMessageCode";
//    //建站购买地址
////    private $tyun_sitecontract_url = "http://tyunapi.71360.com/api/cms/CrmSiteContract";
//    //获取建站购买续费用户订单信息
//    //private $RenewCloudSite="http://tyunapi.71360.com/api/CRM/RenewCloudSite";
//    private $RenewCloudSite="http://apityun.71360.com/api/CRM/GetCloudSiteUser";
//    //创建建站购买续费订单
//    //private $RenewCloudSiteUser="http://apityun.71360.com/api/CRM/RenewCloudSiteUser";
//
//    //====线上地址========================================================
//    //T云合同签收通知
//    private $tyun_contractconfirm_url = "http://apityun.71360.com/api/CRM/ContractConfirm";
//    //获取手机验证码
//    //private $tyun_mobilecode_url = "http://apityun.71360.com/api/cms/GetSiteMobileCode";
//    //建站购买地址
//    private $tyun_sitecontract_url = "http://apityun.71360.com/api/cms/CrmSiteContract";
//	//创建建站购买续费订单
//    private $RenewCloudSiteUser="http://apityun.71360.com/api/CRM/RenewCloudSiteUser";
//
//    //===测试地址=========================================================
//    //private $tyun_contractconfirm_url = "http://tyunapi.arvin.com/api/CRM/ContractConfirm";
//    //private $tyun_mobilecode_url = "http://apityun.71360.com/api/cms/GetSiteMobileCode";
//    //本地测试地址
//    //private $tyun_sitecontract_url = "http://api.tyun.71360.com/api/cms/CrmSiteContract";

    public $elecContractWorkflowsid=372;
    public $changeSmownerWorkflowsid=3072416;
    //public $workflowid=361027;
    private $RenewCloudSite;
    private $RenewCloudSiteUser;
    private $tyun_contractconfirm_url;
    private $tyun_sitecontract_url;
    private $tyun_mobilecode_url;
    private $tyun_sitemobilecode_url;

    private $sault='multiModuleProjectDirectoryasdafdgfdhggijfgfdsadfggiytudstlllkjkgff';
    private $wkSault='1uioqyhiowhdpoijsdoksajdpoaie[pqwieopwiuqepqoiwe[1wsad';
    private $contractSend='common/send';//标准合同下单完成后发送合同接口
    private $auditStatus='common/audit_status';//接收审核状态接口(审核通过时自动签署)
    private $commonView='common/view?contractId=';//合同预览
    private $commonBack='common/back';//撤回接口
    private $commonTovoid='common/tovoid?contractId=';//合同作废
    private $commonAddProduct ='common/add_product';//新增产品
    private $contract_set='erp/contract_set';//定制合同上传
    private $contract_reset='erp/re_edit';//定制合同上传
    private $get_areas='erp/get_areas?contractId=';//定制合同上传
    private $saveAndReplaceOnline = "71360/save_and_replace";//T云线上下单合同替换
    private $signOnline = "71360/sign";//T云线上签署
    private $wkRefuseContract = "wk/refuse_sign";//数字威客拒签合同
    private $wkSignContract = "wk/customized_sign";//数字威客签收定制合同
    public  $add_product='common/add_product';//合同客户签收地址
    public $elecContractUrl='http://testhetong.71360.com ';//合同客户签收地址
    public $elecCancelWorkflowsid=2427397;//电子合同作废申请
    public $CONTRACT_PHASE_SPLIT_NUM=1;

    public $financialReviewNode1=array('ZD');
    public $financialReviewNode2=array('JHZD','HKKLLGJ','HKKLL','ZDNB','ZDSX','ZDYW','TZHZD','ZDWZ','WXKLL');
    public $financialReviewNode3=array('ZDCD','ZDGD','ZDWL','YJSKJ','ZHDZHNJSHJT','ZDGZH','ZDDG','ZDFSH','ZDSD','ZDZNHZ','ZSHZD','ZDSZH');
    public $financialReviewNode4=array('GZKLL','KLLDSHJKJJT');
    public $financialReviewNode5=array('KLL');
    public $financialReviewNode6=array('DCL','ZDNT','ZDCZ','ZDSD','ZDWX','WXJYFGS','WXZDZNJS');
    public $financialReviewNode7=array('ZDHZ','KSHZD','ZDNJ','ZDSZ');

    public $Kllcompanycode=array('KLL','KLLDSHJKJJT','GZKLL');//凯丽隆审核的合同主体公司
    public $TREASURER_TWO=array('ZD','ZDWL','YJSKJ','HKKLLGJ','HKKLL','ZHDZHNJSHJT');//上海财务审核的合同主体公司

    public $Kllcode=array('KLL','KLLDSHJKJJT','GZKLL');//凯丽隆审核的合同主体公司：刘媛媛
    public $TREASURER_THREE=array('ZDCD','ZDGD','ZDWL','YJSKJ','ZSHZD','ZDGZH','ZDDG','ZDFSH','ZDSD','DCL','ZDHZ','ZDCZ','ZDNT','ZDWX','WXZDZNJS','WXJYFGS');//孟昭燕
//    public $TREASURER_FOUR=array('ZDHZ','KSHZD','ZDNJ','ZDSZ','ZD','ZD-新','ZDZX-新');//顾瑞娟
//    public $TREASURER_FIVE=array('JHZD','HKKLLGJ','HKKLL','ZDNB','TZHZD','ZDWZ','ZDWZ','WXKLL');//梁燕
    public $TREASURER_FOUR=array('KSHZD','ZDNJ','ZDSZ','ZDNB','ZDWZ','JHZD','TZHZD','ZDSX','ZDYW','HKKLLGJ','WXKLL','HKKLL');//顾瑞娟
    public $TREASURER_FIVE=array('ZD','ZD-新','ZDZX-新');//袁蔚华

    public $Kllneedle='H283::';//凯丽隆部节门点
    public $WXKLLneedle='H349::';//无锡凯丽隆部节门点
    public static $paymentMatchNode=array(25,24,136,137);//回款匹配的产品节点
    public $phoneCheck = 'phone/check';        //全网手机三要素
    public $governmentCheck = 'government/check';//企业⼯商信息核验
    public $realNameCheck = 'realname/check';        //公安部实名认证
    private $AllCategory;
    private $GetMinPaymentMoney;
    private $ContractConfirmTimeout;
    private $customContractCallback;
    private $customContractAddCallback;
    private $getAgentLevel;
    public static $servicecontractstype= array(
        'upgrade'=>"upgrade",
//        1=>"againbuy",
        'degrade'=>"degrade",
        'buy'=>"新增",
        'renew'=>"续费",
    );
    //非T云合同的可签收最晚时间
    //季度的第1,2个月签订的合同，必须在当季度签收
    //季度第3个月签订的合同，必须在次季度签收
    public static $MONTH = array(
        '01'=>'03-31',
        '02'=>'03-31',
        '03'=>'06-30',
        '04'=>'06-30',
        '05'=>'06-30',
        '06'=>'09-30',
        '07'=>'09-30',
        '08'=>'09-30',
        '09'=>'12-31',
        '10'=>'12-31',
        '11'=>'12-31',
        '12'=>'03-31',
    );

    public function __construct($values = array())
    {
        parent::__construct($values);
        global $service_contracts_renew_cloud_site, $service_contracts_tyun_contractconfirm_url,
               $service_contracts_tyun_sitecontract_url, $service_contracts_renew_cloud_site_user,
               $service_contracts_send_mobile_code,$service_contracts_get_mobile_code,$tyunweburl,$weikeurl;
        $this->RenewCloudSite = $service_contracts_renew_cloud_site;
        $this->tyun_contractconfirm_url = $service_contracts_tyun_contractconfirm_url;
        $this->tyun_sitecontract_url = $service_contracts_tyun_sitecontract_url;
        $this->RenewCloudSiteUser = $service_contracts_renew_cloud_site_user;
        $this->tyun_mobilecode_url = $service_contracts_send_mobile_code;
        $this->tyun_sitemobilecode_url = $service_contracts_get_mobile_code;
        $this->AllCategory = $tyunweburl . 'api/micro/order-basic/v1.0.0/api/Category/AllCategory';
        $this->GetMinPaymentMoney = $tyunweburl . 'api/micro/aggregateservice-api/v1.0.0/api/Order/GetMinPaymentMoney';
        $this->ContractConfirmTimeout = $tyunweburl . 'api/micro/aggregateservice-api/v1.0.0/api/Order/ContractConfirmTimeout';
        $this->customContractCallback = $weikeurl . 'api/customContractCallback';
        $this->customContractAddCallback = $weikeurl . 'api/customContractAddCallback';
        $this->getAgentLevel = $tyunweburl . "api/app/tcloud-agent/v1.0.0/api/getAgentLevel";
    }
    //领取数量
    public static $numberOfReceipts=10;
    /*
     * 详情页面显示产品明细
     * */
    static function getProductsById($record)
    {
        $db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT
	*,
	vtiger_salesorderproductsrel.thepackage AS twebthepackage,
	vtiger_salesorderproductsrel.productid AS twebproductid,
	vtiger_salesorderproductsrel.productname AS twebproductname,
IF
	( vtiger_salesorderproductsrel.standard IS NULL, '默认规格', ( SELECT vtiger_products_standard.standardname FROM vtiger_products_standard WHERE vtiger_products_standard.standardid = vtiger_salesorderproductsrel.standard ) ) AS standard1,
IFNULL(vtiger_salesorderproductsrel.standardname,IF ( vtiger_salesorderproductsrel.standard IS NULL, '默认规格', ( SELECT vtiger_products_standard.standardname FROM vtiger_products_standard WHERE vtiger_products_standard.standardid = vtiger_salesorderproductsrel.standard ) )) AS standardname,
IF
	( vtiger_salesorderproductsrel.isextra = 0, '否', '是' ) AS isextra,
IF
	( vtiger_salesorderproductsrel.standard IS NULL, vtiger_products.realprice, vtiger_products_standard.realprice ) AS realprice,
IF
	( vtiger_salesorderproductsrel.standard IS NULL, vtiger_products.unit_price, vtiger_products_standard.singleprice ) AS unit_price,
	IFNULL( ( SELECT vtiger_products.productname FROM vtiger_products WHERE vtiger_products.productid = vtiger_salesorderproductsrel.productcomboid ), '--' ) AS thepackage,
	vtiger_products.productid,
IF
	( productcomboid IS NULL OR productcomboid = 0, vtiger_salesorderproductsrel.productid, productcomboid ) AS tagid,
	( SELECT vtiger_vendor.vendorname FROM vtiger_vendor WHERE vtiger_vendor.vendorid = vtiger_salesorderproductsrel.vendorid ) AS vendorname,
	( SELECT vtiger_suppliercontracts.contract_no FROM vtiger_suppliercontracts WHERE vtiger_suppliercontracts.suppliercontractsid = vtiger_salesorderproductsrel.suppliercontractsid ) AS supplier_contract_no 
FROM
	vtiger_salesorderproductsrel
	LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_salesorderproductsrel.productid
	LEFT JOIN vtiger_products_standard ON vtiger_products_standard.standardid = vtiger_salesorderproductsrel.standard 
WHERE
	servicecontractsid = ? 
	AND ( multistatus = 0 OR multistatus = 1 ) 
ORDER BY
	tagid", array($record));
        $rows = $db->num_rows($result);
        $product = array();
        if ($rows) {
            for ($i = 0; $i < $rows; ++$i) {
                $temp=$db->fetchByAssoc($result);
                if($temp['istyunweb']==1){
                    $temp['productname']=$temp['twebproductname'];
                    $temp['productid']=$temp['twebproductid'];
                    $temp['thepackage']=$temp['twebthepackage'];
                }
                $product[] = $temp;
            }
            return $product;
        }
        return false;
    }

    /**
     * 2015年4月22日 星期三 获取 货币符号
     * @param $recordId 合同id
     * @return string 返回货币类型，默认为人民币
     */
    public function getcurrencytype($recordId)
    {
        $data = "人民币";
        if (!empty($recordId)) {
            $db = PearDatabase::getInstance();
            $sql = 'SELECT currencytype FROM `vtiger_servicecontracts` WHERE servicecontractsid=?';
            $currencytype = $db->pquery($sql, array($recordId));
            if ($db->num_rows($currencytype) > 0) {
                $data = $currencytype->fields['currencytype'];
            }
        }
        return $data;
    }
/**
     * 当前已经配置的权限用户
     * @return array
     */
    public static function getReportPermissions(){
        $db=PearDatabase::getInstance();
        $query="SELECT vtiger_exportmanage.exportmanageid as id,last_name,classnamezh,module 
        FROM vtiger_exportmanage LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_exportmanage.userid WHERE vtiger_exportmanage.deleted=0 ORDER BY exportmanageid DESC";
        return $db->run_query_allrecords($query);
    }
    /**
     * 模块的名称做通用的
     * @return array
     */
    public static function getModulePicklist(){
        $db=PearDatabase::getInstance();
        $query="SELECT mountmodule as module
        FROM vtiger_mountmodule ";
        return $db->run_query_allrecords($query);
    }
    /**
     * 返回审核权限设置
     * @return array
     */
    public static function getAuditsettings($auditsettingtype="ServiceContracts") {
        $db=PearDatabase::getInstance();
        $sql = "SELECT auditsettingsid, IF(auditsettingtype='ServiceContracts','服务合同',IF(auditsettingtype='ContractsAuditset','非标合同审核','采购合同')) AS auditsettingtype,
   (select vtiger_departments.departmentname FROM vtiger_departments WHERE vtiger_departments.departmentid=vtiger_auditsettings.department) AS department,
   (SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_auditsettings.oneaudituid) AS oneaudituid, 
   IFNULL((SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_auditsettings.towaudituid ),'--') AS towaudituid, 
   IFNULL((SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_auditsettings.audituid3 ),'--') AS audituid3,
   IFNULL((SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_auditsettings.audituid4 ),'--') AS audituid4,
   IFNULL((SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_auditsettings.audituid5 ),'--') AS audituid5
   FROM vtiger_auditsettings WHERE auditsettingtype=? ORDER BY auditsettingsid DESC";
        //return $db->run_query_allrecords($sql,array($auditsettingtype));
        return $db->pquery($sql,array($auditsettingtype));
    }
    /**
     * 显示可导出的列表
     * @return array
     */
    public static function getSetPermissions(){
        $db=PearDatabase::getInstance();
        $query="SELECT * FROM vtiger_contractrpatymtable WHERE deleted=0";
        $result=$db->run_query_allrecords($query,array());
        $arr=array();
        if(!empty($result)) {
            foreach ($result as $value) {
                $arr[$value['module']].='<option value="'.$value['mode'].'">'.$value['modename'].'</option>';
            }
        }
        return json_encode($arr);
    }

    /*
     * 获取产品名称
     */
    static function getProductsId($recordId)
    {
        $db = PearDatabase::getInstance();
        if (!empty($recordId)) {
            $sql = '  SELECT relproductid FROM `vtiger_contractsproductsrel` WHERE vtiger_contractsproductsrel.contract_type =(
                      SELECT vtiger_contract_type.contract_typeid FROM `vtiger_contract_type` WHERE vtiger_contract_type.contract_type=(
                      SELECT vtiger_servicecontracts.contract_type FROM vtiger_servicecontracts WHERE vtiger_servicecontracts.servicecontractsid = ?))';
            $result = $db->pquery($sql, array($recordId));
            $relproductid =  $db->query_result($result, 'relproductid');
            $productid=explode(' |##| ', $relproductid);
            foreach($productid as $value){

                $product_result = $db->pquery(" SELECT productid,productname FROM `vtiger_products` WHERE productid=".$value." ORDER BY sequence");

                for ($i=0; $i<$db->num_rows($product_result); ++$i) {
                    $productname = $db->fetchByAssoc($product_result);
                    $products[]=$productname;
                }

            }
//            $sql = 'select vtiger_products.productname,vtiger_products.productid,vtiger_salesorderproductsrel.salesorderproductsrelid,vtiger_salesorderproductsrel.servicecontractsid from vtiger_salesorderproductsrel left join vtiger_products on  vtiger_products.productid=vtiger_salesorderproductsrel.productid  where servicecontractsid=?';
//            $result = $db->pquery($sql, array($recordId));
//            for ($i=0; $i<$db->num_rows($result); ++$i) {
//                $product = $db->fetchByAssoc($result);
//                $products[]=$product;
//            }
//            for($i=0; $i<$db->num_rows($result); $i++) {
//                $salesorderproductsrelid = $db->query_result($result, $i, 'salesorderproductsrelid');
//                $servicecontractsid = $db->query_result($result, $i, 'servicecontractsid');
//                $productid = $db->query_result($result, $i, 'productid');
//                $productname = $db->query_result($result, $i, 'productname');
//            }
        }
        return $products;
    }
    /*
     * //获取当前的合同类型对应产品名称
     *
     */
    static function getContractType($recordId)
    {
        $db = PearDatabase::getInstance();
        if (!empty($recordId)) {
            $sql = 'SELECT productid FROM vtiger_servicecontracts WHERE servicecontractsid=?';
            $result = $db->pquery($sql, array($recordId));
            $productid =  $db->query_result($result, 'productid');
            $productid=explode(',', $productid);
            /*$sql = '  SELECT relproductid FROM `vtiger_contractsproductsrel` WHERE vtiger_contractsproductsrel.contract_type =(
                      SELECT vtiger_contract_type.contract_typeid FROM `vtiger_contract_type` WHERE vtiger_contract_type.contract_type=(
                      SELECT vtiger_servicecontracts.contract_type FROM vtiger_servicecontracts WHERE vtiger_servicecontracts.servicecontractsid = ?))';
            $result = $db->pquery($sql, array($recordId));
            $relproductid =  $db->query_result($result, 'relproductid');
            $productid=explode(' |##| ', $relproductid);
            foreach($productid as $value){

                $product_result = $db->pquery(" SELECT productid,productname FROM `vtiger_products` WHERE productid=".$value."");

                for ($i=0; $i<$db->num_rows($product_result); ++$i) {
                    $productname = $db->fetchByAssoc($product_result);
                    $product_namelist[]=$productname;
                }
                //chnap
            }*/
        }
        return $productid;
    }

    /**
     *合同关联的产品联动
     */
    static function productcategory($record){
        $db = PearDatabase::getInstance();
        //取得合同类型的第一个联动框的内容列表

        $query = 'SELECT * FROM vtiger_parent_contracttype order by sortorderid';
        $result['parent'] = $db->run_query_allrecords($query);
        //第一个联动框已经选中的项
        $nparentcontracttypeid=0;
        if($record>0){
            $query = 'SELECT parent_contracttypeid FROM vtiger_servicecontracts WHERE servicecontractsid=? limit 1';
            $data=$db->pquery($query,array($record));
            $nparentcontracttypeid=$db->query_result($data,0,'parent_contracttypeid');
            //是否为新建给个1
            $result['nparentid']=$nparentcontracttypeid;
        }


        $nparentcontracttypeid=$nparentcontracttypeid>0?$nparentcontracttypeid:1;
        //取得第二个框中的内容列表
        $query = 'SELECT vtiger_contract_type.contract_type FROM vtiger_parent_contracttype_contracttyprel JOIN vtiger_contract_type ON vtiger_contract_type.contract_typeid=vtiger_parent_contracttype_contracttyprel.contract_typeid WHERE  vtiger_parent_contracttype_contracttyprel.parent_contracttypeid='.$nparentcontracttypeid;
        $arrrecords = $db->run_query_allrecords($query);
        if(!empty($arrrecords)){
            $arrlist=array();
            foreach($arrrecords as $value){
                $arrlist[]=$value['contract_type'];
            }
            $result['ischild']=$arrlist;
        }
        return $result;
    }

    /**
     * 额外产品下列项
     * @param $recordId
     * @return array
     */
    static public function getextraproduct($recordId){
        $db = PearDatabase::getInstance();
        //if (!empty($recordId)) {
            //$query = "SELECT productid,productname FROM vtiger_products WHERE productcategory=(SELECT vtiger_servicecontracts.productcategory FROM vtiger_servicecontracts WHERE servicecontractsid={$recordId})";
            //$query = "SELECT productid,productname FROM vtiger_products WHERE customer=1";
        $query="
            SELECT (CASE 
            WHEN vtiger_products.sequence BETWEEN 0 AND 100 THEN 1
            WHEN vtiger_products.sequence BETWEEN 101 AND 200 THEN 2
            ELSE 3 END) AS groupflag,vtiger_products.productid,vtiger_products.productname FROM vtiger_products
            INNER JOIN vtiger_crmentity ON(vtiger_crmentity.crmid=vtiger_products.productid) WHERE vtiger_products.customer=1 AND vtiger_crmentity.deleted=0 ORDER BY vtiger_products.sequence";

            //$query = "SELECT productid,productname FROM vtiger_products WHERE productid in(361935)";
            return $db->run_query_allrecords($query);

        //}
    }

    /**
     * 合同状态改变来触发提醒
     * @param string $status
     * @param array() $newarr
     */
    static public function setSalesorderandAlert($status,$newarr=array(),$id=0){
        return;
        $db=PearDatabase::getInstance();
        $contractarr=array('contract_no'=>$_REQUEST['contract_no'],'Receiveid'=>$_REQUEST['Receiveid'],'sc_related_to'=>$_REQUEST['sc_related_to'],'assigned_user_id'=>$_REQUEST['assigned_user_id']);
        //$newarr 是通过saveajax传过来的值下面做一个判断是否是savaajax传过来的值
        $contractarr=empty($newarr)?$contractarr:$newarr;
        switch($status){
            case Workflows_Module_Model::$moudulestatus['c_complete']:
                //echo  'c_complete';
                $arralert=array('合同单号:【'.$contractarr['contract_no'].'】已经完成,工单已经生成','合同单号:【'.$contractarr['contract_no'].'】跟进工单',$contractarr['Receiveid'],$contractarr['Receiveid'],$contractarr['sc_related_to']);
                //指定的产品生成工单
                if(self::createIsWorkflows($_REQUEST['productid'])){
                    self::createSaleorder($id);
                    JobAlerts_Record_Model::saveAlert($arralert);
                }

                break;
            case Workflows_Module_Model::$moudulestatus['c_contract_n_account']:
                $arralert=array('合同单号:【'.$contractarr['contract_no'].'】已交回合同,款未到账,请跟进','合同单号:【'.$contractarr['contract_no'].'】跟进到账',$contractarr['assigned_user_id'],$contractarr['assigned_user_id'],$contractarr['sc_related_to']);

                JobAlerts_Record_Model::saveAlert($arralert);
                break;
            case Workflows_Module_Model::$moudulestatus['c_account_n_contract']:
                $arralert=array('合同单号:【'.$contractarr['contract_no'].'】已收到回款,合同尚未交还,请及时交还合同','合同单号:【'.$contractarr['contract_no'].'】跟进合同',$contractarr['assigned_user_id'],$contractarr['assigned_user_id'],$contractarr['sc_related_to']);

                JobAlerts_Record_Model::saveAlert($arralert);
                break;
            default:
        }
    }



    /**
     *合同生成工单
     */
    private function createSaleorder($id){
	 return;
        //作废
        $db=PearDatabase::getInstance();
        $result=$db->run_query_allrecords("SELECT servicecontractsid,concat(accountname,'的合同工单') as accountename,total,receiveid,accountid,account_no FROM vtiger_servicecontracts LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_servicecontracts.sc_related_to WHERE servicecontractsid={$id} LIMIT 1");
        unset($_REQUEST);//删掉$_REQUEST该 数据影响下面的工单生成数据
        $_REQUES['record']='';
        //$_REQUEST['record_id']='';
        //$_REQUEST['workflowsid']=361027;

        $request=new Vtiger_Request($_REQUES, $_REQUES);
        $request->set('subject',$result[0]['accountename']);
        $request->set('servicecontractsid',$result[0]['servicecontractsid']);
        $request->set('customerno',$result[0]['account_no']);
        $request->set('assigned_user_id',$result[0]['receiveid']);
        //根据回款和成本之间来确定是否是回款不足
        if(self::receivedayprice($id)) {
            $request->set('modulestatus', 'a_normal');
        }else{
            //回款不足
            $request->set('modulestatus', 'c_lackpayment');
        }
        $request->set('account_id',$result[0]['accountid']);//
        $request->set('workflowsid',self::selectWorkfows());
        $request->set('salescommission',$result[0]['total']);
        $request->set('issubmit',1);
        $request->set('module','SalesOrder');
        $request->set('view','Edit');
        $request->set('action','Save');
        $ressorder=new SalesOrder_Save_Action();

        $ressorder->saveRecord($request);
        //$crmid=$db->getUniqueID('vtiger_crmentity');求表ID当前最大的
        //求生成后对应工单的ID
        $salesorderid=self::getSalesorderid($id);
        $db->pquery("INSERT INTO vtiger_salesorder_productdetail SELECT ?,relateid,'',formid FROM `vtiger_customer_modulefields` WHERE relatedmodule='Products' AND relateid in(SELECT  vtiger_salesorderproductsrel.productid FROM vtiger_salesorderproductsrel WHERE servicecontractsid=?)",array($salesorderid,$id));

        self::contractsMakeWorkflows($salesorderid,$result[0]['servicecontractsid']);//生成工单对应的工作流
        //看一下是回款总额是否大于成本总和.如果大于则生成工作流
        if(self::receiveDayprice($id)){
            //第一个节点自动审核
            self::setWorkflowNode($salesorderid);
            //首款的自动审核
            //self::setWorkflowNodeFirst($salesorderid);
        }
        if(self::receiveDayprice($id,2)){
            //尾款的自动审核
            self::setWorkflowNodeFirst($salesorderid,'last_payment');
        }
        //生成工流

    }

    /**
     * 合同对应成本之和和回款总和之间比较备份
     * @param $contractid 合同的ID号
     * @return bool
     * @throws Exception
     */
    static public function receiveDaypricebak($contractid,$checkcount=1){
        $db=PearDatabase::getInstance();
        $query="SELECT sum(vtiger_receivedpayments.unit_price) AS sumtotal FROM `vtiger_receivedpayments` WHERE relatetoid =? AND receivedstatus='normal'";
        if($checkcount==1){
            //$query.=" AND isdownpayment!=1";订金
        }
         $results=$db->pquery($query,array($contractid));
        $result=$db->query_result($results,0,'sumtotal');//所有回款的之合
        if($checkcount==1) {
            $query = "SELECT sum(IFNULL(vtiger_salesorderproductsrel.costing,0)+IFNULL(vtiger_salesorderproductsrel.purchasemount,0)) AS realprice,vtiger_salesorderproductsrel.salesorderid,vtiger_servicecontracts.receiveid FROM vtiger_salesorderproductsrel LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_salesorderproductsrel.servicecontractsid WHERE vtiger_salesorderproductsrel.servicecontractsid =?";//回款总合
        }else{
            $query = "SELECT total AS realprice,0 AS salesorderid,receiveid FROM vtiger_servicecontracts WHERE servicecontractsid= ?";//求出合同价
        }
        $realprices=$db->pquery($query,array($contractid));
        $realprice=$db->query_result($realprices,0,'realprice');//所有产品的成本之合数量*年限*成本单价
        $salesorderid=$db->query_result($realprices,0,'salesorderid');//生工单的id
        //$receiveid=$db->query_result($realprices,0,'receiveid');//合同的提单人

        /*if($receiveid>0){
            $query='SELECT IFNULL(sum(total),0) as totals FROM vtiger_guarantee WHERE deleted=0 AND userid=? AND contractid!=?';
            $guarantee=$db->pquery($query,array($receiveid,$contractid));
            $guaranteetal=$db->query_result($guarantee,0,'totals');//商务已经担保总的担保金额
        }*/
        $datetime=date('Y-m-d H:i:s');
        if($result>=$realprice && $realprice>=0){
            //回款大于成本
            if($salesorderid>0){
                $sql="UPDATE vtiger_salesorder SET vtiger_salesorder.guaranteetotal=0 WHERE vtiger_salesorder.salesorderid=?";
                $db->pquery($sql,array($salesorderid));
                $sql="UPDATE `vtiger_guarantee` SET vtiger_guarantee.deleted=1,delta=total,deltatime='{$datetime}' WHERE vtiger_guarantee.contractid=? AND vtiger_guarantee.salesorderid=?";
                $db->pquery($sql,array($contractid,$salesorderid));
            }
            return true;
        }elseif($salesorderid>0){
            //看一下有没有回款没有回款直接退出不向下走
            $Guaranteesalesorderguarante=Guarantee_Record_Model::getGuarantecurrent($salesorderid);//对应工单已担保的总成本
            if($Guaranteesalesorderguarante==0){
                //没有直担保直接返回false;
                return false;
            }
            $Guaranteereceiveprice=Guarantee_Record_Model::getreceivedayprice($contractid);//对应回款的总金额
            $Guaranteerealprice=Guarantee_Record_Model::getrealprice($salesorderid);//对应的总成本
            $temptotal=$Guaranteereceiveprice+$Guaranteesalesorderguarante-$Guaranteerealprice;
            if($temptotal==0){
                //担保金额+回款正好等于成本时不用更新担保直接走工作流
                return true;
            }elseif($temptotal<0){
                //担保金额+回款小于成本时后面无行走直接退出
                return false;
            }elseif($temptotal>0){
                //回款比较足可以用来冲掉部分担保
                $query="SELECT  vtiger_guarantee.guaranteeid,vtiger_guarantee.userid,vtiger_guarantee.contractid, vtiger_guarantee.salesorderid,vtiger_guarantee.total,vtiger_guarantee.presence,vtiger_guarantee.guaranteeid,vtiger_guarantee.createdtime FROM vtiger_guarantee  LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_guarantee.contractid  LEFT JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid=vtiger_guarantee.salesorderid  WHERE 1=1 AND deleted=0 AND vtiger_guarantee.salesorderid={$salesorderid} ORDER BY vtiger_guarantee.presence ASC,vtiger_guarantee.guaranteeid ASC";
                $resultddddd=$db->run_query_allrecords($query);
                $guaranteeids='';
                $insertid='';
                if(!empty($resultddddd)){
                    foreach($resultddddd as $value){
                        $newmoney=$temptotal-$value['total'];
                        if($newmoney>=0){
                            $temptotal=$newmoney;
                            $guaranteeids.=$value['guaranteeid'].',';
                            if($newmoney==0){
                                break;
                            }
                        }else{
                            $insertid=$value['guaranteeid'];
                            $inserttotal=$value['total']-$temptotal;
                            $newresult=$value;
                            break;
                        }
                    }
                    if(!empty($guaranteeids)){
                        $guaranteeids=rtrim($guaranteeids,',');
                        $query="UPDATE `vtiger_guarantee` SET vtiger_guarantee.deleted=1,delta=total,deltatime=? WHERE vtiger_guarantee.guaranteeid in({$guaranteeids})";
                        $db->pquery($query,array($datetime));
                    }
                    if($insertid>0){
                        $db->pquery("UPDATE `vtiger_guarantee` SET vtiger_guarantee.deleted=1,delta=?,deltatime=? WHERE vtiger_guarantee.guaranteeid=?",array($temptotal,$datetime,$insertid));
                        $db->pquery("INSERT INTO vtiger_guarantee(userid,contractid,salesorderid,total,presence,createdtime) VALUES(?,?,?,?,?,?)",array($newresult['userid'],$newresult['contractid'],$newresult['salesorderid'],$inserttotal,$newresult['presence'],$newresult['createdtime']));
                    }
                    $Guaranteesalesorderguarante=Guarantee_Record_Model::getGuarantecurrent($salesorderid);;//对应回款的总金额
                    Guarantee_Record_Model::updatesalesordertotal($Guaranteesalesorderguarante,$salesorderid);//更新对应工单的担保金额
                }

                return true;
            }
        }

        return false;
    }
    /**
     * 合同对应成本之和和回款总和之间比较
     * @param $contractid 合同的ID
     * @param $salesorderid 工单的ID
     * @param bool $flag 标志位True为T云合同,false为非标合同
     * @return bool
     */
    static public function receiveDayprice($contractid,$salesorderid,$flag=true){
        $db=PearDatabase::getInstance();
        //对应工单的总成本
        $query = "SELECT sum(IFNULL(vtiger_salesorderproductsrel.costing,0)+IFNULL(vtiger_salesorderproductsrel.purchasemount,0)) AS realprice,vtiger_salesorder.alreadycalculate,vtiger_salesorder.guaranteetotal FROM vtiger_salesorderproductsrel LEFT JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid = vtiger_salesorderproductsrel.salesorderid WHERE vtiger_salesorderproductsrel.salesorderid =? AND vtiger_salesorderproductsrel.multistatus in(0,3)";
        $realprices=$db->pquery($query,array($salesorderid));
        $salesorderprice=$db->query_result($realprices,0,'realprice');//当前对应工单的总成本
        $salesordeflag=$db->query_result($realprices,0,'alreadycalculate');//当前工单是否已经有计算成本
        $salesordeguaranteetotal=$db->query_result($realprices,0,'guaranteetotal');//当前对应工单的担保金
        if($flag && 1==$salesordeflag && $salesordeguaranteetotal==0){
            //工单已计算
            return true;
        }
        $query="SELECT sum(vtiger_receivedpayments.unit_price) AS sumtotal FROM `vtiger_receivedpayments` WHERE relatetoid =? AND receivedstatus='normal'";
        //对应合同的总回款数
        $results=$db->pquery($query,array($contractid));
        $receivedpaymentsprice=$db->query_result($results,0,'sumtotal');//所有回款的之合
        //对应合同的所有工单总成本
        $query = "SELECT sum(IFNULL(vtiger_salesorderproductsrel.costing,0)+IFNULL(vtiger_salesorderproductsrel.purchasemount,0)) AS realprice FROM vtiger_salesorderproductsrel LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_salesorderproductsrel.servicecontractsid WHERE vtiger_salesorderproductsrel.servicecontractsid =? AND vtiger_salesorderproductsrel.multistatus in(0,3)";
        $realprices=$db->pquery($query,array($contractid));
        $allrealprice=$db->query_result($realprices,0,'realprice');//对应合同的所有工单总成本
        //对应已计算工单的总成本
        $query = "SELECT IFNULL(sum(IFNULL(vtiger_salesorderproductsrel.costing,0)+IFNULL(vtiger_salesorderproductsrel.purchasemount,0)),0) AS realprice FROM vtiger_salesorderproductsrel LEFT JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid = vtiger_salesorderproductsrel.salesorderid WHERE vtiger_salesorderproductsrel.servicecontractsid=? AND vtiger_salesorderproductsrel.salesorderid!=? AND vtiger_salesorder.alreadycalculate=1 AND vtiger_salesorderproductsrel.multistatus in(0,3)";
        $realprices=$db->pquery($query,array($contractid,$salesorderid));
        $salesordealreadycalculate=$db->query_result($realprices,0,'realprice');//对应已计算工单的总成本
        $datetime=date('Y-m-d H:i:s');
        $effectiveamount=$receivedpaymentsprice-$salesordealreadycalculate;//总回款-已经计算过的总成本=可用的回款
        //1:总回款大于总成本直接走
        //2:总回款-当前已经计算过回款的工单大于当前的工单成本直接走
        //3:查担保是否满足条件
        /*echo $receivedpaymentsprice,"receivedpaymentsprice<hr>";
        echo $allrealprice,"allrealprice<hr>";
        echo $effectiveamount,"effectiveamount<hr>";
        echo $salesorderprice,"salesorderprice<hr>";*/
        if($receivedpaymentsprice>=$allrealprice||$effectiveamount>=$salesorderprice){
            //回款大于成本
            $sql="UPDATE vtiger_salesorder SET vtiger_salesorder.guaranteetotal=0,alreadycalculate=1,occupancyamount=? WHERE vtiger_salesorder.salesorderid=?";
            $db->pquery($sql,array($salesorderprice,$salesorderid));
            $sql="UPDATE `vtiger_guarantee` SET vtiger_guarantee.deleted=1,delta=total,deltatime='{$datetime}' WHERE vtiger_guarantee.salesorderid=?";
            $db->pquery($sql,array($salesorderid));
            return true;
        }else{
            //看一下有没有回款,没有回款直接退出不向下走
            $Guaranteesalesorderguarante=Guarantee_Record_Model::getGuarantecurrent($salesorderid);//对应工单已担保的总成本
            //echo $Guaranteesalesorderguarante,"<hr>";
            if($Guaranteesalesorderguarante==0){
                //没有直担保直接返回false;
                return false;
            }

            $query = " SELECT sum(vtiger_salesorder.occupancyamount) AS occupancyamount FROM `vtiger_salesorder` WHERE servicecontractsid=? AND salesorderid!=?";
            $realprices=$db->pquery($query,array($contractid,$salesorderid));
            $occupancyamount=$db->query_result($realprices,0,'occupancyamount');//对应已计算工单的占用的回款
            //可用的有效回款+对应工单的担保-对应工单的成本
            $temptotal=$receivedpaymentsprice+$Guaranteesalesorderguarante-$salesorderprice-$occupancyamount;
            $tempoccupancyamount=$temptotal;
            if($temptotal==0){
                //担保金额+回款正好等于成本时不用更新担保直接走工作流
                return true;
            }elseif($temptotal<0){
                //担保金额+回款小于成本时后面无行走直接退出
                return false;
            }elseif($temptotal>0){
                //回款比较足可以用来冲掉部分担保
                $query="SELECT  vtiger_guarantee.guaranteeid,vtiger_guarantee.userid,vtiger_guarantee.contractid, vtiger_guarantee.salesorderid,vtiger_guarantee.total,vtiger_guarantee.presence,vtiger_guarantee.guaranteeid,vtiger_guarantee.createdtime FROM vtiger_guarantee  LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_guarantee.contractid  LEFT JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid=vtiger_guarantee.salesorderid  WHERE 1=1 AND deleted=0 AND vtiger_guarantee.salesorderid={$salesorderid} ORDER BY vtiger_guarantee.presence ASC,vtiger_guarantee.guaranteeid ASC";
                $resultddddd=$db->run_query_allrecords($query);
                $guaranteeids='';
                $insertid='';
                if(!empty($resultddddd)){
                    foreach($resultddddd as $value){
                        $newmoney=$temptotal-$value['total'];
                        if($newmoney>=0){
                            $temptotal=$newmoney;
                            $guaranteeids.=$value['guaranteeid'].',';
                            if($newmoney==0){
                                break;
                            }
                        }else{
                            $insertid=$value['guaranteeid'];
                            $inserttotal=$value['total']-$temptotal;
                            $newresult=$value;
                            break;
                        }
                    }
                    if(!empty($guaranteeids)){
                        $guaranteeids=rtrim($guaranteeids,',');
                        $query="UPDATE `vtiger_guarantee` SET vtiger_guarantee.deleted=1,delta=total,deltatime=? WHERE vtiger_guarantee.guaranteeid in({$guaranteeids})";
                        $db->pquery($query,array($datetime));
                    }
                    if($insertid>0){
                        $db->pquery("UPDATE `vtiger_guarantee` SET vtiger_guarantee.deleted=1,delta=?,deltatime=? WHERE vtiger_guarantee.guaranteeid=?",array($temptotal,$datetime,$insertid));
                        $db->pquery("INSERT INTO vtiger_guarantee(userid,contractid,salesorderid,total,presence,createdtime) VALUES(?,?,?,?,?,?)",array($newresult['userid'],$newresult['contractid'],$newresult['salesorderid'],$inserttotal,$newresult['presence'],$newresult['createdtime']));
                        $db->pquery("UPDATE `vtiger_guarantee_seq` SET id=(SELECT guaranteeid FROM vtiger_guarantee ORDER BY guaranteeid DESC limit 1)",array());//更新表ID防止添加记录时数据不同步出错
                    }
                    $tempoccupancyamount=$tempoccupancyamount>$salesorderprice?$salesorderprice:$tempoccupancyamount;
                    $Guaranteesalesorderguarante=Guarantee_Record_Model::getGuarantecurrent($salesorderid);;//对应回款的总金额
                    Guarantee_Record_Model::updatesalesordertotal($Guaranteesalesorderguarante,$tempoccupancyamount,$salesorderid);//更新对应工单的担保金额
                }
                return true;
            }
        }
        return false;
    }

    /**
     * 生成工单的工作流
     * @param $workflowsid
     */
    static public function contractsMakeWorkflows($salesorderid,$servicecontractsid,$falg=0){
        $db=PearDatabase::getInstance();
        if($falg!=0){
            $db->pquery("UPDATE vtiger_salesorder SET modulestatus=(IF(modulestatus='c_lackpayment','b_actioning',modulestatus)) WHERE vtiger_salesorder.salesorderid=?",array($salesorderid));
        }

        //$db->pquery('UPDATE vtiger_salesorderproductsrel SET productform=(SELECT vtiger_productcf.notecontent FROM vtiger_productcf WHERE vtiger_productcf.productid=vtiger_salesorderproductsrel.productid) WHERE vtiger_salesorderproductsrel.servicecontractsid=?',array($servicecontractsid));

        //2015-2-12 新增产品负责人

        //$result = $db->pquery("SELECT vtiger_crmentity.smcreatorid, vtiger_products.productname,vtiger_products.productid,vtiger_products.productman FROM `vtiger_salesorderproductsrel` LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_salesorderproductsrel.productid LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_salesorderproductsrel.productid WHERE salesorderid =? ",array($salesorderid));
        $result = $db->pquery("SELECT vtiger_products.productname, vtiger_products.productcategory, vtiger_products.realprice, vtiger_products.unit_price, vtiger_crmentity.smownerid, vtiger_products.productid FROM `vtiger_salesorderproductsrel` LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_salesorderproductsrel.productid LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_salesorderproductsrel.productid WHERE salesorderid =? ",array($salesorderid));
        while($product=$db->fetch_row($result)){
            $checkarray[]=array('workflowstagesname'=> $product['productname'].'审核','smcreatorid'=>$product['smownerid'],'productid'=>$product['productid']);
            //$checkarray[]=array('workflowstagesname'=> $product['productname'].'审核','smcreatorid'=>0,'productid'=>$product['productid'],'productman'=>$product['productman']);
        }
        vglobal('checkproducts',$checkarray);

        $on_focus = CRMEntity::getInstance('SalesOrder');
        $on_focus->makeWorkflows('SalesOrder',self::selectWorkfows(), $salesorderid,'edit');

        //更新客户的等级
        /*
        $recordModel = Vtiger_Record_Model::getInstanceById($servicecontractsid, 'ServiceContracts');

        $entity=$recordModel->entity->column_fields;
        //$accountid='';sc_related_to
        if($entity['sc_related_to']>0){
            Accounts_Record_Model::updateAccountsDealtime($entity['sc_related_to']);
        }
        */


    }

    /**
     * 判断是是否有工作流生成
     * @param $salesorderid//对应工单的ID
     * @throws Exception
     */
    static public function getWorkflows($salesorderid){

        $db=PearDatabase::getInstance();
		$query="SELECT 1 FROM vtiger_salesorder WHERE modulestatus in('c_cancel','a_normal') AND salesorderid=?";
        $resultsalesorder=$db->pquery($query,array($salesorderid));
        $resultn=$db->num_rows($resultsalesorder);//是否是已作废的工单
        if($resultn>0){
            //作废的
            return false;
        }
        $query="SELECT count(1) AS counts FROM vtiger_salesorderworkflowstages WHERE salesorderid= ?";
        $result=$db->pquery($query,array($salesorderid));
        $result=$db->query_result($result,0,'counts');//是否有工作流生成
        if($result>0){
            //已经生成了工作流
            return false;
        }
        return true;
    }

    /**
     * 求合同对应的工单ID
     * @param $id合同对应的ID
     * @return mixed|string
     * @throws Exception
     */
    static public function getSalesorderid($contractid){
        $db=PearDatabase::getInstance();
         $query="SELECT salesorderid FROM vtiger_salesorder LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_salesorder.salesorderid WHERE vtiger_crmentity.deleted=0 AND servicecontractsid={$contractid} ORDER BY salesorderid";
        //从小到大
        return $db->run_query_allrecords($query);
        $salesorderid=$db->pquery($query,array($contractid));
        return $db->query_result($salesorderid,0,'salesorderid');//新生的工单的ID
    }
    /**
     * 工单中对应的工作流ID
     * @param $salesorderid的ID
     * @return mixed|string
     * @throws Exception
     */
    static public function getSalesorderworkflowsid($salesorderid){
        $db=PearDatabase::getInstance();
        $query="SELECT workflowsid FROM vtiger_salesorder WHERE salesorderid=? ";
        $workflowsid=$db->pquery($query,array($salesorderid));
        return $db->query_result($workflowsid,0,'workflowsid');//对应工单中的工作流ID
    }

    /**
     * 选择要生成的工作流
     */
    static public function selectWorkfows(){
        global $service_contracts_select_workfows;
        return $service_contracts_select_workfows;
    }
    /**
     * 保留工单工作流前两个节点要也审核
     */
    static public function keepNode(){
        global $service_contracts_keep_node;
        return $service_contracts_keep_node;
    }
    /**
     * 只判断有没有回款的套餐
     */
    public static function checkParymentsNode(){
        global $service_contracts_check_paryments_node;
        return $service_contracts_check_paryments_node;
    }

    /**
     *指定套餐的产品,判断有没有回款
     *
     */
    public static function whetherPayment($serviceid){
        $db=PearDatabase::getInstance();
        $query="SELECT 1 FROM vtiger_salesorderproductsrel WHERE servicecontractsid=? AND productcomboid in(".implode(',',self::checkParymentsNode()).")";
        $result=$db->pquery($query,array($serviceid));
        if($db->num_rows($result)==0){
            return false;
        }
        $query="SELECT sum(vtiger_receivedpayments.unit_price) AS sumtotal FROM `vtiger_receivedpayments` WHERE relatetoid =? AND receivedstatus='normal'";
        //对应合同的总回款数
        $results=$db->pquery($query,array($serviceid));
        $receivedpaymentsprice=$db->query_result($results,0,'sumtotal');//所有回款的之合
        if($receivedpaymentsprice>0){
            return true;
        }
        return false;
    }
    /**
     * 修改合同金额同时修改工单中的金额
     * @param $salesorderid对工应工的id
     * @param $total工单的修改的值
     * @param $fieldname工单的修改的字段
     */
    static public function setSalesordertotal($contractid,$total,$fieldname){
        $db=PearDatabase::getInstance();
        $fieldname=$fieldname=='remark'?'pending':'salescommission';
        $query="UPDATE vtiger_salesorder SET {$fieldname}=? WHERE servicecontractsid=? ";
        $db->pquery($query,array($total,$contractid));

    }

    /**
     * 工作流的成本审核节点节点当前节点是第一个节点且处于激活状态合同的成本大于0
     */
    static public function setWorkflowNode($salesorderid){
        $db=PearDatabase::getInstance();
        $query="SELECT IF(productcomboid=0,productid,productcomboid) AS productcomboid,istyunweb FROM vtiger_salesorderproductsrel WHERE salesorderid =?";
        $result=$db->pquery($query,array($salesorderid));
        $num=$db->num_rows($result);
        $isDeleteNode1=false;//需要删除回款匹配的产品节点
        $isDeleteNode2=true;//要保留的回款匹配的产品节点
        if($num){
            //T云产品全部删除2,3,5节点
            $istyunpackage=self::tCloudPackage();
            while($row=$db->fetch_array($result)){
                if(in_array($row['productcomboid'],$istyunpackage) || $row['istyunweb']==1){
                    $isDeleteNode1=true;
                }
                if(in_array($row['productcomboid'],self::$paymentMatchNode)){
                    $isDeleteNode2=false;
                }
            }
        }
        if($isDeleteNode1 && $isDeleteNode2){
            $sql='DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND sequence in(2,3,5)';
            $db->pquery($sql,array($salesorderid));
        }

        $query="SELECT
                    vtiger_salesorderworkflowstages.productid,(IFNULL(vtiger_salesorderproductsrel.costing,0)+IFNULL(vtiger_salesorderproductsrel.purchasemount,0)) AS costing,vtiger_salesorderproductsrel.productcomboid,vtiger_salesorderproductsrel.isfillincost
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_salesorderproductsrel ON (vtiger_salesorderworkflowstages.salesorderid = vtiger_salesorderproductsrel.salesorderid AND vtiger_salesorderproductsrel.productid=vtiger_salesorderworkflowstages.productid )
                WHERE
                    vtiger_salesorderworkflowstages.salesorderid = {$salesorderid}
                AND vtiger_salesorderworkflowstages.modulename='SalesOrder'
                AND vtiger_salesorderworkflowstages.isaction = 1
                AND vtiger_salesorderworkflowstages.sequence = 1";
        $result=$db->run_query_allrecords($query);
        $allsubmit=false;//标识第一个节点全部通过
        $autosubmit=false;//用来标识第一个节点中部分产品要自动审核
        $salesorderids='';
        $allsalesorderids='';
        if(!empty($result)){
            $allsubmit=true;//标识财务节点自动审核
            //$keepNode=self::keepNode();
            foreach($result as $value){
                /*if(in_array($value['productcomboid'],$keepNode)){
                    return '';
                }*/
                if($value['costing']>0 && empty($value['isfillincost'])){
                    $autosubmit=true;
                    $salesorderids.=$value['productid'].',';
                }else{
                    $allsubmit=false;
                }
                $allsalesorderids.=$value['productid'].',';
            }
        }
        if($autosubmit){
            //global $current_user;
            $salesorderids=rtrim($salesorderids,',');
            $allsalesorderids=rtrim($allsalesorderids,',');
            $datetime=date('Y-m-d H:i:s');
            //审核节点
            //$sql="UPDATE `vtiger_salesorderworkflowstages` SET auditorid=?,auditortime=?,`schedule`=100,isaction=2 WHERE vtiger_salesorderworkflowstages.salesorderid =?
            //      AND vtiger_salesorderworkflowstages.modulename='SalesOrder'";
            //删除节点
            $sqld="DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid =?
                  AND vtiger_salesorderworkflowstages.modulename='SalesOrder'";
            //$sql1=$sql." AND vtiger_salesorderworkflowstages.productid in({$salesorderids}) AND vtiger_salesorderworkflowstages.isaction=1 AND vtiger_salesorderworkflowstages.sequence=1";
            //审核节点
            //$db->pquery($sql1,array($current_user->id,$datetime,$salesorderid));
            $sql1=$sqld." AND vtiger_salesorderworkflowstages.productid in({$salesorderids}) AND vtiger_salesorderworkflowstages.isaction=1 AND vtiger_salesorderworkflowstages.sequence=1";
            //删除节点
            $db->pquery($sql1,array($salesorderid));
            //修改工单的状态有回款不足变为审核中
            $db->pquery("UPDATE vtiger_salesorder SET modulestatus=(IF(modulestatus='c_lackpayment','b_actioning',IF(modulestatus='a_normal','b_actioning',modulestatus))) WHERE vtiger_salesorder.salesorderid=?",array($salesorderid));
            if($allsalesorderids==$salesorderids){
                $date=date('Y-m-d');
                $db->pquery("UPDATE vtiger_salesorder SET performanceoftime=? WHERE vtiger_salesorder.salesorderid=?",array($date,$salesorderid));
            }
            //$sql2=$sql."  AND vtiger_salesorderworkflowstages.sequence=2";
            //财务审核节点
            //$db->pquery($sql2,array($current_user->id,$datetime,$salesorderid));
            $sql2=$sqld."  AND vtiger_salesorderworkflowstages.sequence in(2,3)";
            //删除财务节点及标准合同的提单人审核节点
            $db->pquery($sql2,array($salesorderid));

            //=====删除 财务尾款审核节点(王琨邮件：调整形式：将图中的《财务尾款审核》流程删除) gaocl add 2018/06/01========================
            $sql3=$sqld."  AND vtiger_salesorderworkflowstages.sequence=5";
            //删除财务尾款审核节点
            $db->pquery($sql3,array($salesorderid));
            //=============================================================================================================================
            if($allsubmit){
                //将审核节点下移激活节点
                $sql3="UPDATE `vtiger_salesorderworkflowstages` SET actiontime=?,isaction=1 WHERE vtiger_salesorderworkflowstages.salesorderid =?
                  AND vtiger_salesorderworkflowstages.modulename='SalesOrder'  AND vtiger_salesorderworkflowstages.productid in({$salesorderids})  AND vtiger_salesorderworkflowstages.sequence=4";
                /*$sql3="UPDATE `vtiger_salesorderworkflowstages` SET actiontime=?,isaction=1 WHERE vtiger_salesorderworkflowstages.salesorderid =?
                  AND vtiger_salesorderworkflowstages.modulename='SalesOrder' AND vtiger_salesorderworkflowstages.sequence=3";*/
                $db->pquery($sql3,array($datetime,$salesorderid));
            }
        }
    }

    /**
     * 当前工作节点是否是财务首款审核,如果是则将当前节点自动审核,不是则不做处理
     * @param $salesorderid
     */
    static public function setWorkflowNodeFirst($salesorderid,$repayment='first_payment'){
        //首款first_payment 尾款last_payment
        $db=PearDatabase::getInstance();
        $query="SELECT
                    vtiger_workflowstages.sequence,vtiger_salesorderworkflowstages.isaction
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderid = {$salesorderid}
                AND vtiger_salesorderworkflowstages.modulename = 'SalesOrder'
                AND vtiger_workflowstages.workflowstagesflag = '{$repayment}'
              AND vtiger_salesorderworkflowstages.isaction  in(0,1) limit 1";
        $result=$db->run_query_allrecords($query);//当前节点是否是首款审核节点

        if(!empty($result)){
            global $current_user;
            $num=$result[0]['sequence'];
            $datetime=date('Y-m-d H:i:s');
            $sql="UPDATE `vtiger_salesorderworkflowstages` SET auditorid=?,auditortime=?,`schedule`=100,isaction=2 WHERE vtiger_salesorderworkflowstages.salesorderid =?
                  AND vtiger_salesorderworkflowstages.modulename='SalesOrder'  AND vtiger_salesorderworkflowstages.sequence=?";

            $db->pquery($sql,array($current_user->id,$datetime,$salesorderid,$num));
            if($result[0]['isaction']==1){
                //当首款节点为当前审核状态时则将审核状态下移一个节点,当前节点不为审核状态时不做处理
                ++$num;
                //节点下移
                $sql3="UPDATE `vtiger_salesorderworkflowstages` SET actiontime=?,isaction=1 WHERE vtiger_salesorderworkflowstages.salesorderid =?
                  AND vtiger_salesorderworkflowstages.modulename='SalesOrder'   AND vtiger_salesorderworkflowstages.sequence=?";
                $db->pquery($sql3,array($datetime,$salesorderid,$num));
            }
        }

    }


    /**
     *
     * @param $arr提交的数组中是否是有要生成工单的产品只要含有一个就生成工单
     * @return bool
     */
    static public function createIsWorkflows($arr,$flag=false){
        //指定产品类型生成工单
        $tempArr=self::tCloudPackage();
        if(!$flag){
            //合同提交过来的
            if(empty($arr)) {
                return false;
            }
        }else{
            //回款提交过来的
            $db=PearDatabase::getInstance();
            $query="SELECT IF(productcomboid=0,productid,productcomboid) AS productcomboid,istyunweb FROM vtiger_salesorderproductsrel WHERE servicecontractsid =?";
            $result=$db->pquery($query,array($flag));
            $num=$db->num_rows($result);
            if($num==0)return false;
            $arr=array();
            $twebflag=false;
            for($i=0;$i<$num;$i++){
                if($db->query_result($result,$i,'istyunweb')==1){
                    $twebflag=true;
                    break;
                }
                if($db->query_result($result,$i,'productcomboid')==0)continue;
                $arr[]=$db->query_result($result,$i,'productcomboid');
            }
            if($twebflag){
                return true;
            }
            if(empty($arr))return false;

        }
        foreach ($arr as $value) {
            if (in_array($value, $tempArr)) {
                return true;
            }
        }
        return false;
    }

    /**
     * T云5个套餐
	 * 396796,396797,396798 new ids
     * @return array
     */
    static public function tCloudPackage(){
         //return array(401,374,850,929,837,396796,396797,396798,417052,417059,417060,436259,436250,436258,436247,426342,426335,426337,426340,426322,422785,483000); //add new standard product ids, by young.yang 2015-09-06
//        $return=array(631612,631769,631761,2115444,426322,426335,426337,426340,565988,787685,2113422,603314,474817,783750,830604,2115445,2116274,2122361,837,781569,781572,781575,2115457,2115461,393333,403863,430156,781577,522819,2115463,781580,781582,783753,2115459,2115460,2116276,2122366,2123633,2123636,2123638,2123496,360689,506127,506129,506141,2115819,2115477,565678,565692,565694,565696,565697,565699,565700,565701,570132,584350,2115470,2115472,2115467,2115468,2115476,506131,506134,506135,2115478,2115474,2115479,2115480,2140134,2140136,2190148,2190144,2177131,2192735,2192729,374,2200833,2226507,
//            2200833,2226485,2226488,2226492,2226496,2226499,2226501,2226503,2226504,2226506,2226507,2278672,2278612
//            );
        global $service_contracts_t_cloud_package;
        $return = $service_contracts_t_cloud_package;
        $db=PearDatabase::getInstance();
        $result=$db->pquery('SELECT productid FROM `vtiger_products` WHERE  ispackage=1 AND istyun=1',array());
        while($row=$db->fetch_array($result)){
            $return[]=$row['productid'];
        }
        return array_unique($return);
    }

    /*
     * 传入产品id 判断是哪一种产品套餐;
     * */
    static  function what_doublepush($products){
        $common = array();      //双推普及版
        $yellow_glod = array(); //双推黄金班
        $white_gold = array();  //双推白金版
        return 'common';

    }

    //wangbin 根据客户id查找当前客户购买的双推产品套餐
    static public function search_double($accountid)
    {
        global $service_contracts_search_doubles;
        $new_search_doubles = $service_contracts_search_doubles;
        $new_search_doubles = array_values($new_search_doubles);
        array_unshift($new_search_doubles, $accountid);

        $db = PearDatabase::getInstance();
        $sql = "SELECT productid FROM vtiger_servicecontracts WHERE vtiger_servicecontracts.sc_related_to = ? AND productid IN(?,?,?)";
        $result = $db->pquery($sql, $new_search_doubles);
        if ($db->num_rows($result) > 0) {
            $product = $db->fetchByAssoc($result, 0);
            $products = $product['productid'];
        }
        if (in_array($products, $service_contracts_search_doubles)) {
            return array_keys($service_contracts_search_doubles, $products);
        }
        return false;
    }
    static public  function servicecontracts_divide($contractid){
        $db=PearDatabase::getInstance();
        $sql = "SELECT *,( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_servicecontracts_divide.receivedpaymentownid = vtiger_users.id ) AS receivedpaymentownname FROM `vtiger_servicecontracts_divide` WHERE servicecontractid =?";
        $result = $db->pquery($sql,array($contractid));
        $result_li = array();
        if($db->num_rows($result)>0){
            for($i=0;$i<$db->num_rows($result);$i++){
                $result_li[] = $db->fetchByAssoc($result);
            }
        }
       return $result_li;
    }
    /**
    *
    *合同超领份数
    **/
    static public function servicecontracts_reviced($receiveid){
        $db=PearDatabase::getInstance();
        $sql = "SELECT IFNULL(sum(1),0) AS totals FROM `vtiger_servicecontracts` LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE vtiger_crmentity.smownerid=? AND vtiger_servicecontracts.modulestatus='已发放' AND vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.sideagreement=0";
        $result = $db->pquery($sql,array($receiveid));
        $num=0;
        if($db->num_rows($result)){
            $num=$db->query_result($result,0,'totals');
        }
        $query="SELECT vtiger_contractexceedingnumber.cnumber FROM `vtiger_contractexceedingnumber` WHERE userid=? limit 1";
        $result=$db->pquery($query,array($receiveid));
        $datanumber=0;
        if($db->num_rows($result)){
            $data=$db->raw_query_result_rowdata($result,0);
            $datanumber=$data['cnumber'];
        }
        $datanumber=$datanumber+self::$numberOfReceipts;
        if($num>=$datanumber){
            return $num;
        }else{
            return false;
        }
    }

    /*
    取出产品明细里面的供应商
    */
    static public function getProductVendor() {
        // 这个地方先 取出全部，后面一定要加条件
        $sql = "SELECT vtiger_vendor.vendorid,vtiger_vendor.vendorname FROM vtiger_vendor LEFT JOIN vtiger_crmentity ON vtiger_vendor.vendorid=vtiger_crmentity.crmid 
            WHERE vtiger_crmentity.deleted=0";
        $db=PearDatabase::getInstance();
        $result = $db->pquery($sql, array());
        $cn = $db->num_rows($result);
        $result_li = array();
        if($cn > 0){
            while($rawData=$db->fetch_array($result)) {
                $result_li[$rawData['vendorid']] = $rawData['vendorname'];
            }
        }
        return $result_li;
    }


    // 获取产品对应的 供应商 和 采购合同
    static public function getVendorAndSupplierByProduct($productid) {
        $vendors = array();
        $suppliercontracts = array();

        global $adb;
        $sql = " select vtiger_vendor.vendorid,vtiger_vendor.vendorname from vtiger_vendorsrebate left join vtiger_vendor ON vtiger_vendorsrebate.vendorid=vtiger_vendor.vendorid where vtiger_vendorsrebate.productid=? AND vtiger_vendorsrebate.deleted=0 ";
        $result = $adb->pquery($sql, array($productid));
        $row = $adb->num_rows($result);
        if($row > 0) {

            while($rawData=$adb->fetch_array($result)) {
                $vendors[] = $rawData;
            }
        }
        return $vendors;
    }

    // 获取采购合同 // 根据供应商获取采购合同
    static public function getSuppliercontracts($vendorid) {
        global $adb;
        $sql = "select suppliercontractsid,contract_no from vtiger_suppliercontracts where vendorid=?";
        $result = $adb->pquery($sql, array($vendorid));
        $row = $adb->num_rows($result);
        $suppliercontracts = array();
        if ($row > 0) {
            while($rawData=$adb->fetch_array($result)) {
                $suppliercontracts[] = $rawData;
            }
        }
        return $suppliercontracts;
    }

    /**
     * 验证T云产品是否为建站产品
     * @param Vtiger_Request $request
     */
    public function checkTyunCrmSiteProduct(Vtiger_Request $request){
        global $adb;
        //自定义产品
        $arr_productid=$request->get('productid');
        if(!empty($arr_productid) && !is_array($arr_productid)){
            $arr_productid=explode(',',$arr_productid);
        }
        $tyun_product_sql = "SELECT 1 FROM vtiger_products
                INNER JOIN vtiger_crmentity ON(vtiger_products.productid=vtiger_crmentity.crmid)
                WHERE vtiger_crmentity.deleted=0 AND vtiger_products.istyun=1 AND vtiger_products.istyunsite=1 AND FIND_IN_SET(vtiger_products.productid,?)";
        $tyun_products_result = $adb->pquery($tyun_product_sql, array(implode(',',$arr_productid)));
        $res_tyun_products = $adb->num_rows($tyun_products_result);
        if($res_tyun_products >0) {
            return true;
        }
        return false;
    }
    /**
     * 验证T云产品和合同是否一致
     * @param Vtiger_Request $request
     */
    public function checkTyunProductActivationCode(Vtiger_Request $request,$newxCheckFlag=true){
        $result_up = array('success'=>true,'ismodify'=>false,'msg'=>'','reason'=>'','id'=>'0','contractstatus'=>'1');
        //$contract_type=$request->get('contract_type');
        $classtype=$request->get('contractbuytype');
        $recordId=$request->get('record');

        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'ServiceContracts');
        $entity=$recordModel->entity->column_fields;
        $contract_no=$entity['contract_no'];
        $agentid=$entity['agentid'];
        $contract_classification=$entity['contract_classification'];
        if('tripcontract' == $request->get("contract_classification")){
            return $result_up;
        }

        //补充协议不做验证
        $sideagreement = $recordModel->get("sideagreement");
        if($sideagreement == '1'){
            return $result_up;
        }

        global $adb;
        //自定义产品
        $arr_productid=$request->get('productid');
        if(!empty($arr_productid) && !is_array($arr_productid)){
            $arr_productid=explode(',',$arr_productid);
        }
        //小程序产品不验证
        //830604,783750,783753,783753
        global $service_contracts_check_tyun_product_tempproductid;
        $tempproductid=array_intersect($arr_productid,$service_contracts_check_tyun_product_tempproductid);
        if(count($tempproductid)>0){
            return $result_up;
        }


        $query="SELECT * FROM vtiger_activationcode WHERE contractid=? AND `status`!=2";
        $result=$adb->pquery($query,array($recordId));
        $num_rows=$adb->num_rows($result);
        if(!$num_rows){
            return $result_up;
//            return array('success'=>false,'ismodify'=>false,'msg'=>'没有相关订单或激活码信息,请确认','reason'=>'');
        }
        $rawData=$adb->raw_query_result_rowdata($result,0);
        if($rawData['comeformtyun']==1){
            $data = $this->checkTyunActivityProductActivationCode($request,$newxCheckFlag);
            return $data;
            if($rawData['fromactivity']){
                $data = $this->checkTyunActivityProductActivationCode($request,$newxCheckFlag);
                return $data;
            }
            $productnamestemp=str_replace('&quot;','"',$rawData['productnames']);
            $productnames=json_decode($productnamestemp,true);
            if($num_rows>1){
                $query="SELECT * FROM vtiger_activationcode WHERE contractid=? AND `status`!=2 AND comeformtyun=1";
                $result=$adb->pquery($query,array($recordId));
                $parckage=0;
                $tempclasstype='buy';
                $rawData=array();
                $sumprice=0;
                $oterproductidsarray=array();
                $productnames=array();
                $oterproductids='';
                $isjoinactivity=0;
                while($row=$adb->fetch_array($result)){
                    $sumprice=bcadd($sumprice,$row['contractprice'],2);
                    if($row['productid']>0){
                        $parckage=$row['productid'];
                        $packageamount=$row['packageamount'];
                    }
                    if(in_array($row['classtype'],array('renew','crenew','upgrade','cupgrade','degrade','cdegrade'))){
                        $tempclasstype=$row['classtype'];
                    }
                    $rawData=$row;
                    $productnamestemp=str_replace('&quot;','"',$rawData['productnames']);
                    $productnamestemp=json_decode($productnamestemp,true);
                    if(!empty($productnamestemp)){
                        foreach($productnamestemp as $value){
                            if(!in_array($value["productID"],$oterproductidsarray)){
                                $oterproductidsarray[]=$value["productID"];
                                $oterproductids.=$value["productID"].',';
                                $productnames[$value["productID"]]=array("productID"=>$value["productID"],
                                    "productTitle"=>$value['productTitle'],
                                    "productCount"=>$value["productCount"],
                                    "specificationId"=>$value["specificationId"],
                                    "specificationTitle"=>$value["specificationTitle"],
                                );
                            }else{
                                $productnames[$value["productID"]]["productCount"]=$value["productCount"]+$productnames[$value["productID"]]["productCount"];
                            }
                        }
                    }
                    $agents = $row['agents'];
                    if($row['activityno']){
                        $isjoinactivity=1;
                    }
                }
                $rawData['buyseparately']=trim($oterproductids,',');
                $rawData['classtype']=$tempclasstype;
                $rawData['productid']=$parckage;
                $rawData['contractprice']=$sumprice;
                $rawData['packageamount']=$packageamount;
                $rawData['agents'] = $agents;

            }
//            if($request->get("isjoinactivity")!=$isjoinactivity){
//                return array('success'=>false,'ismodify'=>false,'msg'=>'订单参与活动情况和合同参与活动情况不一致，无法提交！','reason'=>'');
//            }
            if($request->get('sc_related_to')!=$rawData['customerid'] ){
                return array('success'=>false,'ismodify'=>false,'msg'=>'订单客户和合同客户不是同一个客户,请确认','reason'=>'');
            }
            if($contract_classification == 'tripcontract' && $agentid!=$rawData['agents']){
                return array('success'=>false,'ismodify'=>false,'msg'=>'订单代理商和合同代理商不是同一个代理商,请确认','reason'=>'');
            }
            $ajax_productidpacknum=$request->get('ajax_productidpacknum');
            if(!empty($ajax_productidpacknum) && $rawData['productid']){
                $ajax_productidpacknum1=array_map(function($v){$t=explode(':',$v);return $t[1];},$ajax_productidpacknum);
                $ajax_productidpacknumarr=array_unique($ajax_productidpacknum1);
                if(count($ajax_productidpacknumarr)!=1){
                    return array('success'=>false,'ismodify'=>false,'msg'=>'套餐的数量不一致,请重新选择,请确认','reason'=>'');
                }else{
                    //如果是院校版的验证数量是否相等
                    if($rawData['packageamount']>1&&$ajax_productidpacknumarr[0]!=$rawData['packageamount']){
                        return array('success'=>false,'ismodify'=>false,'msg'=>'套餐的数量不一致,请重新选择,请确认','reason'=>'');
                    }elseif($rawData['packageamount']<=1 && $ajax_productidpacknumarr[0]!=1){
                        return array('success'=>false,'ismodify'=>false,'msg'=>'套餐的数量不一致,请重新选择,请确认','reason'=>'');
                    }
                }
            }
            $agelife=$request->get('agelife');
            $agelifearr=array_unique($agelife);
            if(count($agelifearr)!=1){
                return array('success'=>false,'ismodify'=>false,'msg'=>'产品年限不一致,请重新选择','reason'=>'');
            }
            $currentAgeLife=current($request->get('agelife'));
            $currentAgeLife=round($currentAgeLife/12);
            if($rawData['productlife']!=$currentAgeLife){
                return array('success'=>false,'ismodify'=>false,'msg'=>'产品年限不一致,请重新选择','reason'=>'');
            }
            $total=$request->get('total');
            $total=str_replace(',','',$total);
            $total=str_replace(' ','',$total);
            if($rawData['onoffline']=='offline'){
                if($rawData['contractprice']!=$total){
                    return array('success'=>false,'ismodify'=>false,'msg'=>'合同金额与下单金额不一致!'.$rawData['contractprice'].'<=>'.$total,'reason'=>'');
                }
            }else{
                if($total!=$rawData['orderamount']){
                    return array('success'=>false,'ismodify'=>false,'msg'=>'合同金额与订单金额不一致','reason'=>'');
                }
            }
            $productid=$request->get('productid');
            $orderproductid=$rawData['productid'];
            if(!empty($productid) || !empty($orderproductid)){
                if(empty($productid) || empty($orderproductid)){
                    return array('success'=>false,'ismodify'=>false,'msg'=>'产品购买项不一致,请重新选择','reason'=>'');
                }
                $orderproductidarray=explode(',',$orderproductid);
                $newarray1=array_diff($productid,$orderproductidarray);
                $newarray2=array_diff($orderproductidarray,$productid);
                if(count($newarray1)>0 || count($newarray2)>0){
                    return array('success'=>false,'ismodify'=>false,'msg'=>'产品购买项不一致,请重新选择','reason'=>'');
                }
            }
            $servicecontractstype=$request->get('servicecontractstype');
            $servicecontractstype=$servicecontractstype=='新增'?'buy':($servicecontractstype=='续费'
            ?'renew':($servicecontractstype=='againbuy'
                    ?'buy':$servicecontractstype));
            $classtype=ltrim($rawData['classtype'],'c');
            if($servicecontractstype!=$classtype){
                return array('success'=>false,'ismodify'=>false,'msg'=>'新增/续费/升级/另购/降级类型不一致,请重新选择','reason'=>'');
            }
            $extraproductid=$request->get('extraproductid');
            $buyseparately=$rawData['buyseparately'];
            if(!empty($extraproductid) || !empty($buyseparately)){
                if(empty($extraproductid) || empty($buyseparately)){
                    return array('success'=>false,'ismodify'=>false,'msg'=>'另购产品购买项不一致,请重新选择','reason'=>'');
                }
                $buyseparatelyarray=explode(',',$buyseparately);
                $buyseparatelyarray=array_unique($buyseparatelyarray);
                $newextendarray1=array_diff($extraproductid,$buyseparatelyarray);
                $newextendarray2=array_diff($buyseparatelyarray,$extraproductid);
                if(count($newextendarray1)>0 || count($newextendarray2)>0){
                    return array('success'=>false,'ismodify'=>false,'msg'=>'另购产品购买项不一致,请重新选择','reason'=>'');
                }
                $ajax_extraproductidnum=$request->get('ajax_extraproductidnum');
                $ajax_extraproductidnumarr=array();
                foreach($ajax_extraproductidnum as $value){
                    $ajax_extraproductidnumval=explode(":",$value);
                    $ajax_extraproductidnumarr[$ajax_extraproductidnumval[0]]=$ajax_extraproductidnumval[1];
                }
                foreach($productnames as $value){
                    if($ajax_extraproductidnumarr[$value['productID']]!=$value['productCount']){
                        return array('success'=>false,'ismodify'=>false,'msg'=>$value['productTitle'].'数量不一致,合同数量为:'.$ajax_extraproductidnumarr[$value['productID']].'下单数量为'.$value['productCount'],'reason'=>'');
                    }
                }
            }
            return $result_up;
        }

        //另购产品

        $arr_extraproductid=$request->get('extraproductid');
        if(!empty($arr_extraproductid) && !is_array($arr_extraproductid)){
            $arr_extraproductid=explode(',',$arr_extraproductid);
        }

        $type = "下单";
        if($classtype == 'buy'){
            $type = "购买";
        }else if($classtype == 'renew'){
            $type = "续费";
        }else if($classtype == 'upgrade'){
            $type = "升级";
        }else if($classtype == 'againbuy'){
            $type = "另购";
        }else if($classtype == 'degrade'){
            $type = "降级";
        }else{
            return array('success'=>false,'ismodify'=>false, 'msg'=>'T云产品必须先在移动端'.$type.'才可签收本合同','reason'=>'');
        }

        //产品选择验证
        if($classtype == 'buy' || $classtype == 'upgrade' || $classtype == 'renew' || $classtype == 'degrade') {
            //验证合同信息是否和移动端购买信息一致
            if (empty($arr_productid) || count($arr_productid) == 0 || count($arr_productid) > 1) {
                return array('success'=>false,'ismodify'=>false,'msg'=>'T云'.$type.'合同必须要选择一个自定义产品,请确认','reason'=>'');
            }
        }else{
            //验证合同信息是否和移动端购买信息一致
            if (empty($arr_extraproductid) || count($arr_extraproductid) == 0) {
                return array('success'=>false, 'ismodify'=>false,'msg'=>'T云'.$type.'合同至少要选择一个另购产品,请确认','reason'=>'');
            }
        }

        //获取T云购买产品
        if($classtype != "againbuy"){
            $tyun_product_sql = "SELECT 1 FROM vtiger_products
                INNER JOIN vtiger_crmentity ON(vtiger_products.productid=vtiger_crmentity.crmid)
                WHERE vtiger_crmentity.deleted=0 AND vtiger_products.istyun=1 AND vtiger_products.istyunsite=0 AND FIND_IN_SET(vtiger_products.productid,?)";
            $tyun_products_result = $adb->pquery($tyun_product_sql, array(implode(',',$arr_productid)));
            $res_tyun_products = $adb->num_rows($tyun_products_result);
            if($res_tyun_products == 0) {
                return array('success'=>false,'ismodify'=>false, 'msg'=>'请确认选中的产品是否为T云产品','reason'=>'');
            }
        }

        //验证合同信息
        $tyun_sql = "SELECT M.activationcodeid,M.activecode AS m_activecode,
                            M.classtype AS m_classtype,
                            M.contractname AS m_contractno,
                            M.customerid AS m_customerid,
                            M.customername AS m_customername,
                            M.productlife AS m_productlife,
                            IF(M.buyserviceinfo='[]','',M.buyserviceinfo) AS m_buyserviceinfo,
                            M.contractstatus,
                            MP.productid AS m_productid,
                            MP.productname AS m_productname,
                            PP.productid AS p_productid,
                            PP.productname AS p_productname,
                            P.contractname AS p_contractname,
                            P.customerid AS p_customerid,
                            P.customername AS p_customername,
                            M.usercode,
                            M.status
                    FROM vtiger_activationcode M
                    LEFT JOIN vtiger_products MP ON(M.productid=MP.tyunproductid)
                    LEFT JOIN vtiger_activationcode P ON(M.buyid=P.activationcodeid)
                    LEFT JOIN vtiger_products PP ON(P.productid=PP.tyunproductid)
                    WHERE M.status in (0,1) AND M.contractid=? AND M.contractid>0 limit 1";
        $tyun_result = $adb->pquery($tyun_sql, array($request->get('record')));
        $res_num = $adb->num_rows($tyun_result);
        if($res_num == 0) {
            return array('success'=>false,'ismodify'=>false, 'msg'=>'T云产品必须先在移动端'.$type.'才可签收本合同','reason'=>'');
        }

        $row_data = $adb->query_result_rowdata($tyun_result,0);

        $m_activecode = $row_data['m_activecode'];
        $crm_m_productid = $row_data['m_productid'];
        $crm_p_productid = $row_data['p_productid'];
        $crm_m_productname = $row_data['m_productname'];
        $tyun_m_customid = $row_data['m_customerid'];
        $tyun_p_customid = $row_data['p_customerid'];
        $tyun_p_contractname = $row_data['p_contractname'];
        $tyun_m_classtype = $row_data['m_classtype'];
        $tyun_m_contract_no = $row_data['m_contractno'];
        $tyun_m_customname = $row_data['m_customername'];
        $tyun_p_customname = $row_data['p_customername'];
        $tyun_activationcodeid = $row_data['activationcodeid'];
        $tyun_contractstatus = $row_data['contractstatus'];
        $tyun_status = $row_data['status'];
        $usercode= $row_data['usercode'];
        //年限
        $tyun_m_productyear = $row_data['m_productlife'];
        //另购服务
        $buyserviceinfo = $row_data['m_buyserviceinfo'];

        $is_active = false;
        //验证是否领取激活码
        if($classtype == 'buy') {
            $is_active = $tyun_status=='1'?true:false;
            //新增-必须要有激活码
            if (empty($m_activecode)) {
                return array('success' => false,'ismodify'=>false, 'msg' => 'T云'.$type.'合同,产品必须领取激活码,请确认','reason'=>'');
            }
        }else{
            //新增-以外不能领取激活码
            if(!empty($m_activecode)){
                return array('success'=>false,'ismodify'=>true, 'msg'=>'T云'.$type.'合同,产品不能领取激活码,请确认','reason'=>$type.'版本不能领取激活码,请确认','id'=>$tyun_activationcodeid,"is_active"=>$is_active);
            }
            //验证是否有原合同
           /* if(empty($tyun_p_contractname)){
                return array('success'=>false,'ismodify'=>false, 'msg'=>'T云'.$type.'合同,没有查询到对应的原合同,请确认','reason'=>'');
            }*/

            //验证T云账号是否一致
            $tyun_account=$request->get('tyun_account');
            if($usercode != $tyun_account){
                return array('success'=>false,'ismodify'=>false, 'msg'=>'T云账号【'.$usercode.' | '.$tyun_account.'】和移动端'.$type.'不一致,请确认','reason'=>'');
            }
        }

        //验证合同类型是否一致
        if($tyun_m_classtype != $classtype) {
            return array('success'=>false,'ismodify'=>false, 'msg'=>'合同类型和移动端'.$type.'合同类型不一致,请确认','reason'=>'');
        }

        //验证合同编号是否一致
        if(!empty($tyun_m_contract_no) && $tyun_m_contract_no != $contract_no) {
            return array('success'=>false,'ismodify'=>true, 'msg'=>'合同编号和移动端'.$type.'合同编号不一致,请确认','reason'=>$type.'版本合同编号不一致('.$tyun_m_contract_no.'|'.$contract_no.'),请确认','id'=>$tyun_activationcodeid,"is_active"=>$is_active);
        }

        //验证产品和客户
        if($classtype == 'buy' || $classtype == 'upgrade' || $classtype == 'degrade') {
            //验证产品是否一致
            if (!in_array($crm_m_productid, $arr_productid)) {
                return array('success'=>false,'ismodify'=>false, 'msg'=> 'T云'.$type.'合同必须选择【' . $crm_m_productname . '】产品,请确认','reason'=>'');
            }
        }
        if($classtype == 'renew') {
            $tyun_sql="SELECT 
                    productid,
                    productname 
                    FROM vtiger_products 
                    WHERE parentid=(
                    SELECT 
                    MP.productid
                    FROM vtiger_activationcode M
                    LEFT JOIN vtiger_products MP ON(M.productid=MP.tyunproductid)
                    WHERE M.status IN(0,1) AND M.classtype IN('buy','upgrade','degrade') 
                    AND M.receivetime<(SELECT N.receivetime FROM vtiger_activationcode N WHERE N.status IN(0,1) AND N.contractid=?)
                    AND M.usercode=? 
                    ORDER BY M.receivetime DESC LIMIT 1)";
            $tyun_result = $adb->pquery($tyun_sql, array($recordId,$usercode));
            $res_num = $adb->num_rows($tyun_result);
            if($res_num > 0) {
                $row_data = $adb->query_result_rowdata($tyun_result,0);
                $crm_a_productid = $row_data['productid'];
                $crm_a_productname = $row_data['productname'];
                if (!in_array($crm_a_productid, $arr_productid)) {
                    return array('success'=>false,'ismodify'=>false, 'msg'=>'T云'.$type.'合同必须选择【' . $crm_a_productname . '】产品,请确认','reason'=>'');
                }
            }else{
                return array('success'=>false,'ismodify'=>false, 'msg'=>'T云'.$type.'合同选择产品必须要指定对应的原产品,请确认','reason'=>'');
            }
        }

        $accountid = $request->get('sc_related_to');
        $sc_related_to_display = $request->get('sc_related_to_display');
        if($rawData['customerid']){
            if($accountid!=$rawData['customerid']){
                return array('success'=>false,'ismodify'=>false,'msg'=>'订单客户和合同客户不是同一个客户,请确认','reason'=>'');
            }
        }else{
            //验证客户
            if($classtype == 'buy') {
                $tyun_cmp_customid = $tyun_m_customid;
                $tyun_customname = $tyun_m_customname;
            }else{
                $tyun_cmp_customid = $tyun_p_customid;
                $tyun_customname = $tyun_p_customname;
            }

            if (!empty($tyun_cmp_customid) && $accountid != $tyun_cmp_customid ) {
                return array('success'=>false,'ismodify'=>true, 'msg'=>'合同客户和移动端'.$type.'客户【' . $tyun_customname . '】不一致,请确认','reason'=>$type.'版本客户不一致('.$tyun_customname.'|'.$sc_related_to_display.'),请确认','id'=>$tyun_activationcodeid,"is_active"=>$is_active);
            }
        }

        //$checkFlag=false 以下不需要验证，在前端验证===============================================================================================
        if(!$newxCheckFlag) {
            $adb->pquery("UPDATE vtiger_activationcode SET checkstatus=0,reason='' WHERE activationcodeid=?",array($tyun_activationcodeid));
            return array('success'=>true,'ismodify'=>false,'msg'=>'','reason'=>'','id'=>$tyun_activationcodeid,'contractstatus'=>$tyun_contractstatus);
        }
        //==========================================================================================================================================

        //crm产品id
        $arr_productnumber=$request->get('ajax_productnumber');
        if(!empty($arr_productnumber) && !is_array($arr_productnumber)){
            $arr_productnumber=explode(',',$arr_productnumber);
        }
        $arr_productyear=$request->get('ajax_agelife');
        if(!empty($arr_productyear) && !is_array($arr_productyear)){
            $arr_productyear=explode(',',$arr_productyear);
        }

        //验证年限和数量
        //自定义产品验证================================================================================================
        if($classtype == 'buy' || $classtype == 'upgrade' || $classtype == 'renew' || $classtype == 'degrade') {
            foreach ($arr_productid as $v) {
                $tyun_product_rel_sql = "SELECT GROUP_CONCAT(crmid) AS c_productid FROM `vtiger_seproductsrel` WHERE productid=?";
                $tyun_products_rel_result = $adb->pquery($tyun_product_rel_sql, array($v));
                $res_num = $adb->num_rows($tyun_products_rel_result);

                if ($res_num == 0) {
                    //无明细产品
                    if(count($arr_productnumber)>0){
                        $productnumber = $this->getProductNumOrYear($arr_productnumber,$v);
                    }else{
                        $productnumber = $_REQUEST['productnumber'][$v];
                    }

                    if ($productnumber != '1') {
                        return array('success'=>false,'ismodify'=>false, 'msg'=>'T云'.$type.'合同,产品数量必须为1,请确认','reason'=>'');
                    }
                } else {
                    //有明细产品
                    $data_row = $adb->query_result_rowdata($tyun_products_rel_result, 0);
                    $c_productids = $data_row["c_productid"];

                    if(empty($c_productids)){
                        //无明细产品
                        if(count($arr_productnumber)>0){
                            $productnumber = $this->getProductNumOrYear($arr_productnumber,$v);
                        }else {
                            $productnumber = $_REQUEST['productnumber'][$v];
                        }

                        if ($productnumber != '1') {
                            return array('success'=>false,'ismodify'=>false, 'msg'=>'T云'.$type.'合同,产品数量必须为1,请确认','reason'=>'');
                        }

                        if(count($arr_productyear)>0){
                            $productyear = $this->getProductNumOrYear($arr_productyear,$v);
                        }else {
                            $productyear = $_REQUEST['agelife'][$v];
                        }

                        //年限
                        $productyear = round($productyear / 12, 2);//年限
                        if ($tyun_m_productyear != $productyear) {
                            return array('success'=>false,'ismodify'=>true, 'msg'=>'产品年限和移动端'.$type.'不一致,请确认','reason'=>$type.'版本产品年限不一致('.$tyun_m_productyear.'|'.$productyear.'),请确认','id'=>$tyun_activationcodeid,"is_active"=>$is_active);
                        }
                    }else{
                        $arr_c_productid = explode(',', $c_productids);
                        foreach ($arr_c_productid as $v1) {
                            if(count($arr_productnumber)>0){
                                $productnumber = $this->getProductNumOrYear($arr_productnumber,$v1);
                            }else {
                                $productnumber = $_REQUEST['productnumber'][$v1];
                            }

                            if(count($arr_productyear)>0){
                                $productyear = $this->getProductNumOrYear($arr_productyear,$v1);
                            }else {
                                $productyear = $_REQUEST['agelife'][$v1];
                            }

                            //数量
                            if ($productnumber != '1') {
                                return array('success'=>false,'ismodify'=>false, 'msg'=> 'T云'.$type.'合同,产品数量必须为1,请确认','reason'=>'');
                            }
                            //年限
                            $productyear = round($productyear / 12, 2);//年限
                            if ($tyun_m_productyear != $productyear) {
                                return array('success'=>false,'ismodify'=>true, 'msg'=>'产品年限和移动端'.$type.'不一致,请确认','reason'=>$type.'版本产品年限不一致('.$tyun_m_productyear.'|'.$productyear.'),请确认','id'=>$tyun_activationcodeid,"is_active"=>$is_active);
                            }
                        }
                    }
                }
            }
        }

        //验证另购产品验证==============================================================================================
        $this->_logs(array("T云另购服务:".$buyserviceinfo));
        $this->_logs(array("CRM另购服务:".$arr_extraproductid));
        if(!empty($buyserviceinfo) && (empty($arr_extraproductid) || count($arr_extraproductid) == 0)) {
            return array('success'=>false,'ismodify'=>false, 'msg'=>'T云'.$type.'合同,必须要选择另购产品,请确认','reason'=>'');
        }
        if(empty($buyserviceinfo)  && !empty($arr_extraproductid)) {
            return array('success'=>false,'ismodify'=>false, 'msg'=>'T云'.$type.'合同,另购产品和移动端不一致,请确认','reason'=>'');
        }

        if(!empty($buyserviceinfo) && count($arr_extraproductid) > 0) {
            $buyserviceinfo = htmlspecialchars_decode($buyserviceinfo);
            $arr_serviceinfo = json_decode($buyserviceinfo,true);
            if(count($arr_serviceinfo) != count($arr_extraproductid)){
                return array('success'=>false,'ismodify'=>true, 'msg'=>'另购产品和移动端'.$type.'数量不一致,请确认','reason'=>$type.'版本另购服务个数不一致('.count($arr_serviceinfo).'个|'.count($arr_extraproductid).'个),请确认','id'=>$tyun_activationcodeid,"is_active"=>$is_active);
            }

            //查询另购产品匹配的T云产品ID
            $tyun_product_sql = "SELECT GROUP_CONCAT(vtiger_products.tyunproductid) AS tyunproductid FROM vtiger_products
                            INNER JOIN vtiger_crmentity ON(vtiger_products.productid=vtiger_crmentity.crmid)
                            WHERE vtiger_crmentity.deleted=0 AND vtiger_products.istyun=1 AND vtiger_products.istyunsite=0 AND FIND_IN_SET(vtiger_products.productid,?)";
            $tyun_products_result = $adb->pquery($tyun_product_sql, array(implode(',',$arr_extraproductid)));
            $res_tyun_products = $adb->num_rows($tyun_products_result);
            if($res_tyun_products == 0) {
                //验证产品id是否和T云匹配
                return array('success'=>false, 'ismodify'=>false,'msg'=>'T云'.$type.'合同,产品和T云产品未匹配,请确认','reason'=>'');
            }

            $data_row = $adb->query_result_rowdata($tyun_products_result,0);
            $tyunproductids = $data_row["tyunproductid"];
            if(empty($tyunproductids)){
                //验证产品id是否和T云匹配
                return array('success'=>false, 'ismodify'=>false,'msg'=>'T云'.$type.'合同,产品和T云产品未匹配,请确认','reason'=>'');
            }

            $arr_tyunproductid = explode(',',$tyunproductids);
            $new_arr_tyunproductid = array();
            foreach($arr_tyunproductid as $v1){
                if(!empty($v1)){
                    $new_arr_tyunproductid[] = $v1;
                }
            }
            //查询到的关联产品数量是否一致(处理未匹配T云产品ID情况)
            if(count($new_arr_tyunproductid) != count($arr_extraproductid)){
                return array('success'=>false,'ismodify'=>false, 'msg'=>'T云'.$type.'合同,存在和T云未匹配的产品,请确认','reason'=>'');
            }

            //验证产品是否一致
            foreach($new_arr_tyunproductid as $v1) {
                $bl_check = false;
                for($a=0;$a<count($arr_serviceinfo);$a++){
                    $serviceID = $arr_serviceinfo[$a]['ServiceID'];
                    if($serviceID == $v1){
                        $bl_check = true;
                        break;
                    }
                }
                if(!$bl_check){
                    return array('success'=>false, 'ismodify'=>true,'msg'=>'选择产品和移动端'.$type.'不一致,请确认','reason'=>'存在和客户端不一致的产品,请确认',"is_active"=>$is_active);
                }
            }

            //验证另购数量
            $recordActivationCodeModel = Vtiger_Record_Model::getCleanInstance('ActivationCode');
            $arr_tyunserviceitem = $recordActivationCodeModel->getTyunServiceItem(new Vtiger_Request());
            $this->_logs(array("接口获取另购服务:".$arr_tyunserviceitem));
            $this->_logs(array("TYUN另购产品:".$new_arr_tyunproductid));
            $this->_logs(array("CRM另购产品:".$arr_productnumber));
            foreach($new_arr_tyunproductid as $tv1) {
                $tyun_product_sql = "SELECT productid FROM vtiger_products
                            WHERE vtiger_products.istyun=1 AND vtiger_products.istyunsite=0 AND tyunproductid=? LIMIT 1";
                $tyun_products_result = $adb->pquery($tyun_product_sql, array($tv1));
                $data_row = $adb->query_result_rowdata($tyun_products_result,0);

                $cv1 = $data_row['productid'];
                if(empty($cv1)){
                    //验证产品id是否和T云匹配
                    return array('success'=>false, 'ismodify'=>false,'msg'=>'T云'.$type.'合同,产品和T云产品未匹配,请确认','reason'=>'');
                }
                if(count($arr_productnumber)>0){
                    $productnumber = $this->getProductNumOrYear($arr_productnumber,$cv1);
                }else {
                    $productnumber = $_REQUEST['productnumber'][$cv1];
                }
                for($a=0;$a<count($arr_serviceinfo);$a++){
                    $serviceID = $arr_serviceinfo[$a]['ServiceID'];
                    $buyCount = $arr_serviceinfo[$a]['BuyCount'];

                    //查询另购、除以倍数比较
                    /*for($b=0;$b<count($arr_tyunserviceitem);$b++){
                        $serviceID_tmp = $arr_tyunserviceitem[$b]['ServiceID'];
                        $multiple = $arr_tyunserviceitem[$b]['Multiple'];
                        if($serviceID == $serviceID_tmp){
                            $buyCount = bcdiv($arr_serviceinfo[$a]['BuyCount'],$multiple);
                            $this->_logs(array("合同签收另购服务ID:".$serviceID.",转换后数量:".$buyCount));
                            break;
                        }
                    }*/

                    if($serviceID == $tv1){
                        if($productnumber != $buyCount){
                            return array('success'=>false,'ismodify'=>true, 'msg'=>'另购产品数量和移动端'.$type.'不一致,请确认','reason'=>$type.'版本另购数量不一致('.$buyCount.'|'.$productnumber.'),请确认','id'=>$tyun_activationcodeid,"is_active"=>$is_active);
                        }
                        break;
                    }
                }
            }
        }
        return array('success'=>true,'ismodify'=>false,'msg'=>'','reason'=>'','id'=>$tyun_activationcodeid,'contractstatus'=>$tyun_contractstatus);
    }

    /**
     * 更新合同拒收原因
     * @param $arr_data
     */
    public function updateRejectionReason($arr_data){
        if($arr_data && !empty($arr_data['id'])){
            global $adb;
            $adb->pquery("UPDATE vtiger_activationcode SET checkstatus=1,reason=? WHERE contractstatus=0 AND activationcodeid=?", array($arr_data['reason'],$arr_data['id']));
        }
    }
    private function getProductNumOrYear($arr,$productid){
        foreach ($arr as $val) {
            $arr_v =explode(':',$val);
            if($arr_v[0] == $productid){
                 return $arr_v[1];
            }
        }
        return "";
    }
    /**
     * 验证产品,年限,客户是否一致
     * @param Vtiger_Request $request
     * @return bool
     *
     */
    public function checkTyunProductAndyear(Vtiger_Request $request){
        //426335:T云系列V1（首购）
        //426337:T云系列V2（首购）
        //565988:T云系列V3P（首购）
        //426340:T云系列V3（首购）
        //426342:T-云2.0V系列V5（首购）-作废
        //566004:T-云2.0V系列V6电商版（首购）
        //474817:T云系列V8（首购）
        //426322:T云系列V（首购）
        //837:T-云2.0V系列发布宝（首购）
        //631769:T云系列S1 Plus（首购）
        //631612:T云系列S1（首购）
        //631761:T云系列S2（首购）
        //2115444:T云系列S3小程序建站（首购）
        //787685:T云系列V5（首购）
        //2113422:T云系列V5P（首购）
        //603314:T云系列V6（首购）

        $servicecontractstype=$request->get('servicecontractstype');//是否是续费
        //$array=array(426335,426337,565988,426340,474817,426322,837,631769,631612,631761,2115444,787685,2113422,603314);
//        $array=array(426335,426337,565988,426340,426342,566004,474817,426322,837,631769,631612,631761,2115444,787685,2113422,603314,2140134);
        global $service_contracts_check_tyun_product_and_year;
        $array = $service_contracts_check_tyun_product_and_year;
        $productid=$request->get('productid');
        $flag=false;
        if(!is_array($productid)){
            $productid=explode(',',$productid);
        }
        foreach($productid as $vproduct){
            if(in_array($vproduct,$array)){
                $flag=true;
            }
        }

        if($flag)
        {
            global $adb;
            //版本对应表
            /*$vSerieArr = array(
                'T云系列S1（首购）' => '512cb5c8-7609-11e7-a335-5254003c6d38',
                'T云系列S2（首购）' => '512cb609-7609-11e7-a335-5254003c6d38',
                'T云系列S1 Plus（首购）' => '512cb5e6-7609-11e7-a335-5254003c6d38',
                'T云系列V1（首购）' => 'fafdc07c-4296-11e6-ad98-00155d069461',
                'T云系列V2（首购）' => 'fb016797-4296-11e6-ad98-00155d069461',
                'T云系列V3P（首购）' => 'eb472d25-f1b1-11e6-a335-5254003c6d38',
                'T云系列V3（首购）' => 'fb016866-4296-11e6-ad98-00155d069461',
                'T云系列V5（首购）' => 'fb0174bf-4296-11e6-ad98-00155d069461',
                'T云系列V5P（首购）' => 'b96c4ad7-27f3-4526-ab43-609d8dbd1170',
                'T云系列V6（首购）' => 'ad0bee9e-516f-11e6-a2ff-52540013dadb',
                'T云系列V8（首购）' => 'eb480f94-f1b1-11e6-a335-5254003c6d38',
                'T云系列V（首购）' => 'fb01732e-4296-11e6-ad98-00155d069461',
                'T云系列发布宝(首购)' => 'a36a9cac-516f-11e6-a2ff-52540013dadb',
                'T云系列S3小程序建站（首购）' => 'da1832bc-bc86-459f-a14c-285b2f69e1d3',
				'T云系列旗舰版' => '0fea4ea4-78e3-438b-9b4f-1792f60bea06',
            );*/
            $vSerieArr = array(
                '631612' => '512cb5c8-7609-11e7-a335-5254003c6d38',
                '631761' => '512cb609-7609-11e7-a335-5254003c6d38',
                '631769' => '512cb5e6-7609-11e7-a335-5254003c6d38',
                '426335' => 'fafdc07c-4296-11e6-ad98-00155d069461',
                '426337' => 'fb016797-4296-11e6-ad98-00155d069461',
                '565988' => 'eb472d25-f1b1-11e6-a335-5254003c6d38',
                '426340' => 'fb016866-4296-11e6-ad98-00155d069461',
                '787685' => 'fb0174bf-4296-11e6-ad98-00155d069461',
                '2113422' => 'b96c4ad7-27f3-4526-ab43-609d8dbd1170',
                '603314' => 'ad0bee9e-516f-11e6-a2ff-52540013dadb',
                '474817' => 'eb480f94-f1b1-11e6-a335-5254003c6d38',
                '426322' => 'fb01732e-4296-11e6-ad98-00155d069461',
                '837' => 'a36a9cac-516f-11e6-a2ff-52540013dadb',
                '2115444' => 'da1832bc-bc86-459f-a14c-285b2f69e1d3',
                '2140134' => '0fea4ea4-78e3-438b-9b4f-1792f60bea06',
            );
            $thepackageArr = $request->get('productcomboid');
            $accountid = $request->get('sc_related_to');
            $agelifeArr = $request->get('agelife');
            if(!empty($thepackageArr) && !empty($agelifeArr)){
                $thepackage = '';//产品id
                if($thepackageArr){
                    foreach($thepackageArr as $v){
                        if($v == '--'){
                            continue;
                        }
                        $thepackage = $v;
                        break;
                    }
                }

                $agelife = 0;
                if($agelifeArr){
                    $arrkey=array();
                    foreach($agelifeArr as $v)
                    {
                        ++$arrkey[$v]['key'];
                        $arrkey[$v]['value']=$v;
                    }
                    $key=0;
                    //求出现次数最多的做为年限
                    foreach($arrkey as $arrvalue)
                    {
                        if($arrvalue['key']>$key)
                        {
                            $key=$arrvalue['key'];
                            $agelife = $arrvalue['value'];
                        }
                    }
                }

                $thepackage = $vSerieArr[$thepackage];//产品id
                $agelife = round($agelife/12, 2);//年限
                if($thepackage && $agelife){
                    /*$sql = "SELECT * FROM `vtiger_activationcode` WHERE status in (0,1) AND contractid=(SELECT servicecontractsid FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.contract_no=? LIMIT 1) limit 1";//根据合同编号查询激活码信息
                    $activationcode_result = $adb->pquery($sql, array($request->get('contract_no')));*/
                    $sql = "SELECT * FROM `vtiger_activationcode` WHERE status in (0,1) AND contractid=? AND contractid>0 limit 1";//根据合同编号查询激活码信息
                    $activationcode_result = $adb->pquery($sql, array($request->get('record')));
                    $res_activationcode = $adb->num_rows($activationcode_result);
                    if($res_activationcode > 0){
                        if($servicecontractstype == 'upgrade'){
                            //升级合同
                            return 2;
                        }
                        $row_activationcode = $adb->query_result_rowdata($activationcode_result, 0);
                        if(($row_activationcode['productid'] == $thepackage) && ($row_activationcode['productlife'] == $agelife) && $row_activationcode['customerid']==$accountid && $accountid>0){
                            return 0;
                        }
                    }
                }
            }
            //$adb->pquery("UPDATE vtiger_servicecontracts_print SET constractsstatus='c_receive',recoveredtime=NULL,recoveredid=NULL,dorecoveredid=NULL WHERE servicecontracts_no=?",array($request->get('contract_no')));
            //$adb->pquery("UPDATE vtiger_servicecontracts,vtiger_crmentity SET vtiger_servicecontracts.modulestatus ='已发放' WHERE vtiger_servicecontracts.servicecontractsid=vtiger_crmentity.crmid AND vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.contract_no=?",array($request->get('contract_no')));
            if($servicecontractstype != 'upgrade') {
                return 1;
            }
        }
        return 0;
    }
    /**
     * 是否是T云标准产品
     * @param $checkedproductid
     * @return bool
     */
    public function IsTyunProduct($checkedproductid){
        //$array=array(426335,426337,565988,426340,426342,566004,474817,426322,837,631769,631612,631761);
//        $array=array(426335,426337,565988,426340,426342,566004,474817,426322,837,631769,631612,631761,2116274,787685,2113422,603314,2116274,2122361,2140134,2115444);
        global $service_contracts_is_tyun_product;
        $array = $service_contracts_is_tyun_product;
        $productidflag=false;
        foreach($checkedproductid as $valueproductid){
            if(in_array($valueproductid,$array)){
                $productidflag=true;
                break;
            }
        }
        return $productidflag;
    }
    /**
     * 根据合同的ID判断当前登陆人是否是创建人
     * @param $recordid
     * @return boolean
     */
    public function checkCreator($recordId){
        global $current_user;
        $query='select 1 from vtiger_servicecontracts WHERE creatorid=? AND servicecontractsid=?';
        $db=PearDatabase::getInstance();
        $dataResult=$db->pquery($query,array($current_user->id,$recordId));
        if($db->num_rows($dataResult))
        {
            return true;
        }
        return false;
    }
    /**
     * 标准产品工作流非T云产品是否要重新重新激活
     * @param $salesorderid工单的ID
     * @param $servicecontractsid合同的ID
     */
    public static function noStandardToRestart($salesorderid,$servicecontractsid){

        $recordModel=Vtiger_Record_Model::getInstanceById($salesorderid,"SalesOrder");
        $entity=$recordModel->entity;
        $column_fields=$entity->column_fields;
        //标准产品
        if(self::selectWorkfows()==$column_fields['workflowsid']){
            //状态为回款不足
            if($column_fields["modulestatus"]=='c_lackpayment'){
                //不是T云产品
                if(!self::createIsWorkflows('',$servicecontractsid)){
                    //回款+担保金是否大于成本
                    if(self::receiveDayprice($servicecontractsid,$salesorderid,false)){
                        $db=PearDatabase::getInstance();
                        $db->pquery("UPDATE vtiger_salesorder SET modulestatus='b_actioning' WHERE modulestatus='c_lackpayment' AND vtiger_salesorder.salesorderid=?",array($salesorderid));
                        $db->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=1 WHERE salesorderid=? AND isaction=0 AND sequence =(select sequence FROM (SELECT sequence FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND isaction=0 ORDER BY sequence LIMIT 1) t)",array($salesorderid,$salesorderid));
                    }

                }
            }

        }
    }
	/**
     * 客户升级
     */
    public function accountUpgrade(){
        $Entity=$this->getEntity();
        $column_fields=$Entity->column_fields;
        $accountid=$column_fields['sc_related_to'];
        Accounts_Record_Model::updateAccountsDealtime($accountid);

    }

    /**
     * 移动CRM建站合同推送到T云
     * @param $contract_no 合同编号
     * @param $accountid 客户id
     * @param $productids 自定义产品
     * @param $extraproductids 额外产品
     * @return mixed
     */
    public function uploadCrmSiteContractToTyun($contract_no,$accountid,$productids,$extraproductids){
        global $adb;
        $result_up['success']= true;
        $result_up['message']= '';

        $sql = "SELECT vtiger_tyunstationsale.*,vtiger_account.accountname AS accountname FROM vtiger_tyunstationsale
                LEFT JOIN vtiger_account ON(vtiger_tyunstationsale.accountid=vtiger_account.accountid)
                WHERE vtiger_tyunstationsale.stationsalestatus=0 AND vtiger_tyunstationsale.contractcode=?";
        $result_stationsale = $adb->pquery($sql, array($contract_no));
        $res_num = $adb->num_rows($result_stationsale);
        if($res_num > 0){
            $myData = $adb->query_result_rowdata($result_stationsale, 0);
            $pushstatus = $myData["pushstatus"];
            //如果已经推送过就不需要再推送
            if($pushstatus == '1'){
                return $result_up;
            }

            $stationsaleid = $myData["stationsaleid"];
            //$servicetype = $myData["servicetype"];
            $servicecount = $myData["servicecount"];
            //$buyyear = $myData["serviceyear"];
            //验证客户是否变更
            if($myData["companyname"] != $myData["accountname"]
                || $accountid != $myData["accountid"]){
                $result_up['success']= false;
                $result_up['message']= '建站服务购买客户和合同客户不一致,请确认';
                return $result_up;
            }

            //是否选择额外产品(建站购买服务不能选择额外产品)
            if($extraproductids != "" && count($extraproductids)>0){
                $result_up['success']= false;
                $result_up['message']= '建站服务购买合同不能选择额外产品,请确认';
                return $result_up;
            }
            //验证产品是否一致
            if($productids == "" || count($productids) == 0){
                $result_up['success']= false;
                $result_up['message']= '建站服务购买合同至少要选择一个自定义产品,请确认';
                return $result_up;
            }else{
                /*if(count($productids) > 1){
                    $result_up['success']= false;
                    $result_up['message']= '建站服务购买合同只能选择一个产品,请确认';
                    return $result_up;
                }*/

                //$serviceinfo = str_replace('&quot;','"',$myData['serviceinfo']);
                $serviceinfo = htmlspecialchars_decode($myData['serviceinfo']);
                $arr_serviceinfo = json_decode($serviceinfo,true);
                $serviceyear = 0;
                $buy_products = array();
                for($a=0;$a<count($arr_serviceinfo);$a++){
                    $count = $arr_serviceinfo[$a]['count'];
                    $servicetype = $arr_serviceinfo[$a]['servicetype'];
                    $year = $arr_serviceinfo[$a]['year'];
                    $serviceyear += $count;
                    if($count > 0){
                        if($servicetype == 1){
                            //2115445:T云系列小程序标准建站（首购）-->小程序建站->云建站3.0小程序标准建站
                            //2115460:T云系列小程序标准建站（续费）-->小程序建站
                            //========预发布测试id和线上id====================================
                            $productid1 = "2115445";
                            $productid2 = "2115460";

                            //=======本地测试id=====================================
                           // $productid1 = "861083";
                           // $productid2 = "861084";

                            $buy_products[] = $productid1;
                            $buy_products[] = $productid2;

                            if(!in_array($productid1, $productids) && !in_array($productid2, $productids)){
                                $result_up['success']= false;
                                $result_up['message']= '建站服务购买合同必须选择【云建站3.0微信小程序标准建站】的产品,请确认';
                                return $result_up;
                            }

                            //验证数量和年限
                           if(in_array($productid1, $productids)){
                                if(in_array($productid1, $productids)){
									$check_productid1 = "2122766";
                                    $productnumber = $_REQUEST['productnumber'][$check_productid1];
                                    $agelife = $_REQUEST['agelife'][$check_productid1]/12;
                                    /*$productnumber = (empty($_REQUEST['productnumber'][$check_productid1])?$_REQUEST['productnumber'][$productid1]:$_REQUEST['productnumber'][$check_productid1]);
                                    $agelife = (empty($_REQUEST['agelife'][$check_productid1])?$_REQUEST['agelife'][$productid1]:$_REQUEST['agelife'][$check_productid1])/12;*/
                                    if($count != $productnumber || $year != $agelife){
                                        $result_up['success']= false;
                                        $result_up['message']= '云建站3.0微信小程序标准建站购买时填写的数量或年限和合同设置的数量或年限不一致,请确认';
                                        return $result_up;
                                    }
                                }
                            }else{
                                if(in_array($productid2, $productids)){
                                    $productnumber = $_REQUEST['productnumber'][$productid2];
                                    $agelife = $_REQUEST['agelife'][$productid2]/12;
                                    if($count != $productnumber || $year != $agelife){
                                        $result_up['success']= false;
                                        $result_up['message']= '云建站3.0微信小程序标准建站购买时填写的数量或年限和合同设置的数量或年限不一致,请确认';
                                        return $result_up;
                                    }
                                }
                            }

                        }else if($servicetype == 2){
                            //2116274:T云系列PC标准建站（首购）->云建站3.0PC标准建站
                            //2116276:T云系列PC标准建站（续费）
                            //========线上id====================================
                            $productid1 = "2116274";
                            $productid2 = "2116276";

                            //=======本地测试id=====================================
                            //$productid1 = "861085";
                            //$productid2 = "861086";

                            $buy_products[] = $productid1;
                            $buy_products[] = $productid2;

                            if(!in_array($productid1, $productids) && !in_array($productid2, $productids)){
                                $result_up['success']= false;
                                $result_up['message']= '建站服务购买合同必须选择【云建站3.0PC标准建站】的产品,请确认';
                                return $result_up;
                            }

                            //验证数量和年限
                            if(in_array($productid1, $productids)){
                                if(in_array($productid1, $productids)){
                                    //todo
                                    //临时解决云建站前后的bug start
                                    $agelife2 = $_REQUEST['agelife'];
                                    $dataagelife = array();
                                    $dataproductnumber = array();
                                    foreach ($agelife2 as $key=>$age){
                                        $otheragelife = explode('DZE',$key);
                                        $dataagelife[$otheragelife[0]] = $age;
                                        $dataagelife[$otheragelife[1]] = $age;
                                    }
                                    $productnumber2 = $_REQUEST['productnumber'];
                                    foreach ($productnumber2 as $key=>$number){
                                        $othernumber = explode('DZE',$key);
                                        $dataproductnumber[$othernumber[0]] = $number;
                                        $dataproductnumber[$othernumber[1]] = $number;
                                    }


									$check_productid1 = "2122721";
                                    $productnumber = $productnumber2[$check_productid1];
                                    $agelife = $agelife2[$check_productid1]/12;
                                    //临时解决云建站前后的bug end
//                                    $productnumber = $_REQUEST['productnumber'][$check_productid1];
//                                    $agelife = $_REQUEST['agelife'][$check_productid1]/12;
                                    if($count != $productnumber || $year != $agelife){
                                        $result_up['success']= false;
                                        $result_up['message']= '云建站3.0PC标准建站购买时填写的数量或年限和合同设置的数量或年限不一致,请确认';
                                        return $result_up;
                                    }
                                }
                            }else{
                                if(in_array($productid2, $productids)){
                                    //todo
                                    //临时解决云建站前后的bug
                                    $agelife2 = $_REQUEST['agelife'];
                                    $dataagelife = array();
                                    $dataproductnumber = array();
                                    foreach ($agelife2 as $key=>$age){
                                        $otheragelife = explode('DZE',$key);
                                        $dataagelife[$otheragelife[0]] = $age;
                                        $dataagelife[$otheragelife[1]] = $age;
                                    }
                                    $productnumber2 = $_REQUEST['productnumber'];
                                    foreach ($productnumber2 as $key=>$number){
                                        $othernumber = explode('DZE',$key);
                                        $dataproductnumber[$othernumber[0]] = $number;
                                        $dataproductnumber[$othernumber[1]] = $number;
                                    }

                                    $productnumber = $dataproductnumber[$productid2];
                                    $agelife = $dataagelife[$productid2]/12;
                                    //临时解决云建站前后的bug end

//                                    $productnumber = $_REQUEST['productnumber'][$productid2];
//                                    $agelife = $_REQUEST['agelife'][$productid2]/12;
                                    if($count != $productnumber || $year != $agelife){
                                        $result_up['success']= false;
                                        $result_up['message']= '云建站3.0PC标准建站购买时填写的数量或年限和合同设置的数量或年限不一致,请确认';
                                        return $result_up;
                                    }
                                }
                            }
                        }else if($servicetype == 3){
                            //移动端建站
                            //2122361:T云系列移动标准建站（首购）->云建站3.0移动标准建站
                            //2122366:T云系列移动标准建站（续费）
                            //========线上id====================================
                            $productid1 = "2122361";
                            $productid2 = "2122366";

                            //=======本地测试id=====================================
                            //$productid1 = "2120458";
                            //$productid2 = "2120459";

                            //=======预发布测试id=====================================
                            //$productid1 = "2116541";
                            //$productid2 = "2116542";

                            $buy_products[] = $productid1;
                            $buy_products[] = $productid2;

                            if(!in_array($productid1, $productids) && !in_array($productid2, $productids)){
                                $result_up['success']= false;
                                $result_up['message']= '建站服务购买合同必须选择【云建站3.0移动标准建站】的产品,请确认';
                                return $result_up;
                            }

                            //验证数量和年限
                           if(in_array($productid1, $productids)){
                                if(in_array($productid1, $productids)){
									$check_productid1 = "2122723";
                                    $productnumber = $_REQUEST['productnumber'][$check_productid1];
                                    $agelife = $_REQUEST['agelife'][$check_productid1]/12;
                                    if($count != $productnumber || $year != $agelife){
                                        $result_up['success']= false;
                                        $result_up['message']= '云建站3.0移动标准建站购买时填写的数量或年限和合同设置的数量或年限不一致,请确认';
                                        return $result_up;
                                    }
                                }
                            }else{
                                if(in_array($productid2, $productids)){
                                    $productnumber = $_REQUEST['productnumber'][$productid2];
                                    $agelife = $_REQUEST['agelife'][$productid2]/12;
                                    if($count != $productnumber || $year != $agelife){
                                        $result_up['success']= false;
                                        $result_up['message']= '云建站3.0移动标准建站购买时填写的数量或年限和合同设置的数量或年限不一致,请确认';
                                        return $result_up;
                                    }
                                }
                            }
                        }else if($servicetype == 5){
                            //2271586:T云系列PC标准建站（首购）->云建站3.0百度小程序标准建站
                            //2271588:云建站3.0百度小程序标准建站（续费）
                            //========线上id====================================
							$productid1 = "2271586";
                            $productid2 = "2271588";


                            //=======本地测试id=====================================
                            //$productid1 = "861085";
                            //$productid2 = "861086";

                            $buy_products[] = $productid1;
                            $buy_products[] = $productid2;

                            if(!in_array($productid1, $productids) && !in_array($productid2, $productids)){
                                $result_up['success']= false;
                                $result_up['message']= '建站服务购买合同必须选择【云建站3.0百度小程序标准建站】的产品,请确认';
                                return $result_up;
                            }

                            //验证数量和年限
                            if(in_array($productid1, $productids)){
                                if(in_array($productid1, $productids)){
                                    $check_productid1 = "2271583";
                                    $productnumber = $_REQUEST['productnumber'][$check_productid1];
                                    $agelife = $_REQUEST['agelife'][$check_productid1]/12;
                                    if($count != $productnumber || $year != $agelife){
                                        $result_up['success']= false;
                                        $result_up['message']= '云建站3.0百度小程序标准建站购买时填写的数量或年限和合同设置的数量或年限不一致,请确认';
                                        return $result_up;
                                    }
                                }
                            }else{
                                if(in_array($productid2, $productids)){
                                    $productnumber = $_REQUEST['productnumber'][$productid2];
                                    $agelife = $_REQUEST['agelife'][$productid2]/12;
                                    if($count != $productnumber || $year != $agelife){
                                        $result_up['success']= false;
                                        $result_up['message']= '云建站3.0百度小程序标准建站购买时填写的数量或年限和合同设置的数量或年限不一致,请确认';
                                        return $result_up;
                                    }
                                }
                            }
                        }else if($servicetype == 6){
                            //========线上id====================================
                            $productid1 = "2317553";
                            $productid2 = "2317558";
                            $buy_products[] = $productid1;
                            $buy_products[] = $productid2;

                            if(!in_array($productid1, $productids) && !in_array($productid2, $productids)){
                                $result_up['success']= false;
                                $result_up['message']= '建站服务购买合同必须选择【T云建站独立IP】的产品,请确认';
                                return $result_up;
                            }
                            //验证数量和年限
                            if(in_array($productid1, $productids)){
                                if(in_array($productid1, $productids)){
                                    $check_productid1 = "2317551";
                                    $productnumber = $_REQUEST['productnumber'][$check_productid1];
                                    $agelife = $_REQUEST['agelife'][$check_productid1]/12;
                                    if($count != $productnumber || $year != $agelife){
                                        $result_up['success']= false;
                                        $result_up['message']= 'T云建站独立IP购买时填写的数量或年限和合同设置的数量或年限不一致,请确认';
                                        return $result_up;
                                    }
                                }
                            }else{
                                if(in_array($productid2, $productids)){
                                    $productnumber = $_REQUEST['productnumber'][$productid2];
                                    $agelife = $_REQUEST['agelife'][$productid2]/12;
                                    if($count != $productnumber || $year != $agelife){
                                        $result_up['success']= false;
                                        $result_up['message']= 'T云建站独立IP购买时填写的数量或年限和合同设置的数量或年限不一致,请确认';
                                        return $result_up;
                                    }
                                }
                            }
                        }else{
                            $result_up['success']= false;
                            $result_up['message']= '建站服务购买合同存在无效的服务类型(1云建站3.0微信小程序标准建站,2云建站3.0PC标准建站，3云建站3.0移动标准建站,5云建站3.0百度小程序标准建站,6T云建站独立IP),请确认';
                            return $result_up;
                        }

                    }
                }
                //判断产品数量和年限是否一致
                //print_r($buy_products);die();
                for($a=0;$a<count($productids);$a++){
                    if(!in_array($productids[$a],$buy_products)){
                        $result_up['success']= false;
                        $result_up['message']= '建站服务购买合同所选产品和购买产品不一致,请确认';
                        return $result_up;
                    }
                }
            }
            if($myData["classtype"]=='renew'){
                $up_sql = "UPDATE vtiger_tyunstationsale SET pushstatus=1,updatetime=NOW() WHERE stationsaleid=?";
                $adb->pquery($up_sql, array($stationsaleid));
                $result_up["success"] == true;
                $result_up['message']='';
                return $result_up;
            }
            $arr_custphonecode = $this->getMobileVerifyOther($myData["custphone"]);
            if(!$arr_custphonecode["success"]){
                return $arr_custphonecode;
            }

            //1.'ContractCode'=>'合同编号',
            $tyun_data["ContractCode"]=$myData["contractcode"];
            //2. 'CompanyName'=>'客户名称',
            $tyun_data["CompanyName"]=$myData["companyname"];
            //3. 'AgentCode'=>'代理商标识码',
            $tyun_data["AgentCode"]=$myData["agentcode"];
            //4. 'ContractType'=>'服务类型 1小程序建站 2云网站制作',
            //$tyun_data["ContractType"]=$myData["servicetype"];
            // 'BuyCount'=>'购买数量',
            //$tyun_data["BuyCount"]=$myData["buycount"];
            //'BuyYear'=>'购买年限',
            //$tyun_data["BuyYear"]=$myData["buyyear"];
            //服务内容
            $tyun_data['ServiceInfo'] = $serviceinfo;
            //5 'UserPhone'=>'客户手机',
            $tyun_data["UserPhone"]=$myData["custphone"];
            //6. 'CustPhoneCode'=>'客户手机验证码',
            $tyun_data["CustPhoneCode"]=$arr_custphonecode["message"];
            //7. 'SalesName'=>'签单销售',
            $tyun_data["SalesName"]=$myData["salesname"];
            //8. 'SalesPhone'=>'销售手机',
            $tyun_data["SalesPhone"]=$myData["salesphone"];
            //9. 'SignBillDate'=>'签单时间',
            $tyun_data["SignBillDate"]=$myData["signdate"];
            //10. 'Status'=>'状态 传1 表示合同有效',
            $tyun_data["Status"]=$myData["status"];
            //11. 'ServiceLoginName'=>'客服人员crm登陆账号 预留信息方便以后crm和T云管理后台账号打通',
            $tyun_data["ServiceLoginName"]=$myData["serviceloginname"];

            $this->_logs(array("data加密前数据：", $tyun_data));
            $tempData['data'] = $this->encrypt(json_encode($tyun_data));

            $this->_logs(array("data加密后数据：", $tempData['data']));
            $postData = http_build_query($tempData);//传参数
            try{
                $res = $this->https_request($this->tyun_sitecontract_url, $postData);
                $result = json_decode($res, true);
                $result_up = json_decode($result, true);
                if($result_up["success"] == true || $result_up["success"] == 1){
                    //更新推送状态
                    $up_sql = "UPDATE vtiger_tyunstationsale SET pushstatus=1,updatetime=NOW() WHERE stationsaleid=?";
                    $adb->pquery($up_sql, array($stationsaleid));
                }else{
                    $result_up['success']= false;
                    $result_up['message']= "移动CRM建站合同推送到T云处理失败,原因:".$result_up['message'];
                }
            }catch (Exception $e){
                $result_up['success']= false;
                $result_up['message']= $e->getMessage();
            }
        }else{
            //判断是否为需要建站购买才可签收的合同;
            global $service_contracts_arr_productids,$service_contracts_arr_productNames;
            $arr_productids = $service_contracts_arr_productids;
            $arr_productNames = $service_contracts_arr_productNames;
            //测试
            //$arr_productids = array("861083","861084");
            //$arr_productNames = array("861083"=>"云建站3.0小程序标准建站（首购）");

            for($i=0;$i<count($arr_productids);$i++){
                if(in_array($arr_productids[$i],$productids)){
                    $result_up['success']= false;
                    $result_up['message']= $arr_productNames[$arr_productids[$i]]." 必须先购买才可签收,请确认!";
                    break;
                }
            }
        }
        return $result_up;
    }
    /**
     * 写日志，用于测试,可以开启关闭
     * @param data mixed
     */
    public function _logs($data, $file = 'logs_'){
        $year	= date("Y");
        $month	= date("m");
        $dir	= './logs/tyun/' . $year . '/' . $month . '/';
        if(!is_dir($dir)) {
            mkdir($dir,0755,true);
        }
        $file = $dir . $file . date('Y-m-d').'.txt';
        @file_put_contents($file, '----------------' . date('H:i:s') . '--------------------'.PHP_EOL.var_export($data,true).PHP_EOL, FILE_APPEND);
    }
    public function https_request($url, $data = null){
        $curl = curl_init();
        $this->_logs($url);
        //curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type:application/x-www-form-urlencoded"));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        $this->_logs($output);
        curl_close($curl);

        //throw new Exception($curl);

        return $output;
    }

    /**
     * des加密
     * @param unknown $encrypt 原文
     * @return string
     */
    public function encrypt($encrypt, $key="sdfesdcf\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0") {
        $mcrypt = MCRYPT_TRIPLEDES;
        $iv = mcrypt_create_iv(mcrypt_get_iv_size($mcrypt, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $passcrypt = mcrypt_encrypt($mcrypt, $key, $encrypt, MCRYPT_MODE_ECB, $iv);
        $encode = base64_encode($passcrypt);
        return $encode;
    }
    /**
     * ajax获取手机验证码
     */
    public function getMobileVerifyOther($mobile){
        $postData['data'] = $this->encrypt($mobile);
        $res = $this->https_requestRaw($this->tyun_sitemobilecode_url, json_encode($postData));
        $res=trim($res,'"');
        $res=str_replace('\\','',$res);
        $result = json_decode($res, true);
        if($result['success']){
            return array('success'=>true, 'message'=>$result['message']);
        }else{
            return array('success'=>false, 'message'=>'手机验证码发送失败');
        }
    }
    /**
     * ajax获取手机验证码
     */
    public function getMobileVerify($mobile){
        //$myData = array('Mobile'=>$mobile);
        //$tempData = json_encode($myData);
        //$data['data'] = $this->encrypt($mobile);
        //$postData = http_build_query($data);//传参数
        $myData = array('Mobile'=>$mobile);
        $tempData = json_encode($myData);
        $postData = $this->encrypt($tempData);
        //print_r($data);
        /* echo "参数:" . $mobile."<br>";
        echo "加密后";echo $postData."<br>"; */
        $res = $this->https_request($this->tyun_mobilecode_url, $postData);
        $result = json_decode($res, true);
        //$result = json_decode($result, true);

        if($result['success']){
            return array('success'=>true, 'message'=>$result['Code']);
        }else{
            return array('success'=>false, 'message'=>'手机验证码发送失败');
        }

    }

    /**
     * 查询T云购买信息
     * @param $fieldname
     * @param $userid
     * @return array
     */
    function searchTyunBuyServiceInfo(Vtiger_Request $request){
        global $adb;
        $tyun_account = $request->get('tyun_account');
        $recordId = $request->get('record');
        $query="SELECT 
                M.contractid,
                M.contractname,
                M.productid,
                M.classtype,
                (SELECT MAX(str_to_date(REPLACE(MM.expiredate,'/','-'),'%Y-%m-%d')) FROM vtiger_activationcode MM WHERE MM.status IN(0,1) AND MM.usercode=M.usercode) AS expiredate,
                IF(M.classtype='buy',IFNULL(M.customerid,''),P.customerid) AS customerid,
                IF(M.classtype='buy',M.customername,P.customername) AS customername,
                IF(M.classtype='buy',M.activecode,P.activecode) AS activecode,
                IF(M.classtype='buy',M.activationcodeid,P.activationcodeid) AS activationcodeid,
                IF(M.classtype='buy',M.agents,P.agents) AS agents,
                (SELECT vtiger_products.productname FROM vtiger_products WHERE vtiger_products.tyunproductid=M.productid LIMIT 1) AS productname
                FROM vtiger_activationcode M
                LEFT JOIN vtiger_activationcode P ON(M.buyid=P.activationcodeid)
                WHERE M.status IN(0,1) AND M.classtype IN('buy','upgrade','degrade') 
                AND M.comeformtyun=0
                AND M.contractid !=?
                AND M.usercode=? ORDER BY M.activationcodeid DESC LIMIT 1";
        try{
            //$listResult = $adb->pquery($query, array("%$tyun_account%"));
            $listResult = $adb->pquery($query, array($recordId,$tyun_account));
            $res = array();
            while($rawData=$adb->fetch_array($listResult)) {
                $res[] =  $rawData;
            }
            if(count($res) == 0){
                return array('success'=>false,'message'=>'未查询到原版本信息','buyList'=>null);
            }
        }catch(WebServiceException $exception){
            return array('success'=>false,'message'=>'查询数据错误','buyList'=>null);
        }

        $query2 = "SELECT customerid From  vtiger_activationcode WHERE status IN(0,1) AND classtype IN('buy','upgrade','degrade') 
                AND comeformtyun=0
                AND contractid =?
                AND usercode=?  ORDER BY activationcodeid DESC LIMIT 1";
        $listResult2 = $adb->pquery($query2, array($recordId,$tyun_account));
        $customerid = '';
        while($rawData2=$adb->fetch_array($listResult2)) {
            $customerid =  $rawData2['customerid'];
        }

        return array('success'=>true,'message'=>'','buyList'=>$res,'customerid'=>$customerid);

    }

    public function tyunContractConfirm(Vtiger_Request $request){
        $contractNo = $request->get('contract_no');
        $returndate = $request->get('Returndate');
        $contractbuytype=$request->get('contractbuytype');
        if($contractbuytype == 'buy'){
            $type = "1";
        }else if($contractbuytype == 'renew'){
            $type = "3";
        }else if($contractbuytype == 'upgrade' || $contractbuytype == 'degrade'){
            $type = "2";
        }else if($contractbuytype == 'againbuy'){
            $type = "4";
        }else{
            return array('success'=>true,'message'=>'');
        }

        //合同类型 1购买 2升级 3续费 4另购
        $tyunData['ContractType'] = $type;
        //合同编号
        $tyunData['ContractCode'] = $contractNo;
        //归还时间
        $tyunData['AddDate'] = $returndate;

        $this->_logs(array("data加密前数据：", $tyunData));
        $tempData['data'] = $this->encrypt(json_encode($tyunData));
        $this->_logs(array("data加密后数据：", $tempData['data']));
        $postData = http_build_query($tempData);//传参数
        $res = $this->https_request($this->tyun_contractconfirm_url, $postData);
        $result = json_decode($res, true);
        return json_decode($result, true);
    }

    /**
     * 获取合同购买类型(购买、升级、续费、降级、另购)
     * @param $record
     * @return string
     * @throws Exception
     */
    public function getContractBuyType($record){
        global $adb;
        //判断是否降级
        $classtype_sql="SELECT classtype FROM vtiger_activationcode  WHERE contractid=? AND status IN(0,1)";
        $result_classtype = $adb->pquery($classtype_sql,array($record));
        if ($adb->num_rows($result_classtype)>0) {
            $row = $adb->query_result_rowdata($result_classtype, 0);
            return $row['classtype'];
        }
        return '';
    }

    /**
     * CRUL RAW
     * @param $url
     * @param null $data
     * @return bool|string
     */
    public function https_requestRaw($url, $data = null){
        $curl = curl_init();
        $this->_logs($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type:application/json;charset=utf-8"));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        $this->_logs($output);
        curl_close($curl);
        return $output;
    }

    /**
     *  建站购买续费
     * @param $data
     * @return array
     * @throws Exception
     */
    public function RenewCloudSiteUser($data){
        global $adb;
        //判断合同编号是否重复
        $query_sql = "SELECT 1 FROM vtiger_tyunstationsale WHERE stationsalestatus=0 AND contractcode=?";
        $sel_result = $adb->pquery($query_sql, array($data['contractcode']) );
        $res_cnt = $adb->num_rows($sel_result);
        if($res_cnt > 0){
            $result_up['success']= 2;
            $result_up['message']= "合同编号重复已存在";
            return array($result_up);
        }
        //判断是否已经购买过(判断激活码表中是否存在)
        $query_sql = "SELECT 1 FROM vtiger_activationcode WHERE status IN(0,1) AND contractid=?";
        $sel_result = $adb->pquery($query_sql, array($data['contractid']) );
        $res_cnt = $adb->num_rows($sel_result);
        if($res_cnt > 0){
            $result_up['success']= 2;
            $result_up['message']= "该合同已经领取过激活码,不能购买";
            return array($result_up);
        }
        $myData=array("LoginName"=>$data['loginname'],
                        "ContractCode"=>$data['contractcode'],
                        "OldCloseDate"=>$data['oldclosedate'],
                        "RenewYear"=>$data['productlife'],
                        "AddDate"=>date('Y-m-d H:i:s'));
        $tempData = json_encode($myData);
        $postData['data'] = urlencode($this->encrypt($tempData));
        $res = $this->https_requestRaw($this->RenewCloudSiteUser, json_encode($postData));
        $res=trim($res,'"');
        $res=str_replace('\\','',$res);
        $result = json_decode($res, true);

        if($result['success']){
            $inputData=array(
                'contractid'=>$data['contractid'],
                'agentcode'=>$data['agentcode'],
                'serviceinfo'=>$data['serviceinfo'],
                'signdate'=>$data['signdate'],
                'contractcode'=>$data['contractcode'],
                'opendate'=>$result['openDate'],
                'salesname'=>$data['salesname'],
                'salesphone'=>$data['salesphone'],
                'serviceloginname'=>"",
                'createdid'=>$data['createdid'],
                'createdtime'=>$data['createdtime'],
                'productlife'=>$data['productlife'],
                'loginname'=>$data['loginname']

            );
            //获取客户登录账户名
            $service_sql = "SELECT vtiger_users.user_name FROM vtiger_account
						LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_account.serviceid
						WHERE  accountid=? AND vtiger_users.status='Active' limit 1";
            $sel_result = $adb->pquery($service_sql, array($data['accountid']) );
            $res_cnt = $adb->num_rows($sel_result);
            if($res_cnt > 0){
                $row= $adb->query_result_rowdata($sel_result, 0);
                $inputData['serviceloginname'] = $row['user_name'];
            }
            $adb->pquery("INSERT INTO `vtiger_tyunstationsale` (`contractid`, `accountid`, `companyname`, `agentcode`, `serviceinfo`,  `signdate`, `custphone`, `status`, `contractcode`, `loginname`, `opendate`, `finnishdate`, `salesname`, `salesphone`, `serviceloginname`, `createdid`, `createdtime`,   `stationsalestatus`, `parentid`,servicecount, `classtype`,productlife) 
															SELECT ?,     `accountid`, `companyname`, ?,           ?,  ?,          `custphone`, `status`, ?,             `loginname`, ?,           `finnishdate`, ?,           ?,            ?,                 ?,            ?,              0,                   `stationsaleid`,servicecount,'renew',? FROM 
                        vtiger_tyunstationsale WHERE loginname=? AND classtype='buy' AND stationsalestatus=0 limit 1",array($inputData));
            $adb->pquery("UPDATE vtiger_servicecontracts SET total=? WHERE servicecontractsid=?",array($data['contractamount'],$data['contractid']));

            $user = new Users();
            $current_user = $user->retrieveCurrentUserInfoFromFile($data['createdid']);
            $sql = "SELECT email1,last_name FROM `vtiger_users` WHERE id=? LIMIT 1";
            $sel_result = $adb->pquery($sql, array($current_user->reports_to_id));
            $res_cnt = $adb->num_rows($sel_result);
            $agent_email = '';
            $agent_last_name = '';
            if($res_cnt > 0) {
                $row = $adb->query_result_rowdata($sel_result, 0);
                $agent_email = $row['email1'];
                $agent_last_name = $row['last_name'];
            }

            $email = $current_user->email1;
            $last_name = $current_user->last_name;
            $department = $current_user->department;
            $query="SELECT * FROM vtiger_tyunstationsale WHERE stationsalestatus=0 AND classtype='buy' AND loginname=? LIMIT 1";
            $resultStationsale=$adb->pquery($query,array($data['loginname']));
            $resultData=$adb->raw_query_result_rowdata($resultStationsale);
            $Subject = 'T云建站续费成功通知';
            $nowTime = date("Y-m-d H:i", time());
            $body =  "员工：{$last_name}<br>部门：{$department}<br>客户：{$resultData['companyname']}<br>客户手机：{$resultData['custphone']}{$data['buyContent']}<br><br>购买时间：{$nowTime}<br>";

            $address = array(
                array('mail'=>$email, 'name'=>$last_name),//销售
                array('mail'=>$agent_email, 'name'=>$agent_last_name)//销售上级
            );
            self::sendMail($Subject,$body,$address);
            $result['mobile']=$resultData['custphone'];
            $result['success']=1;
        }else{
            $result['success']=2;
        }
        return array($result);
    }

    /**
     * 检查合同详情处是否可以编辑分成信息
     * @return array
     * @param Vtiger_Request $request
     */
    public function isCanDivided(Vtiger_Request $request){
        global $adb;
        $recorId = $request->get("record");
        $query = " SELECT total,isautoclose,sc_related_to FROM vtiger_servicecontracts AS vs INNER JOIN vtiger_crmentity as vc ON vc.crmid=vs.servicecontractsid WHERE vc.deleted=0 AND vs.servicecontractsid=? limit 1 ";
        $result=$adb->pquery($query,array($recorId));
        $total = $adb->query_result($result, 0, 'total');
        $isautoclose=$adb->query_result($result, 0, 'isautoclose');
        //如果合同金额大于0 && 合同是非框架合同  才进行 判断是否进行  修改拦截
        if($total>0 && $isautoclose==1){
            //获取合同回款总额
            $query=" SELECT sum(unit_price) as receivetotal FROM vtiger_receivedpayments WHERE receivedstatus='normal' AND relatetoid= ? limit 1 ";
            $result=$adb->pquery($query,array($recorId));
            $receivetotal=$adb->query_result($result, 0,'receivetotal');
            if($receivetotal >= $total){
                return  array('result'=>false,'message'=>'非框架合同合计回款金额大于或等于合同总额，不能申请合同分成修改','data'=>array('total'=>$total,'receiveTotal'=>$receivetotal,'request'=>$request->getAll()));
            }
        }
        return array('result'=>true,'total'=>$total,'accountid'=>$adb->query_result($result,'sc_related_to'));
    }
    public function checkTyunWebconfim($request){
        global $adb;
        $record=$request->get('record');
        $query='SELECT * FROM vtiger_activationcode WHERE `status` in(0,1) AND comeformtyun=1 AND contractid=?';
        $return=array('flag'=>true,'pushstatus'=>0);
        if($request->get('contract_classification')=='tripcontract'){
            return   array('flag'=>false,'msg'=>'','pushstatus'=>0);
        }
        do{
            $result=$adb->pquery($query,array($record));
            if($adb->num_rows($result)==0){
                $return['msg']='未找到该合同的相关订单信息,请先下单!';
                break;
            }
            $data=$adb->raw_query_result_rowdata($result);
            $contractRecordModel = ServiceContracts_Record_Model::getInstanceById($record,'ServiceContracts');
            $contract_classification = $contractRecordModel->get('contract_classification');
            $agentid = $contractRecordModel->get('agentid');
            if($request->get('sc_related_to')!=$data['customerid'] ){
                $return['msg']='订单客户和合同客户不是同一个客户!';
                break;
            }
            if($contract_classification=='tripcontract' && $agentid!=$data['agents']){
                $return['msg']='订单代理商和合同代理商不是同一个代理商!';
                break;
            }
            if($data['fromactivity']){
                $ajax_packageyears = $request->get('ajax_packageyear');
                foreach ($ajax_packageyears as $ajax_packageyear){
                    $t = explode(':',$ajax_packageyear);
                    $packageyears[$t[0]] = $t[1];
                }

                $ajax_extraproductidyears = $request->get('ajax_extraproductidyear');
                foreach ($ajax_extraproductidyears as $ajax_extraproductidyear){
                    $t = explode(':',$ajax_extraproductidyear);
                    $extraproductidyear[$t[0]] = $t[1];
                }
                $isequalage = false;
                while ($row=$adb->fetch_array($result)){
                    if($row['productid']){
                        $currentAgeLife=$packageyears[$row['productid']]/12;
                    }else{
                        $buyseparatelys = explode(',',$row['buyseparately']);
                        $currentAgeLife = $extraproductidyear[$buyseparatelys[0]]/12;
                    }
                    if($row['productlife']!=$currentAgeLife){
                        $isequalage = true;
                    }
                }
                if($isequalage){
                    $return['msg']='产品年限不一致,请重新选择!!!';
                    break;
                }
            }else{
                $currentAgeLife=current($request->get('agelife'));
                $currentAgeLife=round($currentAgeLife/12);
                if($data['productlife']!=$currentAgeLife){
                    $return['msg']='产品年限不一致,请重新选择!!!';
                    break;
                }
            }
            if($data['pushstatus']==1){
                $return=array('flag'=>false,'msg'=>'','pushstatus'=>1);
                break;
            }
            $return=array('flag'=>false,'msg'=>'','pushstatus'=>0);
        }while(0);
        return $return;
    }
    public function getTyunWebProducts($record,$productid){
        global $adb;
        $query="SELECT * FROM vtiger_activationcode WHERE contractid=? AND `status`!=2 AND comeformtyun=1 limit 1";
        $result=$adb->pquery($query,array($record));
        $products = array();
        if($adb->num_rows($result)) {
            $data = $adb->raw_query_result_rowdata($result, 0);
            if (!empty($data['buyserviceinfo'])) {
                $json_data = json_decode($data['buyserviceinfo'], true);
                $products = array();
                if (!empty($json_data['package'])) {
                    foreach ($json_data['packageProducts'] as $value) {
                        $temp = array();
                        $temp['excost'] = "0";//外采成本
                        $temp['ptempunit_price'] = 0;
                        $temp['punit_price'] = 0;
                        $temp['productcategory'] = "std";
                        $temp['productcomboid'] = $json_data['package']['ID'];
                        $temp['productid'] = $value['Product']['ID'];
                        $temp['productname'] = $value['Product']['Title'] . '<->' . $json_data['package']['Title'];
                        $temp['realprice'] = "0";//成本价
                        $temp['renewalcost'] = "0";
                        $temp['renewalfee'] = "0";
                        $temp['tagid'] = $productid;
                        $temp['prealprice'] = 0;//成本合计
                        $temp['thepackage'] = '--';
                        $temp['tranperformance'] = "0";
                        $temp['unit_price'] = "0";
                        $temp['version'] = "0";
                        $temp['viewedtime'] = '';
                        $temp['opendate'] = '';
                        $temp['closedata'] = '';
                        $products[] = $temp;
                    }
                }
                if(!empty($json_data['renewpackage'])){
                    $temp=array();
                    $temp['excost']="0";//外采成本
                    $temp['ptempunit_price']=0;
                    $temp['punit_price']=0;
                    $temp['productcategory']="std";
                    $temp['productcomboid']='0';
                    $temp['productid']=$json_data['renewpackage']['ID'];
                    $temp['productname']=$json_data['renewpackage']['Title'];
                    $temp['realprice']="0";//成本价
                    $temp['renewalcost']="0";
                    $temp['renewalfee']="0";
                    $temp['tagid']=$productid;
                    $temp['prealprice']=0;//成本合计
                    $temp['thepackage']='--';
                    $temp['tranperformance']="0";
                    $temp['unit_price']="0";
                    $temp['version']="0";
                    $temp['viewedtime']='';
                    $temp['opendate']='';
                    $temp['closedate']='';
                    $products[]=$temp;
                }
                if (!empty($json_data['products'])) {
                    foreach ($json_data['products'] as $value) {
                        $temp = array();
                        $temp['excost'] = "0";//外采成本
                        $temp['ptempunit_price'] = 0;
                        $temp['punit_price'] = 0;
                        $temp['productcategory'] = "std";
                        $temp['productcomboid'] = '0';
                        $temp['productid'] = $value['Product']['ID'];
                        $temp['productname'] = $value['Product']['Title'];
                        $temp['realprice'] = "0";//成本价
                        $temp['renewalcost'] = "0";
                        $temp['renewalfee'] = "0";
                        $temp['tagid'] = $productid;
                        $temp['prealprice'] = 0;//成本合计
                        $temp['thepackage'] = '--';
                        $temp['tranperformance'] = "0";
                        $temp['unit_price'] = "0";
                        $temp['version'] = "0";
                        $temp['viewedtime'] = '';
                        $temp['opendate'] = '';
                        $temp['closedata'] = '';
                        $products[] = $temp;
                    }
                }
                if (!empty($json_data['renewproducts'])) {
                    foreach ($json_data['renewproducts'] as $value) {
                        $temp = array();
                        $temp['excost'] = "0";//外采成本
                        $temp['ptempunit_price'] = 0;
                        $temp['punit_price'] = 0;
                        $temp['productcategory'] = "std";
                        $temp['productcomboid'] = '0';
                        $temp['productid'] = $value['Product']['ID'];
                        $temp['productname'] = $value['Product']['Title'];
                        $temp['realprice'] = "0";//成本价
                        $temp['renewalcost'] = "0";
                        $temp['renewalfee'] = "0";
                        $temp['tagid'] = $productid;
                        $temp['prealprice'] = 0;//成本合计
                        $temp['thepackage'] = '--';
                        $temp['tranperformance'] = "0";
                        $temp['unit_price'] = "0";
                        $temp['version'] = "0";
                        $temp['viewedtime'] = '';
                        $temp['opendate'] = '';
                        $temp['closedata'] = '';
                        $products[] = $temp;
                    }
                }
            }
        }
        return $products;
    }

       /**
     * 是否可以变更客户
     *
     * @param Vtiger_Request $request
     * @return bool
     */
    public function getAccount(){
        $recordid = $this->getId();
        $ardb = PearDatabase::getInstance();
        $sql1 = "select a.* from vtiger_newinvoice a left join vtiger_crmentity b on a.invoiceid =b.crmid 
where a.contractid=? and a.modulestatus!=? and b.deleted=0";
        $result1 = $ardb->pquery($sql1,array($recordid,'c_cancel'));
        if($ardb->num_rows($result1)){
            while ($row=$ardb->fetchByAssoc($result1)){
                $accounts[]=$row['accountid'];
            }
            return $accounts[0];
        }

        $sql2 = "select * from vtiger_rechargesheet a left join vtiger_refillapplication b on a.refillapplicationid =b.refillapplicationid 
left join vtiger_crmentity c on b.refillapplicationid=c.crmid
left join vtiger_servicecontracts d on d.servicecontractsid=b.servicecontractsid
 where b.servicecontractsid=? and b.modulestatus !=? and d.contract_classification!='tripcontract'";
        $result2 = $ardb->pquery($sql2,array($recordid,'c_cancel'));
        if($ardb->num_rows($result2)){
            while ($row=$ardb->fetchByAssoc($result2)){
                $accounts[] = $row['accountid'];
            }
            return $accounts[0];
        }
        return '';
    }

 /**
     * 获取合同主体id
     *
     * @param Vtiger_Request $request
     * @return bool
     */
    public function getInvoicecompany($record=''){
        $recordid = $record?$record:$this->getId();
        $sql = "select a.* from vtiger_newinvoice a left join vtiger_crmentity b on a.invoiceid =b.crmid
where b.deleted=0 and a.contractid = ? and a.modulestatus != ?";
        $ardb = PearDatabase::getInstance();
        $result = $ardb->pquery($sql,array($recordid,'c_cancel'));
        if($ardb->num_rows($result)){
            while ($row=$ardb->fetchByAssoc($result)){
                $invoicecompany[] = $row['invoicecompany'];
            }
            return $invoicecompany[0];
        }
        return '';
    }

    /**
     * 是否可以变更合同主体
     *
     * @param Vtiger_Request $request
     * @return bool
     */
    public function canChangeMainContract(Vtiger_Request $request){
        $recordid = $this->getId();
        $sql = "select a.* from vtiger_newinvoice a left join vtiger_crmentity b on a.invoiceid =b.crmid
 where b.deleted=0 and a.contractid = ? and a.modulestatus != ?";
        $ardb = PearDatabase::getInstance();
        $result = $ardb->pquery($sql,array($recordid,'c_cancel'));
        if($ardb->num_rows($result)){
            while ($row=$ardb->fetchByAssoc($result)){
                $invoicecompany[] = $row['invoicecompany'];
            }
            if($invoicecompany && !in_array($request->get('value'),$invoicecompany)){
                return false;
            }
        }
        return true;
    }

    /**
     * 获取Tweb产品的套餐
     * @param $data
     * @param string $isall
     * @return bool|string
     */
    public function getTyunWebBuy($data,$isall="all"){
        global $tyunweburl,$sault;
        $usercodeid=$data['usercodeid'];
        $categoryID = $data['productclass'];
        $contract_classification = $data['contract_classification'];
        if($isall!="all"){
            $params = array('categoryID'=>$categoryID,"packageIDList"=>array($isall));
        }else{
            $params = array('categoryID'=>$categoryID);
        }
        if($contract_classification=='tripcontract'){
            $params['agentType'] = 1;
        }
        if($data['agents']){
            $params['agentID']=$data['agents'];
        }
        if($contract_classification=='tripcontract' && $data['agents']){
            $params['packageIDList'] = $this->getAgentLevel($data['agents']);
        }
        $postData=json_encode($params);

        $GetPackageList=$tyunweburl."api/micro/order-basic/v1.0.0/api/Package/GetPackageList";
        $time=time().'123';
        $sault1=$time.$sault;
        $token=md5($sault1);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $res = $this->https_requestTweb($GetPackageList, $postData,$curlset);
        $this->_logs(array('params'=>$params,'result'=>json_decode($res)));
        return $res;
    }
    /**
     * 获取Tweb产品的另购项
     * @param $data
     * @param string $isall
     * @return bool|string
     */
    public function getOtherPorduct($data){
        global $tyunweburl,$sault;
        $GetProductList=$tyunweburl."api/micro/order-basic/v1.0.0/api/Product/GetProductList";
        $categoryID = $data['productclass'];
        $seriesID = $data['seriesID'];
        $authenticationType= $data['agents'];
        $userID= $data['usercodeid'];
        $contract_classification = $data['contract_classification'];
        $params = array('seriesID'=>1,'authenticationType'=>1);
        if($contract_classification=='tripcontract'){
            $params['agentType'] = 1;
        }
        if($userID){
            $params['userID'] = $userID;
        }
        if($data['agents']){
            $params['agentID'] = $data['agents'];
        }
        if($contract_classification=='tripcontract' && $data['agents']){
            $params['packageIDList'] = $this->getAgentLevel($data['agents']);
        }
        if($categoryID || $categoryID==='0'){
            $params['categoryID'] = $categoryID;
        }
        $postData=json_encode($params);
//        $postData=json_encode(array('categoryID'=>$categoryID,'seriesID'=>1,'authenticationType'=>1,'userID'=>$userID));
        $time=time().'123';
        $sault1=$time.$sault;
        $token=md5($sault1);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $res = $this->https_requestTweb($GetProductList, $postData,$curlset);
        $this->_logs(array('GetProductList','params'=>$params,'result'=>json_decode($res)));
        return $res;
    }
    public function https_requestTweb($url, $data = null,$curlset=array()){
        $curl = curl_init();
        if(!empty($curlset)){
            foreach($curlset as $key=>$value){
                curl_setopt($curl, $key, $value);
            }
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    /**
     * 产品组合
     * @param $record
     * @return array
     * @throws Exception
     */
    public function getTyunProductsOnline($record,$contract_typeName,$servicecontractstype='',$contract_classification='',$agents='',$productclass=''){
        global $adb,$configcontracttypeName,$configcontracttypeNameYUN,$configcontracttypeNameJT;
        if($record>0){
            $iscollegeedition=0;//web版
            if($configcontracttypeName==$contract_typeName){
                $iscollegeedition=0;//web版
            }elseif($configcontracttypeNameYUN==$contract_typeName){
                $iscollegeedition=1;//院校版
            }elseif($configcontracttypeNameJT==$contract_typeName){
                $iscollegeedition=2;//集团版
            }
            $query="SELECT a.*,b.contract_classification FROM vtiger_activationcode a left join vtiger_servicecontracts b on a.contractid=b.servicecontractsid WHERE a.contractid=? AND a.status!=2 AND a.comeformtyun=1 AND a.iscollegeedition=?";
            $result=$adb->pquery($query,array($record,$iscollegeedition));
            $pIDsArray=array();
            $pIDs=array();
            $otherArray=array();
            $otherIDs=array();
            if($adb->num_rows($result)) {
                $datas=array();
                $flag=true;
                while($row = $adb->fetch_array($result)){
                    $datas[]=$row;
//                    if($row['classtype']=='renew'){
//                        $flag=false;
//                    }
                    if(!$row['productid']){
                        $productNames = $row['productnames'];
                        $productNames = str_replace("&quot;", '"', $productNames);
                        $productNames = json_decode($productNames, true);
                        foreach ($productNames as $productName){
                            if(!in_array($productName['productID'],$otherIDs)) {
                                $otherIDs[] = $productName['productID'];
                                $otherArray[] = array("istyun" => 5,
                                    "parentid" => "0",
                                    "productid" => $productName['productID'],
                                    "productname" => $productName['productTitle'],
                                    "tyunproductid" => 'plist',
                                );
                            }
                        }
                    }
                }
                foreach($datas as $data) {
                    $categoryID = $data['productclass'];
                    if ($flag &&($data['classtype'] == 'buy' || in_array($data['classtype'], array('degrade','crenew', 'cupgrade', 'cdegrade')))) {
                        $datapack = $this->getTyunWebBuy($data);
                        $json_datapack = json_decode($datapack, true);
                        if ($json_datapack['code'] == 200) {
                            foreach ($json_datapack['data'] as $value) {
                                if(!in_array($value['Package']['ID'],$pIDs)){
                                    $pIDs[]=$value['Package']['ID'];
                                    $pIDsArray[] = array("istyun" => 3,
                                        "parentid" => "0",
                                        "productid" => $value['Package']['ID'],
                                        "productname" => $value['Package']['Title'],
                                        "tyunproductid" => 'plist',
                                    );
                                }

                            }

                        }
                    } elseif (in_array($data['classtype'] ,array('renew'))) {
                        $datapack = $this->getTyunWebRenew($data);
                        $json_datapack = json_decode($datapack, true);
                        //$otherArray = array();
                        if ($json_datapack['code'] == 200) {
                            //$pIDsArray = array();
                            $json_datapack_data = $json_datapack['data'];
                            if(!is_null($json_datapack_data['package']['ID']) && !in_array($json_datapack_data['package']['ID'],$pIDs)) {
                                $pIDs[]=$json_datapack_data['package']['ID'];
                                $pIDsArray[] = array("istyun" => 3,
                                    "parentid" => "0",
                                    "productid" => $json_datapack_data['package']['ID'],
                                    "productname" => $json_datapack_data['package']['Title'],
                                    "tyunproductid" => 'plist',
                                );
                            }
                            //$otherArray = array();
                            //$temparray = array();
                            foreach ($json_datapack_data['productSpecificationList'] as $value) {
                                if (!in_array($value['ProductID'], $otherIDs)) {
                                    $otherIDs[] = $value['ProductID'];
                                    $otherArray[] = array("istyun" => 3,
                                        "parentid" => "0",
                                        "productid" => $value['ProductID'],
                                        "productname" => $value['ProductTitle'],
                                        "tyunproductid" => 'plist',
                                    );
                                }
                            }
                            foreach ($json_datapack_data['packageSpecificationList'] as $value) {
                                if (!in_array($value['ProductID'], $otherIDs)) {
                                    $otherIDs[] = $value['ProductID'];
                                    $otherArray[] = array("istyun" => 3,
                                        "parentid" => "0",
                                        "productid" => $value['ProductID'],
                                        "productname" => $value['ProductTitle'],
                                        "tyunproductid" => 'plist',
                                    );
                                }
                            }
                        }
                    } elseif ($data['classtype'] == 'upgrade') {
                        $datapack = $this->getTyunWebBuy($data);
                        $json_datapack = json_decode($datapack, true);
                        if ($json_datapack['code'] == 200) {
                            //$pIDsArray = array();
                            foreach ($json_datapack['data'] as $value) {
                                if(!in_array($value['Package']['ID'],$pIDs)){
                                    $pIDs[]=$value['Package']['ID'];
                                    $pIDsArray[] = array("istyun" => 3,
                                        "parentid" => "0",
                                        "productid" => $value['Package']['ID'],
                                        "productname" => $value['Package']['Title'],
                                        "tyunproductid" => 'plist',
                                    );
                                }

                            }
                        }
                    }
                }

                $datapack = $this->getOtherPorduct($datas[0]);
                $json_datapack = json_decode($datapack, true);
                if ($json_datapack['code'] == 200) {
                    foreach ($json_datapack['data'] as $value) {
                        foreach ($value['Products'] as $value1) {
//                            if ($value1['CanSeparatePurchase']) {
//                            if (!in_array($value1['ProductID'],$pIDs)&&$value1['CanSeparatePurchase']) {
                            if(!in_array($value1['ProductID'],$otherIDs)){
                                $otherIDs[]=$value1['ProductID'];
                                $otherArray[] = array("istyun" => 5,
                                    "parentid" => "0",
                                    "productid" => $value1['ProductID'],
                                    "productname" => $value1['ProductTitle'],
                                    "tyunproductid" => 'plist',
                                );
                            }

//                            }
                        }
                    }
                }
                return array('product_list'=>$pIDsArray, 'isstandard'=>1,'otherproduct_list'=>$otherArray,'otherproducttype'=>0);
            }
        }

        $data= array();
        switch ($contract_typeName){
            case 'T云院校版':
                $productclass=7;
                break;
            case 'T云集团版':
                $productclass=9;
                break;
        }
        $data['productclass']=$productclass;
        $data['contract_classification']=$contract_classification;
        $data['agents']=$agents;

        $datapack = $this->getTyunWebBuy($data);
        $json_datapack = json_decode($datapack, true);
        $this->_logs(array('data'=>$data,"tyunweb"=>$datapack));
        if ($json_datapack['code'] == 200) {
            foreach ($json_datapack['data'] as $value) {
                $pIDs[]=$value['Package']['ID'];
                $pIDsArray[] = array("istyun" => 3,
                    "parentid" => "0",
                    "productid" => $value['Package']['ID'],
                    "productname" => $value['Package']['Title'],
                    "tyunproductid" => 'plist',
                );
            }
        }

        $datapack = $this->getOtherPorduct($data);
        $json_datapack = json_decode($datapack, true);
        $this->_logs($json_datapack);
        if ($json_datapack['code'] == 200) {
            foreach ($json_datapack['data'] as $value) {
                foreach ($value['Products'] as $value1) {
                    if(!in_array($value1['ProductID'],$otherIDs)){
                        $otherIDs[]=$value1['ProductID'];
                        $otherArray[] = array("istyun" => 5,
                            "parentid" => "0",
                            "productid" => $value1['ProductID'],
                            "productname" => $value1['ProductTitle'],
                            "tyunproductid" => 'plist',
                        );
                    }
                }
            }
        }
        return array('product_list'=>$pIDsArray, 'isstandard'=>1,'otherproduct_list'=>$otherArray,'otherproducttype'=>0);
    }
    public function getTyunWebRenew($data){
        global $tyunweburl,$sault;
        $usercodeid=$data['usercodeid'];
        $categoryID = $data['productclass'];
        $contract_classification = $data['contract_classification'];
        $params = array('categoryID'=>$categoryID,'authenticationType'=>1);
        if($contract_classification=='tripcontract'){
            $params['agentType'] =1 ;
        }
        if($usercodeid){
            $params['userID'] = $usercodeid;
        }
        $postData=json_encode($params);
        $GetPackageList=$tyunweburl."api/micro/order-basic/v1.0.0/api/Product/GetUserProductInfo";
        $time=time().'123';
        $sault1=$time.$sault;
        $token=md5($sault1);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $res = $this->https_requestTweb($GetPackageList, $postData,$curlset);
        return $res;
    }
    public function checkContractClient($request){
        global $adb;
        $record=$request->get('record');
        $query="SELECT * FROM vtiger_activationcode WHERE contractid=? AND comeformtyun=1 AND pushstatus=0 AND `status`!=2 AND classtype!='cbuy' limit 1";
//        $query="SELECT * FROM vtiger_activationcode WHERE contractid=? AND comeformtyun=1 AND pushstatus=0 AND `status`!=2 limit 1";
        $result=$adb->pquery($query,array($record));
        if($adb->num_rows($result)>0){
            $rawData=$adb->raw_query_result_rowdata($result,0);
            if('clientmigration'==$rawData['customerstype']){
                $flag=$this->contractConfirmClient($rawData['contractname']);
                if(!$flag){
                    if($rawData['upgradetransfer']>0){
                        $this->contractConfirmClientDoOldContract($rawData['usercode'],$rawData['creator'],$rawData['upgradetransfer']);
                    }
                    $sql = "SELECT vtiger_users.*,vtiger_departments.* FROM `vtiger_users` LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid WHERE id=? LIMIT 1";
                    $sel_result = $adb->pquery($sql, array($rawData['creator']));
                    $res_cnt = $adb->num_rows($sel_result);
                    if($res_cnt > 0) {
                        $row = $adb->query_result_rowdata($sel_result, 0);
                        $agent_email = trim($row['email1']);
                        $last_name = $row['last_name'];
                        $department = $row['departmentname'];

                        $productnames = TyunWebBuyService_Record_Model::getProductNamesByContractId($record);
                        if($rawData['classtype'] == 'cupgrade'){
                            $Subject = '71360迁移开始通知';
                            $body =  "员工：{$last_name}<br>部门：{$department}<br>客户：{$rawData['customername']}<br>合同编号：{$rawData['contractname']}<br>购买版本：{$productnames}<br>年限：{$rawData['productlife']} 年<br><br/>以上，合同已签收正在迁移中，迁移完成后会以邮件提醒！<br>";
                        }else{
                            $Subject = '71360迁移通知';
                            $body =  "员工：{$last_name}<br>部门：{$department}<br>客户：{$rawData['customername']}<br>合同编号：{$rawData['contractname']}<br>购买版本：{$productnames}<br>年限：{$rawData['productlife']} 年<br><br/>以上，合同已签收，当前版本到期后进行迁移，迁移完成后会以邮件提醒！<br>";
                        }

                        $address = array(
                            array('mail'=>trim($agent_email), 'name'=>$last_name)
                        );
                        $this->_logs(array('email'=>$agent_email,'name'=>$last_name,'subject'=>$Subject,'body'=>$body));
                        Vtiger_Record_Model::sendMail($Subject,$body,$address);
                    }
                }
                return $flag;
            }
        }
        return false;
    }

    /**
     * 合同迁移签收
     */
    public function contractConfirmClient($contractno){
        global $tyunweburl,$sault;
        $postData=json_encode(array('ContractCode'=>$contractno));
        $ContractConfirm=$tyunweburl."api/micro/order-basic/v1.0.0/api/Order/ContractConfirm";
        $time=time().'123';
        $sault1=$time.$sault;
        $token=md5($sault1);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $res = $this->https_requestTweb($ContractConfirm, $postData,$curlset);
        $resultData=json_decode($res,true);
        if($resultData['code']==200){
            return false;
        }
        return true;
    }

    /**
     * 数据迁移成功后处理原合同数据
     * @param $usercode
     * @param $currentdate
     * @throws Exception
     */
    public function contractConfirmClientDoOldContract($usercode,$currentdate,$SurplusMoney){
        global $adb;
        $query='SELECT left(expiredate,10) as expiredate,contractid,productlife,classtype,left(receivetime,10) AS receivetime,left(startdate,10) AS startdate,activationcodeid FROM vtiger_activationcode WHERE usercode=? AND `status` in(0,1) AND comeformtyun=0 AND left(expiredate,10)>?';
        $result=$adb->pquery($query,array($usercode,$currentdate));
        if($adb->num_rows($result)){
            $currenttime=strtotime($currentdate);
            while($row=$adb->fetch_array($result)){
                $query='SELECT vtiger_servicecontracts.total FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE deleted=0 AND vtiger_servicecontracts.servicecontractsid=? and vtiger_servicecontracts.modulestatus=\'c_complete\'';
                $serResult=$adb->pquery($query,array($row['contractid']));
                if($adb->num_rows($serResult)){
                    $resultData=$adb->raw_query_result_rowdata($serResult,0);
                    if(in_array($row['classtype'],array('buy','','degrade','upgrade'))){
                        $starttime=strtotime($row['receivetime']);
                    }else{
                        $starttime=strtotime($row['startdate']);
                    }
                    if($currenttime<=$starttime){
                        if(bcsub($SurplusMoney,$resultData['total'])>-1){
                            $sql='UPDATE vtiger_activationcode SET upgradecontractprice=0,isdisabled=1,canceldatetime=?,surplusmoney=? WHERE activationcodeid=?';
                            $adb->pquery($sql,array(date('Y-m-d H:i:s'),$resultData['total'],$row['activationcodeid']));
                        }
                    }else{
                        $starttime=strtotime($row['expiredate']);
                        if($starttime>$currenttime){
                            $starttime=$starttime-$currenttime;
                            $starttime=$starttime/(24*60*60);
                            $productlife=$row['productlife']*365;
                            $addsurplusmoney=bcmul(bcdiv($resultData['total'],$productlife,6),$starttime,6);
                            $subtotal=bcsub($resultData['total'],$addsurplusmoney,2);
                            $sql='UPDATE vtiger_activationcode SET isdisabled=1,upgradecontractprice=?,canceldatetime=?,surplusmoney=? WHERE activationcodeid=?';
                            $adb->pquery($sql,array($subtotal,date('Y-m-d H:i:s'),$addsurplusmoney,$row['activationcodeid']));
                        }
                    }
                }
            }
        }
    }

    /**
     * 验证T云活动产品和合同是否一致
     * @param Vtiger_Request $request
     */
    public function checkTyunActivityProductActivationCode(Vtiger_Request $request,$newxCheckFlag=true){
        $result_up = array('success'=>true,'ismodify'=>false,'msg'=>'','reason'=>'','id'=>'0','contractstatus'=>'1');
        //$contract_type=$request->get('contract_type');
        $classtype=$request->get('contractbuytype');
        $recordId=$request->get('record');

        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'ServiceContracts');
        $entity=$recordModel->entity->column_fields;
        $contract_no=$entity['contract_no'];
        $agentid=$entity['agentid'];
        $contract_classification=$entity['contract_classification'];


        global $adb;
        $query="SELECT * FROM vtiger_activationcode WHERE contractid=? AND `status`!=2";
        $result=$adb->pquery($query,array($recordId));
        $num_rows=$adb->num_rows($result);
        if(!$num_rows){
            return array('success'=>false,'ismodify'=>false,'msg'=>'没有相关订单或激活码信息,请确认','reason'=>'');
        }
        $rawData=$adb->raw_query_result_rowdata($result,0);
        if($rawData['comeformtyun']==1){
            $productnamestemp=str_replace('&quot;','"',$rawData['productnames']);
            $parckagenum = 0;
            $productids=[];
            if($rawData['productid']){
                $parckagenum=1;
                $rawData['productids']=$rawData['productid'];
            }
            //活动年限
            $ajax_packageyears = $request->get('ajax_packageyear');
            foreach ($ajax_packageyears as $ajax_packageyear){
                $t = explode(':',$ajax_packageyear);
                $packageyears[$t[0]] = $t[1];
            }

            $ajax_productidpacknums = $request->get('ajax_productidpacknum');
            foreach ($ajax_productidpacknums as $ajax_productidpacknum){
                $t = explode(':',$ajax_productidpacknum);
                $packagenums[$t[0]] = $t[1];
            }

            $ajax_extraproductidyears = $request->get('ajax_extraproductidyear');
            foreach ($ajax_extraproductidyears as $ajax_extraproductidyear){
                $t = explode(':',$ajax_extraproductidyear);
                $extraproductidyear[$t[0]] = $t[1];
            }


            $isjoinactivity=0;
            $productnames=json_decode($productnamestemp,true);
            if($num_rows>1){
                $query="SELECT * FROM vtiger_activationcode WHERE contractid=? AND `status`!=2 AND comeformtyun=1";
                $result=$adb->pquery($query,array($recordId));
                $parckage=0;
                $tempclasstype='buy';
                $rawData=array();
                $sumprice=0;
                $oterproductidsarray=array();
                $productnames=array();
                $oterproductids='';
                $parckagenum=0;
                $productids = [];
                $agents = 0;
                while($row=$adb->fetch_array($result)){
                    $sumprice=bcadd($sumprice,$row['contractprice'],2);
                    if($row['productid']>0){
                        $parckage=$row['productid'];
                        $productids[] = $row['productid'];
                    }
                    if(in_array($row['classtype'],array('renew','crenew','upgrade','cupgrade','degrade','cdegrade'))){
                        $tempclasstype=$row['classtype'];
                    }
                    $rawData=$row;
                    $productnamestemp=str_replace('&quot;','"',$rawData['productnames']);
                    $productnamestemp=json_decode($productnamestemp,true);
                    if(!empty($productnamestemp)){
                        foreach($productnamestemp as $value){
                            if(!in_array($value["productID"],$oterproductidsarray)){
                                $oterproductidsarray[]=$value["productID"];
                                $oterproductids.=$value["productID"].',';
                                $productnames[$value["productID"]]=array("productID"=>$value["productID"],
                                    "productTitle"=>$value['productTitle'],
                                    "productCount"=>$value["productCount"],
                                    "specificationId"=>$value["specificationId"],
                                    "specificationTitle"=>$value["specificationTitle"],
                                );
                            }else{
                                $productnames[$value["productID"]]["productCount"]=$value["productCount"]+$productnames[$value["productID"]]["productCount"];
                            }
                        }
                    }
                    if($row['productid']){
                        $iscollegeedition = $row['iscollegeedition'];
//                        $parckagenum++;
                        if(!$iscollegeedition){
                            if($packagenums[$row['productid']]!=1){
                                return array('success'=>false,'ismodify'=>false,'msg'=>'套餐数量不一致,请重新选择','reason'=>'');
                            }
                        }else{
                            if($packagenums[$row['productid']]!=$row['packageamount']){
                                return array('success'=>false,'ismodify'=>false,'msg'=>'套餐数量不一致,请重新选择','reason'=>'');
                            }
                        }
                    }

                    if($row['productid']){
                        $currentAgeLife=$packageyears[$row['productid']]/12;
                        if($row['productlife']!=$currentAgeLife){
                            return array('success'=>false,'ismodify'=>false,'msg'=>'产品年限不一致,请重新选择','reason'=>'');
                        }
                    }else{
                        $buyseparatelys = explode(',',$row['buyseparately']);
                        $currentAgeLife = $extraproductidyear[$buyseparatelys[0]]/12;
//                        $currentAgeLife = $extraproductidyear[$row['buyseparately']]/12;
                        if($row['productlife']!=$currentAgeLife){
                            return array('success'=>false,'ismodify'=>false,'msg'=>'产品年限不一致,请重新选择','reason'=>'');
                        }
                    }
                    $agents = $row['agents'];
                    if($row['activityno']){
                        $isjoinactivity=1;
                    }
                }
                $rawData['buyseparately']=trim($oterproductids,',');
                $rawData['classtype']=$tempclasstype;
                $rawData['productid']=$parckage;
                $rawData['productids']=$productids;
                $rawData['contractprice']=$sumprice;
                $rawData['agents'] = $agents;

            }else{
                if($rawData['productid']){
                    $currentAgeLife=$packageyears[$rawData['productid']]/12;
                    if($rawData['productlife']!=$currentAgeLife){
                        return array('success'=>false,'ismodify'=>false,'msg'=>'产品年限不一致,请重新选择','reason'=>'');
                    }
                }else{
                    $buyseparatelys = explode(',',$rawData['buyseparately']);
                    $currentAgeLife = $extraproductidyear[$buyseparatelys[0]]/12;
//                    $currentAgeLife = $extraproductidyear[$rawData['buyseparately']]/12;
                    if($rawData['productlife']!=$currentAgeLife){
                        return array('success'=>false,'ismodify'=>false,'msg'=>'产品年限不一致,请重新选择','reason'=>'');
                    }
                }

                if($rawData['productid']){
                    $iscollegeedition = $rawData['iscollegeedition'];
//                    $parckagenum++;
                    if(!$iscollegeedition){
                        if($packagenums[$rawData['productid']]!=1){
                            return array('success'=>false,'ismodify'=>false,'msg'=>'套餐数量不一致,请重新选择','reason'=>'');
                        }
                    }else{
                        if($packagenums[$rawData['productid']]!=$rawData['packageamount']){
                            return array('success'=>false,'ismodify'=>false,'msg'=>'套餐数量不一致,请重新选择','reason'=>'');
                        }
                    }
                }

                if($rawData['activityno']){
                    $isjoinactivity=1;
                }
            }
            if($request->get("isjoinactivity")!=$isjoinactivity){
                return array('success'=>false,'ismodify'=>false,'msg'=>'订单参与活动情况和合同参与活动情况不一致，无法提交！','reason'=>'');
            }
            if($request->get('sc_related_to')!=$rawData['customerid'] ){
                return array('success'=>false,'ismodify'=>false,'msg'=>'订单客户和合同客户不是同一个客户,请确认','reason'=>'');
            }
            if($contract_classification == 'tripcontract' && $agentid!=$rawData['agents']){
                return array('success'=>false,'ismodify'=>false,'msg'=>'订单代理商和合同代理商不是同一个代理商,请确认','reason'=>'');
            }
            $ajax_productidpacknum=$request->get('ajax_productidpacknum');
//            if(!empty($ajax_productidpacknum)){
//                $ajax_productidpacknum1=array_map(function($v){$t=explode(':',$v);return $t[1];},$ajax_productidpacknum);
//                if(count($ajax_productidpacknum1)!=$parckagenum){
//                    return array('success'=>false,'ismodify'=>false,'msg'=>'套餐的数量不一致,请重新选择,请确认','reason'=>'');
//                }
//                $productnum = array_unique($ajax_productidpacknum1);
//                if(count($productnum)!=1){
//                    return array('success'=>false,'ismodify'=>false,'msg'=>'套餐的数量异常,请重新选择,请确认','reason'=>'');
//                }
//            }

            $total=$request->get('total');
            $total=str_replace(',','',$total);
            $total=str_replace(' ','',$total);
            if($rawData['onoffline']=='offline'){
                if($rawData['contractprice']!=$total){
                    return array('success'=>false,'ismodify'=>false,'msg'=>'合同金额与下单金额不一致!'.$rawData['contractprice'].'<=>'.$total,'reason'=>'');
                }
            }else{
                if($total!=$rawData['orderamount']){
                    return array('success'=>false,'ismodify'=>false,'msg'=>'合同金额与订单金额不一致','reason'=>'');
                }
            }
            $productid=$request->get('productid');
            $orderproductid=$rawData['productids'];
            if(!empty($productid) || !empty($orderproductid)){
                if(empty($productid) || empty($orderproductid)){
                    return array('success'=>false,'ismodify'=>false,'msg'=>'产品购买项不一致,请重新选择','reason'=>'');
                }
                $newarray1=array_diff($productid,$orderproductid);
                $newarray2=array_diff($orderproductid,$productid);
                if(count($newarray1)>0 || count($newarray2)>0){
                    return array('success'=>false,'ismodify'=>false,'msg'=>'产品购买项不一致,请重新选择','reason'=>'');
                }
            }
            $servicecontractstype=$request->get('servicecontractstype');
            $servicecontractstype=$servicecontractstype=='新增'?'buy':($servicecontractstype=='续费'
                ?'renew':($servicecontractstype=='againbuy'
                    ?'buy':$servicecontractstype));
            $classtype=ltrim($rawData['classtype'],'c');
            if($servicecontractstype!=$classtype){
                return array('success'=>false,'ismodify'=>false,'msg'=>'新增/续费/升级/另购/降级类型不一致,请重新选择','reason'=>'');
            }
            $extraproductid=$request->get('extraproductid');
            $buyseparately=$rawData['buyseparately'];
            if(!empty($extraproductid) || !empty($buyseparately)){
                if(empty($extraproductid) || empty($buyseparately)){
                    return array('success'=>false,'ismodify'=>false,'msg'=>'另购产品购买项不一致,请重新选择','reason'=>'');
                }
                $buyseparatelyarray=explode(',',$buyseparately);
                $buyseparatelyarray=array_unique($buyseparatelyarray);
                $newextendarray1=array_diff($extraproductid,$buyseparatelyarray);
                $newextendarray2=array_diff($buyseparatelyarray,$extraproductid);
                if(count($newextendarray1)>0 || count($newextendarray2)>0){
                    return array('success'=>false,'ismodify'=>false,'msg'=>'另购产品购买项不一致,请重新选择','reason'=>'');
                }
                $ajax_extraproductidnum=$request->get('ajax_extraproductidnum');
                $ajax_extraproductidnumarr=array();
                foreach($ajax_extraproductidnum as $value){
                    $ajax_extraproductidnumval=explode(":",$value);
                    $ajax_extraproductidnumarr[$ajax_extraproductidnumval[0]]=$ajax_extraproductidnumval[1];
                }
                foreach($productnames as $value){
                    if($ajax_extraproductidnumarr[$value['productID']]!=$value['productCount']){
                        return array('success'=>false,'ismodify'=>false,'msg'=>$value['productTitle'].'数量不一致,合同数量为:'.$ajax_extraproductidnumarr[$value['productID']].'下单数量为'.$value['productCount'],'reason'=>'');
                    }
                }
            }
            return $result_up;
        }

        //另购产品

        $arr_extraproductid=$request->get('extraproductid');
        if(!empty($arr_extraproductid) && !is_array($arr_extraproductid)){
            $arr_extraproductid=explode(',',$arr_extraproductid);
        }

        $type = "下单";
        if($classtype == 'buy'){
            $type = "购买";
        }else if($classtype == 'renew'){
            $type = "续费";
        }else if($classtype == 'upgrade'){
            $type = "升级";
        }else if($classtype == 'againbuy'){
            $type = "另购";
        }else if($classtype == 'degrade'){
            $type = "降级";
        }else{
            return array('success'=>false,'ismodify'=>false, 'msg'=>'T云产品必须先在移动端'.$type.'才可签收本合同','reason'=>'');
        }

        //产品选择验证
        if($classtype == 'buy' || $classtype == 'upgrade' || $classtype == 'renew' || $classtype == 'degrade') {
            //验证合同信息是否和移动端购买信息一致
            if (empty($arr_productid) || count($arr_productid) == 0 || count($arr_productid) > 1) {
                return array('success'=>false,'ismodify'=>false,'msg'=>'T云'.$type.'合同必须要选择一个自定义产品,请确认','reason'=>'');
            }
        }else{
            //验证合同信息是否和移动端购买信息一致
            if (empty($arr_extraproductid) || count($arr_extraproductid) == 0) {
                return array('success'=>false, 'ismodify'=>false,'msg'=>'T云'.$type.'合同至少要选择一个另购产品,请确认','reason'=>'');
            }
        }

        //获取T云购买产品
        if($classtype != "againbuy"){
            $tyun_product_sql = "SELECT 1 FROM vtiger_products
                INNER JOIN vtiger_crmentity ON(vtiger_products.productid=vtiger_crmentity.crmid)
                WHERE vtiger_crmentity.deleted=0 AND vtiger_products.istyun=1 AND vtiger_products.istyunsite=0 AND FIND_IN_SET(vtiger_products.productid,?)";
            $tyun_products_result = $adb->pquery($tyun_product_sql, array(implode(',',$arr_productid)));
            $res_tyun_products = $adb->num_rows($tyun_products_result);
            if($res_tyun_products == 0) {
                return array('success'=>false,'ismodify'=>false, 'msg'=>'请确认选中的产品是否为T云产品','reason'=>'');
            }
        }

        //验证合同信息
        $tyun_sql = "SELECT M.activationcodeid,M.activecode AS m_activecode,
                            M.classtype AS m_classtype,
                            M.contractname AS m_contractno,
                            M.customerid AS m_customerid,
                            M.customername AS m_customername,
                            M.productlife AS m_productlife,
                            IF(M.buyserviceinfo='[]','',M.buyserviceinfo) AS m_buyserviceinfo,
                            M.contractstatus,
                            MP.productid AS m_productid,
                            MP.productname AS m_productname,
                            PP.productid AS p_productid,
                            PP.productname AS p_productname,
                            P.contractname AS p_contractname,
                            P.customerid AS p_customerid,
                            P.customername AS p_customername,
                            M.usercode,
                            M.status
                    FROM vtiger_activationcode M
                    LEFT JOIN vtiger_products MP ON(M.productid=MP.tyunproductid)
                    LEFT JOIN vtiger_activationcode P ON(M.buyid=P.activationcodeid)
                    LEFT JOIN vtiger_products PP ON(P.productid=PP.tyunproductid)
                    WHERE M.status in (0,1) AND M.contractid=? AND M.contractid>0 limit 1";
        $tyun_result = $adb->pquery($tyun_sql, array($request->get('record')));
        $res_num = $adb->num_rows($tyun_result);
        if($res_num == 0) {
            return array('success'=>false,'ismodify'=>false, 'msg'=>'T云产品必须先在移动端'.$type.'才可签收本合同','reason'=>'');
        }

        $row_data = $adb->query_result_rowdata($tyun_result,0);

        $m_activecode = $row_data['m_activecode'];
        $crm_m_productid = $row_data['m_productid'];
        $crm_p_productid = $row_data['p_productid'];
        $crm_m_productname = $row_data['m_productname'];
        $tyun_m_customid = $row_data['m_customerid'];
        $tyun_p_customid = $row_data['p_customerid'];
        $tyun_p_contractname = $row_data['p_contractname'];
        $tyun_m_classtype = $row_data['m_classtype'];
        $tyun_m_contract_no = $row_data['m_contractno'];
        $tyun_m_customname = $row_data['m_customername'];
        $tyun_p_customname = $row_data['p_customername'];
        $tyun_activationcodeid = $row_data['activationcodeid'];
        $tyun_contractstatus = $row_data['contractstatus'];
        $tyun_status = $row_data['status'];
        $usercode= $row_data['usercode'];
        //年限
        $tyun_m_productyear = $row_data['m_productlife'];
        //另购服务
        $buyserviceinfo = $row_data['m_buyserviceinfo'];

        $is_active = false;
        //验证是否领取激活码
        if($classtype == 'buy') {
            $is_active = $tyun_status=='1'?true:false;
            //新增-必须要有激活码
            if (empty($m_activecode)) {
                return array('success' => false,'ismodify'=>false, 'msg' => 'T云'.$type.'合同,产品必须领取激活码,请确认','reason'=>'');
            }
        }else{
            //新增-以外不能领取激活码
            if(!empty($m_activecode)){
                return array('success'=>false,'ismodify'=>true, 'msg'=>'T云'.$type.'合同,产品不能领取激活码,请确认','reason'=>$type.'版本不能领取激活码,请确认','id'=>$tyun_activationcodeid,"is_active"=>$is_active);
            }
            //验证是否有原合同
            /* if(empty($tyun_p_contractname)){
                 return array('success'=>false,'ismodify'=>false, 'msg'=>'T云'.$type.'合同,没有查询到对应的原合同,请确认','reason'=>'');
             }*/

            //验证T云账号是否一致
            $tyun_account=$request->get('tyun_account');
            if($usercode != $tyun_account){
                return array('success'=>false,'ismodify'=>false, 'msg'=>'T云账号【'.$usercode.' | '.$tyun_account.'】和移动端'.$type.'不一致,请确认','reason'=>'');
            }
        }

        //验证合同类型是否一致
        if($tyun_m_classtype != $classtype) {
            return array('success'=>false,'ismodify'=>false, 'msg'=>'合同类型和移动端'.$type.'合同类型不一致,请确认','reason'=>'');
        }

        //验证合同编号是否一致
        if(!empty($tyun_m_contract_no) && $tyun_m_contract_no != $contract_no) {
            return array('success'=>false,'ismodify'=>true, 'msg'=>'合同编号和移动端'.$type.'合同编号不一致,请确认','reason'=>$type.'版本合同编号不一致('.$tyun_m_contract_no.'|'.$contract_no.'),请确认','id'=>$tyun_activationcodeid,"is_active"=>$is_active);
        }

        //验证产品和客户
        if($classtype == 'buy' || $classtype == 'upgrade' || $classtype == 'degrade') {
            //验证产品是否一致
            if (!in_array($crm_m_productid, $arr_productid)) {
                return array('success'=>false,'ismodify'=>false, 'msg'=> 'T云'.$type.'合同必须选择【' . $crm_m_productname . '】产品,请确认','reason'=>'');
            }
        }
        if($classtype == 'renew') {
            $tyun_sql="SELECT 
                    productid,
                    productname 
                    FROM vtiger_products 
                    WHERE parentid=(
                    SELECT 
                    MP.productid
                    FROM vtiger_activationcode M
                    LEFT JOIN vtiger_products MP ON(M.productid=MP.tyunproductid)
                    WHERE M.status IN(0,1) AND M.classtype IN('buy','upgrade','degrade') 
                    AND M.receivetime<(SELECT N.receivetime FROM vtiger_activationcode N WHERE N.status IN(0,1) AND N.contractid=?)
                    AND M.usercode=? 
                    ORDER BY M.receivetime DESC LIMIT 1)";
            $tyun_result = $adb->pquery($tyun_sql, array($recordId,$usercode));
            $res_num = $adb->num_rows($tyun_result);
            if($res_num > 0) {
                $row_data = $adb->query_result_rowdata($tyun_result,0);
                $crm_a_productid = $row_data['productid'];
                $crm_a_productname = $row_data['productname'];
                if (!in_array($crm_a_productid, $arr_productid)) {
                    return array('success'=>false,'ismodify'=>false, 'msg'=>'T云'.$type.'合同必须选择【' . $crm_a_productname . '】产品,请确认','reason'=>'');
                }
            }else{
                return array('success'=>false,'ismodify'=>false, 'msg'=>'T云'.$type.'合同选择产品必须要指定对应的原产品,请确认','reason'=>'');
            }
        }

        $accountid = $request->get('sc_related_to');
        $sc_related_to_display = $request->get('sc_related_to_display');
        if($rawData['customerid']){
            if($accountid!=$rawData['customerid']){
                return array('success'=>false,'ismodify'=>false,'msg'=>'订单客户和合同客户不是同一个客户,请确认','reason'=>'');
            }
        }else{
            //验证客户
            if($classtype == 'buy') {
                $tyun_cmp_customid = $tyun_m_customid;
                $tyun_customname = $tyun_m_customname;
            }else{
                $tyun_cmp_customid = $tyun_p_customid;
                $tyun_customname = $tyun_p_customname;
            }

            if (!empty($tyun_cmp_customid) && $accountid != $tyun_cmp_customid ) {
                return array('success'=>false,'ismodify'=>true, 'msg'=>'合同客户和移动端'.$type.'客户【' . $tyun_customname . '】不一致,请确认','reason'=>$type.'版本客户不一致('.$tyun_customname.'|'.$sc_related_to_display.'),请确认','id'=>$tyun_activationcodeid,"is_active"=>$is_active);
            }
        }

        //$checkFlag=false 以下不需要验证，在前端验证===============================================================================================
        if(!$newxCheckFlag) {
            $adb->pquery("UPDATE vtiger_activationcode SET checkstatus=0,reason='' WHERE activationcodeid=?",array($tyun_activationcodeid));
            return array('success'=>true,'ismodify'=>false,'msg'=>'','reason'=>'','id'=>$tyun_activationcodeid,'contractstatus'=>$tyun_contractstatus);
        }
        //==========================================================================================================================================

        //crm产品id
        $arr_productnumber=$request->get('ajax_productnumber');
        if(!empty($arr_productnumber) && !is_array($arr_productnumber)){
            $arr_productnumber=explode(',',$arr_productnumber);
        }
        $arr_productyear=$request->get('ajax_agelife');
        if(!empty($arr_productyear) && !is_array($arr_productyear)){
            $arr_productyear=explode(',',$arr_productyear);
        }

        //验证年限和数量
        //自定义产品验证================================================================================================
        if($classtype == 'buy' || $classtype == 'upgrade' || $classtype == 'renew' || $classtype == 'degrade') {
            foreach ($arr_productid as $v) {
                $tyun_product_rel_sql = "SELECT GROUP_CONCAT(crmid) AS c_productid FROM `vtiger_seproductsrel` WHERE productid=?";
                $tyun_products_rel_result = $adb->pquery($tyun_product_rel_sql, array($v));
                $res_num = $adb->num_rows($tyun_products_rel_result);

                if ($res_num == 0) {
                    //无明细产品
                    if(count($arr_productnumber)>0){
                        $productnumber = $this->getProductNumOrYear($arr_productnumber,$v);
                    }else{
                        $productnumber = $_REQUEST['productnumber'][$v];
                    }

                    if ($productnumber != '1') {
                        return array('success'=>false,'ismodify'=>false, 'msg'=>'T云'.$type.'合同,产品数量必须为1,请确认','reason'=>'');
                    }
                } else {
                    //有明细产品
                    $data_row = $adb->query_result_rowdata($tyun_products_rel_result, 0);
                    $c_productids = $data_row["c_productid"];

                    if(empty($c_productids)){
                        //无明细产品
                        if(count($arr_productnumber)>0){
                            $productnumber = $this->getProductNumOrYear($arr_productnumber,$v);
                        }else {
                            $productnumber = $_REQUEST['productnumber'][$v];
                        }

                        if ($productnumber != '1') {
                            return array('success'=>false,'ismodify'=>false, 'msg'=>'T云'.$type.'合同,产品数量必须为1,请确认','reason'=>'');
                        }

                        if(count($arr_productyear)>0){
                            $productyear = $this->getProductNumOrYear($arr_productyear,$v);
                        }else {
                            $productyear = $_REQUEST['agelife'][$v];
                        }

                        //年限
                        $productyear = round($productyear / 12, 2);//年限
                        if ($tyun_m_productyear != $productyear) {
                            return array('success'=>false,'ismodify'=>true, 'msg'=>'产品年限和移动端'.$type.'不一致,请确认','reason'=>$type.'版本产品年限不一致('.$tyun_m_productyear.'|'.$productyear.'),请确认','id'=>$tyun_activationcodeid,"is_active"=>$is_active);
                        }
                    }else{
                        $arr_c_productid = explode(',', $c_productids);
                        foreach ($arr_c_productid as $v1) {
                            if(count($arr_productnumber)>0){
                                $productnumber = $this->getProductNumOrYear($arr_productnumber,$v1);
                            }else {
                                $productnumber = $_REQUEST['productnumber'][$v1];
                            }

                            if(count($arr_productyear)>0){
                                $productyear = $this->getProductNumOrYear($arr_productyear,$v1);
                            }else {
                                $productyear = $_REQUEST['agelife'][$v1];
                            }

                            //数量
                            if ($productnumber != '1') {
                                return array('success'=>false,'ismodify'=>false, 'msg'=> 'T云'.$type.'合同,产品数量必须为1,请确认','reason'=>'');
                            }
                            //年限
                            $productyear = round($productyear / 12, 2);//年限
                            if ($tyun_m_productyear != $productyear) {
                                return array('success'=>false,'ismodify'=>true, 'msg'=>'产品年限和移动端'.$type.'不一致,请确认','reason'=>$type.'版本产品年限不一致('.$tyun_m_productyear.'|'.$productyear.'),请确认','id'=>$tyun_activationcodeid,"is_active"=>$is_active);
                            }
                        }
                    }
                }
            }
        }

        //验证另购产品验证==============================================================================================
        $this->_logs(array("T云另购服务:".$buyserviceinfo));
        $this->_logs(array("CRM另购服务:".$arr_extraproductid));
        if(!empty($buyserviceinfo) && (empty($arr_extraproductid) || count($arr_extraproductid) == 0)) {
            return array('success'=>false,'ismodify'=>false, 'msg'=>'T云'.$type.'合同,必须要选择另购产品,请确认','reason'=>'');
        }
        if(empty($buyserviceinfo)  && !empty($arr_extraproductid)) {
            return array('success'=>false,'ismodify'=>false, 'msg'=>'T云'.$type.'合同,另购产品和移动端不一致,请确认','reason'=>'');
        }

        if(!empty($buyserviceinfo) && count($arr_extraproductid) > 0) {
            $buyserviceinfo = htmlspecialchars_decode($buyserviceinfo);
            $arr_serviceinfo = json_decode($buyserviceinfo,true);
            if(count($arr_serviceinfo) != count($arr_extraproductid)){
                return array('success'=>false,'ismodify'=>true, 'msg'=>'另购产品和移动端'.$type.'数量不一致,请确认','reason'=>$type.'版本另购服务个数不一致('.count($arr_serviceinfo).'个|'.count($arr_extraproductid).'个),请确认','id'=>$tyun_activationcodeid,"is_active"=>$is_active);
            }

            //查询另购产品匹配的T云产品ID
            $tyun_product_sql = "SELECT GROUP_CONCAT(vtiger_products.tyunproductid) AS tyunproductid FROM vtiger_products
                            INNER JOIN vtiger_crmentity ON(vtiger_products.productid=vtiger_crmentity.crmid)
                            WHERE vtiger_crmentity.deleted=0 AND vtiger_products.istyun=1 AND vtiger_products.istyunsite=0 AND FIND_IN_SET(vtiger_products.productid,?)";
            $tyun_products_result = $adb->pquery($tyun_product_sql, array(implode(',',$arr_extraproductid)));
            $res_tyun_products = $adb->num_rows($tyun_products_result);
            if($res_tyun_products == 0) {
                //验证产品id是否和T云匹配
                return array('success'=>false, 'ismodify'=>false,'msg'=>'T云'.$type.'合同,产品和T云产品未匹配,请确认','reason'=>'');
            }

            $data_row = $adb->query_result_rowdata($tyun_products_result,0);
            $tyunproductids = $data_row["tyunproductid"];
            if(empty($tyunproductids)){
                //验证产品id是否和T云匹配
                return array('success'=>false, 'ismodify'=>false,'msg'=>'T云'.$type.'合同,产品和T云产品未匹配,请确认','reason'=>'');
            }

            $arr_tyunproductid = explode(',',$tyunproductids);
            $new_arr_tyunproductid = array();
            foreach($arr_tyunproductid as $v1){
                if(!empty($v1)){
                    $new_arr_tyunproductid[] = $v1;
                }
            }
            //查询到的关联产品数量是否一致(处理未匹配T云产品ID情况)
            if(count($new_arr_tyunproductid) != count($arr_extraproductid)){
                return array('success'=>false,'ismodify'=>false, 'msg'=>'T云'.$type.'合同,存在和T云未匹配的产品,请确认','reason'=>'');
            }

            //验证产品是否一致
            foreach($new_arr_tyunproductid as $v1) {
                $bl_check = false;
                for($a=0;$a<count($arr_serviceinfo);$a++){
                    $serviceID = $arr_serviceinfo[$a]['ServiceID'];
                    if($serviceID == $v1){
                        $bl_check = true;
                        break;
                    }
                }
                if(!$bl_check){
                    return array('success'=>false, 'ismodify'=>true,'msg'=>'选择产品和移动端'.$type.'不一致,请确认','reason'=>'存在和客户端不一致的产品,请确认',"is_active"=>$is_active);
                }
            }

            //验证另购数量
            $recordActivationCodeModel = Vtiger_Record_Model::getCleanInstance('ActivationCode');
            $arr_tyunserviceitem = $recordActivationCodeModel->getTyunServiceItem(new Vtiger_Request());
            $this->_logs(array("接口获取另购服务:".$arr_tyunserviceitem));
            $this->_logs(array("TYUN另购产品:".$new_arr_tyunproductid));
            $this->_logs(array("CRM另购产品:".$arr_productnumber));
            foreach($new_arr_tyunproductid as $tv1) {
                $tyun_product_sql = "SELECT productid FROM vtiger_products
                            WHERE vtiger_products.istyun=1 AND vtiger_products.istyunsite=0 AND tyunproductid=? LIMIT 1";
                $tyun_products_result = $adb->pquery($tyun_product_sql, array($tv1));
                $data_row = $adb->query_result_rowdata($tyun_products_result,0);

                $cv1 = $data_row['productid'];
                if(empty($cv1)){
                    //验证产品id是否和T云匹配
                    return array('success'=>false, 'ismodify'=>false,'msg'=>'T云'.$type.'合同,产品和T云产品未匹配,请确认','reason'=>'');
                }
                if(count($arr_productnumber)>0){
                    $productnumber = $this->getProductNumOrYear($arr_productnumber,$cv1);
                }else {
                    $productnumber = $_REQUEST['productnumber'][$cv1];
                }
                for($a=0;$a<count($arr_serviceinfo);$a++){
                    $serviceID = $arr_serviceinfo[$a]['ServiceID'];
                    $buyCount = $arr_serviceinfo[$a]['BuyCount'];

                    //查询另购、除以倍数比较
                    /*for($b=0;$b<count($arr_tyunserviceitem);$b++){
                        $serviceID_tmp = $arr_tyunserviceitem[$b]['ServiceID'];
                        $multiple = $arr_tyunserviceitem[$b]['Multiple'];
                        if($serviceID == $serviceID_tmp){
                            $buyCount = bcdiv($arr_serviceinfo[$a]['BuyCount'],$multiple);
                            $this->_logs(array("合同签收另购服务ID:".$serviceID.",转换后数量:".$buyCount));
                            break;
                        }
                    }*/

                    if($serviceID == $tv1){
                        if($productnumber != $buyCount){
                            return array('success'=>false,'ismodify'=>true, 'msg'=>'另购产品数量和移动端'.$type.'不一致,请确认','reason'=>$type.'版本另购数量不一致('.$buyCount.'|'.$productnumber.'),请确认','id'=>$tyun_activationcodeid,"is_active"=>$is_active);
                        }
                        break;
                    }
                }
            }
        }
        return array('success'=>true,'ismodify'=>false,'msg'=>'','reason'=>'','id'=>$tyun_activationcodeid,'contractstatus'=>$tyun_contractstatus);
    }

 /**
     * 合同签收通知71360平台
     * @param $recordModel
     */
    public function tyun71360ContractConfirm(){
        global $adb,$tyunweburl,$sault;
        $query='SELECT usercodeid FROM vtiger_activationcode WHERE contractid=? AND `status` in(0,1) AND usercodeid>0 AND pushstatus=0 AND comeformtyun=1 LIMIT 1';
        $result=$adb->pquery($query,array($this->getId()));
        if($adb->num_rows($result)){
            $usercodeid=$result->fields['usercodeid'];
            $array=array("userID"=>$usercodeid,"ContractCode"=>$this->get('contract_no'));
            $postData=json_encode($array);
            $ContractConfirm=$tyunweburl."api/micro/order-basic/v1.0.0/api/Order/ContractConfirm";
            $time=time().'123';
            $sault1=$time.$sault;
            $token=md5($sault1);
            $curlset=array(CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json",
                "S-Request-Token:".$token,
                "S-Request-Time:".$time));
            $res = $this->https_requestTweb($ContractConfirm, $postData,$curlset);
            $resultData=json_decode($res,true);
			$this->_logs(array('tyun71360ContractConfirm','url'=>$ContractConfirm,'postData'=>$postData,'resultData'=>$resultData));
            if($resultData['code']==200){
				$adb->pquery('UPDATE vtiger_activationcode SET pushstatus=1 WHERE contractid=? AND `status` in(1,0)',array($this->getId()));
                return false;
            }
            return true;

        }
    }
    /*
    *根据合同编号获取合同对应的客户，客户名称金额，年限
     */
    public function getContractByContractNo($request){
        global $adb;
        $contractno=$request->get('contractno');
        $query='SELECT servicecontractsid,contract_no,total,(SELECT accountname FROM vtiger_account WHERE accountid=vtiger_servicecontracts.sc_related_to) AS accountname,vtiger_servicecontracts.modulestatus,
                    (SELECT agelife FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid=vtiger_servicecontracts.servicecontractsid LIMIT 1) as years 
                    FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE vtiger_crmentity.deleted=0 AND contract_no =? AND vtiger_servicecontracts.modulestatus<>\'c_cancel\'';
        $result=$adb->pquery($query,array($contractno));
        $returnData=array('success' => false, 'msg' => '没有相关数据！');
        if($adb->num_rows($result)){
            $returnData=array('success' => true, 'data' =>
                            array(
                                'servicecontractsid'=>$result->fields['servicecontractsid'],
                                'contractNo'=>$result->fields['contract_no'],
                                'total'=>(double)$result->fields['total'],
                                'accountName'=>$result->fields['accountname'],
                                'modulestatus'=>vtranslate($result->fields['modulestatus'],'ServiceContracts'),
                                'years'=>($result->fields['years']/12)
                                )
                            );
        }
        return $returnData;
    }


    public function getElecContractEditViewBolcks($request)
    {
        $detailView = new Vtiger_Index_View();
        $viewer = $detailView->getViewer($request);
        $moduleName = $request->getModule();
        $record = $request->get('record');
        if (!empty($record)) {
            $recordModel = $this->record ? $this->record : Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('RECORD_ID', $record);
            $viewer->assign('MODE', 'edit');
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $viewer->assign('MODE', '');
            $viewer->assign('RECORD_ID', '');
        }
        if (!$this->record) {
            $this->record = $recordModel;
        }

        $moduleModel = $recordModel->getModule();
        //读取模块的字段
        $fieldList = $moduleModel->getFields();

        //取交集?还不知道有什么用
        $requestFieldList = array_intersect_key($request->getAll(), $fieldList);
        if(!empty($requestFieldList)){
            foreach($requestFieldList as $fieldName=>$fieldValue){
                $fieldModel = $fieldList[$fieldName];
                if($fieldModel->isEditable()) {
                    $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
                }
            }
        }
        //return 2222;
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel,'Edit');
        $RECORD_STRUCTURE=$recordStructureInstance->getStructure();
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());//UI字段生成位置
        $viewer->assign('BLOCK_FIELDS', $RECORD_STRUCTURE['ELECCONTRACT_INFO']);//UI字段生成位置
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());//执行好多次，真是
        $viewer->assign('RECORD',$recordModel);//编辑页面显示不可编辑字段内容
        $isRelationOperation = $request->get('relationOperation');
        $viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);

        return $viewer->view('ElecContractEditViewBlocks.tpl', $moduleName,true);
    }

    /**
     * 获取所有公司
     *
     * @return array
     */
    public function allCompany(Vtiger_Request $request){
        global $adb;
//        $getcompanysql ="SELECT owncompany,companyid FROM `vtiger_owncompany`";
        $getcompanysql ="SELECT invoicecompany as owncompany,companyid FROM `vtiger_invoicecompany`";
        $company = $adb->pquery($getcompanysql,array());
        $owncompany = array();
        $sums=$adb->num_rows($company);
        if($sums>0){
            while($row = $adb->fetchByAssoc($company)){
                $owncompany[] = array('owncompany'=>$row['owncompany'],'companyid'=>$row['companyid']);
            }
        }
        $owncompanyid = 0;
        if($request->get('userid')){
            $sql2 = 'SELECT companyid FROM vtiger_users WHERE id= ?';
            $result = $adb->pquery($sql2, array($request->get('userid')));
            if($adb->num_rows($result)){
                $row = $adb->raw_query_result_rowdata($result,0);
                $owncompanyid = $row['companyid'];
            }
        }
        $data = array(
            'owncompany'=>$owncompany,
            'companyid'=>$owncompanyid
        );
        return $data;
    }

    /**
     * 获取主体公司的相关信息
     *
     * @return array
     *根据合同编号获取合同对应的客户，客户名称金额，年限
     * 将提单人,领取人,签订人
     * @param $accountid
     */
    public function setServicecontractsListUser($accountid){
        global $adb;
        $serviceQuery='SELECT servicecontractsid,vtiger_servicecontracts.signid,vtiger_servicecontracts.receiveid,vtiger_crmentity.smownerid FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.sc_related_to=?';
        $serviceResult=$adb->pquery($serviceQuery,array($accountid));
        if($adb->num_rows($serviceResult)){
            $array=array();
            $accountQuery = 'SELECT serviceid,tiger_crmentity.smownerid FROM vtiger_account LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid WHERE vtiger_crmentity.deleted=0 AND vtiger_account.accountid=?';
            $shareAccountQuery = 'SELECT userid FROM vtiger_shareaccount WHERE  vtiger_shareaccount.sharestatus=1 AND vtiger_shareaccount.accountid=?';
            $result = $adb->pquery($shareAccountQuery, array($accountid));
            $resultNum = $adb->num_rows($result);
            if ($resultNum) {
                while ($row = $adb->fetch_array($result)) {
                    $array[] = $row['userid'];
                }
            }
            $result = $adb->pquery($accountQuery, array($accountid));
            $array[] = $result->fields['smownerid'];
            if ($result->fields['serviceid'] > 0) {
                $array[] = $result->fields['serviceid'];
            }
            while($row=$adb->fetch_array($serviceResult)){
                $servicecontractsid=$row['servicecontractsid'];
                $temparray=array($row['signid'],$row['receiveid'],$row['smownerid']);
                $temparray=array_merge($temparray,$array);
                $temparray=array_unique($temparray);
                $temparray=array_filter($temparray);
                $str='';
                foreach($temparray as $value){
                    $str.='('.$servicecontractsid.','.$value.'),';
                }
                $str=trim($str,',');
                $sql='DELETE FROM vtiger_servicecontractslistuser WHERE servicecontractsid=?';
                $adb->pquery($sql,array($servicecontractsid));
                $sql='REPLACE INTO vtiger_servicecontractslistuser(servicecontractsid,userid) VALUES'.$str;
                $adb->pquery($sql,array());
            }
        }
    }

    /**
     * 获取放心签的Toeken
     * @return mixed
     */
    public function getFangXinQianToken(){
        global $fangxinqian_appKey,$fangxinqian_appSecret,$fangxinqian_url,$root_directory;
        $cache_token=@file_get_contents($root_directory.'/wtoken.txt');
        $tokens = json_decode($cache_token,true);
        $token=$tokens['access_token'];
        if(empty($tokens) || !isset($tokens['timeout']) || $tokens['timeout']<time()){
            $url=$fangxinqian_url.'common/token?appKey='.$fangxinqian_appKey.'&appSecret='.$fangxinqian_appSecret;
            $output=$this->https_requestcomm($url);
            $tokens = json_decode($output,true);
            if($tokens['success']){
                $data['timeout'] = time()+3580;
                $data['access_token'] = $tokens['data'];
                file_put_contents($root_directory.'/wtoken.txt', json_encode($data));
                $token=$tokens['data'];
            }
        }
        return $token;
    }

    /**
     * 文件的保存
     * @param $filepath
     * @return array
     */
    public function fileUpload($filepath,$isHeader=true){
        global $adb;
        $current_id = $adb->getUniqueID("vtiger_files");
        $upload_file_path = decideFilePath();
        $newfilename=time();
        $navFilePath = $upload_file_path . $current_id . "_" . $newfilename;
        $fileData=$this->getPdfView($filepath,$isHeader);
        file_put_contents($navFilePath,$fileData);
        return array('path'=>$upload_file_path,'type'=>mime_content_type($navFilePath),  'attachmentsid'=>$current_id,'newfilename'=>$newfilename);
    }

    /**
     * 合同附件的保存暂为PDF
     * @param $filepath//附件的URL
     * @param $filestate／／附件的类别
     */
    public function fileSave($filepath,$filestate,$fileName='',$isHeader=true){
        global $current_user,$adb;
        $fileData=$this->fileUpload($filepath,$isHeader);
        $fileData['style'] = $filestate;
        $fileData['description'] = 'ServiceContracts';
        $fileData['relationid'] = $this->getId();
        $accountname = $this->getAccountName($this->getId());
        $fileData['name'] = $this->get('contract_no').$accountname.vtranslate($filestate,'Files').$fileName.'.pdf';
        $fileData['remarks'] = '';
        $fileData['uploader'] = $current_user->id;
        $fileData['uploadtime'] = date('Y-m-d H:i:s');
        $divideNames = array_keys($fileData);
        $divideValues = array_values($fileData);
        $adb->pquery('INSERT INTO `vtiger_files` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')',$divideValues);
        return array('fileid'=>$fileData['attachmentsid'],'fileName'=>$fileData['name']);
    }

    /**
     * 移动端调用合同附件的保存暂为PDF
     */
    public function mobileFileSave(Vtiger_Request $request){
        global $default_language;
        vglobal('default_language', $default_language);
        $currentLanguage = 'zh_cn';
        //Vtiger_Language_Handler::getLanguage();//2.语言设置
        vglobal('current_language',$currentLanguage);
        $module = $request->getModule();//3.1获取参数module的值
        $qualifiedModuleName = $request->getModule(false);//3.2获取module以及父级parent，返回parent:module如module=Vtiger&parent=Settings|Settings:Vtiger

        //4.返回当前用户的语言设置
        if ($qualifiedModuleName) {
            $moduleLanguageStrings = Vtiger_Language_Handler::getModuleStringsFromFile($currentLanguage,$qualifiedModuleName);
            vglobal('mod_strings', $moduleLanguageStrings['languageStrings']);
        }
        $filepath = $request->get('filepath');
        $filestate = $request->get('filestate');
        $fileName = $request->get('fileName');
        $contract_no = $request->get('contract_no');
        $id = $request->get("recordid");
        global $current_user,$adb;
        $fileData=$this->fileUpload($filepath);
        $fileData['style'] = $filestate;
        $fileData['description'] = 'ServiceContracts';
        $fileData['relationid'] = $id;
        $accountname = $this->getAccountName($id);
        $fileData['name'] = $contract_no.$accountname.vtranslate($filestate,'Files').$fileName.'.pdf';
        $fileData['remarks'] = '';
        $fileData['uploader'] = $current_user->id;
        $fileData['uploadtime'] = date('Y-m-d H:i:s');
        $divideNames = array_keys($fileData);
        $divideValues = array_values($fileData);
        $adb->pquery('INSERT INTO `vtiger_files` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')',$divideValues);
        return array('fileid'=>$fileData['attachmentsid'],'fileName'=>$fileData['name']);
    }

    /**
     * 标准合同下单完成后发送合同接口
     * @param $asgs{
        "contractId":1,//放心签平台返回的合同id
        "number":"123456654321"//生成的合同编号
     *  }
     * @return bool|string
     */
    public function contractSend($args){
        global $fangxinqian_url;
        $viewURL=$fangxinqian_url.$this->contractSend;
        return $this->https_requestcomm($viewURL,json_encode($args),$this->getCURLHeader(),true);
    }
    /**
     *根据合同编号获取合同对应的客户，客户名称金额，年限
     */
    public function companyInfo(Vtiger_Request $request){
        global $adb;
        $getcompanysql ="SELECT * FROM `vtiger_company_code` where companyid=?";
        $result = $adb->pquery($getcompanysql,array($request->get('companyid')));
        $owncompany = array();
        $sums=$adb->num_rows($result);
        if(!$sums){
            while($row = $adb->fetchByAssoc($company)){
                $owncompany[] = array('owncompany'=>$row['owncompany'],'companyid'=>$row['companyid']);
            }
        }
        $row = $adb->query_result_rowdata($result,0);
        //根据用户id获取email
        $result2 = $adb->pquery("select email1 from vtiger_users where id = ?",array($request->get("userid")));
        if($adb->num_rows($result2)){
            $row2 = $adb->fetchByAssoc($result2,0);
            $row['email'] = $row2['email1'];
        }
        return $row;
    }

    /**
     * 获取主体公司的相关信息
     *
     * @return array
     */
    public function getCompanyInfoId($companyId){
        global $adb;
        $getcompanysql ="SELECT * FROM `vtiger_company_code` where companyid=?";
        $result = $adb->pquery($getcompanysql,array($companyId));
        $owncompany = array();
        $sums=$adb->num_rows($result);
        if(!$sums){
            return array();
        }
        $row = $adb->query_result_rowdata($result,0);
        return $row;
    }

    /**
     * 执行确认付款
     */
    public function confirmPayment($userid,$recordId,$mobile){
        global $adb,$orderpaymenturl;
        $payCode = '';
        $query="SELECT * FROM vtiger_activationcode WHERE contractid=? AND comeformtyun=1 AND pushstatus=0 AND `status`!=2";
        $result=$adb->pquery($query,array($recordId));
        $productname = '';
        $products = array();
        if($adb->num_rows($result)>0){
            while ($row = $adb->fetch_row($result)){
                if($row['mobile']!=$mobile){
                    return array("success"=>false,'msg'=>'手机号码不一致');
                }
                $flag=1;
                if($row['customerstype']=='clientmigration'){
                    $flag = 2;
                }
                $productname .= $row['productname'].'、';
                $customername = $row['customername'];
                $usercode = $row['usercode'];
                $usercodeid =$row['usercodeid'];
                $payCode = $row['paycode'];
                $productnamestemp=str_replace('&quot;','"',$row['detailproducts']);
                $detailproducts=json_decode($productnamestemp,true);
                $products = empty($products) ? $detailproducts:array_merge($products,$detailproducts);
                $payDate = $row['createdtime'];
            }
        }
        if(!$payCode){
            return array("success"=>false,'msg'=>'付款码不能为空');
        }

        $sql = "select * from vtiger_servicecontracts where servicecontractsid=?";
        $result = $adb->pquery($sql,array($recordId));
        if(!$adb->num_rows($result)){
            return array("success"=>false,'msg'=>'合同不存在');
        }

        $rowData = $adb->query_result_rowdata($result,0);
        switch ($rowData['servicecontractstype']){
            case 'renew':
            case '续费':
                $producttype=4;
                break;
            case 'upgrade':
                $producttype=2;
                break;
            case 'newlyadded':
            case 'buy':
            case '新增':
                $producttype=1;
                break;
            case 'degrade':
                $producttype=3;
                break;
        }

        $contractStatus=0;
        if($rowData['modulestatus']=='c_complete'){
            $contractStatus=1;
        }
        $resparams = array(
            'userID'=>intval($usercodeid),
            'payCode'=>$payCode,
            'productType'=>intval($producttype),
            "totalPrice"=>floatval($rowData['total']),
            'products'=>$products,
            'payDate'=>date("Y-m-d H:i:s"),
            'contractStatus'=>$contractStatus
        );
        $postData=json_encode($resparams);
        $time=time().'123';
        $sault=$time.$this->sault;
        $token=md5($sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $result = $this->https_requestcomm($orderpaymenturl,$postData,$curlset,true);
        $res = json_decode($result,true);
        if($res['code']!=200){
            return array("success"=>false,'msg'=>$res['message']);
        }


        //更新合同是否确认支付为已确认
        $updateSql = "update vtiger_servicecontracts set ispay=1 where servicecontractsid = ?";
        $adb->pquery($updateSql,array($recordId));
        $orderProducts = $res['data']['data'];
        foreach ($orderProducts as $orderProduct){
            if($orderProduct['detail']['OpenDate'] && $orderProduct['detail']['CloseDate'] ){
                $orderCode = $orderProduct['detail']['OrderCode'];
                $sql3 = "update vtiger_activationcode set startdate=?,expiredate=?,contractstatus=1,orderstatus='orderdoused' where ordercode = ?";
                $adb->pquery($sql3,array($orderProduct['detail']['OpenDate'],$orderProduct['detail']['CloseDate'],$orderCode));
            }
        }

        if($rowData['modulestatus']=='c_complete'){
            $adb->pquery('UPDATE vtiger_activationcode SET pushstatus=1 WHERE contractid=? AND `status` in(1,0)',array($recordId));
        }

        //发送短信给客户
        $TyunWebBuyServiceRecordModel = Vtiger_Record_Model::getCleanInstance('TyunWebBuyService');
        $sql2 = "select 1 from vtiger_activationcode where usercode = ? and contractid!= ? and status!=2";
        $result2 = $adb->pquery($sql2,array($usercode,$recordId));
        $is_first_order = 0;
        if(!$adb->num_rows($result2)){
            $is_first_order = 1;
        }
        $sms_data = array(
            "usercode"=>$usercode,
            "productname"=>rtrim($productname,'、'),
            "customername"=>$customername,
            "mobile"=>$mobile,
            "flag"=>$flag,
            "is_first_order"=>$is_first_order
        );

        $TyunWebBuyServiceRecordModel->web71360SendSMS($sms_data);
        return array('success'=>true,'msg'=>'付款成功');
    }

    /**
     * 生成合同
     * @param $request
     */
    public function createServiceContracts($request){
        global $configcontracttypeName,$current_user,$adb;

        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($request->get('userid'));
        $elecContractNo=$this->createElecContractNo($request);
        if($elecContractNo['id']==0){
            return '';
        }
        $current_date=date('Y-m-d');
        $productlife=$request->get('productlife');
        $expire_date=date('Y-m-d',strtotime("+".$productlife." year"));
        $productid=$request->get('productid');//套餐
        $invoicecompany=$request->get('invoicecompany');//主体公司
        $_REQUEST['currentid']='';//合同的ID
        $_REQUEST['suoshugongsi']=array($invoicecompany);
        if ($request->get('classtype') == 'buy') {
            //新购类型电子合同业绩提成人为合同发起人
            $_REQUEST['suoshuren']=array($current_user->id);
        } else {
            /* 续费、升级、降级类型电子合同业绩提成人为客户所属人*/
            $smownerid = 0;
            $accountid = $request->get('accountid');
            if (is_numeric($accountid) && $accountid > 0) {
                /* 获取客户所属人 start */
                $sql = "SELECT smownerid FROM vtiger_crmentity WHERE deleted=0 AND setype='Accounts' AND crmid=? Limit 1";
                $result = $adb->pquery($sql, array($accountid));
                if ($adb->num_rows($result) > 0) {
                    $row = $adb->raw_query_result_rowdata($result, 0);
                    $smownerid = $row['smownerid'];
                }
                /* 获取客户所属人 end */
            }
            //获取不到客户所属人时，业绩提成人设为合同发起人
            if ($smownerid == 0) {
                $smownerid = $current_user->id;
            }
            $_REQUEST['suoshuren'] = array($smownerid);
        }
        $_REQUEST['bili']=array(100);
        $_POST['sc_related_to']=$request->get('accountid');

        $req1=new Vtiger_Request($_POST, $_POST);
        $req1->set('module','ServiceContracts');
        $req1->set('view','Edit');
        $req1->set('action','Save');
        $req1->set('contract_no',$elecContractNo['contracts_no']);
        $req1->set('contractbuytype',$request->get('contractbuytype'));
        $req1->set('signaturetype','eleccontract');
        if($request->get("signaturetype")!='eleccontract'){
            $req1->set('signaturetype',$request->get("signaturetype"));
        }
        $req1->set('contractattribute',$request->get('contractattribute'));
        $req1->set('clientproperty',$request->get('clientproperty'));
        $req1->set('sc_related_to' ,$request->get('accountid'));
        $req1->set('parent_contracttypeid',$request->get('parent_contracttypeid'));
        $req1->set('contract_type' ,$configcontracttypeName);
        if($request->get('contract_type')){
            $req1->set('contract_type' ,$request->get('contract_type'));
        }
        $req1->set('servicecontractstype',$request->get('servicecontractstype'));
        $req1->set('contract_classification',$request->get('contract_classification'));
        $req1->set('isstandard','on');
        $req1->set('assigned_user_id',$current_user->id);
        $req1->set('Receivedate',$current_date);
        $req1->set('Signid' ,$current_user->id);
        $req1->set('signdate' ,$current_date);
        $req1->set('Receiveid',$current_user->id);
        $req1->set('Returndate',$current_date);
        $req1->set('currencytype', '人民币');
        $req1->set('total', $request->get('totalprice'));
        $req1->set('multitype', '0');
        $req1->set('contractstate' ,'0');
        $req1->set('effectivetime' ,$expire_date);
        $req1->set('actualeffectivetime', $expire_date);
        $req1->set('isautoclose', 'on');
        $req1->set('cantheinvoice' ,'on');
        $req1->set('invoicecompany' ,$invoicecompany);
        $req1->set('iscomplete' ,'0');//是否已完成
        $req1->set('billcontent' ,'');//开票内容
        $req1->set('modulestatus',$request->get('modulestatus'));//合同状态
        $req1->set('remark' ,$request->get('remark'));
        $req1->set('productid' ,$productid);
        $req1->set('elereceiver', $request->get('elereceiver'));
        $req1->set('elereceivermobile', $request->get('elereceivermobile'));
        $req1->set('eleccontractstatus', $request->get('eleccontractstatus'));
        $req1->set('originator', $request->get('originator'));
        $req1->set('originatormobile', $request->get('originatormobile'));
        $req1->set('eleccontracttpl', $request->get('eleccontracttpl'));
        $req1->set('eleccontracttplid', $request->get('eleccontracttplid'));
        $req1->set('relatedattachment', $request->get('relatedattachment'));
        $req1->set('eleccontractid', $request->get('eleccontractid'));
        $req1->set('companycode', $request->get('companycode'));
        $ressorder=new Vtiger_Save_Action();
        $recordModel=$ressorder->saveRecord($req1);
        $accountid=$request->get('accountid');
        $servicecontractsid=$recordModel->getId();
        $request->set('servicecontractsid',$servicecontractsid);
        $ispay = $request->get('ispay')?1:0;
        $adb->pquery("UPDATE vtiger_servicecontracts SET contract_no=?,servicecontractsprintid=?,fromactivity=?,activityenddate=?,comeformtyun=?,ispay=? WHERE servicecontractsid=?",
            array($elecContractNo['contracts_no'],$elecContractNo['id'],$request->get('fromactivity'),$request->get('activityenddate'),$request->get('comeformtyun'),$ispay,$servicecontractsid));
        $adb->pquery("UPDATE vtiger_crmentity SET label=? WHERE crmid=?",array($elecContractNo['contracts_no'],$servicecontractsid));
        $elecContractNo['id']=$servicecontractsid;

        return $elecContractNo;
    }



    /**
     * 生成合同编号
     */
    public function createElecContractNo($request){
        global $adb;

        global $current_user;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($request->get('userid'));

        $invoicecompany=$request->get('invoicecompany');
        $productid=$request->get('productid');
        $otherproductids=$request->get('otherproductids');
        $servicecontractstype=$request->get('classtype');
        $codePrfix = $request->get('codeprefix');
        $query="SELECT products_code FROM vtiger_products_code LEFT JOIN vtiger_products_code_productid ON vtiger_products_code_productid.products_codeid=vtiger_products_code.products_codeid WHERE  vtiger_products_code_productid.servicecontractstype=? ";
        if($request->get("isWK")){
            $wkCode = $request->get("wkcode");
            $query .= ' and vtiger_products_code_productid.tyunproductid=? limit 1';
            $result=$adb->pquery($query,array($servicecontractstype,$wkCode));
            if($adb->num_rows($result)!=1){
                return array('id'=>0);
            }
            $productclass=$adb->query_result($result,0,'products_code');

        }else{
            if($request->get('ispackage')){
                $codePrfix = $codePrfix?$codePrfix:'c';
                $query .= 'and vtiger_products_code_productid.productid=? and vtiger_products_code_productid.ispackage=1 and vtiger_products_code_productid.tyunproductid=? limit 1';
                $result=$adb->pquery($query,array($servicecontractstype,$productid,$codePrfix.$productid));
                if(in_array($codePrfix,array("co","so"))){
                    if($adb->num_rows($result)!=1){
//                        return array('id'=>0);
//                        $productclass='TYUNLINE';
                        switch ($servicecontractstype){
                            case 'buy':
                                $productclass='TYUN';
                                break;
                            default:
                                $productclass='TYUNXF';
                                break;
                        }
                    }else{
                        $productclass=$adb->query_result($result,0,'products_code');
                    }
                }else{
                    if($adb->num_rows($result)!=1){
                        return array('id'=>0);
                    }
                    $productclass=$adb->query_result($result,0,'products_code');
                }
            }else{
                switch ($servicecontractstype){
                    case 'buy':
                        $productclass='TYUN';
                        break;
                    default:
                        $productclass='TYUNXF';
                        break;
                }
            }
        }

        $query="SELECT company_code FROM `vtiger_company_code` WHERE companyfullname=? limit 1";
        $result=$adb->pquery($query,array($invoicecompany));
        $company_codeno=$adb->query_result($result,0,'company_code');
        $_POST['sc_related_to']=4;
        $_POST['quantity']=1;
        $_POST['company_code']=!empty($company_codeno)?$company_codeno:'ZD';
        $_POST['products_code']=$productclass;
        //$_POST['products_code']='TYUN';
        $_POST['contract_template']=$request->get('contract_template');
//        $_POST['contract_template']='ZDY';
        $_POST['signstatus']=1;
        $request1=new Vtiger_Request($_POST, $_POST);
        $request1->set('module','SContractNoGeneration');
        $request1->set('view','Edit');
        $request1->set('action','Save');
        $ressorder=new Vtiger_Save_Action();
        $recordModel=$ressorder->saveRecord($request1);
        $id=$recordModel->getId();
        //$adb->pquery("UPDATE vtiger_servicecontracts_print SET nostand=1,smownerid=? WHERE scontractnogenerationid=?",array($id));
        $query='SELECT servicecontractsprintid,servicecontracts_no FROM `vtiger_servicecontracts_print` WHERE scontractnogenerationid=?';
        $result=$adb->pquery($query,array($id));
        return array('id'=>$result->fields['servicecontractsprintid'],'contracts_no'=>$result->fields['servicecontracts_no']);
    }

    /**
     * 合同产品列表
     */
    public function createSalesorderproductsrel($request){
        $this->_logs(array('createSalesorderproductsrel',$request));
        global $current_user;
        global $adb;
        $paycode=$request->get('paycode');
        $accountid=$request->get('accountid');
        $servicecontractsid=$request->get('servicecontractsid');
        $ordercode = $request->get('ordercode');
        $query="SELECT a.*,b.contract_classification FROM vtiger_activationcode a left join vtiger_servicecontracts b on a.contractid =b.servicecontractsid  WHERE a.paycode=? AND a.status!=2 AND a.comeformtyun=1 ";
        if($ordercode){
            $query .= "and ordercode = '".$ordercode."'";
        }

        $query .= ' order by activationcodeid desc';
        $result=$adb->pquery($query,array($paycode));
        if($adb->num_rows($result)) {
            $tflag=true;
            $packproductid=array();
            $otherproductid=array();
            while ($row = $adb->fetch_row($result)){
                $datas[] = $row;
                if($row['classtype']=='renew'){
                    $tflag=false;
                }
                if($row['productid']>0){
                    $packproductid[]=$row;
                }else{
                    $otherproductid[]=$row;
                }
            }
            foreach ($packproductid as $data){
                $productid=$data['productid'];
                if ($tflag && in_array($data['classtype'],array('upgrade','degrade','buy','crenew','cupgrade','cdegrade'))) {
                    $datapack = $this->getTyunWebBuy($data,$data['productid']);
                    $json_datapack = json_decode($datapack, true);
                    if($json_datapack['code']==200) {
                        foreach ($json_datapack['data'][0]['ProductSpecifications'] as $value) {
                            $array = array('salesorderproductsrelid' => $adb->getUniqueID('vtiger_salesorderproductsrel'),
                                'productid' => $value['ID'],
                                'producttype' => 'std',
                                'createtime' => date('Y-m-d H:i:s'),
                                'creatorid' => $current_user->id,
                                'salesorderproductsrelstatus' => 'pass',
                                'ownerid' => $current_user->id,
                                'servicecontractsid' => $servicecontractsid,
                                'accountid' => $accountid,
                                'realmarketprice' => 0,
                                'marketprice' => 0,
                                'productcomboid' => $productid,
                                'productsolution' => '',
                                'producttext' => '',
                                'productnumber' => 1,
                                'agelife' => $data['productlife']*12,
                                'standard' => "std",
                                'thepackage' =>$json_datapack['data'][0]['Package']['Title']?$json_datapack['data'][0]['Package']['Title']:$json_datapack['data']['package']['Title'],
                                'isextra' => 0,
                                'prealprice' => 0,
                                'punit_price' => 0,
                                'pmarketprice' =>0,
                                'costing' => 0,
                                'purchasemount' => 0,
                                'multistatus' => '1',
                                'vendorid' =>0,
                                'suppliercontractsid' =>0,
                                'productname' => $value['ProductTitle'],
                                'opendate' => $data['startdate'],
                                'closedate' => $data['expiredate'],
                                'istyunweb' => 1
                            );
                            $adb->pquery("insert into vtiger_salesorderproductsrel (" . implode(",", array_keys($array)) . ") values(" . generateQuestionMarks($array) . ")", $array);
                        }

                    }
                }elseif($data['classtype'] == 'renew'){
                    $datapack = $this->getTyunWebRenew($data);
                    $json_datapack = json_decode($datapack, true);
                    $products=array();
                    if($json_datapack['code']==200) {
                        $json_datapack_data=$json_datapack['data'];
                        if($productid==$json_datapack_data['package']['ID'] ){
                            if(!empty($json_datapack_data['packageSpecificationList'])){
                                foreach($json_datapack_data['packageSpecificationList'] as $value){
                                    if($value['CanRenew']){
                                        $array = array('salesorderproductsrelid' => $adb->getUniqueID('vtiger_salesorderproductsrel'),
                                            'productid' => $value['ProductID'],
                                            'producttype' => 'std',
                                            'createtime' => date('Y-m-d H:i:s'),
                                            'creatorid' => $current_user->id,
                                            'salesorderproductsrelstatus' => 'pass',
                                            'ownerid' => $current_user->id,
                                            'servicecontractsid' => $servicecontractsid,
                                            'accountid' => $accountid,
                                            'realmarketprice' => 0,
                                            'marketprice' => 0,
                                            'productcomboid' =>$productid,
                                            'productsolution' => '',
                                            'producttext' => '',
                                            'productnumber' => 1,
                                            'agelife' => $data['productlife']*12,
                                            'standard' => "std",
                                            'thepackage' => $json_datapack['data'][0]['Package']['Title']?$json_datapack['data'][0]['Package']['Title']:$json_datapack['data']['package']['Title'],
                                            'isextra' => 0,
                                            'prealprice' => 0,
                                            'punit_price' => 0,
                                            'pmarketprice' =>0,
                                            'costing' => 0,
                                            'purchasemount' => 0,
                                            'multistatus' => '1',
                                            'vendorid' =>0,
                                            'suppliercontractsid' =>0,
                                            'productname' => $value['ProductTitle'],
                                            'opendate' => $data['startdate'],
                                            'closedate' => $data['expiredate'],
                                            'istyunweb' => 1
                                        );
                                        $adb->pquery("insert into vtiger_salesorderproductsrel (" . implode(",", array_keys($array)) . ") values(" . generateQuestionMarks($array) . ")", $array);
                                    }
                                }
                            }else{
                                $array = array('salesorderproductsrelid' => $this->db->getUniqueID('vtiger_salesorderproductsrel'),
                                    'productid' => $json_datapack_data['package']['ID'],
                                    'producttype' => 'std',
                                    'createtime' => date('Y-m-d H:i:s'),
                                    'creatorid' => $current_user->id,
                                    'salesorderproductsrelstatus' => 'pass',
                                    'ownerid' => $current_user->id,
                                    'servicecontractsid' => $servicecontractsid,
                                    'accountid' => $accountid,
                                    'realmarketprice' => 0,
                                    'marketprice' => 0,
                                    'productcomboid' =>$productid,
                                    'productsolution' => '',
                                    'producttext' => '',
                                    'productnumber' => 1,
                                    'agelife' => $data['productlife']*12,
                                    'standard' => "std",
                                    'thepackage' => $json_datapack['data'][0]['Package']['Title']?$json_datapack['data'][0]['Package']['Title']:$json_datapack['data']['package']['Title'],
                                    'isextra' => 0,
                                    'prealprice' => 0,
                                    'punit_price' => 0,
                                    'pmarketprice' =>0,
                                    'costing' => 0,
                                    'purchasemount' => 0,
                                    'multistatus' => '1',
                                    'vendorid' =>0,
                                    'suppliercontractsid' =>0,
                                    'productname' => $json_datapack_data['package']['Title'],
                                    'opendate' => $data['startdate'],
                                    'closedate' => $data['expiredate'],
                                    'istyunweb' => 1
                                );
                                $adb->pquery("insert into vtiger_salesorderproductsrel (" . implode(",", array_keys($array)) . ") values(" . generateQuestionMarks($array) . ")", $array);
                            }
                        }else{
                            $flag=true;
                            foreach($json_datapack_data['packageSpecificationList'] as $value){
                                if($value['CanRenew'] && $value['ProductID']==$productid){
                                    $array = array('salesorderproductsrelid' => $this->db->getUniqueID('vtiger_salesorderproductsrel'),
                                        'productid' => $value['ProductID'],
                                        'producttype' => 'std',
                                        'createtime' => date('Y-m-d H:i:s'),
                                        'creatorid' => $current_user->id,
                                        'salesorderproductsrelstatus' => 'pass',
                                        'ownerid' => $current_user->id,
                                        'servicecontractsid' => $servicecontractsid,
                                        'accountid' => $accountid,
                                        'realmarketprice' => 0,
                                        'marketprice' => 0,
                                        'productcomboid' =>$productid,
                                        'productsolution' => '',
                                        'producttext' => '',
                                        'productnumber' => 1,
                                        'agelife' => $data['productlife']*12,
                                        'standard' => "std",
                                        'thepackage' => '--',
                                        'isextra' => 0,
                                        'prealprice' => 0,
                                        'punit_price' => 0,
                                        'pmarketprice' =>0,
                                        'costing' => 0,
                                        'purchasemount' => 0,
                                        'multistatus' => '1',
                                        'vendorid' =>0,
                                        'suppliercontractsid' =>0,
                                        'productname' => $value['ProductTitle'],
                                        'opendate' => $data['startdate'],
                                        'closedate' => $data['expiredate'],
                                        'istyunweb' => 1
                                    );
                                    $adb->pquery("insert into vtiger_salesorderproductsrel (" . implode(",", array_keys($array)) . ") values(" . generateQuestionMarks($array) . ")", $array);
                                    $flag=false;
                                    break;
                                }
                            }
                        }
                        if($flag){
                            foreach($json_datapack_data['productSpecificationList'] as $value){
                                if($value['ProductID']==$productid){
                                    $array = array('salesorderproductsrelid' => $this->db->getUniqueID('vtiger_salesorderproductsrel'),
                                        'productid' => $value['ProductID'],
                                        'producttype' => 'std',
                                        'createtime' => date('Y-m-d H:i:s'),
                                        'creatorid' => $current_user->id,
                                        'salesorderproductsrelstatus' => 'pass',
                                        'ownerid' => $current_user->id,
                                        'servicecontractsid' => $servicecontractsid,
                                        'accountid' => $accountid,
                                        'realmarketprice' => 0,
                                        'marketprice' => 0,
                                        'productcomboid' =>$productid,
                                        'productsolution' => '',
                                        'producttext' => '',
                                        'productnumber' => 1,
                                        'agelife' => $data['productlife']*12,
                                        'standard' => "std",
                                        'thepackage' => '--',
                                        'isextra' => 0,
                                        'prealprice' => 0,
                                        'punit_price' => 0,
                                        'pmarketprice' =>0,
                                        'costing' => 0,
                                        'purchasemount' => 0,
                                        'multistatus' => '1',
                                        'vendorid' =>0,
                                        'suppliercontractsid' =>0,
                                        'productname' => $value['ProductTitle'],
                                        'opendate' => $data['startdate'],
                                        'closedate' => $data['expiredate'],
                                        'istyunweb' => 1
                                    );
                                    $adb->pquery("insert into vtiger_salesorderproductsrel (" . implode(",", array_keys($array)) . ") values(" . generateQuestionMarks($array) . ")", $array);
                                    break;
                                }
                            }
                        }

                    }
                }
            }
            foreach($otherproductid as $data){
                $productnames=str_replace('&quot;','"',$data['productnames']);
                $productnames=json_decode($productnames,true);
                $array = array('salesorderproductsrelid' => $adb->getUniqueID('vtiger_salesorderproductsrel'),
                    'productid' => $data['buyseparately'],
                    'producttype' => 'std',
                    'createtime' => date('Y-m-d H:i:s'),
                    'creatorid' => $current_user->id,
                    'salesorderproductsrelstatus' => 'pass',
                    'ownerid' => $current_user->id,
                    'servicecontractsid' => $servicecontractsid,
                    'accountid' => $accountid,
                    'realmarketprice' => 0,
                    'marketprice' => 0,
                    'productcomboid' => $data['buyseparately'],
                    'productsolution' => '',
                    'producttext' => '',
                    'productnumber' => $productnames[0]['productCount'],
                    'agelife' => $data['productlife']*12,
                    'standard' => "std",
                    'thepackage' => '',
                    'isextra' => 0,
                    'prealprice' => 0,
                    'punit_price' => 0,
                    'pmarketprice' =>0,
                    'costing' => 0,
                    'purchasemount' => 0,
                    'multistatus' => '1',
                    'vendorid' =>0,
                    'suppliercontractsid' =>0,
                    'productname' => $data['productname'],
                    'opendate' => $data['startdate'],
                    'closedate' => $data['expiredate'],
                    'istyunweb' => 1
                );
                $adb->pquery("insert into vtiger_salesorderproductsrel (" . implode(",", array_keys($array)) . ") values(" . generateQuestionMarks($array) . ")", $array);
            }
        }
    }

    /**
     * 匹配电子合同模板
     *
     * @param $data
     * @return array
     *
     * junWei.nie
     *
     * 2020/04/15
     */
    public function matchElecContractTemplate($data){
        global $fangxinqian_url;
        $myData=array(
            "productCode"=>$data['productcode'],
            "purchaseType"=>intval($data['servicecontractstype']),
            "orderType"=>$data['ordertype']
        );
        $gettemplateurl = $fangxinqian_url.'tyun/get_template';
        $this->_logs(array('matchElecContractTemplatePostData',$myData));
        $res = $this->https_requestcomm($gettemplateurl,json_encode($myData), $this->getCURLHeader(),true);
        $this->_logs(array('matchElecContractTemplateReturnData',$res));
        $resultArray =  json_decode($res,true);
        if($resultArray['success']){
            return $resultArray['data'];
        }
        return array();
    }

    public static function product2productcode(){
        $db=PearDatabase::getInstance();
        $sql = "select a.products_code_productidid,b.products_code,a.productname,a.servicecontractstype from vtiger_products_code_productid a left join vtiger_products_code b on a.products_codeid=b.products_codeid order by a.products_code_productidid desc";
        return $db->pquery($sql,array());
    }

    public static function productcode(){
        $db=PearDatabase::getInstance();
        $sql = "select products_codeid,products_code from vtiger_products_code order by sortorderid ";
        return $db->pquery($sql,array());
    }


    /**
     * 发送电子合同相关短信
     *
     * @param Vtiger_Request $request
     * @return mixed
     */
    function sendSMS($data){
        global $tyunweburlsms,$sault;
        $tyunweburl1=$tyunweburlsms.'api/app/aggregateservice-api/v1.0.0/api/SMS/SendMobileMessage';
        $time=time().'123';
        $sault1=$time.$sault;
        $token=md5($sault1);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        switch ($data['statustype']){
            case "withdraw":
                $content = "您好,珍岛集团已撤回合同《".$data['eleccontracttpl']."》";
                break;
            default:
                $content = "您好,珍岛集团向您发起一份电子合同《".$data['eleccontracttpl']."》,点击链接： ".$this->elecContractUrl." 前往签署。如有疑问，请联系我方和您对接的商务人员,谢谢";
                break;
        }

        $rebinddata=json_encode(array("mobile"=>$data['mobile'],"content"=>'【珍岛集团】'.$content));
        $Repson=$this->https_requestcomm($tyunweburl1,$rebinddata,$curlset,true);
        return json_decode($Repson,true);
    }

    /**
     * 短信发送
     * @param $data
     * @return bool|string
     */
    function sendSMSComm($data){
        global $tyunweburlsms,$sault;
        $tyunweburl1=$tyunweburlsms.'api/app/aggregateservice-api/v1.0.0/api/SMS/SendMobileMessage';
        $time=time().'123';
        $sault1=$time.$sault;
        $token=md5($sault1);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $rebinddata=json_encode(array("mobile"=>$data['mobile'],"content"=>'【珍岛集团】'.$data['content']));
        return $this->https_requestTweb($tyunweburl1,$rebinddata,$curlset);

    }
    /**
     * 发送电子合同相关短信
     *
     * @param Vtiger_Request $request
     * @return mixed
     */
    function sendSMS2(Vtiger_Request $request){
        global $tyunweburlsms,$sault,$adb;
        $tyunweburl1=$tyunweburlsms.'api/app/aggregateservice-api/v1.0.0/api/SMS/SendMobileMessage';
        $time=time().'123';
        $sault1=$time.$sault;
        $token=md5($sault1);
        $recordid = $request->get('recordid');
        $statustype = $request->get('statustype');
        $sql = "select eleccontractid,contract_no,eleccontracttpl,elereceivermobile from vtiger_servicecontracts where servicecontractsid = ?";
        $result = $adb->pquery($sql,array($recordid));
        if(!$adb->num_rows($result)){
            return false;
        }
        $row = $adb->query_result_rowdata($result,0);

        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        switch ($statustype){
            case "withdraw":
                $content = "您好,珍岛集团已撤回合同《".$row['eleccontracttpl']."》";
                break;
            default:
                $content = "您好,珍岛集团向您发起一份电子合同《".$row['eleccontracttpl']."》,点击链接：".$this->elecContractUrl." 前往签署。如有疑问，请联系我方和您对接的商务人员,谢谢";
                break;
        }

        $rebinddata=json_encode(array("mobile"=>$row['elereceivermobile'],"content"=>'【珍岛集团】'.$content));
        $this->_logs(array('url'=>$tyunweburl1,'params'=>$rebinddata));
        $Repson=$this->https_requestTweb($tyunweburl1,$rebinddata,$curlset);
        $this->_logs(json_decode($Repson,true));
        return json_decode($Repson,true);
    }

    /**
     * 向放心签同步产品数据
     */
    public function syncProductToFangXinQian($data){
        global $fangxinqian_url;
        //向放心签同步产品数据
        $postData = array(
            'productName'=>$data['productName'],
            "productCode"=>$data['productCode']
        );
        $this->https_requestTweb($fangxinqian_url.$this->commonAddProduct, json_encode($postData),$this->getCURLHeader());
    }

    /**
     * 发送电子合同
     */
    public function sendElecContract($recordid){
        global $adb,$fangxinqian_url;
        $sql = "select * from vtiger_servicecontracts where servicecontractsid = ?";
        $result = $adb->pquery($sql,array($recordid));
        if(!$adb->num_rows($result)){
            return false;
        }
        $row = $adb->query_result_rowdata($result,0);
        $postData = array(
            'contractId'=>$row['eleccontractid'],
            'number'=>$row['contract_no']
        );
        $result = $this->https_requestcomm($fangxinqian_url.$this->contractSend, json_encode($postData),$this->getCURLHeader(),true);
        $res = json_decode($result,true);
        if($res['success']){
            //发送电子邮件给商务
            $tyunWebRecordModel = TyunWebBuyService_Record_Model::getCleanInstance("TyunWebBuyService");
            $sendEmailParams = array(
                'contract_no'=>$row['contract_no'],
                'userid'=>$row['receiveid'],
                'customer_name'=>$row['customer_name'],
                'receivedate'=>date('Y-m-d H:i:s'),
                'comeformtyun'=>$row['comeformtyun'],
                'fromactivity'=>$row['fromactivity'],
                'servicecontractstype'=>$row['servicecontractstype'],
                'elereceiver'=>$row['elereceiver'],
                'elereceivermobile'=>$row['elereceivermobile'],
                'modulestatus'=>$row['modulestatus']
            );
            $tyunWebRecordModel->elecContractStatusSendMail($sendEmailParams);

            $recordModel = ServiceContracts_Record_Model::getInstanceById($recordid,'ServiceContracts');
            $recordModel->fileSave($res['data'],'files_style8','单方合同');
            $data  = array(
                'eleccontracttpl'=>$row['eleccontracttpl'],
                'url'=>$this->elecContractUrl,
                'mobile'=>$row['elereceivermobile']
            );
            $this->sendSMS($data);
            return true;
        }
        return false;
    }


    /**
     * 修改电子合同状态
     */
    public function updateElecContractStatus(Vtiger_Request $request){
        $recordId = $request->get("recordid");
        $elecContractStatus = $request->get('eleccontractstatus');
        $moduleStatus = $request->get('modulestatus');
        global $adb;
        $sql = "update vtiger_servicecontracts set eleccontractstatus = '".$elecContractStatus."',modulestatus='".$moduleStatus."'  where servicecontractsid = ?";
        $result = $adb->pquery($sql,array($recordId));
        if($adb->num_rows($result)){
            return true;
        }
        return false;
    }

    #电子合同审核工作流
    function elecContractVerify(Vtiger_Request $request){
        global $isallow,$adb,$current_user;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($request->get('userid'));

        $isallow=array('ServiceContracts');
        $eleContractWorkflowsid = $request->get('eleContractWorkflowsid');
        $servicecontractsid = $request->get('servicecontractsid');
        $verifyLevel = $request->get("verifyLevel");

        $sql = "update vtiger_servicecontracts set modulestatus='b_check',eleccontractstatus='a_elec_sending' where servicecontractsid=?";
        $adb->pquery($sql,array($servicecontractsid));

        $focus = CRMEntity::getInstance('ServiceContracts');
        $_POST['workflowsid'] =  $eleContractWorkflowsid;
        $focus->makeWorkflows('ServiceContracts', $eleContractWorkflowsid, $servicecontractsid,'edit');
        $departmentid=$_SESSION['userdepartmentid'];
        $focus->setAudituid('ContractsAuditset',$departmentid,$servicecontractsid,$eleContractWorkflowsid);
        //修改合同状态为审批中
        //新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$servicecontractsid,'salesorderworkflowstagesid'=>0));


        //根据对应的审核级别删除不需要的审核节点
        switch ($verifyLevel){
            case 1:
                $delSql = "delete from vtiger_salesorderworkflowstages where workflowsid=? and salesorderid=? and sequence in(2,3) and modulename='ServiceContracts'";
                $adb->pquery($delSql,array($eleContractWorkflowsid,$servicecontractsid));
                break;
            case 2:
                $delSql = "delete from vtiger_salesorderworkflowstages where workflowsid=? and salesorderid=? and sequence=3  and modulename='ServiceContracts'";
                $adb->pquery($delSql,array($eleContractWorkflowsid,$servicecontractsid));
                break;
        }

        return array();
    }

    #审核不通过通知放心签
    function auditStatus($data){
        $recordid = $data['recordid'];
        $reason = $data['reason'];
        $isPass = $data['isPass'];
        global $adb,$fangxinqian_url;
        $sql = "select eleccontractid,contract_no,eleccontracttpl,elereceivermobile from vtiger_servicecontracts where servicecontractsid = ?";
        $result = $adb->pquery($sql,array($recordid));
        if(!$adb->num_rows($result)){
            return false;
        }
        $row = $adb->query_result_rowdata($result,0);
        $postData = array(
            'contractId'=>$row['eleccontractid'],
            'number'=>$row['contract_no'],
            'isPass'=>$isPass,
            'reason'=>$reason
        );
        $result = $this->https_requestcomm($fangxinqian_url.$this->auditStatus, json_encode($postData),$this->getCURLHeader(),true);
        $res = json_decode($result,true);
        if($res['success']){
            return true;
        }
        return false;
    }

    /**
     * 头信息上面带上formId参数
     * @return bool|string
     */
    public function getFangXinQianFormId(){
        global $fangxinqian_url;
        $token=$this->getFangXinQianToken();
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "token:".$token));
        $url=$fangxinqian_url.'common/formId';
        return $this->https_requestcomm($url,null,$curlset);
    }

    /**
     * 表单提交生成放心签的FormId
     */
    public function setFangXinQianFormId(){
        $FangXinQianFormId=json_decode($this->getFangXinQianFormId(),true);
        $_SESSION['fangxinqianformid']=$FangXinQianFormId['data'];
    }

    /**
     * 表单提交验证放心签的FormId
     * @return bool
     */
    public function checkFangXinQianFormId(){
        $fangxinqianformid=$_SESSION['fangxinqianformid'];
        if(!empty($fangxinqianformid)){
            unset($_SESSION['fangxinqianformid']);
            return $fangxinqianformid;
        }else{
            return false;
        }
    }

    /**
     * 获取header信息
     * @return array
     */
    public function getCURLHeader($flag=true){
        $token=$this->getFangXinQianToken();
        $this->_logs(array('getFangXinQianToken',$token));
        $headerFormId='';
        if($flag){
            $formId=json_decode($this->getFangXinQianFormId(),true);
            $headerFormId=$formId['data'];
        }
        return array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "token:".$token,
            'formId:'.$headerFormId
        ));
    }
    /**
     * pdf文档预览
     * @param $requesst
     */
    public function getPdfView($fileurl,$isHeader=true){
        //if($isHeader){
            $fileurlData=parse_url($fileurl);
            $curlset=array(CURLOPT_HTTPHEADER=>array(
                "host:".$fileurlData['host']));
        //}

        return $this->https_requestcomm($fileurl,null,$curlset);
    }

    /**
     * 根据合同主体获取相关的信息
     * @param $invoicecompany
     * @return array
     * @throws Exception
     */
    public function getInvoicecompanyInfo($invoicecompany){
        global $adb;
        $query='SELECT * FROM `vtiger_company_code` WHERE companyfullname=? LIMIT 1';
        $result=$adb->pquery($query,array($invoicecompany));
        if($adb->num_rows($result)){
            return $adb->query_result_rowdata($result);
        }
        return array();
    }

    /**
     * 接收审核状态接口(审核通过时自动签署)
     * args{
     *"contractId":2,//放心签平台返回的合同id
     *"isPass":1, //审核状态，0.不通过 1.通过
     *"contractNumber":"审核通过必须加上合同编号", //珍岛生成的最终合同编号
     *"reason":"审核不过时的理由" //审核不通过原因  不通过时必传
     *}
     * @return bool|string
     */
    public function setAuditStatus($args){
        global $fangxinqian_url;
        $viewURL=$fangxinqian_url.$this->auditStatus;
        return $this->https_requestcomm($viewURL,json_encode($args),$this->getCURLHeader(),true);
    }

    /**
     * 合同预览
     * @param $request
     * @return bool|string
     */
    public function getElecTPLView($request){
        global $fangxinqian_url;
        $contractId=$request->get('contractId');
        if($request->get('contract_no')){
            global $adb;
            $sql = "select eleccontractid from vtiger_servicecontracts where contract_no = '".$request->get('contract_no')."'";
            $result = $adb->pquery($sql,array());
            if(!$adb->num_rows($result)){
                return json_encode(array('success'=>false,'msg'=>'无法根据合同编号获取到对应合同'));
            }
            $row = $adb->query_result_rowdata($result,0);
            $contractId = $row['eleccontractid'];
        }
        //$viewURL=$fangxinqian_url.$this->commonView.$contractId;
        $viewURL=$fangxinqian_url.$this->commonView.$contractId;
        return $this->https_requestcomm($viewURL,null,$this->getCURLHeader(false));
    }

    /**
     * $args{
     *"contractId":1, //放心签平台合同id
     *"type":"撤回类型：1：撤回并发送  2：仅撤回  3：撤回并修改合同",
     *"name":"新接收人名称",//撤回并发送时必传
     *"phone":"新接收人手机号码"//撤回并发送时必传
     *}
     * 撤回接口
     */
    public function elecCommonBack($args){
        global $fangxinqian_url;
        $viewURL=$fangxinqian_url.$this->commonBack;
        return $this->https_requestcomm($viewURL,json_encode($args),$this->getCURLHeader(),true);
    }

    /**
     * 合同作废
     */
    public function elecCommonTovoid($contractId){
        global $fangxinqian_url;
        $viewURL=$fangxinqian_url.$this->commonTovoid.$contractId;
        return $this->https_requestcomm($viewURL,null,$this->getCURLHeader(),true);
    }
    /**
     * 获取放心签合同的ID
     */
    public function getElecContractId(){
        global $adb;
        $query='SELECT eleccontractid FROM vtiger_servicecontracts WHERE servicecontractsid=? limit 1';
        $result=$adb->pquery($query,$this->getId());
        $eleccontractid=0;
        if($adb->num_rows($result)){
            $eleccontractid=$result->fields['eleccontractid'];
        }
        return $eleccontractid;
    }

    /**
     * 放心签修改ERP合同数据
     * @param Vtiger_Request $request
     * @return bool
     * @throws Exception
     */
    public function updateModuleStatus(Vtiger_Request $request){
        $contract_no = $request->get('contract_no');
        $eleccontractstatus = $request->get('eleccontractstatus');
        $elechandreason = $request->get('elechandreason');
        $contract_url = $request->get('contract_url');
        $enclouses = $request->get('enclouses');
        $this->_logs(array('updateModuleStatus',$request));
        if(!$contract_no){
            return false;
        }
        global $adb,$configcontracttypeName;
        $sql = "select a.*,b.accountname from vtiger_servicecontracts a  left join vtiger_account b on a.sc_related_to=b.accountid left join vtiger_crmentity ON vtiger_crmentity.crmid=a.servicecontractsid where vtiger_crmentity.deleted=0 AND a.contract_no = ?";
        $result = $adb->pquery($sql,array($contract_no));
        if(!$adb->num_rows($result)){
            return false;
        }

        $rowData = $adb->query_result_rowdata($result,0);
        if(!in_array($rowData['modulestatus'],array('已发放'))){
            return false;
        }
        $TyunWebRecordModel = TyunWebBuyService_Record_Model::getCleanInstance("TyunWebBuyService");
        $recordModel = self::getInstanceById($rowData['servicecontractsid'],'ServiceContracts');

        $effectivetime = date("Y-m-d",strtotime("+1 year"));
        $maxLifeResult =$adb->pquery("select max(productlife) as productlife from vtiger_activationcode where contractid=?",array($rowData['servicecontractsid']));
        if($adb->num_rows($maxLifeResult)){
            $maxLifeArray = $adb->fetchByAssoc($maxLifeResult,0);
            if($maxLifeArray['productlife']){
                $effectivetime =date("Y-m-d",strtotime("+{$maxLifeArray['productlife']} year"));
            }
        }

        global $current_user;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile('6934');

        //判断如果是T云线上的直接存储附件即可

        $sql = "update vtiger_servicecontracts set eleccontractstatus='".$eleccontractstatus."',elechandreason='".$elechandreason."'";
        $coupon_sql="SELECT couponcode,couponname FROM vtiger_activationcode  WHERE contractid=? AND status IN(0,1)";
        $result = $adb->pquery($coupon_sql,array($rowData['servicecontractsid']));
        if($adb->num_rows($result)){
            $data = $adb->fetchByAssoc($result,0);
            if($data['couponcode']){
                $remark = "券码:".$data['couponcode'].' 券码用户名:'.$data['couponname'];
                $sql.=",remark='".$remark."'";
            }
        }
        switch ($eleccontractstatus){
            case 'c_elec_complete':
                $sql .=",modulestatus='c_complete',actualeffectivetime='".$effectivetime."',effectivetime='".$effectivetime."',signfor_date='".date("Y-m-d H:i:s")."',signdate='".date("Y-m-d")."',returndate='".date("Y-m-d")."'";
                //合同状态已签收，邮件通知下单商务，并补全ERP合同对应信息，附件回传ERP，客户名称+合同编号。
                $this->langImport($request);
                $recordModel->fileSave($contract_url,'files_style4','签收件');
                //存储附件
                foreach ($enclouses as $enclouse){
                    $recordModel->fileSave($enclouse,'files_style4','签收件(附件)');
                }

                $modulestatus = 'c_complete';
                //将订单状态改为签收
                $sql2 = "update vtiger_activationcode set contractstatus=1 where contractid=?";
                $adb->pquery($sql2,array($rowData['servicecontractsid']));
                if($rowData['ispay']){
                    $recordModel->tyun71360ContractConfirm();
                }
                $contractAgreementRecordModel = ContractsAgreement_Record_Model::getCleanInstance("ContractsAgreement");
                $contractAgreementRecordModel->recordContractDelaySign($rowData['servicecontractsid'],$rowData['sideagreement'],'c_complete');

                //记录到日志
                $date = date("Y-m-d");
                $array = array(
                    'modulestatus'=>array(
                        'oldValue'=>vtranslate($rowData['modulestatus'],'ServiceContracts'),
                        'currentValue'=>vtranslate('c_complete','ServiceContracts')
                    ),
                    'actualeffectivetime'=>array(
                        'oldValue'=>$rowData['actualeffectivetime'],
                        'currentValue'=>$effectivetime
                    ),
                    'effectivetime'=>array(
                        'oldValue'=>$rowData['effectivetime'],
                        'currentValue'=>$effectivetime
                    ),
                    'signfor_date'=>array(
                        'oldValue'=>$rowData['signfor_date'],
                        'currentValue'=>date("Y-m-d H:i:s")
                    ),
                    'signdate'=>array(
                        'oldValue'=>$rowData['signdate'],
                        'currentValue'=>$date
                    ),
                    'returndate'=>array(
                        'oldValue'=>$rowData['returndate'],
                        'currentValue'=>$date
                    ),
                    'eleccontractstatus'=>array(
                        'oldValue'=>vtranslate($rowData['eleccontractstatus'],'ServiceContracts'),
                        'currentValue'=>vtranslate('c_elec_complete','ServiceContracts')
                    )
                );
                break;
            case "c_elec_cancel":
                if($rowData['contract_type']==$configcontracttypeName){
                    $sql .=",modulestatus='c_cancel',docanceltime='".date("Y-m-d H:i:s")."'";
                    //作废订单
                    $this->cancelOrderByContractNo($rowData['contract_no']);
                    //作废掉合同对应发票
                    $adb->pquery("update vtiger_newinvoice set voidreason='客户拒签合同 ".date("Y-m-d H:i:s")."',modulestatus='c_cancel' where contractid=?",array($rowData['servicecontractsid']));

                    $modulestatus = 'c_cancel';

                    //记录到日志
                    $array = array(
                        'modulestatus'=>array(
                            'oldValue'=>vtranslate($rowData['modulestatus'],'ServiceContracts'),
                            'currentValue'=>vtranslate('c_cancel','ServiceContracts')
                        ),
                        'docanceltime'=>array(
                            'oldValue'=>$rowData['docanceltime'],
                            'currentValue'=>date("Y-m-d H:i:s")
                        ),
                        'eleccontractstatus'=>array(
                            'oldValue'=>vtranslate($rowData['eleccontractstatus'],'ServiceContracts'),
                            'currentValue'=>vtranslate('c_elec_cancel','ServiceContracts')
                        ),
                        'elechandreason'=>array(
                            'oldValue'=>$rowData['elechandreason'],
                            'currentValue'=>$elechandreason
                        )
                    );
                }else{
                    if($rowData['contractattribute']=='customized'){
                        $sql .=",modulestatus='c_cancel',docanceltime='".date("Y-m-d H:i:s")."'";
                        $modulestatus = 'c_cancel';

                        //记录到日志
                        $array = array(
                            'modulestatus'=>array(
                                'oldValue'=>vtranslate($rowData['modulestatus'],'ServiceContracts'),
                                'currentValue'=>vtranslate('c_cancel','ServiceContracts')
                            ),
                            'docanceltime'=>array(
                                'oldValue'=>$rowData['docanceltime'],
                                'currentValue'=>date("Y-m-d H:i:s")
                            ),
                            'eleccontractstatus'=>array(
                                'oldValue'=>vtranslate($rowData['eleccontractstatus'],'ServiceContracts'),
                                'currentValue'=>vtranslate('c_elec_cancel','ServiceContracts')
                            ),
                            'elechandreason'=>array(
                                'oldValue'=>$rowData['elechandreason'],
                                'currentValue'=>$elechandreason
                            )
                        );
                    }else{
                        $sql .=",modulestatus='a_normal'";
                        $modulestatus = 'a_normal';

                        //记录到日志
                        $array = array(
                            'modulestatus'=>array(
                                'oldValue'=>vtranslate($rowData['modulestatus'],'ServiceContracts'),
                                'currentValue'=>vtranslate('a_normal','ServiceContracts')
                            ),
                            'eleccontractstatus'=>array(
                                'oldValue'=>vtranslate($rowData['eleccontractstatus'],'ServiceContracts'),
                                'currentValue'=>vtranslate('c_elec_cancel','ServiceContracts')
                            ),
                            'elechandreason'=>array(
                                'oldValue'=>$rowData['elechandreason'],
                                'currentValue'=>$elechandreason
                            )
                        );

                    }

                    $adb->pquery('DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=?',array($rowData['servicecontractsid']));
                }
                break;
        }
        $sql .= " where servicecontractsid=?";
        $adb->pquery($sql,array($rowData['servicecontractsid']));
        $this->setModTracker('ServiceContracts',$rowData['servicecontractsid'],$array);
        $data = array(
            'userid'=>$rowData['receiveid'],
            'contract_no'=>$contract_no,
            'customer_name'=>$rowData['accountname'],
            "signdate"=>date('Y-m-d H:i:s'),
            "docanceltime"=>date('Y-m-d H:i:s'),
            "receivedate"=>$rowData['receivedate'],
            "elereceiver"=>$rowData['elereceiver'],
            "elereceivermobile"=>$rowData['elereceivermobile'],
            "modulestatus"=>$modulestatus,
            "fromactivity"=>$rowData["fromactivity"],
            "comeformtyun"=>$rowData["comeformtyun"],
            "elechandreason"=>$elechandreason
        );
        $TyunWebRecordModel->elecContractStatusSendMail($data);
        return true;
    }

    /**
     * 放心签发邮件
     * @param $recordModelData
     */
    public function sendMailFXQ(){
        global $adb;
        $query='SELECT label FROM vtiger_crmentity WHERE crmid=?';
        $accountResult=$adb->pquery($query,array($this->get('sc_related_to')));
        $contract_no=$this->get('contract_no');
        $body2='';
        $body2 .= "<span style='font-weight:bold'>系统已发送电子合同给客户，请及时与客户确认并跟进客户完成合同签署！</span><br>";
        $body2 .= "<span style='font-weight:bold'>合同编号:&nbsp;</span>".$contract_no.'<br>';
        $body2 .= "<span style='font-weight:bold'>客户:&nbsp;</span>".$accountResult->fields['label'].'<br>';
        $body2 .= "<span style='font-weight:bold'>发送时间:&nbsp;</span>".date('Y-m-d H:i:s').'<br>';
        $body2 .= "<span style='font-weight:bold'>联系人:&nbsp;</span>".$this->get('elereceiver').'<br>';
        $body2 .= "<span style='font-weight:bold'>联系人手机号:&nbsp;</span>".$this->get('elereceivermobile').'<br>';
        $query='SELECT last_name,email1 FROM vtiger_users WHERE id=? LIMIT 1';
        $useResult=$adb->pquery($query,array($this->get('Receiveid')));
        $this->sendMail('电子合同发送通知---"'.$contract_no.'"',$body2,array(array('name'=>$useResult->fields['last_name'],'mail'=>$useResult->fields['email1'])));
    }


    public function transferServiceContractsType($classType){
        switch ($classType){
            case 'buy':
                $servicecontractstype='新增';
                $templateType = 1;
                break;
            case 'degrade':
            case 'cdegrade':
                $servicecontractstype='degrade';
                $templateType = 4;
                break;
            case 'renew':
            case 'crenew':
                $servicecontractstype='续费';
                $templateType = 2;
                break;
            case 'upgrade':
            case 'cupgrade':
                $servicecontractstype='upgrade';
                $templateType = 3;
                break;
        }
        return array($servicecontractstype,$templateType);
    }

    //生成T云电子合同
    public function createTyunElecServiceContracts(Vtiger_Request $request){
        $ordercode = $request->get('ordercode');
        $accountInfo = $request->get('accountinfo');
        global $adb,$SHZD_companyid;
        //获取线上订单
        $sql = "select * from vtiger_activationcode where ordercode ='".$ordercode."'";
        $result = $adb->pquery($sql,array());
        if(!$adb->num_rows($result)){
            return array('success'=>false,'msg'=>'无对应订单,暂不可创建合同');
        }
        $row = $adb->query_result_rowdata($result,0);
        if(!$row['contractid']){
            $request2 = new Vtiger_Request(array());
            //调用合同里创建T云电子合同的方法
            $request2->set('ordercode', $ordercode);
            $result = $this->createTyunServiceContracts($request2);
            if(!$result['success']){
                return array('success'=>false,'msg'=>$result['msg']);
            }
//            return array('success'=>false,'msg'=>'请先联系客服创建合同,订单号:'.$ordercode);
        }
        $isPackage = false;
        if($row['productid']){
            $isPackage = true;
            $productCode = $row['productid'];
        }else{
            $productCode = $row['buyseparately'];
        }
        $activityId =$row['activityid'];
        $servicecontractstype = $this->transferServiceContractsType($row['classtype']);
        if($isPackage){
            $productCodes = array_map(function($a){return 'co'.$a;},array($productCode));
        }else{
            $productCodes = array_map(function($a){return 'so'.$a;},array($productCode));
        }
        $orderType=0;
        if($activityId){
            $orderType=1;
        }
        $templateParams = array(
            'productcode'=>$productCodes,
            'servicecontractstype'=>$servicecontractstype[1],
            "ordertype"=>$orderType,
        );
        //获取合同模板
        $templateData = $this->matchElecContractTemplate($templateParams);
        if(count($templateData)!=1){
            return array('success'=>false,'msg'=>'无对应合同模板或非唯一合同模板');
        }

        $companyInfo = $this->getCompanyInfoId($SHZD_companyid);
        if(empty($companyInfo) || !count($companyInfo)){
            $returnmsg=array('success'=>false,'msg'=>'获取主体公司信息失败,请重试');
            return $returnmsg;
        }

        $makeData = $this->makeElecContractData($row);
        //生成电子合同
        $previewResult = $this->elecontractPreview($request,$makeData,$companyInfo,$productCodes,$row['contractid'],
            $servicecontractstype[1],$templateData[0]['templateId'],$row['contractname']);
        if(!$previewResult['success']){
            $sql3 = "update vtiger_servicecontracts set eleccontractstatus=? where servicecontractsid=?";
            $adb->pquery($sql3,array('a_elec_actioning_fail',$row['contractid']));
            return array('success'=>false,'msg'=>$previewResult['msg']);
        }

        //存入订单
        $sql1 = "update vtiger_activationcode set contractname ='".$row['contractname']."',contractid=".$row['contractid'].",elereceivermobile='".$accountInfo['first_phone'].
            "',signaturetype='eleccontract',owncompanyid='".$companyInfo['companyid']."',owncompany='".$companyInfo['companyfullname']."' where activationcodeid=?";
        $adb->pquery($sql1,array($row['activationcodeid']));

        //修改合同
        $sql2 = "update vtiger_servicecontracts set eleccontractid=".$previewResult['data']['contractId'].",eleccontracturl=?,elereceivermobile='".$accountInfo['first_phone']."',elereceiver='".$accountInfo['first_name']."',clientproperty='".$request->get('clientproperty'). "',eleccontracttplid=".$templateData[0]['templateId'].",eleccontracttpl='".$templateData[0]['templateName']."',modulestatus='已发放',eleccontractstatus='b_elec_actioning' where servicecontractsid=?";
        $adb->pquery($sql2,array($previewResult['data']['contractUrl'],$row['contractid']));
        return array('success'=>true,'msg'=>'','contract_number'=>$row['contractname'],'contractUrl'=>$previewResult['data']['contractUrl']);
    }

    public function elecontractPreview($requestData,$makeData,$companyInfo,$productCodes,$servicecontractsid,$puchaseType,$templateId,$contractname){
        global $fangxinqian_url;
        $accountInfo = $requestData->get('accountinfo');
        $unit_price = round($makeData['totalprice']/($makeData['packageterm']*$makeData['productcount']),2);
        switch ($requestData->get('clientproperty')){
            case 'personal':
                $type = 1;
                break;
            case 'enterprise':
                $type = 0;
                break;
        }

        switch ($puchaseType){
            case 1:
                $classtype='新增';
                break;
            case 4:
                $classtype='降级';
                break;
            case 2:
                $classtype='续费';
                break;
            case 3:
                $classtype='升级';
                break;
        }

        $postData = array(
            "companyCode"=>$companyInfo['company_codeno'],//公司编号
            "productCode"=>$productCodes[0],//产品编号
            "purchaseType"=>$puchaseType,//购买类型（1.新增2.续费3.升级4.降级5.另购）
            "orderType"=>$requestData->get("fromactivity")?1:0,//下单类型  0普通产品 1活动产品
            "contractNumber"=>$contractname,//合同编号
            "templateId"=>$templateId,
            //关键字替换的字段
            "replaces"=>array(
                "number"=>$contractname,
                "address"=>$companyInfo['address'],
                "company"=>$companyInfo['companyfullname'],
                "name"=>$companyInfo['companyname'],
                "phone"=>$companyInfo['telphone'],
                "taxnumber"=>$companyInfo['taxnumber'],
                "email"=>$companyInfo['email'],

                "firstaddress"=>$accountInfo['first_address'] ? $accountInfo['first_address']:' ',
                "firstcompany"=>$accountInfo['first_company'] ? $accountInfo['first_company'] :' ',
                "firstname"=>$accountInfo['first_name'] ? $accountInfo['first_name']: ' ',
                "firstphone"=>$accountInfo['first_phone'] ? $accountInfo['first_phone'] : ' ',
                "firsttaxnumber"=>" ",

                'product'=>$makeData['productname'],  //产品名
                "year"=>$makeData['packageterm'],  //产品年限
                "count"=>$makeData['productcount'], //产品数量
                'unit'=>strval(round($unit_price,2)),
                'ctariff'=>strval(round($makeData['totalprice'],2)),
                'totaltariff'=>strval(round($makeData['totalprice'],2)), //合同总价
                "chinatotaltariff"=>$makeData['totalpricetochina'], //合同总价大写

                'signdate'=>date("Y年m月d日"),
                'firstsigndate'=>date("Y年m月d日"),
                'classtype'=>$classtype,
                'ordercode'=>$makeData['ordercode']
            ),
            "dynamicRows"=>array()
        );

        $result = $this->https_requestcomm($fangxinqian_url.$this->saveAndReplaceOnline, json_encode($postData),$this->getCURLHeader(),true);
        $data = json_decode($result,true);
        if($data['success']){
            $this->langImport($requestData);
            $recordModel = Vtiger_Record_Model::getInstanceById($servicecontractsid,'ServiceContracts',true);
            $recordModel->fileSave($data['data']['contractUrl'],'files_style6','待签订件');
            //保存放心签的待签订件
            return array('success'=>true,'msg'=>'','data'=>$data['data']);
        }
        return array('success'=>false,'msg'=>'发起电子合同失败');
    }


    /**
     * 生成T云合同
     * @param $request
     */
    public function createTyunServiceContracts($request){
        $this->_logs(array('createTyunServiceContractsfromTyunweb',$request));
        $ordercode = $request->get('ordercode');
        global $adb,$SHZD_companyid;
        //获取线上订单
        $sql = "select * from vtiger_activationcode where ordercode ='".$ordercode."'";
        $result = $adb->pquery($sql,array());
        $row = $adb->query_result_rowdata($result,0);
        if($row['contractid']){
            return array('success'=>false,'message'=>'合同已生成');
        }
        $isPackage = false;
        $otherproductids = array();
        $request->set('productlife',$row['productlife']);
        if($row['productid']){
            $isPackage = true;
            $request->set('productid',$row['productid']);
            $productname = $row['productname'];
            $productname = preg_replace("/\(d+\)/","",$productname);
            $request->set('packagename',$productname);
            $request->set('codeprefix','co');
        }else{
            $otherproductids[] = $row['buyseparately'];
            $request->set('codeprefix','so');
        }
        $request->set('oldproductname',$row['oldproductname']);
        //升级获取原合同
        if($row['classtype']=='upgrade'){
            $request->set('oldcontractcode_display',$row['originalcontractname']);
        }
        $request->set('paycode', $row['paycode']);
        $request->set('otherproductids',$otherproductids);
        $request->set('accountid',$row['customerid']);
        $servicecontractstype = $this->transferServiceContractsType($row['classtype']);
        $request->set('servicecontractstype',$servicecontractstype[0]);
        $request->set('totalprice',$row['contractprice']?$row['contractprice']:$row['orderamount']);
        $request->set('ispackage',$isPackage);
        $request->set('comeformtyun', 1);
        $request->set('eleccontractstatus', 'a_elec_not_apply');
        $request->set('modulestatus', '已发放');
        $request->set('originator', '客户导入');
        $request->set('originatormobile', '');
        $request->set('userid', 6934);
        $request->set('fromactivity', $row['fromactivity']);
        if( $row['fromactivity']){
            $request->set('activityenddate', $row['activityenddate']);
        }

        $companyInfo = $this->getCompanyInfoId($SHZD_companyid);
        $request->set('invoicecompany',$companyInfo['companyfullname']);
        $request->set('companycode',$companyInfo['company_code']);
        $request->set('productid',$row['productid']);
        $request->set('otherproductids',array($row['buyseparately']));
        $request->set('classtype',$row['classtype']);
        $request->set('contractattribute','standard');
        $request->set('parent_contracttypeid',2);
        $request->set('ispay',1);
        $elecContract = $this->createServiceContracts($request);
        if(!$elecContract['id'] || !$elecContract['contracts_no']){
            $this->_logs(array('createTyunServiceContractsRequest',$request,'result'=>$elecContract));
            return array('success'=>false,'message'=>'存在产品未设置合同编号或者多个产品对照合同编号不唯一');
        }
        $request->set('contractNumber',$elecContract['contracts_no']);
        $request->set('ordercode',$ordercode);
        $request->set('servicecontractsid',$elecContract['id']);
        $this->createSalesorderproductsrel($request);

        //存入订单
        $sql1 = "update vtiger_activationcode set contractname ='".$request->get('contractNumber')."',contractid=".$elecContract['id'].",elereceivermobile='".$request->get('elereceivermobile').
            "',signaturetype='eleccontract',owncompanyid='".$companyInfo['companyid']."',owncompany='".$companyInfo['companyfullname']."' where activationcodeid=?";
        $adb->pquery($sql1,array($row['activationcodeid']));
        return array('success'=>true,'message'=>'');
    }

    public function makeElecContractData($row){
        $packageterm = $row['productlife'];
        $totalPrice = $row['contractprice'];
        $productname = preg_replace('/\(.*?\)/', '', $row['productname']);
        $totalpricetochina = $this->toChinaMoney($totalPrice);
        if($row['productid']){
            $productCount = 1;
        }else{
            $productnamestemp=str_replace('&quot;','"',$row['productnames']);
            $productnames=json_decode($productnamestemp,true);
            $productCount = $productnames[0]['productCount'];
        }

        return array(
            'dynamicrows'=>array(),
            "packageterm"=>$packageterm,  //套餐年限
            'totalprice'=>round($totalPrice,2), //合同金额合计
            "totalpricetochina"=>$totalpricetochina,    //合同金额大写
            "productname"=>$productname,
            "productcount"=>$productCount,
            'ordercode'=>$row['ordercode']
        );
    }

    function toChinaMoney($num){
        $c1 = "零壹贰叁肆伍陆柒捌玖";
        $c2 = "分角元拾佰仟万拾佰仟亿";
        //精确到分后面就不要了，所以只留两个小数位
        $num = round($num, 2);
        //将数字转化为整数
        $num = $num * 100;
        if (strlen($num) > 10) {
            return "金额太大，请检查";
        }
        $i = 0;
        $c = "";
        while (1) {
            if ($i == 0) {
                //获取最后一位数字
                $n = substr($num, strlen($num)-1, 1);
            } else {
                $n = $num % 10;
            }
            //每次将最后一位数字转化为中文
            $p1 = substr($c1, 3 * $n, 3);
            $p2 = substr($c2, 3 * $i, 3);
            if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
                $c = $p1 . $p2 . $c;
            } else {
                $c = $p1 . $c;
            }
            $i = $i + 1;
            //去掉数字最后一位了
            $num = $num / 10;
            $num = (int)$num;
            //结束循环
            if ($num == 0) {
                break;
            }
        }
        $j = 0;
        $slen = strlen($c);
        while ($j < $slen) {
            //utf8一个汉字相当3个字符
            $m = substr($c, $j, 6);
            //处理数字中很多0的情况,每次循环去掉一个汉字“零”
            if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
                $left = substr($c, 0, $j);
                $right = substr($c, $j + 3);
                $c = $left . $right;
                $j = $j-3;
                $slen = $slen-3;
            }
            $j = $j + 3;
        }
        //这个是为了去掉类似23.0中最后一个“零”字
        if (substr($c, strlen($c)-3, 3) == '零') {
            $c = substr($c, 0, strlen($c)-3);
        }
        //将处理的汉字加上“整”
        if (empty($c)) {
            return "零元整";
        }else{
            return $c . "整";
        }
    }


    /**
     * 作废电子合同
     *
     * @param Vtiger_Request $request
     * @return array
     * @throws Exception
     */
    public function toVoidElecContract(Vtiger_Request $request){
        $contract_no = $request->get('contract_no');
        global $adb;
        $sql = "select * from vtiger_servicecontracts where contract_no = ?";
        $result = $adb->pquery($sql,array($contract_no));
        if(!$adb->num_rows($result)){
            return array('msg'=>'没有找到对应合同','success'=>false);
        }
        $row = $adb->query_result_rowdata($result,0);
        if(in_array($row['eleccontractstatus'],array('c_elec_complete','c_elec_cancel','a_elec_withdraw'))){
            return array('msg'=>'合同状态不支持作废','success'=>false);
        }

        $contractId = $row['eleccontractid'];
        $voidResult = $this->elecCommonTovoid($contractId);
        $data = json_decode($voidResult,true);
        if(!$data['success']){
            return array('msg'=>'合同作废失败','success'=>false);
        }
        return array('msg'=>$data['msg'],'success'=>true);
    }

    /**
     * 签章
     */
    public function toFormalServiceContracts(Vtiger_Request $request){
        $contract_no = $request->get('contract_no');
        global $adb;
        $sql = "select * from vtiger_servicecontracts where contract_no = '".$contract_no."'";
        $result = $adb->pquery($sql,array());
        if(!$adb->num_rows($result)){
            return array('msg'=>'没有找到对应合同','success'=>false);
        }
        $row = $adb->query_result_rowdata($result,0);
        if(in_array($row['eleccontractstatus'],array('c_elec_complete','c_elec_cancel','a_elec_sending'))){
            return array('msg'=>'合同状态不支持转为正式合同','success'=>false);
        }
        $request->set('contractid',$row['eleccontractid']);
        $params = array(
            "contractid"=>$request->get('contractid'),//放心签合同id
            "idcard"=>$request->get('idcard'),//接收方身份证/企业统一信用代码
            "name"=>$request->get('name'),//个人姓名/企业名称
            "phone"=>$request->get('phone'),//个人手机号码/企业法人手机号码
            "type"=>$request->get('type') //接收方用户类型 0.企业 1.个人
        );
        $data = $this->elecToFormal($params);
        if($data['success']){
            $sql = "update vtiger_servicecontracts set modulestatus=?,eleccontractstatus=?,eleccontracturl=?,eleccontractid=? where servicecontractsid=?";
            $adb->pquery($sql,array('c_complete','c_elec_complete',$data['data']['contractUrl'],$data['data']['contractId'],$row['servicecontractsid']));

            $this->langImport($request);
            $recordModel = Vtiger_Record_Model::getInstanceById($row['servicecontractsid'],'ServiceContracts',true);
            $recordModel->fileSave($data['data']['contractUrl'],'files_style4','签收件');
            return array('msg'=>'合同签章完成','success'=>true);
        }
//        return array('msg'=>'签章失败','success'=>false);
        return array('msg'=>$data['msg'],'success'=>false);
    }

    /**
     * 放心签转正式合同接口
     *
     * @param $contractId
     * @return array
     */
    public function elecToFormal($params){
        global $fangxinqian_url;
        $postData = array(
            "contractId"=>$params['contractid'],//放心签合同id
            "receiver"=>array(
                "idcard"=>$params['idcard'],//接收方身份证/企业统一信用代码
                "name"=>$params['name'],//个人姓名/企业名称
                "phone"=>$params['phone'],//个人手机号码/企业法人手机号码
                "type"=>$params['type'] //接收方用户类型 0.企业 1.个人
            )
        );
        $this->_logs(array("fangxinqian_new_contract：", $postData));
        $result = $this->https_requestcomm($fangxinqian_url.$this->signOnline,json_encode($postData),$this->getCURLHeader(),true);
        return json_decode($result,true);
    }

    /**
     * 71360合同预览
     * @param $request
     * @return bool|string
     */
    public function getTPLView($request){
        global $adb;
        $sql = "select eleccontracturl from vtiger_servicecontracts where contract_no = '".$request->get('contract_no')."'";
        $result = $adb->pquery($sql,array());
        if(!$adb->num_rows($result)){
            return json_encode(array('success'=>false,'msg'=>'无法根据合同编号获取到对应合同'));
        }
        $row = $adb->query_result_rowdata($result,0);
        return json_encode(array('success'=>true,'msg'=>'success','contractUrl'=>$row['eleccontracturl']));
    }


    /**
     * 通过合同工号和合同id作废订单并通知t云作废
     *
     * @param $contractname
     * @param $servicecontractsid
     * @throws Exception
     */
    public function cancelOrderByContractNo($contractname){
        global $adb;
        $TyunWebRecordModel = Vtiger_Record_Model::getCleanInstance("TyunWebBuyService");
        $actiationCodeResult = $adb->pquery("select usercodeid,usercode from vtiger_activationcode where contractname='".$contractname."'",array());
        if(!$adb->num_rows($actiationCodeResult)){
            return '';
        }
        $activationCodeData = $adb->query_result_rowdata($actiationCodeResult,0);
        $TyunWebRecordModel->doOrderCancelByContractNo($contractname,$activationCodeData['usercodeid'],$activationCodeData['usercode']);
    }

    /**
     * 合同重新签收
     * @param $recordModel
     */
    public function elecAgainSign(){
        $contract_no=$this->get('contract_no');
        $eleccontractid = $this->get('eleccontractid');

        $contractattribute=$this->get('contractattribute');
        if($contractattribute=='standard'){
            $arrayData = array("contractId" => $eleccontractid,//放心签平台返回的合同id
                "number" => $contract_no, //珍岛生成的最终合同编号
            );
            $echoData=$returnData = $this->contractSend($arrayData);//审核通过
        }else{
            $arrayData = array("contractId" => $eleccontractid,//放心签平台返回的合同id
                "isPass" => 1, //审核状态，0.不通过 1.通过
                "contractNumber" => $contract_no, //珍岛生成的最终合同编号
                "number" => $contract_no, //珍岛生成的最终合同编号
                "reason" => "");
            $echoData=$returnData = $this->setAuditStatus($arrayData);//审核通过
        }
        return $echoData;
    }

    public function langImport($request){
        global $default_language;
        vglobal('default_language', $default_language);
        $currentLanguage = 'zh_cn';
        //Vtiger_Language_Handler::getLanguage();//2.语言设置
        vglobal('current_language',$currentLanguage);
        $module = $request->getModule();//3.1获取参数module的值
        $qualifiedModuleName = $request->getModule(false);//3.2获取module以及父级parent，返回parent:module如module=Vtiger&parent=Settings|Settings:Vtiger

        //4.返回当前用户的语言设置
        if ($qualifiedModuleName) {
            $moduleLanguageStrings = Vtiger_Language_Handler::getModuleStringsFromFile($currentLanguage,$qualifiedModuleName);
            vglobal('mod_strings', $moduleLanguageStrings['languageStrings']);
        }
    }

    /**
     * 修改电子合同状态
     */
    public function updateMobileContractStatus($request){
        $recordId = $request->get('servicecontractsid');
        $elecContractStatus = $request->get('eleccontractstatus');
        $moduleStatus = $request->get('modulestatus');
        global $adb;
        $sql = "update vtiger_servicecontracts set eleccontractstatus = '".$elecContractStatus."',modulestatus='".$moduleStatus."'  where servicecontractsid = ?";
        $result = $adb->pquery($sql,array($recordId));
        if($adb->num_rows($result)){
            return true;
        }
        return false;
    }
    public function checkUserPermission($userid){
        $where=getAccessibleUsers('ServiceContracts','List',true);
        if($where=='1=1'){
            return true;
        }
        if(in_array($userid,$where)){
            return true;
        }
        return false;
    }
    /**
     * 定制合同替换
     * @param $params
     * @return bool|string
     */
    public function contractSet($params){
        global $fangxinqian_url;
        $viewURL=$fangxinqian_url.$this->contract_set;
        return $this->https_requestcomm($viewURL,json_encode($params),$this->getCURLHeader(),true);
    }
    /**
     * 定制合同编辑替换
     * @param $params
     * @return bool|string
     */
    public function contractReSet($params){
        global $fangxinqian_url;
        $viewURL=$fangxinqian_url.$this->contract_reset;
        return $this->https_requestcomm($viewURL,json_encode($params),$this->getCURLHeader(),true);
    }
    /**
     * 定制合同编辑替换区域
     * @param $params
     * @return bool|string$get_areas
     */
    public function contractReSetArea($contractId){
        global $fangxinqian_url;
        $viewURL=$fangxinqian_url.$this->get_areas.$contractId;
        return $this->https_requestcomm($viewURL,NULL,$this->getCURLHeader(),true);
    }
    public function product2productcodenotyun(){
        $db=PearDatabase::getInstance();
        $sql = "SELECT * FROM `vtiger_contract_type`";
        return $db->pquery($sql,array());
    }
    public function addProduct($args){
        global $fangxinqian_url;
        $viewURL=$fangxinqian_url.$this->add_product;
        return $this->https_requestcomm($viewURL,json_encode($args),$this->getCURLHeader(),true);
    }


    public function getAccountName($servicecontractsid){
        global $adb;
        $sql = "select b.accountname from vtiger_servicecontracts a left join vtiger_account b on a.sc_related_to = b.accountid where servicecontractsid=?";
        $result = $adb->pquery($sql,array($servicecontractsid));
        $res = $adb->query_result_rowdata($result,0);
        return $res['accountname']?$res['accountname']:'';
    }
    public function canUseToTyunWeb(Vtiger_Request $request){
        global $adb,$current_user;
        $userid=$request->get('userid');
        $searchValue=$request->get('searchValue');
        if(empty($searchValue)){
            return false;
        }
        $is_cs_admin=$request->get('is_cs_admin');
        $querySql='';
        if(!$is_cs_admin){
            //if($userid==2824){
            //$querySql = ' AND vtiger_crmentity.smownerid in(1179,2824,7871)';
            //}else {
            $user = new Users();
            $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
            //if ($customerid > 0) {
            //$querySql = ' AND vtiger_crmentity.smownerid=' . $customerid;
            //} else {
            $where = getAccessibleUsers('ServiceContracts', 'List', true);
            if ($where != '1=1') {
                $querySql = ' AND vtiger_crmentity.smownerid in(' . implode(',', $where) . ')';
            }
            // }
            //}
        }

        $query="SELECT 1 FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid=vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0 and vtiger_servicecontracts.signaturetype='papercontract'
                    AND  (vtiger_servicecontracts.modulestatus in('已发放','c_recovered') or (vtiger_servicecontracts.modulestatus='c_complete' and vtiger_servicecontracts.contract_type='T云WEB版')) ".$querySql." AND vtiger_servicecontracts.servicecontractsid = ? 
                     AND NOT EXISTS(SELECT 1 FROM vtiger_activationcode WHERE vtiger_activationcode.contractid=vtiger_servicecontracts.servicecontractsid AND vtiger_activationcode.status IN(0,1)) 
	                AND NOT EXISTS(SELECT 1 FROM vtiger_tyunstationsale WHERE vtiger_tyunstationsale.contractid=vtiger_servicecontracts.servicecontractsid)";
        $this->_logs(array('canUseToTyunWebSql',$query,'searchValue'=>$searchValue));
        $result=$adb->pquery($query,array($searchValue));
        if($adb->num_rows($result)){
            return true;
        }
        return false;
    }
    public function getAbleExecutionContractByNo($contractNo){
        global $adb;
        $sql = "select b.accountname,b.accountid,e.stage,d.processdate,a.servicecontractsid from vtiger_servicecontracts a 
  left join vtiger_account b on a.sc_related_to =b.accountid 
  left join vtiger_crmentity c on a.servicecontractsid=c.crmid 
  left join vtiger_contracts_execution d on a.servicecontractsid=d.contractid
  left join vtiger_contracts_execution_detail e on a.servicecontractsid=e.contractid
where c.deleted=0 and a.modulestatus='c_complete' and a.frameworkcontract='yes' and a.bussinesstype='bigsass' and a.contract_no=? order by stage desc limit 1";
        $result = $adb->pquery($sql,array($contractNo));
        if(!$adb->num_rows($result)){
            return array();
        }
        $data = $adb->query_result_rowdata($result,0);
        return array(
            'accountname'=>$data['accountname'],
            'accountid'=>$data['accountid'],
            'stage'=>$data['stage']?$data['stage']:0,
            'processdate'=>$data['processdate'],
            'contractid'=>$data['servicecontractsid']
        );
    }

    /**
     * 添加阶段明细
     * @param $request
     * @return html
     */
    public function addPhaseSplit($request)
    {
        $detailView = new Vtiger_Index_View();
        $viewer = $detailView->getViewer($request);
        $moduleName = $request->getModule();
        $record = $request->get('record');
        if (!empty($record)) {
            $recordModel = $this->record ? $this->record : Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('RECORD_ID', $record);
            $viewer->assign('MODE', 'edit');
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $viewer->assign('MODE', '');
            $viewer->assign('RECORD_ID', '');
        }
        if (!$this->record) {
            $this->record = $recordModel;
        }

        $moduleModel = $recordModel->getModule();
        if(!$moduleModel->exportGrouprt('ServiceContracts','Received')){
            return '';
        }
        //读取模块的字段
        $fieldList = $moduleModel->getFields();

        $requestFieldList = array_intersect_key($request->getAll(), $fieldList);
        if(!empty($requestFieldList)){
            foreach($requestFieldList as $fieldName=>$fieldValue){
                $fieldModel = $fieldList[$fieldName];
                if($fieldModel->isEditable()) {
                    $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
                }
            }
        }
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel,'Edit');

        $RECORD_STRUCTURE=$recordStructureInstance->getStructure();
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE',$RECORD_STRUCTURE);//UI字段生成位置
        $viewer->assign('BLOCK_FIELDS', $RECORD_STRUCTURE['CONTRACT_PHASE_SPLIT']);//UI字段生成位置
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('RECORD',$recordModel);
        $viewer->assign('INITNUM',$request->get('stagenum'));
        $viewer->assign('ISSHOWHEADER',$request->get('showHeader'));
        $viewer->assign('BLOCK_LABEL','CONTRACT_PHASE_SPLIT');

        return $viewer->view('ROWEditViewBlocksList.tpl', $moduleName,true);
    }

    /**
     * 阶段明细，赋值
     * @param $CONTRACT_PHASE_SPLIT
     * @param int $RECORD_ID
     * @param int $INITNUM
     * @return array
     */
    public function assignContractPhaseSplit($CONTRACT_PHASE_SPLIT,$RECORD_ID=0,$INITNUM=1,$isedit=1){
        $returnData=array();
        $this->CONTRACT_PHASE_SPLIT_NUM=$INITNUM;
        if($RECORD_ID>0){
            global $adb;
            /*$query='SELECT
                           vtiger_contracts_execution_detail.*
                        FROM
                            vtiger_contracts_execution
                        LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contracts_execution.contractexecutionid
                        LEFT JOIN vtiger_contracts_execution_detail ON vtiger_contracts_execution.contractexecutionid = vtiger_contracts_execution_detail.contractexecutionid
                        WHERE
                            vtiger_crmentity.deleted = 0
                        AND vtiger_contracts_execution.contractid=? ORDER BY stage';*/
            $query='SELECT *
                        FROM
                            vtiger_contracts_execution_detail
                        WHERE
                            contractid=? and iscancel=0 ORDER BY stage';
            $result=$adb->pquery($query,array($RECORD_ID));
            $num_rows=$adb->num_rows($result);
            if($num_rows){
                $this->CONTRACT_PHASE_SPLIT_NUM=$num_rows;
                $headertitle=array_keys($CONTRACT_PHASE_SPLIT);
                while($row=$adb->fetch_array($result)){
                    foreach($headertitle as $headerValue){
                        if('executestatus'!=$headerValue){
                            if(!empty($row['stageshow'])){
                                $objheaderValue=clone $CONTRACT_PHASE_SPLIT[$headerValue];
                                $objheaderValue->set('fieldvalue',$row[$headerValue]);
                                $CONTRACT_PHASE_SPLIT[$headerValue]=$objheaderValue;
                            }
                        }else{
                            $executestatus=clone $CONTRACT_PHASE_SPLIT['executestatus'];
                            $executestatus->set('fieldvalue',vtranslate($row['executestatus'],'ContractExecution'));
                            $CONTRACT_PHASE_SPLIT['executestatus']=$executestatus;
                        }
                    }
                    $returnData[]=$CONTRACT_PHASE_SPLIT;
                }
            }else{
                if($isedit) {
                    $CONTRACT_PHASE_SPLIT['stageshow']->set('fieldvalue', '第' . $INITNUM . '阶段');
                    $CONTRACT_PHASE_SPLIT['executestatus']->set('fieldvalue', '未执行');
                    $CONTRACT_PHASE_SPLIT['receiverabledate']->set('fieldvalue', '');
                    $CONTRACT_PHASE_SPLIT['executor']->set('fieldvalue', '');
                    $CONTRACT_PHASE_SPLIT['executedate']->set('fieldvalue', '');
                    $returnData[] = $CONTRACT_PHASE_SPLIT;
                }else{
                    $returnData=array();
                }
            }
        }else{
            $CONTRACT_PHASE_SPLIT['stageshow']->set('fieldvalue','第'.$INITNUM.'阶段');
            $CONTRACT_PHASE_SPLIT['executestatus']->set('fieldvalue','未执行');
            $CONTRACT_PHASE_SPLIT['receiverabledate']->set('fieldvalue','');
            $CONTRACT_PHASE_SPLIT['executor']->set('fieldvalue','');
            $CONTRACT_PHASE_SPLIT['executedate']->set('fieldvalue','');
            $returnData[]=$CONTRACT_PHASE_SPLIT;
        }
        return $returnData;
    }

    public function canCreateExecution(){
        global $adb;
        $canExecutionData = array('bigsass','smallsassdirect');
        $sql ="select b.bussinesstype from vtiger_servicecontracts a left join vtiger_contract_type b on a.contract_type=b.contract_type  where a.servicecontractsid=? limit 1";
        $result = $adb->pquery($sql,array($this->getId()));
        if($adb->num_rows($result)){
            $row = $adb->fetchByAssoc($result,0);
            return in_array($row['bussinesstype'],$canExecutionData);
        }
        return false;
    }

    /**
     * 合同领取的微信消息提醒
     */
    public function sendReceivedWx($contract_no,$userid)
    {
        global $adb;
        $res = $adb->pquery("select a.email1,concat(a.last_name,'[',(ifnull(c.departmentname,'')),']') as name from vtiger_users a 
  left join vtiger_user2department  b on a.id=b.userid  
  left join vtiger_departments c on b.departmentid=c.departmentid
where id=? limit 1",array($userid));
        if(!$adb->num_rows($res)){
            return;
        }
        $row = $adb->fetchByAssoc($res,0);

        $content = "同事您好!您的合同已于".date("Y-m-d H:i:s")."成功领取<br>";
        $content .= '合同编号:'.$contract_no.'<br>合同领取人:'.$row['name'].'<br>';
        $content .= '特别提示:<br>a, 合同作废:根据合同页数收费,每页合同收取1元。<br>b, 合同遗失：收取300元/份;请谨慎使用并保管合同。';
        $this->sendWechatMessage(array('email'=>trim($row['email1']),'description'=>$content,'dataurl'=>'#','title'=>'合同领取消息提醒！！！','flag'=>7));
    }
    /**
     * 根据主体设置合同的财务主管审核流程
     * @param $recordId
     * @param $companyNO
     */
    public function setWorkflowUserID($recordId,$companyNO){
        global $adb;
        if(in_array($companyNO,$this->Kllcompanycode)){
            $deleteSql="DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowstagesflag IN ('TREASURER_ONE','TREASURER_TWO')";
            $adb->pquery($deleteSql,array($recordId));//删除财务主管的节点
        }elseif(in_array($companyNO,$this->TREASURER_TWO)){
            $deleteSql="DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowstagesflag IN ('TREASURER_ONE','CREATE_SIGN_TWO','CREATE_SIGN_ONE','CLOSE_WORKSTREAM','DO_PRINT')";
            $adb->pquery($deleteSql,array($recordId));//删除财务主管的节点
        }else{
            $deleteSql="DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowstagesflag IN ('CREATE_SIGN_TWO','CREATE_SIGN_ONE','CLOSE_WORKSTREAM','DO_PRINT')";
            $adb->pquery($deleteSql,array($recordId));//删除财务主管的节点
        }
    }

    /**
     * 合同领取的微信消息提醒
     */
    public function sendCustomizeMessage($record,$content)
    {
        global $adb;
        $sql = "select signid,receiveid,smownerid from vtiger_servicecontracts left join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid where servicecontractsid=?";
        $result = $adb->pquery($sql,array($record));
        if(!$adb->num_rows($result)){
            return false;
        }
        $row = $adb->fetchByAssoc($result,0);
        $assigned_user_id = $row['smownerid'];
        $signid = $row['signid'];
        $receiveid = $row['receiveid'];
        $userids = array_unique(array($assigned_user_id, $signid, $receiveid));
        $contract_no = $this->get("contract_no");
        $sql = "select email1 from vtiger_users where id in(" . implode(",", $userids) . ")";
        $result = $adb->pquery($sql, array());
        $this->_logs(array("signid"=>$signid,"receiveid"=>$receiveid,"assigned_user_id"=>$assigned_user_id,'contract_no'=>$contract_no));
        if (!$adb->num_rows($result)) {
            return false;
        }
        while ($row = $adb->fetchByAssoc($result)) {
            $emails[] = $row['email1'];
        }
        $Subject = '服务合同消息提醒';
        foreach ($emails as $email) {
            $this->sendWechatMessage(array('email' => trim($email), 'description' => $content, 'dataurl' => '#', 'title' => '你有一条关于合同编号：' . $contract_no . '的消息，请及时处理。！！！', 'flag' => 7));
            $address = array(
                array('mail' => trim($email), 'name' => '')
            );
        }

        $body = '您好！<br>&nbsp;&nbsp;    你有一条来自管理员的消息，请注意查收！<br>';
        Vtiger_Record_Model::sendMail($Subject, $body.$content, $address);
        return true;
    }
	/**
     * 出纳填写节点判断
     * @param $recordid
     * @return bool
     */
    public function checkDORETURNCANCEL($recordid){
        global $adb;
        $query="SELECT 1 FROM `vtiger_salesorderworkflowstages` WHERE salesorderid=? AND workflowstagesflag='DO_RETURN_CANCEL' AND isaction=1";
        if($adb->num_rows($adb->pquery($query,array($recordid)))){
            return true;
        }
        return false;
    }

    /**
     * 获取供应商合同的编号及ID
     * @param $request
     * @return array
     */
    public function getSuppNOAndID($request){
        global $adb;
        $recordid=$request->get('recordid');
        $suppname=$request->get('suppname');
        $suppname=trim($suppname);
        $returnData=array('flag'=>false);
        do {
            if($recordid<1){
                $returnData['msg'] = '服务合同有误！';
                break;
            }
            if(empty($suppname)){
                $returnData['msg'] = '采购合同编号为空！';
                break;
            }
            $query ="SELECT 1 FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.modulestatus!='c_cancel' AND suppliercontractsno=?";
            $result = $adb->pquery($query, array($suppname));
            if($adb->num_rows($result)){
                $returnData['msg'] = '采购合同已被占用！';
                break;
            }
            $query = 'SELECT accountname FROM vtiger_servicecontracts LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_servicecontracts.sc_related_to WHERE vtiger_servicecontracts.sc_related_to>0 AND vtiger_servicecontracts.servicecontractsid=?';
            $result = $adb->pquery($query, array($recordid));
            if ($adb->num_rows($result)) {
                $account = $result->fields['accountname'];
                $query = "SELECT vtiger_suppliercontracts.contract_no,vtiger_suppliercontracts.suppliercontractsid,vtiger_suppliercontracts.vendorid FROM vtiger_suppliercontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_suppliercontracts.suppliercontractsid 
                        WHERE vtiger_crmentity.deleted=0 AND vtiger_suppliercontracts.modulestatus='c_complete' AND vtiger_suppliercontracts.contract_no=?";
                $result = $adb->pquery($query, array($suppname));
                if ($adb->num_rows($result)) {
                    $suppArray = $result->fields;
                    $query = 'SELECT 1 FROM vtiger_vendor WHERE vendorid=? AND vendorname=?';
                    $result = $adb->pquery($query, array($suppArray['vendorid'], $account));
                    if ($adb->num_rows($result)) {
                        $returnData = array('flag' => true,
                            'data'=>array('contract_no' => $suppArray['contract_no'], 'suppid' => $suppArray['suppliercontractsid']),
                            'msg'=>'采购合同可以使用'
                        );
                    } else {
                        $returnData['msg'] = '采购合同与服务合同客户名称不一致！';
                    }
                } else {
                    $returnData['msg'] = '没有找到采购合同，可能原因：不是签收状态，合同编号不正确！';
                }
            } else {
                $returnData['msg'] = '服务合同的客户有误！';
            }
        }while(0);
        return $returnData;
    }
    public function generationNumber($entity){
        global $adb;
        $record=$entity['record_id'];
        $query="SELECT productclass,bussinesstype FROM `vtiger_contract_type` WHERE contract_type=?";
        $result=$adb->pquery($query,array($entity['contract_type']));
        $productclass=$adb->query_result($result,0,'productclass');
        $bussinesstype=$adb->query_result($result,0,'bussinesstype');
        $query="SELECT company_code FROM `vtiger_company_code` WHERE companyfullname=? limit 1";
        $result=$adb->pquery($query,array($entity['invoicecompany']));
        $company_codeno=$adb->query_result($result,0,'company_code');
        $_POST['sc_related_to']=8;
        $_POST['quantity']=1;
        $_POST['company_code']=!empty($company_codeno)?$company_codeno:'ZD';
        $_POST['products_code']=$productclass;
        $_POST['contract_template']='ZDY';
        //$_POST['signstatus']=2;
        $request=new Vtiger_Request($_POST, $_POST);
        $request->set('module','SContractNoGeneration');
        $request->set('view','Edit');
        $request->set('action','Save');
        $ressorder=new Vtiger_Save_Action();
        $ressorder->saveRecord($request);
        $adb->pquery("UPDATE vtiger_servicecontracts SET contract_no=(SELECT servicecontracts_no FROM vtiger_servicecontracts_print ORDER BY servicecontractsprintid DESC LIMIT 1),servicecontractsprintid=(SELECT MAX(servicecontractsprintid) FROM vtiger_servicecontracts_print LIMIT 1),servicecontractsprint=(SELECT concat(MAX(servicecontractsprintid),'-8') FROM vtiger_servicecontracts_print LIMIT 1),bussinesstype=? WHERE servicecontractsid=?",array($bussinesstype,$record));
        $adb->pquery("UPDATE vtiger_crmentity SET label=(SELECT servicecontracts_no FROM vtiger_servicecontracts_print ORDER BY servicecontractsprintid DESC LIMIT 1) WHERE crmid=?",array($record));
        $adb->pquery("UPDATE vtiger_servicecontracts_print SET nostand=1,constractsstatus='c_recovered',contractclassification='ServiceContracts',smownerid=? WHERE servicecontractsprintid=(SELECT * from (SELECT max(servicecontractsprintid) FROM vtiger_servicecontracts_print LIMIT 1) as m)",array($entity['assigned_user_id']));
    }

    public function getAgentList(){
        global $tyunweburl,$sault;
        $reconciliationUrl= $tyunweburl."api/app/agent-admin/v1.0.0/api/listForeignAgent";
        $time=time().'123';
        $sault=$time.$sault;
        $token=md5($sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time),
            CURLINFO_HEADER_OUT=>array(true));
        $res = $this->https_requestcomm($reconciliationUrl, '',$curlset);
        return $res;
    }

    public function agentContract(Vtiger_Request $request){
        $agentid= $request->get('agentid');
        $db = PearDatabase::getInstance();
        $sql = "select a.servicecontractsid,a.contract_no,b.activationcodeid,b.orderstatus from vtiger_servicecontracts a left join vtiger_activationcode b on a.servicecontractsid=b.contractid where a.modulestatus='已发放' and a.signaturetype='papercontract' and a.contract_classification='tripcontract' and a.agentid=?";
        $result = $db->pquery($sql,array($agentid));
        if(!$db->num_rows($result)){
            $return=array('success'=>false,'msg'=>'没有相关数据');
        }else{
            $data=array();
            while($row=$db->fetchByAssoc($result)){
                if($row['activationcodeid'] && $row['orderstatus']!='ordercancel'){
                    continue;
                }
                unset($row['activationcodeid']);
                $data[]=$row;
            }
            $return=array('success'=>true,'data'=>$data,'msg'=>'获取成功');
        }
        return $return;
    }

    //数字威客 同步乙方公司
    public function addWKSecondCompany($request){
        global $fangxinqianurl;
        $isSync = $request->get('isSync');
        $companyCode = $request->get("companyCode");
        $companyName = $request->get('companyName');
        if(!$companyCode){
            $accountRecordModel = Accounts_Record_Model::getCleanInstance("Accounts");
            $accountInfo = $accountRecordModel->getAccountInfoByAccountName($companyName);
            $companyCode ='WK'.$accountInfo['account_no'];
        }
        $data=array("companyName"=>$companyName,
            "creditCode"=>$request->get('creditCode'),
            "managerPhone"=>$request->get('managerPhone'),
            "companyCode"=>$companyCode
        );
        //未同步过则新增
        if(!$isSync){
            $url=$fangxinqianurl.'common/add_company';
            $res =  $this->https_requestcomm($url,json_encode($data),$this->getCURLHeader(true),true);
            $result = json_decode($res,true);
            $result['companyCode'] = $companyCode;
            return $result;
        }
        //已同步过 则调修改
        $url=$fangxinqianurl.'common/update_company';
        $result= $this->https_requestcomm($url,json_encode($data),$this->getCURLHeader(true),true);
        $result = json_decode($result,true);
        $result['companyCode'] =$companyCode;
        return $result;
    }

    //数字威客 生成签章
    public function generateSeal($request){
        if($request->get("checkType")){
            $governmentParams = array(
                'companyName'=>$request->get('name'),
                'creditCode'=>$request->get('creditCode'),
                'businessLicense'=>$request->get('businessLicense'),
                'phone'=>$request->get('phone'),
                'idCard'=>$request->get('idCard'),
            );
            $governmentResult = $this->companyAuth($governmentParams);
            if(!$governmentResult['success']){
                return array('success'=>false,'msg'=>$governmentResult['msg']);
            }
        }else{
            $phoneParams = array(
                'name'=>$request->get("name"),
                'idCard'=>$request->get("idCard"),
                'phone'=>$request->get("phone"),
                'bankCard'=>'',
                'code'=>'',
            );
            $phoneResult = $this->personAuth($phoneParams);
            if(!$phoneResult['success']){
                return array('success'=>false,'msg'=>$phoneResult['msg']);
            }
        }
        return array('success'=>true,'msg'=>'认证成功');
    }

    /**
     *生成个人签章
     *
     * @param $phoneParams
     * @return mixed
     */
    public function personAuth($phoneParams){
        global $fangxinqian_url;
        $url = $fangxinqian_url.'wk/special_person_auth';
        $params = array(
            'authType'=>0,
            'name'=>$phoneParams['name'],
            'idCard'=>$phoneParams['idCard'],
            'bankCard'=>'',
            'code'=>'',
            'phone'=>$phoneParams['phone']
        );

        $result = $this->https_requestcomm($url,json_encode($params),$this->getCURLHeader(),true);
        return json_decode($result,true);
    }

    /**
     * 生成企业签章
     *
     * @param $governmentParams
     * @return mixed
     */
    public function companyAuth($governmentParams){
        global $fangxinqian_url;
        $url = $fangxinqian_url.'wk/special_company_base';
        $params = array(
            'name'=>$governmentParams['companyName'],
            'creditCode'=>$governmentParams['creditCode'],
            'businessLicense'=>$governmentParams['businessLicense'],
            'phone'=>$governmentParams['phone'],
            'idCard'=>$governmentParams['idCard']
        );
        $result = $this->https_requestcomm($url,json_encode($params),$this->getCURLHeader(),true);
        return json_decode($result,true);
    }

    public function fangXinQianApiParams(){
        global $fangxinqianOcrKey,$fangxinqianOcrSecrect;
        return array(
            'appId'=>$fangxinqianOcrKey,
            'appSecret'=>$fangxinqianOcrSecrect
        );
    }


    /**
     * 生成数字威客合同
     *
     * 主体公司是珍岛则erp生成合同  或者直接去电子合同匹配模板 生成电子合同并返回
     *
     * @param $request
     */
    public function createWKElecServiceContracts(Vtiger_Request $request){
        $this->_logs(array('createWKElecServiceContracts',$request));
        $data = json_decode(base64_decode($request->get('data')),true);
        $this->_logs(array('data',$data));
        $classtype = 'buy';
        $servicecontractstype = $this->transferServiceContractsType($classtype);
        $contractType = $data["contractType"];
        if($contractType<10){
            $productCodes = 'wk00'.$contractType;
            if($contractType==0){
                $productCodes =  'wk002';
            }
        }elseif($contractType>=10 && $contractType<100){
            $productCodes = 'wk0'.$contractType;
        }else{
            $productCodes = 'wk'.$contractType;
        }
//        $productCodes = $contractType ==1 ? 'wk001' : 'wk002';
        $orderType=0; //普通购买
        $templateParams = array(
            'productcode'=>array($productCodes),
            'servicecontractstype'=>$servicecontractstype[1],
            "ordertype"=>$orderType,
        );
        $templateData = $this->matchElecContractTemplate($templateParams);
        if(count($templateData)!=1){
            return array('success'=>false,'msg'=>'无对应合同模板或非唯一合同模板');
        }
        $this->_logs(array("合同模板id1",$templateData[0]['templateId']));

        $templateId = $templateData[0]['templateId'];
        $contractname = $data["contractNo"];
        $accountInfo  = $data["accountInfo"];
        $companyInfo  = $data["companyInfo"];
        $contractData = $data['contractData'];
        $companyType =  $data['companyType'];
        $ownerid =  $data['ownerid'];
        $rowsData =  $data['rowsData'];
        $phone = $data['phone'];
        $this->_logs(array("合同模板id3",$templateData[0]['templateId']));

        global $adb,$SHZDWL_companyid;
        //如果是主体是公司则需生成对应的合同在erp 1第三方服务合同
        $servicecontractsid = '';
        if(!$companyType || $companyType==3){
            $companyInfo2 = $this->getCompanyInfoId($SHZDWL_companyid);
            $this->_logs(array("合同模板id4",$companyInfo2));
            if(empty($companyInfo2) || !count($companyInfo2)){
                $returnmsg=array('success'=>false,'msg'=>'获取主体公司信息失败,请重试');
                return $returnmsg;
            }
//            $companyInfo['secondAddress']= $companyInfo2['address'];
//            $companyInfo['secondCompany']= $companyInfo2['companyfullname'];
//            $companyInfo['secondName']= $companyInfo2['companyname'];
//            $companyInfo['secondPhone']= $companyInfo2['telphone'];
//            $companyInfo['secondEmail']= $companyInfo2['email'];
            $companyInfo['companyCode']= $companyInfo2['company_codeno'];
            $this->_logs(array("合同模板id5",$companyInfo));

//            if(!$companyType){
                //创建合同
                $this->_logs(array("合同模板id6",$companyInfo));

                $elecContract = $this->createWkServiceContracts($companyInfo2,$data["serviceYear"],

                    $accountInfo['firstCompany'],$data["contractPrice"],$productCodes,$servicecontractstype[0],$ownerid);
                $this->_logs(array("合同模板id7",$elecContract));

                if(!$elecContract['success']){
                    return array('success'=>false,'message'=>$elecContract['message']);
                }
                $servicecontractsid = $elecContract['servicecontractsid'];
                $contractname = $elecContract['contracts_no'];
//            }

        }
        $this->_logs(array("合同模板id2",$templateData[0]['templateId']));

        $previewResult = $this->elecontractWkPreview($accountInfo,$contractData,$companyInfo,$productCodes,$servicecontractsid,$orderType,$templateId,$contractname,$companyType,$phone,$rowsData);
        $this->_logs(array('elecontractWkPreviewresult',$previewResult));
        $contractId = $previewResult['data']['contractId'];
        if(!$previewResult['success']){
            if($servicecontractsid){
                $sql3 = "update vtiger_servicecontracts set eleccontractstatus=? where servicecontractsid=?";
                $adb->pquery($sql3,array('a_elec_actioning_fail',$servicecontractsid));
            }
            return array('success'=>false,'msg'=>$previewResult['msg']);
        }

        if($servicecontractsid){
            //修改合同
            $sql2 = "update vtiger_servicecontracts set eleccontractid=".$previewResult['data']['contractId'].
                ",eleccontracturl=?,elereceivermobile='".$accountInfo['first_phone']."',elereceiver='".
                $accountInfo['first_name']."',clientproperty='".$data['clientproperty']. "',eleccontracttplid=".
                $templateData[0]['templateId'].",eleccontracttpl='".$templateData[0]['templateName'].
                "',modulestatus='已发放',eleccontractstatus='b_elec_actioning' where servicecontractsid=?";
            $adb->pquery($sql2,array($previewResult['data']['contractUrl'],$servicecontractsid));
        }
        $this->_logs(array('data'=>$data));
        $this->_logs(array('receiver'=>$data['receiver']));
        //电子合同转正式
        $toFormParams = $data['receiver'];
        $toFormParams['contractid'] = $contractId;
        $this->_logs(array('toFormParams'=>$toFormParams));

        $result = $this->toFormalWkServiceContracts($toFormParams);
        $this->_logs(array('toFormalWkServiceContractsReturn',$result));
        if(!$result['success']){
            return array("success"=>false,"msg"=>$result['msg']);
        }
        $result['contractNo'] = $contractname;
        return $result;
    }

    public function toFormalWkServiceContracts($params){
        $this->_logs(array('elecToFormalParams1'=>$params));
        $elecToFormalParams = array(
            "contractId"=>$params['contractid'],//放心签合同id
            "receiver"=>array(
                "idcard"=>$params['idcard'],//接收方身份证/企业统一信用代码
                "name"=>$params['name'],//个人姓名/企业名称
                "phone"=>$params['phone'],//个人手机号码/企业法人手机号码
                "type"=>$params['type'] //接收方用户类型 0.企业 1.个人
            )
        );
        $this->_logs(array('elecToFormalParams'=>$elecToFormalParams));
        $data = $this->elecWKToFormal($elecToFormalParams);
        if($data['success']){
            if($params['servicecontractsid']){
                global $adb;
                $sql = "update vtiger_servicecontracts set modulestatus=?,eleccontractstatus=?,eleccontracturl=?,eleccontractid=? where servicecontractsid=?";
                $adb->pquery($sql,array('c_complete','c_elec_complete',$data['data']['contractUrl'],$data['data']['contractId'],$params['servicecontractsid']));
                $request=new Vtiger_Request(array());
                $request->set('module','ServiceContracts');
                $this->langImport($request);
                $recordModel = Vtiger_Record_Model::getInstanceById($params['servicecontractsid'],'ServiceContracts',true);
                $recordModel->fileSave($data['data']['contractUrl'],'files_style4','签收件');
            }
            return array('msg'=>'合同签章完成','success'=>true,'contractUrl'=>$data['data']['contractUrl']);
        }
        return array('msg'=>$data['msg'],'success'=>false);
    }


    public function elecontractWkPreview($accountInfo,$makeData,$companyInfo,$productCodes,$servicecontractsid,$orderType,$templateId,$contractname,$companyType,$phone,$dynamicRows){
        global $fangxinqian_url;
        $postData = array(
            "companyCode"=>$companyInfo['companyCode'],//公司编号
            "productCode"=>$productCodes,//产品编号
            "purchaseType"=>1,//购买类型（1.新增2.续费3.升级4.降级5.另购）
            "orderType"=>$orderType,//下单类型  0普通产品 1活动产品
            "contractNumber"=>$contractname,//合同编号
            "templateId"=>$templateId,
            "companyType"=>$companyType,
            "phone"=>$phone,
            //关键字替换的字段
            "replaces"=>array(
                "number"=>$contractname,
                "secondAddress"=>$companyInfo['secondAddress'],
                "secondCompany"=>$companyInfo['secondCompany'],
                "secondName"=>$companyInfo['secondName'],
                "secondPhone"=>$companyInfo['secondPhone'],
                "secondEmail"=>$companyInfo['secondEmail'],
                "secondCreditCode"=>$companyInfo['secondCreditCode'],

                "firstAddress"=>$accountInfo['firstAddress'] ? $accountInfo['firstAddress']:' ',
                "firstCompany"=>$accountInfo['firstCompany'] ? $accountInfo['firstCompany'] :' ',
                "firstName"=>$accountInfo['firstName'] ? $accountInfo['firstName']: ' ',
                "firstPhone"=>$accountInfo['firstPhone'] ? $accountInfo['firstPhone'] : ' ',
                "firstEmail"=>$accountInfo['firstEmail'],
                "firstCreditCode"=>$accountInfo['firstCreditCode'],

                'secondSignDate'=>date("Y年m月d日"),
                'firstSignDate'=>date("Y年m月d日"),
            ),
            "dynamicRows"=>
                !empty($dynamicRows)? array(array(
                "tableIndex"=>2,
                "rowIndex"=>1,//当前表格第几行后面加内容 0.表示表格第一行
                "fontSize"=>10, //字体大小
                "color"=>"000000",
                "rows"=>$dynamicRows)):array()
        );
        $postData['replaces'] = array_merge($postData['replaces'],$makeData);
        $postData['replaces']['pay1'] = $makeData['pay']==0 ? '√':' ';
        $postData['replaces']['pay2'] = $makeData['pay']==1 ? '√':' ';
        $postData['replaces']['payMode1'] = $makeData['payMode']==0 ? '√':' ';
        $postData['replaces']['payMode2'] = $makeData['payMode']==1 ? '√':' ';
        $postData['replaces']['deliver1'] = $makeData['deliver']==0 ? '√':' ';
        $postData['replaces']['deliver2'] = $makeData['deliver']==1 ? '√':' ';

        $this->_logs(array("postdata"=>$postData));
        $result = $this->https_requestcomm($fangxinqian_url.'wk/save_and_replace', json_encode($postData),$this->getCURLHeader(),true);
        $data = json_decode($result,true);
        if($data['success']){
            if($servicecontractsid){
                $request=new Vtiger_Request(array());
                $request->set('module','ServiceContracts');
                $this->langImport($request);
                $recordModel = Vtiger_Record_Model::getInstanceById($servicecontractsid,'ServiceContracts',true);
                $recordModel->fileSave($data['data']['contractUrl'],'files_style6','待签订件');
            }
            //保存放心签的待签订件
            return array('success'=>true,'msg'=>'','data'=>$data['data']);
        }
        return array('success'=>false,'msg'=>$data['msg']);
    }


    /**
     * 生成威客合同
     * @param $request
     */
    public function createWkServiceContracts($companyInfo,$serviceYear,$firstCompany,$contractPrice,$wkcode,$servicecontractstype,$creator=6934,$remark='',$signaturetype=''){
        $request=new Vtiger_Request($_POST, $_POST);
        $request->set('productlife',$serviceYear);

        $request->set("isWK",1);
        $request->set("wkcode",$wkcode);
        $this->_logs(array("createWkServiceContracts1",$firstCompany));
        $accountRecordModel = Accounts_Record_Model::getCleanInstance("Accounts");
        $firstCompanyInfo = $accountRecordModel->getAccountInfoByAccountName($firstCompany);
        $this->_logs(array("createWkServiceContracts2"));

        $request->set('accountid',$firstCompanyInfo['accountid']);
        $request->set('servicecontractstype',$servicecontractstype);
        $request->set('totalprice',$contractPrice);
        if(!$signaturetype){
            $request->set('eleccontractstatus', 'a_elec_not_apply');
        }
        $request->set('modulestatus', 'c_complete');
        $request->set('contract_classification', 'normalcontract');
        $request->set('originator', '客户导入');
        $request->set('originatormobile', '');
        $request->set('userid', $creator?$creator:6934);
        $request->set('remark', $remark);
        if($signaturetype){
            $request->set('signaturetype', $signaturetype);
        }

        $request->set('invoicecompany',$companyInfo['companyfullname']);
        $request->set('companycode',$companyInfo['company_code']);
        $request->set('classtype','buy');
        $request->set('contractattribute','standard');
        $request->set('contract_type','其他代运营');
        $request->set('parent_contracttypeid',11);
        $request->set('ispay',1);
        $elecContract = $this->createServiceContracts($request);
        $this->_logs(array("createWkServiceContracts3"));

        if(!$elecContract['id'] || !$elecContract['contracts_no']){
            return array('success'=>false,'message'=>'存在产品未设置合同编号或者多个产品对照合同编号不唯一');
        }
        return array('success'=>true,'servicecontractsid'=>$elecContract['id'],'contracts_no'=>$elecContract['contracts_no']);
    }

    /**
     * 放心签转正式合同接口
     *
     * @param $contractId
     * @return array
     */
    public function elecWKToFormal($params){
        $this->_logs(array("elecWktoForm"=>$params));
        global $fangxinqian_url;
        $postData = array(
            "contractId"=>$params['contractId'],//放心签合同id
            "receiver"=>array(
                "idcard"=>$params['idcard'],//接收方身份证/企业统一信用代码
                "name"=>$params['name'],//个人姓名/企业名称
                "phone"=>$params['phone'],//个人手机号码/企业法人手机号码
                "type"=>$params['type'] //接收方用户类型 0.企业 1.个人
            )
        );
        $this->_logs(array("fangxinqian_new_contract：", $params));
        $result = $this->https_requestcomm($fangxinqian_url.'wk/sign',json_encode($params),$this->getCURLHeader(),true);
        return json_decode($result,true);
    }

    /**
     * 身份证外部调用
     * @param $realNameParams
     * @return mixed
     */
    public  function realNameCheck($realNameParams){
        global $FANGXINQIANURL;
        $url = $FANGXINQIANURL.$this->realNameCheck;
        $params = array(
            'name'=>$realNameParams['name'],
            'identityNumber'=>$realNameParams['identityNumber']
        );
        $data = array_merge($this->fangXinQianApiParams(),$params);
        $result = $this->https_requestcomm($url,json_encode($data),array(CURLOPT_HTTPHEADER=>array("Content-Type:application/json")),true);
        return array('request'=>json_encode($data),'response'=>$result);
    }


    /**
     *生上传营业执照
     *
     * @param $phoneParams
     * @return mixed
     */
    public function uploadBusinessLicense(Vtiger_Request $request){
        global $fangxinqian_url;
//        $url = 'http://127.0.0.1:8011/wk/upload';
        $url = $fangxinqian_url.'/wk/upload';
        $result = $this->saveBusinessLicenseFileFromWeb($request->get("fileUrl"),$url);
//        $result = $this->saveBusinessLicenseFile($request->get("file"),$url);
        if(!$result['success']){
            return array('success'=>false,'msg'=>$result['msg']);
        }
        return $result;
    }

    public function saveBusinessLicenseFileFromWeb($fileUrl,$url){
        $fileSource = file_get_contents($fileUrl);
        $filename = time();
        global $root_directory;
        $new_file_name = $root_directory."temp/"  . $filename . ".png";
        file_put_contents($new_file_name,$fileSource);
        $fields = array(
            'file'=>'@'.$new_file_name
        );
        $this->_logs(array('putpartParas'=>$fields));
        $response = $this->putPart($fields,$url);
        unlink($new_file_name);
        return $response;
    }

    public function putPart($param,$url) {
        $headers = array('Content-Type: multipart/form-data');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_REFERER, '');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($ch);
        $output = json_decode($output,true);
        $this->_logs(array($output));
        if($output['success'] === FALSE) {
            return array('success'=>false,'msg'=>'上传文件失败');
        }
        return $output;
    }

    /**
     * base64文件保存
     * @param $base64File
     * @param $url
     * @return array|bool|mixed|string
     */
    public function saveBusinessLicenseFile($base64File,$url)
    {
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64File, $result)) {
            $type = $result[2];
            global $root_directory;
            $new_file = $root_directory."temp/" . date('Ymd', time()) . "/";
            if (!file_exists($new_file)) {
                mkdir($new_file, 0700);
            }
            $filename = time();
            $new_file = $new_file . $filename . ".{$type}";
            if (!file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64File)))) {
                return '';
            }
            $fields = array(
                'upload'=>'@'.$new_file
            );
            $this->_logs(array('putpartParas'=>$fields));
            $response = $this->putPart($fields,$url);
            unlink($new_file);
            return $response;
        }
        return array("success"=>false,'msg'=>'图片格式不正确');
    }

    /**
     * 是否验证券码
     * @param $record
     * @return string
     * @throws Exception
     */
    public function getCheckCoupon($record){
        if(!$record){
            return 0;
        }
        global $adb;
        //判断是否降级
        $classtype_sql="SELECT couponcode FROM vtiger_activationcode  WHERE contractid=? AND status IN(0,1)";
        $result_classtype = $adb->pquery($classtype_sql,array($record));
        if ($adb->num_rows($result_classtype)>0) {
            $row = $adb->query_result_rowdata($result_classtype, 0);
            if($row['couponcode']){
                return 1;
            }
        }
        return 0;
    }

    /**
     * 是否校验客户和总额
     *
     * @param $record
     * @return int
     * @throws Exception
     */
    public function getCheckAccountAndTotal($record){
        if(!$record){
            return 0;
        }
        global $adb;
        //判断是否降级
        $classtype_sql="SELECT sc_related_to,modulestatus FROM vtiger_servicecontracts  WHERE servicecontractsid=?";
        $result = $adb->pquery($classtype_sql,array($record));
        if ($adb->num_rows($result)>0) {
            $row = $adb->query_result_rowdata($result, 0);
            if($row['modulestatus']=='已发放' &&$row['sc_related_to']){
                return 1;
            }
        }
        return 0;
    }


    public function mainPartByContractNos(Vtiger_Request $request){
        $data = json_decode(base64_decode($request->get('data')),true);
        $contract_nos = $data["contract_nos"];
        if(empty($contract_nos)){
            return array();
        }
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select contract_no,invoicecompany from vtiger_servicecontracts where contract_no in('".implode("','",$contract_nos)."')",array());
        if(!$db->num_rows($result)){
            return array();
        }
        $resData = array();
        while ($row = $db->fetchByAssoc($result)){
            $resData[] = array(
                "contract_no"=>$row['contract_no'],
                "invoicecompany"=>$row['invoicecompany']
            );
        }
        return array($resData);
    }

    /**
     * 凯利龙修改合同状态
     *
     * @param $request
     * @return array
     */
    public function openUpdateModuleStatus($request){
        $db =PearDatabase::getInstance();
        $result = $db->pquery("select * from vtiger_crmentity where crmid=? and setype='ServiceContracts'",array($request->get("contractid")));
        if(!$db->num_rows($result)){
            return array('success'=>false,'msg'=>'合同id异常');
        }
        $modulestatus = $request->get("modulestatus");
        $reuslt2 = $db->pquery("select modulestatus from vtiger_modulestatus ",array());
        while ($row = $db->fetchByAssoc($reuslt2)){
            $modulestatuss[] = $row['modulestatus'];
        }
        if(!in_array($modulestatus,$modulestatuss)){
            return array('success'=>false,'msg'=>'合同状态不合法');
        }
        $db->pquery("update vtiger_servicecontracts set modulestatus=? where servicecontractsid=?",array($request->get("modulestatus"),$request->get("contractid")));
        return array('success'=>true,'data'=>'','msg'=>'');
    }

    public function getContractsAttachment($request){
        global $adb;
        $contractid = $request->get('contractid');
        $query = "SELECT * from vtiger_files where relationid=? AND delflag=0 AND description='ServiceContracts'";
//        $query = "SELECT * from vtiger_files where relationid=? AND delflag=0 AND description='ServiceContracts' AND style IN ('files_style3','files_style4','files_style5')";
        $sales = $adb->pquery($query, array($contractid));
        $rows = $adb->num_rows($sales);
        $ret_lists = array();
        global $site_URL;
        if ($rows > 0) {
            while($row=$adb->fetchByAssoc($sales)){
                $lists = array();
                //附件id
//                $lists['fileid'] = base64_encode($row['attachmentsid']);
                //名称
                $lists['name'] = $row['name'];
                //上传时间
                $lists['uploadtime'] = $row['uploadtime'];
                //附件类型
//                $lists['style'] = vtranslate($row['style'],"Files");
                //附件状态
//                $lists['filestate'] = vtranslate($row['filestate'],"Files");

                //附件地址
                $lists['fileurl'] = $site_URL.'/pdfread.php?fileid='.base64_encode($row['attachmentsid']);
                $ret_lists[]=$lists;
            }
            return  array("success"=>true,"data"=>$ret_lists,'msg'=>'获取成功');
        } else {
            return array("success"=>false,"data"=>$ret_lists,'msg'=>'该合同无对应附件');
        }
    }

    public function  getTyunWebCategory($request){
        $time = time() . '123';
        $sault = $time . $this->sault;
        $token = md5($sault);
        $curlset = array(CURLOPT_HTTPHEADER => array(
            "Content-Type:application/json",
            "S-Request-Token:" . $token,
            "S-Request-Time:" . $time));
        $this->_logs(array("token"=>$token,'url'=>$this->AllCategory));
        $res = $this->https_requestTweb($this->AllCategory, array(), $curlset);
        $json_data = json_decode($res, true);
        $this->_logs($json_data);
        if (!$json_data['success']) {
            exit(json_encode([
                'success' => 0,
                'msg' => $json_data['message']
            ]));
        }
        $list = [];
        if (!empty($json_data['data'])) {
            foreach ($json_data['data'] as $item) {
                //只返回包含套餐的分类
                if ($item['IsPackage']) {
                    $list[] = array(
                        "id"=>$item['ID'],
                        "title"=>$item["Title"]
                    );
                }
            }
        }
        return array("success"=>true,'data'=>$list,'msg'=>'获取成功');
    }

    public function hasOrder($record){
        if(!$record){
            return 0;
        }
        $db = PearDatabase::getInstance();
        $sql = "select 1 from vtiger_activationcode where contractid=? and  status!=2";
        $result = $db->pquery($sql,array($record));
        if($db->num_rows($result)){
            return 1;
        }
        return 0;
    }

    //验证移动端下单信息是否与已签收合同信息相同
    public function checkOrderIsSame($request){
        $db = PearDatabase::getInstance();
        $record = $request->get("record");
        $sql = "select a.* from vtiger_servicecontracts a  left join vtiger_crmentity b on a.servicecontractsid=b.crmid
where a.servicecontractsid=? and b.deleted=0";
        $result = $db->pquery($sql,array($record));
        if(!$db->num_rows($result)){
            return array("success"=>0,'msg'=>'无对应合同');
        }
        $result2 = $db->pquery("select sum(unit_price) as total from vtiger_receivedpayments where relatetoid=? and receivedstatus='normal' ",array($record));
        if($db->num_rows($result2)){
            $row2 = $db->fetchByAssoc($result2,0);
            if(floatval($row2['total'])>floatval($request->get('contractMoney'))){
                return array("success"=>0,'msg'=>'合同金额小于当前已回款金额,不能下单');
            }
        }
        $isstage = $request->get('isstage')>0?1:0;
        $issubmitverify = $request->get('issubmitverify');
        $invoicetype = $request->get('invoicetype');
        $row = $db->fetchByAssoc($result);
        $servicecontractstype =$row['servicecontractstype'];
        $isjoinactivity=$row['isjoinactivity'];
        if($row['modulestatus']=='c_complete' && $row['contract_type']=='T云WEB版') {
            if ($row['contract_classification'] == 'tripcontract') {
                return array("success" => 0, 'msg' => '三方合同不可使用');
            }
//            if ($isstage&&$row['signaturetype']!='eleccontract'&&$issubmitverify==1&&$invoicetype=='c_normal') {
//                $result = $db->pquery("select modulestatus from vtiger_contractsagreement where servicecontractsid=? and supplementarytype='stagepay'", array($record));
//                if (!$db->num_rows($result)) {
//                    return array("success" => 0, 'msg' => '请先上传分期付款协议');
//                }
//                while ($row3 = $db->fetchByAssoc($result)) {
//                    if ($row3['modulestatus'] != 'c_complete') {
//                        return array("success" => 0, 'msg' => '分期付款协议暂未审核通过,通过后方可下单');
//                    }
//                }
//
//            }
            if(intval($row['isstage'])!=intval($isstage)&&$isstage){
                return array("success" => 0, 'msg' => '下单选择的付款方式和已签收的合同付款方式不一致');
            }

            if (ServiceContracts_Record_Model::$servicecontractstype[$request->get('classtype')] != $servicecontractstype) {
                return array("success" => 0, 'msg' => '合同购买类型不一致，合同购买类型:' . (isset($servicecontractstype)?vtranslate($servicecontractstype, 'ServiceContracts'):'空'));
            }
            if ($row['categoryid'] != $request->get("categoryid")) {
                return array("success" => 0, 'msg' => '合同类型不一致');
//                return array("success"=>0,'msg'=>'合同类型不一致，合同类型:'.$request->get("categorys")[$row['categoryid']]);
            }

            if ($row['total'] != $request->get("contractMoney")) {
                return array("success" => 0, 'msg' => '合同金额与订单金额不一致,合同金额:' . $row['total']);
            }
            if ($request->get('cid') != $row['sc_related_to']) {
                return array("success" => 0, 'msg' => '订单客户和合同客户不是同一个客户');
            }

            $package = array();
            $otherproduct = array();
            $buiedProducts=array();
            $result2 = $db->pquery("select * from vtiger_salesorderproductsrel where servicecontractsid=? and salesorderid is null and multistatus=1", array($record));
            while ($row2 = $db->fetchByAssoc($result2)) {
                if($row2['thepackage']=='--'){
                    $otherproduct[$row2['productid']] = $row2['agelife'];
                    $otherproductnum[$row2['productid']] = $row2['productnumber'];
                    $buiedProducts[] = $row2['productname'];
                }else{
                    $package[$row2['productcomboid']] = $row2['agelife'];
                    $packagenum[$row2['productcomboid']] = $row2['productnumber'];
                    $buiedProducts[] = $row2['thepackage'];
                }

            }
            $buiedProducts = array_unique($buiedProducts);
            $clientProducts = array();
            $buyProductId = array_keys($package);
            $buyOtherProductId = array_keys($otherproduct);


            $productInfo = $request->get('productInfo');
            $activityId=0;
            $this->_logs(array('checkOrderIsSame','productInfo'=>$productInfo));
            foreach ($productInfo as $values) {
                if(intval($values['activityID'])){
                    $activityId=1;
                }
                $buyTerm=0;
                if($values['activityType']==3){
                    $activityChildID = explode("-",$values['activityChildID']);
                    $buyTerm = $activityChildID[0]+$activityChildID[1];
                }
                if($values['productList']){
                    $res = $this->mobileCheckProduct($values['productList'],$buyTerm,$package,$packagenum,$otherproduct,$otherproductnum,$buyProductId,$buyOtherProductId);
                    if(!$res['success']){
                        return $res;
                    }
                    $clientProducts = array_merge($res['clientProducts'],$clientProducts);
                }
                if($values['giftProduct']){
                    $res = $this->mobileCheckProduct($values['giftProduct'],$buyTerm,$package,$packagenum,$otherproduct,$otherproductnum,$buyProductId,$buyOtherProductId);
                    if(!$res['success']){
                        return $res;
                    }
                    $clientProducts = array_merge($res['clientProducts'],$clientProducts);
                }
                if($values['assistantProduct']){
                    $res = $this->mobileCheckProduct($values['assistantProduct'],$buyTerm,$package,$packagenum,$otherproduct,$otherproductnum,$buyProductId,$buyOtherProductId);
                    if(!$res['success']){
                        return $res;
                    }
                    $clientProducts = array_merge($res['clientProducts'],$clientProducts);
                }
            }
            if(!$activityId&&$isjoinactivity || $activityId&&!$isjoinactivity){
                return array("success"=>0,'msg'=>'订单参与活动情况和合同参与活动情况不一致，无法提交！');
            }

            $diffProduct = array_diff($buiedProducts,$clientProducts);
            if(count($diffProduct)){
                return array("success"=>0,'msg'=>'缺少产品:'.implode(',',$diffProduct));
            }
        }

        return array("success"=>1,'msg'=>'');
    }

    public function mobileCheckProduct($products,$buyTerm,$package,$packagenum,$otherproduct,$otherproductnum,$buyProductId,$buyOtherProductId){
        foreach ($products as $value) {
            if(!$buyTerm){
                $buyTerm = $value['buyTerm'];
            }
            if($value['packageID'] || $value['PackageID'] ){
                if($value['packageID'] && !in_array($value['packageID'], $buyProductId) ||
                    $value['PackageID']&&!in_array($value['PackageID'], $buyProductId)){
                    return array("success" => 0, 'msgCode' => '001', 'msg' => '套餐产品购买项不一致');
                }

                if( ($package[$value['packageID']] && $buyTerm!=round($package[$value['packageID']]/12)) ||
                    ($package[$value['PackageID']] && $buyTerm!=round($package[$value['PackageID']]/12))){
                    return array("success" => 0, 'msgCode' => '002', 'msg' => '套餐年限不一致');
                }
                if(($value['packageID'] && $value['count']!=$packagenum[$value['packageID']]) ||
                    ($value['PackageID'] && $value['count']!=$packagenum[$value['PackageID']])){
                    return array("success" => 0, 'msgCode' => '002', 'msg' => '套餐数量不一致');
                }
                $clientProducts[]=$value['packageTitle']?$value['packageTitle']:$value['PackageTitle'];
            }else{
                if($value['productID'] && !in_array($value['productID'], $buyOtherProductId) ||
                    $value['ProductID'] && !in_array($value['ProductID'], $buyOtherProductId)){
                    return array("success" => 0, 'msgCode' => '001', 'msg' => '另购产品购买项不一致');
                }

                if(($value['productID'] && $buyTerm!=round($otherproduct[$value['productID']]/12)) ||
                    ($value['ProductID'] && $buyTerm!=round($otherproduct[$value['ProductID']]/12))){
                    return array("success" => 0, 'msgCode' => '002', 'msg' => '另购套餐年限不一致');
                }
                if(($value['productID'] && $value['count']!=$otherproductnum[$value['productID']])||
                    ($value['ProductID'] && $value['count']!=$otherproductnum[$value['ProductID']])){
                    return array("success" => 0, 'msgCode' => '002', 'msg' => '另购套餐数量不一致');
                }
                $clientProducts[]=$value['ProductTitle']?$value['ProductTitle']:$value['productTitle'];
            }
        }
        return array("success" => 1,  'msg' => '成功','clientProducts'=>$clientProducts);
    }

    public function cancelPayToTyun($recordId,$money){
        $this->_logs(array('cancelPayToTyun'=>'取消匹配回款','recordid'=>$recordId,'money'=>$money,'date'=>date("Y-m-d H:i:s")));
        $db = PearDatabase::getInstance();
        $sql = "select * from vtiger_servicecontracts where servicecontractsid=?";
        $result = $db->pquery($sql,array($recordId));
        if(!$db->num_rows($result)){
            return;
        }
        $rowData = $db->query_result_rowdata($result,0);
        $total = $rowData['total'];
        global $orderpaymenturl,$limitDate;
        $payCode = '';
        $query="SELECT * FROM vtiger_activationcode WHERE contractid=? AND comeformtyun=1 AND `status`!=2";
        $result=$db->pquery($query,array($recordId));
        $productname = '';
        $products = array();
        $minActiveDate='';
        if(!$db->num_rows($result)){
            return;
        }
        if($rowData['paytotyun']<abs($money)){
            return;
        }

        $canConfirm = true;
        $result2 = $db->pquery("select sum(unit_price) as total from vtiger_receivedpayments where relatetoid=? and deleted=0 and ismatchdepart=1 and receivedstatus='normal'",array($recordId));
        $receivedpaymentsData = $db->fetchByAssoc($result2,0);
        $returnTotal = $receivedpaymentsData['total'];
        if(!$rowData['isstage'] && $returnTotal<$total){
            $canConfirm=false;
        }
        if(!$canConfirm){
            return;
        }

        while ($row = $db->fetch_row($result)){
            if($row['onoffline']=='line'){
                return;
            }
            $productname .= $row['productname'].'、';
            $customername = $row['customername'];
            $usercode = $row['usercode'];
            $usercodeid =$row['usercodeid'];
            $payCode = $row['paycode'];
            $productnamestemp=str_replace('&quot;','"',$row['detailproducts']);
            $detailproducts=json_decode($productnamestemp,true);
            $products = empty($products) ? $detailproducts:array_merge($products,$detailproducts);
            $createdtime = $row['createdtime'];
            if(strtotime($createdtime)<strtotime($limitDate)){
                return;
            }
            $activeDate =$row['activedate'];
            if($row['activedate'] && (!$minActiveDate || strtotime($row['activedate'])<strtotime($minActiveDate))){
                $minActiveDate = $row['activedate'];
            }
            $mobile = $row['mobile'];
            $creator = $row['creator'];
        }

        switch ($rowData['servicecontractstype']){
            case 'renew':
            case '续费':
                $producttype=4;
                break;
            case 'upgrade':
                $producttype=2;
                break;
            case 'newlyadded':
            case 'buy':
            case '新增':
                $producttype=1;
                break;
            case 'degrade':
                $producttype=3;
                break;
        }

        $contractStatus=0;
        if($rowData['modulestatus']=='c_complete'){
            $contractStatus=1;
        }
        $resparams = array(
            'userID'=>intval($usercodeid),
            'payCode'=>$payCode,
            'productType'=>intval($producttype),
            "totalPrice"=>floatval($rowData['total']),
            'products'=>$products,
            'payDate'=>date("Y-m-d H:i:s"),
            'contractStatus'=>$contractStatus,
            'money'=>$money
        );
        $postData=json_encode($resparams);
        $time=time().'123';
        $sault=$time.$this->sault;
        $token=md5($sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $result = $this->https_requestcomm($orderpaymenturl,$postData,$curlset,true);
        $this->_logs(array('orderpaymenturl'=>$orderpaymenturl,'postdata'=>$postData,'res'=>$result));
        $res = json_decode($result,true);
        if($res['code']!=200){
            return array("success"=>false,'msg'=>$res['message']);
        }

        $orderProducts = $res['data']['data'];
        foreach ($orderProducts as $orderProduct){
            if($orderProduct['detail']['OpenDate'] && $orderProduct['detail']['CloseDate'] ){
                $orderCode = $orderProduct['detail']['OrderCode'];
                $startDate = date("Y-m-d H:i:s",strtotime($orderProduct['detail']['OpenDate']));
                $endDate = date("Y-m-d H:i:s",strtotime($orderProduct['detail']['CloseDate']));
                $sql3 = "update vtiger_activationcode set startdate=?,expiredate=?,contractstatus=1,orderstatus='orderdoused' where ordercode = ?";
                $db->pquery($sql3,array($startDate,$endDate,$orderCode));

                if(strtotime($orderProduct['detail']['OpenDate'])<strtotime($minActiveDate) && $minActiveDate || !$minActiveDate){
                    $minActiveDate = $orderProduct['detail']['OpenDate'];
                }
            }
        }

        $allPayDays = $res['data']['allPayDays'];
        $paymentStatus = $orderProducts[0]['order']['PaymentStatus'];
        $tradingStatus = $orderProducts[0]['order']['TradingStatus'];

        //插入监听订单表
        if($rowData['isstage'] && bccomp(strval($money),strval($rowData['total']))!=0){
            $minActiveDate = date("Y-m-d H:i:s",strtotime($minActiveDate));
            $this->inListenOrder($recordId,$minActiveDate,$allPayDays,$creator);

        }

        if(!$paymentStatus){
            $db->pquery("update vtiger_listenorder set deleted=0 where servicecontractsid=?",array($recordId));
        }

        if(!$tradingStatus){
            //更新合同是否确认支付为已确认
            $updateSql = "update vtiger_servicecontracts set ispay=0 where servicecontractsid = ?";
            $db->pquery($updateSql,array($recordId));
        }
        $db->pquery("update vtiger_servicecontracts set paytotyun=? where servicecontractsid = ?",array($returnTotal,$recordId));
    }

    /**
     * 匹配后相关逻辑
     *
     * 非T云的则进入非T云延期签收表
     * T云的--确认到款
     *
     * @param $recordId
     * @param $money
     * @throws Exception
     */
    public function payAfterMatch($recordId,$money,$canSend=true,$currentid=0){
        $this->_logs(array('payAfterMatch'=>'匹配后触发回款认证','recordid'=>$recordId,'money'=>$money,'date'=>date("Y-m-d H:i:s")));
        $db = PearDatabase::getInstance();
        $sql = "SELECT
	a.*,
	b.smownerid ,
    c.activationcodeid,
    c.contractprice,
    c.couponcode
FROM
	vtiger_servicecontracts a
	LEFT JOIN vtiger_crmentity b ON a.servicecontractsid = b.crmid 
    LEFT JOIN vtiger_activationcode c on c.contractid=a.servicecontractsid
WHERE
	servicecontractsid = ? 
	AND b.deleted = 0 ";
        $result = $db->pquery($sql,array($recordId));
        if(!$db->num_rows($result)){
            return array("success"=>0,'msg'=>'没有可用合同');
        }
        $rowData = $db->query_result_rowdata($result,0);
        global $configcontracttypeNameTYUN;
        if($rowData['contract_type'] && !in_array($rowData['contract_type'],$configcontracttypeNameTYUN) || !$rowData['activationcodeid']){
            $this->_logs(array('payAfterMatch'=>'匹配后触发回款认证','recordid'=>$recordId,'msg'=>'进入非saas延期列表'));
            $this->noTyunContractDelaySign($recordId,$rowData,$rowData['smownerid'],$money);
            return array("success"=>0,'msg'=>'非T云WEB版，进入非saas延期列表');
        }
        $this->_logs(array('payAfterMatch'=>'匹配后触发回款认证','recordid'=>$recordId,'msg'=>'进入saas延期列表'));
        $total = $rowData['total']? $rowData['total']: $rowData['contractprice'];


        global $orderpaymenturl,$limitDate;
        $payCode = '';
        $query="SELECT * FROM vtiger_activationcode WHERE contractid=? AND comeformtyun=1 AND `status`!=2";
        $result=$db->pquery($query,array($recordId));
        $productname = '';
        $products = array();
        $minActiveDate='';
        $creator='';
        if(!$db->num_rows($result)){
            return array("success"=>0,'msg'=>'不存在可用订单');
        }
        $couponcode=$rowData['couponcode'];
        while ($row = $db->fetch_row($result)){
            if($row['onoffline']=='line'){
                $this->_logs(array('payAfterMatch'=>'匹配后触发回款认证','recordid'=>$recordId,'msg'=>'line订单，不确认付款'));
                return array("success"=>0,'msg'=>'line订单，不确认付款');
            }
            $productname .= $row['productname'].'、';
            $customername = $row['customername'];
            $usercode = $row['usercode'];
            $usercodeid =$row['usercodeid'];
            $payCode = $row['paycode'];
            $productnamestemp=str_replace('&quot;','"',$row['detailproducts']);
            $detailproducts=json_decode($productnamestemp,true);
            $products = empty($products) ? $detailproducts:array_merge($products,$detailproducts);
            $createdtime = $row['createdtime'];
            if(strtotime($createdtime)<strtotime($limitDate)){
                $this->_logs(array('payAfterMatch'=>'匹配后触发回款认证','recordid'=>$recordId,'msg'=>'订单创建时间小于限制时间之前，不确认付款'));
                return array("success"=>0,'msg'=>'订单创建时间小于限制时间之前，不确认付款');
            }
            $startdate=$row['startdate'];

            $activeDate =$row['activedate'];
            if($row['activedate'] && (!$minActiveDate || strtotime($row['activedate'])<strtotime($minActiveDate))){
                $minActiveDate = $row['activedate'];
            }
            $mobile = $row['mobile'];
            $creator = $row['creator'];
            $couponcode=$row['couponcode'];
        }
        if(!$payCode){
            $this->_logs(array('payAfterMatch'=>'匹配后触发回款认证','recordid'=>$recordId,'msg'=>'paycode为空'));
            return array("success"=>0,'msg'=>'paycode为空');
        }

        if($creator && $canSend){
            $isActive=true;
            if(!$startdate || $startdate=='0000-00-00 00:00:00'){
                $isActive=false;
            }
            //匹配回款通知
            $this->sendMatchWx($creator,$rowData['contract_no'],$money,$recordId,$isActive,$rowData['isstage']);
        }

        $canUserCoupon=false;
        $addContractToCoupon=false;
        $coupontocontractid=0;
        $contractIds=array();
        if($couponcode){
            $result3 = $db->pquery("select * from vtiger_coupontocontract where couponcode=?",array($couponcode));
            if($db->num_rows($result3)){
                $row3=$db->fetchByAssoc($result3,0);
                $contractIds = explode(",",ltrim($row3['contractids'],','));
                $facevalue = $row3['facevalue'];
                $coupontocontractid=$row3['coupontocontractid'];
                $consumetimes=$row3['consumetimes'];
                if(in_array($recordId,$contractIds)){
                    $canUserCoupon=true;
                } elseif($row3['num']>=$row3['consumetimes']+1){
                    $addContractToCoupon=true;
                    $canUserCoupon=true;
                }
            }
        }


        $canConfirm = true;
        $result = $db->pquery("select sum(unit_price) as total from vtiger_receivedpayments where relatetoid=? and deleted=0 and ismatchdepart=1 and receivedstatus='normal'",array($recordId));
        $receivedpaymentsData = $db->fetchByAssoc($result,0);
        $returnTotal = $receivedpaymentsData['total'];
        if(!$rowData['isstage'] && $money!=0){
            if($canUserCoupon){
                if($returnTotal<($total-$facevalue)){
                    $canConfirm=false;
                }
            }else{
                if($returnTotal<$total){
                    $canConfirm=false;
                }
            }

            if($returnTotal){
                $money = $returnTotal;
            }
        }
        if(!$canConfirm){
            return array("success"=>0,'msg'=>'全款合同，回款不足以确认付款');
        }


        switch ($rowData['servicecontractstype']){
            case 'renew':
            case '续费':
                $producttype=4;
                break;
            case 'upgrade':
                $producttype=2;
                break;
            case 'newlyadded':
            case 'buy':
            case '新增':
                $producttype=1;
                break;
            case 'degrade':
                $producttype=3;
                break;
        }

        $contractStatus=0;
        if($rowData['modulestatus']=='c_complete'){
            $contractStatus=1;
        }
        $resparams = array(
            'userID'=>intval($usercodeid),
            'payCode'=>$payCode,
            'productType'=>intval($producttype),
            "totalPrice"=>floatval($rowData['total']),
            'products'=>$products,
            'payDate'=>date("Y-m-d H:i:s"),
            'contractStatus'=>$contractStatus,
            'money'=>$money
        );
        if($canUserCoupon){
            $resparams['couponAmount']=$facevalue;
        }
        $postData=json_encode($resparams);
        $time=time().'123';
        $sault=$time.$this->sault;
        $token=md5($sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $result = $this->https_requestcomm($orderpaymenturl,$postData,$curlset,true);
        $this->_logs(array('orderpaymenturl'=>$orderpaymenturl,'postdata'=>$postData,'res'=>$result));
        Matchreceivements_Record_Model::recordLog(array('orderpaymenturl'=>$orderpaymenturl,'postdata'=>$postData,'res'=>$result),'automatchpayaftermatch');
        $res = json_decode($result,true);
        if($res['code']!=200){
            return array("success"=>false,'msg'=>$res['message']);
        }

        $orderProducts = $res['data']['data'];
        foreach ($orderProducts as $orderProduct){
            if($orderProduct['detail']['OpenDate'] && $orderProduct['detail']['CloseDate'] ){
                $openDate = date("Y-m-d H:i:s",strtotime($orderProduct['detail']['OpenDate']));
                $closeDate = date("Y-m-d H:i:s",strtotime($orderProduct['detail']['CloseDate']));
                $orderCode = $orderProduct['detail']['OrderCode'];
                $sql3 = "update vtiger_activationcode set startdate=?,expiredate=?,contractstatus=1,orderstatus='orderdoused' where ordercode = ?";
                $db->pquery($sql3,array($openDate,$closeDate,$orderCode));

                if(strtotime($openDate)<strtotime($minActiveDate) && $minActiveDate || !$minActiveDate){
                    $minActiveDate = $openDate;
                }
            }
        }

        $allPayDays = $res['data']['allPayDays'];
        $paymentStatus = $orderProducts[0]['order']['PaymentStatus'];
        $tradingStatus = $orderProducts[0]['order']['TradingStatus'];

        //插入监听订单表
        if($rowData['isstage'] && bccomp(strval($money),strval($rowData['total']))!=0){
            $this->inListenOrder($recordId,$minActiveDate,$allPayDays,$creator);

        }

        if($paymentStatus){
            $db->pquery("update vtiger_listenorder set deleted=1 where servicecontractsid=?",array($recordId));
        }

        if($addContractToCoupon&&$coupontocontractid && (count($contractIds)>0) && !in_array($recordId,$contractIds)){
            array_push($contractIds,$recordId);
            $contractIds = array_unique($contractIds);
            $consumetimes+=1;
            $db->pquery("update vtiger_coupontocontract set consumetimes=?,contractids='".implode(",",$contractIds)."' where coupontocontractid=?",array($consumetimes,$coupontocontractid));
        }

        $ispay=0;
        if($returnTotal && ($returnTotal>=$total || $canUserCoupon&&($returnTotal>=($total-$facevalue)))){
            $ispay=1;
        }
        //更新合同是否确认支付为已确认
        $updateSql = "update vtiger_servicecontracts set ispay=?,paytotyun=? where servicecontractsid = ?";
        $db->pquery($updateSql,array($ispay,$returnTotal,$recordId));

        if($rowData['modulestatus']=='c_complete'){
            $db->pquery('UPDATE vtiger_activationcode SET pushstatus=1,contractstatus=1 WHERE contractid=? AND `status` in(1,0)',array($recordId));
        }

        //判断是该合同的第几比回款  第一笔回款时候发送给客户
        if(!$rowData['issendsms'] && $tradingStatus){
            //发送短信给客户
            $TyunWebBuyServiceRecordModel = Vtiger_Record_Model::getCleanInstance('TyunWebBuyService');
            $sql2 = "select 1 from vtiger_activationcode where usercode = ? and contractid!= ? and status!=2";
            $result2 = $db->pquery($sql2,array($usercode,$recordId));
            $is_first_order = 0;
            if(!$db->num_rows($result2)){
                $is_first_order = 1;
            }
            $sms_data = array(
                "usercode"=>$usercode,
                "productname"=>rtrim($productname,'、'),
                "customername"=>$customername,
                "mobile"=>$mobile,
                "flag"=>1,
                "is_first_order"=>$is_first_order
            );
            $TyunWebBuyServiceRecordModel->web71360SendSMS($sms_data);

            //标注下短信已发送
            $db->pquery("update vtiger_servicecontracts set issendsms=1 where servicecontractsid=?",array($recordId));

        }
        return array("success"=>1,'msg'=>'确认到款成功');
    }

    /**
     * 给商务发送匹配成功通知
     *
     * @param $userId
     * @param $contractNo
     * @param $total
     */
    public function sendMatchWx($userId,$contractNo,$money,$servicecontractsid,$isactive,$isstage)
    {
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select last_name,email1 from vtiger_users where id=?",array($userId));
        $row = $db->fetchByAssoc($result,0);
        $title = '提醒：匹配回款成功';
        $content = $row['last_name'].'，您好，您名下合同编号为'.$contractNo.'的合同于'.date("Y年m月d日 H时i分s秒").' 成功匹配一笔'.$money.'元的回款,';
        if(!$isactive && $isstage){
            $leastResult = $this->leastPayMoney($servicecontractsid);
            if($leastResult['data']>0 && ($leastResult['data']-$money>0)){
                $content .='还剩余'.($leastResult['data']-$money).'方可激活订单使用，';
            }
        }
        $content .='请前往查看，谢谢';
        $this->_logs(array('email'=>trim($row['email1']),'description'=>$content,'dataurl'=>'#','title'=>$title,'flag'=>7));
        $this->sendWechatMessage(array('email'=>trim($row['email1']),'description'=>$content,'dataurl'=>'#','title'=>$title,'flag'=>7));
    }

    public function batchSendWarnFinanceWx($servicecontractsids){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select a.contract_no,b.accountname,a.servicecontractsid,c.enddate,c.userid from vtiger_servicecontracts a 
  left join vtiger_account b on a.sc_related_to=b.accountid 
  left join vtiger_listenorder c on a.servicecontractsid=c.servicecontractsid
where a.servicecontractsid in(".implode(",",$servicecontractsids).') and c.deleted=0',array());
        if(!$db->num_rows($result)){
            return;
        }
        $this->_logs(array('batchSendWarnFinanceWx'=>$db->num_rows($result)));
        while ($row = $db->fetchByAssoc($result)){
            $this->sendWarnFinanceWx($row['contract_no'],$row['accountname'],$row['enddate'],199);
        }
    }

    /**
     * 给财务发送订单消耗完毕提醒
     * @param $servicecontractsids
     */
    public function sendWarnFinanceWx($contract_no,$accountname,$enddate,$userid)
    {
        $this->_logs(array('sendWarnFinanceWx','contract_no'=>$contract_no,'accountname'=>$accountname,'enddate'=>$enddate,'userid'=>$userid));
        global $current_user;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
        $title = '提醒：客户订单金额耗尽';
        $content = $accountname."客户的合同编号(".$contract_no."),已回款金额已于".$enddate."全部消耗完，订单已禁用";
        $this->sendWechatMessage(array('email'=>trim($current_user->email1),'description'=>$content,'dataurl'=>'#','title'=>$title,'flag'=>7));

    }

    public function batchSendWarnSaleWx($servicecontractsids){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select a.contract_no,b.accountname,a.servicecontractsid,c.enddate,c.userid,d.mobile,b.email1 as accountemail,b.serviceid from vtiger_servicecontracts a 
  left join vtiger_account b on a.sc_related_to=b.accountid 
  left join vtiger_listenorder c on a.servicecontractsid=c.servicecontractsid
  left join vtiger_activationcode d on d.contractid=a.servicecontractsid
where a.servicecontractsid in(".implode(",",$servicecontractsids).') and c.deleted=0 group by servicecontractsid',array());
        if(!$db->num_rows($result)){
            return;
        }

        while ($row = $db->fetchByAssoc($result)){
            $this->sendWarnSaleWx($row['userid'],$row['contract_no'],$row['enddate']);
            if($row['serviceid']){
                $this->sendWarnSaleWx($row['serviceid'],$row['contract_no'],$row['enddate']);
            }
            $this->sendWarnSms($row['accountname'],$row['contract_no'],$row['enddate'],$row['mobile'],$row['accountemail']);
        }
    }

    /**
     * 发送提醒微信消息
     *
     * @param $userid
     * @param $orderCodes
     * @param $endDate
     * @throws Exception
     */
    public function sendWarnSaleWx($userid,$orderCodes,$endDate){
        global $adb,$current_user,$isDev;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
        $sql = "SELECT * FROM `vtiger_users` WHERE id=? LIMIT 1";
        $sel_result = $adb->pquery($sql, array($current_user->reports_to_id));
        $res_cnt = $adb->num_rows($sel_result);
        $agent_email = '';
        $agent_last_name='';
        $last_name = $current_user->last_name;

        if($res_cnt > 0) {
            $row = $adb->query_result_rowdata($sel_result, 0);
            $agent_email = $row['email1'];
            $agent_last_name = $row['last_name'];
        }

        $email = $current_user->email1;
//        $last_name = $current_user->last_name;
//        $departmentDataResult =  $adb->pquery("select c.departmentname from vtiger_users a  left join vtiger_user2department b on a.id=b.userid left join  vtiger_departments c on c.departmentid=b.departmentid where a.id=? limit 1",array($current_user->id));
//        $departmentDataRow = $adb->fetchByAssoc($departmentDataResult,0);
//        $department = $departmentDataRow['departmentname'];
//        $this->_logs(array('department'=>$department));

        $TyunWebBuyServiceRecordModel = TyunWebBuyService_Record_Model::getCleanInstance("TyunWebBuyService");
        $upmail=$TyunWebBuyServiceRecordModel->getBranchEmail($userid);
        $Subject = '分期付款回款消耗提醒';
        $body = '您好，您（您团队）名下'.$orderCodes.'订单将于'.$endDate.'，已回款金额将全部消耗完，烦督促客户尽快回款，否则该订单将被终止，谢谢！';
        $upmail=!empty($upmail)?$upmail:$email;

        $lastEmails = $upmail.'|'.$email."|".$agent_email;
        //发送小助手消息
        $this->sendWechatMessage(array('email'=>trim($lastEmails),'description'=>$body,'dataurl'=>'#','title'=>$Subject,'flag'=>7));

        //发送短信
        $emails = array(
            $upmail,$email,$agent_email
        );
        $this->sendWarnSmsToSale($emails,$body);

        //发送邮件
        $upmail=!empty($upmail)?$upmail:$email;
        $address = array(
            array('mail'=>$upmail, 'name'=>''),//营总监
            array('mail'=>$email, 'name'=>$last_name),//负责人
            array('mail'=>$agent_email, 'name'=>$agent_last_name),//负责人上级
        );
        $this->_logs(array('body'=>$body,'address'=>$address));
        Vtiger_Record_Model::sendMail($Subject,$body,$address);
    }
    public function sendWarnSmsToSale($emails,$content){
        $db = PearDatabase::getInstance();
        $sql = "select phone_mobile from vtiger_users where email1 in ('".implode("','",$emails)."') and status='Active' and email1!='0'";
        $result = $db->pquery($sql,array());
        $mobile=array();
        global $adb,$tyunweburlsms,$sault;
        $tyunweburl1=$tyunweburlsms.'api/app/aggregateservice-api/v1.0.0/api/SMS/SendMobileMessage';
        $time=time().'123';
        $sault1=$time.$sault;
        $token=md5($sault1);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        while ($row=$db->fetchByAssoc($result)){
            $rebinddata=json_encode(array("mobile"=>$row['phone_mobile'],"content"=>$content));
            $TyunWebBuyServiceRecordModel = TyunWebBuyService_Record_Model::getCleanInstance("TyunWebBuyService");
            $TyunWebBuyServiceRecordModel->https_request($tyunweburl1,$rebinddata,$curlset);
        }
    }



    /**
     *在分期付款的情况下，若已回款金额将在1个月后消耗完，将告知客户、商务、商务经理、商务总监XXX订单将于XX年XX月XX日将消耗完，促使商务督促客户尽快回款or促使客户尽快回款
     *
     * @param $customerName
     * @param $orderCodes
     * @param $endDate
     * @param $mobile
     */
    public function sendWarnSms($customerName,$orderCodes,$endDate,$mobile,$accountemail){
//        $content = '亲爱的'.$customerName.'，您的'.$orderCodes.'订单将于'.$endDate.'，已回款金额消耗完，若逾期未收到剩余款项，届时该订单所含有的含有的权益将无法使用，为避免给您造成不便，请您尽快处理，谢谢！';
        $content = '尊敬的'.$customerName.'，您好！您的'.$orderCodes.'订单对应的已支付款项将于'.$endDate.'消耗完毕，若逾期未支付剩余款项，届时该订单所含有的权益将无法使用，为避免给您造成不便，请您尽快安排处理，谢谢！';
        global $adb,$tyunweburlsms,$sault;
        $tyunweburl1=$tyunweburlsms.'api/app/aggregateservice-api/v1.0.0/api/SMS/SendMobileMessage';
        $time=time().'123';
        $sault1=$time.$sault;
        $token=md5($sault1);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));

        $rebinddata=json_encode(array("mobile"=>$mobile,"content"=>$content));
        $TyunWebBuyServiceRecordModel = TyunWebBuyService_Record_Model::getCleanInstance("TyunWebBuyService");
        $TyunWebBuyServiceRecordModel->https_request($tyunweburl1,$rebinddata,$curlset);

        if($accountemail){
            $Subject='回款不足提醒';
            $address = array(
                array('mail'=>$accountemail, 'name'=>''),//客户邮箱
            );
            $this->_logs(array('body'=>$content,'address'=>$address));
            Vtiger_Record_Model::sendMail($Subject,$content,$address,'客服系统',5);
        }

    }


    /**
     * 插入监听列表
     *
     * @param $contractid
     * @param $enddate
     * @param $allpaydays
     */
    public function inListenOrder($contractid,$startdate,$allpaydays,$userid){

        $db= PearDatabase::getInstance();
        $result = $db->pquery("select * from vtiger_servicecontracts where servicecontractsid=?",array($contractid));
        if(!$db->num_rows($result)){
            return;
        }
        $row = $db->fetchByAssoc($result,0);
        if(!$row['isstage']){
            return;
        }

        $result = $db->pquery("select startdate,allpaydays from vtiger_listenorder where servicecontractsid=?",array($contractid));
        $enddate ='';
        if($startdate){
            $enddate = date("Y-m-d",strtotime($startdate)+$allpaydays*24*60*60);
        }
        if(!$db->num_rows($result)){
            $db->pquery("insert into vtiger_listenorder (servicecontractsid,startdate,enddate,allpaydays,deleted,userid)  values(?,?,?,?,?,?)",array($contractid,$startdate,$enddate,$allpaydays,0,$userid));
            return;
        }
        $row = $db->fetchByAssoc($result,0);
        $allpaydays = $allpaydays>0 ? $allpaydays :$row['allpaydays'];
        $enddate = date("Y-m-d",strtotime($startdate)+$allpaydays*24*60*60);
//        if(strtotime($row['startdate'])<strtotime($startdate)){
//            $startdate = $row['startdate'];
//        }
        $db->pquery("update vtiger_listenorder set startdate=?,enddate=?,allpaydays=? where servicecontractsid=?",array($startdate,$enddate,$allpaydays,$contractid));

    }


    public function sendSignWarnToSaleEmail($notifyData,$isOver=false){
        if(empty($notifyData)){
            return;
        }
        $Subject = '合同待签收通知';
        $lastBody='Dear ';
        foreach ($notifyData as $key=>$notifyDatum){
            $str='';
            $userid=$key;
            global $adb,$current_user;
            $user = new Users();
            $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
            $sql = "SELECT * FROM `vtiger_users` WHERE id=? LIMIT 1";
            $sel_result = $adb->pquery($sql, array($current_user->reports_to_id));
            $res_cnt = $adb->num_rows($sel_result);
            $agent_email = '';
            $agent_last_name = '';
            if($res_cnt > 0) {
                $row = $adb->query_result_rowdata($sel_result, 0);
                $agent_email = $row['email1'];
                $agent_last_name = $row['last_name'];
            }

            $email = $current_user->email1;
            $last_name = $current_user->last_name;
            $lastBody .=$last_name.'<br>';
            $tyunWebBuyServiceRecordModel = TyunWebBuyService_Record_Model::getCleanInstance("TyunWebBuyService");
            $upmail=$tyunWebBuyServiceRecordModel->getBranchEmail($userid);
            $str .= "<table style='border: 1px solid black;border-collapse: collapse'><tr><th style='border-right: 1px solid black'>合同编号</th>
                    <th  style='border-right: 1px solid black'>合同客户名称</th>
                    <th  style='border-right: 1px solid black'>合同分类</th>
                    <th style='border-right: 1px solid black'>可签收最晚时间</th>
                    <th style='border-right: 1px solid black'>是否可申请延期签收</th>
                    </tr>";
            foreach ($notifyDatum as  $value) {
                    $str .= '<tr  style=\'border: 1px solid black\'>
                        <td  style=\'border-right: 1px solid black\'>' . $value['contract_no'] . '</td>
                        <td style=\'border-right: 1px solid black\'>' . $value['accountname'] . '</td>
                        <td style=\'border-right: 1px solid black\'>' . vtranslate($value['contract_type'],'ServiceContracts') . '</td>
                        <td style=\'border-right: 1px solid black\'>' . $value['lastsigndate'] . '</td>
                        <td style=\'border-right: 1px solid black\'>' . ($value['canapply']?'是':'') . '</td>
                </tr>';
            }
            $str .= "</table><br>";
            if($isOver){
                $lastBody = '<span style="color: red">请尽快签收以下合同,相关合同已超过可签收最晚时间！！！</span><br><br>' . $str;
            }else{
                $lastBody = '<span style="color: orange">请及时签收以下合同,如果合同类型为T云WEB版且未申请过延期签收，可提交合同扫描件延长30天签收！！！</span><br><br>' . $str;
            }

            $upmail=!empty($upmail)?$upmail:$email;
            $address = array(
                array('mail'=>$email, 'name'=>$last_name),//负责人
            );
            $cc = array(
                array('mail'=>$upmail, 'name'=>''),//营总监
                array('mail'=>$agent_email, 'name'=>$agent_last_name),//负责人上级

            );

            Vtiger_Record_Model::sendMail($Subject,$lastBody,$address,'CRM系统',1,$cc);
        }

    }

    /**
     * 匹配后记录最晚可签收时间
     *
     * @param $servicecontractsid
     */
    public function noTyunContractDelaySign($servicecontractsid,$row,$currentid,$money){
        $db = PearDatabase::getInstance();
        //匹配回款通知

        $this->sendMatchWx($currentid,$row['contract_no'],$money,$servicecontractsid,true,0);
        if($row['modulestatus']=='c_complete'){
            return;
        }
        $result2 = $db->pquery("select * from vtiger_contractdelaysign where servicecontractsid=?",array($servicecontractsid));
        $month = date("m");
        $monthData = ServiceContracts_Record_Model::$MONTH;
        $lastSign = date("Y-".$monthData[$month]);
        if($month==12){
            $lastSign = date("Y-".$monthData[$month],strtotime("+1 year"));
        }
        if(!$db->num_rows($result2)){
            global $current_user;
            $user = new Users();
            $current_user = $user->retrieveCurrentUserInfoFromFile($currentid);
            $_REQUES['record'] = '';
            $request = new Vtiger_Request($_REQUES, $_REQUES);
            $request->set('servicecontractsid',  $row['servicecontractsid']);
            $request->set('contract_no', $row['contract_no']);
            $request->set('accountid',  $row['sc_related_to']);
            $request->set('type', 'notyun');
            $request->set('matchdate', date("Y-m-d"));
            $request->set('lastsigndate',$lastSign);
            $request->set('modulestatus','a_apply_normal');
            $request->set('contract_type',$row['contract_type']);
            $request->set('hetongstatus',$row['modulestatus']);
            $request->set('contractsignstatus', 'nosign');
            $request->set('isdelay',0);
            $request->set('creator', $currentid);
            $request->set('module', 'ContractDelaySign');
            $request->set('view', 'Edit');
            $request->set('action', 'Save');
            $ressorder = new Vtiger_Save_Action();
            $ressorderecord = $ressorder->saveRecord($request);
        }

    }

    public function leastPayMoney($servicecontractsid){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select * from vtiger_activationcode where contractid=? and status in(0,1)",array($servicecontractsid));
        if(!$db->num_rows($result)){
            return array('success'=>false,'msg'=>'无订单');
        }
        $row = $db->fetchByAssoc($result,0);
        $payCode = $row['paycode'];
        $time = time() . '123';
        $sault = $time . $this->sault;
        $token = md5($sault);
        $curlset = array(CURLOPT_HTTPHEADER => array(
            "Content-Type:application/json",
            "S-Request-Token:" . $token,
            "S-Request-Time:" . $time));
        $res = $this->https_requestTweb($this->GetMinPaymentMoney, json_encode(array('payCode'=>$payCode)), $curlset);
        $this->_logs(array("token"=>$token,'url'=>$this->GetMinPaymentMoney,'paycode'=>$payCode,'res'=>$res));
        $json_data = json_decode($res, true);
        Matchreceivements_Record_Model::recordLog($json_data,'leastPay');
        if (!$json_data['success']) {
            return array('success' => false,'msg' => $json_data['message']);
        }
        return array('success'=>true,'msg'=>'','data'=>$json_data['data']);
    }

    /**
     * 下单后添加 合同分成人信息
     *
     * @param $servicecontractsid
     * @param $userid
     */
    public function serviceContractDivide($servicecontractsid,$userid,$accountid){
        $db = PearDatabase::getInstance();
        $result2 = $db->pquery("select 1 from vtiger_servicecontracts_divide where servicecontractid=?",array($servicecontractsid));
        if($db->num_rows($result2)){
            return;
        }

        $db->pquery("DELETE FROM `vtiger_servicecontracts_divide` WHERE servicecontractid =?", array($servicecontractsid));
        $saparateRecordModel = SeparateInto_Record_Model::getCleanInstance("SeparateInto");
        $shareInfo = $saparateRecordModel->getMarketingShareInfo($accountid);
        $userShare=100;
        if(count($shareInfo)){
            $userShare=$shareInfo['salesharing'];
            $promotionsharing=$shareInfo['promotionsharing'];
            $params1=array(
                $shareInfo['invoicecompany'],
                $shareInfo['userid'],
                $promotionsharing,
                $servicecontractsid,
                $shareInfo['departmentid'],
            );
            $db->pquery( "INSERT INTO `vtiger_servicecontracts_divide` (owncompanys, receivedpaymentownid,scalling, servicecontractid,signdempart) values (?,?,?,?,?)",$params1);

        }
        $result = $db->pquery("SELECT b.invoicecompany,a.departmentid FROM vtiger_user2department a left join vtiger_users b on a.userid=b.id WHERE a.userid=?",array($userid));
        $row = $db->fetchByAssoc($result,0);
        $params = array(
            $row['invoicecompany'],
            $userid,
            $userShare,
            $servicecontractsid,
            $row['departmentid']
        );
        $db->pquery( "INSERT INTO `vtiger_servicecontracts_divide` (owncompanys, receivedpaymentownid,scalling, servicecontractid,signdempart) values (?,?,?,?,?)",$params);
    }

    /**
     * 更改合同分期、全款
     */
    public function changeStage($servicecontractsid,$stage,$total){
        $db =PearDatabase::getInstance();
        $sql = "select * from vtiger_activationcode where contractid=?";
        $result = $db->pquery($sql,array($servicecontractsid));
        if($db->num_rows($result)){
            $row = $db->fetchByAssoc($result,0);
            if($row['startdate'] && $row['startdate']!='0000-00-00 00:00:00'){
                return array('success'=>false,'message'=>'订单已激活不允许切换回款类型');
            }
        }

        $result = $db->pquery("select sum(unit_price) as total from vtiger_receivedpayments where relatetoid=? and deleted=0 and ismatchdepart=1 and receivedstatus='normal'",array($servicecontractsid));
        $receivedpaymentsData = $db->fetchByAssoc($result,0);
        $returnTotal = $receivedpaymentsData['total'];
        if($returnTotal>=$total){
            return array('success'=>false,'message'=>'该合同已匹配回款金额大于合同金额');
        }

        if(!$stage && $returnTotal>0){
            $this->cancelPayToTyun($servicecontractsid,-$returnTotal);
            $db->pquery("update vtiger_listenorder set deleted=1 where servicecontractsid=?",array($servicecontractsid));
        }
        $db->pquery("update vtiger_servicecontracts set isstage=? where servicecontractsid=?",array($stage,$servicecontractsid));

        if($stage && $returnTotal>0){
            $this->payAfterMatch($servicecontractsid,$returnTotal,false);
        }
        return array('success'=>true,'message'=>'切换成功');
    }

    /**
     * 校验客户和合同
     *
     * @param Vtiger_Request $request
     * @return array
     */
    public function checkCustomerAndContract(Vtiger_Request $request){
        $db = PearDatabase::getInstance();
        $contractid = $request->get("contractid");
        $customerid = $request->get("customerid");
        $categoryid = $request->get("categoryid");
        $ignore_check_owner = $request->get("ignore_check_owner");
        $account_owner_uid = $request->get("account_owner_uid");
        $sql = "select modulestatus,categoryid,sc_related_to,isstage,b.smownerid,c.last_name as smownername,a.receiveid,d.last_name as receivename from vtiger_servicecontracts a  
  left join vtiger_crmentity b on a.servicecontractsid=b.crmid 
  left join vtiger_users c on c.id=b.smownerid
  left join vtiger_users d on d.id=a.receiveid
where a.servicecontractsid=? and b.deleted=0";
        $result = $db->pquery($sql,array($contractid));
        if(!$db->num_rows($result)){
            return array("success"=>0,'message'=>'无对应合同');
        }
        $row = $db->fetchByAssoc($result,0);
        if($row['modulestatus']=='c_complete'){
            if($customerid!=$row['sc_related_to']) {
                return array("success"=>0,'message'=>'选择客户与已签收合同的客户不一致');
            }
            if( $categoryid!=$row['categoryid']) {
                return array("success"=>0,'message'=>'选择的产品分类与已签收合同的分类不一致');
            }
            $fileRecordModel = Files_Record_Model::getCleanInstance("Files");
            if($row['isstage']==1 && !$fileRecordModel->isExistFile('ServiceContracts',$contractid,'files_style13')){
                return array("success"=>0,'message'=>'付款方式为分期，需在PC端提供分期协议，否则无法进入下一步');
            }
        }else{
            //非签收状态的合同 客户与已回款的客户不一致
            $result = $db->pquery("select * from vtiger_receivedpayments where relatetoid=? AND receivedstatus='normal'",array($contractid));
            if($db->num_rows($result)&&$customerid!=$row['sc_related_to']&&$row['sc_related_to']){
                return array("success"=>0,'message'=>'已回款的合同客户与您选择的客户不一致');
            }
        }

        if (!$ignore_check_owner && !in_array($account_owner_uid,array($row['smownerid'],$row['receiveid']))) {
            $result2 = $db->pquery("select last_name from vtiger_users where id=?",array($account_owner_uid));
            if($db->num_rows($result2)){
                $rowData = $db->fetchByAssoc($result2,0);
                return array("success"=>0,'message'=>'客户负责人:'.$rowData['last_name'].'<br>合同领取人:'.$row['smownername'].'<br>合同提单人:'.$row['receivename'].'<br>客户负责人需与合同领取人、提单人其一保持一致');
            }
        }
        return array("success"=>1,'message'=>'成功');
    }


    public function cancelActivationCode(Vtiger_Request $request){
        global $adb,$current_user;
        $userId = $request->get("userid");
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($userId);
        $contractid =$request->get("servicecontractsid");
        $query="SELECT * FROM vtiger_activationcode WHERE contractid=? AND `status`!=2";
        $type_result=$adb->pquery($query,array($contractid));
        if($adb->num_rows($type_result)){
            $max_activationcodeid = 0;
            while ($row = $adb->fetch_row($type_result)){
                $type_result_datas[] = $row;
                $comeformtyun = $row['comeformtyun'];
                $user_id = $row['usercodeid'];
                $usercode = $row['usercode'];
                $contractid = $row['contractid'];
                $max_activationcodeid = max($row['activationcodeid'],$max_activationcodeid);
                $createdtime =$row['createdtime'];
                $activedate = $row['activedate'];
                $contract_no=$row['contractname'];
                $orderstatus=$row['orderstatus'];
                $couponcode=$row['couponcode'];
            }
            if($orderstatus=='ordercancel'){
                return array("success"=>false,'message'=>"订单已取消");
            }

            if($comeformtyun == 1){
                $contractModuleModel = ServiceContracts_Module_Model::getInstance("ServiceContracts");
                $query='SELECT 1 FROM vtiger_activationcode WHERE activationcodeid>? AND contractid != ? AND usercodeid=? AND `status`!=2';
                $result=$adb->pquery($query,array($max_activationcodeid,$contractid,$user_id));
                if($adb->num_rows($result)){
                    return array("success"=>false,'message'=>"请先将该账号对应的续费,或升降级合同作废掉再进行操作");
                }
                if(!$activedate){
                    $recordModel=Vtiger_Record_Model::getCleanInstance('TyunWebBuyService');
                    $Repson=$recordModel->doOrderCancelByContractNo($contract_no,$user_id,$usercode);
                    $jsonData=json_decode($Repson,true);
                    if($jsonData['code']=='200'){
                        $sql="UPDATE  vtiger_activationcode SET `status`=2,orderstatus='ordercancel',canceldatetime=? WHERE contractname=?";
                        $adb->pquery($sql,array(date('Y-m-d H:i:s'),$contract_no));
                        $contractModuleModel->clearCancelOrderRelations($contractid,$couponcode);
                        return array("success"=>true,'message'=>"合同相关的订单取消成功");
                    }
                    return array("success"=>false,'message'=>"合同相关的订单取消失败");
                }

                $result = $adb->pquery("select 1 from vtiger_salesorderworkflowstages where salesorderid=? and  isaction < 2 ",array($contractid));
                if($adb->num_rows($result)){
                    return array("success"=>false,'message'=>"该合同存在未完结的工作流");
                }

                $currentMonth = date("Y-m");
                $createMonth = date("Y-m",strtotime($createdtime));
                if(strtotime($currentMonth)>strtotime($createMonth)){
                    $workflowsid=$contractModuleModel->cancelCrossMonthOrderWorkFlowsid;
                    $messgage='请走线下邮件申请，最终审批通过之后，由对应审批人线上审批通过之后，方可作废成功';
                }else{
                    $workflowsid=$contractModuleModel->cancelOrderWorkFlowsid;
                    $messgage='工作流已提交,审核通过后自动取消订单';
                }

                $contractModuleModel->makeCancelOrderWorkFlows($contractid,$workflowsid,$current_user->id);
                return array("success"=>true,'code'=>1,'message'=>$messgage);
            }

        }else{
            return array("success"=>false,'message'=>"未查询到的该合同下有可用的订单！");
        }

        $sql = "SELECT 
                IFNULL(P.activecode,M.activecode) AS activecode,
                M.usercode AS usercode,
                M.classtype AS classtype,
                M.contractname AS contractno,
				M.receivetime,
                IFNULL(P.customername,M.customername) AS customername,
                M.agents,
                (SELECT MAX(str_to_date(REPLACE(MM.expiredate,'/','-'),'%Y-%m-%d')) FROM vtiger_activationcode MM WHERE MM.status IN(0,1) AND MM.usercode=M.usercode) AS expiredate 
                FROM vtiger_activationcode M
                LEFT JOIN vtiger_activationcode P ON(M.buyid=P.activationcodeid) WHERE M.status IN(0,1) AND M.contractname=?";
        $sel_result = $adb->pquery($sql, array($contract_no));
        $res_cnt = $adb->num_rows($sel_result);

        if($res_cnt > 0) {
            $row = $adb->query_result_rowdata($sel_result, 0);
            $classtype = $row['classtype'];
            $status = $row['status'];
            $usercode = $row['usercode'];
            $receivetime = $row['receivetime'];
            $query="SELECT 1 FROM vtiger_activationcode WHERE usercode=? AND `status` IN(0,1) AND receivetime>?";
            $query_result=$adb->pquery($query,array($usercode,$receivetime));
            if($classtype!= 'buy' && $adb->num_rows($query_result)>0){
                return array("success"=>false,'message'=>"作废失败：合同存在续费或升级合同!");
            }
            if($classtype == 'buy' && $status == '1'){
                return array("success"=>false,'message'=>"作废失败：请先取消激活");
            }
            return $this->invalidContract($row);
        }
        return array("success"=>true,'message'=>"");
    }

    public function isOrderCancel($contractid){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select orderstatus from vtiger_activationcode where contractid=? and status in(0,1) limit 1",array($contractid));
        if(!$db->num_rows($result)){
            return false;
        }
        $row = $db->fetchByAssoc($result,0);
        if($row['orderstatus']=='ordercancel'){
            return false;
        }
        return true;
    }

    public function batchStopOrder($contractNos){
        global $sault;
        $resultData=array();
        foreach ($contractNos as $contractNo){
            $params = array(
                'ContractCode'=>$contractNo
            );
            $postData=json_encode($params);
            $time=time().'123';
            $sault1=$time.$sault;
            $token=md5($sault1);
            $curlset=array(CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json",
                "S-Request-Token:".$token,
                "S-Request-Time:".$time));
            $res = $this->https_requestTweb($this->ContractConfirmTimeout, $postData,$curlset);
//            $this->_logs(array('url'=>$this->ContractConfirmTimeout,'params'=>$params,'result'=>$res));
            $data = json_decode($res,true);
            $resultData[]=$data;

            if(!$data['success']){
//                $this->_logs(array('停止订单失败','batchStopOrder'=>date("Y-m-d"),'contract_no'=>$contractNo,'result'=>$res),'stoporder');
            }
        }
        return $resultData;
    }

    public function getForeignTradeContract(Vtiger_Request $request){
        $agentid= $request->get('agentid');
        $typeArray = ServiceContracts_Record_Model::$servicecontractstype;
        $servicecontractstype= $typeArray[$request->get('servicecontractstype')];

        $accountname= $request->get('accountname');
        $productid = $request->get('productid');
        $ispackage = $request->get('ispackage');
        $db = PearDatabase::getInstance();
        $sql = "select a.servicecontractsid,a.contract_no,b.activationcodeid,b.orderstatus,a.total from vtiger_servicecontracts a 
  left join vtiger_activationcode b on a.servicecontractsid=b.contractid 
 left join vtiger_account c on c.accountid=a.sc_related_to
where a.modulestatus='c_complete' and a.signaturetype='papercontract' and a.agentid=? and a.servicecontractstype=? and c.accountname=?";
        $result = $db->pquery($sql,array($agentid,$servicecontractstype,$accountname));
        if(!$db->num_rows($result)){
            $return=array('success'=>false,'msg'=>'没有相关数据');
        }else{
            $data=array();
            while($row=$db->fetchByAssoc($result)){
                if($row['activationcodeid'] && $row['orderstatus']!='ordercancel'){
                    continue;
                }

                if($productid){
                    if(!$ispackage){
                        $result2 = $db->pquery("select agelife from vtiger_salesorderproductsrel where servicecontractsid=? and productcomboid=? limit 1",array($row['servicecontractsid'],$productid));
                    }else{
                        $result2 = $db->pquery("select agelife from vtiger_salesorderproductsrel where servicecontractsid=? and productid=? limit  1",array($row['servicecontractsid'],$productid));
                    }
                    $relResult = $db->fetchByAssoc($result2,0);
                    $row['agelife']=ceil($relResult['agelife']/12);
                }
                if(!$row['agelife']&&in_array($request->get('servicecontractstype'),array('upgrade','degrade','renew'))){
                    continue;
                }

                unset($row['activationcodeid']);

                $data[]=$row;
            }
            $return=array('success'=>true,'data'=>$data,'msg'=>'获取成功');
            if(!count($data)){
                $return=array('success'=>false,'msg'=>'没有相关数据');
            }
        }
        return $return;
    }

    public function getForeignTradeOrder(Vtiger_Request $request){
        $servicecontractsid = $request->get("servicecontractsid");
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select * from vtiger_salesorderproductsrel where servicecontractsid=?",array($servicecontractsid));
        if(!$db->num_rows($result)){
            return array(
                'success'=>0,
                'msg'=>'无相关购买产品信息'
            );
        }
        $package=array();
        $otherproduct=array();
        while ($row=$db->fetchByAssoc($result)){
            if($row['thepackage']=='--'){
                $otherproduct[]=array(
                    'productid'=>$row['productcomboid'],
                    'productname'=>$row['productname'],
                    'productnumber'=>$row['productnumber'],
                    'specificationid'=>$row['standard'],
                    'agelife'=>ceil($row['agelife']/12)
                );
            }else{
                $package=array(
                    'productid'=>$row['productcomboid'],
                    'productname'=>$row['thepackage'],
                    'productnumber'=>$row['productnumber'],
                    'specificationid'=>$row['standard'],
                    'agelife'=>ceil($row['agelife']/12)
                );
            }
        }
        return array(
            'success'=>1,
            'package'=>$package,
            'otherproduct'=>$otherproduct
        );
    }
    public function checkContract($params){
        global $adb;
        $paramsStr=implode(',',$params);
        $query="SELECT relatetoid as cno,'回款' as aaa FROM vtiger_receivedpayments WHERE receivedstatus = 'normal' AND deleted=0 AND vtiger_receivedpayments.relatetoid in (".$paramsStr.")
                UNION ALL
                SELECT servicecontractsid as cno,'工单' as aaa FROM vtiger_salesorder LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_salesorder.salesorderid WHERE vtiger_crmentity.deleted=0 AND vtiger_salesorder.modulestatus!='c_cancel' AND vtiger_salesorder.servicecontractsid in(".$paramsStr.")
                UNION ALL 
                SELECT servicecontractsid as cno,'充值单' as aaa FROM vtiger_refillapplication LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_refillapplication.refillapplicationid WHERE vtiger_crmentity.deleted=0 AND vtiger_refillapplication.modulestatus<>'c_cancel' AND vtiger_refillapplication.rechargesource != 'contractChanges' and vtiger_refillapplication.servicecontractsid in(".$paramsStr.")
                UNION ALL
                SELECT contractid as cno,'发票' as aaa FROM vtiger_newinvoice LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_newinvoice.invoiceid WHERE vtiger_crmentity.deleted=0 AND vtiger_newinvoice.contractid in(".$paramsStr.")";
        $result=$adb->pquery($query);
        if($adb->num_rows($result)){
            $returnData=array('flag'=>true);
            while($row=$adb->fetch_array($result)){
                $returnData['data'][$row['cno']].='存在'.$row['aaa'].'，';
            }
            return $returnData;
        }else{
            return array('flag'=>false);
        }
    }

    /**
     * 关停合同
     * @param $contract_no
     * @return array|void
     */
    public function doCloseTYUNContract($contract_no){
        global $adb;
        $query="SELECT * FROM vtiger_activationcode WHERE contractname=? AND `status`!=2";
        $type_result=$adb->pquery($query,array($contract_no));
        if($adb->num_rows($type_result)){
            $max_activationcodeid = 0;
            while ($row = $adb->fetch_row($type_result)){
                $type_result_datas[] = $row;
                $comeformtyun = $row['comeformtyun'];
                $user_id = $row['usercodeid'];
                $usercode = $row['usercode'];
                $contractid = $row['contractid'];
                $max_activationcodeid = max($row['activationcodeid'],$max_activationcodeid);
            }
            if($comeformtyun == 1){
                $query='SELECT 1 FROM vtiger_activationcode WHERE activationcodeid>? AND contractid != ? AND usercodeid=? AND `status`!=2';
                $result=$adb->pquery($query,array($max_activationcodeid,$contractid,$user_id));
                if($adb->num_rows($result)){
                    return array("flag"=>false,'msg'=>"请先将该账号对应的续费,或升降级合同作废掉再进行操作");
                }
                global $tyunweburl,$sault,$adb;
                $url=$tyunweburl.'api/micro/order-basic/v1.0.0/api/Order/StopOrderByContractCode';
                $time=time().'123';
                $sault1=$time.$sault;
                $token=md5($sault1);
                $curlset=array(CURLOPT_HTTPHEADER=>array(
                    "Content-Type:application/json",
                    "S-Request-Token:".$token,
                    "S-Request-Time:".$time));
                $canceldata=json_encode(array("userID"=>$user_id,"contractCode"=>$contract_no));
                $this->_logs(array('orderdoclosedbycontractno:',$canceldata));
                //$data=$this->https_request($url,$canceldata,$curlset);
                $data=$this->https_requestcomm($url,$canceldata,$curlset);
                $this->_logs(array($url,$data));
                $jsonData=json_decode($data,true);
                if($jsonData['code']=='200'){
                    $sql="UPDATE vtiger_activationcode SET `status`=2,orderstatus='ordercancel',canceldatetime=?,isdisabled=1 WHERE contractname=?";
                    $adb->pquery($sql,array(date('Y-m-d H:i:s'),$contract_no));
                    return array("flag"=>true,'msg'=>"合同相关的订单关闭成功");
                }
                return array("flag"=>false,'msg'=>"合同相关的订单关闭失败");
            }
        }else{
            return array("flag"=>true,'msg'=>"合同激活码或订单关闭成功！");
        }
    }


    public function wkRefuseContract(Vtiger_Request $request){
        global $fangxinqian_url;
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select eleccontractid,modulestatus,servicecontractsid,docanceltime,elechandreason from vtiger_servicecontracts  where servicecontractsid=?",array($request->get("contractid")));
        if(!$db->num_rows($result)){
            return array("success"=>false,'msg'=>'没有合同，作废失败');
        }
        $row = $db->fetchByAssoc($result,0);
        $contractid=$row['eleccontractid'];
        $viewURL=$fangxinqian_url.$this->wkRefuseContract;
        $params=array(
            "phone"=>$request->get("phone"),
            "contractId"=>$contractid,
            "reason"=>$request->get("reason"),
        );
        $result = $this->https_requestcomm($viewURL,json_encode($params),$this->getCURLHeader(),true);
        $data=json_decode($result,true);
        $this->_logs(array('wkRefuseContract','params'=>$params,'res'=>$result));
        if($data['success']){
            $sql = "update vtiger_servicecontracts set eleccontractstatus='c_elec_cancel',elechandreason='".$request->get("reason").
                "',modulestatus='c_cancel',docanceltime='".date("Y-m-d H:i:s")."' where servicecontractsid=?";
            $db->pquery($sql,array($request->get("contractid")));
            //记录到日志
            $array = array(
                'modulestatus'=>array(
                    'oldValue'=>vtranslate($row['modulestatus'],'ServiceContracts'),
                    'currentValue'=>vtranslate('c_cancel','ServiceContracts')
                ),
                'docanceltime'=>array(
                    'oldValue'=>$row['docanceltime'],
                    'currentValue'=>date("Y-m-d H:i:s")
                ),
                'eleccontractstatus'=>array(
                    'oldValue'=>vtranslate($row['eleccontractstatus'],'ServiceContracts'),
                    'currentValue'=>vtranslate('c_elec_cancel','ServiceContracts')
                ),
                'elechandreason'=>array(
                    'oldValue'=>$row['elechandreason'],
                    'currentValue'=>$request->get("reason")
                )
            );
            $this->setModTracker('ServiceContracts',$row['servicecontractsid'],$array);
        }
        return array(
          "success"=>$data['success'],
          "msg"=>$data['msg']
        );
    }

    public function wkSignContract(Vtiger_Request $request){
        global $fangxinqian_url;
        $viewURL=$fangxinqian_url.$this->wkSignContract;
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select eleccontractid from vtiger_servicecontracts  where servicecontractsid=?",array($request->get("contractid")));
        if(!$db->num_rows($result)){
            return array("success"=>false,'msg'=>'没有合同，作废失败');
        }
        $row = $db->fetchByAssoc($result,0);
        $contractid=$row['eleccontractid'];
        $params=array(
            "phone"=>$request->get("phone"),
            "contractId"=>$contractid,
            "code"=>$request->get("code")?$request->get("code"):'00000',  //默认code是为了躲过验证
        );
        $result = $this->https_requestcomm($viewURL,json_encode($params),$this->getCURLHeader(),true);
        $data=json_decode($result,true);
        $this->_logs(array('wkSignContract','params'=>$params,'res'=>$result));
        if($data['success']){
            return array(
                'success'=>true,
                'contracturl'=>$data['data']['contractUrl']
            );
        }
        return array(
            "success"=>false,
            'msg'=>$data['msg']
        );
    }

    public function getWkExtendInfo($contractid){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select wkcode,wkcontactname,wkcontactphone from vtiger_servicecontracts_wk_extend where servicecontractsid=?",array($contractid));
        if(!$db->num_rows($result)){
            return array();
        }
        $row = $db->fetchByAssoc($result,0);
        return $row;
    }

    //将审核结果同步到威客后台
    public function syncVerifyResultToWk($contractid,$contract_url='',$rejectMsg='',$cancel=0){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT
	a.*,
	b.eleccontracturl,
    b.modulestatus
FROM
	vtiger_servicecontracts_wk_extend a
	LEFT JOIN vtiger_servicecontracts b on a.servicecontractsid = b.servicecontractsid 
WHERE
	a.servicecontractsid =?",array($contractid));
        $tid='';
        $eleccontracturl='';
        $modulestatus='';
        if($db->num_rows($result)){
            $row = $db->fetchByAssoc($result,0);
            $tid=$row['wkcode'];
            $eleccontracturl=$row['eleccontracturl'];
            $modulestatus=$row['modulestatus'];
        }
        if(!$tid){
            return array('success' => false,'msg' => '没有找到券码');
        }
        $contract_status=0;
        if($modulestatus=='已发放'){
            $contract_status=2;
        }
        if($cancel){
            $contract_status=4;
        }
        if($rejectMsg){
            $contract_status=1;
            $contractid=0;
        }
        $timestamp=time().'123';
        $req_token=md5(md5($this->wkSault).$timestamp);
        $tyunparams=array(
            'req_token'=>$req_token,
            'timestamp'=>$timestamp,
            'audited_msg'=>$rejectMsg,
            'contract_id'=>$contractid,
            'contract_url'=>$eleccontracturl,
            'contract_status'=>$contract_status,
            'tid'=>$tid
        );
        $postData=json_encode($tyunparams);
        $time=time().'123';
        $sault=$time.$this->sault;
        $token=md5($sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json"));
        $res = $this->https_requestTweb($this->customContractCallback, $postData,$curlset);
        $this->_logs(array("customContractCallback ：",'params'=>$tyunparams,'res'=>$res));
        $json_data = json_decode($res, true);
        if ($json_data['code']!='200') {
            return array('success' => false,'msg' => $json_data['msg']);
        }
        return array('success'=>true,'msg'=>'','data'=>$json_data['data']);
    }

    public function getAgentLevel($agent){
        $post_data=array();
        $time = time() . '123';
        $sault = $time . $this->sault;
        $token = md5($sault);
        $curlset = array(CURLOPT_HTTPHEADER => array(
            "Content-Type:application/json",
            "S-Request-Token:" . $token,
            "S-Request-Time:" . $time));
        $this->_logs(array("getAgentLevel ：请求参数", $post_data));
        $res = $this->https_requestTweb($this->getAgentLevel."?agentId=".$agent, $post_data, $curlset);
        $json_data = json_decode($res, true);
        if ($json_data['code'] != 200) {
            return array();
        }
        $openProducts = $json_data['data']['openProducts'];
        if(count($openProducts)<1){
            return array();
        }
        $canSaleProductId=array();
        foreach ($openProducts as $openProduct){
            $canSaleProductId[] = $openProduct['packageId'];
        }
        return $canSaleProductId;
    }


    public function contractAfterNotifyWk($tid,$phone,$contract_url){
        $timestamp=time().'123';
        $req_token=md5(md5($this->wkSault).$timestamp);
        $tyunparams=array(
            'req_token'=>$req_token,
            'timestamp'=>$timestamp,
            'phone'=>$phone,
            'tid'=>$tid,
            'contract_url'=>$contract_url,
        );
        $postData=json_encode($tyunparams);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json"));
        $res = $this->https_requestTweb($this->customContractAddCallback, $postData,$curlset);
        $this->_logs(array("customContractAddCallback ：",'params'=>$tyunparams,'res'=>$res));
        $json_data = json_decode($res, true);
        if ($json_data['code']!='200') {
            return array('success' => false,'msg' => $json_data['msg']);
        }
        return array('success'=>true,'msg'=>'','data'=>$json_data['data']);
    }

    public function changeSmowner($recordId){
        $recordModel=ServiceContracts_Record_Model::getInstanceById($recordId,'ServiceContracts');
        global $current_user,$adb;
        $adb->pquery("update vtiger_crmentity set smownerid=? where crmid=?",array($current_user->id,$recordId));
        $array = array(
            'assigned_user_id'=>array(
                'oldValue'=>$recordModel->get("assigned_user_id"),
                'currentValue'=>$current_user->id
            )
        );
        $this->setModTracker('ServiceContracts',$recordId,$array);

        $query='SELECT last_name,email1,vtiger_departments.departmentname FROM vtiger_users
                LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id
                LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid
                WHERE id=?';
        $userResult1 = $adb->pquery($query,array($recordModel->get("assigned_user_id")));
        $userResult2 = $adb->pquery($query,array($current_user->id));
        $content='合同编号：'.$recordModel->get('contract_no').'<br>原领取人：'.$userResult1->fields['last_name'].'【'.$userResult1->fields['departmentname'].'】'.'<br>新领取人：'.$userResult2->fields['last_name'].'【'.$userResult2->fields['departmentname'].'】'.'<br>变更时间：'.date('Y-m-d H:i');
        $recordModel = new Vtiger_Record_Model();
        $recordModel->sendWechatMessage(array('email'=>$userResult1->fields['email1'].'|'.$userResult2->fields['email1'],'description'=>$content,'dataurl'=>'#','title'=>'【合同变更提醒】','flag'=>7));
    }

    public function contractList(Vtiger_Request $request){
        $accountId = $request->get("accountId");
        $db = PearDatabase::getInstance();
        $sql = "select a.*,IFNULL((SELECT sum(IFNULL(unit_price,0)) FROM vtiger_receivedpayments WHERE vtiger_receivedpayments.deleted=0 AND vtiger_receivedpayments.receivedstatus='normal' AND vtiger_receivedpayments.relatetoid=a.servicecontractsid),0) AS unit_price from vtiger_servicecontracts a 
  left join vtiger_crmentity b on a.servicecontractsid=b.crmid
where b.deleted=0 and a.sc_related_to=? ";
        if(!$request->get("isAll")){
            $sql .= " and a.modulestatus='c_complete'";
        }else{
            $sql .= " and a.modulestatus!='c_cancel'";
        }
        $sql .= " order by a.signfor_date desc";

        $pageSize=$request->get("pageSize");
        $pageNum=$request->get("pageNum");
        if($pageSize){
            $sql .= " limit ".$pageNum*$pageSize.",".$pageSize;
        }

        $result = $db->pquery($sql,array($accountId));
        if(!$db->num_rows($result)){
            return array("success"=>false,'code'=>500,'msg'=>'该客户无可用合同');
        }
        $lng = translateLng("ServiceContracts");
        $data=array();
        while ($row=$db->fetchByAssoc($result)){
            $result2 = $db->pquery("select thepackage,productname,productcomboid from vtiger_salesorderproductsrel where servicecontractsid=?",array($row['servicecontractsid']));
            $productnames='';
            $packageNames=array();
            if($db->num_rows($result2)){
                while ($row2=$db->fetchByAssoc($result2)){
                    if($row2['thepackage']=='--'){
                        $productnames .=$row2['productname'].' ';
                    }else{
                        $packageNames[]=$row2['thepackage'];
                    }
                }
            }
            $packageNameStr='';
            if(count($packageNames)){
                $packageNames = array_filter($packageNames);
                foreach ($packageNames as $packageName){
                    $packageNameStr .=$packageName.' ';
                }
            }
            $allProduct = $packageNameStr.$productnames;

            $remainTotal=$row['total']-$row['unit_price'];
            $data[] = array(
                'contractId'=>$row['servicecontractsid'],
                'serviceContractsType'=>$row['servicecontractstype'],
                'serviceContractsTypeLng'=>$lng[$row['servicecontractstype']]?$lng[$row['servicecontractstype']]:$row['servicecontractstype'],
                'contractNo'=>$row['contract_no'],
                'productNames'=>$allProduct,
                'contractType'=>$row['contract_type'],
                'total'=>$row['total'],
                'remainTotal'=>$remainTotal>0?$remainTotal:0,
                'signDate'=>$row['signdate'],
                'expireDate'=>$row['actualeffectivetime'],
                'signForDate'=>$row['signfor_date'],
                'moduleStatus'=>$row['modulestatus'],
                'moduleStatusLng'=>$lng[$row['modulestatus']],
            );
        }
        return array("success"=>true,'code'=>200,'msg'=>'获取成功','contractList'=>$data);
    }

    public function contractFileList(Vtiger_Request $request){
        $db=PearDatabase::getInstance();
        $contractId=$request->get("contractId");
        $sql = "select * from vtiger_files where relationid=? and description='ServiceContracts' and delflag=0";
        $sql .= " order by uploadtime desc ";

        $pageSize=$request->get("pageSize");
        $pageNum=$request->get("pageNum");
        if($pageSize){
            $sql .= " limit ".$pageNum*$request->get("pageSize").",".$request->get("pageSize");
        }

        $result = $db->pquery($sql,array($contractId));
        if(!$db->num_rows($result)){
            return array("success"=>false,'code'=>500,'msg'=>'没有可用附件');
        }
        global $site_URL;
        $data=array();
        $lng = translateLng("Files");
        while ($row=$db->fetchByAssoc($result)){
            $data[]=array(
                "fileId"=>$row['attachmentsid'],
                "fileName"=>$row['name'],
                "type"=>$lng[$row['style']],
                "uploadTime"=>$row['uploadtime'],
                "url"=>$site_URL.'/pdfread.php?fileid='.base64_encode($row['attachmentsid']),
            );
        }
        return array("success"=>true,'code'=>200,'msg'=>'获取成功','contractFileList'=>$data);
    }

    public function confirmPay(Vtiger_Request $request){
        $userid=$request->get("userid");
        $recordId=$request->get("record");
        $mobile=$request->get("mobile");
        $money=$request->get("money");
        global $current_user;
        $user =  new Users();
        $current_user->retrieveCurrentUserInfoFromFile($userid);
        if($mobile){
            $returnData = $this->confirmPayment($userid,$recordId,$mobile);
        }else{
            foreach ($recordId as $id){
                $data[] = $this->payAfterMatch($id,$money,false);
            }
            $returnData=array("success"=>true,'returndata'=>$data);
        }
        return $returnData;
    }
}
