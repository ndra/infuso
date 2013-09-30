// @include inx.form

inx.ns("inx.mod.board").profile = inx.form.extend({

    constructor:function(p) {    
        this.base(p); 
        this.cmd("handleData");
    },
    
    cmd_handleData:function() {
    
        this.cmd("add",{
            type:"inx.textfield",
            label:"Ник"
        });
    
    }
         
});