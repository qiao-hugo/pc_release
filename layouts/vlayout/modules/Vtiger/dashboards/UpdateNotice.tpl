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
<div class="container-fluid">
		<div class="span6" style="margin:5px;">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <h4 style="margin-left:20px;">&nbsp;<span style="float: left;margin-right: 20px;">更新信息</span><span style="float: right;margin-right: 20px;"><a href="/help/account/help.html" target="_blank">帮助文档</a> | <a href="/index.php?module=Knowledge&view=Detail&record=2039" target="_blank">事务对接表</a></span>
                        <span style="float:left;width:50%;margin-right: 20px;">
                        {if !empty($KNOWLEDGEDATA)}
                            <marquee  direction="left">
                                <a href="/index.php?module=Knowledge&view=Detail&record={$KNOWLEDGEDATA['knowledgeid']}" target="_blank" style="color:red;">{$KNOWLEDGEDATA['knowledgetitle']}</a>
                            </marquee>
                        {/if}
                        </span>
                    </h4>
                    <div style="clear:both;"></div>
                </div>
                <div style="height:310px;overflow:hidden">
                    <div  class="accordion-body collapse in">
                        <div class="accordion-inner" style="height:245px;overflow:auto">
                        {*<p><h3 class="text-info"><a><span class="text-error">通知：品牌客户部、KA、策略咨询部每个销售ERP系统中,客户最多只能100个；程序化广告事业部，每个销售ERP系统中,客户最多只能300个；超出部分，将自动随机掉入到公海；请上述部门同事对自己系统内的客户及时进行筛选、处理。</span></a></h3></p>*}
                        <!--<p><h3 class="text-info"><a>公司的营业执照正副本原件（珍岛、珍岛网络、龙教、洞察力、凯丽隆）和上海珍岛诚信信用报告原件保管人现为公共关系部的程芃<br>
联系方式：18001766237<br>
邮箱：gov@71360.com</a></h3></p> -->
                        {*<p class="text-info"><h3><a href="/index.php?module=Knowledge&view=Detail&record=1793" target="_blank"><span class="text-error">自2017年5月22日起CRM系统将启用新的开票系统，新开票系统只针对2017年5月22日（含）后的回款，若需要开具2017年5月22日前回款发票，具体操作流程详见知识库发票操作手册。</span></a></h3></p>*}
						<p class="text-info"><h3><a href="/index.php?module=Knowledge&view=Detail&record=2060" target="_blank">代付款模板已更新，即日（2021年7月26日）起按照最新模板执行；旧模板不再接受。</a></h3></p>
						<p class="text-info"><h3><a href="/index.php?module=Knowledge&view=Detail&record=1541" target="_blank">中小销售过程相关的<font style="color:red;">高压红线</font>（管理层&员工）</a></h3></p>
						<p class="text-info"><h3><a href="/index.php?module=Knowledge&view=Detail&record=2104" target="_blank">	中小商务客服工作对接制度</a></h3></p>
						<p class="text-info"><h3><span class="text-error">拜访单需要先提单再外出（不允许补单），且上级审核完成方视为公出，否则记为未打卡；{*上级不能作为下属拜访单的陪同人，需要其本人自己提拜访单，并由其上级审核完成后，方视为公出，否则记为未打卡*}</span></h3><h3><a style="color: purple" href="index.php?module=Knowledge&view=Detail&record=1984" target="_blank">【ERP】标准合同模板最新版本公告</a></h3></p>
							<h3><a href="/index.php?module=Knowledge&view=Detail&record=2069" target="_blank"><p class="text-error">原《珍岛集团退款管理制度附件》已更新为《珍岛集团退款管理制度附件2021》，请大家注意下载最新的附件进行使用</p></a></h3>
							<h3><a href="/help/nost.html" target="_blank"><p class="text-error">今年不可以参加双推的客户名单</p></a></h3>
							<p class="text-info"><h3><a href="help/pdf/web/viewer.html?file=../doc/Tyunidea.pdf" target="_blank">T云3.6理念沟通版</a></h3></p>
							<p class="text-info"><h3><a href="help/service.htm" target="_blank">客服问题对接表</a></h3></p>
                           <p class="text-info"><h3><a href="help/pdf/web/viewer.html?file=../doc/T-yun.pdf" target="_blank">T云操作注意事项及技巧</a>　　　　 <a href="help/pdf/web/viewer.html?file=../doc/160422.pdf" target="_blank"><span class="text-error">中小商务工作对接流程</span></a></h3></p>
                          <h3><a href="/index.php?module=Knowledge&view=Detail&record=2076" target="_blank"><span class="text-error">T云推广限制行业</span></a>　　　　<a href="http://edu.71360.com" target="_blank"><span class="text-error">珍岛大学线上学习平台</span></a></h3>
                          <h3><a href="/help/tyun/upgradecalculator.html" target="_blank"><p class="text-error">T云版本升级价格计算器</p></a></h3>
	<p class="text-info"><h3><a href="/help/shangwudaokuanliucheng/daokuanliuc.html" target="_blank">商务到款确认流程</a>　
    　　
    {*<a href="http://edu.71360.com/wecenter/?/account/ajax/login_process_api/?user_name={$USER_NAME}&password={$EMAIL}&check_str={$CHECK_STR}" target="_blank"><span class="text-error">T云问答平台</span></a>*}</h3></p>
    		
<p class="text-info"><h3><a href="/help/invoice/video/index.html" target="_blank">发票操作视频教程</a></h3></p>
<p class="text-info"><h3><a href="/help/leads/leads.html" target="_blank">商机管理制度及常见操作教程</a></h3></p>
  <p class="text-info"><h3>拜访单需要先提单再外出,不允许补单!</h3></p>    
