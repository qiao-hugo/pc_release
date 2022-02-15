<?php

/**
	 * 后台缓存数据控制
	 * module/action索引权限
	 * @return success or error
	 */
	 
class Settings_Cacheinfo_Index_View extends Settings_Vtiger_Index_View {

	
	public function process(Vtiger_Request $request) {
		ini_set('memory_limit','8096M');//递归使用到了内存	
		set_time_limit(0);
		
		
		global $adb;
		global $root_directory;
		$this->makeWorkflowStage();
		//$this->makeWorkflows();
		//菜单和按钮
		$saction=Vtiger_Action_Model::$standardActions;
		$uaction=Vtiger_Action_Model::$utilityActions;
			
		//缓存模块信息
		$sql = "select * FROM vtiger_tab order by parentsequence,sequence";
		$result = $adb->pquery($sql);
		$noOfresult = $adb->num_rows($result);
		$code = "array(";
		for ($i=0; $i<$noOfresult; ++$i) {
			$model = $adb->fetchByAssoc($result);
			$tab[$model['tabid']]=$model['name'];
			if($model['name']=='Home'){
				$home=	$model['tabid'];
			}
			$code .="'".$model['name']."'=>array('tabid'=>".$model['tabid'].",'name'=>'".$model['name']."','tablabel'=>";
			$code .=returntypevalue($model['tablabel']);
			$code .=",'version'=>".returntypevalue($model['version']);
			$code .=",'presence'=>".returntypevalue($model['presence']);
			$code .=",'ownedby'=>".returntypevalue($model['ownedby']);
			$code .=",'tabsequence'=>".returntypevalue($model['tabsequence']);
			$code .=",'parent'=>".returntypevalue($model['parent']);
			$code .=",'parentsequence'=>".returntypevalue($model['parentsequence']);
			$code .=",'sequence'=>".returntypevalue($model['sequence']);
			$code .=",'customized'=>".returntypevalue($model['customized']);
			$code .=",'withupdate'=>".returntypevalue($model['withupdate']);
			$code .=",'isentitytype'=>".returntypevalue($model['isentitytype']).'),';
			
			$file=json_encode(Vtiger_Language_Handler::export($model['name'], 'jsLanguageStrings'));
			
			$handle=@fopen($root_directory.'libraries/jquery/posabsolute-jQuery-Validation-Engine/'.$model['name'].'.js',"w+");
			if($handle){
				$newbuf ='var '.$model['name'].'language='.$file;
				fputs($handle, $newbuf);
				fclose($handle);
			}
		}
		
		$dir_handle=opendir($root_directory.'languages/zh_cn/Settings/');
		while($file=readdir($dir_handle)){
			if($file!='.'&&$file!='..'&& $file!='.svn'){
			$name=explode('.',$file);
			if($name[1]=='php'){
				$file=json_encode(Vtiger_Language_Handler::export($name[0],'jsLanguageStrings'));
				$handle=@fopen($root_directory.'libraries/jquery/posabsolute-jQuery-Validation-Engine/'.$name[0].'.js',"w+");
				if($handle){
					$newbuf ='var '.$name[0].'language='.$file;
					fputs($handle, $newbuf);
					fclose($handle);
				}
			}
			}
		}
		
		$handle=@fopen($root_directory.'crmcache/modelinfo.php',"w+");
		if($handle){
			$newbuf ="<?php\n\n";
			$newbuf .= '$modelinfo='.$code.");\n";
			$newbuf .= "?>";
			fputs($handle, $newbuf);
			fclose($handle);
		}
		
		
		
		
		//缓存访问限制	（所有菜单）	
	 	$sql = "select * FROM vtiger_profile2standardpermissions where permissions!=0";
        $result = $adb->pquery($sql);
		$noOfresult = $adb->num_rows($result);
		$roles=array();
		for ($i=0; $i<$noOfresult; ++$i) {
			$role = $adb->fetchByAssoc($result);
			$access=$tab[$role['tabid']].'/'.$saction[$role['operation']];
			$roles[$role['profileid']][$access]=$role['permissions'];
		}
	
		//所有按钮
		$sql = "select profileid,tabid,activityid FROM vtiger_profile2utility where permission=1";
        $result = $adb->pquery($sql);
		$noOfresult = $adb->num_rows($result);
		//$roles=array();
		for ($i=0; $i<$noOfresult; ++$i) {
			$role = $adb->fetchByAssoc($result);
			$access=$tab[$role['tabid']].'/'.$uaction[$role['activityid']];
			$roles[$role['profileid']][$access]=1;
		}
		
		
		
		//按角色缓存页面权限规则
		//废弃已删除的配置
		$sql = "select profileid,tabid from vtiger_profile2tab where permissions=1 and profileid in(select profileid from vtiger_profile)";
		$result = $adb->pquery($sql);
		$num_rows = $adb->num_rows($result);
		$copy = array();
		$field2profile=array();
		for($i=0; $i<$num_rows; $i++){
		$tabpro=$adb->fetch_array($result);
		
		//$field2profile[$tabpro['profileid']]='';
		$copy[$tabpro['profileid']][]=$tabpro['tabid'];
		
		}
	
		//定位角色模块权限
		$sql = "select * from vtiger_role2profile";
		$result = $adb->pquery($sql);
		$num_rows=$adb->num_rows($result);
		$actionPermission=array();
		$tabPermission=array();
		for($i=0;$i<$num_rows;$i++){
			$profArr= $adb->fetch_array($result);
			if(empty($actionPermission[$profArr['roleid']])){
				$actionPermission[$profArr['roleid']]=$roles[$profArr['profileid']];	
			}else{
				foreach ($roles[$profArr['profileid']] as $key=>$val){
					if(empty($actionPermission[$profArr['roleid']][$key]) || $actionPermission[$profArr['roleid']][$key]<$val){
						$actionPermission[$profArr['roleid']][$key]=$val;
					}
				}	
			}
			$tabPermissions[$profArr['roleid']][]=$profArr['profileid'];
			if(empty($tabPermission[$profArr['roleid']])){
				if(!in_array($home,$copy[$profArr['profileid']])){
					$copy[$profArr['profileid']][]=$home;
				}
				
				$tabPermission[$profArr['roleid']]=$copy[$profArr['profileid']];		
			}else{
				$tabPermission[$profArr['roleid']]=array_merge($tabPermission[$profArr['roleid']],$copy[$profArr['profileid']]);
			}
		
		}
		$readonly=array(
			'readonly'=>'SELECT vtiger_field.fieldid FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid WHERE vtiger_field.tabid in(tabids) AND vtiger_profile2field.visible=0 AND vtiger_profile2field.profileid =profiles AND vtiger_field.presence in (0,2)',
			'noreadonly'=>'SELECT vtiger_field.fieldid FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid WHERE vtiger_field.tabid in(tabids) AND vtiger_profile2field.visible=0 AND vtiger_profile2field.readonly=0 AND vtiger_profile2field.profileid =profiles AND vtiger_field.presence in (0,2)'
		);
		$modulePermission = array();
		foreach($copy as $key=>$value){
            //$params = array(, $key);
			foreach($readonly as $k=>$query){
				$query=str_replace('tabids',implode(',',$value),$query);
				$query=str_replace('profiles',$key,$query);
				$result = $adb->pquery($query);
				
			
				
                $noOfFields = $adb->num_rows($result);
                for ($i = 0; $i < $noOfFields; ++$i) {
                    $row = $adb->query_result_rowdata($result, $i);
                    $modulePermission[$key][$k][] = $row['fieldid'];
                }
			
			}
			
			
			 
                //Vtiger_Cache::set('modulePermission-'.$accessmode,$tabid,$modulePermission);
		}
		
		//print_r($modulePermission);
		//exit;
		$fields=array();
		//循环所有角色下包含的权限组
		foreach($tabPermissions as $key=>$profiles){
			$fields[$key]=array('readonly'=>array(),'noreadonly'=>array());
			foreach($profiles as $profile){
				if(empty($fields[$key]['readonly'])){
					$fields[$key]['readonly']=$modulePermission[$profile]['readonly'];
					$fields[$key]['noreadonly']=$modulePermission[$profile]['noreadonly'];
				}else{
					$fields[$key]['readonly']=array_merge($fields[$key]['readonly'],$modulePermission[$profile]['readonly']);
					$fields[$key]['noreadonly']=array_merge($fields[$key]['noreadonly'],$modulePermission[$profile]['noreadonly']);
				}
			}
			
			
			
			$handle=@fopen($root_directory.'crmcache/fields/fieldsPermission'.$key.'.php',"w+");
			if($handle){
				$newbuf ="<?php\n\$fieldsPermission=array('readonly'=>array(".implode(',',array_unique($fields[$key]['readonly']))."),'noreadonly'=>array(".implode(',',array_unique($fields[$key]['noreadonly']))."));\n?>";
				fputs($handle, $newbuf);
				fclose($handle);
			}
		}
        //return;
        $query='SELECT companyid FROM vtiger_users WHERE companyid>0 GROUP BY companyid';
        $result1 = $adb->pquery($query,array());
        $noOfresult = $adb->num_rows($result1);
        $usercompany=array();
        if($noOfresult){
            while($row=$adb->fetch_array($result1)){
                $usercompany[]=$row['companyid'];
            }
        }
        $query='SELECT company_codeno,companyid FROM `vtiger_company_code`';
        $result_companycode = $adb->pquery($query,array());
        $company_codeno=array();
        if($adb->num_rows($result_companycode)){
            $flag=array();
            while($row=$adb->fetch_array($result_companycode)){
                if(!in_array($row['company_codeno'],$flag)){
                    $flag[]=$row['company_codeno'];
                    $company_codeno[$row['company_codeno']]=$row['companyid'];
                }else{
                    if(in_array($row['companyid'],$usercompany)){
                        $company_codeno[$row['company_codeno']]=$row['companyid'];
                    }
                }

            }
        }
		//缓存分组与部门数据共享规则
		$sql1='SELECT * FROM `vtiger_datashare_module_rel` RIGHT JOIN `vtiger_datashare_role2depart` on vtiger_datashare_module_rel.shareid=vtiger_datashare_role2depart.shareid WHERE vtiger_datashare_module_rel.shareid>=0';
		$result1 = $adb->pquery($sql1,array());
		$noOfresult = $adb->num_rows($result1);
		$code = array();
		$company = array();
		for ($i=0; $i<$noOfresult; ++$i) {
			$model = $adb->fetchByAssoc($result1);
			$action=$tab[$model['tabid']];
			if(empty($code[$model['share_roleid']][$action][$model['to_departmentid']])){
				$code[$model['share_roleid']][$action][$model['to_departmentid']]=$model['permission'];
				if(!empty($model['companyid'])){
				    $ccompanyid=explode(',',$model['companyid']);
				    foreach($ccompanyid as $values){
                        $company[$model['share_roleid']][$action][$values]=$company_codeno[$values];
                    }
                }
			}
		} 
		$handle=@fopen($root_directory.'crmcache/groupshare.php',"w+");
		if($handle){
				$newbuf ="<?php\n\$groupshare=".var_export($code,true)."\n?>";
				fputs($handle, $newbuf);
				fclose($handle);	
		}
        $code=null;
        $code=null;
        $handle=@fopen($root_directory.'crmcache/groupsharecompany.php',"w+");
        if($handle){
            $newbuf ="<?php\n\$groupsharecompany=".var_export($company,true)."\n?>";
            fputs($handle, $newbuf);
            fclose($handle);
        }
        $company=null;
        $company=null;
		foreach($actionPermission as $key=>$array){
			$handle=@fopen($root_directory.'crmcache/actionPermission'.$key.'.php',"w+");
			if($handle){
				$newbuf ="<?php\n\$actionPermission=array(";
				if(!empty($array)){
					foreach($array as $k=>$v){
						$newbuf .="'".$k."'=>".$v.',';
					}
				}
				$newbuf .= ");\n?>";
				fputs($handle, $newbuf);
				fclose($handle);
			}
		}
		
		
		$handle=@fopen($root_directory.'crmcache/tabPermission.php',"w+");
		if($handle){
			$newbuf ="<?php\n\n\$tabPermission=array(";
		foreach($tabPermission as $key=>$val){
				$newbuf .="'".$key."'=>array(".implode(',',array_unique($val)).'),';
			}
			$newbuf .= ");\n?>";
			fputs($handle, $newbuf);
			fclose($handle);
		}
		
		//缓存人员与分组关联
		$sql1='SELECT GROUP_CONCAT(groupid) as groupid,userid FROM `vtiger_users2group` GROUP BY userid;';
		$result1 = $adb->pquery($sql1);
		$noOfresult = $adb->num_rows($result1);
		$code = array();
		for ($i=0; $i<$noOfresult; ++$i) {
			$model = $adb->fetchByAssoc($result1);
			$code[$model['userid']]=$model['groupid'];
		}
		$handle=@fopen($root_directory.'crmcache/user2group.php',"w+");
		if($handle){
				$newbuf ="<?php\n\$user2group=".var_export($code,true)."\n?>";
				fputs($handle, $newbuf);
				fclose($handle);	
		}

		
		//按汇报上级确认直属关系   只接受二级分组防止死循环
		$sql = "select id,last_name,status,reports_to_id,companyid FROM vtiger_users where reports_to_id>0";
		$result = $adb->pquery($sql,array());
		$noOfresult = $adb->num_rows($result);
		$reports=array();
		$usercache='$usercache=array(';
        $companyid=array();
		for ($i=0; $i<$noOfresult; ++$i) {
			$list = $adb->fetchByAssoc($result);
				if(empty($reports[$list['reports_to_id']])){
					$reports[$list['reports_to_id']]=array($list['id']);
				}else{
					$reports[$list['reports_to_id']][]=$list['id'];
				}
            $usercache.=$list['id']."=>array('".$list['last_name']."','".$list['status']."'),";
            if(is_numeric($list['companyid'])){
                $companyid[$list['companyid']][]=$list['id'];
            }
		}
        //本公司
        $user2company="<?php\n\$user2company=array(";
        foreach($companyid as $key=>$value){
            $user2company.="'".$key."'=>'".implode(',',$value)."',";
        }
        $handle=@fopen($root_directory.'crmcache/user2company.php',"w+");
        if($handle){
            $newbuf =$user2company.");\n?>";
            fputs($handle, $newbuf);
            fclose($handle);
        }
        $companyid=null;
        $companyid=null;
        $user2company=null;
        $user2company=null;
	function get_mychilddep($list,$i=0) {
		
		global $child;
		global $childlist;
		if(empty($childlist)){
			$childlist=$list;
		}
        here:
		/*if($i>3000){
			return $childlist;
		}else{
			$i++;
		}*/
		foreach($childlist as $key => $value){
			foreach($value as $val){
				if(!empty($list[$val])  && empty($child[$key.'-'.$val])){
					$childlist[$key] = array_merge($value,$list[$val]);
					$child[$key.'-'.$val]=1;
					goto here;
					//get_mychilddep($childlist,$i);
				}
			}	
		}
		return $childlist;  	
	}
	
	$reports= get_mychilddep($reports);
		$code="<?php\n\$subordinate_users=array(";
				foreach($reports as $key=>$array){
					/*foreach($array as $val){
						if(!empty($reports[$val])){
							$reports[$key][]=implode(',',$reports[$val]);
						}
						}*/
					$code.=$key.'=>array('.implode(',',$reports[$key]).'),';
				}
		
				$code.= ");\n?>";
        $reports=null;
        $reports=null;
		$handle=@fopen($root_directory.'crmcache/subordinateusers.php',"w+");
		if($handle){
			fputs($handle, $code);
			fclose($handle);
		}
		
		$handle=@fopen($root_directory.'crmcache/usercache.php',"w+");
		if($handle){
			fputs($handle,  "<?php\n".$usercache.");\n?>");
			fclose($handle);
		}
		
		
		
		
		//缓存部门信息(部门上下级遍历)
		$result=$adb->pquery('select departmentId,departmentname,parentdepartment from vtiger_departments');
		$noOfresult = $adb->num_rows($result);
		$departments = array();
		$level='$departlevel=array(';
        	$departmenttoparent='$departmenttoparent=array(';
        	$departmentinfo=array();
		$cachedepartment='$cachedepartment=array(';
        $parentdepartment='';
		for ($i=0; $i<$noOfresult; ++$i) {
			$department = $adb->fetch_array($result);
			$cachedepartment.="'".$department[0]."'=>'".$department[1]."',";
			$departmentnames[$department[0]]=$department[1];
			$children=explode('::',$department[2]);
			$key="'".str_replace(array('::','H'), '0', $department[2])."'";
			$departmentinfo[$key]=$department;
            $parentdepartment[$department[0]]=array('departmentname'=>$department[1],'parentdepartment'=>$department[2]);
			//$link=(count($children)>1)?'└':'';
			//$level.="'".$department[0]."'=>'".str_repeat("&nbsp;",count($children)*3).$link.$department[1]."',";
			foreach($children as $val ){
				$departments[$val][]=$department[0];
			}
            	$departmenttoparent.="'".$department[0]."'=>'".$department[2]."',";
        }
		ksort($departmentinfo);
        $cacheparentdepartmentstr=");\n\$cacheparentdepartment=array(";
		foreach ($departmentinfo as $v){
			$children=explode('::',$v[2]);
			$link=(count($children)>1)?'|':'';
			$linkvalue=$link.str_repeat("—",(count($children)-1)).$v[1];
			$level.="'".$v[0]."'=>'".$linkvalue."',";
            $tempdeparnetstr='';
            foreach($children as $valuedeparnet){
                if($valuedeparnet=='H1'){
                    continue;
                }
                $tempdeparnetstr.=$departmentnames[$valuedeparnet].'--';
            }
            $tempdeparnetstr=trim($tempdeparnetstr,'--');
            $cacheparentdepartmentstr.="'".$v[0]."'=>array('departmentname'=>'".$linkvalue."','parentdepartmentname'=>'".$tempdeparnetstr."'),";
				
		}
		//缓存人员部门归属信息
		$result=$adb->pquery('select group_concat(userid),departmentid from vtiger_user2department group by departmentid');
		$noOfresult = $adb->num_rows($result);
		$user2departments = array();
		$user2departmentname="<?php\n\$user2departmentname=array(";
		$all=array();
		for ($i=0; $i<$noOfresult; ++$i) {
			$user = $adb->fetch_array($result);
			$all[$user[1]]=$user[0];
			$user2=explode(',',$user[0]);
			foreach($user2 as $uid){
				$user2departmentname.=$uid."=>'".$departmentnames[$user[1]]."',";
			}
			
			
		}
		
		$code = "\$departmentinfo=array(";
		foreach ($departments as $key => $val){
			$code .= "'".$key."'=>array('".implode("','",$val)."'),";
			$temp=array();
			foreach($val as $v){
				if(!empty($all[$v])){
					$temp[]=trim($all[$v],',');
				}
			}
			if(!empty($temp)){
				$user2departments[$key]=implode(',',$temp);
			}
		}
		$code .= ");\n\$user2departmentinfo=array(";
		
		foreach ($user2departments as $key => $val){
			$code .= "'".$key."'=>'".$val."',";
		}
		$code .= ");\n".$level;
        $code .=$cacheparentdepartmentstr;
		
		
		$handle=@fopen($root_directory.'crmcache/departmentanduserinfo.php',"w+");
		if($handle){
			$newbuf ="<?php\n\n";	
			$newbuf .=$cachedepartment.");\n";
            $newbuf .=$departmenttoparent.");\n";
            $newbuf .= $code.");\n";
			$newbuf .= "?>";
			fputs($handle, $newbuf);
			fclose($handle);
		}
		
		$handle=@fopen($root_directory.'crmcache/user2departmentname.php',"w+");
		if($handle){
			$user2departmentname .= ");\n?>";
			fputs($handle, $user2departmentname);
			fclose($handle);
		}
		
		
		
		
		//echo '缓存更新成功！';

		
		//缓存角色上下级		
 		$sql = "select roleid,rolename,parentrole FROM vtiger_role";
        $result = $adb->pquery($sql);
		$noOfresult = $adb->num_rows($result);
		$roles=array();
		for ($i=0; $i<$noOfresult; ++$i) {
			$role = $adb->fetchByAssoc($result);
			$key="'".str_replace(array('::','H'), '0', $role['parentrole'])."'";
			$role_info[$key]=$role;
			/* $temp=str_replace($role['roleid'],'',$role['parentrole']);
			$link='';
			if(!empty($temp)){
				$array=explode('::',$temp);
				if(!empty($array)){
					$link=str_repeat("&nbsp;",count($array)*3).'└';
				}
			}
			$roles[]="'".$role['roleid']."'=>'".$link.$role['rolename']."'"; */
		}
		
		
		ksort($role_info);
		foreach ($role_info as $v){
			$children=explode('::',$v['parentrole']);
			$link=(count($children)>1)?'|':'';
			//$level.="'".$v[0]."'=>'".$link.str_repeat("—",(count($children)-1)).$v[1]."',";
			$roles[]="'".$v['roleid']."'=>'".$link.str_repeat("—",(count($children)-1)).$v['rolename']."'";
				
		}
		//exit;
		$code = "<?php\n\n\$roles=array(".implode(",",$roles).");\n?>";
		$handle=@fopen($root_directory.'crmcache/role.php',"w+");	
		if($handle){
			fputs($handle, $code);
			fclose($handle);
		} 
		
		
		$this->cache_depart_share($tab,$user2departments);
		
		
		
		//缓存表
		$res = $adb->pquery('select * from vtiger_ws_entity', array());
		$entity=array();
		$vtiger_ws_entity=array();
		while($row = $adb->fetchByAssoc($res)){
			$entity[$row['ismodule']][]=$row['name'];
			$vtiger_ws_entity[$row['name']]=$row;
			/* if($row['ismodule'] == '1'){
				$module.=str_replace(',',"','",$row['wsname'])."')";
			}else{
				$entity.=str_replace(',',"','",$row['wsname'])."')";
			} */
		}
		
		
		
		$handle=@fopen($root_directory.'crmcache/ws_entity_export.php',"w+");	
		if($handle){
			fputs($handle, '<?php $ws_entitys='.var_export($vtiger_ws_entity,true).'; ?>');
			fclose($handle);
		} 
		$handle=@fopen($root_directory.'crmcache/ws_entity.php',"w+");	
		if($handle){
			fputs($handle, '<?php $ws_entity=array('."'module'=>array('".implode("','",$entity[1])."'),'entity'=>array('".implode("','",$entity[0])."')); ?>");
			fclose($handle);
		} 
		/* $module="'module'=>array('";
		$entity="'entity'=>array('";
		
		$code = "<?php\n\n\$ws_entity=array(".$module.','.$entity.");\n?>";
		$handle=@fopen($root_directory.'crmcache/ws_entity.php',"w+");	
		if($handle){
			fputs($handle, $code);
			fclose($handle);
		}  */
		//产品负责人对应关系(不包含套餐)
		$result=$adb->pquery('select productid,productman from vtiger_products where productman is not null and productid not in(SELECT DISTINCT productid FROM `vtiger_seproductsrel` where setype=\'Products\')');
		$noOfresult = $adb->num_rows($result);
		$user2product=array();
		for ($i=0; $i<$noOfresult; ++$i) {
			$productid = $adb->fetch_array($result);
			$userid=explode(' |##| ', $productid['productman']);
			foreach ($userid as  $id) {
				if(!isset($user2product[$id])){
					$user2product[$id]=$productid['productid'];
				}else{
					$user2product[$id].=','.$productid['productid'];
				}
				
			}
		}

		$hander=@fopen($root_directory.'crmcache/user2product.php', 'w+');
		if($hander){
			$str="<?php\n\n";
			$str.="\$user2product=".var_export($user2product,true).";\n?>";
			fputs($hander, $str);
			fclose($hander);
		}
		
		
		
		
		
		
		
		
		
		
		
		
		/*$memcache_obj = memcache_connect('127.0.0.1', 11211);
		memcache_flush($memcache_obj);
		memcache_close($memcache_obj);*/
		
		
		
		global $currentModule;
		if($currentModule=='Cacheinfo'){
			echo '<p style="text-align:center;padding-top:300px;color:red;">缓存更新成功！</p>';
		}else{
			echo '失败';
		}
		//exit;
		
		
		
	/* 	$sql = "select * FROM vtiger_actionmapping where securitycheck=1";
        $result = $adb->pquery($sql);
		$noOfresult = $adb->num_rows($result);
		$roles=array();
		for ($i=0; $i<$noOfresult; ++$i) {
			$role = $adb->fetchByAssoc($result);
			print_r($role);
		}
		exit; */	
		//缓存角色上下级		
/* 		$sql = "select roleid,parentrole FROM vtiger_role";
        $result = $adb->pquery($sql);
		$noOfresult = $adb->num_rows($result);
		$roles=array();
		for ($i=0; $i<$noOfresult; ++$i) {
			$role = $adb->fetchByAssoc($result);
			$temp=str_replace($role['roleid'],'',$role['parentrole']);
			if(!empty($temp)){
				$array=explode('::',$temp);
				if(!empty($array)){
					foreach($array as $val){
						if(!empty($val) && !in_array($val,$roles)){
							$roles[]=$val;
						}
					}
				}	
			}
		}
		$code = "<?php\n\n\$ifparentrole=array('".implode("','",$roles)."');\n?>";
		$handle=@fopen($root_directory.'crmcache/ifparentrole.php',"w+");	
		if($handle){
			fputs($handle, $code);
			fclose($handle);
		} */
		
		
		// TODO This is temporarily required, till we provide a hook/entry point for Emails module.
		// Once that is done, Webmails need to be removed permanently.
		//邮件模块原来是临时处理，暂不加入
		/* $emailsTabId = getTabid('Emails');
		 $webmailsTabid = getTabid('Webmails');
		if(array_key_exists($emailsTabId, $copy)) {
		$copy[$webmailsTabid] = $copy[$emailsTabId];
		} */
		
				
	}
	
	
	static public function set_infocache($sql,$filename){
	
	}
	/**
	 * 生成阶段角色关系
	 */
	public function makeWorkflows(){
		$arrDepend=array('');
		$this->cacheDependency($arrDepend);
		//缓存流程角色信息
		$adb=PearDatabase::getInstance();
		$result=$adb->pquery("select workflowstagesid,handleaction,isrole,workflowsid from vtiger_workflowstages where workflowsid !='' and isrole is not null and isrole !=''",array());
		//ProductCheck
		$roleandworkflows=array();
		$roleandworkflowsofproduct=array();
		
		$roleandworkflowsstages=array();
		$roleandworkflowsstagesofproduct=array();
		if($adb->num_rows($result)){
			while($row=$adb->fetch_array($result)){
				if(!empty($row['isrole'])){
					$isroles=explode(' |##| ', $row['isrole']);
					if(!empty($isroles)){
						foreach($isroles as $r){
							if($row['handleaction']!='ProductCheck'){
								if(!empty($roleandworkflows[$r])){
									$roleandworkflows[$r].=','.$row['workflowsid'];
								}else{
									$roleandworkflows[$r]=$row['workflowsid'];
								}
								if(!empty($roleandworkflowsstages[$r])){
									$roleandworkflowsstages[$r].=','.$row['workflowstagesid'];
								}else{
									$roleandworkflowsstages[$r]=$row['workflowstagesid'];
								}
							}else{
								if(!empty($roleandworkflowsofproduct[$r])){
									$roleandworkflowsofproduct[$r].=','.$row['workflowsid'];
								}else{
									$roleandworkflowsofproduct[$r]=$row['workflowsid'];
								}
								if(!empty($roleandworkflowsstagesofproduct[$r])){
									$roleandworkflowsstagesofproduct[$r].=','.$row['workflowstagesid'];
								}else{
									$roleandworkflowsstagesofproduct[$r]=$row['workflowstagesid'];
								}

							}
							
							
						}
					}
				}
			}
		}
		
		$hander=@fopen($root_directory.'crmcache/roleandworkflows.php', 'w+');
		if($hander){
			$str="<?php\n\n";
			$str.="\$roleandworkflows=".var_export($roleandworkflows,true).";\n";
			$str.="\$roleandworkflowsstages=".var_export($roleandworkflowsstages,true).";\n";
			$str.="\$roleandworkflowsofproduct=".var_export($roleandworkflowsofproduct,true).";\n";
			$str.="\$roleandworkflowsstagesofproduct=".var_export($roleandworkflowsstagesofproduct,true).";\n";

			$str.="?>";
			fputs($hander, $str);
			fclose($hander);
		}
	}
	/**
	 * 生成流程数据
	 * 路径:$cache_dir.'workflows'.workflowsid.php
	 */
	public function makeWorkflowStage(){
		$arrDepend=array('Workflows');
		$this->cacheDependency($arrDepend);
		global $root_directory,$adb;
		
		$path=$root_directory.'crmcache/workflows/';
		$sql="select * from vtiger_workflows ORDER BY workflowsid desc";
		$result=$adb->pquery($sql,array());
		if($adb->num_rows($result)){
			while($workflow=$adb->fetch_array($result)){
				$sql="select * from vtiger_workflowstages where workflowsid=? ORDER BY sequence asc";
				$resulta=$adb->pquery($sql,array($workflow['workflowsid']));
				$parentstd=array();
				$temp=$workflow;
					
				while($row=$adb->fetch_array($resulta)){
					$parentstd[$row['sequence']]=$row;
					$std=array();
					$nostd=array();
					if(!empty($row['subworkflowsid'])){
						$subresult=$adb->pquery('select * from vtiger_workflowstages where workflowsid=? ORDER BY workflowsid desc,sequence asc',array($row['subworkflowsid']));
						if($adb->num_rows($subresult)){
							while($subrow=$adb->fetch_array($subresult)){
								//if($subrow['isproductmanger']==0){
								//	$std[$subrow['sequence']]=$subrow;
								//}
								$nostd[$subrow['sequence']]=$subrow;
							}
						}
					}
					//$parentstd[$row['sequence']]['std']=$std;
					$parentstd[$row['sequence']]['nostd']=$nostd;				
				}
				$temp['stage']=$parentstd;
				
				
				$hander=@fopen($path.$workflow['workflowsid'].'.php', 'w+');
				if($hander){
					$str="<?php\n\n";
					$str.="\$workflows=".var_export($temp,true).";\n";
					$str.="?>";
					fputs($hander, $str);
					fclose($hander);
				}
			}
			
		}
	}
	
