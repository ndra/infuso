// @include inx.panel

inx.panel.accordion=inx.panel.extend({constructor:function(p){p.layout="inx.panel.accordion.layout";this.base(p);}})
inx.panel.accordion.layout={create:function(){this.private_secondBody=$("<div>").css({padding:10}).appendTo(this.__body);},add:function(cmp){cmp=inx(cmp);if(cmp.info("param","collapsible"))
$("<div>").appendTo(this.private_secondBody).html("<img src='"+inx.img("expand")+"' align='absmiddle' />"+cmp.info("title")+"").css({fontWeight:"bold",marginBottom:5,cursor:"pointer"}).data("cmpid",cmp.id()).addClass("inx-unselectable").click(inx.panel.accordion.layout.onclick);var e=$("<div>").css({marginBottom:10}).appendTo(this.private_secondBody);cmp.cmd("render",e);},remove:function(cmp){},sync:function(){}}
inx.panel.accordion.layout.onclick=function(){var cmp=inx($(this).data("cmpid"));cmp.cmd(cmp.info("hidden")?"show":"hide");}