<p class="text-info"><h3><a href="/help/tixing/tixing.html" target="_blank">系统提醒配置</a></h3></p>  
	
						</div>
                        <div style="clear:both;"></div>
                        <div class="modal-footer">
                            <p class="text-info">ERP问题反馈邮箱：<span class="label label-a_exception">ERP110@71360.com</span>&nbsp;&nbsp;&nbsp;&nbsp; </p>
      					</div>
                    </div>
                </div>
            </div>
        </div>
        {if $IF_SERVICE}
             <div class="span6" style="margin:5px;">
        <div class="accordion-group">
            <div class="accordion-heading">
                <h4 style="margin-left:20px;">我的客服任务</h4>
            </div>
            <div style="height:310px;overflow:hidden;">
                <div  class="accordion-body collapse in">
                    <div class="accordion-inner"  style="height:245px;overflow:hidden;">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th  style="color:#999;"  nowrap>开始时间</th>
                                <th  style="color:#999;"  nowrap>结束时间</th>
                                <th  style="color:#999;"  nowrap>客户名称</th>
                                <th  style="color:#999"  nowrap>创建时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            {if count($REUTNLIST) neq '0'}
                                {foreach item=returndata from=$REUTNLIST}
                                        <tr>
                                            <td nowrap> <a href="index.php?module=ServiceComments&view=Detail&record={$returndata['commentsid_reference']}&mode=showRecentComments&tab_label=ModComments&page=1" target="_blank">{$returndata['uppertime']}</a></td>
                                            <td nowrap> <a href="index.php?module=ServiceComments&view=Detail&record={$returndata['commentsid_reference']}&mode=showRecentComments&tab_label=ModComments&page=1" target="_blank">{$returndata['lowertime']}</a></td>
                                            <td nowrap> <a href="index.php?module=ServiceComments&view=Detail&record={$returndata['commentsid_reference']}&mode=showRecentComments&tab_label=ModComments&page=1" target="_blank">{$returndata['accountid']}</a></td>
                                            <td nowrap> <a href="index.php?module=ServiceComments&view=Detail&record={$returndata['commentsid_reference']}&mode=showRecentComments&tab_label=ModComments&page=1" target="_blank">{$returndata['commentsid']}</a></td>
                                        </tr>
                                {/foreach}
                            {else}
                                <tr>
                                    <td colspan="5">暂无内容</td>
                                </tr>
                            {/if}
                            </tbody>
                        </table>
                    </div>
                    <div style="clear:both;"></div>
                    <div class="modal-footer">
                        &nbsp;
                    </div>
                </div>
            </div>
        </div>
    </div>
        {/if}
        <div class="span6" style="margin:5px;">
            <div class="accordion-group">
                <div class="accordion-heading">
                <h4 style="margin-left:20px;">&nbsp;<span style="float: left;margin-right: 20px;">新闻公告</span><!--<span style="float: right;margin-right: 20px;"><a href="/help/goods/goods.html" target="_blank">行政物料申请</a></span>-->
                    </h4>
                    <div style="clear:both;"></div>
                </div>
                <div style="height:310px;overflow:hidden">
                    <div  class="accordion-body collapse in">
                        <div class="accordion-inner"  style="height:245px;overflow:hidden;">
                        	<table class="table table-striped">
				                <thead>
				                <tr>
				                    <th  style="color:#999;"  nowrap>标题</th>
				                    <th  style="color:#999;"  nowrap>发布部门</th>
				                    <th  style="color:#999;"  nowrap>执行时间</th>
				                    <th  style="color:#999;" nowrap>发布时间</th>
				                    <th  style="color:#999"  nowrap>浏览人数</th>
				                </tr>
				                </thead>
				                <tbody>
				                {if count($KNOWLEDGERECORD) neq '0'}
				                    {foreach item=data from=$KNOWLEDGERECORD}
				                    <tr>
				                        <td nowrap><a href="/index.php?module=Knowledge&view=Detail&record={$data['knowledgeid']}" target="_blank">{mb_substr($data['knowledgetitle'],0,20)}</a>{if $data['knowledgetop'] eq 1}<span style="color:red;">【置顶】</span>{/if}</td>
				                        <td nowrap>{$data['authordepartment']}</td>
				                        <td nowrap>{if $data['cmdtime'] neq ''}{date('Y-m-d',strtotime($data['cmdtime']))}{/if}</td>
				                        <td nowrap>{$data['knowledgedate']}</td>
				                        <td nowrap>{$data['knowledgecount']}</td>
				                        
				                    </tr>
				                    {/foreach}
				                {else}
				                    <tr>
				                        <td colspan="5">暂无内容</td>
				                    </tr>
				                {/if}
				                </tbody>
				            </table>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="modal-footer">
                        <span class="text-info"><a href="/index.php?module=Knowledge&view=List&filter=NewList" target="_blank">查看更多</a></span>
      					</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="span6" style="margin:5px;">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <h4 style="margin-left:20px;">关注公众号</h4>
                </div>
                <div style="clear:both;"></div>
                <div style="max-height:310px;overflow:hidden">
                    <div  class="accordion-body collapse in">
                       <div class="accordion-inner"  style="height:260px;overflow:hidden;padding: 0">
                         <div id="myCarousel" class="carousel slide">
                              <ol class="carousel-indicators">
                                <li data-target="#myCarousel" style="cursor:pointer;background-color:#ccc" data-slide-to="0" title="珍岛集团" class="active"></li>
                                <li data-target="#myCarousel" style="cursor:pointer;background-color:#a9b6d0;" title="珍岛集团HR" data-slide-to="1"></li>
                                <li data-target="#myCarousel" style="cursor:pointer;background-color:#09853c;" title="珍岛铁军" data-slide-to="2"></li>
                                <li data-target="#myCarousel" style="cursor:pointer;background-color:#b6f063;" title="珍岛准免费获客" data-slide-to="3"></li>
                                <li data-target="#myCarousel" style="cursor:pointer;background-color:#0a79d4;" title="珍岛商业云" data-slide-to="4"></li>
                                <li data-target="#myCarousel" style="cursor:pointer;background-color:#0bb20c;" title="珍客SCRM" data-slide-to="5"></li>
                                <li data-target="#myCarousel" style="cursor:pointer;background-color:#08c;;" title="珍岛T云服务平台" data-slide-to="6"></li>
                                <li data-target="#myCarousel" style="cursor:pointer;background-color:purple;" title="珍岛T云外贸版" data-slide-to="7"></li>
                                <li data-target="#myCarousel" style="cursor:pointer;background-color:#b94a48;" title="企业微信" data-slide-to="8"></li>
                                <li data-target="#myCarousel" style="cursor:pointer;background-color:#D02090;" title="珍岛产教融合服务云" data-slide-to="9"></li>
                                <li data-target="#myCarousel" style="cursor:pointer;background-color:#FFD700;" title="凯丽隆数字营销集团" data-slide-to="10"></li>
                              </ol>
                              <!-- Carousel items -->
                              <div class="carousel-inner">
                                    <div class="active item">
                                        <img src="zhendaojituan.png" title="珍岛集团" alt="珍岛集团" style=" margin-left: auto;margin-right:auto;display:block;width: auto;height: 258px;width: 258px">
                                        <div class="carousel-caption">
                                            <h4>珍岛集团</h4>
                                            <p>珍岛信息技术(上海)股份有限公司，秉承“整合数字资源，技术驱动营销”的核心理念，致力于打造全球智能营销云平台（www.71360.com），聚焦Marketingforce（营销力赋能），面向全球企业提供360度全方位营销力软件及工具服务。</p>
                                        </div>
                                    </div>
                                     <div class="item">
                                        <img src="zhendaojituanhr.png" title="珍岛集团HR" alt="珍岛集团HR" style="margin-left: auto;margin-right:auto;display:block;width: auto;height: 258px;width: 258px">
                                        <div class="carousel-caption">
                                            <h4>珍岛集团HR</h4>
                                            <p>珍岛集团——全球领先的SaaS智能营销云平台，秉承"整合数字资源,技术驱动营销"的核心运营理念，专注于数字营销技术、产品、资源、服务的创新与整合，为企业、院校、园区等客群提供360度全方位多场景赋能。</p>
                                        </div>
                                     </div>
                                     <div class="item">
                                        <img src="zhendaotiejun.png"  title="珍岛铁军" alt="珍岛铁军" style="margin-left: auto;margin-right:auto;display:block;width: auto;height: 258px;width: 258px">
                                        <div class="carousel-caption">
                                            <h4>珍岛铁军</h4>
                                            <p>为全国珍岛铁军赋能!</p>
                                        </div>
                                     </div>
                                     <div class="item">
                                        <img src="zhendaohuoke.png" title="珍岛准免费获客" alt="珍岛准免费获客" style="margin-left: auto;margin-right:auto;display:block;width: auto;height: 258px;width: 258px">
                                        <div class="carousel-caption">
                                            <h4>珍岛准免费获客</h4>
                                            <p>准免费获客——智能营销工具让获客成本趋近于零。</p>
                                        </div>
                                     </div>
                                     <div class="item">
                                        <img src="zhendaoshangyeyun.png" title="珍岛商业云" alt="珍岛商业云" style="margin-left: auto;margin-right:auto;display:block;width: auto;height: 258px;width: 258px">
                                        <div class="carousel-caption">
                                            <h4>珍岛商业云</h4>
                                            <p>打造极致新零售服务!</p>
                                        </div>
                                     </div>
                                     <div class="item">
                                        <img src="zhendaoscrm.png" title="珍客SCRM" alt="珍客SCRM" style="margin-left: auto;margin-right:auto;display:block;width: auto;height: 258px;width: 258px">
                                        <div class="carousel-caption">
                                            <h4>珍客SCRM</h4>
                                            <p>【珍客SCRM 隶属于珍岛集团】珍客SCRM客户资产运营平台，为企业提供全网跨平台营销数据驱动下的私域流量全链路管理及增值赋能。</p>
                                        </div>
                                     </div>
                                     <div class="item">
                                        <img src="zhendaotpingtai.png" title="珍岛T云服务平台" alt="珍岛T云服务平台" style="margin-left: auto;margin-right:auto;display:block;width: auto;height: 258px;width: 258px">
                                        <div class="carousel-caption">
                                            <h4>珍岛T云服务平台</h4>
                                            <p>服务于T云用户,提供信息交流、移动体验等服务。</p>
                                        </div>
                                     </div>
                                     <div class="item">
                                        <img src="zhendaotwaimao.png"  title="珍岛T云外贸版" alt="珍岛T云外贸版" style="margin-left: auto;margin-right:auto;display:block;width: auto;height: 258px;width: 258px">
                                        <div class="carousel-caption">
                                            <h4>珍岛T云外贸版</h4>
                                            <p>珍岛信息技术(上海)股份有限公司，秉承“整合数字资源，技术驱动营销”的核心理念，致力于打造全球智能营销云平台（www.71360.com），聚焦Marketingforce（营销力赋能），面向全球企业提供360度全方位营销力软件及工具服务。</p>
                                        </div>
                                     </div>
                                    <div class="item">
                                        <img src="qywexi.png" title="企业微信" alt="企业微信" style="margin-left: auto;margin-right:auto;display:block;width: auto;height: 258px;width: 258px">
                                        <div class="carousel-caption">
                                            <h4>企业微信</h4>
                                        </div>
                                     </div>
                                     <div class="item">
                                        <img src="zhendaochanjiao.png"  title="珍岛产教融合服务云" alt="珍岛产教融合服务云" style="margin-left: auto;margin-right:auto;display:block;width: auto;height: 258px;width: 258px">
                                        <div class="carousel-caption">
                                            <h4>珍岛产教融合服务云</h4>
                                            <p>关注高校市场营销专业“教与学”，专注营销课程实践与操作，重构老师教学、学生学习与实践场景，为学校构建一个“互动、共享、开放”智能营销云实训平台。</p>
                                        </div>
                                     </div>
                                    <div class="item">
                                        <img src="kaililongshuziyingxiao.png" title="凯丽隆数字营销集团" alt="凯丽隆数字营销集团" style="margin-left: auto;margin-right:auto;display:block;width: auto;height: 258px;width: 258px">
                                        <div class="carousel-caption">
                                            <h4>凯丽隆数字营销集团</h4>
                                            <p>凯丽隆由上海珍岛集团投资控股，是一家定位互联网精准营销服务为主业的新媒体营销公司。公司主营业务为移动新媒体广告，为客户提供从定制营销目标、制定营销计划、媒介采购、媒介执行、数据监控、效果优化等一站式网络营销专业服务。</p>
                                        </div>
                                     </div>
                              </div>
                                   <!-- Carousel nav -->
                                  <a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
                                  <a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
                          </div>
                       </div>
                        <div style="clear:both;"></div>
                        <div class="modal-footer" style="height: 46px"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="span6" style="margin:5px;">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <h4 style="margin-left:20px;">客户负责人变更记录</h4>
                </div>
                <div style="max-height:310px;overflow:hidden">
                    <div  class="accordion-body collapse in">
                        <div class="accordion-inner"   style="height:245px;overflow:hidden;">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th  style="color:#999;" nowrap>客户名称</th>
                                    <th  style="color:#999" nowrap>操作时间</th>
                                    <th  style="color:#999" nowrap>转出</th>
                                    <th  style="color:#999" nowrap>转至</th>
                                    <th  style="color:#999" nowrap>操作人</th>
                                </tr>
                                </thead>
                                <tbody>
                                {if count($ACCCHANGE) neq '0'}
                                    {foreach item=data from=$ACCCHANGE}
                                        <tr>
                                            <td nowrap>{$data['accountname']}</td>
                                            <td nowrap>{$data['createdtime']}</td>
                                            <td nowrap>{$data['olsuser']}</td>
                                            <td nowrap>{$data['newuser']}</td>
                                            <td nowrap>{$data['modiuser']}</td>
                                        </tr>
                                    {/foreach}
                                {else}
                                    <tr>
                                        <td colspan="5">暂无内容</td>
                                    </tr>
                                {/if}
                                </tbody>
                            </table>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="modal-footer">
                            <span class="text-info"><a href="/index.php?module=Accounts&view=List&filter=changeHistory" target="_blank">查看更多</a></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="span6" style="margin:5px;">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <h4 style="margin-left:20px;">7天需要跟进的客户</h4>
                </div>
                <div style="max-height:310px;overflow:hidden;">
                    <div  class="accordion-body collapse in">
                        <div class="accordion-inner"  style="height:245px;overflow:auto;">
	                        <ul class="list-unstyled">
		                        {if !empty($RECORDNOSEVEN)}
									{foreach item=RECORDVALUE from=$RECORDNOSEVEN}
		                            	<li style="width:33%;float:left;list-style:none">
			                            	<p class="text-info">
			                            		<i class="icon-hand-right"></i>
			                            		<a href="/index.php?module=Accounts&view=Detail&record={$RECORDVALUE['accountid']}">{if mb_strlen(strip_tags($RECORDVALUE['accountname']),'utf8')>8}{mb_substr(strip_tags($RECORDVALUE['accountname']),0,8,'utf-8')}...{else}{strip_tags($RECORDVALUE['accountname'])}{/if}</a>剩<span class="label label-warning" style="margin: 0px 3px 0 3px;">{$RECORDVALUE['protectday']}</span>天
		                            		</p>
		                        		</li>
		                        	{/foreach}
		                        {else}
		                        	<li style="width:33%;float:left;list-style:none">暂无跟进客户</li>
	                        	{/if}
                        	</ul>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="modal-footer">
                        <span class="text-info"><a href="/index.php?module=Accounts&view=List&filter=noseven" target="_blank">查看更多</a></span>
      					</div>
                    </div>
                </div>
            </div>
        </div>

        {*一周内新增商机列表*}
        <div class="span6" style="margin:5px;">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <h4 style="margin-left:20px;">一周内新增商机列表</h4>
                </div>
                <div style="max-height:310px;overflow:auto">
                    <div  class="accordion-body collapse in">
                        <div class="accordion-inner" style="height:245px;overflow:auto;">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th  style="color:#999;" nowrap>线索客户名称</th>
                                    <th  style="color:#999" nowrap>联系人</th>
                                    <th  style="color:#999" nowrap>手机</th>
                                    <th  style="color:#999" nowrap>线索来源</th>
                                </tr>
                                </thead>
                                <tbody>
                                {if count($LEADSDATA) neq '0'}
                                    {foreach item=data from=$LEADSDATA}
                                        <tr>
                                            <td nowrap>{$data['company']}</td>
                                            <td nowrap>{$data['lastname']}</td>
                                            <td nowrap>{$data['phone']}</td>
                                            <td nowrap>{$data['leadsource']}</td>
                                            
                                        </tr>
                                    {/foreach}
                                {else}
                                    <tr>
                                        <td colspan="5">暂无内容</td>
                                    </tr>
                                {/if}
                                </tbody>
                            </table>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="modal-footer">
                            <span class="text-info"><a href="/index.php?module=Leads&view=List" target="_blank">查看更多</a></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {*超过7天未跟进商机列表*}
        <div class="span6" style="margin:5px;">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <h4 style="margin-left:20px;">超过7天未跟进商机列表</h4>
                </div>
                <div style="max-height:310px;overflow:auto">
                    <div  class="accordion-body collapse in">
                        <div class="accordion-inner" style="height:245px;overflow:auto">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th  style="color:#999;" nowrap>线索客户名称</th>
                                    <th  style="color:#999" nowrap>联系人</th>
                                    <th  style="color:#999" nowrap>手机</th>
                                    <th  style="color:#999" nowrap>线索来源1</th>
                                </tr>
                                </thead>
                                <tbody>
                                {if count($MORE_LEADSDATA) neq '0'}
                                    {foreach item=data from=$MORE_LEADSDATA}
                                        <tr>
                                            <td nowrap>{$data['company']}</td>
                                            <td nowrap>{$data['lastname']}</td>
                                            <td nowrap>{$data['phone']}</td>
                                            <td nowrap>{$data['leadsource']}</td>
                                            
                                        </tr>
                                    {/foreach}
                                {else}
                                    <tr>
                                        <td colspan="5">暂无内容</td>
                                    </tr>
                                {/if}
                                </tbody>
                            </table>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="modal-footer">
                            <span class="text-info"><a href="index.php?module=Leads&view=List&filter=threeMonth" target="_blank">查看更多</a></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="span6" style="margin:5px;">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <h4 style="margin-left:20px;">未匹配合同的回款</h4>
                </div>
                <div style="max-height:310px;overflow:hidden">
                    <div  class="accordion-body collapse in">
                        <div class="accordion-inner"   style="height:245px;overflow:auto;">
                            <table class="table table-striped" id="receive_table1">
                                <thead>
                                <tr>
                                    <th  style="color:#999;"  nowrap>公司账号</th>
                                    <th  style="color:#999;"  nowrap>汇款抬头</th>
                                    <th  style="color:#999;"  nowrap>回款金额</th>
                                    <th  style="color:#999;"  nowrap>回款类型</th>
                                    <th  style="color:#999"   nowrap>入账日期</th>
                                </tr>
                                </thead>
                                <tbody>
                                {if count($NO_CONTRACTLI) neq '0'}
                                    {foreach item=item from=$NO_CONTRACTLI}
                                        <tr>
                                            <td>{$item['owncompany']}</td>
                                            <td>{$item['paytitle']}</td>
                                            <td>{$item['receivementcurrencytype']}:{$item['unit_price']}</td>
                                            <td>{$item['newrenewa']}</td>
                                            <td>{$item['reality_date']}</td>
                                        </tr>
                                    {/foreach}
                                {else}
                                    <tr>
                                        <td colspan="5">暂无未匹配合同的回款</td>
                                    </tr>
                                {/if}
                                </tbody>
                            </table>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="modal-footer">
                            <p class="text-info">共计金额：{$SUM_LI}元</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        

