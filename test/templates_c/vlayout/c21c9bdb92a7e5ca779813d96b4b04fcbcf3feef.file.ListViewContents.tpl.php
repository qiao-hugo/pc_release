<?php /* Smarty version Smarty-3.1.7, created on 2022-02-15 11:48:44
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\Staypayment\ListViewContents.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5762620b229c2d17f7-04364092%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c21c9bdb92a7e5ca779813d96b4b04fcbcf3feef' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\Staypayment\\ListViewContents.tpl',
      1 => 1634179104,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5762620b229c2d17f7-04364092',
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
    'LISTVIEW_HEADERS' => 0,
    'LISTVIEW_HEADER' => 0,
    'KEY' => 0,
    'MODULE' => 0,
    'LISTVIEW_ENTRIES' => 0,
    'LISTVIEW_ENTRY' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620b229c31393',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620b229c31393')) {function content_620b229c31393($_smarty_tpl) {?>
<div id="pagehtml"><input type="hidden" id="view" value="<?php echo $_smarty_tpl->tpl_vars['VIEW']->value;?>
" /><input type="hidden" id="pageStartRange" value="" /><input type="hidden" id="pageEndRange" value="" /><input type="hidden" id="previousPageExist" value="" /><input type="hidden" id="nextPageExist" value="" /><input type="hidden" id="alphabetSearchKey" value= "" /><input type="hidden" id="Operator" value="<?php echo $_smarty_tpl->tpl_vars['OPERATOR']->value;?>
" /><input type="hidden" id="alphabetValue" value="<?php echo $_smarty_tpl->tpl_vars['ALPHABET_VALUE']->value;?>
" /><input type="hidden" id="totalCount" value="<?php echo $_smarty_tpl->tpl_vars['PAGE_COUNT']->value;?>
" /><input type='hidden' value="<?php echo $_smarty_tpl->tpl_vars['PAGE_NUMBER']->value;?>
" id='pageNumber'><input type='hidden' value="<?php echo $_smarty_tpl->tpl_vars['PAGING_MODEL']->value->getPageLimit();?>
" id='pageLimit'><input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTIRES_COUNT']->value;?>
" id="noOfEntries"><?php $_smarty_tpl->tpl_vars['ALPHABETS_LABEL'] = new Smarty_variable(vtranslate('LBL_ALPHABETS','Vtiger'), null, 0);?><?php $_smarty_tpl->tpl_vars['ALPHABETS'] = new Smarty_variable(explode(',',$_smarty_tpl->tpl_vars['ALPHABETS_LABEL']->value), null, 0);?><div class="listViewEntriesDiv contents-bottomscroll" style="overflow:auto;"><div class="bottomscroll-div" ><input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['ORDER_BY']->value;?>
" id="orderBy"><input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['SORT_ORDER']->value;?>
" id="sortOrder"><?php $_smarty_tpl->tpl_vars['WIDTHTYPE'] = new Smarty_variable($_smarty_tpl->tpl_vars['CURRENT_USER_MODEL']->value->get('rowheight'), null, 0);?><table class="table listViewEntriesTable"><thead><tr class="listViewHeaders"><?php  $_smarty_tpl->tpl_vars['LISTVIEW_HEADER'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->_loop = false;
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
</th><?php } ?><th nowrap style="width:90px">操作</th></tr></thead><?php  $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_ENTRIES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['listview']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->key => $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->_loop = true;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['listview']['index']++;
?><tr class="listViewEntries"  data-id='<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['id'];?>
' data-recordUrl='index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Detail&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['id'];?>
&realoperate=<?php echo setoperate($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['id'],'MODULE');?>
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
?><td class="listViewEntryValue"  nowrap><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']=='workflowsnode'){?><?php echo str_replace(',','<br>',$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value[$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']]);?>
<?php }elseif($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']=='subject'){?><a class="btn-link" href='index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Detail&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['id'];?>
' target="_block"><?php echo vtranslate($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value[$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']],$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a><?php }elseif($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']=='contractid'){?><a class="btn-link" href='index.php?module=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['modulename'];?>
&view=Detail&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['contractid_reference'];?>
' target="_block"><?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value[$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']];?>
</a><?php }elseif($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']=='accountid'){?><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['modulename']=='ServiceContracts'){?><a class="btn-link" href='index.php?module=Accounts&view=Detail&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['accountid_reference'];?>
' target="_block"><?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value[$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']];?>
</a><?php }else{ ?><a class="btn-link" href='index.php?module=Vendors&view=Detail&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['accountid_reference'];?>
' target="_block"><?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value[$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']];?>
</a><?php }?><?php }else{ ?><?php echo uitypeformat($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value,$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php }?></td><?php } ?><td class="listViewEntryValue" ><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->last){?><div  style="width:90px"><i title="详细信息" data-sql="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['sql'];?>
" class="icon-th-list alignMiddle"></i>&nbsp;<a class="deleteRecord"><i title="删除" class="icon-trash alignMiddle"></i></a></div><?php }?></td></tr><?php } ?></table></div></div></div>
<?php }} ?>