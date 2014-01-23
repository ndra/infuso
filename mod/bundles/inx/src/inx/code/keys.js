// @link_with_parent
// @include inx.form

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