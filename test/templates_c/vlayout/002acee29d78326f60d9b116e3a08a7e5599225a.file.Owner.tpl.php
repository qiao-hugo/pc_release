<?php /* Smarty version Smarty-3.1.7, created on 2022-02-15 11:41:23
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\ServiceContracts\uitypes\Owner.tpl" */ ?>
<?php /*%%SmartyHeaderCode:23380620b20e3951d30-30636397%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '002acee29d78326f60d9b116e3a08a7e5599225a' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\ServiceContracts\\uitypes\\Owner.tpl',
      1 => 1594688869,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '23380620b20e3951d30-30636397',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'FIELD_MODEL' => 0,
    'USER_MODEL' => 0,
    'FIELD_VALUE' => 0,
    'CURRENT_USER_ID' => 0,
    'ASSIGNED_USER_ID' => 0,
    'D53TO54' => 0,
    'FINANCIAL_MODIFICATION' => 0,
    'DEPARTMENTID' => 0,
    'MODULE' => 0,
    'IS_NOT_VALIDATOR' => 0,
    'FIELD_INFO' => 0,
    'SPECIAL_VALIDATOR' => 0,
    'ALL_ACTIVEUSER_LIST' => 0,
    'DEPARTMENTNAME' => 0,
    'DEPARTMENTNAME_LIST' => 0,
    'OWNER_ID' => 0,
    'OWNER_NAME' => 0,
    'FIELD_VALUE_LIST' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620b20e39c5f9',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620b20e39c5f9')) {function content_620b20e39c5f9($_smarty_tpl) {?>
<?php $_smarty_tpl->tpl_vars["FIELD_INFO"] = new Smarty_variable(Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldInfo())), null, 0);?><?php $_smarty_tpl->tpl_vars["SPECIAL_VALIDATOR"] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getValidator(), null, 0);?><?php $_smarty_tpl->tpl_vars["D53TO54"] = new Smarty_variable(array('Signid','Receiveid'), null, 0);?><?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')=='53'){?><?php $_smarty_tpl->tpl_vars['ASSIGNED_USER_ID'] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('name'), null, 0);?><?php $_smarty_tpl->tpl_vars['CURRENT_USER_ID'] = new Smarty_variable($_smarty_tpl->tpl_vars['USER_MODEL']->value->get('id'), null, 0);?><?php $_smarty_tpl->tpl_vars['FIELD_VALUE'] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue'), null, 0);?><?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('helpinfo')!='1'){?><?php if ($_smarty_tpl->tpl_vars['FIELD_VALUE']->value==''){?><?php $_smarty_tpl->tpl_vars['FIELD_VALUE'] = new Smarty_variable($_smarty_tpl->tpl_vars['CURRENT_USER_ID']->value, null, 0);?><?php }?><?php }?><?php if (in_array($_smarty_tpl->tpl_vars['ASSIGNED_USER_ID']->value,$_smarty_tpl->tpl_vars['D53TO54']->value)&&$_smarty_tpl->tpl_vars['FINANCIAL_MODIFICATION']->value){?><?php $_smarty_tpl->tpl_vars['ALL_ACTIVEUSER_LIST'] = new Smarty_variable($_smarty_tpl->tpl_vars['USER_MODEL']->value->getAccessibleUsers(54,'','',$_smarty_tpl->tpl_vars['DEPARTMENTID']->value), null, 0);?><select id="select_<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['ASSIGNED_USER_ID']->value;?>
"  class="chzn-select <?php echo $_smarty_tpl->tpl_vars['ASSIGNED_USER_ID']->value;?>
" <?php if (!$_smarty_tpl->tpl_vars['IS_NOT_VALIDATOR']->value){?>data-validation-engine="validate[<?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->isMandatory()==true){?>required,<?php }?>funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"<?php }?> data-name="<?php echo $_smarty_tpl->tpl_vars['ASSIGNED_USER_ID']->value;?>
" name="<?php echo $_smarty_tpl->tpl_vars['ASSIGNED_USER_ID']->value;?>
" data-fieldinfo='<?php echo $_smarty_tpl->tpl_vars['FIELD_INFO']->value;?>
' data-validator=<?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['SPECIAL_VALIDATOR']->value);?>
><?php  $_smarty_tpl->tpl_vars['DEPARTMENTNAME_LIST'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['DEPARTMENTNAME_LIST']->_loop = false;
 $_smarty_tpl->tpl_vars['DEPARTMENTNAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['ALL_ACTIVEUSER_LIST']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['DEPARTMENTNAME_LIST']->key => $_smarty_tpl->tpl_vars['DEPARTMENTNAME_LIST']->value){
$_smarty_tpl->tpl_vars['DEPARTMENTNAME_LIST']->_loop = true;
 $_smarty_tpl->tpl_vars['DEPARTMENTNAME']->value = $_smarty_tpl->tpl_vars['DEPARTMENTNAME_LIST']->key;
?><optgroup label="<?php echo $_smarty_tpl->tpl_vars['DEPARTMENTNAME']->value;?>
"><?php  $_smarty_tpl->tpl_vars['OWNER_NAME'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['OWNER_NAME']->_loop = false;
 $_smarty_tpl->tpl_vars['OWNER_ID'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['DEPARTMENTNAME_LIST']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['OWNER_NAME']->key => $_smarty_tpl->tpl_vars['OWNER_NAME']->value){
$_smarty_tpl->tpl_vars['OWNER_NAME']->_loop = true;
 $_smarty_tpl->tpl_vars['OWNER_ID']->value = $_smarty_tpl->tpl_vars['OWNER_NAME']->key;
?><?php echo $_smarty_tpl->tpl_vars['OWNER_ID']->value;?>
<option value="<?php echo $_smarty_tpl->tpl_vars['OWNER_ID']->value;?>
" data-picklistvalue= '<?php echo $_smarty_tpl->tpl_vars['OWNER_NAME']->value;?>
' <?php if ($_smarty_tpl->tpl_vars['FIELD_VALUE']->value==$_smarty_tpl->tpl_vars['OWNER_ID']->value){?> selected <?php }?>data-userId="<?php echo $_smarty_tpl->tpl_vars['CURRENT_USER_ID']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['OWNER_NAME']->value;?>
</option><?php } ?></optgroup><?php } ?></select><?php }else{ ?><?php $_smarty_tpl->tpl_vars['ALL_ACTIVEUSER_LIST'] = new Smarty_variable($_smarty_tpl->tpl_vars['USER_MODEL']->value->getAccessibleUsers(), null, 0);?><select class="chzn-select <?php echo $_smarty_tpl->tpl_vars['ASSIGNED_USER_ID']->value;?>
" data-validation-engine="validate[<?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->isMandatory()==true){?> required,<?php }?>funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-name="<?php echo $_smarty_tpl->tpl_vars['ASSIGNED_USER_ID']->value;?>
" name="<?php echo $_smarty_tpl->tpl_vars['ASSIGNED_USER_ID']->value;?>
" data-fieldinfo='<?php echo $_smarty_tpl->tpl_vars['FIELD_INFO']->value;?>
' <?php if (!empty($_smarty_tpl->tpl_vars['SPECIAL_VALIDATOR']->value)){?>data-validator=<?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['SPECIAL_VALIDATOR']->value);?>
<?php }?>><optgroup label="<?php echo vtranslate('LBL_USERS');?>
"><?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('helpinfo')=='1'){?><option value="" >选择一个选项  </option><?php }?><option value="<?php echo $_smarty_tpl->tpl_vars['FIELD_VALUE']->value;?>
" selected><?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->getUserName($_smarty_tpl->tpl_vars['FIELD_VALUE']->value);?>
</option><?php  $_smarty_tpl->tpl_vars['OWNER_NAME'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['OWNER_NAME']->_loop = false;
 $_smarty_tpl->tpl_vars['OWNER_ID'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['ALL_ACTIVEUSER_LIST']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['OWNER_NAME']->key => $_smarty_tpl->tpl_vars['OWNER_NAME']->value){
$_smarty_tpl->tpl_vars['OWNER_NAME']->_loop = true;
 $_smarty_tpl->tpl_vars['OWNER_ID']->value = $_smarty_tpl->tpl_vars['OWNER_NAME']->key;
?><?php if (($_smarty_tpl->tpl_vars['FIELD_VALUE']->value!=$_smarty_tpl->tpl_vars['OWNER_ID']->value)&&(strpos($_smarty_tpl->tpl_vars['OWNER_NAME']->value,'离职')==true)){?><?php continue 1?><?php }?><?php if ($_smarty_tpl->tpl_vars['FIELD_VALUE']->value==$_smarty_tpl->tpl_vars['OWNER_ID']->value){?><?php continue 1?><?php }?><option value="<?php echo $_smarty_tpl->tpl_vars['OWNER_ID']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['OWNER_NAME']->value;?>
</option><?php } ?></optgroup></select><?php }?><?php }?><?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')=='54'){?><?php $_smarty_tpl->tpl_vars['ALL_ACTIVEUSER_LIST'] = new Smarty_variable($_smarty_tpl->tpl_vars['USER_MODEL']->value->getAccessibleUsers($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype'),'','',$_smarty_tpl->tpl_vars['DEPARTMENTID']->value), null, 0);?><?php $_smarty_tpl->tpl_vars['ASSIGNED_USER_ID'] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('name'), null, 0);?><?php $_smarty_tpl->tpl_vars['CURRENT_USER_ID'] = new Smarty_variable($_smarty_tpl->tpl_vars['USER_MODEL']->value->get('id'), null, 0);?><?php $_smarty_tpl->tpl_vars['FIELD_VALUE'] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue'), null, 0);?><?php $_smarty_tpl->tpl_vars["FIELD_VALUE_LIST"] = new Smarty_variable(explode(' |##| ',$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue')), null, 0);?><?php if ($_smarty_tpl->tpl_vars['FIELD_VALUE']->value==''){?><?php $_smarty_tpl->tpl_vars['FIELD_VALUE'] = new Smarty_variable($_smarty_tpl->tpl_vars['CURRENT_USER_ID']->value, null, 0);?><?php }?><select id="select_<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['ASSIGNED_USER_ID']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->ismultiple==0){?>multiple<?php }?>  class="chzn-select <?php echo $_smarty_tpl->tpl_vars['ASSIGNED_USER_ID']->value;?>
" <?php if (!$_smarty_tpl->tpl_vars['IS_NOT_VALIDATOR']->value){?>data-validation-engine="validate[<?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->isMandatory()==true){?>required,<?php }?>funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"<?php }?> data-name="<?php echo $_smarty_tpl->tpl_vars['ASSIGNED_USER_ID']->value;?>
" name="<?php echo $_smarty_tpl->tpl_vars['ASSIGNED_USER_ID']->value;?>
[]" data-fieldinfo='<?php echo $_smarty_tpl->tpl_vars['FIELD_INFO']->value;?>
' data-validator=<?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['SPECIAL_VALIDATOR']->value);?>
><?php  $_smarty_tpl->tpl_vars['DEPARTMENTNAME_LIST'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['DEPARTMENTNAME_LIST']->_loop = false;
 $_smarty_tpl->tpl_vars['DEPARTMENTNAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['ALL_ACTIVEUSER_LIST']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['DEPARTMENTNAME_LIST']->key => $_smarty_tpl->tpl_vars['DEPARTMENTNAME_LIST']->value){
$_smarty_tpl->tpl_vars['DEPARTMENTNAME_LIST']->_loop = true;
 $_smarty_tpl->tpl_vars['DEPARTMENTNAME']->value = $_smarty_tpl->tpl_vars['DEPARTMENTNAME_LIST']->key;
?><optgroup label="<?php echo $_smarty_tpl->tpl_vars['DEPARTMENTNAME']->value;?>
"><?php  $_smarty_tpl->tpl_vars['OWNER_NAME'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['OWNER_NAME']->_loop = false;
 $_smarty_tpl->tpl_vars['OWNER_ID'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['DEPARTMENTNAME_LIST']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['OWNER_NAME']->key => $_smarty_tpl->tpl_vars['OWNER_NAME']->value){
$_smarty_tpl->tpl_vars['OWNER_NAME']->_loop = true;
 $_smarty_tpl->tpl_vars['OWNER_ID']->value = $_smarty_tpl->tpl_vars['OWNER_NAME']->key;
?><?php echo $_smarty_tpl->tpl_vars['OWNER_ID']->value;?>
<option value="<?php echo $_smarty_tpl->tpl_vars['OWNER_ID']->value;?>
" data-picklistvalue= '<?php echo $_smarty_tpl->tpl_vars['OWNER_NAME']->value;?>
' <?php if (in_array($_smarty_tpl->tpl_vars['OWNER_ID']->value,$_smarty_tpl->tpl_vars['FIELD_VALUE_LIST']->value)){?> selected <?php }?>data-userId="<?php echo $_smarty_tpl->tpl_vars['CURRENT_USER_ID']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['OWNER_NAME']->value;?>
</option><?php } ?></optgroup><?php } ?></select><?php }?><?php }} ?>