// @include inx.select

inx.ns("inx.mod.reflex.fields").select = inx.select.extend({

    constructor:function(p) {
        p.loader = {
            cmd:"reflex:editor:fieldController:getSelectOptions",
            index:p.index,
            name:p.name
        };
        this.base(p);
    }

});