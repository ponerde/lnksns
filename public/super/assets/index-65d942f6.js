/* empty css                                                              */import{_ as B,C as _,a as u,o as i,b as x,d as f,J as m,e as k,K as C,w as a,h as s,t as d,v as g,y}from"./index-517d70fb.js";const b={props:{title:{type:String,default:""},content:{type:String,default:""},confirmButtonText:{type:[String,Boolean],default:"确定"},cancelButtonText:{type:[String,Boolean],default:"取消"},width:{type:String,default:"400px"},disabled:{type:Boolean,default:!1},async:{type:Boolean,default:!1},clickModalClose:{type:Boolean,default:!1},center:{type:Boolean,default:!1},customClass:{type:String,default:""},zIndex:{type:Number,default:8}},data(){return{visible:!1}},methods:{open(){_(()=>{this.visible=!0})},close(){this.$emit("cancel"),this.visible=!1},handleEvent(l){this.$emit(l),console.log(l),(!this.async||l==="cancel")&&this.close()}}},h={class:"dialog"},S={class:"dialog-footer"};function T(l,t,e,V,c,o){const r=u("el-button"),v=u("el-dialog");return i(),x("div",h,[f("div",{class:"dialog__trigger",onClick:t[0]||(t[0]=(...n)=>o.open&&o.open(...n))},[m(l.$slots,"trigger",{},void 0,!0)]),k(v,{modelValue:c.visible,"onUpdate:modelValue":t[3]||(t[3]=n=>c.visible=n),"custom-class":e.customClass,center:e.center,"append-to-body":"",width:e.width,"destroy-on-close":"","close-on-click-modal":e.clickModalClose,onClosed:o.close,draggable:"","z-index":e.zIndex},C({footer:a(()=>[f("div",S,[e.cancelButtonText?(i(),g(r,{key:0,onClick:t[1]||(t[1]=n=>o.handleEvent("cancel"))},{default:a(()=>[s(d(e.cancelButtonText),1)]),_:1})):y("",!0),e.confirmButtonText?(i(),g(r,{key:1,type:"primary",onClick:t[2]||(t[2]=n=>o.handleEvent("confirm"))},{default:a(()=>[s(d(e.confirmButtonText),1)]),_:1})):y("",!0)])]),default:a(()=>[m(l.$slots,"default",{},()=>[s(d(e.content),1)],!0)]),_:2},[e.title?{name:"header",fn:a(()=>[s(d(e.title),1)]),key:"0"}:void 0]),1032,["modelValue","custom-class","center","width","close-on-click-modal","onClosed","z-index"])])}const N=B(b,[["render",T],["__scopeId","data-v-f678e553"]]);export{N as l};
