{strip}
    <form action="index.php?module=ReceivedPayments&view=List&public=ExportRID" method="post">
    <table class="table table-bordered equalSplit detailview-table"><thead>
        <th colspan="2">回款合同导出</th></thead><tbody>
        <tr><td style="text-align: right">部门
            </td><td>
                <select id="department_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched" name="department">
                    {foreach key=index item=value from=$DEPARTMENT}
                        <option value="{$index}">{$value}</option>
                    {/foreach}
                </select>
            </td>
        </tr>
        </tr>
        <tr><td style="text-align: right">选择时间</td><td><label class="pull-left"><input type="radio" name="timeselected" value="1" checked="">到账时间</label><span class="pull-left">&nbsp;</span><label class="pull-left"><input type="radio" name="timeselected" value="2">匹配时间</label></td></tr>
        <tr><td style="text-align: right">导出时间</td>
            <td>
                <div id="app">
                    <template>
                        <div class="block">
                            <el-date-picker v-model="value2" name="fs"  @change="dateChange" type="daterange" align="center" end-placeholder="结束日期" start-placeholder="开始日期">
                            </el-date-picker>
                        </div>
                    </template>
                </div>
            </td>
            <input type="hidden" name="datatime" value="{date('Y-m-d')}">
            <input type="hidden" name="enddatatime" value="{date('Y-m-d',strtotime("-90 days"))}">
        </tr>
        <tr><td style="text-align: right">导出格式
            </td><td>
                <select id="exportFormat" class="chzn-select referenceModulesList streched" name="exportFormat">
                        <option value="excel">excel</option>
                        <option value="csv">csv</option>
                </select>
            </td>
        </tr>
        <tr><td colspan="2" style="text-align: center"><button class="btn btn-primary">导出</button></td></tr>
        </tbody></table>
        </form>
    <script src="/libraries/jquery/chosen/chosen.jquery.min.js"></script>
    <script src="https://unpkg.com/vue/dist/vue.js"></script>
    <script src="https://unpkg.com/element-ui@2.15.2/lib/index.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/element-ui@2.15.2/lib/theme-chalk/index.css">
    {literal}
    <script>
        initDatePicker();
        $('.chzn-select').chosen();

        $("input[name='timeselected']").change(function () {
            var type='datetimerange';
            if($(this).val()=='1'){
                type='daterange';
            }
            $("#app").html('<template><div class="block"><el-date-picker v-model="value2" name="fs"  @change="dateChange" type="'+type+'" align="center" end-placeholder="结束日期" start-placeholder="开始日期"></el-date-picker></div></template>');
            initDatePicker();
        });

        /**
         * 初始化initDatePickert
         * @returns {{value2: (number|Date)[]}}
         */
        function initDatePicker() {
            var Main = {
                data() {
                    return {
                        value2: [new Date().getTime() - 3600 * 1000 * 24 * 90,new Date()],
                    };
                },
                methods: {
                    dateChange(value) {
                        $("input[name='datatime']").val(dateToString(value[0]));
                        $("input[name='enddatatime']").val(dateToString(value[1]));
                    },
                }
            };
            var Ctor = Vue.extend(Main);
            new Ctor().$mount('#app');
        }

        /**
         * js date对象转字符串
         * @param date
         * @returns {string}
         */
        function dateToString(date) {
            var y = date.getFullYear();
            var m = date.getMonth() + 1;
            m = m < 10 ? ('0' + m) : m;
            var d = date.getDate();
            d = d < 10 ? ('0' + d) : d;
            var h = date.getHours();
            h = h < 10 ? ('0' + h) : h;
            var minute = date.getMinutes();
            minute = minute < 10 ? ('0' + minute) : minute;
            var second= date.getSeconds();
            second = minute < 10 ? ('0' + second) : second;
            return y + '-' + m + '-' + d+' '+h+':'+minute+':'+ second;
        }
    </script>
{/literal}
{include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}
