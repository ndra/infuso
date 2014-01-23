// @include inx.panel,inx.form,inx.dialog,inx.textfield
/*-- /mod/bundles/inx/src/inx/code.js --*/


inx.css(
    ".inx-code-container {position:absolute;min-width:100%;font-size:14px;font-family:Consolas,Courier New,monospace;}",
    ".inx-code-container div{white-space:nowrap;margin-left:50px;height:18px;cursor:text;}",    
   
    ".inx-code-string {font-weight:bold;color:brown}",
    ".inx-code-digit {color:blue}",
    ".inx-code-comment {color:gray;font-style:italic;}",
    ".inx-code-variable {color:blue;font-weight:bold;}",    
    ".inx-code-keyword {font-weight:bold;}",

    ".inx-code-container .selected, .inx-code-container .selected * {background:#0a246a;color:white;}",
    ".inx-code-lineNumbers {background:#ededed;width:45px;position:absolute;top:0;left:0;border-right:1px dotted gray;overflow:hidden;cursor:text;}",
    ".inx-code-lineNumbers div{height:16px;padding-top:2px;padding-right:5px;text-align:right;font-size:11px;color:gray;}"
    
);

inx.code = inx.panel.extend({

    constructor:function(p) {
            
        if(!p.style)
            p.style = {}
        p.style.vscroll = true;
        p.style.hscroll = true;

        this.base(p);
        if(!this.value)
            this.value = "";
        this.lines = [];
        this.private_lineHeight = 18;
        this.private_letterWidth = 8;
        this.private_lineNumbersWidth = 46;
        this.private_selectionStart = {line:0,symbol:0};
        this.private_selectionEnd = {line:0,symbol:0};
        this.private_renderedLineNumbers = 0;
        this.parser = inx({
            type:"inx.code.parser",
            editor:this,lang:this.lang
        });
        this.spaceStr = "";
        for(var i=0;i<100;i++)
            this.spaceStr+= "          ";
        inx.storage.onready(this.id(),"none");      
        
        this.on("scroll","handleScroll");
        this.on("dblclick","handleDblClick");
          
    },
    
    cmd_handleDblClick:function() {
        this.task("selectWord");
    },
    
    cmd_selectWord:function() {
    
       var sel1 = this.info("selection").start;        
        for(var i=0;i<1000;i++) {
            var x = this.info("textFragment",{
                start:{line:sel1.line,symbol:sel1.symbol},
                end:{line:sel1.line,symbol:sel1.symbol+1}
            });
            if(!x.match(/[0-9a-z\_\$]/i)) {
                break;
            }
            sel1.symbol--;                
        }        
        sel1.symbol++;       
        
        var sel2 = this.info("selection").start;        
        for(var i=0;i<1000;i++) {
            var x = this.info("textFragment",{
                start:{line:sel2.line,symbol:sel2.symbol},
                end:{line:sel2.line,symbol:sel2.symbol+1}
            });
            if(!x.match(/[0-9a-z\_\$]/i))
                break;
            sel2.symbol++;                
        }       
        sel2.symbol;            
         
        this.cmd("select",sel1,sel2)
    },
    
    info_contentHeight:function() {
        return this.private_lineHeight * this.lines.length;
    },
    
    info_contentWidth:function() {
        var w = 0;
        for(var i in this.lines)
            if(this.lines[i].code.length>w)
                w = this.lines[i].code.length;

        w++;                
        w *= this.private_letterWidth;
        w+=  + this.private_lineNumbersWidth;
        
        return w;
    },
    
    cmd_destroy:function() {
        this.parser.cmd("destroy");
        this.base();
    },
    
    cmd_render:function(c) {
    
        this.base(c);
        var id = this.id();
        
        this.lineNumbers = $("<div>").appendTo(this.__body).addClass("inx-code-lineNumbers");
                        
        this.codeContainer = $("<div>").appendTo(this.__body)
            .addClass("inx-unselectable")
            .addClass("inx-code-container");
            
        inx.dd.enable(this.__body,this,"test",{offset:0});
        
        this.cursor = $("<div>")
            .css({
                height:this.private_lineHeight,
                width:2,
                position:"absolute",
                background:"black"
            }).appendTo(this.__body);
            
        this.cmd("setValue",this.value);
        
        this.task("updateLetterWidth");
            
        inx.hotkey("ctrl+f",[this.id(),"showSearchDlg"]);
        inx.hotkey("f3",[this.id(),"search"]);
        inx.hotkey("esc",[this.id(),"focus"]);
    },
    
    cmd_updateLetterWidth:function() {
    
        if(!this.ruler) {
        
            this.ruler = $("<span>").css({
                position:"absolute",
                whiteSpace:"nowrap",
                top:-200
            }).appendTo(this.codeContainer);
            
            var str = "";
            for(var i=0;i<100;i++)
                str+="**********";
                
            this.ruler.html(str);
        
        }
        
        this.private_letterWidth = this.ruler.width()/1000;        
    },
    
    cmd_handleScroll:function(e) {
        this.cmd_updateCodeDelayed();
        this.parser.cmd("scrollChanged");
        this.cmd("updateLineNumbers");
    },
    
    cmd_updateLineNumbers:function() {
        var visible = this.info("visibleLines").bottom;
        for(var i=this.private_renderedLineNumbers;i<visible;i++) {
            $("<div>").html(i+1).appendTo(this.lineNumbers);
        }
        this.private_renderedLineNumbers = i;
    },
    
    cmd_test:function(p) {    
        var x = p.event.pageX - this.__body.offset().left;
        var y = p.event.pageY - this.__body.offset().top;
        x+= this.__body.get(0).scrollLeft;
        y+= this.__body.get(0).scrollTop;        
        var carret = this.coordsToCarret(x,y);
        if(p.phase=="start")
            this.cmd("select",!p.shiftKey ? carret : null,carret);
        this.cmd("select",null,carret);
    },

    info_line:function(line) {
        var line = this.lines[line];
        if(line) return line.code+"";
        return "";
    },
    
    // Добавляет линию перед before
    cmd_insertLine:function(before,code) {
    
        var el = document.createElement("div");
        if(before==this.lines.length)
            this.codeContainer.get(0).appendChild(el);        
        else {
            if(before<this.lines.length) {
                var b = this.lines[before].el;
                this.codeContainer.get(0).insertBefore(el,b);
            } else {
                inx.msg("!!!",1);
                return;
            }
        }
        
        // Пишем в журнал
        this.cmd("writeLog",{
            type:"insert",
            before:before
        });
        
        var line = {
            code:code,
            el:el,
            clean:{}
        }
        
        this.lines.splice(before,0,line);        
        this.task("updateCode");
        this.parser.cmd("lineChanged",before);
    },
    
    cmd_deleteLine:function(n) {

        if(this.lines.length<=1)    
            return;
    
        var line = this.lines[n];
        if(!line) return;
        
        // Пишем в журнал
        this.cmd("writeLog",{
            type:"delete",
            line:n,
            data:this.info("line",n)
        });
        
        line.el.parentNode.removeChild(line.el);
        this.lines.splice(n,1);
        this.task("updateCode");
        this.parser.cmd("lineChanged",n);
    },
    
    cmd_writeLog:function(data) {
    
        // Пишем в журнал
        data.t = this.private_transaction++;
        data.sel = this.info("selection");
        if(this.private_enableLog) {
            this.private_transaction++;
            this.private_log.push(data);
        }
        
        // Группируем действия с интервалом меньше 200 мс
        var groupInterval = 10; 
        try{
            clearTimeout(this.private_transactionTimeout);
        } catch(ex) {}
        this.private_transactionTimeout = setTimeout(inx.cmd(this,"beginTransaction"),groupInterval);
        
        
        var maxlog = 1000000;
        if(this.private_log.length>maxlog) {
            this.private_log = this.private_log.slice(-maxlog+100);
            inx.msg(this.private_log.length)
        }
        
    },
    
    cmd_updateLine:function(n,code) {    
    
        var line = this.lines[n];
        if(!line) return;
        
        // Пишем в журнал
        this.cmd("writeLog",{
            type:"update",
            line:n,
            data:this.info("line",n)
        });
        
        line.code = code;
        line.clean.code = 0;        
        this.parser.cmd("lineChanged",n);

        this.task("updateCodeDelayed");
        
    },
    
    
    cmd_updateLineStyle:function(n,style) {    
    
        var line = this.lines[n];
        if(!line) return;
        
        line.style = style;
        line.clean.code = 0;

        this.task("updateCodeDelayed");
        
    },
    
    private_log:[],
    
    private_transaction:0,
    
    private_enableLog:true,
    
    cmd_beginTransaction:function() {
        this.private_transaction = 0;
    },
    
    cmd_undo:function() {
        this.private_enableLog = false;
        do {
            var log = this.private_log.pop();
            if(!log)
                break;
                
            switch(log.type) {
                case "update":
                    this.cmd("updateLine",log.line,log.data);
                    break;
                case "insert":
                    this.cmd("deleteLine",log.before);
                    break;
                case "delete":
                    this.cmd("insertLine",log.line);
                    this.cmd("updateLine",log.line,log.data);
                    break;
            }
            
            if(log.t<=0) {
                this.cmd("select",log.sel.start,log.sel.end);
                break;
            }
            
        } while(1)
        this.private_enableLog = true;
    },
    
    cmd_insert:function(r) {
    
        this.cmd("cut");

        var sel = this.info("selection");
        var a = this.info("line",sel.end.line).substr(0,sel.end.symbol);
        var b = this.info("line",sel.end.line).substr(sel.end.symbol);
        
        //var re = r.split("\n");
        var re = this.private_cleanText(r+"").split("\n");
        
        
        var s = (re.length==1 ? a.length : 0 )+re[re.length-1].length;
        re[0] = a + re[0];
        re[re.length-1] += b;

        this.cmd("updateLine",sel.end.line,re[0]);

        for(var i=1;i<re.length;i++)
            this.cmd("insertLine",sel.end.line+i,re[i]);

        this.cmd("select",null,{line:sel.end.line+re.length-1,symbol:s});
        this.cmd("collapseToEnd");
    },
    
    cmd_insertPressed:function(str) {
    
        var autocomplete = [
            {a:"(",b:")"},
            {a:"[",b:"]"},
            {a:"{",b:"}"},
            {a:"<",b:">"},
            {a:"'",b:"'"},
            {a:"\"",b:"\""}
        ];
    
        for(var i in autocomplete) {
            var a = autocomplete[i].a;
            var b = autocomplete[i].b;
            if(str==a) {
                var sel = this.info("selectedText");
                str = a + sel + b;
                this.cmd("insert",str);
                if(sel.length==0)
                    this.cmd("moveSelectionBack");
                return;
            }
        }
        
        this.cmd("insert",str);        
    },
    
    cmd_cut:function() {
        var sel = this.info("selection");
        var s1 = sel.start;
        var s2 = sel.end;
       
        var a = this.info("line",s1.line).substr(0,s1.symbol);
        var b = this.info("line",s2.line).substr(s2.symbol);
        
        var d = s2.line - s1.line;
        for(var i=0;i<d;i++)
            this.cmd("deleteLine",s1.line+1);
            
        this.cmd("updateLine",s1.line,a+b);
        this.cmd("select",{line:s1.line,symbol:s1.symbol},{line:s1.line,symbol:s1.symbol});
    },
    
    cmd_setIdent:function(line,ident) {
        var str = this.info("line",line).replace(/^[ ]*/,"");
        this.cmd("updateLine",line,this.spaceStr.substr(0,ident)+str);
    },
    
    info_ident:function(line) {
        var m = this.info("line",line).match(/^[ ]*/);
        return m ? m[0].length : 0;    
    },
    
    cmd_syncLayout:function() {
        this.base();
        this.cmd("updateCodeDelayed");
        this.private_updateLineNumbers();
    },
    
    private_updateLineNumbers:function() {
        if(!this.__body)
            return;
        this.cmd("updateLineNumbers");
    },
    
    cmd_updateCode:function() { 
        if(!this.__body)
            return;       
        var lines = this.info("visibleLines");
        for(var i=lines.top;i<=lines.bottom;i++)
            this.private_updateLine(i);
        this.private_finalizeLineEnds();
        this.updateCodeTask = 0;
        this.private_updateLineNumbers();        
        this.task("updateScroll");
    },
    
    cmd_updateCodeDelayed:function() {
        if(!this.updateCodeTask) {
            this.updateCodeTask = 1;
            var id = this.id();
            setTimeout(function(){inx(id).cmd("updateCode")},50);
        }
    },    
    
    private_updateLine:function(n) {
   
        var line = this.lines[n];
        if(!line) return;
        
        // Обновляем html строки
        if(!line.clean.code) {
        
            var code = line.code;        
            var klass = "";
            var split = [];
            for(var i in line.style) split.push(i);
            split.push(code.length);
            split.sort(function(a,b){return a-b});

            var str = [];
            for(var s=0;s<split.length;s++) {
                var from = split[s-1] || 0;
                var length = split[s]-from;
                var substr = code.substr(from,length);
                klass = (line.style && line.style[from]) || klass;
                // Добавляем спан в поток
                str.push("<span class='inx-code-"+klass+" inx-code-piece'>");
                str.push(this.private_escape(substr));
                str.push("</span>");
            }
            line.el.innerHTML = str.join("");
            line.clean.code = 1;
        }
        
        // Обновляем выделение строки
        var sel = this.info("selection");       
        if(n>sel.start.line && n<sel.end.line)
            line.el.className = "selected";
        else
            line.el.className = "";
            
        if(n==sel.start.line && n==sel.end.line) {
            if(!this.info("selectionCollapsed"))
                this.private_renderLineEnd(n,sel.start.symbol,sel.end.symbol);        
        }
        else {
            if(n==sel.start.line)
                this.private_renderLineEnd(n,sel.start.symbol,"auto");
            if(n==sel.end.line)
                this.private_renderLineEnd(n,0,sel.end.symbol);
        }
            
        // Обновляем курсор
        if(!this.info("selectionCollapsed")) {
            this.cursor.css({display:"none"});
        } else {
            var cursor = this.carretToCoords(sel.end.line,sel.end.symbol);
            this.cursor.css({display:"block",top:cursor.y,left:cursor.x});
        }
            
    },
    
    private_renderLineEnd:function(line,from,to) {
        if(!this.private_lineEnd) {
            this.private_lineEnd = [];
            for(var i=0;i<2;i++) {
                this.private_lineEnd[i] = document.createElement("div");
                this.private_lineEnd[i].style.position = "absolute";
                this.private_lineEnd[i].className = "selected zz";
                this.codeContainer.get(0).appendChild(this.private_lineEnd[i]);
            }
        }
        var n = this.private_currentLineEnd || 0;
        this.private_lineEnd[n].style.top = line*this.private_lineHeight+"px";        
        this.private_lineEnd[n].innerHTML = this.lines[line].el.innerHTML;
        
        from = from*this.private_letterWidth + "px";
        to = (to=="auto") ? "auto" : to*this.private_letterWidth + "px";
        this.private_lineEnd[n].style.clip = "rect(auto "+to+" auto "+from+")";
        this.private_lineEnd[n].style.display = "block";
        this.private_currentLineEnd = n+1;
    },
    
    private_finalizeLineEnds:function() {
        if(!this.private_lineEnd) return;
        for(var i=this.private_currentLineEnd;i<2;i++)
            this.private_lineEnd[i].style.display = "none";
        this.private_currentLineEnd = 0;
    },
    
    cmd_select:function(start,end) {
        if(start)this.private_selectionStart = {line:start.line,symbol:start.symbol};
        if(end)this.private_selectionEnd = {line:end.line,symbol:end.symbol};
        this.task("updateCode");
        this.task("scrollToCarret");
    },
    
    info_selection:function(noflip) {
        var start = this.private_selectionStart;
        start = {line:start.line,symbol:start.symbol};
        var end = this.private_selectionEnd;
        end = {line:end.line,symbol:end.symbol};
        if(!noflip) {
            var flip = false;
            if(start.line>end.line) flip = true;
            if(start.line==end.line & start.symbol>end.symbol) flip = true;
            if(flip) {
                var tmp = start;
                start = end;
                end = tmp;
            }
        }
        
        this.private_updateSelectionEdge(start);
        this.private_updateSelectionEdge(end);
        return {start:start,end:end};
    },
    
    private_updateSelectionEdge:function(edge) {
        if(edge.line<0)edge.line=0;
        if(edge.symbol<0) edge.symbol=0;
        if(edge.line>this.lines.length-1) edge.line = this.lines.length-1;
        var len = this.info("line",edge.line).length;
        if(edge.symbol>len)edge.symbol = len;
    },
    
    info_selectionCollapsed:function() {
        var sel = this.info("selection");
        if(sel.start.line!=sel.end.line) return false;
        if(sel.start.symbol!=sel.end.symbol) return false;
        return true;
    },
    
    cmd_moveSelectionFront:function(hold) {
        var sel = this.info("selection",1).end;
        sel.symbol++;
        if(sel.symbol>this.info("line",sel.line).length && sel.line<this.lines.length-1) {
            sel.symbol = 0;
            sel.line++;
        }
        this.cmd("select",(hold?0:sel),sel)
    },
    
    cmd_moveSelectionBack:function(hold) {
        var sel = this.info("selection",1).end;
        if(sel.symbol>0) {
            sel.symbol--;
        } else {
            if(sel.line>0) {
                sel.line--;
                sel.symbol = this.info("line",sel.line).length;
            }            
        }        
        this.cmd("select",(hold?0:sel),sel)
    },
    
    cmd_moveSelectionTop:function(hold,lines) {
        if(lines===undefined)
            lines = 1;
        var sel = this.info("selection",1).end;
        sel.line -= lines;
        this.cmd("select",(hold?0:sel),sel)
    },
    
    cmd_moveSelectionBottom:function(hold,lines) {
        if(lines===undefined)
            lines = 1;
        var sel = this.info("selection",1).end;
        sel.line += lines;
        this.cmd("select",(hold?0:sel),sel)
    },
    
    // Возвращает каретку в начало строки
    cmd_moveSelectionHome:function(hold) {
        var sel = this.info("selection",1).end;        
        var ident = this.info("ident",sel.line);
        sel.symbol = sel.symbol == ident ? 0 : ident;
        this.cmd("select",(hold?0:sel),sel)
    },
    
    cmd_moveSelectionEnd:function(hold) {
        var sel = this.info("selection",1).end;        
        sel.symbol = this.info("line",sel.line).length;
        this.cmd("select",(hold?0:sel),sel)
    },
    
    cmd_collapseToEnd:function() {
        var sel = this.info("selection",1);
        this.cmd("select",sel.end,sel.end);
    },
    
    info_selectedText:function() {
        var sel = this.info("selection");
        return this.info("textFragment",sel);
    },
    
    info_textFragment:function(sel) {
        if(sel.start.line==sel.end.line) {
            return this.info("line",sel.start.line).substr(sel.start.symbol,sel.end.symbol-sel.start.symbol);
        }else {
            var s1 = sel.start;
            var s2 = sel.end;
            var ret = [this.info("line",s1.line).substr(s1.symbol)];
            for(var i=s1.line+1;i<s2.line;i++)
                ret.push(this.info("line",i));
            ret.push(this.info("line",s2.line).substr(0,s2.symbol));
            return ret.join("\n");
        }
    },
    
    cmd_selectAll:function() {
        if(!this.lines) return;
        if(!this.lines.length) return;
        this.cmd("select",{line:0,symbol:0},{line:this.lines.length-1,symbol:this.lines[this.lines.length-1].code.length});    
    },
    
    // Возвращает объект {top:a,bottom:b} с верхней и нижней линией в окне
    info_visibleLines:function() {
        var l1 = this.info("scrollTop");
        var l2 = l1 + this.info("clientHeight");
        l1 = Math.floor(l1/this.private_lineHeight);
        l2 = Math.ceil(l2/this.private_lineHeight);
        return {top:l1,bottom:l2};
    },
    
    // Преоразует координаты в номер строки и символа
    coordsToCarret:function(x,y) {
        x-=50;
        var line = Math.floor(y/this.private_lineHeight);
        var symbol = Math.floor(x/this.private_letterWidth+0.5);
        return {line:line,symbol:symbol};
    },
    
    // Преобразует строку и символ в координаты
    carretToCoords:function(line,symbol) {
        var x = symbol*this.private_letterWidth+50;
        var y = line*this.private_lineHeight;
        return {x:x,y:y};
    },
    
    private_cleanText:function(str) {
        str = str.replace(/\t/g,"    ")
            .replace(/\r\n/g,"\n")
            .replace(/\r/g,"\n");
        return str;    
    },   
    
    private_escape:function(str) {
        str = str.replace(/&/gm, '&amp;')
            .replace(/</gm, '&lt;')
            .replace(/>/gm, '&gt;')
            .replace(/ /gm, '&nbsp;');
        return str;
    },

    cmd_scrollToCarret:function() {
    
        var sel = this.info("selection",1).end;
        var coords = this.carretToCoords(sel.line,sel.symbol);        
        
        var width = this.info("clientWidth")-this.private_letterWidth-60;
        if(width<0)
            return;
       
        var x = coords.x - this.info("scrollLeft")-60;
        if(x<0) {
            this.cmd("scrollLeft",this.info("scrollLeft")+x);
        }
        if(x>width) {
            this.cmd("scrollLeft",this.info("scrollLeft")+x-width);
        }
                
        var y = coords.y - this.info("scrollTop");
        if(y<0)
            this.cmd("scrollTop",this.info("scrollTop")+y);
        var height = this.info("clientHeight")-this.private_lineHeight;
        if(y>height)
            this.cmd("scrollTop",this.info("scrollTop")+y-height);

    },
    
    cmd_mousewheel:function(delta) {
        if(this.cmd("scroll",delta < 0 ? 2 : -2))
        return false;
    },
    
    cmd_scroll:function(line) {
        var s1 = this.info("scrollTop");
        s2 = Math.floor(s1/this.private_lineHeight) + line;
        s2*= this.private_lineHeight;
        this.cmd("scrollTop",s2);
        return s1!=this.info("scrollTop");
    },
    
    info_value:function() {
        var ret = [];
        for(var i in this.lines)
            ret.push(this.lines[i].code);
        return ret.join("\n");
    },
    
    cmd_setValue:function(value) {
        if(value===0)
            value="0";
        if(!value)
            value = "";
        this.cmd("selectAll");
        this.cmd("cut");
        var code = this.private_cleanText(value+"").split("\n");
        for(var i=0;i<code.length;i++)
            this.cmd_insertLine(i,code[i]);
        this.private_log = [];
    },
    
    info_debug:function() {
        var ret = "";
        ret += "lines: "+this.lines.length+"<br/>";
        var sel = this.info("selection");
        ret += "selection: "+sel.start.line+":"+sel.end.line+" &mdash;";
        ret += sel.end.line+":"+sel.end.symbol;
        return ret;
    }    

});


