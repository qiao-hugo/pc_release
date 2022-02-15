<?php
/*+********
 *客户信息管理
 **********/

class ActivationCode_Record_Model extends Vtiger_Record_Model {
//    //CRM新API
//    //====线上地址========================================================
//    //升级订单创建
//    //private $tyun_buy_url = "http://tyunapi.71360.com/api/CRM/UpdateSecretkey";
//    //private $tyun_upgrade_url = "http://tyunapi.71360.com/api/CRM/UpdateSecretkeyUpgrade";
//    //private $tyun_renew_url = "http://tyunapi.71360.com/api/CRM/UpdateSecretkeyRenewal";
//    //private $tyun_againbuy_url = "http://tyunapi.71360.com/api/CRM/UpdateSecretkeyBuyService";
//    //private $tyun_serviceitem_url = "http://tyunapi.71360.com/api/CRM/GetServiceItem";
//    //private $tyun_upgrade_product_url = "http://tyunagent.71360.com/Base.xml";
//
//    //====预发布地址========================================================
//    //升级订单创建
//    private $tyun_buy_url = "http://apityun.71360.com/api/CRM/UpdateSecretkey";
//    private $tyun_upgrade_url = "http://apityun.71360.com/api/CRM/UpdateSecretkeyUpgrade";
//    private $tyun_renew_url = "http://apityun.71360.com/api/CRM/UpdateSecretkeyRenewal";
//    private $tyun_againbuy_url = "http://apityun.71360.com/api/CRM/UpdateSecretkeyBuyService";
//    private $tyun_serviceitem_url = "http://apityun.71360.com/api/CRM/GetServiceItem";
//    private $tyun_upgrade_product_url = "http://tyunagent.71360.com/Base.xml";
//    //===测试地址=========================================================
//    //192.168.40.118:8630
//    //private $tyun_buy_url = "http://192.168.40.118:8630/api/CRM/UpdateSecretkey";
//    //private $tyun_upgrade_url = "http://192.168.40.118:8630/api/CRM/UpdateSecretkeyUpgrade";
//    //private $tyun_renew_url = "http://192.168.40.118:8630/api/CRM/UpdateSecretkeyRenewal";
//    //private $tyun_againbuy_url = "http://192.168.40.118:8630/api/CRM/UpdateSecretkeyBuyService";
//    //private $tyun_serviceitem_url = "http://tyunapi.arvin.com/api/CRM/GetServiceItem";
    private $tyun_buy_url;
    private $tyun_upgrade_url;
    private $tyun_renew_url;
    private $tyun_againbuy_url;
    private $tyun_serviceitem_url;
    private $tyun_upgrade_product_url;
    private $tyun_degrade_url;
    function __construct($values = array())
    {
        parent::__construct($values);
        global $activation_code_tyun_buy_url, $activation_code_tyun_upgrade_url, $activation_code_tyun_renew_url,
               $activation_code_tyun_againbuy_url, $activation_code_tyun_serviceitem_url, $activation_code_tyun_upgrade_product_url,
               $activation_code_tyun_degrade_url;
        $this->tyun_buy_url = $activation_code_tyun_buy_url;
        $this->tyun_upgrade_url = $activation_code_tyun_upgrade_url;
        $this->tyun_renew_url = $activation_code_tyun_renew_url;
        $this->tyun_againbuy_url = $activation_code_tyun_againbuy_url;
        $this->tyun_serviceitem_url = $activation_code_tyun_serviceitem_url;
        $this->tyun_upgrade_product_url = $activation_code_tyun_upgrade_product_url;
        $this->tyun_degrade_url = $activation_code_tyun_degrade_url;
    }
    //保存T接口返回数据
    public function saveTyunResposeData($arr_data){
        global $adb;
        $this->_logs(array("保存T接口返回数据：", $arr_data));
        $adb->pquery("INSERT INTO vtiger_activationcode_tyunres(contractno,classtype,tyunurl,crminput,tyunoutput,success,createdtime)VALUES(?,?,?,?,?,?,NOW())",
            array($arr_data["contractno"],$arr_data["classtype"],$arr_data["tyunurl"],$arr_data["crminput"],$arr_data["tyunoutput"],$arr_data["success"]));
    }

    //获取T云升级/降级产品
    public function searchTyunUpgradeProduct(Vtiger_Request $request)
    {
        global $adb;
        $parm_productid = trim($request->get('p_productid'));
        $is_getname = trim($request->get('is_getname'));
        //是否降级
        $is_degrade = trim($request->get('is_degrade'));
        $contentxml = simplexml_load_file($this->tyun_upgrade_product_url);
        $tyun_products = (array)$contentxml->Client_Products;
        $tyun_products = $tyun_products["Product"];

        $products_count = count($tyun_products);
        $arr_product = array();

        for($i=0;$i<$products_count;$i++){
            $arr_product[$i] = $contentxml->Client_Products->Product[$i];
        }

        $arr_new_product = array();
        $arr_c_productid = array();
        foreach($arr_product as $k=>$v){
            $p_productId = (array)$v["ID"];
            $p_productId = $p_productId[0];
            //$p_productName = (array)$v["Name"];
            //$p_productName = $p_productName[0];
            //$p_version = (array)$v["Version"];
            //$p_version = $p_version[0];

            if($p_productId == $parm_productid) {
                if($is_degrade == '1'){
                    $xml_c_product = (array)$v->DegradeVersions;
                    $xml_c_product = (array)$xml_c_product["DegradeVersion"];
                }else{
                    $xml_c_product = (array)$v->UpgradeVersions;
                    $xml_c_product = (array)$xml_c_product["UpgradeVersion"];
                }

                foreach ($xml_c_product as $k1 => $v1) {
                    $c_productid = (array)$v1["ID"];
                    $c_productid = $c_productid[0];

                    //$c_productName = (array)$v1["Name"];
                    //$c_productName = $c_productName[0];
                    //$arr_c_productid[] = array("c_id"=>$c_productid,"c_name"=>$c_productName);

                    if($is_getname == '1'){
                        $query_sql = "SELECT M.productname,M.unit_price,
                                    IFNULL((SELECT N.unit_price FROM vtiger_products N WHERE N.parentid=M.productid LIMIT 1),0) AS renew_price
                                     FROM vtiger_products M WHERE M.tyunproductid=?";
                        $result_data = $adb->pquery($query_sql,array($c_productid));
                        $dataRow = $adb->query_result_rowdata($result_data,0);
                        $productname = 'T云版本';
                        $unit_price='0';
                        $renew_price='0';
                        if(!empty($dataRow)){
                            $productname = $dataRow['productname'];
                            $unit_price = $dataRow['unit_price'];
                            $renew_price = $dataRow['renew_price'];
                        }
                        $arr_c_productid[] = array("productid"=>$c_productid,'productname'=>$productname,'unit_price'=>$unit_price,'renew_price'=>$renew_price);
                    }else{
                        $arr_c_productid[] = $c_productid;
                    }
                }
                //$arr_new_product[] = array("p_id"=>$p_productId,"p_name"=>$p_productName,"p_version"=>$p_version,"c_ids"=>$arr_c_productid);
            }
        }
        return $arr_c_productid;
    }

