<?php
/*+********
 *客户信息管理
 **********/

class WorkSummarize_Record_Model extends Vtiger_Record_Model {
	static public function getLastRecord(){
		global $current_user;
		$db=Peardatabase::getInstance();
		$sql="SELECT * FROM vtiger_worksummarize WHERE ";
		if($_REQUEST['record']>0){
			$sql.=" worksummarizeid<{$_REQUEST[record]} "; 
			$sql.=" AND smownerid=(select smownerid FROM vtiger_worksummarize WHERE worksummarizeid ={$_REQUEST[record]})";
		}else{
			$sql.=" smownerid='{$current_user->id}' ";
		}
		
		$sql.= " ORDER BY createdtime DESC LIMIT 1;";
		//echo $sql;
		$result=$db->run_query_allrecords($sql);
		
		if(empty($result)){
			return '无';
		}
		return $result[0]['tommorrowcontent'];
		
	} 
	static public function getNoWrite(){
		$db=PearDatabase::getInstance();
        global $current_user;
		$where=getAccessibleUsers('WorkSummarize','List',true);
        if($where=='1=1' || empty($where)){
            //$where='='.$current_user->id;
            return array();
        }
        //是当前用户若无下级
        if(count($where)==1 && in_array($current_user->id,$where)){
            return array();
        }
        $arr1=WorkSummarize_Record_Model::getUserNowrite('nowrite');

        if(count($arr1['nowriteuserid'])>0){
            //直接求两个数组的差集
            $where=array_diff($where,$arr1['nowriteuserid']);
        }
        //如果差集为空返回0
        if(count($where)==0){
            return array();
        }

        //当为一条记录时判断该记录是否是当前登陆人如果是则直接返回0
        if(count($where)==1 && in_array($current_user->id,$where)){
            return array();
        }
		$sql="SELECT DISTINCT vtiger_users.last_name FROM vtiger_users LEFT JOIN vtiger_worksummarize ON vtiger_users.id = vtiger_worksummarize.smownerid
		WHERE	vtiger_users.status='Active' AND vtiger_users.id in(".implode(',',$where).") AND vtiger_users.id !={$current_user->id} ";
		if($_REQUEST['nowdate']!=''){
			$sql.="	AND vtiger_users.id not in(select smownerid FROM vtiger_worksummarize WHERE to_days('{$_REQUEST['nowdate']}')=to_days(vtiger_worksummarize.createdtime))";
		}else{
			$sql.="	AND vtiger_users.id not in(select smownerid FROM vtiger_worksummarize WHERE to_days(now())-to_days(vtiger_worksummarize.createdtime)=1)";
		}
		$result=$db->run_query_allrecords($sql);
        return $result;
	}
    //要回复的工作总结人员
	static public function getReply(){
		$db=PearDatabase::getInstance();
		$sql="SELECT
			vtiger_reply.replycontent,
			vtiger_reply.createdtime,
			vtiger_users.last_name
		FROM
			vtiger_reply
		LEFT JOIN vtiger_worksummarize ON vtiger_worksummarize.worksummarizeid = vtiger_reply.relatedid
		LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_reply.replyuser
		WHERE
			vtiger_reply.relatedid = {$_REQUEST['record']}
		ORDER BY
			vtiger_reply.createdtime DESC
		LIMIT 20";
		$result=$db->run_query_allrecords($sql);
		return $result;
	}
    //读取要设置工作总结的人员
    static public function getUserNowrite($type='get'){
        global $adb,$current_user;

        $where=getAccessibleUsers('WorkSummarize','List',false);
        if($where!='1=1'){
            if($type=='get'){
                $sql="SELECT id,last_name FROM vtiger_users WHERE `status`='active' AND id {$where}";
                $result['userid']=$adb->run_query_allrecords($sql);
            }
            $query="SELECT userid FROM `vtiger_worksummarizenowrite` WHERE uid={$current_user->id}";
            $data=$adb->query($query,array());
            foreach($data as $value){
                $result['nowriteuserid'][]=$value['userid'];
            }
            return $result;

        }
    }

    /**
     * 不用写工作总结人员设设置
     * @param string $fields
     */
    static public function setUserNowrite($fields=''){
        global $adb,$current_user;

        $sql="DELETE FROM `vtiger_worksummarizenowrite` WHERE uid={$current_user->id}";
        $adb->query($sql,array());
        if(!empty($fields)){
            $result='';
            foreach($fields as $value){
                $result.='('.$current_user->id.','.$value.'),';
            }
            $result=rtrim($result,',');
            $sql="INSERT INTO `vtiger_worksummarizenowrite` (uid,userid) VALUES {$result}";
            $adb->query($sql,array());
        }
    }
    /**
     *
     */
    static public function displayrose(){
        global $adb,$current_user;

        $where=getAccessibleUsers('WorkSummarize','List',true);
        if($where=='1=1' || empty($where)|| !is_array($where)){
           return false;
        }
        if(count($where)==1 && in_array($current_user->id,$where)){
            return false;
        }
        $sql="SELECT id,last_name FROM vtiger_users WHERE `status`='active' AND id IN (".implode(',',$where).")";
        $res=$adb->query($sql);
        $result=$adb->num_rows($res);
        if($result>1){
            return true;
        }else{
            return false;
        }

        return false;

    }
	
	
}