</div>

{if !empty($SERVICECONTRACT_RELATION) }
<div id="confirm2" style="margin:0;position:fixed;top:0;left:0;width:100%;height:100%;z-index: 11000;background-color: #fff;">
    {if $IS_FROZEN eq 0}
    <div id="confirmclose2"  class="btn" style="z-index: 11001;position:absolute;top:15px;right:20px;text-align:center;font-size:14px;cursor:pointer;" title="关闭">已知晓</div>
    {/if}
    <div style="overflow-y:scroll;margin: auto 0;">
        <table class="table table-striped" style="position:fixed;background-color: #ffffff;width: 99%">
             <tr>
             <th  colspan="5" style="color:#ff0000;text-align: center;font-size: 30px;height: 50px;line-height: 50px;"  nowrap>
             您相关的T云合同被客服系统驳回，请尽快处理，否则影响分配客服！！</th>
             </tr>
                          <tr>
             <td  colspan="5" style="color:#EC5C5C;text-align: center;font-size: 16px;height: 50px;line-height: 50px;"  nowrap>
             说明：以下被客服驳回的合同可能是您或者您下级的，请尽快协调处理，如有疑问，请联系对应客服。如果已处理完毕请填写已处理结果说明，填写后该合同将不会出现提示。</td>
             </tr>
        </table>

         <table class="table table-striped" style="margin-top: 130px;">
            <thead>
            <tr>
                <th  style="color:#999;"  nowrap>合同编号</th>
                <th  style="color:#999;"  nowrap>客户</th>
                <th  style="color:#999;"  nowrap>提单人</th>
                <th  style="color:#999;"  nowrap>签订人</th>
                <th  style="color:#999;"  nowrap>驳回人</th>
                <th  style="color:#999"   nowrap>驳回时间</th>
                <th  style="color:#999"   nowrap>驳回原因</th>
                <th  style="color:#999"   nowrap>已处理结果回执</th>
            </tr>
            </thead>
            <tbody>

                {foreach item=item from=$SERVICECONTRACT_RELATION}
                    <tr>
                        <td>
                            <a href="index.php?module=ServiceContracts&view=Detail&record={$item['servicecontractsid']}" target="_blank">{$item['contract_no']}</a>
                        </td>
                        <td>{$item['accountname']}</td>
                        <td>{$item['receivename']}</td>
                        <td>{$item['signname']}</td>
                        <td>{$item['rejectname']}</td>
                        <td>{$item['rejecttime']}</td>
                        <td>{$item['reason']}</td>
                        <td>
                            <a class="reply_handle" data-id="{$item['servicecontractsid']}" data-relationid="{$item['id']}" data-kefurelationid="{$item['relationid']}"><i title="处理回执" class="icon-pencil alignMiddle"></i></a>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    </div>
{/if}
{if !empty($CONFIRMLIST)}
<div id="confirm" style="margin:0;position:fixed;top:0;left:0;width:100%;height:100%;z-index: 10000;background-color: #fff;">
    {if $IS_FROZEN eq 0}
    <div id="confirmclose" style="z-index: 10001;position:absolute;top:15px;right:20px;width:25px;height:25px;border:1px solid #ff0000;border-radius: 20px;text-align:center;line-height: 25px;font-size:18px;color:red;cursor:pointer;" title="关闭">X</div>
    {/if}
    <div style="height: 100%;overflow-y:scroll;">
        <table class="table table-striped" id="flalted" style="position:fixed;background-color: #ffffff;">
            <thead>
            <tr>
                <th  colspan="5" style="color:#ff0000;text-align: center;font-size: 30px;height: 50px;line-height: 50px;"  nowrap>近期要审查合同列表<br />以下合同请立即至合同管理员办理相应手续；否则，系统账号将自动冻结。</th>
            </tr>
            <tr id="flalte1">
                <th  style="color:#999;"  nowrap>类别</th>
                <th  style="color:#999;"  nowrap>合同编号</th>
                <th  style="color:#999;"  nowrap>领取时间</th>
                <th  style="color:#999;"  nowrap>最后一次审查</th>
                <th  style="color:#999;"  nowrap><span class="label label-a_exception">关闭天数</span></th>
                <th  style="color:#999"  nowrap>合同领取人</th>
                <th  style="color:#999"  nowrap>操作</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <table class="table table-striped" >
            <thead>
            <tr>
                <th  colspan="5" style="color:#ff0000;text-align: center;font-size: 30px;height: 50px;line-height: 50px;"  nowrap>近期要审查合同列表<br />以下合同请立即至合同管理员办理相应手续；否则，系统账号将自动冻结。</th>
            </tr>
            <tr id="one1">
                <th  style="color:#999;"  nowrap>类别</th>
                <th  style="color:#999;"  nowrap>合同编号</th>
                <th  style="color:#999;"  nowrap>领取时间</th>
                <th  style="color:#999;"  nowrap>最后一次审查</th>
                <th  style="color:#999;"  nowrap>关闭天数</th>
                <th  style="color:#999"   nowrap>合同领取人</th>
                <th  style="color:#999"   nowrap>操作</th>
            </tr>
            </thead>
            <tbody>

                {foreach item=item from=$CONFIRMLIST}
                    <tr>
                        <td>{$item['type']}</td>
                        <td>{$item['contract_no']}</td>
                        <td>{$item['receivedate']}</td>
                        <td>{$item['confirmlasttime']}</td>
                        <td>{$item['diffdate']}</td>
                        <td>{$item['userid']}</td>
                        <td>

                        {if $item['add'] eq 1}
                            {if $item['type'] eq '服务合同'}
                        <span class="label label-success addExtensionTrial" data-id="{$item['servicecontractsid']}" data-userid="
                        {$item['smownerid']}" style="font-size: 14px;cursor:pointer;font-weight: bold;" title="延期申请">+</span>
                            {/if}
                            {if $item['type'] eq '采购合同'}
                        <span class="label label-success addSuppliercontractTime" data-id="{$item['suppliercontractsid']}" data-userid="
                        {$item['smownerid']}" style="font-size: 14px;cursor:pointer;font-weight: bold;" title="延期申请">+</span>
                            {/if}
                        {/if}

                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>
