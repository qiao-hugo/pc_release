<?php /* Smarty version Smarty-3.1.7, created on 2022-02-15 16:37:34
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\Leads\ListViewContents.tpl" */ ?>
<?php /*%%SmartyHeaderCode:25349620b664e1a89c2-97809393%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f4423a4bb0ff510d840d5ad1ab5091b41e13928a' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\Leads\\ListViewContents.tpl',
      1 => 1638951441,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '25349620b664e1a89c2-97809393',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'VIEW' => 0,
    'OPERATOR' => 0,
    'ALPHABET_VALUE' => 0,
    'PAGE_COUNT' => 0,
    'PAGE_NUMBER' => 0,
    'PAGING_MODEL' => 0,
    'LISTVIEW_ENTIRES_COUNT' => 0,
    'ALPHABETS_LABEL' => 0,
    'ORDER_BY' => 0,
    'SORT_ORDER' => 0,
    'CURRENT_USER_MODEL' => 0,
    'LISTVIEW_FIELDS' => 0,
    'KEY' => 0,
    'LISTVIEW_HEADERS' => 0,
    'MODULE' => 0,
    'LISTVIEW_HEADER' => 0,
    'LISTVIEW_ENTRIES' => 0,
    'LISTVIEW_ENTRY' => 0,
    'IS_MODULE_EDITABLE' => 0,
    'IS_MODULE_DELETABLE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620b664e1f34c',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620b664e1f34c')) {function content_620b664e1f34c($_smarty_tpl) {?>
<div id="pagehtml"><input type="hidden" id="view" value="<?php echo $_smarty_tpl->tpl_vars['VIEW']->value;?>
" /><input type="hidden" id="pageStartRange" value="" /><input type="hidden" id="pageEndRange" value="" /><input type="hidden" id="previousPageExist" value="" /><input type="hidden" id="nextPageExist" value="" /><input type="hidden" id="alphabetSearchKey" value= "" /><input type="hidden" id="Operator" value="<?php echo $_smarty_tpl->tpl_vars['OPERATOR']->value;?>
" /><input type="hidden" id="alphabetValue" value="<?php echo $_smarty_tpl->tpl_vars['ALPHABET_VALUE']->value;?>
" /><input type="hidden" id="totalCount" value="<?php echo $_smarty_tpl->tpl_vars['PAGE_COUNT']->value;?>
" /><input type='hidden' value="<?php echo $_smarty_tpl->tpl_vars['PAGE_NUMBER']->value;?>
" id='pageNumber'><input type='hidden' value="<?php echo $_smarty_tpl->tpl_vars['PAGING_MODEL']->value->getPageLimit();?>
" id='pageLimit'><input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTIRES_COUNT']->value;?>
" id="noOfEntries"><?php $_smarty_tpl->tpl_vars['ALPHABETS_LABEL'] = new Smarty_variable(vtranslate('LBL_ALPHABETS','Vtiger'), null, 0);?><?php $_smarty_tpl->tpl_vars['ALPHABETS'] = new Smarty_variable(explode(',',$_smarty_tpl->tpl_vars['ALPHABETS_LABEL']->value), null, 0);?><div class="listViewEntriesDiv contents-bottomscroll" style="overflow:auto;"><div class="bottomscroll-div" ><input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['ORDER_BY']->value;?>
" id="orderBy"><input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['SORT_ORDER']->value;?>
" id="sortOrder"><?php $_smarty_tpl->tpl_vars['WIDTHTYPE'] = new Smarty_variable($_smarty_tpl->tpl_vars['CURRENT_USER_MODEL']->value->get('rowheight'), null, 0);?><table class="table listViewEntriesTable"><thead><tr class="listViewHeaders"><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_FIELDS']->value){?><?php  $_smarty_tpl->tpl_vars['LISTVIEW_HEADER'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->_loop = false;
 $_smarty_tpl->tpl_vars['KEY'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_FIELDS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->key => $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->_loop = true;
 $_smarty_tpl->tpl_vars['KEY']->value = $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->key;
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->iteration++;
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->last = $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->iteration === $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->total;
?><th nowrap data-field="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_HEADERS']->value[$_smarty_tpl->tpl_vars['KEY']->value]['columnname'];?>
"><?php echo vtranslate($_smarty_tpl->tpl_vars['KEY']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
</th><?php } ?><?php }else{ ?><?php  $_smarty_tpl->tpl_vars['LISTVIEW_HEADER'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->_loop = false;
 $_smarty_tpl->tpl_vars['KEY'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_HEADERS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->key => $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->_loop = true;
 $_smarty_tpl->tpl_vars['KEY']->value = $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->key;
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->iteration++;
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->last = $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->iteration === $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->total;
?><th nowrap data-field="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname'];?>
"><?php echo vtranslate($_smarty_tpl->tpl_vars['KEY']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
</th><?php } ?><?php }?><th nowrap style="width:90px">??????</th></tr></thead><?php  $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_ENTRIES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['listview']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->key => $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->_loop = true;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['listview']['index']++;
?><tr class="listViewEntries"  data-id='<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['id'];?>
' data-recordUrl='index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Detail&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['id'];?>
' id="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
_listView_row_<?php echo $_smarty_tpl->getVariable('smarty')->value['foreach']['listview']['index']+1;?>
"><?php  $_smarty_tpl->tpl_vars['LISTVIEW_HEADER'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->_loop = false;
 $_smarty_tpl->tpl_vars['fkey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_HEADERS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->key => $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->_loop = true;
 $_smarty_tpl->tpl_vars['fkey']->value = $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->key;
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->iteration++;
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->last = $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->iteration === $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->total;
?><td class="listViewEntryValue"  nowrap><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']=='subject'){?><a class="btn-link" href='index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Detail&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['id'];?>
' target="_block"><?php echo vtranslate($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value[$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']],$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a><?php }else{ ?><?php echo uitypeformat($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value,$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php }?></td><?php } ?><td class="listViewEntryValue" ><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->last){?><div  style="width:90px"><a  href="index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Detail&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['id'];?>
"><i title="????????????" class="icon-th-list alignMiddle"></i></a>&nbsp;<?php if ($_smarty_tpl->tpl_vars['IS_MODULE_EDITABLE']->value){?><a  href='index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Edit&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['id'];?>
'  target="_block"><i title="<?php echo vtranslate('LBL_EDIT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" class="icon-pencil alignMiddle"></i></a>&nbsp;<?php }?><?php if ($_smarty_tpl->tpl_vars['IS_MODULE_DELETABLE']->value){?><?php }?><?php if (($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['leadcategroy']==0)&&!in_array($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['assignerstatus'],array('c_transformation','c_complete','c_Related','c_Forced_Related'))){?><a class="ChangeRecordButton" id="OVERT"> <i title="<?php echo vtranslate('LBL_TOOVERT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" class="icon-shopping-cart alignMiddle"></i></a><?php }elseif($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['leadcategroy']==2){?><a class="ChangeRecordButton" id="SELF"> <i title="<?php echo vtranslate('LBL_TOSELF',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" class="icon-inbox alignMiddle"></i></a><?php }?></div><?php }?></td></tr><?php } ?></table></div></div></div>
<?php }} ?>