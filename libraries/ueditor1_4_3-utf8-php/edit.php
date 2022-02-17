<?php
	header("Content-type: text/html; charset=utf-8");
	$dbhost = 'localhost'; 
$dbuser = 'root'; //我的用户名 
$dbpass = '123456'; //我的密码 
$dbname = 'vtigercem600'; //我的mysql库名 

$connect = mysql_connect($dbhost,$dbuser,$dbpass,$dbname); 
mysql_set_charset('utf8', $connect);
if ($connect) { 
//echo "非常好,成功了!"; 
} else { 
echo "不好意思,失败了！"; 
} 

//mysql_close($connect);

//var_dump($_POST['editorValue']);
if(!empty($_POST)){
	$tabledata=$_POST['editorValue'];
	$sql3= 'INSERT INTO vtiger_workday (datetype) VALUES ('."'".$tabledata."'".')';
	//echo $tabledata;
	//$html=$_POST['htm'];
	//$text=$_POST['tex'];
}

$sql1="show tables";
$sql2="SELECT * FROM `vtiger_wsapp`";
$sql4 = 'SELECT datetype FROM vtiger_workday WHERE id = (SELECT MAX(id) FROM vtiger_workday)';
//print_r($sql3);


mysql_select_db('vtigercrm600',$connect);
if(!empty($sql3)){
	$result1 = mysql_query($sql3,$connect) or die("错误：" . mysql_error());
}

$result = mysql_query($sql4,$connect) or die("错误：" . mysql_error());
$mysalstr = "";
while($row = mysql_fetch_array($result))
  {
  $mysalstr = $row['0'];
  }
  ?>
<!DOCTYPE HTML>
<html lang="utf-8">

<head>
    <meta charset="UTF-8">
    <title>ueditor demo</title>
</head>

<body>
    <!--加载编辑器的容器-->
	
		<textarea id="container" > 
			<?php echo $mysalstr ?>
		</textarea>
	
	
	<script type="text/javascript" src="ueditor.config.js"></script> <!-- 配置文件 -->
    <script type="text/javascript" src="ueditor.all.js"></script> <!-- 实例化编辑器 -->
    <script type="text/javascript">
      var ue = UE.getEditor('container');
	//对编辑器的操作最好在编辑器ready之后再做
	ue.ready(function() {
		$('#jssubmit').click(function(){
			var html = ue.getContent(); //获取html内容，返回: <p>hello</p>
			var txt = ue.getContentTxt(); //获取纯文本内容，返回: hello
	
	$.post("./edit.php",
    {
      htm:html,
      tex:txt
    },
    function(data,status){
      
    });
		})
    //ue.setContent('hello');//设置编辑器的内容
});
    </script>
	
</body>

</html>



























