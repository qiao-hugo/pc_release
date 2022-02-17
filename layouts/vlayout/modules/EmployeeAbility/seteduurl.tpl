{strip}
    <table class="table table-bordered blockContainer salesorderrayment_tab detailview-table" style="margin:0 auto;width: 90%">
        <thead>
        <tr>
            <th class="blockHeader positionstyle" style="text-align: center;">级别</th>
            <th class="blockHeader positionstyle" style="text-align: center;">达标任务</th>
            <th class="blockHeader positionstyle" style="text-align: center;">完成方法</th>
            <th class="blockHeader positionstyle" style="text-align: center;">课程地址</th>
            <th class="blockHeader positionstyle" style="text-align: center;">操作</th>
        </tr>
        </thead>
        <tbody>
        {foreach item=DATA key=key from=$DATAS}
            <tr>
                <td style="width: 5%;text-align: center;"><label >{vtranslate($DATA['stafflevel'],$MODULE)}</label></td>
                <td style="width: 10%;text-align: center;"><label >{vtranslate($DATA['columnname'],$MODULE)}</label></td>
                <td style="width: 20%;text-align: center;"><label class="columnname">{vtranslate("{$DATA['columnname']}_text", $MODULE)} </label></td>
                <td style="width: 20%;text-align: center;">
                    <label >
                        <input  class="eduurl" type="text" style="width: 80%" value="{$DATA['eduurl']}">
                    </label>
                </td>
                <td style="width: 5%;text-align: center;"><a class="subeduurl" style="cursor: pointer" data-columnnametext="{vtranslate("{$DATA['columnname']}_text", $MODULE)}" data-employeeabilitycolumnid="{$DATA['employeeabilitycolumnid']}" >保存</a></td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    <script>
        $(function () {
            $(".subeduurl").on("click",function (k, v) {
                var thisInstance= this;
                var employeeabilitycolumnid = $(this).data("employeeabilitycolumnid");
                eduurl = $(this).parent().parent().find(".eduurl").val();
                if(eduurl && !checkUrl(eduurl)){
                    alert("非链接地址");
                    $(this).parent().parent().find(".eduurl").val("");
                    return;
                }
                var params=[];
                params['action'] = 'ChangeAjax';
                params['module'] = 'EmployeeAbility';
                params['mode'] = 'setEduUrl';
                params['record'] = employeeabilitycolumnid;
                params['eduurl'] = eduurl;
                AppConnector.request(params).then(
                    function(data) {
                        console.log(data);
                        if(data.success){
                            alert(data.msg)
                        }
                    },
                    function(error,err){

                    }
                );
            })
        });

        function checkUrl(url1) {
            match2 = /^((http|https):\/\/)?(([A-Za-z0-9]+-[A-Za-z0-9]+|[A-Za-z0-9]+)\.)+([A-Za-z]+)[/\?\:]?.*$/;
            vol2 = match2.test(url1);
            console.log(vol2);
            return vol2;
        }

    </script>
{/strip}
