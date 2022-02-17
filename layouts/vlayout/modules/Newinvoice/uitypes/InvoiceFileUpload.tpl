{strip}
<div class="fileUploadContainer" xmlns="http://www.w3.org/1999/html">
    {assign var=FIELD_VALUE value=explode('*|*',$FIELD_MODEL->get('fieldvalue'))}

<div class="upload {$FIELD_VALUE[0]}">
    <div style="display:inline-block;width:70px;height:30px;overflow: hidden;vertical-align: middle;"  title="文件名请勿包含空格"><div style="margin-top:-2px;">文件名请勿</div><div style="margin-top:-5px;">包含空格</div></div>
    <input type="button" id="uploadinvoiceButton" value="上传"  title="文件名请勿包含空格" />
{*    <input style="margin-left: 5px;color:#fff" type="button" id="downloadinvoiceButton" onclick="down()" value="下载"  title="文件名请勿包含空格" />*}
    <button type="button" id="downloadinvoiceButton" onclick="down()"  style="margin-left: 5px;color:#fff">下载</button>
    <div style="display:inline-block" id="fileallinvoice">
    {if !empty($FIELD_VALUE[0])}
        {foreach item=NEWFILD from=$FIELD_VALUE}
        {assign var=NFIELD_VALUE value=explode('##',$NEWFILD)}
        <span class="label file{$NFIELD_VALUE[1]}" style="margin-left:5px;">{$NFIELD_VALUE[0]}&nbsp;<b class="deletefile" data-class="file{$NFIELD_VALUE[1]}" data-id="{$NFIELD_VALUE[1]}" title="删除文件" style="display:inline-block;width:12px;height:12px;line-height:12px;text-align:center">x</b>&nbsp;</span>
        <input class="ke-input-text file{$NFIELD_VALUE[1]}" type="hidden" name="invoicefile[{$NFIELD_VALUE[1]}]" data-id="{$NFIELD_VALUE[1]}" id="invoicefile" value="{$NFIELD_VALUE[0]}" readonly="readonly" />
        <input class="file{$NFIELD_VALUE[1]}" type="hidden" name="attachmentsid[{$NFIELD_VALUE[1]}]" value="{$NFIELD_VALUE[1]}">
        {/foreach}
    {else}
        <input class="ke-input-text invoicefiledelete" type="hidden" name="invoicefile" id="invoicefile" value="" readonly="readonly" />
        <input class="invoicefiledelete" type="hidden" name="attachmentsid" value="">
    {/if}
    </div>
</div>
</div>
{/strip}
<script>
    $("#downloadinvoiceButton").addClass('btn btn-info');
    $("#downloadinvoiceButton").css("color","white");
    function down(){
        $.blockUI({ message: '<h3><img src="./libraries/jquery/layer/skin/default/xubox_loading2.gif" />下载中...</h3>' });
        var recordId = $( "input[name='record']").val() ;
        var params={};
        var module = app.getModuleName();
        params['record']=recordId;
        params['action']='BasicAjax';
        params['module']=module;
        params['mode']='downloadPdf';
        AppConnector.request(params).then(
            function(data){
                if(data.success==true){
                    $('.blockUI').remove();
                    var url = data.result['data'];
                    window.open(url);
                }else{

                }

            }
        );
    }
</script>