webpackJsonp([3],{P1Og:function(e,a,n){var t=n("bHIX");"string"==typeof t&&(t=[[e.i,t,""]]),t.locals&&(e.exports=t.locals);n("FIqI")("2aa48c62",t,!0,{})},bHIX:function(e,a,n){a=e.exports=n("UTlt")(!0),a.push([e.i,'\n.apply-leave[data-v-cff26a4a] .vux-label {\n  font-weight: normal;\n  font-size: 0.36rem;\n}\n.apply-leave[data-v-cff26a4a] .weui-label {\n  font-weight: normal;\n  font-size: 0.36rem;\n}\n.apply-leave[data-v-cff26a4a] .weui-input {\n  color: #999;\n  font-size: 0.36rem;\n}\n.apply-leave[data-v-cff26a4a] .weui-textarea {\n  border: 1px solid #ccc;\n  font-size: 0.36rem;\n}\n.apply-leave .vux-datetime[data-v-cff26a4a] {\n  font-size: 0.36rem;\n}\n.apply-leave .weui-cell[data-v-cff26a4a] {\n  font-size: 0.36rem;\n  padding: 0.2rem 0.3rem;\n}\n.apply-leave .file-upload[data-v-cff26a4a] {\n  padding: 0.2rem 0.3rem;\n  position: relative;\n  font-size: 0.36rem;\n}\n.apply-leave .file-upload[data-v-cff26a4a]::before {\n  content: " ";\n  position: absolute;\n  left: 0;\n  top: 0;\n  right: 0;\n  height: 1px;\n  border-top: 1px solid #D9D9D9;\n  color: #D9D9D9;\n  -webkit-transform-origin: 0 0;\n  transform-origin: 0 0;\n  -webkit-transform: scaleY(0.5);\n  transform: scaleY(0.5);\n  left: 15px;\n}\n.apply-leave .file-upload .file-title[data-v-cff26a4a] {\n  width: 6em;\n  display: inline-block;\n}\n.apply-leave .file-upload > div[data-v-cff26a4a] {\n  display: inline-block;\n  position: relative;\n}\n.apply-leave .file-upload input[data-v-cff26a4a] {\n  width: 1.5rem;\n  height: 0.6rem;\n  z-index: 3;\n  position: absolute;\n  left: 0;\n  top: 0;\n  opacity: 0;\n}\n.apply-leave .file-upload .upload-btn[data-v-cff26a4a] {\n  border-radius: 3px;\n  background-color: #2293fb;\n  color: #fff;\n  width: 1.5rem;\n  height: 0.6rem;\n  display: inline-block;\n  font-size: 0.24rem;\n  text-align: center;\n  line-height: 0.6rem;\n}\n.apply-leave .upload-tip[data-v-cff26a4a] {\n  padding: 0.2rem 0.3rem;\n  font-size: 0.28rem;\n}\n.apply-leave .upload-tip .red[data-v-cff26a4a] {\n  color: red;\n  display: block;\n}\n.apply-leave .leave-list[data-v-cff26a4a] .vux-cell-value {\n  font-size: 0.26rem;\n}\n',"",{version:3,sources:["G:/oa-seal-web/src/views/leave/reportLeave.vue"],names:[],mappings:";AACA;EACE,oBAAoB;EACpB,mBAAmB;CACpB;AACD;EACE,oBAAoB;EACpB,mBAAmB;CACpB;AACD;EACE,YAAY;EACZ,mBAAmB;CACpB;AACD;EACE,uBAAuB;EACvB,mBAAmB;CACpB;AACD;EACE,mBAAmB;CACpB;AACD;EACE,mBAAmB;EACnB,uBAAuB;CACxB;AACD;EACE,uBAAuB;EACvB,mBAAmB;EACnB,mBAAmB;CACpB;AACD;EACE,aAAa;EACb,mBAAmB;EACnB,QAAQ;EACR,OAAO;EACP,SAAS;EACT,YAAY;EACZ,8BAA8B;EAC9B,eAAe;EACf,8BAA8B;EAC9B,sBAAsB;EACtB,+BAA+B;EAC/B,uBAAuB;EACvB,WAAW;CACZ;AACD;EACE,WAAW;EACX,sBAAsB;CACvB;AACD;EACE,sBAAsB;EACtB,mBAAmB;CACpB;AACD;EACE,cAAc;EACd,eAAe;EACf,WAAW;EACX,mBAAmB;EACnB,QAAQ;EACR,OAAO;EACP,WAAW;CACZ;AACD;EACE,mBAAmB;EACnB,0BAA0B;EAC1B,YAAY;EACZ,cAAc;EACd,eAAe;EACf,sBAAsB;EACtB,mBAAmB;EACnB,mBAAmB;EACnB,oBAAoB;CACrB;AACD;EACE,uBAAuB;EACvB,mBAAmB;CACpB;AACD;EACE,WAAW;EACX,eAAe;CAChB;AACD;EACE,mBAAmB;CACpB",file:"reportLeave.vue",sourcesContent:['\n.apply-leave[data-v-cff26a4a] .vux-label {\n  font-weight: normal;\n  font-size: 0.36rem;\n}\n.apply-leave[data-v-cff26a4a] .weui-label {\n  font-weight: normal;\n  font-size: 0.36rem;\n}\n.apply-leave[data-v-cff26a4a] .weui-input {\n  color: #999;\n  font-size: 0.36rem;\n}\n.apply-leave[data-v-cff26a4a] .weui-textarea {\n  border: 1px solid #ccc;\n  font-size: 0.36rem;\n}\n.apply-leave .vux-datetime[data-v-cff26a4a] {\n  font-size: 0.36rem;\n}\n.apply-leave .weui-cell[data-v-cff26a4a] {\n  font-size: 0.36rem;\n  padding: 0.2rem 0.3rem;\n}\n.apply-leave .file-upload[data-v-cff26a4a] {\n  padding: 0.2rem 0.3rem;\n  position: relative;\n  font-size: 0.36rem;\n}\n.apply-leave .file-upload[data-v-cff26a4a]::before {\n  content: " ";\n  position: absolute;\n  left: 0;\n  top: 0;\n  right: 0;\n  height: 1px;\n  border-top: 1px solid #D9D9D9;\n  color: #D9D9D9;\n  -webkit-transform-origin: 0 0;\n  transform-origin: 0 0;\n  -webkit-transform: scaleY(0.5);\n  transform: scaleY(0.5);\n  left: 15px;\n}\n.apply-leave .file-upload .file-title[data-v-cff26a4a] {\n  width: 6em;\n  display: inline-block;\n}\n.apply-leave .file-upload > div[data-v-cff26a4a] {\n  display: inline-block;\n  position: relative;\n}\n.apply-leave .file-upload input[data-v-cff26a4a] {\n  width: 1.5rem;\n  height: 0.6rem;\n  z-index: 3;\n  position: absolute;\n  left: 0;\n  top: 0;\n  opacity: 0;\n}\n.apply-leave .file-upload .upload-btn[data-v-cff26a4a] {\n  border-radius: 3px;\n  background-color: #2293fb;\n  color: #fff;\n  width: 1.5rem;\n  height: 0.6rem;\n  display: inline-block;\n  font-size: 0.24rem;\n  text-align: center;\n  line-height: 0.6rem;\n}\n.apply-leave .upload-tip[data-v-cff26a4a] {\n  padding: 0.2rem 0.3rem;\n  font-size: 0.28rem;\n}\n.apply-leave .upload-tip .red[data-v-cff26a4a] {\n  color: red;\n  display: block;\n}\n.apply-leave .leave-list[data-v-cff26a4a] .vux-cell-value {\n  font-size: 0.26rem;\n}\n'],sourceRoot:""}])},"v67+":function(e,a,n){"use strict";function t(e){n("P1Og")}Object.defineProperty(a,"__esModule",{value:!0});var l=n("4YfN"),o=n.n(l),i=n("w3K0"),p=n("Q/W4"),A=n("KiN3"),r=n("Xyyc"),s=n("dJYW"),f=n("BSzM"),c=n("gyMJ"),d=n("HVJf"),m=(i.a,p.a,A.a,r.a,s.a,f.a,o()({},Object(d.b)(["applyLeaveList"])),{name:"reportLeave",components:{PopupPicker:i.a,Group:p.a,XInput:A.a,PopupRadio:r.a,Datetime:s.a,XTextarea:f.a},data:function(){return{Officialseal:[],OfficialType:[{key:1,value:"正本原件"},{key:2,value:"副本原件"},{key:3,value:"正本复印件"},{key:4,value:"副本复印件"},{key:5,value:"正本扫描件"},{key:6,value:"副本扫描件"}],form:{type:21,companyId:"",loanItemId:"",loanItemType:"",reason:""}}},methods:{showseal:function(){this.Officialseal=[],this.getOfficialsealList()},getOfficialsealList:function(){var e=this,a=[],n={companyId:this.form.companyId,type:2};Object(c.e)(n).then(function(n){a=n.result,a.forEach(function(a){e.Officialseal.push({key:a.id,value:a.name})})})},startLeave:function(){var e=this;Object(c.m)(this.form).then(function(a){e.$vux.toast.show({text:a.message,type:"success"}),e.$router.go(-1)})}},computed:o()({},Object(d.b)(["applyLeaveList"]))}),v=function(){var e=this,a=e.$createElement,n=e._self._c||a;return n("div",{staticClass:"apply-leave"},[n("group",[n("popup-radio",{attrs:{title:"选择公司：",options:e.applyLeaveList},model:{value:e.form.companyId,callback:function(a){e.$set(e.form,"companyId",a)},expression:"form.companyId"}},[n("p",{staticClass:"select-header",attrs:{slot:"popup-header"},slot:"popup-header"},[e._v("\n        请选择\n      ")])]),e._v(" "),n("popup-radio",{attrs:{title:"证照名称：",options:e.Officialseal},on:{"on-show":e.showseal},model:{value:e.form.loanItemId,callback:function(a){e.$set(e.form,"loanItemId",a)},expression:"form.loanItemId"}},[n("p",{staticClass:"select-header",attrs:{slot:"popup-header"},slot:"popup-header"},[e._v("\n        请选择\n      ")])]),e._v(" "),n("popup-radio",{attrs:{title:"证照类型：",options:e.OfficialType},model:{value:e.form.loanItemType,callback:function(a){e.$set(e.form,"loanItemType",a)},expression:"form.loanItemType"}},[n("p",{staticClass:"select-header",attrs:{slot:"popup-header"},slot:"popup-header"},[e._v("\n        请选择\n      ")])]),e._v(" "),n("x-textarea",{attrs:{title:"用途："},model:{value:e.form.reason,callback:function(a){e.$set(e.form,"reason",a)},expression:"form.reason"}})],1),e._v(" "),n("p",{staticClass:"save-btn"},[n("button",{staticClass:"x-button primary",on:{click:e.startLeave}},[e._v("提交")])])],1)},u=[],C={render:v,staticRenderFns:u},B=C,y=n("C7Lr"),E=t,h=y(m,B,!1,E,"data-v-cff26a4a",null);a.default=h.exports}});
//# sourceMappingURL=3.787ce23942270dac281c.js.map