    /**
     * CRM新API-查询另购服务
     */
    public function getTyunServiceItem(Vtiger_Request $request){
        $nowTime = date("Y-m-d H:i:s", time());
        $tyunData['time'] = $nowTime;

        $this->_logs(array("data加密前数据：", $tyunData));
        $this->_logs(array("data加密前数据(json)：", json_encode($tyunData)));
        $tempData['data'] = $this->encrypt(json_encode($tyunData));
        $postData = http_build_query($tempData);//传参数
        $res = $this->https_request($this->tyun_serviceitem_url, $postData);
        $result = json_decode($res, true);
        $arr_buyservice = json_decode($result, true);
        $ret_buyservice = array();

        if($arr_buyservice["success"]){
            $arr_buyservice = $arr_buyservice["data"];
            /*for($i=0;$i<count($arr_buyservice);$i++){
                $serviceID = $arr_buyservice[$i]["ServiceID"];
                if($serviceID == "81920795-7557-4789-9bb5-326357b5dfb1"){
                    //T云再营销广告
                    //T云再营销广告（1000元/4万次）
                    $ret_buyservice[] = array("ServiceID"=>$serviceID."|01","ServiceName"=>"T云再营销广告（1000元/4万次）","Unit"=>$arr_buyservice[$i]["Unit"],"Multiple"=>$arr_buyservice[$i]["Multiple"]);
                    //T云再营销广告（2000元/8万次）
                    $ret_buyservice[] = array("ServiceID"=>$serviceID."|02","ServiceName"=>"T云再营销广告（2000元/8万次）","Unit"=>$arr_buyservice[$i]["Unit"],"Multiple"=>$arr_buyservice[$i]["Multiple"]);
                    //T云再营销广告（5000元/20万次）
                    $ret_buyservice[] = array("ServiceID"=>$serviceID."|03","ServiceName"=>"T云再营销广告（5000元/20万次）","Unit"=>$arr_buyservice[$i]["Unit"],"Multiple"=>$arr_buyservice[$i]["Multiple"]);
                }else if($serviceID == "1e9c758a-2d65-44f1-98af-ff741a39601a"){
                    //T云智能SEO资源
                    //T云智能SEO资源（2000元/2000个资源）
                    $ret_buyservice[] = array("ServiceID"=>$serviceID."|01","ServiceName"=>"T云智能SEO资源（2000元/2000个资源）","Unit"=>$arr_buyservice[$i]["Unit"],"Multiple"=>$arr_buyservice[$i]["Multiple"]);
                    //T云智能SEO资源（5000元/5000个资源）
                    $ret_buyservice[] = array("ServiceID"=>$serviceID."|02","ServiceName"=>"T云智能SEO资源（5000元/5000个资源）","Unit"=>$arr_buyservice[$i]["Unit"],"Multiple"=>$arr_buyservice[$i]["Multiple"]);
                }else if($serviceID == "3d9cd6d3-a991-40b0-a170-2ef9de9b69a6"){
                    //T云地图标注
                    //T云百度地图标注
                    $ret_buyservice[] = array("ServiceID"=>$serviceID."|01","ServiceName"=>"T云百度地图标注","Unit"=>$arr_buyservice[$i]["Unit"],"Multiple"=>$arr_buyservice[$i]["Multiple"]);
                    //T云腾讯地图标注
                    $ret_buyservice[] = array("ServiceID"=>$serviceID."|02","ServiceName"=>"T云腾讯地图标注","Unit"=>$arr_buyservice[$i]["Unit"],"Multiple"=>$arr_buyservice[$i]["Multiple"]);
                    //T云高德地图标注
                    $ret_buyservice[] = array("ServiceID"=>$serviceID."|03","ServiceName"=>"T云高德地图标注","Unit"=>$arr_buyservice[$i]["Unit"],"Multiple"=>$arr_buyservice[$i]["Multiple"]);
                    //T云360地图标注
                    $ret_buyservice[] = array("ServiceID"=>$serviceID."|04","ServiceName"=>"T云360地图标注","Unit"=>$arr_buyservice[$i]["Unit"],"Multiple"=>$arr_buyservice[$i]["Multiple"]);
                    //T云搜狗地图标注
                    $ret_buyservice[] = array("ServiceID"=>$serviceID."|05","ServiceName"=>"T云搜狗地图标注","Unit"=>$arr_buyservice[$i]["Unit"],"Multiple"=>$arr_buyservice[$i]["Multiple"]);
                }else if($serviceID == "ba8207c4-4f76-4d06-834a-88f6f3d03727"){
                    //T云客服电话
                    //T云360客服电话认证
                    $ret_buyservice[] = array("ServiceID"=>$serviceID."|01","ServiceName"=>"T云360客服电话认证","Unit"=>$arr_buyservice[$i]["Unit"],"Multiple"=>$arr_buyservice[$i]["Multiple"]);
                    //T云搜狗客服电话认证
                    $ret_buyservice[] = array("ServiceID"=>$serviceID."|02","ServiceName"=>"T云搜狗客服电话认证","Unit"=>$arr_buyservice[$i]["Unit"],"Multiple"=>$arr_buyservice[$i]["Multiple"]);
                }else{
                    $ret_buyservice[] = $arr_buyservice[$i];
                }
            }*/
            global $adb;
            for($i=0;$i<count($arr_buyservice);$i++) {
                $serviceID = $arr_buyservice[$i]["ServiceID"];
                $multiple = $arr_buyservice[$i]["Multiple"];
                //$serviceName = $arr_buyservice[$i]["ServiceName"];
                //$unit = $arr_buyservice[$i]["Unit"];
                if(bccomp($multiple,1)>0){
                    //$serviceName .= '('.$multiple.$unit.')';
                    //$arr_buyservice[$i]["ServiceName"] = $serviceName;
                    $arr_buyservice[$i]["Unit"] = '组';
                }
                $query_sql = "SELECT productname FROM vtiger_products WHERE tyunproductid=?";
                $result_data = $adb->pquery($query_sql,array($serviceID));
                $dataRow = $adb->query_result_rowdata($result_data,0);
                if($dataRow){
                    $arr_buyservice[$i]["ServiceName"] = $dataRow['productname'];
                }
            }
        }else{
            $arr_buyservice[] = array();
        }
        return $arr_buyservice;
    }

