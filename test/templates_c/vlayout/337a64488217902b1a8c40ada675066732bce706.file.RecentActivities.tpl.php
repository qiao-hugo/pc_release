<?php /* Smarty version Smarty-3.1.7, created on 2022-02-14 16:37:10
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\Accounts\RecentActivities.tpl" */ ?>
<?php /*%%SmartyHeaderCode:4143620a14b6946488-89920557%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '337a64488217902b1a8c40ada675066732bce706' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\Accounts\\RecentActivities.tpl',
      1 => 1523874467,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4143620a14b6946488-89920557',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'RECENT_ACTIVITIES' => 0,
    'RECENT_ACTIVITY' => 0,
    'MODULE_NAME' => 0,
    'FIELDMODEL' => 0,
    'RELATION' => 0,
    'URELATION' => 0,
    'PAGING_MODEL' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620a14b69d7bb',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620a14b69d7bb')) {function content_620a14b69d7bb($_smarty_tpl) {?>
<div class="recentActivitiesContainer"><div><?php if (!empty($_smarty_tpl->tpl_vars['RECENT_ACTIVITIES']->value)){?><ul class="unstyled"><?php  $_smarty_tpl->tpl_vars['RECENT_ACTIVITY'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->_loop = false;
 $_smarty_tpl->tpl_vars['KEG'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['RECENT_ACTIVITIES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->key => $_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value){
$_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->_loop = true;
 $_smarty_tpl->tpl_vars['KEG']->value = $_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->key;
?><div class="bs-callout bs-callout-warning"><?php if ($_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value->isCreate()){?><li><div><span><strong><?php echo $_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value->getModifiedBy()->getName();?>
</strong> <?php echo vtranslate('LBL_CREATED',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</span><span class="pull-right"><p class="muted"><small title="<?php echo Vtiger_Util_Helper::formatDateTimeIntoDayString($_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value->getParent()->get('createdtime'));?>
"><?php echo $_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value->getParent()->get('createdtime');?>
</small></p></span></div></li><?php }elseif($_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value->isUpdate()){?><li><div><span><strong><?php echo $_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value->getModifiedBy()->getDisplayName();?>
</strong> <?php echo vtranslate('LBL_UPDATED',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</span><span class="pull-right"><p class="muted"><small title="<?php echo Vtiger_Util_Helper::formatDateTimeIntoDayString($_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value->getActivityTime());?>
"><?php echo $_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value->getActivityTime();?>
</small></p></span></div><?php  $_smarty_tpl->tpl_vars['FIELDMODEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['FIELDMODEL']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value->getFieldInstances(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['FIELDMODEL']->key => $_smarty_tpl->tpl_vars['FIELDMODEL']->value){
$_smarty_tpl->tpl_vars['FIELDMODEL']->_loop = true;
?><?php if ($_smarty_tpl->tpl_vars['FIELDMODEL']->value&&$_smarty_tpl->tpl_vars['FIELDMODEL']->value->getFieldInstance()&&$_smarty_tpl->tpl_vars['FIELDMODEL']->value->getFieldInstance()->isViewableInDetailView()){?><div class='font-x-small updateInfoContainer'><i><?php echo vtranslate($_smarty_tpl->tpl_vars['FIELDMODEL']->value->getName(),$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</i> :&nbsp;<?php if ($_smarty_tpl->tpl_vars['FIELDMODEL']->value->get('prevalue')!=''){?><?php echo preg_replace('!<[^>]*?>!', ' ', htmlspecialchars_decode($_smarty_tpl->tpl_vars['FIELDMODEL']->value->getDisplayValue($_smarty_tpl->tpl_vars['FIELDMODEL']->value->get('prevalue'))));?>
&nbsp;<?php echo vtranslate('LBL_TO',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
&nbsp;<?php }else{ ?><?php }?><b><?php echo preg_replace('!<[^>]*?>!', ' ', htmlspecialchars_decode($_smarty_tpl->tpl_vars['FIELDMODEL']->value->getDisplayValue($_smarty_tpl->tpl_vars['FIELDMODEL']->value->get('postvalue'))));?>
</b></div><?php }?><?php if ($_smarty_tpl->tpl_vars['FIELDMODEL']->value->get('fieldname')=='serviceid'){?><div class='font-x-small updateInfoContainer'><i><?php echo vtranslate($_smarty_tpl->tpl_vars['FIELDMODEL']->value->getName(),$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</i> :&nbsp;<?php if ($_smarty_tpl->tpl_vars['FIELDMODEL']->value->get('prevalue')!=''){?><?php echo preg_replace('!<[^>]*?>!', ' ', htmlspecialchars_decode($_smarty_tpl->tpl_vars['FIELDMODEL']->value->get('prevalue')));?>
&nbsp;<?php echo vtranslate('LBL_TO',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
&nbsp;<?php }else{ ?><?php }?><b><?php echo preg_replace('!<[^>]*?>!', ' ', htmlspecialchars_decode($_smarty_tpl->tpl_vars['FIELDMODEL']->value->get('postvalue')));?>
</b></div><?php }?><?php } ?></li><?php }elseif($_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value->isRelationLink()){?><li><div class="row-fluid"><?php $_smarty_tpl->tpl_vars['RELATION'] = new Smarty_variable($_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value->getRelationInstance(), null, 0);?><span><?php echo vtranslate($_smarty_tpl->tpl_vars['RELATION']->value->getLinkedRecord()->getModuleName(),$_smarty_tpl->tpl_vars['RELATION']->value->getLinkedRecord()->getModuleName());?>
 <?php echo vtranslate('LBL_ADDED',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
 <strong><?php echo $_smarty_tpl->tpl_vars['RELATION']->value->getLinkedRecord()->getName();?>
</strong></span><span class="pull-right"><p class="muted"><small title="<?php echo Vtiger_Util_Helper::formatDateTimeIntoDayString($_smarty_tpl->tpl_vars['RELATION']->value->getLinkedRecord()->get('createdtime'));?>
"><?php echo $_smarty_tpl->tpl_vars['RELATION']->value->getLinkedRecord()->get('createdtime');?>
</small></p></span></div></li><?php }elseif($_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value->isRelationUnLink()){?><li><div class="row-fluid"><?php $_smarty_tpl->tpl_vars['URELATION'] = new Smarty_variable($_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value->getRelationInstance(), null, 0);?><span><?php echo vtranslate($_smarty_tpl->tpl_vars['URELATION']->value->getUnLinkedRecord()->getModuleName(),$_smarty_tpl->tpl_vars['URELATION']->value->getUnLinkedRecord()->getModuleName());?>
 <?php echo vtranslate('LBL_REMOVED',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
 <strong><?php echo $_smarty_tpl->tpl_vars['URELATION']->value->getUnLinkedRecord()->getName();?>
</strong></span><span class="pull-right"><p class="muted"><small title="<?php echo Vtiger_Util_Helper::formatDateTimeIntoDayString($_smarty_tpl->tpl_vars['URELATION']->value->getUnLinkedRecord()->get('modifiedtime'));?>
"><?php echo $_smarty_tpl->tpl_vars['URELATION']->value->getUnLinkedRecord()->get('modifiedtime');?>
</small></p></span></div></li><?php }elseif($_smarty_tpl->tpl_vars['RECENT_ACTIVITY']->value->isRestore()){?><li></li><?php }?></div><?php } ?></ul><?php }else{ ?><div class="bs-callout bs-callout-warning"><p class="textAlignCenter"><?php echo vtranslate('LBL_NO_RECENT_UPDATES');?>
</p></div><?php }?></div><?php if ($_smarty_tpl->tpl_vars['PAGING_MODEL']->value->isNextPageExists()){?><div class="row-fluid"><div class="pull-right"><a href="javascript:void(0)" class="moreRecentUpdates"><?php echo vtranslate('LBL_MORE',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
..</a></div></div><?php }?><span class="clearfix"></span></div><?php }} ?>