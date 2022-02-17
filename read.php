<?php
error_reporting(0);
if(isset($_GET["readid"])){
    $email=$_GET["readid"];
    //$email='c3NzZHdlc2VkbW5idmI1YTc2NmI2ODhmY2NlYzczNDhlYzIwM2MyMWI0NzI4YWNicTUxMzViOTc0MjdkNjFmNGU1Y2MwZTIzZGQwZTE5MTg3YWZjZGJhYXlx';
    $email=base64_decode($email);
    $dd=md5('Useridstrunlandorgnetcomcn');
    $e=explode($dd,$email);
    $ee=md5('AccountsiD');
    $e=explode($ee,$e[0]);
    $f=str_replace(array('b','c','a','f','m','n','t','o','x','q'),array(0,1,2,4,5,6,7,8,9,3),$e[1]);
    $f=(int)$f;
    if($f>0){
        include_once 'config.inc.php';
        //服务器没有打开PDO
        /* $dbconfig['db_port']=ltrim($dbconfig['db_port'],':');
        try{
            $pdo=new PDO("mysql:host={$dbconfig['db_server']};port={$dbconfig['db_port']};dbname={$dbconfig['db_name']}",$dbconfig['db_username'],$dbconfig['db_password'],array(PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES utf8'));
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $e){
            $e->getMessage();
            exit;
        }

        $datetime=date("Y-m-d H:i:s");
        $sql="UPDATE vtiger_mailaccount SET readtimes=readtimes+1,readdatetime=CONCAT(IFNULL(readdatetime,''),',','{$datetime}'),lastreddatetime='{$datetime}',email_flag='read' WHERE mailaccountid=?";
        $stem=$pdo->prepare($sql);
        $stem->execute(array($f)); */
        //file_put_contents('123.txt',$f);
        $conn=mysql_connect($dbconfig['db_server'].$dbconfig['db_port'],$dbconfig['db_username'],$dbconfig['db_password']) or die('noconnect');
        mysql_select_db($dbconfig['db_name']);
        $datetime=date("Y-m-d H:i:s");
        mysql_query("UPDATE vtiger_mailaccount SET readtimes=readtimes+1,readdatetime=CONCAT(IFNULL(readdatetime,''),',','{$datetime}'),lastreddatetime='{$datetime}',email_flag='read' WHERE mailaccountid={$f}");
    }
}

header("Content/type:image/gif");
$im=imagecreatefromgif("bg.gif"); 
imagegif($im); 
imagedestroy($im);