    /**
     * 获取产品列表
     * @param $recordId
     * @throws Exception
     */
   public function searchProductList($recordId){
       global $adb;
       $arr_products = array();
       $arr_buyservice = array();

       $query_sql = "SELECT M.activationcodeid,M.activecode AS m_activecode,
                            M.classtype AS m_classtype,
                            M.contractname AS m_contractno,
                            M.customerid AS m_customerid,
                            M.customername AS m_customername,
                            M.productid AS m_productid,
                            M.buyserviceinfo AS m_buyserviceinfo,
                            P.contractname AS p_contractname,
                            P.customerid AS p_customerid,
                            P.customername AS p_customername,
                            P.productid AS p_productid
                    FROM vtiger_activationcode M
                    LEFT JOIN vtiger_activationcode P ON(M.buyid=P.activationcodeid) WHERE M.status IN(0,1) AND M.activationcodeid=?";
       $result = $adb->pquery($query_sql,array($recordId));
       $data = $adb->query_result_rowdata($result);

       if($data){
           $query_sql = "SELECT vtiger_products.tyunproductid AS productid,vtiger_products.productname FROM vtiger_products 
                        INNER JOIN vtiger_crmentity ON (vtiger_products.productid=vtiger_crmentity.crmid)
                        WHERE vtiger_crmentity.deleted=0 AND vtiger_products.istyun=1 AND vtiger_products.customer=0  AND LENGTH(vtiger_products.tyunproductid)>0";

            $classtype = $data['m_classtype'];

            if ($classtype == 'upgrade') {
                $request = new Vtiger_Request();
                $request->set("p_productid", $data['p_productid']);

                $arr_tyun_product = $this->searchTyunUpgradeProduct($request);

                $query_sql .= " AND FIND_IN_SET(vtiger_products.tyunproductid,?)";
                $result_data = $adb->pquery($query_sql, array(implode(",", $arr_tyun_product)));
                while ($rawData = $adb->fetch_array($result_data)) {
                    $arr_products[] = $rawData;
                }
            }else if ($classtype == 'degrade'){
                $request = new Vtiger_Request();
                $request->set("p_productid",$data['p_productid']);
                $request->set("is_degrade",'1');

                $arr_tyun_product = $this->searchTyunUpgradeProduct($request);

                $query_sql .=" AND FIND_IN_SET(vtiger_products.tyunproductid,?)";
                $result_data = $adb->pquery($query_sql,array(implode(",",$arr_tyun_product)));
                while($rawData=$adb->fetch_array($result_data)) {
                    $arr_products[]=$rawData;
                }
            }else if ($classtype == 'renew' || $classtype == 'againbuy'){
                $this->_logs(array("获取id：", $recordId));
                $old_data = $this->getOldActivationCodeInfo($recordId);
                $query_sql .=" AND vtiger_products.tyunproductid=?";
                $this->_logs(array("获取T云原产品：", json_encode($old_data)));
                $result_data = $adb->pquery($query_sql,array($old_data['productid']));
                while($rawData=$adb->fetch_array($result_data)) {
                    $arr_products[]=$rawData;
                }
            }else{
                //首购
                $query_sql .=" AND FIND_IN_SET(vtiger_products.productid,(SELECT REPLACE(relproductid,' |##| ',',') FROM vtiger_contractsproductsrel WHERE contract_type=9))";
                $result_data = $adb->pquery($query_sql,array());
                while($rawData=$adb->fetch_array($result_data)) {
                    $arr_products[]=$rawData;
                }
            }

            //另购服务
           $tyunAllServiceItem = $this->getTyunServiceItem(new Vtiger_Request());
           $buyserviceinfo = $data['m_buyserviceinfo'];
           if(!empty($buyserviceinfo)){
               $buyserviceinfo = htmlspecialchars_decode($buyserviceinfo);
               $arr_buyserviceinfo = json_decode($buyserviceinfo,true);
               $serviceContent = "";
               for($a=0;$a<count($arr_buyserviceinfo);$a++){
                   $buyCount = $arr_buyserviceinfo[$a]['BuyCount'];
                   $serviceID = $arr_buyserviceinfo[$a]['ServiceID'];

                   /*$info=$adb->pquery('select productname from vtiger_products where tyunproductid=? limit 1',array($serviceID));
                   $data=$adb->query_result_rowdata($info);
                   $buyservice =empty($data['productname'])?'--':$data['productname'];*/
                   for($b=0;$b<count($tyunAllServiceItem);$b++){
                       $serviceID2 = $tyunAllServiceItem[$b]['ServiceID'];
                       $multiple = $tyunAllServiceItem[$b]["Multiple"];
                       $unit = $tyunAllServiceItem[$b]["Unit"];
                       if($serviceID == $serviceID2){
                           $arr_buyservice[] = array("ServiceID"=>$serviceID,"BuyCount"=>$buyCount,"Multiple"=>$multiple,"Unit"=>$unit);
                           break;
                       }
                   }
               }
           }

       }
       return array("tyun_all_buy_service"=>$tyunAllServiceItem,"tyun_products"=>$arr_products,"tyun_buy_service"=>$arr_buyservice);;
   }

