<?php

class SalesDaily extends baseapp
{
   //添加销售日报
    public function add()
    {
        $params = array('userid' => "$this->userid");
        $list = $this->call('getAccountsAndProduct', $params);

        if($list[0]['isadd']){
            echo "<script>alert('今天已经写了工作总结!!');history.back();</script>";
            exit;
        }
        $params = array('userid' => $this->userid);
        $candeallist = $this->call('getCandealAccounts', $params);
        $this->smarty->assign('candeallist', $candeallist);
        $this->smarty->assign('accountinfo', $list[0]['account']);
        $this->smarty->assign('productsinfo', $list[0]['product']);
        $this->smarty->assign('username', $_SESSION['last_name']);
        $this->smarty->assign('userid', $_SESSION['customer_id']);
        $params = array(
            "fieldname"=>array(
                "module"=>"SalesDaily",
                "mode"=>'getAccountStatistics',
                "dailydate"=>date("Y-m-d"),
                "ismobile"=>1
            ),
            'userid' => "$this->userid"
        );
        $accountStatistics = $this->call("salesDailyAccountStatistics",$params);
        $this->smarty->assign('accountstatistics', $accountStatistics[1]);
        $this->smarty->display('SalesDaily/add.html');
    }

    public function approval() {
        $description = $_REQUEST['description'];
        $relationid =  $_REQUEST['relationid'];
        $params  = array(
            'fieldname'=>array(
                'description'    => $description,
                'createid'=>$this->userid,
                'relationid'=>$relationid,
                "module"    =>'Approval',
                "action"    =>'Save',
                'record'    =>'',
                'relationOperation'=>'1',
                'model'=>'SalesDaily',
                'sourceModule'=>'SalesDaily',
                'sourceRecord'=>$relationid
            ),
            'userid'    => $this->userid
        );
        $res = $this->call('addApproval', $params);
        echo json_encode($res[0]);
    }

    public function one() {
        $id = $_REQUEST['id'];
        $params  = array(
            'fieldname'=>array(
                'id'=> $id,
                "module"    =>'Approval',
                "action"    =>'Save',
                'record'    =>''
            ),
            'userid'    => $this->userid
        );
        $res = $this->call('getOneSalesDaily', $params);
        //var_dump($res[0]['approvalData']);die;
        //print_r(count($res[0]['approvalData']));die;
        $this->smarty->assign('data', $res[0]);
        $this->smarty->assign('id', $id);
        $params = array(
            "fieldname"=>array(
                "module"=>"SalesDaily",
                "mode"=>'getAccountStatistics',
                "dailydate"=>date("Y-m-d"),
                "ismobile"=>1,
                "record"=>$id,
            ),
            'userid' => "$this->userid"
        );
        $accountStatistics = $this->call("salesDailyAccountStatistics",$params);
        $this->smarty->assign('accountstatistics', $accountStatistics[1]);
        $this->smarty->display('SalesDaily/one.html');
    }

    public function goto_approval_ui() {
        $id = $_REQUEST['id'];

         $params  = array(
            'fieldname'=>array(
                'id'=> $id,
                "module"    =>'Approval',
                "action"    =>'Save',
                'record'    =>''
            ),
            'userid'    => $this->userid
        );

        // 获取当前用户的名字和当前服务器的时间 
        $salesDailyOtherData = $this->call('get_SalesDailyOtherData', $params);

        $this->smarty->assign('username', $salesDailyOtherData[0] );
        $this->smarty->assign('nowtime', $salesDailyOtherData[1] );


        $this->smarty->assign('id', $id);
        $this->smarty->display('SalesDaily/pifu.html');
    }

    public function slist() {
        $pagenum   = isset($_REQUEST['pagenum'])?$_REQUEST['pagenum']:1;
        $pagecount  = $this->count;
        $pagecount  = 20;
        $params  = array(
            'fieldname'=>array(
                "module"     =>'SalesDaily',
                'pagenum'    => $pagenum,
                'pagecount'  => $pagecount,
                'userid'     => $this->userid,
                'pagecount'=> 20
            ),
            'userid'    => $this->userid
        );

        $list = $this->call('get_SalesDailyList', $params);

        //$num=ceil($list[0]/$pagecount);
        $this->smarty->assign('totalnum', $list[0]);
        $sumpage = intval(($list[0]+1)/$pagecount);
        //$this->smarty->assign('sumpage',$sumpage);
        $this->smarty->assign('CHECKT',$checkw);
        $this->smarty->assign('list',$list[1]);

        $type = $_REQUEST['type'];
        if ($type == 'ajax') {
            $this->smarty->display('SalesDaily/ajax.html');
        } else {
            $this->smarty->display('SalesDaily/list.html');
        }
        
    }


    #添加日志
    public function doadd()
    {

        $params = array(
            'fieldname' => array($_REQUEST),
            'userid' => $this->userid
        );
        $result=$this->call('addSalesDaily', $params);

        header("location:index.php?action=mycrm");
        exit;

    }

    #批复日报
    public function addFollowInfo(){
        $commentcontent 		= trim($_REQUEST['commentcontent']);
        $moduleid			= trim($_REQUEST['moduleid']);
        if(empty($commentcontent)||empty($moduleid)){
            echo '数据错误';return true;
        }
        $params = array(
            'fieldname'=>array(
                'related_to'		=> $moduleid,
                'moduleid'			=> $moduleid,
                'module'			=> 'ModComments',
                'modulename'		=> 'SalesDaily',
                'ifupdateservice'	=> false,
                'action'			=> "SaveAjax",
                'is_service'		=> "",
                "commentcontent"=>$commentcontent
            ),
            'userid'			=> $this->userid
        );
        $res = $this->call('addMod', $params);
        echo json_encode($res[0]);

    }
}
