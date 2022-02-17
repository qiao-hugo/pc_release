<?php /* Smarty version Smarty-3.1.7, created on 2022-02-16 13:55:56
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\Accounts\ListViewContents.tpl" */ ?>
<?php /*%%SmartyHeaderCode:165196209ee5f81a081-44397268%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1c8b3b0ba2fa89037eaa22e13d7af71413ff8676' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\Accounts\\ListViewContents.tpl',
      1 => 1644888097,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '165196209ee5f81a081-44397268',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_6209ee5f87e4e',
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
    'MODULE_MODEL' => 0,
    'ORDER_BY' => 0,
    'SORT_ORDER' => 0,
    'CURRENT_USER_MODEL' => 0,
    'LISTVIEW_HEADERS' => 0,
    'LISTVIEW_HEADER' => 0,
    'KEY' => 0,
    'MODULE' => 0,
    'LISTVIEW_ENTRIES' => 0,
    'LISTVIEW_ENTRY' => 0,
    'IS_ADVANCEMONY' => 0,
    'IS_MODULE_EDITABLE' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_6209ee5f87e4e')) {function content_6209ee5f87e4e($_smarty_tpl) {?>
<div id="pagehtml"><input type="hidden" id="view" value="<?php echo $_smarty_tpl->tpl_vars['VIEW']->value;?>
" /><input type="hidden" id="pageStartRange" value="" /><input type="hidden" id="pageEndRange" value="" /><input type="hidden" id="previousPageExist" value="" /><input type="hidden" id="nextPageExist" value="" /><input type="hidden" id="alphabetSearchKey" value= "" /><input type="hidden" id="Operator" value="<?php echo $_smarty_tpl->tpl_vars['OPERATOR']->value;?>
" /><input type="hidden" id="alphabetValue" value="<?php echo $_smarty_tpl->tpl_vars['ALPHABET_VALUE']->value;?>
" /><input type="hidden" id="totalCount" value="<?php echo $_smarty_tpl->tpl_vars['PAGE_COUNT']->value;?>
" /><input type='hidden' value="<?php echo $_smarty_tpl->tpl_vars['PAGE_NUMBER']->value;?>
" id='pageNumber'><input type='hidden' value="<?php echo $_smarty_tpl->tpl_vars['PAGING_MODEL']->value->getPageLimit();?>
" id='pageLimit'><input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTIRES_COUNT']->value;?>
" id="noOfEntries"><?php $_smarty_tpl->tpl_vars['ALPHABETS_LABEL'] = new Smarty_variable(vtranslate('LBL_ALPHABETS','Vtiger'), null, 0);?><?php $_smarty_tpl->tpl_vars['ALPHABETS'] = new Smarty_variable(explode(',',$_smarty_tpl->tpl_vars['ALPHABETS_LABEL']->value), null, 0);?><?php $_smarty_tpl->tpl_vars['IS_PROTECTED'] = new Smarty_variable($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->isprotected(), null, 0);?><div class="listViewEntriesDiv contents-bottomscroll" style="overflow:auto;"><div class="bottomscroll-div" ><input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['ORDER_BY']->value;?>
" id="orderBy"><input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['SORT_ORDER']->value;?>
" id="sortOrder"><?php $_smarty_tpl->tpl_vars['WIDTHTYPE'] = new Smarty_variable($_smarty_tpl->tpl_vars['CURRENT_USER_MODEL']->value->get('rowheight'), null, 0);?><table class="table listViewEntriesTable" id="listViewContentTable" ><thead><tr class="listViewHeaders"><?php if ($_GET['filter']=='overt'){?><th nowrap><div  class="noclick" style="width: 100%;height:100%;"><button type="button" class="btn btn-success checkedall">全选</button><button type="button" class="btn btn-inverse checkedinverse">反选</button><button type="button" class="btn btn-primary stampall">领用临时区</button></div></th><?php }?><?php if ($_GET['filter']=='temporary'){?><th nowrap><div  class="noclick" style="width: 100%;height:100%;"><button type="button" class="btn btn-success over_checkedall">全选</button><button type="button" class="btn btn-inverse over_checkedinverse">反选</button><button type="button" class="btn btn-primary over_stampall">放入公海</button></div></th><?php }?><?php  $_smarty_tpl->tpl_vars['LISTVIEW_HEADER'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->_loop = false;
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
" class="listViewEntries"><img src="layouts/vlayout/skins/images/sort_all.png"><?php echo vtranslate($_smarty_tpl->tpl_vars['KEY']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
</th><?php } ?><th id="fixTh">操作</th></tr></thead><?php  $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_ENTRIES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['listview']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->key => $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->_loop = true;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['listview']['index']++;
?><tr class="listViewEntries"  data-id='<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['id'];?>
' data-name="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['accountname'];?>
" data-recordUrl='index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Detail&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['id'];?>
' id="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
_listView_row_<?php echo $_smarty_tpl->getVariable('smarty')->value['foreach']['listview']['index']+1;?>
"><?php if ($_GET['filter']=='overt'){?><td style="text-align: left;"><div class="deletedflag"><label style="height:100%;display: inline-block;"><input type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['id'];?>
" class="entryCheckBox" name="Detailrecord[]" ></label><button type="button" data-id="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['id'];?>
" style="visibility:hidden;" class="btn btn stamp">领用</button><button type="button" data-id="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['id'];?>
" style="display:inline" class="btn btn stamp">领用临时区</button></div></td><?php }?><?php if ($_GET['filter']=='temporary'){?><td style="text-align: left;"><div class="deletedflag"><label style="height:100%;display: inline-block;"><input type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['id'];?>
" class="entryCheckBoxOver" name="DetailrecordOver[]" ></label><button type="button" data-id="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['id'];?>
" style="visibility:hidden;" class="btn btn stamp"></button><button type="button" data-id="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['id'];?>
" style="display:inline" class="btn btn stamp_overt">放入公海</button></div></td><?php }?><?php  $_smarty_tpl->tpl_vars['LISTVIEW_HEADER'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->_loop = false;
 $_smarty_tpl->tpl_vars['fkey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_HEADERS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->key => $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->_loop = true;
 $_smarty_tpl->tpl_vars['fkey']->value = $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->key;
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->iteration++;
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->last = $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->iteration === $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->total;
?><td class="listViewEntryValue  <?php if ($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']=='advancesmoney'){?>advancesmoney_value<?php }?> "  nowrap><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']=='accountname'){?><a class="btn-link" href='index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Detail&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['id'];?>
' target="_block"><?php echo vtranslate($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value[$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']],$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a><?php }else{ ?><?php echo uitypeformat($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['uitype'],$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value[$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']],$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php }?></td><?php } ?><td class="listViewEntryValue" ><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->last){?><div  style="width:120px"><a  href="index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Detail&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['id'];?>
"><i title="详细信息" class="icon-th-list alignMiddle"></i></a>&nbsp;<?php if ($_smarty_tpl->tpl_vars['IS_ADVANCEMONY']->value){?><a class="setAdvancesmoney" data-status="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['advancesmoney'];?>
"> <i title="<?php echo vtranslate('修改垫款',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" class="icon-move alignMiddle"></i></a><?php }?><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['protected']=='否'){?><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['accountcategory']==0){?><a class="deleteRecordButton"><i title="删除" class="icon-trash alignMiddle"></i></a><a  href='index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Edit&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['id'];?>
'  target="_block"><i title="<?php echo vtranslate('LBL_EDIT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" class="icon-pencil alignMiddle"></i></a>&nbsp;<?php if ($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['isown']==1){?><a class="ChangeRecordButton" id="PROTECTED"> <i title="<?php echo vtranslate('LBL_TOPROTECT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" class="icon-share alignMiddle"></i></a><?php }?><a class="ChangeRecordButton" id="OVERT"> <i title="<?php echo vtranslate('LBL_TOOVERT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" class="icon-shopping-cart alignMiddle"></i></a><?php }elseif($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['accountcategory']==1){?><?php if ($_smarty_tpl->tpl_vars['IS_MODULE_EDITABLE']->value){?><a  href='index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Edit&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['id'];?>
'  target="_block"><i title="<?php echo vtranslate('LBL_EDIT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" class="icon-pencil alignMiddle"></i></a>&nbsp;<?php }?><a class="ChangeRecordButton" id="SELF"> <i title="<?php echo vtranslate('LBL_TOSELF',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" class="icon-inbox alignMiddle"></i></a><?php }elseif($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['accountcategory']==2){?><a class="ChangeRecordButton" id="TEMPORARY"> <i title="<?php echo vtranslate('LBL_TOTEMPORARY',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" class="icon-flag alignMiddle"></i></a><a class="ChangeRecordButton" id="SELF"> <i title="<?php echo vtranslate('LBL_TOSELF',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" class="icon-inbox alignMiddle"></i></a><?php }?><?php }elseif($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['isown']==1){?><a class="ChangeRecordButton" id="UNPROTECTED"> <i title="<?php echo vtranslate('LBL_PROTECTED',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" class="icon-lock alignMiddle"></i></a><?php }?></div><?php }?></td></tr><?php } ?></table></div></div></div><?php if ($_GET['filter']=='overt'){?><script type="text/javascript">$(function(){$('.entryCheckBox').iCheck({checkboxClass: 'icheckbox_minimal-orange'});});</script><?php }?><?php if ($_GET['filter']=='temporary'){?><script type="text/javascript">$(function(){$('.entryCheckBoxOver').iCheck({checkboxClass: 'icheckbox_minimal-orange'});});</script><?php }?>
<?php }} ?>