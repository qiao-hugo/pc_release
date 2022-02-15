{strip}
<div style="margin-right:20px;margin-top:10px;">
    <div class="row-fluid" id="c" style="width:100%;">
        <div style="border-right:1px #ccc solid;">
            <table class="table">
                <form name="formtable" id="formtable">
                    <tr>
                        <td><span style="font-size: 14px;font-weight: bold;">提醒设置</span> 默认每天早上9点发出提醒</td></tr>
                <tr>
                    <td>
                        <fieldset>
                            <legend><span style="font-size: 14px;color:#f0a30a;">到期前提醒</span></legend>
                            <div>
                                <label class="pull-left">1，应收时间前&nbsp;</label>
                                <div class="pull-left">
                                    <input name="forwardday" class="span9 dateField" id="forwardday" type="number" min="1" step="1" value="{if $ROWDATA['rbexp']['forwardday']>0}{$ROWDATA['rbexp']['forwardday']}{else}1{/if}" style="width:60px;"/>
                                </div>
                                <label class="pull-left">&nbsp;天提醒</label>
                            </div>
                            <div style="clear: both;"></div>
                            <div>
                                <label class="pull-left">2，提醒渠道：&nbsp;</label>
                                <div class="pull-left">
                                    <select id="alertchannels" name="alertchannels" class="chzn-select referenceModulesList streched" multiple>
                                        <option value="email"{if in_array('email',$ROWDATA['rbexp']['alertchannelsarr'])} selected{/if}>邮件</option>
                                        <option value="assistant"{if in_array('assistant',$ROWDATA['rbexp']['alertchannelsarr'])} selected{/if}>企业小助手</option>
                                    </select>
                                </div>
                            </div>
                            <div style="clear: both;"></div>
                            <div>
                                <label class="pull-left">3，状态&nbsp;&nbsp;</label>
                                <div class="pull-left">
                                    <label class="pull-left"><input name="isclose" type="radio" value="0" {if $ROWDATA['rbexp']['isclose']>0}{else}checked{/if}>&nbsp;&nbsp;开启</label>
                                    <label class="pull-left">&nbsp;<input name="isclose" type="radio" value="1" {if $ROWDATA['rbexp']['isclose']>0}checked{else}{/if}>&nbsp;&nbsp;关闭</label>
                                </div>
                            </div>
                            <div style="clear: both;"></div>
                        </fieldset>
                    </td>
                </tr>
                    <tr>
                        <td>
                            <fieldset>
                                <legend><span style="font-size: 14px;color:#f0a30a;">已逾期预警</span></legend>
                                <div>
                                    <label class="pull-left">1，提醒渠道：&nbsp;</label>
                                    <div class="pull-left">
                                        <select id="alertchannels1" name="alertchannels1" class="chzn-select referenceModulesList streched" multiple>
                                            <option value="email" {if in_array('email',$ROWDATA['Overduewarning']['alertchannelsarr'])} selected{/if}>邮件</option>
                                            <option value="assistant"  {if in_array('assistant',$ROWDATA['Overduewarning']['alertchannelsarr'])} selected{/if}>企业小助手</option>
                                        </select>
                                    </div>
                                </div>
                                <div style="clear: both;"></div>
                                <div>
                                    <label class="pull-left">2，状态&nbsp;&nbsp;</label>
                                    <div class="pull-left">
                                        <label class="pull-left"><input name="isclose1" type="radio" value="0" {if $ROWDATA['Overduewarning']['isclose']>0}{else}checked{/if}>&nbsp;&nbsp;开启</label>
                                        <label class="pull-left">&nbsp;<input name="isclose1" type="radio" value="1" {if $ROWDATA['Overduewarning']['isclose']>0}checked{else}{/if}>&nbsp;&nbsp;关闭</label>
                                    </div>
                                </div>
                                <div style="clear: both;"></div>
                            </fieldset>
                        </td>
                    </tr>

                </form>
                <tr>
                    <td style="text-align: center;"><input type="button" value="保存" id="PostData" name="PostData" class="btn"></td>
                </tr>
            </table>
        </div>
    </div>
</div>
{/strip}
