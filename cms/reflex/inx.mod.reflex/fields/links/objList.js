// @include inx.list
// @link_with_parent

inx.mod.reflex.fields.links.objList = inx.list.extend({
    constructor:function(p) {
        p.tbar = [
            {icon:"plus",text:"Добавить",onclick:[this.id(),"addObject"]},            
            {icon:"up",onclick:[this.id(),"moveUp"]},
            {icon:"down",onclick:[this.id(),"moveDown"]},
            {icon:"refresh",onclick:[this.id(),"load"]},
            "|",
            {text:"Удалить",icon:"delete",onclick:[this.id(),"deleteObject"]}

        ];
        this.base(p);
    },
    
    cmd_addObject:function() {
        this.fire("showAddObjectDlg");
    },
    
    cmd_moveUp:function() {
        var id = this.info("selection")[0];
        if(!id) return;
        this.fire("moveUp",id);
    },
    
    cmd_moveDown:function() {
        var id = this.info("selection")[0];
        if(!id) return;
        this.fire("moveDown",id);
    },
    
    cmd_deleteObject:function() {
        var id = this.info("selection")[0];
        if(!id) return;
        this.fire("deleteObject",id);
    },
    
    cmd_setList:function(ids) {
        this.cmd("setLoader",{
            ids:ids,
            cmd:"reflex:editor:fieldController:getListItems",
            index:this.index,
            name:this.name
        });
        this.cmd("load");
    }
})
