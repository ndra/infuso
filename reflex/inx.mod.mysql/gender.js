// @include inx.select

inx.ns("inx.mod.mysql").gender = inx.select.extend({

    constructor:function(p) {
        p.data = [
            {id:"male",text:"М"},
            {id:"female",text:"Ж"}            
        ];
        this.base(p);
    }

});