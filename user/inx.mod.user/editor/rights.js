// @include inx.form,inx.checkbox

inx.ns("inx.mod.user.editor").rights = inx.form.extend({

    constructor:function(p) {
    
        if(!p.style)
            p.style = {};
        p.style.vscroll = true;
        p.autoHeight = false;            
    
        p.labelWidth = 0;        
        this.base(p);
        this.cmd("requestRights");
        inx.hotkey("ctrl+s",this.id(),"save");                
    },
    
    cmd_requestRights:function() {
        this.call({
            cmd:"user:manager:getRights",
            userID:this.userID
        },[this.id(),"handleRights"]);
    },
    
    cmd_handleRights:function(data) {
    
        var group;
    
        for(var i in data) {
        
            if(data[i].group!=group)
            this.cmd("add",{
                type:"inx.panel",
                html:"<div style='padding-bottom:10px;font-size:18px;'>"+data[i].group+"</div>",
                style:{
                    border:0,
                    padding:0
                }
            });
            group = data[i].group;
        
            this.cmd("add",{
                type:"inx.checkbox",
                name:data[i].id,
                value:data[i].exists,
                label:data[i].text
            });
            
            var descr = data[i].descr;
            if(descr)
            this.cmd("add",{
                type:"inx.panel",
                html:"<div style='color:gray;padding-bottom:10px;font-size:11px;'>"+descr+"</div>",
                style:{
                    border:0,
                    padding:0
                }
            });
        }
            
        this.cmd("add",{
            type:"inx.button",
            icon:"save",
            text:"Сохранить",
            onclick:[this.id(),"save"]
        });
    },
    
    cmd_save:function() {
        var data = this.info("data");
        this.call({cmd:"user:manager:setRights",userID:this.userID,rights:data});
        return false;
    }

})