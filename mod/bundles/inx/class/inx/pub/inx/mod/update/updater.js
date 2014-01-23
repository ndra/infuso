// @include inx.panel
/*-- /update/inx.mod.update/updater.js --*/


inx.ns("inx.mod.update").updater = inx.panel.extend({

    constructor:function(p) {
        this.log = inx({
            type:"inx.panel",
            autoHeight:true,
            style:{
                border:0
            }
        });
        p.items = [
            this.log,
            {type:"inx.button",text:"Заархивировать и выложить",onclick:[this.id(),"process"]}
        ];
        p.width = 400;
        p.autoHeight = true;
        p.style = {
            border:0,
            padding:40
        }
        this.base(p);
    },
    
    cmd_process:function() {
        this.call({cmd:"update:upload"},[this.id(),"handlePack"])
    },
    
    cmd_handlePack:function(data) {
        this.log.cmd("html",data);
    }

});