    public function checkBuyInput(Vtiger_Request $request){
        $contractname = $request->get('contractname');
        $customername = $request->get('customername');
        $classtype = $request->get('classtype');
        $usercode = $request->get('usercode');
        $flag = true;
        $message='';
        //判断T云账号是否为空
        if($classtype != 'buy'){
            if(empty($usercode)){
                return array('success'=>false, 'message'=>"T云账号不能为空,请确认");
            }
        }

        global $adb;
        $sql = 'SELECT servicecontractsid,modulestatus FROM vtiger_servicecontracts INNER JOIN vtiger_crmentity ON(vtiger_servicecontracts.servicecontractsid=vtiger_crmentity.crmid) WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.contract_no=?';
        $sel_result = $adb->pquery($sql, array($contractname));
        $res_cnt = $adb->num_rows($sel_result);
        if ($res_cnt > 0) {

            $datarow = $adb->query_result_rowdata($sel_result);
            if($datarow['modulestatus'] == 'c_complete'){
                $flag = false;
                $message="已签收合同不能修改,请确认";
            }
            $request->set('contractid',$datarow['servicecontractsid']);
        }else{
            $flag = false;
            $message="合同编号不存在,请确认输入是否正确";
        }

        if($flag && $classtype=='buy'){
            $sql = 'SELECT vtiger_account.accountid FROM vtiger_account INNER JOIN vtiger_crmentity ON(vtiger_account.accountid=vtiger_crmentity.crmid) WHERE vtiger_crmentity.deleted=0 AND vtiger_account.accountname=?';
            $sel_result = $adb->pquery($sql, array($customername));
            $res_cnt = $adb->num_rows($sel_result);
            if ($res_cnt > 0) {
                $flag = true;
                //设置客户id
                $data_row = $adb->query_result_rowdata($sel_result,0);
                $request->set('customerid',$data_row['accountid']);
            }else{
                $flag = false;
                $message="客户名称不存在,请确认输入是否正确";
            }
        }

        //判断T云账号是否存在(账号变更时处理)
        $oldusercode = $request->get('oldusercode');
        if($oldusercode != $usercode){
            $sql = "SELECT activationcodeid,(SELECT MM.productid FROM vtiger_activationcode MM  WHERE MM.status IN(0,1) AND MM.classtype IN('buy','upgrade','degrade') AND MM.usercode=? ORDER BY MM.receivetime DESC LIMIT 1) AS productid
                FROM vtiger_activationcode WHERE status IN(0,1) AND usercode=? ORDER BY receivetime DESC LIMIT 1";
            $sel_result = $adb->pquery($sql, array($usercode,$usercode));
            $res_cnt = $adb->num_rows($sel_result);
            if ($res_cnt > 0) {
                $data_row = $adb->query_result_rowdata($sel_result,0);
                $productid = $data_row['productid'];
                $is_tyun_seo = $request->get('is_tyun_seo');
                if($is_tyun_seo == 1){
                    if($productid != 'fb016866-4296-11e6-ad98-00155d069461' && $productid != 'eb472d25-f1b1-11e6-a335-5254003c6d38'){
                        $flag = false;
                        $message="选择了【智能SEO】时,只能选择V3或V3P版本";
                        return array('success'=>$flag, 'message'=>$message);
                    }
                    $dataR = $this->getOldProductInfo($data_row['activationcodeid'],$classtype);
                    $receivetimeflag = $dataR['receivetimeflag'];
                    if($receivetimeflag == '0'){
                        $flag = false;
                        $message="选择了【智能SEO】时,领取激活码时间必须要大于2017年11月4日";
                        return array('success'=>$flag, 'message'=>$message);
                    }
                }

                //验证版本是否一致
                $old_productid = $request->get('productid');
                if($old_productid != $productid){
                    $flag = false;
                    $message="T云账号【".$usercode."】对应的版本和当前版本不一致,不能变更";
                    return array('success'=>$flag, 'message'=>$message);
                }
            }else{
                $flag = false;
                $message="T云账号【".$usercode."】不存在,请确认";
            }
        }
        return array('success'=>$flag, 'message'=>$message);
    }

