{strip}
    <input type="hidden" id="waterTextContent" value="{$waterText}"/>
<div class="page flex-col">
    <div class="layer1 flex-col">
        <div class="layer2 flex-col">
            <div class="group1 flex-row">
                <div class="group-logo">
                    <a href="/"><img class="logo" referrerpolicy="no-referrer" src="libraries/v2/img/logo.png"/></a>
                    <span class="txt1">珍岛数字化平台</span>
                </div>
                <div class="group-inner">
                    <a class="link" href="http://192.168.44.130/" target="_blank">珍岛问答系统</a>
                    <a class="link" href="http://192.168.7.231/" target="_blank">假务系统</a>
                    <a class="link" href="http://192.168.7.231:8081/" target="_blank">证券系统</a>
                    <a class="link" href="http://192.168.7.201:9999/" target="_blank">报销系统</a>
                    <span class="fa fa-chevron-down"></span>
                    {assign var=title value=$CURRENT_USER_MODEL->get('first_name')}
                    {if empty($title)}
                        {assign var=title value=$CURRENT_USER_MODEL->get('last_name')}
                    {/if}
                    <img class="label1" referrerpolicy="no-referrer" src="libraries/v2/img/head.png"/>
                    <span class="word4 username">{$title}</span>
                </div>
                <div class="menu-nav">
                    <div class="nav-con">
                        <a href="http://192.168.7.231:8082/" target="_blank">设备管理系统</a>
                        <a href="http://192.168.44.157:81/" target="_blank">客服系統</a>
                        <a href="http://192.168.7.231:8301/" target="_blank">招聘系統</a>
                        <a href="http://192.168.7.231:8501/" target="_blank">人事系统</a>
                        <a href="http://192.168.7.231:8901/" target="_blank">中小管理系统</a>
                        <a href="https://predmc.71360.com/clue/index?token={$token}" target="_blank">臻寻客</a>
                        <a href="https://prein-gw.71360.com/visit-center/login?__vt_param__={$token}&callback={urlencode('https://prein-web.71360.com/visitcenterweb?original=4001')}" target="_blank">拜访中心</a>
                        <a href="/index.php?module=Vtiger&action=LinkToJump&type=1" target="_blank">SCRM专业版</a>
                        <a href="/index.php?module=Vtiger&action=LinkToJump&type=2" target="_blank">SCRM中小版</a>
                        <a href="/index.php?module=Vtiger&action=LinkToJump&type=3" target="_blank">SCRM商业云</a>
                        <a href="/index.php?module=Vtiger&action=LinkToJump&type=4" target="_blank">SCRM招聘版</a>
                    </div>
                </div>
                <div class="userinfo-con">
                    <div class="userinfo">
                        <a href="index.php?module=Users&view=PreferenceDetail&record={$CURRENT_USER_MODEL->get('id')}">个人选项</a>
                        <a href="index.php?module=Users&parent=Settings&action=Logout">注销</a>
                    </div>
                </div>
            </div>
        </div>
{/strip}