<script>
{literal}
    $(function(){
        $('#one1 th').each(function(index,value){
            $('#flalte1 th').eq(index).width($(value).width());
        });
    });
{/literal}
</script>
{/if}
<!-- Button to trigger modal -->
<!-- Modal -->
<div id="myModal" class="modal hide span6 " style=" float: none; margin:100px auto;" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header" style="background-color: #fff;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">未匹配合同的回款</h3>
    </div>
    <div class="modal-body" style="background-color: #fff;">
        <div class="span12" style="margin:5px;">
            <div class="accordion-group">
                <div style="height:310px;overflow:hidden">
                    <div  class="accordion-body collapse in">
                        <div class="accordion-inner"  style="height:245px;overflow:auto;">
                            <table class="table table-striped" id="receive_table2">
                                <thead>
                                <tr>
                                    <th  style="color:#999;"  nowrap>公司账号</th>
                                    <th  style="color:#999;"  nowrap>汇款抬头</th>
                                    <th  style="color:#999;"  nowrap>回款金额</th>
                                    <th  style="color:#999;"  nowrap>回款类型</th>
                                    <th  style="color:#999"   nowrap>入账日期</th>
                                </tr>
                                </thead>
                                <tbody>
                                {if count($NO_CONTRACTLI) neq '0'}
                                    {foreach item=item from=$NO_CONTRACTLI}
                                        <tr>
                                            <td>{$item['owncompany']}</td>
                                            <td>{$item['paytitle']}</td>
                                            <td>{$item['receivementcurrencytype']}:{$item['unit_price']}</td>
                                            <td>{$item['newrenewa']}</td>
                                            <td>{$item['reality_date']}</td>
                                        </tr>
                                    {/foreach}
                                {else}
                                    <tr>
                                        <td colspan="5">暂无未匹配合同的回款</td>
                                    </tr>
                                {/if}
                                </tbody>
                            </table>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="modal-footer" style="padding:5px;">
                            <p class="text-info">共计金额：{$SUM_LI}元</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
    </div>
