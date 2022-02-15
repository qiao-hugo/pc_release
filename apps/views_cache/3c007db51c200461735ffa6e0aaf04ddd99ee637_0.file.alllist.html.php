<?php
/* Smarty version 3.1.28-dev/79, created on 2017-12-19 09:13:49
  from "/data/httpd/vtigerCRM/apps/views/ExtensionTrial/alllist.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a3867cd4dc0b4_39659628',
  'file_dependency' => 
  array (
    '3c007db51c200461735ffa6e0aaf04ddd99ee637' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/ExtensionTrial/alllist.html',
      1 => 1510568109,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_5a3867cd4dc0b4_39659628 ($_smarty_tpl) {
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
            <div class="tabs">
                <ul class="hd fix">
                    <li class="on">&nbsp;</li>
                    <li class="on">延期审核</li>
                    <li class="on">&nbsp;</li>
                </ul>
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
                            <div style="width:60px;height: 60px;display: inline-block;border: 1px solid #ccc;border-radius: 60px;margin-right:3px;overflow: hidden;"><img src="<?php if (isset($_smarty_tpl->tpl_vars['USERIMGS']->value[$_smarty_tpl->tpl_vars['IMGMD']->value])) {
echo $_smarty_tpl->tpl_vars['USERIMGS']->value[$_smarty_tpl->tpl_vars['IMGMD']->value];
} else { ?>../../static/img/trueland.png<?php }?>" style="width:59px;height:59px;vertical-align: inherit;"></div>
                            <div style="display: inline-block;width: 70%;white-space: nowrap;font-size:18px;overflow: hidden;">
                            <div class="list"><?php echo $_smarty_tpl->tpl_vars['value']->value['contract_no'];?>
</div>
                            <div class="list" style="font-size: 14px;"><?php echo $_smarty_tpl->tpl_vars['value']->value['username'];?>
 <span>[<?php echo $_smarty_tpl->tpl_vars['value']->value['createdtime'];?>
]</span> </div>
                            </div>
                            <div class="fr right doExtensionTrial" style="position: absolute;top:22%;right:10px;" data-toggle="modal" data-contractid="<?php echo $_smarty_tpl->tpl_vars['value']->value['servicecontractsid'];?>
" data-id="<?php echo $_smarty_tpl->tpl_vars['value']->value['extensiontrialid'];?>
">+</div>
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
                        没有要审核的延期合同
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
 type="text/javascript">
   $(function(){
        $('.doExtensionTrial').click(function(){
            var dataid=$(this).data('id');
            var contractid=$(this).data('contractid');
            Tips.confirm({
                content: '确认要审核吗?',
                define: '确定',
                cancel: '取消',
                before: function(){
                },
                after: function(b){
                    if(b){
                        $.ajax({
                            url: "index.php?module=ExtensionTrial&action=doWorkflowStages",
                            data: {
                                id : dataid,
                                contractid:contractid
                            },
                            type:'POST',
                            success: function(data){
                                window.location.reload();
                            }
                        });
                    }else{
                    }
                }
            });
        });
   });
<?php echo '</script'; ?>
>
</html><?php }
}
