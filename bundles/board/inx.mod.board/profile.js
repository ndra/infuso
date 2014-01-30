// @include inx.form

inx.ns("inx.mod.board").profile = inx.panel.extend({

    constructor:function(p) {    
        this.base(p); 
        
        this.form = this.cmd("add",{
            type:"inx.form"
        });
        
        this.cmd("add",{
            type:"inx.separator"
        });
        
        this.cmd("add",{
            type:this.type+".userpick"
        });
        
        this.cmd("requestData");
    },
    
    cmd_requestData:function() {
        this.call({
            cmd:"board/controller/profile/getProfile"
        },[this.id(),"handleData"]);
    },
    
    cmd_handleData:function(data) {
    
        this.form.cmd("add",{
            type:"inx.textfield",
            label:"Ник",
            name:"nickName",
            value:data.nickName
        });
        
        this.form.cmd("add",{
            type:"inx.button",
            text:"Сохранить",
            onclick:[this.id(),"save"]
        });
    
    },
    
    cmd_save:function() {
        this.call({
            cmd:"board/controller/profile/saveProfile",
            data:this.info("data")
        });
    }
         
});