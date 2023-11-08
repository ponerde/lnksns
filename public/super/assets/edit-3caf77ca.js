import{m as r,r as j,c as q,a as n,o as v,b as g,e as a,w as t,F as G,G as L,v as z,g as w,j as m,n as k}from"./index-6cdb2e92.js";import{l as A}from"./index-d76bd3bc.js";/* empty css                                                              */const M={__name:"edit",emits:["success","close"],setup(H,{expose:y,emit:f}){const i=r(null),p=r(null),c=r("add"),l=r({username:"",status:"normal"}),U=j({username:[{required:!0,message:"请输入用户名",trigger:"blur"},{min:3,message:"用户名最小为3个字符串"}]}),_=r([]),x=q(()=>c.value=="edit"?"编辑用户":"新增用户"),R=async()=>{const u=await m.role.list();_.value=u.data},C=async u=>{const e=await m.admin.read({id:u.id});l.value=e.data},F=(u="add")=>{c.value=u,i.value.open()},V=()=>{p.value.resetFields(),i.value.close(),f("close")},B=async()=>{var e;await((e=p.value)==null?void 0:e.validate());let u;c.value=="add"?u=await m.admin.create(l.value):u=await m.admin.update(l.value),u.error?k.msgError(u.msg):(V(),f("success"),k.msgSuccess(u.msg))},D=async()=>{V()};return R(),y({open:F,setFormData:C}),(u,e)=>{const S=n("la-uploader"),s=n("el-form-item"),d=n("el-input"),E=n("el-option"),N=n("el-select"),b=n("el-radio"),O=n("el-radio-group"),T=n("el-form");return v(),g("div",null,[a(A,{ref_key:"dialogRef",ref:i,title:x.value,async:!0,width:"550px",onConfirm:B,onCancel:D},{default:t(()=>[a(T,{ref_key:"formRef",ref:p,model:l.value,"label-width":"84px",rules:U},{default:t(()=>[a(s,{label:"头像",prop:"avatar"},{default:t(()=>[a(S,{modelValue:l.value.avatar,"onUpdate:modelValue":e[0]||(e[0]=o=>l.value.avatar=o)},null,8,["modelValue"])]),_:1}),a(s,{label:"用户名",prop:"username"},{default:t(()=>[a(d,{modelValue:l.value.username,"onUpdate:modelValue":e[1]||(e[1]=o=>l.value.username=o)},null,8,["modelValue"])]),_:1}),a(s,{label:"密码",prop:"password"},{default:t(()=>[a(d,{type:"password",modelValue:l.value.password,"onUpdate:modelValue":e[2]||(e[2]=o=>l.value.password=o)},null,8,["modelValue"])]),_:1}),a(s,{label:"昵称",prop:"nickname"},{default:t(()=>[a(d,{modelValue:l.value.nickname,"onUpdate:modelValue":e[3]||(e[3]=o=>l.value.nickname=o)},null,8,["modelValue"])]),_:1}),a(s,{label:"手机号",prop:"mobile"},{default:t(()=>[a(d,{modelValue:l.value.mobile,"onUpdate:modelValue":e[4]||(e[4]=o=>l.value.mobile=o)},null,8,["modelValue"])]),_:1}),a(s,{label:"邮箱",prop:"email"},{default:t(()=>[a(d,{modelValue:l.value.email,"onUpdate:modelValue":e[5]||(e[5]=o=>l.value.email=o)},null,8,["modelValue"])]),_:1}),a(s,{label:"角色",prop:"role_id"},{default:t(()=>[a(N,{modelValue:l.value.role_id,"onUpdate:modelValue":e[6]||(e[6]=o=>l.value.role_id=o),class:"m-2",placeholder:"Select"},{default:t(()=>[(v(!0),g(G,null,L(_.value,o=>(v(),z(E,{key:o.id,label:o.name,value:o.id},null,8,["label","value"]))),128))]),_:1},8,["modelValue"])]),_:1}),a(s,{label:"状态"},{default:t(()=>[a(O,{modelValue:l.value.status,"onUpdate:modelValue":e[7]||(e[7]=o=>l.value.status=o),class:"ml-4"},{default:t(()=>[a(b,{label:"normal"},{default:t(()=>[w("正常")]),_:1}),a(b,{label:"disabled"},{default:t(()=>[w("禁用")]),_:1})]),_:1},8,["modelValue"])]),_:1})]),_:1},8,["model","rules"])]),_:1},8,["title"])])}}};export{M as default};
