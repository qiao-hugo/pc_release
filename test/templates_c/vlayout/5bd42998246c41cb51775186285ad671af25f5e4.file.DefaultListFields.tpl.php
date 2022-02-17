<?php /* Smarty version Smarty-3.1.7, created on 2022-02-14 13:45:54
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\ServiceContracts\DefaultListFields.tpl" */ ?>
<?php /*%%SmartyHeaderCode:250626209ec920921b2-94300278%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5bd42998246c41cb51775186285ad671af25f5e4' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\ServiceContracts\\DefaultListFields.tpl',
      1 => 1638184433,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '250626209ec920921b2-94300278',
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
  'unifunc' => 'content_6209ec920ae84',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_6209ec920ae84')) {function content_6209ec920ae84($_smarty_tpl) {?><div class="row" style="padding: 10px 20px 0 20px;"><span class="span4"><?php if (count($_smarty_tpl->tpl_vars['LISTVIEW_LINKS']->value['LISTVIEWBASIC'])>0){?><?php  $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_LINKS']->value['LISTVIEWBASIC']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->key => $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->_loop = true;
?><span class="btn-group"><button id="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
_listView_basicAction_<?php echo Vtiger_Util_Helper::replaceSpaceWithUnderScores($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getLabel());?>
" class="btn addButton" <?php if (stripos($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getUrl(),'javascript:')===0){?> onclick='<?php echo substr($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getUrl(),strlen("javascript:"));?>
;'<?php }else{ ?> onclick='window.location.href="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getUrl();?>
"'<?php }?>><i class="icon-plus icon-white"></i>&nbsp;<strong><?php echo vtranslate($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getLabel(),$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong></button></span><?php } ?><?php }?><?php if ($_smarty_tpl->tpl_vars['ISDEFAULT']->value){?><?php if ($_GET['filter']!='changeHistory'){?>&nbsp;<button id="exportContract" class="btn btn-info"><strong>导出</strong></button>&nbsp;<?php if ($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->exportGrouprt('ServiceContracts','COLLATE')){?><button id="collateContract" class="btn btn-info"><strong>核对</strong></button>&nbsp;<?php }?><?php if ($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->exportGrouprt('ServiceContracts','closedContracts')){?><button id="closeContract" class="btn btn-info"><strong>关停合同</strong></button>&nbsp;<?php }?><span>&nbsp;<input onclick="Vtiger_List_Js.showUserFieldEdit('index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=FieldAjax');" class="btn diy"type="button" value="列表自定义"></span><?php }?><?php }?></span><?php if ($_smarty_tpl->tpl_vars['ISPAGE']->value){?><span class="span8" style="text-align: right">  <span>&nbsp;<select id="limit" name="limit" style="width: 80px;margin-top: -13px;"><option value="10">10</option><option value="20" selected="selected">20</option><option value="50">50</option><option value="100">100</option></select>&nbsp;</span><span class="pagination" style="" id="pagination"><ul class="pagination-demo"></ul></span><span>&nbsp;<input type="text" name="jumppage" value="" id="jumppage" class="input-small"style="width: 50px;margin-top: -13px;" placeholder="跳转">&nbsp;</span></span><?php }?></div>
<?php }} ?>