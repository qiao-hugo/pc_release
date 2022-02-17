<?php
/*+***********************************************************************************
	*
	*文件功能说明：
	*时间：2014-12-25
	*作者：steel
	*该文件处理上传EXCEL的XLS和XLSX格式,导入数据库操作；
 *************************************************************************************/
require_once "libraries/PHPExcel/PHPExcel.php";
require_once "libraries/PHPExcel/PHPExcel/IOFactory.php";
class Import_XLSReader_Reader extends Import_FileReader_Reader {
	/**
	 * 调用文件上传文件的前两行，用来对应数据库表中的字段
	 * @see Import_FileReader_Reader::getFirstRowData()
	 */
	public function getFirstRowData($hasHeader=true) {
		global $default_charset;

		$filePath = $this->getFilePath();
		if(!file_exists($filePath)){
			return false;
		}
		$obj=PHPExcel_IOFactory::load($filePath);
		$arr=$obj->getSheet(0);
		
		$fieldValueMappings=$arr->toArray();
		if(count($fieldValueMappings)<2){
			return false;
		}
		$data=array_combine($fieldValueMappings[0], $fieldValueMappings[1]);
		return $data;
	}
	/**
	 * 导入的写入操作
	 * @see Import_FileReader_Reader::read()
	 */
	public function read() {
		require_once('includes/runtime/LanguageHandler.php');
		$db = PearDatabase::getInstance();
		//global $default_charset;

		$filePath = $this->getFilePath();
		$status = $this->createTable();
		if(!$status) {
			return false;
		}
		
		$fieldMapping = $this->request->get('field_mapping');

		$accountcategoryvalue=$this->request->get('accountcategory_defaultvalue');
		$obj=PHPExcel_IOFactory::load($filePath);
		$arr=$obj->getSheet(0);
		
		$data=$arr->toArray();
		unset($data[0]);
		for($i=1; $i<=count($data); ++$i) {
			
			$mappedData = array();
			$allValuesEmpty = true;
			foreach($fieldMapping as $fieldName => $index) {
				$fieldValue = $data[$i][$index];
				$mappedData[$fieldName] = $fieldValue;
				$default_charset= mb_detect_encoding($fieldValue);
				if($this->request->get('file_encoding') != $default_charset) {
					$mappedData[$fieldName] = $this->convertCharacterEncoding($fieldValue, $this->request->get('file_encoding'), $default_charset);
				}
				$mappedData[$fieldName]=str_replace("&",chr(34),$mappedData[$fieldName]);  
				$mappedData[$fieldName]=str_replace("#",'',$mappedData[$fieldName]);  
				$mappedData[$fieldName]=str_replace(">",">",$mappedData[$fieldName]);  
				$mappedData[$fieldName]=str_replace("<","<",$mappedData[$fieldName]);  
				$mappedData[$fieldName]=str_replace("&","&",$mappedData[$fieldName]);  
				$mappedData[$fieldName]=str_replace(" ",chr(32),$mappedData[$fieldName]);  
				$mappedData[$fieldName]=str_replace(" ",chr(9),$mappedData[$fieldName]);  
				$mappedData[$fieldName]=str_replace("'",chr(39),$mappedData[$fieldName]);  
				$mappedData[$fieldName]=str_replace("<br />",chr(13),$mappedData[$fieldName]);  
				$mappedData[$fieldName]=str_replace("''","'",$mappedData[$fieldName]);  
				$mappedData[$fieldName]=str_replace("select","select",$mappedData[$fieldName]);  
				$mappedData[$fieldName]=str_replace("join","join",$mappedData[$fieldName]);  
				$mappedData[$fieldName]=str_replace("union","union",$mappedData[$fieldName]);  
				$mappedData[$fieldName]=str_replace("where","where",$mappedData[$fieldName]);  
				$mappedData[$fieldName]=str_replace("insert","insert",$mappedData[$fieldName]);  
				$mappedData[$fieldName]=str_replace("delete","delete",$mappedData[$fieldName]);  
				$mappedData[$fieldName]=str_replace("update","update",$mappedData[$fieldName]);  
				$mappedData[$fieldName]=str_replace("like","like",$mappedData[$fieldName]);  
				$mappedData[$fieldName]=str_replace("drop","drop",$mappedData[$fieldName]);  
				$mappedData[$fieldName]=str_replace("create","create",$mappedData[$fieldName]);  
				$mappedData[$fieldName]=str_replace("modify","modify",$mappedData[$fieldName]);  
				$mappedData[$fieldName]=str_replace("rename","rename",$mappedData[$fieldName]);  
				$mappedData[$fieldName]=str_replace("alter","alter",$mappedData[$fieldName]);  
				$mappedData[$fieldName]=str_replace("cas","cast",$mappedData[$fieldName]);  
				//判断用户导入的保护模式只能是2(公海)或1(临时区)
                if($accountcategoryvalue<1){
                    if($fieldName=='accountcategory' && (int)$mappedData['accountcategory']==0){
                        $mappedData['accountcategory']=1;
                    }else if($mappedData['accountcategory']>2 || (int)$mappedData['accountcategory']<0){
                        $mappedData['accountcategory']=1;
                    }else{}
                    //如果用户导入0-2之间的小数处理后只有1或2其中业个结果
                    if($fieldName=='accountcategory'){
                        $mappedData['accountcategory']=ceil((int)$mappedData['accountcategory']);
                    }
                }else{
                    $mappedData['accountcategory']=(int)$accountcategoryvalue;
                }
                if($fieldName=='address'){
	                $mappedData['address']='###'.$mappedData['address'];
				}
				if($fieldName=='account_id'){
					$mappedData['account_id']='Accounts::::'.$mappedData['account_id'];
				}
				//导入客户不能为白名单
				if($fieldName=='protected'){
					$mappedData['protected']=0;
				}
				//导入客记不管多高级都为机会客户
				if($fieldName=='accountrank'){
					$mappedData['accountrank']='机会客户';
				}
				//将对应的中文转换为ID
				if($fieldName=='servicetype'){
					$temp=str_replace(',',"','",$mappedData['servicetype']);
					$temp="'".$temp."'";
					
					$result1= $db->run_query_allrecords("SELECT productid FROM vtiger_products WHERE productname in({$temp})");
					if(!empty($result1)){
						$string='';
						foreach($result1 as $v)
						$string.=$v['productid'].' |##| ';
						$mappedData['servicetype']=rtrim($string,' |##| ');
					}else{
						$mappedData['servicetype']='';
					}
				}
				//下列框过滤
				if($fieldName=='leadsource'||$fieldName=='customerproperty'||$fieldName=='makedecisiontype'||$fieldName=='industry'||$fieldName=='communication'){
					$result1= $db->run_query_allrecords("SELECT {$fieldName} FROM vtiger_{$fieldName}");
					$temp=array();
					$arr=array();
					if(!empty($result1)){
						foreach ($result1 as $v){
							$arr[]=$v[$fieldName];
							$temp[]=vtranslate($v[$fieldName],'Accounts');
						}
						if(in_array($mappedData[$fieldName],$temp)){
							$arr2=array();
							$arr2=array_flip($temp);
							$mappedData[$fieldName]=$arr[$arr2[$mappedData[$fieldName]]];
						}else{
							$mappedData[$fieldName]=$arr[0];
						}
					}else{
						$mappedData[$fieldName]='';
					}
					
				}
				
				//将对应的性别转换为
				if($fieldName=='gendertype'){
					if($mappedData['gendertype']=='男'){
						$mappedData['gendertype']='MALE';
					}else{
						$mappedData['gendertype']='FEMALE';
					}
				}
				if(!empty($fieldValue)) $allValuesEmpty = false;
			}
			
			if($allValuesEmpty) continue;
			$fieldNames = array_keys($mappedData);
			$fieldValues = array_values($mappedData);
			$this->addRecordToDB($fieldNames, $fieldValues);
		}
	}
	/**
	 * 该取上传文件的路径:
	 * @see Import_FileReader_Reader::getFilePath()
	 */
	public function getFilePath() {
		return Import_Utils_Helper::getImportFilePath($this->user);
	}
}
?>
