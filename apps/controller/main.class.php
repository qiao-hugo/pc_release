<?php

class main extends baseapp{
	#主页
	public function index(){

		header("Location: index.php?module=VisitingOrder&action=vlist");
	}
	#我的crm
	public function mycrm(){
		$this->smarty->assign('title','我的crm');
		$this->smarty->assign('lastname',$_SESSION['last_name']);
		$this->smarty->assign('userid', $this->userid);
		$this->smarty->display('main/mycrm.html');
	}
	#消息中心
	public function notice(){
		#待跟进
		$params  = array(
			'fieldname'=>array('pagenum' 	=> 1,
						   	   'pagecount'  => 10,
						   	   'public' 	=> 'unaudited',
						   	   'userid'		=> $this->userid
						   )
		);

		$list = $this->call('get_VisitingOrder', $params);
		$this->smarty->assign('dgjsum',$list[0]);

		#待审核
		$params  = array(
			'fieldname'=>array('pagenum' 	=> 1,
						   	   'pagecount'  => 1,
						   	   'public' 	=> 'pass',
						   	   'userid'		=> $this->userid
						   )
		);

		$list = $this->call('getWorkFlowChecks', $params);
		$this->smarty->assign('dshsum',$list[0]);

		$this->smarty->assign('title','消息中心');
		$this->smarty->display('main/notice.html');
	}

    public function getWaterText(){
	    if(isset($_REQUEST['waterText']) && !empty($_REQUEST['waterText'])){
          $data=array("success"=>1,"waterText"=>$_SESSION['waterText']);
        }else{
          $data=array("success"=>0,"waterText"=>"请登录移动erp");
        }
        echo json_encode($data);exit();
    }

}

