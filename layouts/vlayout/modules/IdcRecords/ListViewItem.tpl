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
    <style>
        .triangle-up {
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-bottom: 10px solid #000000;
            padding-top: 5px;
            float: right;
        }
        .triangle-right {
            width: 0;
            height: 0;
            border-top: 5px solid transparent;
            border-left: 10px solid #000000;
            border-bottom: 5px solid transparent;
            margin-top: 8px;
            margin-right: 10px;
            float: right;
            display: none;
        }
        .ListViewItem ul{
            list-style-type: none;
            background-color: #f5f5f5;
            border: 1px solid #dddddd;
            margin: 0;
            padding: 0;
            cursor:pointer;
        }
        /*#moreActionMenu{
            position: absolute;
            bottom: 20px;
            left: 20px;
        }*/
        .bgul{
            width: 90px;
            line-height: 30px;
        }
        .bgli{
            text-align: center;
            height: 30px;
            line-height: 30px;
        }
    </style>
   <div class="ListViewItem">
       <input type="button" value="全选" id="checkall" class="btn" style="font-size: 12px;">
       <input type="button" value="反选" id="check_revsern" class="btn" style="font-size: 12px;">
       {if $IS_VIEWS_LISTBTNADD}
            <button id="ListviewEdit" class="btn" style="font-size: 12px; margin-left: 20px; width: 90px;"> 编辑<span class="triangle-up"></span></button>
       {/if}
       <ul style="display: none;" class="bgul" id="stateF">

           <li class="bgli ListliF">
               <div>状态<span class="triangle-right"></span></div>
               <ul style="display: none;" class="bgul">
                   {foreach item=IDCSTATEARR_VALUE from=$IDCSTATEARR}
                       <li class="bgli ListliC">{$IDCSTATEARR_VALUE.idcstate}</li>
                   {/foreach}
               </ul>
           </li>

       </ul>

   </div>

{/strip}