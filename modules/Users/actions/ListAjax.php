<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Users_ListAjax_Action extends Users_List_View{
	function __construct() {
		parent::__construct();
		$this->exposeMethod('getListViewCount');
		$this->exposeMethod('getRecordsCount');
		$this->exposeMethod('getPageCount');
		$this->exposeMethod('getWeixinMessage');
	}

	function preProcess(Vtiger_Request $request) {
		return true;
	}

	function postProcess(Vtiger_Request $request) {
		return true;
	}

	function process(Vtiger_Request $request) {
		$mode = $request->get('mode');
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}
    public function getWeixinMessage(Vtiger_Request $request){
        $userkey='c0b3Ke0Q4c%2BmGXycVaQ%2BUEcbU0ldxTBeeMAgUILM0PK5Q59cEp%2B40n6qUSJiPQ';
        $url = "http://m.crm.71360.com/api.php";
        $data=array();
        $data['username']='verywell';
        $data['email']=$request->get('email');
        $data['oldemail']=$request->get('email');
        $data['tokenauth']=$userkey;
        $data['flag']=5;
        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $output=curl_exec($ch);
        curl_close($ch);
        $outdata=json_decode($output,true);
        if($outdata['errcode']==0){
            $status=$outdata['status']==1?'已关注':($outdata['status']==4?'未关注':"已禁用");
            $gender=$outdata['gender']==0?'未定义':($outdata['gender']==1?'男':"女");
            $avatar=$outdata['status']==1?'<img src="'.$outdata['avatar'].'" width="32" height="32"/>':'';
            echo '<table class="table table-bordered mergeTables detailview-table ">
                    <thead>
                        <tr><th>头像</th><th>姓名</th><th>账号</th><th>邮箱</th><th>状态</th><th>性别</th></tr>
                    </thead>
                    <tbody>
                    <tr>
                    <td>'.$avatar.'</td><td>'.$outdata['name'].'</td><td>'.$outdata['userid'].'</td><td>'.$outdata['email'].'</td><td>'.$status.'</td><td>'.$gender.'</td>
                    </tr></tbody>
                    </table>';
        }else{
            echo '没有相关信息';
        }
    }
}