    public function checkBuyServiceChange(Vtiger_Request $request,$nextFlag=true){
        $recordId = $request->get('record');
        $classtype = $request->get('classtype');
        global $adb;
        //验证 合同编号、客户名称、产品ID、购买年限、客户手机、另购服务对象 是否有变化
        //获取原激活数据
        $old_data = $this->getOldActivationCodeInfo($recordId);
        if(!$old_data || count($old_data) == 0){
            return array('success'=>false, 'msg'=>'没有查询到对应的购买数据，请确认');
        }

        if($classtype == 'buy'){
            $sql = 'SELECT M.*  FROM vtiger_activationcode M WHERE M.status IN(0,1) AND M.activationcodeid=?';
        }else{
            $sql = "SELECT 
                M.activationcodeid,
                M.contractid,
                M.contractname,
                M.productlife,
                M.buyid,
                M.buyserviceinfo,
                P.activecode,
                IFNULL(M.mobile,P.mobile) AS mobile,
                IFNULL(M.customerid,P.customerid) AS customerid,
                IFNULL(M.customername,P.customername) AS customername,
                DATE_FORMAT(IFNULL(M.receivetime,NOW()),'%Y-%m-%d') AS adddate,
                IFNULL(M.usercode,P.usercode) AS usercode
                FROM vtiger_activationcode M
                LEFT JOIN vtiger_activationcode P ON(M.buyid=P.activationcodeid)
                WHERE M.status IN(0,1) AND M.activationcodeid=?";
        }

        $sel_result = $adb->pquery($sql, array($recordId));
        $res_cnt = $adb->num_rows($sel_result);

        if ($res_cnt > 0) {
            $data_row = $adb->query_result_rowdata($sel_result,0);

            //激活码
            $activecode = $request->get('activecode');
            if($activecode != $old_data['activecode']){
                return array('success'=>true,'changeFlag'=>true, 'parentFlag'=>true,'activecodeFlag'=>true);
            }

            //登录账号
            $usercode = $request->get('usercode');
            if(!empty($usercode) && $usercode != $data_row['usercode']){
                //登录账号和激活码绑定
                return array('success'=>true, 'changeFlag'=>true, 'parentFlag'=>true,'activecodeFlag'=>true);
            }

            //合同编号
            $contractname = $request->get('contractname');
            if($contractname != $data_row['contractname']){
                return array('success'=>true, 'changeFlag'=>true, 'parentFlag'=>false,'activecodeFlag'=>false);
            }

            //客户名称
            $customername = $request->get('customername');
            if($customername != $data_row['customername']){
                return array('success'=>true, 'changeFlag'=>true, 'parentFlag'=>true,'activecodeFlag'=>false);
            }

            //产品ID
            $productid = $request->get('productid');
            if($productid != $old_data['productid']){
                return array('success'=>true, 'changeFlag'=>true, 'parentFlag'=>false,'activecodeFlag'=>false);
            }

            //购买年限
            $productlife = $request->get('productlife');
            if($productlife != $data_row['productlife']){
                return array('success'=>true, 'changeFlag'=>true, 'parentFlag'=>false,'activecodeFlag'=>false);
            }
            //客户手机
            $mobile = $request->get('mobile');
            if($mobile != $data_row['mobile']){
                return array('success'=>true, 'changeFlag'=>true, 'parentFlag'=>true,'activecodeFlag'=>false);
            }

            //下单时间
            $adddate = $request->get('adddate');
            $adddate = date_create($adddate);
            $adddate = date_format($adddate,"Y/m/d");

            $old_adddate = $data_row['adddate'];
            $old_adddate = date_create($old_adddate);
            $old_adddate = date_format($old_adddate,"Y/m/d");
            if($adddate != $old_adddate){
                return array('success'=>true, 'changeFlag'=>true, 'parentFlag'=>true,'activecodeFlag'=>false);
            }

            //if(!$nextFlag) return array('changeFlag'=>false, 'parentFlag'=>false,'activecodeFlag'=>false);

            //另购服务对象
            //新另购服务
            $arr_serviceinfo_new = array();
            if(!empty($_POST['buyindex'])){
                foreach($_POST['buyindex'] as $key=>$value){
                    $serviceID = $_POST['ServiceID'][$value];
                    if(empty($serviceID) || $serviceID == '0'){
                        continue;
                    }
                    $arr_serviceinfo_new[]=array("ServiceID"=>$serviceID,"BuyCount"=>$_POST['BuyCount'][$value]);
                }
            }

            //原另购服务
            $arr_serviceinfo_old = array();
            $buyserviceinfo = $data_row['buyserviceinfo'];
            if(!empty($buyserviceinfo)){
                $buyserviceinfo = htmlspecialchars_decode($buyserviceinfo);
                $arr_buyserviceinfo = json_decode($buyserviceinfo,true);
                for($a=0;$a<count($arr_buyserviceinfo);$a++){
                    $buyCount = $arr_buyserviceinfo[$a]['BuyCount'];
                    $serviceID = $arr_buyserviceinfo[$a]['ServiceID'];
                    $arr_serviceinfo_old[] = array("ServiceID"=>$serviceID,"BuyCount"=>$buyCount);
                }
            }

            //个数不等
            if(count($arr_serviceinfo_new) != count($arr_serviceinfo_old)){
                return array('success'=>true, 'changeFlag'=>true, 'parentFlag'=>false,'activecodeFlag'=>false);
            }
            for($a=0;$a<count($arr_serviceinfo_old);$a++){
                $serviceID_old = $arr_serviceinfo_old[$a]['ServiceID'];
                $buyCount_old = $arr_serviceinfo_old[$a]['BuyCount'];
                $check1 = false;
                $check2 = false;
                for($b=0;$b<count($arr_serviceinfo_new);$b++){
                    $serviceID_new = $arr_serviceinfo_new[$b]['ServiceID'];
                    $buyCount_new = $arr_serviceinfo_new[$b]['BuyCount'];
                    if($serviceID_old == $serviceID_new){
                        $check1 = true;
                        if($buyCount_old == $buyCount_new){
                            $check2 = true;
                        }
                        break;
                    }
                }

                if(!$check1 || !$check2){
                    return array('success'=>true, 'changeFlag'=>true, 'parentFlag'=>false,'activecodeFlag'=>false);
                }

            }

        }else{
            return array('success'=>false, 'changeFlag'=>false, 'parentFlag'=>false,'activecodeFlag'=>false,'msg'=>'购买数据不存在');
        }
        return array('success'=>false, 'changeFlag'=>false, 'parentFlag'=>false,'activecodeFlag'=>false,'msg'=>'您没有做任何修改');
    }

