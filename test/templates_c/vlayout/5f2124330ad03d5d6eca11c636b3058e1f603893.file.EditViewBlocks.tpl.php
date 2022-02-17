<?php /* Smarty version Smarty-3.1.7, created on 2022-02-16 18:03:49
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\ServiceContracts\EditViewBlocks.tpl" */ ?>
<?php /*%%SmartyHeaderCode:20980620b20e3664e07-74112437%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5f2124330ad03d5d6eca11c636b3058e1f603893' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\ServiceContracts\\EditViewBlocks.tpl',
      1 => 1645005797,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20980620b20e3664e07-74112437',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620b20e37bc4f',
  'variables' => 
  array (
    'USER_MODEL' => 0,
    'PICKIST_DEPENDENCY_DATASOURCE' => 0,
    'MODULE' => 0,
    'RECORD_ID' => 0,
    'CONTRACT_CLASS_TYPE' => 0,
    'SCRELATEDTO' => 0,
    'INVOICECOMPANY' => 0,
    'SIDEAGREEMENT' => 0,
    'HASORDER' => 0,
    'MODULESTATUS' => 0,
    'IS_EDIT' => 0,
    'CHECK_ACCOUNT_AND_TOTAL' => 0,
    'IS_RELATION_OPERATION' => 0,
    'SOURCE_MODULE' => 0,
    'SOURCE_RECORD' => 0,
    'SIGNATURETYPEHREF' => 0,
    'RECORD_STRUCTURE_MODEL' => 0,
    'WKCONTACTNAME' => 0,
    'WKCONTACTPHONE' => 0,
    'WKCODE' => 0,
    'SINGLE_MODULE_NAME' => 0,
    'RECORD_STRUCTURE' => 0,
    'BLOCK_FIELDS' => 0,
    'ISRECEIVED' => 0,
    'BLOCK_LABEL' => 0,
    'FIELD_MODEL' => 0,
    'ELECCONTRACT' => 0,
    'COUNTINUFIELDS' => 0,
    'CONTRACTATTRIBUTE' => 0,
    'COUNTER' => 0,
    'WIDTHTYPE' => 0,
    'WIDTH_TYPE_CLASSSES' => 0,
    'isReferenceField' => 0,
    'REFERENCE_LIST' => 0,
    'REFERENCE_LIST_COUNT' => 0,
    'DISPLAYID' => 0,
    'REFERENCED_MODULE_STRUCT' => 0,
    'value' => 0,
    'REFERENCED_MODULE_NAME' => 0,
    'RECORD_ALLPRODUCTID' => 0,
    'RECORD_PARTPRODUCTID' => 0,
    'constactValue' => 0,
    'RECORD_ALLEPRODUCTID1' => 0,
    'extraValue' => 0,
    'EXTRAPRODUCT' => 0,
    'RECORD_ALLEPRODUCTID2' => 0,
    'RECORD_ALLEPRODUCTID3' => 0,
    'CONTRACTTYPE' => 0,
    'CATEGORY' => 0,
    'CATEGORY_VALUE' => 0,
    'CATEGORYID' => 0,
    'OWNCOMPANY' => 0,
    'OWNER_ID' => 0,
    'OWNER_NAME' => 0,
    'FIELD_VALUE' => 0,
    'CURRENT_USER_ID' => 0,
    'ACCESSIBLE_USERS_DIVIDE' => 0,
    'CONTRACTS_DIVIDE' => 0,
    'divide_data' => 0,
    'divide_key' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620b20e37bc4f')) {function content_620b20e37bc4f($_smarty_tpl) {?><link href="libraries/icheck/blue.css" rel="stylesheet"><script src="libraries/icheck/icheck.min.js"></script>

    <script>
        $(document).ready(function(){
            $('.entryCheckBox').iCheck({
                checkboxClass: 'icheckbox_minimal-blue'
            });
        });
    </script>
<div class='editViewContainer container-fluid' xmlns="http://www.w3.org/1999/html"><form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php" enctype="multipart/form-data"><?php $_smarty_tpl->tpl_vars['WIDTHTYPE'] = new Smarty_variable($_smarty_tpl->tpl_vars['USER_MODEL']->value->get('rowheight'), null, 0);?><?php if (!empty($_smarty_tpl->tpl_vars['PICKIST_DEPENDENCY_DATASOURCE']->value)){?><input type="hidden" name="picklistDependency" value='<?php echo Vtiger_Util_Helper::toSafeHTML($_smarty_tpl->tpl_vars['PICKIST_DEPENDENCY_DATASOURCE']->value);?>
' /><?php }?><div id="ajaxanalogy"></div><input type="hidden" name="module" value="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
" /><input type="hidden" name="action" value="Save" /><input type="hidden" name="record" value="<?php echo $_smarty_tpl->tpl_vars['RECORD_ID']->value;?>
" /><input type="hidden" name="contractbuytype" value="<?php echo $_smarty_tpl->tpl_vars['CONTRACT_CLASS_TYPE']->value;?>
" /><input type="hidden" name="old_sc_related_to" value="<?php echo $_smarty_tpl->tpl_vars['SCRELATEDTO']->value;?>
" /><input type="hidden" name="old_invoice_company" value="<?php echo $_smarty_tpl->tpl_vars['INVOICECOMPANY']->value;?>
" /><input type="hidden" name="sideagreement" value="<?php echo $_smarty_tpl->tpl_vars['SIDEAGREEMENT']->value;?>
" /><input type="hidden" name="hasOrder" value="<?php echo $_smarty_tpl->tpl_vars['HASORDER']->value;?>
" /><input type="hidden" name="current_modulestatus" value="<?php echo $_smarty_tpl->tpl_vars['MODULESTATUS']->value;?>
" /><input type="hidden" name="isEdit" value="<?php echo $_smarty_tpl->tpl_vars['IS_EDIT']->value;?>
" /><input type="hidden" name="check_account_and_total" value="<?php echo $_smarty_tpl->tpl_vars['CHECK_ACCOUNT_AND_TOTAL']->value;?>
" /><input type="hidden" name="is_input_account_and_total" value="0" /><?php if ($_smarty_tpl->tpl_vars['IS_RELATION_OPERATION']->value){?><input type="hidden" name="sourceModule" value="<?php echo $_smarty_tpl->tpl_vars['SOURCE_MODULE']->value;?>
" /><input type="hidden" name="sourceRecord" value="<?php echo $_smarty_tpl->tpl_vars['SOURCE_RECORD']->value;?>
" /><input type="hidden" name="relationOperation" value="<?php echo $_smarty_tpl->tpl_vars['IS_RELATION_OPERATION']->value;?>
" /><?php }?><?php if ($_smarty_tpl->tpl_vars['SIGNATURETYPEHREF']->value=='eleccontract'){?><input type="hidden" name="eleccontracttpl" value="<?php echo $_smarty_tpl->tpl_vars['RECORD_STRUCTURE_MODEL']->value->getRecord()->get('eleccontracttpl');?>
" /><input type="hidden" name="relatedattachment" value="<?php echo $_smarty_tpl->tpl_vars['RECORD_STRUCTURE_MODEL']->value->getRecord()->get('relatedattachment');?>
" /><input type="hidden" name="eleccontractid" value="<?php echo $_smarty_tpl->tpl_vars['RECORD_STRUCTURE_MODEL']->value->getRecord()->get('eleccontractid');?>
" /><input type="hidden" name="eleccontracttplurl" value="" /><input type="hidden" name="eleccontractidurl" value="" /><input type="hidden" name="relatedattachmenturl" value="" /><input type="hidden" name="oldeleccontracttplid" value="<?php echo $_smarty_tpl->tpl_vars['RECORD_STRUCTURE_MODEL']->value->getRecord()->get('eleccontracttplid');?>
" /><input type="hidden" name="oldeleccontractid" value="<?php echo $_smarty_tpl->tpl_vars['RECORD_STRUCTURE_MODEL']->value->getRecord()->get('eleccontractid');?>
" /><input type="hidden" name="oldfile" value="<?php echo $_smarty_tpl->tpl_vars['RECORD_STRUCTURE_MODEL']->value->getRecord()->get('file');?>
" /><input type="hidden" name="wkcontactname" value="<?php echo $_smarty_tpl->tpl_vars['WKCONTACTNAME']->value;?>
" /><input type="hidden" name="wkcontactphone" value="<?php echo $_smarty_tpl->tpl_vars['WKCONTACTPHONE']->value;?>
" /><input type="hidden" name="wkcode" value="<?php echo $_smarty_tpl->tpl_vars['WKCODE']->value;?>
" /><?php }?><div class="contentHeader row-fluid"><?php $_smarty_tpl->tpl_vars['SINGLE_MODULE_NAME'] = new Smarty_variable(('SINGLE_').($_smarty_tpl->tpl_vars['MODULE']->value), null, 0);?><?php if ($_smarty_tpl->tpl_vars['RECORD_ID']->value!=''){?><h3 title="<?php echo vtranslate('LBL_EDITING',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 <?php echo vtranslate($_smarty_tpl->tpl_vars['SINGLE_MODULE_NAME']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
 <?php echo $_smarty_tpl->tpl_vars['RECORD_STRUCTURE_MODEL']->value->getRecordName();?>
"><?php echo vtranslate('LBL_EDITING',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 <?php echo vtranslate($_smarty_tpl->tpl_vars['SINGLE_MODULE_NAME']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
 - <?php echo $_smarty_tpl->tpl_vars['RECORD_STRUCTURE_MODEL']->value->getRecordName();?>
</h3><hr><?php }else{ ?><h3><?php echo vtranslate('LBL_CREATING_NEW',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 <?php echo vtranslate($_smarty_tpl->tpl_vars['SINGLE_MODULE_NAME']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
</h3><hr><?php }?><span class="pull-right"><button class="btn btn-success" id="servicecontractsub" type="submit"><strong><?php echo vtranslate('LBL_SAVE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong></button><a class="cancelLink" type="reset" onclick="javascript:window.history.back();"><?php echo vtranslate('LBL_CANCEL',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a></span></div><?php $_smarty_tpl->tpl_vars["COUNTINUFIELDS"] = new Smarty_variable(array('eleccontracttpl','eleccontractid','relatedattachment'), null, 0);?><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['RECORD_STRUCTURE_MODEL']->value->getRecord()->get('contractattribute');?>
<?php $_tmp1=ob_get_clean();?><?php $_smarty_tpl->tpl_vars["CONTRACTATTRIBUTE"] = new Smarty_variable($_tmp1, null, 0);?><?php $_smarty_tpl->tpl_vars["ELECCONTRACT"] = new Smarty_variable(array('originator','originatormobile','elereceiver','elereceivermobile','eleccontractstatus','eleccontracttpl','relatedattachment','contractattribute','clientproperty','eleccontracttplid','eleccontractid','relatedattachmentid'), null, 0);?><?php  $_smarty_tpl->tpl_vars['BLOCK_FIELDS'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['BLOCK_FIELDS']->_loop = false;
 $_smarty_tpl->tpl_vars['BLOCK_LABEL'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['RECORD_STRUCTURE']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['BLOCK_FIELDS']->key => $_smarty_tpl->tpl_vars['BLOCK_FIELDS']->value){
$_smarty_tpl->tpl_vars['BLOCK_FIELDS']->_loop = true;
 $_smarty_tpl->tpl_vars['BLOCK_LABEL']->value = $_smarty_tpl->tpl_vars['BLOCK_FIELDS']->key;
?><?php if (count($_smarty_tpl->tpl_vars['BLOCK_FIELDS']->value)<=0){?><?php continue 1?><?php }?><?php if (!$_smarty_tpl->tpl_vars['ISRECEIVED']->value&&($_smarty_tpl->tpl_vars['BLOCK_LABEL']->value=='CONTRACT_PHASE_SPLIT'||$_smarty_tpl->tpl_vars['BLOCK_LABEL']->value=='SETTLEMENT_CLAUSE')){?><?php continue 1?><?php }?><?php if ($_smarty_tpl->tpl_vars['BLOCK_LABEL']->value=='CONTRACT_PHASE_SPLIT'){?><?php if ($_smarty_tpl->tpl_vars['RECORD_STRUCTURE_MODEL']->value->getRecord()->get('frameworkcontract')!='no'){?><?php continue 1?><?php }?><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('ROWEditViewBlocks.tpl',$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('BLOCK_FIELDS'=>$_smarty_tpl->tpl_vars['BLOCK_FIELDS']->value,'ISSHOWHEADER'=>true,'SHOWFIELD'=>array('stageshow','receiveableamount','collectiondescription'),'ISSUBPHASESPLIT'=>true,'INITNUM'=>1), 0);?>
<?php continue 1?><?php }?><?php if ($_smarty_tpl->tpl_vars['SIGNATURETYPEHREF']->value!='eleccontract'&&$_smarty_tpl->tpl_vars['BLOCK_LABEL']->value=='ELECCONTRACT_INFO'){?><?php continue 1?><?php }?><table class="table table-bordered blockContainer showInlineTable <?php echo $_smarty_tpl->tpl_vars['BLOCK_LABEL']->value;?>
 <?php if ($_smarty_tpl->tpl_vars['BLOCK_LABEL']->value=='LBL_ADV'){?>hide tableadv<?php }?> detailview-table"><thead><tr><th class="blockHeader" colspan="4"><img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;<?php echo vtranslate($_smarty_tpl->tpl_vars['BLOCK_LABEL']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
</th></tr></thead><tbody><tr><?php $_smarty_tpl->tpl_vars['COUNTER'] = new Smarty_variable(0, null, 0);?><?php  $_smarty_tpl->tpl_vars['FIELD_MODEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['FIELD_MODEL']->_loop = false;
 $_smarty_tpl->tpl_vars['FIELD_NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['BLOCK_FIELDS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['FIELD_MODEL']->key => $_smarty_tpl->tpl_vars['FIELD_MODEL']->value){
$_smarty_tpl->tpl_vars['FIELD_MODEL']->_loop = true;
 $_smarty_tpl->tpl_vars['FIELD_NAME']->value = $_smarty_tpl->tpl_vars['FIELD_MODEL']->key;
?><?php if ($_smarty_tpl->tpl_vars['SIGNATURETYPEHREF']->value!='eleccontract'&&in_array($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldName(),$_smarty_tpl->tpl_vars['ELECCONTRACT']->value)){?><?php continue 1?><?php }?><?php if (in_array($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldName(),$_smarty_tpl->tpl_vars['COUNTINUFIELDS']->value)){?><?php continue 1?><?php }?><?php if ((!$_smarty_tpl->tpl_vars['RECORD_ID']->value&&$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldName()=='file')||($_smarty_tpl->tpl_vars['RECORD_ID']->value&&(($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldName()=='file'&&$_smarty_tpl->tpl_vars['CONTRACTATTRIBUTE']->value=='standard')||($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldName()=='eleccontracttplid'&&$_smarty_tpl->tpl_vars['CONTRACTATTRIBUTE']->value=='customized')))){?><?php continue 1?><?php }?><?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldName()=='effectivetime'){?><?php continue 1?><?php }?><?php $_smarty_tpl->tpl_vars["isReferenceField"] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldDataType(), null, 0);?><?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')=="20"||$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')=="19"){?><?php if ($_smarty_tpl->tpl_vars['COUNTER']->value=='1'){?><td class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
"></td><td class="<?php echo $_smarty_tpl->tpl_vars['WIDTH_TYPE_CLASSSES']->value[$_smarty_tpl->tpl_vars['WIDTHTYPE']->value];?>
"></td></tr><tr><?php $_smarty_tpl->tpl_vars['COUNTER'] = new Smarty_variable(0, null, 0);?><?php }?><?php }?><?php if ($_smarty_tpl->tpl_vars['COUNTER']->value==2){?></tr><tr><?php $_smarty_tpl->tpl_vars['COUNTER'] = new Smarty_variable(1, null, 0);?><?php }else{ ?><?php $_smarty_tpl->tpl_vars['COUNTER'] = new Smarty_variable($_smarty_tpl->tpl_vars['COUNTER']->value+1, null, 0);?><?php }?><td class="fieldLabel <?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
"><?php if ($_smarty_tpl->tpl_vars['isReferenceField']->value!="reference"){?><label class="muted pull-right marginRight10px"><?php }?><?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->isMandatory()==true&&$_smarty_tpl->tpl_vars['isReferenceField']->value!="reference"||$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('name')=='agentname'){?> <span class="redColor">*</span> <?php }?><?php if ($_smarty_tpl->tpl_vars['isReferenceField']->value=="reference"){?><?php $_smarty_tpl->tpl_vars["REFERENCE_LIST"] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getReferenceList(), null, 0);?><?php $_smarty_tpl->tpl_vars["REFERENCE_LIST_COUNT"] = new Smarty_variable(count($_smarty_tpl->tpl_vars['REFERENCE_LIST']->value), null, 0);?><?php if ($_smarty_tpl->tpl_vars['REFERENCE_LIST_COUNT']->value>1){?><?php $_smarty_tpl->tpl_vars["DISPLAYID"] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue'), null, 0);?><?php $_smarty_tpl->tpl_vars["REFERENCED_MODULE_STRUCT"] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getUITypeModel()->getReferenceModule($_smarty_tpl->tpl_vars['DISPLAYID']->value), null, 0);?><?php if (!empty($_smarty_tpl->tpl_vars['REFERENCED_MODULE_STRUCT']->value)){?><?php $_smarty_tpl->tpl_vars["REFERENCED_MODULE_NAME"] = new Smarty_variable($_smarty_tpl->tpl_vars['REFERENCED_MODULE_STRUCT']->value->get('name'), null, 0);?><?php }?><span class="pull-right"><?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->isMandatory()==true){?> <span class="redColor">*</span> <?php }?><select class="chzn-select referenceModulesList streched" style="width:140px;"><optgroup><?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_smarty_tpl->tpl_vars['index'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['REFERENCE_LIST']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
 $_smarty_tpl->tpl_vars['index']->value = $_smarty_tpl->tpl_vars['value']->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['value']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['value']->value==$_smarty_tpl->tpl_vars['REFERENCED_MODULE_NAME']->value){?> selected <?php }?>><?php echo vtranslate($_smarty_tpl->tpl_vars['value']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
</option><?php } ?></optgroup></select></span><?php }else{ ?><label class="muted pull-right marginRight10px"><?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('name')=='sc_related_to'){?><span id='tripscrelatedto'></span><?php }else{ ?><?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->isMandatory()==true){?> <span class="redColor">*</span><?php }?> <?php }?><?php echo vtranslate($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('label'),$_smarty_tpl->tpl_vars['MODULE']->value);?>
</label><?php }?><?php }elseif($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')=="83"){?><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getUITypeModel()->getTemplateName(),$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('COUNTER'=>$_smarty_tpl->tpl_vars['COUNTER']->value), 0);?>
<?php }else{ ?><?php echo vtranslate($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('label'),$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php }?><?php if ($_smarty_tpl->tpl_vars['isReferenceField']->value!="reference"){?></label><?php }?></td><?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')!="83"){?><?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('label')=="Priority"){?><td class="PriorityName"><?php  $_smarty_tpl->tpl_vars['constactValue'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['constactValue']->_loop = false;
 $_smarty_tpl->tpl_vars['constactKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['RECORD_ALLPRODUCTID']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['constactValue']->key => $_smarty_tpl->tpl_vars['constactValue']->value){
$_smarty_tpl->tpl_vars['constactValue']->_loop = true;
 $_smarty_tpl->tpl_vars['constactKey']->value = $_smarty_tpl->tpl_vars['constactValue']->key;
?><div style="line-height: 30px;float: left;width: 260px; border: 1px solid  rgba(57, 15, 40, 0.18); margin: 2px;  border-radius: 5px;padding-bottom:5px;"><label class="checkbox inline"><input type="checkbox"  <?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['RECORD_PARTPRODUCTID']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['value']->key;
?><?php if ($_smarty_tpl->tpl_vars['value']->value==$_smarty_tpl->tpl_vars['constactValue']->value['productid']){?>checked <?php }?><?php } ?>value="<?php echo $_smarty_tpl->tpl_vars['constactValue']->value['productid'];?>
" name="productid[]" data-name="productid" data-istyun="<?php echo $_smarty_tpl->tpl_vars['constactValue']->value['istyun'];?>
" class="productid entryCheckBox" >&nbsp;<?php echo $_smarty_tpl->tpl_vars['constactValue']->value['productname'];?>
<input type="hidden" name="producttypename[<?php echo $_smarty_tpl->tpl_vars['constactValue']->value['productid'];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['constactValue']->value['productname'];?>
"/></label></div><?php } ?></td><?php }elseif($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('label')=="extraproductid"){?><td class="extraproductidname"><?php $_smarty_tpl->tpl_vars['EXTRAPRODUCT'] = new Smarty_variable(explode(',',$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue')), null, 0);?><table class="table table-bordered"><thead><tr><td><?php  $_smarty_tpl->tpl_vars['extraValue'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['extraValue']->_loop = false;
 $_smarty_tpl->tpl_vars['constactKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['RECORD_ALLEPRODUCTID1']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['extraValue']->key => $_smarty_tpl->tpl_vars['extraValue']->value){
$_smarty_tpl->tpl_vars['extraValue']->_loop = true;
 $_smarty_tpl->tpl_vars['constactKey']->value = $_smarty_tpl->tpl_vars['extraValue']->key;
?><div style="line-height: 30px;float: left;width: 260px; border: 1px solid  rgba(57, 15, 40, 0.18); margin: 2px;  border-radius: 5px;padding-bottom:5px;"><label class="checkbox inline"><input type="checkbox"<?php if (in_array($_smarty_tpl->tpl_vars['extraValue']->value['productid'],$_smarty_tpl->tpl_vars['EXTRAPRODUCT']->value)){?>checked <?php }?>value="<?php echo $_smarty_tpl->tpl_vars['extraValue']->value['productid'];?>
" name="extraproductid[]" data-name="extraproductid" data-istyun="<?php echo $_smarty_tpl->tpl_vars['extraValue']->value['istyun'];?>
" class="extraproductid entryCheckBox" >&nbsp;<?php echo $_smarty_tpl->tpl_vars['extraValue']->value['productname'];?>
<input type="hidden" name="eproducttypename[<?php echo $_smarty_tpl->tpl_vars['extraValue']->value['productid'];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['extraValue']->value['productname'];?>
"/></label></div><?php } ?></td></tr><tr><td><?php  $_smarty_tpl->tpl_vars['extraValue'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['extraValue']->_loop = false;
 $_smarty_tpl->tpl_vars['constactKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['RECORD_ALLEPRODUCTID2']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['extraValue']->key => $_smarty_tpl->tpl_vars['extraValue']->value){
$_smarty_tpl->tpl_vars['extraValue']->_loop = true;
 $_smarty_tpl->tpl_vars['constactKey']->value = $_smarty_tpl->tpl_vars['extraValue']->key;
?><div style="line-height: 30px;float: left;width: 260px; border: 1px solid  rgba(57, 15, 40, 0.18); margin: 2px;  border-radius: 5px;padding-bottom:5px;"><label class="checkbox inline"><input type="checkbox"<?php if (in_array($_smarty_tpl->tpl_vars['extraValue']->value['productid'],$_smarty_tpl->tpl_vars['EXTRAPRODUCT']->value)){?>checked <?php }?>value="<?php echo $_smarty_tpl->tpl_vars['extraValue']->value['productid'];?>
" name="extraproductid[]" data-istyun="<?php echo $_smarty_tpl->tpl_vars['extraValue']->value['istyun'];?>
" data-name="extraproductid" class="extraproductid entryCheckBox" >&nbsp;<?php echo $_smarty_tpl->tpl_vars['extraValue']->value['productname'];?>
<input type="hidden" name="eproducttypename[<?php echo $_smarty_tpl->tpl_vars['extraValue']->value['productid'];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['extraValue']->value['productname'];?>
"/></label></div><?php } ?></td></tr><tr><td><?php  $_smarty_tpl->tpl_vars['extraValue'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['extraValue']->_loop = false;
 $_smarty_tpl->tpl_vars['constactKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['RECORD_ALLEPRODUCTID3']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['extraValue']->key => $_smarty_tpl->tpl_vars['extraValue']->value){
$_smarty_tpl->tpl_vars['extraValue']->_loop = true;
 $_smarty_tpl->tpl_vars['constactKey']->value = $_smarty_tpl->tpl_vars['extraValue']->key;
?><div style="line-height: 30px;float: left;width: 260px; border: 1px solid  rgba(57, 15, 40, 0.18); margin: 2px;  border-radius: 5px;padding-bottom:5px;"><label class="checkbox inline"><input type="checkbox"<?php if (in_array($_smarty_tpl->tpl_vars['extraValue']->value['productid'],$_smarty_tpl->tpl_vars['EXTRAPRODUCT']->value)){?>checked <?php }?>value="<?php echo $_smarty_tpl->tpl_vars['extraValue']->value['productid'];?>
" name="extraproductid[]" data-name="extraproductid" class="extraproductid entryCheckBox" >&nbsp;<?php echo $_smarty_tpl->tpl_vars['extraValue']->value['productname'];?>
<input type="hidden" name="eproducttypename[<?php echo $_smarty_tpl->tpl_vars['extraValue']->value['productid'];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['extraValue']->value['productname'];?>
"/></label></div><?php } ?></td></tr></thead><tbody></tbody></table></td><?php }elseif($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('label')=="categoryid"){?><td class="fieldValue " id="categoryid" colspan="3"><?php if ($_smarty_tpl->tpl_vars['CONTRACTTYPE']->value=='T云WEB版'){?><select class="chzn-select" name="categoryid"  ><?php  $_smarty_tpl->tpl_vars['CATEGORY_VALUE'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['CATEGORY_VALUE']->_loop = false;
 $_smarty_tpl->tpl_vars['CATEGORY_NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['CATEGORY']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['CATEGORY_VALUE']->key => $_smarty_tpl->tpl_vars['CATEGORY_VALUE']->value){
$_smarty_tpl->tpl_vars['CATEGORY_VALUE']->_loop = true;
 $_smarty_tpl->tpl_vars['CATEGORY_NAME']->value = $_smarty_tpl->tpl_vars['CATEGORY_VALUE']->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['CATEGORY_VALUE']->value['id'];?>
" <?php if ($_smarty_tpl->tpl_vars['CATEGORYID']->value==$_smarty_tpl->tpl_vars['CATEGORY_VALUE']->value['id']){?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['CATEGORY_VALUE']->value['title'];?>
</option><?php } ?></select></td><?php }?><tr/><?php }else{ ?><td class="fieldValue <?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')=='19'){?> colspan="3" <?php $_smarty_tpl->tpl_vars['COUNTER'] = new Smarty_variable($_smarty_tpl->tpl_vars['COUNTER']->value+1, null, 0);?> <?php }?> <?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')=='20'){?> colspan="3"<?php }?>><?php if (!$_smarty_tpl->tpl_vars['RECORD_ID']->value&&$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldName()=='eleccontracttplid'){?><div id="eleccontracttplidblock"><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getUITypeModel()->getTemplateName(),$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('BLOCK_FIELDS'=>$_smarty_tpl->tpl_vars['BLOCK_FIELDS']->value), 0);?>
<button type="button" class="btn preeleccontracttpl" data-name="eleccontracttplid" style="display:inline-block;vertical-align:top;" disabled="disabled">预览</button></div><div id="fileshowblock" style="display:none;"><div class="fileUploadContainer" xmlns="http://www.w3.org/1999/html"><div class="upload"><div style="display:inline-block;width:120px;height:30px;overflow: hidden;vertical-align: middle;"  title="文件名请勿包含空格"><div style="margin-top:-2px;">支持docx格式</div><div style="margin-top:-5px;">文件大小不超过2M</div></div><input type="button" id="uploadButton" value="上传"  title="文件名请勿包含空格" /><div style="display:inline-block;white-space: normal;" id="fileall"><input class="ke-input-text filedelete" type="hidden" name="file" id="file" accept=".doc,.docx" value="" readonly="readonly" /><input class="filedelete" type="hidden" name="attachmentsid" value=""></div></div></div></div><?php }else{ ?><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getUITypeModel()->getTemplateName(),$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('BLOCK_FIELDS'=>$_smarty_tpl->tpl_vars['BLOCK_FIELDS']->value), 0);?>
<?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('name')=='invoicecompany'){?><br><span style="color: red">如果合同主体错误需作废重新提单，请谨慎填写</span><?php }?><?php }?></td><?php }?><?php }?><?php if (count($_smarty_tpl->tpl_vars['BLOCK_FIELDS']->value)==1&&$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')!="19"&&$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')!="20"&&$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype')!="30"&&$_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('name')!="recurringtype"){?><td class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
"></td><td class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
"></td><?php }?><?php } ?></tr></tbody></table><br><?php } ?><div class="widgetContainer_servicecontractproducts" data-url="module=Workflows&amp;view=Detail&amp;mode=getWorkflowsContent&amp;record=" data-name="Workflows"><div class="widget_contents"> </div></div><script>var aaaaa="<tr ><td><select  class=\"chzn-select\" name=\"suoshugongsi\[\]\"> <optgroup label=\"<?php echo vtranslate('LBL_USERS');?>
\"><?php  $_smarty_tpl->tpl_vars['OWNER_NAME'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['OWNER_NAME']->_loop = false;
 $_smarty_tpl->tpl_vars['OWNER_ID'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['OWNCOMPANY']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['OWNER_NAME']->key => $_smarty_tpl->tpl_vars['OWNER_NAME']->value){
$_smarty_tpl->tpl_vars['OWNER_NAME']->_loop = true;
 $_smarty_tpl->tpl_vars['OWNER_ID']->value = $_smarty_tpl->tpl_vars['OWNER_NAME']->key;
?><option value=\"<?php echo $_smarty_tpl->tpl_vars['OWNER_ID']->value;?>
\" data-picklistvalue= '<?php echo $_smarty_tpl->tpl_vars['OWNER_NAME']->value;?>
' <?php if ($_smarty_tpl->tpl_vars['FIELD_VALUE']->value==$_smarty_tpl->tpl_vars['OWNER_ID']->value){?> selected <?php }elseif($_smarty_tpl->tpl_vars['OWNER_ID']->value=="上海珍岛信息技术有限公司"){?>selected<?php }?>  data-userId=\"<?php echo $_smarty_tpl->tpl_vars['CURRENT_USER_ID']->value;?>
\"><?php echo $_smarty_tpl->tpl_vars['OWNER_NAME']->value;?>
 </option> <?php } ?> </optgroup> </select> </td><td> <?php if ($_smarty_tpl->tpl_vars['FIELD_VALUE']->value==''){?><?php $_smarty_tpl->tpl_vars['FIELD_VALUE'] = new Smarty_variable($_smarty_tpl->tpl_vars['USER_MODEL']->value->get('id'), null, 0);?><?php }?>	<select class=\"chzn-select\" name=\"suoshuren\[\]\"> <optgroup label=\"<?php echo vtranslate('LBL_USERS');?>
\"> <?php  $_smarty_tpl->tpl_vars['OWNER_NAME'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['OWNER_NAME']->_loop = false;
 $_smarty_tpl->tpl_vars['OWNER_ID'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['ACCESSIBLE_USERS_DIVIDE']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['OWNER_NAME']->key => $_smarty_tpl->tpl_vars['OWNER_NAME']->value){
$_smarty_tpl->tpl_vars['OWNER_NAME']->_loop = true;
 $_smarty_tpl->tpl_vars['OWNER_ID']->value = $_smarty_tpl->tpl_vars['OWNER_NAME']->key;
?> <option value=\"<?php echo $_smarty_tpl->tpl_vars['OWNER_NAME']->value['id'];?>
\" data-company=\"<?php echo $_smarty_tpl->tpl_vars['OWNER_NAME']->value['invoicecompany'];?>
\" data-picklistvalue= \"<?php echo $_smarty_tpl->tpl_vars['OWNER_NAME']->value['id'];?>
\" <?php if ($_smarty_tpl->tpl_vars['FIELD_VALUE']->value==$_smarty_tpl->tpl_vars['OWNER_NAME']->value['id']){?> selected <?php }?> data-userId=\"<?php echo $_smarty_tpl->tpl_vars['CURRENT_USER_ID']->value;?>
\"><?php echo $_smarty_tpl->tpl_vars['OWNER_NAME']->value['last_name'];?>
</option> <?php } ?> </optgroup>	</select></td><td>	<div class=\"input-append\"> <input name=\"bili\[\]\" type=\"text\" placeholder = \"请输入比例\" class=\"scaling\" ><span class=\"add-on\">%</i></span></div></td><td class=\"muted pull-right marginRight10px\"> <b><button class=\"btn btn-small deletefallinto\" type=\"button\"><i class=\" icon-trash\"></i></button></b> </td> </tr>";</script><table class="table table-bordered blockContainer showInlineTable  detailview-table" id = "fallintotable"><thead><tr><th class="blockHeader" colspan="4" ><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;合同分成信息</th></tr></thead><tbody><tr><td><b>所属公司</b></td><td><b>业绩所属人</b></td><td><b>比例</b></td><td class="muted pull-right marginRight10px"><?php if ($_smarty_tpl->tpl_vars['IS_EDIT']->value!=1){?><b><button class="btn btn-small" type="button" id="addfallinto"><i class=" icon-plus"></i></button></b><?php }?></td></tr><?php if ($_smarty_tpl->tpl_vars['CONTRACTS_DIVIDE']->value!=''){?><?php  $_smarty_tpl->tpl_vars["divide_data"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["divide_data"]->_loop = false;
 $_smarty_tpl->tpl_vars["divide_key"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['CONTRACTS_DIVIDE']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["divide_data"]->key => $_smarty_tpl->tpl_vars["divide_data"]->value){
$_smarty_tpl->tpl_vars["divide_data"]->_loop = true;
 $_smarty_tpl->tpl_vars["divide_key"]->value = $_smarty_tpl->tpl_vars["divide_data"]->key;
?><tr ><td><select class="chzn-select" disabled="disabled" name="suoshugongsi[]"><optgroup label="<?php echo vtranslate('LBL_USERS');?>
"><?php  $_smarty_tpl->tpl_vars['OWNER_NAME'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['OWNER_NAME']->_loop = false;
 $_smarty_tpl->tpl_vars['OWNER_ID'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['OWNCOMPANY']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['OWNER_NAME']->key => $_smarty_tpl->tpl_vars['OWNER_NAME']->value){
$_smarty_tpl->tpl_vars['OWNER_NAME']->_loop = true;
 $_smarty_tpl->tpl_vars['OWNER_ID']->value = $_smarty_tpl->tpl_vars['OWNER_NAME']->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['OWNER_ID']->value;?>
"  <?php if ($_smarty_tpl->tpl_vars['divide_data']->value['owncompanys']==$_smarty_tpl->tpl_vars['OWNER_ID']->value){?> selected <?php }elseif($_smarty_tpl->tpl_vars['OWNER_ID']->value=="上海珍岛信息技术有限公司"){?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['OWNER_NAME']->value;?>
</option><?php } ?></optgroup></select></td><td><select class="chzn-select" <?php if ($_smarty_tpl->tpl_vars['IS_EDIT']->value!=1){?><?php }else{ ?>disabled="disabled"<?php }?> name="suoshuren[]"><optgroup label="<?php echo vtranslate('LBL_USERS');?>
"><?php  $_smarty_tpl->tpl_vars['OWNER_NAME'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['OWNER_NAME']->_loop = false;
 $_smarty_tpl->tpl_vars['OWNER_ID'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['ACCESSIBLE_USERS_DIVIDE']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['OWNER_NAME']->key => $_smarty_tpl->tpl_vars['OWNER_NAME']->value){
$_smarty_tpl->tpl_vars['OWNER_NAME']->_loop = true;
 $_smarty_tpl->tpl_vars['OWNER_ID']->value = $_smarty_tpl->tpl_vars['OWNER_NAME']->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['OWNER_NAME']->value['id'];?>
" data-company='<?php echo $_smarty_tpl->tpl_vars['OWNER_NAME']->value['invoicecompany'];?>
' data-picklistvalue= '<?php echo $_smarty_tpl->tpl_vars['OWNER_NAME']->value['id'];?>
' <?php if ($_smarty_tpl->tpl_vars['divide_data']->value['receivedpaymentownid']==$_smarty_tpl->tpl_vars['OWNER_NAME']->value['id']){?> selected <?php }?>"><?php echo $_smarty_tpl->tpl_vars['OWNER_NAME']->value['last_name'];?>
</option><?php } ?></optgroup></select></td><td><div class="input-append"><input name="bili[]" <?php if ($_smarty_tpl->tpl_vars['IS_EDIT']->value!=1){?><?php }else{ ?>readonly<?php }?> type="text" placeholder = "请输入比例" class="scaling" value="<?php echo $_smarty_tpl->tpl_vars['divide_data']->value['scalling'];?>
" ><span class="add-on">%</i></span></div></td><td class="muted pull-right marginRight10px"><?php if ($_smarty_tpl->tpl_vars['divide_key']->value!='0'){?> <b><button class="btn btn-small deletefallinto" type="button"><i class=" icon-trash"></i></button></b><?php }?></td></tr><?php } ?><?php }?></tbody></table><?php if ($_smarty_tpl->tpl_vars['RECORD_STRUCTURE_MODEL']->value->getRecord()->get('modulestatus')=='c_recovered'&&$_smarty_tpl->tpl_vars['RECORD_STRUCTURE_MODEL']->value->getRecord()->get('isstandard')==0){?><input type="hidden" id="isbringout" value="1" /><?php }else{ ?><input type="hidden" id="isbringout" value="0" /><?php }?><?php }} ?>