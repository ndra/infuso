// @include inx.list

inx.ns("inx.mod.eshop").attrdescr = inx.list.extend({

    constructor:function(p) {
        p.tbar = [
            {icon:"plus",onclick:[this.id(),"addAttr"]},
            {icon:"folder",onclick:[this.id(),"addFolder"]},
            {icon:"up",onclick:[this.id(),"up"]},
            {icon:"down"}
        ]
        p.loader = {cmd:"eshop:edit:getAttrDescr",groupID:p.groupID};
        this.on("dblclick",function(id) { this.bubble("editItem",id); })
        this.base(p);
    },
    
    cmd_addAttr:function() {
        this.call({cmd:"eshop:edit:addAttrDescr",groupID:this.groupID},[this.id(),"load"]);
    },
    
    cmd_addFolder:function() {
        this.call({cmd:"eshop:edit:addAttrGroup",groupID:this.groupID},[this.id(),"load"]);
    },
    
    cmd_up:function() {
        var ids = this.info("selection");
        this.call({cmd:"eshop:edit:attrdescrUp",ids:ids,groupID:this.groupID},[this.id(),"load"]);
    }
     
});