import{a as i}from"./index-b87949a0.js";import{l as S}from"./index-296eea2f.js";import{m,c as T,a as u,o as c,b as _,e,w as l,v as G,F as L,G as j,y as q,h as d,n as w}from"./index-0236d453.js";/* empty css                                                              */const K={__name:"edit",emits:["success","close"],setup(z,{expose:y,emit:v}){const b=m(null),g=m(null),p=m("add"),a=m({status:1}),x=m([]),k=T(()=>p.value="编辑动态"),C=async()=>{const o=await i.role.list();x.value=o.data},R=async o=>{const t=await i.dynamic.read({id:o.id});a.value=t.data},U=(o="add")=>{p.value=o,b.value.open()},V=()=>{v("close")},h=async()=>{var t;await((t=g.value)==null?void 0:t.validate());let o;p.value=="add"?o=await i.dynamic.create(a.value):o=await i.dynamic.update(a.value),o.error?w.msgError(o.msg):(V(),v("success"),w.msgSuccess(o.msg))},B=async()=>{V()};return C(),y({open:U,setFormData:R}),(o,t)=>{const D=u("el-input"),r=u("el-form-item"),F=u("el-image"),N=u("el-input-number"),n=u("el-radio"),f=u("el-radio-group"),E=u("el-form");return c(),_("div",null,[e(S,{ref_key:"dialogRef",ref:b,title:k.value,async:!0,width:"750px",onConfirm:h,onCancel:B},{default:l(()=>[e(E,{ref_key:"formRef",ref:g,model:a.value,"label-width":"84px"},{default:l(()=>[e(r,{label:"内容",prop:"content"},{default:l(()=>[e(D,{type:"textarea",disabled:"",modelValue:a.value.content,"onUpdate:modelValue":t[0]||(t[0]=s=>a.value.content=s),rows:5},null,8,["modelValue"])]),_:1}),a.value.imgs?(c(),G(r,{key:0,label:"图片"},{default:l(()=>[(c(!0),_(L,null,j(a.value.imgs,(s,O)=>(c(),_("div",{key:O},[e(F,{style:{width:"100px",height:"100px",margin:"4px"},src:s.url,"preview-src-list":[s.url],fit:"cover"},null,8,["src","preview-src-list"])]))),128))]),_:1})):q("",!0),e(r,{label:"权重"},{default:l(()=>[e(N,{modelValue:a.value.weigh,"onUpdate:modelValue":t[1]||(t[1]=s=>a.value.weigh=s),min:"0"},null,8,["modelValue"])]),_:1}),e(r,{label:"置顶"},{default:l(()=>[e(f,{modelValue:a.value.top,"onUpdate:modelValue":t[2]||(t[2]=s=>a.value.top=s),class:"ml-4"},{default:l(()=>[e(n,{label:1},{default:l(()=>[d("是")]),_:1}),e(n,{label:0},{default:l(()=>[d("否")]),_:1})]),_:1},8,["modelValue"])]),_:1}),e(r,{label:"推送"},{default:l(()=>[e(f,{modelValue:a.value.show,"onUpdate:modelValue":t[3]||(t[3]=s=>a.value.show=s),class:"ml-4"},{default:l(()=>[e(n,{label:1},{default:l(()=>[d("是")]),_:1}),e(n,{label:0},{default:l(()=>[d("否")]),_:1})]),_:1},8,["modelValue"])]),_:1}),e(r,{label:"状态"},{default:l(()=>[e(f,{modelValue:a.value.status,"onUpdate:modelValue":t[4]||(t[4]=s=>a.value.status=s),class:"ml-4"},{default:l(()=>[e(n,{label:0},{default:l(()=>[d("待审核")]),_:1}),e(n,{label:1},{default:l(()=>[d("正常")]),_:1}),e(n,{label:2},{default:l(()=>[d("用户删除")]),_:1})]),_:1},8,["modelValue"])]),_:1})]),_:1},8,["model"])]),_:1},8,["title"])])}}};export{K as default};
