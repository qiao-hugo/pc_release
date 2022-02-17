<!DOCTYPE HTML>
<html>
<head>
		<title>简历录入</title>
		<meta charset="utf-8"/>
		<meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width" />
		<meta content="yes" name="apple-mobile-web-app-capable" />
		<meta content="yes" name="apple-touch-fullscreen" />
		<meta content="telephone=no" name="format-detection" />
		<link href="static/css/bootstrap.min.css" rel="stylesheet" />
		<link href="static/css/font-awesome.min.css" rel="stylesheet" />
		<link href="static/css/Style.css" rel="stylesheet" />
		<link href="static/css/common.css" rel="stylesheet" />
		<link href="static/css/tips.css" rel="stylesheet" type="text/css"/>
		<script src="static/js/jquery-2.1.0.min.js"></script>
		<script src="static/js/bootstrap.min.js"></script>
		<script src="static/js/tips.min.js"></script>
</head>

<body>
<?php
	error_reporting(0);
    $from=$_REQUEST['from'];
    session_start();

    /*if(!empty($_SESSION['type']) && $_SESSION['type']=='adddata'){
        echoMsgAndGo("亲,您已经添加了!");
    }*/
    if(!isset($from)){
        echoMsg();
    }
    if($from=='qrcode') {
        $schoolid = empty($_GET['schoolid']) ? 0 : $_GET['schoolid'];
        $schoolid = base64decode($schoolid);
        $schoolrecruitid = empty($_GET['schoolrecruitid']) ? 0 : $_GET['schoolrecruitid'];
        $schoolrecruitid = base64decode($schoolrecruitid);
        $schoolname = empty($_GET['schoolname']) ? '' : urldecode($_GET['schoolname']);
        if ($schoolid == 0 || $schoolrecruitid == 0) {
            echoMsg();
        }
    }elseif($from=='adddata'){
        if($_REQUEST['formtype']!='microq'){
            echoMsg('请用微信或QQ扫码!');
        }
        if($_REQUEST['name']==''){
            echoMsg('用户名必填!');
        }
        if($_REQUEST['schoolname']==''){
            echoMsg('院校必填!');
        }
        if($_REQUEST['graduatemajor']==''){
            echoMsg('专业必填!');
        }
        if(!preg_match("/^1[34578]{1}\d{9}$/",$_REQUEST['telephone'])){
            echoMsg('手机号码有误');
        }
        if(!preg_match("/^([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+\.(?:com|cn)$/",$_REQUEST['email'])){
            echoMsg('不支持该邮箱');
        }
        include("include.php");
        include("version.php");
        require_once("PortalConfig.php");
        require_once("include/utils/utils.php");

        $params = array(
            'fieldname'=>$_REQUEST,
            'userid'=>1,
            );
        $resultData = $client->call('addSchoolresumeInfo', $params);
        if($resultData[0]==1){
            $_SESSION['type']=='adddata';
            echoMsgAndGo("您的简历已经登记成功!");
        }else{
            echoMsgAndGo("您手机号码已经登记!");
        }
    }else{
        echoMsg();
    }

    /**
     * 数字解密
     * @param $v
     * @return int|mixed
     */
     function base64decode($v){
        $string=base64_decode($v);
        $dd=md5('Useridstrunlandorgnetcomcn');
        $e=explode($dd,$string);
        $ee=md5('AccountsiD');
        $e=explode($ee,$e[0]);
        $f=str_replace(array('b','c','a','f','m','n','t','o','x','q'),array(0,1,2,4,5,6,7,8,9,3),$e[1]);
        $f=(int)$f;
        return $f;
     }

    /**
     * 错误的信息提示
     */
     function echoMsg($msg="网址有误!!!"){
         echo '<script>Tips.alert({
                        content: "'.$msg.'",
                        define:"确定"
                    });</script>';
         exit;
     }
     function echoMsgAndGo($msg){
         echo '<script>Tips.alert({
                        content: "'.$msg.'",
                        define:"确定",
                        after:function(){
                            window.location.href="http://m.71360.com/";    	
                        }
                    });</script>';
         exit;
     }

?>

