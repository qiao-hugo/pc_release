<?php
/* Smarty version 3.1.28-dev/79, created on 2018-01-26 20:28:05
  from "/data/httpd/vtigerCRM/apps/views/VisitingOrder/addmod.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a6b1ed504ee12_95788389',
  'file_dependency' => 
  array (
    '1a1ef223c09c96278e839d3a4f4edab28fb56161' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/VisitingOrder/addmod.html',
      1 => 1516969157,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_5a6b1ed504ee12_95788389 ($_smarty_tpl) {
?>
<!DOCTYPE HTML>
<html>
<head>
		<title>添加跟进</title>
		<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

		<link type="text/css" rel="stylesheet" href="/css/calendar.min.css" />
		<?php echo '<script'; ?>
 src="static/js/jquery.form.js"><?php echo '</script'; ?>
>

</head>

<body>

<div class="container-fluid w fix">
        <div class="row">
         
            <div class="add-follow-up">
            	<form id='myform'>
                <div class="form-group fix">
                	<input type="hidden" id="accountid" name="accountid" value=<?php echo $_smarty_tpl->tpl_vars['accountid']->value;?>
 >
                    <label>跟进目的</label>
                    <div class="input-box">
                        <select  id="modcommentpurpose" name="modcommentpurpose"  class="form-control" >
								<option value='邀约拜访' selected>邀约拜访</option>
								<option value='商谈合同'>商谈合同</option>
								<option value='签订合同'>签订合同</option>
								<option value='项目收款'>项目收款</option>
								<option value='启动通知'>启动通知</option>
								<option value='日常维护'>日常维护</option>
								<option value='客服甩单'>客服甩单</option>
								<option value='问题处理'>问题处理</option>
							</select>
                    </div>
                </div>
                <div class="form-group fix">
                    <label>联系人</label>
                    <div class="input-box"  >
                        <select class="form-control"  id="contact_id" name="contact_id" data-toggle="popover" data-placement="top" 
                        data-content="请选择联系人" >
								<?php
$_from = $_smarty_tpl->tpl_vars['contacts']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_value_0_saved_item = isset($_smarty_tpl->tpl_vars['value']) ? $_smarty_tpl->tpl_vars['value'] : false;
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable();
$__foreach_value_0_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_value_0_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['value']->value) {
$__foreach_value_0_saved_local_item = $_smarty_tpl->tpl_vars['value'];
?>
									<option value="<?php echo $_smarty_tpl->tpl_vars['value']->value['contactid'];?>
" ><?php echo $_smarty_tpl->tpl_vars['value']->value['name'];?>
</option>
								<?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_0_saved_local_item;
}
} else {
?>

								<?php
}
if ($__foreach_value_0_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_0_saved_item;
}
?>
						</select>
                    </div>
                </div>
                <div class="form-group fix">
                    <label>跟进类型</label>
                    <div class="input-box">
                        
						<select class="form-control" id="modcommenttype" name="modcommenttype"  >
								<option value='邮件告知促销活动' selected>邮件告知促销活动</option>
								<option value='电话推广促销活动'>电话推广促销活动</option>
								<option value='发布宝通知账户'>发布宝通知账户</option>
								<option value='网建培训邀请'>网建培训邀请</option>
								<option value='市场活动培训邀请'>市场活动培训邀请</option>
								<option value='微整站提供排名报表'>微整站提供排名报表</option>
								<option value='产品续费'>产品续费</option>
								<option value='发票寄送'>发票寄送</option>
								<option value='其他'>其他</option>
							</select>
                    </div>
                </div>
                <div class="form-group fix">
                    <label>跟进方式</label>
                    <div class="input-box">
                        <select class="form-control" id="modcommentmode" name="modcommentmode"  >
								<option value='电话'>电话</option>
								<option value='短信通知'>短信通知</option>
								<option value='邮件'>邮件</option>
								<option value='拜访' selected>拜访</option>
								<option value='面谈'>面谈</option>
						</select>
                    </div>
                </div>
                <div class="form-group fix">
                    <label>跟进内容</label>
                    <div class="input-box" >
                        <textarea data-toggle="popover" data-placement="top" 
                        data-content="跟进内容不能为空" class="form-control"  rows="5" id="commentcontent" name="commentcontent"  placeholder="请输入内容...."></textarea>
                    </div>
                </div>
   
                <div class="confirm tc confirm2" >
                    <button id='dosave' class="btn" data-toggle="popover" data-placement="top" 
                        data-content="添加跟进成功">提 交</button>
                </div>
              </form>
            </div>
            <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        </div>
    </div>


	<?php echo '<script'; ?>
 type="text/javascript">       
        $('#dosave').on('click', function() {
            if( $('#commentcontent').val() == ''){
                $('#commentcontent').popover('show');
                $('.popover-content').css("color",'red');
                setTimeout("$('#commentcontent').popover('destroy')",2000);
                return false;
            }        
            if( $('#contact_id').val() == ''){
                $('#contact_id').popover('show');
                $('.popover-content').css("color",'red');
                setTimeout("$('#contact_id').popover('destroy')",2000);
                return false;
            }            
            $('#myform').ajaxSubmit({
                type: 'post', 
                url:"/index.php?module=VisitingOrder&action=doaddmod",
                dataType :'json',
                success: function(data) { 
                    if(data.res=='success'){
                        $('#dosave').popover('show');
                        $('#myform').resetForm();
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
