import{_ as h,c as v,r as x,a,o as V,b as w,d as t,t as k,e,w as s,f as y,g as S,p as C,h as I,i as B,j as E,E as N,k as U,u as j,l as z}from"./index-6cdb2e92.js";const i=d=>(C("data-v-89bdba8f"),d=d(),I(),d),D={class:"login"},M=i(()=>t("div",{class:"left"},[t("div",{class:"img"})],-1)),T={class:"right"},W={class:"login-box"},q=i(()=>t("img",{src:B,alt:"logo",class:"logo"},null,-1)),A={class:"wellcome-title"},F=i(()=>t("div",{class:"subtitle"}," 一个颜值与效率并存的后台开发框架，更多功能等你来探索！ ",-1)),G={class:"login-form"},H={__name:"index",setup(d){const p=v(()=>y.state.config),o=x({username:"",password:""}),m=async()=>{const _=await E.admin.login(o);_.error?N.error(_.msg):(U.storage.set("token",_.data.token,6e3),await j.dispatch("profile"),z.push("/dashboard"))};return(_,l)=>{const r=a("el-input"),c=a("el-form-item"),u=a("el-checkbox"),f=a("el-checkbox-group"),b=a("el-button"),g=a("el-form");return V(),w("div",D,[M,t("div",T,[t("div",W,[q,t("div",A,"Welcome to "+k(p.value.web_name),1),F,t("div",G,[e(g,{model:o,size:"large","label-position":"top"},{default:s(()=>[e(c,{label:"用户名"},{default:s(()=>[e(r,{modelValue:o.username,"onUpdate:modelValue":l[0]||(l[0]=n=>o.username=n),placeholder:"输入用户名"},null,8,["modelValue"])]),_:1}),e(c,{label:"密码"},{default:s(()=>[e(r,{type:"password",modelValue:o.password,"onUpdate:modelValue":l[1]||(l[1]=n=>o.password=n),placeholder:"输入密码"},null,8,["modelValue"])]),_:1}),e(c,null,{default:s(()=>[e(f,{modelValue:o.type,"onUpdate:modelValue":l[2]||(l[2]=n=>o.type=n)},{default:s(()=>[e(u,{label:"下次自动登录",name:"type"})]),_:1},8,["modelValue"])]),_:1}),e(c,null,{default:s(()=>[e(b,{type:"primary",class:"login-btn",onClick:m},{default:s(()=>[S("登录")]),_:1})]),_:1})]),_:1},8,["model"])])])])])}}},K=h(H,[["__scopeId","data-v-89bdba8f"]]);export{K as default};
