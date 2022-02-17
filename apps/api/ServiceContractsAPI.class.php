<?php
/**
 * Api接口
 * @author Jeff
 *
 */
class ServiceContractsAPI extends baseapi
{
    private $tokensalt = "#13anljlellaoonzopaqmvaekj98jjo34943240jljljflj321";
    /**
     * 获取代理商列表
     */
    public function agentContract(){
        date_default_timezone_set("Asia/Shanghai");
        $body = file_get_contents('php://input');
        $ordercode = json_decode($body,true);
        $currenttime = $ordercode['currenttime'];
        $tokensalt = md5($this->tokensalt.$currenttime);
        $token = $ordercode['token'];
        $this->_logs(array('agentContract','params'=>$ordercode,'tokensalt'=>$this->tokensalt,'date'=>date("YmdH"),'mdtoken'=>$tokensalt));
        if($token!=$tokensalt){
            $this->_logs(array('agentContractFailReason'=>'token异常'));
            echo json_encode(array('success' => false, 'code'=>500,'message' => 'token异常，请重试!'), JSON_UNESCAPED_UNICODE);
            exit();
        }
        $agentid = $ordercode['agentid'];
        $params = array(
            'fieldname' => array(
                'module' => 'ServiceContracts',
                'action' => 'agentContract',
                'agentid'=>$agentid,
                'userid' => 0
            ),
            'userid' => 0
        );
        $res = $this->call('getComRecordModule', $params);
        $this->_logs(array('agentContractResult',$res));
        echo json_encode($res[0], JSON_UNESCAPED_UNICODE);
    }
    /**
     * 销账功能
     */
    public function financialstate(){
        $body = file_get_contents('php://input');
        $ordercode = json_decode($body,true);
        $currenttime = $ordercode['currenttime'];
        /*$tokensalt = md5($this->tokensalt.$currenttime);
        $token = $ordercode['token'];
        $this->_logs(array('agentContract','params'=>$ordercode,'tokensalt'=>$this->tokensalt,'date'=>date("YmdH"),'mdtoken'=>$tokensalt));
        if($token!=$tokensalt){
            $this->_logs(array('agentContractFailReason'=>'token异常'));
            //echo json_encode(array('success' => false, 'code'=>500,'message' => 'token异常，请重试!'), JSON_UNESCAPED_UNICODE);
            //exit();
        }*/
        $code = $ordercode['code'];
        $userid = $ordercode['userid'];
        $advanceMoney = $ordercode['advanceMoney'];
        $params = array(
            'fieldname' => array(
                'module' => 'RefillApplication',
                'action' => 'financialstate',
                'code'=>$code,
                'advanceMoney'=>$advanceMoney,
                'userid' => $userid
            ),
            'userid' => $userid
        );
        $res = $this->call('getComRecordModule', $params);
        $this->_logs(array('agentContractResult',$res));
        echo json_encode($res[0], JSON_UNESCAPED_UNICODE);
    }

}
