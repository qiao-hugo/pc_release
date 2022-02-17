<?php
/* Smarty version 3.1.28-dev/79, created on 2018-02-12 09:47:59
  from "/data/httpd/vtigerCRM/apps/views/VisitingOrder/unaudited.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a80f24f9fb987_41872295',
  'file_dependency' => 
  array (
    'e64d78af85259c3b41bcdd4313d5c8377d48ff3b' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/VisitingOrder/unaudited.html',
      1 => 1516969161,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_5a80f24f9fb987_41872295 ($_smarty_tpl) {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
		
		<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        <title>待跟进拜访</title>
</head>

<body>

	<div class="container-fluid w fix">
        <div class="row">
          
            <div class="tabs">
                <div class="bd">
                    <ul class="">
                    	<?php
$_from = $_smarty_tpl->tpl_vars['list']->value;
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
							
							<li class="fix">
	                            <a href="/index.php?module=VisitingOrder&action=detail&record=<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" class="fl">
	                                <div class="list"><?php echo $_smarty_tpl->tpl_vars['value']->value['related_to'];?>
</div>
	                                <div class="text">
	                                    <div class="mr20">主题：<?php echo $_smarty_tpl->tpl_vars['value']->value['subject'];?>
</div><div>外出类型：<?php echo $_smarty_tpl->tpl_vars['value']->value['outobjective'];?>
</div>
	                                </div>
	                            </a>
	                            <div class="fr right" data-toggle="modal" data-target="#myModal" onclick="opendl(<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
,<?php echo $_smarty_tpl->tpl_vars['value']->value['related_to_reference'];?>
)">+</div>
	                        </li>
						<?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_0_saved_local_item;
}
} else {
?>
								没有拜访单 
						<?php
}
if ($__foreach_value_0_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_0_saved_item;
}
?>
                     </ul>
                 </div>
            </div>
            <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        </div>
    </div>

<div class="modal fade" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">选择</h4>
                </div>
                <div class="modal-body">
                    <div class="confirm tc">  
                        <button class="btn btn1" onclick='openurl(1)'>添加跟进</button>
                        <button class="btn btn1" onclick='openurl(3)'>地点签到</button>
                        <!--<button class="btn btn1" onclick='openurl(4)'>图片签到</button>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
     <input type='hidden' id = 'accountid' value='0'>
    <input type='hidden' id = 'recode' value='0'>
    <?php echo '<script'; ?>
 type="text/javascript">
        function opendl(id,recode){
            $('#accountid').val(id);
            $('#recode').val(recode);
        }
        function openurl(type){
            
            if(type==1){
                var accountid = $('#recode').val();
                window.location.href = "/index.php?module=VisitingOrder&action=addMod&id="+accountid;
            }else if(type==2){
                var accountid = $('#recode').val();
                window.location.href = "/index.php?module=Accounts&action=addContact&id="+accountid;
            }else if(type==3){
                if($('#accountid').val()){
                    window.location.href='index.php?module=VisitingOrder&action=sign&id='+$('#accountid').val();
                }else{
                    alert('拜访单信息有误，请刷新后重试');
                }
            }else if(type==4){
                    window.location.href= 'index.php?module=VisitingOrder&action=picture&id='+$('#accountid').val();
            }
            
        }
    <?php echo '</script'; ?>
>
</body>
</html><?php }
}
