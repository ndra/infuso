// @include inx.panel

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
