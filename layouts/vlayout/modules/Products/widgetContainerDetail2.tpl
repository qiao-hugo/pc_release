<strip>
    <div>
        {if  $CURRENTSTAND|@count gt 0}
            <table id="polytypetable" class="table table-bordered table-striped blockContainer showInlineTable ">
                <thead><tr>
                    <th class="blockHeader" colspan="5">多规格</th></tr></thead>
                <tbody>
                <tr><td>规格名称</td><td>单价</td><td>成本价格</td><td>规则</td></tr>
                {foreach key=CURRENTSTAND_LABEL item=CURRENTSTAND_FIELD from=$CURRENTSTAND}
                    <tr>
                        <td><span class="redColor">*</span>{$CURRENTSTAND_FIELD['standardname']}</td>
                        <td><span class="redColor">*</span>¥{$CURRENTSTAND_FIELD['singleprice']} </td>
                        <td><span class="redColor">*</span>¥{$CURRENTSTAND_FIELD['realprice']} </td>
                        <td>{$CURRENTSTAND_FIELD['standardvalue']}</td>
                {/foreach}
                </tbody> </table>
        {/if}
        {if  $PACKAGE|@count gt 0}
            <table id="istable" class="table table-bordered table-striped blockContainer showInlineTable ">
                <thead><tr><th class="blockHeader" colspan="5">套餐信息</th></tr></thead>
                <tbody> <tr><td>产品名称</td><td>默认规格</td><td>可选规格</td><td>默认成本</td><td>年限</td></tr>
                {foreach key=PACKAGE_LABEL item=PACKAGE_FIELD from=$PACKAGE}
                    <tr>
                        <td>
                                {foreach key=ALLPRODUCTS_LABEL item=ALLPRODUCTS_FIELD from=$ALLPRODUCTS}
                                    {if $PACKAGE_FIELD['packproductid'] eq $ALLPRODUCTS_FIELD['productid']} {$ALLPRODUCTS_FIELD['productname']}{/if}
                                {/foreach}
                        </td>
                        <td>
                                {foreach key=ALLSTAND_LABEL item=ALLSTAND_FIELD from=$ALLSTAND}
                                    {if $ALLSTAND_FIELD['productid'] eq $PACKAGE_FIELD['packproductid']}
                                        {if $ALLSTAND_FIELD['standardid'] eq $PACKAGE_FIELD['defaultstand']} {$ALLSTAND_FIELD['standardname']}{/if}
                                    {/if}
                                {/foreach}
                        </td>
                        <td>
                                {foreach key=ALLSTAND_LABEL item=ALLSTAND_FIELD from=$ALLSTAND}
                                    {if $ALLSTAND_FIELD['productid'] eq $PACKAGE_FIELD['packproductid']}
                                        {if in_array($ALLSTAND_FIELD['standardid'],$PACKAGE_FIELD['choosablestand'])}{$ALLSTAND_FIELD['standardname']},{/if}
                                    {/if}
                                {/foreach}
                        </td>
                        <td>
                            <span class="redColor">*</span>
                            <span>¥{$PACKAGE_FIELD['defaultcost']}</span>
                        </td>
                        <td>
                            <span class="redColor">*</span>
                            {$PACKAGE_FIELD['years']}年
                        </td>
                    </tr>
                {/foreach}
                </tbody> </table>
        {/if}
    </div>
</strip>