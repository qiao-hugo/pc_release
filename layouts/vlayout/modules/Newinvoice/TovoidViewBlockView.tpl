{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*<!--去除双击编辑-->
 ********************************************************************************/
-->*}
{strip}

<div class='editViewContainer container-fluid'>
	<form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php" enctype="multipart/form-data">
		<input type="hidden" name="module" value="{$MODULE}" />
		<input type="hidden" name="action" value="Save" />
		<input type="hidden" name="record" value="{$RECORD_ID}" />

		{if !empty($NEWINVOICEDATA)}
	    {foreach key=KEYINDEX item=D from=$NEWINVOICEDATA}
	    <table class="table table-bordered blockContainer newinvoicerayment_tab detailview-table" data-num="{$KEYINDEX + 1}">
	        <thead>
	        <tr>
	            <th class="blockHeader" colspan="4">
	                &nbsp;&nbsp;关联回款信息(申请人录入)[{$KEYINDEX + 1}] <b class="pull-right"></b>
	            </th>
	        </tr>
	        </thead>
	        <tbody>
	        <tr>
	            <td class="fieldLabel medium">
	                <label class="muted pull-right marginRight10px"> 回款信息</label>
	                <input type="hidden" name="updateii[]" value="{$D['newinvoiceraymentid']}">
	            </td>
	            <td class="fieldValue medium">
	                <div class="row-fluid">
	                    <span class="span10">
	                    {$D['paytitle']}
	                    </span>
	                </div>
	            </td>
	            <td class="fieldLabel medium">
	                <label class="muted pull-right marginRight10px"> 所属合同</label>
	            </td>
	            <td class="fieldValue medium">
	                <div class="row-fluid">
	                    <span class="span10">{$D['contract_no']}</span>
	                </div>
	            </td>
	        </tr>
	        <tr>
	            <td class="fieldLabel medium">
	                <label class="muted pull-right marginRight10px"> 入账金额</label>
	            </td>
	            <td class="fieldValue medium">
	                <div class="row-fluid">
	                    <span class="span10">{$D['total']}</span>
	                </div>
	            </td>
	            <td class="fieldLabel medium">
	                <label class="muted pull-right marginRight10px"> 入账日期</label>
	            </td>
	            <td class="fieldValue medium">
	                <div class="row-fluid">
	                    <span class="span10">{$D['arrivaldate']}</span>
	                </div>
	            </td>
	        </tr>
	        <tr>
	            <td class="fieldLabel medium">
	                <label class="muted pull-right marginRight10px"> 可开发票金额</label>
	            </td>
	            <td class="fieldValue medium">
	                <div class="row-fluid">
	                    <span class="span10">{$D['allowinvoicetotal']}</span>
	                </div>
	            </td>
	            <td class="fieldLabel medium">
	                <label class="muted pull-right marginRight10px"> 使用开票金额</label>
	            </td>
	            <td class="fieldValue medium">
	                <div class="row-fluid">
	                    <span class="span10">{$D['invoicetotal']}</span>
	                </div>
	            </td>
	        </tr>
	        <tr>
	            <td class="fieldLabel medium">
	                <label class="muted pull-right marginRight10px"> 开票内容</label>
	            </td>
	            <td class="fieldValue medium">
	                <div class="row-fluid">
	                    <span class="span10">{$D['invoicecontent']}</span>
	                </div>
	            </td>
	            <td class="fieldLabel medium">
	                <label class="muted pull-right marginRight10px"> </label>
	            </td>
	            <td class="fieldValue medium">
	                <div class="row-fluid">
	                    <span class="span10"></span>
	                </div>
	            </td>
	        </tr>
	        <tr>
	            <td class="fieldLabel medium">
	                <label class="muted pull-right marginRight10px"> 备注</label>
	            </td>
	            <td class="fieldValue medium" colspan="3">
	                <div class="row-fluid">
	                    <span class="span10">{$D['remarks']}</span>
	                </div>
	            </td>
	        </tr>
	        </tbody>
	    </table>
	    {/foreach}
	    {/if}
		
	</form>
</div>
    

    <script src="/libraries/jSignature/jSignature.min.noconflict.js"></script>
{/strip}