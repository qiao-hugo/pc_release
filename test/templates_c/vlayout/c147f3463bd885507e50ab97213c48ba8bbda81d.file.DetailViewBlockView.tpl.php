<?php /* Smarty version Smarty-3.1.7, created on 2022-02-16 16:50:37
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\ServiceContracts\DetailViewBlockView.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2442620c97d837ed95-16484622%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c147f3463bd885507e50ab97213c48ba8bbda81d' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\ServiceContracts\\DetailViewBlockView.tpl',
      1 => 1644997759,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2442620c97d837ed95-16484622',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620c97d85e0f5',
  'variables' => 
  array (
    'RECORD' => 0,
    'RECORD_STRUCTURE' => 0,
    'BLOCK_LABEL_KEY' => 0,
    'BLOCK_LIST' => 0,
    'BLOCK' => 0,
    'FIELD_MODEL_LIST' => 0,
    'SIGNATURETYPE' => 0,
    'USER_MODEL' => 0,
    'DAY_STARTS' => 0,
    'ISSTAGE' => 0,
    'ISFENQIFILE' => 0,
    'CANHANDLECONTRACTNUM' => 0,
    'MODULESTATUS' => 0,
    'TAB_LABEL' => 0,
    'accountMoneyArray' => 0,
    'IS_HIDDEN' => 0,
    'MODULE_NAME' => 0,
    'FIELD_MODEL' => 0,
    'ELECCONTRACT' => 0,
    'COUNTINUFIELDS' => 0,
    'CONTRACTATTRIBUTE' => 0,
    'TAXCLASS_DETAILS' => 0,
    'tax' => 0,
    'COUNTER' => 0,
    'WIDTHTYPE' => 0,
    'MODULE' => 0,
    'IMAGE_DETAILS' => 0,
    'IMAGE_INFO' => 0,
    'BASE_CURRENCY_SYMBOL' => 0,
    'CONFIRM' => 0,
    'TEMPV' => 0,
    'TEMPVE' => 0,
    'TEMP' => 0,
    'IS_EDITACCOUNT' => 0,
    'FIELDAJAX' => 0,
    'PERMISSIONS' => 0,
    'CONTRACTS_DIVIDE_1' => 0,
    'CONTRACTS_DIVIDE' => 0,
    'divide_data' => 0,
    'ACCESSIBLE_USERS' => 0,
    'OWNER_ID' => 0,
    'FIELD_VALUE' => 0,
    'CURRENT_USER_ID' => 0,
    'OWNER_NAME' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620c97d85e0f5')) {function content_620c97d85e0f5($_smarty_tpl) {?>
<?php $_smarty_tpl->tpl_vars["COUNTINUFIELDS"] = new Smarty_variable(array('eleccontracttpl','eleccontractid','relatedattachment','file'), null, 0);?><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['RECORD']->value->get('contractattribute');?>
<?php $_tmp1=ob_get_clean();?><?php $_smarty_tpl->tpl_vars["CONTRACTATTRIBUTE"] = new Smarty_variable($_tmp1, null, 0);?><?php $_smarty_tpl->tpl_vars["ELECCONTRACT"] = new Smarty_variable(array('originator','originatormobile','elereceiver','elereceivermobile','eleccontractstatus','eleccontracttpl','relatedattachment','contractattribute','clientproperty','eleccontracttplid','eleccontractid','relatedattachmentid'), null, 0);?><?php  $_smarty_tpl->tpl_vars['FIELD_MODEL_LIST'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['FIELD_MODEL_LIST']->_loop = false;
 $_smarty_tpl->tpl_vars['BLOCK_LABEL_KEY'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['RECORD_STRUCTURE']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['FIELD_MODEL_LIST']->key => $_smarty_tpl->tpl_vars['FIELD_MODEL_LIST']->value){
$_smarty_tpl->tpl_vars['FIELD_MODEL_LIST']->_loop = true;
 $_smarty_tpl->tpl_vars['BLOCK_LABEL_KEY']->value = $_smarty_tpl->tpl_vars['FIELD_MODEL_LIST']->key;
?><?php $_smarty_tpl->tpl_vars['BLOCK'] = new Smarty_variable($_smarty_tpl->tpl_vars['BLOCK_LIST']->value[$_smarty_tpl->tpl_vars['BLOCK_LABEL_KEY']->value], null, 0);?><?php if ($_smarty_tpl->tpl_vars['BLOCK']->value==null||count($_smarty_tpl->tpl_vars['FIELD_MODEL_LIST']->value)<=0){?><?php continue 1?><?php }?><?php if ($_smarty_tpl->tpl_vars['BLOCK_LABEL_KEY']->value=='LBL_EXTRAPRODUCT'||$_smarty_tpl->tpl_vars['BLOCK_LABEL_KEY']->value=='LBL_CUSTOM_INFORMATION'){?><?php continue 1?><?php }?><?php if ($_smarty_tpl->tpl_vars['BLOCK_LABEL_KEY']->value=='ELECCONTRACT_INFO'&&$_smarty_tpl->tpl_vars['SIGNATURETYPE']->value!='eleccontract'){?><?php continue 1?><?php }?><?php if ($_smarty_tpl->tpl_vars['BLOCK_LABEL_KEY']->value=='CONTRACT_PHASE_SPLIT'){?><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('ROWDetailViewBlockView.tpl','ServiceContracts'), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('BLOCK_FIELDS'=>$_smarty_tpl->tpl_vars['FIELD_MODEL_LIST']->value,'INITNUM'=>1), 0);?>
<?php continue 1?><?php }?><?php $_smarty_tpl->tpl_vars['IS_HIDDEN'] = new Smarty_variable($_smarty_tpl->tpl_vars['BLOCK']->value->isHidden(), null, 0);?><?php $_smarty_tpl->tpl_vars['WIDTHTYPE'] = new Smarty_variable($_smarty_tpl->tpl_vars['USER_MODEL']->value->get('rowheight'), null, 0);?><input type=hidden name="timeFormatOptions" data-value='<?php echo $_smarty_tpl->tpl_vars['DAY_STARTS']->value;?>
' /><input type=hidden name="oldstage" data-value='<?php echo $_smarty_tpl->tpl_vars['ISSTAGE']->value;?>
' value="<?php echo $_smarty_tpl->tpl_vars['ISSTAGE']->value;?>
" /><input type=hidden name="isfenfile" data-value='<?php echo $_smarty_tpl->tpl_vars['ISFENQIFILE']->value;?>
' value="<?php echo $_smarty_tpl->tpl_vars['ISFENQIFILE']->value;?>
" /><input type=hidden name="maxhandlecontractnum" data-value='<?php echo $_smarty_tpl->tpl_vars['CANHANDLECONTRACTNUM']->value;?>
' value="<?php echo $_smarty_tpl->tpl_vars['CANHANDLECONTRACTNUM']->value;?>
" /><input type=hidden name="modulestatus" data-value='<?php echo $_smarty_tpl->tpl_vars['MODULESTATUS']->value;?>
' value="<?php echo $_smarty_tpl->tpl_vars['MODULESTATUS']->value;?>
" /><?php if ($_smarty_tpl->tpl_vars['TAB_LABEL']->value=='服务合同 详细内容'&&!$_smarty_tpl->tpl_vars['accountMoneyArray']->value['sideagreement']){?><div class="relatedContents contents-bottomscroll"><div class="bottomscroll-div"><table class="table table-bordered listViewEntriesTable"><thead><tr class="listViewHeaders"><th class="medium" colspan="2"><a href="javascript:void(0); ">回款概要</a></th><th class="medium" colspan="2"><a href="javascript:void(0);" class="pull-right" style="color: #02A7F0" id="receRefresh">刷新</a></th></tr></thead><tbody><tr class="listViewEntries"><td class="fieldLabel medium" style="width: 200px"><label class="muted pull-right marginRight10px">合同总金额</label></td><td class="fieldValue medium" style="width: 200px"><span class="value" id="totalMoney"><?php echo $_smarty_tpl->tpl_vars['accountMoneyArray']->value['paymentTotal'];?>
</span></td><td class="fieldLabel medium" style="width: 200px"><label class="muted pull-right marginRight10px">已回款金额</label></td><td class="fieldValue medium" style="width: 200px"><span class="value" id="receivedMoney"><?php echo $_smarty_tpl->tpl_vars['accountMoneyArray']->value['paymentReceived'];?>
</span></td></tr><tr class="listViewEntries"><td class="fieldLabel medium" style="width: 200px"><label class="muted pull-right marginRight10px">剩余未回款金额</label></td><td class="fieldValue medium" style="width: 200px"><span class="value" id="remainMoney"><?php echo $_smarty_tpl->tpl_vars['accountMoneyArray']->value['paymentElse'];?>
</span></td><td class="fieldLabel medium" style="width: 200px"><label class="muted pull-right marginRight10px">剩余分期付款最低可回款金额</label></td><td class="fieldValue medium" style="width: 200px"><span class="value" id="lowestMoney"><?php echo $_smarty_tpl->tpl_vars['accountMoneyArray']->value['leastPayMoney'];?>
</span></td></tr></tbody></table></div></div><?php }elseif($_smarty_tpl->tpl_vars['accountMoneyArray']->value['sideagreement']){?><div class="relatedContents contents-bottomscroll"><div class="bottomscroll-div"><table class="table table-bordered listViewEntriesTable"><thead><tr class="listViewHeaders"><th class="medium"><a href="javascript:void(0); ">回款概要</a></th></tr></thead><tbody><tr class="listViewEntries"><td style="text-align: center">补充协议无回款概要</td></tr></tbody></table></div></div><?php }?><table class="table table-bordered equalSplit detailview-table"><thead><tr><th class="blockHeader" colspan="4"><img class="cursorPointer alignMiddle blockToggle <?php if (!($_smarty_tpl->tpl_vars['IS_HIDDEN']->value)){?> hide <?php }?> "  src="<?php echo vimage_path('arrowRight.png');?>
" data-mode="hide" data-id=<?php echo $_smarty_tpl->tpl_vars['BLOCK_LIST']->value[$_smarty_tpl->tpl_vars['BLOCK_LABEL_KEY']->value]->get('id');?>
><img class="cursorPointer alignMiddle blockToggle <?php if (($_smarty_tpl->tpl_vars['IS_HIDDEN']->value)){?> hide <?php }?>"  src="<?php echo vimage_path('arrowDown.png');?>
" data-mode="show" data-id=<?php echo $_smarty_tpl->tpl_vars['BLOCK_LIST']->value[$_smarty_tpl->tpl_vars['BLOCK_LABEL_KEY']->value]->get('id');?>
>&nbsp;&nbsp;<?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['BLOCK_LABEL_KEY']->value;?>
<?php $_tmp2=ob_get_clean();?><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['MODULE_NAME']->value;?>
<?php $_tmp3=ob_get_clean();?><?php echo vtranslate($_tmp2,$_tmp3);?>
</th></tr></thead><tbody <?php if ($_smarty_tpl->tpl_vars['IS_HIDDEN']->value){?> class="hide" <?php }?>><?php $_smarty_tpl->tpl_vars['COUNTER'] = new Smarty_variable(0, null, 0);?><tr><?php  $_smarty_tpl->tpl_vars['FIELD_MODEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['FIELD_MODEL']->_loop = false;
 $_smarty_tpl->tpl_vars['FIELD_NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['FIELD_MODEL_LIST']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['FIELD_MODEL']->key => $_smarty_tpl->tpl_vars['FIELD_MODEL']->value){
$_smarty_tpl->tpl_vars['FIELD_MODEL']->_loop = true;
 $_smarty_tpl->tpl_vars['FIELD_NAME']->value = $_smarty_tpl->tpl_vars['FIELD_MODEL']->key;
?><?php if ($_smarty_tpl->tpl_vars['SIGNATURETYPE']->value!='eleccontract'&&in_array($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldName(),$_smarty_tpl->tpl_vars['ELECCONTRACT']->value)){?><?php continue 1?><?php }?><?php if (in_array($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldName(),$_smarty_tpl->tpl_vars['COUNTINUFIELDS']->value)){?><?php continue 1?><?php }?><?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldName()=='eleccontracttplid'&&$_smarty_tpl->tpl_vars['CONTRACTATTRIBUTE']->value=='customized'){?><?php continue 1?><?php }?><?php if (!$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->isViewableInDetailView()||$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getName()=='workflowsid'){?><?php continue 1?><?php }?><?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')=="83"){?><?php  $_smarty_tpl->tpl_vars['tax'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['tax']->_loop = false;
 $_smarty_tpl->tpl_vars['count'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['TAXCLASS_DETAILS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['tax']->key => $_smarty_tpl->tpl_vars['tax']->value){
$_smarty_tpl->tpl_vars['tax']->_loop = true;
 $_smarty_tpl->tpl_vars['count']->value = $_smarty_tpl->tpl_vars['tax']->key;
?><?php if ($_smarty_tpl->tpl_vars['tax']->value['check_value']==1){?><?php if ($_smarty_tpl->tpl_vars['COUNTER']->value==2){?></tr><tr><?php $_smarty_tpl->tpl_vars["COUNTER"] = new Smarty_variable(1, null, 0);?><?php }else{ ?><?php $_smarty_tpl->tpl_vars["COUNTER"] = new Smarty_variable($_smarty_tpl->tpl_vars['COUNTER']->value+1, null, 0);?><?php }?><td class="fieldLabel <?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
"><label class='muted pull-right marginRight10px'><?php echo vtranslate($_smarty_tpl->tpl_vars['tax']->value['taxlabel'],$_smarty_tpl->tpl_vars['MODULE']->value);?>
(%)</label></td><td class="fieldValue <?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
"><span class="value"><?php echo $_smarty_tpl->tpl_vars['tax']->value['percentage'];?>
</span></td><?php }?><?php } ?><?php }elseif($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')=="69"||$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')=="105"){?><?php if ($_smarty_tpl->tpl_vars['COUNTER']->value!=0){?><?php if ($_smarty_tpl->tpl_vars['COUNTER']->value==2){?></tr><tr><?php $_smarty_tpl->tpl_vars['COUNTER'] = new Smarty_variable(0, null, 0);?><?php }?><?php }?><td class="fieldLabel <?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
"><label class="muted pull-right marginRight10px"><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('label');?>
<?php $_tmp4=ob_get_clean();?><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['MODULE_NAME']->value;?>
<?php $_tmp5=ob_get_clean();?><?php echo vtranslate($_tmp4,$_tmp5);?>
</label></td><td class="fieldValue <?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
"><div id="imageContainer" width="300" height="200"><?php  $_smarty_tpl->tpl_vars['IMAGE_INFO'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['IMAGE_INFO']->_loop = false;
 $_smarty_tpl->tpl_vars['ITER'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['IMAGE_DETAILS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['IMAGE_INFO']->key => $_smarty_tpl->tpl_vars['IMAGE_INFO']->value){
$_smarty_tpl->tpl_vars['IMAGE_INFO']->_loop = true;
 $_smarty_tpl->tpl_vars['ITER']->value = $_smarty_tpl->tpl_vars['IMAGE_INFO']->key;
?><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['IMAGE_INFO']->value['orgname'];?>
<?php $_tmp6=ob_get_clean();?><?php if (!empty($_smarty_tpl->tpl_vars['IMAGE_INFO']->value['path'])&&!empty($_tmp6)){?><img src="<?php echo $_smarty_tpl->tpl_vars['IMAGE_INFO']->value['path'];?>
_<?php echo $_smarty_tpl->tpl_vars['IMAGE_INFO']->value['orgname'];?>
" width="300" height="200"><?php }?><?php } ?></div></td><?php $_smarty_tpl->tpl_vars['COUNTER'] = new Smarty_variable($_smarty_tpl->tpl_vars['COUNTER']->value+1, null, 0);?><?php }else{ ?><?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')=="20"||$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')=="19"){?><?php if ($_smarty_tpl->tpl_vars['COUNTER']->value=='1'){?><td class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
"></td><td class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
"></td></tr><tr><?php $_smarty_tpl->tpl_vars['COUNTER'] = new Smarty_variable(0, null, 0);?><?php }?><?php }?><?php if ($_smarty_tpl->tpl_vars['COUNTER']->value==2){?></tr><tr><?php $_smarty_tpl->tpl_vars['COUNTER'] = new Smarty_variable(1, null, 0);?><?php }else{ ?><?php $_smarty_tpl->tpl_vars['COUNTER'] = new Smarty_variable($_smarty_tpl->tpl_vars['COUNTER']->value+1, null, 0);?><?php }?><td class="fieldLabel <?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
" id="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
_detailView_fieldLabel_<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getName();?>
"><label class="muted pull-right marginRight10px"><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('label');?>
<?php $_tmp7=ob_get_clean();?><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['MODULE_NAME']->value;?>
<?php $_tmp8=ob_get_clean();?><?php echo vtranslate($_tmp7,$_tmp8);?>
<?php if (($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')=='72')&&($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getName()=='unit_price')){?>(<?php echo $_smarty_tpl->tpl_vars['BASE_CURRENCY_SYMBOL']->value;?>
)<?php }?></label></td><td class="fieldValue <?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
" id="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
_detailView_fieldValue_<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getName();?>
" <?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')=='19'||$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')=='20'){?> colspan="3" <?php $_smarty_tpl->tpl_vars['COUNTER'] = new Smarty_variable($_smarty_tpl->tpl_vars['COUNTER']->value+1, null, 0);?> <?php }?>><span class="value" data-field-type="<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldDataType();?>
"><?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getName()!='isconfirm'||($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getName()=='isconfirm'&&$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue')==0)){?><?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getName()=='eleccontracttplid'){?><?php echo $_smarty_tpl->tpl_vars['RECORD']->value->get('eleccontracttpl');?>
<?php }elseif($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getName()=='elatedattachmentid'){?><?php echo $_smarty_tpl->tpl_vars['RECORD']->value->get('relatedattachment');?>
<?php }else{ ?><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getUITypeModel()->getDetailViewTemplateName(),$_smarty_tpl->tpl_vars['MODULE_NAME']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('FIELD_MODEL'=>$_smarty_tpl->tpl_vars['FIELD_MODEL']->value,'USER_MODEL'=>$_smarty_tpl->tpl_vars['USER_MODEL']->value,'MODULE'=>$_smarty_tpl->tpl_vars['MODULE_NAME']->value,'RECORD'=>$_smarty_tpl->tpl_vars['RECORD']->value), 0);?>
<?php }?><?php }else{ ?><?php $_smarty_tpl->tpl_vars['CONFIRM'] = new Smarty_variable(explode('##',$_smarty_tpl->tpl_vars['RECORD']->value->entity->column_fields['confirmvalue']), null, 0);?><?php $_smarty_tpl->tpl_vars['TEMP'] = new Smarty_variable(array(), null, 0);?><?php  $_smarty_tpl->tpl_vars['TEMPV'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['TEMPV']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['CONFIRM']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['TEMPV']->key => $_smarty_tpl->tpl_vars['TEMPV']->value){
$_smarty_tpl->tpl_vars['TEMPV']->_loop = true;
?><?php $_smarty_tpl->tpl_vars['TEMPVE'] = new Smarty_variable(explode(',',$_smarty_tpl->tpl_vars['TEMPV']->value), null, 0);?><?php $_smarty_tpl->createLocalArrayVariable('TEMP', null, 0);
$_smarty_tpl->tpl_vars['TEMP']->value[] = ('<span style="width:100px;display:inline-block;overflow:hidden;"><i class="icon-user"></i>').($_smarty_tpl->tpl_vars['TEMPVE']->value[0]).('</span><i class="icon-time"></i>').($_smarty_tpl->tpl_vars['TEMPVE']->value[1]);?><?php } ?><i class="icon-th-list alignMiddle" title="审查详情" data-container="body" data-toggle="popover" data-placement="right" data-content='<?php echo implode('<br>',$_smarty_tpl->tpl_vars['TEMP']->value);?>
'></i><?php }?></span><?php if ($_smarty_tpl->tpl_vars['IS_EDITACCOUNT']->value&&$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getName()=='sc_related_to'){?><span class="hide edit"><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getUITypeModel()->getTemplateName(),$_smarty_tpl->tpl_vars['MODULE_NAME']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('FIELD_MODEL'=>$_smarty_tpl->tpl_vars['FIELD_MODEL']->value,'USER_MODEL'=>$_smarty_tpl->tpl_vars['USER_MODEL']->value,'MODULE'=>$_smarty_tpl->tpl_vars['MODULE_NAME']->value), 0);?>
<input type="hidden" class="fieldname" value='<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('name');?>
' data-prev-value='<?php echo Vtiger_Util_Helper::toSafeHTML($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getDisplayValue($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue')));?>
' /></span><?php }?><?php $_smarty_tpl->tpl_vars['FIELDAJAX'] = new Smarty_variable(array('multitype','invoicecompany',"contractstate",'isautoclose','effectivetime','billcontent','total','remark'), null, 0);?><?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->isEditable()=='true'&&($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldDataType()!=Vtiger_Field_Model::REFERENCE_TYPE)&&$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->isAjaxEditable()=='true'&&in_array($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getName(),$_smarty_tpl->tpl_vars['FIELDAJAX']->value)){?><span class="hide edit"><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getUITypeModel()->getTemplateName(),$_smarty_tpl->tpl_vars['MODULE_NAME']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('FIELD_MODEL'=>$_smarty_tpl->tpl_vars['FIELD_MODEL']->value,'USER_MODEL'=>$_smarty_tpl->tpl_vars['USER_MODEL']->value,'MODULE'=>$_smarty_tpl->tpl_vars['MODULE_NAME']->value), 0);?>
<?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldDataType()=='multipicklist'){?><input type="hidden" class="fieldname" value='<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('name');?>
[]' data-prev-value='<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getDisplayValue($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue'));?>
' /><?php }else{ ?><input type="hidden" class="fieldname" value='<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('name');?>
' data-prev-value='<?php echo Vtiger_Util_Helper::toSafeHTML($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getDisplayValue($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue')));?>
' /><?php }?></span><?php }?></td><?php }?><?php if (count($_smarty_tpl->tpl_vars['FIELD_MODEL_LIST']->value)==1&&$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')!="19"&&$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')!="20"&&$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')!="30"&&$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('name')!="recurringtype"&&$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')!="69"&&$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')!="105"){?><td class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
"></td><td class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
"></td><?php }?><?php } ?></tr><?php if ($_smarty_tpl->tpl_vars['BLOCK_LABEL_KEY']->value=='LBL_SERVICE_CONTRACT_INFORMATION'){?><td class="fieldLabel medium" id="ServiceContracts_detailView_fieldLabel_account_zizhi_file"><label class="muted pull-right marginRight10px">客户资质附件</label></td><td class="fieldValue medium" id="ServiceContracts_detailView_fieldValue_account_zizhi_file"><span class="value" data-field-type="FileUpload" id="FileUpload"></span></td><?php }?></tbody></table><br><?php } ?><div class="widgetContainer_servicecontract" data-url="module=ServiceContracts&view=Detail&mode=getProducts&amp;record=<?php echo $_smarty_tpl->tpl_vars['RECORD']->value->getId();?>
" data-name="Workflows"><div class="widget_contents"></div></div><br/><br/><table class="table table-bordered blockContainer showInlineTable  detailview-table" id = "fallintotable"><thead><tr><th class="blockHeader" colspan="4" >合同分成信息<?php if ($_smarty_tpl->tpl_vars['PERMISSIONS']->value=='true'){?><?php if (count($_smarty_tpl->tpl_vars['CONTRACTS_DIVIDE_1']->value)==0){?><span style="float: right; " ><a id="divided_modification" class="btn label label-info" style="outline:none;border:none" >分成修改申请</a></span><?php }?><?php }?></th></tr></thead><tbody><tr><td><b>所属公司</b></td><td><b>业绩所属人</b></td><td><b>比例</b></td><?php  $_smarty_tpl->tpl_vars["divide_data"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["divide_data"]->_loop = false;
 $_smarty_tpl->tpl_vars["divide_key"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['CONTRACTS_DIVIDE']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["divide_data"]->key => $_smarty_tpl->tpl_vars["divide_data"]->value){
$_smarty_tpl->tpl_vars["divide_data"]->_loop = true;
 $_smarty_tpl->tpl_vars["divide_key"]->value = $_smarty_tpl->tpl_vars["divide_data"]->key;
?><tr ><td><?php echo $_smarty_tpl->tpl_vars['divide_data']->value['owncompanys'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['divide_data']->value['receivedpaymentownname'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['divide_data']->value['scalling'];?>
%</td></tr><?php } ?></tbody></table><?php if (count($_smarty_tpl->tpl_vars['CONTRACTS_DIVIDE_1']->value)!=0){?><div style="position:relative;"><div style='margin:0 auto; top: 50%;right:50%;position: absolute;border:1px solid red;width:60px;text-align:center;color:red;border-radius:5px;font-size:24px;transform: rotate(40deg);-o-transform: rotate(40deg);-webkit-transform: rotate(40deg);-moz-transform: rotate(40deg);filter:progid:DXImageTransform.Microsoft.BasicImage(Rotation=2);'>分成审批</div><table class="table table-bordered blockContainer showInlineTable  detailview-table" id = "fallintotable_1_1"> <!-- 合同分成审批 --><thead><tr><th class="blockHeader" colspan="4">合同分成信息</th></tr></thead><tbody><tr><td><b>所属公司</b></td><td><b>业绩所属人</b></td><td><b>比例</b></td><?php  $_smarty_tpl->tpl_vars["divide_data"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["divide_data"]->_loop = false;
 $_smarty_tpl->tpl_vars["divide_key"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['CONTRACTS_DIVIDE_1']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["divide_data"]->key => $_smarty_tpl->tpl_vars["divide_data"]->value){
$_smarty_tpl->tpl_vars["divide_data"]->_loop = true;
 $_smarty_tpl->tpl_vars["divide_key"]->value = $_smarty_tpl->tpl_vars["divide_data"]->key;
?><tr><td><?php echo $_smarty_tpl->tpl_vars['divide_data']->value['owncompanys'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['divide_data']->value['receivedpaymentownname'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['divide_data']->value['scalling'];?>
%</td></tr><?php } ?></tbody></table></div><?php }?><!--     start 分割线    --------------------------------------------------><!--      end  分割线    --------------------------------------------------><script>
		$(function (){
			$("[data-toggle='popover']").popover();
        });
		var divideUserId = <?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->get('id');?>
;var staffList;</script><script type="text/javascript">var accessible_users = "<select id=\"ddddd\" class=\"chzn-select\" name=\"reportsower\"> <optgroup label=\"<?php echo vtranslate('LBL_USERS');?>
\">     <?php  $_smarty_tpl->tpl_vars['OWNER_NAME'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['OWNER_NAME']->_loop = false;
 $_smarty_tpl->tpl_vars['OWNER_ID'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['ACCESSIBLE_USERS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['OWNER_NAME']->key => $_smarty_tpl->tpl_vars['OWNER_NAME']->value){
$_smarty_tpl->tpl_vars['OWNER_NAME']->_loop = true;
 $_smarty_tpl->tpl_vars['OWNER_ID']->value = $_smarty_tpl->tpl_vars['OWNER_NAME']->key;
?> <option value=\"<?php echo $_smarty_tpl->tpl_vars['OWNER_ID']->value;?>
\" data-picklistvalue= '<?php echo $_smarty_tpl->tpl_vars['OWNER_ID']->value;?>
' <?php if ($_smarty_tpl->tpl_vars['FIELD_VALUE']->value==$_smarty_tpl->tpl_vars['OWNER_ID']->value){?> selected <?php }?> data-userId=\"<?php echo $_smarty_tpl->tpl_vars['CURRENT_USER_ID']->value;?>
\"><?php echo $_smarty_tpl->tpl_vars['OWNER_NAME']->value;?>
</option> <?php } ?> </optgroup>    </select>";</script>
<?php }} ?>