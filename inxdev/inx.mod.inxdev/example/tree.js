// @include inx.tree,inx.debug

inx.ns("inx.mod.inxdev.example").tree = inx.tree.extend({

    constructor:function(p) {
        //p.root = {icon:"folder"}
        p.showRoot = false;
        p.tbar = [
            {icon:"refresh",onclick:[this.id(),"reloadSelected"]},
            {icon:"delete",onclick:[this.id(),"deleteSelected"]},
            {text:"getChildren",onclick:[this.id(),"getChildren"]},
            {text:"getSelection",onclick:[this.id(),"getSelection"]}
        ];
        p.loader = {cmd:"inxdev:example:treeLoader"};
        this.base(p);
    },
    
    cmd_deleteSelected:function() {
        var sel = this.info("selection");
        this.cmd("removeNode",sel);
    },
    
    cmd_reloadSelected:function() {
        var sel = this.info("selection")[0];
        this.cmd("load",sel);
    },
    
    cmd_getChildren:function() {
        var p = this.info("param","children");
        var sel = this.info("selection")[0];
        inx.msg(p[sel]);
    },
    
    cmd_getSelection:function() {
        var sel = this.info("selection");
        inx.msg(sel);
    }

});