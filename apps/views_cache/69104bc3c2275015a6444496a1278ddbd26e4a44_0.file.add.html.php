<?php
/* Smarty version 3.1.28-dev/79, created on 2017-12-27 12:39:53
  from "/data/httpd/vtigerCRM/apps/views/accounts/add.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a4324190eacd2_86546737',
  'file_dependency' => 
  array (
    '69104bc3c2275015a6444496a1278ddbd26e4a44' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/accounts/add.html',
      1 => 1466418351,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_5a4324190eacd2_86546737 ($_smarty_tpl) {
?>
<!DOCTYPE HTML>
<html>
<head>
		
		<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        <title>添加联系人</title>
		<!--<link type="text/css" rel="stylesheet" href="/css/calendar.min.css" />-->
		<?php echo '<script'; ?>
 src="static/js/jquery.form.js"><?php echo '</script'; ?>
>

</head>

<body>

<div class="container-fluid w fix">
        <div class="row">
            
            <form id='myForm2' onsubmit='return check()' method="POST">
            <div class="add-visit">
            	<input type="hidden" id="account_id" name="account_id" value='<?php echo $_smarty_tpl->tpl_vars['account_id']->value;?>
' >
				<input type="hidden" id="account_id_display" name="account_id_display" value='<?php echo $_smarty_tpl->tpl_vars['account_name']->value;?>
' >
                <div class="form-group fix">
                    <label>姓名</label>
                    <div class="input-box">
                        <input data-toggle="popover" data-placement="top" 
                        data-content="姓名不能为空" type="text" id="name" name="name" class="form-control" >
                    </div>
                </div>
                <div class="form-group fix">
                    <label>性别</label>
                    <div class="input-box">
                        <select  id="gendertype" name="gendertype" class="form-control" >
								<option value='MALE'>男</option>
								<option value='FEMALE'>女</option>	
						</select>
                    </div>
                </div>
                <div class="form-group fix">
                    <label>办公电话</label>
                    <div class="input-box">
                        <input data-toggle="popover" data-placement="top" 
                        data-content="办公电话不能为空" type="text" id="phone" name="phone" class="form-control" >
                    </div>
                </div>
                <div class="form-group fix">
                    <label>手机</label>
                    <div class="input-box">

                        <input data-toggle="popover" data-placement="top" 
                        data-content="手机不能为空" type="text" id="mobile" name="mobile" class="form-control">
                    </div>
                </div>
                <div class="form-group fix">
                    <label>职务</label>
                    <div class="input-box">
                        <input data-toggle="popover" data-placement="top" 
                        data-content="职务不能为空" type="text" placeholder="" id="title" name="title" class="form-control">
                    </div>
                </div>
                <div class="form-group fix">
                    <label>常用Email</label>
                    <div class="input-box">
                        <input data-toggle="popover" data-placement="top" 
                        data-content="常用Email不能为空" type="email" placeholder="" id="email" name="email" class="form-control">
                    </div>
                </div>
              
                <div class="form-group fix">
                    <label>决策圈</label>
                    <div class="input-box">
                        <select  id="makedecisiontype" name="makedecisiontype" class="form-control" >
								<option value='Decisionmakers' selected>决策人</option>
								<option value='Effect of human'>影响人</option>
								<option value='The execution layer'>执行层</option>
					     </select>
                    </div>
                </div>
                
              <div class="form-group fix">
                <label>描述</label>
                <div class="input-box">
                	
                    <textarea id="description" name="description" class="form-control" placeholder="请输入内容...." rows="5"></textarea>
                </div>
              </div>

                <div class="confirm tc">
                    <button id="dosave" class="btn" data-toggle="popover" data-placement="top" 
                        data-content="成功添加联系人" >保 存</button>
                </div>
            </div>
            </form>
            <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        </div>
    </div>



	<?php echo '<script'; ?>
 type="text/javascript">

			function check(){
                $('#name').popover('destroy');
				if(''==$('#name').val()){
					$('#name').popover('show');
                    $('.popover-content').css({"color":"red","fontSize":"12px"});
                    setTimeout("$('#name').popover('destroy')",2000);//2秒钟后隐藏
                    $("#name").focus();
					return false;
				}
                $('#phone').popover('destroy');
				if(''==$('#phone').val()){
					$('#phone').popover('show');
                    $('.popover-content').css({"color":"red","fontSize":"12px"});
                    setTimeout("$('#phone').popover('destroy')",2000);
                    $("#phone").focus();
					return false;
				}
                $('#mobile').popover('destroy');
				if(''==$('#mobile').val()){
					$('#mobile').popover('show');
                    $('.popover-content').css({"color":"red","fontSize":"12px"});
                    setTimeout("$('#mobile').popover('destroy')",2000);
                    $("mobile").focus();
					return false;
				}
                var regexp=/^((1[3,5,8][0-9])|(14[5,7])|(17[0,1,3,6,7,8]))\d{8}$/;
                /*
                来源百度百科
                //2016-01-16的手机号码前几位
                 电信
                 中国电信手机号码开头数字
                 2G/3G号段（CDMA2000网络）133、153、180、181、189
                 4G号段 177、173
                 联通
                 中国联通手机号码开头数字
                 2G号段（GSM网络）130、131、132、155、156
                 3G上网卡145
                 3G号段（WCDMA网络）185、186
                 4G号段 176、185[1]
                 移动
                 中国移动手机号码开头数字
                 2G号段（GSM网络）有134x（0-8）、135、136、137、138、139、150、151、152、158、159、182、183、184。
                 3G号段（TD-SCDMA网络）有157、187、188
                 3G上网卡 147
                 4G号段 178、184
                 补充
                 14号段以前为上网卡专属号段，如中国联通的是145，中国移动的是147等等。
                 170号段为虚拟运营商专属号段，170号段的 11 位手机号前四位来区分基础运营商，其中 “1700” 为中国电信的转售号码标识，“1705” 为中国移动，“1709” 为中国联通。
                 171号段也为虚拟运营商专属号段。
                 卫星通信 1349
                */
                if(!regexp.test($('#mobile').val())){
                    $('#mobile').attr('data-content','不是正确的手机号码');
                    $('#mobile').popover('show');
                    $('.popover-content').css({"color":"red","fontSize":"10px"});
                    $('#mobile').focus();
                    setTimeout("$('#mobile').popover('destroy')",2000);
                    return false;
                }
                $('#title').popover('destroy');
				if(''==$('#title').val()){
					$('#title').popover('show');
                    $('.popover-content').css({"color":"red","fontSize":"10px"});
                    setTimeout("$('#title').popover('destroy')",2000);
                    $('#title').focus();
					return false;
				}
                $('#email').popover('destroy');
				if(''==$('#email').val()){
					$('#email').popover('show');
                    $('.popover-content').css({"color":"red","fontSize":"10px"});
                    setTimeout("$('#email').popover('destroy')",2000);
                    $('#email').focus();
					return false;
				}
                //验证邮箱是否正确
                regexp=/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
                if(!regexp.test($('#email').val())){
                    $('#email').attr('data-content','Email格式不正确');
                    $('#email').popover('show');
                    $('.popover-content').css({"color":"red","fontSize":"10px"});
                    $('#email').focus();
                    setTimeout("$('#email').popover('destroy')",2000);
                    return false;
                }
				return true;
			}

        $('#dosave').on('click', function(){
             if(!check()){
                 return false;
             }
            $('#myForm2').ajaxSubmit({
                type: 'post', 
                url:"/index.php?module=Accounts&action=doaddContact",
                dataType :'json',
                success: function(data) {
                    if(data.res=='success'){
                        $('#dosave').popover('show');
                        $('#dosave').removeAttr("id");
                        window.location.href='/index.php?module=VisitingOrder&action=vlist';
                        $('#myForm2').resetForm();
                    }else{
                        alert('error01');
                    }
                },
                error:function(){
                    alert('error');
                }
            });
            return false;
        });

           
	<?php echo '</script'; ?>
>

</body>
</html><?php }
}
