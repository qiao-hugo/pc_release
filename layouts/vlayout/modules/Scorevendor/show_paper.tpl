
<!-- 评分问卷 -->
<div id="paper">
	<div id="paper_head">
		<div class="title_content">
			{$SCOREMODELENTITY['scoremodel_name']}-评分问卷
			<span class="color_red score_total html_hide">{$SCOREMODELENTITY['scoretotal']}分</span>		
		</div>
	</div>
	<div style="height: 15px;"></div>
	<div id="paper_body">
		<form id="paper_form" action="index.php" method="post">
			<input type="hidden" name="module" value="Scorevendor">
			<input type="hidden" name="action" value="SaveScore">
			<input type="hidden" name="record" value="{$smarty.get.record}">
		{assign var=CONTENT value=$SCOREMODELENTITY['scoremodel_content']}

		{foreach from=$CONTENT item=CONTENT_ITEM key=INDEX name=foo}
			{if $CONTENT_ITEM['scorepaper_itme_type'] eq 'o_check'}
				<!-- 多选 -->
				<div class="question" data-num="{$INDEX}">
					<input type="hidden" name="question[{$smarty.foreach.foo.index+1}]" value="{$INDEX}">
					<div class="inner">
						<div class="title">
							<div class="title_text">
								<p>{$smarty.foreach.foo.index+1}. 
								{$CONTENT_ITEM['scorepaper_itme_explan']}
								<span class="color_red prompt">(必填)</span>
								</p>
							</div>
						</div>

						<div class="inputs">
							{foreach from=$CONTENT_ITEM['scorepaper_itme_scorepara_info'] item=PARA_SON}
							<label>
								<input  class="o_check_{$INDEX}" type="checkbox" {if in_array($PARA_SON['scoreparaid'], $CONTENT_ITEM['answer'])}checked{/if} name="o_check[{$INDEX}][]" value="{$PARA_SON['scoreparaid']}">
								<span class="checkbox_text">{$PARA_SON['scorepara_item']}</span>
							</label>
							{/foreach}
						</div>

						<div class="score o_check_score html_hide">
							<span>评分 : <span class="color_666">{$CONTENT_ITEM['score_item']}</span></span>&nbsp;&nbsp;&nbsp;&nbsp;
							<span>权重 : <span class="color_666">{$CONTENT_ITEM['scorepaper_itme_weight']}</span></span>&nbsp;&nbsp;&nbsp;&nbsp;
							<span>得分 : <span class="color_red">{$CONTENT_ITEM['score_actual']}</span></span>
						</div>
					</div>
				</div>
			{/if}

			{if $CONTENT_ITEM['scorepaper_itme_type'] eq 'o_select'}
				<!-- 下拉 -->
				<div class="question" data-num="{$INDEX}">
					<input type="hidden" name="question[{$smarty.foreach.foo.index+1}]" value="{$INDEX}">
					<div class="inner">
						<div class="title">
							<div class="title_text">
								<p>{$smarty.foreach.foo.index+1}. {$CONTENT_ITEM['scorepaper_itme_explan']}
									<span class="color_red prompt">(必填)</span>
								</p>
							</div>
						</div>

						<div class="inputs">
							<select class="form_select" name="o_select[{$INDEX}]">
								<option vlaue="0">请选择</option>
								{foreach from=$CONTENT_ITEM['scorepaper_itme_scorepara_info'] item=PARA_SON}
									<option {if $CONTENT_ITEM['answer'] eq $PARA_SON['scoreparaid']}selected{/if} value="{$PARA_SON['scoreparaid']}">{$PARA_SON['scorepara_item']}</option>
								{/foreach}
							</select>
						</div>
						<div class="score o_check_score html_hide">
							<span>评分 : <span class="color_666">{$CONTENT_ITEM['score_item']}</span></span>&nbsp;&nbsp;&nbsp;&nbsp;
							<span>权重 : <span class="color_666">{$CONTENT_ITEM['scorepaper_itme_weight']}</span></span>&nbsp;&nbsp;&nbsp;&nbsp;
							<span>得分 : <span class="color_red">{$CONTENT_ITEM['score_actual']}</span></span>
						</div>
					</div>
				</div>
			{/if}

			{if $CONTENT_ITEM['scorepaper_itme_type'] eq 'o_numberinterval'}
				<!-- 输入框 -->
				<div class="question" data-num="{$INDEX}">
					<input type="hidden" name="question[{$smarty.foreach.foo.index+1}]" value="{$INDEX}">
					<div class="inner">
						<div class="title">
							<div class="title_text">
								<p>{$smarty.foreach.foo.index+1}. {$CONTENT_ITEM['scorepaper_itme_explan']}
									<span class="color_red prompt">(必填)</span>
								</p>
							</div>
						</div>

						<div class="inputs">
							<input  class="form_input" type="text" name="o_numberinterval[{$INDEX}]" value="{$CONTENT_ITEM['answer']}">
						</div>

						<div class="score o_check_score html_hide">
							<span>评分 : <span class="color_666">{$CONTENT_ITEM['score_item']}</span></span>&nbsp;&nbsp;&nbsp;&nbsp;
							<span>权重 : <span class="color_666">{$CONTENT_ITEM['scorepaper_itme_weight']}</span></span>&nbsp;&nbsp;&nbsp;&nbsp;
							<span>得分 : <span class="color_red">{$CONTENT_ITEM['score_actual']}</span></span>
						</div>
					</div>
				</div>
			{/if}

			{if $CONTENT_ITEM['scorepaper_itme_type'] eq 'o_number'}
				<!-- 输入框 -->
				<div class="question" data-num="{$INDEX}">
					<input type="hidden" name="question[{$smarty.foreach.foo.index+1}]" value="{$INDEX}">
					<div class="inner">
						<div class="title">
							<div class="title_text">
								<p>{$smarty.foreach.foo.index+1}. {$CONTENT_ITEM['scorepaper_itme_explan']}
									<span class="color_red prompt">(必填)</span>
								</p>
							</div>
						</div>

						<div class="inputs">
							<input class="form_input" type="text" name="o_number[{$INDEX}]" value="{$CONTENT_ITEM['answer']}">
						</div>

						<div class="score o_check_score html_hide">
							<span>评分 : <span class="color_666">{$CONTENT_ITEM['score_item']}</span></span>&nbsp;&nbsp;&nbsp;&nbsp;
							<span>权重 : <span class="color_666">{$CONTENT_ITEM['scorepaper_itme_weight']}</span></span>&nbsp;&nbsp;&nbsp;&nbsp;
							<span>得分 : <span class="color_red">{$CONTENT_ITEM['score_actual']}</span></span>
						</div>
					</div>
				</div>
			{/if}

			{if $CONTENT_ITEM['scorepaper_itme_type'] eq 'o_radio'}
				<!-- 单选 -->
				<div class="question" data-num="{$INDEX}">
					<input type="hidden" name="question[{$smarty.foreach.foo.index+1}]" value="{$INDEX}">
					<div class="inner">
						<div class="title">
							<div class="title_text">
								<p>{$smarty.foreach.foo.index+1}. {$CONTENT_ITEM['scorepaper_itme_explan']}
									<span class="color_red prompt">(必填)</span>
								</p>
							</div>
						</div>

						<div class="inputs">
							{foreach from=$CONTENT_ITEM['scorepaper_itme_scorepara_info'] item=PARA_SON}
							<label>
								<input class="o_radio_{$INDEX}" type="radio" {if $CONTENT_ITEM['answer'] eq $PARA_SON['scoreparaid']}checked{/if} name="o_radio[{$INDEX}]" value="{$PARA_SON['scoreparaid']}">
								<span class="checkbox_text">{$PARA_SON['scorepara_item']}</span>
							</label>
							{/foreach}
						</div>

						<div class="score o_check_score html_hide">
							<span>评分 : <span class="color_666">{$CONTENT_ITEM['score_item']}</span></span>&nbsp;&nbsp;&nbsp;&nbsp;
							<span>权重 : <span class="color_666">{$CONTENT_ITEM['scorepaper_itme_weight']}</span></span>&nbsp;&nbsp;&nbsp;&nbsp;
							<span>得分 : <span class="color_red">{$CONTENT_ITEM['score_actual']}</span></span>
						</div>
					</div>
				</div>
			{/if}

			{if $CONTENT_ITEM['scorepaper_itme_type'] eq 'o_text'}
				<!-- 文本 -->
				<div class="question" data-num="{$INDEX}">
					<input type="hidden" name="question[{$smarty.foreach.foo.index+1}]" value="{$INDEX}">
					<div class="inner">
						<div class="title">
							<div class="title_text">
								<p>{$smarty.foreach.foo.index+1}. {$CONTENT_ITEM['scorepaper_itme_explan']}
									<span class="color_red prompt">(必填)</span>
								</p>
							</div>
						</div>

						<div class="inputs">
							<textarea class="form_textarea" rows="3" cols="200" name="o_text[{$INDEX}]">{$CONTENT_ITEM['answer']}</textarea>
						</div>

						<div class="score o_check_score html_hide">
							<span>评分 : <span class="color_666">{$CONTENT_ITEM['score_item']}</span></span>&nbsp;&nbsp;&nbsp;&nbsp;
							<span>权重 : <span class="color_666">{$CONTENT_ITEM['scorepaper_itme_weight']}</span></span>&nbsp;&nbsp;&nbsp;&nbsp;
							<span>得分 : <span class="color_red">{$CONTENT_ITEM['score_actual']}</span></span>
						</div>
					</div>
				</div>
			{/if}

		{/foreach}


		
		{*
		<!-- 输入框 -->
		<div class="question" data-num="2">
			<div class="inner">
				<div class="title">
					<div class="title_text">
						<p>2. 您的性别是？您的性别是？您的性别是？您的性别是？您的性别是？您的性别是？您的性别是？您的性别是？</p>
					</div>
				</div>

				<div class="inputs">
					<input type="text" name="">
					<input class="form_input" type="hidden" name="o_number_num_weight" value="10%">
				</div>

				<div class="score html_hide">
					<span>权重 : <span class="color_666">10%</span></span>&nbsp;&nbsp;&nbsp;&nbsp;
					<span>得分 : <span class="color_red">10</span></span>
					<input type="hidden" name="o_select_num_weight">
				</div>
			</div>
		</div>

		<!-- 单选 -->
		<div class="question" data-num="3">
			<div class="inner">
				<div class="title">
					<div class="title_text">
						<p>3. 您的性别是？</p>
					</div>
				</div>

				<div class="inputs">
					<label>
						<input type="radio" name="sex">
						<span class="checkbox_text">男</span>
					</label>
					<label>
						<input type="radio" name="sex">
						<span class="checkbox_text">女</span>
					</label>
					<input type="hidden" name="o_radio_num_weight" value="10%">
				</div>

				<div class="score o_radio_score html_hide">
					<span>权重 : <span class="color_666">10%</span></span>&nbsp;&nbsp;&nbsp;&nbsp;
					<span>得分 : <span class="color_red">10</span></span>
					<input type="hidden" name="o_select_num_weight">
				</div>
			</div>
		</div>

		<!-- 多选 -->
		<div class="question" data-num="4">
			<div class="inner">
				<div class="title">
					<div class="title_text">
						<p>4. 您的爱好？</p>
					</div>
				</div>

				<div class="inputs">
					<label>
						<input type="checkbox" name="aa">
						<span class="checkbox_text">篮球</span>
					</label>
					<label>
						<input type="checkbox" name="aa">
						<span class="checkbox_text">游戏</span>
					</label>
					<label>
						<input type="checkbox" name="aa">
						<span class="checkbox_text">足球</span>
					</label>
					<label>
						<input type="checkbox" name="aa">
						<span class="checkbox_text">排球</span>
					</label>

					<input type="hidden" name="o_check_num_weight" value="10%">
				</div>

				<div class="score o_check_score html_hide">
					<span>权重 : <span class="color_666">10%</span></span>&nbsp;&nbsp;&nbsp;&nbsp;
					<span>得分 : <span class="color_red">10</span></span>
					<input type="hidden" name="o_select_num_weight">
				</div>
			</div>
		</div>

		<!-- 文本 -->
		<div class="question" data-num="5">
			<div class="inner">
				<div class="title">
					<div class="title_text">
						<p>5. 您的性别是？您的性别是？您的性别是？您的性别是？您的性别是？您的性别是？您的性别是？您的性别是？您的性别是？您的性别是？</p>
					</div>
				</div>

				<div class="inputs">
					<textarea rows="3" cols="200" name=""></textarea>
					<input type="hidden" name="o_text_num_weight" value="10%">
				</div>

				<div class="score html_hide">
					<span>权重 : <span class="color_666">10%</span></span>&nbsp;&nbsp;&nbsp;&nbsp;
					<span>得分 : <span class="color_red">10</span></span>
					<input type="hidden" name="o_select_num_weight">
				</div>
			</div>
		</div>
		*}
		
		</form>
	</div>

	<div id="paper_footer">
		{*{if $IS_LOG neq 1}*}
		<a class="survey_btn" href="javascript:void(0)">提交</a>
		{*{/if}*}
	</div>
	<div style="height:50px;"></div>
