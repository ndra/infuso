// @include inx.list

inx.grid=inx.list.extend({constructor:function(p){this.head=inx({type:"inx.grid.head",region:"top"});if(!p.side)p.side=[];p.side.push(this.head);this.base(p);this.on("data",[this.id(),"handleLoad2"]);},cmd_handleLoad2:function(data,meta){this.head.cmd("setColData",meta.cols);},renderer:function(e,data){var cols=this.head.info("cols");var width=7;for(var i in cols){var value=data[cols[i].name];if(value){var td=$("<div>").addClass("ubd4v2nfv").css({width:cols[i].width-4,whiteSpace:"nowrap",overflow:"hidden",position:"absolute",left:width,top:2}).addClass("inx-core-inlineBlock").appendTo(e);if(cols[i].type=="image")
$("<img>").css({position:"relative"}).attr("src",inx.img(value)).appendTo(td);else
td.html(value+"");}
width+=cols[i].width;}
e.css({overflow:"visible",paddingTop:17,position:"relative"});if(data.text)
$("<div style='white-space:normal;padding:10px 0px 10px 0px;' >").html(data.text).appendTo(e);},cmd_render:function(c){this.base(c);this.__body.css({whiteSpace:"nowrap"});this.__body.scroll(inx.cmd(this,"handleScroll"))},cmd_handleScroll:function(e){this.head.cmd("setScroll",this.__body.scrollLeft());},cmd_mousedown:function(e){if($(e.target).parents().andSelf().filter(".ubd4v2nfv").length){var x=e.pageX+this.__body.scrollLeft();var cols=this.head.info("cols");for(var i in cols)
if(cols[i].left*1>x){e.col=cols[i-1].name;break;}}
this.base(e);},info_col:function(col,key){var cols=this.head.info("cols");for(var i in cols)
if(cols[i].name==col)
return cols[i][key];}})