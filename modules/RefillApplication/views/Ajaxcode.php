<?php
class RefillApplication_Ajaxcode_View extends Vtiger_Index_View {
	
	function preProcess(Vtiger_Request $request) {
		return true;
	}
	
	function postProcess(Vtiger_Request $request) {
		return true;
	}
	//获取当前客户和充值平台所对应的历史账户
	function process (Vtiger_Request $request) {
		global $adb;
		$label='';
        $accountid = $_REQUEST['accountid'];
        $topplatform = $_REQUEST['topplatform'];
		//获取账户信息
        $sql = "SELECT DISTINCT concat(a.accountzh,a.did),b.accountid,a.topplatform,a.accountzh,a.did FROM vtiger_rechargesheet a
                LEFT JOIN vtiger_refillapplication b ON(a.refillapplicationid=b.refillapplicationid)
                WHERE b.accountid=? AND a.topplatform=?";
        $result = $adb->pquery($sql, array($accountid,$topplatform));

        $rows = $adb->num_rows($result);
		if($rows>0){
			for($i=0;$i<$rows;$i++){
				$r=$adb->fetch_array($result);
				if($i%10==0){
					$label.=' <br>';
				}	
				$label.=' <label title="账号:'.$r['accountzh'].',ID:'.$r['did'].'" style="margin-left: 10px;" class="radio inline"><input type="radio" name="optionsRadios" data-did="'.$r["did"].'" value="'.$r['accountzh'].'" > '.$r['accountzh'].',ID:'.$r['did'].'</label> ';
				
			}
		}else{
            $label.=' <label title="账号" style="margin-left: 10px;" class="radio inline">无历史账号</label> ';
        }

	echo '<div class="modelContainer" style="width:800px;">
	<div class="modal-header contentsBackground" >
        <button data-dismiss="modal" class="close" title="关闭">&times;</button>
		<h3>历史账号列表</h3><hr>
		<div style="overflow: auto;max-height:350px;">'.$label.'</div>	
	</div>
	<div class="modal-footer">
			<div class="pull-right cancelLinkContainer">
				<a class="cancelLink" type="reset" data-dismiss="modal" data-dismiss="modal">取消</a>
			</div>
		</div>
	</div>';
    //<button class="btn btn-success" type="submit" disabled="true">
    //<strong>确定</strong>
    //	</button>
}
	

}