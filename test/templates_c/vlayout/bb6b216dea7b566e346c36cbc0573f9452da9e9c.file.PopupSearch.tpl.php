<?php /* Smarty version Smarty-3.1.7, created on 2022-02-15 11:41:40
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\Vtiger\PopupSearch.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15321620b20f4893e80-06855742%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bb6b216dea7b566e346c36cbc0573f9452da9e9c' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\Vtiger\\PopupSearch.tpl',
      1 => 1523874472,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15321620b20f4893e80-06855742',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'SOURCE_MODULE' => 0,
    'MODULE' => 0,
    'PARENT_MODULE' => 0,
    'SOURCE_RECORD' => 0,
    'SOURCE_FIELD' => 0,
    'GETURL' => 0,
    'MULTI_SELECT' => 0,
    'CURRENCY_ID' => 0,
    'RELATED_PARENT_MODULE' => 0,
    'RELATED_PARENT_ID' => 0,
    'VIEW' => 0,
    'RECORD_STRUCTURE' => 0,
    'fields' => 0,
    'fieldName' => 0,
    'LISTVIEW_HEADERS' => 0,
    'fieldObject' => 0,
    'gotosearch' => 0,
    'LISTVIEW_ENTRIES' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620b20f48d1b7',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620b20f48d1b7')) {function content_620b20f48d1b7($_smarty_tpl) {?>
<input type="hidden" id="parentModule" value="<?php echo $_smarty_tpl->tpl_vars['SOURCE_MODULE']->value;?>
"/><input type="hidden" id="module" value="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
"/><input type="hidden" id="parent" value="<?php echo $_smarty_tpl->tpl_vars['PARENT_MODULE']->value;?>
"/><input type="hidden" id="sourceRecord" value="<?php echo $_smarty_tpl->tpl_vars['SOURCE_RECORD']->value;?>
"/><input type="hidden" id="sourceField" value="<?php echo $_smarty_tpl->tpl_vars['SOURCE_FIELD']->value;?>
"/><input type="hidden" id="url" value="<?php echo $_smarty_tpl->tpl_vars['GETURL']->value;?>
" /><input type="hidden" id="multi_select" value="<?php echo $_smarty_tpl->tpl_vars['MULTI_SELECT']->value;?>
" /><input type="hidden" id="currencyId" value="<?php echo $_smarty_tpl->tpl_vars['CURRENCY_ID']->value;?>
" /><input type="hidden" id="relatedParentModule" value="<?php echo $_smarty_tpl->tpl_vars['RELATED_PARENT_MODULE']->value;?>
"/><input type="hidden" id="relatedParentId" value="<?php echo $_smarty_tpl->tpl_vars['RELATED_PARENT_ID']->value;?>
"/><input type="hidden" id="view" value="<?php echo $_smarty_tpl->tpl_vars['VIEW']->value;?>
"/><div class="popupContainer row-fluid"><div class="span12"><div class="row-fluid"><div class="span6 row-fluid"><span class="logo span5"><img src="test/logo/vtiger-crm-logo.png" /></span></div></div></div></div><form class="form-horizontal popupSearchContainer"><div class="control-group margin0px"><span class="paddingLeft10px"><strong><?php echo vtranslate('LBL_SEARCH_FOR');?>
</strong></span><span class="paddingLeft10px"></span><input type="text" placeholder="<?php echo vtranslate('LBL_TYPE_SEARCH');?>
" id="searchvalue"/><span class="paddingLeft10px"><strong><?php echo vtranslate('LBL_IN');?>
</strong></span><span class="paddingLeft10px help-inline pushDownHalfper"><select style="width: 150px;" class="chzn-select help-inline" id="searchableColumnsList"><?php if ($_smarty_tpl->tpl_vars['MODULE']->value=='Users'){?><option value="last_name">姓  名</option><?php }else{ ?><?php $_smarty_tpl->tpl_vars["gotosearch"] = new Smarty_variable(0, null, 0);?><?php  $_smarty_tpl->tpl_vars['fields'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['fields']->_loop = false;
 $_smarty_tpl->tpl_vars['block'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['RECORD_STRUCTURE']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['fields']->key => $_smarty_tpl->tpl_vars['fields']->value){
$_smarty_tpl->tpl_vars['fields']->_loop = true;
 $_smarty_tpl->tpl_vars['block']->value = $_smarty_tpl->tpl_vars['fields']->key;
?><?php  $_smarty_tpl->tpl_vars['fieldObject'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['fieldObject']->_loop = false;
 $_smarty_tpl->tpl_vars['fieldName'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['fields']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['fieldObject']->key => $_smarty_tpl->tpl_vars['fieldObject']->value){
$_smarty_tpl->tpl_vars['fieldObject']->_loop = true;
 $_smarty_tpl->tpl_vars['fieldName']->value = $_smarty_tpl->tpl_vars['fieldObject']->key;
?><?php if (in_array(strtolower($_smarty_tpl->tpl_vars['fieldName']->value),$_smarty_tpl->tpl_vars['LISTVIEW_HEADERS']->value)){?><option value="<?php echo $_smarty_tpl->tpl_vars['fieldName']->value;?>
"><?php echo vtranslate($_smarty_tpl->tpl_vars['fieldObject']->value->get('label'),$_smarty_tpl->tpl_vars['MODULE']->value);?>
</option><?php $_smarty_tpl->tpl_vars["gotosearch"] = new Smarty_variable(1, null, 0);?><?php break 1?><?php }?><?php } ?><?php if ($_smarty_tpl->tpl_vars['gotosearch']->value==1){?><?php break 1?><?php }?><?php } ?><?php }?></select></span><span class="paddingLeft10px cursorPointer help-inline" id="popupSearchButton"><img src="<?php echo vimage_path('search.png');?>
" alt="<?php echo vtranslate('LBL_SEARCH_BUTTON');?>
" title="<?php echo vtranslate('LBL_SEARCH_BUTTON');?>
" /></span></div></form><?php if ($_smarty_tpl->tpl_vars['SOURCE_MODULE']->value!='PriceBooks'){?><div class="popupPaging"><div class="row-fluid"><?php if ($_smarty_tpl->tpl_vars['MULTI_SELECT']->value){?><?php if (!empty($_smarty_tpl->tpl_vars['LISTVIEW_ENTRIES']->value)){?><span class="actions span6">&nbsp;<button class="select btn"><strong><?php echo vtranslate('LBL_SELECT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong></button> </span><?php }?><?php }?><span class="span12"><span class="pull-right">&nbsp;<input type="text" name="jumppage" value="" id="jumppage" class="input-small" style="width: 50px;" placeholder="跳转">&nbsp;</span><span class="pagination pull-right" id="pagination"><ul class="pagination-demo"></ul></span></span></div></div><?php }?>
<?php }} ?>