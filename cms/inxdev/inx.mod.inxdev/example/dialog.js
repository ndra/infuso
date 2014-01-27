// @include inx.dialog

inx.ns("inx.mod.inxdev.example").dialog = inx.panel.extend({

    constructor:function(p) {
       // p.autoHeight = true;
        p.items = [
            {type:"inx.button",text:"Просто диалог",onclick:[this.id(),"openEx1"]},
            {type:"inx.button",text:"autoHeight",onclick:[this.id(),"openEx3"]},
            {type:"inx.button",text:"Диалог с деревом и галлереей",onclick:[this.id(),"openEx2"]}
        ]
        this.base(p);
       // this.cmd("open");
    },
    
    cmd_openEx1:function() {
        inx({
            type:"inx.mod.inxdev.example.dialog.ex1"
        }).cmd("render");
    },
    
    cmd_openEx2:function() {
        inx({
            type:"inx.mod.inxdev.example.dialog.ex2"
        }).cmd("render");
    },
    
    cmd_openEx3:function() {
        inx({
            type:"inx.mod.inxdev.example.dialog.ex3"
        }).cmd("render");
    }
    
});

//--------------------------------------------------------------------------

inx.mod.inxdev.example.dialog.ex1 = inx.dialog.extend({
    constructor:function(p) {
        p.width = 320;
        p.height = 100;
        p.title = "Just a dialog";
        p.html = "Hallo";
        this.base(p);
    }
});

inx.mod.inxdev.example.dialog.ex2 = inx.dialog.extend({
    constructor:function(p) {
        p.width = 600;
        p.height = 240;
        p.title = "Panel";
        p.side = [{type:"inx.tree",width:200,resizable:true,region:"left",loader:{cmd:"inxdev:example:treeLoader"} }]
        p.items = [{type:"inx.gallery",loader:{cmd:"inxdev:example:galleryLoader"}}]
        p.layout = "inx.layout.fit";
        this.base(p);
    }
});
inx.mod.inxdev.example.dialog.ex3 = inx.dialog.extend({
    constructor:function(p) {
        p.width = 600;
        p.title = "Panel";
        p.side = [{type:"inx.tree",width:200,resizable:true,region:"left",loader:{cmd:"inxdev:example:treeLoader"} }]
        p.items = [{
            type:"inx.list",
            loader:{cmd:"inxdev:example:listLoader"}
        }]
        p.layout = "inx.layout.fit";
        this.base(p);
    }
});


