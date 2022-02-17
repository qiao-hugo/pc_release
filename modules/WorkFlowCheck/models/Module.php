<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class WorkFlowCheck_Module_Model extends Vtiger_Module_Model {

	/**
	 * Function to get the Quick Links for the module
	 * @param <Array> $linkParams
	 * @return <Array> List of Vtiger_Link_Model instances
	 */
	public function getSideBarLinks($linkParams) {
		$parentQuickLinks = parent::getSideBarLinks($linkParams);

		$quickLink = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '已审核信息',
				'linkurl' => $this->getListViewUrl().'&public=history',
				'linkicon' => '',
		);
		$quickLink1 = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '超过24小时未审核信息',
				'linkurl' => $this->getListViewUrl().'&public=outnumberday',
				'linkicon' => '',
		);
        $quickLink2 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '工单',
            'linkurl' => $this->getListViewUrl().'&public=SalesOrder',
            'linkicon' => '',
        );
        $quickLink3 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '发票',
            'linkurl' => $this->getListViewUrl().'&public=Invoice',
            'linkicon' => '',
        );
        $quickLink4 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '拜访单',
            'linkurl' => $this->getListViewUrl().'&public=VisitingOrder',
            'linkicon' => '',
        );
        $quickLink5 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '待我审',
            'linkurl' => $this->getListViewUrl().'&public=toBeTriedByMe',
            'linkicon' => '',
        );
        $quickLink6 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '我发起',
            'linkurl' => $this->getListViewUrl().'&public=iInitiated',
            'linkicon' => '',
        );
        $quickLink7 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '我已审',
            'linkurl' => $this->getListViewUrl().'&public=history',
            'linkicon' => '',
        );
        /* $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
        $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink3);
        $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink4);
        $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink1);*/
        $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink5);
        $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink6);
        $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink7);
		return $parentQuickLinks;
	}
    /**
     * 统计有多少条要审核的信息
     * @param $outnumber =""是否要统计超过24小时的信息
     * @return 0 | $reust
     */

    public function getConfirmation($outnumber=''){
        $menuModelsList = Vtiger_Menu_Model::getAll(true);//读取列表返回一个数组对象
        //判断是否该用户是否有列表访问权限如果有则进入没有则直接返回0
        if(in_array('WorkFlowCheck',array_keys($menuModelsList))){
            //array_keys()将数组的下标组成一个新的数组
            $db = PearDatabase::getInstance();

            $moduleall=new WorkFlowCheck_ListView_Model();
            $moduleName = 'WorkFlowCheck';
            $orderBy = $moduleall->getForSql('orderby');
            $sortOrder = $moduleall->getForSql('sortorder');
            if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
                $orderBy = 'salesorderworkflowstagesid';
                $sortOrder = 'DESC';
            }

            $listQuery = "SELECT count(1) as counts FROM vtiger_salesorderworkflowstages WHERE 1=1 ";
            $where=getAccessibleUsers('WorkFlowCheck','List',false);
            $listQuery.=$moduleall->getWhereSql($where);
            if($outnumber=="outnumberday"){
                $listQuery .=" AND vtiger_salesorderworkflowstages.actiontime<'".date("Y-m-d H:i:s",strtotime('-1 day'))."'";//超过24小时的未审核 的
            }
            global  $current_user;
            // 如果非超级管理员加下面的条件 当前用户为节点审核角色时的数据显示出来
            if($current_user->is_admin!='on'){
                // 关联审核节点表
                $listQuery=str_replace('FROM vtiger_salesorderworkflowstages',' FROM vtiger_salesorderworkflowstages  LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid=vtiger_salesorderworkflowstages.workflowstagesid ',$listQuery);
                //加条件审核节点角色等于 当前登录用户角色
                $roleid = $current_user->roleid;
                $newSql = " and  find_in_set('".$roleid."',REPLACE(vtiger_workflowstages.isrole,' |##| ',','))  and vtiger_salesorderworkflowstages.smcreatorid ";
                $listQuery=str_replace('and vtiger_salesorderworkflowstages.smcreatorid ',$newSql,$listQuery);
            }
            $viewid = ListViewSession::getCurrentView($moduleName);
            ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
            //echo $listQuery;die();
            $result = $db->pquery($listQuery,array());
            //echo $listQuery;
            return $db->query_result($result, 0, 'counts');
        }else{
            return 0;
        }

    }
    /**
     * 统计有多少条24小时待跟进拜访单
     * @return Ambigous <s, --, unknown>|number
     */
    public function getVisitingOrderFollowup(){
        $menuModelsList = Vtiger_Menu_Model::getAll(true);//读取列表返回一个数组对象
        //判断是否该用户是否有列表访问权限如果有则进入没有则直接返回0
        if(in_array('VisitingOrder',array_keys($menuModelsList))){
            $db = PearDatabase::getInstance();
            $moduleName = 'VisitingOrder';
            global $current_user;
            $moduleFocus = CRMEntity::getInstance($moduleName);

            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
            $moduleall=new VisitingOrder_ListView_Model();
            $orderBy = $moduleall->getForSql('orderby');
            $sortOrder = $moduleall->getForSql('sortorder');
            $listQuery = "SELECT count(1) as counts FROM vtiger_visitingorder INNER JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid = vtiger_crmentity.crmid  WHERE vtiger_crmentity.deleted=0 AND vtiger_visitingorder.visitingorderid > 0";
            $datetime=time()-86400;//超过一天的时间
            $newdatetime=date("Y-m-d H:i:s",$datetime);//将时间截转换成日期时间格式
            $listQuery .=" and vtiger_visitingorder.workflowsid=400 AND vtiger_visitingorder.followstatus='notfollow'";
            $listQuery .=" and LOCATE('b_',vtiger_visitingorder.modulestatus)>0 ";
            $listQuery.=" AND vtiger_crmentity.createdtime>'{$newdatetime}' ";//一天前的时间和创建时间比较
            $where=getAccessibleUsers('VisitingOrder','List',true);
            //读取该 用户的权限
            if($where!='1=1'){
                if(empty($where)){
                    $where=array($current_user->id);
                }
                $listQuery .= ' and vtiger_crmentity.smownerid in ('.implode(',',$where).')';//用户所在的权限列表
            }

            $result = $db->pquery($listQuery,array());
            return $db->query_result($result, 0, 'counts');
        }else{
            return 0;
        }
    }
    /**
     * 统计未写工作日报的人数
     * @return multitype:NULL |number
     */
    public function getNoWrite(){
        global $current_user;
        //过滤掉赵总的提醒
        if($current_user->user_name=='zhaozong'){
            return 0;
        }
        $menuModelsList = Vtiger_Menu_Model::getAll(true);//读取列表返回一个数组对象
        //判断是否该用户是否有列表访问权限如果有则进入没有则直接返回0
        if(in_array('WorkSummarize',array_keys($menuModelsList))){
            $db=PearDatabase::getInstance();
            //global $current_user;
            $where=getAccessibleUsers('WorkSummarize','List',true);
            if($where=='1=1' || empty($where)){
                //$where=array($current_user->id);
                //如果是管理员直接返回0;
                return 0;
            }
            //先过滤当为一条记录时判断该记录是否是当前登陆人如果是则直接返回0
            if(count($where)==1 && in_array($current_user->id,$where)){
                return 0;
            }
            $arr1=WorkSummarize_Record_Model::getUserNowrite('nowrite');

            if(count($arr1['nowriteuserid'])>0){
                //直接求两个数组的差集
                $where=array_diff($where,$arr1['nowriteuserid']);
            }
            //如果差集为空返回0
            if(count($where)==0){
                return 0;
            }

            //当为一条记录时判断该记录是否是当前登陆人如果是则直接返回0
            if(count($where)==1 && in_array($current_user->id,$where)){
                return 0;
            }
            $sql="SELECT DISTINCT vtiger_users.last_name FROM vtiger_users LEFT JOIN vtiger_worksummarize ON vtiger_users.id = vtiger_worksummarize.smownerid
			WHERE	vtiger_users.status='Active' AND vtiger_users.id in(".implode(',',$where).")";
            $sql.="	AND vtiger_users.id not in(select smownerid FROM vtiger_worksummarize WHERE to_days(now())-to_days(vtiger_worksummarize.createdtime)=1) AND vtiger_users.id !={$current_user->id}";//统计前一天,不包括自已的

            $result=$db->run_query_allrecords($sql);
            return count($result);
        }else{
            return 0;
        }
    }
    /**
     * 有多少要回复的工作日报记录数
     * @return Ambigous <s, --, unknown>|number
     */
    public function getReplynum(){
        global $current_user;
        //过滤掉赵总的提醒
        if($current_user->user_name=='zhaozong'){
            return 0;
        }
        $menuModelsList = Vtiger_Menu_Model::getAll(true);//读取列表返回一个数组对象
        //判断是否该用户是否有列表访问权限如果有则进入没有则直接返回0
        if(in_array('WorkSummarize',array_keys($menuModelsList))){

            $db=PearDatabase::getInstance();
            //global $current_user;
            $where=getAccessibleUsers('WorkSummarize','List',true);
            if($where=='1=1' || empty($where)){
                $where=array($current_user->id);
            }
            $arr1=WorkSummarize_Record_Model::getUserNowrite('nowrite');

            if(count($arr1['nowriteuserid'])>0){
                //直接求两个数组的差集
                $where=array_diff($where,$arr1['nowriteuserid']);
            }
            //如果差集为空返回0
            if(count($where)==0){
                return 0;
            }

            //当为一条记录时判断该记录是否是当前登陆人如果是则直接返回0
            if(count($where)==1 && in_array($current_user->id,$where)){
                return 0;
            }
            $today=date("Y-m-d");
            $threeday=date("Y-m-d",strtotime("-7 day"));
            $sql="SELECT count(1) as counts FROM vtiger_worksummarize WHERE NOT EXISTS ( select replytimes from vtiger_reply where vtiger_worksummarize.worksummarizeid = vtiger_reply.relatedid )
	                AND TO_DAYS(vtiger_worksummarize.createdtime) BETWEEN TO_DAYS('{$threeday}') AND TO_DAYS('{$today}')
					AND (vtiger_worksummarize.smownerid IN (".implode(',',$where).") OR FIND_IN_SET({$current_user->id},replace(touser,' |##| ',','))) AND vtiger_worksummarize.smownerid!={$current_user->id} ";

            $result = $db->pquery($sql,array());
            //echo $listQuery;
            return $db->query_result($result, 0, 'counts');
        }else{
            return 0;
        }
    }
    /**
     * 有多少7天未跟进的客服的客户
     * @return Ambigous <s, --, unknown>|number
     */
    public function getSevenCustomer(){
        $menuModelsList = Vtiger_Menu_Model::getAll(true);//读取列表返回一个数组对象

        //判断是否该用户是否有列表访问权限如果有则进入没有则直接返回0
        if(in_array('ServiceComments',array_keys($menuModelsList))){
            $db=PearDatabase::getInstance();
            global $current_user;
            $sql=" SELECT count(1) as counts FROM vtiger_servicecomments WHERE vtiger_servicecomments.assigntype = 'accountby' ";
            $where=getAccessibleUsers('ServiceComments','List',false);

            if($where!='1=1'){
                //测试一个杨工的账号出现了一个=号
                if($where!='= '){
                    $sql.=" AND vtiger_servicecomments.serviceid {$where} ";
                }else{
                    $sql.=" AND vtiger_servicecomments.serviceid={$current_user->id} ";
                }
            }
            //$sql.=" AND vtiger_servicecomments.nofollowday BETWEEN 1 AND 7 ";//跟进天数在1到7天之内的
            $sql.=" AND vtiger_servicecomments.allnofollowday = 0";// adatian/20150701 全部未跟进客服
            $result = $db->pquery($sql,array());
            return $db->query_result($result, 0, 'counts');
        }else{
            return 0;
        }
    }

    /**
     * @统计当前打回工单的记录条数
     */
    public function getRefuse(){

        $menuModelsList = Vtiger_Menu_Model::getAll(true);//读取列表返回一个数组对象
        global $current_user;
        //判断是否该用户是否有列表访问权限如果有则进入没有则直接返回0
        if(in_array('SalesOrder',array_keys($menuModelsList))) {
            $db = PearDatabase::getInstance();
            $query = "SELECT
                        count(DISTINCT vtiger_salesorder.salesorderid) AS  counts
                    FROM
                        vtiger_salesorder
                    LEFT JOIN vtiger_crmentity ON vtiger_salesorder.salesorderid = vtiger_crmentity.crmid
                    WHERE
                        1 = 1
                    AND vtiger_crmentity.deleted = 0
                    AND vtiger_salesorder.modulestatus = 'a_exception'";
//            $listViewModel = SalesOrder_ListView_Model::getInstance('SalesOrder');
//            $query .= $listViewModel->getUserWhere();
            $where=getAccessibleUsers('SalesOrder','List',true);
            if($where!='1=1'){
                $query.=" and vtiger_crmentity.smownerid = ".$current_user->id;
            }
            // echo $query;die;
            $result = $db->pquery($query, array());
            return $db->query_result($result, 0, 'counts');
        }else{
            return 0;
        }
    }

    /**
     * @统计当前无合同的回款;
     */
    public function get_noservice_receivepayment(){

        $menuModelsList = Vtiger_Menu_Model::getAll(true);//读取列表返回一个数组对象
        global $current_user;
        //判断是否该用户是否有列表访问权限如果有则进入没有则直接返回0
        if(in_array('ReceivedPayments',array_keys($menuModelsList))) {
            $db = PearDatabase::getInstance();
            $query = "SELECT COUNT(1) AS counts FROM vtiger_receivedpayments LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_receivedpayments.relatetoid WHERE 1 = 1 AND vtiger_receivedpayments.relatetoid = '' ";
//            $listViewModel = SalesOrder_ListView_Model::getInstance('SalesOrder');
//            $query .= $listViewModel->getUserWhere();
            //$where=getAccessibleUsers('ReceivedPayments','List',true);
            $where=getAccessibleUsers('ReceivedPayments','List',false);
            //var_dump($where) ;die;
            if($where!='1=1'){
                $query .= ' and exists(select servicecontractsid from vtiger_servicecontracts where vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid and exists(select crmid from vtiger_crmentity where vtiger_servicecontracts.servicecontractsid=vtiger_crmentity.crmid and vtiger_crmentity.deleted=0 and vtiger_crmentity.smownerid '.$where.')) ';
            }
            //echo $query ;die;
            $result = $db->pquery($query, array());
            return $db->query_result($result, 0, 'counts');
        }else{
            return 0;
        }
    }
    /**
     * 取当天消息是否关闭提醒
     * @return bool
     */
    public function getUserClickMsgStatus(){
        $ipnum=$this->getIpNum();
        //$status=Vtiger_Cache::get('vtiger_ipnum'.$ipnum);
        $db=PearDatabase::getInstance();
        $query="SELECT 1 FROM `vtiger_msgreminderstatus` WHERE ipaddr=? AND datenum=?";
        $result=$db->pquery($query,array($ipnum,date("Ymd")));
        if($db->num_rows($result)){
            return false;
        }
        return true;
    }

    /**
     * 将ip转为十进制数
     * @return string
     */
    private function getIpNum(){
        $ip=getip();
        $ip0X=explode('.',$ip);
        $str='';
        foreach($ip0X as $value){
            $str.=base_convert($value,10,16);
        }
        return base_convert($str,16,10);
    }

    /**
     * 将点击状态存入数据库
     */
    public function setUserClickMsgStatus(){
        $ipnum=$this->getIpNum();
        global $adb;
        $query="DELETE FROM `vtiger_msgreminderstatus` WHERE ipaddr=?";
        $adb->pquery($query,array($ipnum));
        $query="REPLACE INTO vtiger_msgreminderstatus(ipaddr,datenum) VALUES({$ipnum},".date("Ymd").")";
        $adb->pquery($query,array());
    }
	
}
