{strip}


    {if $UPDATETHIS}
        <table class="table table-bordered equalSplit detailview-table"><thead>
            <form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php" enctype="multipart/form-data">
        <tr><th colspan="2">客户担保金额设置</th></tr></thead><tbody>


            <tr><td><label class="pull-right">客户</label></td>
                <td>
                    <label class="pull-left">
                        {literal}
                        <input name="popupReferenceModule" type="hidden" value="Accounts"><input name="accountid" type="hidden" value="" data-multiple="0" class="sourceField" data-displayvalue="" data-fieldinfo="{"mandatory":false,"presence":true,"quickcreate":false,"masseditable":true,"defaultvalue":false,"type":"reference","name":"accountid","label":"\u5ba2\u6237"}"><div class="row-fluid input-prepend input-append"><span class="add-on clearReferenceSelection cursorPointer"><i id="RefillApplication_editView_fieldName_accountid_clear" class="icon-remove-sign" title="清除"></i></span><input id="accountid_display" name="accountid_display" type="text" class=" span7 	marginLeftZero autoComplete" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{"mandatory":false,"presence":true,"quickcreate":false,"masseditable":true,"defaultvalue":false,"type":"reference","name":"accountid","label":"\u5ba2\u6237"}" placeholder="查找.."><span class="add-on relatedPopup cursorPointer"><i id="RefillApplication_editView_fieldName_accountid_select" class="icon-search relatedPopup" title="选择"></i></span></div>
                        {/literal}
                    </label>
                </td>
            </tr>
            <tr><td><label class="pull-right">一级担保人</label></td>
                <td>
                    <label class="pull-left">
                        <select id="userid" name="userid" class="chzn-select">
                            {foreach key=index item=value from=$USER}
                                <option value="{$value.id}">{$value.last_name}</option>
                            {/foreach}
                        </select>
                    </label>
                </td>
            </tr>
            <tr><td><label class="pull-right">一级担保金额</label></td>
                <td>
                    <label class="pull-left">
                        <input type="number" id="unitprice" class="input-large nameField"/>
                    </label>
                </td>
            </tr>
            <tr><td><label class="pull-right">二级担保人</label></td>
                <td>
                    <label class="pull-left">
                        <select id="twoleveluserid" name="twoleveluserid" class="chzn-select">
                            {foreach key=index item=value from=$USER}
                                <option value="{$value.id}">{$value.last_name}</option>
                            {/foreach}
                        </select>
                    </label>
                </td>
            </tr>
            <tr><td><label class="pull-right">二级担保金额</label></td>
                <td>
                    <label class="pull-left">
                        <input type="number" id="twounitprice" class="input-large nameField"/>
                    </label>
                </td>
            </tr>
            <tr><td><label class="pull-right">三级担保人</label></td>
                <td>
                    <label class="pull-left">
                        <select id="threeleveluserid" name="threeleveluserid" class="chzn-select">
                            {foreach key=index item=value from=$USER}
                                <option value="{$value.id}">{$value.last_name}</option>
                            {/foreach}
                        </select>
                    </label>
                </td>
            </tr>
            <tr><td><label class="pull-right">三级担保金额</label></td>
                <td>
                    <label class="pull-left">
                        <input type="number" id="threeunitprice" class="input-large nameField"/>
                    </label>
                </td>
            </tr>
            </form>
            <tr><td style="text-align: center" colspan="2"><button class="btn btn-primary" id="savedepartuser">保存</button></td></tr>
        </tbody></table>{/if}
    <div style="margin-top:10px;">
        <div class="row-fluid span12" id="c">
        <div id="msg" style="height:20px;margin:0 auto;border:1px solid #ccc;border-bottom: none;padding-top:20px;"></div>
            <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;">
                <div id="bartable1" class="span12" style="height:490px;cursor:pointer;">
                    <table id="tbl_Detail" class="table listViewEntriesTable" width="100%"><thead><tr>
                            <th nowrap><b>客户</b></th>
                            <th nowrap><b>担保人</b></th>
                            <th nowrap><b>最低担保金额</b></th>
                            <th nowrap><b>2级担保人</b></th>
                            <th nowrap><b>2级担保金额</b></th>
                            <th nowrap><b>3级担保人</b></th>
                            <th nowrap><b>3级担保金额</b></th>
                            <th nowrap><b>创建人</b></th>
                            <th nowrap><b>更改人</b></th>
                            <th nowrap><b>更改时间</b></th>
                            <th nowrap><b>操作</b></th>
                           </tr>
                        </thead><tbody>
                        {foreach item=value from=$LISTDUSER}

                        <tr{if $value['deleted'] eq 1} style="background-color: #ccc;"{/if}>
                            <td nowrap><b>{$value['accountname']}</b></td>
                            <td nowrap><b>{$value['username']}</b></td>
                            <td nowrap><b>{$value['unitprice']}</b></td>
                            <td nowrap><b>{$value['twousername']}</b></td>
                            <td nowrap><b>{$value['twounitprice']}</b></td>
                            <td nowrap><b>{$value['threeusername']}</b></td>
                            <td nowrap><b>{$value['threeunitprice']}</b></td>
                            <td nowrap><b>{$value['createdname']}</b></td>
                            <td nowrap><b>{$value['deletedname']}</b></td>
                            <td nowrap><b>{$value['deleteddate']}</b></td>
                            <td nowrap>{if $UPDATETHIS}<b>{if $value['deleted'] neq 1}<i title="删除" class="icon-trash alignMiddle deleteRecord" data-id="{$value['accountrechargeguaranteeid']}"  style="cursor:pointer"></i></a>{else}已修改{/if}</b>{/if}</td>
                        </tr>
                        {/foreach}
                    </tbody></table>
                </div>
                <div class="clearfix"></div></div>
            </div>
        </div>

    </div>

    {literal}
    <script>
       $(document).ready(function(){
            $('#savedepartuser').click(function(){
                var params={};
                var module = app.getModuleName();

                var userid=$("select[name='userid']").val();
                var twoleveluserid=$("select[name='twoleveluserid']").val();

                var unitprice=$("#unitprice").val()*1;
                var twounitprice=$("#twounitprice").val()*1;
                var threeleveluserid=$("select[name='threeleveluserid']").val();
                var threeunitprice=$("#threeunitprice").val()*1;
                if(unitprice<=0){
                    Vtiger_Helper_Js.showPnotify({text :"担保金额不能为空!",title :'信息必填'});
                    return false;
                }
                if(twounitprice<=0){
                    Vtiger_Helper_Js.showPnotify({text :"二级担保金额不能为空!",title :'信息必填'});
                    return false;
                }
                var accountid=$('input[name="accountid"]').val();
                if(accountid<=0){
                    Vtiger_Helper_Js.showPnotify({text :"客户不能为空!",title :'信息必填'});
                    return false;
                }
                params['action']='BasicAjax';
                params['module']=module;
                params['mode']='setAccountChargeGuarantee';
                params['userid']=userid;
                params['twoleveluserid']=twoleveluserid;
                params['unitprice']=unitprice;
                params['twounitprice']=twounitprice;
                params['threeleveluserid']=threeleveluserid;
                params['threeunitprice']=threeunitprice;
                params['accountid']=accountid;


                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : '正在处理,请耐心等待哟',
                    'position' : 'html',
                    'blockInfo' : {'enabled' : true}
                });
                AppConnector.request(params).then(function (data){
                        progressIndicatorElement.progressIndicator({
                            'mode' : 'hide'
                        });
                        window.location.reload();
                });
            });

            $('.listViewEntriesTable').on('click','.deleteRecord',function(){
                var msg={
                    'message':'确定要删除该用户的权限吗'

                };
                var id=$(this).data("id")

                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e) {
                    var params = {};
                    var module = app.getModuleName();
                    params['id'] =id;
                    params['action'] = 'BasicAjax';
                    params['module'] = module;
                    params['mode'] = 'delAccountChargeGuarantee';

                    AppConnector.request(params).then(function (data) {
                        window.location.reload();
                    });
                });
            });

        jQuery('#tbl_Detail').DataTable({
            language: {"sProcessing":   "处理中...",	"sLengthMenu":   "显示 _MENU_ 项结果","sZeroRecords":  "没有匹配结果","sInfo":         "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",	"sInfoEmpty":    "显示第 0 至 0 项结果，共 0 项","sInfoFiltered": "(由 _MAX_ 项结果过滤)","sInfoPostFix":  "","sSearch":"当前页快速检索:","sUrl":"","sEmptyTable":     "表中数据为空","sLoadingRecords": "载入中...",
                "sInfoThousands":  ",",	"oPaginate": {"sFirst":"首页","sPrevious": "上页","sNext":"下页","sLast":"末页"},
                "oAria": {"sSortAscending":  ": 以升序排列此列","sSortDescending": ": 以降序排列此列"}},
            scrollY:"400px",
            sScrollX:"disabled",
            //paging: false,
            //searching: false,
            aLengthMenu: [ 10, 20, 50, 100, ],
            fnDrawCallback:function(){
            }
        });

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
       });
    </script>
    {/literal}
{include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}
