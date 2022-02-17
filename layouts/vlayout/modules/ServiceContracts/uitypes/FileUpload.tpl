{strip}
<div class="fileUploadContainer" xmlns="http://www.w3.org/1999/html">
    {assign var=FIELD_VALUE value=explode('*|*',$FIELD_MODEL->get('fieldvalue'))}

<div class="upload">
    <div style="display:inline-block;width:120px;height:30px;overflow: hidden;vertical-align: middle;"  title="文件名请勿包含空格"><div style="margin-top:-2px;">支持doc、docx、pdf</div><div style="margin-top:-5px;">文件大小不超过8M</div></div>
    <input type="button" id="uploadButton" value="上传"  title="文件名请勿包含空格" />
    <div style="display:inline-block;white-space:normal;" id="fileall">
    {if !empty($FIELD_VALUE[0])}
        {foreach item=NEWFILD from=$FIELD_VALUE}
        {assign var=NFIELD_VALUE value=explode('##',$NEWFILD)}
        <span class="label file{$NFIELD_VALUE[1]}" style="margin-left:5px;">{$NFIELD_VALUE[0]}&nbsp;<b class="deletefile" data-class="file{$NFIELD_VALUE[1]}" data-id="{$NFIELD_VALUE[1]}" title="删除文件" style="display:inline-block;width:12px;height:12px;line-height:12px;text-align:center">x</b>&nbsp;</span>
        <input class="ke-input-text file{$NFIELD_VALUE[1]}" type="hidden" name="file[{$NFIELD_VALUE[1]}]" data-id="{$NFIELD_VALUE[1]}" id="file" value="{$NFIELD_VALUE[0]}" readonly="readonly" />
        <input class="file{$NFIELD_VALUE[1]}" type="hidden" name="attachmentsid[{$NFIELD_VALUE[1]}]" value="{$NFIELD_VALUE[1]}">
        {/foreach}
    {else}
        <input class="ke-input-text filedelete" type="hidden" name="file" id="file" value="" readonly="readonly" />
        <input class="filedelete" type="hidden" name="attachmentsid" value="">
    {/if}
    </div>
</div>
</div>
{/strip}