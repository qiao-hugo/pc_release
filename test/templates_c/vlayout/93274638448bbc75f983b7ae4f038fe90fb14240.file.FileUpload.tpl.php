<?php /* Smarty version Smarty-3.1.7, created on 2022-02-15 11:49:29
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\Vtiger\uitypes\FileUpload.tpl" */ ?>
<?php /*%%SmartyHeaderCode:24803620b22c9901fc7-00677104%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '93274638448bbc75f983b7ae4f038fe90fb14240' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\Vtiger\\uitypes\\FileUpload.tpl',
      1 => 1523874471,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '24803620b22c9901fc7-00677104',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'FIELD_MODEL' => 0,
    'FIELD_VALUE' => 0,
    'NEWFILD' => 0,
    'NFIELD_VALUE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620b22c992064',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620b22c992064')) {function content_620b22c992064($_smarty_tpl) {?><div class="fileUploadContainer" xmlns="http://www.w3.org/1999/html"><?php $_smarty_tpl->tpl_vars['FIELD_VALUE'] = new Smarty_variable(explode('*|*',$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue')), null, 0);?><div class="upload"><div style="display:inline-block;width:70px;height:30px;overflow: hidden;vertical-align: middle;"  title="文件名请勿包含空格"><div style="margin-top:-2px;">文件名请勿</div><div style="margin-top:-5px;">包含空格</div></div><input type="button" id="uploadButton" value="上传"  title="文件名请勿包含空格" /><div style="display:inline-block" id="fileall"><?php if (!empty($_smarty_tpl->tpl_vars['FIELD_VALUE']->value[0])){?><?php  $_smarty_tpl->tpl_vars['NEWFILD'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['NEWFILD']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['FIELD_VALUE']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['NEWFILD']->key => $_smarty_tpl->tpl_vars['NEWFILD']->value){
$_smarty_tpl->tpl_vars['NEWFILD']->_loop = true;
?><?php $_smarty_tpl->tpl_vars['NFIELD_VALUE'] = new Smarty_variable(explode('##',$_smarty_tpl->tpl_vars['NEWFILD']->value), null, 0);?><span class="label file<?php echo $_smarty_tpl->tpl_vars['NFIELD_VALUE']->value[1];?>
" style="margin-left:5px;"><?php echo $_smarty_tpl->tpl_vars['NFIELD_VALUE']->value[0];?>
&nbsp;<b class="deletefile" data-class="file<?php echo $_smarty_tpl->tpl_vars['NFIELD_VALUE']->value[1];?>
" data-id="<?php echo $_smarty_tpl->tpl_vars['NFIELD_VALUE']->value[1];?>
" title="删除文件" style="display:inline-block;width:12px;height:12px;line-height:12px;text-align:center">x</b>&nbsp;</span><input class="ke-input-text file<?php echo $_smarty_tpl->tpl_vars['NFIELD_VALUE']->value[1];?>
" type="hidden" name="file[<?php echo $_smarty_tpl->tpl_vars['NFIELD_VALUE']->value[1];?>
]" data-id="<?php echo $_smarty_tpl->tpl_vars['NFIELD_VALUE']->value[1];?>
" id="file" value="<?php echo $_smarty_tpl->tpl_vars['NFIELD_VALUE']->value[0];?>
" readonly="readonly" /><input class="file<?php echo $_smarty_tpl->tpl_vars['NFIELD_VALUE']->value[1];?>
" type="hidden" name="attachmentsid[<?php echo $_smarty_tpl->tpl_vars['NFIELD_VALUE']->value[1];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['NFIELD_VALUE']->value[1];?>
"><?php } ?><?php }else{ ?><input class="ke-input-text filedelete" type="hidden" name="file" id="file" value="" readonly="readonly" /><input class="filedelete" type="hidden" name="attachmentsid" value=""><?php }?></div></div></div><?php }} ?>