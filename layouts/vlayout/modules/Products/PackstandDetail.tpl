<strip>
	<div>
	{if  $CURRENTSTAND|@count gt 0}
	<table id="polytypetable" class="table table-bordered table-striped blockContainer showInlineTable ">
	 <thead><tr>
	 <th class="blockHeader" colspan="6">多规格</th></tr></thead>
	 <tbody>
	 <tr><td>默认规格</td><td>规格名称</td><td>单价</td><td>成本价格</td><td>规则</td><td>操作</td></tr>
{foreach key=CURRENTSTAND_LABEL item=CURRENTSTAND_FIELD from=$CURRENTSTAND}
	 <tr>
	 <td><input type="radio" name="polydefault" value="polydefault[]" ></td>
	 <td><span class="redColor">*</span><input type="text" data-validation-engine="validate[required]" name="standardname[]" value="{$CURRENTSTAND_FIELD['standardname']}"></td>
	 <td><span class="redColor">*</span><div class="input-prepend"> <span class="add-on">￥</span> <input name="singleprice[]" data-validation-engine="validate[required]" class="span8"  type="text" value="{$CURRENTSTAND_FIELD['singleprice']}" placeholder="请输入单价"> </div></td>
	 <td><span class="redColor">*</span><div class="input-prepend"> <span class="add-on">￥</span> <input name="realyprice[]" data-validation-engine="validate[required]" class="span8"  type="text"  value="{$CURRENTSTAND_FIELD['realprice']}"placeholder="请输入成本价格"> </div></td> 
	 <td><input type="text" name="rule[]" value="{$CURRENTSTAND_FIELD['standardvalue']}"></td>
     <td> <div class="btn-toolbar"> <span class="btn-group"> <button type="button" class="btn addstandard addButton">增加规格<i class="icon-plus"></i><strong></strong></button> <button type="button" class="btn removestandard">移除规格<i class="icon-minus"></i><strong></strong></button> </span> </div> </td> </tr>
	 {/foreach}
</tbody> </table>
{/if}
{if  $PACKAGE|@count gt 0}
<table id="istable" class="table table-bordered table-striped blockContainer showInlineTable ">
	<thead><tr><th class="blockHeader" colspan="6">套餐信息</th></tr></thead>
	<tbody> <tr><td>产品名称</td><td>默认规格</td><td>可选规格</td><td>默认成本</td><td>年限</td><td>操作</td></tr>
	{foreach key=PACKAGE_LABEL item=PACKAGE_FIELD from=$PACKAGE}
        <tr>
            <td>
                <select class="chzn-select" name="packagepro[]" data-validation-engine="validate[required]">
                    <option value="">选择一个选项</option>
                        {foreach key=ALLPRODUCTS_LABEL item=ALLPRODUCTS_FIELD from=$ALLPRODUCTS}
                            <option value="{$ALLPRODUCTS_FIELD['productid']}" {if $PACKAGE_FIELD['packproductid'] eq $ALLPRODUCTS_FIELD['productid']} selected {/if} data-picklistvalue= "{$ALLPRODUCTS_FIELD['productid']}">{$ALLPRODUCTS_FIELD['productname']}</option>
                        {/foreach}
                </select>
            </td>
            <td>
                <select name="defaultstand[]" class="chzn-select">
                    <option >请选择一个选项</option>
                    {foreach key=ALLSTAND_LABEL item=ALLSTAND_FIELD from=$ALLSTAND}
                        {if $ALLSTAND_FIELD['productid'] eq $PACKAGE_FIELD['packproductid']}
                            <option value="{$ALLSTAND_FIELD['standardid']}" {if $ALLSTAND_FIELD['standardid'] eq $PACKAGE_FIELD['defaultstand']} selected {/if} data-picklistvalue= "{$ALLSTAND_FIELD['standardid']}">{$ALLSTAND_FIELD['standardname']}</option>
                        {/if}
                    {/foreach}
                </select>
            </td>
            <td>
                <select class="chzn-select" multiple="true" name="choosablestand[{$PACKAGE_FIELD['packproductid']}][]">
                    {foreach key=ALLSTAND_LABEL item=ALLSTAND_FIELD from=$ALLSTAND}
                        {if $ALLSTAND_FIELD['productid'] eq $PACKAGE_FIELD['packproductid']}
                            <option value="{$ALLSTAND_FIELD['standardid']}" {if in_array($ALLSTAND_FIELD['standardid'],$PACKAGE_FIELD['choosablestand'])}   selected {/if} data-picklistvalue= "{$ALLSTAND_FIELD['standardid']}">{$ALLSTAND_FIELD['standardname']}</option>
                        {/if}
                    {/foreach}
                </select>
            </td>
            <td>
                <span class="redColor">*</span>
                <div class="input-prepend">
                    <span class="add-on">￥</span>
                    <input data-validation-engine="validate[required]" name="defaultcost[]" class="span8" type="text" value="{$PACKAGE_FIELD['defaultcost']}" placeholder="请输默认成本">
                </div>
            </td>
            <td><span class="redColor">*</span><input data-validation-engine="validate[required]" value="{$PACKAGE_FIELD['years']}" name="years[]" style="width:40px;" type="number">年 </td>
            <td><div class="btn-toolbar"> <span class="btn-group"> <button type="button" class="btn addproduct addButton">增加产品<i class="icon-plus"></i><strong></strong></button> <button type="button" class="btn removeproduct">移除产品<i class="icon-minus"></i><strong></strong></button> </span> </div> </td>
        </tr>
	{/foreach}
	 </tbody> </table>
{/if}
	 </div>
</strip>