</div>


<div id="showmodal" style="margin:0;position:fixed;top:0;left:0;width:100%;height:100%;z-index: 31000;display: none">
	<div class="modal-dialog" style="overflow: hidden">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">请输入处理结果</h4>
			</div>
			<div class="modal-content">
				<div>
				    <input type="hidden" id="servicecontractsid" value="0">
				    <input type="hidden" id="servicecontractsrelationid" value="0">
				    <input type="hidden" id="kefurelationid" value="0">
                    <div class="span12">
                        <span class="span3" style="text-align: right;padding: 5px;">处理结果<span style="color: red">*</span> </span>
                        <select class="span8" id="reply_status">
                        <option value="0">请选择一个选项</option>
                        <option value="1">处理完成</option>
                        <option value="2">不予处理</option>
                        </select>
                    </div>
                    <div class="span12">
                        <span class="span3"  style="text-align: right;padding: 5px;">具体处理描述<span style="color: red">*</span>  </span>
                        <textarea class="span8" id="reply_description" placeholder="1.你具体处理合同的方式：作废原合同/新签补充协议？
2.新合同或者补充协议编号是？
3.大概处理时间是？
"></textarea>
                    </div>
				</div>
            </div>
			<div class="modal-footer" style="margin-top: 120px;">
				<div class=" pull-right cancelLinkContainer"><a class="cancelLink" type="reset" data-dismiss="modal">取消</a></div>
				<button class="btn btn-success" type="submit" id="reply_success">确定</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#myCarousel').carousel();
          $("#myCarousel").hover(function(){
              $(".carousel-caption").hide();
          },function(){
                $(".carousel-caption").show();
          });
          $('.carousel-indicators li').click(function(){
              var num=$(this).data('slide-to');
              console.log(11232332);
              $('#myCarousel').carousel(num);
          })
        var url = window.location.search;
        if(url=='?from=login'){
            $('#myModal').modal();
            $('.modal-backdrop').css({
                "opacity":"0.6",
                "z-index":"0"
            });
            $('#confirmclose').click(function(){
                $('#confirm').hide();
            });
        }

        $('#confirmclose2').click(function(){
            $('#confirm2').hide();
        });
        $('#receive_table2').DataTable({
            language: {
                "sProcessing":"处理中...",
                "sLengthMenu":"显示 _MENU_ 项结果",
                "sZeroRecords":  "没有匹配结果",
                "sInfo":"显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
                "sInfoEmpty": "显示第 0 至 0 项结果，共 0 项","sInfoFiltered": "(由 _MAX_ 项结果过滤)",
                "sInfoPostFix":  "","sSearch":"当前页快速检索:",
                "sUrl":"","sEmptyTable":"表中数据为空","sLoadingRecords": "载入中...",
                "sInfoThousands": ",",
                "oPaginate": { "sFirst":"首页","sPrevious": "上页","sNext":"下页","sLast":"末页"},
                "oAria": { "sSortAscending":  ": 以升序排列此列","sSortDescending": ": 以降序排列此列"}
            },
            "aLengthMenu": [[20,50,100,-1],[20,50,100,'所有']],
            "bLengthChange": true,
            // "dom": '<"toolbar">frtip',
            // iDisplayLength: 1,
            "bInfo": true,//页脚信息,
            "bAutoWidth": true,//自动宽度e
            scrollCollapse: true
        });
       $('#receive_table1').DataTable({
            language: {
                "sProcessing":"处理中...",
                "sLengthMenu":"显示 _MENU_ 项结果",
                "sZeroRecords":  "没有匹配结果",
                "sInfo":"显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
                "sInfoEmpty": "显示第 0 至 0 项结果，共 0 项","sInfoFiltered": "(由 _MAX_ 项结果过滤)",
                "sInfoPostFix":  "","sSearch":"当前页快速检索:",
                "sUrl":"","sEmptyTable":"表中数据为空","sLoadingRecords": "载入中...",
                "sInfoThousands": ",",
                "oPaginate": { "sFirst":"首页","sPrevious": "上页","sNext":"下页","sLast":"末页"},
                "oAria": { "sSortAscending":  ": 以升序排列此列","sSortDescending": ": 以降序排列此列"}
            },
            "aLengthMenu": [[20,50,100,-1],[20,50,100,'所有']],
            "bLengthChange": true,
            // "dom": '<"toolbar">frtip',
            // iDisplayLength: 1,
            "bInfo": true,//页脚信息,
            "bAutoWidth": true,//自动宽度e
            scrollCollapse: true
        });
        $('select[name="receive_table1_length"]').css("width","60px");
        $('select[name="receive_table2_length"]').css("width","60px");
        $('.addExtensionTrial').click(function(){
            var msg={
                'message':'合同延期申请只能申请一次,确定要延期申请吗?'
            };
            var thisInstance=this;
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var sparams = {
                    'module': 'ExtensionTrial',
                    'action': 'BasicAjax',
                    'srecord': $(thisInstance).data('id'),
                    'mode': 'addExtensionTrial',
                    'suserid': $(thisInstance).data('userid')
                };
                $(thisInstance).remove();
                AppConnector.request(sparams).then(
                    function (datas){
                        if (datas.success == true) {

                        }
                    });
            });
        });

        $('.addSuppliercontractTime').click(function() {
            var msg={
                'message':'确定要采购合同延期申请吗?'
            };
            var thisInstance=this;
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var sparams = {
                    'module': 'Suppcontractsextension',
                    'action': 'BasicAjax',
                    'srecord': $(thisInstance).data('id'),
                    'mode': 'addContractsExtension',
                    'suserid': $(thisInstance).data('userid')
                };
                $(thisInstance).remove();
                AppConnector.request(sparams).then(
                    function (datas){
                        if (datas.success == true) {
                            alert('采购合同延期申请成功');
                        }
                    });
            });
        });

         $('.reply_handle').click(function(){
             $("#servicecontractsid").val(0);
             $("#servicecontractsrelationid").val(0);
            var thisInstance=this;
            var servicecontractsid = $(thisInstance).data('id');
            $("#servicecontractsid").val(servicecontractsid);

            var servicecontractsrelationid = $(thisInstance).data('relationid');
            $("#servicecontractsrelationid").val(servicecontractsrelationid);

            var kefurelationid = $(thisInstance).data('kefurelationid');
            $("#kefurelationid").val(kefurelationid);

            $("#showmodal").show();
        });

       $(".cancelLinkContainer").click(function() {
          $("#servicecontractsid").val(0);
          $("#servicecontractsrelationid").val(0);
          $("#kefurelationid").val(0);
          $('#showmodal').hide();
       });

       $("#reply_success").click(function() {
         var reply_status = $("#reply_status").val();
         var reply_description = $("#reply_description").val();
         var servicecontractsrelationid = $("#servicecontractsrelationid").val();
         var servicecontractsid = $("#servicecontractsid").val();
         var relationid = $("#kefurelationid").val();
         if(!reply_status || !reply_description || !servicecontractsrelationid){
             alert('请按要求填完必填项');
             return;
         }
                 var sparams = {
                    'module': 'Home',
                    'action': 'BasicAjax',
                    'mode': 'replyRejectServiceContract',
                    'id':servicecontractsrelationid,
                    'servicecontractsid':servicecontractsid,
                    'status':reply_status,
                    'relationid':relationid,
                    'description':reply_description,
                };
                AppConnector.request(sparams).then(
                    function (data){
                        if (data.result.success) {
                            alert('回复客户成功');
                              $("#servicecontractsid").val(0);
                              $("#servicecontractsrelationid").val(0);
                              $("#kefurelationid").val(0);
                              $('#showmodal').hide();
                              window.location.reload();
                            return;
                        }
                        alert(data.result.message);
                    });
       })
    });
