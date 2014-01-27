// @include inx.panel

inx.ns("inx.mod.file").previews = inx.panel.extend({

    constructor:function(p) {
        
        p.width = 600;
        
        p.style = {
            border:0,
            spacing:15
        }        
        
        p.items = [{
            name:"log",
            style: {
                border:0
            }
        },{
            type:"inx.button",
            text:"Очистить",
            onclick:[this.id(),"clearFiles"]
        }]
        this.base(p);
        this.cmd("collectStep");
    },
    
    cmd_clearFiles:function() {
        inx(this.callID).cmd("destroy");
        this.cmd("collectStep",null,true)
    },
    
    cmd_collectStep:function (folder,clear) {
    
        if(!folder) {
            this.files = 0;
            this.size = 0;
        }
    
        this.callID = this.call({
            cmd:"file:tools:collectPreviews",
            folder:folder,
            clear:clear
        },[this.id(),"handleCollectStep"]);
        
    },
    
    cmd_handleCollectStep:function(data) {
        if(data=="done")
            return;
        
        this.files+=data.files;
        this.size+=data.size;
        this.folder = data.folder;
        
        this.cmd("collectStep",data.folder,data.clear);
        this.cmd("updateInfo");
    },
    
    cmd_updateInfo:function() {
        var html = "";
        html+= "Файлов: "+this.files+"<br/>";
        html+= "Размер: "+this.size+" байт<br/>";
        html+= "Папка: "+this.folder+"<br/>";
        this.items().eq("name","log").cmd("html",html);
    }
    
});