<?php /* Smarty version Smarty-3.1.7, created on 2022-02-15 16:27:48
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\OrderChargeback\LineItemsDetail.tpl" */ ?>
<?php /*%%SmartyHeaderCode:21637620b64041c7eb2-87031242%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '54bd49bf4d1877fe9a89a7873f8f18df3cb8580b' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\OrderChargeback\\LineItemsDetail.tpl',
      1 => 1523874465,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21637620b64041c7eb2-87031242',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'RELATED_PRODUCTS' => 0,
    'LINE_ITEM_DETAIL' => 0,
    'TOTALCOSTING' => 0,
    'C_NEWPORDUCT' => 0,
    'data' => 0,
    'C_OLDPRODUCT' => 0,
    'ndata' => 0,
    'INVOICESTATUS' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620b640421a99',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620b640421a99')) {function content_620b640421a99($_smarty_tpl) {?>
<?php $_smarty_tpl->tpl_vars['FINAL_DETAILS'] = new Smarty_variable($_smarty_tpl->tpl_vars['RELATED_PRODUCTS']->value[1]['final_details'], null, 0);?>
<table class="table table-bordered mergeTables detailview-table ">
    <thead>
	    <th colspan="11" class="detailViewBlockHeader">退款产品明细</th>
	</thead>
	<tbody>
    <tr>
		<td><b>产品名称</b></td><td><b>所属套餐</b></td><td><b>数量</b></td><td><b>年限(月)</b></td><td><b>市场价格(￥)</b><td><b>人力成本(￥)</b></td><td><b>外采成本(￥)</b><td><b>审核人</b></td><td><b>成本明细</b><td><b>修改时间</b></td>
    </tr>
    <?php  $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->_loop = false;
 $_smarty_tpl->tpl_vars['INDEX'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['RELATED_PRODUCTS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->key => $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value){
$_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->_loop = true;
 $_smarty_tpl->tpl_vars['INDEX']->value = $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->key;
?>
	<tr>
        <td><div class="row-fluid"><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["productname"];?>
</div></td>
        <td><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["productcomboname"];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["productnumber"];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["agelife"];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["marketprice"];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["costing"];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["purchasemount"];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["checkproductuser"];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["remark"];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['LINE_ITEM_DETAIL']->value["createtime"];?>
</td>
	 </tr>
	    <?php } ?>
	    <tr>
            <td colspan="5">&nbsp;</td>
            <td><span class="pull-left"><b><?php echo $_smarty_tpl->tpl_vars['TOTALCOSTING']->value['totalcosting'];?>
 </b>&nbsp;</span>&nbsp;</td>
            <td><span class="pull-left"><b><?php echo $_smarty_tpl->tpl_vars['TOTALCOSTING']->value['totalpurchasemount'];?>
 </b>&nbsp;</span>&nbsp;</td>
		    <td><span class="pull-right"><b>总成本(￥)</b></span></td>
		    <td colspan="2"><span class="pull-left"><b><?php echo $_smarty_tpl->tpl_vars['TOTALCOSTING']->value['TOTALCOST'];?>
 </b>&nbsp;</span>&nbsp;</td>
	    </tr>
	    </tbody>
	</table>
<br>
<br>
<table class="table table-bordered blockContainer lineItemTable tableproduct detailview-table" id="lineItemTab">
    <thead>
    <tr>
        <th colspan="5">
            <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
            <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
            &nbsp;&nbsp;退单原工单明细
        </th>
    </tr>
    </thead>
    <tbody>
    <tr id="insertproduct">
        <td><b>工单编号</b></td>
        <td><b>主题</b></td>
        <td><b>流程节点</b></td>
        <td><b>状态</b></td>
        <td><b>负责人</b></td>
    </tr>
        <?php if (!empty($_smarty_tpl->tpl_vars['C_NEWPORDUCT']->value['salesorderlist'])){?>
            <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data']->_loop = false;
 $_smarty_tpl->tpl_vars['row_no'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['C_NEWPORDUCT']->value['salesorderlist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
$_smarty_tpl->tpl_vars['data']->_loop = true;
 $_smarty_tpl->tpl_vars['row_no']->value = $_smarty_tpl->tpl_vars['data']->key;
?>
                <?php if (in_array($_smarty_tpl->tpl_vars['data']->value['salesorderid'],$_smarty_tpl->tpl_vars['C_OLDPRODUCT']->value['salesoldorderid'])){?>
                <tr class="removetr"><td><?php echo $_smarty_tpl->tpl_vars['data']->value['salesorder_no'];?>

                </td><td><?php echo $_smarty_tpl->tpl_vars['data']->value['subject'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['data']->value['workflowsnode'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['data']->value['modulestatus'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['data']->value['salesorderowner'];?>
</td>
                <?php if (!empty($_smarty_tpl->tpl_vars['data']->value['productlist'])){?>
                    <tr class="removetr">
                        <td colspan="7">
                            <table class="table table-striped blockContainer lineItemTable tableproduct detailview-table"><thead><tr><th><img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="1499" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="1499" style="display: inline;">产品明称</th><th>所属套餐</th><th>数量</th><th>年限(月)</th><th>市场价格</th><th>人力成本</th><th>外采成本</th></tr></thead><tbody>
                        <?php  $_smarty_tpl->tpl_vars['ndata'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['ndata']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['data']->value['productlist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['ndata']->key => $_smarty_tpl->tpl_vars['ndata']->value){
$_smarty_tpl->tpl_vars['ndata']->_loop = true;
?>
                            <?php if (in_array($_smarty_tpl->tpl_vars['ndata']->value['salesorderproductsrelid'],$_smarty_tpl->tpl_vars['C_OLDPRODUCT']->value['salesoldorderid'])){?>
                            <tr><td><?php echo $_smarty_tpl->tpl_vars['ndata']->value['productname'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['ndata']->value['productcomboname'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['ndata']->value['productnumber'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['ndata']->value['agelife'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['ndata']->value['realprice'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['ndata']->value['costing'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['ndata']->value['purchasemount'];?>
</td></tr>
                            <?php }?>
                        <?php } ?>
                        </tbody></table></td></tr>
                <?php }?>
                <?php }?>
            <?php } ?>
        <?php }?>

    </tbody>
</table>
<br>
<table class="table table-bordered blockContainer lineItemTable tableproduct detailview-table">
    <thead>
    <tr>
        <th colspan="8">
            <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
            <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
            &nbsp;&nbsp;退款申请发票明细
        </th>
    </tr>
    </thead>
    <tbody>
    <tr id="insertinvoice">
        <td><b>发票代码</b></td>
        <td><b>发票号码</b></td>
        <td><b>开票日期</b></td>
        <td><b>发票金额</b></td>
        <td><b>发票内容</b></td>
        <td><b>处理状态</b></td>
        <td><b>操作处理</b></td>
        <td><b>处理日期</b></td>
    </tr>

        <?php if (!empty($_smarty_tpl->tpl_vars['C_NEWPORDUCT']->value['invoicelist'])){?>
            <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data']->_loop = false;
 $_smarty_tpl->tpl_vars['row_no'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['C_NEWPORDUCT']->value['invoicelist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
$_smarty_tpl->tpl_vars['data']->_loop = true;
 $_smarty_tpl->tpl_vars['row_no']->value = $_smarty_tpl->tpl_vars['data']->key;
?>
                <?php if (in_array($_smarty_tpl->tpl_vars['data']->value['invoiceextendid'],$_smarty_tpl->tpl_vars['C_OLDPRODUCT']->value['invoiceoldorderid'])){?>
                <?php if ($_smarty_tpl->tpl_vars['data']->value['invoicestatus']=='redinvoice'){?>
                    <?php $_smarty_tpl->tpl_vars['INVOICESTATUS'] = new Smarty_variable('<span class="label btn-danger">红冲</span>', null, 0);?>
                <?php }elseif($_smarty_tpl->tpl_vars['data']->value['invoicestatus']=='tovoid'){?>
                    <?php $_smarty_tpl->tpl_vars['INVOICESTATUS'] = new Smarty_variable('<span class="label btn-inverse">作废</span>', null, 0);?>
                    <?php }else{ ?>
                    <?php $_smarty_tpl->tpl_vars['INVOICESTATUS'] = new Smarty_variable('<span class="label btn-success">正常</span>', null, 0);?>
                    <?php }?>
                <tr class="removetr"><td><?php echo $_smarty_tpl->tpl_vars['data']->value['invoice_noextend'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['data']->value['invoicecodeextend'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['data']->value['billingtimeextend'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['data']->value['totalandtaxextend'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['data']->value['commoditynameextend'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['INVOICESTATUS']->value;?>
</td><td><?php echo $_smarty_tpl->tpl_vars['data']->value['operator'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['data']->value['operatortime'];?>
</td></tr>
                <?php }?>
            <?php } ?>
        <?php }?>

    </tbody>
</table>
<br>
<table class="table table-bordered blockContainer lineItemTable tableproduct detailview-table">
    <thead>
    <tr>
        <th colspan="7">
            <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
            <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
            &nbsp;&nbsp;回款明细
        </th>
    </tr>
    </thead>
    <tbody>
    <tr id="insertreceivepay">
        <td><b>公司账号</b></td>
        <td><b>汇款抬头</b></td>
        <td><b>入账日期</b></td>
        <td><b>原币金额</b></td>
        <td><b>汇率</b></td>
        <td><b>金额</b></td>
        <td><b>额外成本</b></td>
    </tr>

        <?php if (!empty($_smarty_tpl->tpl_vars['C_NEWPORDUCT']->value['receivepayments'])){?>
            <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data']->_loop = false;
 $_smarty_tpl->tpl_vars['row_no'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['C_NEWPORDUCT']->value['receivepayments']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
$_smarty_tpl->tpl_vars['data']->_loop = true;
 $_smarty_tpl->tpl_vars['row_no']->value = $_smarty_tpl->tpl_vars['data']->key;
?>
                <tr class="removetr"><td><?php echo $_smarty_tpl->tpl_vars['data']->value['owncompany'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['data']->value['paytitle'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['data']->value['reality_date'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['data']->value['standardmoney'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['data']->value['exchangerate'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['data']->value['unit_price'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['data']->value['sumextra_price'];?>
</td></tr>
            <?php } ?>
        <?php }?>

    </tbody>
</table><?php }} ?>