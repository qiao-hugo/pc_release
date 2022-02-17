
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
    {assign var=RECHARGEARR value=array('Accounts','Vendors')}
    {if $RECORD_ID>0}
        {if in_array($RECHARGESOURCE,$RECHARGEARR)}
            {foreach key=row_no item=data from=$C_RECHARGESHEET}
                {if $data['topplatform'] eq "谷歌"}
                    {assign var="isgoogle" value="1"}
                    {else}
                    {assign var="isgoogle" value="0"}
                {/if}

                <table class="table table-bordered blockContainer showInlineTable  detailview-table Duplicates" data-num="{$row_no+1}">
                    <thead>
                    <tr>
                        <th class="blockHeader" colspan="4"><img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"> <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;"> &nbsp;&nbsp;充值明细&nbsp;&nbsp;<spanclass="label label-success">{$row_no+1}</span><b class="pull-right">
                                <button class="btn btn-small delbutton" type="button" data-id="{$row_no+1}"><i class="icon-trash" title="删除充值明细"></i>
                                </button>
                            </b></th>
                    </tr>
                    </thead>
                    <tbody>
                    {if $RECHARGESOURCE eq 'Accounts'}
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span>{vtranslate('did','RefillApplication')}<input type="hidden" name="insertiref[{$row_no+1}]" data-cid="{$row_no+1}" value="{$row_no+1}"></label>
                        </td>
                        <td class="fieldValue medium"><select class="chzn-select" data-cid="{$row_no+1}" name="mid[{$row_no+1}]" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><option data-cid="{$row_no+1}" value="{$data['did']}">{$data['did']}</option></select>
                        </td>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('accountzh','RefillApplication')}</label></td>
                        <td class="fieldValue medium"><input type="text" class="input-large" data-cid="{$row_no+1}" name="maccountzh[{$row_no+1}]" value="{$data['accountzh']}" readonly="readonly"/></td>
                    </tr>
                    {elseif $RECHARGESOURCE eq 'Vendors'}
                        <tr>
                            <td class="fieldLabel medium">
                                <label class="muted pull-right marginRight10px">
                                    <span class="redColor">*</span>
                                    {vtranslate('productservice','RefillApplication')}
                                </label>
                            </td>
                            <td class="fieldValue medium">
                                <select class="chzn-select" name="mproductservice[{$row_no+1}]" data-cid="{$row_no+1}" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]">
                                    <option value="{$data['productid']}" data-cid="{$row_no+1}">{$data['topplatform']}</option>
                                </select>
                            </td>
                            <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span> {vtranslate('did','RefillApplication')}</label>
                            </td>
                            <td class="fieldValue medium"><select class="chzn-select" data-cid="{$row_no+1}" name="mid[{$row_no+1}]" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><option data-cid="{$row_no+1}" value="{$data['did']}">{$data['did']}</option></select>
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('accountzh','RefillApplication')}</label></td>
                            <td class="fieldValue medium"><input type="text" class="input-large" data-cid="{$row_no+1}" name="maccountzh[{$row_no+1}]" value="{$data['accountzh']}" readonly="readonly"/></td>

                            <td class="fieldLabel medium">
                                <label class="muted pull-right marginRight10px">
                                    {vtranslate('suppliercontractsid','RefillApplication')}</label>
                            </td>
                            <td class="fieldValue medium">
                                <input name="popupReferenceModule" type="hidden" value="SupplierContracts">
                                <input name="msuppliercontractsid[{$row_no+1}]" othername="suppliercontractsid" type="hidden" value="{$data['suppliercontractsid']}" data-multiple="0" class="sourceField" data-displayvalue="">
                                <div class="row-fluid input-prepend input-append">
                                    <!--<span class="add-on clearReferenceSelection cursorPointer"><i class="icon-remove-sign" title="清除"></i></span>-->
                                    <input id="suppliercontractsid[]_display" name="msuppliercontractsid[display{$row_no+1}]_display" type="text" class=" span7  marginLeftZero autoComplete" readonly="readonly" value="{$data['suppliercontractsname']}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  placeholder="查找..">
                                    <!--<span class="add-on relatedPopup cursorPointer"><i id="RefillApplication_editView_fieldName_suppliercontractsid_select" class="icon-search relatedPopup" title="选择"></i></span>--></div>
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel medium">
                                <label class="muted pull-right marginRight10px">签订日期</label>
                            </td>
                            <td class="fieldValue medium">
                                <div class="input-append row-fluid">
                                    <div class="span10 row-fluid date form_datetime">
                                        <input type="text" class="span9 dateField" name="msigndate[{$row_no+1}]" data-date-format="yyyy-mm-dd" readonly="readonly" value="{$data['signdate']}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                                    </div>
                                </div>
                            </td>
                            <td class="fieldLabel medium">
                                <label class="muted pull-right marginRight10px">{vtranslate('havesignedcontract','RefillApplication')}
                                    <input type="hidden" name="insertvendors[{$row_no+1}]" data-cid="{$row_no+1}" value="{$row_no+1}">
                                </label>
                            </td>
                            <td class="fieldValue medium">
                                <select class="chzn-select" data-cid="{$row_no+1}" name="mhavesignedcontract[{$row_no+1}]"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]">
                                    <option value="alreadySigned" {if $data['havesignedcontract'] eq "alreadySigned"}selected{/if}>{vtranslate('alreadySigned','RefillApplication')}</option>
                                    <option value="notSigned" {if $data['havesignedcontract'] eq "notSigned"}selected{/if}>{vtranslate('notSigned','RefillApplication')}</option>
                                </select>
                            </td>
                        </tr>
                    {/if}
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('topplatform','RefillApplication')}</label></td>
                        <td class="fieldValue medium">{*<input type="text" class="input-large" name="mtopplatform[{$row_no+1}]" data-cid="{$row_no+1}" value="{$data['topplatform']}" readonly="readonly">*}
                            <input name="mproductid[{$row_no+1}]" type="hidden" value="{$data['productid']}" data-multiple="0" class="sourceField" data-displayvalue="" data-cid="{$row_no+1}"><div class="row-fluid input-prepend input-append"><input id="mproductid_display[]" name="mproductid_display[{$row_no+1}]" type="text" class=" span7 	marginLeftZero autoComplete" value="{$data['topplatform']}" data-cid="{$row_no+1}" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  placeholder="查找.." readonly="readonly"></td>
                        <input type="hidden" name="msupprebate[{$row_no+1}]" data-cid="{$row_no+1}" value="{$data['supprebate']}">

                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('customeroriginattr','RefillApplication')}</label></td>
                        <td class="fieldValue medium"><select class="chzn-select" data-cid="{$row_no+1}" name="mcustomeroriginattr[{$row_no+1}]"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><option value="free" {if $data['customeroriginattr'] eq "free"}selected{/if}>{vtranslate('free','RefillApplication')}</option><option value="nonfree" {if $data['customeroriginattr'] eq "nonfree"}selected{/if}>{vtranslate('nonfree','RefillApplication')}</option></select></td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('isprovideservice','RefillApplication')}</label>
                        </td>
                        <td class="fieldValue medium">
                            <select class="chzn-select" data-cid="{$row_no+1}" name="misprovideservice[{$row_no+1}]"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><option value="yes" {if $data['isprovideservice'] eq "yes"}selected{/if}>{vtranslate('yes','RefillApplication')}</option><option value="no" {if $data['isprovideservice'] eq "no"}selected{/if}>{vtranslate('no','RefillApplication')}</option></select>
                        </td>

                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('rechargetypedetail','RefillApplication')}</label>
                        </td>
                        <td class="fieldValue medium"><select class="chzn-select" name="mrechargetypedetail[{$row_no+1}]" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-cid="{$row_no+1}">
                                <option {if $data['rechargetypedetail'] eq 'renew'}selected="selected"{/if} value="renew">{vtranslate('renew','RefillApplication')}</option>
                                <option {if $data['rechargetypedetail'] eq 'OpenAnAccount'}selected="selected"{/if} value="OpenAnAccount">{vtranslate('OpenAnAccount','RefillApplication')}</option>
                            </select></td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('accountrebatetype','RefillApplication')}</label>
                        </td>
                        <td class="fieldValue medium"><select class="chzn-select" name="maccountrebatetype[{$row_no+1}]" data-cid="{$row_no+1}">
                                <option value="CashBack" {if $data['accountrebatetype'] eq 'CashBack'}selected="selected"{/if}>{vtranslate('CashBack','RefillApplication')}</option>
                                <option value="GoodsBack" {if $data['accountrebatetype'] eq 'GoodsBack'}selected="selected"{/if}>{vtranslate('GoodsBack','RefillApplication')}</option>

                            </select></td>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('rebatetype','RefillApplication')}</label>
                        </td>
                        <td class="fieldValue medium"><select class="chzn-select" name="mrebatetype[{$row_no+1}]" data-cid="{$row_no+1}">
                                <option value="CashBack" {if $data['rebatetype'] eq 'CashBack'}selected="selected"{/if}>{vtranslate('CashBack','RefillApplication')}</option>
                                <option value="GoodsBack" {if $data['rebatetype'] eq 'GoodsBack'}selected="selected"{/if}>{vtranslate('GoodsBack','RefillApplication')}</option>

                            </select></td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('Receivementcurrencytype','RefillApplication')}</label>
                        </td>
                        <td class="fieldValue medium"><select class="chzn-select" name="mreceivementcurrencytype[{$row_no+1}]" data-cid="{$row_no+1}">
                                <option value="人民币" {if $data['receivementcurrencytype'] eq '人民币'}selected="selected"{/if}>人民币</option>
                                <option value="日元" {if $data['receivementcurrencytype'] eq '日元'}selected="selected"{/if}>日元</option>
                                <option value="美金" {if $data['receivementcurrencytype'] eq '美金'}selected="selected"{/if}>美金</option>
                                <option value="欧元" {if $data['receivementcurrencytype'] eq '欧元'}selected="selected"{/if}>欧元</option>
                            </select></td>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('Exchangerate','RefillApplication')}</label>
                        </td>
                        <td class="fieldValue medium"><input type="text" class="input-large checknumber" data-cid="{$row_no+1}" name="mexchangerate[{$row_no+1}]" value="{$data['receivementcurrencytype']}"/></td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('tax','RefillApplication')} </label>
                        </td>
                        <td class="fieldValue medium"><select name="mtax[{$row_no+1}]" data-cid="{$row_no+1}" class="chzn-select">
                                <option value="6%" {if $data['tax'] eq '6%'}selected="selected"{/if}>6%</option>
                                <option value="17%"{if $data['tax'] eq '17%'}selected="selected"{/if}>17%</option>
                            </select></td>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('prestoreadrate','RefillApplication')}</label>
                        </td>
                        <td class="fieldValue medium"><input type="text" class="input-large checknumber" data-cid="{$row_no+1}" name="mprestoreadrate[{$row_no+1}]" {if $isgoogle eq 0} readonly="readonly"{/if} value="{$data['prestoreadrate']}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('rechargeamount','RefillApplication')}</label>
                        </td>
                        <td class="fieldValue medium"><input type="text" class="input-large checknumber" data-cid="{$row_no+1}" name="mrechargeamount[{$row_no+1}]" value="{$data['rechargeamount']}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly="readonly"/></td>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('discount','RefillApplication')}</label>
                        </td>
                        <td class="fieldValue medium"><input type="text" class="input-large" name="mdiscount[{$row_no+1}]" data-cid="{$row_no+1}" value="{$data['discount']}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly="readonly"/></td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('factorage','RefillApplication')}</label>
                        </td>

                        <td class="fieldValue medium"><input type="text" class="input-large" name="mfactorage[{$row_no+1}]" data-cid="{$row_no+1}" value="{$data['factorage']}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
                        </td>

                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('activationfee','RefillApplication')}</label>
                        </td>
                        <td class="fieldValue medium"><input type="text" class="input-large" name="mactivationfee[{$row_no+1}]" data-cid="{$row_no+1}" value="{$data['activationfee']}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('taxation','RefillApplication')}</label>
                        </td>
                        <td class="fieldValue medium"><input type="text" class="input-large" name="mtaxation[{$row_no+1}]" data-cid="{$row_no+1}" value="{$data['taxation']}" {if $isgoogle eq 1} readonly="readonly"{/if} data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
                        </td>

                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('totalcost','RefillApplication')}</label>
                        </td>
                        <td class="fieldValue medium"><input type="text" class="input-large" name="mtotalcost[{$row_no+1}]" data-cid="{$row_no+1}" value="{$data['totalcost']}" readonly="readonly"/></td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('transferamount','RefillApplication')}</label>
                        </td>
                        <td class="fieldValue medium"><input type="text" class="input-large" name="mtransferamount[{$row_no+1}]" value="{$data['transferamount']}" data-cid="{$row_no+1}" {if $isgoogle eq 1} readonly="readonly"{/if}/></td>

                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('servicecost','RefillApplication')} </label>
                        </td>
                        <td class="fieldValue medium"><input type="text" class="input-large" name="mservicecost[{$row_no+1}]" value="{$data['servicecost']}" readonly="readonly" data-cid="{$row_no+1}"/></td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('totalgrossprofit','RefillApplication')} </label>
                        </td>
                        <td class="fieldValue medium"><input type="text" class="input-large" name="mtotalgrossprofit[{$row_no+1}]" value="{$data['totalgrossprofit']}" readonly="readonly" data-cid="{$row_no+1}"/></td>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('mstatus','RefillApplication')} </label>
                        </td>
                        <td class="fieldValue medium"><input type="text" class="input-large" name="mmstatus[{$row_no+1}]" value="{$data['mstatus']}" data-cid="{$row_no+1}"/></td>
                    </tr>
                    </tbody>
                </table>
        <br>
        {/foreach}
        {elseif $RECHARGESOURCE eq 'PreRecharge'}
            {foreach key=row_no item=data from=$C_RECHARGESHEET}
                <table class="table table-bordered blockContainer showInlineTable  detailview-table Duplicates" data-num="{$row_no+1}">
                    <thead>
                    <tr>
                        <th class="blockHeader" colspan="4"><img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"> <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;"> &nbsp;&nbsp;充值明细&nbsp;&nbsp;<spanclass="label label-success">{$row_no+1}</span><b class="pull-right">
                                <button class="btn btn-small delbutton" type="button" data-id="{$row_no+1}"><i class="icon-trash" title="删除充值明细"></i>
                                </button>
                            </b></th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fieldLabel medium">
                                <label class="muted pull-right marginRight10px">
                                    <span class="redColor">*</span>
                                    {vtranslate('productservice','RefillApplication')}
                                </label>
                            </td>
                            <td class="fieldValue medium">
                                <select class="chzn-select" name="mproductservice[{$row_no+1}]" data-cid="{$row_no+1}" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]">
                                    <option value="{$data['productid']}" selected>{$data['topplatform']}</option>
                                </select>
                            </td>
                            <td class="fieldLabel medium">
                                <label class="muted pull-right marginRight10px">
                                    <input type="hidden" name="msupprebate[{$row_no+1}]" data-cid="{$row_no+1}" value="{$data['supprebate']}">
                                    {vtranslate('suppliercontractsid','RefillApplication')}</label>
                            </td>
                            <td class="fieldValue medium">
                                <input name="popupReferenceModule" type="hidden" value="SupplierContracts">
                                <input name="msuppliercontractsid[{$row_no+1}]" othername="suppliercontractsid" type="hidden" value="{$data['suppliercontractsid']}" data-multiple="0" class="sourceField" data-displayvalue="{$data['suppliercontractsname']}">
                                <div class="row-fluid input-prepend input-append">
                                    <span class="add-on clearReferenceSelection cursorPointer"><i class="icon-remove-sign" title="清除"></i>
                                    </span><input id="suppliercontractsid[rp{$row_no+1}]_display" name="msuppliercontractsid[display{$row_no+1}]_display" type="text" class=" span7 	marginLeftZero autoComplete" value="{$data['suppliercontractsname']}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  placeholder="查找..">
                                    <span class="add-on relatedPopup cursorPointer"><i id="RefillApplication_editView_fieldName_suppliercontractsid_select" class="icon-search relatedPopup" title="选择"></i></span></div>
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel medium">
                                <label class="muted pull-right marginRight10px">已签合同
                                    <input type="hidden" name="mInsertPreRecharge[{$row_no+1}]" data-cid="{$row_no+1}" value="{$row_no+1}">
                                </label>
                            </td>
                            <td class="fieldValue medium">
                                <select class="chzn-select" data-cid="{$row_no+1}" name="mhavesignedcontract[{$row_no+1}]"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]">
                                    <option value="alreadySigned" {if $data['havesignedcontract'] eq "alreadySigned"}selected{/if}>{vtranslate('alreadySigned','RefillApplication')}</option>
                                    <option value="notSigned" {if $data['havesignedcontract'] eq "notSigned"}selected{/if}>{vtranslate('notSigned','RefillApplication')}</option>
                                </select>
                                {*<input type="hidden" name="mhavesignedcontract[{$row_no+1}]" value="0">
                                <input  type="checkbox" name="mhavesignedcontract[{$row_no+1}]" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" {if $data['havesignedcontract'] eq 1} checked{/if}>
                                <span class="help-block"></span>*}
                            </td>
                            <td class="fieldLabel medium">
                                <label class="muted pull-right marginRight10px">签订日期</label>
                            </td>
                            <td class="fieldValue medium">
                                <div class="input-append row-fluid">
                                    <div class="span10 row-fluid date form_datetime">
                                        <input type="text" class="span9 dateField" name="msigndate[{$row_no+1}]" data-date-format="yyyy-mm-dd" readonly="readonly" value="{$data['signdate']}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('topplatform','RefillApplication')}</label></td>
                        <td class="fieldValue medium">
                            <input name="mproductid[{$row_no+1}]" type="hidden" value="{$data['productid']}" data-multiple="0" class="sourceField" data-displayvalue="" data-cid="{$row_no+1}"><div class="row-fluid input-prepend input-append"><input id="mproductid_display[]" name="mproductid_display[{$row_no+1}]" type="text" class=" span7 	marginLeftZero autoComplete" value="{$data['topplatform']}" data-cid="{$row_no+1}" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  placeholder="查找.." readonly="readonly"></td>

                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('rebatetype','RefillApplication')}</label>
                        </td>
                        <td class="fieldValue medium"><select class="chzn-select" name="mrebatetype[{$row_no+1}]" data-cid="{$row_no+1}">
                                <option value="CashBack" {if $data['rebatetype'] eq 'CashBack'}selected="selected"{/if}>{vtranslate('CashBack','RefillApplication')}</option>
                                <option value="GoodsBack" {if $data['rebatetype'] eq 'GoodsBack'}selected="selected"{/if}>{vtranslate('GoodsBack','RefillApplication')}</option>

                            </select></td>
                    </tr><tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('prestoreadrate','RefillApplication')}</label>
                        </td>
                        <td class="fieldValue medium"><input type="text" class="input-large checknumber" data-cid="{$row_no+1}" name="mprestoreadrate[{$row_no+1}]" value="{$data['prestoreadrate']}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly="readonly"/>
                        </td>

                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('rechargeamount','RefillApplication')}</label>
                        </td>
                        <td class="fieldValue medium"><input type="text" class="input-large checknumber" data-cid="{$row_no+1}" name="mrechargeamount[{$row_no+1}]" value="{$data['rechargeamount']}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/></td>
                    </tr><tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('discount','RefillApplication')}</label>
                        </td>
                        <td class="fieldValue medium"><input type="text" class="input-large" name="mdiscount[{$row_no+1}]" data-cid="{$row_no+1}" value="{$data['discount']}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly="readonly"/></td>


                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('rebates','RefillApplication')}</label>
                        </td>
                        <td class="fieldValue medium"><input type="text" class="input-large" name="mrebates[{$row_no+1}]" data-cid="{$row_no+1}" value="{$data['rebates']}" />
                        </td>
                    </tr><tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('mstatus','RefillApplication')} </label>
                        </td>
                        <td class="fieldValue medium"><input type="text" class="input-large" name="mmstatus[{$row_no+1}]" value="{$data['mstatus']}" data-cid="{$row_no+1}"/></td>

                    </tr>
                    </tbody>
                </table>
                <br>
            {/foreach}
            {elseif $RECHARGESOURCE eq 'TECHPROCUREMENT'}
            {foreach key=row_no item=data from=$C_RECHARGESHEET}
                <table class="table table-bordered blockContainer showInlineTable  detailview-table Duplicates" data-num="{$row_no+1}">
                    <thead>
                    <tr>
                        <th class="blockHeader" colspan="4"><img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"> <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;"> &nbsp;&nbsp;充值明细&nbsp;&nbsp;<spanclass="label label-success">{$row_no+1}</span><b class="pull-right">
                                <button class="btn btn-small delbutton" type="button" data-id="{$row_no+1}"><i class="icon-trash" title="删除充值明细"></i>
                                </button>
                            </b></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px">
                                <span class="redColor">*</span>
                                {vtranslate('productservice','RefillApplication')}
                            </label>
                        </td>
                        <td class="fieldValue medium">
                            <select class="chzn-select" name="mproductservice[{$row_no+1}]" data-cid="{$row_no+1}" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]">
                                <option value="{$data['productid']}">{$data['topplatform']}</option>
                            </select>
                        </td>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px">
                                <input type="hidden" name="msupprebate[{$row_no+1}]" data-cid="{$row_no+1}" value="{$data['supprebate']}">
                                {vtranslate('suppliercontractsid','RefillApplication')}</label>
                        </td>
                        <td class="fieldValue medium">
                            <input name="popupReferenceModule" type="hidden" value="SupplierContracts">
                            <input name="msuppliercontractsid[{$row_no+1}]" othername="suppliercontractsid" type="hidden" value="{$data['suppliercontractsid']}" data-multiple="0" class="sourceField" data-displayvalue="{$data['suppliercontractsname']}">
                            <div class="row-fluid input-prepend input-append">
                                    {*<span class="add-on clearReferenceSelection cursorPointer"><i class="icon-remove-sign" title="清除"></i></span>*}
                                    <input id="suppliercontractsid[display{$row_no+1}]_display" name="msuppliercontractsid[display{$row_no+1}]_display" type="text" readonly="readonly" class=" span7 	marginLeftZero autoComplete" value="{$data['suppliercontractsname']}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  placeholder="查找..">
                                {*<span class="add-on relatedPopup cursorPointer"><i id="RefillApplication_editView_fieldName_suppliercontractsid_select" class="icon-search relatedPopup" title="选择"></i></span>*}</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px">已签合同
                                <input type="hidden" name="mInserttechsheet[{$row_no+1}]" data-cid="{$row_no+1}" value="{$row_no+1}">
                            </label>
                        </td>
                        <td class="fieldValue medium">
                            <select class="chzn-select" data-cid="{$row_no+1}" name="mhavesignedcontract[{$row_no+1}]"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]">
                                <option value="alreadySigned" {if $data['havesignedcontract'] eq "alreadySigned"}selected{/if}>{vtranslate('alreadySigned','RefillApplication')}</option>
                                <option value="notSigned" {if $data['havesignedcontract'] eq "notSigned"}selected{/if}>{vtranslate('notSigned','RefillApplication')}</option>
                            </select>
                            {*<input type="hidden" name="mhavesignedcontract[{$row_no+1}]" value="0">
                            <input  type="checkbox" name="mhavesignedcontract[{$row_no+1}]" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" {if $data['havesignedcontract'] eq 1} checked{/if}>
                            <span class="help-block"></span>*}
                        </td>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px">签订日期</label>
                        </td>
                        <td class="fieldValue medium">
                            <div class="input-append row-fluid">
                                <div class="span10 row-fluid date form_datetime">
                                    <input type="text" class="span9 dateField" name="msigndate[{$row_no+1}]" data-date-format="yyyy-mm-dd" readonly="readonly" value="{$data['signdate']}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('topplatform','RefillApplication')}</label></td>
                        <td class="fieldValue medium">
                            <input name="mproductid[{$row_no+1}]" type="hidden" value="{$data['productid']}" data-multiple="0" class="sourceField" data-displayvalue="" data-cid="{$row_no+1}"><div class="row-fluid input-prepend input-append"><input id="mproductid_display[]" name="mproductid_display[{$row_no+1}]" type="text" class=" span7 	marginLeftZero autoComplete" value="{$data['topplatform']}" data-cid="{$row_no+1}" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  placeholder="查找.." readonly="readonly"></td>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('amountpayable','RefillApplication')}</label>
                        </td>
                        <td class="fieldValue medium"><input type="text" class="input-large checknumber" data-cid="{$row_no+1}" name="mamountpayable[{$row_no+1}]" value="{$data['amountpayable']}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <br>
            {/foreach}
        {elseif $RECHARGESOURCE eq 'OtherProcurement'}
            {foreach key=row_no item=data from=$C_RECHARGESHEET}
                <table class="table table-bordered blockContainer showInlineTable  detailview-table Duplicates" data-num="{$row_no+1}">
                    <thead>
                    <tr>
                        <th class="blockHeader" colspan="4"><img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"> <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;"> &nbsp;&nbsp;充值明细&nbsp;&nbsp;<spanclass="label label-success">{$row_no+1}</span><b class="pull-right">
                                <button class="btn btn-small delbutton" type="button" data-id="{$row_no+1}"><i class="icon-trash" title="删除充值明细"></i>
                                </button>
                            </b></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px">
                                <span class="redColor">*</span>
                                {vtranslate('productservice','RefillApplication')}
                            </label>
                        </td>
                        <td class="fieldValue medium">
                            <select class="chzn-select" name="mproductservice[{$row_no+1}]" data-cid="{$row_no+1}" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]">
                                <option value="{$data['productid']}">{$data['topplatform']}</option>
                            </select>
                        </td>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px">
                                <input type="hidden" name="msupprebate[{$row_no+1}]" data-cid="{$row_no+1}" value="{$data['supprebate']}">
                                {vtranslate('suppliercontractsid','RefillApplication')}</label>
                        </td>
                        <td class="fieldValue medium">
                            <input name="popupReferenceModule" type="hidden" value="SupplierContracts">
                            <input name="msuppliercontractsid[{$row_no+1}]" othername="suppliercontractsid" type="hidden" value="{$data['suppliercontractsid']}" data-multiple="0" class="sourceField" data-displayvalue="{$data['suppliercontractsname']}">
                            <div class="row-fluid input-prepend input-append">
                                    {*<span class="add-on clearReferenceSelection cursorPointer"><i class="icon-remove-sign" title="清除"></i>
                                    </span>*}<input id="suppliercontractsid[rp{$row_no+1}]_display" name="msuppliercontractsid[display{$row_no+1}]_display" type="text" class=" span7 	marginLeftZero autoComplete" value="{$data['suppliercontractsname']}" readonly="readonly" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  placeholder="查找..">
                                {*<span class="add-on relatedPopup cursorPointer"><i id="RefillApplication_editView_fieldName_suppliercontractsid_select" class="icon-search relatedPopup" title="选择"></i></span>*}</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px">已签合同
                                <input type="hidden" name="motherprocurement[{$row_no+1}]" data-cid="{$row_no+1}" value="{$row_no+1}">
                            </label>
                        </td>
                        <td class="fieldValue medium">
                            <select class="chzn-select" data-cid="{$row_no+1}" name="mhavesignedcontract[{$row_no+1}]"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]">
                                <option value="alreadySigned" {if $data['havesignedcontract'] eq "alreadySigned"}selected{/if}>{vtranslate('alreadySigned','RefillApplication')}</option>
                                <option value="notSigned" {if $data['havesignedcontract'] eq "notSigned"}selected{/if}>{vtranslate('notSigned','RefillApplication')}</option>
                            </select>
                            {*<input type="hidden" name="mhavesignedcontract[{$row_no+1}]" value="0">
                            <input  type="checkbox" name="mhavesignedcontract[{$row_no+1}]" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" {if $data['havesignedcontract'] eq 1} checked{/if}>
                            <span class="help-block"></span>*}
                        </td>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px">签订日期</label>
                        </td>
                        <td class="fieldValue medium">
                            <div class="input-append row-fluid">
                                <div class="span10 row-fluid date form_datetime">
                                    <input type="text" class="span9 dateField" name="msigndate[{$row_no+1}]" data-date-format="yyyy-mm-dd" readonly="readonly" value="{$data['signdate']}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('topplatform','RefillApplication')}</label></td>
                        <td class="fieldValue medium">
                            <input name="mproductid[{$row_no+1}]" type="hidden" value="{$data['productid']}" data-multiple="0" class="sourceField" data-displayvalue="" data-cid="{$row_no+1}"><div class="row-fluid input-prepend input-append"><input id="mproductid_display[]" name="mproductid_display[{$row_no+1}]" type="text" class=" span7 	marginLeftZero autoComplete" value="{$data['topplatform']}" data-cid="{$row_no+1}" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  placeholder="查找.." readonly="readonly"></td>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('purchaseamount','RefillApplication')}</label>
                        </td>
                        <td class="fieldValue medium"><input type="text" class="input-large checknumber" data-cid="{$row_no+1}" name="mpurchaseamount[{$row_no+1}]" value="{$data['purchaseamount']}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly="readonly"/>
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('purchaseprice','RefillApplication')}</label></td>
                        <td class="fieldValue medium">
                            <input type="text" class="input-large checknumber" data-cid="{$row_no+1}" name="mpurchaseprice[{$row_no+1}]" value="{$data['purchaseprice']}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('purchasequantity','RefillApplication')}</label>
                        </td>
                        <td class="fieldValue medium"><input type="text" class="input-large checknumber" data-cid="{$row_no+1}" name="mpurchasequantity[{$row_no+1}]" value="{$data['purchasequantity']}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <br>
            {/foreach}
        {elseif $RECHARGESOURCE eq 'NonMediaExtraction'}
            {foreach key=row_no item=data from=$C_RECHARGESHEET}
                <table class="table table-bordered blockContainer showInlineTable  detailview-table Duplicates" data-num="{$row_no+1}">
                    <thead>
                    <tr>
                        <th class="blockHeader" colspan="4"><img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"> <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;"> &nbsp;&nbsp;充值明细&nbsp;&nbsp;<spanclass="label label-success">{$row_no+1}</span><b class="pull-right">
                                <button class="btn btn-small delbutton" type="button" data-id="{$row_no+1}"><i class="icon-trash" title="删除充值明细"></i>
                                </button>
                            </b></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px">
                                <span class="redColor">*</span>
                                {vtranslate('productservice','RefillApplication')}
                            </label>
                        </td>
                        <td class="fieldValue medium">
                            <select class="chzn-select" name="mproductservice[{$row_no+1}]" data-cid="{$row_no+1}" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]">
                                <option value="{$data['productid']}">{$data['topplatform']}</option>
                            </select>
                        </td>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px">
                                <input type="hidden" name="msupprebate[{$row_no+1}]" data-cid="{$row_no+1}" value="{$data['supprebate']}">
                                {vtranslate('suppliercontractsid','RefillApplication')}</label>
                        </td>
                        <td class="fieldValue medium">
                            <input name="popupReferenceModule" type="hidden" value="SupplierContracts">
                            <input name="msuppliercontractsid[{$row_no+1}]" othername="suppliercontractsid" type="hidden" value="{$data['suppliercontractsid']}" data-multiple="0" class="sourceField" data-displayvalue="{$data['suppliercontractsname']}">
                            <div class="row-fluid input-prepend input-append">
                                    {*<span class="add-on clearReferenceSelection cursorPointer"><i class="icon-remove-sign" title="清除"></i>
                                    </span>*}<input id="suppliercontractsid[rp{$row_no+1}]_display" name="msuppliercontractsid[display{$row_no+1}]_display" type="text" class=" span7 	marginLeftZero autoComplete" readonly="readonly" value="{$data['suppliercontractsname']}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  placeholder="查找..">
                                {*<span class="add-on relatedPopup cursorPointer"><i id="RefillApplication_editView_fieldName_suppliercontractsid_select" class="icon-search relatedPopup" title="选择"></i></span>*}</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px">已签合同
                                <input type="hidden" name="motherprocurement[{$row_no+1}]" data-cid="{$row_no+1}" value="{$row_no+1}">
                            </label>
                        </td>
                        <td class="fieldValue medium">
                            <select class="chzn-select" data-cid="{$row_no+1}" name="mhavesignedcontract[{$row_no+1}]"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]">
                                <option value="alreadySigned" {if $data['havesignedcontract'] eq "alreadySigned"}selected{/if}>{vtranslate('alreadySigned','RefillApplication')}</option>
                                <option value="notSigned" {if $data['havesignedcontract'] eq "notSigned"}selected{/if}>{vtranslate('notSigned','RefillApplication')}</option>
                            </select>
                            {*<input type="hidden" name="mhavesignedcontract[{$row_no+1}]" value="0">
                            <input  type="checkbox" name="mhavesignedcontract[{$row_no+1}]" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" {if $data['havesignedcontract'] eq 1} checked{/if}>
                            <span class="help-block"></span>*}
                        </td>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px">签订日期</label>
                        </td>
                        <td class="fieldValue medium">
                            <div class="input-append row-fluid">
                                <div class="span10 row-fluid date form_datetime">
                                    <input type="text" class="span9 dateField" name="msigndate[{$row_no+1}]" data-date-format="yyyy-mm-dd" readonly="readonly" value="{$data['signdate']}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('topplatform','RefillApplication')}</label></td>
                        <td class="fieldValue medium">
                            <input name="mproductid[{$row_no+1}]" type="hidden" value="{$data['productid']}" data-multiple="0" class="sourceField" data-displayvalue="" data-cid="{$row_no+1}"><div class="row-fluid input-prepend input-append"><input id="mproductid_display[]" name="mproductid_display[{$row_no+1}]" type="text" class=" span7 	marginLeftZero autoComplete" value="{$data['topplatform']}" data-cid="{$row_no+1}" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  placeholder="查找.." readonly="readonly"></td>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('totalgrossprofit','RefillApplication')} </label>
                        </td>
                        <td class="fieldValue medium"><input type="text" class="input-large" name="mtotalgrossprofit[{$row_no+1}]" value="{$data['totalgrossprofit']}" data-cid="{$row_no+1}"/></td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('purchaseamount','RefillApplication')}</label>
                        </td>
                        <td class="fieldValue medium"><input type="text" class="input-large checknumber" data-cid="{$row_no+1}" name="mpurchaseamount[{$row_no+1}]" value="{$data['purchaseamount']}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <br>
            {/foreach}
        {/if}
        {/if}
    <div style="position:fixed;right: 5%;bottom:15%;" id="insertbefore"><b class="pull-right"><button class="btn btn-small" type="button" id="addfallinto" style="border:1px dashed #178fdd;border-radius:20px;width:40px;height:40px;"><i class="icon-plus" title="点击添加充值明细"></i></button></b></div>
    {if in_array($RECHARGESOURCE,$RECHARGEARR)}
    <table class="table table-bordered blockContainer showInlineTable  detailview-table" data-num="{$row_no+1}">
        <thead>
        <tr>
            <th class="blockHeader" colspan="4"><img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"> <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;"> &nbsp;&nbsp; 合计信息&nbsp;&nbsp;<b class="pull-right">

                </b></th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">
                        合计使用回款金额
                    </label>
                </td>
                <td class="fieldValue medium">
                    <input type="text" class="input-large" name="totalgatheri" readonly="readonly"/>
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">
                        合计垫款金额
                    </label>
                </td>
                <td class="fieldValue medium">
                    <input type="text" class="input-large" name="totaladvances" readonly="readonly"/>
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">
                        合计现金充值
                    </label>
                </td>
                <td class="fieldValue medium">
                    <input type="text" class="input-large" name="totalrechargeamount" readonly="readonly"/>
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">
                        合计充值账户币
                    </label>
                </td>
                <td class="fieldValue medium">
                    <input type="text" class="input-large" name="totalaccountcurrency" readonly="readonly"/>
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">
                        合计成本
                    </label>
                </td>
                <td class="fieldValue medium">
                    <input type="text" class="input-large" name="totalcosts" readonly="readonly"/>
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">
                        合计毛利
                    </label>
                </td>
                <td class="fieldValue medium">
                    <input type="text" class="input-large" name="totalmaori" readonly="readonly"/>
                </td>
            </tr>
        </tbody>
    </table>
    {/if}
        <script>
            {*//var erechargesheet='<table class="table table-bordered blockContainer showInlineTable  detailview-table Duplicates"  data-num="yesreplace"><thead><tr><th class="blockHeader" colspan="4"><img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">    <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">    &nbsp;&nbsp;充值明细&nbsp;&nbsp;<span class="label label-success">yesreplace</span><b class="pull-right">        <button class="btn btn-small delbutton" type="button"  data-id="yesreplace"> <i class="icon-trash" title="删除充值明细"></i>        </button>    </b></th>       </tr>       </thead>       <tbody>       <tr><td class="fieldLabel medium">    <label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('topplatform','RefillApplication')}</label><input type="hidden" name="insertiref[]" value="yesreplace"></td><td class="fieldValue medium"  ><select class="chzn-select"  name="mtopplatform[]">{if !empty($C_RECHARGERODUCT)}{foreach from=$C_RECHARGERODUCT item='RECHARGERODUCT'}<option value="{$RECHARGERODUCT['topplatform']}">{$RECHARGERODUCT['topplatform']}</option> {/foreach}{/if}</select></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('accountzh','RefillApplication')}</label></td><td class="fieldValue medium"  ><input type="text" class="input-large"  name="maccountzh[]" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" /><button id="historyAccountzh_temp" type="button" class="btn btn-info">历史账号</button></td>       </tr><tr><td class="fieldLabel medium">    <label class="muted pull-right marginRight10px">        <span class="redColor">*</span>{vtranslate('did','RefillApplication')}    </label></td><td class="fieldValue medium"><input type="text" class="input-large"  name="mid[]" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" /></td><td class="fieldLabel medium">    <label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('rechargetype','RefillApplication')}</label></td><td class="fieldValue medium"><select class="chzn-select t_mrechargetype"  name="mrechargetype[]" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><option value="c_recharge">充值</option><option value="c_refund">退款</option></select></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('rechargeamount','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="input-large checknumber"  name="mrechargeamount[]" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" /></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('prestoreadrate','RefillApplication')}</label></td><td class="fieldValue medium"  >    <input type="text" class="input-large checknumber"  name="mprestoreadrate[]" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" /></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('discount','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="input-large"  name="mdiscount[]" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" /></td><td class="fieldLabel medium">    <label class="muted pull-right marginRight10px">        {vtranslate('tax','RefillApplication')}    </label></td><td class="fieldValue medium"  >  <select name="mtax[]"><option value="6%">6%</option><option value="17%">17%</option></select>   <!--111111<input type="text" class="input-large"  name="mtax[]" value="" /> -->    </td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('factorage','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="input-large"  name="mfactorage[]" value="" /></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('activationfee','RefillApplication')}</label></td><td class="fieldValue medium"  >    <input type="text" class="input-large"  name="mactivationfee[]" value="" /></td>       </tr>       <tr><td class="fieldLabel medium">    <label class="muted pull-right marginRight10px">{vtranslate('totalcost','RefillApplication')}</label></td><td class="fieldValue medium"  ><input type="text" class="input-large"  name="mtotalcost[]" value="" /></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('dailybudget','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="input-large"  name="mdailybudget[]" value="" /></td>       </tr>       <tr><td class="fieldLabel medium">    <label class="muted pull-right marginRight10px">    {vtranslate('transferamount','RefillApplication')}</label></td><td class="fieldValue medium"  >    <input type="text" class="input-large"  name="mtransferamount[]" value="" /></td><td class="fieldLabel medium">    <label class="muted pull-right marginRight10px">{vtranslate('rebateamount','RefillApplication')}</label></td><td class="fieldValue medium"  >    <input type="text" class="input-large"  name="mrebateamount[]" value="" /></td>       </tr>       <tr><td class="fieldLabel medium">    <label class="muted pull-right marginRight10px">        {vtranslate('totalgrossprofit','RefillApplication')}    </label></td><td class="fieldValue medium"  >    <input type="text" class="input-large"  name="mtotalgrossprofit[]" value="" /></td><td class="fieldLabel medium">    <label class="muted pull-right marginRight10px">        {vtranslate('servicecost','RefillApplication')}    </label></td><td class="fieldValue medium"  >    <input type="text" class="input-large"  name="mservicecost[]" value="" /></td>       </tr>       <tr><td class="fieldLabel medium">    <label class="muted pull-right marginRight10px">{vtranslate('mstatus','RefillApplication')}    </label></td><td class="fieldValue medium"  >    <input type="text" class="input-large"  name="mmstatus[]" value="" /></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">充值类型</label></td><td class="fieldValue medium"><select class="chzn-select" name="mrechargetypedetail[]"><option value="">选择一个选项</option><option value="renew">续费</option><option value="OpenAnAccount">开户</option></select></td></tr></tbody></table>';*}
            {*var erechargesheet='<table class="table table-bordered blockContainer showInlineTable  detailview-table Duplicates"  data-num="yesreplace"><thead><tr><th class="blockHeader" colspan="4"><img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">    <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">    &nbsp;&nbsp;充值明细&nbsp;&nbsp;<span class="label label-success">yesreplace</span><b class="pull-right">        <button class="btn btn-small delbutton" type="button"  data-id="yesreplace"> <i class="icon-trash" title="删除充值明细"></i>        </button>    </b></th>       </tr>       </thead>       <tbody>       <tr><td class="fieldLabel medium">    <label class="muted pull-right marginRight10px">        <span class="redColor">*</span>{vtranslate('did','RefillApplication')}    </label></td><td class="fieldValue medium"><select class="chzn-select" data-cid="yesreplace" name="mid[]" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" ></select></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('accountzh','RefillApplication')}</label><input type="hidden" name="msupprebate[]" data-cid="yesreplace"></td><td class="fieldValue medium"  ><input type="text" class="input-large"  data-cid="yesreplace" name="maccountzh[]" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly="readonly" /></td>       </tr><tr><td class="fieldLabel medium">    <label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('topplatform','RefillApplication')}</label><input type="hidden" name="insertiref[]" data-cid="yesreplace" value="yesreplace"></td><td class="fieldValue medium"  ><input type="text" class="input-large"  name="mtopplatform[]" data-cid="yesreplace" value="" readonly="readonly"></td><td class="fieldLabel medium">    <label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('rechargetype','RefillApplication')}</label></td><td class="fieldValue medium"><select class="chzn-select t_mrechargetype"  name="mrechargetype[]" data-cid="yesreplace" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><option value="c_recharge">充值</option><option value="c_refund">退款</option></select></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('rechargeamount','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="input-large checknumber" data-cid="yesreplace"  name="mrechargeamount[]" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly="readonly" /></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('prestoreadrate','RefillApplication')}</label></td><td class="fieldValue medium"  >    <input type="text" class="input-large checknumber"  data-cid="yesreplace" name="mprestoreadrate[]" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" /></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('discount','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="input-large"  name="mdiscount[]" data-cid="yesreplace" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly="readonly" /></td><td class="fieldLabel medium">    <label class="muted pull-right marginRight10px">        {vtranslate('tax','RefillApplication')}    </label></td><td class="fieldValue medium"  >  <select name="mtax[]" data-cid="yesreplace" class="chzn-select"><option value="6%">6%</option><option value="17%">17%</option></select> </td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('factorage','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="input-large"  name="mfactorage[]" data-cid="yesreplace" value="" /></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('activationfee','RefillApplication')}</label></td><td class="fieldValue medium"  >    <input type="text" class="input-large"  name="mactivationfee[]" data-cid="yesreplace" value="" /></td>       </tr>       <tr><td class="fieldLabel medium">    <label class="muted pull-right marginRight10px">{vtranslate('taxation','RefillApplication')}</label></td><td class="fieldValue medium"  ><input type="text" class="input-large"  name="mtaxation[]" data-cid="yesreplace" value=""/><td class="fieldLabel medium">    <label class="muted pull-right marginRight10px">{vtranslate('totalcost','RefillApplication')}</label></td><td class="fieldValue medium"  ><input type="text" class="input-large"  name="mtotalcost[]" data-cid="yesreplace" value=""  readonly="readonly"/></td>       </tr>       <tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('dailybudget','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="input-large"  name="mdailybudget[]" data-cid="yesreplace" value="" /></td><td class="fieldLabel medium">    <label class="muted pull-right marginRight10px">    {vtranslate('transferamount','RefillApplication')}</label></td><td class="fieldValue medium"  >    <input type="text" class="input-large"  name="mtransferamount[]" value="" readonly="readonly" data-cid="yesreplace"/></td>       </tr>       <tr><td class="fieldLabel medium">    <label class="muted pull-right marginRight10px">{vtranslate('rebateamount','RefillApplication')}</label></td><td class="fieldValue medium"  >    <input type="text" class="input-large"  name="mrebateamount[]" value="" data-cid="yesreplace" /></td><td class="fieldLabel medium">    <label class="muted pull-right marginRight10px">        {vtranslate('totalgrossprofit','RefillApplication')}    </label></td><td class="fieldValue medium"  >    <input type="text" class="input-large"  name="mtotalgrossprofit[]" value="" readonly="readonly" data-cid="yesreplace" /></td>      </tr>       <tr><td class="fieldLabel medium">    <label class="muted pull-right marginRight10px">        {vtranslate('servicecost','RefillApplication')}    </label></td><td class="fieldValue medium"  >    <input type="text" class="input-large"  name="mservicecost[]" value="" readonly="readonly" data-cid="yesreplace" /></td> <td class="fieldLabel medium">    <label class="muted pull-right marginRight10px">{vtranslate('mstatus','RefillApplication')}    </label></td><td class="fieldValue medium"  >    <input type="text" class="input-large"  name="mmstatus[]" value=""  data-cid="yesreplace"/></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">充值类型</label></td><td class="fieldValue medium"><select class="chzn-select" name="mrechargetypedetail[]" data-cid="yesreplace"><option value="">选择一个选项</option><option value="renew">续费</option><option value="OpenAnAccount">开户</option></select></td></tr></tbody></table>';*}
            var erechargesheet='<table class="table table-bordered blockContainer showInlineTable  detailview-table Duplicates" data-num="yesreplace"><thead><tr><th class="blockHeader" colspan="4"><img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"> <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png"data-mode="show" data-id="141" style="display: inline;"> &nbsp;&nbsp;充值明细&nbsp;&nbsp;<spanclass="label label-success">yesreplace</span><b class="pull-right"><button class="btn btn-small delbutton" type="button" data-id="yesreplace"><i class="icon-trash" title="删除充值明细"></i></button></b></th></tr></thead><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span>{vtranslate('did','RefillApplication')}</label></td>	<td class="fieldValue medium"><select class="chzn-select" data-cid="yesreplace" name="mid[]"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"></select></td>	<td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('accountzh','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="input-large" data-cid="yesreplace" name="maccountzh[]" value="" readonly="readonly"/></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('topplatform','RefillApplication')}</label><input type="hidden" name="insertiref[]" data-cid="yesreplace" value="yesreplace"><input type="hidden" name="msupprebate[]" data-cid="yesreplace" value=""></td>	<td class="fieldValue medium"><input name="mproductid[]" type="hidden" value="0" data-multiple="0" class="sourceField" data-displayvalue="" data-cid="yesreplace"><div class="row-fluid input-prepend input-append"><input id="mproductid_display[]" name="mproductid_display[]" type="text" class=" span7 	marginLeftZero autoComplete" value="" data-cid="yesreplace" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  placeholder="查找.." readonly="readonly"></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('customeroriginattr','RefillApplication')}</label></td><td class="fieldValue medium"><select class="chzn-select" data-cid="yesreplace" name="mcustomeroriginattr[]"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><option value="free">{vtranslate('free','RefillApplication')}</option><option value="nonfree">{vtranslate('nonfree','RefillApplication')}</option></select></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('isprovideservice','RefillApplication')}</label></td>	<td class="fieldValue medium"><select class="chzn-select" data-cid="yesreplace" name="misprovideservice[]"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><option value="yes">{vtranslate('yes','RefillApplication')}</option><option value="no">{vtranslate('no','RefillApplication')}</option></select></td>	<td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('rechargetypedetail','RefillApplication')}</label></td>	<td class="fieldValue medium"><select class="chzn-select" name="mrechargetypedetail[]" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-cid="yesreplace"><option value="renew">续费</option><option value="OpenAnAccount">开户</option></select></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('accountrebatetype','RefillApplication')}</label></td>	<td class="fieldValue medium"><select class="chzn-select" name="maccountrebatetype[]"  data-cid="yesreplace"><option value="GoodsBack">{vtranslate('GoodsBack','RefillApplication')}</option><option value="CashBack">{vtranslate('CashBack','RefillApplication')}</option></select></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('rebatetype','RefillApplication')}</label></td>	<td class="fieldValue medium"><select class="chzn-select" name="mrebatetype[]"  data-cid="yesreplace"><option value="GoodsBack" selected="">{vtranslate('GoodsBack','RefillApplication')}</option><option value="CashBack">{vtranslate('CashBack','RefillApplication')}</option></select></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('Receivementcurrencytype','RefillApplication')}</label></td>	<td class="fieldValue medium"><select class="chzn-select" name="mreceivementcurrencytype[]"  data-cid="yesreplace"><option value="人民币" selected="">人民币</option><option value="日元">日元</option><option value="美金">美金</option><option value="欧元">欧元</option></select></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('Exchangerate','RefillApplication')}</label></td>	<td class="fieldValue medium"><input type="text" class="input-large checknumber" data-cid="yesreplace" name="mexchangerate[]" value="1.00" /></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('tax','RefillApplication')} </label></td>	<td class="fieldValue medium"><select name="mtax[]" data-cid="yesreplace" class="chzn-select"><option value="6%">6%</option><option value="17%">17%</option></select></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('prestoreadrate','RefillApplication')}</label></td>	<td class="fieldValue medium"><input type="text" class="input-large checknumber" data-cid="yesreplace" name="mprestoreadrate[]" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly="readonly"/></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('rechargeamount','RefillApplication')}</label></td>	<td class="fieldValue medium"><input type="text" class="input-large checknumber" data-cid="yesreplace" name="mrechargeamount[]" readonly="readonly" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('discount','RefillApplication')}</label></td>	<td class="fieldValue medium"><input type="text" class="input-large" name="mdiscount[]" data-cid="yesreplace" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly="readonly"/></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('factorage','RefillApplication')}</label></td>	<td class="fieldValue medium"><input type="text" class="input-large" name="mfactorage[]" data-cid="yesreplace" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('activationfee','RefillApplication')}</label></td>	<td class="fieldValue medium"><input type="text" class="input-large" name="mactivationfee[]" data-cid="yesreplace" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('taxation','RefillApplication')}</label></td>	<td class="fieldValue medium"><input type="text" class="input-large" name="mtaxation[]" data-cid="yesreplace" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('transferamount','RefillApplication')}</label></td>	<td class="fieldValue medium"><input type="text" class="input-large" name="mtransferamount[]" value="" data-cid="yesreplace"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('totalcost','RefillApplication')}</label></td>	<td class="fieldValue medium"><input type="text" class="input-large" name="mtotalcost[]" data-cid="yesreplace" value="" readonly="readonly"/></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('servicecost','RefillApplication')} </label></td>	<td class="fieldValue medium"><input type="text" class="input-large" name="mservicecost[]" value="" readonly="readonly" data-cid="yesreplace"/></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('totalgrossprofit','RefillApplication')} </label></td>	<td class="fieldValue medium"><input type="text" class="input-large" name="mtotalgrossprofit[]" value="" readonly="readonly" data-cid="yesreplace"/></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('mstatus','RefillApplication')} </label></td><td class="fieldValue medium"><input type="text" class="input-large" name="mmstatus[]" value="" data-cid="yesreplace"/></td></tr></tbody></table>';
            var vendorchargesheet='<table class="table table-bordered blockContainer showInlineTable  detailview-table Duplicates" data-num="yesreplace"><thead><tr><th class="blockHeader" colspan="4"><img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"> <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png"data-mode="show" data-id="141" style="display: inline;"> &nbsp;&nbsp;充值明细&nbsp;&nbsp;<spanclass="label label-success">yesreplace</span><b class="pull-right"><button class="btn btn-small delbutton" type="button" data-id="yesreplace"><i class="icon-trash" title="删除充值明细"></i></button></b></th></tr></thead><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> {vtranslate('productservice','RefillApplication')}</label></td><td class="fieldValue medium"><select class="chzn-select" data-cid="yesreplace" name="mproductservice[]" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"></select></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('did','RefillApplication')}</label></td><td class="fieldValue medium"><select class="chzn-select" data-cid="yesreplace" name="mid[]"  data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"></select></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('accountzh','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="input-large" data-cid="yesreplace" name="maccountzh[]" value="" readonly="readonly"/></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><input type="hidden" name="msupprebate[]" data-cid="yesreplace" value=""><span class="redColor">*</span>{vtranslate('suppliercontractsid','RefillApplication')}</label></td><td class="fieldValue medium"><input name="popupReferenceModule" type="hidden" value="SupplierContracts"><input name="msuppliercontractsid[]" othername="suppliercontractsid" type="hidden" value="" data-multiple="0" class="sourceField" data-displayvalue=""><div class="row-fluid input-prepend input-append"><input id="suppliercontractsid[displayyesreplace]_display" name="msuppliercontractsid[displayyesreplace]_display" type="text" class=" span7 marginLeftZero autoComplete" value="" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly="readonly" placeholder="查找.."></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('havesignedcontract','RefillApplication')}<input type="hidden" name="insertvendors[]" data-cid="yesreplace" value="yesreplace"></label></td><td class="fieldValue medium"><select class="chzn-select" data-cid="yesreplace" name="mhavesignedcontract[]"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><option value="alreadySigned">{vtranslate('alreadySigned','RefillApplication')}</option><option value="notSigned">{vtranslate('notSigned','RefillApplication')}</option></select></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('signdate','RefillApplication')}</label></td><td class="fieldValue medium"><div class="input-append row-fluid"><div class="span10 row-fluid date form_datetime"><input type="text" class="span9 dateField" name="msigndate[]" data-date-format="yyyy-mm-dd" readonly="readonly" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" ></div></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('topplatform','RefillApplication')}</label><input type="hidden" name="msupprebate[]" data-cid="yesreplace" value=""></td><td class="fieldValue medium"><input name="mproductid[]" type="hidden" value="0" data-multiple="0" class="sourceField" data-displayvalue="" data-cid="yesreplace"><div class="row-fluid input-prepend input-append"><input id="mproductid_display[]" name="mproductid_display[]" type="text" class=" span7 marginLeftZero autoComplete" value="" data-cid="yesreplace" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  placeholder="查找.." readonly="readonly"></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('customeroriginattr','RefillApplication')}</label></td><td class="fieldValue medium"><select class="chzn-select" data-cid="yesreplace" name="mcustomeroriginattr[]"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><option value="free">{vtranslate('free','RefillApplication')}</option><option value="nonfree">{vtranslate('nonfree','RefillApplication')}</option></select></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('isprovideservice','RefillApplication')}</label></td><td class="fieldValue medium"><select class="chzn-select" data-cid="yesreplace" name="misprovideservice[]"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><option value="yes">{vtranslate('yes','RefillApplication')}</option><option value="no">{vtranslate('no','RefillApplication')}</option></select></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('rechargetypedetail','RefillApplication')}</label></td><td class="fieldValue medium"><select class="chzn-select" name="mrechargetypedetail[]" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-cid="yesreplace"><option value="renew">续费</option><option value="OpenAnAccount">开户</option></select></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('accountrebatetype','RefillApplication')}</label></td>	<td class="fieldValue medium"><select class="chzn-select" name="maccountrebatetype[]"  data-cid="yesreplace"><option value="GoodsBack" selected="">{vtranslate('GoodsBack','RefillApplication')}</option><option value="CashBack">{vtranslate('CashBack','RefillApplication')}</option></select></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('rebatetype','RefillApplication')}</label></td>	<td class="fieldValue medium"><select class="chzn-select" name="mrebatetype[]"  data-cid="yesreplace"><option value="GoodsBack" selected="">{vtranslate('GoodsBack','RefillApplication')}</option><option value="CashBack">{vtranslate('CashBack','RefillApplication')}</option></select></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('Receivementcurrencytype','RefillApplication')}</label></td><td class="fieldValue medium"><select class="chzn-select" name="mreceivementcurrencytype[]"  data-cid="yesreplace"><option value="人民币" selected="">人民币</option><option value="日元">日元</option><option value="美金">美金</option><option value="欧元">欧元</option></select></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('Exchangerate','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="input-large checknumber" data-cid="yesreplace" name="mexchangerate[]" value="1.00" /></td></tr></tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('tax','RefillApplication')} </label></td><td class="fieldValue medium"><select name="mtax[]" data-cid="yesreplace" class="chzn-select"><option value="6%">6%</option><option value="17%">17%</option></select></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('prestoreadrate','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="input-large checknumber" data-cid="yesreplace" name="mprestoreadrate[]" readonly="readonly" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/></td></tr></tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('rechargeamount','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="input-large checknumber" data-cid="yesreplace" readonly="readonly" name="mrechargeamount[]" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('discount','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="input-large" name="mdiscount[]" data-cid="yesreplace" readonly="readonly" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/></td></tr></tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('factorage','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="input-large" name="mfactorage[]" data-cid="yesreplace" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('activationfee','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="input-large" name="mactivationfee[]" data-cid="yesreplace" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/></td></tr></tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('taxation','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="input-large" name="mtaxation[]" data-cid="yesreplace" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('transferamount','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="input-large" name="mtransferamount[]" value="" data-cid="yesreplace"/></td></tr></tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('totalcost','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="input-large" name="mtotalcost[]" data-cid="yesreplace" value="" readonly="readonly"/></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('servicecost','RefillApplication')} </label></td><td class="fieldValue medium"><input type="text" class="input-large" name="mservicecost[]" value="" readonly="readonly" data-cid="yesreplace"/></td></tr></tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('totalgrossprofit','RefillApplication')} </label></td><td class="fieldValue medium"><input type="text" class="input-large" name="mtotalgrossprofit[]" value="" readonly="readonly" data-cid="yesreplace"/></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('mstatus','RefillApplication')} </label></td><td class="fieldValue medium"><input type="text" class="input-large" name="mmstatus[]" value="" data-cid="yesreplace"/></td></tr></tbody></table>';
            var PreRecharge='<table class="table table-bordered blockContainer showInlineTable  detailview-table Duplicates" data-num="yesreplace"><thead><tr><th class="blockHeader" colspan="4"><img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"> <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png"data-mode="show" data-id="141" style="display: inline;"> &nbsp;&nbsp;充值明细&nbsp;&nbsp;<spanclass="label label-success">yesreplace</span><b class="pull-right"><button class="btn btn-small delbutton" type="button" data-id="yesreplace"><i class="icon-trash" title="删除充值明细"></i></button></b></th></tr></thead><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> {vtranslate('productservice','RefillApplication')}</label></td><td class="fieldValue medium"><select class="chzn-select" data-cid="yesreplace" name="mproductservice[]" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-cid="yesreplace"></select></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><input type="hidden" name="mInsertPreRecharge[]" data-cid="yesreplace" value="yesreplace">{vtranslate('suppliercontractsid','RefillApplication')}</label></td><td class="fieldValue medium"><input name="popupReferenceModule" type="hidden" value="SupplierContracts"><input name="msuppliercontractsid[]" othername="suppliercontractsid" type="hidden" value="" data-multiple="0" class="sourceField" data-displayvalue=""><div class="row-fluid input-prepend input-append"><input id="suppliercontractsid[displayyesreplace]_display" name="msuppliercontractsid[displayyesreplace]_display" type="text" class=" span7 	marginLeftZero autoComplete" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly="readonly" placeholder="查找.."></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">已签合同</label></td><td class="fieldValue medium"><select class="chzn-select" data-cid="yesreplace" name="mhavesignedcontract[]" ><option value="alreadySigned">{vtranslate('alreadySigned','RefillApplication')}</option><option value="notSigned">{vtranslate('notSigned','RefillApplication')}</option></select></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">签订日期</label></td><td class="fieldValue medium"><div class="input-append row-fluid"><div class="span10 row-fluid date form_datetime"><input type="text" class="span9 dateField" name="msigndate[]" data-date-format="yyyy-mm-dd" readonly="readonly" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" ></div></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('topplatform','RefillApplication')}</label><input type="hidden" name="msupprebate[]" data-cid="yesreplace" value=""></td><td class="fieldValue medium"><input name="mproductid[]" type="hidden" value="0" data-multiple="0" class="sourceField" data-displayvalue="" data-cid="yesreplace"><div class="row-fluid input-prepend input-append"><input id="mproductid_display[]" name="mproductid_display[]" type="text" class=" span7 	marginLeftZero autoComplete" value="" data-cid="yesreplace" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  placeholder="查找.." readonly="readonly"></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('rebatetype','RefillApplication')}</label></td>	<td class="fieldValue medium"><select class="chzn-select" name="mrebatetype[]"  data-cid="yesreplace"><option value="GoodsBack" selected="">{vtranslate('GoodsBack','RefillApplication')}</option><option value="CashBack">{vtranslate('CashBack','RefillApplication')}</option></select></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('prestoreadrate','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="input-large checknumber" data-cid="yesreplace" name="mprestoreadrate[]"  readonly="readonly" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('rechargeamount','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="input-large checknumber" data-cid="yesreplace" name="mrechargeamount[]" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('discount','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="input-large" name="mdiscount[]"  readonly="readonly" data-cid="yesreplace" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('rebates','RefillApplication')} </label></td><td class="fieldValue medium"><input type="text" class="input-large" name="mrebates[]" value="" data-cid="yesreplace"/></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('mstatus','RefillApplication')} </label></td><td class="fieldValue medium"><input type="text" class="input-large" name="mmstatus[]" value="" data-cid="yesreplace"/></td></tr></tbody></table>';
            var techsheet='<table class="table table-bordered blockContainer showInlineTable  detailview-table Duplicates" data-num="yesreplace"><thead><tr><th class="blockHeader" colspan="4"><img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"> <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png"data-mode="show" data-id="141" style="display: inline;"> &nbsp;&nbsp;充值明细&nbsp;&nbsp;<spanclass="label label-success">yesreplace</span><b class="pull-right"><button class="btn btn-small delbutton" type="button" data-id="yesreplace"><i class="icon-trash" title="删除充值明细"></i></button></b></th></tr></thead><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> {vtranslate('productservice','RefillApplication')}</label></td><td class="fieldValue medium"><select class="chzn-select" data-cid="yesreplace" name="mproductservice[]" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-cid="yesreplace"></select></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><input type="hidden" name="mInserttechsheet[]" data-cid="yesreplace" value="yesreplace">{vtranslate('suppliercontractsid','RefillApplication')}</label></td><td class="fieldValue medium"><input name="popupReferenceModule" type="hidden" value="SupplierContracts"><input name="msuppliercontractsid[]" othername="suppliercontractsid" type="hidden" value="" data-multiple="0" class="sourceField" data-displayvalue=""><div class="row-fluid input-prepend input-append"> <input id="suppliercontractsid[displayyesreplace]_display" name="msuppliercontractsid[displayyesreplace]_display" type="text" class=" span7 	marginLeftZero autoComplete" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly="readonly"  placeholder="查找.."></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">签订日期</label></td><td class="fieldValue medium"><div class="input-append row-fluid"><div class="span10 row-fluid date form_datetime"><input type="text" class="span9 dateField1" name="msigndate[]" data-date-format="yyyy-mm-dd" readonly="readonly" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" ></div></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('havesignedcontract','RefillApplication')}</label></td><td class="fieldValue medium"><select class="chzn-select" data-cid="yesreplace" name="mhavesignedcontract[]" ><option value="alreadySigned">{vtranslate('alreadySigned','RefillApplication')}</option><option value="notSigned">{vtranslate('notSigned','RefillApplication')}</option></select></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('topplatform','RefillApplication')}</label><input type="hidden" name="msupprebate[]" data-cid="yesreplace" value=""></td><td class="fieldValue medium"><input name="mproductid[]" type="hidden" value="0" data-multiple="0" class="sourceField" data-displayvalue="" data-cid="yesreplace"><div class="row-fluid input-prepend input-append"><input id="mproductid_display[]" name="mproductid_display[]" type="text" class=" span7 	marginLeftZero autoComplete" value="" data-cid="yesreplace" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  placeholder="查找.." readonly="readonly"></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('amountpayable','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="input-large checknumber" data-cid="yesreplace" name="mamountpayable[]"  value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/></td></tr></tbody></table>';
            var otherProcurementSheet='<table class="table table-bordered blockContainer showInlineTable  detailview-table Duplicates" data-num="yesreplace"><thead><tr><th class="blockHeader" colspan="4"><img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"> <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png"data-mode="show" data-id="141" style="display: inline;"> &nbsp;&nbsp;充值明细&nbsp;&nbsp;<spanclass="label label-success">yesreplace</span><b class="pull-right"><button class="btn btn-small delbutton" type="button" data-id="yesreplace"><i class="icon-trash" title="删除充值明细"></i></button></b></th></tr></thead><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> {vtranslate('productservice','RefillApplication')}</label></td><td class="fieldValue medium"><select class="chzn-select" data-cid="yesreplace" name="mproductservice[]" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-cid="yesreplace"></select></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><input type="hidden" name="motherprocurement[]" data-cid="yesreplace" value="yesreplace">{vtranslate('suppliercontractsid','RefillApplication')}</label></td><td class="fieldValue medium"><input name="popupReferenceModule" type="hidden" value="SupplierContracts"><input name="msuppliercontractsid[]" othername="suppliercontractsid" type="hidden" value="" data-multiple="0" class="sourceField" data-displayvalue=""><div class="row-fluid input-prepend input-append"><input id="suppliercontractsid[displayyesreplace]_display" name="msuppliercontractsid[displayyesreplace]_display" type="text" class=" span7 	marginLeftZero autoComplete" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly="readonly" placeholder="查找.."></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">签订日期</label></td><td class="fieldValue medium"><div class="input-append row-fluid"><div class="span10 row-fluid date form_datetime"><input type="text" class="span9 dateField1" name="msigndate[]" data-date-format="yyyy-mm-dd" readonly="readonly" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" ></div></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('havesignedcontract','RefillApplication')}</label></td><td class="fieldValue medium"><select class="chzn-select" data-cid="yesreplace" name="mhavesignedcontract[]" ><option value="alreadySigned">{vtranslate('alreadySigned','RefillApplication')}</option><option value="notSigned">{vtranslate('notSigned','RefillApplication')}</option></select></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('topplatform','RefillApplication')}</label><input type="hidden" name="msupprebate[]" data-cid="yesreplace" value=""></td><td class="fieldValue medium"><input name="mproductid[]" type="hidden" value="0" data-multiple="0" class="sourceField" data-displayvalue="" data-cid="yesreplace"><div class="row-fluid input-prepend input-append"><input id="mproductid_display[]" name="mproductid_display[]" type="text" class=" span7 	marginLeftZero autoComplete" value="" data-cid="yesreplace" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  placeholder="查找.." readonly="readonly"></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('purchaseamount','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="span9 dateField" name="mpurchaseamount[]" readonly="readonly" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" ></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('purchaseprice','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="input-large checknumber" data-cid="yesreplace" name="mpurchaseprice[]"  value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('purchasequantity','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" class="input-large checknumber" data-cid="yesreplace" name="mpurchasequantity[]"  value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/></td></tr></tbody></table>';
            var NonMediaExtractionSheet='<table class="table table-bordered blockContainer showInlineTable  detailview-table Duplicates" data-num="yesreplace"><thead><tr><th class="blockHeader" colspan="4"><img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"> <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png"data-mode="show" data-id="141" style="display: inline;"> &nbsp;&nbsp;充值明细&nbsp;&nbsp;<spanclass="label label-success">yesreplace</span><b class="pull-right"><button class="btn btn-small delbutton" type="button" data-id="yesreplace"><i class="icon-trash" title="删除充值明细"></i></button></b></th></tr></thead><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> {vtranslate('productservice','RefillApplication')}</label></td><td class="fieldValue medium"><select class="chzn-select" data-cid="yesreplace" name="mproductservice[]" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-cid="yesreplace"></select></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><input type="hidden" name="mnonmediaextraction[]" data-cid="yesreplace" value="yesreplace">{vtranslate('suppliercontractsid','RefillApplication')}</label></td><td class="fieldValue medium"><input name="popupReferenceModule" type="hidden" value="SupplierContracts"><input name="msuppliercontractsid[]" othername="suppliercontractsid" type="hidden" value="" data-multiple="0" class="sourceField" data-displayvalue=""><div class="row-fluid input-prepend input-append"><input id="suppliercontractsid[displayyesreplace]_display" name="msuppliercontractsid[displayyesreplace]_display" type="text" class=" span7 	marginLeftZero autoComplete" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly="readonly" placeholder="查找.."></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">签订日期</label></td><td class="fieldValue medium"><div class="input-append row-fluid"><div class="span10 row-fluid date form_datetime"><input type="text" class="span9 dateField1" name="msigndate[]" data-date-format="yyyy-mm-dd" readonly="readonly" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" ></div></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('havesignedcontract','RefillApplication')}</label></td><td class="fieldValue medium"><select class="chzn-select" data-cid="yesreplace" name="mhavesignedcontract[]" ><option value="alreadySigned">{vtranslate('alreadySigned','RefillApplication')}</option><option value="notSigned">{vtranslate('notSigned','RefillApplication')}</option></select></td></tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('topplatform','RefillApplication')}</label><input type="hidden" name="msupprebate[]" data-cid="yesreplace" value=""></td><td class="fieldValue medium"><input name="mproductid[]" type="hidden" value="0" data-multiple="0" class="sourceField" data-displayvalue="" data-cid="yesreplace"><div class="row-fluid input-prepend input-append"><input id="mproductid_display[]" name="mproductid_display[]" type="text" class=" span7 	marginLeftZero autoComplete" value="" data-cid="yesreplace" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  placeholder="查找.." readonly="readonly"></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('totalgrossprofit','RefillApplication')} </label></td>	<td class="fieldValue medium"><input type="text" class="input-large" name="mtotalgrossprofit[]" value="" data-cid="yesreplace"/></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('purchaseamount','RefillApplication')}</label></td><td class="fieldValue medium"><input type="text" cclass="input-large" name="mpurchaseamount[]" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" ></td></tr></tbody></table>';
        </script>
{/strip}