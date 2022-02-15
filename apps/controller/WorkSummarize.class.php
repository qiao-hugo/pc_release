<?php

class WorkSummarize extends baseapp{
    private  $count=20;//只能是10,20,50,100后台做了限制
	public function index(){

        $pagenum 	= isset($_REQUEST['pagenum'])?$_REQUEST['pagenum']:1;
        $pagecount 	= $this->count;
        $params  = array(
            'fieldname'=>array('pagenum' 	=> $pagenum,
                'pagecount'  => $pagecount,
                'userid'		=> $this->userid
            )
        );

        $list = $this->call('get_WorkSummarize', $params);
        $checkw = $this->call('checkWorkSummarize', array('userid'=> $this->userid));
        $num=ceil($list[0]/$pagecount);
        $this->smarty->assign('totalnum',$num);
        $sumpage = intval(($list[0]+1)/$pagecount);
        $this->smarty->assign('sumpage',$sumpage);
        $this->smarty->assign('CHECKT',$checkw);

        $this->smarty->assign('list',$list[1]);
        $this->smarty->display('WorkSummarize/index.html');
	}
    //ajax请求数据
    public function doallList(){
        $pagenum 	= isset($_REQUEST['pagenum'])?$_REQUEST['pagenum']:1;
        $pagecount 	=  $this->count;
        $params  = array(
            'fieldname'=>array('pagenum' 	=> $pagenum,
                'pagecount'  => $pagecount,
                'userid'		=> $this->userid
            )
        );
        $list = $this->call('get_WorkSummarize', $params);

        echo json_encode($list[1]);
    }

	#添加工作总结
	public function add(){
        $params  = array(
            'userid'		=> $this->userid
        );
        $list = $this->call('checkWorkSummarize', $params);
        if($list>0){
            echo "<script>alert('今天已经写了工作总结!!');history.back();</script>";
            exit;
        }
		$this->smarty->assign('username',$_SESSION['last_name']);
		$this->smarty->assign('userid',$_SESSION['customer_id']);
		$this->smarty->display('WorkSummarize/add.html');
	}
	#处理添加的工作总结
	public function doadd(){
		$worksummarizename 			= trim($_REQUEST['worksummarizename']);
		$todaycontent 		= trim($_REQUEST['todaycontent']);
		$dayfeel = trim($_REQUEST['dayfeel']);
		$tommorrowcontent 		= trim($_REQUEST['tommorrowcontent']);
		$params = array(
			'fieldname'=>array( "module"	=>'WorkSummarize',
								"action"	=>'Save',
								'record'	=>'',
								"defaultCallDuration"		=>5,
								"defaultOtherEventDuration"	=>5,
								"worksummarizename"			=>$worksummarizename,
                                "todaycontent"				=>$todaycontent,
                                "dayfeel"				    =>$dayfeel,
                                "tommorrowcontent"			=>$tommorrowcontent,
								"smownerid"					=>$this->userid,

								),"userid"					=>$this->userid,
			);
		$res = $this->call('add_WorkSummarize', $params);
		header("location:index.php?module=WorkSummarize&action=index");
	}
}

