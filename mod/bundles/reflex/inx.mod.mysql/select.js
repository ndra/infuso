// @include inx.select

inx.ns("inx.mod.mysql").select = inx.select.extend({

    constructor:function(p) {
        p.loader = {
            cmd:"mysql:type:select:options",
            index:p.index,
            name:p.name
        };
        this.base(p);
    }

});