/*-- /mod/bundles/inx/src/inx/code/bundle.js --*/


inx.code = inx.code.extend({

    info_range:function(start,end) {
        if(start.line==end.line) {
            return this.info("line",start.line).substr(start.symbol,end.symbol-start.symbol);
        }else {
            var s1 = start;
            var s2 = end;
            var ret = [this.info("line",s1.line).substr(s1.symbol)];
            for(var i=s1.line+1;i<s2.line;i++)
                ret.push(this.info("line",i));
            ret.push(this.info("line",s2.line).substr(0,s2.symbol));
            return ret.join("\n");
        }
    },
    
    cmd_bundle:function() {
    
        // Ищем сниппет
        var sel = this.info("selection");
        sel.start.symbol = 0;        
        var str = this.info("range",sel.start,sel.end);
        var snippetCode = "";
        for(var i in this.snippets) {
            var l = i.length;
            if(str.substr(str.length-l,l)==i) {
                if(snippetCode.length<this.snippets[i].length) {
                    snippet = this.snippets[i];
                    snippetCode = i;
                }
            }
        }
        
        // Если сниппет найден, заменяем его
        if(snippetCode) {
            var snippet = this.snippets[snippetCode];
            var sel = this.info("selection");
            sel.start.symbol-=snippetCode.length;
            this.cmd("select",sel.start,sel.end);
            this.cmd("insert",snippet);
    
            // Если в сниппете обнаружен знак |, ставим курсор на его место
            var pos = snippet.indexOf("|");
            if(pos>0) {
                pos = snippet.length - pos;
                for(var i=0;i<pos;i++)
                    this.cmd("moveSelectionBack");
                this.cmd("moveSelectionFront",1);                    
                this.cmd("cut");
            }
            
            return true;
        }
        
    }
    
});

