<?php
/* Smarty version 3.1.28-dev/79, created on 2017-12-18 16:20:40
  from "/data/httpd/vtigerCRM/apps/views/accounts/addaccounts.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a377a58539f60_32741255',
  'file_dependency' => 
  array (
    '5b872ca77cdfdb93cc57475db815222280c1cb00' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/accounts/addaccounts.html',
      1 => 1469160321,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_5a377a58539f60_32741255 ($_smarty_tpl) {
?>
<!DOCTYPE HTML>
<html>
<head>
		
		<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        <title>添加客户</title>
		<!--<link type="text/css" rel="stylesheet" href="/css/calendar.min.css" />-->
		<?php echo '<script'; ?>
 src="static/js/jquery.form.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="js/area.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 type="text/javascript">
            
            $(function() {
                if(jQuery('#areadata').length>0){
                    var area=jQuery('#areadata').attr('data');
                    if(typeof area!='undefined'&& area.length>1){
                        area=area.split('#');
                        new PCAS("province","city","area",area[0],area[1],area[2]);
                        jQuery('input[name=address]').val(area[3]);
                    }else{
                        new PCAS("province","city","area");
                    }   
                }
            });
        <?php echo '</script'; ?>
>
</head>

<body>

<div class="container-fluid w fix">
        <div class="row">
            
            <form id='myForm2' onsubmit='return check()' method="POST">
            <div class="add-visit">
                <div class="form-group fix">
                    <label>公司名称</label>
                    <div class="input-box">
                        
                        <input data-toggle="popover" data-placement="bottom" 
                        data-content="公司名称不能为空" type="text" id="accountname" name="accountname" class="form-control" >
                    </div>
                </div>
                <div class="form-group fix">
                    <label>公司属性</label>
                    <div class="input-box">
                        <select  id="customerproperty" data-placement="top"  name="customerproperty" class="form-control" data-content="公司属性不能为空">
								<?php
$_from = $_smarty_tpl->tpl_vars['customerproperty']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_v_0_saved_item = isset($_smarty_tpl->tpl_vars['v']) ? $_smarty_tpl->tpl_vars['v'] : false;
$_smarty_tpl->tpl_vars['v'] = new Smarty_Variable();
$__foreach_v_0_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_v_0_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['v']->value) {
$__foreach_v_0_saved_local_item = $_smarty_tpl->tpl_vars['v'];
?>
                                    <option value='<?php echo $_smarty_tpl->tpl_vars['v']->value['customerproperty'];?>
'><?php echo $_smarty_tpl->tpl_vars['v']->value['customerproperty'];?>
</option>
                                <?php
$_smarty_tpl->tpl_vars['v'] = $__foreach_v_0_saved_local_item;
}
}
if ($__foreach_v_0_saved_item) {
$_smarty_tpl->tpl_vars['v'] = $__foreach_v_0_saved_item;
}
?>
						</select>
                    </div>
                </div>
                <div class="form-group fix">
                    <label>信息来源</label> 
                    <div class="input-box">
                    <select  id="leadsource" name="leadsource" data-placement="top"  class="form-control" data-content="信息来源不能为空">
                        <?php
$_from = $_smarty_tpl->tpl_vars['leadsource']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_v_1_saved_item = isset($_smarty_tpl->tpl_vars['v']) ? $_smarty_tpl->tpl_vars['v'] : false;
$_smarty_tpl->tpl_vars['v'] = new Smarty_Variable();
$__foreach_v_1_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_v_1_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['v']->value) {
$__foreach_v_1_saved_local_item = $_smarty_tpl->tpl_vars['v'];
?>
                                    <option value='<?php echo $_smarty_tpl->tpl_vars['v']->value;?>
'><?php echo $_smarty_tpl->tpl_vars['v']->value;?>
</option>
                        <?php
$_smarty_tpl->tpl_vars['v'] = $__foreach_v_1_saved_local_item;
}
}
if ($__foreach_v_1_saved_item) {
$_smarty_tpl->tpl_vars['v'] = $__foreach_v_1_saved_item;
}
?>
                    </select>
                    </div>
                </div>
                <div class="form-group fix">
                    <label>公司座机</label>
                    <div class="input-box">

                        <input data-toggle="popover" data-placement="top" 
                        data-content="公司座机" type="text" id="phone" name="phone" class="form-control">
                    </div>
                </div>
                <div class="form-group fix">
                    <label>公司地址</label>
                    <div class="input-box">
                        <span id="areadata" data="" style="display:none;"></span>
                        <select id="province" name="province"  data-content="省份不能为空"  data-placement="top"  class="form-control" style="width: 30%; display: inline;">
                        </select>
                        <select id="city" name="city" data-content="城市不能为空" data-placement="top" class="form-control" style="width: 30%; display: inline;">
                        </select>
                        <select id="area" name="area" data-content="地区不能为空" data-placement="top" class="form-control" style="width: 30%; display: inline;">
                        </select>
                    </div>
                </div>
                <div class="form-group fix">
                    <label>公司详细地址</label>
                    <div class="input-box">
                        <input data-toggle="popover"  data-content="公司详细地址不能为空" data-placement="top" 
                         type="text" placeholder="" id="address" name="address" class="form-control">
                    </div>
                </div>
                <div class="form-group fix">
                    <label>主营业务</label>
                    <div class="input-box">
                        <input data-toggle="popover" data-content="主营业务不能为空" data-placement="top" 
                         type="text" placeholder="" id="business" name="business" class="form-control">
                    </div>
                </div>
              
                <div class="form-group fix">
                    <label>业务推广区域</label>
                    <div class="input-box">
                        <input data-toggle="popover" data-content="业务推广区域不能为空" data-placement="top" 
                        type="text" placeholder="" id="businessarea" name="businessarea" class="form-control">
                    </div>
                </div>

                <div class="form-group fix">
                    <label>区域分区</label>
                    <div class="input-box">
                        <input data-toggle="popover" data-content="区域分区不能为空" data-placement="top" 
                        type="text" placeholder="" id="regionalpartition" name="regionalpartition" class="form-control">
                    </div>
                </div>

                <div class="form-group fix">
                    <label>联系人</label>
                    <div class="input-box">
                        <input data-toggle="popover" data-content="联系人不能为空" data-placement="top" 
                         type="text" placeholder="" id="linkname" name="linkname" class="form-control">
                    </div>
                </div>
                <div class="form-group fix">
                    <label>职位</label>
                    <div class="input-box">
                        <input data-toggle="popover" data-content="职位不能为空"  data-placement="top" 
                        type="text" placeholder="" id="title" name="title" class="form-control">
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
                    <label>常用Email</label>
                    <div class="input-box">
                        <input data-toggle="popover" data-content="常用Email格式不正确" data-placement="top" 
                        type="email" placeholder="" id="email" name="email" class="form-control">
                    </div>
                </div>
                <div class="form-group fix">
                    <label>手机</label>
                    <div class="input-box">
                        <input data-toggle="popover" data-placement="top" 
                         type="email" placeholder="" id="mobile" name="mobile" class="form-control">
                    </div>
                </div>
                
              <div class="form-group fix">
                <label>备注</label>
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
                $('#accountname').popover('destroy');
				if(''==$.trim($('#accountname').val())){
                    $('#accountname').attr('data-content', '公司名不能为空');
					$('#accountname').popover('show');
                    $('.popover-content').css({"color":"red","fontSize":"12px"});
                    setTimeout("$('#accountname').popover('destroy')", 2000);//2秒钟后隐藏
                    $("#accountname").focus();
					return false;
				}

                $('#customerproperty').popover('destroy');
                if(''==$.trim($('#customerproperty').val())){
                    $('#customerproperty').popover('show');
                    $('.popover-content').css({"color":"red","fontSize":"12px"});
                    setTimeout("$('#customerproperty').popover('destroy')", 2000);//2秒钟后隐藏
                    $("#customerproperty").focus();
                    return false;
                }
                $('#leadsource').popover('destroy');
                if(''==$.trim($('#leadsource').val())){
                    $('#leadsource').popover('show');
                    $('.popover-content').css({"color":"red","fontSize":"12px"});
                    setTimeout("$('#leadsource').popover('destroy')", 2000);//2秒钟后隐藏
                    $("#leadsource").focus();
                    return false;
                }
                //

                $('#phone').popover('destroy');
				if(''==$('#phone').val()){
					$('#phone').popover('show');
                    $('.popover-content').css({"color":"red","fontSize":"12px"});
                    setTimeout("$('#phone').popover('destroy')",2000);
                    $("#phone").focus();
					return false;
				}
                if (''==$('#province').val() && ''==$('#address').val()) {
                    // 地区
                    $('#province').popover('destroy');
                    if(''==$('#province').val()){
                        $('#province').popover('show');
                        $('.popover-content').css({"color":"red","fontSize":"12px"});
                        setTimeout("$('#province').popover('destroy')",2000);
                        $("#province").focus();
                        return false;
                    }
                }
                
                /*$('#city').popover('destroy');
                if(''==$('#city').val()){
                    $('#city').popover('show');
                    $('.popover-content').css({"color":"red","fontSize":"12px"});
                    setTimeout("$('#city').popover('destroy')",2000);
                    $("#city").focus();
                    return false;
                }
                $('#area').popover('destroy');
                if(''==$('#area').val()){
                    $('#area').popover('show');
                    $('.popover-content').css({"color":"red","fontSize":"12px"});
                    setTimeout("$('#area').popover('destroy')",2000);
                    $("#area").focus();
                    return false;
                }*/

                /*$('#address').popover('destroy');
                if(''==$('#address').val()){
                    $('#address').popover('show');
                    $('.popover-content').css({"color":"red","fontSize":"12px"});
                    setTimeout("$('#address').popover('destroy')",2000);
                    $("#address").focus();
                    return false;
                }*/

                $('#business').popover('destroy');
                if(''==$('#business').val()){
                    $('#business').popover('show');
                    $('.popover-content').css({"color":"red","fontSize":"12px"});
                    setTimeout("$('#business').popover('destroy')",2000);
                    $("#business").focus();
                    return false;
                }
                $('#businessarea').popover('destroy');
                if(''==$('#businessarea').val()){
                    $('#businessarea').popover('show');
                    $('.popover-content').css({"color":"red","fontSize":"12px"});
                    setTimeout("$('#businessarea').popover('destroy')",2000);
                    $("#businessarea").focus();
                    return false;
                }
                
                $('#regionalpartition').popover('destroy');
                if(''==$('#regionalpartition').val()){
                    $('#regionalpartition').popover('show');
                    $('.popover-content').css({"color":"red","fontSize":"12px"});
                    setTimeout("$('#regionalpartition').popover('destroy')",2000);
                    $("#regionalpartition").focus();
                    return false;
                }
                
                //linkname
                $('#linkname').popover('destroy');
                if(''==$('#linkname').val()){
                    $('#linkname').popover('show');
                    $('.popover-content').css({"color":"red","fontSize":"12px"});
                    setTimeout("$('#linkname').popover('destroy')",2000);
                    $("#linkname").focus();
                    return false;
                }
                
                //title
                $('#title').popover('destroy');
                if(''==$('#title').val()){
                    $('#title').popover('show');
                    $('.popover-content').css({"color":"red","fontSize":"12px"});
                    setTimeout("$('#title').popover('destroy')",2000);
                    $("#title").focus();
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
				return true;
			}

        $('#accountname').blur(function() {
            if (! $.trim($(this).val())) {
                //data-content
                $('#accountname').attr('data-content', '公司名不能为空');
                // 重复了
                $('#accountname').popover('show');
                $('.popover-content').css({"color":"red", "fontSize":"12px"});
                setTimeout("$('#accountname').popover('destroy')",2000);
                $("#accountname").focus();
                return false;
            }
            $.ajax({
                type: 'post', 
                url:"/index.php?module=Accounts&action=check_accountname",
                dataType :'json',
                data : {accountname: $.trim($('#accountname').val())},
                success: function(data) {
                    if(data.res == 'success'){
                        //data-content
                        $('#accountname').attr('data-content', '公司名重复');
                        // 重复了
                        $('#accountname').popover('show');
                        $('.popover-content').css({"color":"red", "fontSize":"12px"});
                        setTimeout("$('#accountname').popover('destroy')",2000);
                        $("#accountname").focus();
                    } else {
                        $('#accountname').attr('is_check', 1);
                    }
                },
                error:function(){
                    alert('error');
                }
            });
        });
        $('#dosave').on('click', function(){
            if(!check()){
                return false;
            }
            if (!$("#accountname").attr('is_check')) {
                return false;
            }
            $('#myForm2').ajaxSubmit({
                type: 'post', 
                url:"/index.php?module=Accounts&action=doaddAccounts",
                dataType :'json',
                success: function(data) {
                    if(data.res=='success'){

                        $('#dosave').popover('show');
                        $('#dosave').removeAttr("id"); 
                        alert('新建客户成功');
                        window.location.href='/index.php?action=mycrm';
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
