<?php
/* Smarty version 3.1.28-dev/79, created on 2018-01-11 09:47:37
  from "/data/httpd/vtigerCRM/apps/views/ExtensionTrial/reminder.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a56c239474c68_07715477',
  'file_dependency' => 
  array (
    '1d2de89322d6b79cca655c55834ce003337c9da4' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/ExtensionTrial/reminder.html',
      1 => 1515635243,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_5a56c239474c68_07715477 ($_smarty_tpl) {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
		<title>服务合同延期审核</title>
		<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

    <style type="text/css">
        *{
            text-shadow:none;
        }
		.select2 {
            width:100%;
            height:35px;
        }
    </style>
</head>

<body>
    <div class="container-fluid w fix see-visit-list">
        <div class="row">
            <div class="list-head">
                <ul class="fix">
                    <li class="headon" style="width:100%;"><span  data-ajax="false">超期合同列表</span></li>

                </ul>
            </div>
            <div class="tabs">
                <div class="bd" style="padding: 0;">
                    <ul class="ttt_list">
                        <?php if (!empty($_smarty_tpl->tpl_vars['list']->value)) {?>
                        <?php
$_from = $_smarty_tpl->tpl_vars['list']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_value_0_saved_item = isset($_smarty_tpl->tpl_vars['value']) ? $_smarty_tpl->tpl_vars['value'] : false;
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable();
$__foreach_value_0_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_value_0_total) {
$__foreach_value_0_first = true;
foreach ($_from as $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->first = $__foreach_value_0_first;
$__foreach_value_0_first = false;
$__foreach_value_0_saved_local_item = $_smarty_tpl->tpl_vars['value'];
?>
                        <?php $_smarty_tpl->tpl_vars['IMGMD'] = new Smarty_Variable(md5($_smarty_tpl->tpl_vars['value']->value['email']), null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'IMGMD', 0);?>
                         <li class="fix" style="border-bottom: 1px solid #ccc;<?php if ($_smarty_tpl->tpl_vars['value']->first) {?>border-top: 1px solid #ccc;<?php }?>padding:5px 10px;margin-bottom: 0;position: relative;">
                            <div style="width:60px;height: 60px;float:left;border: 1px solid #ccc;border-radius: 60px;margin-right:10px;overflow: hidden;"><img src="<?php if (isset($_smarty_tpl->tpl_vars['USERIMGS']->value[$_smarty_tpl->tpl_vars['IMGMD']->value])) {
echo $_smarty_tpl->tpl_vars['USERIMGS']->value[$_smarty_tpl->tpl_vars['IMGMD']->value];
} else { ?>../../static/img/trueland.png<?php }?>" style="width:59px;height:59px;vertical-align: inherit;"></div>
                            <div class="content" style="float:left;margin-top:5px;white-space: nowrap;font-size:18px;overflow: hidden;text-overflow: ellipsis;">
                                <div class="list"><?php echo $_smarty_tpl->tpl_vars['value']->value['contract_no'];?>
</div>
                                <div class="list" style="font-size: 14px;-ms-text-overflow: ellipsis;text-overflow: ellipsis;overflow: hidden;"><?php echo $_smarty_tpl->tpl_vars['value']->value['userid'];?>
 <span>[<?php echo $_smarty_tpl->tpl_vars['value']->value['receivedate'];?>
]</span></div>
                            </div>
                            <div class="fr right" style="float:left;font-size: 16px;width:50px;height:50px;line-height:50px;" data-toggle="modal" data-contractid="<?php echo $_smarty_tpl->tpl_vars['value']->value['servicecontractsid'];?>
" data-id="<?php echo $_smarty_tpl->tpl_vars['value']->value['extensiontrialid'];?>
"><?php echo $_smarty_tpl->tpl_vars['value']->value['diffdate'];?>
</div>
                        </li>

                        <?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_0_saved_local_item;
}
}
if ($__foreach_value_0_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_0_saved_item;
}
?>
                        <?php } else { ?>
                        <?php }?>
                    </ul>
                </div>
            </div>
            <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        </div>
    </div>
</body>
<?php echo '<script'; ?>
>
    $(function(){
        var ulWidth=$(".ttt_list").width();
        var contentWith=ulWidth-140;
        $('.content').css('width',contentWith);
    });


<?php echo '</script'; ?>
>
</html><?php }
}
