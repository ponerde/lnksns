import{m as k,r as I,z as J,j as B,a as n,q as K,o as s,b as R,e,w as t,A as N,B as o,g as u,d as V,s as f,v as c,C as M,y as S,D as x,n as O}from"./index-6cdb2e92.js";import Q from"./edit-66254fa5.js";import W from"./permissions-5d7884d2.js";import"./index-d76bd3bc.js";/* empty css                                                              */const X={class:"role-lists"},Y={class:"m-t-15"},Z={class:"flex justify-end m-t-15"},se={__name:"index",setup(ee){const w=k(null),b=k(),i=I({}),v=k(!1),C=k(!1),{pager:m,getLists:p,resetPage:g,resetParams:E}=J({fetchFun:B.role.list,params:i}),F=async()=>{v.value=!0,await x(),await w.value.open()},U=async r=>{v.value=!0,await x(),await w.value.open("edit").setFormData(r)},j=async r=>{C.value=!0,await x(),await b.value.setFormData(r),await b.value.open("edit")},q=async r=>{await O.confirm("确定要删除？"),await B.role.delete({id:r}),p()};return p(),(r,a)=>{const P=n("el-input"),h=n("el-form-item"),d=n("el-button"),z=n("el-form"),D=n("el-card"),A=n("Plus"),T=n("el-icon"),_=n("el-table-column"),L=n("el-table"),G=n("la-pagination"),y=K("perms"),H=K("loading");return s(),R("div",X,[e(D,{class:"border-none",shadow:"never"},{default:t(()=>[e(z,{style:{"margin-bottom":"-16px"},model:i,inline:""},{default:t(()=>[e(h,{label:"管理员账号"},{default:t(()=>[e(P,{modelValue:i.account,"onUpdate:modelValue":a[0]||(a[0]=l=>i.account=l),clearable:"",onKeyup:N(o(g),["enter"])},null,8,["modelValue","onKeyup"])]),_:1}),e(h,{label:"管理员名称"},{default:t(()=>[e(P,{modelValue:i.name,"onUpdate:modelValue":a[1]||(a[1]=l=>i.name=l),clearable:"",onKeyup:N(o(g),["enter"])},null,8,["modelValue","onKeyup"])]),_:1}),e(h,null,{default:t(()=>[e(d,{type:"primary",onClick:o(g)},{default:t(()=>[u("查询")]),_:1},8,["onClick"]),e(d,{onClick:o(E)},{default:t(()=>[u("重置")]),_:1},8,["onClick"])]),_:1})]),_:1},8,["model"])]),_:1}),e(D,{class:"border-none m-t-15",shadow:"never"},{default:t(()=>[V("div",null,[f((s(),c(d,{type:"primary",onClick:F},{icon:t(()=>[e(T,null,{default:t(()=>[e(A)]),_:1})]),default:t(()=>[u(" 新增 ")]),_:1})),[[y,["auth.role/add"]]])]),f((s(),R("div",Y,[V("div",null,[e(L,{data:o(m).lists,size:"large","row-key":"id","default-expand-all":""},{default:t(()=>[e(_,{prop:"name",label:"角色名称","min-width":"150"}),e(_,{prop:"description",label:"备注","min-width":"200"}),e(_,{prop:"create_time",label:"创建时间","min-width":"100"}),e(_,{prop:"update_time",label:"更新时间","min-width":"100"}),e(_,{label:"操作",width:"200",fixed:"right"},{default:t(({row:l})=>[f((s(),c(d,{link:"",type:"primary",onClick:$=>U(l)},{default:t(()=>[u(" 编辑 ")]),_:2},1032,["onClick"])),[[y,["auth.role/edit"]]]),f((s(),c(d,{link:"",type:"primary",onClick:$=>j(l)},{default:t(()=>[u(" 分配权限 ")]),_:2},1032,["onClick"])),[[y,["auth.role/edit"]]]),f((s(),c(d,{link:"",type:"danger",onClick:$=>q(l.id)},{default:t(()=>[u(" 删除 ")]),_:2},1032,["onClick"])),[[y,["auth.role/delete"]]])]),_:1})]),_:1},8,["data"])]),V("div",Z,[e(G,{modelValue:o(m),"onUpdate:modelValue":a[2]||(a[2]=l=>M(m)?m.value=l:null),onChange:o(p)},null,8,["modelValue","onChange"])])])),[[H,o(m).loading]])]),_:1}),v.value?(s(),c(Q,{key:0,ref_key:"editRef",ref:w,onSuccess:o(p),onClose:a[3]||(a[3]=l=>v.value=!1)},null,8,["onSuccess"])):S("",!0),C.value?(s(),c(W,{key:1,ref_key:"permissionsRef",ref:b,onSuccess:o(p),onClose:a[4]||(a[4]=l=>C.value=!1)},null,8,["onSuccess"])):S("",!0)])}}};export{se as default};