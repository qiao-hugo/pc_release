<?php
/* Smarty version 3.1.28-dev/79, created on 2018-01-26 20:19:51
  from "/data/httpd/vtigerCRM/apps/views/VisitingOrder/ajax_visiting_order_list.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a6b1ce71fb223_34170799',
  'file_dependency' => 
  array (
    '60fa2a346d4b5b68ee30afdd40831b4e5080f70d' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/VisitingOrder/ajax_visiting_order_list.html',
      1 => 1516969158,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a6b1ce71fb223_34170799 ($_smarty_tpl) {
?>
                        <?php
$_from = $_smarty_tpl->tpl_vars['today_list']->value;
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

                            <a href="index.php?module=VisitingOrder&action=detail&record=<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" class="fl" data-ajax="false" style="overflow:hidden;width:90%;">
                                <div style="width:60px;height: 60px;display: inline-block;border: 1px solid #ccc;border-radius: 60px;margin-right:3px;overflow: hidden;"><img src="<?php if (isset($_smarty_tpl->tpl_vars['USERIMGS']->value[$_smarty_tpl->tpl_vars['IMGMD']->value])) {
echo $_smarty_tpl->tpl_vars['USERIMGS']->value[$_smarty_tpl->tpl_vars['IMGMD']->value];
} else { ?>../../static/img/trueland.png<?php }?>" style="width:59px;height:59px;vertical-align: inherit;"></div>
                                <div style="display: inline-block;width: 70%;white-space: nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    <div class="list" style="font-size:16px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo $_smarty_tpl->tpl_vars['value']->value['accountnamer'];?>
 <span>[<?php echo $_smarty_tpl->tpl_vars['value']->value['contacts'];?>
]</span></div>
                                    <div class="list" style="font-size: 14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">主题：<?php echo $_smarty_tpl->tpl_vars['value']->value['subject'];?>
</div>
                                    <div class="text">
                                        <div class="mr20"><?php echo $_smarty_tpl->tpl_vars['value']->value['startdate'];?>
 </div><div><?php echo $_smarty_tpl->tpl_vars['value']->value['outobjective'];?>
</div><div style="margin-left:10px;"><?php if ($_smarty_tpl->tpl_vars['value']->value['modulestatus'] == 'c_complete') {?><span class="label label-primary">完成</span><?php } elseif ($_smarty_tpl->tpl_vars['value']->value['modulestatus'] == 'a_normal') {?><span class="label label-info">正常</span><?php } elseif ($_smarty_tpl->tpl_vars['value']->value['modulestatus'] == 'a_exception') {?><span class="label label-danger">打回中</span><?php } elseif ($_smarty_tpl->tpl_vars['value']->value['modulestatus'] == 'c_canceling') {?><span class="label label-default">作废中</span><?php } elseif ($_smarty_tpl->tpl_vars['value']->value['modulestatus'] == 'c_cancel') {?><span class="label label-warning">作废</span><?php }?></div>
                                    </div>
                                </div>
                            </a>
                            <?php if ($_smarty_tpl->tpl_vars['value']->value['modulestatus'] == 'c_complete' || $_smarty_tpl->tpl_vars['value']->value['modulestatus'] == 'a_normal') {?><div class="fr right" style="position: absolute;top:22%;right:10px;" data-toggle="modal"  data-target="#myModal"  onclick='opendl(<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
,<?php echo $_smarty_tpl->tpl_vars['value']->value['related_to_reference'];?>
)'>+</div><?php }?>
                        </li>
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
}
}
