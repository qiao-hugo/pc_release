{strip}
<!DOCTYPE html>
<html>
	<head>
		<title>登录 - 珍岛ERP</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!-- for Login page we are added -->
		<link href="libraries/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link href="libraries/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
		<link href="libraries/bootstrap/css/jquery.bxslider.css" rel="stylesheet" />
		<script src="libraries/jquery/jquery.min.js"></script>
		<script src="libraries/jquery/boxslider/respond.min.js"></script>
	</head>
	<body>
		<div class="container-fluid login-container">
			<!--<div class="row-fluid">
				<div class="span3">
					<div class="logo"><img src="layouts/vlayout/skins/images/logo.png"></div>
				</div>
				<div class="span9">
					<div class="helpLinks">
						<a href="http://www.trueland.net">关于珍岛</a> | 
						<a href="http://www.trueland.net/Service.shtml">珍岛服务</a> | 
						<a href="http://case.trueland.net/">案例中心</a> | 
						<a href="http://dingcan.trueland.net">订餐</a> 
					</div>
				</div>
			</div>-->
			<div class="row-fluid">
				<div class="span12">
					<div class="content-wrapper">
						<div class="container-fluid">
							<div class="row-fluid">
								
								<div class="span12">
									<div class="login-area" style="margin:0 auto;">
                                    	<div class="logo"><img src="layouts/vlayout/skins/images/logo.png"></div>
                                        <div class="text_box"><img src="layouts/vlayout/skins/images/text.png"></div>
										<div class="login-box1" id="loginDiv">
											<div class="login_c_group_item" style="padding:0;">
												<div class="control-group" style="text-align: center;">
													<a href="/index.php?module=Users&view=Login" style="color:#ccc;">用户名密码</a>&nbsp;&nbsp;|&nbsp;&nbsp;<span>二维码登陆</span>
												</div>
											</div>
											<div class="login_c_group_item" style="position:relative">
												<div class="control-group" style="text-align: center;">
													<img src="/index.php?module=Users&view=QRLogin&type=QRcode"/>

													<div id="statusflag" style="display:none;position:absolute;right:46%;top:48%;width:45px;height:45px;border-radius:45px;background-color:green;color:white;font-size: 40px;font-weight:bold;text-align: center;line-height:50px;">
													<span id="trueorfalse">√</span>
													</div>
												</div>
										</div>
										</br>
										</br>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="login-copyright"><div>建议使用浏览器及版本&nbsp;360安全浏览器：7.1.1.554以上版本 360极速浏览器：8.1.0.424以上版本谷歌浏览器：40.0.2214.115 m以上版本火狐浏览器：35.0.1以上版本</div>Copyright©2014 珍岛信息技术（上海）股份有限公司</div>
	</body>
	<script>
        var flag=0;
        var looper=0;
        var hander=null;
		jQuery(document).ready(function(){
            hander=setInterval("getStatus()",3000);
		});
        function getStatus(){
            if(looper>=60){
                clearInterval(hander);
            }
            ++looper;
            jQuery.ajax({
                type: "get",
                async:false,
                url: '/index.php?module=Users&view=QRLogin&type=status',
                dataType:'json',
                success: function (data) {
                    if (data.success) {
                        if (flag == 0 && data.status == 1) {
                            jQuery("#statusflag").show();
                            flag = 1;
                        } else if (data.status == 2) {
                            window.location.href = "/index.php?from=login";
                        } else if(data.status == 3){
                            jQuery('#trueorfalse').html('×');
                            jQuery("#statusflag").css("backgroundColor","red");
                            jQuery("#statusflag").show();
                            clearInterval(hander);
                        }
                    }
                }
            });
        }
	</script>
</html>	
{/strip}
