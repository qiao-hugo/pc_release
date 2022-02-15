<?php /* Smarty version Smarty-3.1.7, created on 2022-02-15 11:41:23
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\ServiceContracts\uitypes\String.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14015620b20e387ea43-71604526%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '86a9b5ae116f852991f488e0c4fac3062ab9a15d' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\ServiceContracts\\uitypes\\String.tpl',
      1 => 1614073031,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14015620b20e387ea43-71604526',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'FIELD_MODEL' => 0,
    'MODULE' => 0,
    'FIELD_NAME' => 0,
    'MODE' => 0,
    'FIEDLONLY' => 0,
    'FIELD_INFO' => 0,
    'SPECIAL_VALIDATOR' => 0,
    'AGENTID' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620b20e38a65a',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620b20e38a65a')) {function content_620b20e38a65a($_smarty_tpl) {?>
<?php $_smarty_tpl->tpl_vars["FIELD_INFO"] = new Smarty_variable(Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldInfo())), null, 0);?><?php $_smarty_tpl->tpl_vars["SPECIAL_VALIDATOR"] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getValidator(), null, 0);?><?php $_smarty_tpl->tpl_vars["FIEDLONLY"] = new Smarty_variable(array('elereceiver','originator','','originatormobile','elereceivermobile'), null, 0);?><?php $_smarty_tpl->tpl_vars["FIELD_NAME"] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('name'), null, 0);?><input id="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
_editView_fieldName_<?php echo $_smarty_tpl->tpl_vars['FIELD_NAME']->value;?>
" type="text"class="input-large <?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->isNameField()){?>nameField<?php }?>"data-validation-engine="validate[<?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->isMandatory()==true){?>required,<?php }?>funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"name="<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldName();?>
"  <?php if ($_smarty_tpl->tpl_vars['FIELD_NAME']->value=='agentname'){?> list="agentlist" onchange="inputSelect()"  autocomplete="off" <?php }?>value="<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue');?>
"<?php if (($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')=='106'&&$_smarty_tpl->tpl_vars['MODE']->value!='')||$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')=='3'||$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')=='4'||$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->isReadOnly()||in_array($_smarty_tpl->tpl_vars['FIELD_NAME']->value,$_smarty_tpl->tpl_vars['FIEDLONLY']->value)){?>readonly="readonly"<?php }?><?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('prompt')!=''){?> placeholder="<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('prompt');?>
"<?php }?>data-fieldinfo='<?php echo $_smarty_tpl->tpl_vars['FIELD_INFO']->value;?>
' <?php if (!empty($_smarty_tpl->tpl_vars['SPECIAL_VALIDATOR']->value)){?>data-validator=<?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['SPECIAL_VALIDATOR']->value);?>
<?php }?> /><?php if ($_smarty_tpl->tpl_vars['FIELD_NAME']->value=='agentname'){?><datalist id="agentlist"></datalist><input type="hidden" name="agentid" value="<?php echo $_smarty_tpl->tpl_vars['AGENTID']->value;?>
"/><?php }?><?php }} ?>