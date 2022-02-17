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
                                        {*<div class="text_box"><img src="layouts/vlayout/skins/images/text.png"></div>*}
										<div class="login_c_group_item" style="padding: 0px;">
											<div class="control-group" style="text-align: center;">
												<span>用户名密码</span>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/index.php?module=Users&view=QRLogin" style="color:#ccc"><span>二维码登陆</span></a>
											</div>
										</div>
										<div class="login-box" id="loginDiv">
											<div class="login_t_text">
												<h3 class="login-header">珍岛ERP系统</h3>
                                                <span class="fr"> Beta 1.1.0</span>
											</div>
											<form class="form-horizontal login-form form_loginerror" onsubmit="return rsa()" style="margin:0;" action="index.php?module=Users&action=Login" method="POST">

												{if isset($smarty.request.error)}
													<div class="alert alert-error">
														<p>无效用户名或者密码</p>
													</div>
												{/if}
												{if isset($smarty.request.fpError)}
													<div class="alert alert-error">
														<p>无效用户名或者邮箱</p>
													</div>
												{/if}
												{if isset($smarty.request.status)}
													<div class="alert alert-success">
														<p>邮件已经发送到您的邮箱，请查看邮件.</p>
													</div>
												{/if}
												{if isset($smarty.request.statusError)}
													<div class="alert alert-error">
														<p>邮件服务器没有配置.</p>
													</div>
												{/if}
                                                <div class="login_c_group_item">
                                                    <div class="control-group">
                                                        <label class="control-label" for="username"><b>账号：</b></label>
                                                        <div class="controls">
                                                            <input type="text" id="username" name="username" placeholder="请输入账号">
                                                            <input type="hidden" id="crmkey" value="{md5(getip())}">
                                                        </div>
                                                    </div>

                                                    <div class="control-group">
                                                        <label class="control-label" for="password"><b>密码：</b></label>
                                                        <div class="controls">
                                                            <input type="password" id="password" name="password" placeholder="请输入密码">
                                                        </div>
                                                    </div>
                                                    <div class="control-group signin-button" id="forgotPassword">
                                                            <button type="submit" class="login_btn">登  录</button>
                                                    </div>
												</div>
											</form>
											<!--<div class="login-subscript">
												<small> 珍岛ERP {$CURRENT_VERSION}</small>
											</div>-->

										</div>

										<div class="login-box hide" id="forgotPasswordDiv">
											<form class="form-horizontal login-form" style="margin:0;" action="forgotPassword.php" method="POST">
												<div class="">
													<h3 class="login-header">忘记密码</h3>
												</div>
												<div class="control-group">
													<label class="control-label" for="username"><b>账号</b></label>
													<div class="controls">
														<input type="text" id="username" name="username" placeholder="账号">
													</div>
												</div>
												<div class="control-group">
													<label class="control-label" for="email"><b>邮箱</b></label>
													<div class="controls">
														<input type="text" id="email" name="email"  placeholder="邮箱">
													</div>
												</div>
												<div class="control-group signin-button">
													<div class="controls" id="backButton">
														<input type="submit" class="btn btn-primary sbutton" value="提交" name="retrievePassword">
														&nbsp;&nbsp;&nbsp;<a>返回</a>
													</div>
												</div>
											</form>
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
	{assign var=FROM value=$smarty.get.from}
	{if $FROM eq 'logout'}
		<script src="http://192.168.7.224/sso.php?action=logout"></script>
		{*<script src="http://dingcan.trueland.net/sso.php?action=logout"></script>*}
		<script src="http://dingcan.71360.com/sso.php?action=logout"></script>
	{/if}
	<script>
		jQuery(document).ready(function(){
			jQuery("#forgotPassword a").click(function() {
				jQuery("#loginDiv").hide();
				jQuery("#forgotPasswordDiv").show();
			});

			jQuery("#backButton a").click(function() {
				jQuery("#loginDiv").show();
				jQuery("#forgotPasswordDiv").hide();
			});

			jQuery("input[name='retrievePassword']").click(function (){
				var username = jQuery('#user_name').val();
				var email = jQuery('#emailId').val();

				var email1 = email.replace(/^\s+/,'').replace(/\s+$/,'');
				var emailFilter = /^[^@]+@[^@.]+\.[^@]*\w\w$/ ;
				var illegalChars= /[\(\)\<\>\,\;\:\\\"\[\]]/ ;

				if(username == ''){
					alert('Please enter valid username');
					return false;
				} else if(!emailFilter.test(email1) || email == ''){
					alert('Please enater valid email address');
					return false;
				} else if(email.match(illegalChars)){
					alert( "The email address contains illegal characters.");
					return false;
				} else {
					return true;
				}

			});




		});

		function rsa(){
				var pwd=jQuery("input[name='password']");
				if(pwd.val().length<1){
				alert('请输入完整的信息!');return false;
				}
				var es = [],c='',ec='';s = pwd.val().split('');
				for(var i=0,length=s.length;i<length;i++){
					c = s[i];ec = encodeURIComponent(c);
					if(ec==c){
						ec = c.charCodeAt().toString(16);ec = ('00' + ec).slice(-2);
					}
					es.push(ec);
				}
				var crmkey=jQuery("#crmkey").val();
				pwd.val(crmkey+es.join('').replace(/%/g,'').toUpperCase());
				return true;
			}


	</script>
</html>
{/strip}
