// @include inx.list

inx.ns("inx.mod.inxdev.example").grid = inx.list.extend({

    constructor:function(p) {
       // p.autoHeight = true;
        p.tbar = [
            {text:"Список",onclick:[this.id(),"getList"]},
            {text:"Таблица",onclick:[this.id(),"getTable"]}
        ];
        this.base(p);
        this.cmd("getTable");
    },
    
    cmd_getList:function() {
        this.cmd("setLoader",{cmd:"inxdev:example:listLoader"});
        this.cmd("load");
    },
    
    cmd_getTable:function() {
        this.cmd("setLoader",{cmd:"inxdev:example:gridLoader"});
        this.cmd("load");
    }    

});