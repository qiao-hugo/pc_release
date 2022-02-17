<?php /* Smarty version Smarty-3.1.7, created on 2022-02-16 17:31:23
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\ServiceContracts\uitypes\Picklist.tpl" */ ?>
<?php /*%%SmartyHeaderCode:22745620b20e37cc3b7-20244127%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a6ae0eff0d9d14238933aaad4d1e6e28e36a97a0' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\ServiceContracts\\uitypes\\Picklist.tpl',
      1 => 1645003792,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '22745620b20e37cc3b7-20244127',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620b20e38687e',
  'variables' => 
  array (
    'FIELD_MODEL' => 0,
    'FIELDNAME' => 0,
    'RECORD_ID' => 0,
    'RECORD_PRODUCTSCATEGORY' => 0,
    'PICKLIST_P' => 0,
    'NOCHZN' => 0,
    'FIELD_INFO' => 0,
    'SPECIAL_VALIDATOR' => 0,
    'PICKLIST_VALUES' => 0,
    'PICKLIST_VALUE' => 0,
    'PICKLIST_NAME' => 0,
    'RANKLIMIT' => 0,
    'RECORD_STRUCTURE_MODEL' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620b20e38687e')) {function content_620b20e38687e($_smarty_tpl) {?>
<?php $_smarty_tpl->tpl_vars["FIELD_INFO"] = new Smarty_variable(Zend_Json::encode($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldInfo()), null, 0);?><?php $_smarty_tpl->tpl_vars['PICKLIST_VALUES'] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getPicklistValues(), null, 0);?><?php $_smarty_tpl->tpl_vars["SPECIAL_VALIDATOR"] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getValidator(), null, 0);?><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldName();?>
<?php $_tmp1=ob_get_clean();?><?php $_smarty_tpl->tpl_vars["FIELDNAME"] = new Smarty_variable($_tmp1, null, 0);?><?php $_smarty_tpl->tpl_vars["NOCHZN"] = new Smarty_variable(array('eleccontracttplid'), null, 0);?><?php if ($_smarty_tpl->tpl_vars['FIELDNAME']->value=='contract_type'){?><?php if ($_smarty_tpl->tpl_vars['RECORD_ID']->value>0){?><select class="chzn-select" name="parent_contracttypeid" data-validation-engine="validate[<?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->isMandatory()==true){?> required,<?php }?>funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"   data-selected-value='<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue');?>
' style="width:110px;"><?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->isEmptyPicklistOptionAllowed()){?><option value=""><?php echo vtranslate('LBL_SELECT_OPTION','Vtiger');?>
</option><?php }?><?php  $_smarty_tpl->tpl_vars['PICKLIST_P'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['PICKLIST_P']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['RECORD_PRODUCTSCATEGORY']->value['parent']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['PICKLIST_P']->key => $_smarty_tpl->tpl_vars['PICKLIST_P']->value){
$_smarty_tpl->tpl_vars['PICKLIST_P']->_loop = true;
?><option value="<?php echo $_smarty_tpl->tpl_vars['PICKLIST_P']->value['parent_contracttypeid'];?>
" <?php if ($_smarty_tpl->tpl_vars['PICKLIST_P']->value['parent_contracttypeid']==$_smarty_tpl->tpl_vars['RECORD_PRODUCTSCATEGORY']->value['nparentid']){?> selected <?php }?>><?php echo $_smarty_tpl->tpl_vars['PICKLIST_P']->value['parent_contracttype'];?>
</option><?php } ?></select><select <?php if (!in_array($_smarty_tpl->tpl_vars['FIELDNAME']->value,$_smarty_tpl->tpl_vars['NOCHZN']->value)){?>class="chzn-select"<?php }?> name="<?php echo $_smarty_tpl->tpl_vars['FIELDNAME']->value;?>
" data-validation-engine="validate[<?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->isMandatory()==true){?> required,<?php }?>funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['FIELD_INFO']->value, ENT_QUOTES, 'UTF-8', true);?>
' <?php if (!empty($_smarty_tpl->tpl_vars['SPECIAL_VALIDATOR']->value)){?>data-validator='<?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['SPECIAL_VALIDATOR']->value);?>
'<?php }?> data-selected-value='<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue');?>
' style="width:110px;"><?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->isEmptyPicklistOptionAllowed()){?><option value=""><?php echo vtranslate('LBL_SELECT_OPTION','Vtiger');?>
</option><?php }?><?php  $_smarty_tpl->tpl_vars['PICKLIST_VALUE'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->_loop = false;
 $_smarty_tpl->tpl_vars['PICKLIST_NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['PICKLIST_VALUES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->key => $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->value){
$_smarty_tpl->tpl_vars['PICKLIST_VALUE']->_loop = true;
 $_smarty_tpl->tpl_vars['PICKLIST_NAME']->value = $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->key;
?><?php if (in_array($_smarty_tpl->tpl_vars['PICKLIST_VALUE']->value,$_smarty_tpl->tpl_vars['RECORD_PRODUCTSCATEGORY']->value['ischild'])){?><option value="<?php echo Vtiger_Util_Helper::toSafeHTML($_smarty_tpl->tpl_vars['PICKLIST_NAME']->value);?>
" <?php if (trim(decode_html($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue')))==trim($_smarty_tpl->tpl_vars['PICKLIST_NAME']->value)){?> selected <?php }?>     <?php if ($_smarty_tpl->tpl_vars['FIELDNAME']->value=='accountrank'&&isset($_smarty_tpl->tpl_vars['RANKLIMIT']->value)){?><?php if (!$_smarty_tpl->tpl_vars['RANKLIMIT']->value[$_smarty_tpl->tpl_vars['PICKLIST_NAME']->value]){?> disabled="true"<?php }?><?php }?>><?php echo $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->value;?>
</option><?php }?><?php } ?></select><?php }else{ ?><select class="chzn-select" name="parent_contracttypeid" data-validation-engine="validate[<?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->isMandatory()==true){?> required,<?php }?>funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"   data-selected-value='<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue');?>
' style="width:110px;"><?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->isEmptyPicklistOptionAllowed()){?><option value=""><?php echo vtranslate('LBL_SELECT_OPTION','Vtiger');?>
</option><?php }?><?php  $_smarty_tpl->tpl_vars['PICKLIST_P'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['PICKLIST_P']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['RECORD_PRODUCTSCATEGORY']->value['parent']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['PICKLIST_P']->key => $_smarty_tpl->tpl_vars['PICKLIST_P']->value){
$_smarty_tpl->tpl_vars['PICKLIST_P']->_loop = true;
?><option value="<?php echo $_smarty_tpl->tpl_vars['PICKLIST_P']->value['parent_contracttypeid'];?>
"><?php echo $_smarty_tpl->tpl_vars['PICKLIST_P']->value['parent_contracttype'];?>
   </option><?php } ?></select><?php }?><?php }else{ ?><?php if ($_smarty_tpl->tpl_vars['FIELDNAME']->value!='eleccontracttplid'&&$_smarty_tpl->tpl_vars['FIELDNAME']->value!='elereceiver'){?><select class="chzn-select" name="<?php echo $_smarty_tpl->tpl_vars['FIELDNAME']->value;?>
" data-validation-engine="validate[<?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->isMandatory()==true){?> required,<?php }?>funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['FIELD_INFO']->value, ENT_QUOTES, 'UTF-8', true);?>
' <?php if (!empty($_smarty_tpl->tpl_vars['SPECIAL_VALIDATOR']->value)){?>data-validator='<?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['SPECIAL_VALIDATOR']->value);?>
'<?php }?> data-selected-value='<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue');?>
'><?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->isEmptyPicklistOptionAllowed()){?><option value=""><?php echo vtranslate('LBL_SELECT_OPTION','Vtiger');?>
</option><?php }?><?php  $_smarty_tpl->tpl_vars['PICKLIST_VALUE'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->_loop = false;
 $_smarty_tpl->tpl_vars['PICKLIST_NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['PICKLIST_VALUES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->key => $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->value){
$_smarty_tpl->tpl_vars['PICKLIST_VALUE']->_loop = true;
 $_smarty_tpl->tpl_vars['PICKLIST_NAME']->value = $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->key;
?><option value="<?php echo Vtiger_Util_Helper::toSafeHTML($_smarty_tpl->tpl_vars['PICKLIST_NAME']->value);?>
" <?php if (trim(decode_html($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue')))==trim($_smarty_tpl->tpl_vars['PICKLIST_NAME']->value)){?> selected <?php }?>     <?php if ($_smarty_tpl->tpl_vars['FIELDNAME']->value=='accountrank'&&isset($_smarty_tpl->tpl_vars['RANKLIMIT']->value)){?><?php if (!$_smarty_tpl->tpl_vars['RANKLIMIT']->value[$_smarty_tpl->tpl_vars['PICKLIST_NAME']->value]){?> disabled="true"<?php }?><?php }?>><?php echo $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->value;?>
</option><?php } ?></select><?php }elseif($_smarty_tpl->tpl_vars['FIELDNAME']->value=='eleccontracttplid'){?><select class="chzn-select" name="<?php echo $_smarty_tpl->tpl_vars['FIELDNAME']->value;?>
" data-validation-engine="validate[<?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->isMandatory()==true){?> required,<?php }?>funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['FIELD_INFO']->value, ENT_QUOTES, 'UTF-8', true);?>
' <?php if (!empty($_smarty_tpl->tpl_vars['SPECIAL_VALIDATOR']->value)){?>data-validator='<?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['SPECIAL_VALIDATOR']->value);?>
'<?php }?> data-selected-value='<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue');?>
'><?php if ($_smarty_tpl->tpl_vars['RECORD_ID']->value>0){?><option value="<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue');?>
" selected><?php echo $_smarty_tpl->tpl_vars['RECORD_STRUCTURE_MODEL']->value->getRecord()->get('eleccontracttpl');?>
</option><?php }?></select><?php }elseif($_smarty_tpl->tpl_vars['FIELDNAME']->value=='elereceiver'){?><select class="chzn-select" name="<?php echo $_smarty_tpl->tpl_vars['FIELDNAME']->value;?>
" data-validation-engine="validate[<?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->isMandatory()==true){?> required,<?php }?>funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['FIELD_INFO']->value, ENT_QUOTES, 'UTF-8', true);?>
' <?php if (!empty($_smarty_tpl->tpl_vars['SPECIAL_VALIDATOR']->value)){?>data-validator='<?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['SPECIAL_VALIDATOR']->value);?>
'<?php }?> data-selected-value='<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue');?>
'><?php if ($_smarty_tpl->tpl_vars['RECORD_ID']->value>0){?><option value="<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue');?>
" selected><?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue');?>
</option><?php }?></select><?php }?><?php }?><?php }} ?>