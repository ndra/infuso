// @include inx.list

inx.ns("inx.mod.inxdev.example").gridInx = inx.list.extend({

    constructor:function(p) {
       
        p.loader = {cmd:"inxdev:example:gridLoaderInx"};
        
        this.base(p);        
    } 

});