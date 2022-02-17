{strip}
    <table class="table table-bordered equalSplit detailview-table">
        <thead><th colspan="2">一、数据条件</th></thead>
        <tbody>
            <tr>
                <td style="text-align: right"><span class="redColor">*</span>工作流</td>
                <td>
                    <select id="workflows" class="chzn-select referenceModulesList streched" name="workflows">
                        <option value="">请选择</option>
                        {foreach key=index item=value from=$workflowList}
                            <option value="{$value['workflowsid']}">{$value['workflowsname']}</option>
                        {/foreach}
                    </select>
                </td>
            </tr>
            <tr>
                <td style="text-align: right"><span class="redColor">*</span>客户</td>
                <td>
                    {literal}
                        <input name="popupReferenceModule" type="hidden" value="Accounts"><input id="accountid" name="accountid" type="hidden" value="" data-multiple="0" class="sourceField" data-displayvalue="" data-fieldinfo="{"mandatory":false,"presence":true,"quickcreate":false,"masseditable":true,"defaultvalue":false,"type":"reference","name":"accountid","label":"\u5ba2\u6237"}"><div class="row-fluid input-prepend input-append"><span class="add-on clearReferenceSelection cursorPointer"><i id="RefillApplication_editView_fieldName_accountid_clear" class="icon-remove-sign" title="清除"></i></span><input id="accountid_display" name="accountid_display" type="text" class=" span7 	marginLeftZero autoComplete" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{"mandatory":false,"presence":true,"quickcreate":false,"masseditable":true,"defaultvalue":false,"type":"reference","name":"accountid","label":"\u5ba2\u6237"}" placeholder="查找.."><span class="add-on relatedPopup cursorPointer"><i id="RefillApplication_editView_fieldName_accountid_select" class="icon-search relatedPopup" title="选择"></i></span></div>
                    {/literal}
                </td>
            </tr>
            <tr>
                <td style="text-align: right"><span class="redColor">*</span>产品服务</td>
                <td>
                    <select id="product" name="product" class="chzn-select referenceModulesList streched">
                        <option value="">请选择</option>
                        {foreach key=index item=value from=$productList}
                            <option value="{$value['productid']}">{$value['productname']}</option>
                        {/foreach}
                    </select>
                </td>
            </tr>
            <tr>
                <td style="text-align: right"><span class="redColor">*</span>客户返点类型</td>
                <td>
                    <select id="accountRebateType" name="accountRebateType" class="chzn-select referenceModulesList streched">
                        <option value="">请选择</option>
                        <option value="CashBack">返现</option>
                        <option value="GoodsBack">返货</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td style="text-align: right;"><span class="redColor">*</span>客户返点</td>
                <td>
                    <label class="pull-left" style="height:30px;line-height:30px;">
                        <input class="span9 dateField"type="text" name="discount" id="discount"  autocomplete="off">%
                    </label>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center"><button class="btn btn-primary" id="findNum">查找</button><p id="num" style="display: inline-block;margin-left: 100px"></p></td>
            </tr>
        </tbody>
    </table>
    <table class="table table-bordered equalSplit detailview-table">
        <thead><th colspan="2">二、调整后数据</th></thead>
        <tbody>
        <tr>
            <td style="text-align: right;"><span class="redColor">*调整后客户返点</span></td>
            <td>
                <label class="pull-left" style="height:30px;line-height:30px;">
                    <input class="span9 dateField"type="text" name="accountRebate" id="accountRebate"  autocomplete="off">%
                </label>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center"><button class="btn btn-success" id="change">调整</button></td>
        </tr>
        </tbody>
    </table>
    <script src="/libraries/jquery/chosen/chosen.jquery.min.js"></script>
{literal}
    <script>
        $(document).ready(function(){
            $('body').on('click','.relatedPopup',function(e){
                openPopUp(e);
            });
            function openPopUp(e){
                var thisInstance = this;
                var parentElem = jQuery(e.target).closest('td');

                var params = getPopUpParams(parentElem);

                var isMultiple = false;
                if(params.multi_select) {
                    isMultiple = true;
                }

                var sourceFieldElement = jQuery('input[class="sourceField"]',parentElem);

                var prePopupOpenEvent = jQuery.Event(Vtiger_Edit_Js.preReferencePopUpOpenEvent);
                sourceFieldElement.trigger(prePopupOpenEvent);

                if(prePopupOpenEvent.isDefaultPrevented()) {
                    return ;
                }

                var popupInstance =Vtiger_Popup_Js.getInstance();
                popupInstance.show(params,function(data){
                    var responseData = JSON.parse(data);
                    var dataList = new Array();
                    var idList = new Array();
                    idList['id']=new Array();
                    idList['name']=new Array();
                    for(var id in responseData){
                        var data = {
                            'name' : responseData[id].name,
                            'id' : id
                        }
                        dataList.push(data);
                        if(!isMultiple) {
                            setReferenceFieldValue(parentElem, data);
                        }else{
                            idList['id'].push(id);
                            idList['name'].push(responseData[id].name);
                        }
                    }

                    if(isMultiple) {
                        setMultiReferenceFieldValue(parentElem, idList);
                        //sourceFieldElement.trigger(Vtiger_Edit_Js.refrenceMultiSelectionEvent,{'data':dataList});
                    }
                    sourceFieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent,{'data':responseData});
                });
            }
            function getPopUpParams(container) {
                var params = {};
                var sourceModule = app.getModuleName();
                var popupReferenceModule = jQuery('input[name="popupReferenceModule"]',container).val();
                var sourceFieldElement = jQuery('input[class="sourceField"]',container);
                var sourceField = sourceFieldElement.attr('name');
                var sourceRecordElement = jQuery('input[name="record"]');
                var sourceRecordId = '';
                if(sourceRecordElement.length > 0) {
                    sourceRecordId = sourceRecordElement.val();
                }

                var isMultiple = false;
                if(sourceFieldElement.data('multiple') == true){
                    isMultiple = true;
                }

                var params = {
                    'module' : popupReferenceModule,
                    'src_module' : sourceModule,
                    'src_field' : sourceField,
                    'src_record' : sourceRecordId
                }

                if(isMultiple) {
                    params.multi_select = true ;
                }
                return params;
            }
            function setReferenceFieldValue(container, params) {
                var sourceField = container.find('input[class="sourceField"]').attr('name');
                var fieldElement = container.find('input[name="'+sourceField+'"]');
                var sourceFieldDisplay = sourceField+"_display";
                var fieldDisplayElement = container.find('input[name="'+sourceFieldDisplay+'"]');
                var popupReferenceModule = container.find('input[name="popupReferenceModule"]').val();

                var selectedName = params.name;
                var id = params.id;

                fieldElement.val(id)
                fieldDisplayElement.val(selectedName);
                fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, {'source_module' : popupReferenceModule, 'record' : id, 'selectedName' : selectedName});

                fieldDisplayElement.validationEngine('closePrompt',fieldDisplayElement);
            }
            function setMultiReferenceFieldValue(container, params) {

                var sourceField = container.find('input[class="sourceField"]').attr('name');
                var fieldElement = container.find('input[name="'+sourceField+'"]');
                var sourceFieldDisplay = sourceField+"_display";
                var fieldDisplayElement = container.find('input[name="'+sourceFieldDisplay+'"]');
                var popupReferenceModule = container.find('input[name="popupReferenceModule"]').val();

                var selectedName = params.name.join("、");
                var id = params.id.join(",");
                fieldElement.val(id);
                fieldDisplayElement.val(selectedName);
                //.attr('readonly',true);
                fieldElement.trigger(Vtiger_Edit_Js.refrenceMultiSelectionEvent, {'source_module' : popupReferenceModule, 'record' :id, 'selectedName' : selectedName});
            }

            $("#findNum").click(function () {
                var workflow=$("#workflows").val();
                var accountid=$("#accountid").val();
                var product=$("#product").val();
                var accountRebateType=$("#accountRebateType").val();
                var discount=$("#discount").val();
                if(!workflow){
                    alert("请选择工作流");
                    return false
                }
                if(!accountid){
                    alert("请选择客户");
                    return false
                }
                if(!product){
                    alert("请选择产品服务");
                    return false
                }
                if(!accountRebateType){
                    alert("请选择客户返点类型");
                    return false
                }
                if(!discount){
                    alert("请填写客户返点");
                    return false
                }
                var params=new Array();
                params['module'] = 'AccountPlatform';
                params['view'] = 'BatchEditAccountRebate';
                params['public'] = 'findNum';
                params['workflow'] = workflow;
                params['accountid'] = accountid;
                params['product'] = product;
                params['accountRebateType'] = accountRebateType;
                params['discount'] = discount;
                var Message = app.vtranslate('正在查询...');
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : Message,
                    'position' : 'html',
                    'blockInfo' : {'enabled' : true}
                });
                AppConnector.request(params).then(
                    function(data) {
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        data=JSON.parse(data);
                        $("#num").html('符合以上条件的数据有{'+data.result.num+'}条');
                    },
                    function(error,err){

                    }
                );
            });

            $("#change").click(function () {
                var workflow=$("#workflows").val();
                var accountid=$("#accountid").val();
                var product=$("#product").val();
                var accountRebateType=$("#accountRebateType").val();
                var discount=$("#discount").val();
                var accountRebate=$("#accountRebate").val();
                if(!workflow){
                    alert("请选择工作流");
                    return false
                }
                if(!accountid){
                    alert("请选择客户");
                    return false
                }
                if(!product){
                    alert("请选择产品服务");
                    return false
                }
                if(!accountRebateType){
                    alert("请选择客户返点类型");
                    return false
                }
                if(!discount){
                    alert("请填写客户返点");
                    return false
                }
                if(!accountRebate){
                    alert("请填写调整后客户返点");
                    return false
                }
                var params=new Array();
                params['module'] = 'AccountPlatform';
                params['view'] = 'BatchEditAccountRebate';
                params['public'] = 'change';
                params['workflow'] = workflow;
                params['accountid'] = accountid;
                params['product'] = product;
                params['accountRebateType'] = accountRebateType;
                params['discount'] = discount;
                params['accountRebate'] = accountRebate;
                var Message = app.vtranslate('正在调整...');
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : Message,
                    'position' : 'html',
                    'blockInfo' : {'enabled' : true}
                });
                AppConnector.request(params).then(
                    function(data) {
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        Vtiger_Helper_Js.showMessage({type:'success',text:"调整成功"});
                    },
                    function(error,err){

                    }
                );
            });
        });
    </script>
{/literal}
    {include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}
