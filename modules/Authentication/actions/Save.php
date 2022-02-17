<?php
class Authentication_Save_Action extends Vtiger_Save_Action {
    public function saveRecord($request) {
        global $adb,$current_user;
        $idcard=$request->get('idcard');
        $payer=$request->get('username');
        $result=$adb->pquery('select username,isdelete,authenticationid, successorfail from vtiger_authentication where idcard=?',array($idcard));
        $userName=$adb->query_result($result, 0, 'username');
        if($userName){
            $successorfail=$adb->query_result($result, 0, 'successorfail');
            $authenticationid=$adb->query_result($result, 0, 'authenticationid');
            if($payer!=$userName){
                //库里所存的打款人姓名和身份证不符
                if($successorfail=='success'){
                    //成功的不必再查，翻出来看
                    $sql="update vtiger_authentication set isdelete=0,creatid=?,createdtime=? where authenticationid=?";
                    $adb->pquery($sql,array($current_user->id,date('Y-m-d H:i:s'),$authenticationid));
                    $request->set('record',$authenticationid);
                    $recordModel = $this->getRecordModelFromRequest($request);
                }else{
                    //失败的再查一次
                    $result=$this->getIdCardCheck($request);
                    $recordModel = $this->getRecordModelFromRequest($request);
                    $recordModel->save();
                    $record=$recordModel->getId();
                    $sql="update vtiger_authentication set creatid=?,createdtime=?,successorfail=?,failreason=? where authenticationid=?";
                    $flag=$result['flag']?'success':'fail';
                    $adb->pquery($sql,array($current_user->id,date('Y-m-d H:i:s'),$flag,$result['msg'],$record));
                }
            }else{
                //已经有一样的了,展示出来，不用再查了
                $sql="update vtiger_authentication set isdelete=0,creatid=?,createdtime=? where authenticationid=?";
                $adb->pquery($sql,array($current_user->id,date('Y-m-d H:i:s'),$authenticationid));
                $request->set('record',$authenticationid);
                $recordModel = $this->getRecordModelFromRequest($request);
            }
        }else{
            //没查到，搞新的
            $result=$this->getIdCardCheck($request);
            $recordModel = $this->getRecordModelFromRequest($request);
            $recordModel->save();
            $record=$recordModel->getId();
            $sql="update vtiger_authentication set creatid=?,createdtime=?,successorfail=?,failreason=? where authenticationid=?";
            $flag=$result['flag']?'success':'fail';
            $adb->pquery($sql,array($current_user->id,date('Y-m-d H:i:s'),$flag,$result['msg'],$record));
        }
        return $recordModel;
	}

    /**
     * 查看身份证是否有效
     * @param $request
     */
    public function getIdCardCheck($request){
        $idcard=$request->get('idcard');
        $payer=$request->get('username');
        //先查我们的erp user数据库里有没有
        global $adb;
        $result=$adb->pquery('select id,last_name from vtiger_users where idcard=?',array($idcard));
        if($adb->num_rows($result)){
            //身份证号是内部员工的，不允许
            $data['flag']=false;
            $data['msg']='身份证号是内部员工';
        }else{
            //在user里查不到，去新的身份证库里查
            $result=$adb->pquery('select name from vtiger_idcard where idcard=?',array($idcard));
            $userName=$adb->query_result($result, 0, 'name');
            if($userName){
                if($payer!=$userName){
                    //库里所存的打款人姓名和身份证不符
                    $data['flag']=false;
                    $data['msg']='打款人姓名和身份证不符';
                }else{
                    //通过验证
                    $data['flag']=true;
                    $data['msg']='通过验证';
                }
            }else{
                //我们库里没有存,先查库看有没有失败的再调用外来接口
                $sql="select id from vtiger_idcardlog where idcard=? and name=? and successorfail='fail'";
                $result=$adb->pquery($sql,array($idcard,$payer));
                if($adb->num_rows($result)){
                    //已经有失败的了，直接返回
                    $this->juHeLog(null,$idcard,$payer,null);
                    $data['flag']=false;
                    $data['msg']='此身份证与姓名之前已经验证失败过了';
                }else{
                    $serviceRecord=new ServiceContracts_Record_Model();
                    $verificationArray=$serviceRecord->realNameCheck(array('name'=>$payer,'identityNumber'=>$idcard));
                    //接口调用日志
                    $responseArray=json_decode($verificationArray['response'],true);
                    $this->juHeLog($verificationArray,$idcard,$payer,null);
                    if($responseArray['code']&&$responseArray['code']=='10000'){
                        if($responseArray['data']['result']==1){
                            //通过验证,把数据整到数据库
                            $idcardArray['idcard']=$idcard;
                            $idcardArray['name']=$payer;
                            $adb->run_insert_data('vtiger_idcard',$idcardArray);
                            $data['flag']=true;
                            $data['msg']='通过接口验证';
                        }else if($responseArray['data']['result']){
                            $data['flag']=false;
                            $data['msg']='打款人信息不真实，请提供真实的打款人姓名和身份证号';
                        }
                    }else{
                        $data['flag']=false;
                        if($responseArray['code']){
                            $data['msg']=$responseArray['message'];
                        }else{
                            $data['msg']='姓名中异常字符，请使用中文汉字';
                        }
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 记录调用接口日志
     * @param $verificationArray
     * @param $idcard
     * @param $payer
     * @param $record
     */
    public function juHeLog($verificationArray,$idcard,$payer,$record){
        global $current_user,$adb;
        $insert['creatid']=$current_user->id;
        $insert['createdtime']=date('Y-m-d H:i:s');
        $insert['requestjson']=$verificationArray['request'];
        $insert['responsejson']=$verificationArray['response'];
        $responseArray=json_decode($verificationArray['response'],true);
        $insert['successorfail']='fail';
        $responseArray['code']&&$responseArray['code']=='10000'&&$responseArray['data']['result']==1&&$insert['successorfail']='success';
        $insert['idcard']=$idcard;
        $insert['name']=$payer;
        $insert['recordid']=$record;
        $insert['source']='auth';
        $adb->run_insert_data('vtiger_idcardlog',$insert);
    }
}