/*-- /mod/bundles/inx/src/inx/code/bundles.js --*/


inx.code = inx.code.extend({

    snippets:{
        "@i": "@import url(|);",
        "@m": "@media print {\n\t|\n}",
        "@f": "@font-face {\n\tfont-family:|;\n\tsrc:url(|);\n}",
        "!": "!important",
        "pos": "position:|;",
        "pos:s": "position:static;",
        "pos:a": "position:absolute;",
        "pos:r": "position:relative;",
        "pos:f": "position:fixed;",
        "t": "top:|;",
        "t:a": "top:auto;",
        "r": "right:|;",
        "r:a": "right:auto;",
        "b": "bottom:|;",
        "b:a": "bottom:auto;",
        "l": "left:|;",
        "l:a": "left:auto;",
        "z": "z-index:|;",
        "z:a": "z-index:auto;",
        "fl": "float:|;",
        "fl:n": "float:none;",
        "fl:l": "float:left;",
        "fl:r": "float:right;",
        "cl": "clear:|;",
        "cl:n": "clear:none;",
        "cl:l": "clear:left;",
        "cl:r": "clear:right;",
        "cl:b": "clear:both;",
        "d": "display:|;",
        "d:n": "display:none;",
        "d:b": "display:block;",
        "d:ib": "display:inline;",
        "d:li": "display:list-item;",
        "d:ri": "display:run-in;",
        "d:cp": "display:compact;",
        "d:tb": "display:table;",
        "d:itb": "display:inline-table;",
        "d:tbcp": "display:table-caption;",
        "d:tbcl": "display:table-column;",
        "d:tbclg": "display:table-column-group;",
        "d:tbhg": "display:table-header-group;",
        "d:tbfg": "display:table-footer-group;",
        "d:tbr": "display:table-row;",
        "d:tbrg": "display:table-row-group;",
        "d:tbc": "display:table-cell;",
        "d:rb": "display:ruby;",
        "d:rbb": "display:ruby-base;",
        "d:rbbg": "display:ruby-base-group;",
        "d:rbt": "display:ruby-text;",
        "d:rbtg": "display:ruby-text-group;",
        "v": "visibility:|;",
        "v:v": "visibility:visible;",
        "v:h": "visibility:hidden;",
        "v:c": "visibility:collapse;",
        "ov": "overflow:|;",
        "ov:v": "overflow:visible;",
        "ov:h": "overflow:hidden;",
        "ov:s": "overflow:scroll;",
        "ov:a": "overflow:auto;",
        "ovx": "overflow-x:|;",
        "ovx:v": "overflow-x:visible;",
        "ovx:h": "overflow-x:hidden;",
        "ovx:s": "overflow-x:scroll;",
        "ovx:a": "overflow-x:auto;",
        "ovy": "overflow-y:|;",
        "ovy:v": "overflow-y:visible;",
        "ovy:h": "overflow-y:hidden;",
        "ovy:s": "overflow-y:scroll;",
        "ovy:a": "overflow-y:auto;",
        "ovs": "overflow-style:|;",
        "ovs:a": "overflow-style:auto;",
        "ovs:s": "overflow-style:scrollbar;",
        "ovs:p": "overflow-style:panner;",
        "ovs:m": "overflow-style:move;",
        "ovs:mq": "overflow-style:marquee;",
        "zoo": "zoom:1;",
        "cp": "clip:|;",
        "cp:a": "clip:auto;",
        "cp:r": "clip:rect(|);",
        "bxz": "box-sizing:|;",
        "bxz:cb": "box-sizing:content-box;",
        "bxz:bb": "box-sizing:border-box;",
        "bxsh": "box-shadow:|;",
        "bxsh:n": "box-shadow:none;",
        "bxsh:w": "-webkit-box-shadow:0 0 0 #000;",
        "bxsh:m": "-moz-box-shadow:0 0 0 0 #000;",
        "m": "margin:|;",
        "m:a": "margin:auto;",
        "m:0": "margin:0;",
        "m:2": "margin:0 0;",
        "m:3": "margin:0 0 0;",
        "m:4": "margin:0 0 0 0;",
        "mt": "margin-top:|;",
        "mt:a": "margin-top:auto;",
        "mr": "margin-right:|;",
        "mr:a": "margin-right:auto;",
        "mb": "margin-bottom:|;",
        "mb:a": "margin-bottom:auto;",
        "ml": "margin-left:|;",
        "ml:a": "margin-left:auto;",
        "p": "padding:|;",
        "p:0": "padding:0;",
        "p:2": "padding:0 0;",
        "p:3": "padding:0 0 0;",
        "p:4": "padding:0 0 0 0;",
        "pt": "padding-top:|;",
        "pr": "padding-right:|;",
        "pb": "padding-bottom:|;",
        "pl": "padding-left:|;",
        "w": "width:|;",
        "w:a": "width:auto;",
        "h": "height:|;",
        "h:a": "height:auto;",
        "maw": "max-width:|;",
        "maw:n": "max-width:none;",
        "mah": "max-height:|;",
        "mah:n": "max-height:none;",
        "miw": "min-width:|;",
        "mih": "min-height:|;",
        "o": "outline:|;",
        "o:n": "outline:none;",
        "oo": "outline-offset:|;",
        "ow": "outline-width:|;",
        "os": "outline-style:|;",
        "oc": "outline-color:#000;",
        "oc:i": "outline-color:invert;",
        "bd": "border:|;",
        "bd+": "border:1px solid #000;",
        "bd:n": "border:none;",
        "bdbk": "border-break:|;",
        "bdbk:c": "border-break:close;",
        "bdcl": "border-collapse:|;",
        "bdcl:c": "border-collapse:collapse;",
        "bdcl:s": "border-collapse:separate;",
        "bdc": "border-color:#000;",
        "bdi": "border-image:url(|);",
        "bdi:n": "border-image:none;",
        "bdi:w": "-webkit-border-image:url(|) 0 0 0 0 stretch stretch;",
        "bdi:m": "-moz-border-image:url(|) 0 0 0 0 stretch stretch;",
        "bdti": "border-top-image:url(|);",
        "bdti:n": "border-top-image:none;",
        "bdri": "border-right-image:url(|);",
        "bdri:n": "border-right-image:none;",
        "bdbi": "border-bottom-image:url(|);",
        "bdbi:n": "border-bottom-image:none;",
        "bdli": "border-left-image:url(|);",
        "bdli:n": "border-left-image:none;",
        "bdci": "border-corner-image:url(|);",
        "bdci:n": "border-corner-image:none;",
        "bdci:c": "border-corner-image:continue;",
        "bdtli": "border-top-left-image:url(|);",
        "bdtli:n": "border-top-left-image:none;",
        "bdtli:c": "border-top-left-image:continue;",
        "bdtri": "border-top-right-image:url(|);",
        "bdtri:n": "border-top-right-image:none;",
        "bdtri:c": "border-top-right-image:continue;",
        "bdbri": "border-bottom-right-image:url(|);",
        "bdbri:n": "border-bottom-right-image:none;",
        "bdbri:c": "border-bottom-right-image:continue;",
        "bdbli": "border-bottom-left-image:url(|);",
        "bdbli:n": "border-bottom-left-image:none;",
        "bdbli:c": "border-bottom-left-image:continue;",
        "bdf": "border-fit:|;",
        "bdf:c": "border-fit:clip;",
        "bdf:r": "border-fit:repeat;",
        "bdf:sc": "border-fit:scale;",
        "bdf:st": "border-fit:stretch;",
        "bdf:ow": "border-fit:overwrite;",
        "bdf:of": "border-fit:overflow;",
        "bdf:sp": "border-fit:space;",
        "bdl": "border-length:|;",
        "bdl:a": "border-length:auto;",
        "bdsp": "border-spacing:|;",
        "bds": "border-style:|;",
        "bds:n": "border-style:none;",
        "bds:h": "border-style:hidden;",
        "bds:dt": "border-style:dotted;",
        "bds:ds": "border-style:dashed;",
        "bds:s": "border-style:solid;",
        "bds:db": "border-style:double;",
        "bds:dtds": "border-style:dot-dash;",
        "bds:dtdtds": "border-style:dot-dot-dash;",
        "bds:w": "border-style:wave;",
        "bds:g": "border-style:groove;",
        "bds:r": "border-style:ridge;",
        "bds:i": "border-style:inset;",
        "bds:o": "border-style:outset;",
        "bdw": "border-width:|;",
        "bdt": "border-top:|;",
        "bdt+": "border-top:1px solid #000;",
        "bdt:n": "border-top:none;",
        "bdtw": "border-top-width:|;",
        "bdts": "border-top-style:|;",
        "bdts:n": "border-top-style:none;",
        "bdtc": "border-top-color:#000;",
        "bdr": "border-right:|;",
        "bdr+": "border-right:1px solid #000;",
        "bdr:n": "border-right:none;",
        "bdrw": "border-right-width:|;",
        "bdrs": "border-right-style:|;",
        "bdrs:n": "border-right-style:none;",
        "bdrc": "border-right-color:#000;",
        "bdb": "border-bottom:|;",
        "bdb+": "border-bottom:1px solid #000;",
        "bdb:n": "border-bottom:none;",
        "bdbw": "border-bottom-width:|;",
        "bdbs": "border-bottom-style:|;",
        "bdbs:n": "border-bottom-style:none;",
        "bdbc": "border-bottom-color:#000;",
        "bdl": "border-left:|;",
        "bdl+": "border-left:1px solid #000;",
        "bdl:n": "border-left:none;",
        "bdlw": "border-left-width:|;",
        "bdls": "border-left-style:|;",
        "bdls:n": "border-left-style:none;",
        "bdlc": "border-left-color:#000;",
        "bdrs": "border-radius:|;",
        "bdtrrs": "border-top-right-radius:|;",
        "bdtlrs": "border-top-left-radius:|;",
        "bdbrrs": "border-bottom-right-radius:|;",
        "bdblrs": "border-bottom-left-radius:|;",
        "bg": "background:|;",
        "bg+": "background:#FFF url(|) 0 0 no-repeat;",
        "bg:n": "background:none;",
        "bg:ie": "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='|x.png');",
        "bgc": "background-color:#FFF;",
        "bgi": "background-image:url(|);",
        "bgi:n": "background-image:none;",
        "bgr": "background-repeat:|;",
        "bgr:n": "background-repeat:no-repeat;",
        "bgr:x": "background-repeat:repeat-x;",
        "bgr:y": "background-repeat:repeat-y;",
        "bga": "background-attachment:|;",
        "bga:f": "background-attachment:fixed;",
        "bga:s": "background-attachment:scroll;",
        "bgp": "background-position:0 0;",
        "bgpx": "background-position-x:|;",
        "bgpy": "background-position-y:|;",
        "bgbk": "background-break:|;",
        "bgbk:bb": "background-break:bounding-box;",
        "bgbk:eb": "background-break:each-box;",
        "bgbk:c": "background-break:continuous;",
        "bgcp": "background-clip:|;",
        "bgcp:bb": "background-clip:border-box;",
        "bgcp:pb": "background-clip:padding-box;",
        "bgcp:cb": "background-clip:content-box;",
        "bgcp:nc": "background-clip:no-clip;",
        "bgo": "background-origin:|;",
        "bgo:pb": "background-origin:padding-box;",
        "bgo:bb": "background-origin:border-box;",
        "bgo:cb": "background-origin:content-box;",
        "bgz": "background-size:|;",
        "bgz:a": "background-size:auto;",
        "bgz:ct": "background-size:contain;",
        "bgz:cv": "background-size:cover;",
        "c": "color:#000;",
        "tbl": "table-layout:|;",
        "tbl:a": "table-layout:auto;",
        "tbl:f": "table-layout:fixed;",
        "cps": "caption-side:|;",
        "cps:t": "caption-side:top;",
        "cps:b": "caption-side:bottom;",
        "ec": "empty-cells:|;",
        "ec:s": "empty-cells:show;",
        "ec:h": "empty-cells:hide;",
        "lis": "list-style:|;",
        "lis:n": "list-style:none;",
        "lisp": "list-style-position:|;",
        "lisp:i": "list-style-position:inside;",
        "lisp:o": "list-style-position:outside;",
        "list": "list-style-type:|;",
        "list:n": "list-style-type:none;",
        "list:d": "list-style-type:disc;",
        "list:c": "list-style-type:circle;",
        "list:s": "list-style-type:square;",
        "list:dc": "list-style-type:decimal;",
        "list:dclz": "list-style-type:decimal-leading-zero;",
        "list:lr": "list-style-type:lower-roman;",
        "list:ur": "list-style-type:upper-roman;",
        "lisi": "list-style-image:|;",
        "lisi:n": "list-style-image:none;",
        "q": "quotes:|;",
        "q:n": "quotes:none;",
        "q:ru": "quotes:'\00AB' '\00BB' '\201E' '\201C';",
        "q:en": "quotes:'\201C' '\201D' '\2018' '\2019';",
        "ct": "content:|;",
        "ct:n": "content:normal;",
        "ct:oq": "content:open-quote;",
        "ct:noq": "content:no-open-quote;",
        "ct:cq": "content:close-quote;",
        "ct:ncq": "content:no-close-quote;",
        "ct:a": "content:attr(|);",
        "ct:c": "content:counter(|);",
        "ct:cs": "content:counters(|);",
        "coi": "counter-increment:|;",
        "cor": "counter-reset:|;",
        "va": "vertical-align:|;",
        "va:sup": "vertical-align:super;",
        "va:t": "vertical-align:top;",
        "va:tt": "vertical-align:text-top;",
        "va:m": "vertical-align:middle;",
        "va:bl": "vertical-align:baseline;",
        "va:b": "vertical-align:bottom;",
        "va:tb": "vertical-align:text-bottom;",
        "va:sub": "vertical-align:sub;",
        "ta": "text-align:|;",
        "ta:l": "text-align:left;",
        "ta:c": "text-align:center;",
        "ta:r": "text-align:right;",
        "tal": "text-align-last:|;",
        "tal:a": "text-align-last:auto;",
        "tal:l": "text-align-last:left;",
        "tal:c": "text-align-last:center;",
        "tal:r": "text-align-last:right;",
        "td": "text-decoration:|;",
        "td:n": "text-decoration:none;",
        "td:u": "text-decoration:underline;",
        "td:o": "text-decoration:overline;",
        "td:l": "text-decoration:line-through;",
        "te": "text-emphasis:|;",
        "te:n": "text-emphasis:none;",
        "te:ac": "text-emphasis:accent;",
        "te:dt": "text-emphasis:dot;",
        "te:c": "text-emphasis:circle;",
        "te:ds": "text-emphasis:disc;",
        "te:b": "text-emphasis:before;",
        "te:a": "text-emphasis:after;",
        "th": "text-height:|;",
        "th:a": "text-height:auto;",
        "th:f": "text-height:font-size;",
        "th:t": "text-height:text-size;",
        "th:m": "text-height:max-size;",
        "ti": "text-indent:|;",
        "ti:-": "text-indent:-9999px;",
        "tj": "text-justify:|;",
        "tj:a": "text-justify:auto;",
        "tj:iw": "text-justify:inter-word;",
        "tj:ii": "text-justify:inter-ideograph;",
        "tj:ic": "text-justify:inter-cluster;",
        "tj:d": "text-justify:distribute;",
        "tj:k": "text-justify:kashida;",
        "tj:t": "text-justify:tibetan;",
        "to": "text-outline:|;",
        "to+": "text-outline:0 0 #000;",
        "to:n": "text-outline:none;",
        "tr": "text-replace:|;",
        "tr:n": "text-replace:none;",
        "tt": "text-transform:|;",
        "tt:n": "text-transform:none;",
        "tt:c": "text-transform:capitalize;",
        "tt:u": "text-transform:uppercase;",
        "tt:l": "text-transform:lowercase;",
        "tw": "text-wrap:|;",
        "tw:n": "text-wrap:normal;",
        "tw:no": "text-wrap:none;",
        "tw:u": "text-wrap:unrestricted;",
        "tw:s": "text-wrap:suppress;",
        "tsh": "text-shadow:|;",
        "tsh+": "text-shadow:0 0 0 #000;",
        "tsh:n": "text-shadow:none;",
        "lh": "line-height:|;",
        "whs": "white-space:|;",
        "whs:n": "white-space:normal;",
        "whs:p": "white-space:pre;",
        "whs:nw": "white-space:nowrap;",
        "whs:pw": "white-space:pre-wrap;",
        "whs:pl": "white-space:pre-line;",
        "whsc": "white-space-collapse:|;",
        "whsc:n": "white-space-collapse:normal;",
        "whsc:k": "white-space-collapse:keep-all;",
        "whsc:l": "white-space-collapse:loose;",
        "whsc:bs": "white-space-collapse:break-strict;",
        "whsc:ba": "white-space-collapse:break-all;",
        "wob": "word-break:|;",
        "wob:n": "word-break:normal;",
        "wob:k": "word-break:keep-all;",
        "wob:l": "word-break:loose;",
        "wob:bs": "word-break:break-strict;",
        "wob:ba": "word-break:break-all;",
        "wos": "word-spacing:|;",
        "wow": "word-wrap:|;",
        "wow:nm": "word-wrap:normal;",
        "wow:n": "word-wrap:none;",
        "wow:u": "word-wrap:unrestricted;",
        "wow:s": "word-wrap:suppress;",
        "lts": "letter-spacing:|;",
        "f": "font:|;",
        "f+": "font:1em Arial,sans-serif;",
        "fw": "font-weight:|;",
        "fw:n": "font-weight:normal;",
        "fw:b": "font-weight:bold;",
        "fw:br": "font-weight:bolder;",
        "fw:lr": "font-weight:lighter;",
        "fs": "font-style:|;",
        "fs:n": "font-style:normal;",
        "fs:i": "font-style:italic;",
        "fs:o": "font-style:oblique;",
        "fv": "font-variant:|;",
        "fv:n": "font-variant:normal;",
        "fv:sc": "font-variant:small-caps;",
        "fz": "font-size:|;",
        "fza": "font-size-adjust:|;",
        "fza:n": "font-size-adjust:none;",
        "ff": "font-family:|;",
        "ff:s": "font-family:serif;",
        "ff:ss": "font-family:sans-serif;",
        "ff:c": "font-family:cursive;",
        "ff:f": "font-family:fantasy;",
        "ff:m": "font-family:monospace;",
        "fef": "font-effect:|;",
        "fef:n": "font-effect:none;",
        "fef:eg": "font-effect:engrave;",
        "fef:eb": "font-effect:emboss;",
        "fef:o": "font-effect:outline;",
        "fem": "font-emphasize:|;",
        "femp": "font-emphasize-position:|;",
        "femp:b": "font-emphasize-position:before;",
        "femp:a": "font-emphasize-position:after;",
        "fems": "font-emphasize-style:|;",
        "fems:n": "font-emphasize-style:none;",
        "fems:ac": "font-emphasize-style:accent;",
        "fems:dt": "font-emphasize-style:dot;",
        "fems:c": "font-emphasize-style:circle;",
        "fems:ds": "font-emphasize-style:disc;",
        "fsm": "font-smooth:|;",
        "fsm:a": "font-smooth:auto;",
        "fsm:n": "font-smooth:never;",
        "fsm:aw": "font-smooth:always;",
        "fst": "font-stretch:|;",
        "fst:n": "font-stretch:normal;",
        "fst:uc": "font-stretch:ultra-condensed;",
        "fst:ec": "font-stretch:extra-condensed;",
        "fst:c": "font-stretch:condensed;",
        "fst:sc": "font-stretch:semi-condensed;",
        "fst:se": "font-stretch:semi-expanded;",
        "fst:e": "font-stretch:expanded;",
        "fst:ee": "font-stretch:extra-expanded;",
        "fst:ue": "font-stretch:ultra-expanded;",
        "op": "opacity:|;",
        "op:ie": "filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=100);",
        "op:ms": "-ms-filter:'progid:DXImageTransform.Microsoft.Alpha(Opacity=100)';",
        "rz": "resize:|;",
        "rz:n": "resize:none;",
        "rz:b": "resize:both;",
        "rz:h": "resize:horizontal;",
        "rz:v": "resize:vertical;",
        "cur": "cursor:|;",
        "cur:a": "cursor:auto;",
        "cur:d": "cursor:default;",
        "cur:c": "cursor:crosshair;",
        "cur:ha": "cursor:hand;",
        "cur:he": "cursor:help;",
        "cur:m": "cursor:move;",
        "cur:p": "cursor:pointer;",
        "cur:t": "cursor:text;",
        "pgbb": "page-break-before:|;",
        "pgbb:au": "page-break-before:auto;",
        "pgbb:al": "page-break-before:always;",
        "pgbb:l": "page-break-before:left;",
        "pgbb:r": "page-break-before:right;",
        "pgbi": "page-break-inside:|;",
        "pgbi:au": "page-break-inside:auto;",
        "pgbi:av": "page-break-inside:avoid;",
        "pgba": "page-break-after:|;",
        "pgba:au": "page-break-after:auto;",
        "pgba:al": "page-break-after:always;",
        "pgba:l": "page-break-after:left;",
        "pgba:r": "page-break-after:right;",
        "orp": "orphans:|;",
        "wid": "widows:|;",
        'cc:ie6': '<!--[if lte IE 6]>\n\t${child}|\n<![endif]-->',
        'cc:ie': '<!--[if IE]>\n\t${child}|\n<![endif]-->',
        'cc:noie': '<!--[if !IE]><!-->\n\t${child}|\n<!--<![endif]-->',
        'html:4t': '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">\n' +
            '<html lang="${lang}">\n' +
            '<head>\n' +
            '       <title></title>\n' +
            '       <meta http-equiv="Content-Type" content="text/html;charset=${charset}">\n' +
            '</head>\n' +
            '<body>\n\t${child}|\n</body>\n' +
            '</html>',
                        
                        'html:4s': '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">\n' +
                                        '<html lang="${lang}">\n' +
                                        '<head>\n' +
                                        '       <title></title>\n' +
                                        '       <meta http-equiv="Content-Type" content="text/html;charset=${charset}">\n' +
                                        '</head>\n' +
                                        '<body>\n\t${child}|\n</body>\n' +
                                        '</html>',
                        
                        'html:xt': '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">\n' +
                                        '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="${lang}">\n' +
                                        '<head>\n' +
                                        '       <title></title>\n' +
                                        '       <meta http-equiv="Content-Type" content="text/html;charset=${charset}" />\n' +
                                        '</head>\n' +
                                        '<body>\n\t${child}|\n</body>\n' +
                                        '</html>',
                        
                        'html:xs': '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">\n' +
                                        '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="${lang}">\n' +
                                        '<head>\n' +
                                        '       <title></title>\n' +
                                        '       <meta http-equiv="Content-Type" content="text/html;charset=${charset}" />\n' +
                                        '</head>\n' +
                                        '<body>\n\t${child}|\n</body>\n' +
                                        '</html>',
                        
                        'html:xxs': '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">\n' +
                                        '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="${lang}">\n' +
                                        '<head>\n' +
                                        '       <title></title>\n' +
                                        '       <meta http-equiv="Content-Type" content="text/html;charset=${charset}" />\n' +
                                        '</head>\n' +
                                        '<body>\n\t${child}|\n</body>\n' +
                                        '</html>',
                        
                        'html:5': '<!DOCTYPE HTML>\n' +
                                        '<html lang="${locale}">\n' +
                                        '<head>\n' +
                                        '       <title></title>\n' +
                                        '       <meta charset="${charset}">\n' +
                                        '</head>\n' +
                                        '<body>\n\t${child}|\n</body>\n' +
                                        '</html>'
    }
    
});

