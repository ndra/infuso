// @link_with_parent

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