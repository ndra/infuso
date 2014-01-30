// @include inx.panel

inx.ns("inx.mod.eshop").import = inx.panel.extend({

    constructor:function(p) {
    
        p.width = 800;
    
        p.style = {
            border:0,
            spacing:10
        }

        this.log = inx({
            type:"inx.panel",
            style:{
                border:0
            }
        });

        p.items = [
            {
                html:"Разместите файлы, выгруженные из 1С в каталог /eshop/import/ (каталог считается от корня сайта) и нажмите кнопку «Загрузить XML».<br/>Не удаляйте фотографии после загрузки базы.",
                style:{
                    border:0
                }
            },
            {type:"inx.button",text:"Загрузить XML",icon:"upload",onclick:inx.cmd(this.id(),"start")},
            this.log
        ];
        this.base(p);
    },

    cmd_start:function() {
        this.call({
            cmd:"eshop:1c:import:start"
        },[this.id(),"handleStart"]);
    },

    cmd_handleStart:function() {
        this.cmd("importXML");
    },

    cmd_importXML:function() {
        this.call({cmd:"eshop:1c:import:importXML"},[this.id(),"handleImportXML"]);
    },

    cmd_handleImportXML:function(p) {
        this.log.cmd("html",p.log);
        if(!p.done)
            this.cmd("importXML");
        else
            this.cmd("offersXML");
    },

    cmd_offersXML:function() {
        this.call({cmd:"eshop:1c:import:offersXML"},[this.id(),"handleOffersXML"]);
    },

    cmd_handleOffersXML:function(p) {
        this.log.cmd("html",p.log);
        if(!p.done)
            this.cmd("offersXML");
        else
            this.log.cmd("html","Готово!");
    }

});
