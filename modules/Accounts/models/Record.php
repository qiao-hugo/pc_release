<?php

/* +********
 * 客户信息管理
 * ******** */

class Accounts_Record_Model extends Vtiger_Record_Model {

    /**
     * Function returns the details of Accounts Hierarchy
     * @return <Array>
     */
    function getAccountHierarchy() {
        $focus = CRMEntity::getInstance($this->getModuleName());
        $hierarchy = $focus->getAccountHierarchy($this->getId());
        $i = 0;
        foreach ($hierarchy['entries'] as $accountId => $accountInfo) {
            preg_match('/<a href="+/', $accountInfo[0], $matches);
            if ($matches != null) {
                preg_match('/[.\s]+/', $accountInfo[0], $dashes);
                preg_match("/<a(.*)>(.*)<\/a>/i", $accountInfo[0], $name);

                $recordModel = Vtiger_Record_Model::getCleanInstance('Accounts');
                $recordModel->setId($accountId);
                $hierarchy['entries'][$accountId][0] = $dashes[0] . "<a href=" . $recordModel->getDetailViewUrl() . ">" . $name[2] . "</a>";
            }
        }
        return $hierarchy;
    }

    /**
     * Function returns the url for create event
     * @return <String>
     */
    function getCreateEventUrl() {
        $calendarModuleModel = Vtiger_Module_Model::getInstance('Calendar');
        return $calendarModuleModel->getCreateEventRecordUrl() . '&parent_id=' . $this->getId();
    }

    /**
     * Function returns the url for create todo
     * @retun <String>
     */
    function getCreateTaskUrl() {
        $calendarModuleModel = Vtiger_Module_Model::getInstance('Calendar');
        return $calendarModuleModel->getCreateTaskRecordUrl() . '&parent_id=' . $this->getId();
    }

    /**
     * 客户跟进后修改CRM表的修改时间,方便按修改时间排序
     * @author steel;
     * @param int $crmid
     */
    public static function updateAccountsStatus($crmid,$current_user_id ='') {
        global $current_user;
        $db = PearDatabase::getInstance();
        $datetime = date('Y-m-d H:i:s');
        if(!empty($current_user_id)){
            $current_user->id = $current_user_id;
        }

        $updateSq = "UPDATE vtiger_account SET vtiger_account.lastfollowuptime='{$datetime}',vtiger_account.followuptimes=vtiger_account.followuptimes+1";
//          return (array('assigned_user_id'=>$entity['assigned_user_id'],'current_user'=>$current_user->id,'accountrank'=>$entity['accountrank']));

        $updateSq .= " WHERE vtiger_account.accountid=?";
        $db->pquery($updateSq, array($crmid));
        $updateSql = "UPDATE vtiger_crmentity SET vtiger_crmentity.modifiedtime='{$datetime}'";

        $updateSql .= " WHERE  vtiger_crmentity.crmid=?";
        $db->pquery($updateSql, array($crmid));
        //steel 2015-06-3屏敝掉公海跟进后为变为自已的和跟进后变为正常的
        //self::getOvert($crmid);
        //客户模块加入拜访单是否24小时跟进
        //$endstarttime=date('Y-m-d H:i',time()+24*3600);
        $now = time();
        $datetime = date('Y-m-d H:i:s');
        $query = "UPDATE vtiger_visitingorder SET followstatus='followup',followtime=IFNULL(followtime,?),followid=IFNULL(followid,?),dayfollowup=(if((unix_timestamp(enddate)+24*3600)>=$now,'是',dayfollowup)) WHERE vtiger_visitingorder.modulestatus='c_complete' AND vtiger_visitingorder.related_to=? AND vtiger_visitingorder.extractid=? ORDER BY vtiger_visitingorder.enddate DESC limit 1";
        $db->pquery($query, array($datetime, $current_user->id, $crmid, $current_user->id));
    }

    /**
     * Function to check duplicate exists or not
     * @return <boolean>
     */
    public function checkDuplicate() {
        $record = $this->getId();
        $db = PearDatabase::getInstance();

        /* $query = "SELECT accountcategory,accountrank,vtiger_users.last_name,vtiger_departments.departmentname FROM vtiger_account
          INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid
          LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
          LEFT JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
          WHERE vtiger_crmentity.label= ? and  vtiger_crmentity.setype=? AND vtiger_crmentity.deleted =0 "; */
        $query = "SELECT accountcategory,accountrank,vtiger_users.last_name,vtiger_departments.departmentname FROM vtiger_account
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid
                        LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
                        LEFT JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                        LEFT JOIN vtiger_uniqueaccountname ON vtiger_account.accountid=vtiger_uniqueaccountname.accountid 
                        WHERE vtiger_uniqueaccountname.accountname=? AND vtiger_crmentity.deleted =0 ";

        if ($record > 0) {
            $query .= " AND vtiger_account.accountid!= {$record}";
        }
        //$label=preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;/u','',$this->getName());
        //$label=preg_replace("/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;|\,|\<|\.|\>|\/|\?|\;|\:|\'|\\\"|\\|\||\`|\~|\!|\@|\#|\\$|\\\|\%|\^|\&|\*|\(|\)|\-|\_|\=|\+|\，|\《|\。|\》|\、|\？|\；|\：|\‘|\“|\【|\｛|\】|\｝|\、|\||\·|\~|\！|\@|\#|\￥|\%|\…|\……|\&|\*|\（|\）|\-|\——|\=|\+|\，|\＜|\．|\＞|\？|\／|\；|\：|\＇|\＂|\［|\{|\］|\}|\＼|\｜|\｀|\￣|\！|\＠|\#|\＄|\％|\＾|\＆|\＊|\（|\）|\＿|\－|\＝|\＋|\，|\《|\。|\》|\、|\？|\；|\：|\’|\”|\【|\｛|\】|\｝|\＼|\｜|\·|\～|\！|\＠|\＃|\￥|\％|\……|\…|\＆|\×|\（|\）|\－|\—|\——|\＝|\＋/u",'',$this->getName());
        $label = str_replace('\\', '', $this->getName());
        //echo $label;
        $label = preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;|&quot;|　|&apos;|&amp;|&lt;|&gt;|&#039;|&ldquo;|&rdquo;|&lsquo;|&rsquo;|&hellip;|\“|\”|\‘|\〉|\〈|\’|\〖|\〗|\【|\】|\、|\·|\……|\…|\——|\＋|\－|\＝|\,|\<|\.|\>|\/|\?|\;|\:|\\\'|\"|\[|\{|\]|\}|\\|\||\`|\~|\!|\@|\#|\$|\%|\^|\\&|\*|\(|\)|\-|\_|\=|\+|\，|\＜|\．|\＞|\／|\？|\；|\：|\＇|\＂|\［|\{|\］|\}|\＼|\｜|\｀|\￣|\！|\＠|\#|\＄|\％|\＾|\＆|\＊|\（|\）|\－|\＿|\＝|\＋|\，|\《|\．|\》|\、|\？|\；|\：|\’|\”|\【|\｛|\】|\｝|\＼|\｜|\·|\～|\！|\＠|\＃|\￥|\％|\……|\…|\＆|\×|\（|\）|\－|\——|\—|\＝|\＋|\，|\《|\。|\》|\、|\？|\；|\：|\‘|\“|\【|\｛|\】|\｝|\、|\||\·|\~|\！|\@|\#|\￥|\%|\……|\…|\&|\*|\（|\）|\-|\——|\=|\+/u', '', $label);

        $label = strtoupper($label);
        //$result = $db->pquery($query, array($label,$this->getModule()->getName()));
        $result = $db->pquery($query, array($label));
        global $data; //声明一个全局变量在不改变原结构的情况下方便调用
        $data = $db->query_result_rowdata($result);
        if ($db->num_rows($result)) {
            return $data['accountcategory'] + 1;
        } else {
            return false;
        }
        /* $query = "SELECT crmid FROM vtiger_crmentity WHERE setype = ? AND crmid = ? AND deleted = 0";
          $params = array($this->getModule()->getName(), $this->getId());



          $result = $db->pquery($query, $params);
          if ($db->num_rows($result)) {
          if($params[0]=='Accounts'){
          $result=$db->query_result_rowdata($result);
          $query = "SELECT accountcategory,accountrank,vtiger_users.last_name,vtiger_departments.departmentname FROM vtiger_account
          INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid
          INNER JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
          INNER JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid INNER JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
          WHERE accountid= ? ";
          $params=array($result['crmid']);
          $record = $this->getId();
          if ($record) {
          $query .= " AND vtiger_crmentity.crmid != ?";
          array_push($params, $record);
          }
          $result = $db->pquery($query, $params);
          global $data;//声明一个全局变量在不改变原结构的情况下方便调用
          $data=$db->query_result_rowdata($result);
          if($result){
          return 1;
          }
          return false;
          }
          return false;
          }
          return false; */
    }

    /**
     * @function:公海客户跟进后变为跟进人的和保护模式变为正常
     * @functionName:getOvert
     * @author:steel
     * @time:2015-05-14 17:27
     * @param $accountid
     */
    public function getOvert($accountid) {
        $recordModel = Vtiger_Record_Model::getInstanceById($accountid, 'Accounts');
        $entity = $recordModel->entity->column_fields;

        if ($entity['accountcategory'] == 2) {
            global $current_user;
            $db = PearDatabase::getInstance();
            $datetime = date('Y-m-d H:i:s');
            $sql = "UPDATE vtiger_account,vtiger_crmentity SET vtiger_account.accountcategory=0,vtiger_crmentity.smownerid=?,vtiger_crmentity.modifiedtime=?,vtiger_crmentity.modifiedby=? WHERE vtiger_crmentity.crmid=vtiger_account.accountid AND vtiger_account.accountid=?";
            $db->pquery($sql, array($current_user->id, $datetime, $current_user->id, $accountid));
        }
    }

    /**
     * 合同添加后修改最后成交时间
     * @param unknown $accountid
     */
    public static function updateAccountsDealtime($accountid) {
        $db = PearDatabase::getInstance();
        $datetime = date('Y-m-d H:i:s');
        //$sql="SELECT sum(total) AS total FROM `vtiger_servicecontracts` WHERE modulestatus='c_complete'  AND sc_related_to=?";
        //根据回款的金额来升级客户
        $sql = "SELECT sum(ifnull(unit_price,0)) AS total FROM vtiger_receivedpayments LEFT JOIN `vtiger_servicecontracts` ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid WHERE  vtiger_receivedpayments.receivedstatus = 'normal' AND vtiger_servicecontracts.sc_related_to=?";
        $result = $db->pquery($sql, array($accountid));
        $resutlttotal = $db->query_result($result, 0, 'total');
        if ($resutlttotal > 0) {
            $updateSql = "UPDATE vtiger_account SET saleorderlastdealtime='{$datetime}'";
            if ($resutlttotal < 50000) {
                $updateSql .= ",accountrank='bras_isv' ";
            } elseif ($resutlttotal >= 50000 && $resutlttotal < 100000) {
                $updateSql .= ",accountrank='silv_isv' ";
            } elseif ($resutlttotal < 150000 && $resutlttotal >= 100000) {
                $updateSql .= ",accountrank='gold_isv' ";
            } else {
                $updateSql .= ",accountrank='visp_isv' ";
            }
            $updateSql .= " WHERE accountid=? ";
            $db->pquery($updateSql, array($accountid));
        }
    }

    /**
     * Function to get List of Fields which are related from Accounts to Inventory Record.
     * @return <array>
     */
    public function getInventoryMappingFields() {
        return array(
            //Billing Address Fields
            array('parentField' => 'bill_city', 'inventoryField' => 'bill_city', 'defaultValue' => ''),
            array('parentField' => 'bill_street', 'inventoryField' => 'bill_street', 'defaultValue' => ''),
            array('parentField' => 'bill_state', 'inventoryField' => 'bill_state', 'defaultValue' => ''),
            array('parentField' => 'bill_code', 'inventoryField' => 'bill_code', 'defaultValue' => ''),
            array('parentField' => 'bill_country', 'inventoryField' => 'bill_country', 'defaultValue' => ''),
            array('parentField' => 'bill_pobox', 'inventoryField' => 'bill_pobox', 'defaultValue' => ''),
            //Shipping Address Fields
            array('parentField' => 'ship_city', 'inventoryField' => 'ship_city', 'defaultValue' => ''),
            array('parentField' => 'ship_street', 'inventoryField' => 'ship_street', 'defaultValue' => ''),
            array('parentField' => 'ship_state', 'inventoryField' => 'ship_state', 'defaultValue' => ''),
            array('parentField' => 'ship_code', 'inventoryField' => 'ship_code', 'defaultValue' => ''),
            array('parentField' => 'ship_country', 'inventoryField' => 'ship_country', 'defaultValue' => ''),
            array('parentField' => 'ship_pobox', 'inventoryField' => 'ship_pobox', 'defaultValue' => '')
        );
    }

