// @include inx.panel,inx.layout.fit

inx.css("html,body{padding:0px;margin:0px;overflow:hidden;}");inx.viewport=inx.panel.extend({constructor:function(p){if(!p)p={};p.border=0;p.layout="inx.layout.fit";this.base(p);},cmd_render:function(c){this.base(c);var id=this.id();$(window).resize(function(){inx(id).task("syncToWindow");});this.task("syncToWindow");},cmd_syncToWindow:function(){this.base();this.el.css("overflow","hidden");var e=$("<div style='position:absolute;width:1px;height:1px;background:red;' ></div>").prependTo("body");var top=e.offset().top;e.remove();var e=$("<div style='position:absolute;width:1px;height:1px;background:red;' ></div>").appendTo("body");var bottom=e.offset().top;e.remove();var ch=$(this.container).height();var height=(ch+$("html").get(0).clientHeight-(bottom-top));this.cmd("height",height);}});