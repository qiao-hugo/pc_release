{strip}
   {if !empty($CURRENTCOMMENT['visitimprovement'])}
       <div style="width: 100%;height:60px;">
           <button class="btn btn-small buthidden pull-right" type="button" stats="hide" style="border:1px dashed #178fdd;border-radius:20px;width:40px;height:40px;margin-right:20px;margin-top:10px;"><i class="icon-eye-open" title="隐藏"></i></button>
       </div>

    <div class="span12 hiddensc" style="margin:5px;background-color:#ffffff">
        <div class="accordion-group">
            <div class="accordion-heading">
                <h4 style="margin-left:20px;">改进意见&进度</h4>
            </div>
            <div>
                <div  class="accordion-body collapse in">
                    <div class="accordion-inner">
                        <div class="container-fluid">
                            {foreach item=VISITIMPROVEMENT from=$CURRENTCOMMENT['visitimprovement']}
                            <div class="row">
                                <div class="span12" style="border:1px #ddd dashed;padding:2px;">
                                    <div class="row-fluid">
                                        <div class="{if $VISITIMPROVEMENT['visitimprovement']['improvementdschedule'] eq 100}span5{elseif  !empty($VISITIMPROVEMENT['improveschedule'])}span4{else}span11{/if}">
                                            <div style="border:1px #ddd dashed;padding:2px;">
                                                <blockquote>
                                                    <p style="font-size: 14px;color:#666;">
                                                        <strong>改进意见</strong>:&nbsp;&nbsp;{$VISITIMPROVEMENT['visitimprovement']['improvementdremark']}
                                                    </p><br>
                                                    <small>{$VISITIMPROVEMENT['visitimprovement']['improvementname']}</small>
                                                    <small>{$VISITIMPROVEMENT['visitimprovement']['improvementdatetime']}</small>
                                                </blockquote>
                                            </div>
                                        </div>
                                        {if !empty($VISITIMPROVEMENT['improveschedule'])}
                                        <div class="span7">
                                            {foreach item=IMPROVESCHEDULE from=$VISITIMPROVEMENT['improveschedule']}
                                                <div class="alert {if $IMPROVESCHEDULE['schedule'] lte 30}
                                                alert-error
                                                {elseif $IMPROVESCHEDULE['schedule'] lte 60}
                                                alert
                                                {elseif $IMPROVESCHEDULE['schedule'] lte 80}
                                                alert-info
                                                {else}
                                                alert-success
                                                {/if}
                                                ">
                                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                    <span class="label label-a_normal">{$IMPROVESCHEDULE['improveschedulename']}</span> &nbsp;&nbsp;&nbsp;&nbsp;{$IMPROVESCHEDULE['createdtime']}
                                                    <p style="font-size: 14px;margin-top:10px;text-indent:25px;">
                                                        {$IMPROVESCHEDULE['remark']}
                                                    </p>
                                                    <br>
                                                    <div class="progress progress-striped active {if $IMPROVESCHEDULE['schedule'] lte 30} progress-danger{elseif $IMPROVESCHEDULE['schedule'] lte 60}  progress-warning{elseif $IMPROVESCHEDULE['schedule'] lte 80} progress-info{else} progress-success{/if}">
                                                        <div class="bar" style="width: {$IMPROVESCHEDULE['schedule']}%;">{$IMPROVESCHEDULE['schedule']}
                                                        </div>
                                                    </div>

                                                </div>
                                            {/foreach}
                                        </div>
                                        {/if}
                                        {if $VISITIMPROVEMENT['visitimprovement']['improvementdschedule'] neq 100}
                                        <div class="span1">
                                            <div>
                                                <button class="btn btn-small btnaddschedule" type="button" data-id="{$VISITIMPROVEMENT['visitimprovement']['visitimprovementid']}" style="border:1px dashed #178fdd;border-radius:20px;width:40px;height:40px;margin-right:20px;margin-top:10px;"><i class="icon-plus" title="点击添加点评"></i></button>
                                            </div>
                                        </div>
                                        {/if}
                                    </div>
                                    <div style="clear:both;"></div>
                                </div>
                                <br>

                            </div>
                                <br>
                            {/foreach}
                        </div>
                    </div>
                    <div style="clear:both;"></div>
                </div>
            </div>
        </div>
    </div>

    {/if}
{/strip}