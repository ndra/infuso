
inx=function(p,type){if(!p)return inx.cmp.get(0);switch(typeof(p)){case"number":case"string":return inx.cmp.get(p);break;case"object":if(p.xyaz3m9ijf1hnw5zt890)return p;if(p.bj852tc92op9zqyli3f5)return inx.cmp.get(p.id());if(!p.type)p.type=type;if(p.type)return inx.cmp.create(p);break;}
inx.msg("inx(..) unsupported argument type",1);inx.msg(typeof(p),1);return inx.cmp.get(0);}
inx.conf={url:"/inx/pub/",cmdUrl:"/mod/pub/json.php",z_index_dialog:100000000,z_index_message:120000000,z_index_field:110000000,ajaxIndicator:true,componentLoadingHTML:"Загрузка..."};inx.delegate=function(fn,scope,p){if(p)
return function(){return fn.apply(scope,[p])}
else
return function(){return fn.apply(scope,arguments)}}
inx.cmd=function(id,cmd,p){if(p!==undefined)return function(){inx(id).cmd(cmd,p)}
else return function(p){inx(id).cmd(cmd,p)}}
inx.ns=function(ns){ns=ns.split(".");var obj=inx;for(var i=1;i<ns.length;i++){if(!obj[ns[i]])
obj[ns[i]]={};obj=obj[ns[i]];}
return obj;}
inx.css=function(){var a=[];for(var i=0;i<arguments.length;i++)
a.push(arguments[i]);a=a.join("\n");a=a.replace(/%%(.*)%%/g,function(a){a=a.replace(/%/g,"").split(".");a=inx.conf.url+a.join("/")+"/";return a;});$("<style>"+a+"</style>").appendTo("head");}
inx.geq=function(a,b){a=parseInt(a);if(!a)a=0;if(a<b)a=b;return a;}
inx.strpad=function(str,count){str+="";var ret="";for(var i=0;i<count;i++)
ret+=(i>=count-str.length)?str.substr(str.length-count+i,1):"0";return ret;}
inx.__nextId=0;inx.id=function(){inx.__nextId++;return"inx-"+inx.__nextId;}
var Base=function(){};Base.extend=function(_instance,_static){var extend=Base.prototype.extend;Base._prototyping=true;var proto=new this;extend.call(proto,_instance);delete Base._prototyping;var constructor=proto.constructor;var klass=proto.constructor=function(){if(!Base._prototyping){if(!this.private_id){if(!arguments[0])arguments[0]={};this.private_id=arguments[0].id||inx.id();this.bj852tc92op9zqyli3f5=true;inx.cmp.register(this.private_id,this);}
if(this._constructing||this.constructor==klass){this._constructing=true;constructor.apply(this,arguments);delete this._constructing;}else if(arguments[0]!=null){return(arguments[0].extend||extend).call(arguments[0],proto);}}};klass.ancestor=this;klass.extend=this.extend;klass.forEach=this.forEach;klass.implement=this.implement;klass.prototype=proto;klass.toString=this.toString;klass.valueOf=function(type){return(type=="object")?klass:constructor.valueOf();};extend.call(klass,_static);if(typeof klass.init=="function")klass.init();return klass;};Base.prototype={extend:function(source,value){if(arguments.length>1){var ancestor=this[source];if(ancestor&&(typeof value=="function")&&(!ancestor.valueOf||ancestor.valueOf()!=value.valueOf())&&/\bbase\b/.test(value)){var method=value.valueOf();value=function(){var previous=this.base||Base.prototype.base;this.base=ancestor;var returnValue=method.apply(this,arguments);this.base=previous;return returnValue;};value.valueOf=function(type){return(type=="object")?value:method;};value.toString=Base.toString;}
this[source]=value;}else if(source){var extend=Base.prototype.extend;if(!Base._prototyping&&typeof this!="function"){extend=this.extend||extend;}
var proto={toSource:null};var hidden=["constructor","toString","valueOf"];var i=Base._prototyping?0:1;while(key=hidden[i++]){if(source[key]!=proto[key]){extend.call(this,key,source[key]);}}
for(var key in source){if(!proto[key])extend.call(this,key,source[key]);}}
return this;},base:function(){}};Base=Base.extend({constructor:function(){this.extend(arguments[0]);}},{ancestor:Object,version:"1.1",forEach:function(object,block,context){for(var key in object){if(this.prototype[key]===undefined){block.call(context,object[key],key,object);}}},implement:function(){for(var i=0;i<arguments.length;i++){if(typeof arguments[i]=="function"){arguments[i](this.prototype);}else{this.prototype.extend(arguments[i]);}}
return this;},toString:function(){return String(this.valueOf());}});inx.cmp=function(id){this.__id=id;this.xyaz3m9ijf1hnw5zt890=true;this.cmd=function(name){var ret;var cmp=inx.cmp.buffer[id];if(cmp)cmp=cmp.obj;if(cmp)ret=cmp.cmd.apply(cmp,arguments);return ret===undefined?this:ret;}
this.task=function(name){var cmp=inx.cmp.buffer[id];if(cmp)cmp=cmp.obj;if(cmp)cmp.task(name);return this;}
this.info=function(name){name="info_"+name;var cmp=inx.cmp.buffer[id];if(cmp)cmp=cmp.obj;var a=[];for(var i=1;i<arguments.length;i++)a[i-1]=arguments[i];if(cmp&&cmp[name]&&typeof(cmp[name]=="function"))
return cmp[name].apply(cmp,a);}
this.exists=function(){return!!inx.cmp.buffer[this.id()];}
this.id=function(){return this.__id;}
this.setOwner=function(id){id=inx(id).id();this.data("owner",id);return this;}
this.owner=function(){return inx.cmp.get(this.data("owner"));}
this.data=function(key,val){if(arguments.length==1){var b=inx.cmp.buffer[this.id()];if(b)b=b.data;if(b)return b[key]}
if(arguments.length==2){var b=inx.cmp.buffer[this.id()];if(b){if(!b.data)b.data={};b.data[key]=val;}
return this;}}
this.on=function(event,a,b){var events=this.data("events");if(!events)events={};this.data("events",events);if(!events[event])events[event]=[];if(typeof(a)=="function"){events[event].push({fn:a});return this;}
if(typeof(a)=="string"&&!!a.match(/\(/)){events[event].push({fn:new Function(a)});return this;}
if(typeof(a)=="string"&&!a.match(/\(/)&&!b){events[event].push({id:this.id(),name:a});return this;}
if(typeof(a)=="string"&&typeof(b)=="string"){events[event].push({id:a,name:b});return this;}
if(typeof(a)=="object"){events[event].push({id:a[0],name:a[1]});return this;}
inx.msg("inx.cmp.on - bad params",1);}
this.fire=function(event,p1,p2,p3){var obj=inx.cmp.buffer[this.id()];if(obj)obj=obj.obj;if(!obj)return;var events=this.data("events");if(!events)return;events=events[event];if(!events)return;for(var i in events){var event=events[i];if(event.fn)
return event.fn.apply(obj,[p1,p2,p3]);else
return inx.cmp.get(event.id).cmd(event.name,p1,p2,p3);}},this.suspendEvents=function(){var obj=inx.cmp.buffer[this.id()];if(obj)obj=obj.obj;if(!obj)return;obj.suspendEvents();}
this.unsuspendEvents=function(){var obj=inx.cmp.buffer[this.id()];if(obj)obj=obj.obj;if(!obj)return;obj.unsuspendEvents();}
this.bubble=function(event,p1,p2,p3){var cmp=this;cmp.fire(event,p1,p2,p3);while(1){cmp=cmp.owner();if(!cmp.exists())break;cmp.fire(event,p1,p2,p3);}}
this.call=function(p,success,failure,meta){var cmd=inx.cmp.create({type:"inx.command",data:p,meta:meta,source:this.id()});if(success)cmd.on("success",success)
cmd.cmd("exec");return cmd.id();}
this.find=function(name){var cmp=inx.cmp.buffer[id];if(cmp)cmp=cmp.obj;if(cmp&&cmp.find&&typeof(cmp.find=="function"))
return cmp.find(name);}
this.here=function(){document.write("<div id='"+id+"'></div>");this.cmd("render","#"+id)}}
inx.cmp.get=function(obj){if(!obj)return new inx.cmp(0);if(typeof(obj)!="object")return new inx.cmp(obj);if(typeof(obj)=="object"&&obj.xyaz3m9ijf1hnw5zt890)return obj;inx.msg("<b>inx.cmp.get</b> argument must be an object ID or object constructed by inx.get(..)",1);return inx(0);}
inx.cmp.fromElement=function(e){var id=$(e).parents(".inx-box").andSelf().data("id")||null;return inx(id);}
inx.cmp.buffer={};inx.cmp.register=function(id,obj,rewrite){var b=inx.cmp.buffer[id]||{};b.obj=obj;inx.cmp.buffer[id]=b;}
inx.cmp.unregister=function(id){setTimeout(function(){delete inx.cmp.buffer[id];})}
inx.cmp.create=function(p){if(p.xyaz3m9ijf1hnw5zt890)return p;var constructor;try{constructor=eval(p.type);}
catch(ex){}
if(constructor)
var cmp=new constructor(p);else
var cmp=new inx.box.loader(p);return inx(cmp.id());}
inx.css(".inx-core-inlineBlock{display: -moz-inline-box;display: inline-table;display: inline-block;}");var m=navigator.userAgent.match(/MSIE (\d+\.\d+)/);if(m){var ver=parseFloat(m[1]);if(ver<8)
inx.css(".inx-core-inlineBlock{display: inline;}");if(ver>=8)
inx.css("float:left;");}
inx.css(".inx-unselectable{-khtml-user-select:none; -moz-user-select:none; }");inx.css(".inx-shadowframe{border-radius: 5px;-moz-border-radius: 5px;-webkit-border-radius: 5px;padding:3px;box-shadow: 0 0 10px black;-webkit-box-shadow: 0 0 10px black; -moz-box-shadow: 0 0 10px black;}");inx.deselect=function(){if(window.getSelection){window.getSelection().removeAllRanges();}
else if(document.selection&&document.selection.empty)
document.selection.empty();}
$(document).mousedown(function(e){inx.__unselect=!!$(e.target).parents(".inx-unselectable").length;if(inx.__unselect){inx.deselect();e.preventDefault();window.focus();}});$(document).mouseup(function(e){inx.__unselect=false;var u=!!$(e.target).parents(".inx-unselectable").length;if(u){inx.deselect();e.preventDefault();}});$(document).mousemove(function(e){if(inx.__unselect){inx.deselect();e.preventDefault();}});inx.core={};inx.core.scrollTo=function(e,mode){var offset=0;$(e).parents().each(function(){var c=$(this);if(c.css("overflow")=="auto"&&c.attr("nodeName")!="BODY"&&c.attr("nodeName")!="HTML"){var y1=c.offset().top-offset;var y2=e.offset().top;if(y2<y1||mode=="top"){offset-=y2-y1;c.stop(true).animate({scrollTop:c.scrollTop()+y2-y1},"fast");}
if(mode!="top"){y1+=c.attr("offsetHeight");var y2=y2+e.outerHeight();if(y2>y1){offset+=y2-y1;c.stop(true).animate({scrollTop:c.scrollTop()+y2-y1},"fast");}}}})
var y1=$(window).scrollTop();var y2=e.offset().top+offset;if(y2<y1)$("body,html").stop(true).animate({scrollTop:y2},"fast");y1+=$(window).height();var y2=y2+e.outerHeight();if(y2>y1)$("body,html").stop(true).animate({scrollTop:y2-$(window).height()},"fast");}
inx.focusManager=new function(){var k=[8,9,13,33,34,35,36,37,38,39,40,45,46,27,20,18,16,17,19,91,93,112,113,114,115,116,117,118,119,120,121,122,123];this.systemKeys={};for(var i=0;i<k.length;i++)
this.systemKeys[k[i]]=true;this.focused=null;this.blur=function(cmp){if(this.focused==cmp)this.focus();}
this.focus=function(id){if(this.focused==id)return;var last=this.focused;this.focused=id;inx(last).cmd("handleFocusChange",0);inx(id).cmd("handleFocusChange",1);this.checkSmoothFocus(last,id);}
this.checkSmoothFocus=function(c1,c2){var c1=inx(c1);var c2=inx(c2);var o1=[];while(c1.exists()){o1.unshift(c1.id());c1=c1.owner();}
var o2=[];while(c2.exists()){o2.unshift(c2.id());c2=c2.owner();}
for(var i=0;i<o1.length;i++)
if(o1[i]!=o2[i])
inx(o1[i]).cmd("handleSmoothBlur");}
this.handleMousedown=function(e){var target=e.target;var id=$(target).parents(".inx-box").andSelf().data("id")||null;inx.focusManager.focus(id);}
this.handleMouseMove=function(e){inx.focusManager.lastEvent=e;}
this.checkActivity=function(e){var e=inx.focusManager.lastEvent;if(!e)return;var hash=e.pageX+":"+e.pageY;if(hash!=inx.focusManager.lastHash){inx.focusManager.hashTime=new Date().getTime();inx.focusManager.first=true;inx.popup.hide();}else{if(inx.focusManager.first&&new Date().getTime()-inx.focusManager.hashTime>1000){inx.focusManager.first=false;var id=$(e.target).parents(".inx-box").andSelf().data("id")||null;inx.popup.show(id,e.pageX,e.pageY);}}
inx.focusManager.lastHash=hash;}
this.cmp=function(){return inx.cmp.get(this.focused)}}
$(document).mousedown(inx.focusManager.handleMousedown);$(document).mousemove(inx.focusManager.handleMouseMove);setInterval(inx.focusManager.checkActivity,200);inx.observable=Base.extend({constructor:function(p){this.listeners={};for(var i in p.listeners)
this.on(i,p.listeners[i]);for(var i in p)if(this[i]===undefined)this[i]=p[i];},id:function(){return this.private_id;},cmd:function(name){inx.observable.debug.cmd++;var a=[];for(var i=1;i<arguments.length;i++)a[i-1]=arguments[i];name="cmd_"+name;if(this[name]&&typeof(this[name])=="function")
try{return this[name].apply(this,a)}
catch(ex){inx.msg(this.id()+"["+this.info("param","type")+"]"+":"+name,1);inx.msg(ex,1);}},task:function(name){inx.taskManager.task(this.id(),name)},info:function(name){var a=[];for(var i=1;i<arguments.length;i++)a[i-1]=arguments[i];name="info_"+name;if(this[name]&&typeof(this[name])=="function")
return this[name].apply(this,a);},on:function(event,a,b){inx.cmp.get(this.id()).on(event,a,b);},call:function(p,s,f,m){return inx.cmp.get(this.id()).call(p,s,f,m);},suspendEvents:function(){this.__eventsDisabled=true},unsuspendEvents:function(){this.__eventsDisabled=false},fire:function(){var cmp=inx(this.id());return cmp.fire.apply(cmp,arguments)},bubble:function(event,p1,p2,p3){var cmp=this;cmp.fire(event,p1,p2,p3);while(1){cmp=cmp.owner();if(!cmp.exists())break;cmp.fire(event,p1,p2,p3);}},cmd_destroy:function(){inx.cmp.unregister(this.id());this.fire("destroy");}});inx.observable.debug={cmd:0}
inx.css(".inx-box{background:white;font-family:Verdana;font-size:12px;position:relative;overflow:hidden;color:black;}");inx.box=inx.observable.extend({constructor:function(p){if(!p)p={};p.border=p.border===undefined?1:p.border;this.base(p);},owner:function(){return inx(this.id()).owner();},info_region:function(){return this.region;},info_resizable:function(){return this.resizable;},info_rendered:function(){return this.private_rendered;},info_title:function(){return this.title;},cmd_setTitle:function(title){if(this.title==title)return;this.title=title+"";this.fire("titleChanged");},info_name:function(){return this.name;},cmd_destroy:function(){this.base();if(this.items)
while(this.items.length)
inx(this.items[0]).cmd("destroy");this.owner().cmd("remove",this);$(this.el).remove();},cmd_render:function(container){if(this.info("rendered"))return;if(container)this.container=container;if(!this.container)return;if(!this.el){this.el=$("<div class='inx-box' >").appendTo(this.container);this.el.data("id",this.id());this.background&&this.el.css("background",this.background);}
this.cmd("border",this.border);this.cmd("width",this.width);this.cmd("height",this.height);this.cmd("autoHeight",this.autoHeight);if(this.id()==inx.focusManager.cmp().id())
this.cmd("handleFocusChange",true);if(this.hidden)this.cmd("hide");this.task("checkResize");this.task("completeRender");},cmd_completeRender:function(){this.fire("render");this.private_rendered=true;},info_container:function(){return this.container;},cmd_width:function(width){if(width===undefined)width="auto";if(width<1)width=1;this.w=width;if(this.w!="auto")
this.el&&this.el.width(this.w-(this.border?2:0));else
this.el&&this.el.css("width","auto");this.task("checkResize");},info_width:function(){if(this.hidden)return 0;if(this.el&&this.w=="auto")return this.el.width()+(this.border?2:0);return parseInt(this.w)||0;},info_resizable:function(){return!!this.resizable},cmd_height:function(height){height=parseInt(height)||0;if(height<1)height=1;this.h=height;this.el&&this.el.height(this.h-(this.border?2:0));this.task("checkResize");},info_height:function(){if(this.hidden)return 0;return this.h||0;},cmd_border:function(b){this.border=!!b;this.el&&this.el.css("border",(this.border?1:0)+"px solid #cccccc");this.task("checkResize");},info_innerWidth:function(){return this.info("width")-(this.border?2:0)},info_innerHeight:function(){return this.info("height")-(this.border?2:0)},cmd_handleFocusChange:function(flag){flag=!!flag;if(!this.el)return;this.el.css("borderColor",flag?"blue":"#cccccc");this.fire(flag?"focus":"blur");flag?this.el.addClass("inx-focused"):this.el.removeClass("inx-focused")},cmd_handleSmoothBlur:function(){this.fire("smoothBlur");},cmd_focus:function(){inx.focusManager.focus(this.id());},cmd_blur:function(){inx.focusManager.blur(this.id());},fire_key:function(e){this.fire("keydown",e);return true;},cmd_show:function(){this.hidden=false;if(this.el)this.el.css("display","block");this.fire("show");this.task("checkResize");this.private_hidden=false;},cmd_hide:function(){this.hidden=true;if(this.el)this.el.css("display","none");this.fire("hide");this.task("checkResize");this.private_hidden=true;},info_hidden:function(){return!!this.hidden;},cmd_checkResize:function(){var auto=(this.w=="auto")&&!this.hidden&&!this.owner().exists();inx.box.manager.watch(this.id(),auto);},info_layoutHash:function(){return this.info("width")+":"+this.info("height")+":"+this.border;},info_param:function(key){return this[key];},find:function(name){if(!this.items)return inx(0);for(var i=0;i<this.items.length;i++)
if(inx(this.items[i]).info("param","name")==name)
return inx(this.items[i]);return inx(0);},cmd_syncLayout:function(){this.task("resizeToContents");},cmd_autoHeight:function(flag){this.private_autoHeight=!!flag;if(this.__body)this.__body.css("overflowY",flag?"hidden":"auto");},cmd_resizeToContents:function(){var d=this.info("sideHeight");if(!this.private_autoHeight)return;if(!this.__body)return;var e=$("<div>").css({margin:0}).appendTo(this.__body);var h=e.attr("offsetTop")+(this.private_padding*1||0);e.remove();h+=this.border?2:0;var overflow=false;if(this.maxHeight&&h>this.maxHeight){overflow=true;h=this.maxHeight;}
this.cmd("height",h+d);if(this.__body)this.__body.css("overflowY",overflow?"scroll":"hidden");},cmd_nativeUpdateLoader:function(){if(!inx.conf.ajaxIndicator)return false;var n=inx(this.id()).data("currentRequests");if(!n){$(this.privateLoaderEl).remove();this.privateLoaderEl=null;}
else{if(!this.privateLoaderEl){this.privateLoaderEl=$("<div>").css({background:"white",position:"absolute",padding:5,zIndex:100});$("<img>").attr("src",inx.img("loader")).appendTo(this.privateLoaderEl)}
this.privateLoaderEl.appendTo(this.el)
if(this.__body){var pos=this.__body.position();this.privateLoaderEl.css({top:pos.top,left:pos.left});}}}});inx.box.events=new function(){this.mousedown=function(e){var hit=$(e.target).parents().andSelf().filter(".inx-box").eq(0);inx(hit.data("id")).cmd("mousedown",e);},this.dblclick=function(e){var hit=$(e.target).parents().andSelf().filter(".inx-box").eq(0);inx(hit.data("id")).cmd("dblclick",e);}}
$(document).mousedown(inx.box.events.mousedown);$(document).dblclick(inx.box.events.dblclick);inx.box.loader=inx.box.extend({constructor:function(p){this.initialParams=p;this.base(p);inx.loader.load(p.type,this.id());this.private_cmdBuffer=[];},cmd_render:function(c){this.initialParams.container=c;this.base(c);if(this.el)
this.el.html("<table style='width:100%;height:100%;'><tr><td style='text-align:center;'>"+inx.conf.componentLoadingHTML+"</td></tr></table>");},cmd:function(cmd,p1,p2,p3){this.base(cmd,p1,p2,p3);switch(cmd){case"border":this.initialParams.border=p1;break;case"show":this.initialParams.hidden=0;break;case"hide":this.initialParams.hidden=1;break;case"width":this.initialParams.width=p1;break;case"height":this.initialParams.height=p1;break;case"autoHeight":this.initialParams.autoHeight=p1;break;case"render":this.initialParams.private_renderCalled=true;break;}
if(!this["cmd_"+cmd])
this.private_cmdBuffer.push([cmd,p1,p2,p3]);},cmd_handleLoad:function(){if(this.el)this.el.remove();var p=this.initialParams;p.id=this.id();p.listeners=[];var cmp=inx.cmp.create(p);if(this.initialParams.private_renderCalled)
cmp.cmd("render",this.initialParams.container).cmd("syncLayout");for(var i=0;i<this.private_cmdBuffer.length;i++){var c=this.private_cmdBuffer[i];inx(this.id()).cmd(c[0],c[1],c[2],c[3]);}
this.fire("componentLoaded");},info_loaderObj:function(){return true;}});inx.box.manager=new function(){this.__handleWindowResize=function(){for(var i in this.__watchList)
this.watch(i);}
this.__watchList={};this.__buffer={};this.watch=function(id,watch){this.__buffer[id]=true;this.task();if(watch!==undefined)
watch?this.__watchList[id]=true:delete this.__watchList[id];}
this.task=function(){inx.taskManager.task(this,"__processBuffer");}
this.__processBuffer=function(){delete this.timeout;for(var i in this.__buffer)
this.__checkItem(i);}
this.__checkItem=function(id){var c=inx(id);var hash=c.info("layoutHash");if(hash!=c.data("lastHash")){c.owner().task("syncLayout");c.task("syncLayout");c.data("lastHash",hash);}}}
$(window).resize(function(){inx.box.manager.__handleWindowResize()});inx.command=inx.observable.extend({constructor:function(p){if(!p.data)p.data={};this.base(p);},cmd_exec:function(){this.cmd("count",1);var id=this.id();var json=JSON.stringify(this.data);this.request=$.ajax({url:inx.conf.cmdUrl,data:{data:json},type:"POST",success:function(d){inx(id).cmd('handle',true,d).cmd("destroy");},error:function(r){inx(id).cmd('handle',false,r.responseText).cmd("destroy");}})},cmd_handle:function(success,response){this.cmd("count",-1)
if(!success){inx.msg(response,1);this.fire("error");return;}
var ret=inx.command.parse(response);if(!ret.success){if(ret.text)inx.msg(ret.text,1);this.fire("error");return;}
if(this.meta){if(!ret.meta)ret.meta={};for(var i in this.meta)
ret.meta[i]=this.meta[i];}
this.fire("success",ret.data,ret.meta);},cmd_count:function(p){if(this.lastCount==p)return;this.lastCount=p;var cmp=inx(this.source);var n=(cmp.data("currentRequests")||0)+p;cmp.data("currentRequests",n);cmp.cmd("nativeUpdateLoader",n);},cmd_destroy:function(){try{this.cmd("count",-1);this.request.abort();}
catch(ex){}
this.base();}});inx.command.parse=function(str){try{eval("var data="+str);}catch(ex){return{success:false,text:str};}
for(var i=0;i<data.messages.length;i++){var msg=data.messages[i];inx.msg(msg.text,msg.error);}
return{success:data.completed,data:data.data,meta:data.meta}}
inx.hotkeyManager=new function(){this.init=function(){var that=this;setInterval(function(){that.collectIFrames(window,Math.random())},100);this.handlers={};}
this.collectIFrames=function(wnd,collect_id){if(!wnd)return;if(!wnd.document)return;if($(wnd.document).data("inx.hotkey.collect_id")==collect_id)
return;$(wnd.document).data("inx.hotkey.collect_id",collect_id);var t=this;var ifr=$(wnd.document.body).find("iframe");ifr.each(function(){t.collectIFrames(this.contentWindow,collect_id);})
if(!$(wnd.document).data("inx.hotkey")){$(wnd.document).data("inx.hotkey",true);$(wnd.document).keydown(function(e){t.handleKey(e)});$(wnd.document).keypress(function(e){t.handleKey(e)});$(wnd.document).keyup(function(e){t.handleUp(e)});}}
this.unfreeze=function(){inx.hotkey.prevent=0;inx.hotkey.processed=0;}
this.pressed={};this.handleUp=function(e){this.pressed[e.keyCode]=0;}
this.handleKey=function(p){this.pressed[p.keyCode]=1;setTimeout(function(){inx.hotkeyManager.unfreeze()});if(inx.hotkey.processed){if(inx.hotkey.prevent)
p.preventDefault();return;}
inx.hotkey.processed=true;var hash="c:"+(p.ctrlKey?"1":"0")+"-s:"+(p.shiftKey?"1":"0")+"-key:"+p.keyCode;var handlers=this.handlers[hash];if(!handlers)return;for(var i=0;i<handlers.length;i++){var obj=inx(handlers[i].obj);if(!obj.exists()){handlers.splice(i,1);i--;continue;}
var pa=obj;var hidden=false;while(pa.exists()){if(pa.info("hidden")){hidden=true;break;}
pa=pa.owner();}
if(!hidden&&p.type=='keydown'){var ret=obj.cmd(handlers[i].fn);if(ret===false){p.preventDefault();inx.hotkey.prevent=true;}}}}
this.on=function(p,obj,fn){obj=inx(obj).id();var keys={esc:27,enter:13,f1:112,f2:113,f3:114,f4:115,f5:116,tab:9}
s=(p+"").split("+");p={};for(var i=0;i<s.length;i++){var part=s[i];if(part==parseInt(part))p.keyCode=part;else if(part=="ctrl")p.ctrlKey=true;else if(part=="shift")p.ctrlKey=true;else p.keyCode=keys[part]||part.toUpperCase().charCodeAt();}
var hash="c:"+(p.ctrlKey?"1":"0")+"-s:"+(p.shiftKey?"1":"0")+"-key:"+p.keyCode;if(!this.handlers[hash])this.handlers[hash]=[];this.handlers[hash].push({obj:obj,fn:fn});}
this.init();}
inx.hotkey=function(a,b,c){inx.hotkeyManager.on(a,b,c);}
inx.hotkey.is=function(code){return!!inx.hotkeyManager.pressed[code];}
inx.img=function(name){if(!name)return false;if((name+"").match(/^[\w-_]+$/))
return inx.conf.url+"inx/img/"+name+".gif";return name;}
inx.json={encode:function(data){return JSON.stringify(data);},decode:function(str){try{eval("var data="+str);return data;}
catch(ex){return null;}}}
if(!inx.JSON){JSON={};}
(function(){function f(n){return n<10?'0'+n:n;}
if(typeof Date.prototype.toJSON!=='function'){Date.prototype.toJSON=function(key){return this.getUTCFullYear()+'-'+
f(this.getUTCMonth()+1)+'-'+
f(this.getUTCDate())+'T'+
f(this.getUTCHours())+':'+
f(this.getUTCMinutes())+':'+
f(this.getUTCSeconds())+'Z';};String.prototype.toJSON=Number.prototype.toJSON=Boolean.prototype.toJSON=function(key){return this.valueOf();};}
var cx=/[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,escapeable=/[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,gap,indent,meta={'\b':'\\b','\t':'\\t','\n':'\\n','\f':'\\f','\r':'\\r','"':'\\"','\\':'\\\\'},rep;function quote(string){escapeable.lastIndex=0;return escapeable.test(string)?'"'+string.replace(escapeable,function(a){var c=meta[a];if(typeof c==='string'){return c;}
return'\\u'+('0000'+a.charCodeAt(0).toString(16)).slice(-4);})+'"':'"'+string+'"';}
function str(key,holder){var i,k,v,length,mind=gap,partial,value=holder[key];if(value&&typeof value==='object'&&typeof value.toJSON==='function'){value=value.toJSON(key);}
if(typeof rep==='function'){value=rep.call(holder,key,value);}
switch(typeof value){case'string':return quote(value);case'number':return isFinite(value)?String(value):'null';case'boolean':case'null':return String(value);case'object':if(!value){return'null';}
gap+=indent;partial=[];if(typeof value.length==='number'&&!value.propertyIsEnumerable('length')){length=value.length;for(i=0;i<length;i+=1){partial[i]=str(i,value)||'null';}
v=partial.length===0?'[]':gap?'[\n'+gap+
partial.join(',\n'+gap)+'\n'+
mind+']':'['+partial.join(',')+']';gap=mind;return v;}
if(rep&&typeof rep==='object'){length=rep.length;for(i=0;i<length;i+=1){k=rep[i];if(typeof k==='string'){v=str(k,value);if(v){partial.push(quote(k)+(gap?': ':':')+v);}}}}else{for(k in value){if(Object.hasOwnProperty.call(value,k)){v=str(k,value);if(v){partial.push(quote(k)+(gap?': ':':')+v);}}}}
v=partial.length===0?'{}':gap?'{\n'+gap+partial.join(',\n'+gap)+'\n'+
mind+'}':'{'+partial.join(',')+'}';gap=mind;return v;}}
if(typeof JSON.stringify!=='function'){JSON.stringify=function(value,replacer,space){var i;gap='';indent='';if(typeof space==='number'){for(i=0;i<space;i+=1){indent+=' ';}}else if(typeof space==='string'){indent=space;}
rep=replacer;if(replacer&&typeof replacer!=='function'&&(typeof replacer!=='object'||typeof replacer.length!=='number')){throw new Error('JSON.stringify');}
return str('',{'':value});};}
if(typeof JSON.parse!=='function'){JSON.parse=function(text,reviver){var j;function walk(holder,key){var k,v,value=holder[key];if(value&&typeof value==='object'){for(k in value){if(Object.hasOwnProperty.call(value,k)){v=walk(value,k);if(v!==undefined){value[k]=v;}else{delete value[k];}}}}
return reviver.call(holder,key,value);}
cx.lastIndex=0;if(cx.test(text)){text=text.replace(cx,function(a){return'\\u'+
('0000'+a.charCodeAt(0).toString(16)).slice(-4);});}
if(/^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,'@').replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,']').replace(/(?:^|:|,)(?:\s*\[)+/g,''))){j=eval('('+text+')');return typeof reviver==='function'?walk({'':j},''):j;}
throw new SyntaxError('JSON.parse');};}})();inx.keyManager={handle:function(e){var cmp=inx.focusManager.cmp();if(inx.keyManager.prevent)
e.preventDefault();if(e.type=="keydown")
inx.keyManager.lastKeydown=e.which;if(inx.keyManager.lastKey!=e.which&&inx.keyManager.lastKeydown==e.which){e.stopPropagation();inx.keyManager.lastKey=e.which;while(cmp.exists()){var ret=cmp.cmd("keydown",e);if(ret===false){e.preventDefault();inx.keyManager.prevent=true;break;}else if(ret=="stop"){break;}
cmp=cmp.owner();}}
if(e.type=="keypress"){var cmp=inx.focusManager.cmp();var str=String.fromCharCode(e.which);if(inx.keyManager.realKeypress)
if(!e.ctrlKey){var ret=cmp.cmd("keypress",str);if(ret==false)
e.preventDefault();}}
if(e.type=="keydown")
inx.keyManager.realKeypress=!inx.keyManager.sys[e.which];if(e.type=="keypress")
inx.keyManager.reset();setTimeout(inx.keyManager.reset,100);},reset:function(){inx.keyManager.lastKey=null;inx.keyManager.prevent=false;}}
inx.keyManager.sys={};var sys=[8,9,13,16,17,18,19,20,27,33,34,35,36,37,38,39,40,45,46,112,113,114,115,116,117,118,119,120,121,122,123];for(var i in sys)
inx.keyManager.sys[sys[i]]=true;$(document).keypress(inx.keyManager.handle);$(document).keydown(inx.keyManager.handle);inx.loader={heap:[],code:[],count:0,handlers:[],dependency:[],ready:{},is_requested:function(name){for(var i=0;i<this.heap.length;i++)
if(this.heap[i]==name)
return true;return false;},load:function(name,handler){if(handler)inx.loader.handlers.push(handler);var path=name.split(".");path=inx.conf.url+path.join("/")+".js";if(inx.loader.is_requested(name))return;inx.loader.heap.unshift(name);inx.loader.count++;$.ajax({type:"GET",url:path,cache:true,success:function(data){var include=(data.split("\n")[0]+"").match(/\/\/[ ]*@include(.*)/);include=include?include[1].split(","):[];for(var i in include)
inx.loader.load($.trim(include[i]));inx.loader.count--;inx.loader.code[name]=data;inx.loader.dependency[name]=include;if(inx.loader.count==0)
inx.loader.exec();},error:function(){inx.msg("Script loading error ("+name+")",1)}});},exec:function(){var handlers=[];for(var i=0;i<inx.loader.handlers.length;i++)
handlers.push(inx.loader.handlers[i]);this.handlers=[];for(var i=0;i<inx.loader.heap.length;i++)
inx.loader.eval(inx.loader.heap[i]);for(var i=0;i<handlers.length;i++)
inx(handlers[i]).cmd("handleLoad");},eval:function(name){name=$.trim(name);if(!inx.loader.code[name])return;for(var i=0;i<inx.loader.dependency[name].length;i++)
inx.loader.eval(inx.loader.dependency[name][i]);try{eval(inx.loader.code[name]);}
catch(e){alert(e+"\n"+name);}
inx.loader.code[name]=null;inx.loader.ready[name]=true;},debug:function(){var r=[];for(var i in inx.loader.ready)
r.push(i);return r.join("<br/>");}}
inx.css(".inx-msg-container{top:20px;position:fixed;font-family:Arial;z-index:100001000;}",".inx-msg{ -webkit-border-radius: 5px; width:300px;background:green url(%%inx.msg%%/green.gif) center;;border:2px solid white;color:white;font-size:18px;padding:2px 4px 2px 4px;margin-bottom:2px;}",".inx-msg-error{ -webkit-border-radius: 5px; background:red url(%%inx.msg%%/red.gif) center;}");inx.msg=function(text,error,adv){if(!inx.msg.__container)
inx.msg.__container=$("<div class='inx-msg-container' />").prependTo("body");inx.msg.__container.css("left",$("body").width()-330);if(typeof(text)=="object"){var str="";for(var i in text)
str+=i+" : "+text[i]+"<br/>";text=str;}
var msg=$("<div>").addClass("inx-msg").html(text+"");error&&msg.addClass("inx-msg-error");msg.css("opacity",0).data("name",adv&&adv.name);if(adv&&adv.name&&(msg2=inx.msg.getMessageByName(adv.name))){msg.css("opacity",msg2.css("opacity"));msg2.replaceWith(msg);}else{msg.appendTo(inx.msg.__container);}
msg.animate({opacity:1},500).animate({opacity:1},2000).animate({opacity:0},"slow").hide("slow");}
inx.msg.getMessageByName=function(name){if(!inx.msg.__container)return;var msg=false;inx.msg.__container.children().each(function(){if($(this).data("name")==name)
msg=$(this);});return msg;}
inx.css(".a2op9st4e2jw {font-size:11px;background:#ffeebb;max-width:200px;position:absolute;padding:10px;}");inx.popup={show:function(id,x,y){var popup=inx(id).info("param","popup");if(!popup)return;if(!inx.popup.e)
inx.popup.e=$("<div>").addClass("a2op9st4e2jw").addClass("inx-shadowframe").appendTo("body").css({zIndex:inx.conf.z_index_message})
inx.popup.e.fadeIn(1000).css({left:x+10,top:y+10}).html(popup+"");},hide:function(){if(inx.popup.e)
inx.popup.e.hide();}}
inx.storage={buffer:{},set:function(key,val){inx.storage.buffer[key]=val;if(inx.storage.ready&!inx.storage.dumpPlanned){setTimeout(function(){inx.storage.flush();},300);inx.storage.dumpPlanned=true;}},flush:function(){for(var key in inx.storage.buffer){var val=inx.storage.buffer[key];key=inx.storage.hash(key);try{Storage.put(key,inx.json.encode(val));}
catch(ex){inx.msg("storage error",1);inx.msg(ex,1)}}
inx.storage.dumpPlanned=false;inx.storage.buffer={};},get:function(key){var ret=inx.storage.buffer[key];if(ret!==undefined)return ret;if(!inx.storage.ready)return null;key=inx.storage.hash(key);return inx.json.decode(Storage.get(key));},keys:function(){if(!inx.storage.ready)return[];inx.storage.flush();return Storage.getKeys();},onready:function(id,cmd){id=inx(id).id();if(inx.storage.ready){inx(id).cmd(cmd);return;}
inx.storage.h.push({id:id,cmd:cmd});},h:[],private_init:function(){inx.storage.ready=true;for(var i=0;i<inx.storage.h.length;i++)
inx(inx.storage.h[i].id).cmd(inx.storage.h[i].cmd);inx.storage.flush();},hash:function(key){return key.replace(/\.|\:/g,"_");}},Storage={engines:["WhatWG","userData","Flash8"],swfUrl:"/inx/pub/inx/storage/storage.swf",init:function(onready){for(var i=0;i<this.engines.length;i++){try{this[this.engines[i]](function(){Storage.active=true;onready&&onready()})
return;}catch(e){}}
inx.msg("No storage found",1);}}
Storage.WhatWG=function(onready){var storage=globalStorage[location.hostname];Storage={put:function(key,value){storage[key]=value},get:function(key){return String(storage[key])},remove:function(key){delete storage[key]},getKeys:function(){var list=[]
for(i in storage)list.push(i)
return list},clear:function(){for(i in storage){delete storage[i]}}}
onready()}
Storage.userData=function(onready){var namespace="data"
if(!document.body.addBehavior){throw new Error("No addBehavior available")}
var storage=document.getElementById('storageElement');if(!storage){storage=document.createElement('span')
document.body.appendChild(storage)
storage.addBehavior("#default#userData");storage.load(namespace);}
Storage={put:function(key,value){storage.setAttribute(key,value)
storage.save(namespace)},get:function(key){return storage.getAttribute(key)},remove:function(key){storage.removeAttribute(key)
storage.save(namespace)},getKeys:function(){var list=[]
var attrs=storage.XMLDocument.documentElement.attributes
for(var i=0;i<attrs.length;i++){list.push(attrs[i].name)}
return list},clear:function(){var attrs=storage.XMLDocument.documentElement.attributes
for(var i=0;i<attrs.length;i++){storage.removeAttribute(attrs[i].name)}
storage.save(namespace)}}
onready()}
Storage.Flash8=function(onready){var movie
var swfId="StorageMovie"
while(document.getElementById(swfId))swfId='_'+swfId
var swfUrl=Storage.swfUrl
Storage={put:function(key,value){movie.put(key,value)},get:function(key){return movie.get(key)},remove:function(key){movie.remove(key)},getKeys:function(){return movie.getkeys()},clear:function(){movie.clear()},ready:function(){movie=document[swfId]
onready();}}
var protocol=window.location.protocol=='https'?'https':'http'
var containerStyle="width:0; height:0; position: absolute; z-index: 10000; top: -1000px; left: -1000px;"
var objectHTML='<embed src="'+swfUrl+'" '
+' bgcolor="#ffffff" width="0" height="0" '
+'id="'+swfId+'" name="'+swfId+'" '
+'swLiveConnect="true" '
+'allowScriptAccess="sameDomain" '
+'type="application/x-shockwave-flash" '
+'pluginspage="'+protocol+'://www.macromedia.com/go/getflashplayer" '
+'></embed>'
var div=document.createElement("div");div.setAttribute("id",swfId+"Container");div.setAttribute("style",containerStyle);div.innerHTML=objectHTML;document.body.appendChild(div)}
$(function(){Storage.init(inx.storage.private_init)});inx.taskManager={taskList:[],taskKeys:{},task:function(id,name){if(typeof(id)=="object"){var obj=id;if(!obj.private_taskManagerID)
obj.private_taskManagerID=inx.id();id=obj.private_taskManagerID;var key=id+":"+name;var tm=inx.taskManager;if(!tm.taskKeys[key]){tm.taskList.push([obj,name])
tm.taskKeys[key]=key;}}else{var key=id+":"+name;var tm=inx.taskManager;if(!tm.taskKeys[key]){tm.taskList.push([id,name])
tm.taskKeys[key]=key;}}
if(!tm.timeout)tm.timeout=setTimeout(function(){tm.exec()});},exec:function(dep){var l=inx.taskManager.taskList;if(!l.length)return;dep=dep?dep+1:1;if(dep>100){alert("task depth limit!");return;}
inx.taskManager.taskList=[];inx.taskManager.taskKeys={};inx.taskManager.timeout=false;for(var i in l){if(typeof(l[i][0])=="object"){l[i][0][l[i][1]]();}
else
inx(l[i][0]).cmd(l[i][1]);}
inx.taskManager.exec(dep);}}
inx.wheel=function(e){if(!e)e=window.event;var delta=e.wheelDelta;if(e.detail)delta=-e.detail*40;var target=e.target;if(e.srcElement)target=e.srcElement;target=$(target);var id=$(target).parents(".inx-box").andSelf().data("id")||null;if(inx(id).cmd("mousewheel",delta)===false){if(e.preventDefault)
e.preventDefault();e.returnValue=false;if(e.stopPropagation)e.stopPropagation();}}
window.onmousewheel=document.onmousewheel=inx.wheel;if(window.addEventListener)
window.addEventListener('DOMMouseScroll',inx.wheel,false);