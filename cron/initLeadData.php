<?php
ini_set("include_path", "../");

require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");
//error_reporting(0);


include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';


vglobal('default_language', $default_language);
$currentLanguage = 'zh_cn';
vglobal('current_language', $currentLanguage);
global $adb;
global $root_directory;
$fileName=$root_directory.'lead.xlsx';
include_once $root_directory.'libraries/PHPExcel/PHPExcel.php';
$PHPReader = new PHPExcel_Reader_Excel2007();
if (!$PHPReader->canRead($fileName)) {
    $PHPReader = new PHPExcel_Reader_Excel5();
    if (!$PHPReader->canRead($fileName)) {
        echo '读取文件失败';
        die;
    }
}

$result2= $adb->pquery("select id,usercode from vtiger_users where id in(2357,9575,23553,2007,53,8610,16918,5068,6572,3549,17290,16565,3351,458,3275,20017,191,2295)",array());
while ($row=$adb->fetchByAssoc($result2)){
    $users[intval($row['usercode'])]=$row['id'];
}
echo '<pre>';
echo '用户信息';
var_dump($users);

$PHPExcel = $PHPReader->load($fileName);
$currentSheet = $PHPExcel->getSheet(0);
/**取得一共有多少列*/
$allColumn = $currentSheet->getHighestColumn();
echo $allColumn.'<br>';
/**取得一共有多少行*/
$allRow = $currentSheet->getHighestRow();
echo $allRow.'<br>';
$all = array();
$error = array();
$result=array();
for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
    $flag = 0;
    $col = array();
    for ($currentColumn = 'A'; getascii($currentColumn) <= getascii($allColumn); $currentColumn++) {
        $address = $currentColumn . $currentRow;
        $string = $currentSheet->getCell($address)->getValue();
        $col[$flag] = $string;
        $flag++;
    }
    echo "<pre>";
    var_dump($col);

    $userid= $users[intval($col[10])];
    echo $userid.'<br>';
    if(!$userid){
        $error[]=array('success'=>false,'msg'=>'用户code不对','usercode'=>$col[10]);
        continue;
    }
    global $current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);


    $_REQUES['record']='';
    $request=new Vtiger_Request($_REQUES, $_REQUES);
    $request->set('company',$col[0]);
    $request->set('lastname',$col[1]);
    $request->set('mobile',$col[2]);
    $request->set('locationprovince',$col[3]);
    $request->set('locationcity',$col[4]);
    $request->set('leadsourcetnum',$col[6]);
    $request->set('sourcecategory',$col[7]);
    $request->set('assigned_user_id',$userid);
    $request->set('leadstype','payspread');
    $request->set('leadsource','SCRM');
    $request->set('isFromMobile',1);
    $request->set('noSend',1);
    $request->set('module','Leads');
    $request->set('view','Edit');
    $request->set('action','Save');
    $ressorder=new Leads_Save_Action();
    $result[] = $ressorder->saveRecord($request);
}


echo "<pre>";
echo '失败';
var_dump($error);

echo '---------------------';
var_dump($result);
function getascii( $ch) {
    if(strlen($ch) == 1){
        return ord($ch)-65;
    }
    return ord($ch[1])-38;
}