    //保护只修改protected，领取修改归属和状态
    public function updaterecord($update, $id, $owner = '',$normal=false) {
        $db = PearDatabase::getInstance();
        // 如果不是来自客户编辑 cxh 添加条件判断
        if($update[0]!='AccountEdit'){
            $query = 'update vtiger_account set ' . implode(",", $update) . ' WHERE accountid=' . $id;

            $db->pquery($query);
            $date = $db->formatDate(date('Y-m-d H:i:s'), true);
            $query = 'update vtiger_crmentity set modifiedtime=' . "'" . $date . "'" . ' where crmid=' . $id;
            $db->pquery($query);
        }


        if($normal){
            $datetime=date('Y-m-d H:i:s');
            //从其他区领取的
            $db->pquery('insert into vtiger_accountsfromtemporary(accountid,createdtime,smownerid) VALUES(?,?,?)',array($id,$datetime,$owner));

        }

        if (!empty($owner)) {
            $strBasic='';
            $str='';
            // cxh 添加是否是客户遍及 判断
            if($update[0]!='AccountEdit'){
                //cxh start
                $recordInfo=$db->pquery(" SELECT * FROM vtiger_crmentity WHERE  crmid=? ",array($id));
                $recordInfo=$db->query_result_rowdata($recordInfo,0);
                //cxh end
                //steel加入修改人
                $db->pquery('update vtiger_crmentity set smownerid=' . $owner . ',modifiedby=' . $owner . ' where crmid=' . $id);
                //cxh start
                $uniqueid = $db->getUniqueId('vtiger_modtracker_basic');
                $strBasic.="(".$uniqueid.",'".$id."','Accounts',".$owner.",'".date('Y-m-d H:i:s')."',0),";
                $str.="(".$uniqueid.",'".'assigned_user_id'."','".$recordInfo['smownerid']."','".$owner."'),";
                //cxh end
                $whodid=$owner;
                //如果是客户编辑划转进来的
            }else{
                $whodid=$update[1];
            }
            $result = $db->run_query_allrecords('SELECT vtiger_potential.potentialid,vtiger_crmentity.smownerid FROM vtiger_potential  
								LEFT JOIN vtiger_crmentity ON vtiger_potential.potentialid = vtiger_crmentity.crmid 
								LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id  
								WHERE vtiger_crmentity.deleted=0 AND vtiger_potential.potentialid > 0 and vtiger_potential.related_to = ' . $id);
            $innum = array();
            if (!empty($result)) {

                for ($i = 0; $i < count($result); $i++) {
                    $innum[] = $result[$i]['potentialid'];
                    //cxh start
                    $uniqueid = $db->getUniqueId('vtiger_modtracker_basic');
                    $strBasic.="(".$uniqueid.",'".$result[$i]['potentialid']."','Potentials',".$whodid.",'".date('Y-m-d H:i:s')."',0),";
                    $str.="(".$uniqueid.",'".'assigned_user_id'."','".$result[$i]['smownerid']."','".$owner."'),";
                    //cxh end
                }
            }

            $result1 = $db->run_query_allrecords('SELECT vtiger_contactdetails.contactid,vtiger_crmentity.smownerid FROM vtiger_contactdetails  
												LEFT JOIN vtiger_crmentity ON vtiger_contactdetails.contactid = vtiger_crmentity.crmid 
												LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id  
												WHERE vtiger_crmentity.deleted=0 AND vtiger_contactdetails.contactid > 0 AND vtiger_contactdetails.accountid=' . $id);
            if (!empty($result1)) {
                for ($i = 0; $i < count($result1); $i++) {
                    $innum[] = $result1[$i]['contactid'];
                    //cxh start
                    $uniqueid = $db->getUniqueId('vtiger_modtracker_basic');
                    $strBasic.="(".$uniqueid.",'".$result1[$i]['contactid']."','Contacts',".$whodid.",'".date('Y-m-d H:i:s')."',0),";
                    $str.="(".$uniqueid.",'".'assigned_user_id'."','".$result1[$i]['smownerid']."','".$owner."'),";
                    //cxh end
                }
            }

            $result2 = $db->run_query_allrecords('SELECT  vtiger_quotes.quoteid,vtiger_crmentity.smownerid
												FROM vtiger_quotes  
												LEFT JOIN vtiger_crmentity ON vtiger_quotes.quoteid = vtiger_crmentity.crmid 
												LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id  
												WHERE vtiger_crmentity.deleted=0 AND vtiger_quotes.quoteid > 0 AND vtiger_quotes.accountid=' . $id);

            if (!empty($result2)) {

                for ($i = 0; $i < count($result2); $i++) {
                    $innum[] = $result2[$i]['quoteid'];
                    //cxh start
                    $uniqueid = $db->getUniqueId('vtiger_modtracker_basic');
                    $strBasic.="(".$uniqueid.",'".$result2[$i]['quoteid']."','Quotes',".$whodid.",'".date('Y-m-d H:i:s')."',0),";
                    $str.="(".$uniqueid.",'".'assigned_user_id'."','".$result2[$i]['smownerid']."','".$owner."'),";
                    //cxh end
                }
            }

            if (!empty($innum)) {
                $string = implode(',', $innum);
                $db->pquery('update vtiger_crmentity set smownerid=' . $owner . ' where crmid in(' . $string . ')');
            }
            //cxh start
            $strBasic=trim($strBasic,",");
            $str=trim($str,",");
            if($strBasic && $str){
                $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES'.$strBasic,array());
                /*foreach ($params['strArray'] as $key=>$value){
                    $str.="(".$id.",'".$value['fieldname']."','".$value['prevalue']."','".$value['postvalue']."'),";
                }*/
                $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES'.$str, Array());
            }
            //cxh end
        }
    }

    /*     * 获取已领取的某等级的客户数量
     * @param $array @array[0]:客户等级,array[1],客户状态(0,1),当前客户的负责人
     * @return mixed当前负责人已有的客户数
     */

    public function getRankCounts($array) {
        $db = PearDatabase::getInstance();
        //2016-02-23steel修改  cxh   不做累加了
        /*$temparr = array('chan_notv', 'forp_notv', 'eigp_notv', 'sixp_notv');
        if (in_array($array[0], $temparr)) {
            $query = "select vtiger_crmentity.crmid from vtiger_crmentity LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_crmentity.crmid WHERE vtiger_account.protected=0 and vtiger_account.accountrank IN('chan_notv','forp_notv','eigp_notv','sixp_notv') and vtiger_account.accountcategory=? and vtiger_crmentity.deleted=0  and vtiger_crmentity.smownerid=?";
            $resault = $db->pquery($query, array($array[1], $array[2]));
            return $db->num_rows($resault);
        }*/
        //2016-02-23steel修改
        $query = 'select vtiger_crmentity.crmid from vtiger_crmentity LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_crmentity.crmid WHERE vtiger_account.protected=0 and vtiger_account.accountrank=? and vtiger_account.accountcategory=? and vtiger_crmentity.deleted=0  and vtiger_crmentity.smownerid=?';
        $resault = $db->pquery($query, $array);
        return $db->num_rows($resault);
    }

    //根据等级获取保护客户天数
    public function getRankDays($array) {
        global $current_user;
        $db = PearDatabase::getInstance();

        $departmentid=$current_user->departmentid;
        $user_entered=$current_user->user_entered;

        // 移动端回传四个参数
        if($array[2]){
            $departmentid=$array[2];
            $user_entered=$array[3];
        }
        //查询部门 信息
        $departmentInfo=$db->pquery("SELECT d.* FROM   vtiger_departments as d  WHERE d.departmentid=? LIMIT 1 ",array($departmentid));
        $departmentInfo=$db->query_result_rowdata($departmentInfo,0);
        $departmentArray=explode("::",$departmentInfo['parentdepartment']);
        $departmentArray=array_reverse($departmentArray);
        // 获取员工当前员工阶段
        $staff_stage=$this->getStaffStage($user_entered);
        foreach ($departmentArray as $key=>$val){
                //先查询该 部门该员工阶段的该员工商务等级的是否存在 存在则继续 不存在则  按照不需要员工阶段条件的查询
                $query = 'select protectnum,protectday,isupdate,followday,isfollow from vtiger_rankprotect WHERE  department=? AND staff_stage=? AND performancerank=? AND accountrank=? limit 1 ';
                $result = $db->pquery($query, array($val,$staff_stage,$array[0],$array[1]));
                $noOfresult = $db->num_rows($result);
                if($noOfresult>0){// 如果存在继续走

                }else{//如果不存在则查询不包含员工阶段的查询 保护数
                    $query = 'select protectnum,protectday,isupdate,followday,isfollow from vtiger_rankprotect WHERE  department=?  AND performancerank=?  AND accountrank=? AND  staff_stage=0 limit 1';
                    $result = $db->pquery($query, array($val,$array[0],$array[1]));
                    $noOfresult = $db->num_rows($result);
                    if ($noOfresult>0){
                    }else{
                        continue;
                    }
                }
                return @$db->query_result_rowdata($result);
        }
    }

    //获取商务等级[默认初级]
    public function getSaleRank($uid) {
        $db = PearDatabase::getInstance();
        $query = 'select performancerank  from vtiger_salemanager WHERE relatetoid=? limit 1';
        $resault = $db->pquery($query, array($uid));

        if ($db->num_rows($resault) != 1) {
            return 'juniorB';
        }

        $resault = $db->query_result_rowdata($resault);
        return $resault['performancerank'];
    }

    //查询用户可领取客户
    public function getRankLimit() {
        global $current_user;
        $db = PearDatabase::getInstance();
        $query = 'select accountrank,count(*) as c from vtiger_crmentity LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_crmentity.crmid WHERE vtiger_account.protected=0 and vtiger_account.accountcategory=0 and vtiger_crmentity.deleted=0  and vtiger_crmentity.smownerid=? group by accountrank';
        $result = $db->pquery($query, array($current_user->id));

        $noOfresult = $db->num_rows($result);
        $array = array();
        for ($i = 0; $i < $noOfresult; ++$i) {
            $num = $db->fetchByAssoc($result);
            $array[$num['accountrank']] = $num['c'];
        }
        //机会客户+40%意向客户之为10个 2016-02-24新规则;
        if (!empty($array)) {
            $tempnum = $array['chan_notv'] + $array['forp_notv'] + $array['eigp_notv'] + $array['sixp_notv'];
            $array['chan_notv'] = $tempnum;
            $array['forp_notv'] = $tempnum;
            $array['eigp_notv'] = $tempnum;
            $array['sixp_notv'] = $tempnum;
        }

        //获取用户等级[商务默认初级]
        /* $query = 'select performancerank from vtiger_salemanager WHERE relatetoid=? limit 1';
          $result=$db->pquery($query,array($current_user->id));
          $noOfresult = $db->num_rows($result);
          if($noOfresult!=1){
          $performancerank='juniorB';
          }else{
          $info=$db->query_result_rowdata($result);
          $performancerank=$info['performancerank'];
          } */
        $performancerank = $this->getSaleRank($current_user->id);

        $query = 'select accountrank,protectnum from vtiger_rankprotect WHERE performancerank=?';
        $result = $db->pquery($query, array($performancerank));
        $noOfresult = $db->num_rows($result);
        $residue = array();


        for ($i = 0; $i < $noOfresult; ++$i) {
            $num = $db->fetchByAssoc($result);
            //总数保护数量减去已有保护数量获取剩余保护数量
            $residue[$num['accountrank']] = empty($array[$num['accountrank']]) ? $num['protectnum'] : $num['protectnum'] - $array[$num['accountrank']];
        }
        //编辑模式下当前等级数量加一
        if ($this->getId()) {
            $info = $this->getEntity()->column_fields;
            $residue[$info['accountrank']] += 1;
        }
        return $residue;
    }

    /**
     * @author: steel
     * @time :2015-02-13 获当前客户对应的客服
     * @param int $parentRecordId
     * @param unknown $pagingModel
     * @return multitype:Ambigous <unknown, multitype:, s, --, string, mixed>
     */
    static function getservicecomments($parentRecordId, $pagingModel) {
        $db = PearDatabase::getInstance();
        $recordInstances = array();

        //$startIndex = $pagingModel->getStartIndex();
        //$pageLimit = $pagingModel->getPageLimit();

        /* $listQuery = "SELECT vtiger_servicecomments.*,vtiger_users.last_name FROM vtiger_servicecomments
          INNER JOIN vtiger_users ON vtiger_users.id=vtiger_servicecomments.serviceid WHERE vtiger_servicecomments.assigntype='accountby' AND related_to = ?
          ORDER BY endtime desc"; */
        $listQuery = "SELECT '' AS remark,vtiger_users.last_name FROM vtiger_account 
				INNER JOIN vtiger_users ON vtiger_users.id=vtiger_account.serviceid WHERE accountid = ? 
				LIMIT 1";
        $result = $db->pquery($listQuery, array($parentRecordId));
        $rows = $db->num_rows($result);

        for ($i = 0; $i < $rows; $i++) {
            $row = $db->query_result_rowdata($result, $i);
            //$recordInstance = new self();
            //$recordInstance->setData($row)->setParent($row['crmid'], $row['module']);
            $recordInstances[] = $row;
        }
        return $recordInstances;
    }

    /**
     * @author: steel
     * @time :2015-11-04 获当前客户对应的客服信息
     * @param int $parentRecordId
     * @param unknown $pagingModel
     * @return multitype:Ambigous <unknown, multitype:, s, --, string, mixed>
     */
    static function getservicecommentsandsmower($parentRecordId, $pagingModel) {
        $db = PearDatabase::getInstance();
        $recordInstances = array();
        /* $listQuery = "SELECT vtiger_users.last_name,vtiger_users.phone_work,vtiger_users.phone_mobile,vtiger_users.email1  FROM vtiger_servicecomments
          LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_servicecomments.serviceid WHERE vtiger_servicecomments.assigntype='accountby' AND related_to = ?
          ORDER BY endtime desc limit 1"; */
        $listQuery = "SELECT vtiger_users.last_name,vtiger_users.phone_work,vtiger_users.phone_mobile,vtiger_users.email1  FROM vtiger_account
				LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_account.serviceid WHERE  accountid = ?
				limit 1";
        $result = $db->pquery($listQuery, array($parentRecordId));
        $rows = $db->num_rows($result);
        if ($rows > 0) {
            //客服信息
            $recordInstances['f'] = $db->query_result_rowdata($result, 0);
        }
        $listQuery = "SELECT vtiger_users.last_name,vtiger_users.phone_work,vtiger_users.phone_mobile,vtiger_users.email1  FROM vtiger_account
                    LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid
                    LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
                    WHERE vtiger_account.accountid=?";
        $result = $db->pquery($listQuery, array($parentRecordId));
        $rows = $db->num_rows($result);
        if ($rows > 0) {
            //客户负责人信息
            $recordInstances['h'] = $db->query_result_rowdata($result, 0);
        }
        return $recordInstances;
    }

    /**
     * @author steel 2015-02-13
     * @deprecated 取得当前客户负责人的变历史记录
     * @param unknown $parentRecordId
     * @param unknown $pagingModel
     * @return multitype:Ambigous <unknown, multitype:, s, --, string, mixed>
     */
    static function getheads($parentRecordId, $pagingModel) {
        $db = PearDatabase::getInstance();
        $recordInstances = array();

        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();

        /* $listQuery = "SELECT vtiger_accountsmowneridhistory.*,o.last_name AS oldname,n.last_name AS newname,m.last_name AS mname FROM vtiger_accountsmowneridhistory
          LEFT JOIN vtiger_users as o ON o.id=vtiger_accountsmowneridhistory.oldsmownerid
          LEFT JOIN vtiger_users as n ON n.id=vtiger_accountsmowneridhistory.newsmownerid
          LEFT JOIN vtiger_users as m ON m.id=vtiger_accountsmowneridhistory.modifiedby
          WHERE vtiger_accountsmowneridhistory.accountid=? ORDER BY vtiger_accountsmowneridhistory.id DESC LIMIT 5"; */
        $listQuery = "SELECT vtiger_accountsmowneridhistory.*,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountsmowneridhistory.oldsmownerid LIMIT 1) AS oldname,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountsmowneridhistory.newsmownerid LIMIT 1) AS newname,(SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_accountsmowneridhistory.modifiedby LIMIT 1) AS mname FROM vtiger_accountsmowneridhistory
				WHERE vtiger_accountsmowneridhistory.accountid=? ORDER BY vtiger_accountsmowneridhistory.id DESC LIMIT 5";
        $result = $db->pquery($listQuery, array($parentRecordId));
        $rows = $db->num_rows($result);

        for ($i = 0; $i < $rows; $i++) {
            $row = $db->query_result_rowdata($result, $i);
            //$recordInstance = new self();
            //$recordInstance->setData($row)->setParent($row['crmid'], $row['module']);
            $recordInstances[] = $row;
        }
        return $recordInstances;
    }

    /**
     * 取得联系人表里的联系人信息显不在摘要页上
     * @author steel 2015-03-05
     * @param unknown $contactsid
     * @return Ambigous <unknown, multitype:, s, --, string, mixed>
     */
    static public function getContactsToIndex($contactsid) {
        $db = PearDatabase::getInstance();
        $query = "SELECT vtiger_contactdetails.contactid as crmid,vtiger_contactdetails.*,
            (select vtiger_crmentity.smownerid from vtiger_crmentity where vtiger_crmentity.crmid = vtiger_contactdetails.contactid) as smownerid,
			(select accountname from vtiger_account where vtiger_account.accountid = vtiger_contactdetails.accountid) as accountname, 
			(select last_name from vtiger_users where vtiger_users.id = smownerid) as user_name 
			FROM vtiger_contactdetails 
			WHERE EXISTS(select crmid from vtiger_crmentity where vtiger_crmentity.crmid = vtiger_contactdetails.contactid and vtiger_crmentity.deleted = 0) 
			AND vtiger_contactdetails.accountid = {$contactsid} LIMIT 5";
        $result = $db->pquery($query);
        $rows = $db->num_rows($result);

        for ($i = 0; $i < $rows; $i++) {
            $row = $db->query_result_rowdata($result, $i);
            $recordInstances[] = $row;
        }
        return $recordInstances;
    }

    static public function getContactsrelation($contactsid) {
        $db = PearDatabase::getInstance();
        $query = "SELECT vtiger_contactdetails.contactid as crmid,vtiger_contactdetails.*,
            (select vtiger_crmentity.smownerid from vtiger_crmentity where vtiger_crmentity.crmid = vtiger_contactdetails.contactid) as smownerid,
			(select accountname from vtiger_account where vtiger_account.accountid = vtiger_contactdetails.accountid) as accountname,
			(select last_name from vtiger_users where vtiger_users.id = smownerid) as user_name
			FROM vtiger_contactdetails
			WHERE EXISTS(select crmid from vtiger_crmentity where vtiger_crmentity.crmid = vtiger_contactdetails.contactid and vtiger_crmentity.deleted = 0)
			AND vtiger_contactdetails.accountid = {$contactsid} LIMIT 5";
        $result = $db->pquery($query);
        $rows = $db->num_rows($result);

        for ($i = 0; $i < $rows; $i++) {
            $row = $db->fetchByAssoc($result);
            $recordInstances[] = $row;
        }
        return $recordInstances;
    }

//临时的标记
    //打标记
    public function getsignflag($array) {
        $db = PearDatabase::getInstance();
        //2016-02-23steel修改
        $query = "select vtiger_crmentity.crmid from vtiger_crmentity LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_crmentity.crmid WHERE vtiger_account.protected=0 and vtiger_account.accountrank IN('chan_notv','forp_notv','eigp_notv','sixp_notv') and vtiger_account.accountcategory=0 and vtiger_account.sign=1 and vtiger_crmentity.deleted=0  and vtiger_crmentity.smownerid=?";
        $resault = $db->pquery($query, $array);
        return $db->num_rows($resault);
    }
    public function getMonthNum($date1,$date2){
        if(strtotime($date1)>strtotime($date2)){
            $tmp=$date2;
            $date2=$date1;
            $date1=$tmp;
        }
        list($Y1,$m1,$d1)=explode('-',$date1);
        list($Y2,$m2,$d2)=explode('-',$date2);
        $Y=$Y2-$Y1;
        $m=$m2-$m1;
        $d=$d2-$d1;
        if($d<0){
            $d+=(int)date('t',strtotime("-1 month $date2"));
            $m--;
        }
        if($m<0){
            $m+=12;
            $Y--;
        }
        return $Y*12+$m+($d>0?1:0);
    }
    public function getStaffStage($user_entered){
        $entered=explode('-',$user_entered);
        if($entered[2]>15){
            $entered[1]=$entered[1]+1;
            if($entered[1]<13){
                $enteredday=$entered[0].'-'.$entered[1].'-01';
            }else{
                $enteredday=($entered[0]+1).'-01-01';
            }
        }else{
            $enteredday=$entered[0].'-'.$entered[1].'-01';;
        }
        $currentdate=date("Y-m-d");
        $currentDiffMonth=$this->getMonthNum($enteredday,$currentdate);
        $staff_stage=($currentDiffMonth>=0?
            ($currentDiffMonth>1?
                ($currentDiffMonth>3?
                    ($currentDiffMonth>6?
                        ($currentDiffMonth>12?5:4)
                        :3)
                    :2)
                :1)
            :5);
        return $staff_stage;
    }
    //查询用户可领取客户
    public function getRankLimitm($userid) {
        global $current_user;
        $db = PearDatabase::getInstance();
        $query = 'select accountrank,count(*) as c from vtiger_crmentity LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_crmentity.crmid WHERE vtiger_account.protected=0 and vtiger_account.accountcategory=0 and vtiger_crmentity.deleted=0  and vtiger_crmentity.smownerid=? group by accountrank';
        $result = $db->pquery($query, array($userid));

        $noOfresult = $db->num_rows($result);
        $array = array();
        for ($i = 0; $i < $noOfresult; ++$i) {
            $num = $db->fetchByAssoc($result);
            $array[$num['accountrank']] = $num['c'];
        }
        $performancerank = $this->getSaleRank($userid);
        // 查询部门id
        $departmentInfo=$db->pquery("SELECT d.* FROM   vtiger_user2department as d  WHERE d.userid=? LIMIT 1 ",array($userid));
        $departmentInfo=$db->query_result_rowdata($departmentInfo,0);
        $departmentid=$departmentInfo['departmentid'];

        $userInfo=$db->pquery("SELECT d.user_entered FROM   vtiger_users as d  WHERE d.id=? LIMIT 1 ",array($userid));
        $userInfo=$db->query_result_rowdata($userInfo,0);
        $user_entered=$userInfo['user_entered'];
        //查询部门 信息
        $departmentInfo=$db->pquery("SELECT d.* FROM   vtiger_departments as d  WHERE d.departmentid=? LIMIT 1 ",array($departmentid));
        $departmentInfo=$db->query_result_rowdata($departmentInfo,0);
        $departmentArray=explode("::",$departmentInfo['parentdepartment']);
        $departmentArray=array_reverse($departmentArray);
        // 获取员工当前员工阶段
        $staff_stage=$this->getStaffStage($user_entered);
        foreach ($departmentArray as $key=>$val){
            // 先查询该 部门该员工阶段的该员工商务等级的是否存在 存在则继续 不存在则  按照不需要员工阶段条件的查询
            $query = 'select accountrank,protectnum from vtiger_rankprotect WHERE  department=? AND staff_stage=? AND performancerank=? AND accountrank=? ';
            $result = $db->pquery($query, array($val,$staff_stage,$performancerank,$_REQUEST['ranks']));
            $noOfresult = $db->num_rows($result);
            if($noOfresult>0){// 如果存在继续走

            }else{//如果不存在则查询不包含员工阶段的查询 保护数
                $query = 'select accountrank,protectnum from vtiger_rankprotect WHERE  department=?  AND performancerank=? AND staff_stage=0 AND  accountrank=? ';
                $result = $db->pquery($query, array($val,$performancerank,$_REQUEST['ranks']));
                $noOfresult = $db->num_rows($result);
                if ($noOfresult>0){
                }else{
                   continue;
                }
            }
            $residue = array();
            //保留所有等级的保护数量一并返回。
            $residue['rankProtectNum']=[];
            $residue['havingRankProtectNum']=[];
            for ($i = 0; $i < $noOfresult; ++$i) {
                $num = $db->fetchByAssoc($result);
                //总数保护数量
                $residue['rankProtectNum'][$num['accountrank']]=$num['protectnum'];
                //已有保护数量
                $residue['havingRankProtectNum'][$num['accountrank']]=$array[$num['accountrank']];
            }
            // 获取 剩余保护数量
            foreach ($residue['rankProtectNum'] as $key=>$val){
                //总数保护数量减去已有保护数量获取剩余保护数量
                $residue[$key] =$val-$array[$key];
            }
            //编辑模式下当前等级数量加一
            if ($this->getId()) {
                $info = $this->getEntity()->column_fields;
                if ($info['assigned_user_id'] == $userid) {
                    $residue[$info['accountrank']] += 1;
                }
            }
            return $residue;
        }
    }
    /////临时处理


    static public function getReport() {
        global $current_user;
        $where = getAccessibleUsers();
        $datetime = date("Y-m-d");
        $sql = "SELECT count(accountid) as counts,(select last_name from vtiger_users where id=vtiger_crmentity.smownerid) last_name,
        vtiger_crmentity.smownerid
		,sum(IF(accountrank='chan_notv',1,0)) as chan_notv
		,sum(IF(accountrank='forp_notv',1,0)) as forp_notv
		,sum(IF(accountrank='norm_isv',1,0)) as norm_isv
		,sum(IF(accountrank='spec_isv',1,0)) as spec_isv
		,sum(IF(accountrank='eigp_notv',1,0)) as eigp_notv
		,sum(IF(accountrank='sixp_notv',1,0)) as sixp_notv
		,sum(IF(accountrank='visp_isv',1,0)) as visp_isv
		,sum(IF(accountrank='wlad_isv',1,0)) as wlad_isv
		,sum(IF(accountrank='wlvp_isv',1,0)) as wlvp_isv
		,sum(IF(accountrank='wlbr_isv',1,0)) as wlbr_isv
		,sum(IF(accountrank='wlsi_isv',1,0)) as wlsi_isv
		,sum(IF(accountrank='wlgo_isv',1,0)) as wlgo_isv
		,sum(IF(accountrank='iron_isv',1,0)) as iron_isv
		,sum(IF(accountrank='bras_isv',1,0)) as bras_isv
		,sum(IF(accountrank='silv_isv',1,0)) as silv_isv
		,sum(IF(accountrank='gold_isv',1,0)) as gold_isv
		
		,sum(IF(accountrank='chan_notv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as daychan_notv
		,sum(IF(accountrank='forp_notv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as dayforp_notv
		,sum(IF(accountrank='norm_isv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as daynorm_isv
		,sum(IF(accountrank='spec_isv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as dayspec_isv
		,sum(IF(accountrank='eigp_notv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as dayeigp_notv
		,sum(IF(accountrank='sixp_notv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as daysixp_notv
		,sum(IF(accountrank='visp_isv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as dayvisp_isv
		,sum(IF(accountrank='wlad_isv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as daywlad_isv
		,sum(IF(accountrank='wlvp_isv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as daywlvp_isv
		,sum(IF(accountrank='wlbr_isv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as daywlbr_isv
		,sum(IF(accountrank='wlsi_isv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as daywlsi_isv
		,sum(IF(accountrank='wlgo_isv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as daywlgo_isv
		,sum(IF(accountrank='iron_isv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as dayiron_isv
		,sum(IF(accountrank='bras_isv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as daybras_isv
		,sum(IF(accountrank='silv_isv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as daysilv_isv
		,sum(IF(accountrank='gold_isv'&&TO_DAYS(createdtime)=TO_DAYS('{$datetime}'),1,0)) as daygold_isv
		
		,sum(IF(accountrank='chan_notv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekchan_notv
		,sum(IF(accountrank='forp_notv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekforp_notv
		,sum(IF(accountrank='norm_isv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weeknorm_isv
		,sum(IF(accountrank='spec_isv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekspec_isv
		,sum(IF(accountrank='eigp_notv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekeigp_notv
		,sum(IF(accountrank='sixp_notv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weeksixp_notv
		,sum(IF(accountrank='visp_isv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekvisp_isv
		,sum(IF(accountrank='wlad_isv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekwlad_isv
		,sum(IF(accountrank='wlvp_isv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekwlvp_isv
		,sum(IF(accountrank='wlbr_isv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekwlbr_isv
		,sum(IF(accountrank='wlsi_isv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekwlsi_isv
		,sum(IF(accountrank='wlgo_isv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekwlgo_isv
		,sum(IF(accountrank='iron_isv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekiron_isv
		,sum(IF(accountrank='bras_isv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekbras_isv
		,sum(IF(accountrank='silv_isv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weeksilv_isv
		,sum(IF(accountrank='gold_isv'&&yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}'),1,0)) as weekgold_isv
		
		,sum(IF(accountrank='chan_notv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthchan_notv
		,sum(IF(accountrank='forp_notv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthforp_notv
		,sum(IF(accountrank='norm_isv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthnorm_isv
		,sum(IF(accountrank='spec_isv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthspec_isv
		,sum(IF(accountrank='eigp_notv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as montheigp_notv
		,sum(IF(accountrank='sixp_notv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthsixp_notv
		,sum(IF(accountrank='visp_isv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthvisp_isv
		,sum(IF(accountrank='wlad_isv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthwlad_isv
		,sum(IF(accountrank='wlvp_isv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthwlvp_isv
		,sum(IF(accountrank='wlbr_isv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthwlbr_isv
		,sum(IF(accountrank='wlsi_isv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthwlsi_isv
		,sum(IF(accountrank='wlgo_isv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthwlgo_isv
		,sum(IF(accountrank='iron_isv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthiron_isv
		,sum(IF(accountrank='bras_isv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthbras_isv
		,sum(IF(accountrank='silv_isv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthsilv_isv
		,sum(IF(accountrank='gold_isv'&&date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m'),1,0)) as monthgold_isv
		
		FROM vtiger_account
		LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid
		LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
		WHERE ";
        if ($where != '1=1') {
            $sql .= " smownerid {$where} AND ";
        }
        $sql .= " vtiger_crmentity.deleted=0 AND vtiger_account.accountcategory=0  AND vtiger_users.`status` = 'Active' GROUP BY smownerid";

        $db = PearDatabase::getInstance();
        $result = $db->pquery($sql, array());
        $rows = $db->num_rows($result);
        $recordInstances = array();
        for ($i = 0; $i < $rows; $i++) {
            $row = $db->query_result_rowdata($result, $i);
            $recordInstances[] = $row;
        }

        return $recordInstances;
    }

    /**
     * 求对应跟进记录的总条数
     * @param $id客户的ID
     * @return mixed|string
     * @throws Exception
     */
    static public function getModcommentCount($id) {
        $accountRecordModel = Accounts_Record_Model::getInstanceById($id,"Accounts");
        $smownerdepartment = $accountRecordModel->getSmownerDepartmentId($id);
        $userRecordModel = Users_Record_Model::getCleanInstance("Users");
        $departmentid = $smownerdepartment['departmentid'];
        $smownerid = $smownerdepartment['smownerid'];

        $db = Peardatabase::getInstance();
        $query = "SELECT count(1) AS counts FROM `vtiger_modcomments` WHERE related_to =? AND modulename='Accounts'";
        if($userRecordModel->isChannelUser($departmentid)){
            $accountMoudle = Accounts_Module_Model::getCleanInstance("Accounts");
            $shareAccountUserIds = $accountMoudle->getShareAccountUserIds($id);
            global $current_user;
            if($current_user->id==$smownerid){
                if(!empty($shareAccountUserIds)){
                    $query .=" and creatorid not in  (".implode(",",$shareAccountUserIds).")";
                }
            }elseif(in_array($current_user->id,$shareAccountUserIds)){
                $query .= "   and creatorid=".$current_user->id;
            }else{
                $users = array_keys($userRecordModel->getAccessibleUsers());
                array_push($users,$current_user->id);
                $query .=  "   and creatorid in (".implode(",",$users).") ";
            }
        }

        $result = $db->pquery($query, array($id));
        return $db->query_result($result, 0, 'counts');
    }

    /**
     * 当前登陆的用户是否有修改客户名称客户等级权限
     * @param $filed
     * @return bool
     */
    public static function getsupperaccountupdate($filed) {
        $recordModel=Vtiger_Record_Model::getCleanInstance('Accounts');
        return $recordModel->personalAuthority('Accounts',$filed);
        /*$db = PearDatabase::getInstance();
        global $current_user;
        $query = "SELECT 1 FROM vtiger_supperaccountupdate WHERE deleted=0 and userid=? and field=?";
        $result = $db->pquery($query, array($current_user->id, $filed));
        $num = $db->num_rows($result);
        if ($num > 0) {
            return true;
        }
        return false;*/
    }

    /*
      垫款的加减
     */

    public function setAdvancesmoney($id, $value, $msg) {
        $db = PearDatabase::getInstance();
        global $current_user;

        $sql = "SELECT advancesmoney FROM vtiger_account WHERE accountid=? LIMIT 1";
        $sel_result = $db->pquery($sql, array($id));
        $res_cnt = $db->num_rows($sel_result);
        if ($res_cnt > 0) {
            $row = $db->query_result_rowdata($sel_result, 0);

            $sql = "UPDATE vtiger_account SET advancesmoney=advancesmoney+{$value} WHERE accountid=? LIMIT 1";
            $db->pquery($sql, array($id));

            // 做更新记录
            $did = $db->getUniqueId('vtiger_modtracker_basic');
            $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)', array($did, $id, 'Accounts', $current_user->id, date('Y-m-d H:i:s'), 0));
            $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)', Array($did, 'advancesmoney', $row['advancesmoney'], ($row['advancesmoney'] + $value) . $msg));
        }
    }

    /**
     * 客服跟进中移过来的
     * Function returns latest comments for parent record
     * @param <Integer> $parentRecordId - parent record for which latest comment need to retrieved
     * @param <Vtiger_Paging_Model> - paging model
     * @return ModComments_Record_Model if exits or null
     */
    public function getRecentComments($parentRecordId, $pagingModel, $moduleName = '') {
        $db = PearDatabase::getInstance();
        $startIndex = $pagingModel->getStartIndex();
        $limit = $pagingModel->getPageLimit();

        $query = "SELECT vtiger_modcomments.commentreturnplanid,vtiger_modcomments.commentcontent, vtiger_modcomments.addtime,
				vtiger_modcomments.related_to, vtiger_modcomments.creatorid, vtiger_modcomments.modcommenttype, 
				vtiger_modcomments.modcommentmode, vtiger_modcomments.modcommenthistory, vtiger_modcomments.modcommentpurpose,
				vtiger_modcomments.modcommentsid,vtiger_modcomments.contact_id,IFNULL((select name from vtiger_contactdetails where contactid=vtiger_modcomments.contact_id),IFNULL((select linkname from vtiger_account where accountid=vtiger_modcomments.related_to ),'-')) as lastname,
				IFNULL((select linkname from vtiger_account where accountid=vtiger_modcomments.related_to ),'-') as shouyao ,
                vtiger_modcomments.accountintentionality
				FROM vtiger_modcomments WHERE  ";

        //客户判断

        $where = getAccessibleUsers('Accounts', 'List', true);
        if ($where != '1=1') {//如果不是管理员走这里
            $recordModule = self::getInstanceById($parentRecordId);
            $column_fields = $recordModule->getEntity()->column_fields; //找到该记录对应的客户信息
            $shareAccountQuery = 'SELECT 1 FROM vtiger_shareaccount WHERE sharestatus=1 AND accountid=? AND userid in(' . implode(',', $where) . ')';
            $shareAccountResult = $db->pquery($shareAccountQuery, array($parentRecordId));
            $realoperate = setoperate($parentRecordId, 'Accounts');
            if (in_array($column_fields['assigned_user_id'], $where) || !$db->num_rows($shareAccountResult) || $realoperate == $_REQUEST['realoperate']) {
                //当前登录人的的权限包含客户负责人或登录没有对应的客户共享商务,或其它模块跳过来的
                //则说明当前登录人可能是客服,商务自已或是商务上级,共享部门带过来的可以查看客户的权限.那么该当前登录人可以查看所有的跟进信息
                $query = $query . "  vtiger_modcomments.related_to = ? ORDER BY modcommentsid DESC LIMIT $startIndex, $limit";
            } else {
                //是共享商务或是共享商务的上级
                //可以查看自已的或是自已下级的跟进
                $query = $query . "  vtiger_modcomments.related_to = ?  AND vtiger_modcomments.creatorid IN(" . implode(',', $where) . ") ORDER BY modcommentsid DESC LIMIT $startIndex, $limit";
            }
        } else {
            $query = $query . "  vtiger_modcomments.related_to = ?  ORDER BY modcommentsid DESC
			LIMIT $startIndex, $limit";
        }
        $result = $db->pquery($query, array($parentRecordId));
        $rows = $db->num_rows($result);


        $recordIds = '';
        for ($i = 0; $i < $rows; $i++) {
            if ($i == 0) {
                $recordIds = $db->query_result($result, $i, 'modcommentsid');
            } else {
                $recordIds = $recordIds . ',' . $db->query_result($result, $i, 'modcommentsid');
            }
        }

        //跟进提醒修改 2014-12-22/gaocl start
        //获取跟进提醒数据
        $alertModcomments = ModComments_Record_Model::getAlertModcomments($recordIds);
        //跟进提醒修改 2014-12-22/gaocl end
        //批量获取评论，提醒数据
        $subcomments = ModComments_Record_Model::getSubModcomments($recordIds);
        //print_r($alertModcomments);die();
        //加入
        for ($i = 0; $i < $rows; $i++) {
            $row = $db->query_result_rowdata($result, $i);
            $recordInstance = new ModComments_Record_Model();

            $recordInstance->setData($row);
            //跟进提醒修改 2014-12-22/gaocl start
            $recordInstance->setAlerts(empty($alertModcomments[$row['modcommentsid']]) ? array() : $alertModcomments[$row['modcommentsid']]);

            //跟进提醒修改 2014-12-22/gaocl end
            $recordInstance->setHistory(empty($subcomments[$row['modcommentsid']]) ? array() : $subcomments[$row['modcommentsid']]);
            $recordInstances[] = $recordInstance;
        }

        return $recordInstances;
    }

    //获取掉入公海的天数(客户从临时区掉公海后一段时间（5天）内不允许领取(主要是防止商务频繁给客户打电话)) gaocl add 2018/02/28
    public function getFallToovertDays($id) {
        $db = PearDatabase::getInstance();
        $sql = "SELECT fall_toovert_time FROM vtiger_account WHERE accountid=? LIMIT 1";
        $sel_result = $db->pquery($sql, array($id));
        $res_cnt = $db->num_rows($sel_result);
        if ($res_cnt > 0) {
            $row = $db->query_result_rowdata($sel_result, 0);
            if (!empty($row['fall_toovert_time'])) {
                $fall_toovert_time = strtotime(date("Y-m-d", strtotime($row['fall_toovert_time'])));
                $cur_date = strtotime(date("Y-m-d"));
                return round(($cur_date - $fall_toovert_time) / 86400);
            }
        }
        return -1;
    }

    /**
     * 还原客户保护
     */
    public function RestoreCustomerProtection(){
        global $adb,$current_user;
        $query='SELECT 1 FROM vtiger_accountrole WHERE roleid=?';
        $result=$adb->pquery($query,array($current_user->roleid));
        if($adb->num_rows($result)){
            return true;
        }
        return false;
    }
      /**
     * 获取客户名称及ID
     * @param $request
     * @return array
     */
    public function getAccontByName($request){
        global $adb;
        $accountName=$request->get('accountname');
        $query='SELECT vtiger_account.accountname,vtiger_account.accountid FROM vtiger_account LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid WHERE vtiger_crmentity.deleted=0 AND accountname like ? LIMIT 50';
        $result=$adb->pquery($query,array($accountName.'%'));
        $retrunData=array('success'=>false,'msg'=>'没有客户信息');
        if($adb->num_rows($result)){
            $rowData=array();
            while($row=$adb->fetch_array($result)){
                $rowData[]=array('accountid'=>(int)$row['accountid'],'accountname'=>$row['accountname']);
            }
            $retrunData=array('success'=>true,'data'=>$rowData);
        }
        return $retrunData;
    }
 /**
     * T云web端调用
     * @param $data
     * @return array
     * @throws AppException
     */
    public function getAccountList($data){
        global $adb;
        $querySql='';
        if(!$data['is_cs_admin']){
            if($data['customerid']>0){
                $querySql=' AND vtiger_crmentity.smownerid='.$data['customerid'];
            }else{
                $where=getAccessibleUsers('Accounts','List',true);
                if($where!='1=1'){
                    $querySql=' AND vtiger_crmentity.smownerid in('.implode(',',$where).')';
                }
            }
        }
        $query="SELECT 
            vtiger_account.accountname as mname,
            accountid AS mid,
            vtiger_crmentity.smownerid AS userid,
            IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id),'--') as username FROM vtiger_account LEFT JOIN vtiger_crmentity ON vtiger_account.accountid=vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0 and
             vtiger_account.accountcategory=0".$querySql." LIMIT 50";
        $result=$adb->pquery($query,array());
        $data=array();
        if($adb->num_rows($result)){
            while($row=$adb->fetch_array($result)){
                $data[]=$row;
            }
        }
        return $data;
    }

    public function accountList(Vtiger_Request $request){
            $accountid = $request->get('accountid');
            global $adb;
            $names = array();
            $sql2 = "select linkname,mobile from vtiger_account where accountid=?";
            $result2 = $adb->pquery($sql2,array($accountid));
            if($adb->num_rows($result2)){
                while ($row=$adb->fetch_row($result2)){
                    if(!$row['linkname']){
                        continue;
                    }
                    $names[] = array('linkname'=>$row['linkname'],'mobile'=>$row['mobile']);
                }
            }

            $sql = "select name,mobile from vtiger_contactdetails where accountid = ?";
            $result = $adb->pquery($sql,array($accountid));
            if($adb->num_rows($result)){
                while ($row = $adb->fetch_row($result)){
                    if(!$row['name']){
                        continue;
                    }
                    $names[] = array('linkname'=>$row['name'],'mobile'=>$row['mobile']);
                }
            }
        return $names;
    }

    public function getAccountInfo(Vtiger_Request $request){
        global $adb;
        $res = $adb->pquery("select b.address,b.accountname,b.fax,b.email1,a.depositbank as bank_account,a.accountnumber as numbered_accounts,a.taxpayers_no as taxpayers_no from vtiger_account b left join vtiger_billing a  on a.accountid = b.accountid where b.accountid = ? order by a.modifiedtime desc limit 1", array($request->get('accountid')));
        $accountinfo=$adb->query_result_rowdata($res,0);
        return $accountinfo;
    }
    /**
     * 获取用户的保护数据
     * @return int
     */
    public function getProtectnum(){
        global $current_user;
        $db=PearDatabase::getInstance();
        $query="SELECT protectnum FROM vtiger_protectsetting WHERE userid=? limit 1";
        $result=$db->pquery($query,array($current_user->id));
        $num=0;
        if($db->num_rows($result)){
            $num=$result->fields['protectnum'];
        }
        return $num;
    }

    /**
     * 获取客户信息根据id
     */
    static public function getAccountInfoByDataRecordId($dataRecordid){
        global $adb;
        $data = array();
        $result = $adb->pquery("select * from vtiger_datatransfer where datatransferid=? limit 1",array($dataRecordid));
        if(!$adb->num_rows($result)){
            return $data;
        }
        $row = $adb->fetchByAssoc($result,0);
        $accountIds = explode(',',$row['transferedids']);
        $sql2 = "select a.accountname,a.accountid,accountrank from vtiger_account a where accountid in( ".implode(',',$accountIds).")";
        $result = $adb->pquery($sql2,array());
        if($adb->num_rows($result)){
            while ($row = $adb->fetchByAssoc($result)){
                $row['accountrank'] = vtranslate($row['accountrank'],'Accounts','zh_cn');
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getSmownerDepartmentId($record){
        global $adb;
        $sql = "select c.departmentid,b.smownerid from vtiger_account a left join vtiger_crmentity b on a.accountid=b.crmid   
                  left join vtiger_user2department c on b.smownerid=c.userid
where a.accountid=?";
        $result = $adb->pquery($sql,array($record));
        if(!$adb->num_rows($result)){
            return 'H1';
        }
        $row = $adb->fetchByAssoc($result,0);
        return $row;
    }

    public function getAccountInfoByAccountName($accountName){
        $db = PearDatabase::getInstance();
        $res = $db->pquery("select a.accountname,a.accountid from vtiger_account a left join vtiger_crmentity b  on a.accountid = b.crmid where a.accountname = ? and b.deleted=0", array($accountName));
        $accountinfo=$db->fetchByAssoc($res,0);
        return $accountinfo;
    }


    /**
     * app端获取用户信息
     * @param Vtiger_Request $request
     * @return array
     * @throws Exception
     */
    public function getAccountInfosById(Vtiger_Request $request){
        $cids=json_decode(base64_decode($request->get('cids')),true);
        global $adb;
        $sql="SELECT
	t1.accountid,
	t1.accountname,
	t1.accountename,
	t1.email1 as email,
	t1.linkname,
	t1.mobile,
	t1.gender,
	t2.id as s_id,
	t2.last_name as s_name,
	t2.department as s_department,
	t2.phone_mobile as s_mobile,
	t2.email1 as s_email,
	t4.id as c_id,
	t4.last_name as c_name,
	t4.department as c_department,
	t4.phone_mobile as c_mobile,
	t4.email1 as c_email,
	t5.id as s_report_id,
	t5.last_name as s_report_name,
	t5.department as s_report_department,
	t5.phone_mobile as s_report_mobile,
	t5.email1 as s_report_email,
	t6.id as c_report_id,
	t6.last_name as c_report_name,
	t6.department as c_report_department,
	t6.phone_mobile as c_report_mobile,
	t6.email1 as c_report_email
FROM
	vtiger_account t1
	LEFT JOIN vtiger_users t2 ON t1.serviceid = t2.id
	LEFT JOIN vtiger_crmentity t3 ON t3.crmid = t1.accountid
	LEFT JOIN vtiger_users t4 on  t3.smownerid=t4.id
	LEFT JOIN vtiger_users t5 ON t2.reports_to_id = t5.id
	LEFT JOIN vtiger_users t6 ON t4.reports_to_id = t6.id
where t1.accountid in (".implode(',',$cids).")";
        $res=$adb->pquery($sql,array());
        if($adb->num_rows($res)){
            while ($row = $adb->fetchByAssoc($res)){
                $accountinfo[] = $row;
            }
        }
//        $accountinfo=$adb->run_query_allrecords($res);
        return base64_encode(json_encode($accountinfo));
    }
    /**
     * 臻寻客创建客户
     * @param $fieldname
     * @return array
     * @throws Exception
     */
    public function ZTKCreateAccountCID($fieldname){
        global $adb;
        $body=$fieldname->get('data');
        $body=base64_decode($body);
        $ordercode = json_decode($body,true);
        foreach($ordercode as $key=>$value){
            $_REQUEST[$key]=$value;
        }
        $accountInfo=$this->CreateAccount($_REQUEST);
        $this->addContacts($accountInfo);
        if($accountInfo[0]['flag']==1){
            $sql='UPDATE vtiger_account SET protectday=3,effectivedays=3,accountcategory=1 WHERE accountid=?';
            $adb->pquery($sql,array($accountInfo[0]['cid']));
        }
        return $accountInfo;

    }
    /**
     * 客户创建
     * @param $fieldname
     * @return array
     * @throws Exception
     */
    public function CreateAccount($fieldname){
        global $adb,$current_user;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($fieldname['userid']);
        $accountname=$fieldname['accountname'];
        $accountid=$fieldname['accountid'];
        $accountname=trim($accountname);
        $label=preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;|&quot;|　|&apos;|&amp;|&lt;|&gt;|&#039;|&ldquo;|&rdquo;|&lsquo;|&rsquo;|&hellip;|\“|\”|\‘|\〉|\〈|\’|\〖|\〗|\【|\】|\、|\·|\…|\——|\＋|\－|\＝|\,|\<|\.|\>|\/|\?|\;|\:|\\\'|\"|\[|\{|\]|\}|\\|\||\`|\~|\!|\@|\#|\$|\%|\^|\\&|\*|\(|\)|\-|\_|\=|\+|\，|\＜|\．|\＞|\／|\？|\；|\：|\＇|\＂|\［|\{|\］|\}|\＼|\｜|\｀|\￣|\！|\＠|\#|\＄|\％|\＾|\＆|\＊|\（|\）|\－|\＿|\＝|\＋|\，|\《|\．|\》|\、|\？|\；|\：|\’|\”|\【|\｛|\】|\｝|\＼|\｜|\·|\～|\！|\＠|\＃|\￥|\％|\…|\＆|\×|\（|\）|\－|\——|\—|\＝|\＋|\，|\《|\。|\》|\、|\？|\；|\：|\‘|\“|\【|\｛|\】|\｝|\、|\||\·|\~|\！|\@|\#|\￥|\%|\&|\*|\（|\）|\-|\——|\=|\+/u','',$accountname);
        $labelname=strtoupper($label);
        $sql = "SELECT
				vtiger_crmentity.label,
				vtiger_crmentity.crmid
			FROM
				vtiger_uniqueaccountname
			LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_uniqueaccountname.accountid
			WHERE
				deleted = 0
			AND vtiger_uniqueaccountname.accountname =?
			LIMIT 1";
        $listResult = $adb->pquery($sql, array($labelname));

        if($adb->num_rows($listResult)){
            $resultData=$adb->query_result_rowdata($listResult,0);
            $res=array("cid"=>$resultData['crmid'],'accountname'=>$resultData['label'],'flag'=>0);
        }else{
            include_once('includes/http/Request.php');
            include_once('modules/Vtiger/actions/Save.php');
            $request=new Vtiger_Request(array(), array());
            $_REQUEST['record']='';//save_modules模块中要用到
            $accountname=preg_replace('/^(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+|(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+$/u','',$accountname);
            $address=$fieldname['province'].'#';
            $address.=$fieldname['city'].'#';
            $address.=$fieldname['area'].'#';
            $address.=$fieldname['address'];
            $accountname=trim($accountname);
            $_REQUEST['accountname']=$labelname;
            $request->set('accountname',$accountname);
            $request->set('module','Accounts');
            $request->set('view','Edit');
            $request->set('action','Save');
            $request->set('makedecisiontype','Decisionmakers');
            $request->set('address',$address);
            $request->set('phone',$fieldname['phone']);
            $request->set('linkname',$fieldname['linkname']);
            $request->set('title',$fieldname['title']);
            $request->set('email1',$fieldname['email1']);
            $request->set('mobile',$fieldname['mobile']);
            $request->set('annual_revenue',$fieldname['annual_revenue']);
            $request->set('newleadsource',$fieldname['newleadsource']);
            $request->set('weixin',$fieldname['weixin']);
            $request->set('customertype',$fieldname['customertype']);
            $request->set('website',$fieldname['website']);
            $request->set('gendertype',$fieldname['gendertype']);
            $ressorder=new Vtiger_Save_Action();
            $recordModel=$ressorder->saveRecord($request);
            $crmid=$recordModel->getId();
            $sql='REPLACE INTO vtiger_uniqueaccountname(accountid,accountname) VALUES(?,?)';
            $adb->pquery($sql,array($crmid,$labelname));
            $sql='UPDATE vtiger_account SET protectday=30,effectivedays=30 WHERE accountid=?';

            $adb->pquery($sql,array($crmid));
            $res=array("cid"=>$crmid,'accountname'=>$accountname,'flag'=>1);
        }
        if($accountid>0){
            $adb->pquery("UPDATE vtiger_activationcode SET customerid=? WHERE usercodeid=? AND (customerid IS NULL OR customerid='')", array($res["cid"],$accountid));
        }
        return array($res);
    }

    /**
     * 联系人创建
     * @param $fieldname
     */
    public function addContacts($fieldname){
        try {
            global $adb;
            include_once('includes/http/Request.php');
            include_once('modules/Vtiger/actions/Save.php');
            $contacts = $_REQUEST['contacts'];
            if(false && $fieldname[0]['flag']==0){
                $contacts[] = array('linkname' => $_REQUEST['linkname'], 'mobile' => $_REQUEST['mobile'],
                    'phone' => $_REQUEST['phone'],
                );
            }
            $accountquerymobile = 'SELECT 1 FROM vtiger_account where accountid=? AND mobile=?';
            $accountqueryphone = 'SELECT 1 FROM vtiger_account where accountid=? AND phone=?';
            $contactquery = 'SELECT 1 FROM `vtiger_contactdetails` WHERE accountid=? AND (mobile=? OR phone=?)';
            foreach ($contacts as $value) {
                /*if (empty($value['mobile'])) {
                    $value['mobile'] = $value['phone'];
                }
                if (empty($value['phone'])) {
                    $value['phone'] = $value['mobile'];
                }*/
                $result = $adb->pquery($accountquerymobile, array($fieldname[0]['cid'], $value['mobile']));
                if ($adb->num_rows($result)) {
                    continue;
                }
                $result = $adb->pquery($accountqueryphone, array($fieldname[0]['cid'], $value['phone']));
                if ($adb->num_rows($result)) {
                    continue;
                }
                $result = $adb->pquery($contactquery, array($fieldname[0]['cid'],$value['phone'],$value['mobile']));
                if ($adb->num_rows($result)) {
                    continue;
                }
                $request = new Vtiger_Request(array(), array());
                foreach ($value as $key1 => $value1) {
                    if (!empty($value1)) {
                        $_REQUEST[$key1] =$value1;
                        $request->set($key1, $value1);
                    }
                }
                $_REQUEST['record'] = '';
                $_REQUEST['mobile'] = $value['mobile'];
                $_REQUEST['name'] = $value['linkname'];
                $request->set('name', $value['linkname']);
                $request->set('mobile', $value['mobile']);
                $request->set('account_id', $fieldname[0]['cid']);
                $request->set('makedecisiontype', 'Decisionmakers');
                $request->set('module', 'Contacts');
                $request->set('view','Edit');
                $request->set('action', 'Save');
                $ressorder = new Vtiger_Save_Action();
                $ressorder->saveRecord($request);
            }
        }catch (Exception $e){
            //不处理
        }
    }

  public function getFieldInfo(){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select tablename,columnname,uitype,fieldname,fieldtype,fieldid from vtiger_field where tabid=6 and searchtype=1");
        while ($row=$db->fetchByAssoc($result)){
            $data[$row['fieldname']] = array(
              'tablename'=>$row['tablename'],
              'columnname'=>$row['columnname'],
              'uitype'=>$row['uitype'],
              'fieldname'=>$row['fieldname'],
              'fieldtype'=>$row['fieldtype'],
              'fieldid'=>$row['fieldid'],
            );
        }
        return $data;
    }

    public function newAccountMobile(Vtiger_Request $request){
        $accountColumns = $request->get("accountColumns");
//        $accountColumns = array(
//          "customerproperty","cooperationtypesproperty","customertype","newleadsource","accountlabeling","industry","makedecisiontype"
//        );
        $db = PearDatabase::getInstance();
        $data = array();
        $lng = translateLng('Accounts');
        foreach ($accountColumns as $accountColumn){
            if($accountColumn=="intentionality"){
                $lng = translateLng('ModComments');
            }
            $tablename = "vtiger_".$accountColumn;
            $result = $db->pquery("select * from ".$tablename.' order by sortorderid asc',array());
            while ($row = $db->fetchByAssoc($result)){
                $data[$accountColumn][]= array(
                    "key"=>$row[$accountColumn],
                    "value"=>$lng[$row[$accountColumn]]?$lng[$row[$accountColumn]]:$row[$accountColumn]
                );
            }
        }
        $data['isLeader'] = false;
        global $current_user;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($request->get("userid"));
        $where=getAccessibleUsers('Accounts','List',true);
        if($where=='1=1' || count($where)>1){
            $data['isLeader'] = true;
        }
        $data['userId'] = $request->get("userid");
        $data['lastName'] = $current_user->last_name;

        return $data;
    }

    public function getaccountintentionality(){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select * from vtiger_intentionality order by sortorderid asc",array());
        $langs = translateLng('ModComments');
        while ($row = $db->fetchByAssoc($result)){
            $data[]= array(
                "key"=>$row['intentionality'],
                "value"=>$langs[$row['intentionality']]
            );
        }
        return $data;
    }

    public function getAccountDetail(Vtiger_Request $request){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT
	a.*,
	c.accountname AS parentid ,
	d.last_name as smownerid,
    b.createdtime,
    b.modifiedtime,
       b.description
FROM
	vtiger_account a
	LEFT JOIN vtiger_crmentity b ON a.accountid = b.crmid
	LEFT JOIN vtiger_account c ON a.parentid = c.accountid 
	left join vtiger_users d on b.smownerid=d.id
WHERE
	a.accountid = ?",array($request->get("accountid")));
        $accountData = $db->fetchByAssoc($result,0);
        $lng = translateLng("Accounts");
        $selectArray = array(
            "0"=>'无',
            "1"=>'是'
        );
        $remark = array();
        $result2 = $db->pquery("select * from vtiger_field where tabid=6 and isshowfield!=1 and displaytype<3 and presence!=1 and block in(9,164,165,166) order by sequence asc ",array());
        while ($row = $db->fetchByAssoc($result2)){
            switch ($row['block']){
                case 9:
                    if(in_array($row['columnname'],array('accountcategory'))){
                        $protected = array(	'0'=>'正常',
                            '1'=>'临时区',
                            '2'=>'公海');
                        $value =$protected[$accountData[$row['columnname']]]? $protected[$accountData[$row['columnname']]]:$accountData[$row['columnname']];
                    }elseif(in_array($row['columnname'],array('groupbuyaccount'))){
                        $value =$selectArray[$accountData[$row['columnname']]]? $selectArray[$accountData[$row['columnname']]]:$accountData[$row['columnname']];

                    }else{
                        $value = $lng[$accountData[$row['columnname']]]? $lng[$accountData[$row['columnname']]]:$accountData[$row['columnname']];
                    }
                    $baseInfo[] = array(
                        "label"=>$lng[$row['fieldlabel']],
                        "required"=>(strstr($row['typeofdata'],'M') ?true:false),
                        'value'=>$value
                    );
                    break;
                case 164:
                    if(in_array($row['columnname'],array('emailoptout','sign'))) {
                        $value = $selectArray[$accountData[$row['columnname']]] ? $selectArray[$accountData[$row['columnname']]] : $accountData[$row['columnname']];
                    }else{
                        $value = $lng[$accountData[$row['columnname']]] ? $lng[$accountData[$row['columnname']]] : $accountData[$row['columnname']];
                    }
                    $fistLinkInfo[] = array(
                        "label"=>$lng[$row['fieldlabel']],
                        "required"=>(strstr($row['typeofdata'],'M') ?true:false),
                        'value'=>$value
                    );
                    break;
                case 166:
                case 165:
                    if($row['columnname']=='educationproperty'){
                        break;
                    }
                    if($row['columnname']!='description' && !in_array($row['columnname'],array('annual_revenue'))) {
                        $value = $lng[$accountData[$row['columnname']]] ? $lng[$accountData[$row['columnname']]] : $accountData[$row['columnname']];
                        if($row['columnname']=='industry' && $accountData['educationproperty']){
                            $value.='>'.$accountData['educationproperty'];
                        }
                        $detailInfo[] = array(
                            "label" => $lng[$row['fieldlabel']],
                            "required" => (strstr($row['typeofdata'], 'M') ? true : false),
                            'value' =>$value
                        );
                    }elseif (in_array($row['columnname'],array('annual_revenue'))){
                        $detailInfo[] = array(
                            "label" => $lng[$row['fieldlabel']],
                            "required" => (strstr($row['typeofdata'], 'M') ? true : false),
                            'value' => $accountData[$row['columnname']]
                        );
                    }else{
                        $remark = array(
                            "label"=>$lng[$row['fieldlabel']],
                            "required"=>(strstr($row['typeofdata'],'M') ?true:false),
                            'value'=>$lng[$accountData[$row['columnname']]]? $lng[$accountData[$row['columnname']]]:$accountData[$row['columnname']]
                        );
                    }
                    break;
            }
        }
        $fistLinkInfo[] = $remark;
        $data = array(
            array(
                "title"=>"基本信息",
                "infolist"=>$baseInfo
            ),
            array(
                "title"=>"详细信息",
                "infolist"=>$detailInfo
            ),
            array(
                "title"=>"首要联系人",
                "infolist"=>$fistLinkInfo
            ),
        );
        return $data;
    }

    /**
     * 联系人列表
     *
     * @param Vtiger_Request $request
     * @return array
     */
    public function getContactList(Vtiger_Request $request){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select * from vtiger_account where accountid=?",array($request->get("accountid")));
        while ($row =$db->fetchByAssoc($result)){
            $data[] = array(
                "contactid"=>'',
                "contactname"=>$row['linkname'],
            );
        }

        $result2 = $db->pquery("select * from vtiger_contactdetails where accountid=?",array($request->get("accountid")));
        while ($row =$db->fetchByAssoc($result2)){
            $data[] = array(
                "contactid"=>$row['contactid'],
                "contactname"=>$row['name'],
            );
        }
        return array($data);
    }


    public function screenList(Vtiger_Request $request){
        $customize = $request->get('customize');
        $userid = $request->get('userid');
        if(!$customize){
            $filedname = array(
                'accountrank','intentionality','lastfollowuptime','assigned_user_id','createdtime','newleadsource'
            );
        }
        $db = PearDatabase::getInstance();
        $lng = translateLng("Accounts");

        $result2 = $db->pquery("select * from vtiger_field where tabid=6 and searchtype=1 order by sequence asc ",array());
        while ($row = $db->fetchByAssoc($result2)) {
            if(isset($filedname) && !in_array($row['fieldname'],$filedname) || $row['fieldname']=='accountname'){
                continue;
            }
            switch ($row['uitype']){
                case '70':
                    $stringData[] = array(
                        "showtag"=>3,
                        "key"=>$row['fieldname'],
                        "value"=>$lng[$row['fieldlabel']],
                        'datalist'=>array()
                    );
                    break;
                case '2':
                case '1':
                case '4':
                case '152':
                    $stringData[] = array(
                        "showtag"=>1,
                        "key"=>$row['fieldname'],
                        "value"=>$lng[$row['fieldlabel']],
                        'datalist'=>array()
                    );
                    break;
                case '56':
                    $stringData[] = array(
                        "showtag"=>2,
                        "key"=>$row['fieldname'],
                        "value"=>$lng[$row['fieldlabel']],
                        'datalist'=>array(
                            array(
                                "key"=>0,
                                'value'=>'否'
                            ),
                            array(
                                "key"=>1,
                                'value'=>'是'
                            )
                        )
                    );
                    break;
                case '15':
                case '151':
                    $pickListData = array();
                    $pickListSql = $db->pquery("select * from vtiger_".$row['fieldname']." order by sortorderid asc",array());
                    while ($pickListRow = $db->fetchByAssoc($pickListSql)){
                        $pickListData[] =array(
                            "key"=>$pickListRow[$row['fieldname']],
                            "value"=>$lng[$pickListRow[$row['fieldname']]]?$lng[$pickListRow[$row['fieldname']]]:$pickListRow[$row['fieldname']]
                        );
                    }
                    $stringData[] = array(
                        "showtag"=>2,
                        "key"=>$row['fieldname'],
                        "value"=>$lng[$row['fieldlabel']],
                        'datalist'=>$pickListData
                    );
                    break;
                case '53':
                case '54':
                    $stringData[] = array(
                        "showtag"=>2,
                        "key"=>$row['fieldname'],
                        "value"=>$lng[$row['fieldlabel']],
                        'datalist'=>$this->getUserRelativeUserList("Accounts",$userid)
                    );
                    break;
                default:
                    $stringData[] = array(
                        "showtag"=>2,
                        "key"=>$row['fieldname'],
                        "value"=>$lng[$row['fieldlabel']],
                        'datalist'=>array()
                    );
                    break;

            }
        }
        return $stringData;
    }

    function getUserRelativeUserList($module,$userid){
        global $adb,$current_user;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
        $where=getAccessibleUsers($module,'List',true);
        if($where!='1=1'){
            $query=' and id in ('.implode(',',$where).')';
        }else{
            $query='';
        }
        $sql = "SELECT
					id,brevitycode,last_name
				FROM
					vtiger_users WHERE  vtiger_users.status='Active'{$query}
				ORDER BY user_name";
        $res = $adb->pquery($sql, array());
        $temp_user = array();
        if($adb->num_rows($res) > 0){
            for($i=0; $i<$adb->num_rows($res); $i++){
                $row = $adb->fetchByAssoc($res, $i);
                $temp_user[] = array(
                    "key"=>$row['id'],
                    'value'=>$row['last_name']
                );
            }
        }
        return array($temp_user);
    }

    //获取客户跟进列表数据
    function getFollowList(Vtiger_Request $request) {
        $accountid = $request->get('record');
        $pageNumber =(int)$request->get('page');
        $limit = $request->get('limit');
        if(empty($pageNumber)){
            $pageNumber = 1;
        }
        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        if(!empty($limit)) {
            $pagingModel->set('limit', $limit);
        }
        global $adb,$current_user;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($request->get("userid"));
        $recentComments = ModComments_Record_Model::getRecentComments($accountid, $pagingModel,'Accounts',1);
        $pagingModel->calculatePageRange($recentComments);
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $alertStatus= array(
            'wait'=>'待办',
            'finish'=>'完成',
            'past'=>'完成(过期)',
        );
        $activitytype= array(
            "Call"=>"电话",
            "Meeting"=>"会议"
        );
        $taskpriority= array(
            "High"=>"高",
            "Medium"=>"中",
            "Low"=>"低",
        );

        foreach ($recentComments as $key => $val){
            $recentComments[$key]['valueMap']['accountintentionality'] = translateLng('Accounts')[$val['valueMap']['accountintentionality']];
            $dataComments = $this->dataComments($val['valueMap']['modcommentsid']);
//            $recentComments[$key]['commentor'] = $dataComments;
            $historyrecord = array();
            foreach ($val['jobalerts'] as $historyRecord){
                $historyrecord[] = array(
                    'createdbyer'=>$historyRecord['createdbyer'],
                    'createdbyeravatar'=>$historyRecord['picturepath'],
                    'createdtime'=>$historyRecord['createdtime'],
                    'accountintentionality'=>$historyRecord['accountintentionality'],
                    'modcommenthistory'=>$historyRecord['modcommenthistory'],
                );
            }
            $jobalerts = array();
            foreach ($val['historyrecord'] as $jobalert){
                $jobalerts[] = array(
//                    'commentor'=>$historyRecord['creatorid'],
//                    'commentoravatar'=>$historyRecord['createdbyer'],
                    'createdtime'=>$jobalert['createdtime'],
                    'subject'=>$jobalert['subject'],
                    'alerttime'=>$jobalert['alerttime'],
                    'username'=>$jobalert['username'],
                    'alertstatus'=>$alertStatus[$jobalert['alertstatus']],
                    'activitytype'=>$activitytype[$jobalert['activitytype']],
                    'taskpriority'=>$taskpriority[$jobalert['taskpriority']],
                    'alertcontent'=>$jobalert['alertcontent'],
                );
            }

            $data[] = array(
                "modcommentsid"=>$val['valueMap']['modcommentsid'],
                "last_name"=>$dataComments['last_name'],
                "avatar"=>$dataComments['picturepath'],
                "addtime"=>$val['valueMap']['addtime'],
                "modcommenttype"=>$val['valueMap']['modcommenttype'],
                "modcommentmode"=>$val['valueMap']['modcommentmode'],
                "accountintentionality"=> translateLng('Accounts')[$val['valueMap']['accountintentionality']],
                "lastname"=>$val['valueMap']['lastname'],
                "commentcontent"=>$val['valueMap']['commentcontent'],
                "historyrecord"=>($historyrecord && isset($historyrecord))?$historyrecord:array(),
                "jobalerts"=>($jobalerts && isset($jobalerts))?$jobalerts:array()
            );

        }

        $commentCount =  Accounts_Record_Model::getModcommentCount($accountid);

        return array('data'=>$data,'total'=>$commentCount);
    }

    function dataComments($commentedBy){
        global $adb;
        $sql = "SELECT vm.*,vu.*,vj.* FROM vtiger_modcomments as vm  INNER JOIN vtiger_users as vu on vu.id=vm.creatorid left join vtiger_wexinpicture vj on vj.userid=vu.id where vm.modcommentsid=?";
        $res = $adb->pquery($sql, array($commentedBy));
        $res_data = $adb->fetch_row($res);
        return $res_data;
    }
    public function getAccountInfoById(Vtiger_Request $request){
        global $adb;
        $res = $adb->pquery("
SELECT
	vtiger_account.accountname,
	IFNULL(
	(
SELECT
	CONCAT(
	last_name,
	'[',
	IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ),
	']',
	'[',
	usercode,
	']',
	( IF ( `status` = 'Active' AND isdimission = 0, '', '[离职]' ) ) 
	) AS last_name 
FROM
	vtiger_users 
WHERE
	vtiger_crmentity.smownerid = vtiger_users.id 
	),
	'--' 
	) AS smownerid,
	vtiger_crmentity.smownerid AS smownerid_owner,
	vtiger_account.accountrank,
	vtiger_account.lastfollowuptime,
	vtiger_account.accountid 
FROM
	vtiger_account FORCE INDEX ( accountcategory )
	LEFT JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid
	LEFT JOIN vtiger_servicecomments ON ( vtiger_account.accountid = vtiger_servicecomments.related_to AND vtiger_servicecomments.assigntype = 'accountby' AND vtiger_servicecomments.related_to > 0 ) 
WHERE
	 vtiger_crmentity.deleted = 0 
	AND vtiger_account.accountid = ?
", array($request->get('record')));
        $lng = translateLng("Accounts");
        $accountinfo=$adb->query_result_rowdata($res,0);
        $data = array(
            "accountid"=>$accountinfo['accountid'],
            "accountname"=>$accountinfo['accountname'],
            "accountrank"=>$lng[$accountinfo['accountrank']],
            "lastfollowuptime"=>$accountinfo['lastfollowuptime'],
            "smownerid"=>$accountinfo['smownerid'],
        );
        return $data;
    }

    /**
     * 根据所属人获取客户列表
     * @param Vtiger_Request $request
     */
    public function  getCustomerListByOwner(Vtiger_Request $request) {
        global $adb;
        $name = $request->get('name');
        $pageSize = $request->get('pageSize');
        $pageNum = $request->get('pageNum');
        $userId = $request->get('userid');
        $limit =' LIMIT '.(($pageNum-1) * $pageSize).','.$pageSize;
        $select = "SELECT count(1) as counts";
        $query = " FROM vtiger_account account LEFT JOIN vtiger_crmentity crmentity ON account.accountid = crmentity.crmid
                WHERE account.accountcategory IN(0,1) AND crmentity.deleted = 0 AND crmentity.smownerid = $userId";
        if (!empty($name)) {
            $query .= " AND accountname LIKE '%$name%'";
        }
        $result = $adb->pquery($select . $query, []);
        $total = $adb->query_result($result,'counts',0);
        $data = ['list'=>[], 'total'=>0];
        if ($total <= 0) {
            return $data;
        }
        $data['total'] = $total;
        $select = 'SELECT accountid, accountname, province, city, area, address, longitude, latitude, mobile, linkname';
        $result = $adb->pquery($select . $query . $limit, []);
        if ($adb->num_rows($result) > 0) {
            $list = [];
            while ($row = $adb->fetchByAssoc($result)) {
                $contactList = [];
                if (!empty($row['linkname'])) {
                    $contactList[] = [
                        'id' => 0,
                        'name' => $row['linkname'],
                        'mobile' => $row['mobile']
                    ];
                }
                $list[$row['accountid']] = [
                    'id' => $row['accountid'],
                    'name' => $row['accountname'],
                    'province' => $row['province'],
                    'city' => $row['city'],
                    'area' => $row['area'],
                    'address' => str_replace('#', '',$row['address']),
                    'longitude' => $row['longitude'],
                    'latitude' => $row['latitude'],
                    'contactList' => $contactList
                ];
            }
            $accountIds = implode(',', array_keys($list));
            $query = "SELECT contactid, accountid, name, mobile FROM vtiger_contactdetails WHERE accountid IN({$accountIds})";
            $result = $adb->pquery($query, []);
            if ($adb->num_rows($result) > 0) {
                while ($row = $adb->fetchByAssoc($result)) {
                    $list[$row['accountid']]['contactList'][] = [
                        'id'   => intval($row['contactid']),
                        'name' => $row['name'],
                        'mobile' => $row['mobile']
                    ];
                }
            }
            $data['list'] = $list;
        }
        return $data;
    }

    /**
     * 根据所属人获取客户列表
     * @param Vtiger_Request $request
     */
    public function  getRadiusCustomerListByOwner(Vtiger_Request $request) {
        global $adb;
        //员工ID
        $userId = $request->get('userid');
        //经度
        $longitude = $request->get('longitude');
        //维度
        $latitude = $request->get('latitude');
        //距离
        $distance = $request->get('distance');
        $query = "SELECT accountid, accountname, province, city, area, address, longitude, latitude, mobile, linkname
                FROM vtiger_account account LEFT JOIN vtiger_crmentity crmentity ON account.accountid = crmentity.crmid
                WHERE account.accountcategory IN(0,1) AND crmentity.deleted = 0 AND crmentity.smownerid = ?";
        $result = $adb->pquery($query, [$userId]);
        $list = [];
        if ($adb->num_rows($result) <=0) {
            return $list;
        }
        while ($row = $adb->fetchByAssoc($result)) {
            if (empty($row['latitude']) || empty($row['longitude'])) {
                continue;
            }
            $calculateDistance = $this->_calculateDistance($latitude, $longitude, $row['latitude'], $row['longitude']);
            if ($calculateDistance > $distance) {
                continue;
            }
            $contactList = [];
            if (!empty($row['linkname'])) {
                $contactList[] = [
                    'id' => 0,
                    'name' => $row['linkname'],
                    'mobile' => $row['mobile']
                ];
            }
            $list[$row['accountid']] = [
                'id'          => $row['accountid'],
                'name'        => $row['accountname'],
                'province'    => $row['province'],
                'city'        => $row['city'],
                'area'        => $row['area'],
                'address'     => str_replace('#', '', $row['address']),
                'longitude'   => $row['longitude'],
                'latitude'    => $row['latitude'],
                'distance'    => $calculateDistance,
                'contactList' => $contactList
            ];
        }
        if (empty($list)) {
            return $list;
        }
        $accountIds = implode(',', array_keys($list));
        $query = "SELECT contactid, accountid, name, mobile FROM vtiger_contactdetails WHERE accountid IN({$accountIds})";
        $result = $adb->pquery($query, []);
        if ($adb->num_rows($result) > 0) {
            while ($row = $adb->fetchByAssoc($result)) {
                $list[$row['accountid']]['contactList'][] = [
                    'id'   => intval($row['contactid']),
                    'name'   => $row['name'],
                    'mobile' => $row['mobile']
                ];
            }
        }
        return ['list'=>$list];
    }

    /**
     * 根据经纬度坐标计算距离
     * @param $lat1 float 维度1
     * @param $lng1 float 经度1
     * @param $lat2 float 维度2
     * @param $lng2 float 经度2
     * @return false|float 距离(单位：米)
     */
    public function _calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        //deg2rad()函数将角度转换为弧度
        $radLat1 = deg2rad($lat1);
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6371*1000;
        return round($s);
    }

    /**
     * 更新account
     */
    public function updateAccountZiZhiFile($accountId,$fileArray){
        global $adb;
        $str='';
        foreach ($fileArray as $fileId =>$fileName){
            $str.='*|*'.$fileName.'##'.$fileId;
        }
        $str=ltrim($str,'*|*');
        $sql="update vtiger_account set file=?  where accountid=?";
        $adb->pquery($sql,array($str,$accountId));
    }


    /**
     * 获取客户联系人信息
     * @param Vtiger_Request $request
     */
    public function getListAccountContacts(Vtiger_Request $request){
        global $adb;
        //用户str
        $accountStr = $request->get('accountStr');
        $accountArray=json_decode(base64_decode($accountStr),true);
        $query="SELECT accountid contactid,
			'' salutation,
			accountid contact_no,
			phone,
			mobile,
			accountid,
			title,
			'' fax,
			'' department,
			email1 AS email,
			'' secondaryemail,
			'' donotcall,
			IFNULL(emailoptout,0) AS emailoptout,
			linkname as name,
			gender,
			makedecision,
			'' weixin,
			'' qq,
			0 AS leave_office,
			1 type
			FROM vtiger_account WHERE accountid in (". implode(',', $accountArray).")";
        $query.=" UNION ALL ";
        $query.="SELECT vtiger_contactdetails.contactid,
					vtiger_contactdetails.salutation,
					vtiger_contactdetails.contact_no, 
					vtiger_contactdetails.phone, 
					vtiger_contactdetails.mobile, 
					vtiger_contactdetails.accountid, 
					vtiger_contactdetails.title, 
					vtiger_contactdetails.fax, 
					vtiger_contactdetails.department, 
					vtiger_contactdetails.email, 
					vtiger_contactdetails.secondaryemail, 
					vtiger_contactdetails.donotcall, 
					vtiger_contactdetails.emailoptout,
					vtiger_contactdetails.name,
					vtiger_contactdetails.gender, 
					vtiger_contactdetails.makedecision,
					vtiger_contactdetails.weixin, 
					vtiger_contactdetails.qq,
					vtiger_contactdetails.leave_office,
					0 type
				FROM vtiger_contactdetails 
				INNER JOIN vtiger_crmentity ON vtiger_contactdetails.contactid = vtiger_crmentity.crmid 
				WHERE vtiger_crmentity.deleted=0 AND vtiger_contactdetails.accountid in (". implode(',', $accountArray).")";
        $query .= " ORDER BY type DESC,contactid DESC ";
        $resultArray=$adb->run_query_allrecords($query);
        $arr_contacts=array();
        foreach ($resultArray as $result){
            $arrayt=array();
            $arrayt['accountid'] = $result['accountid'];
            $arrayt['salutation'] = $result['salutation'];
            //联系人ID
            $arrayt['contactid'] = $result['contactid'];
            //离职状态
            if($result['leave_office'] == '1'){
                $arrayt['leaveOffice'] = '1';
            }else{
                $arrayt['leaveOffice'] = '0';
            }
            $arrayt['fax'] = $result['fax'];
            $arrayt['department'] = $result['department'];
            $arrayt['secondaryemail'] = $result['secondaryemail'];
            $arrayt['donotcall'] = $result['donotcall'];
            $arrayt['emailoptout'] = $result['emailoptout'];
            //联系人
            $arrayt['name'] = $result['name'];
            //非首要联系人
            //$arrayt['is_main_linkname'] = 0;
            //性别
            $arrayt['gendertype'] = $result['gender'];
            $arrayt['gendertype_name'] = vtranslate($result['gender'],"Contacts");
            //手机
            $arrayt['mobile'] = $result['mobile'];
            //电话
            $arrayt['phone'] = $result['phone'];
            //微信
            $arrayt['weixin'] = $result['weixin'];
            //qq
            $arrayt['qq'] = $result['qq'];
            //职务
            $arrayt['title'] = $result['title'];
            //决策权
            $arrayt['makedecision'] = $result['makedecision'];
            $arrayt['makedecisiontype_name'] = vtranslate($result['makedecision'],"Contacts");
            //邮箱
            $arrayt['email'] = $result['email'];
            //是否首要联系人
            $arrayt['type'] = $result['type'];

            $arr_contacts[]=$arrayt;
        }
        return $arr_contacts;
    }

    /**
     * 批量同步客户到客服营销系统
     * @param Vtiger_Request $request
     * @return array
     */
    public function batchSyncKefucrm(Vtiger_Request $request)
    {
        global $adb;
        $date = $request->get('date');
        if(!$date) {
            return [
                'success' => false,
                'message' => '服务结束时间不能为空'
            ];
        }
        $deadline =  strtotime('-15 day');
        if (strtotime($date) > $deadline) {
            return [
                'success' => false,
                'message' => '服务结束时间应在15天之前'
            ];
        }
        $query='SELECT columnname,uitype FROM vtiger_field WHERE tabid=6 AND uitype in(15,16,56,151)';
        $result=$adb->pquery($query, array());
        $fieldrow=array();
        while($trow=$adb->fetchByAssoc($result)){
            if(in_array($trow['uitype'],array(15,16,151))){
                $fieldrow[15][]=$trow['columnname'];
            } else {
                $fieldrow[56][]=$trow['columnname'];
            }
        }
        $query = "SELECT vtiger_account.* FROM vtiger_account 
            INNER JOIN (SELECT customerid, MAX(expiredate) AS maxExpiredate FROM vtiger_activationcode WHERE `status` = 1 GROUP BY customerid) activationcode
            ON vtiger_account.accountid=activationcode.customerid
            WHERE activationcode.maxExpiredate < ? AND vtiger_account.cservicetransfer IS NULL ORDER BY maxExpiredate ASC LIMIT 500";
        $result = $adb->pquery($query, [$date]);
        $list = [];
        if ($adb->num_rows($result) <=0) {
            return [
                'success' => true,
                'message' => '无需要同步的客户'
            ];
        }
        while ($row = $adb->fetchByAssoc($result)) {
            $row['accountcategory'] = $row['accountcategory']==0?'正常':($row['accountcategory']==1?'临时区':'公海');
            foreach ($row as $key=>$value)
            {
                if(in_array($key,$fieldrow[15])){
                    $row[$key]=vtranslate($value,'Accounts');
                }elseif(in_array($key,$fieldrow[56])){
                    $row[$key]=$value==1?'是':'否';
                }else{

                    $row[$key]=$value;
                }
            }
            $list[] = $row;
        }
        global $serivce_crm_url;
        $url = $serivce_crm_url.'/sys/customer-batchSyncErpCustomer.json';
        $data = json_encode($list,JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES);
        $curlset = [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            ]
        ];
        $returnData = $this->https_requestcomm($url, $data, $curlset,true);
        $josnData = json_decode($returnData,true);
        if($josnData['result']=='success') {
            $accountIds = implode(',', array_column($list, 'accountid'));
            $sql = "UPDATE vtiger_account SET cservicetransfer='ctransfer' WHERE accountid IN($accountIds)";
            $adb->pquery($sql, []);
            return [
                'success' => true,
                'message' => $josnData['message']
            ];
        } else {
            return [
                'success' => false,
                'message' => $josnData['message']
            ];
        }
    }

     //修改联系人
    function updateContact(Vtiger_Request $request){
        $isFirstLink=$request->get('isFirstLink');
        $db = PearDatabase::getInstance();
        $result = $db->pquery("update vtiger_account set linkname=?,gender=?,makedecision=?,title=?,mobile=?,email1=?,phone=? where accountid=?",
            array($request->get("name"),$request->get("gender"),$request->get("makeDecision"),
                $request->get("title"),$request->get("mobile"),$request->get("email"),$request->get("phone"),
                $request->get("accountid")));
        return array('success'=>true,'msg'=>'修改成功');
    }

    function accountInfo(Vtiger_Request $request){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT
	a.*,
	c.accountname AS parentid ,
	d.last_name as smownerid,
    b.createdtime,
    b.modifiedtime,
    b.description,
    e.last_name as servicename,
    e.phone_mobile as servicephone,
    g.departmentname as servicedeparment,
    h.id as saleid,
    h.last_name as salename,
    h.phone_mobile as salephone,
    m.departmentname as saledepartment
FROM
	vtiger_account a
	LEFT JOIN vtiger_crmentity b ON a.accountid = b.crmid
	LEFT JOIN vtiger_account c ON a.parentid = c.accountid 
	left join vtiger_users d on b.smownerid=d.id
    left join vtiger_users e on e.id=a.serviceid 
    left join vtiger_user2department f on e.id=f.userid
    left join vtiger_departments g on g.departmentid=f.departmentid 
    left join vtiger_users h on h.id=b.smownerid
    left join vtiger_user2department i on i.userid=h.id
   left join vtiger_departments m on m.departmentid=i.departmentid
WHERE
	a.accountid = ?",array($request->get("accountId")));
        $accountData = $db->fetchByAssoc($result,0);
        $lng = translateLng("Accounts");
        $protected = array(	'0'=>'正常',
            '1'=>'临时区',
            '2'=>'公海');
        if(!$accountData){
            return array(
                'success'=>false,
                'msg'=>'没有客户信息'
            );
        }
        $contactRecordModel=Contacts_Record_Model::getCleanInstance("Contacts");
        return array(
            "success"=>true,
            "accountInfo"=>array(
                "accountId"=>$accountData['accountid'],
                "accountName"=>$accountData['accountname'],
                "accountCategory"=>$protected[$accountData['accountcategory']],
                "customerType"=>$lng[$accountData['customertype']],
                "accountRank"=>$lng[$accountData['accountrank']],
                "customerProperty"=>$accountData['customerproperty'],
                "address"=>$accountData['address'],
                "industry"=>$lng[$accountData['industry']],
                "business"=>$accountData['business'],
                "businessArea"=>$accountData['businessarea'],
                "regionalPartition"=>$accountData['regionalpartition'],
                "linkName"=>$accountData['linkname'],
                "mobile"=>$accountData['mobile'],
                "email"=>$accountData['email1'],
                "genderType"=>$lng[$accountData['gender']],
                "title"=>$accountData['title'],
                "makeDecisionType"=>$lng[$accountData['makedecision']],
                "serviceid"=>$accountData['serviceid'],
                "serviceName"=>$accountData['servicename'],
                "servicePhone"=>$accountData['servicephone'],
                "serviceDepartment"=>$accountData['servicedeparment'],
                "saleid"=>$accountData['saleid'],
                "saleName"=>$accountData['salename'],
                "salePhone"=>$accountData['salephone'],
                "saleDepartment"=>$accountData['saledepartment'],
            ),
            'contactList'=>$contactRecordModel->getContactList($request->get("accountId"))
        );
    }

    function noBindAccountList(Vtiger_Request $request){
        $accountName = $request->get("accountName");
        $pageNum = $request->get("pageNum");
        $pageSize = $request->get("pageSize");
        $bindAccountIds = $request->get("bindAccountIds");
        $userId = $request->get("userId");
        $db = PearDatabase::getInstance();
        if($bindAccountIds){
            $result = $db->pquery("select 1 from vtiger_account a left join vtiger_crmentity b on a.accountid=b.crmid where a.accountname like '%".$accountName."%' and a.accountid not in (".$bindAccountIds.") and b.deleted=0",array());
        }else{
            $result = $db->pquery("select 1 from vtiger_account a left join vtiger_crmentity b on a.accountid=b.crmid where a.accountname like '%".$accountName."%' and b.deleted=0",array());
        }
        $total = $db->num_rows($result);
        if(!$total){
            return array('success'=>true,'accountList'=>array(),'totalPage'=>$total,'pageNum'=>$pageNum);
        }
        $sql = "SELECT
	a.accountid,a.accountname,a.serviceid,c.last_name as servicename
FROM
	vtiger_account a
	LEFT JOIN vtiger_crmentity b ON a.accountid = b.crmid
	LEFT JOIN vtiger_users c ON a.serviceid = c.id
	where a.accountname like '%".$accountName."%' and b.deleted=0  ";
        if($bindAccountIds){
            $sql .= ' and a.accountid not in('.$bindAccountIds.') ';
        }
        if($userId){
            $sql .= ' order by field(serviceid,'.$userId.') desc ';
        }
        $sql .= ' limit '.$pageNum*$pageSize.','.$pageSize;
        $result2 = $db->pquery($sql,array());
        $accountList=array();
        if($db->num_rows($result2)){
            while ($row=$db->fetchByAssoc($result2)){
                $accountList[]=array(
                    "accountName"=>$row['accountname'],
                    "accountId"=>$row['accountid'],
                    "serviceid"=>$row['serviceid'],
                    "serviceName"=>$row['servicename'],
                );
            }
        }
        return array('success'=>true,'accountList'=>$accountList,'totalPage'=>$total,'pageNum'=>$pageNum);
    }
}
