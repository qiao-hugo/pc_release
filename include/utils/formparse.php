<?php
/* @param $datas  字段数组
** @param $parse  预定义表单结构
** @param $values  字段值数据
** $productid 产品ID 防止表单字段重复
*/
function parse_toform($datas,$parse,$values=array(),$productid,$html=true){
	$find=array();
	$replace=array();
	foreach($datas as $index=> $data){
		if($html){
			$find[]='&lt;{'.$data['name'].'}&gt;';
		}else{
			$find[]='<{'.$data['name'].'}>';
		}
		if(is_array($data)){
			if(isset($values[$data['name']])){
				$data['value']=$values[$data['name']];
			}
			if(in_array($data['type'],array('text','email','int','float','idcard'))){
				$required=($data['gRequired']==1)?'required':'';
				$tpl='<input '.$required.' type="'.$data['type'].'" title="'.$data['title'].'" name="p['.$productid.']['.$data['name'].']" value="'.$data['value'].'">';
			}elseif(in_array($data['type'],array('radio-inline','radio-noinline'))){
				$tpl='';
				$options=explode('##',$data['list']);
				$inline=($data['type']=='radio-inline')?'inline':'';
				foreach($options as $option){
					$checked=($option==$data['value'])?'checked="checked"':'';
					$tpl.='<label class="radio '.$inline.'"><input type="radio" '.$checked.' name="p['.$productid.']['.$data['name'].']" value="'.$option.'">'.$option.'</label>';
				}
			}elseif(in_array($data['type'],array('textarea-rich','textarea'))){
				$required=($data['gRequired']==1)?'required':'';
				$tpl='<textarea '.$required.' type="'.$data['type'].'" title="'.$data['title'].'" name="p['.$productid.']['.$data['name'].']" >'.$data['value'].'</textarea>';
			}elseif(in_array($data['type'],array('checkebox-inline','checkebox-noinline'))){
				$tpl='';
				$options=explode('##',$data['list']);
				$checke=explode('##',$data['value']);
				$inline=($data['type']=='checkebox-inline')?'inline':'';
				foreach($options as $option){
					$checked=(in_array($option,$checke))?'checked="checked"':'';
					$tpl.='<label class="aaa checkbox '.$inline.'"><input type="checkbox" '.$checked.' name="p['.$productid.']['.$data['name'].'][]" value="'.$option.'">'.$option.'</label>';
				}
			}elseif(in_array($data['type'],array('select'))){
				$options=explode('##',$data['list']);
				$required=($data['gRequired']==1)?'required':'';
				$tpl='<select name="p['.$productid.']['.$data['name'].']" '.$required.'>';
				foreach($options as $option){
					$checked=(trim($option)==trim($data['value']))?'selected="selected':'';
					$tpl.='<option '.$checked.' value="'.$option.'">'.$option.'</option>';
				}
				$tpl.='</select>';
			}elseif($data['type']=='listctrl'){
				//列表需要特殊处理 解出json为保存的数据负责为模版数据
				$th=explode('##',$data['title']);
				//$title=explode('##',$data['title']);
				$values1=array();
				$values1=json_decode($data['value']);
				if(!$values1){
					$values1[]=$data['value'];	
				}
				$tid=$data['name'].$productid;
				$type=explode('##',$data['listtype']);
				$ids=explode('##',$data['listid']);
				$td='';
				$tpl='<table id="'.$tid.'" cellspacing="0" class="table table-bordered table-condensed" style="width: 100%;">
				<thead>
				<tr><th colspan="'.(count($ids)+1).'">'.$data['listname'].'<span class="pull-right">
                    <button class="btn btn-small btn-success" type="button" onclick="tbAddRow(\''.$tid.'\')">添加一行</button></span></th></tr>
                <tr>';
				foreach($ids as $key=> $id){
					$tpl.='<th>'.$th[$key].'</th>';	
				}
				$tpl.='<th></th></tr></thead><tbody>';
				$options=explode('##',$datas[$index]['value']);
				foreach($values1 as $key=> $value){
					$value=explode('##', $value);
					$tr=($key==0)?'<tr class="template">':'<tr>';
					foreach($value as $k=>$v){
						if($type[$k]=='textarea'){
							$tr.='<td><textarea class="input-medium" name="p['.$productid.']['.$data['name'].']['.$ids[$k].'][]">'.$v.'</textarea></td>';	
						}elseif($type[$k]=='select'){
							$tr.='<td><select style="width:85px;height:23px;padding:1px;" name="p['.$productid.']['.$data['name'].']['.$ids[$k].'][]" >';
							
							$select=explode(';',$options[$k]);
							foreach($select as $option){
								$checked=(trim($option)==trim($v))?'selected="selected':'';
								$tr.='<option '.$checked.' value="'.trim($option).'">'.trim($option).'</option>';
							
							}
							$tr.='</select></td>';
							
						}else{
							$tr.='<td><input class="input" style="width:85px;padding:1px;" type="'.$type[$k].'" name="p['.$productid.']['.$data['name'].']['.$ids[$k].'][]" value="'.$v.'"></td>';
						}	
					}
					$show=($key==0)?'hide':'';
					$tpl.=$tr.'<td><i onclick="delrow($(this))" class="icon-remove delrow '.$show.'"></i></td></tr>';	
				}
					
				$tpl.='</tbody></table><style>.delrow{cursor:pointer}</style><script>function delrow(a){a.parent().parent().remove()}function tbAddRow(dname){$("#"+dname+" .template").clone(true).removeClass("template").find(".delrow").removeClass("hide").end().appendTo($("#"+dname));}</script>';
			}
		}
		$replace[]=$tpl;
	}
	return html_entity_decode(str_replace($find,$replace,$parse));
}
//解析成数据
function parse_tohtml($datas,$parse,$values=array(),$html=true){
	$find=array();
	$detail=array();
	$replace=array();
	foreach($datas as  $data){
		if($html){
			$find[]='&lt;{'.$data['name'].'}&gt;';
		}else{
			$find[]='<{'.$data['name'].'}>';
		}
		if(is_array($data)){
			if(in_array($data['type'],array('checkebox-inline','checkebox-noinline'))){
				$replace[]=(isset($values[$data['name']]))?'<code>'.implode(',',$values[$data['name']]).'</code>':'';
				$detail[$data['name']]=(isset($values[$data['name']]))?implode('##',$values[$data['name']]):'';
			}elseif($data['type']=='listctrl'){
				$ids=explode('##',$data['listid']);
				$th=explode('##',$data['title']);
				$tpl='<table cellspacing="0" class="table table-bordered table-condensed" style="width: 100%;"><thead><tr><th colspan="'.count($ids).'">'.$data['listname'].'</th></tr><tr>';
				foreach($ids as $key=> $id){
					$tpl.='<th>'.$th[$key].'</th>';	
				}
				$tpl.='</tr></thead><tbody>';
				$v=array();
				$array=$values[$data['name']];
				if(!empty($array)){			
					foreach($ids as $id){
						foreach($array[$id] as $k=>$val ){
							$v[$k][]=$val;
						}	
					}
				}
				foreach($v as $k=> $val){
					$tr=implode('</code></td><td><code>',$val);
					$tpl.='<tr><td><code>'.$tr.'</code></td></tr>';
					$v[$k]=implode('##', $val);
				}
				$replace[]=$tpl.'</tbody></table>';
				$detail[$data['name']]=json_encode($v);
			
			}else{
				$detail[$data['name']]=(isset($values[$data['name']]))?trim($values[$data['name']]):'';
				$replace[]=(isset($values[$data['name']]))?'<code>'.$values[$data['name']].'</code>':'';
			}
		}
	}
	return array(html_entity_decode(str_replace($find,$replace,$parse)),$detail);
}



