import{m as x,r as j,z as I,a,q as V,o as n,b as D,e,w as t,D as L,A as o,h as s,d as b,s as y,v as c,B as G,y as H,C as P,n as J}from"./index-517d70fb.js";import{a as B}from"./index-5e9f0cf2.js";import M from"./edit-034d88bc.js";import"./index-65d942f6.js";/* empty css                                                              */const O={class:"admin-lists"},Q={class:"m-t-15"},W={class:"flex justify-end m-t-15"},le={__name:"index",setup(X){const m=x(null),_=x(!1),p=j({title:""}),{pager:r,getLists:f,resetPage:g,resetParams:N}=I({fetchFun:B.clause.list,params:p}),$=async()=>{_.value=!0,await P(),await m.value.open()},E=async v=>{_.value=!0,await P(),await m.value.setFormData(v),await m.value.open("edit")},K=async v=>{await J.confirm("确定要删除？"),await B.clause.delete({id:v}),f()};return f(),(v,i)=>{const R=a("el-input"),w=a("el-form-item"),u=a("el-button"),q=a("el-form"),h=a("el-card"),z=a("Plus"),A=a("el-icon"),d=a("el-table-column"),C=a("el-tag"),F=a("el-table"),S=a("la-pagination"),k=V("perms"),T=V("loading");return n(),D("div",O,[e(h,{class:"border-none",shadow:"never"},{default:t(()=>[e(q,{style:{"margin-bottom":"-16px"},model:p,inline:""},{default:t(()=>[e(w,{label:"标题",props:"title"},{default:t(()=>[e(R,{modelValue:p.title,"onUpdate:modelValue":i[0]||(i[0]=l=>p.title=l),clearable:"",onKeyup:L(o(g),["enter"])},null,8,["modelValue","onKeyup"])]),_:1}),e(w,null,{default:t(()=>[e(u,{type:"primary",onClick:o(g)},{default:t(()=>[s("查询")]),_:1},8,["onClick"]),e(u,{onClick:o(N)},{default:t(()=>[s("重置")]),_:1},8,["onClick"])]),_:1})]),_:1},8,["model"])]),_:1}),e(h,{class:"border-none m-t-15",shadow:"never"},{default:t(()=>[b("div",null,[y((n(),c(u,{type:"primary",onClick:$},{icon:t(()=>[e(A,null,{default:t(()=>[e(z)]),_:1})]),default:t(()=>[s(" 新增 ")]),_:1})),[[k,["clause/add"]]])]),y((n(),D("div",Q,[b("div",null,[e(F,{data:o(r).lists,size:"large","row-key":"id","default-expand-all":""},{default:t(()=>[e(d,{prop:"id",label:"ID","min-width":"50"}),e(d,{prop:"title",label:"标题"}),e(d,{prop:"status",label:"状态","min-width":"100"},{default:t(({row:l})=>[l.status==1?(n(),c(C,{key:0,class:"ml-2",type:"success"},{default:t(()=>[s("正常")]),_:1})):(n(),c(C,{key:1,class:"ml-2",type:"danger"},{default:t(()=>[s("关闭")]),_:1}))]),_:1}),e(d,{prop:"create_time",label:"创建时间","min-width":"180"}),e(d,{prop:"update_time",label:"更新时间","min-width":"180"}),e(d,{label:"操作",width:"120",fixed:"right"},{default:t(({row:l})=>[y((n(),c(u,{link:"",type:"primary",onClick:U=>E(l)},{default:t(()=>[s(" 编辑 ")]),_:2},1032,["onClick"])),[[k,["clause/edit"]]]),y((n(),c(u,{link:"",type:"danger",onClick:U=>K(l.id)},{default:t(()=>[s(" 删除 ")]),_:2},1032,["onClick"])),[[k,["clause/delete"]]])]),_:1})]),_:1},8,["data"])]),b("div",W,[e(S,{modelValue:o(r),"onUpdate:modelValue":i[1]||(i[1]=l=>G(r)?r.value=l:null),onChange:o(f)},null,8,["modelValue","onChange"])])])),[[T,o(r).loading]])]),_:1}),_.value?(n(),c(M,{key:0,ref_key:"editRef",ref:m,onSuccess:o(f),onClose:i[2]||(i[2]=l=>_.value=!1)},null,8,["onSuccess"])):H("",!0)])}}};export{le as default};
