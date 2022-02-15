<?php
/*+****
 * 用户自定义列表字段及排序 By Joe@20150421
 ******/

class WorkSummarize_FieldAjax_View extends Vtiger_Index_View {
	
	function checkPermission(Vtiger_Request $request) {
		return true;
	}
	function preProcess(Vtiger_Request $request) {
		return true;
	}
	
	function postProcess(Vtiger_Request $request) {
		return true;
	}

	function process (Vtiger_Request $request) {


		$mode = $request->get('mode');

		if($mode=='updatesort'){
			$feilds=$request->get('fieldList');
            WorkSummarize_Record_Model::setUserNowrite($feilds);

			exit;
		}

		$usernowrite= WorkSummarize_Record_Model::getUserNowrite();

        $where=getAccessibleUsers('','',true);

        if($where!='1=1'){
            if(count($usernowrite['userid'])>1){
                $check='';
                foreach($usernowrite['userid'] as $value){
                    $check.='<div class="pull-left" style="width:70px;"><label class="checkbox inline" id="user'.$value['id'].'"><input type="checkbox"  class="rmuser" value="'.$value['id'].'"';
                    if(in_array($value['id'],$usernowrite['nowriteuserid'])){
                        $check.='checked="checked"';
                    }
                    $check.='>'.$value['last_name'].'</label></div>';
                }
            }
        }


	echo '<div class="modelContainer" style="max-width:800px;">
	<div class="modal-header contentsBackground">
        <button data-dismiss="modal" class="close" title="关闭">&times;</button>
		<h3>勾选不用写工作总结的人员</h3>
	</div>
	<form class="form-horizontal" id="findDuplicat" action="index.php" onsubmit="return false">
	
    <div>
    <table class="dialog-table" id="CustomSetTable" style="width:550px"> 
     <tbody> 
      <tr align="center" valign="middle" class="BgRow"> 
       <td align="left" ><div style="margin-left:15px;max-height:300px; overflow-y:auto;">'.$check.'</div></td>
        </tr>
      <tr> 
       <td style="text-align:center" > <legend></legend><label class="checkbox pull-left" style="margin-left:10px;"><input class="checkall" type="checkbox">全选</label>
       <label class="checkbox pull-left" style="margin-left:10px;"><input class="reversecheck" type="checkbox">反选</label>
       <input type="submit" style="width:120px" value="确定" /> <br /> </td>
      </tr> 
     </tbody>
    </table>
   </div>
   <br>
</form>

</div>';
}

}