// @link_with_parent
inx.mod.moduleManager.templateManager.editor = inx.tabs.extend({

    constructor:function(p) {
        p.title = p.templateID;
        p.selectNew = false;
        this.base(p);        
        var cc = ["php","JS","Css"];
        for(var i in cc)
            this.cmd("add",{
                type:"inx.mod.moduleManager.templateManager.editor.tab",
                templateID:this.templateID,
                themeID:this.themeID,
                contentType:cc[i],
                title:cc[i],
                lazy:true
            });
    }

})