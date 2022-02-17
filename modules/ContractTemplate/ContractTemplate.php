<?php
include_once('config.php');
require_once('include/logging.php');
require_once('modules/Contacts/Contacts.php');
require_once('modules/Potentials/Potentials.php');
require_once('modules/Calendar/Activity.php');
require_once('modules/Documents/Documents.php');
require_once('modules/Emails/Emails.php');
require_once('include/utils/utils.php');
require_once('user_privileges/default_module_view.php');

class ContractTemplate extends CRMEntity {
	var $log;
	var $db;
	var $table_name = "vtiger_contract_template";
	var $table_index= 'contract_templateid';
    var $tab_name_index = Array('vtiger_contract_template'=>'contract_templateid');//'vtiger_crmentity' => 'crmid',
	var $tab_name = Array('vtiger_contract_template');
	var $column_fields = Array();
    //var $entity_table = "vtiger_crmentity";
	var $sortby_fields = Array();
	var $list_fields = Array();
	var $list_fields_name = Array();

	var $required_fields =  array();
	var $mandatory_fields = Array();
	var $emailTemplate_defaultFields = array();
	var $default_sort_order = 'ASC';

	// For Alphabetical search
	var $related_module_table_index = array();
	var $def_basicsearch_col = 'contract_templateid';
	var $default_order_by = 'contract_templateid';
	var $list_link_field= 'contract_templateid';
	//关联模块的一些字段和数组;
	var $relatedmodule_list=array();
	var $relatedmodule_fields=array();
    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }
	/** Function to handle module specific operations when saving a entity
	*/
	function save_module($module){
        global $root_directory;
        $rootpath=$root_directory.'languages'.DIRECTORY_SEPARATOR.'zh_cn'.DIRECTORY_SEPARATOR.'ContractTemplate.php';
        $scontentpath=$root_directory.'languages'.DIRECTORY_SEPARATOR.'zh_cn'.DIRECTORY_SEPARATOR.'SContractNoGeneration.php';
        $filename=current($_REQUEST['file']);
        $filenumber=key($_REQUEST['file']);
        $path=$this->getFilePath($filenumber);
        $extnamearray=explode('.',$filename);
        $extname=end($extnamearray);
        if($path)
		{
			$path=$root_directory.$path;
			$data=file_get_contents($path);
			$basedata=base64_encode($data);
			$Sql="REPLACE INTO vtiger_templatecontent
						(contract_template,templatecontents,versn,extensionname)
						VALUES('".$_REQUEST['contract_template']."',
								'".$basedata."',
								'".$_REQUEST['version']."',
								'".$extname."')";
			$this->db->pquery($Sql,array());
		}
		$id=empty($_REQUEST['record'])?$this->id:$_REQUEST['record'];
        $sql='UPDATE `vtiger_contract_template` SET ctname=?,extensionname=? WHERE contract_templateid=?';
        $this->db->pquery($sql,array($filename,$extname,$id));
        $this->replaceTemplete($rootpath,$filename);
		$this->replaceTemplete($scontentpath,$filename);

    }

    /**
	 * 更改模板的翻译文件
     * @param $filepath
     */
    public function replaceTemplete($filepath,$filename)
	{
		$files=file_get_contents($filepath);
        $pattern="/'".$_REQUEST['contract_template']."'=>'\S.*',/";
        if(preg_match($pattern,$files))
        {

            $replace_str="'".$_REQUEST['contract_template']."'=>'".$filename."',";
        }
        else
		{
            $pattern='/\$languageStrings = array\(/';
			$replace_str="\$languageStrings = array(\n'".$_REQUEST['contract_template']."'=>'".$filename."',";
		}

        $data=preg_replace($pattern,$replace_str,$files);
        file_put_contents($filepath,$data);
	}

    /**
	 * 取模板文件的路径
     * @param $fileid
     * @return bool|string
     */
	public function getFilePath($fileid)
	{
        if($fileid>0){
            global $adb;
            $result = $adb->pquery("SELECT * FROM vtiger_files WHERE attachmentsid=?", array($fileid));
            if($adb->num_rows($result)) {
                $fileDetails = $adb->query_result_rowdata($result);
                $filePath = $fileDetails['path'];
                if($fileDetails['newfilename']>0){
                    $savedFile = $fileDetails['attachmentsid']."_".$fileDetails['newfilename'];
                    $fileName=$fileDetails['newfilename'];
                }else{
                    $fileName = html_entity_decode($fileDetails['name'], ENT_QUOTES, vglobal('default_charset'));
                    $t_fileName = base64_encode($fileName);
                    $t_fileName = str_replace('/', '', $t_fileName);
                    $savedFile = $fileDetails['attachmentsid']."_".$t_fileName;
                }

                if(!file_exists($filePath.$savedFile)){
                    $savedFile = $fileDetails['attachmentsid']."_".$fileName;
                }
                return $filePath.$savedFile;

            }

        }
        return false;
	}
}
?>
