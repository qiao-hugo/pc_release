<?php /* Smarty version Smarty-3.1.7, created on 2022-02-16 14:21:12
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\ServiceContracts\ROWDetailViewBlockView.tpl" */ ?>
<?php /*%%SmartyHeaderCode:19482620c97d8745f05-30653071%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'effb35d38cbef1586039cdfe9ce6764e9205847c' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\ServiceContracts\\ROWDetailViewBlockView.tpl',
      1 => 1596704588,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19482620c97d8745f05-30653071',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'BLOCK_FIELDS' => 0,
    'RECORD' => 0,
    'INITNUM' => 0,
    'PHASE_SPLIT_DATA' => 0,
    'BODYTD' => 0,
    'HEADERTD' => 0,
    'FIELD_MODEL' => 0,
    'PROMPT' => 0,
    'LASTCOL' => 0,
    'MODULE' => 0,
    'USER_MODEL' => 0,
    'MODULE_NAME' => 0,
    'WIDTHTYPE' => 0,
    'WIDTHSTYLE' => 0,
    'COLDATA' => 0,
    'SUBPHASESPLIT' => 0,
    'BLOCK_LABEL' => 0,
    'BLOCK_LABEL_KEY' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620c97d87a298',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620c97d87a298')) {function content_620c97d87a298($_smarty_tpl) {?><?php $_smarty_tpl->tpl_vars['PHASE_SPLIT_DATA'] = new Smarty_variable($_smarty_tpl->tpl_vars['RECORD']->value->assignContractPhaseSplit($_smarty_tpl->tpl_vars['BLOCK_FIELDS']->value,$_smarty_tpl->tpl_vars['RECORD']->value->getId(),$_smarty_tpl->tpl_vars['INITNUM']->value,0), null, 0);?><?php if (count($_smarty_tpl->tpl_vars['PHASE_SPLIT_DATA']->value)>0){?><?php $_smarty_tpl->tpl_vars['BODYTD'] = new Smarty_variable('', null, 0);?><?php  $_smarty_tpl->tpl_vars['BLOCK_FIELDS'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['BLOCK_FIELDS']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['PHASE_SPLIT_DATA']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['BLOCK_FIELDS']->iteration=0;
 $_smarty_tpl->tpl_vars['BLOCK_FIELDS']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['BLOCK_FIELDS']->key => $_smarty_tpl->tpl_vars['BLOCK_FIELDS']->value){
$_smarty_tpl->tpl_vars['BLOCK_FIELDS']->_loop = true;
 $_smarty_tpl->tpl_vars['BLOCK_FIELDS']->iteration++;
 $_smarty_tpl->tpl_vars['BLOCK_FIELDS']->index++;
 $_smarty_tpl->tpl_vars['BLOCK_FIELDS']->first = $_smarty_tpl->tpl_vars['BLOCK_FIELDS']->index === 0;
?><?php if ($_smarty_tpl->tpl_vars['BLOCK_FIELDS']->first){?><?php $_smarty_tpl->tpl_vars['HEADERTD'] = new Smarty_variable('<tr>', null, 0);?><?php }?><?php $_smarty_tpl->tpl_vars['INITNUM'] = new Smarty_variable($_smarty_tpl->tpl_vars['BLOCK_FIELDS']->iteration, null, 0);?><?php $_smarty_tpl->tpl_vars['BODYTD'] = new Smarty_variable(((($_smarty_tpl->tpl_vars['BODYTD']->value).('<tr class="PhaseSplit')).($_smarty_tpl->tpl_vars['INITNUM']->value)).('">'), null, 0);?><?php  $_smarty_tpl->tpl_vars['FIELD_MODEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['FIELD_MODEL']->_loop = false;
 $_smarty_tpl->tpl_vars['FIELD_NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['BLOCK_FIELDS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['FIELD_MODEL']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['FIELD_MODEL']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['FIELD_MODEL']->key => $_smarty_tpl->tpl_vars['FIELD_MODEL']->value){
$_smarty_tpl->tpl_vars['FIELD_MODEL']->_loop = true;
 $_smarty_tpl->tpl_vars['FIELD_NAME']->value = $_smarty_tpl->tpl_vars['FIELD_MODEL']->key;
 $_smarty_tpl->tpl_vars['FIELD_MODEL']->iteration++;
 $_smarty_tpl->tpl_vars['FIELD_MODEL']->last = $_smarty_tpl->tpl_vars['FIELD_MODEL']->iteration === $_smarty_tpl->tpl_vars['FIELD_MODEL']->total;
?><?php $_smarty_tpl->tpl_vars['LASTCOL'] = new Smarty_variable('', null, 0);?><?php $_smarty_tpl->tpl_vars['ADDPHASESPLIT'] = new Smarty_variable('', null, 0);?><?php $_smarty_tpl->tpl_vars['SUBPHASESPLIT'] = new Smarty_variable('', null, 0);?><?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->last){?><?php $_smarty_tpl->tpl_vars['ADDPHASESPLIT'] = new Smarty_variable('', null, 0);?><?php $_smarty_tpl->tpl_vars['LASTCOL'] = new Smarty_variable('</tr>', null, 0);?><?php }?><?php if ($_smarty_tpl->tpl_vars['INITNUM']->value==1){?><?php $_smarty_tpl->tpl_vars['REDFLAG'] = new Smarty_variable('', null, 0);?><?php $_smarty_tpl->tpl_vars['PROMPT'] = new Smarty_variable('', null, 0);?><?php ob_start();?><?php echo vtranslate($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get("label"),'ServiceContracts');?>
<?php $_tmp1=ob_get_clean();?><?php $_smarty_tpl->tpl_vars['HEADERTD'] = new Smarty_variable(((((((((($_smarty_tpl->tpl_vars['HEADERTD']->value).('<td class="fieldLabel_')).($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldName())).('" style="border-bottom: 1px solid #dddddd !important;" title="')).($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('prompt'))).('"><label>')).($_tmp1)).($_smarty_tpl->tpl_vars['PROMPT']->value)).('</label></td>')).($_smarty_tpl->tpl_vars['LASTCOL']->value), null, 0);?><?php }?><?php $_smarty_tpl->tpl_vars['COLDATA'] = new Smarty_variable('', null, 0);?><?php $_smarty_tpl->tpl_vars['COLDATA'] = new Smarty_variable($_smarty_tpl->getSubTemplate (vtemplate_path($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getUITypeModel()->getDetailViewTemplateName(),$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('FIELD_MODEL'=>$_smarty_tpl->tpl_vars['FIELD_MODEL']->value,'USER_MODEL'=>$_smarty_tpl->tpl_vars['USER_MODEL']->value,'MODULE'=>$_smarty_tpl->tpl_vars['MODULE_NAME']->value,'RECORD'=>$_smarty_tpl->tpl_vars['RECORD']->value), 0));?>
<?php $_smarty_tpl->tpl_vars['WIDTHSTYLE'] = new Smarty_variable('', null, 0);?><?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldName()=='collectiondescription'){?><?php $_smarty_tpl->tpl_vars['WIDTHSTYLE'] = new Smarty_variable("width:150px;", null, 0);?><?php }?><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
<?php $_tmp2=ob_get_clean();?><?php $_smarty_tpl->tpl_vars['BODYTD'] = new Smarty_variable(((((((((($_smarty_tpl->tpl_vars['BODYTD']->value).('<td class="')).($_tmp2)).('" style="border-bottom: 1px solid #dddddd !important;')).($_smarty_tpl->tpl_vars['WIDTHSTYLE']->value)).('">')).($_smarty_tpl->tpl_vars['COLDATA']->value)).('</td>')).($_smarty_tpl->tpl_vars['SUBPHASESPLIT']->value)).($_smarty_tpl->tpl_vars['LASTCOL']->value), null, 0);?><?php } ?><?php } ?><table class="table table-bordered blockContainer showInlineTable <?php echo $_smarty_tpl->tpl_vars['BLOCK_LABEL']->value;?>
 <?php if ($_smarty_tpl->tpl_vars['BLOCK_LABEL']->value=='LBL_ADV'){?>hide tableadv<?php }?> detailview-table" style="overflow: auto;" data-stageNum="<?php echo $_smarty_tpl->tpl_vars['INITNUM']->value;?>
"><thead><tr><th class="blockHeader" colspan="4"><img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;<?php echo vtranslate($_smarty_tpl->tpl_vars['BLOCK_LABEL_KEY']->value,'ServiceContracts');?>
</th></tr></thead><tbody><tr><td colspan="4" warp><table  class="table table-bordered blockContainer showInlineTable CONTRACT_PHASE_SPLIT_LIST"><?php echo $_smarty_tpl->tpl_vars['HEADERTD']->value;?>
<?php echo $_smarty_tpl->tpl_vars['BODYTD']->value;?>
</table></td></tr></tbody></table><?php }?><?php }} ?>