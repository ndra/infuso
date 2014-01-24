// @include inx.mod.lang.fields.textarea
/*-- /mod/bundles/lang/inx.mod.lang/fields/textfield.js --*/


inx.ns("inx.mod.lang.fields").textfield = inx.mod.lang.fields.textarea.extend({

    constructor:function(p) {
        p.mode = "textfield";
        p.labelAlign = "top";
        this.base(p);
    }
    
});

