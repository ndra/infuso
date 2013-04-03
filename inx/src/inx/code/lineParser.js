// @link_with_parent

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