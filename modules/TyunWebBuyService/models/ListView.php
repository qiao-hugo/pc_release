<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class TyunWebBuyService_ListView_Model extends Vtiger_ListView_Model {

    /**
     * 模块列表页面显示链接 保留新增 Edit By Joe @20150511
     * @param <Array> $linkParams
     * @return <Array> - Associate array of Link Type to List of Vtiger_Link_Model instances
     */
    public function getListViewLinks($linkParams) {
        $basicLinks = array();
        $links=array();$links['LISTVIEWBASIC'];
        return $links;

    }
	//根据参数显示数据   #移动crm模拟$request请求---2015-12-16 罗志坚
	public function getListViewEntries($pagingModel,$request=array()) {
		$db = PearDatabase::getInstance();
        $moduleName ='TyunWebBuyService';
        if(!empty($request)){
            if(isset($request['BugFreeQuery'])){
                $_REQUEST['BugFreeQuery'] = $request['BugFreeQuery'];
            }
            if(isset($request['public'])){
                $_REQUEST['public'] = $request['public'];
            }
        }
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');
        //List view will be displayed on recently created/modified records
        //列表视图将显示最近的创建修改记录  ---做什么用处
        if(empty($orderBy) && empty($sortOrder)){
            $orderBy = 'activationcodeid';
            $sortOrder = 'DESC';
        }
        $this->getSearchWhere();
        $listQuery = $this->getQuery();
        
        $listQuery.=$this->getUserWhere();
        $listQuery=str_replace(',vtiger_activationcode.activationcodeid',',vtiger_activationcode.contractid,vtiger_activationcode.activationcodeid',$listQuery);
        $listQuery.=" AND vtiger_activationcode.comeformtyun=1 AND iscollegeedition=0";
        $listQuery.=' order by '.$orderBy.' '.$sortOrder;
        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();
        $viewid = ListViewSession::getCurrentView($moduleName);
        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
        //如果是对账或者归档列表走这里  能这么写是应为 归档的数据表 vtiger_activationcode_file 是 未归档前 vtiger_tyuncontractactivacode 和vtiger_activationcode 表的合并表 所以只需要把表名替换以及left join 去掉就ok了。
        if($_REQUEST['orderType']=='reconciliation' || $_REQUEST['orderType']=='filed'){
            // ①归档列表显示
            if($_REQUEST['orderType']=='filed'){
                $listQuery = str_replace("LEFT JOIN vtiger_tyuncontractactivacode ON vtiger_activationcode.activationcodeid = vtiger_tyuncontractactivacode.activationcodeid","",$listQuery);
                $listQuery = str_replace("vtiger_activationcode","vtiger_activationcode_file",$listQuery);
                $listQuery = str_replace("vtiger_tyuncontractactivacode","vtiger_activationcode_file",$listQuery);
                $listQuery .= " LIMIT $startIndex,".($pageLimit);
                $listResult = $db->pquery($listQuery, array());
                while($rawData=$db->fetch_array($listResult)) {
                    $rawData['id'] = $rawData['activationcodeid'];
                    $rawData['startdate']=substr($rawData['startdate'],0,2)>0?$rawData['startdate']:'';
                    $rawData['expiredate']=substr($rawData['expiredate'],0,2)>0?$rawData['expiredate']:'';
                    $listViewRecordModels[$rawData['activationcodeid']] = $rawData;
                }
                return $listViewRecordModels;
            //②对账及获取对账结果
            }else if($_REQUEST['orderType']=='reconciliation'){
                $db->startTransaction();
                //如果是点击对账按钮走这里
                if(isset($_REQUEST['AJAX'])){
                    $listQuery = str_replace("SELECT vtiger_activationcode.ordercode,","SELECT vtiger_activationcode.ordercode,vtiger_activationcode.productid,vtiger_activationcode.buyseparately,vtiger_activationcode.receivetime,",$listQuery);
                    //如果是归档后再次对账 把查询T云WEB订单管理表 （vtiger_activationcode ）都替换成已归档数据表（vtiger_activationcode_file）
                    if(isset($_REQUEST['again'])){
                        $listQuery = str_replace("LEFT JOIN vtiger_tyuncontractactivacode ON vtiger_activationcode.activationcodeid = vtiger_tyuncontractactivacode.activationcodeid","",$listQuery);
                        $listQuery = str_replace("vtiger_activationcode","vtiger_activationcode_file",$listQuery);
                        $listQuery = str_replace("vtiger_tyuncontractactivacode","vtiger_activationcode_file",$listQuery);
                        $listResult = $db->pquery($listQuery, array());//die();
                        while($rawData=$db->fetch_array($listResult)) {
                            // 获取请求T云订单id
                            $rawId[] = $rawData['ordercode'];
                            $rawIdData[$rawData['ordercode']]=$rawData;
                            $listViewRecordModels[] = $rawData;
                            $customerId[]=$rawData['customerid'];
                        }
                    }else{
                        $listResult = $db->pquery($listQuery, array());//die();
                        while($rawData=$db->fetch_array($listResult)) {
                            // 获取请求T云订单id
                            $rawId[] = $rawData['ordercode'];
                            $rawIdData[$rawData['ordercode']]=$rawData;
                            $listViewRecordModels[] = $rawData;
                            $customerId[]=$rawData['customerid'];
                            if($rawData['isitfiled']=='yes_filed'){
                                $successData =array('success'=>'false','message'=>"对账失败，归档过的数据前往归档数据列表进行对账！");
                                echo json_encode($successData);exit();
                            }
                        }
                    }

                    if(count($rawId)>2000){
                        $successData =array('success'=>'false','message'=>"数据量不支持超过2000条，请重新筛选!");
                        echo json_encode($successData);exit();
                    }
                    //要对比的字段数据  productid  buyseparately
                    //$comparedFields=array('createdtime'=>'createdtime','ordercode'=>'ordercode','customername'=>'customername','classtype'=>'classtype','onoffline'=>'onoffline','productname'=>'productname','startdate'=>'startdate','expiredate'=>'expiredate','orderamount'=>'orderamount','contractprice'=>'contractprice','contractname'=>'contractname','productlife'=>'productlife');
                    // 实际字段对应 key -> Tyun  value -> erp  money ->orderamount    contractMoney->contractprice
                    $comparedFields=array('addDate'=>'createdtime','orderCode'=>'ordercode','nickName'=>'customername','productType'=>'classtype','type'=>'onoffline','groupName'=>'productname','openDate'=>'startdate','closeDate'=>'expiredate','money'=>'orderamount','contractMoney'=>'contractprice','contractCode'=>'contractname','buyTerm'=>'productlife');
                    // tyun 数据对应汉字
                    $productType=array(1=>'购买订单',2=>'升级',3=>'降级',4=>'续费产品订单');
                    // 对应erp 字段值
                    $productValue=array(1=>'buy',2=>'upgrade',3=>'degrade',4=>'renew');
                    // tyun 数据对应汉字
                    $type=array(0=>'线上',1=>'线下');
                    // 对应erp 字段值
                    $typeValue=array(0=>'line',1=>'offline');
                    // 对账结果数组
                    $resultReconciliation = array();
                    // Tyun
                    $tyunparams=array();
                       /* "accountIdList"=>array(2136482,2136494,2136508,2136514,2136520,2136521)//   0线上1线下*/
                    $whereTyun = $this->getTyunSearchWhere();
                    if($whereTyun['success']){
                        if(isset($whereTyun['data']['accountIdList'])){
                            $whereTyun['data']['accountIdList']=array_unique($customerId);
                        }
                    }else{
                        $whereTyun['data']['orderCodes']=$rawId;
                    }
                    // 对比直销的所以写死在这里。
                    $tyunparams =$whereTyun['data'];
                    $tyunparams['agentType']=0;
                    $this->_logs(array("本地组装参数：", $tyunparams));
                    global $tyunweburl,$sault;
                    $reconciliationUrl= $tyunweburl."api/app/agent-admin/v1.0.0/agentAdmin/Order/listOrderAndCrm";
                    // 在这开始请求获取结果数据
                    /**
                     *请求接口数据
                     */
                    /*$tyunparams=array(
                        "orderCodes"=>array('Z20190917031841532065'),
                        "accountIdList"=>array(2136482,2136494,2136508,2136514,2136520,2136521)//   0线上1线下
                    );*/
                    //$this->_logs(array("renewdoOrder：", json_encode($tyunparams)));
                    $postData=json_encode($tyunparams);
                    $time=time().'123';
                    $sault=$time.$sault;
                    $token=md5($sault);
                    $curlset=array(CURLOPT_HTTPHEADER=>array(
                        "Content-Type:application/json",
                        "S-Request-Token:".$token,
                        "S-Request-Time:".$time),
                        CURLINFO_HEADER_OUT=>array(true));
                    $res = $this->https_request($reconciliationUrl, $postData,$curlset);
                    $this->_logs(array("Tyunreturndata：",$res));
                    //$this->_logs(array("本地数据数组 ：",$rawIdData));
                    $res = json_decode($res,true);
                    //$this->_logs(array("TyunreturndataTyun返回的数据：",count($res['data'])));
                    $tyunReturnAllData=array();
                    if($res['code']==200){
                        $tyunReturnAllData=$res['data'];
                    }else{
                        $successData =array('success'=>'false','message'=>$res['message']);
                        echo json_encode($successData);exit();
                    }
                    $TyunID = array();
                    // 循环遍历 t 云返回数据组
                    foreach ($tyunReturnAllData as $val){
                        if(isset($tyunRawIdData[$val['orderCode']])){
                            // 原来是套餐和 产品分别比对 现在使用差集处理如果差集存在说明 产品所拥有的产品不同。现在把所有产品id 放到一个数组汇总 下面比对时 求差集是否存判断产品是否一致
                            /*//1单品
                            if($val['packageOrProducts']==1){

                            //2套餐
                            }else if($val['packageOrProducts']==2){
                                $tyunRawIdData[$val['ordercode']]['productId']=$val['productId'];
                            }*/
                            if(in_array($val['orderCode'],$TyunID)){

                            }else{
                                $TyunID[]=$val['orderCode'];
                            }
                            $tyunRawIdData[$val['orderCode']]['allproductId'][]=$val['productId'];
                            $tyunRawIdData[$val['orderCode']]['groupName'].=",".$val['groupName'];
                        }else{
                            if(in_array($val['orderCode'],$TyunID)){

                            }else{
                                $TyunID[]=$val['orderCode'];
                            }
                            /*//1单品
                            if($val['packageOrProducts']==1){
                                $val['buyseparately'][]=$val['productId'];
                                $tyunRawIdData[$val['ordercode']]=$val;
                            //2套餐
                            }else if($val['packageOrProducts']==2){

                            }*/
                            $val['allproductId'][]=$val['productId'];
                            $tyunRawIdData[$val['orderCode']]=$val;
                        }
                    }
                    $this->_logs(array("遍历后Tyun数据orderCode ：".count($TyunID),$TyunID));
                    $this->_logs(array("遍历后Tyun数据 ：",$tyunRawIdData));
                    $this->_logs(array("遍历后Tyun数据记录条数 ：",count($tyunRawIdData)));
                    // T云缺的数据记录
                    $TyunIsLose=array();
                    // erp 缺失的数据记录
                    $ErpIsLose=array();
                    //对账失败的id str
                    $idStrSuccess='';
                    //对账成功的id str
                    $idStrError='';
                    $this->_logs(array("本地查询出数据",$listViewRecordModels));
                    //获取T云数据 由于接口不存在先默认T云数据接口数据为当前查询列表数据
                    foreach ($listViewRecordModels as $key=>$val){
                          //判断Tyun数据是否存在如果T云数据也存在该订单id的订单则进行比对（对账）
                          if(isset($tyunRawIdData[$val['ordercode']])){
                              $isError=false;
                              $errorFieldsArray=array();
                              // 遍历下要比较的的字段
                              foreach ($comparedFields as $keys=>$vals){
                                  switch ($vals){
                                      case 'productname':
                                          // 比对Tyun erp 数据产品是否一样。
                                          $tyunarrayId=$tyunRawIdData[$val['ordercode']]['allproductId'];
                                          //$this->_logs(array("tyunarrayId：",json_encode($tyunarrayId)));
                                          $erparrayId=array();
                                          $erparrayId=explode(',',trim($val['buyseparately'],','));
                                          $erparrayId[]=$val['productid'];
                                          //$this->_logs(array("erparrayId：",json_encode($erparrayId)));
                                          $tyunarrayId=array_filter($tyunarrayId);
                                          $erparrayId = array_filter($erparrayId);
                                          $tyunarrayId=array_unique($tyunarrayId);
                                          $erparrayId=array_unique($erparrayId);
                                          // 如果存在差集那么数据不一致
                                          if(array_diff($tyunarrayId,$erparrayId) || array_diff($erparrayId,$tyunarrayId)){
                                              $isError=true;
                                              $errorFieldsArray[$vals."s"]=1;
                                              if($tyunRawIdData[$val['ordercode']][$keys]){
                                                  $errorFieldsArray[$vals]=$tyunRawIdData[$val['ordercode']][$keys];
                                              }else{
                                                  $errorFieldsArray[$vals]='--';
                                              }

                                          }
                                          /*$buyseparately=false;
                                          if($val['productid']==$tyunRawIdData[$val['ordercode']]['productId'] && $buyseparately){
                                          }*/
                                          break;
                                      //订单类型
                                      case 'classtype':
                                          // buy:购买、upgrade:升级、renew:续费、againbuy:另购
                                          if($val[$vals]!=$productValue[$tyunRawIdData[$val['ordercode']][$keys]]){
                                              $isError=true;
                                              $errorFieldsArray[$vals."s"]=1;
                                              $errorFieldsArray[$vals]=$productType[$tyunRawIdData[$val['ordercode']][$keys]];
                                          }
                                          break;
                                      case 'onoffline':
                                          // 两种 线上 line 线下 offline
                                          if($val[$vals]!=$typeValue[$tyunRawIdData[$val['ordercode']][$keys]]){
                                              $isError=true;
                                              $errorFieldsArray[$vals."s"]=1;
                                              $errorFieldsArray[$vals]=$type[$tyunRawIdData[$val['ordercode']][$keys]];
                                          }
                                          break;
                                      default:
                                          //array('addDate'=>'receivetime','orderCode'=>'ordercode','nickName'=>'customername','productType'=>'classtype','type'=>'onoffline','groupName'=>'productname','openDate'=>'startdate','closeDate'=>'expiredate','maketMoney'=>'orderamount','contractMoney'=>'contractprice','contractCode'=>'contractname','buyTerm'=>'productlife')
                                          //一般可以直接比较的字段处理。
                                          if(in_array($vals,array('createdtime','startdate','expiredate'))){
                                              // 因为本地存储的时间为空的是时间datetime
                                              if($val[$vals]=='0000-00-00 00:00:00'){
                                                  $val[$vals]=null;
                                              }else{
                                                  $val[$vals]=$val[$vals];
                                              }
                                          }
                                          if($val[$vals]!=$tyunRawIdData[$val['ordercode']][$keys]){
                                              $isError=true;
                                              $errorFieldsArray[$vals."s"]=1;
                                              if($tyunRawIdData[$val['ordercode']][$keys]){
                                                  $errorFieldsArray[$vals]=$tyunRawIdData[$val['ordercode']][$keys];
                                              }else{
                                                  $errorFieldsArray[$vals]='--';
                                              }
                                          }
                                          break;
                                  }

                              }
                              // 这一条数据比对完后 重新组装数据先存旧数据 如果有问题把 有问题数据 追加该条旧数据之后 如果有$isError =true 则对账失败
                              if($isError){
                                  $idStrError.="'".$val['ordercode']."',";
                                  // 记录对账出错的信息
                                  $resultReconciliation[]=$val;
                                  $errorFieldsArray['errorType']=3;
                                  $resultReconciliation[]=$errorFieldsArray;
                                  //删除已经对比出错数据
                                  unset($rawIdData[$val['ordercode']]);
                                  //对账成功
                              }else{
                                  $idStrSuccess .="'".$val['ordercode']."',";
                              }
                              //删除T云已经对比过的数据
                              unset($tyunRawIdData[$val['ordercode']]);
                          // 如果Tun记录不存在则记录 Tyun缺的数据记录数据
                          }else{
                             /* 'createdtime','startdate','expiredate'*/
                              if($val['createdtime']=='0000-00-00 00:00:00'){
                                  $val['createdtime']=null;
                              }
                              if($val['startdate']=='0000-00-00 00:00:00'){
                                  $val['startdate']=null;
                              }
                              if($val['expiredate']=='0000-00-00 00:00:00'){
                                  $val['expiredate']=null;
                              }
                              $idStrError.="'".$val['ordercode']."',";
                              //也删除T云没有记录的数据即对比出错数据
                              unset($rawIdData[$val['ordercode']]);
                              $val['errorType']=1;
                              $TyunIsLose[]=$val;
                          }
                    }
                    //重新组装查询条件
                    $whereStr = $this->getSearchWhereContent();
                    $whereStr = str_replace("<br>",",",$whereStr['data']);
                    $whereStr =trim($whereStr,",");
                    $idStrSuccess=trim($idStrSuccess,',');
                    // 如果是归档中对账则不更改对账结果状态
                    if(!isset($_REQUEST['again'])){
                        //修改对账成功数据记录为对账成功
                        $sql=" UPDATE vtiger_activationcode SET reconciliationresult='success_reconciliation' WHERE  ordercode IN(".$idStrSuccess.")";
                        $db->pquery($sql,array());
                        $idStrError = trim($idStrError,',');
                        //修改对账失败数据记录为对账失败
                        $sql=" UPDATE vtiger_activationcode SET reconciliationresult='error_reconciliation' WHERE  ordercode IN(".$idStrError.")";
                        $db->pquery($sql,array());
                    }
                    //给Tyun 剩余未对账数据添加标识（即是erp没有的ordercode的订单数据）
                    foreach ($tyunRawIdData as $key=>$val){
                        $tyunRawIdData[$key]['errorType']=2;
                        $tyunRawIdData[$key]['createdtime']=$val['addDate'];
                        $tyunRawIdData[$key]['ordercode']=$val['orderCode'];
                        $tyunRawIdData[$key]['customername']=$val['nickName'];
                        $tyunRawIdData[$key]['classtype']=$productType[$val['productType']];
                        $tyunRawIdData[$key]['onoffline']=$type[$val['type']];
                        $tyunRawIdData[$key]['productname']=$val['groupName'];
                        $tyunRawIdData[$key]['startdate']=$val['openDate'];
                        $tyunRawIdData[$key]['expiredate']=$val['closeDate'];
                        $tyunRawIdData[$key]['orderamount']=$val['maketMoney'];
                        $tyunRawIdData[$key]['contractprice']=$val['contractMoney'];
                        $tyunRawIdData[$key]['contractname']=$val['contractCode'];
                        $tyunRawIdData[$key]['productlife']=$val['buyTerm'];
                    }
                    $this->_logs(array("计数错误的：本地总数",count($listViewRecordModels)));
                    $this->_logs(array("计数错误的：已对比错误总数",count($resultReconciliation)));
                    $this->_logs(array("计数错误的：Tyun丢失数量",count($TyunIsLose)));
                    $this->_logs(array("计数错误的：erp丢失数量",count($tyunRawIdData)));
                    //然后把Tyun 缺失数据 即前面记录$TyunIsLose 和 Tyun 未对账的数据 追加到对账失败的对账记录之后
                    if($resultReconciliation){
                        if($TyunIsLose){
                            $resultReconciliation = array_merge($resultReconciliation,$TyunIsLose);
                        }
                    }else{
                        $resultReconciliation = $TyunIsLose;
                    }
                    $this->_logs(array("计数错误的：已对比错误总数合并Tyun丢失",count($resultReconciliation)));
                    // 存在才合并不存在不合并
                    if($resultReconciliation){
                        if ($tyunRawIdData){
                            $resultReconciliation = array_merge($resultReconciliation,$tyunRawIdData);
                        }
                    }else{
                        $resultReconciliation = $tyunRawIdData;
                    }
                    global  $current_user;
                    $this->_logs(array("计数错误的：已对比错误总数合并ERp丢失",count($resultReconciliation)));
                    // 对账总失败个数 = 对账本地总数 - 对账成功个数（本地总数-对账失败的-Tun记录缺失的） + T云剩余未对账个数(Tyun返回条数-已完成对账的（对账成功和失败的）)
                    $allErrorNumber=count($listViewRecordModels)-count($rawIdData)+count($tyunRawIdData);
                    //对账成功个数（本地总数-对账失败的-Tun记录缺失的）
                    $successNumber= count($rawIdData);
                    //把对账结果数据 插入数据库记录
                    $reconciliationSql = "INSERT INTO  vtiger_reconciliation_record (userid,successnumber,errornumber,content,searchwhere,createtime) VALUES(?,?,?,?,?,?)";

                   /*$this->_logs(array("resultReconciliation：",$resultReconciliation));*/
                    // 实际数据
                    $result = $db->pquery($reconciliationSql,array($current_user->id,$successNumber,$allErrorNumber,json_encode($resultReconciliation),$whereStr,date("Y-m-d H:i:s")));
                    $db->completeTransaction();
                    /* 这个是把查询记录存储的随时打开就能走通 */
                    //$result = $db->pquery($reconciliationSql,array($current_user->id,$successNumber,$allErrorNumber,json_encode($listViewRecordModels),$whereStr,date("Y-m-d H:i:s")));
                    $id = $db->getLastInsertID($result);

                    $successData =array();
                    if($id){
                        //如果对账错误个数为零不跳转 不为零跳转
                        if($allErrorNumber>0){
                            $successData =array('success'=>'true','jump'=>'true','recordId'=>$id);
                        }else{
                            $successData =array('success'=>'true','jump'=>'false','message'=>"对账成功！");
                        }
                    }else{
                        $successData =array('success'=>'false','message'=>"对账失败请刷新后再次尝试！");
                    }
                    echo json_encode($successData);exit();
                //③显示对账结果数据
                }else{
                    $recorId = $_REQUEST['record'];
                    $sql = "SELECT * FROM  vtiger_reconciliation_record WHERE id= ? LIMIT 1 ";
                    $result = $db->pquery($sql,array($recorId));
                    $reconciliationData=$db->raw_query_result_rowdata($result,0);
                    $_REQUEST['reconciliationData']=array('id'=>$reconciliationData['id'],'successnumber'=>$reconciliationData['successnumber'],'errornumber'=>$reconciliationData['errornumber'],'searchwhere'=>$reconciliationData['searchwhere']);
                    return json_decode($reconciliationData['content'],true);
                }

            }
        //未归档前T云WEB订单管理   正常的走else
        }else{
            $listQuery .= " LIMIT $startIndex,".($pageLimit);

            //echo $listQuery;die();

            $listResult = $db->pquery($listQuery, array());
            $index = 0;
            $moduleMOdel=$this->getModule();
            while($rawData=$db->fetch_array($listResult)) {
                $rawData['id'] = $rawData['activationcodeid'];
                $orderstatus = $rawData['orderstatus'];
                $startdate=$rawData['startdate'];
                $current_date=date('Y-m',strtotime('+1 month')).'-15';
                $rawData['startdate']=substr($startdate,0,2)>0?$startdate:'';
                $rawData['expiredate']=substr($rawData['expiredate'],0,2)>0?$rawData['expiredate']:'';
                if(!empty($startdate) && substr($startdate,0,2)>0 && strtotime($startdate)<strtotime($current_date)){
                    if($orderstatus == 'ordercancel'){
                        $docanceltime = explode('-',$rawData['canceldatetime']);
                        $tempmonth=$docanceltime[1]+1;
                        $docanceldate=$tempmonth>12?(($docanceltime[0]+1).'-01'):($docanceltime[0].'-'.$tempmonth);
                        $current_date=$docanceldate;
                    }
                    $currentDiffMonth=$moduleMOdel->getMonthNum(substr($startdate,0,7).'-01',substr($current_date,0,7).'-15');
                    $currentDiffMonth=$currentDiffMonth['y']*12+$currentDiffMonth['m'];
                    $maxMonth=$rawData['productlife']*12;
                    $diffMonth=$currentDiffMonth>$maxMonth?$maxMonth:$currentDiffMonth;
                    $monthlyIncome=$rawData['contractprice']/$maxMonth;
                    $monthlyIncome=number_format($monthlyIncome,2,'.','');
                    $cumulativeIncome=$diffMonth!=$maxMonth?$diffMonth*$monthlyIncome:$rawData['contractprice'];
                    $isMaturity = $currentDiffMonth > $maxMonth ? '是' : '否';
                    if($orderstatus == 'ordercancel') {
                        $thisMonthlyIncome = '--';
                        $isMaturity =  '是';
                    }else{
                        $thisMonthlyIncome = (empty($rawData['startdate']) || $currentDiffMonth > $maxMonth) ? 0.00 : $monthlyIncome;
                    }
                    $rawData['paymenttotal']=($rawData['paymenttotal']<=$rawData['contractprice'])?$rawData['paymenttotal']:$rawData['servicecontractstotal'];
                    $rawData['thisMonthlyIncome'] = $thisMonthlyIncome;//本月确认收入
                    $rawData['isMaturity'] = $isMaturity;//是否到期
                    $rawData['monthlyIncome'] = $monthlyIncome;//每月确认收入
                    $rawData['cumulativeIncome'] = $cumulativeIncome;//累计确认收入
                    $rawData['temprepaty']=bcsub($rawData['contractprice'],$rawData['paymenttotal'],2);
                    $rawData['tempinvoice']=bcsub($rawData['contractprice'],$rawData['invoicetotal'],2);
                    $rawData['accountsreceivable']=bcsub($cumulativeIncome,$rawData['contractprice'],2);//合同应收账款
                }elseif($orderstatus == 'ordercancel'){
                    $rawData['thisMonthlyIncome'] ='--';//本月确认收入
                    $rawData['isMaturity'] = '是';//是否到期
                    $rawData['monthlyIncome'] = '--';//每月确认收入
                    $rawData['cumulativeIncome'] = '--';//累计确认收入
                    $isMaturity='是';
                    $monthlyIncome=0.00;
                    $cumulativeIncome=0.00;
                    $thisMonthlyIncome=0.00;
                    $rawData['temprepaty']=0;
                    $rawData['tempinvoice']=0;
                    $rawData['accountsreceivable']=0;//合同应收账款
                }else{
                    $rawData['thisMonthlyIncome'] ='--';//本月确认收入
                    $rawData['isMaturity'] = '否';//是否到期
                    $rawData['monthlyIncome'] = '--';//每月确认收入
                    $rawData['cumulativeIncome'] = '--';//累计确认收入
                    $isMaturity='否';
                    $monthlyIncome=0.00;
                    $cumulativeIncome=0.00;
                    $thisMonthlyIncome=0.00;
                    $rawData['temprepaty']=0;
                    $rawData['tempinvoice']=0;
                    $rawData['accountsreceivable']=0;//合同应收账款
                }

            //下单当天取消的都显示为0
            if($rawData['canceldatetime'] && strtotime(date('Y-m-d',strtotime($rawData['canceldatetime']))) == strtotime(date('Y-m-d',strtotime($rawData['activedate'])))){
                $rawData['thisMonthlyIncome'] ='--';//本月确认收入
                $rawData['isMaturity'] = '否';//是否到期
                $rawData['monthlyIncome'] = '--';//每月确认收入
                $rawData['cumulativeIncome'] = '--';//累计确认收入
                $isMaturity='否';
                if($orderstatus=='ordercancel'){
                    $rawData['isMaturity'] = '是';//是否到期
                    $isMaturity='是';
                }
                $monthlyIncome=0.00;
                $cumulativeIncome=0.00;
                $thisMonthlyIncome=0.00;
                $rawData['temprepaty']=0;
                $rawData['tempinvoice']=0;
                $rawData['accountsreceivable']=0;//合同应收账款
            }
                $rawData['thisMonthlyIncome'] = $thisMonthlyIncome;//本月确认收入
                $rawData['isMaturity'] = $isMaturity;//是否到期
                $rawData['monthlyIncome'] = $monthlyIncome;//每月确认收入
                $rawData['cumulativeIncome'] = $cumulativeIncome;//累计确认收入
                $rawData['productnametitle']=$rawData['productname'];
                $rawData['productname']=(mb_strlen($rawData['productname'], 'utf8') > 20) ? mb_substr($rawData['productname'], 0, 20, 'utf8') . '...': $rawData['productname'];
                $listViewRecordModels[$rawData['activationcodeid']] = $rawData;
            }
            return $listViewRecordModels;
        }

	}
    /**
     * 写日志，用于测试,可以开启关闭
     * @param data mixed
     */
    public function _logs($data, $file = 'logs_'){
        $year	= date("Y");
        $month	= date("m");
        $dir	= './Logs/' . $year . '/' . $month . '/';
        if(!is_dir($dir)) {
            mkdir($dir,0755,true);
        }
        $file = $dir . $file . date('Y-m-d').'.txt';
        @file_put_contents($file, '----------------' . date('H:i:s') . '--------------------'.PHP_EOL.var_export($data,true).PHP_EOL, FILE_APPEND);
    }
	// curl 请求
    public function https_request($url, $data = null,$curlset=array()){
        $this->_logs(array("发送到T云服务端的url请求", $url));
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
        $this->_logs(array("返回处理结果：", $output));
        curl_close($curl);
        return $output;
    }
    public function getListViewHeaders() {
        $sourceModule = $this->get('src_module');
        $queryGenerator = $this->get('query_generator');
        if(!empty($sourceModule)){
           return $queryGenerator->getModule()->getPopupFields();
        }else{

            $list=$queryGenerator->getModule()->getListFields();
            foreach($list as $fields){
                $temp[$fields['fieldlabel']]=$fields;
            }
           return $temp;
        }
        return $queryGenerator->getFocus()->list_fields_name;
        
    }
    public function getUserWhere(){
        $searchDepartment = $_REQUEST['department'];
        $listQuery='';
        $companyQuery=getAccessibleCompany('vtiger_activationcode.contractid','ServiceContracts',false);
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('ServiceContracts','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            $listQuery=' and (vtiger_activationcode.creator in ('.implode(',',$where).') OR '.$companyQuery.')';
        }else{
            $where=getAccessibleUsers('ServiceContracts','List');
            if($where!='1=1'){
                $listQuery= ' and (vtiger_activationcode.creator '.$where.' OR '.$companyQuery.')';
            }
        }
        return $listQuery;
    }
    public function getListViewCount() {
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
        $where=$this->getUserWhere();
        //$where.= ' AND accountname is NOT NULL';
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
        $listQuery.=" AND vtiger_activationcode.comeformtyun=1 AND iscollegeedition=0 ";
        if($_REQUEST['orderType']=='reconciliation' || $_REQUEST['orderType']=='filed'){
            $listQuery = str_replace("LEFT JOIN vtiger_tyuncontractactivacode ON vtiger_activationcode.activationcodeid = vtiger_tyuncontractactivacode.activationcodeid","",$listQuery);
            $listQuery = str_replace("vtiger_activationcode","vtiger_activationcode_file",$listQuery);
            $listQuery = str_replace("vtiger_tyuncontractactivacode","vtiger_activationcode_file",$listQuery);
        }
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }

    // 获取查询条件 并返回查询内容 cxh 2019-10-12 只有归档操作 和 对账 用的到
    public function getSearchWhereContent(){
        $searchKey = $this->get('search_key');
        $queryGenerator = $this->get('query_generator');
        $queryGenerator -> addSearchWhere('');//置空
        $searchValue = $this->get('search_value');
        $operator = $this->get('operator');
        if(!empty($searchKey)) {
            $queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator ,'leftkh'=>'','rightkh'=>'','andor'=>''));
        }

        $BugFreeQuery=isset($_REQUEST['BugFreeQuery'])?$_REQUEST['BugFreeQuery']:'';
        $str='';
        $receivetime=array();
        $startdate =array();
        $expiredate=array();
        if(!empty($BugFreeQuery)){
            $BugFreeQuery=json_decode($BugFreeQuery,true);
            if(isset($BugFreeQuery['BugFreeQuery[queryRowOrder]'])){
                $SearchConditionRow=$BugFreeQuery['BugFreeQuery[queryRowOrder]'];
                $SearchConditionRow=explode(',',$SearchConditionRow);
                $counts=count($SearchConditionRow);
                if(is_array($SearchConditionRow)&&!empty($SearchConditionRow)){
                    foreach($SearchConditionRow as $key=>$val){
                        $val=str_replace('SearchConditionRow','',$val);
                        $leftkh=$BugFreeQuery['BugFreeQuery[leftParenthesesName'.$val.']'];
                        $rightkh=$BugFreeQuery['BugFreeQuery[rightParenthesesName'.$val.']'];
                        $andor=$BugFreeQuery['BugFreeQuery[andor'.$val.']'];
                        $searchKey=$BugFreeQuery['BugFreeQuery[field'.$val.']'];
                        $operator=$BugFreeQuery['BugFreeQuery[operator'.$val.']'];
                        $searchValue=$BugFreeQuery['BugFreeQuery[value'.$val.']'];
                        /*$searchKey= preg_match($searchKey,"(?<=.).*?(?=#)");*/
                        $firstIndex = strpos($searchKey,'.');
                        $secondIndex = strpos($searchKey,'#');
                        //把查询条件的field 截取出查询的字段。
                        $searchKey = substr($searchKey,$firstIndex+1,$secondIndex-$firstIndex-1);
                        if($searchKey!='epartmen' && !empty($searchValue)){
                            if($searchKey=='receivetime'){
                                  if($operator =='<='){
                                      $receivetime['leq']=$searchValue;
                                  }else if($operator =='>='){
                                      $receivetime['geq']=$searchValue;
                                      // else 应该报错 或者提示不能这么查
                                  }else{
                                      $receivetime['leq']=$searchValue;
                                      $receivetime['geq']=$searchValue;
                                  }
                            }else if($searchKey=='startdate'){
                                if($operator =='<='){
                                    $startdate['leq']=$searchValue;
                                }else if($operator =='>='){
                                    $startdate['geq']=$searchValue;
                                    // else 应该报错 或者提示不能这么查
                                }else{
                                    $startdate['leq']=$searchValue;
                                    $startdate['geq']=$searchValue;
                                }
                            }else if($searchKey=='expiredate'){
                                if($operator =='<='){
                                    $expiredate['leq']=$searchValue;
                                }else if($operator =='>='){
                                    $expiredate['geq']=$searchValue;
                                    // else 应该报错 或者提示不能这么查
                                }else{
                                    $expiredate['leq']=$searchValue;
                                    $expiredate['geq']=$searchValue;
                                }
                            }else{
                                $searchValue = vtranslate($searchValue,'TyunWebBuyService');
                                if($searchKey=='creator'){
                                    global $adb;
                                    $userSql=" SELECT id, CONCAT( '(',IFNULL(brevitycode,''),')',last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', IF ( `status` = 'Active', '', '[离职]' )) AS last_name FROM vtiger_users WHERE vtiger_users.id=? LIMIT 1 ";
                                    $result=$adb->pquery($userSql,array((int)$searchValue));
                                    $searchValue=$adb->query_result_rowdata($result,0);
                                    $searchValue=$searchValue['last_name'];
                                }
                                $searchKey = vtranslate($searchKey,'TyunWebBuyService');

                                $str .=$searchKey.":".$searchValue."<br>";
                            }

                        }
                    }
                }
            }
        }
        if(!empty($receivetime)){
            $searchKey = vtranslate('receivetime','TyunWebBuyService');
            if($receivetime['geq']==$receivetime['leq']){
                $str .=$searchKey.":".$receivetime['geq']."<br>";
            }else{
                $str .=$searchKey.":".$receivetime['geq']."~".$receivetime['leq']."<br>";
            }
        }
        if(!empty($startdate)){
            $searchKey = vtranslate('startdate','TyunWebBuyService');
            if($startdate['geq']==$startdate['leq']){
                $str .=$searchKey.":".$startdate['geq']."<br>";
            }else {
                $str .= $searchKey . ":" . $startdate['geq'] . "~" . $startdate['leq'] . "<br>";
            }
        }
        if(!empty($expiredate)){
            $searchKey = vtranslate('expiredate','TyunWebBuyService');
            if($expiredate['geq']==$expiredate['leq']){
                $str .=$searchKey.":".$expiredate['geq']."<br>";
            }else {
                $str .= $searchKey . ":" . $expiredate['geq'] . "~" . $expiredate['leq'] . "<br>";
            }
        }
        return array("data"=>$str);
    }

    //重新组装 请求Tyun的参数
    public function getTyunSearchWhere(){
        $searchKey = $this->get('search_key');
        $queryGenerator = $this->get('query_generator');
        $queryGenerator -> addSearchWhere('');//置空
        $searchValue = $this->get('search_value');
        $operator = $this->get('operator');
        if(!empty($searchKey)) {
            $queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator ,'leftkh'=>'','rightkh'=>'','andor'=>''));
        }
        $BugFreeQuery=isset($_REQUEST['BugFreeQuery'])?$_REQUEST['BugFreeQuery']:'';
        $data=array();
        $j=0;
        if(!empty($BugFreeQuery)){
            $BugFreeQuery=json_decode($BugFreeQuery,true);
            if(isset($BugFreeQuery['BugFreeQuery[queryRowOrder]'])){
                $SearchConditionRow=$BugFreeQuery['BugFreeQuery[queryRowOrder]'];
                $SearchConditionRow=explode(',',$SearchConditionRow);
                $counts=count($SearchConditionRow);
                $i=0;
                if(is_array($SearchConditionRow)&&!empty($SearchConditionRow)){
                    foreach($SearchConditionRow as $key=>$val){
                        $val=str_replace('SearchConditionRow','',$val);
                        $leftkh=$BugFreeQuery['BugFreeQuery[leftParenthesesName'.$val.']'];
                        $rightkh=$BugFreeQuery['BugFreeQuery[rightParenthesesName'.$val.']'];
                        $andor=$BugFreeQuery['BugFreeQuery[andor'.$val.']'];
                        $searchKey=$BugFreeQuery['BugFreeQuery[field'.$val.']'];
                        $operator=$BugFreeQuery['BugFreeQuery[operator'.$val.']'];
                        $searchValue=$BugFreeQuery['BugFreeQuery[value'.$val.']'];
                        /*$searchKey= preg_match($searchKey,"(?<=.).*?(?=#)");*/
                        $firstIndex = strpos($searchKey,'.');
                        $secondIndex = strpos($searchKey,'#');
                        //把查询条件的field 截取出查询的字段。
                        $searchKey = substr($searchKey,$firstIndex+1,$secondIndex-$firstIndex-1);
                        $j+=$j;
                        if($j>=0 && !empty($searchKey)){
                            if($searchKey!='epartmen' && in_array($searchKey,array('receivetime','startdate','expiredate','customername')) && !empty($searchValue)){
                                switch ($searchKey){
                                    case 'receivetime':
                                        if($operator =='<='){
                                            if(isset($data['addDataEnd'])) return array("success"=>false,'data'=>[]);
                                            $data['addDataEnd']=$searchValue;
                                        }else if($operator =='>='){
                                            if(isset($data['addDataStart'])) return array("success"=>false,'data'=>[]);
                                            $data['addDataStart']=$searchValue;
                                        }else if($operator =='LIKE'){
                                            if(isset($data['addDataStart'])||isset($data['addDataEnd'])) return array("success"=>false,'data'=>[]);
                                            $data['addDataStart']=$searchValue;
                                            $data['addDataEnd']=$searchValue;
                                        }
                                        break;
                                    case 'startdate':
                                        if($operator =='<='){
                                            if(isset($data['activateDateEnd'])) return array("success"=>false,'data'=>[]);
                                            $data['activateDateEnd']=$searchValue;
                                        }else if($operator =='>='){
                                            if(isset($data['activateDateStart'])) return array("success"=>false,'data'=>[]);
                                            $data['activateDateStart']=$searchValue;
                                        }else if($operator =='LIKE'){
                                            if(isset($data['activateDateStart'])||isset($data['activateDateEnd'])) return array("success"=>false,'data'=>[]);
                                            $data['activateDateStart']=$searchValue;
                                            $data['activateDateEnd']=$searchValue;
                                        }
                                        break;
                                    case 'expiredate':
                                        if($operator =='<='){
                                            if(isset($data['endCloseDate'])) return array("success"=>false,'data'=>[]);
                                            $data['endCloseDate']=$searchValue;
                                        }else if($operator =='>='){
                                            if(isset($data['startCloseDate'])) return array("success"=>false,'data'=>[]);
                                            $data['startCloseDate']=$searchValue;
                                        }else if($operator =='LIKE'){
                                            if(isset($data['startCloseDate'])||isset($data['endCloseDate'])) return array("success"=>false,'data'=>[]);
                                            $data['startCloseDate']=$searchValue;
                                            $data['endCloseDate']=$searchValue;
                                        }
                                        break;
                                    case 'customername':
                                        $data['accountIdList']=array();
                                        //    如果是customername 客户名称 那么
                                        break;
                                    default:
                                        break;
                                }
                            }else {
                                return array("success"=>false,'data'=>[]);
                            }
                        }

                    }
                }
            }
        }
        return array("success"=>true,"data"=>$data);
    }

}