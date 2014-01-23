// @include inx.form
/*-- /board/inx.mod.board/profile.js --*/


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

/*-- /board/inx.mod.board/profile/userpick.js --*/


inx.mod.board.profile.userpick = inx.panel.extend({

    constructor:function(p) {   
        p.style = {
            padding:30
        } 
        this.base(p);
        this.image = this.cmd("add",{
            type:"inx.panel"
        });
        this.file = this.cmd("add",{
            type:"inx.file",
            loader:{
                cmd:"board/controller/profile/saveUserpick"
            },text:"Закачать",
            dropArea:this,
            oncomplete:[this.id(),"requestData"]
        });
        this.cmd("requestData");
    },
    
    cmd_requestData:function() {
        this.call({
            cmd:"board/controller/profile/getUserpick"
        },[this.id(),"handleData"]);
    },
    
    cmd_handleData:function(data) {
        this.eImage = "<img src='"+data.x200+"' style='border:1px solid #ccc;' />";
        this.image
            .cmd("html",this.eImage);   
        //this.file.cmd("setDropArea",this);
    }
         
});