/*-- /mod/bundles/inx/src/inx/code/keys.js --*/


inx.code = inx.code.extend({

    private_createCheatTextarea:function(txt) {
        var t = $("<textarea>").css({width:1,height:1,position:"absolute",left:0,top:0,border:"none",opacity:0}).appendTo(this.el).val(txt+"").focus().select();
        setTimeout(function(){
            t.remove()
        },1000);
        return t;
    },

    private_cheatCopy:function(txt) {
        this.private_createCheatTextarea(txt);
    },
    
    private_cheatPaste:function() {
        var start = "0dclzxkeioyg234vhdyl";
        var t = this.private_createCheatTextarea(start);
        var n = 0;
        var cmp = this;
        var fn = function () {
            var val = t.val();
            if(val!=start) {
                cmp.cmd("insert",val);
                return;
            }
                
            if(n<1000)
                setTimeout(fn,1);
            n++;
        }
        setTimeout(fn,0);
    },

    cmd_keydown:function(e) {
    
         switch(e.keyCode) {   
         
            // Ctrl+z
            case 90:
                if(!e.ctrlKey) return;
                this.cmd("undo");
                return true;
         
            // Ctrl+x
            case 88:
                if(!e.ctrlKey)return;
                this.private_cheatCopy(this.info("selectedText"));
                this.cmd("cut");
                return true;
                  
            // Ctrl+c
            case 67:
                if(!e.ctrlKey)return;
                this.private_cheatCopy(this.info("selectedText"));
                return true;
            
            // Ctrl+z
            case 90:
                if(!e.ctrlKey)break;
                this.cmd("stepBack");
                return false;
        
            // Ctrl+v
            case 86:
                if(!e.ctrlKey) break;
                this.private_cheatPaste();
                break;
        
            // Ctrl+A
            case 65:
                if(!e.ctrlKey)break;
                this.cmd("select",{line:0,symbol:0},{line:this.lines.length-1,symbol:this.lines[this.lines.length-1].code.length});
                return false;
        
            // backspace
            case 8:
                if(this.info("selectionCollapsed"))
                    this.cmd("moveSelectionBack",true);
                this.cmd("cut");
                return false;
        
            // del
            case 46:
                if(this.info("selectionCollapsed"))
                    this.cmd("moveSelectionFront",true);
                this.cmd("cut");
                return false;
        
            // Tab
            case 9:
                if(e.ctrlKey) break;
                var sel = this.info("selection");
                var l1 = sel.start.line;
                var l2 = sel.end.line;
                
                if(l1==l2) {
                
                    var bundle = 0;
                    if(!this.info("selectedText").length)
                        bundle = this.cmd("bundle");
                                            
                    if(!bundle)
                        this.cmd("insert","    ");
                } else {
                    var offset = e.shiftKey ? -4 : 4;
                    for(var i=l1;i<=l2;i++) {
                        var ident = this.info("ident",i) + offset;
                        ident = Math.floor((ident)/4)*4;
                        this.cmd("setIdent",i,ident);
                    }
                }
                return false;
        
            case 37:
                this.cmd("moveSelectionBack",!!e.shiftKey);
                return false;
                break;
            case 39:
                this.cmd("moveSelectionFront",!!e.shiftKey);
                return false;
                break;
            case 38:
                this.cmd("moveSelectionTop",!!e.shiftKey);
                return false;
                break;
            case 40:
                this.cmd("moveSelectionBottom",!!e.shiftKey);
                return false;
                break;
                
            // Page Up
            case 33:
                var lines = this.info("visibleLines");
                var lines = Math.abs(lines.top-lines.bottom)-2;
                this.cmd("moveSelectionTop",!!e.shiftKey,lines,lines);
                return false;
                break;  
                
            // Page Down
            case 34:
                var lines = this.info("visibleLines");
                var lines = Math.abs(lines.top-lines.bottom)-2;
                this.cmd("moveSelectionBottom",!!e.shiftKey,lines,lines);
                return false;
                break;                

            // End                
            case 35:
                this.cmd("moveSelectionEnd",!!e.shiftKey);
                return false;
            // home
            case 36:
                this.cmd("moveSelectionHome",!!e.shiftKey);
                return false;
            // Enter
            case 13:
                var ident = this.info("ident",this.info("selection").end.line);
                var cursor = this.info("selection").start.symbol;
                ident = Math.min(cursor,ident);
                this.cmd("insert","\n"+("                                                                                         ".substr(0,ident)));
                return false;
        }
    },
    
    cmd_keypress:function(e) {
        this.cmd("insertPressed",e);
        return false;
    },
    
// ------------------------------------------------------------------------------------ Поиск

    cmd_showSearchDlg:function() {    
        inx({type:"inx.code.search"}).cmd("render").cmd("show").on("search",[this.id(),"newSearch"]);        
        return false;
    },
    
    cmd_newSearch:function(str) {
    
        str = (str+"").toLowerCase();
        
        var found = false;
        var sel = this.info("selection");
        for(var ii=0;ii<this.lines.length;ii++) {
            var i = (ii + sel.end.line)%this.lines.length;
            var line = this.info("line",i).toLowerCase();
            var symbol = line.indexOf(str,i==sel.end.line ? sel.end.symbol : 0);
            if(symbol!=-1) {
                this.cmd("select",{line:i,symbol:symbol},{line:i,symbol:symbol+str.length});
                found = 1;
                break;
            }
        }
        
        if(found)
            this.cmd("focus");
        else
            inx.msg("Фраза не найдена",1);

        return false;
    },
    
    cmd_search:function() {
        var str = inx.storage.get("viomerdyg2oklbjcus3m")+"";
        this.cmd("newSearch",str);
        return false;
    }
    
});

