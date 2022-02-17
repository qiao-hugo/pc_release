{strip}

    <table class="table table-bordered equalSplit detailview-table" style="margin-right: 10px;"><thead>
        <th colspan="2"></th></thead><tbody>
        <tr><td style="text-align: right">部门
            </td><td>
                <select id="department_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched" name="department">
                    {foreach key=index item=value from=$DEPARTMENT}
                        <option value="{$index}">{$value}</option>
                    {/foreach}
                </select>
            </td></tr>
        <tr><td colspan="2" style="text-align: center"><button class="btn btn-primary">查看</button></td></tr>
        </tbody></table>
    <div class="insertdata" style="margin-right: 10px;">
     {include file='LineItemsDetailM.tpl'|@vtemplate_path:'SalesDaily'}
    </div>
    <script src="/libraries/jquery/chosen/chosen.jquery.min.js"></script>
    <script src="/libraries/jquery/datetimepicker/bootstrap-datetimepicker.min.js"></script>
    {literal}
    <script>
        $('.chzn-select').chosen();
        $(document).ready(function(){
            $('.btn-primary').click(function(){
                var departmentid=$('#department_editView_fieldName_dropDown').val();
                var urlParams={'module':'SalesDaily','view':'ListM',
                'department':departmentid};
                var progressIndicatorElement = jQuery.progressIndicator({
						'message' : '正在加载,请稍后',
						'position' : 'html',
						'blockInfo' : {
							'enabled' : true
						}
					});
                $.ajax({
                        url:'index.php',
                        data:urlParams,
                        success: function(data){
                        progressIndicatorElement.progressIndicator({
                            'mode' : 'hide'
                        })
                        jQuery('.insertdata').html(data);
                        }

                });
                return;
              AppConnector.requestPjax(urlParams).then(
			    function(data){
				/*progressIndicatorElement.progressIndicator({
					'mode' : 'hide'
				})*/
                jQuery('.insertdata').html(data);

			});
            });
        });

    </script>
    {/literal}
{include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}
