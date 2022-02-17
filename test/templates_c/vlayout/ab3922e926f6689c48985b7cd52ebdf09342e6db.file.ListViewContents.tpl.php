<?php /* Smarty version Smarty-3.1.7, created on 2022-02-16 10:36:44
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\WorkFlowCheck\ListViewContents.tpl" */ ?>
<?php /*%%SmartyHeaderCode:4796620c633cbdfe86-38148313%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ab3922e926f6689c48985b7cd52ebdf09342e6db' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\WorkFlowCheck\\ListViewContents.tpl',
      1 => 1639651351,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4796620c633cbdfe86-38148313',
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
  'unifunc' => 'content_620c633cc3333',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620c633cc3333')) {function content_620c633cc3333($_smarty_tpl) {?>
<div id="pagehtml"><input type="hidden" id="view" value="<?php echo $_smarty_tpl->tpl_vars['VIEW']->value;?>
" /><input type="hidden" id="pageStartRange" value="" /><input type="hidden" id="pageEndRange" value="" /><input type="hidden" id="previousPageExist" value="" /><input type="hidden" id="nextPageExist" value="" /><input type="hidden" id="alphabetSearchKey" value= "" /><input type="hidden" id="Operator" value="<?php echo $_smarty_tpl->tpl_vars['OPERATOR']->value;?>
" /><input type="hidden" id="alphabetValue" value="<?php echo $_smarty_tpl->tpl_vars['ALPHABET_VALUE']->value;?>
" /><input type="hidden" id="totalCount" value="<?php echo $_smarty_tpl->tpl_vars['PAGE_COUNT']->value;?>
" /><input type='hidden' value="<?php echo $_smarty_tpl->tpl_vars['PAGE_NUMBER']->value;?>
" id='pageNumber'><input type='hidden' value="<?php echo $_smarty_tpl->tpl_vars['PAGING_MODEL']->value->getPageLimit();?>
" id='pageLimit'><input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTIRES_COUNT']->value;?>
" id="noOfEntries"><?php $_smarty_tpl->tpl_vars['ALPHABETS_LABEL'] = new Smarty_variable(vtranslate('LBL_ALPHABETS','Vtiger'), null, 0);?><?php $_smarty_tpl->tpl_vars['ALPHABETS'] = new Smarty_variable(explode(',',$_smarty_tpl->tpl_vars['ALPHABETS_LABEL']->value), null, 0);?><div class="listViewEntriesDiv contents-bottomscroll" style="overflow:auto;"><div class="bottomscroll-div" ><input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['ORDER_BY']->value;?>
" id="orderBy"><input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['SORT_ORDER']->value;?>
" id="sortOrder"><?php $_smarty_tpl->tpl_vars['WIDTHTYPE'] = new Smarty_variable($_smarty_tpl->tpl_vars['CURRENT_USER_MODEL']->value->get('rowheight'), null, 0);?><table class="table listViewEntriesTable"><thead><tr class="listViewHeaders"><th nowrap style="width:90px">操作</th><?php  $_smarty_tpl->tpl_vars['LISTVIEW_HEADER'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->_loop = false;
 $_smarty_tpl->tpl_vars['KEY'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_HEADERS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->key => $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->_loop = true;
 $_smarty_tpl->tpl_vars['KEY']->value = $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->key;
?><th nowrap data-field="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname'];?>
"><?php echo vtranslate($_smarty_tpl->tpl_vars['KEY']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
</th><?php } ?></tr></thead><?php  $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_ENTRIES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['listview']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->key => $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->_loop = true;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['listview']['index']++;
?><?php  $_smarty_tpl->tpl_vars['LISTVIEW_HEADER'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->_loop = false;
 $_smarty_tpl->tpl_vars['fkey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_HEADERS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['fieldview']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->key => $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->_loop = true;
 $_smarty_tpl->tpl_vars['fkey']->value = $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->key;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['fieldview']['index']++;
?><?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['fieldview']['index']==0){?><tr class="listViewEntries"  data-id='<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['newsalesorderid'];?>
' data-recordUrl='index.php?module=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['modulename'];?>
&view=Detail&record=<?php if ($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['modulename']=='AchievementallotStatistic'){?><?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['originalmoduleid'];?>
&mode=showDetailViewByMode<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['newsalesorderid'];?>
<?php }?>&realoperate=<?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['modulename'];?>
<?php $_tmp1=ob_get_clean();?><?php echo setoperate($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['newsalesorderid'],$_tmp1);?>
' id="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
_listView_row_<?php echo $_smarty_tpl->getVariable('smarty')->value['foreach']['listview']['index']+1;?>
"><td class="listViewEntryValue" ><div  style="width:90px"><a target="_blank"  href="index.php?module=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['modulename'];?>
&view=Detail&record=<?php if ($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['modulename']=='AchievementallotStatistic'){?><?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['originalmoduleid'];?>
&mode=showDetailViewByMode<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['newsalesorderid'];?>
<?php }?>&realoperate=<?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['modulename'];?>
<?php $_tmp2=ob_get_clean();?><?php echo setoperate($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['newsalesorderid'],$_tmp2);?>
"><i title="详细信息" class="icon-th-list alignMiddle"></i></a>&nbsp;</div></td><?php }?><td class="listViewEntryValue"  nowrap><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']=='accountname'){?><a class="btn-link" ><?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['accountname'];?>
</a><?php }elseif($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']=='salesorder_nono'){?><a class="btn-link" href="index.php?module=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['modulename'];?>
&view=Detail&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['newsalesorderid'];?>
&realoperate=<?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['modulename'];?>
<?php $_tmp3=ob_get_clean();?><?php echo setoperate($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['newsalesorderid'],$_tmp3);?>
"  target="_block"><?php echo vtranslate($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value[$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']],'Vtiger');?>
</a><?php }elseif($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']=='modulestatus'){?><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['modulestatus']=='c_complete'){?><span class="label label-c_complete"><?php echo vtranslate($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['modulestatus'],'Vtiger');?>
</span><?php }else{ ?><span class="label label-b_actioning"><?php echo vtranslate($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['modulestatus'],'Vtiger');?>
</span><?php }?><?php }else{ ?><?php echo uitypeformat($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value,$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php }?></td><?php } ?></tr><?php } ?></table></div></div></div>
<?php }} ?>