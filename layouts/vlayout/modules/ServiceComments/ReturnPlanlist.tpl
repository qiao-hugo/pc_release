{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*
********************************************************************************/
-->*}
{strip}
{* Change to this also refer: AddCommentForm.tpl *}
    <div>
    <div class="relatedHeader ">
        <div class="btn-toolbar row-fluid"><div class="span8">&nbsp;</div><div class="span4"><span class="row-fluid"><span class="span7 pushDown"><span class="pull-right pageNumbers alignTop" data-placement="bottom" data-original-title="" style="margin-top: -5px">  to </span></span><span class="span5 pull-right"><span class="btn-group pull-right"></span></span></span></div></div>
    </div>
    <table class="table">
        <thead>
        <tr>
            <th>排序</th>
            <th>时间期限</th>
            <th>跟进内容</th>
            <th>状态</th>
        </tr>
        </thead>
        <tbody>
        {foreach key="index" item="data" from="$RETURNPLAN"}
            <input type="hidden" value="{$data['commentreturnplanid']}" id="commentreturnplanid">
            <tr >
               <td>{$index+1}</td>
                <td>
                    开始时间：
                    <span class="label label-info">{$data['uppertime']}</span>
                    <p><p><p> <p><p>
                        结束时间：
                        <span class="label label-info">{$data['lowertime']}</span>
                </td>
                <td  style="width:60%">
                    {html_entity_decode($data['reviewcontent'])}
                </td>
                <td><span class="badge badge-{if $data['status'] eq '进行中'}success{elseif $data['status'] eq '已完成'}info{elseif $data['status'] eq '未开始'}{elseif $data['status'] eq '已超期'}warning{/if}">{$data['status']}</span></td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
{/strip}