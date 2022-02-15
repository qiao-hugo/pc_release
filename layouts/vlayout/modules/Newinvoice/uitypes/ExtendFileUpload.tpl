{strip}
<div class="fileUploadContainer{$MORE_FIELDS['invoiceextendid']}" data-id="{$MORE_FIELDS['invoiceextendid']}" xmlns="http://www.w3.org/1999/html">
    {assign var=FIELD_VALUE value=explode('*|*',$MORE_FIELDS['file'])}

    <div class="upload">
        <div style="display:inline-block;width:70px;height:30px;overflow: hidden;vertical-align: middle;"  title="文件名请勿包含空格"><div style="margin-top:-2px;">文件名请勿</div><div style="margin-top:-5px;">包含空格</div></div>
        <input type="button" id="uploadButtonExtend{$MORE_FIELDS['invoiceextendid']}" value="上传"  title="文件名请勿包含空格" />
        <div style="display:inline-block" id="fileallExtend{$MORE_FIELDS['invoiceextendid']}">
        {if !empty($FIELD_VALUE[0])}
            {foreach item=NEWFILD from=$FIELD_VALUE}
            {assign var=NFIELD_VALUE value=explode('##',$NEWFILD)}
                <span class="label file{$NFIELD_VALUE[1]}" style="margin-left:5px;"><a href="index.php?module=Newinvoice&action=DownloadFile&filename={base64_encode($NFIELD_VALUE[1])}" target="_blank">{$NFIELD_VALUE[0]}</a>&nbsp;<b class="extendDeletefile" data-class="file{$NFIELD_VALUE[1]}" data-extendId="{$MORE_FIELDS['invoiceextendid']}" data-id="{$NFIELD_VALUE[1]}" title="删除文件" style="display:inline-block;width:12px;height:12px;line-height:12px;text-align:center">x</b>&nbsp;</span>
            <input class="ke-input-text file{$NFIELD_VALUE[1]}" type="hidden" name="extendfile{$MORE_FIELDS['invoiceextendid']}[{$NFIELD_VALUE[1]}]" data-id="{$NFIELD_VALUE[1]}" id="extendfile{$MORE_FIELDS['invoiceextendid']}" value="{$NFIELD_VALUE[0]}" readonly="readonly" />
            <input class="file{$NFIELD_VALUE[1]}" type="hidden" name="extendfilesid{$MORE_FIELDS['invoiceextendid']}[{$NFIELD_VALUE[1]}]" value="{$NFIELD_VALUE[1]}">
            {/foreach}
        {else}
            <input class="ke-input-text extendfiledelete" data-id="{$MORE_FIELDS['invoiceextendid']}" type="hidden" name="extendfile{$MORE_FIELDS['invoiceextendid']}" id="extendfile{$MORE_FIELDS['invoiceextendid']}" value="" readonly="readonly" />
            <input class="extendfiledelete" data-id="{$MORE_FIELDS['invoiceextendid']}" type="hidden" name="extendfilesid{$MORE_FIELDS['invoiceextendid']}" value="">
        {/if}
        </div>
    </div>
</div>
{/strip}