/*-- /mod/bundles/inx/src/inx/code/lang.js --*/



/*-- /mod/bundles/inx/src/inx/code/lang/ini.js --*/


inx.ns("inx.code.lang").ini = {
    normal:{
        triggers:[
            {re:/\[/,name:"section"},        
            {re:/\$\w+/,name:"variable"},
            {re:/\d+/,name:"digit"},
            {re:/"/,name:"string"},
            {re:/\;.*/,name:"comment"}
        ],
        style:"normal"
    },
    variable:{style:"variable"},
    digit:{style:"digit"},
    string:{
        triggers:[
            {re:/"/,name:"back"},
            {re:/\\"/,name:"quote_escape"}
        ],
        style:"string"
    },
    section:{
        triggers:[
            {re:/\]/,name:"back"}
        ],
        style:"keyword"
    },    
    quote_escape:{},
    comment:{style:"comment"}        
}

/*-- /mod/bundles/inx/src/inx/code/lang/js.js --*/


inx.ns("inx.code.lang").js = {
    normal:{
        triggers:[
            {re:/\d+/,name:"digit"},
            {re:/\/\*/,name:"comment_block"},
            {re:/"/,name:"string"},
            {re:/\/\/.*/,name:"comment"},
            {re:/\/([^\/]|(\\_\/))+\//,name:"regex"},
            {re:/\bfunction\b|\breturn\b|\bfor\b|\bvar\b|\bin\b|\bthis\b|\bif\b|\bwhile\b/,name:"keyword"}
        ],
        style:"normal"
    },
    regex:{
        style:"variable"
    },    
    regex_escape_slash:{style:"string"}, 
    comment_block:{
        triggers:[
            {re:/\*\//,name:"back"}
        ],
        style:"comment"
    },
    digit:{style:"digit"},
    string:{
        triggers:[
            {re:/"/,name:"back"},
            {re:/\\"/,name:"quote_escape"}
        ],
        style:"string"
    },
    quote_escape:{},
    keyword:{style:"keyword"},
    comment:{style:"comment"}        
}

/*-- /mod/bundles/inx/src/inx/code/lang/php.js --*/


inx.ns("inx.code.lang").php = {
    normal:{
        triggers:[
            {re:/\$\w+/,name:"variable"},
            {re:/\d+/,name:"digit"},
            {re:/\/\*/,name:"comment_block"},
            {re:/"/,name:"string"},
            {re:/\/\/.*/,name:"comment"},
            {re:/(foreach|public|private|protected|static|final|extends|implements|function|array|return|echo|class|extends|if|else|foreach|while|do)(?=[^a-zA-Z0-9\_])/,name:"keyword"}
        ],
        style:"normal"
    },
    comment_block:{
        triggers:[
            {re:/\*\//,name:"back"}
        ],
        style:"comment"
    },
    variable:{style:"variable"},
    digit:{style:"digit"},
    string:{
        triggers:[
            {re:/"/,name:"back"},
            {re:/\\"/,name:"quote_escape"}
        ],
        style:"string"
    },
    quote_escape:{},
    keyword:{style:"keyword"},
    comment:{style:"comment"}        
}

/*-- /mod/bundles/inx/src/inx/code/lang/text.js --*/


inx.ns("inx.code.lang").text = {
    normal:{triggers:[]}     
}

/*-- /mod/bundles/inx/src/inx/code/lineParser.js --*/


inx.code.lineParser = inx.observable.extend({

    constructor:function(p) {
        if(!p.lang)p.lang="text";
        this.descr = inx.code.lang[p.lang];
        this.base(p);
    },
    
    info_parse:function(code,stack) {
    
        this.stack = [];
        for(var i in stack) this.stack.push(stack[i]);    
        
        this.src = code;
        this.log = [];
        this.index = 0;

        if(this.state()!="normal")
            this.log[0] = this.descr[this.state()].style;
        
        while(this.step()){}
        return {style:this.log,stack:this.stack};
    },
    
    state:function() {
        return this.stack[this.stack.length-1] || "normal";
    },
    
    setState:function(s) {
        s=="back" ? this.stack.pop() : this.stack.push(s);
        this.log[this.pos()] = this.descr[this.state()].style;
    },
    
    pos:function() { return this.index; },
    
    eat:function(length) {
        this.index+=length;
        this.src = this.src.substr(length);
    },
    
    step:function() {
        var triggers = this.descr[this.state()].triggers;
        var index = null;
        if(!triggers) {
            this.setState("back");
            return true;
        }
        for(var i in triggers) {
            var trigger = triggers[i];
            var result = this.src.match(trigger.re);         
            if(result)
                if(index===null || result.index<index) {
                    index = result.index;
                    where = trigger.name;
                    var found = result[0];
                }            
        }
        
        if(found) {
            this.eat(index + (where=="back" ? found.length : 0));
            this.setState(where);
            this.eat(where!="back" ? found.length : 0);
            return true;
        } else {
            return false;
        }
    }

})

/*-- /mod/bundles/inx/src/inx/code/parser.js --*/


inx.code.parser = inx.observable.extend({

    constructor:function(p) {
        this.line = 0;
        this.base(p);
        this.lineParser = inx({type:"inx.code.lineParser",lang:this.lang});
        this.stack = [];
        this.energy = 0;
        this.start();
    },
    
    cmd_destroy:function() {
        this.lineParser.cmd("destroy");
        this.base();
    },
    
    cmd_process:function() {
    
        this.energy++;
        if(this.energy<0)
            return;
    
        for(var k=0;k<25;k++) {
        
            var code = this.editor.info("line",this.line);
            
            if(code===undefined) {
                return; 
            }
            
            var visible = this.editor.info("visibleLines");
            
            if(this.line>visible.bottom)
                this.stop();
                
            var ret = this.lineParser.info("parse",code,this.stack[this.line-1]);
            
            this.editor.cmd("updateLineStyle",this.line,ret.style);
            this.stack[this.line] = ret.stack;
            this.line++;
            
        }
      
    },
    
    cmd_lineChanged:function(line) {        
        if(line<this.line)
            this.line = line;
        this.start();
        this.energy = -20;
    },
    
    cmd_scrollChanged:function() {
        this.start();
        this.energy = -10;        
    },
    
    start:function() {
        if(!this.interval)
            this.interval = setInterval(inx.cmd(this,"process"),10);
    },
    
    stop:function() {
        if(this.interval)
            clearInterval(this.interval);
        this.interval = 0;
    }

});


/*-- /mod/bundles/inx/src/inx/code/search.js --*/


inx.code.search = inx.dialog.extend({

    constructor:function(p) {
        p.width = 300;
        this.input = inx({
            type:"inx.textfield",
            listeners:{blur:[this.id(),"close"]}
        });
        p.items = [this.input];
        p.title = "Поиск";
        this.base(p);
        
        var i = this.input;
        setTimeout(function(){
            i.cmd("focus").cmd("select");
        },100);
        this.on("submit","handleSubmit");
        inx.storage.onready(this.id(),"onStorageReady");
    },
    
    cmd_onStorageReady:function() {
        var val = inx.storage.get("viomerdyg2oklbjcus3m")+"";
        this.input.cmd("setValue",val);
    },
    
    cmd_close:function() {
        this.task("destroy");
    },
    
    cmd_handleSubmit:function(e) {
        var str = this.input.info("value");
        this.fire("search",str);
        inx.storage.set("viomerdyg2oklbjcus3m",str+"");
        this.task("destroy");
    }

})