</div>
<style type="text/css">
	#paper{
		width: 1000px;
		margin: 0 auto;
	}
	#paper_footer{
		text-align: center;
	}
	.title_content{
	    border-top: 2px solid #82898a;
	    border-bottom: 1px solid #a3a8a9;
	    padding: 13px 0;
	    font-size: 40px;
	    color: #000000;
	    line-height: 1.2;
	    text-align: center;
	}
	.score_total{
		
	}
	.question{
		padding: 15px 0;
	}
	.inner {
		padding-left: 60px;
    	padding-right: 60px;
	}
	.inputs{
		margin-left: 10px;
	}
	.score{
		margin-left: 10px;
		font-size: 12px;
	}
	.title_text{
		font-size: 14px;
    	color: #333333;
	}
	.checkbox_text{
		font-size: 14px;
    	color: #333333;
    	position: relative;
    	top: 3px;
	}
    
    .survey_btn:hover {
	    background-color: #53aaf3;
	    color: #fff;
	    text-decoration: none;
	}

	.survey_btn {
	    cursor: pointer;
	    display: inline-block;
	    zoom: 1;
	    background-color: #479de6;
	    border-radius: 3px;
	    height: 40px;
	    line-height: 40px;
	    text-align: center;
	    width: 85px;
	    padding: 0 10px;
	    font-size: 16px;
	    color: #fff;
	}
	.color_red{
		color: red;
	}
	.color_666{
		color:#666;
	}
	.o_check_score, .o_radio_score{
		position: relative;
		top: 5px;
	}

	.html_hide{
		{if $IS_LOG neq 1}
			display: none;
		{/if}
	} 
	.prompt{
		display: none;
	}
