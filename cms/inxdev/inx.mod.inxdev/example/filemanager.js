// @include inx.list

inx.ns("inx.mod.inxdev.example").filemanager = inx.list.extend({

    constructor:function(p) {
        p.autoHeight = true;
        p.loader = {cmd:"inxdev:filemanager:list"};
        this.file = inx({type:"inx.file",text:"Закачать файл",icon:"home",loader:{cmd:"inxdev:filemanager:upload"},oncomplete:[this.id(),"load"]});
        p.tbar = [
            this.file,
            {text:"Удалить файл",icon:"delete",onclick:[this.id(),"deleteFile"]}
        ];
        this.file.cmd("setArea",this);
        p.emptyHTML = "<div style='padding:10px;color:gray;'>Нет файлов</div>";
        this.on("dblclick","showFile");
        this.base(p);
    },
    
    renderer:function(e,data) {
        e.addClass("inx-core-inlineBlock");        
        e.css({fontSize:10,margin:"10px",textAlign:"center",width:102,border:"none"});
        $("<img>").css({border:"1px solid #ededed"}).attr("src",data.preview).appendTo(e);
        $("<div>").html(data.name+"").appendTo(e);
    },
    
    cmd_deleteFile:function() {
        var id = this.info("selection")[0];
        if(!id) return;
        var file = this.info("item",id);
        this.call({cmd:"inxdev:filemanager:delete",name:file.name},[this.id(),"load"]);
    },
    
    cmd_showFile:function(id) {
        var name = this.info("item",id).big;
        inx({
            type:"inx.mod.inxdev.example.filemanager.preview",
            filename:name
        }).cmd("render");
    }
   
});
