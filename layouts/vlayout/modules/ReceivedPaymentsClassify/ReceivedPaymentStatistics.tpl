{strip}
    <link rel="stylesheet" type="text/css" href="libraries/font-awesome/css/font-awesome.min.css">
    <style>
        .table{ font-size: 12px;}
        .table-bordered { border-collapse:collapse; }
        .panel-title { font-size:15px;color:#333;font-weight:bold;margin: 10px 0 20px; text-align: center}
        input[disabled], input[readonly]{ cursor:default; }
        #btn-save2pic{ position:absolute;top:15px;right:0;font-size:16px;color:#1296db;cursor:pointer;}
    </style>
<div style="margin: 0 auto;padding: 10px 20px;font-size:12px;">
    <div style="margin: 0 auto;padding: 10px 20px;font-size: 12px;text-align: center">
        <div style="display:flex;justify-content:center;align-items:center;margin:20px 0;">
            <span>日期</span><input type="text" id="startmonth" value="{$startMonth}" style="width: 120px;margin:0 10px;" readonly><span>至</span><input type="text" id="endmonth" value="{$endMonth}" style="width: 120px;margin:0 10px;" readonly>
            <select id="select-type" style="margin:0 10px 0 0;" multiple>
                {foreach $typeList as $key =>$items}
                <optgroup label="{$key}">
                    {foreach $items as $key =>$item}
                    <option value="{$key}">{$item}</option>
                    {/foreach}
                </optgroup>
                {/foreach}
            </select>
            <button type="button" class="layui-btn layui-btn-normal" id="btn-search" style="margin-left: 20px;">查询</button>
        </div>
        <div style="position:relative;">
        <div class="panel-title" style="position:relative;">回款统计表</div>
        <span class="fa fa-download" id="btn-save2pic"></span>
        <table class="table table-bordered" id="statistics-table" style="min-width: 1420px;overflow: auto;">
            <thead>
                <tr>
                    <th>一级分类</th>
                    <th>二级分类</th>
                    <th>当期回款数据—已匹配</th>
                    <th>当期回款数据—未匹配</th>
                    <th>当期回款数据总额</th>
                    <th>当期计划数</th>
                    <th>当期计划数据占比</th>
                    <th>上期回款数</th>
                    <th>回款同比</th>
                    <th>去年同期回款数</th>
                    <th>回款环比</th>
                    <th>当年回款总额</th>
                </tr>
            </thead>
            <tbody>
            {foreach $ruleList as $parentname=>$prule}
                {assign var = rulenunm value = count($prule)}
                {foreach $prule as $k=>$rule}
                    <tr id="plan{$rule['id']}" data-id="{$rule['id']}">
                        {if $k eq 0}
                            <td rowspan="{$rulenunm}">{$parentname}</td>
                        {/if}
                        <td>{$rule['name']}</td>
                        <td class="month month-1"></td>
                        <td class="month month-2"></td>
                        <td class="month month-3"></td>
                        <td class="month month-4"></td>
                        <td class="month month-5"></td>
                        <td class="month month-6"></td>
                        <td class="month month-7"></td>
                        <td class="month month-8"></td>
                        <td class="month month-9"></td>
                        <td class="month month-10"></td>
                        <td class="month month-11"></td>
                        <td class="month month-12"></td>
                    </tr>
                {/foreach}
            {/foreach}
            </tbody>
        </table>
        </div>
    </div>
    <div class="panel-title">当期回款折线图</div>
    <div id="statisticsChart" style="width: 100%; height:390px"></div>
    </div>
</div>
    <link rel="stylesheet" type="text/css" href="libraries/jquery/layui/css/layui.css">
    <script type="text/javascript" src="libraries/jquery/layui/layui.js"></script>
    <script type="text/javascript" src="libraries/jquery/chosen/chosen.jquery.min.js"></script>
    <script type="text/javascript" src="libraries/echarts5.2.2/echarts.min.js"></script>
    <script type="text/javascript" src="libraries/canvas/html2canvas.min.js"></script>
    <script type="text/javascript" src="libraries/canvas/canvas2image.js"></script>
    <script>
        var layer;
        var myChart = echarts.init(document.getElementById('statisticsChart'));
        layui.use(['layer', 'laydate'], function(){
            layer = layui.layer;
            var laydate = layui.laydate;
            laydate.render({
                elem: '#startmonth,#endmonth',
                type: 'month',
                min: '2010-01-01',
                max: '{date('Y-m-d')}',
                btns: ['clear', 'confirm']
            });
        });
        getStatistics();
        $('#select-type').chosen({
            include_group_label_in_selected:true,
            search_contains: true,
            disable_search_threshold: 2
        });
        $(document).on('click', '#btn-search', function () {
            getStatistics();
        }).on('click', '#btn-save2pic', function () {
            var dom = document.querySelector('#statistics-table');
            html2canvas(dom).then(function(canvas) {
                var w = dom.offsetWidth;
                var h = dom.offsetHeight;
                var type = 'png';
                var f = '回款统计表';
                Canvas2Image.saveAsImage(canvas, w, h, type, f);
            });
        });
        function getStatistics()
        {
            var startMonth = $('#startmonth').val();
            var endMonth = $('#endmonth').val();
            if(startMonth > endMonth) {
                layer.msg('开始月份应早于结束月份', { icon: 0,time: 1500 });
                return false;
            }
            var ruleIds = $('#select-type').val();
            if(!ruleIds) {
                ruleIds = [];
            }
            $.ajax({
                url:'index.php',
                type:'POST',
                data: {
                    module: 'ReceivedPaymentsClassify',
                    action: 'BasicAjax',
                    mode: 'getClassifyData',
                    startMonth: startMonth,
                    endMonth: endMonth,
                    ruleIds: ruleIds
                },
                beforeSend:function() {
                    layer.load();
                },
                complete:function() {
                    layer.closeAll('loading');
                },
                success:function (data) {
                    if(data.result) {
                        var htmlstr= '';
                        var classifyData = data.result.classifyData;
                        for (const k in classifyData) {
                            var pdata = classifyData[k];
                            var num = pdata['value'].length;
                            for (const kk in pdata['value']) {
                                htmlstr +='<tr>';
                                if (kk==0) {
                                    htmlstr += '<td rowspan="' + num + '">' + pdata['key'] + '</td>';
                                }
                                var item = pdata['value'][kk];
                                for (const j in item) {
                                    htmlstr +='<td>' + item[j] +'</td>';
                                }
                            }
                            htmlstr +='</tr>';
                        }
                        $('#statistics-table tbody').html(htmlstr);
                        var option = {
                            title: {
                                text: ''
                            },
                            tooltip: {
                                trigger: 'axis'
                            },
                            legend: {
                                data: null,
                            },
                            grid: {
                                left: '5%',
                                right: '5%',
                                bottom: '5%',
                                containLabel: true
                            },
                            toolbox: {
                                feature: {
                                    saveAsImage: {
                                        name: '当期回款折线图',
                                        title: '',
                                        icon: 'image://data:image/png;base64,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'
                                    },
                                },
                                right: 28
                            },
                            xAxis: {
                                type: 'category',
                                boundaryGap: false,
                                data: data.result.eChartsField
                            },
                            yAxis: {
                                name:'金额(元)',
                                axisLabel: {
                                    formatter: function (value, index) {
                                        if (value >= 100000000) {
                                            return value / 100000000 + "亿";
                                        } else if (value >= 10000) {
                                            return value / 10000 + "万";
                                        } else {
                                            return value;
                                        }
                                    },
                                }
                            },
                            series:  data.result.eChartsData
                        };
                        myChart.clear();
                        myChart.setOption(option);
                        window.onresize = myChart.resize;
                    }
                }
            })
        }
    </script>
{/strip}
