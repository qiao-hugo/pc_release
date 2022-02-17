<?php /* Smarty version Smarty-3.1.7, created on 2022-02-14 16:37:10
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\Accounts\RelatedActivities.tpl" */ ?>
<?php /*%%SmartyHeaderCode:4754620a14b6de28c1-75956388%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4a2318de0ed9338cc61569bbc8c1e7f8845c9dd6' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\Accounts\\RelatedActivities.tpl',
      1 => 1638431461,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4754620a14b6de28c1-75956388',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'ENTITY_FIRST' => 0,
    'MODULE_NAME' => 0,
    'ALLCONTACTS' => 0,
    'RECENT_ACTIVITY' => 0,
    'PAGING_MODEL' => 0,
    'RECENT_ACTIVITIESAND' => 0,
    'RECENT_ACTIVITIES' => 0,
    'RECENT_HEADS' => 0,
    'RECENT_HEAD' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620a14b6e64a9',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620a14b6e64a9')) {function content_620a14b6e64a9($_smarty_tpl) {?>
<div  class="summaryWidgetContainer"><div class="widget_header row-fluid"><span class="span8"><h4 class="textOverflowEllipsis">联系人</h4></span><span class="span4"></span></div><div><ul class="unstyled"><div class="bs-callout bs-callout-info"><li><div><span><i>首要联系人</i> :&nbsp;<strong><?php echo $_smarty_tpl->tpl_vars['ENTITY_FIRST']->value['linkname'];?>
</strong></span><span class="pull-right"><p class="muted"><small title=""></small></p></span></div><div><span><i>性别</i> :&nbsp;<strong><?php echo vtranslate($_smarty_tpl->tpl_vars['ENTITY_FIRST']->value['gendertype'],$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</strong></span><span class="pull-right"><p class="muted"><small title=""></small></p></span></div><div><span><i>手机</i> :&nbsp;<strong><?php echo vtranslate($_smarty_tpl->tpl_vars['ENTITY_FIRST']->value['mobile'],$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</strong></span><span class="pull-right"><p class="muted"><small title=""></small></p></span></div><div><span><i>办公电话</i> :&nbsp;<strong><?php echo vtranslate($_smarty_tpl->tpl_vars['ENTITY_FIRST']->value['phone'],$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</strong></span><span class="pull-right"><p class="muted"><small title=""></small></p></span></div><div><span><i>职务</i> :&nbsp;<strong><?php echo vtranslate($_smarty_tpl->tpl_vars['ENTITY_FIRST']->value['title'],$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</strong></span><span class="pull-right"><p class="muted"><small title=""></small></p></span></div><div><span><i>决策圈</i> :&nbsp;<strong><?php echo vtranslate($_smarty_tpl->tpl_vars['ENTITY_FIRST']->value['makedecisiontype'],$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</strong></span><span class="pull-right"><p class="muted"><small title=""></small></p></span></div><div><span><i>邮箱</i> :&nbsp;<strong><?php echo vtranslate($_smarty_tpl->tpl_vars['ENTITY_FIRST']->value['email1'],$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</strong></span><span class="pull-right"><p class="muted"><small title=""></small></p></span></div></li></div></ul><?php if (!empty($_smarty_tpl->tpl_vars['ALLCONTACTS']->value)){?><ul class="unstyled"><?php  $_smarty_tpl->tpl_vars['RECENT_ACTIVITY'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['ALLCONTACTS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->key => $_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value){
$_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->_loop = true;
?><div class="bs-callout bs-callout-warning"><li><div><span><i>联系人</i> :&nbsp;<a href="/index.php?module=Contacts&view=Detail&record=<?php echo $_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value['contactid'];?>
"><strong><?php echo $_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value['name'];?>
</strong></a></span><span class="pull-right"><p class="muted"><small title=""></small></p></span></div><div><span><i>性别</i> :&nbsp;<strong><?php echo vtranslate($_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value['gender'],$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</strong></span><span class="pull-right"><p class="muted"><small title=""></small></p></span></div><div><span><i>手机</i> :&nbsp;<strong><?php echo vtranslate($_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value['mobile'],$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</strong></span><span class="pull-right"><p class="muted"><small title=""></small></p></span></div><div><span><i>办公电话</i> :&nbsp;<strong><?php echo vtranslate($_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value['phone'],$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</strong></span><span class="pull-right"><p class="muted"><small title=""></small></p></span></div><div><span><i>职务</i> :&nbsp;<strong><?php echo vtranslate($_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value['title'],$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</strong></span><span class="pull-right"><p class="muted"><small title=""></small></p></span></div><div><span><i>决策圈</i> :&nbsp;<strong><?php echo vtranslate($_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value['makedecision'],$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</strong></span><span class="pull-right"><p class="muted"><small title=""></small></p></span></div><div><span><i>邮箱</i> :&nbsp;<strong><?php echo vtranslate($_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value['email'],$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</strong></span><span class="pull-right"><p class="muted"><small title=""></small></p></span></div></li></div><?php } ?></ul><?php }?></div><?php if ($_smarty_tpl->tpl_vars['PAGING_MODEL']->value->isNextPageExists()){?><div class="row-fluid"><div class="pull-right"><a href="javascript:void(0)" class="moreRecentUpdates"><?php echo vtranslate('LBL_MORE',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
..</a></div></div><?php }?><span class="clearfix"></span></div><div  class="summaryWidgetContainer"><div class="widget_header row-fluid"><span class="span8"><h4 class="textOverflowEllipsis">负责人&客  服 信息</h4></span><span class="span4"></span></div><div><?php if (!empty($_smarty_tpl->tpl_vars['RECENT_ACTIVITIESAND']->value)){?><ul class="unstyled"><div class="bs-callout bs-callout-warning"><li><div><span><i>负责人</i> :&nbsp;<strong><?php echo $_smarty_tpl->tpl_vars['RECENT_ACTIVITIESAND']->value['h']['last_name'];?>
</strong></span><span class="pull-right"><p class="muted"><small title=""></small></p></span></div><div><span><i>邮箱</i> :&nbsp;<strong><?php echo $_smarty_tpl->tpl_vars['RECENT_ACTIVITIESAND']->value['h']['email1'];?>
</strong></span><span class="pull-right"><p class="muted"><small title=""></small></p></span></div><div><span><i>办公电话</i> :&nbsp;<strong><?php echo $_smarty_tpl->tpl_vars['RECENT_ACTIVITIESAND']->value['h']['phone_mobile'];?>
</strong></span><span class="pull-right"><p class="muted"><small title=""></small></p></span></div><div><span><i>手机</i> :&nbsp;<strong><?php echo $_smarty_tpl->tpl_vars['RECENT_ACTIVITIESAND']->value['h']['phone_work'];?>
</strong></span><span class="pull-right"><p class="muted"><small title=""></small></p></span></div></li></div></ul><?php if (!empty($_smarty_tpl->tpl_vars['RECENT_ACTIVITIESAND']->value['f'])){?><ul class="unstyled"><div class="bs-callout bs-callout-warning"><li><div><span><i>客服</i> :&nbsp;<strong><?php echo $_smarty_tpl->tpl_vars['RECENT_ACTIVITIESAND']->value['f']['last_name'];?>
</strong></span><span class="pull-right"><p class="muted"><small title=""></small></p></span></div><div><span><i>邮箱</i> :&nbsp;<strong><?php echo $_smarty_tpl->tpl_vars['RECENT_ACTIVITIESAND']->value['f']['email1'];?>
</strong></span><span class="pull-right"><p class="muted"><small title=""></small></p></span></div><div><span><i>办公电话</i> :&nbsp;<strong><?php echo $_smarty_tpl->tpl_vars['RECENT_ACTIVITIESAND']->value['f']['phone_mobile'];?>
</strong></span><span class="pull-right"><p class="muted"><small title=""></small></p></span></div><div><span><i>手机</i> :&nbsp;<strong><?php echo $_smarty_tpl->tpl_vars['RECENT_ACTIVITIESAND']->value['f']['phone_work'];?>
</strong></span><span class="pull-right"><p class="muted"><small title=""></small></p></span></div></li></div></ul><?php }?><?php }?></div><?php if ($_smarty_tpl->tpl_vars['PAGING_MODEL']->value->isNextPageExists()){?><div class="row-fluid"><div class="pull-right"><a href="javascript:void(0)" class="moreRecentUpdates"><?php echo vtranslate('LBL_MORE',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
..</a></div></div><?php }?><span class="clearfix"></span></div><div  class="summaryWidgetContainer"><div class="widget_header row-fluid"><span class="span8"><h4 class="textOverflowEllipsis">客  服</h4></span><span class="span4"></span></div><div><?php if (!empty($_smarty_tpl->tpl_vars['RECENT_ACTIVITIES']->value)){?><ul class="unstyled"><?php  $_smarty_tpl->tpl_vars['RECENT_ACTIVITY'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['RECENT_ACTIVITIES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->key => $_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value){
$_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->_loop = true;
?><div class="bs-callout bs-callout-warning"><li><div><span><i>客服</i> :&nbsp;<strong><?php echo $_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value['last_name'];?>
</strong></span><span class="pull-right"><p class="muted"><small title=""></small></p></span></div><div class='font-x-small updateInfoContainer'><i>备 注</i> :&nbsp;<b><?php echo $_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value['remark'];?>
</b></div></li></div><?php } ?></ul><?php }else{ ?><div class="bs-callout bs-callout-warning"><p class="textAlignCenter">暂未分配客服</p></div><?php }?></div><?php if ($_smarty_tpl->tpl_vars['PAGING_MODEL']->value->isNextPageExists()){?><div class="row-fluid"><div class="pull-right"><a href="javascript:void(0)" class="moreRecentUpdates"><?php echo vtranslate('LBL_MORE',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
..</a></div></div><?php }?><span class="clearfix"></span></div><div  class="summaryWidgetContainer"><div class="widget_header row-fluid"><span class="span8"><h4 class="textOverflowEllipsis">负责人</h4></span><span class="span4"></span></div><div><?php if (!empty($_smarty_tpl->tpl_vars['RECENT_HEADS']->value)){?><ul class="unstyled"><?php  $_smarty_tpl->tpl_vars['RECENT_HEAD'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['RECENT_HEAD']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['RECENT_HEADS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['RECENT_HEAD']->key => $_smarty_tpl->tpl_vars['RECENT_HEAD']->value){
$_smarty_tpl->tpl_vars['RECENT_HEAD']->_loop = true;
?><div class="bs-callout bs-callout-warning"><li><div class='font-x-small updateInfoContainer'><i>负责人</i> :&nbsp;<?php echo $_smarty_tpl->tpl_vars['RECENT_HEAD']->value['oldname'];?>
&nbsp;&nbsp;&nbsp;更改为&nbsp;&nbsp;&nbsp;<b><?php echo $_smarty_tpl->tpl_vars['RECENT_HEAD']->value['newname'];?>
</b><span class="pull-right"><i>修改人</i> :&nbsp;<?php echo $_smarty_tpl->tpl_vars['RECENT_HEAD']->value['mname'];?>
&nbsp;<p class="muted"><small title=""><?php echo $_smarty_tpl->tpl_vars['RECENT_HEAD']->value['createdtime'];?>
</small></p></span></div></li></div><?php } ?></ul><?php }else{ ?><div class="bs-callout bs-callout-warning"><p class="textAlignCenter">暂未记录</p></div><?php }?></div><span class="clearfix"></span></div>
<?php }} ?>