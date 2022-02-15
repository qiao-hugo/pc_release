
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
    <!--
    All final details are stored in the first element in the array with the index name as final_details
    so we will get that array, parse that array and fill the details
    -->

    <br>

    {if $RECORD_ID>0}

            {foreach key=row_no item=data from=$C_CADSNAME}
        <table class="table table-bordered blockContainer showInlineTable  detailview-table cadsname" data-num="{$data['adsnameid']}">
            <thead>
            <tr>
                <th class="blockHeader" colspan="4">
                    <img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
                    <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
                    &nbsp;&nbsp;广告名称&nbsp;&nbsp;<span class="label label-a_normal">{$row_no+1}</span>
                    <b class="pull-right">
                        <button class="btn btn-small delbutton" type="button" data-category="cadsname" data-id="{$data['adsnameid']}">
                            <i class="icon-trash" title="删除"></i>
                        </button>
                    </b>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('adsname','Medium')}</label>
                    <input type="hidden" name="updateads[{$data['adsnameid']}]" value="{$data['adsnameid']}">
                </td>
                <td class="fieldValue medium" >
                    <div class="row-fluid">
                        <span class="span10">
                            <input type="text" class="input-large" name="uadsname[{$data['adsnameid']}]" value="{$data['adsname']}"  />
                        </span></div></td><td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">
                        {vtranslate('channelposition','Medium')}
                    </label>
                </td>
                <td class="fieldValue medium" >
                    <div class="row-fluid">
                        <span class="span10">
                            <input type="text" class="input-large" name="uchannelposition[{$data['adsnameid']}]" value="{$data['channelposition']}"  />
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('majoradvertising','Medium')}</label>
                </td>
                <td class="fieldValue medium" >
                    <div class="row-fluid">
                        <span class="span10">
                            <input type="text" class="input-large " name="umajoradvertising[{$data['adsnameid']}]" value="{$data['majoradvertising']}" />
                        </span>
                    </div>
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('recentmaintenancetime','Medium')}</label>
                </td>
                <td class="fieldValue medium" >
                    <div class="input-append row-fluid">
                        <div class="span10 row-fluid date form_datetime">
                            <input  type="text" class="span9 dateField" name="urecentmaintenancetime[{$data['adsnameid']}]" data-date-format="yyyy-mm-dd" readonly="" value="{$data['recentmaintenancetime']}" >
                            <span class="add-on"><i class="icon-calendar"></i></span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('billingmode','Medium')}</label>
                </td>
                <td class="fieldValue medium" >
                    <div class="row-fluid">
                        <span class="span10">
                            <input type="text" class="input-large " name="ubillingmode[{$data['adsnameid']}]" value="{$data['billingmode']}" />
                        </span>
                    </div>
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('unitprice','Medium')}</label>
                </td>
                <td class="fieldValue medium" >
                    <div class="row-fluid">
                        <span class="span10">
                            <input type="text" class="input-large" name="uunitprice[{$data['adsnameid']}]" value="{$data['unitprice']}"  /></span>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('cpcaverageprice','Medium')}</label>
                </td>
                <td class="fieldValue medium" >
                    <div class="row-fluid">
                        <span class="span10">
                            <input type="text" class="input-large " name="ucpcaverageprice[{$data['adsnameid']}]" value="{$data['cpcaverageprice']}" />
                        </span>
                    </div>
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('cpr','Medium')}</label>
                </td>
                <td class="fieldValue medium" >
                    <div class="row-fluid">
                        <span class="span10">
                            <input  type="text" class="input-large" name="ucpr[{$data['adsnameid']}]" value="{$data['cpr']}"  />
                        </span>
                    </div>
                </td>
            </tr>
        </table>

        <br>

        {/foreach}
    {else}
        <table class="table table-bordered blockContainer showInlineTable cadsname"  data-num="1">
            <tr>
                <th class="blockHeader" colspan="4">广告名称<span class="label label-b_check">1</span>
                    <b class="pull-right">
                        <button class="btn btn-small delbutton" type="button" data-category="cadsname" data-id="1">
                            <i class="icon-trash" title="删除"></i></button>
                    </b>
                </th>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('adsname','Medium')}</label>
                    <input type="hidden" name="insetrads[1]" value="1">
                </td>
                <td class="fieldValue medium" >
                    <div class="row-fluid">
                        <span class="span10"><input type="text" class="input-large" name="adsname[1]" value=""  /></span>
                    </div>
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('channelposition','Medium')}</label>
                </td>
                <td class="fieldValue medium" >
                    <div class="row-fluid">
                        <span class="span10">
                            <input type="text" class="input-large" name="channelposition[1]" value=""  />
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('majoradvertising','Medium')}</label>
                </td><td class="fieldValue medium" >
                    <div class="row-fluid">
                        <span class="span10">
                            <input type="text" class="input-large " name="majoradvertising[1]" value="" />
                        </span>
                    </div>
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('recentmaintenancetime','Medium')}</label>
                </td>
                <td class="fieldValue medium" >
                    <div class="input-append row-fluid">
                        <div class="span10 row-fluid date form_datetime">
                            <input id="cadsrecentmaintenancetime1" type="text" class="span9 dateField" name="recentmaintenancetime[1]" data-date-format="yyyy-mm-dd" readonly="" value="" >
                            <span class="add-on"><i class="icon-calendar"></i></span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('billingmode','Medium')}</label>
                </td>
                <td class="fieldValue medium" >
                    <div class="row-fluid"><span class="span10">
                            <input type="text" class="input-large " name="billingmode[1]" value="" /></span>
                    </div>
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('unitprice','Medium')}</label>
                </td>
                <td class="fieldValue medium" >
                    <div class="row-fluid">
                        <span class="span10">
                            <input id="Medium_editView_fieldName_salesdirectorauthority" type="text" class="input-large" name="unitprice[1]" value=""  />
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('cpcaverageprice','Medium')}</label>
                </td>
                <td class="fieldValue medium" >
                    <div class="row-fluid">
                        <span class="span10">
                            <input type="text" class="input-large " name="cpcaverageprice[1]" value="" />
                        </span>
                    </div>
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('cpr','Medium')}</label>
                </td>
                <td class="fieldValue medium" >
                    <div class="row-fluid">
                        <span class="span10">
                            <input id="Medium_editView_fieldName_salesdirectorauthority" type="text" class="input-large" name="cpr[1]" value=""  />
                        </span>
                    </div>
                </td>
            </tr>
        </table>
    {/if}
    <div style="display:none" id="insertcadsname"></div>
    <div style="position:fixed;right: 5%;bottom:15%;" id="insertbefore">
        <b class="pull-right"><button class="btn btn-small" type="button" id="addcadsname" style="border:1px dashed #ff0000;border-radius:20px;width:40px;height:40px;"><i class="icon-plus" title="点击添加广告信息" ></i></button></b>
        <b class="pull-right"><button class="btn btn-small" type="button" id="addfirmpolicy" style="border:1px dashed #178fdd;border-radius:20px;width:40px;height:40px;margin-right:5px;"><i class=" icon-plane" title="点击添加厂商政策"></i></button></b>
    </div>

    {if $RECORD_ID>0}
    {foreach key=row_no item=data from=$C_FIRMPOLICY}
        <table class="table table-bordered blockContainer showInlineTable  detailview-table firmpolicy" data-num="{$row_no+1}">
            <thead>
            <tr>
                <th class="blockHeader" colspan="4">
                    <img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
                    <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
                    &nbsp;&nbsp;厂商政策&nbsp;&nbsp;<span class="label label-a_normal">{$row_no+1}</span>
                    <b class="pull-right">
                        <button class="btn btn-small delbutton" type="button" data-category="firmpolicy" data-id="{$row_no+1}">
                            <i class="icon-trash" title="删除"></i>
                        </button>
                    </b>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('consumetaskcompletion','Medium')}</label>
                    <input type="hidden" name="updatefirmpolicy[{$data['firmpolicyid']}]" value="{$data['firmpolicyid']}">
                </td>
                <td class="fieldValue medium" >
                    <div class="row-fluid">
                        <span class="span10">
                            <input type="text" class="input-large" name="uconsumetaskcompletion[{$data['firmpolicyid']}]" value="{$data['consumetaskcompletion']}"  />
                        </span>
                    </div>
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('returnproportion','Medium')}</label>
                </td>
                <td class="fieldValue medium" >
                    <div class="row-fluid">
                        <span class="span10">
                            <input type="text" class="input-large" name="ureturnproportion[{$data['firmpolicyid']}]" value="{$data['returnproportion']}" />
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('salesauthority','Medium')}</label>
                </td>
                <td class="fieldValue medium" >
                    <div class="row-fluid">
                        <span class="span10">
                            <input type="text" class="input-large " name="usalesauthority[{$data['firmpolicyid']}]" value="{$data['salesauthority']}" />
                        </span>
                    </div>
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('salesdirectorauthority','Medium')}</label>
                </td>
                <td class="fieldValue medium" >
                    <div class="row-fluid">
                        <span class="span10">
                            <input type="text" class="input-large" name="usalesdirectorauthority[{$data['firmpolicyid']}]" value="{$data['salesdirectorauthority']}"  />
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('vpauthority','Medium')}</label>
                </td>
                <td class="fieldValue medium" >
                    <div class="row-fluid">
                        <span class="span10">
                            <input type="text" name="uvpauthority[{$data['firmpolicyid']}]" value="{$data['vpauthority']}"/>
                        </span>
                    </div>
                </td>
                <td class="medium"></td>
                <td class="medium"></td>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('remarks','Medium')}</label>
                </td>
                <td class="fieldValue medium"  colspan="3">
                    <div class="row-fluid"><span class="span10">
                            <textarea class="span11 " name="uremarks[{$data['firmpolicyid']}]">{$data['remarks']}</textarea>
                        </span>
                    </div>
                </td>
            </tr>
        </table>
        <br>

    {/foreach}
    {else}
        <table class="table table-bordered blockContainer showInlineTable firmpolicy"  data-num="1">
            <tr>
                <th class="blockHeader" colspan="4">厂商政策
                    <span class="label label-success">1</span>
                    <b class="pull-right"><button class="btn btn-small delbutton" type="button" data-category="firmpolicy" data-id="1"> <i class="icon-trash" title="删除"></i>        </button>    </b>
                </th>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('consumetaskcompletion','Medium')}</label>
                    <input type="hidden" name="insertfirmpolicy[1]" value="1">
                </td>
                <td class="fieldValue medium" >
                    <div class="row-fluid">
                        <span class="span10">
                            <input type="text" class="input-large" name="consumetaskcompletion[1]" value=""  />
                        </span>
                    </div>
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('returnproportion','Medium')}</label>
                </td>
                <td class="fieldValue medium" >
                    <div class="row-fluid">
                        <span class="span10">
                            <input type="text" class="input-large" name="returnproportion[1]" value=""  />
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('salesauthority','Medium')}</label>
                </td>
                <td class="fieldValue medium" >
                    <div class="row-fluid">
                        <span class="span10">
                            <input type="text" class="input-large " name="salesauthority[1]" value="" />
                        </span>
                    </div>
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('salesdirectorauthority','Medium')}</label>
                </td>
                <td class="fieldValue medium" >
                    <div class="row-fluid">
                        <span class="span10">
                            <input id="Medium_editView_fieldName_salesdirectorauthority" type="text" class="input-large" name="salesdirectorauthority[1]" value=""  />
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('vpauthority','Medium')}</label>
                </td>
                <td class="fieldValue medium" >
                    <div class="row-fluid">
                        <span class="span10">
                            <input type="text" name="vpauthority[1]"/>
                        </span>
                    </div>
                </td>
                <td class="medium"></td>
                <td class="medium"></td>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">{vtranslate('remarks','Medium')}</label>
                </td>
                <td class="fieldValue medium"  colspan="3">
                    <div class="row-fluid"><span class="span10"><textarea class="span11 " name="remarks[1]"></textarea></span>
                    </div>
                </td>
            </tr>
        </table>

    {/if}
    <div style="display:none" id="insertfirmpolicy"></div>

        <script>
            var insertdata=new Array;
            insertdata['firmpolicy']='<table class="table table-bordered blockContainer showInlineTable firmpolicy"  data-num="yesreplace"><tr><th class="blockHeader" colspan="4">厂商政策<span class="label label-success">yesreplace</span><b class="pull-right"><button class="btn btn-small delbutton" type="button" data-category="firmpolicy" data-id="yesreplace"> <i class="icon-trash" title="删除"></i>        </button>    </b></th></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('consumetaskcompletion','Medium')}</label><input type="hidden" name="insertfirmpolicy[]" value="yesreplace"></td><td class="fieldValue medium" ><div class="row-fluid"><span class="span10"><input type="text" class="input-large" name="consumetaskcompletion[]" value=""  /></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('returnproportion','Medium')}</label></td><td class="fieldValue medium" ><div class="row-fluid"><span class="span10"><input type="text" class="input-large" name="returnproportion[]" value=""  /></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('salesauthority','Medium')}</label></td><td class="fieldValue medium" ><div class="row-fluid"><span class="span10"><input type="text" class="input-large " name="salesauthority[]" value="" /></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('salesdirectorauthority','Medium')}</label></td><td class="fieldValue medium" ><div class="row-fluid"><span class="span10"><input id="Medium_editView_fieldName_salesdirectorauthority" type="text" class="input-large" name="salesdirectorauthority[]" value=""  /></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('vpauthority','Medium')}</label></td><td class="fieldValue medium" ><div class="row-fluid"><span class="span10"><input type="text" name="vpauthority[]"/></span></div></td><td class="medium"></td><td class="medium"></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('remarks','Medium')}</label></td><td class="fieldValue medium"  colspan="3"><div class="row-fluid"><span class="span10"><textarea class="span11 " name="remarks[]"></textarea></span></div></td></tr></table>';
            insertdata['cadsname']='<table class="table table-bordered blockContainer showInlineTable cadsname"  data-num="yesreplace"><tr><th class="blockHeader" colspan="4">广告名称<span class="label label-b_check">yesreplace</span><b class="pull-right"><button class="btn btn-small delbutton" type="button" data-category="cadsname" data-id="yesreplace"> <i class="icon-trash" title="删除"></i></button>    </b></th></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('adsname','Medium')}</label><input type="hidden" name="insetrads[]" value="yesreplace"></td><td class="fieldValue medium" ><div class="row-fluid"><span class="span10"><input type="text" class="input-large" name="adsname[]" value=""  /></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('channelposition','Medium')}</label></td><td class="fieldValue medium" ><div class="row-fluid"><span class="span10"><input type="text" class="input-large" name="channelposition[]" value=""  /></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('majoradvertising','Medium')}</label></td><td class="fieldValue medium" ><div class="row-fluid"><span class="span10"><input type="text" class="input-large " name="majoradvertising[]" value="" /></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('recentmaintenancetime','Medium')}</label></td><td class="fieldValue medium" ><div class="input-append row-fluid"><div class="span10 row-fluid date form_datetime"><input id="cadsrecentmaintenancetimeyesreplace" type="text" class="span9 dateField" name="recentmaintenancetime[]" data-date-format="yyyy-mm-dd" readonly="" value="" ><span class="add-on"><i class="icon-calendar"></i></span></div></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('billingmode','Medium')}</label></td><td class="fieldValue medium" ><div class="row-fluid"><span class="span10"><input type="text" class="input-large " name="billingmode[]" value="" /></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('unitprice','Medium')}</label></td><td class="fieldValue medium" ><div class="row-fluid"><span class="span10"><input id="Medium_editView_fieldName_salesdirectorauthority" type="text" class="input-large" name="unitprice[]" value=""  /></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('cpcaverageprice','Medium')}</label></td><td class="fieldValue medium" ><div class="row-fluid"><span class="span10"><input type="text" class="input-large " name="cpcaverageprice[]" value="" /></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('cpr','Medium')}</label></td><td class="fieldValue medium" ><div class="row-fluid"><span class="span10"><input id="Medium_editView_fieldName_salesdirectorauthority" type="text" class="input-large" name="cpr[]" value=""  /></span></div></td></tr></table>';
        </script>
{/strip}