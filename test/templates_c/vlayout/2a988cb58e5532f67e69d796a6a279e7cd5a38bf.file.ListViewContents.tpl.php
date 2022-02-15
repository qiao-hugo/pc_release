<?php /* Smarty version Smarty-3.1.7, created on 2022-02-15 16:39:08
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\ReceivedPayments\ListViewContents.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5381620b66ac8aedc0-24992164%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2a988cb58e5532f67e69d796a6a279e7cd5a38bf' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\ReceivedPayments\\ListViewContents.tpl',
      1 => 1639724959,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5381620b66ac8aedc0-24992164',
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
    'MODULE_MODEL' => 0,
    'LISTVIEW_ENTRIES' => 0,
    'LISTVIEW_ENTRY' => 0,
    'IS_MODULE_EDITABLE' => 0,
    'IS_SPLIT' => 0,
    'ISREPEATRECEIVEDPAYMENTS' => 0,
    'IS_MODULE_DELETABLE' => 0,
    'isEditAllowinvoicetotal' => 0,
    'IS_COLLATE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620b66ac92ffb',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620b66ac92ffb')) {function content_620b66ac92ffb($_smarty_tpl) {?>
<style>.ellipsis {display:inline-block;width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}.collateloglist {font-size: 13px;margin-left: 20px;list-style:none;}.collateloglist li {position: relative;padding: 0 0 20px 20px;border-left: 1px solid #ccc;}.collateloglist li .serialnum {position: absolute;display: block;left: -10px;top: 0px;border: 2px #178fdd solid;color: #178fdd;background-color: #fff;font-size: 12px;width: 16px;height: 16px;text-align: center;line-height: 16px;vertical-align: middle;border-radius: 50%;}.collateloglist li .collatetime {display: inline-block;width: 150px;vertical-align: middle;}.collateloglist li .collator {display:inline-block;width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;vertical-align:middle}.collateloglist li .status {vertical-align:middle;margin-left:10px;}.collateloglist li .remark {word-wrap:break-word;}</style><div id="pagehtml"><input type="hidden" id="view" value="<?php echo $_smarty_tpl->tpl_vars['VIEW']->value;?>
" /><input type="hidden" id="pageStartRange" value="" /><input type="hidden" id="pageEndRange" value="" /><input type="hidden" id="previousPageExist" value="" /><input type="hidden" id="nextPageExist" value="" /><input type="hidden" id="alphabetSearchKey" value= "" /><input type="hidden" id="Operator" value="<?php echo $_smarty_tpl->tpl_vars['OPERATOR']->value;?>
" /><input type="hidden" id="alphabetValue" value="<?php echo $_smarty_tpl->tpl_vars['ALPHABET_VALUE']->value;?>
" /><input type="hidden" id="totalCount" value="<?php echo $_smarty_tpl->tpl_vars['PAGE_COUNT']->value;?>
" /><input type='hidden' value="<?php echo $_smarty_tpl->tpl_vars['PAGE_NUMBER']->value;?>
" id='pageNumber'><input type='hidden' value="<?php echo $_smarty_tpl->tpl_vars['PAGING_MODEL']->value->getPageLimit();?>
" id='pageLimit'><input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTIRES_COUNT']->value;?>
" id="noOfEntries"><?php $_smarty_tpl->tpl_vars['ALPHABETS_LABEL'] = new Smarty_variable(vtranslate('LBL_ALPHABETS','Vtiger'), null, 0);?><?php $_smarty_tpl->tpl_vars['ALPHABETS'] = new Smarty_variable(explode(',',$_smarty_tpl->tpl_vars['ALPHABETS_LABEL']->value), null, 0);?><div class="listViewEntriesDiv contents-bottomscroll" style="overflow:auto;"><div class="bottomscroll-div" ><input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['ORDER_BY']->value;?>
" id="orderBy"><input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['SORT_ORDER']->value;?>
" id="sortOrder"><?php $_smarty_tpl->tpl_vars['WIDTHTYPE'] = new Smarty_variable($_smarty_tpl->tpl_vars['CURRENT_USER_MODEL']->value->get('rowheight'), null, 0);?><table class="table listViewEntriesTable"><thead><tr class="listViewHeaders"><td><input type="checkbox"  name="checkAll" ></label></td><?php  $_smarty_tpl->tpl_vars['LISTVIEW_HEADER'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->_loop = false;
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
</th><?php } ?><th nowrap style="width:90px">操作</th></tr></thead><?php $_smarty_tpl->tpl_vars['IS_COLLATE'] = new Smarty_variable($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->exportGrouprt('ReceivedPayments','COLLATE'), null, 0);?><?php  $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_ENTRIES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['listview']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->key => $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->_loop = true;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['listview']['index']++;
?><tr class="listViewEntries"  data-id='<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['receivedpaymentsid'];?>
' data-recordUrl='index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Detail&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['receivedpaymentsid'];?>
' id="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
_listView_row_<?php echo $_smarty_tpl->getVariable('smarty')->value['foreach']['listview']['index']+1;?>
"><td style="display: inline-block;"><input type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['id'];?>
"  data-amount="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['unit_price'];?>
"  data-oldamount="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['standardmoney'];?>
" class="entryCheckBox" name="Detailrecord[]"></label></td><?php  $_smarty_tpl->tpl_vars['LISTVIEW_HEADER'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->_loop = false;
 $_smarty_tpl->tpl_vars['fkey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_HEADERS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->key => $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->_loop = true;
 $_smarty_tpl->tpl_vars['fkey']->value = $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->key;
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->iteration++;
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->last = $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->iteration === $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->total;
?><td class="listViewEntryValue <?php if ($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']=='allowinvoicetotal'){?>allowinvoicetotal_value<?php }?> <?php if ($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']=='ismatchdepart'){?>ismatchdepart_value<?php }?> <?php if ($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']=='relatetoid'){?>relatetoid_value<?php }?>"  nowrap><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']=='relatetoid'){?><a class="btn-link" href='index.php?module=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['modulename'];?>
&view=Detail&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['relatetoid_reference'];?>
' target="_block"><?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value[$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']];?>
</a><?php }elseif($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']=='collate_num'){?><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['collate_num']>=1){?><span title="点击展开核对记录" class="collatelog"><?php echo uitypeformat($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value,$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
<i class="icon-list-alt"></i></span><?php }else{ ?><?php echo uitypeformat($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value,$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php }?><?php }elseif(in_array($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname'],array('first_collate_status','last_collate_status'))){?><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value[$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']]=='fit'){?>符合<?php }elseif($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value[$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']]=='unfit'){?>不符合<?php }?><?php }elseif(in_array($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname'],array('first_collate_remark','last_collate_remark'))){?><span class="ellipsis" title="<?php echo uitypeformat($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value,$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
"><?php echo uitypeformat($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value,$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
</span><?php }elseif(in_array($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname'],array('first_collate_time','last_collate_time'))){?><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value[$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']]!='0000-00-00 00:00:00'){?><?php echo uitypeformat($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value,$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php }?><?php }else{ ?><?php echo uitypeformat($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value,$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php }?></td><?php } ?><td class="listViewEntryValue"><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->last){?><div  style="width:120px"><a href="index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Detail&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['receivedpaymentsid'];?>
"><i title="详细信息" class="icon-th-list alignMiddle"></i></a>&nbsp;<?php if ($_smarty_tpl->tpl_vars['IS_MODULE_EDITABLE']->value&&$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['receivedstatus']!='virtualrefund'){?><a  href='index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Edit&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['receivedpaymentsid'];?>
'  target="_block"><i title="<?php echo vtranslate('LBL_EDIT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" class="icon-pencil alignMiddle"></i></a>&nbsp;<?php }?><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['setReceiveStatus']==1&&$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['receivedstatus']!='virtualrefund'){?><a data-status="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['receivedstatus'];?>
" class="setReceiveStatus" target="_block"><i title="修改回款类型" class="icon-share alignMiddle"></i></a>&nbsp;<?php }?><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['NonPayCertificate']==1&&empty($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['relatetoid'])&&$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['receivedstatus']=='normal'){?><a data-status="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['receivedstatus'];?>
" class="NonPayCertificate" target="_block"><i title="设未提供代付款证明" class="icon-adjust alignMiddle"></i></a>&nbsp;<?php }?><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['dochargebacks']==1){?><a data-status="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['receivedstatus'];?>
"  data-chargebacksa="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['rechargeableamount'];?>
" class="chargebacks" target="_block"><i title="扣款" class="icon-tint alignMiddle"></i></a>&nbsp;<?php }?><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['dorechargeable']==1){?><a data-chargebacks="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['chargebacks'];?>
"  data-unit_price="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['unit_price'];?>
" class="dorechargeableamount" target="_block"><i title="可使用金额" class="icon-magnet alignMiddle"></i></a>&nbsp;<?php }?><?php if ($_smarty_tpl->tpl_vars['IS_SPLIT']->value==1){?><a data-status="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['receivedstatus'];?>
" class="splitReceive" target="_block"><i title="回款拆分" class="icon-move alignMiddle"></i></a>&nbsp;<?php }?><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['dobackcash'];?>
<?php $_tmp1=ob_get_clean();?><?php if ($_tmp1==1){?><a data-status="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['receivedstatus'];?>
" class="dobackcash" target="_block"><i title="设为返点款" class="icon-plane alignMiddle"></i></a>&nbsp;<?php }?><?php if ($_smarty_tpl->tpl_vars['ISREPEATRECEIVEDPAYMENTS']->value==1&&$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['receivedstatus']!='virtualrefund'){?><a data-status="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['receivedstatus'];?>
" class="repeatReceive" target="_block"><i title="重新匹配" class="icon-repeat alignMiddle"></i></a>&nbsp;<?php }?><?php if ($_smarty_tpl->tpl_vars['IS_MODULE_DELETABLE']->value&&$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['receivedstatus']=='normal'){?><a data-status="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['receivedstatus'];?>
" class="deleteRecord"><i title="删除" class="icon-trash alignMiddle"></i></a>&nbsp;<?php }?><?php if ($_smarty_tpl->tpl_vars['isEditAllowinvoicetotal']->value&&$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['receivedstatus']!='virtualrefund'){?><a data-status="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['allowinvoicetotal'];?>
" class="isEditAllowinvoicetotal" target="_block"><i title="修改可开票金额" class="alignMiddle glyphicon icon-cog"></i></a><?php }?><?php if ($_smarty_tpl->tpl_vars['IS_COLLATE']->value){?><a href="#" class="collate"><i title="核对" class="icon-check alignMiddle"></i></a><?php }?></div><?php }?></td></tr><?php } ?></table></div></div></div>
<?php }} ?>