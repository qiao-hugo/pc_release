<?php /* Smarty version Smarty-3.1.7, created on 2022-02-16 16:50:37
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\ServiceContracts\LineItemsDetail.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1045620cbadddba806-57065033%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '174f420e1d4d5719f13a50e5cc6bfbde894d80aa' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\ServiceContracts\\LineItemsDetail.tpl',
      1 => 1634605764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1045620cbadddba806-57065033',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'Product' => 0,
    'FINAL_DETAILS' => 0,
    'MODULE_NAME' => 0,
    'LINE_ITEM_DETAIL' => 0,
    'tagid' => 0,
    'ptagid' => 0,
    'nptagid' => 0,
    'CASTING' => 0,
    'PURH' => 0,
    'CASTINGARR' => 0,
    'CASTINGPUAH' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620cbadde993a',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620cbadde993a')) {function content_620cbadde993a($_smarty_tpl) {?><?php $_smarty_tpl->tpl_vars['FINAL_DETAILS'] = new Smarty_variable($_smarty_tpl->tpl_vars['Product']->value, null, 0);?>
<table class="table table-bordered mergeTables detailview-table">
    <thead>
    <th colspan="<?php if ($_smarty_tpl->tpl_vars['FINAL_DETAILS']->value[0]['istyunweb']==1){?>13<?php }else{ ?>12<?php }?>" class="detailViewBlockHeader">
	<?php echo vtranslate('LBL_ITEM_DETAILS',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>

    </th>
    
	</thead>
	<tbody>
	    <tr>
	    	
			<td nowrap><b>产品名称</b></td>
            <td nowrap><b>所属套餐</b></td>
            <td nowrap><b>额外产品</b></td>
            <td nowrap><b>规格</b></td>
            <td nowrap><b>供应商</b></td>
            <td nowrap><b>采购合同</b></td>
            <td nowrap><b>数量</b></td>
            <td nowrap><b>年限(月)</b></td>
			<td nowrap><b>成本价</b></td>
			<td nowrap><b>外采成本</b></td>
	    	<td class="hide" nowrap><b></b></td>
	    	<td class="hide" nowrap><b></b></td>
	    	<td nowrap><b>备注</b></td>
	    	

	        <td><b>创建时间</b></td>
	        <?php if ($_smarty_tpl->tpl_vars['FINAL_DETAILS']->value[0]['istyunweb']==1){?><td><b>有效期间</b></td><?php }?>
	    </tr>
        <?php $_smarty_tpl->tpl_vars['ptagid'] = new Smarty_variable(array(), null, 0);?>
        <?php $_smarty_tpl->tpl_vars['nptagid'] = new Smarty_variable(array(), null, 0);?>
    <?php  $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->_loop = false;
 $_smarty_tpl->tpl_vars['INDEX'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['FINAL_DETAILS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->key => $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value){
$_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->_loop = true;
 $_smarty_tpl->tpl_vars['INDEX']->value = $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->key;
?>
        <?php $_smarty_tpl->tpl_vars['tagid'] = new Smarty_variable($_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value['tagid'], null, 0);?>
        <?php if (in_array($_smarty_tpl->tpl_vars['tagid']->value,array_keys($_smarty_tpl->tpl_vars['ptagid']->value))){?>
            <?php $_smarty_tpl->createLocalArrayVariable('ptagid', null, 0);
$_smarty_tpl->tpl_vars['ptagid']->value[$_smarty_tpl->tpl_vars['tagid']->value] = $_smarty_tpl->tpl_vars['ptagid']->value[$_smarty_tpl->tpl_vars['tagid']->value]+1;?>
            <?php $_smarty_tpl->createLocalArrayVariable('nptagid', null, 0);
$_smarty_tpl->tpl_vars['nptagid']->value[$_smarty_tpl->tpl_vars['tagid']->value] = $_smarty_tpl->tpl_vars['nptagid']->value[$_smarty_tpl->tpl_vars['tagid']->value]+1;?>
        <?php }else{ ?>
            <?php $_smarty_tpl->createLocalArrayVariable('ptagid', null, 0);
$_smarty_tpl->tpl_vars['ptagid']->value[$_smarty_tpl->tpl_vars['tagid']->value] = 1;?>
            <?php $_smarty_tpl->createLocalArrayVariable('nptagid', null, 0);
$_smarty_tpl->tpl_vars['nptagid']->value[$_smarty_tpl->tpl_vars['tagid']->value] = 1;?>
        <?php }?>
    <?php } ?>

    <?php $_smarty_tpl->tpl_vars['PURH'] = new Smarty_variable(0, null, 0);?>
    <?php $_smarty_tpl->tpl_vars['CASTING'] = new Smarty_variable(0, null, 0);?>
    <?php $_smarty_tpl->tpl_vars['CASTINGARR'] = new Smarty_variable(array(), null, 0);?>
    <?php  $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->_loop = false;
 $_smarty_tpl->tpl_vars['INDEX'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['FINAL_DETAILS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->key => $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value){
$_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->_loop = true;
 $_smarty_tpl->tpl_vars['INDEX']->value = $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->key;
?>
        <?php if ($_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["thepackage"]!='--'&&$_smarty_tpl->tpl_vars['ptagid']->value[$_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value['tagid']]==$_smarty_tpl->tpl_vars['nptagid']->value[$_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value['tagid']]){?>
            <tr>
                <td colspan="<?php if ($_smarty_tpl->tpl_vars['FINAL_DETAILS']->value[0]['istyunweb']==1){?>13<?php }else{ ?>12<?php }?>"><div class="row-fluid text-center"><span class="label label-success"><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["thepackage"];?>
</span></div></td>

            </tr>
        <?php }?>
        <?php $_smarty_tpl->createLocalArrayVariable('ptagid', null, 0);
$_smarty_tpl->tpl_vars['ptagid']->value[$_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value['tagid']] = $_smarty_tpl->tpl_vars['ptagid']->value[$_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value['tagid']]-1;?>
	    <tr>
		    <td nowrap><div class="row-fluid"><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["productname"];?>
</div></td>
            <td nowrap><div class="row-fluid"><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["thepackage"];?>
</div></td>
            <td nowrap><div class="row-fluid"><span class="label <?php if ($_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["isextra"]=='否'){?>label-info<?php }else{ ?>label-success<?php }?>"><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["isextra"];?>
</span></div></td>
            <td nowrap><div class="row-fluid"><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["standardname"];?>
</div></td>
            <td nowrap><div class="row-fluid"><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["vendorname"];?>
</div></td>
            <td nowrap><div class="row-fluid"><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["supplier_contract_no"];?>
</div></td>

            <td nowrap><div class="row-fluid"><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["productnumber"];?>
</div></td>
            <td nowrap><div class="row-fluid"><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["agelife"];?>
</div></td>
            <td nowrap><div class="row-fluid"><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["costing"];?>
<?php $_smarty_tpl->tpl_vars['CASTING'] = new Smarty_variable($_smarty_tpl->tpl_vars['CASTING']->value+$_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["costing"], null, 0);?><?php $_smarty_tpl->tpl_vars['PURH'] = new Smarty_variable($_smarty_tpl->tpl_vars['PURH']->value+$_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["purchasemount"], null, 0);?><?php $_smarty_tpl->createLocalArrayVariable('CASTINGARR', null, 0);
$_smarty_tpl->tpl_vars['CASTINGARR']->value[$_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value['tagid']][] = $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["purchasemount"];?></div></td>
            <td nowrap><div class="row-fluid"><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["purchasemount"];?>
</div></td>
            <td class="hide" nowrap><div class="row-fluid"></div></td>
            <td class="hide" nowrap><div class="row-fluid"></div></td>
		    <td   nowrap><table><span style="overflow:hidden;"><textarea id="noteA<?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["tagid"];?>
E<?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["productid"];?>
"><?php echo decode_html($_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["productsolution"]);?>
</textarea></span></table></td>
	    	
			<td nowrap><span ><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["createtime"];?>
</span></td>
            <?php if ($_smarty_tpl->tpl_vars['FINAL_DETAILS']->value[0]['istyunweb']==1){?><td><b><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["opendate"];?>
~<?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["closedate"];?>
</b></td><?php }?>
	    </tr>
        <?php if ($_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["thepackage"]!='--'&&$_smarty_tpl->tpl_vars['ptagid']->value[$_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value['tagid']]==0){?>
        <tr class="success">
            <td nowrap><div class="row-fluid"></div></td>
            <td nowrap><div class="row-fluid"><span class="label label-info"><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["thepackage"];?>
</span></div></td>
            <td nowrap><div class="row-fluid"></div></td>
            <td nowrap><div class="row-fluid"></div></td>
            <td nowrap><div class="row-fluid"></div></td>
            <td nowrap><div class="row-fluid"></div></td>
            <td nowrap><div class="row-fluid"><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["productnumber"];?>
</div></td>
            <td nowrap><div class="row-fluid"><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["agelife"];?>
</div></td>
            <td nowrap><div class="row-fluid"><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["prealprice"];?>
</div></td>
            <td nowrap><div class="row-fluid"><?php echo number_format(array_sum($_smarty_tpl->tpl_vars['CASTINGARR']->value[$_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value['tagid']]),2);?>
</div></td>
            <td class="hide" nowrap><div class="row-fluid"></div></td>
            <td class="hide" nowrap><div class="row-fluid"></div></td>
            <td  ></td>
            <td></td>
            <?php if ($_smarty_tpl->tpl_vars['FINAL_DETAILS']->value[0]['istyunweb']==1){?><td></td><?php }?>
        </tr>
        <?php }?>

	<?php } ?>
       <tr class="success">
            <td nowrap><div class="row-fluid"></div></td>
            <td nowrap><div class="row-fluid"></div></td>
            <td nowrap><div class="row-fluid"></div></td>
            <td nowrap><div class="row-fluid"></div></td>
            <td nowrap><div class="row-fluid"></div></td>
            <td nowrap><div class="row-fluid"></div></td>
            <td nowrap><div class="row-fluid"></div></td>
            <td nowrap><div class="row-fluid"><span class="label label-info">合计</span></div></div></td>
            <td nowrap><div class="row-fluid"><?php echo number_format($_smarty_tpl->tpl_vars['CASTING']->value,2);?>
</div></td>
            <td nowrap><div class="row-fluid"><?php echo number_format($_smarty_tpl->tpl_vars['PURH']->value,2);?>
</div></td>
            <?php $_smarty_tpl->tpl_vars['CASTINGPUAH'] = new Smarty_variable(0, null, 0);?>
            <?php $_smarty_tpl->tpl_vars['CASTINGPUAH'] = new Smarty_variable($_smarty_tpl->tpl_vars['PURH']->value+$_smarty_tpl->tpl_vars['CASTING']->value, null, 0);?> 
	    <td class="hide" nowrap><div class="row-fluid"></div></td>
	    <td class="hide" nowrap><div class="row-fluid"></div></td>
            <td nowrap><span class="label label-info">总计</span><?php echo number_format($_smarty_tpl->tpl_vars['CASTINGPUAH']->value,2);?>
</div></td> 
            <td></td>
        </tr>
	 </tbody>
	</table>

	<!--<table class="table table-bordered">
	    <tr>
		<td width="83%">
		    <div class="pull-right">
			<b><?php echo vtranslate('LBL_ITEMS_TOTAL',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</b>
		    </div>
		</td>
		<td>
		    <span class="pull-right">
			<b><?php echo $_smarty_tpl->tpl_vars['FINAL_DETAILS']->value["hdnSubTotal"];?>
</b>
		    </span>
		</td>
	    </tr>
	   
	</table>-->

<script>

    $(document).ready(function(){
        function loadCkEditor(element) {
            var ue = UE.getEditor(element, {
                toolbars: [['fullscreen']],
                autoFloatEnabled: false,
                initialFrameWidth: '100%',
                initialFrameHeight:100,
                autoHeightEnabled: true,
                autoFloatEnabled: false,
                elementPathEnabled: false,
                wordCount: false,
                autoHeightEnabled:false,
                enableAutoSave:false
                //readonly: true
            });
        }
        

        <?php  $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->_loop = false;
 $_smarty_tpl->tpl_vars['INDEX'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['FINAL_DETAILS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->key => $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value){
$_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->_loop = true;
 $_smarty_tpl->tpl_vars['INDEX']->value = $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->key;
?>
        UE.delEditor('noteA<?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["tagid"];?>
E<?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["productid"];?>
');
        
        <?php } ?>
        <?php  $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->_loop = false;
 $_smarty_tpl->tpl_vars['INDEX'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['FINAL_DETAILS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->key => $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value){
$_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->_loop = true;
 $_smarty_tpl->tpl_vars['INDEX']->value = $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->key;
?>
        loadCkEditor('noteA<?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["tagid"];?>
E<?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["productid"];?>
');
        
        <?php } ?>
        

    });



</script>
<?php }} ?>