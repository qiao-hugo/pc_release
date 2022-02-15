<?php
/* Smarty version 3.1.28-dev/79, created on 2017-12-28 18:00:59
  from "/data/httpd/vtigerCRM/apps/Knowledge/notice.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a44c0db72e4d8_97381296',
  'file_dependency' => 
  array (
    'c4a67b73871a7dd55e221469f7fa50d55e85bfe2' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/Knowledge/notice.html',
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
function content_5a44c0db72e4d8_97381296 ($_smarty_tpl) {
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

</head>
<body>
    <div class="container-fluid w fix notice">
        <div class="row">
            
            <div class="tabs">
                <ul class="hd fix">
                    <li class="on">公</li>
                    <li class="on">告</li>
                </ul>
                <div class="bd">
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
                    <div class="panel panel-default">
                        <div class="panel-heading" data-toggle="collapse"
                             data-parent="#accordion3" href="#collapse<?php echo $_smarty_tpl->tpl_vars['value']->value['knowledgeid'];?>
">
                            <a class="accordion-toggle"  data-toggle="collapse" data-parent="#accordion3" href="#collapse<?php echo $_smarty_tpl->tpl_vars['value']->value['knowledgeid'];?>
" style="display:block;height:100%;width:100%;">
                            <?php if ($_smarty_tpl->tpl_vars['value']->value['knowledgetop'] == '是') {?>【置顶】<?php }?><span class="list"><?php echo $_smarty_tpl->tpl_vars['value']->value['knowledgetitle'];?>
</span><h6><span><?php echo $_smarty_tpl->tpl_vars['value']->value['cmdtime'];?>
</span><h6></a>
                        </div>
                        <div id="collapse<?php echo $_smarty_tpl->tpl_vars['value']->value['knowledgeid'];?>
" class="panel-collapse collapse" style="height: 0px;">
                            <div class="panel-body">
                                    <?php echo htmlspecialchars_decode($_smarty_tpl->tpl_vars['value']->value['knowledgecontent'], ENT_QUOTES);?>

                                <div style="clear:both;"></div>
                            </div>
                        </div>
                    </div>

                        <?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_0_saved_local_item;
}
} else {
?>
                    <div class="list">暂无公告</div>
                        <?php
}
if ($__foreach_value_0_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_0_saved_item;
}
?>
                </div>
            </div>
            <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        </div>
    </div>
    <?php echo '<script'; ?>
>

    <?php echo '</script'; ?>
>
</body>
</html>
<?php }
}