function parse_log($id){
	$db=PearDatabase::getInstance();
	$result=$db->pquery("SELECT vtiger_formdesign.field, vtiger_salesorder_productdetail_history.*, vtiger_products.productname, vtiger_users.last_name FROM `vtiger_salesorder_productdetail_history` LEFT JOIN vtiger_formdesign ON vtiger_salesorder_productdetail_history.TplId = vtiger_formdesign.formid LEFT JOIN vtiger_products ON vtiger_salesorder_productdetail_history.Productid = vtiger_products.productid LEFT JOIN vtiger_users ON vtiger_salesorder_productdetail_history.EditId = vtiger_users.id WHERE SalesOrderId = ?",array($id));
	$rows=$db->num_rows($result);
	if($rows>0){
		$fields=array();
		$log=array();
		$i=0;
		while($row=$db->fetchByAssoc($result)){
			if(empty($fields[$row['tplid']])){
				$fields[$row['tplid']]=json_decode(str_replace('&quot;','"',$row['field']),true);
			}
			$log[$i]=$row;
			$input=json_decode(str_replace('&quot;','"',$row['forminput']),true);
//			foreach($input as $name=>$value){
//				echo $fields[$row['tplid']][$name]."---".$value."\n"
//				
//			}
			foreach($fields[$row['tplid']] as $field){
				if(isset($input[$field['name']])){
					if($field['type']=='checkebox-inline' || $field['type']=='checkebox-noinline' || $field['type']=='listctrl'){
						$input[$field['name']]=str_replace('##', ',', $input[$field['name']]);	
					}
					if($field['type']=='listctrl'){
						$field['title']=str_replace('##','、',$field['title']);
						$input[$field['name']]=implode(';',json_decode($input[$field['name']]));
					}
					$log[$i]['edit'][]=array($field['title'],$input[$field['name']]);
					
				}
				
			}
			$i++;
		}
		
		return $log;
		
	}
	
	
	
	
	
}
