// @include inx.code
inx.ns("inx.mod.moduleManager").advcode = inx.code.extend({

    constructor:function(p) {
        if(!p.tbar) p.tbar = [];
        else p.tbar.push("|");
        p.tbar.push({text:"Generate ID",onclick:[this.id(),"generateID"]});
        
        if(p.comments)
            p.side = [{
                region:"top",
                html:p.comments,
                background:"#ffeebb",
                padding:10
            }];
        
        this.base(p);
    },
    
    cmd_generateID:function() {
        var mask = "1234567890qwertyuiopasdfghjklzxcvbnm";
        var id = "";
        for(var i=0;i<10;i++)
            id+= mask.substr(Math.random()*mask.length,1);
        this.cmd("insert",id);
    }
    
});
