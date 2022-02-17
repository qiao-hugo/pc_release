<?php

class ServiceContracts_List_View  extends Vtiger_KList_View{
    function process (Vtiger_Request $request)
    {

        $strPublic = $request->get('public');

        if ($strPublic == 'Export') {               //导出
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            $viewer->assign('SETTLEMENTMONTH',$this->settlementMonth());
            $viewer->view('export.tpl', $moduleName);
            exit;
        }elseif($strPublic=='ExportD'){             //导出数据
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGroup()){   //权限验证
                return;
            }

            ob_clean();                              //清空缓存
			header('Content-type: text/html;charset=utf-8');
            $arrMonth = $this->settlementMonth();

            global $dbconfig;
            $conn = mysql_connect($dbconfig['db_server'],$dbconfig['db_username'],$dbconfig['db_password']) or die ("数据连接错误!!!");//原生的支持存储过程
            mysql_query("set names 'utf8' ");
            mysql_query("set character_set_client=utf8");
            mysql_query("set character_set_results=utf8");
            mysql_select_db($dbconfig['db_name'],$conn);

            $b=mysql_query("call sp_makeproductprice('".$arrMonth['Received'][0]."','".$arrMonth['Received'][1]."','".$arrMonth['System'][0]."','".$arrMonth['System'][1]."')");//执行
            $result=mysql_query('call sp_makeproductprice_make(1)');
            $temp ='<table cellpadding="0" cellspacing="0" width="100%" style="width:568px;"><tbody>';
            $temp.='<tr height="22" style=";height:22px" class="firstRow"><td height="22" width="171">销售组</td><td width="57" style="">业务员</td><td width="59" style="">状态</td><td width="355" style="">客户名称</td><td width="104" style="">合同签订日期</td><td width="164" style="">合同编号</td><td width="103" style="">工单编号</td><td width="292" style="">合同业务</td><td width="76" style="">合同金额</td><td width="121" style="">第一次收款时间</td><td width="72" style="">收款金额</td><td width="76" style="">应收金额</td><td width="76" style="">未收款项</td><td width="104" style="">人力成本合计</td><td width="104" style="">外采成本合计</td><td width="104" style="">成本合计</td><td width="88" style="">IDC内采成本</td><td width="88" style="">IDC外采成本</td><td width="88" style="">IDC明细</td><td width="104" style="">开发部成本</td><td width="104" style="">开发部市场价</td><td width="104" style="">开发部明细</td><td width="104" style="">创建时间</td></tr>';
            while($row = mysql_fetch_array($result)){
                $temp.="<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td><td>".$row[7]."</td><td>".$row[8]."</td><td>".$row[9]."</td><td>".$row[10]."</td><td>".$row[11]."</td><td>".$row[12]."</td><td>".$row[13]."</td><td>".$row[14]."</td><td>".$row[15]."</td><td>".$row[16]."</td><td>".$row[17]."</td><td>".$row[18]."</td><td>".$row[19]."</td><td>".$row[20]."</td><td>".$row[21]."</td><td>".$row[22]."</td></tr>";
            }
            $temp.='</table>';
           
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=数据.xls");
            //echo mb_convert_encoding($temp,'gb2312','utf-8');
			echo $temp;
            exit;
        }
        parent::process($request);
    }

    /**
     * 结算月
     * @return array
     */
    public function settlementMonth(){
        $iMonth=date('Y-m',time());
		$iDay = date('d',time());
		if($iDay>15){		//大于15号导出当前月
			$iMonth=date('Y-m',strtotime('+1 month'));
		}
        $arrMonth=array(
            '2015-08'=>array('Received'=>array('2015-07-03 00:00:00','2015-08-02 23:59:59'),'System'=>array('2015-07-06 00:00:00','2015-08-05 23:59:59')),
            '2015-09'=>array('Received'=>array('2015-08-03 00:00:00','2015-09-02 23:59:59'),'System'=>array('2015-08-06 00:00:00','2015-09-05 23:59:59')),
            '2015-10'=>array('Received'=>array('2015-08-03 00:00:00','2015-09-02 23:59:59'),'System'=>array('2015-09-06 00:00:00','2015-10-10 23:59:59'))
        );
        if(isset($arrMonth[$iMonth])){
            return $arrMonth[$iMonth];
        }
        return array('Received'=>array('-','-'),'System'=>array('-','-'));
    }
}