// @include inx.form

inx.ns("inx.mod.board").profile = inx.form.extend({

    constructor:function(p) {    
        this.base(p); 
        this.cmd("requestData");
    },
    
    cmd_requestData:function() {
        this.call({
            cmd:"board/controller/profile/getProfile"
        },[this.id(),"handleData"]);
    },
    
    cmd_handleData:function() {
    
        this.cmd("add",{
            type:"inx.textfield",
            label:"Ник"
        });
    
    }
         
});