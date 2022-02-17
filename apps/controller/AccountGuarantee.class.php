<?php

class AccountGuarantee extends baseapp {
    /*
     * 首页
     */
    public function index(){
        if(!$this->rechargeguarantee()){
            if(!$this->dorechargeguarantee()){
                echo '<script>alert("没有权限查看");window.location.href="javascript:history.go(-1)";</script>';
                exit;
            }
        }
        $this->smarty->display('AccountGuarantee/index.html');
    }
    /**
     * 查看权限
     * @return mixed
     */
    public function rechargeguarantee(){
        $params = array(
            'fieldname' => array(
                'module' => 'RefillApplication',
                'action' => 'personalAuthorityMobile',
                'modulename' =>'RefillApplication',
                'classname' =>'rechargeguarantee',
                'userid' => $this->userid
            ),
            'userid' => $this->userid
        );
        $result = $this->call('getComRecordModule', $params);
        return $result[0];
    }

    /**
     * 查看,更改权限
     */
    public function dorechargeguarantee(){
        $params = array(
            'fieldname' => array(
                'module' => 'RefillApplication',
                'action' => 'personalAuthorityMobile',
                'modulename' =>'RefillApplication',
                'classname' =>'dorechargeguarantee',
                'userid' => $this->userid
            ),
            'userid' => $this->userid
        );
        $result = $this->call('getComRecordModule', $params);
        return $result[0];
    }

    /**
     * 详情
     */
    public function one() {
        $id = $_REQUEST['id'];
        if (!empty($id)) {
            if(!$this->rechargeguarantee()){
                if(!$this->dorechargeguarantee()){
                    echo '<script>alert("没有权限查看");window.location.href="javascript:history.go(-1)";</script>';
                    exit;
                }
            }
            $params = array(
                'fieldname' => array(
                    'module' => 'RefillApplication',
                    'action' => 'getAccountGuaranteeDetailMobile',
                    'accountguaranteeid' => $id,
                    'isedit' => 0,
                    'userid' => $this->userid
                ),
                'userid' => $this->userid
            );
            $editdata = $this->call('getComRecordModule', $params);
            $this->smarty->assign('detaildata', $editdata[0]);
            $this->smarty->display('AccountGuarantee/one.html');
        }
    }

    /**
     * 获取数据
     */
    public function getAccountGuaranteeData(){
        $pageCount=20;
        $pageNum=(!empty($_POST['pagenum'])&& $_POST['pagenum']>1)?($_POST['pagenum']-1)*$pageCount:0;
        $accountName=trim($_POST['searchfilename']);
        $params = array(
            'fieldname' => array(
                'module' => 'RefillApplication',
                'action' => 'getAccountChargeGuaranteeMobile',
                'pageNum' => $pageNum,
                'pageCount' => $pageCount,
                'accountName' =>$accountName,
                'userid' => $this->userid
            ),
            'userid' => $this->userid
        );
        $res = $this->call('getComRecordModule', $params);
        if(!empty($res[0])){
            echo json_encode(array("success"=>true,'pageCount'=>$pageCount,'dataCount'=>count($res[0]),'data'=>$res[0]));
        }else{
            echo json_encode(array("success"=>false,'msg'=>'无相关数据'));
        }
    }

    /**
     * 修改数据
     */
    public function setAccountGuaranteeData(){
        if($_POST['accountid']==0){
            echo json_encode(array('success'=>false,'code'=>'客户无效'));
            exit;
        }
        if(!$this->dorechargeguarantee()){
            echo json_encode(array('success'=>false,'code'=>'没有权限'));
            exit;
        }
        $token='AccountGuaranteeData'.$this->userid;
        if($this->getAddToken($token)){
            echo json_encode(array('success'=>false,'code'=>'重复提交'));
            exit;
        }
        $fieldname=array(
            'module' => 'RefillApplication',
            'action' => 'setAccountChargeGuarantee',
            'currentuserid' => $this->userid
        );
        $fieldname=array_merge($fieldname,$_POST);
        $params = array(
            'fieldname' =>$fieldname,
            'userid' => $this->userid
        );
        $res = $this->call('getComRecordModule', $params);
        if($res[0]['flag']){
            echo json_encode(array('success'=>true,'code'=>'保存成功','dataid'=>$res[0]['data']));
            exit;
        }else{
            echo json_encode(array('success'=>true,'code'=>$res[0]['msg']));
            exit;
        }
    }
    /*
     * 删除数据
     */
    public function deleteAccountGuaranteeData(){
        if(!$this->dorechargeguarantee()){
            echo json_encode(array('success'=>false,'code'=>'没有权限'));
            exit;
        }
        $accountguaranteeid=$_POST['accountrechargeguaranteeid'];
        $params = array(
            'fieldname' => array(
                'module' => 'RefillApplication',
                'action' => 'deleteAccountGuaranteeData',
                'accountguaranteeid' =>$accountguaranteeid,
                'userid' => $this->userid
            ),
            'userid' => $this->userid
        );
        $res = $this->call('getComRecordModule', $params);
        if($res[0]['flag']){
            echo json_encode(array("success"=>true));
        }else{
            echo json_encode(array("success"=>false,'msg'=>$res[0]['msg']));
        }
    }
    /**
     * 编辑数据
     */
    public function edit(){
        $token='AccountGuaranteeData'.$this->userid;
        $this->setAddToken($token);
        $id=$_REQUEST['id'];
        if(!$this->dorechargeguarantee()){
            echo json_encode(array('success'=>false,'code'=>'没有权限'));
            exit;
        }
        $params = array(
            'fieldname' => array(
                'module' => 'RefillApplication',
                'action' => 'getuserinfo',
                'userid' => $this->userid
            ),
            'userid' => $this->userid
        );
        $data = $this->call('getComRecordModule', $params);
        $this->smarty->assign('userdata',$data[0]);
        if($id>0){
            $params = array(
                'fieldname' => array(
                    'module' => 'RefillApplication',
                    'action' => 'getAccountGuaranteeDetailMobile',
                    'accountguaranteeid' => $id,
                    'isedit' => 1,
                    'userid' => $this->userid
                ),
                'userid' => $this->userid
            );
            $editdata = $this->call('getComRecordModule', $params);
        }else{
            $editdata=array(array(array('userid' => 0,'accountid' =>0, 'unitprice' => 0,
                 'twoleveluserid' =>0, 'twounitprice' =>0,'threeleveluserid' =>0,'threeunitprice' => 0,'accountname' => '')));
        }
        $this->smarty->assign('EDITDATE',current($editdata[0]));
        $this->smarty->display('AccountGuarantee/edit.html');
    }
}
