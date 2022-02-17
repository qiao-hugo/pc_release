<?php
/**
 * Created by PhpStorm.
 * 更新附件名称
 * User: Administrator
 * Date: 2020/6/2
 * Time: 16:20
 */
include "config.inc.php";
$mysqli = new mysqli($dbconfig['db_server'].$dbconfig['db_port'], $dbconfig['db_username'], $dbconfig['db_password'], $dbconfig['db_name']);

/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
function my_dir($dir) {
    global $mysqli,$root_directory;
    $files = array();
    if(@$handle = opendir($dir)) {
        while(($file = readdir($handle)) !== false) {
            if($file != ".." && $file != ".") {
                if(is_dir($dir . "/" . $file)) { //如果是子文件夹，进行递归
                    $files[$file] = my_dir($dir . "/" . $file);
                } else {
                    $files[] = $dir.'/'.$file;
                    $fileArr=explode('_',$file);
                    $fileid=$fileArr[0];
                    if(is_numeric($fileid)){
                        $time=time();
                        $sql='UPDATE vtiger_files SET newfilename='.$time.' WHERE newfilename=0 AND attachmentsid='.$fileid;
                        $mysqli->query($sql);
                        if($mysqli->affected_rows){
                            $oldName=$root_directory.$dir.'/'.$file;
                            $newName=$root_directory.$dir.'/'.$fileid.'_'.$time;
                            echo $oldName,'<br>hello333<br>',$newName,'<br>';
                            rename($oldName,$newName);
                        }
                    }

                }
            }
        }
        closedir($handle);
    }
}

echo "<pre>";
print_r(my_dir("storage"));
echo "</pre>";
exit;