    /**
     * 调用t云服务购买变更接口
     * @param Vtiger_Request $request
     */
    public function updateTyunBuyService(Vtiger_Request $request){
        global $adb;
        $tyunData = array();
        $url = '';
        $recordId = $request->get('record');
        $classtype = $request->get('classtype');

        //获取原激活数据
        $old_data = $this->getOldActivationCodeInfo($recordId);
        if(!$old_data || count($old_data) == 0){
            return array('success'=>false, 'message'=>'没有查询到对应的购买数据，请确认');
        }

        //查询原合同
        $query_contract_sql = "SELECT contractname,productid,usercode FROM vtiger_activationcode WHERE status IN(0,1) AND activationcodeid=?";
        $result_contract = $adb->pquery($query_contract_sql,array($recordId));
        $data_contract = $adb->query_result_rowdata($result_contract,0);

        if($classtype == 'buy'){
                //原合同编号
                $tyunData['OldContractCode'] = $data_contract['contractname'];
                //原客户名称
                $tyunData['OldCompanyName'] = $old_data['customername'];
                //合同编号
                $tyunData['ContractCode'] = $_REQUEST['contractname'];
                //客户名称
                $tyunData['CompanyName'] = $_REQUEST['customername'];
                //产品ID
                $tyunData['ProductID'] = $_REQUEST['productid'];
                //购买年限
                $tyunData['BuyYear'] = $_REQUEST['productlife'];
                //客户手机
                $tyunData['CustPhone'] = $_REQUEST['mobile'];
                //验证码
                //$tyunData['VerificationCode'] = $_REQUEST[''];
                //代理商标识码
                $tyunData['AgentIdentity'] = $_REQUEST['agents'];
                $url = $this->tyun_buy_url;
        }else{
                //原登录名
                $tyunData['OldLoginName'] = $data_contract['usercode'];
                //原激活码
                $tyunData['OldSecretKeyID'] = $old_data['activecode'];
                //原合同编号
                $tyunData['OldContractCode'] = $data_contract['contractname'];
                //登录名
                $tyunData['LoginName'] = $_REQUEST['usercode'];
                //激活码
                $query_sql2 = "SELECT activecode FROM vtiger_activationcode WHERE status IN(0,1) AND classtype='buy' AND usercode=?";
                $result2 = $adb->pquery($query_sql2,array($_REQUEST['usercode']));
                $data2 = $adb->query_result_rowdata($result2,0);
                if(empty($data2) || empty($data2['activecode'])){
                    return array('success'=>false, 'message'=>'没有查询到【'.$_REQUEST['usercode'].'】账户的激活码，请确认');
                }
                $tyunData['SecretKeyID'] = $data2['activecode'];
                //合同编号
                $tyunData['ContractCode'] = $_REQUEST['contractname'];

                //查找原产品和激活码到期时间
                $query_sql1 = "SELECT (SELECT productid FROM vtiger_activationcode WHERE status IN(0,1) AND classtype IN('buy','upgrade','degrade') AND usercode=M.usercode ORDER BY receivetime DESC LIMIT 1) AS productid,
                              (SELECT MAX(str_to_date(REPLACE(MM.expiredate,'/','-'),'%Y-%m-%d')) FROM vtiger_activationcode MM WHERE MM.status IN(0,1) AND MM.usercode=M.usercode) AS expiredate 
                              FROM vtiger_activationcode M WHERE M.status IN(0,1) AND M.activecode=?";
                $result1 = $adb->pquery($query_sql1,array($data2['activecode']));
                $data1 = $adb->query_result_rowdata($result1);
                if(empty($data1) || empty($data1['productid'])){
                    return array('success'=>false, 'message'=>'没有查询到激活码信息，请确认');
                }
                //原到期时间(针对新激活码)
                $tyunData['OldCloseDate'] = $data1['expiredate'];

                if($classtype == 'upgrade') {
                    //原产品ID(针对新激活码)
                    $tyunData['OldProductID'] = $data1['productid'];
                    //升级产品ID
                    $tyunData['UpgradeProductID'] = $_REQUEST['productid'];
                    //升级年限
                    $tyunData['UpgradeYear'] = $_REQUEST['productlife'];

                    $url = $this->tyun_upgrade_url;
                }
                if($classtype == 'degrade') {
                    //原产品ID(针对新激活码)
                    $tyunData['OldProductID'] = $data1['productid'];
                    //降级产品ID
                    $tyunData['UpgradeProductID'] = $_REQUEST['productid'];
                    //降级年限
                    $tyunData['UpgradeYear'] = $_REQUEST['productlife'];

                    $url = $this->tyun_degrade_url;
                }
                if($classtype == 'renew') {
                    //续费年限
                    $tyunData['RenewYear'] = $_REQUEST['productlife'];
                    $url = $this->tyun_renew_url;
                }
                if($classtype == 'againbuy') {
                    $url = $this->tyun_againbuy_url;
                }
                //另购服务对象
                //$tyunData['BuyServiceinfo'] = $data['m_buyserviceinfo'];
        }

        //更新另购服务
        if(!empty($_POST['buyindex'])){
            $arr_exist_serviceid = array();
            $arr_new_serviceinfo = array();
            foreach($_POST['buyindex'] as $key=>$value){
                $serviceID = $_POST['ServiceID'][$value];
                $buycount = $_POST['BuyCount'][$value];
                $remark = $_POST['servicename_display'][$value].':'.$buycount;
                if(empty($serviceID) || $serviceID == '0'){
                    continue;
                }

                /*$tmp_serviceID1 = explode("|",$serviceID);
                if(!in_array($tmp_serviceID1[0],$arr_exist_serviceid)){
                    $arr_exist_serviceid[]=$tmp_serviceID1[0];
                    $arr_new_serviceinfo[]=array("ServiceID"=>$tmp_serviceID1[0],"BuyCount"=>$buycount,"Remark"=>$remark);
                }else{
                    for($i=0;$i<count($arr_new_serviceinfo);$i++){
                        $tmp_serviceID2 = $arr_new_serviceinfo[$i]['ServiceID'];
                        $tmp_serviceID2 = explode("|",$tmp_serviceID2);
                        $tmp_buycount2 = $arr_new_serviceinfo[$i]['BuyCount'];
                        $tmp_remark2 = $arr_new_serviceinfo[$i]['Remark'];
                        if($tmp_serviceID1[0] == $tmp_serviceID2[0]){
                            $arr_new_serviceinfo[$i]['BuyCount'] = bcadd($buycount,$tmp_buycount2);
                            //$arr_new_serviceinfo[$i]['Remark'] = $tmp_remark2.';'.$remark;
                            break;
                        }
                    }
                }*/
                $arr_serviceinfo[]=array("ServiceID"=>$serviceID,"BuyCount"=>$_POST['TyunBuyCount'][$value]);
            }
            $tyunData['BuyServiceinfo'] = json_encode($arr_serviceinfo);
        }else{
            $tyunData['BuyServiceinfo'] = json_encode(array());
        }
        //下单时间
        //$nowTime = date("Y-m-d H:i:s", time());
        $tyunData['AddDate'] = $request->get("receivetime");

        $this->_logs(array("data加密前数据：", $tyunData));
        $tempData['data'] = $this->encrypt(json_encode($tyunData));
        $this->_logs(array("data加密后数据：", $tempData['data']));
        $postData = http_build_query($tempData);//传参数
        $res = $this->https_request($url, $postData);

        $result = json_decode($res, true);
        $result = json_decode($result, true);

        //保存T云接口返回信息
        $arr_data = array(
            'contractno'=>$tyunData['ContractCode'],
            "classtype"=>$classtype,
            "tyunurl"=>$url,
            "crminput"=>json_encode($tyunData),
            "tyunoutput"=>json_encode($result),
            "success"=>$result['success']);
        $this->saveTyunResposeData($arr_data);

        return $result;
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
        $this->_logs("返回结果:".$output);
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

    public function getOldProductInfo($id,$classtype){
        global $adb;
        if($classtype == 'buy'){
            return array("receivetimeflag"=>0,"oldproductid"=>0);
        }else{
            $query_sql = "SELECT IF(IFNULL(P.receivetime,M.receivetime)>'2017-11-04',1,0) as receivetimeflag,IFNULL(P.productid,M.productid) AS oldproductid
                     FROM vtiger_activationcode M
                     LEFT JOIN vtiger_activationcode P ON(M.buyid=P.activationcodeid)
                     WHERE M.status IN(0,1) AND M.activationcodeid=?";
            $result = $adb->pquery($query_sql,array($id));
            return $adb->query_result_rowdata($result);
        }
    }

    public function getOldActivationCodeInfo($activationcodeid){
        global $adb;
        $oldActivationCodeInfo = array();

        $query_sql = "SELECT * FROM vtiger_activationcode WHERE status IN(0,1) AND activationcodeid=?";
        $result = $adb->pquery($query_sql,array($activationcodeid));
        $data_row = $adb->query_result_rowdata($result);
        $classtype = $data_row['classtype'];
        $usercode = $data_row['usercode'];
        if($classtype == 'buy'){
            return $data_row;
        }
        $query_sql = "SELECT * FROM vtiger_activationcode  WHERE status IN(0,1) AND classtype IN('buy','upgrade','degrade') AND usercode=? ORDER BY receivetime DESC LIMIT 1";
        $result1 = $adb->pquery($query_sql,array($usercode));
        $data_row1 = $adb->query_result_rowdata($result1);
        $oldActivationCodeInfo = $data_row1;
        $type = $data_row1['classtype'];
        $activationcodeid_new = $data_row1['activationcodeid'];
        if($type == 'upgrade'|| $type == 'degrade'){
            $query_sql = "SELECT 
                            M.contractid,
                            M.contractname,
                            M.productid,
                            M.buyserviceinfo,
                            P.activecode,
                            P.customername,
                            P.customerid,
                            (SELECT MAX(str_to_date(REPLACE(MM.expiredate,'/','-'),'%Y-%m-%d')) FROM vtiger_activationcode MM WHERE MM.status IN(0,1) AND MM.usercode=M.usercode) AS expiredate,
                            P.customerid,
                            P.usercode
                     FROM vtiger_activationcode M
                     LEFT JOIN vtiger_activationcode P ON(M.buyid=P.activationcodeid)
                     WHERE M.status IN(0,1) AND M.activationcodeid=?";
            $result2 = $adb->pquery($query_sql,array($activationcodeid_new));
            $data_row2 = $adb->query_result_rowdata($result2);
            $oldActivationCodeInfo = $data_row2;
        }
        return $oldActivationCodeInfo;
    }
    /**
     * 根据T云账户获取客服信息
     */
    public function getServiceUserCode($request){
        global $adb;
        $return=array();
        $pageSize=1000;
        $pageNum=$request->get('page');
        $pageNum=(is_numeric($pageNum)&&$pageNum>1)?$pageNum:0;
        $offset=$pageSize*$pageNum;
        $limit=' LIMIT '.$offset.','.$pageSize;
        $query="SELECT vtiger_activationcode.usercode,vtiger_account.accountname,vtiger_users.last_name,vtiger_users.email1,vtiger_departments.departmentname FROM vtiger_activationcode LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_activationcode.customerid LEFT JOIN vtiger_users ON (vtiger_users.id=vtiger_account.serviceid AND vtiger_users.`status`='Active') 
                LEFT JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid WHERE vtiger_activationcode.`status` in(0,1) AND classtype='buy' AND vtiger_activationcode.usercode!=''".$limit;
        $resultData=$adb->pquery($query,array());
        if($adb->num_rows($resultData)){
            while($row=$adb->fetch_array($resultData)){
                $temp=array();
                $temp['usercode']=$row['usercode'];
                $temp['accountname']=$row['accountname'];
                $temp['username']=$row['last_name'];
                $temp['email']=$row['email1'];
                $temp['department']=$row['departmentname'];
                $return[]=$temp;
            }
        }
        return $return;
    }
    /**
     * 据据71360用户的ID获取对应的合同附件
     */
    public function getFileByUserCodeId($request){
        global $adb,$root_directory;
        $usercodeid=$request->get('usercodeid');
        $query='SELECT contractid from vtiger_activationcode WHERE `status` in(0,1) AND productclass=10 AND contractid>0 AND usercodeid=?  ORDER BY activationcodeid DESC LIMIT 1';
        //$query='SELECT contractid from vtiger_activationcode WHERE `status` in(0,1)  AND contractid>0 AND usercodeid=?  ORDER BY activationcodeid DESC LIMIT 1';
        $result=$adb->pquery($query,array($usercodeid));
        if($adb->num_rows($result)){
            $contractid=$result->fields['contractid'];
            $result = $adb->pquery("SELECT attachmentsid,newfilename,name,type,path FROM vtiger_files WHERE delflag=0 AND description='ServiceContracts' AND style='files_style4' AND relationid=? ORDER BY attachmentsid DESC LIMIT 1", array($contractid));
            if($adb->num_rows($result)) {
                $fileDetails = $adb->query_result_rowdata($result);
                $filePath = $fileDetails['path'];
                if($fileDetails['newfilename']>0){
                    $fileName=$fileDetails['newfilename'];
                    $savedFile = $fileDetails['attachmentsid']."_".$fileName;
                }else{
                    $fileName = html_entity_decode($fileDetails['name'], ENT_QUOTES, vglobal('default_charset'));
                    $t_fileName = base64_encode($fileName);
                    $t_fileName = str_replace('/', '', $t_fileName);
                    $savedFile = $fileDetails['attachmentsid']."_".$t_fileName;
                    if(!file_exists($filePath.$savedFile)){
                        $savedFile = $fileDetails['attachmentsid']."_".$fileName;
                    }
                }
                if(file_exists($root_directory.$filePath.$savedFile)){
                    $fileContent=file_get_contents($root_directory.$filePath.$savedFile);
                    return array(1,array('type'=>$fileDetails['type'],'name'=>$fileDetails['name'],'filedata'=>base64_encode($fileContent)));
                }
            }
        }
        return array(0,'文件不存在');
    }
    /**
     * 据据71360用户的ID获取对应的合同附件
     */
    public function getFileByUserCodeIdNew($request){
        global $adb,$root_directory;
        $ordercode=$request->get('ordercode');
        $producttype=$request->get('producttype');
        do {
            $flag=true;
            $istyunfile=false;
            $query='SELECT contractid from vtiger_activationcode WHERE `status` in(0,1) AND productclass=10 AND contractid>0 AND ordercode=?  ORDER BY activationcodeid DESC LIMIT 1';
            $result = $adb->pquery($query, array($ordercode));
            if($adb->num_rows($result)){
                $contractid = $result->fields['contractid'];
                $flag=false;
            }
            if($flag){
                $query = 'SELECT contractid from vtiger_activationcode WHERE `status` in(0,1)  AND contractid>0 AND ordercode=?  ORDER BY activationcodeid DESC LIMIT 1';
                $result = $adb->pquery($query, array($ordercode));
                if ($adb->num_rows($result)) {
                    $contractid = $result->fields['contractid'];
                    $istyunfile=true;
                }
            }

            if($producttype==1){
                $likeproductname=" (name LIKE '%DSHZCG%'  or name like '%SLGYS%')";
            }else if($producttype==2){
                $likeproductname=" (name LIKE '%DSHSDSJ%'  or name like '%SLGYS%')";
            }else{
                //臻采购续费合同
                $likeproductname=" (name LIKE '%XFDSHZCG%'  or name like '%SLGYS%')";
            }
            if($contractid>0) {
                $flag=true;
                $result = $adb->pquery("SELECT attachmentsid,newfilename,name,type,path FROM vtiger_files WHERE delflag=0 AND description='ServiceContracts' AND style='files_style4' AND relationid=? AND ".$likeproductname." ORDER BY attachmentsid DESC LIMIT 1", array($contractid));
                if($adb->num_rows($result)) {
                    $fileDetails = $adb->query_result_rowdata($result);
                    $flag=false;
                }
                if($flag && !$istyunfile){
                    $result = $adb->pquery("SELECT attachmentsid,newfilename,name,type,path FROM vtiger_files WHERE delflag=0 AND description='ServiceContracts' AND style='files_style4' AND relationid=? ORDER BY attachmentsid DESC LIMIT 1", array($contractid));
                    if($adb->num_rows($result)){
                        $fileDetails = $adb->query_result_rowdata($result);
                        $flag=false;
                    }
                }
                if (!$flag) {
                    $filePath = $fileDetails['path'];
                    if ($fileDetails['newfilename'] > 0) {
                        $fileName = $fileDetails['newfilename'];
                        $savedFile = $fileDetails['attachmentsid'] . "_" . $fileName;
                    } else {
                        $fileName = html_entity_decode($fileDetails['name'], ENT_QUOTES, vglobal('default_charset'));
                        $t_fileName = base64_encode($fileName);
                        $t_fileName = str_replace('/', '', $t_fileName);
                        $savedFile = $fileDetails['attachmentsid'] . "_" . $t_fileName;
                        if (!file_exists($filePath . $savedFile)) {
                            $savedFile = $fileDetails['attachmentsid'] . "_" . $fileName;
                        }
                    }
                    if (file_exists($root_directory . $filePath . $savedFile)) {
                        $fileContent = file_get_contents($root_directory . $filePath . $savedFile);
                        return array(1, array('type' => $fileDetails['type'], 'name' => $fileDetails['name'], 'filedata' => base64_encode($fileContent)));
                    }
                }
            }
        }while(0);
        return array(0,'文件不存在');
    }
}
