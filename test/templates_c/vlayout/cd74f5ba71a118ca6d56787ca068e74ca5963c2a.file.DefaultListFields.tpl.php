<?php /* Smarty version Smarty-3.1.7, created on 2022-02-15 16:39:08
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\ReceivedPayments\DefaultListFields.tpl" */ ?>
<?php /*%%SmartyHeaderCode:29419620b66ac854ec2-57640397%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cd74f5ba71a118ca6d56787ca068e74ca5963c2a' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\ReceivedPayments\\DefaultListFields.tpl',
      1 => 1637465767,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '29419620b66ac854ec2-57640397',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LISTVIEW_LINKS' => 0,
    'MODULE' => 0,
    'LISTVIEW_BASICACTION' => 0,
    'ISDEFAULT' => 0,
    'MODULE_MODEL' => 0,
    'ISPAGE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620b66ac8727c',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620b66ac8727c')) {function content_620b66ac8727c($_smarty_tpl) {?><div class="row" style="padding: 10px 20px 0 20px;"><span class="span4"><?php if (count($_smarty_tpl->tpl_vars['LISTVIEW_LINKS']->value['LISTVIEWBASIC'])>0){?><?php  $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_LINKS']->value['LISTVIEWBASIC']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->key => $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->_loop = true;
?><span class="btn-group"><button id="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
_listView_basicAction_<?php echo Vtiger_Util_Helper::replaceSpaceWithUnderScores($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getLabel());?>
" class="btn addButton" <?php if (stripos($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getUrl(),'javascript:')===0){?> onclick='<?php echo substr($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getUrl(),strlen("javascript:"));?>
;'<?php }else{ ?> onclick='window.location.href="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getUrl();?>
"'<?php }?>><i class="icon-plus icon-white"></i>&nbsp;<strong><?php echo vtranslate($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getLabel(),$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong></button></span><?php } ?><?php }?><?php if ($_smarty_tpl->tpl_vars['ISDEFAULT']->value){?><?php if ($_GET['filter']!='changeHistory'){?><?php if ($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->exportGrouprt('ReceivedPayments','COLLATE')){?><span class="btn-group"><button id="exportContract" class="btn btn-info"><strong>导出</strong></button>&nbsp;</span><span class="btn-group"><button id="collateContract" class="btn btn-info"><strong>核对</strong></button>&nbsp;</span><?php }?><span>&nbsp;<input onclick="Vtiger_List_Js.showUserFieldEdit('index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=FieldAjax');" class="btn"type="button" value="列表自定义"></span><?php }?><?php }?></span><?php if ($_smarty_tpl->tpl_vars['ISPAGE']->value){?><span class="span8" style="text-align: right">  <span>&nbsp;<select id="limit" name="limit" style="width: 80px;margin-top: -13px;"><option value="10">10</option><option value="20" selected="selected">20</option><option value="50">50</option><option value="100">100</option></select>&nbsp;</span><span class="pagination" style="" id="pagination"><ul class="pagination-demo"></ul></span><span>&nbsp;<input type="text" name="jumppage" value="" id="jumppage" class="input-small"style="width: 50px;margin-top: -13px;" placeholder="跳转">&nbsp;</span></span><?php }?></div>
<?php }} ?>