</script>


{*
<div class="container-fluid">
	<div class="row-fluid">
	*}{*
		<div class="span12">
			<div class="row-fluid">
				<div class="span12">
					<div class="accordion" id="accordion-314864">
						<div class="accordion-group">
							<div class="accordion-heading">
								<h4 style="margin-left:20px;">更新内容<span style="float: right;margin-right: 20px;"><a href="/help/help.html" target="_blank">帮助文档</a>|<a href="/shiwuduijie.html" target="_blank">事务对接表</a></span></h4>
							</div>
							<div style="max-height:210px;overflow:auto">
								<div id="accordion-element-416138" class="accordion-body collapse in">
									<div class="accordion-inner">
                                        <p class="text-info"><i class="icon-hand-right"></i><h3>公司投诉电话：18918331910（丁岚）  投诉邮箱：110@trueland.org </h3></p>
                                        <p class="text-info"><i class="icon-hand-right"></i>CRM问题反馈邮箱：young.yang@trueland.net </p>

                                        <p class="text-info"><i class="icon-hand-right"></i>当前客户负责人跟进客户之后，客户列表根据跟进时间进行排序 [2015-03-05]</p> <p class="text-info"><i class="icon-hand-right"></i>审单步骤：1）首先在部门-》待审核信息 查看需要自己审核的信息.2）点击进去，数据是需要填写成本，以及简单的备注信息的3）点击审核即可通过4）打回为打回到底的，需注意5）审核过的工单可在工单列表中看到 [2015-03-05]</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		*}{*
		<div class="span6">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <h4 style="margin-left:20px;">更新信息<span style="float: right;margin-right: 20px;"><a href="/help/account/help.html" target="_blank">帮助文档</a> | <a href="/help/shiwuduijie/shiwuduijie.html" target="_blank">事务对接表</a></span></h4>
                </div>
                <div style="max-height:315px;overflow:auto">
                    <div  class="accordion-body collapse in">
                        <div class="accordion-inner">
							
				
<p class="text-info"><h3><a href="/help/invoice/video/index.html" target="_blank">发票操作视频教程</a></h3></p>
<p class="text-info"><h3><a href="/help/leads/leads.html" target="_blank">商机管理制度及常见操作教程</a></h3></p>
  <p class="text-info"><h3>拜访单需要先提单再外出,不允许补单!</h3></p>    
<p class="text-info"><h3><a href="/help/tixing/tixing.html" target="_blank">系统提醒配置</a></h3></p>  
						</div>
                        <div style="clear:both;"></div>
                        <div class="modal-footer" style="padding:5px;">
                        <p class="text-info">CRM问题反馈邮箱：young.yang@trueland.net </p>
      					</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="span6">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <h4 style="margin-left:20px;">新闻公告</h4>
                </div>
                <div style="max-height:315px;overflow:auto">
                    <div  class="accordion-body collapse in">
                        <div class="accordion-inner">
                        	<table class="table table-striped">
				                <thead>
				                <tr>
				                    <th  style="color:#999;"  nowrap>标题</th>
				                    <th  style="color:#999;"  nowrap>发布人</th>
				                    <th  style="color:#999;"  nowrap>执行时间</th>
				                    <th  style="color:#999;" nowrap>发布时间</th>
				                    <th  style="color:#999"  nowrap>浏览人数</th>
				                </tr>
				                </thead>
				                <tbody>
				                {if count($KNOWLEDGERECORD) neq '0'}
				                    {foreach item=data from=$KNOWLEDGERECORD}
				                    <tr>
				                        <td nowrap><a href="/index.php?module=Knowledge&view=Detail&record={$data['knowledgeid']}" target="_blank">{mb_substr($data['knowledgetitle'],0,20)}</a>{if $data['knowledgetop'] eq 1}<span style="color:red;">【置顶】</span>{/if}</td>
				                        <td nowrap>{$data['last_name']}</td>
				                        <td nowrap>{date('Y-m-d',strtotime($data['cmdtime']))}</td>
				                        <td nowrap>{$data['knowledgedate']}</td>
				                        <td nowrap>{$data['knowledgecount']}</td>
				                        
				                    </tr>
				                    {/foreach}
				                {else}
				                    <tr>
				                        <td colspan="5">暂无内容</td>
				                    </tr>
				                {/if}
				                </tbody>
				            </table>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="modal-footer" style="padding:5px;">
                        <span style="float: right;margin-right: 20px;"><a href="/index.php?module=Knowledge&view=List&filter=NewList" target="_blank">查看更多</a></span>
      					</div>
                    </div>
                </div>
            </div>
        </div>
	</div>
	<div class="row-fluid">
        <div class="span6">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <h4 style="margin-left:20px;">客户负责人变更记录</h4>
                </div>
                <div style="max-height:315px;overflow:auto">
                    <div  class="accordion-body collapse in">
                        <div class="accordion-inner">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th  style="color:#999;" nowrap>客户名称</th>
                                    <th  style="color:#999" nowrap>操作时间</th>
                                    <th  style="color:#999" nowrap>转出</th>
                                    <th  style="color:#999" nowrap>转至</th>
                                    <th  style="color:#999" nowrap>操作人</th>
                                </tr>
                                </thead>
                                <tbody>
                                {if count($ACCCHANGE) neq '0'}
                                    {foreach item=data from=$ACCCHANGE}
                                        <tr>
                                            <td nowrap>{$data['accountname']}</td>
                                            <td nowrap>{$data['createdtime']}</td>
                                            <td nowrap>{$data['olsuser']}</td>
                                            <td nowrap>{$data['newuser']}</td>
                                            <td nowrap>{$data['modiuser']}</td>
                                        </tr>
                                    {/foreach}
                                {else}
                                    <tr>
                                        <td colspan="5">暂无内容</td>
                                    </tr>
                                {/if}
                                </tbody>
                            </table>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="modal-footer" style="padding:5px;">
                            <span style="float: right;margin-right: 20px;"><a href="/index.php?module=Accounts&view=List&filter=changeHistory" target="_blank">查看更多</a></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="span6">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <h4 style="margin-left:20px;">7天需要跟进的客户</h4>
                </div>
                <div style="max-height:315px;overflow:auto">
                    <div  class="accordion-body collapse in">
                        <div class="accordion-inner">
	                        <ul class="list-unstyled">
		                        {if !empty($RECORDNOSEVEN)}
									{foreach item=RECORDVALUE from=$RECORDNOSEVEN}
		                            	<li style="width:33%;float:left;list-style:none">
			                            	<p class="text-info">
			                            		<i class="icon-hand-right"></i>
			                            		<a href="/index.php?module=Accounts&view=Detail&record={$RECORDVALUE['accountid']}">{if mb_strlen(strip_tags($RECORDVALUE['accountname']),'utf8')>8}{mb_substr(strip_tags($RECORDVALUE['accountname']),0,8,'utf-8')}...{else}{strip_tags($RECORDVALUE['accountname'])}{/if}</a>剩<span class="label label-warning" style="margin: 0px 3px 0 3px;">{$RECORDVALUE['protectday']}</span>天
		                            		</p>
		                        		</li>
		                        	{/foreach}
		                        {else}
		                        	<li style="width:33%;float:left;list-style:none">暂无跟进客户</li>
	                        	{/if}
                        	</ul>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="modal-footer" style="padding:5px;">
                        <span style="float: right;margin-right: 20px;"><a href="/index.php?module=Accounts&view=List&filter=noseven" target="_blank">查看更多</a></span>
      					</div>
                    </div>
                </div>
            </div>
        </div>
        
        {if $SORTARR['display'] eq 1 }
        <div class="span6">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <h4 style="text-align:center;">本月商务有效业绩排行榜</h4>
                </div>
                <div style="max-height:315px;overflow:auto">
                    <div  class="accordion-body collapse in">
                        <div class="accordion-inner" style="text-align:center;margin:0 auto;">
                            <ul class="list-unstyled" style="list-style:none;text-align:center;margin:0 auto;">
                                {if !empty($SORTARR['verygood'])}
                                    {foreach item=RECORDVAL from=$SORTARR['verygood']}
                                        <li style="list-style:none;text-align:center;margin:0 auto;">

                                            <div class="progress">
                                                <div class="progress-bar progress-bar-info" role="progressbar"
                                                     {if $RECORDVAL['key'] eq 1}aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"
                                                     style="width: 90%;background-color:#5BC0DE;
                                                     {elseif $RECORDVAL['key'] eq 2}
                                                             aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"
                                                style="width: 80%;background-color:#5BC0DE;{elseif $RECORDVAL['key'] eq 3}
                                                aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"
                                                style="width: 70%;background-color:#5BC0DE;{elseif $RECORDVAL['key'] eq 4}
                                                aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"
                                                style="width: 60%;background-color:#5BC0DE;{elseif $RECORDVAL['key'] eq 5}
                                                aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"
                                                style="width: 50%;background-color:#5BC0DE;{/if}">
                                                    <span class="sr-only"><span class="label label-a_normal">{$RECORDVAL['key']}</span>　<span class="label label-a_normal" style="font-size: 14px;">{$RECORDVAL['user_name']}</span></span>
                                                </div>
                                            </div>
                                        </li>
                                    {/foreach}
                                    {if !empty($SORTARR['mymsg']['user_name'])}
                                        <li style="list-style:none;text-align:center;margin:20px auto 0;">
                                            <p class="text-info" style="text-align:left;">
                                                <span style="font-size: 16px;" class="label label-a_normal">{$SORTARR['mymsg']['user_name']}</span>　当前的排名是　<span class="label label-success">{$SORTARR['mymsg']['key']}</span>
                                                {*<span class="label label-warning" style="margin: 0px 10px 0 3px;background-color:{if $SORTARR['mymsg']['key'] eq 1}red{elseif $SORTARR['mymsg']['key'] eq 2}#FEF102{elseif $SORTARR['mymsg']['key'] eq 3}purple{elseif $SORTARR['mymsg']['key'] eq 4}blue{elseif $SORTARR['mymsg']['key'] eq 5}green{else}#fffff{/if}">{$SORTARR['mymsg']['key']}</span>{$SORTARR['mymsg']['user_name']}*}
                                                {*<a href="/index.php?module=Accounts&view=Detail&record={$RECORDVAL['accountid']}">{if mb_strlen(strip_tags($RECORDVALUE['accountname']),'utf8')>8}{mb_substr(strip_tags($RECORDVALUE['accountname']),0,8,'utf-8')}...{else}{strip_tags($RECORDVALUE['accountname'])}{/if}</a>剩<span class="label label-warning" style="margin: 0px 3px 0 3px;">{$RECORDVALUE['protectday']}</span>天*}
                                            {*</p>
                                        </li>
                                    {else}
                                        <li style="text-align:center;list-style:none">暂无排名</li>
                                    {/if}
                                {else}
                                    <li style="text-align:center;list-style:none">暂无排名</li>
                                {/if}


                            </ul>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="modal-footer" style="padding:5px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {/if}   
        
    </div>
</div>
*}