	/**
	 * 生成依赖关系的缓存数据格式make+模块名称
	 * @param unknown $arr
	 */
	public function cacheDependency($arr){
		if(is_array($arr)&&count($arr)>0){
			foreach($arr as $cache){
				$fun='make'.$cache;
				call_user_func(array($this, $fun));
			}
		}else{
			if(!empty($arr)){
				$fun='make'.$arr;
				call_user_func(array('Settings_Cacheinfo_Index_View', $fun));
			}else{
				//throw new AppException('缓存依赖生成异常');
			}
		}
	}
	/**
	 * 生成所有缓存
	 */
	public function makeAllCache(){
		
	}
	
	
	/**
	*缓存部门共享
	*/
	public function cache_depart_share($tab,$user2departments){
		global $root_directory,$adb;
		$sharesql = "SELECT tabid,share_roleid,to_departmentid FROM `vtiger_datashare_module_rel` LEFT JOIN vtiger_datashare_role2depart on vtiger_datashare_module_rel.shareid=vtiger_datashare_role2depart.shareid";
		$result_share = $adb->pquery($sharesql);
		$no_result = $adb->num_rows($result_share);
		if($no_result<1){
			return false;
		}
		for ($i=0; $i<$no_result; ++$i) {
			$share = $adb->fetchByAssoc($result_share);
			/*if(empty($share_access[$tab[$share['tabid']]][$share['share_roleid']])){
				$share_access[$tab[$share['tabid']]][$share['share_roleid']]=$user2departments[$share['to_departmentid']];
			}else{
				$share_access[$tab[$share['tabid']]][$share['share_roleid']].=','.$user2departments[$share['to_departmentid']];
			}*/
            if(empty($share_access[$tab[$share['tabid']]][$share['share_roleid']])){
                $userids=explode(',',$user2departments[$share['to_departmentid']]);
                $userids=array_unique($userids);
            }else{
                $share_access[$tab[$share['tabid']]][$share['share_roleid']].=','.$user2departments[$share['to_departmentid']];
                $userids=explode(',',$share_access[$tab[$share['tabid']]][$share['share_roleid']]);
                $userids=array_unique($userids);
            }
            $share_access[$tab[$share['tabid']]][$share['share_roleid']]=implode(',',$userids);
			//$roles[$role['profileid']][$access]=$role['permissions'];
		}
		//print_r($share_access);
		//return;
		$array='';
		foreach ($share_access as $key=>$val){
			if(!empty($val)){
				$str='array(';
				foreach($val as $k=>$v){
					if(!empty($v)){
						$str.="'".$k."'=>'".$v."',";
					}
					
				}
				
			}
			if($str!='array('){
				$array.="'".$key."'=>".$str."),";
			}
			
		}
		$handle=@fopen($root_directory.'crmcache/departmentshare.php',"w+");
		if($handle){
			$newbuf ="<?php\n\n\$departmentshare=array(";	
			$newbuf .= $array.");\n";
			$newbuf .= "?>";
			fputs($handle, $newbuf);
			fclose($handle);
		}
		
		
		
		
	} 
}
