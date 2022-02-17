<?php
/*+****
 * 用户自定义列表字段及排序 By Joe@20150421
 ******/

class EmployeeAbility_FieldAjax_View extends Vtiger_Index_View {
	
	function checkPermission(Vtiger_Request $request) {
		return true;
	}
	function preProcess(Vtiger_Request $request, $display = true) {
		return true;
	}
	
	function postProcess(Vtiger_Request $request) {
		return true;
	}

	function process (Vtiger_Request $request) {		
		$mode = $request->get('mode');
		$moduleName = $request->getModule();
		$listViewModel = Vtiger_ListView_Model::getInstance($moduleName,0);
		$frozenColumn = array('userid','stafflevel','user_entered','gradename','employeestage','isdimission');
		if($mode=='updatesort'){
			$feilds=$request->get('fieldList');
			if(!empty($feilds)){
                $columns  = array_merge($frozenColumn,$request->get('fieldList'));
                $feilds=implode(',',$columns);
				$listViewModel->getSelectFields($feilds,'in');
			}
			exit;
		}
		
		$listViewHeaders = $listViewModel->getListViewHeaders();
		$isSelected= $listViewModel->getSelectFields();
		$select1='';
		$select2='';
		//更新排序后编辑以新数序为准
		if(is_array($isSelected)){
			foreach($isSelected as $key=> $field){
                if(in_array($key,$frozenColumn)){
                    continue;
                }
				if(isset($listViewHeaders[$key])){
					$select1.='<option value="'.$key.'">'.vtranslate($key, $moduleName).' </option>';
					unset($listViewHeaders[$key]);
				}
			}
		}
		foreach($listViewHeaders as $key=>$field){
		    if(in_array($key,$frozenColumn)){
		        continue;
            }
			$select2.='<option value="'.$field['fieldlabel'].'">'.vtranslate($field['fieldlabel'], $moduleName).' </option>';	
		}			
	echo '<div class="modelContainer" style="max-width:800px;">
	<div class="modal-header contentsBackground">
        <button data-dismiss="modal" class="close" title="关闭">&times;</button>
		<h3>自定义列表字段</h3>
	</div>
	<form class="form-horizontal" id="findDuplicate" action="index.php">
	
<div> 
    <table class="dialog-table" id="CustomSetTable" style="width:550px"> 
     <tbody> 
      <tr align="center" valign="middle" class="BgRow"> 
       <td align="left" width="5%">&nbsp;</td> 
       <td width="20%"> 
        <fieldset style="border: 0pt none; font-size: 12px;"> 
         <legend>可选字段</legend>
         <div id="selectFieldDiv">
          <select multiple="multiple" style="width:95%;height:200px;" name="fieldsToSelectList" id="fieldsToSelectList">'.$select2.'</select>
         </div>
        </fieldset> </td> 
       <td width="10%"> <input style="width:50px" type="button" id="addAllField" name="addAllField" value="&gt;&gt;|" /><br /> <input style="width:50px" type="button" id="addField" name="addField" value="&gt;&gt;" /><br /> <input style="width:50px" type="button" id="deleteField" name="deleteField" value="&lt;&lt;" /><br /> <input style="width:50px" type="button" id="deleteAllField" name="deleteAllField" value="|&lt;&lt;" /> </td> 
       <td width="20%"> 
        <fieldset style="border: 0pt none; font-size: 12px;"> 
         <legend>要显示的字段</legend>
         <div id="showFieldDiv">
          <select multiple="multiple" style="width:95%;height:200px;" name="fieldsToShowList" id="fieldsToShowList">'.$select1.'</select>
         </div>
        </fieldset> </td> 
       <td align="left" width="8%"> <input type="button" id="upButton" name="upButton" style="width: 30px;" value="↑" /><br /> <input type="button" id="downButton" name="downButton" style="width: 30px;" value="↓" /> </td> 
      </tr> 
      <tr> 
       <td style="text-align:center" colspan="5"> <input type="submit" style="width:120px" value="确定" /> <br /> </td> 
      </tr> 
     </tbody>
    </table>
   </div>
   <br>
</form>

</div>';
}
	
	
	/*<input type="hidden" name="module" value="'.$moduleName.'" />
		<input type="hidden" name="view" value="updatesort" />
		<br>
		<div class="control-group">
			<span class="control-label">
				列表将按所选字段顺序排列
			</span>
			<div class="controls">
				<div class="row-fluid">
					<span class="span8">
						<select id="fieldList" class="select2 row-fluid" multiple="true" name="fields[]"
							data-validation-engine="validate[required]">'.$select.'</select>
					</span>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<div class="pull-right cancelLinkContainer">
				<a class="cancelLink" type="reset" data-dismiss="modal" data-dismiss="modal">取消</a>
			</div>
			<button class="btn btn-success" type="submit" disabled="true">
				<strong>确定</strong>
			</button>
		</div>
	*/
	

}