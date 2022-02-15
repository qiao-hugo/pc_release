<?php
class Users_Ajaxcode_View extends Vtiger_Index_View {
	
	function preProcess(Vtiger_Request $request) {
		return true;
	}
	
	function postProcess(Vtiger_Request $request) {
		return true;
	}
	//获取所有回收的工号和最大可用工号
	function process (Vtiger_Request $request) {		
		//$mode = $request->get('mode');
		//$moduleName = $request->getModule();
		global $current_user;
		if($current_user->is_admin!='on'){
			exit;
		}
		global $adb;
		$label='';
		$code=$adb->pquery('select * from vtiger_userscode where status=0');
		$rows=$adb->num_rows($code);
		if($rows>0){
			for($i=0;$i<$rows;$i++){
				$r=$adb->fetch_array($code);
				if($i%10==0){
					$label.=' <br>';
				}	
				$label.=' <label title="工号 '.$r['ucode'].'" style="margin-left: 10px;" class="radio inline"><input type="radio" name="optionsRadios" value="'.$r['ucode'].'" > '.$r['ucode'].'</label> ';
				
			}
		}
		//获取最新可用工号存入号码资源池
		$max=$adb->getUniqueID("vtiger_usercode");
		$max=str_pad($max, 4, '0', STR_PAD_LEFT);
		$adb->pquery('insert into vtiger_userscode (ucode,status) values(?,?)',array($max,0));
	echo '<div class="modelContainer" style="width:800px;">
	<div class="modal-header contentsBackground" >
        <button data-dismiss="modal" class="close" title="关闭">&times;</button>
		<h3>可用工号列表</h3><hr>
		<label class="radio inline">当前最新可用工号为<input type="radio" id="maxcode" name="optionsRadios" value="'.$max.'" > '.$max.'</label><br>
		<div style="overflow: auto;max-height:350px;">'.$label.'</div>	
	</div>
	<div class="modal-footer">
			<div class="pull-right cancelLinkContainer">
				<a class="cancelLink" type="reset" data-dismiss="modal" data-dismiss="modal">取消</a>
			</div>
			<button class="btn btn-success" type="submit" disabled="true">
				<strong>确定</strong>
			</button>
		</div>
	</div>';
}
	

}