<div class="container-fluid w fix">
        <div class="row">
		<div style="height:150px;width:150px;margin: 10% auto 5% auto;background-size: 100% 100%;border: 1px solid #eee;border-radius:150px;overflow: hidden;"><img src="./static/img/trueland.png" style="height:150px;width:150px;"/></div>
            
			<div class="add-visit">
				<div class="form-group fix">
                    同学，您好！<br/>
				<p style="text-indent:2em;">欢迎应聘珍岛，加入珍岛人才库！该电子简历非常重要，是珍岛录用通知发送的前提基础，请认真填写。</p>
                </div>
			</div>
           
            <form id='myForm2' onsubmit='return check();' action="<?=$_SERVER['PHP_SELF']?>" method="POST">
            <div class="add-visit">
              
                <div class="form-group fix">
                    <label><span style="color:red;">*</span>姓名</label>
                    <div class="input-box">
                        <input type="hidden" name="from" value="adddata"/>
                        <input type="hidden" id="formtype" name="formtype" value=""/>
                        <input type="hidden" name="schoolrecruitid" value="<?=$schoolrecruitid?>"/>
                        <input type="hidden" name="schoololdname" value="<?=$schoolname?>"/>
                        <input type="text" id="name" name="name" class="form-control">
                    </div>
                </div>
                <div class="form-group fix">
                    <label><span style="color:red;">*</span>性别</label>
                    <div class="input-box">
                        <select name="gendertype" class="form-control"><option value="MALE">男</option><option value="FEMALE">女</option></select>
                    </div>
                </div>
                <div class="form-group fix">
                    <label><span style="color:red;">*</span>专业</label>
					<div class="input-box">
						<input type="text" id="graduatemajor" name="graduatemajor"  class="form-control">
					</div>
                </div>
                <div class="form-group fix">
                    <label><span style="color:red;">*</span>学历</label>
                    <div class="input-box">
                        <select id="highestdegree" name="highestdegree" class="form-control"><option value="">选择一个选项</option><?php if(false){?><option value="vocational">中职</option><option value="technical">技校</option><option value="highschool">普通高中</option><?php }?><option value="specialty">大学专科</option><option value="undergraduate">大学本科</option><option value="master">硕士研究生</option><option value="doctor">博士研究生</option></select>
                    </div>
                </div>
                 <div class="form-group fix">
                    <label><span style="color:red;">*</span>院校</label>
                    <div class="input-box">
                        <input type="hidden" name="schoolid"  class="form-control" value="<?=$schoolid?>">
                        <input type="text" id="schoolname" name="schoolname"  class="form-control" value="<?=$schoolname?>">
                    </div>
                </div>
                <div class="form-group fix">
                    <label><span style="color:red;">*</span>手机</label>
                    <div class="input-box">
                        <input type="text" id="telephone" name="telephone"  class="form-control">
                    </div>
                </div>
                <div class="form-group fix">
                    <label><span style="color:red;">*</span>邮箱</label>
                    <div class="input-box">
                        <input type="text" id="email" name="email" value=''class="form-control">
                    </div>
                </div>
                
               
                <div class="confirm tc">
                    <button id='dosave' class="btn" data-toggle="popover" data-placement="top" 
                        data-content="成功添加拜访单,正在跳转请稍等">保 存</button>
                </div>

            </div>
        	</form>
        </div>
    </div>
<script type="text/javascript">

    //alert(navigator.userAgent);
    // 对浏览器的UserAgent进行正则匹配，不含有微信独有标识的则为其他浏览器
    var useragent = navigator.userAgent;
    if (useragent.match(/MicroMessenger/i) !='MicroMessenger' && useragent.match(/QQ\//i) != 'QQ/') {
        // 这里警告框会阻塞当前页面继续加载
        showMsg('已禁止本次访问：您必须使用微信内置浏览器QQ内置浏览器访问本页面！');
        //alert('已禁止本次访问：您必须使用微信内置浏览器访问本页面！');
        $('body').html('已禁止本次访问：您必须使用微信内置浏览器或QQ内置浏览器访问本页面！');
        // 以下代码是用javascript强行关闭当前页面
        var opened = window.open('about:blank', '_self');
        opened.opener = null;
        opened.close();
    }

    function check(){
        $('#formtype').val('microq');
        var username=$('#name').val();
        if(username==''){
            showMsg('请输入您的姓名!');
            return false;
        }
        if(isName(username)){
            showMsg('姓名输入有误!');
            return false;
        }
        var graduatemajor=$('#graduatemajor').val();
        if(graduatemajor==''){
            showMsg('请输入您的专业!');
            return false;
        }
        var highestdegree=$('#highestdegree').val();
        if(highestdegree==''){
            showMsg('请选择您的学历!');
            return false;
        }
        var schoolname=$('#schoolname').val();
        if(schoolname==''){
            showMsg('请输入您所在的院校!');
            return false;
        }
        var telephone=$('#telephone').val();
        if(telephone==''){
            showMsg('请输入您的手机号码!');
            return false;
        }
        if(fucCheckTEL(telephone)){
            showMsg('您输入的手机号码有误!');
            return false;
        }
        var email=$('#email').val();
        if(email==''){
            showMsg('请输入您的邮箱!');
            return false;
        }
        var email=$('#email').val();
        if(isEmail(email)){
            showMsg('您输入的邮箱格式有误!');
            return false;
        }
        return true;
    }
    function showMsg(msg='必填项不能为空'){
        Tips.alert({
            content: msg,
            define:"确定"
        });

    }
    //姓名验证

    function isName(name)
    {
        reg = /^[\u4E00-\u9FA5]{2,6}$/;

        if(!reg.test(name))
        {
            return true;
        }
        else
        {
            return false;
        }

    }


    function isEmail(email)
    {
        if(email.search(/^([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+\.(?:com|cn)$/)!= -1)
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    //验证手机号码

    function fucCheckTEL(tel)
    {
        if (tel.search(/^1[3|4|5|7|8][0-9]{9}$/)!= -1)
        {
            return false;
        }
        else
        {
            return true;

        }

    }
</script>
</body>
</html>