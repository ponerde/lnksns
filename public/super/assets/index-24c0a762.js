import{m as V,r as L,z as G,a as l,q as x,o,b as P,e,w as a,D as I,A as s,h as i,d as b,s as h,v as d,y as B,B as J,k as D,C as E,n as M}from"./index-0236d453.js";import O from"./edit-4da0ff53.js";import"./index-296eea2f.js";/* empty css                                                              */const Q={class:"admin-lists"},W={class:"m-t-15"},X={class:"flex justify-end m-t-15"},le={__name:"index",setup(Y){const _=V(null),p=V(!1),f=L({username:""}),{pager:m,getLists:v,resetPage:k,resetParams:N}=G({fetchFun:D.admin.list,params:f}),$=async()=>{p.value=!0,await E(),await _.value.open()},z=async c=>{p.value=!0,await E(),await _.value.setFormData(c),await _.value.open("edit")},K=async c=>{await M.confirm("确定要删除？"),await D.admin.delete({id:c}),v()};return v(),(c,r)=>{const R=l("el-input"),w=l("el-form-item"),u=l("el-button"),q=l("el-form"),g=l("el-card"),A=l("Plus"),F=l("el-icon"),S=l("el-avatar"),n=l("el-table-column"),C=l("el-tag"),T=l("el-table"),U=l("la-pagination"),y=x("perms"),j=x("loading");return o(),P("div",Q,[e(g,{class:"border-none",shadow:"never"},{default:a(()=>[e(q,{style:{"margin-bottom":"-16px"},model:f,inline:""},{default:a(()=>[e(w,{label:"用户名",props:"username"},{default:a(()=>[e(R,{modelValue:f.username,"onUpdate:modelValue":r[0]||(r[0]=t=>f.username=t),clearable:"",onKeyup:I(s(k),["enter"])},null,8,["modelValue","onKeyup"])]),_:1}),e(w,null,{default:a(()=>[e(u,{type:"primary",onClick:s(k)},{default:a(()=>[i("查询")]),_:1},8,["onClick"]),e(u,{onClick:s(N)},{default:a(()=>[i("重置")]),_:1},8,["onClick"])]),_:1})]),_:1},8,["model"])]),_:1}),e(g,{class:"border-none m-t-15",shadow:"never"},{default:a(()=>[b("div",null,[h((o(),d(u,{type:"primary",onClick:$},{icon:a(()=>[e(F,null,{default:a(()=>[e(A)]),_:1})]),default:a(()=>[i(" 新增 ")]),_:1})),[[y,["auth.admin/add"]]])]),h((o(),P("div",W,[b("div",null,[e(T,{data:s(m).lists,size:"large","row-key":"id","default-expand-all":""},{default:a(()=>[e(n,{prop:"avatar",label:"头像","min-width":"100"},{default:a(({row:t})=>[e(S,{size:50,src:t.avatar,onError:c.errorHandler},null,8,["src","onError"])]),_:1}),e(n,{prop:"username",label:"用户名","min-width":"100"}),e(n,{prop:"nickname",label:"昵称","min-width":"100"}),e(n,{prop:"mobile",label:"手机号","min-width":"100"}),e(n,{prop:"email",label:"邮箱","min-width":"100"}),e(n,{prop:"role.name",label:"角色","min-width":"100"}),e(n,{prop:"status",label:"状态","min-width":"100"},{default:a(({row:t})=>[t.status=="normal"?(o(),d(C,{key:0,class:"ml-2",type:"success"},{default:a(()=>[i("正常")]),_:1})):(o(),d(C,{key:1,class:"ml-2",type:"info"},{default:a(()=>[i("禁用")]),_:1}))]),_:1}),e(n,{prop:"create_time",label:"创建时间","min-width":"180"}),e(n,{prop:"update_time",label:"更新时间","min-width":"180"}),e(n,{label:"操作",width:"200",fixed:"right"},{default:a(({row:t})=>[h((o(),d(u,{link:"",type:"primary",onClick:H=>z(t)},{default:a(()=>[i(" 编辑 ")]),_:2},1032,["onClick"])),[[y,["auth.admin/edit"]]]),t.is_super==0?h((o(),d(u,{key:0,link:"",type:"danger",onClick:H=>K(t.id)},{default:a(()=>[i(" 删除 ")]),_:2},1032,["onClick"])),[[y,["auth.admin/delete"]]]):B("",!0)]),_:1})]),_:1},8,["data"])]),b("div",X,[e(U,{modelValue:s(m),"onUpdate:modelValue":r[1]||(r[1]=t=>J(m)?m.value=t:null),onChange:s(v)},null,8,["modelValue","onChange"])])])),[[j,s(m).loading]])]),_:1}),p.value?(o(),d(O,{key:0,ref_key:"editRef",ref:_,onSuccess:s(v),onClose:r[2]||(r[2]=t=>p.value=!1)},null,8,["onSuccess"])):B("",!0)])}}};export{le as default};