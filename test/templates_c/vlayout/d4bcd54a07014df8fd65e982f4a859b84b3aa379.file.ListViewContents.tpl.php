<?php /* Smarty version Smarty-3.1.7, created on 2022-02-14 13:45:54
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\ServiceContracts\ListViewContents.tpl" */ ?>
<?php /*%%SmartyHeaderCode:309656209ec9211b185-26202571%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd4bcd54a07014df8fd65e982f4a859b84b3aa379' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\ServiceContracts\\ListViewContents.tpl',
      1 => 1638436487,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '309656209ec9211b185-26202571',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'VIEW' => 0,
    'OPERATOR' => 0,
    'ALPHABET_VALUE' => 0,
    'PAGE_COUNT' => 0,
    'public' => 0,
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
    'ISUPDATECONTRACTSCLOSE' => 0,
    'ISUPDATECONTRACTSSTATES' => 0,
    'IS_MODULE_EDITABLE' => 0,
    'IS_MODULE_DELETABLE' => 0,
    'IS_COLLATE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_6209ec921a642',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_6209ec921a642')) {function content_6209ec921a642($_smarty_tpl) {?>
<style>.collateloglist {font-size: 13px;margin-left: 20px;list-style:none;}.collateloglist li {position: relative;padding: 0 0 20px 20px;border-left: 1px solid #ccc;}.collateloglist li .serialnum {position: absolute;display: block;left: -10px;top: 0px;border: 2px #178fdd solid;color: #178fdd;background-color: #fff;font-size: 12px;width: 16px;height: 16px;text-align: center;line-height: 16px;vertical-align: middle;border-radius: 50%;}.collateloglist li .collatetime {display: inline-block;width: 150px;vertical-align: middle;}.collateloglist li .collator {display:inline-block;width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;vertical-align:middle}.collateloglist li .status {vertical-align:middle;margin-left:10px;}</style><div id="pagehtml"><input type="hidden" id="view" value="<?php echo $_smarty_tpl->tpl_vars['VIEW']->value;?>
" /><input type="hidden" id="pageStartRange" value="" /><input type="hidden" id="pageEndRange" value="" /><input type="hidden" id="previousPageExist" value="" /><input type="hidden" id="nextPageExist" value="" /><input type="hidden" id="alphabetSearchKey" value= "" /><input type="hidden" id="Operator" value="<?php echo $_smarty_tpl->tpl_vars['OPERATOR']->value;?>
" /><input type="hidden" id="alphabetValue" value="<?php echo $_smarty_tpl->tpl_vars['ALPHABET_VALUE']->value;?>
" /><input type="hidden" id="totalCount" value="<?php echo $_smarty_tpl->tpl_vars['PAGE_COUNT']->value;?>
" /><input type="hidden" id="public" value="<?php echo $_smarty_tpl->tpl_vars['public']->value;?>
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
" <?php if ($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']=='receivedtotal'&&$_smarty_tpl->tpl_vars['public']->value!='NoComplete'){?>style="display: none;"<?php }?> <?php if (in_array($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname'],array('attachmenttype','firstreceivedate','eleccontractstatus','firstcontract','firstfrommarket','originator','originatormobile','elereceiver','elereceivermobile','contractattribute','clientproperty','bussinesstype','stageshow','agentname','categoryid','workflowsnode','contractstate','servicecontractsprint','isstandard','pagenumber','cancelid','accountsdue','signdate','canceltime','receiptnumber','receiveid','serviceid','multitype','confirmlasttime','supercollar','cantheinvoice','isconfirm','delayuserid','iscomplete','receiptorid','sideagreement','effectivetime','isguarantee','actualeffectivetime','ispay','fulldeliverytime','isstage','returndate','currencytype','supplementarytype','receiverabledate','executor','executedate','executestatus','voucher','frameworkcontract','settlementtype','settlementclause','file','productsearchid','remark','pre_deposit','cancelvoid','cancelfeeid','cancelremark','service_charge','account_opening_fee','createdtime','tax_point','modifiedby','modifiedtime','quotes_no','first_collate_operator','first_collate_time','collate_num','first_collate_status','first_collate_remark','last_collate_operator','last_collate_time','last_collate_status','last_collate_remark','isjoinactivity'))&&$_smarty_tpl->tpl_vars['public']->value=='NoComplete'){?> style="display: none;"<?php }?>><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']=='total'){?>合同金额<?php }else{ ?><?php echo vtranslate($_smarty_tpl->tpl_vars['KEY']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php }?></th><?php } ?><th <?php if ($_smarty_tpl->tpl_vars['public']->value=='NoComplete'){?>style="display:none;"<?php }?> nowrap style="width:90px">操作</th></tr></thead><?php $_smarty_tpl->tpl_vars['IS_COLLATE'] = new Smarty_variable($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->exportGrouprt('ServiceContracts','COLLATE'), null, 0);?><?php  $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->_loop = false;
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
?><td <?php if ($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']=='receivedtotal'&&$_smarty_tpl->tpl_vars['public']->value!='NoComplete'){?>style="display: none;"<?php }?> <?php if (in_array($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname'],array('attachmenttype','firstreceivedate','eleccontractstatus','firstcontract','firstfrommarket','originator','originatormobile','elereceiver','elereceivermobile','contractattribute','clientproperty','bussinesstype','stageshow','agentname','categoryid','workflowsnode','contractstate','servicecontractsprint','isstandard','pagenumber','cancelid','accountsdue','signdate','canceltime','receiptnumber','receiveid','serviceid','multitype','confirmlasttime','supercollar','cantheinvoice','isconfirm','delayuserid','iscomplete','receiptorid','sideagreement','effectivetime','isguarantee','actualeffectivetime','ispay','fulldeliverytime','isstage','returndate','currencytype','supplementarytype','receiverabledate','executor','executedate','executestatus','voucher','frameworkcontract','settlementtype','settlementclause','file','productsearchid','remark','pre_deposit','cancelvoid','cancelfeeid','cancelremark','service_charge','account_opening_fee','createdtime','tax_point','modifiedby','modifiedtime','quotes_no','first_collate_operator','first_collate_time','collate_num','first_collate_status','first_collate_remark','last_collate_operator','last_collate_time','last_collate_status','last_collate_remark','isjoinactivity'))&&$_smarty_tpl->tpl_vars['public']->value=='NoComplete'){?> style="display: none;"<?php }?> class="listViewEntryValue <?php if ($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']=='isautoclose'){?>isautoclose_value<?php }?> <?php if ($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']=='contractstate'){?>contractstate_value<?php }?>" nowrap><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']=='contract_no'){?><a class="btn-link" href='index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Detail&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['id'];?>
' target="_block"><?php echo vtranslate($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value[$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']],$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a><?php }elseif($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']=='collate_num'){?><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['collate_num']>=1){?><span title="点击展开核对记录" class="collatelog"><?php echo uitypeformat($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value,$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
<i class="icon-list-alt"></i></span><?php }else{ ?><?php echo uitypeformat($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value,$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php }?><?php }elseif(in_array($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname'],array('first_collate_status','last_collate_status'))){?><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value[$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']]=='fit'){?>符合<?php }elseif($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value[$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value['columnname']]=='unfit'){?>不符合<?php }?><?php }else{ ?><?php echo uitypeformat($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value,$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php }?></td><?php } ?><td <?php if ($_smarty_tpl->tpl_vars['public']->value=='NoComplete'){?>style="display:none;"<?php }?>  class="listViewEntryValue" ><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->last){?><div style="width:120px"><?php if ($_smarty_tpl->tpl_vars['ISUPDATECONTRACTSCLOSE']->value){?><a  href="#" class="updateContractsCloseButton" data-status="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['isautoclose'];?>
"><i title="修改自动关闭状态" class="icon-share alignMiddle"></i></a>&nbsp;<?php }?><a  href="index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Detail&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['id'];?>
"><i title="详细信息" class="icon-th-list alignMiddle"></i></a>&nbsp;<?php if ($_smarty_tpl->tpl_vars['ISUPDATECONTRACTSSTATES']->value){?><a  href="#" class="updateContractsStatesButton" data-status="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['contractstate'];?>
"><i title="修改关闭状态" class="icon-move alignMiddle"></i></a>&nbsp;<?php }?><?php if ($_smarty_tpl->tpl_vars['IS_MODULE_EDITABLE']->value){?><a class="deleteRecordButton"><i title="删除" class="icon-trash alignMiddle"></i></a><?php }?><?php if ($_smarty_tpl->tpl_vars['IS_MODULE_DELETABLE']->value){?><a  href='index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Edit&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['id'];?>
'  target="_block"><i title="<?php echo vtranslate('LBL_EDIT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" class="icon-pencil alignMiddle"></i></a>&nbsp;<?php }?><?php if ($_smarty_tpl->tpl_vars['IS_COLLATE']->value){?><a href="#" class="collate"><i title="核对" class="icon-check alignMiddle"></i></a><?php }?><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['closedContracts']==1&&$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['modulestatus']=='c_complete'){?><i title="关停合同" class="icon-minus-sign alignMiddle closedContracts" data-msg="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['contract_no'];?>
--<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value['sc_related_to'];?>
"></i><?php }?></div><?php }?></td></tr><?php } ?></table></div></div></div>
<?php }} ?>