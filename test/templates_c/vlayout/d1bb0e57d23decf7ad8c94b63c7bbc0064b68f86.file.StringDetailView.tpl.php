<?php /* Smarty version Smarty-3.1.7, created on 2022-02-14 16:37:09
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\Vtiger\uitypes\StringDetailView.tpl" */ ?>
<?php /*%%SmartyHeaderCode:26122620a14b5dc11a7-20229249%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd1bb0e57d23decf7ad8c94b63c7bbc0064b68f86' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\Vtiger\\uitypes\\StringDetailView.tpl',
      1 => 1631071222,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '26122620a14b5dc11a7-20229249',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'FIELD_MODEL' => 0,
    'UITYPE' => 0,
    'FIELD_VALUE_LIST' => 0,
    'PICKLIST_VALUE' => 0,
    'RECORD' => 0,
    'MODULE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620a14b5de621',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620a14b5de621')) {function content_620a14b5de621($_smarty_tpl) {?>


<?php $_smarty_tpl->tpl_vars['UITYPE'] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype'), null, 0);?>
<?php if ($_smarty_tpl->tpl_vars['UITYPE']->value==54||$_smarty_tpl->tpl_vars['UITYPE']->value==110||$_smarty_tpl->tpl_vars['UITYPE']->value==103||$_smarty_tpl->tpl_vars['UITYPE']->value==52||$_smarty_tpl->tpl_vars['UITYPE']->value==160||$_smarty_tpl->tpl_vars['UITYPE']->value==161){?>
	<?php $_smarty_tpl->tpl_vars["FIELD_VALUE_LIST"] = new Smarty_variable(explode(' |##| ',$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue')), null, 0);?>
	<?php  $_smarty_tpl->tpl_vars['PICKLIST_VALUE'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['FIELD_VALUE_LIST']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->key => $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->value){
$_smarty_tpl->tpl_vars['PICKLIST_VALUE']->_loop = true;
 $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->iteration++;
 $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->last = $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->iteration === $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->total;
?>
		<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getDisplayValue($_smarty_tpl->tpl_vars['PICKLIST_VALUE']->value,$_smarty_tpl->tpl_vars['RECORD']->value->getId(),$_smarty_tpl->tpl_vars['RECORD']->value);?>

		<?php if (!$_smarty_tpl->tpl_vars['PICKLIST_VALUE']->last){?>
		,
		<?php }?>
	<?php } ?>
<?php }elseif($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldname')=="accountrank"){?>
	<?php echo vtranslate($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getDisplayValue($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue'),$_smarty_tpl->tpl_vars['RECORD']->value->getId(),$_smarty_tpl->tpl_vars['RECORD']->value),$_smarty_tpl->tpl_vars['MODULE']->value);?>

<?php }elseif($_smarty_tpl->tpl_vars['MODULE']->value=='Knowledge'&&$_smarty_tpl->tpl_vars['UITYPE']->value==19){?>
	<?php echo decode_html($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getDisplayValue($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue'),$_smarty_tpl->tpl_vars['RECORD']->value->getId(),$_smarty_tpl->tpl_vars['RECORD']->value));?>


<?php }else{ ?>
	<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getDisplayValue($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue'),$_smarty_tpl->tpl_vars['RECORD']->value->getId(),$_smarty_tpl->tpl_vars['RECORD']->value);?>

<?php }?><?php }} ?>