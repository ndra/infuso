// @include inx.textfield

inx.ns("inx.mod.reflex.fields").point = inx.panel.extend({

    constructor:function(p) {
    
        p.labelAlign = "left";
        
        p.layout = "inx.layout.column";
        
        if(!p.style) {
            p.style = {};
        }
        p.style.spacing = 10;

        this.textfield = inx({
            type: "inx.textfield",
            value: p.value,
            buttons: [{
                icon: "trigger",
                onclick: [this.id(), "openMap"]
            }]
        });
        
        p.items = [this.textfield, {
            type: "inx.button",
            text: "Определить коодинаты",
            onclick: [this.id(), "geoCode"]
        }];
        
        this.base(p);
    },
    
    info_value: function() {
        return this.textfield.info("value");
    },
    
    cmd_setValue: function(val) {
        this.textfield.cmd("setValue", val);
    },
    
    cmd_openMap:function() {        
        inx.mod.reflex.fields.point.load(inx.cmd(this.id(), "handleMapReady"));        
    },
    
    cmd_geoCode:function() {        
        var address = inx(this).owner().allItems().eq("name", "addres").info("value");
        inx.mod.reflex.fields.point.load(inx.cmd(this.id(), "handleMapReady", address));   
    },
    
    cmd_handleMapReady: function(address) {
        inx({
            type: "inx.mod.reflex.fields.point.picker",
            value: this.info("value"),
            address: address
        }).cmd("render")
        .on("setValue", [this.id(), "setValue"]);
    }

});

inx.mod.reflex.fields.point.callbacks = [];

inx.mod.reflex.fields.point.runCallbacks = function() {
    for(var i in inx.mod.reflex.fields.point.callbacks) {   
        ymaps.ready(inx.mod.reflex.fields.point.callbacks[i]);
    }
    inx.mod.reflex.fields.point.callbacks = [];
}

inx.mod.reflex.fields.point.load = function(fn) {

    inx.mod.reflex.fields.point.callbacks.push(fn);
    
    if(inx.mod.reflex.fields.point.loaded) {
        inx.mod.reflex.fields.point.runCallbacks();
    }
    
    if(!inx.mod.reflex.fields.point.requested) {
        $.getScript("http://api-maps.yandex.ru/2.1/?lang=ru_RU&coordorder=longlat", function() {
            inx.mod.reflex.fields.point.runCallbacks();
            inx.mod.reflex.fields.point.loaded = true;
        });
        inx.mod.reflex.fields.point.requested = true;
    }
}