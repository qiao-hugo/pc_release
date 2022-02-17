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
{assign var=FIELD_NAME value=$FIELD_MODEL->get('name')}
{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
{assign var=PICKLIST_VALUES value=$FIELD_MODEL->getAllDepartmentsTree(true,true)}
{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
{assign var="FIELD_VALUE_LIST" value=explode(' |##| ',$FIELD_MODEL->get('fieldvalue'))}
{*<!--
    <script src="https://cdn.jsdelivr.net/npm/vue@^2"></script>
    <script src="https://cdn.jsdelivr.net/npm/@riophae/vue-treeselect@^0.4.0/dist/vue-treeselect.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@riophae/vue-treeselect@^0.4.0/dist/vue-treeselect.min.css">
-->*}
    <script src="libraries/jquery/vue/vue.js"></script>
    <script src="libraries/jquery/vue/vue-treeselect.js"></script>
    <link rel="stylesheet" href="libraries/jquery/vue/vue-treeselect.css">
    <style>
        #app input { border:0 !important;outline:none; box-shadow:none; }
        .vue-treeselect__placeholder {
            color: #000;
        }
        .vue-treeselect__control{
            border:1px solid #aaa;
            border-radius:0;
        }
        .vue-treeselect{
            width:220px;
        }
    </style>
    <div id="app" class="select2-container select2-container-multi select2 select2-dropdown-open select2-search-field">
        <treeselect v-model="value" :multiple="true" :options="options" :default-expand-level="1" placeholder="选择多个选项"/>
    </div>
    <div style="display:none">
        <select multiple name="{$FIELD_MODEL->getFieldName()}[]" id="select_{$FIELD_MODEL->getFieldName()}" >
            {foreach item=PICKLIST_NAME key=PICKLIST_VALUE from=$PICKLIST_VALUES[1]}
                <option value="{$PICKLIST_VALUE}" >{vtranslate($PICKLIST_NAME, $MODULE)}</option>
            {/foreach}
        <select>
    </div>
    <script>
        Vue.component('treeselect', VueTreeselect.Treeselect);
        {if $FIELD_MODEL->get('fieldvalue') eq ''}
        var defaultValue = [];
        {else}
        var defaultValue = [{foreach item=VALUE from=$FIELD_VALUE_LIST}'{$VALUE}',{/foreach}];
        {/if}
        var options = {json_encode($PICKLIST_VALUES[0])};
        var Main = {
            data() {
                return {
                    value: defaultValue,
                    valueConsistsOf: 'BRANCH_PRIORITY',
                    options: options,
                };
            },
            watch: {
              'value': 'dataSelect'
            },
            methods: {
                dataSelect(val){
                    $('#select_{$FIELD_MODEL->getFieldName()}').html('');
                    if(val.length == 0){
                        return false;
                    }
                    var html = '';
                    for (var i = 0; i < val.length; i++) {
                        html += '<option value="'+val[i]+'" selected="selected">'+val[i]+'</option>';  
                    }
                    $('#select_{$FIELD_MODEL->getFieldName()}').html(html);
                }
            },
            
        };
        var Ctor = Vue.extend(Main);
        new Ctor().$mount('#app');
  </script>
{/strip}