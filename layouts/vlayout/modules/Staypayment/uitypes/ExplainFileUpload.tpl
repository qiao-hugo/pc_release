{strip}
<div class="fileUploadContainer" xmlns="http://www.w3.org/1999/html">
    {assign var=FIELD_VALUE value=explode('*|*',$FIELD_MODEL->get('fieldvalue'))}

<div class="upload {$FIELD_VALUE[0]}">
    <div style="display:inline-block;width:70px;height:30px;overflow: hidden;vertical-align: middle;"  title="文件名请勿包含空格"><div style="margin-top:-2px;">文件名请勿</div><div style="margin-top:-5px;">包含空格</div></div>
    <input type="button" id="uploadexplainButton" value="上传"  title="文件名请勿包含空格" />
    <div style="display:inline-block" id="fileallexplain">
    {if !empty($FIELD_VALUE[0])}
        {foreach item=NEWFILD from=$FIELD_VALUE}
        {assign var=NFIELD_VALUE value=explode('##',$NEWFILD)}
        <span class="label file{$NFIELD_VALUE[1]}" style="margin-left:5px;">{$NFIELD_VALUE[0]}&nbsp;<b class="deletefile" data-class="file{$NFIELD_VALUE[1]}" data-id="{$NFIELD_VALUE[1]}" title="删除文件" style="display:inline-block;width:12px;height:12px;line-height:12px;text-align:center">x</b>&nbsp;</span>
        <input class="ke-input-text file{$NFIELD_VALUE[1]}" type="hidden" name="explainfile[{$NFIELD_VALUE[1]}]" data-id="{$NFIELD_VALUE[1]}" id="explainfile" value="{$NFIELD_VALUE[0]}" readonly="readonly" />
        <input class="file{$NFIELD_VALUE[1]}" type="hidden" name="attachmentsid[{$NFIELD_VALUE[1]}]" value="{$NFIELD_VALUE[1]}">
        {/foreach}
    {else}
        <input class="ke-input-text explainfiledelete" type="hidden" name="explainfile" id="explainfile" value="" readonly="readonly" />
        <input class="explainfiledelete" type="hidden" name="attachmentsid" value="">
    {/if}
    </div>
</div>
</div>
{/strip}