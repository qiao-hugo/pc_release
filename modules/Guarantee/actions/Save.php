<?php
/* +****************
 *合同保存验证
 *新增产品必填，打回后产品不可编辑
 * ******************* */

class Guarantee_Save_Action extends Vtiger_Save_Action {

	public function saveRecord($request) {
	    $salesorderid=$request->get('salesorderid');
        $submitflag=$request->get('submitflag');//前台是先通过JS验证如果没有则不让提交
	    $total=$request->get('total');
        $salesorder=array('salesorder_no'=>$salesorderid);
        $result=Guarantee_Record_Model::getsalesoderid($salesorder);
        if($submitflag!='yes'){
            echo "非法操作不允许提交";
            exit;
        }
        if(!$result){
            echo "不存在的工单编号";
            exit;
        }else{
            $guaranteetotal=Guarantee_Record_Model::getGuarantetotal();//能担保的总金额
            $Guarantecurrentpay=Guarantee_Record_Model::getGuarantecurrentpay();//已担保的总金额
            $receiveprice=Guarantee_Record_Model::getreceivedayprice($result['servicecontractsid']);//对应回款的总金额
            $realprice=Guarantee_Record_Model::getrealprice($result['salesorderid']);//当前对应工单的总成本
            $alreadycalculate=Guarantee_Record_Model::alreadycalculate($result['servicecontractsid'],$result['salesorderid']);//已计算过有准备回款的工单总成本
            $salesorderguarante=Guarantee_Record_Model::getGuarantecurrent($result['salesorderid']);//当前工单担保的总金额
            $tempoccupancyamount=Guarantee_Record_Model::getoccupancyamount($result['servicecontractsid'],$result['salesorderid']);
            $Canguarantee=$guaranteetotal-$Guarantecurrentpay;//当前登陆用户可用的担保金额

            $Canguaranteedifference=$alreadycalculate+$realprice-$receiveprice-$salesorderguarante+$tempoccupancyamount;//总回款+担保金-已计算工单的总成本-该工单的总成本
            $Canguaranteedifference=$Canguaranteedifference>0?$Canguaranteedifference:0;
            $Canguaranteemoney=$Canguarantee>0?($Canguaranteedifference>0?($Canguarantee-$Canguaranteedifference>=0?$Canguaranteedifference:$Canguarantee):0):0;
            //echo $Canguaranteemoney,"<hr>";
            //echo $total,"<hr>";
            if($Canguaranteemoney==0||empty($total)||$total<=0||$total>$Canguaranteemoney){
                echo "非法操作不允许提交";
                exit;
            }
        }
        $request->set('salesorderid',$result['salesorderid']);
        $recordModel = $this->getRecordModelFromRequest($request);

		$recordModel->save();
        $receiveprice=Guarantee_Record_Model::getreceivedayprice($result['servicecontractsid']);//对应回款的总金额
        $alreadycalculate=Guarantee_Record_Model::alreadycalculate($result['servicecontractsid'],$result['salesorderid']);//已计算过有准备回款的工单总成本
        $salesorderguarante=Guarantee_Record_Model::getGuarantecurrent($result['salesorderid']);//对应工单已担保的总成本
        $realprice=Guarantee_Record_Model::getrealprice($result['salesorderid']);//对应的总成本
        $totalt=$receiveprice+$salesorderguarante-$realprice-$alreadycalculate;
        $tempoccupancyamount=Guarantee_Record_Model::getoccupancyamount($result['servicecontractsid'],$result['salesorderid']);
        $tempoccupancyamount=$receiveprice-$tempoccupancyamount;
        $tempoccupancyamount=$realprice>$tempoccupancyamount?$tempoccupancyamount:$realprice;
        //担保金额+回款金额-总成本价-,大于等于0说明公式成立
        Guarantee_Record_Model::updatesalesordertotal($salesorderguarante,$tempoccupancyamount,$result['salesorderid']);//更新对应工单的担保金额
        if($totalt>=0){
            if(ServiceContracts_Record_Model::getWorkflows($result['salesorderid'])){
                //工单对应的工作流是否是标准工作流
                if(ServiceContracts_Record_Model::getSalesorderworkflowsid($result['salesorderid'])==ServiceContracts_Record_Model::selectWorkfows()){
                    //是否是T-clude套餐
                    if(ServiceContracts_Record_Model::createIsWorkflows('',$result['servicecontractsid'])){
                        //生成工作流
                        ServiceContracts_Record_Model::contractsMakeWorkflows($result['salesorderid'],$result['servicecontractsid'],1);
                        //删除第1,2节点
                        ServiceContracts_Record_Model::setWorkflowNode($result['salesorderid']);
                    }
                }
            }
            //非标合同担保金激活
            ServiceContracts_Record_Model::noStandardToRestart($result['salesorderid'],$result['servicecontractsid']);
        }

		return $recordModel;
	}
}
