// @include inx.panel

inx.ns("inx.mod.eshop.admin").yandexmarket = inx.panel.extend({

    constructor:function(p) {
    
        p.width = 400;
        
        p.style = {
            border:0
        };

        this.log = inx({
            type:"inx.panel",
            style: {
                border:0
            }            
        });

        p.items = [
            {html:"",style:{border:0}},
            {height:10,style:{border:0}},
            {type:"inx.button",text:"Выгрузить",icon:"upload",onclick:[this.id(),"start"]},
            {height:10,style:{border:0}},
            this.log
        ];
        this.base(p);
    },

    cmd_start:function() {
        this.call({
            cmd:"eshop:yandexMarket:export"
        },[this.id(),"handleStep"]);
    },

    cmd_handleStep:function(p) {
        if(!p.done) {
            this.log.cmd("html",p.log);
            this.cmd("start");
        } else {
            this.log.cmd("html","Готово!");
            window.location.reload();
        }
    }
    
});