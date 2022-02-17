{assign var=FINAL_DETAILS value=$Product}
<table class="table table-bordered mergeTables detailview-table">
    <thead>
    <th colspan="{if $FINAL_DETAILS[0]['istyunweb'] eq 1}13{else}12{/if}" class="detailViewBlockHeader">
	{vtranslate('LBL_ITEM_DETAILS', $MODULE_NAME)}
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
	    	<td class="hide" nowrap><b>{*市场价*}</b></td>
	    	<td class="hide" nowrap><b>{*合同价*}</b></td>
	    	<td nowrap><b>备注</b></td>
	    	{*<td><b>规则</b></td>*}

	        <td><b>创建时间</b></td>
	        {if $FINAL_DETAILS[0]['istyunweb'] eq 1}<td><b>有效期间</b></td>{/if}
	    </tr>
        {assign var=ptagid value=array()}
        {assign var=nptagid value=array()}
    {foreach key=INDEX item=LINE_ITEM_DETAIL from=$FINAL_DETAILS}
        {assign var=tagid value=$LINE_ITEM_DETAIL['tagid']}
        {if in_array($tagid,array_keys($ptagid))}
            {$ptagid[$tagid]=$ptagid[$tagid]+1}
            {$nptagid[$tagid]=$nptagid[$tagid]+1}
        {else}
            {$ptagid[$tagid]=1}
            {$nptagid[$tagid]=1}
        {/if}
    {/foreach}

    {assign var=PURH value=0}
    {assign var=CASTING value=0}
    {assign var=CASTINGARR value=[]}
    {foreach key=INDEX item=LINE_ITEM_DETAIL from=$FINAL_DETAILS}
        {if $LINE_ITEM_DETAIL["thepackage"] neq '--' && $ptagid[$LINE_ITEM_DETAIL['tagid']] eq $nptagid[$LINE_ITEM_DETAIL['tagid']] }
            <tr>
                <td colspan="{if $FINAL_DETAILS[0]['istyunweb'] eq 1}13{else}12{/if}"><div class="row-fluid text-center"><span class="label label-success">{$LINE_ITEM_DETAIL["thepackage"]}</span></div></td>

            </tr>
        {/if}
        {$ptagid[$LINE_ITEM_DETAIL['tagid']]=$ptagid[$LINE_ITEM_DETAIL['tagid']]-1}
	    <tr>
		    <td nowrap><div class="row-fluid">{$LINE_ITEM_DETAIL["productname"]}</div></td>
            <td nowrap><div class="row-fluid">{$LINE_ITEM_DETAIL["thepackage"]}</div></td>
            <td nowrap><div class="row-fluid"><span class="label {if $LINE_ITEM_DETAIL["isextra"] eq '否'}label-info{else}label-success{/if}">{$LINE_ITEM_DETAIL["isextra"]}</span></div></td>
            <td nowrap><div class="row-fluid">{$LINE_ITEM_DETAIL["standardname"]}</div></td>
            <td nowrap><div class="row-fluid">{$LINE_ITEM_DETAIL["vendorname"]}</div></td>
            <td nowrap><div class="row-fluid">{$LINE_ITEM_DETAIL["supplier_contract_no"]}</div></td>

            <td nowrap><div class="row-fluid">{$LINE_ITEM_DETAIL["productnumber"]}</div></td>
            <td nowrap><div class="row-fluid">{$LINE_ITEM_DETAIL["agelife"]}</div></td>
            <td nowrap><div class="row-fluid">{$LINE_ITEM_DETAIL["costing"]}{$CASTING=$CASTING+$LINE_ITEM_DETAIL["costing"]}{$PURH=$PURH+$LINE_ITEM_DETAIL["purchasemount"]}{$CASTINGARR[$LINE_ITEM_DETAIL['tagid']][]=$LINE_ITEM_DETAIL["purchasemount"]}</div></td>
            <td nowrap><div class="row-fluid">{*{if $LINE_ITEM_DETAIL["thepackage"] eq '--'}*}{$LINE_ITEM_DETAIL["purchasemount"]}{*{/if}*}</div></td>
            <td class="hide" nowrap><div class="row-fluid">{*{if $LINE_ITEM_DETAIL["thepackage"] eq '--'}{$LINE_ITEM_DETAIL["marketprice"]}{/if}*}</div></td>
            <td class="hide" nowrap><div class="row-fluid">{*{if $LINE_ITEM_DETAIL["thepackage"] eq '--'}{$LINE_ITEM_DETAIL["realmarketprice"]}{/if}*}</div></td>
		    <td   nowrap><table><span style="overflow:hidden;"><textarea id="noteA{$LINE_ITEM_DETAIL["tagid"]}E{$LINE_ITEM_DETAIL["productid"]}">{$LINE_ITEM_DETAIL["productsolution"]|decode_html}</textarea></span></table></td>
	    	{*<td><span ><textarea id="noteB{$LINE_ITEM_DETAIL["productid"]}">{$LINE_ITEM_DETAIL["producttext"]}</textarea></span></td>*}
			<td nowrap><span >{$LINE_ITEM_DETAIL["createtime"]}</span></td>
            {if $FINAL_DETAILS[0]['istyunweb'] eq 1}<td><b>{$LINE_ITEM_DETAIL["opendate"]}~{$LINE_ITEM_DETAIL["closedate"]}</b></td>{/if}
	    </tr>
        {if $LINE_ITEM_DETAIL["thepackage"] neq '--' && $ptagid[$LINE_ITEM_DETAIL['tagid']] eq 0}
        <tr class="success">
            <td nowrap><div class="row-fluid"></div></td>
            <td nowrap><div class="row-fluid"><span class="label label-info">{$LINE_ITEM_DETAIL["thepackage"]}</span></div></td>
            <td nowrap><div class="row-fluid"></div></td>
            <td nowrap><div class="row-fluid"></div></td>
            <td nowrap><div class="row-fluid"></div></td>
            <td nowrap><div class="row-fluid"></div></td>
            <td nowrap><div class="row-fluid">{$LINE_ITEM_DETAIL["productnumber"]}</div></td>
            <td nowrap><div class="row-fluid">{$LINE_ITEM_DETAIL["agelife"]}</div></td>
            <td nowrap><div class="row-fluid">{$LINE_ITEM_DETAIL["prealprice"]}</div></td>
            <td nowrap><div class="row-fluid">{number_format(array_sum($CASTINGARR[$LINE_ITEM_DETAIL['tagid']]),2)}</div></td>
            <td class="hide" nowrap><div class="row-fluid">{*{$LINE_ITEM_DETAIL["punit_price"]}*}</div></td>
            <td class="hide" nowrap><div class="row-fluid">{*{$LINE_ITEM_DETAIL["pmarketprice"]}*}</div></td>
            <td  ></td>
            <td></td>
            {if $FINAL_DETAILS[0]['istyunweb'] eq 1}<td></td>{/if}
        </tr>
        {/if}

	{/foreach}
       <tr class="success">
            <td nowrap><div class="row-fluid"></div></td>
            <td nowrap><div class="row-fluid"></div></td>
            <td nowrap><div class="row-fluid"></div></td>
            <td nowrap><div class="row-fluid"></div></td>
            <td nowrap><div class="row-fluid"></div></td>
            <td nowrap><div class="row-fluid"></div></td>
            <td nowrap><div class="row-fluid"></div></td>
            <td nowrap><div class="row-fluid"><span class="label label-info">合计</span></div></div></td>
            <td nowrap><div class="row-fluid">{number_format($CASTING,2)}</div></td>
            <td nowrap><div class="row-fluid">{number_format($PURH,2)}</div></td>
            {assign var=CASTINGPUAH value=0}
            {$CASTINGPUAH=$PURH+$CASTING} 
	    <td class="hide" nowrap><div class="row-fluid"></div></td>
	    <td class="hide" nowrap><div class="row-fluid"></div></td>
            <td nowrap><span class="label label-info">总计</span>{number_format($CASTINGPUAH,2)}</div></td> 
            <td></td>
        </tr>
	 </tbody>
	</table>

	<!--<table class="table table-bordered">
	    <tr>
		<td width="83%">
		    <div class="pull-right">
			<b>{vtranslate('LBL_ITEMS_TOTAL',$MODULE_NAME)}</b>
		    </div>
		</td>
		<td>
		    <span class="pull-right">
			<b>{$FINAL_DETAILS["hdnSubTotal"]}</b>
		    </span>
		</td>
	    </tr>
	   
	</table>-->
{literal}
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
        {/literal}

        {foreach key=INDEX item=LINE_ITEM_DETAIL from=$FINAL_DETAILS}
        UE.delEditor('noteA{$LINE_ITEM_DETAIL["tagid"]}E{$LINE_ITEM_DETAIL["productid"]}');
        {*UE.delEditor('noteB{$LINE_ITEM_DETAIL["productid"]}');*}
        {/foreach}
        {foreach key=INDEX item=LINE_ITEM_DETAIL from=$FINAL_DETAILS}
        loadCkEditor('noteA{$LINE_ITEM_DETAIL["tagid"]}E{$LINE_ITEM_DETAIL["productid"]}');
        {*loadCkEditor('noteB{$LINE_ITEM_DETAIL["productid"]}');*}
        {/foreach}
        {literal}

    });



</script>
{/literal}