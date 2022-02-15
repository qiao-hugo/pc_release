<?php /* Smarty version Smarty-3.1.7, created on 2022-02-15 11:48:44
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\Staypayment\DefaultListFields.tpl" */ ?>
<?php /*%%SmartyHeaderCode:28839620b229c2a5204-73357135%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'eb403bfbf4c260e8d48d6fe00b886eb5f65c5cb9' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\Staypayment\\DefaultListFields.tpl',
      1 => 1628836324,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '28839620b229c2a5204-73357135',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LISTVIEW_LINKS' => 0,
    'MODULE' => 0,
    'LISTVIEW_BASICACTION' => 0,
    'ISDEFAULT' => 0,
    'ISPAGE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620b229c2c010',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620b229c2c010')) {function content_620b229c2c010($_smarty_tpl) {?><div class="row" style="padding: 10px 20px 0 20px;"><span class="span4"><?php if (count($_smarty_tpl->tpl_vars['LISTVIEW_LINKS']->value['LISTVIEWBASIC'])>0){?><?php  $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_LINKS']->value['LISTVIEWBASIC']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->key => $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->_loop = true;
?><span class="btn-group"><button id="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
_listView_basicAction_<?php echo Vtiger_Util_Helper::replaceSpaceWithUnderScores($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getLabel());?>
" class="btn addButton" <?php if (stripos($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getUrl(),'javascript:')===0){?> onclick='<?php echo substr($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getUrl(),strlen("javascript:"));?>
;'<?php }else{ ?> onclick='window.location.href="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getUrl();?>
"'<?php }?>><i class="icon-plus icon-white"></i>&nbsp;<strong><?php echo vtranslate($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getLabel(),$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong></button></span><?php } ?><?php }?><span class="btn-group"><button id="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
_listView_basicAction_export" class="btn btn-success exportButton"><strong>导出</strong></button></span><?php if ($_smarty_tpl->tpl_vars['ISDEFAULT']->value){?><?php if ($_GET['filter']!='changeHistory'){?><span>&nbsp;<input onclick="Vtiger_List_Js.showUserFieldEdit('index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=FieldAjax');" class="btn"type="button" value="列表自定义"></span><?php }?><?php }?><span>&nbsp;<a href="/index.php?module=Knowledge&view=Detail&record=2060" target="_blank" style="color:red;">代付款证明模板</a></span></span><?php if ($_smarty_tpl->tpl_vars['ISPAGE']->value){?><span class="span8" style="text-align: right">  <span>&nbsp;<select id="limit" name="limit" style="width: 80px;margin-top: -13px;"><option value="10">10</option><option value="20" selected="selected">20</option><option value="50">50</option><option value="100">100</option></select>&nbsp;</span><span class="pagination" style="" id="pagination"><ul class="pagination-demo"></ul></span><span>&nbsp;<input type="text" name="jumppage" value="" id="jumppage" class="input-small"style="width: 50px;margin-top: -13px;" placeholder="跳转">&nbsp;</span></span><?php }?></div>
<?php }} ?>