</style>
<script type="text/javascript">
	$(function () {
		{literal}
		$('.form_input').blur(function () {
			if(! /^[\d]{1,}$/.test($(this).val()) ) {
				$(this).val('');
			}
		});
		{/literal}

		$('.survey_btn').click(function () {
			// 判断是否为空
			var flag = true;
			$('.form_textarea').each(function () {
				var t = $.trim($(this).val());
				if (!t) {
					$(this).closest('.inner').find('.prompt').show();
					flag = false;
				} else {
					$(this).closest('.inner').find('.prompt').hide();
				}
			});
			$('.form_select').each(function () {
				var t = $.trim($(this).val());
				if (t == '请选择') {
					$(this).closest('.inner').find('.prompt').show();
					flag = false;
				} else {
					$(this).closest('.inner').find('.prompt').hide();
				}
			});

			$('.form_input').each(function () {
				var t = $.trim($(this).val());
				if (!t) {
					$(this).closest('.inner').find('.prompt').show();
					flag = false;
				} else {
					$(this).closest('.inner').find('.prompt').hide();
				}
			});

			var radio_arr = {};
			$('input:radio, input:checkbox').each(function () {
				var name = $(this).attr('class');
				var v = $(this).attr('checked');
				if (radio_arr[name] != 1) {
					radio_arr[name] = 0;
					if(v) {
						radio_arr[name] = 1;
					}
				}
			});
			for (var i in radio_arr) {
				if (!radio_arr[i]) {
					$('.'+i).closest('.inner').find('.prompt').show();
					flag = false;
				} else {
					$('.'+i).closest('.inner').find('.prompt').hide();
				}
			}

			if (flag) {
				$('#paper_form').submit();
			}
			
		});
	});
</script>