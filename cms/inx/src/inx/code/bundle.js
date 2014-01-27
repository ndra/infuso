// @link_with_parent

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