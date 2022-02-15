{strip}
    <div style=" position:absolute;top:25%;right:60%;border:2px solid red;width:260px;text-align:center;color:red;border-radius:5px;font-size:30px;font-weight:bold;">若该回款不是你的，请点击放弃！不要瞎匹配合同,如错误匹配,将承担相关的责任</div>
    <div class="row-fluid " id="c">
    <center><h3 style="color:red;">只有匹配完成所有回款才能进行别的相关操作,如果没有你所负责回款，请点击放弃回款按钮</h3></center>
	<table class="table listViewEntriesTable" style="width:100%" id="table_match">
		<thead>
			<tr class="listViewHeaders success" >
                <th>公司账号</th>
                <th>汇款抬头</th>
				<th>回款金额</th>
				<th>代付款金额</th>
				<th>入账日期</th>
				<th>合同编号</th>
				<th>回款类型</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
        {assign var=no_repeat value=array()}
				{foreach item=item key = key from=$DATA}
					{if !in_array($item['receivedpaymentsid'],$no_repeat)}
                        {$no_repeat[] = $item['receivedpaymentsid']}
                        <tr>
                            <input type="hidden" value={$item['receivedpaymentsid']} class='receivepayments'>
                            <input type="hidden" value={$item['unit_price']} class='total'>
                            <input type="hidden" value={$item['staypaymentidid']} class='staypaymentidid'>
                        <td>{$item['owncompany']}</td>
                        <td>{$item['paytitle']}</td>
{*                        <td>{$item['receivementcurrencytype']}:{$item['unit_price']}</td>*}
                        <td>人民币:{$item['unit_price']}</td>
                         <td>
                             <input type="hidden" value="{$item['staypaymenttype']}" name="staypaymenttype[]" class="staypaymenttype"/>
{*                             {if !empty($STAYPAYMENTJINE) and ($item['staypaymentid']=='fixation')}*}
                             {if $item['matchtype']=='staypayment'}
                                 <select class="chzn-select" name="staypaymentjine[]">
                                     <option value="">请选择代付款金额</option>
                                     {foreach item = item3 key = key3 from=$STAYPAYMENTJINE}
                                         {if $item['receivedpaymentsid']==$key3}
                                             {foreach  item = item4  key = key4 from=$item3}
                                                  {if $item4['staypaymenttype'] eq 'fixation'}
                                                      <option data-contract_no="{$item4['contract_no']}" value="{$key4}">{$item4['staypaymentjine']} (合同:{$item4['contract_no']})</option>
                                                  {else}
                                                      <option data-contract_no="{$item4['contract_no']}" value="{$key4}">非固定代付款(合同:{$item4['contract_no']})</option>
                                                  {/if}
                                             {/foreach}
                                         {/if}
                                     {/foreach}
                                 </select>
                             {/if}
                         </td>
                        <td>{$item['reality_date']}<input type="hidden" name="shareuser[]" value="{$item['shareuser']}"></td>
                        <td>
                            <select class="chzn-select" name="contractid[]">
                                <option value="">请选择合同</option>
                                {assign var=TempArray value=[]}
                                {foreach item = item1 key = key2 from=$DATA}
                                    {if ($item['maybe_account'] eq $item1['sc_related_to'])  && !in_array($item1['servicecontractsid'],$TempArray)}
                                    <option value="{$item1['servicecontractsid']}" data-staypaymentid="{$item1['staypaymentidid']}" data-module="{$item1['modulename']}" data-realoperate="{setoperate($item1['servicecontractsid'],{$item1['modulename']})}">{$item1['contract_no']}</option>
                                        {$TempArray[]=$item1['servicecontractsid']}
                                    {/if}
{*                                    {if $item['matchtype1'] eq 1 && !in_array($item1['servicecontractsid'],$TempArray)}*}
{*                                        *}{*走打款人全称*}
{*                                        <option value="{$item1['servicecontractsid']}"  data-staypaymentid="{$item1['staypaymentidid']}" data-module="{$item1['modulename']}" data-realoperate="{setoperate($item1['servicecontractsid'],{$item1['modulename']})}">{$item1['contract_no']}</option>*}
{*                                        {$TempArray[]=$item1['servicecontractsid']}*}
{*                                    {elseif $item['matchtype2'] eq 1 && !in_array($item1['servicecontractsid'],$TempArray)&&(str_replace(' ','',$item['paytitle']) eq str_replace(' ','',$item1['staypaymentname']))}*}
{*                                        <option value="{$item1['servicecontractsid']}"  data-staypaymentid="{$item1['staypaymentidid']}" data-module="{$item1['modulename']}" data-realoperate="{setoperate($item1['servicecontractsid'],{$item1['modulename']})}">{$item1['contract_no']}</option>*}
{*                                        {$TempArray[]=$item1['servicecontractsid']}*}
{*                                    {/if}*}
                                {/foreach}
                            </select>
                        </td>
                            <td>

                                <select class="chzn-select" name="receivedstatus[]">
                                    {if $item['receivedstatus'] neq 'RebateAmount'}
                                        <option value="0" selected>请选择</option>
                                        <option value="normal">正常业务款</option>
                                        <option value="deposit">保证金</option>
                                    {else}
                                        <option value="RebateAmount">返点款</option>
                                    {/if}
                                </select>
                            </td>
                            <td>
                                <button class="option">匹配回款</button>
                                <button class="throw">放弃回款</button>
                                <button class="split" data-id="" data-rid="{$item['receivedpaymentsid']}">拆分回款</button>
                            </td>
                        </tr>
                    {/if}
				{/foreach}
		</tbody>
	</table>
    <div class="info"></div>
    <div class="widgetContainer_receivehistory"></div>
    <div id="servicecontract"></div>
    
        <table class="table listViewEntriesTable" id="isExistServicecontract" style="width:100%;display:none;" id="table_match">
            <thead>
               <tr class="listViewHeaders success">
                   <th><span style="color:red;">合同信息确认栏</span></th>
               </tr>
            </thead>
            <tbody>
               <tr>
                   <td>
                       <div style="font-weight:bold;font-size: 16px">
                           <p>备注：1.请确认以上合同是否为当前回款需要匹配的目标合同</p>
                           <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2.该合同签订人、提单人、总额、客户等合同信息是否正确，如果不正确，请先放弃回款匹配联系财务修改后再行匹配</p>
                           <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3.请确认合同分成信息中的所属公司、业绩所属人及比例是否正确，如果不正确，请先放弃回款匹配，先到服务合同详情自行申请修改，修改完成后再去匹配回款</p>
                       </div>
                       <div class="checkbox">
                           <label>
                               <input type="checkbox" id="isHasChecked"  name="isHasChecked"   value="1" > <span style="color:#1DA41D;font-weight: bold;">我已确认当前合同信息及业绩分成信息无误</span>
                           </label>
                       </div>
                   </td>
               </tr>
            </tbody>
        </table>
</div>
{/strip}
