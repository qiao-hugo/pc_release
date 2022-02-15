<?php /* Smarty version Smarty-3.1.7, created on 2022-02-15 09:23:41
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\Accounts\RelatedList.tpl" */ ?>
<?php /*%%SmartyHeaderCode:24467620b009ddcfab5-13441687%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '52ef65c74b1b19d2c408bad841576b326c51d415' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\Accounts\\RelatedList.tpl',
      1 => 1596704980,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '24467620b009ddcfab5-13441687',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'RELATED_LIST_LINKS' => 0,
    'RELATED_LINK' => 0,
    'IS_SELECT_BUTTON' => 0,
    'RELATION_FIELD' => 0,
    'RELATED_RECORDS' => 0,
    'PAGING' => 0,
    'RELATED_MODULE' => 0,
    'USER_MODEL' => 0,
    'RELATED_HEADERS' => 0,
    'WIDTHTYPE' => 0,
    'HEADER_FIELD' => 0,
    'RELATION_MODULENAME' => 0,
    'RELATED_RECORD' => 0,
    'KEY' => 0,
    'COLUMN_DATA' => 0,
    'FIELDNAME' => 0,
    'RELATED_HEADERNAME' => 0,
    'COLUMN_FIELDNAME' => 0,
    'MODULE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620b009de82f1',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620b009de82f1')) {function content_620b009de82f1($_smarty_tpl) {?>
<div class="relatedContainer"><div class="relatedHeader "><div class="btn-toolbar row-fluid"><div class="span8"><?php  $_smarty_tpl->tpl_vars['RELATED_LINK'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['RELATED_LINK']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['RELATED_LIST_LINKS']->value['LISTVIEWBASIC']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['RELATED_LINK']->key => $_smarty_tpl->tpl_vars['RELATED_LINK']->value){
$_smarty_tpl->tpl_vars['RELATED_LINK']->_loop = true;
?><div class="btn-group"><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['RELATED_LINK']->value->get('_selectRelation');?>
<?php $_tmp1=ob_get_clean();?><?php $_smarty_tpl->tpl_vars['IS_SELECT_BUTTON'] = new Smarty_variable($_tmp1, null, 0);?><button type="button" class="btn addButton<?php if ($_smarty_tpl->tpl_vars['IS_SELECT_BUTTON']->value==true){?> selectRelation <?php }?> "<?php if ($_smarty_tpl->tpl_vars['IS_SELECT_BUTTON']->value==true){?> data-moduleName=<?php echo $_smarty_tpl->tpl_vars['RELATED_LINK']->value->get('_module')->get('name');?>
 <?php }?><?php if (($_smarty_tpl->tpl_vars['RELATED_LINK']->value->isPageLoadLink())){?><?php if ($_smarty_tpl->tpl_vars['RELATION_FIELD']->value){?> data-name="<?php echo $_smarty_tpl->tpl_vars['RELATION_FIELD']->value->getName();?>
" <?php }?>data-url="<?php echo $_smarty_tpl->tpl_vars['RELATED_LINK']->value->getUrl();?>
"<?php }?><?php if ($_smarty_tpl->tpl_vars['IS_SELECT_BUTTON']->value!=true){?>name="addButton"<?php }?>><?php if ($_smarty_tpl->tpl_vars['IS_SELECT_BUTTON']->value==false){?><i class="icon-plus icon-white"></i><?php }?>&nbsp;<strong><?php echo $_smarty_tpl->tpl_vars['RELATED_LINK']->value->getLabel();?>
</strong></button></div><?php } ?>&nbsp;</div><div class="span4"><span class="row-fluid"><span class="span7 pushDown"><span class="pull-right pageNumbers alignTop" data-placement="bottom" data-original-title="" style="margin-top: -5px"><?php if (!empty($_smarty_tpl->tpl_vars['RELATED_RECORDS']->value)){?> <?php echo $_smarty_tpl->tpl_vars['PAGING']->value->getRecordStartRange();?>
 <?php echo vtranslate('LBL_to',$_smarty_tpl->tpl_vars['RELATED_MODULE']->value->get('name'));?>
 <?php echo $_smarty_tpl->tpl_vars['PAGING']->value->getRecordEndRange();?>
<?php }?></span></span><span class="span5 pull-right"><span class="btn-group pull-right"></span></span></span></div></div></div><div class="contents-topscroll"><div class="topscroll-div">&nbsp;</div></div><div class="relatedContents contents-bottomscroll"><div class="bottomscroll-div"><?php $_smarty_tpl->tpl_vars['WIDTHTYPE'] = new Smarty_variable($_smarty_tpl->tpl_vars['USER_MODEL']->value->get('rowheight'), null, 0);?><table class="table table-bordered listViewEntriesTable"><thead><tr class="listViewHeaders"><?php  $_smarty_tpl->tpl_vars['HEADER_FIELD'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['HEADER_FIELD']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['RELATED_HEADERS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['HEADER_FIELD']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['HEADER_FIELD']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['HEADER_FIELD']->key => $_smarty_tpl->tpl_vars['HEADER_FIELD']->value){
$_smarty_tpl->tpl_vars['HEADER_FIELD']->_loop = true;
 $_smarty_tpl->tpl_vars['HEADER_FIELD']->iteration++;
 $_smarty_tpl->tpl_vars['HEADER_FIELD']->last = $_smarty_tpl->tpl_vars['HEADER_FIELD']->iteration === $_smarty_tpl->tpl_vars['HEADER_FIELD']->total;
?><th nowrap class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
"><a href="javascript:void(0);"><?php echo vtranslate($_smarty_tpl->tpl_vars['HEADER_FIELD']->value,$_smarty_tpl->tpl_vars['RELATION_MODULENAME']->value);?>
&nbsp;&nbsp;</a></th><?php } ?><th style="width:85px;"></th></tr></thead><?php  $_smarty_tpl->tpl_vars['RELATED_RECORD'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['RELATED_RECORD']->_loop = false;
 $_smarty_tpl->tpl_vars['KEY'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['RELATED_RECORDS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['RELATED_RECORD']->key => $_smarty_tpl->tpl_vars['RELATED_RECORD']->value){
$_smarty_tpl->tpl_vars['RELATED_RECORD']->_loop = true;
 $_smarty_tpl->tpl_vars['KEY']->value = $_smarty_tpl->tpl_vars['RELATED_RECORD']->key;
?><?php $_smarty_tpl->tpl_vars['COLUMN_DATA'] = new Smarty_variable($_smarty_tpl->tpl_vars['RELATED_RECORD']->value->getData(), null, 0);?><tr class="listViewEntries" data-id='<?php echo $_smarty_tpl->tpl_vars['RELATED_RECORD']->value->getId();?>
' <?php if ($_smarty_tpl->tpl_vars['RELATED_MODULE']->value->get('name')=='Contacts'&&$_smarty_tpl->tpl_vars['KEY']->value==0){?><?php }elseif($_smarty_tpl->tpl_vars['RELATED_MODULE']->value->get('name')=='AutoTask'){?>data-recordUrl='index.php?module=AutoTask&view=Detail&record=<?php echo $_smarty_tpl->tpl_vars['COLUMN_DATA']->value["autoworkflowentityid"];?>
&source_record=<?php echo $_smarty_tpl->tpl_vars['COLUMN_DATA']->value["autoworkflowid"];?>
'<?php }elseif($_smarty_tpl->tpl_vars['RELATED_MODULE']->value->get('name')=='Potentials'){?>data-recordUrl='index.php?module=Potentials&view=Detail&record=<?php echo $_smarty_tpl->tpl_vars['COLUMN_DATA']->value["potentialid"];?>
&mode=showDetailViewByMode&requestMode=full'<?php }else{ ?>data-recordUrl='<?php echo $_smarty_tpl->tpl_vars['RELATED_RECORD']->value->getDetailViewUrl();?>
'<?php }?>><?php $_smarty_tpl->tpl_vars['COLUMN_FIELDNAME'] = new Smarty_variable($_smarty_tpl->tpl_vars['RELATED_RECORD']->value->getEntity()->column_fields, null, 0);?><?php  $_smarty_tpl->tpl_vars['HEADER_FIELD'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['HEADER_FIELD']->_loop = false;
 $_smarty_tpl->tpl_vars['FIELDNAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['RELATED_HEADERS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['HEADER_FIELD']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['HEADER_FIELD']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['HEADER_FIELD']->key => $_smarty_tpl->tpl_vars['HEADER_FIELD']->value){
$_smarty_tpl->tpl_vars['HEADER_FIELD']->_loop = true;
 $_smarty_tpl->tpl_vars['FIELDNAME']->value = $_smarty_tpl->tpl_vars['HEADER_FIELD']->key;
 $_smarty_tpl->tpl_vars['HEADER_FIELD']->iteration++;
 $_smarty_tpl->tpl_vars['HEADER_FIELD']->last = $_smarty_tpl->tpl_vars['HEADER_FIELD']->iteration === $_smarty_tpl->tpl_vars['HEADER_FIELD']->total;
?><?php $_smarty_tpl->tpl_vars['RELATED_HEADERNAME'] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELDNAME']->value, null, 0);?><td class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
"  <?php if ($_smarty_tpl->tpl_vars['RELATED_HEADERNAME']->value!='taskremark'){?>nowrap<?php }else{ ?><?php }?>><?php if (isset($_smarty_tpl->tpl_vars['COLUMN_FIELDNAME']->value[$_smarty_tpl->tpl_vars['RELATED_HEADERNAME']->value])){?><?php echo $_smarty_tpl->tpl_vars['RELATED_RECORD']->value->getDisplayValue($_smarty_tpl->tpl_vars['RELATED_HEADERNAME']->value);?>
<?php }else{ ?><?php if ($_smarty_tpl->tpl_vars['RELATED_MODULE']->value->get('name')=='AutoTask'&&$_smarty_tpl->tpl_vars['RELATED_HEADERNAME']->value=='isaction'){?><?php if ($_smarty_tpl->tpl_vars['COLUMN_DATA']->value[$_smarty_tpl->tpl_vars['RELATED_HEADERNAME']->value]=='0'){?><span class="label label-warning">未开始</span><?php }elseif($_smarty_tpl->tpl_vars['COLUMN_DATA']->value[$_smarty_tpl->tpl_vars['RELATED_HEADERNAME']->value]=='1'){?><span class="label label-success">进行中</span><?php }elseif($_smarty_tpl->tpl_vars['COLUMN_DATA']->value[$_smarty_tpl->tpl_vars['RELATED_HEADERNAME']->value]=='2'){?><span class="label label-important">已结束</span><?php }?><?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['COLUMN_DATA']->value[$_smarty_tpl->tpl_vars['RELATED_HEADERNAME']->value];?>
<?php }?><?php }?></td><?php } ?><td><?php if ($_smarty_tpl->tpl_vars['RELATED_MODULE']->value->get('name')=='Contacts'&&$_smarty_tpl->tpl_vars['KEY']->value==0){?><?php }elseif($_smarty_tpl->tpl_vars['RELATED_MODULE']->value->get('name')=='AutoTask'){?><?php }else{ ?><div class="pull-right actions"><span class="actionImages"><?php if ($_smarty_tpl->tpl_vars['RELATED_MODULE']->value->get('name')=='Potentials'){?><a target="_blank" href="index.php?module=Potentials&view=Detail&record=<?php echo $_smarty_tpl->tpl_vars['COLUMN_DATA']->value['potentialid'];?>
&mode=showDetailViewByMode&requestMode=full"><i title="<?php echo vtranslate('LBL_SHOW_COMPLETE_DETAILS',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" class="icon-th-list alignMiddle"></i></a>&nbsp;<a href="index.php?module=Potentials&view=Edit&record=<?php echo $_smarty_tpl->tpl_vars['COLUMN_DATA']->value['potentialid'];?>
"  target="_block"><i title="编辑" class="icon-pencil alignMiddle"></i></a><?php }else{ ?><a target="_blank" href="<?php echo $_smarty_tpl->tpl_vars['RELATED_RECORD']->value->getDetailViewUrl();?>
"><i title="<?php echo vtranslate('LBL_SHOW_COMPLETE_DETAILS',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" class="icon-th-list alignMiddle"></i></a>&nbsp;<?php }?></span></div><?php }?></td></tr><?php } ?></table></div></div></div>
<?php }} ?>