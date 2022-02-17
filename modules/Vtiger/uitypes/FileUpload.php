<?php
class Vtiger_FileUpload_UIType extends Vtiger_Base_UIType {
	public function getTemplateName() {
		return 'uitypes/FileUpload.tpl';
	}

	public function getDisplayValue($value, $recordId=false, $recordModel=false) {
		global $currentModule;
        $newvalue=explode('*|*',$value);
        if(!empty($newvalue[0])){
            $str='';
            foreach($newvalue as $val){
                $val=explode('##', $val);
                if(count($val)>1){
                    $fileid=array_pop($val);
                    $str.='<a href=index.php?module='.$currentModule.'&action=DownloadFile&filename='.urlencode(base64_encode($fileid)).'>'.implode('##',$val).'</a><span title="双击进行修改">&nbsp;&nbsp;&nbsp;&nbsp;</span><br>';
                }else{
                    $str.=$val[0];
                }
            }
            return $str;
        }else{
            return $newvalue[0];
        }
		/*$value=explode('##', $value);
		if(count($value)>1){
			$fileid=array_pop($value);
			return '<a href=index.php?module='.$currentModule.'&action=DownloadFile&filename='.urlencode(base64_encode($fileid)).'>'.implode('##',$value).'</a>';
		}else{
			return $value[0];
		}*/
		
		
	}
}