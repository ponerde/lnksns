import{_ as u,k as i,a,o as p,b as _,e as t,w as n}from"./index-0236d453.js";import{l as h}from"./index-296eea2f.js";/* empty css                                                              */const D={components:{laDialog:h},data(){return{mode:"add",formData:{type:"1",icon:""},formRules:{}}},computed:{dialogTitle(){return this.mode=="add"?"添加":"编辑"}},methods:{async setFormData(l){const e=await i.role.read({id:l.id});this.formData=e.data},open(l="add"){return this.mode=l,this.$refs.dialogRef.open(),this},close(){this.$emit("close")},async handleSubmit(){this.mode=="add"?await i.role.create(this.formData):await i.role.update(this.formData),this.close(),this.$emit("success")},handleClose(){console.log(99),this.close()}}};function g(l,e,C,w,o,r){const m=a("el-input"),d=a("el-form-item"),f=a("el-form"),c=a("la-dialog");return p(),_("div",null,[t(c,{ref:"dialogRef",title:r.dialogTitle,async:!0,width:"550px",onConfirm:r.handleSubmit,onCancel:r.handleClose},{default:n(()=>[t(f,{ref:"formRef",model:o.formData,"label-width":"84px",rules:o.formRules},{default:n(()=>[t(d,{label:"名称"},{default:n(()=>[t(m,{modelValue:o.formData.name,"onUpdate:modelValue":e[0]||(e[0]=s=>o.formData.name=s)},null,8,["modelValue"])]),_:1}),t(d,{label:"备注"},{default:n(()=>[t(m,{type:"textarea",modelValue:o.formData.description,"onUpdate:modelValue":e[1]||(e[1]=s=>o.formData.description=s)},null,8,["modelValue"])]),_:1})]),_:1},8,["model","rules"])]),_:1},8,["title","onConfirm","onCancel"])])}const y=u(D,[["render",g]]);export{y as default};
